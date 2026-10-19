<?php

use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\KalenderPendidikan\Livewire\KalenderIndex;
use App\Modules\KalenderPendidikan\Livewire\KategoriManager;
use App\Modules\KalenderPendidikan\Livewire\LiburManager;
use App\Modules\KalenderPendidikan\Models\KalenderAgenda;
use App\Modules\KalenderPendidikan\Models\KalenderLibur;
use App\Modules\KalenderPendidikan\Services\KalenderService;
use App\Modules\Sistem\Models\School;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::prefix('kalender-pendidikan')->name('kalender.')->group(function () {
    Route::get('/', KalenderIndex::class)->name('index')->middleware('can:kalender.lihat');
    Route::get('/kategori', KategoriManager::class)->name('kategori')->middleware('can:kalender.kategori.kelola');
    Route::get('/libur', LiburManager::class)->name('libur')->middleware('can:kalender.libur.kelola');

    // Cetak PDF Kalender Resmi
    Route::get('/cetak-pdf', function () {
        $sekolah = School::current();
        $tahunAjaran = TahunAjaran::active()->first();

        $agendas = KalenderAgenda::with(['kategori', 'creator', 'validator'])
            ->visibleForUser(auth()->user())
            ->orderBy('tgl_mulai')
            ->orderBy('jam_mulai')
            ->get();

        $liburs = KalenderLibur::orderBy('tgl_mulai')->get();

        $pdf = Pdf::loadView('kalenderpendidikan::pdf.kalender-pendidikan-pdf', compact('sekolah', 'tahunAjaran', 'agendas', 'liburs'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('Kalender-Pendidikan-SIRAGA.pdf');
    })->name('cetak-pdf')->middleware('can:kalender.lihat');

    // Ekspor Agenda Tunggal ke Format iCal (.ics)
    Route::get('/agenda/{agenda}/ics', function (KalenderAgenda $agenda, KalenderService $service) {
        $icsContent = $service->generateIcs($agenda);
        $filename = 'agenda-' . \Illuminate\Support\Str::slug($agenda->judul) . '.ics';

        return response($icsContent, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    })->name('export-ics')->middleware('can:kalender.lihat');
});
