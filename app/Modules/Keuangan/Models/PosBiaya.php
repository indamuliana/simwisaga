<?php

namespace App\Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class PosBiaya extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'keuangan_pos_biayas';

    protected $fillable = [
        'kode',
        'nama',
        'tipe',
        'is_aktif',
        'deskripsi',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function tarifs(): HasMany
    {
        return $this->hasMany(Tarif::class, 'pos_biaya_id');
    }

    public function siswaTarifs(): HasMany
    {
        return $this->hasMany(SiswaTarif::class, 'pos_biaya_id');
    }

    public function tagihans(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'pos_biaya_id');
    }

    public function getBadgeTipeAttribute(): string
    {
        return match ($this->tipe) {
            'bulanan' => 'Rutin Bulanan',
            'bebas' => 'Bebas / Plafon DSP',
            'insidental' => 'Kegiatan Insidental',
            default => ucfirst($this->tipe),
        };
    }
}

