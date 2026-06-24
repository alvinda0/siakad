<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use Illuminate\Database\Seeder;

class FasilitasSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'nama'      => 'Lab Sains',
                'deskripsi' => 'Laboratorium Fisika, Kimia, dan Biologi dengan peralatan modern standar nasional.',
                'fitur'     => ['Mikroskop digital', 'Alat ukur presisi', 'Bahan kimia lengkap', 'Ruang ber-AC'],
                'kategori'  => 'Akademik',
                'urutan'    => 1,
            ],
            [
                'nama'      => 'Lab Komputer',
                'deskripsi' => '2 ruang lab komputer dengan 40 unit PC terbaru dan koneksi internet 100 Mbps.',
                'fitur'     => ['40 unit PC i5 Gen 12', 'Internet 100 Mbps', 'Software terkini', 'AC & proyektor'],
                'kategori'  => 'Akademik',
                'urutan'    => 2,
            ],
            [
                'nama'      => 'Perpustakaan',
                'deskripsi' => 'Koleksi 15.000+ buku fisik dan akses e-book serta jurnal ilmiah digital.',
                'fitur'     => ['15.000+ koleksi buku', 'Akses e-library', 'Ruang baca nyaman', 'Komputer katalog'],
                'kategori'  => 'Akademik',
                'urutan'    => 3,
            ],
            [
                'nama'      => 'Lapangan Olahraga',
                'deskripsi' => 'Lapangan sepak bola, basket, dan voli berstandar untuk kegiatan olahraga rutin.',
                'fitur'     => ['Lapangan sepak bola', 'Lapangan basket', 'Lapangan voli', 'Lintasan lari'],
                'kategori'  => 'Olahraga',
                'urutan'    => 4,
            ],
            [
                'nama'      => 'Aula & Auditorium',
                'deskripsi' => 'Aula serbaguna berkapasitas 800 orang untuk seminar, wisuda, dan pentas seni.',
                'fitur'     => ['Kapasitas 800 orang', 'Sound system modern', 'AC central', 'Backstage & lighting'],
                'kategori'  => 'Umum',
                'urutan'    => 5,
            ],
            [
                'nama'      => 'Masjid Sekolah',
                'deskripsi' => 'Masjid sekolah yang luas untuk pembinaan akhlak dan kegiatan keagamaan siswa.',
                'fitur'     => ['Kapasitas 500 jamaah', 'Perpustakaan Al-Quran', 'Ruang wudhu bersih', 'Tempat wanita terpisah'],
                'kategori'  => 'Keagamaan',
                'urutan'    => 6,
            ],
            [
                'nama'      => 'Kantin Sehat',
                'deskripsi' => 'Kantin dengan menu bergizi yang higienis, terjangkau, dan bervariasi setiap hari.',
                'fitur'     => ['Menu bergizi bervariasi', 'Harga terjangkau', 'Pengawasan kebersihan', 'Area makan luas'],
                'kategori'  => 'Umum',
                'urutan'    => 7,
            ],
            [
                'nama'      => 'Akses Transportasi',
                'deskripsi' => 'Lokasi strategis dilalui angkot dan tersedia area parkir yang luas dan aman.',
                'fitur'     => ['Jalur angkot tersedia', 'Parkir luas & aman', 'Pos keamanan 24 jam', 'Dekat jalan utama'],
                'kategori'  => 'Umum',
                'urutan'    => 8,
            ],
            [
                'nama'      => 'UKS Modern',
                'deskripsi' => 'Unit Kesehatan Siswa dengan peralatan lengkap dan perawat tetap setiap hari sekolah.',
                'fitur'     => ['Perawat tetap', 'Obat-obatan lengkap', 'Ruang istirahat', 'Kerjasama Puskesmas'],
                'kategori'  => 'Kesehatan',
                'urutan'    => 9,
            ],
        ];

        foreach ($items as $item) {
            Fasilitas::create($item);
        }
    }
}
