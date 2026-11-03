<?php

namespace App\Modules\PortalSiswa\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\PortalSiswa\Models\PengajuanBeasiswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Beasiswa Siswa')]
class BeasiswaSiswa extends Component
{
    use GuardsPermission, WithFileUploads;

    public string $activeTab = 'saya'; // 'saya' atau 'pengajuan'

    // Form State Pengajuan Beasiswa
    public bool $showFormModal = false;
    public string $jenisBeasiswa = 'ota';
    public ?string $nominalPengajuan = null;
    public string $alasan = '';
    public ?string $penghasilanOrangTua = null;
    public ?int $jumlahTanggungan = null;
    public $lampiranDokumen = null;

    public function mount(): void
    {
        $this->guard('portal_siswa.beasiswa.lihat');
    }

    protected function getSiswa(): ?Siswa
    {
        $user = auth()->user();
        if (! $user) {
            return null;
        }

        if ($user->siswa) {
            return $user->siswa;
        }

        return Siswa::where('user_id', $user->id)
            ->orWhere('nisn', $user->username)
            ->orWhere('nipd', $user->username)
            ->first();
    }

    public function openFormModal(): void
    {
        $this->reset([
            'jenisBeasiswa',
            'nominalPengajuan',
            'alasan',
            'penghasilanOrangTua',
            'jumlahTanggungan',
            'lampiranDokumen',
        ]);
        $this->resetErrorBag();
        $this->showFormModal = true;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->reset([
            'jenisBeasiswa',
            'nominalPengajuan',
            'alasan',
            'penghasilanOrangTua',
            'jumlahTanggungan',
            'lampiranDokumen',
        ]);
        $this->resetErrorBag();
    }

    public function submitPengajuan(): void
    {
        $this->guard('portal_siswa.beasiswa.tambah');

        $siswa = $this->getSiswa();
        if (! $siswa) {
            session()->flash('error', 'Data siswa tidak ditemukan.');
            return;
        }

        $this->validate([
            'jenisBeasiswa' => ['required', 'in:ota,prestasi,afirmasi_ekonomi,lainnya'],
            'alasan' => ['required', 'string', 'max:1000'],
            'penghasilanOrangTua' => ['nullable', 'numeric', 'min:0'],
            'jumlahTanggungan' => ['nullable', 'integer', 'min:1'],
            'nominalPengajuan' => ['nullable', 'numeric', 'min:0'],
            'lampiranDokumen' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'],
        ], [
            'alasan.required' => 'Alasan permohonan beasiswa wajib diisi.',
            'penghasilanOrangTua.numeric' => 'Penghasilan orang tua harus berupa angka.',
            'lampiranDokumen.max' => 'Ukuran berkas maksimal 3MB.',
        ]);

        $dokumenPath = null;
        if ($this->lampiranDokumen) {
            $dokumenPath = $this->lampiranDokumen->store('beasiswa/pengajuan', 'public');
        }

        $activeTa = AcademicContext::currentTahunAjaran();
        $activeSem = AcademicContext::currentSemester();

        PengajuanBeasiswa::create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $activeTa?->id,
            'semester_id' => $activeSem?->id,
            'jenis_beasiswa' => $this->jenisBeasiswa,
            'nominal_pengajuan' => $this->nominalPengajuan ? (float) $this->nominalPengajuan : null,
            'alasan' => $this->alasan,
            'penghasilan_orang_tua' => $this->penghasilanOrangTua ? (float) $this->penghasilanOrangTua : null,
            'jumlah_tanggungan' => $this->jumlahTanggungan,
            'lampiran_dokumen' => $dokumenPath,
            'status' => PengajuanBeasiswa::STATUS_MENUNGGU,
        ]);

        $this->closeFormModal();
        $this->activeTab = 'pengajuan';
        session()->flash('success_beasiswa', 'Permohonan beasiswa berhasil diajukan dan akan diverifikasi oleh pengelola Beasiswa.');
    }

    public function render()
    {
        $siswa = $this->getSiswa();

        $riwayatPengajuan = collect();

        if ($siswa) {
            $siswa->loadMissing([
                'otaPenerimas.kelompok',
                'otaPenerimas.komponenBiayas',
                'otaPenerimas.alokasis',
            ]);

            $riwayatPengajuan = PengajuanBeasiswa::where('siswa_id', $siswa->id)
                ->with(['tahunAjaran', 'semester', 'diprosesOleh'])
                ->latest()
                ->get();
        }

        return view('portalsiswa::livewire.beasiswa-siswa', [
            'siswa' => $siswa,
            'riwayatPengajuan' => $riwayatPengajuan,
        ]);
    }
}
