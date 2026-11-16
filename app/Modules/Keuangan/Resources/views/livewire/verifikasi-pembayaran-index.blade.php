<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-border pb-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-text-main flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-primary/10 text-primary">📑</span>
                Verifikasi Bukti Transfer Orang Tua
            </h1>
            <p class="text-xs sm:text-sm text-text-secondary mt-1">
                Pemeriksaan dan persetujuan unggahan bukti transfer pembayaran dari portal orang tua.
            </p>
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 text-xs font-bold">
            ✅ {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-600 text-xs font-bold">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <!-- Filter Tab Status -->
    <div class="flex items-center gap-2 border-b border-border pb-2">
        <button type="button"
                wire:click="$set('filterStatus', 'menunggu')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $filterStatus === 'menunggu' ? 'bg-amber-500 text-white shadow-xs' : 'text-text-secondary hover:bg-surface-hover' }}">
            ⏳ Menunggu Verifikasi
        </button>
        <button type="button"
                wire:click="$set('filterStatus', 'disetujui')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $filterStatus === 'disetujui' ? 'bg-emerald-600 text-white shadow-xs' : 'text-text-secondary hover:bg-surface-hover' }}">
            ✅ Telah Disetujui
        </button>
        <button type="button"
                wire:click="$set('filterStatus', 'ditolak')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $filterStatus === 'ditolak' ? 'bg-rose-600 text-white shadow-xs' : 'text-text-secondary hover:bg-surface-hover' }}">
            ❌ Ditolak
        </button>
        <button type="button"
                wire:click="$set('filterStatus', '')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $filterStatus === '' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:bg-surface-hover' }}">
            Semua
        </button>
    </div>

    <!-- Table Pengajuan -->
    <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-bg text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                    <tr>
                        <th class="py-3 px-4 text-left">Kode / Tanggal</th>
                        <th class="py-3 px-4 text-left">Siswa / Rombel</th>
                        <th class="py-3 px-4 text-left">Pengirim / Bank</th>
                        <th class="py-3 px-4 text-left">Rekening Tujuan</th>
                        <th class="py-3 px-4 text-right">Nominal</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($pengajuans as $p)
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-mono font-bold text-text-main">{{ $p->kode_pengajuan }}</div>
                                <div class="text-[10px] text-text-muted">{{ $p->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-text-main">{{ $p->siswa?->nama }}</div>
                                <div class="text-[11px] text-text-muted">{{ $p->siswa?->rombel?->nama ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-text-main">{{ $p->nama_pemilik_rekening }}</div>
                                <div class="text-[10px] text-text-muted">{{ $p->bank_pengirim ?: 'Bank' }} {{ $p->nomor_rekening_pengirim ? '(' . $p->nomor_rekening_pengirim . ')' : '' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-medium text-text-secondary">{{ $p->rekeningTujuan?->nama_bank ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4 text-right font-black text-emerald-600">
                                Rp {{ number_format($p->nominal_transfer, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($p->status === 'menunggu')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-600">Menunggu</span>
                                @elseif($p->status === 'disetujui')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600">Disetujui</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600" title="{{ $p->alasan_penolakan }}">Ditolak</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button"
                                            wire:click="previewPengajuan({{ $p->id }})"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-border bg-bg hover:bg-surface-hover text-text-main transition-colors">
                                        Periksa
                                    </button>
                                    @if($p->status === 'disetujui' && $p->transaksi_id)
                                        <a href="{{ route('keuangan.cetak.kwitansi', $p->transaksi_id) }}" target="_blank" class="p-1 rounded-lg border border-border hover:bg-surface-hover text-text-muted" title="Cetak Kwitansi">
                                            🖨️
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-xs text-text-muted">
                                Tidak ada data pengajuan bukti transfer pada status ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $pengajuans->links() }}
        </div>
    </div>

    <!-- Modal Preview & Approval -->
    @if($modalPreviewOpen && $selectedPengajuan)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
            <div class="bg-surface rounded-2xl border border-border shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-border flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-text-main">Verifikasi Transfer: {{ $selectedPengajuan->kode_pengajuan }}</h3>
                        <p class="text-xs text-text-muted">Siswa: {{ $selectedPengajuan->siswa?->nama }} ({{ $selectedPengajuan->siswa?->rombel?->nama ?? '-' }})</p>
                    </div>
                    <button type="button" wire:click="closePreview" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                <div class="p-5 overflow-y-auto space-y-4 flex-1">
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-text-muted block">Nama Pemilik Rekening:</span>
                            <span class="font-bold text-text-main">{{ $selectedPengajuan->nama_pemilik_rekening }}</span>
                        </div>
                        <div>
                            <span class="text-text-muted block">Bank &amp; No. Rekening:</span>
                            <span class="font-bold text-text-main">{{ $selectedPengajuan->bank_pengirim ?: '-' }} {{ $selectedPengajuan->nomor_rekening_pengirim ? '(' . $selectedPengajuan->nomor_rekening_pengirim . ')' : '' }}</span>
                        </div>
                        <div>
                            <span class="text-text-muted block">Tanggal Transfer:</span>
                            <span class="font-bold text-text-main">{{ $selectedPengajuan->tanggal_transfer->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <span class="text-text-muted block">Nominal Ditransfer:</span>
                            <span class="font-black text-sm text-emerald-600">Rp {{ number_format($selectedPengajuan->nominal_transfer, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @if($selectedPengajuan->catatan)
                        <div class="p-3 rounded-xl bg-bg border border-border text-xs">
                            <span class="text-text-muted block text-[10px] font-bold uppercase mb-0.5">Catatan Orang Tua:</span>
                            {{ $selectedPengajuan->catatan }}
                        </div>
                    @endif

                    <!-- Gambar Bukti Transfer -->
                    <div>
                        <span class="text-xs font-bold text-text-muted uppercase tracking-wider block mb-2">Foto / Bukti Transfer:</span>
                        <div class="border border-border rounded-xl p-2 bg-bg text-center">
                            @if(str_ends_with(strtolower($selectedPengajuan->file_bukti_path), '.pdf'))
                                <a href="{{ asset('storage/' . $selectedPengajuan->file_bukti_path) }}" target="_blank" class="text-xs text-primary font-bold underline">
                                    Lihat Berkas PDF Bukti Transfer &nearr;
                                </a>
                            @else
                                <img src="{{ asset('storage/' . $selectedPengajuan->file_bukti_path) }}" alt="Bukti Transfer" class="max-h-80 mx-auto rounded-lg object-contain">
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-4 border-t border-border bg-bg/50 flex items-center justify-between">
                    <button type="button" wire:click="closePreview" class="px-4 py-2 rounded-xl border border-border bg-surface text-xs font-semibold text-text-main">
                        Tutup
                    </button>
                    @if($selectedPengajuan->status === 'menunggu')
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="openModalTolak({{ $selectedPengajuan->id }})" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors">
                                Tolak Bukti Transfer
                            </button>
                            <button type="button" wire:click="setujuiPengajuan({{ $selectedPengajuan->id }})" wire:loading.attr="disabled" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors shadow-xs">
                                <span wire:loading.remove>Setujui &amp; Terbitkan Kwitansi</span>
                                <span wire:loading>Memproses...</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Input Alasan Tolak -->
    @if($modalTolakOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
            <div class="bg-surface rounded-2xl border border-border shadow-2xl max-w-md w-full p-5 space-y-4">
                <h3 class="text-sm font-bold text-rose-600">Alasan Penolakan Bukti Transfer</h3>
                <p class="text-xs text-text-muted">Jelaskan alasan penolakan agar orang tua dapat memperbaikinya.</p>
                <textarea wire:model="alasanPenolakan" rows="3" class="w-full text-xs rounded-xl border-border bg-bg text-text-main" placeholder="Contoh: Nominal mutasi tidak ditemukan di rekening sekolah, mohon cek kembali"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="$set('modalTolakOpen', false)" class="px-3 py-1.5 rounded-lg border border-border text-xs font-semibold text-text-main">Batal</button>
                    <button type="button" wire:click="tolakPengajuan" class="px-4 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold">Kirim Penolakan</button>
                </div>
            </div>
        </div>
    @endif
</div>

