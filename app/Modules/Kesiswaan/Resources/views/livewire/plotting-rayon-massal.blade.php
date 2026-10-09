<div class="space-y-6">
 <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
 <div>
 <h1 class="text-2xl font-bold tracking-tight text-text-main">Plotting Rayon Massal</h1>
 <p class="text-sm text-text-secondary">Alokasikan siswa ke Rayon wilayah tempat tinggal secara massal berdasarkan Kecamatan / Kelurahan.</p>
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

 <!-- Action Bar: Target Rayon Selection -->
 <div class="p-4 bg-indigo-50/60 bg-surface/80 rounded-xl border border-indigo-100 dark:border-indigo-900 flex flex-col sm:flex-row items-center justify-between gap-4">
 <div class="flex items-center gap-2">
 <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">
 Pilih Rayon Tujuan:
 </span>
 <select wire:model="targetRayonId" class="text-sm font-semibold rounded-lg border-indigo-300 dark:border-indigo-700 bg-surface text-text-main">
 <option value="">-- Pilih Rayon --</option>
 @foreach($rayons as $ry)
 <option value="{{ $ry->id }}">{{ $ry->nama }} ({{ $ry->wilayah_cakupan ?? 'Wilayah' }})</option>
 @endforeach
 </select>
 </div>
 <div class="flex items-center gap-3">
 <span class="text-xs text-text-secondary">
 Terpilih: <b>{{ count($selectedSiswaIds) }}</b> siswa
 </span>
 <button wire:click="applyPlotting" wire:confirm="Alokasikan {{ count($selectedSiswaIds) }} siswa terpilih ke Rayon tujuan?" class="btn btn-primary" @if(empty($selectedSiswaIds) || !$targetRayonId) disabled @endif>
 Terapkan Rayon ke Siswa Terpilih
 </button>
 </div>
 </div>

 <!-- Filters Card -->
 <div class="p-4 card grid grid-cols-1 sm:grid-cols-4 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Status Rayon</label>
 <select wire:model.live="filterStatusRayon" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="belum_ada">Belum Memiliki Rayon</option>
 <option value="semua">Semua Siswa</option>
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Filter Kecamatan</label>
 <select wire:model.live="filterKecamatan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="">-- Semua Kecamatan --</option>
 @foreach($kecamatans as $kc)
 <option value="{{ $kc }}">{{ $kc }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Filter Kelurahan</label>
 <select wire:model.live="filterKelurahan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main" @if(!$filterKecamatan) disabled @endif>
 <option value="">-- Semua Kelurahan --</option>
 @foreach($kelurahans as $kl)
 <option value="{{ $kl }}">{{ $kl }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Cari Nama / NISN</label>
 <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik nama atau NISN..." class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
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
 <th class="px-6 py-3">Nama Siswa</th>
 <th class="px-6 py-3">NISN</th>
 <th class="px-6 py-3">Rombel</th>
 <th class="px-6 py-3">Rayon Saat Ini</th>
 <th class="px-6 py-3">Kecamatan</th>
 <th class="px-6 py-3">Kelurahan / Desa</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-text-main">
 @forelse($siswas as $s)
 <tr class="hover:bg-surface-hover/50 dark:hover:bg-slate-800/30 transition">
 <td class="px-4 py-4 text-center">
 <input type="checkbox" value="{{ $s->id }}" wire:model.live="selectedSiswaIds" class="rounded border-border text-primary focus:ring-indigo-500">
 </td>
 <td class="px-6 py-4 font-bold text-text-main">
 {{ $s->nama }}
 </td>
 <td class="px-6 py-4 font-mono text-xs text-text-secondary">
 {{ $s->nisn }}
 </td>
 <td class="px-6 py-4">
 <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
 {{ $s->rombel?->nama ?? '-' }}
 </span>
 </td>
 <td class="px-6 py-4">
 @if($s->rayon)
 <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-success-soft text-emerald-700 dark:bg-emerald-950/60 text-success border border-success">
 {{ $s->rayon->nama }}
 </span>
 @else
 <span class="text-xs italic text-rose-500">Belum ada</span>
 @endif
 </td>
 <td class="px-6 py-4 text-xs font-semibold text-text-main text-text-secondary">
 {{ $s->kecamatan ?? '-' }}
 </td>
 <td class="px-6 py-4 text-xs text-text-secondary">
 {{ $s->kelurahan ?? '-' }}
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="7" class="px-6 py-8 text-center text-slate-400">Tidak ada siswa yang sesuai kriteria filter.</td>
 </tr>
 @endforelse
 </tbody>
 </table>

 <div class="px-6 py-4 border-t border-border">
 {{ $siswas->links() }}
 </div>
 </div>
</div>
