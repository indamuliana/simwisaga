<?php

namespace App\Modules\Kepegawaian\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Core\Services\DocumentEngine;
use App\Core\Support\DataMasker;
use App\Models\User;
use App\Modules\Kepegawaian\Models\Pegawai;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app')]
#[Title('Data Guru & Karyawan (GTK)')]
class PegawaiIndex extends Component
{
    use GuardsPermission, WithPagination, \App\Core\Livewire\Traits\WithSorting;

    public function mount(): void
    {
        $this->sortField = 'nama_lengkap';
    }

    public bool $showModal = false;
    public ?int $pegawaiId = null;

    // Biodata Form
    public string $nama_lengkap = '';
    public ?string $gelar_depan = null;
    public ?string $gelar_belakang = null;
    public ?string $nip = null;
    public ?string $nuptk = null;
    public ?string $nik = null;
    public string $jenis_kelamin = 'L';
    public string $jenis_gtk = 'guru';
    public string $status_kepegawaian = 'gty';
    public ?string $tempat_lahir = null;
    public ?string $tanggal_lahir = null;
    public ?string $no_hp = null;
    public ?string $email = null;
    public ?string $alamat = null;
    public bool $is_active = true;

    // Akun Login Pengguna
    public bool $buat_akun = false;
    public string $username = '';
    public string $password = '';
    public string $role_name = 'Guru';

    // Filter & Search
    public string $search = '';
    public string $filterGtk = '';
    public string $filterStatus = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterGtk(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        $userId = $this->pegawaiId ? Pegawai::find($this->pegawaiId)?->user_id : null;

        $rules = [
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'gelar_depan' => ['nullable', 'string', 'max:30'],
            'gelar_belakang' => ['nullable', 'string', 'max:30'],
            'nip' => ['nullable', 'string', 'max:30', Rule::unique('pegawais', 'nip')->ignore($this->pegawaiId)],
            'nuptk' => ['nullable', 'string', 'max:30', Rule::unique('pegawais', 'nuptk')->ignore($this->pegawaiId)],
            'nik' => ['nullable', 'string', 'max:20', Rule::unique('pegawais', 'nik')->ignore($this->pegawaiId)],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'jenis_gtk' => ['required', 'in:guru,tendik'],
            'status_kepegawaian' => ['required', 'in:pns,pppk,gty,gtt,honor'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'alamat' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];

        if ($this->buat_akun) {
            $rules['username'] = ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($userId)];
            $rules['role_name'] = ['required', 'exists:roles,name'];
            if (!$userId) {
                $rules['password'] = ['required', 'string', 'min:6'];
            } else {
                $rules['password'] = ['nullable', 'string', 'min:6'];
            }
        }

        return $rules;
    }

    public function create(): void
    {
        $this->guard('kepegawaian.pegawai.tambah');
        $this->reset([
            'pegawaiId', 'nama_lengkap', 'gelar_depan', 'gelar_belakang',
            'nip', 'nuptk', 'nik', 'tempat_lahir', 'tanggal_lahir',
            'no_hp', 'email', 'alamat', 'username', 'password'
        ]);
        $this->jenis_kelamin = 'L';
        $this->jenis_gtk = 'guru';
        $this->status_kepegawaian = 'gty';
        $this->is_active = true;
        $this->buat_akun = true;
        $this->role_name = 'Guru';
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->guard('kepegawaian.pegawai.edit');
        $p = Pegawai::with('user.roles')->findOrFail($id);
        $this->pegawaiId = $p->id;
        $this->nama_lengkap = $p->nama_lengkap;
        $this->gelar_depan = $p->gelar_depan;
        $this->gelar_belakang = $p->gelar_belakang;
        $this->nip = $p->nip;
        $this->nuptk = $p->nuptk;
        $this->nik = $p->nik;
        $this->jenis_kelamin = $p->jenis_kelamin;
        $this->jenis_gtk = $p->jenis_gtk;
        $this->status_kepegawaian = $p->status_kepegawaian;
        $this->tempat_lahir = $p->tempat_lahir;
        $this->tanggal_lahir = $p->tanggal_lahir?->format('Y-m-d');
        $this->no_hp = $p->no_hp;
        $this->email = $p->email;
        $this->alamat = $p->alamat;
        $this->is_active = $p->is_active;

        if ($p->user) {
            $this->buat_akun = true;
            $this->username = $p->user->username;
            $this->password = '';
            $this->role_name = $p->user->getRoleNames()->first() ?? ($p->jenis_gtk === 'guru' ? 'Guru' : 'Tata Usaha');
        } else {
            $this->buat_akun = false;
            $this->username = '';
            $this->password = '';
            $this->role_name = $p->jenis_gtk === 'guru' ? 'Guru' : 'Tata Usaha';
        }

        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->pegawaiId) {
            $this->guard('kepegawaian.pegawai.edit');
        } else {
            $this->guard('kepegawaian.pegawai.tambah');
        }

        $this->validate();

        $userId = null;
        if ($this->pegawaiId) {
            $pegawai = Pegawai::findOrFail($this->pegawaiId);
            $userId = $pegawai->user_id;
        }

