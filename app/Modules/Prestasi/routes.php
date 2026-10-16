<?php

use App\Modules\Prestasi\Livewire\PrestasiDashboard;
use App\Modules\Prestasi\Livewire\PrestasiGuruIndex;
use App\Modules\Prestasi\Livewire\PrestasiMandiriSiswa;
use App\Modules\Prestasi\Livewire\PrestasiSekolahIndex;
use App\Modules\Prestasi\Livewire\PrestasiSiswaIndex;
use App\Modules\Prestasi\Livewire\PrestasiVerifikasi;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->prefix('prestasi')->name('prestasi.')->group(function () {
    Route::get('/', PrestasiDashboard::class)->name('index')->middleware('can:prestasi.lihat');
    Route::get('/siswa', PrestasiSiswaIndex::class)->name('siswa')->middleware('can:prestasi.lihat');
    Route::get('/guru', PrestasiGuruIndex::class)->name('guru')->middleware('can:prestasi.guru.lihat');
    Route::get('/sekolah', PrestasiSekolahIndex::class)->name('sekolah')->middleware('can:prestasi.sekolah.lihat');
    Route::get('/verifikasi', PrestasiVerifikasi::class)->name('verifikasi')->middleware('can:prestasi.verifikasi');
    Route::get('/mandiri', PrestasiMandiriSiswa::class)->name('mandiri');
});
