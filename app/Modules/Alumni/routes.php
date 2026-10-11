<?php

use App\Modules\Alumni\Livewire\AlumniIndex;
use Illuminate\Support\Facades\Route;

Route::prefix('alumni')->name('alumni.')->group(function () {
    Route::get('/', AlumniIndex::class)->name('index');
});
