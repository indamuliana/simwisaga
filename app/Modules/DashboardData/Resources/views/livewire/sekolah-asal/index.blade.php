<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-text-main flex items-center gap-2">
                <span>Data Sekolah Asal</span>
            </h1>
            <p class="text-sm text-text-secondary">Rekapitulasi & analisis feeder siswa berdasarkan sekolah asal</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" wire:click="openTopModal" class="btn btn-primary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                <span>Lihat Peringkat Sekolah</span>
            </button>
        </div>
    </div>

    {{-- Metric Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-text-muted">Total Sekolah</span>
                <span class="p-2 rounded-lg bg-primary-soft text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-text-main">{{ number_format($totalSekolah) }}</span>
                <span class="text-xs text-text-muted">sekolah unik</span>
            </div>
            <p class="mt-1 text-xs text-text-secondary">Sekolah pengirim terdata</p>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-text-muted">Total Siswa Terdata</span>
                <span class="p-2 rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-text-main">{{ number_format($totalSiswa) }}</span>
                <span class="text-xs text-text-muted">siswa</span>
            </div>
            <p class="mt-1 text-xs text-text-secondary">Memiliki informasi sekolah asal</p>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-text-muted">Rata-rata per Sekolah</span>
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
            <p class="mt-1 text-xs text-text-secondary">Rasio sebaran siswa</p>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-text-muted">Kontributor Terbanyak</span>
                <span class="p-2 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </span>
            </div>
            <div class="mt-2">
                <span class="text-xl font-bold text-text-main block truncate" title="{{ $topSekolah->sekolah_asal ?? '-' }}">
                    {{ $topSekolah ? Str::limit($topSekolah->sekolah_asal, 22) : '-' }}
                </span>
                <span class="text-xs font-medium text-primary mt-0.5 inline-block">
                    {{ $topSekolah ? $topSekolah->c . ' Siswa' : '0 Siswa' }}
                </span>
            </div>
            <p class="mt-1 text-xs text-text-secondary">Peringkat #1 saat ini</p>
        </div>
    </div>

    {{-- Filter & Search Toolbar --}}
    <div class="bg-surface rounded-lg shadow border border-border p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-center">
            {{-- Search --}}
            <div class="lg:col-span-2">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama sekolah asal..." class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
            </div>

            {{-- Filter Tingkat --}}
            <div>
                <select wire:model.live="filterTingkat" class="w-full py-2 px-3 text-sm rounded-lg border border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
                    <option value="">Semua Tingkat</option>
                    <option value="X">Kelas X</option>
                    <option value="XI">Kelas XI</option>
                    <option value="XII">Kelas XII</option>
                </select>
            </div>

            {{-- Filter Jurusan --}}
            <div>
                <select wire:model.live="filterJurusan" class="w-full py-2 px-3 text-sm rounded-lg border border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusans as $j)
                        <option value="{{ $j->id }}">{{ $j->kode }} - {{ $j->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Sort By --}}
            <div>
                <select wire:model.live="sortBy" class="w-full py-2 px-3 text-sm rounded-lg border border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
                    <option value="total_desc">Siswa Terbanyak</option>
                    <option value="total_asc">Siswa Paling Sedikit</option>
                    <option value="name_asc">Nama Sekolah (A - Z)</option>
                    <option value="name_desc">Nama Sekolah (Z - A)</option>
                </select>
            </div>
        </div>

        @if($search || $filterTingkat || $filterJurusan || $sortBy !== 'total_desc')
            <div class="mt-3 pt-3 border-t border-border flex items-center justify-between text-xs">
                <span class="text-text-muted">Filter aktif diterapkan</span>
                <button type="button" wire:click="resetFilters" class="text-danger hover:underline font-medium">Reset Semua Filter</button>
            </div>
        @endif
    </div>

    {{-- Data Table --}}
    <div class="bg-surface rounded-lg shadow border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-text-secondary">
                <thead class="bg-surface-hover/70 text-xs uppercase text-text-main border-b border-border">
                    <tr>
                        <th class="px-4 py-3.5 font-semibold">Nama Sekolah Asal</th>
                        <th class="px-4 py-3.5 text-center font-semibold">Total</th>
                        <th class="px-4 py-3.5 text-center font-semibold text-blue-600 dark:text-blue-400">L</th>
                        <th class="px-4 py-3.5 text-center font-semibold text-pink-600 dark:text-pink-400">P</th>
                        
                        {{-- Kolom Dinamis Jurusan --}}
                        @foreach($jurusans as $j)
                            <th class="px-3 py-3.5 text-center font-semibold" title="{{ $j->nama }}">
                                <span class="px-1.5 py-0.5 rounded bg-surface-raised border border-border text-[11px] font-mono text-text-secondary">
                                    {{ $j->kode }}
                                </span>
                            </th>
                        @endforeach

                        <th class="px-4 py-3.5 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($sekolahAsals as $sa)
                        <tr class="hover:bg-surface-hover/50 transition">
                            <td class="px-4 py-3 font-medium text-text-main">
                                {{ $sa->sekolah_asal }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-bold bg-primary-soft text-primary border border-primary/20">
                                    {{ $sa->total_siswa }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-medium text-blue-600 dark:text-blue-400">
                                {{ $sa->siswa_l }}
                            </td>
                            <td class="px-4 py-3 text-center font-medium text-pink-600 dark:text-pink-400">
                                {{ $sa->siswa_p }}
                            </td>

                            {{-- Sebaran Tiap Jurusan --}}
                            @foreach($jurusans as $j)
                                @php
                                    $cnt = (int) ($sa->{'jurusan_' . $j->id} ?? 0);
                                @endphp
                                <td class="px-3 py-3 text-center text-xs">
                                    @if($cnt > 0)
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-md font-semibold bg-surface-raised text-text-main border border-border">
                                            {{ $cnt }}
                                        </span>
                                    @else
                                        <span class="text-text-muted/30">-</span>
                                    @endif
                                </td>
                            @endforeach

                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <a href="{{ route('dashboard-data.sekolah-asal.students', urlencode($sa->sekolah_asal)) }}" class="btn btn-secondary !py-1 !px-3 !text-xs gap-1.5 shadow-2xs">
                                    <span>Detil</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 5 + count($jurusans) }}" class="px-4 py-12 text-center text-text-muted">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-text-muted/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span class="text-sm font-medium">Tidak ada data sekolah asal yang sesuai kriteria pencarian atau filter.</span>
                                    @if($search || $filterTingkat || $filterJurusan)
                                        <button type="button" wire:click="resetFilters" class="text-xs text-primary hover:underline">Hapus filter untuk menampilkan semua</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sekolahAsals->hasPages())
            <div class="border-t border-border p-4 bg-surface">
                {{ $sekolahAsals->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Top 10 Sekolah Asal (Leaderboard) --}}
    @if($showTopModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface rounded-xl shadow-2xl border border-border w-full max-w-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-surface-raised/40">
                    <div class="flex items-center gap-3">
                        <span class="p-2 rounded-lg bg-amber-500/10 text-amber-500 border border-amber-500/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-lg font-bold text-text-main">Peringkat Top 10 Sekolah Asal</h2>
                            <p class="text-xs text-text-secondary">Sekolah feeder dengan kontribusi siswa terbanyak</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeTopModal" class="p-1.5 rounded-lg text-text-muted hover:text-text-main hover:bg-surface-hover transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 max-h-[70vh] overflow-y-auto divide-y divide-border">
                    @forelse($top10List as $item)
                        <div class="py-3.5 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                {{-- Ranking Badge --}}
                                @if($item['rank'] === 1)
                                    <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 ring-2 ring-amber-400">
                                        #1
                                    </span>
                                @elseif($item['rank'] === 2)
                                    <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200 ring-2 ring-slate-400">
                                        #2
                                    </span>
                                @elseif($item['rank'] === 3)
                                    <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs bg-amber-700/20 text-amber-900 dark:bg-amber-900/60 dark:text-amber-200 ring-2 ring-amber-600/50">
                                        #3
                                    </span>
                                @else
                                    <span class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-xs bg-surface-raised text-text-muted border border-border">
                                        #{{ $item['rank'] }}
                                    </span>
                                @endif

                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-semibold text-text-main truncate" title="{{ $item['nama'] }}">
                                        {{ $item['nama'] }}
                                    </h3>
                                    <div class="flex items-center gap-2 mt-0.5 text-xs text-text-muted">
                                        <span>{{ $item['siswa_l'] }} L / {{ $item['siswa_p'] }} P</span>
                                        <span>•</span>
                                        <span>Dominan: <strong class="text-text-main">{{ $item['dominant'] }}</strong></span>
                                    </div>
                                    {{-- Mini Progress Bar --}}
                                    <div class="w-full bg-surface-raised rounded-full h-1.5 mt-2 overflow-hidden border border-border/50">
                                        <div class="bg-primary h-1.5 rounded-full" style="width: {{ min(100, $item['percentage'] * 10) }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center pl-11 sm:pl-0">
                                <span class="text-base font-bold text-primary">{{ $item['total'] }} Siswa</span>
                                <span class="text-[11px] text-text-muted">{{ $item['percentage'] }}% dari total</span>
                            </div>
                        </div>
                    @empty
                        <p class="py-8 text-center text-text-muted text-sm">Tidak ada data untuk ditampilkan.</p>
                    @endforelse
                </div>

                <div class="px-6 py-3.5 border-t border-border bg-surface-raised/40 flex justify-end">
                    <button type="button" wire:click="closeTopModal" class="btn btn-secondary text-xs">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
