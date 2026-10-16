<?php

namespace App\Modules\Ekstrakurikuler\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Akademik\Models\Semester;

class EkstrakurikulerAnggota extends Model
{
    protected $fillable = [
        'ekstrakurikuler_id',
        'siswa_id',
        'semester_id',
        'sts_1',
        'sas',
        'sts_2',
        'sat',
        'nilai_presensi',
        'total_hadir',
        'total_pertemuan',
        'persentase_kehadiran',
        'status_keanggotaan',
        'catatan_pindah',
        'nilai_angka',
        'nilai_huruf',
        'deskripsi',
    ];

    protected $casts = [
        'sts_1' => 'float',
        'sas' => 'float',
        'sts_2' => 'float',
        'sat' => 'float',
        'nilai_presensi' => 'float',
        'nilai_angka' => 'float',
        'total_hadir' => 'integer',
        'total_pertemuan' => 'integer',
        'persentase_kehadiran' => 'float',
    ];

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

    public function presensiDetails()
    {
        return $this->hasMany(EkskulPresensiDetail::class, 'siswa_id', 'siswa_id');
    }

    public function getPredikatAttribute(): ?string
    {
        return $this->nilai_huruf;
    }

    public function getKeteranganAttribute(): ?string
    {
        return $this->deskripsi;
    }
}
