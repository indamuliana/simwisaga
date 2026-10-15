<?php

namespace App\Modules\BeasiswaOta\Models;

use App\Models\User;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Alokasi extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'ota_alokasis';

    protected $fillable = [
        'nomor_kwitansi',
        'tipe_alokasi',
        'kelompok_id',
        'siswa_id',
        'semester_id',
        'tahun_ajaran_id',
        'tanggal_alokasi',
        'total_nominal',
        'surplus_dialihkan',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_alokasi' => 'date',
        'total_nominal' => 'decimal:2',
        'surplus_dialihkan' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_kwitansi)) {
                $datePrefix = date('Ym');
                $count = static::whereYear('tanggal_alokasi', date('Y'))
                    ->whereMonth('tanggal_alokasi', date('m'))
                    ->count() + 1;
                $model->nomor_kwitansi = sprintf('OTA-KW-%s-%04d', $datePrefix, $count);
            }
        });
    }

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(AlokasiDetail::class, 'alokasi_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
