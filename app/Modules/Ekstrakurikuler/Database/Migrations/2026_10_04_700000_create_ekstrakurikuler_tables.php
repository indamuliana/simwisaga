<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ekstrakurikulers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('pembina_id')->constrained('pegawais')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('ekstrakurikuler_anggotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ekstrakurikuler_id')->constrained('ekstrakurikulers')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->integer('nilai_angka')->nullable();
            $table->string('nilai_huruf', 50)->nullable();
            $table->string('deskripsi', 255)->nullable();
            $table->timestamps();
            
            $table->unique(['ekstrakurikuler_id', 'siswa_id', 'semester_id'], 'ekskul_anggota_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekstrakurikuler_anggotas');
        Schema::dropIfExists('ekstrakurikulers');
    }
};
