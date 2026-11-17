<div class="space-y-6">
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('kesiswaan.siswa') }}" class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Data Siswa</span>
                </a>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Unduh Data Siswa Kustom</h1>
            <p class="text-sm text-text-secondary">Pilih kolom data spesifik yang Anda butuhkan dan saring siswa berdasarkan kriteria fleksibel.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" wire:click="downloadExcel" wire:loading.attr="disabled" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 rounded-xl shadow-xs transition disabled:opacity-50 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.5L19 7.5V19a2 2 0 01-2 2z" />
                </svg>
                <span wire:loading.remove wire:target="downloadExcel">Unduh Excel (.xlsx)</span>
                <span wire:loading wire:target="downloadExcel">Menyiapkan Excel...</span>
            </button>

            <button type="button" wire:click="downloadCsv" wire:loading.attr="disabled" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-text-main bg-surface hover:bg-surface-hover border border-border rounded-xl shadow-xs transition disabled:opacity-50 cursor-pointer">
                <svg class="w-4 h-4 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span wire:loading.remove wire:target="downloadCsv">Unduh CSV (.csv)</span>
                <span wire:loading wire:target="downloadCsv">Menyiapkan CSV...</span>
            </button>
        </div>
    </div>

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Panel Filter Multi-Select -->
    <div class="card p-5 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-border gap-2">
            <div class="flex items-center gap-2">
                <span class="p-1.5 rounded-lg bg-primary/10 text-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </span>
                <h2 class="text-sm font-bold text-text-main uppercase tracking-wider">Filter Data Siswa (Multi-Select)</h2>
            </div>
            
            <div class="flex items-center gap-3 text-xs text-text-secondary">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-bold bg-primary/10 text-primary border border-primary/20">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    <span>Total Target: {{ number_format($totalCount, 0, ',', '.') }} Siswa</span>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- 1. Filter Tingkat -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-text-main uppercase tracking-wider">Tingkat</label>
                    <div class="text-[11px] space-x-2">
                        <button type="button" wire:click="selectAllFilter('tingkat', ['X', 'XI', 'XII'])" class="text-primary hover:underline cursor-pointer">Semua</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" wire:click="clearFilter('tingkat')" class="text-text-secondary hover:underline cursor-pointer">Bersihkan</button>
                    </div>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach(['X', 'XI', 'XII'] as $tk)
                        <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold cursor-pointer transition select-none {{ in_array($tk, $filterTingkat) ? 'bg-primary text-white border-primary shadow-2xs' : 'bg-surface hover:bg-surface-hover text-text-secondary border-border' }}">
                            <input type="checkbox" wire:model.live="filterTingkat" value="{{ $tk }}" class="sr-only">
                            <span>Kelas {{ $tk }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- 2. Filter Status Siswa -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-text-main uppercase tracking-wider">Status Siswa</label>
                    <div class="text-[11px] space-x-2">
                        <button type="button" wire:click="selectAllFilter('status', ['aktif', 'lulus', 'pindah', 'dikeluarkan', 'cuti'])" class="text-primary hover:underline cursor-pointer">Semua</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" wire:click="clearFilter('status')" class="text-text-secondary hover:underline cursor-pointer">Bersihkan</button>
                    </div>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach(['aktif' => 'Aktif', 'lulus' => 'Lulus', 'pindah' => 'Pindah', 'dikeluarkan' => 'Keluar', 'cuti' => 'Cuti'] as $stKey => $stLabel)
                        <label class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border text-xs font-semibold cursor-pointer transition select-none {{ in_array($stKey, $filterStatus) ? 'bg-emerald-600 text-white border-emerald-600 shadow-2xs' : 'bg-surface hover:bg-surface-hover text-text-secondary border-border' }}">
                            <input type="checkbox" wire:model.live="filterStatus" value="{{ $stKey }}" class="sr-only">
                            <span>{{ $stLabel }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- 3. Urutkan Data -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-text-main uppercase tracking-wider block">Urutkan Data</label>
                <div class="grid grid-cols-2 gap-2">
                    <select wire:model.live="sortField" class="text-xs font-semibold rounded-lg border-border bg-surface text-text-main py-1.5">
                        <option value="nama">Nama Siswa</option>
                        <option value="nipd">NIS / NIPD</option>
                        <option value="rombel">Kelas / Rombel</option>
                        <option value="rayon">Rayon</option>
                    </select>
                    <select wire:model.live="sortDirection" class="text-xs font-semibold rounded-lg border-border bg-surface text-text-main py-1.5">
                        <option value="asc">A - Z (Menaik)</option>
                        <option value="desc">Z - A (Menurun)</option>
                    </select>
                </div>
            </div>

            <!-- 4. Filter Program -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-text-main uppercase tracking-wider">Program</label>
                    <div class="text-[11px] space-x-2">
                        <button type="button" wire:click="selectAllFilter('program', {{ json_encode($allPrograms->pluck('id')->toArray()) }})" class="text-primary hover:underline cursor-pointer">Semua</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" wire:click="clearFilter('program')" class="text-text-secondary hover:underline cursor-pointer">Bersihkan</button>
                    </div>
                </div>
                <div class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto p-1">
                    @foreach($allPrograms as $prog)
                        <label class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border text-xs font-medium cursor-pointer transition select-none {{ in_array((string)$prog->id, array_map('strval', $filterProgram)) ? 'bg-primary/20 text-primary border-primary font-bold' : 'bg-surface hover:bg-surface-hover text-text-secondary border-border' }}">
                            <input type="checkbox" wire:model.live="filterProgram" value="{{ $prog->id }}" class="sr-only">
                            <span>{{ $prog->nama }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- 5. Filter Rayon -->
            <div class="space-y-2 md:col-span-1">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-text-main uppercase tracking-wider">Rayon ({{ count($filterRayon) }} dipilih)</label>
                    <div class="text-[11px] space-x-2">
                        <button type="button" wire:click="selectAllFilter('rayon', {{ json_encode($allRayons->pluck('id')->toArray()) }})" class="text-primary hover:underline cursor-pointer">Semua</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" wire:click="clearFilter('rayon')" class="text-text-secondary hover:underline cursor-pointer">Bersihkan</button>
                    </div>
                </div>
                <div class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto p-1 border border-border/50 rounded-lg bg-surface/50">
                    @forelse($allRayons as $ry)
                        <label class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium cursor-pointer transition select-none {{ in_array((string)$ry->id, array_map('strval', $filterRayon)) ? 'bg-primary text-white font-bold' : 'bg-surface hover:bg-surface-hover text-text-secondary border border-border' }}">
                            <input type="checkbox" wire:model.live="filterRayon" value="{{ $ry->id }}" class="sr-only">
                            <span>{{ $ry->nama }}</span>
                        </label>
                    @empty
                        <span class="text-xs text-text-secondary italic">Tidak ada rayon</span>
                    @endforelse
                </div>
            </div>

            <!-- 6. Filter Kelas / Rombel -->
            <div class="space-y-2 md:col-span-1">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-text-main uppercase tracking-wider">Kelas / Rombel ({{ count($filterRombel) }} dipilih)</label>
                    <div class="text-[11px] space-x-2">
                        <button type="button" wire:click="selectAllFilter('rombel', {{ json_encode($allRombels->pluck('id')->toArray()) }})" class="text-primary hover:underline cursor-pointer">Semua</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" wire:click="clearFilter('rombel')" class="text-text-secondary hover:underline cursor-pointer">Bersihkan</button>
                    </div>
                </div>
                <div class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto p-1 border border-border/50 rounded-lg bg-surface/50">
                    @forelse($allRombels as $rb)
                        <label class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium cursor-pointer transition select-none {{ in_array((string)$rb->id, array_map('strval', $filterRombel)) ? 'bg-primary text-white font-bold' : 'bg-surface hover:bg-surface-hover text-text-secondary border border-border' }}">
                            <input type="checkbox" wire:model.live="filterRombel" value="{{ $rb->id }}" class="sr-only">
                            <span>{{ $rb->nama }}</span>
                        </label>
                    @empty
                        <span class="text-xs text-text-secondary italic">Tidak ada rombel aktif</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Presets Cepat Bar -->
    <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 rounded-xl bg-slate-100 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold text-text-secondary uppercase tracking-wider">Preset Kolom:</span>
            @foreach($presets as $pKey => $pVal)
                <button type="button" wire:click="applyPreset('{{ $pKey }}')" class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-border bg-surface hover:bg-surface-hover text-text-main shadow-2xs transition cursor-pointer">
                    ⚡ {{ $pVal['label'] }}
                </button>
            @endforeach
        </div>

        <button type="button" wire:click="clearColumns" class="px-2.5 py-1 text-xs font-semibold text-rose-600 hover:text-rose-700 dark:text-rose-400 hover:underline cursor-pointer">
            Kosongkan Pilihan
        </button>
    </div>

    <!-- Dual Listbox / Transfer List Kolom -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Kotak Kiri: Kolom Tersedia -->
        <div class="card p-5 space-y-3 flex flex-col h-[460px]">
            <div class="flex items-center justify-between pb-2 border-b border-border">
                <div>
                    <h3 class="text-sm font-bold text-text-main uppercase tracking-wider">Kolom Tersedia</h3>
                    <p class="text-xs text-text-secondary">Klik pada item untuk menambahkannya ke berkas unduhan.</p>
                </div>
                <span class="px-2 py-0.5 text-xs font-bold font-mono rounded bg-slate-100 dark:bg-slate-800 text-text-secondary">
                    {{ count($unselectedColumns) }} kolom
                </span>
            </div>

            <!-- Kontrol Pencarian & Filter Kategori -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                <input type="text" wire:model.live.debounce.150ms="searchAvailable" placeholder="Cari nama kolom..." class="text-xs rounded-lg border-border bg-surface text-text-main py-1.5 px-2.5">
                
                <select wire:model.live="selectedCategory" class="text-xs rounded-lg border-border bg-surface text-text-main py-1.5 px-2">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- List Kolom Tersedia -->
            <div class="flex-1 overflow-y-auto space-y-1.5 pr-1 divide-y divide-border/40">
                @forelse($unselectedColumns as $cKey => $col)
                    <div wire:key="avail-{{ $cKey }}" class="flex items-center justify-between p-2 rounded-lg hover:bg-surface-hover transition group cursor-pointer" wire:click="addColumn('{{ $cKey }}')">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-text-main group-hover:text-primary transition">{{ $col['label'] }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-text-secondary">{{ $col['category'] }}</span>
                        </div>
                        <button type="button" class="p-1 rounded text-primary hover:bg-primary/10 transition text-sm font-bold">
                            +
                        </button>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-48 text-center text-text-secondary text-xs">
                        <p>Tidak ada kolom yang cocok atau semua kolom telah dipilih.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Kotak Kanan: Kolom Dipilih & Pengaturan Urutan -->
        <div class="card p-5 space-y-3 flex flex-col h-[460px] border-primary/40">
            <div class="flex items-center justify-between pb-2 border-b border-border">
                <div>
                    <h3 class="text-sm font-bold text-text-main uppercase tracking-wider flex items-center gap-2">
                        <span>Kolom Terpilih (Hasil Export)</span>
                        <span class="px-2 py-0.5 text-xs font-bold font-mono rounded bg-primary/10 text-primary">
                            {{ count($selectedColumns) }} Kolom
                        </span>
                    </h3>
                    <p class="text-xs text-text-secondary">Urutan kolom di sini menentukan urutan kolom pada berkas Excel/CSV.</p>
                </div>
            </div>

            <!-- List Kolom Terpilih -->
            <div class="flex-1 overflow-y-auto space-y-1.5 pr-1 divide-y divide-border/40">
                @forelse($selectedColumns as $idx => $sKey)
                    @php $colDef = $availableColumns[$sKey] ?? null; @endphp
                    @if($colDef)
                        <div wire:key="selected-{{ $sKey }}" class="flex items-center justify-between p-2 rounded-lg bg-surface hover:bg-surface-hover border border-border/60 transition group">
                            <div class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-slate-100 dark:bg-slate-800 text-[10px] font-mono font-bold flex items-center justify-center text-text-secondary">
                                    {{ $idx + 1 }}
                                </span>
                                <div>
                                    <span class="text-xs font-bold text-text-main">{{ $colDef['label'] }}</span>
                                    <span class="text-[10px] text-text-secondary ml-1.5">({{ $colDef['category'] }})</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1">
                                <!-- Geser Naik -->
                                <button type="button" wire:click="moveColumnUp('{{ $sKey }}')" title="Geser ke Kiri/Sebelumnya" class="p-1 rounded text-text-secondary hover:text-text-main hover:bg-slate-200 dark:hover:bg-slate-700 transition cursor-pointer disabled:opacity-20" {{ $idx === 0 ? 'disabled' : '' }}>
                                    ▲
                                </button>
                                <!-- Geser Turun -->
                                <button type="button" wire:click="moveColumnDown('{{ $sKey }}')" title="Geser ke Kanan/Selanjutnya" class="p-1 rounded text-text-secondary hover:text-text-main hover:bg-slate-200 dark:hover:bg-slate-700 transition cursor-pointer disabled:opacity-20" {{ $idx === count($selectedColumns) - 1 ? 'disabled' : '' }}>
                                    ▼
                                </button>
                                <!-- Hapus / Batalkan -->
                                <button type="button" wire:click="removeColumn('{{ $sKey }}')" title="Keluarkan Kolom" class="p-1 rounded text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer ml-1">
                                    ✕
                                </button>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="flex flex-col items-center justify-center h-48 text-center text-text-secondary text-xs">
                        <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="font-semibold text-text-main">Belum ada kolom yang dipilih.</p>
                        <p class="text-[11px] mt-1">Pilih kolom dari kotak kiri atau klik salah satu tombol preset di atas.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Live Preview Data Tabel -->
    <div class="card p-5 space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-border gap-2">
            <div>
                <h3 class="text-sm font-bold text-text-main uppercase tracking-wider flex items-center gap-2">
                    <span>Live Preview Data Siswa</span>
                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-slate-100 dark:bg-slate-800 text-text-secondary">
                        Maks. 5 baris pertama
                    </span>
                </h3>
                <p class="text-xs text-text-secondary">Pratinjau struktur tabel sesuai urutan kolom dan kriteria filter yang Anda tentukan.</p>
            </div>

            <div class="text-xs font-mono text-text-secondary">
                Ditemukan <b>{{ number_format($totalCount, 0, ',', '.') }}</b> siswa
            </div>
        </div>

        @if(empty($selectedColumns))
            <div class="p-8 text-center text-text-secondary text-sm">
                Pilih minimal 1 kolom di atas untuk menampilkan pratinjau tabel.
            </div>
        @elseif(empty($previewData))
            <div class="p-8 text-center text-text-secondary text-sm">
                Tidak ada data siswa yang cocok dengan kriteria filter saat ini.
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-border">
                <table class="min-w-full divide-y divide-border text-left text-xs">
                    <thead class="bg-surface-hover/80 text-text-main font-bold">
                        <tr>
                            <th class="px-3 py-2.5 text-center w-12 border-r border-border">No</th>
                            @foreach($selectedColumns as $cKey)
                                @if(isset($availableColumns[$cKey]))
                                    <th class="px-3 py-2.5 whitespace-nowrap border-r border-border/60">
                                        {{ $availableColumns[$cKey]['label'] }}
                                    </th>
                                @endif
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60 bg-surface">
                        @foreach($previewData as $rowIdx => $row)
                            <tr class="hover:bg-surface-hover/50 transition">
                                <td class="px-3 py-2 text-center text-text-secondary font-mono border-r border-border">
                                    {{ $rowIdx + 1 }}
                                </td>
                                @foreach($selectedColumns as $cKey)
                                    <td class="px-3 py-2 whitespace-nowrap text-text-main border-r border-border/40 font-mono text-[11px]">
                                        {{ $row[$cKey] ?? '-' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
