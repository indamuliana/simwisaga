<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kalender_agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('kalender_agendas')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajarans')->nullOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->nullOnDelete();
            $table->foreignId('kategori_id')->constrained('kalender_kategoris')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->boolean('is_all_day')->default(true);
            
            $table->string('warna', 30)->nullable();
            $table->string('lokasi')->nullable();
            $table->string('audiens', 50)->default('semua'); // semua, guru, kelas, ortu
            $table->string('status', 30)->default('internal'); // draft, internal, publik
            
            $table->boolean('is_final')->default(false);
            $table->dateTime('finalized_at')->nullable();
            
            $table->string('recurrence_type', 30)->default('none'); // none, daily, weekly, monthly
            $table->json('recurrence_days')->nullable();
            $table->date('recurrence_until')->nullable();

            $table->timestamps();

            $table->index(['tgl_mulai', 'tgl_selesai']);
            $table->index('status');
            $table->index('is_final');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kalender_agendas');
    }
};
