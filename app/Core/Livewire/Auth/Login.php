<?php

namespace App\Core\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.guest')]
#[Title('Masuk')]
class Login extends Component
{
    #[Validate('required|string', as: 'username')]
    public string $username = '';

    #[Validate('required|string', as: 'password')]
    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        $this->validate();

        $key = Str::lower($this->username).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('username', "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.");

            return;
        }

        // Username dapat berupa username, NISN/NIP, atau email.
        $user = User::where('username', $this->username)
            ->orWhere('email', $this->username)
            ->first();

        if (! $user || ! Auth::attempt(['id' => $user->id, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($key, 60);
            $this->addError('username', 'Username atau password salah.');

            return;
        }

        if (! $user->is_active) {
            Auth::logout();
            $this->addError('username', 'Akun Anda dinonaktifkan. Hubungi operator.');

            return;
        }

        RateLimiter::clear($key);
        session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->saveQuietly();

        return $this->redirectIntended(route('dashboard'), navigate: false);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
