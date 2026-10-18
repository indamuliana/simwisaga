<?php

use App\Modules\AdministrasiWeb\Livewire\ArticleForm;
use App\Modules\AdministrasiWeb\Livewire\ArticleIndex;
use App\Modules\AdministrasiWeb\Livewire\FacilityIndex;
use App\Modules\AdministrasiWeb\Livewire\LandingSetting;
use App\Modules\AdministrasiWeb\Livewire\MenuIndex;
use App\Modules\AdministrasiWeb\Livewire\PageForm;
use App\Modules\AdministrasiWeb\Livewire\PageIndex;
use App\Modules\AdministrasiWeb\Livewire\TestimonialIndex;
use Illuminate\Support\Facades\Route;

Route::prefix('web-admin')->name('web-admin.')->group(function () {
    Route::get('/landing', LandingSetting::class)
        ->middleware('can:web.landing.lihat')
        ->name('landing');
    
    // Articles & Berita
    Route::get('/articles', ArticleIndex::class)
        ->middleware('can:web.artikel.lihat')
        ->name('articles.index');
    Route::get('/articles/create', ArticleForm::class)
        ->middleware('can:web.artikel.tambah')
        ->name('articles.create');
    Route::get('/articles/{id}/edit', ArticleForm::class)
        ->middleware('can:web.artikel.edit')
        ->name('articles.edit');

    // Menus
    Route::get('/menus', MenuIndex::class)
        ->middleware('can:web.menu.lihat')
        ->name('menus.index');

    // Custom Pages
    Route::get('/pages', PageIndex::class)
        ->middleware('can:web.halaman.lihat')
        ->name('pages.index');
    Route::get('/pages/create', PageForm::class)
        ->middleware('can:web.halaman.tambah')
        ->name('pages.create');
    Route::get('/pages/{id}/edit', PageForm::class)
        ->middleware('can:web.halaman.edit')
        ->name('pages.edit');

    // Facilities & Labs
    Route::get('/facilities', FacilityIndex::class)
        ->middleware('can:web.galeri.lihat')
        ->name('facilities.index');

    // Testimonials & Community Voices
    Route::get('/testimonials', TestimonialIndex::class)
        ->middleware('can:web.galeri.lihat')
        ->name('testimonials.index');
});
