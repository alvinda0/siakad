<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kandidat_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // ── Step 0: Pendaftar ──────────────────────────────────────────
            $table->string('didaftarkan_oleh');          // Diri Sendiri / Ayah / Ibu / Saudara / Guru

            // ── Step 1: Pendidikan ────────────────────────────────────────
            $table->string('jurusan');                   // TKJ / Teknik Otomotif
            $table->string('sistem_pendidikan');         // Reguler / Pondok / Panti

            // ── Step 2: Data Diri ─────────────────────────────────────────
            $table->string('nik')->nullable();
            $table->string('nisn')->nullable();
            $table->string('nama_lengkap');
            $table->string('nama_panggilan')->nullable();
            $table->string('kewarganegaraan')->default('Indonesia');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('agama')->nullable();
            $table->string('status_keluarga')->nullable();       // Kandung / Angkat / Tiri
            $table->string('status_dalam_keluarga')->nullable(); // Anak ke
            $table->unsignedTinyInteger('anak_ke')->nullable();
            $table->unsignedTinyInteger('dari_saudara')->nullable();
            $table->unsignedTinyInteger('total_saudara_kandung')->nullable();
            $table->unsignedTinyInteger('total_saudara_tiri')->nullable();
            $table->unsignedTinyInteger('total_saudara_angkat')->nullable();
            $table->string('asal_sekolah')->nullable();
            $table->unsignedTinyInteger('lama_belajar')->nullable(); // tahun
            $table->string('nomor_ijazah')->nullable();
            $table->date('tanggal_ijazah')->nullable();
            $table->string('npsn')->nullable();
            $table->boolean('penerima_kip')->default(false);
            $table->string('nomor_kip')->nullable();
            $table->string('status_tinggal')->nullable();        // Bersama orang tua / Kost / dll
            $table->string('bahasa_sehari_hari')->nullable();
            $table->string('saudara_di_sekolah')->nullable();
            $table->string('moda_transportasi')->nullable();
            $table->decimal('jarak_sekolah_km', 6, 2)->nullable();
            $table->decimal('waktu_tempuh_jam', 4, 2)->nullable();
            $table->string('foto')->nullable();                  // path file

            // Kontak
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();

            // Alamat
            $table->string('provinsi')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('desa')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->text('alamat_lengkap')->nullable();

            // ── Step 3: Kesehatan ─────────────────────────────────────────
            $table->text('riwayat_kesehatan')->nullable();
            $table->string('disabilitas')->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable();
            $table->decimal('berat_badan', 5, 2)->nullable();

            // ── Step 4: Dokumen ───────────────────────────────────────────
            $table->string('dokumen_kk')->nullable();            // path file Kartu Keluarga
            $table->string('dokumen_ijazah')->nullable();        // path file Ijazah/SKL

            // ── Step 5: Informasi Tambahan ────────────────────────────────
            // Prestasi disimpan sebagai JSON array [{nama, dokumen}]
            $table->json('prestasi')->nullable();

            // ── Step 6: Data Orang Tua ────────────────────────────────────
            // Ayah
            $table->string('nama_ayah')->nullable();
            $table->string('nik_ayah')->nullable();
            $table->string('pendidikan_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('status_pernikahan_ayah')->nullable();
            $table->string('no_hp_ayah')->nullable();
            $table->string('tempat_lahir_ayah')->nullable();
            $table->date('tanggal_lahir_ayah')->nullable();
            $table->string('kewarganegaraan_ayah')->nullable();
            $table->string('agama_ayah')->nullable();

            // Ibu
            $table->string('nama_ibu')->nullable();
            $table->string('nik_ibu')->nullable();
            $table->string('pendidikan_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('status_pernikahan_ibu')->nullable();
            $table->string('no_hp_ibu')->nullable();
            $table->string('tempat_lahir_ibu')->nullable();
            $table->date('tanggal_lahir_ibu')->nullable();
            $table->string('kewarganegaraan_ibu')->nullable();
            $table->string('agama_ibu')->nullable();

            // Penghasilan
            $table->string('penghasilan_ortu')->nullable();

            // ── Status ────────────────────────────────────────────────────
            $table->enum('status', ['draft', 'submitted', 'diterima', 'ditolak'])->default('draft');
            $table->integer('current_step')->default(0); // untuk resume form

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kandidat_profiles');
    }
};
