<?php

use App\Core\Livewire\Auth\ChangePassword;
use App\Core\Livewire\Auth\Login;
use App\Core\Livewire\Dashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $sekolah = \App\Modules\Sistem\Models\School::current();
    $jurusans = rescue(fn () => \App\Modules\Akademik\Models\Jurusan::where('is_active', true)->get(), collect());
    $landingBlocks = rescue(fn () => app(\App\Core\Modules\ModuleRegistry::class)->landingBlocks(), []);

    return view('welcome', compact('sekolah', 'jurusans', 'landingBlocks'));
})->name('home');

Route::get('/berita', \App\Modules\AdministrasiWeb\Livewire\PublicArticleList::class)->name('web.berita.index');
Route::get('/berita/{slug}', \App\Modules\AdministrasiWeb\Livewire\PublicArticleDetail::class)->name('web.berita.detail');
Route::get('/halaman/{slug}', \App\Modules\AdministrasiWeb\Livewire\PublicPageDetail::class)->name('web.page.detail');

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('home');
    })->name('logout');

    Route::get('/ganti-password', ChangePassword::class)->name('password.change');
});

Route::middleware(['auth', 'active', 'password.fresh'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
});
