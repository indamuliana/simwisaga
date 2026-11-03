<?php

namespace App\Modules\Kepesantrenan\Database\Seeders;

use App\Modules\Kepesantrenan\Models\QuranSurah;
use App\Modules\Kepesantrenan\Services\QuranHelper;
use Illuminate\Database\Seeder;

class QuranSurahSeeder extends Seeder
{
    public function run(): void
    {
        $surahs = QuranHelper::getAllSurahs();

        foreach ($surahs as $s) {
            QuranSurah::updateOrCreate(
                ['nomor_surah' => $s['nomor']],
                [
                    'nama_latin' => $s['nama'],
                    'nama_arab' => $s['arab'],
                    'arti' => $s['arti'],
                    'jumlah_ayat' => $s['ayat'],
                    'juz_start' => $s['juz_start'],
                    'juz_end' => $s['juz_end'],
                    'tempat_turun' => $s['turun'],
                ]
            );
        }
    }
}
