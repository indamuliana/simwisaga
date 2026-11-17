<?php

namespace App\Modules\AnalitikSpmb\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\AnalitikSpmb\Models\SpmbMasterSmp;
use App\Modules\AnalitikSpmb\Services\SpmbWilayahExporter;
use App\Modules\AnalitikSpmb\Services\SpmbWilayahService;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Layout('components.layouts.app')]
#[Title('Sebaran Wilayah Asal Siswa &bull; SPMB')]
class WilayahIndex extends Component
{
    use GuardsPermission, WithPagination;

    public string $activeTab = 'analitik'; // Deprecated / redirected if 'perbaikan'

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $filterCakupan = 'semua'; // 'semua', 'dalam_negeri', 'luar_negeri'

    #[Url(history: true)]
    public string $filterProvinsi = '';

    #[Url(history: true)]
    public string $filterKabupaten = '';

    #[Url(history: true)]
    public string $filterTingkat = '';

    #[Url(history: true)]
    public string $filterJurusanId = '';

    #[Url(history: true)]
    public string $filterTahunAjaranId = '';

    #[Url(history: true)]
    public string $filterJk = '';

    #[Url(history: true)]
    public string $filterStatus = 'aktif';

    public int $perPage = 25;

    // Toggle Tampilan Tabel Top 10 Feeder Kab / Kota (Default: false / tertutup)
    public bool $showTopKabupaten = false;

    // Node Tree Wilayah yang sedang di-expand / buka
    public ?array $expandedNodes = null;

    // Slide-over Drawer Siswa
    public bool $showDrawer = false;

    public string $drawerWilayahTitle = '';

    public array $drawerCriteria = [];

    public string $drawerSearch = '';

    public string $drawerFilterTingkat = '';

    public string $drawerFilterJurusan = '';

    public string $drawerFilterJk = '';

    // Modal Edit Alamat Siswa Perorangan (dari Drawer)
    public bool $showEditAddressModal = false;

    public ?int $editingAddressSiswaId = null;

    public string $editAddressAlamat = '';

    public string $editAddressRt = '';

    public string $editAddressRw = '';

    public string $editAddressDusun = '';

    public string $editAddressKelurahan = '';

    public string $editAddressKecamatan = '';

    public string $editAddressKabupaten = '';

    public string $editAddressProvinsi = '';

    public string $editAddressNegara = 'Indonesia';

    public string $editAddressKodePos = '';

    public bool $isManualAddressProvinsi = false;

    public bool $isManualAddressKabupaten = false;

    public bool $isManualAddressKecamatan = false;

    public bool $isManualAddressKelurahan = false;

    public array $drawerSiswaIds = [];

