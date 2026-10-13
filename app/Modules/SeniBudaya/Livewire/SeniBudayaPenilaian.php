<?php

namespace App\Modules\SeniBudaya\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Modules\SeniBudaya\Models\SeniBudaya;
use App\Modules\SeniBudaya\Models\SeniBudayaAnggota;
use App\Modules\SeniBudaya\Models\SeniBudayaTp;
use App\Modules\SeniBudaya\Services\SeniBudayaService;
use App\Modules\Akademik\Models\Semester;

#[Layout('components.layouts.app')]
#[Title('Penilaian Seni Budaya')]
class SeniBudayaPenilaian extends Component
{
    public int $seni_budaya_id;
    public $cabang;
    public $semester;

    // Periode Penilaian Aktif: STS 1, SAS, STS 2, SAT
    public string $activePeriod = 'SAS';

    // Konfigurasi Bobot & KKM
    public int $bobot_presensi = 30;
    public int $bobot_penilaian = 70;
    public int $kkm = 75;

    // Matriks Penilaian Siswa: [siswa_id => [...]]
    public array $nilaiData = [];

    // Daftar TP untuk Periode Asesmen Aktif ini
    public $tps = [];

    // State Modal Pilih TP per Siswa
    public bool $showTpModal = false;
    public ?int $tpActiveSiswaId = null;
    public string $tpActiveSiswaNama = '';
    public array $selectedTpTercapai = [];
    public array $selectedTpPerluBimbingan = [];

    public string $search = '';

    protected $service;

    public function boot(SeniBudayaService $service)
    {
        $this->service = $service;
    }

