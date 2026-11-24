<?php

return [
    'name' => 'AiAssistant',
    'label' => 'Pengaturan Analitik & AI',
    'group' => 'PENGATURAN',
    'icon' => 'cog',
    'order' => 95,
    'menu' => [
        ['label' => 'Perbaikan Data Asal SMP', 'route' => 'analitik-spmb.perbaikan-smp', 'permission' => 'analitik_spmb.perbaikan_smp.lihat'],
        ['label' => 'Master Data SMP', 'route' => 'analitik-spmb.master-smp', 'permission' => 'analitik_spmb.master_smp.lihat'],
        ['label' => 'Pengaturan Persona', 'route' => 'ai-assistant.settings', 'permission' => 'ai-assistant.settings'],
    ],
    'permissions' => [
        'ai-assistant' => [
            'label' => 'AI Assistant',
            'actions' => ['chat', 'settings'],
        ],
    ],
];
