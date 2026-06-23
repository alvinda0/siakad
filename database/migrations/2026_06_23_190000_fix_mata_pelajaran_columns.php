<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menyesuaikan kolom tabel mata_pelajaran dengan struktur terbaru:
 * - Hapus kolom lama: kelompok, jam_per_minggu
 * - Tambah kolom baru: guru_id (FK ke users)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            // Hapus kolom yang tidak lagi digunakan
            if (Schema::hasColumn('mata_pelajaran', 'kelompok')) {
                $table->dropColumn('kelompok');
            }
            if (Schema::hasColumn('mata_pelajaran', 'jam_per_minggu')) {
                $table->dropColumn('jam_per_minggu');
            }

            // Tambah kolom guru_id jika belum ada
            if (! Schema::hasColumn('mata_pelajaran', 'guru_id')) {
                $table->foreignId('guru_id')
                      ->nullable()
                      ->after('nama')
                      ->constrained('users')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            if (Schema::hasColumn('mata_pelajaran', 'guru_id')) {
                $table->dropForeign(['guru_id']);
                $table->dropColumn('guru_id');
            }

            if (! Schema::hasColumn('mata_pelajaran', 'kelompok')) {
                $table->string('kelompok')->nullable()->after('nama');
            }
            if (! Schema::hasColumn('mata_pelajaran', 'jam_per_minggu')) {
                $table->unsignedTinyInteger('jam_per_minggu')->nullable()->after('kelompok');
            }
        });
    }
};
