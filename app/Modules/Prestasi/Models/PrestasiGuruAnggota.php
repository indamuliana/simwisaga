<?php

namespace App\Modules\Prestasi\Models;

use App\Modules\Kepegawaian\Models\Pegawai;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrestasiGuruAnggota extends Model
{
    protected $table = 'prestasi_guru_anggotas';

    protected $fillable = [
        'prestasi_guru_id',
        'pegawai_id',
        'peran',
        'keterangan',
    ];

    public function prestasiGuru(): BelongsTo
    {
        return $this->belongsTo(PrestasiGuru::class);
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
