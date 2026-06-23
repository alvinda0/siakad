<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KandidatProfile extends Model
{
    protected $fillable = [
        // Step 0
        'user_id', 'kelas_id', 'didaftarkan_oleh',
        // Step 1
        'jurusan', 'sistem_pendidikan',
        // Step 2 - Data Diri
        'nik', 'nisn', 'nama_lengkap', 'nama_panggilan', 'kewarganegaraan',
        'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama',
        'status_keluarga', 'status_dalam_keluarga',
        'anak_ke', 'dari_saudara',
        'total_saudara_kandung', 'total_saudara_tiri', 'total_saudara_angkat',
        'asal_sekolah', 'lama_belajar', 'nomor_ijazah', 'tanggal_ijazah', 'npsn',
        'penerima_kip', 'nomor_kip', 'status_tinggal', 'bahasa_sehari_hari',
        'saudara_di_sekolah', 'moda_transportasi', 'jarak_sekolah_km', 'waktu_tempuh_jam',
        'foto',
        // Kontak
        'no_hp', 'email',
        // Alamat
        'provinsi', 'kabupaten', 'kecamatan', 'desa', 'rt', 'rw', 'alamat_lengkap',
        // Step 3 - Kesehatan
        'riwayat_kesehatan', 'disabilitas', 'tinggi_badan', 'berat_badan',
        // Step 4 - Dokumen
        'dokumen_kk', 'dokumen_ijazah',
        // Step 5 - Informasi Tambahan
        'prestasi',
        // Step 6 - Orang Tua - Ayah
        'nama_ayah', 'nik_ayah', 'pendidikan_ayah', 'pekerjaan_ayah',
        'status_pernikahan_ayah', 'no_hp_ayah', 'tempat_lahir_ayah',
        'tanggal_lahir_ayah', 'kewarganegaraan_ayah', 'agama_ayah',
        // Step 6 - Orang Tua - Ibu
        'nama_ibu', 'nik_ibu', 'pendidikan_ibu', 'pekerjaan_ibu',
        'status_pernikahan_ibu', 'no_hp_ibu', 'tempat_lahir_ibu',
        'tanggal_lahir_ibu', 'kewarganegaraan_ibu', 'agama_ibu',
        // Penghasilan
        'penghasilan_ortu',
        // Status
        'status', 'current_step',
    ];

    protected $casts = [
        'tanggal_lahir'       => 'date',
        'tanggal_ijazah'      => 'date',
        'tanggal_lahir_ayah'  => 'date',
        'tanggal_lahir_ibu'   => 'date',
        'penerima_kip'        => 'boolean',
        'prestasi'            => 'array',
    ];

    // ── Options ──────────────────────────────────────────────────────────────

    public static array $jurusan = [
        'TKJ'  => 'Teknik Jaringan dan Telekomunikasi',
        'TO'   => 'Teknik Otomotif',
    ];

    public static array $sistemPendidikan = [
        'Reguler' => 'Reguler',
        'Pondok'  => 'Pondok',
        'Panti'   => 'Panti',
    ];

    public static array $didaftarkanOleh = [
        'Diri Sendiri' => 'Diri Sendiri',
        'Ayah'         => 'Ayah',
        'Ibu'          => 'Ibu',
        'Saudara'      => 'Saudara',
        'Guru'         => 'Guru',
    ];

    public static array $agama = [
        'Islam'     => 'Islam',
        'Kristen'   => 'Kristen',
        'Katolik'   => 'Katolik',
        'Hindu'     => 'Hindu',
        'Buddha'    => 'Buddha',
        'Konghucu'  => 'Konghucu',
    ];

    public static array $jenisKelamin = [
        'L' => 'Laki-laki',
        'P' => 'Perempuan',
    ];

    public static array $statusTinggal = [
        'Bersama Orang Tua'  => 'Bersama Orang Tua',
        'Kost / Kontrak'     => 'Kost / Kontrak',
        'Asrama'             => 'Asrama',
        'Pondok Pesantren'   => 'Pondok Pesantren',
        'Panti Asuhan'       => 'Panti Asuhan',
        'Lainnya'            => 'Lainnya',
    ];

    public static array $modaTransportasi = [
        'Jalan Kaki'       => 'Jalan Kaki',
        'Sepeda'           => 'Sepeda',
        'Sepeda Motor'     => 'Sepeda Motor',
        'Mobil Pribadi'    => 'Mobil Pribadi',
        'Angkutan Umum'    => 'Angkutan Umum',
        'Ojek'             => 'Ojek',
        'Lainnya'          => 'Lainnya',
    ];

    public static array $pendidikanOrtu = [
        'Tidak Sekolah'  => 'Tidak Sekolah',
        'SD / Sederajat' => 'SD / Sederajat',
        'SMP / Sederajat'=> 'SMP / Sederajat',
        'SMA / Sederajat'=> 'SMA / Sederajat',
        'D1'             => 'D1',
        'D2'             => 'D2',
        'D3'             => 'D3',
        'D4 / S1'        => 'D4 / S1',
        'S2'             => 'S2',
        'S3'             => 'S3',
    ];

    public static array $pekerjaanOrtu = [
        'Tidak Bekerja'      => 'Tidak Bekerja',
        'PNS / TNI / POLRI'  => 'PNS / TNI / POLRI',
        'Pegawai Swasta'     => 'Pegawai Swasta',
        'Wiraswasta'         => 'Wiraswasta',
        'Petani / Nelayan'   => 'Petani / Nelayan',
        'Buruh'              => 'Buruh',
        'Pedagang'           => 'Pedagang',
        'Dokter / Tenaga Kesehatan' => 'Dokter / Tenaga Kesehatan',
        'Guru / Dosen'       => 'Guru / Dosen',
        'Lainnya'            => 'Lainnya',
    ];

    public static array $statusPernikahan = [
        'Menikah'        => 'Menikah',
        'Cerai Hidup'    => 'Cerai Hidup',
        'Cerai Mati'     => 'Cerai Mati',
        'Tidak Diketahui'=> 'Tidak Diketahui',
    ];

    public static array $disabilitas = [
        'Tidak Ada'          => 'Tidak Ada',
        'Tuna Netra'         => 'Tuna Netra',
        'Tuna Rungu'         => 'Tuna Rungu',
        'Tuna Wicara'        => 'Tuna Wicara',
        'Tuna Daksa'         => 'Tuna Daksa',
        'Tuna Grahita'       => 'Tuna Grahita',
        'Tuna Laras'         => 'Tuna Laras',
        'Tuna Ganda'         => 'Tuna Ganda',
        'Lainnya'            => 'Lainnya',
    ];

    public static array $penghasilanOrtu = [
        'Kurang dari Rp 500.000'           => 'Kurang dari Rp 500.000',
        'Rp 500.000 – Rp 1.000.000'        => 'Rp 500.000 – Rp 1.000.000',
        'Rp 1.000.000 – Rp 2.000.000'      => 'Rp 1.000.000 – Rp 2.000.000',
        'Rp 2.000.000 – Rp 3.000.000'      => 'Rp 2.000.000 – Rp 3.000.000',
        'Rp 3.000.000 – Rp 5.000.000'      => 'Rp 3.000.000 – Rp 5.000.000',
        'Rp 5.000.000 – Rp 10.000.000'     => 'Rp 5.000.000 – Rp 10.000.000',
        'Lebih dari Rp 10.000.000'          => 'Lebih dari Rp 10.000.000',
    ];

    public static array $statusKeluarga = [
        'Yatim'           => 'Yatim',
        'Piatu'           => 'Piatu',
        'Yatim Piatu'     => 'Yatim Piatu',
        'Keluarga Lengkap'=> 'Keluarga Lengkap',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Kelas::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Label jurusan yang lebih panjang */
    public function jurusanLabel(): string
    {
        return static::$jurusan[$this->jurusan] ?? $this->jurusan;
    }

    /** Total langkah form (0..6) */
    public static function totalSteps(): int
    {
        return 7; // 0=Pendaftar, 1=Pendidikan, 2=DataDiri, 3=Kesehatan, 4=Dokumen, 5=InfoTambahan, 6=OrangTua
    }
}
