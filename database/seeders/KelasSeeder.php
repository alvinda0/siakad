<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = 2026;

        $data = [
            // Kelas X
            ['nama' => 'X TKJ 1',  'tingkat' => 'X',   'jurusan' => 'TKJ', 'kapasitas' => 32],
            ['nama' => 'X TKJ 2',  'tingkat' => 'X',   'jurusan' => 'TKJ', 'kapasitas' => 32],
            ['nama' => 'X TO 1',   'tingkat' => 'X',   'jurusan' => 'TO',  'kapasitas' => 32],
            // Kelas XI
            ['nama' => 'XI TKJ 1', 'tingkat' => 'XI',  'jurusan' => 'TKJ', 'kapasitas' => 32],
            ['nama' => 'XI TKJ 2', 'tingkat' => 'XI',  'jurusan' => 'TKJ', 'kapasitas' => 32],
            ['nama' => 'XI TO 1',  'tingkat' => 'XI',  'jurusan' => 'TO',  'kapasitas' => 32],
            // Kelas XII
            ['nama' => 'XII TKJ 1','tingkat' => 'XII', 'jurusan' => 'TKJ', 'kapasitas' => 32],
            ['nama' => 'XII TKJ 2','tingkat' => 'XII', 'jurusan' => 'TKJ', 'kapasitas' => 32],
            ['nama' => 'XII TO 1', 'tingkat' => 'XII', 'jurusan' => 'TO',  'kapasitas' => 32],
        ];

        foreach ($data as $row) {
            Kelas::updateOrCreate(
                ['nama' => $row['nama'], 'tahun_ajaran' => $tahun],
                array_merge($row, ['tahun_ajaran' => $tahun])
            );
        }

        $this->command->info('✅ ' . count($data) . ' kelas berhasil di-seed untuk tahun ajaran ' . $tahun . '/' . ($tahun + 1) . '.');
    }
}
