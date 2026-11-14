<?php

namespace App\Modules\Penilaian\Services;

use App\Modules\Penilaian\Models\PengaturanPenilaian;
use Illuminate\Support\Collection;

class PenilaianScoreCalculator
{
    protected ?PengaturanPenilaian $settings = null;
    protected array $sortedIntervalsCache = [];

    public function __construct(?PengaturanPenilaian $settings = null)
    {
        $this->settings = $settings;
    }

    public function getSettings(?int $tahunAjaranId = null): PengaturanPenilaian
    {
        if (!$this->settings) {
            $this->settings = PengaturanPenilaian::getActive($tahunAjaranId);
        }
        return $this->settings;
    }

    protected function getSortedIntervals(PengaturanPenilaian $cfg): array
    {
        $id = $cfg->id ?? 'default';
        if (!isset($this->sortedIntervalsCache[$id])) {
            $intervals = $cfg->predikat_intervals ?: PengaturanPenilaian::defaultIntervals();
            usort($intervals, fn($a, $b) => ($b['min'] ?? 0) <=> ($a['min'] ?? 0));
            $this->sortedIntervalsCache[$id] = $intervals;
        }
        return $this->sortedIntervalsCache[$id];
    }

    /**
     * Resolves the predikat capaian string based on score and configured intervals
     */
    public function resolvePredikat(?float $score, float $kkm, ?PengaturanPenilaian $settings = null): string
    {
        if ($score === null) {
            return 'Belum Lengkap';
        }

        if ($score < $kkm) {
            return 'Belum Kompeten';
        }

        $cfg = $settings ?: $this->getSettings();
        $intervals = $this->getSortedIntervals($cfg);

        foreach ($intervals as $interval) {
            $min = (float) ($interval['min'] ?? 0);
            $max = (float) ($interval['max'] ?? 100);
            if ($score >= $min && $score <= $max) {
                return $interval['label'] ?? 'Kompeten';
            }
        }

        return 'Mulai Berkembang';
    }

