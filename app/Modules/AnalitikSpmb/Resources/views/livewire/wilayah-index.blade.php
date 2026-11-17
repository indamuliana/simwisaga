<div class="space-y-6">
    {{-- Header & Navigasi --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-border">
        <div>
            <div class="flex items-center gap-2 text-xs text-text-muted mb-1 font-mono">
                <a href="{{ route('analitik-spmb.asal-smp') }}" class="hover:text-primary transition-colors">Analitik SPMB</a>
                <span>/</span>
                <span class="text-text-main font-medium">Sebaran Wilayah</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-semibold text-text-main tracking-tight flex items-center gap-2.5">
                <span class="p-1.5 rounded-lg bg-surface-subtle border border-border text-text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <span>Sebaran Wilayah Asal Siswa</span>
            </h1>
            <p class="text-xs sm:text-sm text-text-secondary mt-1">Peta demografis tempat tinggal pendaftar dan standarisasi data wilayah siswa.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('analitik-spmb.asal-smp') }}" class="btn btn-secondary text-xs gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span>Asal SMP</span>
            </a>

            <a href="{{ route('analitik-spmb.master-smp') }}" class="btn btn-secondary text-xs gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Master SMP</span>
            </a>

            <a href="{{ route('analitik-spmb.perbaikan-smp') }}" class="btn btn-secondary text-xs gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                <span>Perbaikan SMP</span>
            </a>

            <a href="{{ route('kesiswaan.pemetaan-alamat') }}" class="btn btn-secondary text-xs gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Pemetaan Alamat (Kesiswaan)</span>
            </a>

            @can('analitik_spmb.wilayah.kelola')
                <button type="button" wire:click="autoDetectWilayah" wire:loading.attr="disabled" class="btn btn-secondary text-xs gap-1.5" title="Deteksi otomatis wilayah dari basis data Master SMP & Sekolah Asal">
                    <svg wire:loading.remove wire:target="autoDetectWilayah" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <svg wire:loading wire:target="autoDetectWilayah" class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="autoDetectWilayah">Auto-Detect Wilayah</span>
                    <span wire:loading wire:target="autoDetectWilayah">Menganalisis...</span>
                </button>
            @endcan

            @can('analitik_spmb.wilayah.ekspor')
                <button type="button" wire:click="exportExcelRekap" wire:loading.attr="disabled" class="btn btn-primary text-xs gap-1.5">
                    <svg wire:loading.remove wire:target="exportExcelRekap" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <svg wire:loading wire:target="exportExcelRekap" class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Export Excel</span>
                </button>
            @endcan

            <button type="button" onclick="window.print()" class="btn btn-secondary text-xs gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak</span>
            </button>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if (session('success'))
        <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-200 text-xs flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="text-emerald-700 hover:text-emerald-900" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    {{-- Kartu Ringkasan Statistik (5 Kartu) --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
        {{-- Total Siswa --}}
        <div class="p-4 rounded-xl border border-border bg-surface flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs text-text-secondary font-medium">
                <span>Total Siswa</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div class="mt-2 text-2xl font-semibold font-mono tabular-nums text-text-main">
                {{ number_format($analytics['totalSiswa'] ?? 0, 0, ',', '.') }}
            </div>
            <div class="text-xs text-text-muted mt-1">Siswa terfilter</div>
        </div>

        {{-- Negara --}}
        <div class="p-4 rounded-xl border border-border bg-surface flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs text-text-secondary font-medium">
                <span>Negara Asal</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="mt-2 text-2xl font-semibold font-mono tabular-nums text-text-main">
                {{ $analytics['statNegara'] ?? 0 }} <span class="text-xs font-normal text-text-muted">negara</span>
            </div>
            <div class="text-xs text-text-muted mt-1">Indonesia & luar negeri</div>
        </div>

        {{-- Provinsi --}}
        <div class="p-4 rounded-xl border border-border bg-surface flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs text-text-secondary font-medium">
                <span>Provinsi</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
            </div>
            <div class="mt-2 text-2xl font-semibold font-mono tabular-nums text-text-main">
                {{ $analytics['statProvinsi'] ?? 0 }} <span class="text-xs font-normal text-text-muted">provinsi</span>
            </div>
            <div class="text-xs text-text-muted mt-1">Wilayah tingkat I</div>
        </div>

        {{-- Kab / Kota --}}
        <div class="p-4 rounded-xl border border-border bg-surface flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs text-text-secondary font-medium">
                <span>Kabupaten / Kota</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div class="mt-2 text-2xl font-semibold font-mono tabular-nums text-text-main">
                {{ $analytics['statKabupaten'] ?? 0 }} <span class="text-xs font-normal text-text-muted">kab/kota</span>
            </div>
            <div class="text-xs text-text-muted mt-1">Wilayah tingkat II</div>
        </div>

        {{-- Siswa Luar Negeri --}}
        <div class="p-4 rounded-xl border border-border bg-surface flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs text-text-secondary font-medium">
                <span>Siswa Luar Negeri</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                </svg>
            </div>
            <div class="mt-2 text-2xl font-semibold font-mono tabular-nums text-text-main">
                {{ $analytics['statLuarNegeri'] ?? 0 }} <span class="text-xs font-normal text-text-muted">siswa</span>
            </div>
            <div class="text-xs text-text-muted mt-1">Sabah, Malaysia</div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="p-4 rounded-xl border border-border bg-surface space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-border pb-3">
            <div class="text-xs font-medium text-text-secondary flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span>Filter & pencarian wilayah</span>
            </div>
            <button type="button" wire:click="resetFilters" class="text-xs text-text-muted hover:text-text-main transition-colors flex items-center gap-1 self-start sm:self-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Reset filter</span>
            </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            {{-- Filter Cakupan --}}
            <div>
                <label class="block text-xs font-medium text-text-muted mb-1">Cakupan</label>
                <select wire:model.live="filterCakupan" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                    <option value="semua">Semua Wilayah</option>
                    <option value="dalam_negeri">Dalam Negeri</option>
                    <option value="luar_negeri">Luar Negeri</option>
                </select>
            </div>

            {{-- Filter Provinsi --}}
            <div>
                <label class="block text-xs font-medium text-text-muted mb-1">Provinsi</label>
                <select wire:model.live="filterProvinsi" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                    <option value="">Semua Provinsi</option>
                    @foreach ($provinsiOptions as $prov)
                        <option value="{{ $prov }}">{{ $prov }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Kabupaten / Kota --}}
            <div>
                <label class="block text-xs font-medium text-text-muted mb-1">Kab / Kota</label>
                <select wire:model.live="filterKabupaten" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                    <option value="">Semua Kab/Kota</option>
                    @foreach ($kabupatenOptions as $kab)
                        <option value="{{ $kab }}">{{ $kab }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Tahun Ajaran --}}
            <div>
                <label class="block text-xs font-medium text-text-muted mb-1">Tahun ajaran</label>
                <select wire:model.live="filterTahunAjaranId" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                    <option value="">Semua TA</option>
                    @foreach ($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->tahun }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Tingkat --}}
            <div>
                <label class="block text-xs font-medium text-text-muted mb-1">Tingkat</label>
                <select wire:model.live="filterTingkat" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                    <option value="">Semua Tingkat</option>
                    <option value="X">Kelas X</option>
                    <option value="XI">Kelas XI</option>
                    <option value="XII">Kelas XII</option>
                </select>
            </div>

            {{-- Filter Jurusan --}}
            <div>
                <label class="block text-xs font-medium text-text-muted mb-1">Jurusan</label>
                <select wire:model.live="filterJurusanId" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                    <option value="">Semua Jurusan</option>
                    @foreach ($jurusans as $j)
                        <option value="{{ $j->id }}">{{ $j->kode }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter JK --}}
            <div>
                <label class="block text-xs font-medium text-text-muted mb-1">Jenis kelamin</label>
                <select wire:model.live="filterJk" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                    <option value="">Semua (L/P)</option>
                    <option value="L">Laki-laki (L)</option>
                    <option value="P">Perempuan (P)</option>
                </select>
            </div>

            {{-- Filter Status --}}
            <div>
                <label class="block text-xs font-medium text-text-muted mb-1">Status siswa</label>
                <select wire:model.live="filterStatus" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                    <option value="aktif">Siswa Aktif</option>
                    <option value="semua">Semua Status</option>
                    <option value="lulus">Lulus</option>
                    <option value="mutasi">Mutasi / Pindah</option>
                </select>
            </div>
        </div>

        {{-- Search Bar --}}
        <div class="relative pt-1">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kecamatan, kabupaten/kota, desa, atau alamat..." class="w-full text-xs py-2 pl-9 pr-3 rounded-lg border border-border bg-surface-subtle text-text-main placeholder-text-muted focus:ring-1 focus:ring-primary focus:border-primary">
            <div class="absolute inset-y-0 left-0 pt-1 pl-2.5 flex items-center pointer-events-none text-text-muted">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Leaderboard Top 10 Feeder Kab / Kota (Tabel Mini Compact - Collapsible) --}}
    @if (!empty($analytics['topKabupaten']) && $analytics['topKabupaten']->isNotEmpty())
        <div class="rounded-xl border border-border bg-surface overflow-hidden transition-all">
            <div 
                wire:click="toggleTopKabupaten" 
                class="p-4 sm:p-4.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 cursor-pointer select-none hover:bg-surface-subtle/60 transition-colors {{ $showTopKabupaten ? 'border-b border-border bg-surface-subtle/30' : '' }}"
            >
                <div class="flex items-center gap-3">
                    <button 
                        type="button" 
                        class="p-1.5 rounded-lg border border-border bg-surface hover:bg-surface-subtle text-text-muted hover:text-text-main transition-colors focus:outline-hidden"
                        title="{{ $showTopKabupaten ? 'Sembunyikan' : 'Tampilkan' }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200 {{ $showTopKabupaten ? 'rotate-180 text-primary' : 'text-text-muted' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-semibold text-text-main">Top 10 Kabupaten / Kota Feeder</h2>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-medium text-text-muted bg-surface-subtle border border-border">
                                {{ $analytics['topKabupaten']->count() }} wilayah
                            </span>
                        </div>
                        <p class="text-xs text-text-muted mt-0.5">Wilayah penyumbang pendaftar terbanyak berdasarkan data domisili siswa.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto" @click.stop>
                    <button 
                        type="button" 
                        wire:click="toggleTopKabupaten" 
                        class="btn btn-secondary !py-1 !px-2.5 !text-xs gap-1.5"
                    >
                        <span>{{ $showTopKabupaten ? 'Sembunyikan' : 'Tampilkan' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform duration-200 {{ $showTopKabupaten ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
            </div>

            @if ($showTopKabupaten)
                <div class="p-4 sm:p-5 space-y-3">
                    <div class="overflow-x-auto rounded-lg border border-border">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-surface-subtle text-text-muted font-medium border-b border-border">
                                <tr>
                                    <th class="py-2 px-3 text-center w-14">Peringkat</th>
                                    <th class="py-2 px-3">Kabupaten / Kota</th>
                                    <th class="py-2 px-3">Negara / Provinsi</th>
                                    <th class="py-2 px-3 text-center w-28">Total Siswa</th>
                                    <th class="py-2 px-3 w-48">Porsi kontribusi</th>
                                    <th class="py-2 px-3 text-center w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border text-text-main">
                                @foreach ($analytics['topKabupaten'] as $idx => $tk)
                                    <tr class="hover:bg-surface-hover/70 transition-colors">
                                        <td class="py-2 px-3 text-center font-mono tabular-nums {{ $idx < 3 ? 'text-text-main font-semibold' : 'text-text-muted' }}">
                                            #{{ $idx + 1 }}
                                        </td>
                                        <td class="py-2 px-3 font-medium text-text-main">
                                            {{ $tk->kab_kota }}
                                        </td>
                                        <td class="py-2 px-3 text-text-secondary">
                                            @if ($tk->neg !== 'Indonesia')
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-mono bg-surface-subtle text-text-secondary border border-border/60">
                                                    {{ $tk->neg }} ({{ $tk->prov }})
                                                </span>
                                            @else
                                                <span>{{ $tk->prov ?: '-' }}</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-3 text-center font-mono tabular-nums font-semibold text-text-main">
                                            {{ number_format($tk->total) }}
                                        </td>
                                        <td class="py-2 px-3">
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1 bg-surface-subtle border border-border/40 rounded-full h-1.5 overflow-hidden">
                                                    <div class="h-1.5 rounded-full {{ $idx === 0 ? 'bg-primary' : 'bg-primary/70' }}" style="width: {{ min(100, $tk->percentage * 2) }}%"></div>
                                                </div>
                                                <span class="font-mono tabular-nums text-xs text-text-muted w-10 text-right">{{ $tk->percentage }}%</span>
                                            </div>
                                        </td>
                                        <td class="py-2 px-3 text-center">
                                            <button type="button" wire:click="openDrawer('{{ addslashes($tk->neg) }}', '{{ addslashes($tk->prov) }}', '{{ addslashes($tk->kab_kota) }}')" class="btn btn-secondary !py-1 !px-2.5 !text-xs gap-1 hover:border-primary hover:text-primary whitespace-nowrap">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>Lihat</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @endif

        {{-- Tabel Hirarki Sebaran Wilayah --}}
    <style>
        /* Gradasi Warna Latar Pivot Tree (Semakin ke bawah semakin memutih/pudar) */
        .tree-row-negara { background-color: #e2e8f0 !important; }
        .tree-row-provinsi { background-color: #edf2f7 !important; }
        .tree-row-kabupaten { background-color: #f5f7fa !important; }
        .tree-row-kecamatan { background-color: #fafbfd !important; }
        .tree-row-desa { background-color: #ffffff !important; }

        .dark .tree-row-negara { background-color: #1e293b !important; }
        .dark .tree-row-provinsi { background-color: #182234 !important; }
        .dark .tree-row-kabupaten { background-color: #131b29 !important; }
        .dark .tree-row-kecamatan { background-color: #0f1520 !important; }
        .dark .tree-row-desa { background-color: #0a0e16 !important; }

        /* Indentasi Bertingkat Presisi */
        .tree-indent-1 { padding-left: 0.5rem !important; }
        .tree-indent-2 { padding-left: 2rem !important; }
        .tree-indent-3 { padding-left: 3.5rem !important; }
        .tree-indent-4 { padding-left: 5rem !important; }
        .tree-indent-5 { padding-left: 6.5rem !important; }
    </style>

    <div class="rounded-xl border border-border bg-surface overflow-hidden">
        <div class="p-4 border-b border-border bg-surface-subtle flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-semibold text-text-main">Tabel Hirarki Sebaran Wilayah Asal Siswa</h2>
                <p class="text-xs text-text-muted mt-0.5">
                    Struktur berjenjang: Negara ➔ Provinsi ➔ Kabupaten/Kota ➔ Kecamatan ➔ Kelurahan/Desa.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" wire:click="expandAll" class="btn btn-secondary !py-1 !px-2.5 !text-xs gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7" />
                    </svg>
                    <span>Buka Semua</span>
                </button>
                <button type="button" wire:click="collapseAll" class="btn btn-secondary !py-1 !px-2.5 !text-xs gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 15l7-7 7 7" />
                    </svg>
                    <span>Tutup Semua</span>
                </button>
                <span class="text-xs text-text-muted font-mono ml-1 hidden md:inline">
                    Total: <strong>{{ count($tree) }}</strong> negara
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-100 dark:bg-slate-800 text-text-muted font-medium border-b border-border">
                    <tr>
                        <th class="py-2.5 px-3 min-w-[280px]">Wilayah asal siswa</th>
                        <th class="py-2.5 px-3 text-center w-24">Total Siswa</th>
                        <th class="py-2.5 px-2 text-center w-12">L</th>
                        <th class="py-2.5 px-2 text-center w-12">P</th>
                        <th class="py-2.5 px-2 text-center w-14">Ung</th>
                        <th class="py-2.5 px-2 text-center w-14">Reg</th>
                        <th class="py-2.5 px-2 text-center w-14">TJKT</th>
                        <th class="py-2.5 px-2 text-center w-14">PPLG</th>
                        <th class="py-2.5 px-2 text-center w-14">PMN</th>
                        <th class="py-2.5 px-2 text-center w-14">HTL</th>
                        <th class="py-2.5 px-3 text-center w-20">Porsi (%)</th>
                        <th class="py-2.5 px-3 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-text-main">
                    @forelse ($tree as $nNode)
                        @php
                            $nExpanded = !empty($expandedNodes[$nNode['key']]);
                            $hasChildrenN = !empty($nNode['children']);
                            $nCounts = $nNode['jurusan_counts'] ?? [];
                            $nTjkt = $nCounts['TJKT'] ?? 0;
                            $nPplg = $nCounts['PPLG'] ?? 0;
                            $nPmn  = $nCounts['PMN'] ?? 0;
                            $nHtl  = $nCounts['HTL'] ?? 0;
                        @endphp
                        {{-- LEVEL 1: NEGARA (Paling Kontras / Slate-200) --}}
                        <tr class="tree-row-negara font-bold text-text-main border-t-2 border-slate-300 dark:border-slate-700 hover:brightness-95 dark:hover:brightness-110 transition-colors" style="background-color: #e2e8f0;">
                            <td class="py-2.5 px-3">
                                <div class="flex items-center gap-2 tree-indent-1" style="padding-left: 0.5rem;">
                                    @if ($hasChildrenN)
                                        <button type="button" wire:click="toggleNode('{{ $nNode['key'] }}')" class="p-1 rounded hover:bg-slate-300 dark:hover:bg-slate-700 text-text-muted hover:text-text-main transition-colors focus:outline-hidden" title="{{ $nExpanded ? 'Tutup' : 'Buka' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform duration-150 {{ $nExpanded ? 'rotate-90 text-primary' : 'text-text-muted' }}" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    @else
                                        <span class="w-5 inline-block"></span>
                                    @endif
                                    <span class="w-2.5 h-2.5 rounded-full bg-slate-700 dark:bg-slate-300 inline-block shrink-0"></span>
                                    <span class="font-bold text-sm text-text-main">{{ $nNode['label'] }}</span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-semibold text-slate-800 dark:text-slate-200 bg-slate-300/80 dark:bg-slate-700 border border-slate-400/50">
                                        Negara
                                    </span>
                                    @if ($hasChildrenN)
                                        <span class="text-xs text-text-muted font-mono font-normal">({{ count($nNode['children']) }} prov)</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                <button type="button" wire:click="openDrawer('{{ addslashes($nNode['negara']) }}')" class="font-mono tabular-nums font-bold text-sm text-text-main hover:text-primary hover:underline transition-colors" title="Buka data siswa negara ini">
                                    {{ number_format($nNode['total_siswa']) }}
                                </button>
                            </td>
                            <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                {!! $nNode['siswa_l'] > 0 ? $nNode['siswa_l'] : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                            </td>
                            <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                {!! $nNode['siswa_p'] > 0 ? $nNode['siswa_p'] : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                            </td>
                            <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                {!! ($nNode['program_ung'] ?? 0) > 0 ? ($nNode['program_ung'] ?? 0) : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                            </td>
                            <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                {!! ($nNode['program_reg'] ?? 0) > 0 ? ($nNode['program_reg'] ?? 0) : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                            </td>
                            <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                {!! $nTjkt > 0 ? $nTjkt : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                            </td>
                            <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                {!! $nPplg > 0 ? $nPplg : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                            </td>
                            <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                {!! $nPmn > 0 ? $nPmn : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                            </td>
                            <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                {!! $nHtl > 0 ? $nHtl : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                            </td>
                            <td class="py-2.5 px-3 text-center font-mono tabular-nums font-medium text-xs text-text-main">
                                {{ $nNode['percentage'] }}%
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                <button type="button" wire:click="openDrawer('{{ addslashes($nNode['negara']) }}')" class="btn btn-secondary !py-1 !px-2.5 !text-xs gap-1 hover:border-primary hover:text-primary whitespace-nowrap">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>Lihat Siswa</span>
                                </button>
                            </td>
                        </tr>

                        {{-- LEVEL 2: PROVINSI (Slate-150 / Lebih Pudar) --}}
                        @if ($nExpanded && $hasChildrenN)
                            @foreach ($nNode['children'] as $pNode)
                                @php
                                    $pExpanded = !empty($expandedNodes[$pNode['key']]);
                                    $hasChildrenP = !empty($pNode['children']);
                                    $pCounts = $pNode['jurusan_counts'] ?? [];
                                    $pTjkt = $pCounts['TJKT'] ?? 0;
                                    $pPplg = $pCounts['PPLG'] ?? 0;
                                    $pPmn  = $pCounts['PMN'] ?? 0;
                                    $pHtl  = $pCounts['HTL'] ?? 0;
                                @endphp
                                <tr class="tree-row-provinsi font-medium text-text-main border-t border-slate-200 dark:border-slate-800 hover:brightness-95 dark:hover:brightness-110 transition-colors" style="background-color: #edf2f7;">
                                    <td class="py-2.5 px-3">
                                        <div class="flex items-center gap-1.5 tree-indent-2" style="padding-left: 2rem;">
                                            <span class="text-slate-400 font-mono text-xs select-none">└─</span>
                                            @if ($hasChildrenP)
                                                <button type="button" wire:click="toggleNode('{{ $pNode['key'] }}')" class="p-1 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-text-muted hover:text-text-main transition-colors focus:outline-hidden" title="{{ $pExpanded ? 'Tutup' : 'Buka' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform duration-150 {{ $pExpanded ? 'rotate-90 text-primary' : 'text-text-muted' }}" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="w-5 inline-block"></span>
                                            @endif
                                            <span class="w-2 h-2 rounded-full bg-blue-600 inline-block shrink-0"></span>
                                            <span class="font-semibold text-xs text-text-main {{ $pNode['label'] === '[Belum Diset]' ? 'italic text-amber-600 dark:text-amber-400' : '' }}">
                                                {{ $pNode['label'] }}
                                            </span>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-medium text-blue-800 dark:text-blue-300 bg-blue-100 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800">
                                                Provinsi
                                            </span>
                                            @if ($hasChildrenP)
                                                <span class="text-xs text-text-muted font-mono font-normal">({{ count($pNode['children']) }} kab)</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        <button type="button" wire:click="openDrawer('{{ addslashes($pNode['negara']) }}', '{{ addslashes($pNode['provinsi']) }}')" class="font-mono tabular-nums font-semibold text-xs text-text-main hover:text-primary hover:underline transition-colors" title="Buka data siswa provinsi ini">
                                            {{ number_format($pNode['total_siswa']) }}
                                        </button>
                                    </td>
                                    <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                        {!! $pNode['siswa_l'] > 0 ? $pNode['siswa_l'] : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                    </td>
                                    <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                        {!! $pNode['siswa_p'] > 0 ? $pNode['siswa_p'] : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                    </td>
                                    <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                        {!! ($pNode['program_ung'] ?? 0) > 0 ? ($pNode['program_ung'] ?? 0) : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                    </td>
                                    <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                        {!! ($pNode['program_reg'] ?? 0) > 0 ? ($pNode['program_reg'] ?? 0) : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                    </td>
                                    <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                        {!! $pTjkt > 0 ? $pTjkt : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                    </td>
                                    <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                        {!! $pPplg > 0 ? $pPplg : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                    </td>
                                    <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                        {!! $pPmn > 0 ? $pPmn : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                    </td>
                                    <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                        {!! $pHtl > 0 ? $pHtl : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-mono tabular-nums text-xs text-text-secondary">
                                        {{ $pNode['percentage'] }}%
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        <button type="button" wire:click="openDrawer('{{ addslashes($pNode['negara']) }}', '{{ addslashes($pNode['provinsi']) }}')" class="btn btn-secondary !py-1 !px-2.5 !text-xs gap-1 hover:border-primary hover:text-primary whitespace-nowrap">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Lihat Siswa</span>
                                        </button>
                                    </td>
                                </tr>

                                {{-- LEVEL 3: KABUPATEN / KOTA (Slate-50 / Lebih Pudar Lagi) --}}
                                @if ($pExpanded && $hasChildrenP)
                                    @foreach ($pNode['children'] as $kNode)
                                        @php
                                            $kExpanded = !empty($expandedNodes[$kNode['key']]);
                                            $hasChildrenK = !empty($kNode['children']);
                                            $kCounts = $kNode['jurusan_counts'] ?? [];
                                            $kTjkt = $kCounts['TJKT'] ?? 0;
                                            $kPplg = $kCounts['PPLG'] ?? 0;
                                            $kPmn  = $kCounts['PMN'] ?? 0;
                                            $kHtl  = $kCounts['HTL'] ?? 0;
                                        @endphp
                                        <tr class="tree-row-kabupaten font-medium text-text-main border-t border-slate-200/60 dark:border-slate-800/60 hover:brightness-95 dark:hover:brightness-110 transition-colors" style="background-color: #f5f7fa;">
                                            <td class="py-2.5 px-3">
                                                <div class="flex items-center gap-1.5 tree-indent-3" style="padding-left: 3.5rem;">
                                                    <span class="text-slate-400 font-mono text-xs select-none">└─</span>
                                                    @if ($hasChildrenK)
                                                        <button type="button" wire:click="toggleNode('{{ $kNode['key'] }}')" class="p-1 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-text-muted hover:text-text-main transition-colors focus:outline-hidden" title="{{ $kExpanded ? 'Tutup' : 'Buka' }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform duration-150 {{ $kExpanded ? 'rotate-90 text-primary' : 'text-text-muted' }}" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    @else
                                                        <span class="w-5 inline-block"></span>
                                                    @endif
                                                    <span class="w-2 h-2 rounded-full bg-indigo-500 inline-block shrink-0"></span>
                                                    <span class="font-medium text-xs text-text-main {{ $kNode['label'] === '[Belum Diset]' ? 'italic text-amber-600 dark:text-amber-400' : '' }}">
                                                        {{ $kNode['label'] }}
                                                    </span>
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200/60 dark:border-indigo-800">
                                                        Kab/Kota
                                                    </span>
                                                    @if ($hasChildrenK)
                                                        <span class="text-xs text-text-muted font-mono font-normal">({{ count($kNode['children']) }} kec)</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-2.5 px-3 text-center">
                                                <button type="button" wire:click="openDrawer('{{ addslashes($kNode['negara']) }}', '{{ addslashes($pNode['provinsi']) }}', '{{ addslashes($kNode['kabupaten_kota']) }}')" class="font-mono tabular-nums font-semibold text-xs text-text-main hover:text-primary hover:underline transition-colors" title="Buka data siswa kab/kota ini">
                                                    {{ number_format($kNode['total_siswa']) }}
                                                </button>
                                            </td>
                                            <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                                {!! $kNode['siswa_l'] > 0 ? $kNode['siswa_l'] : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                            </td>
                                            <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                                {!! $kNode['siswa_p'] > 0 ? $kNode['siswa_p'] : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                            </td>
                                            <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                                {!! ($kNode['program_ung'] ?? 0) > 0 ? ($kNode['program_ung'] ?? 0) : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                            </td>
                                            <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                                {!! ($kNode['program_reg'] ?? 0) > 0 ? ($kNode['program_reg'] ?? 0) : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                            </td>
                                            <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                                {!! $kTjkt > 0 ? $kTjkt : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                            </td>
                                            <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                                {!! $kPplg > 0 ? $kPplg : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                            </td>
                                            <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                                {!! $kPmn > 0 ? $kPmn : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                            </td>
                                            <td class="py-2.5 px-2 text-center font-mono tabular-nums text-text-secondary">
                                                {!! $kHtl > 0 ? $kHtl : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                            </td>
                                            <td class="py-2.5 px-3 text-center font-mono tabular-nums text-xs text-text-secondary">
                                                {{ $kNode['percentage'] }}%
                                            </td>
                                            <td class="py-2.5 px-3 text-center">
                                                <button type="button" wire:click="openDrawer('{{ addslashes($kNode['negara']) }}', '{{ addslashes($pNode['provinsi']) }}', '{{ addslashes($kNode['kabupaten_kota']) }}')" class="btn btn-secondary !py-1 !px-2.5 !text-xs gap-1 hover:border-primary hover:text-primary whitespace-nowrap">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <span>Lihat Siswa</span>
                                                </button>
                                            </td>
                                        </tr>

                                        {{-- LEVEL 4: KECAMATAN (Hampir Putih) --}}
                                        @if ($kExpanded && $hasChildrenK)
                                            @foreach ($kNode['children'] as $cNode)
                                                @php
                                                    $cExpanded = !empty($expandedNodes[$cNode['key']]);
                                                    $hasChildrenC = !empty($cNode['children']);
                                                    $cCounts = $cNode['jurusan_counts'] ?? [];
                                                    $cTjkt = $cCounts['TJKT'] ?? 0;
                                                    $cPplg = $cCounts['PPLG'] ?? 0;
                                                    $cPmn  = $cCounts['PMN'] ?? 0;
                                                    $cHtl  = $cCounts['HTL'] ?? 0;
                                                @endphp
                                                <tr class="tree-row-kecamatan text-xs text-text-secondary border-t border-slate-100 dark:border-slate-800/40 hover:brightness-95 dark:hover:brightness-110 transition-colors" style="background-color: #fafbfd;">
                                                    <td class="py-2 px-3">
                                                        <div class="flex items-center gap-1.5 tree-indent-4" style="padding-left: 5rem;">
                                                            <span class="text-slate-400 font-mono text-xs select-none">└─</span>
                                                            @if ($hasChildrenC)
                                                                <button type="button" wire:click="toggleNode('{{ $cNode['key'] }}')" class="p-1 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-text-muted hover:text-text-main transition-colors focus:outline-hidden" title="{{ $cExpanded ? 'Tutup' : 'Buka' }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform duration-150 {{ $cExpanded ? 'rotate-90 text-primary' : 'text-text-muted' }}" viewBox="0 0 20 20" fill="currentColor">
                                                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                                    </svg>
                                                                </button>
                                                            @else
                                                                <span class="w-5 inline-block"></span>
                                                            @endif
                                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 inline-block shrink-0"></span>
                                                            <span class="font-normal text-xs text-text-main {{ $cNode['label'] === '[Belum Diset]' ? 'italic text-amber-600 dark:text-amber-400' : '' }}">
                                                                {{ $cNode['label'] }}
                                                            </span>
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700">
                                                                Kecamatan
                                                            </span>
                                                            @if ($hasChildrenC)
                                                                <span class="text-xs text-text-muted font-mono font-normal">({{ count($cNode['children']) }} desa)</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-3 text-center">
                                                        <button type="button" wire:click="openDrawer('{{ addslashes($cNode['negara']) }}', '{{ addslashes($pNode['provinsi']) }}', '{{ addslashes($kNode['kabupaten_kota']) }}', '{{ addslashes($cNode['kecamatan']) }}')" class="font-mono tabular-nums font-medium text-xs text-text-main hover:text-primary hover:underline transition-colors" title="Buka data siswa kecamatan ini">
                                                            {{ number_format($cNode['total_siswa']) }}
                                                        </button>
                                                    </td>
                                                    <td class="py-2 px-2 text-center font-mono tabular-nums text-text-secondary">
                                                        {!! $cNode['siswa_l'] > 0 ? $cNode['siswa_l'] : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                                    </td>
                                                    <td class="py-2 px-2 text-center font-mono tabular-nums text-text-secondary">
                                                        {!! $cNode['siswa_p'] > 0 ? $cNode['siswa_p'] : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                                    </td>
                                                    <td class="py-2 px-2 text-center font-mono tabular-nums text-text-secondary">
                                                        {!! ($cNode['program_ung'] ?? 0) > 0 ? ($cNode['program_ung'] ?? 0) : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                                    </td>
                                                    <td class="py-2 px-2 text-center font-mono tabular-nums text-text-secondary">
                                                        {!! ($cNode['program_reg'] ?? 0) > 0 ? ($cNode['program_reg'] ?? 0) : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                                    </td>
                                                    <td class="py-2 px-2 text-center font-mono tabular-nums text-text-secondary">
                                                        {!! $cTjkt > 0 ? $cTjkt : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                                    </td>
                                                    <td class="py-2 px-2 text-center font-mono tabular-nums text-text-secondary">
                                                        {!! $cPplg > 0 ? $cPplg : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                                    </td>
                                                    <td class="py-2 px-2 text-center font-mono tabular-nums text-text-secondary">
                                                        {!! $cPmn > 0 ? $cPmn : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                                    </td>
                                                    <td class="py-2 px-2 text-center font-mono tabular-nums text-text-secondary">
                                                        {!! $cHtl > 0 ? $cHtl : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                                    </td>
                                                    <td class="py-2 px-3 text-center font-mono tabular-nums text-xs text-text-secondary">
                                                        {{ $cNode['percentage'] }}%
                                                    </td>
                                                    <td class="py-2 px-3 text-center">
                                                        <button type="button" wire:click="openDrawer('{{ addslashes($cNode['negara']) }}', '{{ addslashes($pNode['provinsi']) }}', '{{ addslashes($kNode['kabupaten_kota']) }}', '{{ addslashes($cNode['kecamatan']) }}')" class="btn btn-secondary !py-1 !px-2.5 !text-xs gap-1 hover:border-primary hover:text-primary whitespace-nowrap">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            <span>Lihat Siswa</span>
                                                        </button>
                                                    </td>
                                                </tr>

                                                {{-- LEVEL 5: DESA / KELURAHAN (Putih Bersih / Memutih Sempurna) --}}
                                                @if ($cExpanded && $hasChildrenC)
                                                    @foreach ($cNode['children'] as $dNode)
                                                        @php
                                                            $dCounts = $dNode['jurusan_counts'] ?? [];
                                                            $dTjkt = $dCounts['TJKT'] ?? 0;
                                                            $dPplg = $dCounts['PPLG'] ?? 0;
                                                            $dPmn  = $dCounts['PMN'] ?? 0;
                                                            $dHtl  = $dCounts['HTL'] ?? 0;
                                                        @endphp
                                                        <tr class="tree-row-desa text-xs text-text-muted border-t border-slate-100/80 dark:border-slate-800/30 hover:brightness-95 dark:hover:brightness-110 transition-colors" style="background-color: #ffffff;">
                                                            <td class="py-1.5 px-3">
                                                                <div class="flex items-center gap-1.5 tree-indent-5" style="padding-left: 6.5rem;">
                                                                    <span class="text-slate-400 font-mono text-xs select-none">└─</span>
                                                                    <span class="w-1 h-1 rounded-full bg-slate-400 inline-block shrink-0"></span>
                                                                    <span class="font-normal text-xs text-text-secondary {{ $dNode['label'] === '[Belum Diset]' ? 'italic text-amber-600 dark:text-amber-400' : '' }}">
                                                                        {{ $dNode['label'] }}
                                                                    </span>
                                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono text-slate-500 bg-slate-50 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800">
                                                                        Desa
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td class="py-1.5 px-3 text-center">
                                                                <button type="button" wire:click="openDrawer('{{ addslashes($dNode['negara']) }}', '{{ addslashes($pNode['provinsi']) }}', '{{ addslashes($kNode['kabupaten_kota']) }}', '{{ addslashes($cNode['kecamatan']) }}', '{{ addslashes($dNode['kelurahan']) }}')" class="font-mono tabular-nums text-xs text-text-secondary hover:text-primary hover:underline transition-colors" title="Buka data siswa desa ini">
                                                                    {{ number_format($dNode['total_siswa']) }}
                                                                </button>
                                                            </td>
                                                            <td class="py-1.5 px-2 text-center font-mono tabular-nums text-text-secondary text-xs">
                                                                {!! $dNode['siswa_l'] > 0 ? $dNode['siswa_l'] : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                                            </td>
                                                            <td class="py-1.5 px-2 text-center font-mono tabular-nums text-text-secondary text-xs">
                                                                {!! $dNode['siswa_p'] > 0 ? $dNode['siswa_p'] : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                                            </td>
                                                            <td class="py-1.5 px-2 text-center font-mono tabular-nums text-text-secondary text-xs">
                                                                {!! ($dNode['program_ung'] ?? 0) > 0 ? ($dNode['program_ung'] ?? 0) : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                                            </td>
                                                            <td class="py-1.5 px-2 text-center font-mono tabular-nums text-text-secondary text-xs">
                                                                {!! ($dNode['program_reg'] ?? 0) > 0 ? ($dNode['program_reg'] ?? 0) : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                                            </td>
                                                            <td class="py-1.5 px-2 text-center font-mono tabular-nums text-text-secondary text-xs">
                                                                {!! $dTjkt > 0 ? $dTjkt : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                                            </td>
                                                            <td class="py-1.5 px-2 text-center font-mono tabular-nums text-text-secondary text-xs">
                                                                {!! $dPplg > 0 ? $dPplg : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                                            </td>
                                                            <td class="py-1.5 px-2 text-center font-mono tabular-nums text-text-secondary text-xs">
                                                                {!! $dPmn > 0 ? $dPmn : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                                            </td>
                                                            <td class="py-1.5 px-2 text-center font-mono tabular-nums text-text-secondary text-xs">
                                                                {!! $dHtl > 0 ? $dHtl : '<span class="text-text-muted/30 font-mono">-</span>' !!}
                                                            </td>
                                                            <td class="py-1.5 px-3 text-center font-mono tabular-nums text-xs text-text-secondary">
                                                                {{ $dNode['percentage'] }}%
                                                            </td>
                                                            <td class="py-1.5 px-3 text-center">
                                                                <button type="button" wire:click="openDrawer('{{ addslashes($dNode['negara']) }}', '{{ addslashes($pNode['provinsi']) }}', '{{ addslashes($kNode['kabupaten_kota']) }}', '{{ addslashes($cNode['kecamatan']) }}', '{{ addslashes($dNode['kelurahan']) }}')" class="btn btn-secondary !py-1 !px-2.5 !text-xs gap-1 hover:border-primary hover:text-primary whitespace-nowrap">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                    </svg>
                                                                    <span>Lihat Siswa</span>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    @empty
                        <tr>
                            <td colspan="12" class="py-12 text-center text-text-muted">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-text-muted opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-xs">Tidak ada data sebaran wilayah yang sesuai dengan filter.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- SLIDE-OVER DRAWER SISWA --}}
    @if ($showDrawer)
        <div class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity z-10 cursor-pointer" wire:click="closeDrawer"></div>

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10 z-20 pointer-events-none">
                <div class="w-screen max-w-2xl bg-surface shadow-2xl flex flex-col border-l border-border pointer-events-auto relative" @click.stop>
                    {{-- Drawer Header --}}
                    <div class="p-4 sm:p-5 border-b border-border bg-surface-subtle flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <span class="text-xs font-mono text-text-muted">
                                Rincian Siswa Asal Wilayah
                            </span>
                            <h2 class="text-base sm:text-lg font-semibold text-text-main mt-0.5 truncate" id="slide-over-title">
                                {!! $drawerWilayahTitle !!}
                            </h2>
                            <p class="text-xs text-text-secondary mt-0.5">
                                Menampilkan siswa yang bertempat tinggal di wilayah yang dipilih.
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @can('analitik_spmb.wilayah.ekspor')
                                <button type="button" wire:click="exportExcelDrawer" class="btn btn-secondary !py-1.5 !px-2.5 !text-xs gap-1.5" title="Download Excel Siswa Wilayah Ini">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="hidden sm:inline">Excel</span>
                                </button>
                            @endcan
                            <button type="button" wire:click="closeDrawer" class="p-1.5 rounded-lg text-text-muted hover:text-text-main hover:bg-surface border border-border">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Drawer Filter Toolbar --}}
                    <div class="p-3 bg-surface border-b border-border grid grid-cols-1 sm:grid-cols-4 gap-2">
                        <div class="sm:col-span-2 relative">
                            <input type="text" wire:model.live.debounce.250ms="drawerSearch" placeholder="Cari nama, NISN, atau alamat..." class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <select wire:model.live="drawerFilterTingkat" class="w-full text-xs py-1.5 px-2 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                                <option value="">Semua Tingkat</option>
                                <option value="X">Kelas X</option>
                                <option value="XI">Kelas XI</option>
                                <option value="XII">Kelas XII</option>
                            </select>
                        </div>
                        <div>
                            <select wire:model.live="drawerFilterJurusan" class="w-full text-xs py-1.5 px-2 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
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
                                <thead class="bg-surface-subtle text-text-muted font-medium border-b border-border">
                                    <tr>
                                        <th class="py-2 px-2 text-center w-8">No</th>
                                        <th class="py-2 px-3">Nama siswa / domisili</th>
                                        <th class="py-2 px-2 text-center">JK</th>
                                        <th class="py-2 px-2 text-center">Rombel</th>
                                        <th class="py-2 px-2 text-center">Jurusan</th>
                                        <th class="py-2 px-3">Sekolah asal</th>
                                        <th class="py-2 px-2 text-center w-20">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border text-text-main">
                                    @forelse ($drawerSiswas as $idx => $ds)
                                        <tr class="hover:bg-surface-hover/70 transition-colors">
                                            <td class="py-2 px-2 text-center text-text-muted font-mono tabular-nums">{{ $idx + 1 }}</td>
                                            <td class="py-2 px-3">
                                                <div class="font-medium text-text-main">{{ $ds->nama }}</div>
                                                <div class="text-[10px] text-text-muted font-mono tabular-nums">
                                                    NISN: {{ $ds->nisn ?: '-' }} &bull; NIS: {{ $ds->nipd ?: '-' }}
                                                </div>
                                                <div class="text-[10px] text-text-secondary mt-1 bg-surface-subtle p-2 rounded-md border border-border/60 space-y-0.5">
                                                    <div class="font-medium text-text-main">
                                                        {{ $ds->alamat ?: '[Alamat jalan/kampung belum diisi]' }}
                                                    </div>
                                                    <div class="text-text-muted flex flex-wrap gap-x-2">
                                                        @if($ds->rt || $ds->rw)
                                                            <span>RT {{ $ds->rt ?: '-' }}/RW {{ $ds->rw ?: '-' }}</span>
                                                        @endif
                                                        @if($ds->kelurahan)
                                                            <span>Desa/Kel. {{ $ds->kelurahan }}</span>
                                                        @endif
                                                        @if($ds->kecamatan)
                                                            <span>Kec. {{ $ds->kecamatan }}</span>
                                                        @endif
                                                        @if($ds->kabupaten_kota)
                                                            <span>{{ $ds->kabupaten_kota }}</span>
                                                        @endif
                                                        @if($ds->kode_pos)
                                                            <span>Pos: {{ $ds->kode_pos }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-2 px-2 text-center">
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-medium {{ $ds->jk === 'L' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-rose-50 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' }}">
                                                    {{ $ds->jk }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-2 text-center font-mono">
                                                {{ $ds->rombel?->nama ?: '-' }}
                                            </td>
                                            <td class="py-2 px-2 text-center font-mono">
                                                <span class="px-1.5 py-0.5 rounded bg-surface-subtle border border-border text-text-secondary">
                                                    {{ $ds->rombel?->jurusan?->kode ?: '-' }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-3 text-text-secondary">
                                                <div class="truncate max-w-xs" title="{{ $ds->sekolah_asal }}">
                                                    {{ $ds->sekolah_asal ?: '-' }}
                                                </div>
                                            </td>
                                            <td class="py-2 px-2 text-center">
                                                @can('analitik_spmb.wilayah.kelola')
                                                    <button type="button" wire:click="openEditAddress({{ $ds->id }})" class="btn btn-secondary !py-1 !px-2.5 !text-xs gap-1 hover:border-primary hover:text-primary whitespace-nowrap" title="Edit Alamat Siswa">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                        <span>Edit</span>
                                                    </button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-8 text-center text-text-muted text-xs">
                                                Tidak ada data siswa yang cocok dengan kriteria pencarian di wilayah ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL EDIT ALAMAT SISWA PERORANGAN (DARI DRAWER) --}}
    @if ($showEditAddressModal && $editingAddressSiswa)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-address-title" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity z-10 cursor-pointer" wire:click="closeEditAddressModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 relative z-20 pointer-events-none">
                <div class="w-full max-w-xl bg-surface rounded-2xl border border-border shadow-2xl overflow-hidden pointer-events-auto text-left transition-all my-8 animate-in fade-in zoom-in-95 duration-150">
                    {{-- Modal Header --}}
                    <div class="p-4 sm:p-5 border-b border-border bg-surface-subtle flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono text-text-secondary bg-surface border border-border">
                                    Edit Alamat Siswa
                                </span>
                                @if(!empty($drawerSiswaIds))
                                    <span class="text-xs text-text-muted font-mono tabular-nums">
                                        ({{ $editingAddressIndex }} dari {{ count($drawerSiswaIds) }})
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-base sm:text-lg font-semibold text-text-main mt-1 truncate" id="modal-address-title">
                                {{ $editingAddressSiswa->nama }}
                            </h3>
                            <div class="text-xs text-text-muted mt-0.5 flex flex-wrap gap-x-2">
                                <span>NISN: <strong>{{ $editingAddressSiswa->nisn ?: '-' }}</strong></span>
                                <span>&bull;</span>
                                <span>Rombel: <strong>{{ $editingAddressSiswa->rombel?->nama ?: '-' }}</strong></span>
                                <span>&bull;</span>
                                <span>Asal: <strong>{{ $editingAddressSiswa->sekolah_asal ?: '-' }}</strong></span>
                            </div>
                        </div>

                        {{-- Navigasi Sebelumnya / Berikutnya & Tombol Close --}}
                        <div class="flex items-center gap-1.5 shrink-0">
                            @if(count($drawerSiswaIds) > 1)
                                <button type="button" wire:click="navigateStudentAddress(-1)" class="p-1.5 rounded-lg border border-border bg-surface hover:bg-surface-hover text-text-secondary hover:text-text-main" title="Siswa Sebelumnya">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button type="button" wire:click="navigateStudentAddress(1)" class="p-1.5 rounded-lg border border-border bg-surface hover:bg-surface-hover text-text-secondary hover:text-text-main" title="Siswa Berikutnya">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @endif
                            <button type="button" wire:click="closeEditAddressModal" class="p-1.5 rounded-lg border border-border bg-surface hover:bg-surface-hover text-text-muted hover:text-text-main ml-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Form Body --}}
                    <form wire:submit="saveStudentAddress(false)" class="p-4 sm:p-5 space-y-4 max-h-[72vh] overflow-y-auto">
                        {{-- Quick Action: Salin Wilayah Saat Ini --}}
                        <div class="p-3 rounded-xl bg-surface-subtle border border-border flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                            <div class="text-xs">
                                <span class="font-medium text-text-secondary">Wilayah drawer:</span>
                                <span class="text-text-main font-semibold ml-1">{!! $drawerWilayahTitle !!}</span>
                            </div>
                            <button type="button" wire:click="copyDrawerWilayahToAddress" class="btn btn-secondary !py-1 !px-2.5 !text-xs gap-1.5 hover:border-primary hover:text-primary shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span>Salin ke form ini</span>
                            </button>
                        </div>

                        {{-- Input Alamat Jalan / Kp --}}
                        <div>
                            <label class="block text-xs font-medium text-text-main mb-1">
                                Alamat jalan / kampung / dusun / no. rumah
                            </label>
                            <textarea wire:model="editAddressAlamat" rows="2" placeholder="Contoh: Jl. Merdeka No. 45 / Kp. Sukamaju" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary focus:border-primary"></textarea>
                            @error('editAddressAlamat') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                        </div>

                        {{-- RT, RW & Dusun --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-text-main mb-1">RT</label>
                                <input type="text" wire:model="editAddressRt" placeholder="Contoh: 01" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary focus:border-primary">
                                @error('editAddressRt') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-text-main mb-1">RW</label>
                                <input type="text" wire:model="editAddressRw" placeholder="Contoh: 03" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary focus:border-primary">
                                @error('editAddressRw') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-text-main mb-1">Dusun / lingkungan</label>
                                <input type="text" wire:model="editAddressDusun" placeholder="Contoh: Dusun Cikole" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary focus:border-primary">
                                @error('editAddressDusun') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Provinsi & Kabupaten/Kota (Cascading Hybrid) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {{-- Provinsi --}}
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-medium text-text-main">
                                        Provinsi <span class="text-rose-500">*</span>
                                    </label>
                                    <button type="button" wire:click="$toggle('isManualAddressProvinsi')" class="text-[11px] font-medium text-primary hover:underline">
                                        {{ $isManualAddressProvinsi ? 'Pilih dari daftar' : 'Ketik manual' }}
                                    </button>
                                </div>
                                @if ($isManualAddressProvinsi)
                                    <input type="text" wire:model.live="editAddressProvinsi" placeholder="Contoh: Jawa Barat" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary focus:border-primary">
                                @else
                                    <select wire:model.live="editAddressProvinsi" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface text-text-main focus:ring-1 focus:ring-primary focus:border-primary">
                                        <option value="">-- Pilih Provinsi --</option>
                                        @if ($editAddressProvinsi && !in_array($editAddressProvinsi, $addressProvinceList))
                                            <option value="{{ $editAddressProvinsi }}">{{ $editAddressProvinsi }} (Nilai Saat Ini)</option>
                                        @endif
                                        @foreach ($addressProvinceList as $prov)
                                            <option value="{{ $prov }}">{{ $prov }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('editAddressProvinsi') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                            </div>

                            {{-- Kabupaten / Kota --}}
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-medium text-text-main">
                                        Kabupaten / Kota <span class="text-rose-500">*</span>
                                    </label>
                                    <button type="button" wire:click="$toggle('isManualAddressKabupaten')" class="text-[11px] font-medium text-primary hover:underline">
                                        {{ $isManualAddressKabupaten ? 'Pilih dari daftar' : 'Ketik manual' }}
                                    </button>
                                </div>
                                @if ($isManualAddressKabupaten)
                                    <input type="text" wire:model.live="editAddressKabupaten" placeholder="Contoh: Kab. Garut" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary focus:border-primary">
                                @else
                                    <select wire:model.live="editAddressKabupaten" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface text-text-main focus:ring-1 focus:ring-primary focus:border-primary">
                                        <option value="">-- Pilih Kabupaten / Kota --</option>
                                        @if ($editAddressKabupaten && !in_array($editAddressKabupaten, $addressKabupatenList))
                                            <option value="{{ $editAddressKabupaten }}">{{ $editAddressKabupaten }} (Nilai Saat Ini)</option>
                                        @endif
                                        @foreach ($addressKabupatenList as $kab)
                                            <option value="{{ $kab }}">{{ $kab }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('editAddressKabupaten') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Kecamatan & Desa/Kelurahan (Cascading Hybrid) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {{-- Kecamatan --}}
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-medium text-text-main">
                                        Kecamatan
                                    </label>
                                    <button type="button" wire:click="$toggle('isManualAddressKecamatan')" class="text-[10px] font-medium text-primary hover:underline">
                                        {{ $isManualAddressKecamatan ? 'Pilih dari daftar' : 'Ketik manual' }}
                                    </button>
                                </div>
                                @if ($isManualAddressKecamatan || empty($addressKecamatanList))
                                    <input type="text" wire:model.live.debounce.300ms="editAddressKecamatan" placeholder="Contoh: Karangpawitan" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                                @else
                                    <select wire:model.live="editAddressKecamatan" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface text-text-main focus:ring-1 focus:ring-primary">
                                        <option value="">-- Pilih Kecamatan --</option>
                                        @if ($editAddressKecamatan && !in_array($editAddressKecamatan, $addressKecamatanList))
                                            <option value="{{ $editAddressKecamatan }}">{{ $editAddressKecamatan }} (Nilai Saat Ini)</option>
                                        @endif
                                        @foreach ($addressKecamatanList as $kec)
                                            <option value="{{ $kec }}">{{ $kec }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('editAddressKecamatan') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                            </div>

                            {{-- Desa / Kelurahan --}}
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-medium text-text-main">
                                        Desa / Kelurahan
                                    </label>
                                    <button type="button" wire:click="$toggle('isManualAddressKelurahan')" class="text-[10px] font-medium text-primary hover:underline">
                                        {{ $isManualAddressKelurahan ? 'Pilih dari daftar' : 'Ketik manual' }}
                                    </button>
                                </div>
                                @if ($isManualAddressKelurahan || empty($addressDesaList))
                                    <input type="text" wire:model="editAddressKelurahan" placeholder="Contoh: Sukamaju" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                                @else
                                    <select wire:model="editAddressKelurahan" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface text-text-main focus:ring-1 focus:ring-primary">
                                        <option value="">-- Pilih Desa / Kelurahan --</option>
                                        @if ($editAddressKelurahan && !in_array($editAddressKelurahan, $addressDesaList))
                                            <option value="{{ $editAddressKelurahan }}">{{ $editAddressKelurahan }} (Nilai Saat Ini)</option>
                                        @endif
                                        @foreach ($addressDesaList as $desa)
                                            <option value="{{ $desa }}">{{ $desa }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('editAddressKelurahan') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Negara & Kode Pos --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-text-main mb-1">Negara <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model="editAddressNegara" placeholder="Contoh: Indonesia / Malaysia" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                                @error('editAddressNegara') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-text-main mb-1">Kode Pos</label>
                                <input type="text" wire:model="editAddressKodePos" placeholder="Contoh: 44182" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                                @error('editAddressKodePos') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="pt-4 border-t border-border flex flex-col sm:flex-row items-center justify-between gap-2.5">
                            <button type="button" wire:click="closeEditAddressModal" class="btn btn-secondary !py-2 !px-3 text-xs w-full sm:w-auto">
                                Batal
                            </button>

                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <button type="button" wire:click="saveStudentAddress(false)" wire:loading.attr="disabled" class="btn btn-secondary !py-2 !px-3.5 text-xs flex-1 sm:flex-none">
                                    <span wire:loading.remove wire:target="saveStudentAddress">Simpan</span>
                                    <span wire:loading wire:target="saveStudentAddress">Menyimpan...</span>
                                </button>

                                <button type="button" wire:click="saveStudentAddress(true)" wire:loading.attr="disabled" class="btn btn-primary !py-2 !px-4 text-xs gap-1.5 flex-1 sm:flex-none">
                                    <span>Simpan & Lanjut</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
