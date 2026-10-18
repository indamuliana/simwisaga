<div class="space-y-6">
    @include('beasiswaota::partials.sub-nav', ['active' => 'donasi-masuk'])

    <!-- Alert Notifikasi -->
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl text-rose-800 dark:text-rose-200 text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
        </div>
    @endif

    <div class="card border border-border rounded-2xl p-6 bg-surface shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <!-- Filter & Search -->
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari donatur / no. transaksi / koordinator / batch..." class="input input-bordered input-sm text-xs w-full sm:w-64">

                <select wire:model.live="filterKelompok" class="select select-bordered select-sm text-xs">
                    <option value="">Semua Kelompok</option>
                    @foreach($kelompoks as $klp)
                        <option value="{{ $klp->id }}">{{ $klp->nama_kelompok }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterTipe" class="select select-bordered select-sm text-xs">
                    <option value="">Semua Tipe Donatur</option>
                    <option value="rutin">Rutin (Kelompok)</option>
                    <option value="insidental">Insidental</option>
                </select>

                <select wire:model.live="filterMetode" class="select select-bordered select-sm text-xs">
                    <option value="">Semua Metode Setor</option>
                    <option value="perorangan">Perorangan (Individu)</option>
                    <option value="kolektif">Kolektif via Koordinator</option>
                </select>
            </div>

            <a href="{{ route('beasiswa-ota.donasi-masuk.create') }}" wire:navigate class="btn btn-primary btn-sm text-xs flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Catat Donasi Masuk
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-border bg-surface-hover/50 text-xs font-semibold text-text-secondary uppercase">
                        <th class="py-3 px-4">No. Transaksi & Tanggal</th>
                        <th class="py-3 px-4">Nama Donatur</th>
                        <th class="py-3 px-4">Periode & Alokasi</th>
                        <th class="py-3 px-4">Metode & Penyetor</th>
                        <th class="py-3 px-4 text-right">Nominal Donasi</th>
                        <th class="py-3 px-4">Keterangan</th>
                        <th class="py-3 px-4 text-center">Cetak Dokumen</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($donasis as $d)
                        <tr class="hover:bg-surface-hover/30 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-mono text-xs font-bold text-text-main">{{ $d->nomor_transaksi }}</div>
                                <div class="text-xs text-text-secondary">{{ $d->tanggal_donasi->translatedFormat('d F Y') }}</div>
                                @if($d->batch_kode)
                                    <div class="font-mono text-[10px] text-indigo-600 dark:text-indigo-400 mt-0.5" title="Kode Batch Kolektif">
                                        {{ $d->batch_kode }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-text-main">{{ $d->donatur->nama }}</div>
                                <span class="inline-block mt-0.5 px-2 py-0.2 rounded text-[10px] font-semibold {{ $d->tipe_donatur === 'rutin' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300' }}">
                                    Donatur {{ ucfirst($d->tipe_donatur) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($d->kelompok)
                                    <div class="font-semibold text-xs text-text-main">{{ $d->kelompok->nama_kelompok }}</div>
                                    <div class="text-[10px] text-text-secondary">{{ $d->kelompok->kode_kelompok }}</div>
                                @else
                                    <span class="text-xs font-medium text-amber-700 dark:text-amber-300">
                                        Kas Bebas (Insidental)
                                    </span>
                                @endif

                                @if($d->keterangan_periode)
                                    <div class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $d->keterangan_periode }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($d->is_kolektif)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 dark:bg-purple-950/50 dark:text-purple-300">
                                        Kolektif Kelompok
                                    </span>
                                    <div class="text-[11px] text-text-secondary mt-0.5">
                                        Koordinator: <span class="font-medium text-text-main">{{ $d->penyetor_nama ?: '-' }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                        Perorangan Langsung
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($d->nominal, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-xs text-text-secondary max-w-xs truncate">
                                {{ $d->keterangan ?: '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex flex-wrap items-center justify-center gap-1.5">
                                    <a href="{{ route('beasiswa-ota.cetak.tanda-terima', $d->id) }}" target="_blank" 
                                       title="Cetak Tanda Terima Pribadi Donatur (A5)"
                                       class="btn btn-xs btn-outline border-indigo-200 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-50 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        Tanda Terima
                                    </a>

                                    @if($d->is_kolektif && $d->batch_kode)
                                        <a href="{{ route('beasiswa-ota.cetak.tanda-terima-kolektif', $d->batch_kode) }}" target="_blank" 
                                           title="Cetak Tanda Terima Kolektif Kelompok untuk Koordinator (A4)"
                                           class="btn btn-xs btn-outline border-purple-200 text-purple-700 dark:text-purple-300 hover:bg-purple-50 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Kolektif
                                        </a>
                                    @endif

                                    <a href="{{ route('beasiswa-ota.cetak.sertifikat', $d->id) }}" target="_blank" 
                                       title="Cetak Sertifikat Piagam Penghargaan Donatur (A4)"
                                       class="btn btn-xs btn-outline border-amber-200 text-amber-700 dark:text-amber-300 hover:bg-amber-50 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                        Sertifikat
                                    </a>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button wire:confirm="Hapus catatan transaksi donasi ini? Kas mutasi akan otomatis disesuaikan." 
                                            wire:click="delete({{ $d->id }})" 
                                            title="Hapus Transaksi"
                                            class="text-rose-500 hover:text-rose-700 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @if($d->is_kolektif && $d->batch_kode)
                                        <button wire:confirm="PERINGATAN: Hapus SELURUH donasi dalam setoran kolektif batch {{ $d->batch_kode }} ini?" 
                                                wire:click="deleteBatch('{{ $d->batch_kode }}')" 
                                                title="Hapus Seluruh Batch Kolektif"
                                                class="text-xs text-rose-600 hover:underline ml-1">
                                            (Batal Batch)
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-text-secondary">Belum ada donasi masuk yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $donasis->links() }}</div>
    </div>
</div>
