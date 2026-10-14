<?php

return [
    'name' => 'Rapor',
    'label' => 'Rapor Akademik',
    'icon' => 'document-text',
    'order' => 80,
    'menu' => [
        [
            'label' => 'Rapor Wikrama (9 Minggu)',
            'route' => 'rapor.wikrama',
        ],
        [
            'label' => 'Rapor Pemerintah (Semester)',
            'route' => 'rapor.pemerintah',
        ],
        [
            'label' => 'Catatan Wali Kelas / Rayon',
            'route' => 'rapor.catatan-wali-kelas',
        ],
    ],
    'permissions' => [
        'rapor' => [
            'label' => 'Cetak Rapor',
            'actions' => ['lihat', 'cetak']
        ]
    ]
];
