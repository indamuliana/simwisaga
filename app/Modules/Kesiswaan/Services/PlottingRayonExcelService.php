<?php

namespace App\Modules\Kesiswaan\Services;

use App\Modules\Akademik\Models\Rayon;
use App\Modules\Kesiswaan\Models\RayonSiswa;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PlottingRayonExcelService
{
    /**
     * Membuat berkas template kosong untuk import plotting rayon.
     */
    public function generateTemplateKosong(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Plotting Rayon');

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
        $sheet->setTitle('Daftar Siswa Plotting Rayon');

        // Header
        $sheet->setCellValue('A1', 'NIS');
        $sheet->setCellValue('B1', 'Nama Siswa');
        $sheet->setCellValue('C1', 'Rombel');
        $sheet->setCellValue('D1', 'Kecamatan');
        $sheet->setCellValue('E1', 'Kelurahan');
        $sheet->setCellValue('F1', 'Rayon Saat Ini');

        $this->applyHeaderStyle($sheet, 'A1:F1');

        $row = 2;
        foreach ($siswas as $s) {
            $nis = $s->nipd ?: $s->nisn;
            $sheet->setCellValueExplicit('A' . $row, (string) $nis, DataType::TYPE_STRING);
            $sheet->setCellValue('B' . $row, $s->nama);
            $sheet->setCellValue('C' . $row, $s->rombel?->nama ?? '-');
            $sheet->setCellValue('D' . $row, $s->kecamatan ?? '-');
            $sheet->setCellValue('E' . $row, $s->kelurahan ?? '-');
            $sheet->setCellValue('F' . $row, $s->rayon?->nama ?? 'Belum Ada');
            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Membaca file Excel dan mengalokasikan siswa berdasarkan NIS ke Rayon tujuan.
     */
    public function importAndPlot(string $filePath, int $targetRayonId): array
    {
        $rayon = Rayon::findOrFail($targetRayonId);

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

            // Sanitasi NIS: hilangkan tanda kutip tunggal jika ada
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
                'rayon_nama' => $rayon->nama,
            ];
        }

        // Cari siswa berdasarkan NIS (NIPD sekolah secara ketat)
        $siswas = Siswa::whereIn('nipd', $nisList)->get();
        $foundNipd = $siswas->pluck('nipd')->map(fn($v) => (string) $v)->toArray();

        // Cari NIS mana saja yang tidak ditemukan di database
        $unmatchedNis = array_values(array_filter($nisList, fn($nis) => !in_array((string) $nis, $foundNipd)));

        // Update database dan catat riwayat rayon
        if ($siswas->isNotEmpty()) {
            DB::transaction(function () use ($siswas, $rayon) {
                foreach ($siswas as $siswa) {
                    $siswa->update(['rayon_id' => $rayon->id]);
                    RayonSiswa::create([
                        'siswa_id' => $siswa->id,
                        'rayon_id' => $rayon->id,
                        'tgl_mulai' => now(),
                        'alasan_pindah' => 'Import Plotting Rayon Excel',
                    ]);
                }
            });
        }

        return [
            'total_nis' => count($nisList),
            'updated_count' => $siswas->count(),
            'unmatched_nis' => $unmatchedNis,
            'rayon_nama' => $rayon->nama,
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
