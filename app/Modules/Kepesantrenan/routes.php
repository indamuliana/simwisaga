<?php

use App\Modules\Kepesantrenan\Livewire\HafalanInput;
use App\Modules\Kepesantrenan\Livewire\HalaqohIndex;
use App\Modules\Kepesantrenan\Livewire\KamarIndex;
use App\Modules\Kepesantrenan\Livewire\RekapHafalanIndex;
use Illuminate\Support\Facades\Route;

Route::prefix('kepesantrenan')->group(function () {
    Route::get('/kamar', KamarIndex::class)->name('pesantren.kamar');
    Route::get('/hafalan', HafalanInput::class)->name('pesantren.hafalan');
    Route::get('/rekap', RekapHafalanIndex::class)->name('pesantren.rekap');
    Route::get('/halaqoh', HalaqohIndex::class)->name('pesantren.halaqoh');
});
