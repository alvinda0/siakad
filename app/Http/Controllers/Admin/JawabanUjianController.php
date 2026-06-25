<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalUjian;
use App\Models\JawabanUjian;
use App\Models\Kelas;
use App\Models\SesiUjian;
use App\Models\SoalUjian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JawabanUjianController extends Controller
{
    // ── Private helpers ───────────────────────────────────────────────────────

    private function recalculateNilai(JadwalUjian $jadwalUjian, int $muridId): void
    {
        $sesi = SesiUjian::where('jadwal_ujian_id', $jadwalUjian->id)
            ->where('murid_id', $muridId)->first();
        if (! $sesi) return;

        $soalList  = SoalUjian::where('jadwal_ujian_id', $jadwalUjian->id)->get();
        $totalPoin = $soalList->sum('poin');
        if ($totalPoin <= 0) return;

        $jawabanMurid = JawabanUjian::with('soal')
            ->where('jadwal_ujian_id', $jadwalUjian->id)
            ->where('murid_id', $muridId)->get();

        $poinPg         = 0;
        $poinEssay      = 0;
        $totalPoinPg    = $soalList->where('tipe', 'pilihan_ganda')->sum('poin');
        $totalPoinEssay = $soalList->where('tipe', 'essay')->sum('poin');

        foreach ($jawabanMurid as $jawaban) {
            if (! $jawaban->soal) continue;
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

    public function index(Request $request)
    {
        $query = JadwalUjian::with(['kelas', 'mataPelajaran', 'guru'])
            ->withCount([
                'sesiList as total_selesai' => fn($q) => $q->where('status', 'selesai'),
            ]);

        if ($request->filled('jenis'))    $query->where('jenis', $request->jenis);
        if ($request->filled('kelas_id')) $query->where('kelas_id', $request->kelas_id);
        if ($request->filled('cari'))     $query->where('nama', 'like', '%'.$request->cari.'%');

        $ujianList = $query->orderByDesc('tanggal')->get();
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama')->get();

        return view('admin.jawaban-ujian.index', compact('ujianList', 'kelasList'));
    }

    public function show(Request $request, JadwalUjian $jadwalUjian)
    {
        $jadwalUjian->load(['kelas', 'mataPelajaran', 'guru', 'soalList']);

        $sesiList = SesiUjian::with('murid')
            ->where('jadwal_ujian_id', $jadwalUjian->id)
            ->orderByDesc('selesai_at')->get();

        $muridDipilih = null;
        $jawabanList  = collect();
        $soalList     = $jadwalUjian->soalList;

        if ($request->filled('murid_id')) {
            $muridDipilih = User::find($request->murid_id);
            if ($muridDipilih) {
                $jawabanList = JawabanUjian::with('soal')
                    ->where('jadwal_ujian_id', $jadwalUjian->id)
                    ->where('murid_id', $muridDipilih->id)
                    ->orderBy('soal_id')->get()->keyBy('soal_id');
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

        return view('admin.jawaban-ujian.show', compact(
            'jadwalUjian', 'sesiList', 'soalList', 'jawabanList', 'muridDipilih', 'stats'
        ));
    }

    /**
     * Update jawaban PG + nilai essay siswa, lalu recalculate.
     */
    public function updateNilai(Request $request, JadwalUjian $jadwalUjian)
    {
        $muridId = (int) $request->input('murid_id');
        abort_unless($muridId, 422, 'murid_id diperlukan.');

        $nilaiEssayInput = $request->input('nilai_essay', []);
        $jawabanPgInput  = $request->input('jawaban_pg', []);

        DB::transaction(function () use ($jadwalUjian, $muridId, $nilaiEssayInput, $jawabanPgInput) {
            foreach ($nilaiEssayInput as $soalId => $nilai) {
                $soal = SoalUjian::find($soalId);
                if (! $soal || $soal->tipe !== 'essay') continue;
                JawabanUjian::where('jadwal_ujian_id', $jadwalUjian->id)
                    ->where('murid_id', $muridId)->where('soal_id', $soalId)
                    ->update(['nilai_essay' => max(0, min((int) $nilai, (int) $soal->poin))]);
            }

            foreach ($jawabanPgInput as $soalId => $pilihan) {
                $soal = SoalUjian::find($soalId);
                if (! $soal || $soal->tipe !== 'pilihan_ganda') continue;
                $pilihan = strtoupper(trim((string) $pilihan));
                if ($pilihan !== '' && ! in_array($pilihan, ['A','B','C','D'], true)) continue;
                JawabanUjian::where('jadwal_ujian_id', $jadwalUjian->id)
                    ->where('murid_id', $muridId)->where('soal_id', $soalId)
                    ->update(['jawaban_pg' => $pilihan === '' ? null : $pilihan]);
            }

            $this->recalculateNilai($jadwalUjian, $muridId);
        });

        return redirect()
            ->to(route('admin.jawaban-ujian.show', $jadwalUjian).'?murid_id='.$muridId)
            ->with('success', 'Jawaban & nilai siswa berhasil diperbarui.');
    }

    /**
     * Update kunci jawaban satu soal, recalculate semua sesi selesai.
     */
    public function updateKunci(Request $request, JadwalUjian $jadwalUjian, SoalUjian $soal)
    {
        abort_unless($soal->jadwal_ujian_id === $jadwalUjian->id, 403);

        $request->validate([
            'kunci_jawaban'       => ['nullable', 'in:A,B,C,D'],
            'kunci_jawaban_essay' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $jadwalUjian, $soal) {
            if ($soal->tipe === 'pilihan_ganda') {
                $soal->update(['kunci_jawaban' => strtoupper($request->input('kunci_jawaban') ?? $soal->kunci_jawaban)]);
            } else {
                $soal->update(['kunci_jawaban_essay' => $request->input('kunci_jawaban_essay') ?? $soal->kunci_jawaban_essay]);
            }

            $muridIds = SesiUjian::where('jadwal_ujian_id', $jadwalUjian->id)
                ->where('status', 'selesai')->pluck('murid_id');

            foreach ($muridIds as $mid) {
                $this->recalculateNilai($jadwalUjian, $mid);
            }
        });

        $muridId = $request->query('murid_id');
        $url = route('admin.jawaban-ujian.show', $jadwalUjian);
        if ($muridId) $url .= '?murid_id='.$muridId;

        return redirect()->to($url)
            ->with('success', 'Kunci jawaban diperbarui. Semua nilai dihitung ulang.');
    }
}
