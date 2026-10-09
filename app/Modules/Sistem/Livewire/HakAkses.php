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

    public function render()
    {
        $roles = Role::where('name', '!=', User::SUPER_ADMIN_ROLE)->orderBy('id')->get();
        $selectedRole = $this->selectedRoleId ? Role::find($this->selectedRoleId) : null;

        return view('sistem::livewire.hak-akses', [
            'roles' => $roles,
            'selectedRole' => $selectedRole,
        ]);
    }
}
