<?php

namespace App\Modules\Kesiswaan\Models;

use App\Models\User;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class RiwayatAkademik extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'riwayat_akademiks';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'rombel_asal_id',
        'rombel_tujuan_id',
        'tingkat_asal',
        'tingkat_tujuan',
        'status_keputusan',
        'catatan',
        'diproses_oleh',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function rombelAsal(): BelongsTo
    {
        return $this->belongsTo(Rombel::class, 'rombel_asal_id');
    }

    public function rombelTujuan(): BelongsTo
    {
        return $this->belongsTo(Rombel::class, 'rombel_tujuan_id');
    }

    public function diprosesOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function getLabelKeputusanAttribute(): string
    {
        return match ($this->status_keputusan) {
            'naik_kelas' => 'Naik Kelas',
            'tinggal_kelas' => 'Tinggal Kelas',
            'lulus' => 'Lulus',
            'tidak_lulus' => 'Tidak Lulus',
            'pindah' => 'Pindah Sekolah',
            'dikeluarkan' => 'Dikeluarkan',
            default => ucfirst(str_replace('_', ' ', $this->status_keputusan)),
        };
    }
}
