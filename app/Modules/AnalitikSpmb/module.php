<?php

return [
    'name' => 'AnalitikSpmb',
    'label' => 'Analitik SPMB',
    'icon' => 'chart-bar',
    'order' => 14,
    'menu' => [
        ['label' => 'Asal SMP / MTs', 'route' => 'analitik-spmb.asal-smp', 'permission' => 'analitik_spmb.asal_smp.lihat'],
        ['label' => 'Perbaikan Data Asal SMP', 'route' => 'analitik-spmb.perbaikan-smp', 'permission' => 'analitik_spmb.perbaikan_smp.lihat'],
        ['label' => 'Master Data SMP', 'route' => 'analitik-spmb.master-smp', 'permission' => 'analitik_spmb.master_smp.lihat'],
        ['label' => 'Sebaran Wilayah', 'route' => 'analitik-spmb.wilayah', 'permission' => 'analitik_spmb.wilayah.lihat'],
    ],
    'permissions' => [
        'analitik_spmb.asal_smp' => [
            'label' => 'Analitik Asal SMP / MTs',
            'actions' => ['lihat', 'kelola'],
        ],
        'analitik_spmb.perbaikan_smp' => [
            'label' => 'Perbaikan Data Asal SMP',
            'actions' => ['lihat', 'kelola'],
        ],
        'analitik_spmb.master_smp' => [
            'label' => 'Master Data SMP',
            'actions' => ['lihat', 'kelola', 'import'],
        ],
        'analitik_spmb.wilayah' => [
            'label' => 'Analitik Sebaran Wilayah',
            'actions' => ['lihat', 'kelola', 'ekspor'],
        ],
    ],
];
