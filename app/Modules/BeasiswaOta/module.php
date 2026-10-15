<?php

return [
    'name' => 'BeasiswaOta',
    'label' => 'Beasiswa OTA',
    'icon' => 'heart',
    'order' => 85,
    'menu' => [
        [
            'label' => 'Dashboard & Kas',
            'route' => 'beasiswa-ota.index',
            'permission' => 'beasiswa_ota.lihat',
        ],
        [
            'label' => 'Donatur & Kelompok',
            'route' => 'beasiswa-ota.donatur-kelompok',
            'permission' => 'beasiswa_ota.kelola',
        ],
        [
            'label' => 'Penerima & Komponen Biaya',
            'route' => 'beasiswa-ota.penerima',
            'permission' => 'beasiswa_ota.kelola',
        ],
        [
            'label' => 'Donasi Masuk',
            'route' => 'beasiswa-ota.donasi-masuk',
            'permission' => 'beasiswa_ota.kelola',
        ],
        [
            'label' => 'Penyaluran Beasiswa',
            'route' => 'beasiswa-ota.penyaluran',
            'permission' => 'beasiswa_ota.kelola',
        ],
        [
            'label' => 'Laporan & Rekap',
            'route' => 'beasiswa-ota.laporan',
            'permission' => 'beasiswa_ota.lihat',
        ],
        [
            'label' => 'Pengaturan & Template',
            'route' => 'beasiswa-ota.setting',
            'permission' => 'beasiswa_ota.setting',
        ],
    ],
    'permissions' => [
        'beasiswa_ota' => [
            'label' => 'Beasiswa Orang Tua Asuh (OTA)',
            'actions' => ['lihat', 'kelola', 'setting', 'cetak'],
        ],
    ],
];
