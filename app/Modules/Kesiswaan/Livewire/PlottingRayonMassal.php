<?php

namespace App\Modules\Kesiswaan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kesiswaan\Models\RayonSiswa;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kesiswaan\Services\PlottingRayonExcelService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Layout('components.layouts.app')]
#[Title('Plotting Rayon Massal')]
class PlottingRayonMassal extends Component
{
    use GuardsPermission, WithPagination, WithFileUploads;

    // Filters
    public string $filterStatusRayon = 'belum_ada'; // 'belum_ada' atau 'semua'
    public string $filterKecamatan = '';
    public string $filterKelurahan = '';
    public ?int $filterRombel = null;
    public string $search = '';

    // Selection
    public array $selectedSiswaIds = [];
    public bool $selectAll = false;

    // Target Rayon
    public ?int $targetRayonId = null;

    // Import Excel Modal
    public bool $showImportModal = false;
    public ?int $importTargetRayonId = null;
    public $fileExcel = null;
    public ?array $importResult = null;

    // Reset Plotting Rayon Modal
    public bool $showResetModal = false;
    public string $resetScope = 'terpilih'; // 'terpilih', 'filter', 'semua'
    public string $resetMode = 'kosongkan';  // 'kosongkan', 'acak'
    public string $resetConfirmText = '';

    public function updatedFilterStatusRayon(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedFilterKecamatan(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedFilterKelurahan(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedFilterRombel(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedSiswaIds = $this->getQuery()->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selectedSiswaIds = [];
        }
    }

    protected function getQuery()
    {
        $query = Siswa::query();

        if ($this->filterStatusRayon === 'belum_ada') {
            $query->whereNull('rayon_id');
        }

        if ($this->filterKecamatan) {
            $query->where('kecamatan', $this->filterKecamatan);
        }

        if ($this->filterKelurahan) {
            $query->where('kelurahan', $this->filterKelurahan);
        }

        if ($this->filterRombel) {
            $query->where('rombel_id', $this->filterRombel);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                    ->orWhere('nisn', 'like', "%{$this->search}%");
            });
        }

        return $query;
    }

    public function applyPlotting(): void
    {
        $this->guard('kesiswaan.siswa.edit');

        if (empty($this->selectedSiswaIds)) {
            session()->flash('error', 'Pilih minimal satu siswa untuk di-plot ke rayon.');
            return;
        }

        if (!$this->targetRayonId) {
            session()->flash('error', 'Silakan pilih Rayon tujuan.');
            return;
        }

        $rayon = Rayon::findOrFail($this->targetRayonId);
        $count = count($this->selectedSiswaIds);

        DB::transaction(function () use ($rayon) {
            foreach ($this->selectedSiswaIds as $siswaId) {
                $siswa = Siswa::find($siswaId);
                if ($siswa) {
                    $siswa->update(['rayon_id' => $rayon->id]);
                    RayonSiswa::create([
                        'siswa_id' => $siswa->id,
                        'rayon_id' => $rayon->id,
                        'tgl_mulai' => now(),
                        'alasan_pindah' => 'Plotting Rayon Massal',
                    ]);
                }
            }
        });

        $this->reset(['selectedSiswaIds', 'selectAll']);
        session()->flash('status', "Berhasil! Sebanyak {$count} siswa berhasil dialokasikan ke rayon {$rayon->nama}.");
    }

    // ------------------------------------------------------------------------
    // FITUR RESET PLOTTING RAYON
    // ------------------------------------------------------------------------

    public function openResetModal(): void
    {
        $this->guard('kesiswaan.siswa.edit');

        if (!empty($this->selectedSiswaIds)) {
            $this->resetScope = 'terpilih';
        } elseif ($this->filterKecamatan || $this->filterKelurahan || $this->filterRombel || $this->search) {
            $this->resetScope = 'filter';
        } else {
            $this->resetScope = 'semua';
        }

        $this->resetMode = 'kosongkan';
        $this->resetConfirmText = '';
        $this->showResetModal = true;
    }

    public function closeResetModal(): void
    {
        $this->showResetModal = false;
        $this->resetConfirmText = '';
    }

