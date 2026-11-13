<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Modules\Kepegawaian\Models\Pegawai;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GeneratePegawaiAccountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kepegawaian:generate-accounts {--dry-run : Simulasi tanpa menyimpan ke database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate akun user login (Guru/Tata Usaha) untuk pegawai GTK yang belum memiliki akun';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info($dryRun ? '=== [SIMULASI DRY-RUN] Generate Akun GTK ===' : '=== Generate Akun Login GTK ===');

        $pegawais = Pegawai::whereNull('user_id')
            ->orderBy('id')
            ->get();

        if ($pegawais->isEmpty()) {
            $this->info('Semua pegawai sudah memiliki akun login. Tidak ada akun baru yang perlu dibuat.');
            return self::SUCCESS;
        }

        $this->line(sprintf('Ditemukan %d pegawai yang belum memiliki akun login.', $pegawais->count()));

        $defaultPassword = 'wisaga123';
        $results = [];

        DB::beginTransaction();

        try {
            foreach ($pegawais as $pegawai) {
                $username = self::generateUsername($pegawai->nama_lengkap);
                $role = ($pegawai->jenis_gtk === 'Guru') ? 'Guru' : 'Tata Usaha';

                // Email jika ada dan belum terpakai user lain
                $email = !empty(trim((string) $pegawai->email)) ? strtolower(trim((string) $pegawai->email)) : null;
                if ($email && User::where('email', $email)->exists()) {
                    // shortcut: jika email sudah terpakai di users, set null agar tidak melanggar unique constraint
                    $email = null;
                }

                if (!$dryRun) {
                    $user = User::create([
                        'name' => $pegawai->nama_lengkap,
                        'username' => $username,
                        'email' => $email,
                        'password' => Hash::make($defaultPassword),
                        'is_active' => true,
                        'must_change_password' => true,
                    ]);

                    $user->assignRole($role);

                    $pegawai->user_id = $user->id;
                    $pegawai->save();
                }

                $results[] = [
                    'id' => $pegawai->id,
                    'nama' => $pegawai->nama_lengkap,
                    'jenis_gtk' => $pegawai->jenis_gtk,
                    'username' => $username,
                    'role' => $role,
                    'password' => $defaultPassword,
                ];
            }

            if ($dryRun) {
                DB::rollBack();
                $this->warn('Mode simulasi (dry-run): tidak ada data yang disimpan ke database.');
            } else {
                DB::commit();
                $this->info(sprintf('Berhasil membuat %d akun baru dan menautkannya ke profil pegawai!', count($results)));
            }

            $this->table(
                ['ID Pegawai', 'Nama Lengkap', 'Jenis GTK', 'Username Baru', 'Role', 'Password Default'],
                $results
            );

            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Generate username bersih, huruf kecil, 2 kata inti nama tanpa gelar.
     */
    public static function generateUsername(string $namaLengkap): string
    {
        // Hapus gelar di belakang tanda koma (S.Pd, S.Kom, Gr, dll)
        $clean = preg_replace('/,.*$/', '', $namaLengkap);
        // Hapus singkatan gelar umum di depan
        $clean = preg_replace('/\b(dr|drs|dra|ir|h|hj|prof|spd|mpd|st|se|kom)\b\.?/i', '', $clean);
        // Hapus karakter non-alfanumerik kecuali spasi
        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', $clean);
        $words = array_values(array_filter(explode(' ', strtolower(trim((string) $clean)))));

        // Jika kata pertama berupa 1 huruf inisial (misal 'U Abdul Aziz'), gunakan kata selanjutnya
        if (count($words) > 2 && strlen($words[0]) <= 1) {
            array_shift($words);
        }

        $base = count($words) >= 2 ? $words[0] . '.' . $words[1] : ($words[0] ?? 'pegawai');

        // Pastikan unik terhadap tabel users
        $username = $base;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $counter++;
            $username = $base . $counter;
        }

        return $username;
    }
}
