<div class="space-y-6">
 <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
 <div>
 <h1 class="text-xl font-bold">Tahun Ajaran &amp; Semester</h1>
 <p class="text-sm text-text-secondary">Atur periode akademik aktif, tahun ajaran baru, dan periode kunci penginputan nilai/absensi.</p>
 </div>
 @can('akademik.tahunajaran.tambah')
 <button wire:click="create" class="btn btn-primary">
 + Tambah Tahun Ajaran
 </button>
 @endcan
 </div>

 @if (session('status'))
 <div class="rounded-lg border border-success bg-success-soft px-4 py-3 text-sm text-success">
 {{ session('status') }}
 </div>
 @endif

 <div class="space-y-4">
 @forelse ($tahunAjarans as $ta)
 <div class="card space-y-4 {{ $ta->is_active ? 'border-brand-500 ring-1 ring-brand-500/20' : '' }}">
 <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 pb-3 border-border">
 <div class="flex items-center gap-2">
 <h2 class="text-lg font-bold">T.A. {{ $ta->nama }}</h2>
 @if ($ta->is_active)
 <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-success dark:bg-green-900/40">
 Tahun Ajaran Berjalan
 </span>
 @endif
 </div>
 <div class="text-xs text-text-secondary">
 @if ($ta->tgl_mulai && $ta->tgl_selesai)
 Periode: {{ $ta->tgl_mulai->format('d M Y') }} s/d {{ $ta->tgl_selesai->format('d M Y') }}
 @endif
 </div>
 </div>

 {{-- Daftar Semester --}}
 <div class="grid gap-3 sm:grid-cols-2">
 @foreach ($ta->semesters as $sem)
 <div class="rounded-lg border p-4 transition {{ $sem->is_active ? 'border-brand-300 bg-brand-50/40 dark:border-brand-800 dark:bg-brand-950/20' : 'border-border bg-surface-hover/50 border-border bg-surface/40' }}">
 <div class="flex items-center justify-between">
 <span class="font-bold text-text-main">Semester {{ ucfirst($sem->semester) }}</span>
 @if ($sem->is_active)
 <span class="inline-flex items-center rounded-full bg-brand-600 px-2.5 py-0.5 text-xs font-bold text-white">
 Aktif Sekarang
 </span>
 @elseif (auth()->user()->can('akademik.tahunajaran.edit'))
 <button wire:click="setActiveSemester({{ $sem->id }})"
 wire:confirm="Aktifkan semester {{ $ta->nama }} {{ ucfirst($sem->semester) }} sebagai periode berjalan?"
 class="text-xs font-semibold text-brand-600 hover:underline dark:text-brand-400">
 Jadikan Aktif
 </button>
 @endif
 </div>

 <div class="mt-4 flex flex-wrap items-center justify-between gap-2 border-t border-gray-100 pt-3 text-xs border-border/60">
 <div class="flex items-center gap-1.5">
 <span class="text-text-secondary">Status Nilai:</span>
 @if ($sem->isLockedForNilai())
 <span class="font-semibold text-red-600 dark:text-red-400">&#128274; Terkunci</span>
 @else
 <span class="font-semibold text-green-600 dark:text-green-400">&#128275; Terbuka</span>
 @endif
 @can('akademik.tahunajaran.edit')
 <button wire:click="toggleLockNilai({{ $sem->id }})" class="ml-1 text-[11px] text-gray-400 hover:text-text-main underline">
 ({{ $sem->isLockedForNilai() ? 'Buka' : 'Kunci' }})
 </button>
 @endcan
 </div>

 <div class="flex items-center gap-1.5">
 <span class="text-text-secondary">Status Absensi:</span>
 @if ($sem->isLockedForAbsensi())
 <span class="font-semibold text-red-600 dark:text-red-400">&#128274; Terkunci</span>
 @else
 <span class="font-semibold text-green-600 dark:text-green-400">&#128275; Terbuka</span>
 @endif
 @can('akademik.tahunajaran.edit')
 <button wire:click="toggleLockAbsensi({{ $sem->id }})" class="ml-1 text-[11px] text-gray-400 hover:text-text-main underline">
 ({{ $sem->isLockedForAbsensi() ? 'Buka' : 'Kunci' }})
 </button>
 @endcan
 </div>
 </div>
 </div>
 @endforeach
 </div>
 </div>
 @empty
 <div class="card text-center text-sm text-text-secondary">
 Belum ada data tahun ajaran. Klik tombol Tambah Tahun Ajaran di atas.
 </div>
 @endforelse
 </div>

 {{-- Modal Tambah Tahun Ajaran --}}
 @if ($showModal)
 <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
 <div class="card w-full max-w-md space-y-4">
 <div class="flex items-center justify-between border-b border-border pb-3">
 <h2 class="text-lg font-semibold">Tambah Tahun Ajaran Baru</h2>
 <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-text-secondary">&times;</button>
 </div>

 <form wire:submit="save" class="space-y-3">
 <div>
 <label class="label">Nama Tahun Ajaran * (contoh: 2025/2026)</label>
 <input type="text" wire:model="nama" placeholder="2025/2026" class="input">
 @error('nama') <p class="error">{{ $message }}</p> @enderror
 </div>

 <div class="grid gap-3 sm:grid-cols-2">
 <div>
 <label class="label">Tanggal Mulai</label>
 <input type="date" wire:model="tgl_mulai" class="input">
 @error('tgl_mulai') <p class="error">{{ $message }}</p> @enderror
 </div>
 <div>
 <label class="label">Tanggal Selesai</label>
 <input type="date" wire:model="tgl_selesai" class="input">
 @error('tgl_selesai') <p class="error">{{ $message }}</p> @enderror
 </div>
 </div>

 <label class="flex items-center gap-2 pt-1 text-sm">
 <input type="checkbox" wire:model="is_active" class="rounded border-border text-brand-600">
 <span>Jadikan sebagai Tahun Ajaran Aktif</span>
 </label>

 <div class="flex justify-end gap-2 border-t border-border pt-3">
 <button type="button" wire:click="$set('showModal', false)" class="btn btn-secondary">Batal</button>
 <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
 <span wire:loading.remove wire:target="save">Simpan</span>
 <span wire:loading wire:target="save">Menyimpan...</span>
 </button>
 </div>
 </form>
 </div>
 </div>
 @endif
</div>
