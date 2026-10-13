<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Ekstrakurikuler\Livewire\EkstrakurikulerDashboard;
use App\Modules\Ekstrakurikuler\Livewire\EkstrakurikulerAnggotaIndex;
use App\Modules\Ekstrakurikuler\Livewire\EkstrakurikulerPresensi;
use App\Modules\Ekstrakurikuler\Livewire\EkstrakurikulerPenilaian;
use App\Modules\Ekstrakurikuler\Livewire\EkstrakurikulerRekap;
use App\Modules\Ekstrakurikuler\Livewire\EkstrakurikulerSetting;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/ekskul', EkstrakurikulerDashboard::class)->name('ekskul.index');
    Route::get('/ekskul/anggota', EkstrakurikulerAnggotaIndex::class)->name('ekskul.anggota');
    Route::get('/ekskul/setting', EkstrakurikulerSetting::class)->name('ekskul.setting');
    Route::get('/ekskul/{ekskul_id}/presensi', EkstrakurikulerPresensi::class)->name('ekskul.presensi');
    Route::get('/ekskul/{ekskul_id}/penilaian', EkstrakurikulerPenilaian::class)->name('ekskul.penilaian');
    Route::get('/ekskul/rekap', EkstrakurikulerRekap::class)->name('ekskul.rekap');

    // Fallback rute lama agar kompatibel
    Route::get('/ekskul/{ekskul_id}/input', EkstrakurikulerPenilaian::class)->name('ekskul.input');
});
