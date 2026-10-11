<?php

use App\Modules\Sistem\Livewire\HakAkses;
use App\Modules\Sistem\Livewire\IdentitasSekolah;
use App\Modules\Sistem\Livewire\LogAktivitas;
use App\Modules\Sistem\Livewire\Pengguna;
use Illuminate\Support\Facades\Route;

Route::prefix('sistem')->name('sistem.')->group(function () {
    Route::get('/sekolah', IdentitasSekolah::class)->name('sekolah');
    Route::get('/template-dokumen', \App\Modules\Sistem\Livewire\TemplateDokumenIndex::class)->name('template');
    Route::get('/titimangsa-rapor', \App\Modules\Sistem\Livewire\PeriodeRaporIndex::class)->name('titimangsa');
    Route::get('/pengguna', Pengguna::class)->name('pengguna');
    Route::get('/hak-akses', HakAkses::class)->name('hakakses');
    Route::get('/log-aktivitas', LogAktivitas::class)->name('audit');
});
