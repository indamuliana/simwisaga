<div class="space-y-5">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-text-main">Setting Program Pendidikan</h1>
            <p class="text-sm text-text-secondary">Pengaturan program pendidikan (misal: Reguler dan Unggulan) yang terhubung ke plotting dan data siswa.</p>
        </div>
        @can('akademik.program.tambah')
        <button wire:click="create" class="btn btn-primary inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>+ Tambah Program</span>
        </button>
        @endcan
    </div>

    <!-- Alert Status -->
    @if (session('status'))
    <div class="rounded-lg border border-success bg-success-soft px-4 py-3 text-sm text-success flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span>{{ session('status') }}</span>
    </div>
    @endif

    @if (session('error'))
    <div class="rounded-lg border border-rose-300 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/40 px-4 py-3 text-sm text-rose-700 dark:text-rose-300 flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Search & Quick Filter -->
    <div class="card p-3 flex flex-col sm:flex-row gap-3 items-center justify-between">
        <div class="w-full sm:w-72">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kode atau nama program..." class="input w-full text-xs">
        </div>
        <div class="text-xs text-text-secondary">
            Total Program: <span class="font-bold text-text-main">{{ count($programs) }}</span>
        </div>
    </div>

    <!-- Tabel Program -->
    <div class="card overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                <thead class="bg-surface-hover text-left bg-surface/60">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-text-secondary text-xs uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-3 font-semibold text-text-secondary text-xs uppercase tracking-wider">Nama Program</th>
                        <th class="px-4 py-3 font-semibold text-text-secondary text-xs uppercase tracking-wider">Keterangan / Deskripsi</th>
                        <th class="px-4 py-3 text-center font-semibold text-text-secondary text-xs uppercase tracking-wider">Jumlah Siswa</th>
                        <th class="px-4 py-3 text-center font-semibold text-text-secondary text-xs uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-text-secondary text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($programs as $p)
                    <tr class="hover:bg-surface-hover dark:hover:bg-gray-800/40 transition">
                        <td class="whitespace-nowrap px-4 py-3 font-mono font-bold text-primary dark:text-blue-400">
                            {{ $p->kode }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-text-main">
                            {{ $p->nama }}
                        </td>
                        <td class="px-4 py-3 text-xs text-text-secondary">
                            {{ $p->deskripsi ?: '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-text-main border border-slate-200 dark:border-slate-700">
                                {{ number_format($p->siswas_count, 0, ',', '.') }} Siswa
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @can('akademik.program.edit')
                            <button type="button" wire:click="toggleActive({{ $p->id }})" title="Klik untuk mengubah status"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold transition cursor-pointer {{ $p->is_active ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-200' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400 border border-slate-300 dark:border-slate-600 hover:bg-slate-300' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $p->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                <span>{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </button>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $p->is_active ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $p->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                <span>{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </span>
                            @endcan
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-2">
                                @can('akademik.program.edit')
                                <button type="button" wire:click="edit({{ $p->id }})" class="px-2.5 py-1 text-xs font-medium text-primary hover:bg-primary/10 rounded transition">
                                    Edit
                                </button>
                                @endcan

                                @can('akademik.program.hapus')
                                <button type="button" wire:click="delete({{ $p->id }})" wire:confirm="Yakin ingin menghapus program '{{ $p->nama }}'? Program tidak dapat dihapus jika masih ada siswa yang terhubung." class="px-2.5 py-1 text-xs font-medium text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded transition">
                                    Hapus
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-text-secondary">
                            Belum ada data program yang terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah / Edit Program -->
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4">
        <div class="card w-full max-w-md space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-border pb-3">
                <h2 class="text-lg font-bold text-text-main">{{ $programId ? 'Edit Program' : 'Tambah Program Baru' }}</h2>
                <button type="button" wire:click="$set('showModal', false)" class="text-gray-400 hover:text-text-main text-2xl font-bold">&times;</button>
            </div>

            <form wire:submit="save" class="space-y-4 text-sm">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-text-main mb-1">
                        Kode Program <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" wire:model="kode" placeholder="Misal: REG, UNG" class="input w-full uppercase font-mono" maxlength="20">
                    <p class="text-[11px] text-text-secondary mt-1">Gunakan kode singkat unik, contoh: REG, UNG, TAHFIDZ.</p>
                    @error('kode') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-text-main mb-1">
                        Nama Program <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" wire:model="nama" placeholder="Misal: Reguler, Unggulan" class="input w-full" maxlength="100">
                    @error('nama') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-text-main mb-1">
                        Keterangan / Deskripsi
                    </label>
                    <textarea wire:model="deskripsi" rows="3" placeholder="Deskripsi program pendidikan..." class="input w-full text-xs"></textarea>
                    @error('deskripsi') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="is_active" wire:model="is_active" class="rounded border-border text-primary focus:ring-primary h-4 w-4">
                    <label for="is_active" class="text-xs font-medium text-text-main cursor-pointer select-none">
                        Program Aktif (Dapat dipilih saat alokasi/plotting siswa)
                    </label>
                </div>

                <div class="flex justify-end gap-2 border-t border-border pt-4">
                    <button type="button" wire:click="$set('showModal', false)" class="btn btn-secondary text-xs">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary text-xs" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">{{ $programId ? 'Simpan Perubahan' : 'Tambah Program' }}</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
