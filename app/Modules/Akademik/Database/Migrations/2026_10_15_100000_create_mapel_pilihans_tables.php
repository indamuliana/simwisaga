<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Master Mapel Pilihan (Mapil)
        Schema::create('mapel_pilihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajarans')->nullOnDelete();
            $table->string('kode', 30)->unique();
            $table->string('nama', 150);
            $table->json('tingkats')->nullable(); // e.g. ["XI", "XII"]
            $table->unsignedSmallInteger('kkm_default')->default(75);
            $table->unsignedSmallInteger('jp')->default(2);
            $table->foreignId('guru_id')->nullable()->constrained('pegawais')->nullOnDelete();
            $table->foreignId('guru_pendamping_id')->nullable()->constrained('pegawais')->nullOnDelete();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Anggota Siswa Mapel Pilihan
        Schema::create('mapel_pilihan_anggotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapel_pilihan_id')->constrained('mapel_pilihans')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajarans')->nullOnDelete();
            $table->string('status', 20)->default('aktif'); // 'aktif', 'nonaktif'
            $table->timestamps();

            $table->unique(['mapel_pilihan_id', 'siswa_id'], 'uk_mpa_mapil_siswa');
        });

        // 3. Tujuan Pembelajaran (TP) Mapel Pilihan
        Schema::create('mapel_pilihan_tps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapel_pilihan_id')->constrained('mapel_pilihans')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->string('kode_tp', 20); // e.g. "TP 1", "TP 2"
            $table->string('lingkup_materi', 255)->nullable();
            $table->text('deskripsi_tp');
            $table->string('kode_ah', 10)->nullable(); // e.g. "AH 1", "AH 2"
            $table->string('target_periode', 10)->default('9M1'); // 9M1, 9M2, 9M3, 9M4
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->timestamps();
        });

        // 4. Nilai Formatif (AH 1 s.d. AH 8) Mapel Pilihan
        Schema::create('nilai_mapil_formatifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapel_pilihan_id')->constrained('mapel_pilihans')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->string('kode_ah', 10); // "AH 1".."AH 8"
            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['mapel_pilihan_id', 'siswa_id', 'semester_id', 'kode_ah'], 'uk_nmf_siswa_ah');
        });

        // 5. Nilai Sumatif (STS 1, SAS, STS 2, SAT) Mapel Pilihan
        Schema::create('nilai_mapil_sumatifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapel_pilihan_id')->constrained('mapel_pilihans')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->decimal('sts_1', 5, 2)->nullable();
            $table->decimal('sas', 5, 2)->nullable();
            $table->decimal('sts_2', 5, 2)->nullable();
            $table->decimal('sat', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['mapel_pilihan_id', 'siswa_id', 'semester_id'], 'uk_nms_siswa_sem');
        });

        // 6. Nilai Akhir & Rekap 9 Minggu / Semester Mapel Pilihan
        Schema::create('nilai_mapil_akhirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapel_pilihan_id')->constrained('mapel_pilihans')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->json('data_9m')->nullable();
            $table->text('deskripsi_tertinggi')->nullable();
            $table->text('deskripsi_terendah')->nullable();
            $table->text('deskripsi_manual')->nullable();
            $table->string('status', 30)->default('belum_lengkap'); // 'kompeten', 'belum_kompeten', 'belum_lengkap'
            $table->timestamps();

            $table->unique(['mapel_pilihan_id', 'siswa_id', 'semester_id'], 'uk_nma_siswa_sem');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_mapil_akhirs');
        Schema::dropIfExists('nilai_mapil_sumatifs');
        Schema::dropIfExists('nilai_mapil_formatifs');
        Schema::dropIfExists('mapel_pilihan_tps');
        Schema::dropIfExists('mapel_pilihan_anggotas');
        Schema::dropIfExists('mapel_pilihans');
    }
};
