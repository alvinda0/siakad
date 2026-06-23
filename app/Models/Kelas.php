<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'nama',
        'tingkat',
        'jurusan',
        'tahun_ajaran',
        'kapasitas',
        'wali_kelas',
        'wali_kelas_id',
        'keterangan',
    ];

    protected $casts = [
        'tahun_ajaran' => 'integer',
        'kapasitas'    => 'integer',
    ];

    // ── Options ──────────────────────────────────────────────────────────────

    public static array $tingkat = [
        'X'   => 'Kelas X',
        'XI'  => 'Kelas XI',
        'XII' => 'Kelas XII',
    ];

    public static array $jurusan = [
        'TKJ' => 'Teknik Jaringan dan Telekomunikasi',
        'TO'  => 'Teknik Otomotif',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    /**
     * Profil siswa yang terdaftar di kelas ini.
     */
    public function siswa(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\KandidatProfile::class, 'kelas_id');
    }

    /**
     * Guru yang menjadi wali kelas.
     */
    public function waliKelas(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'wali_kelas_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeTahunAjaran($query, int $tahun)
    {
        return $query->where('tahun_ajaran', $tahun);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Label tahun ajaran: "2026/2027" */
    public function tahunAjaranLabel(): string
    {
        return $this->tahun_ajaran . '/' . ($this->tahun_ajaran + 1);
    }

    /** Label tingkat + jurusan lengkap */
    public function tingkatJurusanLabel(): string
    {
        return (static::$tingkat[$this->tingkat] ?? $this->tingkat)
            . ' — '
            . (static::$jurusan[$this->jurusan] ?? $this->jurusan);
    }
}
