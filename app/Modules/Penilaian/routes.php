<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Penilaian\Livewire\MapelPilihanPenilaianIndex;
use App\Modules\Penilaian\Livewire\MapelPilihanPenilaianInput;
use App\Modules\Penilaian\Livewire\PengaturanPenilaianIndex;
use App\Modules\Penilaian\Livewire\PenilaianDashboard;
use App\Modules\Penilaian\Livewire\PenilaianInput;
use App\Modules\Penilaian\Livewire\RekapPenilaianIndex;
use App\Modules\Penilaian\Livewire\TranskripPenilaianIndex;
use App\Modules\Penilaian\Services\TranskripNilaiService;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Sistem\Models\School;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/penilaian', PenilaianDashboard::class)->name('penilaian.index');
Route::get('/penilaian/pengaturan', PengaturanPenilaianIndex::class)->name('penilaian.pengaturan');
Route::get('/penilaian/mapil', MapelPilihanPenilaianIndex::class)->name('penilaian.mapil.index');
Route::get('/penilaian/mapil/{id}/input', MapelPilihanPenilaianInput::class)->name('penilaian.mapil.input');
Route::get('/penilaian/{penugasan_id}/input', PenilaianInput::class)->name('penilaian.input');
Route::get('/penilaian/rekap', RekapPenilaianIndex::class)->name('penilaian.rekap');
Route::get('/penilaian/transkrip', TranskripPenilaianIndex::class)->name('penilaian.transkrip');
Route::get('/penilaian/transkrip/{siswa_id}/pdf', function ($siswa_id, TranskripNilaiService $service) {
    $siswa = Siswa::findOrFail((int) $siswa_id);
    abort_unless(auth()->user()->canViewStudent($siswa), 403, 'Anda tidak memiliki hak akses untuk mencetak transkrip siswa ini.');

    $data = $service->getSiswaTranskripData((int) $siswa_id);
    $sekolah = School::current();
    $pdf = Pdf::loadView('penilaian::pdf.transkrip-siswa', compact('data', 'sekolah'))
        ->setPaper('A4', 'portrait');
    $cleanName = preg_replace('/[^A-Za-z0-9_-]/', '_', $data['siswa']['nama']);
    $identifier = $data['siswa']['nisn'] ?: $data['siswa']['nipd'];
    $filename = 'Transkrip_Nilai_' . $cleanName . '_' . $identifier . '.pdf';
    return $pdf->stream($filename);
})->name('penilaian.transkrip.pdf');
