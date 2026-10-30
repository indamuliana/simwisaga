<?php

namespace App\Modules\AnalitikSpmb\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\AnalitikSpmb\Models\SpmbSekolahAsal;
use App\Modules\AnalitikSpmb\Models\SpmbSekolahAsalAlias;
use App\Modules\AnalitikSpmb\Services\SpmbSekolahAsalExporter;
use App\Modules\AnalitikSpmb\Services\SpmbSekolahAsalSyncService;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Layout('components.layouts.app')]
#[Title('Analitik Asal SMP / MTs &bull; SPMB')]
class AsalSmpIndex extends Component
{
    use GuardsPermission;
    use WithPagination;

    // Filters
    public string $search = '';
    public string $filterTingkat = '';
    public string $filterTahunAjaranId = '';
    public string $filterJurusanId = '';
    public string $filterJk = '';
    public string $filterStatus = 'aktif';
    public string $filterTier = '';
    public string $filterJenis = '';
    public string $filterStatusSekolah = '';
    public string $sortBy = 'total_desc';
    public int $perPage = 15;

    // Slide-over Drawer (Daftar Siswa SMP)
    public bool $showDrawer = false;
    public ?int $selectedSekolahId = null;
    public ?string $selectedSekolahNama = null;
    public string $drawerSearch = '';
    public string $drawerFilterTingkat = '';
    public string $drawerFilterJurusan = '';
    public string $drawerFilterJk = '';

    // Modal Kelola Master & Alias
    public bool $showModalKelola = false;
    public ?int $editingMasterId = null;
    public string $formNamaResmi = '';
    public string $formNpsn = '';
    public string $formJenis = 'SMP';
    public string $formStatusSekolah = 'Negeri';
    public string $formKecamatan = '';
    public string $formKabupatenKota = 'Garut';
    public string $formTier = 'Rintisan';
    public string $formCatatan = '';
    public string $newAliasInput = '';

    // Unmapped Alert
    public int $unmappedCount = 0;

