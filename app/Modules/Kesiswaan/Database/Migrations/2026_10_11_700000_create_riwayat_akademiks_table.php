<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_akademiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('rombel_asal_id')->nullable()->constrained('rombels')->nullOnDelete();
            $table->foreignId('rombel_tujuan_id')->nullable()->constrained('rombels')->nullOnDelete();
            $table->string('tingkat_asal', 10)->nullable(); // X, XI, XII
            $table->string('tingkat_tujuan', 10)->nullable(); // X, XI, XII, LULUS
            $table->enum('status_keputusan', [
                'naik_kelas',
                'tinggal_kelas',
                'lulus',
                'tidak_lulus',
                'pindah',
                'dikeluarkan'
            ]);
            $table->text('catatan')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_akademiks');
    }
};
