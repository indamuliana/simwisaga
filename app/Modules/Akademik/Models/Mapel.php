<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Mapel extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public const KELOMPOK_UMUM = 'umum';
    public const KELOMPOK_KEJURUAN = 'kejuruan';
    public const KELOMPOK_MULOK = 'muatan_lokal';
    public const KELOMPOK_P5_PKL = 'p5_pkl';

    public const DAFTAR_KELOMPOK = [
        self::KELOMPOK_UMUM => 'Muatan Umum',
        self::KELOMPOK_KEJURUAN => 'Muatan Kejuruan',
        self::KELOMPOK_MULOK => 'Muatan Lokal',
        self::KELOMPOK_P5_PKL => 'Projek Penguatan Profil Pelajar Pancasila (P5) & PKL',
    ];

    protected $fillable = [
        'kode',
        'nama',
        'kelompok',
        'kkm_default',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'kkm_default' => 'integer',
        'urutan' => 'integer',
    ];

    public function strukturKurikulums(): HasMany
    {
        return $this->hasMany(StrukturKurikulum::class);
    }

    public function getKelompokLabelAttribute(): string
    {
        return self::DAFTAR_KELOMPOK[$this->kelompok] ?? ucfirst($this->kelompok);
    }
}
