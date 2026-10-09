<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('rombel_id')->nullable()->constrained('rombels')->nullOnDelete();
            $table->foreignId('rayon_id')->nullable()->constrained('rayons')->nullOnDelete();

            // Identitas Pribadi (DAPODIK)
            $table->string('nama', 150);
            $table->string('nipd', 30)->nullable(); // Nomor Induk Siswa (NIS)
            $table->enum('jk', ['L', 'P'])->default('L');
            $table->string('nisn', 20)->unique();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('nik', 20)->nullable();
            $table->string('agama', 30)->nullable();

            // Alamat Domisili
            $table->text('alamat')->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('dusun', 100)->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('jenis_tinggal', 50)->nullable();
            $table->string('alat_transportasi', 50)->nullable();
            $table->string('lintang', 30)->nullable();
            $table->string('bujur', 30)->nullable();
            $table->decimal('jarak_ke_sekolah_km', 6, 2)->nullable();

            // Kontak
            $table->string('telepon', 30)->nullable();
            $table->string('hp', 30)->nullable();
            $table->string('email', 100)->nullable();

            // Dokumen & Akademik Asal
            $table->string('skhun', 50)->nullable();
            $table->string('no_peserta_un', 50)->nullable();
            $table->string('no_seri_ijazah', 50)->nullable();
            $table->string('no_akta_lahir', 100)->nullable();
            $table->string('no_kk', 30)->nullable();
            $table->string('sekolah_asal', 150)->nullable();

            // Kesejahteraan & Bantuan
            $table->boolean('penerima_kps')->default(false);
            $table->string('no_kps', 50)->nullable();
            $table->boolean('penerima_kip')->default(false);
            $table->string('nomor_kip', 50)->nullable();
            $table->string('nama_di_kip', 150)->nullable();
            $table->string('nomor_kks', 50)->nullable();
            $table->boolean('layak_pip')->default(false);
            $table->string('alasan_layak_pip', 150)->nullable();
            $table->string('bank', 50)->nullable();
            $table->string('nomor_rekening_bank', 50)->nullable();
            $table->string('rekening_atas_nama', 150)->nullable();

            // Data Fisik
            $table->string('kebutuhan_khusus', 100)->nullable();
            $table->unsignedSmallInteger('anak_ke_berapa')->nullable();
            $table->unsignedSmallInteger('berat_badan')->nullable();
            $table->unsignedSmallInteger('tinggi_badan')->nullable();
            $table->unsignedSmallInteger('lingkar_kepala')->nullable();
            $table->unsignedSmallInteger('jml_saudara_kandung')->nullable();

            // Status Siswa
            $table->enum('status', ['aktif', 'lulus', 'pindah', 'dikeluarkan', 'cuti'])->default('aktif');
            $table->string('foto_path', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('siswa_orang_tuas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->enum('hubungan', ['ayah', 'ibu', 'wali']);
            $table->string('nama', 150);
            $table->unsignedSmallInteger('tahun_lahir')->nullable();
            $table->string('jenjang_pendidikan', 50)->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->string('penghasilan', 100)->nullable();
            $table->string('nik', 20)->nullable();
            $table->string('no_hp', 30)->nullable();
            $table->timestamps();
        });

        Schema::create('rayon_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('rayon_id')->constrained('rayons')->cascadeOnDelete();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->string('alasan_pindah', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('rombel_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('rombel_id')->constrained('rombels')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tahun_ajaran_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rombel_siswa');
        Schema::dropIfExists('rayon_siswa');
        Schema::dropIfExists('siswa_orang_tuas');
        Schema::dropIfExists('siswas');
    }
};
