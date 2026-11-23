<?php

namespace App\Modules\Absensi\Services;

use App\Enums\StatusAbsensi;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Kesiswaan\Models\Program;
use App\Modules\Kesiswaan\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MatriksAbsensiService
{
    /**
     * Pemetaan nama Rombel Excel ke Nama Rombel SIRAGA
     */
    protected array $rombelNameToDbName = [
        'HTL X' => 'X HTL 1',
        'HTL XI' => 'XI HTL 1',
        'HTL XII' => 'XII HTL 1',
        'PMN X' => 'X PMN 1',
        'PMN XI' => 'XI PMN 1',
        'PMN XII' => 'XII PMN 1',
        'PPLG X' => 'X PPLG 1',
        'PPLG XI' => 'XI PPLG 1',
        'PPLG XII' => 'XII PPLG 1',
        'TJKT X' => 'X TJKT 1',
        'TJKT XII' => 'XII TJKT 1',
    ];

    /**
     * Mendapatkan daftar tanggal efektif (YYYY-MM-DD) antara 2 tanggal
     */
    public function getTanggalEfektifList(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        if ($start->gt($end)) {
            return [];
        }

        // Ambil data hari libur KBM dari Kalender Pendidikan bila ada
        $liburList = collect();
        if (class_exists(\App\Modules\KalenderPendidikan\Models\KalenderLibur::class)) {
            $liburList = \App\Modules\KalenderPendidikan\Models\KalenderLibur::where('is_kbm_libur', true)
                ->where('tgl_mulai', '<=', $end->toDateString())
                ->where('tgl_selesai', '>=', $start->toDateString())
                ->get();
        }

        $dates = [];
        $current = $start->copy();

        while ($current->lte($end)) {
            $curStr = $current->toDateString();
            if (!$current->isWeekend()) {
                $isHoliday = $liburList->contains(function ($l) use ($curStr) {
                    return $curStr >= $l->tgl_mulai->toDateString() && $curStr <= $l->tgl_selesai->toDateString();
                });

                if (!$isHoliday) {
                    $dates[] = $curStr;
                }
            }
            $current->addDay();
        }

        // Sertakan juga tanggal Sabtu/Minggu yang memang sudah memiliki data absensi di DB
        $dbWeekendDates = AbsensiHarian::whereBetween('tanggal', [$startDate, $endDate])
            ->distinct()
            ->pluck('tanggal')
            ->filter(fn($d) => Carbon::parse($d)->isWeekend())
            ->map(fn($d) => $d instanceof Carbon ? $d->toDateString() : Carbon::parse($d)->toDateString())
            ->toArray();

        $allDates = array_unique(array_merge($dates, $dbWeekendDates));
        sort($allDates);

        return array_values($allDates);
    }

    /**
     * Membuat Spreadsheet Matriks Presensi (format kalender tanggal per kolom)
     */
    public function generateMatriksExcel(array $filters): Spreadsheet
    {
        $tglMulai = $filters['tgl_mulai'] ?? now()->startOfMonth()->toDateString();
        $tglSelesai = $filters['tgl_selesai'] ?? now()->toDateString();

        $rombelId = $filters['rombel_id'] ?? null;
        $rayonId = $filters['rayon_id'] ?? null;
        $gender = $filters['gender'] ?? null;
        $programId = $filters['program_id'] ?? null;
        $search = trim((string) ($filters['search'] ?? ''));

        $tanggalList = $this->getTanggalEfektifList($tglMulai, $tglSelesai);
        if (empty($tanggalList)) {
            $tanggalList = [$tglMulai];
        }

        // 1. Query Siswa sesuai filter
        $querySiswa = Siswa::with(['rombel', 'rayon', 'rayon.pembimbing'])
            ->where('status', 'aktif');

        if (!empty($rombelId)) {
            $querySiswa->inRombel((int) $rombelId);
        }
        if (!empty($rayonId)) {
            $querySiswa->where('rayon_id', $rayonId);
        }
        if (!empty($gender)) {
            $querySiswa->where('jk', $gender);
        }
        if (!empty($programId)) {
            $querySiswa->where('program_id', $programId);
        }
        if ($search !== '') {
            $querySiswa->where(function ($q) use ($search) {
                $q->where('nipd', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $siswas = $querySiswa->orderBy('nama')->get();
        $siswaIds = $siswas->pluck('id')->toArray();

        // 2. Query Absensi Siswa pada rentang tanggal
        $absensiMap = [];
        if (!empty($siswaIds)) {
            $records = AbsensiHarian::whereBetween('tanggal', [$tglMulai, $tglSelesai])
                ->whereIn('siswa_id', $siswaIds)
                ->get();

            foreach ($records as $r) {
                $tglStr = $r->tanggal instanceof Carbon ? $r->tanggal->toDateString() : Carbon::parse($r->tanggal)->toDateString();
                $absensiMap[$r->siswa_id][$tglStr] = $r;
            }
        }

        // 3. Bangun Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Matriks Presensi');

        // Header Dokumen
        $rombelNama = $rombelId ? Rombel::find($rombelId)?->nama : 'Semua Rombel';
        $rayonNama = $rayonId ? Rayon::find($rayonId)?->nama : 'Semua Rayon';
        $periodeLabel = Carbon::parse($tglMulai)->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($tglSelesai)->translatedFormat('d M Y');

        $sheet->setCellValue('A1', 'MATRIKS PRESENSI SISWA');
        $sheet->setCellValue('A2', "Periode: {$periodeLabel} | Rombel: {$rombelNama} | Rayon: {$rayonNama}");
        $sheet->setCellValue('A3', 'Petunjuk: Kolom tanggal diisi kode S (Sakit), I (Izin), A (Alpa), D (Dispensasi), PLH, KWH, CBT. Sel kosong atau H berarti Hadir.');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A2')->getFont()->setSize(10)->setItalic(true);
        $sheet->getStyle('A3')->getFont()->setSize(9)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('4B5563'));

        // Baris Header Kolom (Baris 5)
        $fixedHeaders = [
            'No',
            'NIS',
            'ROMBEL',
            'L/P',
            'Rayon',
            'Pembimbing',
            'Nama Murid',
        ];

        $colIdx = 1;
        foreach ($fixedHeaders as $fh) {
            $cellCoord = Coordinate::stringFromColumnIndex($colIdx) . '5';
            $sheet->setCellValue($cellCoord, $fh);
            $colIdx++;
        }

        $dateStartColIdx = $colIdx;
        foreach ($tanggalList as $tgl) {
            $cellCoord = Coordinate::stringFromColumnIndex($colIdx) . '5';
            $sheet->setCellValue($cellCoord, $tgl);
            $colIdx++;
        }
        $dateEndColIdx = $colIdx - 1;

        // Kolom Rekapitulasi Akhir
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx) . '5', 'S');
        $colSIdx = $colIdx++;
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx) . '5', 'I');
        $colIIdx = $colIdx++;
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx) . '5', 'A');
        $colAIdx = $colIdx;

        $lastColCoord = Coordinate::stringFromColumnIndex($colIdx);
        $headerRange = "A5:{$lastColCoord}5";

        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '1F2937']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']]],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(25);

        // 4. Baris Data Siswa
        $rowIdx = 6;
        foreach ($siswas as $idx => $s) {
            $sheet->setCellValue('A' . $rowIdx, $idx + 1);
            $sheet->setCellValueExplicit('B' . $rowIdx, (string) ($s->nipd ?: $s->nisn), DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $rowIdx, $s->rombel?->nama ?? '');
            $sheet->setCellValue('D' . $rowIdx, $s->jk ?? '');
            $sheet->setCellValue('E' . $rowIdx, $s->rayon?->nama ?? '');
            $sheet->setCellValue('F' . $rowIdx, $s->rayon?->pembimbing?->nama_lengkap ?? '');
            $sheet->setCellValue('G' . $rowIdx, $s->nama);

            // Tanggal
            $cCol = $dateStartColIdx;
            foreach ($tanggalList as $tgl) {
                $cellCoord = Coordinate::stringFromColumnIndex($cCol) . $rowIdx;
                $rec = $absensiMap[$s->id][$tgl] ?? null;
                $val = '';

                if ($rec) {
                    $st = $rec->status instanceof StatusAbsensi ? $rec->status : StatusAbsensi::tryFrom($rec->status);
                    $val = match ($st) {
                        StatusAbsensi::SAKIT => 'S',
                        StatusAbsensi::IZIN => 'I',
                        StatusAbsensi::ALPA => 'A',
                        StatusAbsensi::DISPEN_CBT_PLH => 'PLH',
                        StatusAbsensi::DISPEN_CBT_KWH => 'KWH',
                        StatusAbsensi::DISPEN_LAINNYA => $this->detectDispenShortCode($rec->keterangan),
                        default => '', // Hadir dibiarkan kosong
                    };
                }

                $sheet->setCellValue($cellCoord, $val);
                $sheet->getStyle($cellCoord)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Warna background khusus jika tidak hadir
                if ($val === 'S') {
                    $sheet->getStyle($cellCoord)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF08A'); // yellow-200
                } elseif ($val === 'I') {
                    $sheet->getStyle($cellCoord)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('BFDBFE'); // blue-200
                } elseif ($val === 'A') {
                    $sheet->getStyle($cellCoord)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FECACA'); // red-200
                    $sheet->getStyle($cellCoord)->getFont()->setBold(true)->getColor()->setRGB('B91C1C');
                } elseif (in_array($val, ['D', 'PLH', 'KWH', 'CBT', 'KKR'])) {
                    $sheet->getStyle($cellCoord)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E9D5FF'); // purple-200
                }

                $cCol++;
            }

            // Formula Rekapitulasi S, I, A
            $dateStartColLetter = Coordinate::stringFromColumnIndex($dateStartColIdx);
            $dateEndColLetter = Coordinate::stringFromColumnIndex($dateEndColIdx);

            $cellS = Coordinate::stringFromColumnIndex($colSIdx) . $rowIdx;
            $cellI = Coordinate::stringFromColumnIndex($colIIdx) . $rowIdx;
            $cellA = Coordinate::stringFromColumnIndex($colAIdx) . $rowIdx;

            $sheet->setCellValue($cellS, "=COUNTIF({$dateStartColLetter}{$rowIdx}:{$dateEndColLetter}{$rowIdx}, \"S\")");
            $sheet->setCellValue($cellI, "=COUNTIF({$dateStartColLetter}{$rowIdx}:{$dateEndColLetter}{$rowIdx}, \"I\")");
            $sheet->setCellValue($cellA, "=COUNTIF({$dateStartColLetter}{$rowIdx}:{$dateEndColLetter}{$rowIdx}, \"A\")");

            $sheet->getStyle("A{$rowIdx}:D{$rowIdx}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("{$cellS}:{$cellA}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $rowIdx++;
        }

        $lastRow = max(6, $rowIdx - 1);
        $dataRange = "A5:{$lastColCoord}{$lastRow}";
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D1D5DB');

        // Auto width kolom identitas
        foreach (range(1, 7) as $ci) {
            $colLetter = Coordinate::stringFromColumnIndex($ci);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }
        // Kolom tanggal & rekap lebar tetap 5
        for ($ci = $dateStartColIdx; $ci <= $colAIdx; $ci++) {
            $colLetter = Coordinate::stringFromColumnIndex($ci);
            $sheet->getColumnDimension($colLetter)->setWidth(5.5);
        }

        return $spreadsheet;
    }

    /**
     * Mendeteksi kode singkat dispensasi dari teks keterangan
     */
    protected function detectDispenShortCode(?string $ket): string
    {
        if (!$ket) {
            return 'D';
        }
        $upper = strtoupper($ket);
        if (str_contains($upper, 'PLH')) return 'PLH';
        if (str_contains($upper, 'KWH')) return 'KWH';
        if (str_contains($upper, 'CBT')) return 'CBT';
        if (str_contains($upper, 'KKR')) return 'KKR';
        if (str_contains($upper, '(M)')) return 'M';
        return 'D';
    }

    /**
     * Mem-parsing file Matriks Excel/CSV yang diupload dan mengembalikan data pratinjau
     */
    public function parseMatriksFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File tidak ditemukan.");
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rawRows = $sheet->toArray(null, true, false, false);

        if (empty($rawRows)) {
            throw new \Exception("File kosong atau tidak dapat dibaca.");
        }

        // 1. Cari baris Header Kolom (mengandung NIS / NIPD dan Nama)
        $headerRowIdx = null;
        $nisColIdx = null;
        $namaColIdx = null;
        $rombelColIdx = null;

        foreach ($rawRows as $rIdx => $row) {
            foreach ($row as $cIdx => $val) {
                $valClean = strtoupper(trim((string) $val));
                if (in_array($valClean, ['NIS', 'NIPD', 'NISN', 'NO INDUK'])) {
                    $nisColIdx = $cIdx;
                    $headerRowIdx = $rIdx;
                }
                if (in_array($valClean, ['NAMA', 'NAMA MURID', 'NAMA SISWA', 'NAMA LENGKAP'])) {
                    $namaColIdx = $cIdx;
                }
                if (in_array($valClean, ['ROMBEL', 'KELAS'])) {
                    $rombelColIdx = $cIdx;
                }
            }

            if ($nisColIdx !== null && $namaColIdx !== null) {
                break;
            }
        }

        if ($headerRowIdx === null || $nisColIdx === null) {
            throw new \Exception("Format kolom tidak dikenali. Kolom 'NIS' atau 'NIPD' tidak ditemukan pada header tabel.");
        }

        $headerRow = $rawRows[$headerRowIdx];

        // 2. Deteksi Kolom-Kolom Tanggal
        $dateCols = [];
        $summaryStopKeywords = ['S', 'I', 'A', 'H', 'TOTAL', 'REKAP', 'PERSEN', 'PERSENTASE', '%'];

        for ($cIdx = 0; $cIdx < count($headerRow); $cIdx++) {
            if ($cIdx === $nisColIdx || $cIdx === $namaColIdx || $cIdx === $rombelColIdx) {
                continue;
            }

            $headerVal = trim((string) $headerRow[$cIdx]);
            if ($headerVal === '') {
                continue;
            }

            // Hentikan jika mencapai kolom rekapitulasi S, I, A di sebelah kanan
            if (in_array(strtoupper($headerVal), $summaryStopKeywords) && $cIdx > max($nisColIdx, $namaColIdx)) {
                // Jangan baca kolom rekapitulasi sebagai tanggal
                continue;
            }

            // Validasi format tanggal YYYY-MM-DD
            $tglStr = null;
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $headerVal)) {
                $tglStr = $headerVal;
            } elseif (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $headerVal, $m)) {
                $tglStr = sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
            }

            if ($tglStr) {
                $dateCols[$cIdx] = $tglStr;
            }
        }

        if (empty($dateCols)) {
            throw new \Exception("Tidak ditemukan kolom tanggal valid (format YYYY-MM-DD) pada header tabel.");
        }

        $datesList = array_values($dateCols);
        sort($datesList);
        $minDate = $datesList[0];
        $maxDate = $datesList[count($datesList) - 1];

        // 3. Muat Data Master (Siswa, Rombel, Semester)
        $activeSemester = Semester::where('is_active', true)->first() ?: Semester::first();
        $allSiswas = Siswa::all()->keyBy('nipd');
        $allSiswasByNisn = Siswa::whereNotNull('nisn')->get()->keyBy('nisn');
        $allRombels = Rombel::all()->keyBy('nama');

        $rombelIdMap = [];
        foreach ($this->rombelNameToDbName as $excelName => $dbName) {
            if ($allRombels->has($dbName)) {
                $rombelIdMap[$excelName] = $allRombels[$dbName]->id;
            }
        }

        // 4. Parsing Baris Siswa
        $matchedStudents = 0;
        $unmatchedStudents = [];
        $records = [];
        $statusCounts = [
            'Hadir' => 0,
            'Sakit' => 0,
            'Izin' => 0,
            'Alpa' => 0,
            'Dispen_CBT_PLH' => 0,
            'Dispen_CBT_KWH' => 0,
            'Dispen_Lainnya' => 0,
        ];

        for ($rIdx = $headerRowIdx + 1; $rIdx < count($rawRows); $rIdx++) {
            $row = $rawRows[$rIdx];
            $rawNis = trim((string) ($row[$nisColIdx] ?? ''));
            $rawNama = trim((string) ($row[$namaColIdx] ?? ''));
            $rawRombel = $rombelColIdx !== null ? trim((string) ($row[$rombelColIdx] ?? '')) : '';

            if ($rawNis === '') {
                continue;
            }

            // Cari siswa berdasarkan NIPD atau NISN
            $siswa = $allSiswas->get($rawNis) ?: $allSiswasByNisn->get($rawNis);

            if (!$siswa) {
                $unmatchedStudents[] = [
                    'nis' => $rawNis,
                    'nama' => $rawNama,
                    'rombel' => $rawRombel,
                ];
                continue;
            }

            $matchedStudents++;
            $rombelId = $rombelIdMap[$rawRombel] ?? $siswa->rombel_id;

            // Baca nilai pada tiap kolom tanggal
            foreach ($dateCols as $cIdx => $tanggal) {
                $cellVal = trim((string) ($row[$cIdx] ?? ''));
                $cellUpper = strtoupper($cellVal);

                $status = StatusAbsensi::HADIR->value;
                $keterangan = null;

                if ($cellUpper === '' || $cellUpper === '-' || $cellUpper === 'H') {
                    $status = StatusAbsensi::HADIR->value;
                } elseif ($cellUpper === 'S') {
                    $status = StatusAbsensi::SAKIT->value;
                    $keterangan = 'Sakit';
                } elseif ($cellUpper === 'I') {
                    $status = StatusAbsensi::IZIN->value;
                    $keterangan = 'Izin';
                } elseif ($cellUpper === 'A') {
                    $status = StatusAbsensi::ALPA->value;
                    $keterangan = 'Tanpa Keterangan / Alpa';
                } elseif ($cellUpper === 'D') {
                    $status = StatusAbsensi::DISPEN_LAINNYA->value;
                    $keterangan = 'Dispensasi';
                } elseif ($cellUpper === 'PLH') {
                    $status = StatusAbsensi::DISPEN_CBT_PLH->value;
                    $keterangan = 'Dispen CBT PLH';
                } elseif ($cellUpper === 'KWH') {
                    $status = StatusAbsensi::DISPEN_CBT_KWH->value;
                    $keterangan = 'Dispen CBT KWH';
                } elseif ($cellUpper === 'CBT') {
                    $status = StatusAbsensi::DISPEN_LAINNYA->value;
                    $keterangan = 'Dispen CBT';
                } elseif ($cellUpper === 'KKR') {
                    $status = StatusAbsensi::DISPEN_LAINNYA->value;
                    $keterangan = 'Dispen Kader Kesehatan Remaja';
                } elseif ($cellUpper === 'M') {
                    $status = StatusAbsensi::DISPEN_LAINNYA->value;
                    $keterangan = 'Dispensasi (M)';
                } else {
                    $status = StatusAbsensi::DISPEN_LAINNYA->value;
                    $keterangan = "Catatan: {$cellVal}";
                }

                $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;

                $records[] = [
                    'tanggal' => $tanggal,
                    'siswa_id' => $siswa->id,
                    'rombel_id' => $rombelId,
                    'semester_id' => $activeSemester?->id ?? 1,
                    'status' => $status,
                    'keterangan' => $keterangan,
                ];
            }
        }

        return [
            'total_siswa_file' => $matchedStudents + count($unmatchedStudents),
            'matched_students_count' => $matchedStudents,
            'unmatched_students' => $unmatchedStudents,
            'total_dates' => count($datesList),
            'date_range' => Carbon::parse($minDate)->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($maxDate)->translatedFormat('d M Y'),
            'start_date' => $minDate,
            'end_date' => $maxDate,
            'total_records' => count($records),
            'status_counts' => $statusCounts,
            'records' => $records,
        ];
    }

    /**
     * Menyimpan batch record absensi ke database secara transaksional
     */
    public function importMatriksRecords(array $records, ?int $userId = null): int
    {
        if (empty($records)) {
            return 0;
        }

        $userId = $userId ?: auth()->id() ?: 1;
        $now = Carbon::now();

        $chunkSize = 1000;
        $chunks = array_chunk($records, $chunkSize);

        DB::transaction(function () use ($chunks, $userId, $now) {
            foreach ($chunks as $chunk) {
                $batchData = array_map(function ($r) use ($userId, $now) {
                    $r['diinput_oleh'] = $userId;
                    $r['created_at'] = $now;
                    $r['updated_at'] = $now;
                    return $r;
                }, $chunk);

                DB::table('absensi_harians')->upsert(
                    $batchData,
                    ['tanggal', 'siswa_id'],
                    ['rombel_id', 'semester_id', 'status', 'keterangan', 'diinput_oleh', 'updated_at']
                );
            }
        });

        return count($records);
    }
}
