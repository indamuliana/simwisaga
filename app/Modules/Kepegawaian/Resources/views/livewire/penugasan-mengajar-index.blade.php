<div class="space-y-6">
 <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
 <div>
 <h1 class="text-2xl font-bold tracking-tight text-text-main">Penugasan Mengajar Guru</h1>
 <p class="text-sm text-text-secondary">Distribusi mata pelajaran dan beban Jam Pelajaran (JP) mengajar per guru per rombel.</p>
 </div>
 <div class="flex items-center gap-2">
 @can('kepegawaian.penugasan.cetak')
 <button wire:click="cetakRekap" class="btn btn-secondary">
 <span>&#128424;</span> Cetak Rekap Beban JP
 </button>
 @endcan
 @can('kepegawaian.penugasan.tambah')
 <button wire:click="create" class="btn btn-primary">
 + Tambah Penugasan
 </button>
 @endcan
 </div>
 </div>

 @if (session('status'))
 <div class="p-4 text-sm text-success bg-success-soft rounded-lg border border-success">
 {{ session('status') }}
 </div>
 @endif

 <!-- Selector / Filter Bar -->
 <div class="p-4 card grid grid-cols-1 sm:grid-cols-3 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Periode Semester</label>
 <select wire:model.live="filterSemester" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="">-- Semua Semester --</option>
 @foreach($semesters as $s)
 <option value="{{ $s->id }}">{{ $s->tahunAjaran?->nama }} - Semester {{ ucfirst($s->semester) }} {{ $s->is_active ? '(Aktif)' : '' }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Filter Guru</label>
 <select wire:model.live="filterPegawai" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="">-- Semua Guru --</option>
 @foreach($gurus as $g)
 <option value="{{ $g->id }}">{{ $g->nama_gelar }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Filter Rombel</label>
 <select wire:model.live="filterRombel" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="">-- Semua Rombel --</option>
 @foreach($rombels as $r)
 <option value="{{ $r->id }}">{{ $r->nama }}</option>
 @endforeach
 </select>
 </div>
 </div>

 <!-- Table Card -->
 <div class="card overflow-hidden">
 <div class="px-6 py-4 bg-surface-hover/50 bg-surface/40 border-b border-border flex justify-between items-center">
 <span class="text-sm font-bold text-text-main">
 Daftar Alokasi Mengajar
 </span>
 <span class="text-sm font-semibold text-primary dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 px-3 py-1 rounded-full border border-indigo-200 dark:border-indigo-800">
 Total Alokasi: {{ $totalJp }} JP
 </span>
 </div>
 <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-sm">
 <thead class="bg-surface-hover bg-surface/50 text-text-secondary font-medium">
 <tr>
 <th class="px-6 py-3">Guru Pengampu</th>
 <th class="px-6 py-3">Mata Pelajaran</th>
 <th class="px-6 py-3">Rombel</th>
 <th class="px-6 py-3">Semester</th>
 <th class="px-6 py-3 text-center">Beban (JP)</th>
 <th class="px-6 py-3 text-right">Aksi</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-text-main">
 @forelse($penugasans as $p)
 <tr class="hover:bg-surface-hover/50 dark:hover:bg-slate-800/30 transition">
 <td class="px-6 py-4">
 <div class="font-bold text-text-main">{{ $p->pegawai?->nama_gelar }}</div>
 <div class="text-xs text-slate-400 font-mono">NIP: {{ $p->pegawai?->nip ?? '-' }}</div>
 </td>
 <td class="px-6 py-4">
 <div class="font-medium text-text-main">{{ $p->mapel?->nama }}</div>
 <div class="text-xs text-primary dark:text-indigo-400 font-mono">{{ $p->mapel?->kode }} ({{ $p->mapel?->kelompok_label }})</div>
 </td>
 <td class="px-6 py-4">
 <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
 {{ $p->rombel?->nama }}
 </span>
 </td>
 <td class="px-6 py-4 text-xs text-text-secondary">
 {{ $p->semester?->tahunAjaran?->nama }} - {{ ucfirst($p->semester?->semester ?? '') }}
 </td>
 <td class="px-6 py-4 text-center font-bold text-text-main">
 {{ $p->jp }} JP
 </td>
 <td class="px-6 py-4 text-right space-x-2">
 @can('kepegawaian.penugasan.edit')
 <button wire:click="edit({{ $p->id }})" class="text-xs font-medium text-primary hover:text-indigo-800 dark:text-indigo-400">Edit</button>
 @endcan
 @can('kepegawaian.penugasan.hapus')
 <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus penugasan mengajar ini?" class="text-xs font-medium text-rose-600 hover:text-rose-800 dark:text-rose-400">Hapus</button>
 @endcan
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada penugasan mengajar untuk filter ini.</td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>

 <!-- Modal Form -->
 @if($showModal)
 <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
 <div class="w-full max-w-lg bg-surface rounded-2xl shadow-xl border border-border overflow-hidden my-8">
 <div class="px-6 py-4 border-b border-border flex justify-between items-center">
 <h2 class="text-lg font-bold text-text-main">
 {{ $penugasanId ? 'Edit Penugasan Mengajar' : 'Tambah Penugasan Mengajar' }}
 </h2>
 <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-text-secondary dark:hover:text-slate-200">&times;</button>
 </div>
 <form wire:submit="save" class="p-6 space-y-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Periode Semester</label>
 <select wire:model.live="semester_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @foreach($semesters as $s)
 <option value="{{ $s->id }}">{{ $s->tahunAjaran?->nama }} - Semester {{ ucfirst($s->semester) }}</option>
 @endforeach
 </select>
 @error('semester_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>

 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Pilih Guru Pengampu</label>
 <select wire:model="pegawai_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @foreach($gurus as $g)
 <option value="{{ $g->id }}">{{ $g->nama_gelar }} (NIP: {{ $g->nip ?? '-' }})</option>
 @endforeach
 </select>
 @error('pegawai_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>

 <div class="grid grid-cols-2 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Rombel Kelas</label>
 <select wire:model.live="rombel_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @foreach($rombels as $r)
 <option value="{{ $r->id }}">{{ $r->nama }}</option>
 @endforeach
 </select>
 @error('rombel_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Beban Jam (JP / Minggu)</label>
 <input type="number" min="1" max="40" wire:model="jp" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @error('jp') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 </div>

 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Mata Pelajaran</label>
 <select wire:model.live="mapel_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @foreach($mapels as $m)
 <option value="{{ $m->id }}">[{{ $m->kode }}] {{ $m->nama }} ({{ $m->kelompok_label }})</option>
 @endforeach
 </select>
 @error('mapel_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>

 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Keterangan (Opsional)</label>
 <input type="text" wire:model="keterangan" placeholder="Misal: Teori / Praktik Lab" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 </div>

 <div class="pt-4 flex justify-end space-x-3 border-t border-border">
 <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-sm text-text-secondary hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg">Batal</button>
 <button type="submit" class="btn btn-primary">Simpan Penugasan</button>
 </div>
 </form>
 </div>
 </div>
 @endif
</div>
