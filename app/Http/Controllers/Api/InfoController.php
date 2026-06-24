<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ekstrakurikuler;
use App\Models\Informasi;
use Illuminate\Http\JsonResponse;

/**
 * Endpoint publik — konten statis profil sekolah.
 */
class InfoController extends Controller
{
    /**
     * Profil / info umum sekolah.
     */
    public function sekolah(): JsonResponse
    {
        return response()->json([
            'data' => [
                'nama'      => 'SMK Muhammadiyah Sempor',
                'tagline'   => 'Excellent in Taqwa, Science & Professional',
                'alamat'    => 'Jl. Klampok-Gombong Km13, Ds. Sampang, Kec. Sempor, Kab. Kebumen',
                'telepon'   => '081325540947',
                'email'     => 'smkmuhse@gmail.com',
                'maps_url'  => 'https://maps.google.com/?q=SMK+Muhammadiyah+Sempor',
                'jam_operasional' => [
                    'senin_jumat' => '07.00 – 16.00 WIB',
                    'sabtu'       => '07.00 – 12.00 WIB',
                ],
                'statistik' => [
                    'siswa_aktif'       => '1.200+',
                    'tenaga_pendidik'   => '85+',
                    'prestasi'          => '200+',
                    'ekstrakurikuler'   => '20+',
                ],
            ],
        ]);
    }

    /**
     * Daftar kegiatan sekolah.
     */
    public function kegiatan(): JsonResponse
    {
        $kegiatan = [
            [
                'id'        => 1,
                'icon'      => '🎓',
                'judul'     => 'Upacara Hari Pendidikan Nasional',
                'tanggal'   => '2025-05-02',
                'kategori'  => 'Nasional',
                'deskripsi' => 'Peringatan Hari Pendidikan Nasional dengan upacara bendera dan pidato Mendikbud.',
            ],
            [
                'id'        => 2,
                'icon'      => '🔬',
                'judul'     => 'Olimpiade Sains Tingkat Kabupaten',
                'tanggal'   => '2025-05-15',
                'kategori'  => 'Akademik',
                'deskripsi' => 'Kompetisi sains Matematika, Fisika, Kimia, dan Biologi antar sekolah se-Kabupaten Kebumen.',
            ],
            [
                'id'        => 3,
                'icon'      => '🎭',
                'judul'     => 'Festival Seni & Budaya',
                'tanggal'   => '2025-05-22',
                'kategori'  => 'Seni',
                'deskripsi' => 'Pentas seni tahunan menampilkan tari tradisional, drama, musik, dan pameran karya siswa.',
            ],
            [
                'id'        => 4,
                'icon'      => '⚽',
                'judul'     => 'Turnamen Olahraga Antar Kelas',
                'tanggal'   => '2025-05-28',
                'kategori'  => 'Olahraga',
                'deskripsi' => 'Pertandingan sepak bola, voli, dan basket antar kelas untuk mempererat kebersamaan.',
            ],
            [
                'id'        => 5,
                'icon'      => '📚',
                'judul'     => 'Seminar Motivasi & Karir',
                'tanggal'   => '2025-06-05',
                'kategori'  => 'Akademik',
                'deskripsi' => 'Seminar bersama alumni sukses dan pakar karir untuk mempersiapkan siswa meraih masa depan.',
            ],
            [
                'id'        => 6,
                'icon'      => '🌿',
                'judul'     => 'Gerakan Sekolah Hijau',
                'tanggal'   => '2025-06-10',
                'kategori'  => 'Lingkungan',
                'deskripsi' => 'Penanaman pohon dan kerja bakti membersihkan lingkungan sekolah bersama seluruh warga sekolah.',
            ],
            [
                'id'        => 7,
                'icon'      => '🕌',
                'judul'     => 'Pesantren Kilat Ramadan',
                'tanggal'   => '2025-03-15',
                'kategori'  => 'Akademik',
                'deskripsi' => 'Kegiatan pesantren kilat selama Ramadan untuk memperdalam ilmu agama dan membangun karakter Islami.',
            ],
            [
                'id'        => 8,
                'icon'      => '🎖️',
                'judul'     => 'Perkemahan Pramuka Gudep',
                'tanggal'   => '2025-04-12',
                'kategori'  => 'Olahraga',
                'deskripsi' => 'Perkemahan penggalang tingkat ramu dan rakit selama tiga hari dua malam di Bumi Perkemahan Sempor.',
            ],
            [
                'id'        => 9,
                'icon'      => '💻',
                'judul'     => 'Workshop Teknologi & AI',
                'tanggal'   => '2025-04-20',
                'kategori'  => 'Akademik',
                'deskripsi' => 'Workshop pengenalan kecerdasan buatan dan pemrograman Python bagi siswa jurusan TJKT dan RPL.',
            ],
        ];

        return response()->json([
            'data'  => $kegiatan,
            'total' => count($kegiatan),
        ]);
    }

