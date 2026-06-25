<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JawabanUjian extends Model
{
    protected $table = 'jawaban_ujian';

    protected $fillable = [
        'jadwal_ujian_id',
        'soal_id',
        'murid_id',
        'jawaban_pg',
        'jawaban_essay',
        'nilai_essay',
    ];

    protected $casts = [
        'nilai_essay' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function jadwalUjian(): BelongsTo
    {
        return $this->belongsTo(JadwalUjian::class, 'jadwal_ujian_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(SoalUjian::class, 'soal_id');
    }

    public function murid(): BelongsTo
    {
        return $this->belongsTo(User::class, 'murid_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Apakah jawaban PG benar?
     */
    public function isBenar(): bool
    {
        if (! $this->soal || $this->soal->tipe !== 'pilihan_ganda') {
            return false;
        }

        return strtoupper($this->jawaban_pg ?? '') === strtoupper($this->soal->kunci_jawaban ?? '');
    }
}
