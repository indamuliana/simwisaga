<div class="space-y-4">
    <!-- Header Page & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-border">
        <div>
            <div class="flex items-center gap-2 text-xs text-text-secondary mb-1">
                <a href="{{ route('penilaian.mapil.index') }}" class="hover:text-primary transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Daftar Mapil
                </a>
                <span>/</span>
                <span class="text-text-main font-semibold">Pengolahan Nilai</span>
            </div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <h1 class="text-xl font-bold tracking-tight text-text-main">{{ $mapelPilihan->nama }}</h1>
                <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-surface-variant text-text-secondary border border-border">
                    {{ $mapelPilihan->kode }}
                </span>
                <span class="text-xs font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                    KKM {{ $kkm }}
                </span>
                <span class="text-xs font-semibold px-2 py-0.5 rounded bg-primary/10 text-primary">
                    {{ count($siswas) }} Siswa Terdaftar
                </span>
            </div>
            <p class="text-xs text-text-secondary mt-1">
                Guru Pengampu: <span class="font-medium text-text-main">{{ $mapelPilihan->guru?->nama_lengkap ?? '-' }}</span>
                @if($mapelPilihan->guruPendamping)
                    | Pendamping: <span class="font-medium text-text-main">{{ $mapelPilihan->guruPendamping->nama_lengkap }}</span>
                @endif
                | Tingkat: <span class="font-medium text-text-main">{{ $mapelPilihan->tingkat_label }}</span>
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <button wire:click="exportTemplate" type="button" class="inline-flex items-center gap-1.5 py-1.5 px-3 text-xs font-medium rounded-lg border border-border bg-surface text-text-main hover:bg-surface-variant transition shadow-2xs">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download Format Excel
            </button>
            <button wire:click="$set('showImportModal', true)" type="button" class="inline-flex items-center gap-1.5 py-1.5 px-3 text-xs font-medium rounded-lg border border-border bg-surface text-text-main hover:bg-surface-variant transition shadow-2xs">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"></path></svg>
                Import Nilai Excel
            </button>
        </div>
    </div>

    <!-- Alert Status & Error -->
    @if (session('status'))
        <div class="p-3 text-xs text-emerald-800 bg-emerald-50 rounded-lg border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800 flex items-center justify-between">
            <span>{{ session('status') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="p-3 text-xs text-rose-800 bg-rose-50 rounded-lg border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800">
            {{ session('error') }}
        </div>
    @endif

    <!-- Tab Navigation -->
    <div class="flex border-b border-border space-x-1">
        <button wire:click="switchTab('formatif')" class="py-2.5 px-4 text-xs font-bold border-b-2 transition flex items-center gap-1.5 {{ $activeTab === 'formatif' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            Asesmen Formatif & Sumatif
        </button>
        <button wire:click="switchTab('tp')" class="py-2.5 px-4 text-xs font-bold border-b-2 transition flex items-center gap-1.5 {{ $activeTab === 'tp' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Tujuan Pembelajaran (TP)
            <span class="ml-1 text-[10px] px-1.5 py-0.2 rounded-full bg-surface-variant font-semibold">{{ count($allTps) }}</span>
        </button>
        <button wire:click="switchTab('rekap')" class="py-2.5 px-4 text-xs font-bold border-b-2 transition flex items-center gap-1.5 {{ $activeTab === 'rekap' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            Rekap Nilai & Rapor
        </button>
    </div>

    <!-- TAB 1: ASESMEN FORMATIF & SUMATIF -->
    @if($activeTab === 'formatif')
        <div class="space-y-3">
            <!-- Filter View Periode & Info KKM -->
            <div class="card p-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-text-secondary uppercase">Tampilan:</span>
                    <div class="inline-flex rounded-lg border border-border p-0.5 bg-surface-variant">
                        <button type="button" wire:click="$set('tabelTahunMode', 'semua')" class="px-2.5 py-1 text-xs font-semibold rounded-md transition {{ $tabelTahunMode === 'semua' ? 'bg-surface text-primary shadow-2xs font-bold' : 'text-text-secondary hover:text-text-main' }}">
                            1 Tahun Penuh
                        </button>
                        <button type="button" wire:click="$set('tabelTahunMode', 'ganjil')" class="px-2.5 py-1 text-xs font-semibold rounded-md transition {{ $tabelTahunMode === 'ganjil' ? 'bg-surface text-primary shadow-2xs font-bold' : 'text-text-secondary hover:text-text-main' }}">
                            Semester Ganjil
                        </button>
                        <button type="button" wire:click="$set('tabelTahunMode', 'genap')" class="px-2.5 py-1 text-xs font-semibold rounded-md transition {{ $tabelTahunMode === 'genap' ? 'bg-surface text-primary shadow-2xs font-bold' : 'text-text-secondary hover:text-text-main' }}">
                            Semester Genap
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-[11px] text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Nilai kosong atau &lt; KKM ({{ $kkm }}) menyebabkan Nilai Akhir menjadi (-) dan belum kompeten.
                    </span>
                    <button wire:click="saveNilai" type="button" class="py-1.5 px-4 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-2xs flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Nilai
                    </button>
                </div>
            </div>

            <!-- Tabel Grid Nilai Siswa -->
            @if(empty($siswas))
                <div class="card p-8 text-center text-text-secondary text-xs">
                    Belum ada siswa yang terdaftar sebagai anggota Mapel Pilihan ini. Silakan tambahkan anggota siswa di menu Master Akademik &gt; Mata Pelajaran Pilihan.
                </div>
            @else
                <div class="card overflow-x-auto">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-variant text-text-secondary font-bold border-b border-border">
                                <th rowspan="2" class="p-2.5 text-center w-10 border-r border-border">No</th>
                                <th rowspan="2" class="p-2.5 w-24 border-r border-border">NISN</th>
                                <th rowspan="2" class="p-2.5 min-w-[180px] border-r border-border">Nama Siswa</th>
                                <th rowspan="2" class="p-2.5 w-24 border-r border-border text-center">Rombel</th>

                                @if($tabelTahunMode === 'semua' || $tabelTahunMode === 'ganjil')
                                    <!-- Header Ganjil -->
                                    <th colspan="3" class="p-2 text-center bg-blue-50/60 dark:bg-blue-950/20 text-blue-800 dark:text-blue-300 border-r border-border">9 Minggu 1 (STS 1)</th>
                                    <th colspan="3" class="p-2 text-center bg-indigo-50/60 dark:bg-indigo-950/20 text-indigo-800 dark:text-indigo-300 border-r border-border">9 Minggu 2 (SAS)</th>
                                @endif

                                @if($tabelTahunMode === 'semua' || $tabelTahunMode === 'genap')
                                    <!-- Header Genap -->
                                    <th colspan="3" class="p-2 text-center bg-emerald-50/60 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300 border-r border-border">9 Minggu 3 (STS 2)</th>
                                    <th colspan="3" class="p-2 text-center bg-teal-50/60 dark:bg-teal-950/20 text-teal-800 dark:text-teal-300 border-r border-border">9 Minggu 4 (SAT)</th>
                                @endif
                            </tr>
                            <tr class="bg-surface text-text-secondary font-semibold text-[11px] border-b border-border">
                                @if($tabelTahunMode === 'semua' || $tabelTahunMode === 'ganjil')
                                    <th class="p-1.5 text-center w-14">AH 1</th>
                                    <th class="p-1.5 text-center w-14">AH 2</th>
                                    <th class="p-1.5 text-center w-16 bg-blue-50/40 text-blue-700 font-bold border-r border-border">STS 1</th>

                                    <th class="p-1.5 text-center w-14">AH 3</th>
                                    <th class="p-1.5 text-center w-14">AH 4</th>
                                    <th class="p-1.5 text-center w-16 bg-indigo-50/40 text-indigo-700 font-bold border-r border-border">SAS</th>
                                @endif

                                @if($tabelTahunMode === 'semua' || $tabelTahunMode === 'genap')
                                    <th class="p-1.5 text-center w-14">AH 5</th>
                                    <th class="p-1.5 text-center w-14">AH 6</th>
                                    <th class="p-1.5 text-center w-16 bg-emerald-50/40 text-emerald-700 font-bold border-r border-border">STS 2</th>

                                    <th class="p-1.5 text-center w-14">AH 7</th>
                                    <th class="p-1.5 text-center w-14">AH 8</th>
                                    <th class="p-1.5 text-center w-16 bg-teal-50/40 text-teal-700 font-bold border-r border-border">SAT</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($siswas as $idx => $siswa)
                                <tr class="hover:bg-surface-variant/40 transition">
                                    <td class="p-2 text-center text-text-secondary border-r border-border">{{ $idx + 1 }}</td>
                                    <td class="p-2 font-mono text-[11px] text-text-secondary border-r border-border">{{ $siswa->nisn ?: $siswa->nipd ?: '-' }}</td>
                                    <td class="p-2 font-semibold text-text-main border-r border-border">
                                        {{ $siswa->nama }}
                                    </td>
                                    <td class="p-2 text-center text-text-secondary border-r border-border">
                                        <span class="px-1.5 py-0.5 rounded bg-surface-variant text-[10px] font-bold">
                                            {{ $siswa->rombel?->nama ?? '-' }}
                                        </span>
                                    </td>

                                    @if($tabelTahunMode === 'semua' || $tabelTahunMode === 'ganjil')
                                        <!-- AH 1 -->
                                        <td class="p-1 text-center">
                                            <input type="number" step="0.01" min="0" max="100" wire:model.defer="ahData.{{ $siswa->id }}.AH 1" class="w-14 text-center text-xs p-1 rounded border border-border bg-surface text-text-main focus:ring-1 focus:ring-primary {{ isset($ahData[$siswa->id]['AH 1']) && is_numeric($ahData[$siswa->id]['AH 1']) && $ahData[$siswa->id]['AH 1'] < $kkm ? 'text-rose-600 font-bold bg-rose-50' : '' }}">
                                        </td>
                                        <!-- AH 2 -->
                                        <td class="p-1 text-center">
                                            <input type="number" step="0.01" min="0" max="100" wire:model.defer="ahData.{{ $siswa->id }}.AH 2" class="w-14 text-center text-xs p-1 rounded border border-border bg-surface text-text-main focus:ring-1 focus:ring-primary {{ isset($ahData[$siswa->id]['AH 2']) && is_numeric($ahData[$siswa->id]['AH 2']) && $ahData[$siswa->id]['AH 2'] < $kkm ? 'text-rose-600 font-bold bg-rose-50' : '' }}">
                                        </td>
                                        <!-- STS 1 -->
                                        <td class="p-1 text-center bg-blue-50/20 border-r border-border">
                                            <input type="number" step="0.01" min="0" max="100" wire:model.defer="sumatifDataGanjil.{{ $siswa->id }}.sts" class="w-14 text-center text-xs p-1 rounded border border-blue-200 bg-blue-50/40 text-blue-900 font-bold focus:ring-1 focus:ring-primary {{ isset($sumatifDataGanjil[$siswa->id]['sts']) && is_numeric($sumatifDataGanjil[$siswa->id]['sts']) && $sumatifDataGanjil[$siswa->id]['sts'] < $kkm ? 'text-rose-600 font-bold bg-rose-50 border-rose-200' : '' }}">
                                        </td>

                                        <!-- AH 3 -->
                                        <td class="p-1 text-center">
                                            <input type="number" step="0.01" min="0" max="100" wire:model.defer="ahData.{{ $siswa->id }}.AH 3" class="w-14 text-center text-xs p-1 rounded border border-border bg-surface text-text-main focus:ring-1 focus:ring-primary {{ isset($ahData[$siswa->id]['AH 3']) && is_numeric($ahData[$siswa->id]['AH 3']) && $ahData[$siswa->id]['AH 3'] < $kkm ? 'text-rose-600 font-bold bg-rose-50' : '' }}">
                                        </td>
                                        <!-- AH 4 -->
                                        <td class="p-1 text-center">
                                            <input type="number" step="0.01" min="0" max="100" wire:model.defer="ahData.{{ $siswa->id }}.AH 4" class="w-14 text-center text-xs p-1 rounded border border-border bg-surface text-text-main focus:ring-1 focus:ring-primary {{ isset($ahData[$siswa->id]['AH 4']) && is_numeric($ahData[$siswa->id]['AH 4']) && $ahData[$siswa->id]['AH 4'] < $kkm ? 'text-rose-600 font-bold bg-rose-50' : '' }}">
                                        </td>
                                        <!-- SAS -->
                                        <td class="p-1 text-center bg-indigo-50/20 border-r border-border">
                                            <input type="number" step="0.01" min="0" max="100" wire:model.defer="sumatifDataGanjil.{{ $siswa->id }}.sas" class="w-14 text-center text-xs p-1 rounded border border-indigo-200 bg-indigo-50/40 text-indigo-900 font-bold focus:ring-1 focus:ring-primary {{ isset($sumatifDataGanjil[$siswa->id]['sas']) && is_numeric($sumatifDataGanjil[$siswa->id]['sas']) && $sumatifDataGanjil[$siswa->id]['sas'] < $kkm ? 'text-rose-600 font-bold bg-rose-50 border-rose-200' : '' }}">
                                        </td>
                                    @endif

                                    @if($tabelTahunMode === 'semua' || $tabelTahunMode === 'genap')
                                        <!-- AH 5 -->
                                        <td class="p-1 text-center">
                                            <input type="number" step="0.01" min="0" max="100" wire:model.defer="ahData.{{ $siswa->id }}.AH 5" class="w-14 text-center text-xs p-1 rounded border border-border bg-surface text-text-main focus:ring-1 focus:ring-primary {{ isset($ahData[$siswa->id]['AH 5']) && is_numeric($ahData[$siswa->id]['AH 5']) && $ahData[$siswa->id]['AH 5'] < $kkm ? 'text-rose-600 font-bold bg-rose-50' : '' }}">
                                        </td>
                                        <!-- AH 6 -->
                                        <td class="p-1 text-center">
                                            <input type="number" step="0.01" min="0" max="100" wire:model.defer="ahData.{{ $siswa->id }}.AH 6" class="w-14 text-center text-xs p-1 rounded border border-border bg-surface text-text-main focus:ring-1 focus:ring-primary {{ isset($ahData[$siswa->id]['AH 6']) && is_numeric($ahData[$siswa->id]['AH 6']) && $ahData[$siswa->id]['AH 6'] < $kkm ? 'text-rose-600 font-bold bg-rose-50' : '' }}">
                                        </td>
                                        <!-- STS 2 -->
                                        <td class="p-1 text-center bg-emerald-50/20 border-r border-border">
                                            <input type="number" step="0.01" min="0" max="100" wire:model.defer="sumatifDataGenap.{{ $siswa->id }}.sts" class="w-14 text-center text-xs p-1 rounded border border-emerald-200 bg-emerald-50/40 text-emerald-900 font-bold focus:ring-1 focus:ring-primary {{ isset($sumatifDataGenap[$siswa->id]['sts']) && is_numeric($sumatifDataGenap[$siswa->id]['sts']) && $sumatifDataGenap[$siswa->id]['sts'] < $kkm ? 'text-rose-600 font-bold bg-rose-50 border-rose-200' : '' }}">
                                        </td>

                                        <!-- AH 7 -->
                                        <td class="p-1 text-center">
                                            <input type="number" step="0.01" min="0" max="100" wire:model.defer="ahData.{{ $siswa->id }}.AH 7" class="w-14 text-center text-xs p-1 rounded border border-border bg-surface text-text-main focus:ring-1 focus:ring-primary {{ isset($ahData[$siswa->id]['AH 7']) && is_numeric($ahData[$siswa->id]['AH 7']) && $ahData[$siswa->id]['AH 7'] < $kkm ? 'text-rose-600 font-bold bg-rose-50' : '' }}">
                                        </td>
                                        <!-- AH 8 -->
                                        <td class="p-1 text-center">
                                            <input type="number" step="0.01" min="0" max="100" wire:model.defer="ahData.{{ $siswa->id }}.AH 8" class="w-14 text-center text-xs p-1 rounded border border-border bg-surface text-text-main focus:ring-1 focus:ring-primary {{ isset($ahData[$siswa->id]['AH 8']) && is_numeric($ahData[$siswa->id]['AH 8']) && $ahData[$siswa->id]['AH 8'] < $kkm ? 'text-rose-600 font-bold bg-rose-50' : '' }}">
                                        </td>
                                        <!-- SAT -->
                                        <td class="p-1 text-center bg-teal-50/20 border-r border-border">
                                            <input type="number" step="0.01" min="0" max="100" wire:model.defer="sumatifDataGenap.{{ $siswa->id }}.sas" class="w-14 text-center text-xs p-1 rounded border border-teal-200 bg-teal-50/40 text-teal-900 font-bold focus:ring-1 focus:ring-primary {{ isset($sumatifDataGenap[$siswa->id]['sas']) && is_numeric($sumatifDataGenap[$siswa->id]['sas']) && $sumatifDataGenap[$siswa->id]['sas'] < $kkm ? 'text-rose-600 font-bold bg-rose-50 border-rose-200' : '' }}">
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-2">
                    <button wire:click="saveNilai" type="button" class="py-2 px-6 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-2xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Nilai Formatif & Sumatif
                    </button>
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 2: TUJUAN PEMBELAJARAN (TP) -->
    @if($activeTab === 'tp')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Form Input / Edit TP -->
            <div class="card p-4 lg:col-span-1 h-fit">
                <h3 class="text-sm font-bold text-text-main mb-3 pb-2 border-b border-border">
                    {{ $edit_tp_id ? 'Edit Tujuan Pembelajaran' : 'Tambah Tujuan Pembelajaran' }}
                </h3>

                <form wire:submit.prevent="saveTp" class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">Semester</label>
                        <select wire:model="tp_semester_id" class="w-full text-xs rounded-lg border-border bg-surface text-text-main py-2 px-2.5 focus:ring-primary focus:border-primary">
                            @foreach($semesters as $s)
                                <option value="{{ $s['id'] }}">{{ $s['nama'] }} ({{ ucfirst($s['semester']) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">Kode TP</label>
                            <input type="text" wire:model="tp_kode" placeholder="TP 1" class="w-full text-xs rounded-lg border-border bg-surface text-text-main py-2 px-2.5 focus:ring-primary focus:border-primary">
                            @error('tp_kode') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">Urutan</label>
                            <input type="number" min="1" wire:model="tp_urutan" class="w-full text-xs rounded-lg border-border bg-surface text-text-main py-2 px-2.5 focus:ring-primary focus:border-primary">
                            @error('tp_urutan') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">Alokasi AH</label>
                            <select wire:model="tp_alokasi_ah" class="w-full text-xs rounded-lg border-border bg-surface text-text-main py-2 px-2.5 focus:ring-primary focus:border-primary">
                                <option value="AH 1">AH 1 (9M1)</option>
                                <option value="AH 2">AH 2 (9M1)</option>
                                <option value="AH 3">AH 3 (9M2)</option>
                                <option value="AH 4">AH 4 (9M2)</option>
                                <option value="AH 5">AH 5 (9M3)</option>
                                <option value="AH 6">AH 6 (9M3)</option>
                                <option value="AH 7">AH 7 (9M4)</option>
                                <option value="AH 8">AH 8 (9M4)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">Target Periode</label>
                            <select wire:model="tp_target_periode" class="w-full text-xs rounded-lg border-border bg-surface text-text-main py-2 px-2.5 focus:ring-primary focus:border-primary">
                                <option value="9M1">9 Minggu 1 (STS 1)</option>
                                <option value="9M2">9 Minggu 2 (SAS)</option>
                                <option value="9M3">9 Minggu 3 (STS 2)</option>
                                <option value="9M4">9 Minggu 4 (SAT)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">Lingkup Materi (Opsional)</label>
                        <input type="text" wire:model="tp_lingkup_materi" placeholder="e.g. Pemrograman Berorientasi Objek" class="w-full text-xs rounded-lg border-border bg-surface text-text-main py-2 px-2.5 focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">Deskripsi Capaian TP</label>
                        <textarea rows="3" wire:model="tp_deskripsi" placeholder="memahami konsep dasar enkapsulasi dan pewarisan..." class="w-full text-xs rounded-lg border-border bg-surface text-text-main py-2 px-2.5 focus:ring-primary focus:border-primary"></textarea>
                        <p class="text-[10px] text-text-secondary mt-0.5">Gunakan awalan kata kerja huruf kecil (e.g. "memahami konsep...") untuk narasi rapor otomatis.</p>
                        @error('tp_deskripsi') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-2xs">
                            {{ $edit_tp_id ? 'Perbarui TP' : 'Simpan TP' }}
                        </button>
                        @if($edit_tp_id)
                            <button type="button" wire:click="resetTpForm" class="py-2 px-3 text-xs font-semibold rounded-lg border border-border bg-surface text-text-secondary hover:text-text-main transition">
                                Batal
                            </button>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Daftar TP Terdaftar -->
            <div class="card p-4 lg:col-span-2">
                <h3 class="text-sm font-bold text-text-main mb-3 pb-2 border-b border-border flex items-center justify-between">
                    <span>Daftar Tujuan Pembelajaran (TP)</span>
                    <span class="text-xs text-text-secondary font-normal">Total: {{ count($allTps) }} TP</span>
                </h3>

                @if(empty($allTps) || count($allTps) === 0)
                    <div class="text-center py-8 text-xs text-text-secondary">
                        Belum ada Tujuan Pembelajaran yang diatur. Silakan tambahkan TP menggunakan formulir di sebelah kiri.
                    </div>
                @else
                    <div class="divide-y divide-border">
                        @foreach($allTps as $tp)
                            <div class="py-2.5 flex items-start justify-between gap-3 hover:bg-surface-variant/30 px-2 rounded-lg transition">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-xs text-text-main">{{ $tp->kode_tp }}</span>
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-surface-variant text-text-secondary border border-border">
                                            {{ $tp->kode_ah ?? 'AH ?' }}
                                        </span>
                                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-blue-50 text-blue-700">
                                            {{ $tp->target_periode }}
                                        </span>
                                        <span class="text-[10px] text-text-secondary">
                                            Semester: {{ $tp->semester?->nama ?? '-' }}
                                        </span>
                                    </div>
                                    @if($tp->lingkup_materi)
                                        <div class="text-[11px] font-medium text-primary">
                                            Materi: {{ $tp->lingkup_materi }}
                                        </div>
                                    @endif
                                    <p class="text-xs text-text-secondary italic">"{{ $tp->deskripsi_tp }}"</p>
                                </div>

                                <div class="flex items-center gap-1 shrink-0">
                                    <button wire:click="editTp({{ $tp->id }})" type="button" class="p-1 rounded text-text-secondary hover:text-primary transition" title="Edit TP">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="deleteTp({{ $tp->id }})" onclick="return confirm('Hapus TP ini?')" type="button" class="p-1 rounded text-text-secondary hover:text-rose-600 transition" title="Hapus TP">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- TAB 3: REKAPITULASI NILAI & RAPOR -->
    @if($activeTab === 'rekap')
        <div class="space-y-3">
            <!-- Toolbar Rekap -->
            <div class="card p-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-text-secondary uppercase">Semester:</label>
                    <select wire:model.live="activeNilaiSemesterId" class="text-xs font-semibold rounded-lg border-border bg-surface text-text-main py-1.5 px-2.5 focus:ring-primary">
                        @foreach($semesters as $sem)
                            <option value="{{ $sem['id'] }}">{{ $sem['nama'] }} ({{ ucfirst($sem['semester']) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-[11px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                        KKM Mapil: <strong class="font-bold">{{ $kkm }}</strong>
                    </span>
                    <button wire:click="hitungUlangRekap" type="button" class="inline-flex items-center gap-1.5 py-1.5 px-3 text-xs font-semibold rounded-lg bg-surface border border-border text-text-main hover:bg-surface-variant transition shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Hitung Ulang Rekap
                    </button>
                </div>
            </div>

            <!-- Tabel Rekapitulasi -->
            @php
                $isGenapSem = $currSemObj && str_contains(strtolower($currSemObj->semester), 'genap');
                $colBlock1 = $isGenapSem ? '9M3' : '9M1';
                $colBlock2 = $isGenapSem ? '9M4' : '9M2';
                $labelBlock1 = $isGenapSem ? '9 Minggu 3 (STS 2)' : '9 Minggu 1 (STS 1)';
                $labelBlock2 = $isGenapSem ? '9 Minggu 4 (SAT)' : '9 Minggu 2 (SAS)';
            @endphp

            <div class="card overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-variant text-text-secondary font-bold border-b border-border">
                            <th class="p-2.5 text-center w-10 border-r border-border">No</th>
                            <th class="p-2.5 min-w-[170px] border-r border-border">Nama Siswa</th>
                            <th class="p-2.5 w-20 text-center border-r border-border">Rombel</th>

                            <!-- Blok 1 -->
                            <th class="p-2 text-center w-16 border-r border-border bg-blue-50/40 text-blue-900">{{ $colBlock1 }} NA</th>
                            <th class="p-2 min-w-[220px] border-r border-border bg-blue-50/20 text-blue-900">Deskripsi {{ $colBlock1 }}</th>

                            <!-- Blok 2 -->
                            <th class="p-2 text-center w-16 border-r border-border bg-indigo-50/40 text-indigo-900">{{ $colBlock2 }} NA</th>
                            <th class="p-2 min-w-[220px] border-r border-border bg-indigo-50/20 text-indigo-900">Deskripsi {{ $colBlock2 }}</th>

                            <!-- Nilai Akhir Semester -->
                            <th class="p-2 text-center w-16 border-r border-border bg-emerald-50/50 text-emerald-900 font-bold">NA Sem</th>
                            <th class="p-2 min-w-[100px] text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($siswas as $idx => $siswa)
                            @php
                                $rekap = $rekapData[$siswa->id] ?? null;
                                $data9m = $rekap?->data_9m ?? [];
                                $b1 = $data9m[$colBlock1] ?? [];
                                $b2 = $data9m[$colBlock2] ?? [];

                                $na1 = $b1['nilai_akhir'] ?? null;
                                $status1 = $b1['status'] ?? 'belum_lengkap';
                                $desc1 = $b1['deskripsi_manual'] ?: ($b1['deskripsi_tertinggi'] ?? '');

                                $na2 = $b2['nilai_akhir'] ?? null;
                                $status2 = $b2['status'] ?? 'belum_lengkap';
                                $desc2 = $b2['deskripsi_manual'] ?: ($b2['deskripsi_tertinggi'] ?? '');

                                $naSem = $rekap?->nilai_akhir;
                                $statusSem = $rekap?->status ?? 'belum_lengkap';
                            @endphp
                            <tr class="hover:bg-surface-variant/40 transition">
                                <td class="p-2 text-center text-text-secondary border-r border-border">{{ $idx + 1 }}</td>
                                <td class="p-2 font-semibold text-text-main border-r border-border">
                                    {{ $siswa->nama }}
                                </td>
                                <td class="p-2 text-center text-text-secondary border-r border-border">
                                    <span class="px-1.5 py-0.5 rounded bg-surface-variant text-[10px] font-bold">
                                        {{ $siswa->rombel?->nama ?? '-' }}
                                    </span>
                                </td>

                                <!-- Blok 1 NA -->
                                <td class="p-2 text-center font-bold border-r border-border {{ $na1 === null ? 'text-text-secondary' : ($na1 < $kkm ? 'text-rose-600' : 'text-text-main') }}">
                                    {{ $na1 !== null ? number_format($na1, 1) : '-' }}
                                </td>
                                <!-- Blok 1 Deskripsi & Aksi -->
                                <td class="p-2 border-r border-border text-[11px]">
                                    @if($status1 === 'belum_lengkap')
                                        <span class="inline-block px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 font-medium mb-1">Belum Lengkap</span>
                                    @elseif($status1 === 'belum_kompeten')
                                        <span class="inline-block px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 font-medium mb-1">Belum Kompeten</span>
                                    @endif
                                    <div class="line-clamp-2 text-text-secondary" title="{{ $desc1 }}">
                                        {{ $desc1 ?: 'Belum ada catatan deskripsi.' }}
                                    </div>
                                    <button wire:click="openModalDeskripsi9M({{ $siswa->id }}, '{{ $colBlock1 }}')" type="button" class="text-[10px] text-primary hover:underline mt-0.5 inline-block">
                                        {{ !empty($b1['deskripsi_manual']) ? 'Sunting Override' : '+ Edit Catatan' }}
                                    </button>
                                </td>

                                <!-- Blok 2 NA -->
                                <td class="p-2 text-center font-bold border-r border-border {{ $na2 === null ? 'text-text-secondary' : ($na2 < $kkm ? 'text-rose-600' : 'text-text-main') }}">
                                    {{ $na2 !== null ? number_format($na2, 1) : '-' }}
                                </td>
                                <!-- Blok 2 Deskripsi & Aksi -->
                                <td class="p-2 border-r border-border text-[11px]">
                                    @if($status2 === 'belum_lengkap')
                                        <span class="inline-block px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 font-medium mb-1">Belum Lengkap</span>
                                    @elseif($status2 === 'belum_kompeten')
                                        <span class="inline-block px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 font-medium mb-1">Belum Kompeten</span>
                                    @endif
                                    <div class="line-clamp-2 text-text-secondary" title="{{ $desc2 }}">
                                        {{ $desc2 ?: 'Belum ada catatan deskripsi.' }}
                                    </div>
                                    <button wire:click="openModalDeskripsi9M({{ $siswa->id }}, '{{ $colBlock2 }}')" type="button" class="text-[10px] text-primary hover:underline mt-0.5 inline-block">
                                        {{ !empty($b2['deskripsi_manual']) ? 'Sunting Override' : '+ Edit Catatan' }}
                                    </button>
                                </td>

                                <!-- NA Semester -->
                                <td class="p-2 text-center font-bold text-sm border-r border-border bg-emerald-50/20 {{ $naSem === null ? 'text-text-secondary' : ($naSem < $kkm ? 'text-rose-600' : 'text-emerald-700') }}">
                                    {{ $naSem !== null ? number_format($naSem, 1) : '-' }}
                                </td>

                                <!-- Status Semester -->
                                <td class="p-2 text-center">
                                    @if($statusSem === 'kompeten')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            Kompeten
                                        </span>
                                    @elseif($statusSem === 'belum_kompeten')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                            Blm Kompeten
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            Blm Lengkap
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-6 text-center text-text-secondary">
                                    Belum ada siswa terdaftar pada Mapel Pilihan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- MODAL OVERRIDE DESKRIPSI 9M -->
    @if($showModalDeskripsi9M)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
            <div class="bg-surface rounded-xl shadow-xl border border-border w-full max-w-lg p-5 space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-border">
                    <div>
                        <h3 class="text-sm font-bold text-text-main">Catatan Rapor {{ $selectedPeriodKey9M }}</h3>
                        <p class="text-xs text-text-secondary">{{ $selectedSiswaNama9M }} - {{ $mapelPilihan->nama }}</p>
                    </div>
                    <button wire:click="$set('showModalDeskripsi9M', false)" type="button" class="text-text-secondary hover:text-text-main">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block text-[11px] font-bold text-text-secondary mb-1">Narasi Capaian Otomatis (Sistem):</label>
                        <div class="p-2.5 rounded-lg bg-surface-variant border border-border text-text-secondary italic">
                            {{ $autoDeskripsiInfo9M ?: 'Belum ada narasi otomatis terbentuk.' }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-text-secondary mb-1">Override Catatan Manual (Tampil di Rapor):</label>
                        <textarea rows="4" wire:model="tempDeskripsiManual9M" placeholder="Ketik narasi catatan kustom wali/guru untuk siswa ini (opsional)..." class="w-full text-xs rounded-lg border-border bg-surface text-text-main p-2.5 focus:ring-primary focus:border-primary"></textarea>
                        <p class="text-[10px] text-text-secondary mt-0.5">Biarkan kosong jika ingin menggunakan narasi otomatis dari sistem.</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-border">
                    <button wire:click="$set('showModalDeskripsi9M', false)" type="button" class="py-2 px-4 text-xs font-semibold rounded-lg border border-border bg-surface text-text-main hover:bg-surface-variant transition">
                        Batal
                    </button>
                    <button wire:click="saveDeskripsi9M" type="button" class="py-2 px-4 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-2xs">
                        Simpan Catatan
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL IMPORT EXCEL -->
    @if($showImportModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
            <div class="bg-surface rounded-xl shadow-xl border border-border w-full max-w-md p-5 space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-border">
                    <h3 class="text-sm font-bold text-text-main">Import Nilai dari File Excel</h3>
                    <button wire:click="$set('showImportModal', false)" type="button" class="text-text-secondary hover:text-text-main">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <p class="text-text-secondary">
                        Unggah file Excel yang telah diisi sesuai format template nilai Mapel Pilihan.
                    </p>

                    <div>
                        <label class="block text-[11px] font-bold text-text-secondary mb-1">Pilih File Excel (.xlsx / .xls)</label>
                        <input type="file" wire:model="importFile" accept=".xlsx,.xls" class="w-full text-xs text-text-secondary file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90">
                        @error('importFile') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div wire:loading wire:target="importFile" class="text-xs text-primary font-medium">
                        Memproses file...
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-border">
                    <button wire:click="$set('showImportModal', false)" type="button" class="py-2 px-4 text-xs font-semibold rounded-lg border border-border bg-surface text-text-main hover:bg-surface-variant transition">
                        Batal
                    </button>
                    <button wire:click="importNilai" wire:loading.attr="disabled" type="button" class="py-2 px-4 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-2xs">
                        Import Nilai
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
