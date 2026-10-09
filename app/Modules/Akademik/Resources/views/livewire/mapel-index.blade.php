<div class="space-y-6">
 <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
 <div>
 <h1 class="text-2xl font-bold tracking-tight text-text-main">Mata Pelajaran (Mapel)</h1>
 <p class="text-sm text-text-secondary">Pengelompokan 4 kategori Kurikulum Merdeka (Muatan Umum, Kejuruan, Muatan Lokal, P5/PKL)</p>
 </div>
 @can('akademik.mapel.tambah')
 <button wire:click="create" class="btn btn-primary">
 + Tambah Mapel
 </button>
 @endcan
 </div>

 @if (session('status'))
 <div class="p-4 text-sm text-success bg-success-soft rounded-lg border border-success">
 {{ session('status') }}
 </div>
 @endif

 <!-- Search & Filter Card -->
 <div class="p-4 card flex flex-col sm:flex-row gap-4">
 <div class="flex-1">
 <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau kode mata pelajaran..." class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 </div>
 <div class="sm:w-72">
 <select wire:model.live="filterKelompok" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="">-- Semua Kelompok Kurikulum --</option>
 @foreach($daftarKelompok as $key => $lbl)
 <option value="{{ $key }}">{{ $lbl }}</option>
 @endforeach
 </select>
 </div>
 </div>

 <!-- Table Card -->
 <div class="card overflow-hidden">
 <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-sm">
 <thead class="bg-surface-hover bg-surface/50 text-text-secondary font-medium">
 <tr>
 <th class="px-6 py-3 w-16">No</th>
 <th class="px-6 py-3">Kode</th>
 <th class="px-6 py-3">Nama Mata Pelajaran</th>
 <th class="px-6 py-3">Kelompok</th>
 <th class="px-6 py-3">KKM Default</th>
 <th class="px-6 py-3">Status</th>
 <th class="px-6 py-3 text-right">Aksi</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-text-main">
 @forelse($mapels as $idx => $m)
 <tr class="hover:bg-surface-hover/50 dark:hover:bg-slate-800/30 transition">
 <td class="px-6 py-4 text-slate-400 text-xs font-mono">{{ $m->urutan }}</td>
 <td class="px-6 py-4 font-mono font-semibold text-primary dark:text-indigo-400">{{ $m->kode }}</td>
 <td class="px-6 py-4 font-medium text-text-main">{{ $m->nama }}</td>
 <td class="px-6 py-4">
 @php
 $badgeColor = match($m->kelompok) {
 'umum' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border-blue-200 dark:border-blue-800',
 'kejuruan' => 'bg-success-soft text-emerald-700 bg-success-soft text-success border-success',
 'muatan_lokal' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border-amber-200 dark:border-amber-800',
 default => 'bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 border-purple-200 dark:border-purple-800'
 };
 @endphp
 <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $badgeColor }}">
 {{ $m->kelompok_label }}
 </span>
 </td>
 <td class="px-6 py-4 font-semibold text-text-main text-text-secondary">{{ $m->kkm_default }}</td>
 <td class="px-6 py-4">
 @if($m->is_active)
 <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-success-soft text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">Aktif</span>
 @else
 <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-text-secondary bg-surface">Nonaktif</span>
 @endif
 </td>
 <td class="px-6 py-4 text-right space-x-2">
 @can('akademik.mapel.edit')
 <button wire:click="edit({{ $m->id }})" class="text-xs font-medium text-primary hover:text-indigo-800 dark:text-indigo-400">Edit</button>
 @endcan
 @can('akademik.mapel.hapus')
 <button wire:click="delete({{ $m->id }})" wire:confirm="Hapus mapel {{ $m->nama }}?" class="text-xs font-medium text-rose-600 hover:text-rose-800 dark:text-rose-400">Hapus</button>
 @endcan
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="7" class="px-6 py-8 text-center text-slate-400">Tidak ada data mata pelajaran yang sesuai.</td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>

 <!-- Modal Form -->
 @if($showModal)
 <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
 <div class="w-full max-w-lg bg-surface rounded-2xl shadow-xl border border-border overflow-hidden">
 <div class="px-6 py-4 border-b border-border flex justify-between items-center">
 <h2 class="text-lg font-bold text-text-main">
 {{ $mapelId ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' }}
 </h2>
 <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-text-secondary dark:hover:text-slate-200">&times;</button>
 </div>
 <form wire:submit="save" class="p-6 space-y-4">
 <div class="grid grid-cols-3 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Kode Mapel</label>
 <input type="text" wire:model="kode" placeholder="Misal: BINDO" class="w-full text-sm uppercase font-mono rounded-lg border-border bg-surface text-text-main">
 @error('kode') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 <div class="col-span-2">
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Nama Mata Pelajaran</label>
 <input type="text" wire:model="nama" placeholder="Misal: Bahasa Indonesia" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @error('nama') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 </div>

 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Kelompok Kurikulum</label>
 <select wire:model="kelompok" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @foreach($daftarKelompok as $key => $lbl)
 <option value="{{ $key }}">{{ $lbl }}</option>
 @endforeach
 </select>
 @error('kelompok') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>

 <div class="grid grid-cols-2 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">KKM Default</label>
 <input type="number" min="0" max="100" wire:model="kkm_default" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @error('kkm_default') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Nomor Urut Tampil</label>
 <input type="number" min="1" wire:model="urutan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @error('urutan') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 </div>

 <div class="flex items-center">
 <label class="inline-flex items-center gap-2 cursor-pointer">
 <input type="checkbox" wire:model="is_active" class="rounded border-border text-primary focus:ring-indigo-500">
 <span class="text-sm text-text-main text-text-secondary">Aktif Digunakan</span>
 </label>
 </div>

 <div class="pt-4 flex justify-end space-x-3 border-t border-border">
 <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-sm text-text-secondary hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg">Batal</button>
 <button type="submit" class="btn btn-primary">Simpan Mapel</button>
 </div>
 </form>
 </div>
 </div>
 @endif
</div>
