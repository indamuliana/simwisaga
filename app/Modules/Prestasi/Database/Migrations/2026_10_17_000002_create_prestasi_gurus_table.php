<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Utama Prestasi Guru & Tendik
        Schema::create('prestasi_gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();

            // Tipe Peserta: Individu atau Tim GTK
            $table->enum('tipe_peserta', ['individu', 'kelompok'])->default('individu');
            $table->string('nama_tim')->nullable(); // Misal: "Tim Inovasi Pendidik Wikrama"

            $table->enum('kategori', [
                'karya_ilmiah',
                'kejuaraan_gtk',
                'sertifikasi_keahlian',
                'penghargaan',
                'lainnya'
            ])->default('kejuaraan_gtk');

            $table->enum('penyelenggara_lingkup', ['dalam_sekolah', 'luar_sekolah'])->default('luar_sekolah');
            $table->string('penyelenggara_nama');
            $table->string('nama_prestasi');
            $table->string('bidang')->nullable();
            $table->enum('tingkat', ['sekolah', 'kecamatan', 'kabupaten_kota', 'provinsi', 'nasional', 'internasional'])->default('kabupaten_kota');
            $table->string('peringkat'); // Peringkat atau capaian (e.g. Juara 1, Best Teacher, Asesor BNSP, dll.)

            $table->date('tanggal_perolehan');
            $table->string('tempat')->nullable();

            $table->string('nomor_sertifikat')->nullable();
            $table->string('sertifikat_file_path')->nullable(); // PDF / scan
            $table->string('foto_file_path')->nullable(); // Foto dokumentasi
            $table->text('deskripsi')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('tahun_ajaran_id');
            $table->index('kategori');
            $table->index('tingkat');
            $table->index('tanggal_perolehan');
        });

        // 2. Tabel Pivot Anggota Guru (Mendukung 1 Prestasi diraih oleh tim guru/karyawan)
        Schema::create('prestasi_guru_anggotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestasi_guru_id')->constrained('prestasi_gurus')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->enum('peran', ['individu', 'ketua', 'anggota'])->default('individu');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['prestasi_guru_id', 'pegawai_id']);
            $table->index('pegawai_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi_guru_anggotas');
        Schema::dropIfExists('prestasi_gurus');
    }
};
