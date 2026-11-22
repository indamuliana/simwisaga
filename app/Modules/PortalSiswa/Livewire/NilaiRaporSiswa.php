<?php

namespace App\Modules\PortalSiswa\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\MapelPilihanTp;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Models\NilaiAsesmenHarian;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\NilaiHarian;
use App\Modules\Penilaian\Models\NilaiMapelPilihanAkhir;
use App\Modules\Penilaian\Models\NilaiMapelPilihanFormatif;
use App\Modules\Penilaian\Models\NilaiMapelPilihanHarian;
use App\Modules\Penilaian\Models\NilaiMapelPilihanSumatif;
use App\Modules\Penilaian\Models\NilaiSumatif;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use App\Modules\Penilaian\Services\TranskripNilaiService;
use App\Modules\Rapor\Models\CatatanWaliKelas;
use App\Modules\Rapor\Services\RaporScoreService;
use App\Modules\Sistem\Models\PeriodeRapor;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Ledger Nilai & Rapor Siswa')]
class NilaiRaporSiswa extends Component
{
    use GuardsPermission;

    public string $viewMode = 'ledger'; // 'ledger', 'rapor_wikrama', 'rapor_pemerintah', 'transkrip'

    public ?int $selectedSemesterId = null;

    // Filter Periode 9 Minggu (9M1, 9M2, 9M3, 9M4)
    public string $selectedPeriode9M = '9M1';

    // State Modal Detail Nilai
    public bool $showDetailModal = false;

    public ?array $selectedDetail = null;

    public function mount(): void
    {
        $this->guard('portal_siswa.nilai.lihat');

        $activeSemester = Semester::where('is_active', true)->first();
        $this->selectedSemesterId = $activeSemester?->id;
        if ($activeSemester) {
            $this->selectedPeriode9M = ($activeSemester->semester === 'genap') ? '9M3' : '9M1';
        }
    }

    public function updatedSelectedSemesterId($val): void
    {
        $sem = Semester::find($val);
        if ($sem) {
            $this->selectedPeriode9M = ($sem->semester === 'genap') ? '9M3' : '9M1';
        }
    }

    protected function getSiswa(): ?Siswa
    {
        $user = auth()->user();
        if (! $user) {
            return null;
        }

        if ($user->siswa) {
            return $user->siswa;
        }

        return Siswa::where('user_id', $user->id)
            ->orWhere('nisn', $user->username)
            ->orWhere('nipd', $user->username)
            ->first();
    }

