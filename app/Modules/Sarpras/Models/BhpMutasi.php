<?php

namespace App\Modules\Sarpras\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BhpMutasi extends Model
{
    use HasFactory;

    protected $table = 'sarpras_bhp_mutasis';

    protected $fillable = [
        'bhp_id',
        'jenis',
        'tanggal',
        'jumlah',
        'sisa_stok_setelahnya',
        'ruangan_id',
        'penerima',
        'keterangan',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah' => 'integer',
            'sisa_stok_setelahnya' => 'integer',
        ];
    }

    public function bhp(): BelongsTo
    {
        return $this->belongsTo(Bhp::class, 'bhp_id');
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
