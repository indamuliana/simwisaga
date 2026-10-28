<?php

namespace App\Modules\Rapor\Services;

use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Models\NilaiAsesmenHarian;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\NilaiHarian;
use App\Modules\Penilaian\Models\NilaiSumatif;
use App\Modules\Penilaian\Models\PengaturanPenilaian;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use App\Modules\Penilaian\Services\MapelPilihanScoreService;
use App\Modules\Penilaian\Services\PenilaianScoreCalculator;
use App\Modules\Rapor\Models\CatatanWaliKelas;
use App\Modules\SeniBudaya\Models\SeniBudayaAnggota;
use App\Modules\SeniBudaya\Models\SeniBudayaTp;
use App\Modules\SeniBudaya\Services\SeniBudayaService;
use App\Modules\Sistem\Models\PeriodeRapor;
use Illuminate\Support\Collection;

class RaporScoreService
{
    protected ?PenilaianScoreCalculator $calculator = null;
    protected array $cohortCache = [];
    protected array $penugasansCache = [];
    protected array $strukturKurikulumCache = [];
    protected mixed $mapelSeniCache = null;

    public function getCalculator(): PenilaianScoreCalculator
    {
        if (!$this->calculator) {
            $this->calculator = new PenilaianScoreCalculator();
        }
        return $this->calculator;
    }

    /**
     * Preload catatan, absensi, ekskul, dan prestasi untuk seluruh cohort siswa sekaligus.
     * Mengeliminasi query N+1 pada saat cetak rapor massal.
     */
    public function preloadCohort(Collection $siswas, Semester $semester, ?int $periodeRaporId = null): void
    {
        $siswaIds = $siswas->pluck('id')->filter()->all();
        if (empty($siswaIds)) {
            return;
        }

        // 1. Preload Catatan Wali Kelas
        $catatans = CatatanWaliKelas::whereIn('siswa_id', $siswaIds)
            ->where('semester_id', $semester->id)
            ->get();
        foreach ($siswaIds as $sId) {
            $this->cohortCache['catatan'][$sId] = $catatans->where('siswa_id', $sId);
        }

        // 2. Preload Absensi Harian (S/I/A)
        $absensiGrouped = AbsensiHarian::whereIn('siswa_id', $siswaIds)
            ->where('semester_id', $semester->id)
            ->get()
            ->groupBy('siswa_id');
        foreach ($siswaIds as $sId) {
            $abs = $absensiGrouped->get($sId, collect())->groupBy('status');
            $this->cohortCache['absen'][$sId] = [
                'sakit' => isset($abs['Sakit']) ? count($abs['Sakit']) : 0,
                'izin' => isset($abs['Izin']) ? count($abs['Izin']) : 0,
                'alpa' => isset($abs['Alpa']) ? count($abs['Alpa']) : 0,
            ];
        }

        // 3. Preload Ekstrakurikuler
        $ekskulsGrouped = EkstrakurikulerAnggota::with('ekstrakurikuler')
            ->whereIn('siswa_id', $siswaIds)
            ->where('semester_id', $semester->id)
            ->where(function ($q) {
                $q->whereNull('status_keanggotaan')->orWhere('status_keanggotaan', 'aktif');
            })
            ->get()
            ->groupBy('siswa_id');
        foreach ($siswaIds as $sId) {
            $this->cohortCache['ekskul'][$sId] = $ekskulsGrouped->get($sId, collect());
        }

        // 4. Preload Prestasi
        if (class_exists(\App\Modules\Prestasi\Models\Prestasi::class)) {
            $prestasis = \App\Modules\Prestasi\Models\Prestasi::with(['pembimbingPegawai', 'siswas'])
                ->whereHas('siswas', fn($q) => $q->whereIn('siswas.id', $siswaIds))
                ->where('semester_id', $semester->id)
                ->approved()
                ->when($periodeRaporId, fn($q) => $q->where(fn($sub) => $sub->where('periode_rapor_id', $periodeRaporId)->orWhereNull('periode_rapor_id')))
                ->orderBy('tanggal_perolehan', 'desc')
                ->get();
            foreach ($siswaIds as $sId) {
                $this->cohortCache['prestasi'][$sId] = $prestasis->filter(fn($p) => $p->siswas->contains('id', $sId))->values();
            }
        }

        // 5. Preload Nilai Akhir
        $naGrouped = NilaiAkhir::with(['penugasanMengajar.mapel.kelompokMapel'])
            ->whereIn('siswa_id', $siswaIds)
            ->whereHas('penugasanMengajar', fn($q) => $q->where('semester_id', $semester->id))
            ->get()
            ->groupBy('siswa_id');
        foreach ($siswaIds as $sId) {
            $this->cohortCache['nilai_akhir'][$sId] = $naGrouped->get($sId, collect())->keyBy('penugasan_mengajar_id');
        }

        // 6. Preload Nilai Formatif
        $nfGrouped = NilaiFormatif::whereIn('siswa_id', $siswaIds)
            ->whereNotNull('nilai')
            ->get()
            ->groupBy('siswa_id');
        foreach ($siswaIds as $sId) {
            $this->cohortCache['nilai_formatif'][$sId] = $nfGrouped->get($sId, collect())->keyBy('tujuan_pembelajaran_id');
        }

        // 7. Preload Nilai Harian (NH)
        $nhGrouped = NilaiHarian::whereIn('siswa_id', $siswaIds)
            ->get()
            ->groupBy('siswa_id');
        foreach ($siswaIds as $sId) {
            $this->cohortCache['nilai_harian'][$sId] = $nhGrouped->get($sId, collect());
        }

        // 8. Preload Nilai Sumatif
        $nsGrouped = NilaiSumatif::whereIn('siswa_id', $siswaIds)
            ->get()
            ->groupBy('siswa_id');
        foreach ($siswaIds as $sId) {
            $this->cohortCache['nilai_sumatif'][$sId] = $nsGrouped->get($sId, collect())->keyBy('penugasan_mengajar_id');
        }
    }
    /**
     * Menentukan target period key (9M1, 9M2, 9M3, 9M4, atau Semester) dari PeriodeRapor
     */
    public function resolveTargetPeriodKey(?PeriodeRapor $periode, ?Semester $semester): string
    {
        $isGenap = $semester && str_contains(strtolower($semester->semester), 'genap');
        if (!$periode) {
            return $isGenap ? '9M4' : '9M2';
        }

        $pKode = strtoupper($periode->kode ?? '');
        $pNama = strtoupper($periode->nama ?? '');
        $pSebutan = strtoupper($periode->sebutan ?? '');

        if (!$isGenap) {
            if (str_contains($pKode, 'PTS') || str_contains($pKode, '9M1') || str_contains($pNama, 'TENGAH') || str_contains($pSebutan, 'MINGGU KE-1')) {
                return '9M1';
            }
            return '9M2';
        }

        if (str_contains($pKode, 'PTS') || str_contains($pKode, '9M3') || str_contains($pNama, 'TENGAH') || str_contains($pSebutan, 'MINGGU KE-3')) {
            return '9M3';
        }
        return '9M4';
    }

