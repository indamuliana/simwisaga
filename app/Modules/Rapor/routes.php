<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Rapor\Livewire\RaporDashboard;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Sistem\Models\School;
use Barryvdh\DomPDF\Facade\Pdf;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/rapor', RaporDashboard::class)->name('rapor.index');
    
    // PDF Generation Routes
    Route::get('/rapor/{siswa_id}/cover', function($siswa_id) {
        $siswa = Siswa::with('rombel.jurusan')->findOrFail($siswa_id);
        $sekolah = School::current();
        
        $pdf = Pdf::loadView('rapor::pdf.cover', compact('siswa', 'sekolah'))->setPaper('A4', 'portrait');
        return $pdf->stream('Rapor_Cover_'.$siswa->nisn.'.pdf');
    })->name('rapor.cetak.cover');
    
    Route::get('/rapor/{siswa_id}/identitas', function($siswa_id) {
        $siswa = Siswa::findOrFail($siswa_id);
        
        $pdf = Pdf::loadView('rapor::pdf.identitas', compact('siswa'))->setPaper('A4', 'portrait');
        return $pdf->stream('Rapor_Identitas_'.$siswa->nisn.'.pdf');
    })->name('rapor.cetak.identitas');
    
    Route::get('/rapor/{siswa_id}/nilai', function($siswa_id) {
        $siswa = Siswa::with('rombel.waliKelas', 'rombel.jurusan')->findOrFail($siswa_id);
        $semester = Semester::where('is_active', true)->first();
        $sekolah = School::current();
        $tanggal = request('tanggal', date('Y-m-d'));
        $periodeId = request('periode_id');
        $periode = $periodeId ? \App\Modules\Sistem\Models\PeriodeRapor::find($periodeId) : null;
        if (! $periode && $semester) {
            $semName = str_contains(strtolower($semester->semester), 'genap') ? 'genap' : 'ganjil';
            $periode = \App\Modules\Sistem\Models\PeriodeRapor::where('tahun_ajaran_id', $semester->tahun_ajaran_id)
                ->where('semester', $semName)
                ->where('is_active', true)
                ->first();
        }
        
        // Ambil Nilai Akademik
        $nilaiAkademiks = \App\Modules\Penilaian\Models\NilaiAkhir::with(['penugasanMengajar.mapel.kelompokMapel'])
            ->where('siswa_id', $siswa_id)
            ->get();

        $groupedNilaiAkademiks = $nilaiAkademiks->sortBy(function ($na) {
            $kmUrutan = $na->penugasanMengajar?->mapel?->kelompokMapel?->urutan ?? 99;
            $mapelUrutan = $na->penugasanMengajar?->mapel?->urutan ?? 999;
            return sprintf('%04d-%04d', $kmUrutan, $mapelUrutan);
        })->groupBy(function ($na) {
            $km = $na->penugasanMengajar?->mapel?->kelompokMapel;
            if ($km) {
                return $km->kode . '. ' . $km->nama;
            }
            return 'Lainnya';
        });
            
        // Ambil Ekstrakurikuler
        $ekskuls = \App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota::with('ekstrakurikuler')
            ->where('siswa_id', $siswa_id)
            ->where('semester_id', $semester->id)
            ->get();
            
        // Ambil Absensi (Sakit, Izin, Alpa) dari rekap harian
        $absensi = \App\Modules\Absensi\Models\AbsensiHarian::where('siswa_id', $siswa_id)
            ->where('semester_id', $semester->id)
            ->get()
            ->groupBy('status');
            
        $rekapAbsen = [
            'sakit' => isset($absensi['Sakit']) ? count($absensi['Sakit']) : 0,
            'izin' => isset($absensi['Izin']) ? count($absensi['Izin']) : 0,
            'alpa' => isset($absensi['Alpa']) ? count($absensi['Alpa']) : 0,
        ];
        
        // Ambil Catatan Wali Kelas
        $catatan = \App\Modules\Rapor\Models\CatatanWaliKelas::where('siswa_id', $siswa_id)
            ->where('semester_id', $semester->id)
            ->first()?->catatan ?? '';
        
        $pdf = Pdf::loadView('rapor::pdf.nilai', compact('siswa', 'semester', 'sekolah', 'tanggal', 'periode', 'nilaiAkademiks', 'groupedNilaiAkademiks', 'ekskuls', 'rekapAbsen', 'catatan'))
            ->setPaper('A4', 'portrait');
            
        return $pdf->stream('Rapor_Nilai_'.$siswa->nisn.'.pdf');
    })->name('rapor.cetak.nilai');
});
