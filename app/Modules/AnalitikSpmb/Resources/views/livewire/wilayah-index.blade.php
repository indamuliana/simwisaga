<div class="space-y-6">
    {{-- Header & Navigasi --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-border">
        <div>
            <div class="flex items-center gap-2 text-xs text-text-muted mb-1 font-mono">
                <a href="{{ route('analitik-spmb.asal-smp') }}" class="hover:text-primary transition-colors">Analitik SPMB</a>
                <span>/</span>
                <span class="text-text-main font-semibold">Sebaran Wilayah</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-text-main tracking-tight flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-primary/10 text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <span>Sebaran Wilayah Asal Siswa</span>
            </h1>
            <p class="text-xs sm:text-sm text-text-secondary mt-0.5">Peta demografis asal domisili pendaftar (Negara, Provinsi, Kab/Kota, Kecamatan) serta standarisasi anomali data wilayah & siswa luar negeri.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('analitik-spmb.asal-smp') }}" class="btn btn-secondary text-xs gap-1.5 shadow-2xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span>Asal SMP</span>
            </a>

            <a href="{{ route('analitik-spmb.master-smp') }}" class="btn btn-secondary text-xs gap-1.5 shadow-2xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Master SMP</span>
            </a>

            <a href="{{ route('analitik-spmb.perbaikan-smp') }}" class="btn btn-secondary text-xs gap-1.5 shadow-2xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                <span>Perbaikan SMP</span>
            </a>

            @can('analitik_spmb.wilayah.kelola')
                <button type="button" wire:click="autoDetectWilayah" wire:loading.attr="disabled" class="btn btn-secondary text-xs gap-1.5 shadow-2xs text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 border-emerald-200" title="Deteksi otomatis wilayah dari basis data Master SMP & Sekolah Asal serta identifikasi pendaftar luar negeri">
                    <svg wire:loading.remove wire:target="autoDetectWilayah" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
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
                <button type="button" wire:click="exportExcelRekap" wire:loading.attr="disabled" class="btn btn-primary text-xs gap-1.5 shadow-2xs">
                    <svg wire:loading.remove wire:target="exportExcelRekap" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <svg wire:loading wire:target="exportExcelRekap" class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Export Excel</span>
                </button>
            @endcan

            <button type="button" onclick="window.print()" class="btn btn-secondary text-xs gap-1.5 shadow-2xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
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

    {{-- Tab Selector --}}
    <div class="flex border-b border-border gap-6">
        <button type="button" wire:click="switchTab('analitik')" class="pb-3 text-xs sm:text-sm font-semibold transition-colors flex items-center gap-2 relative {{ $activeTab === 'analitik' ? 'text-primary border-b-2 border-primary -mb-px' : 'text-text-muted hover:text-text-main' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span>Analitik Sebaran</span>
            @if(isset($analytics['totalSiswa']))
                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono {{ $activeTab === 'analitik' ? 'bg-primary/10 text-primary' : 'bg-surface-subtle text-text-muted' }}">
                    {{ number_format($analytics['totalSiswa'], 0, ',', '.') }}
                </span>
            @endif
        </button>

        <button type="button" wire:click="switchTab('perbaikan')" class="pb-3 text-xs sm:text-sm font-semibold transition-colors flex items-center gap-2 relative {{ $activeTab === 'perbaikan' ? 'text-primary border-b-2 border-primary -mb-px' : 'text-text-muted hover:text-text-main' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $totalAnomaliSiswa > 0 ? 'text-amber-500' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span>Perbaikan Wilayah & Anomali</span>
            @if ($totalAnomaliSiswa > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 font-mono">
                    {{ $totalAnomaliSiswa }} Anomali
                </span>
            @else
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                    Bersih
                </span>
            @endif
        </button>
    </div>

    {{-- TAB 1: ANALITIK SEBARAN --}}
    @if ($activeTab === 'analitik')
        {{-- Kartu Ringkasan Statistik (5 Kartu) --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
            {{-- Total Siswa --}}
            <div class="p-4 rounded-xl border border-border bg-surface shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-text-secondary font-medium">
                    <span>Total Siswa</span>
                    <span class="p-1 rounded-md bg-blue-500/10 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold font-mono text-text-main">
                    {{ number_format($analytics['totalSiswa'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-text-muted mt-0.5">Siswa terfilter</div>
            </div>

            {{-- Negara --}}
            <div class="p-4 rounded-xl border border-border bg-surface shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-text-secondary font-medium">
                    <span>Negara Asal</span>
                    <span class="p-1 rounded-md bg-indigo-500/10 text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold font-mono text-text-main">
                    {{ $analytics['statNegara'] ?? 0 }} <span class="text-xs font-normal text-text-muted">Negara</span>
                </div>
                <div class="text-[11px] text-text-muted mt-0.5">Indonesia & Luar Negeri</div>
            </div>

            {{-- Provinsi --}}
            <div class="p-4 rounded-xl border border-border bg-surface shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-text-secondary font-medium">
                    <span>Provinsi</span>
                    <span class="p-1 rounded-md bg-purple-500/10 text-purple-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold font-mono text-text-main">
                    {{ $analytics['statProvinsi'] ?? 0 }} <span class="text-xs font-normal text-text-muted">Provinsi</span>
                </div>
                <div class="text-[11px] text-text-muted mt-0.5">Sebaran wilayah tingkat I</div>
            </div>

            {{-- Kab / Kota --}}
            <div class="p-4 rounded-xl border border-border bg-surface shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-text-secondary font-medium">
                    <span>Kabupaten / Kota</span>
                    <span class="p-1 rounded-md bg-emerald-500/10 text-emerald-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold font-mono text-text-main">
                    {{ $analytics['statKabupaten'] ?? 0 }} <span class="text-xs font-normal text-text-muted">Kab/Kota</span>
                </div>
                <div class="text-[11px] text-text-muted mt-0.5">Sebaran wilayah tingkat II</div>
            </div>

            {{-- Siswa Luar Negeri --}}
            <div class="p-4 rounded-xl border border-amber-300 dark:border-amber-800/60 bg-amber-50/50 dark:bg-amber-950/20 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-amber-800 dark:text-amber-300 font-medium">
                    <span>Siswa Luar Negeri</span>
                    <span class="p-1 rounded-md bg-amber-500/20 text-amber-700 dark:text-amber-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold font-mono text-amber-700 dark:text-amber-400">
                    {{ $analytics['statLuarNegeri'] ?? 0 }} <span class="text-xs font-normal text-amber-700/80">Siswa</span>
                </div>
                <div class="text-[11px] text-amber-700/80 mt-0.5">Sabah, Malaysia (CLC & LC)</div>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="p-4 rounded-xl border border-border bg-surface shadow-2xs space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-border pb-3">
                <div class="text-xs font-bold uppercase tracking-wider text-text-secondary flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span>Filter & Pencarian Wilayah</span>
                </div>
                <button type="button" wire:click="resetFilters" class="text-xs text-text-muted hover:text-primary transition-colors flex items-center gap-1 self-start sm:self-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Reset Filter</span>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-2.5">
                {{-- Filter Cakupan --}}
                <div class="sm:col-span-1 lg:col-span-1">
                    <label class="block text-[11px] font-semibold text-text-muted mb-1">Cakupan</label>
                    <select wire:model.live="filterCakupan" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                        <option value="semua">Semua Wilayah</option>
                        <option value="dalam_negeri">Dalam Negeri</option>
                        <option value="luar_negeri">Luar Negeri</option>
                    </select>
                </div>

                {{-- Filter Provinsi --}}
                <div class="sm:col-span-1 lg:col-span-1">
                    <label class="block text-[11px] font-semibold text-text-muted mb-1">Provinsi</label>
                    <select wire:model.live="filterProvinsi" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                        <option value="">Semua Provinsi</option>
                        @foreach ($provinsiOptions as $prov)
                            <option value="{{ $prov }}">{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Kabupaten / Kota --}}
                <div class="sm:col-span-1 lg:col-span-1">
                    <label class="block text-[11px] font-semibold text-text-muted mb-1">Kab / Kota</label>
                    <select wire:model.live="filterKabupaten" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                        <option value="">Semua Kab/Kota</option>
                        @foreach ($kabupatenOptions as $kab)
                            <option value="{{ $kab }}">{{ $kab }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Tahun Ajaran --}}
                <div class="sm:col-span-1 lg:col-span-1">
                    <label class="block text-[11px] font-semibold text-text-muted mb-1">Tahun Ajaran</label>
                    <select wire:model.live="filterTahunAjaranId" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                        <option value="">Semua TA</option>
                        @foreach ($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}">{{ $ta->tahun }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Tingkat --}}
                <div class="sm:col-span-1 lg:col-span-1">
                    <label class="block text-[11px] font-semibold text-text-muted mb-1">Tingkat</label>
                    <select wire:model.live="filterTingkat" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                        <option value="">Semua</option>
                        <option value="X">Kelas X</option>
                        <option value="XI">Kelas XI</option>
                        <option value="XII">Kelas XII</option>
                    </select>
                </div>

                {{-- Filter Jurusan --}}
                <div class="sm:col-span-1 lg:col-span-1">
                    <label class="block text-[11px] font-semibold text-text-muted mb-1">Jurusan</label>
                    <select wire:model.live="filterJurusanId" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                        <option value="">Semua Jurusan</option>
                        @foreach ($jurusans as $j)
                            <option value="{{ $j->id }}">{{ $j->kode }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter JK --}}
                <div class="sm:col-span-1 lg:col-span-1">
                    <label class="block text-[11px] font-semibold text-text-muted mb-1">Jenis Kelamin</label>
                    <select wire:model.live="filterJk" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                        <option value="">Semua (L/P)</option>
                        <option value="L">Laki-laki (L)</option>
                        <option value="P">Perempuan (P)</option>
                    </select>
                </div>

                {{-- Filter Status --}}
                <div class="sm:col-span-1 lg:col-span-1">
                    <label class="block text-[11px] font-semibold text-text-muted mb-1">Status</label>
                    <select wire:model.live="filterStatus" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                        <option value="aktif">Siswa Aktif</option>
                        <option value="semua">Semua Status</option>
                        <option value="lulus">Lulus</option>
                        <option value="mutasi">Mutasi / Pindah</option>
                    </select>
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari berdasarkan nama kecamatan, kabupaten, kelurahan, atau kata kunci alamat..." class="w-full text-xs py-2 pl-9 pr-4 rounded-lg border border-border bg-surface-subtle text-text-main placeholder-text-muted focus:ring-1 focus:ring-primary">
                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Seksi Khusus: Siswa Luar Negeri (Sabah, Malaysia) --}}
        @if (!empty($analytics['luarNegeriSiswa']) && $analytics['luarNegeriSiswa']->isNotEmpty() && $filterCakupan !== 'dalam_negeri')
            <div class="rounded-xl border border-amber-300 dark:border-amber-800/60 bg-amber-50/40 dark:bg-amber-950/20 p-4 sm:p-5 shadow-2xs space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🇲🇾</span>
                        <div>
                            <h2 class="text-sm sm:text-base font-bold text-amber-900 dark:text-amber-200">
                                Siswa Asal Luar Negeri &bull; Sabah, Malaysia
                            </h2>
                            <p class="text-xs text-amber-700 dark:text-amber-400">
                                Pendaftar jalur afirmasi/kemitraan dari Pusat Pembelajaran Komuniti (CLC) & Learning Center (LC).
                            </p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-amber-200/80 dark:bg-amber-900/80 text-amber-900 dark:text-amber-200 self-start sm:self-auto">
                        {{ $analytics['luarNegeriSiswa']->count() }} Siswa Terdata
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 pt-2">
                    @foreach ($analytics['luarNegeriSiswa'] as $ln)
                        <div class="p-3 rounded-lg border border-amber-200/70 dark:border-amber-800/40 bg-surface text-text-main shadow-2xs flex flex-col justify-between hover:border-amber-400 transition-colors">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $ln->jk === 'L' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300' }}">
                                        {{ $ln->jk }}
                                    </span>
                                    <span class="text-[11px] font-mono font-semibold text-amber-800 dark:text-amber-300">
                                        {{ $ln->kabupaten_kota }}
                                    </span>
                                </div>
                                <h3 class="font-bold text-xs text-text-main mt-1.5 truncate" title="{{ $ln->nama }}">
                                    {{ $ln->nama }}
                                </h3>
                                <div class="text-[11px] text-text-secondary mt-0.5 truncate" title="{{ $ln->sekolah_asal }}">
                                    🏫 {{ $ln->sekolah_asal ?: 'Sekolah CLC/LC' }}
                                </div>
                            </div>
                            <div class="mt-2 pt-2 border-t border-border flex items-center justify-between text-[10px] text-text-muted font-mono">
                                <span>NISN: {{ $ln->nisn ?: '-' }}</span>
                                <button type="button" wire:click="openDrawer('{{ $ln->negara }}', '{{ $ln->provinsi }}', '{{ $ln->kabupaten_kota }}', '{{ $ln->kecamatan }}')" class="text-primary hover:underline font-sans font-medium">
                                    Lihat Siswa &rarr;
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Leaderboard Top 10 Feeder Kab / Kota --}}
        @if (!empty($analytics['topKabupaten']) && $analytics['topKabupaten']->isNotEmpty())
            <div class="p-4 sm:p-5 rounded-xl border border-border bg-surface shadow-2xs space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-text-main flex items-center gap-2">
                            <span class="text-amber-500">🏆</span>
                            <span>Top 10 Kabupaten / Kota Feeder Siswa Baru</span>
                        </h2>
                        <p class="text-xs text-text-muted mt-0.5">Wilayah penyumbang pendaftar terbanyak berdasarkan data domisili siswa.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                    @foreach ($analytics['topKabupaten'] as $idx => $tk)
                        @php
                            $pctKab = ($analytics['totalSiswa'] ?? 0) > 0 ? round(($tk->total / $analytics['totalSiswa']) * 100, 1) : 0;
                        @endphp
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="w-5 text-center font-mono font-bold text-[11px] {{ $idx < 3 ? 'text-amber-600 dark:text-amber-400' : 'text-text-muted' }}">
                                        #{{ $idx + 1 }}
                                    </span>
                                    <span class="font-semibold text-text-main truncate">
                                        {{ $tk->kab_kota }}
                                    </span>
                                    @if ($tk->neg !== 'Indonesia')
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200">
                                            {{ $tk->neg }}
                                        </span>
                                    @elseif ($tk->prov)
                                        <span class="text-[11px] text-text-muted">({{ $tk->prov }})</span>
                                    @endif
                                </div>
                                <div class="text-right shrink-0 font-mono text-[11px] text-text-secondary">
                                    <strong class="text-text-main font-bold">{{ number_format($tk->total) }}</strong> siswa
                                    <span class="text-text-muted">({{ $pctKab }}%)</span>
                                </div>
                            </div>
                            <div class="w-full bg-surface-subtle rounded-full h-1.5 overflow-hidden">
                                <div class="h-1.5 rounded-full {{ $idx === 0 ? 'bg-primary' : ($idx < 3 ? 'bg-indigo-500' : 'bg-slate-400') }}" style="width: {{ min(100, $pctKab * 2) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Tabel Rekap Sebaran Per Kecamatan --}}
        <div class="rounded-xl border border-border bg-surface shadow-2xs overflow-hidden">
            <div class="p-4 border-b border-border bg-surface-subtle flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-text-main">Tabel Agregasi Sebaran Wilayah</h2>
                    <p class="text-xs text-text-muted">Dikelompokkan berdasarkan Negara, Provinsi, Kabupaten/Kota, dan Kecamatan.</p>
                </div>
                <div class="text-xs text-text-muted font-mono">
                    Total: <strong>{{ number_format($paginatedItems->total() ?? 0) }}</strong> entri wilayah
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-surface-subtle text-text-muted uppercase font-semibold border-b border-border">
                        <tr>
                            <th class="py-2.5 px-3 text-center w-10">No</th>
                            <th class="py-2.5 px-3">Negara</th>
                            <th class="py-2.5 px-3">Provinsi</th>
                            <th class="py-2.5 px-3">Kabupaten / Kota</th>
                            <th class="py-2.5 px-3">Kecamatan</th>
                            <th class="py-2.5 px-3 text-center">Total Siswa</th>
                            <th class="py-2.5 px-2 text-center">L</th>
                            <th class="py-2.5 px-2 text-center">P</th>
                            <th class="py-2.5 px-3">Jurusan Dominan</th>
                            <th class="py-2.5 px-3 text-center">Porsi (%)</th>
                            <th class="py-2.5 px-3 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border text-text-main">
                        @forelse ($paginatedItems as $idx => $row)
                            <tr class="hover:bg-surface-hover/70 transition-colors">
                                <td class="py-2.5 px-3 text-center text-text-muted font-mono">
                                    {{ $paginatedItems->firstItem() + $idx }}
                                </td>
                                <td class="py-2.5 px-3 font-medium">
                                    @if ($row->negara === 'Indonesia')
                                        <span class="inline-flex items-center gap-1 text-slate-700 dark:text-slate-300">
                                            <span>🇮🇩</span> Indonesia
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-amber-700 dark:text-amber-300 font-semibold">
                                            <span>🇲🇾</span> {{ $row->negara }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3">
                                    @if ($row->provinsi === '[Belum Diset]')
                                        <span class="text-amber-600 dark:text-amber-400 italic">{{ $row->provinsi }}</span>
                                    @else
                                        <span class="font-medium">{{ $row->provinsi }}</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3">
                                    @if ($row->kabupaten_kota === '[Belum Diset]')
                                        <span class="text-amber-600 dark:text-amber-400 italic">{{ $row->kabupaten_kota }}</span>
                                    @else
                                        <span class="font-semibold">{{ $row->kabupaten_kota }}</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3">
                                    @if ($row->kecamatan === '[Belum Diset]')
                                        <span class="text-amber-600 dark:text-amber-400 italic">{{ $row->kecamatan }}</span>
                                    @else
                                        <span class="font-medium text-text-main">{{ $row->kecamatan }}</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-center font-mono font-bold text-sm text-primary">
                                    {{ number_format($row->total_siswa) }}
                                </td>
                                <td class="py-2.5 px-2 text-center font-mono text-blue-600 dark:text-blue-400">
                                    {{ $row->siswa_l }}
                                </td>
                                <td class="py-2.5 px-2 text-center font-mono text-rose-600 dark:text-rose-400">
                                    {{ $row->siswa_p }}
                                </td>
                                <td class="py-2.5 px-3 font-mono text-xs">
                                    <span class="px-2 py-0.5 rounded bg-surface-subtle border border-border">
                                        {{ $row->dominant_jurusan }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-center font-mono text-text-secondary">
                                    {{ $row->percentage }}%
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    <button type="button" wire:click="openDrawer('{{ $row->negara }}', '{{ $row->provinsi }}', '{{ $row->kabupaten_kota }}', '{{ $row->kecamatan }}')" class="btn btn-secondary !py-1 !px-2.5 !text-[11px] gap-1 shadow-2xs hover:border-primary hover:text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Lihat Siswa</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-12 text-center text-text-muted">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-text-muted opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-xs">Tidak ada data sebaran wilayah yang sesuai dengan filter.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($paginatedItems->hasPages())
                <div class="p-3 border-t border-border bg-surface">
                    {{ $paginatedItems->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- TAB 2: PERBAIKAN WILAYAH & ANOMALI --}}
    @if ($activeTab === 'perbaikan')
        <div class="space-y-4">
            {{-- Banner Panduan & Quick Action Auto-Detect --}}
            <div class="p-4 sm:p-5 rounded-xl border border-primary/20 bg-primary/5 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-sm font-bold text-text-main flex items-center gap-2">
                        <span class="p-1 rounded-md bg-primary/20 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <span>Standarisasi Cerdas Wilayah Domisili Siswa</span>
                    </h2>
                    <p class="text-xs text-text-secondary max-w-2xl">
                        Sistem mendeteksi anomali seperti penulisan nama kecamatan dengan awalan <code>Kec.</code>, kolom Kabupaten/Kota yang belum terisi, serta siswa asal Sabah, Malaysia (CLC & LC). Gunakan <strong>Auto-Detect Wilayah</strong> untuk memetakan otomatis atau klik <strong>Standarisasi</strong> pada kelompok di bawah.
                    </p>
                </div>

                @can('analitik_spmb.wilayah.kelola')
                    <div class="shrink-0 flex items-center gap-2">
                        <button type="button" wire:click="autoDetectWilayah" wire:loading.attr="disabled" class="btn btn-primary text-xs gap-1.5 shadow-2xs">
                            <svg wire:loading.remove wire:target="autoDetectWilayah" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <svg wire:loading wire:target="autoDetectWilayah" class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Jalankan Auto-Detect Sekarang</span>
                        </button>
                    </div>
                @endcan
            </div>

            {{-- Search Bar Perbaikan --}}
            <div class="p-3 rounded-xl border border-border bg-surface shadow-2xs">
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kelompok anomali (kecamatan, kelurahan, alamat, sekolah asal)..." class="w-full text-xs py-2 pl-9 pr-4 rounded-lg border border-border bg-surface-subtle text-text-main placeholder-text-muted focus:ring-1 focus:ring-primary">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Tabel Kelompok Anomali --}}
            <div class="rounded-xl border border-border bg-surface shadow-2xs overflow-hidden">
                <div class="p-4 border-b border-border bg-surface-subtle flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-text-main">Daftar Kelompok Anomali Data Wilayah</h2>
                        <p class="text-xs text-text-muted">Data dikelompokkan per kombinasi kecamatan/kelurahan untuk mempercepat standarisasi massal.</p>
                    </div>
                    <div class="text-xs font-mono text-text-secondary">
                        <strong>{{ $anomaliGroups->count() }}</strong> kelompok &bull; <strong>{{ $totalAnomaliSiswa }}</strong> total siswa
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-surface-subtle text-text-muted uppercase font-semibold border-b border-border">
                            <tr>
                                <th class="py-2.5 px-3 text-center w-10">No</th>
                                <th class="py-2.5 px-3">Kecamatan Asli</th>
                                <th class="py-2.5 px-3">Kelurahan Asli</th>
                                <th class="py-2.5 px-3">Kab / Kota Saat Ini</th>
                                <th class="py-2.5 px-3">Provinsi Saat Ini</th>
                                <th class="py-2.5 px-3">Negara</th>
                                <th class="py-2.5 px-3 text-center">Jumlah Siswa</th>
                                <th class="py-2.5 px-3">Sampel Siswa & Sekolah</th>
                                <th class="py-2.5 px-3 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border text-text-main">
                            @forelse ($anomaliGroups as $idx => $g)
                                <tr class="hover:bg-surface-hover/70 transition-colors">
                                    <td class="py-2.5 px-3 text-center text-text-muted font-mono">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span class="font-mono font-bold {{ str_starts_with($g->kecamatan, 'Kec.') ? 'text-amber-600 dark:text-amber-400' : 'text-text-main' }}">
                                            {{ $g->kecamatan ?: '[Kosong]' }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-text-secondary">
                                        {{ $g->kelurahan ?: '-' }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        @if (empty($g->kabupaten_kota))
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300">Belum Diset</span>
                                        @else
                                            <span class="font-medium">{{ $g->kabupaten_kota }}</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3">
                                        @if (empty($g->provinsi))
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300">Belum Diset</span>
                                        @else
                                            <span class="font-medium">{{ $g->provinsi }}</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 font-medium">
                                        {{ $g->negara }}
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-mono font-bold text-primary">
                                        {{ $g->total_siswa }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <div class="text-[11px] font-medium text-text-main truncate max-w-xs" title="{{ implode(', ', $g->sample_names) }}">
                                            {{ implode(', ', $g->sample_names) }}
                                        </div>
                                        <div class="text-[10px] text-text-muted truncate max-w-xs" title="{{ $g->sample_school }}">
                                            🏫 {{ $g->sample_school }}
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        @can('analitik_spmb.wilayah.kelola')
                                            <button type="button" wire:click="openEditGroupModal('{{ addslashes($g->kecamatan) }}', '{{ addslashes($g->kelurahan) }}', '{{ $g->siswa_ids }}', {{ $g->total_siswa }})" class="btn btn-primary !py-1 !px-2.5 !text-[11px] gap-1 shadow-2xs">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Standarisasi</span>
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center text-text-muted">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <div class="p-3 rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-semibold text-text-main">Luar Biasa! Semua Data Wilayah Sudah Terstandarisasi</span>
                                            <span class="text-xs text-text-secondary">Tidak ada lagi format kecamatan kotor atau kabupaten/kota kosong pada database siswa.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

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
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-primary/10 text-primary uppercase font-mono">
                                Rincian Siswa Asal Wilayah
                            </span>
                            <h2 class="text-base sm:text-lg font-bold text-text-main mt-1 truncate" id="slide-over-title">
                                {!! $drawerWilayahTitle !!}
                            </h2>
                            <p class="text-xs text-text-secondary mt-0.5">
                                Menampilkan siswa yang bertempat tinggal di wilayah yang dipilih.
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @can('analitik_spmb.wilayah.ekspor')
                                <button type="button" wire:click="exportExcelDrawer" class="btn btn-secondary !py-1.5 !px-2.5 !text-xs gap-1 shadow-2xs" title="Download Excel Siswa Wilayah Ini">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="hidden sm:inline">Excel</span>
                                </button>
                            @endcan
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
                            <input type="text" wire:model.live.debounce.250ms="drawerSearch" placeholder="Cari nama / NISN / alamat..." class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
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
                                <thead class="bg-surface-subtle text-text-muted uppercase font-semibold border-b border-border">
                                    <tr>
                                        <th class="py-2 px-2 text-center w-8">No</th>
                                        <th class="py-2 px-3">Nama Siswa / NISN</th>
                                        <th class="py-2 px-2 text-center">JK</th>
                                        <th class="py-2 px-2 text-center">Rombel</th>
                                        <th class="py-2 px-2 text-center">Jurusan</th>
                                        <th class="py-2 px-3">Sekolah Asal</th>
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
                                                @if ($ds->alamat)
                                                    <div class="text-[10px] text-text-secondary truncate max-w-xs mt-0.5">
                                                        📍 {{ $ds->alamat }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-2 px-2 text-center">
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $ds->jk === 'L' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300' }}">
                                                    {{ $ds->jk }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-2 text-center font-mono font-semibold">
                                                {{ $ds->rombel?->nama ?: '-' }}
                                            </td>
                                            <td class="py-2 px-2 text-center font-mono">
                                                <span class="px-1.5 py-0.5 rounded bg-surface-subtle border border-border">
                                                    {{ $ds->rombel?->jurusan?->kode ?: '-' }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-3 text-[11px] text-text-secondary">
                                                <div class="truncate max-w-xs" title="{{ $ds->sekolah_asal }}">
                                                    {{ $ds->sekolah_asal ?: '-' }}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="py-8 text-center text-text-muted text-xs">
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

    {{-- MODAL STANDARISASI KELOMPOK (TAB PERBAIKAN) --}}
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity z-10 cursor-pointer" wire:click="closeEditModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 relative z-20 pointer-events-none">
                <div class="relative transform overflow-hidden rounded-2xl bg-surface border border-border text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg text-text-main pointer-events-auto" @click.stop>
                    {{-- Header --}}
                    <div class="p-4 sm:p-5 border-b border-border bg-surface-subtle flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-primary/10 text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-base font-bold text-text-main" id="modal-title">Standarisasi Data Wilayah Kelompok</h3>
                                <p class="text-xs text-text-secondary">Terapkan standarisasi pada {{ $editGroupTotalSiswa }} data siswa dalam kelompok ini.</p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeEditModal" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                    </div>

                    {{-- Form Body --}}
                    <form wire:submit="saveStandardizedWilayah">
                        <div class="p-5 space-y-4">
                            {{-- Info Raw Asli --}}
                            <div class="p-3 rounded-lg bg-surface-subtle border border-border text-xs space-y-1">
                                <div class="text-text-muted">Data Asli Terdeteksi:</div>
                                <div class="font-mono font-semibold text-text-main">
                                    Kecamatan: <span class="text-primary">{{ $editGroupKecamatan ?: '[Kosong]' }}</span>
                                    @if ($editGroupKelurahan)
                                        &bull; Kelurahan: <span class="text-primary">{{ $editGroupKelurahan }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Toggle Luar Negeri --}}
                            <div class="p-3 rounded-lg border border-amber-200 dark:border-amber-800/50 bg-amber-50/50 dark:bg-amber-950/20 flex items-center justify-between">
                                <div>
                                    <label for="editIsLuarNegeri" class="text-xs font-bold text-amber-900 dark:text-amber-200 cursor-pointer">
                                        Siswa Luar Negeri (Malaysia / CLC / Sabah)
                                    </label>
                                    <p class="text-[11px] text-amber-700 dark:text-amber-400">Centang jika pendaftar berasal dari luar negeri.</p>
                                </div>
                                <input type="checkbox" id="editIsLuarNegeri" wire:model.live="editIsLuarNegeri" class="rounded border-border text-primary focus:ring-primary h-4 w-4 cursor-pointer">
                            </div>

                            {{-- Input Negara --}}
                            <div>
                                <label class="block text-xs font-semibold text-text-main mb-1">Negara <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model="editNegara" placeholder="Contoh: Indonesia atau Malaysia" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                                @error('editNegara') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                            </div>

                            {{-- Input Provinsi --}}
                            <div>
                                <label class="block text-xs font-semibold text-text-main mb-1">Provinsi <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model="editProvinsi" placeholder="Contoh: Jawa Barat atau Sabah" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                                @error('editProvinsi') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                            </div>

                            {{-- Input Kabupaten / Kota --}}
                            <div>
                                <label class="block text-xs font-semibold text-text-main mb-1">Kabupaten / Kota <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model="editKabupaten" list="kabupatenList" placeholder="Contoh: Kab. Garut, Kota Bogor, atau Tawau" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                                <datalist id="kabupatenList">
                                    @foreach ($kabupatenMasterList as $km)
                                        <option value="{{ $km }}"></option>
                                    @endforeach
                                    <option value="Tawau"></option>
                                    <option value="Sandakan"></option>
                                    <option value="Kota Kinabalu"></option>
                                    <option value="Beaufort"></option>
                                    <option value="Lahad Datu"></option>
                                </datalist>
                                @error('editKabupaten') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror

                                {{-- Opsi Pill Buttons Cepat --}}
                                @if (!empty($modalPillOptions))
                                    <div class="mt-2 space-y-1">
                                        <div class="text-[11px] font-semibold text-text-muted flex items-center gap-1">
                                            <span>💡 Pilihan Cepat Terkait:</span>
                                        </div>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach ($modalPillOptions as $pill)
                                                <button type="button" wire:click="selectPillKabupaten('{{ $pill }}')" class="btn btn-secondary !py-1 !px-2.5 !text-xs font-mono {{ $editKabupaten === $pill ? '!border-primary !text-primary !bg-primary/10' : '' }}">
                                                    {{ $pill }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Petunjuk Master Kemendikbud --}}
                                @if ($modalHint)
                                    <div class="mt-2 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/50 text-blue-800 dark:text-blue-300 text-xs flex items-start gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $modalHint }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Input Kecamatan Standar --}}
                            <div>
                                <label class="block text-xs font-semibold text-text-main mb-1">Nama Kecamatan Bersih</label>
                                <input type="text" wire:model="editKecamatan" placeholder="Contoh: Tarogong Kaler (tanpa 'Kec.')" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary">
                                <p class="text-[11px] text-text-muted mt-1">Sistem akan otomatis menghapus awalan 'Kec.'</p>
                                @error('editKecamatan') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Footer Buttons --}}
                        <div class="p-4 sm:p-5 border-t border-border bg-surface-subtle flex items-center justify-end gap-2">
                            <button type="button" wire:click="closeEditModal" class="btn btn-secondary !py-1.5 !px-3 text-xs">
                                Batal
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary !py-1.5 !px-4 text-xs gap-1.5">
                                <span wire:loading.remove wire:target="saveStandardizedWilayah">Simpan Standarisasi</span>
                                <span wire:loading wire:target="saveStandardizedWilayah" class="flex items-center gap-1">
                                    <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                    <span>Menyimpan...</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
