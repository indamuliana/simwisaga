{{--
    ================================================================================
    HALAMAN DETAIL JURUSAN: TEKNIK JARINGAN KOMPUTER DAN TELEKOMUNIKASI (TJKT)
    Gaya Desain: Cyber Matrix Emerald (#53D769), NOC Console & High-Contrast Light
    File ini dapat diedit secara manual via teks editor (VS Code, Notepad, dll).
    URL Akses: 127.0.0.1:8000/jurusan/tjkt
    ================================================================================
--}}
<x-layouts.blank>
    @php
        $sekolah = $sekolah ?? \App\Modules\Sistem\Models\School::current();
        $schoolName = $sekolah->nama ?? 'SMK Wikrama 1 Garut';
    @endphp

    <!-- ── 1. HERO SECTION: TJKT CYBER INFRASTRUCTURE & NETWORK OPERATIONS ── -->
    <header class="relative pt-8 pb-16 sm:pt-12 sm:pb-20 overflow-hidden bg-gradient-to-b from-[#022c22] via-[#064e3b] to-[#041f1a] text-white" style="background: linear-gradient(180deg, #022c22 0%, #064e3b 50%, #041f1a 100%);">
        <!-- Interactive Constellation Network Canvas & Cyber Matrix Glow -->
        <canvas id="tjkt-network-canvas" class="absolute inset-0 w-full h-full pointer-events-auto z-0 opacity-70"></canvas>
        <div class="absolute inset-0 pointer-events-none z-0">
            <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-[#10b981]/25 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-emerald-500/15 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(rgba(16,185,129,0.18)_1px,transparent_1px)] [background-size:24px_24px] opacity-40"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs font-semibold text-emerald-300/80 mb-6">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
                <span>&rsaquo;</span>
                <a href="{{ url('/jurusan') }}" class="hover:text-white transition-colors">Jurusan</a>
                <span>&rsaquo;</span>
                <span class="text-[#53D769]">TJKT (Teknik Komputer &amp; Jaringan)</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <!-- Text Intro -->
                <div class="lg:col-span-7 space-y-5">
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-500/20 text-[#A2E8B9] border border-emerald-500/35 backdrop-blur-md">
                        <span class="w-2 h-2 rounded-full bg-[#53D769] animate-pulse"></span>
                        <span>Infrastruktur Siber, Fiber Optik &bull; Terakreditasi A</span>
                    </span>

                    <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight">
                        Teknik Jaringan Komputer <br class="hidden sm:inline">
                        <span class="bg-gradient-to-r from-[#53D769] via-emerald-300 to-[#A2E8B9] bg-clip-text text-transparent">
                            &amp; Telekomunikasi (TJKT)
                        </span>
                    </h1>

                    <p class="text-sm sm:text-base text-emerald-100/90 leading-relaxed font-normal">
                        Mencetak arsitek infrastruktur jaringan siber, spesialis fiber optik, dan administrator cloud computing handal. Santri menguasai perancangan jaringan skala enterprise, konfigurasi router MikroTik &amp; Huawei, pertahanan keamanan siber (Cybersecurity), hingga server Linux.
                    </p>

                    <!-- Vendor Academy Badges -->
                    <div class="pt-2 flex flex-wrap gap-2 text-xs font-mono-code">
                        <span class="px-3 py-1 rounded-lg bg-emerald-950/80 text-[#53D769] border border-emerald-800">MikroTik MTCNA</span>
                        <span class="px-3 py-1 rounded-lg bg-emerald-950/80 text-emerald-300 border border-emerald-800">Huawei ICT Academy</span>
                        <span class="px-3 py-1 rounded-lg bg-emerald-950/80 text-cyan-300 border border-emerald-800">Linux Server Admin</span>
                        <span class="px-3 py-1 rounded-lg bg-emerald-950/80 text-yellow-300 border border-emerald-800">Fiber Optic Fusion Splicer</span>
                        <span class="px-3 py-1 rounded-lg bg-emerald-950/80 text-purple-300 border border-emerald-800">Cloud &amp; Virtualization</span>
                        <span class="px-3 py-1 rounded-lg bg-emerald-950/80 text-rose-300 border border-emerald-800">Cyber Defense</span>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="pt-4 flex flex-wrap items-center gap-3">
                        <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="px-6 py-3 rounded-full text-xs sm:text-sm font-bold text-slate-950 bg-gradient-to-r from-[#53D769] to-emerald-400 hover:from-emerald-400 hover:to-emerald-300 shadow-lg shadow-emerald-500/25 transition-all hover:scale-105">
                            Daftar TJKT Sekarang &rarr;
                        </a>
                        <a href="#kurikulum" class="px-5 py-3 rounded-full text-xs sm:text-sm font-bold text-emerald-100 bg-white/10 hover:bg-white/15 border border-white/20 transition-all">
                            Lihat Kurikulum &amp; Lab
                        </a>
                    </div>
                </div>

                <!-- ── Live Simulated Linux & MikroTik RouterOS Interactive Terminal ── -->
                <div class="lg:col-span-5">
                    <div class="rounded-3xl bg-slate-950/95 border border-emerald-500/35 shadow-2xl overflow-hidden text-white backdrop-blur-xl flex flex-col relative" style="height: 440px; min-height: 440px; max-height: 440px;">
                        <!-- Subtle terminal scanline overlay -->
                        <div class="absolute inset-0 bg-[linear-gradient(rgba(16,185,129,0.02)_1px,transparent_1px)] [background-size:100%_3px] pointer-events-none z-10"></div>

                        <!-- Terminal Window Titlebar -->
                        <div class="px-4 py-3 bg-[#021814] border-b border-emerald-900/60 flex items-center justify-between flex-shrink-0 z-20">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-red-500/80 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-yellow-500/80 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500/80 inline-block"></span>
                                <span class="text-[11px] font-mono text-emerald-300 font-semibold pl-2">admin@wikrama-noc:~#</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span id="tjkt-term-badge" class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-950 text-[#53D769] border border-emerald-800">
                                    RouterOS v7.14
                                </span>
                                <span class="flex items-center gap-1.5 text-[10px] font-mono text-emerald-400">
                                    <span class="w-2 h-2 rounded-full bg-[#53D769] animate-ping"></span>
                                    <span>LIVE CLI</span>
                                </span>
                            </div>
                        </div>

                        <!-- Terminal Screen Output Area (Fixed Height, Hidden Scrollbar, Auto-Scroll Only) -->
                        <div id="tjkt-terminal-screen" class="flex-1 p-4 sm:p-5 font-mono text-[11px] leading-relaxed overflow-y-auto space-y-2 select-text relative z-20 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden" style="scrollbar-width: none; -ms-overflow-style: none;">
                            <style>
                                #tjkt-terminal-screen::-webkit-scrollbar { display: none !important; width: 0 !important; height: 0 !important; }
                            </style>
                            <!-- Injected terminal history logs go here -->
                            <div id="tjkt-terminal-logs" class="space-y-1.5"></div>

                            <!-- Currently typing command line -->
                            <div id="tjkt-terminal-input-row" class="flex items-center flex-wrap font-bold pt-1">
                                <span id="tjkt-terminal-prompt" class="text-cyan-400 mr-1.5">[admin@Wikrama-Core] &gt; </span>
                                <span id="tjkt-terminal-cmd" class="text-white"></span>
                                <span class="w-2 h-3.5 bg-[#53D769] inline-block animate-pulse ml-0.5"></span>
                            </div>
                        </div>

                        <!-- Terminal Footer Telemetry -->
                        <div class="px-4 py-2 bg-[#021814]/90 border-t border-emerald-900/60 flex items-center justify-between text-[10px] font-mono text-slate-400 flex-shrink-0 z-20">
                            <div class="flex items-center gap-3">
                                <span>Session: <strong class="text-emerald-300">SSH-256</strong></span>
                                <span>Encoding: <strong class="text-slate-300">UTF-8</strong></span>
                            </div>
                            <span class="text-emerald-400 font-semibold">&#10003; 99.98% NOC Uptime</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ── 2. STATS & KEY PILLARS STRIP ── -->
    <section class="py-12 bg-emerald-50/80 border-y border-emerald-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="p-5 rounded-2xl bg-white border border-emerald-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-emerald-800">100%</span>
                    <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mt-1 block">Sertifikasi MikroTik &amp; BNSP</span>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-emerald-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-emerald-800">4 Lab</span>
                    <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mt-1 block">NOC, Server, Fiber &amp; Cyber</span>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-emerald-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-emerald-800">2 Unit</span>
                    <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mt-1 block">TEFA ISP &amp; RT/RW Net Hub</span>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-emerald-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-emerald-800">99%</span>
                    <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mt-1 block">Keterserapan BMW</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 3. PENJELASAN & PROFIL JURUSAN (CYBER MATRIX BINARY BACKDROP) ── -->
    <section class="py-24 bg-[#020e0b] text-white relative overflow-hidden" style="background-color: #020e0b;">
        <!-- Uploaded Cyber Binary / Network Perspective Background Image -->
        <div class="absolute inset-0 pointer-events-none select-none z-0 overflow-hidden">
            <img src="{{ asset('images/landing/bg-tjkt-binary.jpg') }}" alt="Cyber Data Infrastructure" class="w-full h-full object-cover object-center filter brightness-50 contrast-125 scale-105">
            <!-- Multi-layer Darkening Overlays for 100% Text Legibility -->
            <div class="absolute inset-0 bg-[#020e0b]/75"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-[#020e0b] via-[#020e0b]/90 to-[#020e0b]/50"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,transparent_20%,#020e0b_95%)]"></div>
            <!-- Subtle CRT scanline effect for authentic tech vibe -->
            <div class="absolute inset-0 bg-[linear-gradient(rgba(0,255,170,0.02)_1px,transparent_1px)] [background-size:100%_4px]"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <!-- Left: Protected High-Contrast Glassmorphic Card -->
                <div class="lg:col-span-6 p-6 sm:p-8 rounded-3xl bg-slate-950/80 backdrop-blur-xl border border-emerald-500/25 shadow-2xl space-y-6">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold text-[#A2E8B9] bg-emerald-950/90 border border-emerald-700/60 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-[#53D769] animate-pulse"></span>
                        <span>Tentang Kompetensi Keahlian</span>
                    </div>

                    <h2 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                        Mengapa Memilih Jurusan <br>
                        <span class="bg-gradient-to-r from-[#53D769] via-emerald-300 to-[#A2E8B9] bg-clip-text text-transparent">TJKT Wikrama Garut?</span>
                    </h2>

                    <div class="space-y-3 text-slate-200">
                        <p class="text-emerald-100/95 leading-relaxed text-sm sm:text-base">
                            Program keahlian Teknik Jaringan Komputer dan Telekomunikasi (TJKT) di {{ $schoolName }} dirancang untuk menghasilkan <strong class="text-white font-bold">arsitek infrastruktur jaringan dan pertahanan siber</strong> yang tangguh, adaptif, serta berintegritas tinggi.
                        </p>
                        <p class="text-emerald-100/85 leading-relaxed text-sm sm:text-base">
                            Siswa tidak hanya belajar merakit komputer atau crimping kabel UTP biasa, melainkan mengoperasikan langsung <em class="text-[#53D769] font-medium not-italic">perangkat routing enterprise MikroTik, switch serat optik, mesin fusion splicer fiber optic, server virtualization Proxmox</em>, dan konfigurasi firewall filtering.
                        </p>
                    </div>

                    <div class="space-y-3 pt-2">
                        <div class="flex items-start gap-3.5 p-4 rounded-2xl bg-emerald-950/40 border border-emerald-800/60 hover:border-emerald-500/50 transition-colors">
                            <span class="p-2 rounded-xl bg-emerald-500/20 text-[#53D769] border border-emerald-500/30 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="text-sm font-bold text-white">MikroTik Academy &amp; Huawei ICT Official Center</h4>
                                <p class="text-xs text-emerald-200/80 leading-relaxed mt-0.5">Siswa diajar langsung oleh instruktur bersertifikasi internasional dan berhak mengikuti ujian sertifikasi MTCNA resmi di sekolah.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3.5 p-4 rounded-2xl bg-emerald-950/40 border border-emerald-800/60 hover:border-emerald-500/50 transition-colors">
                            <span class="p-2 rounded-xl bg-emerald-500/20 text-[#53D769] border border-emerald-500/30 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="text-sm font-bold text-white">Praktik Fiber Optic Fusion Splicing Nyata</h4>
                                <p class="text-xs text-emerald-200/80 leading-relaxed mt-0.5">Menguasai pemotongan fiber, penyambungan dengan splicer presisi, pengukuran redaman OTDR, dan penarikan kabel udara.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3.5 p-4 rounded-2xl bg-emerald-950/40 border border-emerald-800/60 hover:border-emerald-500/50 transition-colors">
                            <span class="p-2 rounded-xl bg-emerald-500/20 text-[#53D769] border border-emerald-500/30 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="text-sm font-bold text-white">Unit TEFA Pengelola ISP &amp; RT/RW Net</h4>
                                <p class="text-xs text-emerald-200/80 leading-relaxed mt-0.5">Terlibat langsung dalam pemeliharaan backbone internet sekolah dan layanan jaringan fiber untuk klien sekitar.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Visual Collage Showcase -->
                <div class="lg:col-span-6 grid grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div class="rounded-2xl overflow-hidden shadow-xl border border-emerald-500/30 bg-slate-900/60">
                            <img src="{{ asset('images/landing/lab-tjkt.jpeg') }}" alt="Lab Jaringan TJKT Wikrama" class="w-full h-48 sm:h-56 object-cover hover:scale-105 transition-all duration-300">
                        </div>
                        <div class="p-5 rounded-2xl bg-slate-950/90 backdrop-blur-md border border-emerald-500/30 text-white space-y-2 shadow-lg">
                            <span class="text-[#A2E8B9] text-xs font-bold uppercase tracking-wider">Unit Produksi TEFA</span>
                            <h4 class="text-base font-extrabold">Wikrama Network Center</h4>
                            <p class="text-xs text-emerald-200/90 leading-relaxed">Unit operasional yang memelihara jaringan hotspot, server data, dan CCTV di seluruh kampus.</p>
                        </div>
                    </div>
                    <div class="space-y-4 pt-6">
                        <div class="p-5 rounded-2xl bg-emerald-950/90 backdrop-blur-md border border-[#53D769]/40 text-white space-y-2 shadow-xl shadow-emerald-950/40">
                            <span class="text-[#53D769] text-xs font-bold uppercase tracking-wider">Spesialisasi Unggulan</span>
                            <h4 class="text-base font-extrabold">Fiber Optic &amp; Server Lab</h4>
                            <p class="text-xs text-emerald-100/90 leading-relaxed">Fasilitas splicing mandiri dan konfigurasi cloud server berkecepatan tinggi 10 Gbps.</p>
                        </div>
                        <div class="rounded-2xl overflow-hidden shadow-xl border border-emerald-500/30 bg-slate-900/60">
                            <img src="{{ asset('images/web/hero-tjkt.jpg') }}" alt="Suasana Praktik Server Siswa TJKT" class="w-full h-48 sm:h-56 object-cover hover:scale-105 transition-all duration-300">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 4. PROSPEK KARIR & PELUANG PEKERJAAN ── -->
    <section class="py-20 bg-slate-900 text-white relative overflow-hidden" id="karir">
        <div class="absolute -top-32 -left-32 w-80 h-80 rounded-full bg-[#10b981]/15 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 rounded-full bg-emerald-500/15 blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <!-- Cyber Telemetry Status Bar -->
                <div class="mb-4 inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-950/90 border border-emerald-500/30 text-xs font-mono text-emerald-300 shadow-inner">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>[NOC-TELEMETRY] Scanning industry demand... <span class="text-white font-bold">100% Match with Telecom &amp; ISP</span></span>
                </div>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white mt-1">
                    Prospek Karir Lulusan <span class="bg-gradient-to-r from-[#53D769] to-emerald-400 bg-clip-text text-transparent">TJKT</span>
                </h2>
                <p class="text-slate-300 text-sm sm:text-base mt-3">
                    Setiap perusahaan, perbankan, instansi pemerintah, dan rumah sakit memerlukan infrastruktur jaringan dan pertahanan siber yang handal.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- 1. Network Engineer / NOC Specialist -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-emerald-500/20 hover:border-emerald-500/60 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-[#53D769] flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        🌐
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Network Engineer &amp; NOC Operator</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Mengoperasikan router backbone, bandwidth management, konfigurasi dynamic routing BGP/OSPF, dan memonitor traffic di Network Operations Center.
                    </p>
                    <div class="text-xs font-semibold text-emerald-300">
                        <span>Range Gaji: Rp 6.000.000 - Rp 13.500.000</span>
                    </div>
                </div>

                <!-- 2. Cybersecurity & SOC Analyst -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-teal-500/20 hover:border-teal-500/60 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-teal-500/20 text-teal-300 flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        🛡️
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Cybersecurity &amp; SOC Analyst</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Melindungi perimeter jaringan dari serangan malware, brute-force, dan kebocoran data dengan firewall filtering, IDS/IPS, dan audit penetrasi.
                    </p>
                    <div class="text-xs font-semibold text-teal-300">
                        <span>Range Gaji: Rp 7.000.000 - Rp 15.000.000</span>
                    </div>
                </div>

                <!-- 3. Fiber Optic Specialist -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-cyan-500/20 hover:border-cyan-500/60 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/20 text-cyan-300 flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        ⚡
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Fiber Optic Project Engineer</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Spesialis penarikan jaringan fiber FTTH, fusion splicing inti kaca, pengujian rugi-daya optical power meter, dan sertifikasi redaman kabel optik.
                    </p>
                    <div class="text-xs font-semibold text-cyan-300">
                        <span>Range Gaji: Rp 5.500.000 - Rp 11.000.000</span>
                    </div>
                </div>

                <!-- 4. Linux Cloud System Administrator -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-blue-500/20 hover:border-blue-500/60 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/20 text-blue-300 flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        🐧
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Linux Cloud &amp; Server Administrator</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Mengelola cluster server berbasis Ubuntu/Debian/Rocky Linux, web server Nginx, container Docker, automasi bash script, dan backup data terjadwal.
                    </p>
                    <div class="text-xs font-semibold text-blue-300">
                        <span>Range Gaji: Rp 6.000.000 - Rp 13.000.000</span>
                    </div>
                </div>

                <!-- 5. Wireless & IoT Infrastructure Specialist -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-emerald-500/20 hover:border-emerald-500/60 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        📡
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Wireless Network &amp; IoT Specialist</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Perancangan radio wireless point-to-point jarak jauh, hotspot gateway captive portal, antenna alignment, dan integrasi sensor IoT cerdas.
                    </p>
                    <div class="text-xs font-semibold text-emerald-300">
                        <span>Range Gaji: Rp 5.500.000 - Rp 11.500.000</span>
                    </div>
                </div>

                <!-- 6. ISP & IT Infrastructure Founder -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-yellow-500/20 hover:border-yellow-500/60 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-yellow-500/20 text-yellow-300 flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        💼
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">ISP Owner &amp; IT Consultant</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Mendirikan jasa penyedia layanan internet mandiri (ISP/RT-RW Net), kontraktor instalasi CCTV pabrik/perumahan, dan konsultan IT swasta.
                    </p>
                    <div class="text-xs font-semibold text-yellow-300">
                        <span>Penghasilan: Mandiri &amp; Tak Terbatas</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 5. KURIKULUM 3 TAHUN (FASE E & FASE F) ── -->
    <section id="kurikulum" class="py-20 bg-emerald-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-emerald-800 bg-emerald-100 border border-emerald-200">
                    <span>Roadmap Pembelajaran</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 mt-3">
                    Kurikulum 3 Tahun Berstandar <span class="text-[#059669]">MikroTik &amp; Huawei</span>
                </h2>
                <p class="text-slate-600 text-sm sm:text-base mt-3">
                    Disusun bersama asosiasi penyelenggara internet (APJII) dan mitra telko terkemuka, menggabungkan teori fundamental dan praktik lab riil.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- KELAS X -->
                <div class="bg-white rounded-3xl p-7 border-2 border-emerald-200 shadow-sm relative hover:border-[#059669] transition-all">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-[#059669] font-bold flex items-center justify-center text-sm mb-4">
                        X
                    </div>
                    <span class="text-xs font-bold uppercase text-[#059669] tracking-wider">Fase E &bull; Tahun Pertama</span>
                    <h3 class="text-xl font-bold text-slate-900 mt-1 mb-4">Dasar Jaringan &amp; Sistem Komputer</h3>
                    <p class="text-xs text-slate-600 mb-6">Membangun pemahaman arsitektur komputer, subnetting IPv4/IPv6, dan topologi jaringan lokal.</p>

                    <ul class="space-y-3 text-xs text-slate-700">
                        <li class="flex items-start gap-2">
                            <span class="text-[#059669] font-bold">&check;</span>
                            <span>Konsep OSI 7-Layer &amp; Protokol TCP/IP</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#059669] font-bold">&check;</span>
                            <span>Subnetting VLSM &amp; CIDR (IPv4 &amp; IPv6)</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#059669] font-bold">&check;</span>
                            <span>Teknik Perkabelan UTP/STP &amp; Crimping Tester</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#059669] font-bold">&check;</span>
                            <span>Instalasi &amp; Dasar Perintah Sistem Operasi Linux</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#059669] font-bold">&check;</span>
                            <span>Keselamatan Kerja Teknisi &amp; K3LH Lapangan</span>
                        </li>
                    </ul>
                </div>

                <!-- KELAS XI -->
                <div class="bg-white rounded-3xl p-7 border-2 border-[#059669] shadow-lg relative ring-2 ring-[#059669]/20">
                    <div class="absolute -top-3.5 right-6 px-3 py-1 rounded-full text-[11px] font-extrabold bg-[#059669] text-white uppercase tracking-wider shadow">
                        Spesialisasi Vendor
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-[#059669] to-emerald-700 text-white font-bold flex items-center justify-center text-sm mb-4">
                        XI
                    </div>
                    <span class="text-xs font-bold uppercase text-[#059669] tracking-wider">Fase F &bull; Tahun Kedua</span>
                    <h3 class="text-xl font-bold text-slate-900 mt-1 mb-4">Routing, Fiber Optic &amp; Server</h3>
                    <p class="text-xs text-slate-600 mb-6">Pendalaman konfigurasi routerboard MikroTik, penyambungan serat optik, dan server enterprise.</p>

                    <ul class="space-y-3 text-xs text-slate-700">
                        <li class="flex items-start gap-2">
                            <span class="text-[#059669] font-bold">&check;</span>
                            <span>Routing Statis &amp; Dinamis (OSPF/BGP) MikroTik</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#059669] font-bold">&check;</span>
                            <span>Fiber Optic Fusion Splicing &amp; OTDR Certification</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#059669] font-bold">&check;</span>
                            <span>VLAN Segmentation &amp; Enterprise Switch Management</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#059669] font-bold">&check;</span>
                            <span>Enterprise Linux Server (DNS, Web, Mail, Proxy)</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#059669] font-bold">&check;</span>
                            <span>Firewall Filtering, Mangle, &amp; Bandwidth QoS</span>
                        </li>
                    </ul>
                </div>

                <!-- KELAS XII -->
                <div class="bg-white rounded-3xl p-7 border-2 border-emerald-200 shadow-sm relative hover:border-[#059669] transition-all">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-sm mb-4">
                        XII
                    </div>
                    <span class="text-xs font-bold uppercase text-emerald-800 tracking-wider">Fase F &bull; Tahun Ketiga</span>
                    <h3 class="text-xl font-bold text-slate-900 mt-1 mb-4">Magang 6 Bulan &amp; Sertifikasi MTCNA</h3>
                    <p class="text-xs text-slate-600 mb-6">Penerjunan langsung di ISP / BUMN Telko, ujian sertifikasi internasional MikroTik, dan penyaluran BMW.</p>

                    <ul class="space-y-3 text-xs text-slate-700">
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-800 font-bold">&check;</span>
                            <span>PKL Industri 6 Bulan di ISP, Telkom, &amp; Vendor Data</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-800 font-bold">&check;</span>
                            <span>Uji Sertifikasi MikroTik Certified Associate (MTCNA)</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-800 font-bold">&check;</span>
                            <span>Sertifikasi BNSP Teknisi Madya Jaringan Komputer</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-800 font-bold">&check;</span>
                            <span>Cybersecurity Defense &amp; Penetration Testing Dasar</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-800 font-bold">&check;</span>
                            <span>Penyaluran BMW (Bekerja, Melanjutkan, Wirausaha)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 6. KOLASE RUANGAN PRAKTIK & LAB KOMPUTER ── -->
    <section class="py-20 bg-white" id="lab">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-emerald-800 bg-emerald-100 border border-emerald-200">
                    <span>Fasilitas Standar Industri Telekomunikasi</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 mt-3">
                    Kolase Laboratorium &amp; <span class="text-[#059669]">Studio Jaringan Nyata</span>
                </h2>
                <p class="text-slate-600 text-sm sm:text-base mt-3">
                    Laboratorium dirancang mirip Network Operations Center (NOC) nyata dengan rak server, switch manageable, dan perangkat routerboard aktif.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Fasilitas 1 -->
                <div class="rounded-2xl overflow-hidden border border-emerald-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/landing/lab-tjkt.jpeg') }}" alt="Lab Jaringan Komputer TJKT" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-[#059669] text-white">Lab Router &amp; Switch</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Laboratorium Routerboard MikroTik</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Dilengkapi 40+ unit RouterBoard RB750/RB951, switch Cisco manageable, kabel patch cord CAT6, dan modular testing bench per meja siswa.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 2 -->
                <div class="rounded-2xl overflow-hidden border border-emerald-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/web/facility-lab.jpg') }}" alt="Studio Fiber Optic Splicing" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-teal-700 text-white">Fiber Optic Studio</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Studio Fusion Splicing &amp; OTDR</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Mesin fusion splicer inti kaca presisi, fiber cleaver, Optical Time Domain Reflectometer (OTDR), dan visual fault locator standar Telkom.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 3 -->
                <div class="rounded-2xl overflow-hidden border border-emerald-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/web/activity-students.jpg') }}" alt="Server Cloud Data Center" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-slate-900 text-white">Cloud Server Center</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Enterprise Server &amp; Cloud Staging</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Rak tertutup rackmount server HP ProLiant dan Dell PowerEdge untuk simulasi virtualisasi Proxmox VE, DNS cluster, dan server cloud.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 4 -->
                <div class="rounded-2xl overflow-hidden border border-emerald-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/landing/suasana-belajar-aktif.jpg') }}" alt="NOC Backbone Room" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-cyan-800 text-white">NOC Command Center</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Network Operations Center (NOC)</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Layar monitoring traffic multi-monitor dengan Dude NMS, Zabbix, dan Grafana untuk memantau stabilitas internet 24 jam nonstop.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 5 -->
                <div class="rounded-2xl overflow-hidden border border-emerald-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/web/activity-learning.jpg') }}" alt="Cybersecurity Lab" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-800 text-white">Cyber Defense Sandbox</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Cybersecurity &amp; Sandbox Lab</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Lingkungan jaringan terisolasi untuk simulasi packet sniffing Wireshark, uji firewall rules, penanganan insiden siber, dan ethical hacking.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 6 -->
                <div class="rounded-2xl overflow-hidden border border-emerald-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/landing/jurusan-tjkt.jpg') }}" alt="Bengkel Hardware & Wireless" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-[#059669] text-white">Hardware &amp; Wireless Tower</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Wireless Tower &amp; Hardware Lab</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Tower triangle simulasi antena point-to-point, perakitan PC teknisi, troubleshooting power supply, dan pengujian access point Wi-Fi 6.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 7. SERTIFIKASI KOMPETENSI RESMI (BNSP & MIKROTIK) ── -->
    <section class="py-20 bg-gradient-to-br from-[#022c22] via-[#064e3b] to-[#041f1a] text-white" style="background: linear-gradient(135deg, #022c22 0%, #064e3b 50%, #041f1a 100%);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-emerald-300 bg-emerald-950/80 border border-emerald-800">
                    <span>Pengakuan Industri Internasional</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white mt-3">
                    Sertifikasi Kompetensi Resmi <span class="bg-gradient-to-r from-[#53D769] to-emerald-400 bg-clip-text text-transparent">TJKT</span>
                </h2>
                <p class="text-emerald-100/90 text-sm sm:text-base mt-3">
                    Lulusan dibekali sertifikasi vendor global yang diakui di seluruh dunia serta lisensi sertifikasi berlogo Garuda BNSP.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Sertifikasi 1 -->
                <div class="p-6 rounded-2xl bg-white/5 border border-emerald-500/20 backdrop-blur-md space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-[#53D769] flex items-center justify-center font-bold text-xl">
                        🎖️
                    </div>
                    <h3 class="text-lg font-bold text-white">MikroTik Certified Network Associate</h3>
                    <p class="text-xs text-emerald-100/80 leading-relaxed">
                        Sertifikasi internasional resmi dari MikroTik Latvia (MTCNA) yang membuktikan keahlian routing, bridging, wireless, firewall, QoS, dan VPN tunneling.
                    </p>
                    <div class="pt-2 text-xs font-semibold text-emerald-300">
                        Standar: MikroTik Certified Academy
                    </div>
                </div>

                <!-- Sertifikasi 2 -->
                <div class="p-6 rounded-2xl bg-white/5 border border-teal-500/20 backdrop-blur-md space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-500/20 text-teal-300 flex items-center justify-center font-bold text-xl">
                        📡
                    </div>
                    <h3 class="text-lg font-bold text-white">BNSP Teknisi Madya Jaringan Komputer</h3>
                    <p class="text-xs text-emerald-100/80 leading-relaxed">
                        Sertifikat kompetensi negara dari BNSP berlogo Garuda untuk kualifikasi pemasangan perangkat jaringan, administrasi server, dan pemeliharaan kabel fiber.
                    </p>
                    <div class="pt-2 text-xs font-semibold text-teal-300">
                        Diterbitkan oleh: BNSP LSP P-1
                    </div>
                </div>

                <!-- Sertifikasi 3 -->
                <div class="p-6 rounded-2xl bg-white/5 border border-cyan-500/20 backdrop-blur-md space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/20 text-cyan-300 flex items-center justify-center font-bold text-xl">
                        🏆
                    </div>
                    <h3 class="text-lg font-bold text-white">Huawei ICT Academy Certification</h3>
                    <p class="text-xs text-emerald-100/80 leading-relaxed">
                        Pelatihan dan sertifikasi resmi Datacom dari Huawei Technologies mencakup protokol switching jaringan enterprise dan arsitektur data center.
                    </p>
                    <div class="pt-2 text-xs font-semibold text-cyan-300">
                        Mitra: Huawei ICT Academy Partner
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 8. MITRA DUDI & TEMPAT MAGANG ── -->
    <section class="py-16 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-bold text-[#059669] uppercase tracking-wider block">Jaringan Industri Telekomunikasi</span>
                <h3 class="text-xl sm:text-2xl font-bold text-slate-900 mt-1">Mitra PKL &amp; Rekrutmen Lulusan</h3>
                <p class="text-xs text-slate-600 mt-2">Siswa menjalani magang nyata selama 6 bulan di perusahaan ISP, BUMN Telkom, data center, dan vendor IT terkemuka.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4 items-center">
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">PT Telkom Indonesia</span>
                    <span class="text-[10px] text-slate-500">BUMN Telko Terbesar</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">MikroTik Latvia</span>
                    <span class="text-[10px] text-slate-500">Academy Partner</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">Huawei ICT</span>
                    <span class="text-[10px] text-slate-500">Global Tech Vendor</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">CBN Internet</span>
                    <span class="text-[10px] text-slate-500">Fiber ISP Enterprise</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">MyRepublic / Biznet</span>
                    <span class="text-[10px] text-slate-500">Broadband Provider</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">APJII Jabar</span>
                    <span class="text-[10px] text-slate-500">Asosiasi ISP Nasional</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 9. TESTIMONI ALUMNI TJKT ── -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto bg-gradient-to-r from-[#022c22] to-[#041f1a] rounded-3xl p-8 sm:p-12 text-white border border-emerald-500/30 shadow-xl relative overflow-hidden" style="background: linear-gradient(90deg, #022c22 0%, #041f1a 100%);">
                <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-[#10b981]/20 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-8 relative z-10">
                    <img src="{{ asset('images/web/alumni-ichsan.png') }}" alt="Alumni TJKT Wikrama" class="w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover border-4 border-[#53D769] shadow-lg shrink-0">
                    <div class="space-y-3 text-center sm:text-left">
                        <div class="flex items-center justify-center sm:justify-start gap-1 text-emerald-400">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-sm sm:text-base text-emerald-100 italic leading-relaxed">
                            "Di TJKT Wikrama, kita tidak hanya diajar cara merakit PC, tapi langsung pegang router MikroTik RB Series dan mesin splicing fiber optik sungguhan. Sertifikat MTCNA yang saya peroleh di sekolah menjadi kunci utama saya langsung diterima sebagai Network Engineer di ISP nasional!"
                        </p>
                        <div>
                            <h4 class="font-bold text-white text-base">Ichsan Nugraha</h4>
                            <span class="text-xs text-[#A2E8B9]">Alumni TJKT &bull; Network Operations Specialist di Internet Provider</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 10. FAQ SEPUTAR JURUSAN TJKT ── -->
    <section class="py-20 bg-emerald-50/50" id="faq">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-xs font-bold text-[#059669] uppercase tracking-wider block">Pertanyaan Umum</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">FAQ Jurusan TJKT</h2>
            </div>

            <div class="space-y-4">
                <div class="p-5 rounded-2xl bg-white border border-emerald-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Apakah belajar jaringan komputer rumit dan banyak hafalan IP?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Sama sekali tidak rumit jika dipelajari dengan simulasi lab visual. Di Wikrama, 70% pembelajaran adalah praktik langsung konfigurasi router, sehingga logika pengalamatan IP dan routing dipahami secara intuitif melalui studi kasus nyata.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-emerald-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Apakah siswa diajari cara menyambung kabel fiber optik sungguhan?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Ya, pasti! Sekolah memiliki fasilitas mesin fusion splicer, fiber cleaver, dan alat ukur optik lengkap. Setiap siswa dilatih hingga mahir menyambung serat kaca dan mengukur redaman dB standar industri.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-emerald-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Apakah sertifikat MikroTik MTCNA diakui secara global?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Betul sekali. Sertifikat MTCNA diterbitkan langsung dengan nomor lisensi resmi MikroTik Latvia dan tercatat di database global www.mikrotik.com, berlaku internasional dan diakui industri IT di seluruh dunia.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-emerald-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Apakah lulusan TJKT bisa melanjutkan kuliah ke Perguruan Tinggi?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Tentu bisa! Banyak lulusan TJKT melanjutkan kuliah di jurusan Teknik Komputer, Teknik Telekomunikasi, Rekayasa Keamanan Siber, dan Sistem Komputer di kampus ternama seperti Telkom University, ITB, PNJ, dan Polban.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 11. CTA DAFTAR TJKT ── -->
    <section class="py-20 bg-gradient-to-r from-[#022c22] via-[#064e3b] to-[#10b981] text-white relative overflow-hidden" style="background: linear-gradient(90deg, #022c22 0%, #064e3b 50%, #10b981 100%);">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-6">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-white/20 text-[#A2E8B9] border border-white/30">
                Penerimaan Peserta Didik Baru (SPMB)
            </span>
            <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight">
                Siap Menjadi Arsitek Jaringan Siber &amp; <br class="hidden sm:inline">
                <span class="bg-gradient-to-r from-[#53D769] via-emerald-200 to-white bg-clip-text text-transparent">Infrastruktur Cloud Masa Depan?</span>
            </h2>
            <p class="text-emerald-100/90 text-sm sm:text-base max-w-2xl mx-auto">
                Kuota kelas TJKT sangat terbatas demi menjaga intensitas bimbingan perangkat lab router dan keselamatan praktik fiber optik. Amankan kursi belajarmu sekarang!
            </p>
            <div class="pt-4 flex flex-wrap items-center justify-center gap-4">
                <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="px-8 py-3.5 rounded-full text-sm font-bold text-slate-950 bg-gradient-to-r from-[#53D769] to-emerald-300 hover:from-emerald-300 hover:to-white shadow-xl shadow-emerald-500/25 transition-all hover:scale-105">
                    Daftar SPMB Online Sekarang &rarr;
                </a>
                <a href="{{ url('/jurusan') }}" class="px-6 py-3.5 rounded-full text-sm font-bold text-white bg-white/10 hover:bg-white/15 border border-white/20 transition-all">
                    &larr; Lihat Jurusan Lainnya
                </a>
            </div>
        </div>
    </section>

    <!-- ── 12. CLIENT-SIDE TJKT INTERACTIVE SCRIPTS ── -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- A. Interactive Network Topology & Data Packets Canvas ---
            const canvas = document.getElementById('tjkt-network-canvas');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                let width = 0;
                let height = 0;
                let particles = [];
                let packets = [];
                let mouse = { x: null, y: null, radius: 140 };

                const resizeCanvas = () => {
                    const rect = canvas.parentElement.getBoundingClientRect();
                    width = canvas.width = rect.width;
                    height = canvas.height = rect.height;
                    initNetwork();
                };

                const initNetwork = () => {
                    particles = [];
                    packets = [];
                    const particleCount = Math.min(Math.max(Math.floor(width / 26), 25), 65);
                    for (let i = 0; i < particleCount; i++) {
                        particles.push({
                            x: Math.random() * width,
                            y: Math.random() * height,
                            vx: (Math.random() - 0.5) * 0.7,
                            vy: (Math.random() - 0.5) * 0.7,
                            radius: Math.random() * 1.8 + 1.2,
                            alpha: Math.random() * 0.5 + 0.4
                        });
                    }

                    // Pre-generate data packets
                    for (let k = 0; k < 18; k++) {
                        packets.push({
                            p1: Math.floor(Math.random() * particleCount),
                            p2: Math.floor(Math.random() * particleCount),
                            t: Math.random(),
                            speed: 0.006 + Math.random() * 0.012
                        });
                    }
                };

                // Track mouse on header
                const header = canvas.parentElement;
                if (header) {
                    header.addEventListener('mousemove', (e) => {
                        const rect = canvas.getBoundingClientRect();
                        mouse.x = e.clientX - rect.left;
                        mouse.y = e.clientY - rect.top;
                    });
                    header.addEventListener('mouseleave', () => {
                        mouse.x = null;
                        mouse.y = null;
                    });
                }

                window.addEventListener('resize', resizeCanvas);
                resizeCanvas();

                const maxDist = 120;
                const maxDistSq = maxDist * maxDist;

                const animate = () => {
                    ctx.clearRect(0, 0, width, height);

                    // 1. Move & draw nodes
                    for (let i = 0; i < particles.length; i++) {
                        const p = particles[i];
                        p.x += p.vx;
                        p.y += p.vy;

                        if (p.x < 0 || p.x > width) p.vx *= -1;
                        if (p.y < 0 || p.y > height) p.vy *= -1;

                        ctx.beginPath();
                        ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                        ctx.fillStyle = `rgba(162, 232, 185, ${p.alpha})`;
                        ctx.fill();
                    }

                    // 2. Draw lines between close nodes
                    const activePairs = [];
                    for (let i = 0; i < particles.length; i++) {
                        for (let j = i + 1; j < particles.length; j++) {
                            const dx = particles[i].x - particles[j].x;
                            const dy = particles[i].y - particles[j].y;
                            const distSq = dx * dx + dy * dy;

                            if (distSq < maxDistSq) {
                                const dist = Math.sqrt(distSq);
                                const alpha = (1 - dist / maxDist) * 0.32;
                                ctx.strokeStyle = `rgba(162, 232, 185, ${alpha})`;
                                ctx.lineWidth = 0.7;
                                ctx.beginPath();
                                ctx.moveTo(particles[i].x, particles[i].y);
                                ctx.lineTo(particles[j].x, particles[j].y);
                                ctx.stroke();
                                activePairs.push([i, j]);
                            }
                        }

                        // Connect with mouse cursor
                        if (mouse.x !== null && mouse.y !== null) {
                            const mdx = particles[i].x - mouse.x;
                            const mdy = particles[i].y - mouse.y;
                            const mdist = Math.sqrt(mdx * mdx + mdy * mdy);
                            if (mdist < mouse.radius) {
                                const mAlpha = (1 - mdist / mouse.radius) * 0.6;
                                ctx.strokeStyle = `rgba(83, 215, 105, ${mAlpha})`;
                                ctx.lineWidth = 1.2;
                                ctx.beginPath();
                                ctx.moveTo(particles[i].x, particles[i].y);
                                ctx.lineTo(mouse.x, mouse.y);
                                ctx.stroke();
                            }
                        }
                    }

                    // 3. Move & draw traveling data packets
                    if (activePairs.length > 0) {
                        for (let k = 0; k < packets.length; k++) {
                            const pkt = packets[k];
                            pkt.t += pkt.speed;

                            const p1 = particles[pkt.p1];
                            const p2 = particles[pkt.p2];

                            // Check if current pair is still in range
                            let valid = false;
                            if (p1 && p2) {
                                const d2 = (p1.x - p2.x)**2 + (p1.y - p2.y)**2;
                                if (d2 < maxDistSq) valid = true;
                            }

                            if (!valid || pkt.t >= 1) {
                                pkt.t = 0;
                                const randPair = activePairs[Math.floor(Math.random() * activePairs.length)];
                                if (randPair) {
                                    pkt.p1 = randPair[0];
                                    pkt.p2 = randPair[1];
                                }
                            }

                            if (particles[pkt.p1] && particles[pkt.p2]) {
                                const n1 = particles[pkt.p1];
                                const n2 = particles[pkt.p2];
                                const px = n1.x + (n2.x - n1.x) * pkt.t;
                                const py = n1.y + (n2.y - n1.y) * pkt.t;

                                ctx.save();
                                ctx.shadowBlur = 8;
                                ctx.shadowColor = '#53D769';
                                ctx.beginPath();
                                ctx.arc(px, py, 2.2, 0, Math.PI * 2);
                                ctx.fillStyle = '#ffffff';
                                ctx.fill();
                                ctx.restore();
                            }
                        }
                    }

                    requestAnimationFrame(animate);
                };

                requestAnimationFrame(animate);
            }

            // --- B. Live Simulated MikroTik & Linux Terminal Typing Engine ---
            const termLogs = document.getElementById('tjkt-terminal-logs');
            const termPrompt = document.getElementById('tjkt-terminal-prompt');
            const termCmd = document.getElementById('tjkt-terminal-cmd');
            const termScreen = document.getElementById('tjkt-terminal-screen');
            const termBadge = document.getElementById('tjkt-term-badge');

            if (termLogs && termPrompt && termCmd && termScreen) {
                const terminalSequence = [
                    {
                        badge: 'RouterOS v7.14',
                        prompt: '[admin@Wikrama-Core] > ',
                        cmd: '/interface print brief',
                        outputs: [
                            '<span class="text-slate-500">Flags: D - DYNAMIC; R - RUNNING; S - SLAVE</span>',
                            '<span class="text-slate-400">#   NAME               TYPE    ACTUAL-MTU  MAC-ADDRESS</span>',
                            '<span class="text-emerald-400 font-bold">0 R sfp-sfpplus1 (10G) ether         1500  DC:2C:6E:11:A4:01</span>',
                            '<span class="text-emerald-400 font-bold">1 R ether1-gateway     ether         1500  DC:2C:6E:11:A4:02</span>',
                            '<span class="text-cyan-300">2 R vlan10-siswa       vlan          1500  DC:2C:6E:11:A4:01</span>',
                            '<span class="text-cyan-300">3 R vlan20-guru        vlan          1500  DC:2C:6E:11:A4:01</span>',
                            '<span class="text-cyan-300">4 R vlan30-tefa-isp    vlan          1500  DC:2C:6E:11:A4:01</span>',
                            '<span class="text-slate-400 font-semibold">[Fiber Uplink: 10 Gbps SFP+ Active • Telkom Metro-E Garut]</span>'
                        ]
                    },
                    {
                        badge: 'RouterOS v7.14',
                        prompt: '[admin@Wikrama-Core] > ',
                        cmd: '/ip address print',
                        outputs: [
                            '<span class="text-slate-400">#   ADDRESS            NETWORK         INTERFACE</span>',
                            '<span class="text-slate-300">0   10.10.0.1/16       10.10.0.0       vlan10-siswa</span>',
                            '<span class="text-slate-300">1   10.20.0.1/24       10.20.0.0       vlan20-guru</span>',
                            '<span class="text-slate-300">2   192.168.100.1/24   192.168.100.0   vlan30-tefa-isp</span>',
                            '<span class="text-yellow-300 font-bold">3   180.252.14.82/29   180.252.14.80   ether1-gateway</span>'
                        ]
                    },
                    {
                        badge: 'RouterOS v7.14',
                        prompt: '[admin@Wikrama-Core] > ',
                        cmd: '/ip firewall filter add chain=forward action=drop connection-state=invalid comment="Drop Invalid Packets"',
                        outputs: [
                            '<span class="text-[#53D769] font-bold">&#10003; [OK] Rule #14 successfully applied to Mangle &amp; Filter table.</span>',
                            '<span class="text-slate-400">Anti-DDoS SYN flood &amp; DNS amplification filter: ACTIVE</span>'
                        ]
                    },
                    {
                        badge: 'RouterOS v7.14',
                        prompt: '[admin@Wikrama-Core] > ',
                        cmd: '/ip dhcp-server lease print count-only',
                        outputs: [
                            '<span class="text-cyan-300 font-bold">1,248</span> <span class="text-slate-400">active DHCP leases assigned across campus subnets.</span>'
                        ]
                    },
                    {
                        badge: 'Linux Ubuntu 24.04',
                        prompt: '[admin@Wikrama-Core] > ',
                        cmd: 'ssh -i ~/.ssh/id_ed25519 admin@datacenter.wikrama.sch.id',
                        outputs: [
                            '<span class="text-yellow-300 font-semibold">Connecting to datacenter.wikrama.sch.id (10.10.0.250:22)...</span>',
                            '<span class="text-emerald-400 font-bold">Linux wikrama-datacenter 6.8.0-45-generic #45-Ubuntu SMP</span>',
                            '<span class="text-slate-400">Welcome to Wikrama Cloud Enterprise Server • Proxmox VE 8.2</span>'
                        ]
                    },
                    {
                        badge: 'Linux Ubuntu 24.04',
                        prompt: 'admin@datacenter:~$ ',
                        cmd: 'sudo systemctl status wireguard-mesh@wg0',
                        outputs: [
                            '<span class="text-emerald-400">● wg0.service - WireGuard Encrypted Site-to-Site Tunnel</span>',
                            '<span class="text-slate-300">     Loaded: loaded (/etc/systemd/system/wg0.service; enabled)</span>',
                            '<span class="text-emerald-300 font-bold">     Active: active (running)</span>',
                            '<span class="text-cyan-300">     Peers: 14 active nodes (Branch, Lab Cloud, RT/RW Net Hub)</span>'
                        ]
                    },
                    {
                        badge: 'Linux Ubuntu 24.04',
                        prompt: 'admin@datacenter:~$ ',
                        cmd: 'curl -I https://spmb.smkwikrama1garut.sch.id',
                        outputs: [
                            '<span class="text-emerald-400 font-bold">HTTP/2 200 OK</span>',
                            '<span class="text-slate-400">server: nginx/1.24.0 (Ubuntu) • TLSv1.3 AES_256</span>',
                            '<span class="text-emerald-300 font-semibold">&#10003; Latency: 1.84ms • Response: 28ms • All Nodes Healthy</span>'
                        ]
                    }
                ];

                let currentStep = 0;
                let charIndex = 0;

                const runTerminal = () => {
                    if (currentStep >= terminalSequence.length) {
                        const completeDiv = document.createElement('div');
                        completeDiv.className = 'text-[#53D769] font-bold py-1 border-t border-emerald-900/60 mt-2 text-[11px]';
                        completeDiv.innerHTML = '&#10003; [SIMULATION COMPLETE] All 7 Enterprise Network Configurations Active.';
                        termLogs.appendChild(completeDiv);

                        const restartNotice = document.createElement('div');
                        restartNotice.className = 'text-slate-500 text-[10px] pb-1';
                        restartNotice.innerText = 'Restarting live CLI cycle in 4 seconds...';
                        termLogs.appendChild(restartNotice);

                        termScreen.scrollTop = termScreen.scrollHeight;

                        setTimeout(() => {
                            termLogs.innerHTML = '';
                            currentStep = 0;
                            charIndex = 0;
                            termCmd.innerText = '';
                            runTerminal();
                        }, 4000);
                        return;
                    }

                    const step = terminalSequence[currentStep];
                    if (termBadge) termBadge.innerText = step.badge;
                    termPrompt.innerText = step.prompt;

                    // Type command character by character (~15ms per character)
                    if (charIndex < step.cmd.length) {
                        termCmd.innerText += step.cmd.charAt(charIndex);
                        charIndex++;
                        termScreen.scrollTop = termScreen.scrollHeight;
                        setTimeout(runTerminal, 15);
                    } else {
                        // Finished typing command: commit to log
                        const cmdRow = document.createElement('div');
                        cmdRow.className = 'flex items-baseline flex-wrap pt-1 font-bold';
                        cmdRow.innerHTML = `<span class="text-cyan-400 mr-1.5">${step.prompt}</span><span class="text-white">${step.cmd}</span>`;
                        termLogs.appendChild(cmdRow);

                        // Reset active input line
                        termCmd.innerText = '';
                        charIndex = 0;

                        // Fast stream outputs (45ms per line)
                        let outIdx = 0;
                        const streamOutputs = () => {
                            if (outIdx < step.outputs.length) {
                                const outDiv = document.createElement('div');
                                outDiv.className = 'text-[11px] leading-relaxed pl-2';
                                outDiv.innerHTML = step.outputs[outIdx];
                                termLogs.appendChild(outDiv);
                                termScreen.scrollTop = termScreen.scrollHeight;
                                outIdx++;
                                setTimeout(streamOutputs, 45);
                            } else {
                                currentStep++;
                                setTimeout(runTerminal, 600);
                            }
                        };
                        setTimeout(streamOutputs, 100);
                    }
                };

                // Launch terminal typing loop
                setTimeout(runTerminal, 600);
            }
        });
    </script>
</x-layouts.blank>
