<?php

namespace App\Modules\Ekstrakurikuler\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Modules\Ekstrakurikuler\Models\Ekstrakurikuler;
use App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota;
use App\Modules\Ekstrakurikuler\Models\EkskulPresensi;
use App\Modules\Ekstrakurikuler\Services\EkstrakurikulerService;
use App\Modules\Ekstrakurikuler\Services\EkskulExcelService;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EkstrakurikulerAnggotaIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $activeTab = 'anggota'; // anggota, kepatuhan, import

    public $semester_id;
    public $filterRombel = '';
    public $filterTingkat = ''; // '', 'X', 'XI', 'XII'
    public $filterStatus = 'aktif'; // aktif, pindah, all
    public $search = '';

    // Drill-down UPD: jika null tampilkan daftar seluruh UPD, jika terisi tampilkan detail siswa anggota UPD tsb
    public $selectedEkskulDetailId = null;

    // Quick add modal (tambah manual umum)
    public $showAddModal = false;
    public $selectedEkskulId = '';
    public $searchSiswaQuery = '';
    public $selectedSiswaIds = [];
    public $siswaSearchResults = [];

    // Targeted Enroll Modal (khusus dari tab Audit Kepatuhan tanpa perlu cari siswa)
    public $showEnrollModal = false;
    public $enrollingSiswaId = null;
    public $enrollingSiswaInfo = null;
    public $enrollingMissingPramuka = false;
    public $enrollingTargetEkskulId = '';
    public $pramukaEkskulId = null;

    // Transfer / Mutasi modal (Single atau Massal)
    public $showTransferModal = false;
    public $transferAnggotaId = null;
    public $selectedAnggotaIdsForTransfer = [];
    public $isMassTransfer = false;
    public $targetEkskulId = '';
    public $transferCatatan = '';
    public $transferAnggotaInfo = null;

    // Excel import masal per UPD pilihan
    public $selectedImportEkskulId = '';
    public $importFile;
    public $importPreviewData = null;

    protected $service;
    protected $excelService;

    public function boot(EkstrakurikulerService $service, EkskulExcelService $excelService)
    {
        $this->service = $service;
        $this->excelService = $excelService;
    }

    public function mount()
    {
        $activeSemester = Semester::where('is_active', true)->first();
        if ($activeSemester) {
            $this->semester_id = $activeSemester->id;
        } else {
            $this->semester_id = Semester::latest('id')->first()?->id;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterRombel()
    {
        $this->resetPage();
    }

    public function selectEkskul($ekskulId)
    {
        $this->selectedEkskulDetailId = $ekskulId;
        $this->selectedAnggotaIdsForTransfer = [];
        $this->search = '';
        $this->resetPage();
    }

    public function clearSelectedEkskul()
    {
        $this->selectedEkskulDetailId = null;
        $this->selectedAnggotaIdsForTransfer = [];
        $this->search = '';
        $this->resetPage();
    }

    public function autoEnrollPramuka()
    {
        $result = $this->service->autoEnrollPramukaKelasX($this->semester_id);
        if ($result['success']) {
            session()->flash('message', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function openAddModal(?int $preselectedEkskulId = null)
    {
        $this->reset(['selectedEkskulId', 'searchSiswaQuery', 'selectedSiswaIds', 'siswaSearchResults']);
        $this->selectedEkskulId = $preselectedEkskulId ?: $this->selectedEkskulDetailId ?: '';
        $this->showAddModal = true;
    }

    public function updatedSearchSiswaQuery()
    {
        if (strlen($this->searchSiswaQuery) >= 2) {
            $this->siswaSearchResults = Siswa::with('rombel')
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->searchSiswaQuery . '%')
                        ->orWhere('nisn', 'like', '%' . $this->searchSiswaQuery . '%')
                        ->orWhere('nipd', 'like', '%' . $this->searchSiswaQuery . '%');
                })
                ->take(15)
                ->get();
        } else {
            $this->siswaSearchResults = [];
        }
    }

    public function saveAddAnggota()
    {
        $this->validate([
            'selectedEkskulId' => 'required|exists:ekstrakurikulers,id',
            'selectedSiswaIds' => 'required|array|min:1',
        ]);

        $ekskul = Ekstrakurikuler::findOrFail($this->selectedEkskulId);
        $added = 0;

        foreach ($this->selectedSiswaIds as $siswaId) {
            $anggota = EkstrakurikulerAnggota::firstOrCreate(
                [
                    'ekstrakurikuler_id' => $ekskul->id,
                    'siswa_id' => $siswaId,
                    'semester_id' => $this->semester_id,
                ],
                [
                    'status_keanggotaan' => 'aktif',
                    'nilai_presensi' => 100,
                    'persentase_kehadiran' => 100.00,
                ]
            );

            if ($anggota->status_keanggotaan !== 'aktif') {
                $anggota->update(['status_keanggotaan' => 'aktif']);
            }

            $added++;
        }

        session()->flash('message', "Berhasil mendaftarkan {$added} siswa ke UPD {$ekskul->nama}.");
        $this->showAddModal = false;
    }

    public function openEnrollSiswaModal(int $siswaId, bool $missingPramuka = false)
    {
        $this->enrollingSiswaId = $siswaId;
        $this->enrollingSiswaInfo = Siswa::with('rombel')->findOrFail($siswaId);
        $this->enrollingMissingPramuka = $missingPramuka;

        $pramuka = Ekstrakurikuler::where('is_wajib', true)->where('is_active', true)->first();
        $this->pramukaEkskulId = $pramuka?->id;
        $this->enrollingTargetEkskulId = '';

        $this->showEnrollModal = true;
    }

    public function quickEnrollPramuka()
    {
        if (!$this->enrollingSiswaId || !$this->pramukaEkskulId) {
            return;
        }

        $pramuka = Ekstrakurikuler::find($this->pramukaEkskulId);

        $anggota = EkstrakurikulerAnggota::firstOrCreate(
            [
                'ekstrakurikuler_id' => $this->pramukaEkskulId,
                'siswa_id' => $this->enrollingSiswaId,
                'semester_id' => $this->semester_id,
            ],
            [
                'status_keanggotaan' => 'aktif',
                'nilai_presensi' => 100,
                'persentase_kehadiran' => 100.00,
            ]
        );

        if ($anggota->status_keanggotaan !== 'aktif') {
            $anggota->update(['status_keanggotaan' => 'aktif']);
        }

        $namaSiswa = $this->enrollingSiswaInfo?->nama ?? 'Siswa';
        session()->flash('message', "Siswa {$namaSiswa} berhasil didaftarkan ke {$pramuka->nama}.");
        $this->showEnrollModal = false;
    }

    public function saveEnrollSiswa()
    {
        $this->validate([
            'enrollingTargetEkskulId' => 'required|exists:ekstrakurikulers,id',
        ]);

        $ekskul = Ekstrakurikuler::findOrFail($this->enrollingTargetEkskulId);

        $anggota = EkstrakurikulerAnggota::firstOrCreate(
            [
                'ekstrakurikuler_id' => $ekskul->id,
                'siswa_id' => $this->enrollingSiswaId,
                'semester_id' => $this->semester_id,
            ],
            [
                'status_keanggotaan' => 'aktif',
                'nilai_presensi' => 100,
                'persentase_kehadiran' => 100.00,
            ]
        );

        if ($anggota->status_keanggotaan !== 'aktif') {
            $anggota->update(['status_keanggotaan' => 'aktif']);
        }

        $namaSiswa = $this->enrollingSiswaInfo?->nama ?? 'Siswa';
        session()->flash('message', "Siswa {$namaSiswa} berhasil didaftarkan ke UPD {$ekskul->nama}.");
        $this->showEnrollModal = false;
    }

    public function openTransferModal($anggotaId)
    {
        $this->isMassTransfer = false;
        $this->transferAnggotaId = $anggotaId;
        $this->transferAnggotaInfo = EkstrakurikulerAnggota::with(['siswa.rombel', 'ekstrakurikuler'])->findOrFail($anggotaId);
        $this->targetEkskulId = '';
        $this->transferCatatan = 'Pindah minat bakat UPD pada periode rapor ini';
        $this->showTransferModal = true;
    }

    public function openMassTransferModal()
    {
        if (empty($this->selectedAnggotaIdsForTransfer)) {
            session()->flash('error', 'Silakan pilih minimal satu siswa untuk dipindahkan.');
            return;
        }

        $this->isMassTransfer = true;
        $this->transferAnggotaId = null;
        $this->transferAnggotaInfo = null;
        $this->targetEkskulId = '';
        $this->transferCatatan = 'Mutasi massal siswa ke UPD pilihan baru';
        $this->showTransferModal = true;
    }

    public function executeTransfer()
    {
        $this->validate([
            'targetEkskulId' => 'required|exists:ekstrakurikulers,id',
        ]);

        $targetEkskul = Ekstrakurikuler::findOrFail($this->targetEkskulId);

        if ($this->isMassTransfer) {
            // Mutasi Massal
            $movedCount = 0;
            $anggotasToMove = EkstrakurikulerAnggota::with('siswa')
                ->whereIn('id', $this->selectedAnggotaIdsForTransfer)
                ->get();

            foreach ($anggotasToMove as $oldAnggota) {
                if ($oldAnggota->ekstrakurikuler_id == $targetEkskul->id) {
                    continue; // Skip jika sudah di ekskul yang sama
                }

                $oldAnggota->update([
                    'status_keanggotaan' => 'pindah',
                    'catatan_pindah' => $this->transferCatatan ?: "Pindah ke {$targetEkskul->nama}",
                ]);

                EkstrakurikulerAnggota::firstOrCreate(
                    [
                        'ekstrakurikuler_id' => $targetEkskul->id,
                        'siswa_id' => $oldAnggota->siswa_id,
                        'semester_id' => $this->semester_id,
                    ],
                    [
                        'status_keanggotaan' => 'aktif',
                        'nilai_presensi' => 100,
                        'persentase_kehadiran' => 100.00,
                    ]
                );
                $movedCount++;
            }

            session()->flash('message', "Berhasil memindahkan {$movedCount} siswa ke UPD {$targetEkskul->nama}.");
            $this->selectedAnggotaIdsForTransfer = [];
        } else {
            // Mutasi Single
            $oldAnggota = EkstrakurikulerAnggota::findOrFail($this->transferAnggotaId);

            if ($oldAnggota->ekstrakurikuler_id == $targetEkskul->id) {
                session()->flash('error', 'UPD tujuan harus berbeda dari UPD saat ini.');
                return;
            }

            $oldAnggota->update([
                'status_keanggotaan' => 'pindah',
                'catatan_pindah' => $this->transferCatatan ?: "Pindah ke {$targetEkskul->nama}",
            ]);

            EkstrakurikulerAnggota::firstOrCreate(
                [
                    'ekstrakurikuler_id' => $targetEkskul->id,
                    'siswa_id' => $oldAnggota->siswa_id,
                    'semester_id' => $this->semester_id,
                ],
                [
                    'status_keanggotaan' => 'aktif',
                    'nilai_presensi' => 100,
                    'persentase_kehadiran' => 100.00,
                ]
            );

            session()->flash('message', "Siswa {$oldAnggota->siswa->nama} berhasil dipindahkan ke UPD {$targetEkskul->nama}.");
        }

        $this->showTransferModal = false;
    }

    public function removeAnggota($anggotaId)
    {
        $anggota = EkstrakurikulerAnggota::with('siswa', 'ekstrakurikuler')->findOrFail($anggotaId);
        $namaSiswa = $anggota->siswa->nama ?? 'Siswa';
        $namaEkskul = $anggota->ekstrakurikuler->nama ?? 'UPD';

        $anggota->delete();
        session()->flash('message', "Siswa {$namaSiswa} berhasil dikeluarkan dari UPD {$namaEkskul}.");
    }

    public function downloadTemplatePerEkskul()
    {
        if (!$this->selectedImportEkskulId) {
            session()->flash('error', 'Silakan pilih Unit Pengembangan Diri (UPD) pilihan terlebih dahulu.');
            return;
        }

        $ekskul = Ekstrakurikuler::findOrFail($this->selectedImportEkskulId);
        if ($ekskul->is_wajib) {
            session()->flash('error', 'Import Excel hanya berlaku untuk UPD Pilihan.');
            return;
        }

        $spreadsheet = $this->excelService->generateTemplateForEkskul($ekskul->id);
        $fileName = 'Template_UPD_' . \Illuminate\Support\Str::slug($ekskul->nama, '_') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function updatedImportFile()
    {
        $this->processUploadPreview();
    }

    public function processUploadPreview()
    {
        $this->validate([
            'selectedImportEkskulId' => 'required|exists:ekstrakurikulers,id',
            'importFile' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $path = $this->importFile->getRealPath();
            $cleanTempPath = tempnam(sys_get_temp_dir(), 'srg_upd_') . '.xlsx';
            copy($path, $cleanTempPath);

            $this->importPreviewData = $this->excelService->previewImportPerEkskul(
                $cleanTempPath,
                (int) $this->selectedImportEkskulId,
                $this->semester_id
            );

            @unlink($cleanTempPath);
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal memproses file Excel: ' . $e->getMessage());
            $this->importPreviewData = null;
        }
    }

    public function cancelImportPreview()
    {
        $this->importFile = null;
        $this->importPreviewData = null;
    }

    public function commitImport()
    {
        if (!$this->importPreviewData || empty($this->importPreviewData['rows'])) {
            session()->flash('error', 'Tidak ada data valid untuk diimpor.');
            return;
        }

        $validRows = array_filter($this->importPreviewData['rows'], fn($r) => $r['is_valid'] && !empty($r['siswa_id']));

        if (empty($validRows)) {
            session()->flash('error', 'Tidak ditemukan baris data siswa yang valid untuk diproses.');
            return;
        }

        try {
            $result = $this->excelService->commitImportPerEkskul(
                $validRows,
                $this->importPreviewData['target_ekskul_id'],
                $this->semester_id
            );

            $msg = "Sukses! Berhasil memproses {$result['total_processed']} siswa ke UPD {$result['target_ekskul_nama']}";
            if ($result['transfer_count'] > 0) {
                $msg .= " ({$result['new_count']} siswa baru, {$result['transfer_count']} siswa mutasi dari UPD sebelumnya).";
            } else {
                $msg .= " ({$result['new_count']} siswa baru).";
            }

            session()->flash('message', $msg);
            $this->importFile = null;
            $this->importPreviewData = null;
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan data import: ' . $e->getMessage());
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $semesters = Semester::with('tahunAjaran')->orderByDesc('id')->get();
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama')->get();

        // 1. Ambil seluruh list UPD aktif
        $allEkskuls = Ekstrakurikuler::where('is_active', true)
            ->with(['pembina', 'instrukturUser'])
            ->orderByDesc('is_wajib')
            ->orderBy('nama')
            ->get();

        // Hitung statistik anggota dan sesi per UPD pada semester aktif
        $anggotaCounts = EkstrakurikulerAnggota::where('semester_id', $this->semester_id)
            ->where('status_keanggotaan', 'aktif')
            ->selectRaw('ekstrakurikuler_id, count(*) as total')
            ->groupBy('ekstrakurikuler_id')
            ->pluck('total', 'ekstrakurikuler_id')
            ->all();

        $presensiCounts = EkskulPresensi::where('semester_id', $this->semester_id)
            ->selectRaw('ekstrakurikuler_id, count(*) as total')
            ->groupBy('ekstrakurikuler_id')
            ->pluck('total', 'ekstrakurikuler_id')
            ->all();

        // 2. Data Detail Anggota jika suatu UPD sedang diklik / dipilih
        $selectedEkskulModel = null;
        $anggotas = collect();

        if ($this->selectedEkskulDetailId) {
            $selectedEkskulModel = Ekstrakurikuler::with(['pembina', 'instrukturUser'])->find($this->selectedEkskulDetailId);

            $query = EkstrakurikulerAnggota::with(['ekstrakurikuler', 'siswa.rombel'])
                ->where('semester_id', $this->semester_id)
                ->where('ekstrakurikuler_id', $this->selectedEkskulDetailId);

            if ($this->filterStatus !== 'all') {
                $query->where('status_keanggotaan', $this->filterStatus);
            }

            if ($this->filterRombel) {
                $query->whereHas('siswa', fn($q) => $q->where('rombel_id', $this->filterRombel));
            }

            if ($this->search) {
                $query->whereHas('siswa', function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nisn', 'like', '%' . $this->search . '%')
                        ->orWhere('nipd', 'like', '%' . $this->search . '%');
                });
            }

            $anggotas = $query->orderBy('status_keanggotaan')->paginate(20);
        }

        // 3. Data Audit Kepatuhan
        $complianceData = null;
        if ($this->activeTab === 'kepatuhan') {
            $complianceData = $this->service->checkStudentCompliance(
                $this->semester_id,
                $this->filterRombel ?: null,
                $this->filterTingkat ?: null
            );
        }

        return view('ekstrakurikuler::livewire.anggota-index', [
            'allEkskuls' => $allEkskuls,
            'choiceEkskuls' => $allEkskuls->where('is_wajib', false)->values(),
            'anggotaCounts' => $anggotaCounts,
            'presensiCounts' => $presensiCounts,
            'selectedEkskulModel' => $selectedEkskulModel,
            'anggotas' => $anggotas,
            'semesters' => $semesters,
            'rombels' => $rombels,
            'complianceData' => $complianceData,
        ]);
    }
}
