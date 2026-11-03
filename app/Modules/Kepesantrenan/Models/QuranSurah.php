<?php

namespace App\Modules\Kepesantrenan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuranSurah extends Model
{
    protected $table = 'pesantren_quran_surahs';

    protected $primaryKey = 'nomor_surah';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'nomor_surah',
        'nama_latin',
        'nama_arab',
        'arti',
        'jumlah_ayat',
        'juz_start',
        'juz_end',
        'tempat_turun',
    ];

    public function hafalans(): HasMany
    {
        return $this->hasMany(PesantrenHafalanSiswa::class, 'surah_id', 'nomor_surah');
    }

    public function setorans(): HasMany
    {
        return $this->hasMany(PesantrenSetoranHafalan::class, 'surah_id', 'nomor_surah');
    }
}
