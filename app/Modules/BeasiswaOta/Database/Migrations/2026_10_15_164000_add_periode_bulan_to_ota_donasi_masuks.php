<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ota_donasi_masuks', function (Blueprint $table) {
            $table->foreignId('tahun_ajaran_id')->nullable()->after('kelompok_id')->constrained('tahun_ajarans')->nullOnDelete();
            $table->json('periode_bulan')->nullable()->after('nominal');
            $table->unsignedSmallInteger('jumlah_bulan')->default(1)->after('periode_bulan');
            $table->string('keterangan_periode')->nullable()->after('jumlah_bulan');
        });
    }

    public function down(): void
    {
        Schema::table('ota_donasi_masuks', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->dropColumn(['tahun_ajaran_id', 'periode_bulan', 'jumlah_bulan', 'keterangan_periode']);
        });
    }
};
