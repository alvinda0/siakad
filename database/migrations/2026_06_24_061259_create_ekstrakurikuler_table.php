<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            $table->string('nama');                                                               // nama ekskul
            $table->string('emoji')->default('⭐');                                             // emoji / ikon
            $table->enum('jenis', ['Wajib', 'Pilihan'])->default('Pilihan')->index();
            $table->unsignedSmallInteger('jumlah_anggota')->default(0);
            $table->string('pembina')->nullable();                                                // nama pembina
            $table->text('deskripsi')->nullable();
            $table->string('jadwal')->nullable();                                                 // teks: "Jumat, 14.00–16.00"
            $table->string('gambar')->nullable();                                                 // path ke storage
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekstrakurikuler');
    }
};
