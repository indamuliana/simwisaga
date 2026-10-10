<?php

namespace App\Modules\Kesiswaan\Services;

use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kesiswaan\Models\RombelSiswa;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PlottingRombelExcelService
{
    /**
     * Membuat berkas template kosong untuk import plotting rombel.
     */
    public function generateTemplateKosong(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Plotting Rombel');

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
        $sheet->getColumnDimension('B')->setWidth(48);

        return $spreadsheet;
    }

    /**
     * Membuat template yang sudah terisi otomatis (pre-filled) dengan daftar siswa saat ini.
     */
    public function generateTemplatePrefill(Collection $siswas): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Siswa Plotting Rombel');

        // Header
        $sheet->setCellValue('A1', 'NIS');
        $sheet->setCellValue('B1', 'Nama Siswa');
        $sheet->setCellValue('C1', 'JK');
        $sheet->setCellValue('D1', 'Rombel Saat Ini');
        $sheet->setCellValue('E1', 'Rayon');
        $sheet->setCellValue('F1', 'Program');

        $this->applyHeaderStyle($sheet, 'A1:F1');

        $row = 2;
        foreach ($siswas as $s) {
            $nis = $s->nipd ?: $s->nisn;
            $sheet->setCellValueExplicit('A' . $row, (string) $nis, DataType::TYPE_STRING);
            $sheet->setCellValue('B' . $row, $s->nama);
            $sheet->setCellValue('C' . $row, $s->jk === 'L' ? 'L' : 'P');
            $sheet->setCellValue('D' . $row, $s->rombel?->nama ?? 'Belum Ada');
            $sheet->setCellValue('E' . $row, $s->rayon?->nama ?? '-');
            $sheet->setCellValue('F' . $row, $s->program?->nama ?? 'Reguler');
            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Membaca berkas Excel dan mengalokasikan siswa berdasarkan NIS ke Rombel tujuan.
     */
    public function importAndPlot(string $filePath, int $targetRombelId): array
    {
        $rombel = Rombel::findOrFail($targetRombelId);
        $ta = AcademicContext::currentTahunAjaran();

        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $highestRow = $sheet->getHighestRow();
        $nisList = [];

        // Loop mulai baris 2 (abaikan baris header)
        for ($r = 2; $r <= $highestRow; $r++) {
            $rawNis = trim((string) $sheet->getCell('A' . $r)->getValue());

            if ($rawNis === '') {
                continue;
            }

            // Sanitasi NIS: hilangkan tanda kutip jika diekspor sebagai text
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
                'rombel_nama' => $rombel->nama,
                'kapasitas' => $rombel->kapasitas,
                'total_terisi' => $rombel->siswas()->count(),
                'is_over_capacity' => false,
            ];
        }

        // Cari siswa berdasarkan NIS (NIPD sekolah) atau NISN
        $siswas = Siswa::whereIn('nipd', $nisList)
            ->orWhere(function ($q) use ($nisList) {
                $q->whereIn('nisn', $nisList);
            })
            ->get();

        $foundIdentifiers = [];
        foreach ($siswas as $s) {
            if ($s->nipd) $foundIdentifiers[] = (string) $s->nipd;
            if ($s->nisn) $foundIdentifiers[] = (string) $s->nisn;
        }

        // Cari NIS/NISN mana saja yang tidak ditemukan di sistem
        $unmatchedNis = array_values(array_filter($nisList, fn($nis) => !in_array((string) $nis, $foundIdentifiers)));

        // Update database dan catat riwayat rombel_siswa
        if ($siswas->isNotEmpty()) {
            DB::transaction(function () use ($siswas, $rombel, $ta) {
                foreach ($siswas as $siswa) {
                    $siswa->update(['rombel_id' => $rombel->id]);

                    if ($ta) {
                        RombelSiswa::updateOrCreate(
                            ['tahun_ajaran_id' => $ta->id, 'siswa_id' => $siswa->id],
                            ['rombel_id' => $rombel->id]
                        );
                    }
                }
            });
        }

        $totalTerisi = $rombel->siswas()->count();
        $isOverCapacity = $rombel->kapasitas && $totalTerisi > $rombel->kapasitas;

        return [
            'total_nis' => count($nisList),
            'updated_count' => $siswas->count(),
            'unmatched_nis' => $unmatchedNis,
            'rombel_nama' => $rombel->nama,
            'kapasitas' => $rombel->kapasitas,
            'total_terisi' => $totalTerisi,
            'is_over_capacity' => $isOverCapacity,
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
