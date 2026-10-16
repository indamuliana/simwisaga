<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestasi_sekolahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();

            $table->enum('kategori', [
                'akreditasi',
                'kejuaraan_institusi',
                'sertifikasi_iso_lsp',
                'penghargaan_khusus',
                'lainnya'
            ])->default('penghargaan_khusus');

            $table->string('penyelenggara_nama'); // e.g. BAN-SM, Kemendikbudristek, Kemenkes, Pemprov
            $table->string('nama_prestasi'); // e.g. "Akreditasi A BAN-SM", "Juara 1 Sekolah Sehat Nasional"
            $table->string('bidang')->nullable();
            $table->enum('tingkat', ['kabupaten_kota', 'provinsi', 'nasional', 'internasional'])->default('nasional');
            $table->string('capaian'); // e.g. "Predikat A Unggul (Nilai 96)", "Juara 1"

            $table->date('tanggal_perolehan');
            $table->date('masa_berlaku_sampai')->nullable(); // Masa berlaku akreditasi/sertifikasi

            $table->string('nomor_sk')->nullable();
            $table->string('sertifikat_file_path')->nullable(); // Berkas SK / Sertifikat PDF
            $table->string('foto_file_path')->nullable(); // Foto piala / plakat / penyerahan
            $table->text('deskripsi')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('tahun_ajaran_id');
            $table->index('kategori');
            $table->index('tingkat');
            $table->index('tanggal_perolehan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi_sekolahs');
    }
};
