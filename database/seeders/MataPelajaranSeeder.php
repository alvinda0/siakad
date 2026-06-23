<?php

namespace Database\Seeders;

use App\Models\MataPelajaran;
use Illuminate\Database\Seeder;

class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // ── Kelompok A (Umum – Wajib) ────────────────────────────────
            ['kode' => 'PAI',    'nama' => 'Pendidikan Agama Islam dan Budi Pekerti',  'jurusan' => 'Semua', 'tingkat' => 'Semua'],
            ['kode' => 'PPKn',   'nama' => 'Pendidikan Pancasila dan Kewarganegaraan', 'jurusan' => 'Semua', 'tingkat' => 'Semua'],
            ['kode' => 'B.IND',  'nama' => 'Bahasa Indonesia',                         'jurusan' => 'Semua', 'tingkat' => 'Semua'],
            ['kode' => 'MTK',    'nama' => 'Matematika',                               'jurusan' => 'Semua', 'tingkat' => 'Semua'],
            ['kode' => 'SENI',   'nama' => 'Seni Budaya',                              'jurusan' => 'Semua', 'tingkat' => 'X'],
            ['kode' => 'PJOK',   'nama' => 'Pendidikan Jasmani, Olahraga dan Kesehatan', 'jurusan' => 'Semua', 'tingkat' => 'Semua'],
            ['kode' => 'SEJA',   'nama' => 'Sejarah Indonesia',                        'jurusan' => 'Semua', 'tingkat' => 'X'],
            ['kode' => 'B.ING',  'nama' => 'Bahasa Inggris',                           'jurusan' => 'Semua', 'tingkat' => 'Semua'],

            // ── Kelompok B (Umum – Wajib) ────────────────────────────────
            ['kode' => 'SIMK',   'nama' => 'Simulasi dan Komunikasi Digital',          'jurusan' => 'Semua', 'tingkat' => 'X'],
            ['kode' => 'FIS',    'nama' => 'Fisika',                                   'jurusan' => 'Semua', 'tingkat' => 'X'],
            ['kode' => 'KIM',    'nama' => 'Kimia',                                    'jurusan' => 'Semua', 'tingkat' => 'X'],
            ['kode' => 'KWU',    'nama' => 'Prakarya dan Kewirausahaan',               'jurusan' => 'Semua', 'tingkat' => 'Semua'],

            // ── Kelompok C – TKJ ─────────────────────────────────────────
            ['kode' => 'TKJ-C1', 'nama' => 'Simulasi dan Komunikasi Digital',          'jurusan' => 'TKJ', 'tingkat' => 'X'],
            ['kode' => 'TKJ-C2', 'nama' => 'Sistem Komputer',                          'jurusan' => 'TKJ', 'tingkat' => 'X'],
            ['kode' => 'TKJ-C3', 'nama' => 'Komputer dan Jaringan Dasar',              'jurusan' => 'TKJ', 'tingkat' => 'X'],
            ['kode' => 'TKJ-C4', 'nama' => 'Pemrograman Dasar',                        'jurusan' => 'TKJ', 'tingkat' => 'X'],
            ['kode' => 'TKJ-C5', 'nama' => 'Dasar Desain Grafis',                      'jurusan' => 'TKJ', 'tingkat' => 'X'],
            ['kode' => 'TKJ-C6', 'nama' => 'Administrasi Infrastruktur Jaringan',      'jurusan' => 'TKJ', 'tingkat' => 'XI'],
            ['kode' => 'TKJ-C7', 'nama' => 'Administrasi Sistem Jaringan',             'jurusan' => 'TKJ', 'tingkat' => 'XI'],
            ['kode' => 'TKJ-C8', 'nama' => 'Teknologi Layanan Jaringan',               'jurusan' => 'TKJ', 'tingkat' => 'XII'],
            ['kode' => 'TKJ-C9', 'nama' => 'Produk Kreatif dan Kewirausahaan',         'jurusan' => 'TKJ', 'tingkat' => 'XII'],

            // ── Kelompok C – TO ──────────────────────────────────────────
            ['kode' => 'TO-C1',  'nama' => 'Gambar Teknik Otomotif',                   'jurusan' => 'TO',  'tingkat' => 'X'],
            ['kode' => 'TO-C2',  'nama' => 'Teknologi Dasar Otomotif',                 'jurusan' => 'TO',  'tingkat' => 'X'],
            ['kode' => 'TO-C3',  'nama' => 'Pekerjaan Dasar Teknik Otomotif',          'jurusan' => 'TO',  'tingkat' => 'X'],
            ['kode' => 'TO-C4',  'nama' => 'Pemeliharaan Mesin Kendaraan Ringan',      'jurusan' => 'TO',  'tingkat' => 'XI'],
            ['kode' => 'TO-C5',  'nama' => 'Pemeliharaan Sasis dan Pemindah Tenaga',   'jurusan' => 'TO',  'tingkat' => 'XI'],
            ['kode' => 'TO-C6',  'nama' => 'Pemeliharaan Kelistrikan Kendaraan Ringan','jurusan' => 'TO',  'tingkat' => 'XII'],
            ['kode' => 'TO-C7',  'nama' => 'Produk Kreatif dan Kewirausahaan',         'jurusan' => 'TO',  'tingkat' => 'XII'],

            // ── Muatan Lokal ─────────────────────────────────────────────
            ['kode' => 'MULOK',  'nama' => 'Bahasa Jawa',                              'jurusan' => 'Semua', 'tingkat' => 'Semua'],
            ['kode' => 'ISMUBA', 'nama' => 'Al-Islam dan Kemuhammadiyahan',             'jurusan' => 'Semua', 'tingkat' => 'Semua'],

            // ── Pengembangan Diri ────────────────────────────────────────
            ['kode' => 'BK',     'nama' => 'Bimbingan Konseling',                      'jurusan' => 'Semua', 'tingkat' => 'Semua'],
        ];

        foreach ($data as $row) {
            MataPelajaran::updateOrCreate(
                ['kode' => $row['kode']],
                array_merge($row, ['aktif' => true])
            );
        }

        $this->command->info('✅ ' . count($data) . ' mata pelajaran berhasil di-seed.');
    }
}
