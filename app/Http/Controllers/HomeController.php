<?php

namespace App\Http\Controllers;

use App\Models\KandidatProfile;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Ambil tahun ajaran aktif (berdasarkan tahun sekarang)
        $tahunSekarang = now()->year;
        $tahunAjaran   = $tahunSekarang . '/' . ($tahunSekarang + 1);

        // Query kandidat yang sudah diterima
        $query = KandidatProfile::where('status', 'diterima')
            ->select(['id', 'nama_lengkap', 'nisn', 'asal_sekolah', 'status', 'created_at']);

        // Filter pencarian (nama atau NISN)
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_lengkap', 'like', "%{$keyword}%")
                  ->orWhere('nisn', 'like', "%{$keyword}%");
            });
        }

        $perPage   = in_array((int) $request->per_page, [25, 50, 100, 1000]) ? (int) $request->per_page : 25;
        $diterima  = $query->orderBy('nama_lengkap')->paginate($perPage)->withQueryString();

        return view('pages.beranda', compact('diterima', 'tahunAjaran'));
    }
}
