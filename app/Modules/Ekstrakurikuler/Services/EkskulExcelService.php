<?php

namespace App\Modules\Ekstrakurikuler\Services;

use App\Modules\Ekstrakurikuler\Models\Ekstrakurikuler;
use App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class EkskulExcelService
{
    /**
     * Generate template Excel untuk import anggota ekskul
     */
    public function generateTemplate(?int $rombelId = null): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Anggota Ekskul');

        // Header Informasi
        $sheet->setCellValue('A1', 'TEMPLATE PENDAFTARAN ANGGOTA EKSTRAKURIKULER');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Petunjuk: Isi kolom "Nama Ekskul" sesuai daftar ekskul yang tersedia. Jangan ubah kolom NISN/NIS/Nama.');
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);

        // Header Kolom
        $headers = ['No', 'NISN', 'NIS / NIPD', 'Nama Siswa', 'Rombel', 'Nama Ekskul Pilihan'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F'];

        foreach ($headers as $idx => $title) {
            $sheet->setCellValue($cols[$idx] . '4', $title);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A4:F4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(25);

        // Ambil Siswa
        $query = Siswa::with('rombel')->where('is_active', true);
        if ($rombelId) {
            $query->where('rombel_id', $rombelId);
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
            $sheet->setCellValue('F' . $row, ''); // Kolom ekskul diisi user

            $row++;
        }

        $lastRow = max(5, $row - 1);
        $dataBorderStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ];
        $sheet->getStyle('A5:F' . $lastRow)->applyFromArray($dataBorderStyle);

        // Sheet 2: Daftar Referensi Ekskul Aktif
        $sheetRef = $spreadsheet->createSheet();
        $sheetRef->setTitle('Referensi Ekskul');
        $sheetRef->setCellValue('A1', 'DAFTAR EKSTRAKURIKULER AKTIF');
        $sheetRef->getStyle('A1')->getFont()->setBold(true);
        $sheetRef->setCellValue('A3', 'Nama Ekskul');
        $sheetRef->setCellValue('B3', 'Kategori');
        $sheetRef->setCellValue('C3', 'Instruktur');
        $sheetRef->getStyle('A3:C3')->getFont()->setBold(true);

        $ekskuls = Ekstrakurikuler::where('is_active', true)->get();
        $refRow = 4;
        foreach ($ekskuls as $e) {
            $sheetRef->setCellValue('A' . $refRow, $e->nama);
            $sheetRef->setCellValue('B' . $refRow, $e->is_wajib ? 'Wajib (Tingkat X)' : 'Pilihan (Bakat & Minat)');
            $sheetRef->setCellValue('C' . $refRow, $e->instruktur_display_name);
            $refRow++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        foreach (range('A', 'C') as $col) {
            $sheetRef->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);
        return $spreadsheet;
    }

    /**
     * Import anggota ekskul dari spreadsheet
     */
    public function importAnggota(string $filePath, int $semesterId, bool $allowTransfer = true): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestDataRow();

        $successCount = 0;
        $transferCount = 0;
        $failedRows = [];

        // Cache ekskul by name (lowercase trimmed)
        $ekskulMap = Ekstrakurikuler::where('is_active', true)
            ->get()
            ->keyBy(fn($e) => strtolower(trim($e->nama)));

        for ($row = 5; $row <= $highestRow; $row++) {
            $nisn = trim((string) $sheet->getCell('B' . $row)->getValue());
            $nipd = trim((string) $sheet->getCell('C' . $row)->getValue());
            $namaSiswa = trim((string) $sheet->getCell('D' . $row)->getValue());
            $namaEkskul = trim((string) $sheet->getCell('F' . $row)->getValue());

            if (empty($namaEkskul)) {
                continue; // Baris dilewati jika ekskul tidak diisi
            }

            // Cari siswa berdasarkan NISN, NIPD, atau Nama
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

            // Cari ekskul
            $cleanEkskulName = strtolower($namaEkskul);
            $ekskul = $ekskulMap->get($cleanEkskulName);

            if (!$ekskul) {
                // Coba pencarian like
                $ekskul = Ekstrakurikuler::where('is_active', true)
                    ->where('nama', 'like', '%' . $namaEkskul . '%')
                    ->first();
            }

            if (!$ekskul) {
                $failedRows[] = "Baris {$row}: Ekskul '{$namaEkskul}' tidak ditemukan atau non-aktif.";
                continue;
            }

            // Cek apakah siswa sudah terdaftar di ekskul ini
            $existing = EkstrakurikulerAnggota::where('ekstrakurikuler_id', $ekskul->id)
                ->where('siswa_id', $siswa->id)
                ->where('semester_id', $semesterId)
                ->first();

            if ($existing) {
                if ($existing->status_keanggotaan !== 'aktif') {
                    $existing->update(['status_keanggotaan' => 'aktif']);
                    $successCount++;
                }
                continue;
            }

            // Cek jika allowTransfer = true dan ekskul ini bertipe pilihan, apakah siswa sudah punya ekskul pilihan lain
            if ($allowTransfer && !$ekskul->is_wajib) {
                $otherChoice = EkstrakurikulerAnggota::where('siswa_id', $siswa->id)
                    ->where('semester_id', $semesterId)
                    ->where('status_keanggotaan', 'aktif')
                    ->whereHas('ekstrakurikuler', fn($q) => $q->where('is_wajib', false))
                    ->first();

                if ($otherChoice) {
                    // Tandai ekskul lama sebagai 'pindah'
                    $oldName = $otherChoice->ekstrakurikuler?->nama ?? 'sebelumnya';
                    $otherChoice->update([
                        'status_keanggotaan' => 'pindah',
                        'catatan_pindah' => "Pindah ke {$ekskul->nama} pada periode ini"
                    ]);
                    $transferCount++;
                }
            }

            // Daftarkan ke ekskul baru
            EkstrakurikulerAnggota::create([
                'ekstrakurikuler_id' => $ekskul->id,
                'siswa_id' => $siswa->id,
                'semester_id' => $semesterId,
                'status_keanggotaan' => 'aktif',
                'nilai_presensi' => 100,
                'persentase_kehadiran' => 100.00,
            ]);

            $successCount++;
        }

        return [
            'success_count' => $successCount,
            'transfer_count' => $transferCount,
            'failed_rows' => $failedRows,
        ];
    }

    /**
     * Export Rekapitulasi Nilai & Presensi Ekskul ke Excel
     */
    public function exportRekap(int $ekskulId, int $semesterId): Spreadsheet
    {
        $ekskul = Ekstrakurikuler::with('pembina', 'instrukturUser')->findOrFail($ekskulId);
        $semester = Semester::with('tahunAjaran')->findOrFail($semesterId);

        $anggotas = EkstrakurikulerAnggota::with('siswa.rombel')
            ->where('ekstrakurikuler_id', $ekskulId)
            ->where('semester_id', $semesterId)
            ->where('status_keanggotaan', 'aktif')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($ekskul->nama, 0, 30));

        // Header Title
        $sheet->setCellValue('A1', 'REKAPITULASI PRESENSI & PENILAIAN EKSTRAKURIKULER');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', "Ekskul: {$ekskul->nama} | Instruktur: {$ekskul->instruktur_display_name} | Semester: " . ucfirst($semester->semester) . " {$semester->tahunAjaran->nama}");
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2')->getFont()->setSize(10);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A3', "Bobot Penilaian: Presensi Mingguan {$ekskul->bobot_presensi}% + Asesmen Periode {$ekskul->bobot_penilaian}%");
        $sheet->mergeCells('A3:K3');
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header Table
        $headers = [
            'No', 'NISN', 'Nama Siswa', 'Kelas',
            'Total Hadir', 'Total Sesi', '% Kehadiran',
            'Nilai Asesmen', 'Nilai Akhir', 'Predikat', 'Deskripsi Capaian'
        ];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];

        foreach ($headers as $i => $title) {
            $sheet->setCellValue($cols[$i] . '5', $title);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '047857']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A5:K5')->applyFromArray($headerStyle);
        $sheet->getRowDimension(5)->setRowHeight(25);

        $row = 6;
        $no = 1;
        foreach ($anggotas as $a) {
            $asesmen = $semester->semester === 'ganjil' ? ($a->sas ?? $a->sts_1) : ($a->sat ?? $a->sts_2);

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValueExplicit('B' . $row, (string) ($a->siswa?->nisn ?? '-'), DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $row, $a->siswa?->nama ?? '-');
            $sheet->setCellValue('D' . $row, $a->siswa?->rombel?->nama ?? '-');
            $sheet->setCellValue('E' . $row, $a->total_hadir);
            $sheet->setCellValue('F' . $row, $a->total_pertemuan);
            $sheet->setCellValue('G' . $row, $a->persentase_kehadiran . '%');
            $sheet->setCellValue('H' . $row, $asesmen ?? '-');
            $sheet->setCellValue('I' . $row, $a->nilai_angka ?? '-');
            $sheet->setCellValue('J' . $row, $a->nilai_huruf ?? '-');
            $sheet->setCellValue('K' . $row, $a->deskripsi ?? '-');

            $row++;
        }

        $lastRow = max(6, $row - 1);
        $sheet->getStyle('A6:K' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ]);

        $sheet->getStyle('A6:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E6:J' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Generate template Excel sederhana 2 kolom (A: NIS, B: Nama Siswa) khusus untuk suatu UPD pilihan
     */
    public function generateTemplateForEkskul(int $ekskulId): Spreadsheet
    {
        $ekskul = Ekstrakurikuler::findOrFail($ekskulId);
        if ($ekskul->is_wajib) {
            throw new \InvalidArgumentException("Fitur import Excel ini hanya berlaku untuk UPD Pilihan.");
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr('Anggota ' . $ekskul->nama, 0, 30));

        // Header Title
        $sheet->setCellValue('A1', 'TEMPLATE PENDAFTARAN ANGGOTA UPD: ' . strtoupper($ekskul->nama));
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);

        $sheet->setCellValue('A2', 'Petunjuk: Masukkan NIS dan Nama Siswa mulai baris 5. Jangan mengubah teks baris header kolom 4.');
        $sheet->mergeCells('A2:B2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('6B7280');

        // Header Columns
        $sheet->setCellValue('A4', 'NIS');
        $sheet->setCellValue('B4', 'Nama Siswa');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A4:B4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(24);

        // Beri grid border kosong dari baris 5 sampai 30 agar pengguna tahu area pengisian
        $sheet->getStyle('A5:B30')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
        ]);

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);

        return $spreadsheet;
    }

    /**
     * Preview dan verifikasi baris import anggota Excel untuk suatu UPD pilihan
     */
    public function previewImportPerEkskul(string $filePath, int $targetEkskulId, int $semesterId): array
    {
        $targetEkskul = Ekstrakurikuler::findOrFail($targetEkskulId);
        if ($targetEkskul->is_wajib) {
            throw new \InvalidArgumentException("Fitur import Excel ini hanya berlaku untuk UPD Pilihan.");
        }

        try {
            $reader = IOFactory::createReaderForFile($filePath);
            $spreadsheet = $reader->load($filePath);
        } catch (\Throwable $e) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($filePath);
        }
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        // Ambil semua data siswa aktif, di-index dengan string nipd (NIS)
        $allStudents = Siswa::with('rombel')
            ->where('is_active', true)
            ->whereNotNull('nipd')
            ->where('nipd', '!=', '')
            ->get()
            ->keyBy(fn($s) => trim((string)$s->nipd));

        // Ambil keanggotaan aktif semester ini khusus di UPD pilihan (bukan wajib)
        $activeChoiceMemberships = EkstrakurikulerAnggota::with('ekstrakurikuler')
            ->where('semester_id', $semesterId)
            ->where('status_keanggotaan', 'aktif')
            ->whereHas('ekstrakurikuler', fn($q) => $q->where('is_wajib', false))
            ->get()
            ->groupBy('siswa_id');

        $rows = [];
        $seenNis = [];
        $validCount = 0;
        $newCount = 0;
        $transferCount = 0;
        $alreadyCount = 0;
        $errorCount = 0;

        for ($rowNum = 5; $rowNum <= $highestRow; $rowNum++) {
            $rawNis = trim((string)$sheet->getCell('A' . $rowNum)->getValue());
            $rawNama = trim((string)$sheet->getCell('B' . $rowNum)->getValue());

            // Lewati baris kosong
            if ($rawNis === '' && $rawNama === '') {
                continue;
            }

            if ($rawNis === '') {
                $rows[] = [
                    'row_num' => $rowNum,
                    'nis' => '',
                    'nama_excel' => $rawNama,
                    'siswa_id' => null,
                    'nama_db' => '-',
                    'rombel' => '-',
                    'status' => 'error',
                    'badge_type' => 'rose',
                    'label' => 'Error: NIS Kosong',
                    'is_valid' => false,
                    'old_anggota_id' => null,
                    'old_ekskul_name' => null,
                ];
                $errorCount++;
                continue;
            }

            // Cek duplikasi dalam file
            if (isset($seenNis[$rawNis])) {
                $rows[] = [
                    'row_num' => $rowNum,
                    'nis' => $rawNis,
                    'nama_excel' => $rawNama,
                    'siswa_id' => null,
                    'nama_db' => '-',
                    'rombel' => '-',
                    'status' => 'error',
                    'badge_type' => 'rose',
                    'label' => "Error: NIS {$rawNis} duplikat di file",
                    'is_valid' => false,
                    'old_anggota_id' => null,
                    'old_ekskul_name' => null,
                ];
                $errorCount++;
                continue;
            }
            $seenNis[$rawNis] = true;

            // Cari siswa di database via NIPD (NIS)
            $siswa = $allStudents->get($rawNis);

            if (!$siswa) {
                $rows[] = [
                    'row_num' => $rowNum,
                    'nis' => $rawNis,
                    'nama_excel' => $rawNama,
                    'siswa_id' => null,
                    'nama_db' => '-',
                    'rombel' => '-',
                    'status' => 'error',
                    'badge_type' => 'rose',
                    'label' => "Error: NIS {$rawNis} tidak ditemukan di database",
                    'is_valid' => false,
                    'old_anggota_id' => null,
                    'old_ekskul_name' => null,
                ];
                $errorCount++;
                continue;
            }

            // Cek keanggotaan UPD pilihan yang sedang aktif
            $choiceMemberships = $activeChoiceMemberships->get($siswa->id, collect());
            $currentInTarget = $choiceMemberships->firstWhere('ekstrakurikuler_id', $targetEkskul->id);
            $currentOtherChoice = $choiceMemberships->firstWhere('ekstrakurikuler_id', '!=', $targetEkskul->id);

            if ($currentInTarget) {
                $rows[] = [
                    'row_num' => $rowNum,
                    'nis' => $rawNis,
                    'nama_excel' => $rawNama,
                    'siswa_id' => $siswa->id,
                    'nama_db' => $siswa->nama,
                    'rombel' => $siswa->rombel?->nama ?? '-',
                    'status' => 'already',
                    'badge_type' => 'emerald',
                    'label' => 'Sudah Terdaftar di UPD Ini',
                    'is_valid' => true,
                    'old_anggota_id' => null,
                    'old_ekskul_name' => null,
                ];
                $alreadyCount++;
                $validCount++;
            } elseif ($currentOtherChoice) {
                $oldName = $currentOtherChoice->ekstrakurikuler?->nama ?? 'UPD Lain';
                $rows[] = [
                    'row_num' => $rowNum,
                    'nis' => $rawNis,
                    'nama_excel' => $rawNama,
                    'siswa_id' => $siswa->id,
                    'nama_db' => $siswa->nama,
                    'rombel' => $siswa->rombel?->nama ?? '-',
                    'status' => 'transfer',
                    'badge_type' => 'amber',
                    'label' => "Mutasi dari {$oldName}",
                    'is_valid' => true,
                    'old_anggota_id' => $currentOtherChoice->id,
                    'old_ekskul_name' => $oldName,
                ];
                $transferCount++;
                $validCount++;
            } else {
                $rows[] = [
                    'row_num' => $rowNum,
                    'nis' => $rawNis,
                    'nama_excel' => $rawNama,
                    'siswa_id' => $siswa->id,
                    'nama_db' => $siswa->nama,
                    'rombel' => $siswa->rombel?->nama ?? '-',
                    'status' => 'new',
                    'badge_type' => 'blue',
                    'label' => 'Siswa Baru',
                    'is_valid' => true,
                    'old_anggota_id' => null,
                    'old_ekskul_name' => null,
                ];
                $newCount++;
                $validCount++;
            }
        }

        return [
            'target_ekskul_id' => $targetEkskul->id,
            'target_ekskul_nama' => $targetEkskul->nama,
            'total_rows' => count($rows),
            'valid_count' => $validCount,
            'new_count' => $newCount,
            'transfer_count' => $transferCount,
            'already_count' => $alreadyCount,
            'error_count' => $errorCount,
            'rows' => $rows,
        ];
    }

    /**
     * Eksekusi penyimpanan baris-baris valid import ke database
     */
    public function commitImportPerEkskul(array $validRows, int $targetEkskulId, int $semesterId): array
    {
        $targetEkskul = Ekstrakurikuler::findOrFail($targetEkskulId);
        if ($targetEkskul->is_wajib) {
            throw new \InvalidArgumentException("Fitur import Excel ini hanya berlaku untuk UPD Pilihan.");
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($validRows, $targetEkskul, $semesterId) {
            $successCount = 0;
            $newCount = 0;
            $transferCount = 0;

            foreach ($validRows as $row) {
                if (!$row['is_valid'] || empty($row['siswa_id'])) {
                    continue;
                }

                $siswaId = $row['siswa_id'];
                $status = $row['status'];

                if ($status === 'transfer' && !empty($row['old_anggota_id'])) {
                    // Update keanggotaan lama menjadi pindah
                    EkstrakurikulerAnggota::where('id', $row['old_anggota_id'])
                        ->update([
                            'status_keanggotaan' => 'pindah',
                            'catatan_pindah' => "Mutasi via Import ke UPD {$targetEkskul->nama}",
                        ]);
                    $transferCount++;
                } elseif ($status === 'new') {
                    $newCount++;
                }

                // Daftarkan aktif di target UPD
                $anggota = EkstrakurikulerAnggota::firstOrCreate(
                    [
                        'ekstrakurikuler_id' => $targetEkskul->id,
                        'siswa_id' => $siswaId,
                        'semester_id' => $semesterId,
                    ],
                    [
                        'status_keanggotaan' => 'aktif',
                        'nilai_presensi' => 100,
                        'persentase_kehadiran' => 100.00,
                    ]
                );

                if ($anggota->status_keanggotaan !== 'aktif') {
                    $anggota->update(['status_keanggotaan' => 'aktif']);
                }

                $successCount++;
            }

            return [
                'success' => true,
                'total_processed' => $successCount,
                'new_count' => $newCount,
                'transfer_count' => $transferCount,
                'target_ekskul_nama' => $targetEkskul->nama,
            ];
        });
    }
}