    /**
     * Konfigurasi periode 9 Minggu (AH yang aktif & nama sumatif)
     */
    public function getPeriodMeta(string $periodKey): array
    {
        return match (strtoupper(trim($periodKey))) {
            '9M1', 'STS 1', 'PTS 1' => [
                'period_key' => '9M1',
                'label' => '9 Minggu ke-1 (STS 1)',
                'sebutan' => '9 Minggu ke-1',
                'ah_keys' => ['AH 1', 'AH 2'],
                'sumatif_code' => 'sts',
                'sumatif_label' => 'STS 1',
                'is_genap' => false,
            ],
            '9M2', 'SAS', 'PAS 1' => [
                'period_key' => '9M2',
                'label' => '9 Minggu ke-2 (SAS)',
                'sebutan' => '9 Minggu ke-2',
                'ah_keys' => ['AH 3', 'AH 4'],
                'sumatif_code' => 'sas',
                'sumatif_label' => 'SAS',
                'is_genap' => false,
            ],
            '9M3', 'STS 2', 'PTS 2' => [
                'period_key' => '9M3',
                'label' => '9 Minggu ke-3 (STS 2)',
                'sebutan' => '9 Minggu ke-3',
                'ah_keys' => ['AH 5', 'AH 6'],
                'sumatif_code' => 'sts',
                'sumatif_label' => 'STS 2',
                'is_genap' => true,
            ],
            '9M4', 'SAT', 'PAT', 'PAS 2' => [
                'period_key' => '9M4',
                'label' => '9 Minggu ke-4 (SAT)',
                'sebutan' => '9 Minggu ke-4',
                'ah_keys' => ['AH 7', 'AH 8'],
                'sumatif_code' => 'sas',
                'sumatif_label' => 'SAT',
                'is_genap' => true,
            ],
            default => [
                'period_key' => '9M1',
                'label' => '9 Minggu ke-1',
                'sebutan' => '9 Minggu ke-1',
                'ah_keys' => ['AH 1', 'AH 2'],
                'sumatif_code' => 'sts',
                'sumatif_label' => 'STS 1',
                'is_genap' => false,
            ],
        };
    }

