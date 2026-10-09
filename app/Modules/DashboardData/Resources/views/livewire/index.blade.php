<div>
 <div class="mb-6">
 <h1 class="text-2xl font-bold text-text-main">Dashboard Data</h1>
 <p class="text-sm text-text-secondary">Ringkasan statistik akademik dan kesiswaan</p>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
 <div class="bg-surface p-4 rounded-lg shadow border border-border">
 <h3 class="text-sm font-medium text-text-secondary">Siswa Aktif</h3>
 <p class="mt-2 text-3xl font-semibold text-primary dark:text-blue-400">{{ number_format($totalSiswaAktif) }}</p>
 </div>
 <div class="bg-surface p-4 rounded-lg shadow border border-border">
 <h3 class="text-sm font-medium text-text-secondary">Total Program</h3>
 <p class="mt-2 text-3xl font-semibold text-text-main">{{ number_format($totalProgram) }}</p>
 </div>
 <div class="bg-surface p-4 rounded-lg shadow border border-border">
 <h3 class="text-sm font-medium text-text-secondary">Total Jurusan</h3>
 <p class="mt-2 text-3xl font-semibold text-text-main">{{ number_format($totalJurusan) }}</p>
 </div>
 <div class="bg-surface p-4 rounded-lg shadow border border-border">
 <h3 class="text-sm font-medium text-text-secondary">Total Rombel</h3>
 <p class="mt-2 text-3xl font-semibold text-text-main">{{ number_format($totalRombel) }}</p>
 </div>
 <div class="bg-surface p-4 rounded-lg shadow border border-border">
 <h3 class="text-sm font-medium text-text-secondary">Sekolah Asal</h3>
 <p class="mt-2 text-3xl font-semibold text-text-main">{{ number_format($totalSekolahAsal) }}</p>
 </div>
 </div>

 <div class="bg-surface rounded-lg shadow border border-border">
 <div class="px-4 py-3 border-b border-border">
 <h2 class="text-lg font-medium text-text-main">Rekapitulasi Siswa Aktif per Jurusan</h2>
 </div>
 <div class="overflow-x-auto">
 <table class="w-full text-left text-sm text-text-secondary">
 <thead class="bg-surface-hover text-xs uppercase text-text-main bg-surface-hover/50 text-text-secondary">
 <tr>
 <th class="px-4 py-3 font-semibold">Kode</th>
 <th class="px-4 py-3 font-semibold">Jurusan</th>
 <th class="px-4 py-3 text-center font-semibold">Total Siswa</th>
 <th class="px-4 py-3 text-center font-semibold">Laki-laki (L)</th>
 <th class="px-4 py-3 text-center font-semibold">Perempuan (P)</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
 @forelse($jurusanStats as $j)
 <tr class="hover:bg-surface-hover dark:hover:bg-gray-700/50">
 <td class="px-4 py-3">{{ $j->kode }}</td>
 <td class="px-4 py-3 font-medium text-text-main">{{ $j->nama }}</td>
 <td class="px-4 py-3 text-center font-bold text-primary dark:text-blue-400">{{ $j->total_siswa }}</td>
 <td class="px-4 py-3 text-center">{{ $j->siswa_l }}</td>
 <td class="px-4 py-3 text-center">{{ $j->siswa_p }}</td>
 </tr>
 @empty
 <tr>
 <td colspan="5" class="px-4 py-8 text-center text-text-secondary">Belum ada data jurusan.</td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>
</div>
