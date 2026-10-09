<?php

use App\Modules\DashboardData\Livewire\Index;
use App\Modules\DashboardData\Livewire\Program\Index as ProgramIndex;
use App\Modules\DashboardData\Livewire\Jurusan\Index as JurusanIndex;
use App\Modules\DashboardData\Livewire\Rombel\Index as RombelIndex;
use App\Modules\DashboardData\Livewire\SekolahAsal\Index as SekolahAsalIndex;
use App\Modules\DashboardData\Livewire\StudentDetail;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard-data')->name('dashboard-data.')->group(function () {
    Route::get('/', Index::class)->name('index');
    
    Route::get('/program', ProgramIndex::class)->name('program');
    Route::get('/program/{id}/students', StudentDetail::class)->name('program.students');
    
    Route::get('/jurusan', JurusanIndex::class)->name('jurusan');
    Route::get('/jurusan/{id}/students', StudentDetail::class)->name('jurusan.students');
    
    Route::get('/rombel', RombelIndex::class)->name('rombel');
    Route::get('/rombel/{id}/students', StudentDetail::class)->name('rombel.students');
    
    Route::get('/sekolah-asal', SekolahAsalIndex::class)->name('sekolah-asal');
    Route::get('/sekolah-asal/{name}/students', StudentDetail::class)->name('sekolah-asal.students');
});
