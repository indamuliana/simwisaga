<?php

namespace App\Modules\Kepegawaian\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JabatanPejabat extends Model
{
    protected $table = 'jabatan_pejabat';

    protected $fillable = [
        'jabatan_id',
        'pegawai_id',
        'nama_pejabat',
        'keterangan',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function getNamaTampilAttribute(): string
    {
        if ($this->pegawai) {
            return $this->pegawai->nama_bergelar ?: $this->pegawai->nama_lengkap;
        }

        return $this->nama_pejabat ?: '-';
    }

    public function isInternal(): bool
    {
        return !is_null($this->pegawai_id);
    }
}
