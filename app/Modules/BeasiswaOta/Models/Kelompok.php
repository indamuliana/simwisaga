<?php

namespace App\Modules\BeasiswaOta\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable;

class Kelompok extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'ota_kelompoks';

    protected $fillable = [
        'kode_kelompok',
        'nama_kelompok',
        'koordinator_donatur_id',
        'koordinator_nama',
        'koordinator_telepon',
        'target_nominal',
        'is_aktif',
        'keterangan',
    ];

    protected $casts = [
        'target_nominal' => 'decimal:2',
        'is_aktif' => 'boolean',
    ];

    public function koordinator()
    {
        return $this->belongsTo(Donatur::class, 'koordinator_donatur_id');
    }

    public function donaturs(): BelongsToMany
    {
        return $this->belongsToMany(Donatur::class, 'ota_kelompok_anggotas', 'kelompok_id', 'donatur_id')
            ->withPivot('id', 'nominal_komitmen', 'keterangan')
            ->withTimestamps();
    }

    public function anggotaKelompok(): HasMany
    {
        return $this->hasMany(KelompokAnggota::class, 'kelompok_id');
    }

    public function siswaPenerima(): HasOne
    {
        return $this->hasOne(SiswaPenerima::class, 'kelompok_id')->where('status', 'aktif');
    }

    public function donasiMasuks(): HasMany
    {
        return $this->hasMany(DonasiMasuk::class, 'kelompok_id');
    }

    public function alokasis(): HasMany
    {
        return $this->hasMany(Alokasi::class, 'kelompok_id');
    }

    public function kasMutasis(): HasMany
    {
        return $this->hasMany(KasMutasi::class, 'kelompok_id');
    }

    public function getTotalKomitmenAttribute(): float
    {
        return (float) $this->anggotaKelompok()->sum('nominal_komitmen');
    }

    public function getSaldoKasAttribute(): float
    {
        $masuk = (float) $this->kasMutasis()->where('tipe', 'masuk')->sum('nominal');
        $keluar = (float) $this->kasMutasis()->where('tipe', 'keluar')->sum('nominal');
        return $masuk - $keluar;
    }

    public function getTotalTerkumpulAttribute(): float
    {
        return (float) $this->donasiMasuks()->sum('nominal');
    }
}
