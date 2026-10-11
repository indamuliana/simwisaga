<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Mata Pelajaran (Mapel)</h1>
            <p class="text-sm text-text-secondary">Kelola data mata pelajaran dan kelompok muatan pembelajaran secara dinamis untuk penilaian dan rapot.</p>
        </div>
        <div>
            @if($activeTab === 'mapel')
                @can('akademik.mapel.tambah')
                    <button wire:click="create" class="btn btn-primary inline-flex items-center gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Mapel</span>
                    </button>
                @endcan
            @else
                @can('akademik.mapel.tambah')
                    <button wire:click="createKelompok" class="btn btn-primary inline-flex items-center gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Kelompok</span>
                    </button>
                @endcan
            @endif
        </div>
    </div>

    <!-- Alert Notifications -->
    @if (session('status'))
        <div class="p-4 text-sm text-emerald-800 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 text-sm text-rose-800 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 rounded-xl border border-rose-200 dark:border-rose-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="border-b border-border">
        <nav class="flex space-x-6" aria-label="Tabs">
            <button
                type="button"
                wire:click="switchTab('mapel')"
                class="py-3 px-1 text-sm font-semibold border-b-2 transition-all flex items-center gap-2 {{ $activeTab === 'mapel' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main hover:border-border' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Daftar Mata Pelajaran</span>
                <span class="ml-1.5 px-2 py-0.5 text-xs rounded-full {{ $activeTab === 'mapel' ? 'bg-primary-soft text-primary' : 'bg-surface-hover text-text-muted' }}">
                    {{ $mapels->count() }}
                </span>
            </button>
            <button
                type="button"
                wire:click="switchTab('kelompok')"
                class="py-3 px-1 text-sm font-semibold border-b-2 transition-all flex items-center gap-2 {{ $activeTab === 'kelompok' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main hover:border-border' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Kelompok Mapel</span>
                <span class="ml-1.5 px-2 py-0.5 text-xs rounded-full {{ $activeTab === 'kelompok' ? 'bg-primary-soft text-primary' : 'bg-surface-hover text-text-muted' }}">
                    {{ $kelompokMapels->count() }}
                </span>
            </button>
        </nav>
    </div>

    @if($activeTab === 'mapel')
        <!-- TAB 1: DAFTAR MATA PELAJARAN -->
        <!-- Search & Filter Card -->
        <div class="p-4 card flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau kode mata pelajaran..." class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
            </div>
            <div class="sm:w-72">
                <select wire:model.live="filterKelompok" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                    <option value="">-- Semua Kelompok Mapel --</option>
                    @foreach($activeKelompokList as $km)
                        <option value="{{ $km->id }}">[{{ $km->kode }}] {{ $km->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Table Card Mapel -->
        <div class="card p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-sm">
                    <thead class="bg-surface-hover bg-surface/50 text-text-secondary font-medium">
                        <tr>
                            <th class="px-6 py-3 w-16 text-center">Urutan</th>
                            <th class="px-6 py-3">Kode</th>
                            <th class="px-6 py-3">Nama Mata Pelajaran</th>
                            <th class="px-6 py-3">Kelompok Mapel</th>
                            <th class="px-6 py-3 text-center">KKM Default</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-text-main">
                        @forelse($mapels as $m)
                            <tr class="hover:bg-surface-hover/50 dark:hover:bg-slate-800/30 transition">
                                <td class="px-6 py-4 text-center font-mono font-bold text-text-secondary">{{ $m->urutan }}</td>
                                <td class="px-6 py-4 font-mono font-semibold text-primary dark:text-indigo-400">{{ $m->kode }}</td>
                                <td class="px-6 py-4 font-medium text-text-main">{{ $m->nama }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $kodeKelompok = $m->kelompokMapel?->kode ?? '';
                                        $badgeColor = match(strtoupper($kodeKelompok)) {
                                            'A' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                            'B' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                            'C' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                            'D' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 border-purple-200 dark:border-purple-800',
                                            default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-300 dark:border-slate-700'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium border {{ $badgeColor }}">
                                        @if($m->kelompokMapel)
                                            <span class="font-bold mr-1">[{{ $m->kelompokMapel->kode }}]</span> {{ $m->kelompokMapel->nama }}
                                        @else
                                            {{ $m->kelompok_label }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center font-semibold text-text-main">{{ $m->kkm_default }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($m->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-text-secondary bg-surface">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    @can('akademik.mapel.edit')
                                        <button wire:click="edit({{ $m->id }})" class="text-xs font-medium text-primary hover:text-indigo-800 dark:text-indigo-400">Edit</button>
                                    @endcan
                                    @can('akademik.mapel.hapus')
                                        <button wire:click="delete({{ $m->id }})" wire:confirm="Hapus mapel {{ $m->nama }}?" class="text-xs font-medium text-rose-600 hover:text-rose-800 dark:text-rose-400">Hapus</button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-text-secondary">Tidak ada data mata pelajaran yang sesuai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <!-- TAB 2: KELOMPOK MAPEL -->
        <!-- Search Card Kelompok -->
        <div class="p-4 card flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" wire:model.live.debounce.300ms="searchKelompok" placeholder="Cari nama atau kode kelompok..." class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
            </div>
        </div>

        <!-- Table Card Kelompok Mapel -->
        <div class="card p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-sm">
                    <thead class="bg-surface-hover bg-surface/50 text-text-secondary font-medium">
                        <tr>
                            <th class="px-6 py-3 w-20 text-center">Urutan Rapor</th>
                            <th class="px-6 py-3 w-32">Kode Header</th>
                            <th class="px-6 py-3">Nama Kelompok</th>
                            <th class="px-6 py-3 text-center">Jumlah Mapel</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-text-main">
                        @forelse($kelompokMapels as $km)
                            <tr class="hover:bg-surface-hover/50 dark:hover:bg-slate-800/30 transition">
                                <td class="px-6 py-4 text-center font-mono font-bold text-text-secondary">{{ $km->urutan }}</td>
                                <td class="px-6 py-4 font-mono font-semibold text-primary dark:text-indigo-400">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-primary-soft text-primary font-bold">
                                        {{ $km->kode }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium text-text-main">
                                    {{ $km->nama }}
                                    <span class="block text-xs text-text-secondary mt-0.5">Tampil di rapot sebagai: <strong>{{ $km->kode }}. {{ $km->nama }}</strong></span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-text-main">
                                        {{ $km->mapels_count }} Mapel
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($km->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-text-secondary bg-surface">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    @can('akademik.mapel.edit')
                                        <button wire:click="editKelompok({{ $km->id }})" class="text-xs font-medium text-primary hover:text-indigo-800 dark:text-indigo-400">Edit</button>
                                    @endcan
                                    @can('akademik.mapel.hapus')
                                        <button wire:click="deleteKelompok({{ $km->id }})" wire:confirm="Hapus kelompok '{{ $km->nama }}'?" class="text-xs font-medium text-rose-600 hover:text-rose-800 dark:text-rose-400">Hapus</button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-text-secondary">Tidak ada data kelompok mapel.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- MODAL FORM MAPEL -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-lg bg-surface rounded-2xl shadow-xl border border-border overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex justify-between items-center">
                    <h2 class="text-lg font-bold text-text-main">
                        {{ $mapelId ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' }}
                    </h2>
                    <button wire:click="$set('showModal', false)" class="text-text-secondary hover:text-text-main">&times;</button>
                </div>
                <form wire:submit="save" class="p-6 space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-text-main mb-1">Kode Mapel</label>
                            <input type="text" wire:model="kode" placeholder="Misal: BINDO" class="w-full text-sm uppercase font-mono rounded-lg border-border bg-surface text-text-main">
                            @error('kode') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-text-main mb-1">Nama Mata Pelajaran</label>
                            <input type="text" wire:model="nama" placeholder="Misal: Bahasa Indonesia" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                            @error('nama') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1">Kelompok Mapel</label>
                        <select wire:model.live="kelompok_mapel_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                            <option value="">-- Pilih Kelompok Mapel --</option>
                            @foreach($activeKelompokList as $km)
                                <option value="{{ $km->id }}">[{{ $km->kode }}] {{ $km->nama }}</option>
                            @endforeach
                        </select>
                        @error('kelompok_mapel_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-text-main mb-1">KKM Default</label>
                            <input type="number" min="0" max="100" wire:model="kkm_default" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                            @error('kkm_default') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-main mb-1">
                                Nomor Urut Tampil
                                <span class="text-[10px] text-text-muted font-normal">(Dalam Kelompok)</span>
                            </label>
                            <input type="number" min="1" wire:model="urutan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                            @error('urutan') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="rounded border-border text-primary focus:ring-primary">
                            <span class="text-sm text-text-main">Aktif Digunakan</span>
                        </label>
                    </div>

                    <div class="pt-4 flex justify-end space-x-3 border-t border-border">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-sm text-text-secondary hover:bg-surface-hover rounded-lg">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Mapel</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL FORM KELOMPOK MAPEL -->
    @if($showKelompokModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-md bg-surface rounded-2xl shadow-xl border border-border overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex justify-between items-center">
                    <h2 class="text-lg font-bold text-text-main">
                        {{ $kelompokId ? 'Edit Kelompok Mapel' : 'Tambah Kelompok Mapel' }}
                    </h2>
                    <button wire:click="$set('showKelompokModal', false)" class="text-text-secondary hover:text-text-main">&times;</button>
                </div>
                <form wire:submit="saveKelompok" class="p-6 space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-text-main mb-1">Kode Header</label>
                            <input type="text" wire:model="kelompokKode" placeholder="Contoh: A" class="w-full text-sm uppercase font-mono rounded-lg border-border bg-surface text-text-main">
                            <p class="text-[10px] text-text-muted mt-0.5">Misal: A, B, C</p>
                            @error('kelompokKode') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-text-main mb-1">Nomor Urut Tampil</label>
                            <input type="number" min="1" wire:model="kelompokUrutan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                            <p class="text-[10px] text-text-muted mt-0.5">Urutan tampil di rapot & penilaian</p>
                            @error('kelompokUrutan') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1">Nama Kelompok</label>
                        <input type="text" wire:model="kelompokNama" placeholder="Contoh: Muatan Kejuruan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                        @error('kelompokNama') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="kelompokIsActive" class="rounded border-border text-primary focus:ring-primary">
                            <span class="text-sm text-text-main">Kelompok Aktif</span>
                        </label>
                    </div>

                    <div class="pt-4 flex justify-end space-x-3 border-t border-border">
                        <button type="button" wire:click="$set('showKelompokModal', false)" class="px-4 py-2 text-sm text-text-secondary hover:bg-surface-hover rounded-lg">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Kelompok</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
