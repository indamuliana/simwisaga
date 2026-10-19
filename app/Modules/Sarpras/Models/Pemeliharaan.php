<?php

namespace App\Modules\Sarpras\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pemeliharaan extends Model
{
    use HasFactory;

    protected $table = 'sarpras_pemeliharaans';

    protected $fillable = [
        'nomor_tiket',
        'aset_id',
        'pelapor_id',
        'tanggal_lapor',
        'deskripsi_kerusakan',
        'tingkat_urgensi',
        'status',
        'tindakan_perbaikan',
        'teknisi_vendor',
        'biaya',
        'tanggal_selesai',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lapor' => 'date',
            'tanggal_selesai' => 'date',
            'biaya' => 'decimal:2',
        ];
    }

    public function aset(): BelongsTo
    {
        return $this->belongsTo(Aset::class, 'aset_id');
    }

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    public static function generateNomor(): string
    {
        $datePrefix = 'MTC-' . date('Ymd') . '-';
        $latest = static::where('nomor_tiket', 'like', "{$datePrefix}%")
            ->orderBy('id', 'desc')
            ->value('nomor_tiket');

        $num = 1;
        if ($latest) {
            $parts = explode('-', $latest);
            $num = ((int) end($parts)) + 1;
        }

        return sprintf('%s%04d', $datePrefix, $num);
    }
}
