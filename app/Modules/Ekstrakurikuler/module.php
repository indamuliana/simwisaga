<?php

return [
    'name' => 'Ekstrakurikuler',
    'label' => 'UPD (Ekstrakurikuler)',
    'icon' => 'sparkles',
    'order' => 70,
    'menu' => [
        [
            'label' => 'Kegiatan UPD',
            'route' => 'ekskul.index',
            'permission' => 'ekskul.kelola'
        ],
        [
            'label' => 'Keanggotaan Siswa',
            'route' => 'ekskul.anggota',
            'permission' => 'ekskul.kelola'
        ],
        [
            'label' => 'Rekapitulasi UPD',
            'route' => 'ekskul.rekap',
            'permission' => 'ekskul.kelola'
        ],
        [
            'label' => 'Pengaturan UPD',
            'route' => 'ekskul.setting',
            'permission' => 'ekskul.setting'
        ]
    ],
    'permissions' => [
        'ekskul' => [
            'label' => 'Unit Pengembangan Diri (UPD)',
            'actions' => ['kelola', 'setting']
        ]
    ]
];
