<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-border pb-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-text-main flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-indigo-500/10 text-indigo-600">🎓</span>
                Piutang Alumni &amp; Penahanan Ijazah
            </h1>
            <p class="text-xs sm:text-sm text-text-secondary mt-1">
                Kelola sisa tunggakan siswa yang sudah lulus, pencatatan cicilan pasca-kelulusan, dan status serah terima ijazah.
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

    <!-- Filter Bar -->
    <div class="bg-surface p-4 rounded-2xl border border-border shadow-xs grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="sm:col-span-2">
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Cari nama alumni, NISN, atau NIPD..."
                   class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
        </div>
        <div>
            <select wire:model.live="statusIjazahFilter" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                <option value="">Semua Status Ijazah</option>
                <option value="ditahan">Ijazah Ditahan</option>
                <option value="boleh_diambil">Boleh Diambil</option>
                <option value="sudah_diambil">Sudah Diambil</option>
            </select>
        </div>
    </div>

    <!-- Tabel Alumni -->
    <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-bg text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                    <tr>
                        <th class="py-3 px-4 text-left">Nama Alumni / NIPD</th>
                        <th class="py-3 px-4 text-left">Tahun Lulus / Kontak</th>
                        <th class="py-3 px-4 text-right">Sisa Tunggakan</th>
                        <th class="py-3 px-4 text-center">Status Ijazah</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($alumnis as $al)
                        @php
                            $statusIjazah = $al->alumniTunggakan?->status_ijazah ?? 'ditahan';
                        @endphp
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-bold text-text-main">{{ $al->nama }}</div>
                                <div class="text-[10px] text-text-muted">NISN: {{ $al->nisn ?? '-' }} &bull; NIPD: {{ $al->nipd ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-text-main">Lulus: {{ $al->alumni?->tahun_lulus ?? '-' }}</div>
                                <div class="text-[10px] text-text-muted font-mono">{{ $al->alumni?->no_hp_terkini ?: ($al->hp ?: '-') }}</div>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <span class="font-black {{ (float) $al->total_tunggakan > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    Rp {{ number_format((float) $al->total_tunggakan, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($statusIjazah === 'ditahan')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600">Ijazah Ditahan</span>
                                @elseif($statusIjazah === 'boleh_diambil')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-600">Boleh Diambil</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600">Sudah Diambil</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button"
                                            wire:click="openCicil({{ $al->id }})"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-primary hover:bg-primary-hover text-white transition-colors">
                                        Bayar / Cicil
                                    </button>
                                    <button type="button"
                                            wire:click="openIjazah({{ $al->id }})"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-border bg-bg hover:bg-surface-hover text-text-main transition-colors"
                                            title="Ubah Status Ijazah">
                                        📜 Ijazah
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-xs text-text-muted">
                                Tidak ada data alumni dengan piutang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $alumnis->links() }}
        </div>
    </div>

    <!-- Modal Cicil Alumni -->
    @if($modalCicilOpen && $selectedSiswa)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
            <div class="bg-surface rounded-2xl border border-border shadow-2xl max-w-lg w-full p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-sm font-bold text-text-main">Input Cicilan Alumni: {{ $selectedSiswa->nama }}</h3>
                    <button type="button" wire:click="$set('modalCicilOpen', false)" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Kas / Rekening Penampung</label>
                    <select wire:model="selectedRekeningId" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                        @foreach($rekenings as $rek)
                            <option value="{{ $rek->id }}">{{ $rek->nama_bank }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2 max-h-56 overflow-y-auto">
                    @forelse($tagihans as $tgh)
                        <div class="p-2.5 rounded-xl border border-border bg-bg flex items-center justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <div class="font-bold text-xs text-text-main truncate">{{ $tgh->label_tagihan }}</div>
                                <div class="text-[10px] text-rose-600 font-bold">Sisa: Rp {{ number_format($tgh->sisa_tagihan, 0, ',', '.') }}</div>
                            </div>
                            <input type="number"
                                   wire:model="bayarNominal.{{ $tgh->id }}"
                                   placeholder="Nominal"
                                   class="w-32 text-right text-xs rounded-lg border-border bg-surface text-text-main py-1 px-2 font-bold font-mono">
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-emerald-600 font-bold">Tunggakan telah lunas.</div>
                    @endforelse
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-border">
                    <button type="button" wire:click="$set('modalCicilOpen', false)" class="px-4 py-2 rounded-xl border border-border text-xs font-semibold text-text-main">Batal</button>
                    <button type="button" wire:click="prosesCicilAlumni" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold">Simpan Cicilan</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Status Ijazah -->
    @if($modalIjazahOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
            <div class="bg-surface rounded-2xl border border-border shadow-2xl max-w-md w-full p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-sm font-bold text-text-main">Pengaturan Pelepasan Ijazah Alumni</h3>
                    <button type="button" wire:click="$set('modalIjazahOpen', false)" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Status Ijazah</label>
                    <select wire:model="statusIjazah" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                        <option value="ditahan">Ditahan (Masih Menunggak)</option>
                        <option value="boleh_diambil">Boleh Diambil (Dispensasi / Perjanjian)</option>
                        <option value="sudah_diambil">Sudah Diambil (Telah Diserahkan)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Tanggal Serah Terima (Jika sudah diambil)</label>
                    <input type="date" wire:model="tglSerahTerima" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Catatan Serah Terima / Dispensasi</label>
                    <textarea wire:model="catatanIjazah" rows="2" class="w-full text-xs rounded-xl border-border bg-bg text-text-main" placeholder="Contoh: Diberikan surat perjanjian pelunasan bertahap"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-border">
                    <button type="button" wire:click="$set('modalIjazahOpen', false)" class="px-4 py-2 rounded-xl border border-border text-xs font-semibold text-text-main">Batal</button>
                    <button type="button" wire:click="simpanIjazah" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold">Simpan Status</button>
                </div>
            </div>
        </div>
    @endif
</div>

