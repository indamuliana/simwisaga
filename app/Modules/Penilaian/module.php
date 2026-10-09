<?php

return [
    'name' => 'Penilaian',
    'label' => 'Penilaian',
    'icon' => 'academic-cap',
    'order' => 60,
    'menu' => [
        [
            'label' => 'Dashboard Penilaian',
            'route' => 'penilaian.index',
        ],
        [
            'label' => 'Rekapitulasi',
            'route' => 'penilaian.rekap',
        ]
    ],
    'permissions' => [
        'penilaian' => ['lihat', 'edit']
    ]
];
