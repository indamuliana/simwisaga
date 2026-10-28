<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Bersihkan rekaman duplikat historis pada nilai_akhirs (simpan ID terbaru)
        if (Schema::hasTable('nilai_akhirs')) {
            $duplicateAkhirIds = DB::table('nilai_akhirs as n1')
                ->join('nilai_akhirs as n2', function ($join) {
                    $join->on('n1.siswa_id', '=', 'n2.siswa_id')
                        ->on('n1.penugasan_mengajar_id', '=', 'n2.penugasan_mengajar_id')
                        ->whereColumn('n1.id', '<', 'n2.id');
                })
                ->pluck('n1.id')
                ->all();

            if (!empty($duplicateAkhirIds)) {
                DB::table('nilai_akhirs')->whereIn('id', $duplicateAkhirIds)->delete();
            }

            Schema::table('nilai_akhirs', function (Blueprint $table) {
                $table->unique(['siswa_id', 'penugasan_mengajar_id'], 'uk_nilai_akhirs_siswa_pm');
            });
        }

        // 2. Bersihkan rekaman duplikat historis pada nilai_sumatifs (simpan ID terbaru)
        if (Schema::hasTable('nilai_sumatifs')) {
            $duplicateSumatifIds = DB::table('nilai_sumatifs as n1')
                ->join('nilai_sumatifs as n2', function ($join) {
                    $join->on('n1.siswa_id', '=', 'n2.siswa_id')
                        ->on('n1.penugasan_mengajar_id', '=', 'n2.penugasan_mengajar_id')
                        ->whereColumn('n1.id', '<', 'n2.id');
                })
                ->pluck('n1.id')
                ->all();

            if (!empty($duplicateSumatifIds)) {
                DB::table('nilai_sumatifs')->whereIn('id', $duplicateSumatifIds)->delete();
            }

            Schema::table('nilai_sumatifs', function (Blueprint $table) {
                $table->unique(['siswa_id', 'penugasan_mengajar_id'], 'uk_nilai_sumatifs_siswa_pm');
            });
        }

        // 3. Bersihkan rekaman duplikat historis pada nilai_formatifs (simpan ID terbaru)
        if (Schema::hasTable('nilai_formatifs')) {
            $duplicateFormatifIds = DB::table('nilai_formatifs as n1')
                ->join('nilai_formatifs as n2', function ($join) {
                    $join->on('n1.siswa_id', '=', 'n2.siswa_id')
                        ->on('n1.tujuan_pembelajaran_id', '=', 'n2.tujuan_pembelajaran_id')
                        ->whereColumn('n1.id', '<', 'n2.id');
                })
                ->pluck('n1.id')
                ->all();

            if (!empty($duplicateFormatifIds)) {
                DB::table('nilai_formatifs')->whereIn('id', $duplicateFormatifIds)->delete();
            }

            Schema::table('nilai_formatifs', function (Blueprint $table) {
                $table->unique(['siswa_id', 'tujuan_pembelajaran_id'], 'uk_nilai_formatifs_siswa_tp');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('nilai_akhirs')) {
            Schema::table('nilai_akhirs', function (Blueprint $table) {
                $table->dropUnique('uk_nilai_akhirs_siswa_pm');
            });
        }

        if (Schema::hasTable('nilai_sumatifs')) {
            Schema::table('nilai_sumatifs', function (Blueprint $table) {
                $table->dropUnique('uk_nilai_sumatifs_siswa_pm');
            });
        }

        if (Schema::hasTable('nilai_formatifs')) {
            Schema::table('nilai_formatifs', function (Blueprint $table) {
                $table->dropUnique('uk_nilai_formatifs_siswa_tp');
            });
        }
    }
};
