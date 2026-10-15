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
        // 1. Pengaturan OTA
        Schema::create('ota_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // 2. Donatur (Rutin & Insidental)
        Schema::create('ota_donaturs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('tipe', ['rutin', 'insidental'])->default('rutin');
            $table->string('nomor_telepon')->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 3. Kelompok Donatur
        Schema::create('ota_kelompoks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kelompok')->unique();
            $table->string('nama_kelompok');
            $table->decimal('target_nominal', 15, 2)->default(2000000.00);
            $table->boolean('is_aktif')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 4. Anggota Kelompok Donatur (Pivot dengan iuran komitmen berbeda tiap donatur)
        Schema::create('ota_kelompok_anggotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_id')->constrained('ota_kelompoks')->cascadeOnDelete();
            $table->foreignId('donatur_id')->constrained('ota_donaturs')->cascadeOnDelete();
            $table->decimal('nominal_komitmen', 15, 2)->default(0.00);
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['kelompok_id', 'donatur_id']);
        });

        // 5. Siswa Penerima Beasiswa OTA (1 Kelompok membiayai 1 Siswa)
        Schema::create('ota_siswa_penerimas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('kelompok_id')->nullable()->constrained('ota_kelompoks')->nullOnDelete();
            $table->enum('status', ['aktif', 'selesai', 'dibatalkan'])->default('aktif');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 6. Komponen Pembiayaan Siswa (SPP Bulanan, Asrama Bulanan, Cicilan DSP, dll.)
        Schema::create('ota_komponen_biayas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_penerima_id')->constrained('ota_siswa_penerimas')->cascadeOnDelete();
            $table->string('jenis_komponen'); // 'spp', 'asrama', 'dsp', 'lainnya'
            $table->string('nama_komponen');
            $table->decimal('nominal_per_bulan', 15, 2)->nullable();
            $table->decimal('total_target_semester', 15, 2)->default(0.00);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        // 7. Transaksi Uang Donasi Masuk
        Schema::create('ota_donasi_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi')->unique();
            $table->foreignId('donatur_id')->constrained('ota_donaturs')->cascadeOnDelete();
            $table->foreignId('kelompok_id')->nullable()->constrained('ota_kelompoks')->nullOnDelete();
            $table->string('tipe_donatur'); // rutin / insidental
            $table->date('tanggal_donasi');
            $table->decimal('nominal', 15, 2);
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 8. Transaksi Penyaluran Alokasi Beasiswa (6 Bulanan / Insidental)
        Schema::create('ota_alokasis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kwitansi')->unique();
            $table->string('tipe_alokasi'); // 'rutin_semester', 'insidental'
            $table->foreignId('kelompok_id')->nullable()->constrained('ota_kelompoks')->nullOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->nullOnDelete();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajarans')->nullOnDelete();
            $table->date('tanggal_alokasi');
            $table->decimal('total_nominal', 15, 2);
            $table->decimal('surplus_dialihkan', 15, 2)->default(0.00);
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 9. Rincian Penyaluran Alokasi (Bulan SPP/Asrama & Termin DSP)
        Schema::create('ota_alokasi_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alokasi_id')->constrained('ota_alokasis')->cascadeOnDelete();
            $table->foreignId('komponen_biaya_id')->nullable()->constrained('ota_komponen_biayas')->nullOnDelete();
            $table->string('jenis_komponen'); // 'spp', 'asrama', 'dsp', 'lainnya'
            $table->string('nama_komponen');
            $table->string('bulan')->nullable(); // misal: 'Juli', 'Agustus', dst.
            $table->integer('tahun')->nullable();
            $table->decimal('nominal', 15, 2);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        // 10. Buku Kas & Riwayat Mutasi OTA
        Schema::create('ota_kas_mutasis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->enum('pos_kas', ['kelompok', 'insidental']);
            $table->foreignId('kelompok_id')->nullable()->constrained('ota_kelompoks')->nullOnDelete();
            $table->string('referensi_tipe'); // 'donasi', 'alokasi', 'surplus_transfer', 'koreksi'
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->decimal('nominal', 15, 2);
            $table->decimal('saldo_pos', 15, 2)->default(0.00);
            $table->text('deskripsi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ota_kas_mutasis');
        Schema::dropIfExists('ota_alokasi_details');
        Schema::dropIfExists('ota_alokasis');
        Schema::dropIfExists('ota_donasi_masuks');
        Schema::dropIfExists('ota_komponen_biayas');
        Schema::dropIfExists('ota_siswa_penerimas');
        Schema::dropIfExists('ota_kelompok_anggotas');
        Schema::dropIfExists('ota_kelompoks');
        Schema::dropIfExists('ota_donaturs');
        Schema::dropIfExists('ota_settings');
    }
};
