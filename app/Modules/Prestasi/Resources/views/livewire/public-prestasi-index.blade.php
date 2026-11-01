<div class="min-h-screen hallmark-page antialiased pb-24"
     x-data="{}"
     x-on:update-url.window="window.history.pushState({}, '', $event.detail.url)">

    @php
        $tabTitles = [
            'semua'   => [
                'tag' => 'Etalase Arsip Capaian Resmi • Terverifikasi',
                'title_prefix' => 'Merayakan Bakat',
                'title_gradient' => 'Mengukir Prestasi Gemilang',
                'subtitle' => 'Dokumentasi rekam jejak juara dan penghargaan resmi yang diraih oleh peserta didik, pendidik inspiratif, dan institusi ' . ($sekolah->nama ?? 'SMK Wikrama 1 Garut') . ' di kancah daerah hingga internasional.'
            ],
            'sekolah' => [
                'tag' => 'Arsip Mutu Institusi • Akreditasi & Kelembagaan',
                'title_prefix' => 'Keunggulan Mutu',
                'title_gradient' => 'Prestasi Resmi Institusi',
                'subtitle' => 'Pengakuan mutu kelembagaan, akreditasi unggul, dan apresiasi bergengsi institusi ' . ($sekolah->nama ?? 'SMK Wikrama 1 Garut') . ' dari kementerian, dinas pendidikan, dan mitra industri global.'
            ],
            'guru'    => [
                'tag' => 'Arsip Pendidik • Teladan & Dedikasi',
                'title_prefix' => 'Dedikasi Terbaik',
                'title_gradient' => 'Karya Nyata Guru Inspiratif',
                'subtitle' => 'Inovasi pembelajaran, kompetensi kejuruan, dan capaian juara para guru serta tenaga kependidikan dalam mencetak generasi vokasi unggul berakhlak mulia.'
            ],
            'siswa'   => [
                'tag' => 'Arsip Peserta Didik • Generasi Emas',
                'title_prefix' => 'Bakat Unggul',
                'title_gradient' => 'Jejak Juara Siswa & Santri',
                'subtitle' => 'Kiprah siswa dan santri Wikrama dalam merebut medali emas, trofi kejuaraan, dan sertifikasi bergengsi di bidang teknologi informasi, bisnis, perhotelan, sains, seni, dan olahraga.'
            ],
        ];
        $currentTabInfo = $tabTitles[$tab] ?? $tabTitles['semua'];
    @endphp

    <!-- ── 1. HERO HEADER: WIKRAMA ROYAL ARCHIVE MASTHEAD ── -->
    <header class="hallmark-hero relative text-white overflow-hidden shadow-xl">
        <!-- Archival Geometric Grid -->
        <div class="absolute inset-0 pointer-events-none z-0 opacity-10"
             style="background-image: radial-gradient(#FFFFFF 1.2px, transparent 1.2px); background-size: 28px 28px;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pt-8 sm:pt-14 pb-16 sm:pb-20 min-h-[460px] sm:min-h-[500px] flex flex-col justify-center">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs font-semibold text-blue-200/80 mb-6 sm:mb-8" aria-label="Breadcrumb">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors duration-200">Beranda</a>
                <svg class="w-3 h-3 text-blue-300/50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ url('/prestasi') }}" class="hover:text-white transition-colors duration-200">Prestasi</a>
                @if($tab !== 'semua')
                    <svg class="w-3 h-3 text-blue-300/50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-amber-300 font-bold capitalize">{{ $tab }}</span>
                @endif
            </nav>

            <!-- 2-Column Responsive Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <!-- Left Column: Editorial Masthead (lg:col-span-7) -->
                <div class="lg:col-span-7 space-y-5 text-left">
                    <!-- Pill Badge: Verified Archive Seal -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wide shadow-xs bg-white/10 border border-white/20 text-amber-300 backdrop-blur-md">
                        <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $currentTabInfo['tag'] }}</span>
                    </div>

                    <!-- Main Hero Heading: Authoritative Editorial Serif / Sans -->
                    <h1 class="hallmark-hero-heading text-3xl sm:text-4xl md:text-5xl lg:text-5xl font-black tracking-tight text-white leading-tight">
                        {{ $currentTabInfo['title_prefix'] }}, <br>
                        <span class="text-amber-300 drop-shadow-[0_2px_14px_rgba(245,216,74,0.35)]">
                            {{ $currentTabInfo['title_gradient'] }}
                        </span>
                    </h1>

                    <!-- Subtitle: High Contrast Legible -->
                    <p class="text-sm sm:text-base text-blue-100/90 leading-relaxed font-normal max-w-xl">
                        {{ $currentTabInfo['subtitle'] }}
                    </p>
                </div>

                <!-- Right Column: High-Craft Framed Visual Emblem / Photo (lg:col-span-5) -->
                <div class="lg:col-span-5 relative flex justify-center lg:justify-end">
                    @php
                        $hasCustomStudentImage = file_exists(public_path('images/landing/hero-prestasi-siswa.png')) 
                            ? asset('images/landing/hero-prestasi-siswa.png') 
                            : (file_exists(public_path('images/landing/hero-prestasi-siswa.webp')) 
                                ? asset('images/landing/hero-prestasi-siswa.webp') 
                                : null);
                    @endphp

                    @if($hasCustomStudentImage)
                        <div class="relative w-full max-w-md">
                            <div class="absolute inset-0 bg-radial from-amber-400/20 via-blue-500/10 to-transparent blur-2xl rounded-full transform scale-90"></div>
                            <img src="{{ $hasCustomStudentImage }}" alt="Siswa Berprestasi SMK Wikrama 1 Garut" class="relative z-10 w-full h-auto max-h-[360px] object-contain drop-shadow-[0_15px_30px_rgba(0,0,0,0.6)]">
                        </div>
                    @else
                        <!-- Refined Editorial Emblem Frame -->
                        <div class="hallmark-hero-frame relative w-full max-w-sm sm:max-w-md p-6 sm:p-7 rounded-2xl border border-white/20 backdrop-blur-md shadow-2xl overflow-hidden">
                            <!-- Top Tags -->
                            <div class="flex items-center justify-between gap-2 mb-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400/20 border border-amber-300/40 text-amber-300 text-[10px] font-black tracking-wide">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span>Juara Nasional</span>
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-400/20 border border-emerald-300/40 text-emerald-300 text-[10px] font-black tracking-wide">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                                    <span>Terverifikasi</span>
                                </span>
                            </div>

                            <!-- Centered Trophy Artwork -->
                            <div class="relative z-10 flex flex-col items-center text-center my-3">
                                <div class="w-28 h-28 sm:w-32 sm:h-32 relative flex items-center justify-center">
                                    <svg viewBox="0 0 128 128" class="w-24 h-24 sm:w-28 sm:h-28 drop-shadow-[0_8px_20px_rgba(245,216,74,0.4)]" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M38 24H90V54C90 68.3594 78.3594 80 64 80C49.6406 80 38 68.3594 38 54V24Z" fill="url(#hallmarkGold1)" stroke="#F5D84A" stroke-width="2.5"/>
                                        <path d="M58 80H70V96H58V80Z" fill="url(#hallmarkGold2)" stroke="#E2B714" stroke-width="2"/>
                                        <path d="M42 96H86L92 110H36L42 96Z" fill="url(#hallmarkGold2)" stroke="#E2B714" stroke-width="2"/>
                                        <path d="M38 32H28C22.4772 32 18 36.4772 18 42V48C18 56.8366 25.1634 64 34 64H38" stroke="#F5D84A" stroke-width="3" stroke-linecap="round"/>
                                        <path d="M90 32H100C105.523 32 110 36.4772 110 42V48C110 56.8366 102.837 64 94 64H90" stroke="#F5D84A" stroke-width="3" stroke-linecap="round"/>
                                        <path d="M64 36L67.5 44H76L69 49L72 57L64 52L56 57L59 49L52 44H60.5L64 36Z" fill="#FFF7CC"/>
                                        <path d="M32 110C42 116 86 116 96 110" stroke="#F5D84A" stroke-width="2.5" stroke-linecap="round"/>
                                        <defs>
                                            <linearGradient id="hallmarkGold1" x1="38" y1="24" x2="90" y2="80" gradientUnits="userSpaceOnUse">
                                                <stop stop-color="#FFF3B0"/>
                                                <stop offset="0.4" stop-color="#F5D84A"/>
                                                <stop offset="1" stop-color="#D97706"/>
                                            </linearGradient>
                                            <linearGradient id="hallmarkGold2" x1="42" y1="80" x2="86" y2="110" gradientUnits="userSpaceOnUse">
                                                <stop stop-color="#F5D84A"/>
                                                <stop offset="1" stop-color="#92400E"/>
                                            </linearGradient>
                                        </defs>
                                    </svg>
                                </div>

                                <div class="mt-2 space-y-0.5">
                                    <span class="text-xs font-bold uppercase tracking-wider text-white block">
                                        Etalase Juara Berprestasi
                                    </span>
                                    <span class="text-[11px] text-blue-200/80 block">
                                        Dokumentasi Resmi Siswa &amp; Santri
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Ambient Bottom Rule -->
        <div class="absolute bottom-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-amber-400/40 to-transparent pointer-events-none"></div>
    </header>

    <!-- ── 2. HIGHLIGHT PRESTASI UNGGULAN ── -->
    @if(isset($highlights) && $highlights->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 mt-10 sm:mt-12 mb-8 sm:mb-10">
        <!-- Section Header Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 sm:mb-5 pb-3 border-b border-slate-200/80">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(217,119,6,0.6)]"></span>
                <h2 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span>Highlight Rekam Jejak Prestasi &amp; Kejuaraan</span>
                </h2>
            </div>
            <span class="text-[11px] sm:text-xs text-slate-500 font-medium">
                {{ $highlights->count() > 4 ? 'Arahkan kursor untuk jeda • Klik kartu untuk detail' : 'Klik kartu untuk melihat bukti sertifikat & detail' }}
            </span>
        </div>

        @if($highlights->count() > 4)
            <!-- Marquee Container (> 4 Item Berjalan Loop Kanan ke Kiri) -->
            <div class="relative overflow-hidden w-full py-1">
                <!-- Vignette Gradients di sisi kiri & kanan -->
                <div class="pointer-events-none absolute inset-y-0 left-0 w-8 sm:w-16 bg-gradient-to-r from-[#F8F9FA] to-transparent z-10"></div>
                <div class="pointer-events-none absolute inset-y-0 right-0 w-8 sm:w-16 bg-gradient-to-l from-[#F8F9FA] to-transparent z-10"></div>

                <div class="animate-prestasi-marquee gap-4 sm:gap-5">
                    @foreach($highlights as $item)
                        @include('prestasi::partials.highlight-card', ['item' => $item])
                    @endforeach
                    @foreach($highlights as $item)
                        @include('prestasi::partials.highlight-card', ['item' => $item])
                    @endforeach
                </div>
            </div>
        @else
            <!-- Container Proporsional (<= 4 Item) -->
            <div class="flex flex-wrap items-stretch gap-4 sm:gap-5">
                @foreach($highlights as $item)
                    @include('prestasi::partials.highlight-card', ['item' => $item])
                @endforeach
            </div>
        @endif
    </section>
    @endif

    <!-- ── 3. INTERACTIVE TAB NAVIGATION & FILTER BAR ── -->
    <section id="daftar-prestasi" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 mt-4 mb-8">
        <div class="hallmark-panel bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-sm space-y-4">
            
            <!-- Category Tabs (Semua, Sekolah, Guru, Siswa) -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 sm:pb-0 scrollbar-none border-b border-slate-100">
                <!-- Tab Semua -->
                <a href="{{ url('/prestasi') }}" wire:click.prevent="setTab('semua')"
                   class="hallmark-tab-btn {{ $tab === 'semua' ? 'hallmark-tab-active' : 'hallmark-tab-idle' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/>
                    </svg>
                    <span>Semua Capaian</span>
                    <span class="hallmark-counter {{ $tab === 'semua' ? 'hallmark-counter-active' : 'hallmark-counter-idle' }}">
                        {{ $stats['total'] }}
                    </span>
                </a>

                <!-- Tab Sekolah -->
                <a href="{{ url('/prestasi/sekolah') }}" wire:click.prevent="setTab('sekolah')"
                   class="hallmark-tab-btn {{ $tab === 'sekolah' ? 'hallmark-tab-active' : 'hallmark-tab-idle' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.333A48.357 48.357 0 0012 9.75c-2.551 0-5.056.2-7.5.583V21m16.5 0H3"/>
                    </svg>
                    <span>Prestasi Sekolah</span>
                    <span class="hallmark-counter {{ $tab === 'sekolah' ? 'hallmark-counter-active' : 'hallmark-counter-idle' }}">
                        {{ $stats['sekolah'] }}
                    </span>
                </a>

                <!-- Tab Guru -->
                <a href="{{ url('/prestasi/guru') }}" wire:click.prevent="setTab('guru')"
                   class="hallmark-tab-btn {{ $tab === 'guru' ? 'hallmark-tab-active' : 'hallmark-tab-idle' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                    <span>Guru &amp; Tendik</span>
                    <span class="hallmark-counter {{ $tab === 'guru' ? 'hallmark-counter-active' : 'hallmark-counter-idle' }}">
                        {{ $stats['guru'] }}
                    </span>
                </a>

                <!-- Tab Siswa -->
                <a href="{{ url('/prestasi/siswa') }}" wire:click.prevent="setTab('siswa')"
                   class="hallmark-tab-btn {{ $tab === 'siswa' ? 'hallmark-tab-active' : 'hallmark-tab-idle' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147L12 6.377l7.74 3.77m-15.48 0L12 13.917l7.74-3.77m-15.48 0v4.706c0 2.234 3.466 4.047 7.74 4.047s7.74-1.813 7.74-4.047v-4.706M21 9v6"/>
                    </svg>
                    <span>Siswa &amp; Santri</span>
                    <span class="hallmark-counter {{ $tab === 'siswa' ? 'hallmark-counter-active' : 'hallmark-counter-idle' }}">
                        {{ $stats['siswa'] }}
                    </span>
                </a>
            </div>

            <!-- Filter Controls -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-1">
                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                    <!-- Tingkat Filter -->
                    <select wire:model.live="tingkat" class="hallmark-select text-xs">
                        <option value="">Semua Tingkat</option>
                        <option value="internasional">Internasional</option>
                        <option value="nasional">Nasional</option>
                        <option value="provinsi">Provinsi</option>
                        <option value="kabupaten_kota">Kabupaten / Kota</option>
                    </select>

                    <!-- Tahun Filter -->
                    <select wire:model.live="tahun" class="hallmark-select text-xs">
                        <option value="">Semua Tahun</option>
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}">Tahun {{ $yr }}</option>
                        @endforeach
                    </select>

                    @if($tingkat || $tahun || $search)
                        <button type="button" wire:click="resetFilters" class="hallmark-btn-reset text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Reset Filter</span>
                        </button>
                    @endif
                </div>

                <!-- Instant Search Box -->
                <div class="relative w-full sm:w-64">
                    <input type="text" wire:model.live.debounce.350ms="search" placeholder="Cari nama prestasi / lomba..."
                           class="hallmark-input-search w-full pl-9 pr-4 py-2 rounded-xl text-xs">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 4. PRESTASI CARDS GRID (ARCHIVE FEED) ── -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($items->isEmpty())
            <div class="p-12 text-center rounded-2xl bg-white border border-slate-200 shadow-xs max-w-lg mx-auto">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004-4.5H8.496m8.008 0a6 6 0 00-4.004-5.659 6 6 0 00-4.004 5.66M16.5 10.875A3.375 3.375 0 0019.875 7.5v-1.5A2.25 2.25 0 0017.625 3.75h-1.125m-9 7.125A3.375 3.375 0 014.125 7.5v-1.5A2.25 2.25 0 016.375 3.75h1.125"/>
                </svg>
                <h3 class="text-sm font-bold text-slate-800 mb-1">Belum Ada Data Prestasi Ditemukan</h3>
                <p class="text-xs text-slate-500 mb-4">Silakan sesuaikan filter tingkat, tahun, atau kata kunci pencarian Anda.</p>
                <button type="button" wire:click="resetFilters" class="hallmark-btn-primary text-xs">
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
                        ? 'bg-amber-50 text-amber-900 border-amber-300'
                        : ($isPerak ? 'bg-slate-100 text-slate-800 border-slate-300' 
                        : ($isPerunggu ? 'bg-orange-50 text-orange-900 border-orange-200' 
                        : 'bg-slate-50 text-slate-800 border-slate-200'));
                @endphp
                <article class="hallmark-archive-card bg-white rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between overflow-hidden group">
                    <div>
                        <!-- Thumbnail Header / Image -->
                        <div class="aspect-[16/10] w-full relative overflow-hidden bg-slate-900 border-b border-slate-100 flex items-center justify-center">
                            @if($item['photo_url'])
                                <img src="{{ $item['photo_url'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            @elseif($item['cert_url'])
                                <img src="{{ $item['cert_url'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            @else
                                <div class="text-center p-6 space-y-1 text-slate-300">
                                    <svg class="w-10 h-10 mx-auto text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004-4.5H8.496m8.008 0a6 6 0 00-4.004-5.659 6 6 0 00-4.004 5.66M16.5 10.875A3.375 3.375 0 0019.875 7.5v-1.5A2.25 2.25 0 0017.625 3.75h-1.125m-9 7.125A3.375 3.375 0 014.125 7.5v-1.5A2.25 2.25 0 016.375 3.75h1.125"/>
                                    </svg>
                                    <span class="text-[10px] font-bold uppercase tracking-wider block text-slate-400">Dokumentasi Terverifikasi</span>
                                </div>
                            @endif

                            <!-- Recipient Badge Pill (Top-Left) -->
                            <span class="absolute top-3 left-3 text-[10px] font-bold px-2.5 py-0.5 rounded-full border shadow-xs backdrop-blur-xs {{ $item['recipient_badge_color'] }}">
                                {{ $item['recipient_badge'] }}
                            </span>

                            <!-- Level Pill (Top-Right) -->
                            <span class="absolute top-3 right-3 text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-900/80 text-white backdrop-blur-xs">
                                Tingkat {{ $item['level'] }}
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 space-y-3">
                            <!-- Rank Pill -->
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-bold border {{ $rankBadgeColor }}">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span>{{ $item['rank'] }}</span>
                                </span>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-snug group-hover:text-[#00519E] transition-colors line-clamp-2">
                                {{ $item['title'] }}
                            </h3>

                            <!-- Recipient & Organizer Metadata -->
                            <div class="space-y-1.5 text-xs text-slate-600">
                                <div class="flex items-start gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                    <span class="font-bold text-slate-800 line-clamp-1">{{ $item['recipient'] }}</span>
                                </div>
                                <div class="flex items-start gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                    <span class="text-slate-700 line-clamp-1">{{ $item['organizer'] }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-[11px] text-slate-400 pt-0.5">
                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                    <span>{{ $item['date_formatted'] }}</span>
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
                    <div class="px-5 py-3.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between">
                        <button type="button" wire:click="previewItem('{{ $item['type'] }}', {{ $item['id'] }})"
                                class="w-full py-2 px-3.5 rounded-xl text-xs font-bold text-[#00519E] bg-blue-50/70 hover:bg-[#00519E] hover:text-white border border-blue-200/80 hover:border-[#00519E] transition-colors duration-200 flex items-center justify-center gap-1.5">
                            <span>Lihat Detail &amp; Bukti Sertifikat</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </button>
                    </div>
                </article>
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
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs animate-fade-in"
         wire:keydown.escape="closeModal"
         role="dialog"
         aria-modal="true">
        <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-200 max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="p-5 sm:p-6 bg-slate-50 border-b border-slate-200 flex items-start justify-between gap-4">
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800 border border-blue-200">
                        {{ $modalData['type'] }} &bull; Tingkat {{ $modalData['level'] }}
                    </span>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 mt-1">
                        {{ $modalData['title'] }}
                    </h3>
                    <div class="text-xs font-bold text-amber-700 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <span>Capaian: {{ $modalData['rank'] }}</span>
                    </div>
                </div>
                <button type="button" wire:click="closeModal" aria-label="Tutup modal" class="w-8 h-8 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 flex items-center justify-center font-bold text-base transition-colors duration-200 shrink-0">
                    &times;
                </button>
            </div>

            <!-- Modal Content (Scrollable) -->
            <div class="p-5 sm:p-6 space-y-4 overflow-y-auto">
                <!-- Image Preview (Photo / Certificate) -->
                @if($modalData['photo_url'] || $modalData['cert_url'])
                    <div class="space-y-2">
                        <div class="rounded-xl overflow-hidden border border-slate-200 shadow-xs bg-slate-900">
                            <img src="{{ $modalData['photo_url'] ?: $modalData['cert_url'] }}" alt="{{ $modalData['title'] }}" class="w-full max-h-80 object-contain">
                        </div>
                        @if($modalData['photo_url'] && $modalData['cert_url'])
                            <div class="text-center">
                                <a href="{{ $modalData['cert_url'] }}" target="_blank" rel="noopener" class="text-xs font-bold text-[#00519E] hover:underline inline-flex items-center gap-1">
                                    <span>Buka Berkas Sertifikat Resmi (PDF/Gambar) di Tab Baru</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Metadata Details Grid -->
                <div class="grid grid-cols-2 gap-3 text-xs bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <div>
                        <span class="text-slate-400 block font-medium">Peraih Penghargaan</span>
                        <span class="text-slate-800 font-bold">{{ $modalData['recipient'] }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Lembaga Penyelenggara</span>
                        <span class="text-slate-800 font-bold">{{ $modalData['organizer'] }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Tanggal Perolehan</span>
                        <span class="text-slate-800 font-bold">{{ $modalData['date'] }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Tingkat Kompetisi</span>
                        <span class="text-slate-800 font-bold">{{ $modalData['level'] }}</span>
                    </div>
                </div>

                @if($modalData['description'])
                    <div class="space-y-1 text-xs">
                        <span class="text-slate-400 block font-medium">Deskripsi Capaian</span>
                        <p class="text-slate-600 leading-relaxed bg-white p-3 rounded-xl border border-slate-200">
                            {{ $modalData['description'] }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 transition-colors duration-200">
                    Tutup Pratinjau
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- ── 6. HALLMARK ARCHIVE STYLES & TOKENS ── -->
    <style>
        /* Hallmark · v2.0.0 · theme: Wikrama Royal Archive (dealt manual) · genre: editorial · job: archive */
        :root {
            --color-bg-page: #F8F9FA;
            --color-bg-paper: #FFFFFF;
            --color-bg-hero: #001733;
            --color-bg-hero-alt: #002244;
            --color-text-hero: #F8FAFC;
            --color-text-hero-muted: #94A3B8;
            --color-text-main: #0F172A;
            --color-text-muted: #64748B;
            --color-brand-primary: #00519E;
            --color-brass-gold: #C99700;
            --color-emerald-active: #059669;
            --color-border: #E2E8F0;
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
        }

        /* Hallmark Floor Rule 4: overflow-x clip on both html and body */
        html, body {
            overflow-x: clip !important;
        }

        /* Hallmark Floor Rule 6: Accessible focus-visible */
        a:focus-visible,
        button:focus-visible,
        input:focus-visible,
        select:focus-visible,
        article[tabindex]:focus-visible {
            outline: 2px solid var(--color-brand-primary) !important;
            outline-offset: 2px !important;
        }

        /* Hero styling */
        .hallmark-hero {
            background: linear-gradient(135deg, var(--color-bg-hero) 0%, var(--color-bg-hero-alt) 50%, #00305F 100%);
        }
        .hallmark-hero-frame {
            background: linear-gradient(145deg, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0.03) 100%);
        }

        /* Hallmark Tabs */
        .hallmark-tab-btn {
            padding: 0.6rem 1.1rem;
            border-radius: var(--radius-md);
            font-size: 0.8125rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
            flex-shrink: 0;
            text-decoration: none;
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }
        .hallmark-tab-active {
            background-color: var(--color-emerald-active) !important;
            color: #FFFFFF !important;
            border: 1px solid #047857 !important;
            box-shadow: 0 1px 3px rgba(4, 120, 87, 0.2);
        }
        .hallmark-tab-idle {
            background-color: #FFFFFF;
            color: var(--color-text-main);
            border: 1px solid var(--color-border);
        }
        .hallmark-tab-idle:hover {
            background-color: #F8FAFC;
            border-color: #CBD5E1;
        }
        .hallmark-counter {
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.625rem;
            font-weight: 800;
        }
        .hallmark-counter-active {
            background-color: rgba(255, 255, 255, 0.22);
            color: #FFFFFF;
        }
        .hallmark-counter-idle {
            background-color: #F1F5F9;
            color: var(--color-text-muted);
            border: 1px solid #E2E8F0;
        }

        /* Hallmark Form Controls */
        .hallmark-select {
            padding: 0.5rem 0.85rem;
            border-radius: var(--radius-sm);
            background-color: #F8FAFC;
            border: 1px solid var(--color-border);
            color: var(--color-text-main);
            font-weight: 600;
        }
        .hallmark-input-search {
            background-color: #F8FAFC;
            border: 1px solid var(--color-border);
            color: var(--color-text-main);
        }
        .hallmark-btn-reset {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.5rem 0.75rem;
            border-radius: var(--radius-sm);
            background-color: #FFF1F2;
            color: #E11D48;
            border: 1px solid #FECDD3;
            font-weight: 700;
            transition: background-color 0.2s ease;
        }
        .hallmark-btn-reset:hover {
            background-color: #FFE4E6;
        }
        .hallmark-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-sm);
            background-color: var(--color-brand-primary);
            color: #FFFFFF;
            font-weight: 700;
            transition: background-color 0.2s ease;
        }
        .hallmark-btn-primary:hover {
            background-color: #003e7a;
        }

        /* Highlight Card System */
        .hallmark-highlight-card {
            width: 100%;
            max-width: 270px;
            border-radius: var(--radius-md);
            background-color: #FFFFFF;
            border: 1px solid var(--color-border);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            cursor: pointer;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            flex-shrink: 0;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        @media (min-width: 640px) {
            .hallmark-highlight-card {
                width: 260px;
            }
        }
        .hallmark-highlight-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
        }
        .hallmark-card-media {
            position: relative;
            height: 7.5rem;
            width: 100%;
            background-color: #0F172A;
            overflow: hidden;
        }
        @media (min-width: 640px) {
            .hallmark-card-media {
                height: 8rem;
            }
        }
        .hallmark-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .hallmark-highlight-card:hover .hallmark-card-img {
            transform: scale(1.05);
        }
        .hallmark-card-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.85) 0%, rgba(15, 23, 42, 0.2) 60%, transparent 100%);
        }
        .hallmark-card-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.75rem;
            text-align: center;
            background: linear-gradient(135deg, var(--color-bg-hero) 0%, #00305F 100%);
        }
        .hallmark-trophy-emblem {
            width: 2.25rem;
            height: 2.25rem;
            color: #F5D84A;
            margin-bottom: 0.25rem;
        }
        .hallmark-card-fallback-rank {
            font-size: 0.5625rem;
            font-weight: 800;
            color: #FDE68A;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .hallmark-media-badges {
            position: absolute;
            top: 0.5rem;
            left: 0.5rem;
            right: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.25rem;
            z-index: 10;
        }
        .hallmark-pill {
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.5625rem;
            font-weight: 800;
            letter-spacing: 0.05em;
        }
        .hallmark-pill-category {
            background-color: rgba(255, 255, 255, 0.92);
            color: #0F172A;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .hallmark-pill-level {
            background-color: rgba(15, 23, 42, 0.8);
            color: #FFFFFF;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .hallmark-media-bottom {
            position: absolute;
            bottom: 0.5rem;
            left: 0.5rem;
            right: 0.5rem;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .hallmark-rank-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            background-color: #FBBF24;
            color: #451A03;
            font-size: 0.625rem;
            font-weight: 800;
        }
        .hallmark-date-tag {
            font-size: 0.5625rem;
            font-weight: 600;
            color: rgba(239, 246, 255, 0.95);
            background-color: rgba(0, 0, 0, 0.45);
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
        }
        .hallmark-card-body {
            padding: 0.875rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 0.625rem;
        }
        .hallmark-card-title {
            font-size: 0.8125rem;
            font-weight: 700;
            color: var(--color-text-main);
            line-height: 1.35;
            transition: color 0.2s ease;
        }
        .hallmark-highlight-card:hover .hallmark-card-title {
            color: var(--color-brand-primary);
        }
        .hallmark-card-recipient {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.6875rem;
            font-weight: 700;
            color: var(--color-text-main);
            padding-top: 0.125rem;
        }
        .hallmark-card-organizer {
            font-size: 0.625rem;
            color: var(--color-text-muted);
        }
        .hallmark-card-action {
            padding-top: 0.5rem;
            border-top: 1px solid #F1F5F9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.625rem;
            font-weight: 700;
            color: var(--color-brand-primary);
            transition: color 0.2s ease;
        }
        .hallmark-highlight-card:hover .hallmark-card-action {
            color: #D97706;
        }
        .hallmark-arrow {
            transition: transform 0.25s ease;
        }
        .hallmark-highlight-card:hover .hallmark-arrow {
            transform: translateX(3px);
        }

        /* Archive Card Grid Items */
        .hallmark-archive-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .hallmark-archive-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
        }

        /* Infinite Marquee Looping */
        @keyframes hallmarkMarqueeLoop {
            0% {
                transform: translateX(0%);
            }
            100% {
                transform: translateX(-50%);
            }
        }
        .animate-prestasi-marquee {
            display: flex;
            width: max-content;
            animation: hallmarkMarqueeLoop 35s linear infinite;
        }
        .animate-prestasi-marquee:hover {
            animation-play-state: paused;
        }

        /* Hallmark Floor Rule 7: prefers-reduced-motion fallback */
        @media (prefers-reduced-motion: reduce) {
            .animate-prestasi-marquee {
                animation: none !important;
            }
            * {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</div>
