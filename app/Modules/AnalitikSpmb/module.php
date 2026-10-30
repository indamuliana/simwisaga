<?php

return [
    'name' => 'AnalitikSpmb',
    'label' => 'Analitik SPMB',
    'icon' => 'chart-bar',
    'order' => 14,
    'menu' => [
        ['label' => 'Asal SMP / MTs', 'route' => 'analitik-spmb.asal-smp', 'permission' => 'analitik_spmb.asal_smp.lihat'],
    ],
    'permissions' => [
        'analitik_spmb.asal_smp' => [
            'label' => 'Analitik Asal SMP / MTs',
            'actions' => ['lihat', 'kelola'],
        ],
    ],
];
