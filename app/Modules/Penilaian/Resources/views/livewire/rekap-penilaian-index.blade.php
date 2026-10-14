<div class="space-y-4">
    <!-- Header Page Minimalis & Export Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-text-main">Rekap Nilai Semester</h1>
            <p class="text-xs text-text-secondary">Analisis statistik ketuntasan nilai, buku leger akademik kelas, dan rekap remedial per mata pelajaran.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Dropdown / Tombol Ekspor Excel -->
            <button type="button"
                    wire:click="exportLegerExcel"
                    wire:loading.attr="disabled"
                    @if(!$hasLoaded || !$selectedRombel) disabled @endif
                    class="btn btn-outline text-xs flex items-center gap-1.5 py-1.5 px-3 bg-surface hover:bg-surface-hover text-emerald-700 dark:text-emerald-400 border-emerald-300 dark:border-emerald-800 shadow-2xs font-semibold {{ (!$hasLoaded || !$selectedRombel) ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer' }}">
                <svg wire:loading.remove wire:target="exportLegerExcel" class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <svg wire:loading wire:target="exportLegerExcel" class="w-4 h-4 animate-spin text-emerald-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Unduh Leger (.xlsx)</span>
            </button>

            <button type="button"
                    wire:click="exportAnalisisExcel"
                    wire:loading.attr="disabled"
                    @if(!$hasLoaded || !$selectedRombel) disabled @endif
                    class="btn btn-outline text-xs flex items-center gap-1.5 py-1.5 px-3 bg-surface hover:bg-surface-hover text-primary border-primary/30 shadow-2xs font-semibold {{ (!$hasLoaded || !$selectedRombel) ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer' }}">
                <svg wire:loading.remove wire:target="exportAnalisisExcel" class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <svg wire:loading wire:target="exportAnalisisExcel" class="w-4 h-4 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Unduh Analisis Mapel (.xlsx)</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if (session('status'))
        <div class="p-3 text-xs text-emerald-800 bg-emerald-50 rounded-lg border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-3 text-xs text-rose-800 bg-rose-50 rounded-lg dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
            {{ session('error') }}
        </div>
    @endif

    <!-- Baris Filter Rombel & Periode Akademik -->
    <div class="card p-3.5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <!-- Kolom 1: Tahun Ajaran -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">
                    Tahun Ajaran
                </label>
                <select wire:model.live="selectedTaId" class="w-full text-xs font-semibold rounded-lg border-border bg-surface text-text-main py-2 px-2.5 focus:ring-primary focus:border-primary">
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Kolom 2: Semester -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">
                    Semester
                </label>
                <select wire:model.live="selectedSemesterId" class="w-full text-xs font-semibold rounded-lg border-border bg-surface text-text-main py-2 px-2.5 focus:ring-primary focus:border-primary">
                    @forelse($semesters as $sem)
                        <option value="{{ $sem->id }}">Semester {{ ucfirst($sem->semester) }} {{ $sem->is_active ? '(Aktif)' : '' }}</option>
                    @empty
                        <option value="">-- Pilih TA dahulu --</option>
                    @endforelse
                </select>
            </div>

            <!-- Kolom 3: Tingkat Kelas -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">
                    Tingkat Kelas
                </label>
                <select wire:model.live="filterTingkat" class="w-full text-xs font-semibold rounded-lg border-border bg-surface text-text-main py-2 px-2.5 focus:ring-primary focus:border-primary">
                    <option value="semua">Semua Tingkat</option>
                    <option value="X">Kelas X</option>
                    <option value="XI">Kelas XI</option>
                    <option value="XII">Kelas XII</option>
                </select>
            </div>

            <!-- Kolom 4: Rombongan Belajar -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">
                    Rombongan Belajar
                </label>
                <select wire:model.live="selectedRombelId" class="w-full text-xs font-semibold rounded-lg border-border bg-surface text-text-main py-2 px-2.5 focus:ring-primary focus:border-primary">
                    <option value="">-- Pilih Rombel --</option>
                    @foreach($rombels as $rb)
                        <option value="{{ $rb->id }}">{{ $rb->nama }} ({{ $rb->tingkat }} - {{ $rb->jurusan?->kode ?? 'Umum' }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Kolom 5: Tombol Tampilkan Data -->
            <div>
                <button type="button"
                        wire:click="tampilkanData"
                        wire:loading.attr="disabled"
                        class="w-full btn btn-primary text-xs flex items-center justify-center gap-2 py-2 px-4 font-semibold shadow-sm cursor-pointer">
                    <svg wire:loading.remove wire:target="tampilkanData" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <svg wire:loading wire:target="tampilkanData" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Tampilkan Data</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Tab Navigasi Sub-Modul -->
    <div class="flex items-center border-b border-border gap-2 text-xs font-semibold overflow-x-auto">
        <button type="button"
                wire:click="switchSubTab('leger')"
                class="pb-2 px-3 border-b-2 flex items-center gap-1.5 transition-colors cursor-pointer whitespace-nowrap {{ $activeSubTab === 'leger' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Buku Leger Nilai Rombel</span>
        </button>

        <button type="button"
                wire:click="switchSubTab('mapel')"
                class="pb-2 px-3 border-b-2 flex items-center gap-1.5 transition-colors cursor-pointer whitespace-nowrap {{ $activeSubTab === 'mapel' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span>Analisis & Remedial Mapel</span>
        </button>

        <button type="button"
                wire:click="switchSubTab('komparasi')"
                class="pb-2 px-3 border-b-2 flex items-center gap-1.5 transition-colors cursor-pointer whitespace-nowrap {{ $activeSubTab === 'komparasi' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            <span>Komparasi Antar Rombel</span>
        </button>

        <button type="button"
                wire:click="switchSubTab('rayon')"
                class="pb-2 px-3 border-b-2 flex items-center gap-1.5 transition-colors cursor-pointer whitespace-nowrap {{ $activeSubTab === 'rayon' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>Komparasi Antar Rayon</span>
        </button>
    </div>

    @if(!$hasLoaded)
        <!-- Empty State Panduan Sebelum Tampilkan Data Sesuai SubTab -->
        <div class="card p-10 text-center border-dashed border-2 border-border/80 bg-surface/50">
            <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-3.5 shadow-2xs">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
            </div>
            @if(in_array($activeSubTab, ['leger', 'mapel']))
                <h3 class="text-base font-bold text-text-main mb-1">Silakan Pilih Rombongan Belajar</h3>
                <p class="text-xs text-text-secondary max-w-md mx-auto mb-4">
                    Tentukan Tahun Ajaran, Semester, dan Rombongan Belajar pada filter di atas, lalu klik tombol <strong class="text-primary font-semibold">Tampilkan Data</strong> untuk memuat {{ $activeSubTab === 'leger' ? 'Buku Leger Nilai Rombel' : 'Analisis Ketuntasan & Remedial Mapel' }}.
                </p>
            @elseif($activeSubTab === 'komparasi')
                <h3 class="text-base font-bold text-text-main mb-1">Komparasi Capaian Antar Rombel</h3>
                <p class="text-xs text-text-secondary max-w-md mx-auto mb-4">
                    Tentukan kriteria tingkat kelas pada filter di atas dan klik tombol <strong class="text-primary font-semibold">Tampilkan Data</strong> untuk memuat perbandingan nilai antar rombel.
                </p>
            @elseif($activeSubTab === 'rayon')
                <h3 class="text-base font-bold text-text-main mb-1">Komparasi Capaian Antar Rayon</h3>
                <p class="text-xs text-text-secondary max-w-md mx-auto mb-4">
                    Tentukan kriteria tingkat kelas pada filter di atas dan klik tombol <strong class="text-primary font-semibold">Tampilkan Data</strong> untuk memuat evaluasi ketuntasan nilai per rayon pembimbing.
                </p>
            @endif
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 text-[11px] text-text-secondary font-medium">
                <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Sistem tidak memproses data di awal untuk menjaga kecepatan halaman</span>
            </div>
        </div>
    @else
        <!-- ========================================== -->
        <!-- SUB-TAB 1: BUKU LEGER NILAI ROMBEL -->
        <!-- ========================================== -->
        @if($activeSubTab === 'leger')
            @if($selectedRombel && $legerData)
                <!-- Baris Statistik Ringkasan Rombel Minimalis (1 Baris Ringkas) -->
                <div class="card p-2.5 sm:p-3 flex flex-col xl:flex-row xl:items-center justify-between gap-3 text-xs shadow-2xs">
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                        <!-- Rata-rata Rombel -->
                        <div class="flex items-center gap-1.5">
                            <span class="text-text-secondary">Rata-Rata Rombel:</span>
                            <span class="font-extrabold font-mono text-primary text-xs sm:text-sm">{{ number_format($legerData['summary']['rata_rata_rombel'], 2) }}</span>
                            <span class="text-[11px] text-text-secondary">({{ $legerData['summary']['total_siswa'] }} Siswa)</span>
                        </div>

                        <div class="h-3.5 w-px bg-border hidden sm:block"></div>

                        <!-- Ketuntasan Klasikal -->
                        <div class="flex items-center gap-2">
                            <span class="text-text-secondary whitespace-nowrap">Ketuntasan Klasikal:</span>
                            <div class="w-16 sm:w-20 bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-300" style="width: {{ min(100, $legerData['summary']['persen_tuntas_klasikal']) }}%"></div>
                            </div>
                            <span class="font-bold font-mono text-emerald-600 dark:text-emerald-400">{{ $legerData['summary']['persen_tuntas_klasikal'] }}%</span>
                        </div>

                        <div class="h-3.5 w-px bg-border hidden sm:block"></div>

                        <!-- Bebas Remedial -->
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="text-emerald-700 dark:text-emerald-400 font-medium">Bebas Remedial:</span>
                            <span class="font-bold text-emerald-700 dark:text-emerald-300 font-mono">{{ $legerData['summary']['siswa_tuntas_count'] }}</span>
                            <span class="text-[11px] text-text-secondary">({{ $legerData['summary']['siswa_tuntas_persen'] }}%)</span>
                        </div>

                        <div class="h-3.5 w-px bg-border hidden sm:block"></div>

                        <!-- Butuh Remedial / Kritis -->
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ $legerData['summary']['siswa_remedial_count'] > 0 ? 'bg-amber-500' : 'bg-slate-400' }} shrink-0"></span>
                            <span class="{{ $legerData['summary']['siswa_remedial_count'] > 0 ? 'text-amber-700 dark:text-amber-400' : 'text-text-secondary' }} font-medium">Remedial:</span>
                            <span class="font-bold {{ $legerData['summary']['siswa_remedial_count'] > 0 ? 'text-amber-700 dark:text-amber-300' : 'text-text-secondary' }} font-mono">{{ $legerData['summary']['siswa_remedial_count'] }}</span>
                            @if($legerData['summary']['siswa_kritis_count'] > 0)
                                <span class="px-1.5 py-0.2 rounded bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 text-[10px] font-bold">
                                    {{ $legerData['summary']['siswa_kritis_count'] }} Kritis
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Juara 1 Kelas -->
                    <div class="flex items-center gap-2 shrink-0 pt-2 xl:pt-0 border-t xl:border-t-0 border-border">
                        <span class="text-xs">🏆</span>
                        <span class="text-text-secondary text-[11px]">Juara 1:</span>
                        <span class="font-bold text-text-main truncate max-w-[150px] text-xs" title="{{ $legerData['summary']['top_student_nama'] }}">
                            {{ $legerData['summary']['top_student_nama'] }}
                        </span>
                        <span class="font-bold font-mono text-amber-700 dark:text-amber-400 text-xs">
                            ({{ number_format($legerData['summary']['top_student_avg'], 2) }})
                        </span>
                    </div>
                </div>

                <div class="card p-0 overflow-hidden shadow-2xs space-y-0 border border-border">
                <!-- Toolbar Pencarian, Info Kelas, & Keterangan Freeze -->
                <div class="px-4 py-2.5 border-b border-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 bg-surface-hover/40">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="font-bold text-text-main">{{ $selectedRombel->nama }}</span>
                        <span class="text-text-secondary">&bull;</span>
                        <span class="text-text-secondary">{{ $selectedRombel->jurusan?->nama ?? '-' }}</span>
                        <span class="text-text-secondary">&bull;</span>
                        <span class="text-text-secondary">Wali Kelas: <b class="text-text-main">{{ $selectedRombel->waliKelas?->name ?? 'Belum Ditugaskan' }}</b></span>
                        <span class="text-text-secondary">&bull;</span>
                        <span class="px-2 py-0.5 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-medium text-[11px]">
                            {{ count($legerData['siswa_rows']) }} Siswa Terdata
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <div class="relative w-full sm:w-60">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-text-secondary">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text"
                                   wire:model.live.debounce.300ms="search"
                                   placeholder="Cari siswa (Nama/NIS)..."
                                   class="w-full text-xs rounded-lg border-border bg-surface text-text-main pl-8 pr-7 py-1.5 focus:ring-primary focus:border-primary">
                            @if($search)
                                <button type="button" wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-text-secondary hover:text-text-main cursor-pointer text-xs font-bold">
                                    &times;
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tabel Matriks Leger Nilai dengan Freeze Column & Grouping -->
                @php
                    $groupedMapels = collect($legerData['mapel_list'])->groupBy('kelompok_label');
                @endphp

                <div class="overflow-x-auto max-h-[620px] relative">
                    <table class="min-w-full border-separate border-spacing-0 text-left text-xs">
                        <!-- HEADER: Sticky Top -->
                        <thead class="sticky top-0 z-30 shadow-2xs">
                            <!-- HEADER BARIS 1: Kelompok Mapel & Kolom Rekap -->
                            <tr class="text-[11px] uppercase tracking-wider font-semibold">
                                <!-- No (Sticky Left: 0px) -->
                                <th rowspan="2" class="sticky top-0 left-0 z-40 w-10 min-w-[40px] max-w-[40px] p-2 text-center bg-slate-100 dark:bg-slate-800 border-b-2 border-r border-border font-bold text-text-main">
                                    No
                                </th>

                                <!-- NIS (Sticky Left: 40px) -->
                                <th rowspan="2" class="sticky top-0 left-[40px] z-40 w-24 min-w-[90px] max-w-[90px] p-2 text-center bg-slate-100 dark:bg-slate-800 border-b-2 border-r border-border font-bold text-text-main whitespace-nowrap">
                                    NIS
                                </th>

                                <!-- Nama Peserta Didik (Sticky Left: 130px) -->
                                <th rowspan="2" class="sticky top-0 left-[130px] z-40 min-w-[190px] max-w-[220px] p-2.5 text-left bg-slate-100 dark:bg-slate-800 border-b-2 border-r-2 border-border font-bold text-text-main shadow-[4px_0_8px_-2px_rgba(0,0,0,0.12)] whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <span>Nama Peserta Didik</span>
                                        <span class="text-[9px] px-1 py-0.2 rounded bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-300 font-semibold ml-auto">Freeze</span>
                                    </div>
                                </th>

                                <!-- Kelompok Mapel Kurikulum -->
                                @foreach($groupedMapels as $kelompokName => $gMapels)
                                    <th colspan="{{ count($gMapels) }}" class="p-1.5 text-center border-b border-r border-border bg-slate-200/90 dark:bg-slate-700/90 text-text-main font-bold text-[10px] tracking-wider">
                                        {{ $kelompokName }}
                                    </th>
                                @endforeach

                                <!-- Kolom Rekapitulasi Nilai Siswa -->
                                <th rowspan="2" class="w-16 min-w-[65px] p-2 text-center bg-slate-100 dark:bg-slate-800 border-b-2 border-l-2 border-r border-border font-bold text-text-main">
                                    Total
                                </th>
                                <th rowspan="2" class="w-16 min-w-[65px] p-2 text-center bg-slate-100 dark:bg-slate-800 border-b-2 border-r border-border font-bold text-text-main">
                                    Rerata
                                </th>
                                <th rowspan="2" class="w-14 min-w-[55px] p-2 text-center bg-slate-100 dark:bg-slate-800 border-b-2 border-r border-border font-bold text-text-main">
                                    Rank
                                </th>
                                <th rowspan="2" class="w-14 min-w-[55px] p-2 text-center bg-slate-100 dark:bg-slate-800 border-b-2 border-border font-bold text-rose-600 dark:text-rose-400" title="Jumlah Mapel di Bawah KKM">
                                    &lt; KKM
                                </th>
                            </tr>

                            <!-- HEADER BARIS 2: Kode Mapel & KKM Badge -->
                            <tr class="text-[10px] text-text-secondary bg-surface-hover">
                                @foreach($legerData['mapel_list'] as $mId => $mInfo)
                                    @php
                                        $isSeniCol = strtolower($mInfo['kode']) === 'seni' || str_contains(strtolower($mInfo['nama']), 'seni');
                                    @endphp
                                    <th class="p-1.5 text-center min-w-[85px] sm:min-w-[95px] border-b-2 border-r border-border {{ $isSeniCol ? 'bg-purple-50/80 dark:bg-purple-950/50' : 'bg-slate-100/90 dark:bg-slate-800/90' }}" title="{{ $mInfo['nama'] }} (Guru: {{ $mInfo['guru'] }})">
                                        <div class="font-bold text-text-main truncate max-w-[95px] mx-auto text-xs">{{ $mInfo['kode'] }}</div>
                                        <div class="text-[9px] truncate max-w-[95px] mx-auto {{ $isSeniCol ? 'text-purple-700 dark:text-purple-300 font-bold' : 'text-text-secondary font-medium' }}">
                                            {{ $isSeniCol ? 'Seni Budaya' : $mInfo['nama'] }}
                                        </div>
                                        <div class="mt-0.5">
                                            <span class="inline-block px-1 py-0.2 rounded bg-slate-200/80 dark:bg-slate-700/80 text-text-secondary text-[9px] font-semibold">
                                                KKM {{ $mInfo['kkm'] }}
                                            </span>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>

                        <!-- BODY: Data Siswa -->
                        <tbody class="bg-surface text-text-main">
                            @forelse($legerData['siswa_rows'] as $sIdx => $sRow)
                                <tr class="group hover:bg-surface-hover/70 transition-colors {{ $sRow['below_kkm_count'] > 0 ? 'bg-rose-50/20 dark:bg-rose-950/10' : '' }}">
                                    <!-- No (Sticky Left 0px) -->
                                    <td class="w-10 min-w-[40px] max-w-[40px] p-2 text-center font-mono text-text-secondary sticky left-0 z-20 bg-surface dark:bg-surface-card group-hover:bg-surface-hover border-b border-r border-border">
                                        {{ $loop->iteration }}
                                    </td>

                                    <!-- NIS (Sticky Left 40px) -->
                                    <td class="w-24 min-w-[90px] max-w-[90px] p-2 font-mono text-text-secondary sticky left-[40px] z-20 bg-surface dark:bg-surface-card group-hover:bg-surface-hover border-b border-r border-border whitespace-nowrap text-center">
                                        {{ $sRow['nis'] }}
                                    </td>

                                    <!-- Nama Siswa (Sticky Left 130px) -->
                                    <td class="min-w-[190px] max-w-[220px] p-2.5 font-medium sticky left-[130px] z-20 bg-surface dark:bg-surface-card group-hover:bg-surface-hover border-b border-r-2 border-border shadow-[4px_0_8px_-2px_rgba(0,0,0,0.12)] whitespace-nowrap">
                                        <div class="truncate text-xs font-semibold text-text-main" title="{{ $sRow['nama'] }}">{{ $sRow['nama'] }}</div>
                                    </td>

                                    <!-- Nilai Tiap Mata Pelajaran -->
                                    @foreach($legerData['mapel_list'] as $mId => $mInfo)
                                        @php
                                            $sc = $sRow['scores'][$mId];
                                            $nVal = $sc['nilai_akhir'];
                                        @endphp
                                        <td class="p-2 text-center font-mono border-b border-r border-border whitespace-nowrap {{ $sc['is_below_kkm'] ? 'bg-rose-100/70 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 font-bold' : 'text-text-main' }}">
                                            @if($nVal !== null)
                                                <span title="{{ $mInfo['nama'] }}: {{ $nVal }} (KKM {{ $mInfo['kkm'] }}){{ !empty($sc['cabang_nama']) ? ' | Cabang: ' . $sc['cabang_nama'] . ' | STS: ' . ($sc['sts'] ?? '-') . ' | SAS: ' . ($sc['sas'] ?? '-') : '' }}">
                                                    {{ number_format($nVal, 0) }}
                                                </span>
                                                @if(!empty($sc['cabang_nama']))
                                                    <span class="block text-[9px] font-sans font-medium text-purple-700 dark:text-purple-300">
                                                        [{{ $sc['cabang_nama'] }}]
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-slate-300 dark:text-slate-600">-</span>
                                            @endif
                                        </td>
                                    @endforeach

                                    <!-- Total Nilai -->
                                    <td class="p-2 text-center font-mono font-bold border-b border-l-2 border-r border-border bg-slate-50/70 dark:bg-slate-900/40 text-text-main">
                                        {{ number_format($sRow['total_nilai'], 0) }}
                                    </td>

                                    <!-- Rata-rata -->
                                    <td class="p-2 text-center font-mono font-bold border-b border-r border-border bg-slate-50/70 dark:bg-slate-900/40 text-primary">
                                        {{ number_format($sRow['rata_rata'], 2) }}
                                    </td>

                                    <!-- Peringkat -->
                                    <td class="p-2 text-center border-b border-r border-border bg-slate-50/70 dark:bg-slate-900/40 font-bold">
                                        @if($sRow['rank'] === 1)
                                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-extrabold shadow-2xs">🥇 1</span>
                                        @elseif($sRow['rank'] === 2)
                                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full bg-slate-200 text-slate-700 text-[10px] font-extrabold">🥈 2</span>
                                        @elseif($sRow['rank'] === 3)
                                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full bg-amber-700/20 text-amber-900 dark:text-amber-300 text-[10px] font-extrabold">🥉 3</span>
                                        @else
                                            <span class="font-mono text-text-secondary text-xs">{{ $sRow['rank'] }}</span>
                                        @endif
                                    </td>

                                    <!-- Jumlah Mapel di Bawah KKM -->
                                    <td class="p-2 text-center font-mono font-bold border-b border-border {{ $sRow['below_kkm_count'] > 0 ? 'text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/30' : 'text-slate-400' }}">
                                        {{ $sRow['below_kkm_count'] > 0 ? $sRow['below_kkm_count'] : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($legerData['mapel_list']) + 7 }}" class="px-4 py-8 text-center text-text-secondary">
                                        Tidak ada data siswa ditemukan untuk kriteria filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        <!-- FOOTER LEGER: STATISTIK AKADEMIK KELAS (Sticky Bottom) -->
                        <tfoot class="bg-slate-100 dark:bg-slate-800 text-text-main font-bold border-t-2 border-border text-[11px] sticky bottom-0 z-30 shadow-[0_-4px_8px_-2px_rgba(0,0,0,0.08)]">
                            <!-- 1. Rata-rata Kelas -->
                            <tr>
                                <td class="sticky left-0 z-40 w-10 min-w-[40px] max-w-[40px] bg-slate-100 dark:bg-slate-800 border-r border-b border-border"></td>
                                <td class="sticky left-[40px] z-40 w-24 min-w-[90px] max-w-[90px] bg-slate-100 dark:bg-slate-800 border-r border-b border-border"></td>
                                <td class="sticky left-[130px] z-40 min-w-[190px] max-w-[220px] p-2 text-right uppercase tracking-wider text-text-secondary bg-slate-100 dark:bg-slate-800 border-r-2 border-b border-border shadow-[4px_0_8px_-2px_rgba(0,0,0,0.12)] text-[10px]">
                                    Rata-Rata Kelas
                                </td>
                                @foreach($legerData['mapel_list'] as $mId => $mInfo)
                                    <td class="p-2 text-center font-mono border-r border-b border-border text-primary font-bold">
                                        {{ number_format($legerData['mapel_stats'][$mId]['avg'], 1) }}
                                    </td>
                                @endforeach
                                <td class="p-2 text-center font-mono border-l-2 border-r border-b border-border bg-slate-200/50 dark:bg-slate-700/50">-</td>
                                <td class="p-2 text-center font-mono text-primary font-extrabold border-r border-b border-border bg-slate-200/50 dark:bg-slate-700/50">
                                    {{ number_format($legerData['summary']['rata_rata_rombel'], 2) }}
                                </td>
                                <td colspan="2" class="p-2 text-center text-text-secondary font-normal text-[10px] border-b border-border bg-slate-200/50 dark:bg-slate-700/50">Rerata Rombel</td>
                            </tr>

                            <!-- 2. Nilai Tertinggi -->
                            <tr>
                                <td class="sticky left-0 z-40 w-10 min-w-[40px] max-w-[40px] bg-slate-100 dark:bg-slate-800 border-r border-b border-border"></td>
                                <td class="sticky left-[40px] z-40 w-24 min-w-[90px] max-w-[90px] bg-slate-100 dark:bg-slate-800 border-r border-b border-border"></td>
                                <td class="sticky left-[130px] z-40 min-w-[190px] max-w-[220px] p-2 text-right uppercase tracking-wider text-text-secondary bg-slate-100 dark:bg-slate-800 border-r-2 border-b border-border shadow-[4px_0_8px_-2px_rgba(0,0,0,0.12)] text-[10px]">
                                    Nilai Tertinggi (Max)
                                </td>
                                @foreach($legerData['mapel_list'] as $mId => $mInfo)
                                    <td class="p-2 text-center font-mono border-r border-b border-border text-emerald-600 dark:text-emerald-400 font-bold">
                                        {{ number_format($legerData['mapel_stats'][$mId]['max'], 0) }}
                                    </td>
                                @endforeach
                                <td colspan="4" class="p-2 bg-slate-100 dark:bg-slate-800 border-b border-border"></td>
                            </tr>

                            <!-- 3. Nilai Terendah -->
                            <tr>
                                <td class="sticky left-0 z-40 w-10 min-w-[40px] max-w-[40px] bg-slate-100 dark:bg-slate-800 border-r border-b border-border"></td>
                                <td class="sticky left-[40px] z-40 w-24 min-w-[90px] max-w-[90px] bg-slate-100 dark:bg-slate-800 border-r border-b border-border"></td>
                                <td class="sticky left-[130px] z-40 min-w-[190px] max-w-[220px] p-2 text-right uppercase tracking-wider text-text-secondary bg-slate-100 dark:bg-slate-800 border-r-2 border-b border-border shadow-[4px_0_8px_-2px_rgba(0,0,0,0.12)] text-[10px]">
                                    Nilai Terendah (Min)
                                </td>
                                @foreach($legerData['mapel_list'] as $mId => $mInfo)
                                    <td class="p-2 text-center font-mono border-r border-b border-border text-rose-600 dark:text-rose-400 font-bold">
                                        {{ number_format($legerData['mapel_stats'][$mId]['min'], 0) }}
                                    </td>
                                @endforeach
                                <td colspan="4" class="p-2 bg-slate-100 dark:bg-slate-800 border-b border-border"></td>
                            </tr>

                            <!-- 4. Ketuntasan Klasikal -->
                            <tr>
                                <td class="sticky left-0 z-40 w-10 min-w-[40px] max-w-[40px] bg-slate-100 dark:bg-slate-800 border-r border-border"></td>
                                <td class="sticky left-[40px] z-40 w-24 min-w-[90px] max-w-[90px] bg-slate-100 dark:bg-slate-800 border-r border-border"></td>
                                <td class="sticky left-[130px] z-40 min-w-[190px] max-w-[220px] p-2 text-right uppercase tracking-wider text-text-secondary bg-slate-100 dark:bg-slate-800 border-r-2 border-border shadow-[4px_0_8px_-2px_rgba(0,0,0,0.12)] text-[10px]">
                                    % Ketuntasan (&ge; KKM)
                                </td>
                                @foreach($legerData['mapel_list'] as $mId => $mInfo)
                                    <td class="p-2 text-center font-mono border-r border-border text-emerald-600 dark:text-emerald-400 font-bold">
                                        {{ $legerData['mapel_stats'][$mId]['persen_tuntas'] }}%
                                    </td>
                                @endforeach
                                <td colspan="2" class="p-2 text-center font-mono text-emerald-600 dark:text-emerald-400 border-l-2 border-r border-border bg-slate-200/50 dark:bg-slate-700/50 font-bold">
                                    {{ $legerData['summary']['persen_tuntas_klasikal'] }}%
                                </td>
                                <td colspan="2" class="p-2 text-center text-text-secondary font-normal text-[10px] bg-slate-200/50 dark:bg-slate-700/50">Ketuntasan Kelas</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Legenda & Keterangan Warna -->
                <div class="px-4 py-2.5 bg-surface-hover/40 border-t border-border flex flex-wrap items-center justify-between gap-3 text-xs text-text-secondary">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded bg-rose-100 border border-rose-300 dark:bg-rose-950 dark:border-rose-800"></span>
                            <span>Nilai di Bawah KKM (Perlu Remedial)</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded bg-surface border border-border"></span>
                            <span>Tuntas (&ge; KKM)</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="card p-8 text-center text-text-secondary">
                    <p class="text-xs">Silakan pilih rombongan belajar terlebih dahulu dan klik Tampilkan Data.</p>
                </div>
            @endif
        @endif

        <!-- ========================================== -->
        <!-- SUB-TAB 2: ANALISIS & REMEDIAL MAPEL -->
        <!-- ========================================== -->
        @if($activeSubTab === 'mapel')
            @if($selectedRombel && $legerData)
                <div class="space-y-4">
                <!-- Pemilih Mata Pelajaran -->
                <div class="card p-3">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1.5">
                        Pilih Mata Pelajaran untuk Analisis Ketuntasan
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach($legerData['mapel_list'] as $mId => $mInfo)
                            <button type="button"
                                    wire:click="$set('selectedMapelId', {{ $mId }})"
                                    class="p-2 rounded-lg border text-left transition-all flex items-center justify-between cursor-pointer {{ $selectedMapelId == $mId ? 'border-primary bg-primary/5 text-primary ring-1 ring-primary' : 'border-border bg-surface hover:bg-surface-hover text-text-main' }}">
                                <div class="min-w-0 pr-2">
                                    <div class="text-xs font-bold truncate">{{ $mInfo['nama'] }}</div>
                                    <div class="text-[10px] text-text-secondary truncate">Guru: {{ $mInfo['guru'] }}</div>
                                </div>
                                <div class="shrink-0 text-right">
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 font-semibold font-mono text-text-main">
                                        KKM {{ $mInfo['kkm'] }}
                                    </span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                @if($selectedMapelId && isset($legerData['mapel_stats'][$selectedMapelId]))
                    @php
                        $st = $legerData['mapel_stats'][$selectedMapelId];
                        $totGraded = $st['count_graded'];
                        $dist = $st['grade_distribution'];
                    @endphp

                    <!-- Ringkasan Mapel Terpilih Minimalis (1 Baris Ringkas) -->
                    <div class="card p-2.5 sm:p-3 flex flex-wrap items-center justify-between gap-x-5 gap-y-2 text-xs shadow-2xs">
                        <div class="flex items-center gap-1.5">
                            <span class="text-text-secondary">Rata-Rata Mapel:</span>
                            <span class="font-extrabold font-mono text-primary text-xs sm:text-sm">{{ number_format($st['avg'], 2) }}</span>
                            <span class="text-[11px] text-text-secondary">(KKM: {{ $st['kkm'] }})</span>
                        </div>

                        <div class="h-3.5 w-px bg-border hidden sm:block"></div>

                        <div class="flex items-center gap-2">
                            <span class="text-text-secondary">Rentang Nilai:</span>
                            <span class="font-bold font-mono text-emerald-600 dark:text-emerald-400">Max {{ number_format($st['max'], 0) }}</span>
                            <span class="text-text-secondary">|</span>
                            <span class="font-bold font-mono text-rose-600 dark:text-rose-400">Min {{ number_format($st['min'], 0) }}</span>
                            <span class="text-[11px] text-text-secondary">({{ $totGraded }} Siswa)</span>
                        </div>

                        <div class="h-3.5 w-px bg-border hidden sm:block"></div>

                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="text-emerald-700 dark:text-emerald-400 font-medium">Ketuntasan:</span>
                            <span class="font-bold text-emerald-700 dark:text-emerald-300 font-mono">{{ $st['persen_tuntas'] }}%</span>
                            <span class="text-[11px] text-text-secondary">({{ $st['tuntas_count'] }}/{{ $totGraded }})</span>
                        </div>

                        <div class="h-3.5 w-px bg-border hidden sm:block"></div>

                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ $st['remedial_count'] > 0 ? 'bg-rose-500' : 'bg-emerald-500' }} shrink-0"></span>
                            <span class="{{ $st['remedial_count'] > 0 ? 'text-rose-700 dark:text-rose-400 font-semibold' : 'text-emerald-700 dark:text-emerald-400 font-medium' }}">
                                Remedial:
                            </span>
                            <span class="font-bold font-mono {{ $st['remedial_count'] > 0 ? 'text-rose-700 dark:text-rose-300' : 'text-emerald-700 dark:text-emerald-300' }}">
                                {{ $st['remedial_count'] }} Siswa
                            </span>
                        </div>
                    </div>

                    <!-- Visualisasi Sebaran Predikat Mutu Nilai (A, B, C, D) -->
                    <div class="card p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-text-main flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                                </svg>
                                <span>Distribusi Mutu Capaian Kompetensi (A / B / C / D)</span>
                            </h3>
                            <span class="text-[11px] text-text-secondary">Total {{ $totGraded }} Siswa</span>
                        </div>

                        <!-- Stacked Progress Bar -->
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-3 overflow-hidden flex shadow-2xs">
                            @php
                                $pA = $totGraded > 0 ? round(($dist['A'] / $totGraded) * 100, 1) : 0;
                                $pB = $totGraded > 0 ? round(($dist['B'] / $totGraded) * 100, 1) : 0;
                                $pC = $totGraded > 0 ? round(($dist['C'] / $totGraded) * 100, 1) : 0;
                                $pD = $totGraded > 0 ? round(($dist['D'] / $totGraded) * 100, 1) : 0;
                            @endphp
                            @if($pA > 0)
                                <div style="width: {{ $pA }}%" class="bg-emerald-500 h-full" title="Predikat A: {{ $dist['A'] }} Siswa ({{ $pA }}%)"></div>
                            @endif
                            @if($pB > 0)
                                <div style="width: {{ $pB }}%" class="bg-blue-500 h-full" title="Predikat B: {{ $dist['B'] }} Siswa ({{ $pB }}%)"></div>
                            @endif
                            @if($pC > 0)
                                <div style="width: {{ $pC }}%" class="bg-amber-400 h-full" title="Predikat C: {{ $dist['C'] }} Siswa ({{ $pC }}%)"></div>
                            @endif
                            @if($pD > 0)
                                <div style="width: {{ $pD }}%" class="bg-rose-500 h-full" title="Predikat D: {{ $dist['D'] }} Siswa ({{ $pD }}%)"></div>
                            @endif
                        </div>

                        <!-- Keterangan Legend & Counter -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1 text-xs">
                            <div class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800">
                                <div class="flex items-center gap-1.5 font-bold text-emerald-800 dark:text-emerald-300">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    <span>Predikat A (&ge; 90)</span>
                                </div>
                                <div class="mt-1 flex items-baseline justify-between text-emerald-900 dark:text-emerald-200">
                                    <span class="font-extrabold text-sm font-mono">{{ $dist['A'] }} Siswa</span>
                                    <span class="text-[11px] font-semibold">{{ $pA }}%</span>
                                </div>
                                <div class="text-[10px] text-emerald-700/80 dark:text-emerald-400">Sangat Mahir</div>
                            </div>

                            <div class="p-2 rounded-lg bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center gap-1.5 font-bold text-blue-800 dark:text-blue-300">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                    <span>Predikat B (80 - 89)</span>
                                </div>
                                <div class="mt-1 flex items-baseline justify-between text-blue-900 dark:text-blue-200">
                                    <span class="font-extrabold text-sm font-mono">{{ $dist['B'] }} Siswa</span>
                                    <span class="text-[11px] font-semibold">{{ $pB }}%</span>
                                </div>
                                <div class="text-[10px] text-blue-700/80 dark:text-blue-400">Tuntas &amp; Baik</div>
                            </div>

                            <div class="p-2 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800">
                                <div class="flex items-center gap-1.5 font-bold text-amber-800 dark:text-amber-300">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                    <span>Predikat C (KKM - 79)</span>
                                </div>
                                <div class="mt-1 flex items-baseline justify-between text-amber-900 dark:text-amber-200">
                                    <span class="font-extrabold text-sm font-mono">{{ $dist['C'] }} Siswa</span>
                                    <span class="text-[11px] font-semibold">{{ $pC }}%</span>
                                </div>
                                <div class="text-[10px] text-amber-700/80 dark:text-amber-400">Cukup Kompeten</div>
                            </div>

                            <div class="p-2 rounded-lg bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800">
                                <div class="flex items-center gap-1.5 font-bold text-rose-800 dark:text-rose-300">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                    <span>Predikat D (&lt; KKM)</span>
                                </div>
                                <div class="mt-1 flex items-baseline justify-between text-rose-900 dark:text-rose-200">
                                    <span class="font-extrabold text-sm font-mono">{{ $dist['D'] }} Siswa</span>
                                    <span class="text-[11px] font-semibold">{{ $pD }}%</span>
                                </div>
                                <div class="text-[10px] text-rose-700/80 dark:text-rose-400">Butuh Remedial</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Daftar Siswa Remedial -->
                    <div class="card p-0 overflow-hidden shadow-2xs">
                        <div class="px-4 py-2.5 border-b border-border flex items-center justify-between bg-surface-hover/30">
                            <div>
                                <h3 class="text-xs font-bold text-text-main">Daftar Siswa Remedial Mata Pelajaran: {{ $st['nama'] }}</h3>
                                <p class="text-[11px] text-text-secondary">Siswa dengan Nilai Akhir di bawah KKM ({{ $st['kkm'] }}) yang memerlukan pendampingan.</p>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $st['remedial_count'] > 0 ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' }}">
                                {{ $st['remedial_count'] }} Siswa
                            </span>
                        </div>

                        @if(empty($st['remedial_students']))
                            <div class="p-8 text-center">
                                <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-2 text-xl font-bold">
                                    ✓
                                </div>
                                <h4 class="text-xs font-bold text-text-main">Alhamdulillah! 100% Siswa Tuntas</h4>
                                <p class="text-[11px] text-text-secondary mt-1">Tidak ada peserta didik yang memiliki nilai di bawah KKM untuk mata pelajaran ini.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-border text-left text-xs">
                                    <thead class="bg-surface-hover text-text-secondary uppercase text-[11px] font-semibold">
                                        <tr>
                                            <th class="px-3 py-2 w-10 text-center">No</th>
                                            <th class="px-3 py-2 w-28">NIS</th>
                                            <th class="px-4 py-2">Nama Peserta Didik</th>
                                            <th class="px-3 py-2 text-center w-20">Nilai</th>
                                            <th class="px-3 py-2 text-center w-16">KKM</th>
                                            <th class="px-3 py-2 text-center w-20">Defisit</th>
                                            <th class="px-4 py-2">Area / Catatan Bimbingan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-border bg-surface text-text-main">
                                        @foreach($st['remedial_students'] as $rIdx => $rSiswa)
                                            <tr class="hover:bg-surface-hover/50 transition-colors">
                                                <td class="px-3 py-2 text-center font-mono text-text-secondary">{{ $loop->iteration }}</td>
                                                <td class="px-3 py-2 font-mono text-text-secondary">{{ $rSiswa['nis'] }}</td>
                                                <td class="px-4 py-2 font-semibold text-text-main">{{ $rSiswa['nama'] }}</td>
                                                <td class="px-3 py-2 text-center font-mono font-bold text-rose-600 dark:text-rose-400 bg-rose-50/50 dark:bg-rose-950/30">
                                                    {{ number_format($rSiswa['nilai'], 0) }}
                                                </td>
                                                <td class="px-3 py-2 text-center font-mono text-text-secondary">{{ $rSiswa['kkm'] }}</td>
                                                <td class="px-3 py-2 text-center font-mono font-bold text-rose-600 dark:text-rose-400">
                                                    -{{ number_format($rSiswa['selisih'], 0) }}
                                                </td>
                                                <td class="px-4 py-2 text-text-secondary text-[11px] leading-relaxed">
                                                    {{ $rSiswa['catatan'] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            @else
                <div class="card p-8 text-center text-text-secondary">
                    <p class="text-xs">Silakan pilih rombongan belajar terlebih dahulu dan klik Tampilkan Data.</p>
                </div>
            @endif
        @endif

        <!-- ========================================== -->
        <!-- SUB-TAB 3: KOMPARASI ANTAR ROMBEL -->
        <!-- ========================================== -->
        @if($activeSubTab === 'komparasi')
            <div class="card p-0 overflow-hidden shadow-2xs space-y-0">
                <div class="px-4 py-2.5 border-b border-border flex items-center justify-between bg-surface-hover/30">
                    <div>
                        <h3 class="text-xs font-bold text-text-main">Komparasi Capaian Akademik Antar Rombel</h3>
                        <p class="text-[11px] text-text-secondary">Benchmarking performa rata-rata nilai, ketuntasan klasikal, dan sebaran remedial se-tingkat.</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-primary/10 text-primary">
                        {{ $filterTingkat === 'semua' ? 'Semua Tingkat' : 'Tingkat ' . $filterTingkat }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border text-left text-xs">
                        <thead class="bg-surface-hover text-text-secondary uppercase text-[11px] font-semibold">
                            <tr>
                                <th class="px-3 py-2 w-10 text-center">No</th>
                                <th class="px-4 py-2">Rombongan Belajar</th>
                                <th class="px-4 py-2">Wali Kelas</th>
                                <th class="px-3 py-2 text-center w-20">Siswa</th>
                                <th class="px-4 py-2 text-center w-36">Rata-Rata Rombel</th>
                                <th class="px-4 py-2 text-center w-36">% Ketuntasan Klasikal</th>
                                <th class="px-3 py-2 text-center w-24">Bebas Remedial</th>
                                <th class="px-3 py-2 text-center w-24">Siswa Kritis</th>
                                <th class="px-4 py-2">Siswa Terbaik</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border bg-surface text-text-main">
                            @forelse($crossRombelData as $cIdx => $cRow)
                                <tr class="hover:bg-surface-hover/50 transition-colors {{ $selectedRombelId == $cRow['rombel_id'] ? 'bg-primary/5 font-semibold' : '' }}">
                                    <td class="px-3 py-2 text-center font-mono text-text-secondary">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2">
                                        <div class="font-bold text-text-main flex items-center gap-1.5">
                                            <span>{{ $cRow['nama'] }}</span>
                                            @if($selectedRombelId == $cRow['rombel_id'])
                                                <span class="px-1.5 py-0.2 rounded bg-primary text-white text-[9px] font-normal">Aktif</span>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-text-secondary">{{ $cRow['jurusan'] }}</div>
                                    </td>
                                    <td class="px-4 py-2 text-text-secondary">{{ $cRow['wali_kelas'] }}</td>
                                    <td class="px-3 py-2 text-center font-mono">{{ $cRow['total_siswa'] }}</td>
                                    <td class="px-4 py-2 text-center">
                                        <div class="font-mono font-bold text-sm text-primary">{{ number_format($cRow['rata_rata_rombel'], 2) }}</div>
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-16 bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min(100, $cRow['persen_tuntas_klasikal']) }}%"></div>
                                            </div>
                                            <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ $cRow['persen_tuntas_klasikal'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 text-center font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                                        {{ $cRow['siswa_tuntas_count'] }}
                                    </td>
                                    <td class="px-3 py-2 text-center font-mono font-bold {{ $cRow['siswa_kritis_count'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400' }}">
                                        {{ $cRow['siswa_kritis_count'] > 0 ? $cRow['siswa_kritis_count'] : '-' }}
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="font-semibold text-text-main truncate max-w-[160px]" title="{{ $cRow['top_student_nama'] }}">
                                            {{ $cRow['top_student_nama'] }}
                                        </div>
                                        <div class="text-[10px] text-amber-700 dark:text-amber-400 font-mono">
                                            Rerata: {{ number_format($cRow['top_student_avg'], 2) }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-text-secondary">
                                        Tidak ada data komparasi rombel yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- SUB-TAB 4: KOMPARASI ANTAR RAYON -->
        <!-- ========================================== -->
        @if($activeSubTab === 'rayon')
            <div class="space-y-3">
                <!-- Highlight Performa Rayon Terbaik -->
                @php
                    $activeRayonsWithStudents = $crossRayonData->filter(fn($r) => $r['total_siswa'] > 0);
                    $bestAvgRayon = $activeRayonsWithStudents->sortByDesc('rata_rata_rayon')->first();
                    $bestTuntasRayon = $activeRayonsWithStudents->sortByDesc('persen_tuntas_klasikal')->first();
                    $totalAllRayonStudents = $crossRayonData->sum('total_siswa');
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- 1. Rayon Terbaik (Rerata) -->
                    <div class="card p-3 flex items-center justify-between border-amber-200 dark:border-amber-900/50 bg-amber-50/20 dark:bg-amber-950/10">
                        <div>
                            <span class="text-[10px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider block">🏆 Rayon Rerata Tertinggi</span>
                            <div class="text-sm font-extrabold text-text-main mt-0.5 truncate max-w-[170px]" title="{{ $bestAvgRayon['nama'] ?? '-' }}">
                                {{ $bestAvgRayon['nama'] ?? '-' }}
                            </div>
                            <div class="text-[11px] text-text-secondary mt-0.5">
                                Pembimbing: {{ $bestAvgRayon['pembimbing'] ?? '-' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xl font-extrabold font-mono text-amber-700 dark:text-amber-400">
                                {{ number_format($bestAvgRayon['rata_rata_rayon'] ?? 0, 2) }}
                            </span>
                            <div class="text-[10px] text-text-secondary">Rerata Nilai</div>
                        </div>
                    </div>

                    <!-- 2. Rayon Ketuntasan Tertinggi -->
                    <div class="card p-3 flex items-center justify-between border-emerald-200 dark:border-emerald-900/50 bg-emerald-50/20 dark:bg-emerald-950/10">
                        <div>
                            <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block">🌟 Ketuntasan Klasikal Tertinggi</span>
                            <div class="text-sm font-extrabold text-text-main mt-0.5 truncate max-w-[170px]" title="{{ $bestTuntasRayon['nama'] ?? '-' }}">
                                {{ $bestTuntasRayon['nama'] ?? '-' }}
                            </div>
                            <div class="text-[11px] text-text-secondary mt-0.5">
                                {{ $bestTuntasRayon['siswa_tuntas_count'] ?? 0 }} siswa bebas remedial
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xl font-extrabold font-mono text-emerald-600 dark:text-emerald-400">
                                {{ $bestTuntasRayon['persen_tuntas_klasikal'] ?? 0 }}%
                            </span>
                            <div class="text-[10px] text-text-secondary">Ketuntasan</div>
                        </div>
                    </div>

                    <!-- 3. Total Rayon & Siswa Terdata -->
                    <div class="card p-3 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-text-secondary uppercase tracking-wider block">👥 Cakupan Evaluasi Rayon</span>
                            <div class="text-sm font-extrabold text-text-main mt-0.5">
                                {{ $crossRayonData->count() }} Rayon Terdaftar
                            </div>
                            <div class="text-[11px] text-text-secondary mt-0.5">
                                {{ $filterTingkat === 'semua' ? 'Semua Tingkat (X, XI, XII)' : 'Tingkat ' . $filterTingkat }}
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xl font-extrabold font-mono text-primary">
                                {{ $totalAllRayonStudents }}
                            </span>
                            <div class="text-[10px] text-text-secondary">Total Siswa</div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Komparasi Antar Rayon -->
                <div class="card p-0 overflow-hidden shadow-2xs space-y-0">
                    <div class="px-4 py-2.5 border-b border-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 bg-surface-hover/30">
                        <div>
                            <h3 class="text-xs font-bold text-text-main">Komparasi Capaian Akademik Antar Rayon</h3>
                            <p class="text-[11px] text-text-secondary">Benchmarking rata-rata nilai, ketuntasan klasikal, dan siswa bimbingan per wilayah rayon.</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-primary/10 text-primary self-start sm:self-auto">
                            {{ $filterTingkat === 'semua' ? 'Semua Tingkat' : 'Tingkat ' . $filterTingkat }}
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-border text-left text-xs">
                            <thead class="bg-surface-hover text-text-secondary uppercase text-[11px] font-semibold">
                                <tr>
                                    <th class="px-3 py-2 w-10 text-center">No</th>
                                    <th class="px-4 py-2">Rayon &amp; Wilayah</th>
                                    <th class="px-4 py-2">Pembimbing Rayon</th>
                                    <th class="px-3 py-2 text-center w-20">Siswa</th>
                                    <th class="px-4 py-2 text-center w-36">Rata-Rata Rayon</th>
                                    <th class="px-4 py-2 text-center w-36">% Ketuntasan Klasikal</th>
                                    <th class="px-3 py-2 text-center w-24">Bebas Remedial</th>
                                    <th class="px-3 py-2 text-center w-24">Siswa Kritis</th>
                                    <th class="px-4 py-2">Siswa Terbaik</th>
                                    <th class="px-3 py-2 text-center w-20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border bg-surface text-text-main">
                                @forelse($crossRayonData as $rIdx => $rRow)
                                    <tr class="hover:bg-surface-hover/50 transition-colors {{ $selectedRayonId == $rRow['rayon_id'] ? 'bg-primary/5 font-semibold ring-1 ring-primary/20' : '' }}">
                                        <td class="px-3 py-2 text-center font-mono text-text-secondary">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-2">
                                            <div class="font-bold text-text-main">{{ $rRow['nama'] }}</div>
                                            <div class="text-[10px] text-text-secondary">{{ $rRow['wilayah'] }}</div>
                                        </td>
                                        <td class="px-4 py-2 text-text-secondary">{{ $rRow['pembimbing'] }}</td>
                                        <td class="px-3 py-2 text-center font-mono font-bold">{{ $rRow['total_siswa'] }}</td>
                                        <td class="px-4 py-2 text-center">
                                            @if($rRow['total_siswa'] > 0)
                                                <span class="font-mono font-bold text-sm text-primary">
                                                    {{ number_format($rRow['rata_rata_rayon'], 2) }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 font-mono">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            @if($rRow['total_siswa'] > 0)
                                                <div class="flex items-center justify-center gap-2">
                                                    <div class="w-16 bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min(100, $rRow['persen_tuntas_klasikal']) }}%"></div>
                                                    </div>
                                                    <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                                        {{ $rRow['persen_tuntas_klasikal'] }}%
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-slate-400 font-mono">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-center font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                                            {{ $rRow['siswa_tuntas_count'] }}
                                        </td>
                                        <td class="px-3 py-2 text-center font-mono font-bold {{ $rRow['siswa_kritis_count'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400' }}">
                                            {{ $rRow['siswa_kritis_count'] > 0 ? $rRow['siswa_kritis_count'] : '-' }}
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($rRow['top_student_nama'] !== '-')
                                                <div class="font-semibold text-text-main truncate max-w-[150px]" title="{{ $rRow['top_student_nama'] }}">
                                                    {{ $rRow['top_student_nama'] }}
                                                </div>
                                                <div class="text-[10px] text-amber-700 dark:text-amber-400 font-mono">
                                                    {{ $rRow['top_student_rombel'] }} &bull; Rerata: {{ number_format($rRow['top_student_avg'], 2) }}
                                                </div>
                                            @else
                                                <span class="text-slate-400 font-mono">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            @if($rRow['total_siswa'] > 0)
                                                <button type="button"
                                                        wire:click="toggleRayonDetail({{ $rRow['rayon_id'] }})"
                                                        class="px-2 py-1 rounded text-[11px] font-semibold transition-all cursor-pointer {{ $selectedRayonId == $rRow['rayon_id'] ? 'bg-primary text-white shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-text-main' }}">
                                                    {{ $selectedRayonId == $rRow['rayon_id'] ? 'Tutup' : 'Rincian' }}
                                                </button>
                                            @else
                                                <span class="text-slate-300 dark:text-slate-600 text-xs">-</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Panel Rincian Siswa Rayon yang Terpilih -->
                                    @if($selectedRayonId == $rRow['rayon_id'] && !empty($rRow['students']))
                                        <tr class="bg-slate-50/80 dark:bg-slate-900/60">
                                            <td colspan="10" class="p-3 border-t border-b border-primary/20">
                                                <div class="space-y-2">
                                                    <div class="flex items-center justify-between">
                                                        <h4 class="text-xs font-bold text-text-main flex items-center gap-1.5">
                                                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                                                            <span>Daftar Peserta Didik Rayon: <b>{{ $rRow['nama'] }}</b> ({{ count($rRow['students']) }} Siswa)</span>
                                                        </h4>
                                                        <span class="text-[11px] text-text-secondary">Pembimbing: {{ $rRow['pembimbing'] }}</span>
                                                    </div>

                                                    <div class="overflow-x-auto rounded-lg border border-border bg-surface">
                                                        <table class="min-w-full divide-y divide-border text-left text-xs">
                                                            <thead class="bg-surface-hover text-text-secondary text-[10px] uppercase font-semibold">
                                                                <tr>
                                                                    <th class="px-3 py-1.5 w-10 text-center">No</th>
                                                                    <th class="px-3 py-1.5 w-24">NIS</th>
                                                                    <th class="px-4 py-1.5">Nama Siswa</th>
                                                                    <th class="px-3 py-1.5">Rombel</th>
                                                                    <th class="px-3 py-1.5 text-center">Mapel Dinilai</th>
                                                                    <th class="px-3 py-1.5 text-center">Rata-Rata</th>
                                                                    <th class="px-3 py-1.5 text-center">Mapel &lt; KKM</th>
                                                                    <th class="px-3 py-1.5 text-center">Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-border text-text-main">
                                                                @foreach($rRow['students'] as $sRayonIdx => $stRayon)
                                                                    <tr class="hover:bg-surface-hover/50">
                                                                        <td class="px-3 py-1.5 text-center font-mono text-text-secondary">{{ $loop->iteration }}</td>
                                                                        <td class="px-3 py-1.5 font-mono text-text-secondary">{{ $stRayon['nis'] }}</td>
                                                                        <td class="px-4 py-1.5 font-semibold">
                                                                            <span>{{ $stRayon['nama'] }}</span>
                                                                            @if($loop->first && $stRayon['rata_rata'] > 0)
                                                                                <span class="ml-1 text-[10px] text-amber-600 dark:text-amber-400 font-bold">🥇 Juara 1 Rayon</span>
                                                                            @endif
                                                                        </td>
                                                                        <td class="px-3 py-1.5 text-text-secondary">{{ $stRayon['rombel_nama'] }}</td>
                                                                        <td class="px-3 py-1.5 text-center font-mono">{{ $stRayon['count_mapel'] }}</td>
                                                                        <td class="px-3 py-1.5 text-center font-mono font-bold text-primary">
                                                                            {{ number_format($stRayon['rata_rata'], 2) }}
                                                                        </td>
                                                                        <td class="px-3 py-1.5 text-center font-mono font-bold {{ $stRayon['below_kkm_count'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400' }}">
                                                                            {{ $stRayon['below_kkm_count'] > 0 ? $stRayon['below_kkm_count'] : '-' }}
                                                                        </td>
                                                                        <td class="px-3 py-1.5 text-center">
                                                                            @if($stRayon['is_tuntas'])
                                                                                <span class="px-1.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 text-[10px] font-semibold">
                                                                                    Tuntas
                                                                                </span>
                                                                            @elseif($stRayon['count_mapel'] === 0)
                                                                                <span class="text-slate-400 text-[10px]">Belum Dinilai</span>
                                                                            @else
                                                                                <span class="px-1.5 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 text-[10px] font-semibold">
                                                                                    Remedial
                                                                                </span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-4 py-8 text-center text-text-secondary">
                                            Tidak ada data rayon yang ditemukan untuk periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
