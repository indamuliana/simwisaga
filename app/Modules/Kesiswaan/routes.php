<?php

use App\Modules\Kesiswaan\Livewire\CetakAbsensi;
use App\Modules\Kesiswaan\Livewire\KelulusanIndex;
use App\Modules\Kesiswaan\Livewire\KenaikanKelasIndex;
use App\Modules\Kesiswaan\Livewire\PlottingProgramMassal;
use App\Modules\Kesiswaan\Livewire\PlottingRayonMassal;
use App\Modules\Kesiswaan\Livewire\PlottingRombelMassal;
use App\Modules\Kesiswaan\Livewire\SiswaCreate;
use App\Modules\Kesiswaan\Livewire\SiswaEdit;
use App\Modules\Kesiswaan\Livewire\SiswaImport;
use App\Modules\Kesiswaan\Livewire\SiswaIndex;
use Illuminate\Support\Facades\Route;

Route::prefix('kesiswaan')->name('kesiswaan.')->group(function () {
    Route::get('/siswa', SiswaIndex::class)->name('siswa');
    Route::get('/siswa/create', SiswaCreate::class)->name('siswa.create');
    Route::get('/siswa/{id}/edit', SiswaEdit::class)->name('siswa.edit');
    Route::get('/cetak-absensi', CetakAbsensi::class)->name('cetak-absensi');
    Route::get('/import-dapodik', SiswaImport::class)->name('import');
    Route::get('/plotting-rombel', PlottingRombelMassal::class)->name('plotting-rombel');
    Route::get('/kenaikan-kelas', KenaikanKelasIndex::class)->name('kenaikan-kelas');
    Route::get('/kelulusan', KelulusanIndex::class)->name('kelulusan');
    Route::get('/plotting-program', PlottingProgramMassal::class)->name('plotting-program');
    Route::get('/plotting-rayon', PlottingRayonMassal::class)->name('plotting-rayon');
});
