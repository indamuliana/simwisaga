<?php

namespace App\Modules\BeasiswaOta\Services;

use App\Modules\BeasiswaOta\Models\Donatur;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Models\KelompokAnggota;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OtaExcelService
{
    /**
     * Export seluruh data donatur ke Spreadsheet.
     */
    public static function exportDonaturs(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Donatur');

        // Headers
        $headers = [
            'A1' => 'No',
            'B1' => 'Nama Donatur',
            'C1' => 'Tipe Donatur (rutin/insidental)',
            'D1' => 'Nomor Telepon / WA',
            'E1' => 'Email',
            'F1' => 'Alamat',
            'G1' => 'Kelompok Terafiliasi',
            'H1' => 'Total Donasi Disetor (Rp)',
            'I1' => 'Status',
            'J1' => 'Keterangan',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Header Styling
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $donaturs = Donatur::with(['kelompoks', 'donasiMasuks'])->orderBy('nama')->get();

        $row = 2;
        $no = 1;
        foreach ($donaturs as $d) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $d->nama);
            $sheet->setCellValue('C' . $row, ucfirst($d->tipe));
            $sheet->setCellValueExplicit('D' . $row, (string) ($d->nomor_telepon ?? ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('E' . $row, $d->email ?? '');
            $sheet->setCellValue('F' . $row, $d->alamat ?? '');
            $sheet->setCellValue('G' . $row, $d->kelompoks->pluck('nama_kelompok')->join(', '));
            $sheet->setCellValue('H' . $row, (float) $d->total_donasi);
            $sheet->setCellValue('I' . $row, $d->is_aktif ? 'Aktif' : 'Non-Aktif');
            $sheet->setCellValue('J' . $row, $d->keterangan ?? '');

            $row++;
        }

        $lastRow = $row - 1;
        if ($lastRow >= 2) {
            $sheet->getStyle("A1:J{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C2:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I2:I{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H2:H{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Download Template Import Donatur.
     */
    public static function templateDonaturs(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Donatur');

        // Headers
        $headers = [
            'A1' => 'Nama Donatur (*Wajib)',
            'B1' => 'Tipe (rutin / insidental)',
            'C1' => 'Nomor Telepon / WA',
            'D1' => 'Email',
            'E1' => 'Alamat',
            'F1' => 'Keterangan',
            'G1' => 'Nominal Komitmen Iuran (Opsional)',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Contoh baris petunjuk
        $samples = [
            ['Bapak H. Fulan', 'rutin', '081234567890', 'fulan@example.com', 'Garut', 'Orang tua asuh rutin', 150000],
            ['Ibu Siti Maryam', 'rutin', '081987654321', 'siti@example.com', 'Bandung', 'Donatur rutin kelompok', 200000],
            ['Hamba Allah', 'insidental', '085544332211', '', 'Jakarta', 'Donatur insidental bebas', ''],
        ];

        $r = 2;
        foreach ($samples as $s) {
            $sheet->setCellValue('A' . $r, $s[0]);
            $sheet->setCellValue('B' . $r, $s[1]);
            $sheet->setCellValueExplicit('C' . $r, (string) $s[2], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $r, $s[3]);
            $sheet->setCellValue('E' . $r, $s[4]);
            $sheet->setCellValue('F' . $r, $s[5]);
            if (!empty($s[6])) {
                $sheet->setCellValue('G' . $r, $s[6]);
            }
            $r++;
        }

        $sheet->getStyle("A1:G4")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("G2:G4")->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Import Data Donatur dari Excel.
     */
    public static function importDonaturs(
        string $filePath, 
        ?int $targetKelompokId = null, 
        float $defaultNominalKomitmen = 150000
    ): array {
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, true);

        $imported = 0;
        $updated = 0;
        $kelompokAssigned = 0;
        $kelompokSkipped = 0;

        $targetKelompok = $targetKelompokId ? Kelompok::find($targetKelompokId) : null;

        foreach ($rows as $index => $row) {
            if ($index === 1) {
                continue; // Skip header
            }

            $nama = trim((string) ($row['A'] ?? ''));
            if (empty($nama)) {
                continue;
            }

            $rawTipe = strtolower(trim((string) ($row['B'] ?? 'rutin')));
            // Jika ada target kelompok, tipe otomatis diset 'rutin'
            if ($targetKelompok) {
                $tipe = 'rutin';
            } else {
                $tipe = in_array($rawTipe, ['rutin', 'insidental']) ? $rawTipe : 'rutin';
            }

            $telepon = trim((string) ($row['C'] ?? ''));
            $email = trim((string) ($row['D'] ?? ''));
            $alamat = trim((string) ($row['E'] ?? ''));
            $keterangan = trim((string) ($row['F'] ?? ''));

            // Baca nominal komitmen dari kolom G (jika ada)
            $rawNominal = trim((string) ($row['G'] ?? ''));
            $nominalKomitmen = 0.0;
            if (!empty($rawNominal)) {
                $cleaned = preg_replace('/[.,]00$/', '', $rawNominal);
                $cleaned = preg_replace('/[^\d]/', '', $cleaned);
                $nominalKomitmen = (float) $cleaned;
            }
            if ($nominalKomitmen <= 0) {
                $nominalKomitmen = $defaultNominalKomitmen;
            }

            $existing = Donatur::where('nama', $nama)->first();

            if ($existing) {
                $existing->update([
                    'tipe' => $tipe,
                    'nomor_telepon' => $telepon ?: $existing->nomor_telepon,
                    'email' => $email ?: $existing->email,
                    'alamat' => $alamat ?: $existing->alamat,
                    'keterangan' => $keterangan ?: $existing->keterangan,
                ]);
                $donatur = $existing;
                $updated++;
            } else {
                $donatur = Donatur::create([
                    'nama' => $nama,
                    'tipe' => $tipe,
                    'nomor_telepon' => $telepon ?: null,
                    'email' => $email ?: null,
                    'alamat' => $alamat ?: null,
                    'keterangan' => $keterangan ?: null,
                    'is_aktif' => true,
                ]);
                $imported++;
            }

            // Jika ada target kelompok, daftarkan ke kelompok_anggotas jika belum ada
            if ($targetKelompok && $donatur) {
                $alreadyMember = KelompokAnggota::where('kelompok_id', $targetKelompok->id)
                    ->where('donatur_id', $donatur->id)
                    ->exists();

                if ($alreadyMember) {
                    $kelompokSkipped++;
                } else {
                    KelompokAnggota::create([
                        'kelompok_id' => $targetKelompok->id,
                        'donatur_id' => $donatur->id,
                        'nominal_komitmen' => $nominalKomitmen,
                        'keterangan' => 'Import Excel ' . now()->format('d/m/Y'),
                    ]);
                    $kelompokAssigned++;
                }
            }
        }

        return [
            'imported' => $imported,
            'updated' => $updated,
            'kelompok_assigned' => $kelompokAssigned,
            'kelompok_skipped' => $kelompokSkipped,
        ];
    }

    /**
     * Export Data Kelompok Donatur & Anggotanya.
     */
    public static function exportKelompoks(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kelompok & Anggota OTA');

        $headers = [
            'A1' => 'Kode Kelompok',
            'B1' => 'Nama Kelompok',
            'C1' => 'Target Nominal (Rp)',
            'D1' => 'Siswa Binaan',
            'E1' => 'Nama Donatur Anggota',
            'F1' => 'Tipe Donatur',
            'G1' => 'Nominal Komitmen Iuran (Rp)',
            'H1' => 'Keterangan Anggota',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4338CA'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $kelompoks = Kelompok::with(['anggotaKelompok.donatur', 'siswaPenerima.siswa.rombel'])
            ->orderBy('kode_kelompok')
            ->get();

        $row = 2;
        foreach ($kelompoks as $k) {
            $siswa = $k->siswaPenerima?->siswa;
            $siswaStr = $siswa ? "{$siswa->nama} ({$siswa->rombel?->nama})" : 'Belum Ditentukan';

            if ($k->anggotaKelompok->isEmpty()) {
                $sheet->setCellValue('A' . $row, $k->kode_kelompok);
                $sheet->setCellValue('B' . $row, $k->nama_kelompok);
                $sheet->setCellValue('C' . $row, (float) $k->target_nominal);
                $sheet->setCellValue('D' . $row, $siswaStr);
                $sheet->setCellValue('E' . $row, '(Belum Ada Donatur)');
                $sheet->setCellValue('F' . $row, '-');
                $sheet->setCellValue('G' . $row, 0);
                $sheet->setCellValue('H' . $row, $k->keterangan ?? '');
                $row++;
            } else {
                foreach ($k->anggotaKelompok as $ang) {
                    $sheet->setCellValue('A' . $row, $k->kode_kelompok);
                    $sheet->setCellValue('B' . $row, $k->nama_kelompok);
                    $sheet->setCellValue('C' . $row, (float) $k->target_nominal);
                    $sheet->setCellValue('D' . $row, $siswaStr);
                    $sheet->setCellValue('E' . $row, $ang->donatur->nama);
                    $sheet->setCellValue('F' . $row, ucfirst($ang->donatur->tipe));
                    $sheet->setCellValue('G' . $row, (float) $ang->nominal_komitmen);
                    $sheet->setCellValue('H' . $row, $ang->keterangan ?? '');
                    $row++;
                }
            }
        }

        $lastRow = $row - 1;
        if ($lastRow >= 2) {
            $sheet->getStyle("A1:H{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C2:C{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("G2:G{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Download Template Import Kelompok & Anggota Donatur.
     */
    public static function templateKelompoks(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Kelompok & Anggota');

        $headers = [
            'A1' => 'Kode Kelompok (*Wajib)',
            'B1' => 'Nama Kelompok (*Wajib)',
            'C1' => 'Target Nominal Kelompok (Rp)',
            'D1' => 'Nama Donatur Anggota (*Wajib)',
            'E1' => 'Nominal Komitmen Iuran (Rp)',
            'F1' => 'Keterangan Kelompok / Anggota',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D97706'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Contoh sample pengisian
        $samples = [
            ['KEL-01', 'Kelompok Asuh Peduli 1', 2000000, 'Bapak H. Ahmad', 150000, 'Iuran komitmen donatur'],
            ['KEL-01', 'Kelompok Asuh Peduli 1', 2000000, 'Ibu Siti Maryam', 150000, 'Iuran komitmen donatur'],
            ['KEL-02', 'Kelompok Asuh Peduli 2', 2000000, 'Bapak Ridwan', 200000, 'Iuran komitmen donatur'],
        ];

        $r = 2;
        foreach ($samples as $s) {
            $sheet->setCellValue('A' . $r, $s[0]);
            $sheet->setCellValue('B' . $r, $s[1]);
            $sheet->setCellValue('C' . $r, $s[2]);
            $sheet->setCellValue('D' . $r, $s[3]);
            $sheet->setCellValue('E' . $r, $s[4]);
            $sheet->setCellValue('F' . $r, $s[5]);
            $r++;
        }

        $sheet->getStyle("A1:F4")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("C2:C4")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("E2:E4")->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Import Data Kelompok & Anggota Donatur dari Excel.
     */
    public static function importKelompoks(string $filePath): array
    {
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, true);

        $kelompokCreated = 0;
        $kelompokUpdated = 0;
        $anggotaAdded = 0;

        foreach ($rows as $index => $row) {
            if ($index === 1) {
                continue; // Skip header
            }

            $kode = trim((string) ($row['A'] ?? ''));
            $namaKelompok = trim((string) ($row['B'] ?? ''));

            if (empty($kode) || empty($namaKelompok)) {
                continue;
            }

            $targetNominal = (float) ($row['C'] ?? 2000000);
            if ($targetNominal <= 0) {
                $targetNominal = 2000000.00;
            }

            $namaDonatur = trim((string) ($row['D'] ?? ''));
            $komitmen = (float) ($row['E'] ?? 150000);
            $keterangan = trim((string) ($row['F'] ?? ''));

            $kelompok = Kelompok::where('kode_kelompok', $kode)->first();
            if ($kelompok) {
                $kelompok->update([
                    'nama_kelompok' => $namaKelompok,
                    'target_nominal' => $targetNominal,
                ]);
                $kelompokUpdated++;
            } else {
                $kelompok = Kelompok::create([
                    'kode_kelompok' => $kode,
                    'nama_kelompok' => $namaKelompok,
                    'target_nominal' => $targetNominal,
                    'is_aktif' => true,
                    'keterangan' => $keterangan,
                ]);
                $kelompokCreated++;
            }

            // Jika ada nama donatur, cari atau buat donaturnya dan tautkan sebagai anggota
            if (!empty($namaDonatur)) {
                $donatur = Donatur::firstOrCreate(
                    ['nama' => $namaDonatur],
                    [
                        'tipe' => 'rutin',
                        'is_aktif' => true,
                    ]
                );

                KelompokAnggota::updateOrCreate(
                    [
                        'kelompok_id' => $kelompok->id,
                        'donatur_id' => $donatur->id,
                    ],
                    [
                        'nominal_komitmen' => $komitmen,
                        'keterangan' => $keterangan ?: 'Import Excel',
                    ]
                );
                $anggotaAdded++;
            }
        }

        return [
            'kelompok_created' => $kelompokCreated,
            'kelompok_updated' => $kelompokUpdated,
            'anggota_added' => $anggotaAdded,
        ];
    }

    /**
     * Export rekapitulasi monitoring donasi masuk per kelompok ke Spreadsheet.
     */
    public static function exportRekapMonitoringKelompok(?int $tahunAjaranId = null): Spreadsheet
    {
        $ta = $tahunAjaranId ? \App\Modules\Akademik\Models\TahunAjaran::find($tahunAjaranId) : \App\Modules\Akademik\Models\TahunAjaran::active()->first();
        $taName = $ta ? $ta->nama : 'Semua Periode';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Kelompok OTA');

        // Judul Laporan
        $sheet->setCellValue('A1', "REKAPITULASI MONITORING DONASI MASUK PER KELOMPOK (OTA)");
        $sheet->setCellValue('A2', "Tahun Ajaran: {$taName} | Dicetak: " . now()->translatedFormat('d F Y H:i'));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setSize(10)->setItalic(true);

        // Header Table
        $headers = [
            'A4' => 'No',
            'B4' => 'Kode',
            'C4' => 'Nama Kelompok',
            'D4' => 'Koordinator / PIC',
            'E4' => 'No. Telepon Koordinator',
            'F4' => 'Siswa Binaan',
            'G4' => 'Jumlah Donatur',
            'H4' => 'Komitmen Bulanan (Rp)',
            'I4' => 'Target Kebutuhan (Rp)',
            'J4' => 'Total Donasi Masuk (Rp)',
            'K4' => 'Saldo Kas Saat Ini (Rp)',
            'L4' => 'Setoran Terakhir',
            'M4' => 'Progres Capaian',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A4:M4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(28);

        $kelompoks = Kelompok::with([
            'anggotaKelompok.donatur',
            'siswaPenerima.siswa.rombel',
            'donasiMasuks' => function ($q) use ($tahunAjaranId) {
                if ($tahunAjaranId) {
                    $q->where('tahun_ajaran_id', $tahunAjaranId);
                }
            },
            'kasMutasis',
        ])->where('is_aktif', true)->orderBy('nama_kelompok')->get();

        $row = 5;
        $no = 1;
        $grandTotalKomitmen = 0;
        $grandTotalTarget = 0;
        $grandTotalMasuk = 0;
        $grandTotalSaldo = 0;

        foreach ($kelompoks as $k) {
            $target = (float) $k->target_nominal;
            $komitmen = (float) $k->total_komitmen;
            $totalMasuk = (float) $k->donasiMasuks->sum('nominal');
            $saldoKas = (float) $k->saldo_kas;
            $siswa = $k->siswaPenerima?->siswa;
            $siswaText = $siswa ? "{$siswa->nama} ({$siswa->rombel?->nama})" : 'Belum Dipetakan';

            $lastDonasi = $k->donasiMasuks()->latest('tanggal_donasi')->latest('id')->first();
            $lastDonasiText = $lastDonasi
                ? $lastDonasi->tanggal_donasi->format('d/m/Y') . ' (Rp ' . number_format($lastDonasi->nominal, 0, ',', '.') . ')'
                : '-';

            $persen = $target > 0 ? round(($totalMasuk / $target) * 100, 1) : 0;

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $k->kode_kelompok);
            $sheet->setCellValue("C{$row}", $k->nama_kelompok);
            $sheet->setCellValue("D{$row}", $k->koordinator_nama ?: ($k->koordinator?->nama ?: '-'));
            $sheet->setCellValue("E{$row}", $k->koordinator_telepon ?: '-');
            $sheet->setCellValue("F{$row}", $siswaText);
            $sheet->setCellValue("G{$row}", $k->anggotaKelompok->count());
            $sheet->setCellValue("H{$row}", $komitmen);
            $sheet->setCellValue("I{$row}", $target);
            $sheet->setCellValue("J{$row}", $totalMasuk);
            $sheet->setCellValue("K{$row}", $saldoKas);
            $sheet->setCellValue("L{$row}", $lastDonasiText);
            $sheet->setCellValue("M{$row}", "{$persen}%");

            $grandTotalKomitmen += $komitmen;
            $grandTotalTarget += $target;
            $grandTotalMasuk += $totalMasuk;
            $grandTotalSaldo += $saldoKas;

            $row++;
        }

        // Baris Kas Bebas (Insidental)
        $insidentalQuery = \App\Modules\BeasiswaOta\Models\DonasiMasuk::whereNull('kelompok_id');
        if ($tahunAjaranId) {
            $insidentalQuery->where('tahun_ajaran_id', $tahunAjaranId);
        }
        $totalMasukInsidental = (float) $insidentalQuery->sum('nominal');
        $saldoInsidental = OtaKasService::getSaldoInsidental();
        $lastInsidental = \App\Modules\BeasiswaOta\Models\DonasiMasuk::whereNull('kelompok_id')->latest('tanggal_donasi')->first();
        $lastInsidentalText = $lastInsidental
            ? $lastInsidental->tanggal_donasi->format('d/m/Y') . ' (Rp ' . number_format($lastInsidental->nominal, 0, ',', '.') . ')'
            : '-';

        $sheet->setCellValue("A{$row}", $no++);
        $sheet->setCellValue("B{$row}", "KAS-BEBAS");
        $sheet->setCellValue("C{$row}", "Kas Bebas (Donasi Insidental Non-Kelompok)");
        $sheet->setCellValue("D{$row}", "Pengelola OTA");
        $sheet->setCellValue("E{$row}", "-");
        $sheet->setCellValue("F{$row}", "Umum / Darurat");
        $sheet->setCellValue("G{$row}", "-");
        $sheet->setCellValue("H{$row}", 0);
        $sheet->setCellValue("I{$row}", 0);
        $sheet->setCellValue("J{$row}", $totalMasukInsidental);
        $sheet->setCellValue("K{$row}", $saldoInsidental);
        $sheet->setCellValue("L{$row}", $lastInsidentalText);
        $sheet->setCellValue("M{$row}", "-");

        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FEF3C7'], // Amber light
            ],
            'font' => ['italic' => true],
        ]);

        $grandTotalMasuk += $totalMasukInsidental;
        $grandTotalSaldo += $saldoInsidental;
        $row++;

        // Baris Grand Total
        $sheet->setCellValue("A{$row}", "TOTAL KESELURUHAN");
        $sheet->mergeCells("A{$row}:G{$row}");
        $sheet->setCellValue("H{$row}", $grandTotalKomitmen);
        $sheet->setCellValue("I{$row}", $grandTotalTarget);
        $sheet->setCellValue("J{$row}", $grandTotalMasuk);
        $sheet->setCellValue("K{$row}", $grandTotalSaldo);
        $sheet->setCellValue("L{$row}", "");
        $sheet->setCellValue("M{$row}", "");

        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F172A'],
            ],
        ]);

        // Format Angka Rupiah
        $sheet->getStyle("H5:K{$row}")->getNumberFormat()->setFormatCode('#,##0');

        // Border & Alignment
        $sheet->getStyle("A4:M{$row}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
        ]);
        $sheet->getStyle("A5:B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G5:G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("L5:M{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto width
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
