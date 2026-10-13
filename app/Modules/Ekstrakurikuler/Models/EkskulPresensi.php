<?php

namespace App\Modules\Ekstrakurikuler\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Akademik\Models\Semester;
use App\Models\User;

class EkskulPresensi extends Model
{
    protected $table = 'ekskul_presensis';

    protected $fillable = [
        'ekstrakurikuler_id',
        'semester_id',
        'pertemuan_ke',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'materi',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'pertemuan_ke' => 'integer',
    ];

    public function ekstrakurikuler()
    {
        return $this->belongsTo(Ekstrakurikuler::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasMany(EkskulPresensiDetail::class, 'ekskul_presensi_id');
    }
}
