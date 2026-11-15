<div>
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-base font-bold text-text-main">Data Rombel</h2>
            <p class="text-xs text-text-secondary">Rekapitulasi jumlah siswa per rombongan belajar</p>
        </div>
        <div class="flex flex-col sm:flex-row items-center gap-2.5 w-full sm:w-auto">
            <div class="w-full sm:w-56">
                <select wire:model.live="filterTahunAjaran" class="w-full text-xs rounded-lg border-border bg-surface text-text-main shadow-xs py-1.5">
                    <option value="">-- Semua Tahun Ajaran --</option>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-60">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari rombel..." class="w-full px-3 py-1.5 text-xs rounded-lg border border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary shadow-xs">
            </div>
        </div>
    </div>

    <div class="bg-surface rounded-xl shadow-xs border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-text-secondary">
                <thead class="bg-surface-hover text-xs uppercase text-text-main">
                    <tr>
                        <x-table.sortable-th field="nama" :sortField="$sortField" :sortDirection="$sortDirection" class="px-4 py-3 font-semibold">Nama Rombel</x-table.sortable-th>
                        <x-table.sortable-th field="tingkat" :sortField="$sortField" :sortDirection="$sortDirection" class="px-4 py-3 font-semibold">Tingkat</x-table.sortable-th>
                        <x-table.sortable-th field="jurusan_id" :sortField="$sortField" :sortDirection="$sortDirection" class="px-4 py-3 font-semibold">Jurusan</x-table.sortable-th>
                        <x-table.sortable-th field="kapasitas" :sortField="$sortField" :sortDirection="$sortDirection" class="px-4 py-3 text-center font-semibold">Kapasitas</x-table.sortable-th>
                        <x-table.sortable-th field="total_siswa" :sortField="$sortField" :sortDirection="$sortDirection" class="px-4 py-3 text-center font-semibold">Total Siswa</x-table.sortable-th>
                        <x-table.sortable-th field="siswa_l" :sortField="$sortField" :sortDirection="$sortDirection" class="px-4 py-3 text-center font-semibold">Laki-laki (L)</x-table.sortable-th>
                        <x-table.sortable-th field="siswa_p" :sortField="$sortField" :sortDirection="$sortDirection" class="px-4 py-3 text-center font-semibold">Perempuan (P)</x-table.sortable-th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($rombels as $r)
                        <tr class="hover:bg-surface-hover/60 transition-colors">
                            <td class="px-4 py-3 font-medium text-text-main">{{ $r->nama }}</td>
                            <td class="px-4 py-3">{{ $r->tingkat }}</td>
                            <td class="px-4 py-3">{{ $r->jurusan?->nama }}</td>
                            <td class="px-4 py-3 text-center">{{ $r->kapasitas }}</td>
                            <td class="px-4 py-3 text-center font-bold text-primary dark:text-blue-400">{{ $r->total_siswa }}</td>
                            <td class="px-4 py-3 text-center">{{ $r->siswa_l }}</td>
                            <td class="px-4 py-3 text-center">{{ $r->siswa_p }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('dashboard-data.rombel.students', $r->id) }}" class="btn btn-secondary !py-1 !px-2.5 !text-xs">Detil</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-text-secondary">Tidak ada data rombel ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rombels->hasPages())
            <div class="border-t border-border p-3">
                {{ $rombels->links() }}
            </div>
        @endif
    </div>
</div>
