<?php

namespace App\Modules\Penilaian\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiFormatif extends Model
{
    protected $fillable = [
        'siswa_id',
        'tujuan_pembelajaran_id',
        'nilai',
    ];

    public function siswa()
    {
        return $this->belongsTo('App\Modules\Akademik\Models\Siswa');
    }

    public function tujuanPembelajaran()
    {
        return $this->belongsTo(TujuanPembelajaran::class);
    }
}
