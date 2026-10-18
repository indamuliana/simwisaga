<?php

namespace App\Modules\AdministrasiWeb\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class WebTestimonial extends Model
{
    use HasFactory;

    protected $table = 'web_testimonials';

    protected $fillable = [
        'category',
        'question',
        'quote',
        'name',
        'role',
        'image',
        'rating',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'integer',
        'urutan' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('web_testimonials_active'));
        static::deleted(fn () => Cache::forget('web_testimonials_active'));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function imageUrl(): string
    {
        if (empty($this->image)) {
            return asset('images/landing/suasana-belajar-aktif.jpg');
        }

        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        if (file_exists(public_path($this->image))) {
            return asset($this->image);
        }

        return asset('storage/' . $this->image);
    }

    public static function getActiveTestimonials()
    {
        return Cache::rememberForever('web_testimonials_active', function () {
            return self::active()->orderBy('urutan')->get();
        });
    }
}
