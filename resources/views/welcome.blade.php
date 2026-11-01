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
    <meta name="description" content="SMK Wikrama 1 Garut - Sekolah Menengah Kejuruan Pusat Keunggulan bidang Teknologi Informasi, Bisnis Digital, dan Hospitaliti Pariwisata berakhlakul karimah.">
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">

    <!-- Google Fonts: Plus Jakarta Sans (Institutional Clarity), Playfair Display (Manifesto Elegance), & Roboto Mono (Technical Accents) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600;1,700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Hallmark · v2.0.0 · theme: Wikrama Royal Archive (dealt manual) · genre: editorial */
        /* Critique: Prestigious international academy masthead, asymmetrical bento rhythms, warm linen canvas, native lazyloading */

        [x-cloak] { display: none !important; }

        :root {
            /* ── Core Brand Identity (Wikrama International Spirit) ── */
            --wikrama-navy: #00519E;              /* Primary Endeavour Blue */
            --wikrama-navy-dark: #00305F;         /* Midnight Deep Navy */
            --wikrama-navy-light: #EBF4FC;        /* Soft Ice Blue Background Tint */
            --wikrama-navy-hover: #003D7A;        /* Deeper Navy on Hover */

            --wikrama-gold: #F5D84A;              /* Signature Luminous Sun Gold */
            --wikrama-gold-hover: #E2B714;         /* Rich Amber Gold */
            --wikrama-gold-light: #FEF9E7;        /* Soft Gold Mist */

            --wikrama-forest: #1F513F;            /* Indonesian Tropical Forest Green */
            --wikrama-forest-light: #E8F3EE;      /* Soft Botanical Green Tint */

            /* ── Surfaces & Canvas ── */
            --wikrama-canvas: #FFFFFF;            /* Pure Crisp White */
            --wikrama-surface: #FFFFFF;           /* Card Surface */
            --wikrama-surface-subtle: #F8F9FA;    /* Slate-tint Section Contrast */
            --wikrama-surface-warm: #FAF8F5;      /* Warm Editorial Linen Texture */

            /* ── Typography & Borders ── */
            --wikrama-heading: #00305F;           /* Deep Midnight Navy for High Contrast Titles */
            --wikrama-body: #1E293B;              /* Dark Slate Body Text */
            --wikrama-muted: #64748B;             /* Subdued Descriptive Text */
            --wikrama-border: #E2E8F0;            /* Soft Card & Divider Border */
            --wikrama-border-strong: #CBD5E1;     /* Emphasized Divider Border */

            /* ── Fonts & Motion Timing ── */
            --font-editorial: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --font-manifesto: 'Playfair Display', Georgia, serif;
            --font-mono: 'Roboto Mono', ui-monospace, monospace;
            --ease-editorial: cubic-bezier(0.16, 1, 0.3, 1);
            --ease-out-quart: cubic-bezier(0.165, 0.84, 0.44, 1);

            /* ── Compatibility Fallbacks ── */
            --jis-navy: var(--wikrama-navy);
            --jis-navy-dark: var(--wikrama-navy-dark);
            --jis-navy-light: var(--wikrama-navy-light);
            --jis-gold: var(--wikrama-gold);
            --jis-gold-hover: var(--wikrama-gold-hover);
            --jis-forest: var(--wikrama-forest);
            --jis-forest-light: var(--wikrama-forest-light);
            --brand-canvas: var(--wikrama-canvas);
            --brand-surface: var(--wikrama-surface);
            --brand-surface-subtle: var(--wikrama-surface-subtle);
            --brand-surface-warm: var(--wikrama-surface-warm);
            --brand-heading: var(--wikrama-heading);
            --brand-body: var(--wikrama-body);
            --brand-muted: var(--wikrama-muted);
            --brand-border: var(--wikrama-border);
            --brand-coral: var(--wikrama-navy);
            --brand-blue: var(--wikrama-navy);
            --brand-green: var(--wikrama-forest);
            --brand-amber: var(--wikrama-gold);
        }

        html, body {
            max-width: 100%;
            overflow-x: clip;
            -webkit-overflow-scrolling: touch;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: var(--font-editorial);
            background-color: var(--wikrama-canvas);
            color: var(--wikrama-body);
        }

        /* ── Accessible Focus-Visible ── */
        a:focus-visible, 
        button:focus-visible, 
        input:focus-visible, 
        select:focus-visible,
        [role="button"]:focus-visible {
            outline: 2px solid var(--wikrama-navy);
            outline-offset: 2px;
        }

        /* ── High-Contrast Global Text Selection ── */
        ::selection, *::selection {
            background-color: var(--wikrama-navy) !important;
            color: #FFFFFF !important;
        }
        ::-moz-selection, *::-moz-selection {
            background-color: var(--wikrama-navy) !important;
            color: #FFFFFF !important;
        }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .font-editorial {
            font-family: var(--font-editorial);
            font-style: italic;
        }

        .font-manifesto {
            font-family: var(--font-manifesto);
        }

        .font-mono-code {
            font-family: var(--font-mono);
        }

        /* ── Full-Color Minimalist Institutional Logo Strip (BMW Link & Match) ── */
        .partner-loop-list {
            display: flex !important;
            align-items: center !important;
            flex-shrink: 0 !important;
            gap: 40px !important;
            padding-right: 40px !important;
        }
        @media (min-width: 640px) {
            .partner-loop-list {
                gap: 56px !important;
                padding-right: 56px !important;
            }
        }
        @media (min-width: 1024px) {
            .partner-loop-list {
                gap: 64px !important;
                padding-right: 64px !important;
            }
        }
        .partner-logo-item {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            width: auto !important;
            flex-shrink: 0;
            transition: opacity 0.25s var(--ease-editorial), transform 0.25s var(--ease-editorial);
        }
        .partner-logo-item:hover {
            opacity: 0.85;
            transform: translateY(-2px) scale(1.05);
        }
        .partner-logo-img {
            height: 34px;
            max-height: 34px;
            width: auto;
            max-width: 120px;
            object-fit: contain;
            filter: none !important;
            opacity: 1 !important;
            display: block;
        }
        @media (min-width: 640px) {
            .partner-logo-img {
                height: 40px;
                max-height: 40px;
                max-width: 140px;
            }
        }
        @media (min-width: 1024px) {
            .partner-logo-img {
                height: 44px;
                max-height: 44px;
                max-width: 160px;
            }
        }

        /* ── Infrastructure & Facility Showcase Slider ── */
        .facility-showcase-section {
            width: 100%;
            position: relative;
            min-height: 480px;
            overflow: hidden;
            background-color: #020617;
            color: #FFFFFF;
            scroll-margin-top: 5rem;
        }
        @media (min-width: 640px) {
            .facility-showcase-section {
                min-height: 520px;
            }
        }
        @media (min-width: 1024px) {
            .facility-showcase-section {
                min-height: 620px;
            }
        }
        @media (min-width: 1280px) {
            .facility-showcase-section {
                min-height: 650px;
            }
        }

        .facility-thumb-card {
            position: relative;
            flex-shrink: 0 !important;
            width: 135px !important;
            min-width: 135px !important;
            max-width: 135px !important;
            height: 185px !important;
            min-height: 185px !important;
            max-height: 185px !important;
            border-radius: 1rem !important;
            overflow: hidden !important;
            cursor: pointer;
            box-sizing: border-box !important;
            transition: transform 0.25s ease, opacity 0.25s ease, box-shadow 0.25s ease;
        }
        @media (min-width: 640px) {
            .facility-thumb-card {
                width: 160px !important;
                min-width: 160px !important;
                max-width: 160px !important;
                height: 220px !important;
                min-height: 220px !important;
                max-height: 220px !important;
            }
        }
        @media (min-width: 1024px) {
            .facility-thumb-card {
                width: 190px !important;
                min-width: 190px !important;
                max-width: 190px !important;
                height: 260px !important;
                min-height: 260px !important;
                max-height: 260px !important;
            }
        }
        .facility-thumb-card img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            display: block !important;
        }

        /* ── Institutional Tags & Badges (Refined, Untracked Body Face) ── */
        .google-tag {
            font-family: var(--font-editorial);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.35rem 0.95rem;
            border-radius: 9999px;
            background: var(--wikrama-navy-light);
            color: var(--wikrama-navy);
            border: 1px solid rgba(0, 81, 158, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            box-shadow: 0 1px 3px rgba(0, 81, 158, 0.06);
        }
        .google-tag-blue {
            background: var(--wikrama-navy-light) !important;
            color: var(--wikrama-navy) !important;
            border-color: rgba(0, 81, 158, 0.25) !important;
        }
        .google-tag-green {
            background: var(--wikrama-forest-light) !important;
            color: var(--wikrama-forest) !important;
            border-color: rgba(31, 81, 63, 0.25) !important;
        }
        .google-tag-amber {
            background: var(--wikrama-gold-light) !important;
            color: #92400E !important;
            border-color: rgba(226, 183, 20, 0.35) !important;
        }

        /* ── Button Tokens ── */
        .btn-jis-pill-gold {
            background-color: var(--wikrama-gold);
            color: #00305F !important;
            border: 1.5px solid var(--wikrama-gold-hover);
            font-weight: 800;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.75rem 1.75rem;
            transition: transform 0.25s var(--ease-editorial), background-color 0.25s, box-shadow 0.25s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 14px rgba(245, 216, 74, 0.4);
            text-decoration: none;
            cursor: pointer;
        }
        .btn-jis-pill-gold:hover {
            background-color: var(--wikrama-gold-hover);
            box-shadow: 0 6px 20px rgba(226, 183, 20, 0.5);
            transform: translateY(-2px);
            color: #002244 !important;
        }
        .btn-jis-pill-gold:active {
            transform: scale(0.98);
        }

        .btn-jis-pill-navy {
            background-color: var(--wikrama-navy);
            color: #FFFFFF !important;
            border: 1.5px solid var(--wikrama-navy-dark);
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.75rem 1.75rem;
            transition: transform 0.25s var(--ease-editorial), background-color 0.25s, box-shadow 0.25s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 14px rgba(0, 81, 158, 0.3);
            text-decoration: none;
            cursor: pointer;
        }
        .btn-jis-pill-navy:hover {
            background-color: var(--wikrama-navy-dark);
            box-shadow: 0 6px 20px rgba(0, 48, 95, 0.4);
            transform: translateY(-2px);
            color: #FFFFFF !important;
        }
        .btn-jis-pill-navy:active {
            transform: scale(0.98);
        }

        .btn-jis-pill-outline {
            background: #FFFFFF;
            color: var(--wikrama-body) !important;
            border: 1.5px solid var(--wikrama-border-strong);
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.75rem 1.75rem;
            transition: transform 0.25s var(--ease-editorial), border-color 0.25s, color 0.25s, background-color 0.25s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
            text-decoration: none;
            cursor: pointer;
        }
        .btn-jis-pill-outline:hover {
            border-color: var(--wikrama-navy);
            color: var(--wikrama-navy) !important;
            background: var(--wikrama-navy-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 81, 158, 0.12);
        }

        /* Remapped Legacy Button Classes to Consistent Hallmark Palette */
        .btn-google-primary {
            background-color: var(--wikrama-navy);
            background-image: linear-gradient(135deg, var(--wikrama-navy) 0%, var(--wikrama-navy-dark) 100%);
            color: #FFFFFF;
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.75rem 1.75rem;
            transition: transform 0.25s var(--ease-editorial), box-shadow 0.25s;
            box-shadow: 0 4px 14px rgba(0, 81, 158, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        .btn-google-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(0, 81, 158, 0.45);
            color: #FFFFFF;
        }
        .btn-google-primary:active { transform: scale(0.98); }

        .btn-google-blue {
            background-color: var(--wikrama-navy);
            color: #FFFFFF;
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.75rem 1.75rem;
            transition: transform 0.25s var(--ease-editorial), box-shadow 0.25s;
            box-shadow: 0 4px 14px rgba(0, 81, 158, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        .btn-google-blue:hover {
            background-color: var(--wikrama-navy-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(0, 48, 95, 0.4);
            color: #FFFFFF;
        }

        .btn-google-gold {
            background-color: var(--wikrama-gold);
            color: #00305F;
            font-weight: 800;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.75rem 1.75rem;
            transition: transform 0.25s var(--ease-editorial), box-shadow 0.25s;
            box-shadow: 0 4px 14px rgba(245, 216, 74, 0.4);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        .btn-google-gold:hover {
            background-color: var(--wikrama-gold-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(226, 183, 20, 0.5);
            color: #002244;
        }

        .btn-google-outline {
            background: #FFFFFF;
            color: var(--wikrama-body);
            border: 1.5px solid var(--wikrama-border);
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.75rem 1.65rem;
            transition: transform 0.25s var(--ease-editorial), border-color 0.25s, color 0.25s, background-color 0.25s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
            text-decoration: none;
        }
        .btn-google-outline:hover {
            border-color: var(--wikrama-navy);
            color: var(--wikrama-navy);
            background: var(--wikrama-navy-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(0, 81, 158, 0.12);
        }

        /* ── Tab Pills ── */
        .tab-pill-active {
            background-color: var(--wikrama-navy) !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 14px rgba(0, 81, 158, 0.35) !important;
        }
        .tab-pill-inactive {
            color: var(--wikrama-body) !important;
            background-color: transparent !important;
        }
        .tab-pill-inactive:hover {
            color: var(--wikrama-navy) !important;
            background-color: var(--wikrama-navy-light) !important;
        }

        /* ── Distinct Section Card Styles (Varied Modular Rhythms) ── */
        .card-bento {
            background: #FFFFFF;
            border: 1px solid var(--wikrama-border);
            border-radius: 24px;
            box-shadow: 0 4px 20px -2px rgba(0, 48, 95, 0.05);
            transition: transform 0.3s var(--ease-editorial), box-shadow 0.3s var(--ease-editorial), border-color 0.3s;
            position: relative;
        }
        .card-bento:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 81, 158, 0.35);
            box-shadow: 0 16px 36px -6px rgba(0, 48, 95, 0.12);
        }

        .card-metric {
            background: #FFFFFF;
            border: 1px solid var(--wikrama-border);
            border-radius: 22px;
            box-shadow: 0 4px 20px -2px rgba(0, 48, 95, 0.05);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s var(--ease-editorial), box-shadow 0.3s var(--ease-editorial), border-color 0.3s;
        }
        .card-metric:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 36px -6px rgba(0, 81, 158, 0.12);
            border-color: rgba(0, 81, 158, 0.35);
        }

        .card-blueprint {
            background: #FFFFFF;
            border: 1px solid var(--wikrama-border);
            border-radius: 22px;
            box-shadow: 0 4px 20px -2px rgba(0, 48, 95, 0.05);
            transition: transform 0.3s var(--ease-editorial), box-shadow 0.3s var(--ease-editorial), border-color 0.3s;
            position: relative;
        }
        .card-blueprint:hover {
            transform: translateY(-6px);
            border-color: var(--wikrama-navy);
            box-shadow: 0 16px 36px -6px rgba(0, 81, 158, 0.14);
        }

        .card-pillar-brick {
            background: #FFFFFF;
            border: 1px solid var(--wikrama-border);
            border-left: 4px solid var(--wikrama-navy);
            border-radius: 16px;
            padding: 1.15rem 1.35rem;
            transition: transform 0.25s var(--ease-editorial), box-shadow 0.25s, border-left-color 0.25s;
            box-shadow: 0 2px 10px rgba(0, 48, 95, 0.04);
        }
        .card-pillar-brick:hover {
            border-left-color: var(--wikrama-gold);
            transform: translateX(4px);
            box-shadow: 0 8px 24px rgba(0, 48, 95, 0.09);
        }

        .card-quote-bubble {
            background: #FFFFFF;
            border: 1px solid var(--wikrama-border);
            border-bottom: 3px solid var(--wikrama-navy);
            border-radius: 24px;
            padding: 1.65rem;
            box-shadow: 0 4px 20px -2px rgba(0, 48, 95, 0.05);
            transition: transform 0.3s var(--ease-editorial), box-shadow 0.3s, border-color 0.3s;
            position: relative;
        }
        .card-quote-bubble:hover {
            transform: translateY(-5px);
            border-bottom-color: var(--wikrama-gold);
            border-color: rgba(0, 81, 158, 0.3);
            box-shadow: 0 16px 36px -6px rgba(0, 48, 95, 0.1);
        }

        .card-gallery-tile {
            background: #FFFFFF;
            border: 1px solid var(--wikrama-border);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 4px 20px -2px rgba(0, 48, 95, 0.05);
            transition: transform 0.3s var(--ease-editorial), box-shadow 0.3s, border-color 0.3s;
        }
        .card-gallery-tile:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 81, 158, 0.35);
            box-shadow: 0 16px 36px -6px rgba(0, 81, 158, 0.12);
        }

        .card-editorial {
            background: #FFFFFF;
            border: 1px solid var(--wikrama-border);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 4px 20px -2px rgba(0, 48, 95, 0.05);
            transition: transform 0.3s var(--ease-editorial), box-shadow 0.3s, border-color 0.3s;
        }
        .card-editorial:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 81, 158, 0.35);
            box-shadow: 0 16px 36px -6px rgba(0, 81, 158, 0.12);
        }

        .card-calendar-slate {
            background: #FFFFFF;
            border: 1px solid var(--wikrama-border);
            border-radius: 24px;
            padding: 1.65rem;
            box-shadow: 0 4px 20px -2px rgba(0, 48, 95, 0.05);
            transition: transform 0.3s var(--ease-editorial), box-shadow 0.3s, border-color 0.3s;
            position: relative;
        }
        .card-calendar-slate:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 81, 158, 0.35);
            box-shadow: 0 16px 36px -6px rgba(0, 81, 158, 0.12);
        }

        .card-seal-crest {
            background: #FFFFFF;
            border: 1px solid var(--wikrama-border);
            border-radius: 20px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 4px 16px rgba(0, 48, 95, 0.04);
            transition: transform 0.25s var(--ease-editorial), box-shadow 0.25s, border-color 0.25s;
        }
        .card-seal-crest:hover {
            transform: translateY(-4px);
            border-color: rgba(0, 81, 158, 0.35);
            box-shadow: 0 12px 28px rgba(0, 81, 158, 0.1);
        }

        /* ── Dynamic Hero Media Ken Burns Zoom & Grow ── */
        .hero-media-scale {
            transition: transform 8000ms cubic-bezier(0.25, 1, 0.5, 1), filter 1000ms ease-out;
            will-change: transform;
            transform-origin: center center;
        }

        /* ── Dinamika Siswa Panorama Ribbon ── */
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

        .dinamika-fullwidth-wrapper {
            width: 100%;
            position: relative;
            z-index: 10;
            padding-left: 1rem;
            padding-right: 0;
        }
        @media (min-width: 640px) { .dinamika-fullwidth-wrapper { padding-left: 1.5rem; } }
        @media (min-width: 1024px) { .dinamika-fullwidth-wrapper { padding-left: 2rem; } }
        @media (min-width: 1280px) {
            .dinamika-fullwidth-wrapper {
                padding-left: max(2rem, calc((100vw - 80rem) / 2 + 2rem));
            }
        }

        .dinamika-cards-track { padding-right: 1.5rem; }
        @media (min-width: 640px) { .dinamika-cards-track { padding-right: 2rem; } }
        @media (min-width: 1280px) {
            .dinamika-cards-track { padding-right: max(2rem, calc((100vw - 80rem) / 2 + 2rem)); }
        }

        /* ── Infinite Partner Marquee ── */
        @keyframes partnerMarqueeLtoR {
            0% { transform: translate3d(-50%, 0, 0); }
            100% { transform: translate3d(0%, 0, 0); }
        }
        .partner-marquee-container {
            mask-image: linear-gradient(to right, transparent 0%, black 6%, black 94%, transparent 100%);
            -webkit-mask-image: linear-gradient(to right, transparent 0%, black 6%, black 94%, transparent 100%);
        }
        .partner-marquee-track {
            display: flex !important;
            width: max-content !important;
            animation: partnerMarqueeLtoR 32s linear infinite !important;
            will-change: transform;
        }
        .partner-marquee-track:hover {
            animation-play-state: paused !important;
        }

        .partner-card {
            transition: transform 0.25s var(--ease-editorial), box-shadow 0.25s, border-color 0.25s;
            position: relative;
            cursor: pointer;
        }
        .partner-marquee-track:hover .partner-card:not(:hover) {
            opacity: 0.55;
            filter: grayscale(70%);
            transform: scale(0.97);
        }
        .partner-card:hover {
            transform: translateY(-4px) scale(1.04) !important;
            opacity: 1 !important;
            filter: none !important;
            z-index: 30;
            background-color: #FFFFFF !important;
            border-color: var(--wikrama-navy) !important;
            box-shadow: 0 10px 24px rgba(0, 81, 158, 0.15) !important;
        }

        /* ── JIS Signature Floating Quick-Action Dock (Desktop XL+) ── */
        .hp-sticky-dock { display: none !important; }
        @media (min-width: 1280px) {
            .hp-sticky-dock {
                position: fixed;
                right: 0;
                top: 50%;
                transform: translateY(-50%);
                z-index: 45;
                display: flex !important;
                flex-direction: column;
                gap: 8px;
            }
        }
        .hp-sticky-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--wikrama-navy);
            color: #FFFFFF;
            padding: 11px 18px;
            border-top-left-radius: 9999px;
            border-bottom-left-radius: 9999px;
            box-shadow: -4px 6px 20px rgba(0, 48, 95, 0.25);
            font-size: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.3s var(--ease-editorial), background-color 0.25s, box-shadow 0.3s;
            transform: translateX(calc(100% - 46px));
        }
        .hp-sticky-btn:hover {
            transform: translateX(0);
            background: var(--wikrama-navy-dark);
            box-shadow: -8px 10px 28px rgba(0, 48, 95, 0.38);
        }
        .hp-sticky-btn.highlight {
            background: var(--wikrama-gold);
            color: #00305F;
        }
        .hp-sticky-btn.highlight:hover {
            background: var(--wikrama-gold-hover);
            color: #002244;
        }

        /* ── Natural Spring Scroll-Reveal Animations ── */
        [data-animate] {
            opacity: 0;
            transition: transform 0.7s var(--ease-editorial), opacity 0.65s var(--ease-editorial);
            will-change: transform, opacity;
        }

        [data-animate]:not([data-animate="scale"]):not([data-animate="slide-right"]):not([data-animate="slide-left"]):not([data-animate="tilt"]) {
            transform: translateY(24px);
        }
        [data-animate]:not([data-animate="scale"]):not([data-animate="slide-right"]):not([data-animate="slide-left"]):not([data-animate="tilt"]).is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        [data-animate="scale"] {
            transform: scale(0.95);
        }
        [data-animate="scale"].is-visible {
            opacity: 1;
            transform: scale(1);
        }

        [data-animate="slide-right"] {
            transform: translateX(28px);
        }
        [data-animate="slide-right"].is-visible {
            opacity: 1;
            transform: translateX(0);
        }

        [data-animate="slide-left"] {
            transform: translateX(-28px);
        }
        [data-animate="slide-left"].is-visible {
            opacity: 1;
            transform: translateX(0);
        }

        [data-animate="tilt"] {
            transform: perspective(800px) rotateX(6deg) translateY(16px);
        }
        [data-animate="tilt"].is-visible {
            opacity: 1;
            transform: perspective(800px) rotateX(0deg) translateY(0);
        }

        [data-animate-delay="1"] { transition-delay: 0.06s !important; }
        [data-animate-delay="2"] { transition-delay: 0.12s !important; }
        [data-animate-delay="3"] { transition-delay: 0.18s !important; }
        [data-animate-delay="4"] { transition-delay: 0.24s !important; }
        [data-animate-delay="5"] { transition-delay: 0.30s !important; }

        .group-hover-arrow {
            transition: transform 0.2s var(--ease-editorial);
        }
        .group:hover .group-hover-arrow {
            transform: translateX(4px);
        }

        /* ── SVG Botanical Vine Line Drawing ── */
        .jis-vine-path {
            stroke-dasharray: 1200;
            stroke-dashoffset: 1200;
            transition: stroke-dashoffset 0.1s linear;
        }

        /* ── Instant Vanilla Scroll Fallback for Solid Header Contrast ── */
        #main-nav-container.is-scrolled {
            background-color: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(14px) !important;
            -webkit-backdrop-filter: blur(14px) !important;
            border-bottom: 1px solid rgba(226, 232, 240, 0.9) !important;
            box-shadow: 0 10px 30px -5px rgba(0, 48, 95, 0.08) !important;
        }
        #main-nav-container.is-scrolled a.nav-link {
            color: #1E293B !important;
        }
        #main-nav-container.is-scrolled a.nav-link:hover {
            color: var(--wikrama-navy) !important;
        }
        #main-nav-container.is-scrolled .nav-brand-title {
            color: var(--wikrama-heading) !important;
        }

        /* ── Reduced Motion Fallback (Hallmark Floor Rule 7) ── */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
            [data-animate] {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
            .partner-marquee-track {
                animation: none !important;
            }
        }
    </style>
    @livewireStyles
</head>
<body class="min-h-screen text-slate-800 antialiased flex flex-col justify-between relative bg-white"
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

    <!-- ── High-Performance Scroll-Reveal & Botanical Interactions Script ── -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {

        // ── 1. Guaranteed Instant Vanilla Navbar Scroll Fallback ──
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

        // ── 2. Natural Spring Scroll-Reveal via IntersectionObserver ──
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

            // Content safety insurance: ensure visibility after 1s
            setTimeout(() => {
                animEls.forEach(el => el.classList.add('is-visible'));
            }, 1000);
        } else {
            animEls.forEach(el => el.classList.add('is-visible'));
        }

        // ── 3. Botanical Vine SVG Scroll Animation ──
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

        // ── 4. Animated Stats Counter ──
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