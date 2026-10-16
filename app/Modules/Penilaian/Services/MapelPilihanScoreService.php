<?php

namespace App\Modules\Penilaian\Services;

use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\MapelPilihan;
use App\Modules\Akademik\Models\MapelPilihanAnggota;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\MapelPilihanTp;
use App\Modules\Penilaian\Models\NilaiMapelPilihanAkhir;
use App\Modules\Penilaian\Models\NilaiMapelPilihanFormatif;
use App\Modules\Penilaian\Models\NilaiMapelPilihanHarian;
use App\Modules\Penilaian\Models\NilaiMapelPilihanSumatif;
use App\Modules\Penilaian\Models\PengaturanPenilaian;
use Illuminate\Support\Collection;

class MapelPilihanScoreService
{
    protected PenilaianScoreCalculator $calculator;

    public function __construct(?PenilaianScoreCalculator $calculator = null)
    {
        $this->calculator = $calculator ?: new PenilaianScoreCalculator();
    }

    /**
     * Hitung dan simpan rekap nilai akhir Mapel Pilihan untuk seluruh anggota siswa
     */
    public function calculateAndSaveRekap(MapelPilihan $mapelPilihan, Semester $semester): Collection
    {
        $kkm = (float) ($mapelPilihan->kkm_default ?? 75);
        $isGenap = str_contains(strtolower($semester->semester), 'genap');

        $block1Key = $isGenap ? '9M3' : '9M1';
        $block2Key = $isGenap ? '9M4' : '9M2';

        // Ambil Tujuan Pembelajaran untuk Mapel Pilihan dan Semester ini
        $allTps = MapelPilihanTp::where('mapel_pilihan_id', $mapelPilihan->id)
            ->where('semester_id', $semester->id)
            ->orderBy('urutan')
            ->get();

        $tpsBlock1 = $allTps->filter(fn($tp) => ($tp->target_periode === $block1Key) || empty($tp->target_periode));
        $tpsBlock2 = $allTps->filter(fn($tp) => $tp->target_periode === $block2Key);

        // Ambil seluruh siswa anggota aktif
        $anggotas = MapelPilihanAnggota::with('siswa')
            ->where('mapel_pilihan_id', $mapelPilihan->id)
            ->where('status', 'aktif')
            ->get();

        $siswaIds = $anggotas->pluck('siswa_id')->all();

        // Formatif
        $formatifs = NilaiMapelPilihanFormatif::where('mapel_pilihan_id', $mapelPilihan->id)
            ->where('semester_id', $semester->id)
            ->whereIn('siswa_id', $siswaIds)
            ->get()
            ->groupBy('siswa_id');

        // Nilai Harian (NH)
        $nhRecords = NilaiMapelPilihanHarian::where('mapel_pilihan_id', $mapelPilihan->id)
            ->whereIn('siswa_id', $siswaIds)
            ->get()
            ->groupBy('siswa_id');

        // Sumatif
        $sumatifs = NilaiMapelPilihanSumatif::where('mapel_pilihan_id', $mapelPilihan->id)
            ->where('semester_id', $semester->id)
            ->whereIn('siswa_id', $siswaIds)
            ->get()
            ->keyBy('siswa_id');

        // Rekap lama
        $existingAkhirs = NilaiMapelPilihanAkhir::where('mapel_pilihan_id', $mapelPilihan->id)
            ->where('semester_id', $semester->id)
            ->whereIn('siswa_id', $siswaIds)
            ->get()
            ->keyBy('siswa_id');

        $computedResults = collect();
        $settings = PengaturanPenilaian::getActive();

        foreach ($anggotas as $anggota) {
            $siswaId = $anggota->siswa_id;
            $existingNa = $existingAkhirs->get($siswaId);
            $currentData9m = $existingNa?->data_9m ?? [];

            $sFormatifs = $formatifs->get($siswaId, collect());
            $sFormatifTpMap = [];
            foreach ($sFormatifs as $f) {
                if ($f->mapel_pilihan_tp_id) {
                    $sFormatifTpMap[$f->mapel_pilihan_tp_id] = $f->nilai;
                } elseif ($f->kode_ah) {
                    $matchingTp = $allTps->firstWhere('kode_ah', $f->kode_ah);
                    if ($matchingTp) {
                        $sFormatifTpMap[$matchingTp->id] = $f->nilai;
                    }
                }
            }

            // NH
            $sNhs = $nhRecords->get($siswaId, collect())->keyBy('target_periode');
            $nh1 = $sNhs->get($block1Key)?->nilai ?? 100.0;
            $nh2 = $sNhs->get($block2Key)?->nilai ?? 100.0;

            // Sumatif
            $siswaSumatif = $sumatifs->get($siswaId);
            $sumatifVal1 = $isGenap ? $siswaSumatif?->sts_2 : $siswaSumatif?->sts_1;
            $sumatifVal2 = $isGenap ? $siswaSumatif?->sat : $siswaSumatif?->sas;

            // Evaluasi Blok 1
            $resBlock1 = $this->calculator->calculate9MBlock(
                $sFormatifTpMap,
                $nh1,
                $sumatifVal1,
                $kkm,
                $tpsBlock1,
                $currentData9m[$block1Key]['deskripsi_manual'] ?? null,
                $settings
            );

            // Evaluasi Blok 2
            $resBlock2 = $this->calculator->calculate9MBlock(
                $sFormatifTpMap,
                $nh2,
                $sumatifVal2,
                $kkm,
                $tpsBlock2,
                $currentData9m[$block2Key]['deskripsi_manual'] ?? null,
                $settings
            );

            $currentData9m[$block1Key] = $resBlock1;
            $currentData9m[$block2Key] = $resBlock2;

            // Semester
            $semResult = $this->calculator->calculateSemester($resBlock1, $resBlock2, $kkm, $settings);

            $na = NilaiMapelPilihanAkhir::updateOrCreate(
                [
                    'mapel_pilihan_id' => $mapelPilihan->id,
                    'siswa_id' => $siswaId,
                    'semester_id' => $semester->id,
                ],
                [
                    'nilai_akhir' => $semResult['nilai_akhir'],
                    'predikat' => $semResult['predikat'],
                    'status' => $semResult['status'],
                    'deskripsi_tertinggi' => $resBlock2['deskripsi_tertinggi'] ?: $resBlock1['deskripsi_tertinggi'],
                    'deskripsi_terendah' => $resBlock2['deskripsi_terendah'] ?: $resBlock1['deskripsi_terendah'],
                    'deskripsi_manual' => $resBlock2['deskripsi_manual'] ?: $resBlock1['deskripsi_manual'],
                    'data_9m' => $currentData9m,
                ]
            );

            $computedResults->put($siswaId, $na);
        }

        return $computedResults;
    }

