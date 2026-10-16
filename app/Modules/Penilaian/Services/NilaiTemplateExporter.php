<?php

namespace App\Modules\Penilaian\Services;

use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NilaiTemplateExporter
{
    protected PenugasanMengajar $penugasan;
    protected array $selectedColumns;
    protected string $rombelScope;

    public const ALL_COLUMNS = [
        'AH 1', 'AH 2', 'STS 1', 'AH 3', 'AH 4', 'SAS',
        'AH 5', 'AH 6', 'STS 2', 'AH 7', 'AH 8', 'SAT',
    ];

    public function __construct(PenugasanMengajar $penugasan, array $selectedColumns = [], string $rombelScope = 'current')
    {
        $this->penugasan = $penugasan;
        $this->rombelScope = $rombelScope;
        $this->selectedColumns = !empty($selectedColumns) ? $selectedColumns : self::getColumnsByScope($penugasan, 'semua');
    }

    /**
     * Mengambil seluruh Tujuan Pembelajaran untuk penugasan ini (sepanjang tahun ajaran)
     */
    public static function getAllTujuanPembelajaran(PenugasanMengajar $penugasan): \Illuminate\Support\Collection
    {
        $taId = $penugasan->semester?->tahun_ajaran_id;
        $semGanjil = \App\Modules\Akademik\Models\Semester::where('tahun_ajaran_id', $taId)->where('semester', 'ganjil')->first();
        $semGenap = \App\Modules\Akademik\Models\Semester::where('tahun_ajaran_id', $taId)->where('semester', 'genap')->first();

        $pmGanjil = PenugasanMengajar::where('rombel_id', $penugasan->rombel_id)
            ->where('mapel_id', $penugasan->mapel_id)
            ->where('semester_id', $semGanjil?->id)
            ->first();
        $pmGenap = PenugasanMengajar::where('rombel_id', $penugasan->rombel_id)
            ->where('mapel_id', $penugasan->mapel_id)
            ->where('semester_id', $semGenap?->id)
            ->first();

        $pmIds = array_values(array_unique(array_filter([$penugasan->id, $pmGanjil?->id, $pmGenap?->id])));

        return \App\Modules\Penilaian\Models\TujuanPembelajaran::whereIn('penugasan_mengajar_id', $pmIds)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();
    }

    /**
     * Menghasilkan daftar kolom template secara dinamis berdasarkan Tujuan Pembelajaran yang ada
     */
    public static function getColumnsByScope(PenugasanMengajar $penugasan, string $scope = 'semua', ?string $activePeriode = null): array
    {
        $tps = self::getAllTujuanPembelajaran($penugasan);

        // Jika belum ada TP sama sekali, fallback ke kolom AH legacy
        if ($tps->isEmpty()) {
            if ($scope === 'ganjil') {
                return ['AH 1', 'AH 2', 'STS 1', 'AH 3', 'AH 4', 'SAS'];
            } elseif ($scope === 'genap') {
                return ['AH 5', 'AH 6', 'STS 2', 'AH 7', 'AH 8', 'SAT'];
            } elseif ($scope === 'periode_aktif') {
                return match ($activePeriode) {
                    '9M1' => ['AH 1', 'AH 2', 'STS 1'],
                    '9M2' => ['AH 3', 'AH 4', 'SAS'],
                    '9M3' => ['AH 5', 'AH 6', 'STS 2'],
                    '9M4' => ['AH 7', 'AH 8', 'SAT'],
                    default => ['AH 1', 'AH 2', 'STS 1'],
                };
            }
            return self::ALL_COLUMNS;
        }

        $tpsByPeriod = function ($p) use ($tps) {
            return $tps->filter(function ($t) use ($p) {
                $tpPeriod = $t->target_periode;
                if (empty($tpPeriod)) {
                    $tpPeriod = match ($t->alokasi_ah) {
                        'AH 1', 'AH 2' => '9M1',
                        'AH 3', 'AH 4' => '9M2',
                        'AH 5', 'AH 6' => '9M3',
                        'AH 7', 'AH 8' => '9M4',
                        default => '9M1',
                    };
                }
                return $tpPeriod === $p;
            });
        };

        $getColsForPeriod = function ($p) use ($tpsByPeriod) {
            $pTps = $tpsByPeriod($p);
            $cols = [];
            foreach ($pTps as $tp) {
                $cols[] = $tp->kode;
            }
            $cols[] = "NH {$p}";
            $sumatif = match ($p) {
                '9M1' => 'STS 1',
                '9M2' => 'SAS',
                '9M3' => 'STS 2',
                '9M4' => 'SAT',
                default => 'STS 1',
            };
            $cols[] = $sumatif;
            return $cols;
        };

        if ($scope === 'periode_aktif') {
            $p = $activePeriode ?: '9M1';
            return $getColsForPeriod($p);
        } elseif ($scope === 'ganjil') {
            return array_merge($getColsForPeriod('9M1'), $getColsForPeriod('9M2'));
        } elseif ($scope === 'genap') {
            return array_merge($getColsForPeriod('9M3'), $getColsForPeriod('9M4'));
        }

        // Default: 'semua'
        return array_merge(
            $getColsForPeriod('9M1'),
            $getColsForPeriod('9M2'),
            $getColsForPeriod('9M3'),
            $getColsForPeriod('9M4')
        );
    }

    /**
     * Mengambil koleksi siswa sesuai cakupan rombel yang dipilih
     */
    public function getSiswas()
    {
        if ($this->rombelScope === 'all_assigned') {
            // Ambil semua rombel yang diampu oleh guru ini untuk mapel yang sama pada tahun ajaran ini
            $tahunAjaranId = $this->penugasan->semester?->tahun_ajaran_id;
            
            $query = PenugasanMengajar::where('mapel_id', $this->penugasan->mapel_id)
                ->whereHas('semester', function ($q) use ($tahunAjaranId) {
                    if ($tahunAjaranId) {
                        $q->where('tahun_ajaran_id', $tahunAjaranId);
                    }
                });

            if ($this->penugasan->pegawai_id) {
                $query->where('pegawai_id', $this->penugasan->pegawai_id);
            }

            $rombelIds = $query->pluck('rombel_id')->unique()->filter()->all();
            if (empty($rombelIds)) {
                $rombelIds = [$this->penugasan->rombel_id];
            }

            return Siswa::whereIn('rombel_id', $rombelIds)
                ->where('status', 'aktif')
                ->where('is_active', true)
                ->with('rombel')
                ->join('rombels', 'siswas.rombel_id', '=', 'rombels.id')
                ->orderBy('rombels.nama')
                ->orderBy('siswas.nama')
                ->select('siswas.*')
                ->get();
        }

        // Rombel aktif saja
        return Siswa::where('rombel_id', $this->penugasan->rombel_id)
            ->where('status', 'aktif')
            ->where('is_active', true)
            ->with('rombel')
            ->orderBy('nama')
            ->get();
    }

    /**
     * Membangun Spreadsheet dan mengembalikan instance PhpSpreadsheet
     */
    public function buildSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Nilai');

        $mapelNama = $this->penugasan->mapel->nama ?? 'Mata Pelajaran';
        $rombelNama = $this->penugasan->rombel->nama ?? '-';
        $taNama = $this->penugasan->semester?->tahunAjaran?->nama ?? '-';

        // 1. Informasi Header Dokumen
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT NILAI AKADEMIK - SIMWISAGA');
        $sheet->setCellValue('A2', "Mata Pelajaran: {$mapelNama} | Kelas: " . ($this->rombelScope === 'all_assigned' ? 'Semua Kelas Diampu' : $rombelNama) . " | T.A: {$taNama}");
        $sheet->setCellValue('A3', 'Catatan: Jangan mengubah header kolom. Kosongkan nilai jika belum ada penilaian.');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('64748B');

        // 2. Header Tabel Nilai
        $headers = ['No', 'NIS', 'Nama Siswa', 'Rombel'];
        foreach ($this->selectedColumns as $col) {
            $headers[] = $col;
        }

        $allTps = self::getAllTujuanPembelajaran($this->penugasan)->keyBy(fn($t) => strtoupper(trim($t->kode)));

        $rowIdx = 5;
        $colIdx = 1;
        foreach ($headers as $headerText) {
            $sheet->setCellValueExplicit([$colIdx, $rowIdx], $headerText, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            // Tambahkan komentar/keterangan informatif jika kolom asesmen
            $cleanKey = strtoupper(trim($headerText));
            if ($tp = $allTps->get($cleanKey)) {
                $commentText = "Tujuan Pembelajaran: {$tp->kode}\n{$tp->deskripsi}";
                if (!empty($tp->lingkup_materi)) {
                    $commentText .= "\nLingkup: {$tp->lingkup_materi}";
                }
                if (!empty($tp->target_periode)) {
                    $commentText .= "\nPeriode: {$tp->target_periode}";
                }
                $sheet->getComment([$colIdx, $rowIdx])
                    ->setAuthor('SIRAGA')
                    ->setWidth('260pt')
                    ->setHeight('65pt')
                    ->getText()
                    ->createTextRun($commentText);
            } elseif (preg_match('/^NH(?:\s*(?:9M[1-4]|[1-4]))?$/i', $cleanKey)) {
                $sheet->getComment([$colIdx, $rowIdx])
                    ->setAuthor('SIRAGA')
                    ->setWidth('220pt')
                    ->setHeight('45pt')
                    ->getText()
                    ->createTextRun("Nilai Harian (NH): Nilai subjektif guru (Skala 0-100, default 100).");
            } elseif (in_array($cleanKey, ['STS 1', 'SAS', 'STS 2', 'SAT'])) {
                $sumLabel = match($cleanKey) {
                    'STS 1' => 'Sumatif Tengah Semester 1',
                    'SAS' => 'Sumatif Akhir Semester',
                    'STS 2' => 'Sumatif Tengah Semester 2',
                    'SAT' => 'Sumatif Akhir Tahun',
                    default => 'Asesmen Sumatif',
                };
                $sheet->getComment([$colIdx, $rowIdx])
                    ->setAuthor('SIRAGA')
                    ->setWidth('220pt')
                    ->setHeight('45pt')
                    ->getText()
                    ->createTextRun("Nilai {$sumLabel} (Skala 0-100).");
            }

            $colIdx++;
        }

        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));

        // Styling Header Kolom
        $headerRange = "A5:{$lastColLetter}5";
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF'], // Indigo Blue
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '94A3B8'],
                ],
            ],
        ]);

        // 3. Mengisi Data Siswa
        $siswas = $this->getSiswas();
        $rowIdx = 6;
        $no = 1;

        foreach ($siswas as $siswa) {
            $sheet->setCellValue("A{$rowIdx}", $no++);
            
            // Format NIS eksplisit sebagai string agar leading zero tidak hilang
            $nisVal = $siswa->nipd ?: $siswa->nisn ?: (string)$siswa->id;
            $sheet->setCellValueExplicit("B{$rowIdx}", (string)$nisVal, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            
            $sheet->setCellValue("C{$rowIdx}", $siswa->nama);
            $sheet->setCellValue("D{$rowIdx}", $siswa->rombel?->nama ?? $rombelNama);

            // Kolom nilai dikosongkan untuk diisi guru
            $colNum = 5;
            foreach ($this->selectedColumns as $col) {
                $sheet->setCellValue([$colNum, $rowIdx], '');
                $sheet->getStyle([$colNum, $rowIdx])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $colNum++;
            }

            // Zebra striping lembut
            if ($rowIdx % 2 === 0) {
                $sheet->getStyle("A{$rowIdx}:{$lastColLetter}{$rowIdx}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8FAFC');
            }

            $rowIdx++;
        }

        $lastRow = max($rowIdx - 1, 6);

        // Border seluruh tabel
        $sheet->getStyle("A5:{$lastColLetter}{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');

        // Center align untuk kolom No, NIS, Rombel
        $sheet->getStyle("A6:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B6:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D6:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto size kolom
        for ($i = 1; $i <= count($headers); $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            if ($i === 3) {
                $sheet->getColumnDimension($colLetter)->setWidth(30); // Nama Siswa
            } elseif ($i === 2) {
                $sheet->getColumnDimension($colLetter)->setWidth(16); // NIS
            } elseif ($i === 1) {
                $sheet->getColumnDimension($colLetter)->setWidth(6);  // No
            } elseif ($i === 4) {
                $sheet->getColumnDimension($colLetter)->setWidth(16); // Rombel
            } else {
                $sheet->getColumnDimension($colLetter)->setWidth(12); // Kolom Nilai
            }
        }

        return $spreadsheet;
    }

    /**
     * Download template secara langsung sebagai StreamedResponse
     */
    public function downloadResponse(): StreamedResponse
    {
        $spreadsheet = $this->buildSpreadsheet();
        $mapelSlug = str_replace([' ', '/', '\\'], '_', strtolower($this->penugasan->mapel->nama ?? 'mapel'));
        $rombelSlug = str_replace([' ', '/', '\\'], '_', strtolower($this->penugasan->rombel->nama ?? 'rombel'));
        $filename = "Template_Nilai_{$mapelSlug}_{$rombelSlug}_" . date('Ymd_His') . ".xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
