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
            @if(count($allTps) > 0)
            <div class="text-xs px-3 py-1.5 bg-blue-50 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200 rounded-lg border border-blue-200 dark:border-blue-900 flex items-center gap-1.5 self-start">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Patokan kode berikutnya: <strong>{{ $this->suggestedNextKode }}</strong> (atau <strong>CP {{ $this->suggestedNextNumber }}</strong>)</span>
            </div>
            @endif
        </div>
        
        @if(session()->has('message_tp'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-emerald-900 dark:bg-emerald-950/40 dark:border-emerald-800 dark:text-emerald-200 rounded-lg text-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('message_tp') }}</span>
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

            <button wire:click="saveNilai" class="btn btn-primary flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Simpan Semua Nilai</span>
            </button>
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

    <!-- TAB 3: REKAPITULASI & CAPAIAN -->
    @if($activeTab === 'rekap')
    <div class="card mb-6">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-4 p-3 bg-surface-hover/70 rounded-xl border border-border">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-text-secondary">Rekap Semester:</span>
                <div class="inline-flex rounded-lg border border-border p-1 bg-surface shadow-xs">
                    @foreach($semesters as $s)
                    <button type="button"
                        wire:click="switchNilaiSemester({{ $s['id'] }})"
                        class="px-3.5 py-1.5 rounded-md text-xs font-semibold transition-all flex items-center gap-1.5 {{ (int)$activeNilaiSemesterId === (int)$s['id'] ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                        <span>{{ $s['nama'] }}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            <button wire:click="calculateRekap" class="btn btn-secondary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <span>Hitung Ulang Rekap</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border border-border rounded-lg overflow-hidden">
                <thead class="bg-surface-hover text-xs uppercase tracking-wider">
                    <tr>
                        <th class="p-3 border-b border-border text-text-main">Nama Siswa</th>
                        <th class="p-3 border-b border-border text-center text-text-main">Rata Formatif / AH (50%)</th>
                        <th class="p-3 border-b border-border text-center text-text-main min-w-[85px]">{{ $this->labelSts }} (25%)</th>
                        <th class="p-3 border-b border-border text-center text-text-main min-w-[85px]">{{ $this->labelSas }} (25%)</th>
                        <th class="p-3 border-b border-border text-center bg-blue-100/80 dark:bg-blue-900/50 text-blue-900 dark:text-blue-100 font-bold min-w-[70px]">NA</th>
                        <th class="p-3 border-b border-border text-text-main w-2/5">Deskripsi Capaian (Auto-generated)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($siswas as $siswa)
                    @php $rekap = $rekapData[$siswa->id] ?? null; @endphp
                    <tr class="hover:bg-surface-hover/50">
                        <td class="p-3 font-semibold text-text-main">{{ $siswa->nama }}</td>
                        <td class="p-3 text-center font-mono font-medium text-text-main">{{ $rekap ? number_format($rekap->rata_formatif, 0) : '-' }}</td>
                        <td class="p-3 text-center font-mono font-medium text-text-main">{{ $rekap ? $rekap->sts : '-' }}</td>
                        <td class="p-3 text-center font-mono font-medium text-text-main">{{ $rekap ? $rekap->sas : '-' }}</td>
                        @php
                            $naVal = $rekap ? $rekap->nilai_akhir : null;
                            $isNaBelow = $this->isBelowKkm($naVal);
                        @endphp
                        <td class="p-3 text-center font-mono font-bold text-base transition-colors {{ $isNaBelow ? 'bg-rose-100/70 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300' : 'bg-blue-50/70 dark:bg-blue-950/40 text-blue-900 dark:text-blue-100' }}"
                            @if($isNaBelow) title="Nilai Akhir {{ number_format($naVal, 0) }} di bawah KKM ({{ $this->kkm }}) - Belum Tuntas" @endif>
                            {{ $rekap ? number_format($rekap->nilai_akhir, 0) : '-' }}
                        </td>
                        <td class="p-3 text-xs">
                            @if($rekap)
                            @if(!empty($rekap->deskripsi_tertinggi))
                            <div class="mb-1 text-emerald-800 dark:text-emerald-300 font-medium leading-relaxed">{{ $rekap->deskripsi_tertinggi }}</div>
                            @endif
                            @if(!empty($rekap->deskripsi_terendah))
                            <div class="mb-2 text-rose-800 dark:text-rose-300 font-medium leading-relaxed">{{ $rekap->deskripsi_terendah }}</div>
                            @endif
                            
                            <details class="mt-1">
                                <summary class="cursor-pointer text-xs text-primary dark:text-blue-400 font-semibold hover:underline">✏️ Edit Manual (Override)</summary>
                                <textarea 
                                    class="w-full border border-border bg-surface text-text-main p-2 rounded-lg text-xs mt-1 focus:ring-primary focus:border-primary" 
                                    rows="2" 
                                    placeholder="Ketik deskripsi manual di sini untuk menimpa deskripsi otomatis..."
                                    wire:blur="updateManualDeskripsi({{ $siswa->id }}, $event.target.value)"
                                >{{ $rekap->deskripsi_manual }}</textarea>
                                <div class="text-[11px] text-text-muted mt-0.5">Otomatis tersimpan saat kursor berpindah.</div>
                            </details>
                            @else
                            <span class="italic text-text-muted text-xs">Belum dihitung</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
