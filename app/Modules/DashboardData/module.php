<?php

return [
    'name' => 'DashboardData',
    'label' => 'Dashboard Data',
    'group' => 'ANALISIS',
    'order' => 15,
    'topbar' => [],
    'menu' => [],
    'permissions' => [
        'dashboard_data.dashboard' => ['label' => 'Dashboard Utama', 'actions' => ['lihat']],
        'dashboard_data.rayon' => ['label' => 'Data Rayon', 'actions' => ['lihat']],
        'dashboard_data.program' => ['label' => 'Data Program', 'actions' => ['lihat']],
        'dashboard_data.jurusan' => ['label' => 'Data Jurusan', 'actions' => ['lihat']],
        'dashboard_data.rombel' => ['label' => 'Data Rombel', 'actions' => ['lihat']],
        'dashboard_data.sekolah_asal' => ['label' => 'Data Sekolah Asal', 'actions' => ['lihat']],
    ],
];
