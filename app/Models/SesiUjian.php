<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SesiUjian extends Model
{
    protected $table = 'sesi_ujian';

    protected $fillable = [
        'jadwal_ujian_id',
        'murid_id',
        'status',
        'mulai_at',
        'selesai_at',
        'nilai_pg',
        'nilai_essay',
        'nilai_total',
    ];

    protected $casts = [
        'mulai_at'    => 'datetime',
        'selesai_at'  => 'datetime',
        'nilai_pg'    => 'decimal:2',
        'nilai_essay' => 'decimal:2',
        'nilai_total' => 'decimal:2',
    ];

    // ── Options ──────────────────────────────────────────────────────────────

    public static array $status = [
        'belum'   => 'Belum Mulai',
        'sedang'  => 'Sedang Berlangsung',
        'selesai' => 'Sudah Selesai',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function jadwalUjian(): BelongsTo
    {
        return $this->belongsTo(JadwalUjian::class, 'jadwal_ujian_id');
    }

    public function murid(): BelongsTo
    {
        return $this->belongsTo(User::class, 'murid_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isBelum(): bool   { return $this->status === 'belum'; }
    public function isSedang(): bool  { return $this->status === 'sedang'; }
    public function isSelesai(): bool { return $this->status === 'selesai'; }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'sedang'  => 'bg-blue-50 text-blue-700 border-blue-200',
            'selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            default   => 'bg-slate-50 text-slate-600 border-slate-200',
        };
    }

    public function durasiMenit(): ?int
    {
        if (! $this->mulai_at || ! $this->selesai_at) return null;
        return (int) $this->mulai_at->diffInMinutes($this->selesai_at);
    }
}
