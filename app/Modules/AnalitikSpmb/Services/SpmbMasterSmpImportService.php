<?php

namespace App\Modules\AnalitikSpmb\Services;

use App\Modules\AnalitikSpmb\Models\SpmbMasterSmp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SpmbMasterSmpImportService
{
    /**
     * Import data Master SMP dari file CSV / Excel.
     *
     * @return array{total_read: int, inserted: int, updated: int, skipped: int, provinsi: string}
     */
    public function import(string $filePath, string $provinsi, string $extension = 'csv'): array
    {
        $provinsi = Str::title(trim($provinsi));
        $extension = strtolower($extension);

        if (in_array($extension, ['xlsx', 'xls'])) {
            $rows = $this->readExcel($filePath);
        } else {
            $rows = $this->readCsv($filePath);
        }

        $totalRead = 0;
        $inserted = 0;
        $updated = 0;
        $skipped = 0;

        $batch = [];
        $batchNpsns = [];
        $batchSize = 500;
        $now = now()->toDateTimeString();

        foreach ($rows as $item) {
            $totalRead++;

            $npsn = trim((string) ($item['npsn'] ?? ''));
            $nama = trim((string) ($item['nama'] ?? ''));

            if (empty($npsn) || empty($nama)) {
                $skipped++;
                continue;
            }

            $bentuk = strtoupper(trim((string) ($item['bentuk'] ?? 'SMP')));
            $statusRaw = strtoupper(trim((string) ($item['status'] ?? 'SWASTA')));
            $status = str_contains($statusRaw, 'NEG') ? 'Negeri' : 'Swasta';
            $jenjang = Str::title(trim((string) ($item['jenjang'] ?? 'Pendidikan Dasar')));
            $kabupaten = $this->normalizeKabupaten((string) ($item['kabupaten'] ?? ''));
            $kecamatan = $this->normalizeKecamatan((string) ($item['kecamatan'] ?? ''));
            $kelurahan = Str::title(trim((string) ($item['kelurahan'] ?? '')));
            $alamat = trim((string) ($item['alamat'] ?? ''));

            $batch[] = [
                'npsn' => $npsn,
                'nama' => $nama,
                'bentuk' => $bentuk,
                'status' => $status,
                'jenjang' => $jenjang ?: 'Pendidikan Dasar',
                'provinsi' => $provinsi,
                'kabupaten' => $kabupaten,
                'kecamatan' => $kecamatan,
                'kelurahan' => $kelurahan ?: null,
                'alamat' => $alamat ?: null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $batchNpsns[] = $npsn;

            if (count($batch) >= $batchSize) {
                $res = $this->processBatch($batch, $batchNpsns);
                $inserted += $res['inserted'];
                $updated += $res['updated'];

                $batch = [];
                $batchNpsns = [];
            }
        }

        if (! empty($batch)) {
            $res = $this->processBatch($batch, $batchNpsns);
            $inserted += $res['inserted'];
            $updated += $res['updated'];
        }

        return [
            'total_read' => $totalRead,
            'inserted' => $inserted,
            'updated' => $updated,
            'skipped' => $skipped,
            'provinsi' => $provinsi,
        ];
    }

    /**
     * Eksekusi batch upsert dan hitung jumlah insert vs update
     */
    protected function processBatch(array $batch, array $batchNpsns): array
    {
        $existing = SpmbMasterSmp::whereIn('npsn', $batchNpsns)->pluck('npsn')->flip()->toArray();
        $updatedCount = 0;
        $insertedCount = 0;

        foreach ($batchNpsns as $n) {
            if (isset($existing[$n])) {
                $updatedCount++;
            } else {
                $insertedCount++;
            }
        }

        SpmbMasterSmp::upsert(
            $batch,
            ['npsn'],
            ['nama', 'bentuk', 'status', 'jenjang', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan', 'alamat', 'updated_at']
        );

        return [
            'inserted' => $insertedCount,
            'updated' => $updatedCount,
        ];
    }

    /**
     * Membaca file CSV
     */
    public function readCsv(string $filePath): \Generator
    {
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return;
        }

        // Baca header
        $firstLine = fgets($handle);
        if (! $firstLine) {
            fclose($handle);
            return;
        }

        // Deteksi delimiter (koma atau titik koma)
        $delimiter = str_contains($firstLine, ';') ? ';' : ',';
        $headers = str_getcsv(trim($firstLine), $delimiter);
        $headerMap = $this->mapHeaders($headers);

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }
            yield $this->combineRow($headerMap, $row);
        }

        fclose($handle);
    }

    /**
     * Membaca file Excel (.xlsx / .xls)
     */
    public function readExcel(string $filePath): \Generator
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (empty($rows)) {
            return;
        }

        $headerRow = array_shift($rows);
        $headerMap = $this->mapHeaders(array_values($headerRow));

        foreach ($rows as $row) {
            $rowValues = array_values($row);
            if (empty(array_filter($rowValues))) {
                continue;
            }
            yield $this->combineRow($headerMap, $rowValues);
        }
    }

    /**
     * Petakan indeks kolom berdasarkan nama header
     */
    protected function mapHeaders(array $headers): array
    {
        $map = [];
        foreach ($headers as $idx => $rawHeader) {
            $cleaned = strtolower(trim((string) $rawHeader));
            $cleaned = str_replace(['_', '-'], ' ', $cleaned);

            if (str_contains($cleaned, 'npsn')) {
                $map['npsn'] = $idx;
            } elseif (str_contains($cleaned, 'nama')) {
                $map['nama'] = $idx;
            } elseif (str_contains($cleaned, 'bentuk')) {
                $map['bentuk'] = $idx;
            } elseif (str_contains($cleaned, 'status')) {
                $map['status'] = $idx;
            } elseif (str_contains($cleaned, 'jenjang')) {
                $map['jenjang'] = $idx;
            } elseif (str_contains($cleaned, 'kabupaten') || str_contains($cleaned, 'kota') || $cleaned === 'kab') {
                $map['kabupaten'] = $idx;
            } elseif (str_contains($cleaned, 'kecamatan') || $cleaned === 'kec') {
                $map['kecamatan'] = $idx;
            } elseif (str_contains($cleaned, 'kelurahan') || str_contains($cleaned, 'desa')) {
                $map['kelurahan'] = $idx;
            } elseif (str_contains($cleaned, 'alamat')) {
                $map['alamat'] = $idx;
            }
        }

        return $map;
    }

    protected function combineRow(array $headerMap, array $row): array
    {
        $data = [];
        foreach ($headerMap as $key => $colIdx) {
            $data[$key] = $row[$colIdx] ?? '';
        }
        return $data;
    }

    /**
     * Normalisasi nama Kabupaten/Kota: pertahankan 'Kab.' dan 'Kota'
     */
    public function normalizeKabupaten(string $val): string
    {
        $val = trim($val);
        if (preg_match('/^kab\.?\s+(.*)$/i', $val, $m)) {
            return 'Kab. ' . Str::title(strtolower(trim($m[1])));
        }
        if (preg_match('/^kota\s+(.*)$/i', $val, $m)) {
            return 'Kota ' . Str::title(strtolower(trim($m[1])));
        }
        return Str::title(strtolower($val));
    }

    /**
     * Normalisasi nama Kecamatan: hapus prefix 'KEC.'
     */
    public function normalizeKecamatan(string $val): string
    {
        $val = trim($val);
        if (preg_match('/^kec\.?\s+(.*)$/i', $val, $m)) {
            $val = trim($m[1]);
        }
        return Str::title(strtolower($val));
    }

    /**
     * Generate contoh CSV Template
     */
    public function generateTemplateCsv(): string
    {
        $lines = [
            'NPSN,Nama,Bentuk,Status,Jenjang,Kabupaten,Kecamatan,Kelurahan,Alamat',
            '20270258,SMP MANGGALA LELES,SMP,SWASTA,PENDIDIKAN DASAR,KAB. GARUT,KEC. LELES,SUKARAME,LELES',
            '20209911,SMPN 1 GARUT,SMP,NEGERI,PENDIDIKAN DASAR,KAB. GARUT,KEC. GARUT KOTA,KOTA KULON,JL. AHMAD YANI NO. 43',
            '20220385,SMP NEGERI 1 BOGOR,SMP,NEGERI,PENDIDIKAN DASAR,KOTA BOGOR,KEC. KOTA BOGOR TENGAH,PALEDANG,JL. IR. H. DJUANDA BOGOR NO 16',
        ];

        return implode("\n", $lines) . "\n";
    }

    /**
     * Generate contoh Excel Spreadsheet Template
     */
    public function generateTemplateSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Master SMP');

        $headers = ['NPSN', 'Nama', 'Bentuk', 'Status', 'Jenjang', 'Kabupaten', 'Kecamatan', 'Kelurahan', 'Alamat'];
        $sheet->fromArray([$headers], null, 'A1');

        $samples = [
            ['20270258', 'SMP MANGGALA LELES', 'SMP', 'SWASTA', 'PENDIDIKAN DASAR', 'KAB. GARUT', 'KEC. LELES', 'SUKARAME', 'LELES'],
            ['20209911', 'SMPN 1 GARUT', 'SMP', 'NEGERI', 'PENDIDIKAN DASAR', 'KAB. GARUT', 'KEC. GARUT KOTA', 'KOTA KULON', 'JL. AHMAD YANI NO. 43'],
            ['20220385', 'SMP NEGERI 1 BOGOR', 'SMP', 'NEGERI', 'PENDIDIKAN DASAR', 'KOTA BOGOR', 'KEC. KOTA BOGOR TENGAH', 'PALEDANG', 'JL. IR. H. DJUANDA BOGOR NO 16'],
        ];
        $sheet->fromArray($samples, null, 'A2');

        // Style header bold
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
