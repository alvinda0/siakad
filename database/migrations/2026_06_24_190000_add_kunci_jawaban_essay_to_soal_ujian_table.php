<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soal_ujian', function (Blueprint $table) {
            // Kunci jawaban untuk soal essay — digunakan untuk auto-grading berbasis kemiripan teks
            $table->text('kunci_jawaban_essay')->nullable()->after('kunci_jawaban');
        });
    }

    public function down(): void
    {
        Schema::table('soal_ujian', function (Blueprint $table) {
            $table->dropColumn('kunci_jawaban_essay');
        });
    }
};
