<?php

return [
    'name' => 'Ekstrakurikuler',
    'label' => 'Ekstrakurikuler',
    'icon' => 'sparkles',
    'order' => 70,
    'menu' => [
        [
            'label' => 'Kelola Ekskul',
            'route' => 'ekskul.index',
            'permission' => 'ekskul.kelola'
        ]
    ],
    'permissions' => [
        'ekskul' => [
            'label' => 'Ekstrakurikuler',
            'actions' => ['kelola']
        ]
    ]
];
