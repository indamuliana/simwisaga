<form wire:submit="login" class="card space-y-4">
 <h2 class="text-lg font-semibold">Masuk ke akun Anda</h2>

 <div>
 <label for="username" class="label">Username / NISN / NIP / Email</label>
 <input id="username" type="text" wire:model="username" class="input" autocomplete="username" autofocus>
 @error('username') <p class="error">{{ $message }}</p> @enderror
 </div>

 <div>
 <label for="password" class="label">Password</label>
 <input id="password" type="password" wire:model="password" class="input" autocomplete="current-password">
 @error('password') <p class="error">{{ $message }}</p> @enderror
 </div>

 <label class="flex items-center gap-2 text-sm text-text-secondary">
 <input type="checkbox" wire:model="remember" class="rounded border-border bg-surface text-primary focus:ring-primary/40">
 Ingat saya
 </label>

 <button type="submit" class="btn btn-primary w-full" wire:loading.attr="disabled">
 <span wire:loading.remove>Masuk</span>
 <span wire:loading>Memproses...</span>
 </button>
</form>
