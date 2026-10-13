<?php

namespace App\Modules\SeniBudaya\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Models\User;

class SeniBudaya extends Model
{
    protected $fillable = [
        'kode',
        'nama',
        'guru_id',
        'instruktur_nama',
        'kkm',
        'bobot_presensi',
        'bobot_penilaian',
        'hari_latihan',
        'jam_mulai',
        'jam_selesai',
        'tempat',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'kkm' => 'integer',
        'bobot_presensi' => 'integer',
        'bobot_penilaian' => 'integer',
        'is_active' => 'boolean',
    ];

    public function guru()
    {
        return $this->belongsTo(Pegawai::class, 'guru_id');
    }

    public function tps()
    {
        return $this->hasMany(SeniBudayaTp::class);
    }

    public function anggotas()
    {
        return $this->hasMany(SeniBudayaAnggota::class);
    }

    public function presensis()
    {
        return $this->hasMany(SeniBudayaPresensi::class);
    }

    public function getGuruDisplayNameAttribute(): string
    {
        if ($this->guru) {
            return $this->guru->nama_lengkap;
        }

        if (!empty($this->instruktur_nama)) {
            return $this->instruktur_nama;
        }

        return 'Belum Ditentukan';
    }
}
