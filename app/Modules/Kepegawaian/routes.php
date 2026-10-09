<?php

use App\Modules\Kepegawaian\Livewire\PegawaiIndex;
use App\Modules\Kepegawaian\Livewire\PenugasanMengajarIndex;
use Illuminate\Support\Facades\Route;

Route::prefix('kepegawaian')->name('kepegawaian.')->group(function () {
    Route::get('/pegawai', PegawaiIndex::class)->name('pegawai');
    Route::get('/penugasan', PenugasanMengajarIndex::class)->name('penugasan');
});
