{{--
    ================================================================================
    HALAMAN DETAIL JURUSAN: PENGEMBANGAN PERANGKAT LUNAK DAN GIM (PPLG)
    Konsep Desain: Deep Navy Editorial (Terinspirasi Referensi OceanSphere)
    Menubar & Footer: Menggunakan layout global Wikrama (<x-layouts.blank>)
    URL Akses: /jurusan/pplg
    ================================================================================
--}}
<x-layouts.blank title="Pengembangan Perangkat Lunak dan Gim (PPLG)">
    @php
        $sekolah = $sekolah ?? \App\Modules\Sistem\Models\School::current();
        $schoolName = $sekolah->nama ?? 'SMK Wikrama 1 Garut';

        $stats = [
            ['icon' => 'award', 'value' => '100%', 'label' => 'Sertifikasi BNSP', 'sub' => 'BNSP LSP P-1 & Dicoding'],
            ['icon' => 'monitor', 'value' => '3 Lab', 'label' => 'Studio Software', 'sub' => 'Rasio 1 siswa 1 perangkat'],
            ['icon' => 'layers', 'value' => '2 Unit', 'label' => 'TEFA Agency', 'sub' => 'Proyek aplikasi klien riil'],
            ['icon' => 'trending', 'value' => '99%', 'label' => 'Serapan BMW', 'sub' => 'Kerja, kuliah, wirausaha'],
        ];

        $storyPillars = [
            ['icon' => 'code', 'title' => 'Proyek Industri Riil', 'sub' => 'Bangun SaaS, portal & gim nyata'],
            ['icon' => 'cpu', 'title' => 'Web, Mobile & AI', 'sub' => 'Laravel, Flutter & LLM API'],
            ['icon' => 'briefcase', 'title' => 'Magang 6 Bulan', 'sub' => 'Jaringan mitra Bandung–Jakarta'],
        ];

        $studios = [
            [
                'id' => 0,
                'code' => 'LAB-01',
                'short' => 'Lab Software',
                'name' => 'Laboratorium Software Engineering',
                'spec' => 'iMac & PC Core i7 • RAM 16GB • NVMe SSD',
                'desc' => 'Ruang praktik utama fullstack coding berkecepatan tinggi untuk pengembangan web, debugging, dan pratinjau aplikasi real-time.',
                'image' => asset('images/landing/jurusan-pplg.jpg'),
                'alt' => 'Siswa PPLG menulis kode aplikasi pada editor VS Code',
            ],
            [
                'id' => 1,
                'code' => 'LAB-02',
                'short' => 'Scrum Studio',
                'name' => 'Agile Discussion & Scrum Studio',
                'spec' => 'Kolaborasi tim • Daily standup & code review',
                'desc' => 'Studio kerja kelompok dengan tata meja kolaboratif untuk diskusi sprint, review kode, dan pengelolaan proyek software house.',
                'image' => asset('images/landing/ekosistem-belajar.png'),
                'alt' => 'Suasana kolaborasi proyek siswa PPLG SMK Wikrama 1 Garut',
            ],
            [
                'id' => 2,
                'code' => 'LAB-03',
                'short' => 'Cloud & DevOps',
                'name' => 'Local Cloud Staging & DevOps Lab',
                'spec' => 'Server staging • Docker & CI/CD pipeline',
                'desc' => 'Fasilitas simulasi deployment container, manajemen basis data relasional, serta pengujian pipeline rilis sebelum produksi.',
                'image' => asset('images/landing/budaya-moving-class.jpeg'),
                'alt' => 'Laboratorium sistem, jaringan, dan cloud staging SMK Wikrama 1 Garut',
            ],
            [
                'id' => 3,
                'code' => 'LAB-04',
                'short' => 'Device Testing',
                'name' => 'Mobile App & Device Testing Station',
                'spec' => 'Pengujian multi-perangkat • Android & iOS',
                'desc' => 'Area pengujian responsivitas aplikasi lintas layar serta persiapan kompetisi teknologi tingkat regional hingga nasional.',
                'image' => asset('images/landing/siswa-berprestasi.jpg'),
                'alt' => 'Tim siswa praktikum perangkat teknis SMK Wikrama 1 Garut',
            ],
        ];

        $curriculumCards = [
            [
                'id' => 0,
                'phase' => 'Fase E • Kelas X',
                'title' => 'Fondasi Kode & Algoritma',
                'summary' => 'Logika komputasional, HTML/CSS modern, JavaScript ES6+, Git workflow, dan basis data SQL.',
                'items' => [
                    'Algoritma dan logika pemrograman fundamental',
                    'HTML5 semantik, CSS modern, dan Tailwind CSS',
                    'JavaScript ES6+ dan manipulasi DOM interaktif',
                    'Version Control System (Git & GitHub workflow)',
                    'Basis data relasional SQL (MySQL / PostgreSQL)',
                ],
            ],
            [
                'id' => 1,
                'phase' => 'Fase F • Kelas XI',
                'title' => 'Fullstack, Mobile & Gim',
                'summary' => 'Backend Laravel API, mobile Flutter, game engine Godot/Unity, integrasi AI, dan proyek TEFA.',
                'items' => [
                    'Backend enterprise PHP Laravel dan RESTful API',
                    'Aplikasi mobile lintas platform Flutter (Dart)',
                    'Pengembangan gim 2D & 3D (Godot / Unity)',
                    'Integrasi Machine Learning dan Generative AI API',
                    'Proyek klien nyata di TEFA Wikrama Software Agency',
                ],
            ],
            [
                'id' => 2,
                'phase' => 'Fase F • Kelas XII',
                'title' => 'Magang & Sertifikasi BNSP',
                'summary' => 'PKL 6 bulan di software house, uji kompetensi BNSP, AWS Cloud, dan portofolio GitHub.',
                'items' => [
                    'PKL industri 6 bulan di software house dan startup',
                    'Uji sertifikasi BNSP skema Junior Web Programmer',
                    'Sertifikasi Cloud Practitioner (AWS / Dicoding)',
                    'Capstone final project dengan portofolio terverifikasi',
                    'Penyaluran BMW (Bekerja, Melanjutkan, Wirausaha)',
                ],
            ],
        ];

        $careers = [
            ['icon' => 'code', 'title' => 'Fullstack Web Developer', 'desc' => 'Membangun sistem informasi enterprise, RESTful API, dan antarmuka modern dengan Laravel, React, Vue, dan Next.js.', 'salary' => 'Rp 6 jt – Rp 14 jt'],
            ['icon' => 'smartphone', 'title' => 'Mobile App Developer', 'desc' => 'Mengembangkan aplikasi Android dan iOS lintas platform menggunakan Flutter (Dart) dan React Native.', 'salary' => 'Rp 6 jt – Rp 13 jt'],
            ['icon' => 'gamepad', 'title' => 'Game Developer & Coder', 'desc' => 'Merancang mekanika permainan, fisika 2D/3D, dan desain level menggunakan engine Godot, Unity, serta C#.', 'salary' => 'Rp 5,5 jt – Rp 12 jt'],
            ['icon' => 'cpu', 'title' => 'AI & Prompt Engineer', 'desc' => 'Mengintegrasikan LLM, Computer Vision, dan machine learning ke aplikasi bisnis untuk otomasi alur kerja.', 'salary' => 'Rp 7 jt – Rp 16 jt'],
            ['icon' => 'check', 'title' => 'QA Automation Engineer', 'desc' => 'Menguji keandalan dan keamanan software lewat unit test, integration test, dan automasi (Pest, Cypress).', 'salary' => 'Rp 5 jt – Rp 10,5 jt'],
            ['icon' => 'briefcase', 'title' => 'Software House Founder', 'desc' => 'Mendirikan agensi digital mandiri, merilis gim independen, dan melayani proyek klien nasional maupun global.', 'salary' => 'Mandiri / Proyek'],
        ];

        $certifications = [
            ['code' => 'SKKNI', 'title' => 'Sertifikasi BNSP Junior Web Developer', 'issuer' => 'BNSP LSP P-1', 'desc' => 'Standar kompetensi kerja nasional untuk pemrograman web frontend, backend, basis data, dan keamanan dasar.'],
            ['code' => 'CLOUD', 'title' => 'AWS Cloud Practitioner Academy', 'issuer' => 'Amazon Web Services', 'desc' => 'Penguasaan layanan komputasi awan AWS, arsitektur serverless, storage S3, dan pengelolaan infrastruktur cloud.'],
            ['code' => 'DEV', 'title' => 'Dicoding Professional Certification', 'issuer' => 'Dicoding Tech Partner', 'desc' => 'Kelulusan jalur profesional frontend, backend, atau mobile developer dengan code review langsung dari praktisi.'],
        ];

        $partners = [
            ['name' => 'Telkom Indonesia', 'role' => 'BUMN Telekomunikasi', 'logo' => asset('images/landing/mitra-telkom.png'), 'mono' => 'TL'],
            ['name' => 'AWS Academy', 'role' => 'Cloud Global Partner', 'logo' => asset('images/landing/mitra-aws.png'), 'mono' => 'AW'],
            ['name' => 'Google Cloud', 'role' => 'Edu Partner Program', 'logo' => asset('images/landing/mitra-google.png'), 'mono' => 'GC'],
            ['name' => 'Dicoding Academy', 'role' => 'Tech Platform Mitra', 'logo' => null, 'mono' => 'DC'],
            ['name' => 'PT Barata Tech', 'role' => 'Software House', 'logo' => null, 'mono' => 'BT'],
            ['name' => 'Garena & Indie Lab', 'role' => 'Game Developer', 'logo' => null, 'mono' => 'GI'],
        ];

        $faqs = [
            ['q' => 'Apakah harus sudah bisa coding sebelum masuk PPLG?', 'a' => 'Tidak harus. Kurikulum kelas X dirancang dari nol mulai dari logika komputasional dan algoritma dasar. Yang utama adalah ketekunan dan rasa ingin tahu terhadap teknologi.'],
            ['q' => 'Apakah siswa wajib memiliki laptop sendiri berspesifikasi tinggi?', 'a' => 'Sekolah menyediakan 3 laboratorium komputer berspesifikasi tinggi untuk seluruh praktik harian. Laptop pribadi disarankan untuk latihan mandiri, namun spesifikasi standar cukup untuk tahun pertama.'],
            ['q' => 'Apa perbedaan PPLG di Wikrama dengan SMK lainnya?', 'a' => 'PPLG Wikrama menerapkan budaya kerja software house (Agile/Scrum), unit produksi TEFA pesanan klien resmi, integrasi AI, serta uji sertifikasi resmi BNSP dan AWS Academy.'],
            ['q' => 'Apakah lulusan PPLG bisa langsung kuliah di PTN ternama?', 'a' => 'Bisa. Melalui program BMW (Bekerja, Melanjutkan, Wirausaha), banyak alumni PPLG diterima di Informatika dan Sistem Informasi ITB, UI, Telkom University, UNPAD, dan UPI.'],
        ];

        $galleryItems = [
            [
                'id' => 0,
                'tag' => 'LAB FULLSTACK',
                'title' => 'Praktik Pengembangan Aplikasi di Studio iMac',
                'caption' => 'Sesi praktikum harian 1 siswa 1 perangkat menggunakan iMac dan workstation berperforma tinggi.',
                'image' => asset('images/landing/suasana-belajar-aktif.jpg'),
                'featured' => true,
            ],
            [
                'id' => 1,
                'tag' => 'LIVE CODING',
                'title' => 'Implementasi Backend Laravel & VS Code',
                'caption' => 'Latihan menulis arsitektur kode bersih, RESTful API, dan integrasi basis data relasional.',
                'image' => asset('images/landing/jurusan-pplg.jpg'),
                'featured' => false,
            ],
            [
                'id' => 2,
                'tag' => 'SCRUM & KOLABORASI',
                'title' => 'Diskusi Proyek & Code Review Tim',
                'caption' => 'Siswa bekerja dalam tim layaknya software house melalui daily standup dan sprint proyek.',
                'image' => asset('images/landing/ekosistem-belajar.png'),
                'featured' => false,
            ],
            [
                'id' => 3,
                'tag' => 'CLOUD & STAGING',
                'title' => 'Ruang Praktik Infrastruktur & Server',
                'caption' => 'Simulasi deployment aplikasi, containerization Docker, dan pengelolaan server staging.',
                'image' => asset('images/landing/budaya-moving-class.jpeg'),
                'featured' => false,
            ],
            [
                'id' => 4,
                'tag' => 'PRAKTIKUM & LKS',
                'title' => 'Tim Praktikum & Persiapan Kompetisi IT',
                'caption' => 'Pembinaan tim teknis dan pengujian perangkat untuk ajang LKS serta proyek unggulan.',
                'image' => asset('images/landing/siswa-berprestasi.jpg'),
                'featured' => false,
            ],
            [
                'id' => 5,
                'tag' => 'KAMPUS WIKRAMA',
                'title' => 'Lingkungan Belajar & Showcase Karya',
                'caption' => 'Kampus SMK Wikrama 1 Garut sebagai pusat pembinaan karakter, riset, dan pameran karya tahunan.',
                'image' => asset('images/landing/prestasi-seminar-siswa.jpg'),
                'featured' => true,
            ],
        ];
    @endphp

    <style>
        .ocn-page {
            --ocn-bg: #031326;
            --ocn-bg-deep: #020D1B;
            --ocn-surface: rgba(8, 30, 62, 0.58);
            --ocn-surface-solid: #061C3B;
            --ocn-card-top: #0C3F86;
            --ocn-card-mid: #062148;
            --ocn-card-bot: #04152E;
            --ocn-border: rgba(86, 158, 255, 0.18);
            --ocn-border-strong: rgba(112, 178, 255, 0.34);
            --ocn-accent: #1E82F7;
            --ocn-accent-hover: #3B96FF;
            --ocn-cyan: #59B7FF;
            --ocn-text: #F4F8FF;
            --ocn-sub: #A9BFD8;
            --ocn-muted: #7692B3;
            --ocn-ease: cubic-bezier(0.22, 1, 0.36, 1);

            background-color: var(--ocn-bg);
            color: var(--ocn-text);
            position: relative;
            overflow-x: hidden;
        }

        /* Subtle Deep-Ocean Ambient Depth (matching reference atmosphere) */
        .ocn-ambient-bg {
            background:
                radial-gradient(640px 480px at 56% 10%, rgba(28, 114, 232, 0.22) 0%, transparent 72%),
                radial-gradient(520px 420px at 8% 36%, rgba(20, 88, 184, 0.16) 0%, transparent 70%),
                radial-gradient(600px 480px at 88% 68%, rgba(24, 102, 210, 0.16) 0%, transparent 72%),
                linear-gradient(180deg, #031428 0%, #031224 45%, #041730 100%);
        }

        .ocn-mono {
            font-family: 'Roboto Mono', ui-monospace, SFMono-Regular, Menlo, monospace;
            font-variant-numeric: tabular-nums;
        }

        .ocn-tabular {
            font-variant-numeric: tabular-nums;
        }

        .ocn-container {
            max-width: 1180px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 24px;
            padding-right: 24px;
        }
        @media (min-width: 1024px) {
            .ocn-container {
                padding-left: 32px;
                padding-right: 32px;
            }
        }

        /* Eyebrow Label */
        .ocn-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 11px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            font-weight: 600;
            color: var(--ocn-cyan);
        }
        .ocn-eyebrow-bar {
            width: 2px;
            height: 13px;
            background-color: var(--ocn-cyan);
            border-radius: 2px;
        }

        /* Band 1: Split Giant Hero */
        .ocn-hero {
            padding-top: 40px;
            padding-bottom: 56px;
            position: relative;
        }
        @media (min-width: 1024px) {
            .ocn-hero {
                padding-top: 56px;
                padding-bottom: 72px;
            }
        }

        .ocn-hero-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 32px;
            align-items: center;
        }
        @media (min-width: 1024px) {
            .ocn-hero-grid {
                grid-template-columns: 1.08fr 0.92fr 0.72fr;
                gap: 20px;
                align-items: start;
            }
        }

        .ocn-hero-word {
            font-size: 56px;
            line-height: 0.94;
            font-weight: 800;
            letter-spacing: 0.02em;
            color: #FFFFFF;
            text-transform: uppercase;
        }
        @media (min-width: 640px) {
            .ocn-hero-word { font-size: 76px; }
        }
        @media (min-width: 1024px) {
            .ocn-hero-word { font-size: 98px; }
        }
        .ocn-hero-word-right {
            font-size: 48px;
            line-height: 0.94;
            font-weight: 800;
            letter-spacing: 0.02em;
            color: #FFFFFF;
            text-transform: uppercase;
        }
        @media (min-width: 640px) {
            .ocn-hero-word-right { font-size: 64px; }
        }
        @media (min-width: 1024px) {
            .ocn-hero-word-right {
                font-size: 76px;
                margin-top: 96px;
            }
        }

        .ocn-hero-centerpiece {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 280px;
        }
        @media (min-width: 1024px) {
            .ocn-hero-centerpiece {
                min-height: 390px;
                margin-top: -12px;
            }
        }
        .ocn-hero-glow {
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(38, 140, 255, 0.32) 0%, rgba(16, 70, 155, 0.10) 52%, transparent 74%);
            pointer-events: none;
        }

        /* Pill Buttons (matching reference pills) */
        .ocn-btn-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 42px;
            padding: 0 22px;
            border-radius: 9999px;
            background-color: var(--ocn-accent);
            color: #FFFFFF;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            text-decoration: none;
            box-shadow: 0 8px 22px rgba(30, 130, 247, 0.32);
            transition: background-color 180ms var(--ocn-ease), transform 180ms var(--ocn-ease);
        }
        .ocn-btn-pill:hover {
            background-color: var(--ocn-accent-hover);
            transform: translateY(-1px);
        }

        .ocn-btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 42px;
            padding: 0 20px;
            border-radius: 9999px;
            background-color: rgba(8, 32, 66, 0.45);
            color: #E2EEFF;
            border: 1px solid rgba(112, 178, 255, 0.30);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            text-decoration: none;
            transition: background-color 180ms var(--ocn-ease), border-color 180ms var(--ocn-ease);
        }
        .ocn-btn-outline:hover {
            background-color: rgba(30, 130, 247, 0.16);
            border-color: rgba(112, 178, 255, 0.55);
        }

        .ocn-inline-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--ocn-cyan);
            text-decoration: none;
            transition: color 150ms var(--ocn-ease), gap 180ms var(--ocn-ease);
        }
        .ocn-inline-link:hover {
            color: #93D2FF;
            gap: 11px;
        }

        .ocn-circle-icon {
            width: 34px;
            height: 34px;
            border-radius: 9999px;
            border: 1px solid rgba(112, 178, 255, 0.3);
            background-color: rgba(12, 46, 94, 0.45);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--ocn-cyan);
            flex-shrink: 0;
        }
        .ocn-circle-icon svg { width: 16px; height: 16px; }

        /* Band 2: Enclosed 4-Column Stats Bar */
        .ocn-stats-shell {
            background: linear-gradient(180deg, rgba(9, 34, 70, 0.65) 0%, rgba(5, 22, 47, 0.72) 100%);
            border: 1px solid var(--ocn-border);
            border-radius: 16px;
            backdrop-filter: blur(8px);
        }
        .ocn-stats-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .ocn-stat-cell {
            padding: 24px 16px;
            text-align: center;
            border-bottom: 1px solid rgba(86, 158, 255, 0.12);
        }
        .ocn-stat-cell:nth-child(odd) {
            border-right: 1px solid rgba(86, 158, 255, 0.12);
        }
        .ocn-stat-cell:nth-last-child(-n+2) {
            border-bottom: none;
        }
        @media (min-width: 768px) {
            .ocn-stats-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
            .ocn-stat-cell {
                padding: 28px 20px;
                border-bottom: none;
                border-right: 1px solid rgba(86, 158, 255, 0.14);
            }
            .ocn-stat-cell:last-child {
                border-right: none;
            }
        }

        /* Section Typography */
        .ocn-h2 {
            font-size: 28px;
            line-height: 1.15;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #FFFFFF;
        }
        @media (min-width: 640px) {
            .ocn-h2 { font-size: 36px; }
        }

        /* Band 3 & 4: Framed Visuals */
        .ocn-visual-frame {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            background: linear-gradient(180deg, #0D428A 0%, #051A38 100%);
            border: 1px solid var(--ocn-border-strong);
            box-shadow: 0 20px 50px rgba(1, 10, 24, 0.55);
        }
        .ocn-visual-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(6, 36, 82, 0.12) 0%, rgba(4, 24, 54, 0.25) 55%, rgba(3, 17, 38, 0.88) 100%);
            pointer-events: none;
        }

        .ocn-studio-pill {
            padding: 7px 13px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
            border: 1px solid rgba(86, 158, 255, 0.22);
            background-color: rgba(8, 30, 62, 0.55);
            color: var(--ocn-sub);
            cursor: pointer;
            transition: background-color 160ms var(--ocn-ease), border-color 160ms var(--ocn-ease), color 160ms var(--ocn-ease);
        }
        .ocn-studio-pill:hover {
            border-color: rgba(112, 178, 255, 0.45);
            color: #FFFFFF;
        }
        .ocn-studio-pill.is-active {
            background-color: var(--ocn-accent);
            border-color: var(--ocn-accent);
            color: #FFFFFF;
        }

        /* Band 5: Highlight Action Banner */
        .ocn-impact-banner {
            border-radius: 18px;
            background: linear-gradient(95deg, rgba(28, 76, 146, 0.55) 0%, rgba(11, 38, 80, 0.72) 55%, rgba(7, 27, 58, 0.82) 100%);
            border: 1px solid rgba(112, 178, 255, 0.28);
            padding: 28px 24px;
            box-shadow: 0 16px 40px rgba(1, 10, 24, 0.38);
        }
        @media (min-width: 768px) {
            .ocn-impact-banner {
                padding: 32px 36px;
            }
        }

        /* Band 6: 3 Vertical Cards ("Explore. Learn. Protect.") */
        .ocn-discover-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 28px;
            align-items: start;
        }
        @media (min-width: 1024px) {
            .ocn-discover-grid {
                grid-template-columns: 0.92fr 2.08fr;
                gap: 32px;
            }
        }

        .ocn-v-card {
            border-radius: 18px;
            background: linear-gradient(180deg, #0D428B 0%, #07234B 52%, #041630 100%);
            border: 1px solid rgba(96, 168, 255, 0.24);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: left;
            cursor: pointer;
            transition: transform 220ms var(--ocn-ease), border-color 220ms var(--ocn-ease), box-shadow 220ms var(--ocn-ease);
        }
        .ocn-v-card:hover {
            transform: translateY(-4px);
            border-color: rgba(112, 185, 255, 0.52);
            box-shadow: 0 18px 36px rgba(2, 12, 30, 0.55);
        }
        .ocn-v-card.is-selected {
            border-color: #59B7FF;
            box-shadow: 0 0 0 1px rgba(89, 183, 255, 0.45), 0 18px 36px rgba(2, 12, 30, 0.6);
        }

        /* Companion Info Hub (Tabs: Karier / Sertifikasi & Mitra / Alumni & FAQ) */
        .ocn-hub-shell {
            border-radius: 20px;
            background: linear-gradient(180deg, rgba(8, 30, 62, 0.68) 0%, rgba(4, 19, 41, 0.84) 100%);
            border: 1px solid var(--ocn-border);
            padding: 24px;
        }
        @media (min-width: 768px) {
            .ocn-hub-shell { padding: 36px; }
        }

        .ocn-tab-btn {
            padding: 9px 18px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
            color: var(--ocn-sub);
            background-color: transparent;
            border: 1px solid transparent;
            cursor: pointer;
            transition: background-color 160ms var(--ocn-ease), color 160ms var(--ocn-ease), border-color 160ms var(--ocn-ease);
        }
        .ocn-tab-btn:hover {
            color: #FFFFFF;
            background-color: rgba(255, 255, 255, 0.05);
        }
        .ocn-tab-btn.is-active {
            background-color: var(--ocn-accent);
            color: #FFFFFF;
            border-color: rgba(112, 185, 255, 0.4);
        }

        .ocn-subcard {
            border-radius: 14px;
            background-color: rgba(5, 22, 47, 0.72);
            border: 1px solid rgba(86, 158, 255, 0.16);
            padding: 20px;
            transition: border-color 160ms var(--ocn-ease);
        }
        .ocn-subcard:hover {
            border-color: rgba(112, 178, 255, 0.36);
        }

        .ocn-logo-pill {
            background-color: rgba(255, 255, 255, 0.94);
            border-radius: 8px;
            padding: 6px 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 36px;
            margin-bottom: 10px;
        }
        .ocn-logo-pill img {
            height: 22px;
            width: auto;
            max-width: 96px;
            object-fit: contain;
        }

        /* Band 7: Bottom Slim Strip ("Stay in the Loop") */
        .ocn-bottom-strip {
            border-radius: 18px;
            background: linear-gradient(95deg, rgba(16, 48, 96, 0.52) 0%, rgba(7, 25, 52, 0.78) 100%);
            border: 1px solid var(--ocn-border);
            padding: 24px;
        }
        @media (min-width: 768px) {
            .ocn-bottom-strip { padding: 26px 32px; }
        }

        .ocn-dept-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 36px;
            padding: 0 14px;
            border-radius: 9999px;
            border: 1px solid rgba(112, 178, 255, 0.25);
            background-color: rgba(6, 26, 56, 0.7);
            color: #DCEBFF;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 150ms var(--ocn-ease), border-color 150ms var(--ocn-ease), color 150ms var(--ocn-ease);
        }
        .ocn-dept-chip:hover {
            background-color: rgba(30, 130, 247, 0.25);
            border-color: var(--ocn-cyan);
            color: #FFFFFF;
        }

        .ocn-faq-item {
            border-bottom: 1px solid rgba(86, 158, 255, 0.15);
        }
        .ocn-faq-chevron {
            transition: transform 200ms var(--ocn-ease);
        }
        .ocn-faq-chevron.is-open {
            transform: rotate(180deg);
        }

        .ocn-dept-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-top: 16px;
            border-top: 1px solid rgba(86, 158, 255, 0.14);
        }
        @media (min-width: 1024px) {
            .ocn-dept-wrap {
                padding-top: 0;
                padding-left: 20px;
                border-top: none;
                border-left: 1px solid rgba(86, 158, 255, 0.14);
            }
        }

        /* Galeri Kegiatan Belajar (Deep-Navy Editorial Grid + Lightbox) */
        .ocn-gallery-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }
        @media (min-width: 640px) {
            .ocn-gallery-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (min-width: 1024px) {
            .ocn-gallery-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 18px;
            }
            .ocn-gallery-span-2 {
                grid-column: span 2 / span 2;
            }
        }

        .ocn-gallery-card {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(86, 158, 255, 0.22);
            background-color: #061C3B;
            height: 250px;
            width: 100%;
            text-align: left;
            cursor: pointer;
            transition: transform 220ms var(--ocn-ease), border-color 220ms var(--ocn-ease), box-shadow 220ms var(--ocn-ease);
        }
        @media (min-width: 1024px) {
            .ocn-gallery-card {
                height: 270px;
            }
        }
        .ocn-gallery-card:hover {
            transform: translateY(-3px);
            border-color: rgba(112, 185, 255, 0.52);
            box-shadow: 0 18px 36px rgba(1, 10, 24, 0.55);
        }
        .ocn-gallery-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 420ms var(--ocn-ease);
        }
        .ocn-gallery-card:hover img {
            transform: scale(1.04);
        }
        .ocn-gallery-scrim {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(3, 19, 38, 0.08) 0%, rgba(3, 19, 38, 0.28) 48%, rgba(2, 13, 27, 0.92) 100%);
            pointer-events: none;
        }
        .ocn-overlay-caption {
            position: absolute;
            left: 18px;
            right: 18px;
            bottom: 16px;
            z-index: 10;
        }
        .ocn-lightbox-backdrop {
            position: fixed;
            inset: 0;
            z-index: 90;
            background-color: rgba(2, 11, 24, 0.88);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .ocn-lightbox-box {
            width: 100%;
            max-width: 860px;
            border-radius: 20px;
            overflow: hidden;
            background-color: #051933;
            border: 1px solid rgba(112, 178, 255, 0.34);
            box-shadow: 0 28px 70px rgba(0, 0, 0, 0.65);
        }
    </style>

    <div class="ocn-page ocn-ambient-bg">
        <!-- ═══════════════════════════════════════════════════════════════════════
             BAND 1: HERO SECTION (SPLIT DISPLAY "PPLG [3D] STUDIO" + DID YOU KNOW)
             ═══════════════════════════════════════════════════════════════════════ -->
        <header class="ocn-hero">
            <div class="ocn-container">
                <!-- Subtle Breadcrumb & Eyebrow -->
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                    <div class="ocn-eyebrow">
                        <span class="ocn-eyebrow-bar" aria-hidden="true"></span>
                        <span>Kompetensi Keahlian &bull; {{ $schoolName }}</span>
                    </div>
                    <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs" style="color: #7692B3;">
                        <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
                        <span>/</span>
                        <a href="{{ url('/jurusan') }}" class="hover:text-white transition-colors">Jurusan</a>
                        <span>/</span>
                        <span class="ocn-mono text-white">PPLG</span>
                    </nav>
                </div>

                <!-- 3-Column Split Composition matching OceanSphere Hero -->
                <div class="ocn-hero-grid">
                    <!-- Left: Giant "PPLG" + Subtitle + Intro + 2 Pill Buttons -->
                    <div>
                        <h1 class="ocn-hero-word">PPLG</h1>

                        <p class="mt-4 text-base sm:text-lg font-semibold leading-snug text-white max-w-md">
                            Pengembangan Perangkat Lunak dan Gim: Membangun Solusi Digital Berstandar Industri
                        </p>

                        <p class="mt-3 text-sm leading-relaxed max-w-md" style="color: #A9BFD8;">
                            Mendidik calon software engineer, fullstack developer, dan pembuat gim yang menguasai arsitektur backend, antarmuka web modern, aplikasi mobile Flutter, integrasi kecerdasan buatan, hingga produksi gim.
                        </p>

                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="ocn-btn-pill">
                                <span>Daftar PPLG</span>
                            </a>
                            <a href="#kurikulum" class="ocn-btn-outline">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span>Lihat Kurikulum</span>
                            </a>
                        </div>
                    </div>

                    <!-- Center: Luminous Deep-Sapphire 3D Glass Software Architecture Sculpture -->
                    <div class="ocn-hero-centerpiece" aria-hidden="true">
                        <div class="ocn-hero-glow"></div>
                        <svg viewBox="0 0 380 420" class="w-full h-auto relative z-10" style="max-width: 340px;" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <radialGradient id="pplgHalo" cx="50%" cy="46%" r="50%">
                                    <stop offset="0%" stop-color="#48B2FF" stop-opacity="0.34"/>
                                    <stop offset="58%" stop-color="#1456B8" stop-opacity="0.10"/>
                                    <stop offset="100%" stop-color="#031326" stop-opacity="0"/>
                                </radialGradient>
                                <linearGradient id="glassPaneA" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#9CD6FF" stop-opacity="0.46"/>
                                    <stop offset="48%" stop-color="#227CEB" stop-opacity="0.22"/>
                                    <stop offset="100%" stop-color="#07224A" stop-opacity="0.65"/>
                                </linearGradient>
                                <linearGradient id="glassPaneB" x1="100%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#7CD4FF" stop-opacity="0.52"/>
                                    <stop offset="55%" stop-color="#1E66D0" stop-opacity="0.24"/>
                                    <stop offset="100%" stop-color="#04152E" stop-opacity="0.72"/>
                                </linearGradient>
                                <linearGradient id="glassRim" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#C9ECFF" stop-opacity="0.92"/>
                                    <stop offset="50%" stop-color="#4AA7FF" stop-opacity="0.45"/>
                                    <stop offset="100%" stop-color="#154FA3" stop-opacity="0.2"/>
                                </linearGradient>
                                <linearGradient id="filament" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#7ED6FF" stop-opacity="0.8"/>
                                    <stop offset="65%" stop-color="#2B84F6" stop-opacity="0.3"/>
                                    <stop offset="100%" stop-color="#031326" stop-opacity="0"/>
                                </linearGradient>
                            </defs>

                            <!-- Soft Bioluminescent Halo -->
                            <circle cx="190" cy="185" r="175" fill="url(#pplgHalo)"/>

                            <!-- Delicate Trailing Data Filaments (echoing fluid elegance of reference) -->
                            <path d="M134 264 C128 312, 146 346, 138 394" stroke="url(#filament)" stroke-width="1.6" stroke-linecap="round"/>
                            <path d="M168 278 C164 328, 178 358, 170 408" stroke="url(#filament)" stroke-width="2" stroke-linecap="round"/>
                            <path d="M204 282 C210 332, 196 364, 206 406" stroke="url(#filament)" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M240 264 C248 310, 232 344, 242 388" stroke="url(#filament)" stroke-width="1.4" stroke-linecap="round"/>

                            <!-- Orbital Ring -->
                            <ellipse cx="190" cy="196" rx="142" ry="44" transform="rotate(-18 190 196)" stroke="url(#glassRim)" stroke-width="1.2" stroke-dasharray="4 6" opacity="0.72"/>

                            <!-- Lower Isometric Glass Layer (Database / Cloud Foundation) -->
                            <path d="M190 288 L88 232 L190 176 L292 232 Z" fill="url(#glassPaneB)" stroke="url(#glassRim)" stroke-width="1.4"/>
                            <path d="M88 232 L190 288 L190 302 L88 246 Z" fill="#0B336B" fill-opacity="0.65" stroke="#4AA7FF" stroke-opacity="0.35"/>
                            <path d="M292 232 L190 288 L190 302 L292 246 Z" fill="#061D42" fill-opacity="0.75" stroke="#4AA7FF" stroke-opacity="0.25"/>

                            <!-- Middle Isometric Glass Layer (Application / API Layer) -->
                            <path d="M190 232 L98 182 L190 132 L282 182 Z" fill="url(#glassPaneA)" stroke="url(#glassRim)" stroke-width="1.5"/>
                            <line x1="146" y1="180" x2="210" y2="146" stroke="#A6E0FF" stroke-width="1.6" stroke-linecap="round" opacity="0.75"/>
                            <line x1="164" y1="194" x2="236" y2="156" stroke="#6AC4FF" stroke-width="1.4" stroke-linecap="round" opacity="0.6"/>
                            <line x1="182" y1="208" x2="226" y2="184" stroke="#A6E0FF" stroke-width="1.4" stroke-linecap="round" opacity="0.5"/>

                            <!-- Top Luminous Crystal Core (Code & Game Engine Prism) -->
                            <polygon points="190,46 264,116 190,174 116,116" fill="url(#glassPaneA)" stroke="url(#glassRim)" stroke-width="1.7"/>
                            <polygon points="190,46 264,116 190,136" fill="#9BD7FF" fill-opacity="0.22"/>
                            <polygon points="190,46 116,116 190,136" fill="#4BA7FF" fill-opacity="0.16"/>
                            <line x1="190" y1="46" x2="190" y2="174" stroke="#C5ECFF" stroke-width="1.2" opacity="0.65"/>

                            <!-- Crisp Code Glyph Inside Crystal Core (< / >) -->
                            <path d="M174 106 L160 116 L174 126" stroke="#E0F4FF" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M206 106 L220 116 L206 126" stroke="#E0F4FF" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M195 101 L185 131" stroke="#7AD3FF" stroke-width="2.2" stroke-linecap="round"/>

                            <!-- Luminous Floating Nodes -->
                            <circle cx="88" cy="144" r="4" fill="#99DCFF"/>
                            <circle cx="88" cy="144" r="10" fill="#48B2FF" fill-opacity="0.22"/>
                            <circle cx="298" cy="126" r="4.5" fill="#C4EBFF"/>
                            <circle cx="298" cy="126" r="11" fill="#48B2FF" fill-opacity="0.22"/>
                            <circle cx="286" cy="252" r="3" fill="#7BD2FF"/>
                            <circle cx="104" cy="264" r="3" fill="#7BD2FF"/>
                        </svg>
                    </div>

                    <!-- Right: Giant "STUDIO" + Circular Icon + "Tahukah Kamu" Micro-Callout -->
                    <div class="flex flex-col lg:items-start">
                        <div class="ocn-hero-word-right" aria-hidden="true">STUDIO</div>

                        <div class="mt-6 max-w-xs">
                            <span class="ocn-circle-icon mb-3" aria-hidden="true">
                                <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16 18 6-6-6-6M8 6l-6 6 6 6"/>
                                </svg>
                            </span>
                            <div class="text-xs font-bold uppercase tracking-wider" style="color: #7DBBFF;">Tahukah Kamu</div>
                            <p class="mt-2 text-xs leading-relaxed" style="color: #9BB4D0;">
                                Siswa PPLG Wikrama mulai mengerjakan proyek pesanan klien nyata di TEFA Software House sejak kelas XI dan dibekali uji sertifikasi resmi BNSP.
                            </p>
                            <a href="#profil" class="ocn-inline-link mt-3">
                                <span>Pelajari lebih lanjut</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <!-- ═══════════════════════════════════════════════════════════════════
                 BAND 2: ENCLOSED 4-COLUMN STATS CONTAINER
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="pb-16 lg:pb-24" aria-label="Ringkasan capaian PPLG">
                <div class="ocn-container">
                    <div class="ocn-stats-shell">
                        <div class="ocn-stats-grid">
                            @foreach($stats as $stat)
                                <div class="ocn-stat-cell">
                                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-full mb-3" style="background: rgba(89, 183, 255, 0.12); color: #59B7FF;">
                                        @if($stat['icon'] === 'award')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="6"/><path stroke-linecap="round" stroke-linejoin="round" d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                                        @elseif($stat['icon'] === 'monitor')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true"><rect width="20" height="14" x="2" y="3" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8m-4-4v4"/></svg>
                                        @elseif($stat['icon'] === 'layers')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m12 2 10 5-10 5L2 7l10-5Zm0 10 10 5-10 5-10-5"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17 9 11l4 4 8-8m0 0h-5m5 0v5"/></svg>
                                        @endif
                                    </div>
                                    <div class="ocn-tabular text-2xl sm:text-3xl font-bold" style="color: #4EB1FF;">
                                        {{ $stat['value'] }}
                                    </div>
                                    <div class="mt-1.5 text-xs font-bold uppercase tracking-wider text-white">
                                        {{ $stat['label'] }}
                                    </div>
                                    <div class="mt-1 text-xs" style="color: #7D99B8;">
                                        {{ $stat['sub'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 BAND 3: LEFT SQUARE VISUAL + RIGHT STORY ("OUR STORY" POSITION)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-12 lg:py-20" id="profil">
                <div class="ocn-container">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                        <!-- Left: Square Framed Real PPLG Studio Photo -->
                        <div class="lg:col-span-5">
                            <div class="ocn-visual-frame" style="aspect-ratio: 1 / 1;">
                                <img src="{{ asset('images/landing/suasana-belajar-aktif.jpg') }}"
                                     alt="Siswa PPLG SMK Wikrama 1 Garut praktik coding di laboratorium iMac"
                                     class="w-full h-full object-cover"
                                     loading="lazy"
                                     decoding="async">
                                <div class="ocn-visual-overlay"></div>
                                <div class="ocn-overlay-caption flex items-center justify-between gap-2">
                                    <div>
                                        <div class="ocn-mono text-xs" style="color: #7AD2FF;">STUDIO UTAMA PPLG</div>
                                        <div class="text-sm font-semibold text-white mt-0.5">{{ $schoolName }}</div>
                                    </div>
                                    <span class="ocn-mono text-xs px-2.5 py-1 rounded-full" style="background: rgba(3, 19, 38, 0.72); border: 1px solid rgba(112, 178, 255, 0.28); color: #CFE6FF;">
                                        Akreditasi A
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Eyebrow + Story + Link + 3 Horizontal Pillars -->
                        <div class="lg:col-span-7">
                            <div class="ocn-eyebrow mb-3">
                                <span>Profil Program</span>
                            </div>

                            <h2 class="ocn-h2">
                                Mencetak Software Engineer Berdaya Saing Global
                            </h2>

                            <p class="mt-5 text-sm sm:text-base leading-relaxed" style="color: #B4C8DF;">
                                Program keahlian Pengembangan Perangkat Lunak dan Gim (PPLG) di {{ $schoolName }} menyelaraskan pembelajaran langsung dengan budaya kerja software house profesional. Siswa tidak sekadar menulis sintaks, melainkan membangun arsitektur kode bersih, microservices, containerization Docker, Git workflow, dan kolaborasi Scrum Agile.
                            </p>

                            <a href="#studio" class="ocn-inline-link mt-5">
                                <span>Lihat fasilitas studio</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>

                            <!-- 3 Horizontal Pillars (exact reference layout) -->
                            <div class="mt-8 pt-6 grid grid-cols-1 sm:grid-cols-3 gap-5" style="border-top: 1px solid rgba(86, 158, 255, 0.16);">
                                @foreach($storyPillars as $pillar)
                                    <div class="flex items-start gap-3">
                                        <span class="ocn-circle-icon mt-0.5" aria-hidden="true">
                                            @if($pillar['icon'] === 'code')
                                                <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16 18 6-6-6-6M8 6l-6 6 6 6"/></svg>
                                            @elseif($pillar['icon'] === 'cpu')
                                                <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><rect width="16" height="16" x="4" y="4" rx="2"/><rect width="6" height="6" x="9" y="9" rx="1"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 2v2M15 20v2M2 15h2M2 9h2M20 15h2M20 9h2M9 2v2M9 20v2"/></svg>
                                            @else
                                                <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="7" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                            @endif
                                        </span>
                                        <div>
                                            <h3 class="text-sm font-semibold text-white leading-snug">{{ $pillar['title'] }}</h3>
                                            <p class="mt-1 text-xs leading-relaxed" style="color: #8CA7C6;">{{ $pillar['sub'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 BAND 4: LEFT NARRATIVE + RIGHT WIDE VISUAL ("THE ART OF DRIFTING")
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-12 lg:py-20" id="studio" x-data="{ activeStudio: 0 }">
                <div class="ocn-container">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                        <!-- Left: Title + Description + Interactive 4-Studio Switcher -->
                        <div class="lg:col-span-5">
                            <h2 class="ocn-h2">
                                TEFA Software House &amp; Studio Praktik
                            </h2>

                            <p class="mt-4 text-sm sm:text-base leading-relaxed" style="color: #B4C8DF;">
                                Setiap laboratorium dirancang menyerupai lingkungan kerja perusahaan teknologi: perangkat performa tinggi, serat optik dedicated, ruang Scrum Agile, dan unit produksi TEFA yang melayani proyek klien riil.
                            </p>

                            <!-- Interactive Studio Selector -->
                            <div class="mt-6 flex flex-wrap gap-2">
                                @foreach($studios as $studio)
                                    <button type="button"
                                            @click="activeStudio = {{ $studio['id'] }}"
                                            class="ocn-studio-pill"
                                            :class="{ 'is-active': activeStudio === {{ $studio['id'] }} }">
                                        {{ $studio['short'] }}
                                    </button>
                                @endforeach
                            </div>

                            <!-- Active Studio Quick Description -->
                            <div class="mt-5 p-4 rounded-xl" style="background: rgba(6, 28, 59, 0.65); border: 1px solid rgba(86, 158, 255, 0.16);">
                                @foreach($studios as $studio)
                                    <div x-show="activeStudio === {{ $studio['id'] }}" @if($studio['id'] !== 0) x-cloak @endif>
                                        <div class="ocn-mono text-xs" style="color: #59B7FF;">{{ $studio['code'] }} &bull; {{ $studio['spec'] }}</div>
                                        <div class="text-sm font-semibold text-white mt-1">{{ $studio['name'] }}</div>
                                        <p class="text-xs mt-1.5 leading-relaxed" style="color: #9BB4D0;">{{ $studio['desc'] }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <a href="#kurikulum" class="ocn-inline-link mt-5">
                                <span>Lihat roadmap kurikulum</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>

                        <!-- Right: Wide Rounded Landscape Visual Frame -->
                        <div class="lg:col-span-7">
                            <div class="ocn-visual-frame aspect-[16/10]">
                                @foreach($studios as $studio)
                                    <div x-show="activeStudio === {{ $studio['id'] }}" @if($studio['id'] !== 0) x-cloak @endif class="w-full h-full relative">
                                        <img src="{{ $studio['image'] }}"
                                             alt="{{ $studio['alt'] }}"
                                             class="w-full h-full object-cover"
                                             loading="lazy"
                                             decoding="async">
                                        <div class="ocn-visual-overlay"></div>
                                        <div class="ocn-overlay-caption flex flex-wrap items-end justify-between gap-2">
                                            <div>
                                                <div class="ocn-mono text-xs" style="color: #7AD2FF;">{{ $studio['code'] }}</div>
                                                <div class="text-base font-semibold text-white">{{ $studio['name'] }}</div>
                                            </div>
                                            <span class="ocn-mono text-xs px-3 py-1 rounded-full" style="background: rgba(3, 19, 38, 0.78); border: 1px solid rgba(112, 178, 255, 0.25); color: #D8ECFF;">
                                                {{ $studio['spec'] }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 BAND 5: FULL-WIDTH HIGHLIGHT ACTION BANNER ("YOU CAN MAKE AN IMPACT")
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-8 lg:py-12">
                <div class="ocn-container">
                    <div class="ocn-impact-banner flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex items-start sm:items-center gap-4">
                            <span class="ocn-circle-icon" style="width: 44px; height: 44px;" aria-hidden="true">
                                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m9-9H3m15.364-6.364L5.636 18.364"/>
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-lg sm:text-xl font-bold text-white leading-snug">
                                    Siap Menjadi Software Engineer Berikutnya?
                                </h2>
                                <p class="mt-1 text-xs sm:text-sm" style="color: #A9BFD8;">
                                    Kuota kelas PPLG dibatasi demi menjaga rasio praktik 1 siswa 1 perangkat kerja dan pendampingan kode intensif.
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0">
                            <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="ocn-btn-pill">
                                <span>Daftar SPMB</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 BAND 6: "BELAJAR. BERKARYA. BERKARIR." + 3 VERTICAL CURRICULUM CARDS
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-12 lg:py-20" id="kurikulum" x-data="{ selectedGrade: 1 }">
                <div class="ocn-container">
                    <div class="ocn-discover-grid">
                        <!-- Left Intro Column -->
                        <div>
                            <div class="ocn-eyebrow mb-3">
                                <span>Kurikulum 3 Tahun</span>
                            </div>

                            <h2 class="ocn-h2">
                                Belajar.<br>Berkarya.<br>Berkarir.
                            </h2>

                            <p class="mt-4 text-sm leading-relaxed" style="color: #A9BFD8;">
                                Dirancang bertahap dari pemahaman algoritma fundamental di Fase E hingga penguasaan stack produksi, proyek TEFA, magang industri 6 bulan, dan sertifikasi negara BNSP di Fase F.
                            </p>

                            <div class="mt-6">
                                <a href="#karir" class="ocn-btn-outline">
                                    <span>Prospek Karier</span>
                                    <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        </div>

                        <!-- Right: 3 Tall Vertical Cards (Kelas X, Kelas XI, Kelas XII) -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            @foreach($curriculumCards as $card)
                                <button type="button"
                                        @click="selectedGrade = {{ $card['id'] }}"
                                        class="ocn-v-card p-5"
                                        :class="{ 'is-selected': selectedGrade === {{ $card['id'] }} }">
                                    <!-- Top Bespoke Luminous Deep-Navy 3D Glass Artwork -->
                                    <div class="w-full flex items-center justify-center py-4 mb-4" aria-hidden="true">
                                        @if($card['id'] === 0)
                                            <!-- Card X: Algorithm Lattice Cube -->
                                            <svg viewBox="0 0 180 150" class="w-36 h-32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <defs>
                                                    <radialGradient id="c1Glow" cx="50%" cy="48%" r="50%">
                                                        <stop offset="0%" stop-color="#5EC4FF" stop-opacity="0.45"/>
                                                        <stop offset="100%" stop-color="#07234B" stop-opacity="0"/>
                                                    </radialGradient>
                                                </defs>
                                                <circle cx="90" cy="74" r="64" fill="url(#c1Glow)"/>
                                                <polygon points="90,22 140,50 90,78 40,50" fill="#7CD4FF" fill-opacity="0.28" stroke="#A6E2FF" stroke-width="1.4"/>
                                                <polygon points="40,50 90,78 90,130 40,102" fill="#217AE8" fill-opacity="0.28" stroke="#64BDFF" stroke-width="1.3"/>
                                                <polygon points="140,50 90,78 90,130 140,102" fill="#0B3B82" fill-opacity="0.58" stroke="#64BDFF" stroke-width="1.3"/>
                                                <circle cx="90" cy="78" r="5" fill="#DDF5FF"/>
                                                <circle cx="40" cy="50" r="3.5" fill="#8AD8FF"/>
                                                <circle cx="140" cy="50" r="3.5" fill="#8AD8FF"/>
                                                <circle cx="90" cy="22" r="3.5" fill="#DDF5FF"/>
                                            </svg>
                                        @elseif($card['id'] === 1)
                                            <!-- Card XI: Layered App & Game Prism -->
                                            <svg viewBox="0 0 180 150" class="w-36 h-32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <defs>
                                                    <radialGradient id="c2Glow" cx="50%" cy="48%" r="50%">
                                                        <stop offset="0%" stop-color="#769CFF" stop-opacity="0.48"/>
                                                        <stop offset="100%" stop-color="#07234B" stop-opacity="0"/>
                                                    </radialGradient>
                                                </defs>
                                                <circle cx="90" cy="74" r="64" fill="url(#c2Glow)"/>
                                                <rect x="44" y="32" width="92" height="58" rx="14" transform="rotate(-8 90 61)" fill="#3F94FF" fill-opacity="0.25" stroke="#B6E4FF" stroke-width="1.5"/>
                                                <rect x="52" y="54" width="84" height="54" rx="14" transform="rotate(6 94 81)" fill="#7A6CFF" fill-opacity="0.24" stroke="#9FCBFF" stroke-width="1.4"/>
                                                <circle cx="74" cy="80" r="7" stroke="#E2F4FF" stroke-width="1.6"/>
                                                <path d="M104 75h12M110 69v12" stroke="#E2F4FF" stroke-width="1.8" stroke-linecap="round"/>
                                                <circle cx="122" cy="86" r="3" fill="#8AD8FF"/>
                                            </svg>
                                        @else
                                            <!-- Card XII: Cloud Deployment & BNSP Orbital Core -->
                                            <svg viewBox="0 0 180 150" class="w-36 h-32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <defs>
                                                    <radialGradient id="c3Glow" cx="50%" cy="48%" r="50%">
                                                        <stop offset="0%" stop-color="#58C8FF" stop-opacity="0.48"/>
                                                        <stop offset="100%" stop-color="#07234B" stop-opacity="0"/>
                                                    </radialGradient>
                                                </defs>
                                                <circle cx="90" cy="74" r="64" fill="url(#c3Glow)"/>
                                                <ellipse cx="90" cy="76" rx="64" ry="22" transform="rotate(-18 90 76)" stroke="#8DD8FF" stroke-width="1.3" stroke-dasharray="4 5"/>
                                                <circle cx="90" cy="74" r="34" fill="#237CEB" fill-opacity="0.34" stroke="#B5E5FF" stroke-width="1.6"/>
                                                <path d="M77 75l9 9 18-20" stroke="#E8F8FF" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                                                <circle cx="144" cy="56" r="4" fill="#D5F3FF"/>
                                            </svg>
                                        @endif
                                    </div>

                                    <!-- Card Text Bottom -->
                                    <div>
                                        <div class="ocn-mono text-xs mb-1.5" style="color: #67BEFF;">{{ $card['phase'] }}</div>
                                        <h3 class="text-base font-bold text-white leading-snug">{{ $card['title'] }}</h3>
                                        <p class="mt-2 text-xs leading-relaxed" style="color: #9BB4D0;">{{ $card['summary'] }}</p>
                                        <div class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold" style="color: #59B7FF;">
                                            <span x-text="selectedGrade === {{ $card['id'] }} ? 'Silabus aktif' : 'Lihat silabus'">Lihat silabus</span>
                                            <span aria-hidden="true">&rarr;</span>
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Clean Expandable Syllabus Drawer Below the 3 Cards -->
                    <div class="mt-6 rounded-2xl p-6" style="background: rgba(6, 26, 56, 0.72); border: 1px solid rgba(86, 158, 255, 0.22);">
                        @foreach($curriculumCards as $card)
                            <div x-show="selectedGrade === {{ $card['id'] }}" @if($card['id'] !== 1) x-cloak @endif>
                                <div class="flex flex-wrap items-center justify-between gap-2 pb-4 mb-4" style="border-bottom: 1px solid rgba(86, 158, 255, 0.14);">
                                    <div>
                                        <span class="ocn-mono text-xs" style="color: #59B7FF;">SILABUS {{ strtoupper($card['phase']) }}</span>
                                        <h3 class="text-base sm:text-lg font-bold text-white mt-0.5">{{ $card['title'] }}</h3>
                                    </div>
                                    <span class="ocn-mono text-xs" style="color: #8CA7C6;">5 modul inti industri</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($card['items'] as $item)
                                        <div class="flex items-start gap-2.5 text-xs sm:text-sm" style="color: #D5E6FA;">
                                            <svg class="w-4 h-4 shrink-0 mt-0.5" style="color: #4EB1FF;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/>
                                            </svg>
                                            <span>{{ $item }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 7: PROSPEK KARIER LULUSAN PPLG (DEDICATED SECTION)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-12 lg:py-20" id="karir">
                <div class="ocn-container">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-end mb-10">
                        <div class="lg:col-span-7">
                            <div class="ocn-eyebrow mb-3">
                                <span>Peluang Karier Global</span>
                            </div>
                            <h2 class="ocn-h2">
                                Prospek Karier Lulusan PPLG
                            </h2>
                        </div>
                        <div class="lg:col-span-5">
                            <p class="text-sm leading-relaxed" style="color: #A9BFD8;">
                                Kebutuhan insinyur perangkat lunak terus bertumbuh di perusahaan teknologi, perbankan digital, software house, hingga studio gim independen.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($careers as $career)
                            <article class="ocn-subcard flex flex-col justify-between p-6">
                                <div>
                                    <span class="ocn-circle-icon mb-4" aria-hidden="true">
                                        @if($career['icon'] === 'code')
                                            <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16 18 6-6-6-6M8 6l-6 6 6 6"/></svg>
                                        @elseif($career['icon'] === 'smartphone')
                                            <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><rect width="14" height="20" x="5" y="2" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01"/></svg>
                                        @elseif($career['icon'] === 'gamepad')
                                            <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 11h4M8 9v4m7-1h.01M18 10h.01M17.32 5H6.68a4 4 0 0 0-3.978 3.59L2 16a3 3 0 0 0 3 3c1 0 1.5-.5 2-1l1.4-1.4h7.2L17 18c.5.5 1 1 2 1a3 3 0 0 0 3-3l-.7-7.4A4 4 0 0 0 17.32 5z"/></svg>
                                        @elseif($career['icon'] === 'cpu')
                                            <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><rect width="16" height="16" x="4" y="4" rx="2"/><rect width="6" height="6" x="9" y="9" rx="1"/></svg>
                                        @elseif($career['icon'] === 'check')
                                            <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9 11 3 3L22 4"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                        @else
                                            <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="7" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                        @endif
                                    </span>
                                    <h3 class="text-base font-bold text-white">{{ $career['title'] }}</h3>
                                    <p class="mt-2 text-xs sm:text-sm leading-relaxed" style="color: #9BB4D0;">{{ $career['desc'] }}</p>
                                </div>
                                <div class="mt-5 pt-4 flex items-center justify-between text-xs" style="border-top: 1px solid rgba(86, 158, 255, 0.14);">
                                    <span style="color: #7692B3;">Estimasi / bulan</span>
                                    <span class="ocn-mono font-semibold" style="color: #67C2FF;">{{ $career['salary'] }}</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 8: SERTIFIKASI RESMI & MITRA INDUSTRI (DEDICATED SECTION)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-12 lg:py-20" id="sertifikasi">
                <div class="ocn-container">
                    <div class="ocn-hub-shell">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-end mb-8">
                            <div class="lg:col-span-7">
                                <div class="ocn-eyebrow mb-3">
                                    <span>Pengakuan Industri &amp; Jaringan PKL</span>
                                </div>
                                <h2 class="ocn-h2">
                                    Sertifikasi Resmi &amp; Mitra Industri
                                </h2>
                            </div>
                            <div class="lg:col-span-5">
                                <p class="text-sm leading-relaxed" style="color: #A9BFD8;">
                                    Lulusan dibekali sertifikat kompetensi negara berlogo Garuda (BNSP), lisensi cloud global, serta pengalaman magang 6 bulan di perusahaan mitra.
                                </p>
                            </div>
                        </div>

                        <!-- 3 Official Certifications -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                            @foreach($certifications as $cert)
                                <article class="ocn-subcard p-6">
                                    <div class="flex items-center justify-between gap-2 mb-3">
                                        <span class="ocn-mono text-xs px-2.5 py-0.5 rounded-full" style="background: rgba(30, 130, 247, 0.22); color: #7CCBFF;">{{ $cert['code'] }}</span>
                                        <span class="ocn-mono text-xs" style="color: #7D99B8;">{{ $cert['issuer'] }}</span>
                                    </div>
                                    <h3 class="text-base font-bold text-white leading-snug">{{ $cert['title'] }}</h3>
                                    <p class="mt-2 text-xs sm:text-sm leading-relaxed" style="color: #9BB4D0;">{{ $cert['desc'] }}</p>
                                </article>
                            @endforeach
                        </div>

                        <!-- 6 Industry Partners -->
                        <div class="pt-6" style="border-top: 1px solid rgba(86, 158, 255, 0.16);">
                            <div class="ocn-mono text-xs uppercase tracking-wider mb-4" style="color: #7DBBFF;">Mitra PKL &amp; Rekrutmen Lulusan</div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                                @foreach($partners as $partner)
                                    <div class="ocn-subcard text-center flex flex-col items-center justify-center p-4">
                                        @if($partner['logo'])
                                            <div class="ocn-logo-pill">
                                                <img src="{{ $partner['logo'] }}" alt="Logo {{ $partner['name'] }}" loading="lazy" decoding="async">
                                            </div>
                                        @else
                                            <div class="ocn-mono text-xs font-bold mb-2.5 flex items-center justify-center rounded-lg" style="height: 36px; min-width: 52px; padding: 0 12px; background: rgba(30, 130, 247, 0.16); border: 1px solid rgba(112, 178, 255, 0.28); color: #8DD5FF;">
                                                {{ $partner['mono'] }}
                                            </div>
                                        @endif
                                        <div class="text-xs font-semibold text-white">{{ $partner['name'] }}</div>
                                        <div class="text-xs mt-0.5" style="color: #7D99B8;">{{ $partner['role'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 9: GALERI KEGIATAN BELAJAR PPLG (NEW DEDICATED SECTION + LIGHTBOX)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-12 lg:py-20" id="galeri" x-data="{ lightbox: null }" @keydown.escape.window="lightbox = null">
                <div class="ocn-container">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-end mb-10">
                        <div class="lg:col-span-7">
                            <div class="ocn-eyebrow mb-3">
                                <span>Dokumentasi Studio &amp; Kelas</span>
                            </div>
                            <h2 class="ocn-h2">
                                Galeri Kegiatan Belajar PPLG
                            </h2>
                        </div>
                        <div class="lg:col-span-5">
                            <p class="text-sm leading-relaxed" style="color: #A9BFD8;">
                                Potret keseharian siswa PPLG saat praktik pemrograman, diskusi Scrum, pengujian perangkat, dan kolaborasi proyek di lingkungan kampus {{ $schoolName }}.
                            </p>
                        </div>
                    </div>

                    <!-- Asymmetric 4-Column Deep-Navy Gallery Grid -->
                    <div class="ocn-gallery-grid">
                        @foreach($galleryItems as $item)
                            <button type="button"
                                    @click="lightbox = {{ Js::from($item) }}"
                                    class="ocn-gallery-card {{ $item['featured'] ? 'ocn-gallery-span-2' : '' }}">
                                <img src="{{ $item['image'] }}"
                                     alt="{{ $item['title'] }}"
                                     loading="lazy"
                                     decoding="async">
                                <div class="ocn-gallery-scrim"></div>
                                <div class="ocn-overlay-caption">
                                    <span class="ocn-mono text-xs inline-block px-2 py-0.5 rounded mb-1.5" style="background: rgba(30, 130, 247, 0.28); border: 1px solid rgba(112, 185, 255, 0.35); color: #B8E2FF;">
                                        {{ $item['tag'] }}
                                    </span>
                                    <div class="text-sm sm:text-base font-bold text-white leading-snug">
                                        {{ $item['title'] }}
                                    </div>
                                    <p class="mt-1 text-xs leading-relaxed" style="color: #B4C8DF;">
                                        {{ $item['caption'] }}
                                    </p>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Lightbox Modal for Full-Size Photo Inspection -->
                <div x-show="lightbox !== null"
                     x-cloak
                     role="dialog"
                     aria-modal="true"
                     class="ocn-lightbox-backdrop"
                     @click.self="lightbox = null">
                    <div class="ocn-lightbox-box">
                        <template x-if="lightbox">
                            <div>
                                <div class="relative bg-slate-950" style="max-height: 70vh; overflow: hidden;">
                                    <img :src="lightbox.image" :alt="lightbox.title" class="w-full h-full object-contain" style="max-height: 70vh;">
                                    <button type="button"
                                            @click="lightbox = null"
                                            aria-label="Tutup pratinjau"
                                            class="absolute top-4 right-4 w-9 h-9 rounded-full flex items-center justify-center text-white cursor-pointer"
                                            style="background: rgba(3, 19, 38, 0.78); border: 1px solid rgba(112, 178, 255, 0.35);">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <div class="p-5 flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <span class="ocn-mono text-xs" style="color: #67C2FF;" x-text="lightbox.tag"></span>
                                        <h3 class="text-base font-bold text-white mt-0.5" x-text="lightbox.title"></h3>
                                        <p class="text-xs sm:text-sm mt-1" style="color: #A9BFD8;" x-text="lightbox.caption"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 10: TESTIMONI ALUMNI & FAQ PPLG (DEDICATED SECTION)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-12 lg:py-20" id="alumni-faq" x-data="{ openFaq: 0 }">
                <div class="ocn-container">
                    <div class="ocn-hub-shell">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                            <!-- Left: Alumni Story & Testimonial -->
                            <div class="lg:col-span-5">
                                <div class="ocn-eyebrow mb-3">
                                    <span>Suara Alumni &amp; Tanya Jawab</span>
                                </div>
                                <h2 class="ocn-h2 mb-6">
                                    Cerita Alumni &amp; FAQ PPLG
                                </h2>

                                <div class="ocn-subcard p-6">
                                    <div class="flex items-center gap-4 mb-4">
                                        <img src="{{ asset('images/web/alumni-faras.png') }}"
                                             alt="Faras Haidar, Alumni PPLG SMK Wikrama 1 Garut"
                                             class="w-16 h-16 rounded-full object-cover shrink-0"
                                             style="border: 2px solid rgba(89, 183, 255, 0.55);"
                                             loading="lazy"
                                             decoding="async">
                                        <div>
                                            <div class="text-sm font-bold text-white">Faras Haidar</div>
                                            <div class="text-xs mt-0.5" style="color: #7CCBFF;">Alumni PPLG &bull; Senior Fullstack Developer</div>
                                            <div class="flex items-center gap-1 mt-1.5 text-amber-400" aria-label="Penilaian 5 dari 5">
                                                @for($i = 0; $i < 5; $i++)
                                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                    <blockquote class="text-sm leading-relaxed" style="color: #C9DDF4;">
                                        &ldquo;Di PPLG SMK Wikrama 1 Garut, saya tidak hanya belajar teori, tetapi langsung mengerjakan proyek software agency sungguhan. Terbiasa dengan Git, Laravel, dan kerja tim membuat saya diterima sebagai Software Engineer sebelum genap setahun lulus.&rdquo;
                                    </blockquote>
                                </div>
                            </div>

                            <!-- Right: 4 Interactive FAQ Accordions -->
                            <div class="lg:col-span-7">
                                <div class="ocn-mono text-xs uppercase tracking-wider mb-3" style="color: #7DBBFF;">Pertanyaan yang Sering Diajukan</div>
                                <div>
                                    @foreach($faqs as $idx => $faq)
                                        <div class="ocn-faq-item">
                                            <button type="button"
                                                    @click="openFaq = openFaq === {{ $idx }} ? null : {{ $idx }}"
                                                    :aria-expanded="openFaq === {{ $idx }}"
                                                    class="w-full py-4 flex items-center justify-between gap-4 text-left cursor-pointer">
                                                <span class="text-sm sm:text-base font-semibold text-white">{{ $faq['q'] }}</span>
                                                <svg class="w-4 h-4 shrink-0 ocn-faq-chevron"
                                                     style="color: #59B7FF;"
                                                     :class="{ 'is-open': openFaq === {{ $idx }} }"
                                                     fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
                                                </svg>
                                            </button>
                                            <div x-show="openFaq === {{ $idx }}" x-cloak class="pb-4 pr-6">
                                                <p class="text-xs sm:text-sm leading-relaxed" style="color: #9BB4D0;">{{ $faq['a'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 BAND 7: BOTTOM SLIM STRIP ("STAY IN THE LOOP" -> KONSULTASI & JURUSAN LAIN)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="pt-4 pb-16 lg:pb-20">
                <div class="ocn-container">
                    <div class="ocn-bottom-strip flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="flex items-start sm:items-center gap-4">
                            <span class="ocn-circle-icon" style="width: 40px; height: 40px;" aria-hidden="true">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <div>
                                <div class="text-sm sm:text-base font-bold text-white">Konsultasi Pemilihan Jurusan &amp; Brosur</div>
                                <p class="text-xs mt-0.5" style="color: #9BB4D0;">
                                    Dapatkan informasi jalur pendaftaran SPMB atau bandingkan dengan 3 kompetensi keahlian lainnya.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="ocn-btn-pill whitespace-nowrap" style="height: 38px; padding: 0 18px;">
                                <span>Daftar SPMB</span>
                            </a>
                            <a href="{{ url('/#kontak') }}" class="ocn-btn-outline whitespace-nowrap" style="height: 38px; padding: 0 16px;">
                                <span>Hubungi Kami</span>
                            </a>
                        </div>

                        <div class="ocn-dept-wrap shrink-0">
                            <span class="ocn-mono text-xs uppercase whitespace-nowrap" style="color: #7692B3;">Jurusan lain</span>
                            <div class="flex items-center gap-2">
                                <a href="{{ url('/jurusan/tjkt') }}" class="ocn-dept-chip">TJKT</a>
                                <a href="{{ url('/jurusan/pmn') }}" class="ocn-dept-chip">PMN</a>
                                <a href="{{ url('/jurusan/htl') }}" class="ocn-dept-chip">HTL</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</x-layouts.blank>
