        <!-- ── Babak 7: Kalender Pendidikan & Agenda Akademik ── -->
        @php
            $dbAgendas = class_exists(\App\Modules\KalenderPendidikan\Models\KalenderAgenda::class)
                ? \Illuminate\Support\Facades\Cache::remember('landing.latest_agendas', 300, function () {
                    return \App\Modules\KalenderPendidikan\Models\KalenderAgenda::with('kategori')
                        ->where('status', 'publik')
                        ->where('tgl_selesai', '>=', now()->toDateString())
                        ->orderBy('tgl_mulai')
                        ->limit(3)
                        ->get();
                })
                : collect();
            $agendaFallbackItems = array_slice($cfg['agenda']['items'] ?? [], 0, 3);
            $hasAgendas = $dbAgendas->isNotEmpty() || !empty($agendaFallbackItems);
        @endphp

        @if($hasAgendas)
        <section class="py-16 sm:py-24 bg-[#FAF9F6] text-slate-800 border-b border-slate-200/80 relative scroll-mt-20 overflow-hidden" id="agenda">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <!-- Masthead Agenda -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-slate-200/80 pb-6 mb-8 sm:mb-12" data-animate="fade-up">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white text-slate-700 border border-slate-200/90 shadow-2xs mb-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#D97706] animate-pulse"></span>
                            <span class="tracking-wide">{{ $cfg['agenda']['tag'] ?? 'Agenda & Kalender Akademik' }}</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                            {{ $cfg['agenda']['title'] ?? 'Kalender Pendidikan & Agenda Terdekat' }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600 mt-2 font-normal leading-relaxed">
                            {{ $cfg['agenda']['subtitle'] ?? 'Jadwal sertifikasi kompetensi industri, pembinaan karakter santri, uji vokasi, dan agenda penting SMK Wikrama 1 Garut.' }}
                        </p>
                    </div>

                    <!-- Institutional Sync Pill (Archival Status Stamp) -->
                    <div class="self-start md:self-end flex items-center gap-2.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200/90 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                        <div class="text-[11px] leading-tight">
                            <span class="font-bold text-slate-800 block">Kalender Terpadu</span>
                            <span class="text-slate-400 font-mono text-[10px] block">Disdik Jabar &bull; DUDI</span>
                        </div>
                    </div>
                </div>

                <!-- Agenda Cards Grid (3 Columns) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7 items-stretch" data-animate="fade-up" data-animate-delay="1">
                    @if($dbAgendas->isNotEmpty())
                        @foreach($dbAgendas as $agenda)
                        @php
                            $month = strtoupper($agenda->tgl_mulai->translatedFormat('M'));
                            $day = $agenda->tgl_mulai->format('d');
                            $year = $agenda->tgl_mulai->format('Y');
                            $category = $agenda->kategori?->nama ?? 'Agenda';
                            $audiensMap = [
                                'semua' => 'Seluruh Santri',
                                'kelas' => 'Tingkat Kelas',
                                'guru'  => 'Dewan Guru',
                                'ortu'  => 'Wali Santri',
                            ];
                            $badge = $audiensMap[$agenda->audiens] ?? ($agenda->audiens ?: null);
                            $timeStr = null;
                            if ($agenda->jam_mulai) {
                                $timeStr = substr($agenda->jam_mulai, 0, 5) . ($agenda->jam_selesai ? ' - ' . substr($agenda->jam_selesai, 0, 5) : '') . ' WIB';
                            }
                            $location = $agenda->lokasi ?: 'Kampus SMK Wikrama 1 Garut';
                            $desc = $agenda->deskripsi ? \Illuminate\Support\Str::limit(strip_tags($agenda->deskripsi), 115) : null;
                        @endphp
                        <article class="group relative flex flex-col justify-between p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 hover:border-[#002752]/30 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                            <!-- Subtle Top Accent Gradient on Hover -->
                            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-[#002752] via-[#004080] to-[#D97706] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                            <div>
                                <!-- Date Stamp + Header Info -->
                                <div class="flex items-start gap-4">
                                    <!-- Architectural Date Stamp Block -->
                                    <div class="flex flex-col items-center justify-center shrink-0 w-16 rounded-xl overflow-hidden bg-[#FAF9F6] border border-slate-200/90 shadow-2xs group-hover:border-[#002752]/30 transition-colors">
                                        <span class="w-full text-center py-1 bg-[#002752] text-white font-mono text-[10px] font-bold uppercase tracking-wider">
                                            {{ $month }}
                                        </span>
                                        <div class="py-2 px-1 text-center">
                                            <span class="text-2xl sm:text-3xl font-black text-slate-900 leading-none tabular-nums block">
                                                {{ $day }}
                                            </span>
                                            <span class="text-[9px] font-mono text-slate-400 block mt-1">
                                                {{ $year }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Tags & Title -->
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-1.5 mb-2">
                                            <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-md bg-[#002752]/5 text-[#002752] border border-[#002752]/15">
                                                {{ $category }}
                                            </span>
                                            @if($badge)
                                            <span class="text-[10px] font-medium px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200/60">
                                                {{ $badge }}
                                            </span>
                                            @endif
                                        </div>

                                        <h3 class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-[#002752] transition-colors line-clamp-2 leading-snug">
                                            {{ $agenda->judul }}
                                        </h3>
                                    </div>
                                </div>

                                <!-- Ringkasan / Deskripsi -->
                                @if($desc)
                                <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed mt-3 pt-3 border-t border-slate-100 font-normal">
                                    {{ $desc }}
                                </p>
                                @endif
                            </div>

                            <!-- Card Footer: Jam & Lokasi -->
                            <div class="flex flex-col gap-1.5 text-xs text-slate-500 mt-4 pt-3 border-t border-slate-100">
                                @if($timeStr)
                                <div class="flex items-center gap-2 text-slate-600">
                                    <svg class="w-3.5 h-3.5 text-[#002752] shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="font-mono text-[11px] font-medium text-slate-700">{{ $timeStr }}</span>
                                </div>
                                @endif
                                <div class="flex items-center gap-2 text-slate-500">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                                    </svg>
                                    <span class="line-clamp-1 text-[11px]">{{ $location }}</span>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    @else
                        @foreach($agendaFallbackItems as $item)
                        @php
                            $month = $item['month'] ?? 'AGD';
                            $day = $item['day'] ?? '01';
                            $year = $item['year'] ?? date('Y');
                            $category = $item['category'] ?? 'Agenda';
                            $badge = $item['badge'] ?? null;
                            $title = $item['title'] ?? '';
                            $desc = $item['desc'] ?? null;
                            $timeStr = $item['time'] ?? null;
                            $location = $item['location'] ?? 'Kampus SMK Wikrama 1 Garut';
                        @endphp
                        <article class="group relative flex flex-col justify-between p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 hover:border-[#002752]/30 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                            <!-- Subtle Top Accent Gradient on Hover -->
                            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-[#002752] via-[#004080] to-[#D97706] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                            <div>
                                <!-- Date Stamp + Header Info -->
                                <div class="flex items-start gap-4">
                                    <!-- Architectural Date Stamp Block -->
                                    <div class="flex flex-col items-center justify-center shrink-0 w-16 rounded-xl overflow-hidden bg-[#FAF9F6] border border-slate-200/90 shadow-2xs group-hover:border-[#002752]/30 transition-colors">
                                        <span class="w-full text-center py-1 bg-[#002752] text-white font-mono text-[10px] font-bold uppercase tracking-wider">
                                            {{ $month }}
                                        </span>
                                        <div class="py-2 px-1 text-center">
                                            <span class="text-2xl sm:text-3xl font-black text-slate-900 leading-none tabular-nums block">
                                                {{ $day }}
                                            </span>
                                            <span class="text-[9px] font-mono text-slate-400 block mt-1">
                                                {{ $year }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Tags & Title -->
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-1.5 mb-2">
                                            <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-md bg-[#002752]/5 text-[#002752] border border-[#002752]/15">
                                                {{ $category }}
                                            </span>
                                            @if($badge)
                                            <span class="text-[10px] font-medium px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200/60">
                                                {{ $badge }}
                                            </span>
                                            @endif
                                        </div>

                                        <h3 class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-[#002752] transition-colors line-clamp-2 leading-snug">
                                            {{ $title }}
                                        </h3>
                                    </div>
                                </div>

                                <!-- Ringkasan / Deskripsi -->
                                @if($desc)
                                <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed mt-3 pt-3 border-t border-slate-100 font-normal">
                                    {{ $desc }}
                                </p>
                                @endif
                            </div>

                            <!-- Card Footer: Jam & Lokasi -->
                            <div class="flex flex-col gap-1.5 text-xs text-slate-500 mt-4 pt-3 border-t border-slate-100">
                                @if($timeStr)
                                <div class="flex items-center gap-2 text-slate-600">
                                    <svg class="w-3.5 h-3.5 text-[#002752] shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="font-mono text-[11px] font-medium text-slate-700">{{ $timeStr }}</span>
                                </div>
                                @endif
                                <div class="flex items-center gap-2 text-slate-500">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                                    </svg>
                                    <span class="line-clamp-1 text-[11px]">{{ $location }}</span>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    @endif
                </div>

                <!-- Banner Penyelarasan Kurikulum & Sertifikasi -->
                <div class="mt-8 sm:mt-10 p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex flex-col md:flex-row items-start md:items-center justify-between gap-5 text-left" data-animate="fade-up">
                    <div class="flex items-start sm:items-center gap-4 min-w-0">
                        <div class="w-11 h-11 rounded-xl bg-[#002752]/5 border border-[#002752]/15 flex items-center justify-center text-[#002752] shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-2.18-5.73l-.478-.479a2.25 2.25 0 00-3.182 0l-3.32 3.32a2.25 2.25 0 000 3.182l.479.479a2.25 2.25 0 003.182 0l3.32-3.32a2.25 2.25 0 000-3.182z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-sm sm:text-base font-bold text-slate-900 leading-snug">
                                Kalender Pendidikan Terstandarisasi Disdik Jawa Barat &amp; DUDI Mitra
                            </h4>
                            <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed max-w-3xl font-normal">
                                Jadwal operasional diselaraskan berkala dengan kalender pendidikan Dinas Pendidikan Provinsi Jawa Barat, asesmen nasional, dan agenda sertifikasi kompetensi industri LSP-P1 BNSP.
                            </p>
                        </div>
                    </div>
                    
                    <!-- Partner Accreditation Chips -->
                    <div class="flex flex-wrap items-center gap-2 shrink-0 self-stretch sm:self-auto pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-[#FAF9F6] text-slate-700 border border-slate-200">
                            LSP-P1 BNSP
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-[#FAF9F6] text-slate-700 border border-slate-200">
                            Disdik Jabar
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-[#FAF9F6] text-slate-700 border border-slate-200">
                            Kurikulum Merdeka
                        </span>
                    </div>
                </div>

            </div>
        </section>
        @endif
