<div class="space-y-6">
    <!-- Header Navigasi -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-border pb-4">
        <div>
            <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Keanggotaan Siswa Seni Budaya
            </h2>
            <p class="text-sm text-text-secondary mt-1">
                Daftarkan siswa ke cabang Seni Budaya per semester. Setiap siswa wajib memilih tepat 1 cabang seni.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-1.5 bg-surface-hover p-1 rounded-xl border border-border">
            <a href="{{ route('seni-budaya.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Dashboard
            </a>
            <a href="{{ route('seni-budaya.cabang') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Cabang Seni
            </a>
            <a href="{{ route('seni-budaya.anggota') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold bg-white dark:bg-gray-800 text-primary shadow-xs">
                Keanggotaan Siswa
            </a>
            <a href="{{ route('seni-budaya.tp') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Tujuan Pembelajaran (TP)
            </a>
            <a href="{{ route('seni-budaya.rekap') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Rekapitulasi Nilai
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl text-rose-800 dark:text-rose-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
        </div>
    @endif

    <!-- Toolbar Filter & Tombol Aksi -->
    <div class="card p-4 border border-border space-y-3">
        <div class="flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-3">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 flex-1">
                <!-- Filter Cabang -->
                <select wire:model.live="filterCabangId" class="input text-sm">
                    <option value="all">Semua Cabang Seni Budaya</option>
                    @foreach($cabangs as $c)
                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                    @endforeach
                </select>

                <!-- Filter Rombel -->
                <select wire:model.live="filterRombelId" class="input text-sm">
                    <option value="all">Semua Rombel / Kelas</option>
                    @foreach($rombels as $r)
                        <option value="{{ $r->id }}">{{ $r->nama }}</option>
                    @endforeach
                </select>

                <!-- Search Input -->
                <div class="relative">
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Cari nama / NISN siswa..."
                           class="input pl-9 text-sm w-full">
                    <svg class="w-4 h-4 text-text-muted absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Tombol Aksi Kanan -->
            <div class="flex items-center gap-2 justify-end">
                <a href="{{ route('seni-budaya.template.anggota', ['rombel_id' => $filterRombelId !== 'all' ? $filterRombelId : null]) }}"
                   class="btn-secondary text-sm flex items-center gap-1.5"
                   title="Unduh template Excel untuk pendaftaran">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download Template
                </a>
                <button wire:click="openImportModal" class="btn-secondary text-sm flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/></svg>
                    Import Excel
                </button>
                <button wire:click="openEnrollModal" class="btn-primary text-sm flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Daftar Manual
                </button>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Siswa -->
    <div class="card overflow-hidden border border-border">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-hover text-text-secondary text-xs uppercase tracking-wider border-b border-border">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">NISN / NIS</th>
                        <th class="px-4 py-3">Nama Siswa</th>
                        <th class="px-4 py-3">Kelas / Rombel</th>
                        <th class="px-4 py-3">Cabang Seni Budaya</th>
                        <th class="px-4 py-3 text-center">Presensi (%)</th>
                        <th class="px-4 py-3 text-center">Nilai Akhir</th>
                        <th class="px-4 py-3 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-text-main">
                    @forelse($anggotas as $index => $a)
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="px-4 py-3 text-center text-text-muted">{{ $anggotas->firstItem() + $index }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-text-secondary">
                                {{ $a->siswa?->nisn ?? '-' }} <span class="text-text-muted">/ {{ $a->siswa?->nipd ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 font-medium">{{ $a->siswa?->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-text-secondary">{{ $a->siswa?->rombel?->nama ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-md bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                    {{ $a->seniBudaya?->nama ?? 'Cabang Tidak Dikenal' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-medium">
                                {{ number_format($a->persentase_kehadiran, 0) }}%
                            </td>
                            <td class="px-4 py-3 text-center font-bold {{ $a->nilai_akhir >= ($a->seniBudaya?->kkm ?? 75) ? 'text-emerald-600 dark:text-emerald-400' : 'text-text-secondary' }}">
                                {{ $a->nilai_akhir !== null ? number_format($a->nilai_akhir, 0) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button wire:click="openMoveModal({{ $a->id }})" class="p-1.5 text-text-secondary hover:text-primary rounded-md hover:bg-surface" title="Pindah Cabang">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    </button>
                                    <button wire:click="removeMember({{ $a->id }})" wire:confirm="Hapus keanggotaan siswa ini dari Seni Budaya?" class="p-1.5 text-text-secondary hover:text-rose-600 rounded-md hover:bg-surface" title="Keluarkan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-text-muted">
                                Tidak ada data siswa yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($anggotas->hasPages())
            <div class="p-4 border-t border-border">
                {{ $anggotas->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Daftar Manual -->
    @if($showEnrollModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" x-cloak>
            <div class="card max-w-md w-full p-6 space-y-4 shadow-xl border border-border">
                <div class="flex justify-between items-center border-b border-border pb-3">
                    <h3 class="text-lg font-bold text-text-main">Pendaftaran Siswa Manual</h3>
                    <button type="button" wire:click="$set('showEnrollModal', false)" class="text-text-muted hover:text-text-main text-xl leading-none">&times;</button>
                </div>

                <form wire:submit="saveEnrollment" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Pilih Siswa <span class="text-rose-500">*</span></label>
                        <select wire:model="selectedSiswaId" class="input text-sm w-full">
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($availableSiswas as $s)
                                <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->rombel?->nama ?? '-' }}) - NISN: {{ $s->nisn }}</option>
                            @endforeach
                        </select>
                        @error('selectedSiswaId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Cabang Seni Budaya <span class="text-rose-500">*</span></label>
                        <select wire:model="targetCabangId" class="input text-sm w-full">
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($cabangs as $c)
                                <option value="{{ $c->id }}">{{ $c->nama }} ({{ $c->kode }})</option>
                            @endforeach
                        </select>
                        @error('targetCabangId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="$set('showEnrollModal', false)" class="btn-secondary text-sm">Batal</button>
                        <button type="submit" class="btn-primary text-sm flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Daftarkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Pindah Cabang -->
    @if($showMoveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" x-cloak>
            <div class="card max-w-sm w-full p-6 space-y-4 shadow-xl border border-border">
                <div class="flex justify-between items-center border-b border-border pb-3">
                    <h3 class="text-lg font-bold text-text-main">Pindahkan Cabang Siswa</h3>
                    <button type="button" wire:click="$set('showMoveModal', false)" class="text-text-muted hover:text-text-main text-xl leading-none">&times;</button>
                </div>

                <form wire:submit="saveMove" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Cabang Seni Budaya Baru <span class="text-rose-500">*</span></label>
                        <select wire:model="newCabangId" class="input text-sm w-full">
                            @foreach($cabangs as $c)
                                <option value="{{ $c->id }}">{{ $c->nama }}</option>
                            @endforeach
                        </select>
                        @error('newCabangId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="$set('showMoveModal', false)" class="btn-secondary text-sm">Batal</button>
                        <button type="submit" class="btn-primary text-sm">Pindahkan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Import Excel -->
    @if($showImportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" x-cloak>
            <div class="card max-w-lg w-full p-6 space-y-4 shadow-xl border border-border">
                <div class="flex justify-between items-center border-b border-border pb-3">
                    <h3 class="text-lg font-bold text-text-main">Import Keanggotaan dari Excel</h3>
                    <button type="button" wire:click="$set('showImportModal', false)" class="text-text-muted hover:text-text-main text-xl leading-none">&times;</button>
                </div>

                <div class="space-y-4">
                    <div class="p-3 bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 rounded-lg text-xs text-blue-800 dark:text-blue-200 space-y-1">
                        <p class="font-semibold">Petunjuk Import:</p>
                        <ol class="list-decimal list-inside space-y-0.5">
                            <li>Unduh file template Excel melalui tombol "Download Template".</li>
                            <li>Isi kolom "Cabang Seni Budaya" sesuai nama/kode pada Sheet Referensi.</li>
                            <li>Unggah kembali file tersebut di bawah ini.</li>
                        </ol>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Pilih File Excel (.xlsx / .xls)</label>
                        <input type="file" wire:model="importFile" accept=".xlsx, .xls" class="file-input">
                        @error('importFile') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        <div wire:loading wire:target="importFile" class="text-xs text-primary mt-1">Mengunggah file...</div>
                    </div>

                    @if($importSummary)
                        <div class="p-3 bg-surface-hover rounded-lg border border-border text-xs space-y-1">
                            <p class="font-semibold text-text-main">Ringkasan Hasil Import:</p>
                            <p class="text-emerald-600 font-medium">&bull; Siswa baru terdaftar: {{ $importSummary['success_count'] }}</p>
                            <p class="text-blue-600 font-medium">&bull; Cabang siswa diperbarui: {{ $importSummary['updated_count'] }}</p>
                            @if($importSummary['failed_count'] > 0)
                                <p class="text-rose-600 font-medium">&bull; Baris gagal: {{ $importSummary['failed_count'] }}</p>
                                <ul class="list-disc list-inside text-rose-500 max-h-24 overflow-y-auto mt-1">
                                    @foreach($importSummary['failed_rows'] as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif

                    <div class="flex justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="$set('showImportModal', false)" class="btn-secondary text-sm">Tutup</button>
                        <button type="button" wire:click="processImport" wire:loading.attr="disabled" class="btn-primary text-sm flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/></svg>
                            Mulai Proses Import
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
