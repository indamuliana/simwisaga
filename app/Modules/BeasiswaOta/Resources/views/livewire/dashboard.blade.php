<div class="space-y-6">
    @include('beasiswaota::partials.sub-nav', ['active' => 'index'])

    <!-- KPI Cards Saldo Kas & Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Kas OTA -->
        <div class="card p-5 bg-gradient-to-br from-indigo-500/10 via-surface to-surface border border-indigo-200 dark:border-indigo-900/50 rounded-2xl shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Total Kas OTA Tersedia</span>
                <span class="p-2.5 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-black text-text-main">
                    Rp {{ number_format($ringkasanKas['total_kas'], 0, ',', '.') }}
                </p>
                <p class="text-xs text-text-secondary mt-1">Gabungan kas kelompok & kas insidental</p>
            </div>
        </div>

        <!-- Kas Kelompok Terkumpul -->
        <div class="card p-5 bg-gradient-to-br from-emerald-500/10 via-surface to-surface border border-emerald-200 dark:border-emerald-900/50 rounded-2xl shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Kas Kelompok Terikat</span>
                <span class="p-2.5 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-black text-text-main">
                    Rp {{ number_format($ringkasanKas['saldo_kelompok'], 0, ',', '.') }}
                </p>
                <p class="text-xs text-text-secondary mt-1">Dari {{ $totalKelompok }} kelompok asuh aktif</p>
            </div>
        </div>

        <!-- Kas Bebas / Insidental -->
        <div class="card p-5 bg-gradient-to-br from-amber-500/10 via-surface to-surface border border-amber-200 dark:border-amber-900/50 rounded-2xl shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">Kas Bebas (Insidental)</span>
                <span class="p-2.5 bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-black text-text-main">
                    Rp {{ number_format($ringkasanKas['saldo_insidental'], 0, ',', '.') }}
                </p>
                <p class="text-xs text-text-secondary mt-1">Fleksibel untuk subsidi silang & bantuan darurat</p>
            </div>
        </div>

        <!-- Donatur & Siswa Binaan -->
        <div class="card p-5 bg-gradient-to-br from-rose-500/10 via-surface to-surface border border-rose-200 dark:border-rose-900/50 rounded-2xl shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-rose-600 dark:text-rose-400">Donatur & Siswa</span>
                <span class="p-2.5 bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-black text-text-main">
                    {{ $totalDonaturRutin + $totalDonaturInsidental }} <span class="text-sm font-normal text-text-secondary">Donatur</span>
                </p>
                <p class="text-xs text-text-secondary mt-1">
                    {{ $totalDonaturRutin }} Rutin &bull; {{ $totalDonaturInsidental }} Insidental &bull; <strong>{{ $totalSiswaPenerima }} Siswa Binaan</strong>
                </p>
            </div>
        </div>
    </div>

    <!-- Ringkasan Kelompok Asuh & Status Dana -->
    <div class="card border border-border rounded-2xl p-6 bg-surface shadow-xs">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-5">
            <div>
                <h3 class="text-lg font-bold text-text-main flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Progres Kelompok Donatur & Siswa Asuh
                </h3>
                <p class="text-xs text-text-secondary mt-0.5">
                    1 Kelompok membiayai 1 Siswa binaan. Menampilkan target kebutuhan per semester, komitmen donatur, dan saldo terkumpul.
                </p>
            </div>
            <a href="{{ route('beasiswa-ota.donatur-kelompok') }}" class="btn-sm btn-outline text-xs flex items-center gap-1.5">
                Kelola Kelompok & Donatur
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-border bg-surface-hover/50 text-xs font-semibold text-text-secondary uppercase">
                        <th class="py-3 px-4">Kelompok</th>
                        <th class="py-3 px-4">Siswa Binaan</th>
                        <th class="py-3 px-4">Jumlah Donatur</th>
                        <th class="py-3 px-4 text-right">Target Biaya</th>
                        <th class="py-3 px-4 text-right">Total Komitmen</th>
                        <th class="py-3 px-4 text-right">Saldo Kas Kelompok</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($kelompoks as $kelompok)
                        @php
                            $target = (float) $kelompok->target_nominal;
                            $komitmen = (float) $kelompok->total_komitmen;
                            $saldo = (float) $kelompok->saldo_kas;
                            $siswa = $kelompok->siswaPenerima?->siswa;
                            $persenSaldo = $target > 0 ? min(100, round(($saldo / $target) * 100)) : 0;
                        @endphp
                        <tr class="hover:bg-surface-hover/30 transition">
                            <td class="py-3.5 px-4 font-semibold text-text-main">
                                {{ $kelompok->nama_kelompok }}
                                <div class="text-xs font-normal text-text-secondary">{{ $kelompok->kode_kelompok }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($siswa)
                                    <div class="font-medium text-text-main">{{ $siswa->nama }}</div>
                                    <div class="text-xs text-text-secondary">{{ $siswa->rombel?->nama ?? '-' }} &bull; NISN: {{ $siswa->nisn ?? '-' }}</div>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">
                                        Belum Ditentukan
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-text-main">{{ $kelompok->donaturs->count() }}</span> Donatur
                            </td>
                            <td class="py-3.5 px-4 text-right font-medium text-text-main">
                                Rp {{ number_format($target, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <span class="font-semibold {{ $komitmen >= $target ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                    Rp {{ number_format($komitmen, 0, ',', '.') }}
                                </span>
                                @if($komitmen > $target)
                                    <div class="text-[10px] text-emerald-600 font-semibold">+Rp {{ number_format($komitmen - $target, 0, ',', '.') }} (Surplus)</div>
                                @elseif($komitmen < $target)
                                    <div class="text-[10px] text-rose-500 font-medium">-Rp {{ number_format($target - $komitmen, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="font-bold text-text-main">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
                                <div class="w-24 bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden ml-auto mt-1">
                                    <div class="bg-primary h-full rounded-full" style="width: {{ $persenSaldo }}%"></div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($saldo >= $target)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                        Siap Alokasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950/50 dark:text-blue-300">
                                        Proses Pengumpulan
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-text-secondary">
                                Belum ada kelompok donatur yang dibuat. Silakan tambahkan di menu Donatur & Kelompok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Grid Riwayat Donasi & Alokasi Terakhir -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Donasi Masuk Terakhir -->
        <div class="card border border-border rounded-2xl p-5 bg-surface shadow-xs">
            <div class="flex justify-between items-center mb-4">
                <h4 class="font-bold text-text-main flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    Donasi Masuk Terakhir
                </h4>
                <a href="{{ route('beasiswa-ota.donasi-masuk') }}" class="text-xs text-primary hover:underline font-medium">Lihat Semua</a>
            </div>

            <div class="divide-y divide-border">
                @forelse($recentDonasi as $item)
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div>
                            <div class="font-semibold text-sm text-text-main">{{ $item->donatur->nama }}</div>
                            <div class="text-xs text-text-secondary">
                                {{ $item->tanggal_donasi->translatedFormat('d M Y') }} &bull; 
                                {{ $item->kelompok ? $item->kelompok->nama_kelompok : 'Kas Bebas Insidental' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-sm text-emerald-600 dark:text-emerald-400">
                                +Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </div>
                            <div class="flex items-center gap-1.5 justify-end mt-1">
                                <a href="{{ route('beasiswa-ota.cetak.tanda-terima', $item->id) }}" target="_blank" 
                                   class="text-[11px] text-indigo-600 hover:text-indigo-800 hover:underline">
                                    Tanda Terima
                                </a>
                                <span class="text-gray-300">&bull;</span>
                                <a href="{{ route('beasiswa-ota.cetak.sertifikat', $item->id) }}" target="_blank" 
                                   class="text-[11px] text-amber-600 hover:text-amber-800 hover:underline">
                                    Sertifikat
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-sm text-text-secondary">Belum ada catatan donasi masuk.</div>
                @endforelse
            </div>
        </div>

        <!-- Penyaluran Alokasi Terakhir -->
        <div class="card border border-border rounded-2xl p-5 bg-surface shadow-xs">
            <div class="flex justify-between items-center mb-4">
                <h4 class="font-bold text-text-main flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    Penyaluran Terakhir ke Siswa
                </h4>
                <a href="{{ route('beasiswa-ota.penyaluran') }}" class="text-xs text-primary hover:underline font-medium">Lihat Semua</a>
            </div>

            <div class="divide-y divide-border">
                @forelse($recentAlokasi as $item)
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div>
                            <div class="font-semibold text-sm text-text-main">{{ $item->siswa->nama }}</div>
                            <div class="text-xs text-text-secondary">
                                {{ $item->tanggal_alokasi->translatedFormat('d M Y') }} &bull; 
                                {{ $item->tipe_alokasi === 'rutin_semester' ? ($item->kelompok?->nama_kelompok ?? 'Kelompok OTA') : 'Kas Insidental' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-sm text-text-main">
                                Rp {{ number_format($item->total_nominal, 0, ',', '.') }}
                            </div>
                            <a href="{{ route('beasiswa-ota.cetak.kwitansi', $item->id) }}" target="_blank" 
                               class="text-[11px] text-rose-600 hover:text-rose-800 hover:underline mt-1 inline-block">
                                Cetak Kwitansi
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-sm text-text-secondary">Belum ada alokasi penyaluran beasiswa.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
