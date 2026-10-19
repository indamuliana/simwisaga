<?php

namespace App\Modules\Sarpras\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bhp extends Model
{
    use HasFactory;

    protected $table = 'sarpras_bhps';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori_id',
        'satuan',
        'stok_saat_ini',
        'stok_minimum',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'stok_saat_ini' => 'integer',
            'stok_minimum' => 'integer',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function mutasis(): HasMany
    {
        return $this->hasMany(BhpMutasi::class, 'bhp_id')->latest();
    }

    public function scopeMenipis($query)
    {
        return $query->whereColumn('stok_saat_ini', '<=', 'stok_minimum');
    }

    public static function generateKode(): string
    {
        $latest = static::orderBy('id', 'desc')->value('kode_barang');
        $num = 1;
        if ($latest && preg_match('/BHP-(\d+)/', $latest, $m)) {
            $num = ((int) $m[1]) + 1;
        }
        return sprintf('BHP-%04d', $num);
    }
}
