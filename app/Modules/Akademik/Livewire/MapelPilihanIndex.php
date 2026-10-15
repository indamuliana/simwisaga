<?php

namespace App\Modules\Akademik\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\MapelPilihan;
use App\Modules\Akademik\Models\MapelPilihanAnggota;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Services\MapelPilihanExcelService;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('components.layouts.app')]
#[Title('Mata Pelajaran Pilihan (Mapil)')]
class MapelPilihanIndex extends Component
{
    use GuardsPermission, WithPagination, WithFileUploads;

    // Navigation Tabs: 'mapil' (Master Mapil) | 'anggota' (Kelola Anggota)
    public string $activeTab = 'mapil';

    // Tab 1: Master Mapil
    public string $searchMapil = '';
    public string $filterTingkat = '';
    public string $filterStatus = '';
    public ?int $filterTahunAjaran = null;

    // Form Master Mapil Modal
    public bool $showMapilModal = false;
    public ?int $mapilId = null;
    public string $kode = '';
    public string $nama = '';
    public array $tingkats = ['XI'];
    public int $kkm_default = 75;
    public int $jp = 2;
    public ?int $guru_id = null;
    public ?int $guru_pendamping_id = null;
    public string $deskripsi = '';
    public bool $is_active = true;

    // Tab 2: Kelola Anggota
    public ?int $selectedMapilId = null;
    public string $searchAnggota = '';
    public ?int $filterAnggotaRombel = null;
    public ?int $filterAnggotaRayon = null;

    // Modal Tambah Anggota Massal
    public bool $showAddMemberModal = false;
    public string $searchCandidate = '';
    public ?int $candidateRombel = null;
    public ?int $candidateRayon = null;
    public string $candidateTingkat = '';
    public array $selectedCandidateIds = [];

    // Excel Import
    public bool $showImportModal = false;
    public $importFile = null;

