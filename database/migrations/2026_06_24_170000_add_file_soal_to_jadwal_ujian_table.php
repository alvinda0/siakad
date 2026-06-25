<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_ujian', function (Blueprint $table) {
            $table->string('file_soal')->nullable()->after('keterangan');  // path file soal ujian
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_ujian', function (Blueprint $table) {
            $table->dropColumn('file_soal');
        });
    }
};
