<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('negara', 100)->default('Indonesia')->nullable()->after('kecamatan')->index();
            $table->string('provinsi', 100)->nullable()->after('negara')->index();
            $table->string('kabupaten_kota', 100)->nullable()->after('provinsi')->index();
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['negara', 'provinsi', 'kabupaten_kota']);
        });
    }
};
