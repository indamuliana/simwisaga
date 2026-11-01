        <!-- ── 10. Latest News & Achievements: Modern 4-Column Program Cards (Sesuai Referensi) ── -->
        <section class="py-14 sm:py-20 lg:py-24 bg-[#FAF9F6] text-slate-800 relative overflow-hidden" id="berita">
            <!-- Background Ornaments -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute -top-24 left-1/3 w-80 h-80 bg-blue-100/30 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 right-1/4 w-80 h-80 bg-amber-100/30 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <!-- 3-Column Horizontal Header Layout (Sesuai Gambar Referensi) -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 lg:gap-8 items-end mb-8 sm:mb-12" data-animate="fade-up">
                    <!-- Left: Tag & Bold Display Title -->
                    <div class="md:col-span-6 lg:col-span-5 text-left">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 border border-slate-200/80 text-slate-700 text-xs font-semibold shadow-2xs mb-2.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#00519E]"></span>
                            <span>{{ $cfg['berita_heading']['tag'] ?? 'Kabar & Prestasi Terkini' }}</span>
                        </span>
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight">
                            {{ $cfg['berita_heading']['title'] ?? 'Berita Terbaru SMK Wikrama 1 Garut' }}
                        </h2>
                    </div>

                    <!-- Middle: Descriptive Subtitle Paragraph -->
                    <div class="md:col-span-6 lg:col-span-4 text-left">
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                            {{ $cfg['berita_heading']['desc'] ?? 'Dokumentasi kegiatan, studi tiru nasional, capaian medali kompetisi, dan kiprah alumni di dunia industri.' }}
                        </p>
                    </div>

                    <!-- Right: Direct Link CTA -->
                    <div class="md:col-span-12 lg:col-span-3 flex md:justify-start lg:justify-end">
                        <a href="{{ route('web.berita.index') }}" class="group inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-900 hover:text-[#00519E] transition-colors whitespace-nowrap">
                            <span>Lihat Semua Berita</span>
                            <span class="transform group-hover:translate-x-1 transition-transform duration-200">&rarr;</span>
                        </a>
                    </div>
                </div>

                @php
                    $dbArticles = class_exists(\App\Modules\AdministrasiWeb\Models\WebArticle::class)
                        ? \App\Modules\AdministrasiWeb\Models\WebArticle::with('category')->published()->latest('published_at')->limit(4)->get()
                        : collect();
                    
                    $articles = $dbArticles->isNotEmpty() ? $dbArticles : collect($cfg['berita'] ?? [])->take(4);
                @endphp

                <!-- 4-Column Clean Cards Grid (Sesuai Referensi Gambar) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6 items-stretch" data-animate="fade-up">
                    @foreach($articles as $idx => $art)
                    @php
                        $isDb = is_object($art);
                        $artUrl = $isDb ? url('/berita/' . $art->slug) : ($art['url'] ?? '#');
                        $artTitle = $isDb ? $art->judul : ($art['title'] ?? '');
                        $artImg = $isDb ? $art->gambarSampulUrl() : \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($art['image'] ?? null, 'images/web/achievement-lkbb.jpg');
                        $artBadge = $isDb ? ($art->category?->nama ?? 'Berita') : ($art['badge'] ?? 'Warta');
                        $artDate = $isDb ? ($art->published_at ? $art->published_at->format('d M Y') : date('d M Y')) : ($art['date'] ?? '');
                        $artDesc = $isDb ? ($art->ringkasan ?: Str::limit(strip_tags($art->konten), 90)) : ($art['desc'] ?? '');
                        $isExternal = str_starts_with($artUrl, 'http');
                    @endphp
                    <a href="{{ $artUrl }}"
                       {{ $isExternal ? 'target="_blank" rel="noopener"' : '' }}
                       class="group flex flex-col justify-between h-full bg-white rounded-2xl border border-slate-200/80 p-3 sm:p-3.5 hover:shadow-xl hover:border-slate-300 transition-all duration-300 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#00519E]">
                        
                        <!-- Card Photo (Aspect Ratio 16:10 / 4:3) -->
                        <div>
                            <div class="aspect-[16/10] sm:aspect-[4/3] w-full rounded-xl overflow-hidden bg-slate-100 relative">
                                <img src="{{ $artImg }}" 
                                     alt="{{ $artTitle }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" 
                                     loading="lazy" 
                                     decoding="async">
                            </div>

                            <!-- Card Titles & Excerpt -->
                            <div class="pt-3.5 text-left">
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-snug group-hover:text-[#00519E] transition-colors line-clamp-2 mb-1.5">
                                    {{ $artTitle }}
                                </h3>
                                <p class="text-xs text-slate-600 line-clamp-1 leading-normal mb-1 font-normal">
                                    {{ $artDesc }}
                                </p>
                            </div>
                        </div>

                        <!-- Card Bottom Meta & Circular Angled Arrow Button (↗) -->
                        <div class="pt-3 mt-2 border-t border-slate-100 flex items-center justify-between gap-2 text-left">
                            <div class="min-w-0">
                                <span class="text-[11px] font-semibold text-slate-500 block truncate">
                                    {{ $artDate }} &bull; {{ $artBadge }}
                                </span>
                            </div>

                            <!-- Circular Angled Arrow Button (↗) -->
                            <span class="w-8 h-8 rounded-full border border-slate-200 group-hover:border-slate-800 text-slate-500 group-hover:text-slate-900 group-hover:bg-slate-50 flex items-center justify-center shrink-0 transition-colors shadow-2xs">
                                <svg class="w-3.5 h-3.5 transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H7M17 7V17"/>
                                </svg>
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>

            </div>
        </section>
