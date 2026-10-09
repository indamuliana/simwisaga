<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Penilaian\Livewire\PenilaianDashboard;
use App\Modules\Penilaian\Livewire\PenilaianInput;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/penilaian', PenilaianDashboard::class)->name('penilaian.index');
    Route::get('/penilaian/{penugasan_id}/input', PenilaianInput::class)->name('penilaian.input');
    Route::get('/penilaian/rekap', function() { return 'Rekapitulasi Penilaian'; })->name('penilaian.rekap');
});
