<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';

    protected $fillable = [
        'kode',
        'nama',
        'guru_id',
        'jurusan',
        'tingkat',
        'deskripsi',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function jadwal(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Jadwal::class, 'mata_pelajaran_id');
    }

    public function nilaiList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Nilai::class, 'mata_pelajaran_id');
    }

    // ── Options ──────────────────────────────────────────────────────────────

    public static array $jurusan = [
        'Semua' => 'Semua Jurusan',
        'TKJ'   => 'Teknik Jaringan dan Telekomunikasi',
        'TO'    => 'Teknik Otomotif',
    ];

    public static array $tingkat = [
        'Semua' => 'Semua Tingkat',
        'X'     => 'Kelas X',
        'XI'    => 'Kelas XI',
        'XII'   => 'Kelas XII',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function jurusanLabel(): string
    {
        return static::$jurusan[$this->jurusan] ?? $this->jurusan;
    }

    public function tingkatLabel(): string
    {
        return static::$tingkat[$this->tingkat] ?? $this->tingkat;
    }
}
