<?php

namespace App\Modules\Keuangan\Models;

use App\Models\User;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Transaksi extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'keuangan_transaksis';

    protected $fillable = [
        'nomor_transaksi',
        'kode_verifikasi',
        'siswa_id',
        'user_id',
        'rekening_id',
        'channel_pembayaran',
        'total_bayar',
        'tanggal_bayar',
        'nama_penyetor',
        'catatan',
    ];

    protected $casts = [
        'total_bayar' => 'decimal:2',
        'tanggal_bayar' => 'datetime',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rekening(): BelongsTo
    {
        return $this->belongsTo(Rekening::class, 'rekening_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransaksiItem::class, 'transaksi_id');
    }

    public static function generateNomorTransaksi(): string
    {
        $prefix = 'KWT/' . date('Ym') . '/';
        $last = self::where('nomor_transaksi', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->lockForUpdate()
            ->value('nomor_transaksi');

        $seq = 1;
        if ($last) {
            $parts = explode('/', $last);
            $seq = (int) end($parts) + 1;
        }

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}

