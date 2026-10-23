<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    @php
        $cfg = class_exists(\App\Modules\AdministrasiWeb\Models\WebSetting::class)
            ? \App\Modules\AdministrasiWeb\Models\WebSetting::mergedLandingConfig()
            : (config('landing') ?? []);
        $headerMenus = class_exists(\App\Modules\AdministrasiWeb\Models\WebMenu::class)
            ? \App\Modules\AdministrasiWeb\Models\WebMenu::getHeaderMenu()
            : collect();
        $footerMenus = class_exists(\App\Modules\AdministrasiWeb\Models\WebMenu::class)
            ? \App\Modules\AdministrasiWeb\Models\WebMenu::getFooterMenu()
            : collect();
        $sekolah = $sekolah ?? \App\Modules\Sistem\Models\School::current();
        $appName = $sekolah->nama_aplikasi && $sekolah->nama_aplikasi !== 'Cambling' ? $sekolah->nama_aplikasi : 'SIRAGA';
        $schoolName = $sekolah->nama ?? ($cfg['branding']['school_name'] ?? 'SMK Wikrama 1 Garut');
        $faviconUrl = $sekolah->logoAplikasiUrl() ?: $sekolah->logoSekolahUrl() ?: asset('images/landing/wikrama-logo.png');
        $logoUrl = file_exists(public_path('images/landing/wikrama-logo.png')) 
            ? asset('images/landing/wikrama-logo.png') 
            : ($sekolah->logoSekolahUrl() ?: asset('favicon.png'));
    @endphp
    <title>{{ $schoolName }} &bull; {{ $cfg['branding']['tagline'] ?? 'Learning in Indonesia to be Best for the World' }}</title>
    <meta name="description" content="SMK Wikrama 1 Garut - Sekolah Menengah Kejuruan Pusat Keunggulan bidang Teknologi, Bisnis Digital, dan Hospitaliti Pariwisata berakhlakul karimah.">
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">

    <!-- Google Fonts: Plus Jakarta Sans (Google Sans proportions), Roboto Mono (Technical Badges & Accents), & Playfair Display (Heritage Editorial) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400;1,600;1,700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Unified High-Contrast Institutional Design System -->
    <style>
        [x-cloak] { display: none !important; }

        :root {
            --jis-navy: #014694;
            --jis-navy-dark: #07234B;
            --jis-midnight: #0B192C;
            --jis-gold: #D4AF37;
            --jis-gold-hover: #C59B27;
            
            /* Google AI Tokens */
            --gemini-ease-out-quart: cubic-bezier(0.165, 0.84, 0.44, 1);
            --gemini-ease-out-quad: cubic-bezier(0.25, 0.46, 0.45, 0.94);
            --gemini-blue: #1A73E8;
            --gemini-cyan: #40A2FF;
            --gemini-purple: #8B5CF6;
            --gemini-pink: #EC4899;
        }

        html, body {
            max-width: 100%;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #FAFAFC;
            color: #1F2937;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .dark body {
            background-color: #060D1A;
            color: #F3F4F6;
        }

        .font-editorial {
            font-family: 'Playfair Display', Georgia, serif;
        }

        .font-mono-code {
            font-family: 'Roboto Mono', monospace;
        }

        /* ── Hero Dark Overlay System (Dynamic Dark & Brightness) ── */
        .hero-bg-overlay {
            backdrop-filter: blur(1px);
            -webkit-backdrop-filter: blur(1px);
        }
        .hero-bg-img {
            will-change: filter;
        }

        /* ── Multi-Stop Gradient Text ── */
        .gemini-gradient-text {
            background: linear-gradient(90deg, #38BDF8 0%, #60A5FA 30%, #A78BFA 70%, #F472B6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gemini-gold-gradient {
            background: linear-gradient(90deg, #FDE68A 0%, #F59E0B 50%, #F97316 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ── Concentric Card System ── */
        .google-card {
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 24px;
            box-shadow: 0 4px 20px -2px rgba(1, 70, 148, 0.04);
            transition: transform 0.35s var(--gemini-ease-out-quart), 
                        box-shadow 0.35s var(--gemini-ease-out-quart), 
                        border-color 0.35s var(--gemini-ease-out-quart);
            will-change: transform, box-shadow;
        }
        .google-card:hover {
            transform: translateY(-4px) scale(1.006);
            box-shadow: 0 16px 36px -6px rgba(1, 70, 148, 0.12);
            border-color: rgba(26, 115, 232, 0.35);
        }
        .dark .google-card {
            background: #0B192C;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.4);
        }
        .dark .google-card:hover {
            box-shadow: 0 16px 36px -6px rgba(26, 115, 232, 0.25);
            border-color: rgba(59, 130, 246, 0.4);
        }

        /* Inner media radius follows concentric rule: R_inner = R_outer - padding */
        .google-card-inner {
            border-radius: 16px;
            overflow: hidden;
        }

        /* ── Google Chip & Tag Badges ── */
        .google-tag {
            font-family: 'Roboto Mono', monospace;
            font-size: 0.6875rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 0.35rem 0.9rem;
            border-radius: 9999px;
            background: #EFF6FF;
            color: #1A73E8;
            border: 1px solid rgba(26, 115, 232, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .dark .google-tag {
            background: rgba(26, 115, 232, 0.15);
            color: #93C5FD;
            border-color: rgba(26, 115, 232, 0.35);
        }

        /* ── Action Buttons ── */
        .btn-google-primary {
            background: linear-gradient(90deg, #014694 0%, #1A73E8 100%);
            color: #FFFFFF;
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.8rem 1.85rem;
            transition: all 0.3s var(--gemini-ease-out-quart);
            box-shadow: 0 4px 14px rgba(26, 115, 232, 0.25);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-google-primary:hover {
            background: linear-gradient(90deg, #07234B 0%, #1557B0 100%);
            box-shadow: 0 8px 24px rgba(26, 115, 232, 0.4);
            transform: translateY(-2px);
        }
        .btn-google-primary:active {
            transform: scale(0.98);
        }

        .btn-google-gold {
            background: linear-gradient(90deg, #D4AF37 0%, #F59E0B 100%);
            color: #07234B;
            font-weight: 800;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.8rem 1.85rem;
            transition: all 0.3s var(--gemini-ease-out-quart);
            box-shadow: 0 4px 14px rgba(212, 175, 55, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-google-gold:hover {
            background: linear-gradient(90deg, #C59B27 0%, #D97706 100%);
            box-shadow: 0 8px 24px rgba(212, 175, 55, 0.45);
            transform: translateY(-2px);
        }
        .btn-google-gold:active {
            transform: scale(0.98);
        }

        .btn-google-outline {
            background: transparent;
            color: #1F2937;
            border: 1.5px solid #D1D5DB;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.8rem 1.75rem;
            transition: all 0.3s var(--gemini-ease-out-quart);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-google-outline:hover {
            border-color: #1A73E8;
            color: #1A73E8;
            background: rgba(26, 115, 232, 0.04);
            transform: translateY(-2px);
        }
        .dark .btn-google-outline {
            color: #E5E7EB;
            border-color: #374151;
        }
        .dark .btn-google-outline:hover {
            border-color: #60A5FA;
            color: #60A5FA;
            background: rgba(96, 165, 250, 0.08);
        }

        /* ── Sticky Quick-Action Dock ── */
        .hp-sticky-dock {
            position: fixed;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            z-index: 45;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .hp-sticky-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #014694;
            color: #FFFFFF;
            padding: 10px 16px;
            border-top-left-radius: 9999px;
            border-bottom-left-radius: 9999px;
            box-shadow: -4px 6px 20px rgba(1, 70, 148, 0.25);
            font-size: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s var(--gemini-ease-out-quart);
            transform: translateX(calc(100% - 46px));
        }
        .hp-sticky-btn:hover {
            transform: translateX(0);
            background: #07234B;
            box-shadow: -6px 8px 24px rgba(1, 70, 148, 0.4);
        }
        .hp-sticky-btn.highlight {
            background: linear-gradient(90deg, #D4AF37 0%, #F59E0B 100%);
            color: #07234B;
        }
        .hp-sticky-btn.highlight:hover {
            background: linear-gradient(90deg, #C59B27 0%, #D97706 100%);
            color: #000000;
        }
        .dark .hp-sticky-btn {
            background: #0B192C;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-right: none;
        }

        /* ── Subtle Geometric Grid Background ── */
        .google-grid-pattern {
            background-image: linear-gradient(to right, rgba(255, 255, 255, 0.06) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(255, 255, 255, 0.06) 1px, transparent 1px);
            background-size: 36px 36px;
        }

        /* ── Dual Animated Rotating Rings ── */
        @keyframes geminiRingSpinCW {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @keyframes geminiRingSpinCCW {
            from { transform: rotate(0deg); }
            to { transform: rotate(-360deg); }
        }
        .gemini-ring-small {
            animation: geminiRingSpinCW 24s linear infinite;
        }
        .gemini-ring-large {
            animation: geminiRingSpinCCW 18s linear infinite;
        }

        /* ── Infinite Partner Marquee (Kiri ke Kanan & Interaktif) ── */
        @keyframes partnerMarqueeLtoR {
            0% {
                -webkit-transform: translate3d(-50%, 0, 0);
                transform: translate3d(-50%, 0, 0);
            }
            100% {
                -webkit-transform: translate3d(0%, 0, 0);
                transform: translate3d(0%, 0, 0);
            }
        }
        @-webkit-keyframes partnerMarqueeLtoR {
            0% {
                -webkit-transform: translate3d(-50%, 0, 0);
                transform: translate3d(-50%, 0, 0);
            }
            100% {
                -webkit-transform: translate3d(0%, 0, 0);
                transform: translate3d(0%, 0, 0);
            }
        }
        .partner-marquee-container {
            mask-image: linear-gradient(to right, transparent 0%, black 6%, black 94%, transparent 100%);
            -webkit-mask-image: linear-gradient(to right, transparent 0%, black 6%, black 94%, transparent 100%);
        }
        .partner-marquee-track {
            display: flex !important;
            width: max-content !important;
            -webkit-animation: partnerMarqueeLtoR 32s linear infinite !important;
            animation: partnerMarqueeLtoR 32s linear infinite !important;
            will-change: transform;
        }
        /* Jeda HANYA saat kursor mouse menyentuh barisan logo secara langsung, bukan di seluruh area kosong kontainer */
        .partner-marquee-track:hover {
            -webkit-animation-play-state: paused !important;
            animation-play-state: paused !important;
        }

        /* ── Reaksi Interaktif Barisan Logo saat di-Hover ── */
        .partner-card {
            transition: transform 0.35s cubic-bezier(0.165, 0.84, 0.44, 1), 
                        box-shadow 0.35s cubic-bezier(0.165, 0.84, 0.44, 1), 
                        border-color 0.35s cubic-bezier(0.165, 0.84, 0.44, 1),
                        background-color 0.35s cubic-bezier(0.165, 0.84, 0.44, 1),
                        opacity 0.35s ease,
                        filter 0.35s ease;
            position: relative;
            cursor: pointer;
        }

        /* Saat kursor mouse masuk ke barisan, kartu yang tidak di-hover meredup secara halus */
        .partner-marquee-track:hover .partner-card:not(:hover) {
            opacity: 0.45;
            filter: grayscale(80%);
            transform: scale(0.96);
        }

        /* Kartu yang disentuh kursor terangkat, membesar, bersinar warna biru, dan logo menyala penuh */
        .partner-card:hover {
            transform: translateY(-8px) scale(1.08) !important;
            opacity: 1 !important;
            filter: none !important;
            z-index: 30;
            background-color: #FFFFFF !important;
            border-color: #1A73E8 !important;
            box-shadow: 0 16px 36px -6px rgba(26, 115, 232, 0.25), 0 0 0 2px rgba(26, 115, 232, 0.35) !important;
        }
        .dark .partner-card:hover {
            background-color: #0F1F38 !important;
            border-color: #60A5FA !important;
            box-shadow: 0 16px 36px -6px rgba(0, 0, 0, 0.7), 0 0 0 2px rgba(96, 165, 250, 0.45) !important;
        }

        /* ── Google AI Scroll Reveal System ── */
        [data-animate] {
            opacity: 0;
            transform: translateY(30px) scale(0.98);
            transition: transform 1.0s var(--gemini-ease-out-quart), 
                        opacity 0.85s var(--gemini-ease-out-quart);
            will-change: transform, opacity;
        }
        [data-animate].is-visible {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        [data-animate-delay="1"] { transition-delay: 0.1s !important; }
        [data-animate-delay="2"] { transition-delay: 0.2s !important; }
        [data-animate-delay="3"] { transition-delay: 0.3s !important; }
        [data-animate-delay="4"] { transition-delay: 0.4s !important; }
        [data-animate-delay="5"] { transition-delay: 0.5s !important; }

        /* ── Interactive Link Arrow Hover Slide ── */
        .group-hover-arrow {
            transition: transform 0.25s var(--gemini-ease-out-quart);
        }
        .group:hover .group-hover-arrow {
            transform: translateX(5px);
        }

        /* ── Soft Gaussian Atmospheric Spotlight Glow ── */
        .mouse-glow {
            position: fixed;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(26, 115, 232, 0.12) 0%, rgba(139, 92, 246, 0.06) 40%, transparent 70%);
            pointer-events: none;
            z-index: 10;
            transform: translate(-50%, -50%);
            transition: opacity 0.3s ease-out;
            opacity: 0;
            filter: blur(50px);
        }
        .mouse-glow.active { opacity: 1; }
        .dark .mouse-glow {
            background: radial-gradient(circle, rgba(59, 130, 246, 0.16) 0%, rgba(236, 72, 153, 0.08) 45%, transparent 70%);
        }

        @media (prefers-reduced-motion: reduce) {
            [data-animate] { opacity: 1 !important; transform: none !important; transition: none !important; }
            .gemini-ring-small, .gemini-ring-large { animation: none !important; }
            .mouse-glow { display: none !important; }
        }
    </style>
    @livewireStyles
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="min-h-screen text-slate-800 dark:text-slate-100 antialiased selection:bg-blue-600 selection:text-white flex flex-col justify-between relative"
      x-data="{ 
          darkMode: localStorage.getItem('theme') === 'dark',
          mobileMenu: false,
          flyerModal: false,
          imageModal: false,
          modalImageSrc: '',
          modalImageTitle: '',
          modalImageCategory: '',
          activeHeroTab: 'spmb',
          heroAutoPlay: true,
          heroTabs: ['spmb', 'tech', 'tjkt', 'bisnis', 'hotel'],
          heroTimer: null,
          activeKnowTab: 'admissions',
          nextHeroTab() {
              const idx = this.heroTabs.indexOf(this.activeHeroTab);
              this.activeHeroTab = this.heroTabs[(idx + 1) % this.heroTabs.length];
          },
          prevHeroTab() {
              const idx = this.heroTabs.indexOf(this.activeHeroTab);
              this.activeHeroTab = this.heroTabs[(idx - 1 + this.heroTabs.length) % this.heroTabs.length];
          },
          startHeroAutoplay() {
              if (this.heroTimer) clearInterval(this.heroTimer);
              this.heroTimer = setInterval(() => {
                  if (this.heroAutoPlay && !this.flyerModal && !this.imageModal) {
                      this.nextHeroTab();
                  }
              }, 6000);
          },
          openImageModal(src, title, cat) {
              this.modalImageSrc = src;
              this.modalImageTitle = title;
              this.modalImageCategory = cat;
              this.imageModal = true;
          },
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          },
          words: {{ json_encode($cfg['hero']['typewriter_words'] ?? ['Teknologi & AI', 'Bisnis Digital', 'Hospitaliti Bintang 5', 'Karakter Qur\'ani', 'Kepemimpinan Global']) }},
          typewriterText: 'Teknologi & AI',
          wordIndex: 0,
          charIndex: 0,
          isDeleting: false,
          initTypewriter() {
              const currentWord = this.words[this.wordIndex] || 'Teknologi & AI';
              let delay = 90;

              if (this.isDeleting) {
                  this.typewriterText = currentWord.substring(0, this.charIndex - 1);
                  this.charIndex--;
                  delay = 45;
              } else {
                  this.typewriterText = currentWord.substring(0, this.charIndex + 1);
                  this.charIndex++;
                  delay = 85;
              }

              if (!this.isDeleting && this.charIndex === currentWord.length) {
                  delay = 2400;
                  this.isDeleting = true;
              } else if (this.isDeleting && this.charIndex === 0) {
                  this.isDeleting = false;
                  this.wordIndex = (this.wordIndex + 1) % this.words.length;
                  delay = 350;
              }

              setTimeout(() => this.initTypewriter(), delay);
          }
      }"
      x-init="initTypewriter(); startHeroAutoplay()">

    <!-- ── Floating Sticky Quick-Action Dock (Only on Extra Wide 2xl+ Displays to prevent layout overlap) ── -->
    <aside class="hidden 2xl:flex hp-sticky-dock" aria-label="Aksi Cepat Sekolah">
        @foreach($cfg['sticky_actions'] ?? [] as $st)
        <a href="{{ $st['url'] }}" 
           target="{{ $st['target'] }}" 
           rel="{{ $st['target'] === '_blank' ? 'noopener noreferrer' : '' }}"
           class="hp-sticky-btn {{ ($st['highlight'] ?? false) ? 'highlight' : '' }}"
           title="{{ $st['label'] }} - {{ $st['sub'] }}">
            <span class="w-5 h-5 flex items-center justify-center shrink-0">
                @if(($st['icon'] ?? '') === 'pencil')
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                @elseif(($st['icon'] ?? '') === 'map-pin')
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                @elseif(($st['icon'] ?? '') === 'download')
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                @elseif(($st['icon'] ?? '') === 'chat')
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                @else
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                @endif
            </span>
            <div class="whitespace-nowrap pr-2">
                <span class="block leading-none">{{ $st['label'] }}</span>
                <span class="block text-[9px] opacity-80 font-normal mt-0.5">{{ $st['sub'] }}</span>
            </div>
        </a>
        @endforeach
    </aside>

    <!-- ── Compact Quick Action FAB for Standard Laptops & Mobile (< 2xl) ── -->
    <div class="2xl:hidden fixed bottom-5 right-4 sm:bottom-6 sm:right-6 z-40" x-data="{ fabOpen: false }" @click.outside="fabOpen = false">
        <div x-show="fabOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-3 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-3 scale-95"
             class="mb-3 flex flex-col items-end gap-2.5">
            <a href="{{ $cfg['branding']['spmb_url'] ?? 'https://spmb.smkwikrama1garut.sch.id/' }}" target="_blank" rel="noopener" 
               class="px-4 py-2 rounded-full bg-amber-500 text-slate-950 text-xs font-bold shadow-xl flex items-center gap-2 hover:bg-amber-400 transition-all border border-amber-300">
                <span>Daftar SPMB Online</span>
                <span>&rarr;</span>
            </a>
            <a href="https://wa.me/6281323314430?text=Halo%20Admin%20SMK%20Wikrama%201%20Garut,%20saya%20ingin%20berkonsultasi%20tentang%20penerimaan%20siswa%20baru" target="_blank" rel="noopener" 
               class="px-4 py-2 rounded-full bg-emerald-600 text-white text-xs font-bold shadow-xl flex items-center gap-2 hover:bg-emerald-500 transition-all border border-emerald-400">
                <span>Chat Admin WhatsApp</span>
                <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
            </a>
            <a href="{{ $cfg['branding']['brochure_url'] ?? 'http://brosur.smkwikrama1garut.sch.id/' }}" target="_blank" rel="noopener" 
               class="px-4 py-2 rounded-full bg-slate-900 text-white text-xs font-semibold shadow-xl flex items-center gap-2 hover:bg-slate-800 transition-all border border-white/20">
                <span>Unduh Brosur Resmi</span>
                <span>&darr;</span>
            </a>
        </div>
        <button type="button" 
                @click="fabOpen = !fabOpen" 
                class="w-12 h-12 rounded-full bg-blue-600 hover:bg-blue-700 text-white shadow-xl flex items-center justify-center transition-all hover:scale-105 active:scale-95 cursor-pointer border-2 border-white/20"
                aria-label="Aksi Cepat Sekolah">
            <svg x-show="!fabOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            <svg x-show="fabOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- ── Tier 1: Institutional Announcement Bar ── -->
    @if($cfg['announcement']['enabled'] ?? true)
    <div class="bg-slate-900 text-white text-xs py-2 px-4 border-b border-white/10 relative z-50">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex items-center gap-2.5 text-center sm:text-left">
                <span class="font-mono-code bg-gradient-to-r from-blue-600 via-indigo-500 to-pink-500 text-white px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider shrink-0">
                    {{ $cfg['announcement']['badge'] ?? 'SPMB 2026/2027' }}
                </span>
                <span class="text-slate-300 font-medium text-[11px] sm:text-xs">
                    {{ $cfg['announcement']['text'] ?? 'Penerimaan Peserta Didik Baru Resmi Dibuka!' }}
                </span>
            </div>

            <div class="flex items-center gap-4 text-[11px] font-semibold text-slate-300">
                <a href="{{ $cfg['announcement']['phone_url'] ?? 'tel:081323314430' }}" class="hidden md:flex items-center gap-1.5 hover:text-white transition-colors">
                    <svg class="w-3.5 h-3.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span>{{ $cfg['announcement']['phone_display'] ?? '0813-2331-4430' }}</span>
                </a>
                <span class="hidden md:inline text-white/20">|</span>
                <a href="{{ $cfg['announcement']['cta_url'] ?? 'https://spmb.smkwikrama1garut.sch.id/' }}" target="_blank" rel="noopener" class="text-blue-400 hover:text-blue-300 font-bold inline-flex items-center gap-1">
                    <span>{{ $cfg['announcement']['cta_text'] ?? 'Daftar SPMB Online' }}</span> &rarr;
                </a>
                <span class="text-white/20">|</span>
                <button type="button" 
                        @click="toggleTheme()" 
                        class="p-1 rounded text-slate-300 hover:text-white transition-colors cursor-pointer"
                        :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'">
                    <span x-show="darkMode" x-cloak>
                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </span>
                    <span x-show="!darkMode">
                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- ── Tier 2: Main Header Navigation ── -->
    <header class="sticky top-0 z-40 bg-white/95 dark:bg-[#071324]/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between gap-2.5 sm:gap-4">
            
            <!-- School Brand Crest -->
            <a href="#beranda" class="flex items-center space-x-2 sm:space-x-3.5 group shrink-0 min-w-0">
                <div class="p-1 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs group-hover:scale-105 transition-transform shrink-0">
                    <img src="{{ $logoUrl }}" alt="Logo {{ $schoolName }}" class="h-8 sm:h-11 w-auto object-contain">
                </div>
                <div class="min-w-0 max-w-[160px] sm:max-w-none">
                    <span class="text-xs sm:text-lg font-bold text-slate-900 dark:text-white tracking-tight block leading-tight group-hover:text-blue-600 transition-colors truncate">
                        {{ $schoolName }}
                    </span>
                    <span class="text-[9px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 flex items-center gap-1 sm:gap-1.5 truncate">
                        <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-emerald-500 shrink-0"></span>
                        <span class="truncate">{{ $cfg['branding']['school_type'] ?? 'Islamic Boarding School' }}</span>
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation Menu (Streamlined Institutional Categories) -->
            <nav class="hidden lg:flex items-center space-x-3 xl:space-x-5 text-xs xl:text-sm font-semibold text-slate-700 dark:text-slate-200">
                @if($headerMenus->isNotEmpty())
                    @foreach($headerMenus as $m)
                        <a href="{{ $m->url }}" target="{{ $m->target }}" class="hover:text-blue-600 py-1 transition-colors">{{ $m->label }}</a>
                    @endforeach
                @else
                    <a href="#beranda" class="hover:text-blue-600 py-1 transition-colors">Beranda</a>
                    <a href="#filosofi" class="hover:text-blue-600 py-1 transition-colors">Profil</a>
                    <a href="#jurusan" class="hover:text-blue-600 py-1 transition-colors">4 Jurusan</a>
                    <a href="#dinamika-siswa" class="hover:text-blue-600 py-1 transition-colors">Dinamika Siswa</a>
                    <a href="#fasilitas" class="hover:text-blue-600 py-1 transition-colors">Fasilitas</a>
                    <a href="#berita" class="hover:text-blue-600 py-1 transition-colors">Berita</a>
                    <a href="#kontak" class="hover:text-blue-600 py-1 transition-colors">Kontak</a>
                @endif
            </nav>

            <!-- Actions: SPMB & Mobile Menu Toggle -->
            <div class="flex items-center space-x-2 sm:space-x-3 shrink-0">
                <a href="{{ $cfg['branding']['spmb_url'] ?? 'https://spmb.smkwikrama1garut.sch.id/' }}" 
                   target="_blank" 
                   rel="noopener" 
                   class="btn-google-gold text-xs sm:text-sm !py-1.5 !px-3 sm:!py-2.5 sm:!px-5 inline-flex items-center gap-1 sm:gap-1.5 shadow-xs">
                    <span>Daftar SPMB</span>
                    <span class="text-xs">&rarr;</span>
                </a>

                <!-- Mobile Menu Hamburger Button -->
                <button type="button" 
                        @click="mobileMenu = !mobileMenu"
                        class="lg:hidden p-1.5 sm:p-2 rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
                        aria-label="Buka Menu Navigasi">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenu" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Navigation -->
        <div x-show="mobileMenu" x-cloak 
             @click="mobileMenu = false"
             x-transition:enter="transition-opacity ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-30 lg:hidden"></div>

        <div x-show="mobileMenu" x-cloak 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="lg:hidden absolute top-full inset-x-0 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 z-40 p-5 shadow-2xl max-h-[calc(100vh-5rem)] overflow-y-auto">
            <div class="flex flex-col space-y-2 text-sm font-semibold text-slate-800 dark:text-slate-100">
                @if($headerMenus->isNotEmpty())
                    @foreach($headerMenus as $m)
                        <a href="{{ $m->url }}" target="{{ $m->target }}" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800">{{ $m->label }}</a>
                    @endforeach
                @else
                    <div class="pb-1 text-[11px] font-mono-code font-bold uppercase tracking-wider text-slate-400">Jelajahi Portal</div>
                    <div class="grid grid-cols-2 gap-1.5">
                        <a href="#beranda" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800">Beranda Utama</a>
                        <a href="#filosofi" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800">Tentang Kami</a>
                        <a href="#jurusan" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800">4 Jurusan Unggulan</a>
                        <a href="#dinamika-siswa" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800">Dinamika Siswa</a>
                        <a href="#fasilitas" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800">Fasilitas &amp; Lab</a>
                        <a href="#berita" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800">Berita &amp; Prestasi</a>
                        <a href="#suara-komunitas" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800">Testimoni</a>
                        <a href="#kontak" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800">Kontak Kami</a>
                    </div>
                @endif
                <div class="pt-4 mt-2 border-t border-slate-200 dark:border-slate-800 flex flex-col gap-2">
                    <a href="{{ $cfg['branding']['spmb_url'] ?? 'https://spmb.smkwikrama1garut.sch.id/' }}" target="_blank" rel="noopener" class="btn-google-gold text-center text-xs py-3 shadow-sm">
                        Daftar SPMB Online Sekarang &rarr;
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- ── Main Content Landmark ── -->
    <main class="flex-grow">

        @php
            $heroBgSetting = $cfg['hero']['background_image'] ?? 'images/web/campus-environment.jpg';
            $heroBgUrl = class_exists(\App\Modules\AdministrasiWeb\Models\WebSetting::class)
                ? \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($heroBgSetting, 'images/web/campus-environment.jpg')
                : asset('images/web/campus-environment.jpg');
            $overlayOpacity = isset($cfg['hero']['overlay_opacity']) ? max(0, min(100, (int)$cfg['hero']['overlay_opacity'])) : 70;
            $overlayAlpha = number_format($overlayOpacity / 100, 2, '.', '');
            $imageBrightness = isset($cfg['hero']['image_brightness']) ? max(0, min(100, (int)$cfg['hero']['image_brightness'])) : 40;
            $brightnessFactor = number_format($imageBrightness / 100, 2, '.', '');
        @endphp

        <!-- ── 1. Hero Section: Modern Split-Showcase (High-Contrast Dark Theme) ── -->
        <section class="relative overflow-hidden pt-8 pb-12 sm:pt-16 sm:pb-24 bg-slate-950 text-white" id="beranda" data-mouse-glow style="background-color: #020617;">
            
            <!-- Background Image with Dynamic Dark Overlay & Dimming (strictly positioned above image and below text) -->
            <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden" style="background-color: #020617;">
                <img src="{{ $heroBgUrl }}" 
                     alt="Latar Belakang Kampus SMK Wikrama 1 Garut" 
                     class="w-full h-full object-cover object-center hero-bg-img"
                     style="filter: brightness({{ $brightnessFactor }}) contrast(1.15);">
                <!-- Dynamic Dark Overlay (strictly positioned above image and below text) -->
                <div class="absolute inset-0 hero-bg-overlay"
                     style="background-color: rgba(2, 6, 23, {{ $overlayAlpha }}); backdrop-filter: blur(1px); -webkit-backdrop-filter: blur(1px);"></div>
                <!-- Ambient subtle atmospheric glow -->
                <div class="absolute -top-32 -right-20 w-[550px] h-[550px] bg-gradient-to-br from-blue-600/20 via-indigo-600/15 to-transparent rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute top-1/2 -left-28 w-[450px] h-[450px] bg-gradient-to-tr from-amber-500/15 via-blue-500/10 to-transparent rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute inset-0 google-grid-pattern opacity-15 pointer-events-none"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-8 items-center">
                    
                    <!-- ── Left Column: Hero Narrative, SPMB CTA & Trust Proof ── -->
                    <div class="lg:col-span-7 text-center lg:text-left">
                        
                        <!-- Eyebrow Pill Tag with Monospace Code Accent -->
                        <div class="inline-flex items-center gap-1.5 sm:gap-2 px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full text-[11px] sm:text-xs font-semibold bg-blue-950/80 text-blue-300 border border-blue-700/60 mb-5 sm:mb-6 shadow-lg max-w-full" data-animate>
                            <span class="w-2 h-2 rounded-full bg-blue-400 animate-ping shrink-0"></span>
                            <span class="font-mono-code font-bold text-amber-400 shrink-0">[ SPMB 2026/2027 ]</span>
                            <span class="truncate">{{ $cfg['hero']['tag'] ?? 'SMK Pusat Keunggulan • ISLAMIC BOARDING SCHOOL' }}</span>
                        </div>

                        <!-- Main Title with Clean Hierarchy & Google AI Typography -->
                        <h1 class="text-3xl sm:text-5xl md:text-6xl font-extrabold text-white tracking-tight leading-[1.14]" data-animate data-animate-delay="1">
                            {{ $cfg['hero']['title_line1'] ?? 'Solusi Pendidikan Akhlak Berkualitas' }} <br>
                            <span class="bg-gradient-to-r from-blue-400 via-indigo-300 to-pink-400 bg-clip-text text-transparent">
                                {{ $cfg['hero']['title_highlight'] ?? 'Terbaik bagi Masa Depan' }}
                            </span>
                            <span class="block mt-2 text-xl sm:text-3xl md:text-4xl font-bold text-slate-100">
                                <span class="bg-gradient-to-r from-amber-400 to-orange-400 bg-clip-text text-transparent" x-text="typewriterText">Teknologi &amp; AI</span>
                                <span class="inline-block w-1 h-5 sm:h-8 bg-blue-400 align-middle ml-1 animate-pulse"></span>
                            </span>
                        </h1>

                        <!-- Lead Paragraph: Crisp, Readable White/Slate Text -->
                        <p class="mt-4 sm:mt-6 text-sm sm:text-lg text-slate-300 leading-relaxed max-w-2xl mx-auto lg:mx-0 font-normal" data-animate data-animate-delay="2">
                            {{ $cfg['hero']['description'] ?? 'Keahlian teknologi, ketangkasan bisnis digital, dan standar pelayanan prima hospitaliti dipadukan harmonis dengan keteguhan adab dan integritas spiritual Al-Qur\'an.' }}
                        </p>

                        <!-- Action Buttons Group (Clean 2-Action Hierarchy + Tertiary Link) -->
                        <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row flex-wrap items-center justify-center lg:justify-start gap-3 sm:gap-4" data-animate data-animate-delay="3">
                            <a href="{{ $cfg['branding']['spmb_url'] ?? 'https://spmb.smkwikrama1garut.sch.id/' }}" 
                               target="_blank" 
                               rel="noopener" 
                               class="btn-google-gold shadow-lg shadow-amber-500/20 w-full sm:w-auto">
                                <span>Daftar SPMB 2026/2027</span>
                                <span class="group-hover-arrow">&rarr;</span>
                            </a>
                            <a href="#mengenal" class="btn-google-outline !text-white !border-white/30 hover:!border-white hover:!bg-white/10 w-full sm:w-auto">
                                <span>Mengenal Wikrama</span>
                                <span class="group-hover-arrow">&rarr;</span>
                            </a>
                            <a href="{{ $cfg['branding']['brochure_url'] ?? 'http://brosur.smkwikrama1garut.sch.id/' }}" 
                               target="_blank" 
                               rel="noopener" 
                               class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-300 hover:text-white px-3.5 py-2 rounded-full hover:bg-white/10 transition-colors w-full sm:w-auto justify-center">
                                <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                <span>Unduh Brosur (PDF)</span>
                            </a>
                        </div>

                        <!-- Technical Monospace Metrics Strip -->
                        <div class="mt-8 sm:mt-12 pt-6 sm:pt-8 border-t border-white/10 grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6 text-center sm:text-left" data-animate data-animate-delay="4">
                            @foreach($cfg['hero']['stats'] ?? [] as $st)
                            <div class="pr-2">
                                <div class="text-xl sm:text-3xl font-extrabold font-mono-code text-blue-400">
                                    {{ $st['number'] }}
                                </div>
                                <div class="text-xs font-bold text-white mt-1 leading-snug">
                                    {{ $st['label'] }}
                                </div>
                                <div class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5 font-medium">
                                    {{ $st['sub'] }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- ── Right Column: Interactive 3-Pillar Experience Studio Showcase ── -->
                    <div class="lg:col-span-5 w-full max-w-lg mx-auto lg:mx-0" data-animate>
                        <div class="p-3 sm:p-4 rounded-3xl bg-slate-900/90 border border-white/15 shadow-2xl backdrop-blur-md transition-all duration-300">
                            
                            <!-- Interactive Tab Switcher (Horizontal Swipe on Mobile, Even Distribution on Tablet/Desktop) -->
                            <div class="flex overflow-x-auto no-scrollbar gap-1 p-1 bg-slate-950/80 border border-white/10 rounded-2xl mb-3">
                                @foreach($cfg['hero']['slides'] ?? [] as $sl)
                                <button type="button" 
                                        @click="activeHeroTab = '{{ $sl['id'] }}'; heroAutoPlay = false" 
                                        :class="activeHeroTab === '{{ $sl['id'] }}' 
                                            ? 'bg-blue-600 text-white font-bold shadow-xs' 
                                            : 'text-slate-300 font-medium hover:text-white'" 
                                        class="shrink-0 flex-1 py-1.5 sm:py-2 px-2.5 sm:px-2 rounded-xl text-[11px] font-semibold transition-all text-center whitespace-nowrap cursor-pointer">
                                    {{ $sl['tab_name'] }}
                                </button>
                                @endforeach
                            </div>

                            <!-- Showcase Display Canvas following Concentric Radius -->
                            <div class="relative rounded-2xl overflow-hidden aspect-[4/3] bg-slate-950 group">
                                @foreach($cfg['hero']['slides'] ?? [] as $sl)
                                <div x-show="activeHeroTab === '{{ $sl['id'] }}'"
                                     x-transition:enter="transition-opacity duration-500 ease-out"
                                     x-transition:enter-start="opacity-0 scale-98"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition-opacity duration-300 ease-in"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     class="absolute inset-0">
                                    <img src="{{ asset($sl['image']) }}" 
                                         alt="{{ $sl['title'] }}" 
                                         class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out">
                                    
                                    <!-- Subtle Gradient Scrim on Image to protect tags -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-transparent to-slate-950/40"></div>

                                    <!-- Top Floating Badge -->
                                    <div class="absolute top-3 left-3 right-3 flex items-center justify-between">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-900/90 text-blue-300 border border-white/10 backdrop-blur-md shadow-xs">
                                            {{ $sl['badge'] }}
                                        </span>
                                        <button type="button" 
                                                @click="{{ $sl['id'] === 'spmb' ? 'flyerModal = true' : 'openImageModal(\'' . asset($sl['image']) . '\', \'' . $sl['title'] . '\', \'' . $sl['badge'] . '\')' }}"
                                                class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-950/70 hover:bg-slate-950/90 text-white backdrop-blur-md transition-colors cursor-pointer inline-flex items-center gap-1 border border-white/10"
                                                title="Lihat Gambar Penuh">
                                            <svg class="w-3 h-3 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                            <span>Perbesar</span>
                                        </button>
                                    </div>

                                    <!-- Bottom Caption Over Image -->
                                    <div class="absolute bottom-3 left-3 right-3 p-3 rounded-xl bg-slate-950/85 backdrop-blur-md border border-white/10 text-white">
                                        <h4 class="text-xs sm:text-sm font-bold leading-snug line-clamp-1">{{ $sl['title'] }}</h4>
                                        <p class="text-[11px] text-slate-300 mt-1 line-clamp-2 leading-relaxed">{{ $sl['desc'] }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Showcase Bottom Trust Bar & Slide Indicator -->
                            <div class="mt-3.5 pt-3 border-t border-white/10 flex items-center justify-between px-1">
                                <div class="flex items-center gap-3 text-xs font-semibold text-slate-300">
                                    <span class="flex items-center gap-1.5 text-emerald-400">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                        Akreditasi A Unggul
                                    </span>
                                    <span class="text-slate-600">&bull;</span>
                                    <span class="flex items-center gap-1.5 text-amber-400">
                                        <img src="{{ asset('images/landing/logo-smk-pk-resmi.png') }}" alt="Logo SMK PK" class="h-3.5 w-auto object-contain">
                                        SMK PK
                                    </span>
                                </div>

                                <!-- Slide Autoplay Dots Indicator -->
                                <div class="flex items-center gap-1.5">
                                    @foreach($cfg['hero']['slides'] ?? [] as $sl)
                                    <button type="button" 
                                            @click="activeHeroTab = '{{ $sl['id'] }}'; heroAutoPlay = false" 
                                            :class="activeHeroTab === '{{ $sl['id'] }}' ? 'w-5 bg-blue-400' : 'w-1.5 bg-slate-700'"
                                            class="h-1.5 rounded-full transition-all duration-300 cursor-pointer"
                                            :aria-label="'Pilih slide ' + '{{ $sl['tab_name'] }}'"></button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ── 2. "Welcome to Wikrama" Signature Manifesto Section ── -->
        <section class="py-12 sm:py-20 lg:py-28 relative overflow-hidden bg-slate-950 text-white" id="filosofi">
            <!-- Background Student Activity Image with Dark Elegant Overlay (above image, below text) -->
            <div class="absolute inset-0 z-0 pointer-events-none">
                <img src="{{ asset('images/web/activity-spiritual.jpg') }}" alt="Kegiatan Ibadah & Karakter Siswa Wikrama" class="w-full h-full object-cover object-center opacity-25 filter blur-[2px] brightness-[0.20] contrast-125">
                <div class="absolute inset-0 bg-slate-950/92 dark:bg-slate-950/96 backdrop-blur-[1px]"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10" data-animate>
                <span class="google-tag bg-blue-950/80 text-blue-300 border-blue-700/60 shadow-lg">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    <span>{{ $cfg['welcome']['tag'] ?? 'Filosofi Pendidikan Wikrama' }}</span>
                </span>
                
                <!-- Modern Sans & Monospace Combined Manifesto Quote -->
                <div class="mt-6 sm:mt-8 p-5 sm:p-10 rounded-3xl bg-slate-900/95 border border-white/15 text-left sm:text-center w-full max-w-5xl mx-auto shadow-2xl backdrop-blur-md">
                    <span class="font-mono-code text-xs font-bold uppercase tracking-wider text-blue-400 block mb-3">
                        [ MANIFESTO PENDIDIKAN WIKRAMA ]
                    </span>
                    <blockquote class="font-sans text-lg sm:text-2xl md:text-3xl text-white leading-relaxed font-medium tracking-tight">
                        &ldquo;{{ $cfg['welcome']['quote'] ?? 'Di SMK Wikrama 1 Garut, kami mempersiapkan anak bangsa dari berbagai daerah untuk menjadi yang terbaik dalam keahlian yang mereka tekuni, sekaligus menjadi pribadi yang membawa kemaslahatan terbaik bagi keluarga, masyarakat, dan masa depan peradaban.' }}&rdquo;
                    </blockquote>
                    <div class="mt-6 pt-4 border-t border-white/10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs">
                        <span class="text-amber-300 font-semibold tracking-wide">
                            &mdash; {{ $cfg['welcome']['author'] ?? 'Kunedi, S.Si. — Kepala SMK Wikrama 1 Garut' }}
                        </span>
                        <span class="font-mono-code text-[11px] text-slate-400 uppercase tracking-widest">[ MANIFESTO INSTITUSI ]</span>
                    </div>
                </div>

                <!-- 4 Core Educational Pillars -->
                <div class="mt-10 sm:mt-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 text-left w-full">
                    @foreach($cfg['welcome']['pillars'] ?? [] as $idx => $pil)
                    <div class="p-6 rounded-3xl bg-slate-900/80 border border-white/10 hover:border-blue-500/50 transition-all duration-300 backdrop-blur-sm flex flex-col justify-between h-full group" data-animate data-animate-delay="{{ $idx + 1 }}">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                                {{ $pil['icon'] }}
                            </div>
                            <h4 class="text-base font-bold text-white mb-2 group-hover:text-blue-400 transition-colors">
                                {{ $pil['name'] }}
                            </h4>
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                                {{ $pil['desc'] }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ── 3. "Get to Know Wikrama" Editorial Tabbed Showcase ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-slate-50 dark:bg-[#060D1A]" id="mengenal">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Section Header -->
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14" data-animate>
                    <span class="google-tag">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        <span>{{ $cfg['get_to_know']['tag'] ?? 'Mengenal Lebih Dekat' }}</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-bold tracking-tight text-slate-900 dark:text-white mt-3 sm:mt-4">
                        {{ $cfg['get_to_know']['title'] ?? 'Menemukan Potensi Sejati di Wikrama' }}
                    </h2>
                    <p class="mt-3 sm:mt-4 text-sm sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                        {{ $cfg['get_to_know']['subtitle'] ?? 'Jelajahi ekosistem pembelajaran holistik yang menggabungkan keunggulan akademik, riset teknologi industri, dan pembinaan kepemimpinan islami.' }}
                    </p>

                    <!-- Editorial Tab Navigation -->
                    <div class="mt-8 flex flex-wrap justify-center sm:inline-flex p-1 sm:p-1.5 rounded-2xl sm:rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs gap-1 sm:gap-0 max-w-full">
                        <button type="button" 
                                @click="activeKnowTab = 'admissions'"
                                :class="activeKnowTab === 'admissions' ? 'bg-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-300 font-semibold hover:text-slate-900'"
                                class="px-3.5 sm:px-5 py-1.5 sm:py-2 rounded-xl sm:rounded-full text-xs sm:text-sm transition-all cursor-pointer">
                            {{ $cfg['get_to_know']['tabs']['admissions']['name'] ?? 'Penerimaan Siswa' }}
                        </button>
                        <button type="button" 
                                @click="activeKnowTab = 'academics'"
                                :class="activeKnowTab === 'academics' ? 'bg-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-300 font-semibold hover:text-slate-900'"
                                class="px-3.5 sm:px-5 py-1.5 sm:py-2 rounded-xl sm:rounded-full text-xs sm:text-sm transition-all cursor-pointer">
                            {{ $cfg['get_to_know']['tabs']['academics']['name'] ?? 'Kompetensi Keahlian' }}
                        </button>
                        <button type="button" 
                                @click="activeKnowTab = 'boarding'"
                                :class="activeKnowTab === 'boarding' ? 'bg-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-300 font-semibold hover:text-slate-900'"
                                class="px-3.5 sm:px-5 py-1.5 sm:py-2 rounded-xl sm:rounded-full text-xs sm:text-sm transition-all cursor-pointer">
                            {{ $cfg['get_to_know']['tabs']['boarding']['name'] ?? 'Boarding & Karakter' }}
                        </button>
                    </div>
                </div>

                <!-- Tab Panel 1: Admissions -->
                <div x-show="activeKnowTab === 'admissions'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-3"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                    @foreach($cfg['get_to_know']['tabs']['admissions']['cards'] ?? [] as $idx => $c)
                    <div class="google-card overflow-hidden flex flex-col justify-between h-full group">
                        <div>
                            <div class="aspect-[16/10] w-full relative overflow-hidden bg-slate-950">
                                <img src="{{ asset($c['image']) }}" alt="{{ $c['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                <span class="absolute top-3 left-3 bg-white/95 dark:bg-slate-900/95 text-blue-700 dark:text-blue-300 font-mono-code text-[10px] font-bold px-3 py-1 rounded-full shadow-xs">
                                    {{ $c['badge'] }}
                                </span>
                            </div>
                            <div class="p-6 pt-4">
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2 leading-snug group-hover:text-blue-600 transition-colors">
                                    {{ $c['title'] }}
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                                    {{ $c['desc'] }}
                                </p>
                            </div>
                        </div>
                        <div class="px-6 pb-6">
                            <a href="{{ $c['url'] }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 inline-flex items-center gap-1.5">
                                <span>{{ $c['cta'] }}</span>
                                <span class="group-hover-arrow">&rarr;</span>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Tab Panel 2: Academics -->
                <div x-show="activeKnowTab === 'academics'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-3"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($cfg['get_to_know']['tabs']['academics']['cards'] ?? [] as $idx => $c)
                    <div class="google-card overflow-hidden flex flex-col justify-between h-full group">
                        <div>
                            <div class="aspect-[16/10] w-full relative overflow-hidden bg-slate-950">
                                <img src="{{ asset($c['image']) }}" alt="{{ $c['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                <span class="absolute top-3 left-3 bg-white/95 dark:bg-slate-900/95 text-blue-700 dark:text-blue-300 font-mono-code text-[10px] font-bold px-3 py-1 rounded-full shadow-xs">
                                    {{ $c['badge'] }}
                                </span>
                            </div>
                            <div class="p-5 pt-4">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2 leading-snug group-hover:text-blue-600 transition-colors">
                                    {{ $c['title'] }}
                                </h3>
                                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                                    {{ $c['desc'] }}
                                </p>
                            </div>
                        </div>
                        <div class="px-5 pb-5">
                            <a href="{{ $c['url'] }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 inline-flex items-center gap-1.5">
                                <span>{{ $c['cta'] }}</span>
                                <span class="group-hover-arrow">&rarr;</span>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Tab Panel 3: Boarding & Character -->
                <div x-show="activeKnowTab === 'boarding'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-3"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                    @foreach($cfg['get_to_know']['tabs']['boarding']['cards'] ?? [] as $idx => $c)
                    <div class="google-card overflow-hidden flex flex-col justify-between h-full group">
                        <div>
                            <div class="aspect-[16/10] w-full relative overflow-hidden bg-slate-950">
                                <img src="{{ asset($c['image']) }}" alt="{{ $c['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                <span class="absolute top-3 left-3 bg-white/95 dark:bg-slate-900/95 text-blue-700 dark:text-blue-300 font-mono-code text-[10px] font-bold px-3 py-1 rounded-full shadow-xs">
                                    {{ $c['badge'] }}
                                </span>
                            </div>
                            <div class="p-6 pt-4">
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2 leading-snug group-hover:text-blue-600 transition-colors">
                                    {{ $c['title'] }}
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                                    {{ $c['desc'] }}
                                </p>
                            </div>
                        </div>
                        <div class="px-6 pb-6">
                            <a href="{{ $c['url'] }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 inline-flex items-center gap-1.5">
                                <span>{{ $c['cta'] }}</span>
                                <span class="group-hover-arrow">&rarr;</span>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ── 4. "Where Graduates Succeed" Partner Marquee Ribbon ── -->
        <section class="py-10 sm:py-16 bg-white dark:bg-[#071324] overflow-hidden transition-colors duration-300" id="mitra" data-mouse-glow>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center mb-6 sm:mb-8" data-animate>
                <span class="google-tag mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <span>Link &amp; Match Industri &bull; BMW (Bekerja, Melanjutkan, Wirausaha)</span>
                </span>
                <p class="text-xs sm:text-sm font-bold font-mono-code uppercase tracking-widest text-slate-500 dark:text-slate-400">
                    Jejaring Kemitraan Kampus Perguruan Tinggi &amp; Korporasi Teknologi Global
                </p>
            </div>
            <div class="partner-marquee-container relative w-full overflow-hidden py-6 sm:py-8">
                <div class="partner-marquee-track flex items-center">
                    <!-- First Set -->
                    <div class="flex items-center gap-6 sm:gap-8 shrink-0 pr-6 sm:pr-8">
                        @foreach($cfg['mitra'] ?? [] as $m)
                        <div class="partner-card group/item p-3.5 sm:p-4 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 rounded-2xl h-16 sm:h-20 w-36 sm:w-44 flex items-center justify-center shrink-0">
                            <img src="{{ asset($m['image']) }}" alt="{{ $m['name'] }}" title="{{ $m['name'] }}" class="max-h-8 sm:max-h-10 max-w-full object-contain grayscale group-hover/item:grayscale-0 transition-all duration-300 opacity-75 group-hover/item:opacity-100 group-hover/item:scale-105">
                            
                            <!-- Floating Partner Tooltip Badge -->
                            <div class="absolute -top-7 left-1/2 -translate-x-1/2 pointer-events-none opacity-0 group-hover/item:opacity-100 transition-all duration-200 translate-y-1 group-hover/item:translate-y-0 z-40 whitespace-nowrap">
                                <span class="text-[10px] font-mono-code font-bold py-1 px-2.5 rounded-md bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-md">
                                    {{ $m['name'] }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <!-- Duplicate Set for Seamless 100% Loop -->
                    <div class="flex items-center gap-6 sm:gap-8 shrink-0 pr-6 sm:pr-8" aria-hidden="true">
                        @foreach($cfg['mitra'] ?? [] as $m)
                        <div class="partner-card group/item p-3.5 sm:p-4 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 rounded-2xl h-16 sm:h-20 w-36 sm:w-44 flex items-center justify-center shrink-0">
                            <img src="{{ asset($m['image']) }}" alt="{{ $m['name'] }}" title="{{ $m['name'] }}" class="max-h-8 sm:max-h-10 max-w-full object-contain grayscale group-hover/item:grayscale-0 transition-all duration-300 opacity-75 group-hover/item:opacity-100 group-hover/item:scale-105">
                            
                            <!-- Floating Partner Tooltip Badge -->
                            <div class="absolute -top-7 left-1/2 -translate-x-1/2 pointer-events-none opacity-0 group-hover/item:opacity-100 transition-all duration-200 translate-y-1 group-hover/item:translate-y-0 z-40 whitespace-nowrap">
                                <span class="text-[10px] font-mono-code font-bold py-1 px-2.5 rounded-md bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-md">
                                    {{ $m['name'] }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- ── 5. "Life at Wikrama" Fast Facts ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-slate-50 dark:bg-[#060D1A]" id="fakta">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14" data-animate>
                    <span class="google-tag">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        <span>Life at Wikrama &bull; Data &amp; Fakta</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-bold tracking-tight text-slate-900 dark:text-white mt-3 sm:mt-4">
                        Capaian Nyata yang Membanggakan
                    </h2>
                    <p class="mt-3 sm:mt-4 text-sm sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                        Standar mutu dan konsistensi pendidikan vokasi yang diakui secara resmi di tingkat nasional dan internasional.
                    </p>
                </div>

                <!-- 4 Columns Metric Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($cfg['fast_facts'] ?? [] as $idx => $f)
                    <div class="google-card overflow-hidden flex flex-col justify-between h-full group" data-animate data-animate-delay="{{ $idx + 1 }}">
                        <div>
                            <!-- Photo Header -->
                            <div class="aspect-[16/10] w-full relative overflow-hidden bg-slate-950">
                                <img src="{{ asset($f['image']) }}" alt="{{ $f['label'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                <span class="absolute top-3 left-3 bg-white/95 dark:bg-slate-900/95 text-blue-700 dark:text-blue-300 font-mono-code text-[10px] font-bold px-2.5 py-0.5 rounded-full shadow-xs">
                                    {{ $f['tag'] }}
                                </span>
                            </div>

                            <!-- Big Bold Hero Number & Content -->
                            <div class="p-6 pt-4">
                                <div class="text-3xl sm:text-4xl font-bold font-mono-code text-blue-600 dark:text-blue-400 mb-2">
                                    {{ $f['number'] }}
                                </div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2 leading-snug">
                                    {{ $f['label'] }}
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-normal">
                                    {{ $f['desc'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ── 6. 3 Pilar Kejuruan / 4 Jurusan Unggulan ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-white dark:bg-[#071324]" id="jurusan" x-data="{ activePillar: 'all' }" data-mouse-glow>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14" data-animate>
                    <span class="google-tag">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        <span>Program Keahlian Vokasi Terakreditasi</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-bold tracking-tight text-slate-900 dark:text-white mt-3 sm:mt-4">
                        3 Pilar Kompetensi Masa Depan
                    </h2>
                    <p class="mt-3 sm:mt-4 text-sm sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                        Kurikulum link-and-match dengan industri terkemuka dalam bidang <strong class="text-blue-600 dark:text-blue-400">Teknologi Informasi &amp; AI</strong>, <strong class="text-emerald-700 dark:text-emerald-400">Bisnis Digital</strong>, dan <strong class="text-amber-700 dark:text-amber-400">Pariwisata &amp; Hospitaliti</strong>.
                    </p>

                    <!-- Filter Buttons -->
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-1.5 sm:gap-2">
                        <button type="button" 
                                @click="activePillar = 'all'" 
                                :class="activePillar === 'all' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold'"
                                class="px-3.5 sm:px-5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer">
                            Semua Program Keahlian (4)
                        </button>
                        <button type="button" 
                                @click="activePillar = 'tech'" 
                                :class="activePillar === 'tech' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold'"
                                class="px-3.5 sm:px-5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer flex items-center gap-1.5">
                            <span>💻</span>
                            <span>Teknologi (PPLG &bull; TJKT)</span>
                        </button>
                        <button type="button" 
                                @click="activePillar = 'biz'" 
                                :class="activePillar === 'biz' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold'"
                                class="px-3.5 sm:px-5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer flex items-center gap-1.5">
                            <span>📈</span>
                            <span>Bisnis Digital (PMN)</span>
                        </button>
                        <button type="button" 
                                @click="activePillar = 'tour'" 
                                :class="activePillar === 'tour' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold'"
                                class="px-3.5 sm:px-5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer flex items-center gap-1.5">
                            <span>🛎️</span>
                            <span>Perhotelan (HTL)</span>
                        </button>
                    </div>
                </div>

                <!-- 4 Jurusan Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($cfg['jurusans'] ?? [] as $idx => $jur)
                    <div x-show="activePillar === 'all' || activePillar === '{{ $jur['pillar'] }}'"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="google-card overflow-hidden flex flex-col justify-between h-full group"
                         data-animate data-animate-delay="{{ $idx + 1 }}">
                        <div>
                            <!-- Photo Header -->
                            <div class="aspect-[16/10] w-full relative overflow-hidden bg-slate-950">
                                <img src="{{ asset($jur['image']) }}" alt="{{ $jur['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
                                <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-bold shadow-xs {{ $jur['badge_bg'] }}">
                                    {{ $jur['badge'] }}
                                </span>
                                <span class="absolute bottom-3 left-3 px-2.5 py-0.5 rounded-full font-mono-code text-xs font-bold bg-black/70 text-white">
                                    {{ $jur['code'] }}
                                </span>
                            </div>

                            <!-- Body Info -->
                            <div class="p-5 pt-4">
                                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-2 leading-snug group-hover:text-blue-600 transition-colors">
                                    {{ $jur['name'] }}
                                </h3>
                                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
                                    {{ $jur['desc'] }}
                                </p>
                                
                                <!-- Competency Chips -->
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($jur['chips'] ?? [] as $chip)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        {{ $chip }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Footer Career Pathways -->
                        <div class="px-5 py-3.5 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40">
                            <span class="text-[10px] font-bold font-mono-code uppercase tracking-wider text-blue-600 dark:text-blue-400 block mb-0.5">Karier Lulusan:</span>
                            <p class="text-[11px] text-slate-700 dark:text-slate-300 font-medium leading-tight">
                                {{ $jur['careers'] }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ── 6.5. "Dinamika Kehidupan & Prestasi Siswa" (Student Life & Moments) ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-slate-900 text-white relative overflow-hidden" id="dinamika-siswa" x-data="{ studentTab: 'all' }">
            <!-- Background Texture with Student Photo Accent -->
            <div class="absolute inset-0 z-0 opacity-15 pointer-events-none">
                <img src="{{ asset('images/web/activity-outdoor.jpg') }}" alt="Student Life Backdrop" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-slate-950/90"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Section Header -->
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14" data-animate>
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-blue-900/60 text-blue-300 border border-blue-700/60 shadow-xs mb-3">
                        <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                        <span class="font-mono-code font-bold uppercase tracking-wider">{{ $cfg['student_moments']['tag'] ?? 'Dinamika Kehidupan Siswa' }}</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-bold tracking-tight text-white mt-2">
                        {{ $cfg['student_moments']['title'] ?? 'Tumbuh, Berkarya, & Berprestasi Bersama Wikrama' }}
                    </h2>
                    <p class="mt-3 sm:mt-4 text-sm sm:text-lg text-slate-300 leading-relaxed font-normal">
                        {{ $cfg['student_moments']['subtitle'] ?? 'Setiap hari di SMK Wikrama 1 Garut diisi dengan antusiasme belajar nyata, pembiasaan karakter luhur, dan torehan prestasi nasional.' }}
                    </p>

                    <!-- Filter Categories -->
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-1.5 sm:gap-2">
                        <button type="button" 
                                @click="studentTab = 'all'" 
                                :class="studentTab === 'all' ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-500/20' : 'bg-slate-800 text-slate-300 hover:text-white font-semibold'"
                                class="px-3.5 sm:px-5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer">
                            Semua Momen ({{ count($cfg['student_moments']['items'] ?? []) }})
                        </button>
                        <button type="button" 
                                @click="studentTab = 'prestasi'" 
                                :class="studentTab === 'prestasi' ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'bg-slate-800 text-slate-300 hover:text-white font-semibold'"
                                class="px-3.5 sm:px-5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer flex items-center gap-1.5">
                            <span>🏆</span>
                            <span>Prestasi Nasional</span>
                        </button>
                        <button type="button" 
                                @click="studentTab = 'kejuruan'" 
                                :class="studentTab === 'kejuruan' ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-500/20' : 'bg-slate-800 text-slate-300 hover:text-white font-semibold'"
                                class="px-3.5 sm:px-5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer flex items-center gap-1.5">
                            <span>💻</span>
                            <span>Praktik Kejuruan</span>
                        </button>
                        <button type="button" 
                                @click="studentTab = 'karakter'" 
                                :class="studentTab === 'karakter' ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-500/20' : 'bg-slate-800 text-slate-300 hover:text-white font-semibold'"
                                class="px-3.5 sm:px-5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer flex items-center gap-1.5">
                            <span>🕌</span>
                            <span>Karakter & Spiritual</span>
                        </button>
                        <button type="button" 
                                @click="studentTab = 'lingkungan'" 
                                :class="studentTab === 'lingkungan' ? 'bg-cyan-600 text-white font-bold shadow-md shadow-cyan-500/20' : 'bg-slate-800 text-slate-300 hover:text-white font-semibold'"
                                class="px-3.5 sm:px-5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer flex items-center gap-1.5">
                            <span>🌾</span>
                            <span>Cinta Lingkungan</span>
                        </button>
                    </div>
                </div>

                <!-- Masonry / Responsive Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @foreach($cfg['student_moments']['items'] ?? [] as $idx => $m)
                    <div x-show="studentTab === 'all' || studentTab === '{{ $m['category'] }}'"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="group relative rounded-3xl overflow-hidden bg-slate-800/90 border border-white/10 hover:border-blue-400/50 transition-all duration-300 shadow-xl flex flex-col justify-between"
                         data-animate data-animate-delay="{{ ($idx % 3) + 1 }}">
                        
                        <!-- Image Container with Zoom & Click to Preview -->
                        <div class="relative h-60 w-full overflow-hidden bg-slate-950 cursor-pointer"
                             @click="openImageModal('{{ asset($m['image']) }}', '{{ $m['title'] }}', '{{ $m['cat_label'] }}')">
                            <img src="{{ asset($m['image']) }}" 
                                 alt="{{ $m['title'] }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" 
                                 loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/30"></div>
                            
                            <!-- Badges -->
                            <div class="absolute top-3.5 left-3.5 right-3.5 flex items-center justify-between">
                                <span class="px-3 py-1 rounded-full text-[10px] font-mono-code font-bold uppercase tracking-wider bg-white/95 text-slate-900 shadow-md">
                                    {{ $m['badge'] }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-black/60 text-slate-200 backdrop-blur-md">
                                    {{ $m['cat_label'] }}
                                </span>
                            </div>

                            <!-- Hover Overlay -->
                            <div class="absolute inset-0 bg-blue-600/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <span class="text-xs font-bold uppercase tracking-wider bg-black/70 text-white px-3.5 py-1.5 rounded-full border border-white/30 backdrop-blur-xs flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                    <span>Lihat Foto Penuh</span>
                                </span>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-6 flex flex-col justify-between flex-grow">
                            <div>
                                <h3 class="text-base sm:text-lg font-bold text-white leading-snug group-hover:text-blue-300 transition-colors">
                                    {{ $m['title'] }}
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-300 mt-2.5 leading-relaxed font-normal">
                                    {{ $m['desc'] }}
                                </p>
                            </div>
                            <div class="mt-5 pt-4 border-t border-white/10 flex items-center justify-between text-xs">
                                <span class="font-mono-code text-[11px] text-amber-400 font-bold uppercase">Wikrama Student Life</span>
                                <button type="button" 
                                        @click="openImageModal('{{ asset($m['image']) }}', '{{ $m['title'] }}', '{{ $m['cat_label'] }}')"
                                        class="text-blue-400 hover:text-blue-300 font-bold inline-flex items-center gap-1 cursor-pointer">
                                    <span>Perbesar</span>
                                    <span class="group-hover-arrow">&rarr;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ── 7. Leadership & Institutional Vision ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-slate-50 dark:bg-[#060D1A]" id="profil">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
                    
                    <!-- Principal Card -->
                    <div class="lg:col-span-5" data-animate>
                        <div class="google-card p-5 sm:p-8">
                            <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-5">
                                <div class="shrink-0 relative">
                                    <div class="w-28 sm:w-32 h-36 sm:h-40 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-md bg-white mx-auto">
                                        <img src="{{ asset($cfg['principal']['image'] ?? 'images/web/principal-kunedi.jpg') }}" 
                                             alt="{{ $cfg['principal']['name'] ?? 'Kepala Sekolah' }}" 
                                             class="w-full h-full object-cover object-top"
                                             loading="lazy">
                                    </div>
                                    <span class="absolute -bottom-2 -right-2 bg-blue-600 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full shadow-xs">
                                        Kepala Sekolah
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold font-mono-code text-blue-600 dark:text-blue-400 uppercase tracking-wider block">Pimpinan Lembaga</span>
                                    <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white mt-0.5">
                                        {{ $sekolah->kepala_sekolah_nama ?? ($cfg['principal']['name'] ?? 'Kunedi, S.Si.') }}
                                    </h3>
                                    <span class="text-xs text-slate-500 font-medium block mt-0.5">{{ $schoolName }}</span>
                                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-3 font-editorial italic leading-relaxed">
                                        &ldquo;{{ $cfg['principal']['quote'] ?? 'Sekolah kejuruan teladan nasional yang berbudaya lingkungan, berkarakter sesuai profil pelajar Pancasila, berbasis teknologi informasi.' }}&rdquo;
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vision & 4 Pillars -->
                    <div class="lg:col-span-7" data-animate data-animate-delay="1">
                        <div>
                            <span class="google-tag">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                <span>Visi &amp; Komitmen Kelembagaan</span>
                            </span>
                            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight text-slate-900 dark:text-white mt-3 sm:mt-4">
                                {{ $cfg['principal']['vision_title'] ?? 'Teladan Nasional Berbudaya Lingkungan & Teknologi' }}
                            </h2>
                            <p class="text-xs sm:text-sm md:text-base text-slate-600 dark:text-slate-300 mt-3 leading-relaxed font-normal">
                                {{ $cfg['principal']['vision_desc'] ?? 'Menjadi sekolah kejuruan teladan nasional yang berbudaya lingkungan berkelanjutan, berbasis teknologi informasi, berkomitmen mencetak lulusan berkarakter mulia.' }}
                            </p>
                        </div>

                        <div class="mt-8">
                            <span class="text-xs font-bold font-mono-code uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-3">
                                [ 4 PILAR MISI WIKRAMA ]
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($cfg['principal']['pillars'] ?? [] as $pil)
                                <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-blue-300 text-xs font-bold font-mono-code flex items-center justify-center shrink-0">
                                            {{ $pil['num'] }}
                                        </span>
                                        <h4 class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm">{{ $pil['title'] }}</h4>
                                    </div>
                                    <p class="text-[11px] sm:text-xs text-slate-600 dark:text-slate-400 leading-normal pl-8">{{ $pil['desc'] }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── 8. "Community Voices" Testimonials ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-white dark:bg-[#071324]" id="suara-komunitas">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14" data-animate>
                    <span class="google-tag">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        <span>Suara Komunitas &bull; Community Voices</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-bold tracking-tight text-slate-900 dark:text-white mt-3 sm:mt-4">
                        Apa Kata Mereka Tentang Wikrama?
                    </h2>
                    <p class="mt-3 sm:mt-4 text-sm sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                        Kisah nyata dan ulasan mendalam dari siswa, orang tua, pendidik, dan alumni mengenai pengalaman bertumbuh di kampus Wikrama.
                    </p>
                </div>

                <!-- 4 Community Perspectives Cards -->
                @php
                    $dbTestimonials = class_exists(\App\Modules\AdministrasiWeb\Models\WebTestimonial::class)
                        ? \App\Modules\AdministrasiWeb\Models\WebTestimonial::getActiveTestimonials()
                        : collect();
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @if($dbTestimonials->isNotEmpty())
                        @foreach($dbTestimonials as $idx => $v)
                        <div class="google-card p-6 flex flex-col justify-between h-full group" data-animate data-animate-delay="{{ ($idx % 4) + 1 }}">
                            <div>
                                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-mono-code font-bold uppercase tracking-wider bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 mb-3">
                                    {{ $v->category }}
                                </span>
                                
                                @if($v->question)
                                <h4 class="font-bold text-sm text-slate-900 dark:text-white mb-3 leading-snug">
                                    &ldquo;{{ $v->question }}&rdquo;
                                </h4>
                                @endif

                                <blockquote class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-editorial italic">
                                    &ldquo;{{ $v->quote }}&rdquo;
                                </blockquote>
                            </div>

                            <div class="pt-6 mt-4 border-t border-slate-100 dark:border-slate-800 flex items-center gap-3">
                                <img src="{{ $v->imageUrl() }}" alt="{{ $v->name }}" class="w-10 h-10 rounded-full object-cover shrink-0 border border-slate-200 dark:border-slate-700">
                                <div class="min-w-0">
                                    <span class="font-bold text-xs text-slate-900 dark:text-white block truncate">{{ $v->name }}</span>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 block truncate font-medium">{{ $v->role }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        @foreach($cfg['community_voices'] ?? [] as $idx => $v)
                        <div class="google-card p-6 flex flex-col justify-between h-full group" data-animate data-animate-delay="{{ $idx + 1 }}">
                            <div>
                                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-mono-code font-bold uppercase tracking-wider bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 mb-3">
                                    {{ $v['category'] }}
                                </span>
                                
                                <h4 class="font-bold text-sm text-slate-900 dark:text-white mb-3 leading-snug">
                                    &ldquo;{{ $v['question'] }}&rdquo;
                                </h4>

                                <blockquote class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-editorial italic">
                                    &ldquo;{{ $v['quote'] }}&rdquo;
                                </blockquote>
                            </div>

                            <div class="pt-6 mt-4 border-t border-slate-100 dark:border-slate-800 flex items-center gap-3">
                                <img src="{{ asset($v['image']) }}" alt="{{ $v['name'] }}" class="w-10 h-10 rounded-full object-cover shrink-0 border border-slate-200 dark:border-slate-700">
                                <div class="min-w-0">
                                    <span class="font-bold text-xs text-slate-900 dark:text-white block truncate">{{ $v['name'] }}</span>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 block truncate font-medium">{{ $v['role'] }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>

        <!-- ── 9. Interactive Campus Facilities & Labs Gallery ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-slate-50 dark:bg-[#060D1A]" id="fasilitas" x-data="{ facilityTab: 'all' }" data-mouse-glow>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14" data-animate>
                    <span class="google-tag">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        <span>Infrastruktur &amp; Sarana Industri</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-bold tracking-tight text-slate-900 dark:text-white mt-3 sm:mt-4">
                        Fasilitas &amp; Ekosistem Kampus Modern
                    </h2>
                    <p class="mt-3 sm:mt-4 text-sm sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                        Mendukung pembelajaran praktikum intensif dengan laboratorium mutakhir dan lingkungan Adiwiyata yang hijau asri.
                    </p>

                    <!-- Filter Categories -->
                    @php
                        $dbFacilities = class_exists(\App\Modules\AdministrasiWeb\Models\WebFacility::class)
                            ? \App\Modules\AdministrasiWeb\Models\WebFacility::getActiveFacilities()
                            : collect();
                        $facilityCount = $dbFacilities->isNotEmpty() ? $dbFacilities->count() : count($cfg['fasilitas'] ?? []);
                    @endphp
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-1.5 sm:gap-2">
                        <button type="button" 
                                @click="facilityTab = 'all'" 
                                :class="facilityTab === 'all' ? 'bg-blue-600 text-white font-bold' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold'"
                                class="px-3.5 sm:px-5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer shadow-xs">
                            Semua Fasilitas ({{ $facilityCount }})
                        </button>
                        <button type="button" 
                                @click="facilityTab = 'lab'" 
                                :class="facilityTab === 'lab' ? 'bg-blue-600 text-white font-bold' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold'"
                                class="px-3.5 sm:px-5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer shadow-xs flex items-center gap-1.5">
                            <span>💻</span>
                            <span>Lab Komputer &amp; Coding</span>
                        </button>
                        <button type="button" 
                                @click="facilityTab = 'kelas'" 
                                :class="facilityTab === 'kelas' ? 'bg-blue-600 text-white font-bold' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold'"
                                class="px-3.5 sm:px-5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer shadow-xs flex items-center gap-1.5">
                            <span>📱</span>
                            <span>Kelas Digital &amp; TeFa</span>
                        </button>
                        <button type="button" 
                                @click="facilityTab = 'gedung'" 
                                :class="facilityTab === 'gedung' ? 'bg-blue-600 text-white font-bold' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold'"
                                class="px-3.5 sm:px-5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer shadow-xs flex items-center gap-1.5">
                            <span>🏫</span>
                            <span>Gedung &amp; Adiwiyata</span>
                        </button>
                    </div>
                </div>

                <!-- Facilities Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @if($dbFacilities->isNotEmpty())
                        @foreach($dbFacilities as $idx => $f)
                        <div x-show="facilityTab === 'all' || facilityTab === '{{ $f->category }}'"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="google-card overflow-hidden flex flex-col justify-between h-full group"
                             data-animate data-animate-delay="{{ ($idx % 3) + 1 }}">
                            <div>
                                <div class="relative aspect-[16/10] w-full overflow-hidden bg-slate-950 cursor-pointer"
                                     @click="openImageModal('{{ $f->imageUrl() }}', '{{ $f->title }}', '{{ $f->cat_label }}')">
                                    <img src="{{ $f->imageUrl() }}" alt="{{ $f->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                    @if($f->badge)
                                    <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-mono-code font-bold bg-white/95 dark:bg-slate-900/95 text-blue-700 dark:text-blue-300 shadow-xs">
                                        {{ $f->badge }}
                                    </span>
                                    @endif
                                    <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                        <span class="text-xs font-bold uppercase tracking-wider bg-black/60 px-3.5 py-1.5 rounded-full border border-white/30 flex items-center gap-1">
                                            Perbesar Foto &rarr;
                                        </span>
                                    </div>
                                </div>
                                <div class="p-5 pt-4">
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white leading-snug group-hover:text-blue-600 transition-colors">
                                        {{ $f->title }}
                                    </h3>
                                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-2 leading-relaxed">
                                        {{ $f->desc }}
                                    </p>
                                </div>
                            </div>
                            <div class="px-5 py-3.5 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 flex items-center justify-between text-xs font-medium">
                                <span class="text-slate-500 text-[11px] font-mono-code">{{ $f->highlight }}</span>
                                <button type="button" 
                                        @click="openImageModal('{{ $f->imageUrl() }}', '{{ $f->title }}', '{{ $f->cat_label }}')"
                                        class="text-blue-600 dark:text-blue-400 hover:underline font-bold cursor-pointer inline-flex items-center gap-1">
                                    <span>Lihat Pratinjau</span>
                                    <span class="group-hover-arrow">&rarr;</span>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    @else
                        @foreach($cfg['fasilitas'] ?? [] as $idx => $f)
                        <div x-show="facilityTab === 'all' || facilityTab === '{{ $f['category'] }}'"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="google-card overflow-hidden flex flex-col justify-between h-full group"
                             data-animate data-animate-delay="{{ ($idx % 3) + 1 }}">
                            <div>
                                <div class="relative aspect-[16/10] w-full overflow-hidden bg-slate-950 cursor-pointer"
                                     @click="openImageModal('{{ asset($f['image']) }}', '{{ $f['title'] }}', '{{ $f['cat_label'] }}')">
                                    <img src="{{ asset($f['image']) }}" alt="{{ $f['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                    <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-mono-code font-bold bg-white/95 dark:bg-slate-900/95 text-blue-700 dark:text-blue-300 shadow-xs">
                                        {{ $f['badge'] }}
                                    </span>
                                    <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                        <span class="text-xs font-bold uppercase tracking-wider bg-black/60 px-3.5 py-1.5 rounded-full border border-white/30 flex items-center gap-1">
                                            Perbesar Foto &rarr;
                                        </span>
                                    </div>
                                </div>
                                <div class="p-5 pt-4">
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white leading-snug group-hover:text-blue-600 transition-colors">
                                        {{ $f['title'] }}
                                    </h3>
                                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-2 leading-relaxed">
                                        {{ $f['desc'] }}
                                    </p>
                                </div>
                            </div>
                            <div class="px-5 py-3.5 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 flex items-center justify-between text-xs font-medium">
                                <span class="text-slate-500 text-[11px] font-mono-code">{{ $f['highlight'] }}</span>
                                <button type="button" 
                                        @click="openImageModal('{{ asset($f['image']) }}', '{{ $f['title'] }}', '{{ $f['cat_label'] }}')"
                                        class="text-blue-600 dark:text-blue-400 hover:underline font-bold cursor-pointer inline-flex items-center gap-1">
                                    <span>Lihat Pratinjau</span>
                                    <span class="group-hover-arrow">&rarr;</span>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>

        <!-- ── 10. Latest News & Achievements ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-white dark:bg-[#071324]" id="berita">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 sm:mb-14 gap-4" data-animate>
                    <div>
                        <span class="google-tag">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            <span>Kabar &amp; Prestasi Terkini</span>
                        </span>
                        <h2 class="text-2xl sm:text-4xl md:text-5xl font-bold tracking-tight text-slate-900 dark:text-white mt-3 sm:mt-4">
                            Berita Terbaru SMK Wikrama 1 Garut
                        </h2>
                        <p class="mt-3 sm:mt-4 text-sm sm:text-lg text-slate-600 dark:text-slate-300 max-w-2xl">
                            Dokumentasi kegiatan, studi tiru nasional, capaian medali kompetisi, dan kiprah alumni di dunia industri.
                        </p>
                    </div>
                    <a href="{{ route('web.berita.index') }}" class="btn-google-outline text-xs sm:text-sm !py-2.5 !px-5 shrink-0 w-full sm:w-auto text-center justify-center">
                        <span>Seluruh Arsip Berita</span>
                        <span class="group-hover-arrow">&rarr;</span>
                    </a>
                </div>

                @php
                    $dbArticles = class_exists(\App\Modules\AdministrasiWeb\Models\WebArticle::class)
                        ? \App\Modules\AdministrasiWeb\Models\WebArticle::with('category')->published()->latest('published_at')->limit(4)->get()
                        : collect();
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @if ($dbArticles->isNotEmpty())
                        @foreach ($dbArticles as $idx => $art)
                        <article class="google-card overflow-hidden flex flex-col justify-between h-full group" data-animate data-animate-delay="{{ $idx + 1 }}">
                            <div>
                                <div class="aspect-[16/10] w-full relative overflow-hidden bg-slate-950">
                                    <img src="{{ $art->gambarSampulUrl() }}" alt="{{ $art->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                    <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-mono-code font-bold bg-white/95 dark:bg-slate-900/95 text-blue-700 dark:text-blue-300">
                                        {{ $art->category?->nama ?? 'Berita' }}
                                    </span>
                                </div>
                                <div class="p-5 pt-4">
                                    <span class="text-[11px] font-mono-code text-slate-400 font-medium block mb-1">
                                        {{ $art->published_at ? $art->published_at->format('d M Y') : date('d M Y') }}
                                    </span>
                                    <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-snug hover:text-blue-600 transition-colors line-clamp-2">
                                        <a href="{{ url('/berita/' . $art->slug) }}">
                                            {{ $art->judul }}
                                        </a>
                                    </h3>
                                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-2 leading-relaxed line-clamp-3">
                                        {{ $art->ringkasan ?: Str::limit(strip_tags($art->konten), 130) }}
                                    </p>
                                </div>
                            </div>
                            <div class="px-5 py-3.5 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40">
                                <a href="{{ url('/berita/' . $art->slug) }}" class="text-xs text-blue-600 dark:text-blue-400 font-bold hover:underline inline-flex items-center gap-1">
                                    <span>Baca Artikel Lengkap</span>
                                    <span class="group-hover-arrow">&rarr;</span>
                                </a>
                            </div>
                        </article>
                        @endforeach
                    @else
                        @foreach($cfg['berita'] ?? [] as $idx => $br)
                        <article class="google-card overflow-hidden flex flex-col justify-between h-full group" data-animate data-animate-delay="{{ $idx + 1 }}">
                            <div>
                                <div class="aspect-[16/10] w-full relative overflow-hidden bg-slate-950">
                                    <img src="{{ asset($br['image']) }}" alt="{{ $br['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                    <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-mono-code font-bold bg-white/95 dark:bg-slate-900/95 text-blue-700 dark:text-blue-300">
                                        {{ $br['badge'] }}
                                    </span>
                                </div>
                                <div class="p-5 pt-4">
                                    <span class="text-[11px] font-mono-code text-slate-400 font-medium block mb-1">{{ $br['date'] }}</span>
                                    <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-snug hover:text-blue-600 transition-colors">
                                        <a href="{{ $br['url'] }}" target="_blank" rel="noopener">
                                            {{ $br['title'] }}
                                        </a>
                                    </h3>
                                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-2 leading-relaxed line-clamp-3">
                                        {{ $br['desc'] }}
                                    </p>
                                </div>
                            </div>
                            <div class="px-5 py-3.5 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40">
                                <a href="{{ $br['url'] }}" target="_blank" rel="noopener" class="text-xs text-blue-600 dark:text-blue-400 font-bold hover:underline inline-flex items-center gap-1">
                                    <span>Baca Artikel Lengkap</span>
                                    <span class="group-hover-arrow">&rarr;</span>
                                </a>
                            </div>
                        </article>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>

        <!-- ── 10.B Kalender Pendidikan & Agenda Kegiatan Terdekat ── -->
        <section class="py-12 sm:py-16 lg:py-20 bg-slate-50 dark:bg-[#060D1A] border-t border-slate-200/80 dark:border-slate-800" id="agenda-sekolah">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 sm:mb-10 gap-4" data-animate>
                    <div>
                        <span class="google-tag">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Agenda &amp; Kalender Pendidikan</span>
                        </span>
                        <h2 class="text-2xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white mt-3 sm:mt-4">
                            Kegiatan Sekolah Terdekat
                        </h2>
                        <p class="mt-2 sm:mt-3 text-sm sm:text-base text-slate-600 dark:text-slate-300 max-w-2xl">
                            Jadwal asesmen, kegiatan kurikulum, agenda kesiswaan, dan hari libur resmi yang telah disahkan.
                        </p>
                    </div>
                    @auth
                    <a href="{{ route('kalender.index') }}" class="btn-google-outline text-xs sm:text-sm !py-2.5 !px-5 shrink-0 w-full sm:w-auto text-center justify-center">
                        <span>Buka Kalender Penuh</span>
                        <span class="group-hover-arrow">&rarr;</span>
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="btn-google-outline text-xs sm:text-sm !py-2.5 !px-5 shrink-0 w-full sm:w-auto text-center justify-center">
                        <span>Masuk ke SIRAGA</span>
                        <span class="group-hover-arrow">&rarr;</span>
                    </a>
                    @endauth
                </div>

                @php
                    $upcomingAgendas = class_exists(\App\Modules\KalenderPendidikan\Models\KalenderAgenda::class)
                        ? \App\Modules\KalenderPendidikan\Models\KalenderAgenda::with('kategori')
                            ->where('status', 'publik')
                            ->where('tgl_selesai', '>=', now()->toDateString())
                            ->orderBy('tgl_mulai')
                            ->limit(4)
                            ->get()
                        : collect();
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @forelse($upcomingAgendas as $idx => $agenda)
                    <div class="google-card p-5 flex flex-col justify-between" data-animate data-animate-delay="{{ $idx + 1 }}">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-mono-code font-bold text-white shadow-xs" style="background-color: {{ $agenda->display_warna }}">
                                    {{ $agenda->kategori?->nama ?? 'Kegiatan' }}
                                </span>
                                @if($agenda->is_final)
                                    <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded border border-emerald-300 dark:border-emerald-800">
                                        ✓ FINAL
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-baseline gap-2 mb-2">
                                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $agenda->tgl_mulai->format('d') }}</span>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $agenda->tgl_mulai->translatedFormat('M Y') }}</span>
                            </div>

                            <h3 class="text-base font-bold text-slate-900 dark:text-white leading-snug line-clamp-2">
                                {{ $agenda->judul }}
                            </h3>

                            @if($agenda->deskripsi)
                            <p class="mt-2 text-xs text-slate-600 dark:text-slate-400 line-clamp-2">
                                {{ $agenda->deskripsi }}
                            </p>
                            @endif
                        </div>

                        <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-between">
                            <span>{{ $agenda->is_all_day ? 'Sepanjang Hari' : substr($agenda->jam_mulai,0,5).' WIB' }}</span>
                            <span class="truncate max-w-[120px]">{{ $agenda->lokasi ?: 'Sekolah' }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-12 text-center text-slate-500 text-sm google-card">
                        Belum ada agenda kegiatan publik terdekat yang dipublikasikan.
                    </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- ── 11. National Education Report Card (Rapor Mutu Kemendikdasmen RI) ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-white dark:bg-[#071324] border-t border-slate-200/80 dark:border-slate-800" id="rapor-mutu">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
                    
                    <!-- Progress Bar Metrics -->
                    <div class="lg:col-span-6" data-animate>
                        <span class="google-tag">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            <span>{{ $cfg['rapor_mutu']['tag'] ?? 'Rapor Pendidikan Resmi Kemendikdasmen' }}</span>
                        </span>
                        <h2 class="text-2xl sm:text-4xl md:text-5xl font-bold tracking-tight text-slate-900 dark:text-white mt-3 sm:mt-4">
                            {{ $cfg['rapor_mutu']['title'] ?? 'Peringkat 20% Teratas Nasional' }}
                        </h2>
                        <p class="text-xs text-slate-500 font-medium font-mono-code mt-2 mb-6 sm:mb-8">
                            *{{ $cfg['rapor_mutu']['source'] ?? 'Validasi Data Resmi: Kemendikdasmen RI (raportpendidikan.kemdikbud.go.id)' }}
                        </p>

                        <!-- Bars with Smooth Gradients -->
                        <div class="space-y-5 sm:space-y-6">
                            @foreach($cfg['rapor_mutu']['metrics'] ?? [] as $m)
                            <div>
                                <div class="flex items-center justify-between gap-3 text-xs sm:text-sm font-bold text-slate-900 dark:text-white mb-2">
                                    <span class="leading-tight">{{ $m['label'] }}</span>
                                    <span class="text-blue-600 dark:text-blue-400 font-mono-code font-bold shrink-0 text-xs sm:text-sm">{{ $m['badge'] }}</span>
                                </div>
                                <div class="w-full bg-slate-200 dark:bg-slate-700/80 h-3.5 rounded-full overflow-hidden p-0.5">
                                    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-pink-500 h-full rounded-full transition-all duration-1000" style="width: {{ $m['value'] }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Awards & Accreditations -->
                    <div class="lg:col-span-6" data-animate data-animate-delay="1">
                        <div class="google-card p-6 sm:p-8">
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-5 flex items-center gap-2">
                                <span class="text-amber-500 font-black text-xl">★</span> 
                                <span>Pengakuan Mutu &amp; Akreditasi Lembaga</span>
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($cfg['rapor_mutu']['awards'] ?? [] as $aw)
                                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-mono-code font-bold bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300 mb-1.5">
                                        {{ $aw['badge'] }}
                                    </span>
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ $aw['title'] }}</h4>
                                    <p class="text-[11px] text-slate-600 dark:text-slate-400 font-normal mt-0.5">{{ $aw['desc'] }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── 12. Filosofi Pilar 1: Tiada Pendidikan Bermutu, Tanpa Pendidikan Karakter ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-slate-50 dark:bg-[#060D1A] border-t border-slate-200/80 dark:border-slate-800" id="pilar-karakter">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-10 sm:mb-12" data-animate>
                    <div class="max-w-3xl">
                        <span class="google-tag">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            <span>{{ $cfg['pilar_karakter']['tag'] ?? 'Pilar Mutu & Karakter' }}</span>
                        </span>
                        <h2 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white mt-3">
                            {{ $cfg['pilar_karakter']['title'] ?? 'Tiada pendidikan bermutu, tanpa pendidikan karakter' }}
                        </h2>
                        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 mt-3">
                            {{ $cfg['pilar_karakter']['desc'] ?? 'Delapan pondasi keunggulan terintegrasi yang membentuk ekosistem pendidikan vokasi berkualitas unggul di SMK Wikrama 1 Garut.' }}
                        </p>
                    </div>
                    <div class="shrink-0">
                        <a href="{{ $cfg['pilar_karakter']['btn_url'] ?? 'https://spmb.smkwikrama1garut.sch.id/' }}" 
                           target="_blank" rel="noopener" 
                           class="btn-google-primary text-xs sm:text-sm !py-2.5 sm:!py-3 !px-5 sm:!px-6 shadow-md hover:shadow-xl inline-flex items-center gap-2 w-full sm:w-auto justify-center">
                            <span>{{ $cfg['pilar_karakter']['btn_text'] ?? 'Daftar Sekarang' }}</span>
                            <span class="text-base">&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- 8 Cards Grid (4 columns desktop, 2 tablet, 1 mobile) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($cfg['pilar_karakter']['items'] ?? [] as $idx => $item)
                    <div class="google-card overflow-hidden group flex flex-col justify-between" data-animate data-animate-delay="{{ ($idx % 4) + 1 }}">
                        <div>
                            <div class="aspect-[16/10] w-full overflow-hidden relative bg-slate-100 dark:bg-slate-900">
                                <img src="{{ asset($item['image'] ?? 'images/web/campus-environment.jpg') }}" 
                                     alt="{{ $item['title'] ?? 'Pilar Wikrama' }}" 
                                     class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent opacity-80 group-hover:opacity-60 transition-opacity"></div>
                                <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[10px] font-mono-code font-bold bg-blue-950/80 text-blue-300 border border-blue-700/50 backdrop-blur-md">
                                    {{ $item['badge'] ?? 'Keunggulan' }}
                                </span>
                                <h3 class="absolute bottom-3 left-3 right-3 text-base sm:text-lg font-bold text-white drop-shadow">
                                    {{ $item['title'] }}
                                </h3>
                            </div>
                            <div class="p-4 sm:p-5">
                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                                    {{ $item['desc'] }}
                                </p>
                            </div>
                        </div>
                        <div class="px-4 pb-4 sm:px-5 sm:pb-5 pt-0">
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-blue-600 dark:text-blue-400 group-hover:translate-x-1 transition-transform">
                                <span>Nilai Utama Wikrama</span>
                                <span>&rarr;</span>
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ── 13. Filosofi Pilar 2: Tiada Pendidikan Karakter, Tanpa Budaya Sekolah (Mitra Perguruan Tinggi) ── -->
        <section class="py-12 sm:py-16 lg:py-20 bg-white dark:bg-[#071324] border-t border-slate-200/80 dark:border-slate-800 overflow-hidden" id="pilar-budaya">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 sm:mb-8 text-center" data-animate>
                <span class="google-tag">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <span>{{ $cfg['pilar_budaya']['tag'] ?? 'Jejaring Perguruan Tinggi' }}</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white mt-3">
                    {{ $cfg['pilar_budaya']['title'] ?? 'Tiada pendidikan karakter, tanpa budaya sekolah' }}
                </h2>
                <p class="max-w-2xl mx-auto text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-2">
                    {{ $cfg['pilar_budaya']['desc'] ?? 'Budaya belajar unggul yang mengantarkan alumni menembus berbagai perguruan tinggi terbaik nasional dan internasional.' }}
                </p>
            </div>

            <!-- Logo Marquee Perguruan Tinggi -->
            <div class="partner-marquee-container relative w-full overflow-hidden py-4">
                <div class="partner-marquee-track flex items-center gap-4 sm:gap-6">
                    @for($pass = 0; $pass < 2; $pass++)
                        @foreach($cfg['pilar_budaya']['universities'] ?? [] as $uni)
                        <div class="partner-card flex items-center gap-2.5 sm:gap-3 py-2.5 sm:py-3 px-3.5 sm:px-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm shrink-0">
                            <img src="{{ asset($uni['image']) }}" alt="{{ $uni['name'] }}" class="w-7 h-7 sm:w-8 sm:h-8 object-contain shrink-0">
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white whitespace-nowrap">{{ $uni['name'] }}</h4>
                                <span class="text-[10px] text-blue-600 dark:text-blue-400 font-medium block whitespace-nowrap">{{ $uni['status'] ?? 'Mitra Akademik' }}</span>
                            </div>
                        </div>
                        @endforeach
                    @endfor
                </div>
            </div>
            
            <div class="text-center mt-6" data-animate>
                <a href="{{ $cfg['pilar_budaya']['btn_url'] ?? '#filosofi' }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                    <span>{{ $cfg['pilar_budaya']['btn_text'] ?? 'Pelajari Budaya Sekolah Lebih Lanjut' }}</span>
                </a>
            </div>
        </section>

        <!-- ── 14. Filosofi Pilar 3: Tiada Masyarakat Pembelajar Sekolah, Tanpa Pemimpin Perubahan Sekolah ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-slate-50 dark:bg-[#060D1A] border-t border-slate-200/80 dark:border-slate-800" id="pilar-pemimpin">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-4xl mx-auto mb-10 sm:mb-12" data-animate>
                    <span class="google-tag">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        <span>{{ $cfg['pilar_pemimpin']['tag'] ?? 'SMK Wikrama 1 Garut' }}</span>
                    </span>
                    <h2 class="text-xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white mt-3 leading-snug">
                        {{ $cfg['pilar_pemimpin']['title'] ?? 'Tiada masyarakat pembelajar sekolah, tanpa pemimpin perubahan sekolah, dan tanpa kesungguhan warga sekolah' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-3">
                        {{ $cfg['pilar_pemimpin']['desc'] ?? 'Kepemimpinan visioner dan komitmen total seluruh pendidik, tenaga kependidikan, serta santri dalam mewujudkan sekolah unggul berdaya saing global.' }}
                    </p>
                </div>

                <!-- 4 Feature Cards (Matrikulasi, Lulusan BMW, Moving Class, Belajar Bertiga) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($cfg['pilar_pemimpin']['items'] ?? [] as $idx => $item)
                    <div class="google-card overflow-hidden group flex flex-col justify-between" data-animate data-animate-delay="{{ $idx + 1 }}">
                        <div>
                            <div class="aspect-[16/10] w-full overflow-hidden relative bg-slate-100 dark:bg-slate-900">
                                <img src="{{ asset($item['image'] ?? 'images/web/campus-environment.jpg') }}" 
                                     alt="{{ $item['title'] }}" 
                                     class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent opacity-80"></div>
                                <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[10px] font-mono-code font-bold bg-blue-950/80 text-blue-300 border border-blue-700/50 backdrop-blur-md">
                                    {{ $item['badge'] ?? 'Inovasi Belajar' }}
                                </span>
                            </div>
                            <div class="p-4 sm:p-5">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1.5">
                                    {{ $item['title'] }}
                                </h3>
                                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                                    {{ $item['desc'] }}
                                </p>
                            </div>
                        </div>
                        <div class="px-4 pb-4 sm:px-5 sm:pb-5 pt-0">
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 dark:text-blue-400 group-hover:translate-x-1 transition-transform">
                                <span>Budaya Unggulan</span>
                                <span>&rarr;</span>
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ── 15. Sekolah Teladan Nasional: Partner, Prestasi & Rekognisi Resmi ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-white dark:bg-[#071324] border-t border-slate-200/80 dark:border-slate-800" id="sekolah-teladan">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-12" data-animate>
                    <span class="google-tag">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        <span>{{ $cfg['sekolah_teladan']['tag'] ?? 'Partner & Rekognisi Nasional' }}</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white mt-3">
                        {{ $cfg['sekolah_teladan']['title'] ?? 'Sekolah Teladan Nasional' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-2">
                        {{ $cfg['sekolah_teladan']['desc'] ?? 'Bukti nyata komitmen mutu, akreditasi paripurna BAN-PDM, dan rekognisi resmi dari kementerian serta lembaga internasional.' }}
                    </p>
                </div>

                <!-- 9 Awards & Partnerships Grid (3x3 on desktop) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-6">
                    @foreach($cfg['sekolah_teladan']['awards'] ?? [] as $idx => $aw)
                    <div class="google-card p-4 sm:p-6 flex items-start gap-3.5 sm:gap-4 hover:border-blue-500/40 transition-all group" data-animate data-animate-delay="{{ ($idx % 3) + 1 }}">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80 flex items-center justify-center p-2 shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <img src="{{ asset($aw['image']) }}" alt="{{ $aw['title'] }}" class="max-w-full max-h-full object-contain">
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-mono-code font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 block mb-1">
                                {{ $aw['org'] ?? 'Kementerian & Mitra' }}
                            </span>
                            <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-snug">
                                {{ $aw['title'] }}
                            </h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                                {{ $aw['sub'] }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ── Dynamic Module Landing Blocks (Database Module Registry) ── -->
        @if(!empty($landingBlocks) && count($landingBlocks) > 0)
        <section class="py-10 sm:py-12 bg-slate-50 dark:bg-[#060D1A] border-t border-slate-200/80 dark:border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-6">
                    <span class="google-tag">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        <span>Layanan Terintegrasi Realtime</span>
                    </span>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-2">Transparansi Layanan Sistem {{ $appName }}</h2>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($landingBlocks as $block)
                        <div class="google-card p-4 sm:p-5 text-center">
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block truncate">{{ $block['label'] ?? '' }}</span>
                            <p class="text-2xl sm:text-3xl font-mono-code font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $block['value'] ?? '-' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- ── 12. Grand Institutional CTA Banner ── -->
        <section class="py-14 sm:py-20 lg:py-28 bg-slate-950 text-white relative overflow-hidden">
            <!-- Background Student Activity Image with Dark Overlay (above image, below text) -->
            <div class="absolute inset-0 z-0 pointer-events-none">
                <img src="{{ asset('images/web/activity-learning.jpg') }}" alt="Kegiatan Siswa Wikrama Garut" class="w-full h-full object-cover object-center filter brightness-[0.20] contrast-125">
                <div class="absolute inset-0 bg-slate-950/85 backdrop-blur-[1px]"></div>
            </div>
            <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-blue-600/20 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-purple-600/15 blur-3xl pointer-events-none"></div>

            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10" data-animate>
                <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold font-mono-code bg-white/10 text-amber-300 border border-white/20 mb-5">
                    [ {{ $cfg['cta']['tag'] ?? 'MULAI PERJALANANMU' }} ]
                </span>
                <h2 class="text-2xl sm:text-4xl md:text-5xl font-bold tracking-[-0.025em] leading-tight">
                    {{ $cfg['cta']['title'] ?? 'Siap Menjadi Ahli Teknologi, Pebisnis Digital & Praktisi Hospitaliti?' }}
                </h2>
                <p class="mt-4 sm:mt-5 text-sm sm:text-base md:text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed font-normal">
                    {{ $cfg['cta']['desc'] ?? 'Daftarkan putra-putri Anda di SMK Wikrama 1 Garut. Pembinaan akhlak berkarakter, kurikulum industri 4.0 & 5.0, serta jaminan kompetensi kerja dan kuliah.' }}
                </p>
                <div class="mt-10 flex flex-col sm:flex-row flex-wrap justify-center gap-4">
                    <a href="{{ $cfg['cta']['btn_spmb_url'] ?? 'https://spmb.smkwikrama1garut.sch.id/' }}" target="_blank" rel="noopener" class="btn-google-gold text-sm sm:text-base shadow-xl w-full sm:w-auto">
                        <span>{{ $cfg['cta']['btn_spmb'] ?? 'Daftar SPMB Online Sekarang' }}</span>
                        <span>&rarr;</span>
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-google-outline !text-white !border-white/30 hover:!border-white hover:!bg-white/10 text-sm sm:text-base w-full sm:w-auto">
                            <span>Buka Dashboard {{ $appName }}</span>
                            <span class="group-hover-arrow">&rarr;</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-google-outline !text-white !border-white/30 hover:!border-white hover:!bg-white/10 text-sm sm:text-base w-full sm:w-auto">
                            <span>Masuk Portal {{ $appName }}</span>
                            <span class="group-hover-arrow">&rarr;</span>
                        </a>
                    @endauth
                </div>
            </div>
        </section>
    </main>

    <!-- ── Lightbox Modals ── -->
    <!-- Lightbox SPMB Flyer Modal -->
    <div x-show="flyerModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 p-3 sm:p-4 backdrop-blur-sm"
         @keydown.escape.window="flyerModal = false">
        <div class="relative max-w-2xl w-full max-h-[95vh] flex flex-col items-center bg-slate-900/90 p-3 sm:p-4 rounded-3xl border border-white/20" @click.outside="flyerModal = false">
            <div class="w-full flex items-center justify-between pb-2 mb-2 border-b border-white/10">
                <span class="text-xs font-mono-code font-bold text-amber-400">Brosur &amp; Flyer SPMB</span>
                <button type="button" 
                        @click="flyerModal = false" 
                        class="text-white hover:text-amber-400 text-sm font-bold flex items-center gap-1 cursor-pointer py-1 px-2.5 rounded-lg bg-white/10">
                    <span>Tutup</span> &times;
                </button>
            </div>
            <img src="{{ asset('images/landing/flyer-spmb-2027.jpg') }}" 
                 alt="Flyer SPMB Full" 
                 class="rounded-2xl max-h-[70vh] w-auto max-w-full object-contain shadow-2xl">
            <div class="mt-3 flex w-full justify-center">
                <a href="{{ $cfg['branding']['spmb_url'] ?? 'https://spmb.smkwikrama1garut.sch.id/' }}" target="_blank" rel="noopener" class="btn-google-gold text-xs sm:text-sm w-full sm:w-auto text-center">
                    Buka Formulir Pendaftaran Online &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Universal Image Lightbox Modal -->
    <div x-show="imageModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 p-4 backdrop-blur-md"
         @keydown.escape.window="imageModal = false">
        <div class="relative max-w-4xl w-full max-h-[95vh] flex flex-col items-center bg-white dark:bg-slate-900 rounded-3xl overflow-hidden shadow-2xl p-5" 
             @click.outside="imageModal = false">
            <div class="w-full flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800 mb-3">
                <div class="min-w-0 pr-3">
                    <span class="text-[10px] sm:text-xs font-bold font-mono-code uppercase tracking-wider text-blue-600 dark:text-blue-400 block" x-text="modalImageCategory"></span>
                    <h4 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white truncate" x-text="modalImageTitle"></h4>
                </div>
                <button type="button" 
                        @click="imageModal = false" 
                        class="p-1 rounded-full text-slate-500 hover:text-red-500 transition-colors shrink-0"
                        aria-label="Tutup Pratinjau">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="relative max-h-[70vh] w-full flex items-center justify-center overflow-hidden rounded-2xl bg-slate-950">
                <img :src="modalImageSrc" :alt="modalImageTitle" class="max-h-[66vh] w-auto max-w-full object-contain">
            </div>
            <div class="w-full pt-3 flex items-center justify-between text-xs font-semibold text-slate-500">
                <span>Dokumentasi Resmi &bull; {{ $schoolName }}</span>
                <a :href="modalImageSrc" target="_blank" rel="noopener" class="text-blue-600 dark:text-blue-400 hover:underline font-bold">
                    Buka Ukuran Asli &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- ── Prestige Slate & Navy Footer ── -->
    <footer class="bg-slate-950 text-slate-300 border-t border-white/10" id="kontak">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-20">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10">
                
                <!-- School Info & Crest -->
                <div class="sm:col-span-2 lg:col-span-5 space-y-4">
                    <div class="flex items-center space-x-3.5">
                        <div class="p-1.5 rounded-2xl bg-white/10 border border-white/15">
                            <img src="{{ $logoUrl }}" alt="Logo {{ $schoolName }}" class="h-10 sm:h-12 w-auto object-contain">
                        </div>
                        <div>
                            <span class="text-base sm:text-lg font-bold text-white block leading-tight">{{ $schoolName }}</span>
                            <span class="text-xs text-amber-400 font-mono-code font-medium">{{ $cfg['branding']['badge'] ?? 'Islamic Boarding School • SMK Pusat Keunggulan' }}</span>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-sm font-editorial italic">
                        &ldquo;{{ $cfg['branding']['motto_primary'] ?? 'Ilmu yang Amaliah, Amal yang Ilmiah, Akhlakul Karimah' }}&rdquo;
                    </p>
                    <p class="text-xs text-slate-400 leading-relaxed max-w-sm">
                        Sekolah Menengah Kejuruan Pusat Keunggulan terakreditasi A Unggul di Kabupaten Garut, berfokus pada teknologi mutakhir, bisnis digital, dan hospitaliti pariwisata berbudaya lingkungan.
                    </p>
                    <div class="text-xs text-slate-400">
                        Sistem Informasi Sekolah Terpadu: <span class="font-bold text-white">{{ $appName }}</span> ({{ $sekolah->kepanjangan_aplikasi ?? 'Sistem Informasi Wikrama Garut' }})
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="lg:col-span-3 space-y-3">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider font-mono-code text-amber-400">[ NAVIGASI UTAMA ]</h4>
                    <ul class="space-y-2 text-xs font-medium">
                        @if($footerMenus->isNotEmpty())
                            @foreach($footerMenus as $fm)
                                <li>
                                    <a href="{{ $fm->url }}" target="{{ $fm->target }}" class="hover:text-white transition-colors">
                                        {{ $fm->label }}
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li><a href="#beranda" class="hover:text-white transition-colors">Beranda Utama</a></li>
                            <li><a href="#filosofi" class="hover:text-white transition-colors">Filosofi &amp; Visi Misi</a></li>
                            <li><a href="#mengenal" class="hover:text-white transition-colors">Mengenal Lebih Dekat</a></li>
                            <li><a href="#jurusan" class="hover:text-white transition-colors">3 Pilar Kompetensi Keahlian</a></li>
                            <li><a href="#dinamika-siswa" class="hover:text-white transition-colors">Dinamika Siswa &amp; Prestasi</a></li>
                            <li><a href="#fakta" class="hover:text-white transition-colors">Kehidupan Siswa (Fast Facts)</a></li>
                            <li><a href="#suara-komunitas" class="hover:text-white transition-colors">Suara Komunitas (Testimoni)</a></li>
                            <li><a href="#fasilitas" class="hover:text-white transition-colors">Fasilitas &amp; Lab Praktik</a></li>
                            <li><a href="#berita" class="hover:text-white transition-colors">Berita &amp; Prestasi</a></li>
                            <li><a href="#rapor-mutu" class="hover:text-white transition-colors">Rapor Mutu Kemendikdasmen</a></li>
                            <li><a href="#pilar-karakter" class="hover:text-white transition-colors">Pilar Karakter &amp; Mutu</a></li>
                            <li><a href="#sekolah-teladan" class="hover:text-white transition-colors">Sekolah Teladan Nasional</a></li>
                            <li><a href="{{ $cfg['branding']['brochure_url'] ?? 'http://brosur.smkwikrama1garut.sch.id/' }}" target="_blank" rel="noopener" class="text-amber-400 hover:underline">Unduh Brosur Resmi &rarr;</a></li>
                        @endif
                    </ul>
                </div>

                <!-- Contact & Social -->
                <div class="sm:col-span-2 lg:col-span-4 space-y-3">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider font-mono-code text-amber-400">[ KAMPUS &amp; KONTAK ]</h4>
                    <div class="space-y-3 text-xs text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <a href="{{ $cfg['contact']['maps_url'] ?? 'https://maps.app.goo.gl/MXd5JUXK8SPAcAhn9' }}" target="_blank" rel="noopener" class="leading-relaxed hover:text-white transition-colors">
                                {{ $cfg['contact']['address'] ?? 'Jl. Otto Iskandardinata Kp. Tanjung Ds. Pasawahan Kec. Tarogong Kaler, Kabupaten Garut, Jawa Barat 44151' }} <span class="text-amber-400 underline font-bold">(Lihat di Google Maps &rarr;)</span>
                            </a>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <a href="{{ $cfg['contact']['phone_url'] ?? 'tel:081323314430' }}" class="hover:text-white transition-colors">{{ $cfg['contact']['phone'] ?? '0813-2331-4430 / 0897-1064-617' }}</a>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <a href="mailto:{{ $cfg['contact']['email'] ?? 'info@smkwikrama1garut.sch.id' }}" class="hover:text-white transition-colors truncate">{{ $cfg['contact']['email'] ?? 'info@smkwikrama1garut.sch.id' }}</a>
                        </div>

                        <!-- Social Media Channel Icons -->
                        <div class="pt-3 flex items-center gap-2.5">
                            @if(!empty($cfg['contact']['instagram']))
                            <a href="{{ $cfg['contact']['instagram'] }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white hover:text-amber-400 transition-all" title="Instagram Resmi">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            @endif
                            @if(!empty($cfg['contact']['facebook']))
                            <a href="{{ $cfg['contact']['facebook'] }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white hover:text-blue-400 transition-all" title="Facebook Resmi">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            @endif
                            @if(!empty($cfg['contact']['youtube']))
                            <a href="{{ $cfg['contact']['youtube'] }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white hover:text-red-500 transition-all" title="YouTube Channel Resmi">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            </a>
                            @endif
                            @if(!empty($cfg['contact']['tiktok']))
                            <a href="{{ $cfg['contact']['tiktok'] }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white hover:text-cyan-400 transition-all" title="TikTok Resmi">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 2.89 3.5 2.75 1.34-.03 2.56-.83 3.09-2.05.29-.62.4-1.31.39-2 .03-4.81.01-9.62.02-14.43z"/></svg>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-3 text-center sm:text-left">
                <p>&copy; {{ date('Y') }} {{ $schoolName }}. Seluruh hak cipta dilindungi.</p>
                <p>{{ $appName }} &bull; Sistem Informasi Terintegrasi SMK Wikrama 1 Garut</p>
            </div>
        </div>
    </footer>

    <!-- ── Interactive Google AI Style Animations Script ── -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // ── 1. Google AI Dev Scroll-Reveal with Easing Curve ──
        const animEls = document.querySelectorAll('[data-animate]');
        if (animEls.length && 'IntersectionObserver' in window) {
            const obs = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.classList.add('is-visible');
                        obs.unobserve(e.target);
                    }
                });
            }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
            animEls.forEach(el => obs.observe(el));
        } else {
            animEls.forEach(el => el.classList.add('is-visible'));
        }

        // ── 2. Atmospheric Spotlight Glow with Smooth Lerp ──
        const mouseGlow = document.createElement('div');
        mouseGlow.className = 'mouse-glow';
        document.body.appendChild(mouseGlow);

        const mouseTrackSections = document.querySelectorAll('[data-mouse-glow]');
        let targetX = 0, targetY = 0, currentX = 0, currentY = 0;
        let isHoveringGlowSection = false;
        let rafId = null;

        function animateGlow() {
            currentX += (targetX - currentX) * 0.12;
            currentY += (targetY - currentY) * 0.12;
            mouseGlow.style.left = currentX + 'px';
            mouseGlow.style.top = currentY + 'px';
            if (isHoveringGlowSection) {
                rafId = requestAnimationFrame(animateGlow);
            }
        }

        mouseTrackSections.forEach(sec => {
            sec.addEventListener('mouseenter', (e) => {
                isHoveringGlowSection = true;
                mouseGlow.classList.add('active');
                targetX = currentX = e.clientX;
                targetY = currentY = e.clientY;
                cancelAnimationFrame(rafId);
                rafId = requestAnimationFrame(animateGlow);
            });
            sec.addEventListener('mousemove', (e) => {
                targetX = e.clientX;
                targetY = e.clientY;
            });
            sec.addEventListener('mouseleave', () => {
                isHoveringGlowSection = false;
                mouseGlow.classList.remove('active');
            });
        });
    });
    </script>
    @livewireScripts
</body>
</html>
