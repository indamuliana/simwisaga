<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\AnalitikSpmb\Services\SpmbSekolahAsalSyncService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AnalitikSpmbPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'analitik_spmb.asal_smp.lihat',
            'analitik_spmb.asal_smp.kelola',
            'analitik_spmb.perbaikan_smp.lihat',
            'analitik_spmb.perbaikan_smp.kelola',
            'analitik_spmb.master_smp.lihat',
            'analitik_spmb.master_smp.kelola',
            'analitik_spmb.master_smp.import',
        ];

        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        // Berikan akses ke Super Admin
        $superAdmin = Role::where('name', User::SUPER_ADMIN_ROLE)->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        // Berikan akses ke Role: Admin, Kepala Sekolah, Kesiswaan, Operator
        $roles = Role::whereIn('name', ['Admin', 'Kepala Sekolah', 'Kesiswaan', 'Operator'])->get();
        foreach ($roles as $role) {
            $role->givePermissionTo($permissions);
        }

        // Lakukan sinkronisasi data awal jika tabel migrasi sudah siap
        try {
            if (Schema::hasTable('spmb_sekolah_asals')) {
                app(SpmbSekolahAsalSyncService::class)->syncFromSiswaData();
            }
        } catch (\Throwable $e) {
            // Lanjutkan jika ada kendala koneksi tabel
        }
    }
}
