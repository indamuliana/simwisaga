<?php

use App\Modules\Kepegawaian\Livewire\PegawaiCreate;
use App\Modules\Kepegawaian\Livewire\PegawaiDetail;
use App\Modules\Kepegawaian\Livewire\PegawaiEdit;
use App\Modules\Kepegawaian\Livewire\PegawaiIndex;
use App\Modules\Kepegawaian\Livewire\PenugasanMengajarIndex;
use Illuminate\Support\Facades\Route;

Route::prefix('kepegawaian')->name('kepegawaian.')->group(function () {
    Route::get('/pegawai', PegawaiIndex::class)->name('pegawai');
    Route::get('/pegawai/create', PegawaiCreate::class)->name('pegawai.create');
    Route::get('/pegawai/{id}', PegawaiDetail::class)->name('pegawai.show');
    Route::get('/pegawai/{id}/edit', PegawaiEdit::class)->name('pegawai.edit');
    Route::get('/penugasan', fn () => redirect()->route('akademik.penugasan'))->name('penugasan');
});
