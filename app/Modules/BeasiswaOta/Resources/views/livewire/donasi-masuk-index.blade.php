<div class="space-y-6">
    @include('beasiswaota::partials.sub-nav', ['active' => 'donasi-masuk'])

    <!-- Alert Notifikasi -->
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl text-rose-800 dark:text-rose-200 text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
        </div>
    @endif

    <!-- Header & Tab Navigasi Utama -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-border">
        <div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">Penerimaan & Monitoring Donasi Masuk OTA</h1>
            <p class="text-xs text-text-secondary mt-0.5">
                Pantau realisasi donasi masuk per kelompok donatur binaan atau lihat riwayat transaksi audit individual.
            </p>
        </div>

        <!-- Tab Switcher -->
        <div class="flex p-1 bg-surface-hover/80 rounded-xl border border-border shrink-0 self-start sm:self-auto">
            <button type="button" 
                    wire:click="$set('activeTab', 'kelompok')" 
                    class="py-2 px-4 rounded-lg text-xs font-bold transition flex items-center gap-2 {{ $activeTab === 'kelompok' ? 'bg-surface text-primary shadow-xs border border-border' : 'text-text-secondary hover:text-text-main' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Rekap Per Kelompok
            </button>
            <button type="button" 
                    wire:click="$set('activeTab', 'transaksi')" 
                    class="py-2 px-4 rounded-lg text-xs font-bold transition flex items-center gap-2 {{ $activeTab === 'transaksi' ? 'bg-surface text-primary shadow-xs border border-border' : 'text-text-secondary hover:text-text-main' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                Riwayat Transaksi (Per Donatur)
            </button>
        </div>
    </div>

    <!-- Ringkasan Statistik Kartu -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-4 bg-surface border border-border rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-text-secondary uppercase tracking-wider block">Total Donasi T.A. {{ $selectedTa?->nama }}</span>
            <div class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                Rp {{ number_format($totalDonasiMasukTa, 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-text-secondary mt-0.5 block">
                Kelompok: Rp {{ number_format($totalMasukKelompokTa, 0, ',', '.') }} • Bebas: Rp {{ number_format($totalMasukInsidental, 0, ',', '.') }}
            </span>
        </div>

        <div class="card p-4 bg-surface border border-border rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-text-secondary uppercase tracking-wider block">Saldo Kas Seluruh Kelompok</span>
            <div class="text-xl font-black text-primary mt-1">
                Rp {{ number_format($ringkasanKas['saldo_kelompok'] ?? 0, 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-text-secondary mt-0.5 block">Sisa dana siap dialokasikan ke siswa</span>
        </div>

        <div class="card p-4 bg-surface border border-border rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-text-secondary uppercase tracking-wider block">Saldo Kas Bebas (Insidental)</span>
            <div class="text-xl font-black text-amber-600 dark:text-amber-400 mt-1">
                Rp {{ number_format($saldoInsidental, 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-text-secondary mt-0.5 block">Dana cadangan untuk siswa darurat/umum</span>
        </div>

        <div class="card p-4 bg-surface border border-border rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-text-secondary uppercase tracking-wider block">Total Kas OTA Tersedia</span>
            <div class="text-xl font-black text-text-main mt-1">
                Rp {{ number_format($ringkasanKas['total_kas'] ?? 0, 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-text-secondary mt-0.5 block">Akumulasi seluruh saldo kas riil</span>
        </div>
    </div>

    <!-- ================= TAB 1: REKAP PER KELOMPOK (DEFAULT) ================= -->
    @if($activeTab === 'kelompok')
        <div class="card border border-border rounded-2xl p-6 bg-surface shadow-xs space-y-4">
            <!-- Toolbar Filter & Action -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <!-- Dropdown Tahun Ajaran -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-text-secondary uppercase whitespace-nowrap">Tahun Ajaran:</span>
                        <select wire:model.live="tahunAjaranId" class="select select-bordered select-sm text-xs font-bold w-44">
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Input Kelompok -->
                    <div class="relative w-full sm:w-64">
                        <input type="text" 
                               wire:model.live.debounce.300ms="searchKelompok" 
                               placeholder="Cari kelompok / koordinator / siswa..." 
                               class="input input-bordered input-sm text-xs w-full pl-8">
                        <svg class="w-4 h-4 text-text-secondary absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 w-full sm:w-auto justify-end">
                    <!-- Tombol Ekspor Excel -->
                    <button type="button" 
                            wire:click="exportKelompokExcel" 
                            wire:loading.attr="disabled"
                            class="btn btn-outline btn-sm text-xs flex items-center gap-1.5 border-emerald-300 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span wire:loading.remove wire:target="exportKelompokExcel">Ekspor Excel</span>
                        <span wire:loading wire:target="exportKelompokExcel">Mengunduh...</span>
                    </button>

                    <!-- Tombol Catat Donasi Masuk -->
                    <a href="{{ route('beasiswa-ota.donasi-masuk.create') }}" wire:navigate class="btn btn-primary btn-sm text-xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Catat Donasi Masuk
                    </a>
                </div>
            </div>

            <!-- Tabel Monitoring Donasi Masuk Per Kelompok -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-border bg-surface-hover/50 text-[11px] font-bold text-text-secondary uppercase">
                            <th class="py-3 px-3">Kelompok & Koordinator</th>
                            <th class="py-3 px-3">Siswa Binaan</th>
                            <th class="py-3 px-3 text-center">Donatur</th>
                            <th class="py-3 px-3 text-right">Target Biaya</th>
                            <th class="py-3 px-3 text-right">Total Masuk (T.A.)</th>
                            <th class="py-3 px-3 text-right">Saldo Kas</th>
                            <th class="py-3 px-3">Setoran Terakhir</th>
                            <th class="py-3 px-3 text-center min-w-[140px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($kelompoksPaginated as $k)
                            @php
                                $target = (float) $k->target_nominal;
                                $komitmen = (float) $k->total_komitmen;
                                $totalMasuk = (float) $k->donasiMasuks->sum('nominal');
                                $saldoKas = (float) $k->saldo_kas;
                                $persen = $target > 0 ? min(100, round(($totalMasuk / $target) * 100)) : 0;
                                $siswa = $k->siswaPenerima?->siswa;

                                $lastDonasi = $k->donasiMasuks->sortByDesc('tanggal_donasi')->first();
                            @endphp
                            <tr class="hover:bg-surface-hover/30 transition">
                                <!-- Kelompok & Koordinator -->
                                <td class="py-3.5 px-3">
                                    <div class="font-bold text-sm text-text-main flex items-center gap-1.5">
                                        <span class="font-mono text-xs px-1.5 py-0.5 rounded bg-surface-hover border border-border text-text-secondary">
                                            {{ $k->kode_kelompok }}
                                        </span>
                                        <span>{{ $k->nama_kelompok }}</span>
                                    </div>
                                    <div class="text-[11px] text-text-secondary mt-1 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <span>Koordinator: <strong class="text-text-main">{{ $k->koordinator_nama ?: ($k->koordinator?->nama ?: 'Belum ditentukan') }}</strong></span>
                                        @if($k->koordinator_telepon)
                                            <span>({{ $k->koordinator_telepon }})</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Siswa Binaan -->
                                <td class="py-3.5 px-3">
                                    @if($siswa)
                                        <div class="font-bold text-xs text-text-main">{{ $siswa->nama }}</div>
                                        <div class="text-[10px] text-text-secondary">{{ $siswa->rombel?->nama ?? 'Tanpa Rombel' }}</div>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200">
                                            Belum Dipetakan
                                        </span>
                                    @endif
                                </td>

                                <!-- Donatur & Komitmen -->
                                <td class="py-3.5 px-3 text-center">
                                    <span class="font-bold text-xs text-text-main block">{{ $k->anggotaKelompok->count() }} Donatur</span>
                                    <span class="text-[10px] text-text-secondary block">Rp {{ number_format($komitmen, 0, ',', '.') }}/bln</span>
                                </td>

                                <!-- Target Biaya -->
                                <td class="py-3.5 px-3 text-right">
                                    <span class="font-bold text-xs text-text-main">
                                        Rp {{ number_format($target, 0, ',', '.') }}
                                    </span>
                                </td>

                                <!-- Total Donasi Masuk (T.A.) & Progress -->
                                <td class="py-3.5 px-3 text-right">
                                    <div class="font-black text-sm text-emerald-600 dark:text-emerald-400">
                                        Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                                    </div>
                                    <!-- Progress Bar -->
                                    <div class="w-full bg-surface-hover rounded-full h-1.5 mt-1 border border-border overflow-hidden" title="Realisasi: {{ $persen }}% dari Target">
                                        <div class="h-1.5 rounded-full {{ $persen >= 100 ? 'bg-emerald-500' : ($persen >= 50 ? 'bg-primary' : 'bg-amber-500') }}" style="width: {{ $persen }}%"></div>
                                    </div>
                                    <span class="text-[9px] text-text-secondary mt-0.5 block">{{ $persen }}% terpenuhi</span>
                                </td>

                                <!-- Saldo Kas Kelompok -->
                                <td class="py-3.5 px-3 text-right">
                                    <div class="font-extrabold text-xs text-primary">
                                        Rp {{ number_format($saldoKas, 0, ',', '.') }}
                                    </div>
                                    <span class="text-[9px] text-text-secondary">Kas aktif saat ini</span>
                                </td>

                                <!-- Setoran Terakhir -->
                                <td class="py-3.5 px-3">
                                    @if($lastDonasi)
                                        <div class="font-semibold text-xs text-text-main">{{ $lastDonasi->tanggal_donasi->translatedFormat('d M Y') }}</div>
                                        <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">
                                            + Rp {{ number_format($lastDonasi->nominal, 0, ',', '.') }}
                                        </div>
                                    @else
                                        <span class="text-text-secondary text-[11px] italic">Belum ada setoran</span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Tombol Drawer Rincian -->
                                        <button type="button" 
                                                wire:click="openDrawer({{ $k->id }})" 
                                                class="btn btn-xs btn-outline border-border hover:bg-surface-hover text-text-main text-[11px] gap-1 px-2"
                                                title="Lihat Matrix Status Iuran 12 Bulan & Riwayat Setoran">
                                            <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Rincian
                                        </button>

                                        <!-- Tombol Shortcut Catat Setoran -->
                                        <a href="{{ route('beasiswa-ota.donasi-masuk.create', ['kelompok_id' => $k->id]) }}" 
                                           wire:navigate 
                                           class="btn btn-xs btn-primary text-[11px] gap-1 px-2"
                                           title="Setor Iuran Langsung untuk Kelompok Ini">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Setor
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-text-secondary text-xs">
                                    Tidak ada data kelompok donatur ditemukan.
                                </td>
                            </tr>
                        @endforelse

                        <!-- BARIS KHUSUS: KAS BEBAS (INSIDENTAL) -->
                        <tr class="bg-amber-50/60 dark:bg-amber-950/20 border-t-2 border-amber-300 dark:border-amber-800">
                            <td class="py-3 px-3">
                                <div class="font-bold text-xs text-amber-900 dark:text-amber-200 flex items-center gap-1.5">
                                    <span class="font-mono text-[10px] px-1.5 py-0.5 rounded bg-amber-200/70 text-amber-900 font-bold border border-amber-300">
                                        KAS-BEBAS
                                    </span>
                                    <span>Kas Bebas (Donasi Insidental Non-Kelompok)</span>
                                </div>
                                <div class="text-[10px] text-amber-700 dark:text-amber-300 mt-0.5">
                                    Donasi lepas yang tidak terikat komitmen kelompok asuh manapun.
                                </div>
                            </td>
                            <td class="py-3 px-3 text-text-secondary text-[11px]">
                                Cadangan / Siswa Darurat
                            </td>
                            <td class="py-3 px-3 text-center text-text-secondary text-[11px]">-</td>
                            <td class="py-3 px-3 text-right text-text-secondary text-[11px]">-</td>
                            <td class="py-3 px-3 text-right">
                                <div class="font-black text-xs text-amber-800 dark:text-amber-200">
                                    Rp {{ number_format($totalMasukInsidental, 0, ',', '.') }}
                                </div>
                                <span class="text-[9px] text-amber-700 dark:text-amber-300">Masuk T.A. {{ $selectedTa?->nama }}</span>
                            </td>
                            <td class="py-3 px-3 text-right">
                                <div class="font-black text-xs text-amber-800 dark:text-amber-200">
                                    Rp {{ number_format($saldoInsidental, 0, ',', '.') }}
                                </div>
                                <span class="text-[9px] text-amber-700 dark:text-amber-300">Saldo siap pakai</span>
                            </td>
                            <td class="py-3 px-3">
                                @if($lastDonasiInsidental)
                                    <div class="font-semibold text-xs text-amber-900 dark:text-amber-200">{{ $lastDonasiInsidental->tanggal_donasi->translatedFormat('d M Y') }}</div>
                                    <div class="text-[10px] text-emerald-700 dark:text-emerald-300 font-bold">
                                        + Rp {{ number_format($lastDonasiInsidental->nominal, 0, ',', '.') }}
                                    </div>
                                @else
                                    <span class="text-text-secondary text-[11px] italic">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center">
                                <button type="button" 
                                        wire:click="openDrawer(0)" 
                                        class="btn btn-xs btn-outline border-amber-300 text-amber-800 dark:text-amber-200 hover:bg-amber-100 text-[11px] gap-1 px-2.5">
                                    <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Rincian
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $kelompoksPaginated->links() }}
            </div>
        </div>
    @endif

    <!-- ================= TAB 2: RIWAYAT TRANSAKSI (PER DONATUR) ================= -->
    @if($activeTab === 'transaksi')
        <div class="card border border-border rounded-2xl p-6 bg-surface shadow-xs space-y-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <!-- Filter & Search Transaksi -->
                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari donatur / no. transaksi / koordinator / batch..." class="input input-bordered input-sm text-xs w-full sm:w-64">

                    <select wire:model.live="filterKelompok" class="select select-bordered select-sm text-xs">
                        <option value="">Semua Kelompok</option>
                        @foreach($kelompoks as $klp)
                            <option value="{{ $klp->id }}">{{ $klp->nama_kelompok }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="filterTipe" class="select select-bordered select-sm text-xs">
                        <option value="">Semua Tipe Donatur</option>
                        <option value="rutin">Rutin (Kelompok)</option>
                        <option value="insidental">Insidental</option>
                    </select>

                    <select wire:model.live="filterMetode" class="select select-bordered select-sm text-xs">
                        <option value="">Semua Metode Setor</option>
                        <option value="perorangan">Perorangan (Individu)</option>
                        <option value="kolektif">Kolektif via Koordinator</option>
                    </select>
                </div>

                <a href="{{ route('beasiswa-ota.donasi-masuk.create') }}" wire:navigate class="btn btn-primary btn-sm text-xs flex items-center gap-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Catat Donasi Masuk
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-border bg-surface-hover/50 text-xs font-semibold text-text-secondary uppercase">
                            <th class="py-3 px-4">No. Transaksi & Tanggal</th>
                            <th class="py-3 px-4">Nama Donatur</th>
                            <th class="py-3 px-4">Periode & Alokasi</th>
                            <th class="py-3 px-4">Metode & Penyetor</th>
                            <th class="py-3 px-4 text-right">Nominal Donasi</th>
                            <th class="py-3 px-4">Keterangan</th>
                            <th class="py-3 px-4 text-center">Cetak Dokumen</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($donasis as $d)
                            <tr class="hover:bg-surface-hover/30 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-mono text-xs font-bold text-text-main">{{ $d->nomor_transaksi }}</div>
                                    <div class="text-xs text-text-secondary">{{ $d->tanggal_donasi->translatedFormat('d F Y') }}</div>
                                    @if($d->batch_kode)
                                        <div class="font-mono text-[10px] text-indigo-600 dark:text-indigo-400 mt-0.5" title="Kode Batch Kolektif">
                                            {{ $d->batch_kode }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-text-main">{{ $d->donatur->nama }}</div>
                                    <span class="inline-block mt-0.5 px-2 py-0.2 rounded text-[10px] font-semibold {{ $d->tipe_donatur === 'rutin' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300' }}">
                                        Donatur {{ ucfirst($d->tipe_donatur) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($d->kelompok)
                                        <div class="font-semibold text-xs text-text-main">{{ $d->kelompok->nama_kelompok }}</div>
                                        <div class="text-[10px] text-text-secondary">{{ $d->kelompok->kode_kelompok }}</div>
                                    @else
                                        <span class="text-xs font-medium text-amber-700 dark:text-amber-300">
                                            Kas Bebas (Insidental)
                                        </span>
                                    @endif

                                    @if($d->keterangan_periode)
                                        <div class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ $d->keterangan_periode }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($d->is_kolektif)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 dark:bg-purple-950/50 dark:text-purple-300">
                                            Kolektif Kelompok
                                        </span>
                                        <div class="text-[11px] text-text-secondary mt-0.5">
                                            Koordinator: <span class="font-medium text-text-main">{{ $d->penyetor_nama ?: '-' }}</span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            Perorangan Langsung
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($d->nominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-xs text-text-secondary max-w-xs truncate">
                                    {{ $d->keterangan ?: '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex flex-wrap items-center justify-center gap-1.5">
                                        <a href="{{ route('beasiswa-ota.cetak.tanda-terima', $d->id) }}" target="_blank" 
                                           title="Cetak Tanda Terima Pribadi Donatur (A5)"
                                           class="btn btn-xs btn-outline border-indigo-200 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-50 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            Tanda Terima
                                        </a>

                                        @if($d->is_kolektif && $d->batch_kode)
                                            <a href="{{ route('beasiswa-ota.cetak.tanda-terima-kolektif', $d->batch_kode) }}" target="_blank" 
                                               title="Cetak Tanda Terima Kolektif Kelompok untuk Koordinator (A4)"
                                               class="btn btn-xs btn-outline border-purple-200 text-purple-700 dark:text-purple-300 hover:bg-purple-50 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                Kolektif
                                            </a>
                                        @endif

                                        <a href="{{ route('beasiswa-ota.cetak.sertifikat', $d->id) }}" target="_blank" 
                                           title="Cetak Sertifikat Piagam Penghargaan Donatur (A4)"
                                           class="btn btn-xs btn-outline border-amber-200 text-amber-700 dark:text-amber-300 hover:bg-amber-50 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                            Sertifikat
                                        </a>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @can('beasiswa_ota.kelola')
                                        @if($d->is_kolektif && $d->batch_kode)
                                            <button type="button" 
                                                    wire:click="deleteBatch('{{ $d->batch_kode }}')" 
                                                    wire:confirm="PERINGATAN: Transaksi ini merupakan bagian dari setoran kolektif batch {{ $d->batch_kode }}. Menghapus akan membatalkan SELURUH setoran donatur dalam batch ini dan memotong saldo kas kelompok. Lanjutkan?" 
                                                    class="btn btn-ghost btn-xs text-rose-500 hover:text-rose-700 hover:bg-rose-50 text-[10px]"
                                                    title="Batal Batch Kolektif">
                                                Batal Batch
                                            </button>
                                        @else
                                            <button type="button" 
                                                    wire:click="delete({{ $d->id }})" 
                                                    wire:confirm="Yakin ingin membatalkan dan menghapus catatan donasi ini?" 
                                                    class="btn btn-ghost btn-xs text-rose-500 hover:text-rose-700 hover:bg-rose-50"
                                                    title="Hapus Donasi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-text-secondary text-sm">
                                    Tidak ada data donasi masuk yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $donasis->links() }}
            </div>
        </div>
    @endif

    <!-- ================= SLIDE-OVER DRAWER RINCIAN KELOMPOK ================= -->
    @if($showDrawer)
        <div class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-xs transition-opacity" wire:click="closeDrawer"></div>

            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                <div class="w-screen max-w-4xl bg-surface border-l border-border shadow-2xl flex flex-col">
                    <!-- Drawer Header -->
                    <div class="p-6 border-b border-border bg-surface-hover/40 flex items-start justify-between gap-4">
                        <div>
                            @if($drawerKelompok)
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs px-2 py-0.5 rounded bg-primary/10 text-primary font-bold border border-primary/20">
                                        {{ $drawerKelompok->kode_kelompok }}
                                    </span>
                                    <h2 class="text-xl font-black text-text-main tracking-tight">{{ $drawerKelompok->nama_kelompok }}</h2>
                                </div>
                                <div class="text-xs text-text-secondary mt-1 flex flex-wrap items-center gap-3">
                                    <span>Koordinator: <strong class="text-text-main">{{ $drawerKelompok->koordinator_nama ?: ($drawerKelompok->koordinator?->nama ?: '-') }}</strong></span>
                                    <span>&bull;</span>
                                    <span>Siswa Binaan: <strong class="text-text-main">{{ $drawerKelompok->siswaPenerima?->siswa?->nama ?? 'Belum Dipetakan' }}</strong> ({{ $drawerKelompok->siswaPenerima?->siswa?->rombel?->nama ?? '-' }})</span>
                                    <span>&bull;</span>
                                    <span>T.A.: <strong class="text-primary">{{ $selectedTa?->nama }}</strong></span>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-bold border border-amber-300">
                                        KAS-BEBAS
                                    </span>
                                    <h2 class="text-xl font-black text-text-main tracking-tight">Kas Bebas (Donasi Insidental)</h2>
                                </div>
                                <p class="text-xs text-text-secondary mt-1">Daftar penerimaan donasi insidental lepas pada T.A. {{ $selectedTa?->nama }}.</p>
                            @endif
                        </div>

                        <button type="button" wire:click="closeDrawer" class="btn btn-ghost btn-sm btn-circle text-text-secondary hover:text-text-main text-lg font-bold">
                            &times;
                        </button>
                    </div>

                    <!-- Drawer Sub-Tab Switcher (Jika Kelompok Asuh) -->
                    @if($drawerKelompok)
                        <div class="px-6 pt-4 pb-2 border-b border-border flex items-center justify-between gap-3 bg-surface">
                            <div class="flex p-1 bg-surface-hover/80 rounded-xl border border-border">
                                <button type="button" 
                                        wire:click="$set('drawerSubTab', 'matrix')" 
                                        class="py-1.5 px-3 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $drawerSubTab === 'matrix' ? 'bg-surface text-primary shadow-xs border border-border' : 'text-text-secondary hover:text-text-main' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Matrix Status Iuran Anggota (12 Bulan)
                                </button>
                                <button type="button" 
                                        wire:click="$set('drawerSubTab', 'transaksi')" 
                                        class="py-1.5 px-3 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $drawerSubTab === 'transaksi' ? 'bg-surface text-primary shadow-xs border border-border' : 'text-text-secondary hover:text-text-main' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    Riwayat Bukti Setoran ({{ $drawerTransactions->count() }})
                                </button>
                            </div>

                            <a href="{{ route('beasiswa-ota.donasi-masuk.create', ['kelompok_id' => $drawerKelompok->id]) }}" 
                               wire:navigate 
                               class="btn btn-primary btn-xs text-xs gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Catat Setoran Kelompok Ini
                            </a>
                        </div>
                    @endif

                    <!-- Drawer Content -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-6">
                        @if($drawerKelompok && $drawerSubTab === 'matrix')
                            <!-- SUB-TAB 1: MATRIX SPREADSHEET 12 BULAN PER ANGGOTA -->
                            <div class="space-y-3">
                                <div>
                                    <h3 class="text-sm font-bold text-text-main">Status Checklist 12 Bulan (Juli s.d. Juni) T.A. {{ $selectedTa?->nama }}</h3>
                                    <p class="text-xs text-text-secondary mt-0.5">
                                        Badge hijau <span class="text-emerald-600 font-bold">Lunas</span> menunjukkan iuran donatur pada bulan tersebut telah disetorkan.
                                    </p>
                                </div>

                                <div class="border border-border rounded-2xl overflow-x-auto shadow-xs">
                                    <table class="w-full text-left text-xs border-collapse">
                                        <thead>
                                            <tr class="bg-surface-hover/80 text-text-secondary uppercase border-b border-border text-[10px]">
                                                <th rowspan="2" class="py-2.5 px-3 border-r border-border min-w-[170px]">Donatur & Komitmen</th>
                                                <th colspan="6" class="py-1 px-1 text-center bg-blue-50/50 dark:bg-blue-950/20 border-r border-border font-bold text-blue-900 dark:text-blue-200">
                                                    Semester Ganjil
                                                </th>
                                                <th colspan="6" class="py-1 px-1 text-center bg-indigo-50/50 dark:bg-indigo-950/20 border-r border-border font-bold text-indigo-900 dark:text-indigo-200">
                                                    Semester Genap
                                                </th>
                                                <th rowspan="2" class="py-2.5 px-2 text-center border-r border-border min-w-[55px]">Lunas</th>
                                                <th rowspan="2" class="py-2.5 px-3 text-right min-w-[110px]">Total Disetor</th>
                                            </tr>
                                            <tr class="bg-surface-hover/50 text-[10px] text-text-secondary border-b border-border">
                                                @foreach($drawerMonthsInfo['ganjil'] ?? [] as $bm)
                                                    <th class="py-1 px-1 text-center border-r border-border/60 bg-blue-50/30 dark:bg-blue-950/10 min-w-[34px]">{{ $bm['short'] }}</th>
                                                @endforeach
                                                @foreach($drawerMonthsInfo['genap'] ?? [] as $bm)
                                                    <th class="py-1 px-1 text-center border-r border-border/60 bg-indigo-50/30 dark:bg-indigo-950/10 min-w-[34px]">{{ $bm['short'] }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border">
                                            @forelse($drawerMembers as $m)
                                                <tr class="hover:bg-surface-hover/30 transition">
                                                    <td class="py-2.5 px-3 border-r border-border">
                                                        <div class="font-bold text-xs text-text-main">{{ $m['nama'] }}</div>
                                                        <div class="text-[10px] text-text-secondary">
                                                            Komitmen: <strong class="text-primary">Rp {{ number_format($m['nominal_komitmen'], 0, ',', '.') }}</strong>/bln
                                                        </div>
                                                    </td>

                                                    <!-- Ganjil -->
                                                    @foreach($drawerMonthsInfo['ganjil'] ?? [] as $bm)
                                                        @php $isPaid = in_array($bm['key'], $m['paid_months']); @endphp
                                                        <td class="py-1 px-1 text-center border-r border-border/60">
                                                            @if($isPaid)
                                                                <span class="inline-block px-1 py-0.5 rounded text-[8px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300" title="{{ $bm['name'] }} Lunas">
                                                                    ✓ Lunas
                                                                </span>
                                                            @else
                                                                <span class="text-text-secondary/40 text-[10px]">-</span>
                                                            @endif
                                                        </td>
                                                    @endforeach

                                                    <!-- Genap -->
                                                    @foreach($drawerMonthsInfo['genap'] ?? [] as $bm)
                                                        @php $isPaid = in_array($bm['key'], $m['paid_months']); @endphp
                                                        <td class="py-1 px-1 text-center border-r border-border/60">
                                                            @if($isPaid)
                                                                <span class="inline-block px-1 py-0.5 rounded text-[8px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300" title="{{ $bm['name'] }} Lunas">
                                                                    ✓ Lunas
                                                                </span>
                                                            @else
                                                                <span class="text-text-secondary/40 text-[10px]">-</span>
                                                            @endif
                                                        </td>
                                                    @endforeach

                                                    <!-- Total Bulan Lunas -->
                                                    <td class="py-2.5 px-2 text-center border-r border-border font-bold font-mono text-xs {{ $m['total_paid_months'] >= 12 ? 'text-emerald-600' : 'text-text-main' }}">
                                                        {{ $m['total_paid_months'] }}/12
                                                    </td>

                                                    <!-- Total Disetor -->
                                                    <td class="py-2.5 px-3 text-right font-black text-xs text-emerald-600 dark:text-emerald-400">
                                                        Rp {{ number_format($m['total_nominal_disetor'], 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="15" class="py-6 text-center text-text-secondary text-xs">
                                                        Belum ada anggota donatur terdaftar di kelompok ini.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <!-- SUB-TAB 2 / KAS BEBAS: RIWAYAT BUKTI TRANSAKSI SETORAN -->
                            <div class="space-y-3">
                                <div>
                                    <h3 class="text-sm font-bold text-text-main">Riwayat Bukti Transaksi Setoran Masuk</h3>
                                    <p class="text-xs text-text-secondary mt-0.5">Daftar setoran masuk yang tercatat pada Tahun Ajaran {{ $selectedTa?->nama }}.</p>
                                </div>

                                <div class="border border-border rounded-2xl overflow-x-auto shadow-xs">
                                    <table class="w-full text-left text-xs">
                                        <thead>
                                            <tr class="bg-surface-hover/80 text-text-secondary uppercase border-b border-border text-[10px] font-bold">
                                                <th class="py-2.5 px-3">No. Transaksi & Tanggal</th>
                                                <th class="py-2.5 px-3">Donatur / Penyetor</th>
                                                <th class="py-2.5 px-3">Periode Bulan</th>
                                                <th class="py-2.5 px-3 text-right">Nominal</th>
                                                <th class="py-2.5 px-3 text-center">Bukti / Cetak</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border">
                                            @forelse($drawerTransactions as $trx)
                                                <tr class="hover:bg-surface-hover/30 transition">
                                                    <td class="py-2.5 px-3">
                                                        <div class="font-mono text-xs font-bold text-text-main">{{ $trx->nomor_transaksi }}</div>
                                                        <div class="text-[10px] text-text-secondary">{{ $trx->tanggal_donasi->translatedFormat('d M Y') }}</div>
                                                        @if($trx->batch_kode)
                                                            <div class="font-mono text-[9px] text-indigo-600 mt-0.5">{{ $trx->batch_kode }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="py-2.5 px-3">
                                                        <div class="font-bold text-xs text-text-main">{{ $trx->donatur?->nama ?? '-' }}</div>
                                                        @if($trx->is_kolektif)
                                                            <span class="text-[10px] text-purple-700 font-medium">Kolektif via {{ $trx->penyetor_nama ?: '-' }}</span>
                                                        @else
                                                            <span class="text-[10px] text-slate-600">Perorangan</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-2.5 px-3">
                                                        <span class="text-xs text-text-main">{{ $trx->keterangan_periode ?: '-' }}</span>
                                                    </td>
                                                    <td class="py-2.5 px-3 text-right font-black text-xs text-emerald-600 dark:text-emerald-400">
                                                        Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                                    </td>
                                                    <td class="py-2.5 px-3 text-center">
                                                        <div class="flex items-center justify-center gap-1">
                                                            <a href="{{ route('beasiswa-ota.cetak.tanda-terima', $trx->id) }}" target="_blank" class="btn btn-xs btn-ghost text-indigo-600 text-[10px]" title="Cetak Tanda Terima Pribadi (A5)">
                                                                Tanda Terima
                                                            </a>
                                                            @if($trx->is_kolektif && $trx->batch_kode)
                                                                <a href="{{ route('beasiswa-ota.cetak.tanda-terima-kolektif', $trx->batch_kode) }}" target="_blank" class="btn btn-xs btn-ghost text-purple-600 text-[10px]" title="Cetak Tanda Terima Kolektif (A4)">
                                                                    Kolektif
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="py-6 text-center text-text-secondary text-xs">
                                                        Belum ada transaksi donasi masuk untuk kelompok ini di T.A. {{ $selectedTa?->nama }}.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Drawer Footer -->
                    <div class="p-4 border-t border-border bg-surface-hover/30 flex justify-end">
                        <button type="button" wire:click="closeDrawer" class="btn btn-secondary btn-sm text-xs">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
