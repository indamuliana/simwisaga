<?php

use App\Modules\AnalitikSpmb\Livewire\AsalSmpIndex;
use App\Modules\AnalitikSpmb\Livewire\MasterSmpIndex;
use App\Modules\AnalitikSpmb\Livewire\PerbaikanSmpIndex;
use Illuminate\Support\Facades\Route;

Route::prefix('analitik-spmb')->name('analitik-spmb.')->group(function () {
    Route::get('/asal-smp', AsalSmpIndex::class)->name('asal-smp');
    Route::get('/perbaikan-smp', PerbaikanSmpIndex::class)->name('perbaikan-smp');
    Route::get('/master-smp', MasterSmpIndex::class)->name('master-smp');
});
