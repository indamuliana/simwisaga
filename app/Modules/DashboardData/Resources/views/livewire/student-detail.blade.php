<div>
 <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
 <div class="flex items-center gap-3">
 <button onclick="history.back()" class="rounded-full p-2 bg-gray-100 hover:bg-gray-200 text-text-secondary transition-colors bg-surface dark:hover:bg-gray-700" title="Kembali">
 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
 <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
 </svg>
 </button>
 <div>
 <h1 class="text-2xl font-bold text-text-main">Daftar Siswa</h1>
 <p class="text-sm text-text-secondary">{{ $entityName }}</p>
 </div>
 </div>
 <div class="w-full sm:w-64">
 <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, NISN, NIPD..." class="form-input w-full">
 </div>
 </div>

 <div class="bg-surface rounded-lg shadow border border-border">
 <div class="overflow-x-auto">
 <table class="w-full text-left text-sm text-text-secondary">
 <thead class="bg-surface-hover text-xs uppercase text-text-main bg-surface-hover/50 text-text-secondary">
 <tr>
 <th class="px-4 py-3 font-semibold">NISN / NIPD</th>
 <th class="px-4 py-3 font-semibold">Nama Lengkap</th>
 <th class="px-4 py-3 text-center font-semibold">L/P</th>
 <th class="px-4 py-3 font-semibold">Program</th>
 <th class="px-4 py-3 font-semibold">Jurusan</th>
 <th class="px-4 py-3 font-semibold">Rombel</th>
 <th class="px-4 py-3 font-semibold">Sekolah Asal</th>
 <th class="px-4 py-3 text-center font-semibold">Status</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
 @forelse($siswas as $siswa)
 <tr class="hover:bg-surface-hover dark:hover:bg-gray-700/50">
 <td class="px-4 py-3">
 <div>{{ $siswa->nisn ?: '-' }}</div>
 <div class="text-xs text-text-secondary">{{ $siswa->nipd ?: '-' }}</div>
 </td>
 <td class="px-4 py-3 font-medium text-text-main">{{ $siswa->nama }}</td>
 <td class="px-4 py-3 text-center">{{ $siswa->jk }}</td>
 <td class="px-4 py-3">{{ $siswa->program?->nama ?: '-' }}</td>
 <td class="px-4 py-3">{{ $siswa->rombel?->jurusan?->nama ?: '-' }}</td>
 <td class="px-4 py-3">{{ $siswa->rombel?->nama ?: '-' }}</td>
 <td class="px-4 py-3">{{ $siswa->sekolah_asal ?: '-' }}</td>
 <td class="px-4 py-3 text-center">
 @if($siswa->is_active)
 <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-success bg-success-soft dark:text-green-400">Aktif</span>
 @else
 <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">{{ ucfirst($siswa->status) }}</span>
 @endif
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="8" class="px-4 py-8 text-center text-text-secondary">Tidak ada data siswa ditemukan.</td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 @if($siswas->hasPages())
 <div class="border-t border-border p-4">
 {{ $siswas->links() }}
 </div>
 @endif
 </div>
</div>
