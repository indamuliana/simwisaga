<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('periode_rapors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->string('semester', 20); // 'ganjil' atau 'genap'
            $table->string('kode', 50); // e.g. '9_minggu_1', '9_minggu_2'
            $table->string('nama', 100); // e.g. 'Rapor Tengah Semester Ganjil'
            $table->string('sebutan', 100); // e.g. '9 Minggu ke-1'
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->date('tanggal_bagi'); // Titi mangsa pembagian rapor
            $table->string('tempat', 100)->default('Garut');
            $table->boolean('is_active')->default(false);
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->timestamps();

            $table->index(['tahun_ajaran_id', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_rapors');
    }
};
