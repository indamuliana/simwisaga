<?php

return [
    'name' => 'KalenderPendidikan',
    'label' => 'Kalender Pendidikan',
    'icon' => 'calendar',
    'order' => 16,
    'menu' => [
        [
            'label' => 'Kalender Kegiatan',
            'route' => 'kalender.index',
            'permission' => 'kalender.lihat',
        ],
        [
            'label' => 'Kategori Kegiatan',
            'route' => 'kalender.kategori',
            'permission' => 'kalender.kategori.kelola',
        ],
        [
            'label' => 'Pengaturan Hari Libur',
            'route' => 'kalender.libur',
            'permission' => 'kalender.libur.kelola',
        ],
    ],
    'permissions' => [
        'kalender' => [
            'label' => 'Kalender Pendidikan',
            'actions' => ['lihat', 'tambah', 'edit', 'hapus', 'flag_final'],
        ],
        'kalender.kategori' => [
            'label' => 'Kategori Kegiatan',
            'actions' => ['kelola'],
        ],
        'kalender.libur' => [
            'label' => 'Pengaturan Hari Libur',
            'actions' => ['kelola'],
        ],
    ],
];
