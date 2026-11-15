<?php

use App\Modules\Panduan\Livewire\PanduanIndex;
use App\Modules\Panduan\Livewire\PanduanRaporWikrama;
use Illuminate\Support\Facades\Route;

Route::prefix('panduan')->name('panduan.')->group(function () {
    Route::get('/', PanduanIndex::class)->name('index');
    Route::get('/rapor-wikrama', PanduanRaporWikrama::class)->name('rapor-wikrama');
});
