<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu baris saja (satu sekolah), tetapi disimpan di tabel agar bisa diubah lewat UI.
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('npsn', 20)->nullable();
            $table->string('nss', 20)->nullable();
            $table->string('nama');
            $table->string('nama_yayasan')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('telepon', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            // Sementara teks; akan dihubungkan ke data Pegawai di Sprint S4.
            $table->string('kepala_sekolah_nama')->nullable();
            $table->string('kepala_sekolah_nip', 30)->nullable();
            $table->string('logo_sekolah_path')->nullable();
            $table->string('kop_surat_path')->nullable();
            $table->string('nama_aplikasi')->default('SIRAGA');
            $table->string('kepanjangan_aplikasi')->default('Sistem Informasi Wikrama Garut');
            $table->string('logo_aplikasi_path')->nullable();
            $table->timestamps();
        });

        // Batas data per role (diterapkan mulai modul data siswa/absensi/nilai).
        Schema::create('role_scopes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->unique()->constrained('roles')->cascadeOnDelete();
            $table->string('scope', 30)->default('semua');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_scopes');
        Schema::dropIfExists('schools');
    }
};
