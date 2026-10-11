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
        if (!Schema::hasColumn('tujuan_pembelajarans', 'alokasi_ah')) {
            Schema::table('tujuan_pembelajarans', function (Blueprint $table) {
                $table->string('alokasi_ah', 20)->nullable()->after('deskripsi');
            });
        }

        if (!Schema::hasTable('nilai_asesmen_harians')) {
            Schema::create('nilai_asesmen_harians', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
                $table->foreignId('penugasan_mengajar_id')->constrained('penugasan_mengajars')->cascadeOnDelete();
                $table->string('kode_ah', 20); // 'AH 1', 'AH 2', ..., 'AH 8'
                $table->integer('nilai')->nullable();
                $table->timestamps();

                $table->unique(['siswa_id', 'penugasan_mengajar_id', 'kode_ah'], 'unique_siswa_pm_ah');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_asesmen_harians');

        if (Schema::hasColumn('tujuan_pembelajarans', 'alokasi_ah')) {
            Schema::table('tujuan_pembelajarans', function (Blueprint $table) {
                $table->dropColumn('alokasi_ah');
            });
        }
    }
};
