<?php

namespace App\Core\Livewire\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.guest')]
#[Title('Ganti Password')]
class ChangePassword extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function save()
    {
        $this->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [], [
            'current_password' => 'password saat ini',
            'password' => 'password baru',
        ]);

        $user = auth()->user();

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password saat ini tidak sesuai.');

            return;
        }

        if (Hash::check($this->password, $user->password)) {
            $this->addError('password', 'Password baru tidak boleh sama dengan password lama.');

            return;
        }

        $user->forceFill(['password' => $this->password, 'must_change_password' => false])->save();

        session()->flash('status', 'Password berhasil diganti.');

        return $this->redirect(route('dashboard'));
    }

    public function render()
    {
        return view('livewire.auth.change-password');
    }
}
