<?php

namespace App\Modules\Kepesantrenan\Models;

use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesantrenHalaqohPresensiDetail extends Model
{
    protected $table = 'pesantren_halaqoh_presensi_details';

    protected $fillable = [
        'presensi_id',
        'siswa_id',
        'status',
        'catatan',
    ];

    public function presensi(): BelongsTo
    {
        return $this->belongsTo(PesantrenHalaqohPresensi::class, 'presensi_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
