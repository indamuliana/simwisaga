<?php

namespace App\Modules\Penilaian\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;

class NilaiHarian extends Model
{
    protected $table = 'nilai_harians';

    protected $fillable = [
        'siswa_id',
        'penugasan_mengajar_id',
        'target_periode',
        'nilai',
        'keterangan',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function penugasanMengajar()
    {
        return $this->belongsTo(PenugasanMengajar::class);
    }
}
