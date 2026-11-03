<?php

namespace App\Modules\Kepesantrenan\Models;

use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesantrenSetoranHafalan extends Model
{
    protected $table = 'pesantren_setoran_hafalans';

    protected $fillable = [
        'siswa_id',
        'pegawai_id',
        'tanggal',
        'jenis',
        'surah_id',
        'ayat_awal',
        'ayat_akhir',
        'total_ayat',
        'predikat',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'surah_id' => 'integer',
        'ayat_awal' => 'integer',
        'ayat_akhir' => 'integer',
        'total_ayat' => 'integer',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function penguji(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function surah(): BelongsTo
    {
        return $this->belongsTo(QuranSurah::class, 'surah_id', 'nomor_surah');
    }

    public function getPredikatLabelAttribute(): string
    {
        return match ($this->predikat) {
            'mumtaz' => 'Mumtaz (Sangat Lancar)',
            'jayyid_jiddan' => 'Jayyid Jiddan (Lancar Sekali)',
            'jayyid' => 'Jayyid (Lancar / Baik)',
            'maqbul' => 'Maqbul (Cukup / Mengulang)',
            default => ucfirst((string) $this->predikat),
        };
    }

    public function getPredikatBadgeClassAttribute(): string
    {
        return match ($this->predikat) {
            'jayyid_jiddan' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-300 dark:border-blue-800',
            'jayyid' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300 dark:border-amber-800',
            'maqbul' => 'bg-orange-100 text-orange-800 dark:bg-orange-950/60 dark:text-orange-300 border border-orange-300 dark:border-orange-800',
            default => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800',
        };
    }
}
