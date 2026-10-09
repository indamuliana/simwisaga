<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('SIRAGA Assistant');
            $table->text('style')->nullable();
            $table->text('base_prompt')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default setting
        DB::table('ai_settings')->insert([
            'name' => 'SIRAGA Assistant',
            'style' => 'Formal, ramah, dan profesional. Gunakan bahasa Indonesia yang baik dan benar.',
            'base_prompt' => 'Kamu adalah AI Assistant untuk sistem informasi sekolah SIRAGA. Tugasmu adalah membantu pengguna menemukan informasi seputar siswa, absensi, dan nilai berdasarkan data yang ada di database.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_settings');
    }
};
