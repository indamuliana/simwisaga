{{--
    ================================================================================
    HALAMAN DETAIL JURUSAN: PERHOTELAN / HOSPITALITY (HTL)
    Gaya Desain: Luxury 5-Star Hospitality, Boutique Suite, Warm Rose-Gold & Champagne
    File ini dapat diedit secara manual via teks editor (VS Code, Notepad, dll).
    URL Akses: 127.0.0.1:8000/jurusan/htl (alias: /jurusan/perhotelan)
    ================================================================================
--}}
<x-layouts.blank>
    @php
        $sekolah = $sekolah ?? \App\Modules\Sistem\Models\School::current();
        $schoolName = $sekolah->nama ?? 'SMK Wikrama 1 Garut';
    @endphp

    <!-- ── 1. HERO SECTION: 5-STAR LUXURY HOSPITALITY & BARISTA LOUNGE ── -->
    <header class="relative pt-8 pb-16 sm:pt-12 sm:pb-20 overflow-hidden bg-gradient-to-b from-[#2a0e18] via-[#1a0a12] to-[#0f050b] text-white">
        <!-- Rose Gold & Champagne Ambient Orbs & Motif -->
        <div class="absolute inset-0 pointer-events-none z-0">
            <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-[#FC3158]/20 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-[#E2B714]/15 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(rgba(252,49,88,0.12)_1px,transparent_1px)] [background-size:24px_24px] opacity-40"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs font-semibold text-rose-200/80 mb-6">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
                <span>&rsaquo;</span>
                <a href="{{ url('/jurusan') }}" class="hover:text-white transition-colors">Jurusan</a>
                <span>&rsaquo;</span>
                <span class="text-[#FC3158]">Perhotelan (HTL)</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <!-- Text Intro -->
                <div class="lg:col-span-7 space-y-5">
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-[#FC3158]/20 text-[#FFA8B8] border border-[#FC3158]/30 backdrop-blur-md">
                        <span class="w-2 h-2 rounded-full bg-[#FC3158] animate-pulse"></span>
                        <span>Hospitality &bull; Barista Professional &bull; Terakreditasi A</span>
                    </span>

                    <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight">
                        Perhotelan &amp; <br class="hidden sm:inline">
                        <span class="bg-gradient-to-r from-[#FC3158] via-rose-300 to-[#E2B714] bg-clip-text text-transparent">
                            Hospitality Management
                        </span>
                    </h1>

                    <p class="text-sm sm:text-base text-rose-100/90 leading-relaxed font-normal">
                        Mendidik insan perhotelan yang berakhlak mulia, berwawasan global, dan berstandar internasional bintang 5. Menguasai manajemen Front Office, tata graha (Housekeeping), tata hidang (F&amp;B Service), barista racikan kopi modern, serta etika hospitality berkelas dunia.
                    </p>

                    <!-- Skill Badges -->
                    <div class="pt-2 flex flex-wrap gap-2 text-xs font-mono-code">
                        <span class="px-3 py-1 rounded-lg bg-rose-950/80 text-[#FFA8B8] border border-rose-800/80">Front Office PMS</span>
                        <span class="px-3 py-1 rounded-lg bg-rose-950/80 text-amber-200 border border-amber-800/80">Certified Barista Art</span>
                        <span class="px-3 py-1 rounded-lg bg-rose-950/80 text-rose-300 border border-rose-800/80">Executive Housekeeping</span>
                        <span class="px-3 py-1 rounded-lg bg-rose-950/80 text-yellow-300 border border-yellow-800/80">Table Manner 5-Star</span>
                        <span class="px-3 py-1 rounded-lg bg-rose-950/80 text-pink-300 border border-pink-800/80">F&amp;B Service &amp; Banquet</span>
                        <span class="px-3 py-1 rounded-lg bg-rose-950/80 text-orange-300 border border-orange-800/80">English for Hospitality</span>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="pt-4 flex flex-wrap items-center gap-3">
                        <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="px-6 py-3 rounded-full text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FC3158] via-pink-600 to-rose-700 hover:from-rose-500 hover:to-pink-600 shadow-lg shadow-rose-600/30 transition-all hover:scale-105">
                            Daftar Perhotelan Sekarang &rarr;
                        </a>
                        <a href="#kurikulum" class="px-5 py-3 rounded-full text-xs sm:text-sm font-bold text-rose-100 bg-white/10 hover:bg-white/15 border border-white/20 transition-all">
                            Lihat Kurikulum &amp; Fasilitas
                        </a>
                    </div>
                </div>

                <!-- 5-Star Suite & Concierge Showcase Card -->
                <div class="lg:col-span-5">
                    <div class="relative rounded-3xl overflow-hidden bg-gradient-to-b from-[#3a1525] to-[#200b14] border border-[#FC3158]/30 shadow-2xl p-6 sm:p-7">
                        <!-- Top Bar Luxury Badge -->
                        <div class="flex items-center justify-between pb-4 border-b border-rose-900/60 mb-5">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-[#FC3158]"></span>
                                <span class="text-xs font-bold uppercase tracking-wider text-[#FFA8B8]">Wikrama Hospitality Suite</span>
                            </div>
                            <div class="flex items-center gap-1 text-[#E2B714]">
                                @for($i = 0; $i < 5; $i++)
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>

                        <!-- Hotel Hero Image -->
                        <div class="relative rounded-2xl overflow-hidden mb-5 aspect-[16/10] border border-rose-800/40 group">
                            <img src="{{ asset('images/landing/jurusan-hotel.png') }}" alt="Jurusan Perhotelan SMK Wikrama" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                            <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-white text-xs">
                                <span class="bg-black/60 backdrop-blur-md px-3 py-1 rounded-full border border-rose-500/30">Mockup Room &amp; Cafe Barista</span>
                                <span class="bg-[#FC3158] font-bold px-3 py-1 rounded-full shadow">Bintang 5 Standar</span>
                            </div>
                        </div>

                        <!-- Highlights Grid -->
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="p-3 rounded-xl bg-rose-950/60 border border-rose-900/60">
                                <span class="text-rose-300 font-medium block">Praktek Barista</span>
                                <span class="text-white font-bold text-sm">Espresso &amp; Manual</span>
                            </div>
                            <div class="p-3 rounded-xl bg-rose-950/60 border border-rose-900/60">
                                <span class="text-rose-300 font-medium block">Sistem PMS Hotel</span>
                                <span class="text-white font-bold text-sm">VHP &amp; Opera Ready</span>
                            </div>
                            <div class="p-3 rounded-xl bg-rose-950/60 border border-rose-900/60">
                                <span class="text-rose-300 font-medium block">Sertifikat Resmi</span>
                                <span class="text-[#E2B714] font-bold text-sm">BNSP LSP P-1</span>
                            </div>
                            <div class="p-3 rounded-xl bg-rose-950/60 border border-rose-900/60">
                                <span class="text-rose-300 font-medium block">Mitra Hotel</span>
                                <span class="text-white font-bold text-sm">Aston &bull; Santika</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ── 2. STATS & KEY PILLARS ── -->
    <section class="py-12 bg-rose-50 border-y border-rose-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="p-5 rounded-2xl bg-white border border-rose-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-[#FC3158]">100%</span>
                    <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mt-1 block">Magang Hotel Bintang 4/5</span>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-rose-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-[#FC3158]">3</span>
                    <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mt-1 block">Sertifikasi BNSP &amp; Barista</span>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-rose-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-[#FC3158]">2 Unit</span>
                    <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mt-1 block">Teaching Factory &amp; Cafe</span>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-rose-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-[#FC3158]">98%</span>
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
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-[#FC3158] bg-rose-100 border border-rose-200">
                        <span>Tentang Kompetensi Keahlian</span>
                    </div>
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Mengapa Memilih Jurusan <br>
                        <span class="text-[#FC3158]">Perhotelan Wikrama?</span>
                    </h2>
                    <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                        Program keahlian Perhotelan di {{ $schoolName }} dirancang khusus untuk menghasilkan tenaga profesional tingkat menengah yang memiliki <strong>attitude prima, integritas islami, kemampuan multi-bahasa, serta kecakapan teknis hospitality berstandar bintang 5</strong>.
                    </p>
                    <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                        Bukan hanya sekadar teori, santri diajarkan langsung mengoperasikan <em>Property Management System (PMS)</em> berkelas industri, meracik kopi dengan mesin espresso profesional, table manner internasional, hingga manajemen pengelolaan kamar presidential suite.
                    </p>

                    <div class="space-y-3 pt-2">
                        <div class="flex items-start gap-3 p-3.5 rounded-xl bg-rose-50/70 border border-rose-100">
                            <span class="p-2 rounded-lg bg-[#FC3158] text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Hospitality Mindset &amp; Grooming Standar Bintang 5</h4>
                                <p class="text-xs text-slate-600">Pelatihan kedisiplinan berbusana rapi, postur tubuh elegan, komunikasi santun, dan senyum tulus pelayanan profesional.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3.5 rounded-xl bg-rose-50/70 border border-rose-100">
                            <span class="p-2 rounded-lg bg-[#FC3158] text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Kombinasi Hotelier &amp; Barista Modern</h4>
                                <p class="text-xs text-slate-600">Keahlian ganda yang sangat dicari industri modern: menguasai tata graha hotel sekaligus mahir meracik kopi kekinian.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3.5 rounded-xl bg-rose-50/70 border border-rose-100">
                            <span class="p-2 rounded-lg bg-[#FC3158] text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Kerjasama Langsung DUDI Bintang 4 &amp; 5</h4>
                                <p class="text-xs text-slate-600">Penempatan magang 6 bulan di Aston, Santika, Mercure, favehotel dengan sertifikat penilaian resmi industri.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visual Collage Showcase -->
                <div class="lg:col-span-6 grid grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div class="rounded-2xl overflow-hidden shadow-md border border-slate-200">
                            <img src="{{ asset('images/web/hero-htl.png') }}" alt="Praktik Perhotelan Wikrama" class="w-full h-48 sm:h-56 object-cover hover:scale-105 transition-all duration-300">
                        </div>
                        <div class="p-5 rounded-2xl bg-gradient-to-br from-[#2a0e18] to-[#12050b] text-white space-y-2">
                            <span class="text-[#E2B714] text-xs font-bold uppercase tracking-wider">Unit Produksi TEFA</span>
                            <h4 class="text-base font-extrabold">Wikrama Boutique Suite</h4>
                            <p class="text-xs text-rose-200/90 leading-relaxed">Unit penginapan nyata berstandar kamar hotel bintang yang melayani tamu resmi, dinas, dan relasi sekolah.</p>
                        </div>
                    </div>
                    <div class="space-y-4 pt-6">
                        <div class="p-5 rounded-2xl bg-[#FC3158] text-white space-y-2 shadow-lg shadow-rose-600/20">
                            <span class="text-amber-200 text-xs font-bold uppercase tracking-wider">Keahlian Spesial</span>
                            <h4 class="text-base font-extrabold">Wikrama Coffee Corner</h4>
                            <p class="text-xs text-rose-100 leading-relaxed">Praktik barista langsung dengan pesanan nyata dari guru, tamu, dan santri setiap harinya.</p>
                        </div>
                        <div class="rounded-2xl overflow-hidden shadow-md border border-slate-200">
                            <img src="{{ asset('images/web/activity-learning.jpg') }}" alt="Suasana Praktik Siswa Perhotelan" class="w-full h-48 sm:h-56 object-cover hover:scale-105 transition-all duration-300">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 4. PROSPEK KARIR & PEKERJAAN ALUMNI ── -->
    <section class="py-20 bg-slate-900 text-white relative overflow-hidden">
        <div class="absolute -top-32 -left-32 w-80 h-80 rounded-full bg-[#FC3158]/15 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 rounded-full bg-[#E2B714]/15 blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-[#FFA8B8] bg-rose-950/80 border border-rose-800">
                    <span>Peluang Karir Global</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white mt-3">
                    Prospek Karir Lulusan <span class="bg-gradient-to-r from-[#FC3158] via-rose-300 to-[#E2B714] bg-clip-text text-transparent">Perhotelan</span>
                </h2>
                <p class="text-slate-300 text-sm sm:text-base mt-3">
                    Lulusan perhotelan tidak hanya berkarir di hotel berbintang, tetapi juga di industri pariwisata, kapal pesiar, restoran mewah, hingga wirausaha cafe independen.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- 1. Front Office Specialist -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-rose-500/20 hover:border-rose-500/50 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-[#FC3158]/20 text-[#FC3158] flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        🏨
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Front Desk &amp; Concierge Specialist</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Mengelola reservasi tamu, registrasi check-in/out, pengoperasian sistem PMS, handling VIP guest, serta melayani informasi wisata dengan ramah dan percaya diri.
                    </p>
                    <div class="text-xs font-semibold text-[#FFA8B8] flex items-center gap-1">
                        <span>Range Gaji: Rp 4.500.000 - Rp 9.000.000</span>
                    </div>
                </div>

                <!-- 2. Certified Professional Barista -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-amber-500/20 hover:border-amber-500/50 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-[#E2B714] flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        ☕
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Professional Barista &amp; Latte Artist</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Ahli meracik signature espresso, manual brew (V60, Aeropress), latte art, quality control biji kopi, hingga manajemen operasional kedai kopi / specialty cafe.
                    </p>
                    <div class="text-xs font-semibold text-amber-300 flex items-center gap-1">
                        <span>Range Gaji: Rp 4.000.000 - Rp 8.000.000+</span>
                    </div>
                </div>

                <!-- 3. Executive Housekeeper & Room Attendant -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-pink-500/20 hover:border-pink-500/50 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-pink-500/20 text-pink-400 flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        🛏️
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Executive Housekeeper &amp; Floor Supervisor</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Menjaga standar kebersihan, kerapian, tata ruang kamar suite hotel berbintang, manajemen linen, perlengkapan laundry, dan inspeksi sanitasi harian.
                    </p>
                    <div class="text-xs font-semibold text-pink-300 flex items-center gap-1">
                        <span>Range Gaji: Rp 4.200.000 - Rp 8.500.000</span>
                    </div>
                </div>

                <!-- 4. F&B Service & Banquet Captain -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-rose-500/20 hover:border-rose-500/50 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        🍽️
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">F&amp;B Service &amp; Banquet Captain</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Mengatur tata hidang perjamuan resmi (table setting), event banquet, gala dinner, wedding reception, serta pelayanan pelanggan di restoran hotel mewah.
                    </p>
                    <div class="text-xs font-semibold text-rose-300 flex items-center gap-1">
                        <span>Range Gaji: Rp 4.500.000 - Rp 9.500.000</span>
                    </div>
                </div>

                <!-- 5. Cruise Ship Hospitality Crew -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-cyan-500/20 hover:border-cyan-500/50 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        🚢
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Cruise Ship Hospitality Crew</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Bekerja keliling dunia di kapal pesiar internasional (Royal Caribbean, Carnival, Costa) dengan penghasilan berstandar dollar AS dan fasilitas lengkap.
                    </p>
                    <div class="text-xs font-semibold text-cyan-300 flex items-center gap-1">
                        <span>Range Gaji: $1.200 - $3.000 USD / Bulan</span>
                    </div>
                </div>

                <!-- 6. Hospitality & Cafe Technopreneur -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-yellow-500/20 hover:border-yellow-500/50 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-yellow-500/20 text-[#E2B714] flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        💼
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Cafe Owner &amp; Event Organizer</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Mendirikan bisnis cafe mandiri, jasa catering profesional, wedding/event planning organizer, atau homestay &amp; boutique villa berbasis digital OTA.
                    </p>
                    <div class="text-xs font-semibold text-yellow-300 flex items-center gap-1">
                        <span>Penghasilan: Mandiri &amp; Tak Terbatas</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 5. KURIKULUM 3 TAHUN (FASE E & FASE F) ── -->
    <section id="kurikulum" class="py-20 bg-rose-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-[#FC3158] bg-rose-100 border border-rose-200">
                    <span>Roadmap Pembelajaran</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 mt-3">
                    Kurikulum 3 Tahun Berstandar <span class="text-[#FC3158]">Industri Hospitality</span>
                </h2>
                <p class="text-slate-600 text-sm sm:text-base mt-3">
                    Disusun bersama asosiasi PHRI dan jaringan manajemen hotel berbintang, memadukan teori, praktik mock-up room, dan magang nyata.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- KELAS X -->
                <div class="bg-white rounded-3xl p-7 border-2 border-rose-200 shadow-sm relative hover:border-[#FC3158] transition-all">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-[#FC3158] font-bold flex items-center justify-center text-sm mb-4">
                        X
                    </div>
                    <span class="text-xs font-bold uppercase text-[#FC3158] tracking-wider">Fase E &bull; Tahun Pertama</span>
                    <h3 class="text-xl font-bold text-slate-900 mt-1 mb-4">Hospitality Fundamentals &amp; Grooming</h3>
                    <p class="text-xs text-slate-600 mb-6">Membangun karakter dasar perhotelan, etika pelayanan prima, grooming, dan pengenalan sanitasi hygine.</p>

                    <ul class="space-y-3 text-xs text-slate-700">
                        <li class="flex items-start gap-2">
                            <span class="text-[#FC3158] font-bold">&check;</span>
                            <span>Dasar-Dasar Industri Perhotelan &amp; Pariwisata</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#FC3158] font-bold">&check;</span>
                            <span>Sanitasi, Hygiene, dan Keselamatan Kerja (K3LH)</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#FC3158] font-bold">&check;</span>
                            <span>Personal Grooming &amp; Hospitality Body Language</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#FC3158] font-bold">&check;</span>
                            <span>English &amp; Foreign Language for Frontliner</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#FC3158] font-bold">&check;</span>
                            <span>Customer Service Excellence &amp; Etika Islami</span>
                        </li>
                    </ul>
                </div>

                <!-- KELAS XI -->
                <div class="bg-white rounded-3xl p-7 border-2 border-[#FC3158] shadow-lg relative ring-2 ring-[#FC3158]/20">
                    <div class="absolute -top-3.5 right-6 px-3 py-1 rounded-full text-[11px] font-extrabold bg-[#FC3158] text-white uppercase tracking-wider shadow">
                        Spesialisasi Dept
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-[#FC3158] to-pink-600 text-white font-bold flex items-center justify-center text-sm mb-4">
                        XI
                    </div>
                    <span class="text-xs font-bold uppercase text-[#FC3158] tracking-wider">Fase F &bull; Tahun Kedua</span>
                    <h3 class="text-xl font-bold text-slate-900 mt-1 mb-4">Front Office, Housekeeping &amp; Barista</h3>
                    <p class="text-xs text-slate-600 mb-6">Pendalaman teknis operasional divisi front office, tata graha suite, dan seni racik barista cafe.</p>

                    <ul class="space-y-3 text-xs text-slate-700">
                        <li class="flex items-start gap-2">
                            <span class="text-[#FC3158] font-bold">&check;</span>
                            <span>Front Office Operations (VHP/Opera PMS System)</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#FC3158] font-bold">&check;</span>
                            <span>Housekeeping Management &amp; Making Bed Suite Room</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#FC3158] font-bold">&check;</span>
                            <span>Food &amp; Beverage Service &amp; Banquet Set Up</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#FC3158] font-bold">&check;</span>
                            <span>Barista Masterclass: Espresso, Manual Brew &amp; Latte Art</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#FC3158] font-bold">&check;</span>
                            <span>Operasional TEFA: Wikrama Boutique Suite &amp; Cafe</span>
                        </li>
                    </ul>
                </div>

                <!-- KELAS XII -->
                <div class="bg-white rounded-3xl p-7 border-2 border-rose-200 shadow-sm relative hover:border-[#FC3158] transition-all">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-[#E2B714] font-bold flex items-center justify-center text-sm mb-4">
                        XII
                    </div>
                    <span class="text-xs font-bold uppercase text-[#E2B714] tracking-wider">Fase F &bull; Tahun Ketiga</span>
                    <h3 class="text-xl font-bold text-slate-900 mt-1 mb-4">Magang 6 Bulan, Table Manner &amp; BNSP</h3>
                    <p class="text-xs text-slate-600 mb-6">Penerjunan langsung di hotel berbintang 4 dan 5, sertifikasi uji kompetensi BNSP, dan penyaluran BMW.</p>

                    <ul class="space-y-3 text-xs text-slate-700">
                        <li class="flex items-start gap-2">
                            <span class="text-[#E2B714] font-bold">&check;</span>
                            <span>PKL Industri 6 Bulan di Jaringan Hotel Bintang 4/5</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#E2B714] font-bold">&check;</span>
                            <span>International Table Manner Experience di Hotel Bintang 5</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#E2B714] font-bold">&check;</span>
                            <span>Uji Sertifikasi BNSP Front Office &amp; Housekeeping</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#E2B714] font-bold">&check;</span>
                            <span>Penyusunan Portofolio &amp; Direct Career Recruitment</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-[#E2B714] font-bold">&check;</span>
                            <span>Penyaluran BMW (Bekerja, Melanjutkan, Wirausaha)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 6. KOLASE RUANGAN PRAKTIK & DOKUMENTASI NYATA ── -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-[#FC3158] bg-rose-100 border border-rose-200">
                    <span>Fasilitas Standar Hotel Bintang</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 mt-3">
                    Kolase Laboratorium &amp; <span class="text-[#FC3158]">Ruang Praktik Nyata</span>
                </h2>
                <p class="text-slate-600 text-sm sm:text-base mt-3">
                    Setiap ruang praktik didesain menyerupai operasional hotel berbintang sesungguhnya agar siswa terbiasa dengan ritme kerja industri.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Fasilitas 1: Mockup Room Suite -->
                <div class="rounded-2xl overflow-hidden border border-rose-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/landing/jurusan-hotel.png') }}" alt="Mockup Suite Room Hotel Wikrama" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-slate-900/80 text-white backdrop-blur-md">Mock-up Suite Room</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Wikrama Hotel Mock-up Room</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Kamar mock-up lengkap dengan king size bed, wardrobe, minibar, linen set, dan kamar mandi standar bintang untuk simulasi making bed dan turndown service.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 2: Wikrama Barista Cafe & Coffee Lounge -->
                <div class="rounded-2xl overflow-hidden border border-rose-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/web/hero-htl.png') }}" alt="Cafe Barista Wikrama" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-[#FC3158] text-white">Barista &amp; Coffee Corner</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Wikrama Coffee Corner &amp; Cafe</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Dilengkapi mesin espresso komersial 2-group, grinder profesional, peralatan manual brew V60, french press, dan area bar untuk melayani tamu.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 3: Front Desk Lobby & PMS Lab -->
                <div class="rounded-2xl overflow-hidden border border-rose-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/web/facility-lab.jpg') }}" alt="Front Desk Reception Simulator" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-600 text-white">Front Desk &amp; PMS Lab</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Front Office Reception Desk</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Meja resepsionis hotel lengkap dengan sistem telepon PABX, kunci kamar RFID, komputer terminal simulasi PMS VHP dan reservasi tamu online.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 4: Table Manner Dining Hall -->
                <div class="rounded-2xl overflow-hidden border border-rose-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/web/activity-learning.jpg') }}" alt="Banquet & Dining Hall" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-slate-900/80 text-white backdrop-blur-md">Dining &amp; Banquet</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Table Manner &amp; Banquet Dining Hall</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Area jamuan makan resmi untuk praktik perjamuan kenegaraan, napkin folding, cutleries arrangement, french &amp; american service method.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 5: Laundry & Linen Care Center -->
                <div class="rounded-2xl overflow-hidden border border-rose-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/web/activity-students.jpg') }}" alt="Laundry & Linen Management" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-pink-700 text-white">Linen &amp; Laundry</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Professional Linen &amp; Laundry Center</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Mesin cuci kapasitas industri, flatwork ironer, dry cleaning equipment, dan sistem penataan inventaris sprei, selimut, dan seragam karyawan.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 6: Grooming & Personality Mirror Room -->
                <div class="rounded-2xl overflow-hidden border border-rose-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/landing/suasana-belajar-aktif.jpg') }}" alt="Grooming & Attitude Studio" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-rose-700 text-white">Grooming &amp; Manner</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Grooming &amp; Personal Attitude Studio</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Cermin full-length, tata rias profesional, panduan hairdo, hijab styling, dan etika senyum 3S (Senyum, Salam, Sapa) berstandar international flight/hotelier.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 7. SERTIFIKASI KOMPETENSI RESMI (BNSP & TABLE MANNER) ── -->
    <section class="py-20 bg-gradient-to-br from-[#1d0b14] via-[#2a0e1c] to-[#12050d] text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-[#FFA8B8] bg-rose-950/80 border border-rose-800">
                    <span>Pengakuan Industri Berstandar Nasional</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white mt-3">
                    Sertifikasi Kompetensi Resmi <span class="bg-gradient-to-r from-[#FC3158] to-[#E2B714] bg-clip-text text-transparent">BNSP &amp; Industri</span>
                </h2>
                <p class="text-rose-200/90 text-sm sm:text-base mt-3">
                    Setiap lulusan dibekali sertifikat kompetensi berlogo Garuda dari Badan Nasional Sertifikasi Profesi (BNSP) dan sertifikat resmi industri perhotelan.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Sertifikasi 1 -->
                <div class="p-6 rounded-2xl bg-white/5 border border-rose-500/20 backdrop-blur-md space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-[#FC3158]/20 text-[#FC3158] flex items-center justify-center font-bold text-xl">
                        🎖️
                    </div>
                    <h3 class="text-lg font-bold text-white">Sertifikasi BNSP Front Office</h3>
                    <p class="text-xs text-rose-100/80 leading-relaxed">
                        Pengakuan negara untuk kompetensi Front Desk Agent, Reservation Clerk, Telephone Operator, dan Penanganan Tamu VIP berdasarkan SKKNI Perhotelan.
                    </p>
                    <div class="pt-2 text-xs font-semibold text-[#FFA8B8]">
                        Diterbitkan oleh: BNSP LSP P-1
                    </div>
                </div>

                <!-- Sertifikasi 2 -->
                <div class="p-6 rounded-2xl bg-white/5 border border-amber-500/20 backdrop-blur-md space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-[#E2B714] flex items-center justify-center font-bold text-xl">
                        ☕
                    </div>
                    <h3 class="text-lg font-bold text-white">Sertifikat Barista &amp; Coffee Brewing</h3>
                    <p class="text-xs text-rose-100/80 leading-relaxed">
                        Sertifikasi keahlian barista mencakup kalibrasi espresso, steaming milk micro-foam, latte art free pour, dan sanitasi bar coffee shop modern.
                    </p>
                    <div class="pt-2 text-xs font-semibold text-amber-300">
                        Standar: Specialty Coffee Association / BNSP
                    </div>
                </div>

                <!-- Sertifikasi 3 -->
                <div class="p-6 rounded-2xl bg-white/5 border border-pink-500/20 backdrop-blur-md space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-pink-500/20 text-pink-400 flex items-center justify-center font-bold text-xl">
                        🍽️
                    </div>
                    <h3 class="text-lg font-bold text-white">Sertifikat Table Manner Hotel Bintang 5</h3>
                    <p class="text-xs text-rose-100/80 leading-relaxed">
                        Sertifikat kursus jamuan makan internasional yang diselenggarakan langsung di hotel berbintang 5, ditandatangani General Manager dan Executive Chef.
                    </p>
                    <div class="pt-2 text-xs font-semibold text-pink-300">
                        Mitra: Jaringan Hotel Aston / Santika
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 8. MITRA DUDI & TEMPAT MAGANG ── -->
    <section class="py-16 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-bold text-[#FC3158] uppercase tracking-wider block">Jaringan Industri Hospitality</span>
                <h3 class="text-xl sm:text-2xl font-bold text-slate-900 mt-1">Mitra PKL &amp; Penyerapan Lulusan</h3>
                <p class="text-xs text-slate-600 mt-2">Siswa menjalani magang nyata selama 6 bulan di hotel bintang 4 &amp; 5 dengan pembimbing industri bersertifikat.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4 items-center">
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">Aston Inn Garut</span>
                    <span class="text-[10px] text-slate-500">Hotel Bintang 4</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">Santika Indonesia</span>
                    <span class="text-[10px] text-slate-500">Hotels &amp; Resorts</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">favehotel Garut</span>
                    <span class="text-[10px] text-slate-500">Archipelago Int.</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">Harmoni Hotel</span>
                    <span class="text-[10px] text-slate-500">Resort &amp; Spa</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">Mercure Hotels</span>
                    <span class="text-[10px] text-slate-500">Accor Group</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">PHRI Jabar</span>
                    <span class="text-[10px] text-slate-500">Asosiasi Industri</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 9. TESTIMONI ALUMNI PERHOTELAN ── -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto bg-gradient-to-r from-[#200b14] to-[#3a1525] rounded-3xl p-8 sm:p-12 text-white border border-rose-500/30 shadow-xl relative overflow-hidden">
                <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-[#FC3158]/20 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-8 relative z-10">
                    <img src="{{ asset('images/web/alumni-desta.png') }}" alt="Alumni Perhotelan Wikrama" class="w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover border-4 border-[#FC3158] shadow-lg shrink-0">
                    <div class="space-y-3 text-center sm:text-left">
                        <div class="flex items-center justify-center sm:justify-start gap-1 text-[#E2B714]">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-sm sm:text-base text-rose-100 italic leading-relaxed">
                            "Di SMK Wikrama 1 Garut jurusan Perhotelan, saya bukan hanya diajari menyambut tamu dengan ramah, tetapi juga kedisiplinan tinggi, sistem PMS hotel internasional, dan keterampilan barista kopi. Begitu selesai magang di hotel bintang 4, saya langsung ditawari kontrak kerja sebelum wisuda!"
                        </p>
                        <div>
                            <h4 class="font-bold text-white text-base">Desta Pratama</h4>
                            <span class="text-xs text-[#FFA8B8]">Alumni Perhotelan &bull; Front Office Supervisor &amp; Head Barista</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 10. FAQ SEPUTAR JURUSAN PERHOTELAN ── -->
    <section class="py-20 bg-rose-50/50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-xs font-bold text-[#FC3158] uppercase tracking-wider block">Pertanyaan Umum</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">FAQ Jurusan Perhotelan</h2>
            </div>

            <div class="space-y-4">
                <div class="p-5 rounded-2xl bg-white border border-rose-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Apakah ada syarat tinggi badan minimal untuk masuk Perhotelan?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Tidak ada syarat mutlak tinggi badan yang membatasi. Yang paling utama adalah kesehatan jasmani, kesopanan sikap, komitmen menjaga kerapian (grooming), dan kemauan untuk belajar melayani dengan tulus hati.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-rose-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Apakah harus sudah lancar bahasa Inggris sebelum mendaftar?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Tidak harus. Selama bersekolah di Wikrama, siswa akan dibimbing dari dasar melalui mata pelajaran <em>English for Hospitality</em> secara interaktif dan menyenangkan hingga percaya diri berkomunikasi dengan tamu asing.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-rose-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Di mana saja lokasi magang/PKL siswa jurusan Perhotelan?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Siswa ditempatkan di hotel berbintang 4 dan 5 di wilayah Garut, Bandung, Bogor, hingga Jakarta seperti Aston, Santika, favehotel, dan grup perhotelan nasional lainnya selama 6 bulan penuh.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-rose-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Apakah lulusan Perhotelan bisa melanjutkan kuliah ke Perguruan Tinggi?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Tentu saja bisa! Lulusan dapat melanjutkan kuliah ke Sekolah Tinggi Pariwisata (STP/NHI Bandung), Politeknik Pariwisata, atau jurusan Manajemen Perhotelan, Pariwisata, dan Bisnis di kampus negeri maupun swasta.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 11. CTA DAFTAR PERHOTELAN ── -->
    <section class="py-20 bg-gradient-to-r from-[#2a0e18] via-[#1a0a12] to-[#0f050b] text-white relative overflow-hidden">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-6">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-[#FC3158]/20 text-[#FFA8B8] border border-[#FC3158]/30">
                Penerimaan Peserta Didik Baru (SPMB)
            </span>
            <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight">
                Siap Menjadi Hotelier &amp; Barista <br class="hidden sm:inline">
                <span class="bg-gradient-to-r from-[#FC3158] via-rose-300 to-[#E2B714] bg-clip-text text-transparent">Profesional Berkelas Dunia?</span>
            </h2>
            <p class="text-rose-100/90 text-sm sm:text-base max-w-2xl mx-auto">
                Kuota kelas Perhotelan sangat terbatas demi menjaga kualitas praktik mock-up room dan intensitas bimbingan barista. Amankan kursi belajarmu sekarang juga!
            </p>
            <div class="pt-4 flex flex-wrap items-center justify-center gap-4">
                <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="px-8 py-3.5 rounded-full text-sm font-bold text-white bg-gradient-to-r from-[#FC3158] to-pink-600 hover:from-rose-500 hover:to-pink-500 shadow-xl shadow-rose-600/30 transition-all hover:scale-105">
                    Daftar SPMB Online Sekarang &rarr;
                </a>
                <a href="{{ url('/jurusan') }}" class="px-6 py-3.5 rounded-full text-sm font-bold text-white bg-white/10 hover:bg-white/15 border border-white/20 transition-all">
                    &larr; Lihat Jurusan Lainnya
                </a>
            </div>
        </div>
    </section>
</x-layouts.blank>
