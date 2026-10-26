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
    <title>{{ $title ?? 'Portal Berita & Informasi' }} &bull; {{ $schoolName }}</title>
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    
    <!-- Google Fonts: Plus Jakarta Sans & Roboto Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #FFFFFF;
            color: #1E293B;
        }

        /* ── High-Contrast Global Text Selection ── */
        ::selection, *::selection {
            background-color: #00519E !important;
            color: #FFFFFF !important;
        }
        ::-moz-selection, *::-moz-selection {
            background-color: #00519E !important;
            color: #FFFFFF !important;
        }

        /* ── JIS Signature Pill Buttons ── */
        .btn-jis-pill-gold {
            background-color: #F5D84A;
            color: #232327 !important;
            border: 1.5px solid #E2B714;
            font-weight: 800;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.6rem 1.4rem;
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

        .btn-jis-pill-navy {
            background-color: #00519E;
            color: #FFFFFF !important;
            border: 1.5px solid #00305F;
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.6rem 1.4rem;
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
            background-color: #003D7A;
            box-shadow: 0 8px 24px rgba(0, 81, 158, 0.45);
            transform: translateY(-2px);
            color: #FFFFFF !important;
        }

        .btn-jis-pill-outline {
            background-color: transparent;
            color: #00519E !important;
            border: 1.5px solid #00519E;
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.6rem 1.4rem;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-jis-pill-outline:hover {
            background-color: #EBF4FC;
            color: #00305F !important;
            transform: translateY(-2px);
        }
    </style>
    <script>
        document.documentElement.classList.remove('dark');
    </script>
</head>
<body class="min-h-screen text-slate-800 antialiased flex flex-col justify-between relative bg-[#FFFFFF]"
      x-data="{ 
          scrolled: true,
          mobileMenu: false
      }">

    @include('landing.sections.navigation')

    <div class="pt-20 sm:pt-24 flex-1">
        {{ $slot }}
    </div>

    @include('landing.sections.footer')

    @livewireScripts
</body>
</html>
