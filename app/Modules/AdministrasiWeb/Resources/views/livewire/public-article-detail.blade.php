<div>
    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/95 dark:bg-[#071324]/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between gap-4">
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('images/landing/wikrama-logo.png') }}" alt="Logo" class="h-9 w-auto">
                <div>
                    <span class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-none block">SMK Wikrama 1 Garut</span>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold">Portal Berita &amp; Prestasi Resmi</span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                    <span>&larr; Beranda Utama</span>
                </a>
                <button type="button" @click="toggleTheme()" class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-slate-900 dark:hover:text-white">
                    <span x-show="darkMode" x-cloak>☀️</span>
                    <span x-show="!darkMode">🌙</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
        
        <!-- Breadcrumb & Category -->
        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-4">
            <a href="{{ url('/') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
            <span>&rsaquo;</span>
            <a href="{{ url('/#berita') }}" class="hover:text-blue-600 transition-colors">Berita</a>
            <span>&rsaquo;</span>
            @if ($article->category)
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                {{ $article->category->nama }}
            </span>
            @endif
        </div>

        <!-- Article Headline -->
        <h1 class="text-2xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-[1.2]">
            {{ $article->judul }}
        </h1>

        <!-- Author & Meta Info Strip -->
        <div class="flex flex-wrap items-center justify-between gap-4 py-5 my-6 border-y border-slate-200/80 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-400">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-950/80 border border-blue-200 dark:border-blue-800 flex items-center justify-center font-bold text-blue-700 dark:text-blue-300">
                    {{ strtoupper(substr($article->penulis_nama ?: 'HW', 0, 2)) }}
                </div>
                <div>
                    <span class="font-bold text-slate-900 dark:text-white block">{{ $article->penulis_nama ?: ($article->author?->name ?? 'Humas Wikrama') }}</span>
                    <span class="text-[11px]">{{ $article->published_at ? $article->published_at->isoFormat('D MMMM Y • HH:mm') : date('d M Y') }} WIB</span>
                </div>
            </div>

            <div class="flex items-center gap-4 text-[11px] font-mono">
                <span>⏱️ {{ $article->readingTime() }}</span>
                <span>&bull;</span>
                <span>👁️ {{ number_format($article->view_count) }} kali dibaca</span>
            </div>
        </div>

        <!-- Cover Image -->
        @if ($article->gambar_sampul)
        <div class="rounded-3xl overflow-hidden aspect-[16/9] bg-slate-950 shadow-2xl mb-10 border border-slate-200/60 dark:border-slate-800">
            <img src="{{ $article->gambarSampulUrl() }}" alt="{{ $article->judul }}" class="w-full h-full object-cover">
        </div>
        @endif

        <!-- Article Excerpt Highlight (if available) -->
        @if ($article->ringkasan)
        <div class="p-5 sm:p-6 rounded-2xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200/80 dark:border-blue-900/50 mb-8">
            <p class="text-base sm:text-lg font-medium text-blue-950 dark:text-blue-200 leading-relaxed italic">
                &ldquo;{{ $article->ringkasan }}&rdquo;
            </p>
        </div>
        @endif

        <!-- Article Body -->
        <article class="prose prose-slate dark:prose-invert max-w-none text-base sm:text-lg text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line space-y-4">
            {{ $article->konten }}
        </article>

        <!-- Share Actions Strip -->
        <div class="mt-12 pt-6 border-t border-slate-200/80 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4"
             x-data="{ copied: false }">
            <span class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Bagikan Berita Ini:</span>
            <div class="flex items-center gap-2">
                <a href="https://api.whatsapp.com/send?text={{ urlencode($article->judul . ' ' . url()->current()) }}" target="_blank" rel="noopener" class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition-colors inline-flex items-center gap-1.5">
                    <span>WhatsApp</span>
                </a>
                <button type="button" 
                        @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2500)"
                        class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 transition-colors inline-flex items-center gap-1.5">
                    <span x-text="copied ? 'Tersalin! ✅' : 'Salin Tautan 🔗'"></span>
                </button>
            </div>
        </div>

        <!-- Related Articles -->
        @if ($relatedArticles->isNotEmpty())
        <div class="mt-16 pt-10 border-t border-slate-200 dark:border-slate-800">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Berita Lainnya dari SMK Wikrama</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach ($relatedArticles as $rel)
                <a href="{{ url('/berita/' . $rel->slug) }}" class="group block p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-blue-500 transition-all shadow-xs">
                    <div class="aspect-[16/10] rounded-xl overflow-hidden bg-slate-950 mb-3">
                        <img src="{{ $rel->gambarSampulUrl() }}" alt="{{ $rel->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 block mb-1">
                        {{ $rel->category?->nama ?? 'Berita' }}
                    </span>
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white line-clamp-2 leading-snug group-hover:text-blue-600 transition-colors">
                        {{ $rel->judul }}
                    </h4>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 text-slate-400 text-xs py-8 border-t border-white/10 mt-16 text-center">
        <p>&copy; {{ date('Y') }} SMK Wikrama 1 Garut &bull; Learning in Indonesia to be Best for the World.</p>
    </footer>
</div>
