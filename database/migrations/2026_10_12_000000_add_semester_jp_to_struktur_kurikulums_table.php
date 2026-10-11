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
        Schema::table('struktur_kurikulums', function (Blueprint $table) {
            $table->unsignedTinyInteger('jp_ganjil')->default(0)->after('jp_per_minggu');
            $table->unsignedTinyInteger('jp_genap')->default(0)->after('jp_ganjil');
        });

        // Sinkronkan data eksisting: jadikan jp_ganjil & jp_genap sama dengan jp_per_minggu (status sepanjang tahun)
        DB::table('struktur_kurikulums')->update([
            'jp_ganjil' => DB::raw('jp_per_minggu'),
            'jp_genap' => DB::raw('jp_per_minggu'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('struktur_kurikulums', function (Blueprint $table) {
            $table->dropColumn(['jp_ganjil', 'jp_genap']);
        });
    }
};
