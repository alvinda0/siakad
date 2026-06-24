<?php

namespace Database\Seeders;

use App\Models\KegiatanSekolah;
use Illuminate\Database\Seeder;

class KegiatanSekolahSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'judul'            => 'Upacara Hari Pendidikan Nasional',
                'kategori'         => 'Nasional',
                'tanggal_kegiatan' => '2 Mei 2025',
                'deskripsi'        => 'Peringatan Hari Pendidikan Nasional dengan upacara bendera dan pidato inspiratif dari Kepala Sekolah serta pemberian penghargaan kepada siswa berprestasi.',
                'gambar'           => null,
                'aktif'            => true,
                'urutan'           => 1,
            ],
            [
                'judul'            => 'Olimpiade Sains Tingkat Kabupaten',
                'kategori'         => 'Akademik',
                'tanggal_kegiatan' => '15 Mei 2025',
                'deskripsi'        => 'Kompetisi sains Matematika, Fisika, Kimia, dan Biologi antar sekolah se-Kabupaten Kebumen. SMK Muh. Sempor meraih 3 medali emas dan 2 perak.',
                'gambar'           => null,
                'aktif'            => true,
                'urutan'           => 2,
            ],
            [
                'judul'            => 'Festival Seni & Budaya',
                'kategori'         => 'Seni',
                'tanggal_kegiatan' => '22 Mei 2025',
                'deskripsi'        => 'Pentas seni tahunan menampilkan tari tradisional, drama musikal, pameran karya lukis, dan penampilan band siswa. Dihadiri lebih dari 500 penonton.',
                'gambar'           => null,
                'aktif'            => true,
                'urutan'           => 3,
            ],
            [
                'judul'            => 'Turnamen Olahraga Antar Kelas',
                'kategori'         => 'Olahraga',
                'tanggal_kegiatan' => '28 Mei 2025',
                'deskripsi'        => 'Pertandingan sepak bola, voli, dan basket antar kelas untuk mempererat kebersamaan dan menjaring bibit atlet berprestasi dari kalangan siswa.',
                'gambar'           => null,
                'aktif'            => true,
                'urutan'           => 4,
            ],
            [
                'judul'            => 'Seminar Motivasi & Karir',
                'kategori'         => 'Akademik',
                'tanggal_kegiatan' => '5 Juni 2025',
                'deskripsi'        => 'Seminar bersama alumni sukses dan pakar karir untuk mempersiapkan siswa meraih masa depan gemilang di dunia kerja maupun perguruan tinggi.',
                'gambar'           => null,
                'aktif'            => true,
                'urutan'           => 5,
            ],
            [
                'judul'            => 'Gerakan Sekolah Hijau',
                'kategori'         => 'Lingkungan',
                'tanggal_kegiatan' => '10 Juni 2025',
                'deskripsi'        => 'Penanaman 200 pohon di lingkungan sekolah dan sekitar, diikuti kerja bakti membersihkan lingkungan bersama seluruh warga sekolah.',
                'gambar'           => null,
                'aktif'            => true,
                'urutan'           => 6,
            ],
            [
                'judul'            => 'Lomba Pidato Bahasa Indonesia',
                'kategori'         => 'Akademik',
                'tanggal_kegiatan' => '17 Juni 2025',
                'deskripsi'        => 'Kompetisi pidato tingkat sekolah dalam rangka memperingati hari lahir Pancasila. Diikuti 48 peserta dari seluruh kelas X, XI, dan XII.',
                'gambar'           => null,
                'aktif'            => true,
                'urutan'           => 7,
            ],
            [
                'judul'            => 'Kemah Pramuka',
                'kategori'         => 'Nasional',
                'tanggal_kegiatan' => '24–26 Juni 2025',
                'deskripsi'        => 'Kegiatan perkemahan Pramuka Penggalang di Bumi Perkemahan Selakambang Purbalingga. Melatih kemandirian, kepemimpinan, dan kecintaan alam.',
                'gambar'           => null,
                'aktif'            => true,
                'urutan'           => 8,
            ],
            [
                'judul'            => 'Pameran Karya Seni Siswa',
                'kategori'         => 'Seni',
                'tanggal_kegiatan' => '30 Juni 2025',
                'deskripsi'        => 'Pameran hasil karya seni rupa, fotografi, dan desain grafis siswa angkatan 2025. Menampilkan lebih dari 120 karya dari 80 siswa terpilih.',
                'gambar'           => null,
                'aktif'            => true,
                'urutan'           => 9,
            ],
        ];

        foreach ($data as $item) {
            KegiatanSekolah::firstOrCreate(
                ['judul' => $item['judul']],
                $item
            );
        }
    }
}
