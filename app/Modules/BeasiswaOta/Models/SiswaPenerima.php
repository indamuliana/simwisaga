<?php

namespace App\Modules\BeasiswaOta\Models;

use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class SiswaPenerima extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'ota_siswa_penerimas';

    protected $fillable = [
        'siswa_id',
        'kelompok_id',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_id');
    }

    public function komponenBiayas(): HasMany
    {
        return $this->hasMany(KomponenBiaya::class, 'siswa_penerima_id');
    }

    public function alokasis(): HasMany
    {
        return $this->hasMany(Alokasi::class, 'siswa_id', 'siswa_id');
    }

    public function getTotalKebutuhanSemesterAttribute(): float
    {
        return (float) $this->komponenBiayas()->sum('total_target_semester');
    }
}
