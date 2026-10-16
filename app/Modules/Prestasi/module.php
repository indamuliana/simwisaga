<?php

return [
    'name' => 'Prestasi',
    'label' => 'Manajemen Prestasi',
    'icon' => 'trophy',
    'order' => 65,
    'menu' => [
        [
            'label' => 'Dashboard Prestasi',
            'route' => 'prestasi.index',
            'permission' => 'prestasi.lihat',
        ],
        [
            'label' => 'Prestasi Siswa',
            'route' => 'prestasi.siswa',
            'permission' => 'prestasi.lihat',
        ],
        [
            'label' => 'Prestasi Guru & Tendik',
            'route' => 'prestasi.guru',
            'permission' => 'prestasi.guru.lihat',
        ],
        [
            'label' => 'Prestasi Sekolah',
            'route' => 'prestasi.sekolah',
            'permission' => 'prestasi.sekolah.lihat',
        ],
        [
            'label' => 'Verifikasi Pengajuan',
            'route' => 'prestasi.verifikasi',
            'permission' => 'prestasi.verifikasi',
        ],
        [
            'label' => 'Prestasi Saya (Siswa)',
            'route' => 'prestasi.mandiri',
            'permission' => null, // Bebas diakses siswa
        ],
    ],
    'permissions' => [
        'prestasi' => [
            'label' => 'Prestasi Siswa',
            'actions' => ['lihat', 'tambah', 'edit', 'hapus', 'verifikasi', 'export'],
        ],
        'prestasi.guru' => [
            'label' => 'Prestasi Guru & Tendik',
            'actions' => ['lihat', 'tambah', 'edit', 'hapus', 'export'],
        ],
        'prestasi.sekolah' => [
            'label' => 'Prestasi Sekolah',
            'actions' => ['lihat', 'tambah', 'edit', 'hapus', 'export'],
        ],
    ],
];
