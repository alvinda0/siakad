<?php

namespace Database\Seeders;

use App\Models\Ticker;
use Illuminate\Database\Seeder;

class TickerSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['icon' => '🎓', 'konten' => 'PPDB 2026/2027 telah dibuka — daftarkan segera sebelum kuota penuh!', 'urutan' => 1],
            ['icon' => '🏆', 'konten' => 'Tim Olimpiade Sains meraih Juara 1 Tingkat Provinsi Jawa Tengah', 'urutan' => 2],
            ['icon' => '📚', 'konten' => 'Ujian Akhir Semester Genap: 10–20 Juni 2025 — persiapkan dirimu!', 'urutan' => 3],
            ['icon' => '🌿', 'konten' => 'SMK Muhammadiyah Sempor raih penghargaan Sekolah Adiwiyata Tingkat Kabupaten', 'urutan' => 4],
            ['icon' => '⚽', 'konten' => 'Pendaftaran Ekstrakurikuler semester baru dibuka — ayo bergabung!', 'urutan' => 5],
            ['icon' => '📢', 'konten' => 'Informasi beasiswa KIP Kuliah tersedia — hubungi BK untuk info lengkap', 'urutan' => 6],
        ];

        foreach ($items as $item) {
            Ticker::create([
                'icon'   => $item['icon'],
                'konten' => $item['konten'],
                'urutan' => $item['urutan'],
                'aktif'  => true,
            ]);
        }
    }
}
