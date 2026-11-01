        <!-- ── 5. 3 Pilar Kejuruan / 4 Jurusan Unggulan (Wikrama Royal Archive) ── -->
        <section class="py-14 sm:py-20 lg:py-24 relative overflow-hidden bg-[#FAF9F6] text-slate-800" id="jurusan">
            <!-- Background Ornaments: Isometric Tech Grid & Ambient Glow Orbs -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute inset-0 bg-[radial-gradient(#CBD5E1_1px,transparent_1px)] [background-size:28px_28px] opacity-40"></div>
                <div class="absolute -top-32 -left-20 w-96 h-96 bg-blue-100/40 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-32 -right-20 w-96 h-96 bg-amber-100/30 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14" data-animate="fade-up">
                    <span class="google-tag google-tag-blue shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-[#00519E]"></span>
                        <span>Program Keahlian Vokasi Terakreditasi Unggul</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mt-3 sm:mt-4">
                        4 Pilar Kompetensi Masa Depan
                    </h2>
                    <p class="mt-3 sm:mt-4 text-sm sm:text-lg text-slate-600 leading-relaxed font-normal">
                        Kurikulum link-and-match dengan industri global dalam bidang <strong class="text-[#00519E]">Teknologi Informasi &amp; AI</strong>, <strong class="text-[#D97706]">Bisnis Digital</strong>, dan <strong class="text-[#9F1239]">Pariwisata &amp; Hospitaliti</strong>.
                    </p>
                </div>

                @php
                    $jurColors = [
                        'PPLG' => [
                            'color' => '#00519E',
                            'bg_light' => '#EBF4FC',
                            'border_light' => 'rgba(0, 81, 158, 0.25)',
                        ],
                        'TJKT' => [
                            'color' => '#1F513F',
                            'bg_light' => '#E8F3EE',
                            'border_light' => 'rgba(31, 81, 63, 0.25)',
                        ],
                        'PMN' => [
                            'color' => '#D97706',
                            'bg_light' => '#FEF9E7',
                            'border_light' => 'rgba(217, 119, 6, 0.25)',
                        ],
                        'HTL' => [
                            'color' => '#9F1239',
                            'bg_light' => '#FDF2F4',
                            'border_light' => 'rgba(159, 18, 57, 0.25)',
                        ],
                    ];
                @endphp

                <!-- 4 Jurusan Cards (Blueprint Spec Card Style with Royal Palette) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($cfg['jurusans'] ?? [] as $idx => $jur)
                    @php
                        $jc = $jurColors[$jur['code']] ?? [
                            'color' => '#00519E',
                            'bg_light' => '#EBF4FC',
                            'border_light' => 'rgba(0, 81, 158, 0.25)',
                        ];
                    @endphp
                    <div class="card-blueprint rounded-3xl overflow-hidden flex flex-col justify-between h-full group bg-white"
                         style="border-top: 4px solid {{ $jc['color'] }};"
                         data-animate="fade-up" data-animate-delay="{{ $idx + 1 }}">
                        <div>
                            <!-- Photo Header -->
                            <div class="aspect-[16/10] w-full relative overflow-hidden bg-slate-100">
                                <img src="{{ asset($jur['image']) }}" 
                                     alt="{{ $jur['name'] }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" 
                                     loading="lazy"
                                     decoding="async">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
                                <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-extrabold shadow-sm"
                                      style="background-color: {{ $jc['bg_light'] }}; color: {{ $jc['color'] }}; border: 1px solid {{ $jc['border_light'] }};">
                                    {{ $jur['badge'] }}
                                </span>
                                <span class="absolute bottom-3 left-3 px-2.5 py-0.5 rounded-full font-mono-code text-xs font-bold text-white shadow-sm"
                                      style="background-color: {{ $jc['color'] }};">
                                    {{ $jur['code'] }}
                                </span>
                            </div>

                            <!-- Body Info -->
                            <div class="p-5 pt-4">
                                <h3 class="text-base sm:text-lg font-bold mb-2 leading-snug text-slate-900 group-hover:text-[#00519E] transition-colors">
                                    {{ $jur['name'] }}
                                </h3>
                                <p class="text-xs text-slate-600 leading-relaxed mb-4 font-normal">
                                    {{ $jur['desc'] }}
                                </p>
                                
                                <!-- Competency Chips -->
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($jur['chips'] ?? [] as $chip)
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold"
                                          style="background-color: {{ $jc['bg_light'] }}; color: {{ $jc['color'] }}; border: 1px solid {{ $jc['border_light'] }};">
                                        {{ $chip }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Footer Career Pathways & Link to Detail -->
                        <div class="px-5 py-3.5 border-t border-slate-100 flex flex-col justify-between gap-2.5 bg-slate-50/80">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider block mb-0.5 text-slate-500">Karier Lulusan:</span>
                                <p class="text-[11px] font-semibold text-slate-800 leading-tight line-clamp-2">
                                    {{ $jur['careers'] }}
                                </p>
                            </div>
                            @php
                                $detailSlug = match(strtolower($jur['code'])) {
                                    'pplg' => 'pplg',
                                    'tjkt' => 'tjkt',
                                    'pmn' => 'pmn',
                                    'htl' => 'htl',
                                    default => strtolower($jur['code']),
                                };
                            @endphp
                            <a href="{{ url('/jurusan/' . $detailSlug) }}" 
                               class="inline-flex items-center justify-between text-xs font-bold pt-2 border-t border-slate-200/80 transition-colors group-hover:underline"
                               style="color: {{ $jc['color'] }};">
                                <span>Kurikulum, Lab &amp; Sertifikasi</span>
                                <span class="group-hover-arrow">&rarr;</span>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
