<?php

use App\Core\Livewire\Auth\ChangePassword;
use App\Core\Livewire\Auth\Login;
use App\Core\Livewire\Dashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
})->name('home');

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
