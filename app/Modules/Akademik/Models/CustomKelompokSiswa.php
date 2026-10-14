<?php

namespace App\Modules\Akademik\Models;

use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomKelompokSiswa extends Model
{
    protected $table = 'custom_kelompok_siswas';

    protected $fillable = [
        'custom_kelompok_id',
        'siswa_id',
        'keterangan',
    ];

    public function customKelompok(): BelongsTo
    {
        return $this->belongsTo(CustomKelompok::class, 'custom_kelompok_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
