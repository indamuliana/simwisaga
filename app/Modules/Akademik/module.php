<?php

return [
    'name' => 'Akademik',
    'label' => 'Master Akademik',
    'order' => 20,
    'topbar' => [
        \App\Modules\Akademik\Livewire\AcademicContextSwitcher::class,
    ],
    'menu' => [
        ['label' => 'Tahun Ajaran & Semester', 'route' => 'akademik.tahun-ajaran', 'permission' => 'akademik.tahun_ajaran.lihat'],
        ['label' => 'Jurusan / Keahlian', 'route' => 'akademik.jurusan', 'permission' => 'akademik.jurusan.lihat'],
        ['label' => 'Rombongan Belajar (Rombel)', 'route' => 'akademik.rombel', 'permission' => 'akademik.rombel.lihat'],
        ['label' => 'Rayon Wilayah', 'route' => 'akademik.rayon', 'permission' => 'akademik.rayon.lihat'],
        ['label' => 'Setting Program', 'route' => 'akademik.program', 'permission' => 'akademik.program.lihat'],
        ['label' => 'Mata Pelajaran', 'route' => 'akademik.mapel', 'permission' => 'akademik.mapel.lihat'],
        ['label' => 'Struktur Kurikulum', 'route' => 'akademik.kurikulum', 'permission' => 'akademik.kurikulum.lihat'],
    ],
    'permissions' => [
        'akademik.tahun_ajaran' => ['label' => 'Tahun Ajaran & Semester', 'actions' => ['lihat', 'tambah', 'edit', 'hapus']],
        'akademik.jurusan' => ['label' => 'Jurusan / Keahlian', 'actions' => ['lihat', 'tambah', 'edit', 'hapus', 'cetak']],
        'akademik.rombel' => ['label' => 'Rombongan Belajar', 'actions' => ['lihat', 'tambah', 'edit', 'hapus', 'cetak']],
        'akademik.rayon' => ['label' => 'Rayon Wilayah', 'actions' => ['lihat', 'tambah', 'edit', 'hapus', 'cetak']],
        'akademik.program' => ['label' => 'Setting Program', 'actions' => ['lihat', 'tambah', 'edit', 'hapus']],
        'akademik.mapel' => ['label' => 'Mata Pelajaran', 'actions' => ['lihat', 'tambah', 'edit', 'hapus', 'cetak']],
        'akademik.kurikulum' => ['label' => 'Struktur Kurikulum', 'actions' => ['lihat', 'tambah', 'edit', 'hapus', 'cetak']],
    ],
];
