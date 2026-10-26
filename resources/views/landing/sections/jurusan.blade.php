        <!-- ── 5. 3 Pilar Kejuruan / 4 Jurusan Unggulan (Bright & Warm Education) ── -->
        <section class="py-12 sm:py-20 lg:py-24 relative overflow-hidden" id="jurusan" style="background-color: #FAF9F6; color: #1E293B;" x-data="{ activePillar: 'all' }" data-mouse-glow>
            <!-- Background Ornaments: Isometric Tech Grid & Ambient Glow Orbs -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute inset-0 bg-[radial-gradient(#CBD5E1_1px,transparent_1px)] [background-size:28px_28px] opacity-40"></div>
                <div class="absolute -top-32 -left-20 w-96 h-96 bg-blue-100/50 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-32 -right-20 w-96 h-96 bg-amber-100/40 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14" data-animate="fade-up">
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider mb-4 shadow-xs"
                          style="background-color: #EAF4FE; color: #147EFB; border: 1px solid rgba(20, 126, 251, 0.25);">
                        <span class="w-2 h-2 rounded-full bg-[#147EFB] animate-pulse"></span>
                        <span>Program Keahlian Vokasi Terakreditasi</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight mt-3 sm:mt-4" style="color: #00305F;">
                        3 Pilar Kompetensi Masa Depan
                    </h2>
                    <p class="mt-3 sm:mt-4 text-sm sm:text-lg leading-relaxed font-normal" style="color: #8E8E93;">
                        Kurikulum link-and-match dengan industri terkemuka dalam bidang <strong style="color: #147EFB;">Teknologi Informasi &amp; AI</strong>, <strong style="color: #FD9426;">Bisnis Digital</strong>, dan <strong style="color: #FC3158;">Pariwisata &amp; Hospitaliti</strong>.
                    </p>

                    <!-- Filter Buttons (Pill Navigation) -->
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-1.5 sm:gap-2">
                        <button type="button" 
                                @click="activePillar = 'all'" 
                                :class="activePillar === 'all' ? 'font-bold shadow-md' : 'bg-white text-slate-700 font-bold border border-slate-200 hover:border-[#147EFB] hover:text-[#147EFB] hover:bg-blue-50'"
                                :style="activePillar === 'all' ? 'background-color: #147EFB; color: #ffffff !important;' : ''"
                                class="px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer shadow-xs">
                            Semua Program Keahlian (4)
                        </button>
                        <button type="button" 
                                @click="activePillar = 'tech'" 
                                :class="activePillar === 'tech' ? 'font-bold shadow-md' : 'bg-white text-slate-700 font-bold border border-slate-200 hover:border-[#147EFB] hover:text-[#147EFB] hover:bg-blue-50'"
                                :style="activePillar === 'tech' ? 'background-color: #147EFB; color: #ffffff !important;' : ''"
                                class="px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer flex items-center gap-1.5 shadow-xs">
                            <span>💻</span>
                            <span>Teknologi (PPLG &bull; TJKT)</span>
                        </button>
                        <button type="button" 
                                @click="activePillar = 'biz'" 
                                :class="activePillar === 'biz' ? 'font-bold shadow-md' : 'bg-white text-slate-700 font-bold border border-slate-200 hover:border-[#FD9426] hover:text-[#FD9426] hover:bg-orange-50'"
                                :style="activePillar === 'biz' ? 'background-color: #FD9426; color: #ffffff !important;' : ''"
                                class="px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer flex items-center gap-1.5 shadow-xs">
                            <span>📈</span>
                            <span>Bisnis Digital (PMN)</span>
                        </button>
                        <button type="button" 
                                @click="activePillar = 'tour'" 
                                :class="activePillar === 'tour' ? 'font-bold shadow-md' : 'bg-white text-slate-700 font-bold border border-slate-200 hover:border-[#FC3158] hover:text-[#FC3158] hover:bg-rose-50'"
                                :style="activePillar === 'tour' ? 'background-color: #FC3158; color: #ffffff !important;' : ''"
                                class="px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer flex items-center gap-1.5 shadow-xs">
                            <span>🛎️</span>
                            <span>Perhotelan (HTL)</span>
                        </button>
                    </div>
                </div>

                @php
                    $jurColors = [
                        'PPLG' => [
                            'color' => '#147EFB',        // Blue
                            'bg_light' => '#EAF4FE',
                            'border_light' => 'rgba(20, 126, 251, 0.3)',
                        ],
                        'TJKT' => [
                            'color' => '#53D769',        // Green
                            'bg_light' => '#EBFBF0',
                            'border_light' => 'rgba(83, 215, 105, 0.3)',
                        ],
                        'PMN' => [
                            'color' => '#FD9426',        // Orange
                            'bg_light' => '#FFF4E8',
                            'border_light' => 'rgba(253, 148, 38, 0.3)',
                        ],
                        'HTL' => [
                            'color' => '#FC3158',        // Pink/Red-Pink
                            'bg_light' => '#FEECEF',
                            'border_light' => 'rgba(252, 49, 88, 0.3)',
                        ],
                    ];
                @endphp

                <!-- 4 Jurusan Cards (Blueprint Spec Card Style with User Palette) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($cfg['jurusans'] ?? [] as $idx => $jur)
                    @php
                        $jc = $jurColors[$jur['code']] ?? [
                            'color' => '#147EFB',
                            'bg_light' => '#EAF4FE',
                            'border_light' => 'rgba(20, 126, 251, 0.3)',
                        ];
                    @endphp
                    <div x-show="activePillar === 'all' || activePillar === '{{ $jur['pillar'] }}'"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="card-blueprint rounded-3xl overflow-hidden flex flex-col justify-between h-full group shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2"
                         style="background-color: #FFFFFF !important; border: 1px solid rgba(226, 232, 240, 0.9); border-top: 4px solid {{ $jc['color'] }};"
                         data-animate="fade-up" data-animate-delay="{{ $idx + 1 }}">
                        <div>
                            <!-- Photo Header -->
                            <div class="aspect-[16/10] w-full relative overflow-hidden bg-slate-100">
                                <img src="{{ asset($jur['image']) }}" alt="{{ $jur['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
                                <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-extrabold shadow-sm backdrop-blur-xs"
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
                                <h3 class="text-base sm:text-lg font-bold mb-2 leading-snug transition-colors"
                                    style="color: #1E293B;">
                                    {{ $jur['name'] }}
                                </h3>
                                <p class="text-xs leading-relaxed mb-4 font-normal" style="color: #8E8E93;">
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

                        <!-- Footer Career Pathways -->
                        <div class="px-5 py-3.5 border-t border-slate-100" style="background-color: #F8F9FA;">
                            <span class="text-[10px] font-bold uppercase tracking-wider block mb-0.5" style="color: #8E8E93;">Karier Lulusan:</span>
                            <p class="text-[11px] font-semibold leading-tight" style="color: #1E293B;">
                                {{ $jur['careers'] }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