    protected function rules(): array
    {
        return [
            'kode' => ['required', 'string', 'max:30'],
            'nama' => ['required', 'string', 'max:150'],
            'tingkats' => ['required', 'array', 'min:1'],
            'kkm_default' => ['required', 'integer', 'min:0', 'max:100'],
            'jp' => ['required', 'integer', 'min:1', 'max:20'],
            'guru_id' => ['nullable', 'integer'],
            'guru_pendamping_id' => ['nullable', 'integer'],
            'deskripsi' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function mount(): void
    {
        $this->filterTahunAjaran = AcademicContext::currentTahunAjaran()?->id;
    }

    public function switchTab(string $tab, ?int $mapilId = null): void
    {
        $this->activeTab = $tab;
        if ($mapilId) {
            $this->selectedMapilId = $mapilId;
        }
        $this->resetPage();
    }

    public function openCreateMapil(): void
    {
        $this->guard('akademik.mapil.tambah');
        $this->reset([
            'mapilId', 'kode', 'nama', 'guru_id', 'guru_pendamping_id', 'deskripsi'
        ]);
        $this->tingkats = ['XI'];
        $this->kkm_default = 75;
        $this->jp = 2;
        $this->is_active = true;
        $this->showMapilModal = true;
    }

    public function editMapil(int $id): void
    {
        $this->guard('akademik.mapil.edit');
        $mapil = MapelPilihan::findOrFail($id);
        $this->mapilId = $mapil->id;
        $this->kode = $mapil->kode;
        $this->nama = $mapil->nama;
        $this->tingkats = (array) ($mapil->tingkats ?: ['XI']);
        $this->kkm_default = $mapil->kkm_default;
        $this->jp = $mapil->jp;
        $this->guru_id = $mapil->guru_id;
        $this->guru_pendamping_id = $mapil->guru_pendamping_id;
        $this->deskripsi = $mapil->deskripsi ?? '';
        $this->is_active = (bool) $mapil->is_active;
        $this->showMapilModal = true;
    }

    public function saveMapil(): void
    {
        if ($this->mapilId) {
            $this->guard('akademik.mapil.edit');
        } else {
            $this->guard('akademik.mapil.tambah');
        }

        $this->validate();

        $data = [
            'tahun_ajaran_id' => $this->filterTahunAjaran,
            'kode' => strtoupper(trim($this->kode)),
            'nama' => trim($this->nama),
            'tingkats' => $this->tingkats,
            'kkm_default' => $this->kkm_default,
            'jp' => $this->jp,
            'guru_id' => $this->guru_id ?: null,
            'guru_pendamping_id' => $this->guru_pendamping_id ?: null,
            'deskripsi' => $this->deskripsi ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->mapilId) {
            $mapil = MapelPilihan::findOrFail($this->mapilId);
            $mapil->update($data);
            session()->flash('status', "Mapel Pilihan '{$mapil->nama}' berhasil diperbarui.");
        } else {
            $mapil = MapelPilihan::create($data);
            session()->flash('status', "Mapel Pilihan baru '{$mapil->nama}' berhasil ditambahkan.");
        }

        $this->showMapilModal = false;
    }

    public function toggleActiveMapil(int $id): void
    {
        $this->guard('akademik.mapil.edit');
        $mapil = MapelPilihan::findOrFail($id);
        $mapil->is_active = ! $mapil->is_active;
        $mapil->save();

        session()->flash('status', "Status Mapil '{$mapil->nama}' diperbarui.");
    }

    public function deleteMapil(int $id): void
    {
        $this->guard('akademik.mapil.hapus');
        $mapil = MapelPilihan::findOrFail($id);
        $nama = $mapil->nama;
        $mapil->delete();

        if ($this->selectedMapilId === $id) {
            $this->selectedMapilId = null;
        }

        session()->flash('status', "Mapel Pilihan '{$nama}' berhasil dihapus.");
    }

    // --- Manajemen Anggota Siswa ---
    public function selectMapilForAnggota(int $id): void
    {
        $this->selectedMapilId = $id;
        $this->resetPage();
    }

    public function removeMember(int $anggotaId): void
    {
        $this->guard('akademik.mapil.edit');
        $anggota = MapelPilihanAnggota::findOrFail($anggotaId);
        $anggota->delete();

        session()->flash('status', "Siswa berhasil dikeluarkan dari keanggotaan Mapil.");
    }

    public function openAddMemberModal(): void
    {
        $this->guard('akademik.mapil.edit');
        $this->reset(['selectedCandidateIds', 'searchCandidate', 'candidateRombel', 'candidateRayon']);
        
        $mapil = MapelPilihan::find($this->selectedMapilId);
        if ($mapil && !empty($mapil->tingkats)) {
            $this->candidateTingkat = $mapil->tingkats[0] ?? '';
        }

        $this->showAddMemberModal = true;
    }

    public function addSelectedCandidates(): void
    {
        $this->guard('akademik.mapil.edit');

        if (empty($this->selectedCandidateIds) || ! $this->selectedMapilId) {
            session()->flash('error', 'Pilih minimal satu siswa untuk ditambahkan.');
            return;
        }

        $mapil = MapelPilihan::findOrFail($this->selectedMapilId);
        $added = 0;

        foreach ($this->selectedCandidateIds as $siswaId) {
            $exists = MapelPilihanAnggota::where('mapel_pilihan_id', $mapil->id)
                ->where('siswa_id', $siswaId)
                ->exists();

            if (! $exists) {
                MapelPilihanAnggota::create([
                    'mapel_pilihan_id' => $mapil->id,
                    'siswa_id' => $siswaId,
                    'tahun_ajaran_id' => $mapil->tahun_ajaran_id,
                    'status' => 'aktif',
                ]);
                $added++;
            }
        }

        $this->showAddMemberModal = false;
        $this->selectedCandidateIds = [];
        session()->flash('status', "Berhasil menambahkan {$added} siswa ke dalam Mapel Pilihan '{$mapil->nama}'.");
    }

    // --- Excel Import & Export ---
    public function downloadTemplate(): StreamedResponse
    {
        $this->guard('akademik.mapil.lihat');
        $mapil = MapelPilihan::findOrFail($this->selectedMapilId);
        $service = app(MapelPilihanExcelService::class);
        $spreadsheet = $service->downloadTemplate($mapil);

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'Template_Import_Anggota_' . $mapil->kode . '.xlsx');
    }

    public function exportMembers(): StreamedResponse
    {
        $this->guard('akademik.mapil.cetak');
        $mapil = MapelPilihan::findOrFail($this->selectedMapilId);
        $service = app(MapelPilihanExcelService::class);
        $spreadsheet = $service->exportMembers($mapil);

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'Daftar_Anggota_' . $mapil->kode . '.xlsx');
    }

