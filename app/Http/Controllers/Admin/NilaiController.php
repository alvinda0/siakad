<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalUjian;
use App\Models\Kelas;
use App\Models\KandidatProfile;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\SesiUjian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = Kelas::withCount('siswa')
                          ->orderBy('tingkat')->orderBy('jurusan')->orderBy('nama')->get();
        $mapelList = MataPelajaran::where('aktif', true)->orderBy('nama')->get();

        $selectedKelas = null;
        $selectedMapel = null;
        $muridList     = collect();
        $nilaiRows     = collect();

        $semester     = $request->filled('semester')     ? $request->semester     : '1';
        $tahunAjaran  = $request->filled('tahun_ajaran') ? (int) $request->tahun_ajaran : now()->year;

        if ($request->filled('kelas_id') && $request->filled('mata_pelajaran_id')) {
            $selectedKelas = Kelas::find($request->kelas_id);
            $selectedMapel = MataPelajaran::find($request->mata_pelajaran_id);

            // Ambil murid dari KandidatProfile (sudah punya kelas_id)
            $muridList = KandidatProfile::where('kelas_id', $request->kelas_id)
                                         ->with('user')
                                         ->get()
                                         ->pluck('user')
                                         ->filter()
                                         ->sortBy('name')
                                         ->values();

            // Fallback: kalau tidak ada di KandidatProfile, cari via role murid langsung
            if ($muridList->isEmpty()) {
                $muridList = User::whereHas('roles', fn($q) => $q->where('name', \App\Models\Role::STUDENT))
                                 ->whereHas('kandidatProfile', fn($q) => $q->where('kelas_id', $request->kelas_id))
                                 ->orderBy('name')
                                 ->get();
            }

            $existing = Nilai::where('kelas_id', $request->kelas_id)
                              ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
                              ->where('semester', $semester)
                              ->where('tahun_ajaran', $tahunAjaran)
                              ->get()
                              ->keyBy('murid_id');

            $nilaiRows = $muridList->map(fn($murid) => [
                'murid' => $murid,
                'nilai' => $existing[$murid->id] ?? null,
            ]);
        }

        return view('admin.nilai.index', compact(
            'kelasList', 'mapelList', 'selectedKelas', 'selectedMapel',
            'muridList', 'nilaiRows', 'semester', 'tahunAjaran'
        ));
    }

    /**
     * Simpan/update nilai seluruh murid dalam satu kelas × mapel × semester.
     */
    public function simpan(Request $request)
    {
        $request->validate([
            'kelas_id'          => ['required', 'exists:kelas,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajaran,id'],
            'semester'          => ['required', 'in:1,2'],
            'tahun_ajaran'      => ['required', 'integer', 'min:2000', 'max:2099'],
            'nilai'             => ['required', 'array'],
            'nilai.*.tugas'     => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai.*.uts'       => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai.*.uas'       => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai.*.catatan'   => ['nullable', 'string', 'max:500'],
        ], [
            'kelas_id.required'          => 'Kelas wajib dipilih.',
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'semester.required'          => 'Semester wajib dipilih.',
            'tahun_ajaran.required'      => 'Tahun ajaran wajib diisi.',
        ]);

        foreach ($request->nilai as $muridId => $row) {
            $tugas = isset($row['tugas']) && $row['tugas'] !== '' ? (float) $row['tugas'] : null;
            $uts   = isset($row['uts'])   && $row['uts']   !== '' ? (float) $row['uts']   : null;
            $uas   = isset($row['uas'])   && $row['uas']   !== '' ? (float) $row['uas']   : null;

            // Hitung nilai akhir otomatis
            $nilaiAkhir = null;
            if ($tugas !== null || $uts !== null || $uas !== null) {
                $nilaiAkhir = round(($tugas ?? 0) * 0.20 + ($uts ?? 0) * 0.30 + ($uas ?? 0) * 0.50, 2);
            }

            $predikat = Nilai::predikatDari($nilaiAkhir);

            Nilai::updateOrCreate(
                [
                    'murid_id'          => $muridId,
                    'mata_pelajaran_id' => $request->mata_pelajaran_id,
                    'kelas_id'          => $request->kelas_id,
                    'semester'          => $request->semester,
                    'tahun_ajaran'      => $request->tahun_ajaran,
                ],
                [
                    'nilai_tugas'  => $tugas,
                    'nilai_uts'    => $uts,
                    'nilai_uas'    => $uas,
                    'nilai_akhir'  => $nilaiAkhir,
                    'predikat'     => $predikat,
                    'catatan'      => $row['catatan'] ?? null,
                ]
            );
        }

        return redirect()
            ->route('admin.nilai.index', $request->only('kelas_id', 'mata_pelajaran_id', 'semester', 'tahun_ajaran'))
            ->with('success', 'Nilai berhasil disimpan.');
    }

    /**
     * Rekap nilai murid: tampilkan semua mapel dalam satu kelas.
     */
    public function rekap(Request $request)
    {
        $kelasList    = Kelas::orderBy('tingkat')->orderBy('jurusan')->orderBy('nama')->get();
        $selectedKelas = null;
        $muridRows    = collect();
        $mapelHeaders = collect();

        $semester    = $request->filled('semester')     ? $request->semester     : '1';
        $tahunAjaran = $request->filled('tahun_ajaran') ? (int) $request->tahun_ajaran : now()->year;

        if ($request->filled('kelas_id')) {
            $selectedKelas = Kelas::find($request->kelas_id);

            // Kumpulkan mapel yang punya data nilai untuk kelas ini
            $mapelHeaders = MataPelajaran::whereHas('nilaiList', fn($q) =>
                $q->where('kelas_id', $request->kelas_id)
                  ->where('semester', $semester)
                  ->where('tahun_ajaran', $tahunAjaran)
            )->orderBy('nama')->get();

            $muridList = KandidatProfile::where('kelas_id', $request->kelas_id)
                                         ->with('user')
                                         ->get()
                                         ->pluck('user')
                                         ->filter()
                                         ->sortBy('name')
                                         ->values();

            $muridRows = $muridList->map(function ($murid) use ($request, $semester, $tahunAjaran, $mapelHeaders) {
                $nilaiMap = Nilai::where('murid_id', $murid->id)
                                  ->where('kelas_id', $request->kelas_id)
                                  ->where('semester', $semester)
                                  ->where('tahun_ajaran', $tahunAjaran)
                                  ->get()
                                  ->keyBy('mata_pelajaran_id');

                $totalNilai = $nilaiMap->whereNotNull('nilai_akhir')->avg('nilai_akhir');

                return [
                    'murid'    => $murid,
                    'nilaiMap' => $nilaiMap,
                    'rata'     => $totalNilai ? round($totalNilai, 2) : null,
                ];
            });
        }

        return view('admin.nilai.rekap', compact(
            'kelasList', 'selectedKelas', 'muridRows',
            'mapelHeaders', 'semester', 'tahunAjaran'
        ));
    }

    /**
     * Sync nilai UTS / UAS dari hasil ujian online (sesi_ujian) ke tabel nilai.
     * - Jenis UTS  → nilai_uts
     * - Jenis UAS  → nilai_uas
     * Hanya mengisi kolom yang relevan, tidak menimpa kolom lain (tugas tetap).
     */
    public function syncUjian(Request $request)
    {
        $request->validate([
            'kelas_id'          => ['required', 'exists:kelas,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajaran,id'],
            'semester'          => ['required', 'in:1,2'],
            'tahun_ajaran'      => ['required', 'integer', 'min:2000', 'max:2099'],
        ]);

        $kelasId   = $request->kelas_id;
        $mapelId   = $request->mata_pelajaran_id;
        $semester  = $request->semester;
        $tahunAjaran = (int) $request->tahun_ajaran;

        // Cari jadwal ujian UTS & UAS untuk kelas + mapel ini
        $jadwalList = JadwalUjian::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->whereIn('jenis', ['UTS', 'UAS'])
            ->get();

        if ($jadwalList->isEmpty()) {
            return redirect()
                ->route('admin.nilai.index', $request->only('kelas_id', 'mata_pelajaran_id', 'semester', 'tahun_ajaran'))
                ->with('error', 'Tidak ada jadwal ujian UTS/UAS yang ditemukan untuk kelas dan mata pelajaran ini.');
        }

        // Ambil semua murid di kelas ini
        $muridList = KandidatProfile::where('kelas_id', $kelasId)
            ->with('user')->get()
            ->pluck('user')->filter()->values();

        if ($muridList->isEmpty()) {
            return redirect()
                ->route('admin.nilai.index', $request->only('kelas_id', 'mata_pelajaran_id', 'semester', 'tahun_ajaran'))
                ->with('error', 'Tidak ada murid di kelas ini.');
        }

        $synced = 0;

        DB::transaction(function () use ($jadwalList, $muridList, $kelasId, $mapelId, $semester, $tahunAjaran, &$synced) {
            // Kelompokkan jadwal per jenis — semua jadwal, bukan hanya yang terbaru
            $jadwalUtsIds = $jadwalList->where('jenis', 'UTS')->pluck('id');
            $jadwalUasIds = $jadwalList->where('jenis', 'UAS')->pluck('id');

            foreach ($muridList as $murid) {
                $nilaiUts = null;
                $nilaiUas = null;

                // Cari sesi UTS yang selesai di SEMUA jadwal UTS mapel ini
                if ($jadwalUtsIds->isNotEmpty()) {
                    $sesi = SesiUjian::whereIn('jadwal_ujian_id', $jadwalUtsIds)
                        ->where('murid_id', $murid->id)
                        ->where('status', 'selesai')
                        ->orderByDesc('selesai_at')
                        ->first();
                    if ($sesi && $sesi->nilai_total !== null) {
                        $nilaiUts = (float) $sesi->nilai_total;
                    }
                }

                // Cari sesi UAS yang selesai di SEMUA jadwal UAS mapel ini
                if ($jadwalUasIds->isNotEmpty()) {
                    $sesi = SesiUjian::whereIn('jadwal_ujian_id', $jadwalUasIds)
                        ->where('murid_id', $murid->id)
                        ->where('status', 'selesai')
                        ->orderByDesc('selesai_at')
                        ->first();
                    if ($sesi && $sesi->nilai_total !== null) {
                        $nilaiUas = (float) $sesi->nilai_total;
                    }
                }

                // Kalau tidak ada data sama sekali, skip
                if ($nilaiUts === null && $nilaiUas === null) continue;

                // Ambil data existing agar nilai tugas tidak tertimpa
                $existing = Nilai::firstOrNew([
                    'murid_id'          => $murid->id,
                    'mata_pelajaran_id' => $mapelId,
                    'kelas_id'          => $kelasId,
                    'semester'          => $semester,
                    'tahun_ajaran'      => $tahunAjaran,
                ]);

                if ($nilaiUts !== null) $existing->nilai_uts = $nilaiUts;
                if ($nilaiUas !== null) $existing->nilai_uas = $nilaiUas;

                // Hitung ulang nilai akhir
                $tugas = (float) ($existing->nilai_tugas ?? 0);
                $uts   = (float) ($existing->nilai_uts   ?? 0);
                $uas   = (float) ($existing->nilai_uas   ?? 0);

                $adaData = ($existing->nilai_tugas !== null || $existing->nilai_uts !== null || $existing->nilai_uas !== null);
                $existing->nilai_akhir = $adaData ? round($tugas * 0.20 + $uts * 0.30 + $uas * 0.50, 2) : null;
                $existing->predikat    = Nilai::predikatDari($existing->nilai_akhir);

                $existing->save();
                $synced++;
            }
        });

        return redirect()
            ->route('admin.nilai.index', $request->only('kelas_id', 'mata_pelajaran_id', 'semester', 'tahun_ajaran'))
            ->with('success', "Berhasil sync {$synced} nilai dari ujian online.");
    }
}
