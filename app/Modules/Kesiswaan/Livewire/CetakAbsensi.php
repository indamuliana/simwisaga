<?php

namespace App\Modules\Kesiswaan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kesiswaan\Models\Program;
use App\Modules\Kesiswaan\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('components.layouts.app')]
#[Title('Cetak Absensi')]
class CetakAbsensi extends Component
{
    use GuardsPermission;

    // Filters
    public ?int $filterRombel = null;
    public ?int $filterRayon = null;
    public ?int $filterJurusan = null;
    public ?int $filterProgram = null;
    public string $filterTingkat = '';
    public string $filterJk = '';

    // Shortir by (Sorting)
    public string $sortBy = 'nipd_asc';

    // Cetak Dokumen Settings
    public string $tahunPelajaran = '';
    public string $mapel = '';
    public int $jumlahKolom = 30;
    public string $namaGuru = '';
    public string $nipGuru = '';

    public function mount(): void
    {
        $this->guard('kesiswaan.siswa.lihat');

        $activeTa = AcademicContext::currentTahunAjaran();
        $this->tahunPelajaran = $activeTa?->nama ?? '2026-2027';
    }

    public function resetFilters(): void
    {
        $this->filterRombel = null;
        $this->filterRayon = null;
        $this->filterJurusan = null;
        $this->filterProgram = null;
        $this->filterTingkat = '';
        $this->filterJk = '';
        $this->sortBy = 'nipd_asc';
        $this->mapel = '';
    }

    public function buildQuery()
    {
        $query = Siswa::query()
            ->with(['rombel.jurusan', 'rayon', 'program'])
            ->where('is_active', true)
            ->where('status', 'aktif');

        if ($this->filterRombel) {
            $query->where('rombel_id', $this->filterRombel);
        }

        if ($this->filterRayon) {
            $query->where('rayon_id', $this->filterRayon);
        }

        if ($this->filterJurusan) {
            $query->whereHas('rombel', function ($q) {
                $q->where('jurusan_id', $this->filterJurusan);
            });
        }

        if ($this->filterProgram) {
            $query->where('program_id', $this->filterProgram);
        }

        if ($this->filterTingkat) {
            $query->whereHas('rombel', function ($q) {
                $q->where('tingkat', $this->filterTingkat);
            });
        }

        if ($this->filterJk) {
            $query->where('jk', $this->filterJk);
        }

        // Shortir by logic
        switch ($this->sortBy) {
            case 'nama_asc':
                $query->orderBy('nama', 'asc');
                break;
            case 'nama_desc':
                $query->orderBy('nama', 'desc');
                break;
            case 'nipd_desc':
                $query->orderByRaw('COALESCE(nipd, nisn) DESC')->orderBy('nama', 'asc');
                break;
            case 'rombel_asc':
                $query->leftJoin('rombels', 'siswas.rombel_id', '=', 'rombels.id')
                    ->orderBy('rombels.tingkat', 'asc')
                    ->orderBy('rombels.nama', 'asc')
                    ->orderBy('siswas.nama', 'asc')
                    ->select('siswas.*');
                break;
            case 'rayon_asc':
                $query->leftJoin('rayons', 'siswas.rayon_id', '=', 'rayons.id')
                    ->orderBy('rayons.nama', 'asc')
                    ->orderBy('siswas.nama', 'asc')
                    ->select('siswas.*');
                break;
            case 'jk_asc':
                $query->orderBy('jk', 'asc')->orderBy('nama', 'asc');
                break;
            case 'nipd_asc':
            default:
                $query->orderByRaw('COALESCE(nipd, nisn) ASC')->orderBy('nama', 'asc');
                break;
        }

        return $query;
    }

    public function buildFilterLabel(): string
    {
        $parts = [];

        if ($this->filterJurusan) {
            $jurusan = Jurusan::find($this->filterJurusan);
            $parts[] = $jurusan ? "Jurusan {$jurusan->kode}" : 'Jurusan';
        }
        if ($this->filterTingkat) {
            $parts[] = "Kelas {$this->filterTingkat}";
        }
        if ($this->filterRombel) {
            $rombel = Rombel::find($this->filterRombel);
            $parts[] = $rombel ? "Rombel {$rombel->nama}" : 'Rombel';
        }
        if ($this->filterRayon) {
            $rayon = Rayon::find($this->filterRayon);
            $parts[] = $rayon ? "Rayon {$rayon->nama}" : 'Rayon';
        }
        if ($this->filterJk) {
            $parts[] = $this->filterJk === 'L' ? 'Putra (L)' : 'Putri (P)';
        }
        if ($this->filterProgram) {
            $program = Program::find($this->filterProgram);
            $parts[] = $program ? "Program {$program->nama}" : 'Program';
        }

        return $parts ? implode(' · ', $parts) : 'Semua Siswa Aktif';
    }

