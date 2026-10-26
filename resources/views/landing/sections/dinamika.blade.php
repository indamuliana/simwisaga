        <!-- ── 6. "Dinamika Kehidupan & Prestasi Siswa" (Student Life & Moments - JIS Panorama Ribbon) ── -->
        @php
            $studentMomentsData = $cfg['student_moments'] ?? [];
            $rawItems = $studentMomentsData['items'] ?? [];
            $activeCards = array_values(array_filter($rawItems, fn($it) => ($it['is_active'] ?? true) === true));
            $softColors = [
                '#A1DCEC', // Soft Blue
                '#FDE293', // Soft Yellow
                '#FFB07C', // Soft Orange
                '#FBC4D6', // Soft Pink
                '#A2E8B9', // Soft Green
                '#D5C4F7', // Soft Purple
            ];
        @endphp
        <section class="py-14 sm:py-20 lg:py-24 bg-[#FAF9F6] text-slate-800 relative overflow-hidden" 
                 id="dinamika-siswa" 
                 x-data="{
                     scrollAnimId: null,
                     getStep() {
                         const track = this.$refs.cardTrack;
                         if (!track || !track.firstElementChild) return 290;
                         return track.firstElementChild.offsetWidth;
                     },
                     smoothScroll(delta) {
                         const track = this.$refs.cardTrack;
                         if (!track) return;
                         
                         if (this.scrollAnimId) {
                             cancelAnimationFrame(this.scrollAnimId);
                         }
                         
                         const start = track.scrollLeft;
                         const maxScroll = track.scrollWidth - track.clientWidth;
                         const target = Math.max(0, Math.min(start + delta, maxScroll));
                         const distance = target - start;
                         
                         if (Math.abs(distance) < 2) return;
                         
                         const duration = 500;
                         const startTime = performance.now();
                         track.style.scrollSnapType = 'none';
                         track.style.scrollBehavior = 'auto';
                         
                         // Smooth decelerating cubic curve
                         const easeOutCubic = (t) => (--t) * t * t + 1;
                         
                         const step = (currentTime) => {
                             const elapsed = currentTime - startTime;
                             const progress = Math.min(elapsed / duration, 1);
                             const ease = easeOutCubic(progress);
                             
                             track.scrollLeft = start + (distance * ease);
                             
                             if (progress < 1) {
                                 this.scrollAnimId = requestAnimationFrame(step);
                             } else {
                                 track.scrollLeft = target;
                                 track.style.scrollSnapType = 'x proximity';
                                 this.scrollAnimId = null;
                             }
                         };
                         
                         this.scrollAnimId = requestAnimationFrame(step);
                     },
                     scrollPrev() {
                         this.smoothScroll(-this.getStep());
                     },
                     scrollNext() {
                         this.smoothScroll(this.getStep());
                     }
                 }">
            <!-- Subtle Atmospheric Background Accents (Peach & Sky Glow) -->
            <div class="absolute inset-0 z-0 pointer-events-none">
                <div class="absolute top-10 left-10 w-96 h-96 bg-orange-200/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-10 w-96 h-96 bg-blue-200/20 rounded-full blur-3xl"></div>
                <!-- Subtle dotted pattern -->
                <div class="absolute inset-0 bg-[radial-gradient(#CBD5E1_1px,transparent_1px)] [background-size:28px_28px] opacity-35"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Section Header -->
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14" data-animate="fade-up">
                    <span class="google-tag mb-3 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-[#FF6B4A] animate-pulse"></span>
                        <span>{{ $studentMomentsData['tag'] ?? 'Dinamika Kehidupan Siswa' }}</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mt-2">
                        {{ $studentMomentsData['title'] ?? 'Tumbuh, Berkarya, & Berprestasi Bersama Wikrama' }}
                    </h2>
                    <p class="mt-3 sm:mt-4 text-sm sm:text-lg text-slate-600 leading-relaxed font-normal">
                        {{ $studentMomentsData['subtitle'] ?? 'Setiap hari di SMK Wikrama 1 Garut diisi dengan antusiasme belajar nyata, pembiasaan karakter luhur, dan torehan prestasi nasional.' }}
                    </p>
                </div>
            </div>

            <!-- Full-Width Carousel Area (Edge-to-Edge Single-Row Panorama Track) -->
            <div class="dinamika-fullwidth-wrapper">
                <div class="flex flex-col lg:flex-row items-stretch gap-6 sm:gap-8">
                    <!-- Left Navigation: JIS Diamond Action Buttons (Rotated 45-degree Squares) -->
                    <div class="flex lg:flex-col items-center justify-center gap-3.5 sm:gap-4 shrink-0 py-2 lg:py-0 self-center z-20">
                        <!-- Prev Diamond Button -->
                        <button type="button" 
                                @click="scrollPrev()" 
                                class="w-10 h-10 rotate-45 border-2 border-[#00305F] bg-white text-[#00305F] hover:bg-[#00305F] hover:text-white flex items-center justify-center transition-all duration-300 shadow-sm cursor-pointer active:scale-95 group"
                                title="Sebelumnya">
                            <svg class="-rotate-45 w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <!-- Next Diamond Button -->
                        <button type="button" 
                                @click="scrollNext()" 
                                class="w-10 h-10 rotate-45 border-2 border-[#00305F] bg-white text-[#00305F] hover:bg-[#00305F] hover:text-white flex items-center justify-center transition-all duration-300 shadow-sm cursor-pointer active:scale-95 group"
                                title="Berikutnya">
                            <svg class="-rotate-45 w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Single-Row Cards Track (Horizontal Smooth Scroll - Gap 0) -->
                    <div class="min-w-0 flex-1 relative">
                        <div x-ref="cardTrack" 
                             class="dinamika-cards-track flex items-stretch gap-0 overflow-x-auto no-scrollbar pb-6 pt-2 snap-x snap-proximity focus:outline-hidden"
                             style="scrollbar-width: none; -ms-overflow-style: none; -webkit-overflow-scrolling: touch;">
                            @forelse($activeCards as $cIdx => $card)
                            @php
                                $accent = $softColors[$cIdx % count($softColors)];
                            @endphp
                            <div class="student-moment-card group relative shrink-0 overflow-hidden bg-slate-900 snap-start cursor-pointer border-r border-white/15 select-none transition-all duration-300"
                                 style="width: 290px; min-width: 290px; max-width: 290px; height: 480px; min-height: 480px; max-height: 480px;"
                                 data-animate="fade-up" data-animate-delay="{{ ($cIdx % 4) + 1 }}">
                                
                                <!-- Background Full-Bleed Image -->
                                <img src="{{ \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($card['image'] ?? null, 'images/web/campus-environment.jpg') }}" 
                                     alt="{{ $card['title'] ?? 'Dinamika Siswa' }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" 
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     loading="lazy">

                                <!-- Card Ornaments: Top Accent Line & Corner Ambient Glow -->
                                <div class="absolute top-0 left-0 right-0 h-1.5 z-20 pointer-events-none" style="background-color: {{ $accent }};"></div>
                                <div class="absolute -top-10 -right-10 w-28 h-28 rounded-full blur-2xl pointer-events-none opacity-30 group-hover:opacity-60 transition-opacity" style="background-color: {{ $accent }};"></div>

                                <!-- Bottom Diamond Ornament (percis seperti di screenshot) -->
                                <div class="absolute bottom-4 right-4 w-3.5 h-3.5 rotate-45 z-10 pointer-events-none shadow-xs transition-transform duration-300 group-hover:scale-125" style="background-color: {{ $accent }};"></div>

                                <!-- Normal (Idle) State: Bottom Dark Gradient + Title + Accent Line -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent pointer-events-none transition-opacity duration-300 group-hover:opacity-0"></div>
                                <div class="absolute bottom-6 left-6 right-8 z-10 transition-all duration-300 group-hover:opacity-0 group-hover:translate-y-2 pointer-events-none">
                                    <h3 class="text-lg sm:text-xl font-extrabold text-white leading-tight drop-shadow-xs" style="color: #FFFFFF !important;">
                                        {{ $card['title'] ?? '' }}
                                    </h3>
                                    <!-- Horizontal Accent Bar (seperti di kartu Apply Today) -->
                                    <div class="w-9 h-1 rounded-full mt-2.5" style="background-color: {{ $accent }};"></div>
                                    @if(!empty($card['cat_label']))
                                    <p class="text-[11px] text-white font-medium mt-2 tracking-wide" style="color: #FFFFFF !important;">
                                        {{ $card['cat_label'] }}
                                    </p>
                                    @endif
                                </div>

                                <!-- Hover State: Darker & Blurred Overlay, Title, Divider Line, Description & CTA (percis seperti Visit JIS) -->
                                <div class="absolute inset-0 z-20 p-6 flex flex-col justify-end bg-slate-950/70 backdrop-blur-md opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-1 group-hover:translate-y-0 text-white" style="color: #FFFFFF !important;">
                                    <h3 class="text-lg sm:text-xl font-extrabold text-white leading-tight" style="color: #FFFFFF !important;">
                                        {{ $card['title'] ?? '' }}
                                    </h3>
                                    <!-- Clean Horizontal Divider Line -->
                                    <div class="w-full h-px my-3.5" style="background-color: {{ $accent }}; opacity: 0.7;"></div>
                                    <p class="text-xs sm:text-sm text-white leading-relaxed font-normal mb-5 line-clamp-5" style="color: #FFFFFF !important;">
                                        {{ $card['desc'] ?? '' }}
                                    </p>
                                    <div>
                                        <a href="{{ $card['url'] ?? '#' }}" 
                                           target="{{ $card['target'] ?? '_self' }}"
                                           class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-white hover:underline transition-all group/btn" style="color: #FFFFFF !important;">
                                            <span style="color: #FFFFFF !important;">{{ $card['cta_text'] ?? 'Pelajari Selengkapnya' }}</span>
                                            <span style="color: {{ $accent }};" class="text-base transition-transform group-hover/btn:translate-x-1.5">&rarr;</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="py-12 text-center text-slate-400 w-full">
                                <p>Belum ada card momen siswa yang aktif.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>
