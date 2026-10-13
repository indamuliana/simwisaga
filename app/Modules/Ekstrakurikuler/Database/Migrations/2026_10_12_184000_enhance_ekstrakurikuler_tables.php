<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modifikasi tabel ekstrakurikulers
        Schema::table('ekstrakurikulers', function (Blueprint $table) {
            $table->foreignId('pembina_id')->nullable()->change();
            $table->foreignId('instruktur_user_id')->nullable()->after('pembina_id')->constrained('users')->nullOnDelete();
            $table->string('instruktur_nama', 150)->nullable()->after('instruktur_user_id');
            $table->boolean('is_active')->default(true)->after('instruktur_nama');
            $table->boolean('is_wajib')->default(false)->after('is_active');
            $table->string('hari_latihan', 50)->nullable()->after('is_wajib');
            $table->time('jam_mulai')->nullable()->after('hari_latihan');
            $table->time('jam_selesai')->nullable()->after('jam_mulai');
            $table->string('tempat', 100)->nullable()->after('jam_selesai');
            $table->integer('bobot_presensi')->default(30)->after('tempat');
            $table->integer('bobot_penilaian')->default(70)->after('bobot_presensi');
            $table->integer('threshold_a')->default(90)->after('bobot_penilaian');
            $table->integer('threshold_b')->default(80)->after('threshold_a');
            $table->integer('threshold_c')->default(70)->after('threshold_b');
            $table->text('deskripsi')->nullable()->after('threshold_c');
        });

        // 2. Modifikasi tabel ekstrakurikuler_anggotas
        Schema::table('ekstrakurikuler_anggotas', function (Blueprint $table) {
            $table->decimal('sts_1', 5, 2)->nullable()->after('semester_id');
            $table->decimal('sas', 5, 2)->nullable()->after('sts_1');
            $table->decimal('sts_2', 5, 2)->nullable()->after('sas');
            $table->decimal('sat', 5, 2)->nullable()->after('sts_2');
            $table->decimal('nilai_presensi', 5, 2)->nullable()->after('sat');
            $table->integer('total_hadir')->default(0)->after('nilai_presensi');
            $table->integer('total_pertemuan')->default(0)->after('total_hadir');
            $table->decimal('persentase_kehadiran', 5, 2)->default(0.00)->after('total_pertemuan');
            $table->string('status_keanggotaan', 50)->default('aktif')->after('persentase_kehadiran');
            $table->string('catatan_pindah', 255)->nullable()->after('status_keanggotaan');
        });

        // 3. Buat tabel sesi presensi mingguan ekskul
        Schema::create('ekskul_presensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ekstrakurikuler_id')->constrained('ekstrakurikulers')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->integer('pertemuan_ke');
            $table->date('tanggal');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('materi', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['ekstrakurikuler_id', 'semester_id', 'pertemuan_ke'], 'idx_ekskul_sem_pertemuan');
        });

        // 4. Buat tabel detail presensi per siswa
        Schema::create('ekskul_presensi_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ekskul_presensi_id')->constrained('ekskul_presensis')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->enum('status', ['Hadir', 'Tidak Hadir', 'Izin', 'Sakit'])->default('Hadir');
            $table->string('catatan', 255)->nullable();
            $table->timestamps();

            $table->unique(['ekskul_presensi_id', 'siswa_id'], 'uq_ekskul_presensi_siswa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekskul_presensi_details');
        Schema::dropIfExists('ekskul_presensis');

        Schema::table('ekstrakurikuler_anggotas', function (Blueprint $table) {
            $table->dropColumn([
                'sts_1', 'sas', 'sts_2', 'sat', 'nilai_presensi',
                'total_hadir', 'total_pertemuan', 'persentase_kehadiran',
                'status_keanggotaan', 'catatan_pindah'
            ]);
        });

        Schema::table('ekstrakurikulers', function (Blueprint $table) {
            $table->dropForeign(['instruktur_user_id']);
            $table->dropColumn([
                'instruktur_user_id', 'instruktur_nama', 'is_active', 'is_wajib',
                'hari_latihan', 'jam_mulai', 'jam_selesai', 'tempat',
                'bobot_presensi', 'bobot_penilaian', 'threshold_a', 'threshold_b',
                'threshold_c', 'deskripsi'
            ]);
            $table->foreignId('pembina_id')->nullable(false)->change();
        });
    }
};
