<?php

namespace App\Modules\Kesiswaan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kesiswaan\Services\PemetaanAlamatService;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Pemetaan Alamat Siswa')]
class PemetaanAlamatSiswa extends Component
{
    use GuardsPermission, WithPagination;

    // Filter & Search
    public string $filterCategory = 'semua_anomali'; // 'semua_anomali', 'kabupaten_tidak_sinkron', 'teks_kotor', 'data_kosong', 'luar_negeri', 'sudah_valid', 'semua'

    public ?int $filterRombel = null;

    public string $search = '';

    public int $perPage = 25;

    // Selection
    public array $selectedSiswaIds = [];

    public bool $selectAll = false;

    // Manual Modal State
    public bool $showManualModal = false;

    public ?int $manualSiswaId = null;

    public string $manualNamaSiswa = '';

    public string $manualNegara = 'Indonesia';

    public string $manualProvinsi = '';

    public string $manualKabupaten = '';

    public string $manualKecamatan = '';

    public string $manualKelurahan = '';

    public string $manualAlamat = '';

    public string $manualRt = '';

    public string $manualRw = '';

    public string $manualDusun = '';

    public string $manualKodePos = '';

    public function mount(): void
    {
        $this->guard('kesiswaan.siswa.edit');
    }

    public function updatedFilterCategory(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedFilterRombel(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $analysisList = $this->getAnalyzedItems();
            $this->selectedSiswaIds = $analysisList
                ->where('is_anomali', true)
                ->pluck('siswa.id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedSiswaIds = [];
        }
    }

    public function applySingle(int $siswaId): void
    {
        $this->guard('kesiswaan.siswa.edit');

        $siswa = Siswa::find($siswaId);
        if (! $siswa) {
            session()->flash('error', 'Data siswa tidak ditemukan.');

            return;
        }

        $service = new PemetaanAlamatService;
        $success = $service->applySuggestion($siswa);

        if ($success) {
            $this->selectedSiswaIds = array_values(array_filter(
                $this->selectedSiswaIds,
                fn ($id) => (int) $id !== $siswaId
            ));
            session()->flash('status', "Alamat untuk {$siswa->nama} berhasil diperbarui sesuai Master Kemendagri.");
        }
    }

    public function applySelected(): void
    {
        $this->guard('kesiswaan.siswa.edit');

        if (empty($this->selectedSiswaIds)) {
            session()->flash('error', 'Pilih minimal satu siswa untuk diperbarui.');

            return;
        }

        $service = new PemetaanAlamatService;
        $count = $service->applyBatchSuggestions($this->selectedSiswaIds);

        $this->reset(['selectedSiswaIds', 'selectAll']);
        session()->flash('status', "Berhasil! {$count} data alamat siswa telah diperbarui.");
    }

    public function applyAllConfident(): void
    {
        $this->guard('kesiswaan.siswa.edit');

        $analyzed = $this->getAnalyzedItems();
        $targetIds = $analyzed
            ->where('is_anomali', true)
            ->where('confidence', 'pasti')
            ->pluck('siswa.id')
            ->toArray();

        if (empty($targetIds)) {
            session()->flash('error', 'Tidak ada data anomali dengan tingkat keyakinan pasti.');

            return;
        }

        $service = new PemetaanAlamatService;
        $count = $service->applyBatchSuggestions($targetIds);

        $this->reset(['selectedSiswaIds', 'selectAll']);
        session()->flash('status', "Berhasil! {$count} alamat siswa dengan status pasti berhasil diperbarui.");
    }

    public function openManualModal(int $siswaId): void
    {
        $this->guard('kesiswaan.siswa.edit');

        $siswa = Siswa::find($siswaId);
        if (! $siswa) {
            return;
        }

        $this->manualSiswaId = $siswa->id;
        $this->manualNamaSiswa = $siswa->nama;
        $this->manualNegara = $siswa->negara ?: 'Indonesia';
        $this->manualProvinsi = $siswa->provinsi ?? '';
        $this->manualKabupaten = $siswa->kabupaten_kota ?? '';
        $this->manualKecamatan = $siswa->kecamatan ?? '';
        $this->manualKelurahan = $siswa->kelurahan ?? '';
        $this->manualAlamat = $siswa->alamat ?? '';
        $this->manualRt = $siswa->rt ?? '';
        $this->manualRw = $siswa->rw ?? '';
        $this->manualDusun = $siswa->dusun ?? '';
        $this->manualKodePos = $siswa->kode_pos ?? '';

        $this->showManualModal = true;
    }

    public function updatedManualProvinsi(): void
    {
        $this->manualKabupaten = '';
        $this->manualKecamatan = '';
        $this->manualKelurahan = '';
    }

    public function updatedManualKabupaten(): void
    {
        $this->manualKecamatan = '';
        $this->manualKelurahan = '';
    }

    public function updatedManualKecamatan(): void
    {
        $this->manualKelurahan = '';
    }

    public function closeManualModal(): void
    {
        $this->reset([
            'showManualModal',
            'manualSiswaId',
            'manualNamaSiswa',
            'manualProvinsi',
            'manualKabupaten',
            'manualKecamatan',
            'manualKelurahan',
            'manualAlamat',
            'manualRt',
            'manualRw',
            'manualDusun',
            'manualKodePos',
        ]);
    }

    public function saveManual(): void
    {
        $this->guard('kesiswaan.siswa.edit');

        $siswa = Siswa::find($this->manualSiswaId);
        if (! $siswa) {
            $this->closeManualModal();

            return;
        }

        $siswa->update([
            'negara' => $this->manualNegara ?: 'Indonesia',
            'provinsi' => $this->manualProvinsi ?: null,
            'kabupaten_kota' => $this->manualKabupaten ?: null,
            'kecamatan' => $this->manualKecamatan ?: null,
            'kelurahan' => $this->manualKelurahan ?: null,
            'alamat' => $this->manualAlamat ?: null,
            'rt' => $this->manualRt ?: null,
            'rw' => $this->manualRw ?: null,
            'dusun' => $this->manualDusun ?: null,
            'kode_pos' => $this->manualKodePos ?: null,
        ]);

        $this->closeManualModal();
        session()->flash('status', "Alamat untuk {$siswa->nama} berhasil disimpan.");
    }

    protected function getAnalyzedItems()
    {
        $query = Siswa::query()->with('rombel');

        if ($this->filterRombel) {
            $query->where('rombel_id', $this->filterRombel);
        }

        if ($this->search) {
            $kw = trim($this->search);
            $query->where(function ($q) use ($kw) {
                $q->where('nama', 'like', "%{$kw}%")
                    ->orWhere('nipd', 'like', "%{$kw}%")
                    ->orWhere('nisn', 'like', "%{$kw}%")
                    ->orWhere('kelurahan', 'like', "%{$kw}%")
                    ->orWhere('kecamatan', 'like', "%{$kw}%")
                    ->orWhere('kabupaten_kota', 'like', "%{$kw}%")
                    ->orWhere('alamat', 'like', "%{$kw}%");
            });
        }

        $siswas = $query->orderBy('nama')->get();
        $service = new PemetaanAlamatService;

        return $siswas->map(function ($s) use ($service) {
            $analysis = $service->analyzeSiswa($s);
            $analysis['siswa'] = $s;

            return $analysis;
        });
    }

    public function render()
    {
        $service = new PemetaanAlamatService;
        $analyzedAll = $this->getAnalyzedItems();

        // Hitung statistik
        $stats = [
            'total' => $analyzedAll->count(),
            'anomali' => $analyzedAll->where('is_anomali', true)->count(),
            'mismatch' => $analyzedAll->where('status', 'kabupaten_tidak_sinkron')->count(),
            'dirty' => $analyzedAll->where('status', 'teks_kotor')->count(),
            'empty' => $analyzedAll->where('status', 'data_kosong')->count(),
            'intl' => $analyzedAll->where('status', 'luar_negeri')->count(),
            'valid' => $analyzedAll->where('status', 'sudah_valid')->count(),
            'pasti' => $analyzedAll->where('is_anomali', true)->where('confidence', 'pasti')->count(),
        ];

        // Saring berdasarkan filter kategori
        $filtered = $analyzedAll;
        if ($this->filterCategory === 'semua_anomali') {
            $filtered = $filtered->where('is_anomali', true);
        } elseif ($this->filterCategory !== 'semua') {
            $filtered = $filtered->where('status', $this->filterCategory);
        }

        // Paginasi manual untuk koleksi yang sudah dianalisis
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $paginatedItems = new LengthAwarePaginator(
            $filtered->forPage($currentPage, $this->perPage)->values(),
            $filtered->count(),
            $this->perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        $rombels = Rombel::orderBy('tingkat')->orderBy('nama')->get();

        // Cascading untuk manual edit modal
        $provincesList = [];
        $kabupatenList = [];
        $kecamatanList = [];
        $desaList = [];

        if ($this->showManualModal) {
            $provincesList = $service->getProvincesList();
            $kabupatenList = $service->getKabupatenList($this->manualProvinsi ?: null);
            $kecamatanList = $service->getKecamatanList($this->manualKabupaten ?: null, $this->manualProvinsi ?: null);
            $desaList = $service->getDesaList($this->manualKecamatan ?: null, $this->manualKabupaten ?: null);
        }

        return view('kesiswaan::livewire.pemetaan-alamat-siswa', [
            'items' => $paginatedItems,
            'stats' => $stats,
            'rombels' => $rombels,
            'provincesList' => $provincesList,
            'kabupatenList' => $kabupatenList,
            'kecamatanList' => $kecamatanList,
            'desaList' => $desaList,
        ]);
    }
}
