<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catatan_wali_kelas', function (Blueprint $table) {
            if (!Schema::hasColumn('catatan_wali_kelas', 'periode_rapor_id')) {
                $table->foreignId('periode_rapor_id')
                    ->nullable()
                    ->after('semester_id')
                    ->constrained('periode_rapors')
                    ->nullOnDelete();
            }

            $table->index('siswa_id', 'catatan_wali_kelas_siswa_id_idx');
            $table->dropUnique('catatan_wali_kelas_siswa_id_semester_id_unique');
            $table->unique(['siswa_id', 'semester_id', 'periode_rapor_id'], 'catatan_wali_kelas_siswa_sem_periode_unique');
        });
    }

    public function down(): void
    {
        Schema::table('catatan_wali_kelas', function (Blueprint $table) {
            $table->dropUnique('catatan_wali_kelas_siswa_sem_periode_unique');
            $table->unique(['siswa_id', 'semester_id'], 'catatan_wali_kelas_siswa_id_semester_id_unique');
            $table->dropIndex('catatan_wali_kelas_siswa_id_idx');
            if (Schema::hasColumn('catatan_wali_kelas', 'periode_rapor_id')) {
                $table->dropConstrainedForeignId('periode_rapor_id');
            }
        });
    }
};
