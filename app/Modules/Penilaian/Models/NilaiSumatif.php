<?php

namespace App\Modules\Penilaian\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiSumatif extends Model
{
    protected $fillable = [
        'siswa_id',
        'penugasan_mengajar_id',
        'sts',
        'sas',
    ];

    public function siswa()
    {
        return $this->belongsTo(\App\Modules\Kesiswaan\Models\Siswa::class);
    }

    public function penugasanMengajar()
    {
        return $this->belongsTo(\App\Modules\Kepegawaian\Models\PenugasanMengajar::class);
    }
}