    public function openDetailModal(string $type, int $id): void
    {
        $siswa = $this->getSiswa();
        if (! $siswa) {
            return;
        }

        if ($type === 'regular') {
            $na = NilaiAkhir::with([
                'penugasanMengajar.mapel.kelompokMapel',
                'penugasanMengajar.pegawai',
            ])->find($id);

            if ($na && $na->penugasanMengajar) {
                $pm = $na->penugasanMengajar;
                $mapel = $pm->mapel;
                $kkm = (int) ($mapel?->kkm_default ?? 75);

                $tps = TujuanPembelajaran::where('penugasan_mengajar_id', $pm->id)
                    ->orderBy('urutan')
                    ->get();

                $formatifs = NilaiFormatif::where('siswa_id', $siswa->id)
                    ->whereIn('tujuan_pembelajaran_id', $tps->pluck('id'))
                    ->get()
                    ->keyBy('tujuan_pembelajaran_id');

                $tpList = [];
                foreach ($tps as $tp) {
                    $nilai = $formatifs->has($tp->id) ? (float) $formatifs->get($tp->id)->nilai : null;
                    $tpList[] = [
                        'kode' => $tp->kode,
                        'target_periode' => $tp->target_periode ?: '-',
                        'alokasi_ah' => $tp->alokasi_ah ?: '-',
                        'deskripsi' => $tp->deskripsi,
                        'nilai' => $nilai,
                        'is_remedial' => $nilai !== null && $nilai < $kkm,
                    ];
                }

                $harians = NilaiHarian::where('siswa_id', $siswa->id)
                    ->where('penugasan_mengajar_id', $pm->id)
                    ->orderBy('target_periode')
                    ->get()
                    ->map(fn ($item) => [
                        'target_periode' => $item->target_periode,
                        'nilai' => (float) $item->nilai,
                    ])->all();

                $asesmenHarians = NilaiAsesmenHarian::where('siswa_id', $siswa->id)
                    ->where('penugasan_mengajar_id', $pm->id)
                    ->orderBy('kode_ah')
                    ->get()
                    ->map(fn ($item) => [
                        'kode_ah' => $item->kode_ah,
                        'nilai' => (float) $item->nilai,
                    ])->all();

                $sumatif = NilaiSumatif::where('siswa_id', $siswa->id)
                    ->where('penugasan_mengajar_id', $pm->id)
                    ->first();

                $this->selectedDetail = [
                    'type' => 'regular',
                    'mapel_nama' => $mapel?->nama,
                    'mapel_kode' => $mapel?->kode,
                    'kelompok' => $mapel?->kelompokMapel?->nama ?: 'Muatan Umum',
                    'guru' => $pm->pegawai?->nama_gelar ?: ($pm->pegawai?->nama_lengkap ?: '-'),
                    'kkm' => $kkm,
                    'nilai_akhir' => (float) $na->nilai_akhir,
                    'predikat' => TranskripNilaiService::getPredikat((float) $na->nilai_akhir),
                    'is_remedial' => (float) $na->nilai_akhir < $kkm,
                    'formatif_tps' => $tpList,
                    'nilai_harians' => $harians,
                    'asesmen_harians' => $asesmenHarians,
                    'sts' => $sumatif?->sts !== null ? (float) $sumatif->sts : null,
                    'sas' => $sumatif?->sas !== null ? (float) $sumatif->sas : null,
                    'deskripsi_tertinggi' => $na->capaian_kompetensi,
                    'deskripsi_terendah' => null,
                ];
                $this->showDetailModal = true;
            }
        } elseif ($type === 'mapel_pilihan') {
            $nmpa = NilaiMapelPilihanAkhir::with(['mapelPilihan.guru'])->find($id);
            if ($nmpa && $nmpa->mapelPilihan) {
                $mp = $nmpa->mapelPilihan;
                $kkm = (int) ($mp->kkm_default ?? 75);

                $tps = MapelPilihanTp::where('mapel_pilihan_id', $mp->id)
                    ->orderBy('urutan')
                    ->get();

                $formatifs = NilaiMapelPilihanFormatif::where('siswa_id', $siswa->id)
                    ->whereIn('mapel_pilihan_tp_id', $tps->pluck('id'))
                    ->get()
                    ->keyBy('mapel_pilihan_tp_id');

                $tpList = [];
                foreach ($tps as $tp) {
                    $nilai = $formatifs->has($tp->id) ? (float) $formatifs->get($tp->id)->nilai : null;
                    $tpList[] = [
                        'kode' => $tp->kode,
                        'target_periode' => $tp->target_periode ?: '-',
                        'alokasi_ah' => '-',
                        'deskripsi' => $tp->deskripsi,
                        'nilai' => $nilai,
                        'is_remedial' => $nilai !== null && $nilai < $kkm,
                    ];
                }

                $harians = NilaiMapelPilihanHarian::where('siswa_id', $siswa->id)
                    ->where('mapel_pilihan_id', $mp->id)
                    ->orderBy('target_periode')
                    ->get()
                    ->map(fn ($item) => [
                        'target_periode' => $item->target_periode,
                        'nilai' => (float) $item->nilai,
                    ])->all();

                $sumatif = NilaiMapelPilihanSumatif::where('siswa_id', $siswa->id)
                    ->where('mapel_pilihan_id', $mp->id)
                    ->first();

                $this->selectedDetail = [
                    'type' => 'mapel_pilihan',
                    'mapel_nama' => $mp->nama,
                    'mapel_kode' => $mp->kode,
                    'kelompok' => 'Mata Pelajaran Pilihan',
                    'guru' => $mp->guru?->nama_gelar ?: ($mp->guru?->nama_lengkap ?: '-'),
                    'kkm' => $kkm,
                    'nilai_akhir' => (float) $nmpa->nilai_akhir,
                    'predikat' => $nmpa->predikat ?: TranskripNilaiService::getPredikat((float) $nmpa->nilai_akhir),
                    'is_remedial' => (float) $nmpa->nilai_akhir < $kkm,
                    'formatif_tps' => $tpList,
                    'nilai_harians' => $harians,
                    'asesmen_harians' => [],
                    'sts' => $sumatif?->sts !== null ? (float) $sumatif->sts : null,
                    'sas' => $sumatif?->sas !== null ? (float) $sumatif->sas : null,
                    'deskripsi_tertinggi' => $nmpa->deskripsi_tertinggi,
                    'deskripsi_terendah' => $nmpa->deskripsi_terendah,
                ];
                $this->showDetailModal = true;
            }
        }
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedDetail = null;
    }

