<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->string('pendidikan_terakhir', 255)->nullable()->change();
            $table->string('jurusan_dan_instansi', 255)->nullable()->change();
            $table->string('jabatan_struktural', 255)->nullable()->change();

            $table->string('niy', 30)->nullable()->unique()->after('user_id');
            $table->string('jenjang_pendidikan', 20)->nullable()->after('pendidikan_terakhir');
            $table->date('tgl_pertama_kontrak')->nullable()->after('no_sk_mulai_guru');
            $table->string('no_sk_kontrak_pertama', 100)->nullable()->after('tgl_pertama_kontrak');
            $table->date('habis_kontrak')->nullable()->after('no_sk_tetap_yayasan');
            $table->string('lama_kontrak_selanjutnya', 50)->nullable()->after('habis_kontrak');
            $table->text('keterangan_perpanjang')->nullable()->after('lama_kontrak_selanjutnya');
            $table->string('jam_kerja', 50)->nullable()->after('keterangan_perpanjang');
            $table->string('mapel_diampu', 255)->nullable()->after('jabatan_struktural');
        });
    }

    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->string('pendidikan_terakhir', 50)->nullable()->change();
            $table->string('jurusan_dan_instansi', 200)->nullable()->change();
            $table->string('jabatan_struktural', 100)->nullable()->change();

            $table->dropColumn([
                'niy',
                'jenjang_pendidikan',
                'tgl_pertama_kontrak',
                'no_sk_kontrak_pertama',
                'habis_kontrak',
                'lama_kontrak_selanjutnya',
                'keterangan_perpanjang',
                'jam_kerja',
                'mapel_diampu',
            ]);
        });
    }
};
