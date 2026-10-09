<?php

namespace App\Modules\Sistem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class DocumentTemplate extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'orientasi',
        'ukuran_kertas',
        'titimangsa_default',
        'tampilkan_kop',
        'tampilkan_nomor_halaman',
    ];

    protected $casts = [
        'tampilkan_kop' => 'boolean',
        'tampilkan_nomor_halaman' => 'boolean',
    ];

    public function signers(): HasMany
    {
        return $this->hasMany(DocumentSigner::class)->orderBy('kolom_ke')->orderBy('urutan');
    }

    public static function findByKode(string $kode): ?self
    {
        return static::with('signers')->where('kode', $kode)->first();
    }
}
