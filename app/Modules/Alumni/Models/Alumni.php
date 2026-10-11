<?php

namespace App\Modules\Alumni\Models;

use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Alumni extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'alumnis';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'tahun_lulus',
        'nomor_ijazah',
        'status_karir',
        'nama_instansi',
        'posisi_jurusan',
        'pendapatan_range',
        'alamat_terkini',
        'no_hp_terkini',
        'email_terkini',
        'linkedin_url',
        'catatan',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function getStatusKarirBadgeAttribute(): string
    {
        return match ($this->status_karir) {
            'bekerja' => 'Bekerja',
            'kuliah' => 'Melanjutkan Kuliah',
            'wirausaha' => 'Wirausaha',
            'mencari_kerja' => 'Mencari Kerja',
            'belum_memutuskan' => 'Belum Memutuskan',
            default => ucfirst(str_replace('_', ' ', $this->status_karir)),
        };
    }
}
