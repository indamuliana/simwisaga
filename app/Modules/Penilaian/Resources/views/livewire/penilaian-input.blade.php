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
            <span>2. Input Nilai Akademik</span>
        </button>
        <button wire:click="switchTab('rekap')" class="px-4 py-2.5 border-b-2 font-medium text-sm whitespace-nowrap transition-colors flex items-center gap-2 {{ $activeTab === 'rekap' ? 'border-emerald-500 text-emerald-600 font-semibold' : 'border-transparent text-text-secondary hover:text-text-main' }}">
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
            <div class="text-xs px-3 py-1.5 bg-blue-50 dark:bg-blue-950/40 text-blue-800 dark:text-blue-300 rounded-lg border border-blue-200 dark:border-blue-900 flex items-center gap-1.5 self-start">
                <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Patokan kode berikutnya: <strong>{{ $this->suggestedNextKode }}</strong> (atau <strong>CP {{ $this->suggestedNextNumber }}</strong>)</span>
            </div>
            @endif
        </div>
        
        @if(session()->has('message_tp'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-emerald-800 dark:bg-emerald-950/40 dark:border-emerald-800 dark:text-emerald-300 rounded-lg text-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
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
                <thead class="bg-surface-hover text-text-secondary text-xs uppercase tracking-wider">
                    <tr>
                        <th class="p-3 w-12 text-center border-b dark:border-gray-600">No</th>
                        <th class="p-3 w-32 border-b dark:border-gray-600">Semester</th>
                        <th class="p-3 w-28 text-center border-b dark:border-gray-600">Alokasi AH</th>
                        <th class="p-3 w-24 border-b dark:border-gray-600">Kode</th>
                        <th class="p-3 border-b dark:border-gray-600">Deskripsi Capaian Pembelajaran</th>
                        <th class="p-3 w-32 text-center border-b dark:border-gray-600">Status Formatif</th>
                        <th class="p-3 w-24 text-center border-b dark:border-gray-600">Aksi</th>
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
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                {{ $tp->alokasi_ah }}
                            </span>
                            @else
                            <span class="text-xs text-rose-500 italic">Belum diatur</span>
                            @endif
                        </td>
                        <td class="p-3 font-bold font-mono text-text-main">{{ $tp->kode }}</td>
                        <td class="p-3 text-text-main">{{ $tp->deskripsi }}</td>
                        <td class="p-3 text-center text-xs">
                            @if($tp->nilai_formatifs_count > 0)
                            <span class="inline-flex items-center gap-1 font-semibold text-emerald-600 dark:text-emerald-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                {{ $tp->nilai_formatifs_count }} siswa dinilai
                            </span>
                            @else
                            <span class="text-text-secondary italic">Belum ada nilai</span>
                            @endif
                        </td>
                        <td class="p-3 text-center">
                            <div class="inline-flex items-center gap-2">
                                <button wire:click="editTP({{ $tp->id }})" class="text-xs text-amber-600 hover:text-amber-700 font-semibold hover:underline">Edit</button>
                                <span class="text-border">|</span>
                                <button wire:click="hapusTP({{ $tp->id }})" onclick="confirm('Yakin ingin menghapus TP ini beserta seluruh nilai formatif terkait?') || event.stopImmediatePropagation()" class="text-xs text-rose-600 hover:text-rose-700 font-semibold hover:underline">Hapus</button>
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

    <!-- TAB 2: INPUT NILAI AKADEMIK (KRONOLOGIS AH & SUMATIF) -->
    @if($activeTab === 'formatif')
    <div class="card mb-6">
        <!-- Switcher Semester Penilaian -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-4 p-3 bg-surface-hover/70 rounded-xl border border-border">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-text-secondary">Pilih Semester Penilaian:</span>
                <div class="inline-flex rounded-lg border border-border p-1 bg-surface shadow-xs">
                    @foreach($semesters as $s)
                    <button type="button"
                        wire:click="switchNilaiSemester({{ $s['id'] }})"
                        class="px-3.5 py-1.5 rounded-md text-xs font-semibold transition-all flex items-center gap-1.5 {{ (int)$activeNilaiSemesterId === (int)$s['id'] ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                        <span>{{ $s['nama'] }}</span>
                        @if($s['is_current_penugasan'])
                        <span class="text-[10px] px-1 py-0.2 rounded {{ (int)$activeNilaiSemesterId === (int)$s['id'] ? 'bg-white/20 text-white' : 'bg-surface-hover text-text-secondary' }}">Asal</span>
                        @endif
                    </button>
                    @endforeach
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

        <div class="mb-3 px-3 py-2 bg-blue-50/60 dark:bg-blue-950/30 border border-blue-200/80 dark:border-blue-900/50 rounded-lg text-xs text-text-secondary flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="font-bold text-primary">💡 Alur Penilaian Kronologis:</span>
                <span>Nilai diinput berdasarkan sesi <strong>Asesmen Harian (AH)</strong> per 2 pekan. Nilai AH otomatis menduplikasi nilai ke seluruh TP yang dinaungi.</span>
            </div>
            <span class="text-[11px] text-text-secondary italic">{{ $this->labelSts }} & {{ $this->labelSas }} memiliki porsi 25% masing-masing</span>
        </div>

        @php
            $ahList = $this->activeAhList;
            $ah1 = $ahList[0];
            $ah2 = $ahList[1];
            $ah3 = $ahList[2];
            $ah4 = $ahList[3];

            $mapping = $this->ahTpsMapping;
            $tps1 = $mapping[$ah1] ?? collect();
            $tps2 = $mapping[$ah2] ?? collect();
            $tps3 = $mapping[$ah3] ?? collect();
            $tps4 = $mapping[$ah4] ?? collect();
        @endphp

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border border-border rounded-lg overflow-hidden">
                <thead class="bg-surface-hover">
                    <tr>
                        <th rowspan="2" class="p-3 border-b border-r dark:border-gray-600 sticky left-0 bg-surface-hover z-10 w-60 text-text-main font-semibold">Nama Siswa</th>
                        <th colspan="3" class="p-2 border-b border-r text-center dark:border-gray-600 bg-blue-50/80 dark:bg-blue-900/30 text-blue-900 dark:text-blue-200 font-bold">
                            Periode Pra-Tengah Semester ({{ $ah1 }}, {{ $ah2 }} & {{ $this->labelSts }})
                        </th>
                        <th colspan="3" class="p-2 border-b text-center dark:border-gray-600 bg-amber-50/80 dark:bg-amber-900/30 text-amber-900 dark:text-amber-200 font-bold">
                            Periode Pra-Akhir Semester ({{ $ah3 }}, {{ $ah4 }} & {{ $this->labelSas }})
                        </th>
                    </tr>
                    <tr>
                        <!-- AH 1 / AH 5 -->
                        <th class="p-2 border-b border-r text-center dark:border-gray-600 min-w-[95px] bg-blue-50/40 dark:bg-blue-900/10">
                            <div class="font-mono font-bold text-text-main">{{ $ah1 }}</div>
                            @if($tps1->isNotEmpty())
                            <div class="flex flex-wrap gap-1 justify-center mt-1">
                                @foreach($tps1 as $tp)
                                <span class="text-[10px] px-1.5 py-0.2 rounded bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 font-medium" title="{{ $tp->deskripsi }}">{{ $tp->kode }}</span>
                                @endforeach
                            </div>
                            @else
                            <div class="text-[10px] text-rose-500 font-normal mt-0.5 italic">Belum ada TP</div>
                            @endif
                        </th>

                        <!-- AH 2 / AH 6 -->
                        <th class="p-2 border-b border-r text-center dark:border-gray-600 min-w-[95px] bg-blue-50/40 dark:bg-blue-900/10">
                            <div class="font-mono font-bold text-text-main">{{ $ah2 }}</div>
                            @if($tps2->isNotEmpty())
                            <div class="flex flex-wrap gap-1 justify-center mt-1">
                                @foreach($tps2 as $tp)
                                <span class="text-[10px] px-1.5 py-0.2 rounded bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 font-medium" title="{{ $tp->deskripsi }}">{{ $tp->kode }}</span>
                                @endforeach
                            </div>
                            @else
                            <div class="text-[10px] text-rose-500 font-normal mt-0.5 italic">Belum ada TP</div>
                            @endif
                        </th>

                        <!-- STS -->
                        <th class="p-2 border-b border-r text-center dark:border-gray-600 min-w-[85px] bg-blue-100/60 dark:bg-blue-900/40" title="{{ $this->labelSts === 'STS 2' ? 'Sumatif Tengah Semester 2' : 'Sumatif Tengah Semester 1' }}">
                            <div class="font-mono font-bold text-blue-900 dark:text-blue-200">{{ $this->labelSts }}</div>
                            <div class="text-[10px] text-blue-700 dark:text-blue-300 font-normal">Tengah Sem.</div>
                        </th>

                        <!-- AH 3 / AH 7 -->
                        <th class="p-2 border-b border-r text-center dark:border-gray-600 min-w-[95px] bg-amber-50/40 dark:bg-amber-900/10">
                            <div class="font-mono font-bold text-text-main">{{ $ah3 }}</div>
                            @if($tps3->isNotEmpty())
                            <div class="flex flex-wrap gap-1 justify-center mt-1">
                                @foreach($tps3 as $tp)
                                <span class="text-[10px] px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 font-medium" title="{{ $tp->deskripsi }}">{{ $tp->kode }}</span>
                                @endforeach
                            </div>
                            @else
                            <div class="text-[10px] text-rose-500 font-normal mt-0.5 italic">Belum ada TP</div>
                            @endif
                        </th>

                        <!-- AH 4 / AH 8 -->
                        <th class="p-2 border-b border-r text-center dark:border-gray-600 min-w-[95px] bg-amber-50/40 dark:bg-amber-900/10">
                            <div class="font-mono font-bold text-text-main">{{ $ah4 }}</div>
                            @if($tps4->isNotEmpty())
                            <div class="flex flex-wrap gap-1 justify-center mt-1">
                                @foreach($tps4 as $tp)
                                <span class="text-[10px] px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 font-medium" title="{{ $tp->deskripsi }}">{{ $tp->kode }}</span>
                                @endforeach
                            </div>
                            @else
                            <div class="text-[10px] text-rose-500 font-normal mt-0.5 italic">Belum ada TP</div>
                            @endif
                        </th>

                        <!-- Sumatif Akhir -->
                        <th class="p-2 border-b text-center dark:border-gray-600 min-w-[85px] bg-amber-100/60 dark:bg-amber-900/40" title="{{ $this->labelSas === 'SAT' ? 'Sumatif Akhir Tahun' : 'Sumatif Akhir Semester' }}">
                            <div class="font-mono font-bold text-amber-900 dark:text-amber-200">{{ $this->labelSas }}</div>
                            <div class="text-[10px] text-amber-700 dark:text-amber-300 font-normal">Akhir Sem.</div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($siswas as $siswa)
                    <tr class="hover:bg-surface-hover/50">
                        <td class="p-3 border-r dark:border-gray-600 sticky left-0 bg-surface font-medium text-text-main">
                            {{ $siswa->nama }}
                        </td>
                        
                        <!-- AH 1 / 5 -->
                        <td class="p-2 border-r dark:border-gray-600 text-center {{ $tps1->isEmpty() ? 'bg-gray-50/70 dark:bg-gray-800/40' : '' }}">
                            @if($tps1->isEmpty())
                                <input type="text" disabled value="-" title="Belum dialokasikan TP pada {{ $ah1 }}" class="w-16 md:w-20 text-center border border-border/50 dark:border-gray-700 rounded-lg px-2 py-1 bg-surface-hover text-text-secondary cursor-not-allowed text-xs">
                            @else
                                <input type="number" min="0" max="100" wire:model="ahData.{{ $siswa->id }}.{{ $ah1 }}" placeholder="{{ $ah1 }}" class="w-16 md:w-20 text-center border border-border dark:border-gray-600 rounded-lg px-2 py-1 bg-surface text-text-main focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary font-medium">
                            @endif
                        </td>

                        <!-- AH 2 / 6 -->
                        <td class="p-2 border-r dark:border-gray-600 text-center {{ $tps2->isEmpty() ? 'bg-gray-50/70 dark:bg-gray-800/40' : '' }}">
                            @if($tps2->isEmpty())
                                <input type="text" disabled value="-" title="Belum dialokasikan TP pada {{ $ah2 }}" class="w-16 md:w-20 text-center border border-border/50 dark:border-gray-700 rounded-lg px-2 py-1 bg-surface-hover text-text-secondary cursor-not-allowed text-xs">
                            @else
                                <input type="number" min="0" max="100" wire:model="ahData.{{ $siswa->id }}.{{ $ah2 }}" placeholder="{{ $ah2 }}" class="w-16 md:w-20 text-center border border-border dark:border-gray-600 rounded-lg px-2 py-1 bg-surface text-text-main focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary font-medium">
                            @endif
                        </td>

                        <!-- STS -->
                        <td class="p-2 border-r text-center bg-blue-50/30 dark:bg-blue-950/20">
                            <input type="number" min="0" max="100" wire:model="sumatifData.{{ $siswa->id }}.sts" placeholder="{{ $this->labelSts }}" class="w-16 md:w-20 text-center border border-blue-200 dark:border-blue-800 rounded-lg px-2 py-1 bg-surface text-text-main focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary font-bold text-blue-700 dark:text-blue-300">
                        </td>

                        <!-- AH 3 / 7 -->
                        <td class="p-2 border-r dark:border-gray-600 text-center {{ $tps3->isEmpty() ? 'bg-gray-50/70 dark:bg-gray-800/40' : '' }}">
                            @if($tps3->isEmpty())
                                <input type="text" disabled value="-" title="Belum dialokasikan TP pada {{ $ah3 }}" class="w-16 md:w-20 text-center border border-border/50 dark:border-gray-700 rounded-lg px-2 py-1 bg-surface-hover text-text-secondary cursor-not-allowed text-xs">
                            @else
                                <input type="number" min="0" max="100" wire:model="ahData.{{ $siswa->id }}.{{ $ah3 }}" placeholder="{{ $ah3 }}" class="w-16 md:w-20 text-center border border-border dark:border-gray-600 rounded-lg px-2 py-1 bg-surface text-text-main focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary font-medium">
                            @endif
                        </td>

                        <!-- AH 4 / 8 -->
                        <td class="p-2 border-r dark:border-gray-600 text-center {{ $tps4->isEmpty() ? 'bg-gray-50/70 dark:bg-gray-800/40' : '' }}">
                            @if($tps4->isEmpty())
                                <input type="text" disabled value="-" title="Belum dialokasikan TP pada {{ $ah4 }}" class="w-16 md:w-20 text-center border border-border/50 dark:border-gray-700 rounded-lg px-2 py-1 bg-surface-hover text-text-secondary cursor-not-allowed text-xs">
                            @else
                                <input type="number" min="0" max="100" wire:model="ahData.{{ $siswa->id }}.{{ $ah4 }}" placeholder="{{ $ah4 }}" class="w-16 md:w-20 text-center border border-border dark:border-gray-600 rounded-lg px-2 py-1 bg-surface text-text-main focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary font-medium">
                            @endif
                        </td>

                        <!-- Sumatif Akhir -->
                        <td class="p-2 text-center bg-amber-50/30 dark:bg-amber-950/20">
                            <input type="number" min="0" max="100" wire:model="sumatifData.{{ $siswa->id }}.sas" placeholder="{{ $this->labelSas }}" class="w-16 md:w-20 text-center border border-amber-200 dark:border-amber-800 rounded-lg px-2 py-1 bg-surface text-text-main focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 font-bold text-amber-700 dark:text-amber-300">
                        </td>
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
                <thead class="bg-surface-hover text-text-secondary text-xs uppercase tracking-wider">
                    <tr>
                        <th class="p-3 border-b dark:border-gray-600">Nama Siswa</th>
                        <th class="p-3 border-b text-center dark:border-gray-600">Rata Formatif / AH (50%)</th>
                        <th class="p-3 border-b text-center dark:border-gray-600 min-w-[85px]">{{ $this->labelSts }} (25%)</th>
                        <th class="p-3 border-b text-center dark:border-gray-600 min-w-[85px]">{{ $this->labelSas }} (25%)</th>
                        <th class="p-3 border-b text-center bg-blue-50 dark:bg-blue-900/30 text-primary font-bold dark:border-gray-600">NA</th>
                        <th class="p-3 border-b w-2/5 dark:border-gray-600">Deskripsi Capaian (Auto-generated)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($siswas as $siswa)
                    @php $rekap = $rekapData[$siswa->id] ?? null; @endphp
                    <tr class="hover:bg-surface-hover/50">
                        <td class="p-3 font-medium text-text-main">{{ $siswa->nama }}</td>
                        <td class="p-3 text-center">{{ $rekap ? number_format($rekap->rata_formatif, 0) : '-' }}</td>
                        <td class="p-3 text-center">{{ $rekap ? $rekap->sts : '-' }}</td>
                        <td class="p-3 text-center">{{ $rekap ? $rekap->sas : '-' }}</td>
                        <td class="p-3 text-center bg-blue-50/50 dark:bg-blue-900/10 font-bold text-primary">
                            {{ $rekap ? number_format($rekap->nilai_akhir, 0) : '-' }}
                        </td>
                        <td class="p-3 text-xs text-text-secondary">
                            @if($rekap)
                            <div class="mb-1 text-emerald-600 dark:text-emerald-400 font-medium">{{ $rekap->deskripsi_tertinggi }}</div>
                            <div class="mb-2 text-rose-600 dark:text-rose-400 font-medium">{{ $rekap->deskripsi_terendah }}</div>
                            
                            <details class="mt-1">
                                <summary class="cursor-pointer text-primary font-medium hover:underline">Edit Manual (Override)</summary>
                                <textarea 
                                    class="w-full border border-border dark:border-gray-600 bg-surface text-text-main p-2 rounded-lg text-xs mt-1" 
                                    rows="2" 
                                    placeholder="Ketik deskripsi manual di sini untuk menimpa deskripsi otomatis..."
                                    wire:blur="updateManualDeskripsi({{ $siswa->id }}, $event.target.value)"
                                >{{ $rekap->deskripsi_manual }}</textarea>
                                <div class="text-[10px] text-text-secondary mt-0.5">Otomatis tersimpan saat kursor berpindah.</div>
                            </details>
                            @else
                            <span class="italic text-text-secondary">Belum dihitung</span>
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
