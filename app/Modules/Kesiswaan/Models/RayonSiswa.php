<?php

namespace App\Modules\Kesiswaan\Models;

use App\Modules\Akademik\Models\Rayon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class RayonSiswa extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'rayon_siswa';

    protected $fillable = [
        'siswa_id',
        'rayon_id',
        'tgl_mulai',
        'tgl_selesai',
        'alasan_pindah',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function rayon(): BelongsTo
    {
        return $this->belongsTo(Rayon::class);
    }
}
