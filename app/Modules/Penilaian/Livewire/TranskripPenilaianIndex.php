<?php

namespace App\Modules\Penilaian\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Services\TranskripNilaiExporter;
use App\Modules\Penilaian\Services\TranskripNilaiService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('components.layouts.app')]
#[Title('Transkrip Siswa Tahunan')]
class TranskripPenilaianIndex extends Component
{
    use GuardsPermission;

    #[Url(as: 'tab')]
    public string $activeTab = 'matriks'; // 'matriks' | 'individual' | 'analisis'

    #[Url(as: 'ta')]
    public ?string $selectedTaId = null; // 'semua' | numeric string

    #[Url(as: 'jurusan')]
    public ?int $selectedJurusanId = null;

    #[Url(as: 'tingkat')]
    public string $selectedTingkat = 'XII'; // 'semua' | 'X' | 'XI' | 'XII'

    #[Url(as: 'rombel')]
    public ?int $selectedRombelId = null;

    #[Url(as: 'siswa')]
    public ?int $selectedSiswaId = null;

    public string $search = '';
    public string $searchSiswa = '';
    public bool $hasLoaded = false;

    public function mount(): void
    {
        $this->guard('penilaian.lihat');

        // Jika selectedTaId belum diisi dari URL:
        if ($this->selectedTaId === null) {
            // Jika ada rombel_id di URL, gunakan TA dari rombel tsb agar konsisten
            if ($this->selectedRombelId) {
                $targetRombel = Rombel::find($this->selectedRombelId);
                if ($targetRombel && $targetRombel->tahun_ajaran_id) {
                    $this->selectedTaId = (string) $targetRombel->tahun_ajaran_id;
                }
            }
            // Default ke Tahun Ajaran aktif
            if ($this->selectedTaId === null) {
                $currentTa = AcademicContext::currentTahunAjaran();
                $this->selectedTaId = $currentTa ? (string) $currentTa->id : 'semua';
            }
        }

        // Biarkan selectedJurusanId dan siswa null di awal agar halaman dimuat super cepat (< 30ms)
        $this->hasLoaded = false;
    }

    public function tampilkanData(): void
    {
        if (in_array($this->activeTab, ['matriks', 'analisis']) && ! $this->selectedJurusanId) {
            session()->flash('error', 'Silakan pilih Kompetensi Keahlian (Jurusan) terlebih dahulu.');
            return;
        }

        $this->hasLoaded = true;
    }

    public function updatedSelectedTaId(): void
    {
        $this->hasLoaded = false;
        $this->selectedRombelId = null;
    }

    public function updatedSelectedJurusanId(): void
    {
        $this->hasLoaded = false;
        $this->selectedRombelId = null;
    }

    public function updatedSelectedTingkat(): void
    {
        $this->hasLoaded = false;
        $this->selectedRombelId = null;
    }

    public function updatedSelectedRombelId(): void
    {
        $this->hasLoaded = false;
    }

    public function switchTab(string $tab): void
    {
        if (in_array($tab, ['matriks', 'individual', 'analisis'])) {
            $this->activeTab = $tab;
            $this->hasLoaded = false; // Reset per keputusan grill-me
        }
    }

    public function selectSiswa(int $siswaId): void
    {
        $siswa = Siswa::find($siswaId);
        if ($siswa) {
            $this->selectedSiswaId = $siswa->id;
            $this->activeTab = 'individual';
            $this->hasLoaded = true; // Langsung tampilkan transkrip individual per kesepakatan grill-me
            $this->searchSiswa = '';

            // Jika siswa berada di luar rombel filter aktif saat ini, sesuaikan rombel filter
            if ($this->selectedRombelId && $this->selectedRombelId !== $siswa->rombel_id) {
                $this->selectedRombelId = $siswa->rombel_id;
            }
        }
    }

    public function clearSearchSiswa(): void
    {
        $this->searchSiswa = '';
    }

    protected function ensureSelectedSiswa(): void
    {
        if (!$this->selectedJurusanId) {
            return;
        }

        $rombelQuery = Rombel::where('jurusan_id', $this->selectedJurusanId);
        if ($this->selectedTaId !== 'semua' && $this->selectedTaId) {
            $rombelQuery->where('tahun_ajaran_id', (int) $this->selectedTaId);
        }
        if ($this->selectedTingkat !== 'semua') {
            $rombelQuery->where('tingkat', $this->selectedTingkat);
        }
        if ($this->selectedRombelId) {
            $rombelQuery->where('id', $this->selectedRombelId);
        }
        $rombelIds = $rombelQuery->pluck('id')->toArray();

        $firstSiswa = Siswa::whereIn('rombel_id', $rombelIds)
            ->where('is_active', true)
            ->orderBy('nama')
            ->first();

        if ($firstSiswa) {
            // Periksa apakah siswa saat ini masih valid di rombel terpilih
            if ($this->selectedSiswaId) {
                $exists = Siswa::where('id', $this->selectedSiswaId)
                    ->whereIn('rombel_id', $rombelIds)
                    ->exists();
                if (!$exists) {
                    $this->selectedSiswaId = $firstSiswa->id;
                }
            } else {
                $this->selectedSiswaId = $firstSiswa->id;
            }
        }
    }

