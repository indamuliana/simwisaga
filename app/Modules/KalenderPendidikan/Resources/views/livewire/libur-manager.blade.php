<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main flex items-center gap-2">
                <svg class="w-7 h-7 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Pengaturan Hari Libur
            </h1>
            <p class="text-sm text-text-muted mt-1">
                Kelola hari libur nasional, cuti bersama, serta libur khusus sekolah (libur semester, jeda KBM, awal puasa).
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button type="button" 
                    wire:click="syncNational" 
                    wire:loading.attr="disabled"
                    class="btn btn-secondary inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium">
                <svg wire:loading class="w-4 h-4 animate-spin text-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                <svg wire:loading.remove class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                Sinkronisasi Libur Nasional
            </button>

            <button type="button" 
                    wire:click="openCreateModal" 
                    class="btn btn-primary inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold shadow-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Tambah Libur Sekolah
            </button>

            <a href="{{ route('kalender.index') }}" class="btn btn-secondary px-3 py-2 text-sm font-medium">
                Kembali ke Kalender
            </a>
        </div>
    </div>

    {{-- Filter Tahun --}}
    <div class="card p-3 bg-surface border border-border rounded-xl flex items-center gap-3">
        <label class="text-xs font-semibold text-text-muted">Filter Tahun:</label>
        <select wire:model.live="filterTahun" class="input input-sm w-36">
            <option value="">Semua Tahun</option>
            @for($y = (int) date('Y') - 1; $y <= (int) date('Y') + 2; $y++)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
    </div>

    {{-- Tabel Hari Libur --}}
    <div class="card overflow-hidden bg-surface border border-border rounded-xl shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-hover/70 border-b border-border text-xs uppercase text-text-muted">
                    <tr>
                        <th class="px-4 py-3">Nama Hari Libur</th>
                        <th class="px-4 py-3 w-48">Rentang Tanggal</th>
                        <th class="px-4 py-3 w-32">Jenis Libur</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3 w-28 text-center">Libur KBM</th>
                        <th class="px-4 py-3 w-28 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($liburs as $item)
                    <tr class="hover:bg-surface-hover/40 transition-colors">
                        <td class="px-4 py-3 font-semibold text-text-main">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full {{ $item->jenis === 'sekolah' ? 'bg-amber-500' : 'bg-rose-500' }}"></span>
                                {{ $item->nama }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-text-secondary whitespace-nowrap font-medium">
                            {{ $item->tgl_mulai->translatedFormat('d M Y') }}
                            @if($item->tgl_mulai->format('Y-m-d') !== $item->tgl_selesai->format('Y-m-d'))
                                &mdash; {{ $item->tgl_selesai->translatedFormat('d M Y') }}
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold uppercase 
                                {{ $item->jenis === 'sekolah' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' }}">
                                {{ str_replace('_', ' ', $item->jenis) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-text-muted">
                            {{ $item->keterangan ?: '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($item->is_kbm_libur)
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Ya</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">Tidak</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-1">
                            <button type="button" wire:click="editLibur({{ $item->id }})" class="btn btn-secondary px-2.5 py-1 text-xs">
                                Edit
                            </button>
                            <button type="button" wire:confirm="Yakin ingin menghapus data libur ini?" wire:click="deleteLibur({{ $item->id }})" class="btn btn-secondary px-2.5 py-1 text-xs text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-text-muted">
                            Belum ada data hari libur pada tahun ini. Silakan klik tombol Sinkronisasi atau Tambah Libur.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Tambah / Edit Libur --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-md bg-surface border border-border rounded-2xl shadow-xl overflow-hidden p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between pb-3 border-b border-border">
                <h3 class="text-base font-bold text-text-main">
                    {{ $isEditing ? 'Edit Hari Libur' : 'Tambah Hari Libur Sekolah' }}
                </h3>
                <button type="button" wire:click="$set('showModal', false)" class="text-text-muted hover:text-text-main p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-text-muted mb-1">Nama Hari Libur <span class="text-rose-500">*</span></label>
                    <input type="text" wire:model="nama" placeholder="Contoh: Libur Semester Ganjil" class="input w-full">
                    @error('nama') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-text-muted mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                        <input type="date" wire:model="tgl_mulai" class="input w-full">
                        @error('tgl_mulai') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-muted mb-1">Tanggal Selesai <span class="text-rose-500">*</span></label>
                        <input type="date" wire:model="tgl_selesai" class="input w-full">
                        @error('tgl_selesai') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-muted mb-1">Jenis Libur</label>
                    <select wire:model="jenis" class="input w-full">
                        <option value="sekolah">Libur Khusus Sekolah</option>
                        <option value="nasional">Hari Libur Nasional</option>
                        <option value="cuti_bersama">Cuti Bersama</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-muted mb-1">Keterangan Tambahan</label>
                    <textarea wire:model="keterangan" rows="2" placeholder="SKB 3 Menteri atau Surat Keputusan Kepala Sekolah..." class="input w-full py-2"></textarea>
                </div>

                <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-text-main">
                    <input type="checkbox" wire:model="is_kbm_libur" class="checkbox">
                    KBM Ditiadakan (Hari Efektif Sekolah Libur)
                </label>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                    <button type="button" wire:click="$set('showModal', false)" class="btn btn-secondary px-4 py-2 text-xs">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 py-2 text-xs font-semibold">
                        Simpan Libur
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
