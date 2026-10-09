<?php

namespace App\Modules\AiAssistant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    use HasFactory;

    protected $table = 'ai_settings';

    protected $fillable = [
        'name',
        'style',
        'base_prompt',
        'is_active',
    ];
}
