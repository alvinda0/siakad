<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();           // e.g. "MTK-X", "B.IND"
            $table->string('nama');                          // e.g. "Matematika"
            $table->foreignId('guru_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('jurusan', ['TKJ', 'TO', 'Semua'])->default('Semua');
            $table->enum('tingkat', ['X', 'XI', 'XII', 'Semua'])->default('Semua');
            $table->text('deskripsi')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_pelajaran');
    }
};
