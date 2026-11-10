<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Guru & Tenaga Kependidikan (GTK)</h1>
            <p class="text-sm text-text-secondary">Pengelolaan profil pendidik, staf kependidikan, serta akun pengguna sistem.</p>
        </div>
        <div class="flex items-center gap-2">
            @can('kepegawaian.pegawai.cetak')
                <button wire:click="cetakDaftar" class="btn btn-secondary">
                    <span>&#128424;</span> Cetak PDF
                </button>
            @endcan
            @can('kepegawaian.pegawai.tambah')
                <a href="{{ route('kepegawaian.pegawai.create') }}" class="btn btn-primary">
                    + Tambah Pegawai
                </a>
            @endcan
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 text-sm text-success bg-success-soft rounded-lg border border-success">
            {{ session('status') }}
        </div>
    @endif

    <!-- Search & Filter Card -->
    <div class="p-4 card flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, NIP, NUPTK, atau NIK..." class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
        </div>
        <div class="sm:w-52">
            <select wire:model.live="filterGtk" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                <option value="">-- Semua Jenis GTK --</option>
                <option value="Guru">Guru</option>
                <option value="Tendik">Tendik (Tenaga Kependidikan)</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>
        <div class="sm:w-52">
            <select wire:model.live="filterStatus" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                <option value="">-- Semua Status --</option>
                <option value="GTY">GTY (Tetap Yayasan)</option>
                <option value="STY">STY (Staf Tetap)</option>
                <option value="GTT">GTT (Guru Tidak Tetap)</option>
                <option value="STT">STT (Staf Tidak Tetap)</option>
                <option value="IDS">IDS (Instruktur / Dosen)</option>
                <option value="PNS DPK">PNS DPK</option>
                <option value="PPPK">PPPK</option>
            </select>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-sm">
                <thead class="bg-surface-hover bg-surface/50 text-text-secondary font-medium">
                    <tr>
                        <x-table.sortable-th field="nama_lengkap" :sortField="$sortField" :sortDirection="$sortDirection" class="px-6 py-3">Nama Pegawai & Kontak</x-table.sortable-th>
                        <x-table.sortable-th field="nip" :sortField="$sortField" :sortDirection="$sortDirection" class="px-6 py-3">NIP / NUPTK / NIK</x-table.sortable-th>
                        <x-table.sortable-th field="jenis_gtk" :sortField="$sortField" :sortDirection="$sortDirection" class="px-6 py-3">Jenis & Status</x-table.sortable-th>
                        <x-table.sortable-th field="user_id" :sortField="$sortField" :sortDirection="$sortDirection" class="px-6 py-3">Akun Login</x-table.sortable-th>
                        <x-table.sortable-th field="is_active" :sortField="$sortField" :sortDirection="$sortDirection" class="px-6 py-3">Status</x-table.sortable-th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-text-main">
                    @forelse($pegawais as $p)
                        <tr class="hover:bg-surface-hover/50 dark:hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $p->foto_utama_url }}" alt="{{ $p->nama_lengkap }}" class="w-9 h-9 rounded-xl object-cover border border-border shrink-0">
                                    <div>
                                        <a href="{{ route('kepegawaian.pegawai.show', $p->id) }}" class="font-bold text-text-main hover:text-primary transition">
                                            {{ $p->nama_gelar }}
                                        </a>
                                        <div class="text-xs text-text-secondary">{{ $p->email ?? '-' }} | {{ $p->no_hp ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs">
                                <div><span class="text-slate-400">NIP:</span> {{ $p->nip ?? '-' }}</div>
                                <div><span class="text-slate-400">NUPTK:</span> {{ $p->nuptk ?? '-' }}</div>
                                <div><span class="text-slate-400">NIK:</span> {{ \App\Core\Support\DataMasker::maskNik($p->nik) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ strtolower($p->jenis_gtk) === 'guru' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300' }}">
                                    {{ $p->jenis_gtk }}
                                </span>
                                <span class="text-xs text-text-secondary block mt-0.5 uppercase font-medium">{{ $p->status_kepegawaian }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($p->user)
                                    <div class="font-mono text-xs font-semibold text-text-main">{{ $p->user->username }}</div>
                                    <div class="text-xs text-primary dark:text-indigo-400">{{ $p->user->getRoleNames()->join(', ') }}</div>
                                @else
                                    <span class="text-xs italic text-slate-400">Belum ada akun</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($p->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-success-soft text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-text-secondary bg-surface">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @can('kepegawaian.pegawai.lihat')
                                    <a href="{{ route('kepegawaian.pegawai.show', $p->id) }}" class="text-xs font-semibold text-primary hover:text-indigo-800 dark:text-indigo-400">Lihat</a>
                                @endcan
                                @can('kepegawaian.pegawai.edit')
                                    <a href="{{ route('kepegawaian.pegawai.edit', $p->id) }}" class="text-xs font-semibold text-text-secondary hover:text-text-main">Edit</a>
                                @endcan
                                @can('kepegawaian.pegawai.hapus')
                                    <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus data pegawai {{ $p->nama_lengkap }} beserta akun loginnya?" class="text-xs font-semibold text-rose-600 hover:text-rose-800 dark:text-rose-400">Hapus</button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada data guru atau tenaga kependidikan yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-border">
            {{ $pegawais->links() }}
        </div>
    </div>
</div>
