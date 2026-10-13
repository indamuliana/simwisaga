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
            'label' => 'Rekap Nilai Semester',
            'route' => 'penilaian.rekap',
        ],
        [
            'label' => 'Transkrip Siswa Tahunan',
            'route' => 'penilaian.transkrip',
        ]
    ],
    'permissions' => [
        'penilaian' => ['lihat', 'edit']
    ]
];