    public function getResetTargetCountProperty(): int
    {
        return match ($this->resetScope) {
            'terpilih' => ($this->resetMode === 'kosongkan')
                ? Siswa::whereIn('id', $this->selectedSiswaIds)->whereNotNull('rayon_id')->count()
                : count($this->selectedSiswaIds),
            'filter' => ($this->resetMode === 'kosongkan')
                ? $this->getQuery()->whereNotNull('rayon_id')->count()
                : $this->getQuery()->count(),
            'semua' => ($this->resetMode === 'kosongkan')
                ? Siswa::where('status', 'aktif')->whereNotNull('rayon_id')->count()
                : Siswa::where('status', 'aktif')->count(),
            default => 0,
        };
    }

    public function resetSelectedRayon(): void
    {
        $this->guard('kesiswaan.siswa.edit');

        if (empty($this->selectedSiswaIds)) {
            session()->flash('error', 'Pilih minimal satu siswa untuk mengosongkan rayon.');
            return;
        }

        $count = count($this->selectedSiswaIds);

        DB::transaction(function () {
            RayonSiswa::whereIn('siswa_id', $this->selectedSiswaIds)
                ->whereNull('tgl_selesai')
                ->update([
                    'tgl_selesai' => now(),
                    'alasan_pindah' => 'Reset Plotting Rayon Terpilih',
                ]);

            Siswa::whereIn('id', $this->selectedSiswaIds)->update(['rayon_id' => null]);
        });

        $this->reset(['selectedSiswaIds', 'selectAll']);
        $this->resetPage();
        session()->flash('status', "Berhasil! Rayon untuk {$count} siswa terpilih telah di-reset (dikosongkan).");
    }

    public function executeResetRayon(): void
    {
        $this->guard('kesiswaan.siswa.edit');
        $this->resetErrorBag();

        if ($this->resetScope === 'semua' && strtoupper(trim($this->resetConfirmText)) !== 'RESET') {
            $this->addError('resetConfirmText', 'Ketik kata "RESET" dengan huruf kapital untuk mengonfirmasi.');
            return;
        }

        $query = Siswa::query();

        if ($this->resetScope === 'terpilih') {
            if (empty($this->selectedSiswaIds)) {
                session()->flash('error', 'Tidak ada siswa yang dipilih.');
                return;
            }
            $query->whereIn('id', $this->selectedSiswaIds);
        } elseif ($this->resetScope === 'filter') {
            $query = $this->getQuery();
            if ($this->resetMode === 'kosongkan') {
                $query->whereNotNull('rayon_id');
            }
        } else {
            $query->where('status', 'aktif');
            if ($this->resetMode === 'kosongkan') {
                $query->whereNotNull('rayon_id');
            }
        }

        $targetSiswaIds = $query->pluck('id')->toArray();
        $count = count($targetSiswaIds);

        if ($count === 0) {
            session()->flash('error', 'Tidak ada data siswa yang perlu di-reset.');
            $this->showResetModal = false;
            return;
        }

        DB::transaction(function () use ($targetSiswaIds) {
            if ($this->resetMode === 'kosongkan') {
                RayonSiswa::whereIn('siswa_id', $targetSiswaIds)
                    ->whereNull('tgl_selesai')
                    ->update([
                        'tgl_selesai' => now(),
                        'alasan_pindah' => 'Reset Plotting Rayon',
                    ]);

                Siswa::whereIn('id', $targetSiswaIds)->update(['rayon_id' => null]);
            } elseif ($this->resetMode === 'acak') {
                $rayons = Rayon::all();
                if ($rayons->isEmpty()) {
                    throw new \Exception('Tidak ada data Rayon yang terdaftar di sistem.');
                }

                $rayonList = $rayons->values();
                $rayonCount = $rayonList->count();

                RayonSiswa::whereIn('siswa_id', $targetSiswaIds)
                    ->whereNull('tgl_selesai')
                    ->update([
                        'tgl_selesai' => now(),
                        'alasan_pindah' => 'Reset & Distribusi Ulang Rayon Acak',
                    ]);

                shuffle($targetSiswaIds);
                foreach ($targetSiswaIds as $i => $siswaId) {
                    $rayon = $rayonList[$i % $rayonCount];
                    Siswa::where('id', $siswaId)->update(['rayon_id' => $rayon->id]);
                    RayonSiswa::create([
                        'siswa_id' => $siswaId,
                        'rayon_id' => $rayon->id,
                        'tgl_mulai' => now(),
                        'alasan_pindah' => 'Distribusi Ulang Rayon Acak',
                    ]);
                }
            }
        });

        $this->reset(['selectedSiswaIds', 'selectAll', 'showResetModal', 'resetConfirmText']);
        $this->resetPage();

        $actionText = ($this->resetMode === 'kosongkan')
            ? "berhasil di-reset (dikosongkan) dari pembagian rayon."
            : "berhasil diatur ulang dan didistribusikan secara acak merata ke seluruh rayon.";

        session()->flash('status', "Sukses! Sebanyak {$count} siswa {$actionText}");
    }

