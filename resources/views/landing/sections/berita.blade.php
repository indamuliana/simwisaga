        <!-- ── 10. Latest News & Achievements (Bright & Warm Education) ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-[#FAF9F6] text-slate-800 relative overflow-hidden" id="berita">
            <!-- Background Ornaments -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute -top-24 left-1/3 w-80 h-80 bg-blue-200/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 right-1/4 w-80 h-80 bg-orange-200/20 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 sm:mb-14 gap-4" data-animate="fade-up">
                    <div>
                        <span class="google-tag google-tag-blue shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            <span>Kabar &amp; Prestasi Terkini</span>
                        </span>
                        <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mt-3 sm:mt-4">
                            Berita Terbaru SMK Wikrama 1 Garut
                        </h2>
                        <p class="mt-3 sm:mt-4 text-sm sm:text-lg text-slate-600 max-w-2xl font-normal">
                            Dokumentasi kegiatan, studi tiru nasional, capaian medali kompetisi, dan kiprah alumni di dunia industri.
                        </p>
                    </div>
                    <a href="{{ route('web.berita.index') }}" class="btn-google-outline text-xs sm:text-sm !py-2.5 !px-5 shrink-0 w-full sm:w-auto text-center justify-center">
                        <span>Seluruh Arsip Berita</span>
                        <span class="group-hover-arrow">&rarr;</span>
                    </a>
                </div>

                @php
                    $dbArticles = class_exists(\App\Modules\AdministrasiWeb\Models\WebArticle::class)
                        ? \App\Modules\AdministrasiWeb\Models\WebArticle::with('category')->published()->latest('published_at')->limit(4)->get()
                        : collect();
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @if ($dbArticles->isNotEmpty())
                        @foreach ($dbArticles as $idx => $art)
                        <article class="card-editorial flex flex-col justify-between h-full group" data-animate="fade-up" data-animate-delay="{{ $idx + 1 }}">
                            <div>
                                <div class="aspect-[16/10] w-full relative overflow-hidden bg-slate-100">
                                    <img src="{{ $art->gambarSampulUrl() }}" alt="{{ $art->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                    <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-bold bg-[#2563EB] text-white shadow-xs">
                                        {{ $art->category?->nama ?? 'Berita' }}
                                    </span>
                                </div>
                                <div class="p-5 pt-4">
                                    <span class="text-[11px] text-orange-600 font-semibold block mb-1">
                                        {{ $art->published_at ? $art->published_at->format('d M Y') : date('d M Y') }}
                                    </span>
                                    <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-snug hover:text-[#2563EB] transition-colors line-clamp-2">
                                        <a href="{{ url('/berita/' . $art->slug) }}">
                                            {{ $art->judul }}
                                        </a>
                                    </h3>
                                    <p class="text-xs text-slate-600 mt-2 leading-relaxed line-clamp-3 font-normal">
                                        {{ $art->ringkasan ?: Str::limit(strip_tags($art->konten), 130) }}
                                    </p>
                                </div>
                            </div>
                            <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/80">
                                <a href="{{ url('/berita/' . $art->slug) }}" class="text-xs text-[#2563EB] font-bold hover:text-[#1D4ED8] inline-flex items-center gap-1">
                                    <span>Baca Artikel Lengkap</span>
                                    <span class="group-hover-arrow">&rarr;</span>
                                </a>
                            </div>
                        </article>
                        @endforeach
                    @else
                        @foreach($cfg['berita'] ?? [] as $idx => $br)
                        <article class="card-editorial flex flex-col justify-between h-full group" data-animate="fade-up" data-animate-delay="{{ $idx + 1 }}">
                            <div>
                                <div class="aspect-[16/10] w-full relative overflow-hidden bg-slate-100">
                                    <img src="{{ asset($br['image']) }}" alt="{{ $br['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                    <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-bold bg-[#2563EB] text-white shadow-xs">
                                        {{ $br['badge'] }}
                                    </span>
                                </div>
                                <div class="p-5 pt-4">
                                    <span class="text-[11px] text-orange-600 font-semibold block mb-1">{{ $br['date'] }}</span>
                                    <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-snug hover:text-[#2563EB] transition-colors">
                                        <a href="{{ $br['url'] }}" target="_blank" rel="noopener">
                                            {{ $br['title'] }}
                                        </a>
                                    </h3>
                                    <p class="text-xs text-slate-600 mt-2 leading-relaxed line-clamp-3 font-normal">
                                        {{ $br['desc'] }}
                                    </p>
                                </div>
                            </div>
                            <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/80">
                                <a href="{{ $br['url'] }}" target="_blank" rel="noopener" class="text-xs text-[#2563EB] font-bold hover:text-[#1D4ED8] inline-flex items-center gap-1">
                                    <span>Baca Artikel Lengkap</span>
                                    <span class="group-hover-arrow">&rarr;</span>
                                </a>
                            </div>
                        </article>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>
