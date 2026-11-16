<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-border pb-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-text-main flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-primary/10 text-primary">📢</span>
                Terbitkan Tagihan &amp; WhatsApp
            </h1>
            <p class="text-xs sm:text-sm text-text-secondary mt-1">
                Kompilasi tagihan siswa dari awal masuk s.d. bulan berjalan, cetak surat tagihan fisik resmi, dan kirim pesan WhatsApp otomatis ke orang tua.
            </p>
        </div>
        <div>
            <button type="button"
                    wire:click="terbitkanTagihanMassal"
                    wire:confirm="Terbitkan seluruh tagihan (SPP 12 bulan Juli-Juni & DSP cicilan) untuk seluruh siswa aktif sesuai tarif angkatan dan programnya?"
                    class="px-4 py-2 rounded-xl text-xs font-bold bg-primary hover:bg-primary-hover text-white shadow-xs flex items-center gap-2 transition-all">
                <span>⚡</span>
                <span>Terbitkan Tagihan Massal (SPP &amp; DSP)</span>
            </button>
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

    <!-- Filter Bar -->
    <div class="bg-surface p-4 rounded-2xl border border-border shadow-xs grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div>
            <label class="block text-[11px] font-bold text-text-muted uppercase mb-1">Cari Siswa</label>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Nama, NIPD, atau NISN..."
                   class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-text-muted uppercase mb-1">Filter Rombel</label>
            <select wire:model.live="selectedRombelId" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                <option value="">Semua Rombel</option>
                @foreach($rombels as $r)
                    <option value="{{ $r->id }}">{{ $r->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-text-muted uppercase mb-1">Filter Rayon</label>
            <select wire:model.live="selectedRayonId" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                <option value="">Semua Rayon</option>
                @foreach($rayons as $ray)
                    <option value="{{ $ray->id }}">{{ $ray->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-text-muted uppercase mb-1">Status Tunggakan</label>
            <select wire:model.live="filterTunggakan" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                <option value="menunggak">Hanya yang Menunggak (&gt; Rp 0)</option>
                <option value="lunas">Sudah Lunas (Rp 0)</option>
                <option value="semua">Semua Siswa</option>
            </select>
        </div>
    </div>

    <!-- Table Siswa & Tagihan -->
    <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-bg text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                    <tr>
                        <th class="py-3 px-4 text-left">Nama Siswa / NIPD</th>
                        <th class="py-3 px-4 text-left">Kelas / Rayon</th>
                        <th class="py-3 px-4 text-left">Kontak Orang Tua / Wali</th>
                        <th class="py-3 px-4 text-right">Total Tunggakan</th>
                        <th class="py-3 px-4 text-center">Aksi Dokumen &amp; WA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($siswas as $s)
                        @php
                            $ortu = $s->orangTuas->firstWhere('hubungan', 'ayah')
                                ?? $s->orangTuas->firstWhere('hubungan', 'ibu')
                                ?? $s->orangTuas->firstWhere('hubungan', 'wali');
                            $noHpOrtu = $ortu?->no_hp ?? $s->hp;
                        @endphp
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-bold text-text-main">{{ $s->nama }}</div>
                                <div class="text-[10px] text-text-muted">NIPD: {{ $s->nipd ?? '-' }} &bull; NISN: {{ $s->nisn ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-text-main">{{ $s->rombel?->nama ?? '-' }}</div>
                                <div class="text-[10px] text-text-muted">{{ $s->rayon?->nama ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-text-main">{{ $ortu?->nama ?? 'Nama ortu belum diisi' }}</div>
                                <div class="text-[10px] text-text-muted font-mono">{{ $noHpOrtu ?: 'Tanpa No HP' }}</div>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <span class="font-black {{ (float) $s->total_tunggakan > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    Rp {{ number_format((float) $s->total_tunggakan, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Cetak Surat Tagihan PDF -->
                                    <a href="{{ route('keuangan.cetak.surat-tagihan', $s->id) }}" target="_blank" class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-border bg-bg hover:bg-surface-hover text-text-main flex items-center gap-1" title="Cetak Surat Tagihan Resmi">
                                        <span>📄</span>
                                        <span>Surat</span>
                                    </a>

                                    <!-- Click to chat WhatsApp -->
                                    <button type="button"
                                            wire:click="prepareWa({{ $s->id }})"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white flex items-center gap-1"
                                            title="Kirim Tagihan via WhatsApp">
                                        <span>💬</span>
                                        <span>WA</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-xs text-text-muted">
                                Tidak ada data siswa yang cocok dengan filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $siswas->links() }}
        </div>
    </div>

    <!-- Modal WhatsApp Preview & Direct Link -->
    @if($modalWaOpen && !empty($waData))
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
            <div class="bg-surface rounded-2xl border border-border shadow-2xl max-w-lg w-full p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-sm font-bold text-text-main flex items-center gap-2">
                        <span class="text-emerald-600 text-lg">💬</span>
                        Kirim Notifikasi Tagihan WhatsApp
                    </h3>
                    <button type="button" wire:click="$set('modalWaOpen', false)" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                <div class="text-xs space-y-2">
                    <div>
                        <span class="text-text-muted">Tujuan:</span>
                        <span class="font-bold text-text-main">{{ $waData['nama_ortu'] }}</span>
                        <span class="font-mono text-primary">({{ $waData['no_hp'] ?: 'Nomor HP tidak valid' }})</span>
                    </div>
                    <div>
                        <span class="text-text-muted">Total Kewajiban:</span>
                        <span class="font-black text-rose-600">Rp {{ number_format($waData['total_tunggakan'], 0, ',', '.') }}</span>
                        <span class="text-[10px] text-text-muted">({{ $waData['jumlah_item'] }} pos/item)</span>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-text-muted uppercase mb-1">Pratinjau Pesan:</label>
                    <div class="p-3 bg-bg border border-border rounded-xl font-mono text-[11px] leading-relaxed whitespace-pre-line text-text-main max-h-52 overflow-y-auto">
                        {{ $waData['pesan'] }}
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <button type="button" wire:click="$set('modalWaOpen', false)" class="px-4 py-2 rounded-xl border border-border text-xs font-semibold text-text-main">
                        Tutup
                    </button>
                    @if($waData['wa_link'])
                        <a href="{{ $waData['wa_link'] }}" target="_blank" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2">
                            <span>📲 Buka WhatsApp Web / App</span>
                        </a>
                    @else
                        <span class="text-xs text-rose-600 font-bold">Nomor HP orang tua belum diinput di sistem</span>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

