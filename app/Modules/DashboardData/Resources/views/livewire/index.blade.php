<div>
    {{-- Header Modul --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-text-main">Dashboard Data</h1>
            <p class="text-xs sm:text-sm text-text-secondary mt-0.5">Analisis dan rekapitulasi data akademik, rombel, program, dan feeder siswa</p>
        </div>
        @if($currentTa)
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-surface border border-border text-xs text-text-secondary shadow-2xs self-start sm:self-auto">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>T.A. Aktif:</span>
                <span class="font-bold text-text-main">{{ $currentTa->nama ?? $currentTa->tahun }}</span>
            </div>
        @endif
    </div>

    {{-- Modern Pill Tab Bar --}}
    <div class="mb-6 overflow-x-auto scrollbar-none">
        <div class="inline-flex items-center gap-1.5 p-1 rounded-xl bg-surface-hover/80 border border-border">
            @foreach($allowedTabs as $key => $info)
                <a href="{{ route('dashboard-data.index', ['tab' => $key]) }}"
                    wire:navigate
                    class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $activeTab === $key ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface/80' }}">
                    @if($key === 'overview')
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    @elseif($key === 'rayon')
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    @elseif($key === 'jurusan')
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    @elseif($key === 'rombel')
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    @elseif($key === 'sekolah-asal')
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                        </svg>
                    @endif
                    <span>{{ $info['label'] }}</span>
                    @if(!is_null($info['badge']))
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $activeTab === $key ? 'bg-white/20 text-white' : 'bg-surface text-text-muted border border-border' }}">
                            {{ $info['badge'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    {{-- Konten Tab Dinamis (On-Demand) --}}
    @if($activeTab === 'overview')
        <div class="space-y-6">
            {{-- 5 Kartu KPI Ringkasan --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
                {{-- 1. Siswa Aktif --}}
                <div class="bg-surface p-4 rounded-xl shadow-xs border border-border">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-text-secondary">Siswa Aktif</span>
                        <div class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-primary dark:text-blue-400">{{ number_format($totalSiswaAktif) }}</p>
                    <span class="text-[10px] text-text-muted">Total siswa berstatus aktif</span>
                </div>

                {{-- 2. Total Rayon --}}
                <a href="{{ route('dashboard-data.index', ['tab' => 'rayon']) }}" wire:navigate class="bg-surface p-4 rounded-xl shadow-xs border border-border hover:border-primary/50 transition group text-left block">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-text-secondary group-hover:text-primary transition">Total Rayon</span>
                        <div class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-text-main group-hover:text-primary transition">{{ number_format($totalRayon) }}</p>
                    <span class="text-[10px] text-text-muted">Rayon kewilayahan</span>
                </a>

                {{-- 3. Total Jurusan --}}
                <a href="{{ route('dashboard-data.index', ['tab' => 'jurusan']) }}" wire:navigate class="bg-surface p-4 rounded-xl shadow-xs border border-border hover:border-primary/50 transition group text-left block">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-text-secondary group-hover:text-primary transition">Total Jurusan</span>
                        <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-text-main group-hover:text-primary transition">{{ number_format($totalJurusan) }}</p>
                    <span class="text-[10px] text-text-muted">Kompetensi keahlian</span>
                </a>

                {{-- 4. Total Rombel --}}
                <a href="{{ route('dashboard-data.index', ['tab' => 'rombel']) }}" wire:navigate class="bg-surface p-4 rounded-xl shadow-xs border border-border hover:border-primary/50 transition group text-left block">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-text-secondary group-hover:text-primary transition">Total Rombel</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">T.A. Aktif</span>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-text-main group-hover:text-primary transition">{{ number_format($totalRombel) }}</p>
                    <span class="text-[10px] text-text-muted">Kelas berjalan</span>
                </a>

                {{-- 5. Sekolah Asal --}}
                <a href="{{ route('dashboard-data.index', ['tab' => 'sekolah-asal']) }}" wire:navigate class="bg-surface p-4 rounded-xl shadow-xs border border-border hover:border-primary/50 transition group text-left block col-span-2 sm:col-span-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-text-secondary group-hover:text-primary transition">Sekolah Asal</span>
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-text-main group-hover:text-primary transition">{{ number_format($totalSekolahAsal) }}</p>
                    <span class="text-[10px] text-text-muted">Sekolah pengirim (feeder)</span>
                </a>
            </div>

            {{-- Matriks Rekapitulasi Demografi Siswa --}}
            <div class="bg-surface rounded-xl shadow-xs border border-border overflow-hidden">
                {{-- Header Card & Filter Bar --}}
                <div class="p-4 sm:p-5 border-b border-border space-y-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-base font-bold text-text-main">Matriks Demografi Siswa</h2>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-primary/10 text-primary border border-primary/20">
                                    {{ $filteredRombelCount }} Rombel
                                </span>
                            </div>
                            <p class="text-xs text-text-secondary mt-0.5">
                                Rekapitulasi Siswa Aktif per Jurusan, Rombel, Tingkat & Program Keahlian
                            </p>
                        </div>

                        {{-- Gender Ratio & Quick Summary Chips --}}
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50/70 dark:bg-blue-950/50 border border-blue-200/60 dark:border-blue-800/60 text-xs">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                <span class="text-text-secondary">Laki-laki:</span>
                                <span class="font-bold text-blue-700 dark:text-blue-300">{{ number_format($grandTotal['total_l']) }}</span>
                                <span class="text-[10px] text-text-muted">({{ $rasioL }}%)</span>
                            </div>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50/70 dark:bg-rose-950/50 border border-rose-200/60 dark:border-rose-800/60 text-xs">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <span class="text-text-secondary">Perempuan:</span>
                                <span class="font-bold text-rose-700 dark:text-rose-300">{{ number_format($grandTotal['total_p']) }}</span>
                                <span class="text-[10px] text-text-muted">({{ $rasioP }}%)</span>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Bar --}}
                    <div class="pt-3 border-t border-border/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-2.5">
                            {{-- Filter Tingkat --}}
                            <div class="flex items-center gap-1.5 text-xs">
                                <label for="filterTingkat" class="font-semibold text-text-secondary whitespace-nowrap">Tingkat:</label>
                                <select id="filterTingkat" wire:model.live="filterTingkat" class="px-2.5 py-1.5 rounded-lg bg-surface border border-border text-xs text-text-main focus:ring-1 focus:ring-primary focus:border-primary transition">
                                    <option value="">Semua Tingkat</option>
                                    <option value="X">Kelas X</option>
                                    <option value="XI">Kelas XI</option>
                                    <option value="XII">Kelas XII</option>
                                </select>
                            </div>

                            {{-- Filter Jurusan --}}
                            <div class="flex items-center gap-1.5 text-xs">
                                <label for="filterJurusan" class="font-semibold text-text-secondary whitespace-nowrap">Jurusan:</label>
                                <select id="filterJurusan" wire:model.live="filterJurusan" class="px-2.5 py-1.5 rounded-lg bg-surface border border-border text-xs text-text-main focus:ring-1 focus:ring-primary focus:border-primary transition">
                                    <option value="">Semua Jurusan</option>
                                    @foreach($jurusans as $j)
                                        <option value="{{ $j->id }}">{{ $j->kode }} - {{ $j->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Reset Button --}}
                            @if($filterTingkat !== '' || $filterJurusan !== '')
                                <button type="button" wire:click="resetFilters" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Reset
                                </button>
                            @endif
                        </div>

                        {{-- Hint Mobile Scroll --}}
                        <div class="text-[11px] text-text-muted flex items-center gap-1 sm:hidden">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            <span>Geser tabel untuk melihat rincian</span>
                        </div>
                    </div>
                </div>

                {{-- Tabel Matriks Demografi --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-text-secondary">
                        <thead>
                            {{-- Level 1 Header --}}
                            <tr class="bg-surface-hover/80 text-text-main">
                                <th rowspan="2" class="sticky left-0 z-20 bg-surface-hover px-4 py-3 font-bold text-left border-r border-border min-w-[130px] sm:min-w-[150px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)]">
                                    Rombel
                                </th>
                                <th rowspan="2" class="px-3 py-3 text-center font-bold border-r border-border w-20 hidden sm:table-cell">
                                    Jurusan
                                </th>
                                @foreach($programs as $idx => $prog)
                                    <th colspan="3" class="text-center py-2.5 px-3 font-bold border-l border-border {{ $idx % 2 === 0 ? 'bg-blue-50/70 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200' : 'bg-indigo-50/70 dark:bg-indigo-950/40 text-indigo-900 dark:text-indigo-200' }}">
                                        Program {{ $prog->nama }}
                                    </th>
                                @endforeach
                                <th colspan="3" class="text-center py-2.5 px-3 font-black border-l-2 border-border bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200">
                                    Total Rombel
                                </th>
                            </tr>
                            {{-- Level 2 Header --}}
                            <tr class="bg-surface-hover/60 border-t border-border text-[11px] uppercase tracking-wider text-text-secondary font-semibold">
                                @foreach($programs as $idx => $prog)
                                    <th class="px-2.5 py-2 text-center border-l border-border text-blue-600 dark:text-blue-400">L</th>
                                    <th class="px-2.5 py-2 text-center text-rose-600 dark:text-rose-400">P</th>
                                    <th class="px-2.5 py-2 text-center font-bold text-text-main bg-black/[0.02] dark:bg-white/[0.02]">Jml</th>
                                @endforeach
                                <th class="px-2.5 py-2 text-center border-l-2 border-border text-blue-700 dark:text-blue-300 font-bold">L</th>
                                <th class="px-2.5 py-2 text-center text-rose-700 dark:text-rose-300 font-bold">P</th>
                                <th class="px-3 py-2 text-center font-black text-emerald-800 dark:text-emerald-200 bg-emerald-100/50 dark:bg-emerald-900/30">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($matrix as $tingkatKey => $tingkatData)
                                {{-- Separator Tingkat --}}
                                <tr class="bg-surface-hover/90 border-t-2 border-border/80">
                                    <td colspan="{{ 2 + ($programs->count() * 3) + 3 }}" class="px-4 py-2 text-xs font-bold text-text-main">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                                            <span>Tingkat {{ $tingkatKey }} (Kelas {{ $tingkatKey }})</span>
                                            <span class="text-text-muted font-normal text-[11px]">&bull; {{ count($tingkatData['rows']) }} Rombel</span>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Baris Tiap Rombel --}}
                                @foreach($tingkatData['rows'] as $row)
                                    <tr class="hover:bg-surface-hover/50 transition-colors group">
                                        {{-- Sticky Rombel Cell --}}
                                        <td class="sticky left-0 z-10 bg-surface group-hover:bg-surface-hover/90 transition-colors px-4 py-2.5 border-r border-border font-bold text-text-main shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)] whitespace-nowrap">
                                            <span>{{ $row['nama'] }}</span>
                                            <span class="text-[10px] text-text-muted font-normal block sm:hidden">
                                                {{ $row['jurusan_kode'] }} &bull; Tingkat {{ $row['tingkat'] }}
                                            </span>
                                        </td>

                                        {{-- Jurusan --}}
                                        <td class="px-3 py-2.5 text-center border-r border-border hidden sm:table-cell">
                                            <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-surface-hover border border-border text-text-main">
                                                {{ $row['jurusan_kode'] }}
                                            </span>
                                        </td>

                                        {{-- Program Columns --}}
                                        @foreach($programs as $prog)
                                            <td class="px-2.5 py-2.5 text-center border-l border-border/60 {{ $row['programs'][$prog->id]['l'] > 0 ? 'text-text-main font-semibold' : 'text-text-muted/40' }}">
                                                {{ $row['programs'][$prog->id]['l'] ?: '-' }}
                                            </td>
                                            <td class="px-2.5 py-2.5 text-center {{ $row['programs'][$prog->id]['p'] > 0 ? 'text-text-main font-semibold' : 'text-text-muted/40' }}">
                                                {{ $row['programs'][$prog->id]['p'] ?: '-' }}
                                            </td>
                                            <td class="px-2.5 py-2.5 text-center font-bold bg-black/[0.02] dark:bg-white/[0.02] {{ $row['programs'][$prog->id]['subtotal'] > 0 ? 'text-text-main' : 'text-text-muted/40' }}">
                                                {{ $row['programs'][$prog->id]['subtotal'] ?: '-' }}
                                            </td>
                                        @endforeach

                                        {{-- Total Rombel Columns --}}
                                        <td class="px-2.5 py-2.5 text-center border-l-2 border-border/80 font-bold text-blue-600 dark:text-blue-400">
                                            {{ $row['total_l'] }}
                                        </td>
                                        <td class="px-2.5 py-2.5 text-center font-bold text-rose-600 dark:text-rose-400">
                                            {{ $row['total_p'] }}
                                        </td>
                                        <td class="px-3 py-2.5 text-center font-extrabold text-emerald-700 dark:text-emerald-300 bg-emerald-50/50 dark:bg-emerald-950/30 text-sm">
                                            {{ $row['total_all'] }}
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Baris Subtotal per Tingkat --}}
                                <tr class="bg-surface-hover/90 font-bold border-t border-b-2 border-border text-text-main">
                                    <td class="sticky left-0 z-10 bg-surface-hover px-4 py-2.5 border-r border-border shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)] whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-xs font-bold uppercase tracking-wider">Subtotal Tingkat {{ $tingkatKey }}</span>
                                        </div>
                                    </td>
                                    <td class="hidden sm:table-cell border-r border-border text-center text-text-muted">-</td>
                                    @foreach($programs as $prog)
                                        <td class="px-2.5 py-2.5 text-center border-l border-border/80 text-blue-700 dark:text-blue-300">
                                            {{ $tingkatData['subtotal']['programs'][$prog->id]['l'] }}
                                        </td>
                                        <td class="px-2.5 py-2.5 text-center text-rose-700 dark:text-rose-300">
                                            {{ $tingkatData['subtotal']['programs'][$prog->id]['p'] }}
                                        </td>
                                        <td class="px-2.5 py-2.5 text-center font-black bg-black/[0.04] dark:bg-white/[0.04] text-text-main">
                                            {{ $tingkatData['subtotal']['programs'][$prog->id]['subtotal'] }}
                                        </td>
                                    @endforeach
                                    <td class="px-2.5 py-2.5 text-center border-l-2 border-border text-blue-800 dark:text-blue-200 font-bold">
                                        {{ $tingkatData['subtotal']['total_l'] }}
                                    </td>
                                    <td class="px-2.5 py-2.5 text-center text-rose-800 dark:text-rose-200 font-bold">
                                        {{ $tingkatData['subtotal']['total_p'] }}
                                    </td>
                                    <td class="px-3 py-2.5 text-center font-black text-emerald-800 dark:text-emerald-200 bg-emerald-100/70 dark:bg-emerald-900/50 text-sm">
                                        {{ $tingkatData['subtotal']['total_all'] }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 2 + ($programs->count() * 3) + 3 }}" class="px-4 py-12 text-center text-text-secondary">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 text-text-muted/60 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="font-semibold text-text-main">Tidak ada rombel yang cocok</p>
                                            <p class="text-xs text-text-muted mt-1">Coba sesuaikan filter tingkat atau jurusan yang Anda pilih.</p>
                                            <button type="button" wire:click="resetFilters" class="mt-3 px-3 py-1.5 rounded-lg text-xs font-bold text-primary bg-primary/10 hover:bg-primary/20 transition">
                                                Reset Filter
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        @if(count($matrix) > 0)
                            <tfoot>
                                {{-- Baris Grand Total --}}
                                <tr class="bg-primary/10 dark:bg-primary/20 font-black border-t-2 border-primary/50 text-text-main">
                                    <td class="sticky left-0 z-10 bg-primary/15 dark:bg-primary/30 px-4 py-3 border-r border-primary/30 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)] whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                            </svg>
                                            <span class="text-xs font-black uppercase tracking-wider text-primary dark:text-blue-300">TOTAL KESELURUHAN</span>
                                        </div>
                                    </td>
                                    <td class="hidden sm:table-cell border-r border-primary/20 text-center text-text-muted">-</td>
                                    @foreach($programs as $prog)
                                        <td class="px-2.5 py-3 text-center border-l border-primary/20 text-blue-800 dark:text-blue-200 font-bold">
                                            {{ $grandTotal['programs'][$prog->id]['l'] }}
                                        </td>
                                        <td class="px-2.5 py-3 text-center text-rose-800 dark:text-rose-200 font-bold">
                                            {{ $grandTotal['programs'][$prog->id]['p'] }}
                                        </td>
                                        <td class="px-2.5 py-3 text-center font-black bg-primary/10 dark:bg-primary/25 text-text-main">
                                            {{ $grandTotal['programs'][$prog->id]['subtotal'] }}
                                        </td>
                                    @endforeach
                                    <td class="px-2.5 py-3 text-center border-l-2 border-primary/30 text-blue-900 dark:text-blue-100 font-black">
                                        {{ $grandTotal['total_l'] }}
                                    </td>
                                    <td class="px-2.5 py-3 text-center text-rose-900 dark:text-rose-100 font-black">
                                        {{ $grandTotal['total_p'] }}
                                    </td>
                                    <td class="px-3 py-3 text-center font-black text-white bg-primary text-base">
                                        {{ $grandTotal['total_all'] }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    @elseif($activeTab === 'rayon')
        @livewire(\App\Modules\DashboardData\Livewire\Rayon\Index::class, key('tab-rayon'))
    @elseif($activeTab === 'jurusan')
        @livewire(\App\Modules\DashboardData\Livewire\Jurusan\Index::class, key('tab-jurusan'))
    @elseif($activeTab === 'rombel')
        @livewire(\App\Modules\DashboardData\Livewire\Rombel\Index::class, key('tab-rombel'))
    @elseif($activeTab === 'sekolah-asal')
        @livewire(\App\Modules\DashboardData\Livewire\SekolahAsal\Index::class, key('tab-sekolah-asal'))
    @endif
</div>
