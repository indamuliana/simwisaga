<div x-data="{
    expanded: @js($expandedRayons),
    toggle(id) {
        id = String(id);
        if (this.expanded.includes(id)) {
            this.expanded = this.expanded.filter(x => x !== id);
        } else {
            this.expanded.push(id);
        }
    },
    expandAll(ids) {
        this.expanded = ids.map(String);
    },
    collapseAll() {
        this.expanded = [];
    },
    isExpanded(id) {
        return this.expanded.includes(String(id));
    }
}">
    {{-- Header & Stats Badges --}}
    <div class="mb-5 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base sm:text-lg font-bold text-text-main">Data Rayon & Pembimbing</h2>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-primary/10 text-primary border border-primary/20">
                        {{ $kpi['total_rayon'] }} Rayon
                    </span>
                    @if($kpi['siswa_tanpa_rayon'] > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-300/60 dark:border-amber-800/60">
                            {{ $kpi['siswa_tanpa_rayon'] }} Tanpa Rayon
                        </span>
                    @endif
                </div>
                <p class="text-xs text-text-secondary mt-0.5">
                    Rekapitulasi sebaran siswa per Rayon kewilayahan, Jurusan, Rombel, dan komposisi gender (L/P)
                </p>
            </div>

            {{-- Quick Gender Ratio Chips --}}
            <div class="flex flex-wrap items-center gap-2">
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50/70 dark:bg-blue-950/50 border border-blue-200/60 dark:border-blue-800/60 text-xs">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <span class="text-text-secondary">Laki-laki:</span>
                    <span class="font-bold text-blue-700 dark:text-blue-300">{{ number_format($grandTotal['total_l']) }}</span>
                    <span class="text-[10px] text-text-muted">({{ $kpi['ratio_l'] }}%)</span>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50/70 dark:bg-rose-950/50 border border-rose-200/60 dark:border-rose-800/60 text-xs">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span class="text-text-secondary">Perempuan:</span>
                    <span class="font-bold text-rose-700 dark:text-rose-300">{{ number_format($grandTotal['total_p']) }}</span>
                    <span class="text-[10px] text-text-muted">({{ $kpi['ratio_p'] }}%)</span>
                </div>
            </div>
        </div>

        {{-- 4 Kartu KPI Rayon --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-surface p-3.5 rounded-xl border border-border shadow-2xs">
                <span class="text-[11px] font-semibold text-text-secondary block">Total Rayon</span>
                <p class="mt-1 text-xl font-black text-text-main">{{ number_format($kpi['total_rayon']) }}</p>
                <span class="text-[10px] text-text-muted">Rayon kewilayahan aktif</span>
            </div>
            <div class="bg-surface p-3.5 rounded-xl border border-border shadow-2xs">
                <span class="text-[11px] font-semibold text-text-secondary block">Siswa Ber-Rayon</span>
                <p class="mt-1 text-xl font-black text-primary dark:text-blue-400">{{ number_format($kpi['siswa_ber_rayon']) }}</p>
                <span class="text-[10px] text-text-muted">Telah dipetakan ke rayon</span>
            </div>
            <div class="bg-surface p-3.5 rounded-xl border {{ $kpi['siswa_tanpa_rayon'] > 0 ? 'border-amber-400/50 bg-amber-50/20 dark:bg-amber-950/10' : 'border-border' }} shadow-2xs">
                <span class="text-[11px] font-semibold {{ $kpi['siswa_tanpa_rayon'] > 0 ? 'text-amber-700 dark:text-amber-400' : 'text-text-secondary' }} block">Siswa Tanpa Rayon</span>
                <p class="mt-1 text-xl font-black {{ $kpi['siswa_tanpa_rayon'] > 0 ? 'text-amber-700 dark:text-amber-300' : 'text-text-muted' }}">
                    {{ number_format($kpi['siswa_tanpa_rayon']) }}
                </p>
                <span class="text-[10px] text-text-muted">{{ $kpi['siswa_tanpa_rayon'] > 0 ? 'Perlu penempatan rayon' : 'Semua siswa terpetakan' }}</span>
            </div>
            <div class="bg-surface p-3.5 rounded-xl border border-border shadow-2xs">
                <span class="text-[11px] font-semibold text-text-secondary block">Total Siswa Aktif</span>
                <p class="mt-1 text-xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($kpi['total_siswa']) }}</p>
                <span class="text-[10px] text-text-muted">Siswa terdaftar saat ini</span>
            </div>
        </div>

        {{-- Filter & Action Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Search Box --}}
                <div class="relative w-full sm:w-64">
                    <input type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari Rayon / Pembimbing..."
                        class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary shadow-2xs">
                    <svg class="w-4 h-4 text-text-muted absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                {{-- Filter Tingkat --}}
                <div class="w-full sm:w-40">
                    <select wire:model.live="filterTingkat" class="w-full px-3 py-1.5 text-xs rounded-lg border border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary shadow-2xs">
                        <option value="">Semua Tingkat</option>
                        <option value="X">Tingkat X</option>
                        <option value="XI">Tingkat XI</option>
                        <option value="XII">Tingkat XII</option>
                    </select>
                </div>

                {{-- Reset Filter Button --}}
                @if($search !== '' || $filterTingkat !== '')
                    <button type="button"
                        wire:click="resetFilters"
                        class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 transition">
                        Reset Filter
                    </button>
                @endif

                {{-- Toggle Expand/Collapse All --}}
                <div class="inline-flex rounded-lg border border-border shadow-2xs overflow-hidden">
                    <button type="button"
                        @click="expandAll({{ json_encode($allRayonIds) }})"
                        wire:click="expandAll({{ json_encode($allRayonIds) }})"
                        class="px-2.5 py-1.5 text-xs font-semibold bg-surface hover:bg-surface-hover text-text-secondary hover:text-text-main transition flex items-center gap-1 border-r border-border"
                        title="Buka rincian rombel seluruh rayon">
                        <svg class="w-3.5 h-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <span>Buka Semua</span>
                    </button>
                    <button type="button"
                        @click="collapseAll()"
                        wire:click="collapseAll"
                        class="px-2.5 py-1.5 text-xs font-semibold bg-surface hover:bg-surface-hover text-text-secondary hover:text-text-main transition flex items-center gap-1"
                        title="Tutup rincian rombel seluruh rayon">
                        <svg class="w-3.5 h-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                        <span>Tutup Semua</span>
                    </button>
                </div>
            </div>

            {{-- Mobile Scroll Hint --}}
            <div class="text-[11px] text-text-muted flex items-center gap-1 sm:hidden">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                <span>Geser tabel untuk melihat rincian jurusan</span>
            </div>
        </div>
    </div>

    {{-- Tabel Matriks Rayon x Jurusan --}}
    <div class="bg-surface rounded-xl shadow-xs border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-text-secondary">
                <thead>
                    {{-- Level 1 Header --}}
                    <tr class="bg-surface-hover/80 text-text-main">
                        <th rowspan="2" class="sticky left-0 z-20 bg-surface-hover px-4 py-3 font-bold text-left border-r border-border min-w-[200px] sm:min-w-[240px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)]">
                            Rayon & Pembimbing
                        </th>
                        <th rowspan="2" class="px-2.5 py-3 text-center font-bold border-r border-border w-16">
                            Rombel
                        </th>
                        @foreach($jurusans as $idx => $jurusan)
                            <th colspan="3" class="text-center py-2.5 px-2 font-bold border-l border-border {{ $idx % 2 === 0 ? 'bg-blue-50/70 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200' : 'bg-indigo-50/70 dark:bg-indigo-950/40 text-indigo-900 dark:text-indigo-200' }}">
                                {{ $jurusan->kode ?? $jurusan->nama }}
                            </th>
                        @endforeach
                        <th colspan="3" class="text-center py-2.5 px-3 font-black border-l-2 border-border bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200">
                            Total Rayon
                        </th>
                        <th rowspan="2" class="px-3 py-3 text-center font-bold border-l border-border w-20">
                            Aksi
                        </th>
                    </tr>
                    {{-- Level 2 Header --}}
                    <tr class="bg-surface-hover/60 border-t border-border text-[11px] uppercase tracking-wider text-text-secondary font-semibold">
                        @foreach($jurusans as $jurusan)
                            <th class="px-2 py-2 text-center border-l border-border text-blue-600 dark:text-blue-400">L</th>
                            <th class="px-2 py-2 text-center text-rose-600 dark:text-rose-400">P</th>
                            <th class="px-2 py-2 text-center font-bold text-text-main bg-black/[0.02] dark:bg-white/[0.02]">Jml</th>
                        @endforeach
                        <th class="px-2.5 py-2 text-center border-l-2 border-border text-blue-700 dark:text-blue-300 font-bold">L</th>
                        <th class="px-2.5 py-2 text-center text-rose-700 dark:text-rose-300 font-bold">P</th>
                        <th class="px-3 py-2 text-center font-black text-emerald-800 dark:text-emerald-200 bg-emerald-100/50 dark:bg-emerald-900/30">TOTAL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($rows as $row)
                        @php
                            $isExpanded = in_array((string)$row['id'], $expandedRayons, true);
                            $rombelCount = count($row['rombels']);
                        @endphp

                        {{-- Parent Rayon Row --}}
                        <tr class="hover:bg-surface-hover/50 transition-colors group {{ $row['is_tanpa_rayon'] ? 'bg-amber-50/30 dark:bg-amber-950/20' : ($isExpanded ? 'bg-surface-hover/25' : '') }}">
                            {{-- Sticky Rayon & Pembimbing Cell --}}
                            <td class="sticky left-0 z-10 {{ $row['is_tanpa_rayon'] ? 'bg-amber-50/80 dark:bg-amber-950/40' : 'bg-surface group-hover:bg-surface-hover/90' }} transition-colors px-3 sm:px-4 py-2.5 border-r border-border shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)] whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @if($rombelCount > 0)
                                        <button type="button"
                                            @click="toggle('{{ $row['id'] }}')"
                                            class="p-1 rounded hover:bg-surface-hover text-text-muted hover:text-text-main transition focus:outline-hidden"
                                            title="Lihat rincian rombel">
                                            <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                                :class="isExpanded('{{ $row['id'] }}') ? 'rotate-90 text-primary' : '{{ $isExpanded ? 'rotate-90 text-primary' : '' }}'"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    @else
                                        <span class="w-5.5"></span>
                                    @endif

                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-text-main {{ $row['is_tanpa_rayon'] ? 'text-amber-700 dark:text-amber-300' : '' }}">
                                                {{ $row['nama'] }}
                                            </span>
                                            @if($row['is_tanpa_rayon'])
                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold bg-amber-200/60 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200">
                                                    Perlu Aksi
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-text-muted flex items-center gap-1 mt-0.5">
                                            <svg class="w-3 h-3 text-text-muted/70 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span class="truncate max-w-[170px]" title="{{ $row['pembimbing'] }}">{{ $row['pembimbing'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Jumlah Rombel Badge --}}
                            <td class="px-2.5 py-2.5 text-center border-r border-border">
                                @if($rombelCount > 0)
                                    <button type="button"
                                        @click="toggle('{{ $row['id'] }}')"
                                        class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-medium bg-surface-hover hover:bg-primary-soft hover:text-primary text-text-secondary transition"
                                        title="Klik untuk membuka rincian {{ $rombelCount }} rombel">
                                        {{ $rombelCount }}
                                    </button>
                                @else
                                    <span class="text-text-muted text-[11px]">-</span>
                                @endif
                            </td>

                            {{-- Sebaran per Jurusan --}}
                            @foreach($jurusans as $jurusan)
                                @php
                                    $dataJ = $row['jurusans'][$jurusan->id] ?? ['l' => 0, 'p' => 0, 'total' => 0];
                                @endphp
                                <td class="px-2 py-2.5 text-center border-l border-border {{ $dataJ['l'] > 0 ? 'text-blue-700 dark:text-blue-300 font-medium' : 'text-text-muted/50' }}">
                                    {{ $dataJ['l'] > 0 ? $dataJ['l'] : '-' }}
                                </td>
                                <td class="px-2 py-2.5 text-center text-rose-700 dark:text-rose-300 {{ $dataJ['p'] > 0 ? 'font-medium' : 'text-text-muted/50' }}">
                                    {{ $dataJ['p'] > 0 ? $dataJ['p'] : '-' }}
                                </td>
                                <td class="px-2 py-2.5 text-center font-bold bg-black/[0.015] dark:bg-white/[0.015] {{ $dataJ['total'] > 0 ? 'text-text-main' : 'text-text-muted/50' }}">
                                    {{ $dataJ['total'] > 0 ? $dataJ['total'] : '-' }}
                                </td>
                            @endforeach

                            {{-- Total Rayon --}}
                            <td class="px-2.5 py-2.5 text-center border-l-2 border-border font-bold text-blue-800 dark:text-blue-300">
                                {{ $row['total_l'] > 0 ? $row['total_l'] : '-' }}
                            </td>
                            <td class="px-2.5 py-2.5 text-center font-bold text-rose-800 dark:text-rose-300">
                                {{ $row['total_p'] > 0 ? $row['total_p'] : '-' }}
                            </td>
                            <td class="px-3 py-2.5 text-center font-black text-emerald-800 dark:text-emerald-200 bg-emerald-50/60 dark:bg-emerald-950/30 text-sm">
                                {{ $row['total_all'] }}
                            </td>

                            {{-- Aksi Drill-Down Siswa --}}
                            <td class="px-3 py-2.5 text-center border-l border-border whitespace-nowrap">
                                <a href="{{ route('dashboard-data.rayon.students', $row['id']) }}"
                                    class="inline-flex items-center gap-1 px-2 py-1 rounded text-[11px] font-semibold text-primary hover:bg-primary-soft transition"
                                    title="Lihat daftar siswa rayon ini">
                                    <span>Siswa</span>
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>

                        {{-- Child Sub-Rows: Rombel Breakdown --}}
                        @if($rombelCount > 0)
                            @foreach($row['rombels'] as $rombel)
                                <tr x-show="isExpanded('{{ $row['id'] }}')"
                                    @if(!$isExpanded) style="display: none;" @endif
                                    class="bg-surface-hover/30 dark:bg-surface-hover/10 text-[11px] border-t border-dashed border-border">
                                    {{-- Sub-row Sticky Left --}}
                                    <td class="sticky left-0 z-10 bg-surface/95 dark:bg-surface/95 px-3 sm:px-4 py-1.5 border-r border-border shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)] pl-8 sm:pl-10">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-text-muted font-mono">├─</span>
                                            <span class="font-medium text-text-main">{{ $rombel['nama'] }}</span>
                                            @if($rombel['tingkat'] && $rombel['tingkat'] !== '-')
                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-500/10 text-amber-700 dark:text-amber-300">
                                                    {{ $rombel['tingkat'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Kolom Rombel --}}
                                    <td class="px-2.5 py-1.5 text-center border-r border-border text-text-muted">-</td>

                                    {{-- Kolom Jurusan untuk Rombel Ini --}}
                                    @foreach($jurusans as $jurusan)
                                        @php
                                            $isThisJurusan = ($rombel['jurusan_id'] === $jurusan->id);
                                        @endphp
                                        <td class="px-2 py-1.5 text-center border-l border-border {{ $isThisJurusan && $rombel['l'] > 0 ? 'text-blue-600 dark:text-blue-300 font-medium' : 'text-text-muted/40' }}">
                                            {{ $isThisJurusan ? ($rombel['l'] > 0 ? $rombel['l'] : '-') : '-' }}
                                        </td>
                                        <td class="px-2 py-1.5 text-center {{ $isThisJurusan && $rombel['p'] > 0 ? 'text-rose-600 dark:text-rose-300 font-medium' : 'text-text-muted/40' }}">
                                            {{ $isThisJurusan ? ($rombel['p'] > 0 ? $rombel['p'] : '-') : '-' }}
                                        </td>
                                        <td class="px-2 py-1.5 text-center font-bold bg-black/[0.01] dark:bg-white/[0.01] {{ $isThisJurusan && $rombel['total'] > 0 ? 'text-text-main' : 'text-text-muted/40' }}">
                                            {{ $isThisJurusan ? ($rombel['total'] > 0 ? $rombel['total'] : '-') : '-' }}
                                        </td>
                                    @endforeach

                                    {{-- Subtotal Rombel Ini --}}
                                    <td class="px-2.5 py-1.5 text-center border-l-2 border-border text-blue-700 dark:text-blue-300 font-semibold">
                                        {{ $rombel['l'] > 0 ? $rombel['l'] : '-' }}
                                    </td>
                                    <td class="px-2.5 py-1.5 text-center text-rose-700 dark:text-rose-300 font-semibold">
                                        {{ $rombel['p'] > 0 ? $rombel['p'] : '-' }}
                                    </td>
                                    <td class="px-3 py-1.5 text-center font-black text-text-main bg-emerald-50/40 dark:bg-emerald-950/20">
                                        {{ $rombel['total'] }}
                                    </td>

                                    {{-- Aksi Rombel --}}
                                    <td class="px-3 py-1.5 text-center border-l border-border whitespace-nowrap">
                                        @if($rombel['id'])
                                            <a href="{{ route('dashboard-data.rombel.students', $rombel['id']) }}"
                                                class="text-text-muted hover:text-primary transition text-[10px]"
                                                title="Lihat seluruh siswa kelas {{ $rombel['nama'] }}">
                                                Lihat Rombel
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                    @empty
                        <tr>
                            <td colspan="{{ 6 + (count($jurusans) * 3) }}" class="px-4 py-8 text-center text-text-muted">
                                Tidak ada data rayon yang sesuai dengan pencarian atau filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                {{-- Table Footer: Grand Total Keseluruhan --}}
                @if(count($rows) > 0)
                    <tfoot>
                        <tr class="bg-primary/5 dark:bg-primary/15 border-t-2 border-primary/30 font-bold text-text-main">
                            {{-- Sticky Footer Cell --}}
                            <td class="sticky left-0 z-10 bg-primary/10 dark:bg-primary/20 px-3 sm:px-4 py-3 border-r border-primary/20 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)]">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                    </svg>
                                    <span class="text-xs font-black uppercase tracking-wider text-primary dark:text-blue-300">
                                        TOTAL KESELURUHAN
                                    </span>
                                </div>
                            </td>

                            {{-- Footer Rombel --}}
                            <td class="px-2.5 py-3 text-center border-r border-primary/20 text-text-muted">-</td>

                            {{-- Footer Jurusans --}}
                            @foreach($jurusans as $jurusan)
                                @php
                                    $gtJ = $grandTotal['jurusans'][$jurusan->id] ?? ['l' => 0, 'p' => 0, 'total' => 0];
                                @endphp
                                <td class="px-2 py-3 text-center border-l border-primary/20 text-blue-800 dark:text-blue-200 font-bold">
                                    {{ $gtJ['l'] }}
                                </td>
                                <td class="px-2 py-3 text-center text-rose-800 dark:text-rose-200 font-bold">
                                    {{ $gtJ['p'] }}
                                </td>
                                <td class="px-2 py-3 text-center font-black bg-primary/10 dark:bg-primary/25 text-text-main">
                                    {{ $gtJ['total'] }}
                                </td>
                            @endforeach

                            {{-- Footer Total Rayon --}}
                            <td class="px-2.5 py-3 text-center border-l-2 border-primary/30 text-blue-900 dark:text-blue-100 font-black">
                                {{ $grandTotal['total_l'] }}
                            </td>
                            <td class="px-2.5 py-3 text-center text-rose-900 dark:text-rose-100 font-black">
                                {{ $grandTotal['total_p'] }}
                            </td>
                            <td class="px-3 py-3 text-center font-black text-white bg-primary text-base">
                                {{ $grandTotal['total_all'] }}
                            </td>

                            {{-- Footer Aksi --}}
                            <td class="px-3 py-3 text-center border-l border-primary/20 text-text-muted">-</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
