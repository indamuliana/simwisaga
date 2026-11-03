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

    #[Url(history: true)]
    public string $activeTab = 'analitik'; // 'analitik' | 'perbaikan'

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

    // Slide-over Drawer Siswa
    public bool $showDrawer = false;

    public string $drawerWilayahTitle = '';

    public array $drawerCriteria = [];

    public string $drawerSearch = '';

    public string $drawerFilterTingkat = '';

    public string $drawerFilterJurusan = '';

    public string $drawerFilterJk = '';

    // Modal Standarisasi Kelompok (Tab Perbaikan)
    public bool $showEditModal = false;

    public string $editGroupKecamatan = '';

    public string $editGroupKelurahan = '';

    public array $editGroupSiswaIds = [];

    public int $editGroupTotalSiswa = 0;

    public bool $editIsLuarNegeri = false;

    public string $editNegara = 'Indonesia';

    public string $editProvinsi = '';

    public string $editKabupaten = '';

    public string $editKecamatan = '';

    public array $modalPillOptions = [];

    public ?string $modalHint = null;

    public bool $isManualEditProvinsi = false;

    public bool $isManualEditKabupaten = false;

    public bool $isManualEditKecamatan = false;

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

    public function updatedActiveTab(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
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
        $this->resetPage();
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['analitik', 'perbaikan']) ? $tab : 'analitik';
        $this->resetPage();
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
     * Buka Drawer Slide-over Siswa per Wilayah
     */
    public function openDrawer(string $negara, string $provinsi, string $kabupaten, string $kecamatan): void
    {
        $this->drawerCriteria = [
            'negara' => $negara,
            'provinsi' => $provinsi,
            'kabupaten_kota' => $kabupaten,
            'kecamatan' => $kecamatan,
        ];

        $titleParts = array_filter([$kecamatan, $kabupaten, $provinsi, $negara], fn ($v) => $v && $v !== '[Belum Diset]');
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
     * Buka Modal Edit Kelompok Anomali di Tab Perbaikan
     */
    public function openEditGroupModal(string $kecamatan, string $kelurahan, string $siswaIds, int $total): void
    {
        $this->guard('analitik_spmb.wilayah.kelola');

        $this->editGroupKecamatan = $kecamatan;
        $this->editGroupKelurahan = $kelurahan;
        $this->editGroupSiswaIds = array_filter(explode(',', $siswaIds));
        $this->editGroupTotalSiswa = $total;

        // Ambil sampel data awal dari siswa pertama
        $sample = Siswa::find($this->editGroupSiswaIds[0] ?? 0);
        $isLuar = $sample && $sample->negara && $sample->negara !== 'Indonesia';

        $this->editIsLuarNegeri = (bool) $isLuar;
        $this->editNegara = $sample?->negara ?: ($isLuar ? 'Malaysia' : 'Indonesia');
        $this->editProvinsi = $sample?->provinsi ?: ($isLuar ? 'Sabah' : 'Jawa Barat');
        $this->editKabupaten = $sample?->kabupaten_kota ?: '';
        $this->editKecamatan = preg_replace('/^kec\.?\s+/i', '', $kecamatan);

        $service = app(SpmbWilayahService::class);

        // Jika kabupaten saat ini adalah 'Garut' atau kosong, standarisasikan jika memungkinkan
        if (strtolower($this->editKabupaten) === 'garut' || empty($this->editKabupaten)) {
            $std = $service->standardizeKabupatenKotaName($this->editKabupaten ?: $sample?->kabupaten_kota, $this->editKecamatan, $sample?->sekolah_asal);
            if ($std) {
                $this->editKabupaten = $std;
            }
        }

        // Ambil opsi pill button dan petunjuk dari Master SMP
        $dualityInfo = $service->getDualityPillOptions($this->editKecamatan, $this->editKabupaten);
        $this->modalPillOptions = $dualityInfo['pills'];
        $this->modalHint = $dualityInfo['hint'];

        // Deteksi mode manual jika nilai awal tidak ada di master
        $provinces = $service->getProvincesList();
        $this->isManualEditProvinsi = ! empty($this->editProvinsi) && ! in_array($this->editProvinsi, $provinces, true);

        $kabupatens = $service->getKabupatenList($this->editProvinsi ?: null);
        $this->isManualEditKabupaten = ! empty($this->editKabupaten) && ! in_array($this->editKabupaten, $kabupatens, true);

        $kecamatans = $service->getKecamatanList($this->editKabupaten ?: null, $this->editProvinsi ?: null);
        $this->isManualEditKecamatan = ! empty($this->editKecamatan) && ! in_array($this->editKecamatan, $kecamatans, true);

        $this->showEditModal = true;
    }

    public function selectPillKabupaten(string $val): void
    {
        $this->editKabupaten = $val;
        $this->isManualEditKabupaten = false;
        if (str_contains($val, 'Bogor') || str_contains($val, 'Bandung') || str_contains($val, 'Garut') || str_contains($val, 'Bekasi') || str_contains($val, 'Cirebon') || str_contains($val, 'Sukabumi') || str_contains($val, 'Tasikmalaya')) {
            $this->editProvinsi = 'Jawa Barat';
            $this->editNegara = 'Indonesia';
            $this->editIsLuarNegeri = false;
        }
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->editGroupSiswaIds = [];
        $this->modalPillOptions = [];
        $this->modalHint = null;
    }

    public function updatedEditIsLuarNegeri($val): void
    {
        if ($val) {
            $this->editNegara = 'Malaysia';
            $this->editProvinsi = 'Sabah';
            $this->isManualEditProvinsi = false;
        } else {
            $this->editNegara = 'Indonesia';
            $this->editProvinsi = 'Jawa Barat';
            $this->isManualEditProvinsi = false;
        }
    }

    public function updatedEditProvinsi($val): void
    {
        if (! $this->isManualEditProvinsi) {
            $this->editKabupaten = '';
            $this->editKecamatan = '';
        }
    }

    public function updatedEditKabupaten($val): void
    {
        if (! $this->isManualEditKabupaten) {
            $this->editKecamatan = '';
            if (empty($this->editProvinsi) && ! empty($val)) {
                $master = SpmbMasterSmp::where('kabupaten', $val)->whereNotNull('provinsi')->first();
                if ($master && $master->provinsi) {
                    $this->editProvinsi = $master->provinsi;
                }
            }
        }
    }

    public function updatedEditKecamatan($val): void
    {
        if (! $this->isManualEditKecamatan) {
            $cleanVal = preg_replace('/^kec\.?\s+/i', '', trim($val));
            if ($cleanVal !== $val) {
                $this->editKecamatan = $cleanVal;
            }
        } else {
            $cleanVal = trim($val);
        }

        if (! empty($cleanVal)) {
            $service = app(SpmbWilayahService::class);
            $resolved = $service->resolveLocationFromKecamatan($cleanVal);
            if ($resolved) {
                if (empty($this->editKabupaten) || ! $this->isManualEditKabupaten) {
                    $this->editKabupaten = $resolved['kabupaten'];
                }
                if (empty($this->editProvinsi) || ! $this->isManualEditProvinsi) {
                    $this->editProvinsi = $resolved['provinsi'];
                }
                if (empty($this->editNegara)) {
                    $this->editNegara = $resolved['negara'];
                }
            }
        }
    }

    /**
     * Simpan Standarisasi Wilayah Massal
     */
    public function saveStandardizedWilayah(): void
    {
        $this->guard('analitik_spmb.wilayah.kelola');

        $this->validate([
            'editNegara' => 'required|string|max:100',
            'editProvinsi' => 'required|string|max:100',
            'editKabupaten' => 'required|string|max:100',
            'editKecamatan' => 'nullable|string|max:100',
        ]);

        $service = app(SpmbWilayahService::class);
        $updated = $service->bulkUpdateWilayah(
            ['ids' => $this->editGroupSiswaIds],
            [
                'negara' => trim($this->editNegara),
                'provinsi' => trim($this->editProvinsi),
                'kabupaten_kota' => trim($this->editKabupaten),
                'kecamatan' => trim($this->editKecamatan),
            ]
        );

        $this->closeEditModal();
        session()->flash('success', "Standarisasi berhasil! {$updated} data siswa diperbarui ke {$this->editKabupaten}, {$this->editProvinsi} ({$this->editNegara}).");
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
        $jurusans = Jurusan::orderBy('kode')->get();

        // Data Tab Analitik
        $analytics = [];
        $paginatedItems = null;

        if ($this->activeTab === 'analitik') {
            $analytics = $wilayahService->getWilayahAnalytics(array_merge(get_object_vars($this), [
                'search' => $this->search,
            ]));

            // Manual pagination untuk tabel analitik
            $page = LengthAwarePaginator::resolveCurrentPage();
            $paginatedItems = new LengthAwarePaginator(
                $analytics['items']->forPage($page, $this->perPage)->values(),
                $analytics['items']->count(),
                $this->perPage,
                $page,
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );
        }

        // Data Tab Perbaikan
        $anomaliGroups = collect();
        $totalAnomaliSiswa = 0;

        if ($this->activeTab === 'perbaikan') {
            $anomaliGroups = $wilayahService->getAnomaliGroups($this->search);
            $totalAnomaliSiswa = $anomaliGroups->sum('total_siswa');
        }

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

        // Pilihan Cascading untuk Modal Standarisasi Kelompok (Tab 2 Perbaikan)
        $groupProvinceList = [];
        $groupKabupatenList = [];
        $groupKecamatanList = [];

        if ($this->showEditModal) {
            $groupProvinceList = $wilayahService->getProvincesList();
            $groupKabupatenList = $wilayahService->getKabupatenList($this->editProvinsi ?: null);
            $groupKecamatanList = $wilayahService->getKecamatanList(
                $this->editKabupaten ?: null,
                $this->editProvinsi ?: null
            );
        }

        return view('analitikspmb::livewire.wilayah-index', [
            'analytics' => $analytics,
            'paginatedItems' => $paginatedItems,
            'anomaliGroups' => $anomaliGroups,
            'totalAnomaliSiswa' => $totalAnomaliSiswa,
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
            'groupProvinceList' => $groupProvinceList,
            'groupKabupatenList' => $groupKabupatenList,
            'groupKecamatanList' => $groupKecamatanList,
        ]);
    }
}
