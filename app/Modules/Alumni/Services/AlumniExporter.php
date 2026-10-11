<?php

namespace App\Modules\Alumni\Services;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AlumniExporter
{
    public function export(Collection $alumnis, ?string $tahunLulus = null): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Alumni');

        // Judul Laporan
        $sheet->setCellValue('A1', 'LAPORAN DATA ALUMNI & TRACER STUDY (BMW)');
        $sub = 'SMK Wikrama 1 Garut' . ($tahunLulus ? " - Angkatan/Tahun Lulus {$tahunLulus}" : '');
        $sheet->setCellValue('A2', $sub);
        $sheet->setCellValue('A3', 'Dicetak pada: ' . date('d-m-Y H:i'));

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9);

        // Header Tabel di Baris 5
        $headers = [
            'A' => 'No',
            'B' => 'NISN',
            'C' => 'NIS/NIPD',
            'D' => 'Nama Lengkap Siswa',
            'E' => 'JK',
            'F' => 'Tahun Lulus',
            'G' => 'Nomor Seri Ijazah',
            'H' => 'Status Karir (BMW)',
            'I' => 'Nama Instansi / Kampus / Usaha',
            'J' => 'Posisi / Jabatan / Program Studi',
            'K' => 'Estimasi Pendapatan',
            'L' => 'No. HP / WhatsApp',
            'M' => 'Email',
            'N' => 'Alamat Terkini',
            'O' => 'LinkedIn / Portofolio',
            'P' => 'Catatan Tambahan',
        ];

        foreach ($headers as $col => $title) {
            $sheet->setCellValue($col . '5', $title);
        }

        // Style Header
        $headerRange = 'A5:P5';
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A8A');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(5)->setRowHeight(28);

        $row = 6;
        foreach ($alumnis as $index => $alumni) {
            $siswa = $alumni->siswa;
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValueExplicit('B' . $row, $siswa?->nisn ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $row, $siswa?->nipd ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, $siswa?->nama ?? '-');
            $sheet->setCellValue('E' . $row, $siswa?->jk ?? '-');
            $sheet->setCellValue('F' . $row, $alumni->tahun_lulus);
            $sheet->setCellValueExplicit('G' . $row, $alumni->nomor_ijazah ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValue('H' . $row, $alumni->status_karir_badge);
            $sheet->setCellValue('I' . $row, $alumni->nama_instansi ?? '-');
            $sheet->setCellValue('J' . $row, $alumni->posisi_jurusan ?? '-');
            $sheet->setCellValue('K' . $row, $alumni->pendapatan_range ?? '-');
            $sheet->setCellValueExplicit('L' . $row, $alumni->no_hp_terkini ?: ($siswa?->hp ?: '-'), DataType::TYPE_STRING);
            $sheet->setCellValue('M' . $row, $alumni->email_terkini ?: ($siswa?->email ?: '-'));
            $sheet->setCellValue('N' . $row, $alumni->alamat_terkini ?: ($siswa?->alamat ?: '-'));
            $sheet->setCellValue('O' . $row, $alumni->linkedin_url ?? '-');
            $sheet->setCellValue('P' . $row, $alumni->catatan ?? '-');

            $row++;
        }

        $lastRow = max(6, $row - 1);
        $dataRange = 'A5:P' . $lastRow;
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D1D5DB');

        // Center alignments
        $sheet->getStyle('A6:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B6:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E6:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H6:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto width
        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
