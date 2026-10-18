<?php

namespace App\Modules\AdministrasiWeb\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class WebSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'value' => 'array',
    ];

    private const CACHE_PREFIX = 'web_setting.';
    private const ALL_CACHE_KEY = 'web_setting.merged_all';

    protected static function booted(): void
    {
        static::saved(function (self $setting) {
            Cache::forget(self::CACHE_PREFIX . $setting->group);
            Cache::forget(self::ALL_CACHE_KEY);
        });

        static::deleted(function (self $setting) {
            Cache::forget(self::CACHE_PREFIX . $setting->group);
            Cache::forget(self::ALL_CACHE_KEY);
        });
    }

    /**
     * Ambil data grup pengaturan dari database dengan fallback ke config/landing.php
     */
    public static function getGroup(string $group, ?array $default = null): array
    {
        $default ??= config("landing.{$group}", []);

        return Cache::rememberForever(self::CACHE_PREFIX . $group, function () use ($group, $default) {
            $record = static::where('group', $group)->first();
            if ($record && is_array($record->value)) {
                return self::mergeConfigGroups($default, $record->value);
            }
            return $default;
        });
    }

    /**
     * Simpan pengaturan untuk 1 grup
     */
    public static function setGroup(string $group, array $values): self
    {
        return static::updateOrCreate(
            ['group' => $group, 'key' => $group],
            ['value' => $values]
        );
    }

    /**
     * Menggabungkan konfigurasi dasar file config/landing.php dengan data dinamis database.
     */
    public static function mergedLandingConfig(): array
    {
        return Cache::rememberForever(self::ALL_CACHE_KEY, function () {
            $baseConfig = config('landing') ?? [];

            try {
                $dbSettings = static::all();
                foreach ($dbSettings as $setting) {
                    if (isset($baseConfig[$setting->group]) && is_array($setting->value)) {
                        $baseConfig[$setting->group] = self::mergeConfigGroups($baseConfig[$setting->group], $setting->value);
                    } elseif (is_array($setting->value)) {
                        $baseConfig[$setting->group] = $setting->value;
                    }
                }
            } catch (\Throwable $e) {
                // Fallback aman jika tabel belum dimigrasi
                report($e);
            }

            return $baseConfig;
        });
    }

    /**
     * Helper penggabungan rekursif yang aman untuk array bertipe list (numerik)
     */
    public static function mergeConfigGroups(array $base, array $replacement): array
    {
        foreach ($replacement as $key => $value) {
            if (isset($base[$key]) && is_array($base[$key]) && is_array($value)) {
                if (array_is_list($base[$key]) || array_is_list($value)) {
                    $base[$key] = $value;
                } else {
                    $base[$key] = self::mergeConfigGroups($base[$key], $value);
                }
            } else {
                $base[$key] = $value;
            }
        }
        return $base;
    }

    /**
     * Resolusi URL gambar dinamis (asset lokal, storage upload, atau link http).
     */
    public static function resolveImageUrl(?string $path, string $default = ''): string
    {
        if (empty($path)) {
            return $default ? asset(ltrim($default, '/')) : '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) {
            return $path;
        }

        if (str_starts_with($path, 'images/')) {
            return asset(ltrim($path, '/'));
        }

        if (str_starts_with($path, 'storage/')) {
            return asset(ltrim($path, '/'));
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}
