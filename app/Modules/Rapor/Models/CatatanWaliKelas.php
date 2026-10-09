<?php

namespace App\Modules\Rapor\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Akademik\Models\Semester;

class CatatanWaliKelas extends Model
{
    protected $fillable = ['siswa_id', 'semester_id', 'catatan'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
