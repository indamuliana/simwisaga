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
                Atur izin akses fitur (Lihat, Tambah, Edit, Hapus, Cetak, dan Aksi Khusus) yang dikelompokkan sesuai 3-tier menu sistem.
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
        <div x-data="{ 
                 barHeight: 60,
                 updateHeight() { 
                     if (this.$refs.stickyBar) {
                         this.barHeight = this.$refs.stickyBar.offsetHeight;
                     }
                 } 
             }" 
             x-init="updateHeight(); $nextTick(() => updateHeight()); setTimeout(() => updateHeight(), 200)"
             @resize.window="updateHeight()"
             class="space-y-4">
            <style>
                @media (min-width: 768px) {
                    .hak-akses-table-container {
                        overflow: visible !important;
                    }
                }
            </style>

            <!-- STICKY ACTION & SAVE BAR (Tetap terlihat saat skrol ke bawah) -->
            <div x-ref="stickyBar"
                 id="hak-akses-sticky-bar" 
                 class="sticky z-30 bg-surface/95 dark:bg-surface/90 backdrop-blur-md px-4 py-2.5 rounded-2xl border border-border shadow-md flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 transition-all"
                 style="position: sticky; top: 64px; z-index: 30;">
                <!-- Info Role Aktif & Scope -->
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs font-bold text-text-main">Role: <span class="text-primary font-black">{{ $selectedRole->name }}</span></span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary border border-primary/20">
                            {{ count($rolePermissions) }} Izin Aktif
                        </span>
                    </div>

                    <!-- Scope Selector Inline -->
                    <div class="flex items-center gap-1.5 pl-2 border-l border-border">
                        <label class="text-[11px] font-semibold text-text-secondary whitespace-nowrap">Scope Data:</label>
                        <select wire:model.live="roleScopes.{{ $selectedRole->id }}" class="text-xs font-semibold py-1 px-2 rounded-lg border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                            @foreach ($availableScopes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Toolbar Pencarian & Tombol Aksi Simpan -->
                <div class="flex flex-wrap items-center gap-2 justify-end">
                    <!-- Search Input -->
                    <div class="relative w-48 sm:w-60">
                        <input type="text"
                            wire:model.live.debounce.250ms="search"
                            placeholder="Cari kelompok, modul, fitur..."
                            class="w-full pl-8 pr-3 py-1.5 rounded-lg text-xs border border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                        <svg class="w-3.5 h-3.5 text-text-secondary absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <!-- Quick Action Buttons -->
                    <button type="button"
                        wire:click="selectAllForRole"
                        title="Pilih seluruh hak akses untuk role ini"
                        class="px-2.5 py-1.5 rounded-lg border border-border bg-surface hover:bg-surface-hover text-text-main text-xs font-semibold transition-colors flex items-center gap-1">
                        <span>☑️</span>
                        <span class="hidden sm:inline">Pilih Semua</span>
                    </button>
                    <button type="button"
                        wire:click="clearAllForRole"
                        wire:confirm="Yakin ingin mengosongkan seluruh hak akses untuk role ini?"
                        title="Kosongkan seluruh hak akses"
                        class="px-2.5 py-1.5 rounded-lg border border-rose-200 dark:border-rose-900 bg-rose-50/50 dark:bg-rose-950/30 hover:bg-rose-100 text-rose-700 dark:text-rose-300 text-xs font-semibold transition-colors flex items-center gap-1">
                        <span>⏹️</span>
                        <span class="hidden sm:inline">Kosongkan</span>
                    </button>

                    <!-- Tombol Simpan Perubahan -->
                    <button wire:click="save" 
                            class="px-4 py-1.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition-all flex items-center gap-1.5 shrink-0" 
                            wire:loading.attr="disabled">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                </div>
            </div>

            @php
                $groupIcons = [
                    'ANALISIS' => '📊',
                    'KURIKULUM' => '🎓',
                    'KESISWAAN' => '👥',
                    'KEPESANTRENAN' => '🌙',
                    'KEPEGAWAIAN & SDM' => '👔',
                    'SARANA & PRASARANA' => '🏢',
                    'PENGATURAN' => '⚙️',
                    'Portal Siswa' => '📱',
                ];
            @endphp

            <!-- TABEL MATRIKS PERMISSION DENGAN STICKY THEAD -->
            <div class="bg-surface rounded-2xl border border-border shadow-xs">
                <div class="hak-akses-table-container overflow-x-auto">
                    <table class="w-full divide-y divide-border text-xs border-collapse">
                        <thead class="sticky z-20 bg-surface shadow-xs border-b border-border"
                               :style="'position: sticky; top: ' + (64 + barHeight) + 'px; z-index: 20;'">
                            <tr class="text-left text-text-secondary uppercase text-[10px] font-bold tracking-wider">
                                <th class="py-3 px-4 w-72 bg-surface border-b border-border"
                                    :style="'position: sticky; top: ' + (64 + barHeight) + 'px; z-index: 20;'">Modul / Fitur</th>
                                @foreach ($standardActions as $stdAct)
                                    <th class="py-3 px-3 text-center w-20 capitalize font-mono bg-surface border-b border-border"
                                        :style="'position: sticky; top: ' + (64 + barHeight) + 'px; z-index: 20;'">{{ $stdAct }}</th>
                                @endforeach
                                <th class="py-3 px-4 text-center min-w-44 bg-surface border-b border-border"
                                    :style="'position: sticky; top: ' + (64 + barHeight) + 'px; z-index: 20;'">Aksi Khusus</th>
                                <th class="py-3 px-3 text-center w-28 bg-surface border-b border-border"
                                    :style="'position: sticky; top: ' + (64 + barHeight) + 'px; z-index: 20;'">Pilih Baris</th>
                            </tr>
                        </thead>
                    <tbody class="divide-y divide-border font-medium">
                        @php 
                            $currentGroup = ''; 
                            $currentModule = ''; 
                        @endphp
                        @forelse ($filteredMatrix as $resource => $def)
                            <!-- 1. Header Kelompok Utama (Tier 1) -->
                            @if (($def['group'] ?? 'LAINNYA') !== $currentGroup)
                                @php 
                                    $currentGroup = $def['group'] ?? 'LAINNYA'; 
                                    $currentModule = ''; // Reset module saat ganti kelompok
                                @endphp
                                <tr class="bg-primary/5 dark:bg-primary/10 border-t-4 border-primary/40 font-black">
                                    <td colspan="8" class="py-3 px-4 text-xs uppercase tracking-wider text-text-main">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                            <div class="flex items-center gap-2">
                                                <span class="text-base">{{ $groupIcons[$currentGroup] ?? '📁' }}</span>
                                                <span class="font-extrabold tracking-wide text-primary text-xs sm:text-sm">KELOMPOK {{ $currentGroup }}</span>
                                            </div>
                                            <button type="button"
                                                wire:click="toggleAllForGroup('{{ $currentGroup }}')"
                                                class="text-[11px] font-bold text-primary hover:text-white hover:bg-primary transition-all px-3 py-1 rounded-lg border border-primary/30 bg-surface flex items-center gap-1 self-start sm:self-auto shadow-2xs">
                                                <span>⚡</span>
                                                <span>Toggle Semua Akses Kelompok {{ $currentGroup }}</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endif

                            <!-- 2. Header Modul (Tier 2) -->
                            @if ($def['module'] !== $currentModule)
                                @php $currentModule = $def['module']; @endphp
                                <tr class="bg-surface-hover/70 font-bold border-t border-border text-text-main">
                                    <td colspan="8" class="py-2.5 px-4 text-xs tracking-wider">
                                        <div class="flex items-center justify-between pl-3 border-l-2 border-primary/50">
                                            <div class="flex items-center gap-2">
                                                <span class="text-text-muted">↳</span>
                                                <span class="font-bold text-text-main text-xs uppercase tracking-wider">Modul: {{ $currentModule }}</span>
                                            </div>
                                            <button type="button"
                                                wire:click="toggleAllForModule('{{ $currentModule }}')"
                                                class="text-[10px] font-semibold text-text-secondary hover:text-primary hover:bg-surface px-2.5 py-0.5 rounded border border-border bg-surface/50 transition-all">
                                                Toggle Akses Modul Ini
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endif

                            <!-- 3. Baris Fitur / Resource (Tier 3) -->
                            <tr class="hover:bg-surface-hover/40 transition-colors">
                                <!-- Nama Fitur & Resource Key -->
                                <td class="py-3 px-4 pl-8">
                                    <div class="font-bold text-text-main text-xs">{{ $def['label'] }}</div>
                                    <div class="text-[10px] text-text-secondary font-mono mt-0.5">{{ $resource }}</div>
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

                                <!-- Kolom Aksi Khusus (Dinamis untuk import, ekspor, kelola, setting, dll) -->
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
                                        Pilih Baris
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-text-muted text-xs">
                                    Tidak ada modul atau fitur yang cocok dengan kata kunci pencarian "{{ $search }}".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Kelola & Buat/Edit Role -->
    @if ($showRoleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl bg-surface border border-border shadow-2xl overflow-hidden p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-sm font-bold text-text-main">
                        @if ($roleModalMode === 'create')
                            Buat Role Baru
                        @elseif ($roleModalMode === 'edit')
                            Ubah Nama Role
                        @else
                            Kelola Master Role
                        @endif
                    </h3>
                    <button type="button" wire:click="closeRoleModal" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                @if ($roleModalMode === 'manage')
                    <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                        @foreach ($roles as $r)
                            <div class="flex items-center justify-between p-2.5 rounded-xl border border-border bg-surface-hover/30">
                                <div>
                                    <span class="text-xs font-bold text-text-main">{{ $r->name }}</span>
                                    <span class="text-[10px] text-text-muted ml-2">({{ $r->users_count }} pengguna)</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    @if (!in_array($r->name, ['Super Admin']))
                                        <button type="button" wire:click="openEditRole({{ $r->id }})" class="p-1 text-text-secondary hover:text-primary rounded">
                                            ✏️
                                        </button>
                                        <button type="button" wire:click="deleteRole({{ $r->id }})" wire:confirm="Hapus role '{{ $r->name }}'?" class="p-1 text-text-secondary hover:text-rose-600 rounded">
                                            🗑️
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="pt-2 border-t border-border flex justify-between">
                        <button type="button" wire:click="openCreateRole" class="btn btn-primary text-xs">
                            + Tambah Role
                        </button>
                        <button type="button" wire:click="closeRoleModal" class="btn btn-secondary text-xs">
                            Tutup
                        </button>
                    </div>
                @else
                    <form wire:submit.prevent="saveRole" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-text-main mb-1">Nama Role</label>
                            <input type="text" wire:model="roleName" placeholder="Misal: Koordinator Lab" class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                            @error('roleName') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex justify-end gap-2 pt-2 border-t border-border">
                            <button type="button" wire:click="openManageRoles" class="btn btn-secondary text-xs">Kembali</button>
                            <button type="submit" class="btn btn-primary text-xs">Simpan Role</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif
</div>
