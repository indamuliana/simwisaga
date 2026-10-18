<div class="py-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('web-admin.pages.index') }}" class="p-2 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    {{ $pageId ? 'Edit Halaman Kustom' : 'Buat Halaman Kustom Baru' }}
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Halaman statis mandiri untuk profil, visi-misi, atau informasi kelembagaan.
                </p>
            </div>
        </div>

        @if($pageId && $slug)
        <a href="{{ url('/halaman/' . $slug) }}" target="_blank" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
            <span>Lihat Halaman Publik</span>
            <span>↗</span>
        </a>
        @endif
    </div>

    <!-- Form Card -->
    <form wire:submit.prevent="save" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
        
        <!-- Judul -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Judul Halaman *</label>
            <input type="text" wire:model.live.debounce.300ms="judul" placeholder="Contoh: Visi, Misi &amp; Tujuan SMK Wikrama 1 Garut" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-base font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
            @error('judul') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Slug -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Slug URL *</label>
            <div class="flex items-center">
                <span class="px-3.5 py-2.5 text-xs font-mono text-slate-500 bg-slate-100 dark:bg-slate-800 border border-r-0 border-slate-300 dark:border-slate-700 rounded-l-xl select-none">
                    {{ url('/halaman') }}/
                </span>
                <input type="text" wire:model="slug" placeholder="visi-misi" class="w-full px-3 py-2.5 text-xs font-mono rounded-r-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            @error('slug') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Ringkasan -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Ringkasan / Sub-header Halaman</label>
            <textarea wire:model="ringkasan" rows="2" placeholder="Deskripsi singkat yang tampil tepat di bawah judul halaman..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
            @error('ringkasan') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Banner Upload -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Foto Banner / Header (Opsional)</label>
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                @if ($banner_upload)
                <div class="w-36 h-20 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800">
                    <img src="{{ $banner_upload->temporaryUrl() }}" class="w-full h-full object-cover">
                </div>
                @elseif ($gambar_banner)
                <div class="w-36 h-20 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800">
                    <img src="{{ Str::startsWith($gambar_banner, ['http://', 'https://']) ? $gambar_banner : asset('storage/' . $gambar_banner) }}" class="w-full h-full object-cover">
                </div>
                @endif

                <div class="flex-1">
                    <input type="file" wire:model="banner_upload" accept="image/*" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <span wire:loading wire:target="banner_upload" class="text-xs text-blue-600 ml-2">Mengunggah...</span>
                    <p class="text-[11px] text-slate-500 mt-1">Format JPG, PNG, atau WebP. Maksimal 2MB.</p>
                </div>
            </div>
            @error('banner_upload') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Konten Halaman -->
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Isi Konten Lengkap (Mendukung HTML)</label>
                <span class="text-[11px] text-slate-400 font-mono">Bisa menggunakan tag &lt;p&gt;, &lt;h2&gt;, &lt;ul&gt;, &lt;strong&gt;, &lt;table&gt;</span>
            </div>
            <textarea wire:model="konten" rows="12" placeholder="Tuliskan isi lengkap halaman di sini..." class="w-full font-mono text-xs px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
            @error('konten') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Meta Deskripsi -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Meta Deskripsi (Untuk SEO Mesin Pencari)</label>
            <input type="text" wire:model="meta_deskripsi" placeholder="Deskripsi 1 kalimat untuk Google search snippet..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
            @error('meta_deskripsi') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Status Aktif -->
        <div class="pt-2">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model="is_active" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Publikasikan halaman ini (Bisa diakses publik)</span>
            </label>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <a href="{{ route('web-admin.pages.index') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">
                Batal
            </a>
            <button type="submit" wire:loading.attr="disabled" class="btn-primary px-6 py-2.5 text-xs rounded-xl font-bold flex items-center gap-2">
                <span wire:loading.remove>Simpan Halaman</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
