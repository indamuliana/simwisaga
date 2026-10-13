<?php

namespace App\Modules\Penilaian\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Penilaian\Services\RekapNilaiExporter;
use App\Modules\Penilaian\Services\RekapPenilaianService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('components.layouts.app')]
#[Title('Rekap Nilai Semester')]
class RekapPenilaianIndex extends Component
{
    use GuardsPermission;

    #[Url(as: 'tab')]
    public string $activeSubTab = 'leger'; // 'leger' | 'mapel' | 'komparasi' | 'rayon'

    public ?int $selectedTaId = null;
    public ?int $selectedSemesterId = null;
    public string $filterTingkat = 'semua'; // 'semua', 'X', 'XI', 'XII'
    public ?int $selectedRombelId = null;
    public ?int $selectedMapelId = null;
    public ?int $selectedRayonId = null;
    public string $search = '';

    public function mount(): void
    {
        $currentTa = AcademicContext::currentTahunAjaran();
        $currentSem = AcademicContext::currentSemester();

        $this->selectedTaId = $currentTa?->id;
        $this->selectedSemesterId = $currentSem?->id;

        $user = auth()->user();

        // Cari rombel awal
        $rombelQuery = Rombel::query();
        if ($this->selectedTaId) {
            $rombelQuery->where('tahun_ajaran_id', $this->selectedTaId);
        }

        // Jika user adalah Guru (bukan superadmin), utamakan rombel yang dia ampu atau dia sebagai wali kelas
        if ($user && !$user->isSuperAdmin()) {
            $pegawai = Pegawai::where('user_id', $user->id)->first();
            $myRombelId = null;

            if ($pegawai && $this->selectedSemesterId) {
                $myRombelId = PenugasanMengajar::where('pegawai_id', $pegawai->id)
                    ->where('semester_id', $this->selectedSemesterId)
                    ->value('rombel_id');
            }

            if (!$myRombelId) {
                $myRombelId = Rombel::where('wali_kelas_id', $user->id)
                    ->where('tahun_ajaran_id', $this->selectedTaId)
                    ->value('id');
            }

            if ($myRombelId) {
                $this->selectedRombelId = $myRombelId;
            }
        }

        if (!$this->selectedRombelId) {
            $firstRombel = (clone $rombelQuery)->orderBy('tingkat')->orderBy('nama')->first();
            $this->selectedRombelId = $firstRombel?->id;
        }
    }

    public function updatedFilterTingkat(): void
    {
        $rombelQuery = Rombel::where('tahun_ajaran_id', $this->selectedTaId);
        if ($this->filterTingkat !== 'semua') {
            $rombelQuery->where('tingkat', $this->filterTingkat);
        }

        $available = (clone $rombelQuery)->pluck('id')->toArray();
        if (!in_array($this->selectedRombelId, $available)) {
            $first = (clone $rombelQuery)->orderBy('tingkat')->orderBy('nama')->first();
            $this->selectedRombelId = $first?->id;
        }
        $this->selectedMapelId = null;
    }

    public function updatedSelectedTaId(): void
    {
        // Sesuaikan semester ke semester pada TA terpilih
        $sem = Semester::where('tahun_ajaran_id', $this->selectedTaId)->orderBy('semester')->first();
        $this->selectedSemesterId = $sem?->id;

        $firstRombel = Rombel::where('tahun_ajaran_id', $this->selectedTaId)->orderBy('tingkat')->orderBy('nama')->first();
        $this->selectedRombelId = $firstRombel?->id;
        $this->selectedMapelId = null;
    }

    public function updatedSelectedRombelId(): void
    {
        $this->selectedMapelId = null;
    }

    public function switchSubTab(string $tab): void
    {
        if (in_array($tab, ['leger', 'mapel', 'komparasi', 'rayon'])) {
            $this->activeSubTab = $tab;
        }
    }

    public function toggleRayonDetail(?int $rayonId): void
    {
        $this->selectedRayonId = ($this->selectedRayonId === $rayonId) ? null : $rayonId;
    }

