<?php

namespace App\Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Rekening extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'keuangan_rekenings';

    protected $fillable = [
        'nama_bank',
        'nomor_rekening',
        'atas_nama',
        'is_kas_tunai',
        'saldo_awal',
        'saldo_saat_ini',
        'is_aktif',
        'keterangan',
    ];

    protected $casts = [
        'is_kas_tunai' => 'boolean',
        'is_aktif' => 'boolean',
        'saldo_awal' => 'decimal:2',
        'saldo_saat_ini' => 'decimal:2',
    ];

    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'rekening_id');
    }

    public function pembayaranOnlines(): HasMany
    {
        return $this->hasMany(PembayaranOnline::class, 'rekening_tujuan_id');
    }

    public function getLabelRekeningAttribute(): string
    {
        if ($this->is_kas_tunai) {
            return $this->nama_bank . ' (' . $this->atas_nama . ')';
        }

        return "{$this->nama_bank} - {$this->nomor_rekening} a.n {$this->atas_nama}";
    }
}

