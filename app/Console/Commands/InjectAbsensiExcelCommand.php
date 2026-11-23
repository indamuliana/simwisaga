<?php

namespace App\Console\Commands;

use App\Enums\StatusAbsensi;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Kesiswaan\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InjectAbsensiExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absensi:inject-excel 
                            {file? : Path file CSV absensi (default: scratch/data_absensi_excel.csv)}
                            {--dry-run : Menjalankan simulasi tanpa menyimpan ke database}
                            {--clean-existing : Menghapus data absensi pada rentang tanggal file sebelum injeksi}
                            {--clean-future : Menghapus data absensi dummy setelah tanggal terakhir file}
                            {--only-absent : Hanya menyuntik data tidak hadir (S, I, A, D, dll.) tanpa Hadir}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menyuntikkan data absensi siswa dari file Excel/CSV ke database SIRAGA';

    /**
     * Pemetaan nama Rombel Excel ke Nama Rombel SIRAGA
     */
    protected array $rombelNameToDbName = [
        'HTL X' => 'X HTL 1',
        'HTL XI' => 'XI HTL 1',
        'HTL XII' => 'XII HTL 1',
        'PMN X' => 'X PMN 1',
        'PMN XI' => 'XI PMN 1',
        'PMN XII' => 'XII PMN 1',
        'PPLG X' => 'X PPLG 1',
        'PPLG XI' => 'XI PPLG 1',
        'PPLG XII' => 'XII PPLG 1',
        'TJKT X' => 'X TJKT 1',
        'TJKT XII' => 'XII TJKT 1',
    ];

    public function handle(): int
    {
        $this->info('====================================================');
        $this->info('  SIRAGA - Injeksi Data Absensi Excel/CSV ke Database');
        $this->info('====================================================');

        $filePath = $this->argument('file') ?: base_path('scratch/data_absensi_excel.csv');
        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan di: {$filePath}");
            return Command::FAILURE;
        }

        $isDryRun = (bool) $this->option('dry-run');
        $cleanExisting = (bool) $this->option('clean-existing');
        $cleanFuture = (bool) $this->option('clean-future');
        $onlyAbsent = (bool) $this->option('only-absent');

        if ($isDryRun) {
            $this->warn('Mode: DRY-RUN (Simulasi aktif, tidak ada data yang akan ditulis ke database)');
        }

        // 1. Baca File CSV
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (empty($lines)) {
            $this->error('File kosong.');
            return Command::FAILURE;
        }

        $csvRows = [];
        foreach ($lines as $line) {
            $csvRows[] = str_getcsv($line, ',', '"', '\\');
        }

        if (count($csvRows) < 4) {
            $this->error('Format data tidak memadai. Minimal membutuhkan header dan data baris siswa.');
            return Command::FAILURE;
        }

        // Baris ke-2 (indeks 2) adalah header utama kolom
        $headerRow = $csvRows[2];
        $dateCols = array_slice($headerRow, 7, -3);
        $totalDates = count($dateCols);
        $startDate = $dateCols[0];
        $endDate = $dateCols[$totalDates - 1];

        $this->line("Rentang Tanggal File: <info>{$startDate}</info> s/d <info>{$endDate}</info> ({$totalDates} hari)");

        // 2. Siapkan Master Data (Siswa, Rombel, Semester)
        $activeSemester = Semester::where('is_active', true)->first() ?: Semester::first();
        if (!$activeSemester) {
            $this->error('Tidak ditemukan semester aktif di database.');
            return Command::FAILURE;
        }
        $this->line("Semester Digunakan  : <info>{$activeSemester->id} ({$activeSemester->nama})</info>");

        // Ambil semua rombel di database untuk mapping
        $allRombels = Rombel::all()->keyBy('nama');
        $rombelIdMap = [];
        foreach ($this->rombelNameToDbName as $excelName => $dbName) {
            if ($allRombels->has($dbName)) {
                $rombelIdMap[$excelName] = $allRombels[$dbName]->id;
            }
        }

        // Ambil semua siswa aktif berdasarkan nipd
        $allSiswas = Siswa::all()->keyBy('nipd');
        $this->line("Total Siswa di DB   : <info>{$allSiswas->count()}</info>");

        $studentRows = array_slice($csvRows, 4);
        $this->line("Total Siswa di File : <info>" . count($studentRows) . "</info>");

        // 3. Bersihkan Data Dummy / Existing jika diminta
        if ($cleanExisting && !$isDryRun) {
            $this->warn("Menghapus data absensi lama pada rentang {$startDate} s/d {$endDate}...");
            $deleted = AbsensiHarian::whereBetween('tanggal', [$startDate, $endDate])->delete();
            $this->info("Berhasil menghapus {$deleted} baris data absensi lama.");
        }

        if ($cleanFuture && !$isDryRun) {
            $this->warn("Menghapus data absensi dummy setelah tanggal {$endDate}...");
            $deletedFuture = AbsensiHarian::where('tanggal', '>', $endDate)->delete();
            $this->info("Berhasil menghapus {$deletedFuture} baris data absensi masa depan.");
        }

        // 4. Proses Parsing dan Kompilasi Record Absensi
        $recordsToInsert = [];
        $matchedStudents = 0;
        $unmatchedStudents = [];
        $statusCounts = [
            'Hadir' => 0,
            'Sakit' => 0,
            'Izin' => 0,
            'Alpa' => 0,
            'Dispen_CBT_PLH' => 0,
            'Dispen_CBT_KWH' => 0,
            'Dispen_Lainnya' => 0,
        ];

        $now = Carbon::now();

        foreach ($studentRows as $row) {
            $nipd = trim($row[1] ?? '');
            $nama = trim($row[6] ?? '');
            $excelRombel = trim($row[2] ?? '');

            if (!$nipd) {
                continue;
            }

            if (!$allSiswas->has($nipd)) {
                $unmatchedStudents[] = "{$nipd} - {$nama} ({$excelRombel})";
                continue;
            }

            $matchedStudents++;
            $siswa = $allSiswas->get($nipd);
            $rombelId = $rombelIdMap[$excelRombel] ?? $siswa->rombel_id;

            // Iterasi setiap kolom tanggal
            for ($i = 0; $i < $totalDates; $i++) {
                $tanggal = $dateCols[$i];
                $cellVal = trim($row[7 + $i] ?? '');
                $cellUpper = strtoupper($cellVal);

                $status = StatusAbsensi::HADIR->value;
                $keterangan = null;

                if ($cellUpper === '' || $cellUpper === '-') {
                    if ($onlyAbsent) {
                        continue;
                    }
                    $status = StatusAbsensi::HADIR->value;
                } elseif ($cellUpper === 'S') {
                    $status = StatusAbsensi::SAKIT->value;
                    $keterangan = 'Sakit';
                } elseif ($cellUpper === 'I') {
                    $status = StatusAbsensi::IZIN->value;
                    $keterangan = 'Izin';
                } elseif ($cellUpper === 'A') {
                    $status = StatusAbsensi::ALPA->value;
                    $keterangan = 'Tanpa Keterangan / Alpa';
                } elseif ($cellUpper === 'D') {
                    $status = StatusAbsensi::DISPEN_LAINNYA->value;
                    $keterangan = 'Dispensasi';
                } elseif ($cellUpper === 'PLH') {
                    $status = StatusAbsensi::DISPEN_CBT_PLH->value;
                    $keterangan = 'Dispen CBT PLH';
                } elseif ($cellUpper === 'KWH') {
                    $status = StatusAbsensi::DISPEN_CBT_KWH->value;
                    $keterangan = 'Dispen CBT KWH';
                } elseif ($cellUpper === 'CBT') {
                    $status = StatusAbsensi::DISPEN_LAINNYA->value;
                    $keterangan = 'Dispen CBT';
                } elseif ($cellUpper === 'KKR') {
                    $status = StatusAbsensi::DISPEN_LAINNYA->value;
                    $keterangan = 'Dispen Kader Kesehatan Remaja';
                } elseif ($cellUpper === 'M') {
                    $status = StatusAbsensi::DISPEN_LAINNYA->value;
                    $keterangan = 'Dispensasi (M)';
                } else {
                    // Fallback nilai tidak dikenal
                    $status = StatusAbsensi::DISPEN_LAINNYA->value;
                    $keterangan = "Catatan: {$cellVal}";
                }

                $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;

                $recordsToInsert[] = [
                    'tanggal' => $tanggal,
                    'siswa_id' => $siswa->id,
                    'rombel_id' => $rombelId,
                    'semester_id' => $activeSemester->id,
                    'status' => $status,
                    'keterangan' => $keterangan,
                    'diinput_oleh' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        $totalRecords = count($recordsToInsert);

        $this->newLine();
        $this->info("--- RINGKASAN PARSING ---");
        $this->line("Siswa Terpetakan   : <info>{$matchedStudents}</info> siswa");
        if (!empty($unmatchedStudents)) {
            $this->warn("Siswa Tak Ditemukan : " . count($unmatchedStudents) . " siswa (baris kosong / bukan siswa aktif):");
            foreach ($unmatchedStudents as $unm) {
                $this->line("  - {$unm}");
            }
        }
        $this->line("Total Record Absensi: <info>{$totalRecords}</info> record");
        $this->newLine();
        $this->table(
            ['Status Kehadiran', 'Jumlah Record'],
            collect($statusCounts)->map(fn($cnt, $st) => [$st, number_format($cnt)])->values()->toArray()
        );

        if ($isDryRun) {
            $this->warn('Selesai (Simulasi). Tidak ada perubahan yang disimpan ke database.');
            return Command::SUCCESS;
        }

        // 5. Injeksi Data ke Database dengan Chunking Upsert
        $this->info("Menyuntikkan {$totalRecords} record ke tabel 'absensi_harians'...");
        $chunkSize = 1000;
        $chunks = array_chunk($recordsToInsert, $chunkSize);
        $bar = $this->output->createProgressBar(count($chunks));
        $bar->start();

        DB::transaction(function () use ($chunks, $bar) {
            foreach ($chunks as $chunk) {
                DB::table('absensi_harians')->upsert(
                    $chunk,
                    ['tanggal', 'siswa_id'],
                    ['rombel_id', 'semester_id', 'status', 'keterangan', 'diinput_oleh', 'updated_at']
                );
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("SUKSES: Seluruh data absensi ({$totalRecords} record) berhasil disuntikkan ke database!");

        return Command::SUCCESS;
    }
}
