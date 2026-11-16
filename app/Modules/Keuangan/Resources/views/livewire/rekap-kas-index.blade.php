<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-border pb-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-text-main flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-primary/10 text-primary">📑</span>
                Rekapitulasi &amp; Buku Kas Penerimaan
            </h1>
            <p class="text-xs sm:text-sm text-text-secondary mt-1">
                Laporan pergerakan kas masuk, rekonsiliasi kas tunai &amp; rekening bank, serta pencetakan rekapitulasi periodik.
            </p>
        </div>
        <a href="{{ route('keuangan.cetak.rekap-kas', ['start_date' => $startDate, 'end_date' => $endDate, 'rekening_id' => $selectedRekeningId]) }}"
           target="_blank"
           class="px-4 py-2 rounded-xl text-xs font-bold bg-primary hover:bg-primary-hover text-white shadow-xs flex items-center gap-2 transition-all">
            <span>🖨️</span>
            <span>Cetak Rekap Kas PDF</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-surface p-4 rounded-2xl border border-border shadow-xs grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
            <label class="block text-[11px] font-bold text-text-muted uppercase mb-1">Dari Tanggal</label>
            <input type="date" wire:model.live="startDate" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-text-muted uppercase mb-1">Sampai Tanggal</label>
            <input type="date" wire:model.live="endDate" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-text-muted uppercase mb-1">Akun Kas / Bank</label>
            <select wire:model.live="selectedRekeningId" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                <option value="">Semua Akun Kas / Bank</option>
                @foreach($rekenings as $rek)
                    <option value="{{ $rek->id }}">{{ $rek->nama_bank }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- KPI Ringkasan Periode -->
    <div class="bg-surface p-5 rounded-2xl border border-border shadow-xs flex items-center justify-between">
        <div>
            <div class="text-xs font-bold text-text-muted uppercase">Total Penerimaan Periode Ini</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">
                Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}
            </div>
        </div>
        <div class="text-xs text-text-muted text-right">
            <span>{{ date('d M Y', strtotime($startDate)) }} — {{ date('d M Y', strtotime($endDate)) }}</span>
        </div>
    </div>

    <!-- Table Transaksi -->
    <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-bg text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                    <tr>
                        <th class="py-3 px-4 text-left">No. Kwitansi / Tgl</th>
                        <th class="py-3 px-4 text-left">Siswa / Rombel</th>
                        <th class="py-3 px-4 text-left">Metode / Akun Kas</th>
                        <th class="py-3 px-4 text-left">Rincian Pos</th>
                        <th class="py-3 px-4 text-right">Nominal</th>
                        <th class="py-3 px-4 text-center">Kwitansi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($transaksis as $t)
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="py-3 px-4 font-mono">
                                <div class="font-bold text-primary">{{ $t->nomor_transaksi }}</div>
                                <div class="text-[10px] text-text-muted">{{ $t->tanggal_bayar->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-text-main">{{ $t->siswa?->nama }}</div>
                                <div class="text-[10px] text-text-muted">{{ $t->siswa?->rombel?->nama ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 font-medium text-text-secondary">
                                {{ $t->rekening?->nama_bank ?? 'Kasir Loket' }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-[11px] text-text-main">
                                    {{ $t->items->map(fn($i) => ($i->posBiaya?->kode ?? 'POS') . ' (' . number_format($i->nominal_dibayar, 0, ',', '.') . ')')->join(', ') }}
                                </div>
                            </td>
                            <td class="py-3 px-4 text-right font-black text-emerald-600">
                                Rp {{ number_format($t->total_bayar, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('keuangan.cetak.kwitansi', $t->id) }}" target="_blank" class="p-1.5 rounded-lg border border-border hover:bg-surface text-text-muted hover:text-text-main inline-block" title="Cetak Kwitansi">
                                    🖨️
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-xs text-text-muted">
                                Tidak ada transaksi penerimaan pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $transaksis->links() }}
        </div>
    </div>
</div>

