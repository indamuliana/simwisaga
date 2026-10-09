<div class="space-y-6">
 <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
 <div>
 <h1 class="text-2xl font-bold tracking-tight text-text-main">Plotting Program Siswa</h1>
 <p class="text-sm text-text-secondary">Pengelompokan siswa ke Program Unggulan atau Reguler secara massal.</p>
 </div>
 <a href="{{ route('kesiswaan.siswa') }}" class="inline-flex items-center text-sm font-semibold text-text-secondary hover:text-primary transition">
 &larr; Kembali ke Data Siswa
 </a>
 </div>

 @if (session('status'))
 <div class="p-4 text-sm text-success bg-success-soft rounded-lg border border-success">
 {{ session('status') }}
 </div>
 @endif

 @if (session('error'))
 <div class="p-4 text-sm text-rose-800 bg-rose-50 rounded-lg dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
 {{ session('error') }}
 </div>
 @endif

 <!-- Target Program Selection Bar -->
 <div class="p-4 bg-amber-50/60 bg-surface/80 rounded-xl border border-amber-200 dark:border-amber-900/60 flex flex-col sm:flex-row items-center justify-between gap-4">
 <div class="flex items-center gap-2">
 <span class="text-xs font-bold uppercase tracking-wider text-amber-800 dark:text-amber-300">
 Pilih Program Tujuan:
 </span>
 <select wire:model="targetProgramId" class="text-sm font-semibold rounded-lg border-amber-300 dark:border-amber-700 bg-surface text-text-main">
 <option value="">-- Pilih Program --</option>
 @foreach($programs as $pr)
 <option value="{{ $pr->id }}">{{ $pr->nama }} ({{ $pr->kode }})</option>
 @endforeach
 </select>
 </div>
 <div class="flex items-center gap-3">
 <span class="text-xs text-text-secondary">
 Terpilih: <b>{{ count($selectedSiswaIds) }}</b> siswa
 </span>
 <button wire:click="applyProgram" wire:confirm="Alokasikan {{ count($selectedSiswaIds) }} siswa terpilih ke program ini?" class="px-4 py-2 text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-sm transition disabled:opacity-50" @if(empty($selectedSiswaIds) || !$targetProgramId) disabled @endif>
 Terapkan Program ke Siswa Terpilih
 </button>
 </div>
 </div>

 <!-- Filters Card -->
 <div class="p-4 card grid grid-cols-1 sm:grid-cols-4 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Filter Program Saat Ini</label>
 <select wire:model.live="filterCurrentProgram" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="">-- Semua Program --</option>
 @foreach($programs as $pr)
 <option value="{{ $pr->id }}">{{ $pr->nama }}</option>
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
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Filter Rayon</label>
 <select wire:model.live="filterRayon" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="">-- Semua Rayon --</option>
 @foreach($rayons as $ry)
 <option value="{{ $ry->id }}">{{ $ry->nama }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Cari Nama / NIS</label>
 <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik nama atau NIS..." class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 </div>
 </div>

 <!-- Table Card -->
 <div class="card overflow-hidden">
 <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-sm">
 <thead class="bg-surface-hover bg-surface/50 text-text-secondary font-medium">
 <tr>
 <th class="px-4 py-3 w-10 text-center">
 <input type="checkbox" wire:model.live="selectAll" class="rounded border-border text-primary focus:ring-indigo-500">
 </th>
 <th class="px-6 py-3">NIS</th>
 <th class="px-6 py-3">Nama Siswa</th>
 <th class="px-6 py-3">Rombel</th>
 <th class="px-6 py-3">Rayon</th>
 <th class="px-6 py-3 text-center">Program Saat Ini</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-text-main">
 @forelse($siswas as $s)
 <tr class="hover:bg-surface-hover/50 dark:hover:bg-slate-800/30 transition">
 <td class="px-4 py-4 text-center">
 <input type="checkbox" value="{{ $s->id }}" wire:model.live="selectedSiswaIds" class="rounded border-border text-primary focus:ring-indigo-500">
 </td>
 <td class="px-6 py-4 font-mono font-bold text-xs text-text-main">
 {{ $s->nipd ?: $s->nisn }}
 </td>
 <td class="px-6 py-4 font-bold text-text-main">
 {{ $s->nama }}
 </td>
 <td class="px-6 py-4">
 <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
 {{ $s->rombel?->nama ?? '-' }}
 </span>
 </td>
 <td class="px-6 py-4">
 <span class="text-xs text-text-secondary font-medium">
 {{ $s->rayon?->nama ?? '-' }}
 </span>
 </td>
 <td class="px-6 py-4 text-center">
 @php
 $isUnggulan = ($s->program?->kode ?? '') === 'UNG';
 @endphp
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $isUnggulan ? 'bg-amber-50 text-amber-800 border-amber-300 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800' : 'bg-surface-hover text-text-main border-border bg-surface text-text-secondary border-border' }}">
 {{ $s->program?->nama ?? 'Reguler' }}
 </span>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="6" class="px-6 py-8 text-center text-slate-400">Tidak ada siswa yang sesuai filter.</td>
 </tr>
 @endforelse
 </tbody>
 </table>

 <div class="px-6 py-4 border-t border-border">
 {{ $siswas->links() }}
 </div>
 </div>
</div>
