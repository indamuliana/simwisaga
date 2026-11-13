<?php

namespace Database\Seeders;

use App\Modules\Kepegawaian\Services\PegawaiImportService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class SyncPegawaiDanAkunSeeder extends Seeder
{
    /**
     * Sinkronisasi data pegawai dan pembuatan akun login GTK di hosting/server
     * tanpa menghapus atau mengganggu data yang sudah ada (aman & idempoten).
     */
    public function run(): void
    {
        $filePath = database_path('seeders/data/Data_Pegawai_Wisaga.xlsx');

        if (! file_exists($filePath)) {
            $this->command?->error("File excel sumber tidak ditemukan di {$filePath}");
            return;
        }

        $this->command?->info('Langkah 1: Sinkronisasi data kepegawaian (mode append/update tanpa hapus data)...');
        $service = new PegawaiImportService();
        $stats = $service->importAppend($filePath);
        $this->command?->info(sprintf('Selesai! Ditambahkan: %d, Diperbarui: %d.', $stats['inserted'], $stats['updated']));

        $this->command?->info('Langkah 2: Generate akun login GTK untuk yang belum memiliki akun...');
        Artisan::call('kepegawaian:generate-accounts');
        $this->command?->line(Artisan::output());
        $this->command?->info('Sinkronisasi pegawai & akun login GTK selesai dengan aman!');
    }
}
