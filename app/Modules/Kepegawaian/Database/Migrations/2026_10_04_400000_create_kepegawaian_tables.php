<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nip', 30)->nullable()->unique();
            $table->string('nuptk', 30)->nullable()->unique();
            $table->string('nik', 20)->nullable()->unique();
            $table->string('nama_lengkap', 150);
            $table->string('gelar_depan', 30)->nullable();
            $table->string('gelar_belakang', 30)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->enum('jenis_gtk', ['guru', 'tendik'])->default('guru');
            $table->enum('status_kepegawaian', ['pns', 'pppk', 'gty', 'gtt', 'honor'])->default('gty');
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('no_hp', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->string('foto_path', 255)->nullable();
            $table->string('ttd_path', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('penugasan_mengajars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->foreignId('mapel_id')->constrained('mapels')->cascadeOnDelete();
            $table->foreignId('rombel_id')->constrained('rombels')->cascadeOnDelete();
            $table->unsignedTinyInteger('jp')->default(2);
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();

            $table->unique(['semester_id', 'pegawai_id', 'mapel_id', 'rombel_id'], 'penugasan_mengajar_unique');
        });

        Schema::create('penugasan_wali_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('rombel_id')->constrained('rombels')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->string('sk_penugasan', 100)->nullable();
            $table->timestamps();

            $table->unique(['tahun_ajaran_id', 'rombel_id'], 'wali_kelas_rombel_unique');
        });

        Schema::create('penugasan_pembimbing_rayons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('rayon_id')->constrained('rayons')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->string('sk_penugasan', 100)->nullable();
            $table->timestamps();

            $table->unique(['tahun_ajaran_id', 'rayon_id', 'pegawai_id'], 'pembimbing_rayon_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan_pembimbing_rayons');
        Schema::dropIfExists('penugasan_wali_kelas');
        Schema::dropIfExists('penugasan_mengajars');
        Schema::dropIfExists('pegawais');
    }
};
