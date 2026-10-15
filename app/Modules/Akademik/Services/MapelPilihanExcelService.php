<?php

namespace App\Modules\Akademik\Services;

use App\Modules\Akademik\Models\MapelPilihan;
use App\Modules\Akademik\Models\MapelPilihanAnggota;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MapelPilihanExcelService
{
    /**
     * Download template Excel untuk import anggota Mapil
     */
    public function downloadTemplate(MapelPilihan $mapil): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Mapil');

        // Header Informasi
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT ANGGOTA MAPEL PILIHAN: ' . strtoupper($mapil->nama));
        $sheet->setCellValue('A2', 'Kode Mapil: ' . $mapil->kode . ' | Tingkat: ' . $mapil->tingkat_label);
        $sheet->setCellValue('A3', 'PETUNJUK:');
        $sheet->setCellValue('A4', '1. Kolom B wajib diisi dengan NISN atau NIPD / NIS siswa.');
        $sheet->setCellValue('A5', '2. Kolom Nama Siswa & Rombel hanya referensi pembaca.');
        $sheet->setCellValue('A6', '3. Jangan mengubah urutan baris judul kolom di baris ke-8.');

        $sheet->getStyle('A1:A6')->getFont()->setSize(9)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748B'));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E293B'));

        // Header Kolom di baris 8
        $headers = [
            'A8' => 'No',
            'B8' => 'NISN / NIS (*Wajib)',
            'C8' => 'Nama Siswa (Referensi)',
            'D8' => 'Kelas / Rombel (Referensi)',
            'E8' => 'Status (aktif/nonaktif)',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A8:E8')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A8:E8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4F46E5');
        $sheet->getStyle('A8:E8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Contoh baris
        $sheet->setCellValue('A9', 1);
        $sheet->setCellValueExplicit('B9', '12345678', DataType::TYPE_STRING);
        $sheet->setCellValue('C9', 'Contoh Nama Siswa');
        $sheet->setCellValue('D9', 'XI PPLG 1');
        $sheet->setCellValue('E9', 'aktif');

        $sheet->getStyle('A9:E9')->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748B'));

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Export daftar anggota siswa Mapil ke Excel
     */
    public function exportMembers(MapelPilihan $mapil): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Anggota Mapil');

        $sheet->setCellValue('A1', 'DAFTAR ANGGOTA MAPEL PILIHAN: ' . strtoupper($mapil->nama));
        $sheet->setCellValue('A2', 'Kode: ' . $mapil->kode . ' | Guru Pengampu: ' . $mapil->guru_display_name . ' | Tingkat: ' . $mapil->tingkat_label);
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);

        $headers = [
            'A4' => 'No',
            'B4' => 'NISN',
            'C4' => 'NIS / NIPD',
            'D4' => 'Nama Siswa',
            'E4' => 'Kelas / Rombel',
            'F4' => 'Rayon',
            'G4' => 'Status Keanggotaan',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A4:G4')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A4:G4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4F46E5');

        $anggotas = $mapil->anggotas()->with(['siswa.rombel', 'siswa.rayon'])->get();
        $row = 5;
        foreach ($anggotas as $idx => $anggota) {
            $siswa = $anggota->siswa;
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValueExplicit('B' . $row, (string) ($siswa->nisn ?? '-'), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $row, (string) ($siswa->nipd ?? '-'), DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, $siswa->nama ?? '-');
            $sheet->setCellValue('E' . $row, $siswa->rombel->nama ?? '-');
            $sheet->setCellValue('F' . $row, $siswa->rayon->nama ?? '-');
            $sheet->setCellValue('G' . $row, ucfirst($anggota->status ?? 'aktif'));
            $row++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Import anggota siswa dari file Excel
     */
    public function importMembers(MapelPilihan $mapil, UploadedFile $file): array
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        $successCount = 0;
        $skippedCount = 0;
        $errors = [];

        // Data dimulai dari baris ke-9 (baris 8 adalah header)
        for ($row = 9; $row <= $highestRow; $row++) {
            $identifier = trim((string) $sheet->getCell('B' . $row)->getValue());
            $status = strtolower(trim((string) $sheet->getCell('E' . $row)->getValue())) ?: 'aktif';

            if (empty($identifier)) {
                continue;
            }

            // Cari siswa berdasarkan NISN atau NIPD
            $siswa = Siswa::where('nisn', $identifier)
                ->orWhere('nipd', $identifier)
                ->first();

            if (! $siswa) {
                $errors[] = "Baris {$row}: Siswa dengan identitas '{$identifier}' tidak ditemukan di sistem.";
                $skippedCount++;
                continue;
            }

            // Tautkan siswa ke MapelPilihan jika belum ada
            $existing = MapelPilihanAnggota::where('mapel_pilihan_id', $mapil->id)
                ->where('siswa_id', $siswa->id)
                ->first();

            if ($existing) {
                $existing->update(['status' => $status]);
                $skippedCount++;
            } else {
                MapelPilihanAnggota::create([
                    'mapel_pilihan_id' => $mapil->id,
                    'siswa_id' => $siswa->id,
                    'tahun_ajaran_id' => $mapil->tahun_ajaran_id,
                    'status' => $status,
                ]);
                $successCount++;
            }
        }

        return [
            'success' => $successCount,
            'skipped' => $skippedCount,
            'errors' => $errors,
        ];
    }
}
