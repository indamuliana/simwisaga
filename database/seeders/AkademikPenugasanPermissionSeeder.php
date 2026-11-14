<?php

namespace Database\Seeders;

use App\Core\Support\PermissionSync;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AkademikPenugasanPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Sinkronisasi permissions dari seluruh manifest modul
        PermissionSync::run();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'akademik.penugasan.lihat',
            'akademik.penugasan.tambah',
            'akademik.penugasan.edit',
            'akademik.penugasan.hapus',
            'akademik.penugasan.cetak',
        ];

        foreach ($permissions as $permName) {
            Permission::findOrCreate($permName, 'web');
        }

        // 2. Roles yang wajib memiliki akses ke Penugasan Mengajar
        $targetRoles = [
            'Kurikulum',
            'Kepala Sekolah',
            'Operator',
            User::SUPER_ADMIN_ROLE,
        ];

        foreach ($targetRoles as $roleName) {
            $role = Role::findByName($roleName, 'web');
            if ($role) {
                $role->givePermissionTo($permissions);
            }
        }

        // 3. Migrasikan peran lain yang sebelumnya memiliki kepegawaian.penugasan.*
        $rolesWithOldPerms = Role::whereHas('permissions', function ($q) {
            $q->where('name', 'like', 'kepegawaian.penugasan.%');
        })->get();

        foreach ($rolesWithOldPerms as $r) {
            $r->givePermissionTo($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
