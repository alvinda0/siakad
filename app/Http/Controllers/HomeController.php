<?php

namespace App\Http\Controllers;

use App\Models\Ekstrakurikuler;
use App\Models\KandidatProfile;
use App\Models\Prestasi;
use App\Models\Role;
use App\Models\Ticker;
use App\Models\User;
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

        $perPage  = in_array((int) $request->per_page, [25, 50, 100, 1000]) ? (int) $request->per_page : 25;
        $diterima = $query->orderBy('nama_lengkap')->paginate($perPage)->withQueryString();

        // ── Statistik dinamis ───────────────────────────────────────────────
        $stats = [
            'siswa'          => User::whereHas('roles', fn($q) => $q->where('name', Role::STUDENT))->count(),
            'guru'           => User::whereHas('roles', fn($q) => $q->where('name', Role::TEACHER))->count(),
            'prestasi'       => Prestasi::where('aktif', true)->count(),
            'ekstrakurikuler'=> Ekstrakurikuler::where('aktif', true)->count(),
        ];

        // ── Ticker info berjalan ─────────────────────────────────────────────
        $tickers = Ticker::aktif()->orderBy('urutan')->orderBy('id')->get();

        return view('pages.beranda', compact('diterima', 'tahunAjaran', 'stats', 'tickers'));
    }
}
