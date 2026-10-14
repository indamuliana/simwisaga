<?php

namespace App\Modules\Akademik\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Models\User;
use App\Modules\Akademik\Models\CustomKelompok;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Services\CustomKelompokExcelService;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kesiswaan\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('components.layouts.app')]
#[Title('Custom Kelompok')]
class CustomKelompokIndex extends Component
{
    use GuardsPermission, WithPagination, WithFileUploads;

    // Navigation Tabs: 'siswa' | 'kelompok' | 'pengelompokan'
    public string $activeTab = 'siswa';

    // Tab 1: Seluruh Siswa & Tag
    public string $searchSiswa = '';
    public ?int $filterRombel = null;
    public ?int $filterRayon = null;
    public string $filterKelompok = 'semua'; // 'semua', 'ada', 'belum', or numeric id

    // Tab 2: Kelola Kelompok
    public string $searchKelompok = '';
    public string $filterKategori = '';
    public ?int $filterTahunAjaran = null;

    // Form Master Kelompok Modal
    public bool $showKelompokModal = false;
    public ?int $kelompokId = null;
    public string $kode = '';
    public string $nama = '';
    public string $kategori = 'Akademik';
    public string $keterangan = '';
    public ?int $pembimbing_id = null;
    public string $warna = '#3b82f6';
    public ?int $kapasitas = null;
    public ?int $tahun_ajaran_id = null;
    public bool $is_active = true;

    // Delete Kelompok Confirmation Modal
    public bool $showDeleteModal = false;
    public ?int $kelompokToDeleteId = null;
    public string $kelompokToDeleteNama = '';

    // Tab 3: Workspace Pengelompokan Siswa
    public ?int $selectedKelompokId = null;
    public string $searchAnggota = '';

    // Modal Pendaftaran Manual Siswa
    public bool $showManualModal = false;
    public string $manualSearch = '';
    public ?int $manualFilterRombel = null;
    public ?int $manualFilterRayon = null;
    public array $selectedSiswaIds = [];
    public string $catatanPerSiswa = '';

    // Modal Edit Keterangan Per Siswa
    public bool $showEditKeteranganModal = false;
    public ?int $editingSiswaId = null;
    public string $editingSiswaNama = '';
    public string $editingKeterangan = '';

    // Modal Import Massal Excel
    public bool $showImportModal = false;
    public $importFile = null;
    public ?array $importSummary = null;

    // Modal Cetak Presensi
    public bool $showCetakModal = false;
    public ?int $cetakKelompokId = null;
    public int $cetakJumlahKolom = 20;
    public string $cetakTahunPelajaran = '';
    public string $cetakMapelKeterangan = '';
    public string $cetakNamaGuru = '';
    public string $cetakNipGuru = '';

    public function mount(): void
    {
        $this->guard('akademik.custom_kelompok.lihat');

        $activeTa = AcademicContext::currentTahunAjaran();
        if ($activeTa) {
            $this->filterTahunAjaran = $activeTa->id;
            $this->tahun_ajaran_id = $activeTa->id;
            $this->cetakTahunPelajaran = $activeTa->nama;
        } else {
            $this->cetakTahunPelajaran = date('Y') . '/' . (date('Y') + 1);
        }

        // Set default selected group if any exists
        $firstKelompok = CustomKelompok::orderBy('nama')->first();
        if ($firstKelompok) {
            $this->selectedKelompokId = $firstKelompok->id;
        }
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage('siswaPage');
    }

    // Reset pagination when Tab 1 filters change
    public function updatedSearchSiswa(): void { $this->resetPage('siswaPage'); }
    public function updatedFilterRombel(): void { $this->resetPage('siswaPage'); }
    public function updatedFilterRayon(): void { $this->resetPage('siswaPage'); }
    public function updatedFilterKelompok(): void { $this->resetPage('siswaPage'); }

    // ==========================================
    // TAB 2: KELOLA KELOMPOK (CRUD)
    // ==========================================

