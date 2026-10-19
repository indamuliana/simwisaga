<?php

namespace App\Modules\KalenderPendidikan\Models;

use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class KalenderLibur extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'kalender_liburs';

    protected $fillable = [
        'tahun_ajaran_id',
        'nama',
        'tgl_mulai',
        'tgl_selesai',
        'jenis',
        'keterangan',
        'is_kbm_libur',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'is_kbm_libur' => 'boolean',
    ];

    public const JENIS_NASIONAL = 'nasional';
    public const JENIS_CUTI_BERSAMA = 'cuti_bersama';
    public const JENIS_SEKOLAH = 'sekolah';

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
