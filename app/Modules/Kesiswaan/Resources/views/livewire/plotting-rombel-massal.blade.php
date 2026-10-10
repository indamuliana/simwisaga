<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Plotting Rombel Massal (Kelas)</h1>
            <p class="text-sm text-text-secondary">Alokasikan siswa ke Rombel / Kelas secara massal atau melalui import berkas Excel.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" wire:click="openImportModal" class="btn btn-primary inline-flex items-center gap-1.5 px-3.5 py-2 text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <span>+ Import Excel</span>
            </button>
            <a href="{{ route('kesiswaan.siswa') }}" class="btn btn-secondary inline-flex items-center text-sm font-semibold text-text-secondary hover:text-text-main px-3 py-2">
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

    <!-- Action Bar: Target Rombel Selection -->
    <div class="p-4 card bg-surface-raised border border-border flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-xs font-bold uppercase tracking-wider text-primary">
                Pilih Rombel Tujuan:
            </span>
            <select wire:model="targetRombelId" class="text-sm font-semibold rounded-lg border-border bg-surface text-text-main">
                <option value="">-- Pilih Rombel / Kelas --</option>
                @foreach($rombels as $rb)
                    <option value="{{ $rb->id }}">
                        {{ $rb->nama }} (Terisi: {{ $rb->siswas_count }} / {{ $rb->kapasitas ?? 36 }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-text-secondary">
                Terpilih: <b>{{ count($selectedSiswaIds) }}</b> siswa
            </span>
            <button wire:click="applyPlotting" wire:confirm="Alokasikan {{ count($selectedSiswaIds) }} siswa terpilih ke Rombel tujuan?" class="btn btn-primary" @if(empty($selectedSiswaIds) || !$targetRombelId) disabled @endif>
                Terapkan Rombel ke Siswa Terpilih
            </button>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="p-4 card grid grid-cols-1 sm:grid-cols-5 gap-4">
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Status Rombel</label>
            <select wire:model.live="filterStatusRombel" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                <option value="belum_ada">Belum Memiliki Rombel</option>
                <option value="semua">Semua Siswa</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Tingkat</label>
            <select wire:model.live="filterTingkat" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                <option value="">-- Semua Tingkat --</option>
                <option value="X">Kelas X</option>
                <option value="XI">Kelas XI</option>
                <option value="XII">Kelas XII</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Jurusan</label>
            <select wire:model.live="filterJurusan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                <option value="">-- Semua Jurusan --</option>
                @foreach($jurusans as $jr)
                    <option value="{{ $jr->id }}">{{ $jr->nama }} ({{ $jr->kode }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Rayon</label>
            <select wire:model.live="filterRayon" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                <option value="">-- Semua Rayon --</option>
                @foreach($rayons as $ry)
                    <option value="{{ $ry->id }}">{{ $ry->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Pencarian</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nama, NIS, atau NISN..." class="w-full text-sm rounded-lg border-border bg-surface text-text-main placeholder-text-muted">
        </div>
    </div>

    <!-- Siswa Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-surface-hover/80 text-text-secondary border-b border-border">
                    <tr>
                        <th class="p-3 w-10 text-center">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-border text-primary focus:ring-primary">
                        </th>
                        <th class="p-3">No</th>
                        <th class="p-3">NIS / NISN</th>
                        <th class="p-3">Nama Siswa</th>
                        <th class="p-3 text-center">JK</th>
                        <th class="p-3">Rombel Saat Ini</th>
                        <th class="p-3">Rayon</th>
                        <th class="p-3">Program</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($siswas as $idx => $s)
                        <tr class="hover:bg-surface-hover/40 transition {{ in_array((string)$s->id, $selectedSiswaIds) ? 'bg-primary/5' : '' }}">
                            <td class="p-3 text-center">
                                <input type="checkbox" wire:model.live="selectedSiswaIds" value="{{ (string)$s->id }}" class="rounded border-border text-primary focus:ring-primary">
                            </td>
                            <td class="p-3 text-text-muted">{{ $siswas->firstItem() + $idx }}</td>
                            <td class="p-3 font-mono text-xs">
                                <span class="font-bold text-text-main">{{ $s->nipd ?: '-' }}</span>
                                <span class="text-text-muted block text-[11px]">{{ $s->nisn }}</span>
                            </td>
                            <td class="p-3 font-semibold text-text-main">{{ $s->nama }}</td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-0.5 text-xs rounded font-medium {{ $s->jk === 'L' ? 'bg-blue-500/10 text-blue-400' : 'bg-pink-500/10 text-pink-400' }}">
                                    {{ $s->jk }}
                                </span>
                            </td>
                            <td class="p-3">
                                @if($s->rombel)
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        {{ $s->rombel->nama }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        Belum Ditentukan
                                    </span>
                                @endif
                            </td>
                            <td class="p-3 text-text-secondary text-xs">{{ $s->rayon?->nama ?? '-' }}</td>
                            <td class="p-3 text-text-secondary text-xs">{{ $s->program?->nama ?? 'Reguler' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-text-secondary">
                                Tidak ada data siswa yang cocok dengan filter yang dipilih.
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

    <!-- Modal Import Excel -->
    @if($showImportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="card w-full max-w-xl p-6 space-y-5 bg-surface border border-border shadow-2xl" @click.outside="$wire.closeImportModal()">
                <div class="flex items-center justify-between pb-3 border-b border-border">
                    <h3 class="text-lg font-bold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Import Plotting Rombel via Excel
                    </h3>
                    <button type="button" wire:click="closeImportModal" class="text-text-muted hover:text-text-main text-lg font-bold">&times;</button>
                </div>

                <div class="space-y-4">
                    <!-- Target Rombel Selection -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1">1. Pilih Rombel Tujuan</label>
                        <select wire:model="importTargetRombelId" class="w-full text-sm font-semibold rounded-lg border-border bg-surface text-text-main">
                            <option value="">-- Pilih Rombel Tujuan --</option>
                            @foreach($rombels as $rb)
                                <option value="{{ $rb->id }}">
                                    {{ $rb->nama }} (Terisi: {{ $rb->siswas_count }} / {{ $rb->kapasitas ?? 36 }})
                                </option>
                            @endforeach
                        </select>
                        @error('importTargetRombelId') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Download Templates -->
                    <div class="p-3 bg-surface-hover/60 rounded-lg space-y-2 border border-border">
                        <span class="text-xs font-semibold text-text-secondary block">2. Unduh Format Template:</span>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" wire:click="downloadTemplate('kosong')" class="btn btn-secondary text-xs inline-flex items-center gap-1.5 py-1.5 px-3">
                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Template Kosong (.xlsx)
                            </button>
                            <button type="button" wire:click="downloadTemplate('prefill')" class="btn btn-secondary text-xs inline-flex items-center gap-1.5 py-1.5 px-3">
                                <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Template Prefill Sesuai Filter (.xlsx)
                            </button>
                        </div>
                    </div>

                    <!-- Upload Form -->
                    <form wire:submit="importExcel" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1">3. Unggah Berkas Excel (.xlsx, .xls, .csv)</label>
                            <div class="border-2 border-dashed border-border rounded-xl p-6 text-center hover:border-primary transition">
                                <input type="file" wire:model="fileExcel" accept=".xlsx,.xls,.csv" id="excelFilePlotting" class="hidden">
                                <label for="excelFilePlotting" class="cursor-pointer space-y-1 block">
                                    <div class="text-2xl text-primary">&#128196;</div>
                                    <span class="text-xs font-semibold text-text-main block">
                                        {{ $fileExcel ? $fileExcel->getClientOriginalName() : 'Klik untuk memilih berkas' }}
                                    </span>
                                    <span class="text-[11px] text-text-muted block">Kolom A: NIS (Nomor Induk Siswa)</span>
                                </label>
                            </div>
                            @error('fileExcel') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Import Result Info -->
                        @if($importResult)
                            <div class="p-3 rounded-lg border {{ $importResult['updated_count'] > 0 ? 'bg-success-soft border-success/30 text-success' : 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-900 text-rose-300' }} text-xs space-y-1">
                                <div class="font-bold">
                                    Hasil: {{ $importResult['updated_count'] }} dari {{ $importResult['total_nis'] }} NIS berhasil dialokasikan ke {{ $importResult['rombel_nama'] }}.
                                </div>
                                @if($importResult['is_over_capacity'])
                                    <div class="text-amber-400 font-semibold">
                                        Perhatian: Rombel saat ini terisi {{ $importResult['total_terisi'] }} siswa (melebihi kapasitas standar {{ $importResult['kapasitas'] }}).
                                    </div>
                                @endif
                                @if(!empty($importResult['unmatched_nis']))
                                    <div class="text-[11px] text-rose-400">
                                        NIS tidak ditemukan ({{ count($importResult['unmatched_nis']) }}): {{ implode(', ', array_slice($importResult['unmatched_nis'], 0, 10)) }}{{ count($importResult['unmatched_nis']) > 10 ? '...' : '' }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" wire:click="closeImportModal" class="btn btn-secondary text-xs px-4 py-2">
                                Tutup
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary text-xs px-5 py-2">
                                <span wire:loading.remove>Proses Import Rombel</span>
                                <span wire:loading>Memproses Data...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
