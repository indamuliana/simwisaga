<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesantren_hafalan_siswas', function (Blueprint $table) {
            $table->enum('predikat', ['mumtaz', 'jayyid_jiddan', 'jayyid', 'maqbul'])
                ->default('mumtaz')
                ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('pesantren_hafalan_siswas', function (Blueprint $table) {
            $table->dropColumn('predikat');
        });
    }
};
