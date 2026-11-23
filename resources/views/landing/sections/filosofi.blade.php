        <!-- ── 2. "Welcome to Wikrama" Signature Manifesto & Link & Match BMW Section ── -->
        @php
            $sekolah = $sekolah ?? \App\Modules\Sistem\Models\School::current();
            $schoolName = $sekolah->nama ?? ($cfg['branding']['school_name'] ?? 'SMK Wikrama 1 Garut');
            $schoolLogo = file_exists(public_path('images/landing/wikrama-logo.png')) 
                ? asset('images/landing/wikrama-logo.png') 
                : ($sekolah->logoSekolahUrl() ?: asset('favicon.png'));
        @endphp

        <section class="py-16 sm:py-20 relative overflow-hidden bg-white text-slate-800 border-b border-slate-200/80 scroll-mt-24 sm:scroll-mt-28 w-full" id="filosofi">
            
            <style>
                @keyframes filosofiFloatLeft {
                    0%, 100% {
                        transform: translateY(-50%) translateY(-12px);
                    }
                    50% {
                        transform: translateY(-50%) translateY(12px);
                    }
                }
                @keyframes filosofiFloatRight {
                    0%, 100% {
                        transform: translateY(0px);
                    }
                    50% {
                        transform: translateY(-16px);
                    }
                }

                #filosofi .filosofi-arc-left {
                    position: absolute;
                    left: -130px;
                    top: 50%;
                    width: 320px;
                    height: 320px;
                    border-radius: 50%;
                    border: 56px solid #93C5FD;
                    opacity: 0.95;
                    pointer-events: none;
                    z-index: 1;
                    box-sizing: border-box;
                    animation: filosofiFloatLeft 5.5s ease-in-out infinite;
                }

                #filosofi .filosofi-dots-left {
                    position: absolute;
                    left: 45px;
                    top: calc(50% + 35px);
                    pointer-events: none;
                    z-index: 2;
                    animation: filosofiFloatLeft 5.5s ease-in-out infinite;
                }

                #filosofi .filosofi-arc-right {
                    position: absolute;
                    right: -130px;
                    top: 25px;
                    width: 320px;
                    height: 320px;
                    border-radius: 50%;
                    border: 56px solid #FF6B00;
                    opacity: 0.95;
                    pointer-events: none;
                    z-index: 1;
                    box-sizing: border-box;
                    animation: filosofiFloatRight 6s ease-in-out infinite;
                }

                #filosofi .filosofi-dots-right {
                    position: absolute;
                    right: 45px;
                    top: 25px;
                    pointer-events: none;
                    z-index: 2;
                    animation: filosofiFloatRight 6s ease-in-out infinite;
                }

                #filosofi .highlight-orange {
                    color: #FF6B00;
                    font-weight: 700;
                }

                @media (max-width: 1024px) {
                    #filosofi .filosofi-arc-left {
                        left: -110px;
                        width: 250px;
                        height: 250px;
                        border-width: 44px;
                    }
                    #filosofi .filosofi-dots-left {
                        left: 30px;
                        top: calc(50% + 25px);
                    }
                    #filosofi .filosofi-arc-right {
                        right: -110px;
                        top: 20px;
                        width: 250px;
                        height: 250px;
                        border-width: 44px;
                    }
                    #filosofi .filosofi-dots-right {
                        right: 30px;
                        top: 20px;
                    }
                }

                @media (max-width: 640px) {
                    #filosofi .filosofi-arc-left {
                        left: -80px;
                        width: 170px;
                        height: 170px;
                        border-width: 30px;
                    }
                    #filosofi .filosofi-dots-left {
                        left: 15px;
                        top: calc(50% + 15px);
                    }
                    #filosofi .filosofi-arc-right {
                        right: -80px;
                        top: 10px;
                        width: 170px;
                        height: 170px;
                        border-width: 30px;
                    }
                    #filosofi .filosofi-dots-right {
                        right: 15px;
                        top: 10px;
                    }
                }
            </style>

            <!-- ── Aksen Potongan Lingkaran Biru & Dot Matrix Sejajar (Sisi Kiri Layar) ── -->
            <div class="filosofi-arc-left" aria-hidden="true"></div>
            <div class="filosofi-dots-left" aria-hidden="true">
                <svg class="w-20 h-16 sm:w-28 sm:h-22 opacity-85" viewBox="0 0 120 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="10" cy="10" r="3.5" fill="#94A3B8" />
                    <circle cx="30" cy="10" r="3.5" fill="#94A3B8" />
                    <circle cx="50" cy="10" r="3.5" fill="#94A3B8" />
                    <circle cx="70" cy="10" r="3.5" fill="#94A3B8" />
                    <circle cx="90" cy="10" r="3.5" fill="#94A3B8" />
                    <circle cx="110" cy="10" r="3.5" fill="#94A3B8" />

                    <circle cx="10" cy="30" r="3.5" fill="#94A3B8" />
                    <circle cx="30" cy="30" r="3.5" fill="#94A3B8" />
                    <circle cx="50" cy="30" r="3.5" fill="#94A3B8" />
                    <circle cx="70" cy="30" r="3.5" fill="#94A3B8" />
                    <circle cx="90" cy="30" r="3.5" fill="#94A3B8" />
                    <circle cx="110" cy="30" r="3.5" fill="#94A3B8" />

                    <circle cx="10" cy="50" r="3.5" fill="#94A3B8" />
                    <circle cx="30" cy="50" r="3.5" fill="#94A3B8" />
                    <circle cx="50" cy="50" r="3.5" fill="#94A3B8" />
                    <circle cx="70" cy="50" r="3.5" fill="#94A3B8" />
                    <circle cx="90" cy="50" r="3.5" fill="#94A3B8" />
                    <circle cx="110" cy="50" r="3.5" fill="#94A3B8" />

                    <circle cx="10" cy="70" r="3.5" fill="#94A3B8" />
                    <circle cx="30" cy="70" r="3.5" fill="#94A3B8" />
                    <circle cx="50" cy="70" r="3.5" fill="#94A3B8" />
                    <circle cx="70" cy="70" r="3.5" fill="#94A3B8" />
                    <circle cx="90" cy="70" r="3.5" fill="#94A3B8" />
                    <circle cx="110" cy="70" r="3.5" fill="#94A3B8" />

                    <circle cx="10" cy="90" r="3.5" fill="#94A3B8" />
                    <circle cx="30" cy="90" r="3.5" fill="#94A3B8" />
                    <circle cx="50" cy="90" r="3.5" fill="#94A3B8" />
                    <circle cx="70" cy="90" r="3.5" fill="#94A3B8" />
                    <circle cx="90" cy="90" r="3.5" fill="#94A3B8" />
                    <circle cx="110" cy="90" r="3.5" fill="#94A3B8" />
                </svg>
            </div>

            <!-- ── Aksen Potongan Lingkaran Oranye & Dot Matrix Sejajar (Sisi Kanan Layar) ── -->
            <div class="filosofi-arc-right" aria-hidden="true"></div>
            <div class="filosofi-dots-right" aria-hidden="true">
                <svg class="w-20 h-16 sm:w-28 sm:h-22 opacity-85" viewBox="0 0 120 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="10" cy="10" r="3.5" fill="#94A3B8" />
                    <circle cx="30" cy="10" r="3.5" fill="#94A3B8" />
                    <circle cx="50" cy="10" r="3.5" fill="#94A3B8" />
                    <circle cx="70" cy="10" r="3.5" fill="#94A3B8" />
                    <circle cx="90" cy="10" r="3.5" fill="#94A3B8" />
                    <circle cx="110" cy="10" r="3.5" fill="#94A3B8" />

                    <circle cx="10" cy="30" r="3.5" fill="#94A3B8" />
                    <circle cx="30" cy="30" r="3.5" fill="#94A3B8" />
                    <circle cx="50" cy="30" r="3.5" fill="#94A3B8" />
                    <circle cx="70" cy="30" r="3.5" fill="#94A3B8" />
                    <circle cx="90" cy="30" r="3.5" fill="#94A3B8" />
                    <circle cx="110" cy="30" r="3.5" fill="#94A3B8" />

                    <circle cx="10" cy="50" r="3.5" fill="#94A3B8" />
                    <circle cx="30" cy="50" r="3.5" fill="#94A3B8" />
                    <circle cx="50" cy="50" r="3.5" fill="#94A3B8" />
                    <circle cx="70" cy="50" r="3.5" fill="#94A3B8" />
                    <circle cx="90" cy="50" r="3.5" fill="#94A3B8" />
                    <circle cx="110" cy="50" r="3.5" fill="#94A3B8" />

                    <circle cx="10" cy="70" r="3.5" fill="#94A3B8" />
                    <circle cx="30" cy="70" r="3.5" fill="#94A3B8" />
                    <circle cx="50" cy="70" r="3.5" fill="#94A3B8" />
                    <circle cx="70" cy="70" r="3.5" fill="#94A3B8" />
                    <circle cx="90" cy="70" r="3.5" fill="#94A3B8" />
                    <circle cx="110" cy="70" r="3.5" fill="#94A3B8" />

                    <circle cx="10" cy="90" r="3.5" fill="#94A3B8" />
                    <circle cx="30" cy="90" r="3.5" fill="#94A3B8" />
                    <circle cx="50" cy="90" r="3.5" fill="#94A3B8" />
                    <circle cx="70" cy="90" r="3.5" fill="#94A3B8" />
                    <circle cx="90" cy="90" r="3.5" fill="#94A3B8" />
                    <circle cx="110" cy="90" r="3.5" fill="#94A3B8" />
                </svg>
            </div>

            <!-- ── Konten Utama Filosofi (Rata Tengah, Teks Proporsional dengan Aksen Oren) ── -->
            <div class="relative z-10 w-full max-w-3xl lg:max-w-4xl mx-auto px-6 sm:px-12 text-center flex flex-col items-center" data-animate="fade-up">
                
                <!-- Logo Sekolah Rata Tengah Menggantikan Badge Filosofi -->
                <div class="mb-5 sm:mb-6">
                    <img src="{{ $schoolLogo }}" 
                         alt="{{ $schoolName }}" 
                         class="w-16 h-16 sm:w-20 sm:h-20 object-contain drop-shadow-xs hover:scale-105 transition-transform duration-300">
                </div>

                <!-- Teks Manifesto Utama (Diperkecil & Diberi Aksen Warna Oren Sesuai Permintaan) -->
                <blockquote class="text-base sm:text-lg md:text-xl text-slate-800 font-semibold leading-relaxed sm:leading-relaxed md:leading-normal tracking-normal text-center max-w-2xl sm:max-w-3xl">
                    &ldquo;Di SMK Wikrama 1 Garut, kami mempersiapkan siswa menjadi <span class="highlight-orange">versi terbaik</span> dirinya melalui pendidikan <span class="highlight-orange">karakter</span>, <span class="highlight-orange">disiplin</span>, dan pengembangan <span class="highlight-orange">potensi</span>. Sebab kami percaya, pendidikan bukan hanya tentang mencerdaskan pikiran, tetapi juga menumbuhkan <span class="highlight-orange">hati</span>, membangun <span class="highlight-orange">kemandirian</span>, dan menyiapkan generasi yang kehadirannya membawa <span class="highlight-orange">kebaikan bagi sesama</span>.&rdquo;
                </blockquote>

                <!-- Nama Kepala Sekolah di Tengah -->
                <div class="mt-6 sm:mt-8 pt-5 sm:pt-6 border-t border-slate-200/80 w-full max-w-xs mx-auto text-center">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight leading-tight">
                        Kunedi S,Si., Gr.
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                        Kepala SMK Wikrama 1 Garut
                    </p>
                </div>

            </div>

            <!-- ── Link & Match Industri • BMW (Bekerja, Melanjutkan, Wirausaha) ── -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 sm:mt-12 w-full relative z-10" data-animate="fade-up">
                @php
                    $rawMitra = $cfg['mitra']['items'] ?? $cfg['mitra'] ?? [];
                    $activeMitra = collect($rawMitra)
                        ->filter(fn($m) => !isset($m['is_active']) || $m['is_active'])
                        ->values();
                @endphp

                <!-- Marquee Ticker: Clean Logo Strip -->
                <div class="partner-marquee-container relative w-full overflow-hidden py-3 sm:py-4">
                    <!-- Fade Edges on Canvas -->
                    <div class="absolute left-0 inset-y-0 w-12 sm:w-20 bg-gradient-to-r from-white to-transparent z-20 pointer-events-none"></div>
                    <div class="absolute right-0 inset-y-0 w-12 sm:w-20 bg-gradient-l from-white to-transparent z-20 pointer-events-none"></div>

                    <div class="partner-marquee-track flex items-center hover:[animation-play-state:paused]">
                        <!-- First Loop -->
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

        </section>
