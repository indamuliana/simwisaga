<?php

namespace App\Modules\SeniBudaya\Services;

use App\Modules\SeniBudaya\Models\SeniBudaya;
use App\Modules\SeniBudaya\Models\SeniBudayaAnggota;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class SeniBudayaExcelService
{
    public function __construct(protected SeniBudayaService $seniBudayaService) {}

    /**
     * Generate template Excel untuk pendaftaran anggota Seni Budaya
     */
    public function generateTemplate(?int $rombelId = null, ?string $tingkat = null): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pendaftaran Siswa');

        // Header Dokumen
        $sheet->setCellValue('A1', 'TEMPLATE PENDAFTARAN MATA PELAJARAN SENI BUDAYA');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Petunjuk: Isi kolom "Cabang Seni Budaya" sesuai daftar cabang pada Sheet "Referensi Cabang". Jangan ubah NISN/NIS.');
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);

        // Header Kolom
        $headers = ['No', 'NISN', 'NIS / NIPD', 'Nama Siswa', 'Rombel / Kelas', 'Cabang Seni Budaya'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F'];

        foreach ($headers as $idx => $title) {
            $sheet->setCellValue($cols[$idx] . '4', $title);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7C3AED']], // Warna Ungu/Primary
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A4:F4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(25);

        // Ambil Data Siswa
        $query = Siswa::with('rombel')->where('status', 'aktif');
        if ($rombelId) {
            $query->where('rombel_id', $rombelId);
        }
        if ($tingkat) {
            $query->whereHas('rombel', fn($q) => $q->where('tingkat', $tingkat));
        }

        $siswas = $query->orderBy('nama')->get();

        $row = 5;
        $no = 1;
        foreach ($siswas as $s) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValueExplicit('B' . $row, (string) $s->nisn, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $row, (string) ($s->nipd ?? $s->nik ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, $s->nama);
            $sheet->setCellValue('E' . $row, $s->rombel?->nama ?? '-');
            $sheet->setCellValue('F' . $row, ''); // Kolom Cabang Seni Budaya diisi pengguna

            $row++;
        }

        $lastRow = max(5, $row - 1);
        $dataBorderStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ];
        $sheet->getStyle('A5:F' . $lastRow)->applyFromArray($dataBorderStyle);

        // Sheet 2: Referensi Cabang Seni Budaya
        $sheetRef = $spreadsheet->createSheet();
        $sheetRef->setTitle('Referensi Cabang');
        $sheetRef->setCellValue('A1', 'DAFTAR CABANG SENI BUDAYA AKTIF');
        $sheetRef->getStyle('A1')->getFont()->setBold(true)->setSize(12);

        $sheetRef->setCellValue('A3', 'Kode Cabang');
        $sheetRef->setCellValue('B3', 'Nama Cabang Seni Budaya');
        $sheetRef->setCellValue('C3', 'Guru Pengampu');
        $sheetRef->setCellValue('D3', 'KKM');
        $sheetRef->getStyle('A3:D3')->getFont()->setBold(true);

        $cabangs = SeniBudaya::with('guru')->where('is_active', true)->get();
        $refRow = 4;
        foreach ($cabangs as $c) {
            $sheetRef->setCellValue('A' . $refRow, $c->kode);
            $sheetRef->setCellValue('B' . $refRow, $c->nama);
            $sheetRef->setCellValue('C' . $refRow, $c->guru_display_name);
            $sheetRef->setCellValue('D' . $refRow, $c->kkm);
            $refRow++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        foreach (range('A', 'D') as $col) {
            $sheetRef->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);
        return $spreadsheet;
    }

    /**
     * Import anggota Seni Budaya dari spreadsheet Excel
     */
    public function importAnggota(string $filePath, int $semesterId): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestDataRow();

        $successCount = 0;
        $updatedCount = 0;
        $failedRows = [];

        // Cache daftar cabang seni budaya aktif
        $cabangs = SeniBudaya::where('is_active', true)->get();
        $cabangMap = [];
        foreach ($cabangs as $c) {
            $cabangMap[strtolower(trim($c->nama))] = $c;
            $cabangMap[strtolower(trim($c->kode))] = $c;
        }

        for ($row = 5; $row <= $highestRow; $row++) {
            $nisn = trim((string) $sheet->getCell('B' . $row)->getValue());
            $nipd = trim((string) $sheet->getCell('C' . $row)->getValue());
            $namaSiswa = trim((string) $sheet->getCell('D' . $row)->getValue());
            $namaCabang = trim((string) $sheet->getCell('F' . $row)->getValue());

            if (empty($namaCabang)) {
                continue; // Kolom cabang kosong dilewati
            }

            // Cari Siswa
            $siswa = null;
            if (!empty($nisn)) {
                $siswa = Siswa::where('nisn', $nisn)->first();
            }
            if (!$siswa && !empty($nipd)) {
                $siswa = Siswa::where('nipd', $nipd)->first();
            }
            if (!$siswa && !empty($namaSiswa)) {
                $siswa = Siswa::where('nama', 'like', $namaSiswa)->first();
            }

            if (!$siswa) {
                $failedRows[] = "Baris {$row}: Siswa '{$namaSiswa}' tidak ditemukan di database.";
                continue;
            }

            // Cari Cabang Seni Budaya
            $cleanCabang = strtolower($namaCabang);
            $cabang = $cabangMap[$cleanCabang] ?? null;

            if (!$cabang) {
                // Coba pencarian fleksibel
                foreach ($cabangs as $c) {
                    if (str_contains(strtolower($c->nama), $cleanCabang) || str_contains($cleanCabang, strtolower($c->nama))) {
                        $cabang = $c;
                        break;
                    }
                }
            }

            if (!$cabang) {
                $failedRows[] = "Baris {$row}: Cabang Seni Budaya '{$namaCabang}' tidak ditemukan dalam master data.";
                continue;
            }

            // Daftarkan siswa
            $res = $this->seniBudayaService->enrollStudent($siswa->id, $cabang->id, $semesterId);
            if ($res['success']) {
                if ($res['is_new']) {
                    $successCount++;
                } else {
                    $updatedCount++;
                }
            }
        }

        return [
            'success_count' => $successCount,
            'updated_count' => $updatedCount,
            'failed_count' => count($failedRows),
            'failed_rows' => $failedRows,
        ];
    }

    /**
     * Ekspor Rekapitulasi Nilai & Kehadiran per Cabang Seni Budaya
     */
    public function exportRekap(int $seniBudayaId, int $semesterId): Spreadsheet
    {
        $cabang = SeniBudaya::with('guru')->findOrFail($seniBudayaId);
        $semester = Semester::with('tahunAjaran')->findOrFail($semesterId);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Nilai');

        // Header Title
        $sheet->setCellValue('A1', 'REKAPITULASI NILAI MATA PELAJARAN SENI BUDAYA');
        $sheet->setCellValue('A2', 'Cabang: ' . $cabang->nama . ' | Guru: ' . $cabang->guru_display_name);
        $sheet->setCellValue('A3', 'Tahun Ajaran: ' . ($semester->tahunAjaran?->nama ?? '-') . ' | Semester: ' . ucfirst($semester->semester));
        
        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        $sheet->mergeCells('A3:L3');

        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet->getStyle('A1')->setSize(13);

        // Header Tabel
        $headers = [
            'No', 'NISN', 'NIS', 'Nama Siswa', 'Kelas',
            'STS 1', 'SAS', 'STS 2', 'SAT',
            'Presensi (%)', 'Nilai Akhir', 'Predikat'
        ];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];

        foreach ($headers as $i => $h) {
            $sheet->setCellValue($cols[$i] . '5', $h);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4B5563']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A5:L5')->applyFromArray($headerStyle);
        $sheet->getRowDimension(5)->setRowHeight(24);

        $anggotas = SeniBudayaAnggota::with('siswa.rombel')
            ->where('seni_budaya_id', $seniBudayaId)
            ->where('semester_id', $semesterId)
            ->get()
            ->sortBy(fn($a) => $a->siswa?->nama);

        $row = 6;
        $no = 1;
        foreach ($anggotas as $a) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValueExplicit('B' . $row, (string) ($a->siswa?->nisn ?? '-'), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $row, (string) ($a->siswa?->nipd ?? '-'), DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, $a->siswa?->nama ?? '-');
            $sheet->setCellValue('E' . $row, $a->siswa?->rombel?->nama ?? '-');
            $sheet->setCellValue('F' . $row, $a->sts_1 ?? '-');
            $sheet->setCellValue('G' . $row, $a->sas ?? '-');
            $sheet->setCellValue('H' . $row, $a->sts_2 ?? '-');
            $sheet->setCellValue('I' . $row, $a->sat ?? '-');
            $sheet->setCellValue('J' . $row, $a->persentase_kehadiran ?? 0);
            $sheet->setCellValue('K' . $row, $a->nilai_akhir ?? '-');
            $sheet->setCellValue('L' . $row, $a->nilai_huruf ?? '-');

            $row++;
        }

        $lastRow = max(6, $row - 1);
        $sheet->getStyle('A6:L' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ]);

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
