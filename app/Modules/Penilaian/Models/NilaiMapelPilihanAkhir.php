<?php

namespace App\Modules\Penilaian\Models;

use App\Modules\Akademik\Models\MapelPilihan;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class NilaiMapelPilihanAkhir extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'nilai_mapil_akhirs';

    protected $fillable = [
        'mapel_pilihan_id',
        'siswa_id',
        'semester_id',
        'nilai_akhir',
        'data_9m',
        'deskripsi_tertinggi',
        'deskripsi_terendah',
        'deskripsi_manual',
        'status',
    ];

    protected $casts = [
        'nilai_akhir' => 'decimal:2',
        'data_9m' => 'array',
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
