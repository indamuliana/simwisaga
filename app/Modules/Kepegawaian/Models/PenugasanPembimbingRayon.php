<?php

namespace App\Modules\Kepegawaian\Models;

use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class PenugasanPembimbingRayon extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'penugasan_pembimbing_rayons';

    protected $fillable = [
        'tahun_ajaran_id',
        'rayon_id',
        'pegawai_id',
        'sk_penugasan',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function rayon(): BelongsTo
    {
        return $this->belongsTo(Rayon::class);
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
