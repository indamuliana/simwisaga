<div class="space-y-6">
    <!-- Header Halaman -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-surface p-6 rounded-2xl border border-border shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                <span>🔐 Pengaturan Sistem</span>
                <span>&bull;</span>
                <span>Role & Permissions</span>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">Matriks Hak Akses</h1>
            <p class="text-xs text-text-secondary mt-1">
                Atur izin akses fitur (Lihat, Tambah, Edit, Hapus, Cetak, dan Aksi Khusus) serta batasan scope data per role.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" 
                    wire:click="openManageRoles" 
                    class="px-4 py-2.5 rounded-xl border border-border bg-surface hover:bg-surface-hover text-text-main text-xs font-bold shadow-2xs transition-all flex items-center gap-1.5">
                <span>⚙️</span>
                <span>Kelola Master Role</span>
            </button>
            <button type="button" 
                    wire:click="openCreateRole" 
                    class="px-4 py-2.5 rounded-xl bg-primary-soft hover:bg-primary/20 text-primary border border-primary/30 text-xs font-bold transition-all flex items-center gap-1.5">
                <span>➕</span>
                <span>Buat Role Baru</span>
            </button>
            @if ($selectedRole)
                <button wire:click="save" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition-all flex items-center gap-2" wire:loading.attr="disabled">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            @endif
        </div>
    </div>

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

    <!-- Pemilih Role Tabs -->
    <div class="border-b border-border bg-surface p-2 rounded-2xl border shadow-xs">
        <nav class="flex flex-wrap items-center gap-1.5" aria-label="Tabs">
            @foreach ($roles as $role)
                <div class="inline-flex items-center rounded-xl transition-all {{ $selectedRoleId == $role->id ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                    <button type="button"
                        wire:click="selectRole('{{ $role->id }}')"
                        class="whitespace-nowrap px-3 py-2 text-xs font-bold flex items-center gap-1.5 focus:outline-hidden">
                        <span>{{ $role->name }}</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $selectedRoleId == $role->id ? 'bg-white/20 text-white' : 'bg-surface-raised text-text-muted border border-border' }}" title="{{ $role->users_count }} pengguna aktif">
                            {{ $role->users_count }} user
                        </span>
                    </button>
                    @if($selectedRoleId == $role->id && !in_array($role->name, ['Super Admin']))
                        <button type="button" 
                                wire:click="openEditRole({{ $role->id }})" 
                                title="Ubah Nama Role"
                                class="pr-2.5 pl-0.5 text-white/80 hover:text-white transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    @endif
                </div>
            @endforeach
            <button type="button"
                    wire:click="openCreateRole"
                    class="px-3 py-2 rounded-xl text-xs font-bold text-primary hover:bg-primary-soft border border-dashed border-primary/40 transition-colors flex items-center gap-1">
                <span>➕</span>
                <span>Tambah Role</span>
            </button>
        </nav>
    </div>

    @if ($selectedRole)
        <!-- Panel Konfigurasi Role & Toolbar -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <!-- Batasan Akses Data (Scope) -->
            <div class="md:col-span-6 bg-surface p-5 rounded-2xl border border-border shadow-xs space-y-2">
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-text-secondary">
                    <span>🧭</span>
                    <span>Batasan Akses Data (Scope)</span>
                </div>
                <label class="block text-xs text-text-secondary">
                    Cakupan data untuk role <strong>{{ $selectedRole->name }}</strong>:
                </label>
                <select wire:model.live="roleScopes.{{ $selectedRole->id }}" class="w-full text-xs font-semibold rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                    @foreach ($availableScopes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-text-secondary">
                    Menentukan batas data apa saja yang dapat dikelola oleh pengguna dengan peran ini (misal: Wali Kelas dibatasi pada rombelnya).
                </p>
            </div>

            <!-- Toolbar Aksi Massal & Pencarian -->
            <div class="md:col-span-6 bg-surface p-5 rounded-2xl border border-border shadow-xs flex flex-col justify-between gap-3">
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-text-secondary">
                    <span>⚡</span>
                    <span>Aksi Cepat & Pencarian Fitur</span>
                </div>

                <!-- Input Pencarian -->
                <div class="relative w-full">
                    <input type="text"
                        wire:model.live.debounce.250ms="search"
                        placeholder="Cari modul atau nama fitur (misal: Rapor, Nilai, Siswa)..."
                        class="w-full pl-9 pr-4 py-2 rounded-xl text-xs border border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                    <svg class="w-4 h-4 text-text-secondary absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Tombol Pilih / Kosongkan Semua -->
                <div class="flex items-center gap-2 pt-1 border-t border-border">
                    <button type="button"
                        wire:click="selectAllForRole"
                        class="px-3 py-1.5 rounded-lg border border-border bg-surface hover:bg-surface-hover text-text-main text-[11px] font-semibold transition-colors flex items-center gap-1.5">
                        <span>☑️</span>
                        <span>Pilih Semua Fitur</span>
                    </button>
                    <button type="button"
                        wire:click="clearAllForRole"
                        wire:confirm="Yakin ingin mengosongkan seluruh hak akses untuk role ini?"
                        class="px-3 py-1.5 rounded-lg border border-rose-200 dark:border-rose-900 bg-rose-50/50 dark:bg-rose-950/30 hover:bg-rose-100 text-rose-700 dark:text-rose-300 text-[11px] font-semibold transition-colors flex items-center gap-1.5">
                        <span>⏹️</span>
                        <span>Kosongkan Semua</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabel Matriks Permission -->
        <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-border text-xs border-collapse">
                    <thead>
                        <tr class="bg-surface-hover/80 text-left border-b border-border text-text-secondary uppercase text-[10px] font-bold tracking-wider">
                            <th class="py-3 px-4 w-72">Modul / Fitur</th>
                            @foreach ($standardActions as $stdAct)
                                <th class="py-3 px-3 text-center w-20 capitalize font-mono">{{ $stdAct }}</th>
                            @endforeach
                            <th class="py-3 px-4 text-center min-w-44">Aksi Khusus</th>
                            <th class="py-3 px-3 text-center w-28">Pilih Baris</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border font-medium">
                        @php $currentModule = ''; @endphp
                        @forelse ($filteredMatrix as $resource => $def)
                            @if ($def['module'] !== $currentModule)
                                @php $currentModule = $def['module']; @endphp
                                <tr class="bg-surface-hover font-bold border-t-2 border-border/80 text-text-main">
                                    <td colspan="8" class="py-2.5 px-4 text-xs uppercase tracking-wider text-primary flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span>📁</span>
                                            <span>{{ $currentModule }}</span>
                                        </div>
                                        <button type="button"
                                            wire:click="toggleAllForModule('{{ $currentModule }}')"
                                            class="text-[11px] font-bold normal-case text-primary hover:underline px-2 py-0.5 rounded bg-primary/10">
                                            Toggle Semua Akses Modul Ini
                                        </button>
                                    </td>
                                </tr>
                            @endif
                            <tr class="hover:bg-surface-hover/40 transition-colors">
                                <!-- Nama Fitur & Resource Key -->
                                <td class="py-3 px-4">
                                    <div class="font-bold text-text-main text-xs">{{ $def['label'] }}</div>
                                    <div class="text-[10px] text-text-secondary font-mono">{{ $resource }}</div>
                                </td>

                                <!-- 5 Kolom Standar: Lihat, Tambah, Edit, Hapus, Cetak -->
                                @foreach ($standardActions as $act)
                                    <td class="py-3 px-3 text-center">
                                        @if (in_array($act, $def['actions'], true))
                                            @php $permName = "{$resource}.{$act}"; @endphp
                                            <input type="checkbox"
                                                wire:click="togglePermission('{{ $permName }}')"
                                                @checked(in_array($permName, $rolePermissions, true))
                                                class="h-4 w-4 rounded border-border text-primary focus:ring-primary cursor-pointer transition-all">
                                        @else
                                            <span class="text-xs text-text-muted select-none">-</span>
                                        @endif
                                    </td>
                                @endforeach

                                <!-- Kolom Aksi Khusus (Dinamis untuk kelola, setting, input, lihat_sensitif, dll) -->
                                @php
                                    $customActions = array_diff($def['actions'], $standardActions);
                                @endphp
                                <td class="py-3 px-4 text-center">
                                    @if (!empty($customActions))
                                        <div class="flex flex-wrap items-center justify-center gap-1.5">
                                            @foreach ($customActions as $cAct)
                                                @php
                                                    $permName = "{$resource}.{$cAct}";
                                                    $isChecked = in_array($permName, $rolePermissions, true);
                                                @endphp
                                                <button type="button"
                                                    wire:click="togglePermission('{{ $permName }}')"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold border transition-all cursor-pointer {{ $isChecked ? 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-800' : 'bg-surface border-border text-text-secondary hover:bg-surface-hover' }}">
                                                    <span>{{ $isChecked ? '✓' : '+' }}</span>
                                                    <span>{{ ucfirst(str_replace('_', ' ', $cAct)) }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-text-muted select-none">-</span>
                                    @endif
                                </td>

                                <!-- Tombol Toggle Satu Baris -->
                                <td class="py-3 px-3 text-center">
                                    <button type="button"
                                        wire:click="toggleAllForResource('{{ $resource }}', {{ json_encode($def['actions']) }})"
                                        class="px-2 py-1 rounded text-[11px] font-semibold text-primary hover:bg-primary/10 transition-colors">
                                        Toggle
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-text-secondary">
                                    Tidak ada fitur yang cocok dengan kata kunci "{{ $search }}".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Simpan -->
            <div class="p-4 bg-surface-hover/50 border-t border-border flex items-center justify-between">
                <div class="text-xs text-text-secondary">
                    Total <strong class="text-text-main">{{ count($rolePermissions) }}</strong> izin aktif untuk role <strong class="text-primary">{{ $selectedRole->name }}</strong>
                </div>
                <button wire:click="save" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition-all flex items-center gap-2" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    @endif

    <!-- Modal Kelola Master Role -->
    @if ($showRoleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
             x-data
             @keydown.escape.window="$wire.closeRoleModal()">
            <div class="relative w-full {{ $roleModalMode === 'manage' ? 'max-w-2xl' : 'max-w-md' }} bg-surface rounded-2xl border border-border shadow-2xl p-6 space-y-5"
                 @click.outside="$wire.closeRoleModal()">
                
                <!-- Header Modal -->
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-primary-soft text-primary flex items-center justify-center text-sm font-bold">
                            {{ $roleModalMode === 'manage' ? '⚙️' : ($roleModalMode === 'edit' ? '✏️' : '➕') }}
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-text-main">
                                {{ $roleModalMode === 'manage' ? 'Master Role Pengguna' : ($roleModalMode === 'edit' ? 'Ubah Nama Role' : 'Tambah Role Baru') }}
                            </h3>
                            <p class="text-[11px] text-text-muted">
                                {{ $roleModalMode === 'manage' ? 'Daftar semua role dan jumlah pengguna aktif' : 'Tentukan nama peran untuk hak akses sistem' }}
                            </p>
                        </div>
                    </div>
                    <button type="button" 
                            wire:click="closeRoleModal" 
                            class="rounded-lg p-1.5 text-text-muted hover:text-text-main hover:bg-surface-raised transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Konten Modal: Manage List vs Form Create/Edit -->
                @if ($roleModalMode === 'manage')
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-text-muted">Total <strong>{{ count($roles) }}</strong> role terdaftar</span>
                            <button type="button" 
                                    wire:click="openCreateRole"
                                    class="btn btn-primary px-3 py-1.5 text-xs font-bold flex items-center gap-1 shadow-2xs">
                                <span>➕</span>
                                <span>Tambah Role Baru</span>
                            </button>
                        </div>

                        <div class="max-h-[55vh] overflow-y-auto rounded-xl border border-border">
                            <table class="w-full divide-y divide-border text-xs">
                                <thead class="bg-surface-hover/80 text-text-secondary text-[10px] uppercase font-bold sticky top-0">
                                    <tr>
                                        <th class="py-2.5 px-3 text-left">Nama Role</th>
                                        <th class="py-2.5 px-3 text-center">Pengguna</th>
                                        <th class="py-2.5 px-3 text-center">Tipe</th>
                                        <th class="py-2.5 px-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border divide-dashed">
                                    @foreach ($roles as $r)
                                        <tr class="hover:bg-surface-hover/50 transition-colors">
                                            <td class="py-2.5 px-3 font-semibold text-text-main">
                                                <button type="button" 
                                                        wire:click="selectRole('{{ $r->id }}'); closeRoleModal();" 
                                                        class="hover:text-primary hover:underline text-left">
                                                    {{ $r->name }}
                                                </button>
                                            </td>
                                            <td class="py-2.5 px-3 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $r->users_count > 0 ? 'bg-primary-soft text-primary' : 'bg-surface-raised text-text-muted border border-border' }}">
                                                    {{ $r->users_count }} akun
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-3 text-center">
                                                @if (in_array($r->name, ['Super Admin', 'Guru', 'Operator', 'Siswa', 'Orang Tua']))
                                                    <span class="text-[10px] font-medium text-text-muted">Sistem</span>
                                                @else
                                                    <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">Kustom</span>
                                                @endif
                                            </td>
                                            <td class="py-2.5 px-3 text-right">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    @if ($r->name !== 'Super Admin')
                                                        <button type="button" 
                                                                wire:click="openEditRole({{ $r->id }})" 
                                                                class="p-1 rounded text-text-muted hover:text-primary hover:bg-surface-raised transition-colors" 
                                                                title="Ubah Nama">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                            </svg>
                                                        </button>
                                                        @if ($r->users_count === 0 && !in_array($r->name, ['Guru', 'Operator', 'Siswa', 'Orang Tua']))
                                                            <button type="button" 
                                                                    wire:click="deleteRole({{ $r->id }})" 
                                                                    wire:confirm="Yakin ingin menghapus role '{{ $r->name }}'?"
                                                                    class="p-1 rounded text-text-muted hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors" 
                                                                    title="Hapus Role">
                                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <!-- Form Input Nama Role -->
                    <form wire:submit="saveRole" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-text-main mb-1">
                                Nama Role <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   wire:model="roleName" 
                                   placeholder="Contoh: Instruktur Ekskul, Pembina OSIS, Pengawas..."
                                   class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                            <p class="text-[10px] text-text-muted mt-1">Nama role unik yang akan digunakan untuk penugasan akun dan matriks perizinan.</p>
                            @error('roleName')
                                <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-border">
                            <button type="button" 
                                    wire:click="openManageRoles" 
                                    class="text-xs text-text-muted hover:text-primary transition-colors flex items-center gap-1">
                                &larr; Kembali ke Daftar Role
                            </button>
                            <div class="flex items-center gap-2">
                                <button type="button" 
                                        wire:click="closeRoleModal" 
                                        class="btn btn-secondary px-4 py-2 text-xs font-semibold">
                                    Batal
                                </button>
                                <button type="submit" 
                                        class="btn btn-primary px-5 py-2 text-xs font-bold shadow-xs flex items-center gap-1.5"
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="saveRole">Simpan Role</span>
                                    <span wire:loading wire:target="saveRole">Menyimpan...</span>
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif
</div>
