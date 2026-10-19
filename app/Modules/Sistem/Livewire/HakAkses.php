<?php

namespace App\Modules\Sistem\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Core\Modules\ModuleRegistry;
use App\Core\Support\PermissionSync;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app')]
#[Title('Matriks Hak Akses')]
class HakAkses extends Component
{
    use GuardsPermission;

    public array $matrix = [];
    public ?string $selectedRoleId = null;
    public array $rolePermissions = [];
    public array $roleScopes = [];
    public string $search = '';

    // Master Role State
    public bool $showRoleModal = false;
    public string $roleModalMode = 'manage'; // 'manage', 'create', 'edit'
    public ?int $roleId = null;
    public string $roleName = '';

    public const STANDARD_ACTIONS = ['lihat', 'tambah', 'edit', 'hapus', 'cetak'];

    public const AVAILABLE_SCOPES = [
        'semua' => 'Semua Data',
        'rombel_diampu' => 'Rombel yang Diampu (Wali Kelas)',
        'mapel_diampu' => 'Mapel yang Diampu (Guru Mapel)',
        'rayon_dibimbing' => 'Rayon yang Dibimbing (Pembimbing Rayon)',
        'anak_sendiri' => 'Anak Sendiri (Orang Tua)',
        'diri_sendiri' => 'Data Sendiri (Siswa)',
    ];

    public function mount(ModuleRegistry $registry): void
    {
        PermissionSync::run();
        $this->matrix = $registry->permissionMatrix();

        $firstRole = Role::where('name', '!=', User::SUPER_ADMIN_ROLE)->first();
        if ($firstRole) {
            $this->selectRole((string) $firstRole->id);
        }
    }

    public function selectRole(string $roleId): void
    {
        $this->selectedRoleId = $roleId;
        $role = Role::findById((int) $roleId, 'web');
        $this->rolePermissions = $role->permissions->pluck('name')->toArray();

        $scopeRecord = \Illuminate\Support\Facades\DB::table('role_scopes')
            ->where('role_id', $role->id)
            ->first();
        $this->roleScopes[$role->id] = $scopeRecord->scope ?? 'semua';
    }

    public function togglePermission(string $permissionName): void
    {
        $this->guard('sistem.hakakses.edit');

        if (in_array($permissionName, $this->rolePermissions, true)) {
            $this->rolePermissions = array_values(array_diff($this->rolePermissions, [$permissionName]));
        } else {
            $this->rolePermissions[] = $permissionName;
        }
    }

    public function toggleAllForResource(string $resource, array $actions): void
    {
        $this->guard('sistem.hakakses.edit');

        $permissionNames = array_map(fn ($action) => "{$resource}.{$action}", $actions);
        $allSelected = count(array_intersect($permissionNames, $this->rolePermissions)) === count($permissionNames);

        if ($allSelected) {
            $this->rolePermissions = array_values(array_diff($this->rolePermissions, $permissionNames));
        } else {
            $this->rolePermissions = array_values(array_unique(array_merge($this->rolePermissions, $permissionNames)));
        }
    }

    public function toggleAllForModule(string $moduleName): void
    {
        $this->guard('sistem.hakakses.edit');

        $modulePermissions = [];
        foreach ($this->matrix as $resource => $def) {
            if ($def['module'] === $moduleName) {
                foreach ($def['actions'] as $act) {
                    $modulePermissions[] = "{$resource}.{$act}";
                }
            }
        }

        $allSelected = count(array_intersect($modulePermissions, $this->rolePermissions)) === count($modulePermissions);

        if ($allSelected) {
            $this->rolePermissions = array_values(array_diff($this->rolePermissions, $modulePermissions));
        } else {
            $this->rolePermissions = array_values(array_unique(array_merge($this->rolePermissions, $modulePermissions)));
        }
    }

    public function selectAllForRole(): void
    {
        $this->guard('sistem.hakakses.edit');

        $all = [];
        foreach ($this->matrix as $resource => $def) {
            foreach ($def['actions'] as $act) {
                $all[] = "{$resource}.{$act}";
            }
        }
        $this->rolePermissions = array_values(array_unique($all));
    }

    public function clearAllForRole(): void
    {
        $this->guard('sistem.hakakses.edit');
        $this->rolePermissions = [];
    }

    public function save(): void
    {
        $this->guard('sistem.hakakses.edit');

        if (! $this->selectedRoleId) {
            return;
        }

        $role = Role::findById((int) $this->selectedRoleId, 'web');
        $role->syncPermissions($this->rolePermissions);

        $scope = $this->roleScopes[$role->id] ?? 'semua';
        \Illuminate\Support\Facades\DB::table('role_scopes')->updateOrInsert(
            ['role_id' => $role->id],
            ['scope' => $scope, 'updated_at' => now()]
        );

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        session()->flash('status', "Hak akses untuk role '{$role->name}' berhasil diperbarui.");
    }

