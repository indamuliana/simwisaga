<div class="space-y-6">
    <!-- Header Halaman -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-surface p-6 rounded-2xl border border-border shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                <span>📚 Master Akademik</span>
                <span>&bull;</span>
                <span>Kurikulum Merdeka</span>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">Mata Pelajaran Pilihan (Mapil)</h1>
            <p class="text-xs text-text-secondary mt-1">
                Kelola master mata pelajaran pilihan minat-bakat, penugasan guru pengampu, dan anggota peserta didik lintas kelas/tingkat.
            </p>
        </div>

        <!-- Tab Navigasi Atas -->
        <div class="flex items-center gap-1.5 p-1 bg-surface-hover rounded-xl border border-border text-xs">
            <button type="button"
                wire:click="switchTab('mapil')"
                class="px-4 py-2 rounded-lg font-bold transition-all flex items-center gap-1.5 {{ $activeTab === 'mapil' ? 'bg-surface text-primary shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                <span>📘</span>
                <span>1. Master Mapil</span>
            </button>
            <button type="button"
                wire:click="switchTab('anggota')"
                class="px-4 py-2 rounded-lg font-bold transition-all flex items-center gap-1.5 {{ $activeTab === 'anggota' ? 'bg-surface text-primary shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                <span>👥</span>
                <span>2. Kelola Anggota Siswa</span>
                @if($currentMapil)
                    <span class="px-1.5 py-0.2 rounded-full bg-primary/10 text-primary text-[10px]">
                        {{ $currentMapil->anggotas()->count() }}
                    </span>
                @endif
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 dark:bg-emerald-950/40 p-4 text-xs font-medium text-emerald-800 dark:text-emerald-300 flex items-center gap-2 shadow-xs">
            <span>✅</span>
            <span>{{ session('status') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 dark:bg-rose-950/40 p-4 text-xs font-medium text-rose-800 dark:text-rose-300 flex items-center gap-2 shadow-xs">
            <span>❌</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- TAB 1: MASTER MAPEL PILIHAN -->
    @if ($activeTab === 'mapil')
        <!-- Bar Toolbar & Filter -->
        <div class="bg-surface p-4 rounded-2xl border border-border shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full md:w-auto flex-1">
                <!-- Pencarian -->
                <div class="relative">
                    <input type="text"
                        wire:model.live.debounce.300ms="searchMapil"
                        placeholder="Cari kode atau nama Mapil..."
                        class="w-full pl-8 pr-3 py-2 rounded-xl text-xs border border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                    <svg class="w-3.5 h-3.5 text-text-secondary absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Filter Tingkat -->
                <div>
                    <select wire:model.live="filterTingkat" class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                        <option value="">Semua Tingkat</option>
                        <option value="X">Tingkat X</option>
                        <option value="XI">Tingkat XI</option>
                        <option value="XII">Tingkat XII</option>
                    </select>
                </div>

                <!-- Filter Status -->
                <div>
                    <select wire:model.live="filterStatus" class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            @can('akademik.mapil.tambah')
                <button wire:click="openCreateMapil" class="w-full md:w-auto px-4 py-2 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition-all flex items-center justify-center gap-1.5 shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Mapel Pilihan</span>
                </button>
            @endcan
        </div>

        <!-- Tabel Daftar Mapil -->
        <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-border text-xs border-collapse">
                    <thead>
                        <tr class="bg-surface-hover/80 text-left border-b border-border text-text-secondary uppercase text-[10px] font-bold tracking-wider">
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Kode & Nama Mapil</th>
                            <th class="py-3 px-4">Tingkat Sasaran</th>
                            <th class="py-3 px-4 text-center">KKM & JP</th>
                            <th class="py-3 px-4">Guru Pengampu</th>
                            <th class="py-3 px-4 text-center">Anggota Siswa</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center w-48">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border font-medium">
                        @forelse ($mapils as $idx => $m)
                            <tr class="hover:bg-surface-hover/40 transition-colors">
                                <td class="py-3 px-4 text-center text-text-secondary font-mono">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-text-main text-xs">{{ $m->nama }}</div>
                                    <div class="text-[10px] font-mono text-primary">{{ $m->kode }}</div>
                                    @if($m->deskripsi)
                                        <div class="text-[10px] text-text-muted mt-0.5 line-clamp-1">{{ $m->deskripsi }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ((array) ($m->tingkats ?: []) as $tgk)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200">
                                                Kelas {{ $tgk }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center font-mono">
                                    <div>KKM: <strong class="text-text-main">{{ $m->kkm_default }}</strong></div>
                                    <div class="text-[10px] text-text-secondary">{{ $m->jp }} JP/minggu</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-text-main text-xs">{{ $m->guru->nama_lengkap ?? 'Belum Ditentukan' }}</div>
                                    @if($m->guruPendamping)
                                        <div class="text-[10px] text-text-secondary">Pendamping: {{ $m->guruPendamping->nama_lengkap }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <button type="button"
                                        wire:click="switchTab('anggota', {{ $m->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-primary/10 hover:bg-primary/20 text-primary transition-colors cursor-pointer"
                                        title="Kelola Siswa Anggota">
                                        <span>👥</span>
                                        <span>{{ $m->anggotas_count }} Siswa</span>
                                    </button>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if ($m->is_active)
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-0.5 text-[11px] font-bold text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @can('akademik.mapil.edit')
                                            <button wire:click="editMapil({{ $m->id }})"
                                                class="px-2 py-1 rounded-lg border border-border bg-surface hover:bg-surface-hover text-text-main text-[11px] font-semibold transition-colors flex items-center gap-1"
                                                title="Edit data Mapil">
                                                <span>✏️</span>
                                                <span>Edit</span>
                                            </button>
                                            <button wire:click="toggleActiveMapil({{ $m->id }})"
                                                wire:confirm="Ubah status keaktifan Mapil ini?"
                                                class="px-2 py-1 rounded-lg border text-[11px] font-semibold transition-colors {{ $m->is_active ? 'border-border text-text-secondary hover:text-text-main' : 'border-emerald-200 bg-emerald-50 text-emerald-700' }}"
                                                title="{{ $m->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                {{ $m->is_active ? 'Off' : 'On' }}
                                            </button>
                                        @endcan
                                        @can('akademik.mapil.hapus')
                                            <button wire:click="deleteMapil({{ $m->id }})"
                                                wire:confirm="PERINGATAN: Yakin ingin menghapus Mapel Pilihan '{{ $m->nama }}'? Data anggota dan nilai terkait akan terhapus."
                                                class="px-2 py-1 rounded-lg border border-rose-200 bg-rose-50/50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/30 dark:text-rose-300 text-[11px] font-semibold transition-colors"
                                                title="Hapus Mapil">
                                                🗑️
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-text-secondary">
                                    Belum ada data Mata Pelajaran Pilihan yang dibuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($mapils->hasPages())
                <div class="border-t border-border p-4 bg-surface-hover/30">
                    {{ $mapils->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 2: KELOLA ANGGOTA SISWA -->
    @if ($activeTab === 'anggota')
        <!-- Dropdown Pemilih Mapil & Detail Ringkas -->
        <div class="bg-surface p-5 rounded-2xl border border-border shadow-xs space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                <div class="md:col-span-5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1.5">
                        🎯 Pilih Mapel Pilihan yang Dikelola
                    </label>
                    <select wire:model.live="selectedMapilId" class="w-full text-xs font-bold rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                        <option value="">-- Pilih Mapel Pilihan --</option>
                        @foreach ($mapils as $m)
                            <option value="{{ $m->id }}">{{ $m->nama }} ({{ $m->kode }}) &bull; Kelas {{ $m->tingkat_label }}</option>
                        @endforeach
                    </select>
                </div>

                @if ($currentMapil)
                    <!-- Kartu Informasi Mapil Aktif -->
                    <div class="md:col-span-7 p-3.5 rounded-xl bg-primary/5 border border-primary/20 flex flex-wrap items-center justify-between gap-3 text-xs">
                        <div>
                            <div class="font-black text-sm text-text-main">{{ $currentMapil->nama }} ({{ $currentMapil->kode }})</div>
                            <div class="text-[11px] text-text-secondary">
                                Guru: <strong>{{ $currentMapil->guru_display_name }}</strong> &bull; Tingkat: <strong>{{ $currentMapil->tingkat_label }}</strong> &bull; KKM: <strong>{{ $currentMapil->kkm_default }}</strong>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs uppercase font-bold text-text-secondary block">Total Anggota</span>
                            <span class="text-lg font-black text-primary">{{ $currentMapil->anggotas()->count() }} Siswa</span>
                        </div>
                    </div>
                @endif
            </div>

            @if ($currentMapil)
                <!-- Toolbar Aksi Anggota (Tambah, Import, Export, Download Template) -->
                <div class="pt-3 border-t border-border flex flex-wrap items-center justify-between gap-2.5">
                    <div class="flex items-center gap-2">
                        @can('akademik.mapil.edit')
                            <button wire:click="openAddMemberModal" class="px-3.5 py-2 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition-all flex items-center gap-1.5">
                                <span>➕</span>
                                <span>Tambah Siswa Anggota</span>
                            </button>
                            <button wire:click="$set('showImportModal', true)" class="px-3 py-2 rounded-xl border border-border bg-surface hover:bg-surface-hover text-xs font-semibold text-text-main transition-colors flex items-center gap-1.5">
                                <span>📤</span>
                                <span>Import Excel</span>
                            </button>
                        @endcan
                        <button wire:click="downloadTemplate" class="px-3 py-2 rounded-xl border border-border bg-surface hover:bg-surface-hover text-xs font-semibold text-text-secondary transition-colors flex items-center gap-1.5">
                            <span>📄</span>
                            <span>Download Template</span>
                        </button>
                        <button wire:click="exportMembers" class="px-3 py-2 rounded-xl border border-border bg-surface hover:bg-surface-hover text-xs font-semibold text-text-secondary transition-colors flex items-center gap-1.5">
                            <span>📥</span>
                            <span>Export Excel</span>
                        </button>
                    </div>

                    <!-- Filter Pencarian Anggota -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <input type="text"
                            wire:model.live.debounce.300ms="searchAnggota"
                            placeholder="Cari nama atau NIS siswa..."
                            class="w-full sm:w-64 pl-3 pr-3 py-1.5 rounded-xl text-xs border border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                    </div>
                </div>
            @endif
        </div>

        @if ($currentMapil)
            <!-- Tabel Anggota Terdaftar -->
            <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-border text-xs border-collapse">
                        <thead>
                            <tr class="bg-surface-hover/80 text-left border-b border-border text-text-secondary uppercase text-[10px] font-bold tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-4">Nama Siswa</th>
                                <th class="py-3 px-4">NISN / NIS</th>
                                <th class="py-3 px-4">Kelas / Rombel</th>
                                <th class="py-3 px-4">Rayon</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border font-medium">
                            @forelse ($anggotas as $idx => $ang)
                                <tr class="hover:bg-surface-hover/40 transition-colors">
                                    <td class="py-3 px-4 text-center text-text-secondary font-mono">{{ $idx + 1 }}</td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-text-main text-xs">{{ $ang->siswa->nama ?? '-' }}</div>
                                        <div class="text-[10px] text-text-muted">{{ $ang->siswa->rombel->jurusan->nama ?? '-' }}</div>
                                    </td>
                                    <td class="py-3 px-4 font-mono text-[11px] text-text-secondary">
                                        {{ $ang->siswa->nisn ?? '-' }} / {{ $ang->siswa->nipd ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-semibold text-[11px]">
                                            {{ $ang->siswa->rombel->nama ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-text-secondary text-[11px]">
                                        {{ $ang->siswa->rayon->nama ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.2 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                            {{ ucfirst($ang->status ?? 'aktif') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @can('akademik.mapil.edit')
                                            <button type="button"
                                                wire:click="removeMember({{ $ang->id }})"
                                                wire:confirm="Yakin ingin mengeluarkan siswa '{{ $ang->siswa->nama }}' dari Mapel Pilihan ini?"
                                                class="px-2 py-1 rounded-lg border border-rose-200 bg-rose-50/50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/30 dark:text-rose-300 text-[11px] font-semibold transition-colors"
                                                title="Keluarkan dari Mapil">
                                                Keluarkan
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-text-secondary">
                                        Belum ada siswa yang didaftarkan ke dalam Mapel Pilihan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($anggotas && $anggotas->hasPages())
                    <div class="border-t border-border p-4 bg-surface-hover/30">
                        {{ $anggotas->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="bg-surface p-12 rounded-2xl border border-border text-center text-text-secondary">
                <span class="text-3xl block mb-2">🎯</span>
                <p class="font-bold text-text-main text-sm">Pilih Mapel Pilihan Terlebih Dahulu</p>
                <p class="text-xs mt-1">Silakan pilih Mapel Pilihan pada dropdown di atas untuk mengelola daftar siswa anggotanya.</p>
            </div>
        @endif
    @endif

    <!-- MODAL 1: FORM TAMBAH / EDIT MASTER MAPIL -->
    @if ($showMapilModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 overflow-y-auto">
            <div class="relative w-full max-w-xl bg-surface border border-border rounded-2xl shadow-2xl overflow-hidden my-8">
                <div class="flex items-center justify-between border-b border-border p-5 bg-surface-hover/50">
                    <div>
                        <h2 class="text-base font-black text-text-main tracking-tight">
                            {{ $mapilId ? 'Edit Mata Pelajaran Pilihan' : 'Tambah Mapel Pilihan Baru' }}
                        </h2>
                        <p class="text-xs text-text-secondary mt-0.5">Konfigurasikan kode, nama, tingkat sasaran, dan guru pengampu.</p>
                    </div>
                    <button wire:click="$set('showMapilModal', false)" class="text-text-muted hover:text-text-main text-xl font-bold cursor-pointer">&times;</button>
                </div>

                <form wire:submit="saveMapil" class="p-6 space-y-4 text-xs">
                    <!-- Kode & Nama -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-1">
                            <label class="block font-semibold text-text-secondary mb-1">Kode Mapil *</label>
                            <input type="text" wire:model="kode" placeholder="MPL-WEB" class="w-full text-xs font-mono uppercase rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                            @error('kode') <p class="text-rose-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-semibold text-text-secondary mb-1">Nama Mata Pelajaran Pilihan *</label>
                            <input type="text" wire:model="nama" placeholder="Contoh: Web Development" class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                            @error('nama') <p class="text-rose-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Pilihan Tingkat Sasaran (Checkbox Multi-Level) -->
                    <div>
                        <label class="block font-semibold text-text-secondary mb-1">Tingkat yang Berlaku * (Bisa 1 atau Lebih)</label>
                        <div class="flex items-center gap-3 p-3 rounded-xl border border-border bg-surface-hover/20">
                            @foreach (['X', 'XI', 'XII'] as $tgk)
                                <label class="flex items-center gap-2 cursor-pointer font-bold text-xs">
                                    <input type="checkbox" wire:model="tingkats" value="{{ $tgk }}" class="rounded border-border text-primary focus:ring-primary">
                                    <span>Kelas {{ $tgk }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('tingkats') <p class="text-rose-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- KKM & JP -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-text-secondary mb-1">KKM Default *</label>
                            <input type="number" wire:model="kkm_default" min="0" max="100" class="w-full text-xs font-mono rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                            @error('kkm_default') <p class="text-rose-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-text-secondary mb-1">Jam Pelajaran (JP) / Minggu *</label>
                            <input type="number" wire:model="jp" min="1" max="20" class="w-full text-xs font-mono rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                            @error('jp') <p class="text-rose-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Guru Pengampu & Pendamping -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-text-secondary mb-1">Guru Pengampu Utama</label>
                            <select wire:model="guru_id" class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                                <option value="">-- Pilih Guru --</option>
                                @foreach ($gurus as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-text-secondary mb-1">Guru Pendamping (Opsional)</label>
                            <select wire:model="guru_pendamping_id" class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary">
                                <option value="">-- Tanpa Pendamping --</option>
                                @foreach ($gurus as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Deskripsi Singkat -->
                    <div>
                        <label class="block font-semibold text-text-secondary mb-1">Deskripsi / Silabus Singkat</label>
                        <textarea wire:model="deskripsi" rows="2" placeholder="Gambaran materi dan kompetensi yang dipelajari..." class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary focus:border-primary"></textarea>
                    </div>

                    <!-- Checkbox Status Aktif -->
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer font-medium text-xs">
                            <input type="checkbox" wire:model="is_active" class="rounded border-border text-primary focus:ring-primary">
                            <span>Status Mapel Pilihan Aktif</span>
                        </label>
                    </div>

                    <!-- Footer Modal -->
                    <div class="flex justify-end gap-2 border-t border-border pt-4">
                        <button type="button" wire:click="$set('showMapilModal', false)" class="px-4 py-2 rounded-xl border border-border bg-surface hover:bg-surface-hover text-xs font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition-all">
                            Simpan Mapil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL 2: TAMBAH ANGGOTA SISWA MASSAL -->
    @if ($showAddMemberModal && $currentMapil)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 overflow-y-auto">
            <div class="relative w-full max-w-2xl bg-surface border border-border rounded-2xl shadow-2xl overflow-hidden my-8">
                <div class="flex items-center justify-between border-b border-border p-5 bg-surface-hover/50">
                    <div>
                        <h2 class="text-base font-black text-text-main tracking-tight">
                            Tambah Siswa Anggota: {{ $currentMapil->nama }}
                        </h2>
                        <p class="text-xs text-text-secondary mt-0.5">Pilih siswa calon anggota berdasarkan kelas, rayon, atau pencarian nama.</p>
                    </div>
                    <button wire:click="$set('showAddMemberModal', false)" class="text-text-muted hover:text-text-main text-xl font-bold cursor-pointer">&times;</button>
                </div>

                <div class="p-6 space-y-4 text-xs">
                    <!-- Filter Calon Siswa -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <div>
                            <label class="block text-[10px] font-bold text-text-secondary uppercase mb-1">Tingkat</label>
                            <select wire:model.live="candidateTingkat" class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary">
                                <option value="">Semua Tingkat</option>
                                @foreach ((array) ($currentMapil->tingkats ?: ['X', 'XI', 'XII']) as $t)
                                    <option value="{{ $t }}">Kelas {{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-text-secondary uppercase mb-1">Rombel / Kelas</label>
                            <select wire:model.live="candidateRombel" class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary">
                                <option value="">Semua Rombel</option>
                                @foreach ($rombels as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-text-secondary uppercase mb-1">Pencarian Nama</label>
                            <input type="text" wire:model.live.debounce.300ms="searchCandidate" placeholder="Nama / NISN..." class="w-full text-xs rounded-xl border-border bg-surface text-text-main focus:ring-primary">
                        </div>
                    </div>

                    <!-- Daftar Calon Siswa (Scrollable List) -->
                    <div class="border border-border rounded-xl overflow-hidden max-h-64 overflow-y-auto divide-y divide-border">
                        @forelse ($candidates as $cand)
                            <label class="flex items-center justify-between p-2.5 hover:bg-surface-hover cursor-pointer transition-colors">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" wire:model="selectedCandidateIds" value="{{ $cand->id }}" class="rounded border-border text-primary focus:ring-primary cursor-pointer">
                                    <div>
                                        <div class="font-bold text-text-main">{{ $cand->nama }}</div>
                                        <div class="text-[10px] text-text-secondary font-mono">NISN: {{ $cand->nisn ?: '-' }} &bull; {{ $cand->rombel->nama ?? 'Tanpa Rombel' }}</div>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-surface-hover text-text-secondary">
                                    {{ $cand->rayon->nama ?? '-' }}
                                </span>
                            </label>
                        @empty
                            <div class="p-6 text-center text-text-secondary text-xs">
                                Tidak ada siswa calon anggota yang cocok dengan filter.
                            </div>
                        @endforelse
                    </div>

                    <!-- Summary & Aksi Tambah -->
                    <div class="flex items-center justify-between border-t border-border pt-4">
                        <div class="text-xs text-text-secondary">
                            <strong class="text-primary">{{ count($selectedCandidateIds) }}</strong> siswa terpilih
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="$set('showAddMemberModal', false)" class="px-4 py-2 rounded-xl border border-border bg-surface hover:bg-surface-hover text-xs font-semibold">
                                Batal
                            </button>
                            <button type="button" wire:click="addSelectedCandidates" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition-all">
                                Tambahkan Siswa Terpilih
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 3: IMPORT EXCEL ANGGOTA -->
    @if ($showImportModal && $currentMapil)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 overflow-y-auto">
            <div class="relative w-full max-w-md bg-surface border border-border rounded-2xl shadow-2xl overflow-hidden my-8">
                <div class="flex items-center justify-between border-b border-border p-5 bg-surface-hover/50">
                    <div>
                        <h2 class="text-base font-black text-text-main tracking-tight">Import Anggota dari Excel</h2>
                        <p class="text-xs text-text-secondary mt-0.5">Unggah file Excel daftar anggota untuk Mapil {{ $currentMapil->nama }}.</p>
                    </div>
                    <button wire:click="$set('showImportModal', false)" class="text-text-muted hover:text-text-main text-xl font-bold cursor-pointer">&times;</button>
                </div>

                <form wire:submit="importMembers" class="p-6 space-y-4 text-xs">
                    <div class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 text-amber-900 dark:text-amber-200 text-[11px] leading-relaxed">
                        ⚠️ Pastikan menggunakan format template resmi. Klik tombol <strong>Download Template</strong> jika belum memilikinya.
                    </div>

                    <div>
                        <label class="block font-semibold text-text-secondary mb-1.5">Pilih File Excel (.xlsx, .xls)</label>
                        <input type="file" wire:model="importFile" accept=".xlsx,.xls" class="w-full text-xs rounded-xl border border-border bg-surface p-2 text-text-main">
                        @error('importFile') <p class="text-rose-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-2 border-t border-border pt-4">
                        <button type="button" wire:click="$set('showImportModal', false)" class="px-4 py-2 rounded-xl border border-border bg-surface hover:bg-surface-hover text-xs font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition-all" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="importMembers">Mulai Import</span>
                            <span wire:loading wire:target="importMembers">Mengimpor...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
