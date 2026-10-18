<?php

namespace App\Modules\AdministrasiWeb\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class WebMenu extends Model
{
    use HasFactory;

    protected $table = 'web_menus';

    protected $fillable = [
        'lokasi',
        'label',
        'url',
        'target',
        'urutan',
        'is_active',
        'parent_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('web_menus_header');
            Cache::forget('web_menus_footer');
        });

        static::deleted(function () {
            Cache::forget('web_menus_header');
            Cache::forget('web_menus_footer');
        });
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('urutan');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHeader($query)
    {
        return $query->where('lokasi', 'header');
    }

    public function scopeFooter($query)
    {
        return $query->where('lokasi', 'footer');
    }

    public static function getHeaderMenu()
    {
        return Cache::rememberForever('web_menus_header', function () {
            return self::header()
                ->active()
                ->whereNull('parent_id')
                ->with('children')
                ->orderBy('urutan')
                ->get();
        });
    }

    public static function getFooterMenu()
    {
        return Cache::rememberForever('web_menus_footer', function () {
            return self::footer()
                ->active()
                ->whereNull('parent_id')
                ->with('children')
                ->orderBy('urutan')
                ->get();
        });
    }
}
