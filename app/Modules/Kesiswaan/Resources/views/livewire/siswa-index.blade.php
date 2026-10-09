<div class="space-y-6">
 <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
 <div>
 <h1 class="text-2xl font-bold tracking-tight text-text-main">Data Induk Siswa</h1>
 <p class="text-sm text-text-secondary">Database profil siswa lengkap 66 kolom format standar DAPODIK SMK Wikrama 1 Garut.</p>
 </div>
 <div class="flex flex-wrap items-center gap-2">
 <!-- Dropdown Download Excel -->
 <div class="relative" x-data="{ openExcel: false }" @click.outside="openExcel = false">
 <button type="button" @click="openExcel = !openExcel"
 class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-emerald-700 text-success bg-surface border border-emerald-300 dark:border-emerald-700/80 rounded-md hover:bg-success-soft dark:hover:bg-emerald-950/40 shadow-xs transition">
 <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="currentColor">
 <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zM6 20V4h5v6h6v10H6zm2-8h2v2H8v-2zm0 3h2v2H8v-2zm3-3h5v2h-5v-2zm0 3h5v2h-5v-2z"/>
 </svg>
 <span>Download Excel</span>
 <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': openExcel }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
 </svg>
 </button>

 <div x-show="openExcel" x-cloak x-transition
 class="absolute right-0 mt-2 w-72 rounded-xl bg-surface shadow-xl ring-1 ring-black/5 dark:ring-slate-700 z-50 py-1.5 divide-y divide-slate-100 dark:divide-slate-700">
 <div class="px-3 py-2 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
 Pilih Format Ekspor Excel
 </div>
 <div class="py-1">
 <button type="button" wire:click="exportExcel('ringkasan')" @click="openExcel = false"
 class="flex items-start gap-3 w-full px-4 py-2.5 text-left text-text-main hover:bg-success-soft/70 hover:bg-surface-hover transition group">
 <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 group-hover:scale-105 transition-transform">
 <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.5L19 7.5V19a2 2 0 01-2 2z"/>
 </svg>
 </span>
 <div>
 <div class="font-semibold text-sm text-text-main group-hover:text-emerald-700 dark:group-hover:text-emerald-400">Download Ringkasan</div>
 <div class="text-[11px] text-text-secondary">Data pokok (NIS, NISN, Rombel, Rayon, Kontak HP, Ortu, Alamat)</div>
 </div>
 </button>
 <button type="button" wire:click="exportExcel('lengkap')" @click="openExcel = false"
 class="flex items-start gap-3 w-full px-4 py-2.5 text-left text-text-main hover:bg-blue-50/70 hover:bg-surface-hover transition group">
 <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-950/60 text-primary dark:text-blue-400 group-hover:scale-105 transition-transform">
 <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
 </svg>
 </span>
 <div>
 <div class="font-semibold text-sm text-text-main group-hover:text-blue-700 dark:group-hover:text-blue-400">Download Lengkap</div>
 <div class="text-[11px] text-text-secondary">Format DAPODIK standar 66 kolom profil komplit</div>
 </div>
 </button>
 </div>
 </div>
 </div>

 @can('kesiswaan.siswa.cetak')
 <a href="{{ route('kesiswaan.cetak-absensi') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200 dark:border-indigo-800 rounded-md hover:bg-indigo-100 dark:hover:bg-indigo-900/60 shadow-xs transition" title="Cetak Lembar Presensi / Daftar Hadir">
 <svg class="w-4 h-4 text-primary dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
 </svg>
 <span>Cetak Absensi</span>
 </a>
 <button wire:click="cetakDaftar" class="btn btn-secondary">
 <span>&#128424;</span> Cetak PDF
 </button>
 @endcan
 @can('kesiswaan.siswa.tambah')
 <a href="{{ route('kesiswaan.import') }}" class="btn btn-primary">
 + Import File DAPODIK
 </a>
 @endcan
 </div>
 </div>

 <div wire:loading wire:target="exportExcel" class="p-3.5 bg-success-soft dark:bg-emerald-950/40 border border-success rounded-xl text-success text-xs flex items-center gap-2.5 shadow-xs">
 <svg class="animate-spin h-4 w-4 text-emerald-600 dark:text-emerald-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
 </svg>
 <span>Sedang menyiapkan dan memformat berkas Excel... Unduhan akan dimulai secara otomatis sesaat lagi.</span>
 </div>

 @if (session('status'))
 <div class="p-4 text-sm text-success bg-success-soft rounded-md border border-success">
 {{ session('status') }}
 </div>
 @endif

 <!-- Filters & Search Card -->
 <div class="p-4 card grid grid-cols-1 sm:grid-cols-5 gap-3">
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Cari Siswa</label>
 <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nama, NIS, NISN..." class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Program</label>
 <select wire:model.live="filterProgram" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 <option value="">-- Semua Program --</option>
 @foreach($programs as $pr)
 <option value="{{ $pr->id }}">{{ $pr->nama }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Rombel</label>
 <select wire:model.live="filterRombel" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 <option value="">-- Semua Rombel --</option>
 @foreach($rombels as $r)
 <option value="{{ $r->id }}">{{ $r->nama }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Rayon</label>
 <select wire:model.live="filterRayon" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 <option value="">-- Semua Rayon --</option>
 @foreach($rayons as $ry)
 <option value="{{ $ry->id }}">{{ $ry->nama }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Status</label>
 <select wire:model.live="filterStatus" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 <option value="">-- Semua Status --</option>
 <option value="aktif">Aktif</option>
 <option value="lulus">Lulus</option>
 <option value="pindah">Pindah</option>
 <option value="dikeluarkan">Dikeluarkan</option>
 <option value="cuti">Cuti</option>
 </select>
 </div>
 </div>

 <!-- Table Card: Urutan Header Resmi No, NIS, Nama, JK, Rombel, Rayon, Program -->
 <div class="card overflow-hidden">
 <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-sm">
 <thead class="bg-surface-hover bg-surface/50 text-text-secondary font-medium">
 <tr>
 <x-table.sortable-th field="id" :sortField="$sortField" :sortDirection="$sortDirection" class="px-4 py-3 w-12 text-center">No</x-table.sortable-th>
 <x-table.sortable-th field="nisn" :sortField="$sortField" :sortDirection="$sortDirection" class="px-5 py-3">NIS</x-table.sortable-th>
 <x-table.sortable-th field="nama" :sortField="$sortField" :sortDirection="$sortDirection" class="px-6 py-3">Nama</x-table.sortable-th>
 <x-table.sortable-th field="jk" :sortField="$sortField" :sortDirection="$sortDirection" class="px-4 py-3 text-center">JK</x-table.sortable-th>
 <x-table.sortable-th field="rombel_id" :sortField="$sortField" :sortDirection="$sortDirection" class="px-5 py-3">Rombel</x-table.sortable-th>
 <x-table.sortable-th field="rayon_id" :sortField="$sortField" :sortDirection="$sortDirection" class="px-5 py-3">Rayon</x-table.sortable-th>
 <x-table.sortable-th field="program_id" :sortField="$sortField" :sortDirection="$sortDirection" class="px-5 py-3 text-center">Program</x-table.sortable-th>
 <th class="px-6 py-3 text-right">Aksi</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-text-main">
 @forelse($siswas as $idx => $s)
 <tr class="{{ $s->is_active ? 'hover:bg-surface-hover/50 dark:hover:bg-slate-800/30' : 'bg-pink-50 hover:bg-pink-100 dark:bg-pink-950/40 dark:hover:bg-pink-900/50' }} transition">
 <td class="px-4 py-4 text-center text-xs text-slate-400 font-mono">
 {{ $siswas->firstItem() + $idx }}
 </td>
 <td class="px-5 py-4 font-mono font-bold text-text-main text-xs">
 {{ $s->nipd ?: $s->nisn }}
 </td>
 <td class="px-6 py-4">
 <div>
 <div class="font-bold text-text-main">{{ $s->nama }}</div>
 <div class="text-[11px] text-slate-400 font-mono">NISN: {{ \App\Core\Support\DataMasker::maskNisn($s->nisn) }}</div>
 </div>
 </td>
 <td class="px-4 py-4 text-center">
 <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $s->jk === 'L' ? 'text-blue-700 bg-blue-50 dark:bg-blue-950/60 dark:text-blue-300' : 'text-pink-700 bg-pink-50 dark:bg-pink-950/60 dark:text-pink-300' }}">
 {{ $s->jk }}
 </span>
 </td>
 <td class="px-5 py-4">
 @if($s->rombel)
 <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
 {{ $s->rombel->nama }}
 </span>
 @else
 <span class="text-xs italic text-amber-600 dark:text-amber-400">Belum Ada</span>
 @endif
 </td>
 <td class="px-5 py-4">
 @if($s->rayon)
 <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-success-soft text-emerald-700 dark:bg-emerald-950/60 text-success border border-success">
 {{ $s->rayon->nama }}
 </span>
 @else
 <span class="text-xs italic text-rose-500">Belum di-plot</span>
 @endif
 </td>
 <td class="px-5 py-4 text-center">
 @php
 $cIdx = $s->program ? ($s->program->id % 6) : -1;
 $cClass = match($cIdx) {
 0 => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800',
 1 => 'bg-success-soft text-emerald-700 border-success dark:bg-emerald-950/60 text-success border-success',
 2 => 'bg-amber-50 text-amber-800 border-amber-300 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
 3 => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-800',
 4 => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800',
 5 => 'bg-cyan-50 text-cyan-700 border-cyan-200 dark:bg-cyan-950/60 dark:text-cyan-300 dark:border-cyan-800',
 default => 'bg-surface-hover text-text-main border-border bg-surface text-text-secondary border-border',
 };
 @endphp
 <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold border {{ $cClass }}">
 {{ $s->program?->nama ?? 'Reguler' }}
 </span>
 </td>
 <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
 <!-- WA Dropdown -->
 <div x-data="{ open: false }" class="relative inline-block text-left">
 <button @click="open = !open" @click.away="open = false" class="text-emerald-500 hover:text-emerald-700" title="Hubungi">
 <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
 </button>
 <div x-show="open" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-surface ring-1 ring-black ring-opacity-5 z-[50]" style="display: none;">
 <div class="py-1">
 @php
 $formatWa = function($no) {
 if (!$no) return null;
 $no = preg_replace('/[^0-9]/', '', $no);
 if (str_starts_with($no, '0')) $no = '62' . substr($no, 1);
 return $no;
 };
 $waSiswa = $formatWa($s->hp);
 $waAyah = $formatWa($s->ayah?->no_hp);
 $waIbu = $formatWa($s->ibu?->no_hp);
 $waWali = $formatWa($s->wali?->no_hp);
 @endphp
 @if($waSiswa) <a href="https://wa.me/{{ $waSiswa }}" target="_blank" class="block px-4 py-2 text-sm text-text-main hover:bg-slate-100 hover:bg-surface-hover text-left">Hubungi Siswa</a> @endif
 @if($waAyah) <a href="https://wa.me/{{ $waAyah }}" target="_blank" class="block px-4 py-2 text-sm text-text-main hover:bg-slate-100 hover:bg-surface-hover text-left">Hubungi Ayah</a> @endif
 @if($waIbu) <a href="https://wa.me/{{ $waIbu }}" target="_blank" class="block px-4 py-2 text-sm text-text-main hover:bg-slate-100 hover:bg-surface-hover text-left">Hubungi Ibu</a> @endif
 @if($waWali) <a href="https://wa.me/{{ $waWali }}" target="_blank" class="block px-4 py-2 text-sm text-text-main hover:bg-slate-100 hover:bg-surface-hover text-left">Hubungi Wali</a> @endif
 @if(!$waSiswa && !$waAyah && !$waIbu && !$waWali)
 <span class="block px-4 py-2 text-sm text-slate-400 italic text-left">Tidak ada kontak</span>
 @endif
 </div>
 </div>
 </div>

 <button wire:click="showDetail({{ $s->id }})" class="text-blue-500 hover:text-blue-700" title="Profil">
 <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
 </button>
 @can('kesiswaan.siswa.edit')
 <a href="{{ route('kesiswaan.siswa.edit', $s->id) }}" class="text-indigo-500 hover:text-indigo-700 inline-flex" title="Edit Lengkap">
 <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
 </a>
 @endcan
 @can('kesiswaan.siswa.hapus')
 <button x-data @click="$dispatch('open-delete-modal', { id: {{ $s->id }}, name: '{{ addslashes($s->nama) }}' })" class="text-rose-500 hover:text-rose-700" title="Hapus">
 <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
 </button>
 @endcan
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="8" class="px-6 py-8 text-center text-slate-400">Belum ada data siswa yang sesuai dengan filter.</td>
 </tr>
 @endforelse
 </tbody>
 </table>

 <div class="px-6 py-4 border-t border-border">
 {{ $siswas->links() }}
 </div>
 </div>

 <!-- Modal Detail Profil Siswa -->
 @if($showDetailModal && $selectedSiswa)
 <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
 <div class="w-full max-w-3xl bg-surface rounded-2xl shadow-xl border border-border overflow-hidden my-8">
 <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-surface-hover/50 bg-surface/40">
 <div class="flex items-center gap-3">
 @if($selectedSiswa->foto_path && file_exists(public_path('storage/' . $selectedSiswa->foto_path)))
 <img src="{{ asset('storage/' . $selectedSiswa->foto_path) }}" class="w-12 h-12 rounded-full object-cover border border-border">
 @endif
 <div>
 <h2 class="text-lg font-bold text-text-main">{{ $selectedSiswa->nama }}</h2>
 <p class="text-xs text-text-secondary font-mono">NIS: {{ $selectedSiswa->nipd ?? $selectedSiswa->nisn }} | NISN: {{ \App\Core\Support\DataMasker::maskNisn($selectedSiswa->nisn) }}</p>
 </div>
 </div>
 <button wire:click="$set('showDetailModal', false)" class="text-slate-400 hover:text-text-secondary dark:hover:text-slate-200 text-xl font-bold">&times;</button>
 </div>
 <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto text-sm">
 <!-- Tab Identitas & Rombel -->
 <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pb-4 border-b border-slate-100 border-border">
 <div>
 <span class="text-xs text-slate-400 block">Rombel:</span>
 <span class="font-bold text-primary dark:text-indigo-400">{{ $selectedSiswa->rombel?->nama ?? 'Belum ditentukan' }}</span>
 </div>
 <div>
 <span class="text-xs text-slate-400 block">Rayon:</span>
 <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $selectedSiswa->rayon?->nama ?? 'Belum di-plot' }}</span>
 </div>
 <div>
 <span class="text-xs text-slate-400 block">Program:</span>
 <span class="font-bold text-amber-600 dark:text-amber-400">{{ $selectedSiswa->program?->nama ?? 'Reguler' }}</span>
 </div>
 <div>
 <span class="text-xs text-slate-400 block">Status:</span>
 <span class="font-bold text-text-main uppercase">{{ $selectedSiswa->status }}</span>
 </div>
 </div>

 <!-- Domisili & Alamat -->
 <div class="space-y-2 pb-4 border-b border-slate-100 border-border">
 <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Alamat & Domisili</h4>
 <p class="text-text-main text-text-secondary">
 {{ $selectedSiswa->alamat ?? '-' }} 
 @if($selectedSiswa->rt || $selectedSiswa->rw) (RT {{ $selectedSiswa->rt }}/RW {{ $selectedSiswa->rw }}) @endif
 , Kel. {{ $selectedSiswa->kelurahan ?? '-' }}, Kec. {{ $selectedSiswa->kecamatan ?? '-' }}, Kode Pos: {{ $selectedSiswa->kode_pos ?? '-' }}
 </p>
 <div class="text-xs text-text-secondary">
 Transportasi: {{ $selectedSiswa->alat_transportasi ?? '-' }} | Jarak ke Sekolah: {{ $selectedSiswa->jarak_ke_sekolah_km ?? '-' }} KM
 </div>
 </div>

 <!-- Data Orang Tua -->
 <div class="space-y-3 pb-4 border-b border-slate-100 border-border">
 <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Data Orang Tua / Wali</h4>
 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
 <div class="p-3 bg-surface-hover bg-surface/40 rounded-md">
 <span class="text-xs font-bold text-primary dark:text-indigo-400 block">Data Ayah Kandung</span>
 <div class="font-semibold text-text-main">{{ $selectedSiswa->ayah?->nama ?? '-' }}</div>
 <div class="text-xs text-text-secondary mt-1">Pekerjaan: {{ $selectedSiswa->ayah?->pekerjaan ?? '-' }}</div>
 <div class="text-xs text-text-secondary">Penghasilan: {{ $selectedSiswa->ayah?->penghasilan ?? '-' }}</div>
 </div>
 <div class="p-3 bg-surface-hover bg-surface/40 rounded-md">
 <span class="text-xs font-bold text-pink-600 dark:text-pink-400 block">Data Ibu Kandung</span>
 <div class="font-semibold text-text-main">{{ $selectedSiswa->ibu?->nama ?? '-' }}</div>
 <div class="text-xs text-text-secondary mt-1">Pekerjaan: {{ $selectedSiswa->ibu?->pekerjaan ?? '-' }}</div>
 <div class="text-xs text-text-secondary">Penghasilan: {{ $selectedSiswa->ibu?->penghasilan ?? '-' }}</div>
 </div>
 </div>
 </div>

 <!-- Akun Login & Bantuan -->
 <div class="grid grid-cols-2 gap-4 text-xs">
 <div>
 <span class="text-slate-400 block mb-1 font-semibold">Username Login Siswa (NIS):</span>
 <span class="font-mono bg-slate-100 bg-surface px-2 py-1 rounded-md font-bold">{{ $selectedSiswa->nipd ?: $selectedSiswa->nisn }}</span>
 </div>
 <div>
 <span class="text-slate-400 block mb-1 font-semibold">Username Login Orang Tua:</span>
 <span class="font-mono bg-slate-100 bg-surface px-2 py-1 rounded-md font-bold">ortu_{{ $selectedSiswa->nipd ?: $selectedSiswa->nisn }}</span>
 </div>
 </div>
 </div>
 <div class="px-6 py-3 bg-surface-hover bg-surface/40 border-t border-border flex flex-wrap justify-between items-center gap-2">
 <div class="flex items-center gap-1.5">
 <span class="text-xs text-text-secondary font-medium mr-1">Download Biodata:</span>
 <button type="button" wire:click="downloadDetailSiswa({{ $selectedSiswa->id }}, 'pdf')" class="px-2.5 py-1 text-xs font-semibold rounded bg-rose-50 text-rose-700 hover:bg-rose-100 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800 transition">
 PDF
 </button>
 <button type="button" wire:click="downloadDetailSiswa({{ $selectedSiswa->id }}, 'word')" class="px-2.5 py-1 text-xs font-semibold rounded bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800 transition">
 Word (.doc)
 </button>
 <button type="button" wire:click="downloadDetailSiswa({{ $selectedSiswa->id }}, 'excel')" class="px-2.5 py-1 text-xs font-semibold rounded bg-success-soft text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950/60 text-success border border-success transition">
 Excel (.xlsx)
 </button>
 </div>
 <div class="flex items-center gap-3">
 @can('kesiswaan.siswa.edit')
 <a href="{{ route('kesiswaan.siswa.edit', $selectedSiswa->id) }}" class="text-xs font-semibold text-primary dark:text-indigo-400 hover:underline">
 Buka Form Edit 66 Kolom Lengkap &rarr;
 </a>
 @endcan
 <button wire:click="$set('showDetailModal', false)" class="px-4 py-2 text-sm text-text-secondary hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md">Tutup</button>
 </div>
 </div>
 </div>
 </div>
 @endif

 <!-- Delete Confirmation Modal (Alpine) -->
 <div x-data="{ open: false, id: null, name: '', typed: '' }" 
 @open-delete-modal.window="open = true; id = $event.detail.id; name = $event.detail.name; typed = ''"
 x-show="open" 
 class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" 
 style="display: none;">
 <div @click.away="open = false" class="bg-surface rounded-xl shadow-xl w-full max-w-md p-6">
 <h3 class="text-lg font-bold text-text-main mb-2">Konfirmasi Hapus</h3>
 <p class="text-sm text-text-secondary mb-4">
 Apakah Anda yakin ingin menghapus data siswa <strong x-text="name"></strong>? Tindakan ini tidak dapat dibatalkan.
 </p>
 <div class="mb-4">
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Ketik "HAPUS" untuk konfirmasi</label>
 <input type="text" x-model="typed" placeholder="HAPUS" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div class="flex justify-end gap-3">
 <button @click="open = false" class="px-4 py-2 text-sm text-text-secondary hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md">Batal</button>
 <button :disabled="typed !== 'HAPUS'" 
 @click="$wire.delete(id); open = false" 
 class="px-4 py-2 text-sm font-bold text-white bg-rose-600 rounded-md hover:bg-rose-700 disabled:opacity-50 disabled:cursor-not-allowed">
 Hapus Siswa
 </button>
 </div>
 </div>
 </div>
</div>
