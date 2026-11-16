<?php

namespace App\Modules\BeasiswaOta\Models;

use Illuminate\Database\Eloquent\Model;

class OtaSetting extends Model
{
    protected $fillable = ['key', 'value'];

    protected static array $cache = [];

    public static function get(string $key, $default = null)
    {
        if (array_key_exists($key, static::$cache)) {
            return static::$cache[$key] ?? $default;
        }

        $setting = static::where('key', $key)->first();
        static::$cache[$key] = $setting ? $setting->value : null;

        return static::$cache[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        static::$cache[$key] = $value;
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function clearCache(): void
    {
        static::$cache = [];
    }
}
