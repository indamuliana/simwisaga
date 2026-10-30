<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spmb_sekolah_asal_aliases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_asal_id')->constrained('spmb_sekolah_asals')->cascadeOnDelete();
            $table->string('alias_raw', 150)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spmb_sekolah_asal_aliases');
    }
};
