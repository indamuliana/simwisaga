<div class="space-y-6">
    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Rekapitulasi Presensi Kehadiran Siswa</h1>
            <p class="text-sm text-text-secondary">Rekapitulasi absensi siswa berdasarkan rentang tanggal, rombel, rayon, gender, dan program keahlian.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Tombol Export Excel --}}
            <button type="button" wire:click="exportExcel" wire:loading.attr="disabled" class="btn btn-secondary text-xs sm:text-sm inline-flex items-center gap-1.5 shadow-sm hover:border-emerald-500 hover:text-emerald-600 transition-colors">
                <svg wire:loading.remove wire:target="exportExcel" class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <svg wire:loading wire:target="exportExcel" class="animate-spin w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Export Excel</span>
            </button>

            {{-- Tombol Cetak PDF --}}
            <a href="{{ route('absensi.rekap.pdf', [
                'tgl_mulai' => $tgl_mulai,
                'tgl_selesai' => $tgl_selesai,
                'rombel_id' => $rombelId,
                'rayon_id' => $rayonId,
                'gender' => $gender,
                'program_id' => $programId,
                'search' => $search,
            ]) }}" target="_blank" class="btn btn-secondary text-xs sm:text-sm inline-flex items-center gap-1.5 shadow-sm hover:border-rose-500 hover:text-rose-600 transition-colors">
                <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak PDF</span>
            </a>

            {{-- Tombol Reset Filter --}}
            <button type="button" wire:click="resetFilters" class="btn btn-secondary text-xs sm:text-sm inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Reset</span>
            </button>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card p-5 bg-surface border border-border space-y-4 rounded-xl shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {{-- Tanggal Mulai --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">Tanggal Mulai</label>
                <input type="date" wire:model.live="tgl_mulai" class="input w-full">
            </div>

            {{-- Tanggal Selesai --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">Tanggal Selesai</label>
                <input type="date" wire:model.live="tgl_selesai" class="input w-full">
            </div>

            {{-- Pencarian 1 Siswa (NIS / Nama) --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">Cari Siswa (NIS / Nama)</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik NIS, NISN, atau Nama..." class="input w-full pl-9">
                    <svg class="w-4 h-4 absolute left-3 top-3 text-text-secondary pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-1 border-t border-border/60">
            {{-- Rombel --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">Filter Rombel</label>
                <select wire:model.live="rombelId" class="input w-full">
                    <option value="">Semua Rombel</option>
                    @foreach($rombels as $r)
                        <option value="{{ $r->id }}">{{ $r->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Rayon --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">Filter Rayon</label>
                <select wire:model.live="rayonId" class="input w-full">
                    <option value="">Semua Rayon</option>
                    @foreach($rayons as $ry)
                        <option value="{{ $ry->id }}">{{ $ry->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Gender --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">Filter Gender</label>
                <select wire:model.live="gender" class="input w-full">
                    <option value="">Semua Gender</option>
                    <option value="L">Laki-laki (L)</option>
                    <option value="P">Perempuan (P)</option>
                </select>
            </div>

            {{-- Program --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">Filter Program</label>
                <select wire:model.live="programId" class="input w-full">
                    <option value="">Semua Program</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Information Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-1 text-xs">
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface border border-border text-text-main font-medium">
                <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Periode: <strong>{{ $periode_label }}</strong></span>
            </span>

            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface border border-border text-text-main font-medium">
                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Hari Efektif: <strong>{{ $hari_efektif }} Hari</strong></span>
            </span>

            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface border border-border text-text-main font-medium">
                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Total Siswa: <strong>{{ $total_siswa }}</strong></span>
            </span>
        </div>

        <div class="text-text-secondary">
            Filter: 
            <span class="text-text-main font-medium">{{ $rombel_nama }}</span> &bull; 
            <span class="text-text-main font-medium">{{ $rayon_nama }}</span> &bull; 
            <span class="text-text-main font-medium">{{ $gender_nama }}</span>
        </div>
    </div>

    {{-- Tabel Rekap Data --}}
    <div class="card overflow-hidden bg-surface border border-border rounded-xl shadow-sm">
        @if(empty($rekap))
            <div class="p-12 text-center">
                <svg class="w-12 h-12 mx-auto text-text-secondary/60 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-base font-semibold text-text-main">Tidak ada data siswa ditemukan</h3>
                <p class="text-sm text-text-secondary mt-1">Coba sesuaikan filter atau kata kunci pencarian Anda.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead class="text-[11px] font-semibold uppercase tracking-wider text-text-secondary bg-surface-hover/80 border-b border-border">
                        <tr>
                            <th class="px-3.5 py-3 text-center w-12">No</th>
                            <th class="px-3 py-3 text-center w-28">NIS</th>
                            <th class="px-4 py-3 min-w-[180px]">Nama Siswa</th>
                            <th class="px-3 py-3 text-center w-28">Rombel</th>
                            <th class="px-3 py-3 text-center text-emerald-600 dark:text-emerald-400 w-12" title="Hadir (termasuk Dispen)">H</th>
                            <th class="px-3 py-3 text-center text-amber-600 dark:text-amber-400 w-12" title="Sakit">S</th>
                            <th class="px-3 py-3 text-center text-blue-600 dark:text-blue-400 w-12" title="Izin">I</th>
                            <th class="px-3 py-3 text-center text-rose-600 dark:text-rose-400 w-12" title="Alpa">A</th>
                            <th class="px-3.5 py-3 min-w-[140px] text-text-secondary">Tanggal S</th>
                            <th class="px-3.5 py-3 min-w-[140px] text-text-secondary">Tanggal I</th>
                            <th class="px-3.5 py-3 min-w-[140px] text-rose-600 dark:text-rose-400">Tanggal A</th>
                            <th class="px-3 py-3 text-center w-24">Hadir %</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border text-text-main">
                        @foreach($rekap as $index => $row)
                            <tr class="hover:bg-surface-hover transition-colors {{ $row['a'] >= 3 ? 'bg-rose-50/30 dark:bg-rose-950/10' : '' }}">
                                <td class="px-3.5 py-2.5 text-center text-text-secondary font-medium">{{ $index + 1 }}</td>
                                <td class="px-3 py-2.5 text-center font-mono font-medium text-text-secondary">{{ $row['nis'] }}</td>
                                <td class="px-4 py-2.5 font-medium">
                                    <span class="block">{{ $row['nama'] }}</span>
                                    <span class="text-[10px] text-text-secondary">{{ $row['rayon'] }} &bull; {{ $row['gender'] }}</span>
                                </td>
                                <td class="px-3 py-2.5 text-center text-text-secondary">{{ $row['rombel'] }}</td>
                                <td class="px-3 py-2.5 text-center font-bold text-emerald-600 dark:text-emerald-400">{{ $row['h'] }}</td>
                                <td class="px-3 py-2.5 text-center font-semibold text-amber-600 dark:text-amber-400">{{ $row['s'] }}</td>
                                <td class="px-3 py-2.5 text-center font-semibold text-blue-600 dark:text-blue-400">{{ $row['i'] }}</td>
                                <td class="px-3 py-2.5 text-center font-bold {{ $row['a'] >= 3 ? 'text-rose-600 dark:text-rose-400' : 'text-text-secondary' }}">
                                    @if($row['a'] >= 3)
                                        <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-300">
                                            {{ $row['a'] }}
                                        </span>
                                    @else
                                        {{ $row['a'] }}
                                    @endif
                                </td>
                                <td class="px-3.5 py-2.5 text-[11px] text-text-secondary font-mono">{{ $row['tgl_s'] }}</td>
                                <td class="px-3.5 py-2.5 text-[11px] text-text-secondary font-mono">{{ $row['tgl_i'] }}</td>
                                <td class="px-3.5 py-2.5 text-[11px] font-mono {{ $row['a'] >= 3 ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-text-secondary' }}">
                                    {{ $row['tgl_a'] }}
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    @php
                                        $p = $row['persentase_numeric'];
                                        $badgeColor = $p >= 90 ? 'text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40' : ($p >= 75 ? 'text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/40' : 'text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40');
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $badgeColor }}">
                                        {{ $row['persentase'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
