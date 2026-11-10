<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            // Ubah tipe kolom enum ke string agar fleksibel
            $table->string('jenis_gtk', 50)->default('Guru')->change();
            $table->string('status_kepegawaian', 50)->default('GTY')->change();

            // Wilayah & Alamat
            $table->string('jenis_wilayah', 20)->default('dalam_negeri')->after('alamat');
            $table->string('negara', 100)->nullable()->after('jenis_wilayah');
            $table->string('provinsi_kode', 20)->nullable()->after('negara');
            $table->string('provinsi_nama', 100)->nullable()->after('provinsi_kode');
            $table->string('kabupaten_kode', 20)->nullable()->after('provinsi_nama');
            $table->string('kabupaten_nama', 100)->nullable()->after('kabupaten_kode');
            $table->string('kecamatan_kode', 20)->nullable()->after('kabupaten_nama');
            $table->string('kecamatan_nama', 100)->nullable()->after('kecamatan_kode');
            $table->string('desa_kode', 20)->nullable()->after('kecamatan_nama');
            $table->string('desa_nama', 100)->nullable()->after('desa_kode');
            $table->string('kode_pos', 10)->nullable()->after('desa_nama');

            // Kepegawaian & Sertifikasi
            $table->string('jabatan_struktural', 100)->nullable()->after('status_kepegawaian');
            $table->string('nomor_peserta_ukg', 50)->nullable()->after('nuptk');
            $table->string('nomor_registrasi_guru', 50)->nullable()->after('nomor_peserta_ukg');
            $table->string('nomor_peserta_sertifikasi', 50)->nullable()->after('nomor_registrasi_guru');
            $table->string('nomor_sertifikat_pendidik', 50)->nullable()->after('nomor_peserta_sertifikasi');
            $table->date('tmt_awal_guru')->nullable()->after('nomor_sertifikat_pendidik');
            $table->string('no_sk_mulai_guru', 100)->nullable()->after('tmt_awal_guru');
            $table->date('tgl_diangkat_pegawai_tetap')->nullable()->after('no_sk_mulai_guru');
            $table->string('no_sk_tetap_yayasan', 100)->nullable()->after('tgl_diangkat_pegawai_tetap');
            $table->date('tgl_awal_masuk_wikrama')->nullable()->after('no_sk_tetap_yayasan');

            // Identitas & Pajak & BPJS
            $table->string('golongan_darah', 5)->nullable()->after('email');
            $table->string('npwp', 50)->nullable()->after('golongan_darah');
            $table->string('nama_wajib_pajak', 150)->nullable()->after('npwp');
            $table->string('no_kartu_bpjs_kesehatan', 50)->nullable()->after('nama_wajib_pajak');
            $table->string('no_kartu_bpjs_ketenagakerjaan', 50)->nullable()->after('no_kartu_bpjs_kesehatan');

            // Pendidikan & Pengalaman
            $table->string('pendidikan_terakhir', 50)->nullable()->after('kode_pos');
            $table->string('jurusan_dan_instansi', 200)->nullable()->after('pendidikan_terakhir');
            $table->date('tgl_lulus_pendidikan_terakhir')->nullable()->after('jurusan_dan_instansi');
            $table->string('tahun_lulus_pendidikan_terakhir', 10)->nullable()->after('tgl_lulus_pendidikan_terakhir');
            $table->json('pengalaman_kerja')->nullable()->after('tahun_lulus_pendidikan_terakhir');

            // Keluarga
            $table->string('status_pernikahan', 30)->nullable()->after('pengalaman_kerja');
            $table->string('nama_pasangan', 150)->nullable()->after('status_pernikahan');
            $table->date('tgl_lahir_pasangan')->nullable()->after('nama_pasangan');
            $table->unsignedTinyInteger('jumlah_anak')->default(0)->after('tgl_lahir_pasangan');
            $table->json('data_anak')->nullable()->after('jumlah_anak');
            $table->string('nama_ayah', 150)->nullable()->after('data_anak');
            $table->string('nama_ibu', 150)->nullable()->after('nama_ayah');
        });

        Schema::create('pegawai_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->unsignedTinyInteger('slot')->comment('1: Utama, 2-5: Tambahan');
            $table->string('foto_path', 255);
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();

            $table->unique(['pegawai_id', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai_fotos');

        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_wilayah', 'negara', 'provinsi_kode', 'provinsi_nama',
                'kabupaten_kode', 'kabupaten_nama', 'kecamatan_kode', 'kecamatan_nama',
                'desa_kode', 'desa_nama', 'kode_pos',
                'jabatan_struktural', 'nomor_peserta_ukg', 'nomor_registrasi_guru',
                'nomor_peserta_sertifikasi', 'nomor_sertifikat_pendidik',
                'tmt_awal_guru', 'no_sk_mulai_guru', 'tgl_diangkat_pegawai_tetap',
                'no_sk_tetap_yayasan', 'tgl_awal_masuk_wikrama',
                'golongan_darah', 'npwp', 'nama_wajib_pajak',
                'no_kartu_bpjs_kesehatan', 'no_kartu_bpjs_ketenagakerjaan',
                'pendidikan_terakhir', 'jurusan_dan_instansi', 'tgl_lulus_pendidikan_terakhir',
                'tahun_lulus_pendidikan_terakhir', 'pengalaman_kerja',
                'status_pernikahan', 'nama_pasangan', 'tgl_lahir_pasangan',
                'jumlah_anak', 'data_anak', 'nama_ayah', 'nama_ibu',
            ]);
        });
    }
};
