<div class="min-h-screen bg-gradient-to-b from-[#F0F6FC] via-[#F8FAFC] to-[#FAF9F6] text-slate-800 antialiased selection:bg-[#00519E] selection:text-white pb-24"
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

    <!-- ── 1. HERO HEADER: FRESH & REFINED PASTEL CELEBRATION ── -->
    <header class="relative pt-10 pb-16 sm:pt-14 sm:pb-20 overflow-hidden">
        <!-- Floating Ambient Glow Orbs -->
        <div class="absolute inset-0 pointer-events-none z-0 overflow-hidden">
            <div class="absolute -top-24 -left-20 w-80 h-80 rounded-full bg-blue-200/35 blur-3xl"></div>
            <div class="absolute top-10 right-0 w-96 h-96 rounded-full bg-amber-100/45 blur-3xl"></div>
            <div class="absolute -bottom-10 left-1/3 w-80 h-80 rounded-full bg-emerald-100/40 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(#CBD5E1_1px,transparent_1px)] [background-size:24px_24px] opacity-35"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-6">
                <a href="{{ url('/') }}" class="hover:text-[#00519E] transition-colors">Beranda</a>
                <span>&rsaquo;</span>
                <a href="{{ url('/prestasi') }}" class="hover:text-[#00519E] transition-colors">Prestasi</a>
                @if($tab !== 'semua')
                    <span>&rsaquo;</span>
                    <span class="text-[#00519E] font-bold capitalize">{{ $tab }}</span>
                @endif
            </nav>

            <div class="text-center max-w-3xl mx-auto space-y-4">
                <!-- Pill Badge -->
                <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-wider shadow-xs bg-white/95 border border-blue-200/80 text-[#00519E] backdrop-blur-xs">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#00519E] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#00519E]"></span>
                    </span>
                    <span>{{ $currentTabInfo['tag'] }}</span>
                </div>

                <!-- Main Hero Heading -->
                <h1 class="text-3xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-slate-900 leading-tight">
                    {{ $currentTabInfo['title_prefix'] }}, <br class="hidden sm:inline">
                    <span class="bg-gradient-to-r from-[#00305F] via-[#00519E] to-[#0284C7] bg-clip-text text-transparent">
                        {{ $currentTabInfo['title_gradient'] }}
                    </span>
                </h1>

                <!-- Subtitle -->
                <p class="text-sm sm:text-base md:text-lg text-slate-600 leading-relaxed font-normal max-w-2xl mx-auto">
                    {{ $currentTabInfo['subtitle'] }}
                </p>
            </div>

            <!-- ── 2. SUMMARY METRIC PASTEL CARDS ── -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mt-12">
                <!-- 1. Total Prestasi (Warm Gold / Amber) -->
                <div class="p-5 sm:p-6 rounded-3xl bg-gradient-to-br from-[#FFFBEB] via-[#FEF3C7]/30 to-white border border-amber-200/80 shadow-xs hover:shadow-md transition-all duration-300 hover:-translate-y-1 group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-xl shadow-xs group-hover:scale-110 transition-transform">
                            🏆
                        </span>
                        <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-200/60">Total</span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-amber-950 tracking-tight">{{ $stats['total'] }}</div>
                    <div class="text-xs font-bold text-amber-800/90 mt-1">Seluruh Capaian Juara</div>
                </div>

                <!-- 2. Prestasi Sekolah (Royal Purple) -->
                <div class="p-5 sm:p-6 rounded-3xl bg-gradient-to-br from-[#FAF5FF] via-[#EDE9FE]/30 to-white border border-purple-200/80 shadow-xs hover:shadow-md transition-all duration-300 hover:-translate-y-1 group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-10 h-10 rounded-2xl bg-purple-100 text-purple-800 flex items-center justify-center text-xl shadow-xs group-hover:scale-110 transition-transform">
                            🏛️
                        </span>
                        <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-900 border border-purple-200/60">Institusi</span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-purple-950 tracking-tight">{{ $stats['sekolah'] }}</div>
                    <div class="text-xs font-bold text-purple-800/90 mt-1">Prestasi Sekolah &amp; Akreditasi</div>
                </div>

                <!-- 3. Prestasi Guru (Sky Blue) -->
                <div class="p-5 sm:p-6 rounded-3xl bg-gradient-to-br from-[#F0F9FF] via-[#E0F2FE]/30 to-white border border-sky-200/80 shadow-xs hover:shadow-md transition-all duration-300 hover:-translate-y-1 group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-10 h-10 rounded-2xl bg-sky-100 text-sky-800 flex items-center justify-center text-xl shadow-xs group-hover:scale-110 transition-transform">
                            👨‍🏫
                        </span>
                        <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-900 border border-sky-200/60">Pendidik</span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-sky-950 tracking-tight">{{ $stats['guru'] }}</div>
                    <div class="text-xs font-bold text-sky-800/90 mt-1">Prestasi Guru &amp; Tendik</div>
                </div>

                <!-- 4. Prestasi Siswa (Mint Emerald) -->
                <div class="p-5 sm:p-6 rounded-3xl bg-gradient-to-br from-[#ECFDF5] via-[#D1FAE5]/30 to-white border border-emerald-200/80 shadow-xs hover:shadow-md transition-all duration-300 hover:-translate-y-1 group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl shadow-xs group-hover:scale-110 transition-transform">
                            🎓
                        </span>
                        <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 border border-emerald-200/60">Santri</span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-emerald-950 tracking-tight">{{ $stats['siswa'] }}</div>
                    <div class="text-xs font-bold text-emerald-800/90 mt-1">Prestasi Siswa Terverifikasi</div>
                </div>
            </div>
        </div>
    </header>

    <!-- ── 3. INTERACTIVE TAB NAVIGATION & FILTER BAR ── -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 -mt-4 mb-10">
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