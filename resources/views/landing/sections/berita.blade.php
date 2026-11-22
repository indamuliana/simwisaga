        <!-- ── Babak 6: Campus Gazette (Warta Sekolah & Kabar Prestasi 4 Kolom x 2 Baris) ── -->
        @php
            $targetCount = 8;
            $dbArticles = class_exists(\App\Modules\AdministrasiWeb\Models\WebArticle::class)
                ? \Illuminate\Support\Facades\Cache::remember('landing.latest_articles', 300, function () use ($targetCount) {
                    return \App\Modules\AdministrasiWeb\Models\WebArticle::with('category')
                        ->published()
                        ->latest('published_at')
                        ->limit($targetCount)
                        ->get();
                })
                : collect();
            
            $articles = collect($dbArticles);
            if ($articles->count() < $targetCount) {
                $needed = $targetCount - $articles->count();
                $fallbackItems = collect($cfg['berita'] ?? [])->take($needed);
                $articles = $articles->concat($fallbackItems);
            }
        @endphp

        <section class="py-16 sm:py-24 bg-white text-slate-800 border-b border-slate-200/80 relative scroll-mt-20" id="berita">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <!-- Masthead Section -->
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200/80 pb-5 mb-8 sm:mb-10" data-animate="fade-up">
                    <div>
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-[#EBF4FC] text-[#002752] border border-[#002752]/15 mb-2.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#002752]"></span>
                            <span>Publikasi &amp; Prestasi Kampus</span>
                        </span>
                        <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                            Warta &amp; Kabar Prestasi Sekolah
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1.5 max-w-2xl font-normal">
                            Dokumentasi aktual capaian prestasi santri, inovasi pembelajaran vokasi, serta pengumuman resmi SMK Wikrama 1 Garut.
                        </p>
                    </div>
                    
                    <!-- Link Cepat di Header -->
                    <a href="{{ route('web.berita.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#002752] hover:text-[#014694] group whitespace-nowrap self-start sm:self-end pb-1 border-b-2 border-transparent hover:border-[#002752] transition-all">
                        <span>Lihat Semua Warta</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>

                <!-- 4 Kolom x 2 Baris Grid (8 Kartu Seragam) -->
                @if($articles->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-7 items-stretch" data-animate="fade-up" data-animate-delay="1">
                    @foreach($articles as $art)
                    @php
                        $isDb = is_object($art);
                        $artUrl = $isDb ? url('/berita/' . $art->slug) : ($art['url'] ?? route('web.berita.index'));
                        $artTitle = $isDb ? $art->judul : ($art['title'] ?? '');
                        $artImg = $isDb ? $art->gambarSampulUrl() : \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($art['image'] ?? null, 'images/web/achievement-lkbb.jpg');
                        $artBadge = $isDb ? ($art->category?->nama ?? 'Warta Sekolah') : ($art['badge'] ?? 'Prestasi');
                        $artDate = $isDb ? ($art->published_at ? $art->published_at->translatedFormat('d M Y') : date('d M Y')) : ($art['date'] ?? date('d M Y'));
                        $artDesc = $isDb ? ($art->ringkasan ?: Str::limit(strip_tags($art->konten), 110)) : ($art['desc'] ?? '');
                        $artReading = $isDb ? $art->readingTime() : '2 menit baca';
                    @endphp
                    <article class="group flex flex-col justify-between bg-[#FAF9F6] rounded-2xl border border-slate-200/90 overflow-hidden shadow-2xs hover:shadow-lg hover:border-[#002752]/30 hover:-translate-y-1 transition-all duration-300">
                        <div class="flex flex-col">
                            <!-- Thumbnail Gambar (16:10) -->
                            <a href="{{ $artUrl }}" class="block aspect-[16/10] w-full overflow-hidden bg-slate-100 relative">
                                <img src="{{ $artImg }}" 
                                     alt="{{ $artTitle }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                                     loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/30 via-transparent to-transparent pointer-events-none"></div>
                                <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#002752] text-white shadow-xs tracking-wide">
                                    {{ $artBadge }}
                                </span>
                            </a>

                            <!-- Konten Kartu -->
                            <div class="p-4 sm:p-5 flex flex-col">
                                <div class="flex items-center gap-2 text-[11px] text-slate-500 mb-2">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <time class="font-medium text-slate-600">{{ $artDate }}</time>
                                    <span class="text-slate-300">&bull;</span>
                                    <span class="text-slate-500 font-mono text-[10px]">{{ $artReading }}</span>
                                </div>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-[#002752] transition-colors line-clamp-2 leading-snug">
                                    <a href="{{ $artUrl }}">
                                        {{ $artTitle }}
                                    </a>
                                </h3>
                                <p class="text-xs text-slate-600 mt-2 line-clamp-2 leading-relaxed font-normal">
                                    {{ $artDesc }}
                                </p>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="px-4 sm:p-5 py-3 border-t border-slate-200/70 bg-white flex items-center justify-between mt-auto">
                            <a href="{{ $artUrl }}" class="text-xs font-bold text-[#002752] group-hover:text-[#014694] group-hover:underline inline-flex items-center gap-1 group-hover:gap-1.5 transition-all">
                                <span>Baca Selengkapnya</span>
                                <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                            </a>
                            <span class="text-[10px] font-semibold text-slate-400">
                                {{ $isDb ? 'Dokumentasi' : 'Liputan' }}
                            </span>
                        </div>
                    </article>
                    @endforeach
                </div>
                @endif

                <!-- Tombol CTA Elegan di Bagian Bawah Grid -->
                <div class="mt-10 sm:mt-12 text-center" data-animate="fade-up">
                    <a href="{{ route('web.berita.index') }}" 
                       class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-[#002752] hover:bg-[#003875] shadow-xs hover:shadow-md transition-all group">
                        <span>Lihat Semua Berita &amp; Prestasi</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>

            </div>
        </section>
