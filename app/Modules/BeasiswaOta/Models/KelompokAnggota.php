<?php

namespace App\Modules\BeasiswaOta\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KelompokAnggota extends Model
{
    protected $table = 'ota_kelompok_anggotas';

    protected $fillable = [
        'kelompok_id',
        'donatur_id',
        'nominal_komitmen',
        'keterangan',
    ];

    protected $casts = [
        'nominal_komitmen' => 'decimal:2',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_id');
    }

    public function donatur(): BelongsTo
    {
        return $this->belongsTo(Donatur::class, 'donatur_id');
    }
}
