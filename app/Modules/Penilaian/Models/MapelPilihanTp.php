<?php

namespace App\Modules\Penilaian\Models;

use App\Modules\Akademik\Models\MapelPilihan;
use App\Modules\Akademik\Models\Semester;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class MapelPilihanTp extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'mapel_pilihan_id',
        'semester_id',
        'kode_tp',
        'lingkup_materi',
        'deskripsi_tp',
        'kode_ah',
        'target_periode',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    public function mapelPilihan(): BelongsTo
    {
        return $this->belongsTo(MapelPilihan::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function getDeskripsiAttribute(): string
    {
        return $this->deskripsi_tp ?? '';
    }

    public function getAlokasiAhAttribute(): ?string
    {
        return $this->kode_ah;
    }
}
