<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumnis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->unique()->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->string('tahun_lulus', 10);
            $table->string('nomor_ijazah', 100)->nullable();
            $table->enum('status_karir', [
                'bekerja',
                'kuliah',
                'wirausaha',
                'mencari_kerja',
                'belum_memutuskan'
            ])->default('belum_memutuskan');
            $table->string('nama_instansi', 150)->nullable();
            $table->string('posisi_jurusan', 150)->nullable();
            $table->string('pendapatan_range', 50)->nullable();
            $table->text('alamat_terkini')->nullable();
            $table->string('no_hp_terkini', 30)->nullable();
            $table->string('email_terkini', 100)->nullable();
            $table->string('linkedin_url', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnis');
    }
};
