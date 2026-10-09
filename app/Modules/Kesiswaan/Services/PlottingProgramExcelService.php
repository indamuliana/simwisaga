<?php

namespace App\Modules\Kesiswaan\Services;

use App\Modules\Kesiswaan\Models\Program;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PlottingProgramExcelService
{
    /**
     * Membuat berkas template kosong untuk import plotting program.
     */
    public function generateTemplateKosong(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Plotting Program');

        // Header
        $sheet->setCellValue('A1', 'NIS');
        $sheet->setCellValue('B1', 'Nama Siswa (Opsional)');

        // Contoh Data
        $sheet->setCellValueExplicit('A2', '12208945', DataType::TYPE_STRING);
        $sheet->setCellValue('B2', 'Ahmad Pratama (Hapus baris contoh ini sebelum upload)');

        $sheet->setCellValueExplicit('A3', '12208946', DataType::TYPE_STRING);
        $sheet->setCellValue('B3', 'Budi Santoso (Hapus baris contoh ini sebelum upload)');

        $this->applyHeaderStyle($sheet, 'A1:B1');

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(45);

        return $spreadsheet;
    }

    /**
     * Membuat template yang sudah terisi otomatis (pre-filled) dengan daftar siswa.
     */
    public function generateTemplatePrefill(Collection $siswas): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Siswa Plotting');

        // Header
        $sheet->setCellValue('A1', 'NIS');
        $sheet->setCellValue('B1', 'Nama Siswa');
        $sheet->setCellValue('C1', 'Rombel');
        $sheet->setCellValue('D1', 'Rayon');
        $sheet->setCellValue('E1', 'Program Saat Ini');

        $this->applyHeaderStyle($sheet, 'A1:E1');

        $row = 2;
        foreach ($siswas as $s) {
            $nis = $s->nipd ?: $s->nisn;
            $sheet->setCellValueExplicit('A' . $row, (string) $nis, DataType::TYPE_STRING);
            $sheet->setCellValue('B' . $row, $s->nama);
            $sheet->setCellValue('C' . $row, $s->rombel?->nama ?? '-');
            $sheet->setCellValue('D' . $row, $s->rayon?->nama ?? '-');
            $sheet->setCellValue('E' . $row, $s->program?->nama ?? 'Reguler');
            $row++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Membaca file Excel dan mengalokasikan siswa berdasarkan NIS ke program tujuan.
     */
    public function importAndPlot(string $filePath, int $targetProgramId): array
    {
        $program = Program::findOrFail($targetProgramId);

        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $highestRow = $sheet->getHighestRow();
        $nisList = [];

        // Loop mulai dari baris 2 (melewati baris header)
        for ($r = 2; $r <= $highestRow; $r++) {
            $rawNis = trim((string) $sheet->getCell('A' . $r)->getValue());

            if ($rawNis === '') {
                continue;
            }

            // Sanitasi NIS: hilangkan tanda kutip tunggal jika ada format string Excel
            $cleanNis = ltrim($rawNis, "'");

            if (!in_array($cleanNis, $nisList)) {
                $nisList[] = $cleanNis;
            }
        }

        if (empty($nisList)) {
            return [
                'total_nis' => 0,
                'updated_count' => 0,
                'unmatched_nis' => [],
                'program_nama' => $program->nama,
            ];
        }

        // Cari siswa berdasarkan NIS (NIPD sekolah secara ketat)
        $siswas = Siswa::whereIn('nipd', $nisList)->get();
        $foundIds = $siswas->pluck('id')->toArray();
        $foundNipd = $siswas->pluck('nipd')->map(fn($v) => (string) $v)->toArray();

        // Cari NIS mana saja yang tidak ditemukan
        $unmatchedNis = array_values(array_filter($nisList, fn($nis) => !in_array((string) $nis, $foundNipd)));

        // Update database jika ada siswa yang cocok
        if (!empty($foundIds)) {
            DB::transaction(function () use ($foundIds, $program) {
                Siswa::whereIn('id', $foundIds)->update(['program_id' => $program->id]);
            });
        }

        return [
            'total_nis' => count($nisList),
            'updated_count' => count($foundIds),
            'unmatched_nis' => $unmatchedNis,
            'program_nama' => $program->nama,
        ];
    }

    private function applyHeaderStyle($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '1e293b'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'e2e8f0'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '94a3b8'],
                ],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(25);
    }
}
