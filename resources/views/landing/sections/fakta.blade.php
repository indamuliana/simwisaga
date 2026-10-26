        <!-- ── 4. Impact & Key Metrics Section (Bright & Warm Education with Crisp Cards) ── -->
        <section class="py-16 sm:py-24 lg:py-28 relative overflow-hidden" id="fakta" style="background-color: #FAF9F6; color: #1E293B;">
            <!-- Background Ornaments: Soft Ambient Glow -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-100/50 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-amber-100/40 rounded-full blur-3xl"></div>
                <div class="absolute inset-0 bg-[radial-gradient(#CBD5E1_1px,transparent_1px)] [background-size:28px_28px] opacity-40"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16" data-animate="fade-up">
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider mb-4 shadow-xs"
                          style="background-color: #EAF4FE; color: #147EFB; border: 1px solid rgba(20, 126, 251, 0.25);">
                        <span class="w-2 h-2 rounded-full bg-[#147EFB] animate-pulse"></span>
                        <span>Impact &amp; Key Metrics &bull; Data &amp; Fakta</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight mt-2" style="color: #00305F;">
                        Capaian Nyata yang Membanggakan
                    </h2>
                    <p class="mt-3 sm:mt-4 text-sm sm:text-lg leading-relaxed font-normal" style="color: #8E8E93;">
                        Standar mutu dan konsistensi pendidikan vokasi kelas dunia yang diakui secara resmi di tingkat nasional dan internasional.
                    </p>
                </div>

                @php
                    $cardColors = [
                        0 => [
                            'color' => '#147EFB',        // Blue
                            'bg_badge' => '#EAF4FE',
                            'border_badge' => 'rgba(20, 126, 251, 0.3)',
                        ],
                        1 => [
                            'color' => '#53D769',        // Green
                            'bg_badge' => '#EBFBF0',
                            'border_badge' => 'rgba(83, 215, 105, 0.3)',
                        ],
                        2 => [
                            'color' => '#FD9426',        // Orange
                            'bg_badge' => '#FFF4E8',
                            'border_badge' => 'rgba(253, 148, 38, 0.3)',
                        ],
                        3 => [
                            'color' => '#FC3158',        // Pink/Red-Pink
                            'bg_badge' => '#FEECEF',
                            'border_badge' => 'rgba(252, 49, 88, 0.3)',
                        ],
                    ];
                @endphp

                <!-- 4 Columns Metric Cards (High Contrast White Card Surface) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($cfg['fast_facts'] ?? [] as $idx => $f)
                    @php
                        $cStyle = $cardColors[$idx % 4];
                        $rawNum = trim($f['number'] ?? '0');
                        $hasNum = preg_match('/^(\d+)(.*)$/', $rawNum, $numMatches);
                        $countVal = $hasNum ? $numMatches[1] : null;
                        $suffixVal = $hasNum ? $numMatches[2] : '';
                    @endphp
                    <div class="card-metric rounded-3xl overflow-hidden transition-all duration-300 hover:-translate-y-2 shadow-sm hover:shadow-xl group flex flex-col justify-between" 
                         style="background-color: #FFFFFF !important; border: 1px solid rgba(226, 232, 240, 0.9); border-top: 4px solid {{ $cStyle['color'] }};"
                         data-animate="scale" 
                         data-animate-delay="{{ $idx + 1 }}"
                         data-cursor="METRIK">
                        <div>
                            <!-- Photo Header with Tag Badge -->
                            <div class="aspect-[16/10] w-full relative overflow-hidden bg-slate-100">
                                <img src="{{ asset($f['image']) }}" alt="{{ $f['label'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" loading="lazy">
                                <span class="absolute top-3 left-3 text-[10px] font-extrabold px-3 py-1 rounded-full shadow-xs"
                                      style="background-color: {{ $cStyle['bg_badge'] }}; color: {{ $cStyle['color'] }}; border: 1px solid {{ $cStyle['border_badge'] }};">
                                    {{ $f['tag'] }}
                                </span>
                            </div>

                            <!-- Big Bold Hero Number & Content with Curated Text Palette -->
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
                                <h3 class="text-base font-bold mb-2 leading-snug transition-colors"
                                    style="color: #1E293B;">
                                    {{ $f['label'] }}
                                </h3>
                                <p class="text-xs sm:text-sm leading-relaxed font-normal" style="color: #8E8E93;">
                                    {{ $f['desc'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
