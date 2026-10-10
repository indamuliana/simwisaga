<?php

namespace Database\Seeders;

use App\Modules\Kesiswaan\Services\DapodikImporter;
use Illuminate\Database\Seeder;

class DapodikSiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command?->info('=============================================');
        $this->command?->info('  DapodikSiswaSeeder: Import Siswa DAPODIK   ');
        $this->command?->info('=============================================');

        $filePath = database_path('seeders/data/dapodik.xlsx');

        if (!file_exists($filePath)) {
            $fallback = base_path('DATABASE SISWA.xlsx');
            if (file_exists($fallback)) {
                $filePath = $fallback;
            } else {
                $this->command?->error("File {$filePath} tidak ditemukan.");
                return;
            }
        }

        $this->command?->info("Target File: {$filePath}");

        // 1. Bersihkan data siswa & akun siswa/ortu terlebih dahulu sesuai konfigurasi
        $this->command?->warn('Membersihkan data siswa & akun siswa/orang tua lama...');
        $cleaned = DapodikImporter::cleanSiswaData();
        $this->command?->info("✓ Terhapus: {$cleaned['siswas']} data siswa, {$cleaned['users']} akun user.");

        // 2. Jalankan Import melalui DapodikImporter
        $this->command?->info('Mengimpor data siswa dari Excel Dapodik...');
        $importer = app(DapodikImporter::class);

        $result = $importer->import($filePath, function ($row, $highest, $nama, $nisn) {
            // Bisa log jika diperlukan
        });

        // 3. Tampilkan rangkuman
        $this->command?->info('---------------------------------------------');
        $this->command?->info("✓ Selesai!");
        $this->command?->info("  Total Baris: {$result['total_baris']}");
        $this->command?->info("  Berhasil Diimpor (Baru): {$result['imported']}");
        $this->command?->info("  Diperbarui: {$result['updated']}");
        $this->command?->info("  Error: " . count($result['errors']));
        $this->command?->info('---------------------------------------------');

        if (!empty($result['errors'])) {
            $this->command?->warn('Beberapa baris mengalami kesalahan:');
            foreach (array_slice($result['errors'], 0, 10) as $err) {
                $this->command?->line("  - Baris {$err['baris']} ({$err['nama']}): {$err['pesan']}");
            }
        }
    }
}
