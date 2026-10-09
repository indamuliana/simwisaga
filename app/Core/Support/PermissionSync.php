<?php

namespace App\Core\Support;

use App\Core\Modules\ModuleRegistry;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/** Menyamakan tabel `permissions` dengan permission yang dideklarasikan manifest modul. */
class PermissionSync
{
    public static function run(): int
    {
        $created = 0;

        foreach (app(ModuleRegistry::class)->allPermissionNames() as $name) {
            $permission = Permission::findOrCreate($name, 'web');
            $created += $permission->wasRecentlyCreated ? 1 : 0;
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $created;
    }
}
