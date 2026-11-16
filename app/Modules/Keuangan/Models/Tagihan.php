<?php

namespace App\Modules\Keuangan\Models;

use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Tagihan extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'keuangan_tagihans';

    protected $fillable = [
        'kode_tagihan',
        'siswa_id',
        'pos_biaya_id',
        'tahun_ajaran_id',
        'bulan',
        'tahun',
        'nominal_tagihan',
        'nominal_terbayar',
        'sisa_tagihan',
        'status',
        'jatuh_tempo',
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'nominal_tagihan' => 'decimal:2',
        'nominal_terbayar' => 'decimal:2',
        'sisa_tagihan' => 'decimal:2',
        'jatuh_tempo' => 'date',
    ];

    public const DAFTAR_BULAN = [
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function posBiaya(): BelongsTo
    {
        return $this->belongsTo(PosBiaya::class, 'pos_biaya_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function transaksiItems(): HasMany
    {
        return $this->hasMany(TransaksiItem::class, 'tagihan_id');
    }

    public function getNamaBulanAttribute(): ?string
    {
        if (! $this->bulan) {
            return null;
        }

        return self::DAFTAR_BULAN[$this->bulan] ?? "Bulan {$this->bulan}";
    }

    public function getLabelTagihanAttribute(): string
    {
        $namaPos = $this->posBiaya?->nama ?? 'Tagihan';
        if ($this->posBiaya?->tipe === 'bulanan' && $this->bulan) {
            return "{$namaPos} - {$this->nama_bulan} {$this->tahun}";
        }

        return $namaPos . ($this->tahunAjaran ? " ({$this->tahunAjaran->nama})" : '');
    }

    public function scopeBelumLunas(Builder $query): Builder
    {
        return $query->whereIn('status', ['belum_bayar', 'dicicil'])->where('sisa_tagihan', '>', 0);
    }

    public function scopeLunas(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('status', 'lunas')->orWhere('sisa_tagihan', '<=', 0);
        });
    }

    /**
     * Urutkan tagihan sesuai siklus tahun ajaran:
     * 1. Pos bebas/plafon (DSP) di urutan teratas (bulan IS NULL)
     * 2. Tagihan bulanan (SPP) berurutan dari Juli tahun berjalan s.d Juni tahun berikutnya
     */
    public function scopeUrutTahunAjaran(Builder $query): Builder
    {
        return $query->orderBy('tahun_ajaran_id')
            ->orderByRaw('CASE WHEN bulan IS NULL THEN 0 ELSE 1 END ASC, tahun ASC, bulan ASC');
    }
}

