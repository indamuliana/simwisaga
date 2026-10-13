<?php

namespace App\Modules\Ekstrakurikuler\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Modules\Ekstrakurikuler\Models\Ekstrakurikuler;
use App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota;
use App\Modules\Ekstrakurikuler\Services\EkskulExcelService;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\Rombel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class EkstrakurikulerRekap extends Component
{
    use WithPagination;

    public $semester_id;
    public $filterEkskul = '';
    public $filterRombel = '';
    public $filterPredikat = 'all';
    public $search = '';

    protected $excelService;

    public function boot(EkskulExcelService $excelService)
    {
        $this->excelService = $excelService;
    }

    public function mount()
    {
        $activeSemester = Semester::where('is_active', true)->first();
        $this->semester_id = $activeSemester?->id ?? Semester::latest('id')->first()?->id;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterEkskul()
    {
        $this->resetPage();
    }

    public function updatingFilterRombel()
    {
        $this->resetPage();
    }

    public function updatingFilterPredikat()
    {
        $this->resetPage();
    }

    public function exportExcel()
    {
        $query = EkstrakurikulerAnggota::with(['ekstrakurikuler', 'siswa.rombel', 'semester.tahunAjaran'])
            ->where('semester_id', $this->semester_id)
            ->where('status_keanggotaan', 'aktif');

        if ($this->filterEkskul) {
            $query->where('ekstrakurikuler_id', $this->filterEkskul);
        }
        if ($this->filterRombel) {
            $query->whereHas('siswa', fn($q) => $q->where('rombel_id', $this->filterRombel));
        }
        if ($this->search) {
            $query->whereHas('siswa', fn($q) => $q->where('nama', 'like', '%' . $this->search . '%'));
        }

        $items = $query->get();
        $semester = Semester::with('tahunAjaran')->find($this->semester_id);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Nilai Ekskul');

        // Header Title
        $sheet->setCellValue('A1', 'REKAPITULASI PENILAIAN EKSTRAKURIKULER');
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $semName = $semester ? ucfirst($semester->semester) . ' ' . $semester->tahunAjaran->nama : '';
        $sheet->setCellValue('A2', "Semester: {$semName} | Tanggal Unduh: " . date('d/m/Y H:i'));
        $sheet->mergeCells('A2:L2');
        $sheet->getStyle('A2')->getFont()->setSize(10);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = [
            'No', 'NISN', 'Nama Siswa', 'Kelas', 'Ekstrakurikuler',
            'Kehadiran (%)', 'STS 1', 'SAS', 'STS 2', 'SAT',
            'Nilai Akhir', 'Predikat', 'Deskripsi Capaian'
        ];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];

        foreach ($headers as $idx => $title) {
            $sheet->setCellValue($cols[$idx] . '4', $title);
        }

        $sheet->getStyle('A4:M4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $row = 5;
        $no = 1;
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValueExplicit('B' . $row, (string) ($item->siswa?->nisn ?? '-'), DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $row, $item->siswa?->nama ?? '-');
            $sheet->setCellValue('D' . $row, $item->siswa?->rombel?->nama ?? '-');
            $sheet->setCellValue('E' . $row, $item->ekstrakurikuler?->nama ?? '-');
            $sheet->setCellValue('F' . $row, $item->persentase_kehadiran . '%');
            $sheet->setCellValue('G' . $row, $item->sts_1 ?? '-');
            $sheet->setCellValue('H' . $row, $item->sas ?? '-');
            $sheet->setCellValue('I' . $row, $item->sts_2 ?? '-');
            $sheet->setCellValue('J' . $row, $item->sat ?? '-');
            $sheet->setCellValue('K' . $row, $item->nilai_angka ?? '-');
            $sheet->setCellValue('L' . $row, $item->nilai_huruf ?? '-');
            $sheet->setCellValue('M' . $row, $item->deskripsi ?? '-');

            $row++;
        }

        $lastRow = max(5, $row - 1);
        $sheet->getStyle('A5:M' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ]);

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Rekapitulasi_Ekskul_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $semesters = Semester::with('tahunAjaran')->orderByDesc('id')->get();
        $ekskuls = Ekstrakurikuler::where('is_active', true)->orderByDesc('is_wajib')->orderBy('nama')->get();
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama')->get();

        $query = EkstrakurikulerAnggota::with(['ekstrakurikuler', 'siswa.rombel'])
            ->where('semester_id', $this->semester_id)
            ->where('status_keanggotaan', 'aktif');

        if ($this->filterEkskul) {
            $query->where('ekstrakurikuler_id', $this->filterEkskul);
        }

        if ($this->filterRombel) {
            $query->whereHas('siswa', fn($q) => $q->where('rombel_id', $this->filterRombel));
        }

        if ($this->filterPredikat !== 'all') {
            if ($this->filterPredikat === 'A') {
                $query->where(fn($q) => $q->where('nilai_huruf', 'Sangat Baik')->orWhere('nilai_huruf', 'A'));
            } elseif ($this->filterPredikat === 'B') {
                $query->where(fn($q) => $q->where('nilai_huruf', 'Baik')->orWhere('nilai_huruf', 'B'));
            } elseif ($this->filterPredikat === 'C') {
                $query->where(fn($q) => $q->where('nilai_huruf', 'Cukup')->orWhere('nilai_huruf', 'C'));
            } elseif ($this->filterPredikat === 'D') {
                $query->where(fn($q) => $q->where('nilai_huruf', 'Kurang')->orWhere('nilai_huruf', 'D'));
            }
        }

        if ($this->search) {
            $query->whereHas('siswa', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('nisn', 'like', '%' . $this->search . '%');
            });
        }

        // Hitung statistik
        $allMembers = (clone $query)->get();
        $totalMembers = $allMembers->count();
        $avgScore = $allMembers->whereNotNull('nilai_angka')->avg('nilai_angka');
        $avgAttendance = $allMembers->avg('persentase_kehadiran');

        $countA = $allMembers->filter(fn($m) => $m->nilai_huruf === 'Sangat Baik' || $m->nilai_huruf === 'A')->count();
        $countB = $allMembers->filter(fn($m) => $m->nilai_huruf === 'Baik' || $m->nilai_huruf === 'B')->count();
        $countC = $allMembers->filter(fn($m) => $m->nilai_huruf === 'Cukup' || $m->nilai_huruf === 'C')->count();
        $countD = $allMembers->filter(fn($m) => $m->nilai_huruf === 'Kurang' || $m->nilai_huruf === 'D')->count();

        $rekapList = $query->paginate(20);

        return view('ekstrakurikuler::livewire.rekap', [
            'rekapList' => $rekapList,
            'semesters' => $semesters,
            'ekskuls' => $ekskuls,
            'rombels' => $rombels,
            'totalMembers' => $totalMembers,
            'avgScore' => $avgScore ? round($avgScore, 1) : '-',
            'avgAttendance' => $avgAttendance ? round($avgAttendance, 1) : 0,
            'countA' => $countA,
            'countB' => $countB,
            'countC' => $countC,
            'countD' => $countD,
        ]);
    }
}
