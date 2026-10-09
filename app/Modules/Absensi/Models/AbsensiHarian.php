<?php

namespace App\Modules\Absensi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\StatusAbsensi;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Models\User;

class AbsensiHarian extends Model
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

    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function penginput()
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }
}