    // ------------------------------------------------------------------------
    // IMPORT & TEMPLATE EXCEL
    // ------------------------------------------------------------------------

    public function openImportModal(): void
    {
        $this->guard('kesiswaan.siswa.edit');
        $this->reset(['fileExcel', 'importResult']);
        $this->importTargetRayonId = $this->targetRayonId;
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->reset(['fileExcel', 'importResult']);
        $this->showImportModal = false;
    }

    public function downloadTemplate(string $type = 'kosong')
    {
        $this->guard('kesiswaan.siswa.lihat');
        $service = new PlottingRayonExcelService();

        if ($type === 'prefill') {
            $siswas = $this->getQuery()->with(['rombel', 'rayon'])->orderBy('kecamatan')->orderBy('kelurahan')->orderBy('nama')->get();
            $spreadsheet = $service->generateTemplatePrefill($siswas);
            $filename = 'Template_Plotting_Rayon_Filtered_' . date('Ymd_His') . '.xlsx';
        } else {
            $spreadsheet = $service->generateTemplateKosong();
            $filename = 'Template_Plotting_Rayon_Kosong.xlsx';
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function importExcel(): void
    {
        $this->guard('kesiswaan.siswa.edit');

        $this->validate([
            'importTargetRayonId' => ['required', 'exists:rayons,id'],
            'fileExcel' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ], [
            'importTargetRayonId.required' => 'Pilih rayon tujuan terlebih dahulu.',
            'importTargetRayonId.exists' => 'Rayon tujuan tidak valid.',
            'fileExcel.required' => 'Unggah berkas Excel terlebih dahulu.',
            'fileExcel.mimes' => 'Berkas harus berformat .xlsx, .xls, atau .csv.',
        ]);

        $service = new PlottingRayonExcelService();
        $path = $this->fileExcel->getRealPath();

        $result = $service->importAndPlot($path, (int) $this->importTargetRayonId);
        $this->importResult = $result;

        $this->reset('fileExcel');

        if ($result['updated_count'] > 0) {
            session()->flash('status', "Berhasil! {$result['updated_count']} siswa dialokasikan ke Rayon {$result['rayon_nama']}.");
        }
    }

    public function render()
    {
        $query = $this->getQuery()->with(['rombel', 'rayon']);
        $siswas = $query->orderBy('kecamatan')->orderBy('kelurahan')->orderBy('nama')->paginate(25);

        $rayons = Rayon::orderBy('nama')->get();
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama')->get();

        $kecamatans = Siswa::whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');

        $kelurahans = collect();
        if ($this->filterKecamatan) {
            $kelurahans = Siswa::where('kecamatan', $this->filterKecamatan)
                ->whereNotNull('kelurahan')
                ->where('kelurahan', '!=', '')
                ->distinct()
                ->orderBy('kelurahan')
                ->pluck('kelurahan');
        }

        return view('kesiswaan::livewire.plotting-rayon-massal', [
            'siswas' => $siswas,
            'rayons' => $rayons,
            'rombels' => $rombels,
            'kecamatans' => $kecamatans,
            'kelurahans' => $kelurahans,
            'resetTargetCount' => $this->resetTargetCount,
        ]);
    }
}
