<div class="space-y-6">
 <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
 <div>
 <h1 class="text-2xl font-bold tracking-tight text-text-main">Plotting Rayon Massal</h1>
 <p class="text-sm text-text-secondary">Alokasikan siswa ke Rayon wilayah tempat tinggal secara massal berdasarkan Kecamatan / Kelurahan.</p>
 </div>
 <div class="flex flex-wrap items-center gap-2">
 <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition">
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
 <div class="flex items-center gap-3">
 <span class="text-xs text-text-secondary">
 Terpilih: <b>{{ count($selectedSiswaIds) }}</b> siswa
 </span>
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
 <div class="card overflow-hidden">
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
                <button type="button" wire:click="closeImportModal" class="text-slate-400 hover:text-text-main text-2xl font-bold">&times;</button>
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
                        <button type="button" wire:click="downloadTemplate('kosong')" class="px-3 py-1.5 text-xs font-semibold rounded-md border border-slate-300 dark:border-slate-600 bg-surface hover:bg-surface-hover text-text-main shadow-2xs transition">
                            &darr; Unduh Template Kosong
                        </button>
                        <button type="button" wire:click="downloadTemplate('prefill')" class="px-3 py-1.5 text-xs font-semibold rounded-md border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 text-emerald-700 dark:text-emerald-300 shadow-2xs transition">
                            &darr; Unduh Sesuai Filter Wilayah Aktif
                        </button>
                    </div>
                </div>

                <!-- Langkah 3: Unggah Berkas -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-text-main">
                        3. Unggah Berkas Excel (.xlsx, .xls, .csv) <span class="text-rose-500">*</span>
                    </label>
                    <input type="file" wire:model="fileExcel" accept=".xlsx,.xls,.csv" class="w-full text-xs text-text-main file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-hover border border-border rounded-lg p-1.5 bg-surface cursor-pointer">
                    
                    <div wire:loading wire:target="fileExcel" class="text-xs text-primary flex items-center gap-1.5 pt-1">
                        <svg class="animate-spin h-3.5 w-3.5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
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
                <button type="button" wire:click="closeImportModal" class="px-4 py-2 text-sm text-text-secondary hover:bg-slate-200 dark:hover:bg-slate-800 rounded-lg transition">
                    Tutup
                </button>
                <button type="button" wire:click="importExcel" wire:loading.attr="disabled" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="importExcel">Proses Alokasi Rayon</span>
                    <span wire:loading wire:target="importExcel">Memproses...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
