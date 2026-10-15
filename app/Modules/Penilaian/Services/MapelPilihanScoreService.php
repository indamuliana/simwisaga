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
use App\Modules\Penilaian\Models\NilaiMapelPilihanSumatif;
use Illuminate\Support\Collection;

class MapelPilihanScoreService
{
    /**
     * Hitung dan simpan rekap nilai akhir Mapel Pilihan untuk seluruh anggota siswa
     */
    public function calculateAndSaveRekap(MapelPilihan $mapelPilihan, Semester $semester): Collection
    {
        $kkm = (float) ($mapelPilihan->kkm_default ?? 75);
        $isGenap = str_contains(strtolower($semester->semester), 'genap');

        $block1Key = $isGenap ? '9M3' : '9M1';
        $block2Key = $isGenap ? '9M4' : '9M2';

        $ahBlock1 = $isGenap ? ['AH 5', 'AH 6'] : ['AH 1', 'AH 2'];
        $ahBlock2 = $isGenap ? ['AH 7', 'AH 8'] : ['AH 3', 'AH 4'];

        // Ambil Tujuan Pembelajaran untuk Mapel Pilihan dan Semester ini
        $tps = MapelPilihanTp::where('mapel_pilihan_id', $mapelPilihan->id)
            ->where('semester_id', $semester->id)
            ->orderBy('urutan')
            ->get();

        $ahMapping = [];
        foreach ($tps as $tp) {
            $ah = $tp->kode_ah ?: $tp->alokasi_ah;
            if ($ah) {
                $ahMapping[$ah][] = $tp;
            }
        }

        $tpsBlock1 = collect();
        foreach ($ahBlock1 as $ah) {
            if (isset($ahMapping[$ah])) {
                $tpsBlock1 = $tpsBlock1->merge($ahMapping[$ah]);
            }
        }

        $tpsBlock2 = collect();
        foreach ($ahBlock2 as $ah) {
            if (isset($ahMapping[$ah])) {
                $tpsBlock2 = $tpsBlock2->merge($ahMapping[$ah]);
            }
        }

        // Ambil seluruh siswa anggota aktif
        $anggotas = MapelPilihanAnggota::with('siswa')
            ->where('mapel_pilihan_id', $mapelPilihan->id)
            ->where('status', 'aktif')
            ->get();

        $siswaIds = $anggotas->pluck('siswa_id')->all();

        // Ambil nilai formatif yang tersimpan
        $formatifs = NilaiMapelPilihanFormatif::where('mapel_pilihan_id', $mapelPilihan->id)
            ->where('semester_id', $semester->id)
            ->whereIn('siswa_id', $siswaIds)
            ->get()
            ->groupBy('siswa_id');

        // Ambil nilai sumatif yang tersimpan
        $sumatifs = NilaiMapelPilihanSumatif::where('mapel_pilihan_id', $mapelPilihan->id)
            ->where('semester_id', $semester->id)
            ->whereIn('siswa_id', $siswaIds)
            ->get()
            ->keyBy('siswa_id');

        // Ambil rekap lama (untuk mempertahankan deskripsi manual yang sudah disunting)
        $existingAkhirs = NilaiMapelPilihanAkhir::where('mapel_pilihan_id', $mapelPilihan->id)
            ->where('semester_id', $semester->id)
            ->whereIn('siswa_id', $siswaIds)
            ->get()
            ->keyBy('siswa_id');

        $computedResults = collect();

        foreach ($anggotas as $anggota) {
            $siswaId = $anggota->siswa_id;
            $existingNa = $existingAkhirs->get($siswaId);
            $currentData9m = $existingNa?->data_9m ?? [];

            // Susun map nilai AH siswa
            $siswaFormatifs = $formatifs->get($siswaId, collect())->keyBy('kode_ah');
            $ahScoresMap = [];
            foreach (['AH 1', 'AH 2', 'AH 3', 'AH 4', 'AH 5', 'AH 6', 'AH 7', 'AH 8'] as $ahCode) {
                $ahScoresMap[$ahCode] = $siswaFormatifs->get($ahCode)?->nilai;
            }

            // Ambil sumatif
            $siswaSumatif = $sumatifs->get($siswaId);
            $sumatifVal1 = $isGenap ? $siswaSumatif?->sts_2 : $siswaSumatif?->sts_1;
            $sumatifVal2 = $isGenap ? $siswaSumatif?->sat : $siswaSumatif?->sas;

            // Evaluasi Blok 1 (9M1 atau 9M3)
            $resBlock1 = $this->evaluate9MBlock(
                $siswaId,
                $ahBlock1,
                $tpsBlock1,
                $sumatifVal1,
                $kkm,
                $ahScoresMap,
                $currentData9m[$block1Key]['deskripsi_manual'] ?? null
            );

            // Evaluasi Blok 2 (9M2 atau 9M4)
            $resBlock2 = $this->evaluate9MBlock(
                $siswaId,
                $ahBlock2,
                $tpsBlock2,
                $sumatifVal2,
                $kkm,
                $ahScoresMap,
                $currentData9m[$block2Key]['deskripsi_manual'] ?? null
            );

            $currentData9m[$block1Key] = $resBlock1;
            $currentData9m[$block2Key] = $resBlock2;

            // Evaluasi Semester Penuh
            $allAhThisSem = array_merge($ahBlock1, $ahBlock2);
            $activeAhThisSem = [];
            foreach ($allAhThisSem as $ah) {
                if (!empty($ahMapping[$ah])) {
                    $activeAhThisSem[] = $ah;
                }
            }

            $filledAhs = [];
            $semHasEmpty = false;
            $semHasBelowKkm = false;

            if (!empty($activeAhThisSem)) {
                foreach ($activeAhThisSem as $ah) {
                    $val = $ahScoresMap[$ah] ?? null;
                    if ($val === null || $val === '' || !is_numeric($val)) {
                        $semHasEmpty = true;
                    } else {
                        $num = (float) $val;
                        $filledAhs[] = $num;
                        if ($num < $kkm) {
                            $semHasBelowKkm = true;
                        }
                    }
                }
            } else {
                $semHasEmpty = true;
            }

            $stsVal = ($sumatifVal1 !== null && is_numeric($sumatifVal1)) ? (float) $sumatifVal1 : null;
            $sasVal = ($sumatifVal2 !== null && is_numeric($sumatifVal2)) ? (float) $sumatifVal2 : null;

            if ($stsVal === null || $sasVal === null) {
                $semHasEmpty = true;
            } else {
                if ($stsVal < $kkm || $sasVal < $kkm) {
                    $semHasBelowKkm = true;
                }
            }

            $rataFormatifSem = !empty($filledAhs) ? round(array_sum($filledAhs) / count($filledAhs), 2) : null;

            if ($semHasEmpty || $semHasBelowKkm) {
                $nilaiAkhirSem = null;
                $statusSem = $semHasBelowKkm ? 'belum_kompeten' : 'belum_lengkap';
            } else {
                $nilaiAkhirSem = round(($rataFormatifSem * 0.5) + (($stsVal ?? 0) * 0.25) + (($sasVal ?? 0) * 0.25), 2);
                $statusSem = 'kompeten';
            }

            // Deskripsi semester penuh
            if ($statusSem === 'belum_lengkap' || ($resBlock2['status'] ?? '') === 'belum_lengkap' || ($resBlock1['status'] ?? '') === 'belum_lengkap') {
                $semDescTinggi = 'Nilai belum lengkap, silakan hubungi guru yang bersangkutan.';
                $semDescRendah = '';
            } elseif ($statusSem === 'belum_kompeten' || ($resBlock2['status'] ?? '') === 'belum_kompeten' || ($resBlock1['status'] ?? '') === 'belum_kompeten') {
                $semDescTinggi = 'Nilai belum kompeten, silakan hubungi guru yang bersangkutan.';
                $semDescRendah = '';
            } else {
                $semDescTinggi = $resBlock2['deskripsi_tertinggi'] ?: $resBlock1['deskripsi_tertinggi'];
                $semDescRendah = $resBlock2['deskripsi_terendah'] ?: $resBlock1['deskripsi_terendah'];
            }

            $na = NilaiMapelPilihanAkhir::updateOrCreate(
                [
                    'mapel_pilihan_id' => $mapelPilihan->id,
                    'siswa_id' => $siswaId,
                    'semester_id' => $semester->id,
                ],
                [
                    'nilai_akhir' => $nilaiAkhirSem,
                    'data_9m' => $currentData9m,
                    'deskripsi_tertinggi' => $semDescTinggi,
                    'deskripsi_terendah' => $semDescRendah,
                    'deskripsi_manual' => $resBlock2['deskripsi_manual'] ?: ($resBlock1['deskripsi_manual'] ?? null),
                    'status' => $statusSem,
                ]
            );

            $computedResults->put($siswaId, $na);
        }

        return $computedResults;
    }

