<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    /**
     * Membuat akun Super Admin. Password diambil dari env SEED_ADMIN_PASSWORD,
     * jika kosong dibuat acak dan ditampilkan SEKALI di terminal.
     * Tidak ada password default yang di-hardcode.
     */
    public function run(): void
    {
        $username = env('SEED_ADMIN_USERNAME', 'admin');

        if (User::where('username', $username)->exists()) {
            $this->command?->info("Super Admin '{$username}' sudah ada, dilewati.");

            return;
        }

        $password = env('SEED_ADMIN_PASSWORD') ?: Str::password(14, symbols: false);

        $user = User::create([
            'name' => 'Super Admin',
            'username' => $username,
            'email' => env('SEED_ADMIN_EMAIL'),
            'password' => $password,
            'must_change_password' => true,
        ]);
        $user->assignRole(User::SUPER_ADMIN_ROLE);

        $this->command?->warn("Super Admin dibuat -> username: {$username} | password: {$password}");
        $this->command?->warn('Simpan password ini sekarang; Anda akan diminta menggantinya saat login pertama.');
    }
}
