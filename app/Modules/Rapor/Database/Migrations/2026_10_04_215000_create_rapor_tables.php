<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catatan_wali_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->unique(['siswa_id', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_wali_kelas');
    }
};
