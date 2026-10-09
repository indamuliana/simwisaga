<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Ekstrakurikuler\Livewire\EkstrakurikulerDashboard;
use App\Modules\Ekstrakurikuler\Livewire\EkstrakurikulerInput;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/ekskul', EkstrakurikulerDashboard::class)->name('ekskul.index');
    Route::get('/ekskul/{ekskul_id}/input', EkstrakurikulerInput::class)->name('ekskul.input');
});
