<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('nilai_akhirs', function (Blueprint $table) {
            $table->json('data_9m')->nullable()->after('deskripsi_manual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_akhirs', function (Blueprint $table) {
            $table->dropColumn('data_9m');
        });
    }
};
