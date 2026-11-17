        <!-- ── 2. "Welcome to Wikrama" Signature Manifesto & Link & Match BMW Section ── -->
        <section class="py-16 sm:py-20 relative overflow-hidden bg-white text-slate-800 border-b border-slate-200/80 scroll-mt-20" id="filosofi">
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10" data-animate="fade-up">
                
                <!-- ── Manifesto Pendidikan Wikrama (Dignified Architectural Plate) ── -->
                <div class="relative p-6 sm:p-10 lg:p-12 rounded-3xl bg-[#FAF9F6] border border-slate-200/90 text-center w-full max-w-4xl mx-auto shadow-xs overflow-hidden">
                    
                    <!-- Header Pill Badge -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-slate-200 text-[#002752] text-xs font-semibold mb-6 mx-auto shadow-2xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#002752]"></span>
                        <span>{{ $cfg['welcome']['tag'] ?? 'Manifesto Pendidikan Wikrama' }}</span>
                    </div>

                    <!-- Core Quote Text (Tenang, Proporsional, Nyaman Dibaca) -->
                    <blockquote class="text-lg sm:text-2xl md:text-[1.65rem] text-[#002752] leading-relaxed font-bold tracking-tight max-w-3xl mx-auto text-center">
                        &ldquo;{{ $cfg['welcome']['quote'] ?? 'Di SMK Wikrama 1 Garut, kami mempersiapkan anak bangsa dari berbagai daerah untuk menjadi yang terbaik dalam keahlian yang mereka tekuni, sekaligus menjadi pribadi yang membawa kemaslahatan terbaik bagi keluarga, masyarakat, dan masa depan peradaban.' }}&rdquo;
                    </blockquote>

                    <!-- Author & Institutional Signature -->
                    <div class="mt-8 pt-6 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-4 max-w-3xl mx-auto text-center sm:text-left">
                        <div class="flex items-center gap-4 text-left">
                            @php
                                $principalImgPath = $cfg['principal']['image'] ?? null;
                                $principalImgUrl = class_exists(\App\Modules\AdministrasiWeb\Models\WebSetting::class)
                                    ? \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($principalImgPath, 'images/landing/kepala-sekolah-kunedi.png')
                                    : asset('images/landing/kepala-sekolah-kunedi.png');
                                $principalName = !empty($cfg['principal']['name']) ? $cfg['principal']['name'] : 'Kunedi, S.Si.';
                            @endphp
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-slate-100 border border-slate-200 shrink-0 shadow-xs">
                                <img src="{{ $principalImgUrl }}" 
                                     alt="{{ $principalName }}" 
                                     class="w-full h-full object-cover object-top"
                                     onerror="this.src='{{ asset('images/landing/kepala-sekolah-kunedi.png') }}'">
                            </div>
                            <div>
                                <span class="text-slate-900 font-bold text-sm sm:text-base tracking-wide block leading-tight">
                                    {{ $cfg['welcome']['author'] ?? ($principalName . ' — Kepala SMK Wikrama 1 Garut') }}
                                </span>
                                <span class="text-slate-500 text-xs font-medium block mt-0.5">Pemimpin Lembaga &bull; SMK Wikrama 1 Garut</span>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-[#002752] bg-white px-4 py-1.5 rounded-full border border-slate-200/80 shrink-0 shadow-2xs">
                            Piagam Integritas Karakter
                        </span>
                    </div>
                </div>

                <!-- ── Link & Match Industri • BMW (Bekerja, Melanjutkan, Wirausaha) ── -->
                <div class="mt-12 pt-8 border-t border-slate-200/80 w-full" data-animate="fade-up">
                    @if(!empty(trim($cfg['mitra']['tag'] ?? '')))
                    <div class="text-center mb-4">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF9F6] text-slate-700 border border-slate-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#002752]"></span>
                            <span>{{ $cfg['mitra']['tag'] }}</span>
                        </span>
                    </div>
                    @endif

                    @php
                        $rawMitra = $cfg['mitra']['items'] ?? $cfg['mitra'] ?? [];
                        $activeMitra = collect($rawMitra)
                            ->filter(fn($m) => !isset($m['is_active']) || $m['is_active'])
                            ->values();
                    @endphp

                    <!-- Marquee Ticker: Clean Logo Strip (Hanya Gambar, Tinggi Seragam, Lebar Menyesuaikan, Jarak Seragam) -->
                    <div class="partner-marquee-container relative w-full overflow-hidden py-3 sm:py-4">
                        <!-- Fade Edges on Canvas -->
                        <div class="absolute left-0 inset-y-0 w-12 sm:w-20 bg-gradient-to-r from-white to-transparent z-20 pointer-events-none"></div>
                        <div class="absolute right-0 inset-y-0 w-12 sm:w-20 bg-gradient-to-l from-white to-transparent z-20 pointer-events-none"></div>

                        <div class="partner-marquee-track flex items-center hover:[animation-play-state:paused]">
                            <!-- First Loop (Hanya Gambar, Tinggi Seragam, Jarak Antar Logo Konsisten) -->
                            <div class="partner-loop-list">
                                @foreach($activeMitra as $m)
                                @php
                                    $mUrl = !empty($m['url']) && $m['url'] !== '#' ? $m['url'] : null;
                                    $mName = $m['name'] ?? 'Mitra Industri';
                                    $mImg = \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($m['image'] ?? null, 'images/landing/mitra-huawei.png');
                                @endphp
                                <{{ $mUrl ? 'a href=' . e($mUrl) . ' target=_blank rel=noopener' : 'div' }} 
                                    class="partner-logo-item inline-flex items-center justify-center shrink-0 group/mitra relative cursor-pointer"
                                    title="{{ $mName }}">
                                    <img src="{{ $mImg }}" 
                                         alt="{{ $mName }}" 
                                         class="partner-logo-img h-8 sm:h-9 w-auto object-contain grayscale hover:grayscale-0 opacity-70 hover:opacity-100 transition-all duration-300"
                                         loading="lazy"
                                         decoding="async">
                                    <!-- Tooltip Hover -->
                                    <div class="absolute -top-9 left-1/2 -translate-x-1/2 pointer-events-none opacity-0 group-hover/mitra:opacity-100 transition-all duration-200 translate-y-1 group-hover/mitra:translate-y-0 z-40 whitespace-nowrap">
                                        <span class="text-[10px] font-bold py-1 px-2.5 rounded-lg bg-slate-900 text-white shadow-xl">
                                            {{ $mName }}
                                        </span>
                                    </div>
                                </{{ $mUrl ? 'a' : 'div' }}>
                                @endforeach
                            </div>

                            <!-- Second Loop for 100% Seamless Infinite Animation -->
                            <div class="partner-loop-list" aria-hidden="true">
                                @foreach($activeMitra as $m)
                                @php
                                    $mUrl = !empty($m['url']) && $m['url'] !== '#' ? $m['url'] : null;
                                    $mName = $m['name'] ?? 'Mitra Industri';
                                    $mImg = \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($m['image'] ?? null, 'images/landing/mitra-huawei.png');
                                @endphp
                                <{{ $mUrl ? 'a href=' . e($mUrl) . ' target=_blank rel=noopener' : 'div' }} 
                                    class="partner-logo-item inline-flex items-center justify-center shrink-0 group/mitra relative cursor-pointer"
                                    title="{{ $mName }}">
                                    <img src="{{ $mImg }}" 
                                         alt="{{ $mName }}" 
                                         class="partner-logo-img h-8 sm:h-9 w-auto object-contain grayscale hover:grayscale-0 opacity-70 hover:opacity-100 transition-all duration-300"
                                         loading="lazy"
                                         decoding="async">
                                    <!-- Tooltip Hover -->
                                    <div class="absolute -top-9 left-1/2 -translate-x-1/2 pointer-events-none opacity-0 group-hover/mitra:opacity-100 transition-all duration-200 translate-y-1 group-hover/mitra:translate-y-0 z-40 whitespace-nowrap">
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