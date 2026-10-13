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
        
        // Deteksi periode cetak rapor dinamis (STS 1, SAS, STS 2, SAT)
        $isGenap = $semester && str_contains(strtolower($semester->semester), 'genap');
        $targetPeriodKey = $isGenap ? 'SAT' : 'SAS';

        if ($periode) {
            $pKode = strtoupper($periode->kode ?? '');
            $pNama = strtoupper($periode->nama ?? '');
            $pSebutan = strtoupper($periode->sebutan ?? '');

            if (!$isGenap) {
                if (str_contains($pKode, 'PTS') || str_contains($pKode, '9M1') || str_contains($pNama, 'TENGAH') || str_contains($pSebutan, 'MINGGU KE-1')) {
                    $targetPeriodKey = 'STS 1';
                } else {
                    $targetPeriodKey = 'SAS';
                }
            } else {
                if (str_contains($pKode, 'PTS') || str_contains($pKode, '9M3') || str_contains($pNama, 'TENGAH') || str_contains($pSebutan, 'MINGGU KE-3')) {
                    $targetPeriodKey = 'STS 2';
                } else {
                    $targetPeriodKey = 'SAT';
                }
            }
        }

        // Ambil Nilai Akademik
        $nilaiAkademiks = \App\Modules\Penilaian\Models\NilaiAkhir::with(['penugasanMengajar.mapel.kelompokMapel'])
            ->where('siswa_id', $siswa_id)
            ->get();

        // Sesuaikan nilai dan deskripsi mata pelajaran reguler sesuai periode yang dipilih
        foreach ($nilaiAkademiks as $na) {
            if ($na instanceof \App\Modules\Penilaian\Models\NilaiAkhir) {
                $pScore = $na->getScoreForPeriod($targetPeriodKey);
                if ($pScore !== null) {
                    $na->nilai_akhir = $pScore;
                }
                $pDesc = $na->getDescriptionForPeriod($targetPeriodKey);
                $na->deskripsi_manual = !empty($pDesc['deskripsi_manual']) ? $pDesc['deskripsi_manual'] : null;
                $na->deskripsi_tertinggi = !empty($pDesc['deskripsi_tertinggi']) ? $pDesc['deskripsi_tertinggi'] : null;
                $na->deskripsi_terendah = !empty($pDesc['deskripsi_terendah']) ? $pDesc['deskripsi_terendah'] : null;
            }
        }

        // Integrasi Sub Modul Seni Budaya: Cek apakah siswa terdaftar di Seni Budaya
        $sbAnggota = \App\Modules\SeniBudaya\Models\SeniBudayaAnggota::with('seniBudaya')
            ->where('siswa_id', $siswa_id)
            ->where('semester_id', $semester->id)
            ->where('status_keanggotaan', 'aktif')
            ->first();

        if ($sbAnggota && $sbAnggota->seniBudaya) {
            $customMapelNama = 'Seni Budaya: ' . $sbAnggota->seniBudaya->nama;

            // Ambil nilai dan deskripsi dinamis sesuai periode yang sedang dicetak
            $dynamicScore = $sbAnggota->getScoreForPeriod($targetPeriodKey) ?? $sbAnggota->nilai_akhir ?? 0;
            $dynamicDesc = $sbAnggota->getDescriptionForPeriod($targetPeriodKey);

            // Cek apakah mapel Seni Budaya sudah ada dalam daftar $nilaiAkademiks
            $existingNa = $nilaiAkademiks->first(function ($na) {
                $kode = strtolower($na->penugasanMengajar?->mapel?->kode ?? '');
                $nama = strtolower($na->penugasanMengajar?->mapel?->nama ?? '');
                return $kode === 'seni' || str_contains($nama, 'seni budaya');
            });

            if ($existingNa) {
                if ($existingNa->penugasanMengajar && $existingNa->penugasanMengajar->mapel) {
                    $existingNa->penugasanMengajar->mapel->nama = $customMapelNama;
                }
                $existingNa->nilai_akhir = $dynamicScore;
                $existingNa->deskripsi_tertinggi = $dynamicDesc['deskripsi_tertinggi'] ?? $existingNa->deskripsi_tertinggi;
                $existingNa->deskripsi_terendah = $dynamicDesc['deskripsi_terendah'] ?? $existingNa->deskripsi_terendah;
                $existingNa->deskripsi_manual = $dynamicDesc['deskripsi_manual'] ?? $existingNa->deskripsi_manual;
            } else {
                // Buat item dinamis untuk Kelompok A (Muatan Umum)
                $mapelSeni = \App\Modules\Akademik\Models\Mapel::with('kelompokMapel')
                    ->where('kode', 'SENI')
                    ->orWhere('nama', 'like', '%Seni Budaya%')
                    ->first();

                $mockMapel = clone ($mapelSeni ?: new \App\Modules\Akademik\Models\Mapel([
                    'kode' => 'SENI',
                    'nama' => 'Seni Budaya',
                    'kelompok' => 'umum',
                    'urutan' => 6,
                ]));
                $mockMapel->nama = $customMapelNama;

                $mockPenugasan = new \stdClass();
                $mockPenugasan->mapel = $mockMapel;

                $mockNa = new \stdClass();
                $mockNa->penugasanMengajar = $mockPenugasan;
                $mockNa->nilai_akhir = $dynamicScore;
                $mockNa->deskripsi_tertinggi = $dynamicDesc['deskripsi_tertinggi'] ?? null;
                $mockNa->deskripsi_terendah = $dynamicDesc['deskripsi_terendah'] ?? null;
                $mockNa->deskripsi_manual = $dynamicDesc['deskripsi_manual'] ?? null;

                $nilaiAkademiks->push($mockNa);
            }
        }

        $groupedNilaiAkademiks = $nilaiAkademiks->sortBy(function ($na) {
            $kmUrutan = $na->penugasanMengajar?->mapel?->kelompokMapel?->urutan ?? 99;
            $mapelUrutan = $na->penugasanMengajar?->mapel?->urutan ?? 999;
            return sprintf('%04d-%04d', $kmUrutan, $mapelUrutan);
        })->groupBy(function ($na) {
            $km = $na->penugasanMengajar?->mapel?->kelompokMapel;
            if ($km) {
                return $km->kode . '. ' . $km->nama;
            }
            $kelompok = $na->penugasanMengajar?->mapel?->kelompok ?? 'umum';
            if ($kelompok === 'umum') {
                return 'A. Muatan Umum';
            }
            return 'Lainnya';
        });
            
        // Ambil Ekstrakurikuler (Hanya yang berstatus aktif)
        $ekskuls = \App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota::with('ekstrakurikuler')
            ->where('siswa_id', $siswa_id)
            ->where('semester_id', $semester->id)
            ->where(function ($q) {
                $q->whereNull('status_keanggotaan')->orWhere('status_keanggotaan', 'aktif');
            })
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
