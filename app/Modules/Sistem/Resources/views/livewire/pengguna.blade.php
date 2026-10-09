<div class="space-y-5">
 <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
 <div>
 <h1 class="text-xl font-bold">Pengelolaan Pengguna</h1>
 <p class="text-sm text-text-secondary">Kelola akun, penugasan multi-role, dan status aktif pengguna.</p>
 </div>
 @can('sistem.pengguna.tambah')
 <button wire:click="create" class="btn btn-primary">
 + Tambah Pengguna
 </button>
 @endcan
 </div>

 @if (session('status'))
 <div class="rounded-lg border border-success bg-success-soft px-4 py-3 text-sm text-success">
 {{ session('status') }}
 </div>
 @endif
 @if (session('error'))
 <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200">
 {{ session('error') }}
 </div>
 @endif

 {{-- Filter bar --}}
 <div class="card flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
 <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
 <div class="w-full sm:max-w-xs">
 <input type="text"
 wire:model.live.debounce.300ms="search"
 placeholder="Cari nama, username, email..."
 class="input">
 </div>
 <div class="w-full sm:max-w-xs">
 <select wire:model.live="roleFilter" class="input">
 <option value="">Semua Role</option>
 @foreach ($roles as $r)
 <option value="{{ $r->name }}">{{ $r->name }}</option>
 @endforeach
 </select>
 </div>
 </div>
 </div>

 {{-- Tabel Pengguna --}}
 <div class="card overflow-hidden !p-0">
 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
 <thead class="bg-surface-hover text-left bg-surface/60">
 <tr>
 <th class="px-4 py-3 font-semibold text-text-main text-text-secondary">Pengguna</th>
 <th class="px-4 py-3 font-semibold text-text-main text-text-secondary">Username / Email</th>
 <th class="px-4 py-3 font-semibold text-text-main text-text-secondary">Role</th>
 <th class="px-4 py-3 text-center font-semibold text-text-main text-text-secondary">Status</th>
 <th class="px-4 py-3 text-center font-semibold text-text-main text-text-secondary">Aksi</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
 @forelse ($users as $user)
 <tr class="hover:bg-surface-hover dark:hover:bg-gray-800/40">
 <td class="px-4 py-3 font-medium text-text-main dark:text-gray-100">
 {{ $user->name }}
 @if ($user->must_change_password)
 <span class="ml-1 inline-block rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-medium text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
 Wajib Ganti Password
 </span>
 @endif
 </td>
 <td class="px-4 py-3 text-text-secondary">
 <span class="font-mono text-xs">{{ $user->username }}</span>
 @if ($user->email)
 <span class="block text-xs text-gray-400">{{ $user->email }}</span>
 @endif
 </td>
 <td class="px-4 py-3">
 <div class="flex flex-wrap gap-1">
 @forelse ($user->roles as $role)
 <span class="rounded bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-700 dark:bg-brand-900/30 dark:text-brand-300">
 {{ $role->name }}
 </span>
 @empty
 <span class="text-xs text-gray-400">Tanpa role</span>
 @endforelse
 </div>
 </td>
 <td class="px-4 py-3 text-center">
 @if ($user->is_active)
 <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-success dark:bg-green-900/40">
 Aktif
 </span>
 @else
 <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/40 dark:text-red-300">
 Nonaktif
 </span>
 @endif
 </td>
 <td class="px-4 py-3 text-center">
 <div class="flex items-center justify-center gap-2">
 @can('sistem.pengguna.edit')
 <button wire:click="edit({{ $user->id }})"
 class="text-xs font-medium text-brand-600 hover:underline dark:text-brand-400">
 Edit
 </button>
 @if ($user->id !== auth()->id())
 <button wire:click="toggleActive({{ $user->id }})"
 wire:confirm="Yakin ingin mengubah status akun ini?"
 class="text-xs font-medium {{ $user->is_active ? 'text-amber-600 dark:text-amber-400' : 'text-green-600 dark:text-green-400' }} hover:underline">
 {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
 </button>
 @endif
 @endcan
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="5" class="px-4 py-8 text-center text-sm text-text-secondary">
 Tidak ada data pengguna yang cocok.
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 @if ($users->hasPages())
 <div class="border-t border-border p-4">
 {{ $users->links() }}
 </div>
 @endif
 </div>

 {{-- Modal Tambah / Edit Pengguna --}}
 @if ($showModal)
 <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
 <div class="card w-full max-w-lg space-y-4">
 <div class="flex items-center justify-between border-b border-border pb-3">
 <h2 class="text-lg font-semibold">{{ $userId ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h2>
 <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-text-secondary">&times;</button>
 </div>

 <form wire:submit="save" class="space-y-3">
 <div>
 <label class="label">Nama Lengkap *</label>
 <input type="text" wire:model="name" class="input">
 @error('name') <p class="error">{{ $message }}</p> @enderror
 </div>

 <div class="grid gap-3 sm:grid-cols-2">
 <div>
 <label class="label">Username (NIS/NIP) *</label>
 <input type="text" wire:model="username" class="input">
 @error('username') <p class="error">{{ $message }}</p> @enderror
 </div>
 <div>
 <label class="label">Email (Opsional)</label>
 <input type="email" wire:model="email" class="input">
 @error('email') <p class="error">{{ $message }}</p> @enderror
 </div>
 </div>

 <div>
 <label class="label">{{ $userId ? 'Password Baru (Kosongkan jika tidak diubah)' : 'Password Awal *' }}</label>
 <input type="password" wire:model="password" class="input" autocomplete="new-password">
 @error('password') <p class="error">{{ $message }}</p> @enderror
 </div>

 <div>
 <label class="label">Pilih Role</label>
 <div class="grid grid-cols-2 gap-2 rounded-lg border border-border p-3">
 @foreach ($roles as $r)
 <label class="flex items-center gap-2 text-xs">
 <input type="checkbox"
 wire:model="selectedRoles"
 value="{{ $r->name }}"
 class="rounded border-border text-brand-600 focus:ring-brand-500">
 <span>{{ $r->name }}</span>
 </label>
 @endforeach
 </div>
 </div>

 <div class="flex flex-col gap-2 pt-2">
 <label class="flex items-center gap-2 text-sm">
 <input type="checkbox" wire:model="is_active" class="rounded border-border text-brand-600">
 <span>Akun Aktif</span>
 </label>
 <label class="flex items-center gap-2 text-sm">
 <input type="checkbox" wire:model="must_change_password" class="rounded border-border text-brand-600">
 <span>Wajib Ganti Password saat Login</span>
 </label>
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
