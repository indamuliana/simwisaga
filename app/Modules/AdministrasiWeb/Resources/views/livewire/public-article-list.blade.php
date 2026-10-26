<div>
    <!-- Hero / Headline Area -->
    <section class="py-12 sm:py-16 bg-gradient-to-b from-blue-50/50 via-white to-transparent dark:from-slate-900/50 dark:via-[#060D1A] dark:to-transparent border-b border-slate-200/80 dark:border-slate-800/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 mb-4">
                <span>📰</span>
                <span>Kabar Wikrama Garut</span>
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white tracking-tight">
                Arsip Berita &amp; Prestasi Terkini
            </h1>
            <p class="mt-3 text-sm sm:text-base text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                Eksplorasi seluruh dokumentasi kegiatan, capaian siswa, inovasi teknologi pembelajaran, dan informasi resmi SMK Wikrama 1 Garut.
            </p>

            <!-- Search Bar & Filters -->
            <div class="mt-8 max-w-xl mx-auto flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari topik berita, prestasi..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-sm">
                </div>

                @if($search || $kategori)
                <button type="button" wire:click="$set('search', ''); $set('kategori', '');" class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    Reset Filter
                </button>
                @endif
            </div>

            <!-- Categories Badges -->
            @if(count($categories) > 0)
            <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                <button type="button" wire:click="$set('kategori', '')" class="px-3 py-1 rounded-full text-xs font-semibold transition-all {{ empty($kategori) ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                    Semua Kategori
                </button>
                @foreach($categories as $cat)
                <button type="button" wire:click="selectKategori('{{ $cat->slug }}')" class="px-3 py-1 rounded-full text-xs font-semibold transition-all {{ $kategori === $cat->slug ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                    {{ $cat->nama }}
                </button>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    <!-- Articles Grid Section -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($articles->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            @foreach($articles as $art)
            <article class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <!-- Image -->
                    <div class="aspect-[16/10] bg-slate-100 dark:bg-slate-800 relative overflow-hidden">
                        <img src="{{ $art->gambarSampulUrl() }}" alt="{{ $art->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-bold bg-white/95 dark:bg-slate-900/95 text-blue-700 dark:text-blue-300 shadow-sm">
                            {{ $art->category?->nama ?? 'Berita' }}
                        </span>
                    </div>

                    <!-- Body -->
                    <div class="p-6">
                        <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 mb-2">
                            <span>📅 {{ $art->published_at ? $art->published_at->format('d M Y') : date('d M Y') }}</span>
                            <span>&bull;</span>
                            <span>⏱️ {{ $art->readingTime() }}</span>
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white leading-snug group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                            <a href="{{ url('/berita/' . $art->slug) }}">
                                {{ $art->judul }}
                            </a>
                        </h2>
                        <p class="mt-2 text-xs sm:text-sm text-slate-600 dark:text-slate-400 line-clamp-3 leading-relaxed">
                            {{ $art->ringkasan ?: Str::limit(strip_tags($art->konten), 120) }}
                        </p>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-800/20 flex items-center justify-between">
                    <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                        {{ $art->penulis_nama ?: ($art->author?->name ?? 'Humas Wikrama') }}
                    </span>
                    <a href="{{ url('/berita/' . $art->slug) }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                        <span>Baca Selengkapnya</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </article>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $articles->links() }}
        </div>
        @else
        <div class="text-center py-20 bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-300 dark:border-slate-800 max-w-xl mx-auto px-6">
            <span class="text-4xl block mb-3">🔍</span>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tidak Ada Berita Ditemukan</h3>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kata kunci atau kategori yang Anda pilih belum memiliki artikel terpublikasi.
            </p>
            <button type="button" wire:click="$set('search', ''); $set('kategori', '');" class="mt-4 px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition-colors">
                Tampilkan Semua Berita
            </button>
        </div>
        @endif
    </main>
</div>
