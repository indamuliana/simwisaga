<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ekstrakurikuler_anggotas', function (Blueprint $table) {
            if (!Schema::hasColumn('ekstrakurikuler_anggotas', 'evaluations')) {
                $table->json('evaluations')->nullable()->after('deskripsi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ekstrakurikuler_anggotas', function (Blueprint $table) {
            if (Schema::hasColumn('ekstrakurikuler_anggotas', 'evaluations')) {
                $table->dropColumn('evaluations');
            }
        });
    }
};
