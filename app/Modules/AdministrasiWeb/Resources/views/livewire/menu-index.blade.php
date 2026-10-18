<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span>🧭</span>
                <span>Manajemen Menu Navigasi</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola tautan menu pada header navbar, drawer seluler, dan tautan penting di footer landing page.
            </p>
        </div>

        <button type="button" wire:click="openCreateModal" class="btn-primary inline-flex items-center gap-2 self-start sm:self-auto px-4 py-2 text-sm rounded-xl">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Menu</span>
        </button>
    </div>

    @if (session()->has('message'))
    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-sm text-emerald-800 dark:text-emerald-300 flex items-center justify-between">
        <span>✓ {{ session('message') }}</span>
        <button type="button" wire:click="$refresh" class="text-emerald-600 dark:text-emerald-400 font-bold">&times;</button>
    </div>
    @endif

    <!-- Tabs Navigation -->
    <div class="border-b border-slate-200 dark:border-slate-800">
        <nav class="flex space-x-6" aria-label="Tabs">
            <button type="button" wire:click="$set('activeTab', 'header')" class="pb-3 text-sm font-semibold border-b-2 transition-colors {{ $activeTab === 'header' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400' }}">
                🌐 Header Navbar &amp; Drawer ({{ $activeTab === 'header' ? $menus->count() : \App\Modules\AdministrasiWeb\Models\WebMenu::where('lokasi', 'header')->count() }})
            </button>
            <button type="button" wire:click="$set('activeTab', 'footer')" class="pb-3 text-sm font-semibold border-b-2 transition-colors {{ $activeTab === 'footer' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400' }}">
                ⚓ Footer Links ({{ $activeTab === 'footer' ? $menus->count() : \App\Modules\AdministrasiWeb\Models\WebMenu::where('lokasi', 'footer')->count() }})
            </button>
        </nav>
    </div>

    <!-- Menu Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4 w-20 text-center">Urutan</th>
                        <th class="py-3 px-4">Label Teks</th>
                        <th class="py-3 px-4">Target URL / Anchor</th>
                        <th class="py-3 px-4 w-28 text-center">Buka Di</th>
                        <th class="py-3 px-4 w-28 text-center">Status</th>
                        <th class="py-3 px-4 w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($menus as $m)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-3 px-4 text-center">
                            <div class="inline-flex items-center gap-1">
                                <button type="button" wire:click="moveUp({{ $m->id }})" title="Pindah ke atas" class="p-1 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500">
                                    ▲
                                </button>
                                <span class="font-mono font-bold text-xs px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $m->urutan }}
                                </span>
                                <button type="button" wire:click="moveDown({{ $m->id }})" title="Pindah ke bawah" class="p-1 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500">
                                    ▼
                                </button>
                            </div>
                        </td>
                        <td class="py-3 px-4 font-semibold text-slate-900 dark:text-white">
                            {{ $m->label }}
                        </td>
                        <td class="py-3 px-4 font-mono text-xs text-blue-600 dark:text-blue-400">
                            {{ $m->url }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $m->target === '_blank' ? 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                                {{ $m->target === '_blank' ? 'Tab Baru ↗' : 'Tab Sama' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <button type="button" wire:click="toggleActive({{ $m->id }})" class="text-xs font-semibold px-2.5 py-1 rounded-full transition-colors {{ $m->is_active ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 hover:bg-emerald-200' : 'bg-rose-100 dark:bg-rose-950/80 text-rose-800 dark:text-rose-300 hover:bg-rose-200' }}">
                                {{ $m->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </td>
                        <td class="py-3 px-4 text-right space-x-2">
                            <button type="button" wire:click="edit({{ $m->id }})" class="text-xs text-blue-600 dark:text-blue-400 font-bold hover:underline">
                                Edit
                            </button>
                            <button type="button" wire:click="delete({{ $m->id }})" wire:confirm="Hapus tautan menu ini?" class="text-xs text-rose-600 dark:text-rose-400 font-bold hover:underline">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500 dark:text-slate-400 text-sm">
                            Belum ada tautan menu di bagian ini. Klik "Tambah Menu" di atas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Tambah / Edit Menu -->
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    {{ $editingId ? 'Edit Menu Navigasi' : 'Tambah Menu Baru' }} ({{ ucfirst($activeTab) }})
                </h3>
                <button type="button" wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Label Menu *</label>
                    <input type="text" wire:model="label" placeholder="Contoh: Program Keahlian, Visi Misi..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @error('label') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">URL / Anchor Tujuan *</label>
                    <input type="text" wire:model="url" placeholder="Contoh: #jurusan, /berita, /halaman/visi-misi, atau https://..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                        Tips: Gunakan awalan <code class="bg-slate-100 dark:bg-slate-800 px-1 py-0.5 rounded">#</code> untuk lompat seksi di landing page (misal: #jurusan, #filosofi, #suara-komunitas, #fasilitas, #berita, #rapor-mutu, #kontak).
                    </p>
                    @error('url') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Target Buka</label>
                        <select wire:model="target" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="_self">_self (Halaman yang sama)</option>
                            <option value="_blank">_blank (Buka tab baru)</option>
                        </select>
                        @error('target') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Urutan</label>
                        <input type="number" wire:model="urutan" min="0" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @error('urutan') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="menu_active" wire:model="is_active" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <label for="menu_active" class="text-xs font-medium text-slate-700 dark:text-slate-300">Tampilkan menu ini (Aktif)</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary px-4 py-2 text-xs rounded-xl font-semibold">
                        Simpan Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
