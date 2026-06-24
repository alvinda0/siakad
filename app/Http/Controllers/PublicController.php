<?php

namespace App\Http\Controllers;

use App\Models\Informasi;
use App\Models\KandidatProfile;
use App\Models\KegiatanSekolah;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Halaman Kegiatan
     */
    public function kegiatan()
    {
        $kegiatan  = KegiatanSekolah::aktif()->orderBy('urutan')->orderBy('judul')->get();
        $kategori  = KegiatanSekolah::$kategori;

        return view('pages.kegiatan', compact('kegiatan', 'kategori'));
    }

    /**
     * Halaman Prestasi
     */
    public function prestasi()
    {
        return view('pages.prestasi');
    }

    /**
     * Halaman Ekstrakurikuler
     */
    public function ekstrakurikuler()
    {
        return view('pages.ekstrakurikuler');
    }

    /**
     * Halaman Fasilitas
     */
    public function fasilitas()
    {
        return view('pages.fasilitas');
    }

    /**
     * Halaman Pengumuman & Informasi (digabung 1 halaman)
     */
    public function pengumuman(Request $request)
    {
        $tahunSekarang = now()->year;
        $tahunAjaran   = $tahunSekarang . '/' . ($tahunSekarang + 1);

        $query = KandidatProfile::where('status', 'diterima')
            ->select(['id', 'nama_lengkap', 'nisn', 'asal_sekolah', 'status', 'created_at']);

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_lengkap', 'like', "%{$keyword}%")
                  ->orWhere('nisn', 'like', "%{$keyword}%");
            });
        }

        $perPage  = in_array((int) $request->per_page, [25, 50, 100, 1000]) ? (int) $request->per_page : 25;
        $diterima = $query->orderBy('nama_lengkap')->paginate($perPage)->withQueryString();

        $beasiswa = Informasi::aktif()->beasiswa()->orderBy('urutan')->orderBy('jenis')->get();
        $promo    = Informasi::aktif()->promo()->orderBy('urutan')->orderBy('jenis')->get();

        return view('pages.pengumuman', compact('diterima', 'tahunAjaran', 'beasiswa', 'promo'));
    }

    /**
     * Halaman Informasi — redirect ke pengumuman#informasi
     */
    public function informasi()
    {
        return redirect()->route('pengumuman', ['tab' => 'informasi']);
    }

    /**
     * Halaman Kontak
     */
    public function kontak()
    {
        return view('pages.kontak');
    }
}
