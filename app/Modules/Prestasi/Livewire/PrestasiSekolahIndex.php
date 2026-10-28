<?php

namespace App\Modules\Prestasi\Livewire;

use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Prestasi\Models\PrestasiSekolah;
use App\Modules\Prestasi\Services\PrestasiService;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use App\Core\Concerns\GuardsPermission;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PrestasiSekolahIndex extends Component
{
    use WithPagination, WithFileUploads, GuardsPermission;

    #[Url]
    public string $search = '';

    #[Url]
    public ?string $filterKategori = null;

    #[Url]
    public ?string $filterTingkat = null;

    // Modal Form State
    public bool $showFormModal = false;
    public ?int $prestasiSekolahId = null;

    public ?int $tahunAjaranId = null;
    public string $kategori = 'penghargaan_khusus';
    public string $penyelenggaraNama = '';
    public string $namaPrestasi = '';
    public ?string $bidang = null;
    public string $tingkat = 'nasional';
    public string $capaian = 'Juara 1';
    public string $tanggalPerolehan = '';
    public ?string $masaBerlakuSampai = null;
    public ?string $nomorSk = null;
    public $sertifikatFile = null;
    public $fotoFile = null;
    public ?string $existingSertifikatPath = null;
    public ?string $existingFotoPath = null;
    public ?string $deskripsi = null;

    // Preview Modal
    public bool $showPreviewModal = false;
    public ?PrestasiSekolah $previewItem = null;

    public function mount()
    {
        $activeTa = AcademicContext::currentTahunAjaran();
        $this->tahunAjaranId = $activeTa?->id;
        $this->tanggalPerolehan = date('Y-m-d');
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $activeTa = AcademicContext::currentTahunAjaran();
        $this->tahunAjaranId = $activeTa?->id;
        $this->tanggalPerolehan = date('Y-m-d');
        $this->showFormModal = true;
    }

    public function openEditModal(int $id)
    {
        $this->resetForm();
        $item = PrestasiSekolah::findOrFail($id);

        $this->prestasiSekolahId = $item->id;
        $this->tahunAjaranId = $item->tahun_ajaran_id;
        $this->kategori = $item->kategori;
        $this->penyelenggaraNama = $item->penyelenggara_nama;
        $this->namaPrestasi = $item->nama_prestasi;
        $this->bidang = $item->bidang;
        $this->tingkat = $item->tingkat;
        $this->capaian = $item->capaian;
        $this->tanggalPerolehan = $item->tanggal_perolehan?->format('Y-m-d') ?? date('Y-m-d');
        $this->masaBerlakuSampai = $item->masa_berlaku_sampai?->format('Y-m-d');
        $this->nomorSk = $item->nomor_sk;
        $this->existingSertifikatPath = $item->sertifikat_file_path;
        $this->existingFotoPath = $item->foto_file_path;
        $this->deskripsi = $item->deskripsi;

        $this->showFormModal = true;
    }

    public function setCapaianTemplate(string $text)
    {
        $this->capaian = $text;
    }

    public function save()
    {
        $this->guard($this->prestasiSekolahId ? 'prestasi.sekolah.edit' : 'prestasi.sekolah.tambah');

        $this->validate([
            'namaPrestasi' => 'required|string|max:255',
            'penyelenggaraNama' => 'required|string|max:255',
            'capaian' => 'required|string|max:255',
            'tanggalPerolehan' => 'required|date',
            'tahunAjaranId' => 'required|exists:tahun_ajarans,id',
            'sertifikatFile' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'fotoFile' => 'nullable|image|max:5120',
        ], [
            'namaPrestasi.required' => 'Nama prestasi / akreditasi sekolah wajib diisi.',
            'penyelenggaraNama.required' => 'Penyelenggara / lembaga penerbit wajib diisi.',
            'capaian.required' => 'Capaian / predikat wajib diisi.',
        ]);

        $sertifikatPath = $this->existingSertifikatPath;
        if ($this->sertifikatFile) {
            $sertifikatPath = $this->sertifikatFile->store('prestasi/sekolah/sertifikat', 'public');
        }

        $fotoPath = $this->existingFotoPath;
        if ($this->fotoFile) {
            $fotoPath = $this->fotoFile->store('prestasi/sekolah/foto', 'public');
        }

        $data = [
            'tahun_ajaran_id' => $this->tahunAjaranId,
            'kategori' => $this->kategori,
            'penyelenggara_nama' => $this->penyelenggaraNama,
            'nama_prestasi' => $this->namaPrestasi,
            'bidang' => $this->bidang,
            'tingkat' => $this->tingkat,
            'capaian' => $this->capaian,
            'tanggal_perolehan' => $this->tanggalPerolehan,
            'masa_berlaku_sampai' => $this->masaBerlakuSampai ?: null,
            'nomor_sk' => $this->nomorSk,
            'sertifikat_file_path' => $sertifikatPath,
            'foto_file_path' => $fotoPath,
            'deskripsi' => $this->deskripsi,
            'created_by' => auth()->id(),
        ];

        if ($this->prestasiSekolahId) {
            $item = PrestasiSekolah::findOrFail($this->prestasiSekolahId);
            $item->update($data);
        } else {
            PrestasiSekolah::create($data);
        }

        $this->showFormModal = false;
        $this->resetForm();
        session()->flash('message', 'Prestasi Sekolah berhasil disimpan!');
    }

    public function delete(int $id)
    {
        $this->guard('prestasi.sekolah.hapus');

        $item = PrestasiSekolah::findOrFail($id);
        if ($item->sertifikat_file_path) {
            Storage::disk('public')->delete($item->sertifikat_file_path);
        }
        if ($item->foto_file_path) {
            Storage::disk('public')->delete($item->foto_file_path);
        }
        $item->delete();

        session()->flash('message', 'Data prestasi sekolah berhasil dihapus.');
    }

    public function openPreview(int $id)
    {
        $this->previewItem = PrestasiSekolah::with('tahunAjaran')->findOrFail($id);
        $this->showPreviewModal = true;
    }

    public function exportExcel()
    {
        $service = app(PrestasiService::class);
        $spreadsheet = $service->exportSekolahExcel();

        $filename = 'Data_Prestasi_Sekolah_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function resetForm()
    {
        $this->prestasiSekolahId = null;
        $this->kategori = 'penghargaan_khusus';
        $this->penyelenggaraNama = '';
        $this->namaPrestasi = '';
        $this->bidang = null;
        $this->tingkat = 'nasional';
        $this->capaian = 'Juara 1';
        $this->tanggalPerolehan = date('Y-m-d');
        $this->masaBerlakuSampai = null;
        $this->nomorSk = null;
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
        $query = PrestasiSekolah::with('tahunAjaran')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama_prestasi', 'like', "%{$this->search}%")
                        ->orWhere('penyelenggara_nama', 'like', "%{$this->search}%")
                        ->orWhere('capaian', 'like', "%{$this->search}%")
                        ->orWhere('nomor_sk', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterKategori, fn($q) => $q->where('kategori', $this->filterKategori))
            ->when($this->filterTingkat, fn($q) => $q->where('tingkat', $this->filterTingkat))
            ->orderBy('tanggal_perolehan', 'desc');

        $prestasis = $query->paginate(12);
        $tahunAjarans = TahunAjaran::orderBy('nama', 'desc')->get();

        return view('prestasi::livewire.prestasi-sekolah-index', [
            'prestasis' => $prestasis,
            'tahunAjarans' => $tahunAjarans,
        ]);
    }
}
