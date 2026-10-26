<div>
    @if ($page->gambar_banner)
    <div class="w-full max-h-80 overflow-hidden relative border-b border-slate-200">
        <img src="{{ $page->gambarBannerUrl() }}" alt="{{ $page->judul }}" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 via-transparent to-transparent"></div>
    </div>
    @endif

    <!-- Main Container -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
            <a href="{{ url('/') }}" class="hover:text-[#00519E] transition-colors">Beranda</a>
            <span>&rsaquo;</span>
            <span class="text-slate-800 font-semibold">Halaman</span>
            <span>&rsaquo;</span>
            <span class="text-[#00519E] font-semibold truncate max-w-xs">{{ $page->judul }}</span>
        </nav>

        <!-- Title -->
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight leading-tight">
            {{ $page->judul }}
        </h1>

        @if ($page->ringkasan)
        <p class="mt-4 text-base sm:text-xl text-slate-700 font-medium leading-relaxed border-l-4 border-[#00519E] pl-4 py-1 bg-[#EBF4FC]/40 rounded-r-xl">
            {{ $page->ringkasan }}
        </p>
        @endif

        <div class="my-8 border-b border-slate-200"></div>

        <!-- Article / Page Body -->
        <article class="prose prose-slate max-w-none text-slate-800 leading-relaxed text-sm sm:text-base space-y-4">
            {!! $page->konten !!}
        </article>

        <!-- Bottom Navigation & CTA -->
        <div class="mt-16 pt-8 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ url('/') }}" class="text-xs font-bold text-[#00519E] hover:text-[#00305F] hover:underline inline-flex items-center gap-2">
                <span>&larr;</span>
                <span>Kembali ke Halaman Beranda</span>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ url('/berita') }}" class="text-xs font-bold px-4 py-2 rounded-full border border-slate-300 text-slate-700 hover:bg-slate-100 transition-colors">
                    Arsip Berita &rarr;
                </a>
                <a href="{{ url('/#kontak') }}" class="btn-jis-pill-navy text-xs !py-2 !px-5 font-bold shadow-xs">
                    Hubungi Sekolah
                </a>
            </div>
        </div>
    </main>
</div>
