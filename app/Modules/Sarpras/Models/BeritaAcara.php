<?php

namespace App\Modules\Sarpras\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeritaAcara extends Model
{
    use HasFactory;

    protected $table = 'sarpras_berita_acaras';

    protected $fillable = [
        'nomor_surat',
        'jenis',
        'tanggal_surat',
        'pihak_pertama',
        'pihak_kedua',
        'perihal',
        'data_json',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'data_json' => 'array',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateNomorSurat(string $jenis): string
    {
        $prefix = ($jenis === 'serah_terima') ? 'BAST' : 'BAP';
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $month = $romanMonths[(int) date('n')];
        $year = date('Y');

        $latest = static::where('jenis', $jenis)
            ->whereYear('tanggal_surat', $year)
            ->orderBy('id', 'desc')
            ->value('nomor_surat');

        $seq = 1;
        if ($latest && preg_match('/^(\d+)\//', $latest, $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        return sprintf('%03d/%s-SARPRAS/%s/%s', $seq, $prefix, $month, $year);
    }
}
