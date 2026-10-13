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
            <div class="text-xs font-bold uppercase tracking-wider text-text-secondary mb-3 flex items-center gap-2">
                <span>{{ $edit_tp_id ? '✏️ Formulir Edit TP / CP' : '➕ Formulir Tambah TP / CP & Alokasi AH' }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Semester Target</label>
                    <select wire:model.live="tp_semester_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main py-2 px-3 focus:border-primary focus:ring-primary font-medium">
                        @foreach($semesters as $s)
                        <option value="{{ $s['id'] }}">{{ $s['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('tp_semester_id') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Alokasi AH</label>
                    <select wire:model="tp_alokasi_ah" class="w-full text-sm rounded-lg border-border bg-surface text-text-main py-2 px-3 focus:border-primary focus:ring-primary font-medium" required>
                        <option value="">-- Pilih AH --</option>
                        @foreach($this->availableAhOptions as $ahOpt)
                        <option value="{{ $ahOpt }}">{{ $ahOpt }}</option>
                        @endforeach
                    </select>
                    @error('tp_alokasi_ah') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Kode TP / CP</label>
                    <input type="text" wire:model="tp_kode" class="w-full text-sm rounded-lg border-border bg-surface text-text-main py-2 px-3 focus:border-primary focus:ring-primary font-mono uppercase font-bold" required placeholder="Misal: {{ $this->suggestedNextKode }}">
                    @error('tp_kode') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Deskripsi Capaian Kompetensi</label>
                    <input type="text" wire:model="tp_deskripsi" class="w-full text-sm rounded-lg border-border bg-surface text-text-main py-2 px-3 focus:border-primary focus:ring-primary" required placeholder="Contoh: Memahami dasar logika algoritma dan tata cara...">
                    @error('tp_deskripsi') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="btn {{ $edit_tp_id ? 'btn-yellow' : 'btn-primary' }} flex-grow py-2">
                        {{ $edit_tp_id ? 'Update' : 'Tambah' }}
                    </button>
                    @if($edit_tp_id)
                    <button type="button" wire:click="cancelEditTP" class="btn btn-secondary py-2">Batal</button>
                    @endif
                </div>
            </div>
        </form>

        <!-- Tabel Gabungan Seluruh TP (Ganjil & Genap) -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border border-border rounded-lg overflow-hidden">
                <thead class="bg-surface-hover text-xs uppercase tracking-wider">
                    <tr>
                        <th class="p-3 w-12 text-center border-b border-border text-text-main">No</th>
                        <th class="p-3 w-32 border-b border-border text-text-main">Semester</th>
                        <th class="p-3 w-28 text-center border-b border-border text-text-main">Alokasi AH</th>
                        <th class="p-3 w-24 border-b border-border text-text-main">Kode</th>
                        <th class="p-3 border-b border-border text-text-main">Deskripsi Capaian Pembelajaran</th>
                        <th class="p-3 w-32 text-center border-b border-border text-text-main">Status Formatif</th>
                        <th class="p-3 w-24 text-center border-b border-border text-text-main">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($allTps as $idx => $tp)
                    @php
                        $sem = strtolower($tp->penugasanMengajar?->semester?->semester ?? '');
                        $isGenap = str_contains($sem, 'genap');
                    @endphp
                    <tr class="hover:bg-surface-hover/50 transition-colors {{ $edit_tp_id === $tp->id ? 'bg-amber-50/70 dark:bg-amber-950/30' : '' }}">
                        <td class="p-3 text-center text-text-secondary text-xs">{{ $idx + 1 }}</td>
                        <td class="p-3">
                            @if($isGenap)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                Semester Genap
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                Semester Ganjil
                            </span>
                            @endif
                        </td>
                        <td class="p-3 text-center">
                            @if($tp->alokasi_ah)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-indigo-100 text-indigo-900 dark:bg-indigo-950/70 dark:text-indigo-200 border border-indigo-200 dark:border-indigo-800">
                                {{ $tp->alokasi_ah }}
                            </span>
                            @else
                            <span class="text-xs text-rose-600 dark:text-rose-400 font-medium italic">Belum diatur</span>
                            @endif
                        </td>
                        <td class="p-3 font-bold font-mono text-text-main">{{ $tp->kode }}</td>
                        <td class="p-3 text-text-main">{{ $tp->deskripsi }}</td>
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

    <!-- TAB 2: INPUT NILAI AKADEMIK (1 TAHUN PENUH DENGAN KOLOM NAMA FREEZE) -->
    @if($activeTab === 'formatif')
    <div class="card mb-6">
        <!-- Filter Mode Tampilan & Tombol Simpan -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-4 p-3 bg-surface-hover/70 rounded-xl border border-border">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-text-secondary">Tampilan Tabel:</span>
                <div class="inline-flex rounded-lg border border-border p-1 bg-surface shadow-xs">
                    <button type="button"
                        wire:click="setTabelMode('semua')"
                        class="px-3.5 py-1.5 rounded-md text-xs font-semibold transition-all flex items-center gap-1.5 {{ $tabelTahunMode === 'semua' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                        <span>🗓️ 1 Tahun Penuh (Sepanjang Tahun)</span>
                    </button>
                    <button type="button"
                        wire:click="setTabelMode('ganjil')"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all flex items-center gap-1.5 {{ $tabelTahunMode === 'ganjil' ? 'bg-blue-600 text-white shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                        <span>📘 Semester Ganjil</span>
                    </button>
                    <button type="button"
                        wire:click="setTabelMode('genap')"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all flex items-center gap-1.5 {{ $tabelTahunMode === 'genap' ? 'bg-emerald-600 text-white shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                        <span>📗 Semester Genap</span>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" wire:click="openImportModal" class="btn btn-secondary flex items-center justify-center gap-2 shadow-xs">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    <span>Import Nilai Excel</span>
                </button>

                <button wire:click="saveNilai" class="btn btn-primary flex items-center justify-center gap-2">
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

        <div class="mb-3 px-3 py-2 bg-blue-50/60 dark:bg-blue-950/30 border border-blue-200/80 dark:border-blue-900/50 rounded-lg text-xs text-text-main flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="font-bold text-primary">📌 Buku Nilai 1 Tahun:</span>
                <span>Kolom nama siswa terkunci (freeze). Anda dapat menginput nilai sepanjang tahun secara kronologis.</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/70 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-300 font-bold text-[11px]">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                    <span>KKM: {{ $this->kkm }} &bull; Nilai &lt; {{ $this->kkm }} disorot merah (Belum Kompeten)</span>
                </span>
            </div>
        </div>

        @php
            $mapping = $this->ahTpsMapping;
            $showGanjil = ($tabelTahunMode === 'semua' || $tabelTahunMode === 'ganjil');
            $showGenap = ($tabelTahunMode === 'semua' || $tabelTahunMode === 'genap');

            $tps1 = $mapping['AH 1'] ?? collect();
            $tps2 = $mapping['AH 2'] ?? collect();
            $tps3 = $mapping['AH 3'] ?? collect();
            $tps4 = $mapping['AH 4'] ?? collect();
            $tps5 = $mapping['AH 5'] ?? collect();
            $tps6 = $mapping['AH 6'] ?? collect();
            $tps7 = $mapping['AH 7'] ?? collect();
            $tps8 = $mapping['AH 8'] ?? collect();
        @endphp

        <div class="overflow-x-auto relative max-h-[750px] border border-border rounded-xl shadow-xs">
            <table class="w-full text-sm text-left border-collapse table-nilai">
                <thead class="bg-surface-hover sticky top-0 z-20">
                    <tr>
                        <th rowspan="2" class="p-2.5 sm:p-3 border-b-2 border-r-2 border-border sticky left-0 bg-surface dark:bg-surface-card z-30 min-w-[190px] sm:min-w-[210px] text-text-main font-bold shadow-[4px_0_8px_-2px_rgba(0,0,0,0.15)]">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span>Nama Siswa</span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-100 text-blue-900 dark:bg-blue-950 dark:text-blue-200 font-semibold ml-auto">Freeze</span>
                            </div>
                        </th>

                        @if($showGanjil)
                        <th colspan="6" class="p-2 border-b {{ $showGenap ? 'border-r-4 border-slate-300 dark:border-slate-600' : 'border-r-2 border-border' }} text-center bg-blue-100 text-blue-950 dark:bg-blue-950 dark:text-blue-200 font-black tracking-wider uppercase text-xs shadow-2xs">
                            📘 Semester Ganjil (STS 1 & SAS)
                        </th>
                        @endif

                        @if($showGenap)
                        <th colspan="6" class="p-2 border-b border-border text-center bg-emerald-100 text-emerald-950 dark:bg-emerald-950 dark:text-emerald-200 font-black tracking-wider uppercase text-xs shadow-2xs">
                            📗 Semester Genap (STS 2 & SAT)
                        </th>
                        @endif
                    </tr>
                    <tr>
                        @if($showGanjil)
                        <!-- AH 1 -->
                        <th class="p-1.5 sm:p-2 border-b border-r border-border min-w-[76px] sm:min-w-[82px] text-center bg-blue-50/40 dark:bg-blue-900/10">
                            <div class="font-mono font-bold text-text-main text-xs sm:text-sm">AH 1</div>
                            @if($tps1->isNotEmpty())
                            <div class="flex flex-wrap gap-1 justify-center mt-0.5">
                                @foreach($tps1 as $tp)
                                <span class="text-[9px] px-1 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-200 font-medium" title="{{ $tp->deskripsi }}">{{ $tp->kode }}</span>
                                @endforeach
                            </div>
                            @else
                            <div class="text-[9px] text-rose-500 dark:text-rose-400 font-normal mt-0.5 italic">Belum ada TP</div>
                            @endif
                        </th>

                        <!-- AH 2 -->
                        <th class="p-1.5 sm:p-2 border-b border-r border-border min-w-[76px] sm:min-w-[82px] text-center bg-blue-50/40 dark:bg-blue-900/10">
                            <div class="font-mono font-bold text-text-main text-xs sm:text-sm">AH 2</div>
                            @if($tps2->isNotEmpty())
                            <div class="flex flex-wrap gap-1 justify-center mt-0.5">
                                @foreach($tps2 as $tp)
                                <span class="text-[9px] px-1 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-200 font-medium" title="{{ $tp->deskripsi }}">{{ $tp->kode }}</span>
                                @endforeach
                            </div>
                            @else
                            <div class="text-[9px] text-rose-500 dark:text-rose-400 font-normal mt-0.5 italic">Belum ada TP</div>
                            @endif
                        </th>

                        <!-- STS 1 -->
                        <th class="p-1.5 sm:p-2 border-b border-r border-border min-w-[68px] sm:min-w-[74px] text-center bg-blue-100/70 dark:bg-blue-950/80" title="Sumatif Tengah Semester 1">
                            <div class="font-mono font-bold text-blue-900 dark:text-blue-200 text-xs sm:text-sm">STS 1</div>
                            <div class="text-[9px] text-blue-700 dark:text-blue-300 font-normal leading-tight">Tengah</div>
                        </th>

                        <!-- AH 3 -->
                        <th class="p-1.5 sm:p-2 border-b border-r border-border min-w-[76px] sm:min-w-[82px] text-center bg-blue-50/40 dark:bg-blue-900/10">
                            <div class="font-mono font-bold text-text-main text-xs sm:text-sm">AH 3</div>
                            @if($tps3->isNotEmpty())
                            <div class="flex flex-wrap gap-1 justify-center mt-0.5">
                                @foreach($tps3 as $tp)
                                <span class="text-[9px] px-1 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-200 font-medium" title="{{ $tp->deskripsi }}">{{ $tp->kode }}</span>
                                @endforeach
                            </div>
                            @else
                            <div class="text-[9px] text-rose-500 dark:text-rose-400 font-normal mt-0.5 italic">Belum ada TP</div>
                            @endif
                        </th>

                        <!-- AH 4 -->
                        <th class="p-1.5 sm:p-2 border-b border-r border-border min-w-[76px] sm:min-w-[82px] text-center bg-blue-50/40 dark:bg-blue-900/10">
                            <div class="font-mono font-bold text-text-main text-xs sm:text-sm">AH 4</div>
                            @if($tps4->isNotEmpty())
                            <div class="flex flex-wrap gap-1 justify-center mt-0.5">
                                @foreach($tps4 as $tp)
                                <span class="text-[9px] px-1 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-200 font-medium" title="{{ $tp->deskripsi }}">{{ $tp->kode }}</span>
                                @endforeach
                            </div>
                            @else
                            <div class="text-[9px] text-rose-500 dark:text-rose-400 font-normal mt-0.5 italic">Belum ada TP</div>
                            @endif
                        </th>

                        <!-- SAS -->
                        <th class="p-1.5 sm:p-2 border-b {{ $showGenap ? 'border-r-4 border-slate-300 dark:border-slate-600' : 'border-r-2 border-border' }} text-center min-w-[68px] sm:min-w-[74px] bg-blue-100/70 dark:bg-blue-950/80" title="Sumatif Akhir Semester">
                            <div class="font-mono font-bold text-blue-900 dark:text-blue-200 text-xs sm:text-sm">SAS</div>
                            <div class="text-[9px] text-blue-700 dark:text-blue-300 font-normal leading-tight">Akhir</div>
                        </th>
                        @endif

                        @if($showGenap)
                        <!-- AH 5 -->
                        <th class="p-1.5 sm:p-2 border-b border-r border-border min-w-[76px] sm:min-w-[82px] text-center bg-emerald-50/40 dark:bg-emerald-900/10">
                            <div class="font-mono font-bold text-text-main text-xs sm:text-sm">AH 5</div>
                            @if($tps5->isNotEmpty())
                            <div class="flex flex-wrap gap-1 justify-center mt-0.5">
                                @foreach($tps5 as $tp)
                                <span class="text-[9px] px-1 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200 font-medium" title="{{ $tp->deskripsi }}">{{ $tp->kode }}</span>
                                @endforeach
                            </div>
                            @else
                            <div class="text-[9px] text-rose-500 dark:text-rose-400 font-normal mt-0.5 italic">Belum ada TP</div>
                            @endif
                        </th>

                        <!-- AH 6 -->
                        <th class="p-1.5 sm:p-2 border-b border-r border-border min-w-[76px] sm:min-w-[82px] text-center bg-emerald-50/40 dark:bg-emerald-900/10">
                            <div class="font-mono font-bold text-text-main text-xs sm:text-sm">AH 6</div>
                            @if($tps6->isNotEmpty())
                            <div class="flex flex-wrap gap-1 justify-center mt-0.5">
                                @foreach($tps6 as $tp)
                                <span class="text-[9px] px-1 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200 font-medium" title="{{ $tp->deskripsi }}">{{ $tp->kode }}</span>
                                @endforeach
                            </div>
                            @else
                            <div class="text-[9px] text-rose-500 dark:text-rose-400 font-normal mt-0.5 italic">Belum ada TP</div>
                            @endif
                        </th>

                        <!-- STS 2 -->
                        <th class="p-1.5 sm:p-2 border-b border-r border-border min-w-[68px] sm:min-w-[74px] text-center bg-emerald-100/70 dark:bg-emerald-950/80" title="Sumatif Tengah Semester 2">
                            <div class="font-mono font-bold text-emerald-900 dark:text-emerald-200 text-xs sm:text-sm">STS 2</div>
                            <div class="text-[9px] text-emerald-700 dark:text-emerald-300 font-normal leading-tight">Tengah</div>
                        </th>

                        <!-- AH 7 -->
                        <th class="p-1.5 sm:p-2 border-b border-r border-border min-w-[76px] sm:min-w-[82px] text-center bg-emerald-50/40 dark:bg-emerald-900/10">
                            <div class="font-mono font-bold text-text-main text-xs sm:text-sm">AH 7</div>
                            @if($tps7->isNotEmpty())
                            <div class="flex flex-wrap gap-1 justify-center mt-0.5">
                                @foreach($tps7 as $tp)
                                <span class="text-[9px] px-1 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200 font-medium" title="{{ $tp->deskripsi }}">{{ $tp->kode }}</span>
                                @endforeach
                            </div>
                            @else
                            <div class="text-[9px] text-rose-500 dark:text-rose-400 font-normal mt-0.5 italic">Belum ada TP</div>
                            @endif
                        </th>

                        <!-- AH 8 -->
                        <th class="p-1.5 sm:p-2 border-b border-r border-border min-w-[76px] sm:min-w-[82px] text-center bg-emerald-50/40 dark:bg-emerald-900/10">
                            <div class="font-mono font-bold text-text-main text-xs sm:text-sm">AH 8</div>
                            @if($tps8->isNotEmpty())
                            <div class="flex flex-wrap gap-1 justify-center mt-0.5">
                                @foreach($tps8 as $tp)
                                <span class="text-[9px] px-1 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200 font-medium" title="{{ $tp->deskripsi }}">{{ $tp->kode }}</span>
                                @endforeach
                            </div>
                            @else
                            <div class="text-[9px] text-rose-500 dark:text-rose-400 font-normal mt-0.5 italic">Belum ada TP</div>
                            @endif
                        </th>

                        <!-- SAT -->
                        <th class="p-1.5 sm:p-2 border-b border-border min-w-[68px] sm:min-w-[74px] text-center bg-emerald-100/70 dark:bg-emerald-950/80" title="Sumatif Akhir Tahun">
                            <div class="font-mono font-bold text-emerald-900 dark:text-emerald-200 text-xs sm:text-sm">SAT</div>
                            <div class="text-[9px] text-emerald-700 dark:text-emerald-300 font-normal leading-tight">Akhir</div>
                        </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($siswas as $siswa)
                    @php
                        // Ganjil
                        $vAh1 = $ahData[$siswa->id]['AH 1'] ?? null;
                        $isAh1Below = $this->isBelowKkm($vAh1);

                        $vAh2 = $ahData[$siswa->id]['AH 2'] ?? null;
                        $isAh2Below = $this->isBelowKkm($vAh2);

                        $vSts1 = $sumatifDataGanjil[$siswa->id]['sts'] ?? null;
                        $isSts1Below = $this->isBelowKkm($vSts1);

                        $vAh3 = $ahData[$siswa->id]['AH 3'] ?? null;
                        $isAh3Below = $this->isBelowKkm($vAh3);

                        $vAh4 = $ahData[$siswa->id]['AH 4'] ?? null;
                        $isAh4Below = $this->isBelowKkm($vAh4);

                        $vSas = $sumatifDataGanjil[$siswa->id]['sas'] ?? null;
                        $isSasBelow = $this->isBelowKkm($vSas);

                        // Genap
                        $vAh5 = $ahData[$siswa->id]['AH 5'] ?? null;
                        $isAh5Below = $this->isBelowKkm($vAh5);

                        $vAh6 = $ahData[$siswa->id]['AH 6'] ?? null;
                        $isAh6Below = $this->isBelowKkm($vAh6);

                        $vSts2 = $sumatifDataGenap[$siswa->id]['sts'] ?? null;
                        $isSts2Below = $this->isBelowKkm($vSts2);

                        $vAh7 = $ahData[$siswa->id]['AH 7'] ?? null;
                        $isAh7Below = $this->isBelowKkm($vAh7);

                        $vAh8 = $ahData[$siswa->id]['AH 8'] ?? null;
                        $isAh8Below = $this->isBelowKkm($vAh8);

                        $vSat = $sumatifDataGenap[$siswa->id]['sas'] ?? null;
                        $isSatBelow = $this->isBelowKkm($vSat);

                        $sasBorder = $showGenap ? 'border-r-4 border-slate-300 dark:border-slate-600' : 'border-r-2 border-border';
                    @endphp
                    <tr wire:key="siswa-row-{{ $siswa->id }}" class="hover:bg-surface-hover/60 transition-colors">
                        <!-- Frozen Name Column -->
                        <td class="p-3 border-r-2 border-border sticky left-0 bg-surface dark:bg-surface-card font-semibold text-text-main z-10 whitespace-nowrap shadow-[4px_0_8px_-2px_rgba(0,0,0,0.15)]">
                            {{ $siswa->nama }}
                        </td>

                        @if($showGanjil)
                        <!-- AH 1 -->
                        <td class="p-1.5 sm:p-2 border-r border-border text-center transition-colors {{ $tps1->isEmpty() ? 'bg-surface-hover/40' : ($isAh1Below ? 'bg-rose-100/70 dark:bg-rose-950/50' : '') }}">
                            @if($tps1->isEmpty())
                                <input type="text" disabled value="-" title="Belum dialokasikan TP pada AH 1" class="w-14 sm:w-16 text-center border border-border/40 rounded-lg px-1 py-1 bg-surface-hover/60 text-text-muted/60 cursor-not-allowed text-xs font-mono font-bold">
                            @else
                                <input type="number" min="0" max="100" wire:model.blur="ahData.{{ $siswa->id }}.AH 1" placeholder="AH 1"
                                    @if($isAh1Below) title="Nilai {{ $vAh1 }} di bawah KKM ({{ $this->kkm }}) - Belum Kompeten" @endif
                                    class="w-14 sm:w-16 text-center rounded-lg px-1 py-1 font-mono transition-colors focus:outline-none no-spinner [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none {{ $isAh1Below ? 'border-2 border-rose-500 bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 font-bold focus:border-rose-600 focus:ring-1 focus:ring-rose-600' : 'border border-border bg-surface text-text-main font-semibold placeholder:text-text-muted/50 focus:border-primary focus:ring-1 focus:ring-primary' }}">
                            @endif
                        </td>

                        <!-- AH 2 -->
                        <td class="p-1.5 sm:p-2 border-r border-border text-center transition-colors {{ $tps2->isEmpty() ? 'bg-surface-hover/40' : ($isAh2Below ? 'bg-rose-100/70 dark:bg-rose-950/50' : '') }}">
                            @if($tps2->isEmpty())
                                <input type="text" disabled value="-" title="Belum dialokasikan TP pada AH 2" class="w-14 sm:w-16 text-center border border-border/40 rounded-lg px-1 py-1 bg-surface-hover/60 text-text-muted/60 cursor-not-allowed text-xs font-mono font-bold">
                            @else
                                <input type="number" min="0" max="100" wire:model.blur="ahData.{{ $siswa->id }}.AH 2" placeholder="AH 2"
                                    @if($isAh2Below) title="Nilai {{ $vAh2 }} di bawah KKM ({{ $this->kkm }}) - Belum Kompeten" @endif
                                    class="w-14 sm:w-16 text-center rounded-lg px-1 py-1 font-mono transition-colors focus:outline-none no-spinner [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none {{ $isAh2Below ? 'border-2 border-rose-500 bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 font-bold focus:border-rose-600 focus:ring-1 focus:ring-rose-600' : 'border border-border bg-surface text-text-main font-semibold placeholder:text-text-muted/50 focus:border-primary focus:ring-1 focus:ring-primary' }}">
                            @endif
                        </td>

                        <!-- STS 1 -->
                        <td class="p-1.5 sm:p-2 border-r border-border text-center transition-colors {{ $isSts1Below ? 'bg-rose-100/70 dark:bg-rose-950/50' : 'bg-blue-50/40 dark:bg-blue-950/20' }}">
                            <input type="number" min="0" max="100" wire:model.blur="sumatifDataGanjil.{{ $siswa->id }}.sts" placeholder="STS 1"
                                @if($isSts1Below) title="Nilai {{ $vSts1 }} di bawah KKM ({{ $this->kkm }}) - Belum Kompeten" @endif
                                class="w-14 sm:w-16 text-center rounded-lg px-1 py-1 font-mono transition-colors focus:outline-none no-spinner [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none {{ $isSts1Below ? 'border-2 border-rose-500 bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 font-bold focus:border-rose-600 focus:ring-1 focus:ring-rose-600' : 'border border-blue-300 dark:border-blue-700 bg-surface text-blue-900 dark:text-blue-100 font-bold placeholder:text-blue-400/70 focus:border-primary focus:ring-1 focus:ring-primary' }}">
                        </td>

                        <!-- AH 3 -->
                        <td class="p-1.5 sm:p-2 border-r border-border text-center transition-colors {{ $tps3->isEmpty() ? 'bg-surface-hover/40' : ($isAh3Below ? 'bg-rose-100/70 dark:bg-rose-950/50' : '') }}">
                            @if($tps3->isEmpty())
                                <input type="text" disabled value="-" title="Belum dialokasikan TP pada AH 3" class="w-14 sm:w-16 text-center border border-border/40 rounded-lg px-1 py-1 bg-surface-hover/60 text-text-muted/60 cursor-not-allowed text-xs font-mono font-bold">
                            @else
                                <input type="number" min="0" max="100" wire:model.blur="ahData.{{ $siswa->id }}.AH 3" placeholder="AH 3"
                                    @if($isAh3Below) title="Nilai {{ $vAh3 }} di bawah KKM ({{ $this->kkm }}) - Belum Kompeten" @endif
                                    class="w-14 sm:w-16 text-center rounded-lg px-1 py-1 font-mono transition-colors focus:outline-none no-spinner [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none {{ $isAh3Below ? 'border-2 border-rose-500 bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 font-bold focus:border-rose-600 focus:ring-1 focus:ring-rose-600' : 'border border-border bg-surface text-text-main font-semibold placeholder:text-text-muted/50 focus:border-primary focus:ring-1 focus:ring-primary' }}">
                            @endif
                        </td>

                        <!-- AH 4 -->
                        <td class="p-1.5 sm:p-2 border-r border-border text-center transition-colors {{ $tps4->isEmpty() ? 'bg-surface-hover/40' : ($isAh4Below ? 'bg-rose-100/70 dark:bg-rose-950/50' : '') }}">
                            @if($tps4->isEmpty())
                                <input type="text" disabled value="-" title="Belum dialokasikan TP pada AH 4" class="w-14 sm:w-16 text-center border border-border/40 rounded-lg px-1 py-1 bg-surface-hover/60 text-text-muted/60 cursor-not-allowed text-xs font-mono font-bold">
                            @else
                                <input type="number" min="0" max="100" wire:model.blur="ahData.{{ $siswa->id }}.AH 4" placeholder="AH 4"
                                    @if($isAh4Below) title="Nilai {{ $vAh4 }} di bawah KKM ({{ $this->kkm }}) - Belum Kompeten" @endif
                                    class="w-14 sm:w-16 text-center rounded-lg px-1 py-1 font-mono transition-colors focus:outline-none no-spinner [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none {{ $isAh4Below ? 'border-2 border-rose-500 bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 font-bold focus:border-rose-600 focus:ring-1 focus:ring-rose-600' : 'border border-border bg-surface text-text-main font-semibold placeholder:text-text-muted/50 focus:border-primary focus:ring-1 focus:ring-primary' }}">
                            @endif
                        </td>

                        <!-- SAS -->
                        <td class="p-1.5 sm:p-2 {{ $sasBorder }} text-center transition-colors {{ $isSasBelow ? 'bg-rose-100/70 dark:bg-rose-950/50' : 'bg-blue-50/40 dark:bg-blue-950/20' }}">
                            <input type="number" min="0" max="100" wire:model.blur="sumatifDataGanjil.{{ $siswa->id }}.sas" placeholder="SAS"
                                @if($isSasBelow) title="Nilai {{ $vSas }} di bawah KKM ({{ $this->kkm }}) - Belum Kompeten" @endif
                                class="w-14 sm:w-16 text-center rounded-lg px-1 py-1 font-mono transition-colors focus:outline-none no-spinner [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none {{ $isSasBelow ? 'border-2 border-rose-500 bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 font-bold focus:border-rose-600 focus:ring-1 focus:ring-rose-600' : 'border border-blue-300 dark:border-blue-700 bg-surface text-blue-900 dark:text-blue-100 font-bold placeholder:text-blue-400/70 focus:border-primary focus:ring-1 focus:ring-primary' }}">
                        </td>
                        @endif

                        @if($showGenap)
                        <!-- AH 5 -->
                        <td class="p-1.5 sm:p-2 border-r border-border text-center transition-colors {{ $tps5->isEmpty() ? 'bg-surface-hover/40' : ($isAh5Below ? 'bg-rose-100/70 dark:bg-rose-950/50' : '') }}">
                            @if($tps5->isEmpty())
                                <input type="text" disabled value="-" title="Belum dialokasikan TP pada AH 5" class="w-14 sm:w-16 text-center border border-border/40 rounded-lg px-1 py-1 bg-surface-hover/60 text-text-muted/60 cursor-not-allowed text-xs font-mono font-bold">
                            @else
                                <input type="number" min="0" max="100" wire:model.blur="ahData.{{ $siswa->id }}.AH 5" placeholder="AH 5"
                                    @if($isAh5Below) title="Nilai {{ $vAh5 }} di bawah KKM ({{ $this->kkm }}) - Belum Kompeten" @endif
                                    class="w-14 sm:w-16 text-center rounded-lg px-1 py-1 font-mono transition-colors focus:outline-none no-spinner [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none {{ $isAh5Below ? 'border-2 border-rose-500 bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 font-bold focus:border-rose-600 focus:ring-1 focus:ring-rose-600' : 'border border-border bg-surface text-text-main font-semibold placeholder:text-text-muted/50 focus:border-primary focus:ring-1 focus:ring-primary' }}">
                            @endif
                        </td>

                        <!-- AH 6 -->
                        <td class="p-1.5 sm:p-2 border-r border-border text-center transition-colors {{ $tps6->isEmpty() ? 'bg-surface-hover/40' : ($isAh6Below ? 'bg-rose-100/70 dark:bg-rose-950/50' : '') }}">
                            @if($tps6->isEmpty())
                                <input type="text" disabled value="-" title="Belum dialokasikan TP pada AH 6" class="w-14 sm:w-16 text-center border border-border/40 rounded-lg px-1 py-1 bg-surface-hover/60 text-text-muted/60 cursor-not-allowed text-xs font-mono font-bold">
                            @else
                                <input type="number" min="0" max="100" wire:model.blur="ahData.{{ $siswa->id }}.AH 6" placeholder="AH 6"
                                    @if($isAh6Below) title="Nilai {{ $vAh6 }} di bawah KKM ({{ $this->kkm }}) - Belum Kompeten" @endif
                                    class="w-14 sm:w-16 text-center rounded-lg px-1 py-1 font-mono transition-colors focus:outline-none no-spinner [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none {{ $isAh6Below ? 'border-2 border-rose-500 bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 font-bold focus:border-rose-600 focus:ring-1 focus:ring-rose-600' : 'border border-border bg-surface text-text-main font-semibold placeholder:text-text-muted/50 focus:border-primary focus:ring-1 focus:ring-primary' }}">
                            @endif
                        </td>

                        <!-- STS 2 -->
                        <td class="p-1.5 sm:p-2 border-r border-border text-center transition-colors {{ $isSts2Below ? 'bg-rose-100/70 dark:bg-rose-950/50' : 'bg-emerald-50/40 dark:bg-emerald-950/20' }}">
                            <input type="number" min="0" max="100" wire:model.blur="sumatifDataGenap.{{ $siswa->id }}.sts" placeholder="STS 2"
                                @if($isSts2Below) title="Nilai {{ $vSts2 }} di bawah KKM ({{ $this->kkm }}) - Belum Kompeten" @endif
                                class="w-14 sm:w-16 text-center rounded-lg px-1 py-1 font-mono transition-colors focus:outline-none no-spinner [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none {{ $isSts2Below ? 'border-2 border-rose-500 bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 font-bold focus:border-rose-600 focus:ring-1 focus:ring-rose-600' : 'border border-emerald-300 dark:border-emerald-700 bg-surface text-emerald-900 dark:text-emerald-100 font-bold placeholder:text-emerald-400/70 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500' }}">
                        </td>

                        <!-- AH 7 -->
                        <td class="p-1.5 sm:p-2 border-r border-border text-center transition-colors {{ $tps7->isEmpty() ? 'bg-surface-hover/40' : ($isAh7Below ? 'bg-rose-100/70 dark:bg-rose-950/50' : '') }}">
                            @if($tps7->isEmpty())
                                <input type="text" disabled value="-" title="Belum dialokasikan TP pada AH 7" class="w-14 sm:w-16 text-center border border-border/40 rounded-lg px-1 py-1 bg-surface-hover/60 text-text-muted/60 cursor-not-allowed text-xs font-mono font-bold">
                            @else
                                <input type="number" min="0" max="100" wire:model.blur="ahData.{{ $siswa->id }}.AH 7" placeholder="AH 7"
                                    @if($isAh7Below) title="Nilai {{ $vAh7 }} di bawah KKM ({{ $this->kkm }}) - Belum Kompeten" @endif
                                    class="w-14 sm:w-16 text-center rounded-lg px-1 py-1 font-mono transition-colors focus:outline-none no-spinner [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none {{ $isAh7Below ? 'border-2 border-rose-500 bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 font-bold focus:border-rose-600 focus:ring-1 focus:ring-rose-600' : 'border border-border bg-surface text-text-main font-semibold placeholder:text-text-muted/50 focus:border-primary focus:ring-1 focus:ring-primary' }}">
                            @endif
                        </td>

                        <!-- AH 8 -->
                        <td class="p-1.5 sm:p-2 border-r border-border text-center transition-colors {{ $tps8->isEmpty() ? 'bg-surface-hover/40' : ($isAh8Below ? 'bg-rose-100/70 dark:bg-rose-950/50' : '') }}">
                            @if($tps8->isEmpty())
                                <input type="text" disabled value="-" title="Belum dialokasikan TP pada AH 8" class="w-14 sm:w-16 text-center border border-border/40 rounded-lg px-1 py-1 bg-surface-hover/60 text-text-muted/60 cursor-not-allowed text-xs font-mono font-bold">
                            @else
                                <input type="number" min="0" max="100" wire:model.blur="ahData.{{ $siswa->id }}.AH 8" placeholder="AH 8"
                                    @if($isAh8Below) title="Nilai {{ $vAh8 }} di bawah KKM ({{ $this->kkm }}) - Belum Kompeten" @endif
                                    class="w-14 sm:w-16 text-center rounded-lg px-1 py-1 font-mono transition-colors focus:outline-none no-spinner [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none {{ $isAh8Below ? 'border-2 border-rose-500 bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 font-bold focus:border-rose-600 focus:ring-1 focus:ring-rose-600' : 'border border-border bg-surface text-text-main font-semibold placeholder:text-text-muted/50 focus:border-primary focus:ring-1 focus:ring-primary' }}">
                            @endif
                        </td>

                        <!-- SAT -->
                        <td class="p-1.5 sm:p-2 text-center transition-colors {{ $isSatBelow ? 'bg-rose-100/70 dark:bg-rose-950/50' : 'bg-emerald-50/40 dark:bg-emerald-950/20' }}">
                            <input type="number" min="0" max="100" wire:model.blur="sumatifDataGenap.{{ $siswa->id }}.sas" placeholder="SAT"
                                @if($isSatBelow) title="Nilai {{ $vSat }} di bawah KKM ({{ $this->kkm }}) - Belum Kompeten" @endif
                                class="w-14 sm:w-16 text-center rounded-lg px-1 py-1 font-mono transition-colors focus:outline-none no-spinner [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none {{ $isSatBelow ? 'border-2 border-rose-500 bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 font-bold focus:border-rose-600 focus:ring-1 focus:ring-rose-600' : 'border border-emerald-300 dark:border-emerald-700 bg-surface text-emerald-900 dark:text-emerald-100 font-bold placeholder:text-emerald-400/70 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500' }}">
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- TAB 3: REKAPITULASI & CAPAIAN PER 9 MINGGU -->
    @if($activeTab === 'rekap')
    @php
        $currentSem = collect($semesters)->firstWhere('id', (int) $activeNilaiSemesterId);
        $semName = strtolower($currentSem['semester'] ?? '');
        $isGenap = str_contains($semName, 'genap');
        $k1 = $isGenap ? '9M3' : '9M1';
        $k2 = $isGenap ? '9M4' : '9M2';
        $labelBlock1 = $isGenap ? '9 Minggu ke-3 (Periode STS 2)' : '9 Minggu ke-1 (Periode STS 1)';
        $labelBlock2 = $isGenap ? '9 Minggu ke-4 (Periode SAT)' : '9 Minggu ke-2 (Periode SAS)';
        $ahLabel1 = $isGenap ? 'AH 5 & 6' : 'AH 1 & 2';
        $ahLabel2 = $isGenap ? 'AH 7 & 8' : 'AH 3 & 4';
        $sumatifLabel1 = $isGenap ? 'STS 2' : 'STS 1';
        $sumatifLabel2 = $isGenap ? 'SAT' : 'SAS';
    @endphp

    <div class="card mb-6 space-y-4">
        <!-- Toolbar & Semester Switcher -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 p-3 bg-surface-hover/70 rounded-xl border border-border">
            <div class="flex flex-wrap items-center gap-2.5">
                <span class="text-xs font-bold uppercase tracking-wider text-text-secondary">Rekap Semester:</span>
                <div class="inline-flex rounded-lg border border-border p-1 bg-surface shadow-xs">
                    @foreach($semesters as $s)
                    <button type="button"
                        wire:click="switchNilaiSemester({{ $s['id'] }})"
                        class="px-3.5 py-1 rounded-md text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer {{ (int)$activeNilaiSemesterId === (int)$s['id'] ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                        <span>{{ $s['nama'] }}</span>
                    </button>
                    @endforeach
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

        <!-- Tabel Matriks 9 Minggu Berdampingan -->
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
                        <th colspan="4" class="p-2 text-center border-b border-r-2 border-border bg-blue-100/90 dark:bg-blue-950/70 text-blue-950 dark:text-blue-100 font-bold text-xs tracking-wider">
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                <span>{{ $labelBlock1 }}</span>
                            </div>
                        </th>

                        <!-- Blok 2 (9M2 / 9M4) -->
                        <th colspan="4" class="p-2 text-center border-b border-border bg-emerald-100/90 dark:bg-emerald-950/70 text-emerald-950 dark:text-emerald-100 font-bold text-xs tracking-wider">
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                <span>{{ $labelBlock2 }}</span>
                            </div>
                        </th>
                    </tr>

                    <!-- HEADER BARIS 2: Komponen Nilai & Deskripsi -->
                    <tr class="text-[10px] font-bold text-text-secondary bg-surface-hover">
                        <!-- Blok 1 Sub-kolom -->
                        <th class="p-2 text-center min-w-[85px] border-b-2 border-r border-border bg-blue-50/70 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200" title="Rata-rata Formatif {{ $ahLabel1 }}">
                            Rata {{ $ahLabel1 }} (50%)
                        </th>
                        <th class="p-2 text-center min-w-[75px] border-b-2 border-r border-border bg-blue-50/70 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200" title="{{ $sumatifLabel1 }}">
                            {{ $sumatifLabel1 }} (50%)
                        </th>
                        <th class="p-2 text-center min-w-[70px] border-b-2 border-r border-border bg-blue-200/70 dark:bg-blue-900/60 text-blue-950 dark:text-blue-50 font-extrabold" title="Nilai Akhir {{ $k1 }}">
                            NA {{ $k1 }}
                        </th>
                        <th class="p-2 text-left min-w-[240px] max-w-[320px] border-b-2 border-r-2 border-border bg-blue-50/40 dark:bg-blue-950/30 text-text-main">
                            Deskripsi Capaian (Rapor {{ $k1 }})
                        </th>

                        <!-- Blok 2 Sub-kolom -->
                        <th class="p-2 text-center min-w-[85px] border-b-2 border-r border-border bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200" title="Rata-rata Formatif {{ $ahLabel2 }}">
                            Rata {{ $ahLabel2 }} (50%)
                        </th>
                        <th class="p-2 text-center min-w-[75px] border-b-2 border-r border-border bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200" title="{{ $sumatifLabel2 }}">
                            {{ $sumatifLabel2 }} (50%)
                        </th>
                        <th class="p-2 text-center min-w-[70px] border-b-2 border-r border-border bg-emerald-200/70 dark:bg-emerald-900/60 text-emerald-950 dark:text-emerald-50 font-extrabold" title="Nilai Akhir {{ $k2 }}">
                            NA {{ $k2 }}
                        </th>
                        <th class="p-2 text-left min-w-[240px] max-w-[320px] border-b-2 border-border bg-emerald-50/40 dark:bg-emerald-950/30 text-text-main">
                            Deskripsi Capaian (Rapor {{ $k2 }})
                        </th>
                    </tr>
                </thead>

                <!-- BODY DATA SISWA -->
                <tbody class="divide-y divide-border bg-surface text-text-main">
                    @foreach($siswas as $idx => $siswa)
                    @php
                        $rekap = $rekapData[$siswa->id] ?? null;
                        $d1 = $rekap ? $rekap->getRecordFor9M($k1) : null;
                        $d2 = $rekap ? $rekap->getRecordFor9M($k2) : null;

                        $rf1 = $d1['rata_formatif'] ?? null;
                        $sm1 = $d1['sumatif'] ?? null;
                        $na1 = $d1['nilai_akhir'] ?? null;
                        $isNa1Below = $this->isBelowKkm($na1);

                        $rf2 = $d2['rata_formatif'] ?? null;
                        $sm2 = $d2['sumatif'] ?? null;
                        $na2 = $d2['nilai_akhir'] ?? null;
                        $isNa2Below = $this->isBelowKkm($na2);

                        $isManual1 = !empty($d1['deskripsi_manual']);
                        $desc1 = $isManual1 ? $d1['deskripsi_manual'] : trim(($d1['deskripsi_tertinggi'] ?? '') . ' ' . ($d1['deskripsi_terendah'] ?? ''));

                        $isManual2 = !empty($d2['deskripsi_manual']);
                        $desc2 = $isManual2 ? $d2['deskripsi_manual'] : trim(($d2['deskripsi_tertinggi'] ?? '') . ' ' . ($d2['deskripsi_terendah'] ?? ''));
                    @endphp
                    <tr class="hover:bg-surface-hover/60 transition-colors">
                        <!-- No Sticky Left -->
                        <td class="w-10 min-w-[40px] max-w-[40px] p-2 text-center font-mono text-text-secondary sticky left-0 z-20 bg-surface dark:bg-surface-card border-b border-r border-border">
                            {{ $loop->iteration }}
                        </td>

                        <!-- Nama Siswa Sticky Left -->
                        <td class="min-w-[170px] max-w-[210px] p-2.5 font-medium sticky left-[40px] z-20 bg-surface dark:bg-surface-card border-b border-r-2 border-border shadow-[4px_0_8px_-2px_rgba(0,0,0,0.12)] whitespace-nowrap">
                            <div class="truncate text-xs font-semibold text-text-main" title="{{ $siswa->nama }}">{{ $siswa->nama }}</div>
                            <div class="text-[10px] text-text-secondary font-mono">{{ $siswa->nipd ?? $siswa->nisn ?? '-' }}</div>
                        </td>

                        <!-- BLOK 1: Rata Formatif -->
                        <td class="p-2 text-center font-mono font-medium border-b border-r border-border bg-blue-50/20 dark:bg-blue-950/10">
                            {{ $rf1 !== null ? number_format($rf1, 0) : '-' }}
                        </td>

                        <!-- BLOK 1: Sumatif STS -->
                        <td class="p-2 text-center font-mono font-medium border-b border-r border-border bg-blue-50/20 dark:bg-blue-950/10">
                            {{ $sm1 !== null ? number_format($sm1, 0) : '-' }}
                        </td>

                        <!-- BLOK 1: NA 9M1 / 9M3 -->
                        <td class="p-2 text-center font-mono font-bold text-sm border-b border-r border-border {{ $isNa1Below ? 'bg-rose-100/70 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300' : 'bg-blue-100/40 dark:bg-blue-950/30 text-blue-950 dark:text-blue-100' }}"
                            @if($isNa1Below) title="Nilai Akhir {{ $na1 }} di bawah KKM ({{ $this->kkm }})" @endif>
                            {{ $na1 !== null ? number_format($na1, 0) : '-' }}
                        </td>

                        <!-- BLOK 1: Deskripsi Capaian 9M1 / 9M3 -->
                        <td class="p-2 border-b border-r-2 border-border text-[11px] align-top bg-blue-50/10 dark:bg-blue-950/5 min-w-[240px]">
                            @if($isManual1)
                                <div class="text-[11px] leading-relaxed text-text-main mb-1.5 whitespace-pre-line" title="{{ $desc1 }}">
                                    {{ $desc1 }}
                                </div>
                                <div class="flex items-center justify-between gap-1 pt-1 border-t border-border/40">
                                    <span class="inline-flex items-center gap-0.5 text-[9px] px-1.5 py-0.2 rounded bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-200 font-bold">
                                        ✏️ Manual
                                    </span>
                                    <button type="button"
                                            wire:click="openEditDeskripsi9M({{ $siswa->id }}, '{{ $k1 }}')"
                                            class="text-[10px] text-primary hover:underline font-semibold cursor-pointer flex items-center gap-0.5">
                                        <span>Edit Narasi</span>
                                    </button>
                                </div>
                            @elseif(!empty($d1['deskripsi_tertinggi']) || !empty($d1['deskripsi_terendah']))
                                <div class="space-y-1 mb-1.5 text-[11px]">
                                    @if(!empty($d1['deskripsi_tertinggi']))
                                        <div class="leading-snug text-emerald-800 dark:text-emerald-300 font-medium" title="{{ $d1['deskripsi_tertinggi'] }}">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 align-middle"></span>{{ $d1['deskripsi_tertinggi'] }}
                                        </div>
                                    @endif
                                    @if(!empty($d1['deskripsi_terendah']))
                                        <div class="leading-snug text-slate-700 dark:text-slate-300 font-normal" title="{{ $d1['deskripsi_terendah'] }}">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-slate-400 mr-1 align-middle"></span>{{ $d1['deskripsi_terendah'] }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between gap-1 pt-1 border-t border-border/40">
                                    <span class="inline-flex items-center gap-0.5 text-[9px] px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-800 text-text-secondary font-medium">
                                        ⚡ Otomatis (2 Baris)
                                    </span>
                                    <button type="button"
                                            wire:click="openEditDeskripsi9M({{ $siswa->id }}, '{{ $k1 }}')"
                                            class="text-[10px] text-primary hover:underline font-semibold cursor-pointer flex items-center gap-0.5">
                                        <span>Edit Narasi</span>
                                    </button>
                                </div>
                            @else
                                <div class="flex items-center justify-between text-text-muted italic text-[11px] py-1">
                                    <span>Belum ada nilai</span>
                                    <button type="button"
                                            wire:click="openEditDeskripsi9M({{ $siswa->id }}, '{{ $k1 }}')"
                                            class="text-[10px] text-primary hover:underline font-semibold cursor-pointer">
                                        + Tulis
                                    </button>
                                </div>
                            @endif
                        </td>

                        <!-- BLOK 2: Rata Formatif -->
                        <td class="p-2 text-center font-mono font-medium border-b border-r border-border bg-emerald-50/20 dark:bg-emerald-950/10">
                            {{ $rf2 !== null ? number_format($rf2, 0) : '-' }}
                        </td>

                        <!-- BLOK 2: Sumatif SAS / SAT -->
                        <td class="p-2 text-center font-mono font-medium border-b border-r border-border bg-emerald-50/20 dark:bg-emerald-950/10">
                            {{ $sm2 !== null ? number_format($sm2, 0) : '-' }}
                        </td>

                        <!-- BLOK 2: NA 9M2 / 9M4 -->
                        <td class="p-2 text-center font-mono font-bold text-sm border-b border-r border-border {{ $isNa2Below ? 'bg-rose-100/70 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300' : 'bg-emerald-100/40 dark:bg-emerald-950/30 text-emerald-950 dark:text-emerald-100' }}"
                            @if($isNa2Below) title="Nilai Akhir {{ $na2 }} di bawah KKM ({{ $this->kkm }})" @endif>
                            {{ $na2 !== null ? number_format($na2, 0) : '-' }}
                        </td>

                        <!-- BLOK 2: Deskripsi Capaian 9M2 / 9M4 -->
                        <td class="p-2 border-b border-border text-[11px] align-top bg-emerald-50/10 dark:bg-emerald-950/5 min-w-[240px]">
                            @if($isManual2)
                                <div class="text-[11px] leading-relaxed text-text-main mb-1.5 whitespace-pre-line" title="{{ $desc2 }}">
                                    {{ $desc2 }}
                                </div>
                                <div class="flex items-center justify-between gap-1 pt-1 border-t border-border/40">
                                    <span class="inline-flex items-center gap-0.5 text-[9px] px-1.5 py-0.2 rounded bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-200 font-bold">
                                        ✏️ Manual
                                    </span>
                                    <button type="button"
                                            wire:click="openEditDeskripsi9M({{ $siswa->id }}, '{{ $k2 }}')"
                                            class="text-[10px] text-emerald-700 dark:text-emerald-400 hover:underline font-semibold cursor-pointer flex items-center gap-0.5">
                                        <span>Edit Narasi</span>
                                    </button>
                                </div>
                            @elseif(!empty($d2['deskripsi_tertinggi']) || !empty($d2['deskripsi_terendah']))
                                <div class="space-y-1 mb-1.5 text-[11px]">
                                    @if(!empty($d2['deskripsi_tertinggi']))
                                        <div class="leading-snug text-emerald-800 dark:text-emerald-300 font-medium" title="{{ $d2['deskripsi_tertinggi'] }}">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 align-middle"></span>{{ $d2['deskripsi_tertinggi'] }}
                                        </div>
                                    @endif
                                    @if(!empty($d2['deskripsi_terendah']))
                                        <div class="leading-snug text-slate-700 dark:text-slate-300 font-normal" title="{{ $d2['deskripsi_terendah'] }}">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-slate-400 mr-1 align-middle"></span>{{ $d2['deskripsi_terendah'] }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between gap-1 pt-1 border-t border-border/40">
                                    <span class="inline-flex items-center gap-0.5 text-[9px] px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-800 text-text-secondary font-medium">
                                        ⚡ Otomatis (2 Baris)
                                    </span>
                                    <button type="button"
                                            wire:click="openEditDeskripsi9M({{ $siswa->id }}, '{{ $k2 }}')"
                                            class="text-[10px] text-emerald-700 dark:text-emerald-400 hover:underline font-semibold cursor-pointer flex items-center gap-0.5">
                                        <span>Edit Narasi</span>
                                    </button>
                                </div>
                            @else
                                <div class="flex items-center justify-between text-text-muted italic text-[11px] py-1">
                                    <span>Belum ada nilai</span>
                                    <button type="button"
                                            wire:click="openEditDeskripsi9M({{ $siswa->id }}, '{{ $k2 }}')"
                                            class="text-[10px] text-emerald-700 dark:text-emerald-400 hover:underline font-semibold cursor-pointer">
                                        + Tulis
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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
                    <div class="p-4 bg-surface-hover/40 border border-border rounded-xl">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                            <label class="text-xs font-bold uppercase tracking-wider text-text-secondary flex items-center gap-1.5">
                                <span>🎯 Pilih Kolom Nilai yang Diimpor</span>
                            </label>
                            <!-- Preset Buttons -->
                            <div class="inline-flex gap-1 text-[11px]">
                                <button type="button" wire:click="setImportPreset('semua')" class="px-2 py-0.5 rounded bg-surface border border-border hover:bg-primary hover:text-white transition-colors">Semua (12)</button>
                                <button type="button" wire:click="setImportPreset('ganjil')" class="px-2 py-0.5 rounded bg-blue-50 text-blue-800 dark:bg-blue-950 dark:text-blue-200 border border-blue-200 dark:border-blue-800 hover:bg-blue-600 hover:text-white transition-colors">Ganjil Saja</button>
                                <button type="button" wire:click="setImportPreset('genap')" class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-600 hover:text-white transition-colors">Genap Saja</button>
                                <button type="button" wire:click="setImportPreset('reset')" class="px-2 py-0.5 rounded bg-surface border border-border hover:bg-rose-500 hover:text-white transition-colors">Kosongkan</button>
                            </div>
                        </div>

                        <!-- Checkboxes: Ganjil & Genap -->
                        <div class="space-y-2 text-xs">
                            <!-- Ganjil -->
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

                            <!-- Genap -->
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
