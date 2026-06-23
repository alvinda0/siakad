<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');                         // e.g. "X TKJ 1"
            $table->string('tingkat');                      // X / XI / XII
            $table->string('jurusan');                      // TKJ / TO
            $table->unsignedSmallInteger('tahun_ajaran');   // e.g. 2026
            $table->unsignedTinyInteger('kapasitas')->default(32);
            $table->string('wali_kelas')->nullable();       // nama wali kelas (text sederhana)
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['nama', 'tahun_ajaran']);       // nama kelas unik per tahun ajaran
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
