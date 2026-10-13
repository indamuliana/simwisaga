<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Master Cabang Seni Budaya
        Schema::create('seni_budayas', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->string('nama', 150);
            $table->foreignId('guru_id')->nullable()->constrained('pegawais')->nullOnDelete();
            $table->string('instruktur_nama', 150)->nullable();
            $table->unsignedTinyInteger('kkm')->default(75);
            $table->unsignedTinyInteger('bobot_presensi')->default(30);
            $table->unsignedTinyInteger('bobot_penilaian')->default(70);
            $table->string('hari_latihan', 50)->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('tempat', 100)->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tujuan Pembelajaran (TP) per Cabang Seni Budaya & Semester
        Schema::create('seni_budaya_tps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seni_budaya_id')->constrained('seni_budayas')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->string('kode', 20); // TP 1, TP 2
            $table->text('deskripsi');
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Keanggotaan Siswa pada Cabang Seni Budaya (1 Siswa = 1 Cabang per Semester)
        Schema::create('seni_budaya_anggotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seni_budaya_id')->constrained('seni_budayas')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            
            // Komponen Asesmen Sumatif (0 - 100)
            $table->decimal('sts_1', 5, 2)->nullable();
            $table->decimal('sas', 5, 2)->nullable();
            $table->decimal('sts_2', 5, 2)->nullable();
            $table->decimal('sat', 5, 2)->nullable();
            
            // Kehadiran
            $table->decimal('nilai_presensi', 5, 2)->default(100.00);
            $table->unsignedSmallInteger('total_hadir')->default(0);
            $table->unsignedSmallInteger('total_pertemuan')->default(0);
            $table->decimal('persentase_kehadiran', 5, 2)->default(0.00);
            
            // Hasil Akhir Semester
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->string('nilai_huruf', 20)->nullable(); // A, B, C, D
            
            // Kurikulum Merdeka TP & Deskripsi Capaian Rapor
            $table->json('tp_tercapai_ids')->nullable();
            $table->json('tp_perlu_bimbingan_ids')->nullable();
            $table->text('deskripsi_tertinggi')->nullable();
            $table->text('deskripsi_terendah')->nullable();
            $table->text('deskripsi_manual')->nullable();
            
            $table->string('status_keanggotaan', 50)->default('aktif');
            $table->timestamps();

            // Proteksi: 1 siswa hanya boleh terdaftar di 1 cabang seni budaya dalam 1 semester
            $table->unique(['siswa_id', 'semester_id'], 'uq_sb_siswa_semester');
        });

        // 4. Sesi Presensi Pertemuan Seni Budaya
        Schema::create('seni_budaya_presensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seni_budaya_id')->constrained('seni_budayas')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->unsignedSmallInteger('pertemuan_ke');
            $table->date('tanggal');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('materi', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['seni_budaya_id', 'semester_id', 'pertemuan_ke'], 'idx_sb_sem_pertemuan');
        });

        // 5. Detail Presensi Siswa per Sesi Pertemuan
        Schema::create('seni_budaya_presensi_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seni_budaya_presensi_id')->constrained('seni_budaya_presensis')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->enum('status', ['Hadir', 'Tidak Hadir', 'Izin', 'Sakit'])->default('Hadir');
            $table->string('catatan', 255)->nullable();
            $table->timestamps();

            $table->unique(['seni_budaya_presensi_id', 'siswa_id'], 'uq_sb_presensi_siswa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seni_budaya_presensi_details');
        Schema::dropIfExists('seni_budaya_presensis');
        Schema::dropIfExists('seni_budaya_anggotas');
        Schema::dropIfExists('seni_budaya_tps');
        Schema::dropIfExists('seni_budayas');
    }
};
