<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->string('nama', 150);
            $table->string('deskripsi', 255)->nullable();
            $table->enum('orientasi', ['portrait', 'landscape'])->default('portrait');
            $table->string('ukuran_kertas', 20)->default('A4');
            $table->string('titimangsa_default', 100)->default('Garut, {tanggal}');
            $table->boolean('tampilkan_kop')->default(true);
            $table->boolean('tampilkan_nomor_halaman')->default(true);
            $table->timestamps();
        });

        Schema::create('document_signers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_template_id')->constrained('document_templates')->cascadeOnDelete();
            $table->unsignedTinyInteger('kolom_ke')->default(1); // 1, 2, atau 3 kolom
            $table->string('judul_jabatan', 100);
            $table->enum('sumber', [
                'kepala_sekolah',
                'wali_kelas',
                'pembimbing_rayon',
                'orang_tua',
                'tata_usaha',
                'kurikulum',
                'kesiswaan',
                'custom',
            ])->default('custom');
            $table->string('nama_pejabat', 150)->nullable();
            $table->string('nip_pejabat', 50)->nullable();
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_signers');
        Schema::dropIfExists('document_templates');
    }
};
