<?php

namespace App\Modules\Sistem\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use ZipArchive;

class DatabaseBackupService
{
    /**
     * Pemetaan tabel per modul sistem SIRAGA.
     */
    public const MODULE_TABLES = [
        'Akademik' => [
            'tahun_ajarans', 'semesters', 'jurusans', 'tingkat_rombels', 'rombels',
            'kelompok_mapels', 'mapels', 'struktur_kurikulums', 'penugasan_mengajars',
            'anggota_rombels', 'jadwal_pelajarans', 'custom_kelompoks', 'custom_kelompok_mapels',
            'mapel_pilihans', 'siswa_mapel_pilihans'
        ],
        'Kesiswaan' => [
            'siswas', 'riwayat_akademiks', 'prestasis'
        ],
        'Kepegawaian' => [
            'pegawais', 'riwayat_pendidikans', 'riwayat_pangkat_golongans', 'prestasi_gurus'
        ],
        'Penilaian' => [
            'tujuan_pembelajarans', 'tujuan_pembelajaran_rombels', 'alokasi_asesmen_harians',
            'nilai_asesmen_harians', 'rencana_penilaians', 'bobot_penilaians', 'nilai_formatifs',
            'nilai_sumatifs', 'nilai_akhirs'
        ],
        'Rapor' => [
            'periode_rapors', 'catatan_wali_kelas', 'ekskul_rapors', 'absensi_rapors',
            'kenaikan_kelass', 'rapor_cetak_logs'
        ],
        'Absensi' => [
            'absensi_harians', 'absensi_mapels'
        ],
        'Ekstrakurikuler' => [
            'ekstrakurikulers', 'anggota_ekskuls', 'pembina_ekskuls', 'jadwal_ekskuls',
            'presensi_ekskuls', 'penilaian_ekskuls'
        ],
        'Prestasi' => [
            'prestasis', 'prestasi_gurus', 'prestasi_sekolahs'
        ],
        'Alumni' => [
            'alumnis', 'tracer_studies'
        ],
        'Beasiswa & OTA' => [
            'beasiswas', 'penerima_beasiswas', 'donatur_otas', 'anak_asuh_otas',
            'ota_donasi_masuks', 'ota_penyalurans'
        ],
        'Kalender Pendidikan' => [
            'kalender_kategoris', 'kalender_agendas', 'kalender_liburs'
        ],
        'Sarpras' => [
            'sarpras_tanahs', 'sarpras_bangunans', 'sarpras_ruangans', 'sarpras_kategori_barangs',
            'sarpras_barangs', 'sarpras_sumber_danas', 'sarpras_pemeliharaans', 'sarpras_ruangan_barangs'
        ],
        'Administrasi Web' => [
            'web_settings', 'web_categories', 'web_articles', 'web_menus', 'web_pages',
            'web_facilities', 'web_testimonials'
        ],
        'Pengaturan Sistem & Pengguna' => [
            'users', 'password_reset_tokens', 'sessions', 'roles', 'permissions',
            'model_has_roles', 'model_has_permissions', 'role_has_permissions',
            'schools', 'role_scopes', 'document_templates', 'document_signers',
            'audits', 'ai_settings'
        ],
    ];

    /**
     * Path direktori binary MySQL/mysqldump (lokal xampp atau system PATH).
     */
    public function getMysqlBinaryPath(string $binary = 'mysqldump'): string
    {
        $xamppPath = "C:\\xampp\\mysql\\bin\\{$binary}.exe";
        if (File::exists($xamppPath)) {
            return $xamppPath;
        }

        return $binary;
    }

    /**
     * Dapatkan daftar modul yang tersedia untuk backup sebagian.
     */
    public function getAvailableModules(): array
    {
        return array_keys(self::MODULE_TABLES);
    }

