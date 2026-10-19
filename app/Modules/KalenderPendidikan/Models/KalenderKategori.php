<?php

namespace App\Modules\KalenderPendidikan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class KalenderKategori extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'kalender_kategoris';

    protected $fillable = [
        'nama',
        'warna',
        'deskripsi',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    public function agendas(): HasMany
    {
        return $this->hasMany(KalenderAgenda::class, 'kategori_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('urutan')->orderBy('nama');
    }
}