    /**
     * Mengambil seluruh data nilai akademik untuk Rapor Wikrama (Rincian per 9 Minggu)
     */
    public function getWikramaAcademicRecords(Siswa $siswa, Semester $semester, string $periodKey): Collection
    {
        $meta = $this->getPeriodMeta($periodKey);
        $normKey = $meta['period_key'];
        $ahKeys = $meta['ah_keys'];
        $sumatifCode = $meta['sumatif_code'];
        $isGenap = $meta['is_genap'];

        $rombel = $siswa->rombel;

        // 1. Ambil Penugasan Mengajar untuk rombel siswa pada semester ini (dengan memoization)
        $penugasanKey = "{$siswa->rombel_id}_{$semester->id}";
        if (!isset($this->penugasansCache[$penugasanKey])) {
            $this->penugasansCache[$penugasanKey] = PenugasanMengajar::with(['mapel.kelompokMapel', 'pegawai', 'tujuanPembelajarans'])
                ->where('rombel_id', $siswa->rombel_id)
                ->where('semester_id', $semester->id)
                ->get()
                ->keyBy('mapel_id');
        }
        $penugasans = $this->penugasansCache[$penugasanKey];

        // 2. Ambil Nilai Akhir yang sudah tersimpan untuk siswa ini di semester ini
        $nilaiAkademiks = $this->cohortCache['nilai_akhir'][$siswa->id] ?? NilaiAkhir::with(['penugasanMengajar.mapel.kelompokMapel'])
            ->where('siswa_id', $siswa->id)
            ->whereHas('penugasanMengajar', fn($q) => $q->where('semester_id', $semester->id))
            ->get()
            ->keyBy('penugasan_mengajar_id');

        // 3. Susun daftar Mata Pelajaran terstruktur lengkap kurikulum
        $mapelList = collect();

        // 3a. Dari Struktur Kurikulum jika ada (dengan memoization)
        if ($rombel) {
            $skKey = "{$rombel->tahun_ajaran_id}_{$rombel->jurusan_id}_{$rombel->tingkat}_" . ($isGenap ? 'genap' : 'ganjil');
            if (!isset($this->strukturKurikulumCache[$skKey])) {
                $this->strukturKurikulumCache[$skKey] = StrukturKurikulum::with(['mapel.kelompokMapel'])
                    ->where('tahun_ajaran_id', $rombel->tahun_ajaran_id)
                    ->where('jurusan_id', $rombel->jurusan_id)
                    ->where('tingkat', $rombel->tingkat)
                    ->where(function ($q) use ($isGenap) {
                        if ($isGenap) {
                            $q->where('jp_genap', '>', 0)
                                ->orWhere(fn($sub) => $sub->where('jp_ganjil', 0)->where('jp_genap', 0)->where('jp_per_minggu', '>', 0));
                        } else {
                            $q->where('jp_ganjil', '>', 0)
                                ->orWhere(fn($sub) => $sub->where('jp_ganjil', 0)->where('jp_genap', 0)->where('jp_per_minggu', '>', 0));
                        }
                    })
                    ->orderBy('urutan')
                    ->get();
            }
            $strukturKurikulums = $this->strukturKurikulumCache[$skKey];

            foreach ($strukturKurikulums as $sk) {
                if ($sk->mapel && !$mapelList->has($sk->mapel_id)) {
                    $mapelList->put($sk->mapel_id, $sk->mapel);
                }
            }
        }

        // 3b. Dari Penugasan Mengajar yang belum masuk Struktur Kurikulum
        foreach ($penugasans as $mId => $pm) {
            if ($pm->mapel && !$mapelList->has($mId)) {
                $mapelList->put($mId, $pm->mapel);
            }
        }

        // 3c. Dari Nilai Akhir yang sudah ada
        foreach ($nilaiAkademiks as $na) {
            $pm = $na->penugasanMengajar;
            if ($pm?->mapel && !$mapelList->has($pm->mapel_id)) {
                $mapelList->put($pm->mapel_id, $pm->mapel);
            }
        }

        // Pastikan Mapel Seni Budaya terdaftar jika belum ada di list (dengan memoization)
        $hasSeniMapel = $mapelList->contains(function ($m) {
            return strtolower($m->kode) === 'seni' || str_contains(strtolower($m->nama), 'seni budaya');
        });

        if (!$hasSeniMapel) {
            if ($this->mapelSeniCache === null) {
                $this->mapelSeniCache = Mapel::with('kelompokMapel')
                    ->where('kode', 'SENI')
                    ->orWhere('nama', 'like', '%Seni Budaya%')
                    ->first() ?: false;
            }
            if ($this->mapelSeniCache) {
                $mapelList->put($this->mapelSeniCache->id, $this->mapelSeniCache);
            }
        }

        // 4. Pre-fetch Formatif, Harian (NH), Sumatif, dan TP
        $allPmIds = $penugasans->pluck('id')->merge($nilaiAkademiks->pluck('penugasan_mengajar_id'))->filter()->unique()->values()->all();

        $allFormatifs = $this->cohortCache['nilai_formatif'][$siswa->id] ?? NilaiFormatif::where('siswa_id', $siswa->id)
            ->whereNotNull('nilai')
            ->get()
            ->keyBy('tujuan_pembelajaran_id');

        if (isset($this->cohortCache['nilai_harian'][$siswa->id])) {
            $allNhs = $this->cohortCache['nilai_harian'][$siswa->id]
                ->where('target_periode', $normKey)
                ->whereIn('penugasan_mengajar_id', $allPmIds)
                ->keyBy('penugasan_mengajar_id');
        } else {
            $allNhs = NilaiHarian::where('siswa_id', $siswa->id)
                ->where('target_periode', $normKey)
                ->whereIn('penugasan_mengajar_id', $allPmIds)
                ->get()
                ->keyBy('penugasan_mengajar_id');
        }

        $allSumatifs = $this->cohortCache['nilai_sumatif'][$siswa->id] ?? NilaiSumatif::where('siswa_id', $siswa->id)
            ->whereIn('penugasan_mengajar_id', $allPmIds)
            ->get()
            ->keyBy('penugasan_mengajar_id');

        $calc = $this->getCalculator();
        $settings = $calc->getSettings($semester->tahun_ajaran_id);

        $rows = collect();

        foreach ($mapelList as $mapel) {
            $isSeni = strtolower($mapel->kode) === 'seni' || str_contains(strtolower($mapel->nama), 'seni budaya');
            if ($isSeni) {
                // Seni Budaya diproses dinamis tersendiri di bawah untuk integrasi cabang
                continue;
            }

            $pm = $penugasans->get($mapel->id);
            if (!$pm) {
                $naEntry = $nilaiAkademiks->first(fn($item) => $item->penugasanMengajar?->mapel_id == $mapel->id);
                $pm = $naEntry?->penugasanMengajar;
            }

            $kkm = (float) ($mapel->kkm_default ?? 75);

            // Tujuan Pembelajaran untuk penugasan ini di periode ini
            $tpsInBlock = collect();
            if ($pm) {
                $allTpsForPm = $pm->tujuanPembelajarans ?? collect();
                $tpsInBlock = $allTpsForPm->filter(fn($tp) => ($tp->target_periode === $normKey) || empty($tp->target_periode))->sortBy('urutan')->values();
            }

            // Kumpulkan nilai TP
            $tpScoresMap = [];
            $tpList = [];
            foreach ($tpsInBlock as $tp) {
                $fVal = $allFormatifs->get($tp->id)?->nilai;
                $numScore = ($fVal !== null && $fVal !== '' && is_numeric($fVal)) ? (float) $fVal : null;
                $tpScoresMap[$tp->id] = $numScore;
                $tpList[] = (object) [
                    'id' => $tp->id,
                    'kode' => $tp->kode ?: 'TP',
                    'deskripsi' => $tp->deskripsi ?: $tp->lingkup_materi,
                    'score' => $numScore,
                ];
            }

            if ($tpsInBlock->isEmpty() && $pm) {
                $legacyAhs = NilaiAsesmenHarian::where('siswa_id', $siswa->id)
                    ->where('penugasan_mengajar_id', $pm->id)
                    ->whereIn('kode_ah', $ahKeys)
                    ->orderBy('kode_ah')
                    ->get();
                foreach ($legacyAhs as $lah) {
                    $tpList[] = (object) [
                        'id' => null,
                        'kode' => $lah->kode_ah,
                        'deskripsi' => 'Asesmen Harian ' . $lah->kode_ah,
                        'score' => $lah->nilai !== null ? (float) $lah->nilai : null,
                    ];
                }
            }

            // Nilai Harian (NH)
            $nhRec = $pm ? $allNhs->get($pm->id) : null;
            $nhVal = $nhRec ? (float) $nhRec->nilai : 100.0;

            // Sumatif (STS atau SAS atau SAT)
            $pmSumatif = $pm ? $allSumatifs->get($pm->id) : null;
            $sumatifVal = null;
            if ($pmSumatif) {
                $rawSum = $sumatifCode === 'sts' ? $pmSumatif->sts : $pmSumatif->sas;
                if ($rawSum !== null && $rawSum !== '' && is_numeric($rawSum)) {
                    $sumatifVal = (float) $rawSum;
                }
            }

            // Ambil dari NilaiAkhir jika sudah ada data_9m
            $na = $pm ? $nilaiAkademiks->get($pm->id) : null;
            $block9M = $na?->data_9m[$normKey] ?? null;

            if ($block9M && is_array($block9M)) {
                $eval = $block9M;
            } else {
                $eval = $calc->calculate9MBlock(
                    $tpScoresMap,
                    $nhVal,
                    $sumatifVal,
                    $kkm,
                    $tpsInBlock,
                    null,
                    $settings
                );
            }

            $score9M = $eval['nilai_akhir'] ?? null;
            $status = $eval['status'] ?? 'belum_lengkap';
            $predikat = $eval['predikat'] ?? $calc->resolvePredikat($score9M, $kkm, $settings);
            $descTinggi = $eval['deskripsi_tertinggi'] ?? '';
            $descRendah = $eval['deskripsi_terendah'] ?? '';
            $descManual = $eval['deskripsi_manual'] ?? null;
            $narasiCapaian = $eval['narasi_capaian'] ?? ($descManual ?: trim($descTinggi . ($descRendah ? ' ' . $descRendah : '')));

            $rows->push((object) [
                'is_seni' => false,
                'mapel' => $mapel,
                'mapel_nama' => $mapel->nama,
                'kkm' => $kkm,
                'status' => $status,
                'kelompok' => $mapel->kelompokMapel,
                'kelompok_kode' => $mapel->kelompokMapel?->kode ?? 'A',
                'kelompok_nama' => $mapel->kelompokMapel?->nama ?? 'Muatan Umum',
                'kelompok_urutan' => $mapel->kelompokMapel?->urutan ?? 99,
                'mapel_urutan' => $mapel->urutan ?? 999,
                'tps' => $tpList,
                'rata_tp' => $eval['rata_tp'] ?? null,
                'nh' => $eval['nh'] ?? $nhVal,
                'sumatif' => $eval['sumatif'] ?? $sumatifVal,
                'nilai_akhir' => $score9M,
                'predikat' => $predikat,
                'deskripsi_tertinggi' => $descTinggi,
                'deskripsi_terendah' => $descRendah,
                'deskripsi_manual' => $descManual,
                'narasi_capaian' => $narasiCapaian,
            ]);
        }

        // 5. Integrasi Seni Budaya Dinamis (Cabang Seni Budaya)
        $sbAnggota = SeniBudayaAnggota::with('seniBudaya')
            ->where('siswa_id', $siswa->id)
            ->where('semester_id', $semester->id)
            ->where('status_keanggotaan', 'aktif')
            ->first();

        if ($sbAnggota && $sbAnggota->seniBudaya) {
            $cabangNama = $sbAnggota->seniBudaya->nama;
            $customMapelNama = 'Seni Budaya (' . $cabangNama . ')';
            $sbKkm = (int) ($sbAnggota->seniBudaya->kkm ?? 75);

            // Sumatif Seni Budaya sesuai periode
            $sbSumatif = match ($normKey) {
                '9M1' => $sbAnggota->sts_1 !== null ? (float) $sbAnggota->sts_1 : null,
                '9M2' => $sbAnggota->sas !== null ? (float) $sbAnggota->sas : null,
                '9M3' => $sbAnggota->sts_2 !== null ? (float) $sbAnggota->sts_2 : null,
                '9M4' => $sbAnggota->sat !== null ? (float) $sbAnggota->sat : null,
                default => null,
            };

            // TPs Seni Budaya
            $sbTps = SeniBudayaTp::where('seni_budaya_id', $sbAnggota->seni_budaya_id)
                ->where('semester_id', $semester->id)
                ->get();

            $sbTpList = [];
            foreach ($sbTps as $tp) {
                $sbTpList[] = (object) [
                    'id' => $tp->id,
                    'kode' => $tp->kode ?: 'TP',
                    'deskripsi' => $tp->deskripsi ?: $tp->materi,
                    'score' => null,
                ];
            }

            // Nilai Harian (Presensi)
            $sbNh = $sbAnggota->nilai_presensi !== null ? (float) $sbAnggota->nilai_presensi : 100.0;

            // Nilai Akhir Seni Budaya
            $sbScore = $sbAnggota->getScoreForPeriod($normKey);
            if ($sbScore === null && $sbSumatif !== null) {
                $calcSb = app(SeniBudayaService::class)->calculateGrade(
                    $sbNh,
                    (float) $sbSumatif,
                    (int) ($sbAnggota->seniBudaya->bobot_presensi ?? 30),
                    (int) ($sbAnggota->seniBudaya->bobot_penilaian ?? 70),
                    $sbKkm
                );
                $sbScore = $calcSb['nilai_akhir'];
            }

            // Validasi KKM & Kelengkapan
            if ($sbSumatif === null) {
                $sbScore = null;
                $sbStatus = 'belum_lengkap';
                $sbPredikat = 'Belum Lengkap';
                $sbDescTinggi = 'Nilai belum lengkap, silakan hubungi guru yang bersangkutan.';
                $sbDescRendah = '';
                $sbDescManual = null;
            } elseif ($sbSumatif < $sbKkm || ($sbScore !== null && $sbScore < $sbKkm)) {
                $sbScore = null;
                $sbStatus = 'belum_kompeten';
                $sbPredikat = 'Belum Kompeten';
                $sbDescTinggi = 'Nilai belum kompeten, silakan hubungi guru yang bersangkutan.';
                $sbDescRendah = '';
                $sbDescManual = null;
            } else {
                $sbStatus = 'kompeten';
                $sbPredikat = $calc->resolvePredikat($sbScore, $sbKkm, $settings);
                $descEntry = $sbAnggota->getDescriptionForPeriod($normKey);
                $sbDescTinggi = $descEntry['deskripsi_tertinggi'] ?: 'Menunjukkan penguasaan yang sangat baik dalam berkarya dan mengapresiasi seni.';
                $sbDescRendah = $descEntry['deskripsi_terendah'] ?: '';
                $sbDescManual = $descEntry['deskripsi_manual'] ?? null;
            }

            $sbItem = (object) [
                'is_seni' => true,
                'seni_cabang' => $cabangNama,
                'seni_presensi' => $sbNh,
                'kkm' => $sbKkm,
                'status' => $sbStatus,
                'mapel' => new Mapel(['kode' => 'SENI', 'nama' => $customMapelNama, 'urutan' => 6]),
                'mapel_nama' => $customMapelNama,
                'kelompok' => null,
                'kelompok_kode' => 'A',
                'kelompok_nama' => 'Muatan Umum',
                'kelompok_urutan' => 1,
                'mapel_urutan' => 6,
                'tps' => $sbTpList,
                'rata_tp' => null,
                'nh' => $sbNh,
                'sumatif' => $sbSumatif,
                'nilai_akhir' => $sbScore,
                'predikat' => $sbPredikat,
                'deskripsi_tertinggi' => $sbDescTinggi,
                'deskripsi_terendah' => $sbDescRendah,
                'deskripsi_manual' => $sbDescManual,
                'narasi_capaian' => $sbDescManual ?: trim($sbDescTinggi . ($sbDescRendah ? ' ' . $sbDescRendah : '')),
            ];
            $rows->push($sbItem);
        } else {
            // Siswa belum terdaftar di cabang Seni Budaya
            $sbItem = (object) [
                'is_seni' => true,
                'seni_cabang' => null,
                'seni_presensi' => null,
                'kkm' => 75,
                'status' => 'belum_lengkap',
                'mapel' => new Mapel(['kode' => 'SENI', 'nama' => 'Seni Budaya', 'urutan' => 6]),
                'mapel_nama' => 'Seni Budaya',
                'kelompok' => null,
                'kelompok_kode' => 'A',
                'kelompok_nama' => 'Muatan Umum',
                'kelompok_urutan' => 1,
                'mapel_urutan' => 6,
                'tps' => [],
                'rata_tp' => null,
                'nh' => null,
                'sumatif' => null,
                'nilai_akhir' => null,
                'predikat' => 'Belum Lengkap',
                'deskripsi_tertinggi' => 'Peserta didik belum terdaftar di cabang Seni Budaya, silakan hubungi koordinator Seni Budaya.',
                'deskripsi_terendah' => '',
                'deskripsi_manual' => null,
                'narasi_capaian' => 'Peserta didik belum terdaftar di cabang Seni Budaya, silakan hubungi koordinator Seni Budaya.',
            ];
            $rows->push($sbItem);
        }

        // 6. Integrasi Mapel Pilihan (Kelompok C)
        $mapilRows = app(MapelPilihanScoreService::class)->getStudentRecordsForWikrama($siswa, $semester, $normKey);
        foreach ($mapilRows as $mRow) {
            $rows->push($mRow);
        }

        // Urutkan berdasarkan Kelompok Mapel lalu Urutan Mapel
        return $rows->sortBy(function ($row) {
            return sprintf('%04d-%04d', $row->kelompok_urutan, $row->mapel_urutan);
        })->values();
    }

