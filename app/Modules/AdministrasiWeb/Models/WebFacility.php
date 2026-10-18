<?php

namespace App\Modules\AdministrasiWeb\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class WebFacility extends Model
{
    use HasFactory;

    protected $table = 'web_facilities';

    protected $fillable = [
        'category',
        'cat_label',
        'badge',
        'title',
        'desc',
        'highlight',
        'image',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('web_facilities_active'));
        static::deleted(fn () => Cache::forget('web_facilities_active'));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function imageUrl(): string
    {
        if (empty($this->image)) {
            return asset('images/landing/lab-komputer-pplg.jpg');
        }

        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        if (file_exists(public_path($this->image))) {
            return asset($this->image);
        }

        return asset('storage/' . $this->image);
    }

    public static function getActiveFacilities()
    {
        return Cache::rememberForever('web_facilities_active', function () {
            return self::active()->orderBy('urutan')->get();
        });
    }
}
