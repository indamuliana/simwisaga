<div class="space-y-6">
    @include('sarpras::partials.sub-nav', ['active' => 'sumber'])

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
                   placeholder="Cari kode sumber (4 karakter) atau nama sumber dana..." 
                   class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-border bg-bg text-text-main focus:ring-2 focus:ring-primary">
            <svg class="w-4 h-4 absolute left-3 top-2.5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        </div>

        @can('sarpras.sumber.tambah')
        <button type="button" wire:click="create" class="px-3.5 py-2 rounded-xl bg-primary text-white hover:bg-primary-hover text-xs font-semibold flex items-center gap-1.5 transition shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah Sumber Dana
        </button>
        @endcan
    </div>

    {{-- Tabel Sumber Dana --}}
    <div class="bg-surface border border-border rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-hover/60 border-b border-border text-text-muted font-medium uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Kode Sumber (4 Huruf/Angka)</th>
                        <th class="py-3 px-4">Nama Sumber Anggaran</th>
                        <th class="py-3 px-4 text-center">Jumlah Aset</th>
                        <th class="py-3 px-4">Keterangan</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($sumbers as $s)
                        <tr class="hover:bg-surface-hover/50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-primary tracking-widest text-sm">
                                <span class="px-2 py-0.5 rounded-md bg-primary-soft text-primary">{{ $s->kode }}</span>
                            </td>
                            <td class="py-3 px-4 font-bold text-text-main text-sm">{{ $s->nama }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                    {{ $s->asets_count }} aset
                                </span>
                            </td>
                            <td class="py-3 px-4 text-text-muted">{{ $s->keterangan ?: '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $s->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                    {{ $s->is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @can('sarpras.sumber.edit')
                                    <button type="button" wire:click="edit({{ $s->id }})" class="p-1 rounded-lg hover:bg-surface-hover text-text-muted hover:text-amber-600 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @endcan

                                    @can('sarpras.sumber.hapus')
                                    <button type="button" 
                                            wire:confirm="Yakin ingin menghapus sumber dana '{{ $s->nama }}' ({{ $s->kode }})?"
                                            wire:click="delete({{ $s->id }})" 
                                            class="p-1 rounded-lg hover:bg-surface-hover text-text-muted hover:text-rose-600 transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-text-muted">Belum ada data sumber dana.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sumbers->hasPages())
            <div class="p-4 border-t border-border">
                {{ $sumbers->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Tambah / Edit Sumber Dana --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface border border-border rounded-2xl w-full max-w-md p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-bold text-text-main">
                        {{ $isEdit ? 'Edit Sumber Dana' : 'Tambah Sumber Dana Baru' }}
                    </h3>
                    <button type="button" wire:click="$set('showModal', false)" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                <form wire:submit="save" class="space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-text-main mb-1">Kode Sumber (4 Huruf/Angka) <span class="text-rose-500">*</span></label>
                        <input type="text" 
                               wire:model.live="kode" 
                               maxlength="4" 
                               class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3 font-mono font-bold uppercase tracking-widest" 
                               placeholder="BS26">
                        <span class="text-[11px] text-text-muted block mt-0.5">Wajib 4 karakter alfanumerik. Contoh: BS25, BS26, YYSN, KMTE, HIBA.</span>
                        @error('kode') <span class="text-rose-500 text-[11px] block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-text-main mb-1">Nama Sumber Anggaran <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="nama" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" placeholder="Contoh: BOSP Reguler 2026">
                        @error('nama') <span class="text-rose-500 text-[11px] block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-text-main mb-1">Keterangan / Rincian</label>
                        <textarea wire:model="keterangan" rows="2" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" placeholder="Catatan sumber anggaran..."></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="is_active" wire:model="is_active" class="rounded border-border text-primary focus:ring-primary">
                        <label for="is_active" class="text-text-main font-medium">Sumber Dana Aktif</label>
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