    public function mount()
    {
        $this->guard('analitik_spmb.asal_smp.lihat');

        // Jika master sekolah masih 0 dan data siswa ada, jalankan sync otomatis awal
        if (SpmbSekolahAsal::count() === 0) {
            $siswaWithSchool = Siswa::whereNotNull('sekolah_asal')->where('sekolah_asal', '!=', '')->exists();
            if ($siswaWithSchool) {
                app(SpmbSekolahAsalSyncService::class)->syncFromSiswaData();
            }
        }

        $this->checkUnmapped();
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterTingkat() { $this->resetPage(); }
    public function updatingFilterTahunAjaranId() { $this->resetPage(); }
    public function updatingFilterJurusanId() { $this->resetPage(); }
    public function updatingFilterJk() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterTier() { $this->resetPage(); }
    public function updatingFilterJenis() { $this->resetPage(); }
    public function updatingFilterStatusSekolah() { $this->resetPage(); }
    public function updatingSortBy() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function resetFilters()
    {
        $this->reset([
            'search',
            'filterTingkat',
            'filterTahunAjaranId',
            'filterJurusanId',
            'filterJk',
            'filterStatus',
            'filterTier',
            'filterJenis',
            'filterStatusSekolah',
            'sortBy',
        ]);
        $this->filterStatus = 'aktif';
        $this->resetPage();
    }

    public function checkUnmapped(): void
    {
        $mappedAliases = SpmbSekolahAsalAlias::pluck('alias_raw')->toArray();
        $this->unmappedCount = Siswa::query()
            ->whereNotNull('sekolah_asal')
            ->where('sekolah_asal', '!=', '')
            ->whereNotIn('sekolah_asal', $mappedAliases)
            ->distinct('sekolah_asal')
            ->count('sekolah_asal');
    }

    public function syncSekolah()
    {
        $this->guard('analitik_spmb.asal_smp.kelola');

        $result = app(SpmbSekolahAsalSyncService::class)->syncFromSiswaData();
        $this->checkUnmapped();

        session()->flash('success', "Sinkronisasi berhasil! Dipindai {$result['total_scanned']} sekolah asal, ditambahkan {$result['new_masters']} master baru dan {$result['new_aliases']} alias pemetaan.");
    }

    public function openDrawer(int $sekolahId)
    {
        $sekolah = SpmbSekolahAsal::findOrFail($sekolahId);
        $this->selectedSekolahId = $sekolahId;
        $this->selectedSekolahNama = $sekolah->nama_resmi;
        $this->drawerSearch = '';
        $this->drawerFilterTingkat = $this->filterTingkat;
        $this->drawerFilterJurusan = $this->filterJurusanId;
        $this->drawerFilterJk = $this->filterJk;
        $this->showDrawer = true;
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->selectedSekolahId = null;
        $this->selectedSekolahNama = null;
    }

    public function openModalKelola(?int $id = null)
    {
        $this->guard('analitik_spmb.asal_smp.kelola');

        $this->reset([
            'formNamaResmi',
            'formNpsn',
            'formJenis',
            'formStatusSekolah',
            'formKecamatan',
            'formKabupatenKota',
            'formTier',
            'formCatatan',
            'newAliasInput',
        ]);

        if ($id) {
            $master = SpmbSekolahAsal::with('aliases')->findOrFail($id);
            $this->editingMasterId = $id;
            $this->formNamaResmi = $master->nama_resmi;
            $this->formNpsn = $master->npsn ?? '';
            $this->formJenis = $master->jenis;
            $this->formStatusSekolah = $master->status_sekolah;
            $this->formKecamatan = $master->kecamatan ?? '';
            $this->formKabupatenKota = $master->kabupaten_kota ?? 'Garut';
            $this->formTier = $master->tier;
            $this->formCatatan = $master->catatan ?? '';
        } else {
            $this->editingMasterId = null;
            $this->formJenis = 'SMP';
            $this->formStatusSekolah = 'Negeri';
            $this->formKabupatenKota = 'Garut';
            $this->formTier = 'Rintisan';
        }

        $this->showModalKelola = true;
    }

    public function closeModalKelola()
    {
        $this->showModalKelola = false;
        $this->editingMasterId = null;
    }

    public function saveMasterSekolah()
    {
        $this->guard('analitik_spmb.asal_smp.kelola');

        $uniqueRule = 'required|string|max:150|unique:spmb_sekolah_asals,nama_resmi';
        if ($this->editingMasterId) {
            $uniqueRule .= ',' . $this->editingMasterId;
        }

        $this->validate([
            'formNamaResmi' => $uniqueRule,
            'formNpsn' => 'nullable|string|max:20',
            'formJenis' => 'required|in:SMP,MTs,Pesantren,Lainnya',
            'formStatusSekolah' => 'required|in:Negeri,Swasta',
            'formKecamatan' => 'nullable|string|max:100',
            'formKabupatenKota' => 'nullable|string|max:100',
            'formTier' => 'required|in:Utama,Potensial,Rintisan',
            'formCatatan' => 'nullable|string|max:500',
        ], [
            'formNamaResmi.unique' => 'Nama sekolah resmi ini sudah terdaftar sebagai master sekolah lain. Jika ini sekolah yang sama, silakan gunakan menu "Perbaikan Data Asal SMP" untuk menggabungkannya.',
        ]);

        $cleanName = trim($this->formNamaResmi);

        // Cek apakah nama resmi ini terdaftar sebagai alias pada master lain
        $existingAlias = SpmbSekolahAsalAlias::where('alias_raw', $cleanName)->first();
        if ($existingAlias && $this->editingMasterId && $existingAlias->sekolah_asal_id !== $this->editingMasterId) {
            $this->addError('formNamaResmi', "Nama '{$cleanName}' sudah terdaftar sebagai alias untuk master lain ('{$existingAlias->sekolahAsal?->nama_resmi}'). Silakan satukan melalui fitur 'Gabungkan Sekolah' di menu Perbaikan Data.");
            return;
        }

        try {
            if ($this->editingMasterId) {
                $master = SpmbSekolahAsal::findOrFail($this->editingMasterId);
                $master->update([
                    'nama_resmi' => $cleanName,
                    'npsn' => $this->formNpsn ?: null,
                    'jenis' => $this->formJenis,
                    'status_sekolah' => $this->formStatusSekolah,
                    'kecamatan' => $this->formKecamatan ?: null,
                    'kabupaten_kota' => $this->formKabupatenKota ?: 'Garut',
                    'tier' => $this->formTier,
                    'catatan' => $this->formCatatan ?: null,
                ]);

                session()->flash('success', "Data master sekolah '{$master->nama_resmi}' berhasil diperbarui.");
            } else {
                $master = SpmbSekolahAsal::create([
                    'nama_resmi' => $cleanName,
                    'npsn' => $this->formNpsn ?: null,
                    'jenis' => $this->formJenis,
                    'status_sekolah' => $this->formStatusSekolah,
                    'kecamatan' => $this->formKecamatan ?: null,
                    'kabupaten_kota' => $this->formKabupatenKota ?: 'Garut',
                    'tier' => $this->formTier,
                    'catatan' => $this->formCatatan ?: null,
                ]);

                $this->editingMasterId = $master->id;
                session()->flash('success', "Master sekolah '{$master->nama_resmi}' berhasil dibuat.");
            }
        } catch (\Illuminate\Database\UniqueConstraintViolationException | \Illuminate\Database\QueryException $e) {
            $this->addError('formNamaResmi', 'Nama sekolah ini sudah terdaftar sebagai master sekolah lain di database. Gunakan fitur "Gabungkan Sekolah" di menu Perbaikan Data Asal SMP.');
            return;
        }

        $this->checkUnmapped();
    }

    public function addAlias()
    {
        $this->guard('analitik_spmb.asal_smp.kelola');

        if (! $this->editingMasterId) {
            return;
        }

        $aliasRaw = trim($this->newAliasInput);
        if (empty($aliasRaw)) {
            return;
        }

        $exists = SpmbSekolahAsalAlias::where('alias_raw', $aliasRaw)->first();
        if ($exists) {
            if ($exists->sekolah_asal_id === $this->editingMasterId) {
                $this->addError('newAliasInput', 'Variasi alias ini sudah terdaftar pada sekolah ini.');
                return;
            }
            // Pindahkan ke master ini
            $exists->update(['sekolah_asal_id' => $this->editingMasterId]);
            $this->newAliasInput = '';
            session()->flash('success', "Alias '{$aliasRaw}' berhasil dipindahkan ke master ini.");
            $this->checkUnmapped();
            return;
        }

        SpmbSekolahAsalAlias::create([
            'sekolah_asal_id' => $this->editingMasterId,
            'alias_raw' => $aliasRaw,
        ]);

        $this->newAliasInput = '';
        session()->flash('success', "Alias baru '{$aliasRaw}' berhasil ditambahkan.");
        $this->checkUnmapped();
    }

    public function removeAlias(int $aliasId)
    {
        $this->guard('analitik_spmb.asal_smp.kelola');

        $alias = SpmbSekolahAsalAlias::findOrFail($aliasId);
        $alias->delete();

        session()->flash('success', 'Alias berhasil dihapus.');
        $this->checkUnmapped();
    }

    public function deleteMaster(int $id)
    {
        $this->guard('analitik_spmb.asal_smp.kelola');

        $master = SpmbSekolahAsal::findOrFail($id);
        $nama = $master->nama_resmi;
        $master->delete();

        if ($this->editingMasterId === $id) {
            $this->closeModalKelola();
        }

        session()->flash('success', "Master sekolah '{$nama}' berhasil dihapus.");
        $this->checkUnmapped();
    }

    public function exportExcel()
    {
        $this->guard('analitik_spmb.asal_smp.lihat');

        $data = $this->buildAnalyticsData(false);
        $jurusans = Jurusan::where('is_active', true)->orderBy('kode')->get()->all();

        $exporter = new SpmbSekolahAsalExporter();
        $spreadsheet = $exporter->exportRekapSekolah(
            $data['allSchoolsCollection'],
            [
                'totalSekolah' => $data['totalSekolah'],
                'totalSiswa' => $data['totalSiswa'],
                'avgPerSekolah' => $data['avgPerSekolah'],
            ],
            $jurusans
        );

        $filename = 'Analitik_Asal_SMP_SPMB_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function exportSiswaSekolah(int $sekolahId)
    {
        $this->guard('analitik_spmb.asal_smp.lihat');

        $sekolah = SpmbSekolahAsal::with('aliases')->findOrFail($sekolahId);
        $aliases = SpmbSekolahAsalAlias::where('sekolah_asal_id', $sekolah->id)->pluck('alias_raw')->toArray();
        if (! in_array($sekolah->nama_resmi, $aliases)) {
            $aliases[] = $sekolah->nama_resmi;
        }

        $query = Siswa::with(['rombel.jurusan'])
            ->leftJoin('rombels', 'siswas.rombel_id', '=', 'rombels.id')
            ->whereIn('siswas.sekolah_asal', $aliases);

        if ($this->filterStatus === 'aktif') {
            $query->where('siswas.is_active', true);
        } elseif ($this->filterStatus !== 'semua' && ! empty($this->filterStatus)) {
            $query->where('siswas.status', $this->filterStatus);
        }

        if ($this->filterTingkat) {
            $query->where('rombels.tingkat', $this->filterTingkat);
        }

        if ($this->filterTahunAjaranId) {
            $query->where('rombels.tahun_ajaran_id', $this->filterTahunAjaranId);
        }

        if ($this->filterJurusanId) {
            $query->where('rombels.jurusan_id', $this->filterJurusanId);
        }

        if ($this->filterJk) {
            $query->where('siswas.jk', $this->filterJk);
        }

        $siswas = $query->select('siswas.*')->orderBy('siswas.nama')->get();

        $exporter = new SpmbSekolahAsalExporter();
        $spreadsheet = $exporter->exportDaftarSiswa($sekolah, $siswas);

        $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $sekolah->nama_resmi);
        $filename = "Daftar_Siswa_{$cleanName}_" . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Membangun dataset analitik utama berdasarkan filter yang aktif
     */
    protected function buildAnalyticsData(bool $paginate = true): array
    {
        $jurusans = Jurusan::where('is_active', true)->orderBy('kode')->get();

        // Base Query Siswa
        $siswaQuery = Siswa::query()
            ->leftJoin('rombels', 'siswas.rombel_id', '=', 'rombels.id')
            ->leftJoin('jurusans', 'rombels.jurusan_id', '=', 'jurusans.id')
            ->whereNotNull('siswas.sekolah_asal')
            ->where('siswas.sekolah_asal', '!=', '');

        // Filter Siswa
        if ($this->filterStatus === 'aktif') {
            $siswaQuery->where('siswas.is_active', true);
        } elseif ($this->filterStatus !== 'semua' && ! empty($this->filterStatus)) {
            $siswaQuery->where('siswas.status', $this->filterStatus);
        }

        if ($this->filterTingkat) {
            $siswaQuery->where('rombels.tingkat', $this->filterTingkat);
        }

        if ($this->filterTahunAjaranId) {
            $siswaQuery->where('rombels.tahun_ajaran_id', $this->filterTahunAjaranId);
        }

        if ($this->filterJurusanId) {
            $siswaQuery->where('rombels.jurusan_id', $this->filterJurusanId);
        }

        if ($this->filterJk) {
            $siswaQuery->where('siswas.jk', $this->filterJk);
        }

        // Summary Counts
        $totalSiswa = (clone $siswaQuery)->count();

        // Agregasi Siswa berdasarkan master sekolah via alias
        $selects = [
            'spmb_sekolah_asals.id',
            'spmb_sekolah_asals.nama_resmi',
            'spmb_sekolah_asals.npsn',
            'spmb_sekolah_asals.jenis',
            'spmb_sekolah_asals.status_sekolah',
            'spmb_sekolah_asals.kecamatan',
            'spmb_sekolah_asals.kabupaten_kota',
            'spmb_sekolah_asals.tier',
            'spmb_sekolah_asals.catatan',
            DB::raw('count(siswas.id) as total_siswa'),
            DB::raw('sum(case when siswas.jk = "L" then 1 else 0 end) as siswa_l'),
            DB::raw('sum(case when siswas.jk = "P" then 1 else 0 end) as siswa_p'),
        ];

        foreach ($jurusans as $j) {
            $selects[] = DB::raw("sum(case when siswas.jurusan_id = {$j->id} then 1 else 0 end) as jurusan_{$j->id}");
        }

        $schoolQuery = SpmbSekolahAsal::query()
            ->leftJoin('spmb_sekolah_asal_aliases', 'spmb_sekolah_asals.id', '=', 'spmb_sekolah_asal_aliases.sekolah_asal_id')
            ->leftJoinSub(
                (clone $siswaQuery)->select(
                    'siswas.id',
                    'siswas.sekolah_asal',
                    'siswas.jk',
                    'rombels.jurusan_id',
                    'rombels.tingkat',
                    'rombels.tahun_ajaran_id'
                ),
                'siswas',
                'spmb_sekolah_asal_aliases.alias_raw',
                '=',
                'siswas.sekolah_asal'
            )
            ->select($selects)
            ->groupBy(
                'spmb_sekolah_asals.id',
                'spmb_sekolah_asals.nama_resmi',
                'spmb_sekolah_asals.npsn',
                'spmb_sekolah_asals.jenis',
                'spmb_sekolah_asals.status_sekolah',
                'spmb_sekolah_asals.kecamatan',
                'spmb_sekolah_asals.kabupaten_kota',
                'spmb_sekolah_asals.tier',
                'spmb_sekolah_asals.catatan'
            );

        // Filter Sekolah
        if ($this->search) {
            $s = '%' . trim($this->search) . '%';
            $schoolQuery->where(function ($q) use ($s) {
                $q->where('spmb_sekolah_asals.nama_resmi', 'like', $s)
                    ->orWhere('spmb_sekolah_asals.npsn', 'like', $s)
                    ->orWhere('spmb_sekolah_asals.kecamatan', 'like', $s)
                    ->orWhere('spmb_sekolah_asals.catatan', 'like', $s);
            });
        }

        if ($this->filterTier) {
            $schoolQuery->where('spmb_sekolah_asals.tier', $this->filterTier);
        }

        if ($this->filterJenis) {
            $schoolQuery->where('spmb_sekolah_asals.jenis', $this->filterJenis);
        }

        if ($this->filterStatusSekolah) {
            $schoolQuery->where('spmb_sekolah_asals.status_sekolah', $this->filterStatusSekolah);
        }

        // Terapkan having total_siswa > 0 jika sedang ada filter siswa khusus
        if ($this->filterTingkat || $this->filterJurusanId || $this->filterJk || $this->filterTahunAjaranId) {
            $schoolQuery->having('total_siswa', '>', 0);
        }

        // Sorting
        switch ($this->sortBy) {
            case 'total_asc':
                $schoolQuery->orderBy('total_siswa', 'asc')->orderBy('spmb_sekolah_asals.nama_resmi', 'asc');
                break;
            case 'name_asc':
                $schoolQuery->orderBy('spmb_sekolah_asals.nama_resmi', 'asc');
                break;
            case 'name_desc':
                $schoolQuery->orderBy('spmb_sekolah_asals.nama_resmi', 'desc');
                break;
            case 'total_desc':
            default:
                $schoolQuery->orderByDesc('total_siswa')->orderBy('spmb_sekolah_asals.nama_resmi', 'asc');
                break;
        }

        $allSchools = $schoolQuery->get();
        $totalSekolah = $allSchools->count();
        $avgPerSekolah = $totalSekolah > 0 ? round($totalSiswa / $totalSekolah, 1) : 0;
        $topSekolah = $allSchools->first();

        // Format data & kalkulasi persentase dan dominan
        $allSchools->transform(function ($item) use ($totalSiswa, $jurusans) {
            $dominantName = '-';
            $dominantCount = 0;
            foreach ($jurusans as $j) {
                $cnt = (int) ($item->{'jurusan_' . $j->id} ?? 0);
                if ($cnt > $dominantCount) {
                    $dominantCount = $cnt;
                    $dominantName = $j->kode;
                }
            }

            $pct = $totalSiswa > 0 ? round(($item->total_siswa / $totalSiswa) * 100, 1) : 0;
            $item->percentage = $pct;
            $item->dominant_jurusan = $dominantName !== '-' ? "{$dominantName} ({$dominantCount})" : '-';

            return $item;
        });

        // Top 10 List untuk Visual Chart & Leaderboard
        $top10List = $allSchools->sortByDesc('total_siswa')->values()->take(10);

        if ($paginate) {
            $page = LengthAwarePaginator::resolveCurrentPage();
            $paginated = new LengthAwarePaginator(
                $allSchools->forPage($page, $this->perPage)->values(),
                $allSchools->count(),
                $this->perPage,
                $page,
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );
        } else {
            $paginated = null;
        }

        return [
            'paginated' => $paginated,
            'allSchoolsCollection' => $allSchools,
            'jurusans' => $jurusans,
            'totalSiswa' => $totalSiswa,
            'totalSekolah' => $totalSekolah,
            'avgPerSekolah' => $avgPerSekolah,
            'topSekolah' => $topSekolah,
            'top10List' => $top10List,
        ];
    }

    public function render()
    {
        $data = $this->buildAnalyticsData(true);
        $tahunAjarans = TahunAjaran::orderByDesc('id')->get();

        // Data Siswa untuk Drawer jika terbuka
        $drawerSiswas = collect();
        $drawerSekolah = null;
        if ($this->showDrawer && $this->selectedSekolahId) {
            $drawerSekolah = SpmbSekolahAsal::with('aliases')->find($this->selectedSekolahId);
            if ($drawerSekolah) {
                // Seluruh alias resmi master ini
                $aliases = SpmbSekolahAsalAlias::where('sekolah_asal_id', $drawerSekolah->id)->pluck('alias_raw')->toArray();
                if (! in_array($drawerSekolah->nama_resmi, $aliases)) {
                    $aliases[] = $drawerSekolah->nama_resmi;
                }

                $sQuery = Siswa::with(['rombel.jurusan'])
                    ->leftJoin('rombels', 'siswas.rombel_id', '=', 'rombels.id')
                    ->whereIn('siswas.sekolah_asal', $aliases);

                // Filter status konsisten dengan tabel utama
                if ($this->filterStatus === 'aktif') {
                    $sQuery->where('siswas.is_active', true);
                } elseif ($this->filterStatus !== 'semua' && ! empty($this->filterStatus)) {
                    $sQuery->where('siswas.status', $this->filterStatus);
                }

                // Filter tahun ajaran dari halaman utama
                if ($this->filterTahunAjaranId) {
                    $sQuery->where('rombels.tahun_ajaran_id', $this->filterTahunAjaranId);
                }

                if ($this->drawerSearch) {
                    $ds = '%' . trim($this->drawerSearch) . '%';
                    $sQuery->where(function ($q) use ($ds) {
                        $q->where('siswas.nama', 'like', $ds)
                            ->orWhere('siswas.nisn', 'like', $ds)
                            ->orWhere('siswas.nipd', 'like', $ds);
                    });
                }

                // Prioritas filter drawer, fallback filter utama
                $activeTingkat = $this->drawerFilterTingkat ?: $this->filterTingkat;
                if ($activeTingkat) {
                    $sQuery->where('rombels.tingkat', $activeTingkat);
                }

                $activeJurusan = $this->drawerFilterJurusan ?: $this->filterJurusanId;
                if ($activeJurusan) {
                    $sQuery->where('rombels.jurusan_id', $activeJurusan);
                }

                $activeJk = $this->drawerFilterJk ?: $this->filterJk;
                if ($activeJk) {
                    $sQuery->where('siswas.jk', $activeJk);
                }

                $drawerSiswas = $sQuery->select('siswas.*')->orderBy('siswas.nama')->get();
            }
        }

        // Data Master yang sedang diedit jika modal kelola terbuka
        $editingMaster = null;
        if ($this->showModalKelola && $this->editingMasterId) {
            $editingMaster = SpmbSekolahAsal::with('aliases')->find($this->editingMasterId);
        }

        return view('analitikspmb::livewire.asal-smp-index', array_merge([
            'sekolahs' => $data['paginated'],
            'jurusans' => $data['jurusans'],
            'tahunAjarans' => $tahunAjarans,
            'totalSiswa' => $data['totalSiswa'],
            'totalSekolah' => $data['totalSekolah'],
            'avgPerSekolah' => $data['avgPerSekolah'],
            'topSekolah' => $data['topSekolah'],
            'top10List' => $data['top10List'],
            'drawerSiswas' => $drawerSiswas,
            'drawerSekolah' => $drawerSekolah,
            'editingMaster' => $editingMaster,
        ], get_object_vars($this)));
    }
}
