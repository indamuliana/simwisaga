<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kalender_liburs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajarans')->nullOnDelete();
            $table->string('nama');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->string('jenis', 30)->default('sekolah'); // nasional, cuti_bersama, sekolah
            $table->text('keterangan')->nullable();
            $table->boolean('is_kbm_libur')->default(true);
            $table->timestamps();

            $table->index(['tgl_mulai', 'tgl_selesai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kalender_liburs');
    }
};
