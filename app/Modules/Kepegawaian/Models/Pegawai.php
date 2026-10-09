<?php

namespace App\Modules\Kepegawaian\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Pegawai extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'user_id',
        'nip',
        'nuptk',
        'nik',
        'nama_lengkap',
        'gelar_depan',
        'gelar_belakang',
        'jenis_kelamin',
        'jenis_gtk',
        'status_kepegawaian',
        'tempat_lahir',
        'tanggal_lahir',
        'no_hp',
        'email',
        'alamat',
        'foto_path',
        'ttd_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_lahir' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function penugasanMengajars(): HasMany
    {
        return $this->hasMany(PenugasanMengajar::class);
    }

    public function penugasanWaliKelas(): HasMany
    {
        return $this->hasMany(PenugasanWaliKelas::class);
    }

    public function penugasanPembimbingRayon(): HasMany
    {
        return $this->hasMany(PenugasanPembimbingRayon::class);
    }

    /**
     * Nama lengkap beserta gelar akademik depan dan belakang.
     */
    public function getNamaGelarAttribute(): string
    {
        $depan = $this->gelar_depan ? trim($this->gelar_depan) . ' ' : '';
        $belakang = $this->gelar_belakang ? ', ' . trim($this->gelar_belakang) : '';

        return $depan . $this->nama_lengkap . $belakang;
    }
}
