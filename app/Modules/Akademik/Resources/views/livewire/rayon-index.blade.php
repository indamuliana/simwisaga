<div class="space-y-6">
    <!-- Header Halaman -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Pengelolaan Rayon</h1>
            <p class="text-sm text-text-secondary">Pengelompokan siswa berdasarkan wilayah tempat tinggal dan guru pembimbing rayon.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative w-full sm:w-64">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari rayon, wilayah, pembimbing..."
                    class="w-full text-xs rounded-lg border-border bg-surface text-text-main pl-9 pr-3 py-2 focus:ring-1 focus:ring-primary focus:border-primary"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-text-muted">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            @can('akademik.rayon.tambah')
            <button wire:click="create" class="btn btn-primary inline-flex items-center gap-1.5 shadow-sm text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Rayon</span>
            </button>
            @endcan
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if (session('status'))
        <div class="p-4 text-sm text-emerald-800 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 text-sm text-rose-800 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 rounded-xl border border-rose-200 dark:border-rose-800 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Tabel Daftar Rayon -->
    <div class="card overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border text-sm">
                <thead class="bg-surface-hover/70 text-left">
                    <tr>
                        <th class="px-5 py-3.5 font-semibold text-text-secondary text-xs uppercase tracking-wider">Nama Rayon</th>
                        <th class="px-5 py-3.5 font-semibold text-text-secondary text-xs uppercase tracking-wider">Wilayah Cakupan</th>
                        <th class="px-5 py-3.5 font-semibold text-text-secondary text-xs uppercase tracking-wider">Pembimbing Rayon</th>
                        <th class="px-5 py-3.5 font-semibold text-text-secondary text-xs uppercase tracking-wider text-center">Jumlah Siswa</th>
                        <th class="px-5 py-3.5 font-semibold text-text-secondary text-xs uppercase tracking-wider">Keterangan</th>
                        <th class="px-5 py-3.5 font-semibold text-text-secondary text-xs uppercase tracking-wider text-center w-48">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-text-main">
                    @forelse ($rayons as $r)
                        <tr class="hover:bg-surface-hover/60 transition-colors">
                            <td class="whitespace-nowrap px-5 py-3.5 font-bold text-primary">
                                {{ $r->nama }}
                            </td>
                            <td class="px-5 py-3.5 font-medium text-text-main">
                                {{ $r->wilayah_cakupan ?: '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-text-secondary">
                                @if($r->pembimbing)
                                    <div class="font-medium text-text-main">{{ $r->pembimbing->name }}</div>
                                    <div class="text-xs text-text-muted">{{ $r->pembimbing->nip ?? $r->pembimbing->username }}</div>
                                @else
                                    <span class="text-xs italic text-amber-600 dark:text-amber-400">Belum Ditentukan</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20">
                                    {{ $r->siswas_count }} Siswa
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-text-muted max-w-xs truncate">
                                {{ $r->keterangan ?: '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    {{-- Tombol Lihat (Detail) --}}
                                    @can('akademik.rayon.lihat')
                                    <button
                                        wire:click="showDetail({{ $r->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-400 dark:hover:bg-emerald-900/50 rounded-lg transition-colors cursor-pointer"
                                        title="Lihat Rincian Rayon">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Lihat</span>
                                    </button>
                                    @endcan

                                    {{-- Tombol Edit --}}
                                    @can('akademik.rayon.edit')
                                    <button
                                        wire:click="edit({{ $r->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/40 dark:text-indigo-400 dark:hover:bg-indigo-900/50 rounded-lg transition-colors cursor-pointer"
                                        title="Edit Rayon">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Edit</span>
                                    </button>
                                    @endcan

                                    {{-- Tombol Hapus --}}
                                    @can('akademik.rayon.hapus')
                                    <button
                                        wire:click="delete({{ $r->id }})"
                                        wire:confirm="Apakah Anda yakin ingin menghapus Rayon '{{ $r->nama }}'?"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-400 dark:hover:bg-rose-900/50 rounded-lg transition-colors cursor-pointer"
                                        title="Hapus Rayon">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Hapus</span>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-sm text-text-muted">
                                @if($search)
                                    Tidak ada data rayon yang sesuai dengan pencarian "<span class="font-semibold">{{ $search }}</span>".
                                @else
                                    Belum ada data rayon.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL TAMBAH / EDIT RAYON                               --}}
    {{-- ======================================================== --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div class="card w-full max-w-lg space-y-4 shadow-xl border border-border">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h2 class="text-lg font-bold text-text-main">
                        {{ $rayonId ? 'Edit Rayon' : 'Tambah Rayon Baru' }}
                    </h2>
                    <button wire:click="$set('showModal', false)" class="text-text-muted hover:text-text-main text-xl leading-none">&times;</button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="label font-semibold text-xs text-text-secondary">Nama Rayon * (contoh: Bales 1, Tarkid 1)</label>
                        <input type="text" wire:model="nama" placeholder="Bales 1" class="input">
                        @error('nama') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label font-semibold text-xs text-text-secondary">Wilayah Cakupan (contoh: Banyuresmi, Leles)</label>
                        <input type="text" wire:model="wilayah_cakupan" placeholder="Banyuresmi, Leles" class="input">
                        @error('wilayah_cakupan') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label font-semibold text-xs text-text-secondary">Guru Pembimbing Rayon</label>
                        <select wire:model="pembimbing_id" class="input">
                            <option value="">-- Pilih Pembimbing Rayon --</option>
                            @foreach ($gurus as $g)
                                <option value="{{ $g->id }}">{{ $g->name }} ({{ $g->nip ?? $g->username }})</option>
                            @endforeach
                        </select>
                        @error('pembimbing_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label font-semibold text-xs text-text-secondary">Keterangan</label>
                        <textarea wire:model="keterangan" rows="2" placeholder="Catatan tambahan seputar wilayah / pembimbingan..." class="input"></textarea>
                        @error('keterangan') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-2 border-t border-border pt-4">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-secondary text-xs">Batal</button>
                        <button type="submit" class="btn btn-primary text-xs" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">Simpan Rayon</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ======================================================== --}}
    {{-- MODAL LIHAT / DETAIL RAYON                              --}}
    {{-- ======================================================== --}}
    @if ($showDetailModal && $detailRayon)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div class="bg-surface rounded-2xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl border border-border overflow-hidden">
                <!-- Header Modal -->
                <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-surface-hover/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold text-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-text-main flex items-center gap-2">
                                <span>Rayon {{ $detailRayon->nama }}</span>
                                <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 font-semibold border border-emerald-200 dark:border-emerald-800">
                                    {{ $detailStats['total'] }} Siswa Terdaftar
                                </span>
                            </h2>
                            <p class="text-xs text-text-secondary">Rincian data pembimbing, statistik anggota, dan daftar siswa terdaftar.</p>
                        </div>
                    </div>
                    <button wire:click="closeDetail" class="text-text-muted hover:text-text-main text-2xl leading-none cursor-pointer">&times;</button>
                </div>

                <!-- Konten Modal (Scrollable) -->
                <div class="p-6 overflow-y-auto space-y-6">
                    <!-- Kartu Ringkasan Statistik -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="p-3.5 rounded-xl border border-border bg-surface-hover/30">
                            <span class="text-[11px] font-semibold text-text-secondary uppercase tracking-wider block">Total Siswa</span>
                            <span class="text-xl font-black text-text-main mt-1 block">{{ $detailStats['total'] }}</span>
                            <span class="text-[10px] text-text-muted">terdaftar aktif</span>
                        </div>
                        <div class="p-3.5 rounded-xl border border-border bg-surface-hover/30">
                            <span class="text-[11px] font-semibold text-text-secondary uppercase tracking-wider block">Laki-laki</span>
                            <span class="text-xl font-black text-blue-600 dark:text-blue-400 mt-1 block">{{ $detailStats['laki'] }}</span>
                            <span class="text-[10px] text-text-muted">siswa putra</span>
                        </div>
                        <div class="p-3.5 rounded-xl border border-border bg-surface-hover/30">
                            <span class="text-[11px] font-semibold text-text-secondary uppercase tracking-wider block">Perempuan</span>
                            <span class="text-xl font-black text-rose-600 dark:text-rose-400 mt-1 block">{{ $detailStats['perempuan'] }}</span>
                            <span class="text-[10px] text-text-muted">siswa putri</span>
                        </div>
                        <div class="p-3.5 rounded-xl border border-border bg-surface-hover/30">
                            <span class="text-[11px] font-semibold text-text-secondary uppercase tracking-wider block">Rombel Tersebar</span>
                            <span class="text-xl font-black text-amber-600 dark:text-amber-400 mt-1 block">{{ count($detailStats['rombels']) }}</span>
                            <span class="text-[10px] text-text-muted">kelas berbeda</span>
                        </div>
                    </div>

                    <!-- Informasi Pembimbing & Wilayah -->
                    <div class="p-4 rounded-xl border border-border bg-surface-hover/20 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="font-semibold text-text-secondary block">Guru Pembimbing Rayon:</span>
                                @if($detailRayon->pembimbing)
                                    <div class="font-bold text-text-main text-sm mt-0.5">{{ $detailRayon->pembimbing->name }}</div>
                                    <div class="text-text-muted">{{ $detailRayon->pembimbing->nip ?? 'NIP: -' }} &bull; {{ $detailRayon->pembimbing->email ?? $detailRayon->pembimbing->username }}</div>
                                @else
                                    <span class="italic text-amber-600 dark:text-amber-400 mt-0.5 block">Belum Ditentukan</span>
                                @endif
                            </div>
                            <div>
                                <span class="font-semibold text-text-secondary block">Wilayah Cakupan:</span>
                                <span class="font-medium text-text-main mt-0.5 block">{{ $detailRayon->wilayah_cakupan ?: 'Tidak ada keterangan wilayah cakupan' }}</span>
                            </div>
                        </div>
                        @if($detailRayon->keterangan)
                            <div class="border-t border-border pt-2 text-xs">
                                <span class="font-semibold text-text-secondary">Keterangan:</span>
                                <span class="text-text-main ml-1">{{ $detailRayon->keterangan }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Distribusi Kelas / Rombel -->
                    @if(count($detailStats['rombels']) > 0)
                        <div>
                            <span class="text-xs font-semibold text-text-secondary block mb-2">Sebaran Rombel Siswa:</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($detailStats['rombels'] as $rb)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium border border-border bg-surface text-text-main">
                                        <span class="font-semibold text-primary">{{ $rb['nama'] }}</span>
                                        <span class="px-1.5 py-0.2 rounded-full bg-surface-hover text-text-secondary text-[11px] font-bold">{{ $rb['count'] }}</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Tabel Siswa Terdaftar -->
                    <div class="space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <h3 class="text-sm font-bold text-text-main flex items-center gap-2">
                                <span>Daftar Siswa Rayon</span>
                                <span class="px-2 py-0.5 rounded-full text-xs bg-primary-soft text-primary font-bold">
                                    {{ $detailSiswas->count() }}
                                </span>
                            </h3>

                            <div class="flex flex-wrap items-center gap-2">
                                <!-- Filter Rombel -->
                                <select wire:model.live="filterDetailRombel" class="text-xs rounded-lg border-border bg-surface text-text-main py-1.5 px-2.5">
                                    <option value="">Semua Rombel</option>
                                    @foreach($detailStats['rombels'] as $rb)
                                        @if($rb['id'])
                                            <option value="{{ $rb['id'] }}">{{ $rb['nama'] }} ({{ $rb['count'] }})</option>
                                        @endif
                                    @endforeach
                                </select>

                                <!-- Input Cari Siswa -->
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="searchDetailSiswa"
                                    placeholder="Cari nama / NIS..."
                                    class="text-xs rounded-lg border-border bg-surface text-text-main py-1.5 px-3 w-48 sm:w-56"
                                >
                            </div>
                        </div>

                        <div class="border border-border rounded-xl overflow-hidden">
                            <div class="max-h-72 overflow-y-auto">
                                <table class="min-w-full divide-y divide-border text-xs">
                                    <thead class="bg-surface-hover/70 text-left sticky top-0 z-10">
                                        <tr>
                                            <th class="px-3.5 py-2.5 font-semibold text-text-secondary text-center w-10">No</th>
                                            <th class="px-3.5 py-2.5 font-semibold text-text-secondary">NIS / NISN</th>
                                            <th class="px-3.5 py-2.5 font-semibold text-text-secondary">Nama Lengkap Siswa</th>
                                            <th class="px-3.5 py-2.5 font-semibold text-text-secondary text-center">JK</th>
                                            <th class="px-3.5 py-2.5 font-semibold text-text-secondary">Rombel / Kelas</th>
                                            <th class="px-3.5 py-2.5 font-semibold text-text-secondary text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-border text-text-main">
                                        @forelse($detailSiswas as $idx => $s)
                                            <tr class="hover:bg-surface-hover/50 transition-colors">
                                                <td class="px-3.5 py-2 text-center text-text-muted">{{ $idx + 1 }}</td>
                                                <td class="px-3.5 py-2 font-mono text-text-secondary">
                                                    {{ $s->nipd ?? $s->nisn ?? '-' }}
                                                </td>
                                                <td class="px-3.5 py-2 font-medium text-text-main">
                                                    {{ $s->nama }}
                                                </td>
                                                <td class="px-3.5 py-2 text-center">
                                                    @if($s->jk === 'L')
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400">L</span>
                                                    @elseif($s->jk === 'P')
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400">P</span>
                                                    @else
                                                        <span class="text-text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-3.5 py-2">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-surface-hover text-text-main border border-border">
                                                        {{ $s->rombel?->nama ?? 'Tanpa Rombel' }}
                                                    </span>
                                                </td>
                                                <td class="px-3.5 py-2 text-center">
                                                    @if($s->is_active ?? true)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                                            Aktif
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                                            Nonaktif
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-4 py-8 text-center text-text-muted">
                                                    @if($searchDetailSiswa || $filterDetailRombel)
                                                        Tidak ada siswa yang sesuai kriteria pencarian/filter di rayon ini.
                                                    @else
                                                        Belum ada siswa yang terdaftar di rayon ini.
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="px-6 py-4 border-t border-border flex items-center justify-between bg-surface-hover/30">
                    <span class="text-xs text-text-muted">ID Rayon: #{{ $detailRayon->id }}</span>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="closeDetail" class="btn btn-secondary text-xs">
                            Tutup
                        </button>
                        @can('akademik.rayon.edit')
                        <button type="button" wire:click="edit({{ $detailRayon->id }})" class="btn btn-primary text-xs inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Edit Rayon Ini</span>
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
