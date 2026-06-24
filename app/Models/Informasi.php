<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Informasi extends Model
{
    protected $table = 'informasi';

    protected $fillable = [
        'tipe',
        'jenis',
        'syarat',
        'benefit',
        'aktif',
        'urutan',
    ];

    protected $casts = [
        'aktif'   => 'boolean',
        'urutan'  => 'integer',
    ];

    // ── Options ──────────────────────────────────────────────────────────────

    public static array $tipe = [
        'beasiswa' => 'Informasi Beasiswa',
        'promo'    => 'Promo Program Strategis',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeBeasiswa($query)
    {
        return $query->where('tipe', 'beasiswa');
    }

    public function scopePromo($query)
    {
        return $query->where('tipe', 'promo');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function tipeLabel(): string
    {
        return static::$tipe[$this->tipe] ?? $this->tipe;
    }
}