    /**
     * Download Excel Matriks Transkrip Angkatan 3 Tahun.
     */
    public function exportExcel(TranskripNilaiExporter $exporter): StreamedResponse
    {
        $this->guard('penilaian.lihat');

        $jurusan = Jurusan::findOrFail($this->selectedJurusanId);
        $taId = ($this->selectedTaId !== 'semua' && $this->selectedTaId) ? (int) $this->selectedTaId : null;

        $spreadsheet = $exporter->exportCohortTranskrip(
            $this->selectedJurusanId,
            $this->selectedTingkat,
            $this->selectedRombelId,
            $this->search,
            $taId
        );

        $tingkatSlug = $this->selectedTingkat === 'semua' ? 'Semua-Tingkat' : 'Kelas-' . $this->selectedTingkat;
        $taSlug = $this->selectedTaId && $this->selectedTaId !== 'semua' ? '_TA-' . $this->selectedTaId : '';
        $filename = 'Transkrip_3_Tahun_' . $jurusan->kode . '_' . $tingkatSlug . $taSlug . '_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function render(TranskripNilaiService $service)
    {
        $tahunAjarans = TahunAjaran::orderByDesc('tgl_mulai')->get();
        $jurusans = Jurusan::where('is_active', true)->orderBy('nama')->get();

        $rombelQuery = Rombel::with('tahunAjaran');
        if ($this->selectedJurusanId) {
            $rombelQuery->where('jurusan_id', $this->selectedJurusanId);
        }
        if ($this->selectedTaId !== 'semua' && $this->selectedTaId) {
            $rombelQuery->where('tahun_ajaran_id', (int) $this->selectedTaId);
        }
        if ($this->selectedTingkat !== 'semua') {
            $rombelQuery->where('tingkat', $this->selectedTingkat);
        }
        $rombels = $rombelQuery->orderBy('tingkat')->orderBy('nama')->get();

        // Data Siswa untuk selector & instant Alpine.js search di tab individual
        $rombelIds = $rombels->pluck('id')->toArray();
        $siswasQuery = Siswa::with('rombel.tahunAjaran')
            ->whereIn('rombel_id', $rombelIds)
            ->where('is_active', true);

        if (!empty($this->searchSiswa)) {
            $searchKey = trim($this->searchSiswa);
            $siswasQuery->where(function ($q) use ($searchKey) {
                $q->where('nama', 'like', "%{$searchKey}%")
                  ->orWhere('nipd', 'like', "%{$searchKey}%")
                  ->orWhere('nisn', 'like', "%{$searchKey}%");
            });
        }

        $siswasList = $siswasQuery->orderBy('nama')->get();

        // Seluruh siswa dalam scope rombel ini untuk pencarian instan 0ms di browser via Alpine.js
        $allScopeSiswas = Siswa::with('rombel.tahunAjaran')
            ->whereIn('rombel_id', $rombelIds)
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $siswasJson = $allScopeSiswas->map(function ($s) {
            return [
                'id' => $s->id,
                'nama' => $s->nama,
                'nipd' => $s->nipd ?? '',
                'nisn' => $s->nisn ?? '',
                'rombel' => $s->rombel?->nama ?? '-',
                'ta' => $s->rombel?->tahunAjaran?->nama ?? '',
            ];
        })->values()->toArray();

        $selectedSiswaModel = $this->selectedSiswaId 
            ? Siswa::with('rombel.tahunAjaran')->find($this->selectedSiswaId) 
            : null;

        // Data Matriks Cohort (Hanya dihitung jika hasLoaded dan tab aktif 'matriks')
        $cohortData = null;
        if ($this->hasLoaded && $this->activeTab === 'matriks' && $this->selectedJurusanId) {
            $cohortData = $service->getCohortTranskripData(
                $this->selectedJurusanId,
                $this->selectedTingkat,
                $this->selectedRombelId,
                $this->search,
                ($this->selectedTaId !== 'semua' && $this->selectedTaId) ? (int) $this->selectedTaId : null
            );
        }

        // Data Individual (Hanya dihitung jika hasLoaded dan tab aktif 'individual')
        $individualData = null;
        if ($this->hasLoaded && $this->activeTab === 'individual' && $this->selectedSiswaId) {
            $individualData = $service->getSiswaTranskripData($this->selectedSiswaId);
        }

        // Data Analisis Mapel (Hanya dihitung jika hasLoaded dan tab aktif 'analisis')
        $mapelComparison = null;
        if ($this->hasLoaded && $this->activeTab === 'analisis' && $this->selectedJurusanId) {
            $mapelComparison = $service->getMapelComparisonData(
                $this->selectedJurusanId,
                $this->selectedTingkat
            );
        }

        return view('penilaian::livewire.transkrip-penilaian-index', [
            'tahunAjarans' => $tahunAjarans,
            'jurusans' => $jurusans,
            'rombels' => $rombels,
            'siswasList' => $siswasList,
            'siswasJson' => $siswasJson,
            'selectedSiswaModel' => $selectedSiswaModel,
            'cohortData' => $cohortData,
            'individualData' => $individualData,
            'mapelComparison' => $mapelComparison,
            'hasLoaded' => $this->hasLoaded,
        ]);
    }
}
