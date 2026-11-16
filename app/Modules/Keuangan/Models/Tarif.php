<?php

namespace App\Modules\Keuangan\Models;

use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Program;
use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Tarif extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'keuangan_tarifs';

    protected $fillable = [
        'pos_biaya_id',
        'tahun_ajaran_id',
        'tingkat',
        'program_id',
        'jurusan_id',
        'jalur_masuk',
        'nominal',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];

    public function posBiaya(): BelongsTo
    {
        return $this->belongsTo(PosBiaya::class, 'pos_biaya_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }
}

