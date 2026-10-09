<?php

namespace App\Modules\Kesiswaan\Models;

use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class RombelSiswa extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'rombel_siswa';

    protected $fillable = [
        'tahun_ajaran_id',
        'siswa_id',
        'rombel_id',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }
}
