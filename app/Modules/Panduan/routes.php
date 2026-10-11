<?php

use App\Modules\Panduan\Livewire\PanduanIndex;
use Illuminate\Support\Facades\Route;

Route::prefix('panduan')->name('panduan.')->group(function () {
    Route::get('/', PanduanIndex::class)->name('index');
});
