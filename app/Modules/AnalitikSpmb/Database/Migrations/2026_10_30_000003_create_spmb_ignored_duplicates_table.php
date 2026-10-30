<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spmb_ignored_duplicates', function (Blueprint $table) {
            $table->id();
            $table->string('school_a', 150);
            $table->string('school_b', 150);
            $table->string('pair_hash', 64)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spmb_ignored_duplicates');
    }
};
