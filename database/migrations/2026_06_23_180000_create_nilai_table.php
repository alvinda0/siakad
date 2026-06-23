<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('murid_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->enum('semester', ['1', '2'])->default('1');
            $table->unsignedSmallInteger('tahun_ajaran');      // e.g. 2026
            $table->decimal('nilai_tugas',   5, 2)->nullable();
            $table->decimal('nilai_uts',     5, 2)->nullable();
            $table->decimal('nilai_uas',     5, 2)->nullable();
            $table->decimal('nilai_akhir',   5, 2)->nullable(); // computed or manual
            $table->string('predikat', 5)->nullable();          // A / B / C / D
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['murid_id', 'mata_pelajaran_id', 'kelas_id', 'semester', 'tahun_ajaran'], 'unique_nilai');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai');
    }
};