    /**
     * Jalankan proses Backup.
     */
    public function createBackup(string $type = 'full', array $selectedModules = [], bool $includeFiles = false, ?int $userId = null): array
    {
        $backupDir = storage_path('app/backups');
        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true, true);
        }

        $timestamp = date('Ymd_His');
        $baseName = 'backup_' . $type . '_' . $timestamp;
        $tempSqlFile = $backupDir . DIRECTORY_SEPARATOR . $baseName . '.sql';
        $finalZipFile = $backupDir . DIRECTORY_SEPARATOR . $baseName . '.zip';

        // Tentukan tabel apa saja yang di-dump
        $tablesToDump = [];
        if ($type === 'partial') {
            foreach ($selectedModules as $module) {
                if (isset(self::MODULE_TABLES[$module])) {
                    $tablesToDump = array_merge($tablesToDump, self::MODULE_TABLES[$module]);
                }
            }
            $tablesToDump = array_values(array_unique($tablesToDump));
            if (empty($tablesToDump)) {
                throw new Exception("Pilih minimal satu modul yang valid untuk backup sebagian.");
            }
        }

        // 1. Eksekusi mysqldump
        $this->dumpDatabase($tempSqlFile, $tablesToDump);

        // 2. Buat ZIP Archive
        $zip = new ZipArchive();
        if ($zip->open($finalZipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            if (File::exists($tempSqlFile)) {
                File::delete($tempSqlFile);
            }
            throw new Exception("Gagal membuat arsip ZIP untuk backup.");
        }

        // Tambahkan file database.sql ke dalam ZIP
        $zip->addFile($tempSqlFile, 'database.sql');

        // Tambahkan file metadata
        $meta = [
            'type' => $type,
            'modules' => $type === 'partial' ? $selectedModules : null,
            'has_files' => $includeFiles,
            'created_at' => now()->toIso8601String(),
            'app_version' => 'SIRAGA 1.0',
        ];
        $zip->addFromString('backup_meta.json', json_encode($meta, JSON_PRETTY_PRINT));

        // Jika user memilih menyertakan file storage (storage/app/public)
        if ($includeFiles && $type === 'full') {
            $publicStorageDir = storage_path('app/public');
            if (File::isDirectory($publicStorageDir)) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($publicStorageDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );
                foreach ($files as $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = 'storage/' . substr($filePath, strlen($publicStorageDir) + 1);
                        $zip->addFile($filePath, str_replace('\\', '/', $relativePath));
                    }
                }
            }
        }

        $zip->close();

        // Hapus file SQL sementara
        if (File::exists($tempSqlFile)) {
            File::delete($tempSqlFile);
        }

        $fileSize = File::size($finalZipFile);
        $relativeStoragePath = 'backups/' . basename($finalZipFile);

        return [
            'filename' => basename($finalZipFile),
            'filepath' => $relativeStoragePath,
            'type' => $type,
            'modules' => $type === 'partial' ? $selectedModules : null,
            'has_files' => $includeFiles,
            'size_bytes' => $fileSize,
            'created_by' => $userId,
        ];
    }

    /**
     * Dump database menggunakan mysqldump binary.
     */
    protected function dumpDatabase(string $outputSqlPath, array $tables = []): void
    {
        $mysqldump = $this->getMysqlBinaryPath('mysqldump');
        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');
        $database = config('database.connections.mysql.database', 'siraga');
        $username = config('database.connections.mysql.username', 'root');
        $password = config('database.connections.mysql.password', '');

        $cmd = [
            $mysqldump,
            "--host={$host}",
            "--port={$port}",
            "--user={$username}",
        ];

        if (!empty($password)) {
            $cmd[] = "--password={$password}";
        }

        // Opsi dump yang aman dan terstruktur
        $cmd[] = '--quick';
        $cmd[] = '--single-transaction';
        $cmd[] = '--skip-lock-tables';
        $cmd[] = '--routines';
        $cmd[] = '--triggers';
        $cmd[] = '--result-file=' . $outputSqlPath;

        $cmd[] = $database;

        if (!empty($tables)) {
            $cmd = array_merge($cmd, $tables);
        }

        $process = new Process($cmd);
        $process->setTimeout(600); // 10 menit
        $process->run();

        if (!$process->isSuccessful() || !File::exists($outputSqlPath) || File::size($outputSqlPath) === 0) {
            $error = $process->getErrorOutput();
            throw new Exception("mysqldump gagal: " . ($error ?: "File output kosong atau tidak terbentuk."));
        }
    }

    /**
     * Jalankan proses Restore dari arsip ZIP atau file .sql.
     */
    public function restoreBackup(string $absoluteFilePath): array
    {
        if (!File::exists($absoluteFilePath)) {
            throw new Exception("File backup tidak ditemukan di path: {$absoluteFilePath}");
        }

        $tempExtractDir = storage_path('app/backups/temp_restore_' . time());
        File::makeDirectory($tempExtractDir, 0755, true, true);

        try {
            $sqlFile = null;
            $extension = strtolower(pathinfo($absoluteFilePath, PATHINFO_EXTENSION));

            if ($extension === 'zip') {
                $zip = new ZipArchive();
                if ($zip->open($absoluteFilePath) !== true) {
                    throw new Exception("Gagal membuka file arsip ZIP.");
                }

                $zip->extractTo($tempExtractDir);
                $zip->close();

                // Cari database.sql di hasil ekstrak
                if (File::exists($tempExtractDir . DIRECTORY_SEPARATOR . 'database.sql')) {
                    $sqlFile = $tempExtractDir . DIRECTORY_SEPARATOR . 'database.sql';
                } else {
                    $found = glob($tempExtractDir . DIRECTORY_SEPARATOR . '*.sql');
                    if (!empty($found)) {
                        $sqlFile = $found[0];
                    }
                }

                if (!$sqlFile) {
                    throw new Exception("File dump SQL tidak ditemukan di dalam paket ZIP.");
                }

                // Restore folder storage jika ada
                $extractedStorage = $tempExtractDir . DIRECTORY_SEPARATOR . 'storage';
                if (File::isDirectory($extractedStorage)) {
                    $targetStorage = storage_path('app/public');
                    if (!File::isDirectory($targetStorage)) {
                        File::makeDirectory($targetStorage, 0755, true, true);
                    }
                    File::copyDirectory($extractedStorage, $targetStorage);
                }
            } elseif ($extension === 'sql') {
                $sqlFile = $absoluteFilePath;
            } else {
                throw new Exception("Format file tidak didukung. Harap gunakan file .zip atau .sql.");
            }

            // Jalankan import SQL ke database
            $this->importDatabase($sqlFile);

            return [
                'success' => true,
                'message' => 'Proses restore database dan berkas berhasil diselesaikan.',
            ];
        } finally {
            // Bersihkan direktori sementara
            if (File::isDirectory($tempExtractDir)) {
                File::deleteDirectory($tempExtractDir);
            }
        }
    }

    /**
     * Import file SQL ke database menggunakan mysql CLI client.
     */
    protected function importDatabase(string $sqlFilePath): void
    {
        $mysql = $this->getMysqlBinaryPath('mysql');
        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');
        $database = config('database.connections.mysql.database', 'siraga');
        $username = config('database.connections.mysql.username', 'root');
        $password = config('database.connections.mysql.password', '');

        $cmd = [
            $mysql,
            "--host={$host}",
            "--port={$port}",
            "--user={$username}",
        ];

        if (!empty($password)) {
            $cmd[] = "--password={$password}";
        }

        $cmd[] = $database;

        // Eksekusi pipe input dari file SQL
        $process = new Process($cmd);
        $process->setInput(fopen($sqlFilePath, 'r'));
        $process->setTimeout(600); // 10 menit
        $process->run();

        if (!$process->isSuccessful()) {
            throw new Exception("mysql import gagal: " . $process->getErrorOutput());
        }
    }
}
