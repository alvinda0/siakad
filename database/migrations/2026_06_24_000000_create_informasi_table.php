<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informasi', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['beasiswa', 'promo'])->index(); // beasiswa | promo program strategis
            $table->string('jenis');                               // nama jenis beasiswa / program
            $table->text('syarat')->nullable();                    // syarat
            $table->text('benefit')->nullable();                   // benefit
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);                 // urutan tampil
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informasi');
    }
};
