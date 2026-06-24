<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fasilitas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');                          // Nama fasilitas
            $table->string('icon')->nullable();              // Emoji atau icon
            $table->text('deskripsi')->nullable();           // Deskripsi singkat
            $table->json('fitur')->nullable();               // Array fitur/keunggulan
            $table->string('gambar')->nullable();            // Path file gambar
            $table->string('kategori')->default('Umum');     // Akademik / Olahraga / Kesehatan / dll
            $table->boolean('aktif')->default(true);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasilitas');
    }
};