    /**
     * Daftar prestasi sekolah.
     */
    public function prestasi(): JsonResponse
    {
        $prestasi = [
            ['id' => 1,  'medali' => '🥇', 'judul' => 'Juara 1 LKS Teknik Komputer',  'level' => 'Nasional',  'tahun' => '2024', 'nama' => 'Ahmad Fauzi'],
            ['id' => 2,  'medali' => '🥇', 'judul' => 'Juara 1 Karya Tulis Ilmiah',   'level' => 'Provinsi',  'tahun' => '2024', 'nama' => 'Siti Rahayu'],
            ['id' => 3,  'medali' => '🥈', 'judul' => 'Juara 2 Olimpiade Matematika', 'level' => 'Nasional',  'tahun' => '2024', 'nama' => 'Budi Santoso'],
            ['id' => 4,  'medali' => '🏆', 'judul' => 'Sekolah Adiwiyata',            'level' => 'Nasional',  'tahun' => '2024', 'nama' => 'Institusi'],
            ['id' => 5,  'medali' => '🥇', 'judul' => 'Juara 1 Debat Bahasa Inggris', 'level' => 'Provinsi',  'tahun' => '2025', 'nama' => 'Dewi Lestari'],
            ['id' => 6,  'medali' => '🥈', 'judul' => 'Juara 2 Paskibraka',           'level' => 'Kabupaten', 'tahun' => '2024', 'nama' => 'Rudi Hermawan'],
            ['id' => 7,  'medali' => '🎖️', 'judul' => 'Finalis Lomba Robotika',       'level' => 'Nasional',  'tahun' => '2025', 'nama' => 'Tim Robotika'],
            ['id' => 8,  'medali' => '🥇', 'judul' => 'Juara 1 Futsal Pelajar',       'level' => 'Kabupaten', 'tahun' => '2025', 'nama' => 'Tim Futsal'],
            ['id' => 9,  'medali' => '🥇', 'judul' => 'Juara 1 Lomba Tari Daerah',    'level' => 'Kabupaten', 'tahun' => '2024', 'nama' => 'Grup Tari SMK'],
            ['id' => 10, 'medali' => '🥉', 'judul' => 'Juara 3 Olimpiade Fisika',     'level' => 'Provinsi',  'tahun' => '2024', 'nama' => 'Rizki Pratama'],
            ['id' => 11, 'medali' => '🥇', 'judul' => 'Juara 1 PMR Cerdas Cermat',    'level' => 'Kabupaten', 'tahun' => '2025', 'nama' => 'Tim PMR'],
            ['id' => 12, 'medali' => '🥈', 'judul' => 'Juara 2 Pencak Silat',         'level' => 'Provinsi',  'tahun' => '2025', 'nama' => 'Aldi Firmansyah'],
        ];

        $statistik = [
            'total'     => count($prestasi),
            'nasional'  => collect($prestasi)->where('level', 'Nasional')->count(),
            'provinsi'  => collect($prestasi)->where('level', 'Provinsi')->count(),
            'kabupaten' => collect($prestasi)->where('level', 'Kabupaten')->count(),
        ];

        return response()->json([
            'statistik' => $statistik,
            'data'      => $prestasi,
        ]);
    }

