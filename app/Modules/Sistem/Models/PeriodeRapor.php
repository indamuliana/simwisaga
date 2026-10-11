<?php

namespace App\Modules\Sistem\Models;

use App\Modules\Akademik\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class PeriodeRapor extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'periode_rapors';

    protected $fillable = [
        'tahun_ajaran_id',
        'semester',
        'kode',
        'nama',
        'sebutan',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_bagi',
        'tempat',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_bagi' => 'date',
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForSemester(Builder $query, string $semester): Builder
    {
        return $query->where('semester', strtolower($semester));
    }

    public function getLabelLengkapAttribute(): string
    {
        return "{$this->nama} ({$this->sebutan})";
    }

    public function getTitimangsaLengkapAttribute(): string
    {
        $tgl = $this->tanggal_bagi ? Carbon::parse($this->tanggal_bagi)->translatedFormat('d F Y') : '-';
        return "{$this->tempat}, {$tgl}";
    }
}
