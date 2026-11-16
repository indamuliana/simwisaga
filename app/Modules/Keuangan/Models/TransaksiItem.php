<?php

namespace App\Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class TransaksiItem extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'keuangan_transaksi_items';

    protected $fillable = [
        'transaksi_id',
        'tagihan_id',
        'pos_biaya_id',
        'nominal_dibayar',
        'sisa_setelah_bayar',
        'keterangan',
    ];

    protected $casts = [
        'nominal_dibayar' => 'decimal:2',
        'sisa_setelah_bayar' => 'decimal:2',
    ];

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id');
    }

    public function posBiaya(): BelongsTo
    {
        return $this->belongsTo(PosBiaya::class, 'pos_biaya_id');
    }
}

