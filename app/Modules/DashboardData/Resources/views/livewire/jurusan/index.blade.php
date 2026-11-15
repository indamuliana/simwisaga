<div>
    {{-- Header & Stats Badges --}}
    <div class="mb-5 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base sm:text-lg font-bold text-text-main">Data Jurusan & Kompetensi Keahlian</h2>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-primary/10 text-primary border border-primary/20">
                        {{ count($jurusanRows) }} Jurusan
                    </span>
                </div>
                <p class="text-xs text-text-secondary mt-0.5">
                    Rekapitulasi Siswa per Jurusan, Program Keahlian (Reguler & Unggulan), dan Distribusi Tingkat Kelas
                </p>
            </div>

            {{-- Quick Gender Ratio Chips --}}
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

        {{-- Filter & Action Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Search Box --}}
                <div class="relative w-full sm:w-64">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kode atau nama jurusan..." class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary shadow-2xs">
                    <svg class="w-4 h-4 text-text-muted absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                {{-- Toggle Expand/Collapse All --}}
                <button type="button"
                    wire:click="toggleExpandAll"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold border border-border bg-surface hover:bg-surface-hover text-text-secondary hover:text-text-main transition flex items-center gap-1.5 shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-text-muted transition-transform duration-200 {{ $expandAll ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    <span>{{ $expandAll ? 'Tutup Semua Rombel' : 'Buka Semua Rombel' }}</span>
                </button>
            </div>

            {{-- Mobile Scroll Hint --}}
            <div class="text-[11px] text-text-muted flex items-center gap-1 sm:hidden">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                <span>Geser tabel untuk melihat rincian</span>
            </div>
        </div>
    </div>

    {{-- Tabel Matriks Jurusan --}}
    <div class="bg-surface rounded-xl shadow-xs border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-text-secondary">
                <thead>
                    {{-- Level 1 Header --}}
                    <tr class="bg-surface-hover/80 text-text-main">
                        <th rowspan="2" class="sticky left-0 z-20 bg-surface-hover px-4 py-3 font-bold text-left border-r border-border min-w-[170px] sm:min-w-[220px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)]">
                            Jurusan / Kompetensi Keahlian
                        </th>
                        <th rowspan="2" class="px-3 py-3 text-center font-bold border-r border-border w-24">
                            Rombel
                        </th>
                        @foreach($programs as $idx => $prog)
                            <th colspan="3" class="text-center py-2.5 px-3 font-bold border-l border-border {{ $idx % 2 === 0 ? 'bg-blue-50/70 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200' : 'bg-indigo-50/70 dark:bg-indigo-950/40 text-indigo-900 dark:text-indigo-200' }}">
                                Program {{ $prog->nama }}
                            </th>
                        @endforeach
                        <th colspan="3" class="text-center py-2.5 px-3 font-bold border-l-2 border-border bg-amber-50/70 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200">
                            Distribusi Tingkat
                        </th>
                        <th colspan="3" class="text-center py-2.5 px-3 font-black border-l-2 border-border bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200">
                            Total Siswa
                        </th>
                        <th rowspan="2" class="px-3 py-3 text-center font-bold border-l border-border w-24">
                            Aksi
                        </th>
                    </tr>
                    {{-- Level 2 Header --}}
                    <tr class="bg-surface-hover/60 border-t border-border text-[11px] uppercase tracking-wider text-text-secondary font-semibold">
                        @foreach($programs as $idx => $prog)
                            <th class="px-2.5 py-2 text-center border-l border-border text-blue-600 dark:text-blue-400">L</th>
                            <th class="px-2.5 py-2 text-center text-rose-600 dark:text-rose-400">P</th>
                            <th class="px-2.5 py-2 text-center font-bold text-text-main bg-black/[0.02] dark:bg-white/[0.02]">Jml</th>
                        @endforeach
                        <th class="px-2.5 py-2 text-center border-l-2 border-border text-amber-700 dark:text-amber-300 font-bold">X</th>
                        <th class="px-2.5 py-2 text-center text-amber-700 dark:text-amber-300 font-bold">XI</th>
                        <th class="px-2.5 py-2 text-center text-amber-700 dark:text-amber-300 font-bold">XII</th>
                        <th class="px-2.5 py-2 text-center border-l-2 border-border text-blue-700 dark:text-blue-300 font-bold">L</th>
                        <th class="px-2.5 py-2 text-center text-rose-700 dark:text-rose-300 font-bold">P</th>
                        <th class="px-3 py-2 text-center font-black text-emerald-800 dark:text-emerald-200 bg-emerald-100/50 dark:bg-emerald-900/30">TOTAL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($jurusanRows as $row)
                        @php
                            $isExpanded = in_array($row['id'], $expanded, true);
                        @endphp

                        {{-- Parent Jurusan Row --}}
                        <tr class="hover:bg-surface-hover/50 transition-colors group {{ $isExpanded ? 'bg-surface-hover/20' : '' }}">
                            {{-- Sticky Jurusan Cell --}}
                            <td class="sticky left-0 z-10 bg-surface group-hover:bg-surface-hover/90 transition-colors px-3 sm:px-4 py-2.5 border-r border-border shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)] whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click="toggleExpand({{ $row['id'] }})" class="p-1 rounded hover:bg-surface-hover text-text-muted hover:text-text-main transition focus:outline-hidden" title="Lihat rincian rombel">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $isExpanded ? 'rotate-90 text-primary' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                    <span class="px-2 py-0.5 rounded font-mono font-bold bg-surface-hover border border-border text-text-main text-xs">
                                        {{ $row['kode'] }}
                                    </span>
                                    <span class="font-bold text-text-main text-xs">
                                        {{ $row['nama'] }}
                                    </span>
                                </div>
                            </td>

                            {{-- Rombel Count --}}
                            <td class="px-3 py-2.5 text-center border-r border-border">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-surface-hover border border-border text-text-secondary">
                                    {{ $row['rombel_count'] }} Rombel
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

                            {{-- Tingkat Columns --}}
                            <td class="px-2.5 py-2.5 text-center border-l-2 border-border/80 font-semibold text-text-main">
                                {{ $row['tingkat']['X'] ?: '-' }}
                            </td>
                            <td class="px-2.5 py-2.5 text-center font-semibold text-text-main">
                                {{ $row['tingkat']['XI'] ?: '-' }}
                            </td>
                            <td class="px-2.5 py-2.5 text-center font-semibold text-text-main">
                                {{ $row['tingkat']['XII'] ?: '-' }}
                            </td>

                            {{-- Total Siswa Columns --}}
                            <td class="px-2.5 py-2.5 text-center border-l-2 border-border/80 font-bold text-blue-600 dark:text-blue-400">
                                {{ $row['total_l'] }}
                            </td>
                            <td class="px-2.5 py-2.5 text-center font-bold text-rose-600 dark:text-rose-400">
                                {{ $row['total_p'] }}
                            </td>
                            <td class="px-3 py-2.5 text-center font-extrabold text-emerald-700 dark:text-emerald-300 bg-emerald-50/50 dark:bg-emerald-950/30 text-sm">
                                {{ $row['total_all'] }}
                            </td>

                            {{-- Aksi --}}
                            <td class="px-3 py-2.5 text-center border-l border-border">
                                <a href="{{ route('dashboard-data.jurusan.students', $row['id']) }}" class="btn btn-secondary !py-1 !px-2.5 !text-xs whitespace-nowrap">
                                    Detil
                                </a>
                            </td>
                        </tr>

                        {{-- Expanded Sub-Rows (Rombel-rombel di dalam jurusan ini) --}}
                        @if($isExpanded)
                            @forelse($row['rombels'] as $r)
                                <tr class="bg-surface-hover/40 hover:bg-surface-hover/70 transition-colors border-t border-dashed border-border/60 text-xs group/sub">
                                    {{-- Sticky Sub-Row Rombel Cell --}}
                                    <td class="sticky left-0 z-10 bg-surface/95 group-hover/sub:bg-surface-hover/90 transition-colors pl-8 pr-4 py-2 border-r border-border shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)] whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="text-text-muted/60 font-mono text-xs">└─</span>
                                            <span class="font-bold text-text-main text-xs">{{ $r['nama'] }}</span>
                                            <span class="px-1.5 py-0.5 rounded text-[10px] bg-surface text-text-muted border border-border">
                                                Kelas {{ $r['tingkat'] }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Rombel Cell --}}
                                    <td class="px-3 py-2 text-center border-r border-border text-text-muted">
                                        -
                                    </td>

                                    {{-- Program Columns --}}
                                    @foreach($programs as $prog)
                                        <td class="px-2.5 py-2 text-center border-l border-border/50 {{ $r['programs'][$prog->id]['l'] > 0 ? 'text-text-main' : 'text-text-muted/40' }}">
                                            {{ $r['programs'][$prog->id]['l'] ?: '-' }}
                                        </td>
                                        <td class="px-2.5 py-2 text-center {{ $r['programs'][$prog->id]['p'] > 0 ? 'text-text-main' : 'text-text-muted/40' }}">
                                            {{ $r['programs'][$prog->id]['p'] ?: '-' }}
                                        </td>
                                        <td class="px-2.5 py-2 text-center font-semibold bg-black/[0.02] dark:bg-white/[0.02] {{ $r['programs'][$prog->id]['subtotal'] > 0 ? 'text-text-main' : 'text-text-muted/40' }}">
                                            {{ $r['programs'][$prog->id]['subtotal'] ?: '-' }}
                                        </td>
                                    @endforeach

                                    {{-- Tingkat Columns --}}
                                    <td class="px-2.5 py-2 text-center border-l-2 border-border/50 {{ $r['tingkat'] === 'X' ? 'font-bold text-amber-700 dark:text-amber-300 bg-amber-50/30' : 'text-text-muted/40' }}">
                                        {{ $r['tingkat'] === 'X' ? $r['total_all'] : '-' }}
                                    </td>
                                    <td class="px-2.5 py-2 text-center {{ $r['tingkat'] === 'XI' ? 'font-bold text-amber-700 dark:text-amber-300 bg-amber-50/30' : 'text-text-muted/40' }}">
                                        {{ $r['tingkat'] === 'XI' ? $r['total_all'] : '-' }}
                                    </td>
                                    <td class="px-2.5 py-2 text-center {{ $r['tingkat'] === 'XII' ? 'font-bold text-amber-700 dark:text-amber-300 bg-amber-50/30' : 'text-text-muted/40' }}">
                                        {{ $r['tingkat'] === 'XII' ? $r['total_all'] : '-' }}
                                    </td>

                                    {{-- Total Rombel Columns --}}
                                    <td class="px-2.5 py-2 text-center border-l-2 border-border/50 text-blue-600 dark:text-blue-400 font-semibold">
                                        {{ $r['total_l'] }}
                                    </td>
                                    <td class="px-2.5 py-2 text-center text-rose-600 dark:text-rose-400 font-semibold">
                                        {{ $r['total_p'] }}
                                    </td>
                                    <td class="px-3 py-2 text-center font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50/40 dark:bg-emerald-950/20">
                                        {{ $r['total_all'] }}
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-3 py-2 text-center border-l border-border">
                                        <a href="{{ route('dashboard-data.rombel.students', $r['id']) }}" class="text-[11px] font-semibold text-primary hover:underline whitespace-nowrap">
                                            Rincian &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr class="bg-surface-hover/20">
                                    <td colspan="{{ 2 + ($programs->count() * 3) + 3 + 3 + 1 }}" class="px-8 py-3 text-text-muted text-xs italic">
                                        Belum ada rombel pada jurusan ini untuk tahun ajaran aktif.
                                    </td>
                                </tr>
                            @endforelse
                        @endif
                    @empty
                        <tr>
                            <td colspan="{{ 2 + ($programs->count() * 3) + 3 + 3 + 1 }}" class="px-4 py-12 text-center text-text-secondary">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 text-text-muted/60 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="font-semibold text-text-main">Tidak ada data jurusan ditemukan</p>
                                    <p class="text-xs text-text-muted mt-1">Coba sesuaikan kata kunci pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if(count($jurusanRows) > 0)
                    <tfoot>
                        {{-- Baris Grand Total Seluruh Jurusan --}}
                        <tr class="bg-primary/10 dark:bg-primary/20 font-black border-t-2 border-primary/50 text-text-main">
                            <td class="sticky left-0 z-10 bg-primary/15 dark:bg-primary/30 px-4 py-3 border-r border-primary/30 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)] whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                    </svg>
                                    <span class="text-xs font-black uppercase tracking-wider text-primary dark:text-blue-300">
                                        TOTAL KESELURUHAN
                                    </span>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-center border-r border-primary/20 font-black">
                                <span class="px-2 py-0.5 rounded-full bg-primary/20 text-primary dark:text-blue-300 text-[11px]">
                                    {{ $grandTotal['rombel_count'] }} Rombel
                                </span>
                            </td>
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
                            <td class="px-2.5 py-3 text-center border-l-2 border-primary/30 text-amber-800 dark:text-amber-200 font-black">
                                {{ $grandTotal['tingkat']['X'] }}
                            </td>
                            <td class="px-2.5 py-3 text-center text-amber-800 dark:text-amber-200 font-black">
                                {{ $grandTotal['tingkat']['XI'] }}
                            </td>
                            <td class="px-2.5 py-3 text-center text-amber-800 dark:text-amber-200 font-black">
                                {{ $grandTotal['tingkat']['XII'] }}
                            </td>
                            <td class="px-2.5 py-3 text-center border-l-2 border-primary/30 text-blue-900 dark:text-blue-100 font-black">
                                {{ $grandTotal['total_l'] }}
                            </td>
                            <td class="px-2.5 py-3 text-center text-rose-900 dark:text-rose-100 font-black">
                                {{ $grandTotal['total_p'] }}
                            </td>
                            <td class="px-3 py-3 text-center font-black text-white bg-primary text-base">
                                {{ $grandTotal['total_all'] }}
                            </td>
                            <td class="px-3 py-3 text-center border-l border-primary/20 text-text-muted">
                                -
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
