<?php

namespace App\Http\Controllers;

use App\Models\Informasi;
use App\Models\KandidatProfile;
use App\Models\KegiatanSekolah;
use App\Models\Fasilitas;
use App\Models\Prestasi;
use App\Models\Ekstrakurikuler;
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
        $prestasi = Prestasi::aktif()
            ->orderBy('urutan')
            ->orderByDesc('tahun')
            ->orderBy('judul')
            ->get();

        $stats = [
            'total'     => Prestasi::aktif()->count(),
            'nasional'  => Prestasi::aktif()->tingkat('Nasional')->count(),
            'provinsi'  => Prestasi::aktif()->tingkat('Provinsi')->count(),
            'kabupaten' => Prestasi::aktif()->tingkat('Kabupaten')->count(),
            'kecamatan' => Prestasi::aktif()->tingkat('Kecamatan')->count(),
            'desa'      => Prestasi::aktif()->tingkat('Desa')->count(),
        ];

        return view('pages.prestasi', compact('prestasi', 'stats'));
    }

    /**
     * Halaman Ekstrakurikuler
     */
    public function ekstrakurikuler()
    {
        $wajib   = Ekstrakurikuler::aktif()->jenis('Wajib')->orderBy('urutan')->orderBy('nama')->get();
        $pilihan = Ekstrakurikuler::aktif()->jenis('Pilihan')->orderBy('urutan')->orderBy('nama')->get();

        return view('pages.ekstrakurikuler', compact('wajib', 'pilihan'));
    }

    /**
     * Halaman Fasilitas
     */
    public function fasilitas()
    {
        $fasilitas = Fasilitas::aktif()
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get();

        $grouped = $fasilitas->groupBy('kategori');

        $stats = [
            'total'      => $fasilitas->count(),
            'akademik'   => $fasilitas->where('kategori', 'Akademik')->count(),
            'olahraga'   => $fasilitas->where('kategori', 'Olahraga')->count(),
            'kesehatan'  => $fasilitas->where('kategori', 'Kesehatan')->count(),
        ];

        return view('pages.fasilitas', compact('fasilitas', 'grouped', 'stats'));
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
