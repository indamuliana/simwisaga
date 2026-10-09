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
        Schema::create('tujuan_pembelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penugasan_mengajar_id')->constrained('penugasan_mengajars')->cascadeOnDelete();
            $table->string('kode', 20);
            $table->string('deskripsi', 255);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('nilai_formatifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('tujuan_pembelajaran_id')->constrained('tujuan_pembelajarans')->cascadeOnDelete();
            $table->integer('nilai');
            $table->timestamps();
        });

        Schema::create('nilai_sumatifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('penugasan_mengajar_id')->constrained('penugasan_mengajars')->cascadeOnDelete();
            $table->integer('sts')->nullable();
            $table->integer('sas')->nullable();
            $table->timestamps();
        });

        Schema::create('nilai_akhirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('penugasan_mengajar_id')->constrained('penugasan_mengajars')->cascadeOnDelete();
            $table->decimal('rata_formatif', 5, 2)->nullable();
            $table->integer('sts')->nullable();
            $table->integer('sas')->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->text('deskripsi_tertinggi')->nullable();
            $table->text('deskripsi_terendah')->nullable();
            $table->text('deskripsi_manual')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_akhirs');
        Schema::dropIfExists('nilai_sumatifs');
        Schema::dropIfExists('nilai_formatifs');
        Schema::dropIfExists('tujuan_pembelajarans');
    }
};
