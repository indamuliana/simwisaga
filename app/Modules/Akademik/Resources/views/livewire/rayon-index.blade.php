<div class="space-y-5">
 <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
 <div>
 <h1 class="text-xl font-bold">Pengelolaan Rayon</h1>
 <p class="text-sm text-text-secondary">Pengelompokan siswa berdasarkan wilayah tempat tinggal dan guru pembimbing rayon.</p>
 </div>
 @can('akademik.rayon.tambah')
 <button wire:click="create" class="btn btn-primary">
 + Tambah Rayon
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
 <th class="px-4 py-3 font-semibold text-text-main text-text-secondary">Nama Rayon</th>
 <th class="px-4 py-3 font-semibold text-text-main text-text-secondary">Wilayah Cakupan</th>
 <th class="px-4 py-3 font-semibold text-text-main text-text-secondary">Pembimbing Rayon</th>
 <th class="px-4 py-3 font-semibold text-text-main text-text-secondary">Keterangan</th>
 <th class="px-4 py-3 text-center font-semibold text-text-main text-text-secondary">Aksi</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
 @forelse ($rayons as $r)
 <tr class="hover:bg-surface-hover dark:hover:bg-gray-800/40">
 <td class="whitespace-nowrap px-4 py-3 font-bold text-brand-600 dark:text-brand-400">
 {{ $r->nama }}
 </td>
 <td class="px-4 py-3 font-medium text-text-main dark:text-gray-100">
 {{ $r->wilayah_cakupan ?: '-' }}
 </td>
 <td class="px-4 py-3 text-text-secondary">
 {{ $r->pembimbing?->name ?? 'Belum ditentukan' }}
 </td>
 <td class="px-4 py-3 text-xs text-gray-400">
 {{ $r->keterangan ?: '-' }}
 </td>
 <td class="px-4 py-3 text-center">
 @can('akademik.rayon.edit')
 <button wire:click="edit({{ $r->id }})" class="text-xs font-medium text-brand-600 hover:underline dark:text-brand-400">
 Edit
 </button>
 @endcan
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="5" class="px-4 py-6 text-center text-sm text-text-secondary">Belum ada data rayon.</td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>

 {{-- Modal Tambah / Edit Rayon --}}
 @if ($showModal)
 <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
 <div class="card w-full max-w-md space-y-4">
 <div class="flex items-center justify-between border-b border-border pb-3">
 <h2 class="text-lg font-semibold">{{ $rayonId ? 'Edit Rayon' : 'Tambah Rayon' }}</h2>
 <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-text-secondary">&times;</button>
 </div>

 <form wire:submit="save" class="space-y-3">
 <div>
 <label class="label">Nama Rayon * (contoh: Bales 1, Tarkid 1)</label>
 <input type="text" wire:model="nama" placeholder="Bales 1" class="input">
 @error('nama') <p class="error">{{ $message }}</p> @enderror
 </div>

 <div>
 <label class="label">Wilayah Cakupan (contoh: Banyuresmi, Leles)</label>
 <input type="text" wire:model="wilayah_cakupan" placeholder="Banyuresmi, Leles" class="input">
 @error('wilayah_cakupan') <p class="error">{{ $message }}</p> @enderror
 </div>

 <div>
 <label class="label">Guru Pembimbing Rayon</label>
 <select wire:model="pembimbing_id" class="input">
 <option value="">Pilih Pembimbing Rayon</option>
 @foreach ($gurus as $g)
 <option value="{{ $g->id }}">{{ $g->name }}</option>
 @endforeach
 </select>
 @error('pembimbing_id') <p class="error">{{ $message }}</p> @enderror
 </div>

 <div>
 <label class="label">Keterangan</label>
 <textarea wire:model="keterangan" rows="2" class="input"></textarea>
 @error('keterangan') <p class="error">{{ $message }}</p> @enderror
 </div>

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
