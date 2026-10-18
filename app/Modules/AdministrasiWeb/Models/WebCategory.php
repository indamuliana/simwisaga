<?php

namespace App\Modules\AdministrasiWeb\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebCategory extends Model
{
    protected $guarded = [];

    public function articles(): HasMany
    {
        return $this->hasMany(WebArticle::class, 'kategori_id');
    }

    /**
     * Pastikan kategori bawaan tersedia jika tabel masih kosong
     */
    public static function seedDefaultsIfEmpty(): void
    {
        if (static::count() === 0) {
            $defaults = [
                ['nama' => 'Berita Sekolah', 'slug' => 'berita-sekolah', 'warna_badge' => 'blue', 'urutan' => 1],
                ['nama' => 'Prestasi & Lomba', 'slug' => 'prestasi-lomba', 'warna_badge' => 'amber', 'urutan' => 2],
                ['nama' => 'SPMB & Info', 'slug' => 'spmb-info', 'warna_badge' => 'emerald', 'urutan' => 3],
                ['nama' => 'Kegiatan Siswa', 'slug' => 'kegiatan-siswa', 'warna_badge' => 'purple', 'urutan' => 4],
            ];
            foreach ($defaults as $item) {
                static::create($item);
            }
        }
    }
}
