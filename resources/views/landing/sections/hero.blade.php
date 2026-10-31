        @php
            $rawHeroSlides = $cfg['hero']['slides'] ?? [];
            $activeSlides = array_values(array_filter($rawHeroSlides, function($s) {
                return !isset($s['is_active']) || (bool)$s['is_active'] === true;
            }));
            if (empty($activeSlides)) {
                $activeSlides = $rawHeroSlides;
            }
            $sliderDelay = isset($cfg['hero']['slider_delay']) ? max(3, min(30, (int)$cfg['hero']['slider_delay'])) : 6;
            $sliderAutoplay = isset($cfg['hero']['slider_autoplay']) ? (bool)$cfg['hero']['slider_autoplay'] : true;
        @endphp

        <!-- ── 1. Hero Section: Cinematic JIS-Style Full-Bleed Slider ── -->
        <section class="relative flex flex-col justify-end overflow-hidden bg-slate-950 text-white w-full select-none min-h-screen lg:h-screen lg:max-h-[960px]" 
                 id="beranda" 
                 style="background-color: #020617;"
                 x-data="{
                     slidesCount: {{ count($activeSlides) }},
                     activeIdx: 0,
                     sliderDelay: {{ $sliderDelay * 1000 }},
                     autoplayEnabled: {{ $sliderAutoplay ? 'true' : 'false' }},
                     isHovered: false,
                     timer: null,

                     touchStartX: 0,
                     handleTouchStart(e) {
                         if (e.changedTouches && e.changedTouches.length > 0) {
                             this.touchStartX = e.changedTouches[0].screenX;
                         }
                     },
                     handleTouchEnd(e) {
                         if (!e.changedTouches || e.changedTouches.length === 0) return;
                         const diff = this.touchStartX - e.changedTouches[0].screenX;
                         if (Math.abs(diff) > 40) {
                             if (diff > 0) this.nextSlide();
                             else this.prevSlide();
                         }
                     },

                     init() {
                         this.handleSlideChange();
                     },

                     goToSlide(idx) {
                         this.activeIdx = (idx + this.slidesCount) % this.slidesCount;
                         this.handleSlideChange();
                     },

                     nextSlide() {
                         this.goToSlide(this.activeIdx + 1);
                     },

                     prevSlide() {
                         this.goToSlide(this.activeIdx - 1);
                     },

                     startTimer() {
                         if (this.timer) clearInterval(this.timer);
                         this.timer = setInterval(() => {
                             if (!this.isHovered && this.autoplayEnabled) {
                                 this.nextSlide();
                             }
                         }, this.sliderDelay);
                     },

                     stopTimer() {
                         if (this.timer) {
                             clearInterval(this.timer);
                             this.timer = null;
                         }
                     },

                     handleSlideChange() {
                         if (!this.autoplayEnabled) return;
                         
                         this.$nextTick(() => {
                             // Cek apakah slide aktif memiliki elemen video
                             const activeVideo = this.$el.querySelector(`video[data-slide='${this.activeIdx}']`);
                             if (activeVideo) {
                                 // Pause timer statis karena durasi slide dikendalikan oleh durasi video
                                 this.stopTimer();
                                 activeVideo.currentTime = 0;
                                 activeVideo.play().catch(() => {});
                             } else {
                                 // Jika slide berupa gambar, gunakan timer interval normal
                                 this.startTimer();
                             }
                         });
                     }
                 }"
                 x-init="init()"
                 @mouseenter="isHovered = true"
                 @mouseleave="isHovered = false"
                 @touchstart.passive="handleTouchStart($event)"
                 @touchend.passive="handleTouchEnd($event)">

            <!-- ── Background Slider Canvas: Cinematic Zoom & Grow Pan & Fade ── -->
            <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden" style="background-color: #0F172A;">
                @foreach($activeSlides as $idx => $slide)
                <div class="absolute inset-0 transition-opacity duration-1000 ease-in-out {{ $idx === 0 ? 'opacity-100 z-10 pointer-events-auto' : 'opacity-0 z-0 pointer-events-none' }}"
                     :class="activeIdx === {{ $idx }} 
                         ? 'opacity-100 z-10 pointer-events-auto' 
                         : 'opacity-0 z-0 pointer-events-none'">
                    
                    @php
                        $isVid = in_array($slide['media_type'] ?? 'image', ['video_upload', 'video_url']);
                        $videoSrc = !empty($slide['video_url']) ? (str_starts_with($slide['video_url'], 'http') || str_starts_with($slide['video_url'], '/') ? $slide['video_url'] : asset('storage/' . $slide['video_url'])) : '';
                        $posterPath = $slide['poster'] ?? ($slide['image'] ?? 'images/web/campus-environment.jpg');
                        $posterUrl = class_exists(\App\Modules\AdministrasiWeb\Models\WebSetting::class)
                            ? \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($posterPath, 'images/web/campus-environment.jpg')
                            : asset('images/web/campus-environment.jpg');
                        $imgPath = $slide['image'] ?? 'images/web/campus-environment.jpg';
                        $imgUrl = class_exists(\App\Modules\AdministrasiWeb\Models\WebSetting::class)
                            ? \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($imgPath, 'images/web/campus-environment.jpg')
                            : asset('images/web/campus-environment.jpg');
                        $globalDim = isset($cfg['hero']['image_brightness']) ? (int)$cfg['hero']['image_brightness'] : (isset($slide['dimming']) ? (int)$slide['dimming'] : 40);
                        $globalOver = isset($cfg['hero']['overlay_opacity']) ? (int)$cfg['hero']['overlay_opacity'] : (isset($slide['overlay_opacity']) ? (int)$slide['overlay_opacity'] : 70);
                        $sDim = number_format($globalDim / 100, 2, '.', '');
                        $sOver = number_format($globalOver / 100, 2, '.', '');
                    @endphp

                    @if($isVid && !empty($videoSrc))
                        <video class="w-full h-full object-cover object-center hero-media-scale"
                               :class="activeIdx === {{ $idx }} ? 'scale-110' : 'scale-100'"
                               autoplay 
                               muted 
                               playsinline 
                               preload="metadata"
                               poster="{{ $posterUrl }}"
                               data-slide="{{ $idx }}"
                               x-ref="slideVideo{{ $idx }}"
                               @ended="if (autoplayEnabled && !isHovered) { nextSlide(); }"
                               x-init="$watch('activeIdx', val => { 
                                   if (val === {{ $idx }}) { 
                                       $el.currentTime = 0; 
                                       $el.play().catch(() => {}); 
                                   } else { 
                                       $el.pause(); 
                                   } 
                               })"
                               style="filter: brightness({{ $sDim }}) contrast(1.12);">
                            @if(str_ends_with(strtolower($videoSrc), '.webm'))
                                <source src="{{ $videoSrc }}" type="video/webm">
                            @endif
                            <source src="{{ $videoSrc }}" type="video/mp4">
                        </video>
                    @else
                        <img src="{{ $imgUrl }}" 
                             alt="{{ $slide['title'] ?? 'SMK Wikrama 1 Garut' }}" 
                             class="w-full h-full object-cover object-center hero-media-scale"
                             :class="activeIdx === {{ $idx }} ? 'scale-110' : 'scale-100'"
                             style="filter: brightness({{ $sDim }}) contrast(1.12);">
                    @endif

                    <!-- Dynamic Dark Overlay with Directional Gradient (Modern Navy Slate Vignette) -->
                    <div class="absolute inset-0"
                         style="background: linear-gradient(to top, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, {{ $sOver }}) 40%, rgba(15, 23, 42, {{ max(0.15, (float)$sOver - 0.35) }}) 100%), linear-gradient(to right, rgba(15, 23, 42, 0.80) 0%, transparent 60%);"></div>

                    <!-- Ambient Glow Effect per slide (Warm Coral & Royal Blue) -->
                    <div class="absolute -top-32 -right-20 w-[600px] h-[600px] bg-gradient-to-br from-orange-500/15 via-rose-500/10 to-transparent rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute top-1/2 -left-28 w-[500px] h-[500px] bg-gradient-to-tr from-blue-500/15 via-amber-500/10 to-transparent rounded-full blur-3xl pointer-events-none"></div>
                </div>
                @endforeach
            </div>

            <!-- ── Left & Right Side Navigation Buttons (JIS Navy Hover Glass) ── -->
            <button type="button" 
                    @click="prevSlide()" 
                    class="absolute left-3 sm:left-6 lg:left-8 top-1/2 -translate-y-1/2 z-30 w-11 h-11 sm:w-14 sm:h-14 rounded-full border border-white/25 bg-slate-950/40 hover:bg-[#00519E] hover:text-white hover:border-[#00519E] hover:scale-105 active:scale-95 text-white backdrop-blur-md flex items-center justify-center transition-all duration-300 shadow-2xl group cursor-pointer"
                    aria-label="Slide Sebelumnya"
                    title="Slide Sebelumnya"
                    data-cursor="PREV">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 transition-transform duration-300 group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <button type="button" 
                    @click="nextSlide()" 
                    class="absolute right-3 sm:right-6 lg:right-8 top-1/2 -translate-y-1/2 z-30 w-11 h-11 sm:w-14 sm:h-14 rounded-full border border-white/25 bg-slate-950/40 hover:bg-[#00519E] hover:text-white hover:border-[#00519E] hover:scale-105 active:scale-95 text-white backdrop-blur-md flex items-center justify-center transition-all duration-300 shadow-2xl group cursor-pointer"
                    aria-label="Slide Berikutnya"
                    title="Slide Berikutnya"
                    data-cursor="NEXT">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 transition-transform duration-300 group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- ── Bottom Content Area: JIS Reference Layout (Floating Position & Silky Smooth Slide) ── -->
            <div class="relative z-20 w-full pb-16 sm:pb-24 lg:pb-28 mb-4 sm:mb-8 lg:mb-12 pt-4 sm:pt-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    
                    <!-- Hairline Divider -->
                    <div class="border-t border-white/20 w-full pt-6 sm:pt-8">
                        <div class="grid grid-cols-1 items-center [&>*]:col-start-1 [&>*]:row-start-1 relative w-full">
                            @foreach($activeSlides as $idx => $slide)
                            <div class="w-full transition-all duration-500"
                                 style="grid-area: 1 / 1 / 2 / 2;"
                                 x-show="activeIdx === {{ $idx }}"
                                 x-transition:enter="transition ease-out duration-500"
                                 x-transition:enter-start="opacity-0 translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 {{ $idx !== 0 ? 'x-cloak' : '' }}>
                                 
                                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-center">
                                    <!-- Col 1: Main Title (Left) - Silky Smooth Rise -->
                                    <div class="lg:col-span-5 transition-all duration-600 ease-out"
                                         :class="activeIdx === {{ $idx }} ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
                                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 border border-white/25 text-[#F5D84A] text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-2.5 backdrop-blur-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#F5D84A] animate-pulse"></span>
                                            <span>{{ !empty($slide['badge']) ? $slide['badge'] : 'Learning in Indonesia to be Best for the World' }}</span>
                                        </div>
                                        <h1 class="text-2xl sm:text-3xl lg:text-5xl font-extrabold text-white tracking-tight leading-[1.14] break-words drop-shadow-[0_2px_8px_rgba(0,0,0,0.6)]">
                                            {{ $slide['title'] }}
                                        </h1>
                                    </div>

                                    <!-- Col 2: Description (Middle) - Staggered Silky Smooth Rise -->
                                    <div class="lg:col-span-4 transition-all duration-600 ease-out delay-75"
                                         :class="activeIdx === {{ $idx }} ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
                                        <p class="text-slate-200 text-xs sm:text-sm lg:text-base leading-relaxed font-normal drop-shadow-[0_1px_4px_rgba(0,0,0,0.6)]">
                                            {{ $slide['desc'] }}
                                        </p>
                                    </div>

                                    <!-- Col 3: CTA Button (Right) - JIS Sun Gold Action -->
                                    <div class="lg:col-span-3 flex lg:justify-end items-center transition-all duration-500 ease-out delay-150"
                                         :class="activeIdx === {{ $idx }} ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 translate-y-3 scale-95'">
                                        <a href="{{ $slide['cta_url'] ?? '#' }}" 
                                           target="{{ $slide['cta_target'] ?? '_self' }}" 
                                           rel="{{ ($slide['cta_target'] ?? '_self') === '_blank' ? 'noopener noreferrer' : '' }}"
                                           class="btn-jis-pill-gold !py-3.5 !px-8 text-xs sm:text-sm font-bold shadow-2xl hover:scale-105 active:scale-95 transition-all duration-300 inline-flex items-center gap-2 cursor-pointer group/btn"
                                           data-cursor="EXPLORE">
                                            <span>{{ $slide['cta_text'] ?? 'Pelajari Selengkapnya' }}</span>
                                            <span class="text-base font-bold group-hover/btn:translate-x-1.5 transition-transform">&rarr;</span>
                                        </a>
                                    </div>
                                </div>

                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>

            <!-- ── Organic Wave SVG Divider into #filosofi (#FFFFFF) ── -->
            <div class="absolute bottom-0 inset-x-0 w-full overflow-hidden leading-none z-10 pointer-events-none">
                <svg class="relative block w-full h-8 sm:h-14 lg:h-16 text-white" viewBox="0 0 1200 120" preserveAspectRatio="none" fill="currentColor">
                    <path d="M0,0 C150,90 350,-40 500,45 C650,130 900,10 1200,50 L1200,120 L0,120 Z"></path>
                </svg>
            </div>
        </section>

