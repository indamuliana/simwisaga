{{--
    ================================================================================
    HALAMAN DETAIL JURUSAN: PERHOTELAN / HOSPITALITY (HTL)
    Gaya Desain: Haven Warm Editorial Hospitality — Linen, Olive/Sage, Warm Espresso & Serif
    URL Akses: /jurusan/htl (alias: /jurusan/perhotelan)
    ================================================================================
--}}
<x-layouts.blank>
    @php
        $sekolah = $sekolah ?? \App\Modules\Sistem\Models\School::current();
        $schoolName = $sekolah->nama ?? 'SMK Wikrama 1 Garut';

        $facilities = [
            [
                'num' => '01',
                'tag' => 'Mock-up Suite Room',
                'title' => 'Wikrama Hotel Mock-up Room',
                'desc' => 'Kamar mock-up lengkap dengan king size bed, wardrobe, minibar, linen set, dan kamar mandi standar bintang untuk simulasi making bed dan turndown service.',
                'img' => asset('images/landing/budaya-belajar-bertiga.jpeg'),
                'pos' => '50% 24%',
            ],
            [
                'num' => '02',
                'tag' => 'Front Desk & PMS Lab',
                'title' => 'Front Office Reception Desk',
                'desc' => 'Meja resepsionis hotel lengkap dengan sistem telepon PABX, kunci kamar RFID, komputer terminal simulasi PMS VHP dan reservasi tamu online.',
                'img' => asset('images/landing/jurusan-hotel.png'),
                'pos' => '50% 20%',
            ],
            [
                'num' => '03',
                'tag' => 'Barista & Coffee Corner',
                'title' => 'Wikrama Coffee Corner & Cafe',
                'desc' => 'Dilengkapi mesin espresso komersial 2-group, grinder profesional, peralatan manual brew V60, french press, dan area bar taman untuk melayani tamu.',
                'img' => asset('images/web/pilar/pilar-tefa.jpg'),
                'pos' => '50% 58%',
            ],
            [
                'num' => '04',
                'tag' => 'Dining & Banquet',
                'title' => 'Table Manner & Banquet Dining Hall',
                'desc' => 'Area jamuan makan resmi untuk praktik perjamuan kenegaraan, napkin folding, cutleries arrangement, serta French & American service method.',
                'img' => asset('images/landing/prestasi-seminar-siswa.jpg'),
                'pos' => '50% 45%',
            ],
            [
                'num' => '05',
                'tag' => 'Linen & Laundry',
                'title' => 'Professional Linen & Laundry Center',
                'desc' => 'Mesin cuci kapasitas industri, flatwork ironer, dry cleaning equipment, dan sistem penataan inventaris sprei, selimut, serta seragam hotelier.',
                'img' => asset('images/landing/gedung-sekolah.jpg'),
                'pos' => '50% 50%',
            ],
            [
                'num' => '06',
                'tag' => 'Grooming & Manner',
                'title' => 'Grooming & Personal Attitude Studio',
                'desc' => 'Cermin full-length, tata rias profesional, panduan hairdo, hijab styling, dan etika senyum 3S (Senyum, Salam, Sapa) berstandar hotelier internasional.',
                'img' => asset('images/landing/budaya-lulusan.jpg'),
                'pos' => '50% 22%',
            ],
        ];

        $galleryItems = [
            [
                'src' => asset('images/landing/budaya-belajar-bertiga.jpeg'),
                'title' => 'Standar Grooming & Salam Hospitality',
                'caption' => 'Pembiasaan sikap ramah, postur elegan, dan kerapian seragam hotelier bintang lima.',
                'pos' => '50% 22%',
            ],
            [
                'src' => asset('images/web/pilar/pilar-tefa.jpg'),
                'title' => 'Suasana Senja Wikrama Coffee Corner',
                'caption' => 'Area terbuka asri tempat siswa melatih layanan cafe, barista, dan penerimaan tamu.',
                'pos' => '50% 58%',
            ],
            [
                'src' => asset('images/landing/jurusan-hotel.png'),
                'title' => 'Praktik Front Office & Resepsionis',
                'caption' => 'Simulasi penyambutan tamu VIP, PABX, dan pengoperasian Property Management System.',
                'pos' => '50% 20%',
            ],
            [
                'src' => asset('images/landing/prestasi-seminar-siswa.jpg'),
                'title' => 'Lingkungan Kampus & TEFA Wikrama',
                'caption' => 'Gedung pembelajaran dan unit Teaching Factory di atmosfer sejuk Garut.',
                'pos' => '50% 45%',
            ],
            [
                'src' => asset('images/landing/gedung-sekolah.jpg'),
                'title' => 'Taman Hijau & Eco-Hospitality Campus',
                'caption' => 'Lingkungan belajar yang tertata rapi menumbuhkan kepekaan estetika dan kebersihan.',
                'pos' => '50% 50%',
            ],
            [
                'src' => asset('images/landing/budaya-lulusan.jpg'),
                'title' => 'Prestasi & Karakter Lulusan',
                'caption' => 'Integritas islami berpadu dengan kecakapan pelayanan berstandar industri.',
                'pos' => '50% 20%',
            ],
        ];

        $faqs = [
            [
                'num' => '01',
                'q' => 'Apakah ada syarat tinggi badan minimal untuk masuk Perhotelan?',
                'a' => 'Tidak ada syarat mutlak tinggi badan yang membatasi. Yang paling utama adalah kesehatan jasmani, kesopanan sikap, komitmen menjaga kerapian (grooming), dan kemauan untuk belajar melayani dengan tulus hati.',
            ],
            [
                'num' => '02',
                'q' => 'Apakah harus sudah lancar bahasa Inggris sebelum mendaftar?',
                'a' => 'Tidak harus. Selama bersekolah di Wikrama, siswa akan dibimbing dari dasar melalui mata pelajaran English for Hospitality secara interaktif dan menyenangkan hingga percaya diri berkomunikasi dengan tamu asing.',
            ],
            [
                'num' => '03',
                'q' => 'Di mana saja lokasi magang/PKL siswa jurusan Perhotelan?',
                'a' => 'Siswa ditempatkan di hotel berbintang 4 dan 5 di wilayah Garut, Bandung, Bogor, hingga Jakarta seperti Aston, Santika, favehotel, dan grup perhotelan nasional lainnya selama 6 bulan penuh.',
            ],
            [
                'num' => '04',
                'q' => 'Apakah lulusan Perhotelan bisa melanjutkan kuliah ke Perguruan Tinggi?',
                'a' => 'Tentu saja bisa! Lulusan dapat melanjutkan kuliah ke Sekolah Tinggi Pariwisata (STP/NHI Bandung), Politeknik Pariwisata, atau jurusan Manajemen Perhotelan, Pariwisata, dan Bisnis di kampus negeri maupun swasta.',
            ],
        ];
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&display=swap" rel="stylesheet">

    <style>
        .htl-haven {
            --hv-linen: #F6F4EF;
            --hv-paper: #FBFBF8;
            --hv-stone: #EFECE4;
            --hv-sage: #E7EAE0;
            --hv-olive: #586346;
            --hv-olive-dark: #434C34;
            --hv-espresso: #1B1713;
            --hv-espresso-soft: #29231D;
            --hv-ink: #1F1D19;
            --hv-muted: #625E55;
            --hv-subtle: #8C867B;
            --hv-line: #DCD7CC;
            --hv-gold: #B69A6B;
            background-color: var(--hv-linen);
            color: var(--hv-ink);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            overflow-x: clip;
        }

        .htl-serif {
            font-family: 'Cormorant Garamond', Georgia, 'Times New Roman', serif;
            font-weight: 500;
            letter-spacing: -0.02em;
        }

        .htl-wrap {
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 20px;
            padding-right: 20px;
        }
        @media (min-width: 640px) {
            .htl-wrap { padding-left: 28px; padding-right: 28px; }
        }
        @media (min-width: 1024px) {
            .htl-wrap { padding-left: 36px; padding-right: 36px; }
        }

        .htl-photo {
            filter: saturate(0.92) contrast(1.03) sepia(0.08);
            transition: transform 0.65s cubic-bezier(0.22, 1, 0.36, 1), filter 0.45s ease;
        }
        .htl-photo-hover:hover .htl-photo {
            transform: scale(1.045);
            filter: saturate(0.98) contrast(1.05) sepia(0.04);
        }

        /* ── 1. HERO (HAVEN ATMOSPHERIC ROUNDED BANNER) ── */
        .htl-hero-outer {
            padding-top: 20px;
            padding-bottom: 24px;
            background-color: var(--hv-linen);
        }
        .htl-hero-frame {
            position: relative;
            border-radius: 26px;
            overflow: hidden;
            min-height: 540px;
            background-color: var(--hv-espresso);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 24px 60px -30px rgba(27, 23, 19, 0.42);
        }
        @media (min-width: 1024px) {
            .htl-hero-frame { min-height: 590px; border-radius: 30px; }
        }
        .htl-hero-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: 50% 56%;
            filter: saturate(0.84) contrast(1.05) brightness(0.76) sepia(0.10);
        }
        .htl-hero-scrim {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(102deg, rgba(22, 20, 16, 0.84) 0%, rgba(28, 26, 20, 0.56) 48%, rgba(22, 20, 16, 0.36) 100%),
                linear-gradient(180deg, rgba(20, 18, 15, 0.46) 0%, rgba(20, 18, 15, 0.08) 42%, rgba(20, 18, 15, 0.52) 100%);
        }
        .htl-hero-inner {
            position: relative;
            z-index: 2;
            padding: 28px 24px 36px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 36px;
        }
        @media (min-width: 768px) {
            .htl-hero-inner { padding: 34px 44px 48px; }
        }
        @media (min-width: 1024px) {
            .htl-hero-inner { padding: 38px 56px 56px; }
        }
        .htl-hero-top {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .htl-crumb {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: rgba(246, 244, 239, 0.72);
            letter-spacing: 0.02em;
        }
        .htl-crumb a {
            color: rgba(246, 244, 239, 0.76);
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .htl-crumb a:hover { color: #FFFFFF; }
        .htl-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #F6F4EF;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(246, 244, 239, 0.24);
            backdrop-filter: blur(8px);
        }
        .htl-hero-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 32px;
            align-items: end;
        }
        @media (min-width: 1024px) {
            .htl-hero-grid {
                grid-template-columns: 1.25fr 0.75fr;
                align-items: center;
                gap: 40px;
            }
        }
        .htl-hero-title {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-weight: 500;
            font-size: clamp(2.8rem, 5.4vw, 4.7rem);
            line-height: 0.98;
            letter-spacing: -0.02em;
            color: #FAF8F4;
            margin: 0;
        }
        .htl-hero-desc {
            margin-top: 18px;
            max-width: 470px;
            font-size: 14px;
            line-height: 1.72;
            color: rgba(246, 244, 239, 0.84);
        }
        .htl-hero-actions {
            margin-top: 28px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 14px;
        }
        .htl-btn-olive {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 8px 10px 8px 22px;
            border-radius: 999px;
            background-color: var(--hv-olive);
            color: #FAF8F4;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.25s ease, transform 0.25s ease;
        }
        .htl-btn-olive:hover {
            background-color: var(--hv-olive-dark);
            transform: translateY(-1px);
        }
        .htl-btn-olive-circle {
            width: 30px;
            height: 30px;
            border-radius: 999px;
            background-color: rgba(250, 248, 244, 0.92);
            color: var(--hv-espresso);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .htl-btn-ghost-light {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 999px;
            border: 1px solid rgba(246, 244, 239, 0.34);
            color: #FAF8F4;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(6px);
            transition: background-color 0.25s ease, border-color 0.25s ease;
        }
        .htl-btn-ghost-light:hover {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(246, 244, 239, 0.62);
        }

        /* Haven Circular Radial Orbit Play Badge */
        .htl-orbit-col {
            display: flex;
            justify-content: center;
            padding-top: 8px;
            padding-bottom: 14px;
        }
        @media (min-width: 1024px) {
            .htl-orbit-col {
                justify-content: flex-end;
                padding-top: 0;
                padding-bottom: 0;
                padding-right: 68px;
            }
        }
        .htl-orbit-wrap {
            position: relative;
            width: 204px;
            height: 204px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background: transparent;
            border: 0;
            padding: 0;
        }
        .htl-orbit-ring {
            position: absolute;
            inset: 14px;
            border-radius: 999px;
            border: 1px solid rgba(250, 248, 244, 0.42);
        }
        .htl-orbit-dot {
            position: absolute;
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: #FAF8F4;
        }
        .htl-orbit-dot--t { top: 11px; left: 50%; transform: translateX(-50%); }
        .htl-orbit-dot--r { right: 11px; top: 50%; transform: translateY(-50%); }
        .htl-orbit-dot--b { bottom: 11px; left: 50%; transform: translateX(-50%); }
        .htl-orbit-dot--l { left: 11px; top: 50%; transform: translateY(-50%); }

        .htl-orbit-label {
            position: absolute;
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 0.04em;
            color: rgba(250, 248, 244, 0.88);
            white-space: nowrap;
        }
        .htl-orbit-label--t { top: -8px; left: 50%; transform: translateX(-50%); }
        .htl-orbit-label--b { bottom: -8px; left: 50%; transform: translateX(-50%); }
        .htl-orbit-label--l { right: calc(100% + 4px); top: 50%; transform: translateY(-50%); display: none; }
        .htl-orbit-label--r { left: calc(100% + 4px); top: 50%; transform: translateY(-50%); display: none; }
        @media (min-width: 640px) {
            .htl-orbit-label--l, .htl-orbit-label--r { display: block; }
        }
        .htl-orbit-thumb {
            position: relative;
            width: 112px;
            height: 112px;
            border-radius: 999px;
            overflow: hidden;
            border: 2px solid rgba(250, 248, 244, 0.72);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
            transition: transform 0.35s ease;
        }
        .htl-orbit-wrap:hover .htl-orbit-thumb { transform: scale(1.06); }
        .htl-orbit-play {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(27, 23, 19, 0.34);
            color: #FAF8F4;
        }
        .htl-orbit-play-circle {
            width: 40px;
            height: 40px;
            border-radius: 999px;
            background: rgba(250, 248, 244, 0.92);
            color: var(--hv-espresso);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.25s ease;
        }
        .htl-orbit-wrap:hover .htl-orbit-play-circle { transform: scale(1.08); }

        /* ── 2. BAND 2: 01-03 PILLAR CARDS + ABOUT HAVEN + STATS ── */
        .htl-sec {
            padding-top: 56px;
            padding-bottom: 56px;
        }
        @media (min-width: 1024px) {
            .htl-sec { padding-top: 76px; padding-bottom: 76px; }
        }
        .htl-about-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 36px;
            align-items: center;
        }
        @media (min-width: 1024px) {
            .htl-about-grid {
                grid-template-columns: 1.45fr 1fr;
                gap: 44px;
            }
        }
        .htl-pillar-strip {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }
        @media (min-width: 640px) {
            .htl-pillar-strip { grid-template-columns: repeat(3, 1fr); }
        }
        .htl-pillar-card {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            aspect-ratio: 4 / 5;
            background: var(--hv-espresso);
        }
        @media (max-width: 639px) {
            .htl-pillar-card { aspect-ratio: 16 / 10; }
        }
        .htl-pillar-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .htl-pillar-grad {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(27, 23, 19, 0.04) 36%, rgba(27, 23, 19, 0.84) 100%);
        }
        .htl-pillar-meta {
            position: absolute;
            left: 16px;
            right: 16px;
            bottom: 16px;
            color: #FAF8F4;
        }
        .htl-pillar-num {
            display: block;
            font-size: 11px;
            letter-spacing: 0.14em;
            color: rgba(250, 248, 244, 0.72);
            margin-bottom: 4px;
        }
        .htl-pillar-title {
            font-size: 14px;
            font-weight: 600;
            line-height: 1.3;
            color: #FAF8F4;
        }
        .htl-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--hv-olive);
        }
        .htl-about-heading {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-weight: 500;
            font-size: clamp(1.85rem, 3vw, 2.5rem);
            line-height: 1.12;
            color: var(--hv-ink);
            margin-top: 10px;
        }
        .htl-about-copy {
            margin-top: 14px;
            font-size: 14px;
            line-height: 1.75;
            color: var(--hv-muted);
        }
        .htl-trust-row {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--hv-line);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 18px;
        }
        .htl-avatars {
            display: flex;
            align-items: center;
        }
        .htl-avatars img {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            object-fit: cover;
            border: 2px solid var(--hv-linen);
            margin-left: -10px;
        }
        .htl-avatars img:first-child { margin-left: 0; }
        .htl-stats-bar {
            margin-top: 44px;
            padding-top: 28px;
            border-top: 1px solid var(--hv-line);
            border-bottom: 1px solid var(--hv-line);
            padding-bottom: 28px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }
        @media (min-width: 768px) {
            .htl-stats-bar {
                grid-template-columns: repeat(4, 1fr);
                gap: 0;
            }
            .htl-stat-cell {
                padding: 0 24px;
                border-right: 1px solid var(--hv-line);
            }
            .htl-stat-cell:first-child { padding-left: 0; }
            .htl-stat-cell:last-child { border-right: 0; padding-right: 0; }
        }
        .htl-stat-num {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: clamp(2.1rem, 3.2vw, 2.75rem);
            font-weight: 600;
            line-height: 1;
            color: var(--hv-ink);
        }
        .htl-stat-lbl {
            margin-top: 6px;
            font-size: 12px;
            font-weight: 500;
            color: var(--hv-muted);
        }

        /* ── 3. ASYMMETRIC EDITORIAL FACILITY CAROUSEL (HAVEN BAND 3) ── */
        .htl-carousel-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 32px;
            align-items: center;
        }
        @media (min-width: 1024px) {
            .htl-carousel-grid {
                grid-template-columns: 0.92fr 1.65fr;
                gap: 44px;
            }
        }
        .htl-haven .gsap-word-mask {
            padding-bottom: 0.14em;
            margin-bottom: -0.14em;
        }
        .htl-sec-h2 {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-weight: 500;
            font-size: clamp(2.1rem, 3.8vw, 3.1rem);
            line-height: 1.16;
            letter-spacing: -0.02em;
            color: var(--hv-ink);
            margin: 0;
        }
        .htl-circle-btn {
            width: 42px;
            height: 42px;
            border-radius: 999px;
            border: 1px solid var(--hv-line);
            background: var(--hv-paper);
            color: var(--hv-ink);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }
        .htl-circle-btn:hover {
            background: var(--hv-olive);
            border-color: var(--hv-olive);
            color: #FAF8F4;
        }
        .htl-asym-strip {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
        }
        @media (min-width: 768px) {
            .htl-asym-strip {
                grid-template-columns: 1.85fr 1.05fr 0.52fr;
                gap: 16px;
                height: 360px;
            }
        }
        .htl-asym-card {
            position: relative;
            border-radius: 22px;
            overflow: hidden;
            background: var(--hv-espresso);
            cursor: pointer;
            height: 270px;
        }
        @media (min-width: 768px) {
            .htl-asym-card { height: 100%; }
        }
        .htl-asym-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .htl-asym-caption {
            position: absolute;
            left: 16px;
            right: 16px;
            bottom: 16px;
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(27, 23, 19, 0.62);
            backdrop-filter: blur(8px);
            color: #FAF8F4;
        }

        /* ── 4. DARK ESPRESSO SHOWCASE + CRISP WHITE PMS CARD (HAVEN BAND 4) ── */
        .htl-dark-card {
            position: relative;
            border-radius: 28px;
            overflow: hidden;
            background-color: var(--hv-espresso);
            color: #FAF8F4;
            padding: 24px;
        }
        @media (min-width: 768px) {
            .htl-dark-card { padding: 36px; border-radius: 30px; }
        }
        @media (min-width: 1024px) {
            .htl-dark-card { padding: 44px; }
        }
        .htl-dark-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: 60% 50%;
            opacity: 0.28;
            filter: saturate(0.75) sepia(0.18) brightness(0.65);
        }
        .htl-dark-scrim {
            position: absolute;
            inset: 0;
            background: linear-gradient(96deg, rgba(24, 20, 16, 0.94) 0%, rgba(27, 23, 19, 0.80) 52%, rgba(24, 20, 16, 0.72) 100%);
        }
        .htl-dark-grid {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1fr;
            gap: 32px;
            align-items: stretch;
        }
        @media (min-width: 1024px) {
            .htl-dark-grid {
                grid-template-columns: 0.92fr 1.18fr;
                gap: 44px;
            }
        }
        .htl-pms-white {
            background-color: #FFFFFF;
            color: var(--hv-ink);
            border-radius: 22px;
            padding: 24px;
            box-shadow: 0 22px 48px rgba(0, 0, 0, 0.34);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 18px;
        }
        .htl-pms-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 9px 0;
            border-bottom: 1px solid #ECEAE3;
            font-size: 12.5px;
        }
        .htl-pms-row:last-child { border-bottom: 0; }
        .htl-pms-btn {
            width: 100%;
            padding: 13px 18px;
            border-radius: 12px;
            border: 0;
            background-color: var(--hv-espresso);
            color: #FAF8F4;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background-color 0.2s ease, transform 0.15s ease;
        }
        .htl-pms-btn:hover { background-color: var(--hv-olive); }
        .htl-pms-btn:active { transform: scale(0.985); }
        .htl-dark-right {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 24px;
            padding: 6px 0;
        }
        .htl-dark-h2 {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-weight: 500;
            font-size: clamp(2.15rem, 3.8vw, 3.2rem);
            line-height: 1.06;
            color: #FAF8F4;
            margin: 0;
        }
        .htl-tefa-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .htl-tefa-pill {
            padding: 7px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid rgba(250, 248, 244, 0.24);
            background: transparent;
            color: rgba(250, 248, 244, 0.78);
            transition: all 0.2s ease;
        }
        .htl-tefa-pill.is-active {
            background: var(--hv-olive);
            border-color: var(--hv-olive);
            color: #FAF8F4;
        }

        /* ── 5. EDITORIAL CAREERS GRID (BORDERLESS HAIRLINE RULES) ── */
        .htl-career-grid {
            display: grid;
            grid-template-columns: 1fr;
            border-top: 1px solid var(--hv-line);
        }
        @media (min-width: 768px) {
            .htl-career-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (min-width: 1024px) {
            .htl-career-grid { grid-template-columns: repeat(3, 1fr); }
        }
        .htl-career-cell {
            padding: 28px 0;
            border-bottom: 1px solid var(--hv-line);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 14px;
        }
        @media (min-width: 768px) {
            .htl-career-cell { padding: 30px 26px; }
        }
        @media (min-width: 1024px) {
            .htl-career-cell { border-right: 1px solid var(--hv-line); }
            .htl-career-cell:nth-child(3n) { border-right: 0; }
            .htl-career-cell:nth-child(3n + 1) { padding-left: 0; }
            .htl-career-cell:nth-child(3n) { padding-right: 0; }
        }

        /* ── 6. CURRICULUM 3 YEARS (SOFT STONE BAND) ── */
        .htl-stone-band {
            background-color: var(--hv-stone);
            border-top: 1px solid var(--hv-line);
            border-bottom: 1px solid var(--hv-line);
        }
        .htl-curr-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 22px;
        }
        @media (min-width: 1024px) {
            .htl-curr-grid { grid-template-columns: repeat(3, 1fr); gap: 24px; }
        }
        .htl-curr-card {
            background-color: var(--hv-paper);
            border: 1px solid var(--hv-line);
            border-radius: 22px;
            padding: 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .htl-curr-card--featured {
            border-top: 3px solid var(--hv-olive);
        }

        /* ── 7. CERTIFICATIONS & 8. PARTNERS ── */
        .htl-cert-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 28px;
        }
        @media (min-width: 1024px) {
            .htl-cert-grid { grid-template-columns: repeat(3, 1fr); gap: 36px; }
        }
        .htl-cert-item {
            padding-top: 22px;
            border-top: 1px solid var(--hv-ink);
        }
        .htl-partner-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            border-top: 1px solid var(--hv-line);
            border-bottom: 1px solid var(--hv-line);
        }
        @media (min-width: 768px) {
            .htl-partner-grid { grid-template-columns: repeat(3, 1fr); }
        }
        @media (min-width: 1024px) {
            .htl-partner-grid { grid-template-columns: repeat(6, 1fr); }
        }
        .htl-partner-cell {
            padding: 22px 14px;
            text-align: center;
            border-right: 1px solid var(--hv-line);
            border-bottom: 1px solid var(--hv-line);
        }
        @media (min-width: 1024px) {
            .htl-partner-cell { border-bottom: 0; }
            .htl-partner-cell:last-child { border-right: 0; }
        }

        /* ── 9. TESTIMONIAL GUESTBOOK ── */
        .htl-quote-box {
            background-color: var(--hv-paper);
            border: 1px solid var(--hv-line);
            border-radius: 26px;
            padding: 28px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 28px;
            align-items: center;
        }
        @media (min-width: 768px) {
            .htl-quote-box {
                grid-template-columns: 200px 1fr;
                padding: 40px 44px;
                gap: 40px;
            }
        }
        .htl-quote-text {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-style: italic;
            font-weight: 500;
            font-size: clamp(1.35rem, 2.2vw, 1.75rem);
            line-height: 1.42;
            color: var(--hv-ink);
        }

        /* ── 10. SPLIT EDITORIAL NUMBERED FAQ (HAVEN BAND 5) ── */
        .htl-faq-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 36px;
            align-items: start;
        }
        @media (min-width: 1024px) {
            .htl-faq-grid {
                grid-template-columns: 0.85fr 1.35fr;
                gap: 56px;
            }
        }
        .htl-faq-item {
            border-top: 1px solid var(--hv-line);
        }
        .htl-faq-item:last-child {
            border-bottom: 1px solid var(--hv-line);
        }
        .htl-faq-trigger {
            width: 100%;
            background: transparent;
            border: 0;
            padding: 22px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            text-align: left;
            cursor: pointer;
            color: var(--hv-ink);
        }
        .htl-faq-plus {
            width: 30px;
            height: 30px;
            border-radius: 999px;
            border: 1px solid var(--hv-line);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: var(--hv-muted);
            transition: all 0.2s ease;
        }
        .htl-faq-trigger[aria-expanded="true"] .htl-faq-plus {
            background-color: var(--hv-olive);
            border-color: var(--hv-olive);
            color: #FAF8F4;
        }

        /* ── 11. SOFT SAGE BENTO GALLERY + SERENE CTA (HAVEN BAND 6) ── */
        .htl-sage-box {
            background-color: var(--hv-sage);
            border-radius: 28px;
            padding: 40px 22px;
        }
        @media (min-width: 768px) {
            .htl-sage-box { padding: 52px 36px; border-radius: 32px; }
        }
        @media (min-width: 1024px) {
            .htl-sage-box { padding: 56px 44px; }
        }
        .htl-gallery-layout {
            margin-top: 32px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            align-items: center;
        }
        @media (min-width: 1024px) {
            .htl-gallery-layout {
                grid-template-columns: 140px 1fr 160px;
                gap: 28px;
            }
        }
        .htl-bento-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        @media (min-width: 768px) {
            .htl-bento-grid {
                grid-template-columns: 1.05fr 1fr 1fr;
                grid-template-rows: 175px 175px;
                gap: 14px;
            }
        }
        .htl-bento-tile {
            position: relative;
            border-radius: 18px;
            overflow: hidden;
            background: var(--hv-espresso);
            cursor: pointer;
            border: 0;
            padding: 0;
            height: 170px;
        }
        @media (min-width: 768px) {
            .htl-bento-tile { height: auto; }
            .htl-bento-tile--tall { grid-row: span 2; }
        }
        .htl-bento-tile img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .htl-outline-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 24px;
            border-radius: 999px;
            border: 1px solid rgba(31, 29, 25, 0.34);
            background: transparent;
            color: var(--hv-ink);
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }
        .htl-outline-pill:hover {
            background-color: var(--hv-ink);
            border-color: var(--hv-ink);
            color: #FAF8F4;
        }
        .htl-haven [x-cloak] { display: none !important; }
        .htl-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 70;
            background: rgba(20, 17, 14, 0.84);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
    </style>

    <div
        class="htl-haven"
        x-data="{
            spotlightOpen: false,
            lightboxIdx: null,
            gallery: @js($galleryItems),
            openLightbox(i) { this.lightboxIdx = i; },
            closeLightbox() { this.lightboxIdx = null; },
            prevLightbox() { this.lightboxIdx = (this.lightboxIdx - 1 + this.gallery.length) % this.gallery.length; },
            nextLightbox() { this.lightboxIdx = (this.lightboxIdx + 1) % this.gallery.length; }
        }"
        @keydown.escape.window="spotlightOpen = false; closeLightbox()"
    >
        <!-- ── 1. HERO SECTION: HAVEN ATMOSPHERIC ROUNDED BANNER ── -->
        <header class="htl-hero-outer">
            <div class="htl-wrap">
                <div class="htl-hero-frame">
                    <img
                        src="{{ asset('images/web/pilar/pilar-tefa.jpg') }}"
                        alt="Suasana Senja Kampus & Area Teaching Factory Perhotelan {{ $schoolName }}"
                        class="htl-hero-bg"
                        fetchpriority="high"
                    >
                    <div class="htl-hero-scrim" aria-hidden="true"></div>

                    <div class="htl-hero-inner">
                        <!-- Top Row: Breadcrumb & Accreditation Pill -->
                        <div class="htl-hero-top">
                            <nav class="htl-crumb" aria-label="Breadcrumb">
                                <a href="{{ url('/') }}">Beranda</a>
                                <span>&rsaquo;</span>
                                <a href="{{ url('/jurusan') }}">Jurusan</a>
                                <span>&rsaquo;</span>
                                <span style="color:#FAF8F4; font-weight:500;">Perhotelan (HTL)</span>
                            </nav>

                            <span class="htl-hero-badge">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c-4 3.5-6 7.5-6 11a6 6 0 0012 0c0-3.5-2-7.5-6-11z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v11"/>
                                </svg>
                                <span>Hospitality &bull; Barista Professional &bull; Terakreditasi A</span>
                            </span>
                        </div>

                        <!-- Bottom Split: Editorial Serif Headline + Radial Preview Badge -->
                        <div class="htl-hero-grid">
                            <div>
                                <h1 class="htl-hero-title">
                                    Keramahan.<br>
                                    Elegansi. Anda.
                                </h1>
                                <p class="htl-hero-desc">
                                    Mendidik insan perhotelan yang berakhlak mulia, berwawasan global, dan berstandar internasional bintang 5 &mdash; menguasai Front Office PMS, Executive Housekeeping, F&amp;B Service, serta seni racik Barista modern.
                                </p>

                                <div class="htl-hero-actions">
                                    <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="htl-btn-olive">
                                        <span>Daftar Perhotelan</span>
                                        <span class="htl-btn-olive-circle" aria-hidden="true">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6l6 6-6 6"/>
                                            </svg>
                                        </span>
                                    </a>
                                    <a href="#kurikulum" class="htl-btn-ghost-light">
                                        <span>Lihat Kurikulum &amp; Fasilitas</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Haven Circular Radial Preview Emblem -->
                            <div class="htl-orbit-col">
                                <button
                                    type="button"
                                    class="htl-orbit-wrap"
                                    @click="spotlightOpen = true"
                                    aria-label="Buka sorotan profil jurusan Perhotelan"
                                >
                                    <span class="htl-orbit-ring" aria-hidden="true"></span>
                                    <span class="htl-orbit-dot htl-orbit-dot--t" aria-hidden="true"></span>
                                    <span class="htl-orbit-dot htl-orbit-dot--r" aria-hidden="true"></span>
                                    <span class="htl-orbit-dot htl-orbit-dot--b" aria-hidden="true"></span>
                                    <span class="htl-orbit-dot htl-orbit-dot--l" aria-hidden="true"></span>

                                    <span class="htl-orbit-label htl-orbit-label--t">Front Office PMS</span>
                                    <span class="htl-orbit-label htl-orbit-label--r">Barista Art</span>
                                    <span class="htl-orbit-label htl-orbit-label--b">Table Manner 5-Star</span>
                                    <span class="htl-orbit-label htl-orbit-label--l">Executive Suite</span>

                                    <span class="htl-orbit-thumb">
                                        <img
                                            src="{{ asset('images/landing/jurusan-hotel.png') }}"
                                            alt="Siswa Perhotelan SMK Wikrama 1 Garut"
                                            style="width:100%; height:100%; object-fit:cover; object-position:50% 20%;"
                                        >
                                        <span class="htl-orbit-play">
                                            <span class="htl-orbit-play-circle">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                    <path d="M8 5.14v14l11-7-11-7z"/>
                                                </svg>
                                            </span>
                                        </span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <!-- ── 2. 01-03 PILLAR PHOTO CARDS + ABOUT PERHOTELAN & STATS ── -->
            <section class="htl-sec" aria-labelledby="htl-about-title">
                <div class="htl-wrap">
                    <div class="htl-about-grid">
                        <!-- Left: 3 Numbered Photo Pillar Cards (Haven 01, 02, 03) -->
                        <div class="htl-pillar-strip">
                            <article class="htl-pillar-card htl-photo-hover">
                                <img
                                    src="{{ asset('images/landing/jurusan-hotel.png') }}"
                                    alt="Front Office & Grooming Standar Bintang 5"
                                    class="htl-photo"
                                    style="object-position: 50% 20%;"
                                    loading="lazy"
                                    decoding="async"
                                >
                                <div class="htl-pillar-grad" aria-hidden="true"></div>
                                <div class="htl-pillar-meta">
                                    <span class="htl-pillar-num">01</span>
                                    <h3 class="htl-pillar-title">Front Office &amp; Grooming</h3>
                                </div>
                            </article>

                            <article class="htl-pillar-card htl-photo-hover">
                                <img
                                    src="{{ asset('images/landing/budaya-belajar-bertiga.jpeg') }}"
                                    alt="Executive Housekeeping & Boutique Suite"
                                    class="htl-photo"
                                    style="object-position: 50% 22%;"
                                    loading="lazy"
                                    decoding="async"
                                >
                                <div class="htl-pillar-grad" aria-hidden="true"></div>
                                <div class="htl-pillar-meta">
                                    <span class="htl-pillar-num">02</span>
                                    <h3 class="htl-pillar-title">Suite &amp; Housekeeping</h3>
                                </div>
                            </article>

                            <article class="htl-pillar-card htl-photo-hover">
                                <img
                                    src="{{ asset('images/web/pilar/pilar-tefa.jpg') }}"
                                    alt="Barista Modern & Wikrama Coffee Corner"
                                    class="htl-photo"
                                    style="object-position: 50% 56%;"
                                    loading="lazy"
                                    decoding="async"
                                >
                                <div class="htl-pillar-grad" aria-hidden="true"></div>
                                <div class="htl-pillar-meta">
                                    <span class="htl-pillar-num">03</span>
                                    <h3 class="htl-pillar-title">Barista &amp; TEFA Cafe</h3>
                                </div>
                            </article>
                        </div>

                        <!-- Right: About Perhotelan + Trust Stack (Haven Style) -->
                        <div>
                            <div class="htl-eyebrow">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c-4 3.5-6 7.5-6 11a6 6 0 0012 0c0-3.5-2-7.5-6-11z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v11"/>
                                </svg>
                                <span>Tentang Perhotelan</span>
                            </div>

                            <h2 id="htl-about-title" class="htl-about-heading">
                                Pelayanan prima yang bertumpu pada karakter dan standar industri.
                            </h2>

                            <p class="htl-about-copy">
                                Program keahlian Perhotelan di {{ $schoolName }} membentuk lulusan dengan <strong>attitude prima, integritas islami, kemampuan multi-bahasa, serta kecakapan teknis hospitality berstandar bintang 5</strong>. Siswa belajar langsung mengoperasikan Property Management System (PMS), meracik espresso profesional, table manner internasional, hingga pengelolaan kamar suite.
                            </p>

                            <div class="htl-trust-row">
                                <div class="htl-avatars" aria-hidden="true">
                                    <img src="{{ asset('images/web/alumni-desta.png') }}" alt="" loading="lazy">
                                    <img src="{{ asset('images/web/alumni-auriel.png') }}" alt="" loading="lazy">
                                    <img src="{{ asset('images/web/alumni-faras.png') }}" alt="" loading="lazy">
                                    <img src="{{ asset('images/web/alumni-ichsan.png') }}" alt="" loading="lazy">
                                </div>
                                <div>
                                    <div style="font-size:12px; font-weight:600; color:var(--hv-ink);">98% Keterserapan BMW</div>
                                    <div style="font-size:11.5px; color:var(--hv-muted);">Bekerja, Melanjutkan &amp; Wirausaha</div>
                                </div>
                                <div style="margin-left:auto;">
                                    <div style="display:flex; align-items:center; gap:5px;">
                                        <span style="font-weight:700; font-size:13px; color:var(--hv-ink);">5.0</span>
                                        <span style="color:var(--hv-gold); letter-spacing:1px; font-size:12px;" aria-label="Standar Bintang Lima">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                                    </div>
                                    <div style="font-size:11px; color:var(--hv-muted);">Standar Hotel ★4 &amp; ★5</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hairline Stats Strip (4 Key HTL Metrics) -->
                    <div class="htl-stats-bar">
                        <div class="htl-stat-cell">
                            <div class="htl-stat-num">100%</div>
                            <div class="htl-stat-lbl">Magang Hotel Bintang 4 &amp; 5</div>
                        </div>
                        <div class="htl-stat-cell">
                            <div class="htl-stat-num">3 Skema</div>
                            <div class="htl-stat-lbl">Sertifikasi BNSP &amp; Barista</div>
                        </div>
                        <div class="htl-stat-cell">
                            <div class="htl-stat-num">2 Unit</div>
                            <div class="htl-stat-lbl">Teaching Factory Suite &amp; Cafe</div>
                        </div>
                        <div class="htl-stat-cell">
                            <div class="htl-stat-num">98%</div>
                            <div class="htl-stat-lbl">Keterserapan Lulusan BMW</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── 3. ASYMMETRIC EDITORIAL FACILITY CAROUSEL (6 LABS & SPACES) ── -->
            <section
                id="fasilitas"
                class="htl-sec"
                style="padding-top: 24px;"
                x-data="{
                    idx: 0,
                    items: @js($facilities),
                    prev() { this.idx = (this.idx - 1 + this.items.length) % this.items.length; },
                    next() { this.idx = (this.idx + 1) % this.items.length; },
                    at(offset) { return this.items[(this.idx + offset) % this.items.length]; }
                }"
            >
                <div class="htl-wrap">
                    <div class="htl-carousel-grid">
                        <!-- Left Editorial Column & Controls -->
                        <div>
                            <span class="htl-eyebrow">Fasilitas Standar Hotel Bintang</span>
                            <h2 class="htl-sec-h2" style="margin-top: 10px;">
                                Temukan standar ruang praktik kami
                            </h2>
                            <p style="margin-top: 14px; font-size: 14px; line-height: 1.75; color: var(--hv-muted);">
                                Setiap ruang praktik dirancang menyerupai operasional hotel berbintang sesungguhnya agar siswa terbiasa dengan ritme kerja industri sejak bangku kelas.
                            </p>

                            <!-- Active Facility Info -->
                            <div style="margin-top: 22px; padding-top: 18px; border-top: 1px solid var(--hv-line);">
                                <div style="display:flex; align-items:center; justify-content:space-between; font-size:11.5px; color:var(--hv-olive); font-weight:600; letter-spacing:0.06em; text-transform:uppercase;">
                                    <span x-text="at(0).tag"></span>
                                    <span x-text="at(0).num + ' / 06'"></span>
                                </div>
                                <h3 class="htl-serif" style="font-size: 24px; color: var(--hv-ink); margin-top: 6px; line-height: 1.2;" x-text="at(0).title"></h3>
                                <p style="margin-top: 8px; font-size: 13px; line-height: 1.68; color: var(--hv-muted);" x-text="at(0).desc"></p>
                            </div>

                            <!-- Haven Circular Prev/Next Controls -->
                            <div style="margin-top: 22px; display:flex; align-items:center; gap: 10px;">
                                <button type="button" class="htl-circle-btn" @click="prev()" aria-label="Fasilitas sebelumnya">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m6 6l-6-6 6-6"/>
                                    </svg>
                                </button>
                                <button type="button" class="htl-circle-btn" @click="next()" aria-label="Fasilitas berikutnya">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6l6 6-6 6"/>
                                    </svg>
                                </button>
                                <div style="margin-left: 8px; display:flex; gap: 6px;">
                                    <template x-for="(item, i) in items" :key="item.num">
                                        <button
                                            type="button"
                                            @click="idx = i"
                                            :aria-label="'Lihat ' + item.title"
                                            :style="idx === i ? 'width:22px; background:var(--hv-olive);' : 'width:7px; background:var(--hv-line);'"
                                            style="height:7px; border-radius:999px; border:0; cursor:pointer; transition:all 0.25s ease;"
                                        ></button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Haven Asymmetric 3-Frame Rounded Photo Strip -->
                        <div class="htl-asym-strip">
                            <!-- Wide Active Frame -->
                            <div class="htl-asym-card htl-photo-hover" @click="next()">
                                <img
                                    :src="at(0).img"
                                    :alt="at(0).title"
                                    class="htl-photo"
                                    :style="'object-position:' + at(0).pos"
                                >
                                <div class="htl-asym-caption">
                                    <div style="font-size:10.5px; text-transform:uppercase; letter-spacing:0.1em; color:rgba(250,248,244,0.72);" x-text="at(0).num + ' — ' + at(0).tag"></div>
                                    <div style="font-size:14px; font-weight:600; margin-top:2px;" x-text="at(0).title"></div>
                                </div>
                            </div>

                            <!-- Medium Next Frame -->
                            <div class="htl-asym-card htl-photo-hover" @click="next()">
                                <img
                                    :src="at(1).img"
                                    :alt="at(1).title"
                                    class="htl-photo"
                                    :style="'object-position:' + at(1).pos"
                                >
                                <div class="htl-pillar-grad" aria-hidden="true"></div>
                                <div style="position:absolute; left:14px; right:14px; bottom:14px; color:#FAF8F4;">
                                    <div style="font-size:10px; letter-spacing:0.1em; opacity:0.75;" x-text="at(1).num"></div>
                                    <div style="font-size:13px; font-weight:600; line-height:1.25;" x-text="at(1).title"></div>
                                </div>
                            </div>

                            <!-- Slim Peek Frame -->
                            <div class="htl-asym-card htl-photo-hover" @click="idx = (idx + 2) % items.length">
                                <img
                                    :src="at(2).img"
                                    :alt="at(2).title"
                                    class="htl-photo"
                                    :style="'object-position:' + at(2).pos"
                                >
                                <div class="htl-pillar-grad" aria-hidden="true"></div>
                                <div style="position:absolute; left:12px; bottom:14px; color:#FAF8F4; font-size:11px; font-weight:600;" x-text="at(2).num"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── 4. DARK ESPRESSO SHOWCASE + INTERACTIVE PMS & TAP KEYCARD NFC (HAVEN BAND 4) ── -->
            <section
                id="pms-tefa"
                class="htl-sec"
                style="padding-top: 16px;"
                x-data="{
                    unlocked: false,
                    guestIdx: 0,
                    tefaIdx: 0,
                    feedIdx: 0,
                    guests: [
                        { name: 'Mr. David Miller', origin: 'Geneva, CH', room: 'Presidential Suite 501', checkin: '26 Okt 2026', checkout: '29 Okt 2026', pref: 'V60 Single Origin Garut' },
                        { name: 'Mme. Sophie Laurent', origin: 'Paris, FR', room: 'Executive Suite 408', checkin: '28 Okt 2026', checkout: '31 Okt 2026', pref: 'Flat White & Turndown' },
                        { name: 'Mr. Kenji Sato', origin: 'Tokyo, JP', room: 'Boutique Suite 305', checkin: '02 Nov 2026', checkout: '05 Nov 2026', pref: 'Manual Brew & Early Check-in' }
                    ],
                    tefas: [
                        { num: '01', label: 'Boutique Suite', title: 'Wikrama Boutique Suite', text: 'Unit penginapan nyata berstandar kamar hotel bintang yang melayani tamu resmi, kunjungan dinas, dan relasi sekolah — melatih standar housekeeping & grooming setiap hari.' },
                        { num: '02', label: 'Coffee Corner', title: 'Wikrama Coffee Corner', text: 'Praktik barista profesional dengan pesanan harian nyata: kalibrasi espresso 9-bar, manual brew V60, micro-foam latte art, hingga manajemen kasir cafe.' },
                        { num: '03', label: 'Opera & VHP PMS', title: 'Simulasi Sistem PMS Industri', text: 'Santri terlatih mengoperasikan sistem manajemen hotel VHP & Opera PMS mulai dari reservasi tamu VIP, registrasi check-in, penerbitan kartu akses NFC, hingga billing.' }
                    ],
                    feeds: [
                        'Room Service: Flat White & Artisan Croissant diantar ke Kamar #402',
                        'Housekeeping: Evening Turndown Service selesai untuk Suite 501',
                        'Barista Lab: Single Origin Garut diekstraksi pada tekanan 9 Bar',
                        'Table Manner: Table setup 12-course dinner siap di Executive Room',
                        'Front Office: Check-in pass & keycard NFC terbit via Opera PMS'
                    ],
                    init() {
                        setInterval(() => { this.feedIdx = (this.feedIdx + 1) % this.feeds.length; }, 3200);
                    },
                    tapKeycard() {
                        this.unlocked = true;
                        setTimeout(() => { this.unlocked = false; }, 3600);
                    },
                    nextGuest() {
                        this.guestIdx = (this.guestIdx + 1) % this.guests.length;
                        this.unlocked = false;
                    }
                }"
            >
                <div class="htl-wrap">
                    <div class="htl-dark-card">
                        <img
                            src="{{ asset('images/web/pilar/pilar-tefa.jpg') }}"
                            alt=""
                            class="htl-dark-bg"
                            aria-hidden="true"
                            loading="lazy"
                        >
                        <div class="htl-dark-scrim" aria-hidden="true"></div>

                        <div class="htl-dark-grid">
                            <!-- Left: Crisp White Booking Confirmation & Interactive PMS / NFC Card -->
                            <div class="htl-pms-white">
                                <div>
                                    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:14px;">
                                        <div>
                                            <span
                                                style="display:inline-flex; align-items:center; gap:6px; font-size:10.5px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; padding:4px 10px; border-radius:999px;"
                                                :style="unlocked ? 'background:rgba(88,99,70,0.14); color:#434C34;' : 'background:#F4F1EA; color:#625E55;'"
                                                x-text="unlocked ? 'RFID NFC Terverifikasi' : 'Wikrama Grand Suite • PMS'"
                                            ></span>
                                            <h3
                                                class="htl-serif"
                                                style="font-size: 27px; line-height: 1.08; color: var(--hv-ink); margin-top: 10px;"
                                                x-text="unlocked ? 'Akses pintu terbuka!' : 'Reservasi terkonfirmasi!'"
                                            ></h3>
                                        </div>

                                        <div style="width:62px; height:62px; border-radius:14px; overflow:hidden; flex-shrink:0; border:1px solid #E8E4DB;">
                                            <img
                                                src="{{ asset('images/landing/budaya-belajar-bertiga.jpeg') }}"
                                                alt="Suite Hospitality Wikrama"
                                                style="width:100%; height:100%; object-fit:cover; object-position:50% 20%;"
                                                loading="lazy"
                                            >
                                        </div>
                                    </div>

                                    <!-- Confirmation Rows (Haven Booking Details Layout) -->
                                    <div style="margin-top: 16px;">
                                        <div class="htl-pms-row">
                                            <span style="color:var(--hv-subtle);">Tamu VVIP</span>
                                            <span style="font-weight:600; color:var(--hv-ink);" x-text="guests[guestIdx].name"></span>
                                        </div>
                                        <div class="htl-pms-row">
                                            <span style="color:var(--hv-subtle);">Kamar Suite</span>
                                            <span style="font-weight:600; color:var(--hv-ink);" x-text="guests[guestIdx].room"></span>
                                        </div>
                                        <div class="htl-pms-row">
                                            <span style="color:var(--hv-subtle);">Check-In / Out</span>
                                            <span style="font-weight:500; color:var(--hv-ink);" x-text="guests[guestIdx].checkin + ' — ' + guests[guestIdx].checkout"></span>
                                        </div>
                                        <div class="htl-pms-row">
                                            <span style="color:var(--hv-subtle);">Preferensi Tamu</span>
                                            <span style="font-weight:500; color:var(--hv-olive);" x-text="guests[guestIdx].pref"></span>
                                        </div>
                                    </div>

                                    <!-- Live Butler & Barista Status Line -->
                                    <div style="margin-top: 14px; padding: 11px 13px; border-radius: 12px; background: #F6F4EF; border: 1px solid #E7E2D8;">
                                        <div style="display:flex; align-items:center; justify-content:space-between; font-size:10.5px; font-weight:600; color:var(--hv-olive); text-transform:uppercase; letter-spacing:0.06em;">
                                            <span>Live Concierge Feed</span>
                                            <span>Barista: 9 Bar • 84 Cups</span>
                                        </div>
                                        <p style="margin-top: 5px; font-size: 12px; line-height: 1.45; color: var(--hv-muted);" x-text="feeds[feedIdx]"></p>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div style="display:flex; flex-direction:column; gap:8px;">
                                    <button type="button" class="htl-pms-btn" @click="tapKeycard()">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <rect x="3" y="5" width="18" height="14" rx="2"/>
                                            <path d="M7 15h4M15 11a2 2 0 010 4"/>
                                        </svg>
                                        <span x-text="unlocked ? 'Pintu Suite Terbuka — Selamat Datang!' : 'Tap Keycard NFC — Buka Akses Suite'"></span>
                                    </button>
                                    <button
                                        type="button"
                                        @click="nextGuest()"
                                        style="background:transparent; border:0; color:var(--hv-muted); font-size:11.5px; font-weight:500; cursor:pointer; padding:4px; text-decoration:underline; text-underline-offset:3px;"
                                    >
                                        Ganti Profil Tamu Simulasi (<span x-text="guests[guestIdx].origin"></span>)
                                    </button>
                                </div>
                            </div>

                            <!-- Right: Dark Atmospheric TEFA Showcase (Haven "Reserve your perfect getaway") -->
                            <div class="htl-dark-right">
                                <div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                                    <span style="font-size:11.5px; letter-spacing:0.12em; text-transform:uppercase; color:var(--hv-gold); font-weight:600;">
                                        Teaching Factory &amp; Simulasi Industri
                                    </span>
                                    <span style="width:40px; height:40px; border-radius:999px; border:1px solid rgba(250,248,244,0.24); display:inline-flex; align-items:center; justify-content:center; color:rgba(250,248,244,0.8);" aria-hidden="true">
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                            <rect x="3" y="4" width="18" height="18" rx="3"/>
                                            <path d="M16 2v4M8 2v4M3 10h18"/>
                                        </svg>
                                    </span>
                                </div>

                                <div>
                                    <h2 class="htl-dark-h2">
                                        Rasakan standar layanan hotel berbintang
                                    </h2>
                                    <p style="margin-top: 14px; font-size: 14px; line-height: 1.75; color: rgba(250, 248, 244, 0.78); max-width: 520px;">
                                        Bukan sekadar teori kelas. Siswa Perhotelan {{ $schoolName }} mengoperasikan unit bisnis nyata sekolah &mdash; menggabungkan keahlian hotelier, grooming bintang lima, dan barista kopi modern.
                                    </p>

                                    <!-- Interactive TEFA Tabs (01 - 03) -->
                                    <div class="htl-tefa-pills" style="margin-top: 22px;">
                                        <template x-for="(tf, i) in tefas" :key="tf.num">
                                            <button
                                                type="button"
                                                class="htl-tefa-pill"
                                                :class="{ 'is-active': tefaIdx === i }"
                                                @click="tefaIdx = i"
                                                x-text="tf.num + ' · ' + tf.label"
                                            ></button>
                                        </template>
                                    </div>

                                    <div style="margin-top: 18px; padding-top: 16px; border-top: 1px solid rgba(250, 248, 244, 0.16);">
                                        <h3 class="htl-serif" style="font-size: 24px; color: #FAF8F4;" x-text="tefas[tefaIdx].title"></h3>
                                        <p style="margin-top: 6px; font-size: 13.5px; line-height: 1.7; color: rgba(250, 248, 244, 0.76);" x-text="tefas[tefaIdx].text"></p>
                                    </div>
                                </div>

                                <!-- Bottom Indicator + Pill CTA (Haven 01 —— 03 + Explore Pill) -->
                                <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:16px; padding-top:12px;">
                                    <div style="display:flex; align-items:center; gap:12px; font-size:12px; letter-spacing:0.1em; color:rgba(250,248,244,0.82);">
                                        <span x-text="tefas[tefaIdx].num"></span>
                                        <span style="display:inline-block; width:52px; height:1px; background:rgba(250,248,244,0.34);"></span>
                                        <span style="opacity:0.55;">03</span>
                                    </div>

                                    <a href="#kurikulum" class="htl-btn-ghost-light">
                                        <span>Eksplorasi Kurikulum</span>
                                        <span aria-hidden="true">&rarr;</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── 5. PROSPEK KARIER LULUSAN PERHOTELAN (6 ROLES, BORDERLESS EDITORIAL GRID) ── -->
            <section id="karir" class="htl-sec">
                <div class="htl-wrap">
                    <div style="display:flex; flex-wrap:wrap; align-items:flex-end; justify-content:space-between; gap:20px; margin-bottom:36px;">
                        <div style="max-width:580px;">
                            <span class="htl-eyebrow">Peluang Karier Global</span>
                            <h2 class="htl-sec-h2" style="margin-top:10px;">
                                Prospek Karier Lulusan Perhotelan
                            </h2>
                        </div>
                        <p style="max-width:400px; font-size:13.5px; line-height:1.72; color:var(--hv-muted);">
                            Lulusan perhotelan tidak hanya berkarier di hotel berbintang, tetapi juga di industri pariwisata, kapal pesiar internasional, restoran mewah, hingga wirausaha cafe independen.
                        </p>
                    </div>

                    <div class="htl-career-grid">
                        <article class="htl-career-cell">
                            <div>
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                                    <span class="htl-serif" style="font-size:22px; color:var(--hv-olive);">01</span>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--hv-muted)" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 18h18M5 18v-2a7 7 0 0114 0v2M12 7V5m-2 0h4"/>
                                    </svg>
                                </div>
                                <h3 class="htl-serif" style="font-size:24px; line-height:1.18; color:var(--hv-ink);">Front Desk &amp; Concierge Specialist</h3>
                                <p style="margin-top:10px; font-size:13px; line-height:1.7; color:var(--hv-muted);">
                                    Mengelola reservasi tamu, registrasi check-in/out, pengoperasian sistem PMS, handling VIP guest, serta melayani informasi wisata dengan ramah dan percaya diri.
                                </p>
                            </div>
                            <div style="font-size:12px; font-weight:600; color:var(--hv-olive);">
                                Estimasi Gaji: Rp 4.500.000 &ndash; Rp 9.000.000
                            </div>
                        </article>

                        <article class="htl-career-cell">
                            <div>
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                                    <span class="htl-serif" style="font-size:22px; color:var(--hv-olive);">02</span>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--hv-muted)" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h1a4 4 0 010 8h-1M3 8h14v9a4 4 0 01-4 4H7a4 4 0 01-4-4V8zm3-4v2m4-2v2m4-2v2"/>
                                    </svg>
                                </div>
                                <h3 class="htl-serif" style="font-size:24px; line-height:1.18; color:var(--hv-ink);">Professional Barista &amp; Latte Artist</h3>
                                <p style="margin-top:10px; font-size:13px; line-height:1.7; color:var(--hv-muted);">
                                    Ahli meracik signature espresso, manual brew (V60, Aeropress), latte art, quality control biji kopi, hingga manajemen operasional kedai kopi dan specialty cafe.
                                </p>
                            </div>
                            <div style="font-size:12px; font-weight:600; color:var(--hv-olive);">
                                Estimasi Gaji: Rp 4.000.000 &ndash; Rp 8.000.000+
                            </div>
                        </article>

                        <article class="htl-career-cell">
                            <div>
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                                    <span class="htl-serif" style="font-size:22px; color:var(--hv-olive);">03</span>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--hv-muted)" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M4 12V8a2 2 0 012-2h12a2 2 0 012 2v4M3 12v6m18-6v6M3 16h18"/>
                                    </svg>
                                </div>
                                <h3 class="htl-serif" style="font-size:24px; line-height:1.18; color:var(--hv-ink);">Executive Housekeeper &amp; Floor Supervisor</h3>
                                <p style="margin-top:10px; font-size:13px; line-height:1.7; color:var(--hv-muted);">
                                    Menjaga standar kebersihan, kerapian, tata ruang kamar suite hotel berbintang, manajemen linen, perlengkapan laundry, dan inspeksi sanitasi harian.
                                </p>
                            </div>
                            <div style="font-size:12px; font-weight:600; color:var(--hv-olive);">
                                Estimasi Gaji: Rp 4.200.000 &ndash; Rp 8.500.000
                            </div>
                        </article>

                        <article class="htl-career-cell">
                            <div>
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                                    <span class="htl-serif" style="font-size:22px; color:var(--hv-olive);">04</span>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--hv-muted)" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 3v18M17 3v18M4 7h6M14 7h6M7 11h10"/>
                                    </svg>
                                </div>
                                <h3 class="htl-serif" style="font-size:24px; line-height:1.18; color:var(--hv-ink);">F&amp;B Service &amp; Banquet Captain</h3>
                                <p style="margin-top:10px; font-size:13px; line-height:1.7; color:var(--hv-muted);">
                                    Mengatur tata hidang perjamuan resmi (table setting), event banquet, gala dinner, wedding reception, serta pelayanan pelanggan di restoran hotel mewah.
                                </p>
                            </div>
                            <div style="font-size:12px; font-weight:600; color:var(--hv-olive);">
                                Estimasi Gaji: Rp 4.500.000 &ndash; Rp 9.500.000
                            </div>
                        </article>

                        <article class="htl-career-cell">
                            <div>
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                                    <span class="htl-serif" style="font-size:22px; color:var(--hv-olive);">05</span>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--hv-muted)" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2 18c2 1.2 4 1.2 6 0s4-1.2 6 0 4 1.2 6 0M4 15l2-6h12l2 6M8 9V6h8v3"/>
                                    </svg>
                                </div>
                                <h3 class="htl-serif" style="font-size:24px; line-height:1.18; color:var(--hv-ink);">Cruise Ship Hospitality Crew</h3>
                                <p style="margin-top:10px; font-size:13px; line-height:1.7; color:var(--hv-muted);">
                                    Bekerja keliling dunia di kapal pesiar internasional (Royal Caribbean, Carnival, Costa) dengan penghasilan berstandar dolar AS dan fasilitas lengkap.
                                </p>
                            </div>
                            <div style="font-size:12px; font-weight:600; color:var(--hv-olive);">
                                Estimasi Gaji: $1.200 &ndash; $3.000 USD / Bulan
                            </div>
                        </article>

                        <article class="htl-career-cell">
                            <div>
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                                    <span class="htl-serif" style="font-size:22px; color:var(--hv-olive);">06</span>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--hv-muted)" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10l9-6 9 6v9a1 1 0 01-1 1H4a1 1 0 01-1-1v-9zM9 20v-7h6v7"/>
                                    </svg>
                                </div>
                                <h3 class="htl-serif" style="font-size:24px; line-height:1.18; color:var(--hv-ink);">Cafe Owner &amp; Event Organizer</h3>
                                <p style="margin-top:10px; font-size:13px; line-height:1.7; color:var(--hv-muted);">
                                    Mendirikan bisnis cafe mandiri, jasa catering profesional, wedding &amp; event planning organizer, atau homestay serta boutique villa berbasis digital OTA.
                                </p>
                            </div>
                            <div style="font-size:12px; font-weight:600; color:var(--hv-olive);">
                                Penghasilan: Mandiri &amp; Tak Terbatas
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <!-- ── 6. KURIKULUM 3 TAHUN (FASE E & FASE F) ── -->
            <section id="kurikulum" class="htl-sec htl-stone-band">
                <div class="htl-wrap">
                    <div style="max-width:640px; margin-bottom:40px;">
                        <span class="htl-eyebrow">Roadmap Pembelajaran</span>
                        <h2 class="htl-sec-h2" style="margin-top:10px;">
                            Kurikulum 3 Tahun Berstandar Industri Hospitality
                        </h2>
                        <p style="margin-top:12px; font-size:14px; line-height:1.75; color:var(--hv-muted);">
                            Disusun bersama asosiasi PHRI dan jaringan manajemen hotel berbintang, memadukan teori, praktik mock-up room, serta magang industri nyata.
                        </p>
                    </div>

                    <div class="htl-curr-grid">
                        <!-- KELAS X -->
                        <article class="htl-curr-card">
                            <div>
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
                                    <span class="htl-serif" style="font-size:32px; line-height:1; color:var(--hv-olive);">X</span>
                                    <span style="font-size:11px; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; color:var(--hv-subtle);">Fase E &bull; Tahun 1</span>
                                </div>
                                <h3 class="htl-serif" style="font-size:25px; line-height:1.15; color:var(--hv-ink);">Hospitality Fundamentals &amp; Grooming</h3>
                                <p style="margin-top:8px; font-size:13px; line-height:1.65; color:var(--hv-muted);">
                                    Membangun karakter dasar perhotelan, etika pelayanan prima, grooming, dan pengenalan sanitasi hygiene.
                                </p>

                                <ul style="margin-top:20px; padding-top:18px; border-top:1px solid var(--hv-line); list-style:none; padding-left:0; display:flex; flex-direction:column; gap:11px; font-size:13px; color:var(--hv-ink);">
                                    <li style="display:flex; align-items:flex-start; gap:10px;"><span style="color:var(--hv-olive); font-weight:700;">&mdash;</span><span>Dasar-Dasar Industri Perhotelan &amp; Pariwisata</span></li>
                                    <li style="display:flex; align-items:flex-start; gap:10px;"><span style="color:var(--hv-olive); font-weight:700;">&mdash;</span><span>Sanitasi, Hygiene, dan Keselamatan Kerja (K3LH)</span></li>
                                    <li style="display:flex; align-items:flex-start; gap:10px;"><span style="color:var(--hv-olive); font-weight:700;">&mdash;</span><span>Personal Grooming &amp; Hospitality Body Language</span></li>
                                    <li style="display:flex; align-items:flex-start; gap:10px;"><span style="color:var(--hv-olive); font-weight:700;">&mdash;</span><span>English &amp; Foreign Language for Frontliner</span></li>
                                    <li style="display:flex; align-items:flex-start; gap:10px;"><span style="color:var(--hv-olive); font-weight:700;">&mdash;</span><span>Customer Service Excellence &amp; Etika Islami</span></li>
                                </ul>
                            </div>
                        </article>

                        <!-- KELAS XI -->
                        <article class="htl-curr-card htl-curr-card--featured">
                            <div>
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
                                    <span class="htl-serif" style="font-size:32px; line-height:1; color:var(--hv-olive);">XI</span>
                                    <span style="font-size:11px; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; color:var(--hv-olive);">Fase F &bull; Spesialisasi Dept</span>
                                </div>
                                <h3 class="htl-serif" style="font-size:25px; line-height:1.15; color:var(--hv-ink);">Front Office, Housekeeping &amp; Barista</h3>
                                <p style="margin-top:8px; font-size:13px; line-height:1.65; color:var(--hv-muted);">
                                    Pendalaman teknis operasional divisi front office, tata graha suite, dan seni racik barista cafe.
                                </p>

                                <ul style="margin-top:20px; padding-top:18px; border-top:1px solid var(--hv-line); list-style:none; padding-left:0; display:flex; flex-direction:column; gap:11px; font-size:13px; color:var(--hv-ink);">
                                    <li style="display:flex; align-items:flex-start; gap:10px;"><span style="color:var(--hv-olive); font-weight:700;">&mdash;</span><span>Front Office Operations (VHP/Opera PMS System)</span></li>
                                    <li style="display:flex; align-items:flex-start; gap:10px;"><span style="color:var(--hv-olive); font-weight:700;">&mdash;</span><span>Housekeeping Management &amp; Making Bed Suite Room</span></li>
                                    <li style="display:flex; align-items:flex-start; gap:10px;"><span style="color:var(--hv-olive); font-weight:700;">&mdash;</span><span>Food &amp; Beverage Service &amp; Banquet Set Up</span></li>
                                    <li style="display:flex; align-items:flex-start; gap:10px;"><span style="color:var(--hv-olive); font-weight:700;">&mdash;</span><span>Barista Masterclass: Espresso, Manual Brew &amp; Latte Art</span></li>
                                    <li style="display:flex; align-items:flex-start; gap:10px;"><span style="color:var(--hv-olive); font-weight:700;">&mdash;</span><span>Operasional TEFA: Wikrama Boutique Suite &amp; Cafe</span></li>
                                </ul>
                            </div>
                        </article>

                        <!-- KELAS XII -->
                        <article class="htl-curr-card">
                            <div>
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
                                    <span class="htl-serif" style="font-size:32px; line-height:1; color:var(--hv-olive);">XII</span>
                                    <span style="font-size:11px; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; color:var(--hv-subtle);">Fase F &bull; Tahun 3</span>
                                </div>
                                <h3 class="htl-serif" style="font-size:25px; line-height:1.15; color:var(--hv-ink);">Magang 6 Bulan, Table Manner &amp; BNSP</h3>
                                <p style="margin-top:8px; font-size:13px; line-height:1.65; color:var(--hv-muted);">
                                    Penerjunan langsung di hotel berbintang 4 dan 5, sertifikasi uji kompetensi BNSP, serta penyaluran BMW.
                                </p>

                                <ul style="margin-top:20px; padding-top:18px; border-top:1px solid var(--hv-line); list-style:none; padding-left:0; display:flex; flex-direction:column; gap:11px; font-size:13px; color:var(--hv-ink);">
                                    <li style="display:flex; align-items:flex-start; gap:10px;"><span style="color:var(--hv-olive); font-weight:700;">&mdash;</span><span>PKL Industri 6 Bulan di Jaringan Hotel Bintang 4/5</span></li>
                                    <li style="display:flex; align-items:flex-start; gap:10px;"><span style="color:var(--hv-olive); font-weight:700;">&mdash;</span><span>International Table Manner Experience di Hotel Bintang 5</span></li>
                                    <li style="display:flex; align-items:flex-start; gap:10px;"><span style="color:var(--hv-olive); font-weight:700;">&mdash;</span><span>Uji Sertifikasi BNSP Front Office &amp; Housekeeping</span></li>
                                    <li style="display:flex; align-items:flex-start; gap:10px;"><span style="color:var(--hv-olive); font-weight:700;">&mdash;</span><span>Penyusunan Portofolio &amp; Direct Career Recruitment</span></li>
                                    <li style="display:flex; align-items:flex-start; gap:10px;"><span style="color:var(--hv-olive); font-weight:700;">&mdash;</span><span>Penyaluran BMW (Bekerja, Melanjutkan, Wirausaha)</span></li>
                                </ul>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <!-- ── 7. SERTIFIKASI KOMPETENSI RESMI (BNSP & TABLE MANNER) ── -->
            <section id="sertifikasi" class="htl-sec">
                <div class="htl-wrap">
                    <div style="display:flex; flex-wrap:wrap; align-items:flex-end; justify-content:space-between; gap:20px; margin-bottom:40px;">
                        <div style="max-width:560px;">
                            <span class="htl-eyebrow">Pengakuan Industri Berstandar Nasional</span>
                            <h2 class="htl-sec-h2" style="margin-top:10px;">
                                Sertifikasi Kompetensi Resmi BNSP &amp; Industri
                            </h2>
                        </div>
                        <p style="max-width:420px; font-size:13.5px; line-height:1.72; color:var(--hv-muted);">
                            Setiap lulusan dibekali sertifikat kompetensi berlogo Garuda dari Badan Nasional Sertifikasi Profesi (BNSP) dan sertifikat resmi industri perhotelan.
                        </p>
                    </div>

                    <div class="htl-cert-grid">
                        <article class="htl-cert-item">
                            <div style="font-size:11px; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; color:var(--hv-olive);">SKKNI Perhotelan &bull; 01</div>
                            <h3 class="htl-serif" style="font-size:26px; line-height:1.16; color:var(--hv-ink); margin-top:10px;">Sertifikasi BNSP Front Office</h3>
                            <p style="margin-top:10px; font-size:13.5px; line-height:1.72; color:var(--hv-muted);">
                                Pengakuan negara untuk kompetensi Front Desk Agent, Reservation Clerk, Telephone Operator, dan Penanganan Tamu VIP berdasarkan SKKNI Perhotelan.
                            </p>
                            <div style="margin-top:16px; font-size:12px; font-weight:600; color:var(--hv-ink);">
                                Diterbitkan oleh: BNSP LSP P-1
                            </div>
                        </article>

                        <article class="htl-cert-item">
                            <div style="font-size:11px; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; color:var(--hv-olive);">Coffee Competence &bull; 02</div>
                            <h3 class="htl-serif" style="font-size:26px; line-height:1.16; color:var(--hv-ink); margin-top:10px;">Sertifikat Barista &amp; Coffee Brewing</h3>
                            <p style="margin-top:10px; font-size:13.5px; line-height:1.72; color:var(--hv-muted);">
                                Sertifikasi keahlian barista mencakup kalibrasi espresso, steaming milk micro-foam, latte art free pour, dan sanitasi bar coffee shop modern.
                            </p>
                            <div style="margin-top:16px; font-size:12px; font-weight:600; color:var(--hv-ink);">
                                Standar: Specialty Coffee Association / BNSP
                            </div>
                        </article>

                        <article class="htl-cert-item">
                            <div style="font-size:11px; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; color:var(--hv-olive);">5-Star Etiquette &bull; 03</div>
                            <h3 class="htl-serif" style="font-size:26px; line-height:1.16; color:var(--hv-ink); margin-top:10px;">Sertifikat Table Manner Hotel Bintang 5</h3>
                            <p style="margin-top:10px; font-size:13.5px; line-height:1.72; color:var(--hv-muted);">
                                Sertifikat kursus jamuan makan internasional yang diselenggarakan langsung di hotel berbintang 5, ditandatangani General Manager dan Executive Chef.
                            </p>
                            <div style="margin-top:16px; font-size:12px; font-weight:600; color:var(--hv-ink);">
                                Mitra: Jaringan Hotel Aston / Santika
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <!-- ── 8. MITRA DUDI & TEMPAT MAGANG (6 HOTEL PARTNERS) ── -->
            <section id="mitra" style="padding-top: 12px; padding-bottom: 56px;">
                <div class="htl-wrap">
                    <div style="text-align:center; max-width:560px; margin:0 auto 28px;">
                        <span class="htl-eyebrow">Jaringan Industri Hospitality</span>
                        <h2 class="htl-sec-h2" style="margin-top:8px; font-size: clamp(1.85rem, 3vw, 2.5rem);">
                            Mitra PKL &amp; Penyerapan Lulusan
                        </h2>
                        <p style="margin-top:8px; font-size:13.5px; color:var(--hv-muted);">
                            Siswa menjalani magang nyata selama 6 bulan di hotel bintang 4 &amp; 5 dengan pembimbing industri bersertifikat.
                        </p>
                    </div>

                    <div class="htl-partner-grid">
                        <div class="htl-partner-cell">
                            <div class="htl-serif" style="font-size:21px; color:var(--hv-ink);">Aston Inn Garut</div>
                            <div style="font-size:11px; color:var(--hv-subtle); margin-top:3px;">Hotel Bintang 4</div>
                        </div>
                        <div class="htl-partner-cell">
                            <div class="htl-serif" style="font-size:21px; color:var(--hv-ink);">Santika Indonesia</div>
                            <div style="font-size:11px; color:var(--hv-subtle); margin-top:3px;">Hotels &amp; Resorts</div>
                        </div>
                        <div class="htl-partner-cell">
                            <div class="htl-serif" style="font-size:21px; color:var(--hv-ink);">favehotel Garut</div>
                            <div style="font-size:11px; color:var(--hv-subtle); margin-top:3px;">Archipelago Int.</div>
                        </div>
                        <div class="htl-partner-cell">
                            <div class="htl-serif" style="font-size:21px; color:var(--hv-ink);">Harmoni Hotel</div>
                            <div style="font-size:11px; color:var(--hv-subtle); margin-top:3px;">Resort &amp; Spa</div>
                        </div>
                        <div class="htl-partner-cell">
                            <div class="htl-serif" style="font-size:21px; color:var(--hv-ink);">Mercure Hotels</div>
                            <div style="font-size:11px; color:var(--hv-subtle); margin-top:3px;">Accor Group</div>
                        </div>
                        <div class="htl-partner-cell">
                            <div class="htl-serif" style="font-size:21px; color:var(--hv-ink);">PHRI Jabar</div>
                            <div style="font-size:11px; color:var(--hv-subtle); margin-top:3px;">Asosiasi Industri</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── 9. TESTIMONI ALUMNI PERHOTELAN (DESTA PRATAMA) ── -->
            <section id="alumni" style="padding-top: 16px; padding-bottom: 64px;">
                <div class="htl-wrap">
                    <div class="htl-quote-box">
                        <div style="display:flex; justify-content:center;">
                            <div style="width:168px; height:168px; border-radius:999px; overflow:hidden; background:var(--hv-stone); border:4px solid var(--hv-linen); box-shadow:0 12px 28px rgba(31,29,25,0.08);">
                                <img
                                    src="{{ asset('images/web/alumni-desta.png') }}"
                                    alt="Desta Pratama — Alumni Perhotelan {{ $schoolName }}"
                                    class="htl-photo"
                                    style="width:100%; height:100%; object-fit:cover; object-position:50% 18%;"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </div>
                        </div>

                        <div>
                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                                <span style="color:var(--hv-gold); letter-spacing:2px; font-size:13px;" aria-label="Rating 5 bintang">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                                <span style="font-size:11.5px; letter-spacing:0.08em; text-transform:uppercase; color:var(--hv-subtle);">Kisah Alumni Perhotelan</span>
                            </div>

                            <blockquote class="htl-quote-text" style="margin:0;">
                                &ldquo;Di {{ $schoolName }} jurusan Perhotelan, saya bukan hanya diajari menyambut tamu dengan ramah, tetapi juga kedisiplinan tinggi, sistem PMS hotel internasional, dan keterampilan barista kopi. Begitu selesai magang di hotel bintang 4, saya langsung ditawari kontrak kerja sebelum wisuda.&rdquo;
                            </blockquote>

                            <div style="margin-top:20px; padding-top:16px; border-top:1px solid var(--hv-line); display:flex; flex-wrap:wrap; align-items:baseline; gap:8px 16px;">
                                <span style="font-weight:700; font-size:15px; color:var(--hv-ink);">Desta Pratama</span>
                                <span style="font-size:13px; color:var(--hv-olive);">Alumni Perhotelan &bull; Front Office Supervisor &amp; Head Barista</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── 10. FAQ JURUSAN PERHOTELAN (HAVEN SPLIT NUMBERED ACCORDION 01-04) ── -->
            <section id="faq" class="htl-sec" style="padding-top: 20px;" x-data="{ open: 0 }">
                <div class="htl-wrap">
                    <div class="htl-faq-grid">
                        <!-- Left Editorial Column + Preview Thumbnail + Subtle Botanical Watermark -->
                        <div style="position:relative;">
                            <span class="htl-eyebrow">Pertanyaan Umum</span>
                            <h2 class="htl-sec-h2" style="margin-top:10px;">
                                Ruang tanya untuk mengenal Perhotelan lebih dekat.
                            </h2>

                            <div style="margin-top:22px; width:100%; max-width:280px; border-radius:18px; overflow:hidden; aspect-ratio:16/10; background:var(--hv-espresso);" class="htl-photo-hover">
                                <img
                                    src="{{ asset('images/landing/jurusan-hotel.png') }}"
                                    alt="Siswa Perhotelan menyambut tamu di Front Desk"
                                    class="htl-photo"
                                    style="width:100%; height:100%; object-fit:cover; object-position:50% 20%;"
                                    loading="lazy"
                                >
                            </div>

                            <p style="margin-top:12px; font-size:12.5px; line-height:1.65; color:var(--hv-muted); max-width:280px;">
                                Pembinaan hospitality menyeluruh sejak hari pertama &mdash; dari etika sapaan hingga uji sertifikasi profesi.
                            </p>

                            <!-- Faint Botanical Branch Watermark (Haven Touch) -->
                            <svg width="110" height="120" viewBox="0 0 110 120" fill="none" stroke="#586346" stroke-width="1" style="position:absolute; right:0; bottom:-12px; opacity:0.15; pointer-events:none;" aria-hidden="true">
                                <path d="M22 115 C36 82, 54 52, 82 12"/>
                                <path d="M40 82 C24 72, 18 58, 22 46 C34 52, 42 64, 40 82 Z"/>
                                <path d="M54 60 C72 54, 86 44, 92 32 C76 34, 62 44, 54 60 Z"/>
                                <path d="M64 42 C52 30, 50 18, 56 8 C66 16, 70 28, 64 42 Z"/>
                            </svg>
                        </div>

                        <!-- Right: Numbered Hairline Accordion 01-04 -->
                        <div>
                            @foreach($faqs as $i => $faq)
                                <div class="htl-faq-item">
                                    <button
                                        type="button"
                                        class="htl-faq-trigger"
                                        :aria-expanded="open === {{ $i }}"
                                        @click="open = (open === {{ $i }} ? -1 : {{ $i }})"
                                    >
                                        <span style="display:flex; align-items:baseline; gap:18px;">
                                            <span style="font-size:12px; font-weight:600; color:var(--hv-subtle); font-variant-numeric:tabular-nums;">{{ $faq['num'] }}</span>
                                            <span style="font-size:15px; font-weight:600; line-height:1.4; color:var(--hv-ink);">{{ $faq['q'] }}</span>
                                        </span>
                                        <span class="htl-faq-plus" aria-hidden="true">
                                            <svg x-show="open !== {{ $i }}" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" d="M12 5v14M5 12h14"/>
                                            </svg>
                                            <svg x-show="open === {{ $i }}" x-cloak width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" d="M5 12h14"/>
                                            </svg>
                                        </span>
                                    </button>
                                    <div x-show="open === {{ $i }}" x-transition>
                                        <p style="padding:0 0 22px 34px; font-size:13.5px; line-height:1.75; color:var(--hv-muted); margin:0;">
                                            {{ $faq['a'] }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── 11. SOFT SAGE BENTO GALLERY + SERENE CLOSING CTA (HAVEN BAND 6) ── -->
            <section id="galeri" style="padding-top: 12px; padding-bottom: 84px;">
                <div class="htl-wrap">
                    <!-- Soft Sage Gallery Card -->
                    <div class="htl-sage-box">
                        <div style="text-align:center;">
                            <div class="htl-eyebrow" style="justify-content:center;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c-4 3.5-6 7.5-6 11a6 6 0 0012 0c0-3.5-2-7.5-6-11z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v11"/>
                                </svg>
                                <span>Dokumentasi Autentik</span>
                            </div>
                            <h2 class="htl-sec-h2" style="margin-top:8px;">Galeri Perhotelan</h2>
                        </div>

                        <div class="htl-gallery-layout">
                            <!-- Left Micro Legend -->
                            <div style="display:flex; flex-direction:column; gap:10px; font-size:12px; color:var(--hv-muted);">
                                <span style="font-weight:600; color:var(--hv-ink);">Fokus Praktik</span>
                                <span>Front Office &amp; PMS</span>
                                <span>Suite Housekeeping</span>
                                <span>Barista &amp; Coffee</span>
                                <span>Table Manner 5-Star</span>
                            </div>

                            <!-- Center 5-Tile Rounded Bento Grid -->
                            <div class="htl-bento-grid">
                                <button type="button" class="htl-bento-tile htl-bento-tile--tall htl-photo-hover" @click="openLightbox(0)" aria-label="Buka foto Standar Grooming & Salam Hospitality">
                                    <img src="{{ asset('images/landing/budaya-belajar-bertiga.jpeg') }}" alt="Standar Grooming & Salam Hospitality" class="htl-photo" style="object-position:50% 22%;" loading="lazy">
                                </button>
                                <button type="button" class="htl-bento-tile htl-photo-hover" @click="openLightbox(1)" aria-label="Buka foto Suasana Senja Wikrama Coffee Corner">
                                    <img src="{{ asset('images/web/pilar/pilar-tefa.jpg') }}" alt="Suasana Senja Wikrama Coffee Corner" class="htl-photo" style="object-position:50% 58%;" loading="lazy">
                                </button>
                                <button type="button" class="htl-bento-tile htl-photo-hover" @click="openLightbox(2)" aria-label="Buka foto Praktik Front Office & Resepsionis">
                                    <img src="{{ asset('images/landing/jurusan-hotel.png') }}" alt="Praktik Front Office & Resepsionis" class="htl-photo" style="object-position:50% 20%;" loading="lazy">
                                </button>
                                <button type="button" class="htl-bento-tile htl-photo-hover" @click="openLightbox(3)" aria-label="Buka foto Lingkungan Kampus & TEFA Wikrama">
                                    <img src="{{ asset('images/landing/prestasi-seminar-siswa.jpg') }}" alt="Lingkungan Kampus & TEFA Wikrama" class="htl-photo" style="object-position:50% 45%;" loading="lazy">
                                </button>
                                <button type="button" class="htl-bento-tile htl-photo-hover" @click="openLightbox(4)" aria-label="Buka foto Taman Hijau & Eco-Hospitality Campus">
                                    <img src="{{ asset('images/landing/gedung-sekolah.jpg') }}" alt="Taman Hijau & Eco-Hospitality Campus" class="htl-photo" style="object-position:50% 50%;" loading="lazy">
                                </button>
                            </div>

                            <!-- Right Poetic Micro-Caption -->
                            <div style="font-size:12.5px; line-height:1.68; color:var(--hv-muted);">
                                Momen keramahan, disiplin, dan estetika ruang yang terabadikan di lingkungan {{ $schoolName }}.
                            </div>
                        </div>

                        <div style="margin-top:28px; text-align:center;">
                            <button type="button" class="htl-outline-pill" @click="openLightbox(0)">
                                <span>Lihat Semua Foto (6)</span>
                            </button>
                        </div>
                    </div>

                    <!-- Serene Closing CTA Band -->
                    <div style="margin-top:56px; text-align:center; max-width:680px; margin-left:auto; margin-right:auto;">
                        <span class="htl-eyebrow">Penerimaan Peserta Didik Baru (SPMB)</span>
                        <h2 class="htl-sec-h2" style="margin-top:12px;">
                            Siap menjadi hotelier &amp; barista profesional berkelas dunia?
                        </h2>
                        <p style="margin-top:14px; font-size:14px; line-height:1.75; color:var(--hv-muted);">
                            Kuota kelas Perhotelan terbatas demi menjaga kualitas praktik mock-up room dan intensitas bimbingan barista. Amankan kursi belajarmu sekarang juga.
                        </p>
                        <div style="margin-top:26px; display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:12px;">
                            <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="htl-btn-olive">
                                <span>Daftar SPMB Online Sekarang</span>
                                <span class="htl-btn-olive-circle" aria-hidden="true">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6l6 6-6 6"/>
                                    </svg>
                                </span>
                            </a>
                            <a href="{{ url('/jurusan') }}" class="htl-outline-pill">
                                <span>&larr; Lihat Jurusan Lainnya</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- ── SPOTLIGHT MODAL (FROM HERO RADIAL BADGE) ── -->
        <div x-show="spotlightOpen" x-cloak class="htl-modal-backdrop" @click.self="spotlightOpen = false" role="dialog" aria-modal="true" aria-label="Sorotan Program Perhotelan">
            <div style="background:var(--hv-paper); color:var(--hv-ink); border-radius:24px; max-width:680px; width:100%; overflow:hidden; box-shadow:0 30px 70px rgba(0,0,0,0.45);">
                <div style="position:relative; aspect-ratio:16/9; background:var(--hv-espresso);">
                    <img src="{{ asset('images/landing/jurusan-hotel.png') }}" alt="Siswa Perhotelan {{ $schoolName }}" class="htl-photo" style="width:100%; height:100%; object-fit:cover; object-position:50% 20%;">
                    <button type="button" @click="spotlightOpen = false" aria-label="Tutup sorotan" style="position:absolute; top:14px; right:14px; width:36px; height:36px; border-radius:999px; border:0; background:rgba(27,23,19,0.72); color:#FAF8F4; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                        &times;
                    </button>
                </div>
                <div style="padding:24px 28px 28px;">
                    <span class="htl-eyebrow">Sorotan Kompetensi Perhotelan</span>
                    <h3 class="htl-serif" style="font-size:28px; margin-top:6px; color:var(--hv-ink);">Hospitality Management &amp; Barista Professional</h3>
                    <p style="margin-top:8px; font-size:13.5px; line-height:1.72; color:var(--hv-muted);">
                        Memadukan 6 kompetensi inti: Front Office PMS (VHP &amp; Opera), Certified Barista Art, Executive Housekeeping, Table Manner 5-Star, F&amp;B Service &amp; Banquet, serta English for Hospitality &mdash; lengkap dengan magang 6 bulan di hotel bintang 4 &amp; 5.
                    </p>
                    <div style="margin-top:20px; display:flex; flex-wrap:wrap; gap:10px; align-items:center; justify-content:space-between;">
                        <a href="#pms-tefa" @click="spotlightOpen = false" class="htl-btn-olive">
                            <span>Coba Simulator PMS &amp; NFC</span>
                            <span class="htl-btn-olive-circle" aria-hidden="true">&rarr;</span>
                        </a>
                        <button type="button" @click="spotlightOpen = false" class="htl-outline-pill">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── GALLERY LIGHTBOX MODAL ── -->
        <div x-show="lightboxIdx !== null" x-cloak class="htl-modal-backdrop" @click.self="closeLightbox()" role="dialog" aria-modal="true" aria-label="Galeri Foto Perhotelan">
            <template x-if="lightboxIdx !== null">
                <div style="background:var(--hv-paper); color:var(--hv-ink); border-radius:24px; max-width:820px; width:100%; overflow:hidden; box-shadow:0 30px 70px rgba(0,0,0,0.48);">
                    <div style="position:relative; background:var(--hv-espresso); aspect-ratio:16/10; max-height:62vh;">
                        <img
                            :src="gallery[lightboxIdx].src"
                            :alt="gallery[lightboxIdx].title"
                            style="width:100%; height:100%; object-fit:cover;"
                            :style="'object-position:' + gallery[lightboxIdx].pos"
                        >
                        <button type="button" @click="closeLightbox()" aria-label="Tutup galeri" style="position:absolute; top:14px; right:14px; width:38px; height:38px; border-radius:999px; border:0; background:rgba(27,23,19,0.72); color:#FAF8F4; font-size:20px; cursor:pointer; display:flex; align-items:center; justify-content:center;">&times;</button>
                    </div>
                    <div style="padding:20px 26px; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:14px;">
                        <div>
                            <div style="font-size:11px; font-weight:600; color:var(--hv-olive); letter-spacing:0.08em; text-transform:uppercase;" x-text="'0' + (lightboxIdx + 1) + ' / 0' + gallery.length"></div>
                            <div class="htl-serif" style="font-size:23px; color:var(--hv-ink); margin-top:2px;" x-text="gallery[lightboxIdx].title"></div>
                            <div style="font-size:13px; color:var(--hv-muted); margin-top:2px;" x-text="gallery[lightboxIdx].caption"></div>
                        </div>
                        <div style="display:flex; gap:8px;">
                            <button type="button" class="htl-circle-btn" @click="prevLightbox()" aria-label="Foto sebelumnya">&larr;</button>
                            <button type="button" class="htl-circle-btn" @click="nextLightbox()" aria-label="Foto berikutnya">&rarr;</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</x-layouts.blank>
