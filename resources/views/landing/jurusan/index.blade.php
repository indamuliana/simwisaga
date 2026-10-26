{{--
    ================================================================================
    HALAMAN KATALOG UTAMA: 4 PROGRAM KEAHLIAN VOKASI (JURUSAN)
    File ini dapat diedit secara manual via teks editor (VS Code, Notepad, dll).
    URL Akses: 127.0.0.1:8000/jurusan
    ================================================================================
--}}
<x-layouts.blank>
    @php
        $sekolah = $sekolah ?? \App\Modules\Sistem\Models\School::current();
        $schoolName = $sekolah->nama ?? 'SMK Wikrama 1 Garut';
    @endphp

    <!-- ── Hero Banner Header Jurusan ── -->
    <header class="relative pt-8 pb-16 sm:pt-12 sm:pb-20 overflow-hidden bg-gradient-to-b from-[#002244] via-[#001833] to-[#00305F] text-white" style="background: linear-gradient(180deg, #002244 0%, #001833 50%, #00305F 100%);">
        <!-- Ambient Glow & Pattern Grid -->
        <div class="absolute inset-0 pointer-events-none z-0">
            <div class="absolute -top-32 -right-32 w-[32rem] h-[32rem] rounded-full bg-blue-500/15 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 w-[28rem] h-[28rem] rounded-full bg-sky-400/15 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.06)_1px,transparent_1px)] [background-size:24px_24px] opacity-50"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center justify-center gap-2 text-xs font-semibold text-blue-200/80 mb-6">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
                <span>&rsaquo;</span>
                <span class="text-[#F5D84A]">Program Keahlian</span>
            </nav>

            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider mb-5 bg-white/10 text-blue-100 border border-white/20 backdrop-blur-md shadow-xs">
                <span class="w-2 h-2 rounded-full bg-[#34A853] animate-pulse"></span>
                <span>Terakreditasi A (Unggul) &bull; SMK Pusat Keunggulan</span>
            </span>

            <h1 class="text-3xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-white leading-tight max-w-4xl mx-auto">
                4 Program Keahlian Unggulan <br class="hidden sm:inline">
                <span class="bg-gradient-to-r from-[#5FC9F8] via-[#FDE293] to-[#FD9426] bg-clip-text text-transparent">
                    Berstandar Industri Global
                </span>
            </h1>

            <p class="mt-5 text-sm sm:text-lg text-blue-100/80 max-w-2xl mx-auto leading-relaxed font-normal">
                Kombinasi kurikulum vokasi modern berbasis proyek nyata, sertifikasi kompetensi berstandar BNSP &amp; vendor internasional, serta penyaluran kerja BMW (Bekerja, Melanjutkan, Wirausaha).
            </p>

            <!-- Fast Stats Counter Bar -->
            <div class="mt-10 grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 max-w-3xl mx-auto text-left">
                <div class="p-4 rounded-2xl bg-white/10 border border-white/10 backdrop-blur-sm">
                    <span class="text-2xl sm:text-3xl font-extrabold text-[#5FC9F8] block font-mono-code">4</span>
                    <span class="text-xs text-blue-100/90 font-medium">Jurusan Terakreditasi A</span>
                </div>
                <div class="p-4 rounded-2xl bg-white/10 border border-white/10 backdrop-blur-sm">
                    <span class="text-2xl sm:text-3xl font-extrabold text-[#53D769] block font-mono-code">100%</span>
                    <span class="text-xs text-blue-100/90 font-medium">Uji Sertifikasi BNSP</span>
                </div>
                <div class="p-4 rounded-2xl bg-white/10 border border-white/10 backdrop-blur-sm">
                    <span class="text-2xl sm:text-3xl font-extrabold text-[#FECB2E] block font-mono-code">210+</span>
                    <span class="text-xs text-blue-100/90 font-medium">Mitra DUDI &amp; Kampus</span>
                </div>
                <div class="p-4 rounded-2xl bg-white/10 border border-white/10 backdrop-blur-sm">
                    <span class="text-2xl sm:text-3xl font-extrabold text-[#FC3158] block font-mono-code">8 Lab</span>
                    <span class="text-xs text-blue-100/90 font-medium">Ruang Praktik Standar Industri</span>
                </div>
            </div>
        </div>
    </header>

    <!-- ── Daftar 4 Jurusan Showcase Cards ── -->
    <main class="py-16 sm:py-24 bg-[#FAF9F6] text-slate-800 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="google-tag google-tag-blue mb-3">
                    <span class="w-2 h-2 rounded-full bg-[#147EFB]"></span>
                    <span>Pilihan Kompetensi Masa Depan</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 mt-2">
                    Eksplorasi Profil &amp; Fasilitas Setiap Jurusan
                </h2>
                <p class="text-sm sm:text-base text-slate-600 mt-3">
                    Klik pada masing-masing jurusan untuk melihat rincian kurikulum, prospek karir, dokumentasi ruang praktik, sertifikasi, hingga karya siswa.
                </p>
            </div>

            <!-- Grid 4 Jurusan (2x2 Big Interactive Showcase) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                {{-- ════════════════════════════════════════════════════════════ --}}
                {{-- CARD 1: PPLG (Pengembangan Perangkat Lunak dan Gim)          --}}
                {{-- ════════════════════════════════════════════════════════════ --}}
                <div class="rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col justify-between group">
                    <div>
                        <!-- Header Visual with Tech Blue Accent -->
                        <div class="relative h-60 sm:h-72 w-full overflow-hidden bg-slate-900">
                            <img src="{{ asset('images/web/hero-pplg.jpg') }}" alt="PPLG SMK Wikrama" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/30 to-transparent"></div>
                            
                            <div class="absolute top-4 left-4 flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-[#147EFB] text-white shadow-md">
                                    PPLG
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/90 text-slate-900 backdrop-blur-xs">
                                    💻 Teknologi Informasi &amp; AI
                                </span>
                            </div>

                            <div class="absolute bottom-4 left-4 right-4">
                                <span class="text-xs font-mono-code font-bold text-sky-300 block mb-1">PROGRAM KEAHLIAN UNGGULAN #1</span>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-white leading-tight">
                                    Pengembangan Perangkat Lunak dan Gim
                                </h3>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6 sm:p-8 space-y-5">
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Membekali santri dengan keahlian rekayasa piranti lunak komprehensif: Fullstack Web Development, Mobile Apps (Flutter/React Native), Machine Learning &amp; AI Analytics, Game Development, hingga UI/UX Design modern.
                            </p>

                            <!-- Chips Keahlian -->
                            <div>
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Fokus Kompetensi:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-[#147EFB] border border-blue-100">Fullstack Web</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-[#147EFB] border border-blue-100">Mobile Flutter</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-[#147EFB] border border-blue-100">AI &amp; Data Analyst</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-[#147EFB] border border-blue-100">Game Engine</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-[#147EFB] border border-blue-100">UI/UX Figma</span>
                                </div>
                            </div>

                            <!-- Highlights: Lab & Sertifikasi -->
                            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-100 text-xs">
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-500 block font-medium">Laboratorium:</span>
                                    <strong class="text-slate-800 text-xs sm:text-sm">Lab Software House &amp; iMac</strong>
                                </div>
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-500 block font-medium">Sertifikasi Utama:</span>
                                    <strong class="text-[#147EFB] text-xs sm:text-sm">BNSP Web Programmer &amp; Oracle</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer CTA Link -->
                    <div class="px-6 sm:px-8 py-4 bg-blue-50/50 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-600">Pelajari kurikulum, prospek karir, &amp; lab PPLG</span>
                        <a href="{{ url('/jurusan/pplg') }}" class="btn-jis-pill-navy !py-2 !px-4 text-xs font-bold inline-flex items-center gap-1.5 group-hover:scale-105 transition-all">
                            <span>Buka Halaman PPLG</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

                {{-- ════════════════════════════════════════════════════════════ --}}
                {{-- CARD 2: TJKT (Teknik Jaringan Komputer dan Telekomunikasi)  --}}
                {{-- ════════════════════════════════════════════════════════════ --}}
                <div class="rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col justify-between group">
                    <div>
                        <!-- Header Visual with Cyber Green Accent -->
                        <div class="relative h-60 sm:h-72 w-full overflow-hidden bg-slate-900">
                            <img src="{{ asset('images/web/hero-tjkt.jpg') }}" alt="TJKT SMK Wikrama" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/30 to-transparent"></div>
                            
                            <div class="absolute top-4 left-4 flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-[#53D769] text-slate-950 shadow-md">
                                    TJKT
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/90 text-slate-900 backdrop-blur-xs">
                                    ⚡ Siber, Jaringan &amp; Cloud
                                </span>
                            </div>

                            <div class="absolute bottom-4 left-4 right-4">
                                <span class="text-xs font-mono-code font-bold text-emerald-300 block mb-1">PROGRAM KEAHLIAN UNGGULAN #2</span>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-white leading-tight">
                                    Teknik Jaringan Komputer &amp; Telekomunikasi
                                </h3>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6 sm:p-8 space-y-5">
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Penguasaan arsitektur jaringan fiber optik, administrasi server Linux/Cloud, pertahanan keamanan siber (Cybersecurity), konfigurasi routerboard enterprise, dan Internet of Things (IoT).
                            </p>

                            <!-- Chips Keahlian -->
                            <div>
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Fokus Kompetensi:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-[#1F513F] border border-emerald-100">Cyber Security</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-[#1F513F] border border-emerald-100">MikroTik MTCNA</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-[#1F513F] border border-emerald-100">Huawei Datacom</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-[#1F513F] border border-emerald-100">Cloud Computing</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-[#1F513F] border border-emerald-100">Fiber Optic</span>
                                </div>
                            </div>

                            <!-- Highlights: Lab & Sertifikasi -->
                            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-100 text-xs">
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-500 block font-medium">Laboratorium:</span>
                                    <strong class="text-slate-800 text-xs sm:text-sm">Lab Jaringan &amp; Fiber Optic</strong>
                                </div>
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-500 block font-medium">Sertifikasi Utama:</span>
                                    <strong class="text-emerald-700 text-xs sm:text-sm">MTCNA, Huawei ICT &amp; BNSP</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer CTA Link -->
                    <div class="px-6 sm:px-8 py-4 bg-emerald-50/50 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-600">Pelajari kurikulum, prospek karir, &amp; lab TJKT</span>
                        <a href="{{ url('/jurusan/tjkt') }}" class="btn-jis-pill-navy !py-2 !px-4 text-xs font-bold inline-flex items-center gap-1.5 group-hover:scale-105 transition-all">
                            <span>Buka Halaman TJKT</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

                {{-- ════════════════════════════════════════════════════════════ --}}
                {{-- CARD 3: PMN (Pemasaran / Bisnis Digital)                    --}}
                {{-- ════════════════════════════════════════════════════════════ --}}
                <div class="rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col justify-between group">
                    <div>
                        <!-- Header Visual with Vibrant Orange Accent -->
                        <div class="relative h-60 sm:h-72 w-full overflow-hidden bg-slate-900">
                            <img src="{{ asset('images/web/hero-pmn.jpg') }}" alt="Pemasaran SMK Wikrama" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/30 to-transparent"></div>
                            
                            <div class="absolute top-4 left-4 flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-[#FD9426] text-white shadow-md">
                                    PMN
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/90 text-slate-900 backdrop-blur-xs">
                                    📈 Bisnis Digital &amp; E-Commerce
                                </span>
                            </div>

                            <div class="absolute bottom-4 left-4 right-4">
                                <span class="text-xs font-mono-code font-bold text-amber-300 block mb-1">PROGRAM KEAHLIAN UNGGULAN #3</span>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-white leading-tight">
                                    Pemasaran (Bisnis Digital &amp; Kreatif)
                                </h3>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6 sm:p-8 space-y-5">
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Mencetak digital marketer, content creator, dan entrepreneur muda yang menguasai live streaming sales di TikTok/Shopee, iklan berbayar (Meta &amp; Google Ads), SEO, content production, serta analitik ritel digital.
                            </p>

                            <!-- Chips Keahlian -->
                            <div>
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Fokus Kompetensi:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-orange-50 text-[#FD9426] border border-orange-100">Live Streaming Sales</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-orange-50 text-[#FD9426] border border-orange-100">Meta &amp; Google Ads</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-orange-50 text-[#FD9426] border border-orange-100">SEO &amp; Copywriting</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-orange-50 text-[#FD9426] border border-orange-100">Content Studio</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-orange-50 text-[#FD9426] border border-orange-100">Retail Automation</span>
                                </div>
                            </div>

                            <!-- Highlights: Lab & Sertifikasi -->
                            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-100 text-xs">
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-500 block font-medium">Laboratorium:</span>
                                    <strong class="text-slate-800 text-xs sm:text-sm">Studio Live Streaming &amp; Toko</strong>
                                </div>
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-500 block font-medium">Sertifikasi Utama:</span>
                                    <strong class="text-[#FD9426] text-xs sm:text-sm">BNSP Digital Marketing &amp; LSP</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer CTA Link -->
                    <div class="px-6 sm:px-8 py-4 bg-orange-50/50 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-600">Pelajari kurikulum, prospek karir, &amp; studio PMN</span>
                        <a href="{{ url('/jurusan/pmn') }}" class="btn-jis-pill-navy !py-2 !px-4 text-xs font-bold inline-flex items-center gap-1.5 group-hover:scale-105 transition-all">
                            <span>Buka Halaman Pemasaran</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

                {{-- ════════════════════════════════════════════════════════════ --}}
                {{-- CARD 4: HTL (Perhotelan & Hospitality Management)           --}}
                {{-- ════════════════════════════════════════════════════════════ --}}
                <div class="rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col justify-between group">
                    <div>
                        <!-- Header Visual with Luxury Rose-Gold Accent -->
                        <div class="relative h-60 sm:h-72 w-full overflow-hidden bg-slate-900">
                            <img src="{{ asset('images/web/hero-htl.png') }}" alt="Perhotelan SMK Wikrama" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/30 to-transparent"></div>
                            
                            <div class="absolute top-4 left-4 flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-[#FC3158] text-white shadow-md">
                                    HTL
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/90 text-slate-900 backdrop-blur-xs">
                                    🛎️ Hospitaliti &amp; Barista Bintang 5
                                </span>
                            </div>

                            <div class="absolute bottom-4 left-4 right-4">
                                <span class="text-xs font-mono-code font-bold text-rose-300 block mb-1">PROGRAM KEAHLIAN UNGGULAN #4</span>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-white leading-tight">
                                    Perhotelan (Hospitality &amp; Barista)
                                </h3>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6 sm:p-8 space-y-5">
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Keahlian berstandar internasional dalam industri perhotelan bintang lima: Front Office System, Housekeeping &amp; Laundry, Food &amp; Beverage Service, Seni Kopi (Barista Profesional), dan Guest Service Excellence.
                            </p>

                            <!-- Chips Keahlian -->
                            <div>
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Fokus Kompetensi:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-50 text-[#FC3158] border border-rose-100">Front Office PMS</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-50 text-[#FC3158] border border-rose-100">Housekeeping Suite</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-50 text-[#FC3158] border border-rose-100">Barista &amp; Latte Art</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-50 text-[#FC3158] border border-rose-100">Table Manner</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-50 text-[#FC3158] border border-rose-100">Public Speaking En</span>
                                </div>
                            </div>

                            <!-- Highlights: Lab & Sertifikasi -->
                            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-100 text-xs">
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-500 block font-medium">Laboratorium:</span>
                                    <strong class="text-slate-800 text-xs sm:text-sm">Mock-up Suite Hotel &amp; Cafe</strong>
                                </div>
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-500 block font-medium">Sertifikasi Utama:</span>
                                    <strong class="text-[#FC3158] text-xs sm:text-sm">BNSP Perhotelan &amp; Barista</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer CTA Link -->
                    <div class="px-6 sm:px-8 py-4 bg-rose-50/50 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-600">Pelajari kurikulum, prospek karir, &amp; lab Hotel</span>
                        <a href="{{ url('/jurusan/htl') }}" class="btn-jis-pill-navy !py-2 !px-4 text-xs font-bold inline-flex items-center gap-1.5 group-hover:scale-105 transition-all">
                            <span>Buka Halaman Perhotelan</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Quick Comparison Matrix Table -->
            <div class="mt-16 sm:mt-24 bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-10 shadow-sm">
                <div class="border-b border-slate-100 pb-5 mb-6 text-center sm:text-left">
                    <span class="text-xs font-mono-code font-bold uppercase tracking-wider text-[#00519E] block mb-1">KOMPARASI KEUNGGULAN</span>
                    <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900">Tabel Komparasi Singkat 4 Jurusan</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                                <th class="py-3 px-4">Program Keahlian</th>
                                <th class="py-3 px-4">Akreditasi</th>
                                <th class="py-3 px-4">Sertifikasi Utama</th>
                                <th class="py-3 px-4">Unit Produksi / TeFa</th>
                                <th class="py-3 px-4">Mitra DUDI Kunci</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="hover:bg-blue-50/40 transition-colors">
                                <td class="py-4 px-4 font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#147EFB]"></span>
                                    <span>PPLG (Software &amp; Game)</span>
                                </td>
                                <td class="py-4 px-4"><span class="px-2 py-0.5 rounded font-bold bg-emerald-100 text-emerald-800 text-xs">A (Unggul)</span></td>
                                <td class="py-4 px-4 font-medium text-slate-700">BNSP Programmer, Oracle, Google Cloud</td>
                                <td class="py-4 px-4 font-medium text-slate-700">Software House &amp; IT Consultant</td>
                                <td class="py-4 px-4 text-slate-600">Google, Telkom, AWS, Binus</td>
                                <td class="py-4 px-4 text-right">
                                    <a href="{{ url('/jurusan/pplg') }}" class="font-bold text-[#147EFB] hover:underline">Detail &rarr;</a>
                                </td>
                            </tr>
                            <tr class="hover:bg-emerald-50/40 transition-colors">
                                <td class="py-4 px-4 font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#53D769]"></span>
                                    <span>TJKT (Jaringan &amp; Siber)</span>
                                </td>
                                <td class="py-4 px-4"><span class="px-2 py-0.5 rounded font-bold bg-emerald-100 text-emerald-800 text-xs">A (Unggul)</span></td>
                                <td class="py-4 px-4 font-medium text-slate-700">MikroTik MTCNA, Huawei ICT, BNSP</td>
                                <td class="py-4 px-4 font-medium text-slate-700">NOC &amp; Layanan ISP Sekolah</td>
                                <td class="py-4 px-4 text-slate-600">Huawei, MikroTik, Telkom Akses</td>
                                <td class="py-4 px-4 text-right">
                                    <a href="{{ url('/jurusan/tjkt') }}" class="font-bold text-[#1F513F] hover:underline">Detail &rarr;</a>
                                </td>
                            </tr>
                            <tr class="hover:bg-orange-50/40 transition-colors">
                                <td class="py-4 px-4 font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#FD9426]"></span>
                                    <span>PMN (Bisnis Digital)</span>
                                </td>
                                <td class="py-4 px-4"><span class="px-2 py-0.5 rounded font-bold bg-emerald-100 text-emerald-800 text-xs">A (Unggul)</span></td>
                                <td class="py-4 px-4 font-medium text-slate-700">BNSP Digital Marketer, LSP Ritel</td>
                                <td class="py-4 px-4 font-medium text-slate-700">Studio Live Commerce &amp; Wikrama Mart</td>
                                <td class="py-4 px-4 text-slate-600">Shopee, TikTok Shop, Indomarco</td>
                                <td class="py-4 px-4 text-right">
                                    <a href="{{ url('/jurusan/pmn') }}" class="font-bold text-[#FD9426] hover:underline">Detail &rarr;</a>
                                </td>
                            </tr>
                            <tr class="hover:bg-rose-50/40 transition-colors">
                                <td class="py-4 px-4 font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#FC3158]"></span>
                                    <span>HTL (Perhotelan)</span>
                                </td>
                                <td class="py-4 px-4"><span class="px-2 py-0.5 rounded font-bold bg-emerald-100 text-emerald-800 text-xs">A (Unggul)</span></td>
                                <td class="py-4 px-4 font-medium text-slate-700">BNSP Front Office &amp; Barista Certified</td>
                                <td class="py-4 px-4 font-medium text-slate-700">Wikrama Suite Hotel &amp; Barista Cafe</td>
                                <td class="py-4 px-4 text-slate-600">Aston, Santika, favehotel, Novotel</td>
                                <td class="py-4 px-4 text-right">
                                    <a href="{{ url('/jurusan/htl') }}" class="font-bold text-[#FC3158] hover:underline">Detail &rarr;</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Konsultasi & Direct SPMB Banner -->
            <div class="mt-14 p-8 sm:p-12 rounded-3xl bg-gradient-to-r from-[#00305F] via-[#002244] to-[#001833] text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl" style="background: linear-gradient(90deg, #00305F 0%, #002244 50%, #001833 100%);">
                <div>
                    <span class="text-xs font-mono-code font-bold uppercase tracking-wider text-[#F5D84A] block mb-1">BIMBINGAN MINAT &amp; BAKAT</span>
                    <h3 class="text-xl sm:text-2xl font-extrabold text-white">Masih Ragu Menentukan Jurusan?</h3>
                    <p class="text-xs sm:text-sm text-blue-100/80 mt-1 max-w-xl">
                        Konsultasikan potensi dirimu secara gratis bersama guru pembimbing dan staf akademik SMK Wikrama 1 Garut melalui layanan WhatsApp resmi.
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a href="https://wa.me/6281323314430?text=Halo%20Admin%20SMK%20Wikrama%201%20Garut,%20saya%20ingin%20konsultasi%20pemilihan%20jurusan" 
                       target="_blank" 
                       rel="noopener"
                       class="px-5 py-3 rounded-full text-xs sm:text-sm font-bold text-slate-900 bg-[#F5D84A] hover:bg-[#E2B714] shadow-md transition-all hover:scale-105 inline-flex items-center gap-2">
                        <span>💬 Konsultasi via WhatsApp</span>
                    </a>
                    <a href="https://spmb.smkwikrama1garut.sch.id/" 
                       target="_blank" 
                       rel="noopener"
                       class="btn-jis-pill-navy !py-3 !px-6 text-xs sm:text-sm font-bold border border-white/20">
                        <span>Daftar SPMB Online &rarr;</span>
                    </a>
                </div>
            </div>

        </div>
    </main>
</x-layouts.blank>
