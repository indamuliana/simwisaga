<?php

namespace App\Modules\Rapor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Sistem\Models\PeriodeRapor;

class CatatanWaliKelas extends Model
{
    protected $table = 'catatan_wali_kelas';

    protected $fillable = [
        'siswa_id',
        'semester_id',
        'periode_rapor_id',
        'catatan',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function periodeRapor(): BelongsTo
    {
        return $this->belongsTo(PeriodeRapor::class, 'periode_rapor_id');
    }
}
