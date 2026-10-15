<?php

namespace App\Modules\BeasiswaOta\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasMutasi extends Model
{
    protected $table = 'ota_kas_mutasis';

    protected $fillable = [
        'tanggal',
        'tipe',
        'pos_kas',
        'kelompok_id',
        'referensi_tipe',
        'referensi_id',
        'nominal',
        'saldo_pos',
        'deskripsi',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
        'saldo_pos' => 'decimal:2',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_id');
    }
}
