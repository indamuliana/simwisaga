<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Jurusan (Kompetensi Keahlian)
        Schema::create('jurusans', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique(); // PPLG, TJKT, PMN, HTL
            $table->string('nama', 150);
            $table->json('kode_alias')->nullable(); // ["RPL"], ["TKJ"]
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Rayon (Pengelompokan Siswa Wilayah Tempat Tinggal)
        Schema::create('rayons', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50)->unique(); // Bales 1, Tarkid 1, Tarkid 2
            $table->string('wilayah_cakupan', 255)->nullable(); // Banyuresmi, Leles
            $table->foreignId('pembimbing_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 3. Rombel (Rombongan Belajar) - Format [Tingkat] [Jurusan] [Nomor]
        Schema::create('rombels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('jurusan_id')->constrained('jurusans')->cascadeOnDelete();
            $table->enum('tingkat', ['X', 'XI', 'XII']);
            $table->unsignedTinyInteger('nomor_urut')->default(1);
            $table->string('nama', 50); // Contoh: "X PPLG 1", "XI TJKT 2"
            $table->foreignId('wali_kelas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('kapasitas')->default(36);
            $table->timestamps();

            $table->unique(['tahun_ajaran_id', 'jurusan_id', 'tingkat', 'nomor_urut']);
        });

        // 4. Mata Pelajaran (4 Kelompok Kurikulum Merdeka)
        Schema::create('mapels', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama', 150);
            $table->enum('kelompok', ['umum', 'kejuruan', 'muatan_lokal', 'p5_pkl']);
            $table->unsignedTinyInteger('kkm_default')->default(75);
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Struktur Kurikulum (Alokasi Mapel & JP per Jurusan & Tingkat)
        Schema::create('struktur_kurikulums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('jurusan_id')->constrained('jurusans')->cascadeOnDelete();
            $table->enum('tingkat', ['X', 'XI', 'XII']);
            $table->foreignId('mapel_id')->constrained('mapels')->cascadeOnDelete();
            $table->unsignedTinyInteger('jp_per_minggu')->default(2);
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->timestamps();

            $table->unique(['tahun_ajaran_id', 'jurusan_id', 'tingkat', 'mapel_id'], 'uk_struktur_kurikulum');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('struktur_kurikulums');
        Schema::dropIfExists('mapels');
        Schema::dropIfExists('rombels');
        Schema::dropIfExists('rayons');
        Schema::dropIfExists('jurusans');
    }
};
