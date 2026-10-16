<?php

namespace App\Modules\Akademik\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Rayon extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'nama',
        'wilayah_cakupan',
        'pembimbing_id',
        'keterangan',
    ];

    public function pembimbing(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }

    public function siswas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Modules\Kesiswaan\Models\Siswa::class, 'rayon_id');
    }
}
