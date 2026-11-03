<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_beasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajarans')->nullOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->nullOnDelete();
            $table->enum('jenis_beasiswa', ['ota', 'prestasi', 'afirmasi_ekonomi', 'lainnya'])->default('ota');
            $table->decimal('nominal_pengajuan', 15, 2)->nullable();
            $table->text('alasan');
            $table->decimal('penghasilan_orang_tua', 15, 2)->nullable();
            $table->unsignedInteger('jumlah_tanggungan')->nullable();
            $table->string('lampiran_dokumen')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan_admin')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diproses_at')->nullable();
            $table->timestamps();

            $table->index(['siswa_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_beasiswas');
    }
};
