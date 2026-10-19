<?php

namespace App\Modules\Sarpras\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'sarpras_kategoris';

    protected $fillable = [
        'kode',
        'nama',
        'jenis',
        'deskripsi',
    ];

    public function asets(): HasMany
    {
        return $this->hasMany(Aset::class, 'kategori_id');
    }

    public function bhps(): HasMany
    {
        return $this->hasMany(Bhp::class, 'kategori_id');
    }

    public function scopeAsetTetap($query)
    {
        return $query->where('jenis', 'aset_tetap');
    }

    public function scopeHabisPakai($query)
    {
        return $query->where('jenis', 'habis_pakai');
    }
}
