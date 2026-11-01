        <!-- ── 10. Latest News & Achievements: Editorial Magazine Split (Wikrama Royal Archive) ── -->
        <section class="py-14 sm:py-20 lg:py-24 bg-[#FAF9F6] text-slate-800 relative overflow-hidden" id="berita">
            <!-- Background Ornaments -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute -top-24 left-1/3 w-80 h-80 bg-blue-100/30 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 right-1/4 w-80 h-80 bg-amber-100/30 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 sm:mb-14 gap-4" data-animate="fade-up">
                    <div>
                        <span class="google-tag google-tag-blue shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-[#00519E]"></span>
                            <span>Kabar &amp; Prestasi Terkini</span>
                        </span>
                        <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mt-3 sm:mt-4">
                            Berita Terbaru SMK Wikrama 1 Garut
                        </h2>
                        <p class="mt-3 sm:mt-4 text-sm sm:text-lg text-slate-600 max-w-2xl font-normal">
                            Dokumentasi kegiatan, studi tiru nasional, capaian medali kompetisi, dan kiprah alumni di dunia industri.
                        </p>
                    </div>
                    <a href="{{ route('web.berita.index') }}" class="btn-jis-pill-outline text-xs sm:text-sm !py-2.5 !px-5 shrink-0 w-full sm:w-auto text-center justify-center">
                        <span>Seluruh Arsip Berita</span>
                        <span class="group-hover-arrow">&rarr;</span>
                    </a>
                </div>

                @php
                    $dbArticles = class_exists(\App\Modules\AdministrasiWeb\Models\WebArticle::class)
                        ? \App\Modules\AdministrasiWeb\Models\WebArticle::with('category')->published()->latest('published_at')->limit(4)->get()
                        : collect();
                    
                    if ($dbArticles->isNotEmpty()) {
                        $leadArticle = $dbArticles->first();
                        $sideArticles = $dbArticles->slice(1);
                    } else {
                        $fallbackList = collect($cfg['berita'] ?? []);
                        $leadArticle = $fallbackList->first();
                        $sideArticles = $fallbackList->slice(1);
                    }
                @endphp

                <!-- Editorial Magazine Layout: Lead Feature + Companion Stories -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-stretch">
                    
                    <!-- Lead Feature Story (Col Span 7) -->
                    @if($leadArticle)
                    @php
                        $isDb = is_object($leadArticle);
                        $leadUrl = $isDb ? url('/berita/' . $leadArticle->slug) : ($leadArticle['url'] ?? '#');
                        $leadTitle = $isDb ? $leadArticle->judul : ($leadArticle['title'] ?? '');
                        $leadImg = $isDb ? $leadArticle->gambarSampulUrl() : asset($leadArticle['image']);
                        $leadBadge = $isDb ? ($leadArticle->category?->nama ?? 'Berita') : ($leadArticle['badge'] ?? 'Warta');
                        $leadDate = $isDb ? ($leadArticle->published_at ? $leadArticle->published_at->format('d M Y') : date('d M Y')) : ($leadArticle['date'] ?? '');
                        $leadDesc = $isDb ? ($leadArticle->ringkasan ?: Str::limit(strip_tags($leadArticle->konten), 160)) : ($leadArticle['desc'] ?? '');
                    @endphp
                    <div class="lg:col-span-7 flex flex-col" data-animate="fade-up">
                        <article class="card-editorial flex flex-col justify-between h-full group bg-white">
                            <div>
                                <div class="aspect-[16/10] sm:aspect-[16/9] w-full relative overflow-hidden bg-slate-100">
                                    <img src="{{ $leadImg }}" 
                                         alt="{{ $leadTitle }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" 
                                         loading="lazy"
                                         decoding="async">
                                    <span class="absolute top-4 left-4 px-3.5 py-1 rounded-full text-xs font-bold bg-[#00519E] text-white shadow-md">
                                        {{ $leadBadge }}
                                    </span>
                                </div>
                                <div class="p-6 sm:p-8 pt-5">
                                    <span class="text-xs text-[#00519E] font-semibold block mb-2 font-mono-code">
                                        {{ $leadDate }} &bull; Liputan Utama
                                    </span>
                                    <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-snug group-hover:text-[#00519E] transition-colors">
                                        <a href="{{ $leadUrl }}">
                                            {{ $leadTitle }}
                                        </a>
                                    </h3>
                                    <p class="text-xs sm:text-sm text-slate-600 mt-3 leading-relaxed font-normal">
                                        {{ $leadDesc }}
                                    </p>
                                </div>
                            </div>
                            <div class="px-6 sm:px-8 py-4 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between">
                                <a href="{{ $leadUrl }}" class="text-xs sm:text-sm text-[#00519E] font-bold hover:text-[#00305F] inline-flex items-center gap-1.5 transition-colors">
                                    <span>Baca Berita Selengkapnya</span>
                                    <span class="group-hover-arrow">&rarr;</span>
                                </a>
                                <span class="text-[11px] text-slate-500 font-medium">SMK Wikrama 1 Garut</span>
                            </div>
                        </article>
                    </div>
                    @endif

                    <!-- Companion Stories (Col Span 5 Stacked Cards) -->
                    <div class="lg:col-span-5 flex flex-col gap-4 sm:gap-5" data-animate="fade-up" data-animate-delay="1">
                        @foreach($sideArticles as $sIdx => $side)
                        @php
                            $isDbSide = is_object($side);
                            $sideUrl = $isDbSide ? url('/berita/' . $side->slug) : ($side['url'] ?? '#');
                            $sideTitle = $isDbSide ? $side->judul : ($side['title'] ?? '');
                            $sideImg = $isDbSide ? $side->gambarSampulUrl() : asset($side['image']);
                            $sideBadge = $isDbSide ? ($side->category?->nama ?? 'Berita') : ($side['badge'] ?? 'Warta');
                            $sideDate = $isDbSide ? ($side->published_at ? $side->published_at->format('d M Y') : date('d M Y')) : ($side['date'] ?? '');
                            $sideDesc = $isDbSide ? ($side->ringkasan ?: Str::limit(strip_tags($side->konten), 90)) : ($side['desc'] ?? '');
                        @endphp
                        <article class="card-editorial p-4 sm:p-5 flex flex-col sm:flex-row gap-4 items-center group bg-white">
                            <div class="w-full sm:w-32 h-36 sm:h-28 rounded-xl overflow-hidden shrink-0 bg-slate-100 relative">
                                <img src="{{ $sideImg }}" 
                                     alt="{{ $sideTitle }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" 
                                     loading="lazy"
                                     decoding="async">
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[9px] font-bold bg-[#00305F] text-white">
                                    {{ $sideBadge }}
                                </span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[11px] text-[#00519E] font-semibold block mb-1 font-mono-code">{{ $sideDate }}</span>
                                <h4 class="text-sm font-bold text-slate-900 leading-snug group-hover:text-[#00519E] transition-colors line-clamp-2">
                                    <a href="{{ $sideUrl }}">
                                        {{ $sideTitle }}
                                    </a>
                                </h4>
                                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed line-clamp-2 font-normal">
                                    {{ $sideDesc }}
                                </p>
                                <a href="{{ $sideUrl }}" class="text-[11px] text-[#00519E] font-bold inline-flex items-center gap-1 mt-2 hover:underline">
                                    <span>Baca detail</span>
                                    <span class="group-hover-arrow">&rarr;</span>
                                </a>
                            </div>
                        </article>
                        @endforeach
                    </div>

                </div>
            </div>
        </section>
