<div class="space-y-6 pb-28 sm:pb-12">
    <style>
        .mutabaah-ayat-grid {
            display: grid !important;
            grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
            gap: 0.5rem !important;
        }
        @media (min-width: 640px) {
            .mutabaah-ayat-grid {
                grid-template-columns: repeat(10, minmax(0, 1fr)) !important;
                gap: 0.375rem !important;
            }
        }
        @media (min-width: 1024px) {
            .mutabaah-ayat-grid {
                grid-template-columns: repeat(20, minmax(0, 1fr)) !important;
                gap: 0.375rem !important;
            }
        }
    </style>

    <!-- Header Navigasi Sub-Modul Kepesantrenan -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-border pb-4">
        <div>
            <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
                <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Input Hafalan Qur'an Santri
            </h2>
            <p class="text-sm text-text-secondary mt-1">
                Lembar mutaba'ah digital mobile-first. 5 kolom di HP, proteksi sentuhan anti-batal, dan navigasi 30 Juz lengkap.
            </p>
        </div>

        <!-- Tab Navigasi Submodul -->
        <div class="flex items-center gap-1.5 bg-surface-hover p-1.5 rounded-xl border border-border overflow-x-auto max-w-full">
            <a href="{{ route('pesantren.kamar') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface whitespace-nowrap">
                Kamar & Asrama
            </a>
            <a href="{{ route('pesantren.hafalan') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 shadow-xs whitespace-nowrap">
                Input Hafalan
            </a>
            <a href="{{ route('pesantren.rekap') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface whitespace-nowrap">
                Rekap & Rank MLBB
            </a>
            <a href="{{ route('pesantren.halaqoh') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface whitespace-nowrap">
                Halaqoh & Presensi
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif
    @if(session()->has('info'))
        <div class="p-4 bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 rounded-xl text-sky-800 dark:text-sky-200 text-sm flex items-center justify-between">
            <span>{{ session('info') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-sky-500 hover:text-sky-700">&times;</button>
        </div>
    @endif
    @if(session()->has('warning'))
        <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl text-amber-800 dark:text-amber-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span>⚠️</span>
                <span>{{ session('warning') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-700">&times;</button>
        </div>
    @endif

    <!-- Selector Santri Autocomplete Langsung & Kartu Profil -->
    <div class="bg-surface rounded-2xl border border-border p-4 sm:p-5 shadow-xs space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
            <!-- Autocomplete Pencarian Santri -->
            <div class="{{ $selectedSiswa ? 'md:col-span-6' : 'col-span-full' }} space-y-2 relative">
                <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary">
                    Cari & Pilih Santri Program Unggulan
                </label>

                @if($selectedSiswa)
                    <!-- Tampilan Santri Aktif Dipilih -->
                    <div class="p-3 bg-surface-hover rounded-xl border border-emerald-500/40 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 font-bold flex items-center justify-center shrink-0">
                                👤
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-sm text-text-main truncate">{{ $selectedSiswa->nama }}</div>
                                <div class="text-xs text-text-muted">
                                    {{ $selectedSiswa->rombel?->nama ?? '-' }} &bull; NISN: {{ $selectedSiswa->nisn ?? '-' }}
                                    @if($selectedSiswa->pesantrenKamars->isNotEmpty())
                                        &bull; {{ $selectedSiswa->pesantrenKamars->first()->nama_kamar }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <button type="button" wire:click="clearSiswaSelection" class="btn btn-secondary text-xs py-1.5 px-3 shrink-0 hover:text-rose-600">
                            Ganti Santri
                        </button>
                    </div>
                @else
                    <!-- Input Autocomplete Langsung (Mengetik NIS/NIPD/Nama) -->
                    <div class="relative">
                        <input type="text"
                               wire:model.live.debounce.250ms="searchSiswa"
                               placeholder="Ketik NISN, NIPD, atau Nama santri unggulan..."
                               class="input pl-9 pr-8 w-full text-sm font-medium focus:ring-2 focus:ring-emerald-500"
                               autocomplete="off">
                        <svg class="w-4 h-4 text-text-secondary absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>

                        @if(!empty($searchSiswa))
                            <button type="button" wire:click="$set('searchSiswa', '')" class="absolute right-2.5 top-2.5 text-text-muted hover:text-text-main text-sm">
                                &times;
                            </button>
                        @endif

                        <!-- Rekomendasi Melayang (Floating Suggestion Dropdown) -->
                        @if($showSiswaDropdown && $autocompleteResults->isNotEmpty())
                            <div class="absolute left-0 right-0 top-full mt-1 z-50 bg-surface rounded-xl border border-border shadow-xl overflow-hidden divide-y divide-border max-h-72 overflow-y-auto">
                                @foreach($autocompleteResults as $s)
                                    <button type="button"
                                            wire:click="selectSiswa({{ $s->id }})"
                                            class="w-full text-left p-3 hover:bg-surface-hover transition-colors flex items-center justify-between gap-3">
                                        <div>
                                            <div class="font-bold text-sm text-text-main">{{ $s->nama }}</div>
                                            <div class="text-xs text-text-muted">
                                                {{ $s->rombel?->nama ?? '-' }} &bull; NISN: {{ $s->nisn ?? '-' }}
                                                @if($s->pesantrenKamars->isNotEmpty())
                                                    &bull; {{ $s->pesantrenKamars->first()->nama_kamar }}
                                                @endif
                                            </div>
                                        </div>
                                        <span class="text-xs text-emerald-600 font-bold shrink-0">Pilih &rarr;</span>
                                    </button>
                                @endforeach
                            </div>
                        @elseif($showSiswaDropdown && strlen(trim($searchSiswa)) > 0 && $autocompleteResults->isEmpty())
                            <div class="absolute left-0 right-0 top-full mt-1 z-50 bg-surface rounded-xl border border-border shadow-xl p-4 text-center text-xs text-text-muted">
                                Tidak ada santri unggulan yang cocok dengan "{{ $searchSiswa }}".
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Kartu Stats Santri & Rank Mobile Legends -->
            @if($selectedSiswa && $stats)
                <div class="md:col-span-6 flex flex-wrap sm:flex-nowrap items-center justify-between gap-3 p-3 bg-surface-hover rounded-xl border border-border">
                    <div class="flex items-center gap-3">
                        <!-- Badge MLBB Rank -->
                        <div class="px-3 py-2 rounded-xl border font-black text-center shadow-xs {{ $stats['rank']['badge_color'] }}">
                            <div class="text-[10px] uppercase tracking-widest text-text-muted">RANK TIER</div>
                            <div class="text-sm font-extrabold tracking-wide uppercase">{{ $stats['rank']['tier'] }}</div>
                        </div>

                        <div>
                            <div class="font-bold text-sm text-text-main flex items-center gap-1.5">
                                <span>{{ $selectedSiswa->nama }}</span>
                            </div>
                            <div class="text-xs text-text-secondary mt-0.5">
                                Total: <strong class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $stats['total_ayat'] }}</strong> Ayat
                                (~<strong class="text-emerald-600 font-bold">{{ $stats['juz'] }}</strong> Juz Bebas)
                            </div>
                            <!-- Mini Progress Bar Standar 3 Juz -->
                            <div class="w-36 sm:w-44 bg-border/60 h-2 rounded-full overflow-hidden mt-1.5" title="Progress Menuju Akumulasi 3 Juz (Mythic)">
                                <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full transition-all duration-300" style="width: {{ $stats['target_persen'] }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right flex flex-col items-end">
                        <span class="text-[11px] font-bold {{ $stats['is_target_achieved'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600' }}">
                            {{ $stats['target_persen'] }}% Target
                        </span>
                        <span class="text-[10px] text-text-muted">Standar: 3 Juz (Mythic)</span>
                    </div>
                </div>
            @endif
        </div>

        @if($selectedSiswa)
            <!-- Tab Mode Tampilan & Saklar Mode Koreksi -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-t border-border pt-3">
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="$set('activeTab', 'lembar')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $activeTab === 'lembar' ? 'bg-emerald-600 text-white shadow-xs' : 'text-text-secondary hover:text-text-main bg-surface-hover' }}">
                        Lembar Kontrol Qur'an
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'riwayat')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $activeTab === 'riwayat' ? 'bg-emerald-600 text-white shadow-xs' : 'text-text-secondary hover:text-text-main bg-surface-hover' }}">
                        Riwayat Catatan Setoran
                    </button>
                </div>

                @if($activeTab === 'lembar')
                    <div class="flex items-center gap-3">
                        <!-- Saklar Mode Koreksi / Pengaman Anti-Batal Tidak Sengaja -->
                        <button type="button"
                                wire:click="toggleModeKoreksi"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border
                                {{ $modeKoreksi 
                                    ? 'bg-rose-500 text-white border-rose-600 shadow-sm animate-pulse' 
                                    : 'bg-surface hover:bg-surface-hover text-text-secondary border-border' }}"
                                title="Aktifkan saklar ini hanya jika ingin menghapus/membatalkan ayat yang sudah hijau">
                            <span class="w-2 h-2 rounded-full {{ $modeKoreksi ? 'bg-white' : 'bg-emerald-500' }}"></span>
                            <span>Mode Koreksi: {{ $modeKoreksi ? 'AKTIF (Hati-hati)' : 'Nonaktif (Aman)' }}</span>
                        </button>

                        <button type="button" wire:click="$toggle('showRangeTool')" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            <span>{{ $showRangeTool ? 'Tutup Rentang' : 'Tandai Rentang Cepat' }}</span>
                        </button>
                    </div>
                @endif
            </div>

            @if($modeKoreksi)
                <div class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs flex items-center gap-2">
                    <span>⚠️</span>
                    <span><strong>Mode Koreksi Aktif:</strong> Mengetuk kotak ayat hijau sekarang akan <strong>membatalkan/menghapus</strong> hafalan ayat tersebut dari database. Nonaktifkan saklar jika sudah selesai.</span>
                </div>
            @endif
        @endif
    </div>

    @if(! $selectedSiswa)
        <!-- Empty State ketika Santri Belum Dipilih -->
        <div class="bg-surface rounded-2xl border-2 border-dashed border-border p-10 sm:p-14 text-center space-y-4 shadow-xs">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center mx-auto text-3xl shadow-xs">
                📖
            </div>
            <div class="max-w-md mx-auto space-y-2">
                <h3 class="text-base sm:text-lg font-bold text-text-main">
                    Belum Ada Santri yang Dipilih
                </h3>
                <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                    Silakan ketik nama, NISN, atau NIPD santri program unggulan pada kolom pencarian di atas untuk membuka lembar kontrol Qur'an dan mulai mencatat progres hafalan.
                </p>
            </div>
        </div>
    @else

    @if($activeTab === 'lembar')
        <!-- Tool Rentang Ayat Cepat (Collapsible) -->
        @if($showRangeTool)
            <div class="bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 rounded-2xl p-4 animate-in fade-in duration-200">
                <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-900 dark:text-emerald-200 mb-2">
                    Tandai Rentang Ayat Sekaligus (Hemat Waktu di HP)
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <div class="sm:col-span-5">
                        <label class="block text-[11px] text-emerald-800 dark:text-emerald-300 mb-1">Pilih Surat di Juz {{ $selectedJuz }}</label>
                        <select wire:model="rangeSurahId" class="select w-full text-xs bg-white dark:bg-gray-800">
                            <option value="">-- Pilih Surat --</option>
                            @foreach($surahs as $surah)
                                <option value="{{ $surah->nomor_surah }}">{{ $surah->nomor_surah }}. {{ $surah->nama_latin }} ({{ $surah->jumlah_ayat }} ayat)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] text-emerald-800 dark:text-emerald-300 mb-1">Ayat Mulai</label>
                        <input type="number" wire:model="rangeStart" min="1" class="input w-full text-xs bg-white dark:bg-gray-800">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] text-emerald-800 dark:text-emerald-300 mb-1">Ayat Sampai</label>
                        <input type="number" wire:model="rangeEnd" min="1" class="input w-full text-xs bg-white dark:bg-gray-800">
                    </div>
                    <div class="sm:col-span-3">
                        <button type="button" wire:click="applyRange" class="btn btn-primary text-xs w-full py-2 bg-emerald-600 hover:bg-emerald-700">
                            Tandai Ayat Tersebut
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Navigasi Pemilihan Juz (Pills Horisontal Scrollable di HP dengan Indikator Tuntas) -->
        <div class="bg-surface rounded-2xl border border-border p-3 shadow-xs">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-text-secondary">Pilih Juz Al-Qur'an (1 s/d 30):</span>
                <span class="text-xs text-text-muted">Standar Kelulusan: Akumulasi 3 Juz Bebas</span>
            </div>
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1.5 scrollbar-thin">
                @for($j = 1; $j <= 30; $j++)
                    @php
                        $prog = $juzProgress[$j] ?? ['is_tuntas' => false, 'has_progress' => false, 'memorized' => 0];
                        $isTuntas = $prog['is_tuntas'];
                        $hasProgress = $prog['has_progress'];
                        $isActive = $selectedJuz === $j;
                    @endphp
                    <button type="button"
                            wire:click="changeJuz({{ $j }})"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex-shrink-0 flex items-center gap-1 border
                            {{ $isActive 
                                ? 'bg-amber-400 text-slate-900 border-amber-500 shadow-sm scale-105' 
                                : ($isTuntas 
                                    ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border-emerald-400 dark:border-emerald-700' 
                                    : ($hasProgress
                                        ? 'bg-emerald-50/60 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800'
                                        : 'bg-surface hover:bg-surface-hover text-text-secondary border-border')) }}"
                            title="Juz {{ $j }}: {{ $prog['memorized'] }} / {{ $prog['total'] ?? 0 }} ayat">
                        @if($isTuntas)
                            <span class="text-emerald-600 font-black">✓</span>
                        @elseif($hasProgress)
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        @endif
                        <span>Juz {{ $j }}</span>
                    </button>
                @endfor
            </div>
        </div>

        <!-- Lembar Kontrol Format Kertas Mutaba'ah (5 Kolom Mobile, 20 Kolom Desktop) -->
        <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
            <!-- Header Banner Kuning Juz (Sesuai Gambar Kertas) -->
            <div class="bg-[#ffeb3b] text-slate-900 text-center py-2 font-black text-base sm:text-lg tracking-wider border-b-2 border-slate-900 uppercase">
                JUZ {{ $selectedJuz }}
            </div>

            <!-- Konten Surat dalam Juz Ini -->
            <div class="divide-y-2 divide-slate-900">
                @forelse($juzSections as $sec)
                    @php
                        $surah = $surahs[$sec['surah']] ?? null;
                    @endphp
                    @if($surah)
                        <div>
                            <!-- Banner Biru Muda Nama Surat & Arti (Sesuai Gambar Kertas) -->
                            <div class="bg-[#00e5ff] dark:bg-cyan-600 text-slate-900 dark:text-white px-4 py-2 font-bold text-xs sm:text-sm tracking-wide uppercase flex items-center justify-between border-b border-slate-900">
                                <span>{{ $surah->nomor_surah }}. {{ $surah->nama_latin }} ({{ $surah->arti }}) - {{ $surah->jumlah_ayat }} AYAT</span>
                                <span class="font-serif text-sm">{{ $surah->nama_arab }}</span>
                            </div>

                            <!-- Grid Kotak Nomor Ayat: 5 Kolom di Mobile, 10 Kolom di Tablet, 20 Kolom di Desktop -->
                            <div class="p-3 sm:p-4 bg-white dark:bg-slate-900">
                                <div class="mutabaah-ayat-grid grid-cols-5 sm:grid-cols-10 md:grid-cols-20 text-center select-none">
                                    @for($ayat = $sec['start']; $ayat <= $sec['end']; $ayat++)
                                        @php
                                            $key = "{$surah->nomor_surah}_{$ayat}";
                                            $isMemorized = isset($memorizedMap[$key]);
                                            $isTempSelected = isset($tempSelectedMap[$key]);
                                            $predikat = $isMemorized ? (is_string($memorizedMap[$key]) ? $memorizedMap[$key] : 'mumtaz') : null;
                                            $predStyle = '';
                                            if ($isMemorized) {
                                                if ($predikat === 'jayyid_jiddan') {
                                                    $predStyle = 'background: #2563eb; color: #ffffff; border-color: #1d4ed8;';
                                                } elseif ($predikat === 'jayyid') {
                                                    $predStyle = 'background: #eab308; color: #0f172a; border-color: #ca8a04; font-weight: 800;';
                                                } elseif ($predikat === 'maqbul') {
                                                    $predStyle = 'background: #f97316; color: #ffffff; border-color: #c2410c;';
                                                } else { // mumtaz
                                                    $predStyle = 'background: #10b981; color: #ffffff; border-color: #059669;';
                                                }
                                            }
                                        @endphp
                                        <button type="button"
                                                wire:click="toggleAyat({{ $surah->nomor_surah }}, {{ $ayat }})"
                                                @if($isMemorized)
                                                    style="{{ $predStyle }}"
                                                @elseif($isTempSelected)
                                                    style="background: #facc15; color: #020617; border-color: #ca8a04;"
                                                @endif
                                                class="h-11 sm:h-9 w-full flex items-center justify-center text-sm sm:text-xs font-bold rounded-xl sm:rounded-lg transition-all active:scale-95 border
                                                {{ $isMemorized 
                                                    ? ($modeKoreksi 
                                                        ? 'hover:bg-rose-500! hover:text-white! ring-2 ring-rose-400' 
                                                        : 'shadow-xs cursor-default') 
                                                    : ($isTempSelected 
                                                        ? 'ring-2 ring-amber-300 font-black animate-pulse' 
                                                        : 'bg-slate-50 dark:bg-slate-800 text-text-main border-slate-300 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700') }}"
                                                title="Surat {{ $surah->nama_latin }} Ayat {{ $ayat }} {{ $isMemorized ? ($modeKoreksi ? '(Klik untuk BATALKAN hafalan)' : '[Hafal: ' . ucfirst(str_replace('_', ' ', $predikat)) . ']') : ($isTempSelected ? '(Dipilih)' : '(Klik untuk tandai)') }}">
                                            {{ $ayat }}
                                        </button>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="p-8 text-center text-text-muted">
                        Data bagian juz ini belum dimuat.
                    </div>
                @endforelse
            </div>

            <!-- Petunjuk Warna / Legenda di Bagian Bawah Sesuai Tingkat Kelancaran -->
            <div class="bg-surface-hover p-3 border-t border-border flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                    <span class="font-bold text-text-secondary text-[11px] uppercase tracking-wider">Tingkat Kelancaran:</span>
                    <div class="flex items-center gap-1.5" title="Sangat Lancar / Sempurna">
                        <span class="w-3.5 h-3.5 rounded inline-block" style="background: #10b981; border: 1px solid #059669;"></span>
                        <span class="text-text-secondary font-medium">Mumtaz</span>
                    </div>
                    <div class="flex items-center gap-1.5" title="Lancar Sekali">
                        <span class="w-3.5 h-3.5 rounded inline-block" style="background: #2563eb; border: 1px solid #1d4ed8;"></span>
                        <span class="text-text-secondary font-medium">Jayyid Jiddan</span>
                    </div>
                    <div class="flex items-center gap-1.5" title="Lancar / Baik">
                        <span class="w-3.5 h-3.5 rounded inline-block" style="background: #eab308; border: 1px solid #ca8a04;"></span>
                        <span class="text-text-secondary font-medium">Jayyid</span>
                    </div>
                    <div class="flex items-center gap-1.5" title="Cukup / Mengulang">
                        <span class="w-3.5 h-3.5 rounded inline-block" style="background: #f97316; border: 1px solid #c2410c;"></span>
                        <span class="text-text-secondary font-medium">Maqbul</span>
                    </div>
                    <div class="flex items-center gap-1.5 border-l border-border pl-3">
                        <span class="w-3.5 h-3.5 rounded bg-yellow-300 border border-amber-500 animate-pulse inline-block"></span>
                        <span class="text-text-secondary">Baru Dipilih</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 inline-block"></span>
                        <span class="text-text-secondary">Belum Dihafal</span>
                    </div>
                </div>

                <div class="text-text-muted text-[11px]">
                    * Warna kotak ayat otomatis mengikuti tingkat kelancaran saat disetorkan.
                </div>
            </div>
        </div>

        <!-- Floating Action Button / Sticky Bottom Bar Simpan Progres (Sangat Praktis di Layar HP Guru) -->
        @if(!empty($tempSelectedMap))
            <div class="fixed bottom-4 inset-x-4 sm:inset-x-auto sm:right-8 z-40 animate-in slide-in-from-bottom duration-300">
                <div class="bg-slate-900 text-white rounded-2xl shadow-2xl p-3 sm:p-4 border border-slate-700 flex items-center justify-between gap-4 max-w-md ml-auto">
                    <div>
                        <div class="text-xs text-slate-300 font-medium">Ayat yang baru ditandai:</div>
                        <div class="text-base font-bold text-amber-300">
                            {{ count($tempSelectedMap) }} Ayat Dipilih
                        </div>
                    </div>

                    <button type="button" wire:click="openSaveModal" class="btn btn-primary bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold px-4 py-2 flex items-center gap-2 shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Progres</span>
                    </button>
                </div>
            </div>
        @endif
    @else
        <!-- Tab Riwayat Setoran Santri -->
        <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-text-main">
                        Riwayat Catatan Setoran Hafalan: {{ $selectedSiswa?->nama ?? '-' }}
                    </h3>
                    <p class="text-xs text-text-secondary">Daftar setoran hafalan baru (Ziyadah) dan muroja'ah yang tercatat.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-surface-hover/80 text-text-secondary border-b border-border">
                        <tr>
                            <th class="p-3">Tanggal Setoran</th>
                            <th class="p-3">Surat & Ayat</th>
                            <th class="p-3">Jumlah Ayat</th>
                            <th class="p-3">Jenis Setoran</th>
                            <th class="p-3">Kelancaran / Predikat</th>
                            <th class="p-3">Catatan / Evaluasi Musrif</th>
                            <th class="p-3">Penguji / Musrif</th>
                            <th class="p-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($setorans as $setoran)
                            <tr class="hover:bg-surface-hover/50">
                                <td class="p-3 font-semibold text-text-main whitespace-nowrap">
                                    {{ $setoran->tanggal->format('d M Y') }}
                                </td>
                                <td class="p-3 font-semibold text-text-main">
                                    {{ $setoran->surah?->nomor_surah }}. {{ $setoran->surah?->nama_latin }}
                                    <span class="text-text-muted text-[11px] block">Ayat {{ $setoran->ayat_awal }} s/d {{ $setoran->ayat_akhir }}</span>
                                </td>
                                <td class="p-3 font-bold text-emerald-600">
                                    {{ $setoran->total_ayat }} Ayat
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase {{ $setoran->jenis === 'ziyadah' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300' }}">
                                        {{ $setoran->jenis }}
                                    </span>
                                </td>
                                <td class="p-3 font-medium">
                                    <span class="px-2.5 py-0.5 rounded text-[11px] font-bold {{ $setoran->predikat_badge_class }}">
                                        {{ $setoran->predikat_label }}
                                    </span>
                                </td>
                                <td class="p-3 text-text-secondary italic max-w-xs truncate" title="{{ $setoran->catatan }}">
                                    {{ $setoran->catatan ?: '-' }}
                                </td>
                                <td class="p-3 text-text-muted">
                                    {{ $setoran->penguji?->nama_lengkap ?? '-' }}
                                </td>
                                <td class="p-3 text-right">
                                    @can('pesantren.hafalan.hapus')
                                        <button wire:click="deleteSetoran({{ $setoran->id }})" wire:confirm="Hapus catatan setoran ini?" class="text-rose-500 hover:text-rose-700 font-semibold p-1" title="Hapus Catatan">
                                            Hapus
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-text-muted">
                                    Belum ada catatan setoran untuk santri ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($setorans instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="p-4 border-t border-border">
                    {{ $setorans->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Modal Konfirmasi & Catatan Setoran (Popup Ringkas) -->
    @if($showSaveModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface rounded-2xl border border-border shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-surface-hover/50">
                    <div>
                        <h3 class="text-base font-bold text-text-main">Konfirmasi Simpan Progres Hafalan</h3>
                        <p class="text-xs text-text-secondary">{{ $selectedSiswa?->nama }} - {{ count($tempSelectedMap) }} ayat ditandai</p>
                    </div>
                    <button wire:click="$set('showSaveModal', false)" class="text-text-muted hover:text-text-main text-2xl">&times;</button>
                </div>

                <form wire:submit="saveProgress" class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Tanggal Setoran *</label>
                            <input type="date" wire:model="setoranTanggal" class="input w-full text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Jenis Setoran</label>
                            <select wire:model="setoranJenis" class="select w-full text-sm">
                                <option value="ziyadah">Ziyadah (Hafalan Baru)</option>
                                <option value="murojaah">Muroja'ah (Mengulang)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Tingkat Kelancaran</label>
                            <select wire:model="setoranPredikat" class="select w-full text-sm font-semibold">
                                <option value="mumtaz">🟢 Mumtaz (Sangat Lancar / Sempurna)</option>
                                <option value="jayyid_jiddan">🔵 Jayyid Jiddan (Lancar Sekali)</option>
                                <option value="jayyid">🟡 Jayyid (Lancar / Baik)</option>
                                <option value="maqbul">🟠 Maqbul (Cukup / Perlu Pengulangan)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Musrif Penguji</label>
                            <select wire:model="setoranPegawaiId" class="select w-full text-sm">
                                <option value="">-- Pilih Musrif Penguji --</option>
                                @foreach($pegawais as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Catatan Evaluasi / Tajwid (Opsional)</label>
                        <textarea wire:model="setoranCatatan" rows="3" placeholder="Misal: Makhraj huruf 'Ain dan Ghain perlu dipertegas, tempo tilawah sangat baik..." class="input w-full text-sm"></textarea>
                    </div>

                    <div class="pt-4 border-t border-border flex justify-end gap-2">
                        <button type="button" wire:click="$set('showSaveModal', false)" class="btn btn-secondary text-sm">Kembali</button>
                        <button type="submit" class="btn btn-primary text-sm bg-emerald-600 hover:bg-emerald-700">Simpan Setoran</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    @endif
</div>
