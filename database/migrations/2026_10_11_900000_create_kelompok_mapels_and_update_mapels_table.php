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
        Schema::create('kelompok_mapels', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 150);
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('mapels', function (Blueprint $table) {
            $table->foreignId('kelompok_mapel_id')
                ->nullable()
                ->after('nama')
                ->constrained('kelompok_mapels')
                ->nullOnDelete();
        });

        // Seed 4 kelompok awal Kurikulum Merdeka & sinkronkan data mapel eksisting
        $now = now();
        $kelompokDefault = [
            'umum' => [
                'kode' => 'A',
                'nama' => 'Muatan Umum',
                'urutan' => 1,
            ],
            'kejuruan' => [
                'kode' => 'B',
                'nama' => 'Muatan Kejuruan',
                'urutan' => 2,
            ],
            'muatan_lokal' => [
                'kode' => 'C',
                'nama' => 'Muatan Lokal',
                'urutan' => 3,
            ],
            'p5_pkl' => [
                'kode' => 'D',
                'nama' => 'Projek Penguatan Profil Pelajar Pancasila (P5) & PKL',
                'urutan' => 4,
            ],
        ];

        $kelompokIds = [];
        foreach ($kelompokDefault as $legacyKey => $data) {
            $id = DB::table('kelompok_mapels')->insertGetId([
                'kode' => $data['kode'],
                'nama' => $data['nama'],
                'urutan' => $data['urutan'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $kelompokIds[$legacyKey] = $id;

            // Hubungkan data mapel eksisting
            DB::table('mapels')
                ->where('kelompok', $legacyKey)
                ->update(['kelompok_mapel_id' => $id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mapels', function (Blueprint $table) {
            $table->dropForeign(['kelompok_mapel_id']);
            $table->dropColumn('kelompok_mapel_id');
        });

        Schema::dropIfExists('kelompok_mapels');
    }
};
