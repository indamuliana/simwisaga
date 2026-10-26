        <!-- ── 16. Sekolah Teladan Nasional: Partner, Prestasi & Rekognisi Resmi (Bright & Warm) ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-[#FAF9F6] text-slate-800 border-t border-slate-200/80 relative overflow-hidden" id="sekolah-teladan">
            <!-- Background Ornaments -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute inset-0 bg-[radial-gradient(#CBD5E1_1px,transparent_1px)] [background-size:28px_28px] opacity-40"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-12" data-animate="fade-up">
                    <span class="google-tag google-tag-blue shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span>{{ $cfg['sekolah_teladan']['tag'] ?? 'Partner & Rekognisi Nasional' }}</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mt-3">
                        {{ $cfg['sekolah_teladan']['title'] ?? 'Sekolah Teladan Nasional' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2 font-normal">
                        {{ $cfg['sekolah_teladan']['desc'] ?? 'Bukti nyata komitmen mutu, akreditasi paripurna BAN-PDM, dan rekognisi resmi dari kementerian serta lembaga internasional.' }}
                    </p>
                </div>

                <!-- 9 Awards & Partnerships Grid (Ministry Seal Crest Cards) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-6">
                    @foreach($cfg['sekolah_teladan']['awards'] ?? [] as $idx => $aw)
                    <div class="card-seal-crest flex items-start gap-3.5 sm:gap-4 group bg-white border border-slate-200/80 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.05)]" data-animate="fade-up" data-animate-delay="{{ ($idx % 3) + 1 }}">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-blue-50 border border-blue-200/80 flex items-center justify-center p-2 shrink-0 shadow-xs group-hover:scale-105 transition-transform">
                            <img src="{{ asset($aw['image']) }}" alt="{{ $aw['title'] }}" class="max-w-full max-h-full object-contain">
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 block mb-1">
                                {{ $aw['org'] ?? 'Kementerian & Mitra' }}
                            </span>
                            <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-snug group-hover:text-[#2563EB] transition-colors">
                                {{ $aw['title'] }}
                            </h3>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed font-normal">
                                {{ $aw['sub'] }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
