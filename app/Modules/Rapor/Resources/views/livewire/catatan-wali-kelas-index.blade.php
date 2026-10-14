<div class="space-y-6">
    <!-- Header Halaman -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-surface p-6 rounded-2xl border border-border shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                <span>✍️ Submodul Catatan Perkembangan</span>
                <span>&bull;</span>
                <span class="px-2 py-0.5 rounded-full bg-primary/10 text-primary">
                    {{ $currentPeriode ? $currentPeriode->nama . ' (' . $currentPeriode->sebutan . ')' : 'Rapor Semester Penuh' }}
                </span>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">Catatan Wali Kelas / Rayon</h1>
            <p class="text-xs text-text-secondary mt-1">
                Isi narasi catatan perkembangan karakter dan motivasi belajar peserta didik untuk dicetak pada lembar rapor.
            </p>
        </div>

        <!-- Tombol Simpan Teratas -->
        <div class="flex items-center gap-2">
            <button type="button"
                    wire:click="saveCatatan"
                    wire:loading.attr="disabled"
                    class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-all flex items-center gap-2 cursor-pointer">
                <svg wire:loading.remove wire:target="saveCatatan" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                <svg wire:loading wire:target="saveCatatan" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Simpan Seluruh Catatan</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if(session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" class="text-xs hover:underline cursor-pointer" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <!-- Panel Filter & Pengaturan -->
    <div class="bg-surface p-5 rounded-2xl border border-border space-y-4 shadow-xs">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <!-- 1. Periode Rapor Target -->
            <div class="md:col-span-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1.5">
                    📅 Periode Rapor Target
                </label>
                <select wire:model.live="selectedPeriodeId" class="w-full text-xs font-semibold rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                    <option value="">🏛️ Catatan Rapor Semester Penuh (Pemerintah)</option>
                    @foreach($this->availablePeriodes as $p)
                        <option value="{{ $p->id }}">📘 {{ $p->nama }} ({{ $p->sebutan }})</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Mode Filter: Rombel / Rayon -->
            <div class="md:col-span-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1.5">
                    🎯 Kelompokkan Siswa
                </label>
                <div class="grid grid-cols-2 gap-1 p-1 bg-surface-hover rounded-xl border border-border text-xs">
                    <button type="button"
                            wire:click="$set('filterScope', 'rombel')"
                            class="py-1.5 rounded-lg font-bold transition-all text-center {{ $filterScope === 'rombel' ? 'bg-surface text-primary shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                        👥 By Rombel (Wali Kelas)
                    </button>
                    <button type="button"
                            wire:click="$set('filterScope', 'rayon')"
                            class="py-1.5 rounded-lg font-bold transition-all text-center {{ $filterScope === 'rayon' ? 'bg-surface text-primary shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                        🧭 By Rayon (Pembimbing)
                    </button>
                </div>
            </div>

            <!-- 3. Target Dropdown -->
            <div class="md:col-span-4">
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
                Mengelola catatan untuk <strong class="text-text-main">{{ $siswas->count() }}</strong> peserta didik
                @if($filterScope === 'rayon' && $currentRayon)
                    Rayon <strong class="text-primary">{{ $currentRayon->nama }}</strong>
                @elseif($currentRombel)
                    Kelas <strong class="text-primary">{{ $currentRombel->nama }}</strong>
                @endif
            </div>
        </div>
    </div>

    <!-- Daftar Formulir Input Catatan Siswa -->
    <div class="space-y-4">
        @forelse($siswas as $idx => $siswa)
            <div class="bg-surface p-5 rounded-2xl border border-border shadow-xs hover:border-primary/40 transition-colors">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                    
                    <!-- Info Siswa (Kiri) -->
                    <div class="w-full md:w-72 shrink-0 space-y-1.5">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-surface-hover flex items-center justify-center font-mono text-[11px] font-bold text-text-secondary shrink-0">
                                {{ $idx + 1 }}
                            </span>
                            <div class="font-bold text-sm text-text-main truncate" title="{{ $siswa->nama }}">
                                {{ $siswa->nama }}
                            </div>
                        </div>

                        <div class="text-xs font-mono text-text-secondary pl-8">
                            {{ $siswa->nisn ?? '-' }} / {{ $siswa->nipd ?? '-' }}
                        </div>

                        <div class="pl-8 flex flex-wrap gap-1.5 pt-1">
                            <span class="px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-semibold text-[10px]">
                                {{ $siswa->rombel->nama ?? '-' }}
                            </span>
                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-semibold text-[10px]">
                                {{ $siswa->rayon->nama ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <!-- Textarea & Template Cepat (Kanan) -->
                    <div class="flex-1 w-full space-y-2">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <label class="text-[11px] font-bold uppercase tracking-wider text-text-secondary flex items-center gap-1.5">
                                <span>Narasi Catatan untuk {{ $currentPeriode ? $currentPeriode->sebutan : 'Rapor Semester' }}:</span>
                            </label>
                            
                            <!-- Template Pills -->
                            <div class="flex flex-wrap items-center gap-1 text-[10px]">
                                <span class="text-text-muted">Template cepat:</span>
                                <button type="button"
                                        wire:click="applyTemplate({{ $siswa->id }}, 'sangat_baik')"
                                        class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 font-semibold transition-colors cursor-pointer"
                                        title="Sangat Baik & Berprestasi">
                                    🌟 Berprestasi
                                </button>
                                <button type="button"
                                        wire:click="applyTemplate({{ $siswa->id }}, 'pemantapan')"
                                        class="px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 hover:bg-blue-100 font-semibold transition-colors cursor-pointer"
                                        title="Proaktif & Pemantapan">
                                    📖 Pemantapan
                                </button>
                                <button type="button"
                                        wire:click="applyTemplate({{ $siswa->id }}, 'perlu_bimbingan')"
                                        class="px-2 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 hover:bg-amber-100 font-semibold transition-colors cursor-pointer"
                                        title="Perlu Bimbingan & Fokus">
                                    🤝 Bimbingan
                                </button>
                                <button type="button"
                                        wire:click="applyTemplate({{ $siswa->id }}, 'kedisiplinan')"
                                        class="px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 hover:bg-purple-100 font-semibold transition-colors cursor-pointer"
                                        title="Tingkatkan Kedisiplinan">
                                    ⏰ Kedisiplinan
                                </button>
                            </div>
                        </div>

                        <textarea
                            wire:model.defer="catatans.{{ $siswa->id }}"
                            rows="2"
                            placeholder="Tuliskan catatan motivasi dan perkembangan karakter peserta didik..."
                            class="w-full text-xs p-2.5 rounded-xl border border-border bg-surface text-text-main focus:ring-2 focus:ring-primary focus:border-primary leading-relaxed font-sans resize-y"
                        ></textarea>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-surface p-12 rounded-2xl border border-border text-center text-text-secondary">
                <div class="max-w-xs mx-auto space-y-2">
                    <svg class="w-10 h-10 mx-auto text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p class="font-bold text-text-main text-xs">Tidak ada data siswa ditemukan</p>
                    <p class="text-[11px]">Silakan pilih rombel atau rayon lain yang memiliki anggota siswa aktif.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Tombol Simpan Bawah -->
    @if($siswas->isNotEmpty())
        <div class="sticky bottom-6 z-20 flex justify-end p-4 bg-surface/90 backdrop-blur-md rounded-2xl border border-border shadow-lg">
            <button type="button"
                    wire:click="saveCatatan"
                    wire:loading.attr="disabled"
                    class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md transition-all flex items-center gap-2 cursor-pointer">
                <svg wire:loading.remove wire:target="saveCatatan" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                <svg wire:loading wire:target="saveCatatan" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Simpan Seluruh Catatan</span>
            </button>
        </div>
    @endif
</div>
