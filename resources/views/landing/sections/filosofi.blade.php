        <!-- ── 2. "Welcome to Wikrama" Signature Manifesto & Link & Match BMW Section ── -->
        <section class="pt-0 pb-6 sm:pb-8 lg:pb-10 relative overflow-hidden bg-white text-slate-800" id="filosofi">
            
            <!-- ── JIS Animated SVG Botanical Vine Divider ── -->
            <div class="relative w-full overflow-hidden py-4 sm:py-6 flex justify-center items-center pointer-events-none">
                <svg class="jis-vine-svg w-full max-w-4xl h-16 sm:h-20" viewBox="0 0 1000 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Main Vine Stem -->
                    <path class="jis-vine-path" 
                          d="M0,60 Q250,15 500,60 T1000,60" 
                          stroke="#1F513F"
                          stroke-width="2.5" 
                          stroke-linecap="round" 
                          stroke-dasharray="1200" 
                          stroke-dashoffset="1200" />
                    <!-- Tropical Leaves & Gold Accents -->
                    <path class="jis-vine-leaf"
                          d="M250,35 C240,15 270,10 275,30 C275,45 255,45 250,35 Z" 
                          fill="rgba(31, 81, 63, 0.2)"
                          stroke="#1F513F"
                          stroke-width="1.5" />
                    <path class="jis-vine-leaf"
                          d="M510,50 C525,30 550,40 545,55 C538,70 515,65 510,50 Z" 
                          fill="rgba(245, 216, 74, 0.35)"
                          stroke="#E2B714"
                          stroke-width="1.5" />
                    <path class="jis-vine-leaf"
                          d="M750,45 C735,25 765,20 770,40 C770,55 755,55 750,45 Z" 
                          fill="rgba(31, 81, 63, 0.2)"
                          stroke="#1F513F"
                          stroke-width="1.5" />
                </svg>
            </div>

            <!-- Background Atmospheric Ornaments (Crisp White + Subtle Navy & Forest Accents) -->
            <div class="absolute inset-0 z-0 pointer-events-none">
                <div class="absolute top-10 right-1/4 w-96 h-96 bg-blue-100/30 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-emerald-100/25 rounded-full blur-3xl"></div>
                <!-- Subtle dotted pattern -->
                <div class="absolute inset-0 bg-[radial-gradient(#E2E8F0_1px,transparent_1px)] [background-size:28px_28px] opacity-50"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10" data-animate>
                <!-- ── Manifesto Pendidikan Wikrama (Floating Pure White Card + JIS Navy Accent) ── -->
                <div class="relative p-6 sm:p-12 lg:p-14 rounded-3xl bg-white border border-slate-200/90 border-t-4 border-t-[#00519E] text-left sm:text-center w-full max-w-5xl mx-auto shadow-[0_15px_40px_-10px_rgba(0,48,95,0.06)] overflow-hidden group">
                    <!-- Subtle Watermark Quote Icon Ornament -->
                    <div class="absolute -top-6 -right-4 w-32 h-32 text-blue-900/5 pointer-events-none select-none font-serif text-9xl font-black">
                        &rdquo;
                    </div>

                    <!-- Header Pill Badge -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#EBF4FC] border border-blue-200 text-[#00519E] text-[11px] sm:text-xs font-bold uppercase tracking-wider mb-6">
                        <span class="w-2 h-2 rounded-full bg-[#00519E] animate-ping"></span>
                        <span>{{ $cfg['welcome']['tag'] ?? 'Manifesto Pendidikan Wikrama' }}</span>
                    </div>

                    <!-- Core Quote Text -->
                    <blockquote class="font-sans text-xl sm:text-2xl md:text-3xl lg:text-4xl text-[#232327] leading-snug sm:leading-relaxed font-extrabold tracking-tight max-w-4xl mx-auto">
                        &ldquo;{{ $cfg['welcome']['quote'] ?? 'Di SMK Wikrama 1 Garut, kami mempersiapkan anak bangsa dari berbagai daerah untuk menjadi yang terbaik dalam keahlian yang mereka tekuni, sekaligus menjadi pribadi yang membawa kemaslahatan terbaik bagi keluarga, masyarakat, dan masa depan peradaban.' }}&rdquo;
                    </blockquote>

                    <!-- Author & Institutional Signature -->
                    <div class="mt-8 sm:mt-10 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 max-w-4xl mx-auto">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-full bg-[#EBF4FC] border border-blue-200 flex items-center justify-center text-[#00519E] font-bold text-base shrink-0 shadow-xs">
                                🎓
                            </div>
                            <div class="text-left">
                                <span class="text-slate-900 font-bold text-sm sm:text-base tracking-wide block leading-tight">
                                    {{ $cfg['welcome']['author'] ?? 'Kunedi, S.Si. — Kepala SMK Wikrama 1 Garut' }}
                                </span>
                                <span class="text-slate-500 text-xs font-medium block mt-0.5">Pemimpin Lembaga &bull; SMK Wikrama 1 Garut</span>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-[#1F513F] bg-[#E8F3EE] px-4 py-1.5 rounded-full border border-emerald-300/40 shrink-0">
                            Piagam Integritas Karakter
                        </span>
                    </div>
                </div>

                <!-- ── Link & Match Industri • BMW (Bekerja, Melanjutkan, Wirausaha) ── -->
                <div class="mt-8 sm:mt-10 pt-6 border-t border-slate-200/80 w-full" data-animate>
                    <div class="text-center mb-3 sm:mb-4">
                        <span class="google-tag google-tag-blue mb-1 shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-[#00519E]"></span>
                            <span>{{ $cfg['mitra']['tag'] ?? 'Link & Match Industri • BMW (Bekerja, Melanjutkan, Wirausaha)' }}</span>
                        </span>
                    </div>

                    @php
                        $rawMitra = $cfg['mitra']['items'] ?? $cfg['mitra'] ?? [];
                        $activeMitra = collect($rawMitra)
                            ->filter(fn($m) => !isset($m['is_active']) || $m['is_active'])
                            ->values();
                    @endphp

                    <!-- Marquee Ticker: Berwarna, Tanpa Card, Tinggi Seragam -->
                    <div class="partner-marquee-container relative w-full overflow-hidden py-1.5 sm:py-2">
                        <!-- Fade Edges on Canvas -->
                        <div class="absolute left-0 inset-y-0 w-12 sm:w-20 bg-gradient-to-r from-white to-transparent z-20 pointer-events-none"></div>
                        <div class="absolute right-0 inset-y-0 w-12 sm:w-20 bg-gradient-to-l from-white to-transparent z-20 pointer-events-none"></div>

                        <div class="partner-marquee-track flex items-center hover:[animation-play-state:paused]">
                            <!-- First Loop (Full Color, Logo Left + Text Right, Compact Spacing) -->
                            <div class="flex items-center gap-3 sm:gap-4 lg:gap-5 shrink-0 pr-3 sm:pr-4 lg:pr-5">
                                @foreach($activeMitra as $m)
                                @php
                                    $mUrl = !empty($m['url']) && $m['url'] !== '#' ? $m['url'] : null;
                                    $mName = $m['name'] ?? 'Mitra Industri';
                                    $mText = $m['text'] ?? $mName;
                                    $mImg = \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($m['image'] ?? null, 'images/landing/mitra-huawei.png');
                                @endphp
                                <{{ $mUrl ? 'a href=' . e($mUrl) . ' target=_blank rel=noopener' : 'div' }} 
                                    class="partner-logo-item inline-flex items-center gap-2 sm:gap-2.5 shrink-0 px-2 sm:px-2.5 py-1 rounded-xl transition-all group/mitra hover:bg-slate-50 relative"
                                    title="{{ $mName }}">
                                    <div class="partner-logo-slot shrink-0 flex items-center justify-center self-center">
                                        <img src="{{ $mImg }}" 
                                             alt="{{ $mName }}" 
                                             class="partner-logo-img block my-auto">
                                    </div>
                                    @if(!empty(trim($mText)))
                                    <span class="partner-logo-text text-xs sm:text-[13px] font-semibold text-slate-700 group-hover/mitra:text-[#00519E] transition-colors text-left leading-snug whitespace-pre-line max-w-[130px] sm:max-w-[150px] select-none self-center my-auto">
                                        {{ $mText }}
                                    </span>
                                    @endif
                                    <!-- Tooltip Hover -->
                                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 pointer-events-none opacity-0 group-hover/mitra:opacity-100 transition-all duration-200 translate-y-1 group-hover/mitra:translate-y-0 z-40 whitespace-nowrap">
                                        <span class="text-[10px] font-bold py-1 px-2.5 rounded-lg bg-slate-900 text-white shadow-xl">
                                            {{ $mName }}
                                        </span>
                                    </div>
                                </{{ $mUrl ? 'a' : 'div' }}>
                                @endforeach
                            </div>

                            <!-- Second Loop for 100% Seamless Infinite Animation -->
                            <div class="flex items-center gap-3 sm:gap-4 lg:gap-5 shrink-0 pr-3 sm:pr-4 lg:pr-5" aria-hidden="true">
                                @foreach($activeMitra as $m)
                                @php
                                    $mUrl = !empty($m['url']) && $m['url'] !== '#' ? $m['url'] : null;
                                    $mName = $m['name'] ?? 'Mitra Industri';
                                    $mText = $m['text'] ?? $mName;
                                    $mImg = \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($m['image'] ?? null, 'images/landing/mitra-huawei.png');
                                @endphp
                                <{{ $mUrl ? 'a href=' . e($mUrl) . ' target=_blank rel=noopener' : 'div' }} 
                                    class="partner-logo-item inline-flex items-center gap-2 sm:gap-2.5 shrink-0 px-2 sm:px-2.5 py-1 rounded-xl transition-all group/mitra hover:bg-slate-50 relative"
                                    title="{{ $mName }}">
                                    <div class="partner-logo-slot shrink-0 flex items-center justify-center self-center">
                                        <img src="{{ $mImg }}" 
                                             alt="{{ $mName }}" 
                                             class="partner-logo-img block my-auto">
                                    </div>
                                    @if(!empty(trim($mText)))
                                    <span class="partner-logo-text text-xs sm:text-[13px] font-semibold text-slate-700 group-hover/mitra:text-[#00519E] transition-colors text-left leading-snug whitespace-pre-line max-w-[130px] sm:max-w-[150px] select-none self-center my-auto">
                                        {{ $mText }}
                                    </span>
                                    @endif
                                    <!-- Tooltip Hover -->
                                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 pointer-events-none opacity-0 group-hover/mitra:opacity-100 transition-all duration-200 translate-y-1 group-hover/mitra:translate-y-0 z-40 whitespace-nowrap">
                                        <span class="text-[10px] font-bold py-1 px-2.5 rounded-lg bg-slate-900 text-white shadow-xl">
                                            {{ $mName }}
                                        </span>
                                    </div>
                                </{{ $mUrl ? 'a' : 'div' }}>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
