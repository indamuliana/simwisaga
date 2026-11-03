<?php

namespace App\Modules\PortalSiswa\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\PortalSiswa\Models\PengajuanPerbaikanDataSiswa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Profil Siswa')]
class ProfilSiswa extends Component
{
    use GuardsPermission, WithFileUploads;

    public string $activeSection = 'biodata'; // biodata, orang_tua, pengajuan, keamanan

    // State Pengajuan Perbaikan Data
    public bool $showPengajuanModal = false;
    public string $kategori = 'biodata';
    public string $namaField = 'nama';
    public string $nilaiBaru = '';
    public string $alasan = '';
    public $lampiranFile = null;

    // State Ganti Password
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount(): void
    {
        $this->guard('portal_siswa.profil.lihat');
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

    public function openPengajuanModal(): void
    {
        $this->reset(['kategori', 'namaField', 'nilaiBaru', 'alasan', 'lampiranFile']);
        $this->resetErrorBag();
        $this->showPengajuanModal = true;
    }

    public function closePengajuanModal(): void
    {
        $this->showPengajuanModal = false;
        $this->reset(['kategori', 'namaField', 'nilaiBaru', 'alasan', 'lampiranFile']);
        $this->resetErrorBag();
    }

    public function submitPengajuan(): void
    {
        $this->guard('portal_siswa.profil.edit');

        $siswa = $this->getSiswa();
        if (! $siswa) {
            session()->flash('error', 'Data siswa tidak ditemukan.');
            return;
        }

        $this->validate([
            'kategori' => ['required', 'string'],
            'namaField' => ['required', 'string'],
            'nilaiBaru' => ['required', 'string', 'max:500'],
            'alasan' => ['required', 'string', 'max:1000'],
            'lampiranFile' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $lampiranPath = null;
        if ($this->lampiranFile) {
            $lampiranPath = $this->lampiranFile->store('pengajuan-perbaikan', 'public');
        }

        $nilaiLama = $this->getOldValue($siswa, $this->namaField);

        PengajuanPerbaikanDataSiswa::create([
            'siswa_id' => $siswa->id,
            'kategori' => $this->kategori,
            'rincian_perubahan' => [
                $this->namaField => [
                    'field' => $this->namaField,
                    'lama' => $nilaiLama,
                    'baru' => $this->nilaiBaru,
                ],
            ],
            'alasan' => $this->alasan,
            'lampiran' => $lampiranPath,
            'status' => PengajuanPerbaikanDataSiswa::STATUS_MENUNGGU,
        ]);

        $this->closePengajuanModal();
        $this->activeSection = 'pengajuan';
        session()->flash('success_pengajuan', 'Pengajuan perbaikan data berhasil dikirim dan menunggu verifikasi staf Kesiswaan.');
    }

    protected function getOldValue(Siswa $siswa, string $field): string
    {
        if (in_array($field, ['nama_ayah', 'nama_ibu', 'pekerjaan_ayah', 'pekerjaan_ibu'])) {
            $ortu = $siswa->orangTuas->first();
            return match ($field) {
                'nama_ayah' => $ortu?->nama_ayah ?? '-',
                'nama_ibu' => $ortu?->nama_ibu ?? '-',
                'pekerjaan_ayah' => $ortu?->pekerjaan_ayah ?? '-',
                'pekerjaan_ibu' => $ortu?->pekerjaan_ibu ?? '-',
                default => '-',
            };
        }

        return (string) ($siswa->{$field} ?? '-');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = auth()->user();

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password saat ini salah.');
            return;
        }

        $user->forceFill([
            'password' => Hash::make($this->new_password),
            'password_changed_at' => now(),
        ])->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('success_password', 'Password akun Anda berhasil diperbarui!');
    }

    public function render()
    {
        $siswa = $this->getSiswa();

        $riwayatPengajuan = collect();
        if ($siswa) {
            $siswa->loadMissing([
                'rombel.waliKelas',
                'rayon.pembimbing',
                'program',
                'orangTuas',
            ]);

            $riwayatPengajuan = PengajuanPerbaikanDataSiswa::where('siswa_id', $siswa->id)
                ->with('diprosesOleh')
                ->latest()
                ->get();
        }

        return view('portalsiswa::livewire.profil-siswa', [
            'siswa' => $siswa,
            'riwayatPengajuan' => $riwayatPengajuan,
        ]);
    }
}
