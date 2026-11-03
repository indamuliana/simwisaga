<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesantren_quran_surahs', function (Blueprint $table) {
            $table->unsignedInteger('nomor_surah')->primary();
            $table->string('nama_latin', 100);
            $table->string('nama_arab', 100)->nullable();
            $table->string('arti', 150)->nullable();
            $table->unsignedInteger('jumlah_ayat');
            $table->unsignedInteger('juz_start')->default(1);
            $table->unsignedInteger('juz_end')->default(1);
            $table->enum('tempat_turun', ['Makkiyyah', 'Madaniyyah'])->default('Makkiyyah');
            $table->timestamps();
        });

        Schema::create('pesantren_hafalan_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->unsignedInteger('surah_id');
            $table->unsignedInteger('ayat_number');
            $table->date('tanggal_hafal');
            $table->enum('status', ['lancar', 'murojaah'])->default('lancar');
            $table->foreignId('verified_by_id')->nullable()->constrained('pegawais')->nullOnDelete();
            $table->timestamps();

            $table->foreign('surah_id')->references('nomor_surah')->on('pesantren_quran_surahs')->cascadeOnDelete();
            $table->unique(['siswa_id', 'surah_id', 'ayat_number'], 'hafalan_siswa_ayat_unique');
            $table->index(['siswa_id', 'surah_id']);
        });

        Schema::create('pesantren_setoran_hafalans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawais')->nullOnDelete();
            $table->date('tanggal');
            $table->enum('jenis', ['ziyadah', 'murojaah'])->default('ziyadah');
            $table->unsignedInteger('surah_id');
            $table->unsignedInteger('ayat_awal');
            $table->unsignedInteger('ayat_akhir');
            $table->unsignedInteger('total_ayat');
            $table->enum('predikat', ['mumtaz', 'jayyid_jiddan', 'jayyid', 'maqbul'])->default('mumtaz');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('surah_id')->references('nomor_surah')->on('pesantren_quran_surahs')->cascadeOnDelete();
            $table->index(['siswa_id', 'tanggal']);
        });

        Schema::create('pesantren_settings', function (Blueprint $table) {
            $table->string('key', 100)->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesantren_settings');
        Schema::dropIfExists('pesantren_setoran_hafalans');
        Schema::dropIfExists('pesantren_hafalan_siswas');
        Schema::dropIfExists('pesantren_quran_surahs');
    }
};
