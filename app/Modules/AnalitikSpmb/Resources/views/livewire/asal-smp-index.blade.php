<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center p-2 rounded-lg bg-primary/10 text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </span>
                <div>
                    <h1 class="text-2xl font-bold text-text-main">Analitik Asal SMP / MTs</h1>
                    <p class="text-xs sm:text-sm text-text-secondary">Peta sebaran feeder penerimaan siswa baru, demografi sekolah asal, dan preferensi jurusan</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @can('analitik_spmb.asal_smp.kelola')
                <button type="button" wire:click="syncSekolah" wire:loading.attr="disabled" class="btn btn-secondary text-xs gap-1.5 shadow-2xs">
                    <svg wire:loading.remove wire:target="syncSekolah" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <svg wire:loading wire:target="syncSekolah" class="animate-spin h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Sinkronkan Siswa</span>
                </button>

                <button type="button" wire:click="openModalKelola()" class="btn btn-secondary text-xs gap-1.5 shadow-2xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>Tambah Master Sekolah</span>
                </button>
            @endcan

            <button type="button" wire:click="exportExcel" wire:loading.attr="disabled" class="btn btn-primary text-xs gap-1.5 shadow-2xs">
                <svg wire:loading.remove wire:target="exportExcel" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <svg wire:loading wire:target="exportExcel" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Export Excel</span>
            </button>

            <button type="button" onclick="window.print()" class="btn btn-secondary text-xs gap-1.5 shadow-2xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Rekap</span>
            </button>
        </div>
    </div>

    {{-- Notification Alerts --}}
    @if (session()->has('success'))
        <div class="rounded-lg p-3 text-sm bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
        </div>
    @endif

    @if ($unmappedCount > 0)
        <div class="rounded-lg p-3 text-sm bg-amber-50 text-amber-900 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Ditemukan <strong>{{ $unmappedCount }}</strong> variasi nama sekolah asal siswa baru yang belum terpetakan ke master.</span>
            </div>
            @can('analitik_spmb.asal_smp.kelola')
                <button type="button" wire:click="syncSekolah" class="btn btn-secondary !py-1 !px-3 !text-xs self-start sm:self-auto bg-amber-100 dark:bg-amber-900 text-amber-900 dark:text-amber-100 hover:bg-amber-200 border-amber-300">
                    Sinkronkan Sekarang
                </button>
            @endcan
        </div>
    @endif

    {{-- Metric Summary Cards (4 Cards Grid) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Sekolah Feeder --}}
        <div class="card p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-text-muted">Total SMP Feeder</span>
                <span class="p-2 rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-text-main">{{ number_format($totalSekolah) }}</span>
                <span class="text-xs text-text-muted">sekolah master</span>
            </div>
            <p class="mt-1 text-xs text-text-secondary">SMP/MTs mitra pengirim terdata</p>
        </div>

        {{-- Card 2: Total Siswa Terdata --}}
        <div class="card p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-text-muted">Total Siswa Terdata</span>
                <span class="p-2 rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-text-main">{{ number_format($totalSiswa) }}</span>
                <span class="text-xs text-text-muted">siswa</span>
            </div>
            <p class="mt-1 text-xs text-text-secondary">Siswa aktif sesuai kriteria filter</p>
        </div>

        {{-- Card 3: Rata-rata per Sekolah --}}
        <div class="card p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-text-muted">Rata-rata per Feeder</span>
                <span class="p-2 rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-text-main">{{ $avgPerSekolah }}</span>
                <span class="text-xs text-text-muted">siswa / sekolah</span>
            </div>
            <p class="mt-1 text-xs text-text-secondary">Rasio distribusi sebaran siswa</p>
        </div>

        {{-- Card 4: Top Kontributor Terbanyak --}}
        <div class="card p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-text-muted">Top #1 Feeder</span>
                <span class="p-2 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </span>
            </div>
            <div class="mt-2">
                <span class="text-lg font-bold text-text-main block truncate" title="{{ $topSekolah->nama_resmi ?? '-' }}">
                    {{ $topSekolah ? Str::limit($topSekolah->nama_resmi, 22) : '-' }}
                </span>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-xs font-bold text-primary">
                        {{ $topSekolah ? $topSekolah->total_siswa . ' Siswa' : '0' }}
                    </span>
                    @if ($topSekolah && $totalSiswa > 0)
                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-primary/10 text-primary">
                            {{ $topSekolah->percentage }}%
                        </span>
                    @endif
                </div>
            </div>
            <p class="mt-1 text-xs text-text-secondary">Kontributor siswa terbanyak</p>
        </div>
    </div>

    {{-- Top 10 Feeder Leaderboard Visual Bar Chart --}}
    @if ($top10List->count() > 0)
        <div class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                <div>
                    <h2 class="text-base font-bold text-text-main flex items-center gap-2">
                        <span>Peringkat 10 Besar Feeder Terbanyak</span>
                        <span class="text-xs font-normal text-text-muted">(Top 10 Sekolah Mitra)</span>
                    </h2>
                    <p class="text-xs text-text-secondary">Klik pada baris sekolah untuk melihat daftar siswa langsung</p>
                </div>
                <span class="text-xs font-medium text-text-muted self-start sm:self-auto bg-surface-subtle px-2.5 py-1 rounded border border-border">
                    Berdasarkan Filter Aktif
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-6 gap-y-3">
                @foreach ($top10List as $idx => $top)
                    @php
                        $maxVal = $top10List->first()->total_siswa ?: 1;
                        $barPct = round(($top->total_siswa / $maxVal) * 100);
                        $rankColor = match($idx) {
                            0 => 'bg-amber-400 text-slate-900 border-amber-300 font-black',
                            1 => 'bg-slate-300 text-slate-800 border-slate-400 font-bold',
                            2 => 'bg-amber-600 text-white border-amber-700 font-bold',
                            default => 'bg-surface-subtle text-text-secondary border-border',
                        };
                    @endphp
                    <div wire:click="openDrawer({{ $top->id }})" class="group p-2.5 rounded-lg border border-border hover:border-primary/50 hover:bg-surface-hover cursor-pointer transition-all">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-6 h-6 flex items-center justify-center rounded-full text-xs border {{ $rankColor }} shrink-0">
                                    {{ $idx + 1 }}
                                </span>
                                <span class="text-sm font-semibold text-text-main truncate group-hover:text-primary transition-colors">
                                    {{ $top->nama_resmi }}
                                </span>
                                <span class="text-[10px] uppercase px-1.5 py-0.2 rounded font-medium bg-surface-subtle text-text-muted shrink-0">
                                    {{ $top->jenis }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="text-sm font-bold text-text-main">{{ $top->total_siswa }}</span>
                                <span class="text-xs text-text-muted">({{ $top->percentage }}%)</span>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="w-full bg-surface-subtle rounded-full h-2 overflow-hidden flex">
                            <div class="bg-primary group-hover:bg-primary-hover transition-all duration-500 rounded-full h-2" style="width: {{ $barPct }}%"></div>
                        </div>

                        <div class="flex items-center justify-between text-[11px] text-text-secondary mt-1.5">
                            <span class="flex items-center gap-2">
                                <span class="text-blue-600 dark:text-blue-400">L: {{ $top->siswa_l }}</span>
                                <span class="text-rose-600 dark:text-rose-400">P: {{ $top->siswa_p }}</span>
                            </span>
                            <span class="text-text-muted truncate">
                                Favorit: <strong class="text-text-main">{{ $top->dominant_jurusan }}</strong>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Filter & Search Toolbar --}}
    <div class="card p-4 space-y-3">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            {{-- Search Bar --}}
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama SMP/MTs, NPSN, atau kecamatan..." class="input pl-9 w-full text-sm">
                @if ($search)
                    <button type="button" wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-text-muted hover:text-text-main">
                        &times;
                    </button>
                @endif
            </div>

            {{-- Sort By --}}
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-text-muted whitespace-nowrap">Urutkan:</label>
                <select wire:model.live="sortBy" class="input !py-1.5 !px-2.5 text-xs">
                    <option value="total_desc">Siswa Terbanyak &darr;</option>
                    <option value="total_asc">Siswa Tersedikit &uarr;</option>
                    <option value="name_asc">Nama Sekolah (A &ndash; Z)</option>
                    <option value="name_desc">Nama Sekolah (Z &ndash; A)</option>
                </select>

                <select wire:model.live="perPage" class="input !py-1.5 !px-2.5 text-xs">
                    <option value="15">15 per hal</option>
                    <option value="30">30 per hal</option>
                    <option value="50">50 per hal</option>
                    <option value="100">100 per hal</option>
                </select>
            </div>
        </div>

        {{-- Row 2: Secondary Dropdown Filters --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-2 pt-2 border-t border-border">
            {{-- Filter Tingkat --}}
            <div>
                <label class="block text-[11px] font-semibold text-text-muted mb-1">Tingkat Kelas</label>
                <select wire:model.live="filterTingkat" class="input !py-1 !px-2 text-xs w-full">
                    <option value="">Semua Tingkat</option>
                    <option value="X">Kelas X (Baru)</option>
                    <option value="XI">Kelas XI</option>
                    <option value="XII">Kelas XII</option>
                </select>
            </div>

            {{-- Filter Tahun Ajaran --}}
            <div>
                <label class="block text-[11px] font-semibold text-text-muted mb-1">Tahun Ajaran</label>
                <select wire:model.live="filterTahunAjaranId" class="input !py-1 !px-2 text-xs w-full">
                    <option value="">Semua Tahun</option>
                    @foreach ($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Jurusan --}}
            <div>
                <label class="block text-[11px] font-semibold text-text-muted mb-1">Jurusan</label>
                <select wire:model.live="filterJurusanId" class="input !py-1 !px-2 text-xs w-full">
                    <option value="">Semua Jurusan</option>
                    @foreach ($jurusans as $j)
                        <option value="{{ $j->id }}">{{ $j->kode }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Tier --}}
            <div>
                <label class="block text-[11px] font-semibold text-text-muted mb-1">Tier Feeder</label>
                <select wire:model.live="filterTier" class="input !py-1 !px-2 text-xs w-full">
                    <option value="">Semua Tier</option>
                    <option value="Utama">Utama (&ge; 20)</option>
                    <option value="Potensial">Potensial (8-19)</option>
                    <option value="Rintisan">Rintisan (&lt; 8)</option>
                </select>
            </div>

            {{-- Filter Bentuk / Jenis --}}
            <div>
                <label class="block text-[11px] font-semibold text-text-muted mb-1">Bentuk Sekolah</label>
                <select wire:model.live="filterJenis" class="input !py-1 !px-2 text-xs w-full">
                    <option value="">Semua Bentuk</option>
                    <option value="SMP">SMP</option>
                    <option value="MTs">MTs</option>
                    <option value="Pesantren">Pesantren</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            {{-- Filter Status Sekolah --}}
            <div>
                <label class="block text-[11px] font-semibold text-text-muted mb-1">Status Negeri/Swasta</label>
                <select wire:model.live="filterStatusSekolah" class="input !py-1 !px-2 text-xs w-full">
                    <option value="">Semua Status</option>
                    <option value="Negeri">Negeri</option>
                    <option value="Swasta">Swasta</option>
                </select>
            </div>

            {{-- Filter Gender --}}
            <div>
                <label class="block text-[11px] font-semibold text-text-muted mb-1">Jenis Kelamin</label>
                <select wire:model.live="filterJk" class="input !py-1 !px-2 text-xs w-full">
                    <option value="">Semua (L/P)</option>
                    <option value="L">Laki-laki (L)</option>
                    <option value="P">Perempuan (P)</option>
                </select>
            </div>
        </div>

        {{-- Filter Badges & Reset Button --}}
        @if ($search || $filterTingkat || $filterTahunAjaranId || $filterJurusanId || $filterTier || $filterJenis || $filterStatusSekolah || $filterJk)
            <div class="flex items-center justify-between pt-2 text-xs text-text-secondary border-t border-border/60">
                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="text-text-muted font-medium">Filter Aktif:</span>
                    @if ($search) <span class="px-2 py-0.5 rounded bg-primary/10 text-primary">Cari: "{{ $search }}"</span> @endif
                    @if ($filterTingkat) <span class="px-2 py-0.5 rounded bg-primary/10 text-primary">Tingkat: {{ $filterTingkat }}</span> @endif
                    @if ($filterTier) <span class="px-2 py-0.5 rounded bg-primary/10 text-primary">Tier: {{ $filterTier }}</span> @endif
                    @if ($filterJenis) <span class="px-2 py-0.5 rounded bg-primary/10 text-primary">Bentuk: {{ $filterJenis }}</span> @endif
                    @if ($filterStatusSekolah) <span class="px-2 py-0.5 rounded bg-primary/10 text-primary">{{ $filterStatusSekolah }}</span> @endif
                    @if ($filterJk) <span class="px-2 py-0.5 rounded bg-primary/10 text-primary">JK: {{ $filterJk }}</span> @endif
                </div>
                <button type="button" wire:click="resetFilters" class="text-rose-600 hover:text-rose-700 font-semibold text-xs underline">
                    Reset Filter
                </button>
            </div>
        @endif
    </div>

    {{-- Main Data Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-surface-subtle text-text-muted text-xs uppercase font-semibold border-b border-border">
                    <tr>
                        <th class="py-3 px-3 text-center w-12">No</th>
                        <th class="py-3 px-4 min-w-[200px]">Nama Sekolah Asal (SMP / MTs)</th>
                        <th class="py-3 px-3 text-center">Tier</th>
                        <th class="py-3 px-3 text-right">Total Siswa</th>
                        <th class="py-3 px-3 min-w-[140px]">Kontribusi (%)</th>
                        <th class="py-3 px-3 text-center">Gender (L/P)</th>
                        <th class="py-3 px-3 text-center">Favorit</th>
                        @foreach ($jurusans as $j)
                            <th class="py-3 px-2 text-center text-[11px]" title="{{ $j->nama }}">{{ $j->kode }}</th>
                        @endforeach
                        <th class="py-3 px-3 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-text-main">
                    @forelse ($sekolahs as $index => $item)
                        @php
                            $rowNum = ($sekolahs->currentPage() - 1) * $sekolahs->perPage() + $index + 1;
                            $tierBadge = match($item->tier) {
                                'Utama' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950/50 dark:text-amber-300',
                                'Potensial' => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-950/50 dark:text-blue-300',
                                default => 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300',
                            };
                            $jenisBadge = match($item->jenis) {
                                'MTs' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300',
                                'Pesantren' => 'bg-purple-100 text-purple-800 dark:bg-purple-950/50 dark:text-purple-300',
                                default => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300',
                            };
                        @endphp
                        <tr class="hover:bg-surface-hover/70 transition-colors">
                            <td class="py-3 px-3 text-center text-xs text-text-muted font-mono">{{ $rowNum }}</td>
                            
                            {{-- Nama Sekolah --}}
                            <td class="py-3 px-4">
                                <div class="flex flex-col">
                                    <button type="button" wire:click="openDrawer({{ $item->id }})" class="text-left font-bold text-text-main hover:text-primary transition-colors inline-flex items-center gap-1.5 group">
                                        <span>{{ $item->nama_resmi }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-text-muted group-hover:text-primary group-hover:translate-x-0.5 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="text-[10px] font-semibold px-1.5 py-0.2 rounded {{ $jenisBadge }}">
                                            {{ $item->jenis }}
                                        </span>
                                        <span class="text-[10px] px-1.5 py-0.2 rounded bg-surface-subtle text-text-muted border border-border">
                                            {{ $item->status_sekolah }}
                                        </span>
                                        @if ($item->kecamatan)
                                            <span class="text-[11px] text-text-muted">&bull; Kec. {{ $item->kecamatan }}</span>
                                        @endif
                                        @if ($item->npsn)
                                            <span class="text-[11px] text-text-muted">&bull; NPSN: {{ $item->npsn }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Tier --}}
                            <td class="py-3 px-3 text-center">
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full border {{ $tierBadge }}">
                                    {{ $item->tier }}
                                </span>
                            </td>

                            {{-- Total Siswa --}}
                            <td class="py-3 px-3 text-right">
                                <span class="text-sm font-bold text-text-main font-mono">{{ number_format($item->total_siswa) }}</span>
                            </td>

                            {{-- Persentase Kontribusi --}}
                            <td class="py-3 px-3">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-text-main font-mono">{{ $item->percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-surface-subtle rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-primary rounded-full h-1.5" style="width: {{ min(100, $item->percentage * 3) }}%"></div>
                                    </div>
                                </div>
                            </td>

                            {{-- Gender (L/P) --}}
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5 text-xs font-mono">
                                    <span class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 font-medium" title="Laki-laki">
                                        L: {{ $item->siswa_l }}
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 font-medium" title="Perempuan">
                                        P: {{ $item->siswa_p }}
                                    </span>
                                </div>
                            </td>

                            {{-- Jurusan Terbanyak --}}
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded bg-primary-soft text-primary">
                                    {{ $item->dominant_jurusan }}
                                </span>
                            </td>

                            {{-- Jurusan Breakdown Columns --}}
                            @foreach ($jurusans as $j)
                                @php $cnt = (int) ($item->{'jurusan_' . $j->id} ?? 0); @endphp
                                <td class="py-3 px-2 text-center text-xs font-mono {{ $cnt > 0 ? 'font-bold text-text-main' : 'text-text-muted/60' }}">
                                    {{ $cnt ?: '-' }}
                                </td>
                            @endforeach

                            {{-- Aksi --}}
                            <td class="py-3 px-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <button type="button" wire:click="openDrawer({{ $item->id }})" class="btn btn-secondary !py-1 !px-2 !text-xs gap-1 shadow-2xs" title="Lihat Siswa">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Siswa</span>
                                    </button>

                                    @can('analitik_spmb.asal_smp.kelola')
                                        <button type="button" wire:click="openModalKelola({{ $item->id }})" class="p-1 rounded text-text-secondary hover:text-text-main hover:bg-surface-subtle" title="Kelola Master & Alias">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 8 + count($jurusans) }}" class="py-12 text-center text-text-muted">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-text-muted/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span class="text-sm font-medium">Tidak ada data sekolah asal yang sesuai dengan filter.</span>
                                    <button type="button" wire:click="resetFilters" class="btn btn-secondary !py-1 !px-3 text-xs mt-1">
                                        Reset Filter
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($sekolahs->hasPages())
            <div class="p-4 border-t border-border">
                {{ $sekolahs->links() }}
            </div>
        @endif
    </div>

    {{-- Slide-over Drawer: Daftar Siswa dari SMP Terpilih --}}
    @if ($showDrawer && $drawerSekolah)
        <div class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" wire:click="closeDrawer"></div>

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
                <div class="w-screen max-w-2xl bg-surface shadow-2xl flex flex-col border-l border-border">
                    {{-- Drawer Header --}}
                    <div class="p-4 sm:p-5 border-b border-border bg-surface-subtle flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-primary-soft text-primary uppercase">
                                    {{ $drawerSekolah->jenis }} &bull; {{ $drawerSekolah->status_sekolah }}
                                </span>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full border {{ match($drawerSekolah->tier) { 'Utama' => 'bg-amber-100 text-amber-800 border-amber-300', 'Potensial' => 'bg-blue-100 text-blue-800 border-blue-300', default => 'bg-slate-100 text-slate-700 border-slate-300'} }}">
                                    {{ $drawerSekolah->tier }}
                                </span>
                            </div>
                            <h2 class="text-lg font-bold text-text-main mt-1 truncate" id="slide-over-title">
                                {{ $drawerSekolah->nama_resmi }}
                            </h2>
                            <p class="text-xs text-text-secondary mt-0.5">
                                NPSN: {{ $drawerSekolah->npsn ?: '-' }} &bull; Kec. {{ $drawerSekolah->kecamatan ?: '-' }}, {{ $drawerSekolah->kabupaten_kota ?: 'Garut' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" wire:click="exportSiswaSekolah({{ $drawerSekolah->id }})" class="btn btn-secondary !py-1.5 !px-2.5 !text-xs gap-1 shadow-2xs" title="Download Excel Siswa">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="hidden sm:inline">Excel</span>
                            </button>
                            <button type="button" wire:click="closeDrawer" class="p-2 rounded-lg text-text-muted hover:text-text-main hover:bg-surface border border-border">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Drawer Filter Toolbar --}}
                    <div class="p-3 bg-surface border-b border-border grid grid-cols-1 sm:grid-cols-4 gap-2">
                        <div class="sm:col-span-2 relative">
                            <input type="text" wire:model.live.debounce.250ms="drawerSearch" placeholder="Cari nama / NISN..." class="input !py-1 !px-2.5 text-xs w-full">
                        </div>
                        <div>
                            <select wire:model.live="drawerFilterTingkat" class="input !py-1 !px-2 text-xs w-full">
                                <option value="">Semua Tingkat</option>
                                <option value="X">Kelas X</option>
                                <option value="XI">Kelas XI</option>
                                <option value="XII">Kelas XII</option>
                            </select>
                        </div>
                        <div>
                            <select wire:model.live="drawerFilterJurusan" class="input !py-1 !px-2 text-xs w-full">
                                <option value="">Semua Jurusan</option>
                                @foreach ($jurusans as $j)
                                    <option value="{{ $j->id }}">{{ $j->kode }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Drawer Content: Table Siswa --}}
                    <div class="flex-1 overflow-y-auto p-4 space-y-3">
                        <div class="flex items-center justify-between text-xs text-text-muted">
                            <span>Menampilkan <strong>{{ $drawerSiswas->count() }}</strong> siswa terdaftar:</span>
                        </div>

                        <div class="border border-border rounded-lg overflow-hidden">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead class="bg-surface-subtle text-text-muted uppercase font-semibold border-b border-border">
                                    <tr>
                                        <th class="py-2 px-2 text-center w-8">No</th>
                                        <th class="py-2 px-3">Nama Siswa / NISN</th>
                                        <th class="py-2 px-2 text-center">JK</th>
                                        <th class="py-2 px-2 text-center">Rombel</th>
                                        <th class="py-2 px-2 text-center">Jurusan</th>
                                        <th class="py-2 px-2 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border text-text-main">
                                    @forelse ($drawerSiswas as $idx => $ds)
                                        <tr class="hover:bg-surface-hover/70 transition-colors">
                                            <td class="py-2 px-2 text-center text-text-muted font-mono">{{ $idx + 1 }}</td>
                                            <td class="py-2 px-3">
                                                <div class="font-bold text-text-main">{{ $ds->nama }}</div>
                                                <div class="text-[10px] text-text-muted font-mono">
                                                    NISN: {{ $ds->nisn ?: '-' }} &bull; NIS: {{ $ds->nipd ?: '-' }}
                                                </div>
                                            </td>
                                            <td class="py-2 px-2 text-center">
                                                <span class="px-1 py-0.2 rounded text-[10px] font-bold {{ $ds->jk === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-rose-50 text-rose-700' }}">
                                                    {{ $ds->jk }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-2 text-center font-semibold">
                                                {{ $ds->rombel?->nama ?: '-' }}
                                            </td>
                                            <td class="py-2 px-2 text-center">
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-surface-subtle text-text-secondary border border-border">
                                                    {{ $ds->rombel?->jurusan?->kode ?: '-' }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-2 text-center">
                                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-medium {{ $ds->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                                    {{ ucfirst($ds->status ?: 'aktif') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="py-8 text-center text-text-muted">
                                                Tidak ada data siswa yang cocok dengan filter drawer ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Drawer Footer --}}
                    <div class="p-3 border-t border-border bg-surface-subtle flex items-center justify-between">
                        <span class="text-xs text-text-secondary">
                            Variasi nama asal: <strong>{{ count($drawerSekolah->all_names) }}</strong> alias
                        </span>
                        <button type="button" wire:click="closeDrawer" class="btn btn-secondary !py-1.5 !px-3 text-xs">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Kelola Master & Alias Sekolah --}}
    @if ($showModalKelola)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" wire:click="closeModalKelola"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-surface rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-border">
                    <form wire:submit.prevent="saveMasterSekolah">
                        {{-- Modal Header --}}
                        <div class="p-4 sm:p-5 border-b border-border bg-surface-subtle flex items-center justify-between">
                            <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span>{{ $editingMasterId ? 'Edit Master & Alias Sekolah' : 'Tambah Master Sekolah Baru' }}</span>
                            </h3>
                            <button type="button" wire:click="closeModalKelola" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto">
                            {{-- Nama Resmi --}}
                            <div>
                                <label class="label font-semibold">Nama Resmi Sekolah (Standar Master) <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model="formNamaResmi" placeholder="Contoh: SMP Negeri 1 Garut" class="input w-full">
                                @error('formNamaResmi') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="label font-semibold">NPSN</label>
                                    <input type="text" wire:model="formNpsn" placeholder="Nomor Pokok Sekolah Nasional" class="input w-full font-mono">
                                    @error('formNpsn') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="label font-semibold">Tier Feeder <span class="text-rose-500">*</span></label>
                                    <select wire:model="formTier" class="input w-full">
                                        <option value="Utama">Utama (&ge; 20 Siswa)</option>
                                        <option value="Potensial">Potensial (8-19 Siswa)</option>
                                        <option value="Rintisan">Rintisan (&lt; 8 Siswa)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="label font-semibold">Bentuk Sekolah <span class="text-rose-500">*</span></label>
                                    <select wire:model="formJenis" class="input w-full">
                                        <option value="SMP">SMP</option>
                                        <option value="MTs">MTs</option>
                                        <option value="Pesantren">Pesantren</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="label font-semibold">Status Sekolah <span class="text-rose-500">*</span></label>
                                    <select wire:model="formStatusSekolah" class="input w-full">
                                        <option value="Negeri">Negeri</option>
                                        <option value="Swasta">Swasta</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="label font-semibold">Kecamatan</label>
                                    <input type="text" wire:model="formKecamatan" placeholder="Contoh: Tarogong Kidul" class="input w-full">
                                </div>
                                <div>
                                    <label class="label font-semibold">Kabupaten / Kota</label>
                                    <input type="text" wire:model="formKabupatenKota" placeholder="Garut" class="input w-full">
                                </div>
                            </div>

                            <div>
                                <label class="label font-semibold">Catatan Tambahan</label>
                                <textarea wire:model="formCatatan" rows="2" placeholder="Catatan internal mitra sekolah..." class="input w-full text-xs"></textarea>
                            </div>

                            {{-- Bagian Pemetaan Alias (Hanya jika editing master sudah tersimpan) --}}
                            @if ($editingMasterId && $editingMaster)
                                <div class="pt-4 border-t border-border space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-bold text-text-main">Pemetaan Variasi Penulisan (Alias)</h4>
                                            <p class="text-[11px] text-text-secondary">Seluruh variasi teks di bawah ini akan otomatis dikelompokkan ke master <strong>{{ $editingMaster->nama_resmi }}</strong></p>
                                        </div>
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded bg-primary/10 text-primary">
                                            {{ $editingMaster->aliases->count() }} Alias
                                        </span>
                                    </div>

                                    {{-- Form Tambah Alias --}}
                                    <div class="flex items-center gap-2">
                                        <input type="text" wire:model="newAliasInput" placeholder="Ketik variasi nama teks mentah (misal: SMPN 1 GARUT)..." class="input text-xs flex-1">
                                        <button type="button" wire:click="addAlias" class="btn btn-secondary !py-2 !px-3 text-xs gap-1 shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            <span>Tambah Alias</span>
                                        </button>
                                    </div>
                                    @error('newAliasInput') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror

                                    {{-- Daftar Badge Alias --}}
                                    <div class="flex flex-wrap gap-1.5 p-3 rounded-lg bg-surface-subtle border border-border min-h-[60px] max-h-36 overflow-y-auto">
                                        @forelse ($editingMaster->aliases as $alias)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs bg-surface border border-border text-text-main shadow-2xs">
                                                <span>{{ $alias->alias_raw }}</span>
                                                @if (strtolower($alias->alias_raw) !== strtolower($editingMaster->nama_resmi))
                                                    <button type="button" wire:click="removeAlias({{ $alias->id }})" class="text-rose-400 hover:text-rose-600 font-bold ml-0.5" title="Hapus alias">&times;</button>
                                                @endif
                                            </span>
                                        @empty
                                            <span class="text-xs text-text-muted italic">Belum ada variasi alias tambahan.</span>
                                        @endforelse
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Modal Footer --}}
                        <div class="p-4 border-t border-border bg-surface-subtle flex items-center justify-between">
                            <div>
                                @if ($editingMasterId)
                                    <button type="button" wire:confirm="Yakin ingin menghapus master sekolah ini beserta seluruh pemetaan aliasnya?" wire:click="deleteMaster({{ $editingMasterId }})" class="btn btn-danger !py-1.5 !px-3 text-xs">
                                        Hapus Master
                                    </button>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="closeModalKelola" class="btn btn-secondary !py-1.5 !px-3 text-xs">
                                    Batal
                                </button>
                                <button type="submit" class="btn btn-primary !py-1.5 !px-4 text-xs">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
