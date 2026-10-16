<?php

namespace App\Modules\Akademik\Models;

use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\MapelPilihanTp;
use App\Modules\Penilaian\Models\NilaiMapelPilihanAkhir;
use App\Modules\Penilaian\Models\NilaiMapelPilihanFormatif;
use App\Modules\Penilaian\Models\NilaiMapelPilihanSumatif;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class MapelPilihan extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'tahun_ajaran_id',
        'kode',
        'nama',
        'tingkats',
        'kkm_default',
        'jp',
        'guru_id',
        'guru_pendamping_id',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'tingkats' => 'array',
        'kkm_default' => 'integer',
        'jp' => 'integer',
        'is_active' => 'boolean',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'guru_id');
    }

    public function pengajar(): BelongsTo
    {
        return $this->guru();
    }

    public function guruPendamping(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'guru_pendamping_id');
    }

    public function anggotas(): HasMany
    {
        return $this->hasMany(MapelPilihanAnggota::class);
    }

    public function siswas(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'mapel_pilihan_anggotas', 'mapel_pilihan_id', 'siswa_id')
            ->withPivot(['status', 'tahun_ajaran_id'])
            ->withTimestamps();
    }

    public function tps(): HasMany
    {
        return $this->hasMany(MapelPilihanTp::class);
    }

    public function nilaiFormatifs(): HasMany
    {
        return $this->hasMany(NilaiMapelPilihanFormatif::class);
    }

    public function nilaiSumatifs(): HasMany
    {
        return $this->hasMany(NilaiMapelPilihanSumatif::class);
    }

    public function nilaiAkhirs(): HasMany
    {
        return $this->hasMany(NilaiMapelPilihanAkhir::class);
    }

    public function getTingkatLabelAttribute(): string
    {
        if (empty($this->tingkats)) {
            return 'Semua Tingkat';
        }

        return implode(', ', (array) $this->tingkats);
    }

    public function getGuruDisplayNameAttribute(): string
    {
        if ($this->guru) {
            $name = $this->guru->nama_lengkap;
            if ($this->guruPendamping) {
                $name .= ' & ' . $this->guruPendamping->nama_lengkap;
            }
            return $name;
        }

        return 'Belum Ditentukan';
    }
}
