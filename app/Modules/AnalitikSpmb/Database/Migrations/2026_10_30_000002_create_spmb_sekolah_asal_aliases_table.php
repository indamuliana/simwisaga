<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spmb_sekolah_asal_aliases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_asal_id')->constrained('spmb_sekolah_asals')->cascadeOnDelete();
            $table->string('alias_raw', 150)->unique();
            $table->timestamps();
        });

        // Otomatis sinkronkan permission dan beri akses ke role standar saat migrasi
        try {
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            $perms = [
                'analitik_spmb.asal_smp.lihat',
                'analitik_spmb.asal_smp.kelola',
                'analitik_spmb.perbaikan_smp.lihat',
                'analitik_spmb.perbaikan_smp.kelola',
            ];
            foreach ($perms as $p) {
                \Spatie\Permission\Models\Permission::findOrCreate($p, 'web');
            }
            $roles = \Spatie\Permission\Models\Role::whereIn('name', ['Super Admin', 'Admin', 'Kepala Sekolah', 'Kesiswaan', 'Operator'])->get();
            foreach ($roles as $r) {
                $r->givePermissionTo($perms);
            }
        } catch (\Throwable $e) {
            // Lanjutkan jika ada kendala
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('spmb_sekolah_asal_aliases');
    }
};
