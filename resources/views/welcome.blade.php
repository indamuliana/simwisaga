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

    <!-- Google Fonts: Plus Jakarta Sans (Unified Modern Geometry) & Roboto Mono (Technical Accents) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Unified High-Contrast Institutional Design System: Bright & Warm Education -->
    <style>
        [x-cloak] { display: none !important; }

        :root {
            /* ── JIS Luminous Institutional Design Tokens (jisedu.or.id) ── */
            --jis-navy: #00519E;              /* JIS Primary Endeavour Blue */
            --jis-navy-dark: #00305F;         /* Deep Midnight Navy for Utility, Impact & Footer */
            --jis-navy-light: #EBF4FC;        /* Soft Ice Blue Background Tint */
            --jis-navy-hover: #003D7A;        /* Deeper Navy on hover */

            /* Secondary / Accent Colors */
            --jis-gold: #F5D84A;              /* JIS Signature Luminous Sun Gold */
            --jis-gold-hover: #E2B714;         /* Rich Amber Gold */
            --jis-forest: #1F513F;            /* Indonesian Tropical Forest Green */
            --jis-forest-light: #E8F3EE;      /* Soft Botanical Green Tint */

            /* Neutral Surface & Background Colors */
            --brand-canvas: #FFFFFF;          /* Pure White Core Canvas */
            --brand-surface: #FFFFFF;         /* Clean Card Surface */
            --brand-surface-subtle: #F8F9FA;  /* Off-White Section Contrast */
            --brand-surface-warm: #FAF8F5;    /* Warm Linen Texture */

            /* Legacy / Compatibility Fallbacks */
            --brand-coral: #00519E;           /* Remapped to JIS Navy */
            --brand-coral-hover: #00305F;     /* Remapped to Midnight Navy */
            --brand-coral-subtle: #EBF4FC;    /* Remapped to Ice Blue */
            --brand-blue: #00519E;            /* JIS Endeavour Blue */
            --brand-blue-hover: #00305F;      /* Midnight Deep */
            --brand-blue-subtle: #EBF4FC;     /* Ice Blue */
            --brand-green: #1F513F;           /* Tropical Forest Green */
            --brand-green-subtle: #E8F3EE;    /* Botanical Mist */
            --brand-amber: #F5D84A;           /* JIS Sun Gold */
            --brand-amber-subtle: #FEF9E7;    /* Soft Gold Mist */

            /* Typography Hierarchy */
            --brand-heading: #00305F;         /* Midnight Deep Navy */
            --brand-body: #232327;            /* Shark Dark Charcoal for High Readability */
            --brand-muted: #5F6368;           /* Subdued Slate for Descriptions */
            --brand-border: #E8EAED;          /* Soft Card & Divider Border */
            
            /* Motion Timing Tokens */
            --gemini-ease-out-quart: cubic-bezier(0.165, 0.84, 0.44, 1);
            --gemini-ease-out-quad: cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        html, body {
            max-width: 100%;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--brand-canvas);
            color: #1E293B;
        }

        /* ── High-Contrast Global Text Selection (Seleksi / Blok Teks Bebas Masalah Putih) ── */
        ::selection, *::selection {
            background-color: #00519E !important;
            color: #FFFFFF !important;
        }
        ::-moz-selection, *::-moz-selection {
            background-color: #00519E !important;
            color: #FFFFFF !important;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .font-editorial {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-style: italic;
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

        /* ── Multi-Stop Gradient Text (Coral Orange to Royal Blue) ── */
        .gemini-gradient-text {
            background: linear-gradient(135deg, #FF6B4A 0%, #EA580C 45%, #2563EB 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gemini-gold-gradient {
            background: linear-gradient(90deg, #F59E0B 0%, #FF6B4A 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gemini-blue-gradient {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gemini-coral-gradient {
            background: linear-gradient(135deg, #FF6B4A 0%, #FF5722 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ── Concentric Floating Card System (Pure White + Soft Warm Diffusion Shadow) ── */
        .google-card {
            background: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 24px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(0, 0, 0, 0.02);
            transition: transform 0.35s var(--gemini-ease-out-quart), 
                        box-shadow 0.35s var(--gemini-ease-out-quart), 
                        border-color 0.35s var(--gemini-ease-out-quart);
            will-change: transform, box-shadow;
        }
        .google-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -8px rgba(255, 107, 74, 0.12), 0 0 0 1px rgba(255, 107, 74, 0.25);
            border-color: rgba(255, 107, 74, 0.4);
        }

        /* ── Atmospheric Ambient Mesh Glow (Warm Peach & Soft Sky Blue) ── */
        .hero-mesh-bg {
            background: radial-gradient(circle at 50% 0%, rgba(255, 107, 74, 0.08) 0%, transparent 60%),
                        radial-gradient(circle at 100% 50%, rgba(37, 99, 235, 0.06) 0%, transparent 50%),
                        radial-gradient(circle at 0% 80%, rgba(16, 185, 129, 0.06) 0%, transparent 50%);
        }

        /* ── Tab Pill Navigation (Mengenal Wikrama & Jurusan) ── */
        .tab-pill-active {
            background-color: #FF6B4A !important;
            background-image: linear-gradient(135deg, #FF6B4A 0%, #FF5722 100%) !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 14px -2px rgba(255, 107, 74, 0.45) !important;
        }
        .tab-pill-blue-active {
            background-color: #2563EB !important;
            background-image: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 14px -2px rgba(37, 99, 235, 0.45) !important;
        }
        .tab-pill-inactive {
            color: #334155 !important;
            background-color: transparent !important;
        }
        .tab-pill-inactive:hover {
            color: #FF6B4A !important;
            background-color: #FFF1EE !important;
        }

        /* ── Dynamic Hero Media Ken Burns Zoom & Grow ── */
        .hero-media-scale {
            transition: transform 8000ms cubic-bezier(0.25, 1, 0.5, 1), filter 1000ms ease-out;
            will-change: transform;
            transform-origin: center center;
        }

        /* ── Dinamika Siswa JIS Seamless Ribbon (No-Gap Flush Cards) ── */
        .student-moment-card {
            width: 290px !important;
            min-width: 290px !important;
            max-width: 290px !important;
            height: 480px !important;
            min-height: 480px !important;
            max-height: 480px !important;
            flex-shrink: 0 !important;
            border-radius: 0 !important;
        }

        @media (max-width: 640px) {
            .student-moment-card {
                width: 280px !important;
                min-width: 280px !important;
                max-width: 280px !important;
                height: 440px !important;
                min-height: 440px !important;
                max-height: 440px !important;
            }
        }

        /* ── Full-Width Edge-to-Edge Dinamika Siswa Track Layout ── */
        .dinamika-fullwidth-wrapper {
            width: 100%;
            position: relative;
            z-index: 10;
            padding-left: 1rem;
            padding-right: 0;
        }
        @media (min-width: 640px) {
            .dinamika-fullwidth-wrapper {
                padding-left: 1.5rem;
            }
        }
        @media (min-width: 1024px) {
            .dinamika-fullwidth-wrapper {
                padding-left: 2rem;
            }
        }
        @media (min-width: 1280px) {
            .dinamika-fullwidth-wrapper {
                /* Presisi sejajar dengan sisi kiri kontainer max-w-7xl */
                padding-left: max(2rem, calc((100vw - 80rem) / 2 + 2rem));
            }
        }

        .dinamika-cards-track {
            padding-right: 1.5rem;
        }
        @media (min-width: 640px) {
            .dinamika-cards-track {
                padding-right: 2rem;
            }
        }
        @media (min-width: 1280px) {
            .dinamika-cards-track {
                /* Padding akhir saat di-scroll mentok ke ujung kanan */
                padding-right: max(2rem, calc((100vw - 80rem) / 2 + 2rem));
            }
        }

        /* Inner media radius follows concentric rule: R_inner = R_outer - padding */
        .google-card-inner {
            border-radius: 16px;
            overflow: hidden;
        }

        /* ── Google Chip & Tag Badges (Pastel Friendly Pills - Bright & Warm) ── */
        .google-tag {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 0.725rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 0.35rem 1rem;
            border-radius: 9999px;
            background: #FFF1EE;
            color: #EA580C;
            border: 1px solid rgba(255, 107, 74, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            box-shadow: 0 1px 3px rgba(255, 107, 74, 0.08);
        }
        .google-tag-blue {
            background: #EFF6FF !important;
            color: #2563EB !important;
            border-color: rgba(37, 99, 235, 0.25) !important;
        }
        .google-tag-green {
            background: #ECFDF5 !important;
            color: #059669 !important;
            border-color: rgba(16, 185, 129, 0.25) !important;
        }
        .google-tag-amber {
            background: #FEF3C7 !important;
            color: #D97706 !important;
            border-color: rgba(245, 158, 11, 0.25) !important;
        }

        /* ── Action Buttons (Coral Orange & Royal Blue) ── */
        .btn-google-primary {
            background: linear-gradient(135deg, #FF6B4A 0%, #FF5722 100%);
            color: #FFFFFF;
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.8rem 1.85rem;
            transition: all 0.3s var(--gemini-ease-out-quart);
            box-shadow: 0 6px 18px rgba(255, 107, 74, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-google-primary:hover {
            background: linear-gradient(135deg, #FF7B5C 0%, #E85535 100%);
            box-shadow: 0 10px 26px rgba(255, 107, 74, 0.48);
            transform: translateY(-2px);
            color: #FFFFFF;
        }
        .btn-google-primary:active {
            transform: scale(0.98);
        }

        .btn-google-blue {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            color: #FFFFFF;
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.8rem 1.85rem;
            transition: all 0.3s var(--gemini-ease-out-quart);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-google-blue:hover {
            background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
            box-shadow: 0 10px 26px rgba(37, 99, 235, 0.48);
            transform: translateY(-2px);
            color: #FFFFFF;
        }

        .btn-google-gold {
            background: linear-gradient(135deg, #FBBF24 0%, #F59E0B 50%, #D97706 100%);
            color: #FFFFFF;
            font-weight: 800;
            font-size: 0.875rem;
            border-radius: 9999px;
            border: 1px solid rgba(254, 240, 138, 0.6);
            padding: 0.8rem 1.85rem;
            transition: all 0.3s var(--gemini-ease-out-quart);
            box-shadow: 0 6px 18px rgba(245, 158, 11, 0.4);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-google-gold:hover {
            background: linear-gradient(135deg, #FDE68A 0%, #FBBF24 50%, #F59E0B 100%);
            box-shadow: 0 10px 28px rgba(245, 158, 11, 0.55);
            transform: translateY(-2px);
            color: #FFFFFF;
        }
        .btn-google-gold:active {
            transform: scale(0.98);
        }

        .btn-google-outline {
            background: #FFFFFF;
            color: #1E293B;
            border: 1.5px solid #E2E8F0;
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.8rem 1.75rem;
            transition: all 0.3s var(--gemini-ease-out-quart);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        }
        .btn-google-outline:hover {
            border-color: #00519E;
            color: #00519E;
            background: #EBF4FC;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 81, 158, 0.15);
        }

        /* ── JIS Signature Pill Buttons (Rounded-Full) ── */
        .btn-jis-pill-gold {
            background-color: #F5D84A;
            color: #232327 !important;
            border: 1.5px solid #E2B714;
            font-weight: 800;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.8rem 1.85rem;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 18px rgba(245, 216, 74, 0.45);
            text-decoration: none;
            cursor: pointer;
        }
        .btn-jis-pill-gold:hover {
            background-color: #E2B714;
            box-shadow: 0 8px 24px rgba(226, 183, 20, 0.55);
            transform: translateY(-2px);
            color: #111827 !important;
        }
        .btn-jis-pill-gold:active {
            transform: scale(0.98);
        }

        .btn-jis-pill-navy {
            background-color: #00519E;
            color: #FFFFFF !important;
            border: 1.5px solid #00305F;
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.8rem 1.85rem;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 18px rgba(0, 81, 158, 0.35);
            text-decoration: none;
            cursor: pointer;
        }
        .btn-jis-pill-navy:hover {
            background-color: #00305F;
            box-shadow: 0 8px 24px rgba(0, 48, 95, 0.45);
            transform: translateY(-2px);
            color: #FFFFFF !important;
        }

        .btn-jis-pill-outline {
            background: #FFFFFF;
            color: #232327 !important;
            border: 1.5px solid #CBD5E1;
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.8rem 1.85rem;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            text-decoration: none;
            cursor: pointer;
        }
        .btn-jis-pill-outline:hover {
            border-color: #00519E;
            color: #00519E !important;
            background: #EBF4FC;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 81, 158, 0.15);
        }

        /* ── JIS Interactive Follower Cursor (Desktop Only) ── */
        @media (min-width: 1024px) {
            .jis-cursor-dot {
                position: fixed;
                top: 0;
                left: 0;
                width: 8px;
                height: 8px;
                background-color: #00519E;
                border-radius: 50%;
                pointer-events: none;
                z-index: 99999;
                transform: translate(-50%, -50%);
                transition: opacity 0.2s;
            }
            .jis-cursor-follower {
                position: fixed;
                top: 0;
                left: 0;
                width: 36px;
                height: 36px;
                border: 1.5px solid rgba(0, 81, 158, 0.55);
                border-radius: 50%;
                pointer-events: none;
                z-index: 99998;
                transform: translate(-50%, -50%);
                transition: width 0.25s ease-out, height 0.25s ease-out, background-color 0.25s, border-color 0.25s;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 9px;
                font-weight: 800;
                color: transparent;
                letter-spacing: 0.1em;
            }
            body.jis-cursor-hover .jis-cursor-follower {
                width: 68px;
                height: 68px;
                background-color: rgba(0, 48, 95, 0.92);
                border-color: #F5D84A;
                color: #FFFFFF;
                box-shadow: 0 10px 25px rgba(0, 48, 95, 0.35);
            }
            body.jis-cursor-hover .jis-cursor-dot {
                opacity: 0;
            }
        }

        /* ── SVG Botanical Vine Line Drawing ── */
        .jis-vine-path {
            stroke-dasharray: 1200;
            stroke-dashoffset: 1200;
            transition: stroke-dashoffset 0.1s linear;
        }

        /* ── Sticky Quick-Action Dock (Only on Extra Wide 2xl+ Displays) ── */
        .hp-sticky-dock {
            display: none !important;
        }
        @media (min-width: 1536px) {
            .hp-sticky-dock {
                position: fixed;
                right: 0;
                top: 50%;
                transform: translateY(-50%);
                z-index: 45;
                display: flex !important;
                flex-direction: column;
                gap: 6px;
            }
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

        /* ── BMW Partner Logo Uniform Slot Track (Jarak Presisi & Konsisten) ── */
        .partner-logo-slot {
            width: auto;
            max-width: 120px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            padding: 0 4px;
            position: relative;
            cursor: pointer;
        }
        @media (min-width: 640px) {
            .partner-logo-slot {
                max-width: 135px;
                height: 52px;
                padding: 0 6px;
            }
        }
        @media (min-width: 1024px) {
            .partner-logo-slot {
                max-width: 150px;
                height: 56px;
                padding: 0 8px;
            }
        }

        .partner-logo-img {
            max-height: 36px;
            max-width: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), filter 0.3s ease;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.05));
        }
        @media (min-width: 640px) {
            .partner-logo-img {
                max-height: 40px;
            }
        }
        @media (min-width: 1024px) {
            .partner-logo-img {
                max-height: 44px;
            }
        }
        .partner-logo-slot:hover .partner-logo-img {
            transform: scale(1.12);
        }

        .partner-logo-item {
            display: inline-flex !important;
            align-items: center !important;
            vertical-align: middle !important;
        }

        .partner-logo-item .partner-logo-text {
            display: inline-flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-self: center !important;
            margin-top: auto !important;
            margin-bottom: auto !important;
            line-height: 1.25 !important;
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

        /* ── Differentiated Card Styling System (Bright & Warm Education) ── */
        
        /* 1. Bento Card Style (#mengenal) */
        .card-bento {
            background: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 24px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            transition: all 0.35s var(--gemini-ease-out-quart);
            position: relative;
        }
        .card-bento:hover {
            transform: translateY(-6px);
            border-color: rgba(255, 107, 74, 0.4);
            box-shadow: 0 20px 40px -8px rgba(255, 107, 74, 0.12), 0 0 0 1px rgba(255, 107, 74, 0.2);
        }

        /* 2. Stat Metric Glowing Pillar Card (#fakta) */
        .card-metric {
            background: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 22px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
            transition: all 0.35s var(--gemini-ease-out-quart);
        }
        .card-metric::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #FF6B4A 0%, #F59E0B 50%, #2563EB 100%);
            opacity: 0.9;
            transition: height 0.3s ease;
        }
        .card-metric:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px -8px rgba(37, 99, 235, 0.12);
            border-color: rgba(37, 99, 235, 0.35);
        }
        .card-metric:hover::before {
            height: 5px;
        }

        /* 3. Blueprint Spec Card (#jurusan) */
        .card-blueprint {
            background: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 20px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            transition: all 0.35s var(--gemini-ease-out-quart);
            position: relative;
        }
        .card-blueprint:hover {
            transform: translateY(-7px);
            border-color: #2563EB;
            box-shadow: 0 20px 40px -6px rgba(37, 99, 235, 0.15), 0 0 0 1px #2563EB;
        }

        /* 4. Architectural Pillar Brick Card (#profil) */
        .card-pillar-brick {
            background: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-left: 4px solid #FF6B4A;
            border-radius: 16px;
            padding: 1rem 1.25rem;
            transition: all 0.3s var(--gemini-ease-out-quart);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
        }
        .card-pillar-brick:hover {
            border-left-color: #2563EB;
            transform: translateX(4px);
            box-shadow: 0 10px 28px rgba(37, 99, 235, 0.12);
        }

        /* 5. Quote Bubble Card (#suara-komunitas) */
        .card-quote-bubble {
            background: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-bottom: 3px solid #2563EB;
            border-radius: 24px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            transition: all 0.35s var(--gemini-ease-out-quart);
            position: relative;
        }
        .card-quote-bubble:hover {
            transform: translateY(-5px);
            border-bottom-color: #FF6B4A;
            border-color: rgba(255, 107, 74, 0.35);
            box-shadow: 0 20px 40px -6px rgba(255, 107, 74, 0.12);
        }

        /* 6. Gallery Tile Card (#fasilitas) */
        .card-gallery-tile {
            background: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            transition: all 0.35s var(--gemini-ease-out-quart);
        }
        .card-gallery-tile:hover {
            transform: translateY(-6px);
            border-color: rgba(255, 107, 74, 0.4);
            box-shadow: 0 20px 40px -8px rgba(255, 107, 74, 0.15);
        }

        /* 7. Editorial News Card (#berita) */
        .card-editorial {
            background: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            transition: all 0.35s var(--gemini-ease-out-quart);
        }
        .card-editorial:hover {
            transform: translateY(-5px);
            border-color: rgba(37, 99, 235, 0.35);
            box-shadow: 0 20px 40px -6px rgba(37, 99, 235, 0.12);
        }

        /* 8. Calendar Slate Card (#agenda) */
        .card-calendar-slate {
            background: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 24px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            transition: all 0.35s var(--gemini-ease-out-quart);
            position: relative;
        }
        .card-calendar-slate:hover {
            transform: translateY(-6px);
            border-color: rgba(255, 107, 74, 0.4);
            box-shadow: 0 20px 40px -6px rgba(255, 107, 74, 0.12);
        }

        /* 9. Ministry Seal Crest Card (#sekolah-teladan) */
        .card-seal-crest {
            background: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 20px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s var(--gemini-ease-out-quart);
        }
        .card-seal-crest:hover {
            transform: translateY(-4px);
            border-color: rgba(37, 99, 235, 0.4);
            box-shadow: 0 16px 36px -4px rgba(37, 99, 235, 0.12);
        }

        /* ── Multi-Flavor Smooth Scroll Reveal System ── */
        [data-animate] {
            opacity: 0;
            transition: transform 0.8s var(--gemini-ease-out-quart), 
                        opacity 0.75s var(--gemini-ease-out-quart);
            will-change: transform, opacity;
        }

        /* Default / fade-up */
        [data-animate]:not([data-animate="scale"]):not([data-animate="slide-right"]):not([data-animate="slide-left"]):not([data-animate="tilt"]) {
            transform: translateY(28px);
        }
        [data-animate]:not([data-animate="scale"]):not([data-animate="slide-right"]):not([data-animate="slide-left"]):not([data-animate="tilt"]).is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Flavor 2: Scale Pop */
        [data-animate="scale"] {
            transform: scale(0.92);
        }
        [data-animate="scale"].is-visible {
            opacity: 1;
            transform: scale(1);
        }

        /* Flavor 3: Slide from Right */
        [data-animate="slide-right"] {
            transform: translateX(36px);
        }
        [data-animate="slide-right"].is-visible {
            opacity: 1;
            transform: translateX(0);
        }

        /* Flavor 4: Slide from Left */
        [data-animate="slide-left"] {
            transform: translateX(-36px);
        }
        [data-animate="slide-left"].is-visible {
            opacity: 1;
            transform: translateX(0);
        }

        /* Flavor 5: Perspective 3D Tilt */
        [data-animate="tilt"] {
            transform: perspective(900px) rotateX(10deg) translateY(20px);
        }
        [data-animate="tilt"].is-visible {
            opacity: 1;
            transform: perspective(900px) rotateX(0deg) translateY(0);
        }

        [data-animate-delay="1"] { transition-delay: 0.08s !important; }
        [data-animate-delay="2"] { transition-delay: 0.16s !important; }
        [data-animate-delay="3"] { transition-delay: 0.24s !important; }
        [data-animate-delay="4"] { transition-delay: 0.32s !important; }
        [data-animate-delay="5"] { transition-delay: 0.40s !important; }

        /* ── Interactive Link Arrow Hover Slide ── */
        .group-hover-arrow {
            transition: transform 0.25s var(--gemini-ease-out-quart);
        }
        .group:hover .group-hover-arrow {
            transform: translateX(5px);
        }

        /* ── Soft Atmospheric Spotlight Glow (Warm Coral & Sky Blue) ── */
        .mouse-glow {
            position: fixed;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 107, 74, 0.12) 0%, rgba(37, 99, 235, 0.08) 40%, transparent 70%);
            pointer-events: none;
            z-index: 10;
            transform: translate(-50%, -50%);
            transition: opacity 0.3s ease-out;
            opacity: 0;
            filter: blur(50px);
        }
        .mouse-glow.active { opacity: 1; }

        @media (prefers-reduced-motion: reduce) {
            [data-animate] { opacity: 1 !important; transform: none !important; transition: none !important; }
            .gemini-ring-small, .gemini-ring-large { animation: none !important; }
            .mouse-glow { display: none !important; }
        }
    
        /* ── Instant Vanilla Scroll Fallback for Solid Header Contrast (Crisp White Floating Nav) ── */
        #main-nav-container.is-scrolled {
            background-color: rgba(255, 255, 255, 0.96) !important;
            backdrop-filter: blur(14px) !important;
            -webkit-backdrop-filter: blur(14px) !important;
            border-bottom: 1px solid rgba(226, 232, 240, 0.85) !important;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.07) !important;
        }
        #main-nav-container.is-scrolled a.nav-link {
            color: #1E293B !important;
        }
        #main-nav-container.is-scrolled a.nav-link:hover {
            color: #00519E !important;
        }
        #main-nav-container.is-scrolled .nav-brand-title {
            color: #0F172A !important;
        }
        #main-nav-container.is-scrolled .nav-brand-sub {
            color: #64748B !important;
        }
    </style>
    @livewireStyles
</head>
<body class="min-h-screen text-slate-800 antialiased flex flex-col justify-between relative bg-[#FFFFFF]"
      x-data="{ 
          scrolled: false,
          mobileMenu: false,
          flyerModal: false,
          imageModal: false,
          modalImageSrc: '',
          modalImageTitle: '',
          modalImageCategory: '',
          activeKnowTab: 'admissions',
          openImageModal(src, title, cat) {
              this.modalImageSrc = src;
              this.modalImageTitle = title;
              this.modalImageCategory = cat;
              this.imageModal = true;
          }
      }"
      @scroll.window="scrolled = (window.pageYOffset > 30)">

    <!-- ── JIS Custom Interactive Cursor (Desktop Only) ── -->
    <div class="jis-cursor-dot hidden lg:block"></div>
    <div class="jis-cursor-follower hidden lg:block"></div>



    {{-- ── Navigation & Sticky Quick Actions ── --}}
    @include('landing.sections.navigation')

    {{-- ── Main Content Landmark ── --}}
    <main class="flex-grow">
        @include('landing.sections.hero')
        @include('landing.sections.filosofi')
        @include('landing.sections.mengenal')
        @include('landing.sections.fakta')
        @include('landing.sections.jurusan')
        @include('landing.sections.dinamika')
        @include('landing.sections.profil')
        @include('landing.sections.suara-komunitas')
        @include('landing.sections.fasilitas')
        @include('landing.sections.berita')
        @include('landing.sections.agenda')
        @include('landing.sections.rapor-mutu')
        @include('landing.sections.pilar-karakter')
        @include('landing.sections.pilar-budaya')
        @include('landing.sections.pilar-pemimpin')
        @include('landing.sections.sekolah-teladan')
        @include('landing.sections.module-blocks')
        @include('landing.sections.cta-banner')
    </main>

    {{-- ── Modals & Footer ── --}}
    @include('landing.sections.modals')
    @include('landing.sections.footer')

    <!-- ── Interactive Google AI Style Animations Script ── -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {

        // ── Guaranteed Instant Vanilla Navbar Scroll Fallback ──
        const mainNav = document.getElementById('main-nav-container');
        function syncNavScroll() {
            if (!mainNav) return;
            if (window.pageYOffset > 30) {
                mainNav.classList.add('is-scrolled');
            } else {
                mainNav.classList.remove('is-scrolled');
            }
        }
        window.addEventListener('scroll', syncNavScroll, { passive: true });
        syncNavScroll();

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
            }, { threshold: 0.02, rootMargin: '0px 0px 60px 0px' });
            animEls.forEach(el => obs.observe(el));

            // Jaminan keamanan: seluruh elemen dipastikan tampil setelah 1 detik
            setTimeout(() => {
                animEls.forEach(el => el.classList.add('is-visible'));
            }, 1000);
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
        // ── 3. JIS Custom Cursor Engine (Desktop Only) ──
        function initJISCursor() {
            if (window.innerWidth < 1024) return;
            const dot = document.querySelector('.jis-cursor-dot');
            const follower = document.querySelector('.jis-cursor-follower');
            if (!dot || !follower) return;

            let mouseX = -100, mouseY = -100;
            let followerX = -100, followerY = -100;
            let isMoving = false;

            window.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = e.clientY;
                dot.style.transform = `translate(${mouseX}px, ${mouseY}px)`;
                if (!isMoving) {
                    isMoving = true;
                    dot.style.opacity = '1';
                    follower.style.opacity = '1';
                }
            }, { passive: true });

            function renderFollower() {
                followerX += (mouseX - followerX) * 0.18;
                followerY += (mouseY - followerY) * 0.18;
                follower.style.transform = `translate(${followerX}px, ${followerY}px)`;
                requestAnimationFrame(renderFollower);
            }
            requestAnimationFrame(renderFollower);

            const hoverTargets = document.querySelectorAll('a, button, [data-cursor], .interactive-card, [role="button"], input, select');
            hoverTargets.forEach((target) => {
                target.addEventListener('mouseenter', () => {
                    document.body.classList.add('jis-cursor-hover');
                    const label = target.getAttribute('data-cursor') || target.getAttribute('data-cursor-text') || 'EXPLORE';
                    follower.textContent = label;
                });
                target.addEventListener('mouseleave', () => {
                    document.body.classList.remove('jis-cursor-hover');
                    follower.textContent = '';
                });
            });
        }
        initJISCursor();

        // ── 4. JIS Botanical Vine SVG Scroll Animation ──
        function initVineAnimation() {
            const vinePaths = document.querySelectorAll('.jis-vine-path');
            if (!vinePaths.length) return;

            function onScrollVine() {
                const windowHeight = window.innerHeight;
                vinePaths.forEach(path => {
                    const rect = path.getBoundingClientRect();
                    if (rect.top < windowHeight && rect.bottom > 0) {
                        const progress = Math.min(Math.max((windowHeight - rect.top) / (windowHeight + rect.height * 0.5), 0), 1);
                        const offset = 1200 - (progress * 1200);
                        path.style.strokeDashoffset = offset;
                    }
                });
            }
            window.addEventListener('scroll', onScrollVine, { passive: true });
            onScrollVine();
        }
        initVineAnimation();

        // ── 5. JIS Animated Stats Counter ──
        function initStatsCounter() {
            const statNumbers = document.querySelectorAll('.jis-counter-number');
            if (!statNumbers.length || !('IntersectionObserver' in window)) return;

            const observer = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = entry.target;
                        const targetValue = parseInt(target.getAttribute('data-count'), 10) || 0;
                        const prefix = target.getAttribute('data-prefix') || '';
                        const suffix = target.getAttribute('data-suffix') || '';
                        const duration = 1800;
                        const stepTime = 25;
                        const totalSteps = duration / stepTime;
                        let currentStep = 0;

                        const timer = setInterval(() => {
                            currentStep++;
                            const progress = currentStep / totalSteps;
                            const easedProgress = 1 - Math.pow(1 - progress, 2);
                            const currentVal = Math.round(easedProgress * targetValue);
                            target.textContent = prefix + currentVal.toLocaleString('id-ID') + suffix;

                            if (currentStep >= totalSteps) {
                                target.textContent = prefix + targetValue.toLocaleString('id-ID') + suffix;
                                clearInterval(timer);
                            }
                        }, stepTime);

                        obs.unobserve(target);
                    }
                });
            }, { threshold: 0.25 });

            statNumbers.forEach(num => observer.observe(num));
        }
        initStatsCounter();
    });
    </script>

    @livewireScripts
</body>
</html>
