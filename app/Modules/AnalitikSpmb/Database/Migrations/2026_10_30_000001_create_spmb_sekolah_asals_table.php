<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spmb_sekolah_asals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_resmi', 150)->unique();
            $table->string('npsn', 20)->nullable()->index();
            $table->string('jenis', 20)->default('SMP'); // SMP, MTs, Pesantren, Lainnya
            $table->string('status_sekolah', 20)->default('Negeri'); // Negeri, Swasta
            $table->string('kecamatan', 100)->nullable();
            $table->string('kabupaten_kota', 100)->nullable()->default('Garut');
            $table->string('tier', 20)->default('Rintisan'); // Utama, Potensial, Rintisan
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spmb_sekolah_asals');
    }
};
