<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanSekolah extends Model
{
    protected $table = 'kegiatan_sekolah';

    protected $fillable = [
        'judul',
        'kategori',
        'tanggal_kegiatan',
        'deskripsi',
        'gambar',
        'aktif',
        'urutan',
    ];

    protected $casts = [
        'aktif'  => 'boolean',
        'urutan' => 'integer',
    ];

    // ── Options ──────────────────────────────────────────────────────────────

    public static array $kategori = [
        'Akademik'    => 'Akademik',
        'Nasional'    => 'Nasional',
        'Seni'        => 'Seni',
        'Olahraga'    => 'Olahraga',
        'Lingkungan'  => 'Lingkungan',
        'Lainnya'     => 'Lainnya',
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
     * URL gambar: storage upload > fallback placeholder per kategori > default.
     */
    public function gambarUrl(): string
    {
        if ($this->gambar) {
            // File uploaded ke storage/app/public/kegiatan/
            return asset('storage/' . $this->gambar);
        }

        // Placeholder lokal per kategori
        $map = [
            'Akademik'   => 'akademik.svg',
            'Nasional'   => 'nasional.svg',
            'Seni'       => 'seni.svg',
            'Olahraga'   => 'olahraga.svg',
            'Lingkungan' => 'lingkungan.svg',
            'Lainnya'    => 'lainnya.svg',
        ];

        $file = $map[$this->kategori] ?? 'lainnya.svg';

        return asset('image/kegiatan/' . $file);
    }
}
