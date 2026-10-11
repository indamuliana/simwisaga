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
                <h3 class="text-lg font-bold text-text-main">Kelola Tujuan Pembelajaran (TP / CP)</h3>
                <p class="text-xs text-text-secondary mt-0.5">
                    Kelola seluruh TP untuk Semester Ganjil dan Genap. Gunakan daftar TP semester sebelumnya sebagai patokan urutan berkelanjutan.
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
                <span>{{ $edit_tp_id ? '✏️ Formulir Edit TP / CP' : '➕ Formulir Tambah TP / CP' }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Semester Target</label>
                    <select wire:model="tp_semester_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main py-2 px-3 focus:border-primary focus:ring-primary">
                        @foreach($semesters as $s)
                        <option value="{{ $s['id'] }}">{{ $s['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('tp_semester_id') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Kode TP / CP</label>
                    <input type="text" wire:model="tp_kode" class="w-full text-sm rounded-lg border-border bg-surface text-text-main py-2 px-3 focus:border-primary focus:ring-primary font-mono uppercase" required placeholder="Misal: {{ $this->suggestedNextKode }}">
                    @error('tp_kode') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-5">
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
                        <th class="p-3 w-36 border-b dark:border-gray-600">Semester</th>
                        <th class="p-3 w-28 border-b dark:border-gray-600">Kode</th>
                        <th class="p-3 border-b dark:border-gray-600">Deskripsi Capaian Pembelajaran</th>
                        <th class="p-3 w-36 text-center border-b dark:border-gray-600">Status Formatif</th>
                        <th class="p-3 w-28 text-center border-b dark:border-gray-600">Aksi</th>
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
                        <td colspan="6" class="p-6 text-center text-text-secondary">
                            Belum ada Tujuan Pembelajaran yang diinput untuk kelas ini. Silakan gunakan formulir di atas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- TAB 2: INPUT NILAI FORMATIF & SUMATIF -->
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

        @if(count($tps) === 0)
        <div class="p-6 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900 text-amber-800 dark:text-amber-200 rounded-xl text-center">
            <p class="font-semibold mb-1">Belum ada Tujuan Pembelajaran (TP / CP) untuk semester ini.</p>
            <p class="text-xs text-amber-700 dark:text-amber-300">
                Silakan buka tab <button wire:click="switchTab('tp')" class="underline font-bold">1. Tujuan Pembelajaran</button> dan tambahkan TP untuk semester yang dipilih.
            </p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border border-border rounded-lg overflow-hidden">
                <thead class="bg-surface-hover">
                    <tr>
                        <th rowspan="2" class="p-3 border-b border-r dark:border-gray-600 sticky left-0 bg-surface-hover z-10 w-64 text-text-main font-semibold">Nama Siswa</th>
                        <th colspan="{{ count($tps) }}" class="p-2 border-b border-r text-center dark:border-gray-600 bg-blue-50 dark:bg-blue-900/20 text-primary font-bold">Nilai Formatif (Harian)</th>
                        <th colspan="2" class="p-2 border-b text-center dark:border-gray-600 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 font-bold">Nilai Sumatif</th>
                    </tr>
                    <tr>
                        @foreach($tps as $tp)
                        <th class="p-2 border-b border-r text-center dark:border-gray-600 min-w-[70px]" title="{{ $tp->deskripsi }}">
                            <span class="font-mono font-bold">{{ $tp->kode }}</span>
                        </th>
                        @endforeach
                        <th class="p-2 border-b border-r text-center dark:border-gray-600 min-w-[70px]">STS</th>
                        <th class="p-2 border-b text-center dark:border-gray-600 min-w-[70px]">SAS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($siswas as $siswa)
                    <tr class="hover:bg-surface-hover/50">
                        <td class="p-3 border-r dark:border-gray-600 sticky left-0 bg-surface font-medium text-text-main">
                            {{ $siswa->nama }}
                        </td>
                        
                        <!-- Formatif -->
                        @foreach($tps as $tp)
                        <td class="p-2 border-r dark:border-gray-600 text-center">
                            <input type="number" min="0" max="100" wire:model="formatifData.{{ $siswa->id }}.{{ $tp->id }}" class="w-16 md:w-20 text-center border border-border dark:border-gray-600 rounded-lg px-2 py-1 bg-surface text-text-main focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        </td>
                        @endforeach
                        
                        <!-- Sumatif -->
                        <td class="p-2 border-r text-center bg-amber-50/20 dark:bg-amber-950/10">
                            <input type="number" min="0" max="100" wire:model="sumatifData.{{ $siswa->id }}.sts" class="w-16 md:w-20 text-center border border-border dark:border-gray-600 rounded-lg px-2 py-1 bg-surface text-text-main focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 font-medium">
                        </td>
                        <td class="p-2 text-center bg-amber-50/20 dark:bg-amber-950/10">
                            <input type="number" min="0" max="100" wire:model="sumatifData.{{ $siswa->id }}.sas" class="w-16 md:w-20 text-center border border-border dark:border-gray-600 rounded-lg px-2 py-1 bg-surface text-text-main focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 font-medium">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
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
                        <th class="p-3 border-b text-center dark:border-gray-600">Rata Formatif (50%)</th>
                        <th class="p-3 border-b text-center dark:border-gray-600">STS (25%)</th>
                        <th class="p-3 border-b text-center dark:border-gray-600">SAS (25%)</th>
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
