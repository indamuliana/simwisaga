<?php

namespace App\Modules\Sarpras\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'sarpras_peminjamans';

    protected $fillable = [
        'nomor_peminjaman',
        'aset_id',
        'peminjam_nama',
        'peminjam_tipe',
        'user_id',
        'kontak_peminjam',
        'keperluan',
        'tanggal_pinjam',
        'batas_kembali',
        'tanggal_kembali',
        'kondisi_awal',
        'kondisi_kembali',
        'status',
        'catatan',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'batas_kembali' => 'date',
            'tanggal_kembali' => 'date',
        ];
    }

    public function aset(): BelongsTo
    {
        return $this->belongsTo(Aset::class, 'aset_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateNomor(): string
    {
        $datePrefix = 'PJM-' . date('Ymd') . '-';
        $latest = static::where('nomor_peminjaman', 'like', "{$datePrefix}%")
            ->orderBy('id', 'desc')
            ->value('nomor_peminjaman');

        $num = 1;
        if ($latest) {
            $parts = explode('-', $latest);
            $num = ((int) end($parts)) + 1;
        }

        return sprintf('%s%04d', $datePrefix, $num);
    }
}
