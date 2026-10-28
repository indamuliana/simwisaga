<?php

use App\Modules\AiAssistant\Controllers\AiAssistantController;
use App\Modules\AiAssistant\Controllers\ChatApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('ai-assistant')->name('ai-assistant.')->middleware(['role_or_permission:Super Admin|Admin|Kurikulum|Guru'])->group(function () {
    // Chat Routes
    Route::get('/', [AiAssistantController::class, 'chatView'])->name('chat.view');
    Route::post('/chat', [ChatApiController::class, 'chat'])->middleware('throttle:20,1')->name('chat.api');
    
    // Settings Routes (Admin / Super Admin only)
    Route::middleware('can:sistem.sekolah.edit')->group(function () {
        Route::get('/settings', [AiAssistantController::class, 'settingsView'])->name('settings');
        Route::post('/settings', [AiAssistantController::class, 'settingsUpdate'])->name('settings.update');
    });
});