    public function openCreateRole(): void
    {
        $this->guard('sistem.hakakses.edit');
        $this->roleId = null;
        $this->roleName = '';
        $this->roleModalMode = 'create';
        $this->showRoleModal = true;
        $this->resetErrorBag();
    }

    public function openEditRole(int $id): void
    {
        $this->guard('sistem.hakakses.edit');
        $role = Role::findOrFail($id);
        if ($role->name === User::SUPER_ADMIN_ROLE) {
            session()->flash('error', 'Role Super Admin dilindungi sistem dan tidak dapat diubah namanya.');
            return;
        }
        $this->roleId = $role->id;
        $this->roleName = $role->name;
        $this->roleModalMode = 'edit';
        $this->showRoleModal = true;
        $this->resetErrorBag();
    }

    public function openManageRoles(): void
    {
        $this->guard('sistem.hakakses.edit');
        $this->roleModalMode = 'manage';
        $this->showRoleModal = true;
    }

    public function closeRoleModal(): void
    {
        $this->showRoleModal = false;
        $this->roleId = null;
        $this->roleName = '';
        $this->resetErrorBag();
    }

    public function saveRole(): void
    {
        $this->guard('sistem.hakakses.edit');

        $this->validate([
            'roleName' => [
                'required',
                'string',
                'max:50',
                \Illuminate\Validation\Rule::unique('roles', 'name')->ignore($this->roleId),
            ],
        ], [], ['roleName' => 'nama role']);

        $cleanName = trim($this->roleName);

        if ($this->roleId) {
            $role = Role::findOrFail($this->roleId);
            if ($role->name === User::SUPER_ADMIN_ROLE) {
                session()->flash('error', 'Role Super Admin tidak dapat diubah.');
                return;
            }
            $oldName = $role->name;
            $role->name = $cleanName;
            $role->save();
            $message = "Role '{$oldName}' berhasil diubah menjadi '{$cleanName}'.";
        } else {
            $role = Role::create(['name' => $cleanName, 'guard_name' => 'web']);
            \Illuminate\Support\Facades\DB::table('role_scopes')->insertOrIgnore([
                'role_id' => $role->id,
                'scope' => 'semua',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $message = "Role '{$cleanName}' berhasil dibuat. Silakan atur hak aksesnya.";
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $this->selectRole((string) $role->id);
        $this->closeRoleModal();
        session()->flash('status', $message);
    }

    public function deleteRole(int $id): void
    {
        $this->guard('sistem.hakakses.edit');
        $role = Role::findOrFail($id);

        if ($role->name === User::SUPER_ADMIN_ROLE) {
            session()->flash('error', 'Role Super Admin dilindungi sistem dan tidak dapat dihapus.');
            return;
        }

        $userCount = $role->users()->count();
        if ($userCount > 0) {
            session()->flash('error', "Role '{$role->name}' tidak dapat dihapus karena masih digunakan oleh {$userCount} pengguna.");
            return;
        }

        $roleName = $role->name;
        $role->delete();

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $firstRole = Role::where('name', '!=', User::SUPER_ADMIN_ROLE)->first();
        if ($firstRole) {
            $this->selectRole((string) $firstRole->id);
        } else {
            $this->selectedRoleId = null;
            $this->rolePermissions = [];
        }

        session()->flash('status', "Role '{$roleName}' berhasil dihapus.");
    }

    public function render()
    {
        $roles = Role::where('name', '!=', User::SUPER_ADMIN_ROLE)->withCount('users')->orderBy('id')->get();
        $selectedRole = $this->selectedRoleId ? Role::find($this->selectedRoleId) : null;

        $filteredMatrix = $this->matrix;
        if (!empty($this->search)) {
            $term = strtolower(trim($this->search));
            $filteredMatrix = array_filter($this->matrix, function ($def, $resource) use ($term) {
                return str_contains(strtolower($resource), $term)
                    || str_contains(strtolower($def['module'] ?? ''), $term)
                    || str_contains(strtolower($def['label'] ?? ''), $term);
            }, ARRAY_FILTER_USE_BOTH);
        }

        return view('sistem::livewire.hak-akses', [
            'roles' => $roles,
            'selectedRole' => $selectedRole,
            'filteredMatrix' => $filteredMatrix,
            'standardActions' => self::STANDARD_ACTIONS,
            'availableScopes' => self::AVAILABLE_SCOPES,
        ]);
    }
}
