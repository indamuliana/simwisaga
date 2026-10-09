<div class="space-y-5">
 <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
 <div>
 <h1 class="text-xl font-bold">Kompetensi Keahlian (Jurusan)</h1>
 <p class="text-sm text-text-secondary">Pengelolaan jurusan resmi SMK Wikrama 1 Garut beserta pemetaan kode lama/alias.</p>
 </div>
 @can('akademik.jurusan.tambah')
 <button wire:click="create" class="btn btn-primary">
 + Tambah Jurusan
 </button>
 @endcan
 </div>

 @if (session('status'))
 <div class="rounded-lg border border-success bg-success-soft px-4 py-3 text-sm text-success">
 {{ session('status') }}
 </div>
 @endif

 <div class="card overflow-hidden !p-0">
 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
 <thead class="bg-surface-hover text-left bg-surface/60">
 <tr>
 <th class="px-4 py-3 font-semibold text-text-main text-text-secondary">Kode</th>
 <th class="px-4 py-3 font-semibold text-text-main text-text-secondary">Nama Kompetensi Keahlian</th>
 <th class="px-4 py-3 font-semibold text-text-main text-text-secondary">Kode Alias / Nama Lama</th>
 <th class="px-4 py-3 text-center font-semibold text-text-main text-text-secondary">Jumlah Rombel</th>
 <th class="px-4 py-3 text-center font-semibold text-text-main text-text-secondary">Status</th>
 <th class="px-4 py-3 text-center font-semibold text-text-main text-text-secondary">Aksi</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
 @forelse ($jurusans as $j)
 <tr class="hover:bg-surface-hover dark:hover:bg-gray-800/40">
 <td class="whitespace-nowrap px-4 py-3 font-bold text-brand-600 dark:text-brand-400">
 {{ $j->kode }}
 </td>
 <td class="px-4 py-3 font-medium text-text-main dark:text-gray-100">
 {{ $j->nama }}
 @if ($j->deskripsi)
 <span class="block text-xs font-normal text-gray-400">{{ $j->deskripsi }}</span>
 @endif
 </td>
 <td class="px-4 py-3 text-xs">
 @if (!empty($j->kode_alias))
 @foreach ($j->kode_alias as $alias)
 <span class="inline-block rounded bg-gray-100 px-1.5 py-0.5 font-mono text-text-secondary bg-surface">
 {{ $alias }}
 </span>
 @endforeach
 @else
 <span class="text-gray-400">-</span>
 @endif
 </td>
 <td class="px-4 py-3 text-center font-semibold">
 {{ $j->rombels_count }}
 </td>
 <td class="px-4 py-3 text-center">
 @if ($j->is_active)
 <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-success dark:bg-green-900/40">
 Aktif
 </span>
 @else
 <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-text-secondary bg-surface">
 Nonaktif
 </span>
 @endif
 </td>
 <td class="px-4 py-3 text-center">
 @can('akademik.jurusan.edit')
 <button wire:click="edit({{ $j->id }})" class="text-xs font-medium text-brand-600 hover:underline dark:text-brand-400">
 Edit
 </button>
 @endcan
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="6" class="px-4 py-6 text-center text-sm text-text-secondary">Belum ada data jurusan.</td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>

 {{-- Modal Tambah / Edit Jurusan --}}
 @if ($showModal)
 <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
 <div class="card w-full max-w-md space-y-4">
 <div class="flex items-center justify-between border-b border-border pb-3">
 <h2 class="text-lg font-semibold">{{ $jurusanId ? 'Edit Jurusan' : 'Tambah Jurusan' }}</h2>
 <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-text-secondary">&times;</button>
 </div>

 <form wire:submit="save" class="space-y-3">
 <div>
 <label class="label">Kode Jurusan * (contoh: PPLG)</label>
 <input type="text" wire:model="kode" placeholder="PPLG" class="input uppercase">
 @error('kode') <p class="error">{{ $message }}</p> @enderror
 </div>

 <div>
 <label class="label">Nama Jurusan *</label>
 <input type="text" wire:model="nama" placeholder="Pengembangan Perangkat Lunak dan Gim" class="input">
 @error('nama') <p class="error">{{ $message }}</p> @enderror
 </div>

 <div>
 <label class="label">Kode Alias / Nama Lama (pisahkan koma, contoh: RPL, RPL 1)</label>
 <input type="text" wire:model="kode_alias_text" placeholder="RPL" class="input">
 <p class="mt-1 text-[11px] text-text-secondary">Membantu pencocokan otomatis data lama saat impor DAPODIK.</p>
 @error('kode_alias_text') <p class="error">{{ $message }}</p> @enderror
 </div>

 <div>
 <label class="label">Deskripsi Singkat</label>
 <textarea wire:model="deskripsi" rows="2" class="input"></textarea>
 @error('deskripsi') <p class="error">{{ $message }}</p> @enderror
 </div>

 <label class="flex items-center gap-2 pt-1 text-sm">
 <input type="checkbox" wire:model="is_active" class="rounded border-border text-brand-600">
 <span>Jurusan Aktif</span>
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
