<?php

use App\Modules\AiAssistant\Controllers\AiAssistantController;
use App\Modules\AiAssistant\Controllers\ChatApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('ai-assistant')->name('ai-assistant.')->group(function () {
    // Chat Routes
    Route::get('/', [AiAssistantController::class, 'chatView'])->name('chat.view');
    Route::post('/chat', [ChatApiController::class, 'chat'])->name('chat.api');
    
    // Settings Routes
    Route::get('/settings', [AiAssistantController::class, 'settingsView'])->name('settings');
    Route::post('/settings', [AiAssistantController::class, 'settingsUpdate'])->name('settings.update');
});
