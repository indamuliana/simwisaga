<?php

namespace App\Modules\Kepesantrenan\Models;

use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PesantrenKamar extends Model
{
    protected $table = 'pesantren_kamars';

    protected $fillable = [
        'tahun_ajaran_id',
        'nama_kamar',
        'gedung',
        'jenis_kelamin',
        'kapasitas',
        'musrif_id',
        'keterangan',
    ];

    protected $casts = [
        'kapasitas' => 'integer',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function musrif(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'musrif_id');
    }

    public function kamarSiswas(): HasMany
    {
        return $this->hasMany(PesantrenKamarSiswa::class, 'kamar_id');
    }

    public function siswas()
    {
        return $this->belongsToMany(Siswa::class, 'pesantren_kamar_siswas', 'kamar_id', 'siswa_id')
            ->withPivot('peran', 'nomor_ranjang')
            ->withTimestamps();
    }

    public function mudabbirs()
    {
        return $this->siswas()->wherePivot('peran', 'mudabbir');
    }

    public function getTerisiCountAttribute(): int
    {
        return $this->kamarSiswas()->count();
    }
}
