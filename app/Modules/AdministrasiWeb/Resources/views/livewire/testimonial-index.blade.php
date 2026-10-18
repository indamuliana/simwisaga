<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span>💬</span>
                <span>Suara Komunitas &amp; Testimoni</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kurasi ulasan, kisah sukses, dan kesan dari siswa, orang tua, alumni berprestasi, dan mitra dunia industri.
            </p>
        </div>

        <button type="button" wire:click="openCreateModal" class="btn-primary inline-flex items-center gap-2 self-start sm:self-auto px-4 py-2 text-sm rounded-xl">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Testimoni</span>
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
            Semua ({{ \App\Modules\AdministrasiWeb\Models\WebTestimonial::count() }})
        </button>
        <button type="button" wire:click="$set('filterCategory', 'Siswa Aktif')" class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all {{ $filterCategory === 'Siswa Aktif' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100' }}">
            🎓 Siswa Aktif
        </button>
        <button type="button" wire:click="$set('filterCategory', 'Orang Tua Siswa')" class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all {{ $filterCategory === 'Orang Tua Siswa' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100' }}">
            👨‍👩‍👧 Orang Tua Siswa
        </button>
        <button type="button" wire:click="$set('filterCategory', 'Alumni')" class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all {{ $filterCategory === 'Alumni' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100' }}">
            🚀 Alumni
        </button>
        <button type="button" wire:click="$set('filterCategory', 'Mitra Industri')" class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all {{ $filterCategory === 'Mitra Industri' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100' }}">
            🏢 Mitra Industri
        </button>
    </div>

    <!-- Table of Testimonials -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4 w-20 text-center">Urutan</th>
                        <th class="py-3 px-4 w-16">Foto</th>
                        <th class="py-3 px-4 w-52">Nama &amp; Peran</th>
                        <th class="py-3 px-4">Pertanyaan &amp; Kutipan Ulasan</th>
                        <th class="py-3 px-4 w-28 text-center">Status</th>
                        <th class="py-3 px-4 w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($testimonials as $t)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-3 px-4 text-center">
                            <div class="inline-flex items-center gap-1">
                                <button type="button" wire:click="moveUp({{ $t->id }})" title="Pindah ke atas" class="p-1 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500">
                                    ▲
                                </button>
                                <span class="font-mono font-bold text-xs px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $t->urutan }}
                                </span>
                                <button type="button" wire:click="moveDown({{ $t->id }})" title="Pindah ke bawah" class="p-1 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500">
                                    ▼
                                </button>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="w-10 h-10 rounded-full overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800">
                                <img src="{{ $t->imageUrl() }}" alt="{{ $t->name }}" class="w-full h-full object-cover">
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-mono-code font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 mb-1 border border-blue-200 dark:border-blue-800">
                                {{ $t->category }}
                            </span>
                            <h3 class="font-bold text-slate-900 dark:text-white leading-snug">
                                {{ $t->name }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $t->role }}
                            </p>
                        </td>
                        <td class="py-3 px-4">
                            @if($t->question)
                            <h4 class="font-bold text-xs text-slate-800 dark:text-slate-200 mb-1">
                                &ldquo;{{ $t->question }}&rdquo;
                            </h4>
                            @endif
                            <p class="text-xs text-slate-600 dark:text-slate-400 italic line-clamp-2">
                                &ldquo;{{ $t->quote }}&rdquo;
                            </p>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <button type="button" wire:click="toggleActive({{ $t->id }})" class="text-xs font-semibold px-2.5 py-1 rounded-full transition-colors {{ $t->is_active ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 hover:bg-emerald-200' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-400 hover:bg-slate-300' }}">
                                {{ $t->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </td>
                        <td class="py-3 px-4 text-right space-x-2">
                            <button type="button" wire:click="edit({{ $t->id }})" class="text-xs text-blue-600 dark:text-blue-400 font-bold hover:underline">
                                Edit
                            </button>
                            <button type="button" wire:click="delete({{ $t->id }})" wire:confirm="Hapus data testimoni ini?" class="text-xs text-rose-600 dark:text-rose-400 font-bold hover:underline">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500 dark:text-slate-400 text-sm">
                            Belum ada testimoni pada kategori ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Tambah / Edit Testimoni -->
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    {{ $editingId ? 'Edit Testimoni' : 'Tambah Testimoni Baru' }}
                </h3>
                <button type="button" wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Peran *</label>
                        <select wire:model="category" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="Siswa Aktif">🎓 Siswa Aktif</option>
                            <option value="Orang Tua Siswa">👨‍👩‍👧 Orang Tua Siswa</option>
                            <option value="Alumni">🚀 Alumni</option>
                            <option value="Mitra Industri">🏢 Mitra Industri</option>
                            <option value="Guru & Staf">👨‍🏫 Guru &amp; Tenaga Pendidik</option>
                        </select>
                        @error('category') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap *</label>
                        <input type="text" wire:model="name" placeholder="Contoh: Muhammad Fikri Ramadhan" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @error('name') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jabatan / Profesi / Jurusan</label>
                    <input type="text" wire:model="role" placeholder="Contoh: Siswa PPLG (Software Engineering) atau Senior Dev di Tokopedia" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @error('role') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Pertanyaan Pemicu (Header Testimoni)</label>
                    <input type="text" wire:model="question" placeholder="Contoh: Apa yang paling kamu sukai dari belajar di Wikrama?" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @error('question') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Isi Kutipan Testimoni *</label>
                    <textarea wire:model="quote" rows="4" placeholder="Tuliskan pengalaman nyata dan kesan mendalam..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                    @error('quote') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <!-- Foto Avatar Upload -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Foto Avatar / Profil</label>
                    <div class="flex items-center gap-4">
                        @if ($avatar_upload)
                        <div class="w-12 h-12 rounded-full overflow-hidden border border-slate-300 bg-slate-100">
                            <img src="{{ $avatar_upload->temporaryUrl() }}" class="w-full h-full object-cover">
                        </div>
                        @elseif ($existing_image)
                        <div class="w-12 h-12 rounded-full overflow-hidden border border-slate-300 bg-slate-100">
                            <img src="{{ Str::startsWith($existing_image, ['http://', 'https://']) ? $existing_image : asset($existing_image) }}" class="w-full h-full object-cover">
                        </div>
                        @endif

                        <div class="flex-1">
                            <input type="file" wire:model="avatar_upload" accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-[11px] text-slate-500 mt-0.5">JPG, PNG, atau WebP. Maks 2MB.</p>
                        </div>
                    </div>
                    @error('avatar_upload') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
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
                        Simpan Testimoni
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
