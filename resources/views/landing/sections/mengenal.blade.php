        <!-- ── 3. "Get to Know Wikrama" Destination Card Showcase (Gaya Visual Rekomendasi Sesuai Referensi) ── -->
        <section class="py-14 sm:py-20 lg:py-24 bg-[#FAF9F6] text-slate-800 relative overflow-hidden" id="mengenal"
                 x-data="{
                     scrollSlider(direction) {
                         const slider = document.getElementById('know-slider-' + activeKnowTab);
                         if (slider) {
                             const step = Math.min(slider.clientWidth * 0.8, 360);
                             slider.scrollBy({ left: direction * step, behavior: 'smooth' });
                         }
                     }
                 }">
            <!-- Background Ornaments: Dot Matrix & Ambient Glow Orbs -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute inset-0 bg-[radial-gradient(#CBD5E1_1px,transparent_1px)] [background-size:24px_24px] opacity-40"></div>
                <div class="absolute -top-32 right-10 w-96 h-96 bg-blue-100/30 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-32 left-10 w-96 h-96 bg-amber-100/30 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <!-- Section Header -->
                <div class="text-center max-w-3xl mx-auto mb-8 sm:mb-12" data-animate="fade-up">
                    <!-- Minimalist Soft Pill Badge Tag -->
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-100 border border-slate-200/80 text-slate-700 text-xs font-semibold shadow-2xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#00519E]"></span>
                        <span>{{ $cfg['get_to_know']['tag'] ?? 'Mengenal Lebih Dekat' }}</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mt-3 sm:mt-4">
                        {{ $cfg['get_to_know']['title'] ?? 'Menemukan Potensi Sejati di Wikrama' }}
                    </h2>
                    <p class="mt-3 sm:mt-4 text-sm sm:text-base text-slate-600 leading-relaxed font-normal">
                        {{ $cfg['get_to_know']['subtitle'] ?? 'Jelajahi ekosistem pembelajaran holistik yang menggabungkan keunggulan akademik, riset teknologi industri, dan pembinaan kepemimpinan islami.' }}
                    </p>
                </div>

                <!-- Minimalist Underline Tab Bar & Navigation Controls (Sesuai Gambar Referensi) -->
                <div class="flex items-center justify-between border-b border-slate-200/90 pb-0 gap-4 mb-6 sm:mb-8" data-animate="fade-up">
                    <!-- Category Tabs (Underline Active Indicator) -->
                    <div class="flex items-center gap-6 sm:gap-8 overflow-x-auto no-scrollbar py-1">
                        @foreach($cfg['get_to_know']['tabs'] ?? [] as $tabKey => $tabData)
                        <button type="button" 
                                @click="activeKnowTab = '{{ $tabKey }}'"
                                :class="activeKnowTab === '{{ $tabKey }}' 
                                    ? 'border-slate-900 text-slate-900 font-bold border-b-2' 
                                    : 'border-transparent text-slate-400 hover:text-slate-700 font-medium border-b-2'"
                                class="pb-3 text-sm sm:text-base whitespace-nowrap transition-colors duration-200 cursor-pointer -mb-px">
                            {{ $tabData['name'] ?? ucfirst($tabKey) }}
                        </button>
                        @endforeach
                    </div>

                    <!-- Circular Navigation Arrows (Prev / Next Slider) -->
                    <div class="flex items-center gap-2 shrink-0 pb-2">
                        <button type="button"
                                @click="scrollSlider(-1)"
                                aria-label="Geser ke kiri"
                                class="w-8 h-8 sm:w-9 sm:h-9 rounded-full border border-slate-300 hover:border-slate-800 text-slate-600 hover:text-slate-900 flex items-center justify-center transition-colors duration-200 bg-white hover:bg-slate-50 cursor-pointer shadow-2xs">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button type="button"
                                @click="scrollSlider(1)"
                                aria-label="Geser ke kanan"
                                class="w-8 h-8 sm:w-9 sm:h-9 rounded-full border border-slate-300 hover:border-slate-800 text-slate-600 hover:text-slate-900 flex items-center justify-center transition-colors duration-200 bg-white hover:bg-slate-50 cursor-pointer shadow-2xs">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Card Slider Track for Each Tab -->
                @foreach($cfg['get_to_know']['tabs'] ?? [] as $tabKey => $tabData)
                <div x-show="activeKnowTab === '{{ $tabKey }}'"
                     id="know-slider-{{ $tabKey }}"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-3"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="flex items-stretch gap-5 sm:gap-6 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory py-2 -mx-4 px-4 sm:mx-0 sm:px-0">
                    @foreach($tabData['cards'] ?? [] as $idx => $c)
                    @php
                        $cardImg = \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($c['image'] ?? null, 'images/web/campus-environment.jpg');
                        $cardUrl = !empty($c['url']) ? $c['url'] : '#';
                        $isExternal = str_starts_with($cardUrl, 'http');
                    @endphp
                    <a href="{{ $cardUrl }}"
                       {{ $isExternal ? 'target="_blank" rel="noopener"' : '' }}
                       class="group relative flex-none w-[78vw] sm:w-[290px] md:w-[310px] lg:min-w-[260px] lg:flex-1 h-[410px] sm:h-[440px] rounded-3xl overflow-hidden shadow-[0_10px_25px_-5px_rgba(0,0,0,0.12)] snap-start block text-left bg-slate-900 transition-transform duration-300 hover:-translate-y-1.5 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#00519E]">
                        
                        <!-- Full Cover Background Image -->
                        <img src="{{ $cardImg }}" 
                             alt="{{ $c['title'] ?? 'Program Wikrama' }}" 
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" 
                             loading="lazy"
                             decoding="async">

                        <!-- Multi-gradient Bottom Dark Overlay for Optimal Readability -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/45 to-transparent pointer-events-none"></div>

                        <!-- Top Right Mini Hover Arrow -->
                        <div class="absolute top-3.5 right-3.5 z-10">
                            <span class="w-8 h-8 rounded-full bg-black/25 backdrop-blur-md border border-white/20 flex items-center justify-center text-white/90 group-hover:bg-[#00519E] group-hover:border-[#00519E] group-hover:text-white transition-colors duration-200 shadow-sm">
                                <svg class="w-3.5 h-3.5 transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </span>
                        </div>

                        <!-- Bottom Content Overlay (Sesuai Gambar Referensi) -->
                        <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6 z-10 flex flex-col justify-end">
                            <!-- Card Title -->
                            <h3 class="text-base sm:text-lg font-bold text-white mb-2 leading-snug group-hover:text-amber-300 transition-colors duration-200">
                                {{ $c['title'] }}
                            </h3>

                            <!-- Card Description -->
                            <p class="text-xs text-slate-200/90 line-clamp-2 leading-relaxed mb-4 font-normal">
                                {{ $c['desc'] }}
                            </p>

                            <!-- Bottom Meta Row (Kiri: Badge Kategori & Pin Ikon | Kanan: Tombol CTA) -->
                            <div class="pt-3 border-t border-white/15 flex items-center justify-between gap-2">
                                <!-- Left: Pin / Tag Info -->
                                <div class="inline-flex items-center gap-1.5 text-xs text-slate-200 font-medium min-w-0">
                                    <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="truncate">
                                        {{ $c['badge'] ?? 'Wikrama Garut' }}
                                    </span>
                                </div>

                                <!-- Right: CTA Action Link -->
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-300 group-hover:text-amber-200 transition-colors shrink-0">
                                    <span>{{ $c['cta'] ?? 'Detail' }}</span>
                                    <span class="transform group-hover:translate-x-1 transition-transform duration-200">&rarr;</span>
                                </span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @endforeach

            </div>
        </section>
