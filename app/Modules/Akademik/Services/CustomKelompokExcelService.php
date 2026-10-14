<?php

namespace App\Modules\Akademik\Services;

use App\Modules\Akademik\Models\CustomKelompok;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Sistem\Models\School;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CustomKelompokExcelService
{
    /**
     * Download format template Excel untuk import anggota kelompok
     */
    public function downloadTemplate(CustomKelompok $kelompok): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');

        // Petunjuk pengisian di baris atas
        $sheet->setCellValue('A1', 'PETUNJUK PENGISIAN IMPORT SISWA KELOMPOK: ' . strtoupper($kelompok->nama));
        $sheet->setCellValue('A2', '1. Masukkan NIS / NIPD siswa yang valid (terdaftar di sistem SIRAGA).');
        $sheet->setCellValue('A3', '2. Kolom Nama Siswa bersifat referensi/opsional (pencarian utama menggunakan NIS).');
        $sheet->setCellValue('A4', '3. Kolom Keterangan Siswa opsional (contoh: Ketua, Anggota Inti, Remedial Bab 1, dll).');
        $sheet->setCellValue('A5', '4. Jangan mengubah susunan baris header di baris ke-7.');

        $sheet->getStyle('A1:A5')->getFont()->setSize(9)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748B'));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E293B'));

        // Table Header di baris 7
        $headers = [
            'A7' => 'No',
            'B7' => 'NIS / NIPD (*Wajib)',
            'C7' => 'Nama Siswa (Referensi)',
            'D7' => 'Keterangan Khusus Siswa (Opsional)',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A7:D7')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A7:D7')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E3A8A');
        $sheet->getStyle('A7:D7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Contoh baris sample di baris 8
        $sheet->setCellValue('A8', 1);
        $sheet->setCellValueExplicit('B8', '12345678', DataType::TYPE_STRING);
        $sheet->setCellValue('C8', 'Contoh Nama Siswa');
        $sheet->setCellValue('D8', 'Anggota');

        $sheet->getStyle('A8:D8')->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748B'));

        // Border styling
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'CBD5E1'],
                ],
            ],
        ];
        $sheet->getStyle('A7:D8')->applyFromArray($borderStyle);

        // Auto column width
        foreach (['A', 'B', 'C', 'D'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Memproses import file Excel ke dalam kelompok terpilih
     */
    public function importMembers(CustomKelompok $kelompok, string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestDataRow();

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        // Deteksi baris awal data (cari baris setelah header NIS)
        $startRow = 8;
        for ($r = 1; $r <= min(15, $highestRow); $r++) {
            $val = trim((string) $sheet->getCell("B{$r}")->getValue());
            if (str_contains(strtolower($val), 'nis') || str_contains(strtolower($val), 'nipd')) {
                $startRow = $r + 1;
                break;
            }
        }

        for ($row = $startRow; $row <= $highestRow; $row++) {
            $nis = trim((string) $sheet->getCell("B{$row}")->getValue());
            $keterangan = trim((string) $sheet->getCell("D{$row}")->getValue());

            if (empty($nis)) {
                continue;
            }

            // Cari siswa berdasarkan nipd atau nisn
            $siswa = Siswa::where('nipd', $nis)
                ->orWhere('nisn', $nis)
                ->first();

            if (! $siswa) {
                $nama = trim((string) $sheet->getCell("C{$row}")->getValue());
                if (! empty($nama)) {
                    $siswa = Siswa::where('nama', 'like', "%{$nama}%")->first();
                }
            }

            if (! $siswa) {
                $skipped++;
                $errors[] = "Baris {$row}: Siswa dengan NIS '{$nis}' tidak ditemukan.";
                continue;
            }

            // Periksa kapasitas kuota jika ada
            if ($kelompok->kapasitas && $kelompok->siswas()->count() >= $kelompok->kapasitas) {
                // Jangan tambah lagi jika sudah mencapai kapasitas
                $existing = $kelompok->siswas()->where('siswa_id', $siswa->id)->exists();
                if (! $existing) {
                    $skipped++;
                    $errors[] = "Baris {$row}: Kapasitas maksimal kelompok ({$kelompok->kapasitas} siswa) sudah terpenuhi.";
                    continue;
                }
            }

            $existing = $kelompok->siswas()->where('siswa_id', $siswa->id)->first();
            if ($existing) {
                // Update keterangan jika ada
                if (! empty($keterangan)) {
                    $kelompok->siswas()->updateExistingPivot($siswa->id, [
                        'keterangan' => $keterangan,
                    ]);
                    $updated++;
                } else {
                    $skipped++;
                }
            } else {
                $kelompok->siswas()->attach($siswa->id, [
                    'keterangan' => $keterangan ?: null,
                ]);
                $imported++;
            }
        }

        return [
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Export data anggota kelompok terpilih ke format Excel
     */
    public function exportMembers(CustomKelompok $kelompok): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Anggota Kelompok');

        $kelompok->load(['pembimbing', 'tahunAjaran']);
        $members = $kelompok->siswas()->with(['rombel', 'rayon'])->orderBy('nama')->get();

        // Judul & Info Kelompok
        $sheet->setCellValue('A1', 'DAFTAR ANGGOTA KELOMPOK: ' . strtoupper($kelompok->nama));
        $sheet->setCellValue('A2', 'Kode: ' . ($kelompok->kode ?: '-') . ' | Kategori: ' . ($kelompok->kategori ?: '-') . ' | Pembimbing: ' . ($kelompok->pembimbing?->name ?: '-'));
        $sheet->setCellValue('A3', 'Tahun Ajaran: ' . ($kelompok->tahunAjaran?->nama ?: 'Umum / Semua Tahun') . ' | Total Anggota: ' . $members->count() . ' Siswa');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A2:A3')->getFont()->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('475569'));

        // Table Header
        $headers = [
            'A5' => 'No',
            'B5' => 'NIS / NIPD',
            'C5' => 'NISN',
            'D5' => 'Nama Siswa',
            'E5' => 'JK',
            'F5' => 'Rombel',
            'G5' => 'Rayon',
            'H5' => 'Keterangan Siswa',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A5:H5')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A5:H5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F766E');
        $sheet->getStyle('A5:H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = 6;
        foreach ($members as $index => $siswa) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValueExplicit("B{$row}", $siswa->nipd ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("C{$row}", $siswa->nisn ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValue("D{$row}", $siswa->nama);
            $sheet->setCellValue("E{$row}", $siswa->jk);
            $sheet->setCellValue("F{$row}", $siswa->rombel?->nama ?? '-');
            $sheet->setCellValue("G{$row}", $siswa->rayon?->nama ?? '-');
            $sheet->setCellValue("H{$row}", $siswa->pivot->keterangan ?? '-');

            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}:C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        $lastRow = max(6, $row - 1);
        $sheet->getStyle("A5:H{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'CBD5E1'],
                ],
            ],
        ]);

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Export rekap seluruh siswa beserta tag-tag kelompoknya (Tab 1)
     */
    public function exportSiswaTags($siswas): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Siswa dan Tag Kelompok');

        $school = School::current();
        $sheet->setCellValue('A1', strtoupper($school->nama ?? 'SMK WIKRAMA 1 GARUT'));
        $sheet->setCellValue('A2', 'REKAPITULASI SISWA & KELOMPOK CUSTOM');
        $sheet->setCellValue('A3', 'Diekspor pada: ' . date('d/m/Y H:i'));

        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3')->getFont()->setSize(9)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748B'));

        $headers = [
            'A5' => 'No',
            'B5' => 'NIS',
            'C5' => 'NISN',
            'D5' => 'Nama Lengkap Siswa',
            'E5' => 'JK',
            'F5' => 'Rombel',
            'G5' => 'Rayon',
            'H5' => 'Jumlah Kelompok',
            'I5' => 'Daftar Kelompok Yang Diikuti',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A5:I5')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A5:I5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E293B');
        $sheet->getStyle('A5:I5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = 6;
        foreach ($siswas as $idx => $siswa) {
            $kelompokNames = $siswa->customKelompoks->map(function ($k) {
                return $k->nama . ($k->pivot->keterangan ? " ({$k->pivot->keterangan})" : "");
            })->implode(', ');

            $sheet->setCellValue("A{$row}", $idx + 1);
            $sheet->setCellValueExplicit("B{$row}", $siswa->nipd ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("C{$row}", $siswa->nisn ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValue("D{$row}", $siswa->nama);
            $sheet->setCellValue("E{$row}", $siswa->jk);
            $sheet->setCellValue("F{$row}", $siswa->rombel?->nama ?? '-');
            $sheet->setCellValue("G{$row}", $siswa->rayon?->nama ?? '-');
            $sheet->setCellValue("H{$row}", $siswa->customKelompoks->count());
            $sheet->setCellValue("I{$row}", $kelompokNames ?: 'Belum Ada');

            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}:C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        $lastRow = max(6, $row - 1);
        $sheet->getStyle("A5:I{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'CBD5E1'],
                ],
            ],
        ]);

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Cetak Lembar Absensi format Excel (.xlsx) dengan kolom presensi dinamis (10, 16, 20, 25, 30)
     */
    public function exportAttendanceSheet(CustomKelompok $kelompok, array $options = []): Spreadsheet
    {
        $kelompok->load(['pembimbing', 'tahunAjaran']);
        $members = $kelompok->siswas()->with(['rombel', 'rayon'])->orderBy('nama')->get();

        $jumlahKolom = max(5, min(40, (int) ($options['jumlahKolom'] ?? 20)));
        $tahunPelajaran = $options['tahunPelajaran'] ?? ($kelompok->tahunAjaran?->nama ?? '2026/2027');
        $mapel = $options['mapel'] ?? ($kelompok->kategori ? "Kelompok {$kelompok->kategori}" : 'Kegiatan Pembelajaran');
        $namaGuru = $options['namaGuru'] ?? ($kelompok->pembimbing?->name ?? '.........................................');
        $nipGuru = $options['nipGuru'] ?? '';

        $school = School::current();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Hadir');

        // Orientasi Kertas Portrait A4
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $lastColIndex = 5 + $jumlahKolom;
        $lastColLetter = Coordinate::stringFromColumnIndex($lastColIndex);

        $startRow = 1;

        // Header Kop Surat
        if (! empty($school->kop_surat_path) && file_exists(public_path('storage/' . $school->kop_surat_path))) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Kop Surat');
            $drawing->setPath(public_path('storage/' . $school->kop_surat_path));
            $drawing->setCoordinates('A1');
            $drawing->setHeight(75);
            $drawing->setWorksheet($sheet);
            $sheet->getRowDimension(1)->setRowHeight(55);
            $startRow = 3;
        } else {
            $sheet->setCellValue('A1', 'YAYASAN PRAWITAMA');
            $sheet->setCellValue('A2', strtoupper($school->nama ?? 'SMK WIKRAMA 1 GARUT'));
            $sheet->mergeCells("A1:{$lastColLetter}1");
            $sheet->mergeCells("A2:{$lastColLetter}2");
            $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $startRow = 4;
        }

        // Judul Dokumen
        $sheet->setCellValue("A{$startRow}", 'DAFTAR HADIR SISWA - KELOMPOK KHUSUS');
        $sheet->setCellValue("A" . ($startRow + 1), strtoupper($kelompok->nama) . ($kelompok->kode ? " ({$kelompok->kode})" : ''));
        $sheet->setCellValue("A" . ($startRow + 2), 'TAHUN PELAJARAN ' . $tahunPelajaran);

        $sheet->mergeCells("A{$startRow}:{$lastColLetter}{$startRow}");
        $sheet->mergeCells("A" . ($startRow + 1) . ":{$lastColLetter}" . ($startRow + 1));
        $sheet->mergeCells("A" . ($startRow + 2) . ":{$lastColLetter}" . ($startRow + 2));

        $sheet->getStyle("A{$startRow}:A" . ($startRow + 2))->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("A{$startRow}:A" . ($startRow + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Metadata Info Bar
        $metaRow = $startRow + 4;
        $sheet->setCellValue("A{$metaRow}", 'Kategori: ' . ($kelompok->kategori ?: '-'));
        $midColLetter = Coordinate::stringFromColumnIndex(max(4, (int) ($lastColIndex / 2)));
        $sheet->setCellValue("{$midColLetter}{$metaRow}", 'Kegiatan/Materi: ' . $mapel);
        $sheet->setCellValue("A" . ($metaRow + 1), 'Pembimbing: ' . $namaGuru);

        $sheet->getStyle("A{$metaRow}:{$lastColLetter}" . ($metaRow + 1))->getFont()->setName('Arial')->setSize(9);

        // Table Header
        $tableHeaderRow = $metaRow + 3;
        $sheet->setCellValue("A{$tableHeaderRow}", 'No');
        $sheet->setCellValue("B{$tableHeaderRow}", 'NIS');
        $sheet->setCellValue("C{$tableHeaderRow}", 'Nama Siswa');
        $sheet->setCellValue("D{$tableHeaderRow}", 'JK');
        $sheet->setCellValue("E{$tableHeaderRow}", 'Rombel');

        $sheet->mergeCells("A{$tableHeaderRow}:A" . ($tableHeaderRow + 1));
        $sheet->mergeCells("B{$tableHeaderRow}:B" . ($tableHeaderRow + 1));
        $sheet->mergeCells("C{$tableHeaderRow}:C" . ($tableHeaderRow + 1));
        $sheet->mergeCells("D{$tableHeaderRow}:D" . ($tableHeaderRow + 1));
        $sheet->mergeCells("E{$tableHeaderRow}:E" . ($tableHeaderRow + 1));

        $startPertemuanColIndex = 6;
        $endPertemuanColLetter = Coordinate::stringFromColumnIndex($lastColIndex);
        $startPertemuanColLetter = Coordinate::stringFromColumnIndex($startPertemuanColIndex);

        $sheet->setCellValue("{$startPertemuanColLetter}{$tableHeaderRow}", 'Pertemuan / Tanggal Kehadiran');
        $sheet->mergeCells("{$startPertemuanColLetter}{$tableHeaderRow}:{$endPertemuanColLetter}{$tableHeaderRow}");

        for ($k = 1; $k <= $jumlahKolom; $k++) {
            $colLetter = Coordinate::stringFromColumnIndex(5 + $k);
            $sheet->setCellValue("{$colLetter}" . ($tableHeaderRow + 1), $k);
            $sheet->getColumnDimension($colLetter)->setWidth(3.5);
        }

        $sheet->getStyle("A{$tableHeaderRow}:{$lastColLetter}" . ($tableHeaderRow + 1))->getFont()->setBold(true)->setSize(8.5);
        $sheet->getStyle("A{$tableHeaderRow}:{$lastColLetter}" . ($tableHeaderRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A{$tableHeaderRow}:{$lastColLetter}" . ($tableHeaderRow + 1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF1F5F9');

        // Baris Siswa
        $row = $tableHeaderRow + 2;
        foreach ($members as $index => $siswa) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValueExplicit("B{$row}", $siswa->nipd ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValue("C{$row}", $siswa->nama . ($siswa->pivot->keterangan ? " [{$siswa->pivot->keterangan}]" : ""));
            $sheet->setCellValue("D{$row}", $siswa->jk);
            $sheet->setCellValue("E{$row}", $siswa->rombel?->nama ?? '-');

            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Kotak presensi dikosongkan
            for ($k = 1; $k <= $jumlahKolom; $k++) {
                $colLetter = Coordinate::stringFromColumnIndex(5 + $k);
                $sheet->setCellValue("{$colLetter}{$row}", '');
            }

            $sheet->getRowDimension($row)->setRowHeight(19);
            $row++;
        }

        $lastDataRow = max($tableHeaderRow + 2, $row - 1);
        $sheet->getStyle("A{$tableHeaderRow}:{$lastColLetter}{$lastDataRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        $sheet->getColumnDimension('A')->setWidth(4);
        $sheet->getColumnDimension('B')->setWidth(11);
        $sheet->getColumnDimension('C')->setWidth(26);
        $sheet->getColumnDimension('D')->setWidth(4);
        $sheet->getColumnDimension('E')->setWidth(13);

        // Footer Tanda Tangan
        $signRow = $lastDataRow + 3;
        $signColLetter = Coordinate::stringFromColumnIndex(max(1, $lastColIndex - 6));
        $sheet->setCellValue("{$signColLetter}{$signRow}", 'Garut, .................................... 20....');
        $sheet->setCellValue("{$signColLetter}" . ($signRow + 1), 'Guru / Pembimbing Kelompok,');
        $sheet->setCellValue("{$signColLetter}" . ($signRow + 5), $namaGuru);
        if ($nipGuru) {
            $sheet->setCellValue("{$signColLetter}" . ($signRow + 6), 'NIP. ' . $nipGuru);
        }

        $sheet->getStyle("{$signColLetter}{$signRow}:{$lastColLetter}" . ($signRow + 6))->getFont()->setSize(9);
        $sheet->getStyle("{$signColLetter}" . ($signRow + 5))->getFont()->setBold(true)->setUnderline(true);

        return $spreadsheet;
    }
}
