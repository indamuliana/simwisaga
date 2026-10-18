<?php

namespace App\Modules\AdministrasiWeb\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WebPage extends Model
{
    use HasFactory;

    protected $table = 'web_pages';

    protected $fillable = [
        'judul',
        'slug',
        'ringkasan',
        'konten',
        'gambar_banner',
        'meta_deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->judul);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function gambarBannerUrl(): ?string
    {
        if (! $this->gambar_banner) {
            return null;
        }

        if (Str::startsWith($this->gambar_banner, ['http://', 'https://'])) {
            return $this->gambar_banner;
        }

        return asset('storage/' . $this->gambar_banner);
    }

    public function getUrlAttribute(): string
    {
        return url('/halaman/' . $this->slug);
    }
}
