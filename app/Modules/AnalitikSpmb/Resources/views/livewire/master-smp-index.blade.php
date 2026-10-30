<div class="space-y-6">
    {{-- Header & Navigasi --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-border">
        <div>
            <div class="flex items-center gap-2 text-xs text-text-muted mb-1 font-mono">
                <a href="{{ route('analitik-spmb.asal-smp') }}" class="hover:text-primary transition-colors">Analitik SPMB</a>
                <span>/</span>
                <span class="text-text-main font-semibold">Master Data SMP</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-text-main tracking-tight flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-primary/10 text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </span>
                <span>Master Data SMP Resmi</span>
            </h1>
            <p class="text-xs sm:text-sm text-text-secondary mt-0.5">Katalog referensi resmi sekolah menengah (Kemendikbud / Kemenag) untuk pemetaan dan verifikasi asal pendaftar SPMB.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('analitik-spmb.asal-smp') }}" class="btn btn-secondary text-xs gap-1.5 shadow-2xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>Analitik Asal SMP</span>
            </a>

            <a href="{{ route('analitik-spmb.perbaikan-smp') }}" class="btn btn-secondary text-xs gap-1.5 shadow-2xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                <span>Perbaikan Data</span>
            </a>

            @can('analitik_spmb.master_smp.import')
                <button type="button" wire:click="openImportModal" class="btn btn-primary text-xs gap-1.5 shadow-2xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Import Excel / CSV</span>
                </button>
            @endcan
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

    {{-- Kartu Ringkasan Statistik --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        {{-- Total Master --}}
        <div class="p-4 rounded-xl border border-border bg-surface shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs text-text-secondary font-medium">
                <span>Total Master SMP</span>
                <span class="p-1 rounded-md bg-blue-500/10 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </span>
            </div>
            <div class="mt-2 text-2xl font-bold font-mono text-text-main">
                {{ number_format($totalMaster, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-text-muted mt-0.5">Database referensi resmi</div>
        </div>

        {{-- Cakupan Provinsi --}}
        <div class="p-4 rounded-xl border border-border bg-surface shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs text-text-secondary font-medium">
                <span>Provinsi Terdata</span>
                <span class="p-1 rounded-md bg-purple-500/10 text-purple-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-2 text-2xl font-bold font-mono text-text-main">
                {{ $totalProvinsi }} <span class="text-xs font-normal text-text-muted">Provinsi</span>
            </div>
            <div class="text-[11px] text-text-muted mt-0.5">Cakupan wilayah impor</div>
        </div>

        {{-- Sudah Terjaring --}}
        <div class="p-4 rounded-xl border border-border bg-surface shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs text-text-secondary font-medium">
                <span>Sudah Terjaring SPMB</span>
                <span class="p-1 rounded-md bg-emerald-500/10 text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-2 text-2xl font-bold font-mono text-emerald-600 dark:text-emerald-400">
                {{ number_format($totalTerjaring, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-text-muted mt-0.5">Memiliki riwayat siswa</div>
        </div>

        {{-- Peluang Outreach --}}
        <div class="p-4 rounded-xl border border-border bg-surface shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs text-text-secondary font-medium">
                <span>Peluang Outreach Baru</span>
                <span class="p-1 rounded-md bg-amber-500/10 text-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </span>
            </div>
            <div class="mt-2 text-2xl font-bold font-mono text-amber-600 dark:text-amber-400">
                {{ number_format($totalPeluang, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-text-muted mt-0.5">Belum pernah terjaring</div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="p-4 rounded-2xl border border-border bg-surface shadow-2xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            {{-- Search Box --}}
            <div class="sm:col-span-2 relative">
                <label class="block text-[11px] font-semibold text-text-secondary mb-1">Cari NPSN / Nama Sekolah</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik nama atau NPSN..." class="w-full text-xs py-1.5 pl-8 pr-7 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary focus:border-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-text-muted absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    @if ($search)
                        <button type="button" wire:click="$set('search', '')" class="absolute right-2 top-2 text-text-muted hover:text-text-main text-xs">&times;</button>
                    @endif
                </div>
            </div>

            {{-- Filter Provinsi --}}
            <div>
                <label class="block text-[11px] font-semibold text-text-secondary mb-1">Provinsi</label>
                <select wire:model.live="filterProvinsi" class="w-full text-xs py-1.5 px-2 rounded-lg border border-border bg-surface-subtle text-text-main">
                    <option value="">Semua Provinsi</option>
                    @foreach ($provinsiList as $prov)
                        <option value="{{ $prov }}">{{ $prov }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Kabupaten / Kota --}}
            <div>
                <label class="block text-[11px] font-semibold text-text-secondary mb-1">Kabupaten / Kota</label>
                <select wire:model.live="filterKabupaten" class="w-full text-xs py-1.5 px-2 rounded-lg border border-border bg-surface-subtle text-text-main">
                    <option value="">Semua Kab/Kota</option>
                    @foreach ($kabupatenList as $kab)
                        <option value="{{ $kab }}">{{ $kab }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Kecamatan --}}
            <div>
                <label class="block text-[11px] font-semibold text-text-secondary mb-1">Kecamatan</label>
                <select wire:model.live="filterKecamatan" class="w-full text-xs py-1.5 px-2 rounded-lg border border-border bg-surface-subtle text-text-main">
                    <option value="">Semua Kecamatan</option>
                    @foreach ($kecamatanList as $kec)
                        <option value="{{ $kec }}">{{ $kec }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status Penetrasi SPMB --}}
            <div>
                <label class="block text-[11px] font-semibold text-text-secondary mb-1">Status Penetrasi SPMB</label>
                <select wire:model.live="filterPenetrasi" class="w-full text-xs py-1.5 px-2 rounded-lg border border-border bg-surface-subtle text-text-main">
                    <option value="">Semua Status</option>
                    <option value="terjaring">Sudah Terjaring (Ada Siswa)</option>
                    <option value="belum">Belum Terjaring (Peluang Baru)</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-between pt-1 text-xs border-t border-border/50">
            <div class="flex items-center gap-3">
                {{-- Filter Bentuk --}}
                <div class="flex items-center gap-1.5">
                    <span class="text-text-muted text-[11px]">Bentuk:</span>
                    <select wire:model.live="filterBentuk" class="text-xs py-0.5 px-2 rounded border border-border bg-surface text-text-main">
                        <option value="">Semua Bentuk</option>
                        @foreach ($bentukList as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Status Sekolah --}}
                <div class="flex items-center gap-1.5">
                    <span class="text-text-muted text-[11px]">Status:</span>
                    <select wire:model.live="filterStatus" class="text-xs py-0.5 px-2 rounded border border-border bg-surface text-text-main">
                        <option value="">Semua</option>
                        <option value="Negeri">Negeri</option>
                        <option value="Swasta">Swasta</option>
                    </select>
                </div>
            </div>

            @if ($search || $filterProvinsi || $filterKabupaten || $filterKecamatan || $filterStatus || $filterBentuk || $filterPenetrasi)
                <button type="button" wire:click="resetFilters" class="text-xs text-rose-600 hover:text-rose-700 font-semibold underline flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Reset Filter</span>
                </button>
            @endif
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="rounded-2xl border border-border bg-surface shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle border-b border-border text-text-secondary uppercase text-[10px] tracking-wider font-semibold">
                    <tr>
                        <th class="py-3 px-3 text-center w-12">No</th>
                        <th class="py-3 px-3 text-center w-28">NPSN</th>
                        <th class="py-3 px-4">Nama Sekolah & Bentuk</th>
                        <th class="py-3 px-3 text-center w-24">Status</th>
                        <th class="py-3 px-4">Wilayah (Kec / Kab / Prov)</th>
                        <th class="py-3 px-3 text-center w-36">Status SPMB</th>
                        <th class="py-3 px-3 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/60">
                    @forelse ($paginated as $idx => $item)
                        @php
                            $rowNum = $paginated->firstItem() + $idx;
                            $hasSekolahAsal = $item->sekolahAsal !== null;
                        @endphp
                        <tr class="hover:bg-surface-hover/70 transition-colors">
                            <td class="py-3 px-3 text-center text-text-muted font-mono">{{ $rowNum }}</td>
                            
                            {{-- NPSN --}}
                            <td class="py-3 px-3 text-center">
                                <span class="font-mono text-xs font-semibold px-2 py-0.5 rounded bg-surface-subtle border border-border text-text-main">
                                    {{ $item->npsn }}
                                </span>
                            </td>

                            {{-- Nama Sekolah & Bentuk --}}
                            <td class="py-3 px-4">
                                <div class="font-bold text-text-main text-sm flex items-center gap-1.5">
                                    <span>{{ $item->nama }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-0.5 text-[11px] text-text-muted">
                                    <span class="font-medium text-text-secondary">{{ $item->bentuk ?? 'SMP' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $item->jenjang ?? 'Pendidikan Dasar' }}</span>
                                    @if ($item->kelurahan)
                                        <span>&bull;</span>
                                        <span>Desa/Kel. {{ $item->kelurahan }}</span>
                                    @endif
                                </div>
                                @if ($item->alamat)
                                    <div class="text-[11px] text-text-muted/80 truncate max-w-md mt-0.5" title="{{ $item->alamat }}">
                                        {{ $item->alamat }}
                                    </div>
                                @endif
                            </td>

                            {{-- Status Negeri/Swasta --}}
                            <td class="py-3 px-3 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $item->status === 'Negeri' ? 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-slate-100 text-slate-800 border-slate-300 dark:bg-slate-800 dark:text-slate-300' }}">
                                    {{ $item->status ?? 'Swasta' }}
                                </span>
                            </td>

                            {{-- Wilayah --}}
                            <td class="py-3 px-4">
                                <div class="font-medium text-text-main text-xs">
                                    {{ $item->kecamatan ? 'Kec. ' . $item->kecamatan : '-' }}
                                </div>
                                <div class="text-[11px] text-text-secondary mt-0.5">
                                    {{ $item->kabupaten }} &bull; {{ $item->provinsi }}
                                </div>
                            </td>

                            {{-- Status Penetrasi SPMB --}}
                            <td class="py-3 px-3 text-center">
                                @if ($hasSekolahAsal)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Terjaring SPMB</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-surface-subtle text-text-muted border border-border">
                                        <span>Belum Terjaring</span>
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3 px-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    {{-- Cek Referensi Kemendikbud --}}
                                    <a href="{{ $item->referensi_kemendikbud_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary !p-1.5" title="Buka Profil Resmi Kemendikbud">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>

                                    {{-- Jadikan Asal SMP / Hubungkan jika belum ada --}}
                                    @can('analitik_spmb.master_smp.kelola')
                                        @if (! $hasSekolahAsal)
                                            <button type="button" wire:click="jadikanSekolahAsal({{ $item->id }})" class="btn btn-secondary !p-1.5 text-emerald-600 hover:text-emerald-700" title="Jadikan Master Asal SMP Lokal">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-text-muted">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-text-muted/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <p class="font-semibold text-text-main text-sm">Belum Ada Data Master SMP</p>
                                    <p class="text-xs text-text-secondary">Silakan impor file CSV atau Excel Master SMP per provinsi menggunakan tombol Import di atas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($paginated->hasPages())
            <div class="p-3.5 border-t border-border bg-surface-subtle">
                {{ $paginated->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL IMPORT EXCEL / CSV --}}
    @if ($showImportModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity z-10 cursor-pointer" wire:click="closeImportModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 relative z-20 pointer-events-none">
                <div class="relative transform overflow-hidden rounded-2xl bg-surface border border-border text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg text-text-main pointer-events-auto" @click.stop>
                    {{-- Header --}}
                    <div class="p-4 sm:p-5 border-b border-border bg-surface-subtle flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-primary/10 text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-base font-bold text-text-main" id="modal-title">Import Master Data SMP</h3>
                                <p class="text-xs text-text-secondary">Impor data referensi sekolah menengah dari file Excel / CSV.</p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeImportModal" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                    </div>

                    {{-- Form Body --}}
                    <form wire:submit="processImport">
                        <div class="p-5 space-y-4">
                            {{-- Input Nama Provinsi --}}
                            <div>
                                <label class="block text-xs font-semibold text-text-main mb-1">Nama Provinsi <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model="inputProvinsi" placeholder="Contoh: Jawa Barat, Jawa Tengah, DKI Jakarta" class="w-full text-xs py-2 px-3 rounded-lg border border-border bg-surface-subtle text-text-main focus:ring-1 focus:ring-primary focus:border-primary">
                                <p class="text-[11px] text-text-muted mt-1">Data yang diimpor akan dilabeli dengan provinsi ini.</p>
                                @error('inputProvinsi') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                            </div>

                            {{-- File Upload --}}
                            <div>
                                <label class="block text-xs font-semibold text-text-main mb-1">File Excel / CSV <span class="text-rose-500">*</span></label>
                                <input type="file" wire:model="importFile" accept=".csv,.xlsx,.xls,text/csv" class="w-full text-xs text-text-secondary file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90 border border-border rounded-lg bg-surface-subtle cursor-pointer">
                                <div wire:loading wire:target="importFile" class="text-xs text-primary mt-1 font-medium flex items-center gap-1">
                                    <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                    <span>Mengunggah file...</span>
                                </div>
                                @error('importFile') <span class="text-rose-500 text-[11px] block mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Ringkasan Format & Unduh Template --}}
                            <div class="p-3 rounded-xl bg-surface-subtle border border-border/80 text-xs space-y-2">
                                <div class="font-semibold text-text-main flex items-center justify-between">
                                    <span>Format Kolom yang Diharapkan:</span>
                                </div>
                                <p class="text-[11px] text-text-muted font-mono leading-relaxed">
                                    NPSN, Nama, Bentuk, Status, Jenjang, Kabupaten, Kecamatan, Kelurahan, Alamat
                                </p>
                                <div class="flex items-center gap-2 pt-1 border-t border-border/40">
                                    <span class="text-[11px] text-text-secondary">Unduh Template:</span>
                                    <button type="button" wire:click="downloadTemplate('csv')" class="text-xs text-primary hover:underline font-semibold flex items-center gap-1">
                                        <span>Template CSV</span>
                                    </button>
                                    <span class="text-text-muted">&bull;</span>
                                    <button type="button" wire:click="downloadTemplate('xlsx')" class="text-xs text-primary hover:underline font-semibold flex items-center gap-1">
                                        <span>Template Excel (.xlsx)</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Hasil Import Summary jika selesai --}}
                            @if ($importResult)
                                <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 text-xs text-emerald-900 dark:text-emerald-100 space-y-1">
                                    <div class="font-bold flex items-center gap-1.5 text-emerald-700 dark:text-emerald-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Ringkasan Impor Berhasil:</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-1 text-[11px] pt-1">
                                        <div>Total Baris: <strong class="font-mono">{{ $importResult['total_read'] }}</strong></div>
                                        <div>Data Ditambahkan: <strong class="font-mono text-emerald-600">{{ $importResult['inserted'] }}</strong></div>
                                        <div>Data Diperbarui: <strong class="font-mono text-blue-600">{{ $importResult['updated'] }}</strong></div>
                                        <div>Baris Dilewati: <strong class="font-mono text-amber-600">{{ $importResult['skipped'] }}</strong></div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Footer Actions --}}
                        <div class="p-3.5 border-t border-border bg-surface-subtle flex items-center justify-end gap-2">
                            <button type="button" wire:click="closeImportModal" class="btn btn-secondary !py-1.5 !px-3 text-xs">
                                Tutup
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary !py-1.5 !px-4 text-xs gap-1.5">
                                <span wire:loading.remove wire:target="processImport">Mulai Import</span>
                                <span wire:loading wire:target="processImport" class="flex items-center gap-1">
                                    <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                    <span>Memproses...</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
