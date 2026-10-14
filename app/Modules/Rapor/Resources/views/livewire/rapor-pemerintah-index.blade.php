<div class="space-y-6">
    <!-- Header Halaman -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-surface p-6 rounded-2xl border border-border shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                <span>🏛️ Submodul Rapor Pemerintah</span>
                <span>&bull;</span>
                <span class="px-2 py-0.5 rounded-full bg-primary/10 text-primary">Standar Kemdikbudristek</span>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">Rapor Pemerintah (Semester)</h1>
            <p class="text-xs text-text-secondary mt-1">
                Laporan resmi capaian hasil belajar akhir semester Kurikulum Merdeka (Nilai Akhir dan Capaian Kompetensi).
            </p>
        </div>

        <!-- Tombol Aksi Massal -->
        <div class="flex items-center gap-2">
            @php
                $targetId = $filterScope === 'rayon' ? $selectedRayonId : $selectedRombelId;
            @endphp
            @if($targetId && $siswas->isNotEmpty())
                <a href="{{ route('rapor.cetak-massal.pemerintah', ['scope' => $filterScope, 'target_id' => $targetId, 'semester_id' => $selectedSemesterId, 'tanggal' => $printTanggal]) }}"
                   target="_blank"
                   class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Massal Nilai ({{ $siswas->count() }} Siswa)</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Panel Filter & Pengaturan -->
    <div class="bg-surface p-5 rounded-2xl border border-border space-y-4 shadow-xs">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <!-- 1. Pilihan Semester -->
            <div class="md:col-span-3">
                <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1.5">
                    📅 Semester
                </label>
                <select wire:model.live="selectedSemesterId" class="w-full text-xs font-semibold rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                    @foreach($this->availableSemesters as $sem)
                        <option value="{{ $sem->id }}">Semester {{ ucfirst($sem->semester) }} ({{ $sem->tahunAjaran->nama ?? '' }})</option>
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

            <!-- 3. Target Dropdown -->
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

    <!-- Tabel Daftar Siswa Rapor Pemerintah -->
    <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-surface-hover/70 border-b border-border text-text-secondary uppercase text-[10px] font-bold tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">NISN / NIS</th>
                        <th class="py-3 px-4">{{ $filterScope === 'rayon' ? 'Kelas / Rombel' : 'Rayon' }}</th>
                        <th class="py-3 px-4">Wali Kelas</th>
                        <th class="py-3 px-4 text-center w-60">Opsi Cetak Berkas</th>
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
                                {{ $siswa->rombel?->waliKelas?->nama_lengkap ?? ($siswa->rombel?->waliKelas?->name ?? '-') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Cetak Nilai Semester -->
                                    <a href="{{ route('rapor.cetak.pemerintah', ['siswa_id' => $siswa->id, 'semester_id' => $selectedSemesterId, 'tanggal' => $printTanggal]) }}"
                                       target="_blank"
                                       class="px-2.5 py-1.5 rounded-lg bg-primary hover:bg-primary-hover text-white text-[11px] font-semibold transition-colors flex items-center gap-1 shadow-xs"
                                       title="Cetak Lembar Nilai Semester">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>Nilai</span>
                                    </a>

                                    <!-- Tombol Pilihan Berkas Lainnya -->
                                    <button type="button"
                                            wire:click="openPrintModal({{ $siswa->id }})"
                                            class="px-2.5 py-1.5 rounded-lg border border-border bg-surface hover:bg-surface-hover text-text-main text-[11px] font-semibold transition-colors flex items-center gap-1">
                                        <span>Berkas Lain...</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-text-secondary">
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

    <!-- MODAL OPSI CETAK BERKAS RAPOR LAINNYA -->
    @if($showPrintModal && $this->siswaPrint)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closePrintModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-surface border border-border text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md text-text-main" @click.stop>
                    
                    <!-- Header Modal -->
                    <div class="px-6 py-4 border-b border-border bg-surface-hover/50 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-text-main">
                                Cetak Berkas Rapor Pemerintah
                            </h3>
                            <p class="text-xs text-text-secondary mt-0.5">
                                {{ $this->siswaPrint->nama }} ({{ $this->siswaPrint->rombel->nama ?? '-' }})
                            </p>
                        </div>
                        <button type="button" wire:click="closePrintModal" class="text-text-muted hover:text-text-main text-lg font-bold p-1 cursor-pointer">
                            &times;
                        </button>
                    </div>

                    <!-- Body Modal -->
                    <div class="p-6 space-y-3 text-xs">
                        <p class="text-text-secondary text-[11px] mb-2">
                            Pilih dokumen kelengkapan Rapor Kurikulum Merdeka yang ingin diunduh atau dicetak:
                        </p>

                        <!-- Pilihan 1: Cover Depan -->
                        <a href="{{ route('rapor.cetak.cover', ['siswa_id' => $this->siswaPrint->id]) }}"
                           target="_blank"
                           class="flex items-center justify-between p-3 rounded-xl border border-border bg-surface hover:bg-surface-hover transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold">
                                    📕
                                </div>
                                <div>
                                    <div class="font-bold text-text-main group-hover:text-primary">Cover Depan Rapor</div>
                                    <div class="text-[10px] text-text-secondary">Sampul depan resmi dengan logo sekolah & nama siswa</div>
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-primary">Cetak &rarr;</span>
                        </a>

                        <!-- Pilihan 2: Lembar Identitas Siswa -->
                        <a href="{{ route('rapor.cetak.identitas', ['siswa_id' => $this->siswaPrint->id]) }}"
                           target="_blank"
                           class="flex items-center justify-between p-3 rounded-xl border border-border bg-surface hover:bg-surface-hover transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold">
                                    📋
                                </div>
                                <div>
                                    <div class="font-bold text-text-main group-hover:text-emerald-600">Lembar Identitas Siswa</div>
                                    <div class="text-[10px] text-text-secondary">Data diri lengkap siswa dan orang tua / wali</div>
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-emerald-600">Cetak &rarr;</span>
                        </a>

                        <!-- Pilihan 3: Lembar Nilai Akademik Semester -->
                        <a href="{{ route('rapor.cetak.pemerintah', ['siswa_id' => $this->siswaPrint->id, 'semester_id' => $selectedSemesterId, 'tanggal' => $printTanggal]) }}"
                           target="_blank"
                           class="flex items-center justify-between p-3 rounded-xl border border-border bg-surface hover:bg-surface-hover transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold">
                                    📊
                                </div>
                                <div>
                                    <div class="font-bold text-text-main group-hover:text-blue-600">Lembar Nilai Semester</div>
                                    <div class="text-[10px] text-text-secondary">Nilai akhir, capaian kompetensi, absensi, & ekskul</div>
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-blue-600">Cetak &rarr;</span>
                        </a>
                    </div>

                    <!-- Footer Modal -->
                    <div class="px-6 py-3 border-t border-border bg-surface-hover/40 flex justify-end">
                        <button type="button" wire:click="closePrintModal" class="px-3.5 py-1.5 rounded-xl border border-border bg-surface hover:bg-surface-hover text-xs font-semibold">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
