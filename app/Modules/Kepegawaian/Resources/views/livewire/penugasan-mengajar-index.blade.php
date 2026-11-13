<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Penugasan Mengajar Guru</h1>
            <p class="text-sm text-text-secondary">Distribusi mata pelajaran dan beban Jam Pelajaran (JP) mengajar tahunan maupun per semester.</p>
        </div>
        <div class="flex items-center gap-2">
            @can('kepegawaian.penugasan.cetak')
                <button wire:click="cetakRekap" class="btn btn-secondary flex items-center gap-1.5 text-xs">
                    <svg class="w-4 h-4 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Rekap Beban JP</span>
                </button>
            @endcan
            @can('kepegawaian.penugasan.tambah')
                <button wire:click="create" class="btn btn-primary flex items-center gap-1.5 text-xs">
                    <span>+</span> Tambah Penugasan
                </button>
            @endcan
        </div>
    </div>

    <!-- Alert Notifications -->
    @if (session('status'))
        <div class="p-4 text-sm text-emerald-700 bg-emerald-50 dark:bg-emerald-950/40 dark:text-emerald-300 rounded-xl border border-emerald-200 dark:border-emerald-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 text-sm text-rose-700 bg-rose-50 dark:bg-rose-950/40 dark:text-rose-300 rounded-xl border border-rose-200 dark:border-rose-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Selector / Filter Bar -->
    <div class="p-4 card grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Tahun Ajaran</label>
            <select wire:model.live="filterTahunAjaran" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
                <option value="">-- Semua Tahun Ajaran --</option>
                @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Semester</label>
            <select wire:model.live="filterSemester" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
                <option value="">-- Semua Semester --</option>
                <option value="ganjil">Semester Ganjil</option>
                <option value="genap">Semester Genap</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Filter Guru</label>
            <select wire:model.live="filterPegawai" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
                <option value="">-- Semua Guru --</option>
                @foreach($gurus as $g)
                    <option value="{{ $g->id }}">{{ $g->nama_gelar }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Filter Rombel</label>
            <select wire:model.live="filterRombel" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
                <option value="">-- Semua Rombel --</option>
                @foreach($rombels as $r)
                    <option value="{{ $r->id }}">{{ $r->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card p-0 overflow-hidden">
        <div class="px-6 py-4 bg-surface-hover/50 bg-surface/40 border-b border-border flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <span class="text-sm font-bold text-text-main">Daftar Alokasi Penugasan Mengajar</span>
                <span class="text-xs text-text-secondary ml-2">({{ $penugasans->count() }} alokasi terdaftar)</span>
            </div>
            <span class="text-xs font-semibold text-primary dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 px-3 py-1 rounded-full border border-indigo-200 dark:border-indigo-800">
                Total Alokasi: {{ $totalJp }} JP
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-xs">
                <thead class="bg-surface-hover/80 text-text-secondary font-medium">
                    <tr>
                        <th class="px-5 py-3">Guru Pengampu</th>
                        <th class="px-5 py-3">Mata Pelajaran</th>
                        <th class="px-5 py-3">Rombel & Jurusan</th>
                        <th class="px-5 py-3">Periode Semester</th>
                        <th class="px-5 py-3 text-center">Beban (JP)</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-text-main">
                    @forelse($penugasans as $p)
                        <tr class="hover:bg-surface-hover/40 transition">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-text-main">{{ $p->pegawai?->nama_gelar }}</div>
                                <div class="text-[11px] text-text-muted font-mono mt-0.5">NIP: {{ $p->pegawai?->nip ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-medium text-text-main">{{ $p->mapel?->nama }}</div>
                                <div class="text-[11px] text-primary dark:text-indigo-400 font-mono mt-0.5">
                                    {{ $p->mapel?->kode }} ({{ $p->mapel?->kelompok_label }})
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                        {{ $p->rombel?->nama }}
                                    </span>
                                    @if($p->rombel?->jurusan)
                                        <span class="text-[10px] text-text-muted font-medium">({{ $p->rombel->jurusan->kode }})</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-medium text-text-main">{{ $p->semester?->tahunAjaran?->nama }}</div>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium mt-0.5 {{ $p->semester?->semester === 'ganjil' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300' : 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300' }}">
                                    Semester {{ ucfirst($p->semester?->semester ?? '') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center font-bold text-text-main text-xs">
                                <span class="px-2 py-1 rounded bg-slate-100 dark:bg-slate-800 border border-border">
                                    {{ $p->jp }} JP
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2">
                                @can('kepegawaian.penugasan.edit')
                                    <button wire:click="edit({{ $p->id }})" class="text-xs font-semibold text-primary hover:text-indigo-800 dark:text-indigo-400">Edit</button>
                                @endcan
                                @can('kepegawaian.penugasan.hapus')
                                    <button wire:click="promptDelete({{ $p->id }})" class="text-xs font-semibold text-rose-600 hover:text-rose-800 dark:text-rose-400">Hapus</button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-text-muted">
                                Tidak ada penugasan mengajar yang cocok dengan filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form (Tambah Penugasan Multi-Rombel / Edit) -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
            <div class="w-full max-w-2xl bg-surface rounded-2xl shadow-2xl border border-border overflow-hidden my-8 max-h-[90vh] flex flex-col">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-surface-hover/30 shrink-0">
                    <div>
                        <h2 class="text-base font-bold text-text-main">
                            {{ $penugasanId ? 'Ubah Penugasan Mengajar' : 'Tambah Penugasan Mengajar (Tahunan & Multi-Kelas)' }}
                        </h2>
                        <p class="text-xs text-text-secondary mt-0.5">
                            {{ $penugasanId ? 'Sesuaikan beban jam atau rombel penugasan' : 'Pilih periode, guru pengampu, mapel, dan centang kelas yang diampu' }}
                        </p>
                    </div>
                    <button wire:click="$set('showModal', false)" class="text-text-muted hover:text-text-main text-lg leading-none p-1">&times;</button>
                </div>

                <!-- Modal Body -->
                <form wire:submit="save" class="p-6 space-y-5 overflow-y-auto flex-1">
                    @if($penugasanId)
                        <!-- Single Edit Form -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-text-main mb-1">Semester</label>
                                <select wire:model="edit_semester_id" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
                                    @foreach($semesters as $s)
                                        <option value="{{ $s->id }}">{{ $s->tahunAjaran?->nama }} - Semester {{ ucfirst($s->semester) }}</option>
                                    @endforeach
                                </select>
                                @error('edit_semester_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-text-main mb-1">Rombel Kelas</label>
                                <select wire:model="edit_rombel_id" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
                                    @foreach($rombels as $r)
                                        <option value="{{ $r->id }}">{{ $r->nama }} ({{ $r->jurusan?->kode }})</option>
                                    @endforeach
                                </select>
                                @error('edit_rombel_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-text-main mb-1">Guru Pengampu</label>
                                <select wire:model="pegawai_id" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
                                    @foreach($gurus as $g)
                                        <option value="{{ $g->id }}">{{ $g->nama_gelar }}</option>
                                    @endforeach
                                </select>
                                @error('pegawai_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-text-main mb-1">Mata Pelajaran</label>
                                <select wire:model="mapel_id" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
                                    @foreach($mapels as $m)
                                        <option value="{{ $m->id }}">[{{ $m->kode }}] {{ $m->nama }}</option>
                                    @endforeach
                                </select>
                                @error('mapel_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-text-main mb-1">Beban Jam (JP / Minggu)</label>
                            <input type="number" min="1" max="200" wire:model="edit_jp" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
                            @error('edit_jp') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    @else
                        <!-- Bulk / Multi-Rombel Create Form -->
                        <!-- 1. Periode Penugasan (Tahunan / Semester) -->
                        <div class="p-4 rounded-xl border border-border bg-surface-hover/20 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-text-main uppercase tracking-wider">1. Periode Penugasan</span>
                                <span class="text-[11px] text-text-muted">Biasanya dialokasikan 1 tahun penuh</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-text-secondary mb-1">Tahun Ajaran</label>
                                    <select wire:model.live="tahun_ajaran_id" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
                                        @foreach($tahunAjarans as $ta)
                                            <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
                                        @endforeach
                                    </select>
                                    @error('tahun_ajaran_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-text-secondary mb-1">Cakupan Semester</label>
                                    <div class="grid grid-cols-3 gap-1.5 pt-0.5">
                                        <label class="flex items-center justify-center p-2 rounded-lg border text-center cursor-pointer transition text-[11px] {{ $periode_mode === 'full_year' ? 'border-primary bg-primary/10 text-primary font-bold' : 'border-border bg-surface hover:bg-surface-hover text-text-secondary' }}">
                                            <input type="radio" wire:model.live="periode_mode" value="full_year" class="sr-only">
                                            <span>1 Tahun Penuh</span>
                                        </label>
                                        <label class="flex items-center justify-center p-2 rounded-lg border text-center cursor-pointer transition text-[11px] {{ $periode_mode === 'ganjil' ? 'border-primary bg-primary/10 text-primary font-bold' : 'border-border bg-surface hover:bg-surface-hover text-text-secondary' }}">
                                            <input type="radio" wire:model.live="periode_mode" value="ganjil" class="sr-only">
                                            <span>Ganjil Saja</span>
                                        </label>
                                        <label class="flex items-center justify-center p-2 rounded-lg border text-center cursor-pointer transition text-[11px] {{ $periode_mode === 'genap' ? 'border-primary bg-primary/10 text-primary font-bold' : 'border-border bg-surface hover:bg-surface-hover text-text-secondary' }}">
                                            <input type="radio" wire:model.live="periode_mode" value="genap" class="sr-only">
                                            <span>Genap Saja</span>
                                        </label>
                                    </div>
                                    @error('periode_mode') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- 2. Guru & Mata Pelajaran -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-text-main mb-1">2. Pilih Guru Pengampu</label>
                                <select wire:model="pegawai_id" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
                                    <option value="">-- Pilih Guru --</option>
                                    @foreach($gurus as $g)
                                        <option value="{{ $g->id }}">{{ $g->nama_gelar }} (NIP: {{ $g->nip ?? '-' }})</option>
                                    @endforeach
                                </select>
                                @error('pegawai_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-text-main mb-1">3. Pilih Mata Pelajaran</label>
                                <select wire:model.live="mapel_id" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    @foreach($mapels as $m)
                                        <option value="{{ $m->id }}">[{{ $m->kode }}] {{ $m->nama }} ({{ $m->kelompok_label }})</option>
                                    @endforeach
                                </select>
                                @error('mapel_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- 3. Pemilihan Rombel Kelas (Bisa Lebih dari 1 Beda Tingkat & Beda Jurusan) -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="block text-xs font-bold text-text-main uppercase tracking-wider">
                                        4. Pilih Rombel Kelas (Bisa Lebih dari 1 Kelas)
                                    </label>
                                    <p class="text-[11px] text-text-secondary">Pilih kelas lintas tingkat (X, XI, XII) dan jurusan sekaligus.</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold {{ count($selectedRombels) > 0 ? 'bg-primary/10 text-primary border border-primary/20' : 'bg-slate-100 text-text-muted border border-border' }}">
                                    Terpilih: {{ count($selectedRombels) }} Rombel
                                </span>
                            </div>
                            @error('selectedRombels') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror

                            <!-- Tingkat Groups -->
                            <div class="space-y-3">
                                @foreach($rombelsByTingkat as $tingkat => $rombelGroup)
                                    @php
                                        $rombelIdsInTingkat = $rombelGroup->pluck('id')->toArray();
                                        $allSelectedInTingkat = count(array_intersect($selectedRombels, $rombelIdsInTingkat)) === count($rombelIdsInTingkat) && count($rombelIdsInTingkat) > 0;
                                    @endphp
                                    <div class="p-3 rounded-xl border border-border bg-surface">
                                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-border/60">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-xs text-text-main">Tingkat {{ $tingkat }}</span>
                                                <span class="text-[10px] text-text-muted">({{ $rombelGroup->count() }} Kelas)</span>
                                            </div>
                                            <button type="button" wire:click="toggleTingkat('{{ $tingkat }}')" class="text-[11px] font-semibold text-primary hover:text-indigo-800 dark:text-indigo-400">
                                                {{ $allSelectedInTingkat ? 'Batal Pilih Semua' : '+ Pilih Semua Kelas ' . $tingkat }}
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                            @foreach($rombelGroup as $r)
                                                @php $isChecked = in_array($r->id, $selectedRombels); @endphp
                                                <label class="flex items-center gap-2 p-2 rounded-lg border cursor-pointer transition select-none {{ $isChecked ? 'border-primary bg-primary/5 text-primary' : 'border-border bg-surface-hover/30 hover:bg-surface-hover text-text-main' }}">
                                                    <input type="checkbox" wire:model.live="selectedRombels" value="{{ $r->id }}" class="rounded border-border text-primary focus:ring-primary w-3.5 h-3.5">
                                                    <div class="min-w-0 flex-1">
                                                        <div class="text-xs font-semibold truncate">{{ $r->nama }}</div>
                                                        @if($r->jurusan)
                                                            <div class="text-[9px] text-text-muted uppercase tracking-wider">{{ $r->jurusan->kode }}</div>
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 4. Pratinjau & Penyesuaian Beban JP per Rombel Terpilih -->
                        @if(count($selectedRombels) > 0)
                            <div class="p-4 rounded-xl border border-border bg-surface space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-text-main uppercase tracking-wider">
                                        5. Pratinjau Beban Jam (JP) per Rombel
                                    </span>
                                    <span class="text-[10px] text-text-muted">Otomatis dideteksi dari Struktur Kurikulum</span>
                                </div>
                                <div class="max-h-40 overflow-y-auto divide-y divide-border">
                                    @foreach($selectedRombels as $rId)
                                        @php $r = $rombels->firstWhere('id', $rId); @endphp
                                        @if($r)
                                            <div class="py-1.5 flex items-center justify-between text-xs">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-text-main">{{ $r->nama }}</span>
                                                    <span class="text-[10px] text-text-muted">({{ $r->jurusan?->nama ?? $r->jurusan?->kode }})</span>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-[11px] text-text-secondary">JP:</span>
                                                    <input type="number" min="1" max="200" wire:model="rombelJpMap.{{ $rId }}" class="w-16 text-center text-xs py-1 px-1.5 rounded-md border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
                                                    <span class="text-[11px] text-text-secondary">Jam/Mgg</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif

                    <!-- Keterangan Opsional -->
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1">Keterangan Tambahan (Opsional)</label>
                        <input type="text" wire:model="keterangan" placeholder="Misal: Teori / Praktik Kejuruan / Kelas Kolaborasi" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>

                    <!-- Modal Footer -->
                    <div class="pt-4 flex items-center justify-between border-t border-border shrink-0">
                        <div class="text-[11px] text-text-secondary">
                            @if(!$penugasanId && count($selectedRombels) > 0)
                                Total akan disimpan: <b class="text-text-main">{{ count($selectedRombels) }} Rombel &times; {{ $periode_mode === 'full_year' ? '2 Semester' : '1 Semester' }} = {{ count($selectedRombels) * ($periode_mode === 'full_year' ? 2 : 1) }} entri penugasan</b>.
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" wire:click="$set('showModal', false)" class="btn btn-secondary text-xs">Batal</button>
                            <button type="submit" class="btn btn-primary text-xs">
                                {{ $penugasanId ? 'Simpan Perubahan' : 'Simpan Penugasan' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Smart Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-md bg-surface rounded-2xl shadow-2xl border border-border p-6 space-y-4">
                <div class="flex items-center gap-3 text-rose-600">
                    <div class="w-10 h-10 rounded-full bg-rose-100 dark:bg-rose-950/60 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-text-main">Konfirmasi Hapus Penugasan</h3>
                        <p class="text-xs text-text-secondary">{{ $deleteTargetInfo }}</p>
                    </div>
                </div>

                <div class="text-xs text-text-secondary bg-surface-hover/40 p-3 rounded-lg border border-border leading-relaxed">
                    @if($partnerPenugasanId)
                        Penugasan ini terdeteksi memiliki alokasi tahunan (ada di Semester Ganjil & Genap). Anda dapat memilih untuk menghapus 1 tahun penuh atau hanya semester ini saja.
                    @else
                        Apakah Anda yakin ingin menghapus alokasi penugasan mengajar ini? Tindakan ini tidak dapat dibatalkan.
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-2 pt-2">
                    <button type="button" wire:click="$set('showDeleteModal', false)" class="btn btn-secondary text-xs">
                        Batal
                    </button>
                    @if($partnerPenugasanId)
                        <button type="button" wire:click="executeDelete('single')" class="btn bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 text-text-main text-xs">
                            Hanya Semester Ini
                        </button>
                        <button type="button" wire:click="executeDelete('full_year')" class="btn btn-danger text-xs">
                            Hapus 1 Tahun Penuh
                        </button>
                    @else
                        <button type="button" wire:click="executeDelete('single')" class="btn btn-danger text-xs">
                            Ya, Hapus Penugasan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
