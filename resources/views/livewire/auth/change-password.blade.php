<form wire:submit="save" class="card space-y-4">
 <div>
 <h2 class="text-lg font-semibold">Ganti password</h2>
 @if (auth()->user()->must_change_password)
 <p class="text-sm text-amber-600 dark:text-amber-400">Demi keamanan, Anda wajib mengganti password awal sebelum melanjutkan.</p>
 @endif
 </div>

 <div>
 <label for="current_password" class="label">Password saat ini</label>
 <input id="current_password" type="password" wire:model="current_password" class="input" autocomplete="current-password">
 @error('current_password') <p class="error">{{ $message }}</p> @enderror
 </div>

 <div>
 <label for="password" class="label">Password baru</label>
 <input id="password" type="password" wire:model="password" class="input" autocomplete="new-password">
 <p class="mt-1 text-xs text-text-secondary">Minimal 8 karakter, kombinasi huruf dan angka.</p>
 @error('password') <p class="error">{{ $message }}</p> @enderror
 </div>

 <div>
 <label for="password_confirmation" class="label">Ulangi password baru</label>
 <input id="password_confirmation" type="password" wire:model="password_confirmation" class="input" autocomplete="new-password">
 </div>

 <div class="flex gap-2">
 <button type="submit" class="btn btn-primary flex-1">Simpan</button>
 </div>
</form>
