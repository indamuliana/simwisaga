<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_menus', function (Blueprint $table) {
            $table->id();
            $table->string('lokasi')->default('header')->index(); // header, footer
            $table->string('label');
            $table->string('url');
            $table->string('target', 20)->default('_self'); // _self, _blank
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('parent_id')->nullable()->constrained('web_menus')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('web_pages', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('ringkasan')->nullable();
            $table->longText('konten')->nullable();
            $table->string('gambar_banner')->nullable();
            $table->string('meta_deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_menus');
        Schema::dropIfExists('web_pages');
    }
};
