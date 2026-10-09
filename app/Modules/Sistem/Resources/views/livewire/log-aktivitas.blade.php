<div class="space-y-5">
 <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
 <div>
 <h1 class="text-xl font-bold">Log Aktivitas (Audit Trail)</h1>
 <p class="text-sm text-text-secondary">Riwayat perubahan data: siapa, apa yang diubah, waktu, dan IP address.</p>
 </div>
 </div>

 {{-- Filter bar --}}
 <div class="card flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
 <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
 <div class="w-full sm:max-w-xs">
 <input type="text"
 wire:model.live.debounce.300ms="search"
 placeholder="Cari user, model, IP..."
 class="input">
 </div>
 <div class="w-full sm:max-w-xs">
 <select wire:model.live="eventFilter" class="input">
 <option value="">Semua Aksi</option>
 <option value="created">Dibuat (Created)</option>
 <option value="updated">Diubah (Updated)</option>
 <option value="deleted">Dihapus (Deleted)</option>
 <option value="restored">Dipulihkan (Restored)</option>
 </select>
 </div>
 </div>
 </div>

 {{-- Tabel Audit --}}
 <div class="card overflow-hidden !p-0">
 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
 <thead class="bg-surface-hover text-left bg-surface/60">
 <tr>
 <th class="px-4 py-3 font-semibold text-text-main text-text-secondary">Waktu</th>
 <th class="px-4 py-3 font-semibold text-text-main text-text-secondary">Pengguna</th>
 <th class="px-4 py-3 font-semibold text-text-main text-text-secondary">Aksi</th>
 <th class="px-4 py-3 font-semibold text-text-main text-text-secondary">Entitas / ID</th>
 <th class="px-4 py-3 font-semibold text-text-main text-text-secondary">Perubahan</th>
 <th class="px-4 py-3 font-semibold text-text-main text-text-secondary">IP Address</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
 @forelse ($audits as $audit)
 @php
 $badgeColor = match ($audit->event) {
 'created' => 'bg-green-100 text-success dark:bg-green-900/40 text-success',
 'updated' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
 'deleted' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
 default => 'bg-gray-100 text-text-main bg-surface-hover text-text-secondary',
 };
 @endphp
 <tr class="hover:bg-surface-hover dark:hover:bg-gray-800/40">
 <td class="whitespace-nowrap px-4 py-3 text-xs text-text-secondary">
 {{ $audit->created_at->format('d M Y H:i:s') }}
 </td>
 <td class="px-4 py-3 font-medium text-text-main dark:text-gray-100">
 {{ $audit->user?->name ?? 'Sistem' }}
 @if ($audit->user?->username)
 <span class="block text-xs font-normal text-gray-400">({{ $audit->user->username }})</span>
 @endif
 </td>
 <td class="px-4 py-3">
 <span class="rounded px-2 py-0.5 text-xs font-medium uppercase {{ $badgeColor }}">
 {{ $audit->event }}
 </span>
 </td>
 <td class="px-4 py-3 text-xs text-text-secondary">
 <span class="font-medium">{{ class_basename($audit->auditable_type) }}</span>
 <span class="text-gray-400">#{{ $audit->auditable_id }}</span>
 </td>
 <td class="px-4 py-3 text-xs">
 @if ($audit->event === 'updated')
 <details class="cursor-pointer">
 <summary class="text-brand-600 dark:text-brand-400 hover:underline">
 {{ count($audit->new_values) }} kolom diubah
 </summary>
 <div class="mt-2 space-y-1 rounded bg-surface-hover p-2 font-mono text-[11px] bg-surface">
 @foreach ($audit->new_values as $key => $newVal)
 @php $oldVal = $audit->old_values[$key] ?? null; @endphp
 <div>
 <span class="font-bold text-text-main text-text-secondary">{{ $key }}:</span>
 <span class="text-red-500 line-through">{{ is_array($oldVal) ? json_encode($oldVal) : ($oldVal ?? 'null') }}</span>
 &rarr;
 <span class="text-green-600 dark:text-green-400">{{ is_array($newVal) ? json_encode($newVal) : ($newVal ?? 'null') }}</span>
 </div>
 @endforeach
 </div>
 </details>
 @elseif ($audit->event === 'created')
 <span class="text-text-secondary">{{ count($audit->new_values) }} nilai awal dicatat</span>
 @else
 <span class="text-gray-400">-</span>
 @endif
 </td>
 <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-text-secondary">
 {{ $audit->ip_address ?? '-' }}
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="6" class="px-4 py-8 text-center text-sm text-text-secondary">
 Belum ada catatan aktivitas.
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 @if ($audits->hasPages())
 <div class="border-t border-border p-4">
 {{ $audits->links() }}
 </div>
 @endif
 </div>
</div>
