<?php

use App\Modules\PortalSiswa\Livewire\BeasiswaSiswa;
use App\Modules\PortalSiswa\Livewire\BerandaSiswa;
use App\Modules\PortalSiswa\Livewire\KehadiranSiswa;
use App\Modules\PortalSiswa\Livewire\KepesantrenanSiswa;
use App\Modules\PortalSiswa\Livewire\NilaiRaporSiswa;
use App\Modules\PortalSiswa\Livewire\PrestasiSiswa;
use App\Modules\PortalSiswa\Livewire\ProfilSiswa;
use App\Modules\PortalSiswa\Livewire\UpdSeniSiswa;
use Illuminate\Support\Facades\Route;

Route::prefix('portal-siswa')->group(function () {
    Route::get('/beranda', BerandaSiswa::class)->name('portal-siswa.beranda');
    Route::get('/profil', ProfilSiswa::class)->name('portal-siswa.profil');
    Route::get('/nilai-rapor', NilaiRaporSiswa::class)->name('portal-siswa.nilai-rapor');
    Route::get('/kehadiran', KehadiranSiswa::class)->name('portal-siswa.kehadiran');
    Route::get('/upd-seni', UpdSeniSiswa::class)->name('portal-siswa.upd-seni');
    Route::get('/prestasi', PrestasiSiswa::class)->name('portal-siswa.prestasi');
    Route::get('/beasiswa', BeasiswaSiswa::class)->name('portal-siswa.beasiswa');
    Route::get('/kepesantrenan', KepesantrenanSiswa::class)->name('portal-siswa.kepesantrenan');
});
