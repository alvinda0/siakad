<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalUjian extends Model
{
    protected $table = 'jadwal_ujian';

    protected $fillable = [
        'nama',
        'jenis',
        'kelas_id',
        'mata_pelajaran_id',
        'guru_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'ruangan',
        'keterangan',
        'aktif',
        'file_soal',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'aktif'   => 'boolean',
    ];

    // ── Options ──────────────────────────────────────────────────────────────

    public static array $jenis = [
        'UTS'      => 'UTS — Ulangan Tengah Semester',
        'UAS'      => 'UAS — Ulangan Akhir Semester',
        'UKK'      => 'UKK — Ujian Kompetensi Keahlian',
        'Sumatif'  => 'Sumatif',
        'Lainnya'  => 'Lainnya',
    ];

    public static array $jenisColor = [
        'UTS'     => 'blue',
        'UAS'     => 'purple',
        'UKK'     => 'rose',
        'Sumatif' => 'amber',
        'Lainnya' => 'slate',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function soalList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SoalUjian::class, 'jadwal_ujian_id')->orderBy('nomor');
    }

    public function sesiList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SesiUjian::class, 'jadwal_ujian_id');
    }

    public function jawabanList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JawabanUjian::class, 'jadwal_ujian_id');
    }

    /**
     * Sesi ujian milik murid tertentu.
     */
    public function sesiMurid(int $muridId): ?SesiUjian
    {
        return $this->sesiList()->where('murid_id', $muridId)->first();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function jamLabel(): string
    {
        return substr($this->jam_mulai, 0, 5) . ' – ' . substr($this->jam_selesai, 0, 5);
    }

    public function tanggalLabel(): string
    {
        $hari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
                 'Saturday' => 'Sabtu'];
        $bulan = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
                  'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
                  'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                  'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];

        $d = $this->tanggal->format('l j F Y');
        foreach ($hari   as $en => $id) $d = str_replace($en, $id, $d);
        foreach ($bulan  as $en => $id) $d = str_replace($en, $id, $d);
        return $d;
    }

    public function jenisLabel(): string
    {
        return static::$jenis[$this->jenis] ?? $this->jenis;
    }

    public function isSudahLewat(): bool
    {
        return $this->tanggal->isPast();
    }
}
