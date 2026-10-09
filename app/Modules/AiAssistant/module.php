<?php

return [
    'name' => 'AiAssistant',
    'label' => 'AI Assistant',
    'order' => 90,
    'menu' => [
        ['label' => 'Chat AI', 'route' => 'ai-assistant.chat.view', 'permission' => 'ai-assistant.chat'],
        ['label' => 'Pengaturan Persona', 'route' => 'ai-assistant.settings', 'permission' => 'ai-assistant.settings'],
    ],
    'permissions' => [
        'ai-assistant' => [
            'label' => 'AI Assistant',
            'actions' => ['chat', 'settings'],
        ],
    ],
];
