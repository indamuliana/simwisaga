@php
    $cfg = $cfg ?? (class_exists(\App\Modules\AdministrasiWeb\Models\WebSetting::class)
        ? \App\Modules\AdministrasiWeb\Models\WebSetting::mergedLandingConfig()
        : (config('landing') ?? []));
    $headerMenus = $headerMenus ?? (class_exists(\App\Modules\AdministrasiWeb\Models\WebMenu::class)
        ? \App\Modules\AdministrasiWeb\Models\WebMenu::getHeaderMenu()
        : collect());
    $sekolah = $sekolah ?? \App\Modules\Sistem\Models\School::current();
    $schoolName = $schoolName ?? ($sekolah->nama ?? ($cfg['branding']['school_name'] ?? 'SMK Wikrama 1 Garut'));
    $logoUrl = $logoUrl ?? (file_exists(public_path('images/landing/wikrama-logo.png')) 
        ? asset('images/landing/wikrama-logo.png') 
        : ($sekolah->logoSekolahUrl() ?: asset('favicon.png')));
    $isSubpage = !request()->is('/');
@endphp

    <!-- ── Floating Sticky Quick-Action Dock (Only on Extra Wide 2xl+ Displays to prevent layout overlap) ── -->
    <aside class="hidden 2xl:flex hp-sticky-dock" aria-label="Aksi Cepat Sekolah">
        @foreach($cfg['sticky_actions'] ?? [] as $st)
        <a href="{{ $st['url'] }}" 
           target="{{ $st['target'] }}" 
           rel="{{ $st['target'] === '_blank' ? 'noopener noreferrer' : '' }}"
           class="hp-sticky-btn {{ ($st['highlight'] ?? false) ? 'highlight' : '' }}"
           title="{{ $st['label'] }} - {{ $st['sub'] }}">
            <span class="w-5 h-5 flex items-center justify-center shrink-0">
                @if(($st['icon'] ?? '') === 'pencil')
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                @elseif(($st['icon'] ?? '') === 'map-pin')
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                @elseif(($st['icon'] ?? '') === 'download')
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                @elseif(($st['icon'] ?? '') === 'chat')
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                @else
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                @endif
            </span>
            <div class="whitespace-nowrap pr-2">
                <span class="block leading-none">{{ $st['label'] }}</span>
                <span class="block text-[9px] opacity-80 font-normal mt-0.5">{{ $st['sub'] }}</span>
            </div>
        </a>
        @endforeach
    </aside>

    <!-- ── Compact Quick Action FAB for Standard Laptops & Mobile (< 2xl) ── -->
    <div class="2xl:hidden fixed bottom-5 right-4 sm:bottom-6 sm:right-6 z-40" x-show="!mobileMenu" x-data="{ fabOpen: false }" @click.outside="fabOpen = false">
        <div x-show="fabOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-3 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-3 scale-95"
             class="mb-3 flex flex-col items-end gap-2.5">
            <a href="{{ $cfg['branding']['spmb_url'] ?? 'https://spmb.smkwikrama1garut.sch.id/' }}" target="_blank" rel="noopener" 
               class="px-4 py-2 rounded-full bg-[#F5D84A] text-[#232327] text-xs font-bold shadow-xl flex items-center gap-2 hover:bg-[#E2B714] transition-all border border-[#E2B714]">
                <span>Daftar SPMB Online</span>
                <span>&rarr;</span>
            </a>
            <a href="https://wa.me/6281323314430?text=Halo%20Admin%20SMK%20Wikrama%201%20Garut,%20saya%20ingin%20berkonsultasi%20tentang%20penerimaan%20siswa%20baru" target="_blank" rel="noopener" 
               class="px-4 py-2 rounded-full bg-[#1F513F] text-white text-xs font-bold shadow-xl flex items-center gap-2 hover:bg-[#163c2f] transition-all border border-emerald-400/40">
                <span>Chat Admin WhatsApp</span>
                <span class="w-2 h-2 rounded-full bg-[#F5D84A] animate-pulse"></span>
            </a>
            <a href="{{ $cfg['branding']['brochure_url'] ?? 'http://brosur.smkwikrama1garut.sch.id/' }}" target="_blank" rel="noopener" 
               class="px-4 py-2 rounded-full bg-[#00305F] text-white text-xs font-semibold shadow-xl flex items-center gap-2 hover:bg-[#00519E] transition-all border border-white/20">
                <span>Unduh Brosur Resmi</span>
                <span>&darr;</span>
            </a>
        </div>
        <button type="button" 
                @click="fabOpen = !fabOpen" 
                class="w-12 h-12 rounded-full bg-[#00519E] hover:bg-[#00305F] text-white shadow-xl flex items-center justify-center transition-all hover:scale-105 active:scale-95 cursor-pointer border-2 border-white/20"
                aria-label="Aksi Cepat Sekolah">
            <svg x-show="!fabOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            <svg x-show="fabOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- ── Institutional Navigation: Transparent Overlay on Hero ── -->
    <div id="main-nav-container"
         class="fixed top-0 inset-x-0 z-40 transition-all duration-300 pointer-events-auto"
         :class="(scrolled || {{ $isSubpage ? 'true' : 'false' }}) ? 'bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-lg shadow-slate-200/50' : 'bg-gradient-to-b from-slate-950/85 via-slate-950/40 to-transparent border-b border-white/10'">
        
        <!-- Tier 1: JIS Midnight Navy Top Utility Bar (#00305F) -->
        @if($cfg['announcement']['enabled'] ?? true)
        <div x-show="!scrolled && !{{ $isSubpage ? 'true' : 'false' }}" x-transition.opacity.duration.200ms class="text-white text-xs py-2 px-4 border-b border-white/10 bg-[#00305F] shadow-sm">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                <div class="flex items-center gap-2.5 text-center sm:text-left">
                    <span class="bg-[#F5D84A] text-[#00305F] px-2.5 py-0.5 rounded-full text-[10px] font-extrabold tracking-wider shrink-0 shadow-xs">
                        {{ $cfg['announcement']['badge'] ?? 'SPMB 2026/2027' }}
                    </span>
                    <span class="text-white/95 font-semibold text-[11px] sm:text-xs drop-shadow-xs">
                        {{ $cfg['announcement']['text'] ?? 'Penerimaan Peserta Didik Baru Resmi Dibuka!' }}
                    </span>
                </div>

                <div class="flex items-center gap-4 text-[11px] font-semibold text-white/90">
                    <a href="{{ $cfg['announcement']['phone_url'] ?? 'tel:081323314430' }}" class="hidden md:flex items-center gap-1.5 text-white/90 hover:text-[#F5D84A] transition-colors drop-shadow-xs">
                        <svg class="w-3.5 h-3.5 text-[#F5D84A]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>{{ $cfg['announcement']['phone_display'] ?? '0813-2331-4430' }}</span>
                    </a>
                    <span class="hidden md:inline text-white/30">|</span>
                    <a href="{{ $cfg['announcement']['cta_url'] ?? 'https://spmb.smkwikrama1garut.sch.id/' }}" target="_blank" rel="noopener" class="text-[#00305F] bg-[#F5D84A] hover:bg-[#E2B714] px-3.5 py-1 rounded-full font-bold inline-flex items-center gap-1 transition-all shadow-xs">
                        <span>{{ $cfg['announcement']['cta_text'] ?? 'Daftar SPMB Online' }}</span> &rarr;
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Tier 2: Main Header Navigation -->
        <header class="w-full">
        <div class="w-full max-w-[1680px] mx-auto px-4 sm:px-6 lg:px-10 xl:px-16 h-16 sm:h-20 flex items-center justify-between gap-2.5 sm:gap-4">
            
            <!-- School Brand Crest (Unboxed Logo & Crisp School Name) -->
            <a href="{{ url('/') }}" class="flex items-center space-x-2.5 sm:space-x-3.5 group shrink-0 min-w-0" data-cursor="BERANDA">
                <img src="{{ $logoUrl }}" alt="Logo {{ $schoolName }}" class="h-9 sm:h-12 w-auto object-contain group-hover:scale-105 transition-transform drop-shadow-md shrink-0">
                <div class="min-w-0 max-w-[180px] sm:max-w-none">
                    <span class="text-sm sm:text-lg lg:text-xl font-extrabold tracking-tight block leading-tight group-hover:text-[#00519E] transition-colors truncate nav-brand-title"
                          :class="(scrolled || {{ $isSubpage ? 'true' : 'false' }}) ? 'text-slate-900 drop-shadow-none' : 'text-white drop-shadow-[0_1px_3px_rgba(0,0,0,0.9)]'">
                        {{ $schoolName }}
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation Menu -->
            <nav class="hidden lg:flex items-center space-x-1 xl:space-x-2 text-xs xl:text-sm font-medium"
                 x-data="{ activeDropdown: null }"
                 @click.outside="activeDropdown = null">
                @if($headerMenus->isNotEmpty())
                    @foreach($headerMenus as $m)
                        @if($m->children->isNotEmpty())
                            <!-- Dropdown Menu: {{ $m->label }} -->
                            <div class="relative" @mouseenter="activeDropdown = 'menu-{{ $m->id }}'" @mouseleave="activeDropdown = null">
                                <button type="button" 
                                        @click="activeDropdown = (activeDropdown === 'menu-{{ $m->id }}' ? null : 'menu-{{ $m->id }}')"
                                        class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl font-semibold transition-all inline-flex items-center gap-1 cursor-pointer nav-link"
                                        :class="(scrolled || {{ $isSubpage ? 'true' : 'false' }}) ? (activeDropdown === 'menu-{{ $m->id }}' ? 'text-[#00519E] bg-[#EBF4FC]' : 'text-slate-700 hover:text-[#00519E] hover:bg-[#EBF4FC]/70') : (activeDropdown === 'menu-{{ $m->id }}' ? 'text-[#F5D84A] bg-white/20' : 'text-white hover:text-[#F5D84A] hover:bg-white/15 drop-shadow-[0_1px_3px_rgba(0,0,0,0.85)]')">
                                    <span>{{ $m->label }}</span>
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="activeDropdown === 'menu-{{ $m->id }}' ? 'rotate-180 text-[#00519E]' : ((scrolled || {{ $isSubpage ? 'true' : 'false' }}) ? 'text-slate-500' : 'text-white')" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="activeDropdown === 'menu-{{ $m->id }}'" 
                                     x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                     class="absolute left-0 mt-1 min-w-[210px] w-auto max-w-xs rounded-2xl bg-white shadow-xl border border-slate-200/90 p-2 z-50">
                                    @foreach($m->children as $child)
                                        @php
                                            $childUrl = str_starts_with($child->url, 'http://') || str_starts_with($child->url, 'https://')
                                                ? $child->url
                                                : (str_starts_with($child->url, '#') ? ($isSubpage ? url('/' . $child->url) : $child->url) : url($child->url));
                                        @endphp
                                        <a href="{{ $childUrl }}" 
                                           target="{{ $child->target }}" 
                                           @click="activeDropdown = null" 
                                           class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-[#EBF4FC] hover:text-[#00519E] transition-colors">
                                            {{ $child->label }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            @php
                                $menuUrl = str_starts_with($m->url, 'http://') || str_starts_with($m->url, 'https://')
                                    ? $m->url
                                    : (str_starts_with($m->url, '#') ? ($isSubpage ? url('/' . $m->url) : $m->url) : url($m->url));
                            @endphp
                            <a href="{{ $menuUrl }}" target="{{ $m->target }}" 
                               class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl font-semibold transition-all inline-flex items-center nav-link"
                               :class="(scrolled || {{ $isSubpage ? 'true' : 'false' }}) ? 'text-slate-700 hover:text-[#00519E] hover:bg-[#EBF4FC]/70' : 'text-white hover:text-[#F5D84A] hover:bg-white/15 drop-shadow-[0_1px_3px_rgba(0,0,0,0.85)]'">
                                {{ $m->label }}
                            </a>
                        @endif
                    @endforeach
                @else
                    <a href="{{ $isSubpage ? url('/#beranda') : '#beranda' }}" class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl font-semibold transition-all nav-link" :class="(scrolled || {{ $isSubpage ? 'true' : 'false' }}) ? 'text-slate-700 hover:text-[#00519E] hover:bg-[#EBF4FC]/70' : 'text-white hover:text-[#F5D84A] hover:bg-white/15 drop-shadow-[0_1px_3px_rgba(0,0,0,0.85)]'">Beranda</a>
                    <a href="{{ $isSubpage ? url('/#filosofi') : '#filosofi' }}" class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl font-semibold transition-all nav-link" :class="(scrolled || {{ $isSubpage ? 'true' : 'false' }}) ? 'text-slate-700 hover:text-[#00519E] hover:bg-[#EBF4FC]/70' : 'text-white hover:text-[#F5D84A] hover:bg-white/15 drop-shadow-[0_1px_3px_rgba(0,0,0,0.85)]'">Profil</a>
                    <a href="{{ url('/jurusan') }}" class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl font-semibold transition-all nav-link {{ request()->is('jurusan*') ? 'text-[#00519E] bg-[#EBF4FC]' : '' }}" :class="(scrolled || {{ $isSubpage ? 'true' : 'false' }}) ? 'text-slate-700 hover:text-[#00519E] hover:bg-[#EBF4FC]/70' : 'text-white hover:text-[#F5D84A] hover:bg-white/15 drop-shadow-[0_1px_3px_rgba(0,0,0,0.85)]'">Jurusan</a>
                    <a href="{{ $isSubpage ? url('/#dinamika-siswa') : '#dinamika-siswa' }}" class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl font-semibold transition-all nav-link" :class="(scrolled || {{ $isSubpage ? 'true' : 'false' }}) ? 'text-slate-700 hover:text-[#00519E] hover:bg-[#EBF4FC]/70' : 'text-white hover:text-[#F5D84A] hover:bg-white/15 drop-shadow-[0_1px_3px_rgba(0,0,0,0.85)]'">Kesiswaan</a>
                    <a href="{{ url('/berita') }}" class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl font-semibold transition-all nav-link {{ request()->is('berita*') ? 'text-[#00519E] bg-[#EBF4FC]' : '' }}" :class="(scrolled || {{ $isSubpage ? 'true' : 'false' }}) ? 'text-slate-700 hover:text-[#00519E] hover:bg-[#EBF4FC]/70' : 'text-white hover:text-[#F5D84A] hover:bg-white/15 drop-shadow-[0_1px_3px_rgba(0,0,0,0.85)]'">Berita</a>
                    <a href="{{ $isSubpage ? url('/#kontak') : '#kontak' }}" class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl font-semibold transition-all nav-link" :class="(scrolled || {{ $isSubpage ? 'true' : 'false' }}) ? 'text-slate-700 hover:text-[#00519E] hover:bg-[#EBF4FC]/70' : 'text-white hover:text-[#F5D84A] hover:bg-white/15 drop-shadow-[0_1px_3px_rgba(0,0,0,0.85)]'">Kontak</a>
                @endif
            </nav>

            <!-- Actions: Auth Button, SPMB & Mobile Menu Toggle -->
            <div class="flex items-center space-x-2 sm:space-x-3 shrink-0">
                @auth
                    <a href="{{ route('dashboard') }}" 
                       class="btn-jis-pill-navy text-xs sm:text-sm !py-1.5 !px-3 sm:!py-2.5 sm:!px-4 inline-flex items-center gap-1.5 shadow-xs group"
                       title="Akses Sistem Informasi SIRAGA"
                       data-cursor="PORTAL">
                        <svg class="w-4 h-4 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z" />
                        </svg>
                        <span class="font-bold">Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="px-3 sm:px-4 py-1.5 sm:py-2.5 rounded-full border text-xs sm:text-sm font-semibold transition-all inline-flex items-center gap-1.5 backdrop-blur-xs"
                       :class="(scrolled || {{ $isSubpage ? 'true' : 'false' }}) ? 'border-slate-300 text-slate-800 hover:bg-[#EBF4FC] hover:border-[#00519E] hover:text-[#00519E]' : 'border-white/30 text-white hover:bg-white/15'"
                       data-cursor="MASUK">
                        <svg class="w-3.5 h-3.5 shrink-0" :class="(scrolled || {{ $isSubpage ? 'true' : 'false' }}) ? 'text-slate-600' : 'text-white/80'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>Masuk</span>
                    </a>
                @endauth

                <!-- JIS Signature Pill CTA Button (Sun Gold) -->
                <a href="{{ $cfg['branding']['spmb_url'] ?? 'https://spmb.smkwikrama1garut.sch.id/' }}" 
                   target="_blank" 
                   rel="noopener" 
                   class="btn-jis-pill-gold text-xs sm:text-sm !py-1.5 !px-3 sm:!py-2.5 sm:!px-5 inline-flex items-center gap-1 sm:gap-1.5 shadow-md group"
                   data-cursor="DAFTAR">
                    <span>Daftar SPMB</span>
                    <span class="text-xs group-hover:translate-x-1 transition-transform">&rarr;</span>
                </a>

                <!-- Mobile Menu Hamburger Button -->
                <button type="button" 
                        @click="mobileMenu = !mobileMenu"
                        class="lg:hidden p-1.5 sm:p-2 rounded-xl cursor-pointer transition-colors"
                        :class="(scrolled || {{ $isSubpage ? 'true' : 'false' }}) ? 'text-slate-800 hover:bg-slate-100' : 'text-white hover:bg-white/15'"
                        aria-label="Buka Menu Navigasi">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenu" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Navigation -->
        <div x-show="mobileMenu" x-cloak 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="lg:hidden absolute top-full inset-x-0 bg-white border-b border-slate-200 z-40 p-5 shadow-2xl max-h-[calc(100vh-5rem)] overflow-y-auto">
            <div class="flex flex-col space-y-3 text-sm font-semibold text-slate-800">
                @auth
                    <div class="p-3 rounded-xl bg-[#EBF4FC] border border-blue-200 flex items-center justify-between">
                        <div class="min-w-0 pr-2">
                            <span class="text-[10px] font-mono-code font-bold uppercase tracking-wider text-[#00519E] block">Sesi Login Aktif</span>
                            <span class="text-xs font-bold text-slate-900 truncate block">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] text-slate-500 capitalize block">{{ auth()->user()->role ?? 'Pengguna' }}</span>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn-jis-pill-navy text-xs !py-1.5 !px-3 shrink-0 inline-flex items-center gap-1">
                            <span>Dashboard</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                @else
                    <div class="pb-1">
                        <a href="{{ route('login') }}" class="btn-jis-pill-outline w-full text-center text-xs py-2.5 flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                            <span>Masuk ke Portal SIRAGA</span>
                        </a>
                    </div>
                @endauth

                @if($headerMenus->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($headerMenus as $m)
                            @if($m->children->isNotEmpty())
                                <div>
                                    <span class="text-[10px] font-mono-code font-bold uppercase tracking-wider text-slate-500 block px-2 pb-1">
                                        {{ $m->label }}
                                    </span>
                                    <div class="grid grid-cols-2 gap-1.5">
                                        @foreach($m->children as $child)
                                            @php
                                                $childUrl = str_starts_with($child->url, 'http://') || str_starts_with($child->url, 'https://')
                                                    ? $child->url
                                                    : (str_starts_with($child->url, '#') ? ($isSubpage ? url('/' . $child->url) : $child->url) : url($child->url));
                                            @endphp
                                            <a href="{{ $childUrl }}" 
                                               target="{{ $child->target }}" 
                                               @click="mobileMenu = false" 
                                               class="p-2 rounded-lg hover:bg-[#EBF4FC] hover:text-[#00519E] text-xs font-semibold text-slate-800 transition-colors">
                                                {{ $child->label }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                @php
                                    $menuUrl = str_starts_with($m->url, 'http://') || str_starts_with($m->url, 'https://')
                                        ? $m->url
                                        : (str_starts_with($m->url, '#') ? ($isSubpage ? url('/' . $m->url) : $m->url) : url($m->url));
                                @endphp
                                <a href="{{ $menuUrl }}" 
                                   target="{{ $m->target }}" 
                                   @click="mobileMenu = false" 
                                   class="p-2 rounded-lg hover:bg-[#EBF4FC] hover:text-[#00519E] text-xs font-bold text-slate-800 block transition-colors">
                                    {{ $m->label }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="space-y-3">
                        <div>
                            <span class="text-[10px] font-mono-code font-bold uppercase tracking-wider text-slate-500 block px-2 pb-1">Profil &amp; Keunggulan</span>
                            <div class="grid grid-cols-2 gap-1.5">
                                <a href="{{ $isSubpage ? url('/#beranda') : '#beranda' }}" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-[#EBF4FC] hover:text-[#00519E] text-xs font-semibold text-slate-800 transition-colors">Beranda</a>
                                <a href="{{ $isSubpage ? url('/#filosofi') : '#filosofi' }}" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-[#EBF4FC] hover:text-[#00519E] text-xs font-semibold text-slate-800 transition-colors">Filosofi &amp; Karakter</a>
                                <a href="{{ $isSubpage ? url('/#mengenal') : '#mengenal' }}" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-[#EBF4FC] hover:text-[#00519E] text-xs font-semibold text-slate-800 transition-colors">Mengenal Wikrama</a>
                                <a href="{{ $isSubpage ? url('/#rapor-mutu') : '#rapor-mutu' }}" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-[#EBF4FC] hover:text-[#00519E] text-xs font-semibold text-slate-800 transition-colors">Rapor Mutu Kemdikbud</a>
                                <a href="{{ $isSubpage ? url('/#sekolah-teladan') : '#sekolah-teladan' }}" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-[#EBF4FC] hover:text-[#00519E] text-xs font-semibold text-slate-800 transition-colors">Sekolah Teladan</a>
                            </div>
                        </div>

                        <div>
                            <span class="text-[10px] font-mono-code font-bold uppercase tracking-wider text-slate-500 block px-2 pb-1">Akademik &amp; Kesiswaan</span>
                            <div class="grid grid-cols-2 gap-1.5">
                                <a href="{{ url('/jurusan') }}" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-[#EBF4FC] hover:text-[#00519E] text-xs font-semibold text-slate-800 transition-colors {{ request()->is('jurusan*') ? 'text-[#00519E] font-bold bg-[#EBF4FC]' : '' }}">4 Jurusan Kejuruan</a>
                                <a href="{{ $isSubpage ? url('/#agenda') : '#agenda' }}" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-[#EBF4FC] hover:text-[#00519E] text-xs font-bold text-[#00519E] transition-colors">Agenda Kegiatan</a>
                                <a href="{{ $isSubpage ? url('/#dinamika-siswa') : '#dinamika-siswa' }}" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-[#EBF4FC] hover:text-[#00519E] text-xs font-semibold text-slate-800 transition-colors">Dinamika Siswa</a>
                                <a href="{{ $isSubpage ? url('/#fasilitas') : '#fasilitas' }}" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-[#EBF4FC] hover:text-[#00519E] text-xs font-semibold text-slate-800 transition-colors">Fasilitas Lab</a>
                                <a href="{{ url('/berita') }}" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-[#EBF4FC] hover:text-[#00519E] text-xs font-semibold text-slate-800 transition-colors">Berita &amp; Prestasi</a>
                                <a href="{{ $isSubpage ? url('/#suara-komunitas') : '#suara-komunitas' }}" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-[#EBF4FC] hover:text-[#00519E] text-xs font-semibold text-slate-800 transition-colors">Testimoni Komunitas</a>
                                <a href="{{ $isSubpage ? url('/#kontak') : '#kontak' }}" @click="mobileMenu = false" class="p-2 rounded-lg hover:bg-[#EBF4FC] hover:text-[#00519E] text-xs font-semibold text-slate-800 transition-colors">Kontak &amp; Lokasi</a>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="pt-3 mt-2 border-t border-slate-200 flex flex-col gap-2">
                    <a href="{{ $cfg['branding']['spmb_url'] ?? 'https://spmb.smkwikrama1garut.sch.id/' }}" target="_blank" rel="noopener" class="btn-jis-pill-gold text-center text-xs py-3 shadow-sm">
                        <span>Daftar SPMB Online Sekarang</span> &rarr;
                    </a>
                </div>
            </div>
        </div>
    </header>
    </div>

