<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestasis', function (Blueprint $table) {
            $table->boolean('is_highlight')->default(false)->after('status_verifikasi')->index();
        });

        Schema::table('prestasi_gurus', function (Blueprint $table) {
            $table->boolean('is_highlight')->default(false)->after('deskripsi')->index();
        });

        Schema::table('prestasi_sekolahs', function (Blueprint $table) {
            $table->boolean('is_highlight')->default(false)->after('deskripsi')->index();
        });
    }

    public function down(): void
    {
        Schema::table('prestasis', function (Blueprint $table) {
            $table->dropIndex(['is_highlight']);
            $table->dropColumn('is_highlight');
        });

        Schema::table('prestasi_gurus', function (Blueprint $table) {
            $table->dropIndex(['is_highlight']);
            $table->dropColumn('is_highlight');
        });

        Schema::table('prestasi_sekolahs', function (Blueprint $table) {
            $table->dropIndex(['is_highlight']);
            $table->dropColumn('is_highlight');
        });
    }
};
