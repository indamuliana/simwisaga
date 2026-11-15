<div>
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-base font-bold text-text-main">Data Program Keahlian</h2>
            <p class="text-xs text-text-secondary">Rekapitulasi jumlah siswa per program keahlian</p>
        </div>
        <div class="w-full sm:w-64">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari program..." class="w-full px-3 py-1.5 text-xs rounded-lg border border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary shadow-xs">
        </div>
    </div>

    <div class="bg-surface rounded-xl shadow-xs border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-text-secondary">
                <thead class="bg-surface-hover text-xs uppercase text-text-main">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Kode</th>
                        <th class="px-4 py-3 font-semibold">Nama Program</th>
                        <th class="px-4 py-3 text-center font-semibold">Total Siswa</th>
                        <th class="px-4 py-3 text-center font-semibold">Laki-laki (L)</th>
                        <th class="px-4 py-3 text-center font-semibold">Perempuan (P)</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($programs as $p)
                        <tr class="hover:bg-surface-hover/60 transition-colors">
                            <td class="px-4 py-3 font-mono font-bold text-text-main">{{ $p->kode }}</td>
                            <td class="px-4 py-3 font-medium text-text-main">{{ $p->nama }}</td>
                            <td class="px-4 py-3 text-center font-bold text-primary dark:text-blue-400">{{ $p->total_siswa }}</td>
                            <td class="px-4 py-3 text-center">{{ $p->siswa_l }}</td>
                            <td class="px-4 py-3 text-center">{{ $p->siswa_p }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('dashboard-data.program.students', $p->id) }}" class="btn btn-secondary !py-1 !px-2.5 !text-xs">Detil</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-text-secondary">Tidak ada data program ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($programs->hasPages())
            <div class="border-t border-border p-3">
                {{ $programs->links() }}
            </div>
        @endif
    </div>
</div>
