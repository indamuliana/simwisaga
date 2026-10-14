<?php

namespace Database\Seeders;

use App\Core\Support\PermissionSync;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SeniBudayaPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Sync permissions from all module manifests (including SeniBudaya)
        PermissionSync::run();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 2. Roles eligible for kelola (mengisi nilai, presensi, keanggotaan)
        $kelolaRoles = [
            'Guru',
            'Instruktur Ekskul',
            'Kurikulum',
            'Operator',
            'Kepala Sekolah',
            'Kesiswaan',
            'Super Admin',
        ];

        // 3. Roles eligible for setting (pengaturan cabang & TP)
        $settingRoles = [
            'Guru',
            'Kurikulum',
            'Operator',
            'Super Admin',
        ];

        $kelolaPermission = Permission::findByName('seni_budaya.kelola', 'web');
        $settingPermission = Permission::findByName('seni_budaya.setting', 'web');

        foreach ($kelolaRoles as $roleName) {
            $role = Role::findByName($roleName, 'web');
            if ($role && !$role->hasPermissionTo($kelolaPermission)) {
                $role->givePermissionTo($kelolaPermission);
            }
        }

        foreach ($settingRoles as $roleName) {
            $role = Role::findByName($roleName, 'web');
            if ($role && !$role->hasPermissionTo($settingPermission)) {
                $role->givePermissionTo($settingPermission);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
