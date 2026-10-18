<?php

use App\Modules\BeasiswaOta\Controllers\OtaPrintController;
use App\Modules\BeasiswaOta\Livewire\DonasiMasukCreate;
use App\Modules\BeasiswaOta\Livewire\DonasiMasukIndex;
use App\Modules\BeasiswaOta\Livewire\DonaturKelompokIndex;
use App\Modules\BeasiswaOta\Livewire\LaporanOtaIndex;
use App\Modules\BeasiswaOta\Livewire\OtaDashboard;
use App\Modules\BeasiswaOta\Livewire\OtaSettingIndex;
use App\Modules\BeasiswaOta\Livewire\PenerimaBeasiswaIndex;
use App\Modules\BeasiswaOta\Livewire\PenyaluranIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    // Livewire Pages
    Route::middleware('can:beasiswa_ota.lihat')->group(function () {
        Route::get('/beasiswa-ota', OtaDashboard::class)->name('beasiswa-ota.index');
        Route::get('/beasiswa-ota/laporan', LaporanOtaIndex::class)->name('beasiswa-ota.laporan');
    });

    Route::middleware('can:beasiswa_ota.kelola')->group(function () {
        Route::get('/beasiswa-ota/donatur-kelompok', DonaturKelompokIndex::class)->name('beasiswa-ota.donatur-kelompok');
        Route::get('/beasiswa-ota/penerima', PenerimaBeasiswaIndex::class)->name('beasiswa-ota.penerima');
        Route::get('/beasiswa-ota/donasi-masuk', DonasiMasukIndex::class)->name('beasiswa-ota.donasi-masuk');
        Route::get('/beasiswa-ota/donasi-masuk/create', DonasiMasukCreate::class)->name('beasiswa-ota.donasi-masuk.create');
        Route::get('/beasiswa-ota/penyaluran', PenyaluranIndex::class)->name('beasiswa-ota.penyaluran');
    });

    Route::get('/beasiswa-ota/setting', OtaSettingIndex::class)->middleware('can:beasiswa_ota.setting')->name('beasiswa-ota.setting');

    // PDF Cetak & Download
    Route::middleware('can:beasiswa_ota.cetak')->group(function () {
        Route::get('/beasiswa-ota/cetak/tanda-terima/{id}', [OtaPrintController::class, 'tandaTerima'])->name('beasiswa-ota.cetak.tanda-terima');
        Route::get('/beasiswa-ota/cetak/tanda-terima-kolektif/{batchKode}', [OtaPrintController::class, 'tandaTerimaKolektif'])->name('beasiswa-ota.cetak.tanda-terima-kolektif');
        Route::get('/beasiswa-ota/cetak/sertifikat/{id}', [OtaPrintController::class, 'sertifikat'])->name('beasiswa-ota.cetak.sertifikat');
        Route::get('/beasiswa-ota/cetak/kwitansi/{id}', [OtaPrintController::class, 'kwitansiPenyaluran'])->name('beasiswa-ota.cetak.kwitansi');
    });
});
