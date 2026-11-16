        <!-- ── 12. National Education Report Card (Validasi Mutu Resmi Kemendikdasmen) ── -->
        <section class="py-16 sm:py-24 bg-[#FAF9F6] text-slate-800 border-t border-slate-200/80 relative overflow-hidden scroll-mt-20" id="rapor-mutu">
            <span id="suara-komunitas" class="absolute -top-20"></span>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                    
                    <!-- Progress Bar Metrics (Left: 6 Cols) -->
                    <div class="lg:col-span-6 text-left" data-animate="fade-up">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-[#EBF4FC] text-[#002752] border border-[#002752]/20 mb-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#002752]"></span>
                            <span>{{ $cfg['rapor_mutu']['tag'] ?? 'Rapor Pendidikan Resmi Kemendikdasmen' }}</span>
                        </span>
                        <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mt-2 leading-tight">
                            {{ $cfg['rapor_mutu']['title'] ?? 'Peringkat 20% Teratas Nasional' }}
                        </h2>
                        <p class="text-xs text-slate-500 font-medium mt-2 mb-8 font-mono">
                            *{{ $cfg['rapor_mutu']['source'] ?? 'Validasi Data Resmi: Kemendikdasmen RI (raportpendidikan.kemdikbud.go.id)' }}
                        </p>

                        <!-- High-Contrast Institutional Progress Bars with Clean Solid Track -->
                        <div class="space-y-5 sm:space-y-6">
                            @foreach($cfg['rapor_mutu']['metrics'] ?? [] as $m)
                            @php
                                $badgeText = $m['badge'] ?? '';
                                preg_match('/^(\d+)%(.*)$/', $badgeText, $matches);
                                $targetNumber = isset($matches[1]) ? (int)$matches[1] : (int)($m['value'] ?? 0);
                                $suffixText = isset($matches[2]) ? trim($matches[2]) : '';
                            @endphp
                            <div>
                                <div class="flex items-center justify-between gap-3 text-xs sm:text-sm font-bold text-slate-900 mb-2">
                                    <span class="leading-tight">{{ $m['label'] }}</span>
                                    <span class="text-[#002752] font-mono font-bold shrink-0 text-xs sm:text-sm tabular-nums">
                                        <span class="rapor-counter-val" data-target="{{ $targetNumber }}">{{ $targetNumber }}</span>%@if($suffixText) <span class="font-normal text-slate-500">{{ str_starts_with($suffixText, '(') ? $suffixText : '(' . $suffixText . ')' }}</span>@endif
                                    </span>
                                </div>
                                <div class="w-full bg-slate-200/80 h-3 rounded-full overflow-hidden p-0.5 shadow-inner">
                                    <div class="h-full rounded-full transition-all duration-1000 ease-out" 
                                         style="width: {{ $m['value'] ?? 75 }}%; background-color: #002752;"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Awards & Accreditations (Card Bento Style - Right: 6 Cols) -->
                    <div class="lg:col-span-6 text-left" data-animate="fade-up" data-animate-delay="1">
                        <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs">
                            <div class="flex items-center gap-2.5 mb-6 pb-4 border-b border-slate-100">
                                <div class="w-8 h-8 rounded-lg bg-[#EBF4FC] border border-[#002752]/20 flex items-center justify-center text-[#002752] shrink-0">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <h3 class="text-base sm:text-lg font-bold text-slate-900 leading-snug">
                                    Pengakuan Mutu &amp; Akreditasi Lembaga
                                </h3>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($cfg['rapor_mutu']['awards'] ?? [] as $aw)
                                <div class="p-4.5 rounded-2xl bg-[#FAF9F6] border border-slate-200/80 hover:border-[#002752]/30 transition-all duration-200 flex flex-col justify-between">
                                    <div>
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white text-[#002752] border border-slate-200 mb-2 shadow-2xs">
                                            {{ $aw['badge'] }}
                                        </span>
                                        <h4 class="text-sm font-bold text-slate-900 leading-snug">{{ $aw['title'] }}</h4>
                                    </div>
                                    <p class="text-xs text-slate-500 font-normal mt-2 leading-relaxed">{{ $aw['desc'] }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>