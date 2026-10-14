<?php

namespace App\Modules\Akademik\Models;

use App\Models\User;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class CustomKelompok extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'tahun_ajaran_id',
        'kode',
        'nama',
        'kategori',
        'keterangan',
        'pembimbing_id',
        'warna',
        'kapasitas',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'kapasitas' => 'integer',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function pembimbing(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }

    public function siswas(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'custom_kelompok_siswas')
            ->withPivot('id', 'keterangan')
            ->withTimestamps();
    }

    public function anggotas(): HasMany
    {
        return $this->hasMany(CustomKelompokSiswa::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForTahunAjaran(Builder $query, ?int $tahunAjaranId): Builder
    {
        if (! $tahunAjaranId) {
            return $query;
        }

        return $query->where(function ($q) use ($tahunAjaranId) {
            $q->where('tahun_ajaran_id', $tahunAjaranId)
              ->orWhereNull('tahun_ajaran_id');
        });
    }
}
