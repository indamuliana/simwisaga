<div>
 <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
 <div>
 <h1 class="text-2xl font-bold text-text-main">Data Jurusan</h1>
 <p class="text-sm text-text-secondary">Rekapitulasi jumlah siswa per jurusan/kompetensi keahlian</p>
 </div>
 <div class="w-full sm:w-64">
 <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari jurusan..." class="form-input w-full">
 </div>
 </div>

 <div class="bg-surface rounded-lg shadow border border-border">
 <div class="overflow-x-auto">
 <table class="w-full text-left text-sm text-text-secondary">
 <thead class="bg-surface-hover text-xs uppercase text-text-main bg-surface-hover/50 text-text-secondary">
 <tr>
 <th class="px-4 py-3 font-semibold">Kode</th>
 <th class="px-4 py-3 font-semibold">Nama Jurusan</th>
 <th class="px-4 py-3 text-center font-semibold">Total Siswa</th>
 <th class="px-4 py-3 text-center font-semibold">Laki-laki (L)</th>
 <th class="px-4 py-3 text-center font-semibold">Perempuan (P)</th>
 <th class="px-4 py-3 text-center font-semibold">Aksi</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
 @forelse($jurusans as $j)
 <tr class="hover:bg-surface-hover dark:hover:bg-gray-700/50">
 <td class="px-4 py-3">{{ $j->kode }}</td>
 <td class="px-4 py-3 font-medium text-text-main">{{ $j->nama }}</td>
 <td class="px-4 py-3 text-center font-bold text-primary dark:text-blue-400">{{ $j->total_siswa }}</td>
 <td class="px-4 py-3 text-center">{{ $j->siswa_l }}</td>
 <td class="px-4 py-3 text-center">{{ $j->siswa_p }}</td>
 <td class="px-4 py-3 text-center">
 <a href="{{ route('dashboard-data.jurusan.students', $j->id) }}" class="btn btn-secondary !py-1 !px-3 !text-xs">Detil</a>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="6" class="px-4 py-8 text-center text-text-secondary">Tidak ada data jurusan ditemukan.</td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 @if($jurusans->hasPages())
 <div class="border-t border-border p-4">
 {{ $jurusans->links() }}
 </div>
 @endif
 </div>
</div>
