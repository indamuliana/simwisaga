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
        Schema::create('absensi_mapels', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('penugasan_mengajar_id')->constrained('penugasan_mengajars')->cascadeOnDelete();
            $table->string('status', 30);
            $table->text('keterangan')->nullable();
            $table->foreignId('diinput_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Hindari duplikasi
            $table->unique(['tanggal', 'siswa_id', 'penugasan_mengajar_id'], 'absensi_mapels_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_mapels');
    }
};
