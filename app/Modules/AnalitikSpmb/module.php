<?php

return [
    'name' => 'AnalitikSpmb',
    'label' => 'Analitik SPMB',
    'group' => 'ANALISIS',
    'icon' => 'chart-bar',
    'order' => 10,
    'menu' => [
        ['label' => 'Dashboard Data', 'route' => 'dashboard-data.index', 'permission' => 'dashboard_data.dashboard.lihat'],
        ['label' => 'AI Asistent', 'route' => 'ai-assistant.chat.view', 'permission' => 'ai-assistant.chat'],
        ['label' => 'Asal SMP / MTS', 'route' => 'analitik-spmb.asal-smp', 'permission' => 'analitik_spmb.asal_smp.lihat'],
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
