<div class="space-y-6">
    @if($isSiswa)
        <!-- Akses Dibatasi untuk Akun Siswa -->
        <div class="bg-surface rounded-2xl border border-border p-8 text-center max-w-lg mx-auto my-12 shadow-xs">
            <div class="w-16 h-16 rounded-full bg-amber-500/10 text-amber-500 flex items-center justify-center mx-auto mb-4 text-2xl">
                🛡️
            </div>
            <h2 class="text-base font-bold text-text-main mb-1">Akses Khusus Orang Tua / Wali</h2>
            <p class="text-xs text-text-secondary leading-relaxed mb-6">
                Informasi rincian tagihan dan administrasi keuangan sekolah merupakan kewenangan Orang Tua / Wali. Siswa diharapkan fokus pada pembelajaran dan kegiatan akademik.
            </p>
            <div class="p-3 bg-bg rounded-xl border border-border text-[11px] text-text-muted">
                Untuk pertanyaan terkait administrasi, orang tua dapat menghubungi langsung Bendahara Sekolah.
            </div>
        </div>
    @elseif(! $siswa)
        <div class="bg-surface rounded-2xl border border-border p-8 text-center max-w-lg mx-auto my-12 shadow-xs">
            <div class="w-16 h-16 rounded-full bg-rose-500/10 text-rose-500 flex items-center justify-center mx-auto mb-4 text-2xl">
                ⚠️
            </div>
            <h2 class="text-base font-bold text-text-main mb-1">Data Siswa Tidak Ditemukan</h2>
            <p class="text-xs text-text-secondary">Akun orang tua ini belum tertaut dengan data siswa aktif.</p>
        </div>
    @else
        <!-- Header Ortu -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-border pb-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-text-main flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-primary/10 text-primary">💳</span>
                    Informasi Tagihan &amp; Pembayaran
                </h1>
                <p class="text-xs sm:text-sm text-text-secondary mt-1">
                    Putra/Putri Anda: <span class="font-bold text-text-main">{{ $siswa->nama }}</span> ({{ $siswa->rombel?->nama ?? '-' }}) &bull; Rayon: {{ $siswa->rayon?->nama ?? '-' }}
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

        <!-- Card Total Kewajiban -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white p-6 rounded-2xl shadow-lg border border-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="text-xs text-slate-300 font-bold uppercase tracking-wider">Total Sisa Kewajiban Administrasi</div>
                <div class="text-2xl sm:text-3xl font-black text-amber-400 mt-1">
                    Rp {{ number_format($totalTunggakan, 0, ',', '.') }}
                </div>
                <div class="text-xs text-slate-300 mt-1">
                    {{ $tagihans->count() }} pos tagihan belum lunas
                </div>
            </div>
            <div>
                <button type="button"
                        wire:click="$set('activeTab', 'upload')"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2">
                    <span>📤</span>
                    <span>Kirim Bukti Transfer Bank</span>
                </button>
            </div>
        </div>

        <!-- Navigation Tab -->
        <div class="flex items-center gap-2 border-b border-border pb-2">
            <button type="button"
                    wire:click="$set('activeTab', 'tagihan')"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $activeTab === 'tagihan' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:bg-surface-hover' }}">
                Daftar Tagihan ({{ $tagihans->count() }})
            </button>
            <button type="button"
                    wire:click="$set('activeTab', 'upload')"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $activeTab === 'upload' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:bg-surface-hover' }}">
                Upload Bukti Transfer
            </button>
            <button type="button"
                    wire:click="$set('activeTab', 'riwayat')"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $activeTab === 'riwayat' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:bg-surface-hover' }}">
                Riwayat Pembayaran &amp; Kwitansi
            </button>
        </div>

        <!-- TAB 1: DAFTAR TAGIHAN -->
        @if($activeTab === 'tagihan')
            <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-bg text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                            <tr>
                                <th class="py-3 px-4 text-left">Uraian Tagihan</th>
                                <th class="py-3 px-4 text-right">Tarif</th>
                                <th class="py-3 px-4 text-right">Sudah Dibayar</th>
                                <th class="py-3 px-4 text-right">Sisa Tunggakan</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($tagihans as $tgh)
                                <tr class="hover:bg-surface-hover transition-colors">
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-text-main">{{ $tgh->label_tagihan }}</div>
                                        <div class="text-[10px] text-text-muted">{{ $tgh->posBiaya?->badge_tipe }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-right font-medium text-text-secondary">
                                        Rp {{ number_format($tgh->nominal_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-medium text-emerald-600">
                                        Rp {{ number_format($tgh->nominal_terbayar, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-black text-rose-600">
                                        Rp {{ number_format($tgh->sisa_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($tgh->status === 'dicicil')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-600">Dicicil</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-600">Belum Bayar</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-xs text-emerald-600 font-bold">
                                        🎉 Alhamdulillah, tidak ada tunggakan tagihan saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- TAB 2: UPLOAD BUKTI TRANSFER -->
        @if($activeTab === 'upload')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Panduan Transfer Bank -->
                <div class="bg-surface p-5 rounded-2xl border border-border shadow-xs space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-text-muted">Rekening Resmi Sekolah</h3>
                    <p class="text-xs text-text-secondary leading-relaxed">
                        Silakan transfer biaya sekolah ke salah satu rekening resmi SMK Wikrama 1 Garut berikut:
                    </p>

                    <div class="space-y-3">
                        @foreach($rekenings as $rek)
                            <div class="p-3.5 rounded-xl border border-border bg-bg">
                                <div class="font-bold text-xs text-text-main flex items-center gap-1.5">
                                    <span>🏦</span>
                                    <span>{{ $rek->nama_bank }}</span>
                                </div>
                                <div class="font-mono font-bold text-sm text-primary mt-1 select-all">
                                    {{ $rek->nomor_rekening }}
                                </div>
                                <div class="text-[11px] text-text-muted">a.n {{ $rek->atas_nama }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Formulir Unggah Bukti -->
                <div class="lg:col-span-2 bg-surface p-5 rounded-2xl border border-border shadow-xs space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-text-muted">Formulir Konfirmasi Pembayaran</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary mb-1">Rekening Sekolah Tujuan</label>
                            <select wire:model="rekeningTujuanId" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                                @foreach($rekenings as $rek)
                                    <option value="{{ $rek->id }}">{{ $rek->nama_bank }} - {{ $rek->nomor_rekening }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-text-secondary mb-1">Nama Pemilik Rekening Pengirim</label>
                            <input type="text" wire:model="namaPemilikRekening" placeholder="Nama pengirim sesuai buku tabungan" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-text-secondary mb-1">Bank Pengirim (Opsional)</label>
                            <input type="text" wire:model="bankPengirim" placeholder="Contoh: BCA, BRI, Mandiri" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-text-secondary mb-1">Tanggal Transfer</label>
                            <input type="date" wire:model="tanggalTransfer" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-text-secondary mb-1">Nominal yang Ditransfer (Rp)</label>
                            <input type="number" wire:model="nominalTransfer" placeholder="Contoh: 500000" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2 font-mono font-bold">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-text-secondary mb-1">Unggah Struk / Bukti Transfer (JPG/PNG/PDF, maks 3MB)</label>
                            <input type="file" wire:model="fileBukti" class="w-full text-xs text-text-muted file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-soft file:text-primary hover:file:bg-primary hover:file:text-white cursor-pointer">
                            <div wire:loading wire:target="fileBukti" class="text-[11px] text-primary mt-1 font-semibold">Mengunggah file...</div>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-text-secondary mb-1">Catatan Tambahan (Opsional)</label>
                            <textarea wire:model="catatan" rows="2" placeholder="Tuliskan keterangan peruntukan transfer (cth: Bayar SPP bulan Juli dan cicil DSP)" class="w-full text-xs rounded-xl border-border bg-bg text-text-main"></textarea>
                        </div>
                    </div>

                    <button type="button"
                            wire:click="uploadBuktiBayar"
                            wire:loading.attr="disabled"
                            class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition-all">
                        <span wire:loading.remove>Kirim Bukti Pembayaran</span>
                        <span wire:loading>Sedang Mengirim...</span>
                    </button>
                </div>
            </div>
        @endif

        <!-- TAB 3: RIWAYAT PEMBAYARAN & KWITANSI -->
        @if($activeTab === 'riwayat')
            <div class="space-y-6">
                <!-- Riwayat Kwitansi Sah -->
                <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                    <div class="p-4 border-b border-border">
                        <h2 class="text-sm font-bold text-text-main">Kwitansi Resmi yang Telah Diverifikasi</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-bg text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                                <tr>
                                    <th class="py-3 px-4 text-left">No. Kwitansi</th>
                                    <th class="py-3 px-4 text-left">Tanggal</th>
                                    <th class="py-3 px-4 text-left">Rincian Pos</th>
                                    <th class="py-3 px-4 text-right">Total Bayar</th>
                                    <th class="py-3 px-4 text-center">Unduh Kwitansi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @forelse($riwayatTransaksis as $trx)
                                    <tr class="hover:bg-surface-hover transition-colors">
                                        <td class="py-3 px-4 font-mono font-bold text-primary">{{ $trx->nomor_transaksi }}</td>
                                        <td class="py-3 px-4">{{ $trx->tanggal_bayar->format('d/m/Y H:i') }}</td>
                                        <td class="py-3 px-4">
                                            {{ $trx->items->map(fn($i) => ($i->posBiaya?->kode ?? 'POS') . ' (' . number_format($i->nominal_dibayar, 0, ',', '.') . ')')->join(', ') }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-black text-emerald-600">
                                            Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <a href="{{ route('keuangan.cetak.kwitansi', $trx->id) }}" target="_blank" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-primary-soft text-primary hover:bg-primary hover:text-white transition-colors inline-block">
                                                📄 Unduh PDF
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-xs text-text-muted">Belum ada riwayat kwitansi pembayaran.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Status Unggahan Transfer -->
                <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                    <div class="p-4 border-b border-border">
                        <h2 class="text-sm font-bold text-text-main">Status Unggahan Bukti Transfer Anda</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-bg text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                                <tr>
                                    <th class="py-3 px-4 text-left">Kode / Tanggal</th>
                                    <th class="py-3 px-4 text-left">Rekening Tujuan</th>
                                    <th class="py-3 px-4 text-right">Nominal</th>
                                    <th class="py-3 px-4 text-center">Status</th>
                                    <th class="py-3 px-4 text-left">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @forelse($pengajuansOnline as $po)
                                    <tr class="hover:bg-surface-hover transition-colors">
                                        <td class="py-3 px-4">
                                            <div class="font-mono font-bold text-text-main">{{ $po->kode_pengajuan }}</div>
                                            <div class="text-[10px] text-text-muted">{{ $po->created_at->format('d/m/Y H:i') }}</div>
                                        </td>
                                        <td class="py-3 px-4 font-medium text-text-secondary">{{ $po->rekeningTujuan?->nama_bank }}</td>
                                        <td class="py-3 px-4 text-right font-black text-text-main">Rp {{ number_format($po->nominal_transfer, 0, ',', '.') }}</td>
                                        <td class="py-3 px-4 text-center">
                                            @if($po->status === 'menunggu')
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-600">Menunggu Verifikasi</span>
                                            @elseif($po->status === 'disetujui')
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-600">Disetujui</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-600">Ditolak</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-text-secondary">
                                            {{ $po->alasan_penolakan ?: ($po->status === 'disetujui' ? 'Dana terverifikasi' : 'Sedang diperiksa bendahara') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-xs text-text-muted">Belum ada unggahan bukti transfer.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>

