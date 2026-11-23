<div class="space-y-6">
    {{-- Notifikasi Flash Message --}}
    @if(session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 flex items-center justify-between shadow-sm animate-in fade-in">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium">{{ session('message') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400 hover:opacity-75">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Rekapitulasi Absensi Siswa</h1>
            <p class="text-sm text-text-secondary">Rekapitulasi absensi siswa berdasarkan rentang tanggal, rombel, rayon, gender, dan program keahlian.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Tombol Import Excel --}}
            @can('absensi.harian.input')
                <button type="button" wire:click="openImportModal" class="btn btn-primary text-xs sm:text-sm inline-flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Import Excel</span>
                </button>
            @endcan

            {{-- Dropdown Export Excel --}}
            <div class="relative" x-data="{ openExport: false }">
                <button type="button" @click="openExport = !openExport" class="btn btn-secondary text-xs sm:text-sm inline-flex items-center gap-1.5 shadow-sm hover:border-emerald-500 hover:text-emerald-600 transition-colors">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Export Excel</span>
                    <svg class="w-3.5 h-3.5 text-text-secondary transition-transform duration-200" :class="{ 'rotate-180': openExport }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="openExport" 
                     @click.outside="openExport = false" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-72 rounded-xl bg-surface border border-border shadow-xl z-30 py-1 text-xs">
                    
                    <button type="button" 
                            wire:click="exportExcel" 
                            @click="openExport = false" 
                            class="w-full text-left px-4 py-2.5 hover:bg-surface-hover flex items-start gap-2.5 transition-colors">
                        <div class="p-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-text-main block">Export Rekapitulasi Ringkasan</span>
                            <span class="text-text-muted text-[11px] leading-tight block">Format ringkasan jumlah H, S, I, A dan persentase kehadiran per siswa</span>
                        </div>
                    </button>

                    <button type="button" 
                            wire:click="exportMatriksExcel" 
                            @click="openExport = false" 
                            class="w-full text-left px-4 py-2.5 hover:bg-surface-hover flex items-start gap-2.5 transition-colors border-t border-border/50">
                        <div class="p-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-text-main block">Export Matriks Presensi / Template</span>
                            <span class="text-text-muted text-[11px] leading-tight block">Format kalender tanggal per kolom, siap diedit dan di-import kembali</span>
                        </div>
                    </button>
                </div>
            </div>

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

            {{-- Tombol Reset Absensi Data (Bahaya / Guarded) --}}
            @can('absensi.harian.hapus')
                <button type="button" wire:click="openResetModal" class="btn text-xs sm:text-sm inline-flex items-center gap-1.5 border border-rose-200 dark:border-rose-900/60 bg-rose-50/50 hover:bg-rose-100 dark:bg-rose-950/30 dark:hover:bg-rose-950/60 text-rose-700 dark:text-rose-300 transition-colors shadow-sm">
                    <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Reset Absensi</span>
                </button>
            @endcan
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
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>

            {{-- Program Keahlian --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">Filter Program Keahlian</label>
                <select wire:model.live="programId" class="input w-full">
                    <option value="">Semua Program</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Summary Info Card --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card p-4 bg-surface border border-border rounded-xl flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Periode Aktif</p>
                <p class="text-sm font-bold text-text-main mt-0.5">{{ $periode_label }}</p>
            </div>
            <div class="p-2.5 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <div class="card p-4 bg-surface border border-border rounded-xl flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Hari Efektif KBM</p>
                <p class="text-xl font-bold text-text-main mt-0.5">{{ $hari_efektif }} <span class="text-xs font-normal text-text-muted">Hari</span></p>
            </div>
            <div class="p-2.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="card p-4 bg-surface border border-border rounded-xl flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Total Siswa Terfilter</p>
                <p class="text-xl font-bold text-text-main mt-0.5">{{ $total_siswa }} <span class="text-xs font-normal text-text-muted">Siswa</span></p>
            </div>
            <div class="p-2.5 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Tabel Rekapitulasi Presensi --}}
    <div class="card bg-surface border border-border rounded-xl overflow-hidden shadow-sm">
        @if(count($rekap) === 0)
            <div class="p-12 text-center text-text-secondary space-y-3">
                <svg class="w-12 h-12 mx-auto text-text-muted opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm font-medium">Tidak ada data presensi yang sesuai dengan kriteria filter.</p>
                <p class="text-xs text-text-muted">Cobalah untuk menyesuaikan rentang tanggal atau mereset filter yang dipilih.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs whitespace-nowrap">
                    <thead class="bg-surface-hover/70 border-b border-border text-text-secondary uppercase font-semibold text-[11px] tracking-wider">
                        <tr>
                            <th class="px-3.5 py-3 text-center w-10">No</th>
                            <th class="px-3.5 py-3 text-center">NIS</th>
                            <th class="px-4 py-3">Nama Siswa</th>
                            <th class="px-3 py-3 text-center">Rombel</th>
                            <th class="px-3 py-3 text-center font-bold text-emerald-600 dark:text-emerald-400">H</th>
                            <th class="px-3 py-3 text-center font-bold text-amber-600 dark:text-amber-400">S</th>
                            <th class="px-3 py-3 text-center font-bold text-blue-600 dark:text-blue-400">I</th>
                            <th class="px-3 py-3 text-center font-bold text-rose-600 dark:text-rose-400">A</th>
                            <th class="px-3.5 py-3">Tanggal S</th>
                            <th class="px-3.5 py-3">Tanggal I</th>
                            <th class="px-3.5 py-3">Tanggal A</th>
                            <th class="px-3.5 py-3 text-center">Presensi (%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        @foreach($rekap as $idx => $row)
                            <tr class="hover:bg-surface-hover/50 transition-colors {{ $row['a'] >= 3 ? 'bg-rose-50/40 dark:bg-rose-950/20' : '' }}">
                                <td class="px-3.5 py-2.5 text-center text-text-muted">{{ $idx + 1 }}</td>
                                <td class="px-3.5 py-2.5 text-center font-mono font-medium text-text-secondary">{{ $row['nis'] }}</td>
                                <td class="px-4 py-2.5 font-medium text-text-main">
                                    {{ $row['nama'] }}
                                    @if($row['a'] >= 3)
                                        <span class="inline-flex items-center gap-1 ml-1.5 px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-300">
                                            SP Alpa ({{ $row['a'] }})
                                        </span>
                                    @endif
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

    {{-- MODAL IMPORT EXCEL --}}
    @if($showImportModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface border border-border rounded-2xl w-full max-w-2xl p-6 shadow-2xl relative space-y-5 animate-in fade-in zoom-in-95 duration-150">
                
                {{-- Header Modal --}}
                <div class="flex items-center justify-between border-b border-border/70 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-text-main">Import Presensi Siswa</h3>
                            <p class="text-xs text-text-secondary">Unggah file Excel/CSV berformat matriks kalender presensi</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeImportModal" class="p-1 rounded-lg text-text-muted hover:text-text-main hover:bg-surface-hover transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Banner Template & Panduan --}}
                <div class="p-4 rounded-xl bg-surface-hover/70 border border-border/70 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-text-main flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Belum memiliki format file?
                        </p>
                        <p class="text-[11px] text-text-secondary leading-relaxed">
                            Unduh template matriks yang otomatis disesuaikan dengan rombel dan rentang tanggal filter yang aktif.
                        </p>
                    </div>
                    <button type="button" 
                            wire:click="downloadTemplate" 
                            class="btn btn-secondary text-xs inline-flex items-center gap-1.5 shrink-0 hover:border-primary hover:text-primary transition-colors">
                        <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Unduh Template</span>
                    </button>
                </div>

                {{-- Alert Error --}}
                @if($importErrorMessage)
                    <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-200 text-xs flex items-start gap-2.5">
                        <svg class="w-4 h-4 mt-0.5 shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <span class="font-bold block">Gagal Menganalisis File:</span>
                            <span>{{ $importErrorMessage }}</span>
                        </div>
                    </div>
                @endif

                {{-- Area Upload File --}}
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider">Pilih File (.xlsx, .xls, .csv)</label>
                    <div class="relative border-2 border-dashed border-border hover:border-primary/60 rounded-xl p-6 text-center transition-colors bg-surface-hover/30">
                        <input type="file" 
                               wire:model="file" 
                               accept=".xlsx,.xls,.csv" 
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="space-y-2 pointer-events-none">
                            <div class="w-10 h-10 rounded-full bg-surface border border-border mx-auto flex items-center justify-center text-text-muted">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <p class="text-xs font-medium text-text-main">
                                Klik untuk memilih file atau seret file ke sini
                            </p>
                            <p class="text-[11px] text-text-muted">Format yang didukung: Excel (.xlsx, .xls) atau CSV (Maks. 20MB)</p>
                        </div>
                    </div>
                    <div wire:loading wire:target="file" class="text-xs text-primary font-medium flex items-center gap-2 pt-1">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Sedang membaca dan menganalisis struktur file...</span>
                    </div>
                </div>

                {{-- HASIL PRATINJAU (PREVIEW VALIDASI) --}}
                @if($previewData)
                    <div class="space-y-3 pt-2 border-t border-border/70 animate-in fade-in">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-text-main flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Hasil Validasi File
                            </h4>
                            <span class="text-[11px] font-medium text-text-secondary font-mono">{{ $previewData['date_range'] }}</span>
                        </div>

                        {{-- Ringkasan Angka --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            <div class="p-2.5 rounded-xl bg-surface border border-border text-center">
                                <span class="text-[10px] uppercase font-semibold text-text-muted block">Siswa Cocok</span>
                                <span class="text-sm font-bold text-text-main">{{ $previewData['matched_students_count'] }} <span class="text-[10px] font-normal text-text-muted">/ {{ $previewData['total_siswa_file'] }}</span></span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-surface border border-border text-center">
                                <span class="text-[10px] uppercase font-semibold text-text-muted block">Hari Efektif</span>
                                <span class="text-sm font-bold text-text-main">{{ $previewData['total_dates'] }} <span class="text-[10px] font-normal text-text-muted">Hari</span></span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-surface border border-border text-center col-span-2">
                                <span class="text-[10px] uppercase font-semibold text-text-muted block">Total Record Siap Disuntik</span>
                                <span class="text-sm font-bold text-primary">{{ number_format($previewData['total_records']) }} <span class="text-[10px] font-normal text-text-muted">record</span></span>
                            </div>
                        </div>

                        {{-- Rincian Status Kehadiran --}}
                        <div class="p-3 rounded-xl bg-surface-hover/50 border border-border text-xs space-y-2">
                            <span class="text-[11px] font-semibold text-text-secondary block">Distribusi Status Presensi Terdeteksi:</span>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-[11px]">
                                <div class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-medium">
                                    Hadir: {{ number_format($previewData['status_counts']['Hadir'] ?? 0) }}
                                </div>
                                <div class="p-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 font-medium">
                                    Sakit: {{ number_format($previewData['status_counts']['Sakit'] ?? 0) }}
                                </div>
                                <div class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 font-medium">
                                    Izin: {{ number_format($previewData['status_counts']['Izin'] ?? 0) }}
                                </div>
                                <div class="p-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 font-bold">
                                    Alpa: {{ number_format($previewData['status_counts']['Alpa'] ?? 0) }}
                                </div>
                            </div>
                            @php
                                $totalDispen = ($previewData['status_counts']['Dispen_Lainnya'] ?? 0) + ($previewData['status_counts']['Dispen_CBT_PLH'] ?? 0) + ($previewData['status_counts']['Dispen_CBT_KWH'] ?? 0);
                            @endphp
                            @if($totalDispen > 0)
                                <p class="text-[11px] text-text-secondary text-right pt-0.5">
                                    Dispensasi (D/PLH/KWH/CBT): <span class="font-bold text-purple-600 dark:text-purple-400">{{ number_format($totalDispen) }}</span>
                                </p>
                            @endif
                        </div>

                        {{-- Peringatan jika ada Siswa Tak Ditemukan --}}
                        @if(!empty($previewData['unmatched_students']))
                            <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 text-xs space-y-1.5 max-h-28 overflow-y-auto">
                                <span class="font-bold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    {{ count($previewData['unmatched_students']) }} Siswa pada file tidak ditemukan di database (diabaikan otomatis):
                                </span>
                                <ul class="list-disc list-inside space-y-0.5 pl-1 text-[11px] text-amber-700 dark:text-amber-300">
                                    @foreach($previewData['unmatched_students'] as $unm)
                                        <li>{{ $unm['nis'] }} - {{ $unm['nama'] }} ({{ $unm['rombel'] }})</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Footer Tombol Aksi --}}
                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-border/70">
                    <button type="button" 
                            wire:click="closeImportModal" 
                            class="px-4 py-2 rounded-xl border border-border text-xs font-semibold text-text-secondary hover:bg-surface-hover transition-colors">
                        Batal
                    </button>

                    @if($previewData && !empty($previewData['records']))
                        <button type="button" 
                                wire:click="confirmImport" 
                                wire:loading.attr="disabled"
                                class="btn btn-primary text-xs font-semibold inline-flex items-center gap-2 shadow-sm">
                            <span wire:loading.remove wire:target="confirmImport">Konfirmasi & Simpan ke Database</span>
                            <span wire:loading wire:target="confirmImport" class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Menyimpan {{ number_format($previewData['total_records']) }} Record...</span>
                            </span>
                        </button>
                    @endif
                </div>

            </div>
        </div>
    @endif

    {{-- MODAL RESET / HAPUS ABSENSI --}}
    @if($showResetModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface border border-rose-300 dark:border-rose-900 rounded-2xl w-full max-w-lg p-6 shadow-2xl relative space-y-5 animate-in fade-in zoom-in-95 duration-150">
                
                {{-- Header Modal --}}
                <div class="flex items-center justify-between border-b border-border/70 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-950/60 text-rose-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-text-main">Reset / Hapus Data Absensi</h3>
                            <p class="text-xs text-text-secondary">Pembersihan data riwayat presensi siswa</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeResetModal" class="p-1 rounded-lg text-text-muted hover:text-text-main hover:bg-surface-hover transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Alert Peringatan Bahaya --}}
                <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs space-y-1">
                    <div class="flex items-center gap-1.5 font-bold">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>Perhatian: Tindakan Bersifat Permanen!</span>
                    </div>
                    <p class="text-[11px] leading-relaxed text-rose-700 dark:text-rose-300">
                        Record absensi yang dihapus tidak dapat dikembalikan kecuali Anda memiliki berkas cadangan (backup) Excel atau snapshot database.
                    </p>
                </div>

                {{-- Pilihan Cakupan Reset --}}
                <div class="space-y-2.5">
                    <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider">Pilih Cakupan Data yang Direset:</label>

                    <label class="flex items-start gap-3 p-3 rounded-xl border border-border hover:bg-surface-hover/50 cursor-pointer transition {{ $resetScope === 'filtered' ? 'bg-rose-50/30 dark:bg-rose-950/20 border-rose-300 dark:border-rose-800' : '' }}">
                        <input type="radio" wire:model.live="resetScope" value="filtered" class="mt-0.5 text-rose-600 focus:ring-rose-500">
                        <div class="text-xs space-y-0.5">
                            <span class="font-bold text-text-main block">Sesuai Filter Halaman Saat Ini</span>
                            <span class="text-text-muted text-[11px] block">
                                Rentang: <strong>{{ $periode_label }}</strong> | Rombel: <strong>{{ $rombel_nama }}</strong> | Rayon: <strong>{{ $rayon_nama }}</strong>
                            </span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3 rounded-xl border border-border hover:bg-surface-hover/50 cursor-pointer transition {{ $resetScope === 'semester' ? 'bg-rose-50/30 dark:bg-rose-950/20 border-rose-300 dark:border-rose-800' : '' }}">
                        <input type="radio" wire:model.live="resetScope" value="semester" class="mt-0.5 text-rose-600 focus:ring-rose-500">
                        <div class="text-xs space-y-0.5">
                            <span class="font-bold text-text-main block">Seluruh Data Semester Aktif</span>
                            <span class="text-text-muted text-[11px] block">
                                Menghapus seluruh data absensi semua siswa di semua tanggal untuk semester ganjil saat ini.
                            </span>
                        </div>
                    </label>
                </div>

                {{-- Indikator Jumlah Record Terkena Dampak --}}
                <div class="p-3 rounded-xl bg-surface-hover/70 border border-border flex items-center justify-between text-xs">
                    <span class="text-text-secondary font-medium">Record absensi yang akan dihapus:</span>
                    <span class="text-sm font-bold text-rose-600 dark:text-rose-400">
                        {{ number_format($this->countRecordsToReset) }} record
                    </span>
                </div>

                {{-- Konfirmasi Ketik RESET --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-text-secondary">
                        Ketik <strong class="text-rose-600 font-mono">RESET</strong> untuk mengonfirmasi:
                    </label>
                    <input type="text" 
                           wire:model.live="resetConfirmationText" 
                           placeholder="Ketik RESET di sini..." 
                           class="input w-full font-mono text-xs uppercase">
                    
                    @if($resetErrorMessage)
                        <p class="text-xs text-rose-600 font-medium">{{ $resetErrorMessage }}</p>
                    @endif
                </div>

                {{-- Footer Tombol Aksi --}}
                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-border/70">
                    <button type="button" 
                            wire:click="closeResetModal" 
                            class="px-4 py-2 rounded-xl border border-border text-xs font-semibold text-text-secondary hover:bg-surface-hover transition-colors">
                        Batal
                    </button>

                    <button type="button" 
                            wire:click="executeResetAbsensi" 
                            wire:loading.attr="disabled"
                            @if(strtoupper(trim($resetConfirmationText)) !== 'RESET' || $this->countRecordsToReset === 0) disabled @endif
                            class="btn text-xs font-semibold inline-flex items-center gap-1.5 shadow-sm text-white bg-rose-600 hover:bg-rose-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                        <svg wire:loading.remove wire:target="executeResetAbsensi" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span wire:loading.remove wire:target="executeResetAbsensi">Hapus & Reset Sekarang</span>
                        <span wire:loading wire:target="executeResetAbsensi" class="inline-flex items-center gap-1.5">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Sedang Mereset...</span>
                        </span>
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