    public function importMembers(): void
    {
        $this->guard('akademik.mapil.edit');
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $mapil = MapelPilihan::findOrFail($this->selectedMapilId);
        $service = app(MapelPilihanExcelService::class);
        $res = $service->importMembers($mapil, $this->importFile);

        $this->showImportModal = false;
        $this->importFile = null;

        session()->flash('status', "Import berhasil! {$res['success']} siswa ditambahkan, {$res['skipped']} data dilewati.");
    }

    public function render()
    {
        // 1. Data Tab Master Mapil
        $mapils = MapelPilihan::with(['guru', 'guruPendamping'])
            ->withCount('anggotas')
            ->when($this->searchMapil, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama', 'like', "%{$this->searchMapil}%")
                        ->orWhere('kode', 'like', "%{$this->searchMapil}%");
                });
            })
            ->when($this->filterTingkat, function ($q) {
                $q->whereJsonContains('tingkats', $this->filterTingkat);
            })
            ->when($this->filterStatus !== '', function ($q) {
                $q->where('is_active', $this->filterStatus === 'aktif');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        // 2. Data Tab Anggota
        $currentMapil = $this->selectedMapilId ? MapelPilihan::with(['guru', 'guruPendamping'])->find($this->selectedMapilId) : null;
        $anggotaQuery = $currentMapil ? $currentMapil->anggotas()->with(['siswa.rombel', 'siswa.rayon']) : null;

        if ($anggotaQuery) {
            $anggotaQuery->when($this->searchAnggota, function ($q) {
                $q->whereHas('siswa', function ($sub) {
                    $sub->where('nama', 'like', "%{$this->searchAnggota}%")
                        ->orWhere('nisn', 'like', "%{$this->searchAnggota}%")
                        ->orWhere('nipd', 'like', "%{$this->searchAnggota}%");
                });
            })
            ->when($this->filterAnggotaRombel, function ($q) {
                $q->whereHas('siswa', fn ($s) => $s->where('rombel_id', $this->filterAnggotaRombel));
            })
            ->when($this->filterAnggotaRayon, function ($q) {
                $q->whereHas('siswa', fn ($s) => $s->where('rayon_id', $this->filterAnggotaRayon));
            });
        }

        $anggotas = $anggotaQuery ? $anggotaQuery->paginate(15) : null;

        // 3. Data Candidate Siswa for Modal
        $candidates = collect();
        if ($this->showAddMemberModal && $currentMapil) {
            $existingSiswaIds = $currentMapil->anggotas()->pluck('siswa_id')->toArray();

            $candidates = Siswa::with(['rombel', 'rayon'])
                ->whereNotIn('id', $existingSiswaIds)
                ->when($this->candidateTingkat, function ($q) {
                    $q->whereHas('rombel', fn ($r) => $r->where('tingkat', $this->candidateTingkat));
                })
                ->when($this->candidateRombel, fn ($q) => $q->where('rombel_id', $this->candidateRombel))
                ->when($this->candidateRayon, fn ($q) => $q->where('rayon_id', $this->candidateRayon))
                ->when($this->searchCandidate, function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('nama', 'like', "%{$this->searchCandidate}%")
                            ->orWhere('nisn', 'like', "%{$this->searchCandidate}%")
                            ->orWhere('nipd', 'like', "%{$this->searchCandidate}%");
                    });
                })
                ->orderBy('nama')
                ->limit(60)
                ->get();
        }

        $rombels = Rombel::orderBy('tingkat')->orderBy('nama')->get();
        $rayons = Rayon::orderBy('nama')->get();
        $gurus = Pegawai::where('is_active', true)->orderBy('nama_lengkap')->get();
        $tahunAjarans = TahunAjaran::orderBy('id', 'desc')->get();

        return view('akademik::livewire.mapel-pilihan-index', [
            'mapils' => $mapils,
            'currentMapil' => $currentMapil,
            'anggotas' => $anggotas,
            'candidates' => $candidates,
            'rombels' => $rombels,
            'rayons' => $rayons,
            'gurus' => $gurus,
            'tahunAjarans' => $tahunAjarans,
        ]);
    }
}
