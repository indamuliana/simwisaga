<?php

use App\Modules\DashboardData\Livewire\Index;
use App\Modules\DashboardData\Livewire\StudentDetail;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard-data')->name('dashboard-data.')->group(function () {
    Route::get('/', Index::class)->name('index');

    // Legacy route redirects menuju parameter tab
    Route::get('/rayon', fn() => redirect()->route('dashboard-data.index', ['tab' => 'rayon']))->name('rayon');
    Route::get('/program', fn() => redirect()->route('dashboard-data.index', ['tab' => 'rayon']))->name('program');
    Route::get('/jurusan', fn() => redirect()->route('dashboard-data.index', ['tab' => 'jurusan']))->name('jurusan');
    Route::get('/rombel', fn() => redirect()->route('dashboard-data.index', ['tab' => 'rombel']))->name('rombel');
    Route::get('/sekolah-asal', fn() => redirect()->route('dashboard-data.index', ['tab' => 'sekolah-asal']))->name('sekolah-asal');

    // Detail Siswa Drilldowns
    Route::get('/rayon/{id}/students', StudentDetail::class)->name('rayon.students');
    Route::get('/program/{id}/students', StudentDetail::class)->name('program.students');
    Route::get('/jurusan/{id}/students', StudentDetail::class)->name('jurusan.students');
    Route::get('/rombel/{id}/students', StudentDetail::class)->name('rombel.students');
    Route::get('/sekolah-asal/{name}/students', StudentDetail::class)->name('sekolah-asal.students');
});