    /**
     * Mengambil seluruh data nilai akademik untuk Rapor Pemerintah (Standar Kemdikbud Semester)
     */
    public function getPemerintahAcademicRecords(Siswa $siswa, Semester $semester): Collection
    {
        $calc = app(\App\Modules\Penilaian\Services\PenilaianScoreCalculator::class);
        $settings = \App\Modules\Penilaian\Models\PengaturanPenilaian::getActive();

        $nilaiAkademiks = NilaiAkhir::with(['penugasanMengajar.mapel.kelompokMapel'])
            ->where('siswa_id', $siswa->id)
            ->whereHas('penugasanMengajar', fn($q) => $q->where('semester_id', $semester->id))
            ->get();

        $rows = collect();

        foreach ($nilaiAkademiks as $na) {
            $pm = $na->penugasanMengajar;
            $mapel = $pm?->mapel;
            if (!$mapel) continue;

            $kkm = (float) ($mapel->kkm_default ?? 75);
            $predikat = $na->predikat ?? $calc->resolvePredikat($na->nilai_akhir, $kkm, $settings);

            $rows->push((object) [
                'is_seni' => false,
                'mapel' => $mapel,
                'mapel_nama' => $mapel->nama,
                'kkm' => $kkm,
                'kelompok' => $mapel->kelompokMapel,
                'kelompok_kode' => $mapel->kelompokMapel?->kode ?? 'A',
                'kelompok_nama' => $mapel->kelompokMapel?->nama ?? 'Muatan Umum',
                'kelompok_urutan' => $mapel->kelompokMapel?->urutan ?? 99,
                'mapel_urutan' => $mapel->urutan ?? 999,
                'nilai_akhir' => $na->nilai_akhir,
                'predikat' => $predikat,
                'deskripsi_tertinggi' => $na->deskripsi_tertinggi,
                'deskripsi_terendah' => $na->deskripsi_terendah,
                'deskripsi_manual' => $na->deskripsi_manual,
            ]);
        }

        // Integrasi Seni Budaya (Jika terdaftar aktif)
        $sbAnggota = SeniBudayaAnggota::with('seniBudaya')
            ->where('siswa_id', $siswa->id)
            ->where('semester_id', $semester->id)
            ->where('status_keanggotaan', 'aktif')
            ->first();

        if ($sbAnggota && $sbAnggota->seniBudaya) {
            $customMapelNama = 'Seni Budaya: ' . $sbAnggota->seniBudaya->nama;
            $sbScore = $sbAnggota->nilai_akhir;
            $evals = $sbAnggota->deskripsi_evaluations ?? [];
            $semKey = str_contains(strtolower($semester->semester), 'genap') ? 'SAT' : 'SAS';
            $sbDesc = $evals[$semKey] ?? [];

            $existingIndex = $rows->search(function ($item) {
                $kode = strtolower($item->mapel?->kode ?? '');
                $nama = strtolower($item->mapel?->nama ?? '');
                return $kode === 'seni' || str_contains($nama, 'seni budaya');
            });

            $sbKkm = (float) ($sbAnggota->seniBudaya?->kkm ?? 75);
            $sbItem = (object) [
                'is_seni' => true,
                'mapel' => $existingIndex !== false ? $rows[$existingIndex]->mapel : new Mapel(['kode' => 'SENI', 'nama' => $customMapelNama, 'urutan' => 6]),
                'mapel_nama' => $customMapelNama,
                'kkm' => $sbKkm,
                'kelompok' => $existingIndex !== false ? $rows[$existingIndex]->kelompok : null,
                'kelompok_kode' => 'A',
                'kelompok_nama' => 'Muatan Umum',
                'kelompok_urutan' => 1,
                'mapel_urutan' => 6,
                'nilai_akhir' => $sbScore,
                'predikat' => $calc->resolvePredikat($sbScore, $sbKkm, $settings),
                'deskripsi_tertinggi' => $sbDesc['deskripsi_tertinggi'] ?? $sbAnggota->deskripsi_tertinggi,
                'deskripsi_terendah' => $sbDesc['deskripsi_terendah'] ?? $sbAnggota->deskripsi_terendah,
                'deskripsi_manual' => $sbDesc['deskripsi_manual'] ?? $sbAnggota->deskripsi_manual,
            ];

            if ($existingIndex !== false) {
                $rows[$existingIndex] = $sbItem;
            } else {
                $rows->push($sbItem);
            }
        }

        // Integrasi Mapel Pilihan (Kelompok C)
        $mapilRows = app(MapelPilihanScoreService::class)->getStudentRecordsForPemerintah($siswa, $semester);
        foreach ($mapilRows as $mRow) {
            $rows->push($mRow);
        }

        return $rows->sortBy(function ($row) {
            return sprintf('%04d-%04d', $row->kelompok_urutan, $row->mapel_urutan);
        })->values();
    }

