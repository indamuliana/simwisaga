<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Rapor\Livewire\RaporWikramaIndex;
use App\Modules\Rapor\Livewire\RaporPemerintahIndex;
use App\Modules\Rapor\Livewire\CatatanWaliKelasIndex;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Sistem\Models\School;
use App\Modules\Sistem\Models\PeriodeRapor;
use App\Modules\Rapor\Services\RaporScoreService;
use Barryvdh\DomPDF\Facade\Pdf;

Route::middleware(['web', 'auth'])->group(function () {
    // 1. Menu Utama & Submodul Rapor
    Route::redirect('/rapor', '/rapor/wikrama')->name('rapor.index');
    Route::get('/rapor/wikrama', RaporWikramaIndex::class)->name('rapor.wikrama');
    Route::get('/rapor/pemerintah', RaporPemerintahIndex::class)->name('rapor.pemerintah');
    Route::get('/rapor/catatan-wali-kelas', CatatanWaliKelasIndex::class)->name('rapor.catatan-wali-kelas');

    // 2. Cetak Massal (Didefinisikan sebelum route berparameter {siswa_id})
    Route::get('/rapor/bulk/wikrama-nilai', function () {
        $scope = request('scope', 'rombel');
        $targetId = request('target_id');
        $periodeId = request('periode_id');
        $tanggal = request('tanggal', date('Y-m-d'));

        $semester = Semester::where('is_active', true)->firstOrFail();
        $sekolah = School::current();
        $periode = $periodeId ? PeriodeRapor::find($periodeId) : null;

        $query = Siswa::with(['rombel.jurusan', 'rombel.waliKelas', 'rayon.pembimbing'])->where('is_active', true);
        if ($scope === 'rayon') {
            $query->where('rayon_id', $targetId);
        } else {
            $query->inRombel((int) $targetId);
        }
        $siswas = $query->orderBy('nama')->get();

        if ($siswas->isEmpty()) {
            return back()->with('error', 'Tidak ada data siswa ditemukan untuk dicetak.');
        }

        $service = app(RaporScoreService::class);
        $targetKey = $service->resolveTargetPeriodKey($periode, $semester);
        $periodMeta = $service->getPeriodMeta($targetKey);

        $studentsData = [];
        foreach ($siswas as $siswa) {
            $records = $service->getWikramaAcademicRecords($siswa, $semester, $targetKey);
            $groupedAcademic = $service->groupAcademicRecords($records);
            $catatan = $service->getCatatanForSiswa($siswa->id, $semester->id, $periode?->id);
            $rekapAbsen = $service->getRekapAbsen($siswa->id, $semester->id);
            $ekskuls = $service->getEkskuls($siswa->id, $semester->id);

            $studentsData[] = [
                'siswa' => $siswa,
                'semester' => $semester,
                'sekolah' => $sekolah,
                'tanggal' => $tanggal,
                'periode' => $periode,
                'periodMeta' => $periodMeta,
                'groupedAcademic' => $groupedAcademic,
                'catatan' => $catatan,
                'rekapAbsen' => $rekapAbsen,
                'ekskuls' => $ekskuls,
            ];
        }

        $pdf = Pdf::loadView('rapor::pdf.wikrama-nilai', compact('studentsData'))
            ->setPaper('A4', 'portrait');

        $filename = 'Rapor_Wikrama_Massal_' . ($scope === 'rayon' ? 'Rayon_' : 'Rombel_') . $targetId . '.pdf';
        return $pdf->stream($filename);
    })->name('rapor.cetak-massal.wikrama');

    Route::get('/rapor/bulk/pemerintah-nilai', function () {
        $scope = request('scope', 'rombel');
        $targetId = request('target_id');
        $semesterId = request('semester_id');
        $tanggal = request('tanggal', date('Y-m-d'));

        $semester = $semesterId ? Semester::findOrFail($semesterId) : Semester::where('is_active', true)->firstOrFail();
        $sekolah = School::current();

        $query = Siswa::with(['rombel.jurusan', 'rombel.waliKelas', 'rayon.pembimbing', 'riwayatAkademiks'])->where('is_active', true);
        if ($scope === 'rayon') {
            $query->where('rayon_id', $targetId);
        } else {
            $query->inRombel((int) $targetId);
        }
        $siswas = $query->orderBy('nama')->get();

        if ($siswas->isEmpty()) {
            return back()->with('error', 'Tidak ada data siswa ditemukan untuk dicetak.');
        }

        $service = app(RaporScoreService::class);
        $studentsData = [];

        foreach ($siswas as $siswa) {
            $records = $service->getPemerintahAcademicRecords($siswa, $semester);
            $groupedAcademic = $service->groupAcademicRecords($records);
            $catatan = $service->getCatatanForSiswa($siswa->id, $semester->id, null);
            $rekapAbsen = $service->getRekapAbsen($siswa->id, $semester->id);
            $ekskuls = $service->getEkskuls($siswa->id, $semester->id);

            $studentsData[] = [
                'siswa' => $siswa,
                'semester' => $semester,
                'sekolah' => $sekolah,
                'tanggal' => $tanggal,
                'groupedAcademic' => $groupedAcademic,
                'catatan' => $catatan,
                'rekapAbsen' => $rekapAbsen,
                'ekskuls' => $ekskuls,
            ];
        }

        $pdf = Pdf::loadView('rapor::pdf.pemerintah-nilai', compact('studentsData'))
            ->setPaper('A4', 'portrait');

        $filename = 'Rapor_Pemerintah_Massal_' . ($scope === 'rayon' ? 'Rayon_' : 'Rombel_') . $targetId . '.pdf';
        return $pdf->stream($filename);
    })->name('rapor.cetak-massal.pemerintah');

    // 3. Cetak Dokumen Rapor Wikrama Individual
    Route::get('/rapor/{siswa_id}/wikrama-nilai', function ($siswa_id) {
        $siswa = Siswa::with(['rombel.jurusan', 'rombel.waliKelas', 'rayon.pembimbing'])->findOrFail($siswa_id);
        $semester = Semester::where('is_active', true)->firstOrFail();
        $sekolah = School::current();
        $tanggal = request('tanggal', date('Y-m-d'));
        $periodeId = request('periode_id');
        $periode = $periodeId ? PeriodeRapor::find($periodeId) : null;

        $service = app(RaporScoreService::class);
        $targetKey = $service->resolveTargetPeriodKey($periode, $semester);
        $periodMeta = $service->getPeriodMeta($targetKey);

        $records = $service->getWikramaAcademicRecords($siswa, $semester, $targetKey);
        $groupedAcademic = $service->groupAcademicRecords($records);
        $catatan = $service->getCatatanForSiswa($siswa->id, $semester->id, $periode?->id);
        $rekapAbsen = $service->getRekapAbsen($siswa->id, $semester->id);
        $ekskuls = $service->getEkskuls($siswa->id, $semester->id);

        $studentsData = [
            [
                'siswa' => $siswa,
                'semester' => $semester,
                'sekolah' => $sekolah,
                'tanggal' => $tanggal,
                'periode' => $periode,
                'periodMeta' => $periodMeta,
                'groupedAcademic' => $groupedAcademic,
                'catatan' => $catatan,
                'rekapAbsen' => $rekapAbsen,
                'ekskuls' => $ekskuls,
            ]
        ];

        $pdf = Pdf::loadView('rapor::pdf.wikrama-nilai', compact('studentsData'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('Rapor_Wikrama_' . ($periodMeta['period_key'] ?? '9M') . '_' . $siswa->nisn . '.pdf');
    })->whereNumber('siswa_id')->name('rapor.cetak.wikrama');

    // 4. Cetak Dokumen Rapor Pemerintah Individual
    $renderPemerintahSingle = function ($siswa_id) {
        $siswa = Siswa::with(['rombel.jurusan', 'rombel.waliKelas', 'rayon.pembimbing', 'riwayatAkademiks'])->findOrFail($siswa_id);
        $semesterId = request('semester_id');
        $semester = $semesterId ? Semester::findOrFail($semesterId) : Semester::where('is_active', true)->firstOrFail();
        $sekolah = School::current();
        $tanggal = request('tanggal', date('Y-m-d'));

        $service = app(RaporScoreService::class);
        $records = $service->getPemerintahAcademicRecords($siswa, $semester);
        $groupedAcademic = $service->groupAcademicRecords($records);
        $catatan = $service->getCatatanForSiswa($siswa->id, $semester->id, null);
        $rekapAbsen = $service->getRekapAbsen($siswa->id, $semester->id);
        $ekskuls = $service->getEkskuls($siswa->id, $semester->id);

        $studentsData = [
            [
                'siswa' => $siswa,
                'semester' => $semester,
                'sekolah' => $sekolah,
                'tanggal' => $tanggal,
                'groupedAcademic' => $groupedAcademic,
                'catatan' => $catatan,
                'rekapAbsen' => $rekapAbsen,
                'ekskuls' => $ekskuls,
            ]
        ];

        $pdf = Pdf::loadView('rapor::pdf.pemerintah-nilai', compact('studentsData'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('Rapor_Pemerintah_Sem_' . $semester->semester . '_' . $siswa->nisn . '.pdf');
    };

    Route::get('/rapor/{siswa_id}/pemerintah-nilai', $renderPemerintahSingle)->whereNumber('siswa_id')->name('rapor.cetak.pemerintah');
    Route::get('/rapor/{siswa_id}/nilai', $renderPemerintahSingle)->whereNumber('siswa_id')->name('rapor.cetak.nilai');

    // 5. Dokumen Cover & Identitas Individual
    Route::get('/rapor/{siswa_id}/cover', function ($siswa_id) {
        $siswa = Siswa::with('rombel.jurusan')->findOrFail($siswa_id);
        $sekolah = School::current();

        $pdf = Pdf::loadView('rapor::pdf.cover', compact('siswa', 'sekolah'))->setPaper('A4', 'portrait');
        return $pdf->stream('Rapor_Cover_' . $siswa->nisn . '.pdf');
    })->whereNumber('siswa_id')->name('rapor.cetak.cover');

    Route::get('/rapor/{siswa_id}/identitas', function ($siswa_id) {
        $siswa = Siswa::findOrFail($siswa_id);

        $pdf = Pdf::loadView('rapor::pdf.identitas', compact('siswa'))->setPaper('A4', 'portrait');
        return $pdf->stream('Rapor_Identitas_' . $siswa->nisn . '.pdf');
    })->whereNumber('siswa_id')->name('rapor.cetak.identitas');
});
