<div class="space-y-6">
    @include('sarpras::partials.sub-nav', ['active' => 'peminjaman'])

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
                       placeholder="Cari peminjam, nomor peminjaman, atau nama aset..." 
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-border bg-bg text-text-main focus:ring-2 focus:ring-primary">
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>

            <select wire:model.live="filterStatus" class="text-xs rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                <option value="">Semua Status</option>
                <option value="Dipinjam">Sedang Dipinjam</option>
                <option value="Dikembalikan">Sudah Dikembalikan</option>
            </select>
        </div>

        @can('sarpras.peminjaman.tambah')
        <button type="button" wire:click="create" class="px-3.5 py-2 rounded-xl bg-primary text-white hover:bg-primary-hover text-xs font-semibold flex items-center gap-1.5 transition shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Catat Peminjaman
        </button>
        @endcan
    </div>

    {{-- Tabel Peminjaman --}}
    <div class="bg-surface border border-border rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-hover/60 border-b border-border text-text-muted font-medium uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">No. Pinjam</th>
                        <th class="py-3 px-4">Aset Yang Dipinjam</th>
                        <th class="py-3 px-4">Peminjam</th>
                        <th class="py-3 px-4">Keperluan</th>
                        <th class="py-3 px-4">Tgl Pinjam / Batas</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($peminjamans as $p)
                        <tr class="hover:bg-surface-hover/50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-primary">{{ $p->nomor_peminjaman }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-text-main">{{ $p->aset?->nama_barang }}</div>
                                <div class="text-[11px] font-mono text-text-muted">{{ $p->aset?->kode_aset }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-text-main">{{ $p->peminjam_nama }}</div>
                                <div class="text-[11px] text-text-muted">{{ $p->peminjam_tipe }} @if($p->kontak_peminjam) ({{ $p->kontak_peminjam }}) @endif</div>
                            </td>
                            <td class="py-3 px-4 text-text-secondary">{{ $p->keperluan }}</td>
                            <td class="py-3 px-4">
                                <div>{{ $p->tanggal_pinjam?->format('d/m/Y') }} &rarr; <span class="font-bold">{{ $p->batas_kembali?->format('d/m/Y') }}</span></div>
                                @if($p->tanggal_kembali)
                                    <div class="text-[10px] text-emerald-600 dark:text-emerald-400">Kembali: {{ $p->tanggal_kembali?->format('d/m/Y') }} ({{ $p->kondisi_kembali }})</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $p->status === 'Dipinjam' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($p->status === 'Dipinjam')
                                    @can('sarpras.peminjaman.kembali')
                                    <button type="button" wire:click="openReturn({{ $p->id }})" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[11px] transition shadow-xs">
                                        Kembalikan
                                    </button>
                                    @endcan
                                @else
                                    <span class="text-text-muted text-[11px]">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-text-muted">Belum ada data peminjaman aset.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($peminjamans->hasPages())
            <div class="p-4 border-t border-border">
                {{ $peminjamans->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Catat Peminjaman --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface border border-border rounded-2xl w-full max-w-lg p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-bold text-text-main">
                        Form Peminjaman Aset Sekolah
                    </h3>
                    <button type="button" wire:click="$set('showCreateModal', false)" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                <form wire:submit="submitPinjam" class="space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-text-main mb-1">Pilih Aset yang Dipinjam <span class="text-rose-500">*</span></label>
                        <select wire:model="aset_id" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                            <option value="">-- Pilih Aset Tersedia --</option>
                            @foreach($tersediaAsets as $a)
                                <option value="{{ $a->id }}">{{ $a->nama_barang }} ({{ $a->kode_aset }}) - Ruangan: {{ $a->ruangan?->nama_ruangan ?? '-' }}</option>
                            @endforeach
                        </select>
                        @error('aset_id') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-medium text-text-main mb-1">Nama Peminjam <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="peminjam_nama" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                            @error('peminjam_nama') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-medium text-text-main mb-1">Tipe Peminjam</label>
                            <select wire:model="peminjam_tipe" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                                <option value="Guru/GTK">Guru / GTK</option>
                                <option value="Siswa">Siswa</option>
                                <option value="Eksternal">Pihak Luar / Eksternal</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-text-main mb-1">Nomor Kontak / WA Peminjam</label>
                        <input type="text" wire:model="kontak_peminjam" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" placeholder="08xxxxxxxxxx">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-medium text-text-main mb-1">Tanggal Pinjam <span class="text-rose-500">*</span></label>
                            <input type="date" wire:model="tanggal_pinjam" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                        </div>
                        <div>
                            <label class="block font-medium text-text-main mb-1">Batas Pengembalian <span class="text-rose-500">*</span></label>
                            <input type="date" wire:model="batas_kembali" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-text-main mb-1">Keperluan Peminjaman <span class="text-rose-500">*</span></label>
                        <textarea wire:model="keperluan" rows="2" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" placeholder="Contoh: Mengajar presentasi di Ruang Audio Visual"></textarea>
                        @error('keperluan') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="px-4 py-2 rounded-xl border border-border text-text-secondary hover:bg-surface-hover">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-primary-hover shadow-xs">Simpan Peminjaman</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Form Pengembalian --}}
    @if($showReturnModal && $activePeminjaman)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface border border-border rounded-2xl w-full max-w-md p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-bold text-text-main">
                        Pengembalian Aset
                    </h3>
                    <button type="button" wire:click="$set('showReturnModal', false)" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                <div class="p-3 bg-surface-hover rounded-xl text-xs space-y-1">
                    <div>Aset: <strong>{{ $activePeminjaman->aset?->nama_barang }}</strong></div>
                    <div>Peminjam: <strong>{{ $activePeminjaman->peminjam_nama }}</strong></div>
                    <div>Kondisi Sebelum Dipinjam: <span class="font-bold text-emerald-600">{{ $activePeminjaman->kondisi_awal }}</span></div>
                </div>

                <form wire:submit="processReturn" class="space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-text-main mb-1">Tanggal Pengembalian Aktual <span class="text-rose-500">*</span></label>
                        <input type="date" wire:model="tanggal_kembali" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                    </div>

                    <div>
                        <label class="block font-medium text-text-main mb-1">Kondisi Fisik Saat Kembali <span class="text-rose-500">*</span></label>
                        <select wire:model="kondisi_kembali" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                            <option value="Baik">Baik (Normal tanpa kendala)</option>
                            <option value="Rusak Ringan">Rusak Ringan (Perlu diservis ringan)</option>
                            <option value="Rusak Berat">Rusak Berat (Tidak dapat difungsikan)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-text-main mb-1">Catatan Pengembalian</label>
                        <textarea wire:model="returnCatatan" rows="2" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" placeholder="Kelengkapan kabel, remote, kondisi fisik..."></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="$set('showReturnModal', false)" class="px-4 py-2 rounded-xl border border-border text-text-secondary hover:bg-surface-hover">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold shadow-xs">Konfirmasi Kembali</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
