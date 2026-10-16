<?php

namespace App\Modules\Penilaian\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Akademik\Models\TahunAjaran;

class PengaturanPenilaian extends Model
{
    protected $table = 'pengaturan_penilaians';

    protected $fillable = [
        'bobot_tp',
        'bobot_sts',
        'bobot_nh',
        'nh_default_value',
        'predikat_intervals',
    ];

    protected $casts = [
        'bobot_tp' => 'integer',
        'bobot_sts' => 'integer',
        'bobot_nh' => 'integer',
        'nh_default_value' => 'float',
        'predikat_intervals' => 'array',
    ];

    /**
     * Get active settings or default
     */
    public static function getActive(?int $tahunAjaranId = null): self
    {
        $default = static::first();
        if ($default) {
            return $default;
        }

        // Fallback default instance if not found in DB
        return new static([
            'bobot_tp' => 50,
            'bobot_sts' => 30,
            'bobot_nh' => 20,
            'nh_default_value' => 100.00,
            'predikat_intervals' => self::defaultIntervals(),
        ]);
    }

    public static function defaultIntervals(): array
    {
        return [
            ['min' => 94.0, 'max' => 100.0, 'label' => 'Mahir', 'deskripsi' => 'Menunjukkan penguasaan materi yang sangat mendalam dan mandiri.'],
            ['min' => 88.0, 'max' => 93.99, 'label' => 'Cakap', 'deskripsi' => 'Menunjukkan penguasaan materi yang baik dan mampu menerapkan secara tepat.'],
            ['min' => 81.0, 'max' => 87.99, 'label' => 'Layak', 'deskripsi' => 'Menunjukkan penguasaan materi yang memadai sesuai kriteria ketercapaian.'],
            ['min' => 75.0, 'max' => 80.99, 'label' => 'Mulai Berkembang', 'deskripsi' => 'Menunjukkan pemahaman dasar namun masih memerlukan bimbingan terarah.'],
        ];
    }
}
