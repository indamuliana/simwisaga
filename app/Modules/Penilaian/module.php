<?php

return [
    'name' => 'Penilaian',
    'label' => 'Penilaian Pembelajaran',
    'icon' => 'academic-cap',
    'order' => 60,
    'menu' => [
        [
            'label' => 'Dashboard Penilaian',
            'route' => 'penilaian.index',
            'permission' => 'penilaian.lihat',
        ],
        [
            'label' => 'Penilaian Mapil',
            'route' => 'penilaian.mapil.index',
            'permission' => 'penilaian.lihat',
        ],
        [
            'label' => 'Rekap Nilai Semester',
            'route' => 'penilaian.rekap',
            'permission' => 'penilaian.lihat',
        ],
        [
            'label' => 'Transkrip Siswa Tahunan',
            'route' => 'penilaian.transkrip',
            'permission' => 'penilaian.lihat',
        ],
    ],
    'permissions' => [
        'penilaian' => [
            'label' => 'Penilaian Pembelajaran',
            'actions' => ['lihat', 'tambah', 'edit', 'hapus', 'cetak'],
        ],
    ],
];
