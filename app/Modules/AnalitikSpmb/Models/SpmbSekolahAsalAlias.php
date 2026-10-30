<?php

namespace App\Modules\AnalitikSpmb\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class SpmbSekolahAsalAlias extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'sekolah_asal_id',
        'alias_raw',
    ];

    public function sekolahAsal(): BelongsTo
    {
        return $this->belongsTo(SpmbSekolahAsal::class, 'sekolah_asal_id');
    }
}
