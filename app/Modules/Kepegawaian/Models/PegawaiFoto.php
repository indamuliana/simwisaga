<?php

namespace App\Modules\Kepegawaian\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PegawaiFoto extends Model
{
    protected $table = 'pegawai_fotos';

    protected $fillable = [
        'pegawai_id',
        'slot',
        'foto_path',
        'keterangan',
    ];

    protected $casts = [
        'slot' => 'integer',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function getIsUtamaAttribute(): bool
    {
        return $this->slot === 1;
    }
}
