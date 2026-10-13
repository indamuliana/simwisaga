<?php

namespace App\Modules\Penilaian\Services;

use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RekapNilaiExporter
{
    protected RekapPenilaianService $service;

    public function __construct(RekapPenilaianService $service)
    {
        $this->service = $service;
    }

    /**
     * Menghasilkan berkas Excel Buku Leger Nilai Rombel (.xlsx).
     */
    public function exportLegerRombel(Rombel $rombel, Semester $semester): Spreadsheet
    {
        $data = $this->service->getLegerRombelData($rombel, $semester);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Leger Nilai Kelas');
        $sheet->setShowGridLines(true);

        // 1. KOP / Judul Laporan
        $sheet->setCellValue('A1', 'SMK WIKRAMA 1 GARUT');
        $sheet->setCellValue('A2', 'LEGER HASIL BELAJAR PESERTA DIDIK');
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // 2. Metadata Kelas
        $sheet->setCellValue('A4', 'Kelas / Rombel');
        $sheet->setCellValue('B4', ': ' . $data['rombel']['nama']);
        $sheet->setCellValue('A5', 'Konsentrasi Keahlian');
        $sheet->setCellValue('B5', ': ' . $data['rombel']['jurusan']);

        $sheet->setCellValue('E4', 'Tahun Pelajaran');
        $sheet->setCellValue('F4', ': ' . $data['rombel']['tahun_ajaran']);
        $sheet->setCellValue('E5', 'Semester');
        $sheet->setCellValue('F5', ': ' . $data['semester']['nama']);
        $sheet->setCellValue('E6', 'Wali Kelas');
        $sheet->setCellValue('F6', ': ' . $data['rombel']['wali_kelas']);

        $sheet->getStyle('A4:F6')->getFont()->setSize(10);
        $sheet->getStyle('A4:A6')->getFont()->setBold(true);
        $sheet->getStyle('E4:E6')->getFont()->setBold(true);

        // 3. Header Tabel Leger
        $startRow = 8;
        $sheet->setCellValue('A' . $startRow, 'No');
        $sheet->setCellValue('B' . $startRow, 'NIS');
        $sheet->setCellValue('C' . $startRow, 'NISN');
        $sheet->setCellValue('D' . $startRow, 'Nama Peserta Didik');
        $sheet->setCellValue('E' . $startRow, 'L/P');

        $sheet->mergeCells("A{$startRow}:A" . ($startRow + 1));
        $sheet->mergeCells("B{$startRow}:B" . ($startRow + 1));
        $sheet->mergeCells("C{$startRow}:C" . ($startRow + 1));
        $sheet->mergeCells("D{$startRow}:D" . ($startRow + 1));
        $sheet->mergeCells("E{$startRow}:E" . ($startRow + 1));

        $colIdx = 6; // Mulai dari kolom F
        $mapelColMap = [];

        foreach ($data['mapel_list'] as $mId => $mInfo) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx);
            $sheet->setCellValue($colLetter . $startRow, $mInfo['nama']);
            $sheet->setCellValue($colLetter . ($startRow + 1), 'KKM: ' . $mInfo['kkm']);
            $mapelColMap[$mId] = $colLetter;
            $colIdx++;
        }

        // Kolom Rekapitulasi Siswa
        $colTotal = Coordinate::stringFromColumnIndex($colIdx++);
        $colAvg = Coordinate::stringFromColumnIndex($colIdx++);
        $colRank = Coordinate::stringFromColumnIndex($colIdx++);
        $colBelow = Coordinate::stringFromColumnIndex($colIdx++);

        $sheet->setCellValue($colTotal . $startRow, 'Total');
        $sheet->setCellValue($colTotal . ($startRow + 1), 'Nilai');
        $sheet->setCellValue($colAvg . $startRow, 'Rata');
        $sheet->setCellValue($colAvg . ($startRow + 1), 'Rata');
        $sheet->setCellValue($colRank . $startRow, 'Rank');
        $sheet->setCellValue($colRank . ($startRow + 1), 'Kelas');
        $sheet->setCellValue($colBelow . $startRow, 'Jml Mapel');
        $sheet->setCellValue($colBelow . ($startRow + 1), '< KKM');

        $lastColLetter = $colBelow;

        // Styling Header
        $headerRange = "A{$startRow}:{$lastColLetter}" . ($startRow + 1);
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($headerRange)->getAlignment()->setWrapText(true);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');

        // 4. Data Siswa Baris per Baris
        $currentRow = $startRow + 2;
        $no = 1;

        foreach ($data['siswa_rows'] as $sRow) {
            $sheet->setCellValue('A' . $currentRow, $no++);
            $sheet->setCellValueExplicit('B' . $currentRow, (string) $sRow['nis'], DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $currentRow, (string) $sRow['nisn'], DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $currentRow, $sRow['nama']);
            $sheet->setCellValue('E' . $currentRow, $sRow['jenis_kelamin']);

            foreach ($data['mapel_list'] as $mId => $mInfo) {
                $colLetter = $mapelColMap[$mId];
                $scoreInfo = $sRow['scores'][$mId];
                $scoreVal = $scoreInfo['nilai_akhir'];

                if ($scoreVal !== null) {
                    $sheet->setCellValue($colLetter . $currentRow, $scoreVal);
                    if ($scoreInfo['is_below_kkm']) {
                        $sheet->getStyle($colLetter . $currentRow)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFFFE4E6'); // Merah muda
                        $sheet->getStyle($colLetter . $currentRow)->getFont()
                            ->getColor()->setARGB('FFE11D48'); // Teks merah
                        $sheet->getStyle($colLetter . $currentRow)->getFont()->setBold(true);
                    }
                } else {
                    $sheet->setCellValue($colLetter . $currentRow, '-');
                    $sheet->getStyle($colLetter . $currentRow)->getFont()->getColor()->setARGB('FF94A3B8');
                }
            }

            // Nilai Total & Rata-rata
            $sheet->setCellValue($colTotal . $currentRow, $sRow['total_nilai']);
            $sheet->setCellValue($colAvg . $currentRow, $sRow['rata_rata']);
            $sheet->setCellValue($colRank . $currentRow, $sRow['rank']);
            $sheet->setCellValue($colBelow . $currentRow, $sRow['below_kkm_count']);

            if ($sRow['below_kkm_count'] > 0) {
                $sheet->getStyle($colBelow . $currentRow)->getFont()->setBold(true)->getColor()->setARGB('FFE11D48');
            }

            $currentRow++;
        }

        // 5. Baris Rekapitulasi Footer Tabel Leger
        $footerStartRow = $currentRow;

        // Baris Rata-rata Kelas
        $sheet->setCellValue('A' . $currentRow, 'Rata-rata Kelas');
        $sheet->mergeCells("A{$currentRow}:E{$currentRow}");
        foreach ($data['mapel_list'] as $mId => $mInfo) {
            $sheet->setCellValue($mapelColMap[$mId] . $currentRow, $data['mapel_stats'][$mId]['avg']);
        }
        $sheet->setCellValue($colAvg . $currentRow, $data['summary']['rata_rata_rombel']);
        $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->getFont()->setBold(true);
        $currentRow++;

        // Baris Nilai Tertinggi
        $sheet->setCellValue('A' . $currentRow, 'Nilai Tertinggi (Max)');
        $sheet->mergeCells("A{$currentRow}:E{$currentRow}");
        foreach ($data['mapel_list'] as $mId => $mInfo) {
            $sheet->setCellValue($mapelColMap[$mId] . $currentRow, $data['mapel_stats'][$mId]['max']);
        }
        $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->getFont()->setBold(true);
        $currentRow++;

        // Baris Nilai Terendah
        $sheet->setCellValue('A' . $currentRow, 'Nilai Terendah (Min)');
        $sheet->mergeCells("A{$currentRow}:E{$currentRow}");
        foreach ($data['mapel_list'] as $mId => $mInfo) {
            $sheet->setCellValue($mapelColMap[$mId] . $currentRow, $data['mapel_stats'][$mId]['min']);
        }
        $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->getFont()->setBold(true);
        $currentRow++;

        // Baris % Ketuntasan
        $sheet->setCellValue('A' . $currentRow, '% Ketuntasan (>= KKM)');
        $sheet->mergeCells("A{$currentRow}:E{$currentRow}");
        foreach ($data['mapel_list'] as $mId => $mInfo) {
            $sheet->setCellValue($mapelColMap[$mId] . $currentRow, $data['mapel_stats'][$mId]['persen_tuntas'] . '%');
        }
        $sheet->setCellValue($colAvg . $currentRow, $data['summary']['persen_tuntas_klasikal'] . '%');
        $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->getFont()->setBold(true);

        // Fill warna footer statistik
        $sheet->getStyle("A{$footerStartRow}:{$lastColLetter}{$currentRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF1F5F9');

        // Border seluruh tabel
        $tableRange = "A{$startRow}:{$lastColLetter}{$currentRow}";
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->getColor()->setARGB('FFCBD5E1');

        // Alignment kolom data
        $dataRowsCount = $currentRow - $startRow - 1;
        $sheet->getStyle("A" . ($startRow + 2) . ":A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B" . ($startRow + 2) . ":C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E" . ($startRow + 2) . ":{$lastColLetter}{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 6. Blok Tanda Tangan
        $signRow = $currentRow + 3;
        $sheet->setCellValue('B' . $signRow, 'Mengetahui,');
        $sheet->setCellValue('B' . ($signRow + 1), 'Kepala SMK Wikrama 1 Garut');
        $sheet->setCellValue('B' . ($signRow + 5), 'Drs. H. Mumu, M.Pd.');

        $sigColLetter = Coordinate::stringFromColumnIndex(max(1, $colIdx - 4));
        $sheet->setCellValue($sigColLetter . $signRow, 'Garut, ' . date('d F Y'));
        $sheet->setCellValue($sigColLetter . ($signRow + 1), 'Wali Kelas ' . $data['rombel']['nama']);
        $sheet->setCellValue($sigColLetter . ($signRow + 5), $data['rombel']['wali_kelas']);

        $sheet->getStyle("B{$signRow}:{$sigColLetter}" . ($signRow + 5))->getFont()->setSize(10);
        $sheet->getStyle("B" . ($signRow + 5))->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle($sigColLetter . ($signRow + 5))->getFont()->setBold(true)->setUnderline(true);

        // Auto size kolom
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(14);
        $sheet->getColumnDimension('C')->setWidth(14);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(6);

        foreach ($mapelColMap as $colL) {
            $sheet->getColumnDimension($colL)->setWidth(13);
        }

        $sheet->getColumnDimension($colTotal)->setWidth(11);
        $sheet->getColumnDimension($colAvg)->setWidth(10);
        $sheet->getColumnDimension($colRank)->setWidth(8);
        $sheet->getColumnDimension($colBelow)->setWidth(11);

        return $spreadsheet;
    }

    /**
     * Menghasilkan berkas Excel Rekap Analisis Mapel & Daftar Siswa Remedial (.xlsx).
     */
    public function exportAnalisisMapel(Rombel $rombel, Semester $semester): Spreadsheet
    {
        $data = $this->service->getLegerRombelData($rombel, $semester);
        $spreadsheet = new Spreadsheet();

        // ------------------ SHEET 1: RINGKASAN MAPEL ------------------
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Ringkasan Ketuntasan Mapel');
        $sheet1->setShowGridLines(true);

        // Judul
        $sheet1->setCellValue('A1', 'ANALISIS KETUNTASAN MATA PELAJARAN');
        $sheet1->setCellValue('A2', "Kelas: {$data['rombel']['nama']} | Semester: {$data['semester']['nama']} | TP: {$data['rombel']['tahun_ajaran']}");
        $sheet1->mergeCells('A1:L1');
        $sheet1->mergeCells('A2:L2');
        $sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet1->getStyle('A2')->getFont()->setSize(10);

        // Header Tabel
        $headers1 = [
            'No', 'Kode', 'Mata Pelajaran', 'Guru Pengampu', 'KKM', 'Siswa Dinilai',
            'Rata-rata', 'Nilai Min', 'Nilai Max', 'Jml Tuntas', 'Jml Remedial', '% Ketuntasan'
        ];

        $row1 = 4;
        foreach ($headers1 as $i => $h) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $sheet1->setCellValue($col . $row1, $h);
        }

        $sheet1->getStyle("A{$row1}:L{$row1}")->getFont()->setBold(true)->setSize(10);
        $sheet1->getStyle("A{$row1}:L{$row1}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');
        $sheet1->getStyle("A{$row1}:L{$row1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowIdx = $row1 + 1;
        $no = 1;
        foreach ($data['mapel_stats'] as $ms) {
            $sheet1->setCellValue('A' . $rowIdx, $no++);
            $sheet1->setCellValue('B' . $rowIdx, $ms['kode']);
            $sheet1->setCellValue('C' . $rowIdx, $ms['nama']);
            $sheet1->setCellValue('D' . $rowIdx, $ms['guru']);
            $sheet1->setCellValue('E' . $rowIdx, $ms['kkm']);
            $sheet1->setCellValue('F' . $rowIdx, $ms['count_graded']);
            $sheet1->setCellValue('G' . $rowIdx, $ms['avg']);
            $sheet1->setCellValue('H' . $rowIdx, $ms['min']);
            $sheet1->setCellValue('I' . $rowIdx, $ms['max']);
            $sheet1->setCellValue('J' . $rowIdx, $ms['tuntas_count']);
            $sheet1->setCellValue('K' . $rowIdx, $ms['remedial_count']);
            $sheet1->setCellValue('L' . $rowIdx, $ms['persen_tuntas'] . '%');

            if ($ms['remedial_count'] > 0) {
                $sheet1->getStyle('K' . $rowIdx)->getFont()->setBold(true)->getColor()->setARGB('FFE11D48');
            }

            $rowIdx++;
        }

        $sheet1->getStyle("A{$row1}:L" . ($rowIdx - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet1->getStyle("A{$row1}:L" . ($rowIdx - 1))->getBorders()->getAllBorders()->getColor()->setARGB('FFCBD5E1');

        foreach (range(1, 12) as $colI) {
            $sheet1->getColumnDimension(Coordinate::stringFromColumnIndex($colI))->setAutoSize(true);
        }

        // ------------------ SHEET 2: DAFTAR SISWA REMEDIAL ------------------
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Daftar Siswa Remedial');
        $sheet2->setShowGridLines(true);

        $sheet2->setCellValue('A1', 'DAFTAR SISWA PERLU REMEDIAL (NILAI < KKM)');
        $sheet2->setCellValue('A2', "Kelas: {$data['rombel']['nama']} | Semester: {$data['semester']['nama']}");
        $sheet2->mergeCells('A1:H1');
        $sheet2->mergeCells('A2:H2');
        $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(13);

        $headers2 = ['No', 'Mata Pelajaran', 'KKM', 'NIS', 'Nama Siswa', 'Nilai Akhir', 'Selisih KKM', 'Catatan / Materi Belum Tuntas'];
        $row2 = 4;
        foreach ($headers2 as $i => $h) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $sheet2->setCellValue($col . $row2, $h);
        }

        $sheet2->getStyle("A{$row2}:H{$row2}")->getFont()->setBold(true)->setSize(10);
        $sheet2->getStyle("A{$row2}:H{$row2}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFE4E6');
        $sheet2->getStyle("A{$row2}:H{$row2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowIdx2 = $row2 + 1;
        $no2 = 1;

        foreach ($data['mapel_stats'] as $ms) {
            foreach ($ms['remedial_students'] as $rem) {
                $sheet2->setCellValue('A' . $rowIdx2, $no2++);
                $sheet2->setCellValue('B' . $rowIdx2, $ms['nama']);
                $sheet2->setCellValue('C' . $rowIdx2, $ms['kkm']);
                $sheet2->setCellValueExplicit('D' . $rowIdx2, (string) $rem['nis'], DataType::TYPE_STRING);
                $sheet2->setCellValue('E' . $rowIdx2, $rem['nama']);
                $sheet2->setCellValue('F' . $rowIdx2, $rem['nilai']);
                $sheet2->setCellValue('G' . $rowIdx2, '-' . $rem['selisih']);
                $sheet2->setCellValue('H' . $rowIdx2, $rem['catatan']);

                $sheet2->getStyle('F' . $rowIdx2)->getFont()->setBold(true)->getColor()->setARGB('FFE11D48');
                $rowIdx2++;
            }
        }

        if ($no2 === 1) {
            $sheet2->setCellValue('A5', 'Seluruh siswa telah tuntas pada seluruh mata pelajaran (Tidak ada siswa remedial).');
            $sheet2->mergeCells('A5:H5');
            $sheet2->getStyle('A5')->getFont()->setItalic(true)->getColor()->setARGB('FF059669');
            $rowIdx2 = 6;
        }

        $sheet2->getStyle("A{$row2}:H" . ($rowIdx2 - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet2->getStyle("A{$row2}:H" . ($rowIdx2 - 1))->getBorders()->getAllBorders()->getColor()->setARGB('FFCBD5E1');

        foreach (range(1, 8) as $colI) {
            $sheet2->getColumnDimension(Coordinate::stringFromColumnIndex($colI))->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);
        return $spreadsheet;
    }
}
