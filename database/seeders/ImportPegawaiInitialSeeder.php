<?php

namespace Database\Seeders;

use App\Modules\Kepegawaian\Services\PegawaiImportService;
use Illuminate\Database\Seeder;

class ImportPegawaiInitialSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/data/Data_Pegawai_Wisaga.xlsx');

        if (! file_exists($filePath)) {
            $fallbackPath = '/Users/inda/.gemini/antigravity/brain/fd59613c-9e38-4543-9983-ad57ddf50d3e/.user_uploaded/media_1794551294038_b87fced3.xlsx';
            if (file_exists($fallbackPath)) {
                $filePath = $fallbackPath;
            } else {
                $this->command?->error("File sumber excel tidak ditemukan di {$filePath}");
                return;
            }
        }

        $service = new PegawaiImportService();
        $count = $service->importFresh($filePath);

        $this->command?->info("Berhasil mengimpor {$count} data pegawai riil ke tabel pegawais.");
    }
}
