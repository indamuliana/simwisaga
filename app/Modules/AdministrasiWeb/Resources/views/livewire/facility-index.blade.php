<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span>🏢</span>
                <span>Manajemen Fasilitas &amp; Lab Praktik</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola foto, spesifikasi, dan deskripsi ruang laboratorium, studio, dan infrastruktur kampus yang tampil di landing page.
            </p>
        </div>

        <button type="button" wire:click="openCreateModal" class="btn-primary inline-flex items-center gap-2 self-start sm:self-auto px-4 py-2 text-sm rounded-xl">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Fasilitas</span>
        </button>
    </div>

    @if (session()->has('message'))
    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-sm text-emerald-800 dark:text-emerald-300 flex items-center justify-between">
        <span>✓ {{ session('message') }}</span>
        <button type="button" wire:click="$refresh" class="text-emerald-600 dark:text-emerald-400 font-bold">&times;</button>
    </div>
    @endif

    <!-- Category Filter Tabs -->
    <div class="flex flex-wrap items-center gap-2">
        <button type="button" wire:click="$set('filterCategory', 'all')" class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all {{ $filterCategory === 'all' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100' }}">
            Semua ({{ \App\Modules\AdministrasiWeb\Models\WebFacility::count() }})
        </button>
        <button type="button" wire:click="$set('filterCategory', 'lab')" class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all {{ $filterCategory === 'lab' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100' }}">
            💻 Lab Komputer &amp; Coding
        </button>
        <button type="button" wire:click="$set('filterCategory', 'kelas')" class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all {{ $filterCategory === 'kelas' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100' }}">
            📱 Kelas Digital &amp; TeFa
        </button>
        <button type="button" wire:click="$set('filterCategory', 'gedung')" class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all {{ $filterCategory === 'gedung' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100' }}">
            🏫 Gedung &amp; Kawasan
        </button>
    </div>

    <!-- Table of Facilities -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4 w-20 text-center">Urutan</th>
                        <th class="py-3 px-4 w-28">Foto</th>
                        <th class="py-3 px-4">Nama &amp; Badge</th>
                        <th class="py-3 px-4">Kategori &amp; Highlight</th>
                        <th class="py-3 px-4 w-28 text-center">Status</th>
                        <th class="py-3 px-4 w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($facilities as $f)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-3 px-4 text-center">
                            <div class="inline-flex items-center gap-1">
                                <button type="button" wire:click="moveUp({{ $f->id }})" title="Pindah ke atas" class="p-1 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500">
                                    ▲
                                </button>
                                <span class="font-mono font-bold text-xs px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $f->urutan }}
                                </span>
                                <button type="button" wire:click="moveDown({{ $f->id }})" title="Pindah ke bawah" class="p-1 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500">
                                    ▼
                                </button>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="w-20 h-14 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800">
                                <img src="{{ $f->imageUrl() }}" alt="{{ $f->title }}" class="w-full h-full object-cover">
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            @if($f->badge)
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-mono-code font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 mb-1 border border-blue-200 dark:border-blue-800">
                                {{ $f->badge }}
                            </span>
                            @endif
                            <h3 class="font-bold text-slate-900 dark:text-white leading-snug">
                                {{ $f->title }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1 mt-0.5">
                                {{ $f->desc }}
                            </p>
                        </td>
                        <td class="py-3 px-4 text-xs">
                            <span class="font-medium text-slate-700 dark:text-slate-300 block">
                                {{ $f->cat_label ?: ucfirst($f->category) }}
                            </span>
                            @if($f->highlight)
                            <span class="font-mono text-[11px] text-amber-600 dark:text-amber-400 block mt-0.5">
                                ✨ {{ $f->highlight }}
                            </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <button type="button" wire:click="toggleActive({{ $f->id }})" class="text-xs font-semibold px-2.5 py-1 rounded-full transition-colors {{ $f->is_active ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 hover:bg-emerald-200' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-400 hover:bg-slate-300' }}">
                                {{ $f->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </td>
                        <td class="py-3 px-4 text-right space-x-2">
                            <button type="button" wire:click="edit({{ $f->id }})" class="text-xs text-blue-600 dark:text-blue-400 font-bold hover:underline">
                                Edit
                            </button>
                            <button type="button" wire:click="delete({{ $f->id }})" wire:confirm="Hapus data fasilitas ini?" class="text-xs text-rose-600 dark:text-rose-400 font-bold hover:underline">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500 dark:text-slate-400 text-sm">
                            Belum ada data fasilitas pada kategori ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Tambah / Edit Fasilitas -->
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    {{ $editingId ? 'Edit Fasilitas Kampus' : 'Tambah Fasilitas Baru' }}
                </h3>
                <button type="button" wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Tab *</label>
                        <select wire:model.live="category" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="lab">💻 Lab Komputer &amp; Coding</option>
                            <option value="kelas">📱 Kelas Digital &amp; TeFa</option>
                            <option value="gedung">🏫 Gedung &amp; Adiwiyata</option>
                            <option value="aktivitas">🌟 Ekosistem Kampus</option>
                            <option value="umum">🏛️ Fasilitas Umum</option>
                        </select>
                        @error('category') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Label Kategori</label>
                        <input type="text" wire:model="cat_label" placeholder="Contoh: Lab Komputer &amp; Coding" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @error('cat_label') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Fasilitas *</label>
                        <input type="text" wire:model="title" placeholder="Contoh: Laboratorium Komputer PPLG" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @error('title') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Badge Pojok Kiri Atas</label>
                        <input type="text" wire:model="badge" placeholder="Contoh: AI &amp; Software Studio" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @error('badge') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Highlight Spesifikasi / Kunci</label>
                    <input type="text" wire:model="highlight" placeholder="Contoh: Spesifikasi Core i7 / GPU Dedicated, Fiber Dedicated..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @error('highlight') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Lengkap Fasilitas</label>
                    <textarea wire:model="desc" rows="3" placeholder="Penjelasan lengkap tentang peralatan, pemanfaatan oleh siswa, dan standar industri..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                    @error('desc') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <!-- Foto Upload -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Foto Fasilitas</label>
                    <div class="flex items-center gap-4">
                        @if ($image_upload)
                        <div class="w-20 h-14 rounded-lg overflow-hidden border border-slate-300 bg-slate-100">
                            <img src="{{ $image_upload->temporaryUrl() }}" class="w-full h-full object-cover">
                        </div>
                        @elseif ($existing_image)
                        <div class="w-20 h-14 rounded-lg overflow-hidden border border-slate-300 bg-slate-100">
                            <img src="{{ Str::startsWith($existing_image, ['http://', 'https://']) ? $existing_image : asset($existing_image) }}" class="w-full h-full object-cover">
                        </div>
                        @endif

                        <div class="flex-1">
                            <input type="file" wire:model="image_upload" accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-[11px] text-slate-500 mt-0.5">JPG, PNG, atau WebP. Maks 2MB.</p>
                        </div>
                    </div>
                    @error('image_upload') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Urutan</label>
                        <input type="number" wire:model="urutan" min="0" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div class="flex items-center pt-5">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Tampilkan di Landing Page</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary px-5 py-2 text-xs rounded-xl font-bold">
                        Simpan Fasilitas
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
