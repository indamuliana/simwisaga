        <!-- ── 12. National Education Report Card (Bright & Warm Education) ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-[#FAF9F6] text-slate-800 border-t border-slate-200/80 relative overflow-hidden" id="rapor-mutu">
            <!-- Background Ornaments -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute -top-20 left-10 w-80 h-80 bg-orange-200/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-20 right-10 w-80 h-80 bg-blue-200/20 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
                    
                    <!-- Progress Bar Metrics -->
                    <div class="lg:col-span-6" data-animate="slide-right">
                        <span class="google-tag google-tag-green shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ $cfg['rapor_mutu']['tag'] ?? 'Rapor Pendidikan Resmi Kemendikdasmen' }}</span>
                        </span>
                        <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mt-3 sm:mt-4">
                            {{ $cfg['rapor_mutu']['title'] ?? 'Peringkat 20% Teratas Nasional' }}
                        </h2>
                        <p class="text-xs text-slate-500 font-semibold mt-2 mb-6 sm:mb-8">
                            *{{ $cfg['rapor_mutu']['source'] ?? 'Validasi Data Resmi: Kemendikdasmen RI (raportpendidikan.kemdikbud.go.id)' }}
                        </p>

                        <!-- Bars with Coral to Leaf Green Gradient -->
                        <div class="space-y-5 sm:space-y-6">
                            @foreach($cfg['rapor_mutu']['metrics'] ?? [] as $m)
                            <div>
                                <div class="flex items-center justify-between gap-3 text-xs sm:text-sm font-bold text-slate-900 mb-2">
                                    <span class="leading-tight">{{ $m['label'] }}</span>
                                    <span class="text-[#FF6B4A] font-mono-code font-bold shrink-0 text-xs sm:text-sm">{{ $m['badge'] }}</span>
                                </div>
                                <div class="w-full bg-slate-200 h-3 rounded-full overflow-hidden p-0.5">
                                    <div class="bg-gradient-to-r from-[#FF6B4A] via-[#F59E0B] to-[#10B981] h-full rounded-full transition-all duration-1000" style="width: {{ $m['value'] }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Awards & Accreditations (Card Bento Style) -->
                    <div class="lg:col-span-6" data-animate="slide-left" data-animate-delay="1">
                        <div class="card-bento p-6 sm:p-8 bg-white border border-slate-200/80 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.05)]">
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-5 flex items-center gap-2">
                                <span class="text-amber-400 font-black text-xl">★</span> 
                                <span>Pengakuan Mutu &amp; Akreditasi Lembaga</span>
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($cfg['rapor_mutu']['awards'] ?? [] as $aw)
                                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200 mb-1.5">
                                        {{ $aw['badge'] }}
                                    </span>
                                    <h4 class="text-sm font-bold text-slate-900">{{ $aw['title'] }}</h4>
                                    <p class="text-[11px] text-slate-600 font-normal mt-0.5">{{ $aw['desc'] }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
