<div>
    <!-- Main Content Container -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
        
        <!-- Breadcrumb & Category -->
        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 mb-4">
            <a href="{{ url('/') }}" class="hover:text-[#00519E] transition-colors">Beranda</a>
            <span>&rsaquo;</span>
            <a href="{{ url('/berita') }}" class="hover:text-[#00519E] transition-colors">Berita</a>
            <span>&rsaquo;</span>
            @if ($article->category)
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EBF4FC] text-[#00519E] border border-blue-200">
                {{ $article->category->nama }}
            </span>
            @endif
        </div>

        <!-- Article Headline -->
        <h1 class="text-2xl sm:text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-[1.2]">
            {{ $article->judul }}
        </h1>

        <!-- Author & Meta Info Strip -->
        <div class="flex flex-wrap items-center justify-between gap-4 py-5 my-6 border-y border-slate-200 text-xs text-slate-600">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#EBF4FC] border border-[#00519E]/20 flex items-center justify-center font-bold text-[#00519E]">
                    {{ strtoupper(substr($article->penulis_nama ?: 'HW', 0, 2)) }}
                </div>
                <div>
                    <span class="font-bold text-slate-900 block">{{ $article->penulis_nama ?: ($article->author?->name ?? 'Humas Wikrama') }}</span>
                    <span class="text-[11px] text-slate-500">{{ $article->published_at ? $article->published_at->isoFormat('D MMMM Y • HH:mm') : date('d M Y') }} WIB</span>
                </div>
            </div>

            <div class="flex items-center gap-4 text-[11px] font-mono text-slate-500">
                <span>⏱️ {{ $article->readingTime() }}</span>
                <span>&bull;</span>
                <span>👁️ {{ number_format($article->view_count) }} kali dibaca</span>
            </div>
        </div>

        <!-- Cover Image -->
        @if ($article->gambar_sampul)
        <div class="rounded-3xl overflow-hidden aspect-[16/9] bg-slate-100 shadow-xl mb-10 border border-slate-200">
            <img src="{{ $article->gambarSampulUrl() }}" alt="{{ $article->judul }}" class="w-full h-full object-cover">
        </div>
        @endif

        <!-- Article Excerpt Highlight (if available) -->
        @if ($article->ringkasan)
        <div class="p-5 sm:p-6 rounded-2xl bg-[#EBF4FC]/80 border border-blue-200/80 mb-8">
            <p class="text-base sm:text-lg font-medium text-[#00305F] leading-relaxed italic">
                &ldquo;{{ $article->ringkasan }}&rdquo;
            </p>
        </div>
        @endif

        <!-- Article Body -->
        <article class="prose prose-slate max-w-none text-base sm:text-lg text-slate-800 leading-relaxed whitespace-pre-line space-y-4">
            {{ $article->konten }}
        </article>

        <!-- Share Actions Strip -->
        <div class="mt-12 pt-6 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4"
             x-data="{ copied: false }">
            <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Bagikan Berita Ini:</span>
            <div class="flex items-center gap-2">
                <a href="https://api.whatsapp.com/send?text={{ urlencode($article->judul . ' ' . url()->current()) }}" target="_blank" rel="noopener" class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-[#1F513F] text-white hover:bg-[#163c2f] transition-colors inline-flex items-center gap-1.5 shadow-2xs">
                    <span>WhatsApp</span>
                </a>
                <button type="button" 
                        @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2500)"
                        class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 transition-colors inline-flex items-center gap-1.5">
                    <span x-text="copied ? 'Tersalin! ✅' : 'Salin Tautan 🔗'"></span>
                </button>
            </div>
        </div>

        <!-- Related Articles -->
        @if ($relatedArticles->isNotEmpty())
        <div class="mt-16 pt-10 border-t border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-6">Berita Lainnya dari SMK Wikrama</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach ($relatedArticles as $rel)
                <a href="{{ url('/berita/' . $rel->slug) }}" class="group block p-4 rounded-2xl bg-white border border-slate-200 hover:border-[#00519E] hover:shadow-md transition-all">
                    <div class="aspect-[16/10] rounded-xl overflow-hidden bg-slate-100 mb-3">
                        <img src="{{ $rel->gambarSampulUrl() }}" alt="{{ $rel->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <span class="text-[10px] font-bold text-[#00519E] block mb-1">
                        {{ $rel->category?->nama ?? 'Berita' }}
                    </span>
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 line-clamp-2 leading-snug group-hover:text-[#00519E] transition-colors">
                        {{ $rel->judul }}
                    </h4>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </main>
</div>
