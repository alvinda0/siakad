<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JadwalUjian;
use App\Models\JawabanUjian;
use App\Models\MataPelajaran;
use App\Models\SesiUjian;
use App\Models\SoalUjian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JawabanUjianGuruController extends Controller
{
    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Pastikan guru mengampu mata pelajaran ujian ini, abort 403 jika tidak.
     * Superadmin bypass semua pengecekan.
     */
    private function authorizeAmpu(JadwalUjian $jadwalUjian): void
    {
        $user = Auth::user();

        // Superadmin bisa akses semua ujian tanpa perlu mengampu mapelnya
        if ($user->hasRole(\App\Models\Role::SUPERADMIN)) {
            return;
        }

        $isAmpu = MataPelajaran::where('id', $jadwalUjian->mata_pelajaran_id)
            ->where('guru_id', $user->id)
            ->exists();

        abort_unless($isAmpu, 403, 'Anda tidak memiliki akses untuk ujian ini.');
    }

    /**
     * Hitung ulang nilai_pg, nilai_essay, nilai_total di sesi_ujian
     * berdasarkan jawaban terbaru untuk satu murid.
     */
    private function recalculateNilai(JadwalUjian $jadwalUjian, int $muridId): void
    {
        $sesi = SesiUjian::where('jadwal_ujian_id', $jadwalUjian->id)
            ->where('murid_id', $muridId)
            ->first();

        if (! $sesi) {
            return;
        }

        $soalList  = SoalUjian::where('jadwal_ujian_id', $jadwalUjian->id)->get();
        $totalPoin = $soalList->sum('poin');

        if ($totalPoin <= 0) {
            return;
        }

        $jawabanMurid = JawabanUjian::with('soal')
            ->where('jadwal_ujian_id', $jadwalUjian->id)
            ->where('murid_id', $muridId)
            ->get();

        $poinPg         = 0;
        $poinEssay      = 0;
        $totalPoinPg    = $soalList->where('tipe', 'pilihan_ganda')->sum('poin');
        $totalPoinEssay = $soalList->where('tipe', 'essay')->sum('poin');

        foreach ($jawabanMurid as $jawaban) {
            if (! $jawaban->soal) {
                continue;
            }
            if ($jawaban->soal->tipe === 'pilihan_ganda' && $jawaban->isBenar()) {
                $poinPg += $jawaban->soal->poin;
            }
            if ($jawaban->soal->tipe === 'essay') {
                $poinEssay += (int) $jawaban->nilai_essay;
            }
        }

        $nilaiPg    = $totalPoinPg > 0    ? round(($poinPg    / $totalPoin) * 100, 2) : ($sesi->nilai_pg ?? 0);
        $nilaiEssay = $totalPoinEssay > 0 ? round(($poinEssay / $totalPoin) * 100, 2) : 0;

        $sesi->update([
            'nilai_pg'    => $nilaiPg,
            'nilai_essay' => $nilaiEssay,
            'nilai_total' => round($nilaiPg + $nilaiEssay, 2),
        ]);
    }

    // ── Public actions ────────────────────────────────────────────────────────

    /**
     * Daftar ujian dari mapel yang diampu guru.
     * Superadmin melihat semua ujian.
     */
    public function index(Request $request)
    {
        $user  = Auth::user();
        $isSuperAdmin = $user->hasRole(\App\Models\Role::SUPERADMIN);

        $query = JadwalUjian::with(['kelas', 'mataPelajaran', 'guru'])
            ->withCount([
                'sesiList as total_selesai' => fn($q) => $q->where('status', 'selesai'),
            ]);

        // Guru hanya melihat ujian dari mapel yang dia ampu
        if (! $isSuperAdmin) {
            $mapelIds = MataPelajaran::where('guru_id', $user->id)->pluck('id');
            $query->whereIn('mata_pelajaran_id', $mapelIds);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('cari')) {
            $query->where('nama', 'like', '%' . $request->cari . '%');
        }

        $ujianList = $query->orderByDesc('tanggal')->get();

        return view('guru.jawaban-ujian.index', compact('ujianList', 'user', 'isSuperAdmin'));
    }

    /**
     * Detail jawaban siswa untuk satu ujian.
     * Guru hanya bisa akses jika mengampu mapel ujian tersebut.
     */
    public function show(Request $request, JadwalUjian $jadwalUjian)
    {
        $this->authorizeAmpu($jadwalUjian);

        $jadwalUjian->load(['kelas', 'mataPelajaran', 'guru', 'soalList']);

        $sesiList = SesiUjian::with('murid')
            ->where('jadwal_ujian_id', $jadwalUjian->id)
            ->orderByDesc('selesai_at')
            ->get();

        $muridDipilih = null;
        $jawabanList  = collect();
        $soalList     = $jadwalUjian->soalList;

        if ($request->filled('murid_id')) {
            $muridDipilih = User::find($request->murid_id);

            if ($muridDipilih) {
                $jawabanList = JawabanUjian::with('soal')
                    ->where('jadwal_ujian_id', $jadwalUjian->id)
                    ->where('murid_id', $muridDipilih->id)
                    ->orderBy('soal_id')
                    ->get()
                    ->keyBy('soal_id');
            }
        }

        $stats = [
            'total_murid'      => $jadwalUjian->kelas->siswa()->count(),
            'total_sesi'       => $sesiList->count(),
            'selesai'          => $sesiList->where('status', 'selesai')->count(),
            'sedang'           => $sesiList->where('status', 'sedang')->count(),
            'belum'            => $sesiList->where('status', 'belum')->count(),
            'total_soal'       => $soalList->count(),
            'total_soal_pg'    => $soalList->where('tipe', 'pilihan_ganda')->count(),
            'total_soal_essay' => $soalList->where('tipe', 'essay')->count(),
        ];

        return view('guru.jawaban-ujian.show', compact(
            'jadwalUjian', 'sesiList', 'soalList', 'jawabanList', 'muridDipilih', 'stats'
        ));
    }

    /**
     * Simpan nilai essay + koreksi jawaban PG yang diedit guru untuk satu murid,
     * lalu hitung ulang nilai di sesi_ujian.
     */
    public function updateNilai(Request $request, JadwalUjian $jadwalUjian)
    {
        $this->authorizeAmpu($jadwalUjian);

        $muridId = (int) $request->input('murid_id');
        abort_unless($muridId, 422, 'murid_id diperlukan.');

        $nilaiEssayInput = $request->input('nilai_essay', []); // [soal_id => int]
        $jawabanPgInput  = $request->input('jawaban_pg', []);  // [soal_id => 'A'|'B'|'C'|'D'|'']

        DB::transaction(function () use ($jadwalUjian, $muridId, $nilaiEssayInput, $jawabanPgInput) {

            // --- Update nilai essay ---
            foreach ($nilaiEssayInput as $soalId => $nilai) {
                $soal = SoalUjian::find($soalId);
                if (! $soal || $soal->tipe !== 'essay') {
                    continue;
                }
                JawabanUjian::where('jadwal_ujian_id', $jadwalUjian->id)
                    ->where('murid_id', $muridId)
                    ->where('soal_id', $soalId)
                    ->update(['nilai_essay' => max(0, min((int) $nilai, (int) $soal->poin))]);
            }

            // --- Update jawaban PG (koreksi manual) ---
            foreach ($jawabanPgInput as $soalId => $pilihan) {
                $soal = SoalUjian::find($soalId);
                if (! $soal || $soal->tipe !== 'pilihan_ganda') {
                    continue;
                }
                $pilihan = strtoupper(trim((string) $pilihan));
                // Boleh kosong (tidak dijawab) atau salah satu A–D
                if ($pilihan !== '' && ! in_array($pilihan, ['A', 'B', 'C', 'D'], true)) {
                    continue;
                }
                JawabanUjian::where('jadwal_ujian_id', $jadwalUjian->id)
                    ->where('murid_id', $muridId)
                    ->where('soal_id', $soalId)
                    ->update(['jawaban_pg' => $pilihan === '' ? null : $pilihan]);
            }

            $this->recalculateNilai($jadwalUjian, $muridId);
        });

        return redirect()
            ->to(route('guru.jawaban-ujian.show', $jadwalUjian) . '?murid_id=' . $muridId)
            ->with('success', 'Jawaban & nilai siswa berhasil diperbarui.');
    }

    /**
     * Update kunci jawaban satu soal (PG → kunci_jawaban, essay → kunci_jawaban_essay).
     * Setelah diubah, semua sesi yang sudah selesai dihitung ulang nilainya.
     */
    public function updateKunci(Request $request, JadwalUjian $jadwalUjian, SoalUjian $soal)
    {
        $this->authorizeAmpu($jadwalUjian);

        abort_unless(
            $soal->jadwal_ujian_id === $jadwalUjian->id,
            403,
            'Soal tidak termasuk dalam ujian ini.'
        );

        $request->validate([
            'kunci_jawaban'       => ['nullable', 'in:A,B,C,D'],
            'kunci_jawaban_essay' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $jadwalUjian, $soal) {
            if ($soal->tipe === 'pilihan_ganda') {
                $soal->update([
                    'kunci_jawaban' => strtoupper($request->input('kunci_jawaban') ?? $soal->kunci_jawaban),
                ]);
            } else {
                $soal->update([
                    'kunci_jawaban_essay' => $request->input('kunci_jawaban_essay') ?? $soal->kunci_jawaban_essay,
                ]);
            }

            // Recalculate semua sesi selesai supaya nilai PG ikut terupdate
            $muridIds = SesiUjian::where('jadwal_ujian_id', $jadwalUjian->id)
                ->where('status', 'selesai')
                ->pluck('murid_id');

            foreach ($muridIds as $mid) {
                $this->recalculateNilai($jadwalUjian, $mid);
            }
        });

        $muridId = $request->query('murid_id');
        $url     = route('guru.jawaban-ujian.show', $jadwalUjian);
        if ($muridId) {
            $url .= '?murid_id=' . $muridId;
        }

        return redirect()->to($url)
            ->with('success', 'Kunci jawaban diperbarui. Semua nilai dihitung ulang otomatis.');
    }
}
