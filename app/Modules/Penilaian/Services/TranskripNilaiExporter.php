<?php

namespace App\Modules\Penilaian\Services;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TranskripNilaiExporter
{
    protected TranskripNilaiService $service;

    public function __construct(TranskripNilaiService $service)
    {
        $this->service = $service;
    }

    /**
     * Menghasilkan berkas Excel Leger Transkrip Siswa 3 Tahun (.xlsx).
     */
    public function exportCohortTranskrip(
        int $jurusanId,
        string $tingkat = 'XII',
        ?int $rombelId = null,
        ?string $search = null,
        ?int $tahunAjaranId = null
    ): Spreadsheet {
        $data = $this->service->getCohortTranskripData($jurusanId, $tingkat, $rombelId, $search, $tahunAjaranId);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Transkrip 3 Tahun');
        $sheet->setShowGridLines(true);

        // 1. KOP / Judul Laporan
        $sheet->setCellValue('A1', 'SMK WIKRAMA 1 GARUT');
        $sheet->setCellValue('A2', 'TRANSKRIP NILAI AKADEMIK PESERTA DIDIK (KELAS X - KELAS XII)');
        $sheet->mergeCells('A1:S1');
        $sheet->mergeCells('A2:S2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // 2. Metadata Jurusan & Tingkat
        $sheet->setCellValue('A4', 'Program Keahlian');
        $sheet->setCellValue('B4', ': ' . $data['jurusan']->nama . ' (' . $data['jurusan']->kode . ')');
        $sheet->setCellValue('A5', 'Tingkat / Angkatan');
        $sheet->setCellValue('B5', ': ' . ($tingkat === 'semua' ? 'Semua Tingkat' : 'Tingkat ' . $tingkat));

        $sheet->setCellValue('G4', 'Total Siswa');
        $sheet->setCellValue('H4', ': ' . $data['total_siswa'] . ' Siswa');
        $sheet->setCellValue('G5', 'Rata-rata Angkatan');
        $sheet->setCellValue('H5', ': ' . number_format($data['stats']['rata_angkatan'], 2));

        $sheet->getStyle('A4:B5')->getFont()->setSize(10);
        $sheet->getStyle('G4:H5')->getFont()->setSize(10);
        $sheet->getStyle('A4:A5')->getFont()->setBold(true);
        $sheet->getStyle('G4:G5')->getFont()->setBold(true);

        // 3. Header Tabel
        $row1 = 7;
        $row2 = 8;

        // Kolom A-F: Identitas
        $sheet->setCellValue("A{$row1}", 'NO');
        $sheet->mergeCells("A{$row1}:A{$row2}");

        $sheet->setCellValue("B{$row1}", 'NIS');
        $sheet->mergeCells("B{$row1}:B{$row2}");

        $sheet->setCellValue("C{$row1}", 'NISN');
        $sheet->mergeCells("C{$row1}:C{$row2}");

        $sheet->setCellValue("D{$row1}", 'NAMA LENGKAP SISWA');
        $sheet->mergeCells("D{$row1}:D{$row2}");

        $sheet->setCellValue("E{$row1}", 'L/P');
        $sheet->mergeCells("E{$row1}:E{$row2}");

        $sheet->setCellValue("F{$row1}", 'ROMBEL');
        $sheet->mergeCells("F{$row1}:F{$row2}");

        // Kolom G-I: KELAS X
        $sheet->setCellValue("G{$row1}", 'KELAS X');
        $sheet->mergeCells("G{$row1}:I{$row1}");
        $sheet->setCellValue("G{$row2}", 'SEM 1');
        $sheet->setCellValue("H{$row2}", 'SEM 2');
        $sheet->setCellValue("I{$row2}", 'RATA X');

        // Kolom J-L: KELAS XI
        $sheet->setCellValue("J{$row1}", 'KELAS XI');
        $sheet->mergeCells("J{$row1}:L{$row1}");
        $sheet->setCellValue("J{$row2}", 'SEM 3');
        $sheet->setCellValue("K{$row2}", 'SEM 4');
        $sheet->setCellValue("L{$row2}", 'RATA XI');

        // Kolom M-O: KELAS XII
        $sheet->setCellValue("M{$row1}", 'KELAS XII');
        $sheet->mergeCells("M{$row1}:O{$row1}");
        $sheet->setCellValue("M{$row2}", 'SEM 5');
        $sheet->setCellValue("N{$row2}", 'SEM 6');
        $sheet->setCellValue("O{$row2}", 'RATA XII');

        // Kolom P-S: REKAP AKHIR TRANSKRIP
        $sheet->setCellValue("P{$row1}", 'NILAI AKHIR KUMULATIF');
        $sheet->mergeCells("P{$row1}:P{$row2}");

        $sheet->setCellValue("Q{$row1}", 'PREDIKAT');
        $sheet->mergeCells("Q{$row1}:Q{$row2}");

        $sheet->setCellValue("R{$row1}", 'RANK');
        $sheet->mergeCells("R{$row1}:R{$row2}");

        $sheet->setCellValue("S{$row1}", 'STATUS KELULUSAN');
        $sheet->mergeCells("S{$row1}:S{$row2}");

        // Styling Header
        $headerRange = "A{$row1}:S{$row2}";
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle($headerRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getStyle("A{$row1}:F{$row2}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0'); // Slate 200

        $sheet->getStyle("G{$row1}:I{$row2}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFDBEAFE'); // Blue 100

        $sheet->getStyle("J{$row1}:L{$row2}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E7FF'); // Indigo 100

        $sheet->getStyle("M{$row1}:O{$row2}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD1FAE5'); // Emerald 100

        $sheet->getStyle("P{$row1}:S{$row2}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFEF3C7'); // Amber 100

        // 4. Data Siswa
        $currentRow = 9;
        $no = 1;

        foreach ($data['students'] as $student) {
            $sheet->setCellValue("A{$currentRow}", $no++);
            $sheet->setCellValueExplicit("B{$currentRow}", $student['nipd'], DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("C{$currentRow}", $student['nisn'], DataType::TYPE_STRING);
            $sheet->setCellValue("D{$currentRow}", $student['nama']);
            $sheet->setCellValue("E{$currentRow}", $student['jk']);
            $sheet->setCellValue("F{$currentRow}", $student['rombel_nama']);

            // Kelas X
            $sheet->setCellValue("G{$currentRow}", $student['s1'] !== null ? $student['s1'] : '-');
            $sheet->setCellValue("H{$currentRow}", $student['s2'] !== null ? $student['s2'] : '-');
            $sheet->setCellValue("I{$currentRow}", $student['rata_x'] !== null ? $student['rata_x'] : '-');

            // Kelas XI
            $sheet->setCellValue("J{$currentRow}", $student['s3'] !== null ? $student['s3'] : '-');
            $sheet->setCellValue("K{$currentRow}", $student['s4'] !== null ? $student['s4'] : '-');
            $sheet->setCellValue("L{$currentRow}", $student['rata_xi'] !== null ? $student['rata_xi'] : '-');

            // Kelas XII
            $sheet->setCellValue("M{$currentRow}", $student['s5'] !== null ? $student['s5'] : '-');
            $sheet->setCellValue("N{$currentRow}", $student['s6'] !== null ? $student['s6'] : '-');
            $sheet->setCellValue("O{$currentRow}", $student['rata_xii'] !== null ? $student['rata_xii'] : '-');

            // Kumulatif
            $sheet->setCellValue("P{$currentRow}", $student['nilai_kumulatif'] > 0 ? $student['nilai_kumulatif'] : '-');
            $sheet->setCellValue("Q{$currentRow}", $student['predikat']);
            $sheet->setCellValue("R{$currentRow}", $student['ranking']);
            $sheet->setCellValue("S{$currentRow}", $student['is_tuntas'] ? 'TUNTAS' : 'REMEDIAL');

            // Formats
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$currentRow}:C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$currentRow}:F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$currentRow}:S{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Bold Kumulatif
            $sheet->getStyle("P{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("R{$currentRow}")->getFont()->setBold(true);

            // Zebra striping
            if ($no % 2 === 0) {
                $sheet->getStyle("A{$currentRow}:S{$currentRow}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF8FAFC');
            }

            $currentRow++;
        }

        // 5. Border Seluruh Tabel
        $lastRow = $currentRow - 1;
        if ($lastRow >= 9) {
            $sheet->getStyle("A{$row1}:S{$lastRow}")->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)
                ->getColor()->setARGB('FFCBD5E1');

            // Border tebal untuk header
            $sheet->getStyle("A{$row1}:S{$row2}")->getBorders()->getOutline()
                ->setBorderStyle(Border::BORDER_MEDIUM)
                ->getColor()->setARGB('FF475569');
        }

        // Auto-width columns
        foreach (range('A', 'S') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('D')->setWidth(32); // Nama siswa

        return $spreadsheet;
    }
}
