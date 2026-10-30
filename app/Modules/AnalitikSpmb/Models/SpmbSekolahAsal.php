<?php

namespace App\Modules\AnalitikSpmb\Models;

use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class SpmbSekolahAsal extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'nama_resmi',
        'npsn',
        'jenis',
        'status_sekolah',
        'kecamatan',
        'kabupaten_kota',
        'tier',
        'catatan',
    ];

    public function aliases(): HasMany
    {
        return $this->hasMany(SpmbSekolahAsalAlias::class, 'sekolah_asal_id');
    }

    /**
     * Mengambil seluruh variasi nama string (nama resmi + semua alias)
     */
    public function getAllNamesAttribute(): array
    {
        return array_unique(array_merge(
            [$this->nama_resmi],
            $this->aliases->pluck('alias_raw')->toArray()
        ));
    }
}
