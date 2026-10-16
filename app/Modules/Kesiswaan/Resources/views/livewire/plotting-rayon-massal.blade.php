<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Plotting Rayon Massal</h1>
            <p class="text-sm text-text-secondary">Alokasikan siswa ke Rayon wilayah tempat tinggal secara massal berdasarkan Kecamatan / Kelurahan.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" wire:click="openResetModal" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-300 dark:hover:bg-rose-900/60 rounded-lg border border-rose-200 dark:border-rose-800 shadow-2xs transition cursor-pointer">
                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Reset Plotting Rayon</span>
            </button>
            <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <span>+ Import Excel</span>
            </button>
            <a href="{{ route('kesiswaan.siswa') }}" class="inline-flex items-center text-sm font-semibold text-text-secondary hover:text-primary transition px-3 py-2">
                &larr; Kembali ke Data Siswa
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 text-sm text-success bg-success-soft rounded-lg border border-success">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 text-sm text-rose-800 bg-rose-50 rounded-lg dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
            {{ session('error') }}
        </div>
    @endif

    <!-- Action Bar: Target Rayon Selection -->
    <div class="p-4 bg-indigo-50/60 bg-surface/80 rounded-xl border border-indigo-100 dark:border-indigo-900 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">
                Pilih Rayon Tujuan:
            </span>
            <select wire:model="targetRayonId" class="text-sm font-semibold rounded-lg border-indigo-300 dark:border-indigo-700 bg-surface text-text-main">
                <option value="">-- Pilih Rayon --</option>
                @foreach($rayons as $ry)
                    <option value="{{ $ry->id }}">{{ $ry->nama }} ({{ $ry->wilayah_cakupan ?? 'Wilayah' }})</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-xs text-text-secondary">
                Terpilih: <b>{{ count($selectedSiswaIds) }}</b> siswa
            </span>
            @if(!empty($selectedSiswaIds))
                <button type="button" wire:click="resetSelectedRayon" wire:confirm="Yakin ingin mengosongkan pembagian rayon untuk {{ count($selectedSiswaIds) }} siswa terpilih?" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-rose-700 dark:text-rose-300 bg-rose-100 hover:bg-rose-200 dark:bg-rose-950/60 rounded-lg transition border border-rose-200 dark:border-rose-800 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Kosongkan Rayon Terpilih</span>
                </button>
            @endif
            <button wire:click="applyPlotting" wire:confirm="Alokasikan {{ count($selectedSiswaIds) }} siswa terpilih ke Rayon tujuan?" class="btn btn-primary" @if(empty($selectedSiswaIds) || !$targetRayonId) disabled @endif>
                Terapkan Rayon ke Siswa Terpilih
            </button>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="p-4 card grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Status Rayon</label>
            <select wire:model.live="filterStatusRayon" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                <option value="belum_ada">Belum Memiliki Rayon</option>
                <option value="semua">Semua Siswa</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Filter Kecamatan</label>
            <select wire:model.live="filterKecamatan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                <option value="">-- Semua Kecamatan --</option>
                @foreach($kecamatans as $kc)
                    <option value="{{ $kc }}">{{ $kc }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Filter Kelurahan</label>
            <select wire:model.live="filterKelurahan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main" @if(!$filterKecamatan) disabled @endif>
                <option value="">-- Semua Kelurahan --</option>
                @foreach($kelurahans as $kl)
                    <option value="{{ $kl }}">{{ $kl }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Cari Nama / NISN</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik nama atau NISN..." class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
        </div>
    </div>

    <!-- Table Card -->
    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-sm">
                <thead class="bg-surface-hover bg-surface/50 text-text-secondary font-medium">
                    <tr>
                        <th class="px-4 py-3 w-10 text-center">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-border text-primary focus:ring-indigo-500">
                        </th>
                        <th class="px-6 py-3">Nama Siswa</th>
                        <th class="px-6 py-3">NISN</th>
                        <th class="px-6 py-3">Rombel</th>
                        <th class="px-6 py-3">Rayon Saat Ini</th>
                        <th class="px-6 py-3">Kecamatan</th>
                        <th class="px-6 py-3">Kelurahan / Desa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-text-main">
                    @forelse($siswas as $s)
                        <tr class="hover:bg-surface-hover/50 dark:hover:bg-slate-800/30 transition">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" value="{{ $s->id }}" wire:model.live="selectedSiswaIds" class="rounded border-border text-primary focus:ring-indigo-500">
                            </td>
                            <td class="px-6 py-4 font-bold text-text-main">
                                {{ $s->nama }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-text-secondary">
                                {{ $s->nisn }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                    {{ $s->rombel?->nama ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($s->rayon)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-success-soft text-emerald-700 dark:bg-emerald-950/60 text-success border border-success">
                                        {{ $s->rayon->nama }}
                                    </span>
                                @else
                                    <span class="text-xs italic text-rose-500">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-text-main text-text-secondary">
                                {{ $s->kecamatan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-text-secondary">
                                {{ $s->kelurahan ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400">Tidak ada siswa yang sesuai kriteria filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-border">
            {{ $siswas->links() }}
        </div>
    </div>

    <!-- Modal Import Excel -->
    @if($showImportModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div class="w-full max-w-xl bg-surface rounded-2xl shadow-2xl border border-border overflow-hidden my-8">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-surface-hover/50">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.5L19 7.5V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-bold text-text-main">Import Plotting Rayon via Excel</h2>
                        <p class="text-xs text-text-secondary">Alokasikan siswa ke rayon tempat tinggal menggunakan berkas spreadsheet.</p>
                    </div>
                </div>
                <button type="button" wire:click="closeImportModal" class="text-slate-400 hover:text-text-main text-2xl font-bold cursor-pointer">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-5 text-sm">
                <!-- Langkah 1: Pilih Rayon Tujuan -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-text-main">
                        1. Pilih Rayon Tujuan <span class="text-rose-500">*</span>
                    </label>
                    <select wire:model="importTargetRayonId" class="w-full text-sm font-semibold rounded-lg border-border bg-surface text-text-main">
                        <option value="">-- Pilih Rayon Tujuan --</option>
                        @foreach($rayons as $ry)
                            <option value="{{ $ry->id }}">{{ $ry->nama }}</option>
                        @endforeach
                    </select>
                    @error('importTargetRayonId')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Langkah 2: Unduh Template -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200 dark:border-slate-700/60 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-text-main">2. Unduh Format Template</span>
                        <span class="text-[11px] text-text-secondary">Kolom wajib: <b>Kolom A (NIS)</b></span>
                    </div>
                    <p class="text-xs text-text-secondary">
                        Sistem mencocokkan data secara ketat berdasarkan <b>NIS (NIPD)</b> siswa. Anda dapat memilih template kosong atau yang sudah terisi otomatis sesuai filter wilayah/rombel aktif saat ini.
                    </p>
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <button type="button" wire:click="downloadTemplate('kosong')" class="px-3 py-1.5 text-xs font-semibold rounded-md border border-slate-300 dark:border-slate-600 bg-surface hover:bg-surface-hover text-text-main shadow-2xs transition cursor-pointer">
                            &darr; Unduh Template Kosong
                        </button>
                        <button type="button" wire:click="downloadTemplate('prefill')" class="px-3 py-1.5 text-xs font-semibold rounded-md border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 text-emerald-700 dark:text-emerald-300 shadow-2xs transition cursor-pointer">
                            &darr; Unduh Sesuai Filter Wilayah Aktif
                        </button>
                    </div>
                </div>

                <!-- Langkah 3: Unggah Berkas -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-text-main">
                        3. Unggah Berkas Excel (.xlsx, .xls, .csv) <span class="text-rose-500">*</span>
                    </label>
                    <input type="file" wire:model="fileExcel" accept=".xlsx,.xls,.csv" class="file-input">
                    
                    <div wire:loading wire:target="fileExcel" class="text-xs text-primary flex items-center gap-1.5 pt-1">
                        <svg class="animate-spin h-3.5 w-3.5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Sedang membaca dan mengunggah berkas...</span>
                    </div>

                    @error('fileExcel')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ringkasan Hasil Import -->
                @if($importResult)
                    <div class="p-4 rounded-xl border {{ $importResult['updated_count'] > 0 ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800' : 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800' }} space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider {{ $importResult['updated_count'] > 0 ? 'text-emerald-800 dark:text-emerald-300' : 'text-amber-800 dark:text-amber-300' }}">
                                Hasil Import Terakhir:
                            </span>
                            <span class="text-xs font-mono font-bold">Total NIS: {{ $importResult['total_nis'] }}</span>
                        </div>
                        <p class="text-xs text-text-main">
                            &bull; <b>{{ $importResult['updated_count'] }}</b> siswa berhasil dialokasikan ke Rayon <b>{{ $importResult['rayon_nama'] }}</b>.
                        </p>
                        @if(!empty($importResult['unmatched_nis']))
                            <div class="pt-2 border-t border-amber-200 dark:border-amber-800/80">
                                <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold mb-1">
                                    ⚠️ {{ count($importResult['unmatched_nis']) }} NIS tidak ditemukan di sistem:
                                </p>
                                <div class="max-h-24 overflow-y-auto p-2 bg-surface rounded border border-border text-[11px] font-mono text-text-secondary">
                                    {{ implode(', ', $importResult['unmatched_nis']) }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3.5 bg-surface-hover/50 border-t border-border flex justify-end items-center gap-3">
                <button type="button" wire:click="closeImportModal" class="px-4 py-2 text-sm text-text-secondary hover:bg-slate-200 dark:hover:bg-slate-800 rounded-lg transition cursor-pointer">
                    Tutup
                </button>
                <button type="button" wire:click="importExcel" wire:loading.attr="disabled" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition disabled:opacity-50 cursor-pointer">
                    <span wire:loading.remove wire:target="importExcel">Proses Alokasi Rayon</span>
                    <span wire:loading wire:target="importExcel">Memproses...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Reset Plotting Rayon -->
    @if($showResetModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div class="w-full max-w-xl bg-surface rounded-2xl shadow-2xl border border-border overflow-hidden my-8">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-rose-50/50 dark:bg-rose-950/20">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-lg bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-bold text-text-main">Reset & Atur Ulang Plotting Rayon</h2>
                        <p class="text-xs text-text-secondary">Atur ulang atau kosongkan alokasi pembagian rayon siswa secara massal.</p>
                    </div>
                </div>
                <button type="button" wire:click="closeResetModal" class="text-slate-400 hover:text-text-main text-2xl font-bold cursor-pointer">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-5 text-sm">
                <!-- Pilihan 1: Cakupan Target Reset -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-text-main">
                        1. Pilih Siswa yang Di-reset (Cakupan Target):
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-border hover:bg-surface-hover/50 cursor-pointer transition {{ $resetScope === 'terpilih' ? 'bg-indigo-50/40 dark:bg-indigo-950/20 border-indigo-300 dark:border-indigo-700' : '' }}">
                            <input type="radio" wire:model.live="resetScope" value="terpilih" class="mt-0.5 text-primary focus:ring-primary" @if(empty($selectedSiswaIds)) disabled @endif>
                            <div class="text-xs">
                                <span class="font-bold text-text-main">Siswa yang Dicentang (Terpilih)</span>
                                <span class="text-text-secondary block">Hanya memproses {{ count($selectedSiswaIds) }} siswa yang saat ini Anda centang di tabel.</span>
                                @if(empty($selectedSiswaIds))
                                    <span class="text-[11px] text-amber-600 dark:text-amber-400 italic">Tidak ada siswa yang dicentang saat ini.</span>
                                @endif
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3 rounded-xl border border-border hover:bg-surface-hover/50 cursor-pointer transition {{ $resetScope === 'filter' ? 'bg-indigo-50/40 dark:bg-indigo-950/20 border-indigo-300 dark:border-indigo-700' : '' }}">
                            <input type="radio" wire:model.live="resetScope" value="filter" class="mt-0.5 text-primary focus:ring-primary">
                            <div class="text-xs">
                                <span class="font-bold text-text-main">Siswa Berdasarkan Filter Aktif</span>
                                <span class="text-text-secondary block">Memproses siswa yang cocok dengan filter kecamatan, kelurahan, status rayon, atau pencarian saat ini.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3 rounded-xl border border-border hover:bg-surface-hover/50 cursor-pointer transition {{ $resetScope === 'semua' ? 'bg-rose-50/40 dark:bg-rose-950/20 border-rose-300 dark:border-rose-700' : '' }}">
                            <input type="radio" wire:model.live="resetScope" value="semua" class="mt-0.5 text-rose-600 focus:ring-rose-500">
                            <div class="text-xs">
                                <span class="font-bold text-rose-700 dark:text-rose-400">Seluruh Siswa Aktif Sekolah (Global)</span>
                                <span class="text-text-secondary block">Memproses seluruh siswa aktif di sekolah tanpa memandang filter pencarian.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Pilihan 2: Metode Reset -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-text-main">
                        2. Pilih Metode Pengaturan Ulang:
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-border hover:bg-surface-hover/50 cursor-pointer transition {{ $resetMode === 'kosongkan' ? 'bg-rose-50/40 dark:bg-rose-950/20 border-rose-300 dark:border-rose-700' : '' }}">
                            <input type="radio" wire:model.live="resetMode" value="kosongkan" class="mt-0.5 text-rose-600 focus:ring-rose-500">
                            <div class="text-xs">
                                <span class="font-bold text-rose-700 dark:text-rose-400 block mb-0.5">Kosongkan Rayon</span>
                                <span class="text-text-secondary text-[11px] leading-relaxed">Lepas alokasi siswa dari rayon saat ini. Siswa kembali berstatus "Belum Memiliki Rayon".</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3 rounded-xl border border-border hover:bg-surface-hover/50 cursor-pointer transition {{ $resetMode === 'acak' ? 'bg-indigo-50/40 dark:bg-indigo-950/20 border-indigo-300 dark:border-indigo-700' : '' }}">
                            <input type="radio" wire:model.live="resetMode" value="acak" class="mt-0.5 text-indigo-600 focus:ring-indigo-500">
                            <div class="text-xs">
                                <span class="font-bold text-indigo-700 dark:text-indigo-400 block mb-0.5">Acak & Distribusi Ulang</span>
                                <span class="text-text-secondary text-[11px] leading-relaxed">Bagi dan alokasikan siswa secara acak merata ke seluruh rayon yang aktif terdaftar.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Ringkasan Dampak -->
                <div class="p-3.5 bg-slate-100 dark:bg-slate-800/60 rounded-xl border border-border flex items-center justify-between">
                    <div>
                        <span class="text-xs text-text-secondary block">Estimasi Siswa Terdampak:</span>
                        <span class="text-base font-bold text-text-main">
                            {{ $resetTargetCount }} siswa
                        </span>
                    </div>
                    <span class="px-2.5 py-1 text-xs rounded-full font-bold {{ $resetMode === 'kosongkan' ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300' }}">
                        Mode: {{ $resetMode === 'kosongkan' ? 'Dikosongkan' : 'Distribusi Acak' }}
                    </span>
                </div>

                <!-- Konfirmasi Khusus untuk Seluruh Siswa -->
                @if($resetScope === 'semua')
                    <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl space-y-2">
                        <div class="flex items-center gap-2 text-rose-700 dark:text-rose-300 font-bold text-xs uppercase tracking-wide">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Perhatian: Aksi Skala Penuh</span>
                        </div>
                        <p class="text-xs text-rose-800 dark:text-rose-200">
                            Tindakan ini akan mempengaruhi seluruh siswa aktif di sekolah. Ketik kata <b>RESET</b> di bawah ini untuk mengonfirmasi:
                        </p>
                        <input type="text" wire:model="resetConfirmText" placeholder="Ketik RESET" class="w-full text-sm font-mono tracking-wider rounded-lg border-rose-300 dark:border-rose-700 bg-surface text-text-main focus:ring-rose-500">
                        @error('resetConfirmText')
                            <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3.5 bg-surface-hover/50 border-t border-border flex justify-end items-center gap-3">
                <button type="button" wire:click="closeResetModal" class="px-4 py-2 text-sm text-text-secondary hover:bg-slate-200 dark:hover:bg-slate-800 rounded-lg transition cursor-pointer">
                    Batal
                </button>
                <button type="button" wire:click="executeResetRayon" wire:loading.attr="disabled" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-sm transition disabled:opacity-50 cursor-pointer">
                    <span wire:loading.remove wire:target="executeResetRayon">Konfirmasi & Eksekusi Reset</span>
                    <span wire:loading wire:target="executeResetRayon">Memproses...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
