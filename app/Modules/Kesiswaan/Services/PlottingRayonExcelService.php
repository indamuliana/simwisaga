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
        $spreadsheet = new Spreadsheet;
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
        $spreadsheet = new Spreadsheet;
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
            $sheet->setCellValueExplicit('A'.$row, (string) $nis, DataType::TYPE_STRING);
            $sheet->setCellValue('B'.$row, $s->nama);
            $sheet->setCellValue('C'.$row, $s->rombel?->nama ?? '-');
            $sheet->setCellValue('D'.$row, $s->kecamatan ?? '-');
            $sheet->setCellValue('E'.$row, $s->kelurahan ?? '-');
            $sheet->setCellValue('F'.$row, $s->rayon?->nama ?? 'Belum Ada');
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
            $rawNis = trim((string) $sheet->getCell('A'.$r)->getValue());

            if ($rawNis === '') {
                continue;
            }

            // Sanitasi NIS: hilangkan tanda kutip tunggal jika ada
            $cleanNis = ltrim($rawNis, "'");

            if (! in_array($cleanNis, $nisList)) {
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
        $foundNipd = $siswas->pluck('nipd')->map(fn ($v) => (string) $v)->toArray();

        // Cari NIS mana saja yang tidak ditemukan di database
        $unmatchedNis = array_values(array_filter($nisList, fn ($nis) => ! in_array((string) $nis, $foundNipd)));

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
            'mode' => 'single',
            'total_nis' => count($nisList),
            'updated_count' => $siswas->count(),
            'unmatched_nis' => $unmatchedNis,
            'rayon_nama' => $rayon->nama,
        ];
    }

    /**
     * Membuat berkas template untuk import plotting Multi-Rayon.
     */
    public function generateTemplateMultiRayon(string $type = 'kosong', ?Collection $siswas = null): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Multi Rayon');

        if ($type === 'prefill' && $siswas) {
            // Header Prefill
            $sheet->setCellValue('A1', 'NIS');
            $sheet->setCellValue('B1', 'Kode / Nama Rayon');
            $sheet->setCellValue('C1', 'Nama Siswa');
            $sheet->setCellValue('D1', 'Rombel');
            $sheet->setCellValue('E1', 'Kecamatan');
            $sheet->setCellValue('F1', 'Kelurahan');

            $this->applyHeaderStyle($sheet, 'A1:F1');

            $row = 2;
            foreach ($siswas as $s) {
                $nis = $s->nipd ?: $s->nisn;
                $sheet->setCellValueExplicit('A'.$row, (string) $nis, DataType::TYPE_STRING);
                $sheet->setCellValue('B'.$row, $s->rayon?->nama ?? '');
                $sheet->setCellValue('C'.$row, $s->nama);
                $sheet->setCellValue('D'.$row, $s->rombel?->nama ?? '-');
                $sheet->setCellValue('E'.$row, $s->kecamatan ?? '-');
                $sheet->setCellValue('F'.$row, $s->kelurahan ?? '-');
                $row++;
            }

            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        } else {
            // Header Template Kosong
            $sheet->setCellValue('A1', 'NIS');
            $sheet->setCellValue('B1', 'Kode / Nama Rayon');
            $sheet->setCellValue('C1', 'Nama Siswa (Opsional)');

            // Contoh Data
            $sampleRayon1 = Rayon::first()?->nama ?? 'Alik 1';
            $sampleRayon2 = Rayon::skip(1)->first()?->nama ?? 'Bale 1';

            $sheet->setCellValueExplicit('A2', '12208945', DataType::TYPE_STRING);
            $sheet->setCellValue('B2', $sampleRayon1);
            $sheet->setCellValue('C2', 'Ahmad Pratama (Hapus baris contoh ini sebelum upload)');

            $sheet->setCellValueExplicit('A3', '12208946', DataType::TYPE_STRING);
            $sheet->setCellValue('B3', $sampleRayon2);
            $sheet->setCellValue('C3', 'Budi Santoso (Hapus baris contoh ini sebelum upload)');

            $this->applyHeaderStyle($sheet, 'A1:C1');

            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(25);
            $sheet->getColumnDimension('C')->setWidth(45);
        }

        // Sheet 2: Daftar Referensi Rayon Resmi yang Terdaftar
        $sheetRayon = $spreadsheet->createSheet();
        $sheetRayon->setTitle('Daftar Rayon Tersedia');
        $sheetRayon->setCellValue('A1', 'Kode / Nama Rayon');
        $sheetRayon->setCellValue('B1', 'Wilayah Cakupan');
        $sheetRayon->setCellValue('C1', 'Pembimbing Rayon');
        $this->applyHeaderStyle($sheetRayon, 'A1:C1');

        $rayons = Rayon::with('pembimbing')->orderBy('nama')->get();
        $rRow = 2;
        foreach ($rayons as $ray) {
            $sheetRayon->setCellValue('A'.$rRow, $ray->nama);
            $sheetRayon->setCellValue('B'.$rRow, $ray->wilayah_cakupan ?? '-');
            $sheetRayon->setCellValue('C'.$rRow, $ray->pembimbing?->name ?? '-');
            $rRow++;
        }
        foreach (range('A', 'C') as $col) {
            $sheetRayon->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    /**
     * Membaca file Excel dan mengalokasikan siswa ke masing-masing Rayon sesuai Kolom B.
     */
    public function importAndPlotMultiRayon(string $filePath): array
    {
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $highestRow = $sheet->getHighestRow();

        // 1. Preload master Rayon ke dictionary map (case-insensitive & whitespace trimmed)
        $rayons = Rayon::all();
        $rayonMap = [];
        foreach ($rayons as $ray) {
            $norm = strtolower(preg_replace('/\s+/', ' ', trim($ray->nama)));
            $rayonMap[$norm] = $ray;
        }

        // 2. Baca seluruh baris data dari Excel
        $rowsData = [];
        $nisList = [];

        for ($r = 2; $r <= $highestRow; $r++) {
            $rawNis = trim((string) $sheet->getCell('A'.$r)->getValue());
            $rawRayon = trim((string) $sheet->getCell('B'.$r)->getValue());
            $rawNama = trim((string) $sheet->getCell('C'.$r)->getValue());

            // Lewati jika seluruh kolom di baris ini kosong
            if ($rawNis === '' && $rawRayon === '') {
                continue;
            }

            $cleanNis = ltrim($rawNis, "'");
            $rowsData[] = [
                'row_number' => $r,
                'nis' => $cleanNis,
                'rayon' => $rawRayon,
                'nama' => $rawNama,
            ];

            if ($cleanNis !== '' && ! in_array($cleanNis, $nisList, true)) {
                $nisList[] = $cleanNis;
            }
        }

        if (empty($rowsData)) {
            return [
                'mode' => 'multi',
                'total_rows' => 0,
                'updated_count' => 0,
                'per_rayon_counts' => [],
                'unmatched_nis' => [],
                'invalid_rayons' => [],
            ];
        }

        // 3. Preload Siswa berdasarkan NIS (NIPD atau NISN)
        $siswas = Siswa::whereIn('nipd', $nisList)
            ->orWhereIn('nisn', $nisList)
            ->get();

        $siswaMap = [];
        foreach ($siswas as $siswa) {
            if ($siswa->nipd) {
                $siswaMap[(string) $siswa->nipd] = $siswa;
            }
            if ($siswa->nisn) {
                $siswaMap[(string) $siswa->nisn] = $siswa;
            }
        }

        // 4. Validasi per baris
        $matchedUpdates = [];
        $unmatchedNis = [];
        $invalidRayons = [];

        foreach ($rowsData as $item) {
            $nis = $item['nis'];
            $rayonRaw = $item['rayon'];

            if ($nis === '') {
                continue;
            }

            if ($rayonRaw === '') {
                $invalidRayons[] = [
                    'nis' => $nis,
                    'rayon' => '(Kosong)',
                    'alasan' => 'Kolom Rayon kosong',
                ];
                continue;
            }

            $normRayon = strtolower(preg_replace('/\s+/', ' ', $rayonRaw));
            if (! isset($rayonMap[$normRayon])) {
                $invalidRayons[] = [
                    'nis' => $nis,
                    'rayon' => $rayonRaw,
                    'alasan' => 'Rayon tidak terdaftar di sistem',
                ];
                continue;
            }

            if (! isset($siswaMap[$nis])) {
                if (! in_array($nis, $unmatchedNis, true)) {
                    $unmatchedNis[] = $nis;
                }
                continue;
            }

            $matchedUpdates[] = [
                'siswa' => $siswaMap[$nis],
                'rayon' => $rayonMap[$normRayon],
            ];
        }

        // 5. Eksekusi update database dalam transaksi
        $perRayonCounts = [];
        $updatedSiswaIds = [];

        if (! empty($matchedUpdates)) {
            DB::transaction(function () use ($matchedUpdates, &$perRayonCounts, &$updatedSiswaIds) {
                foreach ($matchedUpdates as $m) {
                    $siswa = $m['siswa'];
                    $rayon = $m['rayon'];

                    // Update hanya jika rayon berubah
                    if ($siswa->rayon_id !== $rayon->id) {
                        $siswa->update(['rayon_id' => $rayon->id]);
                        RayonSiswa::create([
                            'siswa_id' => $siswa->id,
                            'rayon_id' => $rayon->id,
                            'tgl_mulai' => now(),
                            'alasan_pindah' => 'Import Multi-Rayon Excel',
                        ]);
                    }

                    $perRayonCounts[$rayon->nama] = ($perRayonCounts[$rayon->nama] ?? 0) + 1;
                    $updatedSiswaIds[$siswa->id] = true;
                }
            });
        }

        return [
            'mode' => 'multi',
            'total_rows' => count($rowsData),
            'updated_count' => count($updatedSiswaIds),
            'per_rayon_counts' => $perRayonCounts,
            'unmatched_nis' => $unmatchedNis,
            'invalid_rayons' => $invalidRayons,
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
