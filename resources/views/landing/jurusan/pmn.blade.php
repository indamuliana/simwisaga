{{--
    ================================================================================
    HALAMAN DETAIL JURUSAN: PEMASARAN / BISNIS DIGITAL (PMN)
    Gaya Desain: Vibrant E-Commerce Orange (#FD9426), Live Studio & High-Contrast Light
    File ini dapat diedit secara manual via teks editor (VS Code, Notepad, dll).
    URL Akses: 127.0.0.1:8000/jurusan/pmn (alias: /jurusan/pemasaran)
    ================================================================================
--}}
<x-layouts.blank>
    @php
        $sekolah = $sekolah ?? \App\Modules\Sistem\Models\School::current();
        $schoolName = $sekolah->nama ?? 'SMK Wikrama 1 Garut';
    @endphp

    <!-- ── 1. HERO SECTION: PMN DIGITAL COMMERCE & CREATOR STUDIO ── -->
    <header class="relative pt-8 pb-16 sm:pt-12 sm:pb-20 overflow-hidden bg-gradient-to-b from-[#3d1900] via-[#2d1200] to-[#1a0900] text-white" style="background: linear-gradient(180deg, #3d1900 0%, #2d1200 50%, #1a0900 100%);">
        <!-- Sunset Amber Ambient Orbs & Mesh -->
        <div class="absolute inset-0 pointer-events-none z-0">
            <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-[#FD9426]/25 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-amber-500/15 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(rgba(253,148,38,0.18)_1px,transparent_1px)] [background-size:24px_24px] opacity-40"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs font-semibold text-amber-200/80 mb-6">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
                <span>&rsaquo;</span>
                <a href="{{ url('/jurusan') }}" class="hover:text-white transition-colors">Jurusan</a>
                <span>&rsaquo;</span>
                <span class="text-[#FD9426]">Pemasaran &amp; Bisnis Digital (PMN)</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <!-- Text Intro -->
                <div class="lg:col-span-7 space-y-5">
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-[#FD9426]/20 text-[#FFB07C] border border-[#FD9426]/35 backdrop-blur-md">
                        <span class="w-2 h-2 rounded-full bg-[#FD9426] animate-pulse"></span>
                        <span>Bisnis Digital &bull; Social Commerce &bull; Terakreditasi A</span>
                    </span>

                    <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight">
                        Pemasaran &amp; <br class="hidden sm:inline">
                        <span class="bg-gradient-to-r from-[#FD9426] via-amber-300 to-[#FFB07C] bg-clip-text text-transparent">
                            Bisnis Digital Kreatif
                        </span>
                    </h1>

                    <p class="text-sm sm:text-base text-amber-100/90 leading-relaxed font-normal">
                        Mencetak talenta digital marketing, professional live streamer, content creator, dan technopreneur handal. Santri dilatih langsung menjalankan penjualan live streaming di TikTok Shop &amp; Shopee, iklan berbayar (Meta/Google Ads), SEO, dan manajemen ritel modern.
                    </p>

                    <!-- Skill Badges -->
                    <div class="pt-2 flex flex-wrap gap-2 text-xs font-mono-code">
                        <span class="px-3 py-1 rounded-lg bg-amber-950/80 text-[#FD9426] border border-amber-800">Live Streamer Host</span>
                        <span class="px-3 py-1 rounded-lg bg-amber-950/80 text-amber-200 border border-amber-800">Meta &amp; TikTok Ads</span>
                        <span class="px-3 py-1 rounded-lg bg-amber-950/80 text-orange-300 border border-amber-800">Google SEO Specialist</span>
                        <span class="px-3 py-1 rounded-lg bg-amber-950/80 text-yellow-300 border border-amber-800">Content Studio Creator</span>
                        <span class="px-3 py-1 rounded-lg bg-amber-950/80 text-rose-300 border border-amber-800">E-Commerce Management</span>
                        <span class="px-3 py-1 rounded-lg bg-amber-950/80 text-cyan-300 border border-amber-800">AI Business Copywriting</span>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="pt-4 flex flex-wrap items-center gap-3">
                        <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="px-6 py-3 rounded-full text-xs sm:text-sm font-bold text-slate-950 bg-gradient-to-r from-[#FD9426] to-amber-400 hover:from-amber-400 hover:to-amber-300 shadow-lg shadow-orange-500/25 transition-all hover:scale-105">
                            Daftar Pemasaran Sekarang &rarr;
                        </a>
                        <a href="#kurikulum" class="px-5 py-3 rounded-full text-xs sm:text-sm font-bold text-amber-100 bg-white/10 hover:bg-white/15 border border-white/20 transition-all">
                            Lihat Kurikulum &amp; Studio
                        </a>
                    </div>
                </div>

                <!-- Live Stream & E-Commerce Studio Mockup -->
                <div class="lg:col-span-5">
                    <div class="rounded-2xl bg-slate-950 border border-amber-900/60 shadow-2xl overflow-hidden text-xs">
                        <!-- Live Bar -->
                        <div class="px-4 py-3 bg-[#260e00] border-b border-amber-900/60 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-red-500 animate-pulse"></span>
                                <span class="bg-red-600 text-white font-extrabold text-[10px] px-2 py-0.5 rounded uppercase tracking-wider">LIVE ON AIR</span>
                                <span class="text-[11px] text-amber-200 font-bold">Wikrama Creator Studio</span>
                            </div>
                            <span class="text-amber-400 font-mono font-bold text-[11px]">👁️ 4.8K Viewers</span>
                        </div>
                        <!-- Live Simulation Container -->
                        <div class="p-5 space-y-3 bg-gradient-to-b from-[#1f0b00] to-slate-950">
                            <!-- Product Pin Banner -->
                            <div class="p-3 rounded-xl bg-amber-950/60 border border-amber-700/50 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-orange-500 text-white font-bold flex items-center justify-center text-lg shrink-0">
                                    🛍️
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[10px] text-amber-300 block font-semibold">Produk Unggulan TEFA Siswa</span>
                                    <h4 class="text-xs font-bold text-white truncate">Keripik Tempe &amp; Kopi Robusta Garut</h4>
                                    <span class="text-xs font-extrabold text-[#FD9426]">Rp 25.000 <span class="text-[10px] line-through text-slate-400">Rp 35.000</span></span>
                                </div>
                                <span class="px-2.5 py-1 bg-[#FD9426] text-slate-950 font-bold text-[10px] rounded-lg shrink-0">Beli</span>
                            </div>
                            <!-- Live Chat Stream -->
                            <div class="space-y-1.5 text-[11px] font-mono-code text-slate-300 max-h-32 overflow-hidden">
                                <p><span class="text-amber-300 font-bold">@sarah_shop:</span> Kak, bahannya renyah dan halal MUI kan?</p>
                                <p><span class="text-cyan-300 font-bold">@host_wikrama:</span> Halal 100% kak, sudah izin P-IRT dan BPOM ya!</p>
                                <p><span class="text-emerald-300 font-bold">@budi_tech:</span> Langsung checkout 3 pack kak, mantap santri garut!</p>
                                <p><span class="text-[#FD9426] font-bold">System:</span> Pesanan #WK-8902 baru saja dibayar via QRIS!</p>
                            </div>
                            <div class="pt-2 border-t border-amber-950 text-[11px] text-amber-300 flex items-center justify-between">
                                <span>&#10003; TikTok Shop &amp; Shopee Certified</span>
                                <span class="text-emerald-400 font-bold">Omzet: Rp 12.8M+ / Bulan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ── 2. STATS & KEY PILLARS STRIP ── -->
    <section class="py-12 bg-amber-50/80 border-y border-amber-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="p-5 rounded-2xl bg-white border border-amber-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-[#C2410C]">100%</span>
                    <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mt-1 block">Sertifikasi BNSP &amp; Google</span>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-amber-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-[#C2410C]">3 Studio</span>
                    <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mt-1 block">Live Stream, Ads Lab &amp; Retail</span>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-amber-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-[#C2410C]">2 Unit</span>
                    <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mt-1 block">TEFA Digital Mart &amp; Agency</span>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-amber-200 shadow-sm">
                    <span class="block text-3xl font-extrabold text-[#C2410C]">99%</span>
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
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-amber-900 bg-amber-100 border border-amber-200">
                        <span>Tentang Kompetensi Keahlian</span>
                    </div>
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Mengapa Memilih Jurusan <br>
                        <span class="text-[#C2410C]">Pemasaran Wikrama Garut?</span>
                    </h2>
                    <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                        Jurusan Pemasaran (Bisnis Digital) di {{ $schoolName }} mendobrak paradigma lama ilmu dagang konvensional. Siswa diajarkan menjadi <strong>digital growth marketer, live shopping creator, dan technopreneur</strong> yang mampu mendulang omzet sejak bangku sekolah.
                    </p>
                    <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                        Santri mempraktikkan langsung strategi <em>social commerce, paid traffic (Meta Ads &amp; TikTok Ads), optimasi mesin pencari Google (SEO), product visual copywriting</em>, serta pengelolaan point-of-sale ritel minimarket modern.
                    </p>

                    <div class="space-y-3 pt-2">
                        <div class="flex items-start gap-3 p-3.5 rounded-xl bg-amber-50/70 border border-amber-100">
                            <span class="p-2 rounded-lg bg-[#FD9426] text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Live Streaming Studio Berstandar Creator Agency</h4>
                                <p class="text-xs text-slate-600">Dilengkapi lighting profesional, soundcard multi-mic, dan kamera streaming beresolusi tinggi untuk praktik siaran komersial nyata.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3.5 rounded-xl bg-amber-50/70 border border-amber-100">
                            <span class="p-2 rounded-lg bg-[#FD9426] text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Praktik Beriklan Riil dengan Budget Nyata</h4>
                                <p class="text-xs text-slate-600">Siswa belajar membaca dashboard analitik ROAS (Return on Ad Spend), targeting audiens pixel, dan A/B testing konten iklan.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3.5 rounded-xl bg-amber-50/70 border border-amber-100">
                            <span class="p-2 rounded-lg bg-[#FD9426] text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Inkubasi Bisnis Mandiri Penghasil Omzet</h4>
                                <p class="text-xs text-slate-600">Santri dibimbing merilis brand produk sendiri, mengurus izin usaha NIB, dan menjual produk ke ribuan pembeli online.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visual Collage Showcase -->
                <div class="lg:col-span-6 grid grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div class="rounded-2xl overflow-hidden shadow-md border border-slate-200">
                            <img src="{{ asset('images/landing/jurusan-pmn.jpg') }}" alt="Studio Live Streaming PMN Wikrama" class="w-full h-48 sm:h-56 object-cover hover:scale-105 transition-all duration-300">
                        </div>
                        <div class="p-5 rounded-2xl bg-gradient-to-br from-[#3d1900] to-[#1a0900] text-white space-y-2">
                            <span class="text-[#FFB07C] text-xs font-bold uppercase tracking-wider">Unit Produksi TEFA</span>
                            <h4 class="text-base font-extrabold">Wikrama Digital Commerce</h4>
                            <p class="text-xs text-amber-200/90 leading-relaxed">Agensi live commerce yang mengelola etalase produk UMKM lokal Garut dan produk kreasi santri.</p>
                        </div>
                    </div>
                    <div class="space-y-4 pt-6">
                        <div class="p-5 rounded-2xl bg-[#FD9426] text-white space-y-2 shadow-lg shadow-orange-500/20">
                            <span class="text-amber-100 text-xs font-bold uppercase tracking-wider">Spesialisasi Modern</span>
                            <h4 class="text-base font-extrabold">Social Ads &amp; Creator Hub</h4>
                            <p class="text-xs text-amber-100 leading-relaxed">Laboratorium riset audiens digital marketing, kampanye influencer, dan optimasi SEO Google.</p>
                        </div>
                        <div class="rounded-2xl overflow-hidden shadow-md border border-slate-200">
                            <img src="{{ asset('images/landing/kelas-digital.jpg') }}" alt="Suasana Praktik Siswa Pemasaran" class="w-full h-48 sm:h-56 object-cover hover:scale-105 transition-all duration-300">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 4. PROSPEK KARIR & PELUANG PEKERJAAN ── -->
    <section class="py-20 bg-slate-900 text-white relative overflow-hidden" id="karir">
        <div class="absolute -top-32 -left-32 w-80 h-80 rounded-full bg-[#FD9426]/15 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 rounded-full bg-amber-500/15 blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-amber-300 bg-amber-950/80 border border-amber-800">
                    <span>Peluang Karir Global</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white mt-3">
                    Prospek Karir Lulusan <span class="bg-gradient-to-r from-[#FD9426] to-amber-300 bg-clip-text text-transparent">Pemasaran</span>
                </h2>
                <p class="text-slate-300 text-sm sm:text-base mt-3">
                    Era digitalisasi bisnis membuka ribuan lapangan kerja baru untuk spesialis e-commerce, live host, dan media buyer di berbagai brand ternama.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- 1. Digital Marketing Specialist -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-amber-500/20 hover:border-amber-500/60 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-[#FD9426] flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        📈
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Digital Marketing Specialist</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Merancang strategi kampanye promosi berbayar Meta Ads &amp; Google Ads, funneling penjualan, lead generation, dan optimasi conversion rate.
                    </p>
                    <div class="text-xs font-semibold text-amber-300">
                        <span>Range Gaji: Rp 6.000.000 - Rp 14.000.000</span>
                    </div>
                </div>

                <!-- 2. Professional Live Streamer Host -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-orange-500/20 hover:border-orange-500/60 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-orange-500/20 text-[#FFB07C] flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        🎙️
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Professional Live Streamer Host</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Memandu siaran langsung interaktif di TikTok Shop dan Shopee Live, presentasi produk persuasif, handling promo flash sale, dan closing penjualan.
                    </p>
                    <div class="text-xs font-semibold text-orange-300">
                        <span>Range Gaji: Rp 5.500.000 - Rp 15.000.000+</span>
                    </div>
                </div>

                <!-- 3. Social Media Content Creator -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-rose-500/20 hover:border-rose-500/60 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-rose-500/20 text-rose-300 flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        🎬
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Social Media Content Creator</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Riset tren viral FYP, produksi video pendek reels/TikTok bernilai jual tinggi, penulisan script storytelling, dan interaksi komunitas followers.
                    </p>
                    <div class="text-xs font-semibold text-rose-300">
                        <span>Range Gaji: Rp 5.000.000 - Rp 12.000.000</span>
                    </div>
                </div>

                <!-- 4. E-Commerce Marketplace Specialist -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-yellow-500/20 hover:border-yellow-500/60 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-yellow-500/20 text-yellow-300 flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        🛒
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Marketplace Operations Manager</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Mengelola official store Shopee, Tokopedia, dan Lazada, optimasi judul produk, dekorasi toko online, pengelolaan stok inventaris, dan kepuasan pelanggan.
                    </p>
                    <div class="text-xs font-semibold text-yellow-300">
                        <span>Range Gaji: Rp 5.500.000 - Rp 12.500.000</span>
                    </div>
                </div>

                <!-- 5. Search Engine Optimization (SEO) Specialist -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-cyan-500/20 hover:border-cyan-500/60 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/20 text-cyan-300 flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        🔍
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">SEO &amp; Inbound Growth Specialist</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Menaikkan peringkat website bisnis di halaman pertama pencarian Google secara organik melalui riset keyword, optimasi on-page/off-page, dan backlink.
                    </p>
                    <div class="text-xs font-semibold text-cyan-300">
                        <span>Range Gaji: Rp 6.000.000 - Rp 13.500.000</span>
                    </div>
                </div>

                <!-- 6. Online Brand Technopreneur -->
                <div class="p-6 rounded-2xl bg-slate-800/80 border border-emerald-500/20 hover:border-emerald-500/60 hover:bg-slate-800 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center font-bold text-xl mb-4 group-hover:scale-110 transition-transform">
                        💼
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Online Brand Owner &amp; Founder</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Membangun brand produk retail/fashion/kuliner sendiri, mengelola tim marketing independen, dan menjual ke pasar nasional hingga ekspor internasional.
                    </p>
                    <div class="text-xs font-semibold text-emerald-300">
                        <span>Penghasilan: Mandiri &amp; Tak Terbatas</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 5. KURIKULUM 3 TAHUN (FASE E & FASE F) ── -->
    <section id="kurikulum" class="py-20 bg-amber-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-amber-900 bg-amber-100 border border-amber-200">
                    <span>Roadmap Pembelajaran</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 mt-3">
                    Kurikulum 3 Tahun Berstandar <span class="text-[#C2410C]">Industri Digital Marketing</span>
                </h2>
                <p class="text-slate-600 text-sm sm:text-base mt-3">
                    Disusun bersama agensi digital marketing nasional dan marketplace terkemuka untuk melatih kecakapan bisnis nyata.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- KELAS X -->
                <div class="bg-white rounded-3xl p-7 border-2 border-amber-200 shadow-sm relative hover:border-[#FD9426] transition-all">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-800 font-bold flex items-center justify-center text-sm mb-4">
                        X
                    </div>
                    <span class="text-xs font-bold uppercase text-orange-800 tracking-wider">Fase E &bull; Tahun Pertama</span>
                    <h3 class="text-xl font-bold text-slate-900 mt-1 mb-4">Dasar Bisnis &amp; Personal Selling</h3>
                    <p class="text-xs text-slate-600 mb-6">Membangun karakter sales profesional, riset perilaku konsumen, dan komunikasi persuasif.</p>

                    <ul class="space-y-3 text-xs text-slate-700">
                        <li class="flex items-start gap-2">
                            <span class="text-orange-800 font-bold">&check;</span>
                            <span>Prinsip Dasar Ekonomi Bisnis &amp; Administrasi Transaksi</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-800 font-bold">&check;</span>
                            <span>Teknik Negosiasi &amp; Komunikasi Bisnis Efektif</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-800 font-bold">&check;</span>
                            <span>Desain Grafis Promosi Produk (Canva &amp; Photoshop)</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-800 font-bold">&check;</span>
                            <span>Psikologi Konsumen &amp; Copywriting Dasar</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-800 font-bold">&check;</span>
                            <span>Praktek Kasir &amp; Pengelolaan Ritel Modern</span>
                        </li>
                    </ul>
                </div>

                <!-- KELAS XI -->
                <div class="bg-white rounded-3xl p-7 border-2 border-[#FD9426] shadow-lg relative ring-2 ring-[#FD9426]/20">
                    <div class="absolute -top-3.5 right-6 px-3 py-1 rounded-full text-[11px] font-extrabold bg-[#C2410C] text-white uppercase tracking-wider shadow">
                        Spesialisasi Commerce
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-[#FD9426] to-amber-600 text-white font-bold flex items-center justify-center text-sm mb-4">
                        XI
                    </div>
                    <span class="text-xs font-bold uppercase text-orange-800 tracking-wider">Fase F &bull; Tahun Kedua</span>
                    <h3 class="text-xl font-bold text-slate-900 mt-1 mb-4">E-Commerce, Ads &amp; Live Stream</h3>
                    <p class="text-xs text-slate-600 mb-6">Penerapan strategi penjualan digital, iklan berbayar media sosial, dan penyiaran live commerce.</p>

                    <ul class="space-y-3 text-xs text-slate-700">
                        <li class="flex items-start gap-2">
                            <span class="text-orange-800 font-bold">&check;</span>
                            <span>Operasional Marketplace (Shopee, Tokopedia, TikTok Shop)</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-800 font-bold">&check;</span>
                            <span>Teknik Live Streaming Host &amp; Penataan Studio Visual</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-800 font-bold">&check;</span>
                            <span>Iklan Berbayar Meta Ads &amp; TikTok Ads Manager</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-800 font-bold">&check;</span>
                            <span>Optimasi Search Engine Optimization (SEO) Google</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-800 font-bold">&check;</span>
                            <span>Operasional TEFA: Wikrama Digital Commerce</span>
                        </li>
                    </ul>
                </div>

                <!-- KELAS XII -->
                <div class="bg-white rounded-3xl p-7 border-2 border-amber-200 shadow-sm relative hover:border-[#FD9426] transition-all">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-800 font-bold flex items-center justify-center text-sm mb-4">
                        XII
                    </div>
                    <span class="text-xs font-bold uppercase text-orange-800 tracking-wider">Fase F &bull; Tahun Ketiga</span>
                    <h3 class="text-xl font-bold text-slate-900 mt-1 mb-4">Magang 6 Bulan &amp; Sertifikasi BNSP</h3>
                    <p class="text-xs text-slate-600 mb-6">Penerjunan langsung di agensi pemasaran digital, sertifikasi profesi nasional, dan inkubasi usaha.</p>

                    <ul class="space-y-3 text-xs text-slate-700">
                        <li class="flex items-start gap-2">
                            <span class="text-orange-800 font-bold">&check;</span>
                            <span>PKL Industri 6 Bulan di Agensi Pemasaran / Retail</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-800 font-bold">&check;</span>
                            <span>Uji Sertifikasi BNSP Tenaga Pemasar Operasional</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-800 font-bold">&check;</span>
                            <span>Sertifikasi Google Digital Garage &amp; Meta Associate</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-800 font-bold">&check;</span>
                            <span>Pameran Karya Bisnis &amp; Business Pitching Final</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-800 font-bold">&check;</span>
                            <span>Penyaluran BMW (Bekerja, Melanjutkan, Wirausaha)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 6. KOLASE RUANGAN PRAKTIK & STUDIO BISNIS ── -->
    <section class="py-20 bg-white" id="lab">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-amber-900 bg-amber-100 border border-amber-200">
                    <span>Fasilitas Standar Creator Agency</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 mt-3">
                    Kolase Studio &amp; <span class="text-[#C2410C]">Ruang Praktik Bisnis Nyata</span>
                </h2>
                <p class="text-slate-600 text-sm sm:text-base mt-3">
                    Seluruh ruang praktik dirancang untuk mensimulasikan operasional agensi periklanan, live shopping hub, dan toko retail sesungguhnya.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Fasilitas 1 -->
                <div class="rounded-2xl overflow-hidden border border-amber-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/landing/jurusan-pmn.jpg') }}" alt="Studio Live Streaming PMN" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-[#FD9426] text-slate-950">Live Creator Studio</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Studio Live Streaming TikTok &amp; Shopee</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Dilengkapi ring light, softbox 3-titik, standing mic condenser, green screen, dan smartphone flagship khusus siaran langsung e-commerce.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 2 -->
                <div class="rounded-2xl overflow-hidden border border-amber-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/landing/kelas-digital.jpg') }}" alt="Lab Ads & SEO Analytics" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-700 text-white">Ads &amp; SEO Lab</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Laboratorium Ads Analytics &amp; SEO</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Komputer PC untuk riset keyword Ahrefs/Semrush, setting Meta Business Manager, Google Analytics, dan optimasi toko marketplace.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 3 -->
                <div class="rounded-2xl overflow-hidden border border-amber-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/web/activity-learning.jpg') }}" alt="Wikrama Mini Mart POS" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-orange-700 text-white">Retail POS Center</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Wikrama Retail &amp; POS Cashier Mart</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Mesin kasir barcode scanner, software POS terintegrasi, rak display gondola, dan sistem stock opname barang minimarket modern.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 4 -->
                <div class="rounded-2xl overflow-hidden border border-amber-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/landing/prestasi-seminar-siswa.jpg') }}" alt="Product Photo Studio" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-rose-700 text-white">Product Photo Studio</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Studio Fotografi &amp; Videografi Produk</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Lightbox mini, alas putar 360 derajat, lensa makro, dan perlengkapan styling produk kuliner/fashion untuk katalog jualan visual menarik.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 5 -->
                <div class="rounded-2xl overflow-hidden border border-amber-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/web/activity-students.jpg') }}" alt="Packaging & Packing Hub" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-yellow-700 text-slate-950">Packaging Center</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Logistik &amp; Packing Fulfillment Center</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Meja pengemasan pesanan, timbangan digital, printer label resi thermal, bubble wrap dispenser, dan integrasi kurir ekspedisi.
                        </p>
                    </div>
                </div>

                <!-- Fasilitas 6 -->
                <div class="rounded-2xl overflow-hidden border border-amber-200 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/landing/budaya-moving-class.jpeg') }}" alt="Business Pitching Room" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-700 text-white">Pitching &amp; Deal Room</span>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-slate-900 text-base mb-1">Business Presentation &amp; Pitching Room</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Ruang negosiasi bisnis dengan klien UMKM, presentasi proposal sponsorship kegiatan, dan pitching produk di depan dewan penguji.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 7. SERTIFIKASI KOMPETENSI RESMI (BNSP & GOOGLE) ── -->
    <section class="py-20 bg-gradient-to-br from-[#3d1900] via-[#2d1200] to-[#1a0900] text-white" style="background: linear-gradient(135deg, #3d1900 0%, #2d1200 50%, #1a0900 100%);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-amber-300 bg-amber-950/80 border border-amber-800">
                    <span>Pengakuan Industri Berstandar Nasional &amp; Global</span>
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white mt-3">
                    Sertifikasi Kompetensi Resmi <span class="bg-gradient-to-r from-[#FD9426] to-amber-300 bg-clip-text text-transparent">Pemasaran</span>
                </h2>
                <p class="text-amber-100/90 text-sm sm:text-base mt-3">
                    Setiap lulusan dibekali sertifikat kompetensi berlogo Garuda BNSP dan sertifikasi digital marketing dari Google serta Meta.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Sertifikasi 1 -->
                <div class="p-6 rounded-2xl bg-white/5 border border-amber-500/20 backdrop-blur-md space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-[#FD9426] flex items-center justify-center font-bold text-xl">
                        🎖️
                    </div>
                    <h3 class="text-lg font-bold text-white">BNSP Tenaga Pemasar Operasional</h3>
                    <p class="text-xs text-amber-100/80 leading-relaxed">
                        Sertifikasi resmi negara dari BNSP untuk kompetensi penetapan harga, pengelolaan etalase, pelayanan pelanggan, dan teknik negosiasi bisnis.
                    </p>
                    <div class="pt-2 text-xs font-semibold text-amber-300">
                        Diterbitkan oleh: BNSP LSP P-1
                    </div>
                </div>

                <!-- Sertifikasi 2 -->
                <div class="p-6 rounded-2xl bg-white/5 border border-orange-500/20 backdrop-blur-md space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-orange-500/20 text-[#FFB07C] flex items-center justify-center font-bold text-xl">
                        🌐
                    </div>
                    <h3 class="text-lg font-bold text-white">Google Digital Garage Certification</h3>
                    <p class="text-xs text-amber-100/80 leading-relaxed">
                        Sertifikasi internasional dari Google yang mencakup dasar-dasar digital marketing, optimasi mesin pencari Google SEO, dan analitik data web.
                    </p>
                    <div class="pt-2 text-xs font-semibold text-orange-300">
                        Standar: Google Digital Skills Academy
                    </div>
                </div>

                <!-- Sertifikasi 3 -->
                <div class="p-6 rounded-2xl bg-white/5 border border-rose-500/20 backdrop-blur-md space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-rose-500/20 text-rose-300 flex items-center justify-center font-bold text-xl">
                        📱
                    </div>
                    <h3 class="text-lg font-bold text-white">Meta Certified Digital Associate</h3>
                    <p class="text-xs text-amber-100/80 leading-relaxed">
                        Pengakuan kompetensi resmi ekosistem Meta dalam mengelola halaman Instagram &amp; Facebook bisnis, strategi konten iklan, dan ads targeting.
                    </p>
                    <div class="pt-2 text-xs font-semibold text-rose-300">
                        Mitra: Meta Blueprint Certification
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 8. MITRA DUDI & TEMPAT MAGANG ── -->
    <section class="py-16 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-bold text-orange-800 uppercase tracking-wider block">Jaringan Industri E-Commerce &amp; Ritel</span>
                <h3 class="text-xl sm:text-2xl font-bold text-slate-900 mt-1">Mitra PKL &amp; Rekrutmen Lulusan</h3>
                <p class="text-xs text-slate-600 mt-2">Siswa menjalani magang nyata selama 6 bulan di marketplace, agensi periklanan, dan jaringan retail modern nasional.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4 items-center">
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">Shopee Indonesia</span>
                    <span class="text-[10px] text-slate-500">Marketplace Terbesar</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">TikTok Shop</span>
                    <span class="text-[10px] text-slate-500">Live Creator Partner</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">Alfamart (SAT)</span>
                    <span class="text-[10px] text-slate-500">Jaringan Ritel Modern</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">Indomaret (IDM)</span>
                    <span class="text-[10px] text-slate-500">Ritel &amp; Point of Sale</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">J&amp;T / SiCepat</span>
                    <span class="text-[10px] text-slate-500">Logistik E-Commerce</span>
                </div>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-center shadow-sm">
                    <span class="font-bold text-slate-800 text-sm block">Digital Agency Jkt</span>
                    <span class="text-[10px] text-slate-500">Performance Marketing</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 9. TESTIMONI ALUMNI PEMASARAN ── -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto bg-gradient-to-r from-[#3d1900] to-[#1a0900] rounded-3xl p-8 sm:p-12 text-white border border-amber-500/30 shadow-xl relative overflow-hidden" style="background: linear-gradient(90deg, #3d1900 0%, #1a0900 100%);">
                <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-[#FD9426]/20 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-8 relative z-10">
                    <img src="{{ asset('images/web/alumni-auriel.png') }}" alt="Alumni Pemasaran Wikrama" class="w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover border-4 border-[#FD9426] shadow-lg shrink-0">
                    <div class="space-y-3 text-center sm:text-left">
                        <div class="flex items-center justify-center sm:justify-start gap-1 text-amber-400">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-sm sm:text-base text-amber-100 italic leading-relaxed">
                            "Di Pemasaran SMK Wikrama 1 Garut, kami diajari ilmu yang relevan banget dengan zaman sekarang: live streaming TikTok, setting iklan Meta Ads, dan branding produk. Saya terlatih berbicara percaya diri di depan kamera, sehingga saat magang langsung ditunjuk jadi Head Live Streamer brand fashion dengan penghasilan belasan juta per bulan!"
                        </p>
                        <div>
                            <h4 class="font-bold text-white text-base">Auriel Salsabila</h4>
                            <span class="text-xs text-[#FFB07C]">Alumni Pemasaran &bull; Live Stream Host &amp; Social Commerce Lead</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 10. FAQ SEPUTAR JURUSAN PEMASARAN ── -->
    <section class="py-20 bg-amber-50/50" id="faq">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-xs font-bold text-orange-800 uppercase tracking-wider block">Pertanyaan Umum</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">FAQ Jurusan Pemasaran</h2>
            </div>

            <div class="space-y-4">
                <div class="p-5 rounded-2xl bg-white border border-amber-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Apakah jurusan Pemasaran hanya belajar berjualan keliling?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Sama sekali tidak! Pemasaran di Wikrama berfokus pada <strong>Bisnis Digital (Digital Marketing)</strong>, optimasi iklan berbayar online (Meta/TikTok Ads), optimasi mesin pencari Google (SEO), dan siaran live streaming studio profesional.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-amber-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Saya orangnya pemalu, apakah bisa mengikuti jurusan ini?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Sangat bisa! Di jurusan ini ada bimbingan public speaking bertahap dari dasar, pelatihan grooming, dan olah vokal. Banyak santri yang awalnya pendiam justru bertransformasi menjadi host live shopping yang luwes dan penuh percaya diri.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-amber-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Apakah siswa harus mengeluarkan modal uang pribadi untuk berbisnis?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Tidak perlu modal besar. Sekolah menyediakan produk dari unit Teaching Factory (TEFA) dan UMKM mitra sekolah untuk dipasarkan oleh siswa dengan sistem dropship dan affiliate marketing, sehingga siswa bisa menghasilkan komisi tanpa modal stok barang.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-amber-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Apakah lulusan Pemasaran bisa melanjutkan kuliah ke Perguruan Tinggi?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Tentu bisa! Lulusan dapat melanjutkan ke jurusan Manajemen Bisnis, Ilmu Komunikasi, Administrasi Niaga, Digital Marketing, atau Akuntansi di PTN/PTS ternama seperti UNPAD, UPI, Telkom University, dan Universitas Indonesia.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 11. CTA DAFTAR PEMASARAN ── -->
    <section class="py-20 bg-gradient-to-r from-[#3d1900] via-[#5c2400] to-[#FD9426] text-white relative overflow-hidden" style="background: linear-gradient(90deg, #3d1900 0%, #5c2400 50%, #FD9426 100%);">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-6">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-white/20 text-[#FFB07C] border border-white/30">
                Penerimaan Peserta Didik Baru (SPMB)
            </span>
            <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight">
                Siap Menjadi Digital Marketer &amp; <br class="hidden sm:inline">
                <span class="bg-gradient-to-r from-[#FFB07C] via-amber-200 to-white bg-clip-text text-transparent">Creator E-Commerce Masa Depan?</span>
            </h2>
            <p class="text-amber-100/90 text-sm sm:text-base max-w-2xl mx-auto">
                Kuota kelas Pemasaran terbatas demi menjaga intensitas bimbingan praktik studio live streaming dan proyek inkubasi usaha. Daftarkan dirimu hari ini!
            </p>
            <div class="pt-4 flex flex-wrap items-center justify-center gap-4">
                <a href="https://spmb.smkwikrama1garut.sch.id/" target="_blank" rel="noopener" class="px-8 py-3.5 rounded-full text-sm font-bold text-slate-950 bg-gradient-to-r from-[#FFB07C] to-amber-300 hover:from-amber-300 hover:to-white shadow-xl shadow-orange-500/25 transition-all hover:scale-105">
                    Daftar SPMB Online Sekarang &rarr;
                </a>
                <a href="{{ url('/jurusan') }}" class="px-6 py-3.5 rounded-full text-sm font-bold text-white bg-white/10 hover:bg-white/15 border border-white/20 transition-all">
                    &larr; Lihat Jurusan Lainnya
                </a>
            </div>
        </div>
    </section>
</x-layouts.blank>
