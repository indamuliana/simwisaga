<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-text-main">Data Rombel</h1>
            <p class="text-sm text-text-secondary">Rekapitulasi jumlah siswa per rombongan belajar</p>
        </div>
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <div class="w-full sm:w-56">
                <select wire:model.live="filterTahunAjaran" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                    <option value="">-- Semua Tahun Ajaran --</option>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-64">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari rombel..." class="w-full text-sm rounded-md border-border bg-surface text-text-main">
            </div>
        </div>
    </div>

    <div class="bg-surface rounded-lg shadow border border-border">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-text-secondary">
                <thead class="bg-surface-hover text-xs uppercase text-text-main bg-surface-hover/50 text-text-secondary">
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
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($rombels as $r)
                        <tr class="hover:bg-surface-hover dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 font-medium text-text-main">{{ $r->nama }}</td>
                            <td class="px-4 py-3">{{ $r->tingkat }}</td>
                            <td class="px-4 py-3">{{ $r->jurusan?->nama }}</td>
                            <td class="px-4 py-3 text-center">{{ $r->kapasitas }}</td>
                            <td class="px-4 py-3 text-center font-bold text-primary dark:text-blue-400">{{ $r->total_siswa }}</td>
                            <td class="px-4 py-3 text-center">{{ $r->siswa_l }}</td>
                            <td class="px-4 py-3 text-center">{{ $r->siswa_p }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('dashboard-data.rombel.students', $r->id) }}" class="btn btn-secondary !py-1 !px-3 !text-xs">Detil</a>
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
            <div class="border-t border-border p-4">
                {{ $rombels->links() }}
            </div>
        @endif
    </div>
</div>
