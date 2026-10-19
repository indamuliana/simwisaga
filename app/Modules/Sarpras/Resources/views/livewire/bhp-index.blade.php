<div class="space-y-6">
    @include('sarpras::partials.sub-nav', ['active' => 'bhp'])

    {{-- Alert Flash Message --}}
    @if(session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm flex items-center justify-between">
            <span>{{ session('message') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    {{-- Action & Filter Bar --}}
    <div class="bg-surface border border-border rounded-2xl p-4 shadow-xs flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
        <div class="flex-1 flex flex-col sm:flex-row gap-2">
            <!-- Search -->
            <div class="relative flex-1">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Cari kode atau nama bahan habis pakai..." 
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-border bg-bg text-text-main focus:ring-2 focus:ring-primary">
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>

            <!-- Filter Kategori -->
            <select wire:model.live="filterKategori" class="text-xs rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                <option value="">Semua Kategori BHP</option>
                @foreach($kategoris as $k)
                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                @endforeach
            </select>

            <!-- Toggle Filter Stok Menipis -->
            <button type="button" 
                    wire:click="$toggle('filterMenipis')" 
                    class="px-3 py-2 rounded-xl border text-xs font-semibold flex items-center gap-1.5 transition {{ $filterMenipis ? 'bg-amber-500 text-white border-amber-600' : 'bg-surface border-border text-text-secondary hover:bg-surface-hover' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                Stok Menipis
            </button>
        </div>

        @can('sarpras.bhp.tambah')
        <button type="button" wire:click="create" class="px-3.5 py-2 rounded-xl bg-primary text-white hover:bg-primary-hover text-xs font-semibold flex items-center gap-1.5 transition shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah Item BHP
        </button>
        @endcan
    </div>

    {{-- Tabel BHP --}}
    <div class="bg-surface border border-border rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-hover/60 border-b border-border text-text-muted font-medium uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Kode</th>
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-center">Satuan</th>
                        <th class="py-3 px-4 text-center">Stok Saat Ini</th>
                        <th class="py-3 px-4 text-center">Batas Minimum</th>
                        <th class="py-3 px-4 text-center">Mutasi Cepat</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($bhps as $b)
                        @php
                            $isMenipis = $b->stok_saat_ini <= $b->stok_minimum;
                        @endphp
                        <tr class="hover:bg-surface-hover/50 transition">
                            <td class="py-3 px-4 font-mono font-semibold text-primary">{{ $b->kode_barang }}</td>
                            <td class="py-3 px-4 font-bold text-text-main">
                                {{ $b->nama_barang }}
                                @if($b->keterangan)
                                    <div class="text-[11px] text-text-muted font-normal">{{ $b->keterangan }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-text-secondary">{{ $b->kategori?->nama }}</td>
                            <td class="py-3 px-4 text-center font-medium">{{ $b->satuan }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $isMenipis ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 animate-pulse' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' }}">
                                    {{ $b->stok_saat_ini }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center text-text-muted font-medium">{{ $b->stok_minimum }}</td>
                            <td class="py-3 px-4 text-center">
                                @can('sarpras.bhp.mutasi')
                                <div class="inline-flex items-center gap-1">
                                    <button type="button" wire:click="openMutasi({{ $b->id }}, 'masuk')" class="px-2 py-1 rounded-md bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-semibold text-[11px] transition" title="Tambah Stok (Barang Masuk)">
                                        + Masuk
                                    </button>
                                    <button type="button" wire:click="openMutasi({{ $b->id }}, 'keluar')" class="px-2 py-1 rounded-md bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 font-semibold text-[11px] transition" title="Pakai / Distribusi (Barang Keluar)">
                                        - Pakai
                                    </button>
                                </div>
                                @endcan
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" wire:click="showHistory({{ $b->id }})" class="p-1 rounded-lg hover:bg-surface-hover text-text-muted hover:text-primary transition" title="Kartu Stok / Riwayat Mutasi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </button>

                                    @can('sarpras.bhp.edit')
                                    <button type="button" wire:click="edit({{ $b->id }})" class="p-1 rounded-lg hover:bg-surface-hover text-text-muted hover:text-amber-600 transition" title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @endcan

                                    @can('sarpras.bhp.edit')
                                    <button type="button" 
                                            wire:confirm="Yakin ingin menghapus item {{ $b->nama_barang }}?"
                                            wire:click="delete({{ $b->id }})" 
                                            class="p-1 rounded-lg hover:bg-surface-hover text-text-muted hover:text-rose-600 transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-text-muted">
                                Tidak ada data bahan habis pakai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bhps->hasPages())
            <div class="p-4 border-t border-border">
                {{ $bhps->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Tambah / Edit BHP --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface border border-border rounded-2xl w-full max-w-md p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-bold text-text-main">
                        {{ $isEdit ? 'Edit Bahan Habis Pakai' : 'Tambah BHP Baru' }}
                    </h3>
                    <button type="button" wire:click="$set('showModal', false)" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                <form wire:submit="save" class="space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-text-main mb-1">Kode Barang <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="kode_barang" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3 font-mono">
                        @error('kode_barang') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-text-main mb-1">Nama Barang BHP <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="nama_barang" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" placeholder="Contoh: Kertas HVS A4 80gr">
                        @error('nama_barang') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-medium text-text-main mb-1">Kategori BHP <span class="text-rose-500">*</span></label>
                            <select wire:model="kategori_id" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                                @foreach($kategoris as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-text-main mb-1">Satuan <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="satuan" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" placeholder="Rim / Pcs / Box / Liter">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-medium text-text-main mb-1">Stok Awal / Saat Ini</label>
                            <input type="number" wire:model="stok_saat_ini" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" min="0">
                        </div>
                        <div>
                            <label class="block font-medium text-text-main mb-1">Batas Minimum Peringatan</label>
                            <input type="number" wire:model="stok_minimum" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" min="0">
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-text-main mb-1">Keterangan</label>
                        <textarea wire:model="keterangan" rows="2" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 rounded-xl border border-border text-text-secondary hover:bg-surface-hover">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-primary-hover shadow-xs">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Catat Mutasi (Masuk / Keluar) --}}
    @if($showMutasiModal && $mutasiBhp)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface border border-border rounded-2xl w-full max-w-md p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-bold text-text-main">
                        Catat Mutasi: {{ $mutasiBhp->nama_barang }}
                    </h3>
                    <button type="button" wire:click="$set('showMutasiModal', false)" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                <div class="p-3 bg-surface-hover rounded-xl text-xs space-y-1">
                    <div class="flex justify-between">
                        <span class="text-text-muted">Kode Barang:</span>
                        <span class="font-mono font-bold">{{ $mutasiBhp->kode_barang }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-text-muted">Sisa Stok Saat Ini:</span>
                        <span class="font-bold text-primary">{{ $mutasiBhp->stok_saat_ini }} {{ $mutasiBhp->satuan }}</span>
                    </div>
                </div>

                <form wire:submit="saveMutasi" class="space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-text-main mb-1">Jenis Mutasi <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center justify-center gap-2 p-2 rounded-xl border cursor-pointer {{ $mutasiJenis === 'masuk' ? 'bg-emerald-50 dark:bg-emerald-900/30 border-emerald-500 text-emerald-700 dark:text-emerald-300 font-bold' : 'border-border' }}">
                                <input type="radio" wire:model.live="mutasiJenis" value="masuk" class="hidden">
                                <span>+ Barang Masuk</span>
                            </label>
                            <label class="flex items-center justify-center gap-2 p-2 rounded-xl border cursor-pointer {{ $mutasiJenis === 'keluar' ? 'bg-rose-50 dark:bg-rose-900/30 border-rose-500 text-rose-700 dark:text-rose-300 font-bold' : 'border-border' }}">
                                <input type="radio" wire:model.live="mutasiJenis" value="keluar" class="hidden">
                                <span>- Pakai / Keluar</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-medium text-text-main mb-1">Tanggal <span class="text-rose-500">*</span></label>
                            <input type="date" wire:model="mutasiTanggal" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                        </div>
                        <div>
                            <label class="block font-medium text-text-main mb-1">Jumlah ({{ $mutasiBhp->satuan }}) <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model="mutasiJumlah" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" min="1">
                            @error('mutasiJumlah') <span class="text-rose-500 text-[11px] block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    @if($mutasiJenis === 'keluar')
                        <div>
                            <label class="block font-medium text-text-main mb-1">Untuk Ruangan / Unit Kerja</label>
                            <select wire:model="mutasiRuanganId" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                                <option value="">Pilih Ruangan</option>
                                @foreach($ruangans as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block font-medium text-text-main mb-1">{{ $mutasiJenis === 'masuk' ? 'Sumber / Pengadaan' : 'Nama Penerima / Guru' }}</label>
                        <input type="text" wire:model="mutasiPenerima" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" placeholder="{{ $mutasiJenis === 'masuk' ? 'Contoh: Pengadaan BOS 2026' : 'Contoh: Pak Budi (Guru RPL)' }}">
                    </div>

                    <div>
                        <label class="block font-medium text-text-main mb-1">Keterangan / Keperluan</label>
                        <textarea wire:model="mutasiKeterangan" rows="2" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" placeholder="Keperluan penggunaan..."></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="$set('showMutasiModal', false)" class="px-4 py-2 rounded-xl border border-border text-text-secondary hover:bg-surface-hover">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-primary-hover shadow-xs">Simpan Mutasi</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Riwayat Kartu Stok Mutasi --}}
    @if($showHistoryModal && $selectedBhpHistory)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface border border-border rounded-2xl w-full max-w-2xl p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <div>
                        <h3 class="text-base font-bold text-text-main">
                            Kartu Stok: {{ $selectedBhpHistory->nama_barang }}
                        </h3>
                        <p class="text-xs text-text-muted">Kode: {{ $selectedBhpHistory->kode_barang }} | Sisa Stok: <strong class="text-primary">{{ $selectedBhpHistory->stok_saat_ini }} {{ $selectedBhpHistory->satuan }}</strong></p>
                    </div>
                    <button type="button" wire:click="$set('showHistoryModal', false)" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                <div class="overflow-x-auto max-h-96">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-surface-hover/60 border-b border-border text-text-muted font-medium uppercase tracking-wider sticky top-0">
                            <tr>
                                <th class="py-2.5 px-3">Tanggal</th>
                                <th class="py-2.5 px-3">Jenis</th>
                                <th class="py-2.5 px-3 text-center">Jumlah</th>
                                <th class="py-2.5 px-3 text-center">Saldo</th>
                                <th class="py-2.5 px-3">Keterangan / Penerima</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($selectedBhpHistory->mutasis as $m)
                                <tr>
                                    <td class="py-2.5 px-3">{{ $m->tanggal?->format('d/m/Y') }}</td>
                                    <td class="py-2.5 px-3">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $m->jenis === 'masuk' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ $m->jenis === 'masuk' ? '+ Masuk' : '- Keluar' }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-bold">{{ $m->jumlah }}</td>
                                    <td class="py-2.5 px-3 text-center font-bold text-primary">{{ $m->sisa_stok_setelahnya }}</td>
                                    <td class="py-2.5 px-3">
                                        <div class="text-text-main font-medium">{{ $m->penerima ?: '-' }}</div>
                                        <div class="text-[10px] text-text-muted">{{ $m->ruangan?->nama_ruangan }} {{ $m->keterangan ? '('.$m->keterangan.')' : '' }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-text-muted">Belum ada riwayat mutasi stok.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                    <button type="button" wire:click="$set('showHistoryModal', false)" class="px-4 py-2 rounded-xl bg-surface-hover text-text-main text-xs font-semibold">Tutup</button>
                </div>
            </div>
        </div>
    @endif
</div>
