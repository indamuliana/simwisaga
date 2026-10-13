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
        $this->selectedColumns = !empty($selectedColumns) ? $selectedColumns : self::ALL_COLUMNS;
        $this->rombelScope = $rombelScope;
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

        $rowIdx = 5;
        $colIdx = 1;
        foreach ($headers as $headerText) {
            $sheet->setCellValueExplicit([$colIdx, $rowIdx], $headerText, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
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
