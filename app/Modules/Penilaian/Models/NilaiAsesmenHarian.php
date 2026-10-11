<?php

namespace App\Modules\Penilaian\Models;

use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;

class NilaiAsesmenHarian extends Model
{
    protected $table = 'nilai_asesmen_harians';

    protected $fillable = [
        'siswa_id',
        'penugasan_mengajar_id',
        'kode_ah',
        'nilai',
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
