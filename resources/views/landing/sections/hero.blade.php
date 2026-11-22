@php
    $heroCfg = $cfg['hero'] ?? [];
    $rawSlides = $heroCfg['slides'] ?? [];
    $slides = array_values(array_filter($rawSlides, fn($s) => !isset($s['is_active']) || (bool)$s['is_active'] === true));
    
    // Fallback jika belum ada slide terkonfigurasi di database WebSetting
    if (empty($slides)) {
        $slides = [
            [
                'id' => 'spmb',
                'tab_name' => 'Brosur & SPMB',
                'badge' => 'SPMB 2026/2027 Gelombang 1',
                'title' => 'Penerimaan Peserta Didik Baru Resmi Dibuka',
                'desc' => 'Tersedia Beasiswa Orang Tua Asuh, Beasiswa Ikatan Dinas Tahfidz, dan Beasiswa Prestasi Akademik/Non-Akademik.',
                'media_type' => 'video_upload',
                'image' => 'images/web/campus-environment.jpg',
                'video_url' => 'storage/web/hero/RUE3ztebwBxCLNLCxYBSvNEffodlND19aFsFF1NI.mp4',
                'poster' => 'images/web/campus-environment.jpg',
                'cta_text' => 'Unduh Brosur Lengkap',
                'cta_url' => 'http://brosur.smkwikrama1garut.sch.id/',
                'cta_target' => '_blank',
            ],
            [
                'id' => 'tech',
                'tab_name' => 'PPLG & AI',
                'badge' => 'Software & AI Studio',
                'title' => 'Pengembangan Perangkat Lunak & Gim',
                'desc' => 'Praktik langsung Machine Learning, Web Fullstack, Flutter Mobile, Database Architecture, dan UI/UX modern di lab canggih.',
                'media_type' => 'image',
                'image' => 'images/web/hero-pplg.jpg',
                'video_url' => '',
                'poster' => 'images/web/hero-pplg.jpg',
                'cta_text' => 'Pelajari Kurikulum PPLG',
                'cta_url' => '#jurusan',
                'cta_target' => '_self',
            ],
            [
                'id' => 'tjkt',
                'tab_name' => 'TJKT & Siber',
                'badge' => 'MikroTik & Huawei Academy',
                'title' => 'Teknik Jaringan Komputer & Telekomunikasi',
                'desc' => 'Penguasaan infrastruktur fiber optik, server Linux cloud, keamanan siber (Cybersecurity), dan sertifikasi MTCNA.',
                'media_type' => 'image',
                'image' => 'images/web/hero-tjkt.jpg',
                'video_url' => '',
                'poster' => 'images/web/hero-tjkt.jpg',
                'cta_text' => 'Pelajari Kurikulum TJKT',
                'cta_url' => '#jurusan',
                'cta_target' => '_self',
            ],
            [
                'id' => 'bisnis',
                'tab_name' => 'Bisnis Digital',
                'badge' => 'Studio Live Commerce',
                'title' => 'Pemasaran & Bisnis Digital (PMN)',
                'desc' => 'Strategi penjualan e-commerce, live commerce streamer, Google/Meta Ads, content creator, dan unit usaha WikraMart.',
                'media_type' => 'image',
                'image' => 'images/web/hero-pmn.jpg',
                'video_url' => '',
                'poster' => 'images/web/hero-pmn.jpg',
                'cta_text' => 'Lihat Studio Live Commerce',
                'cta_url' => '#jurusan',
                'cta_target' => '_self',
            ],
            [
                'id' => 'hotel',
                'tab_name' => 'Perhotelan',
                'badge' => 'Hospitality Bintang 5',
                'title' => 'Perhotelan & Barista Internasional (HTL)',
                'desc' => 'Standar pelayanan hotel bintang 5, operasional front office, mock-up suite room, banquet, barista profesional, dan table manner.',
                'media_type' => 'image',
                'image' => 'images/web/hero-htl.png',
                'video_url' => '',
                'poster' => 'images/web/hero-htl.png',
                'cta_text' => 'Lihat Fasilitas Hotel',
                'cta_url' => '#jurusan',
                'cta_target' => '_self',
            ],
        ];
    }

    $sliderDelay = max(3, min(30, (int)($heroCfg['slider_delay'] ?? 6)));
    $sliderAutoplay = !empty($heroCfg['slider_autoplay']);
    $overlayOpacity = isset($heroCfg['overlay_opacity']) ? max(0, min(100, (int)$heroCfg['overlay_opacity'])) / 100 : 0.65;
    $imageBrightness = isset($heroCfg['image_brightness']) ? max(10, min(100, (int)$heroCfg['image_brightness'])) / 100 : 0.55;
