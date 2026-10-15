<?php

namespace App\Modules\BeasiswaOta\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class DonasiMasuk extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'ota_donasi_masuks';

    protected $fillable = [
        'nomor_transaksi',
        'batch_kode',
        'penyetor_nama',
        'is_kolektif',
        'donatur_id',
        'kelompok_id',
        'tahun_ajaran_id',
        'tipe_donatur',
        'tanggal_donasi',
        'nominal',
        'periode_bulan',
        'jumlah_bulan',
        'keterangan_periode',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_donasi' => 'date',
        'nominal' => 'decimal:2',
        'is_kolektif' => 'boolean',
        'periode_bulan' => 'array',
        'jumlah_bulan' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_transaksi)) {
                $datePrefix = date('Ym');
                $count = static::whereYear('tanggal_donasi', date('Y'))
                    ->whereMonth('tanggal_donasi', date('m'))
                    ->count() + 1;
                $model->nomor_transaksi = sprintf('OTA-IN-%s-%04d', $datePrefix, $count);
            }
        });
    }

    public function donatur(): BelongsTo
    {
        return $this->belongsTo(Donatur::class, 'donatur_id');
    }

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Akademik\Models\TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
