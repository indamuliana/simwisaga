<div class="space-y-6">
    <!-- Header Navigasi Sub-Modul Kepesantrenan -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-border pb-4">
        <div>
            <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
                <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                Rekapitulasi & Leaderboard Hafalan
            </h2>
            <p class="text-sm text-text-secondary mt-1">
                Peringkat santri unggulan dengan tier rank Mobile Legends (Warrior s/d Mythical Immortal), rekapitulasi kamar/rayon, dan kartu kontrol mutaba'ah per santri.
            </p>
        </div>

        <!-- Tab Navigasi Submodul -->
        <div class="flex items-center gap-1.5 bg-surface-hover p-1.5 rounded-xl border border-border overflow-x-auto max-w-full">
            <a href="{{ route('pesantren.kamar') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface whitespace-nowrap">
                Kamar & Asrama
            </a>
            <a href="{{ route('pesantren.hafalan') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface whitespace-nowrap">
                Input Hafalan
            </a>
            <a href="{{ route('pesantren.rekap') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold bg-white dark:bg-gray-800 text-amber-500 shadow-xs whitespace-nowrap">
                Rekap & Rank MLBB
            </a>
            <a href="{{ route('pesantren.halaqoh') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface whitespace-nowrap">
                Halaqoh & Presensi
            </a>
        </div>
    </div>

    <!-- 3 Tab Utama Rekapitulasi -->
    <div class="flex items-center gap-2 border-b border-border pb-1 overflow-x-auto">
        <button type="button" wire:click="$set('activeTab', 'leaderboard')"
                class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 whitespace-nowrap
                {{ $activeTab === 'leaderboard' ? 'bg-amber-500 text-white shadow-md' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
            <span>🏆</span>
            <span>1. Leaderboard Hafalan (Rank MLBB)</span>
        </button>

        <button type="button" wire:click="$set('activeTab', 'rekap')"
                class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 whitespace-nowrap
                {{ $activeTab === 'rekap' ? 'bg-amber-500 text-white shadow-md' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
            <span>📊</span>
            <span>2. Rekap Keseluruhan, Kamar & Rayon</span>
        </button>

        <button type="button" wire:click="$set('activeTab', 'persiswa')"
                class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 whitespace-nowrap
                {{ $activeTab === 'persiswa' ? 'bg-amber-500 text-white shadow-md' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
            <span>📜</span>
            <span>3. Laporan Hafalan Per Siswa</span>
        </button>
    </div>

    <!-- ==================== TAB 1: LEADERBOARD HAFALAN ==================== -->
    @if($activeTab === 'leaderboard')
        <div class="space-y-6">
            <!-- Podium Top 3 Santri (Jika ada data hafalan) -->
            @if($leaderboard->isNotEmpty() && $leaderboard->first()->total_ayat > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4">
                    <!-- Juara 2 (Perak) -->
                    @if(isset($leaderboard[1]) && $leaderboard[1]->total_ayat > 0)
                        @php $p2 = $leaderboard[1]; @endphp
                        <div class="order-2 md:order-1 bg-surface rounded-2xl border border-slate-300 dark:border-slate-700 p-5 text-center shadow-xs relative overflow-hidden flex flex-col justify-between">
                            <div class="absolute -top-10 -left-10 w-24 h-24 bg-slate-400/10 rounded-full blur-xl"></div>
                            <div>
                                <div class="w-12 h-12 mx-auto rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-black text-lg flex items-center justify-center border-2 border-slate-400 shadow-md">
                                    🥈 2
                                </div>
                                <h4 class="font-bold text-base text-text-main mt-3 truncate">{{ $p2->siswa->nama }}</h4>
                                <div class="text-xs text-text-muted">{{ $p2->siswa->rombel?->nama ?? '-' }} | {{ $p2->kamar }}</div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-border">
                                <span class="px-2.5 py-0.5 rounded text-xs font-extrabold uppercase {{ $p2->rank['badge_color'] }}" style="{{ $p2->rank['badge_style'] ?? '' }}">
                                    {{ $p2->rank['tier'] }}
                                </span>
                                <div class="text-base font-black text-emerald-600 dark:text-emerald-400 mt-1">
                                    {{ $p2->total_ayat }} Ayat (~{{ $p2->juz }} Juz)
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="order-2 md:order-1"></div>
                    @endif

                    <!-- Juara 1 (Emas / Champion) -->
                    @if(isset($leaderboard[0]) && $leaderboard[0]->total_ayat > 0)
                        @php $p1 = $leaderboard[0]; @endphp
                        <div class="order-1 md:order-2 bg-gradient-to-b from-amber-500/10 via-surface to-surface rounded-2xl border-2 border-amber-400 p-6 text-center shadow-lg relative overflow-hidden flex flex-col justify-between transform md:-translate-y-2">
                            <div class="absolute -top-8 -right-8 w-28 h-28 bg-amber-400/20 rounded-full blur-2xl"></div>
                            <div>
                                <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-tr from-amber-400 to-yellow-300 text-amber-950 font-black text-2xl flex items-center justify-center border-2 border-amber-300 shadow-xl ring-4 ring-amber-400/30">
                                    👑 1
                                </div>
                                <div class="text-[11px] font-black uppercase tracking-widest text-amber-600 dark:text-amber-400 mt-2">CHAMPION SANTRI</div>
                                <h4 class="font-black text-lg text-text-main mt-0.5 truncate">{{ $p1->siswa->nama }}</h4>
                                <div class="text-xs text-text-muted">{{ $p1->siswa->rombel?->nama ?? '-' }} | {{ $p1->kamar }}</div>
                            </div>
                            <div class="mt-5 pt-3 border-t border-border">
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase {{ $p1->rank['badge_color'] }}" style="{{ $p1->rank['badge_style'] ?? '' }}">
                                    ★ {{ $p1->rank['tier'] }} ★
                                </span>
                                <div class="text-xl font-black text-amber-600 dark:text-amber-400 mt-1.5">
                                    {{ $p1->total_ayat }} Ayat (~{{ $p1->juz }} Juz)
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Juara 3 (Perunggu) -->
                    @if(isset($leaderboard[2]) && $leaderboard[2]->total_ayat > 0)
                        @php $p3 = $leaderboard[2]; @endphp
                        <div class="order-3 bg-surface rounded-2xl border border-amber-700/30 p-5 text-center shadow-xs relative overflow-hidden flex flex-col justify-between">
                            <div>
                                <div class="w-12 h-12 mx-auto rounded-full bg-amber-900/20 text-amber-800 dark:text-amber-300 font-black text-lg flex items-center justify-center border-2 border-amber-600 shadow-md">
                                    🥉 3
                                </div>
                                <h4 class="font-bold text-base text-text-main mt-3 truncate">{{ $p3->siswa->nama }}</h4>
                                <div class="text-xs text-text-muted">{{ $p3->siswa->rombel?->nama ?? '-' }} | {{ $p3->kamar }}</div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-border">
                                <span class="px-2.5 py-0.5 rounded text-xs font-extrabold uppercase {{ $p3->rank['badge_color'] }}" style="{{ $p3->rank['badge_style'] ?? '' }}">
                                    {{ $p3->rank['tier'] }}
                                </span>
                                <div class="text-base font-black text-emerald-600 dark:text-emerald-400 mt-1">
                                    {{ $p3->total_ayat }} Ayat (~{{ $p3->juz }} Juz)
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Toolbar Filter Leaderboard -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 bg-surface p-4 rounded-xl border border-border">
                <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                    <div class="relative w-full sm:w-60">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama santri..." class="input pl-8 w-full text-xs">
                        <svg class="w-4 h-4 text-text-secondary absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <select wire:model.live="filterKamarId" class="select text-xs w-40">
                        <option value="">Semua Kamar</option>
                        @foreach($kamars as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kamar }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="filterRayonId" class="select text-xs w-40">
                        <option value="">Semua Rayon</option>
                        @foreach($rayons as $r)
                            <option value="{{ $r->id }}">{{ $r->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="text-xs text-text-muted">
                    Total {{ $leaderboard->count() }} Santri Unggulan
                </div>
            </div>

            <!-- Tabel Leaderboard Lengkap -->
            <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-surface-hover/80 text-text-secondary border-b border-border font-bold">
                            <tr>
                                <th class="p-3.5 text-center w-12">Rank</th>
                                <th class="p-3.5">Santri</th>
                                <th class="p-3.5">Tier Rank MLBB</th>
                                <th class="p-3.5">Total Hafalan</th>
                                <th class="p-3.5">Target Standar (3 Juz)</th>
                                <th class="p-3.5">Kamar & Rayon</th>
                                <th class="p-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($leaderboard as $item)
                                <tr class="hover:bg-surface-hover/50 transition-colors {{ $item->ranking <= 3 ? 'font-semibold' : '' }}">
                                    <!-- Kolom Ranking -->
                                    <td class="p-3.5 text-center">
                                        @if($item->ranking === 1)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-400 text-slate-900 font-black text-xs shadow-xs">1</span>
                                        @elseif($item->ranking === 2)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-300 text-slate-900 font-black text-xs shadow-xs">2</span>
                                        @elseif($item->ranking === 3)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-700 text-white font-black text-xs shadow-xs">3</span>
                                        @else
                                            <span class="text-text-muted font-bold text-xs">{{ $item->ranking }}</span>
                                        @endif
                                    </td>

                                    <!-- Santri & Rombel -->
                                    <td class="p-3.5">
                                        <button type="button" wire:click="selectDetailSiswa({{ $item->siswa->id }})" class="font-bold text-text-main hover:text-primary text-left">
                                            {{ $item->siswa->nama }}
                                        </button>
                                        <div class="text-[11px] text-text-muted">
                                            {{ $item->siswa->rombel?->nama ?? '-' }} | NISN: {{ $item->siswa->nisn ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- MLBB Rank Tier Badge -->
                                    <td class="p-3.5">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-black uppercase border shadow-xs {{ $item->rank['badge_color'] }}" style="{{ $item->rank['badge_style'] ?? '' }}">
                                            <span>{{ $item->rank['tier'] }}</span>
                                        </span>
                                    </td>

                                    <!-- Total Hafalan -->
                                    <td class="p-3.5">
                                        <div class="font-extrabold text-sm text-emerald-600 dark:text-emerald-400">
                                            {{ $item->total_ayat }} Ayat
                                        </div>
                                        <div class="text-[11px] text-text-secondary">
                                            Ekuivalen: ~<strong>{{ $item->juz }}</strong> Juz
                                        </div>
                                    </td>

                                    <!-- Progress Menuju Standar 3 Juz -->
                                    <td class="p-3.5">
                                        <div class="flex items-center gap-2">
                                            <div class="w-24 bg-border/70 h-2 rounded-full overflow-hidden">
                                                <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full" style="width: {{ $item->persen_target }}%"></div>
                                            </div>
                                            <span class="text-[11px] font-bold {{ $item->juz >= 3.0 ? 'text-emerald-600 font-extrabold' : 'text-text-muted' }}">
                                                {{ $item->persen_target }}%
                                            </span>
                                        </div>
                                        @if($item->juz >= 3.0)
                                            <span class="text-[10px] font-bold text-emerald-600 block mt-0.5">✓ Tuntas Mythic</span>
                                        @endif
                                    </td>

                                    <!-- Kamar & Rayon -->
                                    <td class="p-3.5 text-text-secondary">
                                        <div class="font-medium text-text-main">{{ $item->kamar }}</div>
                                        <div class="text-[11px] text-text-muted">{{ $item->siswa->rayon?->nama ?? '-' }}</div>
                                    </td>

                                    <!-- Aksi Lihat Detail -->
                                    <td class="p-3.5 text-right">
                                        <button type="button" wire:click="selectDetailSiswa({{ $item->siswa->id }})" class="btn btn-secondary text-xs py-1 px-2.5">
                                            Kartu Hafalan
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-text-muted">
                                        Tidak ada data santri ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ==================== TAB 2: REKAP KESELURUHAN, KAMAR & RAYON ==================== -->
    @if($activeTab === 'rekap')
        <div class="space-y-6">
            <!-- 4 Kartu KPI Ringkasan Metrik Sekolah -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-surface p-5 rounded-2xl border border-border shadow-xs">
                    <div class="text-xs text-text-muted uppercase font-bold tracking-wider">Total Santri Unggulan</div>
                    <div class="text-2xl font-black text-text-main mt-1">{{ $rekapData['summary']['total_santri'] }}</div>
                    <div class="text-xs text-text-secondary mt-1">Siswa Program Unggulan</div>
                </div>

                <div class="bg-surface p-5 rounded-2xl border border-border shadow-xs">
                    <div class="text-xs text-text-muted uppercase font-bold tracking-wider">Akumulasi Hafalan</div>
                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                        {{ number_format($rekapData['summary']['total_ayat']) }} <span class="text-sm font-medium">Ayat</span>
                    </div>
                    <div class="text-xs text-text-secondary mt-1">Total Ekuivalen ~{{ $rekapData['summary']['total_juz'] }} Juz</div>
                </div>

                <div class="bg-surface p-5 rounded-2xl border border-border shadow-xs">
                    <div class="text-xs text-text-muted uppercase font-bold tracking-wider">Rata-rata Hafalan</div>
                    <div class="text-2xl font-black text-amber-500 mt-1">
                        {{ $rekapData['summary']['rata_juz'] }} <span class="text-sm font-medium">Juz / Santri</span>
                    </div>
                    <div class="text-xs text-text-secondary mt-1">Rata-rata capaian angkatan</div>
                </div>

                <div class="bg-surface p-5 rounded-2xl border border-border shadow-xs">
                    <div class="text-xs text-text-muted uppercase font-bold tracking-wider">Tuntas Standar 3 Juz</div>
                    <div class="text-2xl font-black text-teal-600 dark:text-teal-400 mt-1">
                        {{ $rekapData['summary']['santri_mythic'] }} <span class="text-sm font-medium">Santri</span>
                    </div>
                    <div class="text-xs text-text-secondary mt-1">{{ $rekapData['summary']['persen_mythic'] }}% Capaian Target Kelulusan</div>
                </div>
            </div>

            <!-- Tabel Agregat Rekap Per Kamar -->
            <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-text-main">Rekapitulasi Progress Hafalan Per Kamar</h3>
                        <p class="text-xs text-text-secondary">Statistik hafalan santri per asrama dan musrif pembimbing.</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-surface-hover/80 text-text-secondary border-b border-border font-bold">
                            <tr>
                                <th class="p-3.5">Kamar & Gedung</th>
                                <th class="p-3.5">Gender</th>
                                <th class="p-3.5">Musrif / PJ</th>
                                <th class="p-3.5 text-center">Jumlah Santri</th>
                                <th class="p-3.5">Total Ayat</th>
                                <th class="p-3.5">Rata-rata Juz</th>
                                <th class="p-3.5">Tuntas 3 Juz</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($rekapData['kamars'] as $k)
                                <tr class="hover:bg-surface-hover/50">
                                    <td class="p-3.5 font-bold text-text-main">
                                        {{ $k['nama'] }}
                                        <span class="text-text-muted text-[11px] font-normal block">{{ $k['gedung'] }}</span>
                                    </td>
                                    <td class="p-3.5">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $k['gender'] === 'Putra' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                            {{ $k['gender'] }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 text-text-secondary font-medium">{{ $k['musrif'] }}</td>
                                    <td class="p-3.5 text-center font-bold">{{ $k['jumlah_santri'] }}</td>
                                    <td class="p-3.5 font-bold text-emerald-600">{{ number_format($k['total_ayat']) }} Ayat</td>
                                    <td class="p-3.5 font-bold">{{ $k['rata_juz'] }} Juz</td>
                                    <td class="p-3.5">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300">
                                            {{ $k['santri_tuntas'] }} Santri
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-6 text-center text-text-muted">Belum ada data kamar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel Agregat Rekap Per Rayon -->
            <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-text-main">Rekapitulasi Progress Hafalan Per Rayon (Santri Unggulan)</h3>
                        <p class="text-xs text-text-secondary">Statistik hafalan santri unggulan terkelompok berdasarkan rayon bimbingan.</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-surface-hover/80 text-text-secondary border-b border-border font-bold">
                            <tr>
                                <th class="p-3.5">Nama Rayon</th>
                                <th class="p-3.5 text-center">Jumlah Santri Unggulan</th>
                                <th class="p-3.5">Total Ayat Terhafal</th>
                                <th class="p-3.5">Rata-rata Juz</th>
                                <th class="p-3.5">Tuntas Standar 3 Juz</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($rekapData['rayons'] as $r)
                                <tr class="hover:bg-surface-hover/50">
                                    <td class="p-3.5 font-bold text-text-main">{{ $r['nama'] }}</td>
                                    <td class="p-3.5 text-center font-bold">{{ $r['jumlah_santri'] }}</td>
                                    <td class="p-3.5 font-bold text-emerald-600">{{ number_format($r['total_ayat']) }} Ayat</td>
                                    <td class="p-3.5 font-bold">{{ $r['rata_juz'] }} Juz</td>
                                    <td class="p-3.5">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300">
                                            {{ $r['santri_tuntas'] }} Santri
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-text-muted">Belum ada data rayon.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ==================== TAB 3: LAPORAN PER SISWA (KARTU KONTROL DIGITAL) ==================== -->
    @if($activeTab === 'persiswa')
        <div class="space-y-6">
            <!-- Selector Santri Detail -->
            <div class="bg-surface p-4 rounded-xl border border-border flex flex-col sm:flex-row items-center gap-3">
                <span class="text-xs font-bold uppercase tracking-wider text-text-secondary whitespace-nowrap">Pilih Santri:</span>
                <div class="relative w-full sm:w-64">
                    <input type="text" wire:model.live.debounce.300ms="searchSiswaDetail" placeholder="Ketik nama santri..." class="input pl-8 w-full text-xs">
                    <svg class="w-4 h-4 text-text-secondary absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <select wire:model.live="selectedSiswaId" class="select text-xs w-full sm:w-80 font-semibold">
                    @foreach($unggulans as $u)
                        <option value="{{ $u->id }}">{{ $u->nama }} ({{ $u->rombel?->nama ?? '-' }})</option>
                    @endforeach
                </select>
            </div>

            @if($selectedSiswa && $siswaStats)
                <!-- Kartu Identitas & Badge Rank Gamified ala Mobile Legends -->
                <div class="rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden border border-white/10"
                     style="background: {{ $siswaStats['rank']['gradient_style'] ?? 'linear-gradient(135deg, #1e293b 0%, #334155 50%, #0f172a 100%)' }}; color: #ffffff;">
                    <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center relative z-10">
                        <!-- Profil Santri -->
                        <div class="md:col-span-7 space-y-3">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                                 style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); color: #ffffff;">
                                <span>PROGRAM UNGGULAN KEPESANTRENAN</span>
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-black tracking-tight" style="color: #ffffff; text-shadow: 0 2px 4px rgba(0,0,0,0.4);">
                                {{ $selectedSiswa->nama }}
                            </h3>
                            <div class="text-xs sm:text-sm flex flex-wrap gap-x-4 gap-y-1" style="color: #e2e8f0;">
                                <span>NISN: <strong style="color: #ffffff;">{{ $selectedSiswa->nisn ?? '-' }}</strong></span>
                                <span>Rombel: <strong style="color: #ffffff;">{{ $selectedSiswa->rombel?->nama ?? '-' }}</strong></span>
                                <span>Rayon: <strong style="color: #ffffff;">{{ $selectedSiswa->rayon?->nama ?? '-' }}</strong></span>
                                <span>Kamar: <strong style="color: #ffffff;">{{ $selectedSiswa->pesantrenKamars->first()?->nama_kamar ?? '-' }}</strong></span>
                            </div>

                            <!-- Progress Bar Standar 3 Juz -->
                            <div class="pt-2">
                                <div class="flex items-center justify-between text-xs font-semibold mb-1" style="color: #cbd5e1;">
                                    <span>Progress Menuju Standar Kelulusan (3 Juz - Mythic):</span>
                                    <span class="font-bold" style="color: #fde047;">{{ $siswaStats['target_persen'] }}%</span>
                                </div>
                                <div class="w-full h-3 rounded-full overflow-hidden p-0.5"
                                     style="background: rgba(0, 0, 0, 0.45); border: 1px solid rgba(255, 255, 255, 0.2);">
                                    <div class="h-full rounded-full transition-all duration-500"
                                         style="background: linear-gradient(90deg, #f59e0b, #eab308, #10b981); width: {{ $siswaStats['target_persen'] }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Badge Rank Mobile Legends Besar -->
                        <div class="md:col-span-5 flex flex-col items-center justify-center p-5 rounded-2xl text-center shadow-lg"
                             style="background: rgba(0, 0, 0, 0.45); border: 1px solid rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px);">
                            <div class="text-[11px] font-black tracking-widest uppercase" style="color: #fde047;">RANK TIER HAFALAN</div>
                            <div class="text-2xl sm:text-3xl font-black tracking-wider uppercase mt-1 drop-shadow-md"
                                 style="color: {{ $siswaStats['rank']['color'] }}; text-shadow: 0 0 12px {{ $siswaStats['rank']['color'] }}66;">
                                {{ $siswaStats['rank']['tier'] }}
                            </div>
                            <div class="text-xs font-medium mt-0.5" style="color: #cbd5e1;">
                                Kategori: {{ $siswaStats['rank']['roman'] }}
                            </div>

                            <div class="mt-4 pt-3 w-full grid grid-cols-2 gap-2 text-center text-xs" style="border-top: 1px solid rgba(255, 255, 255, 0.15);">
                                <div>
                                    <div style="color: #cbd5e1;">Total Ayat</div>
                                    <div class="text-lg font-black" style="color: #fde047;">{{ $siswaStats['total_ayat'] }}</div>
                                </div>
                                <div>
                                    <div style="color: #cbd5e1;">Ekuivalen Juz</div>
                                    <div class="text-lg font-black" style="color: #34d399;">~{{ $siswaStats['juz'] }} Juz</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rincian Surat yang Dihafal -->
                <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-bold text-text-main">Daftar Surat yang Telah Disetorkan</h4>
                            <p class="text-xs text-text-secondary">Rincian kemajuan per surat Al-Qur'an.</p>
                        </div>
                    </div>

                    <div class="p-6">
                        @if($surahDetails->isNotEmpty())
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($surahDetails as $item)
                                    <div class="p-3.5 bg-surface-hover rounded-xl border border-border flex items-center justify-between">
                                        <div>
                                            <div class="font-bold text-xs text-text-main flex items-center gap-1.5">
                                                <span>{{ $item['surah']?->nomor_surah }}. {{ $item['surah']?->nama_latin }}</span>
                                                @if($item['is_lengkap'])
                                                    <span class="text-emerald-500 font-bold" title="Lengkap Satu Surat">✓</span>
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-text-muted mt-0.5">
                                                Ayat {{ $item['ayat_range'] }} ({{ $item['ayat_count'] }}/{{ $item['total_ayat'] }} ayat)
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $item['is_lengkap'] ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-800' }}">
                                                {{ $item['persen'] }}%
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="py-8 text-center text-text-muted">
                                Santri ini belum memiliki riwayat setoran hafalan tersimpan.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Log Riwayat Catatan Setoran Terakhir -->
                <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-border">
                        <h4 class="text-base font-bold text-text-main">Log Riwayat Setoran Terakhir</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-surface-hover/80 text-text-secondary border-b border-border font-bold">
                                <tr>
                                    <th class="p-3">Tanggal</th>
                                    <th class="p-3">Surat & Rentang Ayat</th>
                                    <th class="p-3">Jenis</th>
                                    <th class="p-3">Predikat</th>
                                    <th class="p-3">Musrif Penguji</th>
                                    <th class="p-3">Catatan / Evaluasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @forelse($riwayatSetoran as $setoran)
                                    <tr class="hover:bg-surface-hover/50">
                                        <td class="p-3 font-semibold whitespace-nowrap">{{ $setoran->tanggal->format('d M Y') }}</td>
                                        <td class="p-3 font-medium">{{ $setoran->surah?->nomor_surah }}. {{ $setoran->surah?->nama_latin }} (Ayat {{ $setoran->ayat_awal }}-{{ $setoran->ayat_akhir }})</td>
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
                                        <td colspan="6" class="p-6 text-center text-text-muted">Belum ada catatan setoran.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
