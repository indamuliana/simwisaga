<?php

namespace App\Modules\Kepesantrenan\Models;

use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesantrenHafalanSiswa extends Model
{
    protected $table = 'pesantren_hafalan_siswas';

    protected $fillable = [
        'siswa_id',
        'surah_id',
        'ayat_number',
        'tanggal_hafal',
        'status',
        'predikat',
        'verified_by_id',
    ];

    public static function getPredikatStyle(?string $predikat): array
    {
        return match ($predikat) {
            'jayyid_jiddan' => [
                'label' => 'Jayyid Jiddan (Lancar Sekali)',
                'box_style' => 'background: #2563eb; color: #ffffff; border-color: #1d4ed8;',
                'badge_class' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-300 dark:border-blue-800',
                'color' => '#2563eb',
            ],
            'jayyid' => [
                'label' => 'Jayyid (Lancar / Baik)',
                'box_style' => 'background: #eab308; color: #0f172a; border-color: #ca8a04; font-weight: 800;',
                'badge_class' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300 dark:border-amber-800',
                'color' => '#eab308',
            ],
            'maqbul' => [
                'label' => 'Maqbul (Cukup / Mengulang)',
                'box_style' => 'background: #f97316; color: #ffffff; border-color: #c2410c;',
                'badge_class' => 'bg-orange-100 text-orange-800 dark:bg-orange-950/60 dark:text-orange-300 border border-orange-300 dark:border-orange-800',
                'color' => '#f97316',
            ],
            default => [
                'label' => 'Mumtaz (Sangat Lancar)',
                'box_style' => 'background: #10b981; color: #ffffff; border-color: #059669;',
                'badge_class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800',
                'color' => '#10b981',
            ],
        };
    }

    protected $casts = [
        'tanggal_hafal' => 'date',
        'ayat_number' => 'integer',
        'surah_id' => 'integer',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function surah(): BelongsTo
    {
        return $this->belongsTo(QuranSurah::class, 'surah_id', 'nomor_surah');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'verified_by_id');
    }
}
