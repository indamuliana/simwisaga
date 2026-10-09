<?php

namespace App\Modules\Ekstrakurikuler\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Akademik\Models\Semester;

class EkstrakurikulerAnggota extends Model
{
    protected $fillable = ['ekstrakurikuler_id', 'siswa_id', 'semester_id', 'nilai_angka', 'nilai_huruf', 'deskripsi'];

    public function ekstrakurikuler()
    {
        return $this->belongsTo(Ekstrakurikuler::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