    /**
     * Mengambil seluruh data nilai Mapel Pilihan untuk satu siswa pada Rapor Wikrama (9 Minggu)
     */
    public function getStudentRecordsForWikrama(Siswa $siswa, Semester $semester, string $periodKey): Collection
    {
        $taId = $semester->tahun_ajaran_id;

        $anggotas = MapelPilihanAnggota::with('mapelPilihan')
            ->where('siswa_id', $siswa->id)
            ->where('status', 'aktif')
            ->where(function ($q) use ($taId) {
                $q->where('tahun_ajaran_id', $taId)
                  ->orWhereNull('tahun_ajaran_id');
            })
            ->get();

        if ($anggotas->isEmpty()) {
            return collect();
        }

        $isGenap = str_contains(strtolower($semester->semester), 'genap');
        $normKey = match (strtoupper(trim($periodKey))) {
            '9M1', 'STS 1', 'PTS 1' => '9M1',
            '9M2', 'SAS', 'PAS 1' => '9M2',
            '9M3', 'STS 2', 'PTS 2' => '9M3',
            '9M4', 'SAT', 'PAT', 'PAS 2' => '9M4',
            default => $isGenap ? '9M3' : '9M1',
        };

        $rows = collect();
        $settings = PengaturanPenilaian::getActive();

        foreach ($anggotas as $anggota) {
            $mapil = $anggota->mapelPilihan;
            if (!$mapil || !$mapil->is_active) {
                continue;
            }

            $kkm = (float) ($mapil->kkm_default ?? 75);

            // TPs untuk periode ini
            $tps = MapelPilihanTp::where('mapel_pilihan_id', $mapil->id)
                ->where('semester_id', $semester->id)
                ->where(function ($q) use ($normKey) {
                    $q->where('target_periode', $normKey)
                      ->orWhereNull('target_periode');
                })
                ->orderBy('urutan')
                ->get();

            // Formatif
            $formatifs = NilaiMapelPilihanFormatif::where('mapel_pilihan_id', $mapil->id)
                ->where('siswa_id', $siswa->id)
                ->where('semester_id', $semester->id)
                ->get();

            $tpScoresMap = [];
            $tpList = [];
            foreach ($tps as $tp) {
                $f = $formatifs->firstWhere('mapel_pilihan_tp_id', $tp->id);
                if (!$f && $tp->kode_ah) {
                    $f = $formatifs->firstWhere('kode_ah', $tp->kode_ah);
                }
                $val = $f?->nilai;
                $score = ($val !== null && $val !== '' && is_numeric($val)) ? (float) $val : null;
                $tpScoresMap[$tp->id] = $score;
                $tpList[] = (object) [
                    'kode' => $tp->kode_tp ?: 'TP',
                    'deskripsi' => $tp->deskripsi_tp ?: $tp->lingkup_materi,
                    'score' => $score,
                ];
            }

            // NH
            $nhRecord = NilaiMapelPilihanHarian::where('mapel_pilihan_id', $mapil->id)
                ->where('siswa_id', $siswa->id)
                ->where('target_periode', $normKey)
                ->first();
            $nhVal = $nhRecord ? (float) $nhRecord->nilai : 100.0;

            // Sumatif
            $sumatifRecord = NilaiMapelPilihanSumatif::where('mapel_pilihan_id', $mapil->id)
                ->where('siswa_id', $siswa->id)
                ->where('semester_id', $semester->id)
                ->first();

            $sumatifVal = match ($normKey) {
                '9M1' => $sumatifRecord?->sts_1 !== null ? (float) $sumatifRecord->sts_1 : null,
                '9M2' => $sumatifRecord?->sas !== null ? (float) $sumatifRecord->sas : null,
                '9M3' => $sumatifRecord?->sts_2 !== null ? (float) $sumatifRecord->sts_2 : null,
                '9M4' => $sumatifRecord?->sat !== null ? (float) $sumatifRecord->sat : null,
                default => null,
            };

            // Ambil dari NilaiAkhir jika sudah ada
            $na = NilaiMapelPilihanAkhir::where('mapel_pilihan_id', $mapil->id)
                ->where('siswa_id', $siswa->id)
                ->where('semester_id', $semester->id)
                ->first();

            $block9M = $na?->data_9m[$normKey] ?? null;

            if ($block9M) {
                $calcResult = $block9M;
            } else {
                $calcResult = $this->calculator->calculate9MBlock(
                    $tpScoresMap,
                    $nhVal,
                    $sumatifVal,
                    $kkm,
                    $tps,
                    null,
                    $settings
                );
            }

            $mapelObj = new Mapel([
                'id' => 'mapil_' . $mapil->id,
                'kode' => $mapil->kode,
                'nama' => $mapil->nama,
                'kkm_default' => $kkm,
                'urutan' => 100 + $mapil->id,
            ]);

            $rows->push((object) [
                'is_seni' => false,
                'is_mapil' => true,
                'mapil_id' => $mapil->id,
                'mapel' => $mapelObj,
                'mapel_nama' => $mapil->nama,
                'kkm' => $kkm,
                'status' => $calcResult['status'] ?? 'belum_lengkap',
                'kelompok' => null,
                'kelompok_kode' => 'C',
                'kelompok_nama' => 'Mata Pelajaran Pilihan',
                'kelompok_urutan' => 3,
                'mapel_urutan' => 100 + $mapil->id,
                'tps' => $tpList,
                'rata_tp' => $calcResult['rata_tp'] ?? null,
                'nh' => $calcResult['nh'] ?? $nhVal,
                'sumatif' => $calcResult['sumatif'] ?? $sumatifVal,
                'nilai_akhir' => $calcResult['nilai_akhir'] ?? null,
                'predikat' => $calcResult['predikat'] ?? $this->calculator->resolvePredikat($calcResult['nilai_akhir'] ?? null, $kkm, $settings),
                'deskripsi_tertinggi' => $calcResult['deskripsi_tertinggi'] ?? '',
                'deskripsi_terendah' => $calcResult['deskripsi_terendah'] ?? '',
                'deskripsi_manual' => $calcResult['deskripsi_manual'] ?? null,
                'narasi_capaian' => $calcResult['narasi_capaian'] ?? '',
            ]);
        }

        return $rows;
    }

