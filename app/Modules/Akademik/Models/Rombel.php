<?php

namespace App\Modules\Akademik\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Rombel extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'tahun_ajaran_id',
        'jurusan_id',
        'tingkat',
        'nomor_urut',
        'nama',
        'wali_kelas_id',
        'kapasitas',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    /**
     * Format resmi nama rombel SMK Wikrama 1 Garut: [Tingkat] [Jurusan] [Nomor]
     * Contoh: X PPLG 1, XI TJKT 2, XII HTL 1
     */
    public static function generateNama(string $tingkat, string $kodeJurusan, int $nomorUrut): string
    {
        return "{$tingkat} {$kodeJurusan} {$nomorUrut}";
    }

    public function siswas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Modules\Kesiswaan\Models\Siswa::class);
    }
}
