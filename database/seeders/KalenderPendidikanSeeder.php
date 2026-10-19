<?php

namespace Database\Seeders;

use App\Modules\KalenderPendidikan\Models\KalenderKategori;
use App\Modules\KalenderPendidikan\Services\KalenderService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class KalenderPendidikanSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 1. Kategori Bawaan
        $categories = [
            [
                'nama' => 'Kurikulum & KBM',
                'warna' => '#014694',
                'deskripsi' => 'Kegiatan akademik, awal semester, supervisi, dan KBM',
                'urutan' => 1,
            ],
            [
                'nama' => 'Asesmen & Ujian',
                'warna' => '#dc2626',
                'deskripsi' => 'Asesmen Harian, Tengah Semester, Akhir Semester, ANBK, dan Uji Kompetensi',
                'urutan' => 2,
            ],
            [
                'nama' => 'Kesiswaan & Ekskul',
                'warna' => '#059669',
                'deskripsi' => 'Kegiatan MPLS, LDKS, Peringatan Hari Besar, Classmeeting, dan Lomba',
                'urutan' => 3,
            ],
            [
                'nama' => 'Kunjungan Tamu & Industri',
                'warna' => '#7c3aed',
                'deskripsi' => 'Kunjungan industri, studi banding sekolah tamu, dan rekrutmen mitra DU/DI',
                'urutan' => 4,
            ],
            [
                'nama' => 'Agenda Kedinasan & Pemerintah',
                'warna' => '#d97706',
                'deskripsi' => 'Upacara kenegaraan, rapat dinas pendidikan, dan agenda pemda',
                'urutan' => 5,
            ],
            [
                'nama' => 'Rapat & Raker Guru',
                'warna' => '#0d9488',
                'deskripsi' => 'Rapat dewan guru, raker tahunan, dan workshop kurikulum',
                'urutan' => 6,
            ],
        ];

        foreach ($categories as $cat) {
            KalenderKategori::updateOrCreate(
                ['nama' => $cat['nama']],
                [
                    'warna' => $cat['warna'],
                    'deskripsi' => $cat['deskripsi'],
                    'urutan' => $cat['urutan'],
                    'is_active' => true,
                ]
            );
        }

        // 2. Permissions Spatie
        $permissions = [
            'kalender.lihat',
            'kalender.tambah',
            'kalender.edit',
            'kalender.hapus',
            'kalender.flag_final',
            'kalender.kategori.kelola',
            'kalender.libur.kelola',
        ];

        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        // Pimpinan & Verifikator: Akses Penuh
        $managementRoles = ['Super Admin', 'Kepala Sekolah', 'Kurikulum', 'Kesiswaan'];
        foreach ($managementRoles as $roleName) {
            $role = Role::findByName($roleName, 'web');
            if ($role) {
                $role->givePermissionTo($permissions);
            }
        }

        // Guru, Operator, Tata Usaha: Buat, edit, hapus agenda biasa
        $staffRoles = ['Guru', 'Operator', 'Tata Usaha'];
        foreach ($staffRoles as $roleName) {
            $role = Role::findByName($roleName, 'web');
            if ($role) {
                $role->givePermissionTo([
                    'kalender.lihat',
                    'kalender.tambah',
                    'kalender.edit',
                    'kalender.hapus',
                ]);
            }
        }

        // Siswa & Orang Tua: Hanya melihat agenda publik
        $viewerRoles = ['Siswa', 'Orang Tua'];
        foreach ($viewerRoles as $roleName) {
            $role = Role::findByName($roleName, 'web');
            if ($role) {
                $role->givePermissionTo(['kalender.lihat']);
            }
        }

        // 3. Sinkronisasi Hari Libur Nasional Awal
        try {
            app(KalenderService::class)->syncHariLiburNasional();
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
