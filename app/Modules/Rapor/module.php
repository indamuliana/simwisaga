<?php

return [
    'name' => 'Rapor',
    'label' => 'Cetak Rapor',
    'icon' => 'document-text',
    'order' => 80,
    'menu' => [
        [
            'label' => 'Rapor Semester',
            'route' => 'rapor.index',
        ]
    ],
    'permissions' => [
        'rapor' => [
            'label' => 'Cetak Rapor',
            'actions' => ['lihat', 'cetak']
        ]
    ]
];
