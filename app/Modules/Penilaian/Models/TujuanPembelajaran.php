<?php

namespace App\Modules\Penilaian\Models;

use Illuminate\Database\Eloquent\Model;

class TujuanPembelajaran extends Model
{
    protected $fillable = [
        'penugasan_mengajar_id',
        'kode',
        'deskripsi',
        'alokasi_ah',
        'is_active',
    ];

    public function penugasanMengajar()
    {
        return $this->belongsTo(\App\Modules\Kepegawaian\Models\PenugasanMengajar::class);
    }

    public function nilaiFormatifs()
    {
        return $this->hasMany(NilaiFormatif::class);
    }
}
