<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ota_kas_mutasis', function (Blueprint $table) {
            $table->index(['pos_kas', 'kelompok_id', 'tipe'], 'ota_km_pos_kel_tipe_idx');
            $table->index(['referensi_tipe', 'referensi_id'], 'ota_km_ref_idx');
            $table->index('tanggal', 'ota_km_tanggal_idx');
        });

        Schema::table('ota_donasi_masuks', function (Blueprint $table) {
            $table->index(['donatur_id', 'tahun_ajaran_id', 'kelompok_id'], 'ota_dm_donatur_ta_kel_idx');
            $table->index('tanggal_donasi', 'ota_dm_tgl_donasi_idx');
        });

        Schema::table('ota_alokasis', function (Blueprint $table) {
            $table->index('tanggal_alokasi', 'ota_alokasi_tgl_idx');
            $table->index(['siswa_id', 'semester_id'], 'ota_alokasi_siswa_sem_idx');
        });
    }

    public function down(): void
    {
        Schema::table('ota_alokasis', function (Blueprint $table) {
            $table->dropIndex('ota_alokasi_siswa_sem_idx');
            $table->dropIndex('ota_alokasi_tgl_idx');
        });

        Schema::table('ota_donasi_masuks', function (Blueprint $table) {
            $table->dropIndex('ota_dm_donatur_ta_kel_idx');
            $table->dropIndex('ota_dm_tgl_donasi_idx');
        });

        Schema::table('ota_kas_mutasis', function (Blueprint $table) {
            $table->dropIndex('ota_km_pos_kel_tipe_idx');
            $table->dropIndex('ota_km_ref_idx');
            $table->dropIndex('ota_km_tanggal_idx');
        });
    }
};
