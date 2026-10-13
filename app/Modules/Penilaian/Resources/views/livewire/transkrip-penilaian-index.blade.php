<div class="space-y-6">
    <!-- HEADER & TITLE -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-text-main flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-primary/10 text-primary">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </span>
                <span>Transkrip Siswa Tahunan</span>
            </h1>
            <p class="text-xs sm:text-sm text-text-secondary mt-1">
                Rekapitulasi Capaian Nilai Akademik Siswa Selama 3 Tahun (Kelas X s.d. XII) Berbasis Kurikulum SMK Wikrama
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if($activeTab === 'individual' && $selectedSiswaId)
                <a href="{{ route('penilaian.transkrip.pdf', ['siswa_id' => $selectedSiswaId]) }}"
                   target="_blank"
                   class="btn btn-outline text-xs flex items-center gap-1.5 py-2 px-3 border-border hover:bg-surface-hover">
                    <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak PDF Transkrip</span>
                </a>
            @endif

            <button type="button"
                    wire:click="exportExcel"
                    wire:loading.attr="disabled"
                    class="btn btn-primary text-xs flex items-center gap-1.5 py-2 px-3.5 font-semibold cursor-pointer shadow-sm">
                <svg wire:loading.remove wire:target="exportExcel" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <svg wire:loading wire:target="exportExcel" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Export Excel Matriks</span>
            </button>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="card p-4 sm:p-5 bg-surface border border-border rounded-2xl shadow-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4">
            <!-- 1. Tahun Pelajaran -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1.5">
                    Tahun Pelajaran
                </label>
                <select wire:model.live="selectedTaId"
                        class="w-full text-xs sm:text-sm rounded-xl border border-border bg-surface px-3 py-2 text-text-main focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="semua">Semua Tahun Pelajaran</option>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}">
                            TP {{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Jurusan -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1.5">
                    Kompetensi / Program Keahlian
                </label>
                <select wire:model.live="selectedJurusanId"
                        class="w-full text-xs sm:text-sm rounded-xl border border-border bg-surface px-3 py-2 text-text-main focus:outline-none focus:ring-2 focus:ring-primary">
                    @foreach($jurusans as $j)
                        <option value="{{ $j->id }}">{{ $j->nama }} ({{ $j->kode }})</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Tingkat -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1.5">
                    Tingkat Siswa
                </label>
                <select wire:model.live="selectedTingkat"
                        class="w-full text-xs sm:text-sm rounded-xl border border-border bg-surface px-3 py-2 text-text-main focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="XII">Tingkat XII (Kelulusan / Penuh 3 Thn)</option>
                    <option value="XI">Tingkat XI (Berjalan s.d. Sem 4)</option>
                    <option value="X">Tingkat X (Berjalan s.d. Sem 2)</option>
                    <option value="semua">Semua Tingkat</option>
                </select>
            </div>

            <!-- 4. Rombel -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1.5">
                    Pilihan Rombel / Kelas
                </label>
                <select wire:model.live="selectedRombelId"
                        class="w-full text-xs sm:text-sm rounded-xl border border-border bg-surface px-3 py-2 text-text-main focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">Semua Rombel di Kriteria Ini</option>
                    @if($selectedTaId === 'semua')
                        @foreach($rombels->groupBy(fn($r) => ($r->tahunAjaran?->nama ?? 'Tanpa TP') . ($r->tahunAjaran?->is_active ? ' (Aktif)' : '')) as $taLabel => $groupRombels)
                            <optgroup label="Tahun Pelajaran {{ $taLabel }}">
                                @foreach($groupRombels as $r)
                                    <option value="{{ $r->id }}">
                                        {{ $r->nama }} — TP {{ $r->tahunAjaran?->nama ?? '-' }}{{ $r->tahunAjaran?->is_active ? ' (Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    @else
                        @foreach($rombels as $r)
                            <option value="{{ $r->id }}">
                                {{ $r->nama }} — TP {{ $r->tahunAjaran?->nama ?? '-' }}{{ $r->tahunAjaran?->is_active ? ' (Aktif)' : '' }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- 5. Cari Siswa -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1.5">
                    Cari Nama / NISN / NIPD
                </label>
                <div class="relative">
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Ketik nama atau NIS..."
                           class="w-full text-xs sm:text-sm rounded-xl border border-border bg-surface pl-9 pr-3 py-2 text-text-main focus:outline-none focus:ring-2 focus:ring-primary">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-text-muted">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- TABS NAVIGATION -->
    <div class="flex items-center gap-2 border-b border-border overflow-x-auto pb-1 text-sm">
        <button type="button"
                wire:click="switchTab('matriks')"
                class="px-4 py-2.5 rounded-xl font-medium cursor-pointer transition-all flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'matriks' ? 'bg-primary text-white shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
            <span>📊</span>
            <span>Matriks Leger Angkatan (3 Tahun)</span>
            @if($cohortData && $cohortData['total_siswa'] > 0)
                <span class="text-xs px-2 py-0.5 rounded-full {{ $activeTab === 'matriks' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-text-secondary' }}">
                    {{ $cohortData['total_siswa'] }}
                </span>
            @endif
        </button>

        <button type="button"
                wire:click="switchTab('individual')"
                class="px-4 py-2.5 rounded-xl font-medium cursor-pointer transition-all flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'individual' ? 'bg-primary text-white shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
            <span>🎓</span>
            <span>Transkrip Individual Siswa</span>
        </button>

        <button type="button"
                wire:click="switchTab('analisis')"
                class="px-4 py-2.5 rounded-xl font-medium cursor-pointer transition-all flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'analisis' ? 'bg-primary text-white shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
            <span>📈</span>
            <span>Analisis Capaian Mata Pelajaran</span>
        </button>
    </div>

    <!-- TAB 1: MATRIKS LEGER ANGKATAN 3 TAHUN -->
    @if($activeTab === 'matriks')
        @if($cohortData)
            <!-- KPI CARDS SUMMARY -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="card p-4 bg-surface border border-border rounded-2xl flex items-center gap-3">
                    <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold uppercase text-text-secondary tracking-wider">Total Siswa</div>
                        <div class="text-xl sm:text-2xl font-bold font-mono text-text-main">{{ $cohortData['total_siswa'] }}</div>
                    </div>
                </div>

                <div class="card p-4 bg-surface border border-border rounded-2xl flex items-center gap-3">
                    <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold uppercase text-text-secondary tracking-wider">Rata-rata Angkatan</div>
                        <div class="text-xl sm:text-2xl font-bold font-mono text-text-main">{{ number_format($cohortData['stats']['rata_angkatan'], 2) }}</div>
                    </div>
                </div>

                <div class="card p-4 bg-surface border border-border rounded-2xl flex items-center gap-3">
                    <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold uppercase text-text-secondary tracking-wider">Tertinggi (#1 Rank)</div>
                        <div class="text-xl sm:text-2xl font-bold font-mono text-amber-600 dark:text-amber-400">
                            {{ number_format($cohortData['stats']['nilai_tertinggi'], 2) }}
                        </div>
                        <div class="text-[10px] text-text-secondary truncate max-w-[130px]" title="{{ $cohortData['stats']['siswa_tertinggi'] }}">
                            {{ $cohortData['stats']['siswa_tertinggi'] }}
                        </div>
                    </div>
                </div>

                <div class="card p-4 bg-surface border border-border rounded-2xl flex items-center gap-3">
                    <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold uppercase text-text-secondary tracking-wider">Ketuntasan (≥ 75)</div>
                        <div class="text-xl sm:text-2xl font-bold font-mono text-text-main">{{ $cohortData['stats']['persen_tuntas'] }}%</div>
                        <div class="text-[10px] text-text-secondary">{{ $cohortData['stats']['jumlah_tuntas'] }} dari {{ $cohortData['total_siswa'] }} Siswa</div>
                    </div>
                </div>
            </div>

            <!-- MATRIKS TABLE -->
            <div class="card bg-surface border border-border rounded-2xl overflow-hidden shadow-xs">
                <div class="overflow-x-auto max-h-[700px] relative">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead class="sticky top-0 z-30 bg-surface-hover/90 backdrop-blur-md border-b-2 border-border text-text-main font-bold select-none">
                            <tr>
                                <th rowspan="2" class="w-10 min-w-[40px] p-2.5 text-center border-r border-border sticky left-0 z-40 bg-surface dark:bg-surface-card">
                                    No
                                </th>
                                <th rowspan="2" class="w-24 min-w-[85px] p-2.5 text-center border-r border-border sticky left-[40px] z-40 bg-surface dark:bg-surface-card">
                                    NIS / NISN
                                </th>
                                <th rowspan="2" class="min-w-[170px] max-w-[210px] p-2.5 border-r-2 border-border sticky left-[125px] z-40 bg-surface dark:bg-surface-card shadow-[4px_0_8px_-2px_rgba(0,0,0,0.12)]">
                                    Nama Lengkap Siswa
                                </th>
                                <th rowspan="2" class="w-10 p-2.5 text-center border-r border-border">L/P</th>
                                <th rowspan="2" class="w-24 p-2.5 text-center border-r-2 border-border">Rombel</th>

                                <!-- KELAS X -->
                                <th colspan="3" class="p-2 text-center border-r-2 border-border bg-blue-50/70 dark:bg-blue-950/40 text-blue-950 dark:text-blue-200">
                                    KELAS X
                                </th>

                                <!-- KELAS XI -->
                                <th colspan="3" class="p-2 text-center border-r-2 border-border bg-indigo-50/70 dark:bg-indigo-950/40 text-indigo-950 dark:text-indigo-200">
                                    KELAS XI
                                </th>

                                <!-- KELAS XII -->
                                <th colspan="3" class="p-2 text-center border-r-2 border-border bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-950 dark:text-emerald-200">
                                    KELAS XII
                                </th>

                                <!-- TRANSKRIP AKHIR -->
                                <th colspan="4" class="p-2 text-center border-r border-border bg-amber-50/70 dark:bg-amber-950/40 text-amber-950 dark:text-amber-200">
                                    TRANSKRIP AKHIR 3 TAHUN
                                </th>

                                <th rowspan="2" class="w-24 p-2.5 text-center">Aksi</th>
                            </tr>
                            <tr>
                                <!-- Sub Header Kelas X -->
                                <th class="p-2 text-center border-r border-border bg-blue-50/40 dark:bg-blue-950/20 font-mono text-[11px]">S1</th>
                                <th class="p-2 text-center border-r border-border bg-blue-50/40 dark:bg-blue-950/20 font-mono text-[11px]">S2</th>
                                <th class="p-2 text-center border-r-2 border-border bg-blue-100/60 dark:bg-blue-900/40 font-mono text-[11px]">Rata X</th>

                                <!-- Sub Header Kelas XI -->
                                <th class="p-2 text-center border-r border-border bg-indigo-50/40 dark:bg-indigo-950/20 font-mono text-[11px]">S3</th>
                                <th class="p-2 text-center border-r border-border bg-indigo-50/40 dark:bg-indigo-950/20 font-mono text-[11px]">S4</th>
                                <th class="p-2 text-center border-r-2 border-border bg-indigo-100/60 dark:bg-indigo-900/40 font-mono text-[11px]">Rata XI</th>

                                <!-- Sub Header Kelas XII -->
                                <th class="p-2 text-center border-r border-border bg-emerald-50/40 dark:bg-emerald-950/20 font-mono text-[11px]">S5</th>
                                <th class="p-2 text-center border-r border-border bg-emerald-50/40 dark:bg-emerald-950/20 font-mono text-[11px]">S6</th>
                                <th class="p-2 text-center border-r-2 border-border bg-emerald-100/60 dark:bg-emerald-900/40 font-mono text-[11px]">Rata XII</th>

                                <!-- Sub Header Transkrip Akhir -->
                                <th class="p-2 text-center border-r border-border bg-amber-100/70 dark:bg-amber-900/40 font-mono text-[11px]">Kumulatif</th>
                                <th class="p-2 text-center border-r border-border bg-amber-50/40 dark:bg-amber-950/20 font-mono text-[11px]">Predikat</th>
                                <th class="p-2 text-center border-r border-border bg-amber-50/40 dark:bg-amber-950/20 font-mono text-[11px]">Rank</th>
                                <th class="p-2 text-center border-r border-border bg-amber-50/40 dark:bg-amber-950/20 font-mono text-[11px]">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($cohortData['students'] as $s)
                                <tr class="hover:bg-surface-hover/70 transition-colors">
                                    <!-- No Sticky -->
                                    <td class="p-2.5 text-center font-mono text-text-secondary border-r border-border sticky left-0 z-20 bg-surface dark:bg-surface-card">
                                        {{ $loop->iteration }}
                                    </td>

                                    <!-- NIS Sticky -->
                                    <td class="p-2.5 text-center font-mono text-[11px] text-text-secondary border-r border-border sticky left-[40px] z-20 bg-surface dark:bg-surface-card">
                                        <div>{{ $s['nipd'] }}</div>
                                        @if(!empty($s['nisn']) && $s['nisn'] !== '-')
                                            <div class="text-[9px] text-text-muted">{{ $s['nisn'] }}</div>
                                        @endif
                                    </td>

                                    <!-- Nama Siswa Sticky -->
                                    <td class="p-2.5 border-r-2 border-border sticky left-[125px] z-20 bg-surface dark:bg-surface-card shadow-[4px_0_8px_-2px_rgba(0,0,0,0.12)] whitespace-nowrap">
                                        <div class="font-semibold text-text-main hover:text-primary cursor-pointer truncate"
                                             wire:click="selectSiswa({{ $s['siswa_id'] }})"
                                             title="{{ $s['nama'] }}">
                                            {{ $s['nama'] }}
                                        </div>
                                    </td>

                                    <!-- L/P -->
                                    <td class="p-2.5 text-center text-text-secondary border-r border-border font-medium">{{ $s['jk'] }}</td>

                                    <!-- Rombel -->
                                    <td class="p-2.5 text-center text-text-secondary border-r-2 border-border text-[11px] whitespace-nowrap">
                                        <div class="font-medium text-text-main">{{ $s['rombel_nama'] }}</div>
                                        @if(!empty($s['tahun_ajaran']) && $s['tahun_ajaran'] !== '-')
                                            <div class="text-[9px] text-text-muted mt-0.5">
                                                TP {{ $s['tahun_ajaran'] }}
                                                @if(!empty($s['ta_is_active']))
                                                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold">(Aktif)</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Nilai Kelas X -->
                                    <td class="p-2 text-center font-mono border-r border-border {{ $s['s1'] && $s['s1'] < 75 ? 'text-rose-600 font-bold bg-rose-50/30' : '' }}">
                                        {{ $s['s1'] !== null ? number_format($s['s1'], 0) : '-' }}
                                    </td>
                                    <td class="p-2 text-center font-mono border-r border-border {{ $s['s2'] && $s['s2'] < 75 ? 'text-rose-600 font-bold bg-rose-50/30' : '' }}">
                                        {{ $s['s2'] !== null ? number_format($s['s2'], 0) : '-' }}
                                    </td>
                                    <td class="p-2 text-center font-mono font-semibold border-r-2 border-border bg-blue-50/30 dark:bg-blue-950/20 text-blue-900 dark:text-blue-200">
                                        {{ $s['rata_x'] !== null ? number_format($s['rata_x'], 1) : '-' }}
                                    </td>

                                    <!-- Nilai Kelas XI -->
                                    <td class="p-2 text-center font-mono border-r border-border {{ $s['s3'] && $s['s3'] < 75 ? 'text-rose-600 font-bold bg-rose-50/30' : '' }}">
                                        {{ $s['s3'] !== null ? number_format($s['s3'], 0) : '-' }}
                                    </td>
                                    <td class="p-2 text-center font-mono border-r border-border {{ $s['s4'] && $s['s4'] < 75 ? 'text-rose-600 font-bold bg-rose-50/30' : '' }}">
                                        {{ $s['s4'] !== null ? number_format($s['s4'], 0) : '-' }}
                                    </td>
                                    <td class="p-2 text-center font-mono font-semibold border-r-2 border-border bg-indigo-50/30 dark:bg-indigo-950/20 text-indigo-900 dark:text-indigo-200">
                                        {{ $s['rata_xi'] !== null ? number_format($s['rata_xi'], 1) : '-' }}
                                    </td>

                                    <!-- Nilai Kelas XII -->
                                    <td class="p-2 text-center font-mono border-r border-border {{ $s['s5'] && $s['s5'] < 75 ? 'text-rose-600 font-bold bg-rose-50/30' : '' }}">
                                        {{ $s['s5'] !== null ? number_format($s['s5'], 0) : '-' }}
                                    </td>
                                    <td class="p-2 text-center font-mono border-r border-border {{ $s['s6'] && $s['s6'] < 75 ? 'text-rose-600 font-bold bg-rose-50/30' : '' }}">
                                        {{ $s['s6'] !== null ? number_format($s['s6'], 0) : '-' }}
                                    </td>
                                    <td class="p-2 text-center font-mono font-semibold border-r-2 border-border bg-emerald-50/30 dark:bg-emerald-950/20 text-emerald-900 dark:text-emerald-200">
                                        {{ $s['rata_xii'] !== null ? number_format($s['rata_xii'], 1) : '-' }}
                                    </td>

                                    <!-- Kumulatif Akhir -->
                                    <td class="p-2.5 text-center font-mono font-bold text-sm border-r border-border bg-amber-50/50 dark:bg-amber-950/30 text-amber-950 dark:text-amber-200">
                                        {{ $s['nilai_kumulatif'] > 0 ? number_format($s['nilai_kumulatif'], 2) : '-' }}
                                    </td>
                                    <td class="p-2.5 text-center font-bold border-r border-border">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $s['predikat'] === 'A' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' : ($s['predikat'] === 'B' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-200' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-200') }}">
                                            {{ $s['predikat'] }}
                                        </span>
                                    </td>
                                    <td class="p-2.5 text-center font-mono font-bold border-r border-border">
                                        @if($s['ranking'] <= 3)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-400 text-amber-950 font-extrabold text-[11px] shadow-xs">
                                                {{ $s['ranking'] }}
                                            </span>
                                        @else
                                            <span class="text-text-secondary">{{ $s['ranking'] }}</span>
                                        @endif
                                    </td>
                                    <td class="p-2.5 text-center border-r border-border">
                                        @if($s['is_tuntas'])
                                            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">✓ Tuntas</span>
                                        @else
                                            <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400">✗ Remedial</span>
                                        @endif
                                    </td>

                                    <!-- Aksi -->
                                    <td class="p-2.5 text-center whitespace-nowrap">
                                        <button type="button"
                                                wire:click="selectSiswa({{ $s['siswa_id'] }})"
                                                class="btn btn-outline text-[11px] py-1 px-2.5 text-primary hover:bg-primary/10 cursor-pointer">
                                            <span>Detail Transkrip</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="19" class="p-8 text-center text-text-muted italic">
                                        Tidak ada data siswa ditemukan untuk kriteria jurusan dan tingkat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif

    <!-- TAB 2: TRANSKRIP INDIVIDUAL SISWA -->
    @if($activeTab === 'individual')
        <div class="space-y-5">
            <!-- Siswa Selector Bar -->
            <div class="card p-4 bg-surface border border-border rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-text-secondary">Pilih Siswa:</span>
                    <select wire:model.live="selectedSiswaId"
                            class="text-xs sm:text-sm rounded-xl border border-border bg-surface px-3 py-1.5 text-text-main focus:outline-none focus:ring-2 focus:ring-primary min-w-[240px]">
                        @foreach($siswasList as $sw)
                            <option value="{{ $sw->id }}">{{ $sw->nama }} ({{ $sw->nipd }}) &mdash; {{ $sw->rombel?->nama }} [TP {{ $sw->rombel?->tahunAjaran?->nama ?? '-' }}{{ $sw->rombel?->tahunAjaran?->is_active ? ' - Aktif' : '' }}]</option>
                        @endforeach
                    </select>
                </div>

                @if($selectedSiswaId)
                    <div class="flex items-center gap-2">
                        <a href="{{ route('penilaian.transkrip.pdf', ['siswa_id' => $selectedSiswaId]) }}"
                           target="_blank"
                           class="btn btn-primary text-xs py-1.5 px-3 font-semibold flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Cetak PDF Transkrip Siswa</span>
                        </a>
                    </div>
                @endif
            </div>

            @if($individualData)
                <!-- Identity Card -->
                <div class="card p-5 bg-surface border border-border rounded-2xl shadow-xs">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-border pb-4 mb-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-bold text-lg">
                                {{ substr($individualData['siswa']['nama'], 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-text-main">{{ $individualData['siswa']['nama'] }}</h3>
                                <p class="text-xs text-text-secondary mt-0.5">
                                    NIS: <span class="font-mono font-semibold">{{ $individualData['siswa']['nipd'] }}</span> &bull; 
                                    NISN: <span class="font-mono font-semibold">{{ $individualData['siswa']['nisn'] }}</span> &bull; 
                                    Rombel: <span class="font-semibold">{{ $individualData['siswa']['rombel'] }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Mini Summary Pill -->
                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                <div class="text-[10px] uppercase tracking-wider text-text-secondary font-bold">Nilai Kumulatif</div>
                                <div class="text-2xl font-bold font-mono text-primary">{{ number_format($individualData['summary']['nilai_kumulatif'], 2) }}</div>
                            </div>
                            <div class="px-3 py-1.5 rounded-xl font-bold text-base {{ $individualData['summary']['predikat'] === 'A' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-200' }}">
                                {{ $individualData['summary']['predikat'] }}
                            </div>
                        </div>
                    </div>

                    <!-- Metadata Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div>
                            <span class="text-text-secondary block">Program Keahlian:</span>
                            <span class="font-semibold text-text-main">{{ $individualData['siswa']['jurusan_nama'] }}</span>
                        </div>
                        <div>
                            <span class="text-text-secondary block">Konsentrasi Keahlian:</span>
                            <span class="font-semibold text-text-main">{{ $individualData['siswa']['jurusan_kode'] }}</span>
                        </div>
                        <div>
                            <span class="text-text-secondary block">Tempat, Tgl Lahir:</span>
                            <span class="font-semibold text-text-main">{{ $individualData['siswa']['tempat_lahir'] }}, {{ $individualData['siswa']['tanggal_lahir'] }}</span>
                        </div>
                        <div>
                            <span class="text-text-secondary block">Wali Kelas:</span>
                            <span class="font-semibold text-text-main">{{ $individualData['siswa']['wali_kelas'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- 6-SEMESTER SUBJECT TABLE -->
                <div class="card bg-surface border border-border rounded-2xl overflow-hidden shadow-xs">
                    <div class="p-4 border-b border-border bg-surface-hover/30 flex items-center justify-between">
                        <h4 class="text-sm font-bold text-text-main flex items-center gap-2">
                            <span>📋 Transkrip Nilai Akademik 6 Semester</span>
                        </h4>
                        <span class="text-xs text-text-secondary">
                            Total: <strong class="text-text-main">{{ $individualData['summary']['total_mapel'] }}</strong> Mata Pelajaran
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left border-collapse">
                            <thead>
                                <tr class="bg-surface-hover/80 text-text-main font-bold border-b border-border">
                                    <th rowspan="2" class="w-10 p-2.5 text-center border-r border-border">No</th>
                                    <th rowspan="2" class="p-2.5 border-r border-border">Mata Pelajaran</th>
                                    <th rowspan="2" class="w-12 p-2.5 text-center border-r-2 border-border">KKM</th>
                                    <th colspan="2" class="p-2 text-center border-r-2 border-border bg-blue-50/60 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200">KELAS X</th>
                                    <th colspan="2" class="p-2 text-center border-r-2 border-border bg-indigo-50/60 dark:bg-indigo-950/40 text-indigo-900 dark:text-indigo-200">KELAS XI</th>
                                    <th colspan="2" class="p-2 text-center border-r-2 border-border bg-emerald-50/60 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200">KELAS XII</th>
                                    <th rowspan="2" class="w-20 p-2.5 text-center border-r border-border bg-amber-50/60 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200">Nilai Akhir</th>
                                    <th rowspan="2" class="w-16 p-2.5 text-center border-r border-border bg-amber-50/60 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200">Huruf</th>
                                    <th rowspan="2" class="w-24 p-2.5 text-center">Status</th>
                                </tr>
                                <tr class="bg-surface-hover/80 text-text-main font-bold border-b-2 border-border font-mono text-[11px]">
                                    <th class="p-1.5 text-center border-r border-border bg-blue-50/30 dark:bg-blue-950/20">Sem 1</th>
                                    <th class="p-1.5 text-center border-r-2 border-border bg-blue-50/30 dark:bg-blue-950/20">Sem 2</th>
                                    <th class="p-1.5 text-center border-r border-border bg-indigo-50/30 dark:bg-indigo-950/20">Sem 3</th>
                                    <th class="p-1.5 text-center border-r-2 border-border bg-indigo-50/30 dark:bg-indigo-950/20">Sem 4</th>
                                    <th class="p-1.5 text-center border-r border-border bg-emerald-50/30 dark:bg-emerald-950/20">Sem 5</th>
                                    <th class="p-1.5 text-center border-r-2 border-border bg-emerald-50/30 dark:bg-emerald-950/20">Sem 6</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @php $globalNum = 1; @endphp
                                @foreach($individualData['grouped_mapels'] as $grp)
                                    <!-- Kelompok Header -->
                                    <tr class="bg-surface-hover/50 font-bold">
                                        <td colspan="12" class="p-2 text-xs text-text-main tracking-wider uppercase">
                                            Kelompok {{ $grp['kelompok_kode'] }}. {{ $grp['kelompok_nama'] }}
                                        </td>
                                    </tr>

                                    @foreach($grp['items'] as $item)
                                        <tr class="hover:bg-surface-hover/60 transition-colors">
                                            <td class="p-2 text-center font-mono text-text-secondary border-r border-border">{{ $globalNum++ }}</td>
                                            <td class="p-2 font-medium text-text-main border-r border-border">{{ $item['nama'] }}</td>
                                            <td class="p-2 text-center font-mono text-text-secondary border-r-2 border-border">{{ number_format($item['kkm'], 0) }}</td>

                                            <!-- S1 & S2 -->
                                            <td class="p-2 text-center font-mono border-r border-border {{ $item['semesters'][1] && $item['semesters'][1] < $item['kkm'] ? 'text-rose-600 font-bold bg-rose-50/40' : '' }}">
                                                {{ $item['semesters'][1] !== null ? number_format($item['semesters'][1], 0) : '-' }}
                                            </td>
                                            <td class="p-2 text-center font-mono border-r-2 border-border {{ $item['semesters'][2] && $item['semesters'][2] < $item['kkm'] ? 'text-rose-600 font-bold bg-rose-50/40' : '' }}">
                                                {{ $item['semesters'][2] !== null ? number_format($item['semesters'][2], 0) : '-' }}
                                            </td>

                                            <!-- S3 & S4 -->
                                            <td class="p-2 text-center font-mono border-r border-border {{ $item['semesters'][3] && $item['semesters'][3] < $item['kkm'] ? 'text-rose-600 font-bold bg-rose-50/40' : '' }}">
                                                {{ $item['semesters'][3] !== null ? number_format($item['semesters'][3], 0) : '-' }}
                                            </td>
                                            <td class="p-2 text-center font-mono border-r-2 border-border {{ $item['semesters'][4] && $item['semesters'][4] < $item['kkm'] ? 'text-rose-600 font-bold bg-rose-50/40' : '' }}">
                                                {{ $item['semesters'][4] !== null ? number_format($item['semesters'][4], 0) : '-' }}
                                            </td>

                                            <!-- S5 & S6 -->
                                            <td class="p-2 text-center font-mono border-r border-border {{ $item['semesters'][5] && $item['semesters'][5] < $item['kkm'] ? 'text-rose-600 font-bold bg-rose-50/40' : '' }}">
                                                {{ $item['semesters'][5] !== null ? number_format($item['semesters'][5], 0) : '-' }}
                                            </td>
                                            <td class="p-2 text-center font-mono border-r-2 border-border {{ $item['semesters'][6] && $item['semesters'][6] < $item['kkm'] ? 'text-rose-600 font-bold bg-rose-50/40' : '' }}">
                                                {{ $item['semesters'][6] !== null ? number_format($item['semesters'][6], 0) : '-' }}
                                            </td>

                                            <!-- Nilai Akhir Mapel -->
                                            <td class="p-2 text-center font-mono font-bold text-sm border-r border-border bg-amber-50/30 dark:bg-amber-950/20 text-amber-950 dark:text-amber-200">
                                                {{ $item['rata_mapel'] !== null ? number_format($item['rata_mapel'], 2) : '-' }}
                                            </td>
                                            <td class="p-2 text-center font-bold border-r border-border">
                                                {{ $item['predikat'] }}
                                            </td>
                                            <td class="p-2 text-center">
                                                @if($item['is_tuntas'])
                                                    <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">Tuntas</span>
                                                @else
                                                    <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400">Remedial</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                            <tfoot class="border-t-2 border-border bg-surface-hover/70 font-bold">
                                <tr>
                                    <td colspan="3" class="p-2.5 text-center border-r border-border">RATA-RATA SEMESTER</td>
                                    @for($s = 1; $s <= 6; $s++)
                                        <td class="p-2 text-center font-mono border-r border-border">
                                            {{ $individualData['semester_averages'][$s] !== null ? number_format($individualData['semester_averages'][$s], 1) : '-' }}
                                        </td>
                                    @endfor
                                    <td class="p-2 text-center font-mono font-bold text-sm bg-amber-100/50 dark:bg-amber-950/40" colspan="3">
                                        {{ number_format($individualData['summary']['nilai_kumulatif'], 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @else
                <div class="card p-8 text-center bg-surface border border-border rounded-2xl text-text-muted italic">
                    Silakan pilih siswa untuk melihat lembar transkrip nilai individual.
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 3: ANALISIS CAPAIAN MATA PELAJARAN -->
    @if($activeTab === 'analisis')
        @if($mapelComparison)
            <div class="card bg-surface border border-border rounded-2xl overflow-hidden shadow-xs">
                <div class="p-4 border-b border-border bg-surface-hover/40 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-text-main">
                            Analisis Rata-Rata Capaian Mata Pelajaran (Angkatan 3 Tahun)
                        </h4>
                        <p class="text-xs text-text-secondary mt-0.5">
                            Menampilkan perkembangan rata-rata nilai seluruh siswa per mata pelajaran dari Semester 1 s.d. 6
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-hover/80 text-text-main font-bold border-b border-border">
                                <th rowspan="2" class="w-10 p-2.5 text-center border-r border-border">No</th>
                                <th rowspan="2" class="p-2.5 border-r border-border">Mata Pelajaran</th>
                                <th rowspan="2" class="p-2.5 border-r border-border">Kelompok</th>
                                <th rowspan="2" class="w-12 p-2.5 text-center border-r-2 border-border">KKM</th>
                                <th colspan="2" class="p-2 text-center border-r-2 border-border bg-blue-50/60 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200">KELAS X</th>
                                <th colspan="2" class="p-2 text-center border-r-2 border-border bg-indigo-50/60 dark:bg-indigo-950/40 text-indigo-900 dark:text-indigo-200">KELAS XI</th>
                                <th colspan="2" class="p-2 text-center border-r-2 border-border bg-emerald-50/60 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200">KELAS XII</th>
                                <th rowspan="2" class="w-24 p-2.5 text-center bg-amber-50/60 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200">Rata-rata 3 Thn</th>
                            </tr>
                            <tr class="bg-surface-hover/80 text-text-main font-bold border-b-2 border-border font-mono text-[11px]">
                                <th class="p-1.5 text-center border-r border-border bg-blue-50/30 dark:bg-blue-950/20">Sem 1</th>
                                <th class="p-1.5 text-center border-r-2 border-border bg-blue-50/30 dark:bg-blue-950/20">Sem 2</th>
                                <th class="p-1.5 text-center border-r border-border bg-indigo-50/30 dark:bg-indigo-950/20">Sem 3</th>
                                <th class="p-1.5 text-center border-r-2 border-border bg-indigo-50/30 dark:bg-indigo-950/20">Sem 4</th>
                                <th class="p-1.5 text-center border-r border-border bg-emerald-50/30 dark:bg-emerald-950/20">Sem 5</th>
                                <th class="p-1.5 text-center border-r-2 border-border bg-emerald-50/30 dark:bg-emerald-950/20">Sem 6</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($mapelComparison['items'] as $m)
                                <tr class="hover:bg-surface-hover/60 transition-colors">
                                    <td class="p-2 text-center font-mono text-text-secondary border-r border-border">{{ $loop->iteration }}</td>
                                    <td class="p-2 font-medium text-text-main border-r border-border">
                                        {{ $m['nama'] }}
                                        <span class="text-[10px] text-text-muted font-mono block">[{{ $m['kode'] }}]</span>
                                    </td>
                                    <td class="p-2 text-text-secondary border-r border-border">{{ $m['kelompok'] }}</td>
                                    <td class="p-2 text-center font-mono border-r-2 border-border">{{ number_format($m['kkm'], 0) }}</td>

                                    <!-- S1 s/d S6 -->
                                    <td class="p-2 text-center font-mono border-r border-border">{{ $m['semesters'][1] !== null ? number_format($m['semesters'][1], 1) : '-' }}</td>
                                    <td class="p-2 text-center font-mono border-r-2 border-border">{{ $m['semesters'][2] !== null ? number_format($m['semesters'][2], 1) : '-' }}</td>
                                    <td class="p-2 text-center font-mono border-r border-border">{{ $m['semesters'][3] !== null ? number_format($m['semesters'][3], 1) : '-' }}</td>
                                    <td class="p-2 text-center font-mono border-r-2 border-border">{{ $m['semesters'][4] !== null ? number_format($m['semesters'][4], 1) : '-' }}</td>
                                    <td class="p-2 text-center font-mono border-r border-border">{{ $m['semesters'][5] !== null ? number_format($m['semesters'][5], 1) : '-' }}</td>
                                    <td class="p-2 text-center font-mono border-r-2 border-border">{{ $m['semesters'][6] !== null ? number_format($m['semesters'][6], 1) : '-' }}</td>

                                    <!-- Overall -->
                                    <td class="p-2 text-center font-mono font-bold text-sm bg-amber-50/40 dark:bg-amber-950/20 text-amber-950 dark:text-amber-200">
                                        {{ $m['rata_overall'] !== null ? number_format($m['rata_overall'], 2) : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="p-8 text-center text-text-muted italic">
                                        Belum ada data mata pelajaran untuk dianalisis.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif
</div>
