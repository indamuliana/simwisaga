<?php

namespace App\Modules\AdministrasiWeb\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class WebArticle extends Model
{
    protected $guarded = [];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(WebCategory::class, 'kategori_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penulis_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function gambarSampulUrl(): string
    {
        if (empty($this->gambar_sampul)) {
            return asset('images/landing/lab-komputer-pplg.jpg');
        }

        if (str_starts_with($this->gambar_sampul, 'http://') || str_starts_with($this->gambar_sampul, 'https://')) {
            return $this->gambar_sampul;
        }

        if (Storage::disk('public')->exists($this->gambar_sampul)) {
            return Storage::disk('public')->url($this->gambar_sampul);
        }

        if (file_exists(public_path($this->gambar_sampul))) {
            return asset($this->gambar_sampul);
        }

        return asset('images/landing/lab-komputer-pplg.jpg');
    }

    public function readingTime(): string
    {
        $words = str_word_count(strip_tags($this->konten));
        $minutes = max(1, (int) ceil($words / 180));
        return "{$minutes} menit baca";
    }
}