    /**
     * Mengambil seluruh data nilai Mapel Pilihan untuk satu siswa pada Rapor Pemerintah (Semester)
     */
    public function getStudentRecordsForPemerintah(Siswa $siswa, Semester $semester): Collection
    {
        $taId = $semester->tahun_ajaran_id;

        $anggotas = MapelPilihanAnggota::with('mapelPilihan')
            ->where('siswa_id', $siswa->id)
            ->where('status', 'aktif')
            ->where(function ($q) use ($taId) {
                $q->where('tahun_ajaran_id', $taId)
                  ->orWhereNull('tahun_ajaran_id');
            })
            ->get();

        if ($anggotas->isEmpty()) {
            return collect();
        }

        $rows = collect();
        $settings = PengaturanPenilaian::getActive();

        foreach ($anggotas as $anggota) {
            $mapil = $anggota->mapelPilihan;
            if (!$mapil || !$mapil->is_active) {
                continue;
            }

            $kkm = (float) ($mapil->kkm_default ?? 75);

            $na = NilaiMapelPilihanAkhir::where('mapel_pilihan_id', $mapil->id)
                ->where('siswa_id', $siswa->id)
                ->where('semester_id', $semester->id)
                ->first();

            $scoreSem = $na?->nilai_akhir;
            $status = $na?->status ?? 'belum_lengkap';
            $predikat = $na?->predikat ?? $this->calculator->resolvePredikat($scoreSem, $kkm, $settings);
            $descTinggi = $na?->deskripsi_tertinggi ?? 'Nilai belum lengkap, silakan hubungi guru yang bersangkutan.';
            $descRendah = $na?->deskripsi_terendah ?? '';
            $descManual = $na?->deskripsi_manual;

            $mapelObj = new Mapel([
                'id' => 'mapil_' . $mapil->id,
                'kode' => $mapil->kode,
                'nama' => $mapil->nama,
                'kkm_default' => $kkm,
                'urutan' => 100 + $mapil->id,
            ]);

            $rows->push((object) [
                'is_seni' => false,
                'is_mapil' => true,
                'mapil_id' => $mapil->id,
                'mapel' => $mapelObj,
                'mapel_nama' => $mapil->nama,
                'kkm' => $kkm,
                'status' => $status,
                'kelompok' => null,
                'kelompok_kode' => 'C',
                'kelompok_nama' => 'Mata Pelajaran Pilihan',
                'kelompok_urutan' => 3,
                'mapel_urutan' => 100 + $mapil->id,
                'nilai_akhir' => $scoreSem,
                'predikat' => $predikat,
                'deskripsi_tertinggi' => $descTinggi,
                'deskripsi_terendah' => $descRendah,
                'deskripsi_manual' => $descManual,
            ]);
        }

        return $rows;
    }
}
