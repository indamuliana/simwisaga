<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Custom Kelompok</h1>
            <p class="text-sm text-text-secondary">Pengelompokan siswa fleksibel (1 siswa dapat memiliki lebih dari 1 kelompok) untuk keperluan peminatan, remedial, bimbingan, atau projek.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if ($activeTab === 'kelompok')
                @can('akademik.custom_kelompok.tambah')
                <button wire:click="createKelompok" class="btn btn-primary inline-flex items-center gap-1.5 shadow-sm text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Buat Kelompok Baru</span>
                </button>
                @endcan
            @elseif ($activeTab === 'pengelompokan' && $selectedKelompok)
                @can('akademik.custom_kelompok.cetak')
                <button wire:click="openCetakModal" class="btn btn-secondary inline-flex items-center gap-1.5 shadow-xs text-xs">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Lembar Absen</span>
                </button>
                @endcan
                @can('akademik.custom_kelompok.tambah')
                <button wire:click="openImportModal" class="btn btn-secondary inline-flex items-center gap-1.5 shadow-xs text-xs">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Import Excel</span>
                </button>
                <button wire:click="openManualModal" class="btn btn-primary inline-flex items-center gap-1.5 shadow-sm text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>+ Tambah Siswa</span>
                </button>
                @endcan
            @endif
        </div>
    </div>

    <!-- Alert Status & Errors -->
    @if (session('status'))
        <div class="p-4 text-sm text-emerald-800 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 text-sm text-rose-800 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 rounded-xl border border-rose-200 dark:border-rose-800 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- 3 Navigation Tabs -->
    <div class="border-b border-border">
        <nav class="flex space-x-6" aria-label="Tabs">
            <!-- Tab 1 -->
            <button
                type="button"
                wire:click="switchTab('siswa')"
                class="py-3 px-1 text-sm font-semibold border-b-2 transition-all flex items-center gap-2 {{ $activeTab === 'siswa' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main hover:border-border' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Tab 1: Seluruh Siswa & Tag</span>
                <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $activeTab === 'siswa' ? 'bg-primary-soft text-primary font-bold' : 'bg-surface-hover text-text-muted' }}">
                    {{ $siswas->total() }}
                </span>
            </button>

            <!-- Tab 2 -->
            <button
                type="button"
                wire:click="switchTab('kelompok')"
                class="py-3 px-1 text-sm font-semibold border-b-2 transition-all flex items-center gap-2 {{ $activeTab === 'kelompok' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main hover:border-border' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Tab 2: Kelola Kelompok</span>
                <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $activeTab === 'kelompok' ? 'bg-primary-soft text-primary font-bold' : 'bg-surface-hover text-text-muted' }}">
                    {{ $kelompoks->count() }}
                </span>
            </button>

            <!-- Tab 3 -->
            <button
                type="button"
                wire:click="switchTab('pengelompokan')"
                class="py-3 px-1 text-sm font-semibold border-b-2 transition-all flex items-center gap-2 {{ $activeTab === 'pengelompokan' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main hover:border-border' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span>Tab 3: Pengelompokan Siswa</span>
                @if ($selectedKelompok)
                <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 font-semibold">
                    {{ $selectedKelompok->nama }} ({{ $selectedKelompok->siswas_count }})
                </span>
                @endif
            </button>
        </nav>
    </div>

    <!-- ======================================================== -->
    <!-- TAB 1: SELURUH SISWA (NIS, NAMA, ROMBEL, RAYON) DAN TAG  -->
    <!-- ======================================================== -->
    @if ($activeTab === 'siswa')
        <div class="space-y-4">
            <!-- Filter Bar Card -->
            <div class="card p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search -->
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Cari Siswa</label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="searchSiswa" placeholder="Nama, NIS, atau NISN..." class="w-full pl-9 text-xs rounded-lg border-border bg-surface text-text-main">
                        <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                <!-- Rombel -->
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Rombel</label>
                    <select wire:model.live="filterRombel" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                        <option value="">-- Semua Rombel --</option>
                        @foreach($rombels as $r)
                        <option value="{{ $r->id }}">{{ $r->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Rayon -->
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Rayon</label>
                    <select wire:model.live="filterRayon" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                        <option value="">-- Semua Rayon --</option>
                        @foreach($rayons as $ry)
                        <option value="{{ $ry->id }}">{{ $ry->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Kelompok -->
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Status / Kelompok</label>
                    <select wire:model.live="filterKelompok" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                        <option value="semua">Semua Siswa</option>
                        <option value="ada">Sudah Punya Kelompok</option>
                        <option value="belum">Belum Ada Kelompok</option>
                        <optgroup label="Spesifik Kelompok">
                            @foreach($allKelompoks as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
            </div>

            <!-- Toolbar Aksi Ekspor -->
            <div class="flex items-center justify-between">
                <div class="text-xs text-text-secondary">
                    Menampilkan <span class="font-bold text-text-main">{{ $siswas->firstItem() ?? 0 }}-{{ $siswas->lastItem() ?? 0 }}</span> dari <span class="font-bold text-text-main">{{ $siswas->total() }}</span> siswa aktif
                </div>
                @can('akademik.custom_kelompok.cetak')
                <button wire:click="exportSiswaExcel" class="btn btn-secondary inline-flex items-center gap-1.5 shadow-xs text-xs">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zM6 20V4h5v6h6v10H6zm2-8h2v2H8v-2zm0 3h2v2H8v-2zm3-3h5v2h-5v-2zm0 3h5v2h-5v-2z"/>
                    </svg>
                    <span>Export Siswa & Tag (Excel)</span>
                </button>
                @endcan
            </div>

            <!-- Tabel Siswa -->
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border text-left text-xs">
                        <thead class="bg-surface-hover/70 font-semibold text-text-secondary uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3">NIS / NIPD</th>
                                <th class="px-4 py-3">Nama Siswa</th>
                                <th class="px-4 py-3 text-center">JK</th>
                                <th class="px-4 py-3">Rombel</th>
                                <th class="px-4 py-3">Rayon</th>
                                <th class="px-4 py-3">Tag Kelompok Yang Diikuti</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border text-text-main">
                            @forelse($siswas as $idx => $siswa)
                            <tr class="hover:bg-surface-hover transition-colors">
                                <td class="px-4 py-3 text-center text-slate-400 font-mono">{{ $siswas->firstItem() + $idx }}</td>
                                <td class="px-4 py-3 font-mono font-medium text-slate-600 dark:text-slate-300">
                                    {{ $siswa->nipd ?: $siswa->nisn ?: '-' }}
                                </td>
                                <td class="px-4 py-3 font-semibold text-text-main">
                                    {{ $siswa->nama }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold {{ $siswa->jk === 'L' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400' : 'bg-pink-100 text-pink-700 dark:bg-pink-950/60 dark:text-pink-400' }}">
                                        {{ $siswa->jk }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $siswa->rombel?->nama ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $siswa->rayon?->nama ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse($siswa->customKelompoks as $k)
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium shadow-xs"
                                            style="background-color: {{ $k->warna }}18; color: {{ $k->warna }}; border: 1px solid {{ $k->warna }}40;">
                                            <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $k->warna }};"></span>
                                            <span>{{ $k->nama }}</span>
                                            @if($k->pivot->keterangan)
                                            <span class="opacity-75 text-[10px]">({{ $k->pivot->keterangan }})</span>
                                            @endif
                                        </span>
                                        @empty
                                        <span class="text-slate-400 italic text-[11px]">Belum ada kelompok</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($siswa->customKelompoks->isNotEmpty())
                                    <button
                                        type="button"
                                        wire:click="selectKelompok({{ $siswa->customKelompoks->first()->id }})"
                                        class="text-primary hover:underline text-[11px] font-medium"
                                        title="Buka di Tab Pengelompokan">
                                        Lihat Workspace &rarr;
                                    </button>
                                    @else
                                    <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                    <svg class="mx-auto h-8 w-8 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    <p class="font-medium">Tidak ada data siswa yang cocok dengan filter.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-border">
                    {{ $siswas->links() }}
                </div>
            </div>
        </div>
    @endif

    <!-- ======================================================== -->
    <!-- TAB 2: KELOLA KELOMPOK (MEMBUAT, KETERANGAN, SETTINGAN)   -->
    <!-- ======================================================== -->
    @if ($activeTab === 'kelompok')
        <div class="space-y-4">
            <!-- Filter & Search Kelompok -->
            <div class="card p-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Cari Kelompok</label>
                    <input type="text" wire:model.live.debounce.300ms="searchKelompok" placeholder="Nama, kode, atau keterangan..." class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Kategori</label>
                    <select wire:model.live="filterKategori" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                        <option value="">-- Semua Kategori --</option>
                        <option value="Akademik">Akademik</option>
                        <option value="Remedial">Remedial</option>
                        <option value="Minat/Bakat">Minat/Bakat</option>
                        <option value="Proyek / Lomba">Proyek / Lomba</option>
                        <option value="Bimbingan Konseling">Bimbingan Konseling</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Tahun Ajaran</label>
                    <select wire:model.live="filterTahunAjaran" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                        <option value="">-- Semua / Lintas Tahun --</option>
                        @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Table Master Kelompok -->
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border text-left text-xs">
                        <thead class="bg-surface-hover/70 font-semibold text-text-secondary uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3">Kelompok & Kode</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3">Tahun Ajaran</th>
                                <th class="px-4 py-3">Guru Pembimbing / PJ</th>
                                <th class="px-4 py-3 text-center">Anggota / Kuota</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border text-text-main">
                            @forelse($kelompoks as $idx => $k)
                            <tr class="hover:bg-surface-hover transition-colors">
                                <td class="px-4 py-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $k->warna }};"></span>
                                        <div>
                                            <div class="font-bold text-text-main hover:text-primary cursor-pointer" wire:click="selectKelompok({{ $k->id }})">
                                                {{ $k->nama }}
                                            </div>
                                            <div class="text-[11px] text-slate-400 flex items-center gap-2">
                                                @if($k->kode)
                                                <span class="font-mono uppercase font-semibold text-slate-500">{{ $k->kode }}</span>
                                                <span>&bull;</span>
                                                @endif
                                                <span>{{ Str::limit($k->keterangan ?: 'Tidak ada keterangan khusus', 40) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        {{ $k->kategori ?: 'Akademik' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $k->tahunAjaran?->nama ?? 'Umum / Semua Tahun' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $k->pembimbing?->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="font-bold text-sm text-text-main">{{ $k->siswas_count }}</span>
                                        <span class="text-[10px] text-slate-400">
                                            {{ $k->kapasitas ? "dari maks {$k->kapasitas}" : 'tanpa batas' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($k->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">Aktif</span>
                                    @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button
                                            type="button"
                                            wire:click="selectKelompok({{ $k->id }})"
                                            class="btn btn-secondary px-2 py-1 text-[11px] inline-flex items-center gap-1"
                                            title="Kelola Anggota Siswa (Tab 3)">
                                            <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                            <span>Anggota</span>
                                        </button>
                                        @can('akademik.custom_kelompok.cetak')
                                        <button
                                            type="button"
                                            wire:click="openCetakModal({{ $k->id }})"
                                            class="btn btn-secondary px-2 py-1 text-[11px] inline-flex items-center gap-1"
                                            title="Cetak Absensi PDF/XLS">
                                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            <span>Absen</span>
                                        </button>
                                        @endcan
                                        @can('akademik.custom_kelompok.edit')
                                        <button
                                            type="button"
                                            wire:click="editKelompok({{ $k->id }})"
                                            class="btn btn-secondary p-1 text-slate-600 hover:text-primary"
                                            title="Edit Kelompok">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        @endcan
                                        @can('akademik.custom_kelompok.hapus')
                                        <button
                                            type="button"
                                            wire:click="confirmDeleteKelompok({{ $k->id }})"
                                            class="btn btn-secondary p-1 text-slate-600 hover:text-rose-600"
                                            title="Hapus Kelompok">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                    <svg class="mx-auto h-8 w-8 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    <p class="font-medium">Belum ada kelompok yang terdaftar.</p>
                                    @can('akademik.custom_kelompok.tambah')
                                    <button wire:click="createKelompok" class="mt-2 btn btn-primary text-xs">
                                        + Buat Kelompok Sekarang
                                    </button>
                                    @endcan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ======================================================== -->
    <!-- TAB 3: PENGELOMPOKAN SISWA (PENDAFTARAN MANUAL / IMPORT) -->
    <!-- ======================================================== -->
    @if ($activeTab === 'pengelompokan')
        <div class="space-y-4">
            <!-- Selector Kelompok Aktif & Ringkasan -->
            <div class="card p-5 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-border pb-4">
                    <div class="w-full md:w-80">
                        <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Pilih Kelompok Yang Dikelola</label>
                        <select wire:model.live="selectedKelompokId" class="w-full text-sm font-semibold rounded-lg border-border bg-surface text-text-main shadow-xs">
                            @foreach($allKelompoks as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }} {{ $k->kode ? "({$k->kode})" : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($selectedKelompok)
                    <div class="flex flex-wrap items-center gap-2">
                        @can('akademik.custom_kelompok.cetak')
                        <button wire:click="exportAnggotaExcel" class="btn btn-secondary inline-flex items-center gap-1.5 text-xs">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zM6 20V4h5v6h6v10H6zm2-8h2v2H8v-2zm0 3h2v2H8v-2zm3-3h5v2h-5v-2zm0 3h5v2h-5v-2z"/></svg>
                            <span>Export Anggota</span>
                        </button>
                        <button wire:click="openCetakModal" class="btn btn-secondary inline-flex items-center gap-1.5 text-xs">
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak Absen (PDF / XLS)</span>
                        </button>
                        @endcan
                        @can('akademik.custom_kelompok.tambah')
                        <button wire:click="openImportModal" class="btn btn-secondary inline-flex items-center gap-1.5 text-xs">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Import Excel</span>
                        </button>
                        <button wire:click="openManualModal" class="btn btn-primary inline-flex items-center gap-1.5 text-xs shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>+ Tambah Siswa Manual</span>
                        </button>
                        @endcan
                    </div>
                    @endif
                </div>

                @if($selectedKelompok)
                <!-- Detail Info Kelompok -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-text-secondary block">Kategori & Kode:</span>
                        <span class="font-semibold text-text-main flex items-center gap-1 mt-0.5">
                            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: {{ $selectedKelompok->warna }};"></span>
                            {{ $selectedKelompok->kategori ?: 'Akademik' }} {{ $selectedKelompok->kode ? "({$selectedKelompok->kode})" : '' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-text-secondary block">Guru Pembimbing / PJ:</span>
                        <span class="font-semibold text-text-main mt-0.5 block">{{ $selectedKelompok->pembimbing?->name ?? 'Belum Ditentukan' }}</span>
                    </div>
                    <div>
                        <span class="text-text-secondary block">Tahun Ajaran:</span>
                        <span class="font-semibold text-text-main mt-0.5 block">{{ $selectedKelompok->tahunAjaran?->nama ?? 'Umum / Semua Tahun' }}</span>
                    </div>
                    <div>
                        <span class="text-text-secondary block">Jumlah Anggota / Kapasitas:</span>
                        <span class="font-bold text-sm text-text-main mt-0.5 block">
                            {{ $selectedKelompok->siswas_count }}
                            <span class="text-xs font-normal text-text-secondary">
                                {{ $selectedKelompok->kapasitas ? "/ {$selectedKelompok->kapasitas} siswa" : 'siswa (tanpa kuota)' }}
                            </span>
                        </span>
                    </div>
                </div>

                @if($selectedKelompok->keterangan)
                <div class="p-3 bg-surface-hover/60 rounded-lg text-xs text-text-secondary border border-border">
                    <span class="font-semibold text-text-main">Keterangan:</span> {{ $selectedKelompok->keterangan }}
                </div>
                @endif
                @endif
            </div>

            @if($selectedKelompok)
            <!-- Tabel Anggota Kelompok -->
            <div class="card overflow-hidden">
                <div class="p-4 border-b border-border flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <h2 class="text-sm font-bold text-text-main flex items-center gap-2">
                        <span>Daftar Siswa Terdaftar</span>
                        <span class="px-2 py-0.5 text-xs rounded-full bg-primary-soft text-primary font-bold">
                            {{ $anggotaList->count() }}
                        </span>
                    </h2>
                    <div class="w-full sm:w-64">
                        <input type="text" wire:model.live.debounce.300ms="searchAnggota" placeholder="Cari dalam anggota..." class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border text-left text-xs">
                        <thead class="bg-surface-hover/70 font-semibold text-text-secondary uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3">NIS</th>
                                <th class="px-4 py-3">Nama Siswa</th>
                                <th class="px-4 py-3 text-center">JK</th>
                                <th class="px-4 py-3">Rombel</th>
                                <th class="px-4 py-3">Rayon</th>
                                <th class="px-4 py-3">Keterangan Siswa (Catatan/Peran)</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border text-text-main">
                            @forelse($anggotaList as $idx => $siswa)
                            <tr class="hover:bg-surface-hover transition-colors">
                                <td class="px-4 py-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3 font-mono font-medium">{{ $siswa->nipd ?: $siswa->nisn ?: '-' }}</td>
                                <td class="px-4 py-3 font-semibold text-text-main">{{ $siswa->nama }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold {{ $siswa->jk === 'L' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400' : 'bg-pink-100 text-pink-700 dark:bg-pink-950/60 dark:text-pink-400' }}">
                                        {{ $siswa->jk }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $siswa->rombel?->nama ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $siswa->rayon?->nama ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 group">
                                        @if($siswa->pivot->keterangan)
                                        <span class="px-2 py-0.5 rounded bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800 text-[11px] font-medium">
                                            {{ $siswa->pivot->keterangan }}
                                        </span>
                                        @else
                                        <span class="text-slate-400 italic text-[11px]">-</span>
                                        @endif
                                        @can('akademik.custom_kelompok.edit')
                                        <button
                                            type="button"
                                            wire:click="editKeteranganSiswa({{ $siswa->id }})"
                                            class="text-slate-400 hover:text-primary transition-opacity"
                                            title="Ubah Keterangan Siswa">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @can('akademik.custom_kelompok.hapus')
                                    <button
                                        type="button"
                                        wire:click="removeSiswa({{ $siswa->id }})"
                                        wire:confirm="Yakin ingin mengeluarkan siswa ini dari kelompok '{{ $selectedKelompok->nama }}'?"
                                        class="text-rose-600 hover:text-rose-700 text-xs inline-flex items-center gap-1 font-medium"
                                        title="Keluarkan dari kelompok">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Keluarkan</span>
                                    </button>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                    <svg class="mx-auto h-8 w-8 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    <p class="font-medium">Belum ada siswa yang didaftarkan ke kelompok ini.</p>
                                    <div class="flex items-center justify-center gap-2 mt-3">
                                        <button wire:click="openManualModal" class="btn btn-primary text-xs">
                                            + Tambah Siswa Manual
                                        </button>
                                        <button wire:click="openImportModal" class="btn btn-secondary text-xs">
                                            Import File Excel
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="card p-12 text-center text-slate-400">
                <p>Silakan buat kelompok terlebih dahulu di Tab 2 (Kelola Kelompok).</p>
            </div>
            @endif
        </div>
    @endif

    <!-- ======================================================== -->
    <!-- MODAL 1: FORM TAMBAH / EDIT KELOMPOK (TAB 2)             -->
    <!-- ======================================================== -->
    @if ($showKelompokModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="w-full max-w-lg bg-surface rounded-2xl shadow-xl border border-border overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-surface-hover/30">
                <h2 class="text-base font-bold text-text-main">
                    {{ $kelompokId ? 'Edit Data Kelompok' : 'Buat Kelompok Baru' }}
                </h2>
                <button wire:click="$set('showKelompokModal', false)" class="text-slate-400 hover:text-text-main text-lg">&times;</button>
            </div>

            <form wire:submit="saveKelompok" class="p-6 space-y-4 text-xs">
                <!-- Nama & Kode -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block font-semibold text-text-secondary mb-1">Nama Kelompok <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="nama" placeholder="Contoh: Tim Olimpiade Web, KBM Remedial..." class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                        @error('nama') <span class="text-rose-500 text-[11px] block mt-0.5">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold text-text-secondary mb-1">Kode / Singkatan</label>
                        <input type="text" wire:model="kode" placeholder="KBM-01" class="w-full text-xs rounded-lg border-border bg-surface text-text-main uppercase font-mono">
                        @error('kode') <span class="text-rose-500 text-[11px] block mt-0.5">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Kategori & Warna Tag -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-text-secondary mb-1">Kategori Kelompok</label>
                        <select wire:model="kategori" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                            <option value="Akademik">Akademik</option>
                            <option value="Remedial">Remedial</option>
                            <option value="Minat/Bakat">Minat/Bakat</option>
                            <option value="Proyek / Lomba">Proyek / Lomba</option>
                            <option value="Bimbingan Konseling">Bimbingan Konseling</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        @error('kategori') <span class="text-rose-500 text-[11px] block mt-0.5">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold text-text-secondary mb-1">Warna Badge / Tag</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="warna" class="h-8 w-10 p-0.5 border border-border rounded-lg bg-surface cursor-pointer">
                            <input type="text" wire:model="warna" class="w-full text-xs rounded-lg border-border bg-surface text-text-main font-mono">
                        </div>
                        @error('warna') <span class="text-rose-500 text-[11px] block mt-0.5">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Tahun Ajaran & Pembimbing -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-text-secondary mb-1">Tahun Ajaran (Opsional)</label>
                        <select wire:model="tahun_ajaran_id" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                            <option value="">-- Umum / Lintas Tahun --</option>
                            @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                        <span class="text-[10px] text-slate-400">Kosongkan jika berlaku selamanya</span>
                    </div>
                    <div>
                        <label class="block font-semibold text-text-secondary mb-1">Guru Pembimbing / PJ</label>
                        <select wire:model="pembimbing_id" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                            <option value="">-- Belum Ditentukan --</option>
                            @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Kapasitas & Status -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-text-secondary mb-1">Kapasitas Maksimal (Siswa)</label>
                        <input type="number" min="1" max="500" wire:model="kapasitas" placeholder="Contoh: 30 (opsional)" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                        <span class="text-[10px] text-slate-400">Kosongkan jika tidak dibatasi</span>
                    </div>
                    <div>
                        <label class="block font-semibold text-text-secondary mb-1">Status Keaktifan</label>
                        <select wire:model="is_active" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <!-- Keterangan -->
                <div>
                    <label class="block font-semibold text-text-secondary mb-1">Keterangan / Deskripsi Kelompok</label>
                    <textarea wire:model="keterangan" rows="2" placeholder="Catatan mengenai target capaian, kriteria peserta, atau tujuan kelompok..." class="w-full text-xs rounded-lg border-border bg-surface text-text-main"></textarea>
                    @error('keterangan') <span class="text-rose-500 text-[11px] block mt-0.5">{{ $message }}</span> @enderror
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-border">
                    <button type="button" wire:click="$set('showKelompokModal', false)" class="btn btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn btn-primary text-xs">Simpan Kelompok</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- ======================================================== -->
    <!-- MODAL 2: KONFIRMASI HAPUS KELOMPOK                       -->
    <!-- ======================================================== -->
    @if ($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="w-full max-w-sm bg-surface rounded-2xl shadow-xl border border-border p-6 text-center space-y-4">
            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 flex items-center justify-center mx-auto">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-text-main">Hapus Kelompok?</h3>
                <p class="text-xs text-text-secondary mt-1">
                    Anda yakin ingin menghapus kelompok <strong>"{{ $kelompokToDeleteNama }}"</strong>? Semua data pendaftaran siswa di kelompok ini akan ikut terhapus.
                </p>
            </div>
            <div class="flex justify-center gap-2 pt-2">
                <button type="button" wire:click="$set('showDeleteModal', false)" class="btn btn-secondary text-xs">Batal</button>
                <button type="button" wire:click="deleteKelompok" class="btn btn-danger text-xs">Ya, Hapus Kelompok</button>
            </div>
        </div>
    </div>
    @endif

    <!-- ======================================================== -->
    <!-- MODAL 3: PENDAFTARAN SISWA MANUAL (MULTI-SELECT)         -->
    <!-- ======================================================== -->
    @if ($showManualModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="w-full max-w-2xl bg-surface rounded-2xl shadow-xl border border-border overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-surface-hover/30 shrink-0">
                <div>
                    <h2 class="text-sm font-bold text-text-main">Tambah Siswa ke Kelompok: {{ $selectedKelompok->nama }}</h2>
                    <p class="text-[11px] text-text-secondary">Pilih satu atau beberapa siswa yang belum terdaftar di kelompok ini.</p>
                </div>
                <button wire:click="$set('showManualModal', false)" class="text-slate-400 hover:text-text-main text-lg">&times;</button>
            </div>

            <!-- Filter Siswa Modal -->
            <div class="p-4 border-b border-border grid grid-cols-1 sm:grid-cols-3 gap-2 shrink-0 text-xs">
                <div>
                    <input type="text" wire:model.live.debounce.300ms="manualSearch" placeholder="Cari nama atau NIS..." class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                </div>
                <div>
                    <select wire:model.live="manualFilterRombel" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                        <option value="">-- Semua Rombel --</option>
                        @foreach($rombels as $r)
                        <option value="{{ $r->id }}">{{ $r->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select wire:model.live="manualFilterRayon" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                        <option value="">-- Semua Rayon --</option>
                        @foreach($rayons as $ry)
                        <option value="{{ $ry->id }}">{{ $ry->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Daftar Calon Siswa (Scrollable) -->
            <div class="p-4 overflow-y-auto flex-1 space-y-2 text-xs">
                @if($candidateSiswas->isNotEmpty())
                <div class="flex items-center justify-between pb-2 text-[11px] text-slate-400 border-b border-border">
                    <span>Terpilih: <strong class="text-primary">{{ count($selectedSiswaIds) }}</strong> siswa</span>
                    <span class="text-slate-400">Menampilkan maks 50 siswa sesuai filter</span>
                </div>

                <div class="divide-y divide-border border border-border rounded-lg overflow-hidden">
                    @foreach($candidateSiswas as $s)
                    <label class="flex items-center justify-between p-2.5 hover:bg-surface-hover cursor-pointer transition-colors {{ in_array($s->id, $selectedSiswaIds) ? 'bg-primary-soft/40' : '' }}">
                        <div class="flex items-center gap-3">
                            <input
                                type="checkbox"
                                value="{{ $s->id }}"
                                wire:click="toggleSiswaSelection({{ $s->id }})"
                                {{ in_array($s->id, $selectedSiswaIds) ? 'checked' : '' }}
                                class="rounded border-border text-primary focus:ring-primary">
                            <div>
                                <div class="font-semibold text-text-main">{{ $s->nama }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">
                                    NIS: {{ $s->nipd ?: $s->nisn ?: '-' }} &bull; {{ $s->rombel?->nama ?? '-' }} &bull; {{ $s->rayon?->nama ?? '-' }}
                                </div>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $s->jk === 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                            {{ $s->jk }}
                        </span>
                    </label>
                    @endforeach
                </div>
                @else
                <div class="py-12 text-center text-slate-400">
                    <p>Tidak ada siswa yang dapat ditambahkan sesuai filter pencarian.</p>
                </div>
                @endif
            </div>

            <!-- Catatan Default Per Siswa & Footer Submit -->
            <div class="p-4 border-t border-border bg-surface-hover/30 shrink-0 space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">
                        Keterangan Siswa (Opsional untuk siswa yang terpilih):
                    </label>
                    <input type="text" wire:model="catatanPerSiswa" placeholder="Contoh: Anggota, Ketua, Remedial Bab 1..." class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                </div>
                <div class="flex items-center justify-between pt-1">
                    <span class="text-xs text-text-secondary">
                        Total terpilih: <strong class="text-primary">{{ count($selectedSiswaIds) }}</strong> siswa
                    </span>
                    <div class="flex gap-2">
                        <button type="button" wire:click="$set('showManualModal', false)" class="btn btn-secondary text-xs">Batal</button>
                        <button type="button" wire:click="saveManualSiswa" class="btn btn-primary text-xs" {{ empty($selectedSiswaIds) ? 'disabled' : '' }}>
                            Simpan ke Kelompok
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ======================================================== -->
    <!-- MODAL 4: EDIT KETERANGAN PER SISWA                       -->
    <!-- ======================================================== -->
    @if ($showEditKeteranganModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="w-full max-w-sm bg-surface rounded-2xl shadow-xl border border-border p-5 space-y-4">
            <div>
                <h3 class="text-sm font-bold text-text-main">Ubah Keterangan Siswa</h3>
                <p class="text-xs text-text-secondary mt-0.5">{{ $editingSiswaNama }}</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1">Catatan / Peran di Kelompok</label>
                <input type="text" wire:model="editingKeterangan" placeholder="Contoh: Ketua, Anggota Inti, Tugas Presentasi..." class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-border">
                <button type="button" wire:click="$set('showEditKeteranganModal', false)" class="btn btn-secondary text-xs">Batal</button>
                <button type="button" wire:click="saveKeteranganSiswa" class="btn btn-primary text-xs">Simpan Keterangan</button>
            </div>
        </div>
    </div>
    @endif

    <!-- ======================================================== -->
    <!-- MODAL 5: IMPORT MASSAL EXCEL (PER KELOMPOK TERPILIH)     -->
    <!-- ======================================================== -->
    @if ($showImportModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="w-full max-w-md bg-surface rounded-2xl shadow-xl border border-border overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-surface-hover/30">
                <div>
                    <h2 class="text-sm font-bold text-text-main">Import Siswa ke Kelompok</h2>
                    <p class="text-[11px] text-text-secondary">{{ $selectedKelompok->nama }}</p>
                </div>
                <button wire:click="$set('showImportModal', false)" class="text-slate-400 hover:text-text-main text-lg">&times;</button>
            </div>

            <div class="p-6 space-y-4 text-xs">
                <!-- Step 1: Download Template -->
                <div class="p-3 bg-surface-hover/60 rounded-xl border border-border flex items-center justify-between gap-2">
                    <div>
                        <div class="font-semibold text-text-main">1. Unduh Template Resmi</div>
                        <div class="text-[11px] text-text-secondary">Berkas Excel berformat standar (NIS & Keterangan).</div>
                    </div>
                    <button type="button" wire:click="downloadTemplate" class="btn btn-secondary text-xs shrink-0 inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Unduh</span>
                    </button>
                </div>

                <!-- Step 2: Upload File -->
                <div>
                    <label class="block font-semibold text-text-secondary mb-1">2. Unggah Berkas Excel (.xlsx / .xls)</label>
                    <input type="file" wire:model="importFile" accept=".xlsx,.xls" class="w-full text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90 border border-border rounded-lg bg-surface">
                    @error('importFile') <span class="text-rose-500 text-[11px] block mt-1">{{ $message }}</span> @enderror
                </div>

                <div wire:loading wire:target="importFile" class="text-primary text-[11px]">
                    Mengunggah berkas...
                </div>

                <!-- Summary if processed -->
                @if ($importSummary)
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800 text-[11px] space-y-1">
                    <div class="font-bold text-emerald-800 dark:text-emerald-300">Hasil Import:</div>
                    <div class="text-emerald-700 dark:text-emerald-400">
                        &bull; Siswa baru ditambahkan: <strong>{{ $importSummary['imported'] }}</strong><br>
                        &bull; Keterangan diperbarui: <strong>{{ $importSummary['updated'] }}</strong><br>
                        &bull; Dilewati / gagal: <strong>{{ $importSummary['skipped'] }}</strong>
                    </div>
                    @if(!empty($importSummary['errors']))
                    <div class="pt-2 text-rose-600 dark:text-rose-400 space-y-0.5">
                        @foreach(array_slice($importSummary['errors'], 0, 3) as $err)
                        <div>&bull; {{ $err }}</div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endif

                <div class="pt-2 flex justify-end gap-2 border-t border-border">
                    <button type="button" wire:click="$set('showImportModal', false)" class="btn btn-secondary text-xs">Tutup</button>
                    <button type="button" wire:click="processImport" wire:loading.attr="disabled" class="btn btn-primary text-xs inline-flex items-center gap-1.5" {{ !$importFile ? 'disabled' : '' }}>
                        <span wire:loading.remove wire:target="processImport">Proses Import</span>
                        <span wire:loading wire:target="processImport">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ======================================================== -->
    <!-- MODAL 6: CETAK ABSENSI DINAMIS (PDF & XLS)               -->
    <!-- ======================================================== -->
    @if ($showCetakModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="w-full max-w-md bg-surface rounded-2xl shadow-xl border border-border overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-surface-hover/30">
                <div>
                    <h2 class="text-sm font-bold text-text-main">Cetak Lembar Presensi / Absensi</h2>
                    <p class="text-[11px] text-text-secondary">Pilih format unduhan PDF atau Excel (.xlsx)</p>
                </div>
                <button wire:click="$set('showCetakModal', false)" class="text-slate-400 hover:text-text-main text-lg">&times;</button>
            </div>

            <div class="p-6 space-y-4 text-xs">
                <!-- Pilihan Kolom Pertemuan -->
                <div>
                    <label class="block font-semibold text-text-secondary mb-1">Jumlah Kolom Pertemuan Presensi</label>
                    <select wire:model="cetakJumlahKolom" class="w-full text-xs rounded-lg border-border bg-surface text-text-main font-semibold">
                        <option value="10">10 Kolom Pertemuan</option>
                        <option value="16">16 Kolom Pertemuan (1 Semester)</option>
                        <option value="20">20 Kolom Pertemuan</option>
                        <option value="25">25 Kolom Pertemuan</option>
                        <option value="30">30 Kolom Pertemuan (Format Penuh)</option>
                    </select>
                </div>

                <!-- Tahun Pelajaran -->
                <div>
                    <label class="block font-semibold text-text-secondary mb-1">Tahun Pelajaran</label>
                    <input type="text" wire:model="cetakTahunPelajaran" placeholder="Contoh: 2026/2027" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                </div>

                <!-- Keterangan / Nama Kegiatan -->
                <div>
                    <label class="block font-semibold text-text-secondary mb-1">Materi / Nama Kegiatan</label>
                    <input type="text" wire:model="cetakMapelKeterangan" placeholder="Contoh: Pembinaan Lomba, Remedial Aljabar..." class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                </div>

                <!-- Pembimbing TTD -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-text-secondary mb-1">Nama Guru Pembimbing</label>
                        <input type="text" wire:model="cetakNamaGuru" placeholder="Nama Guru Pembimbing" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block font-semibold text-text-secondary mb-1">NIP (Opsional)</label>
                        <input type="text" wire:model="cetakNipGuru" placeholder="NIP Pembimbing" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                    </div>
                </div>

                <!-- Tombol Aksi Cetak -->
                <div class="pt-3 border-t border-border flex flex-col sm:flex-row items-center justify-end gap-2">
                    <button type="button" wire:click="$set('showCetakModal', false)" class="btn btn-secondary text-xs w-full sm:w-auto">Batal</button>
                    
                    <button type="button" wire:click="cetakAbsenExcel" class="btn btn-secondary text-xs w-full sm:w-auto inline-flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zM6 20V4h5v6h6v10H6zm2-8h2v2H8v-2zm0 3h2v2H8v-2zm3-3h5v2h-5v-2zm0 3h5v2h-5v-2z"/></svg>
                        <span>Unduh Excel (.xlsx)</span>
                    </button>

                    <button type="button" wire:click="cetakAbsenPdf" class="btn btn-primary text-xs w-full sm:w-auto inline-flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Unduh PDF</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