    /**
     * Calculate 9M Block evaluation for a student.
     *
     * @param array $tpScores Map of [tp_id => score] or list of scores
     * @param float|null $nhScore Subjective daily score (default 100)
     * @param float|null $sumatifScore STS/SAS score
     * @param float $kkm Minimum competency score
     * @param Collection $tps Collection of TujuanPembelajaran objects in this block
     * @param string|null $manualNarrative Manual override text if any
     * @param PengaturanPenilaian|null $settings Custom settings instance
     */
    public function calculate9MBlock(
        array $tpScores,
        ?float $nhScore,
        ?float $sumatifScore,
        float $kkm,
        Collection $tps,
        ?string $manualNarrative = null,
        ?PengaturanPenilaian $settings = null
    ): array {
        $cfg = $settings ?: $this->getSettings();
        $bobotTp = $cfg->bobot_tp ?? 50;
        $bobotSts = $cfg->bobot_sts ?? 30;
        $bobotNh = $cfg->bobot_nh ?? 20;

        // Default NH to 100 if null and other grades entered? Or require explicit NH?
        // User stated: "Nilai harian ini adalah nilai subjektif guru secara default 100"
        $effectiveNh = $nhScore !== null ? (float) $nhScore : 100.0;

        // Check if there are any scores entered at all
        $validTpScores = [];
        $hasAnyInput = ($nhScore !== null) || ($sumatifScore !== null);

        foreach ($tps as $tp) {
            $val = $tpScores[$tp->id] ?? null;
            if ($val !== null && $val !== '' && is_numeric($val)) {
                $num = (float) $val;
                $validTpScores[$tp->id] = $num;
                $hasAnyInput = true;
            }
        }

        // Check if completely untouched
        if (!$hasAnyInput && empty($validTpScores) && $sumatifScore === null) {
            return [
                'status' => 'belum_ada_nilai',
                'rata_tp' => null,
                'rata_formatif' => null,
                'nh' => $effectiveNh,
                'sumatif' => null,
                'sts' => null,
                'nilai_akhir' => null,
                'na' => null,
                'predikat' => 'Belum Lengkap',
                'deskripsi_tertinggi' => '',
                'deskripsi_terendah' => '',
                'deskripsi_manual' => $manualNarrative,
                'narasi_capaian' => $manualNarrative ?: '',
            ];
        }

        // Check completeness: all defined TPs in this block must be graded, NH must be graded, and STS must be graded
        $missingItems = [];
        foreach ($tps as $tp) {
            if (!isset($validTpScores[$tp->id])) {
                $missingItems[] = $tp->kode ?: ('TP ' . $tp->id);
            }
        }
        if ($nhScore === null || $nhScore === '') {
            $missingItems[] = 'NH (Harian)';
        }
        if ($sumatifScore === null || $sumatifScore === '') {
            $missingItems[] = 'Sumatif';
        }

        $hasBelowKkm = false;

        // Check TP scores against KKM
        foreach ($validTpScores as $s) {
            if ($s < $kkm) {
                $hasBelowKkm = true;
                break;
            }
        }

        // Check STS against KKM
        if ($sumatifScore !== null && is_numeric($sumatifScore)) {
            if ((float) $sumatifScore < $kkm) {
                $hasBelowKkm = true;
            }
        }

        // Check NH against KKM
        if ($effectiveNh < $kkm) {
            $hasBelowKkm = true;
        }

        $rataTp = count($validTpScores) > 0 ? round(array_sum($validTpScores) / count($validTpScores), 2) : null;
        $numSumatif = ($sumatifScore !== null && is_numeric($sumatifScore)) ? (float) $sumatifScore : null;

        if (!empty($missingItems)) {
            $status = 'belum_lengkap';
            $predikat = 'Belum Lengkap';
            $nilaiAkhir = null;
            $deskripsiTinggi = 'Segera selesaikan nilai ' . implode(', ', $missingItems) . '.';
            $deskripsiRendah = '';
        } elseif ($hasBelowKkm) {
            $status = 'belum_kompeten';
            $predikat = 'Belum Kompeten';
            $nilaiAkhir = null;
            $deskripsiTinggi = 'Nilai belum kompeten, silakan hubungi guru yang bersangkutan.';
            $deskripsiRendah = '';
        } else {
            $status = 'kompeten';
            // Formula: ((Rata TP * bobot_tp) + (Sumatif * bobot_sts) + (NH * bobot_nh)) / 100
            $totalBobot = $bobotTp + $bobotSts + $bobotNh;
            if ($totalBobot <= 0) {
                $totalBobot = 100;
            }

            $rawNa = (($rataTp * $bobotTp) + ($numSumatif * $bobotSts) + ($effectiveNh * $bobotNh)) / $totalBobot;
            $nilaiAkhir = round($rawNa, 2);
            $predikat = $this->resolvePredikat($nilaiAkhir, $kkm, $cfg);

            $narratives = $this->generateKmNarration($validTpScores, $tps, $kkm);
            $deskripsiTinggi = $narratives['deskripsi_tertinggi'];
            $deskripsiRendah = $narratives['deskripsi_terendah'];
        }

        $narasiCapaian = $manualNarrative ?: trim($deskripsiTinggi . ($deskripsiRendah ? ' ' . $deskripsiRendah : ''));

        return [
            'status' => $status,
            'rata_tp' => $rataTp,
            'rata_formatif' => $rataTp,
            'nh' => $effectiveNh,
            'sumatif' => $numSumatif,
            'sts' => $numSumatif,
            'nilai_akhir' => $nilaiAkhir,
            'na' => $nilaiAkhir,
            'predikat' => $predikat,
            'deskripsi_tertinggi' => $deskripsiTinggi,
            'deskripsi_terendah' => $deskripsiRendah,
            'deskripsi_manual' => $manualNarrative,
            'narasi_capaian' => $narasiCapaian,
        ];
    }

    /**
     * Format penggabungan teks daftar deskripsi TP dengan koma dan 'serta'
     */
    protected function formatJoinedDescriptions(array $descs): string
    {
        $count = count($descs);
        if ($count === 0) return '';
        if ($count === 1) return $descs[0];
        if ($count === 2) return $descs[0] . ' serta ' . $descs[1];

        $last = array_pop($descs);
        return implode(', ', $descs) . ', serta ' . $last;
    }

