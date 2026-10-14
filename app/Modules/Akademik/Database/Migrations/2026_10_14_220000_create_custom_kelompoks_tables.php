<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_kelompoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajarans')->nullOnDelete();
            $table->string('kode', 30)->nullable();
            $table->string('nama', 150);
            $table->string('kategori', 50)->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('pembimbing_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('warna', 20)->default('#3b82f6');
            $table->unsignedSmallInteger('kapasitas')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('custom_kelompok_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_kelompok_id')->constrained('custom_kelompoks')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();

            $table->unique(['custom_kelompok_id', 'siswa_id'], 'uk_kelompok_siswa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_kelompok_siswas');
        Schema::dropIfExists('custom_kelompoks');
    }
};