    public function mount()
    {
        $this->guard('analitik_spmb.wilayah.lihat');

        if (request()->query('activeTab') === 'perbaikan') {
            return redirect()->route('kesiswaan.pemetaan-alamat');
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        if (! empty($this->search)) {
            $this->expandAll();
        }
    }

    public function updatedFilterCakupan(): void
    {
        $this->filterProvinsi = '';
        $this->filterKabupaten = '';
        $this->resetPage();
    }

    public function updatedFilterProvinsi(): void
    {
        $this->filterKabupaten = '';
        $this->resetPage();
    }

    public function updatedFilterKabupaten(): void
    {
        $this->resetPage();
    }

    public function updatedFilterTingkat(): void
    {
        $this->resetPage();
    }

    public function updatedFilterJurusanId(): void
    {
        $this->resetPage();
    }

    public function updatedFilterTahunAjaranId(): void
    {
        $this->resetPage();
    }

    public function updatedFilterJk(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'filterCakupan',
            'filterProvinsi',
            'filterKabupaten',
            'filterTingkat',
            'filterJurusanId',
            'filterTahunAjaranId',
            'filterJk',
        ]);
        $this->filterStatus = 'aktif';
        $this->expandedNodes = null;
        $this->resetPage();
    }

    /**
     * Buka / Tutup simpul (node) pada pohon hirarki
     */
    public function toggleNode(string $key): void
    {
        if ($this->expandedNodes === null) {
            $service = app(SpmbWilayahService::class);
            $analytics = $service->getWilayahAnalytics(array_merge(get_object_vars($this), [
                'search' => $this->search,
            ]));
            $this->expandedNodes = $this->getDefaultExpandedKeys($analytics['tree'] ?? []);
        }

        if (isset($this->expandedNodes[$key])) {
            unset($this->expandedNodes[$key]);
        } else {
            $this->expandedNodes[$key] = true;
        }
    }

    /**
     * Buka seluruh tingkatan cabang pohon hirarki
     */
    public function expandAll(): void
    {
        $service = app(SpmbWilayahService::class);
        $analytics = $service->getWilayahAnalytics(array_merge(get_object_vars($this), [
            'search' => $this->search,
        ]));

        $keys = [];
        $collectKeys = function ($nodes) use (&$collectKeys, &$keys) {
            foreach ($nodes as $node) {
                $keys[$node['key']] = true;
                if (! empty($node['children'])) {
                    $collectKeys($node['children']);
                }
            }
        };
        $collectKeys($analytics['tree'] ?? []);
        $this->expandedNodes = $keys;
    }

    /**
     * Tutup seluruh tingkatan cabang pohon hirarki
     */
    public function collapseAll(): void
    {
        $this->expandedNodes = [];
    }

    /**
     * Buka / Tutup tampilan leaderboard Top 10 Kabupaten / Kota Feeder
     */
    public function toggleTopKabupaten(): void
    {
        $this->showTopKabupaten = ! $this->showTopKabupaten;
    }

    /**
     * Kunci default yang terbuka (Negara, Provinsi, dan Kab/Kota)
     */
    protected function getDefaultExpandedKeys(array $tree): array
    {
        $keys = [];
        foreach ($tree as $negKey => $neg) {
            $keys[$negKey] = true;
            foreach ($neg['children'] as $provKey => $prov) {
                $keys[$provKey] = true;
                foreach ($prov['children'] as $kabKey => $kab) {
                    $keys[$kabKey] = true;
                }
            }
        }

        return $keys;
    }

    /**
     * Jalankan Auto-Detect Wilayah dari Master SMP & Sekolah Asal
     */
    public function autoDetectWilayah(): void
    {
        $this->guard('analitik_spmb.wilayah.kelola');

        $service = app(SpmbWilayahService::class);
        $res = $service->autoDetectWilayahFromMaster();

        session()->flash('success', "Auto-Detect Wilayah berhasil! {$res['updated_domestic']} siswa lokal distandarisasi dan {$res['updated_international']} siswa luar negeri (Sabah/Malaysia) berhasil diidentifikasi.");
    }

    /**
     * Buka Drawer Slide-over Siswa per Wilayah (Tingkat Negara hingga Desa/Kelurahan)
     */
    public function openDrawer(
        string $negara,
        ?string $provinsi = null,
        ?string $kabupaten = null,
        ?string $kecamatan = null,
        ?string $kelurahan = null
    ): void {
        $this->drawerCriteria = array_filter([
            'negara' => $negara,
            'provinsi' => $provinsi,
            'kabupaten_kota' => $kabupaten,
            'kecamatan' => $kecamatan,
            'kelurahan' => $kelurahan,
        ], fn ($v) => ! is_null($v) && $v !== '');

        $titleParts = array_filter([$kelurahan, $kecamatan, $kabupaten, $provinsi, $negara], fn ($v) => $v && $v !== '[Belum Diset]');
        $this->drawerWilayahTitle = implode(' &bull; ', $titleParts) ?: 'Wilayah Tidak Terdefinisi';
        $this->drawerSearch = '';
        $this->drawerFilterTingkat = '';
        $this->drawerFilterJurusan = '';
        $this->drawerFilterJk = '';
        $this->showDrawer = true;
    }

    public function closeDrawer(): void
    {
        $this->showDrawer = false;
        $this->drawerCriteria = [];
        $this->drawerWilayahTitle = '';
    }

    /**
     * Buka Modal Edit Alamat Siswa Perorangan dari Drawer
     */
    public function openEditAddress(int $siswaId): void
    {
        $this->guard('analitik_spmb.wilayah.kelola');

        $siswa = Siswa::findOrFail($siswaId);
        $this->editingAddressSiswaId = $siswa->id;
        $this->editAddressAlamat = $siswa->alamat ?? '';
        $this->editAddressRt = $siswa->rt ?? '';
        $this->editAddressRw = $siswa->rw ?? '';
        $this->editAddressDusun = $siswa->dusun ?? '';
        $this->editAddressKelurahan = preg_replace('/^(desa\/kel\.|desa|kelurahan|kel\.)\s+/i', '', trim($siswa->kelurahan ?? ''));
        $this->editAddressKecamatan = preg_replace('/^kec\.?\s+/i', '', trim($siswa->kecamatan ?? ''));
        $this->editAddressKabupaten = $siswa->kabupaten_kota ?? '';
        $this->editAddressProvinsi = $siswa->provinsi ?? '';
        $this->editAddressNegara = $siswa->negara ?: 'Indonesia';
        $this->editAddressKodePos = $siswa->kode_pos ?? '';

        $service = app(SpmbWilayahService::class);
        $provinces = $service->getProvincesList();
        $this->isManualAddressProvinsi = ! empty($this->editAddressProvinsi) && ! in_array($this->editAddressProvinsi, $provinces, true);

        $kabupatens = $service->getKabupatenList($this->editAddressProvinsi ?: null);
        $this->isManualAddressKabupaten = ! empty($this->editAddressKabupaten) && ! in_array($this->editAddressKabupaten, $kabupatens, true);

        $kecamatans = $service->getKecamatanList($this->editAddressKabupaten ?: null, $this->editAddressProvinsi ?: null);
        $this->isManualAddressKecamatan = ! empty($this->editAddressKecamatan) && ! in_array($this->editAddressKecamatan, $kecamatans, true);

        $desas = $service->getDesaList($this->editAddressKecamatan ?: null, $this->editAddressKabupaten ?: null);
        $this->isManualAddressKelurahan = ! empty($this->editAddressKelurahan) && ! in_array($this->editAddressKelurahan, $desas, true);

        $this->showEditAddressModal = true;
    }

    public function updatedEditAddressProvinsi($val): void
    {
        if (! $this->isManualAddressProvinsi) {
            $this->editAddressKabupaten = '';
            $this->editAddressKecamatan = '';
            $this->editAddressKelurahan = '';
        }
    }

    public function updatedEditAddressKabupaten($val): void
    {
        if (! $this->isManualAddressKabupaten) {
            $this->editAddressKecamatan = '';
            $this->editAddressKelurahan = '';

            // Auto upward fill province jika provinsi kosong
            if (empty($this->editAddressProvinsi) && ! empty($val)) {
                $master = SpmbMasterSmp::where('kabupaten', $val)->whereNotNull('provinsi')->first();
                if ($master && $master->provinsi) {
                    $this->editAddressProvinsi = $master->provinsi;
                }
            }
        }
    }

    public function updatedEditAddressKecamatan($val): void
    {
        if (! $this->isManualAddressKecamatan) {
            $cleanVal = preg_replace('/^kec\.?\s+/i', '', trim($val));
            if ($cleanVal !== $val) {
                $this->editAddressKecamatan = $cleanVal;
            }
            $this->editAddressKelurahan = '';
        } else {
            $cleanVal = trim($val);
        }

        // Smart upward auto-fill dari kecamatan
        if (! empty($cleanVal)) {
            $service = app(SpmbWilayahService::class);
            $resolved = $service->resolveLocationFromKecamatan($cleanVal);
            if ($resolved) {
                if (empty($this->editAddressKabupaten) || ! $this->isManualAddressKabupaten) {
                    $this->editAddressKabupaten = $resolved['kabupaten'];
                }
                if (empty($this->editAddressProvinsi) || ! $this->isManualAddressProvinsi) {
                    $this->editAddressProvinsi = $resolved['provinsi'];
                }
                if (empty($this->editAddressNegara)) {
                    $this->editAddressNegara = $resolved['negara'];
                }
            }
        }
    }

    public function updatedEditAddressKelurahan($val): void
    {
        if (! $this->isManualAddressKelurahan) {
            $cleanVal = preg_replace('/^(desa\/kel\.|desa|kelurahan|kel\.)\s+/i', '', trim($val));
            if ($cleanVal !== $val) {
                $this->editAddressKelurahan = $cleanVal;
            }
        }
    }

    /**
     * Salin nilai wilayah dari kriteria drawer saat ini ke form edit alamat siswa
     */
    public function copyDrawerWilayahToAddress(): void
    {
        $crit = $this->drawerCriteria;

        if (! empty($crit['negara']) && $crit['negara'] !== '[Belum Diset]') {
            $this->editAddressNegara = $crit['negara'];
        }
        if (! empty($crit['provinsi']) && $crit['provinsi'] !== '[Belum Diset]') {
            $this->editAddressProvinsi = $crit['provinsi'];
            $this->isManualAddressProvinsi = false;
        }
        if (! empty($crit['kabupaten_kota']) && $crit['kabupaten_kota'] !== '[Belum Diset]') {
            $this->editAddressKabupaten = $crit['kabupaten_kota'];
            $this->isManualAddressKabupaten = false;
        }
        if (! empty($crit['kecamatan']) && $crit['kecamatan'] !== '[Belum Diset]') {
            $this->editAddressKecamatan = preg_replace('/^kec\.?\s+/i', '', trim($crit['kecamatan']));
            $this->isManualAddressKecamatan = false;
        }
        if (! empty($crit['kelurahan']) && $crit['kelurahan'] !== '[Belum Diset]') {
            $this->editAddressKelurahan = preg_replace('/^(desa\/kel\.|desa|kelurahan|kel\.)\s+/i', '', trim($crit['kelurahan']));
            $this->isManualAddressKelurahan = false;
        }
    }

    /**
     * Simpan Perubahan Alamat Siswa (opsional: lanjut ke siswa berikutnya)
     */
    public function saveStudentAddress(bool $andNext = false): void
    {
        $this->guard('analitik_spmb.wilayah.kelola');

        if (! $this->editingAddressSiswaId) {
            return;
        }

        $this->validate([
            'editAddressAlamat' => 'nullable|string|max:255',
            'editAddressRt' => 'nullable|string|max:10',
            'editAddressRw' => 'nullable|string|max:10',
            'editAddressDusun' => 'nullable|string|max:100',
            'editAddressKelurahan' => 'nullable|string|max:100',
            'editAddressKecamatan' => 'nullable|string|max:100',
            'editAddressKabupaten' => 'nullable|string|max:100',
            'editAddressProvinsi' => 'nullable|string|max:100',
            'editAddressNegara' => 'required|string|max:100',
            'editAddressKodePos' => 'nullable|string|max:10',
        ]);

        $siswa = Siswa::findOrFail($this->editingAddressSiswaId);
        $siswa->update([
            'alamat' => trim($this->editAddressAlamat) ?: null,
            'rt' => trim($this->editAddressRt) ?: null,
            'rw' => trim($this->editAddressRw) ?: null,
            'dusun' => trim($this->editAddressDusun) ?: null,
            'kelurahan' => trim($this->editAddressKelurahan) ?: null,
            'kecamatan' => trim($this->editAddressKecamatan) ?: null,
            'kabupaten_kota' => trim($this->editAddressKabupaten) ?: null,
            'provinsi' => trim($this->editAddressProvinsi) ?: null,
            'negara' => trim($this->editAddressNegara) ?: 'Indonesia',
            'kode_pos' => trim($this->editAddressKodePos) ?: null,
        ]);

        $savedName = $siswa->nama;

        if ($andNext) {
            $ids = ! empty($this->drawerSiswaIds) ? $this->drawerSiswaIds : $this->buildDrawerQuery()->pluck('siswas.id')->map(fn ($id) => (int) $id)->toArray();
            $currentIndex = array_search((int) $this->editingAddressSiswaId, array_map('intval', $ids));
            if ($currentIndex !== false && isset($ids[$currentIndex + 1])) {
                $nextId = (int) $ids[$currentIndex + 1];
                $this->openEditAddress($nextId);
                session()->flash('success', "Alamat {$savedName} disimpan. Membuka siswa berikutnya.");

                return;
            }
        }

        $this->closeEditAddressModal();
        session()->flash('success', "Alamat siswa {$savedName} berhasil diperbarui.");
    }

    public function closeEditAddressModal(): void
    {
        $this->showEditAddressModal = false;
        $this->editingAddressSiswaId = null;
    }

    public function navigateStudentAddress(int $direction): void
    {
        if (! $this->editingAddressSiswaId) {
            return;
        }

        $ids = ! empty($this->drawerSiswaIds) ? $this->drawerSiswaIds : $this->buildDrawerQuery()->pluck('siswas.id')->map(fn ($id) => (int) $id)->toArray();
        $currentIndex = array_search((int) $this->editingAddressSiswaId, array_map('intval', $ids));
        if ($currentIndex !== false) {
            $targetIndex = $currentIndex + $direction;
            if (isset($ids[$targetIndex])) {
                $this->openEditAddress((int) $ids[$targetIndex]);
            }
        }
    }

    /**
     * Unduh Rekap Excel Sebaran Wilayah
     */
    public function exportExcelRekap()
    {
        $this->guard('analitik_spmb.wilayah.ekspor');

        $service = app(SpmbWilayahService::class);
        $analytics = $service->getWilayahAnalytics(array_merge(get_object_vars($this), [
            'search' => $this->search,
        ]));

        $exporter = app(SpmbWilayahExporter::class);
        $spreadsheet = $exporter->exportRekap(
            $analytics['items'],
            $analytics['jurusans'],
            $analytics
        );

        $timestamp = now()->format('Ymd_His');

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, "Rekap_Sebaran_Wilayah_Siswa_{$timestamp}.xlsx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Unduh Excel Daftar Siswa dari Drawer
     */
    public function exportExcelDrawer()
    {
        $this->guard('analitik_spmb.wilayah.ekspor');

        if (empty($this->drawerCriteria)) {
            return;
        }

        $sQuery = $this->buildDrawerQuery();
        $siswas = $sQuery->get();

        $exporter = app(SpmbWilayahExporter::class);
        $spreadsheet = $exporter->exportDrawer($siswas, $this->drawerWilayahTitle);

        $safeTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', $this->drawerWilayahTitle);
        $timestamp = now()->format('Ymd_His');

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, "Siswa_Wilayah_{$safeTitle}_{$timestamp}.xlsx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    protected function buildDrawerQuery()
    {
        $crit = $this->drawerCriteria;
        $sQuery = Siswa::with(['rombel.jurusan'])
            ->leftJoin('rombels', 'siswas.rombel_id', '=', 'rombels.id');

        if (($crit['negara'] ?? '') === 'Indonesia') {
            $sQuery->where(function ($q) {
                $q->whereNull('siswas.negara')
                    ->orWhere('siswas.negara', 'Indonesia');
            });
        } elseif (! empty($crit['negara']) && $crit['negara'] !== '[Belum Diset]') {
            $sQuery->where('siswas.negara', $crit['negara']);
        }

        if (($crit['provinsi'] ?? '') === '[Belum Diset]') {
            $sQuery->where(function ($q) {
                $q->whereNull('siswas.provinsi')->orWhere('siswas.provinsi', '');
            });
        } elseif (! empty($crit['provinsi'])) {
            $sQuery->where('siswas.provinsi', $crit['provinsi']);
        }

        if (($crit['kabupaten_kota'] ?? '') === '[Belum Diset]') {
            $sQuery->where(function ($q) {
                $q->whereNull('siswas.kabupaten_kota')->orWhere('siswas.kabupaten_kota', '');
            });
        } elseif (! empty($crit['kabupaten_kota'])) {
            $sQuery->where('siswas.kabupaten_kota', $crit['kabupaten_kota']);
        }

        if (($crit['kecamatan'] ?? '') === '[Belum Diset]') {
            $sQuery->where(function ($q) {
                $q->whereNull('siswas.kecamatan')->orWhere('siswas.kecamatan', '');
            });
        } elseif (! empty($crit['kecamatan'])) {
            $sQuery->where('siswas.kecamatan', $crit['kecamatan']);
        }

        if (($crit['kelurahan'] ?? '') === '[Belum Diset]') {
            $sQuery->where(function ($q) {
                $q->whereNull('siswas.kelurahan')->orWhere('siswas.kelurahan', '');
            });
        } elseif (! empty($crit['kelurahan'])) {
            $sQuery->where('siswas.kelurahan', $crit['kelurahan']);
        }

        // Filter status utama
        if ($this->filterStatus === 'aktif') {
            $sQuery->where('siswas.is_active', true);
        } elseif ($this->filterStatus !== 'semua' && ! empty($this->filterStatus)) {
            $sQuery->where('siswas.status', $this->filterStatus);
        }

        // Filter Tingkat & Jurusan
        $actTingkat = $this->drawerFilterTingkat ?: $this->filterTingkat;
        if ($actTingkat) {
            $sQuery->where('rombels.tingkat', $actTingkat);
        }

        $actJurusan = $this->drawerFilterJurusan ?: $this->filterJurusanId;
        if ($actJurusan) {
            $sQuery->where('rombels.jurusan_id', $actJurusan);
        }

        $actJk = $this->drawerFilterJk ?: $this->filterJk;
        if ($actJk) {
            $sQuery->where('siswas.jk', $actJk);
        }

        if ($this->drawerSearch) {
            $ds = '%'.trim($this->drawerSearch).'%';
            $sQuery->where(function ($q) use ($ds) {
                $q->where('siswas.nama', 'like', $ds)
                    ->orWhere('siswas.nisn', 'like', $ds)
                    ->orWhere('siswas.nipd', 'like', $ds)
                    ->orWhere('siswas.alamat', 'like', $ds);
            });
        }

        return $sQuery->select('siswas.*')->orderBy('siswas.nama');
    }

    public function render()
    {
        $this->guard('analitik_spmb.wilayah.lihat');

        $wilayahService = app(SpmbWilayahService::class);
        $tahunAjarans = TahunAjaran::orderByDesc('id')->get();

        // Data Analitik
        $analytics = $wilayahService->getWilayahAnalytics(array_merge(get_object_vars($this), [
            'search' => $this->search,
        ]));
        $jurusans = $analytics['jurusans'];

        // Inisialisasi status buka/tutup node tree default jika belum di-set
        if ($this->expandedNodes === null) {
            $this->expandedNodes = $this->getDefaultExpandedKeys($analytics['tree'] ?? []);
        }

        // Manual pagination untuk tabel analitik
        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginatedItems = new LengthAwarePaginator(
            $analytics['items']->forPage($page, $this->perPage)->values(),
            $analytics['items']->count(),
            $this->perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        // Drawer Data Siswa
        $drawerSiswas = collect();
        if ($this->showDrawer && ! empty($this->drawerCriteria)) {
            $drawerSiswas = $this->buildDrawerQuery()->get();
            $this->drawerSiswaIds = $drawerSiswas->pluck('id')->toArray();
        }

        // Data Siswa Sedang Diedit Alamatnya
        $editingAddressSiswa = null;
        $editingAddressIndex = 0;
        if ($this->showEditAddressModal && $this->editingAddressSiswaId) {
            $editingAddressSiswa = Siswa::with('rombel.jurusan')->find($this->editingAddressSiswaId);
            $idx = array_search($this->editingAddressSiswaId, $this->drawerSiswaIds);
            $editingAddressIndex = ($idx !== false) ? ($idx + 1) : 1;
        }

        // Pilihan Dropdown Filter
        $provinsiOptions = Siswa::distinct()->whereNotNull('provinsi')->where('provinsi', '!=', '')->orderBy('provinsi')->pluck('provinsi');
        $kabupatenOptions = Siswa::query()
            ->when($this->filterProvinsi, fn ($q) => $q->where('provinsi', $this->filterProvinsi))
            ->distinct()
            ->whereNotNull('kabupaten_kota')
            ->where('kabupaten_kota', '!=', '')
            ->orderBy('kabupaten_kota')
            ->pluck('kabupaten_kota');

        // Pilihan Master SMP untuk saran edit modal
        $kabupatenMasterList = SpmbMasterSmp::distinct()->whereNotNull('kabupaten')->orderBy('kabupaten')->pluck('kabupaten');

        // Pilihan Cascading untuk Drawer / Modal Edit Alamat Siswa
        $addressProvinceList = [];
        $addressKabupatenList = [];
        $addressKecamatanList = [];
        $addressDesaList = [];

        if ($this->showEditAddressModal) {
            $addressProvinceList = $wilayahService->getProvincesList();
            $addressKabupatenList = $wilayahService->getKabupatenList($this->editAddressProvinsi ?: null);
            $addressKecamatanList = $wilayahService->getKecamatanList(
                $this->editAddressKabupaten ?: null,
                $this->editAddressProvinsi ?: null
            );
            $addressDesaList = $wilayahService->getDesaList(
                $this->editAddressKecamatan ?: null,
                $this->editAddressKabupaten ?: null
            );
        }

        return view('analitikspmb::livewire.wilayah-index', [
            'analytics' => $analytics,
            'tree' => $analytics['tree'] ?? [],
            'expandedNodes' => $this->expandedNodes ?? [],
            'paginatedItems' => $paginatedItems,
            'tahunAjarans' => $tahunAjarans,
            'jurusans' => $jurusans,
            'drawerSiswas' => $drawerSiswas,
            'editingAddressSiswa' => $editingAddressSiswa,
            'editingAddressIndex' => $editingAddressIndex,
            'provinsiOptions' => $provinsiOptions,
            'kabupatenOptions' => $kabupatenOptions,
            'kabupatenMasterList' => $kabupatenMasterList,
            'addressProvinceList' => $addressProvinceList,
            'addressKabupatenList' => $addressKabupatenList,
            'addressKecamatanList' => $addressKecamatanList,
            'addressDesaList' => $addressDesaList,
        ]);
    }
}