    public function getSortLabel(): string
    {
        return match ($this->sortBy) {
            'nama_asc' => 'Nama (A–Z)',
            'nama_desc' => 'Nama (Z–A)',
            'nipd_desc' => 'NIS Terbesar ke Terkecil',
            'rombel_asc' => 'Rombongan Belajar',
            'rayon_asc' => 'Rayon Wilayah',
            'jk_asc' => 'Jenis Kelamin (Lalu Nama)',
            default => 'NIS Terkecil ke Terbesar',
        };
    }

    /**
     * Unduh PDF Lembar Absensi Cetak Landscape dengan Kolom 1-30
     */
    public function cetakAbsensi(): ?StreamedResponse
    {
        $this->guard('kesiswaan.siswa.cetak');

        $siswas = $this->buildQuery()->get();

        if ($siswas->isEmpty()) {
            session()->flash('error', 'Tidak ada data siswa yang sesuai dengan filter yang dipilih.');
            return null;
        }

        $filterLabel = $this->buildFilterLabel();
        $sortLabel = $this->getSortLabel();
        $tahunPelajaran = $this->tahunPelajaran ?: '2026-2027';
        $jumlahKolom = max(1, min(50, $this->jumlahKolom));

        $pdf = Pdf::loadView('kesiswaan::pdf.cetak-absensi', [
            'siswas' => $siswas,
            'filterLabel' => $filterLabel,
            'sortLabel' => $sortLabel,
            'tahunPelajaran' => $tahunPelajaran,
            'mapel' => $this->mapel,
            'jumlahKolom' => $jumlahKolom,
            'namaGuru' => $this->namaGuru,
            'nipGuru' => $this->nipGuru,
        ])->setPaper('A4', 'portrait');

        $safeLabel = Str::slug($filterLabel);
        $filename = "Daftar_Hadir_Murid_{$safeLabel}_" . date('Ymd_His') . ".pdf";

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename
        );
    }

    /**
     * Unduh berkas format Word (.doc)
     */
    public function cetakWord(): ?StreamedResponse
    {
        $this->guard('kesiswaan.siswa.cetak');

        $siswas = $this->buildQuery()->get();

        if ($siswas->isEmpty()) {
            session()->flash('error', 'Tidak ada data siswa yang sesuai dengan filter yang dipilih.');
            return null;
        }

        $filterLabel = $this->buildFilterLabel();
        $sortLabel = $this->getSortLabel();
        $tahunPelajaran = $this->tahunPelajaran ?: '2026-2027';
        $jumlahKolom = max(1, min(50, $this->jumlahKolom));

        $html = view('kesiswaan::pdf.cetak-absensi', [
            'siswas' => $siswas,
            'filterLabel' => $filterLabel,
            'sortLabel' => $sortLabel,
            'tahunPelajaran' => $tahunPelajaran,
            'mapel' => $this->mapel,
            'jumlahKolom' => $jumlahKolom,
            'namaGuru' => $this->namaGuru,
            'nipGuru' => $this->nipGuru,
        ])->render();

        // Agar file doc terbuka dengan layout cetak web yang baik,
        // tambahkan meta charset UTF-8 di string output.
        $safeLabel = Str::slug($filterLabel);
        $filename = "Daftar_Hadir_Murid_{$safeLabel}_" . date('Ymd_His') . ".doc";

        return response()->streamDownload(
            fn () => print($html),
            $filename,
            [
                'Content-Type' => 'application/msword',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    /**
     * Unduh berkas format Excel (.xlsx) dengan 30 kolom presensi
     */
    public function cetakExcel(): ?StreamedResponse
    {
        $this->guard('kesiswaan.siswa.cetak');

        $siswas = $this->buildQuery()->get();

        if ($siswas->isEmpty()) {
            session()->flash('error', 'Tidak ada data siswa yang sesuai dengan filter yang dipilih.');
            return null;
        }

        $filterLabel = $this->buildFilterLabel();
        $tahunPelajaran = $this->tahunPelajaran ?: '2026-2027';
        $jumlahKolom = max(1, min(50, $this->jumlahKolom));

        $school = \App\Modules\Sistem\Models\School::current();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Hadir');

        // Orientasi Kertas Portrait (sama seperti PDF)
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $lastColIndex = 6 + $jumlahKolom; // jumlah kolom
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIndex);

        $startRow = 1;

        // Kop Surat Image
        if (!empty($school->kop_surat_path) && file_exists(public_path('storage/' . $school->kop_surat_path))) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Kop Surat');
            $drawing->setDescription('Kop Surat');
            $drawing->setPath(public_path('storage/' . $school->kop_surat_path));
            $drawing->setCoordinates('A1');
            $drawing->setHeight(80);
            $drawing->setWorksheet($sheet);
            // Supaya image ada space
            $sheet->getRowDimension(1)->setRowHeight(60);
            $startRow = 3;
        } else {
            $sheet->setCellValue('A1', 'YAYASAN PRAWITAMA');
            $sheet->setCellValue('A2', $school->nama ?? 'SMK WIKRAMA 1 GARUT');
            $sheet->mergeCells("A1:{$lastColLetter}1");
            $sheet->mergeCells("A2:{$lastColLetter}2");
            $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $startRow = 4;
        }

        // Header Dokumen
        $sheet->setCellValue("A{$startRow}", 'DAFTAR HADIR MURID');
        $sheet->setCellValue("A" . ($startRow+1), 'SMKS WIKRAMA 1 GARUT');
        $sheet->setCellValue("A" . ($startRow+2), 'TAHUN PELAJARAN ' . $tahunPelajaran);

        $sheet->mergeCells("A{$startRow}:{$lastColLetter}{$startRow}");
        $sheet->mergeCells("A" . ($startRow+1) . ":{$lastColLetter}" . ($startRow+1));
        $sheet->mergeCells("A" . ($startRow+2) . ":{$lastColLetter}" . ($startRow+2));

        $sheet->getStyle("A{$startRow}:A" . ($startRow+2))->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("A{$startRow}:A" . ($startRow+2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Info Metadata (Times New Roman 11)
        $metaRow = $startRow + 4;
        $sheet->setCellValue("A{$metaRow}", 'Kriteria / Kelas: ' . $filterLabel);
        $sheet->setCellValue("F{$metaRow}", 'Mata Pelajaran: ' . ($this->mapel ?: '..........................................'));
        
        $sheet->getStyle("A{$metaRow}:F{$metaRow}")->getFont()->setName('Times New Roman')->setSize(11);

        // Table Header
        $tableStartRow = $metaRow + 2;
        $sheet->setCellValue("A{$tableStartRow}", 'No');
        $sheet->setCellValue("B{$tableStartRow}", 'NIS');
        $sheet->setCellValue("C{$tableStartRow}", 'Nama Siswa');
        $sheet->setCellValue("D{$tableStartRow}", 'JK');
        $sheet->setCellValue("E{$tableStartRow}", 'Prog');
        $sheet->setCellValue("F{$tableStartRow}", 'Rayon');

        $sheet->mergeCells("A{$tableStartRow}:A" . ($tableStartRow + 1));
        $sheet->mergeCells("B{$tableStartRow}:B" . ($tableStartRow + 1));
        $sheet->mergeCells("C{$tableStartRow}:C" . ($tableStartRow + 1));
        $sheet->mergeCells("D{$tableStartRow}:D" . ($tableStartRow + 1));
        $sheet->mergeCells("E{$tableStartRow}:E" . ($tableStartRow + 1));
        $sheet->mergeCells("F{$tableStartRow}:F" . ($tableStartRow + 1));

        // Tanggal / Pertemuan Header
        $dayStartLetter = 'G';
        $dayEndLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(6 + $jumlahKolom);
        $sheet->setCellValue("G{$tableStartRow}", '........................................................');
        $sheet->mergeCells("G{$tableStartRow}:{$dayEndLetter}{$tableStartRow}");

        for ($d = 1; $d <= $jumlahKolom; $d++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(6 + $d);
            $sheet->setCellValue("{$colLetter}" . ($tableStartRow + 1), '');
            $sheet->getColumnDimension($colLetter)->setWidth(2.5); // Sangat sempit
        }

        // Header Styling
        $headerRange = "A{$tableStartRow}:{$dayEndLetter}" . ($tableStartRow + 1);
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0F4F8');

        // Font Tahoma untuk kolom No, NIS, Nama, JK, Prog, Rayon
        $sheet->getStyle("A{$tableStartRow}:F" . ($tableStartRow + 1))->getFont()->setName('Tahoma');

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(4); // Lebar kolom No
        $sheet->getColumnDimension('B')->setWidth(14); // Lebar kolom NIS
        $sheet->getColumnDimension('C')->setWidth(30); // Lebar kolom Nama Siswa
        $sheet->getColumnDimension('D')->setWidth(4); // Lebar kolom JK
        $sheet->getColumnDimension('E')->setWidth(5); // Lebar kolom Prog
        $sheet->getColumnDimension('F')->setWidth(10); // Lebar kolom Rayon

        // Rows Data Siswa
        $currentRow = $tableStartRow + 2;
        foreach ($siswas as $idx => $s) {
            $progNama = strtolower($s->program?->nama ?? '');
            $progCode = str_contains($progNama, 'unggul') ? 'U' : (str_contains($progNama, 'regul') ? 'R' : ($s->program ? substr($s->program->nama, 0, 1) : '-'));

            $sheet->setCellValue("A{$currentRow}", $idx + 1);
            $sheet->setCellValueExplicit("B{$currentRow}", $s->nipd ?: $s->nisn, DataType::TYPE_STRING);
            $sheet->setCellValue("C{$currentRow}", $s->nama);
            $sheet->setCellValue("D{$currentRow}", $s->jk);
            $sheet->setCellValue("E{$currentRow}", $progCode);
            $sheet->setCellValue("F{$currentRow}", $s->rayon?->nama ?? '-');

            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$currentRow}:F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Set font Tahoma and normal weight for data cells
            $sheet->getStyle("A{$currentRow}:F{$currentRow}")->getFont()->setName('Tahoma')->setBold(false);

            $currentRow++;
        }

        // Apply grid borders
        $tableRange = "A{$tableStartRow}:{$lastColLetter}" . ($currentRow - 1);
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($tableRange)->getFont()->setSize(9);

        // Teks Jumlah Siswa (Kiri Bawah tabel) Times New Roman 11 (1 enter di atasnya karena $currentRow kosong)
        $sheet->setCellValue("A" . ($currentRow + 1), "Jumlah: " . $siswas->count() . " Siswa (L: " . $siswas->where('jk', 'L')->count() . ", P: " . $siswas->where('jk', 'P')->count() . ")");
        $sheet->setCellValue("A" . ($currentRow + 2), "* Program: U = Unggulan, R = Reguler");
        $sheet->getStyle("A" . ($currentRow + 1))->getFont()->setName('Times New Roman')->setSize(11);
        $sheet->getStyle("A" . ($currentRow + 2))->getFont()->setSize(9);

        // Signature section (Kanan Bawah tabel) Times New Roman 11, jangan sejajar
        $sigRow = $currentRow + 5; 
        $sigColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(max(1, $lastColIndex - 8));
        
        $sheet->setCellValue("{$sigColLetter}{$sigRow}", 'Garut, ........................... (' . date('Y') . ')');
        $sheet->setCellValue("{$sigColLetter}" . ($sigRow + 1), 'Guru Mata Pelajaran,');
        $sheet->setCellValue("{$sigColLetter}" . ($sigRow + 4), $this->namaGuru ?: '( ................................................ )');
        $sheet->setCellValue("{$sigColLetter}" . ($sigRow + 5), 'NIP. ' . ($this->nipGuru ?: '..........................................'));
        
        $sheet->getStyle("{$sigColLetter}{$sigRow}:{$sigColLetter}" . ($sigRow + 5))->getFont()->setName('Times New Roman')->setSize(11);
        // Bold for signature name
        $sheet->getStyle("{$sigColLetter}" . ($sigRow + 4))->getFont()->setBold(true);

        $safeLabel = Str::slug($filterLabel);
        $filename = "Daftar_Hadir_Murid_{$safeLabel}_" . date('Ymd_His') . ".xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function render()
    {
        $siswas = $this->buildQuery()->get();
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama')->get();
        $rayons = Rayon::orderBy('nama')->get();
        $jurusans = Jurusan::where('is_active', true)->orderBy('nama')->get();
        $programs = Program::where('is_active', true)->orderBy('nama')->get();

        return view('kesiswaan::livewire.cetak-absensi', [
            'siswas' => $siswas,
            'rombels' => $rombels,
            'rayons' => $rayons,
            'jurusans' => $jurusans,
            'programs' => $programs,
            'filterLabel' => $this->buildFilterLabel(),
            'sortLabel' => $this->getSortLabel(),
        ]);
    }
}
