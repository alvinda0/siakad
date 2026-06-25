<?php

namespace Database\Seeders;

use App\Models\JadwalUjian;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class JadwalUjianSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = Kelas::all()->keyBy('nama');
        $mp    = MataPelajaran::all()->keyBy('kode');
        $guru  = User::whereHas('roles', fn($q) => $q->where('name', Role::TEACHER))
                     ->orderBy('id')->get();

        $guruCount = $guru->count();
        $getGuru   = fn(int $i): ?int => $guruCount ? $guru[$i % $guruCount]->id : null;

        // ── UTS Semester Ganjil — Juli 2026 ──────────────────────────────────
        $uts = [
            // [kode_kelas, kode_mapel, tanggal, jam_mulai, jam_selesai, ruangan]
            ['X TKJ 1',  'MTK',    '2026-07-07', '07:30', '09:30', 'R. 101'],
            ['X TKJ 1',  'B.IND',  '2026-07-08', '07:30', '09:30', 'R. 101'],
            ['X TKJ 1',  'B.ING',  '2026-07-09', '07:30', '09:30', 'R. 101'],
            ['X TKJ 1',  'PAI',    '2026-07-10', '07:30', '09:30', 'R. 101'],
            ['X TKJ 1',  'TKJ-C3', '2026-07-11', '07:30', '09:30', 'Lab. Jaringan'],

            ['X TKJ 2',  'MTK',    '2026-07-07', '07:30', '09:30', 'R. 102'],
            ['X TKJ 2',  'B.IND',  '2026-07-08', '07:30', '09:30', 'R. 102'],
            ['X TKJ 2',  'B.ING',  '2026-07-09', '07:30', '09:30', 'R. 102'],
            ['X TKJ 2',  'PAI',    '2026-07-10', '07:30', '09:30', 'R. 102'],
            ['X TKJ 2',  'TKJ-C3', '2026-07-11', '07:30', '09:30', 'Lab. Komputer'],

            ['X TO 1',   'MTK',    '2026-07-07', '07:30', '09:30', 'R. 103'],
            ['X TO 1',   'B.IND',  '2026-07-08', '07:30', '09:30', 'R. 103'],
            ['X TO 1',   'B.ING',  '2026-07-09', '07:30', '09:30', 'R. 103'],
            ['X TO 1',   'PAI',    '2026-07-10', '07:30', '09:30', 'R. 103'],
            ['X TO 1',   'TO-C2',  '2026-07-11', '07:30', '09:30', 'Lab. Otomotif'],

            ['XI TKJ 1', 'MTK',    '2026-07-07', '10:00', '12:00', 'R. 201'],
            ['XI TKJ 1', 'B.IND',  '2026-07-08', '10:00', '12:00', 'R. 201'],
            ['XI TKJ 1', 'TKJ-C6', '2026-07-09', '10:00', '12:00', 'Lab. Jaringan'],
            ['XI TKJ 1', 'TKJ-C7', '2026-07-10', '10:00', '12:00', 'Lab. Server'],

            ['XI TKJ 2', 'MTK',    '2026-07-07', '10:00', '12:00', 'R. 202'],
            ['XI TKJ 2', 'B.IND',  '2026-07-08', '10:00', '12:00', 'R. 202'],
            ['XI TKJ 2', 'TKJ-C6', '2026-07-09', '10:00', '12:00', 'Lab. Komputer'],
            ['XI TKJ 2', 'TKJ-C7', '2026-07-10', '10:00', '12:00', 'Lab. Server'],

            ['XI TO 1',  'MTK',    '2026-07-07', '10:00', '12:00', 'R. 203'],
            ['XI TO 1',  'B.IND',  '2026-07-08', '10:00', '12:00', 'R. 203'],
            ['XI TO 1',  'TO-C4',  '2026-07-09', '10:00', '12:00', 'Lab. Otomotif'],
            ['XI TO 1',  'TO-C5',  '2026-07-10', '10:00', '12:00', 'Lab. Otomotif'],

            ['XII TKJ 1','MTK',    '2026-07-07', '13:00', '15:00', 'R. 301'],
            ['XII TKJ 1','B.IND',  '2026-07-08', '13:00', '15:00', 'R. 301'],
            ['XII TKJ 1','TKJ-C8', '2026-07-09', '13:00', '15:00', 'Lab. Jaringan'],
            ['XII TKJ 1','TKJ-C9', '2026-07-10', '13:00', '15:00', 'R. 301'],

            ['XII TKJ 2','MTK',    '2026-07-07', '13:00', '15:00', 'R. 302'],
            ['XII TKJ 2','B.IND',  '2026-07-08', '13:00', '15:00', 'R. 302'],
            ['XII TKJ 2','TKJ-C8', '2026-07-09', '13:00', '15:00', 'Lab. Server'],
            ['XII TKJ 2','TKJ-C9', '2026-07-10', '13:00', '15:00', 'R. 302'],

            ['XII TO 1', 'MTK',    '2026-07-07', '13:00', '15:00', 'R. 303'],
            ['XII TO 1', 'B.IND',  '2026-07-08', '13:00', '15:00', 'R. 303'],
            ['XII TO 1', 'TO-C6',  '2026-07-09', '13:00', '15:00', 'Lab. Kelistrikan'],
            ['XII TO 1', 'TO-C7',  '2026-07-10', '13:00', '15:00', 'R. 303'],
        ];

        // ── UAS Semester Ganjil — Desember 2026 ──────────────────────────────
        $uas = [
            ['X TKJ 1',  'MTK',    '2026-12-01', '07:30', '09:30', 'R. 101'],
            ['X TKJ 1',  'B.IND',  '2026-12-02', '07:30', '09:30', 'R. 101'],
            ['X TKJ 1',  'B.ING',  '2026-12-03', '07:30', '09:30', 'R. 101'],
            ['X TKJ 1',  'TKJ-C3', '2026-12-04', '07:30', '09:30', 'Lab. Jaringan'],
            ['X TKJ 1',  'TKJ-C4', '2026-12-05', '07:30', '09:30', 'Lab. Komputer'],

            ['X TKJ 2',  'MTK',    '2026-12-01', '07:30', '09:30', 'R. 102'],
            ['X TKJ 2',  'B.IND',  '2026-12-02', '07:30', '09:30', 'R. 102'],
            ['X TKJ 2',  'B.ING',  '2026-12-03', '07:30', '09:30', 'R. 102'],
            ['X TKJ 2',  'TKJ-C3', '2026-12-04', '07:30', '09:30', 'Lab. Jaringan'],
            ['X TKJ 2',  'TKJ-C4', '2026-12-05', '07:30', '09:30', 'Lab. Komputer'],

            ['X TO 1',   'MTK',    '2026-12-01', '07:30', '09:30', 'R. 103'],
            ['X TO 1',   'B.IND',  '2026-12-02', '07:30', '09:30', 'R. 103'],
            ['X TO 1',   'TO-C1',  '2026-12-03', '07:30', '09:30', 'R. Gambar Teknik'],
            ['X TO 1',   'TO-C2',  '2026-12-04', '07:30', '09:30', 'Lab. Otomotif'],

            ['XI TKJ 1', 'MTK',    '2026-12-01', '10:00', '12:00', 'R. 201'],
            ['XI TKJ 1', 'TKJ-C6', '2026-12-02', '10:00', '12:00', 'Lab. Jaringan'],
            ['XI TKJ 1', 'TKJ-C7', '2026-12-03', '10:00', '12:00', 'Lab. Server'],

            ['XI TO 1',  'MTK',    '2026-12-01', '10:00', '12:00', 'R. 203'],
            ['XI TO 1',  'TO-C4',  '2026-12-02', '10:00', '12:00', 'Lab. Otomotif'],
            ['XI TO 1',  'TO-C5',  '2026-12-03', '10:00', '12:00', 'Lab. Otomotif'],

            ['XII TKJ 1','MTK',    '2026-12-01', '13:00', '15:00', 'R. 301'],
            ['XII TKJ 1','TKJ-C8', '2026-12-02', '13:00', '15:00', 'Lab. Jaringan'],
            ['XII TKJ 1','TKJ-C9', '2026-12-03', '13:00', '15:00', 'R. 301'],

            ['XII TO 1', 'MTK',    '2026-12-01', '13:00', '15:00', 'R. 303'],
            ['XII TO 1', 'TO-C6',  '2026-12-02', '13:00', '15:00', 'Lab. Kelistrikan'],
            ['XII TO 1', 'TO-C7',  '2026-12-03', '13:00', '15:00', 'R. 303'],
        ];

        // ── UKK — Februari 2027 (khusus kelas XII) ───────────────────────────
        $ukk = [
            ['XII TKJ 1', 'TKJ-C8', '2027-02-03', '07:00', '11:00', 'Lab. Jaringan'],
            ['XII TKJ 1', 'TKJ-C9', '2027-02-04', '07:00', '11:00', 'Lab. Komputer'],
            ['XII TKJ 2', 'TKJ-C8', '2027-02-03', '07:00', '11:00', 'Lab. Server'],
            ['XII TKJ 2', 'TKJ-C9', '2027-02-04', '07:00', '11:00', 'Lab. Komputer'],
            ['XII TO 1',  'TO-C6',  '2027-02-05', '07:00', '11:00', 'Lab. Kelistrikan'],
            ['XII TO 1',  'TO-C7',  '2027-02-06', '07:00', '11:00', 'Lab. Otomotif'],
        ];

        $datasets = [
            'UTS' => ['nama' => 'UTS Semester Ganjil 2026', 'rows' => $uts],
            'UAS' => ['nama' => 'UAS Semester Ganjil 2026', 'rows' => $uas],
            'UKK' => ['nama' => 'UKK 2027',                 'rows' => $ukk],
        ];

        $total   = 0;
        $skipped = 0;
        $i       = 0;

        foreach ($datasets as $jenis => $dataset) {
            foreach ($dataset['rows'] as $row) {
                [$namaKelas, $kodeMapel, $tanggal, $jamMulai, $jamSelesai, $ruangan] = $row;

                $kelasModel = $kelas->get($namaKelas);
                $mapelModel = $mp->get($kodeMapel);

                if (! $kelasModel || ! $mapelModel) {
                    $skipped++;
                    continue;
                }

                JadwalUjian::updateOrCreate(
                    [
                        'kelas_id'          => $kelasModel->id,
                        'mata_pelajaran_id' => $mapelModel->id,
                        'jenis'             => $jenis,
                        'tanggal'           => $tanggal,
                        'jam_mulai'         => $jamMulai,
                    ],
                    [
                        'nama'        => $dataset['nama'],
                        'jam_selesai' => $jamSelesai,
                        'ruangan'     => $ruangan,
                        'guru_id'     => $getGuru($i),
                        'aktif'       => true,
                    ]
                );

                $total++;
                $i++;
            }
        }

        $this->command->info("✅ {$total} jadwal ujian berhasil di-seed" .
            ($skipped ? " ({$skipped} dilewati)" : '') . '.');
    }
}
