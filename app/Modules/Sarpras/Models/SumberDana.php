<?php

namespace App\Modules\Sarpras\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SumberDana extends Model
{
    use HasFactory;

    protected $table = 'sarpras_sumber_danas';

    protected $fillable = [
        'kode',
        'nama',
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
        return $this->hasMany(Aset::class, 'sumber_dana_id');
    }
}
