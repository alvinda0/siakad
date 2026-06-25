<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiMuridController extends Controller
{
    public function index(Request $request)
    {
        $murid = Auth::user();

        // Ambil kelas murid dari kandidat profile
        $kelasId = $murid->kandidatProfile?->kelas_id;

        $semester    = $request->filled('semester')     ? $request->semester     : '1';
        $tahunAjaran = $request->filled('tahun_ajaran') ? (int) $request->tahun_ajaran : now()->year;

        $nilaiList = collect();
        $rata      = null;

        if ($kelasId) {
            $nilaiList = Nilai::where('murid_id', $murid->id)
                ->where('kelas_id', $kelasId)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->with('mataPelajaran')
                ->orderBy('mata_pelajaran_id')
                ->get();

            $avg = $nilaiList->whereNotNull('nilai_akhir')->avg('nilai_akhir');
            $rata = $avg ? round($avg, 2) : null;
        }

        return view('murid.nilai.index', compact(
            'nilaiList', 'semester', 'tahunAjaran', 'rata', 'kelasId'
        ));
    }
}
