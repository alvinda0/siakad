<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil data kelas berdasarkan nama
        $kelas = Kelas::all()->keyBy('nama');

        // Ambil mata pelajaran berdasarkan kode
        $mp = MataPelajaran::all()->keyBy('kode');

        // Ambil guru (role teacher) – pakai satu guru default jika belum ada banyak
        $guru = User::whereHas('roles', fn($q) => $q->where('name', Role::TEACHER))
                    ->orderBy('id')
                    ->get();

        // Helper: ambil id guru secara round-robin agar tersebar
        $guruCount = $guru->count();
        $getGuru   = function (int $index) use ($guru, $guruCount): ?int {
            if ($guruCount === 0) return null;
            return $guru[$index % $guruCount]->id;
        };

        // ─────────────────────────────────────────────────────────────────────
        // Definisi jadwal per kelas
        // Format: [kode_mapel, hari, jam_mulai, jam_selesai, ruangan]
        // ─────────────────────────────────────────────────────────────────────

        $jadwalDefinisi = [

            // ── X TKJ 1 ──────────────────────────────────────────────────────
            'X TKJ 1' => [
                ['PAI',    'Senin',  '07:00', '08:30', 'R. 101'],
                ['B.IND',  'Senin',  '08:30', '10:00', 'R. 101'],
                ['MTK',    'Senin',  '10:15', '11:45', 'R. 101'],
                ['PPKn',   'Selasa', '07:00', '08:30', 'R. 101'],
                ['B.ING',  'Selasa', '08:30', '10:00', 'R. 101'],
                ['TKJ-C3', 'Selasa', '10:15', '12:15', 'Lab. Jaringan'],
                ['PJOK',   'Rabu',   '07:00', '08:30', 'Lapangan'],
                ['TKJ-C2', 'Rabu',   '08:30', '10:00', 'R. 101'],
                ['TKJ-C4', 'Rabu',   '10:15', '12:15', 'Lab. Komputer'],
                ['FIS',    'Kamis',  '07:00', '08:30', 'R. 101'],
                ['KIM',    'Kamis',  '08:30', '10:00', 'R. 101'],
                ['TKJ-C5', 'Kamis',  '10:15', '12:15', 'Lab. Komputer'],
                ['SIMK',   'Jumat',  '07:00', '08:30', 'Lab. Komputer'],
                ['ISMUBA', 'Jumat',  '08:30', '10:00', 'R. 101'],
                ['MULOK',  'Sabtu',  '07:00', '08:30', 'R. 101'],
                ['BK',     'Sabtu',  '08:30', '09:15', 'R. 101'],
                ['KWU',    'Sabtu',  '09:15', '10:45', 'R. 101'],
            ],

            // ── X TKJ 2 ──────────────────────────────────────────────────────
            'X TKJ 2' => [
                ['MTK',    'Senin',  '07:00', '08:30', 'R. 102'],
                ['PAI',    'Senin',  '08:30', '10:00', 'R. 102'],
                ['B.IND',  'Senin',  '10:15', '11:45', 'R. 102'],
                ['B.ING',  'Selasa', '07:00', '08:30', 'R. 102'],
                ['PPKn',   'Selasa', '08:30', '10:00', 'R. 102'],
                ['TKJ-C3', 'Selasa', '10:15', '12:15', 'Lab. Jaringan'],
                ['TKJ-C2', 'Rabu',   '07:00', '08:30', 'R. 102'],
                ['PJOK',   'Rabu',   '08:30', '10:00', 'Lapangan'],
                ['TKJ-C4', 'Rabu',   '10:15', '12:15', 'Lab. Komputer'],
                ['TKJ-C5', 'Kamis',  '07:00', '09:00', 'Lab. Komputer'],
                ['FIS',    'Kamis',  '09:00', '10:30', 'R. 102'],
                ['KIM',    'Kamis',  '10:30', '12:00', 'R. 102'],
                ['SIMK',   'Jumat',  '07:00', '08:30', 'Lab. Komputer'],
                ['ISMUBA', 'Jumat',  '08:30', '10:00', 'R. 102'],
                ['MULOK',  'Sabtu',  '07:00', '08:30', 'R. 102'],
                ['KWU',    'Sabtu',  '08:30', '10:00', 'R. 102'],
                ['BK',     'Sabtu',  '10:00', '10:45', 'R. 102'],
            ],

            // ── X TO 1 ───────────────────────────────────────────────────────
            'X TO 1' => [
                ['PAI',   'Senin',  '07:00', '08:30', 'R. 103'],
                ['MTK',   'Senin',  '08:30', '10:00', 'R. 103'],
                ['B.IND', 'Senin',  '10:15', '11:45', 'R. 103'],
                ['PPKn',  'Selasa', '07:00', '08:30', 'R. 103'],
                ['B.ING', 'Selasa', '08:30', '10:00', 'R. 103'],
                ['TO-C1', 'Selasa', '10:15', '12:15', 'R. Gambar Teknik'],
                ['PJOK',  'Rabu',   '07:00', '08:30', 'Lapangan'],
                ['TO-C2', 'Rabu',   '08:30', '10:30', 'Lab. Otomotif'],
                ['TO-C3', 'Rabu',   '10:30', '12:30', 'Lab. Otomotif'],
                ['FIS',   'Kamis',  '07:00', '08:30', 'R. 103'],
                ['KIM',   'Kamis',  '08:30', '10:00', 'R. 103'],
                ['KWU',   'Kamis',  '10:15', '11:45', 'R. 103'],
                ['ISMUBA','Jumat',  '07:00', '08:30', 'R. 103'],
                ['MULOK', 'Sabtu',  '07:00', '08:30', 'R. 103'],
                ['BK',    'Sabtu',  '08:30', '09:15', 'R. 103'],
                ['SEJA',  'Sabtu',  '09:15', '10:45', 'R. 103'],
            ],

            // ── XI TKJ 1 ─────────────────────────────────────────────────────
            'XI TKJ 1' => [
                ['PAI',    'Senin',  '07:00', '08:30', 'R. 201'],
                ['B.IND',  'Senin',  '08:30', '10:00', 'R. 201'],
                ['MTK',    'Senin',  '10:15', '11:45', 'R. 201'],
                ['PPKn',   'Selasa', '07:00', '08:30', 'R. 201'],
                ['B.ING',  'Selasa', '08:30', '10:00', 'R. 201'],
                ['TKJ-C6', 'Selasa', '10:15', '12:15', 'Lab. Jaringan'],
                ['TKJ-C7', 'Rabu',   '07:00', '09:00', 'Lab. Server'],
                ['PJOK',   'Rabu',   '09:00', '10:30', 'Lapangan'],
                ['TKJ-C6', 'Rabu',   '10:30', '12:30', 'Lab. Jaringan'],
                ['TKJ-C7', 'Kamis',  '07:00', '09:00', 'Lab. Server'],
                ['KWU',    'Kamis',  '09:00', '10:30', 'R. 201'],
                ['ISMUBA', 'Jumat',  '07:00', '08:30', 'R. 201'],
                ['MULOK',  'Sabtu',  '07:00', '08:30', 'R. 201'],
                ['BK',     'Sabtu',  '08:30', '09:15', 'R. 201'],
            ],

            // ── XI TKJ 2 ─────────────────────────────────────────────────────
            'XI TKJ 2' => [
                ['MTK',    'Senin',  '07:00', '08:30', 'R. 202'],
                ['PAI',    'Senin',  '08:30', '10:00', 'R. 202'],
                ['B.IND',  'Senin',  '10:15', '11:45', 'R. 202'],
                ['B.ING',  'Selasa', '07:00', '08:30', 'R. 202'],
                ['PPKn',   'Selasa', '08:30', '10:00', 'R. 202'],
                ['TKJ-C6', 'Selasa', '10:15', '12:15', 'Lab. Jaringan'],
                ['TKJ-C7', 'Rabu',   '07:00', '09:00', 'Lab. Server'],
                ['PJOK',   'Rabu',   '09:00', '10:30', 'Lapangan'],
                ['TKJ-C7', 'Kamis',  '07:00', '09:00', 'Lab. Server'],
                ['TKJ-C6', 'Kamis',  '09:00', '11:00', 'Lab. Jaringan'],
                ['KWU',    'Kamis',  '11:00', '12:30', 'R. 202'],
                ['ISMUBA', 'Jumat',  '07:00', '08:30', 'R. 202'],
                ['MULOK',  'Sabtu',  '07:00', '08:30', 'R. 202'],
                ['BK',     'Sabtu',  '08:30', '09:15', 'R. 202'],
            ],

            // ── XI TO 1 ──────────────────────────────────────────────────────
            'XI TO 1' => [
                ['PAI',   'Senin',  '07:00', '08:30', 'R. 203'],
                ['B.IND', 'Senin',  '08:30', '10:00', 'R. 203'],
                ['MTK',   'Senin',  '10:15', '11:45', 'R. 203'],
                ['PPKn',  'Selasa', '07:00', '08:30', 'R. 203'],
                ['B.ING', 'Selasa', '08:30', '10:00', 'R. 203'],
                ['TO-C4', 'Selasa', '10:15', '12:15', 'Lab. Otomotif'],
                ['TO-C5', 'Rabu',   '07:00', '09:00', 'Lab. Otomotif'],
                ['PJOK',  'Rabu',   '09:00', '10:30', 'Lapangan'],
                ['TO-C4', 'Rabu',   '10:30', '12:30', 'Lab. Otomotif'],
                ['TO-C5', 'Kamis',  '07:00', '09:00', 'Lab. Otomotif'],
                ['KWU',   'Kamis',  '09:00', '10:30', 'R. 203'],
                ['ISMUBA','Jumat',  '07:00', '08:30', 'R. 203'],
                ['MULOK', 'Sabtu',  '07:00', '08:30', 'R. 203'],
                ['BK',    'Sabtu',  '08:30', '09:15', 'R. 203'],
            ],

            // ── XII TKJ 1 ────────────────────────────────────────────────────
            'XII TKJ 1' => [
                ['PAI',    'Senin',  '07:00', '08:30', 'R. 301'],
                ['B.IND',  'Senin',  '08:30', '10:00', 'R. 301'],
                ['MTK',    'Senin',  '10:15', '11:45', 'R. 301'],
                ['PPKn',   'Selasa', '07:00', '08:30', 'R. 301'],
                ['B.ING',  'Selasa', '08:30', '10:00', 'R. 301'],
                ['TKJ-C8', 'Selasa', '10:15', '12:15', 'Lab. Jaringan'],
                ['TKJ-C9', 'Rabu',   '07:00', '09:00', 'R. 301'],
                ['PJOK',   'Rabu',   '09:00', '10:30', 'Lapangan'],
                ['TKJ-C8', 'Rabu',   '10:30', '12:30', 'Lab. Jaringan'],
                ['TKJ-C8', 'Kamis',  '07:00', '09:00', 'Lab. Server'],
                ['KWU',    'Kamis',  '09:00', '10:30', 'R. 301'],
                ['ISMUBA', 'Jumat',  '07:00', '08:30', 'R. 301'],
                ['MULOK',  'Sabtu',  '07:00', '08:30', 'R. 301'],
                ['BK',     'Sabtu',  '08:30', '09:15', 'R. 301'],
            ],

            // ── XII TKJ 2 ────────────────────────────────────────────────────
            'XII TKJ 2' => [
                ['MTK',    'Senin',  '07:00', '08:30', 'R. 302'],
                ['PAI',    'Senin',  '08:30', '10:00', 'R. 302'],
                ['B.IND',  'Senin',  '10:15', '11:45', 'R. 302'],
                ['B.ING',  'Selasa', '07:00', '08:30', 'R. 302'],
                ['PPKn',   'Selasa', '08:30', '10:00', 'R. 302'],
                ['TKJ-C8', 'Selasa', '10:15', '12:15', 'Lab. Server'],
                ['TKJ-C9', 'Rabu',   '07:00', '09:00', 'R. 302'],
                ['PJOK',   'Rabu',   '09:00', '10:30', 'Lapangan'],
                ['TKJ-C8', 'Rabu',   '10:30', '12:30', 'Lab. Jaringan'],
                ['KWU',    'Kamis',  '07:00', '08:30', 'R. 302'],
                ['ISMUBA', 'Jumat',  '07:00', '08:30', 'R. 302'],
                ['MULOK',  'Sabtu',  '07:00', '08:30', 'R. 302'],
                ['BK',     'Sabtu',  '08:30', '09:15', 'R. 302'],
            ],

            // ── XII TO 1 ─────────────────────────────────────────────────────
            'XII TO 1' => [
                ['PAI',   'Senin',  '07:00', '08:30', 'R. 303'],
                ['B.IND', 'Senin',  '08:30', '10:00', 'R. 303'],
                ['MTK',   'Senin',  '10:15', '11:45', 'R. 303'],
                ['PPKn',  'Selasa', '07:00', '08:30', 'R. 303'],
                ['B.ING', 'Selasa', '08:30', '10:00', 'R. 303'],
                ['TO-C6', 'Selasa', '10:15', '12:15', 'Lab. Kelistrikan'],
                ['TO-C7', 'Rabu',   '07:00', '09:00', 'R. 303'],
                ['PJOK',  'Rabu',   '09:00', '10:30', 'Lapangan'],
                ['TO-C6', 'Rabu',   '10:30', '12:30', 'Lab. Kelistrikan'],
                ['TO-C6', 'Kamis',  '07:00', '09:00', 'Lab. Otomotif'],
                ['KWU',   'Kamis',  '09:00', '10:30', 'R. 303'],
                ['ISMUBA','Jumat',  '07:00', '08:30', 'R. 303'],
                ['MULOK', 'Sabtu',  '07:00', '08:30', 'R. 303'],
                ['BK',    'Sabtu',  '08:30', '09:15', 'R. 303'],
            ],
        ];

        $total   = 0;
        $skipped = 0;
        $i       = 0;

        foreach ($jadwalDefinisi as $namaKelas => $items) {
            $kelasModel = $kelas->get($namaKelas);
            if (! $kelasModel) {
                $this->command->warn("  ⚠  Kelas '{$namaKelas}' tidak ditemukan, dilewati.");
                $skipped++;
                continue;
            }

            foreach ($items as $row) {
                [$kodeMapel, $hari, $jamMulai, $jamSelesai, $ruangan] = $row;

                $mapelModel = $mp->get($kodeMapel);
                if (! $mapelModel) {
                    $this->command->warn("  ⚠  Mapel '{$kodeMapel}' tidak ditemukan, dilewati.");
                    $skipped++;
                    continue;
                }

                Jadwal::updateOrCreate(
                    [
                        'kelas_id'          => $kelasModel->id,
                        'mata_pelajaran_id' => $mapelModel->id,
                        'hari'              => $hari,
                        'jam_mulai'         => $jamMulai,
                    ],
                    [
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

        $this->command->info("✅ {$total} jadwal pelajaran berhasil di-seed" .
            ($skipped ? " ({$skipped} dilewati)" : '') . '.');
    }
}
