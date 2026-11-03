<div class="space-y-6">
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

    <!-- Header -->
    <div class="border-b border-border pb-4">
        <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
            <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            Kepesantrenan & Hafalan Qur'an
        </h2>
        <p class="text-sm text-text-secondary mt-1">
            Pantau capaian hafalan Qur'an, tier rank Mobile Legends, lembar mutaba'ah digital, serta catatan bimbingan musrif.
        </p>
    </div>

    @if(!$isUnggulan)
        <div class="p-8 text-center bg-surface rounded-2xl border border-border space-y-3">
            <div class="w-16 h-16 mx-auto rounded-full bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl">
                ℹ️
            </div>
            <h3 class="text-lg font-bold text-text-main">Khusus Program Unggulan</h3>
            <p class="text-sm text-text-secondary max-w-md mx-auto">
                Fitur Kepesantrenan dan Lembar Mutaba'ah Tahfiz Qur'an ini khusus diperuntukkan bagi santri yang terdaftar dalam Program Unggulan SMK Wikrama 1 Garut.
            </p>
        </div>
    @else
        <!-- Kartu Identitas & Badge Rank Gamified ala Mobile Legends -->
        <div class="rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden border border-white/10"
             style="background: {{ $stats['rank']['gradient_style'] }}; color: #ffffff;">
            <div class="absolute -right-10 -bottom-10 w-52 h-52 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center relative z-10">
                <!-- Info Siswa & Progress -->
                <div class="md:col-span-7 space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                         style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); color: #ffffff;">
                        <span>PROGRAM UNGGULAN KEPESANTRENAN</span>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-black tracking-tight" style="color: #ffffff; text-shadow: 0 2px 4px rgba(0,0,0,0.4);">
                        {{ $siswa->nama }}
                    </h3>
                    <div class="text-xs sm:text-sm flex flex-wrap gap-x-4 gap-y-1" style="color: #e2e8f0;">
                        <span>Rombel: <strong style="color: #ffffff;">{{ $siswa->rombel?->nama ?? '-' }}</strong></span>
                        <span>Rayon: <strong style="color: #ffffff;">{{ $siswa->rayon?->nama ?? '-' }}</strong></span>
                        <span>Kamar: <strong style="color: #ffffff;">{{ $siswa->pesantrenKamars->first()?->nama_kamar ?? '-' }}</strong></span>
                        <span>Halaqoh: <strong style="color: #ffffff;">{{ $siswa->pesantrenHalaqohs->first()?->nama_halaqoh ?? '-' }}</strong></span>
                    </div>

                    <!-- Progress Bar Standar 3 Juz (Mythic) -->
                    <div class="pt-2">
                        <div class="flex items-center justify-between text-xs font-semibold mb-1" style="color: #cbd5e1;">
                            <span>Target Kelulusan Sekolah (3 Juz - Mythic):</span>
                            <span class="font-bold" style="color: #fde047;">{{ $stats['target_persen'] }}%</span>
                        </div>
                        <div class="w-full h-3 rounded-full overflow-hidden p-0.5"
                             style="background: rgba(0, 0, 0, 0.45); border: 1px solid rgba(255, 255, 255, 0.2);">
                            <div class="h-full rounded-full transition-all duration-500"
                                 style="background: linear-gradient(90deg, #f59e0b, #eab308, #10b981); width: {{ $stats['target_persen'] }}%"></div>
                        </div>
                        @if($stats['is_target_achieved'])
                            <span class="text-xs font-bold block mt-1" style="color: #6ee7b7;">🎉 Selamat! Kamu telah mencapai standar target kelulusan 3 Juz (Mythic)!</span>
                        @endif
                    </div>
                </div>

                <!-- Badge Rank Mobile Legends Besar -->
                <div class="md:col-span-5 flex flex-col items-center justify-center p-5 rounded-2xl text-center shadow-lg"
                     style="background: rgba(0, 0, 0, 0.45); border: 1px solid rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px);">
                    <div class="text-[11px] font-black tracking-widest uppercase" style="color: #fde047;">TIER RANK HAFALAN</div>
                    <div class="text-2xl sm:text-3xl font-black tracking-wider uppercase mt-1 drop-shadow-md"
                         style="color: {{ $stats['rank']['color'] }}; text-shadow: 0 0 12px {{ $stats['rank']['color'] }}66;">
                        {{ $stats['rank']['tier'] }}
                    </div>
                    <div class="text-xs font-medium mt-0.5" style="color: #cbd5e1;">
                        Kategori: {{ $stats['rank']['roman'] }}
                    </div>

                    <div class="mt-4 pt-3 w-full grid grid-cols-2 gap-2 text-center text-xs" style="border-top: 1px solid rgba(255, 255, 255, 0.15);">
                        <div>
                            <div style="color: #cbd5e1;">Total Ayat</div>
                            <div class="text-lg font-black" style="color: #fde047;">{{ $stats['total_ayat'] }}</div>
                        </div>
                        <div>
                            <div style="color: #cbd5e1;">Ekuivalen Juz</div>
                            <div class="text-lg font-black" style="color: #34d399;">~{{ $stats['juz'] }} Juz</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3 Tab Tampilan Siswa -->
        <div class="flex items-center gap-2 border-b border-border pb-1 overflow-x-auto">
            <button type="button" wire:click="$set('activeTab', 'kontrol')"
                    class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all whitespace-nowrap
                    {{ $activeTab === 'kontrol' ? 'bg-emerald-600 text-white shadow-md' : 'text-text-secondary hover:text-text-main bg-surface-hover' }}">
                📖 Lembar Kontrol Hafalan Saya (View-Only)
            </button>
            <button type="button" wire:click="$set('activeTab', 'riwayat')"
                    class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all whitespace-nowrap
                    {{ $activeTab === 'riwayat' ? 'bg-emerald-600 text-white shadow-md' : 'text-text-secondary hover:text-text-main bg-surface-hover' }}">
                📝 Riwayat Catatan Setoran
            </button>
            <button type="button" wire:click="$set('activeTab', 'leaderboard')"
                    class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all whitespace-nowrap
                    {{ $activeTab === 'leaderboard' ? 'bg-emerald-600 text-white shadow-md' : 'text-text-secondary hover:text-text-main bg-surface-hover' }}">
                🏆 Leaderboard Santri Unggulan
            </button>
        </div>

        <!-- Tab 1: Lembar Kontrol View-Only -->
        @if($activeTab === 'kontrol')
            <div class="space-y-4">
                <!-- Navigasi Juz -->
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

                <!-- Grid Lembar Mutaba'ah Sesuai Format Kertas (5 Kolom Mobile, 20 Kolom Desktop) -->
                <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                    <div class="bg-[#ffeb3b] text-slate-900 text-center py-2 font-black text-base sm:text-lg tracking-wider border-b-2 border-slate-900 uppercase">
                        JUZ {{ $selectedJuz }}
                    </div>

                    <div class="divide-y-2 divide-slate-900">
                        @foreach($juzSections as $sec)
                            @php
                                $surah = $surahs[$sec['surah']] ?? null;
                            @endphp
                            @if($surah)
                                <div>
                                    <div class="bg-[#00e5ff] dark:bg-cyan-600 text-slate-900 dark:text-white px-4 py-2 font-bold text-xs sm:text-sm tracking-wide uppercase flex items-center justify-between border-b border-slate-900">
                                        <span>{{ $surah->nomor_surah }}. {{ $surah->nama_latin }} ({{ $surah->arti }}) - {{ $surah->jumlah_ayat }} AYAT</span>
                                        <span class="font-serif text-sm">{{ $surah->nama_arab }}</span>
                                    </div>

                                    <div class="p-3 sm:p-4 bg-white dark:bg-slate-900">
                                        <div class="mutabaah-ayat-grid grid-cols-5 sm:grid-cols-10 md:grid-cols-20 text-center select-none">
                                            @for($ayat = $sec['start']; $ayat <= $sec['end']; $ayat++)
                                                @php
                                                    $key = "{$surah->nomor_surah}_{$ayat}";
                                                    $isMemorized = isset($memorizedMap[$key]);
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
                                                <div class="h-11 sm:h-9 w-full flex items-center justify-center text-sm sm:text-xs font-bold rounded-xl sm:rounded-lg border
                                                    {{ $isMemorized ? 'shadow-xs font-bold' : 'bg-slate-50 dark:bg-slate-800 text-text-muted border-slate-200 dark:border-slate-800' }}"
                                                    @if($isMemorized) style="{{ $predStyle }}" @endif
                                                    title="Surat {{ $surah->nama_latin }} Ayat {{ $ayat }} {{ $isMemorized ? '[Hafal: ' . ucfirst(str_replace('_', ' ', $predikat)) . ']' : '' }}">
                                                    {{ $ayat }}
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="bg-surface-hover p-3 border-t border-border flex flex-wrap items-center gap-4 text-xs">
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
                            <span class="w-3.5 h-3.5 rounded bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 inline-block"></span>
                            <span class="text-text-secondary">Belum Dihafal</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tab 2: Riwayat Setoran -->
        @if($activeTab === 'riwayat')
            <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                    <h3 class="text-base font-bold text-text-main">Riwayat Catatan Setoran Hafalan Kamu</h3>
                    <p class="text-xs text-text-secondary">Daftar evaluasi dan bimbingan musrif atas setoran hafalanmu.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-surface-hover/80 text-text-secondary border-b border-border font-bold">
                            <tr>
                                <th class="p-3">Tanggal</th>
                                <th class="p-3">Surat & Rentang Ayat</th>
                                <th class="p-3">Jumlah</th>
                                <th class="p-3">Jenis</th>
                                <th class="p-3">Predikat</th>
                                <th class="p-3">Musrif Penguji</th>
                                <th class="p-3">Catatan / Evaluasi Musrif</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($setorans as $setoran)
                                <tr class="hover:bg-surface-hover/50">
                                    <td class="p-3 font-semibold whitespace-nowrap">{{ $setoran->tanggal->format('d M Y') }}</td>
                                    <td class="p-3 font-bold text-text-main">
                                        {{ $setoran->surah?->nomor_surah }}. {{ $setoran->surah?->nama_latin }}
                                        <span class="text-text-muted text-[11px] block font-normal">Ayat {{ $setoran->ayat_awal }} s/d {{ $setoran->ayat_akhir }}</span>
                                    </td>
                                    <td class="p-3 font-bold text-emerald-600">{{ $setoran->total_ayat }} Ayat</td>
                                    <td class="p-3 uppercase font-bold text-[10px]">{{ $setoran->jenis }}</td>
                                    <td class="p-3">
                                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold {{ $setoran->predikat_badge_class }}">
                                            {{ $setoran->predikat_label }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-text-muted">{{ $setoran->penguji?->nama_lengkap ?? '-' }}</td>
                                    <td class="p-3 italic text-text-secondary">{{ $setoran->catatan ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-text-muted">
                                        Belum ada catatan setoran tercatat. Semangat menambah hafalan!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Tab 3: Leaderboard Motivasi -->
        @if($activeTab === 'leaderboard')
            <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-text-main">Leaderboard Santri Unggulan</h3>
                        <p class="text-xs text-text-secondary">Top peringkat santri unggulan berdasarkan akumulasi hafalan Qur'an.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-surface-hover/80 text-text-secondary border-b border-border font-bold">
                            <tr>
                                <th class="p-3.5 text-center w-12">Rank</th>
                                <th class="p-3.5">Santri</th>
                                <th class="p-3.5">Tier Rank MLBB</th>
                                <th class="p-3.5">Total Hafalan</th>
                                <th class="p-3.5">Kamar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($leaderboard as $item)
                                @php
                                    $isMe = $item->siswa->id === $siswa->id;
                                @endphp
                                <tr class="hover:bg-surface-hover/50 {{ $isMe ? 'bg-amber-50 dark:bg-amber-950/30 font-bold border-l-4 border-l-amber-500' : '' }}">
                                    <td class="p-3.5 text-center">
                                        @if($item->ranking === 1)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-400 text-slate-900 font-black text-xs">1</span>
                                        @elseif($item->ranking === 2)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-300 text-slate-900 font-black text-xs">2</span>
                                        @elseif($item->ranking === 3)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-700 text-white font-black text-xs">3</span>
                                        @else
                                            <span class="text-text-muted font-bold text-xs">{{ $item->ranking }}</span>
                                        @endif
                                    </td>
                                    <td class="p-3.5">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ $item->siswa->nama }}</span>
                                            @if($isMe)
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-amber-500 text-white uppercase">SAYA</span>
                                            @endif
                                        </div>
                                        <span class="text-[10px] text-text-muted">{{ $item->siswa->rombel?->nama ?? '-' }}</span>
                                    </td>
                                    <td class="p-3.5">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase {{ $item->rank['badge_color'] }}" style="{{ $item->rank['badge_style'] ?? '' }}">
                                            {{ $item->rank['tier'] }}
                                        </span>
                                    </td>
                                    <td class="p-3.5">
                                        <span class="font-bold text-emerald-600">{{ $item->total_ayat }} Ayat</span>
                                        <span class="text-text-muted text-[11px] block">~{{ $item->juz }} Juz</span>
                                    </td>
                                    <td class="p-3.5 text-text-secondary">{{ $item->kamar }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif
</div>
