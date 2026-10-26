{{--
    ================================================================================
    HALAMAN DETAIL JURUSAN: PENGEMBANGAN PERANGKAT LUNAK DAN GIM (PPLG)
    Gaya Desain: Modern Tech Blue (#147EFB), Terminal Code Studio & High-Contrast Light
    File ini dapat diedit secara manual via teks editor (VS Code, Notepad, dll).
    URL Akses: 127.0.0.1:8000/jurusan/pplg
    ================================================================================
--}}
<x-layouts.blank>
    @php
        $sekolah = $sekolah ?? \App\Modules\Sistem\Models\School::current();
        $schoolName = $sekolah->nama ?? 'SMK Wikrama 1 Garut';
    @endphp

    <!-- ── 1. HERO SECTION: PPLG CYBER TECH & CODE STUDIO ── -->
    <header class="relative pt-8 pb-16 sm:pt-12 sm:pb-20 overflow-hidden bg-gradient-to-b from-[#001D3D] via-[#001428] to-[#0A192F] text-white" style="background: linear-gradient(180deg, #001D3D 0%, #001428 50%, #0A192F 100%);">
        <!-- Ambient Glowing Dots & Circuit Lines -->
        <div class="absolute inset-0 pointer-events-none z-0">
            <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-[#147EFB]/25 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-cyan-500/15 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(rgba(20,126,251,0.18)_1px,transparent_1px)] [background-size:24px_24px] opacity-40"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs font-semibold text-blue-200/80 mb-6">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
                <span>&rsaquo;</span>
                <a href="{{ url('/jurusan') }}" class="hover:text-white transition-colors">Jurusan</a>
                <span>&rsaquo;</span>
                <span class="text-[#5FC9F8]">PPLG (Rekayasa Perangkat Lunak)</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <!-- Text Intro -->
                <div class="lg:col-span-7 space-y-5">
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-[#147EFB]/20 text-[#5FC9F8] border border-[#147EFB]/35 backdrop-blur-md">
                        <span class="w-2 h-2 rounded-full bg-[#147EFB] animate-pulse"></span>
                        <span>Software Engineering &bull; AI &amp; Game Dev &bull; Terakreditasi A</span>
                    </span>

                    <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight">
                        Pengembangan Perangkat Lunak <br class="hidden sm:inline">
                        <span class="bg-gradient-to-r from-[#5FC9F8] via-blue-400 to-[#147EFB] bg-clip-text text-transparent">
                            dan Gim (PPLG)
                        </span>
                    </h1>

                    <p class="text-sm sm:text-base text-slate-200 leading-relaxed font-normal">
                        Mendidik calon software engineer, fullstack developer, dan creator gim berstandar industri modern. Santri menguasai siklus hidup pembuatan aplikasi mulai dari arsitektur backend, frontend interaktif, mobile app, kecerdasan buatan (Machine Learning), hingga game development.
                    </p>

                    <!-- Tech Stack Pills -->
                    <div class="pt-2 flex flex-wrap gap-2 text-xs font-mono-code">
                        <span class="px-3 py-1 rounded-lg bg-blue-950/80 text-cyan-300 border border-blue-800">TypeScript / JS</span>
                        <span class="px-3 py-1 rounded-lg bg-blue-950/80 text-blue-300 border border-blue-800">PHP Laravel</span>
                        <span class="px-3 py-1 rounded-lg bg-blue-950/80 text-sky-300 border border-blue-800">Flutter Mobile</span>
                        <span class="px-3 py-1 rounded-lg bg-blue-950/80 text-amber-300 border border-blue-800">Python AI &amp; ML</span>
                        <span class="px-3 py-1 rounded-lg bg-blue-950/80 text-emerald-300 border border-blue-800">Git &amp; CI/CD</span>
                        <span class="px-3 py-1 rounded-lg bg-blue-950/80 text-purple-300 border border-blue-800">Godot / Unity Engine</span>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="pt-4 flex flex-wrap items-center gap-3">
                        <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="px-6 py-3 rounded-full text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#147EFB] to-blue-700 hover:from-blue-600 hover:to-blue-800 shadow-lg shadow-blue-500/25 transition-all hover:scale-105">
                            Daftar PPLG Sekarang &rarr;
                        </a>
                        <a href="#kurikulum" class="px-5 py-3 rounded-full text-xs sm:text-sm font-bold text-blue-100 bg-white/10 hover:bg-white/15 border border-white/20 transition-all">
                            Lihat Kurikulum &amp; Lab
                        </a>
                    </div>
                </div>

                <!-- Code Terminal Visual Mockup: Live TypeScript Interactive Simulator -->
                <div class="lg:col-span-5">
                    <div class="rounded-3xl bg-slate-950/95 border border-blue-500/35 shadow-2xl overflow-hidden text-white backdrop-blur-xl flex flex-col relative" style="height: 440px; min-height: 440px; max-height: 440px;">
                        <!-- Terminal Scanline Overlay -->
                        <div class="absolute inset-0 bg-[linear-gradient(rgba(0,180,255,0.02)_1px,transparent_1px)] [background-size:100%_3px] pointer-events-none z-10"></div>

                        <!-- Terminal Header -->
                        <div class="px-4 py-3 bg-[#021329] border-b border-blue-900/60 flex items-center justify-between flex-shrink-0 z-20">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-red-500/80 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-yellow-500/80 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-green-500/80 inline-block"></span>
                                <div class="flex items-center gap-1.5 pl-2">
                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-[#3178C6] text-white font-mono">TS</span>
                                    <span class="text-[11px] font-mono text-blue-200 font-semibold">PPLG_Student_Console.ts</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="flex items-center gap-1.5 text-[10px] font-mono text-cyan-400">
                                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                                    <span>AUTO CODING</span>
                                </span>
                            </div>
                        </div>

                        <!-- Terminal Code Screen (Fixed Height, Hidden Scrollbar, Auto-Scroll Only) -->
                        <div id="pplg-code-screen" class="flex-1 p-4 sm:p-5 font-mono text-[11px] leading-relaxed overflow-y-auto select-text relative z-20 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden" style="scrollbar-width: none; -ms-overflow-style: none;">
                            <style>
                                #pplg-code-screen::-webkit-scrollbar { display: none !important; width: 0 !important; height: 0 !important; }
                            </style>
                            <!-- Injected completed code lines -->
                            <div id="pplg-code-lines" class="space-y-0.5"></div>

                            <!-- Currently typing active line -->
                            <div id="pplg-active-line" class="flex items-baseline font-mono text-[11px] pt-0.5">
                                <span id="pplg-line-num" class="text-slate-600 select-none w-6 text-right pr-3 flex-shrink-0">01</span>
                                <span id="pplg-line-content" class="text-white whitespace-pre font-mono"></span>
                                <span class="w-2 h-3.5 bg-blue-400 inline-block animate-pulse ml-0.5 flex-shrink-0"></span>
                            </div>
                        </div>

                        <!-- Terminal Footer Telemetry -->
                        <div class="px-4 py-2 bg-[#021329]/90 border-t border-blue-900/60 flex items-center justify-between text-[10px] font-mono text-slate-400 flex-shrink-0 z-20">
                            <div class="flex items-center gap-3">
                                <span>Target: <strong class="text-blue-300">ES2024 / Node.js 22</strong></span>
                                <span>Framework: <strong class="text-cyan-300">Next.js 15 &bull; AI Agent</strong></span>
                            </div>
                            <span id="pplg-compile-badge" class="text-emerald-400 font-bold">&#10003; 0 errors</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ── 2. STATS & KEY PILLARS STRIP ── -->
    <section class="py-12 bg-blue-50/80 border-y border-blue-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="p-5 rounded-2xl bg-white border border-blue-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-[#00519E]">100%</span>
                    <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mt-1 block">Sertifikasi BNSP &amp; Dicoding</span>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-blue-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-[#00519E]">3 Lab</span>
                    <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mt-1 block">Studio Software &amp; Game AI</span>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-blue-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-[#00519E]">2 Unit</span>
                    <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mt-1 block">TEFA Software Agency</span>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-blue-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-[#00519E]">99%</span>
                    <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mt-1 block">Keterserapan BMW</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 3. PENJELASAN & PROFIL JURUSAN (NEURAL NETWORK MESH BACKDROP) ── -->
    <section class="py-24 bg-[#020b18] text-white relative overflow-hidden" style="background-color: #020b18;">
        <!-- Glowing Deep Learning Synaptic Mesh Canvas (Vibrant & High-Visibility) -->
        <canvas id="pplg-neural-canvas" class="absolute inset-0 w-full h-full pointer-events-auto z-0 opacity-90 sm:opacity-95"></canvas>

        <!-- Subtle Vignette Frame -->
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,transparent_55%,rgba(2,11,24,0.65)_100%)] pointer-events-none z-1"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <!-- Left: Transparent Text Column allowing Neural Network to Shine Through -->
                <div class="lg:col-span-6 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold text-cyan-300 bg-blue-950/80 border border-blue-700/60 shadow-sm backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                        <span>Tentang Kompetensi Keahlian</span>
                    </div>

                    <h2 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight" style="text-shadow: 0 2px 12px rgba(2, 11, 24, 0.9);">
                        Mengapa Memilih Jurusan <br>
                        <span class="text-[#00F0FF] font-extrabold inline-block" style="color: #00F0FF; text-shadow: 0 0 20px rgba(0, 240, 255, 0.6), 0 2px 6px rgba(2, 11, 24, 0.9);">PPLG Wikrama Garut?</span>
                    </h2>

                    <div class="space-y-3 text-slate-100" style="text-shadow: 0 1px 4px rgba(2, 11, 24, 0.95);">
                        <p class="text-blue-50/95 leading-relaxed text-sm sm:text-base">
                            Program keahlian Pengembangan Perangkat Lunak dan Gim (PPLG) di {{ $schoolName }} dirancang khusus untuk mencetak <strong class="text-white font-bold">software engineer berdaya saing global yang berakhlak mulia</strong>. Kurikulum diselaraskan langsung dengan standar software house dan tech unicorn terkemuka.
                        </p>
                        <p class="text-blue-100/90 leading-relaxed text-sm sm:text-base">
                            Siswa tidak hanya diajarkan menulis sintaks kode, tetapi dibiasakan dengan <em class="text-cyan-300 font-medium not-italic">clean architecture, microservices, containerization Docker, version control Git</em>, serta kolaborasi tim berbasis Scrum Agile.
                        </p>
                    </div>

                    <div class="space-y-3 pt-2">
                        <div class="flex items-start gap-3.5 p-4 rounded-2xl bg-blue-950/40 backdrop-blur-sm border border-blue-800/50 hover:border-blue-500/60 hover:bg-blue-900/40 transition-colors">
                            <span class="p-2 rounded-xl bg-blue-500/20 text-[#5FC9F8] border border-blue-500/30 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="text-sm font-bold text-white">Kurikulum Berbasis Proyek Industri Riil</h4>
                                <p class="text-xs text-blue-200/80 leading-relaxed mt-0.5">Siswa belajar dengan membangun produk aplikasi nyata (SaaS, Portal Edukasi, E-Commerce, dan Indie Game).</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3.5 p-4 rounded-2xl bg-blue-950/40 backdrop-blur-sm border border-blue-800/50 hover:border-blue-500/60 hover:bg-blue-900/40 transition-colors">
                            <span class="p-2 rounded-xl bg-blue-500/20 text-[#5FC9F8] border border-blue-500/30 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="text-sm font-bold text-white">Kombinasi Web, Mobile &amp; Artificial Intelligence</h4>
                                <p class="text-xs text-blue-200/80 leading-relaxed mt-0.5">Mengintegrasikan model AI generatif (OpenAI, Gemini API) ke dalam aplikasi modern masa kini.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3.5 p-4 rounded-2xl bg-blue-950/40 backdrop-blur-sm border border-blue-800/50 hover:border-blue-500/60 hover:bg-blue-900/40 transition-colors">
                            <span class="p-2 rounded-xl bg-blue-500/20 text-[#5FC9F8] border border-blue-500/30 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="text-sm font-bold text-white">Magang Industri 6 Bulan &amp; Penyaluran Kerja</h4>
                                <p class="text-xs text-blue-200/80 leading-relaxed mt-0.5">Terhubung dengan jaringan perusahaan teknologi mitra sekolah di Bandung, Jakarta, dan Bogor.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Visual Collage Showcase -->
                <div class="lg:col-span-6 grid grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div class="rounded-2xl overflow-hidden shadow-xl border border-blue-500/30 bg-slate-900/60">
                            <img src="{{ asset('images/landing/lab-komputer-pplg.jpg') }}" alt="Praktik Coding PPLG Wikrama" class="w-full h-48 sm:h-56 object-cover hover:scale-105 transition-all duration-300">
                        </div>
                        <div class="p-5 rounded-2xl bg-slate-950/90 backdrop-blur-md border border-blue-500/30 text-white space-y-2 shadow-lg">
                            <span class="text-[#5FC9F8] text-xs font-bold uppercase tracking-wider">Unit Produksi TEFA</span>
                            <h4 class="text-base font-extrabold">Wikrama Software House</h4>
                            <p class="text-xs text-blue-200/90 leading-relaxed">Mengerjakan proyek pesanan klien riil: website perusahaan, aplikasi sekolah, dan sistem database.</p>
                        </div>
                    </div>
                    <div class="space-y-4 pt-6">
                        <div class="p-5 rounded-2xl bg-blue-950/90 backdrop-blur-md border border-[#147EFB]/40 text-white space-y-2 shadow-xl shadow-blue-950/40">
                            <span class="text-cyan-300 text-xs font-bold uppercase tracking-wider">Spesialisasi Modern</span>
                            <h4 class="text-base font-extrabold">AI &amp; Mobile Studio</h4>
                            <p class="text-xs text-blue-100/90 leading-relaxed">Pengembangan aplikasi Flutter lintas platform (Android/iOS) dan integrasi LLM AI.</p>
                        </div>
                        <div class="rounded-2xl overflow-hidden shadow-xl border border-blue-500/30 bg-slate-900/60">
                            <img src="{{ asset('images/web/hero-pplg.jpg') }}" alt="Suasana Belajar Siswa PPLG" class="w-full h-48 sm:h-56 object-cover hover:scale-105 transition-all duration-300">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 4. PROSPEK KARIR & PELUANG PEKERJAAN ── -->
    <section class="py-20 bg-slate-900 text-white relative overflow-hidden" id="karir">
        <div class="absolute -top-32 -left-32 w-80 h-80 rounded-full bg-[#147EFB]/15 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 rounded-full bg-cyan-500/15 blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-cyan-300 bg-blue-950/80 border border-blue-800">
                    <span>Peluang Karir Global</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white mt-3">
                    Prospek Karir Lulusan <span class="bg-gradient-to-r from-[#5FC9F8] to-[#147EFB] bg-clip-text text-transparent">PPLG</span>
                </h2>
                <p class="text-slate-300 text-sm sm:text-base mt-3">
                    Kebutuhan software developer dan engineer terus melesat di perusahaan rintisan teknologi, korporasi finansial, unicorn, dan industri kreatif.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- 1. Fullstack Web Developer -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-blue-500/20 hover:border-blue-500/60 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/20 text-[#5FC9F8] flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        💻
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Fullstack Web Developer</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Membangun sistem informasi enterprise, RESTful API backend, dan antarmuka web modern dengan Laravel, React, Vue, Next.js, dan Tailwind CSS.
                    </p>
                    <div class="text-xs font-semibold text-cyan-300">
                        <span>Range Gaji: Rp 6.000.000 - Rp 14.000.000</span>
                    </div>
                </div>

                <!-- 2. Mobile App Developer -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-sky-500/20 hover:border-sky-500/60 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-sky-500/20 text-sky-300 flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        📱
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Mobile Application Developer</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Mengembangkan aplikasi Android dan iOS native maupun multi-platform menggunakan Flutter (Dart) dan React Native berstandar Play Store &amp; App Store.
                    </p>
                    <div class="text-xs font-semibold text-sky-300">
                        <span>Range Gaji: Rp 6.000.000 - Rp 13.000.000</span>
                    </div>
                </div>

                <!-- 3. Game Developer & Programmer -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-purple-500/20 hover:border-purple-500/60 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/20 text-purple-300 flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        🎮
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Game Developer &amp; Gameplay Coder</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Merancang gameplay mechanics, physic logic, 2D/3D interaction, dan level design menggunakan engine Godot, Unity, dan C#/GDScript.
                    </p>
                    <div class="text-xs font-semibold text-purple-300">
                        <span>Range Gaji: Rp 5.500.000 - Rp 12.000.000</span>
                    </div>
                </div>

                <!-- 4. AI & Data Prompt Engineer -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-cyan-500/20 hover:border-cyan-500/60 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/20 text-cyan-300 flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        🤖
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">AI Integration &amp; Prompt Engineer</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Mengintegrasikan model LLM, Computer Vision, dan machine learning ke dalam aplikasi bisnis untuk automasi workflow dan chat cerdas.
                    </p>
                    <div class="text-xs font-semibold text-cyan-300">
                        <span>Range Gaji: Rp 7.000.000 - Rp 16.000.000</span>
                    </div>
                </div>

                <!-- 5. QA Automation & Tester -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-emerald-500/20 hover:border-emerald-500/60 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        🧪
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Software QA Automation Engineer</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Menguji kehandalan, keamanan, dan fungsionalitas software melalui unit test, integration test, dan automated testing framework (Pest, Cypress).
                    </p>
                    <div class="text-xs font-semibold text-emerald-300">
                        <span>Range Gaji: Rp 5.000.000 - Rp 10.500.000</span>
                    </div>
                </div>

                <!-- 6. Software Agency Technopreneur -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-amber-500/20 hover:border-amber-500/60 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        💼
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Software House Founder &amp; Creator</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Mendirikan agensi teknologi mandiri, menjual lisensi template digital, mempublikasikan gim independen ke Steam/Play Store, dan melayani klien global.
                    </p>
                    <div class="text-xs font-semibold text-amber-300">
                        <span>Penghasilan: Mandiri &amp; Tak Terbatas</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 5. KURIKULUM 3 TAHUN (FASE E & FASE F) ── -->
    <section id="kurikulum" class="py-20 bg-blue-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-[#147EFB] bg-blue-100 border border-blue-200">
                    <span>Roadmap Pembelajaran</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 mt-3">
                    Kurikulum 3 Tahun Berstandar <span class="text-[#147EFB]">Software House Industri</span>
                </h2>
                <p class="text-slate-600 text-sm sm:text-base mt-3">
                    Dirancang bertahap dari pemahaman algoritma fundamental hingga implementasi arsitektur enterprise skala produksi.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- KELAS X -->
                <div class="bg-white rounded-3xl p-7 border-2 border-blue-200 shadow-sm relative hover:border-[#147EFB] transition-all">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-[#147EFB] font-bold flex items-center justify-center text-sm mb-4">
                        X
                    </div>
                    <span class="text-xs font-bold uppercase text-[#147EFB] tracking-wider">Fase E &bull; Tahun Pertama</span>
                    <h3 class="text-xl font-bold text-slate-900 mt-1 mb-4">Dasar Pemrograman &amp; Algoritma</h3>
                    <p class="text-xs text-slate-600 mb-6">Membangun logika berpikir komputasional, sintaks dasar pemrograman, dan disiplin penulisan kode.</p>

                    <ul class="space-y-3 text-xs text-slate-700">
                        <li class="flex items-start gap-2">
                            <span class="text-[#147EFB] font-bold">&check;</span>
                            <span>Algoritma &amp; Logika Pemrograman Fundamental</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#147EFB] font-bold">&check;</span>
                            <span>HTML5 Semantik, Modern CSS &amp; Tailwind</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#147EFB] font-bold">&check;</span>
                            <span>JavaScript ES6+ &amp; DOM Manipulation</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#147EFB] font-bold">&check;</span>
                            <span>Version Control System (Git &amp; GitHub Workflow)</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#147EFB] font-bold">&check;</span>
                            <span>Basis Data Relasional SQL (MySQL / PostgreSQL)</span>
                        </li>
                    </ul>
                </div>

                <!-- KELAS XI -->
                <div class="bg-white rounded-3xl p-7 border-2 border-[#147EFB] shadow-lg relative ring-2 ring-[#147EFB]/20">
                    <div class="absolute -top-3.5 right-6 px-3 py-1 rounded-full text-[11px] font-extrabold bg-[#147EFB] text-white uppercase tracking-wider shadow">
                        Spesialisasi Stack
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-[#147EFB] to-blue-700 text-white font-bold flex items-center justify-center text-sm mb-4">
                        XI
                    </div>
                    <span class="text-xs font-bold uppercase text-[#147EFB] tracking-wider">Fase F &bull; Tahun Kedua</span>
                    <h3 class="text-xl font-bold text-slate-900 mt-1 mb-4">Fullstack, Mobile App &amp; Gim</h3>
                    <p class="text-xs text-slate-600 mb-6">Penerapan framework industri modern untuk membangun aplikasi web interaktif, mobile, dan game.</p>

                    <ul class="space-y-3 text-xs text-slate-700">
                        <li class="flex items-start gap-2">
                            <span class="text-[#147EFB] font-bold">&check;</span>
                            <span>Backend Enterprise PHP Laravel &amp; RESTful API</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#147EFB] font-bold">&check;</span>
                            <span>Mobile App Cross-Platform Flutter (Dart)</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#147EFB] font-bold">&check;</span>
                            <span>Game Engine Development (Godot / Unity 2D &amp; 3D)</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#147EFB] font-bold">&check;</span>
                            <span>Integrasi Machine Learning &amp; AI Prompt Engineering</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#147EFB] font-bold">&check;</span>
                            <span>Proyek Nyata TEFA: Wikrama Software Agency</span>
                        </li>
                    </ul>
                </div>

                <!-- KELAS XII -->
                <div class="bg-white rounded-3xl p-7 border-2 border-blue-200 shadow-sm relative hover:border-[#147EFB] transition-all">
                    <div class="w-10 h-10 rounded-xl bg-cyan-100 text-[#00519E] font-bold flex items-center justify-center text-sm mb-4">
                        XII
                    </div>
                    <span class="text-xs font-bold uppercase text-[#00519E] tracking-wider">Fase F &bull; Tahun Ketiga</span>
                    <h3 class="text-xl font-bold text-slate-900 mt-1 mb-4">Magang 6 Bulan &amp; Sertifikasi BNSP</h3>
                    <p class="text-xs text-slate-600 mb-6">Penerjunan langsung di software house, uji sertifikasi kompetensi negara, dan portfolio showoff.</p>

                    <ul class="space-y-3 text-xs text-slate-700">
                        <li class="flex items-start gap-2">
                            <span class="text-[#00519E] font-bold">&check;</span>
                            <span>PKL Industri 6 Bulan di Software House &amp; Startup</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#00519E] font-bold">&check;</span>
                            <span>Uji Sertifikasi BNSP Skema Junior Web Programmer</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#00519E] font-bold">&check;</span>
                            <span>Sertifikasi Cloud Practitioner (AWS / Dicoding)</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#00519E] font-bold">&check;</span>
                            <span>Capstone Final Project: Portofolio GitHub Terverifikasi</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#00519E] font-bold">&check;</span>
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
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-[#147EFB] bg-blue-100 border border-blue-200">
                    <span>Fasilitas Standar Software House</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 mt-3">
                    Kolase Laboratorium &amp; <span class="text-[#147EFB]">Studio Praktik Nyata</span>
                </h2>
                <p class="text-slate-600 text-sm sm:text-base mt-3">
                    Setiap komputer dilengkapi prosesor performa tinggi, koneksi internet serat optik dedicated, dan software IDE berlisensi resmi.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Fasilitas 1 -->
                <div class="rounded-2xl overflow-hidden border border-blue-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/landing/lab-komputer-pplg.jpg') }}" alt="Lab Komputer PPLG" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-[#147EFB] text-white">Lab Fullstack Code Studio</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Laboratorium Software Engineering</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Dilengkapi PC Core i7, RAM 16GB, SSD NVMe, dual monitor setup untuk kenyamanan coding, debugging, dan preview aplikasi real-time.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 2 -->
                <div class="rounded-2xl overflow-hidden border border-blue-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/landing/kelas-digital.jpg') }}" alt="Studio Game Dev & AI" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-cyan-700 text-white">Studio Game &amp; AI</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Studio AI &amp; Game Development</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Stasiun kerja dengan GPU dedicated RTX untuk rendering 3D, simulasi physics engine gim, dan pelatihan model Machine Learning.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 3 -->
                <div class="rounded-2xl overflow-hidden border border-blue-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/web/facility-lab.jpg') }}" alt="Lab Mobile Apps" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-800 text-white">Mobile Testing Station</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Mobile App &amp; Device Testing Lab</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Area pengujian aplikasi Android &amp; iOS dengan perangkat fisik multi-layar untuk memastikan kompatibilitas responsif di semua resolusi.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 4 -->
                <div class="rounded-2xl overflow-hidden border border-blue-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/web/activity-learning.jpg') }}" alt="Coworking Space Scrum" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-indigo-700 text-white">Scrum &amp; Agile Hub</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Agile Discussion &amp; Scrum Room</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Ruang kolaborasi tim dengan papan sprint fisik/digital, proyektor presentasi demo aplikasi, dan standing desk untuk daily standup meeting.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 5 -->
                <div class="rounded-2xl overflow-hidden border border-blue-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/landing/suasana-belajar-aktif.jpg') }}" alt="Server Staging Lab" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-slate-900 text-white">Server &amp; DevOps</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Local Cloud Staging Server</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Server on-premise lokal untuk simulasi deployment Docker container, database clustering, dan pipeline CI/CD sebelum rilis ke production.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 6 -->
                <div class="rounded-2xl overflow-hidden border border-blue-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/landing/prestasi-seminar-siswa.jpg') }}" alt="Showcase & Pitching Arena" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-700 text-white">Showcase &amp; Pitching</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Project Demo &amp; Pitching Arena</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Panggung presentasi karya siswa di hadapan para praktisi industri, investor startup, dan civitas akademika saat wisuda karya tahunan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 7. SERTIFIKASI KOMPETENSI RESMI (BNSP & GLOBAL) ── -->
    <section class="py-20 bg-gradient-to-br from-[#001D3D] via-[#002B5B] to-[#0A192F] text-white" style="background: linear-gradient(135deg, #001D3D 0%, #002B5B 50%, #0A192F 100%);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-cyan-300 bg-blue-950/80 border border-blue-800">
                    <span>Pengakuan Industri Berstandar Nasional &amp; Internasional</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white mt-3">
                    Sertifikasi Kompetensi Resmi <span class="bg-gradient-to-r from-[#5FC9F8] to-[#147EFB] bg-clip-text text-transparent">PPLG</span>
                </h2>
                <p class="text-blue-200/90 text-sm sm:text-base mt-3">
                    Lulusan dibekali sertifikat kompetensi berlogo Garuda dari Badan Nasional Sertifikasi Profesi (BNSP) dan lisensi vendor teknologi global.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Sertifikasi 1 -->
                <div class="p-6 rounded-2xl bg-white/5 border border-blue-500/20 backdrop-blur-md space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/20 text-[#5FC9F8] flex items-center justify-center font-bold text-xl">
                        🎖️
                    </div>
                    <h3 class="text-lg font-bold text-white">Sertifikasi BNSP Junior Web Developer</h3>
                    <p class="text-xs text-blue-100/80 leading-relaxed">
                        Standar kompetensi kerja nasional (SKKNI) penguasaan pemrograman web frontend, backend, implementasi basis data, dan keamanan aplikasi dasar.
                    </p>
                    <div class="pt-2 text-xs font-semibold text-cyan-300">
                        Diterbitkan oleh: BNSP LSP P-1
                    </div>
                </div>

                <!-- Sertifikasi 2 -->
                <div class="p-6 rounded-2xl bg-white/5 border border-cyan-500/20 backdrop-blur-md space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/20 text-cyan-300 flex items-center justify-center font-bold text-xl">
                        ☁️
                    </div>
                    <h3 class="text-lg font-bold text-white">AWS Cloud Practitioner Academy</h3>
                    <p class="text-xs text-blue-100/80 leading-relaxed">
                        Sertifikasi pengenalan layanan komputasi awan Amazon Web Services, arsitektur serverless, storage S3, dan pengelolaan infrastruktur cloud modern.
                    </p>
                    <div class="pt-2 text-xs font-semibold text-blue-300">
                        Standar: Amazon Web Services Academy
                    </div>
                </div>

                <!-- Sertifikasi 3 -->
                <div class="p-6 rounded-2xl bg-white/5 border border-indigo-500/20 backdrop-blur-md space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/20 text-indigo-300 flex items-center justify-center font-bold text-xl">
                        📜
                    </div>
                    <h3 class="text-lg font-bold text-white">Dicoding Professional Certification</h3>
                    <p class="text-xs text-blue-100/80 leading-relaxed">
                        Kelulusan kelas profesional frontend developer, backend developer, atau mobile developer dengan penilaian review kode langsung dari expert industri.
                    </p>
                    <div class="pt-2 text-xs font-semibold text-indigo-300">
                        Mitra: Dicoding Elite Tech Partner
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 8. MITRA DUDI & TEMPAT MAGANG ── -->
    <section class="py-16 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-bold text-[#147EFB] uppercase tracking-wider block">Jaringan Industri Teknologi</span>
                <h3 class="text-xl sm:text-2xl font-bold text-slate-900 mt-1">Mitra PKL &amp; Rekrutmen Lulusan</h3>
                <p class="text-xs text-slate-600 mt-2">Siswa menjalani magang nyata selama 6 bulan di software house, startup, dan instansi dengan bimbingan mentor senior.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4 items-center">
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">Telkom Indonesia</span>
                    <span class="text-[10px] text-slate-500">BUMN Telekomunikasi</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">Dicoding Academy</span>
                    <span class="text-[10px] text-slate-500">Tech Platform Mitra</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">AWS Academy</span>
                    <span class="text-[10px] text-slate-500">Cloud Global Partner</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">Google Cloud</span>
                    <span class="text-[10px] text-slate-500">Edu Partner Program</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">PT Barata Tech</span>
                    <span class="text-[10px] text-slate-500">Software House</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">Garena &amp; Indie Lab</span>
                    <span class="text-[10px] text-slate-500">Game Developer</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 9. TESTIMONI ALUMNI PPLG ── -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto bg-gradient-to-r from-[#001D3D] to-[#0A192F] rounded-3xl p-8 sm:p-12 text-white border border-blue-500/30 shadow-xl relative overflow-hidden" style="background: linear-gradient(90deg, #001D3D 0%, #0A192F 100%);">
                <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-[#147EFB]/20 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-8 relative z-10">
                    <img src="{{ asset('images/web/alumni-faras.png') }}" alt="Alumni PPLG Wikrama" class="w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover border-4 border-[#147EFB] shadow-lg shrink-0">
                    <div class="space-y-3 text-center sm:text-left">
                        <div class="flex items-center justify-center sm:justify-start gap-1 text-amber-400">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-sm sm:text-base text-blue-100 italic leading-relaxed">
                            "Di PPLG SMK Wikrama 1 Garut, saya tidak cuma diajari teori, tetapi langsung mengerjakan project software agency sungguhan. Terbiasa menggunakan Git, Laravel, dan kolaborasi tim membuat saya percaya diri saat magang. Sekarang saya bekerja sebagai Software Engineer di tech company sebelum genap setahun lulus!"
                        </p>
                        <div>
                            <h4 class="font-bold text-white text-base">Faras Haidar</h4>
                            <span class="text-xs text-[#5FC9F8]">Alumni PPLG &bull; Senior Fullstack Web Developer</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 10. FAQ SEPUTAR JURUSAN PPLG ── -->
    <section class="py-20 bg-blue-50/50" id="faq">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-xs font-bold text-[#147EFB] uppercase tracking-wider block">Pertanyaan Umum</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">FAQ Jurusan PPLG</h2>
            </div>

            <div class="space-y-4">
                <div class="p-5 rounded-2xl bg-white border border-blue-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Apakah harus sudah bisa coding sebelum masuk PPLG?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Sama sekali tidak harus! Kurikulum kelas X dirancang dari nol (fundamental logika dan algoritma dasar). Yang terpenting adalah kemauan belajar logika, ketelitian, dan rasa penasaran terhadap teknologi.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-blue-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Apakah siswa wajib memiliki laptop sendiri dengan spesifikasi tinggi?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Sekolah menyediakan 3 laboratorium komputer lengkap berspesifikasi tinggi untuk seluruh kegiatan praktik harian. Memiliki laptop pribadi sangat disarankan untuk latihan mandiri di asrama/rumah, namun laptop standar sudah cukup untuk tahun pertama.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-blue-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Apa perbedaan PPLG di Wikrama dengan SMK lainnya?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        PPLG Wikrama menerapkan budaya kerja software house nyata (Agile/Scrum), unit produksi pesanan klien resmi, kurikulum integrasi Artificial Intelligence (AI), dan sertifikasi kompetensi berstandar BNSP &amp; AWS Academy.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-blue-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Apakah lulusan PPLG bisa langsung kuliah di PTN ternama?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Sangat bisa! Melalui program BMW (Bekerja, Melanjutkan, Wirausaha), banyak alumni PPLG berhasil diterima di jurusan Informatika dan Sistem Informasi di PTN/PTS favorit seperti ITB, UI, Telkom University, UNPAD, dan UPI.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 11. CTA DAFTAR PPLG ── -->
    <section class="py-20 bg-gradient-to-r from-[#001D3D] via-[#002B5B] to-[#147EFB] text-white relative overflow-hidden" style="background: linear-gradient(90deg, #001D3D 0%, #002B5B 50%, #147EFB 100%);">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-6">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-white/20 text-[#5FC9F8] border border-white/30">
                Penerimaan Peserta Didik Baru (SPMB)
            </span>
            <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight">
                Siap Menjadi Software Engineer &amp; <br class="hidden sm:inline">
                <span class="bg-gradient-to-r from-[#5FC9F8] via-cyan-200 to-white bg-clip-text text-transparent">Creator Teknologi Masa Depan?</span>
            </h2>
            <p class="text-blue-100/90 text-sm sm:text-base max-w-2xl mx-auto">
                Kuota kelas PPLG sangat terbatas demi menjaga rasio komputer 1 siswa 1 perangkat dan intensitas bimbingan kode. Segera daftarkan dirimu hari ini!
            </p>
            <div class="pt-4 flex flex-wrap items-center justify-center gap-4">
                <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="px-8 py-3.5 rounded-full text-sm font-bold text-slate-950 bg-gradient-to-r from-[#5FC9F8] to-cyan-300 hover:from-cyan-300 hover:to-white shadow-xl shadow-cyan-500/25 transition-all hover:scale-105">
                    Daftar SPMB Online Sekarang &rarr;
                </a>
                <a href="{{ url('/jurusan') }}" class="px-6 py-3.5 rounded-full text-sm font-bold text-white bg-white/10 hover:bg-white/15 border border-white/20 transition-all">
                    &larr; Lihat Jurusan Lainnya
                </a>
            </div>
        </div>
    </section>

    <!-- ── 12. CLIENT-SIDE PPLG INTERACTIVE SCRIPTS ── -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // =========================================================================
            // A. Live TypeScript Coding Simulator (PPLG_Student_Console.ts)
            // =========================================================================
            const codeLinesContainer = document.getElementById('pplg-code-lines');
            const activeLineNum = document.getElementById('pplg-line-num');
            const activeLineContent = document.getElementById('pplg-line-content');
            const codeScreen = document.getElementById('pplg-code-screen');
            const compileBadge = document.getElementById('pplg-compile-badge');

            if (codeLinesContainer && activeLineNum && activeLineContent && codeScreen) {
                const pplgCodeLines = [
                    { num: '01', raw: "import { LLMClient, Agent } from '@ai/core';", html: "<span class=\"text-purple-400 font-bold\">import</span> { <span class=\"text-cyan-300\">LLMClient</span>, <span class=\"text-cyan-300\">Agent</span> } <span class=\"text-purple-400 font-bold\">from</span> <span class=\"text-emerald-400\">'@ai/core'</span>;" },
                    { num: '02', raw: "import { Database } from '@wikrama/framework';", html: "<span class=\"text-purple-400 font-bold\">import</span> { <span class=\"text-cyan-300\">Database</span> } <span class=\"text-purple-400 font-bold\">from</span> <span class=\"text-emerald-400\">'@wikrama/framework'</span>;" },
                    { num: '03', raw: "", html: "&nbsp;" },
                    { num: '04', raw: "interface SoftwareEngineer {", html: "<span class=\"text-purple-400 font-bold\">interface</span> <span class=\"text-yellow-300 font-bold\">SoftwareEngineer</span> {" },
                    { num: '05', raw: "  name: string;", html: "&nbsp;&nbsp;name: <span class=\"text-cyan-300\">string</span>;" },
                    { num: '06', raw: "  stack: Array<'Next.js' | 'Laravel' | 'Flutter' | 'PyTorch'>;", html: "&nbsp;&nbsp;stack: <span class=\"text-yellow-300\">Array</span>&lt;<span class=\"text-emerald-400\">'Next.js'</span> | <span class=\"text-emerald-400\">'Laravel'</span> | <span class=\"text-emerald-400\">'Flutter'</span> | <span class=\"text-emerald-400\">'PyTorch'</span>&gt;;" },
                    { num: '07', raw: "  certifications: string[];", html: "&nbsp;&nbsp;certifications: <span class=\"text-cyan-300\">string</span>[];" },
                    { num: '08', raw: "  deployToCloud(): Promise<boolean>;", html: "&nbsp;&nbsp;<span class=\"text-blue-400 font-semibold\">deployToCloud</span>(): <span class=\"text-yellow-300\">Promise</span>&lt;<span class=\"text-cyan-300\">boolean</span>&gt;;" },
                    { num: '09', raw: "}", html: "}" },
                    { num: '10', raw: "", html: "&nbsp;" },
                    { num: '11', raw: "export class WikramaDeveloper implements SoftwareEngineer {", html: "<span class=\"text-purple-400 font-bold\">export class</span> <span class=\"text-yellow-300 font-bold\">WikramaDeveloper</span> <span class=\"text-purple-400 font-bold\">implements</span> <span class=\"text-cyan-300\">SoftwareEngineer</span> {" },
                    { num: '12', raw: "  public readonly name = 'Santri PPLG Wikrama';", html: "&nbsp;&nbsp;<span class=\"text-purple-400\">public readonly</span> name = <span class=\"text-emerald-400\">'Santri PPLG Wikrama'</span>;" },
                    { num: '13', raw: "  public stack = ['Next.js 15', 'Laravel 11', 'Flutter', 'PyTorch'] as const;", html: "&nbsp;&nbsp;<span class=\"text-purple-400\">public</span> stack = [<span class=\"text-emerald-400\">'Next.js 15'</span>, <span class=\"text-emerald-400\">'Laravel 11'</span>, <span class=\"text-emerald-400\">'Flutter'</span>, <span class=\"text-emerald-400\">'PyTorch'</span>] <span class=\"text-purple-400\">as const</span>;" },
                    { num: '14', raw: "  public certifications = ['BNSP Software Engineer', 'AWS Cloud Practitioner'];", html: "&nbsp;&nbsp;<span class=\"text-purple-400\">public</span> certifications = [<span class=\"text-emerald-400\">'BNSP Software Engineer'</span>, <span class=\"text-emerald-400\">'AWS Cloud Practitioner'</span>];" },
                    { num: '15', raw: "", html: "&nbsp;" },
                    { num: '16', raw: "  async deployToCloud(): Promise<boolean> {", html: "&nbsp;&nbsp;<span class=\"text-purple-400 font-bold\">async</span> <span class=\"text-blue-400 font-semibold\">deployToCloud</span>(): <span class=\"text-yellow-300\">Promise</span>&lt;<span class=\"text-cyan-300\">boolean</span>&gt; {" },
                    { num: '17', raw: "    const agent = new Agent({ model: 'gemini-1.5-pro' });", html: "&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"text-purple-400\">const</span> agent = <span class=\"text-purple-400\">new</span> <span class=\"text-cyan-300\">Agent</span>({ model: <span class=\"text-emerald-400\">'gemini-1.5-pro'</span> });" },
                    { num: '18', raw: "    await agent.optimizeCodebase({ testCoverage: 98.5 });", html: "&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"text-purple-400\">await</span> agent.<span class=\"text-blue-400\">optimizeCodebase</span>({ testCoverage: <span class=\"text-orange-400\">98.5</span> });" },
                    { num: '19', raw: "    console.log('🚀 System live: 100% Production Ready!');", html: "&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"text-cyan-300\">console</span>.<span class=\"text-blue-400\">log</span>(<span class=\"text-emerald-400\">'🚀 System live: 100% Production Ready!'</span>);" },
                    { num: '20', raw: "    return true;", html: "&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"text-purple-400\">return</span> <span class=\"text-orange-400 font-bold\">true</span>;" },
                    { num: '21', raw: "  }", html: "&nbsp;&nbsp;}" },
                    { num: '22', raw: "}", html: "}" }
                ];

                let lineIndex = 0;
                let charIndex = 0;

                const typeCode = () => {
                    if (lineIndex >= pplgCodeLines.length) {
                        // Finished typing whole code block
                        if (compileBadge) {
                            compileBadge.innerHTML = '&#10003; 0 errors &bull; Built in 380ms';
                            compileBadge.className = 'text-emerald-400 font-bold animate-pulse';
                        }

                        const buildNotice = document.createElement('div');
                        buildNotice.className = 'mt-3 pt-2 border-t border-blue-900/60 text-emerald-400 font-bold text-[11px] flex items-center gap-1.5';
                        buildNotice.innerHTML = '&#10003; [BUILD SUCCESS] Next.js 15 &amp; AI Microservices compiled successfully.';
                        codeLinesContainer.appendChild(buildNotice);

                        const loopNotice = document.createElement('div');
                        loopNotice.className = 'text-slate-500 text-[10px] pb-1';
                        loopNotice.innerText = 'Restarting live coding cycle in 4 seconds...';
                        codeLinesContainer.appendChild(loopNotice);

                        codeScreen.scrollTop = codeScreen.scrollHeight;

                        setTimeout(() => {
                            codeLinesContainer.innerHTML = '';
                            lineIndex = 0;
                            charIndex = 0;
                            activeLineContent.innerText = '';
                            if (compileBadge) {
                                compileBadge.innerHTML = '&#10003; 0 errors';
                                compileBadge.className = 'text-emerald-400 font-bold';
                            }
                            typeCode();
                        }, 4000);
                        return;
                    }

                    const curLine = pplgCodeLines[lineIndex];
                    activeLineNum.innerText = curLine.num;

                    // Handle empty lines immediately
                    if (curLine.raw.length === 0) {
                        const committedLine = document.createElement('div');
                        committedLine.className = 'flex items-baseline text-[11px] leading-relaxed';
                        committedLine.innerHTML = `<span class="text-slate-600 select-none w-6 text-right pr-3 flex-shrink-0">${curLine.num}</span><span class="whitespace-pre">&nbsp;</span>`;
                        codeLinesContainer.appendChild(committedLine);

                        activeLineContent.innerText = '';
                        lineIndex++;
                        charIndex = 0;
                        codeScreen.scrollTop = codeScreen.scrollHeight;
                        setTimeout(typeCode, 40);
                        return;
                    }

                    // Fast character typing (~14ms per character)
                    if (charIndex < curLine.raw.length) {
                        activeLineContent.innerText += curLine.raw.charAt(charIndex);
                        charIndex++;
                        codeScreen.scrollTop = codeScreen.scrollHeight;
                        setTimeout(typeCode, 14);
                    } else {
                        // Line finished: commit syntax-highlighted HTML
                        const committedLine = document.createElement('div');
                        committedLine.className = 'flex items-baseline text-[11px] leading-relaxed';
                        committedLine.innerHTML = `<span class="text-slate-600 select-none w-6 text-right pr-3 flex-shrink-0">${curLine.num}</span><span class="whitespace-pre font-mono">${curLine.html}</span>`;
                        codeLinesContainer.appendChild(committedLine);

                        activeLineContent.innerText = '';
                        charIndex = 0;
                        lineIndex++;
                        codeScreen.scrollTop = codeScreen.scrollHeight;
                        setTimeout(typeCode, 50);
                    }
                };

                setTimeout(typeCode, 600);
            }

            // =========================================================================
            // B. Deep Learning Synaptic Mesh Canvas (Section 3: Tentang Kompetensi)
            // =========================================================================
            const neuralCanvas = document.getElementById('pplg-neural-canvas');
            if (neuralCanvas) {
                const ctx = neuralCanvas.getContext('2d');
                let width = 0;
                let height = 0;
                let layers = [];
                let synapses = [];
                let pulses = [];
                let mouse = { x: null, y: null, radius: 150 };

                const resizeNeural = () => {
                    const rect = neuralCanvas.parentElement.getBoundingClientRect();
                    width = neuralCanvas.width = rect.width;
                    height = neuralCanvas.height = rect.height;
                    initNetwork();
                };

                const initNetwork = () => {
                    layers = [];
                    synapses = [];
                    pulses = [];

                    // 5-Layer Deep Neural Architecture: Input (5) -> Hidden 1 (7) -> Hidden 2 (8) -> Hidden 3 (6) -> Output (4)
                    const layerCounts = [5, 7, 8, 6, 4];
                    const numLayers = layerCounts.length;

                    for (let l = 0; l < numLayers; l++) {
                        const count = layerCounts[l];
                        const layerNodes = [];
                        const x = (width * 0.06) + l * (width * 0.88 / (numLayers - 1));

                        for (let n = 0; n < count; n++) {
                            const y = (height * 0.10) + n * (height * 0.80 / Math.max(count - 1, 1));
                            layerNodes.push({
                                x: x,
                                y: y,
                                baseY: y,
                                radius: l === 0 || l === numLayers - 1 ? 7.0 : 5.8,
                                activation: 0.4 + Math.random() * 0.4,
                                phase: Math.random() * Math.PI * 2,
                                color: l === 0 ? '#00f0ff' : (l === numLayers - 1 ? '#10b981' : '#38bdf8')
                            });
                        }
                        layers.push(layerNodes);
                    }

                    // Build Synaptic Connections between adjacent layers
                    for (let l = 0; l < numLayers - 1; l++) {
                        const currLayer = layers[l];
                        const nextLayer = layers[l + 1];

                        for (let i = 0; i < currLayer.length; i++) {
                            for (let j = 0; j < nextLayer.length; j++) {
                                synapses.push({
                                    fromLayer: l,
                                    fromIdx: i,
                                    toLayer: l + 1,
                                    toIdx: j,
                                    weight: 0.25 + Math.random() * 0.4
                                });
                            }
                        }
                    }

                    // Spawn initial intense burst of electrical pulses
                    for (let p = 0; p < 36; p++) {
                        spawnPulse();
                    }
                };

                const spawnPulse = (specificSynapse = null) => {
                    if (layers.length < 2) return;
                    const syn = specificSynapse || synapses[Math.floor(Math.random() * synapses.length)];
                    if (syn) {
                        pulses.push({
                            synapse: syn,
                            progress: specificSynapse ? 0 : Math.random(),
                            speed: 0.016 + Math.random() * 0.022,
                            size: 3.5 + Math.random() * 1.5,
                            color: Math.random() > 0.25 ? '#00f0ff' : '#a855f7'
                        });
                    }
                };

                // Track Mouse Movement over Section 3
                const sectionContainer = neuralCanvas.parentElement;
                if (sectionContainer) {
                    sectionContainer.addEventListener('mousemove', (e) => {
                        const rect = neuralCanvas.getBoundingClientRect();
                        mouse.x = e.clientX - rect.left;
                        mouse.y = e.clientY - rect.top;
                    });
                    sectionContainer.addEventListener('mouseleave', () => {
                        mouse.x = null;
                        mouse.y = null;
                    });
                }

                window.addEventListener('resize', resizeNeural);
                resizeNeural();

                let time = 0;

                const animateNeural = () => {
                    ctx.clearRect(0, 0, width, height);
                    time += 0.025;

                    // Constantly maintain active data pulses across layers
                    if (pulses.length < 32 && Math.random() < 0.3) {
                        spawnPulse();
                    }

                    // 1. Draw Synapses (Axon connections) with vibrant glowing stroke
                    for (let s = 0; s < synapses.length; s++) {
                        const syn = synapses[s];
                        const n1 = layers[syn.fromLayer][syn.fromIdx];
                        const n2 = layers[syn.toLayer][syn.toIdx];

                        if (n1 && n2) {
                            ctx.beginPath();
                            ctx.moveTo(n1.x, n1.y);
                            ctx.lineTo(n2.x, n2.y);
                            const alpha = Math.min(0.20 + syn.weight * 0.3 + (n1.activation + n2.activation) * 0.35, 0.75);
                            ctx.strokeStyle = `rgba(0, 220, 255, ${alpha})`;
                            ctx.lineWidth = 1.3;
                            ctx.stroke();
                        }
                    }

                    // 2. Animate Action Potentials (Traveling Data Sparks with Laser Trails)
                    for (let p = pulses.length - 1; p >= 0; p--) {
                        const pulse = pulses[p];
                        pulse.progress += pulse.speed;

                        const syn = pulse.synapse;
                        const n1 = layers[syn.fromLayer][syn.fromIdx];
                        const n2 = layers[syn.toLayer][syn.toIdx];

                        if (n1 && n2) {
                            const px = n1.x + (n2.x - n1.x) * pulse.progress;
                            const py = n1.y + (n2.y - n1.y) * pulse.progress;

                            // Glowing Spark Tail
                            const tailProgress = Math.max(0, pulse.progress - 0.08);
                            const tx = n1.x + (n2.x - n1.x) * tailProgress;
                            const ty = n1.y + (n2.y - n1.y) * tailProgress;

                            ctx.save();
                            ctx.beginPath();
                            ctx.moveTo(tx, ty);
                            ctx.lineTo(px, py);
                            ctx.strokeStyle = pulse.color;
                            ctx.lineWidth = pulse.size;
                            ctx.shadowBlur = 12;
                            ctx.shadowColor = pulse.color;
                            ctx.stroke();

                            // Spark Head
                            ctx.beginPath();
                            ctx.arc(px, py, pulse.size * 0.9, 0, Math.PI * 2);
                            ctx.fillStyle = '#ffffff';
                            ctx.fill();
                            ctx.restore();

                            // Pulse reached destination neuron
                            if (pulse.progress >= 1) {
                                n2.activation = 1.0; // Flash destination neuron

                                // Chain pulse to next layer
                                if (syn.toLayer < layers.length - 1) {
                                    const nextSynapses = synapses.filter(s => s.fromLayer === syn.toLayer && s.fromIdx === syn.toIdx);
                                    if (nextSynapses.length > 0) {
                                        const nextSyn = nextSynapses[Math.floor(Math.random() * nextSynapses.length)];
                                        spawnPulse(nextSyn);
                                    }
                                }

                                pulses.splice(p, 1);
                            }
                        }
                    }

                    // 3. Draw Neurons with Glowing Halos & Activation Ripples
                    for (let l = 0; l < layers.length; l++) {
                        const layer = layers[l];
                        for (let n = 0; n < layer.length; n++) {
                            const node = layer[n];

                            // Subtle floating oscillation
                            node.y = node.baseY + Math.sin(time + node.phase) * 6;

                            // Decay activation smoothly
                            node.activation = Math.max(0.35, node.activation * 0.96);

                            // Mouse proximity energetic charge
                            if (mouse.x !== null && mouse.y !== null) {
                                const mdx = node.x - mouse.x;
                                const mdy = node.y - mouse.y;
                                const mdist = Math.sqrt(mdx * mdx + mdy * mdy);
                                if (mdist < mouse.radius) {
                                    node.activation = Math.min(1.0, node.activation + 0.2);
                                }
                            }

                            ctx.save();

                            // Expanding ripple ring on activation peak
                            if (node.activation > 0.65) {
                                ctx.beginPath();
                                ctx.arc(node.x, node.y, node.radius + 7 * node.activation, 0, Math.PI * 2);
                                ctx.strokeStyle = `rgba(0, 240, 255, ${(node.activation - 0.65) * 1.5})`;
                                ctx.lineWidth = 1.5;
                                ctx.stroke();
                            }

                            // Outer neuron glowing halo
                            ctx.shadowBlur = 18 * node.activation;
                            ctx.shadowColor = node.color;
                            ctx.beginPath();
                            ctx.arc(node.x, node.y, node.radius + node.activation * 2.5, 0, Math.PI * 2);
                            ctx.fillStyle = node.color;
                            ctx.fill();

                            // Inner bright white nucleus
                            ctx.beginPath();
                            ctx.arc(node.x, node.y, node.radius * 0.55, 0, Math.PI * 2);
                            ctx.fillStyle = '#ffffff';
                            ctx.fill();
                            ctx.restore();
                        }
                    }

                    requestAnimationFrame(animateNeural);
                };

                requestAnimationFrame(animateNeural);
            }
        });
    </script>
</x-layouts.blank>
