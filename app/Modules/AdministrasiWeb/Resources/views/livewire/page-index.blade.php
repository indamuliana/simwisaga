<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span>📄</span>
                <span>Manajemen Halaman Kustom</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola halaman statis mandiri seperti Sejarah Sekolah, Visi &amp; Misi, Struktur Organisasi, dan Kebijakan Mutu.
            </p>
        </div>

        <a href="{{ route('web-admin.pages.create') }}" class="btn-primary inline-flex items-center gap-2 self-start sm:self-auto px-4 py-2 text-sm rounded-xl">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Buat Halaman Baru</span>
        </a>
    </div>

    @if (session()->has('message'))
    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-sm text-emerald-800 dark:text-emerald-300 flex items-center justify-between">
        <span>✓ {{ session('message') }}</span>
        <button type="button" wire:click="$refresh" class="text-emerald-600 dark:text-emerald-400 font-bold">&times;</button>
    </div>
    @endif

    <!-- Search Bar -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="relative max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari judul atau slug halaman..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Judul Halaman</th>
                        <th class="py-3 px-4">Slug / URL Publik</th>
                        <th class="py-3 px-4 w-28 text-center">Status</th>
                        <th class="py-3 px-4 w-36 text-center">Diperbarui</th>
                        <th class="py-3 px-4 w-36 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($pages as $p)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-3 px-4 font-semibold text-slate-900 dark:text-white">
                            {{ $p->judul }}
                        </td>
                        <td class="py-3 px-4 font-mono text-xs">
                            <a href="{{ url('/halaman/' . $p->slug) }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1">
                                <span>/halaman/{{ $p->slug }}</span>
                                <span>↗</span>
                            </a>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <button type="button" wire:click="toggleActive({{ $p->id }})" class="text-xs font-semibold px-2.5 py-1 rounded-full transition-colors {{ $p->is_active ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 hover:bg-emerald-200' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-400 hover:bg-slate-300' }}">
                                {{ $p->is_active ? 'Aktif' : 'Draft' }}
                            </button>
                        </td>
                        <td class="py-3 px-4 text-center text-xs text-slate-500 dark:text-slate-400">
                            {{ $p->updated_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-3 px-4 text-right space-x-2">
                            <a href="{{ route('web-admin.pages.edit', $p->id) }}" class="text-xs text-blue-600 dark:text-blue-400 font-bold hover:underline">
                                Edit
                            </a>
                            <button type="button" wire:click="delete({{ $p->id }})" wire:confirm="Hapus halaman kustom ini?" class="text-xs text-rose-600 dark:text-rose-400 font-bold hover:underline">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-500 dark:text-slate-400 text-sm">
                            Belum ada halaman kustom. Klik "Buat Halaman Baru" untuk memulai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pages->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $pages->links() }}
        </div>
        @endif
    </div>
</div>
