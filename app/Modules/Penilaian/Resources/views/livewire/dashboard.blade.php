<div class="space-y-4">
    <!-- Header Page Minimalis -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-text-main">Dashboard Pengolahan Nilai</h1>
            <p class="text-xs text-text-secondary">Pantau progres pengisian nilai dan input Tujuan Pembelajaran (TP) per Rombel.</p>
        </div>
        @if($currentSem)
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200 dark:border-indigo-800 text-primary dark:text-indigo-400 text-xs font-semibold self-start sm:self-auto shadow-2xs">
                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                <span>Semester {{ $currentSem->nama }} (T.A. {{ $currentSem->tahunAjaran?->nama ?? $currentSem->tahunAjaran?->tahun }})</span>
            </div>
        @endif
    </div>

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

    <!-- Baris Filter & Pencarian: 1 Baris Dibagi 3 Kolom -->
    <div class="card p-3">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center">
            <!-- Kolom 1: Pilih Tingkat Kelas (Dropdown) -->
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

            <!-- Kolom 2: Pilih Rombel (Dropdown) -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">
                    Rombongan Belajar
                </label>
                <select wire:model.live="selectedRombelId" class="w-full text-xs font-semibold rounded-lg border-border bg-surface text-text-main py-2 px-2.5 focus:ring-primary focus:border-primary">
                    @forelse($availableRombels as $rb)
                        <option value="{{ $rb->id }}">
                            {{ $rb->nama }} ({{ $rb->tingkat }} - {{ $rb->jurusan?->kode ?? 'Umum' }})
                        </option>
                    @empty
                        <option value="">-- Tidak ada rombel --</option>
                    @endforelse
                </select>
            </div>

            <!-- Kolom 3: Cari Mapel / Guru -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">
                    Cari Mapel / Guru
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-text-secondary">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik nama mapel atau guru..." class="w-full text-xs rounded-lg border-border bg-surface text-text-main pl-8 pr-7 py-2 focus:ring-primary focus:border-primary">
                    @if($search)
                        <button type="button" wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-text-secondary hover:text-text-main cursor-pointer text-xs font-bold">
                            &times;
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($selectedRombel)
        <!-- Card Statistik Ringkasan: 1 Baris Ukuran Kecil -->
        <div class="card p-3 flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
            <div class="flex flex-wrap items-center gap-x-5 gap-y-1.5">
                <div class="flex items-center gap-1.5">
                    <span class="text-text-secondary">Total Mapel:</span>
                    <span class="font-bold text-text-main font-mono text-xs sm:text-sm">{{ $summary['totalMapel'] }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-emerald-700 dark:text-emerald-400 font-medium">Lengkap (100%):</span>
                    <span class="font-bold text-emerald-700 dark:text-emerald-300 font-mono">{{ $summary['lengkap'] }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span class="text-amber-700 dark:text-amber-400 font-medium">Dalam Proses:</span>
                    <span class="font-bold text-amber-700 dark:text-amber-300 font-mono">{{ $summary['proses'] }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span class="text-rose-700 dark:text-rose-400 font-medium">Belum Diisi:</span>
                    <span class="font-bold text-rose-700 dark:text-rose-300 font-mono">{{ $summary['belum'] }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2.5 shrink-0 pt-1 md:pt-0 border-t md:border-t-0 border-border">
                <span class="text-text-secondary whitespace-nowrap">Keterisian:</span>
                <div class="w-24 sm:w-32 bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full transition-all duration-300 {{ $summary['persenRombel'] == 100 ? 'bg-emerald-500' : ($summary['persenRombel'] > 40 ? 'bg-indigo-600' : 'bg-amber-500') }}" style="width: {{ $summary['persenRombel'] }}%"></div>
                </div>
                <span class="font-bold font-mono text-text-main whitespace-nowrap">{{ $summary['persenRombel'] }}%</span>
                <span class="text-[11px] text-text-secondary">({{ $totalSiswaRombel }} Siswa)</span>
            </div>
        </div>

        <!-- Tabel Struktur Mata Pelajaran Minimalis -->
        <div class="card p-0 overflow-hidden shadow-2xs">
            <div class="px-4 py-2.5 border-b border-border flex items-center justify-between bg-surface-hover/30">
                <div class="flex items-center gap-2 text-xs">
                    <span class="font-bold text-text-main">{{ $selectedRombel->nama }}</span>
                    <span class="text-text-secondary">&bull;</span>
                    <span class="text-text-secondary">{{ $selectedRombel->jurusan?->nama ?? '-' }}</span>
                    <span class="text-text-secondary">&bull;</span>
                    <span class="text-text-secondary">Wali: {{ $selectedRombel->waliKelas?->name ?? '-' }}</span>
                </div>
                @if($search)
                    <span class="text-[11px] text-text-secondary">
                        Cari: "<b>{{ $search }}</b>"
                    </span>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-xs">
                    <thead class="bg-surface-hover/60 text-text-secondary uppercase font-semibold tracking-wider text-[11px]">
                        <tr>
                            <th class="px-3 py-2 w-10 text-center">No</th>
                            <th class="px-4 py-2">Mata Pelajaran</th>
                            <th class="px-4 py-2">Guru</th>
                            <th class="px-3 py-2 text-center w-16">JP</th>
                            <th class="px-4 py-2 min-w-[180px]">Progres</th>
                            <th class="px-3 py-2 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-text-main">
                        @php $globalIndex = 1; @endphp
                        @forelse($groupedStrukturKurikulum as $kelompokLabel => $mapelList)
                            <!-- Header Baris Kelompok Kurikulum -->
                            <tr class="bg-slate-100/70 dark:bg-slate-800/50">
                                <td colspan="6" class="px-4 py-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                                        <span class="text-[11px] font-bold uppercase tracking-wider text-text-main">
                                            {{ $kelompokLabel }}
                                        </span>
                                        <span class="text-[10px] text-text-secondary font-normal">
                                            ({{ count($mapelList) }} Mapel)
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            <!-- Baris Mata Pelajaran -->
                            @foreach($mapelList as $item)
                                <tr class="hover:bg-surface-hover/30 transition">
                                    <!-- No -->
                                    <td class="px-3 py-2 text-center font-mono text-text-secondary">
                                        {{ $globalIndex++ }}
                                    </td>

                                    <!-- Mata Pelajaran (Tanpa Singkatan) -->
                                    <td class="px-4 py-2 font-medium text-text-main">
                                        {{ $item->mapel_nama }}
                                    </td>

                                    <!-- Guru (Tanpa Badge Inisial) -->
                                    <td class="px-4 py-2">
                                        @if($item->guru)
                                            <span class="text-text-main font-medium">
                                                {{ $item->guru }}
                                            </span>
                                        @else
                                            <span class="text-amber-600 dark:text-amber-400 italic">
                                                Belum Ditugaskan
                                            </span>
                                        @endif
                                    </td>

                                    <!-- JP -->
                                    <td class="px-3 py-2 text-center text-text-secondary font-mono">
                                        {{ $item->jp }}
                                    </td>

                                    <!-- Progres Penilaian -->
                                    <td class="px-4 py-2">
                                        <div class="space-y-1">
                                            <div class="flex items-center justify-between text-[11px]">
                                                <span class="{{ $item->status === 'lengkap' ? 'text-emerald-700 dark:text-emerald-400 font-semibold' : ($item->status === 'sebagian' ? 'text-indigo-700 dark:text-indigo-400' : ($item->status === 'proses_tp' ? 'text-amber-700 dark:text-amber-400' : 'text-slate-400')) }}">
                                                    @if($item->status === 'lengkap')
                                                        ✓ Lengkap
                                                    @elseif($item->status === 'sebagian')
                                                        {{ $item->nilai_count }}/{{ $totalSiswaRombel }} Siswa
                                                    @elseif($item->status === 'proses_tp')
                                                        {{ $item->tp_count }} TP
                                                    @else
                                                        Belum Diisi
                                                    @endif
                                                </span>
                                                <span class="font-mono text-text-secondary text-[10px]">
                                                    {{ $item->persen }}%
                                                </span>
                                            </div>
                                            <!-- Mini Progress Bar -->
                                            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                                <div class="h-1.5 rounded-full transition-all duration-300 {{ $item->status === 'lengkap' ? 'bg-emerald-500' : ($item->status === 'sebagian' ? 'bg-indigo-600' : ($item->status === 'proses_tp' ? 'bg-amber-500' : 'bg-slate-300 dark:bg-slate-600')) }}" style="width: {{ $item->persen }}%"></div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Aksi: Ganti "Input Nilai" menjadi "Input" -->
                                    <td class="px-3 py-2 text-center">
                                        @if($item->penugasan_id)
                                            <a href="{{ route('penilaian.input', $item->penugasan_id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-white bg-primary hover:bg-primary-hover rounded-md shadow-2xs transition cursor-pointer">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Input</span>
                                            </a>
                                        @else
                                            <button type="button" wire:click="bukaPenilaian({{ $item->mapel_id }})" wire:loading.attr="disabled" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/50 dark:text-indigo-300 rounded-md border border-indigo-200 dark:border-indigo-800 transition cursor-pointer disabled:opacity-50">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                <span>Input</span>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-text-secondary">
                                    <div class="flex flex-col items-center justify-center space-y-1.5">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        @if($search)
                                            <p class="text-xs font-medium">Tidak ada mapel atau guru yang cocok dengan "{{ $search }}".</p>
                                            <button type="button" wire:click="$set('search', '')" class="text-xs text-primary font-bold hover:underline">Reset Pencarian</button>
                                        @else
                                            <p class="text-xs font-medium">Struktur Kurikulum belum dikonfigurasi untuk rombel ini pada semester aktif.</p>
                                            @if(auth()->user()->isSuperAdmin())
                                                <a href="{{ route('akademik.kurikulum') }}" class="text-xs text-primary font-bold hover:underline">&rarr; Atur Struktur Kurikulum</a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- State jika tidak ada rombel -->
        <div class="card p-8 text-center text-text-secondary">
            <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <h3 class="text-sm font-bold text-text-main mb-1">Belum Ada Rombongan Belajar</h3>
            <p class="text-xs text-text-secondary">Silakan tambahkan data rombel di modul Akademik untuk mulai mengelola pengolahan nilai.</p>
        </div>
    @endif
</div>
