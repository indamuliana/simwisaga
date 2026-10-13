<?php

namespace App\Modules\Penilaian\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiAkhir extends Model
{
    protected $fillable = [
        'siswa_id',
        'penugasan_mengajar_id',
        'rata_formatif',
        'sts',
        'sas',
        'nilai_akhir',
        'deskripsi_tertinggi',
        'deskripsi_terendah',
        'deskripsi_manual',
        'data_9m',
    ];

    protected $casts = [
        'rata_formatif' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
        'sts' => 'integer',
        'sas' => 'integer',
        'data_9m' => 'array',
    ];

    public function siswa()
    {
        return $this->belongsTo(\App\Modules\Kesiswaan\Models\Siswa::class);
    }

    public function penugasanMengajar()
    {
        return $this->belongsTo(\App\Modules\Kepegawaian\Models\PenugasanMengajar::class);
    }

    /**
     * Normalisasi string sebutan / kode periode ke key 9M (9M1, 9M2, 9M3, 9M4)
     */
    public static function normalizePeriodKey(string $period): string
    {
        $clean = strtoupper(trim($period));

        if (str_contains($clean, '9M1') || str_contains($clean, 'STS 1') || str_contains($clean, 'PTS 1') || str_contains($clean, 'MINGGU KE-1')) {
            return '9M1';
        }
        if (str_contains($clean, '9M2') || str_contains($clean, 'SAS') || str_contains($clean, 'PAS 1') || str_contains($clean, 'MINGGU KE-2')) {
            return '9M2';
        }
        if (str_contains($clean, '9M3') || str_contains($clean, 'STS 2') || str_contains($clean, 'PTS 2') || str_contains($clean, 'MINGGU KE-3')) {
            return '9M3';
        }
        if (str_contains($clean, '9M4') || str_contains($clean, 'SAT') || str_contains($clean, 'PAT') || str_contains($clean, 'PAS 2') || str_contains($clean, 'MINGGU KE-4')) {
            return '9M4';
        }

        return $clean;
    }

    /**
     * Mengambil record data 9M untuk periode tertentu
     */
    public function getRecordFor9M(string $period): ?array
    {
        $key = self::normalizePeriodKey($period);
        $data = $this->data_9m ?? [];

        return $data[$key] ?? null;
    }

    /**
     * Mengambil nilai akhir angka untuk periode tertentu (9M1, 9M2, 9M3, 9M4)
     */
    public function getScoreForPeriod(string $period): ?float
    {
        $rec = $this->getRecordFor9M($period);
        if ($rec && isset($rec['nilai_akhir']) && $rec['nilai_akhir'] !== null) {
            return (float) $rec['nilai_akhir'];
        }

        return $this->nilai_akhir !== null ? (float) $this->nilai_akhir : null;
    }

    /**
     * Mengambil evaluasi deskripsi untuk periode tertentu (9M1, 9M2, 9M3, 9M4)
     */
    public function getDescriptionForPeriod(string $period): array
    {
        $rec = $this->getRecordFor9M($period);
        if ($rec) {
            return [
                'deskripsi_tertinggi' => $rec['deskripsi_tertinggi'] ?? null,
                'deskripsi_terendah' => $rec['deskripsi_terendah'] ?? null,
                'deskripsi_manual' => $rec['deskripsi_manual'] ?? null,
            ];
        }

        return [
            'deskripsi_tertinggi' => $this->deskripsi_tertinggi,
            'deskripsi_terendah' => $this->deskripsi_terendah,
            'deskripsi_manual' => $this->deskripsi_manual,
        ];
    }
}
