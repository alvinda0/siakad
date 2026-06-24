<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    protected $table = 'fasilitas';

    protected $fillable = [
        'nama',
        'deskripsi',
        'fitur',
        'gambar',
        'kategori',
        'aktif',
        'urutan',
    ];

    protected $casts = [
        'fitur'  => 'array',
        'aktif'  => 'boolean',
        'urutan' => 'integer',
    ];

    // ── Options ──────────────────────────────────────────────────────────────

    public static array $kategori = [
        'Akademik'   => 'Akademik',
        'Olahraga'   => 'Olahraga',
        'Kesehatan'  => 'Kesehatan',
        'Keagamaan'  => 'Keagamaan',
        'Umum'       => 'Umum',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeKategori($query, string $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * URL gambar dari storage, atau null jika tidak ada.
     */
    public function gambarUrl(): ?string
    {
        if ($this->gambar) {
            return asset('storage/' . $this->gambar);
        }

        return null;
    }

    /**
     * Fitur sebagai array (sudah di-cast, tapi helper untuk null-safety).
     */
    public function fiturList(): array
    {
        return $this->fitur ?? [];
    }
}
