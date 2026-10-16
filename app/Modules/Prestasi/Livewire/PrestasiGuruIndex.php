<?php

namespace App\Modules\Prestasi\Livewire;

use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Prestasi\Models\PrestasiGuru;
use App\Modules\Prestasi\Services\PrestasiService;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PrestasiGuruIndex extends Component
{
    use WithPagination, WithFileUploads;

    #[Url]
    public string $search = '';

    #[Url]
    public ?string $filterKategori = null;

    #[Url]
    public ?string $filterTingkat = null;

    #[Url]
    public ?int $filterTahunAjaranId = null;

    // Modal Form State
    public bool $showFormModal = false;
    public ?int $prestasiGuruId = null;

    public string $tipePeserta = 'individu';
    public ?string $namaTim = null;
    public array $selectedPegawaiIds = [];
    public ?int $quickSinglePegawaiId = null;

    public string $kategori = 'kejuaraan_gtk';
    public string $penyelenggaraLingkup = 'luar_sekolah';
    public string $penyelenggaraNama = '';
    public string $namaPrestasi = '';
    public ?string $bidang = null;
    public string $tingkat = 'kabupaten_kota';
    public string $peringkat = 'Juara 1';
    public string $tanggalPerolehan = '';
    public ?string $tempat = null;

    public ?string $nomorSertifikat = null;
    public $sertifikatFile = null;
    public $fotoFile = null;
    public ?string $existingSertifikatPath = null;
    public ?string $existingFotoPath = null;
    public ?string $deskripsi = null;

    public ?int $tahunAjaranId = null;
    public ?int $semesterId = null;

    // Preview Modal
    public bool $showPreviewModal = false;
    public ?PrestasiGuru $previewItem = null;

    public function mount()
    {
        $activeTa = AcademicContext::currentTahunAjaran();
        $activeSem = AcademicContext::currentSemester();

        $this->filterTahunAjaranId = $activeTa?->id;
        $this->tanggalPerolehan = date('Y-m-d');
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $activeTa = AcademicContext::currentTahunAjaran();
        $activeSem = AcademicContext::currentSemester();

        $this->tahunAjaranId = $activeTa?->id;
        $this->semesterId = $activeSem?->id;
        $this->tanggalPerolehan = date('Y-m-d');

        // Jika user yang login adalah guru, otomatis preselect dirinya
        $userPegawai = auth()->user()?->pegawai;
        if ($userPegawai) {
            $this->selectedPegawaiIds = [
                ['pegawai_id' => $userPegawai->id, 'peran' => 'individu', 'nama' => $userPegawai->nama_gelar]
            ];
            $this->quickSinglePegawaiId = $userPegawai->id;
        }

        $this->showFormModal = true;
    }

    public function openEditModal(int $id)
    {
        $this->resetForm();
        $item = PrestasiGuru::with('pegawais')->findOrFail($id);

        $this->prestasiGuruId = $item->id;
        $this->tahunAjaranId = $item->tahun_ajaran_id;
        $this->semesterId = $item->semester_id;
        $this->tipePeserta = $item->tipe_peserta;
        $this->namaTim = $item->nama_tim;

        $this->selectedPegawaiIds = [];
        foreach ($item->pegawais as $p) {
            $this->selectedPegawaiIds[] = [
                'pegawai_id' => $p->id,
                'peran' => $p->pivot->peran ?? 'anggota',
                'nama' => $p->nama_gelar,
            ];
        }
        if (!empty($this->selectedPegawaiIds)) {
            $this->quickSinglePegawaiId = $this->selectedPegawaiIds[0]['pegawai_id'];
        }

        $this->kategori = $item->kategori;
        $this->penyelenggaraLingkup = $item->penyelenggara_lingkup;
        $this->penyelenggaraNama = $item->penyelenggara_nama;
        $this->namaPrestasi = $item->nama_prestasi;
        $this->bidang = $item->bidang;
        $this->tingkat = $item->tingkat;
        $this->peringkat = $item->peringkat;
        $this->tanggalPerolehan = $item->tanggal_perolehan?->format('Y-m-d') ?? date('Y-m-d');
        $this->tempat = $item->tempat;
        $this->nomorSertifikat = $item->nomor_sertifikat;
        $this->existingSertifikatPath = $item->sertifikat_file_path;
        $this->existingFotoPath = $item->foto_file_path;
        $this->deskripsi = $item->deskripsi;

        $this->showFormModal = true;
    }

    public function updatedQuickSinglePegawaiId($pegawaiId)
    {
        if ($pegawaiId && $this->tipePeserta === 'individu') {
            $pegawai = Pegawai::find($pegawaiId);
            if ($pegawai) {
                $this->selectedPegawaiIds = [
                    ['pegawai_id' => $pegawai->id, 'peran' => 'individu', 'nama' => $pegawai->nama_gelar]
                ];
            }
        }
    }

    public function addPegawai(int $pegawaiId)
    {
        $pegawai = Pegawai::find($pegawaiId);
        if (!$pegawai) return;

        foreach ($this->selectedPegawaiIds as $item) {
            if ($item['pegawai_id'] == $pegawaiId) return;
        }

        $peran = ($this->tipePeserta === 'individu' || empty($this->selectedPegawaiIds)) ? 'ketua' : 'anggota';
        if ($this->tipePeserta === 'individu') {
            $peran = 'individu';
            $this->selectedPegawaiIds = [];
        }

        $this->selectedPegawaiIds[] = [
            'pegawai_id' => $pegawai->id,
            'peran' => $peran,
            'nama' => $pegawai->nama_gelar,
        ];
    }

    public function removePegawai(int $index)
    {
        unset($this->selectedPegawaiIds[$index]);
        $this->selectedPegawaiIds = array_values($this->selectedPegawaiIds);
    }

    public function setPeringkatTemplate(string $text)
    {
        $this->peringkat = $text;
    }

    public function save()
    {
        // Jika mode individu dan memilih single dropdown
        if ($this->tipePeserta === 'individu' && $this->quickSinglePegawaiId && empty($this->selectedPegawaiIds)) {
            $this->updatedQuickSinglePegawaiId($this->quickSinglePegawaiId);
        }

        $this->validate([
            'namaPrestasi' => 'required|string|max:255',
            'penyelenggaraNama' => 'required|string|max:255',
            'peringkat' => 'required|string|max:255',
            'tanggalPerolehan' => 'required|date',
            'tahunAjaranId' => 'required|exists:tahun_ajarans,id',
            'semesterId' => 'required|exists:semesters,id',
            'selectedPegawaiIds' => 'required|array|min:1',
            'sertifikatFile' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'fotoFile' => 'nullable|image|max:5120',
        ], [
            'selectedPegawaiIds.min' => 'Wajib memilih minimal 1 guru/pegawai.',
            'namaPrestasi.required' => 'Nama kegiatan / karya / prestasi wajib diisi.',
            'penyelenggaraNama.required' => 'Pihak penyelenggara / pemberi penghargaan wajib diisi.',
            'peringkat.required' => 'Capaian / peringkat wajib diisi.',
        ]);

        $sertifikatPath = $this->existingSertifikatPath;
        if ($this->sertifikatFile) {
            $sertifikatPath = $this->sertifikatFile->store('prestasi/guru/sertifikat', 'public');
        }

        $fotoPath = $this->existingFotoPath;
        if ($this->fotoFile) {
            $fotoPath = $this->fotoFile->store('prestasi/guru/foto', 'public');
        }

        $data = [
            'tahun_ajaran_id' => $this->tahunAjaranId,
            'semester_id' => $this->semesterId,
            'tipe_peserta' => $this->tipePeserta,
            'nama_tim' => $this->tipePeserta === 'kelompok' ? $this->namaTim : null,
            'kategori' => $this->kategori,
            'penyelenggara_lingkup' => $this->penyelenggaraLingkup,
            'penyelenggara_nama' => $this->penyelenggaraNama,
            'nama_prestasi' => $this->namaPrestasi,
            'bidang' => $this->bidang,
            'tingkat' => $this->tingkat,
            'peringkat' => $this->peringkat,
            'tanggal_perolehan' => $this->tanggalPerolehan,
            'tempat' => $this->tempat,
            'nomor_sertifikat' => $this->nomorSertifikat,
            'sertifikat_file_path' => $sertifikatPath,
            'foto_file_path' => $fotoPath,
            'deskripsi' => $this->deskripsi,
            'created_by' => auth()->id(),
        ];

        if ($this->prestasiGuruId) {
            $prestasi = PrestasiGuru::findOrFail($this->prestasiGuruId);
            $prestasi->update($data);
        } else {
            $prestasi = PrestasiGuru::create($data);
        }

        $pivotData = [];
        foreach ($this->selectedPegawaiIds as $item) {
            $pivotData[$item['pegawai_id']] = [
                'peran' => $item['peran'] ?? 'anggota',
            ];
        }
        $prestasi->pegawais()->sync($pivotData);

        $this->showFormModal = false;
        $this->resetForm();
        session()->flash('message', 'Data prestasi Guru & Tendik berhasil disimpan!');
    }

    public function delete(int $id)
    {
        $prestasi = PrestasiGuru::findOrFail($id);
        if ($prestasi->sertifikat_file_path) {
            Storage::disk('public')->delete($prestasi->sertifikat_file_path);
        }
        if ($prestasi->foto_file_path) {
            Storage::disk('public')->delete($prestasi->foto_file_path);
        }
        $prestasi->pegawais()->detach();
        $prestasi->delete();

        session()->flash('message', 'Data prestasi Guru berhasil dihapus.');
    }

    public function openPreview(int $id)
    {
        $this->previewItem = PrestasiGuru::with(['pegawais', 'tahunAjaran', 'semester'])->findOrFail($id);
        $this->showPreviewModal = true;
    }

    public function exportExcel()
    {
        $filters = [
            'tahun_ajaran_id' => $this->filterTahunAjaranId,
        ];

        $service = app(PrestasiService::class);
        $spreadsheet = $service->exportGuruExcel($filters);

        $filename = 'Data_Prestasi_Guru_GTK_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function resetForm()
    {
        $this->prestasiGuruId = null;
        $this->tipePeserta = 'individu';
        $this->namaTim = null;
        $this->selectedPegawaiIds = [];
        $this->quickSinglePegawaiId = null;
        $this->kategori = 'kejuaraan_gtk';
        $this->penyelenggaraLingkup = 'luar_sekolah';
        $this->penyelenggaraNama = '';
        $this->namaPrestasi = '';
        $this->bidang = null;
        $this->tingkat = 'kabupaten_kota';
        $this->peringkat = 'Juara 1';
        $this->tanggalPerolehan = date('Y-m-d');
        $this->tempat = null;
        $this->nomorSertifikat = null;
        $this->sertifikatFile = null;
        $this->fotoFile = null;
        $this->existingSertifikatPath = null;
        $this->existingFotoPath = null;
        $this->deskripsi = null;
        $this->resetErrorBag();
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $query = PrestasiGuru::with(['pegawais', 'tahunAjaran', 'semester'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama_prestasi', 'like', "%{$this->search}%")
                        ->orWhere('penyelenggara_nama', 'like', "%{$this->search}%")
                        ->orWhere('peringkat', 'like', "%{$this->search}%")
                        ->orWhere('nama_tim', 'like', "%{$this->search}%")
                        ->orWhereHas('pegawais', function ($sq) {
                            $sq->where('nama_lengkap', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->filterKategori, fn($q) => $q->where('kategori', $this->filterKategori))
            ->when($this->filterTingkat, fn($q) => $q->where('tingkat', $this->filterTingkat))
            ->when($this->filterTahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $this->filterTahunAjaranId))
            ->orderBy('tanggal_perolehan', 'desc');

        $prestasis = $query->paginate(12);

        $pegawais = Pegawai::where('is_active', true)->orderBy('nama_lengkap')->get();
        $tahunAjarans = TahunAjaran::orderBy('nama', 'desc')->get();
        $semesters = Semester::when($this->filterTahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $this->filterTahunAjaranId))->get();

        return view('prestasi::livewire.prestasi-guru-index', [
            'prestasis' => $prestasis,
            'pegawais' => $pegawais,
            'tahunAjarans' => $tahunAjarans,
            'semesters' => $semesters,
        ]);
    }
}
