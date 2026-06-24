<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestasi', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('nama_peraih');                                    // nama siswa / tim / institusi
            $table->enum('tingkat', ['Nasional', 'Provinsi', 'Kabupaten', 'Kecamatan', 'Desa', 'Lainnya'])->index();
            $table->string('medali')->default('🏅');                         // emoji medali
            $table->string('tahun', 10);                                     // teks: "2024" / "2024/2025"
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();                             // path ke storage/public/prestasi/
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi');
    }
};
