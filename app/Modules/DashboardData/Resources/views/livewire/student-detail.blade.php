<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <button onclick="history.back()" class="rounded-full p-2 bg-surface hover:bg-surface-hover text-text-secondary border border-border transition-colors shadow-2xs" title="Kembali">
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
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, NISN, NIPD..." class="w-full px-3 py-2 text-sm rounded-lg border border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary shadow-xs">
        </div>
    </div>

    {{-- Ringkasan Khusus Sekolah Asal --}}
    @if($schoolStats)
        <div class="card p-4 mb-6 border-l-4 border-l-primary">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-xl bg-primary-soft text-primary border border-primary/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-text-muted">Profil Feeder Sekolah</span>
                        <h2 class="text-lg font-bold text-text-main">{{ $identifier }}</h2>
                        <div class="flex items-center gap-3 text-xs text-text-secondary mt-0.5">
                            <span class="font-medium text-primary">{{ $schoolStats['total'] }} Total Siswa</span>
                            <span>•</span>
                            <span class="text-blue-600 dark:text-blue-400">{{ $schoolStats['l'] }} Laki-laki</span>
                            <span>•</span>
                            <span class="text-pink-600 dark:text-pink-400">{{ $schoolStats['p'] }} Perempuan</span>
                        </div>
                    </div>
                </div>

                {{-- Sebaran Jurusan Pills --}}
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs text-text-muted font-medium mr-1">Pilihan Jurusan:</span>
                    @forelse($schoolStats['jurusans'] as $kodeJurusan => $count)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-surface-raised border border-border text-text-main">
                            <span class="font-mono font-bold text-primary">{{ $kodeJurusan }}</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-primary-soft text-primary font-bold">{{ $count }}</span>
                        </span>
                    @empty
                        <span class="text-xs text-text-muted">Belum ada jurusan terpetakan</span>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    {{-- Tabel Siswa --}}
    <div class="bg-surface rounded-lg shadow border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-text-secondary">
                <thead class="bg-surface-hover/70 text-xs uppercase text-text-main border-b border-border">
                    <tr>
                        <th class="px-4 py-3.5 font-semibold">NISN / NIPD</th>
                        <th class="px-4 py-3.5 font-semibold">Nama Lengkap</th>
                        <th class="px-4 py-3.5 text-center font-semibold">L/P</th>
                        <th class="px-4 py-3.5 font-semibold">Program</th>
                        <th class="px-4 py-3.5 font-semibold">Jurusan</th>
                        <th class="px-4 py-3.5 font-semibold">Rombel</th>
                        <th class="px-4 py-3.5 font-semibold">Sekolah Asal</th>
                        <th class="px-4 py-3.5 text-center font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($siswas as $siswa)
                        <tr class="hover:bg-surface-hover/50 transition">
                            <td class="px-4 py-3">
                                <div class="font-medium text-text-main">{{ $siswa->nisn ?: '-' }}</div>
                                <div class="text-xs text-text-muted">{{ $siswa->nipd ?: '-' }}</div>
                            </td>
                            <td class="px-4 py-3 font-medium text-text-main">{{ $siswa->nama }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($siswa->jk === 'L')
                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">L</span>
                                @else
                                    <span class="text-xs font-bold text-pink-600 dark:text-pink-400">P</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $siswa->program?->nama ?: '-' }}</td>
                            <td class="px-4 py-3">
                                @if($siswa->rombel?->jurusan)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-surface-raised border border-border text-text-main">
                                        {{ $siswa->rombel->jurusan->kode }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $siswa->rombel?->nama ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $siswa->sekolah_asal ?: '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($siswa->is_active)
                                    <span class="badge badge-success text-[11px]">Aktif</span>
                                @else
                                    <span class="badge badge-danger text-[11px]">{{ ucfirst($siswa->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-text-muted">
                                Tidak ada data siswa yang cocok dengan kriteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($siswas->hasPages())
            <div class="border-t border-border p-4 bg-surface">
                {{ $siswas->links() }}
            </div>
        @endif
    </div>
</div>
