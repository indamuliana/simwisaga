<?php

namespace App\Core\Livewire\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class ModalChangePassword extends Component
{
    public bool $isOpen = false;
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected $listeners = ['open-change-password-modal' => 'open'];

    protected function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->numbers()],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'current_password' => 'password saat ini',
            'password' => 'password baru',
            'password_confirmation' => 'konfirmasi password baru',
        ];
    }

    public function open(): void
    {
        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->resetErrorBag();
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->validate();

        $user = auth()->user();
        if (! $user) {
            return;
        }

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password saat ini tidak sesuai.');
            return;
        }

        if (Hash::check($this->password, $user->password)) {
            $this->addError('password', 'Password baru tidak boleh sama dengan password saat ini.');
            return;
        }

        $user->forceFill([
            'password' => $this->password,
            'must_change_password' => false,
        ])->save();

        $this->close();
        session()->flash('status', 'Password Anda berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.auth.modal-change-password');
    }
}
