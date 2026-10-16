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

    <!-- TAB 1: ASESMEN FORMATIF (PER TP) & SUMATIF -->
    @if($activeTab === 'formatif')
        @php
            $activeTpsList = $this->activeTps;
            $sumatifLabel = $this->getLabelSumatif($activePeriode);
        @endphp
        <div class="space-y-3">
            <!-- Toolbar Switcher Periode 9M & Simpan -->
            <div class="card p-3 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 bg-surface-hover/50">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-text-secondary uppercase">Periode 9M:</span>
                    <div class="inline-flex rounded-lg border border-border p-1 bg-surface shadow-xs gap-1">
                        <button type="button" wire:click="switchPeriode('9M1')" class="px-3 py-1 text-xs font-semibold rounded-md transition cursor-pointer {{ $activePeriode === '9M1' ? 'bg-primary text-white shadow-xs font-bold' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                            9M1 (STS 1)
                        </button>
                        <button type="button" wire:click="switchPeriode('9M2')" class="px-3 py-1 text-xs font-semibold rounded-md transition cursor-pointer {{ $activePeriode === '9M2' ? 'bg-primary text-white shadow-xs font-bold' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                            9M2 (SAS)
                        </button>
                        <button type="button" wire:click="switchPeriode('9M3')" class="px-3 py-1 text-xs font-semibold rounded-md transition cursor-pointer {{ $activePeriode === '9M3' ? 'bg-primary text-white shadow-xs font-bold' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                            9M3 (STS 2)
                        </button>
                        <button type="button" wire:click="switchPeriode('9M4')" class="px-3 py-1 text-xs font-semibold rounded-md transition cursor-pointer {{ $activePeriode === '9M4' ? 'bg-primary text-white shadow-xs font-bold' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                            9M4 (SAT)
                        </button>
                    </div>

                    <span class="text-[11px] px-2.5 py-1 rounded-md bg-blue-50 dark:bg-blue-950/40 text-blue-800 dark:text-blue-200 border border-blue-200 dark:border-blue-900 font-semibold">
                        {{ count($activeTpsList) }} TP aktif di {{ $activePeriode }}
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <span class="hidden sm:inline-flex items-center gap-1 text-[11px] text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        KKM: {{ $kkm }} (Nilai kosong/&lt;KKM = Belum Kompeten)
                    </span>
                    <button wire:click="saveNilai" type="button" class="py-1.5 px-4 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-2xs flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Nilai
                    </button>
                </div>
            </div>

            <!-- Tabel Grid Nilai Siswa Per TP -->
            @if(empty($siswas))
                <div class="card p-8 text-center text-text-secondary text-xs">
                    Belum ada siswa yang terdaftar sebagai anggota Mapel Pilihan ini. Silakan tambahkan anggota siswa di menu Master Akademik &gt; Mata Pelajaran Pilihan.
                </div>
            @else
                <div class="card overflow-x-auto border border-border rounded-xl shadow-2xs">
                    <table class="min-w-full text-xs text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-variant text-text-secondary font-bold border-b border-border">
                                <th rowspan="2" class="p-2.5 text-center w-10 border-r border-border">No</th>
                                <th rowspan="2" class="p-2.5 w-24 border-r border-border">NISN</th>
                                <th rowspan="2" class="p-2.5 min-w-[170px] border-r border-border">Nama Siswa</th>
                                <th rowspan="2" class="p-2.5 w-24 border-r border-border text-center">Rombel</th>

                                <!-- Dynamic Columns TP -->
                                @if(count($activeTpsList) > 0)
                                    <th colspan="{{ count($activeTpsList) }}" class="p-2 text-center bg-blue-50/80 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200 border-r border-border font-bold">
                                        Asesmen Formatif (Tujuan Pembelajaran - {{ $activePeriode }})
                                    </th>
                                @endif

                                <!-- Kolom Nilai Harian (NH) -->
                                <th rowspan="2" class="p-2 text-center w-24 min-w-[85px] border-r border-border bg-emerald-50 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200 font-bold" title="Nilai Harian Subjektif Guru (Default 100)">
                                    Harian (NH)
                                    <span class="block text-[9px] font-normal text-emerald-700 dark:text-emerald-300">Default: 100</span>
                                </th>

                                <!-- Kolom Sumatif -->
                                <th rowspan="2" class="p-2 text-center w-24 min-w-[85px] border-b border-border bg-purple-50 dark:bg-purple-950/40 text-purple-900 dark:text-purple-200 font-bold" title="Asesmen Sumatif ({{ $sumatifLabel }})">
                                    {{ $sumatifLabel }}
                                    <span class="block text-[9px] font-normal text-purple-700 dark:text-purple-300">Sumatif</span>
                                </th>
                            </tr>

                            <!-- Sub-header baris TP -->
                            <tr class="bg-surface text-text-secondary font-semibold text-[11px] border-b border-border">
                                @forelse($activeTpsList as $tp)
                                    <th class="p-1.5 text-center min-w-[65px] border-r border-border bg-blue-50/30 dark:bg-blue-950/20" title="{{ $tp->deskripsi_tp }} ({{ $tp->lingkup_materi }})">
                                        <div class="font-bold text-text-main">{{ $tp->kode_tp }}</div>
                                        @if($tp->lingkup_materi)
                                            <div class="text-[9px] text-text-muted truncate max-w-[80px]" title="{{ $tp->lingkup_materi }}">{{ $tp->lingkup_materi }}</div>
                                        @endif
                                    </th>
                                @empty
                                    <th class="p-2 text-center text-text-muted italic border-r border-border text-[11px]">
                                        Belum ada TP pada {{ $activePeriode }}.
                                        <button type="button" wire:click="switchTab('tp')" class="text-primary underline font-bold cursor-pointer ml-1">Buat TP di sini</button>
                                    </th>
                                @endforelse
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border bg-surface text-text-main">
                            @foreach($siswas as $idx => $siswa)
                                <tr class="hover:bg-surface-variant/40 transition">
                                    <td class="p-2 text-center text-text-secondary border-r border-border font-mono">{{ $idx + 1 }}</td>
                                    <td class="p-2 font-mono text-[11px] text-text-secondary border-r border-border">{{ $siswa->nisn ?: $siswa->nipd ?: '-' }}</td>
                                    <td class="p-2 font-semibold text-text-main border-r border-border">
                                        {{ $siswa->nama }}
                                    </td>
                                    <td class="p-2 text-center text-text-secondary border-r border-border">
                                        <span class="px-1.5 py-0.5 rounded bg-surface-variant text-[10px] font-bold">
                                            {{ $siswa->rombel?->nama ?? '-' }}
                                        </span>
                                    </td>

                                    <!-- Nilai per TP -->
                                    @forelse($activeTpsList as $tp)
                                        @php
                                            $valTp = $tpScores[$siswa->id][$tp->id] ?? null;
                                            $isBelowKkmTp = ($valTp !== null && $valTp !== '' && is_numeric($valTp) && (float)$valTp < $kkm);
                                        @endphp
                                        <td class="p-1 text-center border-r border-border">
                                            <input type="number" step="0.01" min="0" max="100"
                                                wire:model.defer="tpScores.{{ $siswa->id }}.{{ $tp->id }}"
                                                placeholder="-"
                                                class="w-14 text-center text-xs p-1 rounded border border-border bg-surface text-text-main focus:ring-1 focus:ring-primary {{ $isBelowKkmTp ? 'text-rose-600 font-bold bg-rose-50 border-rose-300' : '' }}">
                                        </td>
                                    @empty
                                        <td class="p-2 text-center text-text-muted italic border-r border-border text-[11px]">
                                            -
                                        </td>
                                    @endforelse

                                    <!-- Nilai Harian (NH) Default 100 -->
                                    @php
                                        $valNh = $nhData[$siswa->id][$activePeriode] ?? 100.0;
                                        $isBelowKkmNh = ($valNh !== null && $valNh !== '' && is_numeric($valNh) && (float)$valNh < $kkm);
                                    @endphp
                                    <td class="p-1 text-center border-r border-border bg-emerald-50/20 dark:bg-emerald-950/10">
                                        <input type="number" step="0.01" min="0" max="100"
                                            wire:model.defer="nhData.{{ $siswa->id }}.{{ $activePeriode }}"
                                            placeholder="100"
                                            class="w-14 text-center text-xs p-1 rounded border border-emerald-300 bg-emerald-50/40 text-emerald-900 font-bold focus:ring-1 focus:ring-primary {{ $isBelowKkmNh ? 'text-rose-600 font-bold bg-rose-50 border-rose-300' : '' }}">
                                    </td>

                                    <!-- Nilai Sumatif (STS / SAS / SAT) -->
                                    @php
                                        $valSumatif = $sumatifData[$siswa->id][$activePeriode] ?? null;
                                        $isBelowKkmSum = ($valSumatif !== null && $valSumatif !== '' && is_numeric($valSumatif) && (float)$valSumatif < $kkm);
                                    @endphp
                                    <td class="p-1 text-center bg-purple-50/20 dark:bg-purple-950/10 border-b border-border">
                                        <input type="number" step="0.01" min="0" max="100"
                                            wire:model.defer="sumatifData.{{ $siswa->id }}.{{ $activePeriode }}"
                                            placeholder="-"
                                            class="w-14 text-center text-xs p-1 rounded border border-purple-300 bg-purple-50/40 text-purple-900 font-bold focus:ring-1 focus:ring-primary {{ $isBelowKkmSum ? 'text-rose-600 font-bold bg-rose-50 border-rose-300' : '' }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-2">
                    <button wire:click="saveNilai" type="button" class="py-2 px-6 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-2xs flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Seluruh Nilai TP, NH & Sumatif
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
        @php
            $settings = \App\Modules\Penilaian\Models\PengaturanPenilaian::getActive();
            $bobotTp = $settings->bobot_tp ?? 50;
            $bobotSts = $settings->bobot_sts ?? 30;
            $bobotNh = $settings->bobot_nh ?? 20;

            $isGenapSem = $currSemObj && str_contains(strtolower($currSemObj->semester), 'genap');
            $k1 = $isGenapSem ? '9M3' : '9M1';
            $k2 = $isGenapSem ? '9M4' : '9M2';
            $labelBlock1 = $isGenapSem ? '9 Minggu 3 (STS 2)' : '9 Minggu 1 (STS 1)';
            $labelBlock2 = $isGenapSem ? '9 Minggu 4 (SAT)' : '9 Minggu 2 (SAS)';
            $sumatifLabel1 = $isGenapSem ? 'STS 2' : 'STS 1';
            $sumatifLabel2 = $isGenapSem ? 'SAT' : 'SAS';

            $isSingleView = ($rekapPeriode !== 'semester');
            $targetP = $isSingleView ? $rekapPeriode : $k1;
            $sumatifLabelSingle = match($targetP) {
                '9M1' => 'STS 1',
                '9M2' => 'SAS',
                '9M3' => 'STS 2',
                '9M4' => 'SAT',
                default => 'STS',
            };
        @endphp

        <div class="space-y-3">
            <!-- Toolbar Switcher Rekap & Bobot -->
            <div class="card p-3 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 bg-surface-hover/50">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-text-secondary uppercase">Tampilan Rekap:</span>
                    <div class="inline-flex rounded-lg border border-border p-1 bg-surface shadow-xs gap-1">
                        <button type="button" wire:click="switchRekapPeriode('9M1')" class="px-3 py-1 text-xs font-semibold rounded-md transition cursor-pointer {{ $rekapPeriode === '9M1' ? 'bg-primary text-white shadow-xs font-bold' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                            9M1 (STS 1)
                        </button>
                        <button type="button" wire:click="switchRekapPeriode('9M2')" class="px-3 py-1 text-xs font-semibold rounded-md transition cursor-pointer {{ $rekapPeriode === '9M2' ? 'bg-primary text-white shadow-xs font-bold' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                            9M2 (SAS)
                        </button>
                        <button type="button" wire:click="switchRekapPeriode('9M3')" class="px-3 py-1 text-xs font-semibold rounded-md transition cursor-pointer {{ $rekapPeriode === '9M3' ? 'bg-primary text-white shadow-xs font-bold' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                            9M3 (STS 2)
                        </button>
                        <button type="button" wire:click="switchRekapPeriode('9M4')" class="px-3 py-1 text-xs font-semibold rounded-md transition cursor-pointer {{ $rekapPeriode === '9M4' ? 'bg-primary text-white shadow-xs font-bold' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                            9M4 (SAT)
                        </button>
                        <button type="button" wire:click="switchRekapPeriode('semester')" class="px-3 py-1 text-xs font-semibold rounded-md transition cursor-pointer {{ $rekapPeriode === 'semester' ? 'bg-primary text-white shadow-xs font-bold' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                            1 Semester (Berdampingan)
                        </button>
                    </div>

                    <!-- Info Bobot Formula -->
                    <div class="hidden xl:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900 text-[11px] text-blue-800 dark:text-blue-200">
                        <span class="font-bold">Bobot:</span>
                        <span>TP {{ $bobotTp }}%</span> &bull;
                        <span>Harian {{ $bobotNh }}%</span> &bull;
                        <span>STS/SAS {{ $bobotSts }}%</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-[11px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                        KKM: <strong class="font-bold">{{ $kkm }}</strong>
                    </span>
                    <button wire:click="hitungUlangRekap" type="button" class="inline-flex items-center gap-1.5 py-1.5 px-3 text-xs font-semibold rounded-lg bg-surface border border-border text-text-main hover:bg-surface-variant transition shadow-2xs cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Hitung Ulang Rekap
                    </button>
                </div>
            </div>

            @if($isSingleView)
            <!-- VIEW 1: SINGLE 9M FOCUS (Tampil Rata TP, Harian/NH, STS, NA 9M, Predikat, Narasi Capaian) -->
            <div class="card overflow-x-auto border border-border rounded-xl shadow-2xs">
                <table class="min-w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-variant text-text-secondary font-bold border-b border-border">
                            <th class="p-2.5 text-center w-10 border-r border-border">No</th>
                            <th class="p-2.5 min-w-[170px] border-r border-border">Nama Siswa</th>
                            <th class="p-2.5 w-20 text-center border-r border-border">Rombel</th>
                            <th class="p-2 text-center min-w-[95px] border-r border-border bg-blue-50/70 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200">
                                Rata TP ({{ $bobotTp }}%)
                            </th>
                            <th class="p-2 text-center min-w-[95px] border-r border-border bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200">
                                Harian / NH ({{ $bobotNh }}%)
                            </th>
                            <th class="p-2 text-center min-w-[95px] border-r border-border bg-purple-50/70 dark:bg-purple-950/40 text-purple-900 dark:text-purple-200">
                                {{ $sumatifLabelSingle }} ({{ $bobotSts }}%)
                            </th>
                            <th class="p-2 text-center min-w-[85px] border-r-2 border-border bg-primary/10 text-primary font-extrabold">
                                NA {{ $targetP }}
                            </th>
                            <th class="p-2 text-center min-w-[125px] border-r border-border bg-slate-100/90 dark:bg-slate-800/90 font-bold text-text-main">
                                Predikat
                            </th>
                            <th class="p-2 text-left min-w-[260px] border-r border-border text-text-main">
                                Narasi Capaian (Rapor {{ $targetP }})
                            </th>
                            <th class="p-2 text-center w-16 border-border font-bold text-text-secondary">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-surface text-text-main">
                        @forelse($siswas as $idx => $siswa)
                            @php
                                $rekap = $rekapData[$siswa->id] ?? null;
                                $d = $rekap ? $rekap->getRecordFor9M($targetP) : null;

                                $rataTp = $d['rata_tp'] ?? null;
                                $nh = $d['nh'] ?? ($nhData[$siswa->id][$targetP] ?? 100.0);
                                $sumatif = $d['sumatif'] ?? ($sumatifData[$siswa->id][$targetP] ?? null);
                                $na = $d['nilai_akhir'] ?? null;
                                $predikat = $d['predikat'] ?? 'Belum Lengkap';
                                $status = $d['status'] ?? 'belum_ada_nilai';

                                $isManual = !empty($d['deskripsi_manual']);
                                $desc = $isManual ? $d['deskripsi_manual'] : trim(($d['deskripsi_tertinggi'] ?? '') . ' ' . ($d['deskripsi_terendah'] ?? ''));

                                $predikatBadgeClass = match($predikat) {
                                    'Mahir' => 'bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-200 border-emerald-300 dark:border-emerald-800',
                                    'Cakap' => 'bg-indigo-100 dark:bg-indigo-950/70 text-indigo-800 dark:text-indigo-200 border-indigo-300 dark:border-indigo-800',
                                    'Layak' => 'bg-sky-100 dark:bg-sky-950/70 text-sky-800 dark:text-sky-200 border-sky-300 dark:border-sky-800',
                                    'Mulai Berkembang' => 'bg-amber-100 dark:bg-amber-950/70 text-amber-800 dark:text-amber-200 border-amber-300 dark:border-amber-800',
                                    'Belum Kompeten' => 'bg-rose-100 dark:bg-rose-950/70 text-rose-800 dark:text-rose-200 border-rose-300 dark:border-rose-800 font-bold',
                                    'Belum Lengkap' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-300 dark:border-slate-700',
                                    default => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200',
                                };
                            @endphp
                            <tr class="hover:bg-surface-variant/40 transition">
                                <td class="p-2 text-center text-text-secondary border-r border-border font-mono">{{ $idx + 1 }}</td>
                                <td class="p-2 font-semibold text-text-main border-r border-border">
                                    {{ $siswa->nama }}
                                </td>
                                <td class="p-2 text-center text-text-secondary border-r border-border">
                                    <span class="px-1.5 py-0.5 rounded bg-surface-variant text-[10px] font-bold">
                                        {{ $siswa->rombel?->nama ?? '-' }}
                                    </span>
                                </td>

                                <!-- Rata TP -->
                                <td class="p-2 text-center font-mono font-medium border-r border-border bg-blue-50/20 dark:bg-blue-950/10">
                                    {{ $rataTp !== null ? number_format($rataTp, 1) : '-' }}
                                </td>

                                <!-- Harian / NH -->
                                <td class="p-2 text-center font-mono font-medium border-r border-border bg-emerald-50/20 dark:bg-emerald-950/10">
                                    {{ $nh !== null ? number_format($nh, 1) : '100.0' }}
                                </td>

                                <!-- STS / SAS -->
                                <td class="p-2 text-center font-mono font-medium border-r border-border bg-purple-50/20 dark:bg-purple-950/10">
                                    {{ $sumatif !== null ? number_format($sumatif, 1) : '-' }}
                                </td>

                                <!-- NA 9M -->
                                @if($status === 'belum_kompeten')
                                    <td class="p-2 text-center font-mono font-bold text-xs border-r-2 border-border bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300"
                                        title="Belum Kompeten: Ada nilai TP/Sumatif di bawah KKM ({{ $kkm }})">
                                        <span class="inline-block px-2 py-0.5 rounded bg-rose-100 dark:bg-rose-900/60 text-rose-800 dark:text-rose-200 font-bold">-</span>
                                    </td>
                                @elseif($status === 'belum_lengkap')
                                    <td class="p-2 text-center font-mono font-bold text-xs border-r-2 border-border bg-amber-50/50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300"
                                        title="Nilai Belum Lengkap: Ada nilai TP/Sumatif yang belum terisi">
                                        <span class="inline-block px-2 py-0.5 rounded bg-amber-100/80 dark:bg-amber-900/40 text-amber-800 dark:text-amber-200 font-bold">-</span>
                                    </td>
                                @else
                                    <td class="p-2 text-center font-mono font-bold text-sm border-r-2 border-border bg-primary/5 text-primary">
                                        {{ $na !== null ? number_format($na, 1) : '-' }}
                                    </td>
                                @endif

                                <!-- Predikat -->
                                <td class="p-2 text-center border-r border-border">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $predikatBadgeClass }}">
                                        {{ $predikat }}
                                    </span>
                                </td>

                                <!-- Narasi Capaian -->
                                <td class="p-2 border-r border-border text-[11px] align-top min-w-[260px]">
                                    @if($isManual)
                                        <div class="text-[11px] leading-relaxed text-text-main mb-1 whitespace-pre-line">{{ $desc }}</div>
                                        <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 font-bold">✏️ Manual Override</span>
                                    @elseif($status === 'belum_lengkap')
                                        <div class="text-[10px] text-amber-800 dark:text-amber-300 font-medium">⚠️ Nilai belum lengkap, silakan hubungi guru yang bersangkutan.</div>
                                    @elseif($status === 'belum_kompeten')
                                        <div class="text-[10px] text-rose-800 dark:text-rose-300 font-medium">🚫 Nilai belum kompeten, silakan hubungi guru yang bersangkutan.</div>
                                    @elseif(!empty($desc))
                                        <div class="text-[10px] text-text-main line-clamp-2">{{ $desc }}</div>
                                    @else
                                        <span class="italic text-text-muted text-[10px]">-</span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="p-2 text-center border-border">
                                    <button wire:click="openModalDeskripsi9M({{ $siswa->id }}, '{{ $targetP }}')" type="button" class="text-[10px] text-primary hover:underline font-semibold cursor-pointer">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="p-6 text-center text-text-secondary">
                                    Belum ada siswa terdaftar pada Mapel Pilihan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @else
            <!-- VIEW 2: DUA BLOK 9 MINGGU BERDAMPINGAN (1 SEMESTER) -->
            <div class="card overflow-x-auto border border-border rounded-xl shadow-2xs">
                <table class="min-w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-variant text-text-secondary font-bold border-b border-border">
                            <th rowspan="2" class="p-2.5 text-center w-10 border-r border-border">No</th>
                            <th rowspan="2" class="p-2.5 min-w-[170px] border-r border-border">Nama Siswa</th>
                            <th rowspan="2" class="p-2.5 w-20 text-center border-r border-border">Rombel</th>

                            <!-- Blok 1 -->
                            <th colspan="5" class="p-2 text-center border-r-2 border-border bg-blue-100/90 dark:bg-blue-950/70 text-blue-950 dark:text-blue-100 font-bold text-xs">
                                {{ $labelBlock1 }}
                            </th>

                            <!-- Blok 2 -->
                            <th colspan="5" class="p-2 text-center border-border bg-emerald-100/90 dark:bg-emerald-950/70 text-emerald-950 dark:text-emerald-100 font-bold text-xs">
                                {{ $labelBlock2 }}
                            </th>
                        </tr>
                        <tr class="text-[10px] font-bold text-text-secondary bg-surface-hover border-b border-border">
                            <!-- Blok 1 Sub-kolom -->
                            <th class="p-1.5 text-center w-14 border-r border-border bg-blue-50/70 text-blue-900">Rata TP</th>
                            <th class="p-1.5 text-center w-14 border-r border-border bg-blue-50/70 text-blue-900">NH</th>
                            <th class="p-1.5 text-center w-14 border-r border-border bg-blue-50/70 text-blue-900">{{ $sumatifLabel1 }}</th>
                            <th class="p-1.5 text-center w-14 border-r border-border bg-blue-200/70 text-blue-950 font-bold">NA</th>
                            <th class="p-1.5 text-center min-w-[140px] border-r-2 border-border bg-blue-50/40">Narasi</th>

                            <!-- Blok 2 Sub-kolom -->
                            <th class="p-1.5 text-center w-14 border-r border-border bg-emerald-50/70 text-emerald-900">Rata TP</th>
                            <th class="p-1.5 text-center w-14 border-r border-border bg-emerald-50/70 text-emerald-900">NH</th>
                            <th class="p-1.5 text-center w-14 border-r border-border bg-emerald-50/70 text-emerald-900">{{ $sumatifLabel2 }}</th>
                            <th class="p-1.5 text-center w-14 border-r border-border bg-emerald-200/70 text-emerald-950 font-bold">NA</th>
                            <th class="p-1.5 text-center min-w-[140px] border-border bg-emerald-50/40">Narasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-surface text-text-main">
                        @forelse($siswas as $idx => $siswa)
                            @php
                                $rekap = $rekapData[$siswa->id] ?? null;
                                $b1 = $rekap ? $rekap->getRecordFor9M($k1) : null;
                                $b2 = $rekap ? $rekap->getRecordFor9M($k2) : null;

                                $rf1 = $b1['rata_tp'] ?? null;
                                $nh1 = $b1['nh'] ?? ($nhData[$siswa->id][$k1] ?? 100.0);
                                $sm1 = $b1['sumatif'] ?? ($sumatifData[$siswa->id][$k1] ?? null);
                                $na1 = $b1['nilai_akhir'] ?? null;
                                $pred1 = $b1['predikat'] ?? 'Belum Lengkap';
                                $desc1 = !empty($b1['deskripsi_manual']) ? $b1['deskripsi_manual'] : trim(($b1['deskripsi_tertinggi'] ?? '') . ' ' . ($b1['deskripsi_terendah'] ?? ''));

                                $rf2 = $b2['rata_tp'] ?? null;
                                $nh2 = $b2['nh'] ?? ($nhData[$siswa->id][$k2] ?? 100.0);
                                $sm2 = $b2['sumatif'] ?? ($sumatifData[$siswa->id][$k2] ?? null);
                                $na2 = $b2['nilai_akhir'] ?? null;
                                $pred2 = $b2['predikat'] ?? 'Belum Lengkap';
                                $desc2 = !empty($b2['deskripsi_manual']) ? $b2['deskripsi_manual'] : trim(($b2['deskripsi_tertinggi'] ?? '') . ' ' . ($b2['deskripsi_terendah'] ?? ''));
                            @endphp
                            <tr class="hover:bg-surface-variant/40 transition">
                                <td class="p-2 text-center text-text-secondary border-r border-border font-mono">{{ $idx + 1 }}</td>
                                <td class="p-2 font-semibold text-text-main border-r border-border">
                                    {{ $siswa->nama }}
                                </td>
                                <td class="p-2 text-center text-text-secondary border-r border-border">
                                    <span class="px-1.5 py-0.5 rounded bg-surface-variant text-[10px] font-bold">
                                        {{ $siswa->rombel?->nama ?? '-' }}
                                    </span>
                                </td>

                                <!-- Blok 1 Data -->
                                <td class="p-1.5 text-center font-mono border-r border-border bg-blue-50/10">{{ $rf1 !== null ? number_format($rf1, 1) : '-' }}</td>
                                <td class="p-1.5 text-center font-mono border-r border-border bg-blue-50/10">{{ $nh1 !== null ? number_format($nh1, 1) : '100.0' }}</td>
                                <td class="p-1.5 text-center font-mono border-r border-border bg-blue-50/10">{{ $sm1 !== null ? number_format($sm1, 1) : '-' }}</td>
                                <td class="p-1.5 text-center font-mono font-bold border-r border-border bg-blue-100/40 text-blue-950">{{ $na1 !== null ? number_format($na1, 1) : '-' }}</td>
                                <td class="p-1.5 border-r-2 border-border text-[10px] bg-blue-50/10">
                                    <div class="line-clamp-2">{{ $desc1 ?: '-' }}</div>
                                    <button wire:click="openModalDeskripsi9M({{ $siswa->id }}, '{{ $k1 }}')" type="button" class="text-[9px] text-primary hover:underline font-semibold mt-0.5">Edit</button>
                                </td>

                                <!-- Blok 2 Data -->
                                <td class="p-1.5 text-center font-mono border-r border-border bg-emerald-50/10">{{ $rf2 !== null ? number_format($rf2, 1) : '-' }}</td>
                                <td class="p-1.5 text-center font-mono border-r border-border bg-emerald-50/10">{{ $nh2 !== null ? number_format($nh2, 1) : '100.0' }}</td>
                                <td class="p-1.5 text-center font-mono border-r border-border bg-emerald-50/10">{{ $sm2 !== null ? number_format($sm2, 1) : '-' }}</td>
                                <td class="p-1.5 text-center font-mono font-bold border-r border-border bg-emerald-100/40 text-emerald-950">{{ $na2 !== null ? number_format($na2, 1) : '-' }}</td>
                                <td class="p-1.5 border-border text-[10px] bg-emerald-50/10">
                                    <div class="line-clamp-2">{{ $desc2 ?: '-' }}</div>
                                    <button wire:click="openModalDeskripsi9M({{ $siswa->id }}, '{{ $k2 }}')" type="button" class="text-[9px] text-emerald-700 hover:underline font-semibold mt-0.5">Edit</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="p-6 text-center text-text-secondary">
                                    Belum ada siswa terdaftar pada Mapel Pilihan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif
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
                        <input type="file" wire:model="importFile" accept=".xlsx,.xls" class="file-input">
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
