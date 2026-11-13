<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Guru & Tenaga Kependidikan (GTK)</h1>
            <p class="text-sm text-text-secondary">Pengelolaan profil pendidik, staf kependidikan, serta akun pengguna sistem.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(($unlinkedCount ?? 0) > 0 && auth()->user()->hasRole('Super Admin'))
                <button wire:click="generateAccounts" wire:confirm="Buat akun login otomatis untuk {{ $unlinkedCount }} pegawai yang belum memiliki akun?" class="btn bg-amber-500 hover:bg-amber-600 text-white flex items-center gap-1.5 text-xs shadow-sm" title="Generate akun untuk pegawai yang belum punya akun">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>Buat Akun GTK ({{ $unlinkedCount }})</span>
                </button>
            @endif

            @can('kepegawaian.pegawai.import')
                <button wire:click="openImportModal" class="btn btn-secondary flex items-center gap-1.5 text-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Import Data</span>
                </button>
            @endcan

            @can('kepegawaian.pegawai.ekspor')
                <div x-data="{ openExport: false }" class="relative inline-block text-left">
                    <button @click="openExport = !openExport" type="button" class="btn btn-secondary flex items-center gap-1.5 text-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Export Data</span>
                        <svg class="w-3 h-3 ml-0.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openExport"
                         @click.away="openExport = false"
                         x-cloak
                         class="absolute right-0 z-30 mt-2 w-48 origin-top-right rounded-lg border border-border bg-surface p-1 shadow-lg">
                        <button wire:click="exportCsv" @click="openExport = false" type="button" class="w-full text-left px-3 py-2 text-xs text-text-main hover:bg-surface-hover rounded-md flex items-center gap-2">
                            <span class="font-bold text-emerald-600">CSV</span>
                            <span>Download Format CSV</span>
                        </button>
                        <button wire:click="exportXlsx" @click="openExport = false" type="button" class="w-full text-left px-3 py-2 text-xs text-text-main hover:bg-surface-hover rounded-md flex items-center gap-2">
                            <span class="font-bold text-primary">XLSX</span>
                            <span>Download Format Excel</span>
                        </button>
                    </div>
                </div>
            @endcan

            @can('kepegawaian.pegawai.cetak')
                <button wire:click="cetakDaftar" class="btn btn-secondary flex items-center gap-1.5 text-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak PDF</span>
                </button>
            @endcan

            @can('kepegawaian.pegawai.tambah')
                <a href="{{ route('kepegawaian.pegawai.create') }}" class="btn btn-primary text-xs flex items-center gap-1.5">
                    <span>+</span> Tambah Pegawai
                </a>
            @endcan
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 text-sm text-emerald-700 bg-emerald-50 dark:bg-emerald-950/40 dark:text-emerald-300 rounded-lg border border-emerald-200 dark:border-emerald-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    <!-- Search & Filter Card -->
    <div class="p-4 card flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, NIY, NIP, NUPTK, atau NIK..." class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
        </div>
        <div class="sm:w-48">
            <select wire:model.live="filterGtk" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                <option value="">-- Semua Jenis GTK --</option>
                <option value="Guru">Guru (Pendidik)</option>
                <option value="Tendik">Tendik (Staf Kependidikan)</option>
            </select>
        </div>
        <div class="sm:w-48">
            <select wire:model.live="filterStatus" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                <option value="">-- Semua Status --</option>
                <option value="GTY">GTY (Guru Tetap Yayasan)</option>
                <option value="STY">STY (Staf Tetap Yayasan)</option>
                <option value="GTT">GTT (Guru Tidak Tetap)</option>
                <option value="STT">STT (Staf Tidak Tetap)</option>
                <option value="PTT">PTT (Pegawai Tidak Tetap)</option>
                <option value="PNS">PNS DPK</option>
                <option value="PPPK">PPPK</option>
            </select>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-xs">
                <thead class="bg-surface-hover/80 text-text-secondary font-medium">
                    <tr>
                        <x-table.sortable-th field="nama_lengkap" :sortField="$sortField" :sortDirection="$sortDirection" class="px-5 py-3">Nama Pegawai & Kontak</x-table.sortable-th>
                        <x-table.sortable-th field="nip" :sortField="$sortField" :sortDirection="$sortDirection" class="px-5 py-3">Identitas Resmi (NIP / NUPTK / NIK)</x-table.sortable-th>
                        <x-table.sortable-th field="jenis_gtk" :sortField="$sortField" :sortDirection="$sortDirection" class="px-5 py-3">Jenis & Status</x-table.sortable-th>
                        <x-table.sortable-th field="user_id" :sortField="$sortField" :sortDirection="$sortDirection" class="px-5 py-3">Akun Login</x-table.sortable-th>
                        <x-table.sortable-th field="is_active" :sortField="$sortField" :sortDirection="$sortDirection" class="px-5 py-3">Status</x-table.sortable-th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-text-main">
                    @forelse($pegawais as $p)
                        <tr class="hover:bg-surface-hover/40 transition">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $p->foto_utama_url }}" alt="{{ $p->nama_lengkap }}" class="w-10 h-10 rounded-xl object-cover border border-border shrink-0">
                                    <div class="min-w-0">
                                        <a href="{{ route('kepegawaian.pegawai.show', $p->id) }}" class="font-bold text-text-main hover:text-primary transition block truncate">
                                            {{ $p->nama_gelar }}
                                        </a>
                                        <div class="text-[11px] font-mono text-text-secondary flex items-center gap-1.5 mt-0.5">
                                            <span class="font-semibold text-text-main">NIY: {{ $p->niy_label }}</span>
                                            @if(empty($p->niy))
                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">NIY Belum Terbit</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-text-muted truncate mt-0.5">
                                            {{ $p->email ?? '-' }} &bull; {{ $p->no_hp ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-[11px] space-y-0.5">
                                <div><span class="text-text-muted">NIP:</span> {{ $p->nip ?? '-' }}</div>
                                <div><span class="text-text-muted">NUPTK:</span> {{ $p->nuptk ?? '-' }}</div>
                                <div class="flex items-center gap-1">
                                    <span class="text-text-muted">NIK:</span>
                                    <span>{{ \App\Core\Support\DataMasker::maskNik($p->nik) }}</span>
                                    @if($p->perlu_verifikasi_nik)
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-semibold bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300" title="Digit terakhir NIK terpotong (0000) oleh Excel, perlu dicek dari KTP asli">&#9888; Cek KTP</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ strtolower($p->jenis_gtk) === 'guru' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800' }}">
                                    {{ $p->jenis_gtk }}
                                </span>
                                <div class="text-[11px] text-text-secondary mt-1 font-semibold uppercase">{{ strtoupper($p->status_kepegawaian) }}</div>
                                @if($p->jabatan_struktural)
                                    <div class="text-[10px] text-text-muted truncate max-w-xs mt-0.5" title="{{ $p->jabatan_struktural }}">{{ $p->jabatan_struktural }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @if($p->user)
                                    <div class="font-mono text-xs font-semibold text-text-main flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>{{ $p->user->username }}</span>
                                    </div>
                                    <div class="text-[10px] text-primary dark:text-indigo-400 mt-0.5">{{ $p->user->getRoleNames()->join(', ') }}</div>
                                @else
                                    <span class="text-xs italic text-text-muted">Belum ada akun</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @if($p->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-text-muted">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2 whitespace-nowrap">
                                @can('kepegawaian.pegawai.lihat')
                                    <a href="{{ route('kepegawaian.pegawai.show', $p->id) }}" class="text-xs font-semibold text-primary hover:underline">Lihat</a>
                                @endcan
                                @can('kepegawaian.pegawai.edit')
                                    <a href="{{ route('kepegawaian.pegawai.edit', $p->id) }}" class="text-xs font-semibold text-text-secondary hover:text-text-main">Edit</a>
                                @endcan
                                @can('kepegawaian.pegawai.hapus')
                                    <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus data pegawai {{ $p->nama_lengkap }} beserta akun loginnya?" class="text-xs font-semibold text-rose-600 hover:text-rose-800">Hapus</button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-text-muted">Belum ada data guru atau tenaga kependidikan yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3.5 border-t border-border">
            {{ $pegawais->links() }}
        </div>
    </div>

    <!-- Modal Import Data -->
    @if($showImportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-2xl bg-surface border border-border shadow-2xl overflow-hidden p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-border">
                    <div class="flex items-center gap-2">
                        <div class="p-2 rounded-lg bg-primary-soft text-primary">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-text-main">Import Data Pegawai (GTK)</h3>
                            <p class="text-xs text-text-muted">Unggah file CSV atau Excel (.xlsx) untuk sinkronisasi data.</p>
                        </div>
                    </div>
                    <button wire:click="closeImportModal" type="button" class="text-text-muted hover:text-text-main p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4 text-xs">
                    <div class="rounded-lg bg-surface-hover/70 p-3 border border-border/80 space-y-2">
                        <div class="font-semibold text-text-main flex items-center justify-between">
                            <span>Panduan Format Data</span>
                            <button type="button" wire:click="downloadTemplateCsv" class="text-primary hover:underline font-bold flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span>Unduh Template CSV</span>
                            </button>
                        </div>
                        <ul class="list-disc pl-4 space-y-1 text-text-muted">
                            <li>Mendukung file format <b>.csv</b>, <b>.xlsx</b>, atau <b>.xls</b>.</li>
                            <li>Data yang sudah ada (berdasarkan NIY / NIK / Nama) akan otomatis diperbarui (*update*). Data baru akan ditambahkan (*insert*).</li>
                            <li>Format tanggal mendukung <i>YYYY-MM-DD</i>, teks bahasa Indonesia/Inggris, atau format standar Excel.</li>
                        </ul>
                    </div>

                    <div>
                        <label class="block font-semibold text-text-main mb-1.5">Pilih File CSV / Excel</label>
                        <input type="file" wire:model="fileImport" accept=".csv, .xlsx, .xls, text/csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" class="w-full text-xs text-text-main file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-hover border border-border rounded-lg bg-surface cursor-pointer">
                        @error('fileImport')
                            <p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div wire:loading wire:target="fileImport, processImport" class="p-3 bg-primary-soft/50 rounded-lg text-primary text-xs flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Memproses file... Mohon tunggu sebentar.</span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                    <button type="button" wire:click="closeImportModal" class="btn btn-secondary text-xs">
                        Batal
                    </button>
                    <button type="button" wire:click="processImport" wire:loading.attr="disabled" class="btn btn-primary text-xs flex items-center gap-1.5">
                        <span>Unggah & Impor</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
