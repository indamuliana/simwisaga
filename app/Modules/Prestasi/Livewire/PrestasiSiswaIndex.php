<?php

namespace App\Modules\Prestasi\Livewire;

use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Prestasi\Models\Prestasi;
use App\Modules\Prestasi\Models\PrestasiSiswaAnggota;
use App\Modules\Prestasi\Services\PrestasiService;
use App\Modules\Sistem\Models\PeriodeRapor;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PrestasiSiswaIndex extends Component
{
    use WithPagination, WithFileUploads;

    // Filter & Search
    #[Url]
    public string $search = '';

    #[Url]
    public ?int $filterRombelId = null;

    #[Url]
    public ?int $filterRayonId = null;

    #[Url]
    public ?string $filterKategori = null;

    #[Url]
    public ?string $filterTingkat = null;

    #[Url]
    public ?int $filterTahunAjaranId = null;

    #[Url]
    public ?int $filterSemesterId = null;

    #[Url]
    public ?int $filterPeriodeRaporId = null;

    // Modal Form State
    public bool $showFormModal = false;
    public ?int $prestasiId = null;

    // Form fields
    public string $tipePeserta = 'individu'; // 'individu' | 'kelompok'
    public ?string $namaTim = null;
    public array $selectedSiswaIds = []; // [ ['siswa_id' => 123, 'peran' => 'ketua', 'nama' => '...'] ]
    public string $siswaSearchQuery = '';

    public string $kategori = 'non_akademik'; // 'akademik', 'non_akademik', 'sertifikasi'
    public string $penyelenggaraLingkup = 'luar_sekolah'; // 'dalam_sekolah', 'luar_sekolah'
    public string $penyelenggaraNama = '';
    public string $namaPrestasi = '';
    public ?string $bidang = 'Teknologi & Komputer';
    public string $tingkat = 'kabupaten_kota';
    public string $peringkat = 'Juara 1'; // Bisa template atau custom
    public string $tanggalPerolehan = '';
    public ?string $tanggalSelesai = null;
    public ?string $tempat = null;

    // Guru Pembimbing
    public string $pembimbingType = 'pegawai'; // 'pegawai' | 'manual'
    public ?int $pembimbingPegawaiId = null;
    public ?string $pembimbingNamaManual = null;

    public ?string $nomorSertifikat = null;
    public $sertifikatFile = null; // Upload file
    public $fotoFile = null; // Upload foto
    public ?string $existingSertifikatPath = null;
    public ?string $existingFotoPath = null;
    public ?string $deskripsi = null;

    public ?int $tahunAjaranId = null;
    public ?int $semesterId = null;
    public ?int $periodeRaporId = null;

    // Preview Modal
    public bool $showPreviewModal = false;
    public ?Prestasi $previewItem = null;

    public function mount()
    {
        $activeTa = AcademicContext::currentTahunAjaran();
        $activeSem = AcademicContext::currentSemester();

        $this->filterTahunAjaranId = $activeTa?->id;
        $this->filterSemesterId = $activeSem?->id;
        $this->tanggalPerolehan = date('Y-m-d');
    }

    public function updatedTanggalPerolehan($val)
    {
        if ($val && $this->tahunAjaranId && $this->semesterId) {
            $service = app(PrestasiService::class);
            $matched = $service->resolvePeriodeRapor($val, $this->tahunAjaranId, $this->semesterId);
            if ($matched) {
                $this->periodeRaporId = $matched;
            }
        }
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $activeTa = AcademicContext::currentTahunAjaran();
        $activeSem = AcademicContext::currentSemester();

        $this->tahunAjaranId = $activeTa?->id;
        $this->semesterId = $activeSem?->id;
        $this->tanggalPerolehan = date('Y-m-d');

        // Otomatis deteksi periode jika ada
        if ($this->tahunAjaranId && $this->semesterId) {
            $service = app(PrestasiService::class);
            $this->periodeRaporId = $service->resolvePeriodeRapor($this->tanggalPerolehan, $this->tahunAjaranId, $this->semesterId);
        }

        $this->showFormModal = true;
    }

    public function openEditModal(int $id)
    {
        $this->resetForm();
        $item = Prestasi::with(['siswas', 'pembimbingPegawai'])->findOrFail($id);

        $this->prestasiId = $item->id;
        $this->tahunAjaranId = $item->tahun_ajaran_id;
        $this->semesterId = $item->semester_id;
        $this->periodeRaporId = $item->periode_rapor_id;
        $this->tipePeserta = $item->tipe_peserta;
        $this->namaTim = $item->nama_tim;

        $this->selectedSiswaIds = [];
        foreach ($item->siswas as $s) {
            $this->selectedSiswaIds[] = [
                'siswa_id' => $s->id,
                'peran' => $s->pivot->peran ?? 'anggota',
                'nama' => $s->nama . ' (' . ($s->rombel?->nama ?? '-') . ')',
            ];
        }

        $this->kategori = $item->kategori;
        $this->penyelenggaraLingkup = $item->penyelenggara_lingkup;
        $this->penyelenggaraNama = $item->penyelenggara_nama;
        $this->namaPrestasi = $item->nama_prestasi;
        $this->bidang = $item->bidang;
        $this->tingkat = $item->tingkat;
        $this->peringkat = $item->peringkat;
        $this->tanggalPerolehan = $item->tanggal_perolehan?->format('Y-m-d') ?? date('Y-m-d');
        $this->tanggalSelesai = $item->tanggal_selesai?->format('Y-m-d');
        $this->tempat = $item->tempat;

        if ($item->pembimbing_pegawai_id) {
            $this->pembimbingType = 'pegawai';
            $this->pembimbingPegawaiId = $item->pembimbing_pegawai_id;
        } else {
            $this->pembimbingType = 'manual';
            $this->pembimbingNamaManual = $item->pembimbing_nama_manual;
        }

        $this->nomorSertifikat = $item->nomor_sertifikat;
        $this->existingSertifikatPath = $item->sertifikat_file_path;
        $this->existingFotoPath = $item->foto_file_path;
        $this->deskripsi = $item->deskripsi;

        $this->showFormModal = true;
    }

    public function addSiswa(int $siswaId)
    {
        $siswa = Siswa::with('rombel')->find($siswaId);
        if (!$siswa) return;

        // Cek apakah sudah ada
        foreach ($this->selectedSiswaIds as $item) {
            if ($item['siswa_id'] == $siswaId) return;
        }

        $peran = ($this->tipePeserta === 'individu' || empty($this->selectedSiswaIds)) ? 'ketua' : 'anggota';
        if ($this->tipePeserta === 'individu') {
            $peran = 'individu';
            $this->selectedSiswaIds = []; // Individu hanya 1
        }

        $this->selectedSiswaIds[] = [
            'siswa_id' => $siswa->id,
            'peran' => $peran,
            'nama' => $siswa->nama . ' (' . ($siswa->rombel?->nama ?? '-') . ')',
        ];

        $this->siswaSearchQuery = '';
    }

    public function removeSiswa(int $index)
    {
        unset($this->selectedSiswaIds[$index]);
        $this->selectedSiswaIds = array_values($this->selectedSiswaIds);
    }

    public function setPeringkatTemplate(string $text)
    {
        $this->peringkat = $text;
    }

    public function save()
    {
        $this->validate([
            'namaPrestasi' => 'required|string|max:255',
            'penyelenggaraNama' => 'required|string|max:255',
            'kategori' => 'required|in:akademik,non_akademik,sertifikasi',
            'tingkat' => 'required|in:sekolah,kecamatan,kabupaten_kota,provinsi,nasional,internasional',
            'peringkat' => 'required|string|max:255',
            'tanggalPerolehan' => 'required|date',
            'tahunAjaranId' => 'required|exists:tahun_ajarans,id',
            'semesterId' => 'required|exists:semesters,id',
            'selectedSiswaIds' => 'required|array|min:1',
            'sertifikatFile' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'fotoFile' => 'nullable|image|max:5120',
        ], [
            'selectedSiswaIds.min' => 'Wajib memilih minimal 1 siswa penerima prestasi.',
            'namaPrestasi.required' => 'Nama kegiatan / kejuaraan / sertifikasi wajib diisi.',
            'penyelenggaraNama.required' => 'Nama pihak penyelenggara wajib diisi.',
            'peringkat.required' => 'Capaian atau peringkat wajib diisi.',
        ]);

        $sertifikatPath = $this->existingSertifikatPath;
        if ($this->sertifikatFile) {
            $sertifikatPath = $this->sertifikatFile->store('prestasi/sertifikat', 'public');
        }

        $fotoPath = $this->existingFotoPath;
        if ($this->fotoFile) {
            $fotoPath = $this->fotoFile->store('prestasi/foto', 'public');
        }

        $pembimbingPegId = ($this->pembimbingType === 'pegawai') ? $this->pembimbingPegawaiId : null;
        $pembimbingManual = ($this->pembimbingType === 'manual') ? $this->pembimbingNamaManual : null;

        $data = [
            'tahun_ajaran_id' => $this->tahunAjaranId,
            'semester_id' => $this->semesterId,
            'periode_rapor_id' => $this->periodeRaporId ?: null,
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
            'tanggal_selesai' => $this->tanggalSelesai ?: null,
            'tempat' => $this->tempat,
            'pembimbing_pegawai_id' => $pembimbingPegId,
            'pembimbing_nama_manual' => $pembimbingManual,
            'nomor_sertifikat' => $this->nomorSertifikat,
            'sertifikat_file_path' => $sertifikatPath,
            'foto_file_path' => $fotoPath,
            'deskripsi' => $this->deskripsi,
            'status_verifikasi' => 'approved', // Input admin/guru langsung approved
            'created_by' => auth()->id(),
        ];

        if ($this->prestasiId) {
            $prestasi = Prestasi::findOrFail($this->prestasiId);
            $prestasi->update($data);
        } else {
            $prestasi = Prestasi::create($data);
        }

        // Sinkronisasi Anggota Siswa
        $pivotData = [];
        foreach ($this->selectedSiswaIds as $item) {
            $pivotData[$item['siswa_id']] = [
                'peran' => $item['peran'] ?? 'anggota',
            ];
        }
        $prestasi->siswas()->sync($pivotData);

        $this->showFormModal = false;
        $this->resetForm();
        session()->flash('message', 'Data prestasi siswa berhasil disimpan dan siap tercantum di buku rapor!');
    }

    public function delete(int $id)
    {
        $prestasi = Prestasi::findOrFail($id);
        if ($prestasi->sertifikat_file_path) {
            Storage::disk('public')->delete($prestasi->sertifikat_file_path);
        }
        if ($prestasi->foto_file_path) {
            Storage::disk('public')->delete($prestasi->foto_file_path);
        }
        $prestasi->siswas()->detach();
        $prestasi->delete();

        session()->flash('message', 'Data prestasi berhasil dihapus.');
    }

    public function openPreview(int $id)
    {
        $this->previewItem = Prestasi::with(['siswas.rombel', 'siswas.rayon', 'pembimbingPegawai', 'tahunAjaran', 'semester', 'periodeRapor'])->findOrFail($id);
        $this->showPreviewModal = true;
    }

    public function exportExcel()
    {
        $filters = [
            'tahun_ajaran_id' => $this->filterTahunAjaranId,
            'semester_id' => $this->filterSemesterId,
            'kategori' => $this->filterKategori,
            'tingkat' => $this->filterTingkat,
        ];

        $service = app(PrestasiService::class);
        $spreadsheet = $service->exportSiswaExcel($filters);

        $filename = 'Data_Prestasi_Siswa_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function resetForm()
    {
        $this->prestasiId = null;
        $this->tipePeserta = 'individu';
        $this->namaTim = null;
        $this->selectedSiswaIds = [];
        $this->siswaSearchQuery = '';
        $this->kategori = 'non_akademik';
        $this->penyelenggaraLingkup = 'luar_sekolah';
        $this->penyelenggaraNama = '';
        $this->namaPrestasi = '';
        $this->bidang = 'Teknologi & Komputer';
        $this->tingkat = 'kabupaten_kota';
        $this->peringkat = 'Juara 1';
        $this->tanggalPerolehan = date('Y-m-d');
        $this->tanggalSelesai = null;
        $this->tempat = null;
        $this->pembimbingType = 'pegawai';
        $this->pembimbingPegawaiId = null;
        $this->pembimbingNamaManual = null;
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
        $query = Prestasi::with(['siswas.rombel', 'siswas.rayon', 'pembimbingPegawai', 'tahunAjaran', 'semester', 'periodeRapor'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama_prestasi', 'like', "%{$this->search}%")
                        ->orWhere('penyelenggara_nama', 'like', "%{$this->search}%")
                        ->orWhere('peringkat', 'like', "%{$this->search}%")
                        ->orWhere('nama_tim', 'like', "%{$this->search}%")
                        ->orWhereHas('siswas', function ($sq) {
                            $sq->where('nama', 'like', "%{$this->search}%")
                               ->orWhere('nisn', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->filterKategori, fn($q) => $q->where('kategori', $this->filterKategori))
            ->when($this->filterTingkat, fn($q) => $q->where('tingkat', $this->filterTingkat))
            ->when($this->filterTahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $this->filterTahunAjaranId))
            ->when($this->filterSemesterId, fn($q) => $q->where('semester_id', $this->filterSemesterId))
            ->when($this->filterPeriodeRaporId, fn($q) => $q->where('periode_rapor_id', $this->filterPeriodeRaporId))
            ->when($this->filterRombelId, function ($q) {
                $q->whereHas('siswas', fn($sq) => $sq->where('rombel_id', $this->filterRombelId));
            })
            ->when($this->filterRayonId, function ($q) {
                $q->whereHas('siswas', fn($sq) => $sq->where('rayon_id', $this->filterRayonId));
            })
            ->orderBy('tanggal_perolehan', 'desc');

        $prestasis = $query->paginate(12);

        // Pencarian siswa untuk modal
        $siswaSearchResults = [];
        if (strlen($this->siswaSearchQuery) >= 2) {
            $siswaSearchResults = Siswa::with('rombel')
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->where('nama', 'like', "%{$this->siswaSearchQuery}%")
                      ->orWhere('nisn', 'like', "%{$this->siswaSearchQuery}%");
                })
                ->limit(8)
                ->get();
        }

        $rombels = Rombel::orderBy('nama')->get();
        $rayons = Rayon::orderBy('nama')->get();
        $pegawais = Pegawai::where('is_active', true)->orderBy('nama_lengkap')->get();
        $tahunAjarans = TahunAjaran::orderBy('nama', 'desc')->get();
        $semesters = Semester::when($this->filterTahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $this->filterTahunAjaranId))->get();

        $formPeriodes = [];
        if ($this->tahunAjaranId) {
            $sem = Semester::find($this->semesterId);
            $semName = $sem ? (str_contains(strtolower($sem->semester), 'genap') ? 'genap' : 'ganjil') : 'ganjil';
            $formPeriodes = PeriodeRapor::where('tahun_ajaran_id', $this->tahunAjaranId)
                ->where('semester', $semName)
                ->orderBy('urutan')
                ->get();
        }

        return view('prestasi::livewire.prestasi-siswa-index', [
            'prestasis' => $prestasis,
            'rombels' => $rombels,
            'rayons' => $rayons,
            'pegawais' => $pegawais,
            'tahunAjarans' => $tahunAjarans,
            'semesters' => $semesters,
            'formPeriodes' => $formPeriodes,
            'siswaSearchResults' => $siswaSearchResults,
        ]);
    }
}
