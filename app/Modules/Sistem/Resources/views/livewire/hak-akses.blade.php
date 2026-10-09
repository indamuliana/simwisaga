<div class="space-y-6">
 <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
 <div>
 <h1 class="text-xl font-bold">Matriks Hak Akses</h1>
 <p class="text-sm text-text-secondary">Atur izin akses (Lihat, Tambah, Edit, Hapus, Cetak) dan batasan data per role.</p>
 </div>
 @if ($selectedRole)
 <div class="flex items-center gap-2">
 <button wire:click="save" class="btn btn-primary" wire:loading.attr="disabled">
 <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
 <span wire:loading wire:target="save">Menyimpan...</span>
 </button>
 </div>
 @endif
 </div>

 @if (session('status'))
 <div class="rounded-lg border border-success bg-success-soft px-4 py-3 text-sm text-success">
 {{ session('status') }}
 </div>
 @endif

 {{-- Pemilih Role Tabs --}}
 <div class="border-b border-border">
 <nav class="-mb-px flex flex-wrap gap-2" aria-label="Tabs">
 @foreach ($roles as $role)
 <button type="button"
 wire:click="selectRole('{{ $role->id }}')"
 class="whitespace-nowrap border-b-2 px-3 py-2 text-sm font-medium transition {{ $selectedRoleId == $role->id ? 'border-brand-600 text-brand-600 dark:border-brand-500 dark:text-brand-400' : 'border-transparent text-text-secondary hover:border-border hover:text-text-main dark:hover:text-gray-300' }}">
 {{ $role->name }}
 </button>
 @endforeach
 </nav>
 </div>

 @if ($selectedRole)
 {{-- Pengaturan Scope Data --}}
 <div class="card space-y-3">
 <h2 class="text-sm font-semibold text-text-main dark:text-gray-100">Batasan Akses Data (Scope)</h2>
 <div class="max-w-md">
 <label class="label">Scope data untuk role <strong>{{ $selectedRole->name }}</strong>:</label>
 <select wire:model.live="roleScopes.{{ $selectedRole->id }}" class="input">
 @foreach (self::AVAILABLE_SCOPES as $key => $label)
 <option value="{{ $key }}">{{ $label }}</option>
 @endforeach
 </select>
 <p class="mt-1 text-xs text-text-secondary">Menentukan data apa saja yang dapat dilihat/dikelola oleh pengguna dengan role ini.</p>
 </div>
 </div>

 {{-- Tabel Matriks Permission --}}
 <div class="card overflow-hidden !p-0">
 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
 <thead class="bg-surface-hover text-left bg-surface/60">
 <tr>
 <th class="px-4 py-3 font-semibold text-text-main text-text-secondary">Modul / Fitur</th>
 <th class="px-4 py-3 text-center font-semibold text-text-main text-text-secondary">Lihat</th>
 <th class="px-4 py-3 text-center font-semibold text-text-main text-text-secondary">Tambah</th>
 <th class="px-4 py-3 text-center font-semibold text-text-main text-text-secondary">Edit</th>
 <th class="px-4 py-3 text-center font-semibold text-text-main text-text-secondary">Hapus</th>
 <th class="px-4 py-3 text-center font-semibold text-text-main text-text-secondary">Cetak</th>
 <th class="px-4 py-3 text-center font-semibold text-text-main text-text-secondary">Pilih Semua</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
 @php $currentModule = ''; @endphp
 @foreach ($matrix as $resource => $def)
 @if ($def['module'] !== $currentModule)
 @php $currentModule = $def['module']; @endphp
 <tr class="bg-gray-100/70 font-semibold bg-surface/50">
 <td colspan="7" class="px-4 py-2 text-xs uppercase tracking-wider text-text-secondary">
 {{ $currentModule }}
 </td>
 </tr>
 @endif
 <tr class="hover:bg-surface-hover dark:hover:bg-gray-800/40">
 <td class="px-4 py-3 font-medium text-text-main dark:text-gray-100">
 {{ $def['label'] }}
 <span class="block text-xs font-normal text-gray-400">({{ $resource }})</span>
 </td>
 @foreach (['lihat', 'tambah', 'edit', 'hapus', 'cetak'] as $act)
 <td class="px-4 py-3 text-center">
 @if (in_array($act, $def['actions'], true))
 @php $permName = "{$resource}.{$act}"; @endphp
 <input type="checkbox"
 wire:click="togglePermission('{{ $permName }}')"
 @checked(in_array($permName, $rolePermissions, true))
 class="h-4 w-4 rounded border-border text-brand-600 focus:ring-brand-500 dark:border-gray-600 bg-surface-hover">
 @else
 <span class="text-xs text-gray-300 dark:text-text-secondary">-</span>
 @endif
 </td>
 @endforeach
 <td class="px-4 py-3 text-center">
 <button type="button"
 wire:click="toggleAllForResource('{{ $resource }}', {{ json_encode($def['actions']) }})"
 class="text-xs font-medium text-brand-600 hover:underline dark:text-brand-400">
 Toggle
 </button>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 </div>

 <div class="flex justify-end">
 <button wire:click="save" class="btn btn-primary" wire:loading.attr="disabled">
 <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
 <span wire:loading wire:target="save">Menyimpan...</span>
 </button>
 </div>
 @endif
</div>
