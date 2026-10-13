<?php

namespace App\Modules\Ekstrakurikuler\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Models\User;

class Ekstrakurikuler extends Model
{
    protected $fillable = [
        'nama',
        'pembina_id',
        'instruktur_user_id',
        'instruktur_nama',
        'is_active',
        'is_wajib',
        'hari_latihan',
        'jam_mulai',
        'jam_selesai',
        'tempat',
        'bobot_presensi',
        'bobot_penilaian',
        'threshold_a',
        'threshold_b',
        'threshold_c',
        'deskripsi',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_wajib' => 'boolean',
        'bobot_presensi' => 'integer',
        'bobot_penilaian' => 'integer',
        'threshold_a' => 'integer',
        'threshold_b' => 'integer',
        'threshold_c' => 'integer',
    ];

    public function pembina()
    {
        return $this->belongsTo(Pegawai::class, 'pembina_id');
    }

    public function instrukturUser()
    {
        return $this->belongsTo(User::class, 'instruktur_user_id');
    }

    public function anggota()
    {
        return $this->hasMany(EkstrakurikulerAnggota::class);
    }

    public function presensis()
    {
        return $this->hasMany(EkskulPresensi::class);
    }

    public function getInstrukturDisplayNameAttribute(): string
    {
        if ($this->instrukturUser) {
            return $this->instrukturUser->name;
        }

        if ($this->pembina) {
            return $this->pembina->nama_lengkap;
        }

        if (!empty($this->instruktur_nama)) {
            return $this->instruktur_nama;
        }

        return 'Belum ditentukan';
    }

    public function userCanManage(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->isSuperAdmin() || $user->hasRole(['Kesiswaan', 'Kurikulum', 'Operator'])) {
            return true;
        }

        if ($this->instruktur_user_id && $this->instruktur_user_id == $user->id) {
            return true;
        }

        if ($this->pembina && $this->pembina->user_id == $user->id) {
            return true;
        }

        return false;
    }
}
