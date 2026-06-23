<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nilai extends Model
{
    protected $table = 'nilai';

    protected $fillable = [
        'murid_id',
        'mata_pelajaran_id',
        'kelas_id',
        'semester',
        'tahun_ajaran',
        'nilai_tugas',
        'nilai_uts',
        'nilai_uas',
        'nilai_akhir',
        'predikat',
        'catatan',
    ];

    protected $casts = [
        'nilai_tugas'  => 'decimal:2',
        'nilai_uts'    => 'decimal:2',
        'nilai_uas'    => 'decimal:2',
        'nilai_akhir'  => 'decimal:2',
        'tahun_ajaran' => 'integer',
    ];

    // ── Options ──────────────────────────────────────────────────────────────

    public static array $semester = [
        '1' => 'Semester 1 (Ganjil)',
        '2' => 'Semester 2 (Genap)',
    ];

    public static array $predikat = [
        'A'  => 'A  (Sangat Baik)',
        'B'  => 'B  (Baik)',
        'C'  => 'C  (Cukup)',
        'D'  => 'D  (Kurang)',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function murid(): BelongsTo
    {
        return $this->belongsTo(User::class, 'murid_id');
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Hitung nilai akhir otomatis: 20% tugas + 30% UTS + 50% UAS
     */
    public function hitungNilaiAkhir(): float|null
    {
        if ($this->nilai_tugas === null && $this->nilai_uts === null && $this->nilai_uas === null) {
            return null;
        }

        $tugas = (float) ($this->nilai_tugas ?? 0);
        $uts   = (float) ($this->nilai_uts   ?? 0);
        $uas   = (float) ($this->nilai_uas   ?? 0);

        return round($tugas * 0.20 + $uts * 0.30 + $uas * 0.50, 2);
    }

    /**
     * Tentukan predikat berdasarkan nilai akhir.
     */
    public static function predikatDari(float|null $nilai): string|null
    {
        if ($nilai === null) return null;
        return match (true) {
            $nilai >= 90 => 'A',
            $nilai >= 75 => 'B',
            $nilai >= 60 => 'C',
            default      => 'D',
        };
    }

    public function predikatColorClass(): string
    {
        return match ($this->predikat) {
            'A'     => 'bg-emerald-50 text-emerald-700',
            'B'     => 'bg-teal-50 text-teal-700',
            'C'     => 'bg-amber-50 text-amber-700',
            'D'     => 'bg-red-50 text-red-700',
            default => 'bg-slate-50 text-slate-500',
        };
    }
}
