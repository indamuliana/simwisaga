<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesSeeder extends Seeder
{
    /** 11 role aplikasi + 1 role teknis. */
    public const ROLES = [
        'Guru',
        'Operator',
        'Siswa',
        'Orang Tua',
        'Kepala Sekolah',
        'Bendahara',
        'Tata Usaha',
        'Instruktur Ekskul',
        'Kurikulum',
        'Kesiswaan',
        'Koordinator Alumni',
        'Koordinator Sarpras',
        User::SUPER_ADMIN_ROLE,
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::ROLES as $name) {
            Role::findOrCreate($name, 'web');
        }
    }
}