    public function mount(int $seni_budaya_id)
    {
        $this->seni_budaya_id = $seni_budaya_id;
        $this->cabang = SeniBudaya::with('guru')->findOrFail($seni_budaya_id);

        $this->bobot_presensi = $this->cabang->bobot_presensi ?? 30;
        $this->bobot_penilaian = $this->cabang->bobot_penilaian ?? 70;
        $this->kkm = $this->cabang->kkm ?? 75;

        $this->semester = Semester::where('is_active', true)->first();
        if ($this->semester && str_contains(strtolower($this->semester->semester), 'genap')) {
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
        $semesterId = $this->semester?->id ?? 0;

        // Pastikan kalkulasi kehadiran terkini
        $this->service->recalculateAttendance($this->seni_budaya_id, $semesterId);

        // Ambil daftar TP yang sesuai dengan target alokasi asesmen periode aktif
        $this->tps = SeniBudayaTp::where('seni_budaya_id', $this->seni_budaya_id)
            ->where('semester_id', $semesterId)
            ->where('is_active', true)
            ->forAsesmen($this->activePeriod)
            ->orderBy('urutan')
            ->get();

        // Jika tidak ada TP spesifik untuk periode ini, ambil semua TP aktif semester ini sebagai fallback
        if ($this->tps->isEmpty()) {
            $this->tps = SeniBudayaTp::where('seni_budaya_id', $this->seni_budaya_id)
                ->where('semester_id', $semesterId)
                ->where('is_active', true)
                ->orderBy('urutan')
                ->get();
        }

        // Ambil anggota aktif
        $anggotas = SeniBudayaAnggota::with('siswa.rombel')
            ->where('seni_budaya_id', $this->seni_budaya_id)
            ->where('semester_id', $semesterId)
            ->where('status_keanggotaan', 'aktif')
            ->get()
            ->sortBy(fn($a) => $a->siswa?->nama);

        $this->nilaiData = [];
        foreach ($anggotas as $a) {
            $asesmen = match ($this->activePeriod) {
                'STS 1' => $a->sts_1,
                'SAS' => $a->sas,
                'STS 2' => $a->sts_2,
                'SAT' => $a->sat,
                default => $a->sas,
            };

            $presensi = (float) ($a->nilai_presensi ?? 100);

            // Ambil evaluasi spesifik periode jika ada
            $tpEvals = $a->tp_evaluations ?? [];
            $periodTp = $tpEvals[$this->activePeriod] ?? null;

            $descEvals = $a->deskripsi_evaluations ?? [];
            $periodDesc = $descEvals[$this->activePeriod] ?? null;

            $tpTercapai = $periodTp['tercapai'] ?? ($a->tp_tercapai_ids ?? []);
            $tpBimbingan = $periodTp['bimbingan'] ?? ($a->tp_perlu_bimbingan_ids ?? []);

            $descTinggi = $periodDesc['deskripsi_tertinggi'] ?? ($a->deskripsi_tertinggi ?? '');
            $descRendah = $periodDesc['deskripsi_terendah'] ?? ($a->deskripsi_terendah ?? '');
            $descManual = $periodDesc['deskripsi_manual'] ?? ($a->deskripsi_manual ?? '');

            // Hitung nilai akhir periode
            $periodFinalScore = match ($this->activePeriod) {
                'STS 1' => $a->nilai_akhir_sts_1,
                'SAS' => $a->nilai_akhir_sas ?? $a->nilai_akhir,
                'STS 2' => $a->nilai_akhir_sts_2,
                'SAT' => $a->nilai_akhir_sat ?? $a->nilai_akhir,
                default => $a->nilai_akhir,
            };

            $predikat = '-';
            if ($asesmen !== null) {
                $calc = $this->service->calculateGrade(
                    $presensi,
                    (float) $asesmen,
                    $this->bobot_presensi,
                    $this->bobot_penilaian,
                    $this->kkm
                );
                if ($periodFinalScore === null) {
                    $periodFinalScore = $calc['nilai_akhir'];
                }
                $predikat = $calc['nilai_huruf'];
            }

            $this->nilaiData[$a->siswa_id] = [
                'anggota_id' => $a->id,
                'nama' => $a->siswa?->nama ?? 'Siswa',
                'rombel' => $a->siswa?->rombel?->nama ?? '-',
                'nisn' => $a->siswa?->nisn ?? '-',
                'nilai_presensi' => $presensi,
                'nilai_asesmen' => $asesmen,
                'nilai_akhir' => $periodFinalScore,
                'nilai_huruf' => $predikat,
                'tp_tercapai_ids' => $tpTercapai,
                'tp_perlu_bimbingan_ids' => $tpBimbingan,
                'deskripsi_tertinggi' => $descTinggi,
                'deskripsi_terendah' => $descRendah,
                'deskripsi_manual' => $descManual,
                'tp_evaluations' => $tpEvals,
                'deskripsi_evaluations' => $descEvals,
            ];
        }
    }

    public function updatedNilaiData($value, $key)
    {
        // $key misal: "12.nilai_asesmen"
        if (str_contains($key, '.nilai_asesmen')) {
            $parts = explode('.', $key);
            $siswaId = (int) $parts[0];

            $val = $this->nilaiData[$siswaId]['nilai_asesmen'];
            $presensi = (float) ($this->nilaiData[$siswaId]['nilai_presensi'] ?? 100);

            if ($val !== '' && $val !== null) {
                $valFloat = min(100, max(0, (float) $val));
                $this->nilaiData[$siswaId]['nilai_asesmen'] = $valFloat;

                $calc = $this->service->calculateGrade(
                    $presensi,
                    $valFloat,
                    $this->bobot_presensi,
                    $this->bobot_penilaian,
                    $this->kkm
                );

                $this->nilaiData[$siswaId]['nilai_akhir'] = $calc['nilai_akhir'];
                $this->nilaiData[$siswaId]['nilai_huruf'] = $calc['nilai_huruf'];
            } else {
                $this->nilaiData[$siswaId]['nilai_akhir'] = null;
                $this->nilaiData[$siswaId]['nilai_huruf'] = '-';
            }
        }
    }

    public function openTpModal(int $siswaId)
    {
        $this->tpActiveSiswaId = $siswaId;
        $this->tpActiveSiswaNama = $this->nilaiData[$siswaId]['nama'] ?? 'Siswa';
        $this->selectedTpTercapai = $this->nilaiData[$siswaId]['tp_tercapai_ids'] ?? [];
        $this->selectedTpPerluBimbingan = $this->nilaiData[$siswaId]['tp_perlu_bimbingan_ids'] ?? [];

        $this->showTpModal = true;
    }

    public function saveTpSelection()
    {
        if (!$this->tpActiveSiswaId) {
            return;
        }

        $sid = $this->tpActiveSiswaId;
        $tercapaiIds = array_values(array_map('intval', $this->selectedTpTercapai));
        $bimbinganIds = array_values(array_map('intval', $this->selectedTpPerluBimbingan));

        $this->nilaiData[$sid]['tp_tercapai_ids'] = $tercapaiIds;
        $this->nilaiData[$sid]['tp_perlu_bimbingan_ids'] = $bimbinganIds;

        // Generate deskripsi otomatis khusus periode ini
        $desc = $this->service->generateDeskripsi(
            $tercapaiIds,
            $bimbinganIds,
            $this->seni_budaya_id
        );

        $this->nilaiData[$sid]['deskripsi_tertinggi'] = $desc['deskripsi_tertinggi'] ?? '';
        $this->nilaiData[$sid]['deskripsi_terendah'] = $desc['deskripsi_terendah'] ?? '';

        // Simpan ke struktur evaluasi per periode
        $this->nilaiData[$sid]['tp_evaluations'][$this->activePeriod] = [
            'tercapai' => $tercapaiIds,
            'bimbingan' => $bimbinganIds,
        ];

        $this->nilaiData[$sid]['deskripsi_evaluations'][$this->activePeriod] = [
            'deskripsi_tertinggi' => $desc['deskripsi_tertinggi'] ?? null,
            'deskripsi_terendah' => $desc['deskripsi_terendah'] ?? null,
            'deskripsi_manual' => $this->nilaiData[$sid]['deskripsi_manual'] ?? null,
        ];

        $this->showTpModal = false;
    }

    public function generateDeskripsiSemua()
    {
        $currentTpIds = $this->tps->pluck('id')->all();

        foreach ($this->nilaiData as $siswaId => $row) {
            $score = $row['nilai_akhir'] ?? $row['nilai_asesmen'];
            if ($score === null) {
                continue;
            }

            if (empty($row['tp_tercapai_ids']) && empty($row['tp_perlu_bimbingan_ids'])) {
                if ($score >= $this->kkm) {
                    $this->nilaiData[$siswaId]['tp_tercapai_ids'] = $currentTpIds;
                    $this->nilaiData[$siswaId]['tp_perlu_bimbingan_ids'] = [];
                } else {
                    $this->nilaiData[$siswaId]['tp_tercapai_ids'] = [];
                    $this->nilaiData[$siswaId]['tp_perlu_bimbingan_ids'] = $currentTpIds;
                }
            }

            $desc = $this->service->generateDeskripsi(
                $this->nilaiData[$siswaId]['tp_tercapai_ids'],
                $this->nilaiData[$siswaId]['tp_perlu_bimbingan_ids'],
                $this->seni_budaya_id
            );

            $this->nilaiData[$siswaId]['deskripsi_tertinggi'] = $desc['deskripsi_tertinggi'] ?? '';
            $this->nilaiData[$siswaId]['deskripsi_terendah'] = $desc['deskripsi_terendah'] ?? '';

            // Update struktur evaluasi per periode
            $this->nilaiData[$siswaId]['tp_evaluations'][$this->activePeriod] = [
                'tercapai' => $this->nilaiData[$siswaId]['tp_tercapai_ids'],
                'bimbingan' => $this->nilaiData[$siswaId]['tp_perlu_bimbingan_ids'],
            ];

            $this->nilaiData[$siswaId]['deskripsi_evaluations'][$this->activePeriod] = [
                'deskripsi_tertinggi' => $desc['deskripsi_tertinggi'] ?? null,
                'deskripsi_terendah' => $desc['deskripsi_terendah'] ?? null,
                'deskripsi_manual' => $row['deskripsi_manual'] ?? null,
            ];
        }

        session()->flash('message', "Deskripsi capaian {$this->activePeriod} seluruh siswa berhasil di-generate secara otomatis.");
    }

    public function saveAll()
    {
        foreach ($this->nilaiData as $siswaId => $row) {
            $anggota = SeniBudayaAnggota::find($row['anggota_id']);
            if (!$anggota) {
                continue;
            }

            $scoreAsesmen = $row['nilai_asesmen'] !== '' && $row['nilai_asesmen'] !== null ? (float) $row['nilai_asesmen'] : null;
            $scoreAkhir = $row['nilai_akhir'] !== '' && $row['nilai_akhir'] !== null ? (float) $row['nilai_akhir'] : null;

            // Simpan riwayat evaluasi spesifik periode
            $tpEvals = $row['tp_evaluations'] ?? ($anggota->tp_evaluations ?? []);
            $tpEvals[$this->activePeriod] = [
                'tercapai' => $row['tp_tercapai_ids'] ?? [],
                'bimbingan' => $row['tp_perlu_bimbingan_ids'] ?? [],
            ];

            $descEvals = $row['deskripsi_evaluations'] ?? ($anggota->deskripsi_evaluations ?? []);
            $descEvals[$this->activePeriod] = [
                'deskripsi_tertinggi' => $row['deskripsi_tertinggi'] ?: null,
                'deskripsi_terendah' => $row['deskripsi_terendah'] ?: null,
                'deskripsi_manual' => $row['deskripsi_manual'] ?: null,
            ];

            $updateData = [
                'tp_evaluations' => $tpEvals,
                'deskripsi_evaluations' => $descEvals,
                'deskripsi_manual' => $row['deskripsi_manual'] ?: null,
            ];

            // Tentukan field kolom target sesuai periode aktif
            if ($this->activePeriod === 'STS 1') {
                $updateData['sts_1'] = $scoreAsesmen;
                $updateData['nilai_akhir_sts_1'] = $scoreAkhir;
                // Jika belum ada nilai akhir umum, gunakan ini sebagai fallback
                if ($anggota->nilai_akhir === null) {
                    $updateData['nilai_akhir'] = $scoreAkhir;
                    $updateData['nilai_huruf'] = $row['nilai_huruf'];
                    $updateData['deskripsi_tertinggi'] = $row['deskripsi_tertinggi'];
                    $updateData['deskripsi_terendah'] = $row['deskripsi_terendah'];
                }
            } elseif ($this->activePeriod === 'SAS') {
                $updateData['sas'] = $scoreAsesmen;
                $updateData['nilai_akhir_sas'] = $scoreAkhir;
                $updateData['nilai_akhir'] = $scoreAkhir;
                $updateData['nilai_huruf'] = $row['nilai_huruf'];
                $updateData['deskripsi_tertinggi'] = $row['deskripsi_tertinggi'];
                $updateData['deskripsi_terendah'] = $row['deskripsi_terendah'];
            } elseif ($this->activePeriod === 'STS 2') {
                $updateData['sts_2'] = $scoreAsesmen;
                $updateData['nilai_akhir_sts_2'] = $scoreAkhir;
                if ($anggota->nilai_akhir === null) {
                    $updateData['nilai_akhir'] = $scoreAkhir;
                    $updateData['nilai_huruf'] = $row['nilai_huruf'];
                    $updateData['deskripsi_tertinggi'] = $row['deskripsi_tertinggi'];
                    $updateData['deskripsi_terendah'] = $row['deskripsi_terendah'];
                }
            } elseif ($this->activePeriod === 'SAT') {
                $updateData['sat'] = $scoreAsesmen;
                $updateData['nilai_akhir_sat'] = $scoreAkhir;
                $updateData['nilai_akhir'] = $scoreAkhir;
                $updateData['nilai_huruf'] = $row['nilai_huruf'];
                $updateData['deskripsi_tertinggi'] = $row['deskripsi_tertinggi'];
                $updateData['deskripsi_terendah'] = $row['deskripsi_terendah'];
            }

            $anggota->update($updateData);
        }

        session()->flash('message', "Seluruh penilaian dan capaian rapor {$this->activePeriod} berhasil disimpan.");
    }

    public function render()
    {
        return view('senibudaya::livewire.penilaian');
    }
}
