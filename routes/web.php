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

// Halaman Publik: Prestasi & Capaian Unggulan
Route::prefix('prestasi')->name('web.prestasi.')->group(function () {
    Route::get('/', \App\Modules\Prestasi\Livewire\PublicPrestasiIndex::class)->name('index');
    Route::get('/sekolah', \App\Modules\Prestasi\Livewire\PublicPrestasiIndex::class)->name('sekolah');
    Route::get('/guru', \App\Modules\Prestasi\Livewire\PublicPrestasiIndex::class)->name('guru');
    Route::get('/siswa', \App\Modules\Prestasi\Livewire\PublicPrestasiIndex::class)->name('siswa');
});

// Halaman Publik: Jurusan & Program Keahlian
Route::prefix('jurusan')->name('web.jurusan.')->group(function () {
    Route::get('/', function () {
        $sekolah = \App\Modules\Sistem\Models\School::current();
        return view('landing.jurusan.index', compact('sekolah'));
    })->name('index');

    Route::get('/pplg', function () {
        $sekolah = \App\Modules\Sistem\Models\School::current();
        return view('landing.jurusan.pplg', compact('sekolah'));
    })->name('pplg');

    Route::get('/tjkt', function () {
        $sekolah = \App\Modules\Sistem\Models\School::current();
        return view('landing.jurusan.tjkt', compact('sekolah'));
    })->name('tjkt');

    Route::get('/pmn', function () {
        $sekolah = \App\Modules\Sistem\Models\School::current();
        return view('landing.jurusan.pmn', compact('sekolah'));
    })->name('pmn');
    Route::redirect('/pemasaran', '/jurusan/pmn');

    Route::get('/htl', function () {
        $sekolah = \App\Modules\Sistem\Models\School::current();
        return view('landing.jurusan.htl', compact('sekolah'));
    })->name('htl');
    Route::redirect('/perhotelan', '/jurusan/htl');
});

// Halaman Publik: Verifikasi Tanda Terima Donasi Beasiswa OTA (Akses via Scan QR Code)
Route::get('/beasiswa-ota/verifikasi/{nomorTransaksi}', [\App\Modules\BeasiswaOta\Controllers\OtaPrintController::class, 'verifikasiDonasi'])
    ->name('beasiswa-ota.verifikasi-donasi');
Route::get('/v/ota/{nomorTransaksi}', [\App\Modules\BeasiswaOta\Controllers\OtaPrintController::class, 'verifikasiDonasi'])
    ->name('beasiswa-ota.v');
Route::get('/beasiswa-ota/verifikasi-kolektif/{batchKode}', [\App\Modules\BeasiswaOta\Controllers\OtaPrintController::class, 'verifikasiKolektif'])
    ->name('beasiswa-ota.verifikasi-kolektif');
Route::get('/v/ota-batch/{batchKode}', [\App\Modules\BeasiswaOta\Controllers\OtaPrintController::class, 'verifikasiKolektif'])
    ->name('beasiswa-ota.v-kolektif');
Route::get('/beasiswa-ota/verifikasi-penyaluran/{nomorKwitansi}', [\App\Modules\BeasiswaOta\Controllers\OtaPrintController::class, 'verifikasiPenyaluran'])
    ->name('beasiswa-ota.verifikasi-penyaluran');
Route::get('/v/ota-kwitansi/{nomorKwitansi}', [\App\Modules\BeasiswaOta\Controllers\OtaPrintController::class, 'verifikasiPenyaluran'])
    ->name('beasiswa-ota.v-kwitansi');
Route::get('/beasiswa-ota/verifikasi-sertifikat/{donasiId}', [\App\Modules\BeasiswaOta\Controllers\OtaPrintController::class, 'verifikasiSertifikat'])
    ->name('beasiswa-ota.verifikasi-sertifikat');
Route::get('/v/ota-sertifikat/{donasiId}', [\App\Modules\BeasiswaOta\Controllers\OtaPrintController::class, 'verifikasiSertifikat'])
    ->name('beasiswa-ota.v-sertifikat');

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
