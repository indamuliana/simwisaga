        <!-- ── Babak 6: Campus Gazette (Warta Sekolah & Agenda Akademik Berdampingan) ── -->
        @php
            $dbArticles = class_exists(\App\Modules\AdministrasiWeb\Models\WebArticle::class)
                ? \App\Modules\AdministrasiWeb\Models\WebArticle::with('category')->published()->latest('published_at')->limit(3)->get()
                : collect();
            
            $articles = $dbArticles->isNotEmpty() ? $dbArticles : collect($cfg['berita'] ?? [])->take(3);
            
            $dbAgendas = class_exists(\App\Modules\KalenderPendidikan\Models\KalenderAgenda::class)
                ? \App\Modules\KalenderPendidikan\Models\KalenderAgenda::with('kategori')
                    ->where('status', 'publik')
                    ->where('tgl_selesai', '>=', now()->toDateString())
                    ->orderBy('tgl_mulai')
                    ->limit(3)
                    ->get()
                : collect();
            $agendaFallbackItems = array_slice($cfg['agenda']['items'] ?? [], 0, 3);
        @endphp

        <section class="py-16 sm:py-24 bg-white text-slate-800 border-b border-slate-200/80 relative scroll-mt-20" id="berita">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">
                    
                    <!-- ── Kolom Kiri: Warta & Kabar Prestasi Sekolah (7 Cols) ── -->
                    <div class="lg:col-span-7 text-left space-y-6" data-animate="fade-up">
                        
                        <!-- Masthead Kiri -->
                        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200/80 pb-4">
                            <div>
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-[#EBF4FC] text-[#002752] border border-[#002752]/15 mb-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#002752]"></span>
                                    <span>Publikasi &amp; Prestasi Kampus</span>
                                </span>
                                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                                    Warta &amp; Kabar Prestasi Sekolah
                                </h2>
                            </div>
                            <a href="{{ route('web.berita.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#002752] hover:text-[#014694] group whitespace-nowrap self-start sm:self-end pb-0.5">
                                <span>Lihat Semua Warta</span>
                                <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>

                        <!-- Lead News Item (Editorial Featured Story) -->
                        @if($articles->isNotEmpty())
                        @php
                            $leadArt = $articles->first();
                            $isDb = is_object($leadArt);
                            $leadUrl = $isDb ? url('/berita/' . $leadArt->slug) : ($leadArt['url'] ?? '#');
                            $leadTitle = $isDb ? $leadArt->judul : ($leadArt['title'] ?? '');
                            $leadImg = $isDb ? $leadArt->gambarSampulUrl() : \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($leadArt['image'] ?? null, 'images/web/achievement-lkbb.jpg');
                            $leadBadge = $isDb ? ($leadArt->category?->nama ?? 'Warta Utama') : ($leadArt['badge'] ?? 'Prestasi Nasional');
                            $leadDate = $isDb ? ($leadArt->published_at ? $leadArt->published_at->translatedFormat('d F Y') : date('d M Y')) : ($leadArt['date'] ?? '11 Nov 2025');
                            $leadDesc = $isDb ? ($leadArt->ringkasan ?: Str::limit(strip_tags($leadArt->konten), 140)) : ($leadArt['desc'] ?? '');
                        @endphp
                        
                        <article class="group rounded-2xl border border-slate-200/90 overflow-hidden bg-[#FAF9F6] shadow-2xs hover:shadow-md transition-all duration-300">
                            <a href="{{ $leadUrl }}" class="block">
                                <div class="aspect-[16/9] w-full overflow-hidden bg-slate-100 relative">
                                    <img src="{{ $leadImg }}" 
                                         alt="{{ $leadTitle }}" 
                                         class="w-full h-full object-cover object-top group-hover:scale-102 transition-transform duration-500 ease-out"
                                         loading="lazy">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 via-transparent to-transparent pointer-events-none"></div>
                                    <div class="absolute top-4 left-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#002752] text-white shadow-xs tracking-wide">
                                            {{ $leadBadge }}
                                        </span>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <div class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                                        <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <time>{{ $leadDate }}</time>
                                        </span>
                                        <span class="text-slate-300">&bull;</span>
                                        <span class="text-slate-500 font-medium">Humas SMK Wikrama 1 Garut</span>
                                    </div>
                                    <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 group-hover:text-[#002752] transition-colors leading-snug">
                                        {{ $leadTitle }}
                                    </h3>
                                    <p class="text-xs sm:text-sm text-slate-600 mt-2.5 font-normal line-clamp-2 leading-relaxed">
                                        {{ $leadDesc }}
                                    </p>
                                    <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between">
                                        <span class="text-xs font-bold text-[#002752] group-hover:underline inline-flex items-center gap-1">
                                            <span>Baca Selengkapnya</span>
                                            <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                                        </span>
                                        <span class="text-[11px] font-semibold text-slate-400">Liputan Khusus</span>
                                    </div>
                                </div>
                            </a>
                        </article>

                        <!-- Secondary News Items (2 Horizontal Companion Cards) -->
                        <div class="space-y-3.5 pt-1">
                            @foreach($articles->slice(1) as $subArt)
                            @php
                                $isDbSub = is_object($subArt);
                                $subUrl = $isDbSub ? url('/berita/' . $subArt->slug) : ($subArt['url'] ?? '#');
                                $subTitle = $isDbSub ? $subArt->judul : ($subArt['title'] ?? '');
                                $subBadge = $isDbSub ? ($subArt->category?->nama ?? 'Warta') : ($subArt['badge'] ?? 'Prestasi');
                                $subDate = $isDbSub ? ($subArt->published_at ? $subArt->published_at->format('d M Y') : date('d M Y')) : ($subArt['date'] ?? '');
                                $subDesc = $isDbSub ? ($subArt->ringkasan ?: Str::limit(strip_tags($subArt->konten), 90)) : ($subArt['desc'] ?? '');
                                $subImg = $isDbSub ? $subArt->gambarSampulUrl() : \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($subArt['image'] ?? null, 'images/web/achievement-panahan.jpg');
                            @endphp
                            <a href="{{ $subUrl }}" class="group flex items-start gap-4 p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-[#002752]/30 hover:shadow-xs transition-all duration-200">
                                <div class="w-24 h-24 sm:w-28 sm:h-24 rounded-xl overflow-hidden bg-slate-100 shrink-0 border border-slate-100 relative">
                                    <img src="{{ $subImg }}" 
                                         alt="{{ $subTitle }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                                         loading="lazy">
                                </div>
                                <div class="min-w-0 flex-1 flex flex-col justify-between self-stretch">
                                    <div>
                                        <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                                            <span class="text-[#002752] font-bold">{{ $subBadge }}</span>
                                            <span class="text-slate-300">&bull;</span>
                                            <span class="font-medium text-slate-500">{{ $subDate }}</span>
                                        </div>
                                        <h4 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-[#002752] transition-colors leading-snug line-clamp-2">
                                            {{ $subTitle }}
                                        </h4>
                                    </div>
                                    <div class="pt-2 flex items-center justify-between text-xs text-slate-400 group-hover:text-[#002752] transition-colors">
                                        <span class="text-[11px] text-slate-500 line-clamp-1 font-normal">{{ Str::limit($subDesc, 55) }}</span>
                                        <span class="font-bold group-hover:translate-x-1 transition-transform shrink-0 ml-2">&rarr;</span>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <!-- ── Kolom Kanan: Kalender Akademik & Agenda Terdekat (5 Cols) ── -->
                    <div class="lg:col-span-5 text-left space-y-6 relative" id="agenda" data-animate="fade-up" data-animate-delay="1">
                        
                        <!-- Masthead Kanan -->
                        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200/80 pb-4">
                            <div>
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF9F6] text-slate-700 border border-slate-200 mb-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#D97706]"></span>
                                    <span>Jadwal Kegiatan &amp; Uji Vokasi</span>
                                </span>
                                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                                    Kalender Pendidikan
                                </h2>
                            </div>
                            <a href="{{ $cfg['agenda']['calendar_pdf_url'] ?? 'https://smkwikrama1garut.sch.id/' }}" 
                               target="_blank" 
                               rel="noopener" 
                               class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 hover:text-[#002752] group whitespace-nowrap self-start sm:self-end pb-0.5">
                                <svg class="w-3.5 h-3.5 text-slate-500 group-hover:text-[#002752] group-hover:translate-y-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                <span>Unduh PDF</span>
                            </a>
                        </div>

                        <!-- Agenda Items -->
                        <div class="space-y-3.5">
                            @if($dbAgendas->isNotEmpty())
                                @foreach($dbAgendas as $agenda)
                                <div class="p-4 rounded-2xl bg-[#FAF9F6] border border-slate-200/80 hover:border-slate-300 transition-all flex items-start gap-4">
                                    <!-- Architectural Date Block -->
                                    <div class="flex flex-col items-center justify-center shrink-0 w-14 sm:w-16 h-16 sm:h-18 rounded-xl overflow-hidden bg-white border border-slate-200 shadow-2xs">
                                        <span class="w-full text-center py-0.5 bg-[#002752] text-white font-mono text-[9px] font-bold uppercase tracking-wider">
                                            {{ strtoupper($agenda->tgl_mulai->translatedFormat('M')) }}
                                        </span>
                                        <span class="text-xl sm:text-2xl font-black text-slate-900 leading-tight py-1 tabular-nums">
                                            {{ $agenda->tgl_mulai->format('d') }}
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-white text-slate-700 border border-slate-200/80">
                                                {{ $agenda->kategori?->nama ?? 'Agenda' }}
                                            </span>
                                        </div>
                                        <h4 class="text-xs sm:text-sm font-bold text-slate-900 line-clamp-1 leading-snug">
                                            {{ $agenda->judul }}
                                        </h4>
                                        <div class="flex items-center gap-3 text-[11px] text-slate-500 mt-1.5">
                                            <span class="inline-flex items-center gap-1 line-clamp-1">
                                                <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span>{{ $agenda->lokasi ?: 'Kampus SMK Wikrama 1 Garut' }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                @foreach($agendaFallbackItems as $item)
                                <div class="p-4 rounded-2xl bg-[#FAF9F6] border border-slate-200/80 hover:border-slate-300 transition-all flex items-start gap-4">
                                    <!-- Architectural Date Block -->
                                    <div class="flex flex-col items-center justify-center shrink-0 w-14 sm:w-16 h-16 sm:h-18 rounded-xl overflow-hidden bg-white border border-slate-200 shadow-2xs">
                                        <span class="w-full text-center py-0.5 bg-[#002752] text-white font-mono text-[9px] font-bold uppercase tracking-wider">
                                            {{ $item['month'] }}
                                        </span>
                                        <span class="text-xl sm:text-2xl font-black text-slate-900 leading-tight py-1 tabular-nums">
                                            {{ $item['day'] }}
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-white text-slate-700 border border-slate-200/80">
                                                {{ $item['category'] }}
                                            </span>
                                        </div>
                                        <h4 class="text-xs sm:text-sm font-bold text-slate-900 line-clamp-1 leading-snug">
                                            {{ $item['title'] }}
                                        </h4>
                                        <div class="flex items-center gap-3 text-[11px] text-slate-500 mt-1.5">
                                            @if(!empty($item['time']))
                                            <span class="inline-flex items-center gap-1 shrink-0 font-mono">
                                                <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>{{ $item['time'] }}</span>
                                            </span>
                                            <span class="text-slate-300">&bull;</span>
                                            @endif
                                            <span class="inline-flex items-center gap-1 line-clamp-1">
                                                <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span>{{ $item['location'] ?? 'Kampus Wikrama Garut' }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Card Penyelarasan Kurikulum & Sertifikasi -->
                        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex items-start gap-3.5 text-left">
                            <div class="w-9 h-9 rounded-xl bg-[#EBF4FC] border border-[#002752]/20 flex items-center justify-center text-[#002752] font-bold text-xs shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h5 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug">
                                    Kalender Pendidikan Terstandarisasi Disdik &amp; DUDI
                                </h5>
                                <p class="text-[11px] sm:text-xs text-slate-600 mt-1 leading-relaxed font-normal">
                                    Jadwal operasional diselaraskan berkala dengan Dinas Pendidikan Provinsi Jawa Barat, kalender ujian nasional, dan agenda uji sertifikasi kompetensi industri LSP-P1 BNSP.
                                </p>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </section>