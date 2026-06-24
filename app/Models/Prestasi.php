<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    protected $table = 'prestasi';

    protected $fillable = [
        'judul',
        'nama_peraih',
        'tingkat',
        'medali',
        'tahun',
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

    public static array $tingkat = [
        'Nasional'  => 'Nasional',
        'Provinsi'  => 'Provinsi',
        'Kabupaten' => 'Kabupaten',
        'Kecamatan' => 'Kecamatan',
        'Desa'      => 'Desa',
        'Lainnya'   => 'Lainnya',
    ];

    public static array $medaliOptions = [
        '🥇' => '🥇 Juara Umum 1',
        '🥈' => '🥈 Juara Umum 2',
        '🥉' => '🥉 Juara Umum 3',
        '🏆' => '🏆 Juara 1',
        '🎖️' => '🎖️ Juara 2',
        '🏅' => '🏅 Juara 3',
        '⭐' => '⭐ Lainnya',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeTingkat($query, string $tingkat)
    {
        return $query->where('tingkat', $tingkat);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Warna badge berdasarkan tingkat.
     */
    public function tingkatColor(): array
    {
        return match ($this->tingkat) {
            'Nasional'  => ['text' => '#B91C1C', 'bg' => '#FEE2E218'],
            'Provinsi'  => ['text' => '#1B7A8A', 'bg' => '#E0F4F718'],
            'Kabupaten' => ['text' => '#E6920A', 'bg' => '#FEF3C718'],
            'Kecamatan' => ['text' => '#7C3AED', 'bg' => '#EDE9FE18'],
            'Desa'      => ['text' => '#065F46', 'bg' => '#D1FAE518'],
            'Lainnya'   => ['text' => '#64748B', 'bg' => '#F1F5F918'],
            default     => ['text' => '#64748B', 'bg' => '#F1F5F918'],
        };
    }

    /**
     * URL gambar: storage upload atau null.
     */
    public function gambarUrl(): ?string
    {
        if ($this->gambar) {
            return asset('storage/' . $this->gambar);
        }

        return null;
    }

    /**
     * Orientasi gambar: 'portrait' | 'landscape' | null (jika tidak ada gambar).
     */
    public function gambarOrientasi(): ?string
    {
        if (! $this->gambar) {
            return null;
        }

        $path = storage_path('app/public/' . $this->gambar);

        if (! file_exists($path)) {
            return null;
        }

        $size = @getimagesize($path);

        if (! $size) {
            return null;
        }

        [$width, $height] = $size;

        return $height > $width ? 'portrait' : 'landscape';
    }
}
