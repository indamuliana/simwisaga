<?php

namespace App\Modules\BeasiswaOta\Models;

use App\Models\User;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
                $model->nomor_kwitansi = static::generateNomorKwitansi(
                    $model->tanggal_alokasi ? (string) $model->tanggal_alokasi : null
                );
            }
        });
    }

    public static function generateNomorKwitansi(?string $tanggalAlokasi = null): string
    {
        $date = $tanggalAlokasi ? Carbon::parse($tanggalAlokasi) : now();
        $datePrefix = $date->format('Ym');
        $prefix = "OTA-KW-{$datePrefix}-";

        $query = static::where('nomor_kwitansi', 'like', "{$prefix}%");
        if (DB::transactionLevel() > 0) {
            $query->lockForUpdate();
        }

        $existingNumbers = $query->pluck('nomor_kwitansi');

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
            $exists = static::where('nomor_kwitansi', $candidate)->exists();
            if ($exists) {
                $nextNum++;
            }
        } while ($exists);

        return $candidate;
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
