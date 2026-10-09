<?php

namespace App\Modules\Penilaian\Models;

use Illuminate\Database\Eloquent\Model;

class TujuanPembelajaran extends Model
{
    protected $fillable = [
        'penugasan_mengajar_id',
        'kode',
        'deskripsi',
        'is_active',
    ];

    public function penugasanMengajar()
    {
        return $this->belongsTo('App\Modules\Akademik\Models\PenugasanMengajar');
    }

    public function nilaiFormatifs()
    {
        return $this->hasMany(NilaiFormatif::class);
    }
}
