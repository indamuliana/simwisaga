<?php

namespace App\Modules\Prestasi\Models;

use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrestasiSiswaAnggota extends Model
{
    protected $table = 'prestasi_siswa_anggotas';

    protected $fillable = [
        'prestasi_id',
        'siswa_id',
        'peran',
        'keterangan',
    ];

    public function prestasi(): BelongsTo
    {
        return $this->belongsTo(Prestasi::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