    /**
     * Kelompokkan records nilai berdasarkan Kelompok Mapel
     */
    public function groupAcademicRecords(Collection $records): Collection
    {
        return $records->groupBy(function ($row) {
            if ($row->kelompok) {
                return $row->kelompok->kode . '. ' . $row->kelompok->nama;
            }
            return $row->kelompok_kode . '. ' . $row->kelompok_nama;
        });
    }

    /**
     * Ambil Catatan Wali Kelas / Pembimbing Rayon per Periode
     */
    public function getCatatanForSiswa(int $siswaId, int $semesterId, ?int $periodeRaporId = null): string
    {
        if (isset($this->cohortCache['catatan'][$siswaId])) {
            $catatans = $this->cohortCache['catatan'][$siswaId];
            if ($periodeRaporId) {
                $cat = $catatans->first(fn($c) => $c->periode_rapor_id == $periodeRaporId);
                if ($cat && !empty($cat->catatan)) {
                    return $cat->catatan;
                }
            }
            $fallbackCat = $catatans->first(fn($c) => is_null($c->periode_rapor_id));
            return $fallbackCat?->catatan ?? '';
        }

        if ($periodeRaporId) {
            $cat = CatatanWaliKelas::where('siswa_id', $siswaId)
                ->where('semester_id', $semesterId)
                ->where('periode_rapor_id', $periodeRaporId)
                ->first();
            if ($cat && !empty($cat->catatan)) {
                return $cat->catatan;
            }
        }

        // Fallback jika tidak ada catatan khusus periode, ambil catatan semester umum
        $fallbackCat = CatatanWaliKelas::where('siswa_id', $siswaId)
            ->where('semester_id', $semesterId)
            ->whereNull('periode_rapor_id')
            ->first();

        return $fallbackCat?->catatan ?? '';
    }