    public function render()
    {
        $siswa = $this->getSiswa();

        $semesters = Semester::with('tahunAjaran')
            ->orderByDesc('id')
            ->get();

        $selectedSemester = $this->selectedSemesterId
            ? $semesters->firstWhere('id', $this->selectedSemesterId)
            : $semesters->first();

        $historicalRombel = null;
        $historicalWaliKelas = null;

        $ledgerRows = [];
        $summary = [
            'total_mapel' => 0,
            'tuntas_count' => 0,
            'remedial_count' => 0,
            'rata_rata' => 0,
            'nilai_tertinggi' => 0,
            'nilai_terendah' => 100,
        ];

        $catatanWali = null;
        $raporWikramaRecords = [];
        $raporPemerintahRecords = [];
        $transkripData = [];

        if ($siswa && $selectedSemester) {
            $siswa->loadMissing(['rombel.jurusan', 'rombel.waliKelas', 'rayon.pembimbing', 'riwayatRombel.rombel.waliKelas']);

            // 1. Tentukan Rombel Historis (karena siswa naik kelas setiap tahun)
            $rr = $siswa->riwayatRombel->firstWhere('tahun_ajaran_id', $selectedSemester->tahun_ajaran_id);
            if ($rr && $rr->rombel) {
                $historicalRombel = $rr->rombel;
            } else {
                $historicalRombel = $siswa->rombel;
            }
            $historicalWaliKelas = $historicalRombel?->waliKelas;

            // 2. Ambil Nilai Akhir untuk semester ini (Mapel Reguler)
            $naRecords = NilaiAkhir::with([
                'penugasanMengajar.mapel.kelompokMapel',
                'penugasanMengajar.pegawai',
                'penugasanMengajar.tujuanPembelajarans',
                'penugasanMengajar.rombel',
            ])
                ->where('siswa_id', $siswa->id)
                ->whereHas('penugasanMengajar', function ($q) use ($selectedSemester) {
                    $q->where('semester_id', $selectedSemester->id);
                })
                ->get();

            // Fallback historical rombel jika didapatkan dari penugasan mengajar nilai siswa
            if (! $historicalRombel && $naRecords->isNotEmpty()) {
                $firstPm = $naRecords->first()->penugasanMengajar;
                if ($firstPm && $firstPm->rombel) {
                    $historicalRombel = $firstPm->rombel;
                    $historicalWaliKelas = $firstPm->rombel->waliKelas;
                }
            }

            // Ambil semua Nilai Formatif per TP (Reguler)
            $pmIds = $naRecords->pluck('penugasan_mengajar_id')->filter()->all();

            $formatifs = NilaiFormatif::with('tujuanPembelajaran')
                ->where('siswa_id', $siswa->id)
                ->whereHas('tujuanPembelajaran', function ($q) use ($pmIds) {
                    $q->whereIn('penugasan_mengajar_id', $pmIds);
                })
                ->get()
                ->groupBy(fn ($item) => $item->tujuanPembelajaran?->penugasan_mengajar_id);

            // Ambil semua Nilai Sumatif (STS & SAS)
            $sumatifs = NilaiSumatif::where('siswa_id', $siswa->id)
                ->whereIn('penugasan_mengajar_id', $pmIds)
                ->get()
                ->keyBy('penugasan_mengajar_id');

            // Susun baris buku ledger (Mapel Reguler)
            $totalNilai = 0;
            foreach ($naRecords as $na) {
                $pm = $na->penugasanMengajar;
                $mapel = $pm?->mapel;
                if (! $mapel) {
                    continue;
                }

                $kkm = (int) ($mapel->kkm_default ?? 75);
                $skorAkhir = (float) $na->nilai_akhir;

                // Formatif per TP
                $tpList = [];
                $tpFormatifs = $formatifs->get($pm->id, collect());
                $formatifScores = [];
                foreach ($tpFormatifs as $tf) {
                    $tpScore = (float) $tf->nilai;
                    $formatifScores[] = $tpScore;
                    $tpList[] = [
                        'kode' => $tf->tujuanPembelajaran?->kode ?: 'TP',
                        'deskripsi' => $tf->tujuanPembelajaran?->deskripsi ?: 'Tujuan Pembelajaran',
                        'nilai' => $tpScore,
                        'is_remedial' => $tpScore < $kkm,
                    ];
                }
                $avgFormatif = ! empty($formatifScores) ? round(array_sum($formatifScores) / count($formatifScores), 1) : (float) ($na->nilai_formatif ?? 0);

                // Sumatif
                $sumatifItem = $sumatifs->get($pm->id);
                $sts = $sumatifItem?->sts !== null ? (float) $sumatifItem->sts : null;
                $sas = $sumatifItem?->sas !== null ? (float) $sumatifItem->sas : null;

                $isRemedial = $skorAkhir < $kkm;
                $predikat = TranskripNilaiService::getPredikat($skorAkhir);
                $tpRemedial = collect($tpList)->where('is_remedial', true)->values()->all();

                $ledgerRows[] = [
                    'id' => $na->id,
                    'type' => 'regular',
                    'mapel_id' => $mapel->id,
                    'mapel_nama' => $mapel->nama,
                    'kelompok' => $mapel->kelompokMapel?->nama ?: 'Muatan Umum',
                    'guru' => $pm->pegawai?->nama_gelar ?? ($pm->pegawai?->nama_lengkap ?? '-'),
                    'kkm' => $kkm,
                    'avg_formatif' => $avgFormatif,
                    'formatif_tps' => $tpList,
                    'sts' => $sts,
                    'sas' => $sas,
                    'nilai_akhir' => $skorAkhir,
                    'predikat' => $predikat,
                    'is_remedial' => $isRemedial,
                    'tp_remedial' => $tpRemedial,
                    'capaian' => $na->capaian_kompetensi,
                ];

                $totalNilai += $skorAkhir;
                if ($isRemedial) {
                    $summary['remedial_count']++;
                } else {
                    $summary['tuntas_count']++;
                }

                if ($skorAkhir > $summary['nilai_tertinggi']) {
                    $summary['nilai_tertinggi'] = $skorAkhir;
                }
                if ($skorAkhir < $summary['nilai_terendah']) {
                    $summary['nilai_terendah'] = $skorAkhir;
                }
            }

            // 3. Tambahkan Nilai Mapel Pilihan untuk semester ini
            $mapilAkhirs = NilaiMapelPilihanAkhir::with(['mapelPilihan.guru'])
                ->where('siswa_id', $siswa->id)
                ->where('semester_id', $selectedSemester->id)
                ->get();

            foreach ($mapilAkhirs as $mpa) {
                $mp = $mpa->mapelPilihan;
                if (! $mp) {
                    continue;
                }

                $kkm = (int) ($mp->kkm_default ?? 75);
                $skorAkhir = (float) $mpa->nilai_akhir;

                // Formatif TP Mapel Pilihan
                $tps = MapelPilihanTp::where('mapel_pilihan_id', $mp->id)->orderBy('urutan')->get();
                $mpFormatifs = NilaiMapelPilihanFormatif::where('siswa_id', $siswa->id)
                    ->whereIn('mapel_pilihan_tp_id', $tps->pluck('id'))
                    ->get()
                    ->keyBy('mapel_pilihan_tp_id');

                $tpList = [];
                $formatifScores = [];
                foreach ($tps as $tp) {
                    if ($mpFormatifs->has($tp->id)) {
                        $tpScore = (float) $mpFormatifs->get($tp->id)->nilai;
                        $formatifScores[] = $tpScore;
                        $tpList[] = [
                            'kode' => $tp->kode,
                            'deskripsi' => $tp->deskripsi,
                            'nilai' => $tpScore,
                            'is_remedial' => $tpScore < $kkm,
                        ];
                    }
                }
                $avgFormatif = ! empty($formatifScores) ? round(array_sum($formatifScores) / count($formatifScores), 1) : 0;

                // Sumatif Mapel Pilihan
                $sumatifItem = NilaiMapelPilihanSumatif::where('siswa_id', $siswa->id)
                    ->where('mapel_pilihan_id', $mp->id)
                    ->first();
                $sts = $sumatifItem?->sts !== null ? (float) $sumatifItem->sts : null;
                $sas = $sumatifItem?->sas !== null ? (float) $sumatifItem->sas : null;

                $isRemedial = $skorAkhir < $kkm;
                $predikat = $mpa->predikat ?: TranskripNilaiService::getPredikat($skorAkhir);
                $tpRemedial = collect($tpList)->where('is_remedial', true)->values()->all();

                $ledgerRows[] = [
                    'id' => $mpa->id,
                    'type' => 'mapel_pilihan',
                    'mapel_id' => 90000 + $mp->id,
                    'mapel_nama' => $mp->nama,
                    'kelompok' => 'Mata Pelajaran Pilihan',
                    'guru' => $mp->guru?->nama_gelar ?? ($mp->guru?->nama_lengkap ?? '-'),
                    'kkm' => $kkm,
                    'avg_formatif' => $avgFormatif,
                    'formatif_tps' => $tpList,
                    'sts' => $sts,
                    'sas' => $sas,
                    'nilai_akhir' => $skorAkhir,
                    'predikat' => $predikat,
                    'is_remedial' => $isRemedial,
                    'tp_remedial' => $tpRemedial,
                    'capaian' => $mpa->deskripsi_tertinggi,
                ];

                $totalNilai += $skorAkhir;
                if ($isRemedial) {
                    $summary['remedial_count']++;
                } else {
                    $summary['tuntas_count']++;
                }

                if ($skorAkhir > $summary['nilai_tertinggi']) {
                    $summary['nilai_tertinggi'] = $skorAkhir;
                }
                if ($skorAkhir < $summary['nilai_terendah']) {
                    $summary['nilai_terendah'] = $skorAkhir;
                }
            }

            $summary['total_mapel'] = count($ledgerRows);
            if ($summary['total_mapel'] > 0) {
                $summary['rata_rata'] = round($totalNilai / $summary['total_mapel'], 2);
            } else {
                $summary['nilai_terendah'] = 0;
            }

            // Catatan Wali Kelas
            $catatanWali = CatatanWaliKelas::where('siswa_id', $siswa->id)
                ->where('semester_id', $selectedSemester->id)
                ->first();

            // Opsi Periode 9 Minggu sesuai semester yang dipilih
            $isGenap = ($selectedSemester->semester ?? 'ganjil') === 'genap';
            $availablePeriodes9M = $isGenap
                ? ['9M3' => '9 Minggu ke-3 (STS 2)', '9M4' => '9 Minggu ke-4 (SAT)']
                : ['9M1' => '9 Minggu ke-1 (STS 1)', '9M2' => '9 Minggu ke-2 (SAS)'];

            if (! array_key_exists($this->selectedPeriode9M, $availablePeriodes9M)) {
                $this->selectedPeriode9M = array_key_first($availablePeriodes9M);
            }

            // Cari ID Periode Rapor untuk parameter tombol cetak
            $matchingPeriode = PeriodeRapor::where('tahun_ajaran_id', $selectedSemester->tahun_ajaran_id)
                ->where(function ($q) {
                    if ($this->selectedPeriode9M === '9M1') {
                        $q->where('kode', 'like', '%PTS%')->where('semester', 'ganjil');
                    } elseif ($this->selectedPeriode9M === '9M2') {
                        $q->where('kode', 'like', '%PAS%')->where('semester', 'ganjil');
                    } elseif ($this->selectedPeriode9M === '9M3') {
                        $q->where('kode', 'like', '%PTS%')->where('semester', 'genap');
                    } elseif ($this->selectedPeriode9M === '9M4') {
                        $q->where('kode', 'like', '%PAS%')->where('semester', 'genap');
                    }
                })->first();
            $matchingPeriodeId = $matchingPeriode?->id;

            // Data Pratinjau Rapor (Normalisasi objek stdClass ke Array)
            try {
                $raporService = app(RaporScoreService::class);
                if ($this->viewMode === 'rapor_wikrama') {
                    $rawWikrama = $raporService->getWikramaAcademicRecords($siswa, $selectedSemester, $this->selectedPeriode9M);
                    $raporWikramaRecords = collect($rawWikrama)->map(function ($rec) {
                        return [
                            'mapel_nama' => $rec->mapel_nama ?? ($rec->mapel?->nama ?? '-'),
                            'kelompok' => $rec->kelompok_nama ?? ($rec->kelompok?->nama ?? 'Muatan Umum'),
                            'kkm' => $rec->kkm ?? 75,
                            'rata_tp' => $rec->rata_tp ?? null,
                            'nh' => $rec->nh ?? null,
                            'sumatif' => $rec->sumatif ?? null,
                            'nilai_akhir' => (float) ($rec->nilai_akhir ?? 0),
                            'predikat' => $rec->predikat ?? '-',
                            'status' => $rec->status ?? 'kompeten',
                            'narasi_capaian' => $rec->narasi_capaian ?? ($rec->deskripsi_tertinggi ?? 'Mencapai kompetensi pembelajaran dengan baik.'),
                            'tps' => $rec->tps ?? [],
                        ];
                    })->all();
                } elseif ($this->viewMode === 'rapor_pemerintah') {
                    $rawPemerintah = $raporService->getPemerintahAcademicRecords($siswa, $selectedSemester);
                    $raporPemerintahRecords = collect($rawPemerintah)->map(function ($rec) {
                        return [
                            'mapel_nama' => $rec->mapel_nama ?? ($rec->mapel?->nama ?? '-'),
                            'kelompok' => $rec->kelompok_nama ?? ($rec->kelompok?->nama ?? 'Muatan Umum'),
                            'kkm' => $rec->kkm ?? 75,
                            'nilai_akhir' => (float) ($rec->nilai_akhir ?? 0),
                            'predikat' => $rec->predikat ?? '-',
                            'deskripsi_tertinggi' => $rec->deskripsi_tertinggi ?? null,
                            'deskripsi_terendah' => $rec->deskripsi_terendah ?? null,
                            'capaian_kompetensi' => $rec->capaian_kompetensi ?? ($rec->deskripsi_tertinggi ?? 'Mencapai kompetensi pembelajaran sesuai standar.'),
                        ];
                    })->all();
                } elseif ($this->viewMode === 'transkrip') {
                    $transkripService = app(TranskripNilaiService::class);
                    $transkripData = $transkripService->getSiswaTranskripData($siswa->id);
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return view('portalsiswa::livewire.nilai-rapor-siswa', [
            'siswa' => $siswa,
            'semesters' => $semesters,
            'selectedSemester' => $selectedSemester,
            'historicalRombel' => $historicalRombel,
            'historicalWaliKelas' => $historicalWaliKelas,
            'ledgerRows' => $ledgerRows,
            'summary' => $summary,
            'catatanWali' => $catatanWali,
            'availablePeriodes9M' => $availablePeriodes9M ?? [],
            'selectedPeriode9M' => $this->selectedPeriode9M,
            'matchingPeriodeId' => $matchingPeriodeId ?? null,
            'raporWikramaRecords' => $raporWikramaRecords,
            'raporPemerintahRecords' => $raporPemerintahRecords,
            'transkripData' => $transkripData,
        ]);
    }
}
