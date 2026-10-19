<div class="space-y-6">
    @include('sarpras::partials.sub-nav', ['active' => 'pemeliharaan'])

    @if(session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm flex items-center justify-between">
            <span>{{ session('message') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    {{-- Filter & Action Bar --}}
    <div class="bg-surface border border-border rounded-2xl p-4 shadow-xs flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
        <div class="flex-1 flex gap-2">
            <div class="relative flex-1">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Cari no. tiket, nama aset, deskripsi..." 
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-border bg-bg text-text-main focus:ring-2 focus:ring-primary">
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>

            <select wire:model.live="filterStatus" class="text-xs rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                <option value="">Semua Status</option>
                <option value="Menunggu Tindakan">Menunggu Tindakan</option>
                <option value="Sedang Diperbaiki">Sedang Diperbaiki</option>
                <option value="Selesai">Selesai</option>
                <option value="Afkir">Afkir (Tidak Dapat Diperbaiki)</option>
            </select>
        </div>

        @can('sarpras.pemeliharaan.tambah')
        <button type="button" wire:click="createReport" class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold flex items-center gap-1.5 transition shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Lapor Kerusakan Aset
        </button>
        @endcan
    </div>

    {{-- Tabel Pemeliharaan --}}
    <div class="bg-surface border border-border rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-hover/60 border-b border-border text-text-muted font-medium uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">No. Tiket</th>
                        <th class="py-3 px-4">Aset Rusak</th>
                        <th class="py-3 px-4">Pelapor & Tanggal</th>
                        <th class="py-3 px-4">Deskripsi Kerusakan</th>
                        <th class="py-3 px-4 text-center">Urgensi</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Biaya (Rp)</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($pemeliharaans as $p)
                        <tr class="hover:bg-surface-hover/50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-primary">{{ $p->nomor_tiket }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-text-main">{{ $p->aset?->nama_barang }}</div>
                                <div class="text-[11px] font-mono text-text-muted">{{ $p->aset?->kode_aset }} &bull; {{ $p->aset?->ruangan?->nama_ruangan ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-text-main">{{ $p->pelapor?->name }}</div>
                                <div class="text-[11px] text-text-muted">{{ $p->tanggal_lapor?->format('d/m/Y') }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-text-main">{{ $p->deskripsi_kerusakan }}</div>
                                @if($p->tindakan_perbaikan)
                                    <div class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-0.5">Tindakan: {{ $p->tindakan_perbaikan }} ({{ $p->teknisi_vendor ?: 'Internal' }})</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @php
                                    $urgClass = match($p->tingkat_urgensi) {
                                        'Berat' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 font-bold',
                                        'Sedang' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                        default => 'bg-blue-100 text-blue-700'
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $urgClass }}">
                                    {{ $p->tingkat_urgensi }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @php
                                    $stClass = match($p->status) {
                                        'Menunggu Tindakan' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
                                        'Sedang Diperbaiki' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                        'Selesai' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 font-bold',
                                        'Afkir' => 'bg-gray-200 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $stClass }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-medium text-text-main">
                                {{ number_format($p->biaya, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @can('sarpras.pemeliharaan.proses')
                                    <button type="button" wire:click="openAction({{ $p->id }})" class="px-2.5 py-1 rounded-lg bg-surface-hover hover:bg-border text-text-main font-semibold text-[11px] transition" title="Tindak Lanjuti Servis">
                                        Proses
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-text-muted">Tidak ada tiket pemeliharaan aktif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pemeliharaans->hasPages())
            <div class="p-4 border-t border-border">
                {{ $pemeliharaans->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Lapor Kerusakan Baru --}}
    @if($showReportModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface border border-border rounded-2xl w-full max-w-md p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-bold text-text-main">
                        Lapor Kerusakan Aset / Fasilitas
                    </h3>
                    <button type="button" wire:click="$set('showReportModal', false)" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                <form wire:submit="submitReport" class="space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-text-main mb-1">Aset yang Mengalami Masalah <span class="text-rose-500">*</span></label>
                        <select wire:model="aset_id" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                            <option value="">-- Pilih Aset --</option>
                            @foreach($allAsets as $a)
                                <option value="{{ $a->id }}">{{ $a->nama_barang }} ({{ $a->kode_aset }}) - Ruang: {{ $a->ruangan?->nama_ruangan ?? '-' }}</option>
                            @endforeach
                        </select>
                        @error('aset_id') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-medium text-text-main mb-1">Tanggal Diketahui <span class="text-rose-500">*</span></label>
                            <input type="date" wire:model="tanggal_lapor" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                        </div>
                        <div>
                            <label class="block font-medium text-text-main mb-1">Tingkat Urgensi</label>
                            <select wire:model="tingkat_urgensi" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                                <option value="Ringan">Ringan (Masih bisa dipakai)</option>
                                <option value="Sedang">Sedang (Terganggu/perlu segera)</option>
                                <option value="Berat">Berat (Mati total/bahaya)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-text-main mb-1">Gejala / Deskripsi Kerusakan <span class="text-rose-500">*</span></label>
                        <textarea wire:model="deskripsi_kerusakan" rows="3" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" placeholder="Jelaskan kendala secara spesifik..."></textarea>
                        @error('deskripsi_kerusakan') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="$set('showReportModal', false)" class="px-4 py-2 rounded-xl border border-border text-text-secondary hover:bg-surface-hover">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold shadow-xs">Kirim Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Tindak Lanjut Servis (Sarpras) --}}
    @if($showActionModal && $activeTicket)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface border border-border rounded-2xl w-full max-w-md p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-bold text-text-main">
                        Tindak Lanjut Servis: {{ $activeTicket->nomor_tiket }}
                    </h3>
                    <button type="button" wire:click="$set('showActionModal', false)" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                <div class="p-3 bg-surface-hover rounded-xl text-xs space-y-1">
                    <div>Aset: <strong>{{ $activeTicket->aset?->nama_barang }}</strong></div>
                    <div>Kendala: <span class="text-rose-600">{{ $activeTicket->deskripsi_kerusakan }}</span></div>
                </div>

                <form wire:submit="processAction" class="space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-text-main mb-1">Status Penanganan <span class="text-rose-500">*</span></label>
                        <select wire:model.live="statusAction" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                            <option value="Sedang Diperbaiki">Sedang Diperbaiki (Dalam proses servis)</option>
                            <option value="Selesai">Selesai (Aset pulih dan siap digunakan kembali)</option>
                            <option value="Afkir">Afkir / Tidak Dapat Diperbaiki (Dihapuskan)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-text-main mb-1">Tindakan / Solusi Perbaikan</label>
                        <textarea wire:model="tindakan_perbaikan" rows="2" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" placeholder="Ganti komponen, install ulang, kalibrasi..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-medium text-text-main mb-1">Teknisi / Vendor</label>
                            <input type="text" wire:model="teknisi_vendor" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" placeholder="Internal / Nama Vendor">
                        </div>
                        <div>
                            <label class="block font-medium text-text-main mb-1">Biaya Perbaikan (Rp)</label>
                            <input type="number" wire:model="biaya" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" min="0">
                        </div>
                    </div>

                    @if($statusAction === 'Selesai' || $statusAction === 'Afkir')
                        <div>
                            <label class="block font-medium text-text-main mb-1">Tanggal Selesai</label>
                            <input type="date" wire:model="tanggal_selesai" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                        </div>
                    @endif

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="$set('showActionModal', false)" class="px-4 py-2 rounded-xl border border-border text-text-secondary hover:bg-surface-hover">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-primary-hover shadow-xs">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