    /**
     * Evaluasi satu blok 9 minggu untuk satu siswa Mapel Pilihan
     */
    public function evaluate9MBlock(
        int $siswaId,
        array $ahCodes,
        Collection $tpsInBlock,
        $sumatifVal,
        float $kkm,
        array $ahScoresMap,
        ?string $existingManualOverride = null
    ): array {
        // 1. Identifikasi AH aktif pada blok ini
        $activeAhCodes = [];
        foreach ($ahCodes as $ah) {
            $tpsForAh = $tpsInBlock->filter(fn($tp) => ($tp->kode_ah ?: $tp->alokasi_ah) === $ah);
            if ($tpsForAh->isNotEmpty()) {
                $activeAhCodes[] = $ah;
            }
        }

        // Kumpulkan nilai AH aktif & periksa kondisi kosong / di bawah KKM
        $ahScores = [];
        $hasEmptyAh = false;
        $hasBelowKkm = false;

        if (!empty($activeAhCodes)) {
            foreach ($activeAhCodes as $ah) {
                $score = $ahScoresMap[$ah] ?? null;
                if ($score === null || $score === '' || !is_numeric($score)) {
                    $hasEmptyAh = true;
                } else {
                    $numScore = (float) $score;
                    $ahScores[$ah] = $numScore;
                    if ($numScore < $kkm) {
                        $hasBelowKkm = true;
                    }
                }
            }
        }

        // 2. Evaluasi Sumatif (STS atau SAS atau SAT)
        $sumatif = null;
        $hasSumatif = false;
        if ($sumatifVal !== null && $sumatifVal !== '' && is_numeric($sumatifVal)) {
            $sumatif = (float) $sumatifVal;
            $hasSumatif = true;
            if ($sumatif < $kkm) {
                $hasBelowKkm = true;
            }
        }

        // Kasus: Belum ada nilai sama sekali
        $hasAnyScoreEntered = !empty($ahScores) || $hasSumatif;
        if (!$hasAnyScoreEntered) {
            return [
                'status' => 'belum_ada_nilai',
                'rata_formatif' => null,
                'sumatif' => null,
                'nilai_akhir' => null,
                'deskripsi_tertinggi' => '',
                'deskripsi_terendah' => '',
                'deskripsi_manual' => $existingManualOverride ?: null,
            ];
        }

        $rataFormatif = !empty($ahScores) ? round(array_sum($ahScores) / count($ahScores), 2) : null;
        $hasEmptyScore = $hasEmptyAh || !$hasSumatif || (empty($activeAhCodes) && $tpsInBlock->isNotEmpty());

        if ($hasEmptyScore) {
            return [
                'status' => 'belum_lengkap',
                'rata_formatif' => $rataFormatif,
                'sumatif' => $sumatif,
                'nilai_akhir' => null,
                'deskripsi_tertinggi' => 'Nilai belum lengkap, silakan hubungi guru yang bersangkutan.',
                'deskripsi_terendah' => '',
                'deskripsi_manual' => $existingManualOverride ?: null,
            ];
        }

        if ($hasBelowKkm) {
            return [
                'status' => 'belum_kompeten',
                'rata_formatif' => $rataFormatif,
                'sumatif' => $sumatif,
                'nilai_akhir' => null,
                'deskripsi_tertinggi' => 'Nilai belum kompeten, silakan hubungi guru yang bersangkutan.',
                'deskripsi_terendah' => '',
                'deskripsi_manual' => $existingManualOverride ?: null,
            ];
        }

        // Semua lengkap dan >= KKM
        $nilaiAkhir = null;
        if ($rataFormatif !== null && $sumatif !== null) {
            $nilaiAkhir = round(($rataFormatif * 0.5) + ($sumatif * 0.5), 2);
        } elseif ($rataFormatif !== null) {
            $nilaiAkhir = $rataFormatif;
        } elseif ($sumatif !== null) {
            $nilaiAkhir = $sumatif;
        }

        // Bangun narasi capaian TP
        $tpScores = [];
        foreach ($tpsInBlock as $tp) {
            $ah = $tp->kode_ah ?: $tp->alokasi_ah;
            $score = $ahScoresMap[$ah] ?? null;
            if ($score !== null && $score !== '' && is_numeric($score)) {
                $tpScores[$tp->id] = (float) $score;
            }
        }

        $descTinggi = '';
        $descRendah = '';

        if (!empty($tpScores)) {
            $countTps = count($tpScores);

            if ($countTps === 1) {
                $onlyTpId = array_key_first($tpScores);
                $onlyScore = $tpScores[$onlyTpId];
                $onlyTp = $tpsInBlock->firstWhere('id', $onlyTpId);
                $tpDesc = lcfirst(rtrim($onlyTp->deskripsi_tp ?? $onlyTp->deskripsi ?? '', '.'));

                if ($onlyScore >= 85) {
                    $descTinggi = 'Menunjukkan penguasaan yang sangat baik dalam ' . $tpDesc . '.';
                } elseif ($onlyScore >= $kkm) {
                    $descTinggi = 'Menunjukkan penguasaan yang baik dalam ' . $tpDesc . '.';
                } else {
                    $descTinggi = 'Cukup menguasai dalam ' . $tpDesc . '.';
                }

                if ($onlyScore >= $kkm) {
                    $descRendah = 'Perlu pemantapan lebih lanjut dalam ' . $tpDesc . ' secara berkelanjutan.';
                } else {
                    $descRendah = 'Perlu bimbingan lebih lanjut dalam ' . $tpDesc . '.';
                }
            } else {
                $maxScore = max($tpScores);
                $minScore = min($tpScores);

                if ($maxScore == $minScore) {
                    $tpIds = array_keys($tpScores);
                    $firstTpId = $tpIds[0];
                    $otherTpIds = array_slice($tpIds, 1);

                    $firstTp = $tpsInBlock->firstWhere('id', $firstTpId);
                    $firstDesc = lcfirst(rtrim($firstTp->deskripsi_tp ?? $firstTp->deskripsi ?? '', '.'));

                    if ($maxScore >= 85) {
                        $descTinggi = 'Menunjukkan penguasaan yang sangat baik dalam ' . $firstDesc . '.';
                    } elseif ($maxScore >= $kkm) {
                        $descTinggi = 'Menunjukkan penguasaan yang baik dalam ' . $firstDesc . '.';
                    } else {
                        $descTinggi = 'Cukup menguasai dalam ' . $firstDesc . '.';
                    }

                    $otherTps = $tpsInBlock->whereIn('id', $otherTpIds);
                    $tpOtherDesc = $otherTps->map(fn($t) => lcfirst(rtrim($t->deskripsi_tp ?? $t->deskripsi ?? '', '.')))->values()->all();
                    $joinedOtherDesc = count($tpOtherDesc) > 1 ? implode(' dan ', $tpOtherDesc) : ($tpOtherDesc[0] ?? '');

                    if ($minScore >= $kkm) {
                        $descRendah = 'Perlu pemantapan dalam ' . $joinedOtherDesc . '.';
                    } else {
                        $descRendah = 'Perlu bimbingan dalam ' . $joinedOtherDesc . '.';
                    }
                } else {
                    $highestTpIds = array_keys(array_filter($tpScores, fn($s) => $s == $maxScore));
                    $lowestTpIds = array_keys(array_filter($tpScores, fn($s) => $s == $minScore));

                    $highestTps = $tpsInBlock->whereIn('id', $highestTpIds);
                    $tpDescTinggi = $highestTps->map(fn($t) => lcfirst(rtrim($t->deskripsi_tp ?? $t->deskripsi ?? '', '.')))->values()->all();
                    $joinedDescTinggi = count($tpDescTinggi) > 1 ? implode(' serta ', $tpDescTinggi) : ($tpDescTinggi[0] ?? '');

                    if ($maxScore >= 85) {
                        $descTinggi = 'Menunjukkan penguasaan yang sangat baik dalam ' . $joinedDescTinggi . '.';
                    } elseif ($maxScore >= $kkm) {
                        $descTinggi = 'Menunjukkan penguasaan yang baik dalam ' . $joinedDescTinggi . '.';
                    } else {
                        $descTinggi = 'Cukup menguasai dalam ' . $joinedDescTinggi . '.';
                    }

                    $lowestTps = $tpsInBlock->whereIn('id', $lowestTpIds);
                    $tpDescRendah = $lowestTps->map(fn($t) => lcfirst(rtrim($t->deskripsi_tp ?? $t->deskripsi ?? '', '.')))->values()->all();
                    $joinedDescRendah = count($tpDescRendah) > 1 ? implode(' dan ', $tpDescRendah) : ($tpDescRendah[0] ?? '');

                    if ($minScore >= $kkm) {
                        $descRendah = 'Perlu pemantapan dalam ' . $joinedDescRendah . '.';
                    } else {
                        $descRendah = 'Perlu bimbingan dalam ' . $joinedDescRendah . '.';
                    }
                }
            }
        }

        return [
            'status' => 'kompeten',
            'rata_formatif' => $rataFormatif,
            'sumatif' => $sumatif,
            'nilai_akhir' => $nilaiAkhir,
            'deskripsi_tertinggi' => $descTinggi,
            'deskripsi_terendah' => $descRendah,
            'deskripsi_manual' => $existingManualOverride ?: null,
        ];
    }

