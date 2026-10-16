<?php

namespace App\Modules\Penilaian\Services;

use App\Modules\Akademik\Models\Semester;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Models\NilaiAsesmenHarian;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\NilaiHarian;
use App\Modules\Penilaian\Models\NilaiSumatif;
use App\Modules\Penilaian\Models\PengaturanPenilaian;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class NilaiImporter
{
    public const COLUMN_MAPPING = [
        'ah1' => 'AH 1',
        'ah2' => 'AH 2',
        'ah3' => 'AH 3',
        'ah4' => 'AH 4',
        'ah5' => 'AH 5',
        'ah6' => 'AH 6',
        'ah7' => 'AH 7',
        'ah8' => 'AH 8',
        'sts1' => 'STS 1',
        'sts' => 'STS 1',
        'sas' => 'SAS',
        'pas' => 'SAS',
        'sts2' => 'STS 2',
        'sat' => 'SAT',
        'pat' => 'SAT',
    ];

    /**
     * Menyelesaikan identifikasi kolom nilai (TP, NH, Sumatif, atau Legacy AH)
     */
    public function resolveColumn(string $rawHeader, Collection $allTps, ?string $activePeriode = null): ?array
    {
        $rawTrimmed = trim($rawHeader);
        $clean = strtolower(str_replace([' ', '_', '-', '(', ')', '[', ']', ':', '.', ','], '', $rawTrimmed));

        if (empty($clean) || in_array($clean, ['no', 'nis', 'nipd', 'nisn', 'nomorinduk', 'nama', 'namasiswa', 'pesertadidik', 'rombel', 'kelas'])) {
            return null;
        }

        // 1. Cek apakah cocok dengan Tujuan Pembelajaran (TP) yang ada
        // a. Cek spesifik ID jika format [TP:12] atau tp_12
        if (preg_match('/(?:\[tp:|tp_?)(\d+)/i', $rawTrimmed, $m)) {
            $tpId = (int) $m[1];
            $matched = $allTps->firstWhere('id', $tpId);
            if ($matched) {
                return [
                    'type' => 'tp',
                    'key' => $matched->kode,
                    'tp_id' => $matched->id,
                    'tp_kode' => $matched->kode,
                    'target_periode' => $matched->target_periode,
                    'penugasan_id' => $matched->penugasan_mengajar_id,
                ];
            }
        }

        // b. Cek kecocokan kode TP persis (misal 'TP 1', 'TP 2', 'TP1')
        $matchedTp = $allTps->first(function ($t) use ($clean, $rawTrimmed) {
            $cleanTp = strtolower(str_replace([' ', '_', '-', '(', ')', '[', ']', ':', '.', ','], '', $t->kode));
            return $cleanTp === $clean || strcasecmp(trim($t->kode), $rawTrimmed) === 0;
        });

        // c. Cek jika header diawali kode TP (misal 'TP 1: Memahami...', 'TP 1 - Lingkup...', 'TP 1 (9M1)')
        if (!$matchedTp && preg_match('/^(?:tp|tujuan\s*pembelajaran)[\s_-]*(\d+)/i', $rawTrimmed, $m)) {
            $num = (int) $m[1];
            $candidates = $allTps->filter(function ($t) use ($num) {
                preg_match('/\d+/', $t->kode, $cm);
                return (isset($cm[0]) && (int) $cm[0] === $num) || ($t->urutan == $num);
            });

            if ($candidates->count() === 1) {
                $matchedTp = $candidates->first();
            } elseif ($candidates->count() > 1) {
                // Saring berdasarkan periode jika ada di teks header
                if (preg_match('/9M[1-4]/i', $rawTrimmed, $pm)) {
                    $matchedTp = $candidates->firstWhere('target_periode', strtoupper($pm[0]));
                }
                if (!$matchedTp && $activePeriode) {
                    $matchedTp = $candidates->firstWhere('target_periode', $activePeriode);
                }
                if (!$matchedTp) {
                    $matchedTp = $candidates->first();
                }
            }
        }

        if ($matchedTp) {
            return [
                'type' => 'tp',
                'key' => $matchedTp->kode,
                'tp_id' => $matchedTp->id,
                'tp_kode' => $matchedTp->kode,
                'target_periode' => $matchedTp->target_periode,
                'penugasan_id' => $matchedTp->penugasan_mengajar_id,
            ];
        }

        // 2. Cek apakah Nilai Harian (NH)
        if (preg_match('/^(?:nh|nilai\s*harian|harian)(?:[\s_-]*\(?([1-4]|9m[1-4])\)?)?$/i', $rawTrimmed, $m)) {
            $p = null;
            if (!empty($m[1])) {
                $rawP = strtoupper($m[1]);
                $p = in_array($rawP, ['1', '2', '3', '4']) ? "9M{$rawP}" : $rawP;
            } else {
                $p = $activePeriode ?: '9M1';
            }
            return [
                'type' => 'nh',
                'key' => "NH {$p}",
                'target_periode' => $p,
                'semester' => in_array($p, ['9M3', '9M4']) ? 'genap' : 'ganjil',
            ];
        }

        // 3. Cek Sumatif (STS 1, SAS, STS 2, SAT)
        if (preg_match('/^(?:sts|pts)[\s_-]*1?$/i', $clean) || $clean === 'sumatiftengah1' || $clean === 'sumatiftengahsemester1') {
            return ['type' => 'sumatif', 'key' => 'STS 1', 'field' => 'sts', 'semester' => 'ganjil'];
        }
        if (preg_match('/^(?:sas|pas)[\s_-]*1?$/i', $clean) || $clean === 'sumatifakhir1' || $clean === 'sumatifakhirsemester1') {
            return ['type' => 'sumatif', 'key' => 'SAS', 'field' => 'sas', 'semester' => 'ganjil'];
        }
        if (preg_match('/^(?:sts|pts)[\s_-]*2$/i', $clean) || $clean === 'sumatiftengah2' || $clean === 'sumatiftengahsemester2') {
            return ['type' => 'sumatif', 'key' => 'STS 2', 'field' => 'sts', 'semester' => 'genap'];
        }
        if (preg_match('/^(?:sat|pat|sas2|pas2)[\s_-]*2?$/i', $clean) || $clean === 'sumatifakhirtahun' || $clean === 'sumatifakhir2') {
            return ['type' => 'sumatif', 'key' => 'SAT', 'field' => 'sas', 'semester' => 'genap'];
        }

        // 4. Cek Legacy Asesmen Harian (AH 1 s.d AH 8)
        if (preg_match('/^ah([1-8])$/i', $clean, $m)) {
            $num = (int) $m[1];
            return [
                'type' => 'ah',
                'key' => "AH {$num}",
                'ah_code' => "AH {$num}",
                'semester' => ($num <= 4) ? 'ganjil' : 'genap',
            ];
        }

        return null;
    }

    /**
     * Membaca dan memvalidasi berkas spreadsheet sebelum disimpan
     */
    public function validateAndParse(string $filePath, PenugasanMengajar $sourcePenugasan, array $allowedColumns = []): array
    {
        try {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
        } catch (\Throwable $e) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
        }
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $highestCol = $sheet->getHighestColumn();
        $highestColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestCol);

        // 1. Cari baris header (pindai 15 baris pertama)
        $headerRow = 5;
        $nisColIndex = null;
        $namaColIndex = null;
        $rombelColIndex = null;
        $scoreColIndices = []; // colIndex => colMeta array

        for ($r = 1; $r <= min(15, $highestRow); $r++) {
            for ($c = 1; $c <= $highestColIndex; $c++) {
                $val = trim((string)$sheet->getCell([$c, $r])->getValue());
                $valLower = strtolower(str_replace([' ', '_', '-'], '', $val));
                if (in_array($valLower, ['nis', 'nipd', 'nisn', 'nomorinduk'])) {
                    $headerRow = $r;
                    $nisColIndex = $c;
                    break 2;
                }
            }
        }

        if ($nisColIndex === null) {
            return [
                'success' => false,
                'total_rows' => 0,
                'valid_count' => 0,
                'error_count' => 1,
                'warning_count' => 0,
                'detected_columns' => [],
                'valid_rows' => [],
                'errors' => ['Kolom NIS / NIPD tidak ditemukan pada berkas Excel. Pastikan menggunakan format template yang disediakan.'],
                'warnings' => [],
            ];
        }

        // Ambil data TP untuk penugasan ini (sepanjang tahun ajaran)
        $allTps = NilaiTemplateExporter::getAllTujuanPembelajaran($sourcePenugasan);
        $isGenap = str_contains(strtolower($sourcePenugasan->semester?->semester ?? ''), 'genap');
        $activePeriode = $isGenap ? '9M3' : '9M1';

        // 2. Petakan kolom-kolom pada baris header yang ditemukan
        for ($c = 1; $c <= $highestColIndex; $c++) {
            $rawHeader = trim((string)$sheet->getCell([$c, $headerRow])->getValue());
            $cleanHeader = strtolower(str_replace([' ', '_', '-'], '', $rawHeader));

            if (in_array($cleanHeader, ['nis', 'nipd', 'nisn', 'nomorinduk'])) {
                $nisColIndex = $c;
                continue;
            } elseif (in_array($cleanHeader, ['nama', 'namasiswa', 'pesertadidik'])) {
                $namaColIndex = $c;
                continue;
            } elseif (in_array($cleanHeader, ['rombel', 'kelas'])) {
                $rombelColIndex = $c;
                continue;
            }

            $colMeta = $this->resolveColumn($rawHeader, $allTps, $activePeriode);
            if ($colMeta) {
                $colKey = $colMeta['key'];
                // Jika ada filter kolom yang diizinkan (checklist), hanya petakan jika cocok
                $isAllowed = empty($allowedColumns)
                    || in_array($colKey, $allowedColumns)
                    || in_array($rawHeader, $allowedColumns)
                    || (isset($colMeta['tp_kode']) && in_array($colMeta['tp_kode'], $allowedColumns));

                if ($isAllowed) {
                    $scoreColIndices[$c] = $colMeta;
                }
            }
        }

        if (empty($scoreColIndices)) {
            return [
                'success' => false,
                'total_rows' => 0,
                'valid_count' => 0,
                'error_count' => 1,
                'warning_count' => 0,
                'detected_columns' => [],
                'valid_rows' => [],
                'errors' => ['Tidak ada kolom nilai asesmen (TP, NH, STS, SAS, SAT, atau AH) yang cocok untuk diimpor.'],
                'warnings' => [],
            ];
        }

        // 3. Iterasi setiap baris data
        $validRows = [];
        $errors = [];
        $warnings = [];
        $totalProcessedRows = 0;

        for ($r = $headerRow + 1; $r <= $highestRow; $r++) {
            $rawNis = trim((string)$sheet->getCell([$nisColIndex, $r])->getValue());
            $cleanNis = ltrim($rawNis, "'");

            if ($cleanNis === '') {
                // Periksa apakah baris ini benar-benar kosong sepenuhnya
                $hasAnyValue = false;
                foreach ($scoreColIndices as $colIdx => $colMeta) {
                    if (trim((string)$sheet->getCell([$colIdx, $r])->getValue()) !== '') {
                        $hasAnyValue = true;
                        break;
                    }
                }
                if ($hasAnyValue) {
                    $errors[] = "Baris {$r}: Kolom NIS kosong padahal nilai asesmen terisi. Baris dilewati.";
                }
                continue;
            }

            $totalProcessedRows++;

            // Cari siswa berdasarkan NIPD (NIS) atau NISN
            $siswa = Siswa::where('nipd', $cleanNis)
                ->orWhere('nisn', $cleanNis)
                ->with('rombel')
                ->first();

            if (!$siswa) {
                $namaFromExcel = $namaColIndex ? trim((string)$sheet->getCell([$namaColIndex, $r])->getValue()) : '';
                $labelSiswa = $namaFromExcel ? " ({$namaFromExcel})" : '';
                $errors[] = "Baris {$r}: Siswa dengan NIS '{$cleanNis}'{$labelSiswa} tidak terdaftar di sistem. Baris dilewati.";
                continue;
            }

            // Baca nilai-nilai asesmen
            $rowScores = [];
            foreach ($scoreColIndices as $colIdx => $colMeta) {
                $colKey = $colMeta['key'];
                $cellVal = $sheet->getCell([$colIdx, $r])->getValue();

                // Sel kosong/blank -> abaikan (tidak menimpa nilai yang sudah ada di database)
                if ($cellVal === null || trim((string)$cellVal) === '') {
                    continue;
                }

                $numericVal = str_replace(',', '.', trim((string)$cellVal));
                if (!is_numeric($numericVal)) {
                    $warnings[] = "Baris {$r} ({$siswa->nama}): Nilai {$colKey} ('{$cellVal}') bukan angka valid, dilewati.";
                    continue;
                }

                $scoreFloat = (float)$numericVal;
                if ($scoreFloat < 0 || $scoreFloat > 100) {
                    $warnings[] = "Baris {$r} ({$siswa->nama}): Nilai {$colKey} ({$scoreFloat}) di luar skala 0 s.d 100, dilewati.";
                    continue;
                }

                $rowScores[$colKey] = $scoreFloat;
            }

            if (!empty($rowScores)) {
                $validRows[] = [
                    'baris' => $r,
                    'siswa_id' => $siswa->id,
                    'nama' => $siswa->nama,
                    'nis' => $cleanNis,
                    'rombel_id' => $siswa->rombel_id,
                    'rombel_nama' => $siswa->rombel?->nama ?? 'Tanpa Rombel',
                    'scores' => $rowScores,
                ];
            }
        }

        $detectedColumns = array_values(array_unique(array_column($scoreColIndices, 'key')));

        return [
            'success' => count($validRows) > 0,
            'total_rows' => $totalProcessedRows,
            'valid_count' => count($validRows),
            'error_count' => count($errors),
            'warning_count' => count($warnings),
            'detected_columns' => $detectedColumns,
            'valid_rows' => $validRows,
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Menyimpan baris-baris nilai yang telah tervalidasi ke dalam basis data
     */
    public function commitImport(array $validRows, PenugasanMengajar $sourcePenugasan): array
    {
        $taId = $sourcePenugasan->semester?->tahun_ajaran_id;
        $mapelId = $sourcePenugasan->mapel_id;
        $pegawaiId = $sourcePenugasan->pegawai_id;

        // Ambil data semester ganjil & genap
        $semGanjil = Semester::where('tahun_ajaran_id', $taId)->where('semester', 'ganjil')->first();
        $semGenap = Semester::where('tahun_ajaran_id', $taId)->where('semester', 'genap')->first();

        $semGanjilId = $semGanjil?->id ?? $sourcePenugasan->semester_id;
        $semGenapId = $semGenap?->id;

        $savedStudents = 0;
        $affectedRombels = [];
        $affectedPenugasans = [];

        DB::transaction(function () use (
            $validRows, $mapelId, $pegawaiId, $semGanjilId, $semGenapId,
            &$savedStudents, &$affectedRombels, &$affectedPenugasans
        ) {
            // Cache penugasan mengajar: [rombel_id][semester_id] => PenugasanMengajar
            $pmCache = [];
            // Cache TP per penugasan_id: [penugasan_id] => Collection TP
            $tpCache = [];

            $getPenugasan = function ($rombelId, $semesterId) use (&$pmCache, $mapelId, $pegawaiId) {
                if (isset($pmCache[$rombelId][$semesterId])) {
                    return $pmCache[$rombelId][$semesterId];
                }

                $pm = PenugasanMengajar::where('rombel_id', $rombelId)
                    ->where('mapel_id', $mapelId)
                    ->where('semester_id', $semesterId)
                    ->first();

                if (!$pm) {
                    $pm = PenugasanMengajar::create([
                        'rombel_id' => $rombelId,
                        'mapel_id' => $mapelId,
                        'semester_id' => $semesterId,
                        'pegawai_id' => $pegawaiId,
                        'jp' => 2,
                    ]);
                }

                $pmCache[$rombelId][$semesterId] = $pm;
                return $pm;
            };

            $getTps = function ($penugasanId) use (&$tpCache) {
                if (isset($tpCache[$penugasanId])) {
                    return $tpCache[$penugasanId];
                }

                $tps = TujuanPembelajaran::where('penugasan_mengajar_id', $penugasanId)->get();
                $tpCache[$penugasanId] = $tps;
                return $tps;
            };

            foreach ($validRows as $row) {
                $siswaId = $row['siswa_id'];
                $rombelId = $row['rombel_id'];
                $scores = $row['scores'];

                if (!$rombelId) {
                    continue;
                }

                $affectedRombels[$rombelId] = true;
                $hasSaved = false;

                $pmGanjil = $semGanjilId ? $getPenugasan($rombelId, $semGanjilId) : null;
                $pmGenap = $semGenapId ? $getPenugasan($rombelId, $semGenapId) : null;

                $tpsGanjil = $pmGanjil ? $getTps($pmGanjil->id) : collect();
                $tpsGenap = $pmGenap ? $getTps($pmGenap->id) : collect();

                if ($pmGanjil) {
                    $affectedPenugasans[$pmGanjil->id] = $pmGanjil;
                }
                if ($pmGenap) {
                    $affectedPenugasans[$pmGenap->id] = $pmGenap;
                }

                foreach ($scores as $colKey => $scoreVal) {
                    $cleanKey = strtolower(str_replace([' ', '_', '-', '(', ')', '[', ']', ':', '.', ','], '', $colKey));

                    // 1. Cek apakah cocok dengan Tujuan Pembelajaran (TP)
                    $targetTp = $tpsGanjil->first(fn($t) => strtolower(str_replace([' ', '_', '-', '(', ')', '[', ']', ':', '.', ','], '', $t->kode)) === $cleanKey)
                        ?: $tpsGenap->first(fn($t) => strtolower(str_replace([' ', '_', '-', '(', ')', '[', ']', ':', '.', ','], '', $t->kode)) === $cleanKey);

                    // Jika belum cocok, cari berdasarkan nomor urut TP (misal TP 1)
                    if (!$targetTp && preg_match('/^(?:tp|tujuan\s*pembelajaran)[\s_-]*(\d+)/i', $colKey, $tm)) {
                        $num = (int)$tm[1];
                        $targetTp = $tpsGanjil->first(function ($t) use ($num) {
                            preg_match('/\d+/', $t->kode, $cm);
                            return (isset($cm[0]) && (int)$cm[0] === $num) || ($t->urutan == $num);
                        }) ?: $tpsGenap->first(function ($t) use ($num) {
                            preg_match('/\d+/', $t->kode, $cm);
                            return (isset($cm[0]) && (int)$cm[0] === $num) || ($t->urutan == $num);
                        });
                    }

                    if ($targetTp) {
                        NilaiFormatif::updateOrCreate(
                            [
                                'siswa_id' => $siswaId,
                                'tujuan_pembelajaran_id' => $targetTp->id,
                            ],
                            ['nilai' => $scoreVal]
                        );

                        // Sinkronkan ke NilaiAsesmenHarian jika TP memiliki alokasi_ah
                        if (!empty($targetTp->alokasi_ah)) {
                            NilaiAsesmenHarian::updateOrCreate(
                                [
                                    'siswa_id' => $siswaId,
                                    'penugasan_mengajar_id' => $targetTp->penugasan_mengajar_id,
                                    'kode_ah' => $targetTp->alokasi_ah,
                                ],
                                ['nilai' => $scoreVal]
                            );
                        }
                        $hasSaved = true;
                        continue;
                    }

                    // 2. Cek apakah Nilai Harian (NH)
                    if (preg_match('/^NH(?:\s*(?:9M([1-4])|([1-4])))?$/i', $colKey, $nhm)) {
                        $pNum = !empty($nhm[1]) ? $nhm[1] : (!empty($nhm[2]) ? $nhm[2] : '1');
                        $periodKey = "9M{$pNum}";
                        $targetPm = in_array($periodKey, ['9M3', '9M4']) ? $pmGenap : $pmGanjil;

                        if ($targetPm) {
                            NilaiHarian::updateOrCreate(
                                [
                                    'siswa_id' => $siswaId,
                                    'penugasan_mengajar_id' => $targetPm->id,
                                    'target_periode' => $periodKey,
                                ],
                                ['nilai' => $scoreVal]
                            );
                            $hasSaved = true;
                        }
                        continue;
                    }

                    // 3. Cek Sumatif (STS 1, SAS, STS 2, SAT)
                    if (in_array(strtoupper($colKey), ['STS 1', 'STS', 'PTS 1', 'PTS']) && $pmGanjil) {
                        $sumatifGanjil = NilaiSumatif::firstOrNew([
                            'siswa_id' => $siswaId,
                            'penugasan_mengajar_id' => $pmGanjil->id,
                        ]);
                        $sumatifGanjil->sts = $scoreVal;
                        $sumatifGanjil->save();
                        $hasSaved = true;
                        continue;
                    }

                    if (in_array(strtoupper($colKey), ['SAS', 'PAS', 'SAS 1', 'PAS 1']) && $pmGanjil) {
                        $sumatifGanjil = NilaiSumatif::firstOrNew([
                            'siswa_id' => $siswaId,
                            'penugasan_mengajar_id' => $pmGanjil->id,
                        ]);
                        $sumatifGanjil->sas = $scoreVal;
                        $sumatifGanjil->save();
                        $hasSaved = true;
                        continue;
                    }

                    if (in_array(strtoupper($colKey), ['STS 2', 'PTS 2']) && $pmGenap) {
                        $sumatifGenap = NilaiSumatif::firstOrNew([
                            'siswa_id' => $siswaId,
                            'penugasan_mengajar_id' => $pmGenap->id,
                        ]);
                        $sumatifGenap->sts = $scoreVal;
                        $sumatifGenap->save();
                        $hasSaved = true;
                        continue;
                    }

                    if (in_array(strtoupper($colKey), ['SAT', 'PAT', 'SAS 2', 'PAS 2']) && $pmGenap) {
                        $sumatifGenap = NilaiSumatif::firstOrNew([
                            'siswa_id' => $siswaId,
                            'penugasan_mengajar_id' => $pmGenap->id,
                        ]);
                        $sumatifGenap->sas = $scoreVal;
                        $sumatifGenap->save();
                        $hasSaved = true;
                        continue;
                    }

                    // 4. Cek Legacy Asesmen Harian (AH 1 s.d AH 8)
                    if (preg_match('/^AH\s*([1-8])$/i', $colKey, $ahm)) {
                        $ahNum = (int) $ahm[1];
                        $isGenap = $ahNum >= 5;
                        $targetPm = $isGenap ? $pmGenap : $pmGanjil;
                        $targetTps = $isGenap ? $tpsGenap : $tpsGanjil;
                        $stdAhCode = "AH {$ahNum}";

                        if ($targetPm) {
                            NilaiAsesmenHarian::updateOrCreate(
                                [
                                    'siswa_id' => $siswaId,
                                    'penugasan_mengajar_id' => $targetPm->id,
                                    'kode_ah' => $stdAhCode,
                                ],
                                ['nilai' => $scoreVal]
                            );

                            // Sinkronkan ke seluruh TP yang dialokasikan ke AH ini
                            $tpsInAh = $targetTps->filter(fn($t) => trim(strtoupper($t->alokasi_ah ?? '')) === $stdAhCode);
                            foreach ($tpsInAh as $tp) {
                                NilaiFormatif::updateOrCreate(
                                    [
                                        'siswa_id' => $siswaId,
                                        'tujuan_pembelajaran_id' => $tp->id,
                                    ],
                                    ['nilai' => $scoreVal]
                                );
                            }
                            $hasSaved = true;
                        }
                        continue;
                    }
                }

                if ($hasSaved) {
                    $savedStudents++;
                }
            }
        });

        // Hitung ulang rekap nilai untuk seluruh penugasan yang terpengaruh
        foreach ($affectedPenugasans as $pm) {
            try {
                self::recalculatePenugasan($pm);
            } catch (\Throwable $e) {
                // Jangan menggagalkan import jika rekap background gagal
            }
        }

        return [
            'saved_students' => $savedStudents,
            'affected_rombels_count' => count($affectedRombels),
        ];
    }

    /**
     * Menghitung ulang seluruh rekap NilaiAkhir (NA 9M & Semester) untuk suatu penugasan mengajar
     */
    public static function recalculatePenugasan(PenugasanMengajar $pm): void
    {
        $taId = $pm->semester?->tahun_ajaran_id;
        $isGenap = str_contains(strtolower($pm->semester?->semester ?? ''), 'genap');
        $b1Key = $isGenap ? '9M3' : '9M1';
        $b2Key = $isGenap ? '9M4' : '9M2';

        $kkm = (float) ($pm->mapel->kkm_default ?? 75);
        $tps = TujuanPembelajaran::where('penugasan_mengajar_id', $pm->id)->get();
        $tpsB1 = $tps->filter(fn($t) => ($t->target_periode ?: ($isGenap ? '9M3' : '9M1')) === $b1Key);
        $tpsB2 = $tps->filter(fn($t) => $t->target_periode === $b2Key);

        $siswas = Siswa::where('rombel_id', $pm->rombel_id)->where('status', 'aktif')->get();
        if ($siswas->isEmpty()) {
            return;
        }

        $calc = app(PenilaianScoreCalculator::class);
        $settings = PengaturanPenilaian::getActive($taId);

        $nilaiFormatifs = NilaiFormatif::whereIn('tujuan_pembelajaran_id', $tps->pluck('id'))
            ->whereIn('siswa_id', $siswas->pluck('id'))
            ->get();

        $nilaiHarians = NilaiHarian::where('penugasan_mengajar_id', $pm->id)
            ->whereIn('siswa_id', $siswas->pluck('id'))
            ->get();

        $sumatifs = NilaiSumatif::where('penugasan_mengajar_id', $pm->id)
            ->whereIn('siswa_id', $siswas->pluck('id'))
            ->get();

        $existingNas = NilaiAkhir::where('penugasan_mengajar_id', $pm->id)
            ->whereIn('siswa_id', $siswas->pluck('id'))
            ->get()
            ->keyBy('siswa_id');

        foreach ($siswas as $siswa) {
            $existingNa = $existingNas->get($siswa->id);
            $currentData9m = $existingNa?->data_9m ?? [];

            $sTpScores = [];
            foreach ($tps as $tp) {
                $nf = $nilaiFormatifs->where('siswa_id', $siswa->id)->where('tujuan_pembelajaran_id', $tp->id)->first();
                if ($nf && $nf->nilai !== null) {
                    $sTpScores[$tp->id] = (float) $nf->nilai;
                }
            }

            $nh1Rec = $nilaiHarians->where('siswa_id', $siswa->id)->where('target_periode', $b1Key)->first();
            $nh2Rec = $nilaiHarians->where('siswa_id', $siswa->id)->where('target_periode', $b2Key)->first();
            $nh1 = $nh1Rec ? (float) $nh1Rec->nilai : 100.0;
            $nh2 = $nh2Rec ? (float) $nh2Rec->nilai : 100.0;

            $sumRec = $sumatifs->firstWhere('siswa_id', $siswa->id);
            $sum1 = $sumRec && $sumRec->sts !== null ? (float) $sumRec->sts : null;
            $sum2 = $sumRec && $sumRec->sas !== null ? (float) $sumRec->sas : null;

            $resB1 = $calc->calculate9MBlock($sTpScores, $nh1, $sum1, $kkm, $tpsB1, $currentData9m[$b1Key]['deskripsi_manual'] ?? null, $settings);
            $resB2 = $calc->calculate9MBlock($sTpScores, $nh2, $sum2, $kkm, $tpsB2, $currentData9m[$b2Key]['deskripsi_manual'] ?? null, $settings);
            $resSem = $calc->calculateSemester($resB1, $resB2, $kkm, $settings);

            $data9m = [
                $b1Key => $resB1,
                $b2Key => $resB2,
            ];

            NilaiAkhir::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'penugasan_mengajar_id' => $pm->id,
                ],
                [
                    'nilai_formatif_rata' => $resSem['nilai_akhir'] ?? null,
                    'nilai_akhir' => $resSem['nilai_akhir'] ?? null,
                    'predikat' => $resSem['predikat'] ?? 'Belum Lengkap',
                    'deskripsi_tertinggi' => $resB1['deskripsi_tertinggi'] ?? '',
                    'deskripsi_terendah' => $resB1['deskripsi_terendah'] ?? '',
                    'data_9m' => $data9m,
                ]
            );
        }
    }
}