    /**
     * Generate Kurikulum Merdeka 2-line narration:
     * 1. Menunjukkan penguasaan yang sangat baik / baik / cukup dalam ...
     * 2. Perlu pemantapan / peningkatan / bimbingan dalam ...
     */
    public function generateKmNarration(array $validTpScores, Collection $tps, float $kkm = 75): array
    {
        if (empty($validTpScores)) {
            return ['deskripsi_tertinggi' => '', 'deskripsi_terendah' => ''];
        }

        $cleanDesc = function ($raw) {
            $c = preg_replace('/^(Peserta didik mampu|Siswa mampu|Mampu|Dapat)\s+/i', '', trim($raw ?? ''));
            return lcfirst(rtrim($c, '.'));
        };

        // Helper pembuka frasa capaian tertinggi berdasarkan nilai TP
        $getPrefixTinggi = function (float $score) use ($kkm): string {
            if ($score >= 90) return 'Menunjukkan penguasaan yang sangat baik dalam ';
            if ($score >= 80) return 'Menunjukkan penguasaan yang baik dalam ';
            if ($score >= $kkm) return 'Cukup menguasai dalam ';
            return 'Perlu bimbingan dalam ';
        };

        // Helper pembuka frasa capaian terendah / perlu peningkatan
        $getPrefixRendah = function (float $score) use ($kkm): string {
            if ($score >= 85) return 'Perlu pemantapan dalam ';
            if ($score >= $kkm) return 'Perlu peningkatan dan latihan dalam ';
            return 'Perlu bimbingan intensif dalam ';
        };

        // KASUS 1: Hanya ada 1 TP dalam 1 periode rapor
        if (count($validTpScores) === 1) {
            $onlyTpId = array_key_first($validTpScores);
            $onlyScore = (float) $validTpScores[$onlyTpId];
            $onlyTp = $tps->firstWhere('id', $onlyTpId);
            $onlyDesc = $cleanDesc($onlyTp->deskripsi ?? $onlyTp->deskripsi_tp ?? '');

            if ($onlyScore >= $kkm) {
                // Baris tunggal capaian tertinggi tanpa baris bimbingan kontradiktif jika kompeten
                return [
                    'deskripsi_tertinggi' => $getPrefixTinggi($onlyScore) . $onlyDesc . '.',
                    'deskripsi_terendah' => '',
                ];
            } else {
                // Baris tunggal jika di bawah KKM
                return [
                    'deskripsi_tertinggi' => '',
                    'deskripsi_terendah' => $getPrefixRendah($onlyScore) . $onlyDesc . '.',
                ];
            }
        }

        // KASUS 2: Ada lebih dari 1 TP (2, 3, 4, dst)
        arsort($validTpScores);

        $bestScore = (float) reset($validTpScores);
        $worstScore = (float) end($validTpScores);

        // KASUS 2A: Seluruh TP bernilai sama
        if ($bestScore === $worstScore) {
            $allDescs = [];
            foreach (array_keys($validTpScores) as $tpId) {
                $tp = $tps->firstWhere('id', $tpId);
                if ($tp) {
                    $allDescs[] = $cleanDesc($tp->deskripsi ?? $tp->deskripsi_tp);
                }
            }
            $allDescs = array_values(array_filter($allDescs));

            if ($bestScore >= $kkm) {
                return [
                    'deskripsi_tertinggi' => $getPrefixTinggi($bestScore) . $this->formatJoinedDescriptions($allDescs) . '.',
                    'deskripsi_terendah' => '',
                ];
            } else {
                return [
                    'deskripsi_tertinggi' => '',
                    'deskripsi_terendah' => $getPrefixRendah($bestScore) . $this->formatJoinedDescriptions($allDescs) . '.',
                ];
            }
        }

        // KASUS 2B: Nilai bervariasi (terdapat skor tertinggi, menengah, dan terendah)
        // 1. Ambil TP dengan skor terendah untuk deskripsi_terendah / perlu peningkatan
        $worstTpIds = array_keys(array_filter($validTpScores, fn($s) => (float)$s == $worstScore));
        $worstDescs = [];
        foreach ($worstTpIds as $tpId) {
            $tp = $tps->firstWhere('id', $tpId);
            if ($tp) {
                $worstDescs[] = $cleanDesc($tp->deskripsi ?? $tp->deskripsi_tp);
            }
        }
        $worstDescs = array_values(array_filter($worstDescs));
        $descRendah = !empty($worstDescs)
            ? $getPrefixRendah($worstScore) . $this->formatJoinedDescriptions($worstDescs) . '.'
            : '';

        // 2. Ambil sisa TP (skor di atas terendah) dan kelompokkan berdasarkan tier kemampuan
        $remainingTpScores = array_filter($validTpScores, fn($s) => (float)$s > $worstScore);
        
        $tierGroups = [
            'sangat_baik' => ['prefix' => 'Menunjukkan penguasaan yang sangat baik dalam ', 'tp_ids' => []],
            'baik'        => ['prefix' => 'Menunjukkan penguasaan yang baik dalam ',        'tp_ids' => []],
            'cukup'       => ['prefix' => 'Cukup menguasai dalam ',                         'tp_ids' => []],
            'bimbingan'   => ['prefix' => 'Perlu bimbingan dalam ',                         'tp_ids' => []],
        ];

        foreach ($remainingTpScores as $tpId => $score) {
            $num = (float) $score;
            if ($num >= 90) {
                $tierGroups['sangat_baik']['tp_ids'][] = $tpId;
            } elseif ($num >= 80) {
                $tierGroups['baik']['tp_ids'][] = $tpId;
            } elseif ($num >= $kkm) {
                $tierGroups['cukup']['tp_ids'][] = $tpId;
            } else {
                $tierGroups['bimbingan']['tp_ids'][] = $tpId;
            }
        }

        $linesTinggi = [];
        foreach ($tierGroups as $tier) {
            if (empty($tier['tp_ids'])) {
                continue;
            }

            $groupDescs = [];
            foreach ($tier['tp_ids'] as $tpId) {
                $tp = $tps->firstWhere('id', $tpId);
                if ($tp) {
                    $groupDescs[] = $cleanDesc($tp->deskripsi ?? $tp->deskripsi_tp);
                }
            }
            $groupDescs = array_values(array_filter($groupDescs));

            if (!empty($groupDescs)) {
                $linesTinggi[] = $tier['prefix'] . $this->formatJoinedDescriptions($groupDescs) . '.';
            }
        }

        $descTinggi = implode("\n", $linesTinggi);

        return [
            'deskripsi_tertinggi' => $descTinggi,
            'deskripsi_terendah' => $descRendah,
        ];
    }