    /**
     * Mengambil seluruh data nilai Mapel Pilihan untuk satu siswa pada Rapor Wikrama (9 Minggu)
     */
    public function getStudentRecordsForWikrama(Siswa $siswa, Semester $semester, string $periodKey): Collection
    {
        $taId = $semester->tahun_ajaran_id;

        // Ambil semua keanggotaan Mapil aktif siswa
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
        $ahKeys = match (strtoupper(trim($periodKey))) {
            '9M1', 'STS 1', 'PTS 1' => ['AH 1', 'AH 2'],
            '9M2', 'SAS', 'PAS 1' => ['AH 3', 'AH 4'],
            '9M3', 'STS 2', 'PTS 2' => ['AH 5', 'AH 6'],
            '9M4', 'SAT', 'PAT', 'PAS 2' => ['AH 7', 'AH 8'],
            default => $isGenap ? ['AH 5', 'AH 6'] : ['AH 1', 'AH 2'],
        };
        $normKey = match (strtoupper(trim($periodKey))) {
            '9M1', 'STS 1', 'PTS 1' => '9M1',
            '9M2', 'SAS', 'PAS 1' => '9M2',
            '9M3', 'STS 2', 'PTS 2' => '9M3',
            '9M4', 'SAT', 'PAT', 'PAS 2' => '9M4',
            default => $isGenap ? '9M3' : '9M1',
        };

        $rows = collect();

        foreach ($anggotas as $anggota) {
            $mapil = $anggota->mapelPilihan;
            if (!$mapil || !$mapil->is_active) {
                continue;
            }

            $kkm = (float) ($mapil->kkm_default ?? 75);

            // Ambil formatif untuk periode ini
            $formatifs = NilaiMapelPilihanFormatif::where('mapel_pilihan_id', $mapil->id)
                ->where('siswa_id', $siswa->id)
                ->where('semester_id', $semester->id)
                ->whereIn('kode_ah', $ahKeys)
                ->get()
                ->keyBy('kode_ah');

            $ahScores = [];
            foreach ($ahKeys as $ah) {
                $val = $formatifs->get($ah)?->nilai;
                $ahScores[$ah] = ($val !== null && $val !== '' && is_numeric($val)) ? (float) $val : null;
            }

            // Ambil sumatif
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

            // Ambil Nilai Akhir
            $na = NilaiMapelPilihanAkhir::where('mapel_pilihan_id', $mapil->id)
                ->where('siswa_id', $siswa->id)
                ->where('semester_id', $semester->id)
                ->first();

            $block9M = $na?->data_9m[$normKey] ?? null;

            $score9M = null;
            $descTinggi = null;
            $descRendah = null;
            $descManual = null;
            $status = 'belum_lengkap';

            if ($block9M) {
                $status = $block9M['status'] ?? 'belum_lengkap';
                $score9M = $block9M['nilai_akhir'] ?? null;
                $descTinggi = $block9M['deskripsi_tertinggi'] ?? null;
                $descRendah = $block9M['deskripsi_terendah'] ?? null;
                $descManual = $block9M['deskripsi_manual'] ?? null;
            } else {
                // Evaluasi on-the-fly jika belum pernah calculateRekap
                $tps = MapelPilihanTp::where('mapel_pilihan_id', $mapil->id)
                    ->where('semester_id', $semester->id)
                    ->whereIn('kode_ah', $ahKeys)
                    ->get();

                $eval = $this->evaluate9MBlock($siswa->id, $ahKeys, $tps, $sumatifVal, $kkm, $ahScores);
                $status = $eval['status'];
                $score9M = $eval['nilai_akhir'];
                $descTinggi = $eval['deskripsi_tertinggi'];
                $descRendah = $eval['deskripsi_terendah'];
            }

            // Pseudo-model Mapel untuk keseragaman view
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
                'ah_scores' => $ahScores,
                'sumatif' => $sumatifVal,
                'nilai_akhir' => $score9M,
                'deskripsi_tertinggi' => $descTinggi,
                'deskripsi_terendah' => $descRendah,
                'deskripsi_manual' => $descManual,
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
                'deskripsi_tertinggi' => $descTinggi,
                'deskripsi_terendah' => $descRendah,
                'deskripsi_manual' => $descManual,
            ]);
        }

        return $rows;
    }
}
