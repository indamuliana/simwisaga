<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterWilayahSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/data/wilayah_kemendagri.sql.gz');
        if (! file_exists($filePath)) {
            $this->command?->error("File dataset {$filePath} tidak ditemukan.");

            return;
        }

        $gz = gzopen($filePath, 'r');
        if (! $gz) {
            $this->command?->error("Gagal membuka file dataset {$filePath}.");

            return;
        }

        $this->command?->info('Mengimpor Master Wilayah Kemendagri...');

        DB::disableQueryLog();
        DB::table('master_wilayahs')->truncate();

        $chunk = [];
        $total = 0;
        $batchSize = 2500;

        while (! gzeof($gz)) {
            $line = trim(gzgets($gz));
            if (preg_match("/^\('([0-9.]+)',\s*'(.*)'\)[,;]?$/", $line, $m)) {
                $kode = $m[1];
                $nama = str_replace("''", "'", $m[2]);
                $dots = substr_count($kode, '.');
                $level = $dots + 1;
                $parent = $dots > 0 ? substr($kode, 0, strrpos($kode, '.')) : null;

                if ($level === 2) {
                    $nama = preg_replace('/^Kabupaten\s+/i', 'Kab. ', $nama);
                }

                $chunk[] = [
                    'kode' => $kode,
                    'nama' => $nama,
                    'level' => $level,
                    'parent_kode' => $parent,
                ];

                if (count($chunk) >= $batchSize) {
                    DB::table('master_wilayahs')->insert($chunk);
                    $total += count($chunk);
                    $chunk = [];
                }
            }
        }

        if (! empty($chunk)) {
            DB::table('master_wilayahs')->insert($chunk);
            $total += count($chunk);
        }

        gzclose($gz);

        $this->command?->info("Master Wilayah Kemendagri berhasil diimpor: {$total} data.");
    }
}
