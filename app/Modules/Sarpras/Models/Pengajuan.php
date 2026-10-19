<?php

namespace App\Modules\Sarpras\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Pengajuan extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = 'sarpras_pengajuans';

    protected $fillable = [
        'nomor_pengajuan',
        'user_id',
        'nama_barang',
        'kategori_id',
        'spesifikasi',
        'jumlah',
        'estimasi_harga_satuan',
        'total_estimasi',
        'urgensi',
        'ruangan_id',
        'status',
        'catatan_sarpras',
        'catatan_kepsek',
        'catatan_bendahara',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'estimasi_harga_satuan' => 'decimal:2',
            'total_estimasi' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public static function generateNomor(): string
    {
        $datePrefix = 'PP-' . date('Ymd') . '-';
        $latest = static::where('nomor_pengajuan', 'like', "{$datePrefix}%")
            ->orderBy('id', 'desc')
            ->value('nomor_pengajuan');

        $num = 1;
        if ($latest) {
            $parts = explode('-', $latest);
            $num = ((int) end($parts)) + 1;
        }

        return sprintf('%s%04d', $datePrefix, $num);
    }
}