    /**
     * Ambil Prestasi Siswa yang approved untuk dicetak di Catatan Rapor
     */
    public function getPrestasiForSiswa(int $siswaId, int $semesterId, ?int $periodeRaporId = null): Collection
    {
        if (isset($this->cohortCache['prestasi'][$siswaId])) {
            return $this->cohortCache['prestasi'][$siswaId];
        }

        if (!class_exists(\App\Modules\Prestasi\Models\Prestasi::class)) {
            return collect();
        }

        $query = \App\Modules\Prestasi\Models\Prestasi::with(['pembimbingPegawai', 'siswas'])
            ->whereHas('siswas', function ($q) use ($siswaId) {
                $q->where('siswas.id', $siswaId);
            })
            ->where('semester_id', $semesterId)
            ->approved();

        if ($periodeRaporId) {
            $query->where(function ($q) use ($periodeRaporId) {
                $q->where('periode_rapor_id', $periodeRaporId)
                  ->orWhereNull('periode_rapor_id');
            });
        }

        return $query->orderBy('tanggal_perolehan', 'desc')->get();
    }

    /**
     * Ambil Rekap Ketidakhadiran (S/I/A)
     */
    public function getRekapAbsen(int $siswaId, int $semesterId): array
    {
        if (isset($this->cohortCache['absen'][$siswaId])) {
            return $this->cohortCache['absen'][$siswaId];
        }

        $absensi = AbsensiHarian::where('siswa_id', $siswaId)
            ->where('semester_id', $semesterId)
            ->get()
            ->groupBy('status');

        return [
            'sakit' => isset($absensi['Sakit']) ? count($absensi['Sakit']) : 0,
            'izin' => isset($absensi['Izin']) ? count($absensi['Izin']) : 0,
            'alpa' => isset($absensi['Alpa']) ? count($absensi['Alpa']) : 0,
        ];
    }

