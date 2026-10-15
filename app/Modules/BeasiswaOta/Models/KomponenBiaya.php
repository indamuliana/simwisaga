<?php

namespace App\Modules\BeasiswaOta\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KomponenBiaya extends Model
{
    protected $table = 'ota_komponen_biayas';

    protected $fillable = [
        'siswa_penerima_id',
        'jenis_komponen',
        'nama_komponen',
        'nominal_per_bulan',
        'total_target_semester',
        'keterangan',
    ];

    protected $casts = [
        'nominal_per_bulan' => 'decimal:2',
        'total_target_semester' => 'decimal:2',
    ];

    public function siswaPenerima(): BelongsTo
    {
        return $this->belongsTo(SiswaPenerima::class, 'siswa_penerima_id');
    }

    public function alokasiDetails(): HasMany
    {
        return $this->hasMany(AlokasiDetail::class, 'komponen_biaya_id');
    }

    public function getTotalTeralokasiAttribute(): float
    {
        return (float) $this->alokasiDetails()->sum('nominal');
    }
}
