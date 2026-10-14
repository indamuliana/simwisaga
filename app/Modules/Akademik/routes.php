<?php

use App\Modules\Akademik\Livewire\CustomKelompokIndex;
use App\Modules\Akademik\Livewire\JurusanIndex;
use App\Modules\Akademik\Livewire\MapelIndex;
use App\Modules\Akademik\Livewire\ProgramIndex;
use App\Modules\Akademik\Livewire\RayonIndex;
use App\Modules\Akademik\Livewire\RombelIndex;
use App\Modules\Akademik\Livewire\StrukturKurikulumIndex;
use App\Modules\Akademik\Livewire\TahunAjaranManager;
use Illuminate\Support\Facades\Route;

Route::prefix('akademik')->name('akademik.')->group(function () {
    Route::get('/tahun-ajaran', TahunAjaranManager::class)->name('tahun-ajaran');
    Route::get('/jurusan', JurusanIndex::class)->name('jurusan');
    Route::get('/rombel', RombelIndex::class)->name('rombel');
    Route::get('/rayon', RayonIndex::class)->name('rayon');
    Route::get('/program', ProgramIndex::class)->name('program');
    Route::get('/mapel', MapelIndex::class)->name('mapel');
    Route::get('/kurikulum', StrukturKurikulumIndex::class)->name('kurikulum');
    Route::get('/custom-kelompok', CustomKelompokIndex::class)->name('custom-kelompok');
});
