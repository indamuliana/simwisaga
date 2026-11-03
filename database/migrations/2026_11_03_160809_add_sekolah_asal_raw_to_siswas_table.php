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
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('sekolah_asal_raw', 150)->nullable()->after('sekolah_asal');
        });

        // 1. Inisialisasi sekolah_asal_raw dengan nilai sekolah_asal yang ada saat ini
        DB::table('siswas')
            ->whereNull('sekolah_asal_raw')
            ->update([
                'sekolah_asal_raw' => DB::raw('sekolah_asal'),
            ]);

        // 2. Sinkronkan nilai sekolah_asal pada data siswa yang sudah terpetakan di master analitik
        $aliases = DB::table('spmb_sekolah_asal_aliases')
            ->join('spmb_sekolah_asals', 'spmb_sekolah_asal_aliases.sekolah_asal_id', '=', 'spmb_sekolah_asals.id')
            ->select('spmb_sekolah_asal_aliases.alias_raw', 'spmb_sekolah_asals.nama_resmi')
            ->get();

        foreach ($aliases as $alias) {
            if ($alias->alias_raw !== $alias->nama_resmi) {
                DB::table('siswas')
                    ->where('sekolah_asal_raw', $alias->alias_raw)
                    ->update(['sekolah_asal' => $alias->nama_resmi]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert sekolah_asal ke sekolah_asal_raw jika ada
        DB::table('siswas')
            ->whereNotNull('sekolah_asal_raw')
            ->update([
                'sekolah_asal' => DB::raw('sekolah_asal_raw'),
            ]);

        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn('sekolah_asal_raw');
        });
    }
};
