<?php

namespace App\Modules\Sarpras\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'sarpras_ruangans';

    protected $fillable = [
        'kode_ruangan',
        'nama_ruangan',
        'lokasi',
        'keterangan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function asets(): HasMany
    {
        return $this->hasMany(Aset::class, 'ruangan_id');
    }

    public function pengajuans(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'ruangan_id');
    }

    public function bhpMutasis(): HasMany
    {
        return $this->hasMany(BhpMutasi::class, 'ruangan_id');
    }
}