        // Pengelolaan Akun User
        if ($this->buat_akun) {
            if ($userId) {
                $user = User::findOrFail($userId);
                $userData = [
                    'name' => $this->nama_lengkap,
                    'username' => $this->username,
                    'email' => $this->email ?: "{$this->username}@wikrama.sch.id",
                ];
                if (!empty($this->password)) {
                    $userData['password'] = Hash::make($this->password);
                    $userData['must_change_password'] = false;
                }
                $user->update($userData);
                $user->syncRoles([$this->role_name]);
            } else {
                $user = User::create([
                    'name' => $this->nama_lengkap,
                    'username' => $this->username,
                    'email' => $this->email ?: "{$this->username}@wikrama.sch.id",
                    'password' => Hash::make($this->password),
                    'is_active' => true,
                    'must_change_password' => false,
                ]);
                $user->assignRole($this->role_name);
                $userId = $user->id;
            }
        }

        $pegawaiData = [
            'user_id' => $userId,
            'nama_lengkap' => $this->nama_lengkap,
            'gelar_depan' => $this->gelar_depan ?: null,
            'gelar_belakang' => $this->gelar_belakang ?: null,
            'nip' => $this->nip ?: null,
            'nuptk' => $this->nuptk ?: null,
            'nik' => $this->nik ?: null,
            'jenis_kelamin' => $this->jenis_kelamin,
            'jenis_gtk' => $this->jenis_gtk,
            'status_kepegawaian' => $this->status_kepegawaian,
            'tempat_lahir' => $this->tempat_lahir ?: null,
            'tanggal_lahir' => $this->tanggal_lahir ?: null,
            'no_hp' => $this->no_hp ?: null,
            'email' => $this->email ?: null,
            'alamat' => $this->alamat ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->pegawaiId) {
            $pegawai->update($pegawaiData);
        } else {
            Pegawai::create($pegawaiData);
        }

        $this->showModal = false;
        session()->flash('status', "Data pegawai {$this->nama_lengkap} berhasil disimpan.");
    }

    public function delete(int $id): void
    {
        $this->guard('kepegawaian.pegawai.hapus');
        $p = Pegawai::findOrFail($id);
        $nama = $p->nama_lengkap;
        $user = $p->user;
        $p->delete();
        if ($user) {
            $user->delete();
        }
        session()->flash('status', "Pegawai {$nama} berhasil dihapus.");
    }

    public function cetakDaftar()
    {
        $this->guard('kepegawaian.pegawai.cetak');

        $pegawais = Pegawai::orderBy('jenis_gtk')->orderBy('nama_lengkap')->get();

        $rows = '';
        foreach ($pegawais as $idx => $p) {
            $no = $idx + 1;
            $nama = $p->nama_gelar;
            $nip = $p->nip ?? '-';
            $nuptk = $p->nuptk ?? '-';
            $nik = DataMasker::maskNik($p->nik);
            $jenis = strtoupper($p->jenis_gtk);
            $status = strtoupper($p->status_kepegawaian);
            $jk = $p->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';

            $rows .= "
                <tr>
                    <td style='text-align: center;'>{$no}</td>
                    <td><b>{$nama}</b><br><small style='color: #64748b;'>NIK: {$nik}</small></td>
                    <td>NIP: {$nip}<br>NUPTK: {$nuptk}</td>
                    <td>{$jk}</td>
                    <td>{$jenis} ({$status})</td>
                    <td>{$p->no_hp}</td>
                </tr>
            ";
        }

        $html = "
            <table class='content-table'>
                <thead>
                    <tr>
                        <th style='width: 30px; text-align: center;'>No</th>
                        <th>Nama Lengkap & NIK</th>
                        <th>NIP / NUPTK</th>
                        <th>JK</th>
                        <th>Jenis & Status</th>
                        <th>Kontak</th>
                    </tr>
                </thead>
                <tbody>
                    {$rows}
                </tbody>
            </table>
        ";

        $pdf = DocumentEngine::createPdf(
            title: 'DAFTAR GURU DAN TENAGA KEPENDIDIKAN (GTK)',
            content: $html,
            options: [
                'subtitle' => 'SMK Wikrama 1 Garut — Tahun Ajaran ' . now()->year . '/' . (now()->year + 1),
                'template_kode' => 'daftar_siswa',
            ]
        );

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'Daftar_GTK_Wikrama.pdf'
        );
    }

    public function render()
    {
        $query = Pegawai::with('user.roles');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_lengkap', 'like', "%{$this->search}%")
                    ->orWhere('nip', 'like', "%{$this->search}%")
                    ->orWhere('nuptk', 'like', "%{$this->search}%")
                    ->orWhere('nik', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterGtk) {
            $query->where('jenis_gtk', $this->filterGtk);
        }

        if ($this->filterStatus) {
            $query->where('status_kepegawaian', $this->filterStatus);
        }

        $pegawais = $query->orderBy($this->sortField, $this->sortDirection)->paginate(15);
        $roles = Role::whereNotIn('name', ['Super Admin', 'Siswa', 'Orang Tua'])->get();

        return view('kepegawaian::livewire.pegawai-index', [
            'pegawais' => $pegawais,
            'roles' => $roles,
        ]);
    }
}
