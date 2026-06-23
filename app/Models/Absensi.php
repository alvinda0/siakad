<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'jadwal_id',
        'murid_id',
        'tanggal',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // ── Options ──────────────────────────────────────────────────────────────

    public static array $status = [
        'Hadir' => 'Hadir',
        'Sakit' => 'Sakit',
        'Izin'  => 'Izin',
        'Alpha' => 'Alpha',
    ];

    public static array $statusColor = [
        'Hadir' => 'emerald',
        'Sakit' => 'blue',
        'Izin'  => 'amber',
        'Alpha' => 'red',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function murid(): BelongsTo
    {
        return $this->belongsTo(User::class, 'murid_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function colorClass(): string
    {
        return match ($this->status) {
            'Hadir' => 'bg-emerald-50 text-emerald-700',
            'Sakit' => 'bg-blue-50 text-blue-700',
            'Izin'  => 'bg-amber-50 text-amber-700',
            'Alpha' => 'bg-red-50 text-red-700',
            default => 'bg-slate-50 text-slate-700',
        };
    }
}
