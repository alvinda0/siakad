<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ekstrakurikuler extends Model
{
    protected $table = 'ekstrakurikuler';

    protected $fillable = [
        'nama',
        'jenis',
        'jumlah_anggota',
        'pembina',
        'deskripsi',
        'jadwal',
        'gambar',
        'aktif',
        'urutan',
    ];

    protected $casts = [
        'aktif'          => 'boolean',
        'urutan'         => 'integer',
        'jumlah_anggota' => 'integer',
    ];

    // ── Options ──────────────────────────────────────────────────────────────

    public static array $jenis = [
        'Wajib'   => 'Wajib',
        'Pilihan' => 'Pilihan',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeJenis($query, string $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * URL gambar: storage upload > null.
     */
    public function gambarUrl(): ?string
    {
        if ($this->gambar) {
            return asset('storage/' . $this->gambar);
        }

        return null;
    }
}