    /**
     * Calculate Semester from 2 9M blocks (e.g. 9M1 + 9M2 or 9M3 + 9M4)
     */
    public function calculateSemester(?array $block1, ?array $block2, float $kkm, ?PengaturanPenilaian $settings = null): array
    {
        $b1Status = $block1['status'] ?? 'belum_ada_nilai';
        $b2Status = $block2['status'] ?? 'belum_ada_nilai';

        if ($b1Status === 'belum_lengkap' || $b2Status === 'belum_lengkap') {
            $msg = '';
            if ($b1Status === 'belum_lengkap' && !empty($block1['deskripsi_tertinggi'])) {
                $msg = $block1['deskripsi_tertinggi'];
            } elseif ($b2Status === 'belum_lengkap' && !empty($block2['deskripsi_tertinggi'])) {
                $msg = $block2['deskripsi_tertinggi'];
            } else {
                $msg = 'Nilai belum lengkap, silakan hubungi guru yang bersangkutan.';
            }

            return [
                'status' => 'belum_lengkap',
                'nilai_akhir' => null,
                'predikat' => 'Belum Lengkap',
                'deskripsi' => $msg,
            ];
        }

        if ($b1Status === 'belum_kompeten' || $b2Status === 'belum_kompeten') {
            return [
                'status' => 'belum_kompeten',
                'nilai_akhir' => null,
                'predikat' => 'Belum Kompeten',
                'deskripsi' => 'Nilai belum kompeten, silakan hubungi guru yang bersangkutan.',
            ];
        }

        $na1 = $block1['nilai_akhir'] ?? null;
        $na2 = $block2['nilai_akhir'] ?? null;

        if ($na1 === null && $na2 === null) {
            return [
                'status' => 'belum_ada_nilai',
                'nilai_akhir' => null,
                'predikat' => 'Belum Lengkap',
                'deskripsi' => '',
            ];
        }

        $scores = array_filter([$na1, $na2], fn($v) => $v !== null);
        $naSemester = round(array_sum($scores) / count($scores), 2);
        $predikat = $this->resolvePredikat($naSemester, $kkm, $settings);

        // Descriptions combined
        $desc1 = $block1['narasi_capaian'] ?? '';
        $desc2 = $block2['narasi_capaian'] ?? '';
        $combinedDesc = trim($desc2 ?: $desc1);

        return [
            'status' => 'kompeten',
            'nilai_akhir' => $naSemester,
            'predikat' => $predikat,
            'deskripsi' => $combinedDesc,
        ];
    }
}
