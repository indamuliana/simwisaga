<?php

return [
    'name' => 'SeniBudaya',
    'label' => 'Seni Budaya',
    'icon' => 'sparkles',
    'order' => 65,
    'menu' => [
        [
            'label' => 'Dashboard & Cabang',
            'route' => 'seni-budaya.index',
            'permission' => 'seni_budaya.kelola'
        ],
        [
            'label' => 'Keanggotaan Siswa',
            'route' => 'seni-budaya.anggota',
            'permission' => 'seni_budaya.kelola'
        ],
        [
            'label' => 'Tujuan Pembelajaran (TP)',
            'route' => 'seni-budaya.tp',
            'permission' => 'seni_budaya.kelola'
        ],
        [
            'label' => 'Rekapitulasi Nilai',
            'route' => 'seni-budaya.rekap',
            'permission' => 'seni_budaya.kelola'
        ],
    ],
    'permissions' => [
        'seni_budaya' => [
            'label' => 'Seni Budaya',
            'actions' => ['kelola', 'setting']
        ]
    ]
];
