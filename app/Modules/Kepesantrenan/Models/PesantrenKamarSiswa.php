<?php

namespace App\Modules\Kepesantrenan\Models;

use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesantrenKamarSiswa extends Model
{
    protected $table = 'pesantren_kamar_siswas';

    protected $fillable = [
        'kamar_id',
        'siswa_id',
        'peran',
        'nomor_ranjang',
    ];

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(PesantrenKamar::class, 'kamar_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
