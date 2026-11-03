<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesantren_kamars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->string('nama_kamar', 100);
            $table->string('gedung', 100)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->unsignedInteger('kapasitas')->default(8);
            $table->foreignId('musrif_id')->nullable()->constrained('pegawais')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('pesantren_kamar_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kamar_id')->constrained('pesantren_kamars')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->enum('peran', ['mudabbir', 'anggota'])->default('anggota');
            $table->string('nomor_ranjang', 50)->nullable();
            $table->timestamps();

            $table->unique(['kamar_id', 'siswa_id'], 'pesantren_kamar_siswa_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesantren_kamar_siswas');
        Schema::dropIfExists('pesantren_kamars');
    }
};
