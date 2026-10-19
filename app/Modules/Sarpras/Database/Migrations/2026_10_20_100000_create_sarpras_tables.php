<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kategori Aset (Aset Tetap & Bahan Habis Pakai)
        Schema::create('sarpras_kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 100);
            $table->enum('jenis', ['aset_tetap', 'habis_pakai'])->default('aset_tetap');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 2. Ruangan (Minimalis sebagai penanda lokasi aset)
        Schema::create('sarpras_ruangans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ruangan', 50)->unique();
            $table->string('nama_ruangan', 100);
            $table->string('lokasi', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Aset Tetap
        Schema::create('sarpras_asets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset', 60)->unique();
            $table->string('nama_barang', 150);
            $table->string('merk_tipe', 100)->nullable();
            $table->string('nomor_seri', 100)->nullable();
            $table->foreignId('kategori_id')->constrained('sarpras_kategoris')->cascadeOnDelete();
            $table->foreignId('ruangan_id')->nullable()->constrained('sarpras_ruangans')->nullOnDelete();
            $table->unsignedSmallInteger('tahun_perolehan');
            $table->date('tanggal_pengadaan')->nullable();
            $table->string('sumber_dana', 50)->default('BOS Reguler');
            $table->decimal('harga_perolehan', 15, 2)->default(0);
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang', 'Dimusnahkan'])->default('Baik');
            $table->enum('status', ['Tersedia', 'Dipinjam', 'Dalam Perbaikan', 'Dihapuskan'])->default('Tersedia');
            $table->string('foto')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 4. Bahan Habis Pakai (BHP)
        Schema::create('sarpras_bhps', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 50)->unique();
            $table->string('nama_barang', 150);
            $table->foreignId('kategori_id')->constrained('sarpras_kategoris')->cascadeOnDelete();
            $table->string('satuan', 30)->default('Pcs');
            $table->integer('stok_saat_ini')->default(0);
            $table->integer('stok_minimum')->default(5);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 5. Mutasi Kartu Stok BHP
        Schema::create('sarpras_bhp_mutasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bhp_id')->constrained('sarpras_bhps')->cascadeOnDelete();
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->date('tanggal');
            $table->integer('jumlah');
            $table->integer('sisa_stok_setelahnya');
            $table->foreignId('ruangan_id')->nullable()->constrained('sarpras_ruangans')->nullOnDelete();
            $table->string('penerima', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 6. Pengajuan Pembelian Aset / Barang
        Schema::create('sarpras_pengajuans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengajuan', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_barang', 150);
            $table->foreignId('kategori_id')->nullable()->constrained('sarpras_kategoris')->nullOnDelete();
            $table->text('spesifikasi')->nullable();
            $table->integer('jumlah')->default(1);
            $table->decimal('estimasi_harga_satuan', 15, 2)->default(0);
            $table->decimal('total_estimasi', 15, 2)->default(0);
            $table->enum('urgensi', ['Rendah', 'Sedang', 'Tinggi', 'Mendesak'])->default('Sedang');
            $table->foreignId('ruangan_id')->nullable()->constrained('sarpras_ruangans')->nullOnDelete();
            $table->enum('status', [
                'Diajukan',
                'Review Sarpras',
                'Disetujui Kepsek',
                'Ditolak',
                'Realisasi Bendahara',
                'Selesai'
            ])->default('Diajukan');
            $table->text('catatan_sarpras')->nullable();
            $table->text('catatan_kepsek')->nullable();
            $table->text('catatan_bendahara')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        // 7. Peminjaman Aset Bergerak
        Schema::create('sarpras_peminjamans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_peminjaman', 50)->unique();
            $table->foreignId('aset_id')->constrained('sarpras_asets')->cascadeOnDelete();
            $table->string('peminjam_nama', 100);
            $table->string('peminjam_tipe', 30)->default('Guru/GTK'); // Guru/GTK, Siswa, Eksternal
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('kontak_peminjam', 50)->nullable();
            $table->text('keperluan');
            $table->date('tanggal_pinjam');
            $table->date('batas_kembali');
            $table->date('tanggal_kembali')->nullable();
            $table->string('kondisi_awal', 30)->default('Baik');
            $table->string('kondisi_kembali', 30)->nullable();
            $table->enum('status', ['Dipinjam', 'Dikembalikan', 'Terlambat'])->default('Dipinjam');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 8. Pemeliharaan & Tiket Laporan Kerusakan
        Schema::create('sarpras_pemeliharaans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket', 50)->unique();
            $table->foreignId('aset_id')->constrained('sarpras_asets')->cascadeOnDelete();
            $table->foreignId('pelapor_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal_lapor');
            $table->text('deskripsi_kerusakan');
            $table->enum('tingkat_urgensi', ['Ringan', 'Sedang', 'Berat'])->default('Sedang');
            $table->enum('status', [
                'Menunggu Tindakan',
                'Sedang Diperbaiki',
                'Selesai',
                'Afkir'
            ])->default('Menunggu Tindakan');
            $table->text('tindakan_perbaikan')->nullable();
            $table->string('teknisi_vendor', 100)->nullable();
            $table->decimal('biaya', 15, 2)->default(0);
            $table->date('tanggal_selesai')->nullable();
            $table->timestamps();
        });

        // 9. Berita Acara (BAST & Pemusnahan)
        Schema::create('sarpras_berita_acaras', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat', 80)->unique();
            $table->enum('jenis', ['serah_terima', 'pemusnahan']);
            $table->date('tanggal_surat');
            $table->string('pihak_pertama', 150);
            $table->string('pihak_kedua', 150);
            $table->string('perihal', 200);
            $table->json('data_json')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sarpras_berita_acaras');
        Schema::dropIfExists('sarpras_pemeliharaans');
        Schema::dropIfExists('sarpras_peminjamans');
        Schema::dropIfExists('sarpras_pengajuans');
        Schema::dropIfExists('sarpras_bhp_mutasis');
        Schema::dropIfExists('sarpras_bhps');
        Schema::dropIfExists('sarpras_asets');
        Schema::dropIfExists('sarpras_ruangans');
        Schema::dropIfExists('sarpras_kategoris');
    }
};
