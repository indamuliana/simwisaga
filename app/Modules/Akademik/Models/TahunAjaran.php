<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class TahunAjaran extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'nama',
        'tgl_mulai',
        'tgl_selesai',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
    ];

    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class);
    }

    public function rombels(): HasMany
    {
        return $this->hasMany(Rombel::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
