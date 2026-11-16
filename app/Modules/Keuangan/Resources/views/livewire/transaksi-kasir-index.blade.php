<div class="space-y-6" x-data="{}" @buka-kwitansi.window="window.open($event.detail.url, '_blank')">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-border pb-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-text-main flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-primary/10 text-primary">🏪</span>
                Loket Pembayaran Kasir Siswa
            </h1>
            <p class="text-xs sm:text-sm text-text-secondary mt-1">
                Layanan pembayaran tunai loket sekolah &amp; konfirmasi setoran (SPP bulanan, cicilan DSP, &amp; insidental).
            </p>
        </div>
        @if($selectedSiswaId)
            <button type="button" wire:click="resetSiswa" class="px-3.5 py-2 rounded-xl text-xs font-semibold border border-border bg-surface hover:bg-surface-hover text-text-main transition-colors flex items-center gap-1.5">
                <span>🔄</span>
                <span>Ganti Siswa</span>
            </button>
        @endif
    </div>

    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 text-xs font-bold flex items-center justify-between">
            <span>✅ {{ session('status') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-600 text-xs font-bold">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    @if(! $selectedSiswaId)
        <!-- Search Box Siswa -->
        <div class="max-w-xl mx-auto my-8 bg-surface p-6 rounded-2xl border border-border shadow-xs text-center">
            <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-3 text-xl">
                🔍
            </div>
            <h2 class="text-sm font-bold text-text-main mb-1">Cari Siswa untuk Melayani Pembayaran</h2>
            <p class="text-xs text-text-muted mb-4">Ketik Nama, NIPD, atau NISN siswa yang akan membayar</p>

            <div class="relative text-left">
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Ketik minimal 2 karakter (contoh: Muhammad, 2324...)"
                       class="w-full text-xs rounded-xl border-border bg-bg py-2.5 pl-3 pr-10 text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
            </div>

            <!-- Search Dropdown Results -->
            @if(count($searchResults) > 0)
                <div class="mt-3 divide-y divide-border border border-border rounded-xl bg-bg text-left max-h-72 overflow-y-auto shadow-md">
                    @foreach($searchResults as $s)
                        <button type="button"
                                wire:click="selectSiswa({{ $s->id }})"
                                class="w-full p-3 hover:bg-primary-soft transition-colors flex items-center justify-between text-left">
                            <div>
                                <div class="text-xs font-bold text-text-main">{{ $s->nama }}</div>
                                <div class="text-[11px] text-text-muted">
                                    NIPD: {{ $s->nipd ?? '-' }} &bull; Kelas: {{ $s->rombel?->nama ?? '-' }} &bull; Rayon: {{ $s->rayon?->nama ?? '-' }}
                                </div>
                            </div>
                            <span class="text-xs font-bold text-primary">Pilih &rarr;</span>
                        </button>
                    @endforeach
                </div>
            @elseif(strlen(trim($search)) >= 2)
                <div class="mt-3 p-4 text-xs text-text-muted border border-border rounded-xl bg-bg">
                    Tidak ditemukan siswa dengan kata kunci "<span class="font-semibold">{{ $search }}</span>".
                </div>
            @endif
        </div>
    @else
        <!-- Siswa Terpilih & Billing Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Profil Siswa & Pilihan Rekening -->
            <div class="space-y-6">
                <!-- Info Card Siswa -->
                <div class="bg-surface p-5 rounded-2xl border border-border shadow-xs">
                    <div class="flex items-center gap-3 pb-4 border-b border-border">
                        <div class="w-12 h-12 rounded-xl bg-primary-soft text-primary font-black flex items-center justify-center text-sm border border-primary/20">
                            {{ strtoupper(substr($siswa->nama, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-sm font-bold text-text-main truncate">{{ $siswa->nama }}</h2>
                            <p class="text-xs text-text-muted">NISN: {{ $siswa->nisn ?: '-' }} &bull; NIPD: {{ $siswa->nipd ?: '-' }}</p>
                        </div>
                    </div>
                    <div class="mt-4 space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-text-muted">Rombel:</span>
                            <span class="font-semibold text-text-main">{{ $siswa->rombel?->nama ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-text-muted">Rayon:</span>
                            <span class="font-semibold text-text-main">{{ $siswa->rayon?->nama ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-text-muted">Pembimbing Rayon:</span>
                            <span class="font-semibold text-text-main">{{ $siswa->rayon?->pembimbing?->nama_lengkap ?? ($siswa->rayon?->pembimbing?->name ?? '-') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-text-muted">No. HP Siswa:</span>
                            <span class="font-semibold text-text-main font-mono">{{ $siswa->hp ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Formulir Pembayaran -->
                <div class="bg-surface p-5 rounded-2xl border border-border shadow-xs space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-text-muted">Informasi Kasir</h3>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Kas / Rekening Penampung</label>
                        <select wire:model="selectedRekeningId" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                            @foreach($rekenings as $rek)
                                <option value="{{ $rek->id }}">{{ $rek->is_kas_tunai ? '💵 Kas Tunai' : '🏦 ' . $rek->nama_bank }} - {{ $rek->atas_nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Metode Pembayaran</label>
                        <select wire:model="channel" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                            <option value="offline_kasir">Tunai Loket Sekolah</option>
                            <option value="online_transfer">Transfer Bank (Langsung Di Loket)</option>
                            <option value="subsidi_beasiswa">Subsidi Beasiswa / OTA</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Nama Penyetor</label>
                        <input type="text" wire:model="namaPenyetor" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2" placeholder="Nama siswa / orang tua">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Catatan (Opsional)</label>
                        <textarea wire:model="catatan" rows="2" class="w-full text-xs rounded-xl border-border bg-bg text-text-main" placeholder="Keterangan tambahan jika ada"></textarea>
                    </div>

                    <!-- Total Ringkasan & Submit -->
                    <div class="pt-4 border-t border-border">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-xs font-bold text-text-muted">Total Bayar:</span>
                            <span class="text-lg font-black text-primary">Rp {{ number_format($totalAkanDibayar, 0, ',', '.') }}</span>
                        </div>
                        <button type="button"
                                wire:click="prosesBayar"
                                wire:loading.attr="disabled"
                                class="w-full py-3 rounded-xl bg-primary hover:bg-primary-hover text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-2">
                            <span wire:loading.remove>💾 Simpan &amp; Cetak Kwitansi</span>
                            <span wire:loading>Memproses Transaksi...</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Rincian Tagihan yang Bisa Dibayar (Multi-pos & Cicilan) -->
            <div class="lg:col-span-2 bg-surface rounded-2xl border border-border shadow-xs overflow-hidden flex flex-col">
                <div class="p-4 sm:p-5 border-b border-border flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-text-main">Daftar Tagihan Belum Lunas</h2>
                        <p class="text-xs text-text-muted mt-0.5">Ketik nominal yang akan disetor pada masing-masing pos</p>
                    </div>
                    <span class="text-xs font-bold text-rose-600 bg-rose-500/10 px-2.5 py-1 rounded-full">
                        {{ $tagihans->count() }} Tagihan
                    </span>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-xs">
                        <thead class="bg-bg text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                            <tr>
                                <th class="py-3 px-4 text-left">Pos Biaya</th>
                                <th class="py-3 px-3 text-right">Tarif Tagihan</th>
                                <th class="py-3 px-3 text-right">Sisa Tunggakan</th>
                                <th class="py-3 px-4 text-right" style="width: 180px;">Nominal Bayar</th>
                                <th class="py-3 px-2 text-center" style="width: 50px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($tagihans as $tgh)
                                <tr class="hover:bg-surface-hover transition-colors">
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-text-main">{{ $tgh->label_tagihan }}</div>
                                        <div class="text-[10px] text-text-muted">{{ $tgh->posBiaya?->badge_tipe }}</div>
                                    </td>
                                    <td class="py-3 px-3 text-right font-medium text-text-secondary">
                                        Rp {{ number_format($tgh->nominal_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-bold text-rose-600">
                                        Rp {{ number_format($tgh->sisa_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <input type="number"
                                               wire:model.live.debounce.300ms="bayarNominal.{{ $tgh->id }}"
                                               placeholder="0"
                                               max="{{ (int) $tgh->sisa_tagihan }}"
                                               class="w-full text-right text-xs rounded-lg border-border bg-bg text-text-main py-1.5 px-2 font-bold font-mono focus:border-primary">
                                    </td>
                                    <td class="py-3 px-2 text-center">
                                        <button type="button"
                                                wire:click="setNominalPenuh({{ $tgh->id }}, {{ (float) $tgh->sisa_tagihan }})"
                                                class="px-2 py-1 text-[10px] font-bold rounded bg-primary-soft text-primary hover:bg-primary hover:text-white transition-colors"
                                                title="Bayar Lunas Tagihan Ini">
                                            Full
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-xs text-emerald-600 font-bold">
                                        🎉 Siswa ini tidak memiliki tunggakan biaya (Seluruh tagihan LUNAS).
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

