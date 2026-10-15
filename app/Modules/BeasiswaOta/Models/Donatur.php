<?php

namespace App\Modules\BeasiswaOta\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable;

class Donatur extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'ota_donaturs';

    protected $fillable = [
        'nama',
        'tipe',
        'nomor_telepon',
        'email',
        'alamat',
        'is_aktif',
        'keterangan',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function kelompoks(): BelongsToMany
    {
        return $this->belongsToMany(Kelompok::class, 'ota_kelompok_anggotas', 'donatur_id', 'kelompok_id')
            ->withPivot('id', 'nominal_komitmen', 'keterangan')
            ->withTimestamps();
    }

    public function kelompokAnggotas(): HasMany
    {
        return $this->hasMany(KelompokAnggota::class, 'donatur_id');
    }

    public function donasiMasuks(): HasMany
    {
        return $this->hasMany(DonasiMasuk::class, 'donatur_id')->orderByDesc('tanggal_donasi');
    }

    public function getTotalDonasiAttribute(): float
    {
        return (float) $this->donasiMasuks()->sum('nominal');
    }
}
