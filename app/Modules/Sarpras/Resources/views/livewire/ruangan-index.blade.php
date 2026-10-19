<div class="space-y-6">
    @include('sarpras::partials.sub-nav', ['active' => 'ruangan'])

    @if(session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm flex items-center justify-between">
            <span>{{ session('message') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-sm flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
        </div>
    @endif

    {{-- Filter & Action Bar --}}
    <div class="bg-surface border border-border rounded-2xl p-4 shadow-xs flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
        <div class="relative flex-1">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Cari kode ruangan, nama, gedung, lokasi..." 
                   class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-border bg-bg text-text-main focus:ring-2 focus:ring-primary">
            <svg class="w-4 h-4 absolute left-3 top-2.5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        </div>

        @can('sarpras.ruangan.tambah')
        <button type="button" wire:click="create" class="px-3.5 py-2 rounded-xl bg-primary text-white hover:bg-primary-hover text-xs font-semibold flex items-center gap-1.5 transition shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah Ruangan
        </button>
        @endcan
    </div>

    {{-- Tabel Ruangan --}}
    <div class="bg-surface border border-border rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-hover/60 border-b border-border text-text-muted font-medium uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Kode Ruangan</th>
                        <th class="py-3 px-4">Nama Ruangan</th>
                        <th class="py-3 px-4">Lokasi / Gedung</th>
                        <th class="py-3 px-4 text-center">Jumlah Aset</th>
                        <th class="py-3 px-4">Keterangan</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($ruangans as $r)
                        <tr class="hover:bg-surface-hover/50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-primary">{{ $r->kode_ruangan }}</td>
                            <td class="py-3 px-4 font-bold text-text-main">{{ $r->nama_ruangan }}</td>
                            <td class="py-3 px-4 text-text-secondary">{{ $r->lokasi ?: '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                    {{ $r->asets_count }} unit
                                </span>
                            </td>
                            <td class="py-3 px-4 text-text-muted">{{ $r->keterangan ?: '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $r->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                    {{ $r->is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @can('sarpras.ruangan.edit')
                                    <button type="button" wire:click="edit({{ $r->id }})" class="p-1 rounded-lg hover:bg-surface-hover text-text-muted hover:text-amber-600 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @endcan

                                    @can('sarpras.ruangan.hapus')
                                    <button type="button" 
                                            wire:confirm="Yakin ingin menghapus ruangan '{{ $r->nama_ruangan }}'?"
                                            wire:click="delete({{ $r->id }})" 
                                            class="p-1 rounded-lg hover:bg-surface-hover text-text-muted hover:text-rose-600 transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-text-muted">Belum ada data ruangan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ruangans->hasPages())
            <div class="p-4 border-t border-border">
                {{ $ruangans->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Tambah / Edit Ruangan --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface border border-border rounded-2xl w-full max-w-md p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-bold text-text-main">
                        {{ $isEdit ? 'Edit Ruangan' : 'Tambah Ruangan Baru' }}
                    </h3>
                    <button type="button" wire:click="$set('showModal', false)" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                <form wire:submit="save" class="space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-text-main mb-1">Kode Ruangan <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="kode_ruangan" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3 font-mono" placeholder="Contoh: LAB-KOMP-1">
                        @error('kode_ruangan') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-text-main mb-1">Nama Ruangan <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="nama_ruangan" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" placeholder="Contoh: Laboratorium Komputer 1">
                        @error('nama_ruangan') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-text-main mb-1">Lokasi / Gedung / Lantai</label>
                        <input type="text" wire:model="lokasi" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" placeholder="Contoh: Gedung B Lantai 2">
                    </div>

                    <div>
                        <label class="block font-medium text-text-main mb-1">Keterangan / Fungsi</label>
                        <textarea wire:model="keterangan" rows="2" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" placeholder="Deskripsi ruangan..."></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="is_active" wire:model="is_active" class="rounded border-border text-primary focus:ring-primary">
                        <label for="is_active" class="text-text-main font-medium">Ruangan Aktif</label>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 rounded-xl border border-border text-text-secondary hover:bg-surface-hover">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-primary-hover shadow-xs">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
