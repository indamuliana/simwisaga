<?php

namespace App\Modules\Kepegawaian\Models;

use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class PenugasanMengajar extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'semester_id',
        'pegawai_id',
        'mapel_id',
        'rombel_id',
        'jp',
        'keterangan',
    ];

    protected $casts = [
        'jp' => 'integer',
    ];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class);
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }

    public function tujuanPembelajarans()
    {
        return $this->hasMany(\App\Modules\Penilaian\Models\TujuanPembelajaran::class, 'penugasan_mengajar_id');
    }

    public function nilaiAkhirs()
    {
        return $this->hasMany(\App\Modules\Penilaian\Models\NilaiAkhir::class, 'penugasan_mengajar_id');
    }
}
