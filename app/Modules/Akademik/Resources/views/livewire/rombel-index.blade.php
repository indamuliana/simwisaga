<div class="space-y-6">
 <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
 <div>
 <h1 class="text-2xl font-bold tracking-tight text-text-main">Rombongan Belajar (Rombel)</h1>
 <p class="text-sm text-text-secondary">Pengelolaan kelas belajar format standar <code class="text-primary dark:text-indigo-400 font-semibold">[Tingkat] [Jurusan] [Nomor]</code></p>
 </div>
 @can('akademik.rombel.tambah')
 <button wire:click="create" class="btn btn-primary">
 + Tambah Rombel
 </button>
 @endcan
 </div>

 @if (session('status'))
 <div class="p-4 text-sm text-success bg-success-soft rounded-lg border border-success">
 {{ session('status') }}
 </div>
 @endif

 <!-- Filter Card -->
 <div class="p-4 card grid grid-cols-1 sm:grid-cols-3 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Tahun Ajaran</label>
 <select wire:model.live="filterTahunAjaran" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="">-- Semua Tahun Ajaran --</option>
 @foreach($tahunAjarans as $ta)
 <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Jurusan</label>
 <select wire:model.live="filterJurusan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="">-- Semua Jurusan --</option>
 @foreach($jurusans as $j)
 <option value="{{ $j->id }}">{{ $j->kode }} - {{ $j->nama }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Tingkat</label>
 <select wire:model.live="filterTingkat" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="">-- Semua Tingkat --</option>
 <option value="X">Kelas X</option>
 <option value="XI">Kelas XI</option>
 <option value="XII">Kelas XII</option>
 </select>
 </div>
 </div>

 <!-- Table Card -->
 <div class="card overflow-hidden">
 <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-sm">
 <thead class="bg-surface-hover bg-surface/50 text-text-secondary font-medium">
 <tr>
 <x-table.sortable-th field="nama" :sortField="$sortField" :sortDirection="$sortDirection" class="px-6 py-3">Nama Rombel</x-table.sortable-th>
 <x-table.sortable-th field="tingkat" :sortField="$sortField" :sortDirection="$sortDirection" class="px-6 py-3">Tingkat & Jurusan</x-table.sortable-th>
 <x-table.sortable-th field="tahun_ajaran_id" :sortField="$sortField" :sortDirection="$sortDirection" class="px-6 py-3">Tahun Ajaran</x-table.sortable-th>
 <x-table.sortable-th field="wali_kelas_id" :sortField="$sortField" :sortDirection="$sortDirection" class="px-6 py-3">Wali Kelas</x-table.sortable-th>
 <x-table.sortable-th field="kapasitas" :sortField="$sortField" :sortDirection="$sortDirection" class="px-6 py-3">Jumlah Murid</x-table.sortable-th>
 <th class="px-6 py-3 text-right">Aksi</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-text-main">
 @forelse($rombels as $r)
 <tr class="hover:bg-surface-hover/50 dark:hover:bg-slate-800/30 transition">
 <td class="px-6 py-4 font-bold text-text-main">
 <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
 {{ $r->nama }}
 </span>
 </td>
 <td class="px-6 py-4">
 <span class="font-medium text-text-main">Kelas {{ $r->tingkat }}</span>
 <span class="text-xs text-text-secondary block">{{ $r->jurusan?->nama }} ({{ $r->jurusan?->kode }})</span>
 </td>
 <td class="px-6 py-4 text-text-secondary">
 {{ $r->tahunAjaran?->nama }}
 </td>
 <td class="px-6 py-4">
 @if($r->waliKelas)
 <div class="font-medium text-text-main">{{ $r->waliKelas->name }}</div>
 <div class="text-xs text-slate-400">{{ $r->waliKelas->nip ?? $r->waliKelas->username }}</div>
 @else
 <span class="text-xs italic text-amber-600 dark:text-amber-400">Belum Ditentukan</span>
 @endif
 </td>
 <td class="px-6 py-4 text-text-secondary font-medium">
 {{ $r->siswas_count }} siswa
 </td>
 <td class="px-6 py-4 text-right space-x-2">
 @can('akademik.rombel.edit')
 <button wire:click="edit({{ $r->id }})" class="text-xs font-medium text-primary hover:text-indigo-800 dark:text-indigo-400">Edit</button>
 @endcan
 @can('akademik.rombel.hapus')
 <button wire:click="delete({{ $r->id }})" wire:confirm="Hapus rombel {{ $r->nama }}?" class="text-xs font-medium text-rose-600 hover:text-rose-800 dark:text-rose-400">Hapus</button>
 @endcan
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada data rombel untuk filter ini.</td>
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
 {{ $rombelId ? 'Edit Rombel' : 'Tambah Rombel Baru' }}
 </h2>
 <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-text-secondary dark:hover:text-slate-200">&times;</button>
 </div>
 <form wire:submit="save" class="p-6 space-y-4">
 <div class="grid grid-cols-2 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Tahun Ajaran</label>
 <select wire:model.live="tahun_ajaran_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @foreach($tahunAjarans as $ta)
 <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
 @endforeach
 </select>
 @error('tahun_ajaran_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Tingkat</label>
 <select wire:model.live="tingkat" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="X">Kelas X</option>
 <option value="XI">Kelas XI</option>
 <option value="XII">Kelas XII</option>
 </select>
 @error('tingkat') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 </div>

 <div class="grid grid-cols-3 gap-4">
 <div class="col-span-2">
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Jurusan</label>
 <select wire:model.live="jurusan_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @foreach($jurusans as $j)
 <option value="{{ $j->id }}">{{ $j->kode }} - {{ $j->nama }}</option>
 @endforeach
 </select>
 @error('jurusan_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Nomor Rombel</label>
 <input type="number" min="1" max="20" wire:model.live="nomor_urut" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @error('nomor_urut') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 </div>

 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Nama Resmi Rombel (Auto-Generate)</label>
 <input type="text" wire:model="nama" readonly class="w-full text-sm font-bold bg-surface-hover bg-surface/80 rounded-lg border-border text-indigo-700 dark:text-indigo-400">
 <span class="text-[11px] text-slate-400">Otomatis berformat: [Tingkat] [Jurusan] [Nomor]</span>
 @error('nama') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
 </div>

 <div class="grid grid-cols-2 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Wali Kelas</label>
 <select wire:model="wali_kelas_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="">-- Belum Ditentukan --</option>
 @foreach($gurus as $g)
 <option value="{{ $g->id }}">{{ $g->name }}</option>
 @endforeach
 </select>
 @error('wali_kelas_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Kapasitas Maksimal</label>
 <input type="number" min="1" max="60" wire:model="kapasitas" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @error('kapasitas') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 </div>

 <div class="pt-4 flex justify-end space-x-3 border-t border-border">
 <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-sm text-text-secondary hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg">Batal</button>
 <button type="submit" class="btn btn-primary">Simpan Rombel</button>
 </div>
 </form>
 </div>
 </div>
 @endif
</div>
