<?php

namespace App\Modules\Kepesantrenan\Models;

use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesantrenHalaqohSiswa extends Model
{
    protected $table = 'pesantren_halaqoh_siswas';

    protected $fillable = [
        'halaqoh_id',
        'siswa_id',
    ];

    public function halaqoh(): BelongsTo
    {
        return $this->belongsTo(PesantrenHalaqoh::class, 'halaqoh_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
