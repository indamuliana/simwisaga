<?php

use App\Modules\Keuangan\Controllers\KeuanganPrintController;
use App\Modules\Keuangan\Livewire\KeuanganDashboard;
use App\Modules\Keuangan\Livewire\PantauanBinaanIndex;
use App\Modules\Keuangan\Livewire\PiutangAlumniIndex;
use App\Modules\Keuangan\Livewire\PosDanTarifIndex;
use App\Modules\Keuangan\Livewire\RekapKasIndex;
use App\Modules\Keuangan\Livewire\RekeningSekolahIndex;
use App\Modules\Keuangan\Livewire\TerbitkanTagihanIndex;
use App\Modules\Keuangan\Livewire\TransaksiKasirIndex;
use App\Modules\Keuangan\Livewire\VerifikasiPembayaranIndex;
use Illuminate\Support\Facades\Route;

// Dashboard & Rekap
Route::middleware('can:keuangan.lihat')->group(function () {
    Route::get('/keuangan', KeuanganDashboard::class)->name('keuangan.index');
});

// Transaksi Kasir
Route::middleware('can:keuangan.transaksi')->group(function () {
    Route::get('/keuangan/kasir', TransaksiKasirIndex::class)->name('keuangan.kasir');
});

// Verifikasi Bukti Bayar Ortu
Route::middleware('can:keuangan.verifikasi')->group(function () {
    Route::get('/keuangan/verifikasi', VerifikasiPembayaranIndex::class)->name('keuangan.verifikasi');
});

// Terbitkan Tagihan & WA
Route::middleware('can:keuangan.tagihan')->group(function () {
    Route::get('/keuangan/tagihan', TerbitkanTagihanIndex::class)->name('keuangan.tagihan');
});

// Pengaturan Pos, Tarif, dan Rekening
Route::middleware('can:keuangan.tarif')->group(function () {
    Route::get('/keuangan/tarif', PosDanTarifIndex::class)->name('keuangan.tarif');
    Route::get('/keuangan/rekening', RekeningSekolahIndex::class)->name('keuangan.rekening');
});

// Piutang Alumni & Ijazah
Route::middleware('can:keuangan.alumni')->group(function () {
    Route::get('/keuangan/alumni', PiutangAlumniIndex::class)->name('keuangan.alumni');
});

// Pantauan Pembimbing Rayon & Wali Kelas
Route::middleware('can:keuangan.binaan')->group(function () {
    Route::get('/keuangan/binaan', PantauanBinaanIndex::class)->name('keuangan.binaan');
});

// Buku Kas & Laporan
Route::middleware('can:keuangan.laporan')->group(function () {
    Route::get('/keuangan/rekap', RekapKasIndex::class)->name('keuangan.rekap');
});

// PDF Cetak & Download
Route::get('/keuangan/cetak/kwitansi/{id}', [KeuanganPrintController::class, 'kwitansi'])->name('keuangan.cetak.kwitansi');

Route::middleware('can:keuangan.cetak')->group(function () {
    Route::get('/keuangan/cetak/surat-tagihan/{siswaId}', [KeuanganPrintController::class, 'suratTagihan'])->name('keuangan.cetak.surat-tagihan');
    Route::get('/keuangan/cetak/rekap-kas', [KeuanganPrintController::class, 'rekapKas'])->name('keuangan.cetak.rekap-kas');
});

