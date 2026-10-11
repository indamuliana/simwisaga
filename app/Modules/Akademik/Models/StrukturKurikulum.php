<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class StrukturKurikulum extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'tahun_ajaran_id',
        'jurusan_id',
        'tingkat',
        'mapel_id',
        'jp_per_minggu',
        'jp_ganjil',
        'jp_genap',
        'urutan',
    ];

    protected $casts = [
        'jp_per_minggu' => 'integer',
        'jp_ganjil' => 'integer',
        'jp_genap' => 'integer',
        'urutan' => 'integer',
    ];

    public function getSemesterStatusAttribute(): string
    {
        $ganjil = (int) $this->jp_ganjil;
        $genap = (int) $this->jp_genap;

        if ($ganjil > 0 && $genap > 0) {
            return 'Sepanjang Tahun';
        }
        if ($ganjil > 0) {
            return 'Ganjil Saja';
        }
        if ($genap > 0) {
            return 'Genap Saja';
        }

        return 'Nonaktif';
    }

    public function getJpForSemester(?string $semester): int
    {
        if ($semester && str_contains(strtolower($semester), 'genap')) {
            return (int) $this->jp_genap;
        }

        return (int) $this->jp_ganjil;
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class);
    }
}
