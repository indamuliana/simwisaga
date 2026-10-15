<?php

namespace App\Modules\Akademik\Models;

use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class MapelPilihanAnggota extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'mapel_pilihan_id',
        'siswa_id',
        'tahun_ajaran_id',
        'status',
    ];

    public function mapelPilihan(): BelongsTo
    {
        return $this->belongsTo(MapelPilihan::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
