<div>
    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
             x-data
             @keydown.escape.window="$wire.close()">
            <div class="relative w-full max-w-md bg-surface rounded-2xl border border-border shadow-2xl p-6 space-y-5"
                 @click.outside="$wire.close()">
                
                <!-- Header Modal -->
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-primary-soft text-primary flex items-center justify-center text-sm font-bold">
                            🔑
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-text-main">Ganti Password</h3>
                            <p class="text-[11px] text-text-muted">Perbarui kata sandi akun Anda</p>
                        </div>
                    </div>
                    <button type="button" 
                            wire:click="close" 
                            class="rounded-lg p-1.5 text-text-muted hover:text-text-main hover:bg-surface-raised transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form Ganti Password -->
                <form wire:submit="save" class="space-y-4">
                    <!-- Password Saat Ini -->
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1">
                            Password Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" 
                               wire:model="current_password" 
                               placeholder="Masukkan password saat ini..."
                               class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                        @error('current_password')
                            <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Baru -->
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1">
                            Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" 
                               wire:model="password" 
                               placeholder="Minimal 8 karakter (huruf & angka)..."
                               class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                        <p class="text-[10px] text-text-muted mt-0.5">Wajib mengandung minimal 8 karakter, huruf, dan angka.</p>
                        @error('password')
                            <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1">
                            Konfirmasi Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" 
                               wire:model="password_confirmation" 
                               placeholder="Ulangi password baru..."
                               class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                        @error('password_confirmation')
                            <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-border">
                        <button type="button" 
                                wire:click="close" 
                                class="btn btn-secondary px-4 py-2 text-xs font-semibold">
                            Batal
                        </button>
                        <button type="submit" 
                                class="btn btn-primary px-5 py-2 text-xs font-bold shadow-xs flex items-center gap-1.5"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">Simpan Password</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
