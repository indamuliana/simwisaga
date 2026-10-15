<?php

namespace App\Modules\Penilaian\Models;

use App\Modules\Akademik\Models\MapelPilihan;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class NilaiMapelPilihanSumatif extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'nilai_mapil_sumatifs';

    protected $fillable = [
        'mapel_pilihan_id',
        'siswa_id',
        'semester_id',
        'sts_1',
        'sas',
        'sts_2',
        'sat',
    ];

    protected $casts = [
        'sts_1' => 'decimal:2',
        'sas' => 'decimal:2',
        'sts_2' => 'decimal:2',
        'sat' => 'decimal:2',
    ];

    public function mapelPilihan(): BelongsTo
    {
        return $this->belongsTo(MapelPilihan::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }
}
