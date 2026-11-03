<?php

namespace App\Modules\Kepesantrenan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PesantrenHalaqohPresensi extends Model
{
    protected $table = 'pesantren_halaqoh_presensis';

    protected $fillable = [
        'halaqoh_id',
        'tanggal',
        'waktu_sesi',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function halaqoh(): BelongsTo
    {
        return $this->belongsTo(PesantrenHalaqoh::class, 'halaqoh_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PesantrenHalaqohPresensiDetail::class, 'presensi_id');
    }
}
