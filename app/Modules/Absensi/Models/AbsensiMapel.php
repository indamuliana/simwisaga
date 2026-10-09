<?php

namespace App\Modules\Absensi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\StatusAbsensi;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Models\User;

class AbsensiMapel extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date',
        'status' => StatusAbsensi::class,
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function penugasanMengajar()
    {
        return $this->belongsTo(PenugasanMengajar::class);
    }

    public function penginput()
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }
}
