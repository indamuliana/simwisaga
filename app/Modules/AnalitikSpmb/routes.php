<?php

use App\Modules\AnalitikSpmb\Livewire\AsalSmpIndex;
use Illuminate\Support\Facades\Route;

Route::prefix('analitik-spmb')->name('analitik-spmb.')->group(function () {
    Route::get('/asal-smp', AsalSmpIndex::class)->name('asal-smp');
});
