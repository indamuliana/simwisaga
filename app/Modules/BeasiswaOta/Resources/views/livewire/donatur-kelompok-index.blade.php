<div class="space-y-6">
    @include('beasiswaota::partials.sub-nav', ['active' => 'donatur-kelompok'])

    <!-- Notifikasi Alert -->
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl text-rose-800 dark:text-rose-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
        </div>
    @endif

    <!-- Tab Pilihan -->
    <div class="flex items-center justify-between border-b border-border pb-2">
        <div class="flex gap-2">
            <button wire:click="$set('activeTab', 'kelompok')" 
                    class="px-4 py-2 font-semibold text-sm rounded-xl transition {{ $activeTab === 'kelompok' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                Daftar Kelompok Donatur
            </button>
            <button wire:click="$set('activeTab', 'donatur')" 
                    class="px-4 py-2 font-semibold text-sm rounded-xl transition {{ $activeTab === 'donatur' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                Master Donatur (Rutin & Insidental)
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($activeTab === 'kelompok')
                <button wire:click="downloadTemplateKelompok" class="btn btn-outline btn-sm text-xs flex items-center gap-1.5" title="Unduh Template Excel Kelompok">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Template Excel
                </button>
                <button wire:click="$set('showImportKelompokModal', true)" class="btn btn-outline btn-sm text-xs flex items-center gap-1.5" title="Import Kelompok dari Excel">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import Excel
                </button>
                <button wire:click="exportKelompokExcel" class="btn btn-outline btn-sm text-xs flex items-center gap-1.5" title="Export Seluruh Kelompok ke Excel">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export Excel
                </button>
                <button wire:click="openCreateKelompok" class="btn btn-primary btn-sm text-xs flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    + Kelompok Baru
                </button>
            @else
                <button wire:click="downloadTemplateDonatur" class="btn btn-outline btn-sm text-xs flex items-center gap-1.5" title="Unduh Template Excel Donatur">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Template Excel
                </button>
                <button wire:click="$set('showImportDonaturModal', true)" class="btn btn-outline btn-sm text-xs flex items-center gap-1.5" title="Import Donatur dari Excel">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import Excel
                </button>
                <button wire:click="exportDonaturExcel" class="btn btn-outline btn-sm text-xs flex items-center gap-1.5" title="Export Seluruh Donatur ke Excel">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export Excel
                </button>
                <button wire:click="openCreateDonatur" class="btn btn-primary btn-sm text-xs flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    + Donatur Baru
                </button>
            @endif
        </div>
    </div>

    <!-- TAB 1: KELOMPOK DONATUR -->
    @if($activeTab === 'kelompok')
        <div class="card border border-border rounded-2xl p-6 bg-surface shadow-xs space-y-4">
            <!-- Bulk Action Bar Kelompok -->
            @if(count($selectedKelompokIds) > 0)
                <div class="p-3 bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 rounded-xl flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-indigo-900 dark:text-indigo-200">{{ count($selectedKelompokIds) }} kelompok dipilih</span>
                        <button wire:click="$set('selectedKelompokIds', [])" class="text-text-secondary hover:underline">(Batalkan pilihan)</button>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:confirm="Yakin ingin menghapus {{ count($selectedKelompokIds) }} kelompok yang dipilih?" 
                                wire:click="bulkDeleteKelompok" 
                                class="btn btn-error btn-xs text-white flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus Kelompok Terpilih ({{ count($selectedKelompokIds) }})
                        </button>
                        <button wire:confirm="PERINGATAN: Yakin ingin MENGHAPUS SELURUH KELOMPOK DONATUR? Data tidak dapat dipulihkan!" 
                                wire:click="deleteAllKelompok" 
                                class="btn btn-outline btn-error btn-xs">
                            Hapus Semua Kelompok
                        </button>
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-border bg-surface-hover/50 text-xs font-semibold text-text-secondary uppercase">
                            <th class="py-3 px-3 text-center" style="width: 40px;">
                                <input type="checkbox" wire:model.live="selectAllKelompok" class="checkbox checkbox-primary checkbox-xs" title="Pilih Semua Kelompok">
                            </th>
                            <th class="py-3 px-4">Kode & Nama Kelompok</th>
                            <th class="py-3 px-4">Siswa Binaan Asuh</th>
                            <th class="py-3 px-4 text-center">Jumlah Anggota</th>
                            <th class="py-3 px-4 text-right">Target Kebutuhan</th>
                            <th class="py-3 px-4 text-right">Total Komitmen Iuran</th>
                            <th class="py-3 px-4 text-center">Status Pembiayaan</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($kelompoks as $k)
                            @php
                                $target = (float) $k->target_nominal;
                                $komitmen = (float) $k->total_komitmen;
                                $siswa = $k->siswaPenerima?->siswa;
                            @endphp
                            <tr class="hover:bg-surface-hover/30 transition {{ in_array((string)$k->id, array_map('strval', $selectedKelompokIds)) ? 'bg-primary/5' : '' }}">
                                <td class="py-3.5 px-3 text-center">
                                    <input type="checkbox" wire:model.live="selectedKelompokIds" value="{{ $k->id }}" class="checkbox checkbox-primary checkbox-xs">
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-text-main">
                                    {{ $k->nama_kelompok }}
                                    <div class="text-xs font-normal text-text-secondary">Kode: {{ $k->kode_kelompok }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($siswa)
                                        <div class="font-medium text-text-main">{{ $siswa->nama }}</div>
                                        <div class="text-xs text-text-secondary">{{ $siswa->rombel?->nama ?? '-' }}</div>
                                    @else
                                        <a href="{{ route('beasiswa-ota.penerima') }}" class="text-xs text-amber-600 hover:underline flex items-center gap-1">
                                            + Tautkan Siswa Asuh
                                        </a>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <button wire:click="manageAnggota({{ $k->id }})" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        {{ $k->anggotaKelompok->count() }} Donatur
                                    </button>
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-text-main">
                                    Rp {{ number_format($target, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="font-bold {{ $komitmen >= $target ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600' }}">
                                        Rp {{ number_format($komitmen, 0, ',', '.') }}
                                    </div>
                                    @if($komitmen > $target)
                                        <div class="text-[10px] text-emerald-600 font-semibold">+Rp {{ number_format($komitmen - $target, 0, ',', '.') }} (Surplus)</div>
                                    @elseif($komitmen < $target)
                                        <div class="text-[10px] text-rose-500 font-semibold">-Rp {{ number_format($target - $komitmen, 0, ',', '.') }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($komitmen >= $target)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                            Terpenuhi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300">
                                            Kurang Donatur
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button wire:click="manageAnggota({{ $k->id }})" title="Atur Anggota Donatur" class="p-1.5 rounded-lg text-text-secondary hover:text-indigo-600 hover:bg-surface-hover">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        </button>
                                        <button wire:click="editKelompok({{ $k->id }})" title="Edit Kelompok" class="p-1.5 rounded-lg text-text-secondary hover:text-amber-600 hover:bg-surface-hover">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button wire:confirm="Yakin ingin menghapus kelompok ini?" wire:click="deleteKelompok({{ $k->id }})" title="Hapus Kelompok" class="p-1.5 rounded-lg text-text-secondary hover:text-rose-600 hover:bg-surface-hover">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-text-secondary">Belum ada kelompok donatur. Klik "Tambah Kelompok Baru" di atas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $kelompoks->links() }}</div>
        </div>
    @endif

    <!-- TAB 2: MASTER DONATUR -->
    @if($activeTab === 'donatur')
        <div class="card border border-border rounded-2xl p-6 bg-surface shadow-xs space-y-4">
            <!-- Bulk Action Bar Donatur -->
            @if(count($selectedDonaturIds) > 0)
                <div class="p-3 bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 rounded-xl flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-indigo-900 dark:text-indigo-200">{{ count($selectedDonaturIds) }} donatur dipilih</span>
                        <button wire:click="$set('selectedDonaturIds', [])" class="text-text-secondary hover:underline">(Batalkan pilihan)</button>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:confirm="Yakin ingin menghapus {{ count($selectedDonaturIds) }} donatur yang dipilih?" 
                                wire:click="bulkDeleteDonatur" 
                                class="btn btn-error btn-xs text-white flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus Donatur Terpilih ({{ count($selectedDonaturIds) }})
                        </button>
                        <button wire:confirm="PERINGATAN: Yakin ingin MENGHAPUS SELURUH DATA DONATUR? Data tidak dapat dipulihkan!" 
                                wire:click="deleteAllDonatur" 
                                class="btn btn-outline btn-error btn-xs">
                            Hapus Semua Donatur
                        </button>
                    </div>
                </div>
            @endif

            <div class="flex justify-between items-center">
                <input type="text" wire:model.live.debounce.300ms="searchDonatur" placeholder="Cari nama atau telepon donatur..." class="input input-bordered max-w-sm text-sm">
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-border bg-surface-hover/50 text-xs font-semibold text-text-secondary uppercase">
                            <th class="py-3 px-3 text-center" style="width: 40px;">
                                <input type="checkbox" wire:model.live="selectAllDonatur" class="checkbox checkbox-primary checkbox-xs" title="Pilih Semua Donatur">
                            </th>
                            <th class="py-3 px-4">Nama Donatur</th>
                            <th class="py-3 px-4">Tipe</th>
                            <th class="py-3 px-4">Kontak (HP / Email)</th>
                            <th class="py-3 px-4">Kelompok Terafiliasi</th>
                            <th class="py-3 px-4 text-right">Total Donasi Masuk</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($donaturs as $d)
                            <tr class="hover:bg-surface-hover/30 transition {{ in_array((string)$d->id, array_map('strval', $selectedDonaturIds)) ? 'bg-primary/5' : '' }}">
                                <td class="py-3.5 px-3 text-center">
                                    <input type="checkbox" wire:model.live="selectedDonaturIds" value="{{ $d->id }}" class="checkbox checkbox-primary checkbox-xs">
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-text-main">
                                    {{ $d->nama }}
                                    @if($d->alamat)
                                        <div class="text-xs font-normal text-text-secondary">{{ $d->alamat }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($d->tipe === 'rutin')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950/50 dark:text-blue-300">
                                            Rutin Kelompok
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300">
                                            Insidental / Bebas
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-text-secondary">
                                    <div>{{ $d->nomor_telepon ?: '-' }}</div>
                                    <div class="text-xs">{{ $d->email ?: '' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($d->kelompoks->isNotEmpty())
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($d->kelompoks as $klp)
                                                <span class="inline-block px-2 py-0.5 bg-surface-hover rounded text-xs text-text-main font-medium border border-border">
                                                    {{ $klp->nama_kelompok }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-text-secondary">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-text-main">
                                    Rp {{ number_format($d->total_donasi, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button wire:click="editDonatur({{ $d->id }})" title="Edit Donatur" class="p-1.5 rounded-lg text-text-secondary hover:text-amber-600 hover:bg-surface-hover">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button wire:confirm="Yakin ingin menghapus donatur ini?" wire:click="deleteDonatur({{ $d->id }})" title="Hapus Donatur" class="p-1.5 rounded-lg text-text-secondary hover:text-rose-600 hover:bg-surface-hover">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-text-secondary">Belum ada donatur terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $donaturs->links() }}</div>
        </div>
    @endif

    <!-- MODAL: KELOMPOK BARU / EDIT -->
    @if($showKelompokModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="bg-surface border border-border rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4">
                <div class="flex justify-between items-center border-b border-border pb-3">
                    <h3 class="text-lg font-bold text-text-main">
                        {{ $kelompokId ? 'Edit Kelompok Donatur' : 'Tambah Kelompok Donatur Baru' }}
                    </h3>
                    <button wire:click="$set('showKelompokModal', false)" class="text-text-secondary hover:text-text-main">&times;</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Kode Kelompok</label>
                        <input type="text" wire:model="kodeKelompok" class="input input-bordered w-full text-sm" placeholder="Misal: KEL-01">
                        @error('kodeKelompok') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Nama Kelompok</label>
                        <input type="text" wire:model="namaKelompok" class="input input-bordered w-full text-sm" placeholder="Misal: Kelompok Asuh Peduli 1">
                        @error('namaKelompok') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Target Nominal Pembiayaan (Rp) / Semester</label>
                        <input type="number" wire:model="targetNominal" class="input input-bordered w-full text-sm" placeholder="2000000">
                        <span class="text-[11px] text-text-secondary mt-1 block">Default Rp 2.000.000 per 6 bulan (dapat diubah sesuai kebutuhan siswa).</span>
                        @error('targetNominal') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Keterangan / Catatan</label>
                        <textarea wire:model="keteranganKelompok" rows="2" class="textarea textarea-bordered w-full text-sm" placeholder="Catatan tambahan kelompok..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-border">
                    <button wire:click="$set('showKelompokModal', false)" class="btn btn-ghost text-sm">Batal</button>
                    <button wire:click="saveKelompok" class="btn btn-primary text-sm">Simpan Kelompok</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: KELOLA ANGGOTA DONATUR DALAM KELOMPOK -->
    @if($showAnggotaModal && $selectedKelompok)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="bg-surface border border-border rounded-2xl max-w-2xl w-full p-6 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center border-b border-border pb-3">
                    <div>
                        <h3 class="text-lg font-bold text-text-main">
                            Anggota Kelompok: {{ $selectedKelompok->nama_kelompok }}
                        </h3>
                        <p class="text-xs text-text-secondary">
                            Target Biaya: Rp {{ number_format($selectedKelompok->target_nominal, 0, ',', '.') }} per semester.
                        </p>
                    </div>
                    <button wire:click="$set('showAnggotaModal', false)" class="text-text-secondary hover:text-text-main text-xl">&times;</button>
                </div>

                @if(session()->has('message_anggota'))
                    <div class="p-3 bg-emerald-50 text-emerald-800 text-xs rounded-lg border border-emerald-200">
                        {{ session('message_anggota') }}
                    </div>
                @endif
                @if(session()->has('error_anggota'))
                    <div class="p-3 bg-rose-50 text-rose-800 text-xs rounded-lg border border-rose-200">
                        {{ session('error_anggota') }}
                    </div>
                @endif

                <!-- Form Tambah Anggota Donatur ke Kelompok (Multi-Select Checkbox) -->
                <div class="p-4 bg-surface-hover/40 rounded-xl border border-border space-y-3">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                        <div>
                            <div class="font-bold text-xs text-text-main uppercase tracking-wider">
                                Tambah Donatur ke Kelompok Ini
                            </div>
                            <p class="text-[11px] text-text-secondary">Pilih satu atau centang banyak donatur rutin sekaligus untuk dimasukkan ke kelompok ini.</p>
                        </div>
                        @if(count($selectedDonaturModalIds) > 0)
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-primary/10 text-primary">
                                    {{ count($selectedDonaturModalIds) }} Donatur Terpilih
                                </span>
                                <button type="button" wire:click="$set('selectedDonaturModalIds', [])" class="text-[11px] text-text-secondary hover:underline">
                                    (Reset)
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Setting Nominal Komitmen Bersama & Tombol Tambah -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end bg-surface p-3 rounded-xl border border-border">
                        <div class="sm:col-span-5">
                            <label class="block text-[11px] font-semibold text-text-secondary mb-1">
                                Nominal Iuran Komitmen per Donatur (Rp)
                            </label>
                            <input type="text" inputmode="numeric" 
                                   wire:model.live.debounce.300ms="nominalKomitmen" 
                                   class="input input-bordered input-sm w-full text-xs font-bold" 
                                   placeholder="150000">
                            @if(!empty($nominalKomitmen))
                                <span class="text-[10px] text-primary font-medium block mt-0.5">
                                    Rp {{ number_format((float) \App\Modules\BeasiswaOta\Livewire\DonaturKelompokIndex::cleanNominal($nominalKomitmen), 0, ',', '.') }} / donatur
                                </span>
                            @endif
                            @error('nominalKomitmen') <span class="text-[10px] text-rose-500 block mt-0.5">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-4">
                            <label class="block text-[11px] font-semibold text-text-secondary mb-1">Keterangan Komitmen (Opsional)</label>
                            <input type="text" wire:model="keteranganKomitmen" class="input input-bordered input-sm w-full text-xs" placeholder="Misal: Iuran Rutin Bulanan">
                        </div>

                        <div class="sm:col-span-3">
                            <button wire:click="addAnggotaToKelompok" 
                                    class="btn btn-primary btn-sm w-full text-xs flex items-center justify-center gap-1.5"
                                    @disabled(count($selectedDonaturModalIds) === 0)>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                + Tambahkan ({{ count($selectedDonaturModalIds) }})
                            </button>
                        </div>
                    </div>

                    @if(count($selectedDonaturModalIds) > 0)
                        @php
                            $nomClean = (float) \App\Modules\BeasiswaOta\Livewire\DonaturKelompokIndex::cleanNominal($nominalKomitmen);
                            $simulasiTotal = count($selectedDonaturModalIds) * $nomClean;
                        @endphp
                        <div class="p-2.5 bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 rounded-lg text-xs text-indigo-900 dark:text-indigo-200 flex justify-between items-center">
                            <span>Simulasi Tambahan Komitmen:</span>
                            <span class="font-bold">
                                {{ count($selectedDonaturModalIds) }} donatur &times; Rp {{ number_format($nomClean, 0, ',', '.') }} = +Rp {{ number_format($simulasiTotal, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif

                    @error('selectedDonaturModalIds') 
                        <div class="text-xs text-rose-500 font-medium">{{ $message }}</div> 
                    @enderror

                    <!-- Daftar Donatur dengan Checkbox dan Pencarian -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <div class="relative flex-1">
                                <input type="text" wire:model.live.debounce.250ms="searchDonaturModal" 
                                       placeholder="Cari nama atau no HP donatur..." 
                                       class="input input-bordered input-xs w-full text-xs pl-7">
                                <svg class="w-3.5 h-3.5 text-text-secondary absolute left-2 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <label class="flex items-center gap-1.5 text-xs text-text-secondary cursor-pointer shrink-0 select-none hover:text-text-main">
                                <input type="checkbox" wire:model.live="selectAllModalDonatur" class="checkbox checkbox-primary checkbox-xs">
                                <span class="font-semibold">Pilih Semua</span>
                            </label>
                        </div>

                        @php
                            $currentMemberIds = $selectedKelompok->anggotaKelompok->pluck('donatur_id')->toArray();
                            $filteredDonaturs = $availableDonaturs->filter(function($d) use ($currentMemberIds) {
                                if (in_array($d->id, $currentMemberIds)) return false;
                                if (!empty($this->searchDonaturModal)) {
                                    $q = strtolower($this->searchDonaturModal);
                                    return str_contains(strtolower($d->nama), $q) || str_contains($d->nomor_telepon ?? '', $q);
                                }
                                return true;
                            });
                        @endphp

                        <div class="max-h-48 overflow-y-auto border border-border rounded-xl bg-surface divide-y divide-border text-xs">
                            @forelse($filteredDonaturs as $don)
                                @php $isSelected = in_array((string)$don->id, array_map('strval', $selectedDonaturModalIds)); @endphp
                                <label class="p-2.5 flex items-center justify-between gap-3 hover:bg-surface-hover/60 cursor-pointer transition {{ $isSelected ? 'bg-primary/5' : '' }}">
                                    <div class="flex items-center gap-2.5">
                                        <input type="checkbox" wire:model.live="selectedDonaturModalIds" value="{{ $don->id }}" class="checkbox checkbox-primary checkbox-xs">
                                        <div>
                                            <span class="font-bold text-text-main {{ $isSelected ? 'text-primary' : '' }}">{{ $don->nama }}</span>
                                            <span class="text-[11px] text-text-secondary ml-1">({{ $don->nomor_telepon ?: 'No HP -' }})</span>
                                        </div>
                                    </div>
                                    @if($don->kelompoks->count() > 0)
                                        <span class="text-[10px] text-text-secondary bg-surface-hover px-1.5 py-0.5 rounded">
                                            {{ $don->kelompoks->count() }} Kelompok lain
                                        </span>
                                    @endif
                                </label>
                            @empty
                                <div class="p-4 text-center text-text-secondary text-xs">
                                    @if(empty($availableDonaturs))
                                        Belum ada data Donatur Rutin aktif. Tambahkan donatur terlebih dahulu di tab Master Donatur.
                                    @else
                                        Tidak ada donatur rutin lain yang tersedia (semua sudah menjadi anggota di kelompok ini atau tidak cocok dengan kata kunci pencarian).
                                    @endif
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Tabel Anggota Kelompok -->
                <div class="border border-border rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-surface-hover text-text-secondary uppercase">
                            <tr>
                                <th class="py-2.5 px-3">No</th>
                                <th class="py-2.5 px-3">Nama Donatur</th>
                                <th class="py-2.5 px-3 text-right">Iuran Komitmen</th>
                                <th class="py-2.5 px-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @php 
                                $totalKmt = 0; 
                                $noK = 1;
                            @endphp
                            @forelse($selectedKelompok->anggotaKelompok as $ang)
                                @php $totalKmt += (float) $ang->nominal_komitmen; @endphp
                                <tr>
                                    <td class="py-2 px-3">{{ $noK++ }}</td>
                                    <td class="py-2 px-3 font-semibold text-text-main">
                                        {{ $ang->donatur->nama }}
                                        <div class="text-[10px] font-normal text-text-secondary">{{ $ang->donatur->nomor_telepon }}</div>
                                    </td>
                                    <td class="py-2 px-3 text-right font-bold text-text-main">
                                        Rp {{ number_format($ang->nominal_komitmen, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <button wire:confirm="Hapus donatur ini dari kelompok?" wire:click="removeAnggota({{ $ang->id }})" class="text-rose-500 hover:text-rose-700 font-bold p-1">
                                            &times; Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-text-secondary">Belum ada donatur di kelompok ini. Tambahkan di atas.</td>
                                </tr>
                            @endforelse
                            <tr class="bg-surface-hover/50 font-bold">
                                <td colspan="2" class="py-2.5 px-3 text-right">Total Komitmen Iuran Kelompok:</td>
                                <td class="py-2.5 px-3 text-right text-emerald-600 text-sm">
                                    Rp {{ number_format($totalKmt, 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Kalkulasi Simulasi Target vs Komitmen -->
                <div class="p-3 bg-surface-hover/30 rounded-xl border border-border flex justify-between items-center text-xs">
                    <div>
                        <span class="text-text-secondary">Target Kebutuhan Siswa:</span>
                        <strong class="text-text-main ml-1">Rp {{ number_format($selectedKelompok->target_nominal, 0, ',', '.') }}</strong>
                    </div>
                    <div>
                        @if($totalKmt >= $selectedKelompok->target_nominal)
                            <span class="text-emerald-600 font-bold">
                                Surplus +Rp {{ number_format($totalKmt - $selectedKelompok->target_nominal, 0, ',', '.') }}
                            </span>
                            <span class="text-text-secondary text-[10px] ml-1">(Otomatis dipindahkan ke kas insidental saat alokasi)</span>
                        @else
                            <span class="text-rose-500 font-bold">
                                Kurang -Rp {{ number_format($selectedKelompok->target_nominal - $totalKmt, 0, ',', '.') }}
                            </span>
                            <span class="text-text-secondary text-[10px] ml-1">(Butuh tambahan donatur / subsidi kas insidental)</span>
                        @endif
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-border">
                    <button wire:click="$set('showAnggotaModal', false)" class="btn btn-ghost text-sm">Tutup</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: DONATUR BARU / EDIT -->
    @if($showDonaturModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="bg-surface border border-border rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4">
                <div class="flex justify-between items-center border-b border-border pb-3">
                    <h3 class="text-lg font-bold text-text-main">
                        {{ $donaturId ? 'Edit Donatur' : 'Tambah Donatur Baru' }}
                    </h3>
                    <button wire:click="$set('showDonaturModal', false)" class="text-text-secondary hover:text-text-main">&times;</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Nama Lengkap Donatur</label>
                        <input type="text" wire:model="namaDonatur" class="input input-bordered w-full text-sm" placeholder="Misal: Bapak H. Ahmad / Ibu Siti">
                        @error('namaDonatur') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Jenis / Tipe Donatur</label>
                        <select wire:model="tipeDonatur" class="select select-bordered w-full text-sm">
                            <option value="rutin">Donatur Rutin (Tergabung dalam Kelompok Asuh)</option>
                            <option value="insidental">Donatur Insidental (Donasi Bebas / Fleksibel)</option>
                        </select>
                        @error('tipeDonatur') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Nomor WhatsApp / HP</label>
                            <input type="text" wire:model="nomorTelepon" class="input input-bordered w-full text-sm" placeholder="08xxxxxxxxxx">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Email</label>
                            <input type="email" wire:model="email" class="input input-bordered w-full text-sm" placeholder="donatur@email.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Alamat</label>
                        <input type="text" wire:model="alamat" class="input input-bordered w-full text-sm" placeholder="Alamat donatur / kota asal">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Keterangan Tambahan</label>
                        <textarea wire:model="keteranganDonatur" rows="2" class="textarea textarea-bordered w-full text-sm" placeholder="Catatan profesi / instansi / lainnya..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-border">
                    <button wire:click="$set('showDonaturModal', false)" class="btn btn-ghost text-sm">Batal</button>
                    <button wire:click="saveDonatur" class="btn btn-primary text-sm">Simpan Donatur</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: IMPORT DONATUR DARI EXCEL -->
    @if($showImportDonaturModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="bg-surface border border-border rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4">
                <div class="flex justify-between items-center border-b border-border pb-3">
                    <h3 class="text-lg font-bold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Import Daftar Donatur dari Excel
                    </h3>
                    <button wire:click="$set('showImportDonaturModal', false)" class="text-text-secondary hover:text-text-main text-xl">&times;</button>
                </div>

                <div class="p-3 bg-surface-hover/50 rounded-xl border border-border text-xs space-y-2">
                    <div class="font-bold text-text-main">Petunjuk Import:</div>
                    <ul class="list-disc list-inside space-y-1 text-text-secondary">
                        <li>Gunakan file format <strong>.xlsx</strong> atau <strong>.xls</strong>.</li>
                        <li>Pastikan kolom sesuai dengan template resmi SIRAGA.</li>
                        <li>Jika nama donatur sudah ada, datanya akan diperbarui secara otomatis.</li>
                    </ul>
                    <div class="pt-1">
                        <button type="button" wire:click="downloadTemplateDonatur" class="text-xs text-primary hover:underline font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Klik di sini untuk mengunduh Template Excel Donatur
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-text-secondary uppercase">Pilih File Excel (.xlsx / .xls)</label>
                    <input type="file" wire:model="fileImportDonatur" accept=".xlsx,.xls" class="file-input file-input-bordered file-input-sm w-full text-xs">
                    <div wire:loading wire:target="fileImportDonatur" class="text-xs text-primary mt-1">Mengunggah file...</div>
                    @error('fileImportDonatur') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-border">
                    <button wire:click="$set('showImportDonaturModal', false)" class="btn btn-ghost text-sm">Batal</button>
                    <button wire:click="importDonaturExcel" wire:loading.attr="disabled" class="btn btn-primary text-sm flex items-center gap-2">
                        <span wire:loading wire:target="importDonaturExcel" class="loading loading-spinner loading-xs"></span>
                        Proses Import Donatur
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: IMPORT KELOMPOK DARI EXCEL -->
    @if($showImportKelompokModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="bg-surface border border-border rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4">
                <div class="flex justify-between items-center border-b border-border pb-3">
                    <h3 class="text-lg font-bold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Import Kelompok & Anggota dari Excel
                    </h3>
                    <button wire:click="$set('showImportKelompokModal', false)" class="text-text-secondary hover:text-text-main text-xl">&times;</button>
                </div>

                <div class="p-3 bg-surface-hover/50 rounded-xl border border-border text-xs space-y-2">
                    <div class="font-bold text-text-main">Petunjuk Import:</div>
                    <ul class="list-disc list-inside space-y-1 text-text-secondary">
                        <li>Gunakan file format <strong>.xlsx</strong> atau <strong>.xls</strong>.</li>
                        <li>Satu kelompok dapat memuat beberapa baris untuk masing-masing donatur anggotanya.</li>
                        <li>Jika nama donatur belum ada di sistem, akun donatur akan otomatis dibuatkan.</li>
                    </ul>
                    <div class="pt-1">
                        <button type="button" wire:click="downloadTemplateKelompok" class="text-xs text-primary hover:underline font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Klik di sini untuk mengunduh Template Excel Kelompok
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-text-secondary uppercase">Pilih File Excel (.xlsx / .xls)</label>
                    <input type="file" wire:model="fileImportKelompok" accept=".xlsx,.xls" class="file-input file-input-bordered file-input-sm w-full text-xs">
                    <div wire:loading wire:target="fileImportKelompok" class="text-xs text-primary mt-1">Mengunggah file...</div>
                    @error('fileImportKelompok') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-border">
                    <button wire:click="$set('showImportKelompokModal', false)" class="btn btn-ghost text-sm">Batal</button>
                    <button wire:click="importKelompokExcel" wire:loading.attr="disabled" class="btn btn-primary text-sm flex items-center gap-2">
                        <span wire:loading wire:target="importKelompokExcel" class="loading loading-spinner loading-xs"></span>
                        Proses Import Kelompok
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
