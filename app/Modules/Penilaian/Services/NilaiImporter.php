<?php

namespace App\Modules\Penilaian\Services;

use App\Modules\Akademik\Models\Semester;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\NilaiAsesmenHarian;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\NilaiSumatif;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
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

        // 1. Cari baris header (pindai 10 baris pertama)
        $headerRow = 5;
        $nisColIndex = null;
        $namaColIndex = null;
        $rombelColIndex = null;
        $scoreColIndices = []; // colIndex => 'AH 1'

        for ($r = 1; $r <= min(15, $highestRow); $r++) {
            for ($c = 1; $c <= $highestColIndex; $c++) {
                $val = trim((string)$sheet->getCell([$c, $r])->getValue());
                $valLower = strtolower($val);
                if (in_array($valLower, ['nis', 'nipd', 'nisn', 'nomor induk'])) {
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

        // 2. Petakan kolom-kolom pada baris header yang ditemukan
        for ($c = 1; $c <= $highestColIndex; $c++) {
            $rawHeader = trim((string)$sheet->getCell([$c, $headerRow])->getValue());
            $cleanHeader = strtolower(str_replace([' ', '_', '-'], '', $rawHeader));

            if (in_array($cleanHeader, ['nis', 'nipd', 'nisn', 'nomorinduk'])) {
                $nisColIndex = $c;
            } elseif (in_array($cleanHeader, ['nama', 'namasiswa', 'pesertadidik'])) {
                $namaColIndex = $c;
            } elseif (in_array($cleanHeader, ['rombel', 'kelas'])) {
                $rombelColIndex = $c;
            } elseif (isset(self::COLUMN_MAPPING[$cleanHeader])) {
                $standardCol = self::COLUMN_MAPPING[$cleanHeader];
                // Jika ada filter kolom yang diizinkan (checklist), hanya petakan jika cocok
                if (empty($allowedColumns) || in_array($standardCol, $allowedColumns)) {
                    $scoreColIndices[$c] = $standardCol;
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
                'errors' => ['Tidak ada kolom nilai asesmen (AH 1-8, STS, SAS, SAT) yang cocok untuk diimpor.'],
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
            // Bersihkan kutip tunggal jika ada
            $cleanNis = ltrim($rawNis, "'");

            if ($cleanNis === '') {
                // Periksa apakah baris ini benar-benar kosong sepenuhnya
                $hasAnyValue = false;
                foreach ($scoreColIndices as $colIdx => $colCode) {
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
            foreach ($scoreColIndices as $colIdx => $colCode) {
                $cellVal = $sheet->getCell([$colIdx, $r])->getValue();
                
                // Sel kosong/blank -> abaikan (tidak menimpa nilai yang ada)
                if ($cellVal === null || trim((string)$cellVal) === '') {
                    continue;
                }

                $numericVal = str_replace(',', '.', trim((string)$cellVal));
                if (!is_numeric($numericVal)) {
                    $warnings[] = "Baris {$r} ({$siswa->nama}): Nilai {$colCode} ('{$cellVal}') bukan angka valid, dilewati.";
                    continue;
                }

                $scoreFloat = (float)$numericVal;
                if ($scoreFloat < 0 || $scoreFloat > 100) {
                    $warnings[] = "Baris {$r} ({$siswa->nama}): Nilai {$colCode} ({$scoreFloat}) di luar skala 0 s.d 100, dilewati.";
                    continue;
                }

                $rowScores[$colCode] = $scoreFloat;
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

        return [
            'success' => count($validRows) > 0,
            'total_rows' => $totalProcessedRows,
            'valid_count' => count($validRows),
            'error_count' => count($errors),
            'warning_count' => count($warnings),
            'detected_columns' => array_values(array_unique($scoreColIndices)),
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

        // Kolom asesmen semester ganjil vs genap
        $colsGanjil = ['AH 1', 'AH 2', 'AH 3', 'AH 4', 'STS 1', 'SAS'];
        $colsGenap = ['AH 5', 'AH 6', 'AH 7', 'AH 8', 'STS 2', 'SAT'];

        $savedStudents = 0;
        $affectedRombels = [];

        DB::transaction(function () use (
            $validRows, $mapelId, $pegawaiId, $semGanjilId, $semGenapId,
            $colsGanjil, $colsGenap, &$savedStudents, &$affectedRombels
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

                // 1. Proses Kolom Ganjil
                $hasGanjil = false;
                foreach ($colsGanjil as $cg) {
                    if (isset($scores[$cg])) {
                        $hasGanjil = true;
                        break;
                    }
                }

                if ($hasGanjil && $semGanjilId) {
                    $pmGanjil = $getPenugasan($rombelId, $semGanjilId);
                    $tpsGanjil = $getTps($pmGanjil->id);

                    // Asesmen Harian Ganjil (AH 1 s.d AH 4)
                    for ($i = 1; $i <= 4; $i++) {
                        $ahCode = "AH {$i}";
                        if (isset($scores[$ahCode])) {
                            $nilai = $scores[$ahCode];
                            NilaiAsesmenHarian::updateOrCreate(
                                [
                                    'siswa_id' => $siswaId,
                                    'penugasan_mengajar_id' => $pmGanjil->id,
                                    'kode_ah' => $ahCode,
                                ],
                                ['nilai' => $nilai]
                            );

                            // Sinkronkan ke seluruh TP yang dialokasikan ke AH ini
                            $tpsInAh = $tpsGanjil->filter(fn($t) => trim(strtoupper($t->alokasi_ah ?? '')) === $ahCode);
                            foreach ($tpsInAh as $tp) {
                                NilaiFormatif::updateOrCreate(
                                    [
                                        'siswa_id' => $siswaId,
                                        'tujuan_pembelajaran_id' => $tp->id,
                                    ],
                                    ['nilai' => $nilai]
                                );
                            }
                            $hasSaved = true;
                        }
                    }

                    // Sumatif Ganjil (STS 1 & SAS)
                    if (isset($scores['STS 1']) || isset($scores['SAS'])) {
                        $sumatifGanjil = NilaiSumatif::firstOrNew([
                            'siswa_id' => $siswaId,
                            'penugasan_mengajar_id' => $pmGanjil->id,
                        ]);
                        if (isset($scores['STS 1'])) {
                            $sumatifGanjil->sts = $scores['STS 1'];
                        }
                        if (isset($scores['SAS'])) {
                            $sumatifGanjil->sas = $scores['SAS'];
                        }
                        $sumatifGanjil->save();
                        $hasSaved = true;
                    }
                }

                // 2. Proses Kolom Genap
                $hasGenap = false;
                foreach ($colsGenap as $cg) {
                    if (isset($scores[$cg])) {
                        $hasGenap = true;
                        break;
                    }
                }

                if ($hasGenap && $semGenapId) {
                    $pmGenap = $getPenugasan($rombelId, $semGenapId);
                    $tpsGenap = $getTps($pmGenap->id);

                    // Asesmen Harian Genap (AH 5 s.d AH 8)
                    for ($i = 5; $i <= 8; $i++) {
                        $ahCode = "AH {$i}";
                        if (isset($scores[$ahCode])) {
                            $nilai = $scores[$ahCode];
                            NilaiAsesmenHarian::updateOrCreate(
                                [
                                    'siswa_id' => $siswaId,
                                    'penugasan_mengajar_id' => $pmGenap->id,
                                    'kode_ah' => $ahCode,
                                ],
                                ['nilai' => $nilai]
                            );

                            // Sinkronkan ke seluruh TP yang dialokasikan ke AH ini
                            $tpsInAh = $tpsGenap->filter(fn($t) => trim(strtoupper($t->alokasi_ah ?? '')) === $ahCode);
                            foreach ($tpsInAh as $tp) {
                                NilaiFormatif::updateOrCreate(
                                    [
                                        'siswa_id' => $siswaId,
                                        'tujuan_pembelajaran_id' => $tp->id,
                                    ],
                                    ['nilai' => $nilai]
                                );
                            }
                            $hasSaved = true;
                        }
                    }

                    // Sumatif Genap (STS 2 & SAT)
                    if (isset($scores['STS 2']) || isset($scores['SAT'])) {
                        $sumatifGenap = NilaiSumatif::firstOrNew([
                            'siswa_id' => $siswaId,
                            'penugasan_mengajar_id' => $pmGenap->id,
                        ]);
                        if (isset($scores['STS 2'])) {
                            $sumatifGenap->sts = $scores['STS 2'];
                        }
                        if (isset($scores['SAT'])) {
                            $sumatifGenap->sas = $scores['SAT'];
                        }
                        $sumatifGenap->save();
                        $hasSaved = true;
                    }
                }

                if ($hasSaved) {
                    $savedStudents++;
                }
            }
        });

        return [
            'saved_students' => $savedStudents,
            'affected_rombels_count' => count($affectedRombels),
        ];
    }
}
