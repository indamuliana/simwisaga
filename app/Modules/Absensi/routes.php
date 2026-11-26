<?php

use Illuminate\Support\Facades\Route;

Route::get('/absensi/dashboard', \App\Modules\Absensi\Livewire\AbsensiDashboard::class)
    ->middleware('can:absensi.harian.lihat')
    ->name('absensi.dashboard');
Route::get('/absensi/harian', \App\Modules\Absensi\Livewire\AbsensiHarianIndex::class)
    ->middleware('can:absensi.harian.lihat')
    ->name('absensi.harian');
Route::get('/absensi/mapel', \App\Modules\Absensi\Livewire\AbsensiMapelIndex::class)->name('absensi.mapel');
Route::get('/absensi/rekap', \App\Modules\Absensi\Livewire\RekapAbsensiIndex::class)->name('absensi.rekap');
Route::get('/absensi/rekap/pdf', [\App\Modules\Absensi\Controllers\RekapAbsensiController::class, 'cetakPdf'])->name('absensi.rekap.pdf');