    public function createKelompok(): void
    {
        $this->guard('akademik.custom_kelompok.tambah');
        $this->resetKelompokForm();
        $this->showKelompokModal = true;
    }

    public function editKelompok(int $id): void
    {
        $this->guard('akademik.custom_kelompok.edit');
        $kelompok = CustomKelompok::findOrFail($id);
        $this->kelompokId = $kelompok->id;
        $this->kode = $kelompok->kode ?? '';
        $this->nama = $kelompok->nama;
        $this->kategori = $kelompok->kategori ?? 'Akademik';
        $this->keterangan = $kelompok->keterangan ?? '';
        $this->pembimbing_id = $kelompok->pembimbing_id;
        $this->warna = $kelompok->warna ?? '#3b82f6';
        $this->kapasitas = $kelompok->kapasitas;
        $this->tahun_ajaran_id = $kelompok->tahun_ajaran_id;
        $this->is_active = (bool) $kelompok->is_active;

        $this->showKelompokModal = true;
    }

    public function saveKelompok(): void
    {
        if ($this->kelompokId) {
            $this->guard('akademik.custom_kelompok.edit');
        } else {
            $this->guard('akademik.custom_kelompok.tambah');
        }

        $this->validate([
            'nama' => ['required', 'string', 'max:150'],
            'kode' => ['nullable', 'string', 'max:30'],
            'kategori' => ['nullable', 'string', 'max:50'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'pembimbing_id' => ['nullable', 'exists:users,id'],
            'warna' => ['required', 'string', 'max:20'],
            'kapasitas' => ['nullable', 'integer', 'min:1', 'max:500'],
            'tahun_ajaran_id' => ['nullable', 'exists:tahun_ajarans,id'],
            'is_active' => ['boolean'],
        ]);

        $data = [
            'nama' => $this->nama,
            'kode' => $this->kode ?: null,
            'kategori' => $this->kategori ?: null,
            'keterangan' => $this->keterangan ?: null,
            'pembimbing_id' => $this->pembimbing_id ?: null,
            'warna' => $this->warna,
            'kapasitas' => $this->kapasitas ?: null,
            'tahun_ajaran_id' => $this->tahun_ajaran_id ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->kelompokId) {
            CustomKelompok::where('id', $this->kelompokId)->update($data);
            session()->flash('status', "Kelompok '{$this->nama}' berhasil diperbarui.");
        } else {
            $created = CustomKelompok::create($data);
            $this->selectedKelompokId = $created->id;
            session()->flash('status', "Kelompok baru '{$this->nama}' berhasil dibuat.");
        }

        $this->showKelompokModal = false;
        $this->resetKelompokForm();
    }

    public function confirmDeleteKelompok(int $id): void
    {
        $this->guard('akademik.custom_kelompok.hapus');
        $kelompok = CustomKelompok::findOrFail($id);
        $this->kelompokToDeleteId = $kelompok->id;
        $this->kelompokToDeleteNama = $kelompok->nama;
        $this->showDeleteModal = true;
    }

    public function deleteKelompok(): void
    {
        $this->guard('akademik.custom_kelompok.hapus');
        if (! $this->kelompokToDeleteId) {
            return;
        }

        $kelompok = CustomKelompok::findOrFail($this->kelompokToDeleteId);
        $nama = $kelompok->nama;
        $kelompok->delete();

        if ($this->selectedKelompokId === $this->kelompokToDeleteId) {
            $this->selectedKelompokId = CustomKelompok::orderBy('nama')->first()?->id;
        }

        $this->showDeleteModal = false;
        $this->kelompokToDeleteId = null;
        session()->flash('status', "Kelompok '{$nama}' beserta data keanggotaannya berhasil dihapus.");
    }

    private function resetKelompokForm(): void
    {
        $this->kelompokId = null;
        $this->kode = '';
        $this->nama = '';
        $this->kategori = 'Akademik';
        $this->keterangan = '';
        $this->pembimbing_id = null;
        $this->warna = '#3b82f6';
        $this->kapasitas = null;
        $activeTa = AcademicContext::currentTahunAjaran();
        $this->tahun_ajaran_id = $activeTa?->id;
        $this->is_active = true;
    }

    // ==========================================
    // TAB 3: PENGELOMPOKAN SISWA (WORKSPACE)
    // ==========================================

    public function selectKelompok(int $id): void
    {
        $this->selectedKelompokId = $id;
        $this->activeTab = 'pengelompokan';
        $this->searchAnggota = '';
    }

    public function removeSiswa(int $siswaId): void
    {
        $this->guard('akademik.custom_kelompok.hapus');
        if (! $this->selectedKelompokId) {
            return;
        }

        $kelompok = CustomKelompok::findOrFail($this->selectedKelompokId);
        $kelompok->siswas()->detach($siswaId);
        session()->flash('status', 'Siswa berhasil dikeluarkan dari kelompok ini.');
    }

    public function editKeteranganSiswa(int $siswaId): void
    {
        $this->guard('akademik.custom_kelompok.edit');
        if (! $this->selectedKelompokId) {
            return;
        }

        $kelompok = CustomKelompok::findOrFail($this->selectedKelompokId);
        $siswa = $kelompok->siswas()->where('siswas.id', $siswaId)->firstOrFail();

        $this->editingSiswaId = $siswaId;
        $this->editingSiswaNama = $siswa->nama;
        $this->editingKeterangan = $siswa->pivot->keterangan ?? '';
        $this->showEditKeteranganModal = true;
    }

    public function saveKeteranganSiswa(): void
    {
        $this->guard('akademik.custom_kelompok.edit');
        if (! $this->selectedKelompokId || ! $this->editingSiswaId) {
            return;
        }

        $kelompok = CustomKelompok::findOrFail($this->selectedKelompokId);
        $kelompok->siswas()->updateExistingPivot($this->editingSiswaId, [
            'keterangan' => $this->editingKeterangan ?: null,
        ]);

        $this->showEditKeteranganModal = false;
        $this->editingSiswaId = null;
        session()->flash('status', "Keterangan siswa '{$this->editingSiswaNama}' berhasil disimpan.");
    }

    // Manual Enroll Modal Actions
    public function openManualModal(): void
    {
        $this->guard('akademik.custom_kelompok.tambah');
        $this->manualSearch = '';
        $this->manualFilterRombel = null;
        $this->manualFilterRayon = null;
        $this->selectedSiswaIds = [];
        $this->catatanPerSiswa = '';
        $this->showManualModal = true;
    }

    public function toggleSiswaSelection(int $siswaId): void
    {
        if (in_array($siswaId, $this->selectedSiswaIds)) {
            $this->selectedSiswaIds = array_diff($this->selectedSiswaIds, [$siswaId]);
        } else {
            $this->selectedSiswaIds[] = $siswaId;
        }
    }

    public function saveManualSiswa(): void
    {
        $this->guard('akademik.custom_kelompok.tambah');
        if (! $this->selectedKelompokId) {
            session()->flash('error', 'Silakan pilih kelompok terlebih dahulu.');
            return;
        }

        if (empty($this->selectedSiswaIds)) {
            session()->flash('error', 'Pilih minimal satu siswa untuk ditambahkan.');
            return;
        }

        $kelompok = CustomKelompok::findOrFail($this->selectedKelompokId);

        // Cek kuota kapasitas
        if ($kelompok->kapasitas) {
            $currentCount = $kelompok->siswas()->count();
            $maxCanAdd = max(0, $kelompok->kapasitas - $currentCount);
            if (count($this->selectedSiswaIds) > $maxCanAdd) {
                session()->flash('error', "Kapasitas kelompok tersisa {$maxCanAdd} siswa. Tidak dapat menambahkan " . count($this->selectedSiswaIds) . " siswa.");
                return;
            }
        }

        $added = 0;
        foreach ($this->selectedSiswaIds as $sId) {
            if (! $kelompok->siswas()->where('siswa_id', $sId)->exists()) {
                $kelompok->siswas()->attach($sId, [
                    'keterangan' => $this->catatanPerSiswa ?: null,
                ]);
                $added++;
            }
        }

        $this->showManualModal = false;
        $this->selectedSiswaIds = [];
        session()->flash('status', "Berhasil menambahkan {$added} siswa ke dalam kelompok '{$kelompok->nama}'.");
    }

    // ==========================================
    // IMPORT & EXPORT ACTIONS
    // ==========================================

    public function openImportModal(): void
    {
        $this->guard('akademik.custom_kelompok.tambah');
        $this->importFile = null;
        $this->importSummary = null;
        $this->showImportModal = true;
    }

    public function downloadTemplate(CustomKelompokExcelService $excelService): StreamedResponse
    {
        $this->guard('akademik.custom_kelompok.lihat');
        $kelompok = CustomKelompok::findOrFail($this->selectedKelompokId);
        $spreadsheet = $excelService->downloadTemplate($kelompok);

        $safeName = Str::slug($kelompok->nama);
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, "Template_Import_{$safeName}.xlsx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function processImport(CustomKelompokExcelService $excelService): void
    {
        $this->guard('akademik.custom_kelompok.tambah');
        $this->validate([
            'importFile' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ]);

        $kelompok = CustomKelompok::findOrFail($this->selectedKelompokId);
        $path = $this->importFile->getRealPath();

        $this->importSummary = $excelService->importMembers($kelompok, $path);
        session()->flash('status', "Import selesai: {$this->importSummary['imported']} siswa ditambahkan, {$this->importSummary['updated']} keterangan diperbarui, {$this->importSummary['skipped']} dilewati.");
    }

    public function exportAnggotaExcel(CustomKelompokExcelService $excelService): StreamedResponse
    {
        $this->guard('akademik.custom_kelompok.cetak');
        $kelompok = CustomKelompok::findOrFail($this->selectedKelompokId);
        $spreadsheet = $excelService->exportMembers($kelompok);

        $safeName = Str::slug($kelompok->nama);
        $filename = "Anggota_Kelompok_{$safeName}_" . date('Ymd_His') . ".xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportSiswaExcel(CustomKelompokExcelService $excelService): StreamedResponse
    {
        $this->guard('akademik.custom_kelompok.cetak');

        $query = Siswa::query()
            ->with(['rombel', 'rayon', 'customKelompoks'])
            ->where('is_active', true)
            ->where('status', 'aktif');

        if ($this->searchSiswa) {
            $query->where(function ($q) {
                $q->where('nama', 'like', "%{$this->searchSiswa}%")
                  ->orWhere('nipd', 'like', "%{$this->searchSiswa}%")
                  ->orWhere('nisn', 'like', "%{$this->searchSiswa}%");
            });
        }

        if ($this->filterRombel) {
            $query->where('rombel_id', $this->filterRombel);
        }

        if ($this->filterRayon) {
            $query->where('rayon_id', $this->filterRayon);
        }

        if ($this->filterKelompok === 'ada') {
            $query->has('customKelompoks');
        } elseif ($this->filterKelompok === 'belum') {
            $query->doesntHave('customKelompoks');
        } elseif (is_numeric($this->filterKelompok)) {
            $query->whereHas('customKelompoks', function ($q) {
                $q->where('custom_kelompoks.id', (int) $this->filterKelompok);
            });
        }

        $siswas = $query->orderBy('nama')->get();
        $spreadsheet = $excelService->exportSiswaTags($siswas);

        $filename = "Daftar_Siswa_Kelompok_Tag_" . date('Ymd_His') . ".xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ==========================================
    // CETAK ABSENSI (PDF & XLS)
    // ==========================================

    public function openCetakModal(?int $kelompokId = null): void
    {
        $this->guard('akademik.custom_kelompok.cetak');
        $targetId = $kelompokId ?: $this->selectedKelompokId;
        if (! $targetId) {
            session()->flash('error', 'Pilih kelompok terlebih dahulu untuk mencetak absensi.');
            return;
        }

        $kelompok = CustomKelompok::with('pembimbing')->findOrFail($targetId);
        $this->cetakKelompokId = $kelompok->id;
        $this->cetakJumlahKolom = 20;
        $activeTa = AcademicContext::currentTahunAjaran();
        $this->cetakTahunPelajaran = $kelompok->tahunAjaran?->nama ?? ($activeTa?->nama ?? (date('Y') . '/' . (date('Y') + 1)));
        $this->cetakMapelKeterangan = $kelompok->kategori ? "Kegiatan {$kelompok->kategori}" : 'Daftar Presensi';
        $this->cetakNamaGuru = $kelompok->pembimbing?->name ?? '';
        $this->cetakNipGuru = '';

        $this->showCetakModal = true;
    }

    public function cetakAbsenPdf(): ?StreamedResponse
    {
        $this->guard('akademik.custom_kelompok.cetak');
        $kelompok = CustomKelompok::findOrFail($this->cetakKelompokId);
        $siswas = $kelompok->siswas()->with(['rombel', 'rayon'])->orderBy('nama')->get();

        $pdf = Pdf::loadView('akademik::pdf.cetak-absen-kelompok', [
            'kelompok' => $kelompok,
            'siswas' => $siswas,
            'jumlahKolom' => max(5, min(40, $this->cetakJumlahKolom)),
            'tahunPelajaran' => $this->cetakTahunPelajaran,
            'mapel' => $this->cetakMapelKeterangan,
            'namaGuru' => $this->cetakNamaGuru,
            'nipGuru' => $this->cetakNipGuru,
        ])->setPaper('A4', 'portrait');

        $safeName = Str::slug($kelompok->nama);
        $filename = "Absen_Kelompok_{$safeName}_" . date('Ymd_His') . ".pdf";

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename
        );
    }

    public function cetakAbsenExcel(CustomKelompokExcelService $excelService): StreamedResponse
    {
        $this->guard('akademik.custom_kelompok.cetak');
        $kelompok = CustomKelompok::findOrFail($this->cetakKelompokId);

        $options = [
            'jumlahKolom' => $this->cetakJumlahKolom,
            'tahunPelajaran' => $this->cetakTahunPelajaran,
            'mapel' => $this->cetakMapelKeterangan,
            'namaGuru' => $this->cetakNamaGuru,
            'nipGuru' => $this->cetakNipGuru,
        ];

        $spreadsheet = $excelService->exportAttendanceSheet($kelompok, $options);
        $safeName = Str::slug($kelompok->nama);
        $filename = "Absen_Kelompok_{$safeName}_" . date('Ymd_His') . ".xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ==========================================
    // RENDER VIEW
    // ==========================================

    public function render()
    {
        // 1. Data Tab 1 (Siswa)
        $siswaQuery = Siswa::query()
            ->with(['rombel', 'rayon', 'customKelompoks'])
            ->where('is_active', true)
            ->where('status', 'aktif');

        if ($this->searchSiswa) {
            $siswaQuery->where(function ($q) {
                $q->where('nama', 'like', "%{$this->searchSiswa}%")
                  ->orWhere('nipd', 'like', "%{$this->searchSiswa}%")
                  ->orWhere('nisn', 'like', "%{$this->searchSiswa}%");
            });
        }

        if ($this->filterRombel) {
            $siswaQuery->where('rombel_id', $this->filterRombel);
        }

        if ($this->filterRayon) {
            $siswaQuery->where('rayon_id', $this->filterRayon);
        }

        if ($this->filterKelompok === 'ada') {
            $siswaQuery->has('customKelompoks');
        } elseif ($this->filterKelompok === 'belum') {
            $siswaQuery->doesntHave('customKelompoks');
        } elseif (is_numeric($this->filterKelompok)) {
            $siswaQuery->whereHas('customKelompoks', function ($q) {
                $q->where('custom_kelompoks.id', (int) $this->filterKelompok);
            });
        }

        $siswas = $siswaQuery->orderBy('nama')->paginate(20, ['*'], 'siswaPage');

        // 2. Data Tab 2 (Kelompok)
        $kelompokQuery = CustomKelompok::query()
            ->with(['tahunAjaran', 'pembimbing'])
            ->withCount('siswas');

        if ($this->searchKelompok) {
            $kelompokQuery->where(function ($q) {
                $q->where('nama', 'like', "%{$this->searchKelompok}%")
                  ->orWhere('kode', 'like', "%{$this->searchKelompok}%")
                  ->orWhere('keterangan', 'like', "%{$this->searchKelompok}%");
            });
        }

        if ($this->filterKategori) {
            $kelompokQuery->where('kategori', $this->filterKategori);
        }

        if ($this->filterTahunAjaran) {
            $kelompokQuery->where(function ($q) {
                $q->where('tahun_ajaran_id', $this->filterTahunAjaran)
                  ->orWhereNull('tahun_ajaran_id');
            });
        }

        $kelompoks = $kelompokQuery->orderBy('nama')->get();

        // 3. Data Tab 3 (Anggota Kelompok Terpilih)
        $selectedKelompok = null;
        $anggotaList = collect();

        if ($this->selectedKelompokId) {
            $selectedKelompok = CustomKelompok::with(['pembimbing', 'tahunAjaran'])
                ->withCount('siswas')
                ->find($this->selectedKelompokId);

            if ($selectedKelompok) {
                $anggotaQuery = $selectedKelompok->siswas()->with(['rombel', 'rayon']);
                if ($this->searchAnggota) {
                    $anggotaQuery->where(function ($q) {
                        $q->where('siswas.nama', 'like', "%{$this->searchAnggota}%")
                          ->orWhere('siswas.nipd', 'like', "%{$this->searchAnggota}%");
                    });
                }
                $anggotaList = $anggotaQuery->orderBy('siswas.nama')->get();
            }
        }

        // 4. Data Siswa Calon Anggota (Modal Manual)
        $candidateSiswas = collect();
        if ($this->showManualModal && $this->selectedKelompokId) {
            $currentMemberIds = $selectedKelompok ? $selectedKelompok->siswas()->pluck('siswas.id')->all() : [];
            $candidateQuery = Siswa::query()
                ->with(['rombel', 'rayon'])
                ->where('is_active', true)
                ->where('status', 'aktif')
                ->whereNotIn('id', $currentMemberIds);

            if ($this->manualSearch) {
                $candidateQuery->where(function ($q) {
                    $q->where('nama', 'like', "%{$this->manualSearch}%")
                      ->orWhere('nipd', 'like', "%{$this->manualSearch}%");
                });
            }

            if ($this->manualFilterRombel) {
                $candidateQuery->where('rombel_id', $this->manualFilterRombel);
            }

            if ($this->manualFilterRayon) {
                $candidateQuery->where('rayon_id', $this->manualFilterRayon);
            }

            $candidateSiswas = $candidateQuery->orderBy('nama')->limit(50)->get();
        }

        // Shared dropdown data
        $rombels = Rombel::orderBy('nama')->get();
        $rayons = Rayon::orderBy('nama')->get();
        $tahunAjarans = TahunAjaran::orderByDesc('nama')->get();
        $users = User::orderBy('name')->get();
        $allKelompoks = CustomKelompok::orderBy('nama')->get(['id', 'nama', 'kode', 'warna']);

        return view('akademik::livewire.custom-kelompok-index', [
            'siswas' => $siswas,
            'kelompoks' => $kelompoks,
            'selectedKelompok' => $selectedKelompok,
            'anggotaList' => $anggotaList,
            'candidateSiswas' => $candidateSiswas,
            'rombels' => $rombels,
            'rayons' => $rayons,
            'tahunAjarans' => $tahunAjarans,
            'users' => $users,
            'allKelompoks' => $allKelompoks,
        ]);
    }
}
