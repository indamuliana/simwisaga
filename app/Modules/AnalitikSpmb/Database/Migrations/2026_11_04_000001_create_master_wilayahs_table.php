<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_wilayahs', function (Blueprint $table) {
            $table->string('kode', 13)->primary();
            $table->string('nama', 100);
            $table->unsignedTinyInteger('level')->comment('1: Provinsi, 2: Kab/Kota, 3: Kecamatan, 4: Desa/Kelurahan');
            $table->string('parent_kode', 13)->nullable()->index();

            $table->index(['level', 'parent_kode']);
            $table->index(['level', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_wilayahs');
    }
};
