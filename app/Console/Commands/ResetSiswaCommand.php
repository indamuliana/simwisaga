<?php

namespace App\Console\Commands;

use App\Modules\Kesiswaan\Services\DapodikImporter;
use Illuminate\Console\Command;

class ResetSiswaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'siswa:reset {--force : Jalankan pembersihan tanpa meminta konfirmasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus seluruh data siswa, relasi kelas/rayon/ortu, serta akun user Siswa & Orang Tua (akun Admin/Guru tetap aman)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->warn('========================================================');
        $this->warn('  PERINGATAN: Pembersihan Seluruh Data Siswa & Akun');
        $this->warn('========================================================');
        $this->line('Perintah ini akan menghapus:');
        $this->line(' - Seluruh data siswa di tabel `siswas`');
        $this->line(' - Data orang tua siswa di tabel `siswa_orang_tuas`');
        $this->line(' - Riwayat kelas & rayon (`rombel_siswa`, `rayon_siswa`)');
        $this->line(' - Seluruh akun user bertipe `Siswa` dan `Orang Tua`');
        $this->line('');
        $this->info('Akun Super Admin, Guru, Staff, serta Master Data (Jurusan, Rombel, Rayon) TETAP AMAN.');
        $this->newLine();

        if (!$this->option('force') && !$this->confirm('Apakah Anda yakin ingin menghapus seluruh data siswa?', false)) {
            $this->info('Pembersihan dibatalkan.');
            return Command::SUCCESS;
        }

        $this->warn('Sedang membersihkan database...');
        $result = DapodikImporter::cleanSiswaData();

        $this->info("✓ Berhasil menghapus {$result['siswas']} data siswa.");
        $this->info("✓ Berhasil menghapus {$result['users']} akun user (Siswa & Orang Tua).");
        $this->info('✓ Database siswa sekarang sudah bersih.');

        return Command::SUCCESS;
    }
}