    /**
     * Daftar ekstrakurikuler.
     */
    public function ekskul(): JsonResponse
    {
        $ekskul = [
            ['id' => 1,  'emoji' => '⚽',  'nama' => 'Futsal',         'anggota' => 48],
            ['id' => 2,  'emoji' => '🏐',  'nama' => 'Bola Voli',      'anggota' => 36],
            ['id' => 3,  'emoji' => '🥋',  'nama' => 'Pencak Silat',   'anggota' => 30],
            ['id' => 4,  'emoji' => '🎭',  'nama' => 'Teater & Drama', 'anggota' => 25],
            ['id' => 5,  'emoji' => '🎵',  'nama' => 'Paduan Suara',   'anggota' => 40],
            ['id' => 6,  'emoji' => '🤖',  'nama' => 'Robotika',       'anggota' => 22],
            ['id' => 7,  'emoji' => '📸',  'nama' => 'Fotografi',      'anggota' => 28],
            ['id' => 8,  'emoji' => '🌿',  'nama' => 'Pramuka',        'anggota' => 120],
            ['id' => 9,  'emoji' => '🎨',  'nama' => 'Seni Lukis',     'anggota' => 20],
            ['id' => 10, 'emoji' => '📰',  'nama' => 'Jurnalistik',    'anggota' => 18],
            ['id' => 11, 'emoji' => '🏹',  'nama' => 'Paskibraka',     'anggota' => 60],
            ['id' => 12, 'emoji' => '💻',  'nama' => 'Coding & IT',    'anggota' => 35],
        ];

        return response()->json([
            'data'  => $ekskul,
            'total' => count($ekskul),
        ]);
    }

    /**
     * Berita terkini.
     */
    public function berita(): JsonResponse
    {
        $berita = [
            [
                'id'        => 1,
                'kategori'  => 'Akademik',
                'tanggal'   => '2025-05-20',
                'judul'     => 'SMK Muhammadiyah Sempor Raih Kelulusan 100% di Tahun 2025',
                'ringkasan' => 'Seluruh siswa kelas XII berhasil lulus dengan nilai rata-rata melampaui standar nasional.',
            ],
            [
                'id'        => 2,
                'kategori'  => 'Prestasi',
                'tanggal'   => '2025-05-18',
                'judul'     => 'Tim LKS Teknik Komputer Lolos ke Babak Final Nasional',
                'ringkasan' => 'Tim LKS SMK Muhammadiyah Sempor berhasil melewati babak penyisihan dan siap bertarung di final.',
            ],
            [
                'id'        => 3,
                'kategori'  => 'Kegiatan',
                'tanggal'   => '2025-05-15',
                'judul'     => 'PPDB 2025/2026 Resmi Dibuka, Ini Jadwal dan Syaratnya',
                'ringkasan' => 'Penerimaan Peserta Didik Baru tahun ajaran 2025/2026 resmi dibuka. Pendaftaran dapat dilakukan secara online.',
            ],
        ];

        return response()->json([
            'data'  => $berita,
            'total' => count($berita),
        ]);
    }

    // ── Informasi dari Database ───────────────────────────────────────────────

    /**
     * Semua informasi aktif (beasiswa + promo), dikelompokkan per tipe.
     * GET /api/info/informasi
     */
    public function informasi(): JsonResponse
    {
        $data = Informasi::aktif()
            ->orderBy('tipe')
            ->orderBy('urutan')
            ->orderBy('jenis')
            ->get(['id', 'tipe', 'jenis', 'syarat', 'benefit', 'urutan']);

        $grouped = $data->groupBy('tipe')->map(fn($items, $tipe) => [
            'tipe'   => $tipe,
            'label'  => Informasi::$tipe[$tipe] ?? $tipe,
            'data'   => $items->values(),
        ])->values();

        return response()->json([
            'data'  => $grouped,
            'total' => $data->count(),
        ]);
    }

    /**
     * Hanya informasi beasiswa aktif.
     * GET /api/info/beasiswa
     */
    public function beasiswa(): JsonResponse
    {
        $data = Informasi::aktif()
            ->beasiswa()
            ->orderBy('urutan')
            ->orderBy('jenis')
            ->get(['id', 'tipe', 'jenis', 'syarat', 'benefit', 'urutan']);

        return response()->json([
            'tipe'  => 'beasiswa',
            'label' => 'Informasi Beasiswa',
            'data'  => $data,
            'total' => $data->count(),
        ]);
    }

    /**
     * Hanya promo program strategis aktif.
     * GET /api/info/promo
     */
    public function promo(): JsonResponse
    {
        $data = Informasi::aktif()
            ->promo()
            ->orderBy('urutan')
            ->orderBy('jenis')
            ->get(['id', 'tipe', 'jenis', 'syarat', 'benefit', 'urutan']);

        return response()->json([
            'tipe'  => 'promo',
            'label' => 'Promo Program Strategis',
            'data'  => $data,
            'total' => $data->count(),
        ]);
    }
}
