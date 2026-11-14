<?php

namespace App\Modules\Akademik\Models;

use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class PenugasanMengajar extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'penugasan_mengajars';

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

    public function tujuanPembelajarans(): HasMany
    {
        return $this->hasMany(TujuanPembelajaran::class, 'penugasan_mengajar_id');
    }

    public function nilaiAkhirs(): HasMany
    {
        return $this->hasMany(NilaiAkhir::class, 'penugasan_mengajar_id');
    }
}
