<?php

namespace App\Modules\Prestasi\Livewire;

use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Prestasi\Models\Prestasi;
use App\Modules\Prestasi\Services\PrestasiService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class PrestasiMandiriSiswa extends Component
{
    use WithFileUploads;

    public ?Siswa $currentSiswa = null;

    // Form Pengajuan
    public bool $showSubmitModal = false;

    public string $tipePeserta = 'individu';
    public ?string $namaTim = null;
    public string $kategori = 'non_akademik';
    public string $penyelenggaraLingkup = 'luar_sekolah';
    public string $penyelenggaraNama = '';
    public string $namaPrestasi = '';
    public ?string $bidang = 'Teknologi & Komputer';
    public string $tingkat = 'kabupaten_kota';
    public string $peringkat = 'Juara 1';
    public string $tanggalPerolehan = '';
    public ?string $tempat = null;

    public string $pembimbingType = 'pegawai';
    public ?int $pembimbingPegawaiId = null;
    public ?string $pembimbingNamaManual = null;

    public $sertifikatFile = null;
    public $fotoFile = null;
    public ?string $deskripsi = null;

    public function mount()
    {
        $user = auth()->user();
        if ($user) {
            $this->currentSiswa = $user->siswa ?: Siswa::with(['rombel', 'rayon'])->first(); // Fallback jika admin menguji view siswa
        }
        $this->tanggalPerolehan = date('Y-m-d');
    }

    public function openSubmitModal()
    {
        $this->reset([
            'namaTim', 'penyelenggaraNama', 'namaPrestasi', 'tempat',
            'pembimbingPegawaiId', 'pembimbingNamaManual', 'sertifikatFile', 'fotoFile', 'deskripsi'
        ]);
        $this->tipePeserta = 'individu';
        $this->kategori = 'non_akademik';
        $this->penyelenggaraLingkup = 'luar_sekolah';
        $this->tingkat = 'kabupaten_kota';
        $this->peringkat = 'Juara 1';
        $this->tanggalPerolehan = date('Y-m-d');
        $this->pembimbingType = 'pegawai';
        $this->resetErrorBag();

        $this->showSubmitModal = true;
    }

    public function setPeringkatTemplate(string $text)
    {
        $this->peringkat = $text;
    }

    public function submitPengajuan()
    {
        if (!$this->currentSiswa) {
            session()->flash('error', 'Data profil siswa tidak ditemukan.');
            return;
        }

        $this->validate([
            'namaPrestasi' => 'required|string|max:255',
            'penyelenggaraNama' => 'required|string|max:255',
            'peringkat' => 'required|string|max:255',
            'tanggalPerolehan' => 'required|date',
            'sertifikatFile' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'fotoFile' => 'nullable|image|max:5120',
        ], [
            'sertifikatFile.required' => 'Wajib mengunggah scan sertifikat / piagam penghargaan sebagai bukti fisik.',
            'namaPrestasi.required' => 'Nama kejuaraan / sertifikasi wajib diisi.',
            'penyelenggaraNama.required' => 'Penyelenggara wajib diisi.',
        ]);

        $activeTa = AcademicContext::currentTahunAjaran();
        $activeSem = AcademicContext::currentSemester();

        $service = app(PrestasiService::class);
        $periodeId = null;
        if ($activeTa && $activeSem) {
            $periodeId = $service->resolvePeriodeRapor($this->tanggalPerolehan, $activeTa->id, $activeSem->id);
        }

        $sertifikatPath = $this->sertifikatFile->store('prestasi/sertifikat', 'public');
        $fotoPath = $this->fotoFile ? $this->fotoFile->store('prestasi/foto', 'public') : null;

        $pembimbingPegId = ($this->pembimbingType === 'pegawai') ? $this->pembimbingPegawaiId : null;
        $pembimbingManual = ($this->pembimbingType === 'manual') ? $this->pembimbingNamaManual : null;

        $prestasi = Prestasi::create([
            'tahun_ajaran_id' => $activeTa?->id ?? 1,
            'semester_id' => $activeSem?->id ?? 1,
            'periode_rapor_id' => $periodeId,
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
            'pembimbing_pegawai_id' => $pembimbingPegId,
            'pembimbing_nama_manual' => $pembimbingManual,
            'sertifikat_file_path' => $sertifikatPath,
            'foto_file_path' => $fotoPath,
            'deskripsi' => $this->deskripsi,
            'status_verifikasi' => 'pending', // Mandiri siswa berstatus pending verifikasi
            'created_by' => auth()->id(),
        ]);

        // Hubungkan siswa yang sedang login
        $prestasi->siswas()->attach($this->currentSiswa->id, [
            'peran' => $this->tipePeserta === 'individu' ? 'individu' : 'ketua',
        ]);

        $this->showSubmitModal = false;
        session()->flash('message', 'Pengajuan prestasi berhasil dikirim! Menunggu verifikasi tim Kesiswaan / Wali Kelas.');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $myPrestasis = collect();
        if ($this->currentSiswa) {
            $myPrestasis = $this->currentSiswa->prestasis()
                ->with(['pembimbingPegawai', 'periodeRapor'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $pegawais = Pegawai::where('is_active', true)->orderBy('nama_lengkap')->get();

        return view('prestasi::livewire.prestasi-mandiri-siswa', [
            'myPrestasis' => $myPrestasis,
            'pegawais' => $pegawais,
        ]);
    }
}
