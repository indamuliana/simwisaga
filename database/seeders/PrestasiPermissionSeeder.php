<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PrestasiPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // Prestasi Siswa
            'prestasi.lihat',
            'prestasi.tambah',
            'prestasi.edit',
            'prestasi.hapus',
            'prestasi.verifikasi',
            'prestasi.export',

            // Prestasi Guru
            'prestasi.guru.lihat',
            'prestasi.guru.tambah',
            'prestasi.guru.edit',
            'prestasi.guru.hapus',
            'prestasi.guru.export',

            // Prestasi Sekolah
            'prestasi.sekolah.lihat',
            'prestasi.sekolah.tambah',
            'prestasi.sekolah.edit',
            'prestasi.sekolah.hapus',
            'prestasi.sekolah.export',
        ];

        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        // Grant to Super Admin
        $superAdmin = Role::where('name', User::SUPER_ADMIN_ROLE)->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        // Grant to Admin & Kesiswaan
        $adminRoles = Role::whereIn('name', ['Admin', 'Kesiswaan', 'Operator'])->get();
        foreach ($adminRoles as $role) {
            $role->givePermissionTo($permissions);
        }

        // Grant to Guru
        $guruRole = Role::where('name', 'Guru')->first();
        if ($guruRole) {
            $guruRole->givePermissionTo([
                'prestasi.lihat',
                'prestasi.tambah',
                'prestasi.edit',
                'prestasi.verifikasi',
                'prestasi.export',
                'prestasi.guru.lihat',
                'prestasi.guru.tambah',
                'prestasi.guru.edit',
                'prestasi.sekolah.lihat',
            ]);
        }

        // Grant to Kepala Sekolah
        $kepsekRole = Role::where('name', 'Kepala Sekolah')->first();
        if ($kepsekRole) {
            $kepsekRole->givePermissionTo([
                'prestasi.lihat',
                'prestasi.export',
                'prestasi.guru.lihat',
                'prestasi.guru.export',
                'prestasi.sekolah.lihat',
                'prestasi.sekolah.export',
            ]);
        }
    }
}
