<?php

namespace App\Modules\BeasiswaOta\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
                $model->nomor_transaksi = static::generateNomorTransaksi(
                    $model->tanggal_donasi ? (string) $model->tanggal_donasi : null
                );
            }
        });
    }

    public static function generateNomorTransaksi(?string $tanggalDonasi = null): string
    {
        $date = $tanggalDonasi ? Carbon::parse($tanggalDonasi) : now();
        $datePrefix = $date->format('Ym');
        $prefix = "OTA-IN-{$datePrefix}-";

        $query = static::where('nomor_transaksi', 'like', "{$prefix}%");
        if (DB::transactionLevel() > 0) {
            $query->lockForUpdate();
        }

        $existingNumbers = $query->pluck('nomor_transaksi');

        $maxNum = 0;
        foreach ($existingNumbers as $no) {
            $parts = explode('-', (string) $no);
            $val = (int) end($parts);
            if ($val > $maxNum) {
                $maxNum = $val;
            }
        }

        $nextNum = $maxNum + 1;

        do {
            $candidate = sprintf('%s%04d', $prefix, $nextNum);
            $exists = static::where('nomor_transaksi', $candidate)->exists();
            if ($exists) {
                $nextNum++;
            }
        } while ($exists);

        return $candidate;
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
