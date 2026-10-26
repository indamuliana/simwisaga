        <!-- ── 11. Kalender Pendidikan & Agenda Kegiatan Terdekat (Bright & Warm Education) ── -->
        <section class="py-14 sm:py-20 lg:py-24 bg-[#FAF9F6] text-slate-800 border-t border-slate-200/80 relative overflow-hidden" id="agenda">
            <!-- Background Ornaments -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute -top-24 right-1/4 w-80 h-80 bg-orange-200/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 left-1/4 w-80 h-80 bg-blue-200/20 rounded-full blur-3xl"></div>
            </div>

            <div id="agenda-sekolah" class="scroll-mt-24"></div>
            <div class="w-full max-w-[1680px] mx-auto px-4 sm:px-6 lg:px-10 xl:px-16 relative z-10">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 sm:mb-12 gap-6" data-animate="fade-up">
                    <div class="max-w-3xl">
                        <span class="google-tag shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-[#FF6B4A]"></span>
                            <span>{{ $cfg['agenda']['tag'] ?? 'Agenda & Kalender Akademik' }}</span>
                        </span>
                        <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mt-3 sm:mt-4">
                            {{ $cfg['agenda']['title'] ?? 'Agenda Kegiatan Sekolah Terdekat' }}
                        </h2>
                        <p class="mt-3 text-sm sm:text-base text-slate-600 leading-relaxed font-normal">
                            {{ $cfg['agenda']['subtitle'] ?? 'Jadwal sertifikasi kompetensi industri, pembinaan karakter santri, uji vokasi, dan agenda penting SMK Wikrama 1 Garut.' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        <a href="{{ $cfg['agenda']['calendar_pdf_url'] ?? 'https://smkwikrama1garut.sch.id/' }}" 
                           target="_blank" 
                           rel="noopener"
                           class="btn-google-outline text-xs sm:text-sm !py-2.5 !px-5 inline-flex items-center justify-center gap-2 group w-full sm:w-auto">
                            <svg class="w-4 h-4 text-red-500 shrink-0 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ $cfg['agenda']['btn_calendar_text'] ?? 'Unduh Kalender Akademik (PDF)' }}</span>
                            <span class="text-xs">&darr;</span>
                        </a>
                        @auth
                        <a href="{{ route('kalender.index') }}" class="btn-google-primary text-xs sm:text-sm !py-2.5 !px-5 inline-flex items-center justify-center gap-2 shrink-0 w-full sm:w-auto">
                            <span>Buka Kalender SIRAGA</span>
                            <span>&rarr;</span>
                        </a>
                        @endauth
                    </div>
                </div>

                @php
                    $dbAgendas = class_exists(\App\Modules\KalenderPendidikan\Models\KalenderAgenda::class)
                        ? \App\Modules\KalenderPendidikan\Models\KalenderAgenda::with('kategori')
                            ->where('status', 'publik')
                            ->where('tgl_selesai', '>=', now()->toDateString())
                            ->orderBy('tgl_mulai')
                            ->limit(3)
                            ->get()
                        : collect();
                    $agendaFallbackItems = $cfg['agenda']['items'] ?? [];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @if($dbAgendas->isNotEmpty())
                        @foreach($dbAgendas as $idx => $agenda)
                        <article class="card-calendar-slate group relative flex flex-col justify-between" data-animate="fade-up" data-animate-delay="{{ $idx + 1 }}">
                            <div>
                                <!-- Header Date & Badge -->
                                <div class="flex items-start justify-between gap-3 mb-5">
                                    <div class="flex flex-col items-center justify-center shrink-0 w-16 rounded-2xl overflow-hidden border border-slate-200 shadow-xs bg-white">
                                        <span class="w-full text-center py-1 bg-gradient-to-r from-[#FF6B4A] to-[#FF5722] text-white font-mono-code font-bold text-[10px] tracking-widest uppercase">
                                            {{ strtoupper($agenda->tgl_mulai->translatedFormat('M')) }}
                                        </span>
                                        <div class="py-1.5 px-2 flex flex-col items-center">
                                            <span class="text-2xl sm:text-3xl font-black text-slate-900 leading-none tracking-tight">
                                                {{ $agenda->tgl_mulai->format('d') }}
                                            </span>
                                            <span class="text-[9px] font-mono-code font-bold text-[#FF6B4A] mt-0.5">
                                                {{ $agenda->tgl_mulai->format('Y') }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-end gap-1.5 text-right">
                                        <span class="px-3 py-1 rounded-full text-[11px] font-bold text-white shadow-xs" style="background-color: {{ $agenda->display_warna }}">
                                            {{ $agenda->kategori?->nama ?? 'Kegiatan' }}
                                        </span>
                                        @if($agenda->is_final)
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                            ✓ FINAL
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <h3 class="text-lg sm:text-xl font-bold text-slate-900 group-hover:text-[#FF6B4A] transition-colors leading-snug line-clamp-2">
                                    {{ $agenda->judul }}
                                </h3>

                                <p class="mt-2.5 text-xs sm:text-sm text-slate-600 leading-relaxed font-normal line-clamp-3">
                                    {{ $agenda->deskripsi ?: 'Agenda kurikulum dan kegiatan resmi SMK Wikrama 1 Garut yang telah disahkan.' }}
                                </p>
                            </div>

                            <div class="pt-4 mt-6 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-600 font-semibold">
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-medium">{{ $agenda->is_all_day ? 'Sepanjang Hari' : substr($agenda->jam_mulai,0,5).' WIB' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="font-medium truncate" title="{{ $agenda->lokasi ?: 'Sekolah' }}">{{ $agenda->lokasi ?: 'Sekolah' }}</span>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    @else
                        @foreach($agendaFallbackItems as $idx => $item)
                        <article class="card-calendar-slate group relative flex flex-col justify-between" data-animate="fade-up" data-animate-delay="{{ $idx + 1 }}">
                            <div>
                                <!-- Header Date & Badge -->
                                <div class="flex items-start justify-between gap-3 mb-5">
                                    <div class="flex flex-col items-center justify-center shrink-0 w-16 rounded-2xl overflow-hidden border border-slate-200 shadow-xs bg-white">
                                        <span class="w-full text-center py-1 bg-gradient-to-r from-[#FF6B4A] to-[#FF5722] text-white font-mono-code font-bold text-[10px] tracking-widest uppercase">
                                            {{ $item['month'] }}
                                        </span>
                                        <div class="py-1.5 px-2 flex flex-col items-center">
                                            <span class="text-2xl sm:text-3xl font-black text-slate-900 leading-none tracking-tight">
                                                {{ $item['day'] }}
                                            </span>
                                            <span class="text-[9px] font-mono-code font-bold text-[#FF6B4A] mt-0.5">
                                                {{ $item['year'] }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-end gap-1.5 text-right">
                                        <span class="px-3 py-1 rounded-full text-[11px] font-bold {{ $item['category_badge'] ?? 'bg-orange-50 text-orange-700 border border-orange-200' }}">
                                            {{ $item['category'] }}
                                        </span>
                                        @if(!empty($item['badge']))
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $item['badge'] }}
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <h3 class="text-lg sm:text-xl font-bold text-slate-900 group-hover:text-[#FF6B4A] transition-colors leading-snug line-clamp-2">
                                    {{ $item['title'] }}
                                </h3>

                                <p class="mt-2.5 text-xs sm:text-sm text-slate-600 leading-relaxed font-normal line-clamp-3">
                                    {{ $item['desc'] }}
                                </p>
                            </div>

                            <div class="pt-4 mt-6 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-600 font-semibold">
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-medium">{{ $item['time'] }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="font-medium truncate" title="{{ $item['location'] }}">{{ $item['location'] }}</span>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    @endif
                </div>

                <!-- Bottom Information Banner -->
                <div class="mt-8 sm:mt-10 p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs shadow-xs" data-animate="fade-up">
                    <div class="flex items-center gap-3 text-slate-800 font-semibold">
                        <span class="w-8 h-8 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center shrink-0 border border-orange-200">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <span>Seluruh agenda kurikulum disesuaikan dengan kalender akademik resmi Dinas Pendidikan &amp; Dunia Industri mitra SMK Wikrama 1 Garut.</span>
                    </div>
                    @auth
                    <a href="{{ route('kalender.index') }}" class="text-[#FF6B4A] font-bold hover:text-[#E85535] inline-flex items-center gap-1 shrink-0">
                        <span>Kelola Kalender di SIRAGA</span>
                        <span>&rarr;</span>
                    </a>
                    @endauth
                </div>
            </div>
        </section>
