<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Utama Prestasi Siswa
        Schema::create('prestasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->foreignId('periode_rapor_id')->nullable()->constrained('periode_rapors')->nullOnDelete();

            // Tipe Peserta: Individu atau Beregu/Kelompok
            $table->enum('tipe_peserta', ['individu', 'kelompok'])->default('individu');
            $table->string('nama_tim')->nullable(); // Misal: "Tim Robotik Wikrama", "Regu Cerdas Cermat"

            // Klasifikasi Prestasi
            $table->enum('kategori', ['akademik', 'non_akademik', 'sertifikasi'])->default('non_akademik');
            $table->enum('penyelenggara_lingkup', ['dalam_sekolah', 'luar_sekolah'])->default('luar_sekolah');
            $table->string('penyelenggara_nama');
            $table->string('nama_prestasi');
            $table->string('bidang')->nullable();
            $table->enum('tingkat', ['sekolah', 'kecamatan', 'kabupaten_kota', 'provinsi', 'nasional', 'internasional'])->default('kabupaten_kota');
            $table->string('peringkat'); // Template (Juara 1, 2, dll.) atau input custom

            // Waktu & Tempat
            $table->date('tanggal_perolehan');
            $table->date('tanggal_selesai')->nullable();
            $table->string('tempat')->nullable();

            // Guru Pembimbing / Pendamping (Bisa dari Pegawai atau Tulis Manual)
            $table->foreignId('pembimbing_pegawai_id')->nullable()->constrained('pegawais')->nullOnDelete();
            $table->string('pembimbing_nama_manual')->nullable();

            // Bukti & Dokumen (Upload Berkas PDF & Upload Foto Dokumentasi)
            $table->string('nomor_sertifikat')->nullable();
            $table->string('sertifikat_file_path')->nullable(); // Berkas PDF / Scan dokumen
            $table->string('foto_file_path')->nullable(); // Foto dokumentasi / penyerahan piala
            $table->text('deskripsi')->nullable();

            // Status Verifikasi (Pengajuan Mandiri vs Langsung Disetujui)
            $table->enum('status_verifikasi', ['pending', 'approved', 'rejected'])->default('approved');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('verified_at')->nullable();
            $table->text('catatan_verifikasi')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Index performa query
            $table->index(['tahun_ajaran_id', 'semester_id']);
            $table->index('periode_rapor_id');
            $table->index('status_verifikasi');
            $table->index('tanggal_perolehan');
            $table->index('kategori');
            $table->index('tingkat');
        });

        // 2. Tabel Pivot Anggota Siswa (Mendukung 1 Prestasi diraih oleh banyak siswa)
        Schema::create('prestasi_siswa_anggotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestasi_id')->constrained('prestasis')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->enum('peran', ['individu', 'ketua', 'anggota'])->default('individu');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['prestasi_id', 'siswa_id']);
            $table->index('siswa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi_siswa_anggotas');
        Schema::dropIfExists('prestasis');
    }
};
