<?php

namespace App\Modules\SeniBudaya\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Kesiswaan\Models\Siswa;

class SeniBudayaPresensiDetail extends Model
{
    protected $fillable = [
        'seni_budaya_presensi_id',
        'siswa_id',
        'status',
        'catatan',
    ];

    public function presensi()
    {
        return $this->belongsTo(SeniBudayaPresensi::class, 'seni_budaya_presensi_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
