<div>
    @if ($page->gambar_banner)
    <div class="w-full max-h-80 overflow-hidden relative border-b border-slate-200 dark:border-slate-800">
        <img src="{{ $page->gambarBannerUrl() }}" alt="{{ $page->judul }}" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-[#060D1A]/80 via-transparent to-transparent"></div>
    </div>
    @endif

    <!-- Main Container -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-6">
            <a href="{{ url('/') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
            <span>&rsaquo;</span>
            <span class="text-slate-900 dark:text-white font-medium">Halaman</span>
            <span>&rsaquo;</span>
            <span class="text-blue-600 dark:text-blue-400 font-medium truncate max-w-xs">{{ $page->judul }}</span>
        </nav>

        <!-- Title -->
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
            {{ $page->judul }}
        </h1>

        @if ($page->ringkasan)
        <p class="mt-4 text-base sm:text-xl text-slate-600 dark:text-slate-300 font-medium leading-relaxed border-l-4 border-blue-600 pl-4 py-1">
            {{ $page->ringkasan }}
        </p>
        @endif

        <div class="my-8 border-b border-slate-200/80 dark:border-slate-800"></div>

        <!-- Article / Page Body -->
        <article class="prose prose-slate dark:prose-invert max-w-none text-slate-800 dark:text-slate-200 leading-relaxed text-sm sm:text-base space-y-4">
            {!! $page->konten !!}
        </article>

        <!-- Bottom Navigation & CTA -->
        <div class="mt-16 pt-8 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ url('/') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-2">
                <span>&larr;</span>
                <span>Kembali ke Halaman Beranda</span>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ url('/berita') }}" class="text-xs font-semibold px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    Arsip Berita &rarr;
                </a>
                <a href="{{ url('/#kontak') }}" class="btn-primary text-xs !py-2 !px-4 rounded-xl font-bold">
                    Hubungi Sekolah
                </a>
            </div>
        </div>
    </main>
</div>
