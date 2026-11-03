<?php

return [
    'name' => 'Kepesantrenan',
    'label' => 'Kepesantrenan',
    'icon' => 'book-open',
    'order' => 75,
    'menu' => [
        [
            'label' => 'Kamar & Asrama',
            'route' => 'pesantren.kamar',
            'permission' => 'pesantren.kamar.lihat',
        ],
        [
            'label' => 'Input Hafalan',
            'route' => 'pesantren.hafalan',
            'permission' => 'pesantren.hafalan.input',
        ],
        [
            'label' => 'Rekap & Leaderboard',
            'route' => 'pesantren.rekap',
            'permission' => 'pesantren.rekap.lihat',
        ],
        [
            'label' => 'Halaqoh & Presensi',
            'route' => 'pesantren.halaqoh',
            'permission' => 'pesantren.halaqoh.lihat',
        ],
    ],
    'permissions' => [
        'pesantren.kamar' => [
            'label' => 'Kamar & Asrama',
            'actions' => ['lihat', 'kelola'],
        ],
        'pesantren.hafalan' => [
            'label' => 'Hafalan Qur\'an',
            'actions' => ['lihat', 'input', 'hapus'],
        ],
        'pesantren.rekap' => [
            'label' => 'Rekap & Leaderboard',
            'actions' => ['lihat', 'export'],
        ],
        'pesantren.halaqoh' => [
            'label' => 'Halaqoh Santri',
            'actions' => ['lihat', 'kelola', 'absen'],
        ],
    ],
];
