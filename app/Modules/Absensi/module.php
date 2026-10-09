<?php

return [
    'name' => 'Absensi',
    'label' => 'Pengelolaan Absensi',
    'order' => 50,
    'menu' => [
        ['label' => 'Absensi Harian', 'route' => 'absensi.harian', 'permission' => 'absensi.harian.lihat'],
        ['label' => 'Absensi Mapel', 'route' => 'absensi.mapel', 'permission' => 'absensi.mapel.lihat'],
        ['label' => 'Rekap Absensi', 'route' => 'absensi.rekap', 'permission' => 'absensi.rekap.lihat'],
    ],
    'permissions' => [
        'absensi.harian' => [
            'label' => 'Absensi Harian',
            'actions' => ['lihat', 'input', 'hapus'],
        ],
        'absensi.mapel' => [
            'label' => 'Absensi Mata Pelajaran',
            'actions' => ['lihat', 'input'],
        ],
        'absensi.rekap' => [
            'label' => 'Rekapitulasi Absensi',
            'actions' => ['lihat', 'cetak'],
        ],
    ],
];
