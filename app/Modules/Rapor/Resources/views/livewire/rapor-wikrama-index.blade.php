<div class="space-y-6">
    <!-- Header Halaman -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-surface p-6 rounded-2xl border border-border shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                <span>📘 Submodul Rapor Wikrama</span>
                <span>&bull;</span>
                <span class="px-2 py-0.5 rounded-full bg-primary/10 text-primary">{{ $periodMeta['label'] }}</span>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">Rapor Wikrama (9 Minggu)</h1>
            <p class="text-xs text-text-secondary mt-1">
                Laporan periodik hasil belajar dengan rincian nilai Asesmen Harian, Sumatif, dan capaian kompetensi.
            </p>
        </div>

        <!-- Tombol Aksi Massal -->
        <div class="flex items-center gap-2">
            @php
                $targetId = $filterScope === 'rayon' ? $selectedRayonId : $selectedRombelId;
            @endphp
            @if($targetId && $siswas->isNotEmpty())
                <a href="{{ route('rapor.cetak-massal.wikrama', ['scope' => $filterScope, 'target_id' => $targetId, 'periode_id' => $selectedPeriodeId, 'tanggal' => $printTanggal]) }}"
                   target="_blank"
                   class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Massal 1 {{ $filterScope === 'rayon' ? 'Rayon' : 'Rombel' }} ({{ $siswas->count() }} Siswa)</span>
                </a>
            @endif
        </div>
    </div>

    <!-- KPI Summary Cards Terintegrasi -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Siswa -->
        <div class="bg-surface p-4 rounded-2xl border border-border shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 flex items-center justify-center font-bold text-lg">
                👥
            </div>
            <div>
                <div class="text-[11px] uppercase font-bold text-text-secondary tracking-wider">Total Siswa</div>
                <div class="text-xl font-black text-text-main">{{ $siswas->count() }} <span class="text-xs font-normal text-text-secondary">peserta didik</span></div>
            </div>
        </div>

        <!-- 2. Terdaftar Seni Budaya -->
        <div class="bg-surface p-4 rounded-2xl border border-border shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 flex items-center justify-center font-bold text-lg">
                🎨
            </div>
            <div>
                <div class="text-[11px] uppercase font-bold text-text-secondary tracking-wider">Cabang Seni Budaya</div>
                <div class="text-xl font-black text-purple-700 dark:text-purple-300">
                    {{ $seniBudayaMap->count() }} / {{ $siswas->count() }}
                    <span class="text-xs font-normal text-text-secondary">terdaftar</span>
                </div>
            </div>
        </div>

        <!-- 3. Periode & Komponen Rapor -->
        <div class="bg-surface p-4 rounded-2xl border border-border shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                📅
            </div>
            <div>
                <div class="text-[11px] uppercase font-bold text-text-secondary tracking-wider">Periode Rapor</div>
                <div class="text-sm font-black text-text-main">{{ $periodMeta['sebutan'] }}</div>
                <div class="text-[10px] text-text-secondary">{{ implode(' & ', $periodMeta['ah_keys']) }} + {{ $periodMeta['sumatif_label'] }}</div>
            </div>
        </div>

        <!-- 4. Validasi KKM & Kelulusan -->
        <div class="bg-surface p-4 rounded-2xl border border-border shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 flex items-center justify-center font-bold text-lg">
                ⚖️
            </div>
            <div>
                <div class="text-[11px] uppercase font-bold text-text-secondary tracking-wider">Regulasi KKM</div>
                <div class="text-sm font-black text-text-main">Nilai Utuh</div>
                <div class="text-[10px] text-text-secondary">Kosong (-) jika belum tuntas/KKM</div>
            </div>
        </div>
    </div>

    <!-- Panel Filter & Pengaturan -->
    <div class="bg-surface p-5 rounded-2xl border border-border space-y-4 shadow-xs">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <!-- 1. Periode Rapor 9M -->
            <div class="md:col-span-3">
                <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1.5">
                    📅 Periode Rapor (9 Minggu)
                </label>
                <select wire:model.live="selectedPeriodeId" class="w-full text-xs font-semibold rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                    @foreach($this->availablePeriodes as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->sebutan }})</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Mode Filter: Rombel / Rayon -->
            <div class="md:col-span-3">
                <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1.5">
                    🎯 Kelompokkan Siswa
                </label>
                <div class="grid grid-cols-2 gap-1 p-1 bg-surface-hover rounded-xl border border-border text-xs">
                    <button type="button"
                            wire:click="$set('filterScope', 'rombel')"
                            class="py-1.5 rounded-lg font-bold transition-all text-center {{ $filterScope === 'rombel' ? 'bg-surface text-primary shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                        👥 By Rombel
                    </button>
                    <button type="button"
                            wire:click="$set('filterScope', 'rayon')"
                            class="py-1.5 rounded-lg font-bold transition-all text-center {{ $filterScope === 'rayon' ? 'bg-surface text-primary shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                        🧭 By Rayon
                    </button>
                </div>
            </div>

            <!-- 3. Dropdown Target (Rombel atau Rayon) -->
            <div class="md:col-span-3">
                @if($filterScope === 'rayon')
                    <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1.5">
                        🧭 Pilih Rayon
                    </label>
                    <select wire:model.live="selectedRayonId" class="w-full text-xs font-semibold rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                        @foreach($this->availableRayons as $ray)
                            <option value="{{ $ray->id }}">
                                {{ $ray->nama }} {{ $ray->pembimbing ? '— ' . ($ray->pembimbing->nama_lengkap ?? $ray->pembimbing->name) : '' }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1.5">
                        👥 Pilih Rombel
                    </label>
                    <select wire:model.live="selectedRombelId" class="w-full text-xs font-semibold rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                        @foreach($this->availableRombels as $rom)
                            <option value="{{ $rom->id }}">{{ $rom->nama }} (Tingkat {{ $rom->tingkat }})</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- 4. Tanggal Cetak Rapor -->
            <div class="md:col-span-3">
                <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1.5">
                    🗓️ Tanggal Cetak Rapor
                </label>
                <input type="date" wire:model.live="printTanggal" class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary font-mono">
            </div>
        </div>

        <!-- Bar Pencarian Cepat Siswa -->
        <div class="pt-3 border-t border-border flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="relative w-full sm:w-80">
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari nama atau NIS siswa..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl text-xs border border-border bg-surface text-text-main focus:ring-2 focus:ring-primary focus:border-primary">
                <svg class="w-4 h-4 text-text-secondary absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div class="text-xs text-text-secondary font-medium">
                Menampilkan <strong class="text-text-main">{{ $siswas->count() }}</strong> peserta didik
                @if($filterScope === 'rayon' && $currentRayon)
                    di Rayon <strong class="text-primary">{{ $currentRayon->nama }}</strong>
                @elseif($currentRombel)
                    di Kelas <strong class="text-primary">{{ $currentRombel->nama }}</strong>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Siswa Rapor Wikrama -->
    <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-surface-hover/70 border-b border-border text-text-secondary uppercase text-[10px] font-bold tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">NISN / NIS</th>
                        <th class="py-3 px-4">{{ $filterScope === 'rayon' ? 'Kelas / Rombel' : 'Rayon' }}</th>
                        <th class="py-3 px-4">Pembimbing Rayon</th>
                        <th class="py-3 px-4">Cabang Seni Budaya</th>
                        <th class="py-3 px-4 text-center w-52">Aksi Cetak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-text-main font-medium">
                    @forelse($siswas as $idx => $siswa)
                        <tr class="hover:bg-surface-hover/50 transition-colors">
                            <td class="py-3 px-4 text-center font-mono text-text-secondary">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-text-main text-xs">{{ $siswa->nama }}</div>
                                <div class="text-[10px] text-text-secondary">{{ $siswa->rombel?->jurusan?->nama ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 font-mono text-[11px] text-text-secondary">
                                {{ $siswa->nisn ?? '-' }} / {{ $siswa->nipd ?? '-' }}
                            </td>
                            <td class="py-3 px-4">
                                @if($filterScope === 'rayon')
                                    <span class="px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-semibold text-[11px]">
                                        {{ $siswa->rombel->nama ?? '-' }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-semibold text-[11px]">
                                        {{ $siswa->rayon->nama ?? '-' }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-text-secondary text-[11px]">
                                {{ $siswa->rayon?->pembimbing?->nama_lengkap ?? ($siswa->rayon?->pembimbing?->name ?? '-') }}
                            </td>
                            <td class="py-3 px-4">
                                @if(isset($seniBudayaMap[$siswa->id]))
                                    @php $sb = $seniBudayaMap[$siswa->id]; @endphp
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border border-purple-200/60">
                                        <span>🎨</span>
                                        <span>{{ $sb->seniBudaya->nama ?? 'Seni' }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 border border-amber-200">
                                        <span>⚠️</span>
                                        <span>Belum Terdaftar</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Tombol Pratinjau Nilai -->
                                    <button type="button"
                                            wire:click="openPreview({{ $siswa->id }})"
                                            class="px-2.5 py-1.5 rounded-lg border border-border bg-surface hover:bg-surface-hover text-text-main text-[11px] font-semibold transition-colors flex items-center gap-1"
                                            title="Lihat Pratinjau Nilai">
                                        <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Pratinjau</span>
                                    </button>

                                    <!-- Tombol Cetak PDF Wikrama -->
                                    <a href="{{ route('rapor.cetak.wikrama', ['siswa_id' => $siswa->id, 'periode_id' => $selectedPeriodeId, 'tanggal' => $printTanggal]) }}"
                                       target="_blank"
                                       class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold transition-colors flex items-center gap-1 shadow-xs"
                                       title="Cetak Lembar Rapor Wikrama">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        <span>Cetak Rapor</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-text-secondary">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <svg class="w-10 h-10 mx-auto text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <p class="font-bold text-text-main text-xs">Tidak ada data siswa ditemukan</p>
                                    <p class="text-[11px]">Silakan pilih rombel atau rayon lain yang memiliki siswa aktif.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL PRATINJAU RAPOR SISWA -->
    @if($showPreviewModal && $previewData)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closePreview"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-surface border border-border text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl text-text-main max-h-[90vh] flex flex-col" @click.stop>
                    
                    <!-- Header Modal -->
                    <div class="px-6 py-4 border-b border-border bg-surface-hover/50 flex items-center justify-between shrink-0">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary">
                                    {{ $previewData['periodMeta']['label'] }}
                                </span>
                                <h3 class="text-base font-bold text-text-main">
                                    Pratinjau Rapor: {{ $previewData['siswa']->nama }}
                                </h3>
                            </div>
                            <p class="text-xs text-text-secondary mt-0.5">
                                Kelas: {{ $previewData['siswa']->rombel->nama ?? '-' }} &bull; Rayon: {{ $previewData['siswa']->rayon->nama ?? '-' }}
                            </p>
                        </div>
                        <button type="button" wire:click="closePreview" class="text-text-muted hover:text-text-main text-lg font-bold p-1 cursor-pointer">
                            &times;
                        </button>
                    </div>

                    <!-- Body Modal (Scrollable) -->
                    <div class="p-6 space-y-5 overflow-y-auto text-xs">
                        <!-- Alert Status Belum Tuntas / Di Bawah KKM -->
                        @if(($previewData['incompleteCount'] ?? 0) > 0 || ($previewData['belowKkmCount'] ?? 0) > 0)
                            <div class="p-3.5 rounded-xl border border-amber-200 bg-amber-50/70 dark:bg-amber-950/30 text-amber-900 dark:text-amber-200 flex items-start gap-3 text-xs shadow-xs">
                                <span class="text-base shrink-0">⚠️</span>
                                <div class="space-y-0.5">
                                    <p class="font-bold">Perhatian: Status Nilai Peserta Didik Belum Tuntas</p>
                                    <p class="text-[11px] leading-relaxed text-amber-800 dark:text-amber-300">
                                        Terdapat <strong>{{ $previewData['incompleteCount'] ?? 0 }}</strong> mata pelajaran dengan nilai belum lengkap, dan <strong>{{ $previewData['belowKkmCount'] ?? 0 }}</strong> mata pelajaran di bawah KKM. Sesuai ketentuan Kurikulum Merdeka Wikrama, Nilai Akhir (NA) otomatis ditahan (tampil <strong>-</strong>) dan deskripsi diarahkan untuk menghubungi guru pengampu.
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- Tabel Nilai Akademik -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-bold text-xs uppercase tracking-wider text-text-secondary">
                                    📊 A. Nilai Akademik (Rincian Hasil Belajar)
                                </h4>
                                <span class="text-[11px] text-text-muted">
                                    Format: Formatif ({{ implode(', ', $previewData['periodMeta']['ah_keys']) }}) &bull; Sumatif ({{ $previewData['periodMeta']['sumatif_label'] }})
                                </span>
                            </div>
                            <div class="border border-border rounded-xl overflow-hidden shadow-xs">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-surface-hover text-text-secondary text-[11px] font-bold border-b border-border">
                                            <th class="p-2.5 w-8 text-center">No</th>
                                            <th class="p-2.5">Mata Pelajaran</th>
                                            <th class="p-2.5 text-center w-12 font-mono">KKM</th>
                                            @foreach($previewData['periodMeta']['ah_keys'] as $ah)
                                                <th class="p-2.5 text-center w-14 font-mono">{{ $ah }}</th>
                                            @endforeach
                                            <th class="p-2.5 text-center w-16 font-mono">{{ $previewData['periodMeta']['sumatif_label'] }}</th>
                                            <th class="p-2.5 text-center w-16 font-mono font-bold">NA</th>
                                            <th class="p-2.5">Capaian Kompetensi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-border">
                                        @foreach($previewData['groupedAcademic'] as $namaKelompok => $items)
                                            <tr class="bg-surface-hover/60 font-bold text-[11px]">
                                                <td colspan="{{ 5 + count($previewData['periodMeta']['ah_keys']) }}" class="p-2 px-3 text-text-main">
                                                    {{ $namaKelompok }}
                                                </td>
                                            </tr>
                                            @foreach($items as $idxItem => $row)
                                                <tr class="hover:bg-surface-hover/30 {{ $row->is_seni ? 'bg-purple-50/20 dark:bg-purple-950/20' : '' }}">
                                                    <td class="p-2.5 text-center text-text-secondary font-mono">{{ $idxItem + 1 }}</td>
                                                    <td class="p-2.5">
                                                        <div class="font-semibold text-text-main flex items-center gap-1.5">
                                                            @if($row->is_seni)
                                                                <span class="text-purple-600">🎨</span>
                                                                <span>{{ $row->mapel_nama }}</span>
                                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-purple-100 dark:bg-purple-900/60 text-purple-700 dark:text-purple-300">
                                                                    Seni Budaya
                                                                </span>
                                                            @elseif(!empty($row->is_mapil))
                                                                <span class="text-emerald-600">🎯</span>
                                                                <span>{{ $row->mapel_nama }}</span>
                                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300">
                                                                    Mapil
                                                                </span>
                                                            @else
                                                                <span>{{ $row->mapel_nama }}</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="p-2.5 text-center font-mono text-[11px] text-text-muted">
                                                        {{ (int) ($row->kkm ?? 75) }}
                                                    </td>
                                                    @foreach($previewData['periodMeta']['ah_keys'] as $ah)
                                                        @php
                                                            $ahVal = isset($row->ah_scores[$ah]) ? $row->ah_scores[$ah] : null;
                                                            $isAhBelow = $ahVal !== null && $ahVal < ($row->kkm ?? 75);
                                                        @endphp
                                                        <td class="p-2.5 text-center font-mono {{ $isAhBelow ? 'text-rose-600 font-bold bg-rose-50/50' : 'text-text-main' }}">
                                                            {{ $ahVal !== null ? number_format($ahVal, 0) : '-' }}
                                                        </td>
                                                    @endforeach
                                                    @php
                                                        $sumVal = $row->sumatif;
                                                        $isSumBelow = $sumVal !== null && $sumVal < ($row->kkm ?? 75);
                                                    @endphp
                                                    <td class="p-2.5 text-center font-mono {{ $isSumBelow ? 'text-rose-600 font-bold bg-rose-50/50' : 'text-text-main' }}">
                                                        {{ $sumVal !== null ? number_format($sumVal, 0) : '-' }}
                                                    </td>
                                                    <td class="p-2.5 text-center font-mono font-bold">
                                                        @if($row->nilai_akhir !== null)
                                                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold">
                                                                {{ number_format($row->nilai_akhir, 0) }}
                                                            </span>
                                                        @else
                                                            <span class="px-2 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/60 text-rose-600 font-bold">
                                                                -
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="p-2.5 text-[11px] leading-relaxed">
                                                        @if($row->status === 'belum_lengkap')
                                                            <span class="text-amber-700 dark:text-amber-400 font-medium">
                                                                ⚠️ {{ $row->deskripsi_tertinggi }}
                                                            </span>
                                                        @elseif($row->status === 'belum_kompeten')
                                                            <span class="text-rose-700 dark:text-rose-400 font-medium">
                                                                ❌ {{ $row->deskripsi_tertinggi }}
                                                            </span>
                                                        @elseif($row->deskripsi_manual)
                                                            <span class="text-amber-700 font-semibold">[Manual]</span> {{ $row->deskripsi_manual }}
                                                        @else
                                                            <div class="text-text-main font-medium">{{ $row->deskripsi_tertinggi }}</div>
                                                            @if(!empty($row->deskripsi_terendah))
                                                                <div class="mt-0.5 text-text-muted text-[10px]">{{ $row->deskripsi_terendah }}</div>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Rekap Absensi & Catatan Pembimbing Rayon -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Absensi -->
                            <div class="p-4 rounded-xl border border-border bg-surface-hover/20">
                                <h4 class="font-bold text-xs uppercase tracking-wider text-text-secondary mb-2">
                                    🗓️ Ketidakhadiran
                                </h4>
                                <div class="grid grid-cols-3 gap-2 text-center font-mono">
                                    <div class="p-2 rounded-lg bg-surface border border-border">
                                        <div class="text-[10px] text-text-secondary">Sakit</div>
                                        <div class="text-sm font-bold text-text-main">{{ $previewData['rekapAbsen']['sakit'] }} hr</div>
                                    </div>
                                    <div class="p-2 rounded-lg bg-surface border border-border">
                                        <div class="text-[10px] text-text-secondary">Izin</div>
                                        <div class="text-sm font-bold text-text-main">{{ $previewData['rekapAbsen']['izin'] }} hr</div>
                                    </div>
                                    <div class="p-2 rounded-lg bg-surface border border-border">
                                        <div class="text-[10px] text-text-secondary">Alpa</div>
                                        <div class="text-sm font-bold text-rose-600">{{ $previewData['rekapAbsen']['alpa'] }} hr</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Catatan Pembimbing Rayon -->
                            <div class="p-4 rounded-xl border border-border bg-surface-hover/20">
                                <h4 class="font-bold text-xs uppercase tracking-wider text-text-secondary mb-2">
                                    ✍️ Catatan Pembimbing Rayon
                                </h4>
                                <p class="text-xs text-text-main leading-relaxed italic bg-surface p-2.5 rounded-lg border border-border">
                                    {{ $previewData['catatan'] ?: 'Belum ada catatan khusus untuk periode ini.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Modal -->
                    <div class="px-6 py-3.5 border-t border-border bg-surface-hover/40 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                        <!-- Quick Shortcuts to Modules -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('penilaian.index') }}" target="_blank" class="px-3 py-1.5 rounded-xl border border-border bg-surface hover:bg-surface-hover text-xs font-semibold text-primary flex items-center gap-1.5 transition-colors">
                                <span>📝</span>
                                <span>Input Nilai Mapel</span>
                            </a>
                            <a href="{{ route('seni-budaya.index') }}" target="_blank" class="px-3 py-1.5 rounded-xl border border-border bg-surface hover:bg-surface-hover text-xs font-semibold text-purple-700 dark:text-purple-300 flex items-center gap-1.5 transition-colors">
                                <span>🎨</span>
                                <span>Modul Seni Budaya</span>
                            </a>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="closePreview" class="px-3.5 py-2 rounded-xl border border-border bg-surface hover:bg-surface-hover text-xs font-semibold transition-colors">
                                Tutup
                            </button>
                            <a href="{{ route('rapor.cetak.wikrama', ['siswa_id' => $previewData['siswa']->id, 'periode_id' => $selectedPeriodeId, 'tanggal' => $printTanggal]) }}"
                               target="_blank"
                               class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs flex items-center gap-1.5 transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                <span>Buka Dokumen PDF</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
