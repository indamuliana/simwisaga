<?php

namespace App\Modules\BeasiswaOta\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlokasiDetail extends Model
{
    protected $table = 'ota_alokasi_details';

    protected $fillable = [
        'alokasi_id',
        'komponen_biaya_id',
        'jenis_komponen',
        'nama_komponen',
        'bulan',
        'tahun',
        'nominal',
        'keterangan',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tahun' => 'integer',
    ];

    public function alokasi(): BelongsTo
    {
        return $this->belongsTo(Alokasi::class, 'alokasi_id');
    }

    public function komponenBiaya(): BelongsTo
    {
        return $this->belongsTo(KomponenBiaya::class, 'komponen_biaya_id');
    }
}
