<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sarpras_sumber_danas', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 4)->unique(); // Kode persis 4 karakter alfanumerik
            $table->string('nama', 100);
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tambah foreign key sumber_dana_id ke sarpras_asets
        Schema::table('sarpras_asets', function (Blueprint $table) {
            $table->foreignId('sumber_dana_id')->nullable()->after('ruangan_id')->constrained('sarpras_sumber_danas')->nullOnDelete();
        });

        // Seed data awal sumber dana
        $now = now();
        $defaults = [
            ['kode' => 'BS26', 'nama' => 'BOSP 2026', 'keterangan' => 'Bantuan Operasional Satuan Pendidikan Tahun 2026', 'created_at' => $now, 'updated_at' => $now],
            ['kode' => 'BS25', 'nama' => 'BOSP 2025', 'keterangan' => 'Bantuan Operasional Satuan Pendidikan Tahun 2025', 'created_at' => $now, 'updated_at' => $now],
            ['kode' => 'YYSN', 'nama' => 'Yayasan', 'keterangan' => 'Anggaran Mandiri Yayasan', 'created_at' => $now, 'updated_at' => $now],
            ['kode' => 'KMTE', 'nama' => 'Komite Sekolah', 'keterangan' => 'Dana Sumbangan / Partisipasi Komite', 'created_at' => $now, 'updated_at' => $now],
            ['kode' => 'HIBA', 'nama' => 'Hibah / Bantuan', 'keterangan' => 'Bantuan Pemerintah / DAK / Hibah Industri', 'created_at' => $now, 'updated_at' => $now],
            ['kode' => 'LAIN', 'nama' => 'Lain-lain', 'keterangan' => 'Sumber Pendanaan Lainnya', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('sarpras_sumber_danas')->insert($defaults);

        // Migrasikan aset eksisting ke sumber dana terkait
        $sumberMap = DB::table('sarpras_sumber_danas')->pluck('id', 'kode');

        DB::table('sarpras_asets')->where('sumber_dana', 'like', '%BOS Kinerja%')->update(['sumber_dana_id' => $sumberMap['BS26'] ?? null]);
        DB::table('sarpras_asets')->where('sumber_dana', 'like', '%BOS Reguler%')->update(['sumber_dana_id' => $sumberMap['BS25'] ?? null]);
        DB::table('sarpras_asets')->where('sumber_dana', 'like', '%Bantuan%')->update(['sumber_dana_id' => $sumberMap['HIBA'] ?? null]);
        DB::table('sarpras_asets')->where('sumber_dana', 'like', '%Yayasan%')->update(['sumber_dana_id' => $sumberMap['YYSN'] ?? null]);
        DB::table('sarpras_asets')->where('sumber_dana', 'like', '%Komite%')->update(['sumber_dana_id' => $sumberMap['KMTE'] ?? null]);
        DB::table('sarpras_asets')->whereNull('sumber_dana_id')->update(['sumber_dana_id' => $sumberMap['BS26'] ?? null]);
    }

    public function down(): void
    {
        Schema::table('sarpras_asets', function (Blueprint $table) {
            $table->dropForeign(['sumber_dana_id']);
            $table->dropColumn('sumber_dana_id');
        });

        Schema::dropIfExists('sarpras_sumber_danas');
    }
};
