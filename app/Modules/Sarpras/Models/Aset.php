<?php

namespace App\Modules\Sarpras\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Aset extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = 'sarpras_asets';

    protected $fillable = [
        'kode_aset',
        'nama_barang',
        'merk_tipe',
        'nomor_seri',
        'kategori_id',
        'ruangan_id',
        'sumber_dana_id',
        'tahun_perolehan',
        'tanggal_pengadaan',
        'sumber_dana',
        'harga_perolehan',
        'kondisi',
        'status',
        'foto',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'sumber_dana_id' => 'integer',
            'tahun_perolehan' => 'integer',
            'tanggal_pengadaan' => 'date',
            'harga_perolehan' => 'decimal:2',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function sumberDana(): BelongsTo
    {
        return $this->belongsTo(SumberDana::class, 'sumber_dana_id');
    }

    public function peminjamans(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'aset_id')->latest();
    }

    public function pemeliharaans(): HasMany
    {
        return $this->hasMany(Pemeliharaan::class, 'aset_id')->latest();
    }

    public static function generateKodeAset(?string $arg1, ?string $arg2 = null, ?int $arg3 = null): string
    {
        if ($arg3 === null && is_numeric($arg2)) {
            $sumberKode = null;
            $kategoriKode = (string) $arg1;
            $tahun = (int) $arg2;
        } else {
            $sumberKode = $arg1;
            $kategoriKode = (string) $arg2;
            $tahun = (int) ($arg3 ?? date('Y'));
        }

        $cleanKat = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $kategoriKode));
        $cleanSumber = $sumberKode ? strtoupper(substr(preg_replace('/[^A-Z0-9]/i', '', $sumberKode), 0, 4)) : '';

        $prefix = $cleanSumber
            ? "AST/{$cleanSumber}/{$cleanKat}/{$tahun}/"
            : "AST/{$cleanKat}/{$tahun}/";

        $latest = static::where('kode_aset', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('kode_aset');

        $nextSeq = 1;
        if ($latest) {
            $parts = explode('/', $latest);
            $lastNum = (int) end($parts);
            $nextSeq = $lastNum + 1;
        }

        return sprintf('%s%04d', $prefix, $nextSeq);
    }
}
