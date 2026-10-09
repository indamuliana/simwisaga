<?php

namespace App\Modules\Kesiswaan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class SiswaOrangTua extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'siswa_orang_tuas';

    protected $fillable = [
        'siswa_id',
        'hubungan',
        'nama',
        'tahun_lahir',
        'jenjang_pendidikan',
        'pekerjaan',
        'penghasilan',
        'nik',
        'no_hp',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
