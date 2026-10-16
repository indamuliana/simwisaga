<?php

namespace App\Modules\Penilaian\Models;

use Illuminate\Database\Eloquent\Model;

class TujuanPembelajaran extends Model
{
    protected $fillable = [
        'penugasan_mengajar_id',
        'target_periode',
        'lingkup_materi',
        'urutan',
        'kode',
        'deskripsi',
        'alokasi_ah',
        'is_active',
    ];

    protected static function booted()
    {
        static::saving(function ($tp) {
            if (empty($tp->target_periode) || $tp->target_periode === '9M1') {
                if (!empty($tp->alokasi_ah)) {
                    $derived = match ($tp->alokasi_ah) {
                        'AH 1', 'AH 2' => '9M1',
                        'AH 3', 'AH 4' => '9M2',
                        'AH 5', 'AH 6' => '9M3',
                        'AH 7', 'AH 8' => '9M4',
                        default => null,
                    };
                    if ($derived) {
                        $tp->target_periode = $derived;
                    }
                } elseif ($tp->penugasan_mengajar_id) {
                    $pm = $tp->relationLoaded('penugasanMengajar')
                        ? $tp->penugasanMengajar
                        : \App\Modules\Kepegawaian\Models\PenugasanMengajar::with('semester')->find($tp->penugasan_mengajar_id);
                    if ($pm && str_contains(strtolower($pm->semester?->semester ?? ''), 'genap')) {
                        $tp->target_periode = '9M3';
                    }
                }
            }
        });
    }

    public function scopeByPeriode($query, ?string $periode)
    {
        if ($periode) {
            return $query->where('target_periode', $periode);
        }
        return $query;
    }

    public function penugasanMengajar()
    {
        return $this->belongsTo(\App\Modules\Kepegawaian\Models\PenugasanMengajar::class);
    }

    public function nilaiFormatifs()
    {
        return $this->hasMany(NilaiFormatif::class);
    }
}
