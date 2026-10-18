<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_categories', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('slug', 100)->unique();
            $table->string('warna_badge', 30)->default('blue');
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('web_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->nullable()->constrained('web_categories')->nullOnDelete();
            $table->foreignId('penulis_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('penulis_nama', 100)->nullable();
            $table->string('judul', 255);
            $table->string('slug', 255)->unique();
            $table->text('ringkasan')->nullable();
            $table->longText('konten');
            $table->string('gambar_sampul', 255)->nullable();
            $table->string('status', 20)->default('published')->index();
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_articles');
        Schema::dropIfExists('web_categories');
    }
};
