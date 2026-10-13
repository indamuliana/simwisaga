<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah target_asesmen pada tabel seni_budaya_tps
        Schema::table('seni_budaya_tps', function (Blueprint $table) {
            $table->string('target_asesmen', 50)->default('semua')->after('deskripsi');
        });

        // 2. Tambah nilai akhir per periode & evaluasi TP per periode pada seni_budaya_anggotas
        Schema::table('seni_budaya_anggotas', function (Blueprint $table) {
            $table->decimal('nilai_akhir_sts_1', 5, 2)->nullable()->after('sts_1');
            $table->decimal('nilai_akhir_sas', 5, 2)->nullable()->after('sas');
            $table->decimal('nilai_akhir_sts_2', 5, 2)->nullable()->after('sts_2');
            $table->decimal('nilai_akhir_sat', 5, 2)->nullable()->after('sat');

            $table->json('tp_evaluations')->nullable()->after('tp_perlu_bimbingan_ids');
            $table->json('deskripsi_evaluations')->nullable()->after('deskripsi_manual');
        });
    }

    public function down(): void
    {
        Schema::table('seni_budaya_anggotas', function (Blueprint $table) {
            $table->dropColumn([
                'nilai_akhir_sts_1',
                'nilai_akhir_sas',
                'nilai_akhir_sts_2',
                'nilai_akhir_sat',
                'tp_evaluations',
                'deskripsi_evaluations',
            ]);
        });

        Schema::table('seni_budaya_tps', function (Blueprint $table) {
            $table->dropColumn(['target_asesmen']);
        });
    }
};
