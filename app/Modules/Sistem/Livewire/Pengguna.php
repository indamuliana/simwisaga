<?php

namespace App\Modules\Sistem\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Models\User;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app')]
#[Title('Pengelolaan Pengguna')]
class Pengguna extends Component
{
    use GuardsPermission, WithPagination;

    public string $activeTab = 'staff'; // 'staff', 'siswa', 'ortu', 'semua'
    public string $search = '';
    public string $roleFilter = '';
    public string $statusFilter = '';
    public string $linkedFilter = '';
    public bool $showModal = false;
    public ?int $userId = null;

    public function setTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['staff', 'siswa', 'ortu', 'semua'], true) ? $tab : 'staff';
        $this->roleFilter = '';
        $this->resetPage();
    }

    // Field Pengguna
    public string $name = '';
    public string $username = '';
    public ?string $email = null;
    public string $password = '';
    public bool $is_active = true;
    public bool $must_change_password = true;
    public array $selectedRoles = [];

    // Penautan Profil (Pegawai / Siswa)
    public string $profileType = ''; // '', 'pegawai', 'siswa'
    public ?int $selectedPegawaiId = null;
    public ?int $selectedSiswaId = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'username' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($this->userId),
            ],
            'email' => [
                'nullable',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],
            'password' => [$this->userId ? 'nullable' : 'required', 'string', 'min:6'],
            'is_active' => ['boolean'],
            'must_change_password' => ['boolean'],
            'selectedRoles' => ['array'],
            'profileType' => ['nullable', 'string', 'in:pegawai,siswa'],
            'selectedPegawaiId' => ['nullable', 'integer'],
            'selectedSiswaId' => ['nullable', 'integer'],
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedLinkedFilter(): void
    {
        $this->resetPage();
    }

    public function updatedProfileType(string $val): void
    {
        if ($val === 'pegawai') {
            $this->selectedSiswaId = null;
        } elseif ($val === 'siswa') {
            $this->selectedPegawaiId = null;
        } else {
            $this->selectedPegawaiId = null;
            $this->selectedSiswaId = null;
        }
    }

    public function updatedSelectedPegawaiId(?int $pegawaiId): void
    {
        if ($pegawaiId) {
            $pegawai = Pegawai::find($pegawaiId);
            if ($pegawai) {
                if (empty($this->name) || $this->userId === null) {
                    $this->name = $pegawai->nama_lengkap;
                }
                if (empty($this->username) || $this->userId === null) {
                    $nip = trim($pegawai->nip ?: ($pegawai->nuptk ?: ''));
                    if (!empty($nip)) {
                        $this->username = preg_replace('/[^A-Za-z0-9_-]/', '', $nip);
                    }
                }
                if (empty($this->email) && !empty($pegawai->email)) {
                    $this->email = $pegawai->email;
                }
            }
        }
    }

    public function updatedSelectedSiswaId(?int $siswaId): void
    {
        if ($siswaId) {
            $siswa = Siswa::find($siswaId);
            if ($siswa) {
                if (empty($this->name) || $this->userId === null) {
                    $this->name = $siswa->nama;
                }
                if (empty($this->username) || $this->userId === null) {
                    $nis = trim($siswa->nisn ?: ($siswa->nipd ?: ''));
                    if (!empty($nis)) {
                        $this->username = preg_replace('/[^A-Za-z0-9_-]/', '', $nis);
                    }
                }
            }
        }
    }

    public function create(): void
    {
        $this->guard('sistem.pengguna.tambah');
        $this->reset([
            'userId', 'name', 'username', 'email', 'password', 'selectedRoles',
            'profileType', 'selectedPegawaiId', 'selectedSiswaId'
        ]);
        $this->is_active = true;
        $this->must_change_password = true;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->guard('sistem.pengguna.edit');
        $user = User::with(['roles', 'pegawai', 'siswa'])->findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->password = '';
        $this->is_active = (bool) $user->is_active;
        $this->must_change_password = (bool) $user->must_change_password;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();

        // Tautan profil
        if ($user->pegawai) {
            $this->profileType = 'pegawai';
            $this->selectedPegawaiId = $user->pegawai->id;
            $this->selectedSiswaId = null;
        } elseif ($user->siswa) {
            $this->profileType = 'siswa';
            $this->selectedSiswaId = $user->siswa->id;
            $this->selectedPegawaiId = null;
        } else {
            $this->profileType = '';
            $this->selectedPegawaiId = null;
            $this->selectedSiswaId = null;
        }

        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->userId) {
            $this->guard('sistem.pengguna.edit');
        } else {
            $this->guard('sistem.pengguna.tambah');
        }

        $this->validate();

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email ?: null,
            'is_active' => $this->is_active,
            'must_change_password' => $this->must_change_password,
        ];

        if (! empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $user->update($data);
        } else {
            $user = User::create($data);
        }

        $user->syncRoles($this->selectedRoles);

        // Sinkronisasi tautan profil (Pegawai / Siswa)
        if ($this->profileType === 'pegawai' && $this->selectedPegawaiId) {
            Pegawai::where('user_id', $user->id)->where('id', '!=', $this->selectedPegawaiId)->update(['user_id' => null]);
            Siswa::where('user_id', $user->id)->update(['user_id' => null]);
            Pegawai::where('id', $this->selectedPegawaiId)->update(['user_id' => $user->id]);
        } elseif ($this->profileType === 'siswa' && $this->selectedSiswaId) {
            Siswa::where('user_id', $user->id)->where('id', '!=', $this->selectedSiswaId)->update(['user_id' => null]);
            Pegawai::where('user_id', $user->id)->update(['user_id' => null]);
            Siswa::where('id', $this->selectedSiswaId)->update(['user_id' => $user->id]);
        } else {
            Pegawai::where('user_id', $user->id)->update(['user_id' => null]);
            Siswa::where('user_id', $user->id)->update(['user_id' => null]);
        }

        $this->showModal = false;
        session()->flash('status', 'Data pengguna berhasil disimpan.');
    }

    public function toggleActive(int $id): void
    {
        $this->guard('sistem.pengguna.edit');
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
            return;
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        session()->flash('status', "Status pengguna '{$user->username}' diperbarui.");
    }

    public function resetPassword(int $id): void
    {
        $this->guard('sistem.pengguna.edit');
        $user = User::findOrFail($id);

        $defaultPassword = 'wikrama123';
        $user->password = Hash::make($defaultPassword);
        $user->must_change_password = true;
        $user->save();

        session()->flash('status', "Password pengguna '{$user->username}' berhasil direset menjadi '{$defaultPassword}' (Wajib Ganti Password aktif).");
    }

    public function deleteUser(int $id): void
    {
        $this->guard('sistem.pengguna.hapus');
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }

        if ($user->hasRole(User::SUPER_ADMIN_ROLE)) {
            $superAdminCount = User::role(User::SUPER_ADMIN_ROLE)->count();
            if ($superAdminCount <= 1) {
                session()->flash('error', 'Tidak dapat menghapus satu-satunya akun Super Admin pada sistem.');
                return;
            }
        }

        // Lepas tautan profil sebelum dihapus
        Pegawai::where('user_id', $user->id)->update(['user_id' => null]);
        Siswa::where('user_id', $user->id)->update(['user_id' => null]);

        $username = $user->username;
        $user->delete();

        session()->flash('status', "Akun pengguna '{$username}' berhasil dihapus.");
    }

    public function render()
    {
        $countStaff = User::where(function ($q) {
            $q->whereHas('pegawai')
                ->orWhere(function ($sub) {
                    $sub->whereDoesntHave('roles', fn ($r) => $r->whereIn('name', ['Siswa', 'Orang Tua']))
                        ->where('username', 'not like', 'ortu_%');
                });
        })->count();

        $countSiswa = User::where(function ($q) {
            $q->whereHas('siswa')->orWhereHas('roles', fn ($r) => $r->where('name', 'Siswa'));
        })->count();

        $countOrtu = User::where(function ($q) {
            $q->whereHas('roles', fn ($r) => $r->where('name', 'Orang Tua'))->orWhere('username', 'like', 'ortu_%');
        })->count();

        $countSemua = User::count();

        $query = User::with(['roles', 'pegawai', 'siswa.rombel'])
            ->when($this->activeTab === 'staff', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereHas('pegawai')
                        ->orWhere(function ($inner) {
                            $inner->whereDoesntHave('roles', fn ($r) => $r->whereIn('name', ['Siswa', 'Orang Tua']))
                                ->where('username', 'not like', 'ortu_%');
                        });
                });
            })
            ->when($this->activeTab === 'siswa', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereHas('siswa')->orWhereHas('roles', fn ($r) => $r->where('name', 'Siswa'));
                });
            })
            ->when($this->activeTab === 'ortu', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereHas('roles', fn ($r) => $r->where('name', 'Orang Tua'))
                        ->orWhere('username', 'like', 'ortu_%');
                });
            })
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('username', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->roleFilter, function ($q) {
                $q->whereHas('roles', fn ($r) => $r->where('name', $this->roleFilter));
            })
            ->when($this->statusFilter !== '', function ($q) {
                $q->where('is_active', $this->statusFilter === 'aktif');
            })
            ->when($this->linkedFilter, function ($q) {
                if ($this->linkedFilter === 'pegawai') {
                    $q->whereHas('pegawai');
                } elseif ($this->linkedFilter === 'siswa') {
                    $q->whereHas('siswa');
                } elseif ($this->linkedFilter === 'umum') {
                    $q->whereDoesntHave('pegawai')->whereDoesntHave('siswa');
                }
            })
            ->orderBy('id', 'desc');

        $users = $query->paginate(15);

        // Filter roles untuk dropdown filter sesuai tab yang aktif
        $rolesQuery = Role::orderBy('name');
        if ($this->activeTab === 'staff') {
            $rolesQuery->whereNotIn('name', ['Siswa', 'Orang Tua']);
        } elseif ($this->activeTab === 'siswa') {
            $rolesQuery->where('name', 'Siswa');
        } elseif ($this->activeTab === 'ortu') {
            $rolesQuery->where('name', 'Orang Tua');
        }
        $filterRoles = $rolesQuery->get();

        // Semua roles untuk modal penugasan role
        $allRoles = Role::orderBy('name')->get();

        // Daftar Pegawai dan Siswa untuk dropdown penautan
        $availablePegawais = Pegawai::where(function ($q) {
            $q->whereNull('user_id');
            if ($this->userId) {
                $q->orWhere('user_id', $this->userId);
            }
        })
        ->orderBy('nama_lengkap')
        ->get(['id', 'nama_lengkap', 'nip', 'jenis_gtk']);

        $availableSiswas = Siswa::with('rombel')
            ->where(function ($q) {
                $q->whereNull('user_id');
                if ($this->userId) {
                    $q->orWhere('user_id', $this->userId);
                }
            })
            ->orderBy('nama')
            ->limit(100)
            ->get(['id', 'nama', 'nisn', 'nipd', 'rombel_id']);

        return view('sistem::livewire.pengguna', [
            'users' => $users,
            'roles' => $allRoles,
            'filterRoles' => $filterRoles,
            'availablePegawais' => $availablePegawais,
            'availableSiswas' => $availableSiswas,
            'countStaff' => $countStaff,
            'countSiswa' => $countSiswa,
            'countOrtu' => $countOrtu,
            'countSemua' => $countSemua,
        ]);
    }
}
