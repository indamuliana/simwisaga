<div class="space-y-6">
    {{-- Header & Navigation --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs text-text-muted mb-1">
                <a href="{{ route('analitik-spmb.asal-smp') }}" class="hover:text-primary transition-colors">Analitik SPMB</a>
                <span>&bull;</span>
                <span class="text-text-main font-semibold">Perbaikan Data Asal SMP</span>
            </nav>
            <h1 class="text-2xl font-bold tracking-tight text-text-main flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-primary/10 text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </span>
                <span>Perbaikan & Normalisasi Data Asal SMP</span>
            </h1>
            <p class="text-xs text-text-secondary mt-1">
                Bersihkan variasi teks nama sekolah asal pada biodata siswa, satukan entitas duplikat, dan perbarui data secara otomatis.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('analitik-spmb.asal-smp') }}" class="btn btn-secondary !py-2 !px-3 text-xs gap-1.5 shadow-2xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Analitik SMP</span>
            </a>

            @can('analitik_spmb.perbaikan_smp.kelola')
                @if (count($selectedSchools) > 0)
                    <button type="button" wire:click="openMergeFromSelection" class="btn btn-primary !py-2 !px-3.5 text-xs gap-1.5 shadow-sm animate-pulse">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        <span>Gabungkan ({{ count($selectedSchools) }}) Terpilih</span>
                    </button>
                @endif
            @endcan
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if (session()->has('success'))
        <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 font-bold">&times;</button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 font-bold">&times;</button>
        </div>
    @endif

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="card p-4 flex flex-col justify-between">
            <span class="text-xs font-semibold text-text-muted">Total Variasi Teks</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-black text-text-main font-mono">{{ number_format($totalVariasi) }}</span>
                <span class="text-[11px] text-text-muted">varian</span>
            </div>
        </div>

        <div class="card p-4 flex flex-col justify-between">
            <span class="text-xs font-semibold text-text-muted">Total Siswa Terdata</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-black text-primary font-mono">{{ number_format($totalSiswaTerdata) }}</span>
                <span class="text-[11px] text-text-muted">siswa</span>
            </div>
        </div>

        <div class="card p-4 flex flex-col justify-between">
            <span class="text-xs font-semibold text-text-muted">Belum Terpetakan Master</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-black font-mono {{ $totalUnmapped > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                    {{ number_format($totalUnmapped) }}
                </span>
                <span class="text-[11px] text-text-muted">varian</span>
            </div>
        </div>

        <div class="card p-4 flex flex-col justify-between">
            <span class="text-xs font-semibold text-text-muted">Rekomendasi Duplikat</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-black font-mono {{ count($suggestions) > 0 ? 'text-blue-600' : 'text-text-muted' }}">
                    {{ count($suggestions) }}
                </span>
                <span class="text-[11px] text-text-muted">kelompok</span>
            </div>
        </div>
    </div>

    {{-- Smart Duplicate Suggestions --}}
    @if (count($suggestions) > 0)
        <div class="card p-5 border-blue-200 dark:border-blue-900/50 bg-blue-50/30 dark:bg-blue-950/20 space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-text-main">Rekomendasi Duplikat Cerdas Ditemukan</h3>
                        <p class="text-xs text-text-secondary">Sistem mendeteksi kelompok nama sekolah yang sangat mirip dan berpotensi merupakan sekolah yang sama.</p>
                    </div>
                </div>
                <button type="button" wire:click="refreshSuggestions" class="text-xs text-primary hover:underline font-semibold flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Pindai Ulang</span>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 pt-1">
                @foreach ($suggestions as $idx => $cluster)
                    <div class="p-3.5 rounded-xl bg-surface border border-border flex flex-col justify-between gap-3 shadow-2xs hover:border-primary/50 transition-all">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-[11px] text-text-muted">
                                <span class="font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400">Potensi Duplikat #{{ $idx + 1 }}</span>
                                <span class="font-mono font-bold">{{ $cluster['total_siswa'] }} Siswa</span>
                            </div>

                            <div class="space-y-1.5">
                                @foreach ($cluster['items'] as $cItem)
                                    <div class="flex items-center justify-between p-1.5 rounded bg-surface-subtle text-xs border border-border/50">
                                        <span class="font-medium text-text-main truncate mr-2" title="{{ $cItem['raw_name'] }}">{{ $cItem['raw_name'] }}</span>
                                        <span class="text-[10px] px-1.5 py-0.2 rounded-full bg-surface text-text-muted border border-border shrink-0 font-mono">
                                            {{ $cItem['total_siswa'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="text-[11px] text-text-secondary pt-1">
                                Saran Nama Target: <strong class="text-text-main">{{ $cluster['suggested_target'] }}</strong>
                            </div>
                        </div>

                        @can('analitik_spmb.perbaikan_smp.kelola')
                            <button type="button" wire:click="openMergeFromSuggestion({{ $idx }})" class="btn btn-secondary !py-1.5 !px-3 text-xs w-full justify-center gap-1.5 hover:bg-primary hover:text-white transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                <span>Tinjau & Satukan Sekolah Ini</span>
                            </button>
                        @endcan
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Main Filter & Data Table --}}
    <div class="card p-4 space-y-4">
        {{-- Toolbar Filter --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex flex-1 items-center gap-2 max-w-md">
                <div class="relative w-full">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari teks sekolah asal atau master..." class="input !py-1.5 !px-3 text-xs w-full pl-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-text-muted absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <select wire:model.live="filterMapping" class="input !py-1.5 !px-2.5 text-xs">
                    <option value="">Semua Status Pemetaan</option>
                    <option value="unmapped">Belum Terpetakan Master</option>
                    <option value="mapped">Sudah Terpetakan Master</option>
                </select>

                <select wire:model.live="perPage" class="input !py-1.5 !px-2.5 text-xs">
                    <option value="20">20 per hal</option>
                    <option value="50">50 per hal</option>
                    <option value="100">100 per hal</option>
                </select>
            </div>
        </div>

        {{-- Selection Counter --}}
        @if (count($selectedSchools) > 0)
            <div class="p-2.5 rounded-lg bg-primary/10 border border-primary/20 flex items-center justify-between text-xs text-primary">
                <span class="font-medium">
                    <strong>{{ count($selectedSchools) }}</strong> variasi nama sekolah dipilih.
                </span>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="$set('selectedSchools', [])" class="underline text-text-secondary hover:text-text-main text-xs">
                        Batal Pilih
                    </button>
                    @can('analitik_spmb.perbaikan_smp.kelola')
                        <button type="button" wire:click="openMergeFromSelection" class="btn btn-primary !py-1 !px-3 text-xs gap-1 shadow-2xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            <span>Gabungkan Sekarang</span>
                        </button>
                    @endcan
                </div>
            </div>
        @endif

        {{-- Table --}}
        <div class="overflow-x-auto rounded-lg border border-border">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-surface-subtle text-text-muted uppercase font-semibold border-b border-border">
                    <tr>
                        <th class="py-3 px-3 text-center w-10">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-border text-primary focus:ring-primary/40">
                        </th>
                        <th class="py-3 px-2 text-center w-10">No</th>
                        <th class="py-3 px-3 text-center min-w-[110px]">NPSN</th>
                        <th class="py-3 px-4 min-w-[240px]">Teks Sekolah Asal di Biodata Siswa</th>
                        <th class="py-3 px-4 min-w-[200px]">Terpetakan ke Master (Analitik)</th>
                        <th class="py-3 px-3 text-center w-28">Jumlah Siswa</th>
                        <th class="py-3 px-3 min-w-[130px]">Catatan Master</th>
                        <th class="py-3 px-3 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-text-main">
                    @forelse ($paginated as $index => $item)
                        @php
                            $rowNum = ($paginated->currentPage() - 1) * $paginated->perPage() + $index + 1;
                            $isSelected = in_array($item->raw_name, $selectedSchools);
                        @endphp
                        <tr class="hover:bg-surface-hover/70 transition-colors {{ $isSelected ? 'bg-primary/5' : '' }}">
                            <td class="py-3 px-3 text-center">
                                <input type="checkbox" wire:model.live="selectedSchools" value="{{ $item->raw_name }}" class="rounded border-border text-primary focus:ring-primary/40">
                            </td>
                            <td class="py-3 px-2 text-center text-text-muted font-mono">{{ $rowNum }}</td>
                            
                            {{-- NPSN Master --}}
                            <td class="py-3 px-3 text-center">
                                @if ($item->is_mapped && $item->master_npsn)
                                    <span class="font-mono text-xs font-semibold text-text-main bg-surface-subtle px-2 py-0.5 rounded border border-border">
                                        {{ $item->master_npsn }}
                                    </span>
                                @elseif ($item->is_mapped)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-300 dark:bg-amber-950/40 dark:text-amber-300" title="NPSN master belum diisi">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <span>Belum diisi</span>
                                    </span>
                                @else
                                    <span class="text-xs text-text-muted/50">-</span>
                                @endif
                            </td>

                            {{-- Teks Mentah --}}
                            <td class="py-3 px-4">
                                <div class="font-bold text-text-main text-sm">{{ $item->raw_name }}</div>
                            </td>

                            {{-- Status Pemetaan Master --}}
                            <td class="py-3 px-4">
                                @if ($item->is_mapped && $item->master_name)
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-semibold text-text-main">{{ $item->master_name }}</span>
                                        <span class="text-[10px] px-1.5 py-0.2 rounded-full border {{ match($item->master_tier) { 'Utama' => 'bg-amber-100 text-amber-800 border-amber-300', 'Potensial' => 'bg-blue-100 text-blue-800 border-blue-300', default => 'bg-slate-100 text-slate-700 border-slate-300'} }}">
                                            {{ $item->master_tier }}
                                        </span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <span>Belum ada master</span>
                                    </span>
                                @endif
                            </td>

                            {{-- Jumlah Siswa --}}
                            <td class="py-3 px-3 text-center">
                                <span class="font-bold font-mono text-sm text-text-main">{{ $item->total_siswa }}</span>
                                <span class="text-[11px] text-text-muted block">({{ $item->total_aktif }} aktif)</span>
                            </td>

                            {{-- Catatan Master --}}
                            <td class="py-3 px-3 min-w-[130px] max-w-[200px]">
                                @if ($item->is_mapped && !empty($item->master_catatan))
                                    <span class="text-xs text-text-secondary truncate block" title="{{ $item->master_catatan }}">
                                        {{ \Illuminate\Support\Str::limit($item->master_catatan, 35) }}
                                    </span>
                                @else
                                    <span class="text-xs text-text-muted/60">-</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3 px-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    {{-- Lihat Siswa --}}
                                    <button type="button" wire:click="openDrawer('{{ addslashes($item->raw_name) }}')" class="btn btn-secondary !p-1.5" title="Lihat Siswa">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    @can('analitik_spmb.perbaikan_smp.kelola')
                                        {{-- Edit Master (NPSN & Catatan) jika sudah terpetakan --}}
                                        @if ($item->is_mapped && $item->master_id)
                                            <button type="button" wire:click="openEditMasterModal({{ $item->master_id }})" class="btn btn-secondary !p-1.5 text-amber-600 hover:text-amber-700" title="Edit Master (NPSN & Catatan)">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- Quick Rename Teks Mentah --}}
                                        <button type="button" wire:click="openRenameModal('{{ addslashes($item->raw_name) }}')" class="btn btn-secondary !p-1.5" title="Ganti Nama Mentah">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>

                                        {{-- Merge Single --}}
                                        <button type="button" wire:click="$set('selectedSchools', ['{{ addslashes($item->raw_name) }}']); openMergeFromSelection();" class="btn btn-secondary !p-1.5" title="Gabungkan Sekolah Ini">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                            </svg>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-text-muted">
                                Tidak ada data sekolah asal yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($paginated->hasPages())
            <div class="pt-2">
                {{ $paginated->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL PENGGABUNGAN SEKOLAH (MERGE MODAL) --}}
    @if ($showMergeModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity z-10 cursor-pointer" wire:click="closeMergeModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 relative z-20 pointer-events-none">
                <div class="relative transform overflow-hidden rounded-2xl bg-surface border border-border text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl text-text-main pointer-events-auto" @click.stop>
                    <form wire:submit.prevent="executeMerge">
                        {{-- Header --}}
                        <div class="p-4 sm:p-5 border-b border-border bg-surface-subtle flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="p-1.5 rounded-lg bg-primary/10 text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                </span>
                                <div>
                                    <h3 class="text-base font-bold text-text-main">Penggabungan Sekolah Asal Siswa</h3>
                                    <p class="text-xs text-text-secondary">Satukan variasi nama asal menjadi satu nama sekolah resmi yang seragam.</p>
                                </div>
                            </div>
                            <button type="button" wire:click="closeMergeModal" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                        </div>

                        {{-- Body --}}
                        <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-xs">
                            {{-- Sumber Nama yang Digabung --}}
                            <div>
                                <label class="label font-semibold text-text-secondary mb-1">
                                    Sekolah Sumber yang Digabung ({{ count($mergeSourceNames) }} variasi):
                                </label>
                                <div class="flex flex-wrap gap-1.5 p-2.5 rounded-lg bg-surface-subtle border border-border max-h-32 overflow-y-auto">
                                    @foreach ($mergeSourceNames as $src)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-surface border border-border text-text-main font-semibold shadow-2xs">
                                            <span>{{ $src }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Pilihan Target Nama Sekolah --}}
                            <div class="space-y-3 pt-2 border-t border-border">
                                <div>
                                    <label class="label font-semibold">Pilih dari Master Sekolah yang Sudah Ada (Opsional):</label>
                                    <select wire:model.live="mergeTargetMasterId" class="input w-full">
                                        <option value="">-- Ketik Nama Baru di Bawah Ini Atau Pilih Master --</option>
                                        @foreach ($masterSekolahs as $ms)
                                            <option value="{{ $ms->id }}">{{ $ms->nama_resmi }} (Tier: {{ $ms->tier }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="label font-semibold">Nama Resmi Sekolah Tujuan (Target) <span class="text-rose-500">*</span></label>
                                    <input type="text" wire:model="mergeTargetName" placeholder="Contoh: SMP Baitul Hikmah" class="input w-full font-bold">
                                    @error('mergeTargetName') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                                    <p class="text-[11px] text-text-muted mt-1">Nama ini akan menjadi nama rujukan resmi bagi seluruh siswa dan analitik SPMB.</p>
                                </div>
                            </div>

                            {{-- Opsi Checklist Perilaku Update --}}
                            <div class="p-3.5 rounded-xl bg-blue-50/50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/40 space-y-2">
                                <span class="font-bold text-text-main block">Pilihan Tindakan Data:</span>
                                
                                <label class="flex items-start gap-2.5 cursor-pointer">
                                    <input type="checkbox" wire:model="optUpdateSiswa" class="rounded border-border text-primary focus:ring-primary/40 mt-0.5">
                                    <div>
                                        <strong class="text-text-main">Perbarui data biodata siswa secara otomatis</strong>
                                        <p class="text-[11px] text-text-secondary">Nilai kolom <code class="font-mono text-primary font-bold">sekolah_asal</code> pada tabel siswa akan langsung diubah menjadi nama sekolah target.</p>
                                    </div>
                                </label>

                                <label class="flex items-start gap-2.5 cursor-pointer pt-1">
                                    <input type="checkbox" wire:model="optCreateAlias" class="rounded border-border text-primary focus:ring-primary/40 mt-0.5">
                                    <div>
                                        <strong class="text-text-main">Simpan variasi teks lama sebagai Alias Analitik</strong>
                                        <p class="text-[11px] text-text-secondary">Jika di masa depan ada data Dapodik baru yang membawa teks lama, sistem akan otomatis mengenalinya sebagai sekolah target ini.</p>
                                    </div>
                                </label>
                            </div>

                            {{-- Preview Siswa Terdampak --}}
                            <div class="space-y-2 pt-2 border-t border-border">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-text-main">Daftar Siswa yang Terdampak ({{ $previewStudents->count() }} siswa):</span>
                                    <span class="text-text-muted text-[11px]">Pratinjau sebelum eksekusi</span>
                                </div>

                                <div class="border border-border rounded-lg max-h-48 overflow-y-auto">
                                    <table class="w-full text-left text-xs border-collapse">
                                        <thead class="bg-surface-subtle text-text-muted font-semibold border-b border-border">
                                            <tr>
                                                <th class="py-2 px-2 text-center w-8">No</th>
                                                <th class="py-2 px-3">Nama Siswa</th>
                                                <th class="py-2 px-2 text-center">Rombel</th>
                                                <th class="py-2 px-3">Teks Asal Saat Ini</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border text-text-main">
                                            @forelse ($previewStudents as $pIdx => $ps)
                                                <tr class="hover:bg-surface-hover/70">
                                                    <td class="py-1.5 px-2 text-center text-text-muted font-mono">{{ $pIdx + 1 }}</td>
                                                    <td class="py-1.5 px-3 font-semibold">{{ $ps->nama }}</td>
                                                    <td class="py-1.5 px-2 text-center">{{ $ps->rombel?->nama ?: '-' }}</td>
                                                    <td class="py-1.5 px-3 text-text-secondary truncate max-w-[160px]">{{ $ps->sekolah_asal }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="py-4 text-center text-text-muted">Tidak ada siswa terdampak.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="p-4 border-t border-border bg-surface-subtle flex items-center justify-between">
                            <span class="text-xs text-text-secondary">
                                Terdampak: <strong>{{ $previewStudents->count() }}</strong> siswa
                            </span>
                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="closeMergeModal" class="btn btn-secondary !py-1.5 !px-3 text-xs">
                                    Batal
                                </button>
                                <button type="submit" class="btn btn-primary !py-1.5 !px-4 text-xs">
                                    Konfirmasi & Gabungkan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL QUICK EDIT MASTER SEKOLAH (NPSN & CATATAN) --}}
    @if ($showEditMasterModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-edit-master-title" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity z-10 cursor-pointer" wire:click="closeEditMasterModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 relative z-20 pointer-events-none">
                <div class="relative transform overflow-hidden rounded-2xl bg-surface border border-border text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md text-text-main pointer-events-auto" @click.stop>
                    <form wire:submit.prevent="saveEditMaster">
                        {{-- Header --}}
                        <div class="p-4 sm:p-5 border-b border-border bg-surface-subtle flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="p-1.5 rounded-lg bg-amber-500/10 text-amber-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-text-main" id="modal-edit-master-title">Perbarui Master Sekolah Asal</h3>
                                    <p class="text-xs text-text-secondary">Lengkapi nomor unik NPSN dan catatan perbaikan.</p>
                                </div>
                            </div>
                            <button type="button" wire:click="closeEditMasterModal" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                        </div>

                        {{-- Body --}}
                        <div class="p-4 space-y-3.5 text-xs">
                            <div>
                                <label class="label font-semibold text-text-muted">Nama Resmi Master:</label>
                                <div class="p-2 rounded bg-surface-subtle border border-border font-bold text-text-main flex items-center justify-between">
                                    <span>{{ $editMasterNamaResmi }}</span>
                                    <span class="text-[10px] px-1.5 py-0.2 rounded border bg-surface text-text-muted">
                                        {{ $editMasterJenis }} &bull; {{ $editMasterTier }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="label font-semibold">Nomor Pokok Sekolah Nasional (NPSN)</label>
                                <input type="text" wire:model="editMasterNpsn" placeholder="Contoh: 20209912" class="input w-full font-mono text-sm">
                                <span class="text-[11px] text-text-muted block mt-0.5">8 digit nomor unik resmi Kemendikbudristek / Kemenag.</span>
                                @error('editMasterNpsn') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="label font-semibold">Catatan Perbaikan / Keterangan</label>
                                <textarea wire:model="editMasterCatatan" rows="3" placeholder="Contoh: Telah diverifikasi dengan Dapodik; Sekolah induk merger SMPN 2..." class="input w-full text-xs"></textarea>
                                @error('editMasterCatatan') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="p-3.5 border-t border-border bg-surface-subtle flex items-center justify-end gap-2">
                            <button type="button" wire:click="closeEditMasterModal" class="btn btn-secondary !py-1.5 !px-3 text-xs">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-primary !py-1.5 !px-4 text-xs">
                                Simpan Data Master
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL QUICK RENAME --}}
    @if ($showRenameModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity z-10 cursor-pointer" wire:click="closeRenameModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 relative z-20 pointer-events-none">
                <div class="relative transform overflow-hidden rounded-2xl bg-surface border border-border text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md text-text-main pointer-events-auto" @click.stop>
                    <form wire:submit.prevent="executeRename">
                        {{-- Header --}}
                        <div class="p-4 border-b border-border bg-surface-subtle flex items-center justify-between">
                            <h3 class="text-sm font-bold text-text-main flex items-center gap-2">
                                <span>✏️ Ganti Nama Sekolah Asal</span>
                            </h3>
                            <button type="button" wire:click="closeRenameModal" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                        </div>

                        {{-- Body --}}
                        <div class="p-4 space-y-3 text-xs">
                            <div>
                                <label class="label font-semibold text-text-muted">Nama Saat Ini (Lama):</label>
                                <div class="p-2 rounded bg-surface-subtle border border-border font-mono text-text-main">
                                    {{ $renameOldName }}
                                </div>
                            </div>

                            <div>
                                <label class="label font-semibold">Nama Baru yang Benar <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model="renameNewName" class="input w-full font-bold">
                                @error('renameNewName') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="p-3 rounded-lg bg-surface-subtle border border-border space-y-2">
                                <label class="flex items-start gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="renameOptUpdateSiswa" class="rounded border-border text-primary focus:ring-primary/40 mt-0.5">
                                    <span>Ubah otomatis pada seluruh biodata siswa yang memakai nama ini</span>
                                </label>
                                <label class="flex items-start gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="renameOptCreateAlias" class="rounded border-border text-primary focus:ring-primary/40 mt-0.5">
                                    <span>Simpan nama lama sebagai Alias pemetaan Analitik SPMB</span>
                                </label>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="p-3.5 border-t border-border bg-surface-subtle flex items-center justify-end gap-2">
                            <button type="button" wire:click="closeRenameModal" class="btn btn-secondary !py-1.5 !px-3 text-xs">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-primary !py-1.5 !px-4 text-xs">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- SLIDE-OVER DRAWER DAFTAR SISWA --}}
    @if ($showDrawer && $drawerSchoolName)
        <div class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity z-10 cursor-pointer" wire:click="closeDrawer"></div>

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10 z-20 pointer-events-none">
                <div class="w-screen max-w-xl bg-surface shadow-2xl flex flex-col border-l border-border pointer-events-auto relative" @click.stop>
                    {{-- Header --}}
                    <div class="p-4 sm:p-5 border-b border-border bg-surface-subtle flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <span class="text-[10px] uppercase font-bold text-primary tracking-wider">Daftar Siswa Terdaftar</span>
                            <h2 class="text-base font-bold text-text-main mt-0.5 truncate" id="slide-over-title">
                                {{ $drawerSchoolName }}
                            </h2>
                            <p class="text-xs text-text-muted mt-0.5">
                                Total {{ $drawerStudents->count() }} siswa menggunakan teks sekolah asal ini
                            </p>
                        </div>
                        <button type="button" wire:click="closeDrawer" class="p-1.5 rounded-lg text-text-muted hover:text-text-main hover:bg-surface border border-border">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 overflow-y-auto p-4">
                        <div class="border border-border rounded-lg overflow-hidden">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead class="bg-surface-subtle text-text-muted uppercase font-semibold border-b border-border">
                                    <tr>
                                        <th class="py-2 px-2 text-center w-8">No</th>
                                        <th class="py-2 px-3">Nama Siswa / NISN</th>
                                        <th class="py-2 px-2 text-center">JK</th>
                                        <th class="py-2 px-2 text-center">Rombel</th>
                                        <th class="py-2 px-2 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border text-text-main">
                                    @forelse ($drawerStudents as $dIdx => $ds)
                                        <tr class="hover:bg-surface-hover/70 transition-colors">
                                            <td class="py-2 px-2 text-center text-text-muted font-mono">{{ $dIdx + 1 }}</td>
                                            <td class="py-2 px-3">
                                                <div class="font-bold text-text-main">{{ $ds->nama }}</div>
                                                <div class="text-[10px] text-text-muted font-mono">
                                                    NISN: {{ $ds->nisn ?: '-' }} &bull; NIS: {{ $ds->nipd ?: '-' }}
                                                </div>
                                            </td>
                                            <td class="py-2 px-2 text-center">
                                                <span class="px-1 py-0.2 rounded text-[10px] font-bold {{ $ds->jk === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-rose-50 text-rose-700' }}">
                                                    {{ $ds->jk }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-2 text-center font-semibold">
                                                {{ $ds->rombel?->nama ?: '-' }}
                                            </td>
                                            <td class="py-2 px-2 text-center">
                                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-medium {{ $ds->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                                    {{ ucfirst($ds->status ?: 'aktif') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-6 text-center text-text-muted">
                                                Tidak ada data siswa ditemukan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="p-3 border-t border-border bg-surface-subtle flex items-center justify-end">
                        <button type="button" wire:click="closeDrawer" class="btn btn-secondary !py-1.5 !px-3 text-xs">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
