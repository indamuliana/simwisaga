<div>
    <div class="mb-4">
        <a href="{{ route('penilaian.index') }}" class="text-primary hover:underline inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Dashboard Penilaian
        </a>
    </div>

    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-4">
        <div>
            <h2 class="text-2xl font-bold text-text-main">Pengolahan Nilai: {{ $penugasan->mapel->nama }}</h2>
            <p class="text-sm text-text-secondary mt-1">
                Kelas: <span class="font-semibold text-text-main">{{ $penugasan->rombel->nama }}</span> | 
                Tahun Ajaran: <span class="font-semibold text-text-main">{{ $penugasan->semester?->tahunAjaran?->nama ?? '2025/2026' }}</span> | 
                Kurikulum Merdeka
            </p>
        </div>
    </div>

    <!-- Tabs Nav -->
    <div class="border-b border-border mb-6 flex overflow-x-auto gap-2">
        <button wire:click="switchTab('tp')" class="px-4 py-2.5 border-b-2 font-medium text-sm whitespace-nowrap transition-colors flex items-center gap-2 {{ $activeTab === 'tp' ? 'border-primary text-primary font-semibold' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            <span>1. Tujuan Pembelajaran (TP / CP)</span>
            <span class="text-xs px-2 py-0.5 rounded-full {{ $activeTab === 'tp' ? 'bg-primary/10 text-primary' : 'bg-surface-hover text-text-secondary' }}">{{ count($allTps) }}</span>
        </button>
        <button wire:click="switchTab('formatif')" class="px-4 py-2.5 border-b-2 font-medium text-sm whitespace-nowrap transition-colors flex items-center gap-2 {{ $activeTab === 'formatif' ? 'border-primary text-primary font-semibold' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            <span>2. Input Nilai Akademik (1 Tahun)</span>
        </button>
        <button wire:click="switchTab('rekap')" class="px-4 py-2.5 border-b-2 font-medium text-sm whitespace-nowrap transition-colors flex items-center gap-2 {{ $activeTab === 'rekap' ? 'border-primary text-primary font-semibold' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            <span>3. Rekap & Capaian Rapor</span>
        </button>
    </div>

    <!-- TAB 1: TUJUAN PEMBELAJARAN (TP / CP) -->
    @if($activeTab === 'tp')
    <div class="card mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-4">
            <div>
                <h3 class="text-lg font-bold text-text-main">Kelola Tujuan Pembelajaran (TP / CP) & Alokasi AH</h3>
                <p class="text-xs text-text-secondary mt-0.5">
                    Tentukan TP dan alokasikan ke Asesmen Harian (AH 1 s.d AH 4 untuk Ganjil, AH 5 s.d AH 8 untuk Genap). Satu AH dapat menaungi 1 atau beberapa TP.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2 self-start md:self-auto">
                @if(count($allTps) > 0)
                <button type="button" wire:click="openCopyTpModal" class="btn btn-secondary text-xs py-1.5 px-3 flex items-center gap-1.5 shadow-xs border border-border hover:border-primary hover:text-primary transition-all">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    <span>📋 Salin TP ke Rombel Lain</span>
                </button>
                <div class="text-xs px-3 py-1.5 bg-blue-50 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200 rounded-lg border border-blue-200 dark:border-blue-900 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Patokan kode berikutnya: <strong>{{ $this->suggestedNextKode }}</strong> (atau <strong>CP {{ $this->suggestedNextNumber }}</strong>)</span>
                </div>
                @endif
            </div>
        </div>
        
        @if(session()->has('message_tp'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-emerald-900 dark:bg-emerald-950/40 dark:border-emerald-800 dark:text-emerald-200 rounded-lg text-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('message_tp') }}</span>
        </div>
        @endif

        @if(session()->has('message_tp_error'))
        <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-900 dark:bg-rose-950/40 dark:border-rose-800 dark:text-rose-200 rounded-lg text-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('message_tp_error') }}</span>
        </div>
        @endif

        <!-- Form Tambah / Edit TP -->
        <form wire:submit.prevent="saveTP" class="p-4 bg-surface-hover/60 border border-border rounded-xl mb-6">
            <div class="text-xs font-bold uppercase tracking-wider text-text-secondary mb-3 flex items-center justify-between">
                <span>{{ $edit_tp_id ? '✏️ Formulir Edit TP / CP' : '➕ Formulir Tambah TP / CP' }}</span>
                <span class="text-[11px] text-text-muted">Setiap TP dialokasikan langsung ke Blok Periode 9M</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Semester Target</label>
                    <select wire:model.live="tp_semester_id" class="w-full text-xs rounded-lg border-border bg-surface text-text-main py-2 px-2.5 focus:border-primary focus:ring-primary font-medium">
                        @foreach($semesters as $s)
                        <option value="{{ $s['id'] }}">{{ $s['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('tp_semester_id') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Target Periode 9M</label>
                    <select wire:model="tp_target_periode" class="w-full text-xs rounded-lg border-border bg-surface text-text-main py-2 px-2.5 focus:border-primary focus:ring-primary font-medium" required>
                        @php
                            $targetSem = collect($semesters)->firstWhere('id', (int) $tp_semester_id);
                            $isGenap = str_contains(strtolower($targetSem['semester'] ?? ''), 'genap');
                        @endphp
                        @if(!$isGenap)
                            <option value="9M1">9M1 (Periode STS 1)</option>
                            <option value="9M2">9M2 (Periode SAS)</option>
                        @else
                            <option value="9M3">9M3 (Periode STS 2)</option>
                            <option value="9M4">9M4 (Periode SAT)</option>
                        @endif
                    </select>
                    @error('tp_target_periode') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-1">
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Urutan</label>
                    <input type="number" wire:model="tp_urutan" min="1" class="w-full text-xs rounded-lg border-border bg-surface text-text-main py-2 px-2 focus:border-primary focus:ring-primary font-bold text-center" required>
                    @error('tp_urutan') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Kode TP / CP</label>
                    <input type="text" wire:model="tp_kode" class="w-full text-xs rounded-lg border-border bg-surface text-text-main py-2 px-2.5 focus:border-primary focus:ring-primary font-mono uppercase font-bold" required placeholder="Misal: {{ $this->suggestedNextKode }}">
                    @error('tp_kode') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Lingkup Materi</label>
                    <input type="text" wire:model="tp_lingkup_materi" class="w-full text-xs rounded-lg border-border bg-surface text-text-main py-2 px-2.5 focus:border-primary focus:ring-primary" placeholder="Contoh: Logika Algoritma">
                    @error('tp_lingkup_materi') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Deskripsi Capaian Kompetensi</label>
                    <input type="text" wire:model="tp_deskripsi" class="w-full text-xs rounded-lg border-border bg-surface text-text-main py-2 px-2.5 focus:border-primary focus:ring-primary" required placeholder="Contoh: Memahami konsep dasar...">
                    @error('tp_deskripsi') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-12 flex justify-end gap-2 mt-1">
                    @if($edit_tp_id)
                    <button type="button" wire:click="cancelEditTP" class="btn btn-secondary text-xs py-1.5 px-3">Batal</button>
                    @endif
                    <button type="submit" class="btn {{ $edit_tp_id ? 'btn-yellow' : 'btn-primary' }} text-xs py-1.5 px-5">
                        {{ $edit_tp_id ? 'Update TP' : 'Tambah TP' }}
                    </button>
                </div>
            </div>
        </form>

        <!-- Tabel Gabungan Seluruh TP (Ganjil & Genap) -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border border-border rounded-lg overflow-hidden">
                <thead class="bg-surface-hover text-xs uppercase tracking-wider">
                    <tr>
                        <th class="p-3 w-12 text-center border-b border-border text-text-main">No</th>
                        <th class="p-3 w-28 text-center border-b border-border text-text-main">Periode 9M</th>
                        <th class="p-3 w-24 border-b border-border text-text-main">Kode</th>
                        <th class="p-3 w-36 border-b border-border text-text-main">Lingkup Materi</th>
                        <th class="p-3 border-b border-border text-text-main">Deskripsi Capaian Pembelajaran</th>
                        <th class="p-3 w-32 text-center border-b border-border text-text-main">Status Formatif</th>
                        <th class="p-3 w-24 text-center border-b border-border text-text-main">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($allTps as $idx => $tp)
                    @php
                        $targetPeriode = $tp->target_periode ?: '9M1';
                        $isGenap = in_array($targetPeriode, ['9M3', '9M4']);
                    @endphp
                    <tr class="hover:bg-surface-hover/50 transition-colors {{ $edit_tp_id === $tp->id ? 'bg-amber-50/70 dark:bg-amber-950/30' : '' }}">
                        <td class="p-3 text-center text-text-secondary text-xs">{{ $loop->iteration }}</td>
                        <td class="p-3 text-center">
                            @if(in_array($targetPeriode, ['9M1', '9M2']))
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/70 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                {{ $targetPeriode }}
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                {{ $targetPeriode }}
                            </span>
                            @endif
                        </td>
                        <td class="p-3 font-bold font-mono text-text-main">{{ $tp->kode }}</td>
                        <td class="p-3 text-xs text-text-secondary font-medium">{{ $tp->lingkup_materi ?: '-' }}</td>
                        <td class="p-3 text-text-main text-xs">{{ $tp->deskripsi }}</td>
                        <td class="p-3 text-center text-xs">
                            @if($tp->nilai_formatifs_count > 0)
                            <span class="inline-flex items-center gap-1 font-semibold text-emerald-700 dark:text-emerald-400">
                                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                {{ $tp->nilai_formatifs_count }} siswa dinilai
                            </span>
                            @else
                            <span class="text-text-muted italic">Belum ada nilai</span>
                            @endif
                        </td>
                        <td class="p-3 text-center">
                            <div class="inline-flex items-center gap-2">
                                <button wire:click="editTP({{ $tp->id }})" class="text-xs text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 font-semibold hover:underline">Edit</button>
                                <span class="text-text-muted">|</span>
                                <button wire:click="hapusTP({{ $tp->id }})" onclick="confirm('Yakin ingin menghapus TP ini beserta seluruh nilai formatif terkait?') || event.stopImmediatePropagation()" class="text-xs text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 font-semibold hover:underline">Hapus</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-text-secondary">
                            Belum ada Tujuan Pembelajaran yang diinput untuk kelas ini. Silakan gunakan formulir di atas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- TAB 2: INPUT NILAI FORMATIF & HARIAN PER TP (9 MINGGU) -->
    @if($activeTab === 'formatif')
    <div class="card mb-6">
        <!-- Switcher Periode 9 Minggu & Tombol Aksi -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-4 p-3 bg-surface-hover/70 rounded-xl border border-border">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-text-secondary">Tampilan Tabel:</span>
                <span class="text-xs text-text-secondary font-medium">Semester Ganjil &bull; Semester Genap &bull; Blok Periode:</span>
                <div class="inline-flex rounded-lg border border-border p-1 bg-surface shadow-xs">
                    @php
                        $periodDisplayLabels = [
                            '9M1' => '9M1 (STS 1 · AH 1, AH 2)',
                            '9M2' => '9M2 (SAS · AH 3, AH 4)',
                            '9M3' => '9M3 (STS 2 · AH 5, AH 6)',
                            '9M4' => '9M4 (SAT · AH 7, AH 8)',
                        ];
                    @endphp
                    @foreach($periodDisplayLabels as $pKey => $pLabel)
                    <button type="button"
                        wire:click="switchPeriode('{{ $pKey }}')"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer {{ $activePeriode === $pKey ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                        <span>{{ $pLabel }}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" wire:click="openImportModal" class="btn btn-secondary text-xs flex items-center justify-center gap-2 py-2 px-3 shadow-xs">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    <span>Import Nilai Excel</span>
                </button>

                <button wire:click="saveNilai" class="btn btn-primary text-xs flex items-center justify-center gap-2 py-2 px-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan Semua Nilai</span>
                </button>
            </div>
        </div>

        @if(session()->has('message_nilai'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-emerald-800 dark:bg-emerald-950/40 dark:border-emerald-800 dark:text-emerald-300 rounded-lg text-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('message_nilai') }}</span>
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 dark:bg-rose-950/40 dark:border-rose-800 dark:text-rose-300 rounded-lg text-sm">
            <p class="font-bold mb-1">Terdapat kesalahan validasi nilai (skala harus 0 s.d. 100):</p>
            <ul class="list-disc pl-5 text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Informasi Penilaian Blok Aktif -->
        <div class="mb-3 px-3 py-2 bg-blue-50/60 dark:bg-blue-950/30 border border-blue-200/80 dark:border-blue-900/50 rounded-lg text-xs text-text-main flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="font-bold text-primary">📌 Periode Aktif: {{ $activePeriode }} ({{ $this->labelSts }})</span>
                <span class="text-text-secondary">&bull;</span>
                <span class="text-text-secondary">Input nilai per Tujuan Pembelajaran (TP) + Nilai Harian (NH, default 100) + {{ $this->labelSts }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/70 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-300 font-bold text-[11px]">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                    <span>KKM: {{ $this->kkm }} &bull; Nilai &lt; {{ $this->kkm }} disorot merah (Belum Kompeten)</span>
                </span>
            </div>
        </div>

        @php
            $activeTps = $this->activeTps;
            $sumatifLabel = $this->labelSts;
        @endphp

        <div class="overflow-x-auto relative max-h-[750px] border border-border rounded-xl shadow-xs">
            <table class="w-full text-sm text-left border-collapse table-nilai">
                <thead class="bg-surface-hover sticky top-0 z-20">
                    <tr>
                        <th class="p-2.5 sm:p-3 border-b-2 border-r-2 border-border sticky left-0 bg-surface dark:bg-surface-card z-30 min-w-[210px] text-text-main font-bold shadow-[4px_0_8px_-2px_rgba(0,0,0,0.15)]">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span>Nama Siswa</span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-100 text-blue-900 dark:bg-blue-950 dark:text-blue-200 font-semibold ml-auto">Freeze</span>
                            </div>
                        </th>

                        <!-- Kolom Dinamis per TP untuk Periode Aktif -->
                        @foreach($activeTps as $tp)
                        <th class="p-2 border-b border-r border-border min-w-[90px] text-center bg-blue-50/50 dark:bg-blue-950/20" title="{{ $tp->deskripsi }}">
                            <div class="font-mono font-bold text-text-main text-xs sm:text-sm">{{ $tp->kode }}</div>
                            <div class="text-[9px] text-text-secondary truncate max-w-[100px] mx-auto mt-0.5" title="{{ $tp->deskripsi }}">
                                {{ $tp->lingkup_materi ?: Str::limit($tp->deskripsi, 18) }}
                            </div>
                        </th>
                        @endforeach

                        <!-- Kolom Nilai Harian (NH) -->
                        <th class="p-2 border-b border-r border-border min-w-[95px] text-center bg-amber-100/70 dark:bg-amber-950/60" title="Nilai Harian subjektif guru (Default: 100)">
                            <div class="font-bold text-amber-950 dark:text-amber-200 text-xs sm:text-sm">NH (Harian)</div>
                            <div class="text-[9px] text-amber-800 dark:text-amber-300 font-medium">Default: 100</div>
                        </th>

                        <!-- Kolom Sumatif (STS 1 / SAS / STS 2 / SAT) -->
                        <th class="p-2 border-b border-border min-w-[85px] text-center bg-blue-100/80 dark:bg-blue-950/80" title="Sumatif {{ $sumatifLabel }}">
                            <div class="font-mono font-bold text-blue-950 dark:text-blue-200 text-xs sm:text-sm">{{ $sumatifLabel }}</div>
                            <div class="text-[9px] text-blue-800 dark:text-blue-300 font-medium">Sumatif</div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($siswas as $siswa)
                    <tr wire:key="siswa-row-{{ $siswa->id }}" class="hover:bg-surface-hover/50 transition-colors">
                        <!-- Freeze Nama Siswa -->
                        <td class="p-2 sm:p-2.5 border-r-2 border-border sticky left-0 bg-surface dark:bg-surface-card z-10 shadow-[4px_0_8px_-2px_rgba(0,0,0,0.15)]">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-mono text-text-muted w-6 text-right">{{ $loop->iteration }}.</span>
                                <div>
                                    <div class="font-semibold text-text-main text-xs sm:text-sm">{{ $siswa->nama }}</div>
                                    <div class="text-[10px] text-text-secondary font-mono">{{ $siswa->nipd ?? $siswa->nisn ?? '-' }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Input Nilai Tiap TP -->
                        @foreach($activeTps as $tp)
                        @php
                            $vTp = $tpScores[$siswa->id][$tp->id] ?? null;
                            $isTpBelow = $this->isBelowKkm($vTp);
                        @endphp
                        <td class="p-1.5 sm:p-2 border-r border-border text-center transition-colors {{ $isTpBelow ? 'bg-rose-100/70 dark:bg-rose-950/50' : '' }}">
                            <input type="number" min="0" max="100" wire:model.blur="tpScores.{{ $siswa->id }}.{{ $tp->id }}"
                                placeholder="0-100"
                                @if($isTpBelow) title="Nilai {{ $vTp }} di bawah KKM ({{ $this->kkm }}) - Belum Kompeten" @endif
                                class="w-14 sm:w-16 text-center rounded-lg px-1 py-1 font-mono text-xs sm:text-sm transition-colors focus:outline-none no-spinner [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none {{ $isTpBelow ? 'border-2 border-rose-500 bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 font-bold focus:border-rose-600 focus:ring-1 focus:ring-rose-600' : 'border border-border bg-surface text-text-main font-semibold placeholder:text-text-muted/50 focus:border-primary focus:ring-1 focus:ring-primary' }}">
                        </td>
                        @endforeach

                        <!-- Input Nilai Harian (NH) -->
                        @php
                            $vNh = $nhData[$siswa->id][$activePeriode] ?? 100.0;
                            $isNhBelow = $this->isBelowKkm($vNh);
                        @endphp
                        <td class="p-1.5 sm:p-2 border-r border-border text-center transition-colors bg-amber-50/30 dark:bg-amber-950/20 {{ $isNhBelow ? 'bg-rose-100/70 dark:bg-rose-950/50' : '' }}">
                            <input type="number" min="0" max="100" wire:model.blur="nhData.{{ $siswa->id }}.{{ $activePeriode }}"
                                placeholder="100"
                                @if($isNhBelow) title="Nilai Harian {{ $vNh }} di bawah KKM ({{ $this->kkm }}) - Belum Kompeten" @endif
                                class="w-14 sm:w-16 text-center rounded-lg px-1 py-1 font-mono text-xs sm:text-sm transition-colors focus:outline-none no-spinner [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none {{ $isNhBelow ? 'border-2 border-rose-500 bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 font-bold focus:border-rose-600 focus:ring-1 focus:ring-rose-600' : 'border border-amber-300 dark:border-amber-700 bg-surface text-amber-950 dark:text-amber-100 font-bold placeholder:text-amber-400 focus:border-primary focus:ring-1 focus:ring-primary' }}">
                        </td>

                        <!-- Input Nilai Sumatif -->
                        @php
                            $vSum = $sumatifData[$siswa->id][$activePeriode] ?? null;
                            if ($vSum === null || $vSum === '') {
                                if ($activePeriode === '9M1' && isset($sumatifDataGanjil[$siswa->id]['sts'])) {
                                    $vSum = $sumatifDataGanjil[$siswa->id]['sts'];
                                } elseif ($activePeriode === '9M2' && isset($sumatifDataGanjil[$siswa->id]['sas'])) {
                                    $vSum = $sumatifDataGanjil[$siswa->id]['sas'];
                                } elseif ($activePeriode === '9M3' && isset($sumatifDataGenap[$siswa->id]['sts'])) {
                                    $vSum = $sumatifDataGenap[$siswa->id]['sts'];
                                } elseif ($activePeriode === '9M4' && isset($sumatifDataGenap[$siswa->id]['sas'])) {
                                    $vSum = $sumatifDataGenap[$siswa->id]['sas'];
                                }
                            }
                            $isSumBelow = $this->isBelowKkm($vSum);
                        @endphp
                        <td class="p-1.5 sm:p-2 border-border text-center transition-colors {{ $isSumBelow ? 'bg-rose-100/70 border-rose-500' : 'bg-blue-50/30 dark:bg-blue-950/20' }}">
                            <input type="number" min="0" max="100" wire:model.blur="sumatifData.{{ $siswa->id }}.{{ $activePeriode }}"
                                placeholder="{{ $sumatifLabel }}"
                                @if($isSumBelow) title="Nilai {{ $sumatifLabel }} {{ $vSum }} di bawah KKM ({{ $this->kkm }}) - Belum Kompeten" @endif
                                class="w-14 sm:w-16 text-center rounded-lg px-1 py-1 font-mono text-xs sm:text-sm transition-colors focus:outline-none no-spinner [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none {{ $isSumBelow ? 'border-rose-500 bg-rose-100/70 text-rose-700 dark:text-rose-300 font-bold focus:border-rose-600 focus:ring-1 focus:ring-rose-600' : 'border border-blue-300 dark:border-blue-700 bg-surface text-blue-950 dark:text-blue-100 font-bold placeholder:text-blue-400 focus:border-primary focus:ring-1 focus:ring-primary' }}">
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 3 + count($activeTps) }}" class="p-8 text-center text-text-secondary bg-surface-hover/30">
                            Belum ada data siswa dalam rombel ini.
                        </td>
                    </tr>
                    @endforelse

                    @if($activeTps->isEmpty())
                    <tr>
                        <td colspan="4" class="p-8 text-center text-text-secondary bg-surface-hover/30">
                            <div class="max-w-md mx-auto space-y-2">
                                <div class="text-3xl">📝</div>
                                <div class="font-bold text-text-main text-base">Belum Ada Tujuan Pembelajaran untuk Periode {{ $activePeriode }}</div>
                                <p class="text-xs text-text-secondary">
                                    Silakan buat Tujuan Pembelajaran (TP) terlebih dahulu pada tab
                                    <button type="button" wire:click="switchTab('tp')" class="text-primary font-bold underline cursor-pointer">1. Tujuan Pembelajaran (TP / CP)</button>
                                    dan tentukan Target Periode 9M ke <strong>{{ $activePeriode }}</strong>.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- TAB 3: REKAPITULASI & CAPAIAN PER 9 MINGGU -->
    @if($activeTab === 'rekap')
    @php
        $settings = \App\Modules\Penilaian\Models\PengaturanPenilaian::getActive();
        $bobotTp = $settings->bobot_tp ?? 50;
        $bobotSts = $settings->bobot_sts ?? 30;
        $bobotNh = $settings->bobot_nh ?? 20;

        $currentSem = collect($semesters)->firstWhere('id', (int) $activeNilaiSemesterId);
        $semName = strtolower($currentSem['semester'] ?? '');
        $isGenap = str_contains($semName, 'genap');
        $k1 = $isGenap ? '9M3' : '9M1';
        $k2 = $isGenap ? '9M4' : '9M2';
        $labelBlock1 = $isGenap ? '9 Minggu ke-3 (STS 2)' : '9 Minggu ke-1 (STS 1)';
        $labelBlock2 = $isGenap ? '9 Minggu ke-4 (SAT)' : '9 Minggu ke-2 (SAS)';
        $sumatifLabel1 = $isGenap ? 'STS 2' : 'STS 1';
        $sumatifLabel2 = $isGenap ? 'SAT' : 'SAS';

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

    <div class="card mb-6 space-y-4">
        <!-- Toolbar & Periode Switcher -->
        <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-3 p-3 bg-surface-hover/70 rounded-xl border border-border">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-text-secondary mr-1">Rekap Semester:</span>
                <div class="inline-flex flex-wrap rounded-lg border border-border p-1 bg-surface shadow-xs gap-1">
                    @if(!$isGenap)
                    <button type="button"
                        wire:click="switchRekapPeriode('9M1')"
                        class="px-3 py-1 rounded-md text-xs font-semibold transition-all cursor-pointer {{ $rekapPeriode === '9M1' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                        <span>9M1 (STS 1)</span>
                    </button>
                    <button type="button"
                        wire:click="switchRekapPeriode('9M2')"
                        class="px-3 py-1 rounded-md text-xs font-semibold transition-all cursor-pointer {{ $rekapPeriode === '9M2' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                        <span>9M2 (SAS)</span>
                    </button>
                    @else
                    <button type="button"
                        wire:click="switchRekapPeriode('9M3')"
                        class="px-3 py-1 rounded-md text-xs font-semibold transition-all cursor-pointer {{ $rekapPeriode === '9M3' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                        <span>9M3 (STS 2)</span>
                    </button>
                    <button type="button"
                        wire:click="switchRekapPeriode('9M4')"
                        class="px-3 py-1 rounded-md text-xs font-semibold transition-all cursor-pointer {{ $rekapPeriode === '9M4' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                        <span>9M4 (SAT)</span>
                    </button>
                    @endif
                    <button type="button"
                        wire:click="switchRekapPeriode('semester')"
                        class="px-3 py-1 rounded-md text-xs font-semibold transition-all cursor-pointer {{ $rekapPeriode === 'semester' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                        <span>1 Semester (Berdampingan)</span>
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
                @if (session('message_rekap'))
                    <span class="text-xs text-emerald-600 font-semibold animate-pulse">
                        ✓ {{ session('message_rekap') }}
                    </span>
                @endif
                <button wire:click="calculateRekap" class="btn btn-secondary text-xs flex items-center gap-2 py-1.5 px-3 cursor-pointer shadow-2xs font-semibold">
                    <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span>Hitung Ulang Rekap 9M</span>
                </button>
            </div>
        </div>

        @if($isSingleView)
        <!-- VIEW 1: SINGLE 9M FOCUS (Tampil Rata TP, Harian/NH, STS, NA 9M, Predikat, Narasi Capaian) -->
        <div class="overflow-x-auto max-h-[640px] relative border border-border rounded-xl shadow-2xs">
            <table class="min-w-full border-separate border-spacing-0 text-left text-xs">
                <thead class="sticky top-0 z-30 shadow-2xs">
                    <tr class="text-[11px] uppercase tracking-wider font-semibold bg-surface-hover text-text-secondary">
                        <th class="w-10 min-w-[40px] max-w-[40px] p-2 text-center bg-slate-100 dark:bg-slate-800 border-b-2 border-r border-border font-bold text-text-main sticky left-0 z-40">
                            No
                        </th>
                        <th class="min-w-[180px] max-w-[220px] p-2.5 text-left bg-slate-100 dark:bg-slate-800 border-b-2 border-r-2 border-border font-bold text-text-main sticky left-[40px] z-40 shadow-[4px_0_8px_-2px_rgba(0,0,0,0.12)] whitespace-nowrap">
                            Nama Peserta Didik
                        </th>
                        <th class="p-2 text-center min-w-[95px] border-b-2 border-r border-border bg-blue-50/70 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200" title="Rata-rata Seluruh Tujuan Pembelajaran">
                            Rata-rata TP ({{ $bobotTp }}%)
                        </th>
                        <th class="p-2 text-center min-w-[95px] border-b-2 border-r border-border bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200" title="Nilai Harian / Subjektif Guru (Default: 100)">
                            Harian / NH ({{ $bobotNh }}%)
                        </th>
                        <th class="p-2 text-center min-w-[95px] border-b-2 border-r border-border bg-purple-50/70 dark:bg-purple-950/40 text-purple-900 dark:text-purple-200" title="Asesmen Sumatif ({{ $sumatifLabelSingle }})">
                            {{ $sumatifLabelSingle }} ({{ $bobotSts }}%)
                        </th>
                        <th class="p-2 text-center min-w-[85px] border-b-2 border-r-2 border-border bg-primary/10 text-primary font-extrabold" title="Nilai Akhir {{ $targetP }}">
                            NA {{ $targetP }}
                        </th>
                        <th class="p-2 text-center min-w-[130px] border-b-2 border-r border-border bg-slate-100/90 dark:bg-slate-800/90 font-bold text-text-main">
                            Predikat Capaian
                        </th>
                        <th class="p-2 text-left min-w-[280px] border-b-2 border-r border-border text-text-main">
                            Narasi Capaian (Rapor {{ $targetP }})
                        </th>
                        <th class="p-2 text-center w-20 min-w-[70px] border-b-2 border-border font-bold text-text-secondary">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-surface text-text-main">
                    @foreach($siswas as $idx => $siswa)
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
                    <tr class="hover:bg-surface-hover/60 transition-colors">
                        <td class="w-10 min-w-[40px] max-w-[40px] p-2 text-center font-mono text-text-secondary sticky left-0 z-20 bg-surface dark:bg-surface-card border-b border-r border-border">
                            {{ $loop->iteration }}
                        </td>
                        <td class="min-w-[180px] max-w-[220px] p-2.5 font-medium sticky left-[40px] z-20 bg-surface dark:bg-surface-card border-b border-r-2 border-border shadow-[4px_0_8px_-2px_rgba(0,0,0,0.12)] whitespace-nowrap">
                            <div class="truncate text-xs font-semibold text-text-main" title="{{ $siswa->nama }}">{{ $siswa->nama }}</div>
                            <div class="text-[10px] text-text-secondary font-mono">{{ $siswa->nipd ?? $siswa->nisn ?? '-' }}</div>
                        </td>

                        <!-- Rata TP -->
                        <td class="p-2 text-center font-mono font-medium border-b border-r border-border bg-blue-50/20 dark:bg-blue-950/10">
                            {{ $rataTp !== null ? number_format($rataTp, 1) : '-' }}
                        </td>

                        <!-- Harian / NH -->
                        <td class="p-2 text-center font-mono font-medium border-b border-r border-border bg-emerald-50/20 dark:bg-emerald-950/10">
                            {{ $nh !== null ? number_format($nh, 1) : '100.0' }}
                        </td>

                        <!-- STS / SAS -->
                        <td class="p-2 text-center font-mono font-medium border-b border-r border-border bg-purple-50/20 dark:bg-purple-950/10">
                            {{ $sumatif !== null ? number_format($sumatif, 1) : '-' }}
                        </td>

                        <!-- NA 9M -->
                        @if($status === 'belum_kompeten')
                            <td class="p-2 text-center font-mono font-bold text-xs border-b border-r-2 border-border bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300"
                                title="Belum Kompeten: Ada nilai TP/Sumatif di bawah KKM ({{ $this->kkm }})">
                                <span class="inline-block px-2 py-0.5 rounded bg-rose-100 dark:bg-rose-900/60 text-rose-800 dark:text-rose-200 font-bold">-</span>
                            </td>
                        @elseif($status === 'belum_lengkap')
                            <td class="p-2 text-center font-mono font-bold text-xs border-b border-r-2 border-border bg-amber-50/50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300"
                                title="Nilai Belum Lengkap: Ada nilai TP/Sumatif yang belum terisi">
                                <span class="inline-block px-2 py-0.5 rounded bg-amber-100/80 dark:bg-amber-900/40 text-amber-800 dark:text-amber-200 font-bold">-</span>
                            </td>
                        @else
                            <td class="p-2 text-center font-mono font-bold text-sm border-b border-r-2 border-border bg-primary/5 text-primary">
                                {{ $na !== null ? number_format($na, 1) : '-' }}
                            </td>
                        @endif

                        <!-- Predikat -->
                        <td class="p-2 text-center border-b border-r border-border">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold border {{ $predikatBadgeClass }}">
                                {{ $predikat }}
                            </span>
                        </td>

                        <!-- Narasi Capaian -->
                        <td class="p-2 border-b border-r border-border text-[11px] align-top min-w-[280px]">
                            @if($isManual)
                                <div class="text-[11px] leading-relaxed text-text-main mb-1.5 whitespace-pre-line" title="{{ $desc }}">
                                    {{ $desc }}
                                </div>
                                <div class="flex items-center gap-1 pt-1 border-t border-border/40">
                                    <span class="inline-flex items-center gap-0.5 text-[9px] px-1.5 py-0.2 rounded bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-200 font-bold">
                                        ✏️ Manual Override
                                    </span>
                                </div>
                            @elseif($status === 'belum_lengkap')
                                <div class="p-2 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/50 text-amber-900 dark:text-amber-200">
                                    <div class="font-bold flex items-center gap-1 text-xs text-amber-800 dark:text-amber-300">
                                        ⚠️ Nilai belum lengkap
                                    </div>
                                    <div class="text-[10px] mt-0.5 text-amber-700 dark:text-amber-300/90 leading-tight">
                                        Silakan hubungi guru yang bersangkutan.
                                    </div>
                                </div>
                            @elseif($status === 'belum_kompeten')
                                <div class="p-2 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/50 text-rose-900 dark:text-rose-200">
                                    <div class="font-bold flex items-center gap-1 text-xs text-rose-800 dark:text-rose-300">
                                        🚫 Nilai belum kompeten
                                    </div>
                                    <div class="text-[10px] mt-0.5 text-rose-700 dark:text-rose-300/90 leading-tight">
                                        Silakan hubungi guru yang bersangkutan.
                                    </div>
                                </div>
                            @elseif(!empty($d['deskripsi_tertinggi']) || !empty($d['deskripsi_terendah']))
                                <div class="space-y-1 text-[11px]">
                                    @if(!empty($d['deskripsi_tertinggi']))
                                        <div class="leading-snug text-emerald-800 dark:text-emerald-300 font-medium" title="{{ $d['deskripsi_tertinggi'] }}">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 align-middle"></span>{{ $d['deskripsi_tertinggi'] }}
                                        </div>
                                    @endif
                                    @if(!empty($d['deskripsi_terendah']))
                                        <div class="leading-snug text-slate-700 dark:text-slate-300 font-normal" title="{{ $d['deskripsi_terendah'] }}">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-slate-400 mr-1 align-middle"></span>{{ $d['deskripsi_terendah'] }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="italic text-text-muted text-[11px]">Belum ada data nilai</span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="p-2 text-center border-b border-border">
                            <button type="button"
                                    wire:click="openEditDeskripsi9M({{ $siswa->id }}, '{{ $targetP }}')"
                                    class="text-[11px] text-primary hover:underline font-semibold cursor-pointer inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                <span>Edit</span>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <!-- VIEW 2: DUA BLOK 9 MINGGU BERDAMPINGAN (1 SEMESTER) -->
        <div class="overflow-x-auto max-h-[640px] relative border border-border rounded-xl shadow-2xs">
            <table class="min-w-full border-separate border-spacing-0 text-left text-xs">
                <thead class="sticky top-0 z-30 shadow-2xs">
                    <!-- HEADER BARIS 1: Header Blok 1 & Blok 2 -->
                    <tr class="text-[11px] uppercase tracking-wider font-semibold">
                        <th rowspan="2" class="sticky top-0 left-0 z-40 w-10 min-w-[40px] max-w-[40px] p-2 text-center bg-slate-100 dark:bg-slate-800 border-b-2 border-r border-border font-bold text-text-main">
                            No
                        </th>
                        <th rowspan="2" class="sticky top-0 left-[40px] z-40 min-w-[170px] max-w-[210px] p-2.5 text-left bg-slate-100 dark:bg-slate-800 border-b-2 border-r-2 border-border font-bold text-text-main shadow-[4px_0_8px_-2px_rgba(0,0,0,0.12)] whitespace-nowrap">
                            Nama Peserta Didik
                        </th>

                        <!-- Blok 1 (9M1 / 9M3) -->
                        <th colspan="6" class="p-2 text-center border-b border-r-2 border-border bg-blue-100/90 dark:bg-blue-950/70 text-blue-950 dark:text-blue-100 font-bold text-xs tracking-wider">
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                <span>{{ $labelBlock1 }}</span>
                            </div>
                        </th>

                        <!-- Blok 2 (9M2 / 9M4) -->
                        <th colspan="6" class="p-2 text-center border-b border-border bg-emerald-100/90 dark:bg-emerald-950/70 text-emerald-950 dark:text-emerald-100 font-bold text-xs tracking-wider">
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                <span>{{ $labelBlock2 }}</span>
                            </div>
                        </th>
                    </tr>

                    <!-- HEADER BARIS 2: Komponen Nilai Blok 1 & Blok 2 -->
                    <tr class="text-[10px] font-bold text-text-secondary bg-surface-hover">
                        <!-- Blok 1 Sub-kolom -->
                        <th class="p-2 text-center min-w-[75px] border-b-2 border-r border-border bg-blue-50/70 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200">
                            Rata TP ({{ $bobotTp }}%)
                        </th>
                        <th class="p-2 text-center min-w-[75px] border-b-2 border-r border-border bg-blue-50/70 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200">
                            NH ({{ $bobotNh }}%)
                        </th>
                        <th class="p-2 text-center min-w-[75px] border-b-2 border-r border-border bg-blue-50/70 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200">
                            {{ $sumatifLabel1 }} ({{ $bobotSts }}%)
                        </th>
                        <th class="p-2 text-center min-w-[65px] border-b-2 border-r border-border bg-blue-200/70 dark:bg-blue-900/60 text-blue-950 dark:text-blue-50 font-extrabold">
                            NA {{ $k1 }}
                        </th>
                        <th class="p-2 text-center min-w-[105px] border-b-2 border-r border-border bg-blue-50/60 dark:bg-blue-950/40 text-text-main font-bold">
                            Predikat
                        </th>
                        <th class="p-2 text-left min-w-[220px] max-w-[280px] border-b-2 border-r-2 border-border bg-blue-50/40 dark:bg-blue-950/30 text-text-main">
                            Narasi Capaian {{ $k1 }}
                        </th>

                        <!-- Blok 2 Sub-kolom -->
                        <th class="p-2 text-center min-w-[75px] border-b-2 border-r border-border bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200">
                            Rata TP ({{ $bobotTp }}%)
                        </th>
                        <th class="p-2 text-center min-w-[75px] border-b-2 border-r border-border bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200">
                            NH ({{ $bobotNh }}%)
                        </th>
                        <th class="p-2 text-center min-w-[75px] border-b-2 border-r border-border bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200">
                            {{ $sumatifLabel2 }} ({{ $bobotSts }}%)
                        </th>
                        <th class="p-2 text-center min-w-[65px] border-b-2 border-r border-border bg-emerald-200/70 dark:bg-emerald-900/60 text-emerald-950 dark:text-emerald-50 font-extrabold">
                            NA {{ $k2 }}
                        </th>
                        <th class="p-2 text-center min-w-[105px] border-b-2 border-r border-border bg-emerald-50/60 dark:bg-emerald-950/40 text-text-main font-bold">
                            Predikat
                        </th>
                        <th class="p-2 text-left min-w-[220px] max-w-[280px] border-b-2 border-border bg-emerald-50/40 dark:bg-emerald-950/30 text-text-main">
                            Narasi Capaian {{ $k2 }}
                        </th>
                    </tr>
                </thead>

                <!-- BODY DATA SISWA 1 SEMESTER -->
                <tbody class="divide-y divide-border bg-surface text-text-main">
                    @foreach($siswas as $idx => $siswa)
                    @php
                        $rekap = $rekapData[$siswa->id] ?? null;
                        $d1 = $rekap ? $rekap->getRecordFor9M($k1) : null;
                        $d2 = $rekap ? $rekap->getRecordFor9M($k2) : null;

                        $rf1 = $d1['rata_tp'] ?? null;
                        $nh1 = $d1['nh'] ?? ($nhData[$siswa->id][$k1] ?? 100.0);
                        $sm1 = $d1['sumatif'] ?? ($sumatifData[$siswa->id][$k1] ?? null);
                        $na1 = $d1['nilai_akhir'] ?? null;
                        $pred1 = $d1['predikat'] ?? 'Belum Lengkap';
                        $status1 = $d1['status'] ?? 'belum_ada_nilai';

                        $rf2 = $d2['rata_tp'] ?? null;
                        $nh2 = $d2['nh'] ?? ($nhData[$siswa->id][$k2] ?? 100.0);
                        $sm2 = $d2['sumatif'] ?? ($sumatifData[$siswa->id][$k2] ?? null);
                        $na2 = $d2['nilai_akhir'] ?? null;
                        $pred2 = $d2['predikat'] ?? 'Belum Lengkap';
                        $status2 = $d2['status'] ?? 'belum_ada_nilai';

                        $isManual1 = !empty($d1['deskripsi_manual']);
                        $desc1 = $isManual1 ? $d1['deskripsi_manual'] : trim(($d1['deskripsi_tertinggi'] ?? '') . ' ' . ($d1['deskripsi_terendah'] ?? ''));

                        $isManual2 = !empty($d2['deskripsi_manual']);
                        $desc2 = $isManual2 ? $d2['deskripsi_manual'] : trim(($d2['deskripsi_tertinggi'] ?? '') . ' ' . ($d2['deskripsi_terendah'] ?? ''));

                        $badgePred1 = match($pred1) {
                            'Mahir' => 'bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-200 border-emerald-300',
                            'Cakap' => 'bg-indigo-100 dark:bg-indigo-950/70 text-indigo-800 dark:text-indigo-200 border-indigo-300',
                            'Layak' => 'bg-sky-100 dark:bg-sky-950/70 text-sky-800 dark:text-sky-200 border-sky-300',
                            'Mulai Berkembang' => 'bg-amber-100 dark:bg-amber-950/70 text-amber-800 dark:text-amber-200 border-amber-300',
                            'Belum Kompeten' => 'bg-rose-100 dark:bg-rose-950/70 text-rose-800 dark:text-rose-200 border-rose-300 font-bold',
                            'Belum Lengkap' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-300',
                            default => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200',
                        };

                        $badgePred2 = match($pred2) {
                            'Mahir' => 'bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-200 border-emerald-300',
                            'Cakap' => 'bg-indigo-100 dark:bg-indigo-950/70 text-indigo-800 dark:text-indigo-200 border-indigo-300',
                            'Layak' => 'bg-sky-100 dark:bg-sky-950/70 text-sky-800 dark:text-sky-200 border-sky-300',
                            'Mulai Berkembang' => 'bg-amber-100 dark:bg-amber-950/70 text-amber-800 dark:text-amber-200 border-amber-300',
                            'Belum Kompeten' => 'bg-rose-100 dark:bg-rose-950/70 text-rose-800 dark:text-rose-200 border-rose-300 font-bold',
                            'Belum Lengkap' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-300',
                            default => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200',
                        };
                    @endphp
                    <tr class="hover:bg-surface-hover/60 transition-colors">
                        <td class="w-10 min-w-[40px] max-w-[40px] p-2 text-center font-mono text-text-secondary sticky left-0 z-20 bg-surface dark:bg-surface-card border-b border-r border-border">
                            {{ $loop->iteration }}
                        </td>
                        <td class="min-w-[170px] max-w-[210px] p-2.5 font-medium sticky left-[40px] z-20 bg-surface dark:bg-surface-card border-b border-r-2 border-border shadow-[4px_0_8px_-2px_rgba(0,0,0,0.12)] whitespace-nowrap">
                            <div class="truncate text-xs font-semibold text-text-main" title="{{ $siswa->nama }}">{{ $siswa->nama }}</div>
                            <div class="text-[10px] text-text-secondary font-mono">{{ $siswa->nipd ?? $siswa->nisn ?? '-' }}</div>
                        </td>

                        <!-- BLOK 1: Rata TP -->
                        <td class="p-2 text-center font-mono font-medium border-b border-r border-border bg-blue-50/20 dark:bg-blue-950/10">
                            {{ $rf1 !== null ? number_format($rf1, 1) : '-' }}
                        </td>

                        <!-- BLOK 1: Harian NH -->
                        <td class="p-2 text-center font-mono font-medium border-b border-r border-border bg-blue-50/20 dark:bg-blue-950/10">
                            {{ $nh1 !== null ? number_format($nh1, 1) : '100.0' }}
                        </td>

                        <!-- BLOK 1: Sumatif STS -->
                        <td class="p-2 text-center font-mono font-medium border-b border-r border-border bg-blue-50/20 dark:bg-blue-950/10">
                            {{ $sm1 !== null ? number_format($sm1, 1) : '-' }}
                        </td>

                        <!-- BLOK 1: NA -->
                        @if($status1 === 'belum_kompeten')
                            <td class="p-2 text-center font-mono font-bold text-xs border-b border-r border-border bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300">
                                <span class="inline-block px-1.5 py-0.5 rounded bg-rose-100 dark:bg-rose-900/60 text-rose-800 dark:text-rose-200 font-bold">-</span>
                            </td>
                        @elseif($status1 === 'belum_lengkap')
                            <td class="p-2 text-center font-mono font-bold text-xs border-b border-r border-border bg-amber-50/50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300">
                                <span class="inline-block px-1.5 py-0.5 rounded bg-amber-100/80 dark:bg-amber-900/40 text-amber-800 dark:text-amber-200 font-bold">-</span>
                            </td>
                        @else
                            <td class="p-2 text-center font-mono font-bold text-xs border-b border-r border-border bg-blue-100/40 dark:bg-blue-950/30 text-blue-950 dark:text-blue-100">
                                {{ $na1 !== null ? number_format($na1, 1) : '-' }}
                            </td>
                        @endif

                        <!-- BLOK 1: Predikat -->
                        <td class="p-2 text-center border-b border-r border-border">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-semibold border {{ $badgePred1 }}">
                                {{ $pred1 }}
                            </span>
                        </td>

                        <!-- BLOK 1: Narasi -->
                        <td class="p-2 border-b border-r-2 border-border text-[11px] align-top bg-blue-50/10 dark:bg-blue-950/5 min-w-[220px]">
                            @if($isManual1)
                                <div class="text-[11px] leading-relaxed text-text-main mb-1 whitespace-pre-line">{{ $desc1 }}</div>
                                <div class="flex items-center justify-between gap-1 pt-1 border-t border-border/40">
                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 font-bold">✏️ Manual</span>
                                    <button type="button" wire:click="openEditDeskripsi9M({{ $siswa->id }}, '{{ $k1 }}')" class="text-[10px] text-primary hover:underline font-semibold cursor-pointer">Edit</button>
                                </div>
                            @elseif($status1 === 'belum_lengkap')
                                <div class="text-[10px] text-amber-800 dark:text-amber-300">⚠️ Nilai belum lengkap</div>
                                <button type="button" wire:click="openEditDeskripsi9M({{ $siswa->id }}, '{{ $k1 }}')" class="text-[10px] text-primary hover:underline font-semibold cursor-pointer mt-1">Edit Narasi</button>
                            @elseif($status1 === 'belum_kompeten')
                                <div class="text-[10px] text-rose-800 dark:text-rose-300">🚫 Nilai belum kompeten</div>
                                <button type="button" wire:click="openEditDeskripsi9M({{ $siswa->id }}, '{{ $k1 }}')" class="text-[10px] text-primary hover:underline font-semibold cursor-pointer mt-1">Edit Narasi</button>
                            @elseif(!empty($desc1))
                                <div class="text-[10px] text-text-main line-clamp-2">{{ $desc1 }}</div>
                                <button type="button" wire:click="openEditDeskripsi9M({{ $siswa->id }}, '{{ $k1 }}')" class="text-[10px] text-primary hover:underline font-semibold cursor-pointer mt-1">Edit Narasi</button>
                            @else
                                <span class="italic text-text-muted text-[10px]">-</span>
                            @endif
                        </td>

                        <!-- BLOK 2: Rata TP -->
                        <td class="p-2 text-center font-mono font-medium border-b border-r border-border bg-emerald-50/20 dark:bg-emerald-950/10">
                            {{ $rf2 !== null ? number_format($rf2, 1) : '-' }}
                        </td>

                        <!-- BLOK 2: Harian NH -->
                        <td class="p-2 text-center font-mono font-medium border-b border-r border-border bg-emerald-50/20 dark:bg-emerald-950/10">
                            {{ $nh2 !== null ? number_format($nh2, 1) : '100.0' }}
                        </td>

                        <!-- BLOK 2: Sumatif SAS / SAT -->
                        <td class="p-2 text-center font-mono font-medium border-b border-r border-border bg-emerald-50/20 dark:bg-emerald-950/10">
                            {{ $sm2 !== null ? number_format($sm2, 1) : '-' }}
                        </td>

                        <!-- BLOK 2: NA -->
                        @if($status2 === 'belum_kompeten')
                            <td class="p-2 text-center font-mono font-bold text-xs border-b border-r border-border bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300">
                                <span class="inline-block px-1.5 py-0.5 rounded bg-rose-100 dark:bg-rose-900/60 text-rose-800 dark:text-rose-200 font-bold">-</span>
                            </td>
                        @elseif($status2 === 'belum_lengkap')
                            <td class="p-2 text-center font-mono font-bold text-xs border-b border-r border-border bg-amber-50/50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300">
                                <span class="inline-block px-1.5 py-0.5 rounded bg-amber-100/80 dark:bg-amber-900/40 text-amber-800 dark:text-amber-200 font-bold">-</span>
                            </td>
                        @else
                            <td class="p-2 text-center font-mono font-bold text-xs border-b border-r border-border bg-emerald-100/40 dark:bg-emerald-950/30 text-emerald-950 dark:text-emerald-100">
                                {{ $na2 !== null ? number_format($na2, 1) : '-' }}
                            </td>
                        @endif

                        <!-- BLOK 2: Predikat -->
                        <td class="p-2 text-center border-b border-r border-border">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-semibold border {{ $badgePred2 }}">
                                {{ $pred2 }}
                            </span>
                        </td>

                        <!-- BLOK 2: Narasi -->
                        <td class="p-2 border-b border-border text-[11px] align-top bg-emerald-50/10 dark:bg-emerald-950/5 min-w-[220px]">
                            @if($isManual2)
                                <div class="text-[11px] leading-relaxed text-text-main mb-1 whitespace-pre-line">{{ $desc2 }}</div>
                                <div class="flex items-center justify-between gap-1 pt-1 border-t border-border/40">
                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 font-bold">✏️ Manual</span>
                                    <button type="button" wire:click="openEditDeskripsi9M({{ $siswa->id }}, '{{ $k2 }}')" class="text-[10px] text-emerald-700 hover:underline font-semibold cursor-pointer">Edit</button>
                                </div>
                            @elseif($status2 === 'belum_lengkap')
                                <div class="text-[10px] text-amber-800 dark:text-amber-300">⚠️ Nilai belum lengkap</div>
                                <button type="button" wire:click="openEditDeskripsi9M({{ $siswa->id }}, '{{ $k2 }}')" class="text-[10px] text-emerald-700 hover:underline font-semibold cursor-pointer mt-1">Edit Narasi</button>
                            @elseif($status2 === 'belum_kompeten')
                                <div class="text-[10px] text-rose-800 dark:text-rose-300">🚫 Nilai belum kompeten</div>
                                <button type="button" wire:click="openEditDeskripsi9M({{ $siswa->id }}, '{{ $k2 }}')" class="text-[10px] text-emerald-700 hover:underline font-semibold cursor-pointer mt-1">Edit Narasi</button>
                            @elseif(!empty($desc2))
                                <div class="text-[10px] text-text-main line-clamp-2">{{ $desc2 }}</div>
                                <button type="button" wire:click="openEditDeskripsi9M({{ $siswa->id }}, '{{ $k2 }}')" class="text-[10px] text-emerald-700 hover:underline font-semibold cursor-pointer mt-1">Edit Narasi</button>
                            @else
                                <span class="italic text-text-muted text-[10px]">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @endif

    <!-- MODAL EDIT DESKRIPSI 9 MINGGU -->
    @if($showModalDeskripsi9M)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closeEditDeskripsi9M"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-surface border border-border text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg text-text-main" @click.stop>
                <!-- Header Modal -->
                <div class="px-5 py-4 border-b border-border flex items-center justify-between bg-surface-hover/40">
                    <div>
                        <h3 class="text-sm font-bold text-text-main flex items-center gap-2">
                            <span>✏️ Edit Deskripsi Capaian Rapor</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-primary/10 text-primary">
                                {{ $selectedPeriodKey9M }}
                            </span>
                        </h3>
                        <p class="text-xs text-text-secondary mt-0.5">{{ $selectedSiswaNama9M }} &bull; {{ $selectedPeriodLabel9M }}</p>
                    </div>
                    <button type="button" wire:click="closeEditDeskripsi9M" class="text-text-secondary hover:text-text-main text-lg font-bold p-1 cursor-pointer">
                        &times;
                    </button>
                </div>

                <!-- Body Modal -->
                <div class="p-5 space-y-4 text-xs">
                    <!-- Referensi Deskripsi Otomatis -->
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-border space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-[11px] text-text-secondary uppercase tracking-wider flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Rekomendasi Deskripsi Sistem (2 Baris):
                            </span>
                            <button type="button"
                                    wire:click="copyAutoToManual9M"
                                    class="text-[11px] text-primary hover:underline font-semibold cursor-pointer flex items-center gap-1">
                                <span>📋 Salin ke Input</span>
                            </button>
                        </div>
                        <p class="text-text-secondary leading-relaxed italic whitespace-pre-line text-[11px] pt-1">
                            {{ $autoDeskripsiInfo9M }}
                        </p>
                    </div>

                    <!-- Textarea Edit Manual (Override) -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block font-bold text-text-main text-xs">
                                Narasi Capaian Rapor (Override):
                            </label>
                            <span class="text-[10px] text-text-muted">Gunakan Enter untuk memisahkan 2 baris</span>
                        </div>
                        <textarea
                            wire:model="tempDeskripsiManual9M"
                            rows="4"
                            placeholder="Baris 1: Menunjukkan penguasaan yang sangat baik dalam...&#10;Baris 2: Perlu pemantapan/bimbingan dalam..."
                            class="w-full border border-border bg-surface text-text-main p-2.5 rounded-xl text-xs focus:ring-2 focus:ring-primary focus:border-primary leading-relaxed font-sans"
                        ></textarea>
                        <p class="text-[11px] text-text-secondary">
                            *Jika diisi, narasi di atas yang akan tercetak pada lembar Rapor Siswa. Kosongkan untuk kembali ke deskripsi otomatis sistem.
                        </p>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="px-5 py-3.5 bg-surface-hover/40 border-t border-border flex items-center justify-between">
                    <button type="button"
                            wire:click="resetToAutoDeskripsi9M"
                            class="btn btn-outline text-xs text-text-secondary hover:text-text-main py-1.5 px-3 cursor-pointer">
                        Kembalikan ke Otomatis
                    </button>

                    <div class="flex items-center gap-2">
                        <button type="button"
                                wire:click="closeEditDeskripsi9M"
                                class="btn btn-secondary text-xs py-1.5 px-3 cursor-pointer">
                            Batal
                        </button>
                        <button type="button"
                                wire:click="saveDeskripsi9M"
                                class="btn btn-primary text-xs py-1.5 px-4 font-semibold cursor-pointer">
                            Simpan Narasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL IMPORT NILAI EXCEL -->
    @if($showImportModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closeImportModal"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-surface border border-border text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl text-text-main" @click.stop>
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-border bg-surface-hover/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-text-main" id="modal-title">
                                {{ $importStep === 'select' ? '📥 Import Nilai Excel' : '📋 Pratinjau & Konfirmasi Import' }}
                            </h3>
                            <p class="text-xs text-text-secondary mt-0.5">
                                Mapel: <span class="font-semibold text-text-main">{{ $penugasan->mapel->nama }}</span> | 
                                Rombel: <span class="font-semibold text-text-main">{{ $penugasan->rombel->nama }}</span>
                            </p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeImportModal" class="text-text-muted hover:text-text-main p-1.5 rounded-lg hover:bg-surface transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Body: LANGKAH 1 (Pilih Kolom & Upload) -->
                @if($importStep === 'select')
                <div class="p-6 space-y-5 text-sm">
                    
                    <!-- 1. Cakupan Rombel Template -->
                    <div class="p-4 bg-surface-hover/40 border border-border rounded-xl">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-text-secondary flex items-center gap-1.5">
                                <span>👥 Cakupan Siswa / Rombel</span>
                            </label>
                            <span class="text-[11px] text-text-muted">Deteksi rombel otomatis via NIS</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            <label class="flex items-start gap-2.5 p-2.5 rounded-lg border cursor-pointer transition-colors {{ $importRombelScope === 'current' ? 'border-primary bg-primary/5 text-primary font-semibold' : 'border-border bg-surface text-text-main hover:bg-surface-hover' }}">
                                <input type="radio" wire:model.live="importRombelScope" value="current" class="mt-0.5 text-primary focus:ring-primary">
                                <div>
                                    <div class="font-medium">Hanya Rombel Aktif</div>
                                    <div class="text-[10px] text-text-muted font-normal mt-0.5">Khusus siswa {{ $penugasan->rombel->nama }} saja</div>
                                </div>
                            </label>
                            <label class="flex items-start gap-2.5 p-2.5 rounded-lg border cursor-pointer transition-colors {{ $importRombelScope === 'all_assigned' ? 'border-primary bg-primary/5 text-primary font-semibold' : 'border-border bg-surface text-text-main hover:bg-surface-hover' }}">
                                <input type="radio" wire:model.live="importRombelScope" value="all_assigned" class="mt-0.5 text-primary focus:ring-primary">
                                <div>
                                    <div class="font-medium">Semua Rombel yang Diampu</div>
                                    <div class="text-[10px] text-text-muted font-normal mt-0.5">Gabungan seluruh kelas mapel ini</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- 2. Checklist Kolom Asesmen -->
                    <!-- 2. Checklist Kolom Asesmen Sesuai TP -->
                    <div class="p-4 bg-surface-hover/40 border border-border rounded-xl">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                            <label class="text-xs font-bold uppercase tracking-wider text-text-secondary flex items-center gap-1.5">
                                <span>🎯 Pilih Kolom Nilai TP & Asesmen</span>
                            </label>
                            <!-- Preset Buttons -->
                            <div class="inline-flex flex-wrap gap-1 text-[11px]">
                                <button type="button" wire:click="setImportPreset('periode_aktif')" class="px-2 py-0.5 rounded bg-primary/10 text-primary font-semibold border border-primary/30 hover:bg-primary hover:text-white transition-colors">
                                    Periode Aktif ({{ $activePeriode }})
                                </button>
                                <button type="button" wire:click="setImportPreset('ganjil')" class="px-2 py-0.5 rounded bg-blue-50 text-blue-800 dark:bg-blue-950 dark:text-blue-200 border border-blue-200 dark:border-blue-800 hover:bg-blue-600 hover:text-white transition-colors">
                                    Ganjil Saja
                                </button>
                                <button type="button" wire:click="setImportPreset('genap')" class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-600 hover:text-white transition-colors">
                                    Genap Saja
                                </button>
                                <button type="button" wire:click="setImportPreset('semua')" class="px-2 py-0.5 rounded bg-surface border border-border hover:bg-primary hover:text-white transition-colors">
                                    Semua TP
                                </button>
                                <button type="button" wire:click="setImportPreset('reset')" class="px-2 py-0.5 rounded bg-surface border border-border hover:bg-rose-500 hover:text-white transition-colors">
                                    Kosongkan
                                </button>
                            </div>
                        </div>

                        @if($allTps && count($allTps) > 0)
                        <!-- Daftar TP Berdasarkan Periode 9M -->
                        <div class="space-y-3 text-xs">
                            @php
                                $periodConfig = [
                                    '9M1' => ['label' => 'Blok 9M1 (Semester Ganjil)', 'sumatif' => 'STS 1'],
                                    '9M2' => ['label' => 'Blok 9M2 (Semester Ganjil)', 'sumatif' => 'SAS'],
                                    '9M3' => ['label' => 'Blok 9M3 (Semester Genap)', 'sumatif' => 'STS 2'],
                                    '9M4' => ['label' => 'Blok 9M4 (Semester Genap)', 'sumatif' => 'SAT'],
                                ];
                            @endphp

                            @foreach($periodConfig as $pKey => $pCfg)
                                @php
                                    $pTps = $allTps->filter(function ($t) use ($pKey) {
                                        $tpP = $t->target_periode;
                                        if (empty($tpP)) {
                                            $tpP = match($t->alokasi_ah) {
                                                'AH 1', 'AH 2' => '9M1',
                                                'AH 3', 'AH 4' => '9M2',
                                                'AH 5', 'AH 6' => '9M3',
                                                'AH 7', 'AH 8' => '9M4',
                                                default => '9M1',
                                            };
                                        }
                                        return $tpP === $pKey;
                                    });
                                    $nhKey = "NH {$pKey}";
                                    $sumKey = $pCfg['sumatif'];
                                @endphp
                                <div class="p-2.5 rounded-lg border border-border/80 bg-surface">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[11px] font-bold text-text-main flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full {{ in_array($pKey, ['9M1', '9M2']) ? 'bg-blue-600' : 'bg-emerald-600' }}"></span>
                                            <span>{{ $pCfg['label'] }}</span>
                                        </span>
                                        <span class="text-[10px] text-text-muted font-mono">{{ $pTps->count() }} TP</span>
                                    </div>
                                    
                                    <div class="flex flex-wrap gap-1.5">
                                        <!-- TP Checkboxes -->
                                        @forelse($pTps as $tp)
                                        <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded border cursor-pointer font-mono text-xs transition-colors {{ in_array($tp->kode, $importSelectedColumns) ? 'bg-primary/10 border-primary text-primary font-bold' : 'bg-surface border-border text-text-muted hover:bg-surface-hover' }}"
                                               title="{{ $tp->kode }}: {{ $tp->deskripsi }}">
                                            <input type="checkbox" wire:model.live="importSelectedColumns" value="{{ $tp->kode }}" class="rounded text-primary focus:ring-primary w-3.5 h-3.5">
                                            <span>{{ $tp->kode }}</span>
                                        </label>
                                        @empty
                                        <span class="text-[11px] text-text-muted italic py-0.5">Belum ada TP untuk {{ $pKey }}</span>
                                        @endforelse

                                        <!-- NH Checkbox -->
                                        <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded border cursor-pointer font-mono text-xs transition-colors {{ in_array($nhKey, $importSelectedColumns) ? 'bg-emerald-50 border-emerald-400 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-200 font-bold' : 'bg-surface border-border text-text-muted hover:bg-surface-hover' }}"
                                               title="Nilai Harian (NH) periode {{ $pKey }} (Default: 100)">
                                            <input type="checkbox" wire:model.live="importSelectedColumns" value="{{ $nhKey }}" class="rounded text-emerald-600 focus:ring-emerald-500 w-3.5 h-3.5">
                                            <span>{{ $nhKey }}</span>
                                        </label>

                                        <!-- Sumatif Checkbox -->
                                        <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded border cursor-pointer font-mono text-xs transition-colors {{ in_array($sumKey, $importSelectedColumns) ? 'bg-purple-50 border-purple-400 text-purple-800 dark:bg-purple-950/60 dark:text-purple-200 font-bold' : 'bg-surface border-border text-text-muted hover:bg-surface-hover' }}"
                                               title="Sumatif: {{ $sumKey }}">
                                            <input type="checkbox" wire:model.live="importSelectedColumns" value="{{ $sumKey }}" class="rounded text-purple-600 focus:ring-purple-500 w-3.5 h-3.5">
                                            <span>{{ $sumKey }}</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @else
                        <!-- Fallback jika belum ada TP: Gunakan format AH legacy -->
                        <div class="p-3 mb-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 rounded-lg text-xs text-amber-900 dark:text-amber-200">
                            <div class="font-bold flex items-center gap-1 mb-0.5">
                                ⚠️ Belum Ada Tujuan Pembelajaran (TP)
                            </div>
                            <div>
                                Buat TP terlebih dahulu pada Tab 'Tujuan Pembelajaran' untuk penilaian per TP, atau gunakan kolom Asesmen Harian (AH 1-8) di bawah ini:
                            </div>
                        </div>

                        <!-- Legacy AH Checkboxes -->
                        <div class="space-y-2 text-xs">
                            <div>
                                <span class="text-[10px] font-bold text-blue-700 dark:text-blue-400 block mb-1">📘 Semester Ganjil:</span>
                                <div class="grid grid-cols-3 sm:grid-cols-6 gap-1.5">
                                    @foreach(['AH 1', 'AH 2', 'STS 1', 'AH 3', 'AH 4', 'SAS'] as $col)
                                    <label class="flex items-center gap-1.5 p-1.5 rounded border cursor-pointer text-center justify-center font-mono font-semibold transition-colors {{ in_array($col, $importSelectedColumns) ? 'bg-blue-50 border-blue-300 text-blue-900 dark:bg-blue-950/60 dark:border-blue-700 dark:text-blue-200' : 'bg-surface border-border text-text-muted hover:bg-surface-hover' }}">
                                        <input type="checkbox" wire:model.live="importSelectedColumns" value="{{ $col }}" class="rounded text-primary focus:ring-primary w-3.5 h-3.5">
                                        <span>{{ $col }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 block mb-1">📗 Semester Genap:</span>
                                <div class="grid grid-cols-3 sm:grid-cols-6 gap-1.5">
                                    @foreach(['AH 5', 'AH 6', 'STS 2', 'AH 7', 'AH 8', 'SAT'] as $col)
                                    <label class="flex items-center gap-1.5 p-1.5 rounded border cursor-pointer text-center justify-center font-mono font-semibold transition-colors {{ in_array($col, $importSelectedColumns) ? 'bg-emerald-50 border-emerald-300 text-emerald-900 dark:bg-emerald-950/60 dark:border-emerald-700 dark:text-emerald-200' : 'bg-surface border-border text-text-muted hover:bg-surface-hover' }}">
                                        <input type="checkbox" wire:model.live="importSelectedColumns" value="{{ $col }}" class="rounded text-emerald-600 focus:ring-emerald-500 w-3.5 h-3.5">
                                        <span>{{ $col }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        @error('importSelectedColumns')
                        <div class="mt-2 text-rose-500 text-xs font-semibold">{{ $message }}</div>
                        @enderror

                        <!-- Tombol Unduh Template -->
                        <div class="mt-3 pt-3 border-t border-border flex flex-col sm:flex-row items-center justify-between gap-2">
                            <span class="text-[11px] text-text-muted">Unduh template sesuai pilihan rombel dan kolom di atas:</span>
                            <button type="button" wire:click="downloadTemplate" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs inline-flex items-center gap-1.5 shadow-xs transition-colors shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>Unduh Template Excel</span>
                            </button>
                        </div>
                    </div>

                    <!-- 3. Upload Berkas Excel -->
                    <div class="p-4 bg-surface-hover/40 border border-border rounded-xl">
                        <label class="text-xs font-bold uppercase tracking-wider text-text-secondary flex items-center gap-1.5 mb-2">
                            <span>📤 Unggah Berkas Nilai (.xlsx / .xls)</span>
                        </label>
                        
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-border rounded-xl hover:border-primary/50 transition-colors bg-surface">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-10 w-10 text-text-muted" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-xs text-text-secondary justify-center items-center gap-1">
                                    <label for="import-file-input" class="relative cursor-pointer rounded-md font-bold text-primary hover:text-primary-hover focus-within:outline-none">
                                        <span>Pilih berkas Excel</span>
                                        <input id="import-file-input" type="file" wire:model="importFile" accept=".xlsx, .xls" class="sr-only">
                                    </label>
                                    <p class="pl-1">atau seret ke sini</p>
                                </div>
                                <p class="text-[10px] text-text-muted">Mendukung format .xlsx, .xls (Maks. 10 MB)</p>
                            </div>
                        </div>

                        <!-- Status Uploading / File Selected -->
                        <div class="mt-2">
                            <div wire:loading wire:target="importFile" class="text-xs text-primary font-semibold flex items-center gap-1.5">
                                <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span>Mengunggah berkas...</span>
                            </div>
                            @if($importFile && !$errors->has('importFile'))
                            <div wire:loading.remove wire:target="importFile" class="text-xs text-emerald-700 dark:text-emerald-400 font-medium flex items-center gap-1.5 mt-1">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Berkas terpilih: <strong>{{ $importFile->getClientOriginalName() }}</strong></span>
                            </div>
                            @endif
                            @error('importFile')
                            <div class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>

                <!-- Footer Langkah 1 -->
                <div class="px-6 py-4 bg-surface-hover/50 border-t border-border flex items-center justify-end gap-3">
                    <button type="button" wire:click="closeImportModal" class="btn btn-secondary px-4 py-2">Batal</button>
                    <button type="button" wire:click="uploadAndPreview" wire:loading.attr="disabled" class="btn btn-primary px-5 py-2 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span>Cek & Pratinjau Nilai</span>
                    </button>
                </div>
                @endif

                <!-- Modal Body: LANGKAH 2 (Preview Validasi & Konfirmasi) -->
                @if($importStep === 'preview' && $importPreviewData)
                <div class="p-6 space-y-4 text-sm max-h-[70vh] overflow-y-auto">
                    
                    <!-- Stats Cards Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-3 bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900 rounded-xl text-center">
                            <div class="text-2xl font-bold text-blue-900 dark:text-blue-100 font-mono">{{ $importPreviewData['total_rows'] }}</div>
                            <div class="text-xs text-blue-700 dark:text-blue-300">Total Baris Siswa</div>
                        </div>
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900 rounded-xl text-center">
                            <div class="text-2xl font-bold text-emerald-800 dark:text-emerald-200 font-mono">{{ $importPreviewData['valid_count'] }}</div>
                            <div class="text-xs text-emerald-700 dark:text-emerald-300">Siswa Siap Diimpor</div>
                        </div>
                        <div class="p-3 {{ $importPreviewData['error_count'] > 0 ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-900' : 'bg-surface-hover/50 border-border' }} border rounded-xl text-center">
                            <div class="text-2xl font-bold {{ $importPreviewData['error_count'] > 0 ? 'text-rose-700 dark:text-rose-300' : 'text-text-muted' }} font-mono">{{ $importPreviewData['error_count'] }}</div>
                            <div class="text-xs {{ $importPreviewData['error_count'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-text-muted' }}">Baris Dilewati (Error)</div>
                        </div>
                    </div>

                    <!-- Kolom Terdeteksi Badge -->
                    <div class="flex flex-wrap items-center gap-1.5 p-2.5 bg-surface-hover/50 rounded-lg text-xs">
                        <span class="font-bold text-text-secondary">Kolom Nilai Terdeteksi:</span>
                        @foreach($importPreviewData['detected_columns'] as $col)
                        <span class="px-2 py-0.5 rounded bg-primary/10 text-primary font-mono font-bold">{{ $col }}</span>
                        @endforeach
                    </div>

                    <!-- Alert Error / Warning jika ada -->
                    @if(!empty($importPreviewData['errors']))
                    <div class="p-3 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900 rounded-xl text-xs text-rose-800 dark:text-rose-300">
                        <div class="font-bold mb-1 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <span>Daftar Peringatan / Baris Dilewati:</span>
                        </div>
                        <ul class="list-disc pl-5 space-y-0.5 max-h-28 overflow-y-auto text-[11px]">
                            @foreach($importPreviewData['errors'] as $err)
                            <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(!empty($importPreviewData['warnings']))
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900 rounded-xl text-xs text-amber-800 dark:text-amber-300">
                        <div class="font-bold mb-1">Catatan Nilai Tertentu Dilewati:</div>
                        <ul class="list-disc pl-5 space-y-0.5 max-h-24 overflow-y-auto text-[11px]">
                            @foreach($importPreviewData['warnings'] as $warn)
                            <li>{{ $warn }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Pratinjau Tabel Mini Siswa -->
                    <div class="border border-border rounded-xl overflow-hidden">
                        <div class="px-3 py-2 bg-surface-hover/80 text-xs font-bold text-text-secondary border-b border-border flex justify-between items-center">
                            <span>Pratinjau Data Nilai (Menampilkan s.d 8 siswa pertama):</span>
                            <span class="text-[11px] font-normal text-text-muted">Total {{ count($importPreviewData['valid_rows']) }} data siap disimpan</span>
                        </div>
                        <div class="overflow-x-auto max-h-60">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-surface-hover text-text-secondary sticky top-0">
                                    <tr>
                                        <th class="p-2 border-b border-border">NIS</th>
                                        <th class="p-2 border-b border-border">Nama Siswa</th>
                                        <th class="p-2 border-b border-border">Rombel</th>
                                        @foreach($importPreviewData['detected_columns'] as $col)
                                        <th class="p-2 border-b border-border text-center font-mono">{{ $col }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    @foreach(array_slice($importPreviewData['valid_rows'], 0, 8) as $row)
                                    <tr class="hover:bg-surface-hover/40">
                                        <td class="p-2 font-mono text-text-muted">{{ $row['nis'] }}</td>
                                        <td class="p-2 font-semibold text-text-main whitespace-nowrap">{{ $row['nama'] }}</td>
                                        <td class="p-2">
                                            <span class="px-1.5 py-0.5 rounded bg-surface border border-border text-[10px]">{{ $row['rombel_nama'] }}</span>
                                        </td>
                                        @foreach($importPreviewData['detected_columns'] as $col)
                                        <td class="p-2 text-center font-mono font-bold {{ isset($row['scores'][$col]) && $row['scores'][$col] < $this->kkm ? 'text-rose-600 dark:text-rose-400' : 'text-text-main' }}">
                                            {{ $row['scores'][$col] ?? '-' }}
                                        </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- Footer Langkah 2 -->
                <div class="px-6 py-4 bg-surface-hover/50 border-t border-border flex items-center justify-between">
                    <button type="button" wire:click="cancelPreview" class="btn btn-secondary px-4 py-2 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        <span>Ganti Berkas / Kembali</span>
                    </button>

                    <button type="button" wire:click="executeImport" wire:loading.attr="disabled" class="btn btn-primary px-5 py-2 flex items-center gap-2">
                        <span wire:loading.remove wire:target="executeImport">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Simpan Nilai Sekarang ({{ $importPreviewData['valid_count'] }} Siswa)</span>
                        </span>
                        <span wire:loading wire:target="executeImport" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            <span>Menyimpan nilai ke sistem...</span>
                        </span>
                    </button>
                </div>
                @endif

            </div>
        </div>
    </div>
    @endif

    <!-- Modal Salin TP ke Rombel Lain -->
    @if($showCopyTpModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity animate-in fade-in duration-200">
        <div class="relative w-full max-w-2xl bg-surface border border-border rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-surface-hover/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-text-main">Salin Tujuan Pembelajaran ke Rombel Lain</h3>
                        <p class="text-xs text-text-secondary mt-0.5">
                            Sumber: <strong class="text-text-main">{{ $penugasan->rombel?->nama }}</strong> (Tingkat {{ $penugasan->rombel?->tingkat }}) &bull; Mapel: <strong class="text-text-main">{{ $penugasan->mapel?->nama }}</strong>
                        </p>
                    </div>
                </div>
                <button type="button" wire:click="closeCopyTpModal" class="text-text-muted hover:text-text-main p-1.5 rounded-lg hover:bg-surface-hover transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-5 text-sm overflow-y-auto">
                
                @if(session()->has('message_copy_tp_error'))
                <div class="p-3 bg-rose-50 border border-rose-200 text-rose-900 dark:bg-rose-950/40 dark:border-rose-800 dark:text-rose-200 rounded-xl text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('message_copy_tp_error') }}</span>
                </div>
                @endif

                <!-- 1. Cakupan Semester -->
                <div class="p-4 bg-surface-hover/40 border border-border rounded-xl">
                    <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-2">
                        1. Pilih Cakupan Semester TP yang Disalin
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition-all {{ $copyTpSemesterScope === 'all' ? 'bg-primary/10 border-primary text-primary font-bold' : 'bg-surface hover:bg-surface-hover/60 border-border text-text-secondary' }}">
                            <input type="radio" wire:model.live="copyTpSemesterScope" value="all" class="text-primary focus:ring-primary h-4 w-4 border-border">
                            <span class="text-xs">Seluruh TP (1 Tahun Penuh)</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition-all {{ $copyTpSemesterScope === 'ganjil' ? 'bg-primary/10 border-primary text-primary font-bold' : 'bg-surface hover:bg-surface-hover/60 border-border text-text-secondary' }}">
                            <input type="radio" wire:model.live="copyTpSemesterScope" value="ganjil" class="text-primary focus:ring-primary h-4 w-4 border-border">
                            <span class="text-xs">Semester Ganjil Saja</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition-all {{ $copyTpSemesterScope === 'genap' ? 'bg-primary/10 border-primary text-primary font-bold' : 'bg-surface hover:bg-surface-hover/60 border-border text-text-secondary' }}">
                            <input type="radio" wire:model.live="copyTpSemesterScope" value="genap" class="text-primary focus:ring-primary h-4 w-4 border-border">
                            <span class="text-xs">Semester Genap Saja</span>
                        </label>
                    </div>
                </div>

                <!-- 2. Pilih Rombel Tujuan -->
                <div class="p-4 bg-surface-hover/40 border border-border rounded-xl">
                    <div class="flex items-center justify-between gap-2 mb-2.5">
                        <label class="text-xs font-bold uppercase tracking-wider text-text-secondary">
                            2. Pilih Rombel Tujuan (Tingkat {{ $penugasan->rombel?->tingkat }})
                        </label>
                        <div class="flex items-center gap-2 text-xs">
                            <button type="button" wire:click="selectAllTargetRombels" class="text-primary hover:underline font-semibold text-[11px]">
                                Pilih Semua
                            </button>
                            <span class="text-text-muted">&bull;</span>
                            <button type="button" wire:click="deselectAllTargetRombels" class="text-text-secondary hover:underline text-[11px]">
                                Batal Pilih
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-56 overflow-y-auto p-1">
                        @forelse($availableTargetRombels as $targetRombel)
                        @php
                            $isSelected = in_array($targetRombel['id'], $selectedTargetRombelIds);
                        @endphp
                        <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all {{ $isSelected ? 'bg-primary/5 border-primary shadow-xs' : 'bg-surface hover:bg-surface-hover/60 border-border' }}">
                            <input type="checkbox" wire:model.live="selectedTargetRombelIds" value="{{ $targetRombel['id'] }}" class="rounded text-primary focus:ring-primary h-4 w-4 border-border">
                            <div class="flex-grow min-w-0">
                                <div class="flex items-center justify-between gap-1.5">
                                    <span class="font-bold text-xs text-text-main truncate">{{ $targetRombel['nama'] }}</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded font-medium bg-surface-hover text-text-secondary border border-border shrink-0">{{ $targetRombel['jurusan_singkatan'] }}</span>
                                </div>
                                <div class="text-[11px] text-text-muted mt-0.5 flex items-center gap-1.5">
                                    @if($targetRombel['existing_tp_count'] > 0)
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span class="text-emerald-700 dark:text-emerald-400 font-medium">{{ $targetRombel['existing_tp_count'] }} TP saat ini</span>
                                    @else
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-text-muted"></span>
                                        <span>Belum ada TP</span>
                                    @endif
                                </div>
                            </div>
                        </label>
                        @empty
                        <div class="col-span-2 text-center py-6 text-text-muted text-xs">
                            Tidak ditemukan rombel lain di tingkat {{ $penugasan->rombel?->tingkat }}.
                        </div>
                        @endforelse
                    </div>

                    <div class="mt-2 text-[11px] text-text-muted flex items-center justify-between">
                        <span>Terpilih: <strong class="text-primary font-bold">{{ count($selectedTargetRombelIds) }}</strong> dari {{ count($availableTargetRombels) }} rombel</span>
                        @if(count($selectedTargetRombelIds) > 0)
                        <span class="text-emerald-600 dark:text-emerald-400 font-medium">Siap disalin</span>
                        @endif
                    </div>
                </div>

                <!-- Petunjuk Mode Sinkronisasi Cerdas -->
                <div class="p-3 bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200/80 dark:border-blue-900 rounded-xl text-[11px] text-blue-900 dark:text-blue-300 flex items-start gap-2">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="space-y-0.5">
                        <strong class="font-bold">Mode Sinkronisasi Cerdas:</strong>
                        <p class="text-text-secondary leading-relaxed">
                            Kode TP yang sudah ada di rombel tujuan akan disinkronkan deskripsi dan alokasi AH-nya tanpa menghapus nilai formatif siswa yang sudah diinput. Setelah disalin, Anda tetap dapat mengedit, menambah, atau menghapus TP di masing-masing rombel secara mandiri.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-surface-hover/50 border-t border-border flex items-center justify-between">
                <button type="button" wire:click="closeCopyTpModal" class="btn btn-secondary px-4 py-2">
                    Batal
                </button>

                <button type="button" wire:click="executeCopyTp" wire:loading.attr="disabled" @disabled(count($selectedTargetRombelIds) === 0) class="btn btn-primary px-5 py-2 flex items-center gap-2 {{ count($selectedTargetRombelIds) === 0 ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <span wire:loading.remove wire:target="executeCopyTp" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        <span>Salin TP ke {{ count($selectedTargetRombelIds) }} Rombel</span>
                    </span>
                    <span wire:loading wire:target="executeCopyTp" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span>Menyalin TP...</span>
                    </span>
                </button>
            </div>

        </div>
    </div>
    @endif
</div>
