<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesantren_halaqohs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->string('nama_halaqoh', 100);
            $table->foreignId('pengajar_id')->nullable()->constrained('pegawais')->nullOnDelete();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('pesantren_halaqoh_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('halaqoh_id')->constrained('pesantren_halaqohs')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['halaqoh_id', 'siswa_id'], 'pesantren_halaqoh_siswa_unique');
        });

        Schema::create('pesantren_halaqoh_presensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('halaqoh_id')->constrained('pesantren_halaqohs')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('waktu_sesi', 50)->default('subuh'); // e.g. subuh, ashar, maghrib, isya, custom
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['halaqoh_id', 'tanggal', 'waktu_sesi'], 'halaqoh_presensi_unique');
        });

        Schema::create('pesantren_halaqoh_presensi_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presensi_id')->constrained('pesantren_halaqoh_presensis')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->enum('status', ['H', 'S', 'I', 'A'])->default('H');
            $table->string('catatan', 255)->nullable();
            $table->timestamps();

            $table->unique(['presensi_id', 'siswa_id'], 'presensi_detail_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesantren_halaqoh_presensi_details');
        Schema::dropIfExists('pesantren_halaqoh_presensis');
        Schema::dropIfExists('pesantren_halaqoh_siswas');
        Schema::dropIfExists('pesantren_halaqohs');
    }
};
