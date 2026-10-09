<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Jurusan extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'kode',
        'nama',
        'kode_alias',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'kode_alias' => 'array',
        'is_active' => 'boolean',
    ];

    public function rombels(): HasMany
    {
        return $this->hasMany(Rombel::class);
    }

    public function strukturKurikulums(): HasMany
    {
        return $this->hasMany(StrukturKurikulum::class);
    }
}
