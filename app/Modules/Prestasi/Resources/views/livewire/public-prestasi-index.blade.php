<div class="min-h-screen bg-[#F8FAFC] text-slate-800 antialiased selection:bg-[#00519E] selection:text-white pb-24"
     x-data="{}"
     x-on:update-url.window="window.history.pushState({}, '', $event.detail.url)">

    @php
        $tabTitles = [
            'semua'   => [
                'tag' => '🏆 Etalase Prestasi & Capaian Resmi • Terverifikasi',
                'title_prefix' => 'Merayakan Bakat',
                'title_gradient' => 'Mengukir Prestasi Gemilang',
                'subtitle' => 'Dokumentasi rekam jejak juara dan penghargaan resmi yang diraih oleh peserta didik, pendidik inspiratif, dan institusi ' . ($sekolah->nama ?? 'SMK Wikrama 1 Garut') . ' di kancah daerah, nasional, hingga internasional.'
            ],
            'sekolah' => [
                'tag' => '🏛️ Prestasi Institusi • Akreditasi & Kelembagaan',
                'title_prefix' => 'Keunggulan Mutu',
                'title_gradient' => 'Prestasi Resmi Institusi',
                'subtitle' => 'Pengakuan mutu kelembagaan, akreditasi unggul, dan apresiasi bergengsi institusi ' . ($sekolah->nama ?? 'SMK Wikrama 1 Garut') . ' dari kementerian, dinas pendidikan, dan mitra industri global.'
            ],
            'guru'    => [
                'tag' => '👨‍🏫 Prestasi Pendidik • Teladan & Inspirasi',
                'title_prefix' => 'Dedikasi Terbaik',
                'title_gradient' => 'Karya Nyata Guru Inspiratif',
                'subtitle' => 'Inovasi pembelajaran, kompetensi kejuruan, dan capaian juara para guru serta tenaga kependidikan dalam mencetak generasi vokasi unggul berakhlak mulia.'
            ],
            'siswa'   => [
                'tag' => '🎓 Prestasi Peserta Didik • Generasi Emas',
                'title_prefix' => 'Bakat Unggul',
                'title_gradient' => 'Jejak Juara Siswa & Santri',
                'subtitle' => 'Kiprah siswa dan santri Wikrama dalam merebut medali emas, trofi kejuaraan, dan sertifikasi bergengsi di bidang teknologi informasi, bisnis, perhotelan, sains, seni, dan olahraga.'
            ],
        ];
        $currentTabInfo = $tabTitles[$tab] ?? $tabTitles['semua'];
    @endphp

    <!-- ── 1. HERO HEADER: 2-COLUMN DEEP MIDNIGHT ROYAL NAVY & LUMINOUS GOLD ── -->
    <header class="relative text-white overflow-hidden shadow-2xl"
            style="background: linear-gradient(135deg, #001733 0%, #002244 50%, #00305F 100%);">
        
        <!-- Technical Star/Dot Grid (No Foggy Blur Blobs) -->
        <div class="absolute inset-0 pointer-events-none z-0 opacity-15"
             style="background-image: radial-gradient(#FFFFFF 1.2px, transparent 1.2px); background-size: 28px 28px;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pt-8 sm:pt-12 pb-20 sm:pb-24">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs font-semibold text-blue-200/80 mb-6 sm:mb-8">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
                <span>&rsaquo;</span>
                <a href="{{ url('/prestasi') }}" class="hover:text-white transition-colors">Prestasi</a>
                @if($tab !== 'semua')
                    <span>&rsaquo;</span>
                    <span class="text-[#F5D84A] font-bold capitalize">{{ $tab }}</span>
                @endif
            </nav>

            <!-- 2-Column Responsive Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <!-- Left Column: Teks Rata Kiri (lg:col-span-7) -->
                <div class="lg:col-span-7 space-y-5 text-left">
                    <!-- Pill Badge: Solid & Glowing -->
                    <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider shadow-md bg-white/10 border border-white/20 text-[#F5D84A] backdrop-blur-md">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#F5D84A] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#F5D84A]"></span>
                        </span>
                        <span>{{ $currentTabInfo['tag'] }}</span>
                    </div>

                    <!-- Main Hero Heading: 100% Solid & High-Contrast (No Washed-Out Transparent Gradient) -->
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-5xl font-black tracking-tight text-white leading-tight">
                        {{ $currentTabInfo['title_prefix'] }}, <br>
                        <span class="text-[#F5D84A] drop-shadow-[0_2px_14px_rgba(245,216,74,0.35)]">
                            {{ $currentTabInfo['title_gradient'] }}
                        </span>
                    </h1>

                    <!-- Subtitle: High Contrast Legible -->
                    <p class="text-sm sm:text-base text-blue-100/90 leading-relaxed font-normal max-w-xl">
                        {{ $currentTabInfo['subtitle'] }}
                    </p>

                    <!-- Action CTA & Institutional Badges -->
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="#daftar-prestasi" class="btn-jis-pill-gold text-xs py-3 px-6 shadow-lg shadow-amber-400/20">
                            <span>🏆 Jelajahi Capaian Juara</span>
                            <span class="text-sm font-black">&darr;</span>
                        </a>
                        <div class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-full bg-white/10 border border-white/20 text-blue-100 text-xs font-semibold backdrop-blur-md">
                            <span class="text-amber-300">⭐</span>
                            <span>Akreditasi A Unggul &bull; SMK PK</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Gambar Siswa / Mockup Piala Emas (lg:col-span-5) -->
                <div class="lg:col-span-5 relative flex justify-center lg:justify-end">
                    @php
                        $hasCustomStudentImage = file_exists(public_path('images/landing/hero-prestasi-siswa.png')) 
                            ? asset('images/landing/hero-prestasi-siswa.png') 
                            : (file_exists(public_path('images/landing/hero-prestasi-siswa.webp')) 
                                ? asset('images/landing/hero-prestasi-siswa.webp') 
                                : null);
                    @endphp

                    @if($hasCustomStudentImage)
                        <!-- Gambar Kustom PNG/WebP Siswa Berprestasi -->
                        <div class="relative w-full max-w-md">
                            <div class="absolute inset-0 bg-radial from-amber-400/20 via-blue-500/10 to-transparent blur-2xl rounded-full transform scale-90"></div>
                            <img src="{{ $hasCustomStudentImage }}" alt="Siswa Berprestasi SMK Wikrama 1 Garut" class="relative z-10 w-full h-auto max-h-[380px] object-contain drop-shadow-[0_15px_30px_rgba(0,0,0,0.6)]">
                        </div>
                    @else
                        <!-- Visual Mockup Ilustrasi SVG Piala Emas Kejuaraan (Siap Diganti File PNG/WebP) -->
                        <div class="relative w-full max-w-sm sm:max-w-md p-6 sm:p-8 rounded-3xl border border-white/20 backdrop-blur-md shadow-2xl overflow-hidden"
                             style="background: linear-gradient(145deg, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0.03) 100%);">
                            
                            <!-- Floating Pill Left -->
                            <div class="absolute top-4 left-4 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400/20 border border-amber-300/40 text-[#F5D84A] text-[10px] font-black shadow-md backdrop-blur-md">
                                <span>🥇</span>
                                <span>Juara Nasional</span>
                            </div>

                            <!-- Floating Pill Right -->
                            <div class="absolute top-4 right-4 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-400/20 border border-emerald-300/40 text-emerald-300 text-[10px] font-black shadow-md backdrop-blur-md">
                                <span>✨</span>
                                <span>Terverifikasi</span>
                            </div>

                            <!-- Trophy SVG -->
                            <div class="relative z-10 flex flex-col items-center text-center mt-6">
                                <div class="w-32 h-32 sm:w-40 sm:h-40 relative my-2 flex items-center justify-center">
                                    <svg viewBox="0 0 128 128" class="w-28 h-28 sm:w-36 sm:h-36 drop-shadow-[0_10px_25px_rgba(245,216,74,0.45)]" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <!-- Cup Bowl -->
                                        <path d="M38 24H90V54C90 68.3594 78.3594 80 64 80C49.6406 80 38 68.3594 38 54V24Z" fill="url(#goldGrad)" stroke="#F5D84A" stroke-width="2.5"/>
                                        <!-- Cup Base & Stem -->
                                        <path d="M58 80H70V96H58V80Z" fill="url(#goldGradDark)" stroke="#E2B714" stroke-width="2"/>
                                        <path d="M42 96H86L92 110H36L42 96Z" fill="url(#goldGradDark)" stroke="#E2B714" stroke-width="2"/>
                                        <!-- Left Handle -->
                                        <path d="M38 32H28C22.4772 32 18 36.4772 18 42V48C18 56.8366 25.1634 64 34 64H38" stroke="#F5D84A" stroke-width="3" stroke-linecap="round"/>
                                        <!-- Right Handle -->
                                        <path d="M90 32H100C105.523 32 110 36.4772 110 42V48C110 56.8366 102.837 64 94 64H90" stroke="#F5D84A" stroke-width="3" stroke-linecap="round"/>
                                        <!-- Star inside cup -->
                                        <path d="M64 36L67.5 44H76L69 49L72 57L64 52L56 57L59 49L52 44H60.5L64 36Z" fill="#FFF7CC"/>
                                        <!-- Ribbon Base -->
                                        <path d="M32 110C42 116 86 116 96 110" stroke="#F5D84A" stroke-width="2.5" stroke-linecap="round"/>
                                        <defs>
                                            <linearGradient id="goldGrad" x1="38" y1="24" x2="90" y2="80" gradientUnits="userSpaceOnUse">
                                                <stop stop-color="#FFF3B0"/>
                                                <stop offset="0.4" stop-color="#F5D84A"/>
                                                <stop offset="1" stop-color="#D97706"/>
                                            </linearGradient>
                                            <linearGradient id="goldGradDark" x1="42" y1="80" x2="86" y2="110" gradientUnits="userSpaceOnUse">
                                                <stop stop-color="#F5D84A"/>
                                                <stop offset="1" stop-color="#92400E"/>
                                            </linearGradient>
                                        </defs>
                                    </svg>
                                </div>

                                <div class="mt-2 space-y-1">
                                    <span class="text-xs font-black uppercase tracking-wider text-white block">
                                        Etalase Juara Berprestasi
                                    </span>
                                    <span class="text-[11px] text-blue-200/80 block">
                                        Slot gambar siswa memegang piala (PNG/WebP)
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- ── 2. SUMMARY METRIC PASTEL CARDS: FLOATING DOCK BRIDGING HERO & CONTENT ── -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-30 -mt-10 sm:-mt-12">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- 1. Total Prestasi (Warm Amber Gold) -->
            <div class="p-5 sm:p-6 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1.5 group"
                 style="background-color: #FFFBEB; border: 2px solid #FCD34D;">
                <div class="flex items-center justify-between mb-3">
                    <span class="w-11 h-11 rounded-2xl flex items-center justify-center text-2xl shadow-xs group-hover:scale-110 transition-transform"
                          style="background-color: #FEF08A; color: #78350F;">
                        🏆
                    </span>
                    <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full"
                          style="background-color: #FEF08A; color: #78350F; border: 1px solid #FDE047;">TOTAL</span>
                </div>
                <div class="text-3xl sm:text-4xl font-black tracking-tight" style="color: #78350F;">{{ $stats['total'] }}</div>
                <div class="text-xs font-bold mt-1" style="color: #92400E;">Seluruh Capaian Juara</div>
            </div>

            <!-- 2. Prestasi Sekolah (Royal Purple) -->
            <div class="p-5 sm:p-6 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1.5 group"
                 style="background-color: #FAF5FF; border: 2px solid #C4B5FD;">
                <div class="flex items-center justify-between mb-3">
                    <span class="w-11 h-11 rounded-2xl flex items-center justify-center text-2xl shadow-xs group-hover:scale-110 transition-transform"
                          style="background-color: #DDD6FE; color: #581C87;">
                        🏛️
                    </span>
                    <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full"
                          style="background-color: #DDD6FE; color: #581C87; border: 1px solid #C4B5FD;">INSTITUSI</span>
                </div>
                <div class="text-3xl sm:text-4xl font-black tracking-tight" style="color: #581C87;">{{ $stats['sekolah'] }}</div>
                <div class="text-xs font-bold mt-1" style="color: #6B21A8;">Prestasi Sekolah &amp; Akreditasi</div>
            </div>

            <!-- 3. Prestasi Guru (Sky Blue) -->
            <div class="p-5 sm:p-6 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1.5 group"
                 style="background-color: #F0F9FF; border: 2px solid #7DD3FC;">
                <div class="flex items-center justify-between mb-3">
                    <span class="w-11 h-11 rounded-2xl flex items-center justify-center text-2xl shadow-xs group-hover:scale-110 transition-transform"
                          style="background-color: #BAE6FD; color: #0C4A6E;">
                        👨‍🏫
                    </span>
                    <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full"
                          style="background-color: #BAE6FD; color: #0C4A6E; border: 1px solid #7DD3FC;">PENDIDIK</span>
                </div>
                <div class="text-3xl sm:text-4xl font-black tracking-tight" style="color: #0C4A6E;">{{ $stats['guru'] }}</div>
                <div class="text-xs font-bold mt-1" style="color: #0369A1;">Prestasi Guru &amp; Tendik</div>
            </div>

            <!-- 4. Prestasi Siswa (Mint Emerald) -->
            <div class="p-5 sm:p-6 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1.5 group"
                 style="background-color: #ECFDF5; border: 2px solid #6EE7B7;">
                <div class="flex items-center justify-between mb-3">
                    <span class="w-11 h-11 rounded-2xl flex items-center justify-center text-2xl shadow-xs group-hover:scale-110 transition-transform"
                          style="background-color: #A7F3D0; color: #064E3B;">
                        🎓
                    </span>
                    <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full"
                          style="background-color: #A7F3D0; color: #064E3B; border: 1px solid #6EE7B7;">SANTRI</span>
                </div>
                <div class="text-3xl sm:text-4xl font-black tracking-tight" style="color: #064E3B;">{{ $stats['siswa'] }}</div>
                <div class="text-xs font-bold mt-1" style="color: #047857;">Prestasi Siswa Terverifikasi</div>
            </div>
        </div>
    </section>

    <!-- ── 3. INTERACTIVE TAB NAVIGATION & FILTER BAR ── -->
    <section id="daftar-prestasi" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 mt-8 mb-10">
        <div class="bg-white/95 backdrop-blur-md rounded-3xl p-4 sm:p-6 border border-slate-200/80 shadow-md space-y-4">
            
            <!-- Category Tabs (Semua, Sekolah, Guru, Siswa) -->
            <div class="flex items-center gap-2.5 overflow-x-auto pb-2 sm:pb-0 scrollbar-none border-b border-slate-100">
                <!-- Tab Semua -->
                <a href="{{ url('/prestasi') }}" wire:click.prevent="setTab('semua')"
                   class="px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all shrink-0 flex items-center gap-2 {{ $tab === 'semua' ? 'bg-[#00519E] text-white border border-[#00305F] shadow-sm shadow-blue-900/20' : 'bg-white text-slate-700 border border-slate-200/90 hover:bg-slate-50 hover:border-slate-300' }}">
                    <span>✨ Semua Capaian</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $tab === 'semua' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700 border border-slate-200/60' }}">
                        {{ $stats['total'] }}
                    </span>
                </a>

                <!-- Tab Sekolah -->
                <a href="{{ url('/prestasi/sekolah') }}" wire:click.prevent="setTab('sekolah')"
                   class="px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all shrink-0 flex items-center gap-2 {{ $tab === 'sekolah' ? 'bg-[#7C3AED] text-white border border-[#6D28D9] shadow-sm shadow-purple-900/20' : 'bg-white text-slate-700 border border-slate-200/90 hover:bg-slate-50 hover:border-slate-300' }}">
                    <span>🏛️ Prestasi Sekolah</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $tab === 'sekolah' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700 border border-slate-200/60' }}">
                        {{ $stats['sekolah'] }}
                    </span>
                </a>

                <!-- Tab Guru -->
                <a href="{{ url('/prestasi/guru') }}" wire:click.prevent="setTab('guru')"
                   class="px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all shrink-0 flex items-center gap-2 {{ $tab === 'guru' ? 'bg-[#0284C7] text-white border border-[#0369A1] shadow-sm shadow-sky-900/20' : 'bg-white text-slate-700 border border-slate-200/90 hover:bg-slate-50 hover:border-slate-300' }}">
                    <span>👨‍🏫 Guru &amp; Tendik</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $tab === 'guru' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700 border border-slate-200/60' }}">
                        {{ $stats['guru'] }}
                    </span>
                </a>

                <!-- Tab Siswa -->
                <a href="{{ url('/prestasi/siswa') }}" wire:click.prevent="setTab('siswa')"
                   class="px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all shrink-0 flex items-center gap-2 {{ $tab === 'siswa' ? 'bg-[#059669] text-white border border-[#047857] shadow-sm shadow-emerald-900/20' : 'bg-white text-slate-700 border border-slate-200/90 hover:bg-slate-50 hover:border-slate-300' }}">
                    <span>🎓 Siswa &amp; Santri</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $tab === 'siswa' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700 border border-slate-200/60' }}">
                        {{ $stats['siswa'] }}
                    </span>
                </a>
            </div>

            <!-- Filter Controls -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-1">
                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                    <!-- Tingkat Filter -->
                    <select wire:model.live="tingkat" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-50 border border-slate-200 text-slate-700 focus:ring-2 focus:ring-[#00519E] focus:outline-hidden">
                        <option value="">Semua Tingkat</option>
                        <option value="internasional">Internasional</option>
                        <option value="nasional">Nasional</option>
                        <option value="provinsi">Provinsi</option>
                        <option value="kabupaten_kota">Kabupaten / Kota</option>
                    </select>

                    <!-- Tahun Filter -->
                    <select wire:model.live="tahun" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-50 border border-slate-200 text-slate-700 focus:ring-2 focus:ring-[#00519E] focus:outline-hidden">
                        <option value="">Semua Tahun</option>
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}">Tahun {{ $yr }}</option>
                        @endforeach
                    </select>

                    @if($tingkat || $tahun || $search)
                        <button type="button" wire:click="resetFilters" class="px-3 py-2 rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors">
                            &times; Reset Filter
                        </button>
                    @endif
                </div>

                <!-- Instant Search Box -->
                <div class="relative w-full sm:w-64">
                    <input type="text" wire:model.live.debounce.350ms="search" placeholder="Cari nama prestasi / lomba..."
                           class="w-full pl-9 pr-4 py-2 rounded-xl text-xs bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-[#00519E] focus:outline-hidden">
                    <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 4. PRESTASI CARDS GRID ── -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($items->isEmpty())
            <div class="p-12 text-center rounded-3xl bg-white border border-slate-200 shadow-sm max-w-lg mx-auto">
                <span class="text-5xl block mb-3">🎖️</span>
                <h3 class="text-base font-bold text-slate-800 mb-1">Belum Ada Data Prestasi Ditemukan</h3>
                <p class="text-xs text-slate-500 mb-4">Coba sesuaikan filter tingkat, tahun, atau kata kunci pencarian Anda.</p>
                <button type="button" wire:click="resetFilters" class="px-4 py-2 rounded-xl text-xs font-bold text-[#00519E] bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-colors">
                    Tampilkan Semua Prestasi
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($items as $item)
                @php
                    $isEmas = str_contains(strtolower($item['rank'] ?? ''), '1') || str_contains(strtolower($item['rank'] ?? ''), 'emas') || str_contains(strtolower($item['rank'] ?? ''), 'akreditasi a');
                    $isPerak = str_contains(strtolower($item['rank'] ?? ''), '2') || str_contains(strtolower($item['rank'] ?? ''), 'perak');
                    $isPerunggu = str_contains(strtolower($item['rank'] ?? ''), '3') || str_contains(strtolower($item['rank'] ?? ''), 'perunggu');

                    $rankBadgeColor = $isEmas 
                        ? 'bg-amber-50 text-amber-900 border-amber-200/90'
                        : ($isPerak ? 'bg-sky-50 text-sky-900 border-sky-200/90' 
                        : ($isPerunggu ? 'bg-orange-50 text-orange-900 border-orange-200/90' 
                        : 'bg-slate-50 text-slate-800 border-slate-200/90'));

                    $fallbackBg = match($item['type']) {
                        'sekolah' => 'from-purple-50 via-indigo-50/40 to-white',
                        'guru'    => 'from-sky-50 via-blue-50/40 to-white',
                        'siswa'   => 'from-emerald-50 via-teal-50/40 to-white',
                        default   => 'from-blue-50 via-slate-50 to-amber-50/40',
                    };
                @endphp
                <div class="rounded-3xl bg-white border border-slate-200/80 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1.5 flex flex-col justify-between overflow-hidden group">
                    <div>
                        <!-- Thumbnail Header / Image -->
                        <div class="aspect-[16/10] w-full relative overflow-hidden bg-gradient-to-br {{ $fallbackBg }} border-b border-slate-100 flex items-center justify-center">
                            @if($item['photo_url'])
                                <img src="{{ $item['photo_url'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @elseif($item['cert_url'])
                                <img src="{{ $item['cert_url'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <!-- Cheerful Illustrated Fallback Banner -->
                                <div class="text-center p-6 space-y-2">
                                    <span class="text-4xl block transform group-hover:scale-110 transition-transform">
                                        {{ $item['type'] === 'sekolah' ? '🏛️' : ($item['type'] === 'guru' ? '👨‍🏫' : '🎓') }}
                                    </span>
                                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">Dokumentasi Terverifikasi</span>
                                </div>
                            @endif

                            <!-- Recipient Badge Pill (Top-Left) -->
                            <span class="absolute top-3 left-3 text-[10px] font-extrabold px-3 py-1 rounded-full border shadow-xs backdrop-blur-xs {{ $item['recipient_badge_color'] }}">
                                {{ $item['recipient_badge'] }}
                            </span>

                            <!-- Level Pill (Top-Right) -->
                            <span class="absolute top-3 right-3 text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-slate-900/80 text-white backdrop-blur-xs">
                                Tingkat {{ $item['level'] }}
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6 space-y-3">
                            <!-- Rank Pill Banner -->
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-extrabold border shadow-2xs {{ $rankBadgeColor }}">
                                    <span>{{ $isEmas ? '🥇' : ($isPerak ? '🥈' : ($isPerunggu ? '🥉' : '⭐')) }}</span>
                                    <span>{{ $item['rank'] }}</span>
                                </span>
                            </div>

                            <!-- Title -->
                            <h3 class="text-base font-extrabold text-slate-900 leading-snug group-hover:text-[#00519E] transition-colors line-clamp-2">
                                {{ $item['title'] }}
                            </h3>

                            <!-- Recipient & Organizer Metadata -->
                            <div class="space-y-1.5 text-xs text-slate-600">
                                <div class="flex items-start gap-2">
                                    <span class="text-slate-400 font-bold shrink-0">Peraih:</span>
                                    <span class="font-bold text-slate-800 line-clamp-1">{{ $item['recipient'] }}</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="text-slate-400 shrink-0">Penyelenggara:</span>
                                    <span class="text-slate-700 line-clamp-1">{{ $item['organizer'] }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] text-slate-400 pt-1">
                                    <span>🗓️ {{ $item['date_formatted'] }}</span>
                                </div>
                            </div>

                            @if($item['description'])
                                <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed pt-1">
                                    {{ $item['description'] }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between">
                        <button type="button" wire:click="previewItem('{{ $item['type'] }}', {{ $item['id'] }})"
                                class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-[#00519E] bg-[#EAF4FE] hover:bg-[#00519E] hover:text-white border border-[#147EFB]/30 hover:border-[#00519E] transition-all flex items-center justify-center gap-2 group-btn">
                            <span>🔍 Lihat Detail &amp; Bukti Sertifikat</span>
                            <span class="text-sm font-black transform group-hover:translate-x-1 transition-transform">&rarr;</span>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-10">
                {{ $items->links() }}
            </div>
        @endif
    </main>

    <!-- ── 5. LIGHTBOX / PREVIEW MODAL ── -->
    @if($modalData)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm animate-fade-in"
         wire:keydown.escape="closeModal">
        <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200 max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-blue-50/70 via-slate-50 to-amber-50/50 border-b border-slate-200/80 flex items-start justify-between gap-4">
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-blue-100 text-blue-800 border border-blue-200">
                        {{ $modalData['type'] }} &bull; Tingkat {{ $modalData['level'] }}
                    </span>
                    <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 mt-1">
                        {{ $modalData['title'] }}
                    </h3>
                    <div class="text-xs font-extrabold text-amber-700 mt-1 flex items-center gap-1">
                        <span>🏆 Capaian:</span>
                        <span>{{ $modalData['rank'] }}</span>
                    </div>
                </div>
                <button type="button" wire:click="closeModal" class="w-8 h-8 rounded-full bg-slate-200 hover:bg-slate-300 text-slate-700 flex items-center justify-center font-bold text-base transition-colors shrink-0">
                    &times;
                </button>
            </div>

            <!-- Modal Content (Scrollable) -->
            <div class="p-6 space-y-5 overflow-y-auto">
                <!-- Image Preview (Photo / Certificate) -->
                @if($modalData['photo_url'] || $modalData['cert_url'])
                    <div class="space-y-3">
                        <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-sm bg-slate-100">
                            <img src="{{ $modalData['photo_url'] ?: $modalData['cert_url'] }}" alt="{{ $modalData['title'] }}" class="w-full max-h-80 object-contain bg-slate-900">
                        </div>
                        @if($modalData['photo_url'] && $modalData['cert_url'])
                            <div class="text-center">
                                <a href="{{ $modalData['cert_url'] }}" target="_blank" rel="noopener" class="text-xs font-bold text-[#00519E] hover:underline">
                                    📄 Buka Berkas Sertifikat Resmi (PDF/Gambar) di Tab Baru &rarr;
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Metadata Details Grid -->
                <div class="grid grid-cols-2 gap-3 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <div>
                        <span class="text-slate-400 block font-semibold">Peraih Penghargaan</span>
                        <span class="text-slate-800 font-bold">{{ $modalData['recipient'] }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Lembaga Penyelenggara</span>
                        <span class="text-slate-800 font-bold">{{ $modalData['organizer'] }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Tanggal Perolehan</span>
                        <span class="text-slate-800 font-bold">{{ $modalData['date'] }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Tingkat Kompetisi</span>
                        <span class="text-slate-800 font-bold">{{ $modalData['level'] }}</span>
                    </div>
                </div>

                @if($modalData['description'])
                    <div class="space-y-1 text-xs">
                        <span class="text-slate-400 block font-semibold">Deskripsi Capaian</span>
                        <p class="text-slate-600 leading-relaxed bg-white p-3 rounded-xl border border-slate-100">
                            {{ $modalData['description'] }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                <button type="button" wire:click="closeModal" class="px-5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 transition-colors">
                    Tutup Pratinjau
                </button>
            </div>
        </div>
    </div>
    @endif
</div>