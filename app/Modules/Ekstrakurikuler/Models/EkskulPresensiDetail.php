<?php

namespace App\Modules\Ekstrakurikuler\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Kesiswaan\Models\Siswa;

class EkskulPresensiDetail extends Model
{
    protected $table = 'ekskul_presensi_details';

    protected $fillable = [
        'ekskul_presensi_id',
        'siswa_id',
        'status',
        'catatan',
    ];

    public function presensi()
    {
        return $this->belongsTo(EkskulPresensi::class, 'ekskul_presensi_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
