<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Semester extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'tahun_ajaran_id',
        'semester',
        'is_active',
        'nilai_locked_at',
        'absensi_locked_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'nilai_locked_at' => 'datetime',
        'absensi_locked_at' => 'datetime',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function isLockedForNilai(): bool
    {
        return $this->nilai_locked_at !== null;
    }

    public function isLockedForAbsensi(): bool
    {
        return $this->absensi_locked_at !== null;
    }

    public function getLabelAttribute(): string
    {
        return "{$this->tahunAjaran?->nama} " . ucfirst($this->semester);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
