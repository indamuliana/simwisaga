        <!-- ── 4. Impact & Key Metrics Section (Wikrama Royal Archive) ── -->
        <section class="py-16 sm:py-24 lg:py-28 relative overflow-hidden bg-[#FAF9F6] text-slate-800" id="fakta">
            <!-- Background Ornaments: Soft Ambient Glow -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-100/40 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-amber-100/30 rounded-full blur-3xl"></div>
                <div class="absolute inset-0 bg-[radial-gradient(#CBD5E1_1px,transparent_1px)] [background-size:28px_28px] opacity-40"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16" data-animate="fade-up">
                    <span class="google-tag google-tag-blue shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-[#00519E]"></span>
                        <span>Impact &amp; Key Metrics &bull; Data &amp; Fakta</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mt-3 sm:mt-4">
                        Capaian Nyata yang Membanggakan
                    </h2>
                    <p class="mt-3 sm:mt-4 text-sm sm:text-lg text-slate-600 leading-relaxed font-normal">
                        Standar mutu dan konsistensi pendidikan vokasi kelas dunia yang diakui secara resmi di tingkat nasional dan internasional.
                    </p>
                </div>

                @php
                    $cardColors = [
                        0 => [
                            'color' => '#00519E',
                            'bg_badge' => '#EBF4FC',
                            'border_badge' => 'rgba(0, 81, 158, 0.25)',
                        ],
                        1 => [
                            'color' => '#1F513F',
                            'bg_badge' => '#E8F3EE',
                            'border_badge' => 'rgba(31, 81, 63, 0.25)',
                        ],
                        2 => [
                            'color' => '#D97706',
                            'bg_badge' => '#FEF9E7',
                            'border_badge' => 'rgba(217, 119, 6, 0.25)',
                        ],
                        3 => [
                            'color' => '#00305F',
                            'bg_badge' => '#EBF4FC',
                            'border_badge' => 'rgba(0, 48, 95, 0.25)',
                        ],
                    ];
                @endphp

                <!-- 4 Columns Metric Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($cfg['fast_facts'] ?? [] as $idx => $f)
                    @php
                        $cStyle = $cardColors[$idx % 4];
                        $rawNum = trim($f['number'] ?? '0');
                        $hasNum = preg_match('/^(\d+)(.*)$/', $rawNum, $numMatches);
                        $countVal = $hasNum ? $numMatches[1] : null;
                        $suffixVal = $hasNum ? $numMatches[2] : '';
                    @endphp
                    <div class="card-metric rounded-3xl overflow-hidden bg-white group flex flex-col justify-between" 
                         style="border-top: 4px solid {{ $cStyle['color'] }};"
                         data-animate="scale" 
                         data-animate-delay="{{ $idx + 1 }}">
                        <div>
                            <!-- Photo Header with Tag Badge -->
                            <div class="aspect-[16/10] w-full relative overflow-hidden bg-slate-100">
                                <img src="{{ class_exists(\App\Modules\AdministrasiWeb\Models\WebSetting::class) ? \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($f['image'] ?? null, 'images/web/hero-tjkt.jpg') : asset($f['image']) }}" 
                                     alt="{{ $f['label'] }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" 
                                     loading="lazy"
                                     decoding="async">
                                <span class="absolute top-3 left-3 text-[10px] font-extrabold px-3 py-1 rounded-full shadow-xs"
                                      style="background-color: {{ $cStyle['bg_badge'] }}; color: {{ $cStyle['color'] }}; border: 1px solid {{ $cStyle['border_badge'] }};">
                                    {{ $f['tag'] }}
                                </span>
                            </div>

                            <!-- Big Bold Hero Number & Content -->
                            <div class="p-6 pt-5">
                                @if($countVal !== null)
                                    <div class="jis-counter-number text-4xl sm:text-5xl font-extrabold mb-2 tracking-tight"
                                         style="color: {{ $cStyle['color'] }};"
                                         data-count="{{ $countVal }}"
                                         data-suffix="{{ $suffixVal }}">
                                        {{ $rawNum }}
                                    </div>
                                @else
                                    <div class="text-3xl sm:text-4xl font-extrabold mb-2 tracking-tight"
                                         style="color: {{ $cStyle['color'] }};">
                                        {{ $rawNum }}
                                    </div>
                                @endif
                                <h3 class="text-base font-bold mb-2 leading-snug text-slate-900 group-hover:text-[#00519E] transition-colors">
                                    {{ $f['label'] }}
                                </h3>
                                <p class="text-xs sm:text-sm leading-relaxed font-normal text-slate-600">
                                    {{ $f['desc'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Prestigious Wikrama CTA Button Connecting to Public Prestasi Showcase -->
                <div class="mt-12 sm:mt-16 text-center" data-animate="fade-up">
                    <a href="{{ url('/prestasi') }}" 
                       class="btn-jis-pill-gold !py-3.5 !px-8 text-sm sm:text-base font-extrabold shadow-lg inline-flex items-center gap-2.5 group">
                        <svg class="w-5 h-5 text-[#00305F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                        </svg>
                        <span>Jelajahi Seluruh Prestasi &amp; Penghargaan Resmi</span>
                        <span class="text-base font-bold group-hover:translate-x-1.5 transition-transform">&rarr;</span>
                    </a>
                </div>
            </div>
        </section>