    public function exportLegerExcel(): ?StreamedResponse
    {
        $rombel = Rombel::find($this->selectedRombelId);
        $semester = Semester::find($this->selectedSemesterId);

        if (!$rombel || !$semester) {
            session()->flash('error', 'Rombel atau Semester belum dipilih.');
            return null;
        }

        $exporter = app(RekapNilaiExporter::class);
        $spreadsheet = $exporter->exportLegerRombel($rombel, $semester);
        $fileName = 'Leger_Nilai_' . str_replace(' ', '_', $rombel->nama) . '_' . ucfirst($semester->semester) . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportAnalisisExcel(): ?StreamedResponse
    {
        $rombel = Rombel::find($this->selectedRombelId);
        $semester = Semester::find($this->selectedSemesterId);

        if (!$rombel || !$semester) {
            session()->flash('error', 'Rombel atau Semester belum dipilih.');
            return null;
        }

        $exporter = app(RekapNilaiExporter::class);
        $spreadsheet = $exporter->exportAnalisisMapel($rombel, $semester);
        $fileName = 'Analisis_Ketuntasan_' . str_replace(' ', '_', $rombel->nama) . '_' . ucfirst($semester->semester) . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function render(RekapPenilaianService $service)
    {
        $tahunAjarans = TahunAjaran::orderBy('nama', 'desc')->get();
        $semesters = Semester::where('tahun_ajaran_id', $this->selectedTaId)->orderBy('semester')->get();

        $rombelQuery = Rombel::where('tahun_ajaran_id', $this->selectedTaId);
        if ($this->filterTingkat !== 'semua') {
            $rombelQuery->where('tingkat', $this->filterTingkat);
        }
        $rombels = $rombelQuery->orderBy('tingkat')->orderBy('nama')->get();

        $selectedRombel = $this->selectedRombelId ? Rombel::with(['jurusan', 'waliKelas', 'tahunAjaran'])->find($this->selectedRombelId) : null;
        $selectedSemester = $this->selectedSemesterId ? Semester::find($this->selectedSemesterId) : null;

        $legerData = null;
        $crossRombelData = collect();
        $crossRayonData = collect();

        if ($selectedSemester) {
            $crossRayonData = $service->getCrossRayonComparison($this->filterTingkat, $selectedSemester);
        }

        if ($selectedRombel && $selectedSemester) {
            $legerData = $service->getLegerRombelData($selectedRombel, $selectedSemester);

            // Jika belum ada mapel terpilih untuk Tab 2, pilih mapel pertama
            if (!$this->selectedMapelId && !empty($legerData['mapel_list'])) {
                $this->selectedMapelId = array_key_first($legerData['mapel_list']);
            }

            // Terapkan filter pencarian siswa jika ada
            if (trim($this->search) !== '') {
                $kw = strtolower(trim($this->search));
                $legerData['siswa_rows'] = array_filter($legerData['siswa_rows'], function ($s) use ($kw) {
                    return str_contains(strtolower($s['nama']), $kw)
                        || str_contains(strtolower($s['nis']), $kw)
                        || str_contains(strtolower($s['nisn']), $kw);
                });
            }

            // Data komparasi antar rombel
            $crossRombelData = $service->getCrossRombelComparison($this->filterTingkat, $selectedSemester);
        }

        return view('penilaian::livewire.rekap-penilaian-index', [
            'tahunAjarans' => $tahunAjarans,
            'semesters' => $semesters,
            'rombels' => $rombels,
            'selectedRombel' => $selectedRombel,
            'selectedSemester' => $selectedSemester,
            'legerData' => $legerData,
            'crossRombelData' => $crossRombelData,
            'crossRayonData' => $crossRayonData,
            'selectedRayonId' => $this->selectedRayonId,
            'activeSubTab' => $this->activeSubTab,
            'selectedMapelId' => $this->selectedMapelId,
            'filterTingkat' => $this->filterTingkat,
            'search' => $this->search,
        ]);
    }
}
