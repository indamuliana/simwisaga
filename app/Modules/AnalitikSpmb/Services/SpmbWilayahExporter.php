<?php

namespace App\Modules\AnalitikSpmb\Services;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SpmbWilayahExporter
{
    /**
     * Ekspor Rekapitulasi Sebaran Wilayah ke Excel
     */
    public function exportRekap(Collection $items, Collection $jurusans, array $stats): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sebaran Wilayah Siswa');

        // Header Dokumen
        $sheet->setCellValue('A1', 'REKAPITULASI SEBARAN WILAYAH ASAL SISWA (SPMB)');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('A2', 'Total Siswa: ' . ($stats['totalSiswa'] ?? 0) . ' | Negara: ' . ($stats['statNegara'] ?? 0) . ' | Provinsi: ' . ($stats['statProvinsi'] ?? 0) . ' | Kab/Kota: ' . ($stats['statKabupaten'] ?? 0) . ' | Siswa Luar Negeri: ' . ($stats['statLuarNegeri'] ?? 0));
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);

        // Header Kolom Tabel
        $headers = [
            'No',
            'Negara',
            'Provinsi',
            'Kabupaten / Kota',
            'Kecamatan',
            'Total Siswa',
            'L',
            'P',
        ];

        foreach ($jurusans as $j) {
            $headers[] = $j->kode;
        }

        $headers[] = 'Dominan Jurusan';
        $headers[] = 'Persentase (%)';

        $sheet->fromArray([$headers], null, 'A4');
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));

        // Style Header Tabel
        $headerRange = "A4:{$lastCol}4";
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowNum = 5;
        foreach ($items as $idx => $item) {
            $row = [
                $idx + 1,
                $item->negara ?? 'Indonesia',
                $item->provinsi ?? '-',
                $item->kabupaten_kota ?? '-',
                $item->kecamatan ?? '-',
                $item->total_siswa,
                $item->siswa_l,
                $item->siswa_p,
            ];

            foreach ($jurusans as $j) {
                $row[] = (int) ($item->{'jurusan_' . $j->id} ?? 0);
            }

            $row[] = $item->dominant_jurusan ?? '-';
            $row[] = ($item->percentage ?? 0) . '%';

            $sheet->fromArray([$row], null, "A{$rowNum}");
            $rowNum++;
        }

        // Borders
        $sheet->getStyle("A4:{$lastCol}" . ($rowNum - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Ekspor Detail Siswa pada Wilayah Tertentu
     */
    public function exportDrawer(Collection $siswas, string $wilayahTitle): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Siswa');

        $sheet->setCellValue('A1', 'DAFTAR SISWA - ' . strtoupper($wilayahTitle));
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $headers = [
            'No',
            'Nama Siswa',
            'NISN',
            'NIPD',
            'L/P',
            'Rombel / Kelas',
            'Jurusan',
            'Sekolah Asal',
            'Alamat Lengkap',
            'Kecamatan',
            'Kabupaten / Kota',
            'Provinsi',
            'Negara',
        ];

        $sheet->fromArray([$headers], null, 'A3');
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));

        $headerRange = "A3:{$lastCol}3";
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');

        $rowNum = 4;
        foreach ($siswas as $idx => $s) {
            $row = [
                $idx + 1,
                $s->nama,
                $s->nisn ?? '-',
                $s->nipd ?? '-',
                $s->jk,
                $s->rombel?->nama ?? '-',
                $s->rombel?->jurusan?->nama ?? '-',
                $s->sekolah_asal ?? '-',
                $s->alamat ?? '-',
                $s->kecamatan ?? '-',
                $s->kabupaten_kota ?? '-',
                $s->provinsi ?? '-',
                $s->negara ?? 'Indonesia',
            ];

            $sheet->fromArray([$row], null, "A{$rowNum}");
            $rowNum++;
        }

        $sheet->getStyle("A3:{$lastCol}" . ($rowNum - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
