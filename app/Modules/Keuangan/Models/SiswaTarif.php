<?php

namespace App\Modules\Keuangan\Models;

use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class SiswaTarif extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'keuangan_siswa_tarifs';

    protected $fillable = [
        'siswa_id',
        'pos_biaya_id',
        'tahun_ajaran_id',
        'nominal_kustom',
        'jenis_potongan',
        'persentase_potongan',
        'alasan',
    ];

    protected $casts = [
        'nominal_kustom' => 'decimal:2',
        'persentase_potongan' => 'decimal:2',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function posBiaya(): BelongsTo
    {
        return $this->belongsTo(PosBiaya::class, 'pos_biaya_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
}

