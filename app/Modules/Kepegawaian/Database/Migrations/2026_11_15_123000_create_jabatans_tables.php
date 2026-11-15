<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jabatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('jabatans')->nullOnDelete();
            $table->string('nama', 150);
            $table->string('kode', 30)->nullable();
            $table->decimal('bobot_jam', 5, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['parent_id', 'urutan']);
        });

        Schema::create('jabatan_pejabat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jabatan_id')->constrained('jabatans')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawais')->nullOnDelete();
            $table->string('nama_pejabat', 150)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->index(['jabatan_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatan_pejabat');
        Schema::dropIfExists('jabatans');
    }
};
