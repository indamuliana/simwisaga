<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spmb_master_smps', function (Blueprint $table) {
            $table->id();
            $table->string('npsn', 20)->unique();
            $table->string('nama', 150)->index();
            $table->string('bentuk', 50)->nullable()->index(); // SMP, MTS, dll
            $table->string('status', 20)->nullable()->index(); // NEGERI, SWASTA
            $table->string('jenjang', 50)->nullable();
            $table->string('provinsi', 100)->index();
            $table->string('kabupaten', 100)->index();
            $table->string('kecamatan', 100)->index();
            $table->string('kelurahan', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spmb_master_smps');
    }
};
