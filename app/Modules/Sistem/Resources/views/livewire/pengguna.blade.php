<div class="space-y-6">
    <!-- Header Halaman -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-surface p-6 rounded-2xl border border-border shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                <span>🔐 Pengaturan Sistem</span>
                <span>&bull;</span>
                <span>Identitas & Akses Pengguna</span>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">Pengelolaan Pengguna</h1>
            <p class="text-xs text-text-secondary mt-1">
                Kelola akun pengguna, penugasan multi-role, tautan profil Pegawai/Siswa, dan status keamanan login.
            </p>
        </div>
        @can('sistem.pengguna.tambah')
            <button wire:click="create" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Pengguna</span>
            </button>
        @endcan
    </div>

    <!-- Alert Notifikasi -->
    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 dark:bg-emerald-950/40 p-4 text-xs font-medium text-emerald-800 dark:text-emerald-300 flex items-center gap-2 shadow-xs">
            <span>✅</span>
            <span>{{ session('status') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 dark:bg-rose-950/40 p-4 text-xs font-medium text-rose-800 dark:text-rose-300 flex items-center gap-2 shadow-xs">
            <span>❌</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Bar Filter & Pencarian -->
    <div class="bg-surface p-4 rounded-2xl border border-border shadow-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- 1. Pencarian -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-text-secondary mb-1">Pencarian</label>
                <div class="relative">
                    <input type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Nama, username, email..."
                        class="w-full pl-8 pr-3 py-2 rounded-xl text-xs border border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                    <svg class="w-3.5 h-3.5 text-text-secondary absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- 2. Filter Role -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-text-secondary mb-1">Role Pengguna</label>
                <select wire:model.live="roleFilter" class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                    <option value="">Semua Role</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->name }}">{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Filter Status Akun -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-text-secondary mb-1">Status Akun</label>
                <select wire:model.live="statusFilter" class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            <!-- 4. Filter Keterikatan Profil -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-text-secondary mb-1">Tautan Profil</label>
                <select wire:model.live="linkedFilter" class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                    <option value="">Semua Tautan</option>
                    <option value="pegawai">Tertaut Pegawai (GTK)</option>
                    <option value="siswa">Tertaut Siswa</option>
                    <option value="umum">Akun Umum / Tanpa Tautan</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabel Pengguna -->
    <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-border text-xs border-collapse">
                <thead>
                    <tr class="bg-surface-hover/80 text-left border-b border-border text-text-secondary uppercase text-[10px] font-bold tracking-wider">
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3 px-4">Profil Tertaut</th>
                        <th class="py-3 px-4">Username / Email</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center w-56">Aksi Akun</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border font-medium">
                    @forelse ($users as $user)
                        <tr class="hover:bg-surface-hover/40 transition-colors">
                            <!-- Nama Pengguna -->
                            <td class="py-3 px-4">
                                <div class="font-bold text-text-main text-xs">{{ $user->name }}</div>
                                @if ($user->must_change_password)
                                    <span class="inline-flex items-center gap-1 mt-0.5 px-2 py-0.2 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 text-[10px] font-semibold">
                                        <span>🔑</span>
                                        <span>Wajib Ganti Password</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Profil Tertaut (Pegawai / Siswa) -->
                            <td class="py-3 px-4">
                                @if ($user->pegawai)
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200">
                                            <span>👔</span>
                                            <span>Pegawai: {{ $user->pegawai->nama_lengkap }}</span>
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-text-muted mt-0.5 font-mono">
                                        NIP: {{ $user->pegawai->nip ?: ($user->pegawai->nuptk ?: '-') }} &bull; {{ ucfirst($user->pegawai->jenis_gtk ?? 'GTK') }}
                                    </div>
                                @elseif ($user->siswa)
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border border-blue-200">
                                            <span>🎓</span>
                                            <span>Siswa: {{ $user->siswa->nama }}</span>
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-text-muted mt-0.5 font-mono">
                                        NISN: {{ $user->siswa->nisn ?: '-' }} &bull; {{ $user->siswa->rombel->nama ?? 'Tanpa Rombel' }}
                                    </div>
                                @else
                                    <span class="text-text-muted text-[11px] italic">Akun Bebas (Tanpa Profil)</span>
                                @endif
                            </td>

                            <!-- Username & Email -->
                            <td class="py-3 px-4">
                                <span class="font-mono text-xs font-semibold text-text-main">{{ $user->username }}</span>
                                @if ($user->email)
                                    <span class="block text-[11px] text-text-secondary">{{ $user->email }}</span>
                                @endif
                            </td>

                            <!-- Roles -->
                            <td class="py-3 px-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($user->roles as $role)
                                        <span class="rounded-md bg-primary/10 px-2 py-0.5 text-[11px] font-bold text-primary">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-text-muted italic">Tanpa role</span>
                                    @endforelse
                                </div>
                            </td>

                            <!-- Status Aktif -->
                            <td class="py-3 px-4 text-center">
                                @if ($user->is_active)
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-0.5 text-[11px] font-bold text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi Akun -->
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @can('sistem.pengguna.edit')
                                        <!-- Edit -->
                                        <button wire:click="edit({{ $user->id }})"
                                            class="px-2 py-1 rounded-lg border border-border bg-surface hover:bg-surface-hover text-text-main text-[11px] font-semibold transition-colors flex items-center gap-1"
                                            title="Edit data akun">
                                            <span>✏️</span>
                                            <span>Edit</span>
                                        </button>

                                        <!-- Reset Password -->
                                        <button wire:click="resetPassword({{ $user->id }})"
                                            wire:confirm="Yakin ingin mereset password akun '{{ $user->username }}' ke default (wikrama123)?"
                                            class="px-2 py-1 rounded-lg border border-amber-200 bg-amber-50/50 hover:bg-amber-100 text-amber-800 dark:bg-amber-950/30 dark:text-amber-300 text-[11px] font-semibold transition-colors flex items-center gap-1"
                                            title="Reset password ke wikrama123">
                                            <span>🔑</span>
                                            <span>Reset</span>
                                        </button>

                                        <!-- Toggle Status Aktif -->
                                        @if ($user->id !== auth()->id())
                                            <button wire:click="toggleActive({{ $user->id }})"
                                                wire:confirm="Ubah status aktif akun ini?"
                                                class="px-2 py-1 rounded-lg border text-[11px] font-semibold transition-colors {{ $user->is_active ? 'border-border text-text-secondary hover:text-text-main' : 'border-emerald-200 bg-emerald-50 text-emerald-700' }}"
                                                title="{{ $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                                {{ $user->is_active ? 'Off' : 'On' }}
                                            </button>
                                        @endif
                                    @endcan

                                    <!-- Hapus Akun -->
                                    @can('sistem.pengguna.hapus')
                                        @if ($user->id !== auth()->id())
                                            <button wire:click="deleteUser({{ $user->id }})"
                                                wire:confirm="PERINGATAN: Yakin ingin menghapus akun '{{ $user->username }}'? Tautan profil Pegawai/Siswa akan dilepas secara aman."
                                                class="px-2 py-1 rounded-lg border border-rose-200 bg-rose-50/50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/30 dark:text-rose-300 text-[11px] font-semibold transition-colors"
                                                title="Hapus akun pengguna">
                                                🗑️
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-text-secondary">
                                Tidak ada data pengguna yang cocok dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-border p-4 bg-surface-hover/30">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah / Edit Pengguna -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 overflow-y-auto">
            <div class="relative w-full max-w-xl bg-surface border border-border rounded-2xl shadow-2xl overflow-hidden my-8">
                <!-- Header Modal -->
                <div class="flex items-center justify-between border-b border-border p-5 bg-surface-hover/50">
                    <div>
                        <h2 class="text-base font-black text-text-main tracking-tight">
                            {{ $userId ? 'Edit Akun Pengguna' : 'Tambah Pengguna Baru' }}
                        </h2>
                        <p class="text-xs text-text-secondary mt-0.5">
                            Konfigurasikan informasi login, tautan profil Pegawai/Siswa, dan role.
                        </p>
                    </div>
                    <button wire:click="$set('showModal', false)" class="text-text-muted hover:text-text-main text-xl font-bold cursor-pointer">&times;</button>
                </div>

                <!-- Form Modal -->
                <form wire:submit="save" class="p-6 space-y-4 text-xs">
                    <!-- 1. Pilihan Tautkan Profil -->
                    <div class="p-4 rounded-xl border border-border bg-surface-hover/20 space-y-3">
                        <label class="block font-bold uppercase tracking-wider text-text-secondary text-[11px]">
                            🔗 Tautkan ke Profil (Opsional)
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="flex items-center gap-2 p-2 rounded-lg border border-border bg-surface cursor-pointer text-xs">
                                <input type="radio" wire:model.live="profileType" value="" class="text-primary focus:ring-primary">
                                <span>Tanpa Tautan</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 rounded-lg border border-border bg-surface cursor-pointer text-xs">
                                <input type="radio" wire:model.live="profileType" value="pegawai" class="text-primary focus:ring-primary">
                                <span>Pegawai (GTK)</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 rounded-lg border border-border bg-surface cursor-pointer text-xs">
                                <input type="radio" wire:model.live="profileType" value="siswa" class="text-primary focus:ring-primary">
                                <span>Siswa</span>
                            </label>
                        </div>

                        <!-- Dropdown Pegawai -->
                        @if ($profileType === 'pegawai')
                            <div>
                                <label class="block text-text-secondary font-semibold mb-1">Pilih Data Pegawai</label>
                                <select wire:model.live="selectedPegawaiId" class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                                    <option value="">-- Pilih Pegawai --</option>
                                    @foreach ($availablePegawais as $peg)
                                        <option value="{{ $peg->id }}">{{ $peg->nama_lengkap }} ({{ $peg->nip ?: ($peg->nuptk ?: 'Tanpa NIP') }}) &bull; {{ $peg->jenis_gtk }}</option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-text-muted mt-1">Memilih pegawai akan otomatis menyarankan nama & NIP sebagai username.</p>
                            </div>
                        @endif

                        <!-- Dropdown Siswa -->
                        @if ($profileType === 'siswa')
                            <div>
                                <label class="block text-text-secondary font-semibold mb-1">Pilih Data Siswa</label>
                                <select wire:model.live="selectedSiswaId" class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                                    <option value="">-- Pilih Siswa --</option>
                                    @foreach ($availableSiswas as $sis)
                                        <option value="{{ $sis->id }}">{{ $sis->nama }} (NISN: {{ $sis->nisn ?: '-' }}) &bull; {{ $sis->rombel->nama ?? 'Tanpa Rombel' }}</option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-text-muted mt-1">Memilih siswa akan otomatis menyarankan nama & NISN sebagai username.</p>
                            </div>
                        @endif
                    </div>

                    <!-- 2. Nama Lengkap & Username -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-text-secondary mb-1">Nama Lengkap *</label>
                            <input type="text" wire:model="name" class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                            @error('name') <p class="text-rose-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-text-secondary mb-1">Username (NIP / NIS) *</label>
                            <input type="text" wire:model="username" class="w-full text-xs font-mono rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                            @error('username') <p class="text-rose-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- 3. Email & Password -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-text-secondary mb-1">Email (Opsional)</label>
                            <input type="email" wire:model="email" class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                            @error('email') <p class="text-rose-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-text-secondary mb-1">
                                {{ $userId ? 'Password Baru (Kosongkan jika tetap)' : 'Password Awal *' }}
                            </label>
                            <input type="password" wire:model="password" autocomplete="new-password" class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                            @error('password') <p class="text-rose-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- 4. Pilihan Role -->
                    <div>
                        <label class="block font-semibold text-text-secondary mb-1.5">Penugasan Role</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 p-3 rounded-xl border border-border bg-surface-hover/20">
                            @foreach ($roles as $r)
                                <label class="flex items-center gap-2 text-xs font-medium cursor-pointer">
                                    <input type="checkbox"
                                        wire:model="selectedRoles"
                                        value="{{ $r->name }}"
                                        class="rounded border-border text-primary focus:ring-primary cursor-pointer">
                                    <span>{{ $r->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 5. Opsi Akun -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer font-medium text-xs">
                            <input type="checkbox" wire:model="is_active" class="rounded border-border text-primary focus:ring-primary">
                            <span>Akun Aktif</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer font-medium text-xs">
                            <input type="checkbox" wire:model="must_change_password" class="rounded border-border text-primary focus:ring-primary">
                            <span>Wajib Ganti Password saat Login Pertama</span>
                        </label>
                    </div>

                    <!-- Footer Modal -->
                    <div class="flex justify-end gap-2 border-t border-border pt-4">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 rounded-xl border border-border bg-surface hover:bg-surface-hover text-xs font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition-all" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">Simpan Akun</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
