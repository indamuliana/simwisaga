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
        // 1. Rekening Bank & Kas Tunai Sekolah
        Schema::create('keuangan_rekenings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank');
            $table->string('nomor_rekening')->nullable();
            $table->string('atas_nama');
            $table->boolean('is_kas_tunai')->default(false);
            $table->decimal('saldo_awal', 15, 2)->default(0.00);
            $table->decimal('saldo_saat_ini', 15, 2)->default(0.00);
            $table->boolean('is_aktif')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 2. Pos Pembayaran (SPP, DSP, Insidental Kegiatan)
        Schema::create('keuangan_pos_biayas', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama');
            $table->enum('tipe', ['bulanan', 'bebas', 'insidental'])->default('bulanan');
            $table->boolean('is_aktif')->default(true);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 3. Tarif Standar Biaya per Angkatan / Tingkat / Tahun Ajaran
        Schema::create('keuangan_tarifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_biaya_id')->constrained('keuangan_pos_biayas')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->enum('tingkat', ['X', 'XI', 'XII'])->nullable();
            $table->foreignId('jurusan_id')->nullable()->constrained('jurusans')->nullOnDelete();
            $table->string('jalur_masuk')->nullable();
            $table->decimal('nominal', 15, 2)->default(0.00);
            $table->timestamps();
        });

        // 4. Tarif Kustom Siswa (Dispensasi, Beasiswa OTA, KIP, dsb)
        Schema::create('keuangan_siswa_tarifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('pos_biaya_id')->constrained('keuangan_pos_biayas')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->decimal('nominal_kustom', 15, 2)->default(0.00);
            $table->enum('jenis_potongan', ['nominal_tetap', 'diskon_persen', 'gratis'])->default('nominal_tetap');
            $table->decimal('persentase_potongan', 5, 2)->nullable();
            $table->string('alasan')->nullable();
            $table->timestamps();
        });

        // 5. Buku Tagihan & Piutang Siswa
        Schema::create('keuangan_tagihans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tagihan', 40)->unique();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('pos_biaya_id')->constrained('keuangan_pos_biayas')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->unsignedTinyInteger('bulan')->nullable(); // 1-12 (7=Juli s.d 6=Juni)
            $table->unsignedSmallInteger('tahun')->nullable();
            $table->decimal('nominal_tagihan', 15, 2)->default(0.00);
            $table->decimal('nominal_terbayar', 15, 2)->default(0.00);
            $table->decimal('sisa_tagihan', 15, 2)->default(0.00);
            $table->enum('status', ['belum_bayar', 'dicicil', 'lunas'])->default('belum_bayar');
            $table->date('jatuh_tempo')->nullable();
            $table->timestamps();

            $table->index(['siswa_id', 'status']);
            $table->index(['pos_biaya_id', 'tahun_ajaran_id']);
        });

        // 6. Transaksi Kas Masuk & Kwitansi
        Schema::create('keuangan_transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi', 40)->unique();
            $table->string('kode_verifikasi', 64)->unique();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('rekening_id')->nullable()->constrained('keuangan_rekenings')->nullOnDelete();
            $table->enum('channel_pembayaran', ['offline_kasir', 'online_transfer', 'subsidi_beasiswa'])->default('offline_kasir');
            $table->decimal('total_bayar', 15, 2)->default(0.00);
            $table->dateTime('tanggal_bayar');
            $table->string('nama_penyetor')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 7. Rincian Alokasi Transaksi ke Tagihan (Mendukung Pembayaran Parsial/Cicilan)
        Schema::create('keuangan_transaksi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('keuangan_transaksis')->cascadeOnDelete();
            $table->foreignId('tagihan_id')->constrained('keuangan_tagihans')->cascadeOnDelete();
            $table->foreignId('pos_biaya_id')->constrained('keuangan_pos_biayas')->cascadeOnDelete();
            $table->decimal('nominal_dibayar', 15, 2)->default(0.00);
            $table->decimal('sisa_setelah_bayar', 15, 2)->default(0.00);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        // 8. Pembayaran Online & Verifikasi Struk Transfer Orang Tua
        Schema::create('keuangan_pembayaran_onlines', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengajuan', 40)->unique();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('rekening_tujuan_id')->nullable()->constrained('keuangan_rekenings')->nullOnDelete();
            $table->string('bank_pengirim')->nullable();
            $table->string('nomor_rekening_pengirim')->nullable();
            $table->string('nama_pemilik_rekening');
            $table->decimal('nominal_transfer', 15, 2)->default(0.00);
            $table->date('tanggal_transfer');
            $table->string('file_bukti_path');
            $table->json('alokasi_pos')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('alasan_penolakan')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('diverifikasi_pada')->nullable();
            $table->foreignId('transaksi_id')->nullable()->constrained('keuangan_transaksis')->nullOnDelete();
            $table->timestamps();
        });

        // 9. Piutang & Status Ijazah Alumni
        Schema::create('keuangan_alumni_tunggakans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->unique()->constrained('siswas')->cascadeOnDelete();
            $table->enum('status_ijazah', ['ditahan', 'boleh_diambil', 'sudah_diambil'])->default('ditahan');
            $table->date('tanggal_serah_terima')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keuangan_alumni_tunggakans');
        Schema::dropIfExists('keuangan_pembayaran_onlines');
        Schema::dropIfExists('keuangan_transaksi_items');
        Schema::dropIfExists('keuangan_transaksis');
        Schema::dropIfExists('keuangan_tagihans');
        Schema::dropIfExists('keuangan_siswa_tarifs');
        Schema::dropIfExists('keuangan_tarifs');
        Schema::dropIfExists('keuangan_pos_biayas');
        Schema::dropIfExists('keuangan_rekenings');
    }
};

