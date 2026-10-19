<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kalender_agendas') && ! Schema::hasColumn('kalender_agendas', 'koordinator')) {
            Schema::table('kalender_agendas', function (Blueprint $table) {
                $table->string('koordinator')->nullable()->after('lokasi');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('kalender_agendas') && Schema::hasColumn('kalender_agendas', 'koordinator')) {
            Schema::table('kalender_agendas', function (Blueprint $table) {
                $table->dropColumn('koordinator');
            });
        }
    }
};
