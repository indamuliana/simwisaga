<div class="space-y-6">
    @include('sarpras::partials.sub-nav', ['active' => 'aset'])

    {{-- Alert Flash Message --}}
    @if(session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('message') }}</span>
            </div>
            <div class="flex items-center gap-2">
                @if(session()->has('batch_print_ids'))
                    <a href="{{ route('sarpras.cetak.label-qr', ['ids' => session('batch_print_ids')]) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-xs font-semibold flex items-center gap-1.5 shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        Cetak {{ session('batch_count', 1) }} Label QR Ini
                    </a>
                @endif
                <button type="button" @click="$el.closest('div.p-4').remove()" class="text-emerald-500 hover:text-emerald-700 text-lg">&times;</button>
            </div>
        </div>
    @endif

    {{-- Alert Error Message --}}
    @if(session()->has('error'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.closest('div.p-4').remove()" class="text-rose-500 hover:text-rose-700 text-lg">&times;</button>
        </div>
    @endif

    {{-- Filter & Action Bar --}}
    <div class="bg-surface border border-border rounded-2xl p-4 shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
            <!-- Search -->
            <div class="relative flex-1">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Cari kode aset, nama barang, merk, nomor seri..." 
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-border bg-bg text-text-main focus:outline-hidden focus:ring-2 focus:ring-primary">
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <a href="{{ route('sarpras.cetak.label-qr') }}" target="_blank" class="px-3 py-2 rounded-xl border border-border bg-surface text-text-main hover:bg-surface-hover text-xs font-semibold flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    Cetak Label QR
                </a>

                @can('sarpras.aset.tambah')
                <button type="button" wire:click="create" class="px-3.5 py-2 rounded-xl bg-primary text-white hover:bg-primary-hover text-xs font-semibold flex items-center gap-1.5 transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Aset Baru
                </button>
                @endcan
            </div>
        </div>

        <!-- Filter Dropdowns -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 pt-2 border-t border-border">
            <select wire:model.live="filterRuangan" class="text-xs rounded-lg border border-border bg-bg text-text-main py-1.5 px-2">
                <option value="">Semua Ruangan</option>
                @foreach($ruangans as $r)
                    <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterKategori" class="text-xs rounded-lg border border-border bg-bg text-text-main py-1.5 px-2">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $k)
                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterKondisi" class="text-xs rounded-lg border border-border bg-bg text-text-main py-1.5 px-2">
                <option value="">Semua Kondisi</option>
                <option value="Baik">Baik</option>
                <option value="Rusak Ringan">Rusak Ringan</option>
                <option value="Rusak Berat">Rusak Berat</option>
                <option value="Hilang">Hilang</option>
                <option value="Dimusnahkan">Dimusnahkan</option>
            </select>

            <select wire:model.live="filterStatus" class="text-xs rounded-lg border border-border bg-bg text-text-main py-1.5 px-2">
                <option value="">Semua Status</option>
                <option value="Tersedia">Tersedia</option>
                <option value="Dipinjam">Dipinjam</option>
                <option value="Dalam Perbaikan">Dalam Perbaikan</option>
                <option value="Dihapuskan">Dihapuskan</option>
            </select>

            <select wire:model.live="filterSumberDanaId" class="text-xs rounded-lg border border-border bg-bg text-text-main py-1.5 px-2">
                <option value="">Semua Sumber Dana</option>
                @foreach($sumberDanas as $s)
                    <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->kode }})</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tabel Aset Tetap --}}
    <div class="bg-surface border border-border rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-hover/60 border-b border-border text-text-muted font-medium uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Kode Aset</th>
                        <th class="py-3 px-4">Nama Barang & Merk</th>
                        <th class="py-3 px-4">Kategori & Ruangan</th>
                        <th class="py-3 px-4">Tahun / Sumber</th>
                        <th class="py-3 px-4">Kondisi</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Harga (Rp)</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($asets as $a)
                        <tr class="hover:bg-surface-hover/50 transition">
                            <td class="py-3 px-4 font-mono font-semibold text-primary">
                                {{ $a->kode_aset }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-text-main">{{ $a->nama_barang }}</div>
                                <div class="text-[11px] text-text-muted">{{ $a->merk_tipe ?: '-' }} @if($a->nomor_seri) &bull; SN: {{ $a->nomor_seri }} @endif</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-block px-1.5 py-0.5 rounded-md bg-surface-hover text-text-secondary font-medium text-[10px]">
                                    {{ $a->kategori?->nama }}
                                </span>
                                <div class="text-[11px] text-text-main mt-0.5">{{ $a->ruangan?->nama_ruangan ?? 'Belum ditentukan' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div>{{ $a->tahun_perolehan }}</div>
                                <div class="text-[11px] text-text-muted font-medium">{{ $a->sumberDana?->nama ?? $a->sumber_dana }}</div>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $badgeKondisi = match($a->kondisi) {
                                        'Baik' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                        'Rusak Ringan' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                        'Rusak Berat' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
                                        default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $badgeKondisi }}">
                                    {{ $a->kondisi }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $badgeStatus = match($a->status) {
                                        'Tersedia' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                        'Dipinjam' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
                                        'Dalam Perbaikan' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                        default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $badgeStatus }}">
                                    {{ $a->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-medium text-text-main">
                                {{ number_format($a->harga_perolehan, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" wire:click="showDetail({{ $a->id }})" class="p-1 rounded-lg hover:bg-surface-hover text-text-muted hover:text-primary transition" title="Lihat Detail & QR Code">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>

                                    @can('sarpras.aset.edit')
                                    <button type="button" wire:click="edit({{ $a->id }})" class="p-1 rounded-lg hover:bg-surface-hover text-text-muted hover:text-amber-600 transition" title="Edit Aset">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @endcan

                                    @can('sarpras.aset.hapus')
                                    <button type="button" 
                                            wire:confirm="Yakin ingin menghapus data aset {{ $a->kode_aset }}?"
                                            wire:click="delete({{ $a->id }})" 
                                            class="p-1 rounded-lg hover:bg-surface-hover text-text-muted hover:text-rose-600 transition" title="Hapus Aset">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-text-muted">
                                Tidak ada data aset yang sesuai dengan pencarian atau filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($asets->hasPages())
            <div class="p-4 border-t border-border">
                {{ $asets->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Tambah / Edit Aset --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface border border-border rounded-2xl w-full max-w-2xl p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-bold text-text-main">
                        {{ $isEdit ? 'Edit Data Aset' : 'Tambah Aset Tetap Baru' }}
                    </h3>
                    <button type="button" wire:click="$set('showModal', false)" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        @if(!$isEdit)
                        <!-- Jumlah Unit (Qty) -->
                        <div class="sm:col-span-2 bg-primary/5 border border-primary/20 rounded-xl p-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div>
                                    <label class="block font-semibold text-text-main text-xs">
                                        Jumlah Unit Pengadaan (Qty) <span class="text-rose-500">*</span>
                                    </label>
                                    <span class="text-[11px] text-text-muted">Isi lebih dari 1 untuk pengadaan barang banyak sekaligus (misal: 20 kursi).</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="number" wire:model.live="jumlah" min="1" max="100" class="w-24 text-center rounded-lg border border-border bg-bg text-text-main py-1.5 px-2 font-bold text-sm focus:ring-2 focus:ring-primary">
                                    <span class="text-xs font-semibold text-text-muted">Unit</span>
                                </div>
                            </div>
                            @error('jumlah') <span class="text-rose-500 text-[11px] block mt-1">{{ $message }}</span> @enderror
                            @if($jumlah > 1)
                                <p class="text-[11px] text-primary mt-2 font-medium flex items-center gap-1.5">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Sistem akan otomatis mencatat <strong>{{ $jumlah }} unit individual</strong> dengan akhiran nama <code>(Unit 1)</code> s/d <code>(Unit {{ $jumlah }})</code>, nomor kode berurut, dan QR Code masing-masing.
                                </p>
                            @endif
                        </div>
                        @endif

                        <!-- Kategori -->
                        <div>
                            <label class="block font-medium text-text-main mb-1">Kategori Aset <span class="text-rose-500">*</span></label>
                            <select wire:model.live="kategori_id" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3 focus:ring-2 focus:ring-primary">
                                <option value="">Pilih Kategori</option>
                                @foreach($kategoris as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }} ({{ $k->kode }})</option>
                                @endforeach
                            </select>
                            @error('kategori_id') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Kode Aset -->
                        <div>
                            <label class="block font-medium text-text-main mb-1">
                                Kode Aset {{ !$isEdit && $jumlah > 1 ? '(Nomor Awal)' : '(Auto / Editable)' }} <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" wire:model="kode_aset" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3 font-mono focus:ring-2 focus:ring-primary" placeholder="AST/ELK/2026/0001">
                            @if(!$isEdit && $jumlah > 1 && $kode_aset)
                                <p class="text-[11px] text-text-muted mt-1 font-mono">
                                    Range: <span class="text-primary font-semibold">{{ $kode_aset }}</span> s/d <span class="text-primary font-semibold">{{ $this->previewKodeAkhir }}</span>
                                </p>
                            @endif
                            @error('kode_aset') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nama Barang -->
                        <div class="sm:col-span-2">
                            <label class="block font-medium text-text-main mb-1">Nama Barang / Aset <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="nama_barang" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3 focus:ring-2 focus:ring-primary" placeholder="Contoh: Laptop Asus Vivobook 14">
                            @error('nama_barang') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Merk / Model -->
                        <div>
                            <label class="block font-medium text-text-main mb-1">Merk / Tipe / Spesifikasi</label>
                            <input type="text" wire:model="merk_tipe" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3 focus:ring-2 focus:ring-primary" placeholder="Contoh: Core i5 8GB SSD 512GB">
                        </div>

                        <!-- Nomor Seri -->
                        <div>
                            <label class="block font-medium text-text-main mb-1">Nomor Seri / Pabrik</label>
                            <input type="text" wire:model="nomor_seri" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3 focus:ring-2 focus:ring-primary" placeholder="Contoh: SN-882910398">
                        </div>

                        <!-- Ruangan -->
                        <div>
                            <label class="block font-medium text-text-main mb-1">Ruangan / Lokasi</label>
                            <select wire:model="ruangan_id" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3 focus:ring-2 focus:ring-primary">
                                <option value="">Pilih Ruangan</option>
                                @foreach($ruangans as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tahun Perolehan -->
                        <div>
                            <label class="block font-medium text-text-main mb-1">Tahun Perolehan <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model.live="tahun_perolehan" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3 focus:ring-2 focus:ring-primary">
                        </div>

                        <!-- Tanggal Pengadaan -->
                        <div>
                            <label class="block font-medium text-text-main mb-1">Tanggal Pengadaan</label>
                            <input type="date" wire:model="tanggal_pengadaan" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3 focus:ring-2 focus:ring-primary">
                        </div>

                        <!-- Sumber Dana -->
                        <div>
                            <label class="block font-medium text-text-main mb-1">Sumber Dana (Otomatis Update Kode)</label>
                            <select wire:model.live="sumber_dana_id" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3 focus:ring-2 focus:ring-primary">
                                <option value="">Pilih Sumber Dana</option>
                                @foreach($sumberDanas as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->kode }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Harga Perolehan -->
                        <div>
                            <label class="block font-medium text-text-main mb-1">
                                Harga Satuan (Rp/Unit) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" wire:model.live="harga_perolehan" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3 focus:ring-2 focus:ring-primary" min="0">
                            @if(!$isEdit && $jumlah > 1 && $harga_perolehan > 0)
                                <p class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-1 font-semibold">
                                    Total Belanja ({{ $jumlah }} unit): Rp {{ number_format($jumlah * $harga_perolehan, 0, ',', '.') }}
                                </p>
                            @endif
                            @error('harga_perolehan') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Kondisi -->
                        <div>
                            <label class="block font-medium text-text-main mb-1">Kondisi Fisik</label>
                            <select wire:model="kondisi" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3 focus:ring-2 focus:ring-primary">
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                                <option value="Hilang">Hilang</option>
                                <option value="Dimusnahkan">Dimusnahkan</option>
                            </select>
                        </div>

                        <!-- Status Operasional -->
                        <div>
                            <label class="block font-medium text-text-main mb-1">Status Operasional</label>
                            <select wire:model="status" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3 focus:ring-2 focus:ring-primary">
                                <option value="Tersedia">Tersedia</option>
                                <option value="Dipinjam">Dipinjam</option>
                                <option value="Dalam Perbaikan">Dalam Perbaikan</option>
                                <option value="Dihapuskan">Dihapuskan</option>
                            </select>
                        </div>

                        <!-- Keterangan -->
                        <div class="sm:col-span-2">
                            <label class="block font-medium text-text-main mb-1">Keterangan / Catatan Tambahan</label>
                            <textarea wire:model="keterangan" rows="2" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3 focus:ring-2 focus:ring-primary" placeholder="Catatan inventaris..."></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-border">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 rounded-xl border border-border text-xs font-semibold text-text-secondary hover:bg-surface-hover">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white text-xs font-semibold hover:bg-primary-hover shadow-xs">Simpan Aset</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Detail Aset & QR Code --}}
    @if($showDetailModal && $selectedAset)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface border border-border rounded-2xl w-full max-w-xl p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-bold text-text-main">
                        Detail Kartu Aset: {{ $selectedAset->kode_aset }}
                    </h3>
                    <button type="button" wire:click="$set('showDetailModal', false)" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                <div class="flex flex-col sm:flex-row gap-5 items-center sm:items-start text-xs">
                    <!-- QR Code Card -->
                    <div class="p-3 bg-white rounded-xl border border-gray-200 text-center shadow-xs flex-shrink-0">
                        {!! \App\Modules\Sarpras\Services\QrCodeService::renderHtml($selectedAset->kode_aset, 120) !!}
                        <div class="mt-2 font-mono font-bold text-[10px] text-gray-800">{{ $selectedAset->kode_aset }}</div>
                        <div class="text-[9px] text-gray-500">Scan untuk cek aset</div>
                    </div>

                    <!-- Details -->
                    <div class="space-y-2 flex-1 w-full">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <span class="text-text-muted text-[10px] uppercase">Nama Barang</span>
                                <p class="font-bold text-text-main text-sm">{{ $selectedAset->nama_barang }}</p>
                            </div>
                            <div>
                                <span class="text-text-muted text-[10px] uppercase">Merk / Model</span>
                                <p class="font-semibold text-text-main">{{ $selectedAset->merk_tipe ?: '-' }}</p>
                            </div>
                            <div>
                                <span class="text-text-muted text-[10px] uppercase">Nomor Seri</span>
                                <p class="font-mono text-text-main">{{ $selectedAset->nomor_seri ?: '-' }}</p>
                            </div>
                            <div>
                                <span class="text-text-muted text-[10px] uppercase">Ruangan</span>
                                <p class="font-semibold text-primary">{{ $selectedAset->ruangan?->nama_ruangan ?? 'Belum Ditentukan' }}</p>
                            </div>
                            <div>
                                <span class="text-text-muted text-[10px] uppercase">Tahun & Sumber Dana</span>
                                <p class="text-text-main">{{ $selectedAset->tahun_perolehan }} ({{ $selectedAset->sumberDana?->nama ?? $selectedAset->sumber_dana }})</p>
                            </div>
                            <div>
                                <span class="text-text-muted text-[10px] uppercase">Harga Perolehan</span>
                                <p class="font-bold text-text-main">Rp {{ number_format($selectedAset->harga_perolehan, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <span class="text-text-muted text-[10px] uppercase">Kondisi Fisik</span>
                                <p class="font-semibold text-text-main">{{ $selectedAset->kondisi }}</p>
                            </div>
                            <div>
                                <span class="text-text-muted text-[10px] uppercase">Status Operasional</span>
                                <p class="font-semibold text-text-main">{{ $selectedAset->status }}</p>
                            </div>
                        </div>

                        @if($selectedAset->keterangan)
                            <div class="mt-2 pt-2 border-t border-border">
                                <span class="text-text-muted text-[10px] uppercase">Keterangan:</span>
                                <p class="text-text-secondary italic mt-0.5">{{ $selectedAset->keterangan }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-border">
                    <button type="button" wire:click="$set('showDetailModal', false)" class="px-4 py-2 rounded-xl bg-surface-hover text-text-main text-xs font-semibold hover:bg-border">Tutup</button>
                </div>
            </div>
        </div>
    @endif
</div>