    /**
     * Ambil Ekstrakurikuler aktif
     */
    public function getEkskuls(int $siswaId, int $semesterId): Collection
    {
        if (isset($this->cohortCache['ekskul'][$siswaId])) {
            return $this->cohortCache['ekskul'][$siswaId];
        }

        return EkstrakurikulerAnggota::with('ekstrakurikuler')
            ->where('siswa_id', $siswaId)
            ->where('semester_id', $semesterId)
            ->where(function ($q) {
                $q->whereNull('status_keanggotaan')->orWhere('status_keanggotaan', 'aktif');
            })
            ->get();
    }

    /**
     * Menghitung status kelengkapan dan kompetensi rapor siswa untuk periode tertentu
     */
    public function getStudentRaporStatus(Siswa $siswa, Semester $semester, string $periodKey): array
    {
        $records = $this->getWikramaAcademicRecords($siswa, $semester, $periodKey);
        $totalMapels = $records->count();
        $lengkapCount = 0;
        $belumLengkapCount = 0;
        $belumKompetenCount = 0;
        $seniInfo = null;

        foreach ($records as $r) {
            if ($r->is_seni) {
                $seniInfo = [
                    'terdaftar' => !empty($r->seni_cabang),
                    'cabang' => $r->seni_cabang ?? 'Belum Terdaftar',
                    'nilai_akhir' => $r->nilai_akhir,
                    'status' => $r->status,
                ];
            }

            if ($r->status === 'kompeten' && $r->nilai_akhir !== null) {
                $lengkapCount++;
            } elseif ($r->status === 'belum_kompeten') {
                $belumKompetenCount++;
            } else {
                $belumLengkapCount++;
            }
        }

        $overallStatus = 'kompeten';
        if ($belumKompetenCount > 0) {
            $overallStatus = 'belum_kompeten';
        } elseif ($belumLengkapCount > 0 || $lengkapCount < $totalMapels) {
            $overallStatus = 'belum_lengkap';
        }

        return [
            'overall_status' => $overallStatus,
            'total_mapels' => $totalMapels,
            'lengkap_count' => $lengkapCount,
            'belum_lengkap_count' => $belumLengkapCount,
            'belum_kompeten_count' => $belumKompetenCount,
            'seni_budaya' => $seniInfo,
        ];
    }
}

