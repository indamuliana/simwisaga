<?php

namespace App\Modules\Sistem\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Models\User;
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

    public string $search = '';
    public string $roleFilter = '';
    public bool $showModal = false;
    public ?int $userId = null;

    public string $name = '';
    public string $username = '';
    public ?string $email = null;
    public string $password = '';
    public bool $is_active = true;
    public bool $must_change_password = true;
    public array $selectedRoles = [];

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

    public function create(): void
    {
        $this->guard('sistem.pengguna.tambah');
        $this->reset(['userId', 'name', 'username', 'email', 'password', 'selectedRoles']);
        $this->is_active = true;
        $this->must_change_password = true;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->guard('sistem.pengguna.edit');
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->password = '';
        $this->is_active = $user->is_active;
        $this->must_change_password = $user->must_change_password;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
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

    public function render()
    {
        $query = User::with('roles')
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
            ->orderBy('id', 'desc');

        $users = $query->paginate(15);
        $roles = Role::orderBy('name')->get();

        return view('sistem::livewire.pengguna', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
}
