<?php

namespace App\Console\Commands;

use App\Modules\Kesiswaan\Services\DapodikImporter;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportDapodikCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'siswa:import-dapodik 
                            {file? : Path ke file Excel DAPODIK (default: database/seeders/data/dapodik.xlsx)}
                            {--fresh : Bersihkan seluruh data siswa & akun siswa/ortu terlebih dahulu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data siswa Dapodik secara massal melalui CLI tanpa batasan timeout web server';

    /**
     * Execute the console command.
     */
    public function handle(DapodikImporter $importer): int
    {
        $this->info('====================================================');
        $this->info('  SIRAGA - Import Data Siswa DAPODIK via CLI (Artisan)');
        $this->info('====================================================');

        // 1. Tentukan Path File Excel
        $fileArg = $this->argument('file');
        $filePath = null;

        if ($fileArg) {
            $filePath = file_exists($fileArg) ? $fileArg : base_path($fileArg);
        } else {
            $candidates = [
                database_path('seeders/data/dapodik.xlsx'),
                base_path('DATABASE SISWA.xlsx'),
                storage_path('app/dapodik.xlsx'),
            ];

            foreach ($candidates as $cand) {
                if (file_exists($cand)) {
                    $filePath = $cand;
                    break;
                }
            }
        }

        if (!$filePath || !file_exists($filePath)) {
            $this->error('File Excel DAPODIK tidak ditemukan.');
            $this->line('Silakan letakkan file di: database/seeders/data/dapodik.xlsx');
            $this->line('Atau berikan path file: php artisan siswa:import-dapodik "path/ke/file.xlsx"');
            return Command::FAILURE;
        }

        $this->line("File target : <comment>{$filePath}</comment>");

        // 2. Opsi Fresh / Reset Data
        if ($this->option('fresh')) {
            $this->warn('Membersihkan data siswa lama & akun siswa/orang tua...');
            $cleaned = DapodikImporter::cleanSiswaData();
            $this->info("✓ Berhasil membersihkan {$cleaned['siswas']} data siswa dan {$cleaned['users']} akun user siswa/orang tua.");
        }

        // 3. Hitung estimasi baris untuk progress bar
        $this->line('Membaca struktur file Excel...');
        $totalRows = 0;
        try {
            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            $info = $reader->listWorksheetInfo($filePath);
            $totalRows = ($info[0]['totalRows'] ?? 2) - 2;
        } catch (\Throwable $e) {
            $totalRows = 0;
        }

        $this->info("Memulai proses impor...");
        $bar = $this->output->createProgressBar(max(1, $totalRows));
        $bar->setFormat(" %current%/%max% [%bar%] %percent:3s%% -- %message%");
        $bar->setMessage('Memulai...');
        $bar->start();

        // 4. Eksekusi Import
        $result = $importer->import($filePath, function ($row, $highest, $nama, $nisn) use ($bar) {
            $bar->setMessage(substr("{$nama} ({$nisn})", 0, 35));
            $bar->advance();
        });

        $bar->finish();
        $this->newLine(2);

        // 5. Tampilkan Rangkuman Hasil
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Total Baris Diproses', $result['total_baris']],
                ['Data Siswa Baru (Imported)', $result['imported']],
                ['Data Siswa Diperbarui (Updated)', $result['updated']],
                ['Gagal / Error', count($result['errors'])],
            ]
        );

        // 6. Tampilkan Rincian Error jika ada
        if (!empty($result['errors'])) {
            $this->warn('Rincian Baris Gagal:');
            $rowsError = array_map(function ($err) {
                return [
                    $err['baris'] ?? '-',
                    $err['nama'] ?? '-',
                    substr($err['pesan'] ?? '', 0, 80),
                ];
            }, array_slice($result['errors'], 0, 20));

            $this->table(['Baris', 'Nama', 'Keterangan Error'], $rowsError);

            if (count($result['errors']) > 20) {
                $this->line('... dan ' . (count($result['errors']) - 20) . ' kesalahan lainnya.');
            }
        }

        $this->info('✓ Proses import selesai dengan sukses!');
        return Command::SUCCESS;
    }
}
