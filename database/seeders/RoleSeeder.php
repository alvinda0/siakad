<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name'        => Role::STUDENT,
                'label'       => 'Student',
                'description' => 'Siswa aktif yang terdaftar di sekolah.',
            ],
            [
                'name'        => Role::TEACHER,
                'label'       => 'Teacher',
                'description' => 'Guru / tenaga pengajar.',
            ],
            [
                'name'        => Role::CANDIDATE,
                'label'       => 'Candidate',
                'description' => 'Calon siswa baru (PPDB).',
            ],
            [
                'name'        => Role::ADMIN,
                'label'       => 'Admin',
                'description' => 'Administrator sekolah.',
            ],
            [
                'name'        => Role::SUPERADMIN,
                'label'       => 'SuperAdmin',
                'description' => 'Super administrator dengan akses penuh.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }

        $this->command->info('✅ 5 roles berhasil dibuat: Student, Teacher, Candidate, Admin, SuperAdmin');
    }
}
