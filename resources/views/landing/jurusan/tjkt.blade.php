{{--
    ================================================================================
    HALAMAN DETAIL JURUSAN: TEKNIK JARINGAN KOMPUTER DAN TELEKOMUNIKASI (TJKT)
    Konsep Desain: A-SPACE Orbital Telecom (#000000 Void, #081628 Surface, #4E9AE6 Sky)
    Menubar & Footer: Menggunakan layout global Wikrama (<x-layouts.blank>)
    URL Akses: /jurusan/tjkt
    ================================================================================
--}}
<x-layouts.blank title="Teknik Jaringan Komputer & Telekomunikasi (TJKT)">
    @php
        $sekolah = $sekolah ?? \App\Modules\Sistem\Models\School::current();
        $schoolName = $sekolah->nama ?? 'SMK Wikrama 1 Garut';

        $stats = [
            ['icon' => 'orbit', 'code' => 'MTCNA & BNSP', 'value' => '100%', 'sub' => 'Sertifikasi MikroTik & BNSP'],
            ['icon' => 'satellite', 'code' => 'INFRASTRUKTUR', 'value' => '4 LAB', 'sub' => 'NOC, Server, Fiber & Cyber'],
            ['icon' => 'globe', 'code' => 'TEFA ISP', 'value' => '2 UNIT', 'sub' => 'TEFA ISP & RT/RW Net Hub'],
            ['icon' => 'signal', 'code' => 'SERAPAN BMW', 'value' => '99%', 'sub' => 'Bekerja, Kuliah & Wirausaha'],
        ];

        $pillars = [
            [
                'code' => '01',
                'title' => 'MikroTik Academy & Huawei ICT Official Center',
                'desc' => 'Siswa diajar langsung oleh instruktur bersertifikasi internasional dan berhak mengikuti ujian sertifikasi MTCNA resmi di sekolah.',
            ],
            [
                'code' => '02',
                'title' => 'Praktik Fiber Optic Fusion Splicing Nyata',
                'desc' => 'Menguasai pemotongan fiber, penyambungan dengan splicer presisi, pengukuran redaman OTDR, dan penarikan kabel udara.',
            ],
            [
                'code' => '03',
                'title' => 'Unit TEFA Pengelola ISP & RT/RW Net',
                'desc' => 'Terlibat langsung dalam pemeliharaan backbone internet sekolah dan layanan jaringan fiber untuk klien sekitar.',
            ],
        ];

        $careers = [
            ['icon' => 'globe', 'title' => 'Network Engineer & NOC Operator', 'desc' => 'Mengoperasikan router backbone, bandwidth management, konfigurasi dynamic routing BGP/OSPF, dan memonitor traffic di Network Operations Center.', 'salary' => 'Rp 6.000.000 – Rp 13.500.000'],
            ['icon' => 'shield', 'title' => 'Cybersecurity & SOC Analyst', 'desc' => 'Melindungi perimeter jaringan dari serangan malware, brute-force, dan kebocoran data dengan firewall filtering, IDS/IPS, dan audit penetrasi.', 'salary' => 'Rp 7.000.000 – Rp 15.000.000'],
            ['icon' => 'zap', 'title' => 'Fiber Optic Project Engineer', 'desc' => 'Spesialis penarikan jaringan fiber FTTH, fusion splicing inti kaca, pengujian rugi-daya optical power meter, dan sertifikasi redaman kabel optik.', 'salary' => 'Rp 5.500.000 – Rp 11.000.000'],
            ['icon' => 'server', 'title' => 'Linux Cloud & Server Administrator', 'desc' => 'Mengelola cluster server berbasis Ubuntu/Debian/Rocky Linux, web server Nginx, container Docker, automasi bash script, dan backup data terjadwal.', 'salary' => 'Rp 6.000.000 – Rp 13.000.000'],
            ['icon' => 'radio', 'title' => 'Wireless Network & IoT Specialist', 'desc' => 'Perancangan radio wireless point-to-point jarak jauh, hotspot gateway captive portal, antenna alignment, dan integrasi sensor IoT cerdas.', 'salary' => 'Rp 5.500.000 – Rp 11.500.000'],
            ['icon' => 'briefcase', 'title' => 'ISP Owner & IT Consultant', 'desc' => 'Mendirikan jasa penyedia layanan internet mandiri (ISP/RT-RW Net), kontraktor instalasi CCTV pabrik/perumahan, dan konsultan IT swasta.', 'salary' => 'Mandiri & Tak Terbatas'],
        ];

        $curriculum = [
            [
                'grade' => 'KELAS X',
                'phase' => 'FASE E • TAHUN 01',
                'title' => 'Dasar Jaringan & Sistem Komputer',
                'desc' => 'Membangun pemahaman arsitektur komputer, subnetting IPv4/IPv6, dan topologi jaringan lokal.',
                'featured' => false,
                'items' => [
                    'Konsep OSI 7-Layer & Protokol TCP/IP',
                    'Subnetting VLSM & CIDR (IPv4 & IPv6)',
                    'Teknik Perkabelan UTP/STP & Crimping Tester',
                    'Instalasi & Dasar Perintah Sistem Operasi Linux',
                    'Keselamatan Kerja Teknisi & K3LH Lapangan',
                ],
            ],
            [
                'grade' => 'KELAS XI',
                'phase' => 'FASE F • TAHUN 02',
                'badge' => 'SPESIALISASI VENDOR',
                'title' => 'Routing, Fiber Optic & Server',
                'desc' => 'Pendalaman konfigurasi routerboard MikroTik, penyambungan serat optik, dan server enterprise.',
                'featured' => true,
                'items' => [
                    'Routing Statis & Dinamis (OSPF/BGP) MikroTik',
                    'Fiber Optic Fusion Splicing & OTDR Certification',
                    'VLAN Segmentation & Enterprise Switch Management',
                    'Enterprise Linux Server (DNS, Web, Mail, Proxy)',
                    'Firewall Filtering, Mangle, & Bandwidth QoS',
                ],
            ],
            [
                'grade' => 'KELAS XII',
                'phase' => 'FASE F • TAHUN 03',
                'title' => 'Magang 6 Bulan & Sertifikasi MTCNA',
                'desc' => 'Penerjunan langsung di ISP / BUMN Telko, ujian sertifikasi internasional MikroTik, dan penyaluran BMW.',
                'featured' => false,
                'items' => [
                    'PKL Industri 6 Bulan di ISP, Telkom, & Vendor Data',
                    'Uji Sertifikasi MikroTik Certified Associate (MTCNA)',
                    'Sertifikasi BNSP Teknisi Madya Jaringan Komputer',
                    'Cybersecurity Defense & Penetration Testing Dasar',
                    'Penyaluran BMW (Bekerja, Melanjutkan, Wirausaha)',
                ],
            ],
        ];

        $labs = [
            [
                'code' => 'LAB-01',
                'tag' => 'Lab Router & Switch',
                'name' => 'Laboratorium Routerboard MikroTik',
                'desc' => 'Dilengkapi 40+ unit RouterBoard RB750/RB951, switch Cisco manageable, kabel patch cord CAT6, dan modular testing bench per meja siswa.',
                'image' => asset('images/landing/lab-tjkt.jpeg'),
                'alt' => 'Lab Jaringan Komputer TJKT Wikrama',
            ],
            [
                'code' => 'LAB-02',
                'tag' => 'Fiber Optic Studio',
                'name' => 'Studio Fusion Splicing & OTDR',
                'desc' => 'Mesin fusion splicer inti kaca presisi, fiber cleaver, Optical Time Domain Reflectometer (OTDR), dan visual fault locator standar Telkom.',
                'image' => asset('images/landing/siswa-berprestasi.jpg'),
                'alt' => 'Tim siswa TJKT bersama perangkat jaringan dan rak server',
            ],
            [
                'code' => 'LAB-03',
                'tag' => 'Cloud Server Center',
                'name' => 'Enterprise Server & Cloud Staging',
                'desc' => 'Rak tertutup rackmount server HP ProLiant dan Dell PowerEdge untuk simulasi virtualisasi Proxmox VE, DNS cluster, dan server cloud.',
                'image' => asset('images/landing/jurusan-tjkt.jpg'),
                'alt' => 'Siswa TJKT merakit dan mengonfigurasi perangkat keras server',
            ],
            [
                'code' => 'LAB-04',
                'tag' => 'NOC Command Center',
                'name' => 'Network Operations Center (NOC)',
                'desc' => 'Layar monitoring traffic multi-monitor dengan Dude NMS, Zabbix, dan Grafana untuk memantau stabilitas internet 24 jam nonstop.',
                'image' => asset('images/landing/suasana-belajar-aktif.jpg'),
                'alt' => 'Ruang monitoring dan praktik komputer SMK Wikrama 1 Garut',
            ],
            [
                'code' => 'LAB-05',
                'tag' => 'Cyber Defense Sandbox',
                'name' => 'Cybersecurity & Sandbox Lab',
                'desc' => 'Lingkungan jaringan terisolasi untuk simulasi packet sniffing Wireshark, uji firewall rules, penanganan insiden siber, dan ethical hacking.',
                'image' => asset('images/landing/ekosistem-belajar.png'),
                'alt' => 'Suasana praktikum jaringan dan keamanan siber siswa',
            ],
            [
                'code' => 'LAB-06',
                'tag' => 'Hardware & Wireless Tower',
                'name' => 'Wireless Tower & Hardware Lab',
                'desc' => 'Tower triangle simulasi antena point-to-point, perakitan PC teknisi, troubleshooting power supply, dan pengujian access point Wi-Fi 6.',
                'image' => asset('images/landing/jurusan-pplg.jpg'),
                'alt' => 'Konfigurasi sistem operasi dan pengujian perangkat jaringan',
            ],
        ];

        $certifications = [
            [
                'code' => 'MTCNA',
                'icon' => 'award',
                'title' => 'MikroTik Certified Network Associate',
                'issuer' => 'MikroTik Certified Academy (Latvia)',
                'desc' => 'Sertifikasi internasional resmi dari MikroTik Latvia (MTCNA) yang membuktikan keahlian routing, bridging, wireless, firewall, QoS, dan VPN tunneling.',
            ],
            [
                'code' => 'SKKNI',
                'icon' => 'radio',
                'title' => 'BNSP Teknisi Madya Jaringan Komputer',
                'issuer' => 'BNSP LSP P-1',
                'desc' => 'Sertifikat kompetensi negara dari BNSP berlogo Garuda untuk kualifikasi pemasangan perangkat jaringan, administrasi server, dan pemeliharaan kabel fiber.',
            ],
            [
                'code' => 'HCIA',
                'icon' => 'shield',
                'title' => 'Huawei ICT Academy Certification',
                'issuer' => 'Huawei ICT Academy Partner',
                'desc' => 'Pelatihan dan sertifikasi resmi Datacom dari Huawei Technologies mencakup protokol switching jaringan enterprise dan arsitektur data center.',
            ],
        ];

        $partners = [
            ['name' => 'PT Telkom Indonesia', 'role' => 'BUMN Telko Terbesar', 'logo' => asset('images/landing/mitra-telkom.png'), 'mono' => 'TL'],
            ['name' => 'MikroTik Latvia', 'role' => 'Academy Partner', 'logo' => asset('images/landing/mitra-mikrotik.jpeg'), 'mono' => 'MT'],
            ['name' => 'Huawei ICT', 'role' => 'Global Tech Vendor', 'logo' => asset('images/landing/mitra-huawei.png'), 'mono' => 'HW'],
            ['name' => 'CBN Internet', 'role' => 'Fiber ISP Enterprise', 'logo' => null, 'mono' => 'CBN'],
            ['name' => 'MyRepublic / Biznet', 'role' => 'Broadband Provider', 'logo' => null, 'mono' => 'MRB'],
            ['name' => 'APJII Jabar', 'role' => 'Asosiasi ISP Nasional', 'logo' => null, 'mono' => 'APJ'],
        ];

        $faqs = [
            [
                'q' => 'Apakah belajar jaringan komputer rumit dan banyak hafalan IP?',
                'a' => 'Tidak rumit jika dipelajari dengan simulasi lab visual. Di Wikrama, 70% pembelajaran adalah praktik langsung konfigurasi router, sehingga logika pengalamatan IP dan routing dipahami secara intuitif melalui studi kasus nyata.',
            ],
            [
                'q' => 'Apakah siswa diajari cara menyambung kabel fiber optik sungguhan?',
                'a' => 'Ya. Sekolah memiliki fasilitas mesin fusion splicer, fiber cleaver, dan alat ukur optik lengkap. Setiap siswa dilatih hingga mahir menyambung serat kaca dan mengukur redaman dB standar industri.',
            ],
            [
                'q' => 'Apakah sertifikat MikroTik MTCNA diakui secara global?',
                'a' => 'Betul. Sertifikat MTCNA diterbitkan langsung dengan nomor lisensi resmi MikroTik Latvia dan tercatat di database global www.mikrotik.com, berlaku internasional dan diakui industri IT di seluruh dunia.',
            ],
            [
                'q' => 'Apakah lulusan TJKT bisa melanjutkan kuliah ke Perguruan Tinggi?',
                'a' => 'Bisa. Banyak lulusan TJKT melanjutkan kuliah di jurusan Teknik Komputer, Teknik Telekomunikasi, Rekayasa Keamanan Siber, dan Sistem Komputer di kampus seperti Telkom University, ITB, PNJ, dan Polban.',
            ],
        ];
    @endphp

    <!-- Aerospace Display Font (Orbitron) paired with global Plus Jakarta Sans & Roboto Mono -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        .spc-page {
            --spc-void: #000000;
            --spc-deep: #030912;
            --spc-card: #081628;
            --spc-card-hi: #0C2038;
            --spc-sky: #4E9AE6;
            --spc-sky-hi: #6DB3F8;
            --spc-ice: #9CD0FF;
            --spc-border: rgba(78, 154, 230, 0.22);
            --spc-border-hi: rgba(109, 179, 248, 0.55);
            --spc-text: #F2F7FD;
            --spc-sub: #A4B6CA;
            --spc-muted: #6E849E;
            --spc-ease: cubic-bezier(0.22, 1, 0.36, 1);

            background-color: var(--spc-void);
            color: var(--spc-text);
            position: relative;
            overflow-x: hidden;
        }

        .spc-display {
            font-family: 'Orbitron', 'Plus Jakarta Sans', sans-serif;
            letter-spacing: 0.04em;
        }

        .spc-mono {
            font-family: 'Roboto Mono', ui-monospace, SFMono-Regular, Menlo, monospace;
            font-variant-numeric: tabular-nums;
        }

        .spc-container {
            max-width: 1180px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 24px;
            padding-right: 24px;
        }
        @media (min-width: 1024px) {
            .spc-container {
                padding-left: 32px;
                padding-right: 32px;
            }
        }

        /* Eyebrow with Horizontal Dash (matching "— OUR MISSION" / "— FUNCTION") */
        .spc-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: 'Orbitron', sans-serif;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--spc-sky-hi);
        }
        .spc-eyebrow::before {
            content: '';
            display: inline-block;
            width: 18px;
            height: 1.5px;
            background-color: var(--spc-sky);
        }

        /* Aerospace Rectangular Buttons (matching "Get Started" / "View All" / "Submit") */
        .spc-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 44px;
            padding: 0 24px;
            border-radius: 4px;
            background-color: var(--spc-sky);
            color: #FFFFFF;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.03em;
            text-decoration: none;
            transition: background-color 160ms var(--spc-ease), transform 160ms var(--spc-ease);
        }
        .spc-btn-primary:hover {
            background-color: var(--spc-sky-hi);
            transform: translateY(-1px);
        }

        .spc-btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 44px;
            padding: 0 22px;
            border-radius: 4px;
            background-color: rgba(8, 22, 40, 0.72);
            color: #E2EEFB;
            border: 1px solid rgba(78, 154, 230, 0.42);
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.03em;
            text-decoration: none;
            transition: background-color 160ms var(--spc-ease), border-color 160ms var(--spc-ease);
        }
        .spc-btn-outline:hover {
            background-color: rgba(78, 154, 230, 0.16);
            border-color: var(--spc-sky-hi);
            color: #FFFFFF;
        }

        /* Hero Orbital Horizon Backdrop */
        .spc-hero {
            position: relative;
            padding-top: 40px;
            padding-bottom: 64px;
            background:
                radial-gradient(680px 440px at 78% 18%, rgba(44, 118, 210, 0.24) 0%, rgba(12, 36, 72, 0.12) 48%, transparent 74%),
                linear-gradient(180deg, #020812 0%, #000000 72%);
        }
        @media (min-width: 1024px) {
            .spc-hero {
                padding-top: 56px;
                padding-bottom: 84px;
            }
        }

        .spc-h1 {
            font-family: 'Orbitron', 'Plus Jakarta Sans', sans-serif;
            font-size: 32px;
            line-height: 1.15;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: #FFFFFF;
        }
        @media (min-width: 640px) {
            .spc-h1 { font-size: 40px; }
        }
        @media (min-width: 1024px) {
            .spc-h1 { font-size: 46px; }
        }

        .spc-h2 {
            font-family: 'Orbitron', 'Plus Jakarta Sans', sans-serif;
            font-size: 24px;
            line-height: 1.2;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #FFFFFF;
        }
        @media (min-width: 640px) {
            .spc-h2 { font-size: 30px; }
        }

        /* Dark Midnight Orbital Cards (matching LEO / HI-SPEED / EARTH blocks) */
        .spc-stat-card {
            background-color: rgba(8, 22, 40, 0.92);
            border: 1px solid rgba(78, 154, 230, 0.20);
            border-radius: 4px;
            padding: 28px 20px;
            text-align: center;
            transition: border-color 180ms var(--spc-ease), transform 180ms var(--spc-ease);
        }
        .spc-stat-card:hover {
            border-color: var(--spc-sky);
            transform: translateY(-2px);
        }

        .spc-card {
            background-color: var(--spc-card);
            border: 1px solid var(--spc-border);
            border-radius: 4px;
            transition: border-color 180ms var(--spc-ease), background-color 180ms var(--spc-ease);
        }
        .spc-card:hover {
            border-color: var(--spc-border-hi);
            background-color: var(--spc-card-hi);
        }

        /* Signature A-SPACE L-Bracket Corner Frames */
        .spc-bracket-rb {
            position: relative;
            padding-right: 16px;
            padding-bottom: 16px;
        }
        .spc-bracket-rb::after {
            content: '';
            position: absolute;
            right: 0;
            bottom: 0;
            width: 82%;
            height: 78%;
            border-right: 1.5px solid var(--spc-sky);
            border-bottom: 1.5px solid var(--spc-sky);
            pointer-events: none;
        }

        .spc-bracket-lb {
            position: relative;
            padding-left: 16px;
            padding-bottom: 16px;
        }
        .spc-bracket-lb::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 82%;
            height: 78%;
            border-left: 1.5px solid var(--spc-sky);
            border-bottom: 1.5px solid var(--spc-sky);
            pointer-events: none;
        }

        /* Silky Orbital Signal Wave Divider */
        .spc-wave-wrap {
            width: 100%;
            overflow: hidden;
            line-height: 0;
            pointer-events: none;
            user-select: none;
        }

        /* NOC CLI Console (A-SPACE Aerospace Style) */
        .spc-terminal {
            background-color: rgba(4, 11, 20, 0.94);
            border: 1px solid rgba(78, 154, 230, 0.35);
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 22px 60px rgba(0, 0, 0, 0.72);
            height: 410px;
            display: flex;
            flex-direction: column;
        }
        .spc-terminal-bar {
            background-color: #071526;
            border-bottom: 1px solid rgba(78, 154, 230, 0.22);
        }
        .spc-terminal-body {
            flex: 1;
            overflow-y: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .spc-terminal-body::-webkit-scrollbar {
            display: none;
        }
        .spc-terminal-foot {
            background-color: #071526;
            border-top: 1px solid rgba(78, 154, 230, 0.22);
        }

        .spc-tag {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 3px;
            background-color: rgba(8, 22, 40, 0.9);
            border: 1px solid rgba(78, 154, 230, 0.28);
            color: var(--spc-ice);
            font-size: 12px;
        }

        .spc-icon-frame {
            width: 40px;
            height: 40px;
            border-radius: 4px;
            background-color: rgba(78, 154, 230, 0.12);
            border: 1px solid rgba(78, 154, 230, 0.3);
            color: var(--spc-sky-hi);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .spc-icon-frame svg {
            width: 18px;
            height: 18px;
        }

        .spc-logo-pill {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 4px;
            padding: 6px 12px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        .spc-logo-pill img {
            height: 22px;
            width: auto;
            max-width: 96px;
            object-fit: contain;
        }

        .spc-faq-row {
            border-bottom: 1px solid rgba(78, 154, 230, 0.2);
        }
        .spc-faq-chevron {
            transition: transform 200ms var(--spc-ease);
        }
        .spc-faq-chevron.is-open {
            transform: rotate(180deg);
        }

        .spc-testimonial-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 28px;
            align-items: center;
        }
        @media (min-width: 768px) {
            .spc-testimonial-grid {
                grid-template-columns: 132px 1fr;
                gap: 36px;
            }
        }
    </style>

    <div class="spc-page">
        <!-- ═══════════════════════════════════════════════════════════════════════
             SECTION 1: HERO — ORBITAL NETWORK INFRASTRUCTURE & NOC CONSOLE
             ═══════════════════════════════════════════════════════════════════════ -->
        <header class="spc-hero">
            <!-- Luminous Orbital Globe & Satellite Arc Artwork (Top-Right Space Backdrop) -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden z-0" aria-hidden="true">
                <svg viewBox="0 0 1440 620" class="w-full h-full object-cover" preserveAspectRatio="xMidYMid slice" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <radialGradient id="earthGlow" cx="76%" cy="28%" r="42%">
                            <stop offset="0%" stop-color="#6CB8FF" stop-opacity="0.34"/>
                            <stop offset="46%" stop-color="#1E5EA8" stop-opacity="0.16"/>
                            <stop offset="100%" stop-color="#000000" stop-opacity="0"/>
                        </radialGradient>
                        <radialGradient id="planetBody" cx="68%" cy="30%" r="55%">
                            <stop offset="0%" stop-color="#4A9AE6" stop-opacity="0.42"/>
                            <stop offset="52%" stop-color="#0A2342" stop-opacity="0.88"/>
                            <stop offset="100%" stop-color="#01050C" stop-opacity="0.98"/>
                        </radialGradient>
                        <linearGradient id="rimArc" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#B9E1FF" stop-opacity="0.85"/>
                            <stop offset="55%" stop-color="#4E9AE6" stop-opacity="0.35"/>
                            <stop offset="100%" stop-color="#000000" stop-opacity="0"/>
                        </linearGradient>
                    </defs>

                    <rect width="1440" height="620" fill="url(#earthGlow)"/>

                    <!-- Crisp Starfield Dots -->
                    <circle cx="140" cy="72" r="1.2" fill="#FFFFFF" opacity="0.55"/>
                    <circle cx="290" cy="128" r="1" fill="#9CD0FF" opacity="0.45"/>
                    <circle cx="480" cy="64" r="1.4" fill="#FFFFFF" opacity="0.6"/>
                    <circle cx="620" cy="112" r="1" fill="#FFFFFF" opacity="0.4"/>
                    <circle cx="1290" cy="96" r="1.3" fill="#9CD0FF" opacity="0.5"/>
                    <circle cx="1360" cy="210" r="1" fill="#FFFFFF" opacity="0.45"/>
                    <circle cx="88" cy="260" r="1" fill="#9CD0FF" opacity="0.35"/>
                    <circle cx="540" cy="240" r="1.2" fill="#FFFFFF" opacity="0.35"/>

                    <!-- Planetary Globe Silhouette + Luminous Atmosphere Limb -->
                    <circle cx="1030" cy="295" r="290" fill="url(#planetBody)" stroke="url(#rimArc)" stroke-width="2"/>
                    <circle cx="1030" cy="295" r="304" stroke="#4E9AE6" stroke-opacity="0.18" stroke-width="1"/>

                    <!-- Global Network Latitude/Longitude Orbital Lines -->
                    <ellipse cx="1030" cy="295" rx="290" ry="92" transform="rotate(-18 1030 295)" stroke="#6DB3F8" stroke-opacity="0.22" stroke-width="1" stroke-dasharray="4 6"/>
                    <ellipse cx="1030" cy="295" rx="220" ry="290" transform="rotate(24 1030 295)" stroke="#6DB3F8" stroke-opacity="0.15" stroke-width="1"/>
                    <ellipse cx="1030" cy="295" rx="360" ry="120" transform="rotate(-12 1030 295)" stroke="#9CD0FF" stroke-opacity="0.26" stroke-width="1.2"/>

                    <!-- Satellite Relay Nodes -->
                    <circle cx="770" cy="186" r="3.5" fill="#C4E6FF"/>
                    <circle cx="770" cy="186" r="9" fill="#4E9AE6" fill-opacity="0.25"/>
                    <circle cx="1268" cy="160" r="3" fill="#C4E6FF"/>
                    <circle cx="1268" cy="160" r="8" fill="#4E9AE6" fill-opacity="0.22"/>
                </svg>
            </div>

            <div class="spc-container relative z-10">
                <!-- Breadcrumbs -->
                <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs mb-6" style="color: #7D95B0;">
                    <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
                    <span>/</span>
                    <a href="{{ url('/jurusan') }}" class="hover:text-white transition-colors">Jurusan</a>
                    <span>/</span>
                    <span class="spc-mono text-white">TJKT</span>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                    <!-- Left Column: Aerospace Headline, Description, Badges & Rectangular CTAs -->
                    <div class="lg:col-span-7">
                        <div class="spc-eyebrow mb-4">
                            Infrastruktur Siber &bull; Fiber Optik &bull; Terakreditasi A
                        </div>

                        <h1 class="spc-h1">
                            Teknik Jaringan Komputer &amp; Telekomunikasi
                        </h1>

                        <p class="mt-5 text-sm sm:text-base leading-relaxed max-w-xl" style="color: #B6C7DA;">
                            Mencetak arsitek infrastruktur jaringan siber, spesialis fiber optik, dan administrator cloud computing handal di {{ $schoolName }}. Siswa menguasai perancangan jaringan skala enterprise, konfigurasi router MikroTik dan Huawei, pertahanan keamanan siber, hingga server Linux.
                        </p>

                        <!-- Vendor Academy Spec Tags -->
                        <div class="mt-6 flex flex-wrap gap-2 spc-mono">
                            <span class="spc-tag">MikroTik MTCNA</span>
                            <span class="spc-tag">Huawei ICT Academy</span>
                            <span class="spc-tag">Linux Server Admin</span>
                            <span class="spc-tag">Fiber Optic Fusion Splicer</span>
                            <span class="spc-tag">Cloud &amp; Virtualization</span>
                            <span class="spc-tag">Cyber Defense</span>
                        </div>

                        <!-- Rectangular A-SPACE CTA Buttons -->
                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="spc-btn-primary">
                                <span>Daftar TJKT Sekarang</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                            <a href="#kurikulum" class="spc-btn-outline">
                                <span>Lihat Kurikulum &amp; Lab</span>
                            </a>
                        </div>
                    </div>

                    <!-- Right Column: Aerospace NOC Live RouterOS & Linux CLI Console -->
                    <div class="lg:col-span-5">
                        <div class="spc-terminal">
                            <!-- Titlebar -->
                            <div class="spc-terminal-bar px-4 py-3 flex items-center justify-between gap-2 shrink-0">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-2.5 h-2.5 rounded-full" style="background: rgba(156, 208, 255, 0.35);"></span>
                                    <span class="w-2.5 h-2.5 rounded-full" style="background: rgba(156, 208, 255, 0.25);"></span>
                                    <span class="w-2.5 h-2.5 rounded-full" style="background: rgba(78, 154, 230, 0.85);"></span>
                                    <span class="spc-mono text-xs truncate pl-1" style="color: #9CD0FF;">admin@wikrama-noc:~#</span>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span id="tjkt-term-badge" class="spc-mono text-xs px-2 py-0.5 rounded" style="background: rgba(78, 154, 230, 0.16); color: #9CD0FF; border: 1px solid rgba(78, 154, 230, 0.32);">
                                        RouterOS v7.14
                                    </span>
                                    <button type="button" id="tjkt-replay-btn" class="spc-mono text-xs px-2 py-0.5 rounded cursor-pointer" style="background: rgba(255, 255, 255, 0.06); color: #A4B6CA;">
                                        Ulang
                                    </button>
                                </div>
                            </div>

                            <!-- CLI Screen -->
                            <div id="tjkt-terminal-screen" class="spc-terminal-body p-4 spc-mono text-xs leading-relaxed space-y-1.5 select-text">
                                <div id="tjkt-terminal-logs" class="space-y-1.5"></div>
                                <div id="tjkt-terminal-input-row" class="flex items-center flex-wrap pt-1 font-semibold">
                                    <span id="tjkt-terminal-prompt" class="mr-1.5" style="color: #6DB3F8;">[admin@Wikrama-Core] &gt; </span>
                                    <span id="tjkt-terminal-cmd" class="text-white"></span>
                                    <span id="tjkt-cursor" class="inline-block ml-1" style="width: 6px; height: 14px; background-color: #4E9AE6;"></span>
                                </div>
                            </div>

                            <!-- Footer Telemetry -->
                            <div class="spc-terminal-foot px-4 py-2.5 flex items-center justify-between gap-2 spc-mono text-xs shrink-0" style="color: #7D95B0;">
                                <div class="flex items-center gap-3">
                                    <span>SSH-256</span>
                                    <span>10 Gbps SFP+</span>
                                </div>
                                <span style="color: #6DB3F8;">99.98% NOC Uptime</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 2: STATS & KEY PILLARS STRIP OVER ORBITAL WAVE RIBBON
                 (MATCHING A-SPACE "LEO / HI-SPEED / EARTH" BAND)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="relative py-8 lg:py-12" aria-label="Ringkasan capaian TJKT">
                <!-- Background Silky Orbital Signal Wave -->
                <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 pointer-events-none z-0 opacity-75" aria-hidden="true">
                    <svg viewBox="0 0 1440 160" class="w-full h-32 sm:h-40" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 95 C260 22, 520 148, 760 80 C1000 12, 1220 135, 1440 62" stroke="#4E9AE6" stroke-opacity="0.45" stroke-width="1.4"/>
                        <path d="M0 108 C280 38, 500 156, 775 88 C1020 22, 1210 142, 1440 76" stroke="#6DB3F8" stroke-opacity="0.32" stroke-width="1.1"/>
                        <path d="M0 120 C295 52, 485 162, 790 96 C1040 34, 1205 148, 1440 90" stroke="#9CD0FF" stroke-opacity="0.22" stroke-width="0.9"/>
                        <path d="M0 82 C240 10, 535 138, 745 72 C985 6, 1235 128, 1440 50" stroke="#4E9AE6" stroke-opacity="0.25" stroke-width="0.9"/>
                    </svg>
                </div>

                <div class="spc-container relative z-10">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                        @foreach($stats as $stat)
                            <div class="spc-stat-card">
                                <div class="inline-flex items-center justify-center w-11 h-11 mb-4" style="color: #6DB3F8;">
                                    @if($stat['icon'] === 'orbit')
                                        <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="2.2"/><ellipse cx="12" cy="12" rx="9" ry="4" transform="rotate(30 12 12)"/><ellipse cx="12" cy="12" rx="9" ry="4" transform="rotate(-30 12 12)"/><ellipse cx="12" cy="12" rx="9" ry="4" transform="rotate(90 12 12)"/></svg>
                                    @elseif($stat['icon'] === 'satellite')
                                        <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m13 7 4 4m-9 1 4 4m-9 2 3-3m11-11 3 3-4 4-3-3 4-4ZM6 10l4-4 4 4-4 4-4-4Zm10 6a4 4 0 0 1-4 4m4-1a1 1 0 0 1-1 1"/></svg>
                                    @elseif($stat['icon'] === 'globe')
                                        <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/></svg>
                                    @else
                                        <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.9 19.1C1 15.2 1 8.8 4.9 4.9m14.2 0c3.9 3.9 3.9 10.3 0 14.2M7.8 16.2a6 6 0 0 1 0-8.4m8.4 0a6 6 0 0 1 0 8.4M12 12h.01"/></svg>
                                    @endif
                                </div>
                                <div class="spc-display text-xs" style="color: #9CD0FF;">{{ $stat['code'] }}</div>
                                <div class="spc-display text-2xl sm:text-3xl font-bold text-white mt-2">{{ $stat['value'] }}</div>
                                <div class="text-xs mt-2" style="color: #8CA4BE;">{{ $stat['sub'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 3: PROFIL JURUSAN (MATCHING "— OUR MISSION / RAPIDSCAT" + L-BRACKET)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-16 lg:py-24" id="profil">
                <div class="spc-container">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
                        <!-- Left: Eyebrow, Wide Heading, Story & 3 Structured Pillars -->
                        <div class="lg:col-span-6">
                            <div class="spc-eyebrow mb-3">Tentang Kompetensi Keahlian</div>
                            <h2 class="spc-h2">
                                Mengapa Memilih TJKT Wikrama Garut
                            </h2>

                            <div class="mt-5 space-y-4 text-sm sm:text-base leading-relaxed" style="color: #B4C5D8;">
                                <p>
                                    Program keahlian Teknik Jaringan Komputer dan Telekomunikasi (TJKT) di {{ $schoolName }} dirancang untuk menghasilkan <span class="text-white font-semibold">arsitek infrastruktur jaringan dan pertahanan siber</span> yang tangguh, adaptif, serta berintegritas tinggi.
                                </p>
                                <p>
                                    Siswa tidak hanya belajar merakit komputer atau crimping kabel UTP biasa, melainkan mengoperasikan langsung perangkat routing enterprise MikroTik, switch serat optik, mesin fusion splicer fiber optic, server virtualization Proxmox, dan konfigurasi firewall filtering.
                                </p>
                            </div>

                            <div class="mt-8 space-y-3">
                                @foreach($pillars as $pillar)
                                    <div class="spc-card p-4 flex items-start gap-4">
                                        <span class="spc-mono text-xs px-2 py-1 rounded shrink-0" style="background: rgba(78, 154, 230, 0.16); color: #9CD0FF; border: 1px solid rgba(78, 154, 230, 0.3);">
                                            {{ $pillar['code'] }}
                                        </span>
                                        <div>
                                            <h3 class="text-sm font-bold text-white">{{ $pillar['title'] }}</h3>
                                            <p class="mt-1 text-xs leading-relaxed" style="color: #9BB0C6;">{{ $pillar['desc'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Right: Signature A-SPACE L-Bracketed Real TJKT Photos + TEFA Units -->
                        <div class="lg:col-span-6 space-y-6">
                            <!-- Main L-Bracket Photo -->
                            <div class="spc-bracket-rb">
                                <div class="relative overflow-hidden rounded" style="aspect-ratio: 16 / 10; border: 1px solid rgba(78, 154, 230, 0.25); background-color: #081628;">
                                    <img src="{{ asset('images/landing/lab-tjkt.jpeg') }}"
                                         alt="Lab Jaringan Komputer dan Server TJKT Wikrama"
                                         class="w-full h-full object-cover"
                                         loading="lazy"
                                         decoding="async">
                                    <div class="absolute inset-0 pointer-events-none" style="background: linear-gradient(180deg, rgba(0,0,0,0.05) 0%, rgba(2,10,22,0.85) 100%);"></div>
                                    <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between gap-2">
                                        <span class="spc-mono text-xs" style="color: #9CD0FF;">NOC &amp; ROUTING LAB • 10 GBPS</span>
                                        <span class="spc-mono text-xs text-white">SMK Wikrama 1 Garut</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Two TEFA Unit Blocks + Real Hardware Photo -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="spc-card p-5">
                                    <div class="spc-mono text-xs" style="color: #6DB3F8;">UNIT PRODUKSI TEFA</div>
                                    <h3 class="mt-2 text-base font-bold text-white">Wikrama Network Center</h3>
                                    <p class="mt-1.5 text-xs leading-relaxed" style="color: #9BB0C6;">
                                        Unit operasional yang memelihara jaringan hotspot, server data, dan CCTV di seluruh kampus.
                                    </p>
                                </div>
                                <div class="spc-card p-5">
                                    <div class="spc-mono text-xs" style="color: #6DB3F8;">SPESIALISASI UNGGULAN</div>
                                    <h3 class="mt-2 text-base font-bold text-white">Fiber Optic &amp; Server Lab</h3>
                                    <p class="mt-1.5 text-xs leading-relaxed" style="color: #9BB0C6;">
                                        Fasilitas splicing mandiri dan konfigurasi cloud server berkecepatan tinggi 10 Gbps.
                                    </p>
                                </div>
                            </div>

                            <!-- Second Authentic TJKT Student Photo with Left L-Bracket -->
                            <div class="spc-bracket-lb">
                                <div class="relative overflow-hidden rounded" style="aspect-ratio: 16 / 9; border: 1px solid rgba(78, 154, 230, 0.25); background-color: #081628;">
                                    <img src="{{ asset('images/landing/jurusan-tjkt.jpg') }}"
                                         alt="Praktik perakitan perangkat server dan komputer siswa TJKT"
                                         class="w-full h-full object-cover"
                                         loading="lazy"
                                         decoding="async">
                                    <div class="absolute inset-0 pointer-events-none" style="background: linear-gradient(180deg, rgba(0,0,0,0.05) 0%, rgba(2,10,22,0.82) 100%);"></div>
                                    <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between gap-2">
                                        <span class="spc-mono text-xs" style="color: #9CD0FF;">PRAKTIKUM HARDWARE &amp; SERVER</span>
                                        <span class="spc-mono text-xs text-white">70% Praktik Lab</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Luminous Orbital Signal Wave Divider -->
            <div class="spc-wave-wrap py-2" aria-hidden="true">
                <svg viewBox="0 0 1440 110" class="w-full h-20 sm:h-24" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 58 C240 8, 460 102, 720 54 C980 6, 1200 100, 1440 46" stroke="#4E9AE6" stroke-opacity="0.45" stroke-width="1.4"/>
                    <path d="M0 68 C255 18, 445 108, 735 62 C995 16, 1185 106, 1440 58" stroke="#6DB3F8" stroke-opacity="0.3" stroke-width="1"/>
                    <path d="M0 48 C225 2, 475 94, 705 46 C965 0, 1215 92, 1440 36" stroke="#9CD0FF" stroke-opacity="0.2" stroke-width="0.9"/>
                </svg>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 4: PROSPEK KARIR & PELUANG PEKERJAAN (#karir)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-16 lg:py-24" id="karir">
                <div class="spc-container">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-end mb-12">
                        <div class="lg:col-span-7">
                            <div class="spc-eyebrow mb-3">NOC Telemetry &bull; 100% Match Telecom &amp; ISP</div>
                            <h2 class="spc-h2">Prospek Karier Lulusan TJKT</h2>
                        </div>
                        <div class="lg:col-span-5">
                            <p class="text-sm leading-relaxed" style="color: #A4B6CA;">
                                Setiap perusahaan, perbankan, instansi pemerintah, dan rumah sakit memerlukan infrastruktur jaringan dan pertahanan siber yang handal.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($careers as $career)
                            <article class="spc-card p-6 flex flex-col justify-between">
                                <div>
                                    <span class="spc-icon-frame mb-5" aria-hidden="true">
                                        @if($career['icon'] === 'globe')
                                            <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/></svg>
                                        @elseif($career['icon'] === 'shield')
                                            <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3 4 7v5c0 5 3.5 8.5 8 9 4.5-.5 8-4 8-9V7l-8-4Z"/></svg>
                                        @elseif($career['icon'] === 'zap')
                                            <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"/></svg>
                                        @elseif($career['icon'] === 'server')
                                            <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><rect width="20" height="8" x="2" y="3" rx="2"/><rect width="20" height="8" x="2" y="13" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h.01M6 17h.01"/></svg>
                                        @elseif($career['icon'] === 'radio')
                                            <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.9 19.1C1 15.2 1 8.8 4.9 4.9m14.2 0c3.9 3.9 3.9 10.3 0 14.2M8.5 15.5a5 5 0 0 1 0-7m7 0a5 5 0 0 1 0 7M12 12h.01"/></svg>
                                        @else
                                            <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="7" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                        @endif
                                    </span>

                                    <h3 class="text-base font-bold text-white leading-snug">{{ $career['title'] }}</h3>
                                    <p class="mt-2 text-xs sm:text-sm leading-relaxed" style="color: #9BB0C6;">{{ $career['desc'] }}</p>
                                </div>

                                <div class="mt-5 pt-4 flex items-center justify-between gap-2 text-xs" style="border-top: 1px solid rgba(78, 154, 230, 0.16);">
                                    <span style="color: #6E849E;">Range Gaji</span>
                                    <span class="spc-mono font-semibold" style="color: #6DB3F8;">{{ $career['salary'] }}</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 5: KURIKULUM 3 TAHUN (FASE E & FASE F) (#kurikulum)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-16 lg:py-24" id="kurikulum" style="background: linear-gradient(180deg, #000000 0%, #030B16 50%, #000000 100%);">
                <div class="spc-container">
                    <div class="max-w-3xl mb-12">
                        <div class="spc-eyebrow mb-3">Roadmap Pembelajaran</div>
                        <h2 class="spc-h2">Kurikulum 3 Tahun Berstandar MikroTik &amp; Huawei</h2>
                        <p class="mt-4 text-sm sm:text-base leading-relaxed" style="color: #A4B6CA;">
                            Disusun bersama asosiasi penyelenggara internet (APJII) dan mitra telko terkemuka, menggabungkan teori fundamental dan praktik lab riil.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        @foreach($curriculum as $step)
                            <div class="spc-card p-6 sm:p-7 flex flex-col relative overflow-hidden" @if($step['featured']) style="border-color: rgba(78, 154, 230, 0.6);" @endif>
                                @if($step['featured'])
                                    <div class="absolute top-0 inset-x-0 h-1" style="background-color: #4E9AE6;" aria-hidden="true"></div>
                                @endif

                                <div class="flex items-center justify-between gap-2 mb-5">
                                    <span class="spc-mono text-xs" style="color: #6DB3F8;">{{ $step['phase'] }}</span>
                                    <span class="spc-display text-xs px-2.5 py-1 rounded" style="background: rgba(78, 154, 230, 0.16); color: #FFFFFF; border: 1px solid rgba(78, 154, 230, 0.32);">
                                        {{ $step['grade'] }}
                                    </span>
                                </div>

                                <h3 class="text-lg font-bold text-white leading-snug">{{ $step['title'] }}</h3>
                                <p class="mt-2 text-xs sm:text-sm leading-relaxed" style="color: #9BB0C6;">{{ $step['desc'] }}</p>

                                <ul class="mt-6 pt-5 space-y-3 text-xs sm:text-sm flex-1" style="border-top: 1px solid rgba(78, 154, 230, 0.16); color: #DCE9F8;">
                                    @foreach($step['items'] as $item)
                                        <li class="flex items-start gap-2.5">
                                            <svg class="w-4 h-4 shrink-0 mt-0.5" style="color: #4E9AE6;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
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
                 SECTION 6: KOLASE LABORATORIUM & STUDIO JARINGAN NYATA (#lab)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-16 lg:py-24" id="lab">
                <div class="spc-container">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-end mb-12">
                        <div class="lg:col-span-7">
                            <div class="spc-eyebrow mb-3">Fasilitas Standar Industri Telekomunikasi</div>
                            <h2 class="spc-h2">Kolase Laboratorium &amp; Studio Jaringan Nyata</h2>
                        </div>
                        <div class="lg:col-span-5">
                            <p class="text-sm leading-relaxed" style="color: #A4B6CA;">
                                Laboratorium dirancang mirip Network Operations Center (NOC) nyata dengan rak server, switch manageable, dan perangkat routerboard aktif.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($labs as $lab)
                            <article class="spc-card overflow-hidden flex flex-col">
                                <div class="relative aspect-video overflow-hidden" style="background-color: #030912; border-bottom: 1px solid rgba(78, 154, 230, 0.18);">
                                    <img src="{{ $lab['image'] }}"
                                         alt="{{ $lab['alt'] }}"
                                         class="w-full h-full object-cover"
                                         loading="lazy"
                                         decoding="async">
                                    <div class="absolute inset-0 pointer-events-none" style="background: linear-gradient(180deg, rgba(0,0,0,0.05) 0%, rgba(2,10,22,0.72) 100%);"></div>
                                    <span class="absolute bottom-3 left-3 spc-mono text-xs px-2 py-0.5 rounded" style="background: rgba(8, 22, 40, 0.88); color: #9CD0FF; border: 1px solid rgba(78, 154, 230, 0.35);">
                                        {{ $lab['code'] }} &bull; {{ $lab['tag'] }}
                                    </span>
                                </div>
                                <div class="p-5 flex-1 flex flex-col">
                                    <h3 class="text-base font-bold text-white leading-snug">{{ $lab['name'] }}</h3>
                                    <p class="mt-2 text-xs sm:text-sm leading-relaxed" style="color: #9BB0C6;">{{ $lab['desc'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- Luminous Orbital Signal Wave Divider -->
            <div class="spc-wave-wrap py-2" aria-hidden="true">
                <svg viewBox="0 0 1440 110" class="w-full h-20 sm:h-24" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 46 C260 98, 500 10, 740 58 C980 106, 1210 14, 1440 62" stroke="#4E9AE6" stroke-opacity="0.45" stroke-width="1.4"/>
                    <path d="M0 58 C275 106, 485 20, 755 68 C995 112, 1195 24, 1440 72" stroke="#6DB3F8" stroke-opacity="0.3" stroke-width="1"/>
                    <path d="M0 36 C245 88, 515 4, 725 48 C965 98, 1225 6, 1440 52" stroke="#9CD0FF" stroke-opacity="0.2" stroke-width="0.9"/>
                </svg>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 7: SERTIFIKASI KOMPETENSI RESMI (BNSP, MIKROTIK, HUAWEI)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-16 lg:py-24" id="sertifikasi">
                <div class="spc-container">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-end mb-12">
                        <div class="lg:col-span-7">
                            <div class="spc-eyebrow mb-3">Pengakuan Industri Internasional</div>
                            <h2 class="spc-h2">Sertifikasi Kompetensi Resmi TJKT</h2>
                        </div>
                        <div class="lg:col-span-5">
                            <p class="text-sm leading-relaxed" style="color: #A4B6CA;">
                                Lulusan dibekali sertifikasi vendor global yang diakui di seluruh dunia serta lisensi sertifikasi berlogo Garuda BNSP.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($certifications as $cert)
                            <article class="spc-card p-6 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-5">
                                        <span class="spc-icon-frame" aria-hidden="true">
                                            @if($cert['icon'] === 'award')
                                                <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path stroke-linecap="round" stroke-linejoin="round" d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                                            @elseif($cert['icon'] === 'radio')
                                                <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.9 19.1C1 15.2 1 8.8 4.9 4.9m14.2 0c3.9 3.9 3.9 10.3 0 14.2M8.5 15.5a5 5 0 0 1 0-7m7 0a5 5 0 0 1 0 7M12 12h.01"/></svg>
                                            @else
                                                <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3 4 7v5c0 5 3.5 8.5 8 9 4.5-.5 8-4 8-9V7l-8-4Z"/></svg>
                                            @endif
                                        </span>
                                        <span class="spc-mono text-xs px-2.5 py-0.5 rounded" style="background: rgba(78, 154, 230, 0.16); color: #9CD0FF; border: 1px solid rgba(78, 154, 230, 0.3);">
                                            {{ $cert['code'] }}
                                        </span>
                                    </div>
                                    <h3 class="text-base font-bold text-white leading-snug">{{ $cert['title'] }}</h3>
                                    <p class="mt-2 text-xs sm:text-sm leading-relaxed" style="color: #9BB0C6;">{{ $cert['desc'] }}</p>
                                </div>
                                <div class="mt-5 pt-4 spc-mono text-xs" style="border-top: 1px solid rgba(78, 154, 230, 0.16); color: #6DB3F8;">
                                    {{ $cert['issuer'] }}
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 8: MITRA DUDI & TEMPAT MAGANG (#mitra)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-16 lg:py-20" id="mitra" style="background-color: #030A14; border-top: 1px solid rgba(78, 154, 230, 0.14); border-bottom: 1px solid rgba(78, 154, 230, 0.14);">
                <div class="spc-container">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-end mb-10">
                        <div class="lg:col-span-7">
                            <div class="spc-eyebrow mb-3">Jaringan Industri Telekomunikasi</div>
                            <h2 class="spc-h2">Mitra PKL &amp; Rekrutmen Lulusan</h2>
                        </div>
                        <div class="lg:col-span-5">
                            <p class="text-sm leading-relaxed" style="color: #A4B6CA;">
                                Siswa menjalani magang nyata selama 6 bulan di perusahaan ISP, BUMN Telkom, data center, dan vendor IT terkemuka.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                        @foreach($partners as $partner)
                            <div class="spc-card p-4 text-center flex flex-col items-center justify-center">
                                @if($partner['logo'])
                                    <div class="spc-logo-pill">
                                        <img src="{{ $partner['logo'] }}" alt="Logo {{ $partner['name'] }}" loading="lazy" decoding="async">
                                    </div>
                                @else
                                    <div class="spc-display text-xs font-bold mb-2.5 flex items-center justify-center rounded" style="height: 36px; min-width: 56px; padding: 0 10px; background: rgba(78, 154, 230, 0.14); border: 1px solid rgba(78, 154, 230, 0.34); color: #9CD0FF;">
                                        {{ $partner['mono'] }}
                                    </div>
                                @endif
                                <div class="text-xs font-bold text-white">{{ $partner['name'] }}</div>
                                <div class="text-xs mt-0.5" style="color: #7D95B0;">{{ $partner['role'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 9: TESTIMONI ALUMNI TJKT (MATCHING "— FUNCTION / SATELLITE" L-BRACKET)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-16 lg:py-24" id="alumni">
                <div class="spc-container">
                    <div class="max-w-5xl mx-auto spc-bracket-lb">
                        <div class="spc-card p-8 sm:p-10">
                            <div class="spc-testimonial-grid">
                                <div class="flex items-start md:items-center">
                                    <img src="{{ asset('images/web/alumni-ichsan.png') }}"
                                         alt="Ichsan Nugraha, Alumni TJKT SMK Wikrama 1 Garut"
                                         class="w-24 h-24 sm:w-28 sm:h-28 rounded object-cover"
                                         style="border: 1.5px solid rgba(78, 154, 230, 0.65);"
                                         loading="lazy"
                                         decoding="async">
                                </div>

                                <div>
                                    <div class="spc-eyebrow mb-3">Suara Alumni TJKT</div>
                                    <blockquote class="text-sm sm:text-base leading-relaxed" style="color: #D8E7F8;">
                                        &ldquo;Di TJKT Wikrama, kita tidak hanya diajar cara merakit PC, tapi langsung pegang router MikroTik RB Series dan mesin splicing fiber optik sungguhan. Sertifikat MTCNA yang saya peroleh di sekolah menjadi kunci utama saya langsung diterima sebagai Network Engineer di ISP nasional.&rdquo;
                                    </blockquote>

                                    <div class="mt-6 pt-5 flex flex-wrap items-center justify-between gap-3" style="border-top: 1px solid rgba(78, 154, 230, 0.18);">
                                        <div>
                                            <div class="text-base font-bold text-white">Ichsan Nugraha</div>
                                            <div class="text-xs mt-0.5" style="color: #9CD0FF;">Alumni TJKT &bull; Network Operations Specialist di Internet Provider</div>
                                        </div>
                                        <div class="flex items-center gap-1" style="color: #6DB3F8;" aria-label="Penilaian 5 dari 5">
                                            @for($i = 0; $i < 5; $i++)
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 10: FAQ SEPUTAR JURUSAN TJKT (#faq)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-16 lg:py-24" id="faq" style="background-color: #030A14; border-top: 1px solid rgba(78, 154, 230, 0.14);">
                <div class="spc-container">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                        <div class="lg:col-span-4">
                            <div class="spc-eyebrow mb-3">Pertanyaan Umum</div>
                            <h2 class="spc-h2">FAQ Jurusan TJKT</h2>
                            <p class="mt-4 text-sm leading-relaxed" style="color: #A4B6CA;">
                                Informasi cepat mengenai praktik fiber optik, pengalamatan IP, pengakuan sertifikat MikroTik MTCNA, dan peluang melanjutkan kuliah.
                            </p>
                        </div>

                        <div class="lg:col-span-8" x-data="{ openFaq: 0 }" style="border-top: 1px solid rgba(78, 154, 230, 0.2);">
                            @foreach($faqs as $idx => $faq)
                                <div class="spc-faq-row">
                                    <button type="button"
                                            @click="openFaq = openFaq === {{ $idx }} ? null : {{ $idx }}"
                                            :aria-expanded="openFaq === {{ $idx }}"
                                            class="w-full py-5 flex items-center justify-between gap-4 text-left cursor-pointer">
                                        <span class="text-sm sm:text-base font-semibold text-white">{{ $faq['q'] }}</span>
                                        <svg class="w-4 h-4 shrink-0 spc-faq-chevron"
                                             style="color: #6DB3F8;"
                                             :class="{ 'is-open': openFaq === {{ $idx }} }"
                                             fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
                                        </svg>
                                    </button>
                                    <div x-show="openFaq === {{ $idx }}" x-cloak class="pb-5 pr-6">
                                        <p class="text-xs sm:text-sm leading-relaxed" style="color: #A4B6CA;">{{ $faq['a'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════════
                 SECTION 11: CTA DAFTAR TJKT (A-SPACE ORBITAL CLOSING BAND)
                 ═══════════════════════════════════════════════════════════════════ -->
            <section class="py-16 lg:py-24 relative overflow-hidden" style="background: radial-gradient(640px 320px at 50% 100%, rgba(44, 118, 210, 0.28) 0%, rgba(4, 14, 28, 0.92) 68%, #000000 100%); border-top: 1px solid rgba(78, 154, 230, 0.22);">
                <div class="spc-container relative z-10">
                    <div class="max-w-4xl mx-auto text-center">
                        <div class="spc-eyebrow mb-4">Penerimaan Peserta Didik Baru (SPMB)</div>
                        <h2 class="spc-h2">
                            Siap Menjadi Arsitek Jaringan Siber &amp; Infrastruktur Cloud Masa Depan
                        </h2>
                        <p class="mt-4 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed" style="color: #B4C5D8;">
                            Kuota kelas TJKT sangat terbatas demi menjaga intensitas bimbingan perangkat lab router dan keselamatan praktik fiber optik. Amankan kursi belajarmu sekarang.
                        </p>
                        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                            <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="spc-btn-primary">
                                <span>Daftar SPMB Online Sekarang</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                            <a href="{{ url('/jurusan') }}" class="spc-btn-outline">
                                <span aria-hidden="true">&larr;</span>
                                <span>Lihat Jurusan Lainnya</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- ── 12. CLIENT-SIDE ROUTEROS & LINUX NOC CLI SIMULATOR (CALM, REPLAYABLE) ── -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const termLogs = document.getElementById('tjkt-terminal-logs');
            const termPrompt = document.getElementById('tjkt-terminal-prompt');
            const termCmd = document.getElementById('tjkt-terminal-cmd');
            const termScreen = document.getElementById('tjkt-terminal-screen');
            const termBadge = document.getElementById('tjkt-term-badge');
            const inputRow = document.getElementById('tjkt-terminal-input-row');
            const replayBtn = document.getElementById('tjkt-replay-btn');

            if (!termLogs || !termPrompt || !termCmd || !termScreen) return;

            const terminalSequence = [
                {
                    badge: 'RouterOS v7.14',
                    prompt: '[admin@Wikrama-Core] > ',
                    cmd: '/interface print brief',
                    outputs: [
                        '<span style="color:#6E849E">#  NAME              TYPE   MTU   STATUS</span>',
                        '<span style="color:#6DB3F8">0  sfp-sfpplus1      10G    1500  RUNNING • Telkom Metro-E</span>',
                        '<span style="color:#9CD0FF">1  vlan10-siswa      vlan   1500  RUNNING</span>',
                        '<span style="color:#9CD0FF">2  vlan30-tefa-isp   vlan   1500  RUNNING</span>'
                    ]
                },
                {
                    badge: 'RouterOS v7.14',
                    prompt: '[admin@Wikrama-Core] > ',
                    cmd: '/ip firewall filter add chain=forward action=drop connection-state=invalid',
                    outputs: [
                        '<span style="color:#6DB3F8">[OK] Rule #14 applied • Anti-DDoS &amp; SYN Flood Filter ACTIVE</span>'
                    ]
                },
                {
                    badge: 'Ubuntu 24.04 NOC',
                    prompt: 'admin@datacenter:~$ ',
                    cmd: 'systemctl status wireguard-mesh@wg0',
                    outputs: [
                        '<span style="color:#9CD0FF">Active: active (running) • 14 encrypted peer nodes online</span>',
                        '<span style="color:#6DB3F8">Latency: 1.84ms • Proxmox VE 8.2 Cluster Healthy</span>'
                    ]
                }
            ];

            let timerId = null;
            let currentStep = 0;
            let charIndex = 0;

            const startSimulation = () => {
                if (timerId) clearTimeout(timerId);
                termLogs.innerHTML = '';
                termCmd.textContent = '';
                currentStep = 0;
                charIndex = 0;
                if (inputRow) inputRow.style.display = 'flex';

                const runStep = () => {
                    if (currentStep >= terminalSequence.length) {
                        if (inputRow) inputRow.style.display = 'none';
                        const done = document.createElement('div');
                        done.className = 'pt-2 mt-2';
                        done.style.borderTop = '1px solid rgba(78, 154, 230, 0.2)';
                        done.style.color = '#6DB3F8';
                        done.textContent = '[NOC READY] Seluruh node routing, firewall, dan tunnel aktif.';
                        termLogs.appendChild(done);
                        termScreen.scrollTop = termScreen.scrollHeight;
                        return;
                    }

                    const step = terminalSequence[currentStep];
                    if (termBadge) termBadge.textContent = step.badge;
                    termPrompt.textContent = step.prompt;

                    if (charIndex < step.cmd.length) {
                        termCmd.textContent += step.cmd.charAt(charIndex);
                        charIndex++;
                        termScreen.scrollTop = termScreen.scrollHeight;
                        timerId = setTimeout(runStep, 16);
                    } else {
                        const cmdRow = document.createElement('div');
                        cmdRow.className = 'flex items-baseline flex-wrap pt-1 font-semibold';
                        cmdRow.innerHTML = `<span style="color:#6DB3F8;margin-right:6px;">${step.prompt}</span><span class="text-white">${step.cmd}</span>`;
                        termLogs.appendChild(cmdRow);

                        termCmd.textContent = '';
                        charIndex = 0;

                        let outIdx = 0;
                        const streamOut = () => {
                            if (outIdx < step.outputs.length) {
                                const outDiv = document.createElement('div');
                                outDiv.className = 'pl-2';
                                outDiv.innerHTML = step.outputs[outIdx];
                                termLogs.appendChild(outDiv);
                                termScreen.scrollTop = termScreen.scrollHeight;
                                outIdx++;
                                timerId = setTimeout(streamOut, 70);
                            } else {
                                currentStep++;
                                timerId = setTimeout(runStep, 420);
                            }
                        };
                        timerId = setTimeout(streamOut, 100);
                    }
                };

                timerId = setTimeout(runStep, 260);
            };

            if (replayBtn) replayBtn.addEventListener('click', startSimulation);
            startSimulation();
        });
    </script>
</x-layouts.blank>
