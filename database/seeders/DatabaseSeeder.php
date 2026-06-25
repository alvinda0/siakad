<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\JadwalSeeder;
use Database\Seeders\JadwalUjianSeeder;
use Database\Seeders\KelasSeeder;
use Database\Seeders\MataPelajaranSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Daftar default user per role.
     * password plain-text ditampilkan di console, lalu di-hash sebelum disimpan.
     */
    private array $defaultUsers = [
        [
            'name'     => 'Super Admin',
            'email'    => 'superadmin@smkmuhsempor.sch.id',
            'password' => 'superadmin123',
            'role'     => Role::SUPERADMIN,
        ],
        [
            'name'     => 'Admin Sekolah',
            'email'    => 'admin@smkmuhsempor.sch.id',
            'password' => 'admin123',
            'role'     => Role::ADMIN,
        ],
        [
            'name'     => 'Budi Santoso',
            'email'    => 'guru@smkmuhsempor.sch.id',
            'password' => 'teacher123',
            'role'     => Role::TEACHER,
        ],
        [
            'name'     => 'Siti Rahayu',
            'email'    => 'siswa@smkmuhsempor.sch.id',
            'password' => 'student123',
            'role'     => Role::STUDENT,
        ],
        [
            'name'     => 'Ahmad Fauzi',
            'email'    => 'calon@smkmuhsempor.sch.id',
            'password' => 'candidate123',
            'role'     => Role::CANDIDATE,
        ],
    ];

    public function run(): void
    {
        // 1. Seed semua roles terlebih dahulu
        $this->call(RoleSeeder::class);

        // 2. Seed kelas
        $this->call(KelasSeeder::class);

        // 3. Seed mata pelajaran
        $this->call(MataPelajaranSeeder::class);

        // 4. Seed jadwal pelajaran
        $this->call(JadwalSeeder::class);

        // 5. Seed jadwal ujian
        $this->call(JadwalUjianSeeder::class);

        // 6. Buat satu user untuk setiap role
        $rows = [];

        foreach ($this->defaultUsers as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make($data['password']),
                    'email_verified_at' => now(),
                ]
            );

            $user->assignRole($data['role']);

            $rows[] = [
                $data['name'],
                $data['email'],
                $data['password'],   // tampilkan plain-text di console saja
                ucfirst($data['role']),
            ];
        }

        $this->command->newLine();
        $this->command->info('✅ Default users berhasil dibuat:');
        $this->command->table(
            ['Nama', 'Email', 'Password', 'Role'],
            $rows
        );
        $this->command->newLine();
        $this->command->warn('⚠️  Segera ganti password di atas setelah deploy ke production!');
    }
}
