<?php

namespace App\Modules\Ekstrakurikuler\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Modules\Ekstrakurikuler\Models\Ekstrakurikuler;
use App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota;
use App\Modules\Ekstrakurikuler\Services\EkstrakurikulerService;
use App\Modules\Ekstrakurikuler\Services\EkskulExcelService;
use App\Modules\Akademik\Models\Semester;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EkstrakurikulerPenilaian extends Component
{
    public $ekskul_id;
    public $ekskul;
    public $semester_id;
    public $semester;

    // Periode Penilaian: STS 1, SAS, STS 2, SAT
    public $activePeriod = 'SAS';

    // Konfigurasi Bobot & Threshold Predikat (Bisa Diedit Langsung)
    public $bobot_presensi = 30;
    public $bobot_penilaian = 70;
    public $threshold_a = 90;
    public $threshold_b = 80;
    public $threshold_c = 70;

    // [siswa_id => [ 'nilai_asesmen', 'nilai_presensi', 'nilai_akhir', 'nilai_huruf', 'deskripsi' ]]
    public $nilaiData = [];

    // Filter
    public $search = '';

    protected $service;
    protected $excelService;

    public function boot(EkstrakurikulerService $service, EkskulExcelService $excelService)
    {
        $this->service = $service;
        $this->excelService = $excelService;
    }

    public function mount($ekskul_id)
    {
        $this->ekskul_id = $ekskul_id;
        $this->ekskul = Ekstrakurikuler::with(['pembina', 'instrukturUser'])->findOrFail($ekskul_id);

        $this->bobot_presensi = $this->ekskul->bobot_presensi ?? 30;
        $this->bobot_penilaian = $this->ekskul->bobot_penilaian ?? 70;
        $this->threshold_a = $this->ekskul->threshold_a ?? 90;
        $this->threshold_b = $this->ekskul->threshold_b ?? 80;
        $this->threshold_c = $this->ekskul->threshold_c ?? 70;

        $activeSemester = Semester::with('tahunAjaran')->where('is_active', true)->first();
        if ($activeSemester) {
            $this->semester_id = $activeSemester->id;
            $this->semester = $activeSemester;
        } else {
            $this->semester = Semester::with('tahunAjaran')->latest('id')->first();
            $this->semester_id = $this->semester?->id;
        }

        // Tentukan periode default berdasarkan semester aktif
        if ($this->semester && $this->semester->semester === 'genap') {
            $this->activePeriod = 'SAT';
        } else {
            $this->activePeriod = 'SAS';
        }

        $this->loadData();
    }

    public function updatedActivePeriod()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Hitung ulang akumulasi kehadiran terbaru terlebih dahulu
        $this->service->recalculateAttendance($this->ekskul_id, $this->semester_id);

        $anggotas = EkstrakurikulerAnggota::with('siswa.rombel')
            ->where('ekstrakurikuler_id', $this->ekskul_id)
            ->where('semester_id', $this->semester_id)
            ->where('status_keanggotaan', 'aktif')
            ->get();

        $this->nilaiData = [];
        foreach ($anggotas as $a) {
            // Ambil asesmen sesuai activePeriod
            $asesmen = null;
            if ($this->activePeriod === 'STS 1') {
                $asesmen = $a->sts_1;
            } elseif ($this->activePeriod === 'SAS') {
                $asesmen = $a->sas;
            } elseif ($this->activePeriod === 'STS 2') {
                $asesmen = $a->sts_2;
            } elseif ($this->activePeriod === 'SAT') {
                $asesmen = $a->sat;
            }

            // Jika belum ada nilai di periode ini tapi sudah ada nilai_angka
            if ($asesmen === null && $a->nilai_angka !== null) {
                $asesmen = $a->nilai_angka;
            }

            $presensi = $a->nilai_presensi ?? $a->persentase_kehadiran ?? 100.0;

            // Hitung nilai akhir awal
            $finalScore = null;
            $predikat = $a->nilai_huruf ?? '-';
            $deskripsi = $a->deskripsi ?? '';

            if ($asesmen !== null) {
                $calc = $this->calculateGrade($presensi, (float)$asesmen);
                $finalScore = $calc['finalScore'];
                $predikat = $calc['predikat'];
                if (empty($deskripsi)) {
                    $deskripsi = $calc['deskripsi'];
                }
            }

            $this->nilaiData[$a->siswa_id] = [
                'anggota_id' => $a->id,
                'nama' => $a->siswa?->nama ?? 'Siswa',
                'rombel' => $a->siswa?->rombel?->nama ?? '-',
                'nisn' => $a->siswa?->nisn ?? '-',
                'nilai_presensi' => $presensi,
                'nilai_asesmen' => $asesmen,
                'nilai_akhir' => $finalScore,
                'nilai_huruf' => $predikat,
                'deskripsi' => $deskripsi,
            ];
        }
    }

    public function updatedNilaiData($value, $key)
    {
        // $key format: "123.nilai_asesmen"
        if (str_contains($key, '.nilai_asesmen')) {
            $parts = explode('.', $key);
            $siswaId = $parts[0];

            $val = $this->nilaiData[$siswaId]['nilai_asesmen'];
            $presensi = $this->nilaiData[$siswaId]['nilai_presensi'] ?? 100;

            if ($val !== '' && $val !== null) {
                $valFloat = min(100, max(0, (float) $val));
                $this->nilaiData[$siswaId]['nilai_asesmen'] = $valFloat;

                $calc = $this->calculateGrade($presensi, $valFloat);
                $this->nilaiData[$siswaId]['nilai_akhir'] = $calc['finalScore'];
                $this->nilaiData[$siswaId]['nilai_huruf'] = $calc['predikat'];

                if (empty($this->nilaiData[$siswaId]['deskripsi'])) {
                    $this->nilaiData[$siswaId]['deskripsi'] = $calc['deskripsi'];
                }
            } else {
                $this->nilaiData[$siswaId]['nilai_akhir'] = null;
                $this->nilaiData[$siswaId]['nilai_huruf'] = '-';
            }
        }
    }

    public function calculateGrade(float $presensi, float $asesmen): array
    {
        $finalScore = round(
            (($presensi * $this->bobot_presensi) / 100) + (($asesmen * $this->bobot_penilaian) / 100),
            2
        );

        if ($finalScore >= $this->threshold_a) {
            $predikat = 'Sangat Baik';
            $deskripsi = "Menunjukkan penguasaan teknik dan keaktifan yang sangat baik dalam kegiatan {$this->ekskul->nama}.";
        } elseif ($finalScore >= $this->threshold_b) {
            $predikat = 'Baik';
            $deskripsi = "Menunjukkan partisipasi dan penguasaan teknik yang baik dalam kegiatan {$this->ekskul->nama}.";
        } elseif ($finalScore >= $this->threshold_c) {
            $predikat = 'Cukup';
            $deskripsi = "Cukup aktif dalam kegiatan {$this->ekskul->nama}. Perlu meningkatkan kedisiplinan dan teknik latihan.";
        } else {
            $predikat = 'Kurang';
            $deskripsi = "Perlu bimbingan dan peningkatan kehadiran serta komitmen dalam kegiatan {$this->ekskul->nama}.";
        }

        return [
            'finalScore' => $finalScore,
            'predikat' => $predikat,
            'deskripsi' => $deskripsi,
        ];
    }

    public function generateDeskripsiSemua()
    {
        foreach ($this->nilaiData as $siswaId => $row) {
            if ($row['nilai_asesmen'] !== null && $row['nilai_asesmen'] !== '') {
                $calc = $this->calculateGrade((float)$row['nilai_presensi'], (float)$row['nilai_asesmen']);
                $this->nilaiData[$siswaId]['deskripsi'] = $calc['deskripsi'];
            }
        }

        session()->flash('message', 'Deskripsi capaian rapor berhasil di-generate otomatis untuk seluruh siswa.');
    }

    public function saveNilai()
    {
        $savedCount = 0;

        foreach ($this->nilaiData as $siswaId => $row) {
            $asesmen = $row['nilai_asesmen'];
            if ($asesmen === '' || $asesmen === null) {
                continue;
            }

            $asesmenFloat = min(100, max(0, (float) $asesmen));
            $calc = $this->calculateGrade((float)$row['nilai_presensi'], $asesmenFloat);

            $updateData = [
                'nilai_angka' => $calc['finalScore'],
                'nilai_huruf' => $calc['predikat'],
                'deskripsi' => $row['deskripsi'] ?: $calc['deskripsi'],
            ];

            // Simpan ke kolom periode spesifik
            if ($this->activePeriod === 'STS 1') {
                $updateData['sts_1'] = $asesmenFloat;
            } elseif ($this->activePeriod === 'SAS') {
                $updateData['sas'] = $asesmenFloat;
            } elseif ($this->activePeriod === 'STS 2') {
                $updateData['sts_2'] = $asesmenFloat;
            } elseif ($this->activePeriod === 'SAT') {
                $updateData['sat'] = $asesmenFloat;
            }

            EkstrakurikulerAnggota::where('ekstrakurikuler_id', $this->ekskul_id)
                ->where('siswa_id', $siswaId)
                ->where('semester_id', $this->semester_id)
                ->update($updateData);

            $savedCount++;
        }

        session()->flash('message', "Nilai {$this->activePeriod} untuk {$savedCount} siswa berhasil disimpan dan tersinkronisasi ke Rapor.");
        $this->loadData();
    }

    public function exportExcel()
    {
        $spreadsheet = $this->excelService->exportRekap($this->ekskul_id, $this->semester_id);
        $fileName = 'Rekap_Nilai_' . str_replace(' ', '_', $this->ekskul->nama) . '_' . date('Ymd') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $filteredList = collect($this->nilaiData);
        if ($this->search) {
            $q = strtolower($this->search);
            $filteredList = $filteredList->filter(function ($item) use ($q) {
                return str_contains(strtolower($item['nama']), $q)
                    || str_contains(strtolower($item['rombel']), $q)
                    || str_contains(strtolower($item['nisn']), $q);
            });
        }

        return view('ekstrakurikuler::livewire.penilaian', [
            'filteredList' => $filteredList,
            'totalSiswa' => count($this->nilaiData),
        ]);
    }
}
