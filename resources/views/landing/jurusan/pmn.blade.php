{{--
    ================================================================================
    HALAMAN DETAIL JURUSAN: PEMASARAN / BISNIS DIGITAL (PMN)
    Konsep Desain: Pure-White Borderless Editorial (Setia Referensi Daily Travel)
    Menubar & Footer: Menggunakan layout global Wikrama (<x-layouts.blank>)
    URL Akses: /jurusan/pmn (alias: /jurusan/pemasaran)
    ================================================================================
--}}
<x-layouts.blank title="Pemasaran & Bisnis Digital (PMN)">
    @php
        $sekolah = $sekolah ?? \App\Modules\Sistem\Models\School::current();
        $schoolName = $sekolah->nama ?? 'SMK Wikrama 1 Garut';

        $stats = [
            ['icon' => 'award', 'label' => 'Sertifikasi BNSP & Google', 'value' => '100% Lulusan Tersertifikasi'],
            ['icon' => 'video', 'label' => 'Fasilitas Studio Praktik', 'value' => '3 Studio Live, Ads & Retail'],
            ['icon' => 'bag', 'label' => 'Unit Produksi TEFA', 'value' => '2 Unit Digital Mart & Agency'],
            ['icon' => 'trending', 'label' => 'Keterserapan BMW', 'value' => '99% Kerja, Kuliah & Usaha'],
        ];

        $pillars = [
            [
                'icon' => 'video',
                'title' => 'Live Streaming Studio Berstandar Creator Agency',
                'desc' => 'Lighting profesional, soundcard multi-mic, dan kamera streaming resolusi tinggi untuk siaran komersial nyata.',
            ],
            [
                'icon' => 'bar-chart',
                'title' => 'Praktik Beriklan Riil dengan Budget Nyata',
                'desc' => 'Membaca dashboard analitik ROAS, targeting audiens pixel Meta/TikTok Ads, dan A/B testing konten iklan.',
            ],
            [
                'icon' => 'store',
                'title' => 'Inkubasi Bisnis Mandiri & Unit TEFA',
                'desc' => 'Merilis brand sendiri lewat Wikrama Digital Commerce & Social Ads Hub dengan izin NIB hingga penjualan online.',
            ],
        ];

        $careers = [
            ['icon' => 'trending', 'title' => 'Digital Marketing Specialist', 'desc' => 'Strategi Meta Ads & Google Ads, funneling penjualan, lead generation, dan conversion rate.', 'salary' => 'Rp 6–14 jt', 'meta' => 'Performance Ads', 'rating' => '4.9', 'tint' => '#EDF6FC'],
            ['icon' => 'mic', 'title' => 'Live Streamer Host', 'desc' => 'Memandu siaran TikTok Shop & Shopee Live, presentasi produk persuasif, dan closing flash sale.', 'salary' => 'Rp 5,5–15 jt', 'meta' => 'Live Commerce', 'rating' => '4.9', 'tint' => '#F2F5FA'],
            ['icon' => 'film', 'title' => 'Social Media Creator', 'desc' => 'Riset tren FYP, produksi video pendek reels/TikTok, storytelling, dan pertumbuhan komunitas.', 'salary' => 'Rp 5–12 jt', 'meta' => 'Creative Studio', 'rating' => '4.8', 'tint' => '#EDF6FC'],
            ['icon' => 'cart', 'title' => 'Marketplace Manager', 'desc' => 'Kelola official store Shopee, Tokopedia & Lazada, optimasi etalase, stok, dan kepuasan pembeli.', 'salary' => 'Rp 5,5–12,5 jt', 'meta' => 'E-Commerce Ops', 'rating' => '4.8', 'tint' => '#F2F5FA'],
            ['icon' => 'search', 'title' => 'SEO Growth Specialist', 'desc' => 'Naikkan peringkat bisnis di halaman pertama Google lewat riset keyword, SEO on-page & backlink.', 'salary' => 'Rp 6–13,5 jt', 'meta' => 'Organic Search', 'rating' => '4.8', 'tint' => '#EDF6FC'],
            ['icon' => 'briefcase', 'title' => 'Online Brand Founder', 'desc' => 'Membangun brand retail, fashion, atau kuliner mandiri dengan tim marketing hingga pasar ekspor.', 'salary' => 'Mandiri', 'meta' => 'Technopreneur', 'rating' => '5.0', 'tint' => '#F2F5FA'],
        ];

        $curriculum = [
            [
                'grade' => 'Kelas X',
                'phase' => 'Fase E • Tahun 01',
                'title' => 'Dasar Bisnis & Personal Selling',
                'desc' => 'Membangun karakter sales profesional, riset perilaku konsumen, dan komunikasi persuasif.',
                'featured' => false,
                'items' => [
                    'Prinsip Dasar Ekonomi Bisnis & Administrasi Transaksi',
                    'Teknik Negosiasi & Komunikasi Bisnis Efektif',
                    'Desain Grafis Promosi Produk (Canva & Photoshop)',
                    'Psikologi Konsumen & Copywriting Dasar',
                    'Praktek Kasir & Pengelolaan Ritel Modern',
                ],
            ],
            [
                'grade' => 'Kelas XI',
                'phase' => 'Fase F • Tahun 02',
                'title' => 'E-Commerce, Ads & Live Stream',
                'desc' => 'Penerapan strategi penjualan digital, iklan berbayar media sosial, dan penyiaran live commerce.',
                'featured' => true,
                'items' => [
                    'Operasional Marketplace (Shopee, Tokopedia, TikTok Shop)',
                    'Teknik Live Streaming Host & Penataan Studio Visual',
                    'Iklan Berbayar Meta Ads & TikTok Ads Manager',
                    'Optimasi Search Engine Optimization (SEO) Google',
                    'Operasional TEFA: Wikrama Digital Commerce',
                ],
            ],
            [
                'grade' => 'Kelas XII',
                'phase' => 'Fase F • Tahun 03',
                'title' => 'Magang 6 Bulan & Sertifikasi BNSP',
                'desc' => 'Penerjunan langsung di agensi pemasaran digital, sertifikasi profesi nasional, dan inkubasi usaha.',
                'featured' => false,
                'items' => [
                    'PKL Industri 6 Bulan di Agensi Pemasaran / Retail',
                    'Uji Sertifikasi BNSP Tenaga Pemasar Operasional',
                    'Sertifikasi Google Digital Garage & Meta Associate',
                    'Pameran Karya Bisnis & Business Pitching Final',
                    'Penyaluran BMW (Bekerja, Melanjutkan, Wirausaha)',
                ],
            ],
        ];

        $studios = [
            [
                'id' => 0,
                'cat' => 'live',
                'short' => 'Live Creator Studio',
                'name' => 'Studio Live Streaming TikTok & Shopee',
                'desc' => 'Dilengkapi ring light, softbox 3-titik, standing mic condenser, green screen, dan smartphone flagship khusus siaran langsung e-commerce.',
                'image' => asset('images/landing/jurusan-pmn.jpg'),
                'alt' => 'Siswa Pemasaran praktik live streaming TikTok Shop di studio Wikrama',
            ],
            [
                'id' => 1,
                'cat' => 'ads',
                'short' => 'Ads & SEO Lab',
                'name' => 'Laboratorium Ads Analytics & SEO',
                'desc' => 'Ruang praktik riset keyword Ahrefs/Semrush, setting Meta Business Manager, Google Analytics, dan optimasi toko marketplace.',
                'image' => asset('images/web/hero-pmn.jpg'),
                'alt' => 'Ruang kelas studio pemasaran digital SMK Wikrama 1 Garut',
            ],
            [
                'id' => 2,
                'cat' => 'ads',
                'short' => 'Retail POS Mart',
                'name' => 'Wikrama Retail & POS Cashier Mart',
                'desc' => 'Mesin kasir barcode scanner, software POS terintegrasi, rak display gondola, dan sistem stock opname barang minimarket modern.',
                'image' => asset('images/landing/ekosistem-belajar.png'),
                'alt' => 'Suasana praktikum administrasi transaksi dan bisnis digital',
            ],
            [
                'id' => 3,
                'cat' => 'live',
                'short' => 'Photo & Video Studio',
                'name' => 'Studio Fotografi & Videografi Produk',
                'desc' => 'Lightbox mini, alas putar 360 derajat, lensa makro, dan perlengkapan styling produk kuliner/fashion untuk katalog visual menarik.',
                'image' => asset('images/landing/kelas-digital.jpg'),
                'alt' => 'Dokumentasi prestasi dan karya kreatif siswi SMK Wikrama 1 Garut',
            ],
            [
                'id' => 4,
                'cat' => 'ops',
                'short' => 'Fulfillment Center',
                'name' => 'Logistik & Packing Fulfillment Center',
                'desc' => 'Meja pengemasan pesanan, timbangan digital, printer label resi thermal, bubble wrap dispenser, dan integrasi kurir ekspedisi.',
                'image' => asset('images/landing/suasana-belajar-aktif.jpg'),
                'alt' => 'Ruang pengelolaan data pesanan dan operasi e-commerce siswa',
            ],
            [
                'id' => 5,
                'cat' => 'ops',
                'short' => 'Pitching Room',
                'name' => 'Business Presentation & Pitching Room',
                'desc' => 'Ruang negosiasi bisnis dengan klien UMKM, presentasi proposal sponsorship kegiatan, dan pitching produk di depan dewan penguji.',
                'image' => asset('images/landing/budaya-moving-class.jpeg'),
                'alt' => 'Ruang diskusi dan presentasi proyek bisnis SMK Wikrama 1 Garut',
            ],
        ];

        $certifications = [
            [
                'code' => 'SKKNI',
                'icon' => 'award',
                'title' => 'BNSP Tenaga Pemasar Operasional',
                'issuer' => 'BNSP LSP P-1',
                'desc' => 'Sertifikasi resmi negara untuk kompetensi penetapan harga, pengelolaan etalase, pelayanan pelanggan, dan teknik negosiasi bisnis.',
            ],
            [
                'code' => 'GOOGLE',
                'icon' => 'globe',
                'title' => 'Google Digital Garage Certification',
                'issuer' => 'Google Digital Skills Academy',
                'desc' => 'Sertifikasi internasional dari Google mencakup dasar digital marketing, optimasi mesin pencari Google SEO, dan analitik data web.',
            ],
            [
                'code' => 'META',
                'icon' => 'smartphone',
                'title' => 'Meta Certified Digital Associate',
                'issuer' => 'Meta Blueprint Certification',
                'desc' => 'Pengakuan kompetensi resmi ekosistem Meta dalam mengelola Instagram & Facebook bisnis, strategi konten iklan, dan ads targeting.',
            ],
        ];

        $partners = [
            ['name' => 'Shopee Indonesia', 'role' => 'Marketplace Terbesar'],
            ['name' => 'TikTok Shop', 'role' => 'Live Creator Partner'],
            ['name' => 'Alfamart (SAT)', 'role' => 'Jaringan Ritel Modern'],
            ['name' => 'Indomaret (IDM)', 'role' => 'Ritel & Point of Sale'],
            ['name' => 'J&T / SiCepat', 'role' => 'Logistik E-Commerce'],
            ['name' => 'Digital Agency Jkt', 'role' => 'Performance Marketing'],
        ];

        $faqs = [
            [
                'q' => 'Apakah jurusan Pemasaran hanya belajar berjualan keliling?',
                'a' => 'Sama sekali tidak. Pemasaran di Wikrama berfokus pada Bisnis Digital (Digital Marketing), optimasi iklan berbayar online (Meta & TikTok Ads), optimasi mesin pencari Google (SEO), dan siaran live streaming studio profesional.',
            ],
            [
                'q' => 'Saya orangnya pemalu, apakah bisa mengikuti jurusan ini?',
                'a' => 'Sangat bisa. Di jurusan ini ada bimbingan public speaking bertahap dari dasar, pelatihan grooming, dan olah vokal. Banyak santri yang awalnya pendiam justru bertransformasi menjadi host live shopping yang luwes dan percaya diri.',
            ],
            [
                'q' => 'Apakah siswa harus mengeluarkan modal uang pribadi untuk berbisnis?',
                'a' => 'Tidak perlu modal besar. Sekolah menyediakan produk dari unit Teaching Factory (TEFA) dan UMKM mitra sekolah untuk dipasarkan oleh siswa dengan sistem dropship dan affiliate marketing, sehingga siswa bisa menghasilkan komisi tanpa modal stok barang.',
            ],
            [
                'q' => 'Apakah lulusan Pemasaran bisa melanjutkan kuliah ke Perguruan Tinggi?',
                'a' => 'Tentu bisa. Lulusan dapat melanjutkan ke jurusan Manajemen Bisnis, Ilmu Komunikasi, Administrasi Niaga, Digital Marketing, atau Akuntansi di perguruan tinggi seperti UNPAD, UPI, Telkom University, dan Universitas Indonesia.',
            ],
        ];
    @endphp

    <style>
        .dt-page {
            --dt-white: #FFFFFF;
            --dt-navy: #0D213F;
            --dt-navy-hi: #1A3660;
            --dt-sky: #2EA3DD;
            --dt-sky-wash: #E2F1FA;
            --dt-soft: #F3F5F8;
            --dt-ink: #111827;
            --dt-sub: #64748B;
            --dt-muted: #94A3B8;
            --dt-line: #E8EDF4;
            --dt-gold: #F5B727;
            --dt-ease: cubic-bezier(0.22, 1, 0.36, 1);

            background-color: var(--dt-white);
            color: var(--dt-ink);
            position: relative;
            overflow-x: hidden;
        }

        .dt-mono {
            font-family: 'Roboto Mono', ui-monospace, SFMono-Regular, Menlo, monospace;
            font-variant-numeric: tabular-nums;
        }

        .dt-container {
            max-width: 1120px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 24px;
            padding-right: 24px;
        }
        @media (min-width: 1024px) {
            .dt-container {
                padding-left: 32px;
                padding-right: 32px;
            }
        }

        /* Pure-White Editorial Typography matching Daily Travel */
        .dt-h1 {
            font-size: 36px;
            line-height: 1.16;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #0F172A;
        }
        @media (min-width: 640px) {
            .dt-h1 { font-size: 46px; }
        }
        @media (min-width: 1024px) {
            .dt-h1 { font-size: 50px; }
        }

        .dt-h2 {
            font-size: 26px;
            line-height: 1.22;
            font-weight: 700;
            letter-spacing: -0.015em;
            color: #0F172A;
        }
        @media (min-width: 640px) {
            .dt-h2 { font-size: 34px; }
        }
        .dt-h2-thin {
            font-weight: 400;
            color: #334155;
        }

        /* Signature Compact Daily Travel Buttons */
        .dt-btn-navy {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 42px;
            padding: 0 22px;
            border-radius: 6px;
            background-color: var(--dt-navy);
            color: #FFFFFF;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 160ms var(--dt-ease), transform 160ms var(--dt-ease);
        }
        .dt-btn-navy:hover {
            background-color: var(--dt-navy-hi);
            transform: translateY(-1px);
        }

        .dt-btn-ghost {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 42px;
            padding: 0 18px;
            border-radius: 6px;
            background-color: var(--dt-soft);
            color: var(--dt-navy);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 160ms var(--dt-ease);
        }
        .dt-btn-ghost:hover {
            background-color: #E5EBF3;
        }

        /* Hero Dashed Sky-Blue Circle Medallion + Soft Watercolor Cloud Wash */
        .dt-hero-circle-wrap {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 16px 0;
        }
        .dt-hero-wash {
            position: absolute;
            width: 390px;
            height: 390px;
            border-radius: 9999px;
            background: radial-gradient(circle at 50% 48%, rgba(125, 204, 242, 0.34) 0%, rgba(196, 232, 249, 0.22) 48%, rgba(255, 255, 255, 0) 74%);
            pointer-events: none;
            z-index: 0;
        }
        .dt-hero-ring {
            position: relative;
            z-index: 2;
            width: 300px;
            height: 300px;
            border-radius: 9999px;
            border: 2px dashed var(--dt-sky);
            padding: 14px;
            background-color: rgba(255, 255, 255, 0.45);
        }
        @media (min-width: 640px) {
            .dt-hero-ring {
                width: 360px;
                height: 360px;
                padding: 16px;
            }
        }
        .dt-hero-photo {
            width: 100%;
            height: 100%;
            border-radius: 9999px;
            overflow: hidden;
            box-shadow: 0 20px 44px rgba(13, 33, 63, 0.14);
        }

        /* Real Organic Painterly Brush-Stroke Photo Mask (Exact Signature from Reference!) */
        .dt-brush-photo {
            width: 100%;
            aspect-ratio: 16 / 11;
            overflow: hidden;
            -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 600 420' preserveAspectRatio='none'%3E%3Cpath d='M52,36 C128,12 245,8 370,16 C458,22 528,16 566,42 C586,56 544,82 574,108 C602,132 588,168 564,196 C546,216 594,244 584,280 C574,316 542,326 558,354 C570,376 502,396 426,404 C324,414 196,406 108,394 C52,386 14,368 30,338 C46,308 8,284 18,248 C28,214 58,202 24,172 C-4,148 16,112 42,92 C62,76 18,48 52,36 Z' fill='black'/%3E%3C/svg%3E");
            mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 600 420' preserveAspectRatio='none'%3E%3Cpath d='M52,36 C128,12 245,8 370,16 C458,22 528,16 566,42 C586,56 544,82 574,108 C602,132 588,168 564,196 C546,216 594,244 584,280 C574,316 542,326 558,354 C570,376 502,396 426,404 C324,414 196,406 108,394 C52,386 14,368 30,338 C46,308 8,284 18,248 C28,214 58,202 24,172 C-4,148 16,112 42,92 C62,76 18,48 52,36 Z' fill='black'/%3E%3C/svg%3E");
            -webkit-mask-size: 100% 100%;
            mask-size: 100% 100%;
            -webkit-mask-repeat: no-repeat;
            mask-repeat: no-repeat;
        }
        .dt-brush-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 500ms var(--dt-ease);
        }
        .dt-brush-photo:hover img {
            transform: scale(1.03);
        }

        /* Borderless Spec Underline Field (matching "Location / Date / Price") */
        .dt-spec-field {
            padding-bottom: 14px;
            border-bottom: 1px solid var(--dt-line);
        }

        /* Dark Navy Search / Filter Pill Bar (matching "Search Destination") */
        .dt-navy-bar {
            display: inline-flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 5px 6px;
            border-radius: 9999px;
            background-color: var(--dt-navy);
            box-shadow: 0 10px 26px rgba(13, 33, 63, 0.16);
        }
        .dt-navy-tab {
            padding: 7px 16px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.78);
            cursor: pointer;
            transition: background-color 160ms var(--dt-ease), color 160ms var(--dt-ease);
        }
        .dt-navy-tab:hover {
            color: #FFFFFF;
        }
        .dt-navy-tab.is-active {
            background-color: #FFFFFF;
            color: var(--dt-navy);
            font-weight: 600;
        }

        /* Signature 4-Card Tilted Full-Bleed Photo Fan (matching "Find Your Best Destination") */
        .dt-fan-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }
        @media (min-width: 1024px) {
            .dt-fan-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 20px;
                padding: 18px 8px;
            }
        }
        .dt-fan-tile {
            position: relative;
            aspect-ratio: 1 / 1;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 18px 36px rgba(13, 33, 63, 0.16);
            cursor: pointer;
            text-align: left;
            border: 3px solid #FFFFFF;
            transition: transform 280ms var(--dt-ease), box-shadow 280ms var(--dt-ease);
        }
        @media (min-width: 1024px) {
            .dt-fan-tile.fan-0 { transform: rotate(-5.5deg) translateY(8px); }
            .dt-fan-tile.fan-1 { transform: rotate(-1.8deg) translateY(0px); }
            .dt-fan-tile.fan-2 { transform: rotate(2deg) translateY(2px); }
            .dt-fan-tile.fan-3 { transform: rotate(5.5deg) translateY(10px); }
            .dt-fan-tile:hover,
            .dt-fan-tile.is-picked {
                transform: rotate(0deg) translateY(-8px) scale(1.02);
                box-shadow: 0 26px 48px rgba(13, 33, 63, 0.24);
                z-index: 10;
            }
        }
        .dt-fan-tile img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .dt-fan-scrim {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(13, 33, 63, 0) 40%, rgba(13, 33, 63, 0.84) 100%);
            pointer-events: none;
        }
        .dt-fan-label {
            position: absolute;
            left: 16px;
            right: 16px;
            bottom: 16px;
            z-index: 5;
            color: #FFFFFF;
        }

        /* Borderless Editorial Career Item (matching "Best Vacation Plan") */
        .dt-plan-thumb {
            height: 126px;
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform 220ms var(--dt-ease);
        }
        .dt-plan-item:hover .dt-plan-thumb {
            transform: translateY(-3px);
        }

        /* Borderless 3-Column Curriculum Dividers */
        .dt-curr-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 36px;
        }
        @media (min-width: 1024px) {
            .dt-curr-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 0;
            }
            .dt-curr-col {
                padding: 0 28px;
                border-right: 1px solid var(--dt-line);
            }
            .dt-curr-col:first-child { padding-left: 0; }
            .dt-curr-col:last-child { padding-right: 0; border-right: none; }
        }

        /* Full-Bleed Right Split CTA Banner (matching "We Make World Travel Easy") */
        .dt-split-cta {
            display: grid;
            grid-template-columns: 1fr;
            border-top: 1px solid var(--dt-line);
        }
        @media (min-width: 1024px) {
            .dt-split-cta {
                grid-template-columns: 1fr 1.15fr;
                min-height: 410px;
            }
        }
        .dt-split-cta-right {
            background: linear-gradient(180deg, #DDF0FA 0%, #CEE7F5 100%);
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding: 40px 24px 0 24px;
            overflow: hidden;
            position: relative;
        }
        .dt-phone-big {
            width: 100%;
            max-width: 310px;
            background-color: #0F172A;
            border-radius: 34px 34px 0 0;
            padding: 12px 12px 0 12px;
            box-shadow: 0 -10px 40px rgba(13, 33, 63, 0.18);
        }
        .dt-phone-inner {
            border-radius: 24px 24px 0 0;
            overflow: hidden;
            background-color: #FFFFFF;
        }

        /* Demo Modal */
        .dt-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 95;
            background-color: rgba(13, 33, 63, 0.58);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .dt-modal-panel {
            width: 100%;
            max-width: 460px;
            background-color: #FFFFFF;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 28px 70px rgba(13, 33, 63, 0.28);
        }

        .dt-faq-item {
            border-bottom: 1px solid var(--dt-line);
        }
        .dt-faq-chevron {
            transition: transform 200ms var(--dt-ease);
        }
        .dt-faq-chevron.is-open {
            transform: rotate(180deg);
        }
    </style>

    <div class="dt-page" x-data="{ demoOpen: false }" @keydown.escape.window="demoOpen = false">
        <!-- ═══════════════════════════════════════════════════════════════════════
             SECTION 1: HERO — ULTRA-CLEAN PURE WHITE + DASHED SKY CIRCLE MEDALLION
             (EXACT HOMAGE TO "Travel Memories You'll Never Forget")
             ═══════════════════════════════════════════════════════════════════════ -->
        <header class="pt-10 pb-16 lg:pt-16 lg:pb-24 bg-white relative">
            <!-- Ultra-faint Decorative Editorial Watermark Accent (Top-Left) -->
            <svg class="absolute top-6 left-0 w-40 h-40 pointer-events-none select-none" style="opacity: 0.045;" viewBox="0 0 200 200" fill="none" aria-hidden="true">
                <circle cx="40" cy="80" r="72" stroke="#0D213F" stroke-width="1.5" stroke-dasharray="6 6"/>
                <path d="M12 128 C60 88, 112 108, 174 52" stroke="#0D213F" stroke-width="1.5"/>
            </svg>

            <div class="dt-container relative z-10">
                <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs mb-10" style="color: #94A3B8;">
                    <a href="{{ url('/') }}" class="hover:text-slate-900 transition-colors">Beranda</a>
                    <span>/</span>
                    <a href="{{ url('/jurusan') }}" class="hover:text-slate-900 transition-colors">Jurusan</a>
                    <span>/</span>
                    <span style="color: #0D213F; font-weight: 600;">Pemasaran (PMN)</span>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    <!-- Left: Pure Clean Headline, Short Copy & 2 Compact Buttons -->
                    <div class="lg:col-span-6">
                        <h1 class="dt-h1">
                            Pemasaran &amp; Bisnis<br>Digital Kreatif
                        </h1>

                        <p class="mt-5 text-sm sm:text-base leading-relaxed max-w-md" style="color: #64748B;">
                            Mencetak talenta digital marketing, live streamer profesional, content creator, dan technopreneur yang siap menghasilkan omzet sejak bangku sekolah di {{ $schoolName }}.
                        </p>

                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="dt-btn-navy">
                                <span>Daftar Sekarang</span>
                            </a>
                            <button type="button" @click="demoOpen = true" class="dt-btn-ghost">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                </svg>
                                <span>Play Demo</span>
                            </button>
                        </div>
                    </div>

                    <!-- Right: Clean Sky-Blue Dashed Medallion Photo + Watercolor Wash -->
                    <div class="lg:col-span-6 dt-hero-circle-wrap">
                        <div class="dt-hero-wash" aria-hidden="true"></div>
                        <div class="dt-hero-ring">
                            <div class="dt-hero-photo">
                                <img src="{{ asset('images/landing/jurusan-pmn.jpg') }}"
                                     alt="Siswa Pemasaran SMK Wikrama 1 Garut praktik live commerce TikTok Shop"
                                     class="w-full h-full object-cover"
                                     fetchpriority="high"
                                     decoding="async">
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-2 text-xs relative z-10" style="color: #64748B;">
                            <span class="w-2 h-2 rounded-full" style="background-color: #2EA3DD;"></span>
                            <span>Wikrama Live Creator Studio &bull; Terakreditasi A</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 2: STATS STRIP — BORDERLESS UNDERLINE SPEC ROW
                 (ECHOING "Location / Date / Price" CLEAN FIELDS)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-10 bg-white" aria-label="Ringkasan capaian PMN">
                <div class="dt-container">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                        @foreach($stats as $stat)
                            <div class="dt-spec-field">
                                <div class="flex items-center gap-2 text-xs font-semibold mb-1.5" style="color: #0D213F;">
                                    @if($stat['icon'] === 'award')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="6"/><path stroke-linecap="round" stroke-linejoin="round" d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                                    @elseif($stat['icon'] === 'video')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><rect width="15" height="12" x="2" y="6" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="m17 10 5-3v10l-5-3"/></svg>
                                    @elseif($stat['icon'] === 'bag')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/></svg>
                                    @else
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17 9 11l4 4 8-8"/></svg>
                                    @endif
                                    <span>{{ $stat['label'] }}</span>
                                </div>
                                <div class="text-sm font-medium" style="color: #64748B;">{{ $stat['value'] }}</div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Clean Unboxed Core Skills Line -->
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-xs" style="color: #94A3B8;">
                        <span>Live Streamer Host</span>
                        <span>&bull;</span>
                        <span>Meta &amp; TikTok Ads</span>
                        <span>&bull;</span>
                        <span>Google SEO Specialist</span>
                        <span>&bull;</span>
                        <span>Content Studio Creator</span>
                        <span>&bull;</span>
                        <span>E-Commerce Management</span>
                        <span>&bull;</span>
                        <span>AI Business Copywriting</span>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 3: PROFIL PROGRAM — BRUSH-STROKE PHOTO LEFT + BORDERLESS SPEC RIGHT
                 (EXACT HOMAGE TO BAND 2 OF DAILY TRAVEL)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-16 lg:py-24 bg-white" id="profil">
                <div class="dt-container">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                        <!-- Left: Organic Painterly Brush-Stroke Masked Photo -->
                        <div class="lg:col-span-6">
                            <div class="dt-brush-photo">
                                <img src="{{ asset('images/web/hero-pmn.jpg') }}"
                                     alt="Suasana praktikum bisnis digital siswa Pemasaran SMK Wikrama 1 Garut"
                                     loading="lazy"
                                     decoding="async">
                            </div>
                        </div>

                        <!-- Right: Borderless Editorial Story, Underline Pillars & Compact Button Pair -->
                        <div class="lg:col-span-6">
                            <h2 class="dt-h2">
                                Mengapa Memilih <span class="dt-h2-thin">Pemasaran Wikrama</span>
                            </h2>

                            <p class="mt-4 text-sm leading-relaxed" style="color: #64748B;">
                                Jurusan Pemasaran (Bisnis Digital) di {{ $schoolName }} mendobrak paradigma lama ilmu dagang konvensional. Siswa mempraktikkan langsung social commerce, iklan berbayar Meta &amp; TikTok Ads, optimasi Google SEO, product copywriting, serta operasional POS ritel modern.
                            </p>

                            <!-- 3 Borderless Underline Pillars -->
                            <div class="mt-6 space-y-4">
                                @foreach($pillars as $pillar)
                                    <div class="dt-spec-field">
                                        <div class="flex items-center gap-2.5 text-sm font-semibold" style="color: #0D213F;">
                                            @if($pillar['icon'] === 'video')
                                                <svg class="w-4 h-4 shrink-0" style="color: #2EA3DD;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><rect width="15" height="12" x="2" y="6" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="m17 10 5-3v10l-5-3"/></svg>
                                            @elseif($pillar['icon'] === 'bar-chart')
                                                <svg class="w-4 h-4 shrink-0" style="color: #2EA3DD;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20V10m6 10V4M6 20v-4"/></svg>
                                            @else
                                                <svg class="w-4 h-4 shrink-0" style="color: #2EA3DD;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9 5 4h14l2 5M4 9h16v11H4V9Z"/></svg>
                                            @endif
                                            <span>{{ $pillar['title'] }}</span>
                                        </div>
                                        <p class="mt-1 text-xs leading-relaxed pl-6" style="color: #64748B;">{{ $pillar['desc'] }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Compact 2-Button Pair (echoing "Preview Hotel" + "Book Now") -->
                            <div class="mt-6 flex flex-wrap items-center gap-3">
                                <a href="#lab" class="dt-btn-ghost">Lihat Studio</a>
                                <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="dt-btn-navy">Daftar PMN</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 4: KOLASE STUDIO — TILTED FULL-BLEED PHOTO FAN + DARK NAVY BAR
                 (EXACT HOMAGE TO "Find Your Best Destination")
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-16 lg:py-24 bg-white" id="lab" x-data="{ cat: 'all', picked: 0, showAll: false }">
                <div class="dt-container">
                    <div class="text-center max-w-xl mx-auto">
                        <h2 class="dt-h2">
                            Temukan Studio <span class="dt-h2-thin">Praktik Terbaikmu</span>
                        </h2>
                        <p class="mt-2.5 text-sm leading-relaxed" style="color: #64748B;">
                            Seluruh ruang praktik dirancang menyerupai agensi periklanan, live shopping hub, dan toko retail sesungguhnya.
                        </p>

                        <!-- Signature Dark Navy Rounded Filter Bar -->
                        <div class="mt-6">
                            <div class="dt-navy-bar">
                                <button type="button" @click="cat = 'all'; showAll = false" class="dt-navy-tab" :class="{ 'is-active': cat === 'all' }">Semua Studio</button>
                                <button type="button" @click="cat = 'live'; showAll = true" class="dt-navy-tab" :class="{ 'is-active': cat === 'live' }">Live &amp; Konten</button>
                                <button type="button" @click="cat = 'ads'; showAll = true" class="dt-navy-tab" :class="{ 'is-active': cat === 'ads' }">Ads &amp; Retail</button>
                                <button type="button" @click="cat = 'ops'; showAll = true" class="dt-navy-tab" :class="{ 'is-active': cat === 'ops' }">Logistik &amp; Pitching</button>
                            </div>
                        </div>
                    </div>

                    <!-- Playful Tilted 4-Card Full-Bleed Photo Row (expandable to all 6 via "Lihat semua") -->
                    <div class="mt-10 dt-fan-grid">
                        @foreach($studios as $idx => $studio)
                            <button type="button"
                                    x-show="(cat === 'all' && (showAll || {{ $idx }} < 4)) || cat === '{{ $studio['cat'] }}'"
                                    @click="picked = {{ $studio['id'] }}"
                                    class="dt-fan-tile fan-{{ $idx % 4 }}"
                                    :class="{ 'is-picked': picked === {{ $studio['id'] }} }">
                                <img src="{{ $studio['image'] }}" alt="{{ $studio['alt'] }}" loading="lazy" decoding="async">
                                <div class="dt-fan-scrim"></div>
                                <div class="dt-fan-label">
                                    <div class="text-sm font-bold leading-snug">{{ $studio['short'] }}</div>
                                </div>
                            </button>
                        @endforeach
                    </div>

                    <!-- Quiet Active Studio Caption Below Fan (no heavy box!) -->
                    <div class="mt-8 max-w-2xl mx-auto text-center">
                        @foreach($studios as $studio)
                            <div x-show="picked === {{ $studio['id'] }}" @if($studio['id'] !== 0) x-cloak @endif>
                                <div class="text-sm font-bold" style="color: #0D213F;">{{ $studio['name'] }}</div>
                                <p class="mt-1 text-xs sm:text-sm leading-relaxed" style="color: #64748B;">{{ $studio['desc'] }}</p>
                            </div>
                        @endforeach

                        <div class="mt-5" x-show="cat === 'all'">
                            <button type="button"
                                    @click="showAll = !showAll"
                                    class="text-xs font-semibold underline cursor-pointer"
                                    style="color: #0D213F;"
                                    x-text="showAll ? 'Tampilkan ringkas (4 studio)' : 'Lihat semua 6 studio'">
                                Lihat semua 6 studio
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 5: PROSPEK KARIER — BORDERLESS 2-LINE CARDS
                 (EXACT HOMAGE TO "Best Vacation Plan")
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-16 lg:py-24 bg-white" id="karir">
                <div class="dt-container">
                    <div class="text-center max-w-xl mx-auto mb-12">
                        <h2 class="dt-h2">
                            Prospek <span class="dt-h2-thin">Karier Lulusan</span>
                        </h2>
                        <p class="mt-2.5 text-sm leading-relaxed" style="color: #64748B;">
                            Era digitalisasi bisnis membuka ribuan lapangan kerja baru untuk spesialis e-commerce, live host, dan media buyer di berbagai brand ternama.
                        </p>
                    </div>

                    <!-- Borderless 3-Column Editorial Grid directly on #FFFFFF -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($careers as $career)
                            <article class="dt-plan-item">
                                <!-- Clean Soft Visual Top (No Border, Rounded 16px) -->
                                <div class="dt-plan-thumb" style="background-color: {{ $career['tint'] }}; color: #0D213F;">
                                    @if($career['icon'] === 'trending')
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17 9 11l4 4 8-8m0 0h-5m5 0v5"/></svg>
                                    @elseif($career['icon'] === 'mic')
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><rect width="6" height="12" x="9" y="2" rx="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M5 10v2a7 7 0 0 0 14 0v-2M12 19v3"/></svg>
                                    @elseif($career['icon'] === 'film')
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="m10 9 5 3-5 3V9Z"/></svg>
                                    @elseif($career['icon'] === 'cart')
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 3h3l2.4 12.2a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 7H6"/></svg>
                                    @elseif($career['icon'] === 'search')
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" stroke-linejoin="round" d="m20 20-3.5-3.5"/></svg>
                                    @else
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><rect width="20" height="14" x="2" y="7" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                    @endif
                                    <span class="dt-mono text-xs" style="color: #52627A;">{{ $career['meta'] }}</span>
                                </div>

                                <!-- Exact Daily Travel 2-Row Clean Caption Below -->
                                <div class="mt-3.5 flex items-baseline justify-between gap-2">
                                    <h3 class="text-sm sm:text-base font-bold" style="color: #0D213F;">{{ $career['title'] }}</h3>
                                    <span class="dt-mono text-xs font-bold shrink-0" style="color: #0D213F;">{{ $career['salary'] }}</span>
                                </div>

                                <div class="mt-1 flex items-center justify-between gap-2 text-xs" style="color: #94A3B8;">
                                    <span>{{ $career['meta'] }}</span>
                                    <span class="inline-flex items-center gap-1 font-semibold" style="color: #64748B;">
                                        <svg class="w-3.5 h-3.5" style="color: #F5B727;" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <span>{{ $career['rating'] }}</span>
                                    </span>
                                </div>

                                <p class="mt-2 text-xs leading-relaxed" style="color: #64748B;">{{ $career['desc'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 6: KURIKULUM 3 TAHUN — BORDERLESS 3-COLUMN EDITORIAL
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-16 lg:py-24 bg-white" id="kurikulum" style="border-top: 1px solid #F1F5F9;">
                <div class="dt-container">
                    <div class="text-center max-w-xl mx-auto mb-14">
                        <h2 class="dt-h2">
                            Kurikulum <span class="dt-h2-thin">3 Tahun Industri</span>
                        </h2>
                        <p class="mt-2.5 text-sm leading-relaxed" style="color: #64748B;">
                            Disusun bersama agensi digital marketing nasional dan marketplace terkemuka untuk melatih kecakapan bisnis nyata.
                        </p>
                    </div>

                    <div class="dt-curr-grid">
                        @foreach($curriculum as $step)
                            <div class="dt-curr-col">
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="dt-mono text-xs" style="color: #2EA3DD;">{{ $step['phase'] }}</span>
                                    <span class="text-xs font-bold" style="color: #0D213F;">{{ $step['grade'] }}</span>
                                </div>
                                <h3 class="text-lg font-bold" style="color: #0D213F;">{{ $step['title'] }}</h3>
                                <p class="mt-2 text-xs sm:text-sm leading-relaxed" style="color: #64748B;">{{ $step['desc'] }}</p>

                                <ul class="mt-5 pt-5 space-y-2.5 text-xs sm:text-sm" style="border-top: 1px solid #E8EDF4; color: #334155;">
                                    @foreach($step['items'] as $item)
                                        <li class="flex items-start gap-2.5">
                                            <svg class="w-4 h-4 shrink-0 mt-0.5" style="color: #2EA3DD;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/>
                                            </svg>
                                            <span>{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 7: SERTIFIKASI KOMPETENSI RESMI — BORDERLESS EDITORIAL STRIP
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-16 lg:py-20 bg-white" id="sertifikasi" style="border-top: 1px solid #F1F5F9;">
                <div class="dt-container">
                    <div class="text-center max-w-xl mx-auto mb-12">
                        <h2 class="dt-h2">
                            Sertifikasi <span class="dt-h2-thin">Kompetensi Resmi</span>
                        </h2>
                        <p class="mt-2.5 text-sm leading-relaxed" style="color: #64748B;">
                            Setiap lulusan dibekali sertifikat kompetensi berlogo Garuda BNSP dan sertifikasi digital marketing dari Google serta Meta.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        @foreach($certifications as $cert)
                            <div>
                                <div class="flex items-center justify-between gap-2 pb-3 mb-4" style="border-bottom: 1px solid #E8EDF4;">
                                    <span class="dt-mono text-xs font-bold" style="color: #0D213F;">{{ $cert['code'] }}</span>
                                    <span class="text-xs" style="color: #2EA3DD;">{{ $cert['issuer'] }}</span>
                                </div>
                                <h3 class="text-base font-bold" style="color: #0D213F;">{{ $cert['title'] }}</h3>
                                <p class="mt-2 text-xs sm:text-sm leading-relaxed" style="color: #64748B;">{{ $cert['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 8: MITRA DUDI & TEMPAT MAGANG — CLEAN TYPOGRAPHIC STRIP
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-14 bg-white" id="mitra" style="border-top: 1px solid #F1F5F9;">
                <div class="dt-container">
                    <div class="text-center max-w-xl mx-auto mb-10">
                        <h2 class="dt-h2">
                            Mitra PKL <span class="dt-h2-thin">&amp; Rekrutmen</span>
                        </h2>
                        <p class="mt-2 text-xs sm:text-sm" style="color: #64748B;">
                            Siswa menjalani magang nyata selama 6 bulan di marketplace, agensi periklanan, dan jaringan retail modern nasional.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6 text-center">
                        @foreach($partners as $partner)
                            <div class="py-3" style="border-bottom: 1px solid #E8EDF4;">
                                <div class="text-sm font-bold" style="color: #0D213F;">{{ $partner['name'] }}</div>
                                <div class="text-xs mt-1" style="color: #94A3B8;">{{ $partner['role'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 9: TESTIMONI ALUMNI — BRUSH-STROKE PHOTO LEFT + STORY RIGHT
                 (EXACT HOMAGE TO "Our Blog / Beautiful Kashmir")
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-16 lg:py-24 bg-white" id="alumni">
                <div class="dt-container">
                    <div class="text-center max-w-xl mx-auto mb-14">
                        <h2 class="dt-h2">
                            Cerita <span class="dt-h2-thin">Alumni Pemasaran</span>
                        </h2>
                        <p class="mt-2 text-sm" style="color: #64748B;">
                            Wawasan nyata dari perjalanan lulusan Pemasaran {{ $schoolName }} di industri digital.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                        <!-- Left: Painterly Brush-Stroke Masked Alumni Photo -->
                        <div class="lg:col-span-5">
                            <div class="dt-brush-photo" style="aspect-ratio: 4 / 3;">
                                <img src="{{ asset('images/web/alumni-auriel.png') }}"
                                     alt="Auriel Salsabila, Alumni Pemasaran SMK Wikrama 1 Garut"
                                     class="w-full h-full object-cover object-top"
                                     loading="lazy"
                                     decoding="async">
                            </div>
                        </div>

                        <!-- Right: Pure Borderless Editorial Story -->
                        <div class="lg:col-span-7">
                            <div class="flex items-center gap-1 mb-3" style="color: #F5B727;" aria-label="Penilaian 5 dari 5">
                                @for($i = 0; $i < 5; $i++)
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>

                            <h3 class="text-xl sm:text-2xl font-bold" style="color: #0D213F;">
                                Auriel Salsabila &mdash; Head Live Streamer &amp; Social Commerce Lead
                            </h3>

                            <blockquote class="mt-4 text-sm sm:text-base leading-relaxed" style="color: #64748B;">
                                &ldquo;Di Pemasaran SMK Wikrama 1 Garut, kami diajari ilmu yang relevan dengan zaman sekarang: live streaming TikTok, setting iklan Meta Ads, dan branding produk. Saya terlatih berbicara percaya diri di depan kamera, sehingga saat magang langsung ditunjuk jadi Head Live Streamer brand fashion dengan penghasilan belasan juta per bulan.&rdquo;
                            </blockquote>

                            <div class="mt-5">
                                <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-xs font-semibold" style="color: #0D213F;">
                                    <span>Mulai ceritamu di Pemasaran</span>
                                    <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 10: FAQ — PURE WHITE BORDERLESS HAIRLINE ACCORDION
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-16 lg:py-20 bg-white" id="faq" style="border-top: 1px solid #F1F5F9;">
                <div class="dt-container">
                    <div class="max-w-3xl mx-auto">
                        <div class="text-center mb-10">
                            <h2 class="dt-h2">
                                Pertanyaan <span class="dt-h2-thin">Umum</span>
                            </h2>
                            <p class="mt-2 text-sm" style="color: #64748B;">
                                Hal yang paling sering ditanyakan seputar jurusan Pemasaran &amp; Bisnis Digital.
                            </p>
                        </div>

                        <div x-data="{ openFaq: 0 }" style="border-top: 1px solid #E8EDF4;">
                            @foreach($faqs as $idx => $faq)
                                <div class="dt-faq-item">
                                    <button type="button"
                                            @click="openFaq = openFaq === {{ $idx }} ? null : {{ $idx }}"
                                            :aria-expanded="openFaq === {{ $idx }}"
                                            class="w-full py-5 flex items-center justify-between gap-4 text-left cursor-pointer">
                                        <span class="text-sm sm:text-base font-semibold" style="color: #0D213F;">{{ $faq['q'] }}</span>
                                        <svg class="w-4 h-4 shrink-0 dt-faq-chevron"
                                             style="color: #2EA3DD;"
                                             :class="{ 'is-open': openFaq === {{ $idx }} }"
                                             fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
                                        </svg>
                                    </button>
                                    <div x-show="openFaq === {{ $idx }}" x-cloak class="pb-5 pr-6">
                                        <p class="text-xs sm:text-sm leading-relaxed" style="color: #64748B;">{{ $faq['a'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 11: FULL-BLEED 50/50 SPLIT CTA BANNER
                 (EXACT HOMAGE TO "We Make World Travel Easy" + FULL-BLEED SKY PHONE PANEL)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="dt-split-cta bg-white">
                <!-- Left 50%: Pure White Centered Editorial CTA -->
                <div class="flex items-center justify-center px-6 py-16 lg:px-12 lg:py-20 bg-white">
                    <div class="max-w-md text-center lg:text-left">
                        <h2 class="dt-h2">
                            Kami Mempermudah<br>Langkah Bisnis Digitalmu
                        </h2>
                        <p class="mt-4 text-sm leading-relaxed" style="color: #64748B;">
                            Kuota kelas Pemasaran terbatas demi menjaga intensitas bimbingan praktik studio live streaming dan proyek inkubasi usaha bersama praktisi.
                        </p>
                        <div class="mt-6 flex flex-wrap items-center justify-center lg:justify-start gap-4">
                            <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="dt-btn-navy">
                                <span>Daftar SPMB Online</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                            <a href="{{ url('/jurusan') }}" class="text-xs font-semibold inline-flex items-center gap-1.5" style="color: #0D213F;">
                                <span>Lihat Jurusan Lain</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right 50%: Full-Bleed Soft Sky-Blue Panel + Rising Smartphone Live Studio -->
                <div class="dt-split-cta-right">
                    <div class="dt-phone-big">
                        <div class="dt-phone-inner">
                            <img src="{{ asset('images/landing/jurusan-pmn.jpg') }}"
                                 alt="Wikrama Live Creator Studio Pemasaran"
                                 class="w-full object-cover"
                                 style="aspect-ratio: 4 / 3;"
                                 loading="lazy"
                                 decoding="async">
                            <div class="p-4 flex items-center justify-between text-xs bg-white">
                                <div>
                                    <div class="font-bold" style="color: #0D213F;">Wikrama Live Studio</div>
                                    <div style="color: #64748B;">TikTok Shop &amp; Shopee Partner</div>
                                </div>
                                <span class="dt-mono font-bold px-2 py-1 rounded" style="background-color: #E2F1FA; color: #0D213F;">ROAS 5.2x</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- ═══════════════════════════════════════════════════════════════════════
             INTERACTIVE "PLAY DEMO" LIVE CREATOR STUDIO MODAL (LAUNCHED FROM HERO)
             ═══════════════════════════════════════════════════════════════════════ -->
        <div x-show="demoOpen"
             x-cloak
             role="dialog"
             aria-modal="true"
             class="dt-modal-backdrop"
             @click.self="demoOpen = false">
            <div class="dt-modal-panel">
                <div class="px-5 py-4 flex items-center justify-between" style="background-color: #0D213F; color: #FFFFFF;">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold px-2 py-0.5 rounded" style="background-color: #EF4444; color: #FFFFFF;">LIVE</span>
                        <span class="text-sm font-semibold">Wikrama Creator Studio Demo</span>
                    </div>
                    <button type="button" @click="demoOpen = false" aria-label="Tutup demo" class="text-white/80 hover:text-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="relative bg-slate-900" style="aspect-ratio: 16 / 10;">
                    <img src="{{ asset('images/landing/jurusan-pmn.jpg') }}" alt="Live Demo Studio PMN" class="w-full h-full object-cover">
                    <div class="absolute top-3 right-3 flex items-center gap-2 dt-mono text-xs">
                        <span class="px-2.5 py-1 rounded-full text-white" style="background: rgba(13,33,63,0.78);"><span id="pmn-live-viewers">4.820</span> penonton</span>
                        <button type="button" id="pmn-like-btn" class="px-2.5 py-1 rounded-full text-white cursor-pointer" style="background: rgba(225,29,72,0.88);">&hearts; <span id="pmn-live-likes">28.4K</span></button>
                    </div>
                </div>

                <div class="p-5 space-y-4 bg-white">
                    <div class="flex items-center justify-between gap-3 pb-4" style="border-bottom: 1px solid #E8EDF4;">
                        <div class="min-w-0">
                            <span class="dt-mono text-xs font-semibold" style="color: #2EA3DD;" id="pmn-product-pin-label">Pin #1 • TEFA Unggulan</span>
                            <div class="text-sm font-bold truncate mt-0.5" style="color: #0D213F;" id="pmn-product-title">Keripik Tempe Super Renyah Garut</div>
                            <div class="flex items-center gap-2 mt-0.5 dt-mono text-xs">
                                <span class="font-bold" style="color: #0D213F;" id="pmn-product-price">Rp 25.000</span>
                                <span class="line-through text-slate-400" id="pmn-product-original">Rp 35.000</span>
                                <span class="font-semibold" style="color: #16A34A;" id="pmn-product-discount">-28%</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-xs" style="color: #94A3B8;">Omzet TEFA</div>
                            <div class="dt-mono text-xs font-bold" style="color: #0D213F;">Rp 12.8M+</div>
                        </div>
                    </div>

                    <div id="pmn-chat-stream" class="space-y-1.5 text-xs" style="color: #475569; min-height: 54px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── LIGHTWEIGHT DEMO STUDIO SCRIPT ── -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const viewersEl = document.getElementById('pmn-live-viewers');
            const likesEl = document.getElementById('pmn-live-likes');
            const likeBtn = document.getElementById('pmn-like-btn');
            const labelEl = document.getElementById('pmn-product-pin-label');
            const titleEl = document.getElementById('pmn-product-title');
            const priceEl = document.getElementById('pmn-product-price');
            const origEl = document.getElementById('pmn-product-original');
            const discEl = document.getElementById('pmn-product-discount');
            const chatBox = document.getElementById('pmn-chat-stream');

            let viewers = 4820;
            let likes = 28410;

            setInterval(() => {
                viewers += Math.floor(Math.random() * 9) - 4;
                if (viewersEl) viewersEl.textContent = viewers.toLocaleString('id-ID');
                likes += Math.floor(Math.random() * 6) + 1;
                if (likesEl) likesEl.textContent = (likes / 1000).toFixed(1) + 'K';
            }, 3000);

            if (likeBtn) {
                likeBtn.addEventListener('click', () => {
                    likes += 15;
                    if (likesEl) likesEl.textContent = (likes / 1000).toFixed(1) + 'K';
                });
            }

            const products = [
                { label: 'Pin #1 • TEFA Unggulan', title: 'Keripik Tempe Super Renyah Garut', price: 'Rp 25.000', original: 'Rp 35.000', discount: '-28%' },
                { label: 'Pin #2 • TEFA Unggulan', title: 'Kopi Robusta Garut Signature Roast', price: 'Rp 45.000', original: 'Rp 60.000', discount: '-25%' },
                { label: 'Pin #3 • TEFA Unggulan', title: 'Dodol Premium Dark Chocolate Wikrama', price: 'Rp 32.000', original: 'Rp 42.000', discount: '-24%' }
            ];
            let pIdx = 0;
            setInterval(() => {
                pIdx = (pIdx + 1) % products.length;
                const p = products[pIdx];
                if (labelEl) labelEl.textContent = p.label;
                if (titleEl) titleEl.textContent = p.title;
                if (priceEl) priceEl.textContent = p.price;
                if (origEl) origEl.textContent = p.original;
                if (discEl) discEl.textContent = p.discount;
            }, 5000);

            const comments = [
                { user: '@sarah_shop', text: 'Kak, bahannya renyah dan halal MUI kan?' },
                { user: '@host_wikrama', text: 'Halal 100% kak, sudah izin P-IRT dan siap kirim sore ini.' },
                { user: '@budi_tech', text: 'Langsung checkout 3 pack kak, mantap.' },
                { user: '@maya_kuliner', text: 'Spill dodol dark chocolatenya dong kak host.' }
            ];
            let cIdx = 0;
            const addComment = () => {
                if (!chatBox) return;
                const c = comments[cIdx % comments.length];
                cIdx++;
                const row = document.createElement('div');
                row.innerHTML = `<strong style="color:#0D213F">${c.user}:</strong> <span>${c.text}</span>`;
                chatBox.appendChild(row);
                if (chatBox.children.length > 3) chatBox.removeChild(chatBox.firstElementChild);
            };
            addComment();
            addComment();
            setInterval(addComment, 2800);
        });
    </script>
</x-layouts.blank>