@endphp

        <!-- ── Self-Contained Hero CSS Styles ── -->
        <style>
            @keyframes kenBurnsSlow {
                0% { transform: scale(1) translate(0, 0); }
                50% { transform: scale(1.08) translate(-1%, -1%); }
                100% { transform: scale(1) translate(0, 0); }
            }
            .animate-ken-burns {
                animation: kenBurnsSlow 20s ease-in-out infinite alternate;
            }
            .btn-jis-gold {
                background-color: #F9D900;
                color: #002752 !important;
                font-weight: 700;
                border-radius: 9999px;
                padding: 0.75rem 1.75rem;
                transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s, box-shadow 0.2s;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                box-shadow: 0 4px 14px rgba(249, 217, 0, 0.35);
                text-decoration: none;
                cursor: pointer;
                border: 1px solid #E2B714;
            }
            .btn-jis-gold:hover {
                background-color: #E2B714;
                transform: translateY(-2px);
                box-shadow: 0 6px 18px rgba(249, 217, 0, 0.45);
            }
            .btn-jis-ghost {
                background: rgba(255, 255, 255, 0.12);
                color: #FFFFFF !important;
                border: 1.5px solid rgba(255, 255, 255, 0.75);
                font-weight: 600;
                border-radius: 9999px;
                padding: 0.75rem 1.75rem;
                transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s, border-color 0.2s;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
                text-decoration: none;
                cursor: pointer;
            }
            .btn-jis-ghost:hover {
                background: rgba(255, 255, 255, 0.25);
                border-color: #FFFFFF;
                transform: translateY(-2px);
            }
            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }
            .no-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        </style>

        <!-- ── Babak 1: Cinematic Full-Bleed Video & Media Slider (Tata Letak Sesuai Gambar Referensi) ── -->
        <section class="relative min-h-screen flex items-end justify-start overflow-hidden bg-slate-950 text-white w-full select-none" 
                 id="beranda"
                 style="min-height: 100vh; min-height: 100dvh;"
                 x-data="{
                     currentSlide: 0,
                     totalSlides: {{ count($slides) }},
                     autoplay: {{ $sliderAutoplay ? 'true' : 'false' }},
                     defaultDelay: {{ $sliderDelay * 1000 }},
                     timer: null,
                     progressTimer: null,
                     progressWidth: 0,
                     isHovered: false,
                     init() {
                         this.setupSlide();
                     },
                     setupSlide() {
                         this.stopTimers();
                         this.progressWidth = 0;
                         this.$nextTick(() => {
                             const vids = this.$el.querySelectorAll('video');
                             let isCurrentVideo = false;

                             vids.forEach((v) => {
                                 const sIdx = parseInt(v.dataset.slideIndex ?? -1);
                                 v.muted = true;
                                 if (sIdx === this.currentSlide) {
                                     isCurrentVideo = true;
                                     v.currentTime = 0;

                                     if (!v.dataset.hasListeners) {
                                         v.dataset.hasListeners = 'true';
                                         v.addEventListener('ended', () => {
                                             if (this.currentSlide === sIdx && this.autoplay && !this.isHovered) {
                                                 this.next();
                                             }
                                         });
                                         v.addEventListener('timeupdate', () => {
                                             if (this.currentSlide === sIdx && v.duration) {
                                                 this.progressWidth = Math.min(100, (v.currentTime / v.duration) * 100);
                                                 // Deteksi bila mendekati akhir durasi
                                                 if (this.autoplay && !this.isHovered && v.currentTime >= (v.duration - 0.25)) {
                                                     this.next();
                                                 }
                                             }
                                         });
                                         v.addEventListener('error', () => {
                                             if (this.currentSlide === sIdx) {
                                                 this.startImageAutoplay();
                                             }
                                         });
                                     }

                                     const playPromise = v.play();
                                     if (playPromise !== undefined) {
                                         playPromise.catch(() => {
                                             this.startImageAutoplay();
                                         });
                                     }
                                 } else {
                                     v.pause();
                                 }
                             });

                             // Jika slide aktif berupa gambar, jalankan timer default
                             if (!isCurrentVideo && this.autoplay && this.totalSlides > 1) {
                                 this.startImageAutoplay();
                             }
                         });
                     },
                     startImageAutoplay() {
                         this.stopTimers();
                         if (!this.autoplay || this.isHovered) return;
                         this.progressWidth = 0;
                         const step = 50;
                         const inc = (step / this.defaultDelay) * 100;
                         this.progressTimer = setInterval(() => {
                             if (!this.isHovered) {
                                 this.progressWidth = Math.min(100, this.progressWidth + inc);
                             }
                         }, step);
                         this.timer = setInterval(() => {
                             if (!this.isHovered) {
                                 this.next();
                             }
                         }, this.defaultDelay);
                     },
                     stopTimers() {
                         if (this.timer) { clearInterval(this.timer); this.timer = null; }
                         if (this.progressTimer) { clearInterval(this.progressTimer); this.progressTimer = null; }
                     },
                     next() {
                         this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                         this.setupSlide();
                     },
                     prev() {
                         this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
                         this.setupSlide();
                     },
                     goTo(idx) {
                         this.currentSlide = idx;
                         this.setupSlide();
                     },
                     onMouseEnter() {
                         this.isHovered = true;
                     },
                     onMouseLeave() {
                         this.isHovered = false;
                         const curVid = this.$el.querySelector(`video[data-slide-index='${this.currentSlide}']`);
                         if (!curVid) {
                             this.startImageAutoplay();
                         } else {
                             if (curVid.ended) {
                                 this.next();
                             }
                         }
                     }
                 }"
                 @mouseenter="onMouseEnter()"
                 @mouseleave="onMouseLeave()"
                 aria-label="Hero Slider Media Kampus">
            
            <!-- ── Layer 1: Background Media Stack (Image & Muted Video) ── -->
            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                @foreach($slides as $index => $slide)
                @php
                    $isSlideVideo = in_array($slide['media_type'] ?? 'image', ['video_upload', 'video_url']) && !empty($slide['video_url']);
                    $videoSrc = $isSlideVideo 
                        ? (class_exists(\App\Modules\AdministrasiWeb\Models\WebSetting::class)
                            ? \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($slide['video_url'])
                            : (str_starts_with($slide['video_url'], 'http') || str_starts_with($slide['video_url'], '/') ? $slide['video_url'] : asset('storage/' . $slide['video_url'])))
                        : '';
                    $posterSrc = class_exists(\App\Modules\AdministrasiWeb\Models\WebSetting::class)
                        ? \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($slide['poster'] ?? ($slide['image'] ?? 'images/web/campus-environment.jpg'))
                        : asset('images/web/campus-environment.jpg');
                    $imageSrc = class_exists(\App\Modules\AdministrasiWeb\Models\WebSetting::class)
                        ? \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($slide['image'] ?? 'images/web/campus-environment.jpg')
                        : asset('images/web/campus-environment.jpg');
                @endphp
                <div x-show="currentSlide === {{ $index }}" 
                     x-cloak
                     x-transition:enter="transition-opacity duration-1000 ease-in-out"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity duration-1000 ease-in-out absolute inset-0"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0 w-full h-full">
                    
                    @if($isSlideVideo)
                        <!-- Video Latar Otomatis Berputar Tanpa Suara (Muted) Mengikuti Durasi Alami -->
                        <video id="hero-slide-vid-{{ $index }}"
                               data-slide-index="{{ $index }}"
                               class="w-full h-full object-cover" 
                               autoplay 
                               muted 
                               playsinline 
                               webkit-playsinline 
                               preload="auto" 
                               poster="{{ $posterSrc }}"
                               style="filter: brightness({{ $imageBrightness }}) contrast(1.1);">
                            <source src="{{ $videoSrc }}" type="video/mp4">
                            <!-- Fallback Image jika browser memblokir video -->
                            <img src="{{ $posterSrc }}" alt="{{ $slide['title'] ?? 'SMK Wikrama 1 Garut' }}" class="w-full h-full object-cover">
                        </video>
                    @else
                        <!-- Foto Resolusi Tinggi dengan Efek Ken Burns Slow Zoom -->
                        <img src="{{ $imageSrc }}" 
                             alt="{{ $slide['title'] ?? 'Kampus SMK Wikrama 1 Garut' }}" 
                             class="w-full h-full object-cover animate-ken-burns origin-center"
                             loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                             style="filter: brightness({{ $imageBrightness }}) contrast(1.15);">
                    @endif

                </div>
                @endforeach

                <!-- ── Dynamic Asymmetric Cinematic Overlay: Bebaskan Area Tengah & Kanan Video (70% Terbuka) ── -->
                <!-- Gradasi Gelap Khusus di Sisi Kiri (Memastikan Keterbacaan Teks Tanpa Menghalangi Video Tengah/Kanan) -->
                <div class="absolute inset-y-0 left-0 w-full sm:w-4/5 lg:w-3/5 bg-gradient-to-r from-slate-950/95 via-slate-950/65 to-transparent pointer-events-none z-1"></div>
                
                <!-- Lapisan Kontras Global Halus (Terkoneksi ke Web-Admin) -->
                <div class="absolute inset-0 pointer-events-none transition-colors duration-500 z-1" 
                     style="background-color: rgba(2, 6, 23, {{ $overlayOpacity * 0.4 }});"></div>

                <!-- Gradasi Puncak (Untuk Kontras Header Transparan) -->
                <div class="absolute top-0 inset-x-0 h-36 bg-gradient-to-b from-black/85 via-black/35 to-transparent pointer-events-none z-1"></div>

                <!-- Gradasi Dasar Sinematik (Mulus ke Konten Selanjutnya Tanpa Gelombang Putih) -->
                <div class="absolute bottom-0 inset-x-0 h-32 bg-gradient-to-t from-slate-950/95 via-slate-950/60 to-transparent pointer-events-none z-1"></div>
            </div>

            <!-- ── Layer 2: Main Slider Typography & Call-To-Actions (Bottom-Left Composition) ── -->
            <div class="relative z-10 w-full max-w-4xl px-6 sm:px-12 lg:px-16 pt-36 pb-20 sm:pb-24 text-left">
                
                <!-- Slide Content Carousel -->
                @foreach($slides as $index => $slide)
                <div x-show="currentSlide === {{ $index }}" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-700 delay-100"
                     x-transition:enter-start="opacity-0 translate-y-3"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-300 absolute inset-x-0"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-3"
                     class="w-full space-y-4">
                    
                    <!-- Badge Tag Legalitas/Slide (Dark Frosted Glass Sesuai Gambar) -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white/10 backdrop-blur-md border border-white/20 text-white shadow-md">
                        <span class="w-2 h-2 rounded-full bg-[#F9D900] shadow-[0_0_8px_#F9D900]"></span>
                        <span class="tracking-wide">{{ $slide['badge'] ?? 'SPMB 2026/2027 Gelombang 1' }}</span>
                    </div>

                    <!-- Grand Headline Dinamis dari Pengaturan Web-Admin -->
                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-[1.12] drop-shadow-xl max-w-2xl">
                        {{ $slide['title'] ?? 'Penerimaan Peserta Didik Baru Resmi Dibuka' }}
                    </h1>

                    <!-- Deskripsi Slide Dinamis -->
                    <p class="text-sm sm:text-base md:text-lg text-slate-200 leading-relaxed font-normal max-w-xl drop-shadow-md">
                        {{ $slide['desc'] ?? 'Tersedia Beasiswa Orang Tua Asuh, Beasiswa Ikatan Dinas Tahfidz, dan Beasiswa Prestasi Akademik/Non-Akademik.' }}
                    </p>

                    <!-- Tombol Aksi (Kuning Emas Solid + Kaca Ghost Melengkung Sesuai Gambar) -->
                    <div class="pt-2 flex flex-wrap items-center gap-3 sm:gap-4">
                        <a href="{{ $slide['cta_url'] ?? 'http://brosur.smkwikrama1garut.sch.id/' }}" 
                           target="{{ $slide['cta_target'] ?? '_blank' }}" 
                           rel="{{ ($slide['cta_target'] ?? '_blank') === '_blank' ? 'noopener noreferrer' : '' }}" 
                           class="btn-jis-gold text-sm sm:text-base font-bold shadow-xl group">
                            <span>{{ $slide['cta_text'] ?? 'Unduh Brosur Lengkap' }}</span>
                            <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                        </a>

                        <a href="#filosofi" 
                           class="btn-jis-ghost text-sm sm:text-base font-semibold group">
                            <span>Jelajahi Profil &amp; Nilai</span>
                            <svg class="w-4 h-4 group-hover:translate-y-0.5 transition-transform text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                    </div>

                </div>
                @endforeach

            </div>

            <!-- ── Navigation Arrow Controls (Left & Right Screen Edges) ── -->
            @if(count($slides) > 1)
            <div class="absolute inset-y-0 left-3 sm:left-6 flex items-center z-20 pointer-events-none">
                <button type="button" 
                        @click="prev()"
                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-black/30 hover:bg-black/60 text-white border border-white/20 backdrop-blur-md flex items-center justify-center transition-all hover:scale-110 active:scale-95 cursor-pointer pointer-events-auto shadow-lg"
                        aria-label="Slide Sebelumnya">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            </div>

            <div class="absolute inset-y-0 right-3 sm:right-6 flex items-center z-20 pointer-events-none">
                <button type="button" 
                        @click="next()"
                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-black/30 hover:bg-black/60 text-white border border-white/20 backdrop-blur-md flex items-center justify-center transition-all hover:scale-110 active:scale-95 cursor-pointer pointer-events-auto shadow-lg"
                        aria-label="Slide Berikutnya">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
            @endif

            <!-- ── Bottom Slide Progress Indicator & Scroll Down Chevron ── -->
            <div class="absolute bottom-4 sm:bottom-6 inset-x-0 px-6 sm:px-12 lg:px-16 pointer-events-none z-20">
                <div class="relative flex items-center justify-between">
                    @if(count($slides) > 1)
                    <!-- Slide Dots with Active Progress Timer (Sinkron dengan Waktu Riil Video / Gambar) -->
                    <div class="flex items-center gap-2 pointer-events-auto bg-black/40 px-3 py-1.5 rounded-full border border-white/15 backdrop-blur-md">
                        @foreach($slides as $idx => $s)
                        <button type="button" 
                                @click="goTo({{ $idx }})"
                                class="relative h-2 rounded-full transition-all duration-300 overflow-hidden cursor-pointer"
                                :class="currentSlide === {{ $idx }} ? 'w-8 bg-white/40' : 'w-2 bg-white/30 hover:bg-white/60'"
                                title="Buka Slide {{ $idx + 1 }}">
                            <div x-show="currentSlide === {{ $idx }}" 
                                 class="h-full bg-[#F9D900] transition-all duration-75"
                                 :style="`width: ${progressWidth}%`"></div>
                        </button>
                        @endforeach
                    </div>
                    @else
                    <div></div>
                    @endif

                    <!-- Animated Bouncing Down Chevron pointing to #filosofi (Posisi Tengah) -->
                    <div class="absolute left-1/2 -translate-x-1/2 flex items-center justify-center pointer-events-none">
                        <a href="#filosofi" class="pointer-events-auto p-2 text-white/70 hover:text-white transition-colors animate-bounce flex items-center gap-1.5 text-xs font-semibold" aria-label="Scroll ke konten berikutnya">
                            <span>Jelajahi</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </a>
                    </div>

                    <!-- Right placeholder -->
                    <div></div>
                </div>
            </div>

        </section>
