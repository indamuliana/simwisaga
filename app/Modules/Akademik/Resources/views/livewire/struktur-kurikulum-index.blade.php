<div class="space-y-6">
 <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
 <div>
 <h1 class="text-2xl font-bold tracking-tight text-text-main">Struktur Kurikulum</h1>
 <p class="text-sm text-text-secondary">Pemetaan alokasi mata pelajaran dan beban Jam Pelajaran (JP) per jurusan dan tingkat</p>
 </div>
 @can('akademik.kurikulum.tambah')
 <button wire:click="create" class="btn btn-primary">
 + Tambah Mapel ke Kurikulum
 </button>
 @endcan
 </div>

 @if (session('status'))
 <div class="p-4 text-sm text-success bg-success-soft rounded-lg border border-success">
 {{ session('status') }}
 </div>
 @endif

 <!-- Selector Bar -->
 <div class="p-4 card grid grid-cols-1 sm:grid-cols-3 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Tahun Ajaran</label>
 <select wire:model.live="filterTahunAjaran" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @foreach($tahunAjarans as $ta)
 <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Jurusan / Keahlian</label>
 <select wire:model.live="filterJurusan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @foreach($jurusans as $j)
 <option value="{{ $j->id }}">{{ $j->kode }} - {{ $j->nama }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Tingkat Kelas</label>
 <select wire:model.live="filterTingkat" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="X">Kelas X</option>
 <option value="XI">Kelas XI</option>
 <option value="XII">Kelas XII</option>
 </select>
 </div>
 </div>

 <!-- Table Card -->
 <div class="card overflow-hidden">
 <div class="px-6 py-4 bg-surface-hover/50 bg-surface/40 border-b border-border flex justify-between items-center">
 <span class="text-sm font-bold text-text-main">
 Daftar Mata Pelajaran & Jam Pelajaran
 </span>
 <span class="text-sm font-semibold text-primary dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 px-3 py-1 rounded-full border border-indigo-200 dark:border-indigo-800">
 Total Alokasi: {{ $totalJp }} JP / Minggu
 </span>
 </div>
 <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-sm">
 <thead class="bg-surface-hover bg-surface/50 text-text-secondary font-medium">
 <tr>
 <th class="px-6 py-3 w-16">No</th>
 <th class="px-6 py-3">Kode</th>
 <th class="px-6 py-3">Mata Pelajaran</th>
 <th class="px-6 py-3">Kelompok</th>
 <th class="px-6 py-3 text-center">Beban (JP/Minggu)</th>
 <th class="px-6 py-3 text-right">Aksi</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-text-main">
 @forelse($items as $idx => $it)
 <tr class="hover:bg-surface-hover/50 dark:hover:bg-slate-800/30 transition">
 <td class="px-6 py-4 text-slate-400 text-xs font-mono">{{ $it->urutan }}</td>
 <td class="px-6 py-4 font-mono font-semibold text-primary dark:text-indigo-400">{{ $it->mapel?->kode }}</td>
 <td class="px-6 py-4 font-medium text-text-main">{{ $it->mapel?->nama }}</td>
 <td class="px-6 py-4">
 <span class="text-xs text-text-secondary">
 {{ $it->mapel?->kelompok_label }}
 </span>
 </td>
 <td class="px-6 py-4 text-center font-bold text-text-main">
 {{ $it->jp_per_minggu }} JP
 </td>
 <td class="px-6 py-4 text-right space-x-2">
 @can('akademik.kurikulum.edit')
 <button wire:click="edit({{ $it->id }})" class="text-xs font-medium text-primary hover:text-indigo-800 dark:text-indigo-400">Edit</button>
 @endcan
 @can('akademik.kurikulum.hapus')
 <button wire:click="delete({{ $it->id }})" wire:confirm="Hapus mapel ini dari struktur kurikulum?" class="text-xs font-medium text-rose-600 hover:text-rose-800 dark:text-rose-400">Hapus</button>
 @endcan
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada mata pelajaran yang dimasukkan untuk kurikulum ini.</td>
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
 {{ $kurikulumId ? 'Edit Alokasi Kurikulum' : 'Tambah Mapel ke Kurikulum' }}
 </h2>
 <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-text-secondary dark:hover:text-slate-200">&times;</button>
 </div>
 <form wire:submit="save" class="p-6 space-y-4">
 <div class="grid grid-cols-3 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Tahun Ajaran</label>
 <select wire:model="tahun_ajaran_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @foreach($tahunAjarans as $ta)
 <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Jurusan</label>
 <select wire:model="jurusan_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @foreach($jurusans as $j)
 <option value="{{ $j->id }}">{{ $j->kode }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Tingkat</label>
 <select wire:model="tingkat" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="X">Kelas X</option>
 <option value="XI">Kelas XI</option>
 <option value="XII">Kelas XII</option>
 </select>
 </div>
 </div>

 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Pilih Mata Pelajaran</label>
 <select wire:model="mapel_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @foreach($allMapels as $m)
 <option value="{{ $m->id }}">[{{ $m->kode }}] {{ $m->nama }} ({{ $m->kelompok_label }})</option>
 @endforeach
 </select>
 @error('mapel_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>

 <div class="grid grid-cols-2 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Beban JP per Minggu</label>
 <input type="number" min="1" max="30" wire:model="jp_per_minggu" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @error('jp_per_minggu') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Nomor Urut</label>
 <input type="number" min="1" wire:model="urutan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @error('urutan') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 </div>

 <div class="pt-4 flex justify-end space-x-3 border-t border-border">
 <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-sm text-text-secondary hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg">Batal</button>
 <button type="submit" class="btn btn-primary">Simpan Kurikulum</button>
 </div>
 </form>
 </div>
 </div>
 @endif
</div>
