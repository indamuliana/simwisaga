<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ota_kelompoks', function (Blueprint $table) {
            $table->foreignId('koordinator_donatur_id')->nullable()->after('nama_kelompok')->constrained('ota_donaturs')->nullOnDelete();
            $table->string('koordinator_nama')->nullable()->after('koordinator_donatur_id');
            $table->string('koordinator_telepon', 30)->nullable()->after('koordinator_nama');
        });

        Schema::table('ota_donasi_masuks', function (Blueprint $table) {
            $table->string('batch_kode')->nullable()->index()->after('nomor_transaksi');
            $table->string('penyetor_nama')->nullable()->after('batch_kode');
            $table->boolean('is_kolektif')->default(false)->after('penyetor_nama');
        });
    }

    public function down(): void
    {
        Schema::table('ota_donasi_masuks', function (Blueprint $table) {
            $table->dropColumn(['batch_kode', 'penyetor_nama', 'is_kolektif']);
        });

        Schema::table('ota_kelompoks', function (Blueprint $table) {
            $table->dropForeign(['koordinator_donatur_id']);
            $table->dropColumn(['koordinator_donatur_id', 'koordinator_nama', 'koordinator_telepon']);
        });
    }
};
