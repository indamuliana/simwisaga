<?php

namespace App\Modules\SeniBudaya\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Akademik\Models\Semester;

class SeniBudayaAnggota extends Model
{
    protected $fillable = [
        'seni_budaya_id',
        'siswa_id',
        'semester_id',
        'sts_1',
        'nilai_akhir_sts_1',
        'sas',
        'nilai_akhir_sas',
        'sts_2',
        'nilai_akhir_sts_2',
        'sat',
        'nilai_akhir_sat',
        'nilai_presensi',
        'total_hadir',
        'total_pertemuan',
        'persentase_kehadiran',
        'nilai_akhir',
        'nilai_huruf',
        'tp_tercapai_ids',
        'tp_perlu_bimbingan_ids',
        'tp_evaluations',
        'deskripsi_tertinggi',
        'deskripsi_terendah',
        'deskripsi_manual',
        'deskripsi_evaluations',
        'status_keanggotaan',
    ];

    protected $casts = [
        'sts_1' => 'decimal:2',
        'nilai_akhir_sts_1' => 'decimal:2',
        'sas' => 'decimal:2',
        'nilai_akhir_sas' => 'decimal:2',
        'sts_2' => 'decimal:2',
        'nilai_akhir_sts_2' => 'decimal:2',
        'sat' => 'decimal:2',
        'nilai_akhir_sat' => 'decimal:2',
        'nilai_presensi' => 'decimal:2',
        'total_hadir' => 'integer',
        'total_pertemuan' => 'integer',
        'persentase_kehadiran' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
        'tp_tercapai_ids' => 'array',
        'tp_perlu_bimbingan_ids' => 'array',
        'tp_evaluations' => 'array',
        'deskripsi_evaluations' => 'array',
    ];

    public function seniBudaya()
    {
        return $this->belongsTo(SeniBudaya::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Mengambil nilai angka untuk periode tertentu (STS 1, SAS, STS 2, SAT)
     */
    public function getScoreForPeriod(string $period): ?float
    {
        $clean = strtoupper(trim($period));

        return match ($clean) {
            'STS 1', 'PTS 1', '9M1' => $this->nilai_akhir_sts_1 !== null ? (float) $this->nilai_akhir_sts_1 : ($this->sts_1 !== null ? (float) $this->sts_1 : null),
            'SAS', 'PAS 1', '9M2' => $this->nilai_akhir_sas !== null ? (float) $this->nilai_akhir_sas : ($this->nilai_akhir !== null ? (float) $this->nilai_akhir : ($this->sas !== null ? (float) $this->sas : null)),
            'STS 2', 'PTS 2', '9M3' => $this->nilai_akhir_sts_2 !== null ? (float) $this->nilai_akhir_sts_2 : ($this->sts_2 !== null ? (float) $this->sts_2 : null),
            'SAT', 'PAT', 'PAS 2', '9M4' => $this->nilai_akhir_sat !== null ? (float) $this->nilai_akhir_sat : ($this->nilai_akhir !== null ? (float) $this->nilai_akhir : ($this->sat !== null ? (float) $this->sat : null)),
            default => $this->nilai_akhir !== null ? (float) $this->nilai_akhir : null,
        };
    }

    /**
     * Mengambil evaluasi deskripsi untuk periode tertentu (STS 1, SAS, STS 2, SAT)
     */
    public function getDescriptionForPeriod(string $period): array
    {
        $clean = strtoupper(trim($period));
        $normalizedPeriod = match ($clean) {
            'STS 1', 'PTS 1', '9M1' => 'STS 1',
            'SAS', 'PAS 1', '9M2' => 'SAS',
            'STS 2', 'PTS 2', '9M3' => 'STS 2',
            'SAT', 'PAT', 'PAS 2', '9M4' => 'SAT',
            default => 'SAS',
        };

        $evals = $this->deskripsi_evaluations ?? [];
        if (isset($evals[$normalizedPeriod]) && is_array($evals[$normalizedPeriod])) {
            $entry = $evals[$normalizedPeriod];
            return [
                'deskripsi_tertinggi' => $entry['deskripsi_tertinggi'] ?? null,
                'deskripsi_terendah' => $entry['deskripsi_terendah'] ?? null,
                'deskripsi_manual' => $entry['deskripsi_manual'] ?? null,
            ];
        }

        // Fallback ke deskripsi umum
        return [
            'deskripsi_tertinggi' => $this->deskripsi_tertinggi,
            'deskripsi_terendah' => $this->deskripsi_terendah,
            'deskripsi_manual' => $this->deskripsi_manual,
        ];
    }

    public function getEffectiveDeskripsiAttribute(): string
    {
        if (!empty($this->deskripsi_manual)) {
            return $this->deskripsi_manual;
        }

        $parts = [];
        if (!empty($this->deskripsi_tertinggi)) {
            $parts[] = $this->deskripsi_tertinggi;
        }
        if (!empty($this->deskripsi_terendah)) {
            $parts[] = $this->deskripsi_terendah;
        }

        return implode(' ', $parts);
    }
}
