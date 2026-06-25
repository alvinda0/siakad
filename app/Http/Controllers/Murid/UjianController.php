<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\JadwalUjian;
use App\Models\JawabanUjian;
use App\Models\SesiUjian;
use App\Models\SoalUjian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UjianController extends Controller
{
    /**
     * Daftar ujian yang tersedia untuk murid yang sedang login.
     * Filter berdasarkan kelas murid.
     */
    public function index(Request $request)
    {
        $murid  = Auth::user();
        $profil = $murid->kandidatProfile;

        if (! $profil || ! $profil->kelas_id) {
            return view('murid.ujian.index', [
                'ujian'    => collect(),
                'byStatus' => [],
                'profil'   => $profil,
            ]);
        }

        $query = JadwalUjian::with(['kelas', 'mataPelajaran', 'guru'])
            ->where('kelas_id', $profil->kelas_id)
            ->where('aktif', true);

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        $ujianList = $query->orderBy('tanggal')->orderBy('jam_mulai')->get();

        // Ambil semua sesi milik murid ini sekaligus
        $jadwalIds = $ujianList->pluck('id');
        $sesiMap   = SesiUjian::where('murid_id', $murid->id)
                              ->whereIn('jadwal_ujian_id', $jadwalIds)
                              ->get()
                              ->keyBy('jadwal_ujian_id');

        // Hitung jumlah soal per ujian
        $soalCount = SoalUjian::whereIn('jadwal_ujian_id', $jadwalIds)
                               ->selectRaw('jadwal_ujian_id, count(*) as total')
                               ->groupBy('jadwal_ujian_id')
                               ->pluck('total', 'jadwal_ujian_id');

        // Cek apakah tiap ujian punya soal essay (untuk menyembunyikan nilai di index)
        $essayCount = SoalUjian::whereIn('jadwal_ujian_id', $jadwalIds)
                                ->where('tipe', 'essay')
                                ->selectRaw('jadwal_ujian_id, count(*) as total')
                                ->groupBy('jadwal_ujian_id')
                                ->pluck('total', 'jadwal_ujian_id');

        return view('murid.ujian.index', compact(
            'ujianList', 'sesiMap', 'soalCount', 'essayCount', 'profil'
        ));
    }

    /**
     * Halaman awal sebelum mulai mengerjakan ujian.
     */
    public function show(JadwalUjian $jadwalUjian)
    {
        $murid  = Auth::user();
        $profil = $murid->kandidatProfile;

        // Pastikan ujian milik kelas murid
        abort_unless(
            $profil && $profil->kelas_id === $jadwalUjian->kelas_id && $jadwalUjian->aktif,
            403,
            'Anda tidak memiliki akses ke ujian ini.'
        );

        $soalCount = $jadwalUjian->soalList()->count();

        abort_if($soalCount === 0, 404, 'Soal ujian ini belum tersedia.');

        // Ambil atau buat sesi
        $sesi = SesiUjian::firstOrCreate(
            ['jadwal_ujian_id' => $jadwalUjian->id, 'murid_id' => $murid->id],
            ['status' => 'belum']
        );

        $jadwalUjian->load(['kelas', 'mataPelajaran', 'guru']);

        return view('murid.ujian.show', compact('jadwalUjian', 'sesi', 'soalCount'));
    }

    /**
     * Mulai mengerjakan ujian — tampilkan semua soal sekaligus.
     */
    public function kerjakan(JadwalUjian $jadwalUjian)
    {
        $murid  = Auth::user();
        $profil = $murid->kandidatProfile;

        abort_unless(
            $profil && $profil->kelas_id === $jadwalUjian->kelas_id && $jadwalUjian->aktif,
            403,
            'Anda tidak memiliki akses ke ujian ini.'
        );

        $soalList = $jadwalUjian->soalList()->get();

        abort_if($soalList->isEmpty(), 404, 'Soal ujian ini belum tersedia.');

        // Ambil atau buat sesi — jika sudah selesai, redirect ke hasil
        $sesi = SesiUjian::firstOrCreate(
            ['jadwal_ujian_id' => $jadwalUjian->id, 'murid_id' => $murid->id],
            ['status' => 'belum']
        );

        if ($sesi->isSelesai()) {
            return redirect()->route('murid.ujian.hasil', $jadwalUjian)
                             ->with('info', 'Anda sudah menyelesaikan ujian ini.');
        }

        // Tandai sedang berlangsung
        if ($sesi->isBelum()) {
            $sesi->update(['status' => 'sedang', 'mulai_at' => now()]);
        }

        // Ambil jawaban yang sudah tersimpan (jika ada)
        $jawabanMap = JawabanUjian::where('jadwal_ujian_id', $jadwalUjian->id)
                                  ->where('murid_id', $murid->id)
                                  ->get()
                                  ->keyBy('soal_id');

        $jadwalUjian->load(['kelas', 'mataPelajaran']);

        return view('murid.ujian.kerjakan', compact(
            'jadwalUjian', 'soalList', 'jawabanMap', 'sesi'
        ));
    }

    /**
     * Simpan satu jawaban (AJAX — auto-save saat murid memilih).
     */
    public function simpanJawaban(Request $request, JadwalUjian $jadwalUjian)
    {
        $murid  = Auth::user();
        $profil = $murid->kandidatProfile;

        abort_unless(
            $profil && $profil->kelas_id === $jadwalUjian->kelas_id,
            403
        );

        // Pastikan sesi sedang berlangsung
        $sesi = SesiUjian::where('jadwal_ujian_id', $jadwalUjian->id)
                         ->where('murid_id', $murid->id)
                         ->where('status', 'sedang')
                         ->first();

        abort_unless($sesi, 422, 'Sesi ujian tidak ditemukan atau sudah selesai.');

        $request->validate([
            'soal_id'       => ['required', 'exists:soal_ujian,id'],
            'jawaban_pg'    => ['nullable', 'in:A,B,C,D'],
            'jawaban_essay' => ['nullable', 'string', 'max:5000'],
        ]);

        $soal = SoalUjian::findOrFail($request->soal_id);

        // Pastikan soal milik ujian ini
        abort_unless($soal->jadwal_ujian_id === $jadwalUjian->id, 422, 'Soal tidak valid.');

        JawabanUjian::updateOrCreate(
            [
                'jadwal_ujian_id' => $jadwalUjian->id,
                'soal_id'         => $soal->id,
                'murid_id'        => $murid->id,
            ],
            [
                'jawaban_pg'    => $soal->isPilihanGanda() ? strtoupper($request->jawaban_pg) : null,
                'jawaban_essay' => $soal->isEssay() ? $request->jawaban_essay : null,
            ]
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Submit / selesaikan ujian.
     */
    public function submit(Request $request, JadwalUjian $jadwalUjian)
    {
        $murid  = Auth::user();
        $profil = $murid->kandidatProfile;

        abort_unless(
            $profil && $profil->kelas_id === $jadwalUjian->kelas_id,
            403
        );

        $sesi = SesiUjian::where('jadwal_ujian_id', $jadwalUjian->id)
                         ->where('murid_id', $murid->id)
                         ->firstOrFail();

        if ($sesi->isSelesai()) {
            return redirect()->route('murid.ujian.hasil', $jadwalUjian);
        }

        DB::transaction(function () use ($jadwalUjian, $murid, $sesi) {
            $soalList    = $jadwalUjian->soalList()->get();
            $soalPG      = $soalList->where('tipe', 'pilihan_ganda');
            $soalEssay   = $soalList->where('tipe', 'essay');

            // ── Hitung nilai PG ────────────────────────────────────────────
            $poinPGDapat = 0;
            $totalPoinPG = 0;

            foreach ($soalPG as $soal) {
                $totalPoinPG += $soal->poin;
                $jawaban = JawabanUjian::where([
                    'jadwal_ujian_id' => $jadwalUjian->id,
                    'soal_id'         => $soal->id,
                    'murid_id'        => $murid->id,
                ])->first();

                if ($jawaban && strtoupper($jawaban->jawaban_pg ?? '') === strtoupper($soal->kunci_jawaban ?? '')) {
                    $poinPGDapat += $soal->poin;
                }
            }

            // ── Hitung nilai Essay otomatis (kemiripan teks) ───────────────
            $poinEssayDapat = 0;
            $totalPoinEssay = 0;

            foreach ($soalEssay as $soal) {
                $totalPoinEssay += $soal->poin;

                $jawaban = JawabanUjian::where([
                    'jadwal_ujian_id' => $jadwalUjian->id,
                    'soal_id'         => $soal->id,
                    'murid_id'        => $murid->id,
                ])->first();

                if ($jawaban && $jawaban->jawaban_essay && $soal->kunci_jawaban_essay) {
                    // Auto-grade menggunakan similarity
                    $skorEssay = $soal->hitungNilaiEssay($jawaban->jawaban_essay);
                    $poinEssayDapat += $skorEssay;

                    // Simpan nilai essay per soal ke tabel jawaban
                    $jawaban->update(['nilai_essay' => $skorEssay]);
                }
            }

            // ── Konversi ke skala 100 berbobot ────────────────────────────
            $totalPoin = $totalPoinPG + $totalPoinEssay;

            if ($totalPoin > 0) {
                $nilaiTotal = round((($poinPGDapat + $poinEssayDapat) / $totalPoin) * 100, 2);
            } else {
                $nilaiTotal = null;
            }

            // Nilai PG saja (skala 100)
            $nilaiPGPersen    = $totalPoinPG    > 0
                ? round(($poinPGDapat    / $totalPoinPG)    * 100, 2)
                : null;

            // Nilai Essay saja (skala 100)
            $nilaiEssayPersen = $totalPoinEssay > 0
                ? round(($poinEssayDapat / $totalPoinEssay) * 100, 2)
                : null;

            $sesi->update([
                'status'      => 'selesai',
                'selesai_at'  => now(),
                'nilai_pg'    => $nilaiPGPersen,
                'nilai_essay' => $nilaiEssayPersen,
                'nilai_total' => $nilaiTotal,
            ]);
        });

        return redirect()->route('murid.ujian.hasil', $jadwalUjian)
                         ->with('success', 'Ujian berhasil diselesaikan!');
    }

    /**
     * Halaman hasil / rekap jawaban murid.
     */
    public function hasil(JadwalUjian $jadwalUjian)
    {
        $murid  = Auth::user();
        $profil = $murid->kandidatProfile;

        abort_unless(
            $profil && $profil->kelas_id === $jadwalUjian->kelas_id,
            403
        );

        $sesi = SesiUjian::where('jadwal_ujian_id', $jadwalUjian->id)
                         ->where('murid_id', $murid->id)
                         ->first();

        abort_unless($sesi && $sesi->isSelesai(), 404, 'Hasil ujian belum tersedia.');

        $soalList  = $jadwalUjian->soalList()->get();
        $jawabanMap = JawabanUjian::where('jadwal_ujian_id', $jadwalUjian->id)
                                  ->where('murid_id', $murid->id)
                                  ->get()
                                  ->keyBy('soal_id');

        $jadwalUjian->load(['kelas', 'mataPelajaran', 'guru']);

        return view('murid.ujian.hasil', compact(
            'jadwalUjian', 'soalList', 'jawabanMap', 'sesi'
        ));
    }
}
