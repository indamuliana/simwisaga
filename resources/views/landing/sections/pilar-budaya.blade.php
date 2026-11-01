        <!-- ── 14. Filosofi Pilar 2: Tiada Pendidikan Karakter, Tanpa Budaya Sekolah (Wikrama Royal Archive) ── -->
        <section class="py-14 sm:py-18 lg:py-22 bg-[#FAF9F6] text-slate-800 border-t border-slate-200/80 overflow-hidden" id="pilar-budaya">
            <div id="budaya" class="scroll-mt-24"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 sm:mb-8 text-center" data-animate="fade-up">
                <span class="google-tag google-tag-blue shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-[#00519E]"></span>
                    <span>{{ $cfg['pilar_budaya']['tag'] ?? 'Jejaring Perguruan Tinggi' }}</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mt-3">
                    {{ $cfg['pilar_budaya']['title'] ?? 'Tiada pendidikan karakter, tanpa budaya sekolah' }}
                </h2>
                <p class="max-w-2xl mx-auto text-xs sm:text-sm text-slate-600 mt-2 font-normal">
                    {{ $cfg['pilar_budaya']['desc'] ?? 'Budaya belajar unggul yang mengantarkan alumni menembus berbagai perguruan tinggi terbaik nasional dan internasional.' }}
                </p>
            </div>

            <!-- Logo Marquee Perguruan Tinggi (Warm Canvas Themed) -->
            <div class="partner-marquee-container relative w-full overflow-hidden py-4">
                <div class="partner-marquee-track flex items-center gap-4 sm:gap-6">
                    @for($pass = 0; $pass < 2; $pass++)
                        @foreach($cfg['pilar_budaya']['universities'] ?? [] as $uni)
                        <div class="partner-card flex items-center gap-2.5 sm:gap-3 py-2.5 sm:py-3 px-3.5 sm:px-5 rounded-2xl bg-white border border-slate-200/80 hover:border-[#00519E] shadow-xs shrink-0">
                            <img src="{{ asset($uni['image']) }}" 
                                 alt="{{ $uni['name'] }}" 
                                 class="w-7 h-7 sm:w-8 sm:h-8 object-contain shrink-0" 
                                 loading="lazy" 
                                 decoding="async">
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 whitespace-nowrap">{{ $uni['name'] }}</h4>
                                <span class="text-[10px] text-[#00519E] font-semibold block whitespace-nowrap">{{ $uni['status'] ?? 'Mitra Akademik' }}</span>
                            </div>
                        </div>
                        @endforeach
                    @endfor
                </div>
            </div>
            
            <div class="text-center mt-6" data-animate="fade-up">
                <a href="{{ $cfg['pilar_budaya']['btn_url'] ?? '#filosofi' }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#00519E] hover:text-[#00305F] transition-colors">
                    <span>{{ $cfg['pilar_budaya']['btn_text'] ?? 'Pelajari Budaya Sekolah Lebih Lanjut' }}</span>
                    <span class="group-hover-arrow">&rarr;</span>
                </a>
            </div>
        </section>
