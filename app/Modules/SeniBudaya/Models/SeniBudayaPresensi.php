<?php

namespace App\Modules\SeniBudaya\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Akademik\Models\Semester;
use App\Models\User;

class SeniBudayaPresensi extends Model
{
    protected $fillable = [
        'seni_budaya_id',
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
        'pertemuan_ke' => 'integer',
        'tanggal' => 'date',
    ];

    public function seniBudaya()
    {
        return $this->belongsTo(SeniBudaya::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasMany(SeniBudayaPresensiDetail::class, 'seni_budaya_presensi_id');
    }
}
