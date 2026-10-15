<div class="space-y-4">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-text-main">Penilaian Mapel Pilihan (Mapil)</h1>
            <p class="text-xs text-text-secondary">Kelola Tujuan Pembelajaran (TP), nilai asesmen formatif (AH), sumatif (STS/SAS), serta rekap kelulusan Mapil.</p>
        </div>
        @if($currentSem)
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 text-xs font-semibold self-start sm:self-auto shadow-2xs">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Semester {{ $currentSem->nama }} (T.A. {{ $currentSem->tahunAjaran?->nama ?? $currentSem->tahunAjaran?->tahun }})</span>
            </div>
        @endif
    </div>

    @if (session('status'))
        <div class="p-3 text-xs text-emerald-800 bg-emerald-50 rounded-lg border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="card p-3.5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">
                    Filter Tingkat
                </label>
                <select wire:model.live="filterTingkat" class="w-full text-xs font-semibold rounded-lg border-border bg-surface text-text-main py-2 px-2.5 focus:ring-primary focus:border-primary">
                    <option value="semua">Semua Tingkat</option>
                    <option value="X">Kelas X</option>
                    <option value="XI">Kelas XI</option>
                    <option value="XII">Kelas XII</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">
                    Cari Mapel Pilihan
                </label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik nama atau kode mapil..." class="w-full text-xs rounded-lg border-border bg-surface text-text-main py-2 pl-8 pr-3 focus:ring-primary focus:border-primary">
                    <svg class="w-4 h-4 absolute left-2.5 top-2.5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Kartu Mapil -->
    @if($mapils->isEmpty())
        <div class="card p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-text-secondary opacity-40 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <p class="text-sm font-semibold text-text-main">Tidak ada data Mata Pelajaran Pilihan yang ditemukan.</p>
            <p class="text-xs text-text-secondary mt-1">Pastikan Mapil sudah dibuat di Master Akademik dan Anda terdaftar sebagai guru pengampu/pendamping.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($mapils as $mapil)
                @php
                    $stats = $mapilStats[$mapil->id] ?? [
                        'total_siswa' => 0,
                        'total_tp' => 0,
                        'persen_formatif' => 0,
                        'persen_sumatif' => 0,
                        'rekap_tuntas' => 0,
                    ];
                @endphp
                <div class="card p-4 flex flex-col justify-between hover:border-primary/50 transition-all duration-200">
                    <div>
                        <!-- Header Card -->
                        <div class="flex items-start justify-between gap-2 mb-2.5">
                            <div>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-surface-variant text-text-secondary border border-border">
                                        {{ $mapil->kode }}
                                    </span>
                                    @foreach((array) $mapil->tingkats as $tingkat)
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-primary/10 text-primary">
                                            Kelas {{ $tingkat }}
                                        </span>
                                    @endforeach
                                </div>
                                <h3 class="text-sm font-bold text-text-main mt-1.5 line-clamp-1" title="{{ $mapil->nama }}">
                                    {{ $mapil->nama }}
                                </h3>
                            </div>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 shrink-0">
                                KKM {{ $mapil->kkm_default ?? 75 }}
                            </span>
                        </div>

                        <!-- Guru Pengampu -->
                        <div class="text-[11px] text-text-secondary space-y-0.5 mb-3.5 pb-2.5 border-b border-border">
                            <div class="flex items-center gap-1.5">
                                <span class="font-medium text-text-main">Pengampu:</span>
                                <span class="truncate">{{ $mapil->guru?->nama_lengkap ?? 'Belum ditentukan' }}</span>
                            </div>
                            @if($mapil->guruPendamping)
                                <div class="flex items-center gap-1.5">
                                    <span class="font-medium text-text-main">Pendamping:</span>
                                    <span class="truncate">{{ $mapil->guruPendamping->nama_lengkap }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Ringkasan Statistik -->
                        <div class="grid grid-cols-2 gap-2 mb-3.5 text-center">
                            <div class="p-2 rounded-lg bg-surface-variant border border-border">
                                <span class="block text-[10px] font-medium text-text-secondary">Siswa Anggota</span>
                                <span class="text-sm font-bold text-text-main">{{ $stats['total_siswa'] }}</span>
                            </div>
                            <div class="p-2 rounded-lg bg-surface-variant border border-border">
                                <span class="block text-[10px] font-medium text-text-secondary">Tujuan Belajar (TP)</span>
                                <span class="text-sm font-bold text-text-main">{{ $stats['total_tp'] }}</span>
                            </div>
                        </div>

                        <!-- Progres Pengisian Nilai -->
                        <div class="space-y-2 mb-4 text-[11px]">
                            <div>
                                <div class="flex justify-between items-center text-text-secondary mb-1">
                                    <span>Formatif (AH 1..AH 8)</span>
                                    <span class="font-semibold text-text-main">{{ $stats['persen_formatif'] }}%</span>
                                </div>
                                <div class="w-full h-1.5 bg-surface-variant rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-500 rounded-full transition-all duration-300" style="width: {{ $stats['persen_formatif'] }}%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between items-center text-text-secondary mb-1">
                                    <span>Sumatif (STS/SAS/SAT)</span>
                                    <span class="font-semibold text-text-main">{{ $stats['persen_sumatif'] }}%</span>
                                </div>
                                <div class="w-full h-1.5 bg-surface-variant rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-300" style="width: {{ $stats['persen_sumatif'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center gap-2 pt-2 border-t border-border">
                        <a href="{{ route('penilaian.mapil.input', $mapil->id) }}" class="flex-1 text-center py-2 px-3 text-xs font-semibold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-2xs">
                            Input Nilai & TP
                        </a>
                        <a href="{{ route('penilaian.mapil.input', ['id' => $mapil->id, 'tab' => 'rekap']) }}" class="py-2 px-3 text-xs font-semibold rounded-lg border border-border bg-surface text-text-main hover:bg-surface-variant transition">
                            Rekap
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
