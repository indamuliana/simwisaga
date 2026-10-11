<div>
    <div class="mb-4">
        <a href="{{ route('penilaian.index') }}" class="text-primary hover:underline">&larr; Kembali ke Dashboard</a>
    </div>

    <h2 class="mb-2 text-2xl font-bold text-text-main">Pengolahan Nilai: {{ $penugasan->mapel->nama }}</h2>
    <p class="mb-6 text-text-secondary">Kelas: {{ $penugasan->rombel->nama }} | Kurikulum Merdeka</p>

    <!-- Tabs -->
    <div class="border-b border-border mb-6 flex overflow-x-auto">
        <button wire:click="switchTab('tp')" class="px-4 py-2 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'tp' ? 'border-google-blue text-primary' : 'border-transparent text-text-secondary hover:text-text-main dark:hover:text-gray-300' }}">
            1. Tujuan Pembelajaran
        </button>
        <button wire:click="switchTab('formatif')" class="px-4 py-2 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'formatif' ? 'border-google-blue text-primary' : 'border-transparent text-text-secondary hover:text-text-main dark:hover:text-gray-300' }}">
            2. Input Nilai Akademik
        </button>
        <button wire:click="switchTab('rekap')" class="px-4 py-2 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'rekap' ? 'border-google-green text-google-green' : 'border-transparent text-text-secondary hover:text-text-main dark:hover:text-gray-300' }}">
            3. Rekap & Capaian
        </button>
    </div>

    @if($activeTab === 'tp')
    <div class="card mb-6">
        <h3 class="text-lg font-bold mb-4">Input Tujuan Pembelajaran (TP)</h3>
        
        @if(session()->has('message_tp'))
        <div class="mb-4 p-3 bg-green-100 text-success rounded">
            {{ session('message_tp') }}
        </div>
        @endif

        <form wire:submit.prevent="saveTP" class="flex flex-col md:flex-row gap-4 items-end mb-6">
            <div>
                <label class="label">Kode TP</label>
                <input type="text" wire:model="tp_kode" class="input w-24" required placeholder="TP1">
            </div>
            <div class="flex-grow">
                <label class="label">Deskripsi Capaian Kompetensi</label>
                <input type="text" wire:model="tp_deskripsi" class="input" required placeholder="Contoh: Memahami dasar logika algoritma">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn {{ $edit_tp_id ? 'btn-yellow' : 'btn-primary' }}">
                    {{ $edit_tp_id ? 'Update TP' : 'Tambah TP' }}
                </button>
                @if($edit_tp_id)
                <button type="button" wire:click="cancelEditTP" class="btn btn-secondary">Batal</button>
                @endif
            </div>
        </form>

        <table class="w-full text-sm text-left mt-4 border border-border">
            <thead class="bg-surface-hover">
                <tr>
                    <th class="p-3 w-24 border-b dark:border-gray-600">Kode TP</th>
                    <th class="p-3 border-b dark:border-gray-600">Deskripsi</th>
                    <th class="p-3 w-32 text-center border-b dark:border-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                @forelse($tps as $tp)
                <tr class="{{ $edit_tp_id === $tp->id ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                    <td class="p-3 font-semibold">{{ $tp->kode }}</td>
                    <td class="p-3">{{ $tp->deskripsi }}</td>
                    <td class="p-3 text-center flex justify-center gap-3">
                        <button wire:click="editTP({{ $tp->id }})" class="text-google-yellow hover:underline">Edit</button>
                        <button wire:click="hapusTP({{ $tp->id }})" onclick="confirm('Yakin hapus TP ini beserta nilainya?') || event.stopImmediatePropagation()" class="text-google-red hover:underline">Hapus</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="p-4 text-center text-text-secondary">Belum ada TP untuk kelas ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    @if($activeTab === 'formatif')
    <div class="card mb-6 overflow-x-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Input Nilai Formatif & Sumatif</h3>
            <button wire:click="saveNilai" class="btn btn-primary">Simpan Semua Nilai</button>
        </div>

        @if(session()->has('message_nilai'))
        <div class="mb-4 p-3 bg-green-100 text-success rounded">
            {{ session('message_nilai') }}
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
        <div class="p-4 bg-yellow-50 text-yellow-800 rounded">Harap isi Tujuan Pembelajaran (TP) terlebih dahulu pada tab sebelumnya.</div>
        @else
        <table class="w-full text-sm text-left border border-border">
            <thead class="bg-surface-hover">
                <tr>
                    <th rowspan="2" class="p-3 border-b border-r dark:border-gray-600 sticky left-0 bg-surface-hover z-10 w-64">Nama Siswa</th>
                    <th colspan="{{ count($tps) }}" class="p-2 border-b border-r text-center dark:border-gray-600 bg-blue-50 dark:bg-blue-900/20 text-primary">Nilai Formatif (Harian)</th>
                    <th colspan="2" class="p-2 border-b text-center dark:border-gray-600 bg-yellow-50 dark:bg-yellow-900/20 text-google-yellow">Nilai Sumatif</th>
                </tr>
                <tr>
                    @foreach($tps as $tp)
                    <th class="p-2 border-b border-r text-center dark:border-gray-600" title="{{ $tp->deskripsi }}">{{ $tp->kode }}</th>
                    @endforeach
                    <th class="p-2 border-b text-center dark:border-gray-600">STS</th>
                    <th class="p-2 border-b text-center dark:border-gray-600">SAS</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                @foreach($siswas as $siswa)
                <tr class="hover:bg-surface-hover dark:hover:bg-gray-800/50">
                    <td class="p-3 border-r dark:border-gray-600 sticky left-0 bg-surface font-medium">
                        {{ $siswa->nama }}
                    </td>
                    
                    <!-- Formatif -->
                    @foreach($tps as $tp)
                    <td class="p-2 border-r dark:border-gray-600 text-center">
                        <input type="number" min="0" max="100" wire:model="formatifData.{{ $siswa->id }}.{{ $tp->id }}" class="w-16 md:w-20 text-center border border-border dark:border-gray-600 rounded px-2 py-1 bg-surface focus:outline-none focus:border-google-blue focus:ring-1 focus:ring-google-blue">
                    </td>
                    @endforeach
                    
                    <!-- Sumatif -->
                    <td class="p-2 text-center bg-yellow-50/30 dark:bg-yellow-900/10">
                        <input type="number" min="0" max="100" wire:model="sumatifData.{{ $siswa->id }}.sts" class="w-16 md:w-20 text-center border border-border dark:border-gray-600 rounded px-2 py-1 bg-surface focus:outline-none focus:border-google-yellow focus:ring-1 focus:ring-google-yellow">
                    </td>
                    <td class="p-2 text-center bg-yellow-50/30 dark:bg-yellow-900/10">
                        <input type="number" min="0" max="100" wire:model="sumatifData.{{ $siswa->id }}.sas" class="w-16 md:w-20 text-center border border-border dark:border-gray-600 rounded px-2 py-1 bg-surface focus:outline-none focus:border-google-yellow focus:ring-1 focus:ring-google-yellow">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endif

    @if($activeTab === 'rekap')
    <div class="card mb-6 overflow-x-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Rekapitulasi Nilai Akhir & Capaian</h3>
            <button wire:click="calculateRekap" class="btn btn-secondary">Hitung Ulang</button>
        </div>

        <table class="w-full text-sm text-left border border-border">
            <thead class="bg-surface-hover">
                <tr>
                    <th class="p-3 border-b dark:border-gray-600">Nama Siswa</th>
                    <th class="p-3 border-b text-center dark:border-gray-600">Rata Formatif (50%)</th>
                    <th class="p-3 border-b text-center dark:border-gray-600">STS (25%)</th>
                    <th class="p-3 border-b text-center dark:border-gray-600">SAS (25%)</th>
                    <th class="p-3 border-b text-center bg-blue-50 dark:bg-blue-900/30 text-primary font-bold dark:border-gray-600">NA</th>
                    <th class="p-3 border-b w-1/3 dark:border-gray-600">Deskripsi Capaian (Auto-generated)</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                @foreach($siswas as $siswa)
                @php $rekap = $rekapData[$siswa->id] ?? null; @endphp
                <tr>
                    <td class="p-3 font-medium">{{ $siswa->nama }}</td>
                    <td class="p-3 text-center">{{ $rekap ? number_format($rekap->rata_formatif, 0) : '-' }}</td>
                    <td class="p-3 text-center">{{ $rekap ? $rekap->sts : '-' }}</td>
                    <td class="p-3 text-center">{{ $rekap ? $rekap->sas : '-' }}</td>
                    <td class="p-3 text-center bg-blue-50/50 dark:bg-blue-900/10 font-bold text-primary">
                        {{ $rekap ? number_format($rekap->nilai_akhir, 0) : '-' }}
                    </td>
                    <td class="p-3 text-xs text-text-secondary">
                        @if($rekap)
                        <div class="mb-1 text-google-green">{{ $rekap->deskripsi_tertinggi }}</div>
                        <div class="mb-2 text-google-red">{{ $rekap->deskripsi_terendah }}</div>
                        
                        <details>
                            <summary class="cursor-pointer text-primary font-medium mb-1">Edit Manual (Override)</summary>
                            <textarea 
                                class="w-full border border-border dark:border-gray-600 bg-surface p-2 rounded" 
                                rows="3" 
                                placeholder="Ketik deskripsi manual di sini untuk menimpa deskripsi otomatis..."
                                wire:blur="updateManualDeskripsi({{ $siswa->id }}, $event.target.value)"
                            >{{ $rekap->deskripsi_manual }}</textarea>
                            <div class="text-[10px] text-text-secondary mt-1">Otomatis tersimpan saat kursor berpindah (blur).</div>
                        </details>
                        @else
                        <span class="italic text-gray-400">Belum dihitung</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
