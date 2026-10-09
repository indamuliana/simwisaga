<?php

namespace App\Modules\Kepegawaian\Models;

use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class PenugasanWaliKelas extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'penugasan_wali_kelas';

    protected $fillable = [
        'tahun_ajaran_id',
        'rombel_id',
        'pegawai_id',
        'sk_penugasan',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
