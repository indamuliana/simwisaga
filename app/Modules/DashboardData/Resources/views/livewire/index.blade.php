<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h1 class="text-2xl font-bold text-text-main">Dashboard Data</h1>
            <p class="text-sm text-text-secondary">Ringkasan statistik akademik dan kesiswaan</p>
        </div>
        @if($currentTa)
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-surface border border-border text-xs text-text-secondary shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Tahun Ajaran Aktif:</span>
                <span class="font-bold text-text-main">{{ $currentTa->nama ?? $currentTa->tahun }}</span>
            </div>
        @endif
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
        <a href="{{ route('dashboard-data.rombel') }}" class="bg-surface p-4 rounded-lg shadow border border-border hover:border-primary/50 transition group block">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-medium text-text-secondary group-hover:text-primary transition">Total Rombel</h3>
                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">T.A. Aktif</span>
            </div>
            <p class="mt-2 text-3xl font-semibold text-text-main group-hover:text-primary transition">{{ number_format($totalRombel) }}</p>
        </a>
        <div class="bg-surface p-4 rounded-lg shadow border border-border">
            <h3 class="text-sm font-medium text-text-secondary">Sekolah Asal</h3>
            <p class="mt-2 text-3xl font-semibold text-text-main">{{ number_format($totalSekolahAsal) }}</p>
        </div>
    </div>

    <div class="bg-surface rounded-lg shadow border border-border">
        <div class="px-4 py-3 border-b border-border flex items-center justify-between">
            <h2 class="text-lg font-medium text-text-main">Rekapitulasi Siswa Aktif per Jurusan</h2>
            <span class="text-xs text-text-secondary">Siswa pada Rombel T.A. Aktif</span>
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
                            <td class="px-4 py-3 font-mono font-bold">{{ $j->kode }}</td>
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
