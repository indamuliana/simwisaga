<?php

namespace App\Modules\Kepesantrenan\Models;

use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PesantrenHalaqoh extends Model
{
    protected $table = 'pesantren_halaqohs';

    protected $fillable = [
        'tahun_ajaran_id',
        'nama_halaqoh',
        'pengajar_id',
        'jenis_kelamin',
        'keterangan',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function pengajar(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pengajar_id');
    }

    public function halaqohSiswas(): HasMany
    {
        return $this->hasMany(PesantrenHalaqohSiswa::class, 'halaqoh_id');
    }

    public function siswas()
    {
        return $this->belongsToMany(Siswa::class, 'pesantren_halaqoh_siswas', 'halaqoh_id', 'siswa_id')
            ->withTimestamps();
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(PesantrenHalaqohPresensi::class, 'halaqoh_id');
    }
}
