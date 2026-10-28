<?php

use Illuminate\Support\Facades\Route;
use App\Modules\SeniBudaya\Livewire\SeniBudayaDashboard;
use App\Modules\SeniBudaya\Livewire\SeniBudayaCabangIndex;
use App\Modules\SeniBudaya\Livewire\SeniBudayaAnggotaIndex;
use App\Modules\SeniBudaya\Livewire\SeniBudayaTpIndex;
use App\Modules\SeniBudaya\Livewire\SeniBudayaPresensi;
use App\Modules\SeniBudaya\Livewire\SeniBudayaPenilaian;
use App\Modules\SeniBudaya\Livewire\SeniBudayaRekap;
use App\Modules\SeniBudaya\Controllers\SeniBudayaExcelController;

Route::middleware('can:seni_budaya.kelola')->prefix('seni-budaya')->name('seni-budaya.')->group(function () {
    Route::get('/', SeniBudayaDashboard::class)->name('index');
    Route::get('/cabang', SeniBudayaCabangIndex::class)->name('cabang');
    Route::get('/anggota', SeniBudayaAnggotaIndex::class)->name('anggota');
    Route::get('/tp', SeniBudayaTpIndex::class)->name('tp');
    Route::get('/{seni_budaya_id}/presensi', SeniBudayaPresensi::class)->name('presensi');
    Route::get('/{seni_budaya_id}/penilaian', SeniBudayaPenilaian::class)->name('penilaian');
    Route::get('/rekap', SeniBudayaRekap::class)->name('rekap');

    // Excel Endpoints
    Route::get('/template-anggota', [SeniBudayaExcelController::class, 'downloadTemplate'])->name('template.anggota');
    Route::get('/export-rekap/{seni_budaya_id}', [SeniBudayaExcelController::class, 'exportRekap'])->name('export.rekap');
});
