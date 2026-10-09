<?php

return [
    'name' => 'DashboardData',
    'label' => 'Dashboard Data',
    'order' => 15,
    'topbar' => [],
    'menu' => [
        ['label' => 'Dashboard', 'route' => 'dashboard-data.index', 'permission' => 'dashboard_data.dashboard.lihat'],
        ['label' => 'Data Program', 'route' => 'dashboard-data.program', 'permission' => 'dashboard_data.program.lihat'],
        ['label' => 'Data Jurusan', 'route' => 'dashboard-data.jurusan', 'permission' => 'dashboard_data.jurusan.lihat'],
        ['label' => 'Data Rombel', 'route' => 'dashboard-data.rombel', 'permission' => 'dashboard_data.rombel.lihat'],
        ['label' => 'Data Sekolah Asal', 'route' => 'dashboard-data.sekolah-asal', 'permission' => 'dashboard_data.sekolah_asal.lihat'],
    ],
    'permissions' => [
        'dashboard_data.dashboard' => ['label' => 'Dashboard Utama', 'actions' => ['lihat']],
        'dashboard_data.program' => ['label' => 'Data Program', 'actions' => ['lihat']],
        'dashboard_data.jurusan' => ['label' => 'Data Jurusan', 'actions' => ['lihat']],
        'dashboard_data.rombel' => ['label' => 'Data Rombel', 'actions' => ['lihat']],
        'dashboard_data.sekolah_asal' => ['label' => 'Data Sekolah Asal', 'actions' => ['lihat']],
    ],
];
