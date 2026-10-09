<?php

namespace App\Modules\Sistem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Contracts\Auditable;

class School extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    private const CACHE_KEY = 'school.current';

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /** Default sebelum identitas diisi di halaman Identitas Sekolah. */
    public static function defaults(): array
    {
        return [
            'nama' => 'SMK Wikrama 1 Garut',
            'nama_aplikasi' => 'SIRAGA',
            'kepanjangan_aplikasi' => 'Sistem Informasi Wikrama Garut',
        ];
    }

    /** Identitas sekolah aktif (di-cache; selalu mengembalikan objek). */
    public static function current(): self
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::query()->first() ?? new static(static::defaults()));
    }

    public function logoSekolahUrl(): ?string
    {
        return $this->urlFor($this->logo_sekolah_path);
    }

    public function logoAplikasiUrl(): ?string
    {
        return $this->urlFor($this->logo_aplikasi_path);
    }

    public function kopSuratUrl(): ?string
    {
        return $this->urlFor($this->kop_surat_path);
    }

    private function urlFor(?string $path): ?string
    {
        return $path && Storage::disk('public')->exists($path) ? Storage::disk('public')->url($path) : null;
    }
}
