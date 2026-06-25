<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel soal ujian (pilihan ganda & essay)
        Schema::create('soal_ujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ujian_id')
                  ->constrained('jadwal_ujian')
                  ->cascadeOnDelete();
            $table->enum('tipe', ['pilihan_ganda', 'essay'])->default('pilihan_ganda');
            $table->unsignedSmallInteger('nomor');           // urutan soal
            $table->text('pertanyaan');
            // Pilihan ganda — null jika essay
            $table->text('pilihan_a')->nullable();
            $table->text('pilihan_b')->nullable();
            $table->text('pilihan_c')->nullable();
            $table->text('pilihan_d')->nullable();
            $table->char('kunci_jawaban', 1)->nullable();    // A/B/C/D untuk PG
            // Bobot poin
            $table->unsignedSmallInteger('poin')->default(1);
            $table->timestamps();

            $table->unique(['jadwal_ujian_id', 'nomor']);
        });

        // Tabel jawaban murid
        Schema::create('jawaban_ujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ujian_id')
                  ->constrained('jadwal_ujian')
                  ->cascadeOnDelete();
            $table->foreignId('soal_id')
                  ->constrained('soal_ujian')
                  ->cascadeOnDelete();
            $table->foreignId('murid_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            // Jawaban
            $table->char('jawaban_pg', 1)->nullable();       // A/B/C/D
            $table->text('jawaban_essay')->nullable();
            // Penilaian (diisi guru untuk essay)
            $table->unsignedSmallInteger('nilai_essay')->nullable();
            $table->timestamps();

            $table->unique(['jadwal_ujian_id', 'soal_id', 'murid_id']);
        });

        // Tabel sesi pengerjaan — satu baris per murid per ujian
        Schema::create('sesi_ujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ujian_id')
                  ->constrained('jadwal_ujian')
                  ->cascadeOnDelete();
            $table->foreignId('murid_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->enum('status', ['belum', 'sedang', 'selesai'])->default('belum');
            $table->timestamp('mulai_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
            // Nilai final (dihitung setelah submit)
            $table->decimal('nilai_pg', 5, 2)->nullable();
            $table->decimal('nilai_essay', 5, 2)->nullable();
            $table->decimal('nilai_total', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['jadwal_ujian_id', 'murid_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_ujian');
        Schema::dropIfExists('jawaban_ujian');
        Schema::dropIfExists('soal_ujian');
    }
};
