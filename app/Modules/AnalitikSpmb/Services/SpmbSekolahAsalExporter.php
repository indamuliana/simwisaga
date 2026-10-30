<?php

namespace App\Modules\AnalitikSpmb\Services;

use App\Modules\AnalitikSpmb\Models\SpmbSekolahAsal;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SpmbSekolahAsalExporter
{
    /**
     * Export rekapitulasi data feeder SMP/MTs ke Excel
     */
    public function exportRekapSekolah(Collection $sekolahs, array $summary, array $jurusans): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Asal SMP');

        // Header Title
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'REKAPITULASI ANALITIK ASAL SMP / MTS (FEEDER SISWA)');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', 'SMK WIKRAMA 1 GARUT - SIMWISAGA / ANALITIK SPMB');
        $sheet->getStyle('A2')->getFont()->setSize(11)->setItalic(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A3:L3');
        $sheet->setCellValue('A3', 'Dicetak pada: ' . date('d-m-Y H:i') . ' WIB | Total Feeder: ' . ($summary['totalSekolah'] ?? 0) . ' Sekolah | Total Siswa: ' . ($summary['totalSiswa'] ?? 0) . ' Siswa');
        $sheet->getStyle('A3')->getFont()->setSize(9);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Table Columns
        $headers = [
            'No',
            'Nama Sekolah (Master)',
            'NPSN',
            'Bentuk',
            'Status',
            'Kecamatan',
            'Tier Feeder',
            'Total Siswa',
            'Kontribusi (%)',
            'Laki-laki',
            'Perempuan',
            'Jurusan Terbanyak',
        ];

        // Tambah kolom per jurusan
        foreach ($jurusans as $j) {
            $headers[] = $j->kode;
        }

        // Tambah kolom catatan
        $headers[] = 'Catatan Master';

        $headerRow = 5;
        $colIndex = 1;
        foreach ($headers as $header) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue("{$colLetter}{$headerRow}", $header);
            $colIndex++;
        }

        $lastColLetter = Coordinate::stringFromColumnIndex(count($headers));

        // Styling Header
        $sheet->getStyle("A{$headerRow}:{$lastColLetter}{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00519E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D0D7DE']]],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(26);

        // Data Rows
        $currentRow = 6;
        $no = 1;
        foreach ($sekolahs as $item) {
            $sheet->setCellValue("A{$currentRow}", $no++);
            $sheet->setCellValue("B{$currentRow}", $item->nama_resmi);
            $sheet->setCellValueExplicit("C{$currentRow}", $item->npsn ?: '-', DataType::TYPE_STRING);
            $sheet->setCellValue("D{$currentRow}", $item->jenis ?: 'SMP');
            $sheet->setCellValue("E{$currentRow}", $item->status_sekolah ?: 'Negeri');
            $sheet->setCellValue("F{$currentRow}", $item->kecamatan ?: '-');
            $sheet->setCellValue("G{$currentRow}", $item->tier ?: 'Rintisan');
            $sheet->setCellValue("H{$currentRow}", (int) $item->total_siswa);
            $sheet->setCellValue("I{$currentRow}", ($item->percentage ?? 0) . '%');
            $sheet->setCellValue("J{$currentRow}", (int) $item->siswa_l);
            $sheet->setCellValue("K{$currentRow}", (int) $item->siswa_p);
            $sheet->setCellValue("L{$currentRow}", $item->dominant_jurusan ?? '-');

            $colIdx = 13;
            foreach ($jurusans as $j) {
                $jLetter = Coordinate::stringFromColumnIndex($colIdx);
                $val = (int) ($item->{'jurusan_' . $j->id} ?? 0);
                $sheet->setCellValue("{$jLetter}{$currentRow}", $val);
                $colIdx++;
            }

            // Catatan Master
            $sheet->setCellValue("{$lastColLetter}{$currentRow}", $item->catatan ?: '-');

            $lastJurusanColLetter = Coordinate::stringFromColumnIndex(12 + count($jurusans));

            // Alignments
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$currentRow}:G{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H{$currentRow}:K{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("L{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            if (count($jurusans) > 0) {
                $sheet->getStyle("M{$currentRow}:{$lastJurusanColLetter}{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $sheet->getStyle("{$lastColLetter}{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $currentRow++;
        }

        // Total Row
        $sheet->setCellValue("A{$currentRow}", 'TOTAL');
        $sheet->mergeCells("A{$currentRow}:G{$currentRow}");
        $sheet->setCellValue("H{$currentRow}", "=SUM(H6:H" . ($currentRow - 1) . ")");
        $sheet->setCellValue("I{$currentRow}", '100%');
        $sheet->setCellValue("J{$currentRow}", "=SUM(J6:J" . ($currentRow - 1) . ")");
        $sheet->setCellValue("K{$currentRow}", "=SUM(K6:K" . ($currentRow - 1) . ")");
        $sheet->setCellValue("L{$currentRow}", '-');

        $colIdx = 13;
        foreach ($jurusans as $j) {
            $jLetter = Coordinate::stringFromColumnIndex($colIdx);
            $sheet->setCellValue("{$jLetter}{$currentRow}", "=SUM({$jLetter}6:{$jLetter}" . ($currentRow - 1) . ")");
            $colIdx++;
        }

        $sheet->setCellValue("{$lastColLetter}{$currentRow}", '-');

        $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D0D7DE']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("H{$currentRow}:{$lastColLetter}{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // All Borders
        $sheet->getStyle("A6:{$lastColLetter}" . ($currentRow - 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Auto width
        for ($i = 1; $i <= count($headers); $i++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Export daftar siswa dari sekolah asal tertentu
     */
    public function exportDaftarSiswa(SpmbSekolahAsal $sekolah, Collection $siswas): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Siswa');

        // Header Title
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'DAFTAR SISWA ASAL SEKOLAH: ' . strtoupper($sekolah->nama_resmi));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', 'SMK WIKRAMA 1 GARUT | Total Siswa: ' . $siswas->count() . ' Orang');
        $sheet->getStyle('A2')->getFont()->setSize(10)->setItalic(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = [
            'No',
            'NIS / NIPD',
            'NISN',
            'Nama Lengkap',
            'L/P',
            'Tingkat',
            'Rombel (Kelas)',
            'Jurusan',
            'Status',
        ];

        $headerRow = 4;
        $colIndex = 1;
        foreach ($headers as $h) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue("{$colLetter}{$headerRow}", $h);
            $colIndex++;
        }

        $lastCol = Coordinate::stringFromColumnIndex(count($headers));

        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00519E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D0D7DE']]],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(24);

        $currentRow = 5;
        $no = 1;
        foreach ($siswas as $s) {
            $sheet->setCellValue("A{$currentRow}", $no++);
            $sheet->setCellValueExplicit("B{$currentRow}", $s->nipd ?: '-', DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("C{$currentRow}", $s->nisn ?: '-', DataType::TYPE_STRING);
            $sheet->setCellValue("D{$currentRow}", $s->nama);
            $sheet->setCellValue("E{$currentRow}", $s->jk ?: '-');
            $sheet->setCellValue("F{$currentRow}", $s->rombel?->tingkat ?: '-');
            $sheet->setCellValue("G{$currentRow}", $s->rombel?->nama ?: '-');
            $sheet->setCellValue("H{$currentRow}", $s->rombel?->jurusan?->nama ?: ($s->rombel?->jurusan?->kode ?: '-'));
            $sheet->setCellValue("I{$currentRow}", ucfirst($s->status ?: 'aktif'));

            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$currentRow}:C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$currentRow}:F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $currentRow++;
        }

        $sheet->getStyle("A5:{$lastCol}" . ($currentRow - 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        for ($i = 1; $i <= count($headers); $i++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
