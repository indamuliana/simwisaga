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
    <header class="relative pt-8 pb-16 sm:pt-12 sm:pb-20 overflow-hidden bg-gradient-to-b from-[#022c22] via-[#064e3b] to-[#041f1a] text-white">
        <!-- Cyber Matrix Grid & Emerald Ambient Orbs -->
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

                <!-- Network NOC Topology Status Mockup -->
                <div class="lg:col-span-5">
                    <div class="rounded-2xl bg-slate-950 border border-emerald-900/60 shadow-2xl overflow-hidden font-mono-code text-xs">
                        <div class="px-4 py-3 bg-[#032219] border-b border-emerald-900/60 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                                <span class="text-[11px] text-emerald-300 font-bold">NOC Wikrama Backbone Router</span>
                            </div>
                            <span class="text-[10px] text-emerald-400 bg-emerald-950 px-2 py-0.5 rounded border border-emerald-800">RouterOS v7.14</span>
                        </div>
                        <div class="p-5 space-y-2 text-slate-300 leading-relaxed overflow-x-auto">
                            <p class="text-slate-500">// Status Antarmuka Jaringan Fiber Optic</p>
                            <p class="text-emerald-400">[admin@NOC-Wikrama] &gt; /interface print</p>
                            <p><span class="text-cyan-400">0 R sfp-sfpplus1</span> &bull; 10G Fiber Uplink &bull; <span class="text-emerald-400">STATUS: RUNNING</span></p>
                            <p><span class="text-cyan-400">1 R ether1-gateway</span> &bull; Telkom Metro-E &bull; <span class="text-emerald-400">1 Gbps DEDICATED</span></p>
                            <p><span class="text-cyan-400">2 R vlan-siswa</span> &bull; IP: 10.10.0.1/16 &bull; <span class="text-cyan-300">DHCP ACTIVE (1.200 Leases)</span></p>
                            <p class="text-emerald-400">[admin@NOC-Wikrama] &gt; /ip firewall filter print count-only</p>
                            <p><span class="text-yellow-300">Rules Loaded:</span> 48 security rules &bull; <span class="text-emerald-400">DDoS Protection Active</span></p>
                            <div class="pt-2 border-t border-emerald-900/60 text-[11px] text-emerald-300/80">
                                <span class="text-emerald-400">&#10003; Network Health:</span> 99.98% Uptime &bull; MikroTik Academy Center
                            </div>
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
                    <span class="block text-3xl font-extrabold text-[#059669]">100%</span>
                    <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mt-1 block">Sertifikasi MikroTik &amp; BNSP</span>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-emerald-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-[#059669]">4 Lab</span>
                    <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mt-1 block">NOC, Server, Fiber &amp; Cyber</span>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-emerald-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-[#059669]">2 Unit</span>
                    <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mt-1 block">TEFA ISP &amp; RT/RW Net Hub</span>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-emerald-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-[#059669]">99%</span>
                    <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mt-1 block">Keterserapan BMW</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 3. PENJELASAN & PROFIL JURUSAN ── -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-6 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-emerald-800 bg-emerald-100 border border-emerald-200">
                        <span>Tentang Kompetensi Keahlian</span>
                    </div>
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Mengapa Memilih Jurusan <br>
                        <span class="text-[#059669]">TJKT Wikrama Garut?</span>
                    </h2>
                    <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                        Program keahlian Teknik Jaringan Komputer dan Telekomunikasi (TJKT) di {{ $schoolName }} dirancang untuk menghasilkan <strong>arsitek infrastruktur jaringan dan pertahanan siber</strong> yang tangguh, adaptif, serta berintegritas tinggi.
                    </p>
                    <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                        Siswa tidak hanya belajar merakit komputer atau crimping kabel UTP biasa, melainkan mengoperasikan langsung <em>perangkat routing enterprise MikroTik, switch serat optik, mesin fusion splicer fiber optic, server virtualization Proxmox</em>, dan konfigurasi firewall filtering.
                    </p>

                    <div class="space-y-3 pt-2">
                        <div class="flex items-start gap-3 p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-100">
                            <span class="p-2 rounded-lg bg-[#059669] text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">MikroTik Academy &amp; Huawei ICT Official Center</h4>
                                <p class="text-xs text-slate-600">Siswa diajar langsung oleh instruktur bersertifikasi internasional dan berhak mengikuti ujian sertifikasi MTCNA resmi di sekolah.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-100">
                            <span class="p-2 rounded-lg bg-[#059669] text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Praktik Fiber Optic Fusion Splicing Nyata</h4>
                                <p class="text-xs text-slate-600">Menguasai pemotongan fiber, penyambungan dengan splicer presisi, pengukuran redaman OTDR, dan penarikan kabel udara.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-100">
                            <span class="p-2 rounded-lg bg-[#059669] text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Unit TEFA Pengelola ISP &amp; RT/RW Net</h4>
                                <p class="text-xs text-slate-600">Terlibat langsung dalam pemeliharaan backbone internet sekolah dan layanan jaringan fiber untuk klien sekitar.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visual Collage Showcase -->
                <div class="lg:col-span-6 grid grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div class="rounded-2xl overflow-hidden shadow-md border border-slate-200">
                            <img src="{{ asset('images/landing/lab-tjkt.jpeg') }}" alt="Lab Jaringan TJKT Wikrama" class="w-full h-48 sm:h-56 object-cover hover:scale-105 transition-all duration-300">
                        </div>
                        <div class="p-5 rounded-2xl bg-gradient-to-br from-[#022c22] to-[#041f1a] text-white space-y-2">
                            <span class="text-[#A2E8B9] text-xs font-bold uppercase tracking-wider">Unit Produksi TEFA</span>
                            <h4 class="text-base font-extrabold">Wikrama Network Center</h4>
                            <p class="text-xs text-emerald-200/90 leading-relaxed">Unit operasional yang memelihara jaringan hotspot, server data, dan CCTV di seluruh kampus.</p>
                        </div>
                    </div>
                    <div class="space-y-4 pt-6">
                        <div class="p-5 rounded-2xl bg-[#059669] text-white space-y-2 shadow-lg shadow-emerald-500/20">
                            <span class="text-emerald-100 text-xs font-bold uppercase tracking-wider">Spesialisasi Unggulan</span>
                            <h4 class="text-base font-extrabold">Fiber Optic &amp; Server Lab</h4>
                            <p class="text-xs text-emerald-100 leading-relaxed">Fasilitas splicing mandiri dan konfigurasi cloud server berkecepatan tinggi 10 Gbps.</p>
                        </div>
                        <div class="rounded-2xl overflow-hidden shadow-md border border-slate-200">
                            <img src="{{ asset('images/web/facility-lab.jpg') }}" alt="Suasana Praktik Server Siswa TJKT" class="w-full h-48 sm:h-56 object-cover hover:scale-105 transition-all duration-300">
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
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-emerald-300 bg-emerald-950/80 border border-emerald-800">
                    <span>Peluang Karir Global</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white mt-3">
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
    <section class="py-20 bg-gradient-to-br from-[#022c22] via-[#064e3b] to-[#041f1a] text-white">
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
            <div class="max-w-4xl mx-auto bg-gradient-to-r from-[#022c22] to-[#041f1a] rounded-3xl p-8 sm:p-12 text-white border border-emerald-500/30 shadow-xl relative overflow-hidden">
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
    <section class="py-20 bg-gradient-to-r from-[#022c22] via-[#064e3b] to-[#10b981] text-white relative overflow-hidden">
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
</x-layouts.blank>
