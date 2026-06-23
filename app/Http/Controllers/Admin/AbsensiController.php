<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\KandidatProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = Kelas::orderBy('tingkat')->orderBy('jurusan')->orderBy('nama')->get();

        $selectedKelas   = null;
        $selectedJadwal  = null;
        $jadwalList      = collect();
        $tanggal         = $request->filled('tanggal') ? Carbon::parse($request->tanggal) : today();
        $absensiRows     = collect();
        $muridList       = collect();

        if ($request->filled('kelas_id')) {
            $selectedKelas = Kelas::find($request->kelas_id);
            $jadwalList    = Jadwal::where('kelas_id', $request->kelas_id)
                                   ->where('aktif', true)
                                   ->with('mataPelajaran')
                                   ->orderBy('hari')
                                   ->orderBy('jam_mulai')
                                   ->get();

            $muridList = KandidatProfile::where('kelas_id', $request->kelas_id)
                                         ->with('user')
                                         ->get()
                                         ->pluck('user')
                                         ->filter()
                                         ->sortBy('name')
                                         ->values();

            if ($request->filled('jadwal_id')) {
                $selectedJadwal = Jadwal::with('mataPelajaran')->find($request->jadwal_id);

                // Load existing absensi records for this jadwal + tanggal
                $existing = Absensi::where('jadwal_id', $request->jadwal_id)
                                   ->whereDate('tanggal', $tanggal)
                                   ->pluck('status', 'murid_id');

                $absensiRows = $muridList->map(function ($murid) use ($existing) {
                    return [
                        'murid'  => $murid,
                        'status' => $existing[$murid->id] ?? null,
                    ];
                });
            }
        }

        // Rekap per kelas (untuk summary card)
        $rekap = null;
        if ($selectedKelas && $request->filled('jadwal_id') && $absensiRows->isNotEmpty()) {
            $rekap = [
                'Hadir' => $absensiRows->where('status', 'Hadir')->count(),
                'Sakit' => $absensiRows->where('status', 'Sakit')->count(),
                'Izin'  => $absensiRows->where('status', 'Izin')->count(),
                'Alpha' => $absensiRows->where('status', 'Alpha')->count(),
            ];
        }

        return view('admin.absensi.index', compact(
            'kelasList', 'selectedKelas', 'selectedJadwal',
            'jadwalList', 'tanggal', 'absensiRows', 'muridList', 'rekap'
        ));
    }

    /**
     * Simpan absensi satu sesi (jadwal + tanggal).
     */
    public function simpan(Request $request)
    {
        $request->validate([
            'jadwal_id' => ['required', 'exists:jadwal,id'],
            'tanggal'   => ['required', 'date'],
            'absensi'   => ['required', 'array'],
            'absensi.*' => ['required', 'in:Hadir,Sakit,Izin,Alpha'],
        ], [
            'jadwal_id.required' => 'Jadwal wajib dipilih.',
            'tanggal.required'   => 'Tanggal wajib diisi.',
            'absensi.required'   => 'Data absensi tidak boleh kosong.',
        ]);

        $jadwal  = Jadwal::findOrFail($request->jadwal_id);
        $tanggal = $request->tanggal;

        foreach ($request->absensi as $muridId => $status) {
            Absensi::updateOrCreate(
                [
                    'jadwal_id' => $jadwal->id,
                    'murid_id'  => $muridId,
                    'tanggal'   => $tanggal,
                ],
                ['status' => $status]
            );
        }

        return redirect()
            ->route('admin.absensi.index', [
                'kelas_id'  => $jadwal->kelas_id,
                'jadwal_id' => $jadwal->id,
                'tanggal'   => $tanggal,
            ])
            ->with('success', "Absensi berhasil disimpan untuk tanggal " . Carbon::parse($tanggal)->translatedFormat('d F Y') . ".");
    }

    /**
     * Rekap absensi per murid dalam satu kelas.
     */
    public function rekap(Request $request)
    {
        $kelasList     = Kelas::orderBy('tingkat')->orderBy('jurusan')->orderBy('nama')->get();
        $selectedKelas = null;
        $rows          = collect();

        if ($request->filled('kelas_id')) {
            $selectedKelas = Kelas::find($request->kelas_id);

            $muridList = KandidatProfile::where('kelas_id', $request->kelas_id)
                                         ->with('user')
                                         ->get()
                                         ->pluck('user')
                                         ->filter()
                                         ->sortBy('name')
                                         ->values();

            $rows = $muridList->map(function ($murid) use ($selectedKelas) {
                $counts = Absensi::where('murid_id', $murid->id)
                                  ->whereHas('jadwal', fn($q) => $q->where('kelas_id', $selectedKelas->id))
                                  ->selectRaw('status, count(*) as total')
                                  ->groupBy('status')
                                  ->pluck('total', 'status');

                return [
                    'murid'  => $murid,
                    'hadir'  => $counts->get('Hadir', 0),
                    'sakit'  => $counts->get('Sakit', 0),
                    'izin'   => $counts->get('Izin', 0),
                    'alpha'  => $counts->get('Alpha', 0),
                    'total'  => $counts->sum(),
                ];
            });
        }

        return view('admin.absensi.rekap', compact('kelasList', 'selectedKelas', 'rows'));
    }
}
