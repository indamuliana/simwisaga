<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main flex items-center gap-2">
                <svg class="w-7 h-7 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                Kategori Kegiatan Kalender
            </h1>
            <p class="text-sm text-text-muted mt-1">
                Kelola kategori agenda dinamis (Kurikulum, Asesmen, Kesiswaan, Kunjungan Tamu, dll) beserta warna penanda.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" wire:click="openCreateModal" class="btn btn-primary inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold shadow-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Tambah Kategori
            </button>
            <a href="{{ route('kalender.index') }}" class="btn btn-secondary px-3 py-2 text-sm font-medium">
                Kembali ke Kalender
            </a>
        </div>
    </div>

    {{-- Tabel Kategori --}}
    <div class="card overflow-hidden bg-surface border border-border rounded-xl shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-hover/70 border-b border-border text-xs uppercase text-text-muted">
                    <tr>
                        <th class="px-4 py-3 w-16">Urutan</th>
                        <th class="px-4 py-3">Nama Kategori</th>
                        <th class="px-4 py-3 w-32">Warna Penanda</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3 w-28 text-center">Jumlah Agenda</th>
                        <th class="px-4 py-3 w-24 text-center">Status</th>
                        <th class="px-4 py-3 w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($kategoris as $item)
                    <tr class="hover:bg-surface-hover/40 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-text-muted">{{ $item->urutan }}</td>
                        <td class="px-4 py-3 font-semibold text-text-main">
                            {{ $item->nama }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-mono font-medium text-white shadow-xs" style="background-color: {{ $item->warna }}">
                                <span class="w-2 h-2 rounded-full bg-white"></span>
                                {{ $item->warna }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-text-secondary">
                            {{ $item->deskripsi ?: '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-surface-hover text-text-muted">
                                {{ $item->agendas_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" wire:click="toggleActive({{ $item->id }})" class="cursor-pointer">
                                @if($item->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                        Nonaktif
                                    </span>
                                @endif
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right space-x-1">
                            <button type="button" wire:click="editKategori({{ $item->id }})" class="btn btn-secondary px-2.5 py-1 text-xs">
                                Edit
                            </button>
                            <button type="button" wire:confirm="Yakin ingin menghapus kategori ini?" wire:click="deleteKategori({{ $item->id }})" class="btn btn-secondary px-2.5 py-1 text-xs text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-text-muted">
                            Belum ada kategori kegiatan yang dibuat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Tambah / Edit Kategori --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-md bg-surface border border-border rounded-2xl shadow-xl overflow-hidden p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between pb-3 border-b border-border">
                <h3 class="text-base font-bold text-text-main">
                    {{ $isEditing ? 'Edit Kategori Kegiatan' : 'Tambah Kategori Baru' }}
                </h3>
                <button type="button" wire:click="$set('showModal', false)" class="text-text-muted hover:text-text-main p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-text-muted mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" wire:model="nama" placeholder="Contoh: Asesmen & Ujian" class="input w-full">
                    @error('nama') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-muted mb-1">Pilih Warna Penanda <span class="text-rose-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <input type="color" wire:model="warna" class="w-12 h-10 rounded-lg cursor-pointer border border-border p-1 bg-surface">
                        <input type="text" wire:model="warna" class="input flex-1 font-mono uppercase text-xs" placeholder="#014694">
                    </div>
                    @error('warna') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-muted mb-1">Urutan Tampilan</label>
                    <input type="number" wire:model="urutan" min="0" class="input w-full">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-muted mb-1">Deskripsi / Catatan</label>
                    <textarea wire:model="deskripsi" rows="2" placeholder="Keterangan cakupan kategori..." class="input w-full py-2"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                    <button type="button" wire:click="$set('showModal', false)" class="btn btn-secondary px-4 py-2 text-xs">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 py-2 text-xs font-semibold">
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
