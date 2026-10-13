<?php

namespace App\Modules\SeniBudaya\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Akademik\Models\Semester;
use Illuminate\Database\Eloquent\Builder;

class SeniBudayaTp extends Model
{
    protected $fillable = [
        'seni_budaya_id',
        'semester_id',
        'kode',
        'deskripsi',
        'target_asesmen',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'is_active' => 'boolean',
    ];

    public function seniBudaya()
    {
        return $this->belongsTo(SeniBudaya::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Scope untuk memfilter TP sesuai target periode asesmen (STS 1, SAS, STS 2, SAT)
     */
    public function scopeForAsesmen(Builder $query, string $period): Builder
    {
        $cleanPeriod = strtoupper(trim($period));

        return $query->where(function ($q) use ($cleanPeriod) {
            $q->where('target_asesmen', 'semua')
                ->orWhereNull('target_asesmen')
                ->orWhere('target_asesmen', 'like', "%{$cleanPeriod}%");
        });
    }

    /**
     * Label representasi manusiawi untuk target asesmen
     */
    public function getTargetAsesmenLabelAttribute(): string
    {
        return match ($this->target_asesmen) {
            'STS 1' => 'STS 1 (Tengah Sem. Ganjil)',
            'SAS' => 'SAS (Akhir Sem. Ganjil)',
            'STS 1, SAS', 'STS 1 & SAS' => 'STS 1 & SAS (Ganjil Penuh)',
            'STS 2' => 'STS 2 (Tengah Sem. Genap)',
            'SAT' => 'SAT (Akhir Tahun)',
            'STS 2, SAT', 'STS 2 & SAT' => 'STS 2 & SAT (Genap Penuh)',
            'semua' => 'Semua Asesmen',
            default => $this->target_asesmen ?? 'Semua Asesmen',
        };
    }
}
