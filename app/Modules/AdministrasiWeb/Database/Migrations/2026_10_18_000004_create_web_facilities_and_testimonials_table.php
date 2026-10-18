<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_facilities', function (Blueprint $table) {
            $table->id();
            $table->string('category')->default('lab')->index(); // lab, kelas, gedung, aktivitas, umum
            $table->string('cat_label')->nullable(); // Lab Komputer & Coding, dll
            $table->string('badge')->nullable(); // AI & Software Studio, dll
            $table->string('title');
            $table->text('desc')->nullable();
            $table->string('highlight')->nullable(); // Spesifikasi Core i7, dll
            $table->string('image')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('web_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('category')->default('Alumni')->index(); // Siswa Aktif, Orang Tua, Alumni, Mitra
            $table->string('question')->nullable();
            $table->text('quote');
            $table->string('name');
            $table->string('role')->nullable();
            $table->string('image')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_facilities');
        Schema::dropIfExists('web_testimonials');
    }
};
