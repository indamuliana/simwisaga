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
        'predikat',
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

    public function getRecordFor9M(string $period): ?array
    {
        $key = strtoupper(trim($period));
        if (str_contains($key, '9M1') || str_contains($key, 'STS 1')) $key = '9M1';
        elseif (str_contains($key, '9M2') || str_contains($key, 'SAS')) $key = '9M2';
        elseif (str_contains($key, '9M3') || str_contains($key, 'STS 2')) $key = '9M3';
        elseif (str_contains($key, '9M4') || str_contains($key, 'SAT')) $key = '9M4';

        $data = $this->data_9m ?? [];
        return $data[$key] ?? null;
    }
}
