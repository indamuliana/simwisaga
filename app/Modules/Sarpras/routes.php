<?php

use App\Modules\Sarpras\Controllers\CetakDokumenController;
use App\Modules\Sarpras\Livewire\AsetIndex;
use App\Modules\Sarpras\Livewire\BhpIndex;
use App\Modules\Sarpras\Livewire\KategoriIndex;
use App\Modules\Sarpras\Livewire\LaporanIndex;
use App\Modules\Sarpras\Livewire\PemeliharaanIndex;
use App\Modules\Sarpras\Livewire\PeminjamanIndex;
use App\Modules\Sarpras\Livewire\PengajuanIndex;
use App\Modules\Sarpras\Livewire\RuanganIndex;
use App\Modules\Sarpras\Livewire\SarprasDashboard;
use App\Modules\Sarpras\Livewire\SumberDanaIndex;
use Illuminate\Support\Facades\Route;

Route::prefix('sarpras')->group(function () {
    Route::get('/', SarprasDashboard::class)->name('sarpras.index')->middleware('permission:sarpras.dashboard.lihat');
    Route::get('/aset', AsetIndex::class)->name('sarpras.aset')->middleware('permission:sarpras.aset.lihat');
    Route::get('/bhp', BhpIndex::class)->name('sarpras.bhp')->middleware('permission:sarpras.bhp.lihat');
    Route::get('/ruangan', RuanganIndex::class)->name('sarpras.ruangan')->middleware('permission:sarpras.ruangan.lihat');
    Route::get('/kategori', KategoriIndex::class)->name('sarpras.kategori')->middleware('permission:sarpras.kategori.lihat');
    Route::get('/sumber-dana', SumberDanaIndex::class)->name('sarpras.sumber-dana')->middleware('permission:sarpras.sumber.lihat');
    Route::get('/pengajuan', PengajuanIndex::class)->name('sarpras.pengajuan')->middleware('permission:sarpras.pengajuan.lihat');
    Route::get('/peminjaman', PeminjamanIndex::class)->name('sarpras.peminjaman')->middleware('permission:sarpras.peminjaman.lihat');
    Route::get('/pemeliharaan', PemeliharaanIndex::class)->name('sarpras.pemeliharaan')->middleware('permission:sarpras.pemeliharaan.lihat');
    Route::get('/laporan', LaporanIndex::class)->name('sarpras.laporan')->middleware('permission:sarpras.laporan.lihat');

    // Cetak Dokumen & PDF
    Route::get('/cetak/bast/{id?}', [CetakDokumenController::class, 'cetakBast'])->name('sarpras.cetak.bast')->middleware('permission:sarpras.aset.cetak');
    Route::get('/cetak/pemusnahan/{id?}', [CetakDokumenController::class, 'cetakPemusnahan'])->name('sarpras.cetak.pemusnahan')->middleware('permission:sarpras.aset.cetak');
    Route::get('/cetak/laporan-pdf', [CetakDokumenController::class, 'cetakLaporanPdf'])->name('sarpras.cetak.laporan-pdf')->middleware('permission:sarpras.laporan.cetak');
    Route::get('/cetak/label-qr', [CetakDokumenController::class, 'cetakLabelQr'])->name('sarpras.cetak.label-qr')->middleware('permission:sarpras.aset.cetak');
});
