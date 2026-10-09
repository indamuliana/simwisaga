<?php

namespace App\Modules\Ekstrakurikuler\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Kepegawaian\Models\Pegawai;

class Ekstrakurikuler extends Model
{
    protected $fillable = ['nama', 'pembina_id'];

    public function pembina()
    {
        return $this->belongsTo(Pegawai::class, 'pembina_id');
    }

    public function anggota()
    {
        return $this->hasMany(EkstrakurikulerAnggota::class);
    }
}
