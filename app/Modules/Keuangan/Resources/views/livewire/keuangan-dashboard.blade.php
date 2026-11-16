<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-border pb-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-text-main flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-primary/10 text-primary">💳</span>
                Dashboard Keuangan &amp; Pembayaran Siswa
            </h1>
            <p class="text-xs sm:text-sm text-text-secondary mt-1">
                Ikhtisar penerimaan kas, rasio ketuntasan administrasi, dan pemantauan piutang siswa aktif serta alumni.
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($menungguVerifikasiCount > 0)
                <a href="{{ route('keuangan.verifikasi') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white shadow-xs flex items-center gap-2 transition-all animate-pulse">
                    <span>🔔</span>
                    <span>{{ $menungguVerifikasiCount }} Verifikasi Menunggu</span>
                </a>
            @endif
            <a href="{{ route('keuangan.kasir') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-primary hover:bg-primary-hover text-white shadow-xs flex items-center gap-2 transition-all">
                <span>➕</span>
                <span>Kasir Loket</span>
            </a>
        </div>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Penerimaan Bulan Ini -->
        <div class="bg-surface p-4 sm:p-5 rounded-2xl border border-border shadow-xs">
            <div class="flex items-center justify-between text-xs text-text-muted font-bold uppercase tracking-wider mb-2">
                <span>Penerimaan Bulan Ini</span>
                <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-600">💰</span>
            </div>
            <div class="text-xl sm:text-2xl font-black text-emerald-600">
                Rp {{ number_format($penerimaanBulanIni, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-text-muted mt-2 flex items-center gap-1">
                <span>Bulan {{ now()->translatedFormat('F Y') }}</span>
            </div>
        </div>

        <!-- Piutang Bulan Ini -->
        <div class="bg-surface p-4 sm:p-5 rounded-2xl border border-border shadow-xs">
            <div class="flex items-center justify-between text-xs text-text-muted font-bold uppercase tracking-wider mb-2">
                <span>Piutang Bulan Ini</span>
                <span class="p-1.5 rounded-lg bg-rose-500/10 text-rose-600">⏳</span>
            </div>
            <div class="text-xl sm:text-2xl font-black text-rose-600">
                Rp {{ number_format($piutangBulanIni, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-text-muted mt-2">
                Tagihan jatuh tempo bulan berjalan
            </div>
        </div>

        <!-- Total Piutang Aktif Kumulatif -->
        <div class="bg-surface p-4 sm:p-5 rounded-2xl border border-border shadow-xs">
            <div class="flex items-center justify-between text-xs text-text-muted font-bold uppercase tracking-wider mb-2">
                <span>Total Piutang Siswa</span>
                <span class="p-1.5 rounded-lg bg-amber-500/10 text-amber-600">📊</span>
            </div>
            <div class="text-xl sm:text-2xl font-black text-text-main">
                Rp {{ number_format($totalPiutangAktif, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-text-muted mt-2">
                Kumulatif seluruh siswa aktif
            </div>
        </div>

        <!-- Piutang Alumni -->
        <div class="bg-surface p-4 sm:p-5 rounded-2xl border border-border shadow-xs">
            <div class="flex items-center justify-between text-xs text-text-muted font-bold uppercase tracking-wider mb-2">
                <span>Piutang Alumni</span>
                <span class="p-1.5 rounded-lg bg-indigo-500/10 text-indigo-600">🎓</span>
            </div>
            <div class="text-xl sm:text-2xl font-black text-indigo-600">
                Rp {{ number_format($totalPiutangAlumni, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-text-muted mt-2">
                Tunggakan siswa yang telah lulus
            </div>
        </div>
    </div>

    <!-- Ketuntasan & Saldo Kas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Progress Ketuntasan -->
        <div class="bg-surface p-5 rounded-2xl border border-border shadow-xs">
            <h3 class="text-xs font-bold uppercase tracking-wider text-text-muted mb-4">Rasio Ketuntasan Pembayaran</h3>
            <div class="flex items-center justify-between mb-2">
                <span class="text-2xl font-black text-primary">{{ $rasioKetuntasan }}%</span>
                <span class="text-xs text-text-muted font-semibold">dari total kewajiban</span>
            </div>
            <div class="w-full h-3 bg-surface-hover rounded-full overflow-hidden">
                <div class="h-full bg-primary rounded-full transition-all duration-500" style="width: {{ min(100, $rasioKetuntasan) }}%;"></div>
            </div>
            <div class="mt-4 pt-4 border-t border-border/60 text-xs text-text-secondary flex justify-between">
                <span>Target Kolektibilitas</span>
                <span class="font-bold text-text-main">95.0%</span>
            </div>
        </div>

        <!-- Saldo Kas & Bank Sekolah -->
        <div class="lg:col-span-2 bg-surface p-5 rounded-2xl border border-border shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-text-muted">Posisi Saldo Kas &amp; Bank Sekolah</h3>
                <span class="text-xs font-bold text-emerald-600">Total: Rp {{ number_format($totalSaldoKas, 0, ',', '.') }}</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($rekenings as $rek)
                    <div class="p-3.5 rounded-xl border border-border bg-bg/50 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-xs text-text-main flex items-center gap-1.5">
                                <span>{{ $rek->is_kas_tunai ? '💵' : '🏦' }}</span>
                                <span>{{ $rek->nama_bank }}</span>
                            </div>
                            <div class="text-[11px] text-text-muted mt-0.5 font-mono">
                                {{ $rek->nomor_rekening ?: 'Kas Tunai Loket' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-black text-text-main">
                                Rp {{ number_format($rek->saldo_saat_ini, 0, ',', '.') }}
                            </div>
                            <span class="text-[10px] text-emerald-600 font-semibold">Aktif</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Tunggakan per Rombel & Transaksi Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Rombel Tunggakan -->
        <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-border flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-text-main">Tunggakan Terbesar per Rombel</h2>
                    <p class="text-xs text-text-muted mt-0.5">Pantauan rombel untuk penagihan prioritas</p>
                </div>
                <a href="{{ route('keuangan.tagihan') }}" class="text-xs font-semibold text-primary hover:underline">Lihat Semua Tagihan &rsaquo;</a>
            </div>
            <div class="divide-y divide-border">
                @forelse($tunggakanPerRombel as $rombel)
                    <div class="p-3.5 sm:px-5 flex items-center justify-between hover:bg-surface-hover transition-colors">
                        <div>
                            <div class="text-xs font-bold text-text-main">{{ $rombel->nama }}</div>
                            <div class="text-[11px] text-text-muted">{{ $rombel->jurusan?->nama ?? '-' }} &bull; Tingkat {{ $rombel->tingkat }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-black text-rose-600">
                                Rp {{ number_format($rombel->total_tunggakan, 0, ',', '.') }}
                            </div>
                            <span class="text-[10px] text-text-muted">Total Tunggakan</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-text-muted">Belum ada data piutang rombel.</div>
                @endforelse
            </div>
        </div>

        <!-- Transaksi Terbaru -->
        <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-border flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-text-main">Riwayat Pembayaran Terbaru</h2>
                    <p class="text-xs text-text-muted mt-0.5">8 transaksi pembayaran kasir &amp; transfer terakhir</p>
                </div>
                <a href="{{ route('keuangan.rekap') }}" class="text-xs font-semibold text-primary hover:underline">Buku Kas &rsaquo;</a>
            </div>
            <div class="divide-y divide-border">
                @forelse($transaksiTerbaru as $t)
                    <div class="p-3.5 sm:px-5 flex items-center justify-between hover:bg-surface-hover transition-colors">
                        <div>
                            <div class="text-xs font-bold text-text-main">{{ $t->siswa?->nama }}</div>
                            <div class="text-[11px] text-text-muted">
                                <span class="font-mono text-primary font-semibold">{{ $t->nomor_transaksi }}</span> &bull; {{ $t->tanggal_bayar->format('d/m H:i') }}
                            </div>
                        </div>
                        <div class="text-right flex items-center gap-3">
                            <div>
                                <div class="text-xs font-black text-emerald-600">+Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</div>
                                <div class="text-[10px] text-text-muted">{{ $t->rekening?->nama_bank ?? 'Kasir' }}</div>
                            </div>
                            <a href="{{ route('keuangan.cetak.kwitansi', $t->id) }}" target="_blank" class="p-1.5 rounded-lg border border-border hover:bg-surface text-text-muted hover:text-text-main" title="Cetak Kwitansi">
                                🖨️
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-text-muted">Belum ada transaksi pembayaran.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

