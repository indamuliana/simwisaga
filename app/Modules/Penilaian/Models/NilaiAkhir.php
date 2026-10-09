<?php

namespace App\Modules\Penilaian\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiAkhir extends Model
{
    protected $fillable = [
        'siswa_id',
        'penugasan_mengajar_id',
        'rata_formatif',
        'sts',
        'sas',
        'nilai_akhir',
        'deskripsi_tertinggi',
        'deskripsi_terendah',
        'deskripsi_manual',
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
