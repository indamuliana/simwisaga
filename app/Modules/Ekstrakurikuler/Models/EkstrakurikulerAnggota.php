<?php

namespace App\Modules\Ekstrakurikuler\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Akademik\Models\Semester;

class EkstrakurikulerAnggota extends Model
{
    protected $fillable = [
        'ekstrakurikuler_id',
        'siswa_id',
        'semester_id',
        'sts_1',
        'sas',
        'sts_2',
        'sat',
        'nilai_presensi',
        'total_hadir',
        'total_pertemuan',
        'persentase_kehadiran',
        'status_keanggotaan',
        'catatan_pindah',
        'nilai_angka',
        'nilai_huruf',
        'deskripsi',
        'evaluations',
    ];

    protected $casts = [
        'sts_1' => 'float',
        'sas' => 'float',
        'sts_2' => 'float',
        'sat' => 'float',
        'nilai_presensi' => 'float',
        'nilai_angka' => 'float',
        'total_hadir' => 'integer',
        'total_pertemuan' => 'integer',
        'persentase_kehadiran' => 'float',
        'evaluations' => 'array',
    ];

    public function ekstrakurikuler()
    {
        return $this->belongsTo(Ekstrakurikuler::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function presensiDetails()
    {
        return $this->hasMany(EkskulPresensiDetail::class, 'siswa_id', 'siswa_id');
    }

    public function getPredikatAttribute(): ?string
    {
        return $this->nilai_huruf;
    }

    public function getKeteranganAttribute(): ?string
    {
        return $this->deskripsi;
    }

    /**
     * Mengambil evaluasi ekskul terisolasi per periode (STS 1, SAS, STS 2, SAT / 9M1 s/d 9M4)
     */
    public function getEvaluationForPeriod(?string $period): ?array
    {
        if (empty($period)) {
            return [
                'asesmen' => null,
                'nilai_presensi' => $this->nilai_presensi ?? 100.0,
                'nilai_akhir' => $this->nilai_angka,
                'nilai_huruf' => $this->nilai_huruf,
                'deskripsi' => $this->deskripsi,
            ];
        }

        $key = strtoupper(trim($period));
        $normMap = [
            '9M1' => 'STS 1',
            '9M2' => 'SAS',
            '9M3' => 'STS 2',
            '9M4' => 'SAT',
            'STS 1' => 'STS 1',
            'SAS' => 'SAS',
            'STS 2' => 'STS 2',
            'SAT' => 'SAT',
        ];
        $target = $normMap[$key] ?? $key;

        $evals = $this->evaluations ?? [];
        if (isset($evals[$target]) && is_array($evals[$target])) {
            return $evals[$target];
        }

        // Fallback ke kolom periode spesifik jika ada
        $score = match ($target) {
            'STS 1' => $this->sts_1,
            'SAS' => $this->sas,
            'STS 2' => $this->sts_2,
            'SAT' => $this->sat,
            default => null,
        };

        if ($score !== null) {
            return [
                'asesmen' => (float) $score,
                'nilai_presensi' => (float) ($this->nilai_presensi ?? 100.0),
                'nilai_akhir' => (float) ($this->nilai_angka ?? $score),
                'nilai_huruf' => $this->nilai_huruf ?? 'Baik',
                'deskripsi' => $this->deskripsi ?? '',
            ];
        }

        if ($this->nilai_angka !== null || $this->nilai_huruf !== null || $this->deskripsi !== null) {
            return [
                'asesmen' => null,
                'nilai_presensi' => (float) ($this->nilai_presensi ?? 100.0),
                'nilai_akhir' => $this->nilai_angka,
                'nilai_huruf' => $this->nilai_huruf,
                'deskripsi' => $this->deskripsi,
            ];
        }

        return null;
    }
}
