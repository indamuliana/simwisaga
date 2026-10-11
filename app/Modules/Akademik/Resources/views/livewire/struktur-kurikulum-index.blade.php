<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Struktur Kurikulum</h1>
            <p class="text-sm text-text-secondary">Pemetaan alokasi mata pelajaran dan beban Jam Pelajaran (JP) per jurusan, tingkat, dan semester.</p>
        </div>
        @can('akademik.kurikulum.tambah')
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="openDuplicateModal" class="btn btn-outline border-border hover:bg-surface-hover text-text-main flex items-center gap-1.5 shadow-xs">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                </svg>
                <span>Salin dari T.A. Lain</span>
            </button>
            <button wire:click="create" class="btn btn-outline border-border hover:bg-surface-hover text-text-main flex items-center gap-1.5 shadow-xs">
                <span>+ Tambah Satuan</span>
            </button>
            <button wire:click="openBatchModal" class="btn btn-primary flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <span class="font-semibold">Alokasi Mapel (Multi-Jurusan / Tingkat)</span>
            </button>
        </div>
        @endcan
    </div>

    <!-- Alert Notifications -->
    @if (session('status'))
        <div class="p-4 text-sm text-emerald-800 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 text-sm text-rose-800 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 rounded-xl border border-rose-200 dark:border-rose-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Selector Bar -->
    <div class="p-4 card grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Tahun Ajaran</label>
            <select wire:model.live="filterTahunAjaran" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Jurusan / Keahlian</label>
            <select wire:model.live="filterJurusan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                @foreach($jurusans as $j)
                    <option value="{{ $j->id }}">{{ $j->kode }} - {{ $j->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Tingkat Kelas</label>
            <select wire:model.live="filterTingkat" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                <option value="X">Kelas X</option>
                <option value="XI">Kelas XI</option>
                <option value="XII">Kelas XII</option>
            </select>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card p-0 overflow-hidden">
        <div class="px-6 py-4 bg-surface-hover/50 bg-surface/40 border-b border-border flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <span class="text-sm font-bold text-text-main">
                    Daftar Mata Pelajaran & Alokasi Beban Jam
                </span>
                <p class="text-xs text-text-secondary mt-0.5">Struktur kurikulum aktif untuk jurusan dan tingkat terpilih</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-primary dark:text-indigo-400 bg-primary-soft px-3 py-1 rounded-full border border-primary/20">
                    Smt Ganjil: <strong>{{ $totalJpGanjil }} JP</strong>
                </span>
                <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/50 px-3 py-1 rounded-full border border-emerald-200 dark:border-emerald-800">
                    Smt Genap: <strong>{{ $totalJpGenap }} JP</strong>
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-sm">
                <thead class="bg-surface-hover bg-surface/50 text-text-secondary font-medium">
                    <tr>
                        <th class="px-6 py-3 w-16 text-center">Urutan</th>
                        <th class="px-6 py-3">Kode</th>
                        <th class="px-6 py-3">Mata Pelajaran</th>
                        <th class="px-6 py-3">Kelompok</th>
                        <th class="px-4 py-3 text-center">Smt Ganjil</th>
                        <th class="px-4 py-3 text-center">Smt Genap</th>
                        <th class="px-6 py-3 text-center">Status Berlaku</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-text-main">
                    @forelse($items as $it)
                        <tr class="hover:bg-surface-hover/50 dark:hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 text-center font-mono font-bold text-text-secondary">{{ $it->urutan }}</td>
                            <td class="px-6 py-4 font-mono font-semibold text-primary dark:text-indigo-400">{{ $it->mapel?->kode }}</td>
                            <td class="px-6 py-4 font-medium text-text-main">{{ $it->mapel?->nama }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $kodeKelompok = $it->mapel?->kelompokMapel?->kode ?? '';
                                    $badgeColor = match(strtoupper($kodeKelompok)) {
                                        'A' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                        'B' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                        'C' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                        'D' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 border-purple-200 dark:border-purple-800',
                                        default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-300 dark:border-slate-700'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $badgeColor }}">
                                    @if($it->mapel?->kelompokMapel)
                                        <span class="font-bold mr-1">[{{ $it->mapel->kelompokMapel->kode }}]</span> {{ $it->mapel->kelompokMapel->nama }}
                                    @else
                                        {{ $it->mapel?->kelompok_label }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center font-bold text-text-main">
                                @if($it->jp_ganjil > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-primary-soft text-primary">
                                        {{ $it->jp_ganjil }} JP
                                    </span>
                                @else
                                    <span class="text-xs text-text-muted">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center font-bold text-text-main">
                                @if($it->jp_genap > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">
                                        {{ $it->jp_genap }} JP
                                    </span>
                                @else
                                    <span class="text-xs text-text-muted">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusBadge = match($it->semester_status) {
                                        'Sepanjang Tahun' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                        'Ganjil Saja' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                        'Genap Saja' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 border-purple-200 dark:border-purple-800',
                                        default => 'bg-slate-100 text-slate-700 border-slate-300'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusBadge }}">
                                    {{ $it->semester_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @can('akademik.kurikulum.edit')
                                    <button wire:click="edit({{ $it->id }})" class="text-xs font-medium text-primary hover:text-indigo-800 dark:text-indigo-400">Edit</button>
                                @endcan
                                @can('akademik.kurikulum.hapus')
                                    <button wire:click="delete({{ $it->id }})" wire:confirm="Hapus mapel {{ $it->mapel?->nama }} dari struktur kurikulum?" class="text-xs font-medium text-rose-600 hover:text-rose-800 dark:text-rose-400">Hapus</button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-text-secondary">Belum ada mata pelajaran yang dimasukkan untuk kurikulum ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL 1: ALOKASI CERDAS (MULTI-JURUSAN & MULTI-TINGKAT) -->
    @if($showBatchModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div class="w-full max-w-2xl bg-surface rounded-2xl shadow-xl border border-border overflow-hidden my-8">
            <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-surface-hover/30">
                <div>
                    <h2 class="text-lg font-bold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>Alokasi Cepat Mapel ke Kurikulum</span>
                    </h2>
                    <p class="text-xs text-text-secondary mt-0.5">Aktifkan 1 mata pelajaran ke beberapa jurusan dan tingkatan sekaligus dalam 1 kali simpan.</p>
                </div>
                <button wire:click="closeBatchModal" class="text-text-secondary hover:text-text-main">&times;</button>
            </div>

            <form wire:submit="saveBatch" class="p-6 space-y-6">
                <!-- Baris 1: Tahun Ajaran & Pilihan Mapel -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1">Tahun Ajaran</label>
                        <select wire:model="batch_tahun_ajaran_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                            @endforeach
                        </select>
                        @error('batch_tahun_ajaran_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-text-main mb-1">Pilih Mata Pelajaran</label>
                        <select wire:model="batch_mapel_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                            @foreach($allMapels as $m)
                                <option value="{{ $m->id }}">
                                    [{{ $m->kode }}] {{ $m->nama }} ({{ $m->kelompok_label }})
                                </option>
                            @endforeach
                        </select>
                        @error('batch_mapel_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Bagian 2: Ceklis Jurusan / Keahlian -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-semibold text-text-main">
                            Pilih Jurusan yang Mengambil Mapel Ini:
                        </label>
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="selectAllJurusans" class="text-xs text-primary hover:underline font-medium">Pilih Semua</button>
                            <span class="text-text-muted">|</span>
                            <button type="button" wire:click="deselectAllJurusans" class="text-xs text-text-secondary hover:underline">Batal Semua</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3 bg-surface-hover/30 rounded-xl border border-border">
                        @foreach($jurusans as $j)
                            <label class="inline-flex items-center gap-2.5 p-2 rounded-lg hover:bg-surface transition cursor-pointer border border-transparent hover:border-border">
                                <input type="checkbox" wire:model="selectedJurusans" value="{{ $j->id }}" class="rounded border-border text-primary focus:ring-primary">
                                <div class="min-w-0">
                                    <span class="text-xs font-bold text-text-main block truncate">{{ $j->kode }}</span>
                                    <span class="text-[10px] text-text-secondary block truncate">{{ $j->nama }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('selectedJurusans') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
                </div>

                <!-- Bagian 3: Alokasi Tingkat & Beban Jam (JP Ganjil & Genap) -->
                <div class="space-y-3">
                    <label class="text-xs font-semibold text-text-main block">
                        Atur Beban Jam Pelajaran (JP) per Tingkat Kelas:
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach(['X' => 'Kelas X', 'XI' => 'Kelas XI', 'XII' => 'Kelas XII'] as $t => $label)
                            <div class="p-4 rounded-xl border {{ !empty($tingkatSettings[$t]['active']) ? 'border-primary/50 bg-primary-soft/10 ring-1 ring-primary/20' : 'border-border bg-surface-hover/20 opacity-70' }} transition">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" wire:model.live="tingkatSettings.{{ $t }}.active" class="rounded border-border text-primary focus:ring-primary">
                                        <span class="text-sm font-bold text-text-main">{{ $label }}</span>
                                    </label>
                                </div>

                                <div class="space-y-3 {{ empty($tingkatSettings[$t]['active']) ? 'pointer-events-none opacity-40' : '' }}">
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-text-secondary mb-1">JP Ganjil</label>
                                            <input type="number" min="0" wire:model.live.debounce.300ms="tingkatSettings.{{ $t }}.jp_ganjil" class="w-full text-xs rounded-lg border-border bg-surface text-text-main text-center font-bold">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-text-secondary mb-1">JP Genap</label>
                                            <input type="number" min="0" wire:model.live="tingkatSettings.{{ $t }}.jp_genap" {{ !empty($tingkatSettings[$t]['samakan']) ? 'readonly' : '' }} class="w-full text-xs rounded-lg border-border bg-surface text-text-main text-center font-bold {{ !empty($tingkatSettings[$t]['samakan']) ? 'bg-surface-hover/60 cursor-not-allowed' : '' }}">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer text-[11px] text-text-secondary">
                                            <input type="checkbox" wire:model.live="tingkatSettings.{{ $t }}.samakan" wire:change="toggleSamakanJp('{{ $t }}')" class="rounded border-border text-primary focus:ring-primary w-3.5 h-3.5">
                                            <span>Samakan Genap dg Ganjil</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 flex justify-end space-x-3 border-t border-border">
                    <button type="button" wire:click="closeBatchModal" class="px-4 py-2 text-sm text-text-secondary hover:bg-surface-hover rounded-lg">Batal</button>
                    <button type="submit" class="btn btn-primary font-semibold">Simpan Alokasi Kurikulum</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL 2: EDIT SATUAN / FORM TAMBAH BIASA -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="w-full max-w-lg bg-surface rounded-2xl shadow-xl border border-border overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-surface-hover/30">
                <h2 class="text-lg font-bold text-text-main">
                    {{ $kurikulumId ? 'Edit Struktur Kurikulum Satuan' : 'Tambah Struktur Kurikulum Satuan' }}
                </h2>
                <button wire:click="$set('showModal', false)" class="text-text-secondary hover:text-text-main">&times;</button>
            </div>
            <form wire:submit="save" class="p-6 space-y-4">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1">Tahun Ajaran</label>
                        <select wire:model="tahun_ajaran_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                            @endforeach
                        </select>
                        @error('tahun_ajaran_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1">Jurusan</label>
                        <select wire:model="jurusan_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                            @foreach($jurusans as $j)
                                <option value="{{ $j->id }}">{{ $j->kode }}</option>
                            @endforeach
                        </select>
                        @error('jurusan_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1">Tingkat</label>
                        <select wire:model="tingkat" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                            <option value="X">Kelas X</option>
                            <option value="XI">Kelas XI</option>
                            <option value="XII">Kelas XII</option>
                        </select>
                        @error('tingkat') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Pilih Mata Pelajaran</label>
                    <select wire:model="mapel_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                        @foreach($allMapels as $m)
                            <option value="{{ $m->id }}">[{{ $m->kode }}] {{ $m->nama }} ({{ $m->kelompok_label }})</option>
                        @endforeach
                    </select>
                    @error('mapel_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1">JP Smt Ganjil</label>
                        <input type="number" min="0" wire:model.live.debounce.300ms="jp_ganjil" class="w-full text-sm rounded-lg border-border bg-surface text-text-main text-center font-bold">
                        @error('jp_ganjil') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1">JP Smt Genap</label>
                        <input type="number" min="0" wire:model="jp_genap" {{ $samakanJp ? 'readonly' : '' }} class="w-full text-sm rounded-lg border-border bg-surface text-text-main text-center font-bold {{ $samakanJp ? 'bg-surface-hover/60 cursor-not-allowed' : '' }}">
                        @error('jp_genap') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1">Nomor Urut</label>
                        <input type="number" min="1" wire:model="urutan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main text-center font-bold">
                        @error('urutan') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs text-text-secondary">
                        <input type="checkbox" wire:model.live="samakanJp" class="rounded border-border text-primary focus:ring-primary">
                        <span>Samakan JP Semester Genap dengan Ganjil</span>
                    </label>
                </div>

                <div class="pt-4 flex justify-end space-x-3 border-t border-border">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-sm text-text-secondary hover:bg-surface-hover rounded-lg">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kurikulum</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL 3: DUPLIKASI STRUKTUR KURIKULUM -->
    @if($showDuplicateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="w-full max-w-lg bg-surface rounded-2xl shadow-xl border border-border overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-surface-hover/30">
                <h2 class="text-lg font-bold text-text-main">
                    Salin Struktur Kurikulum
                </h2>
                <button wire:click="closeDuplicateModal" class="text-text-secondary hover:text-text-main">&times;</button>
            </div>
            <div class="p-6 space-y-4">
                <p class="text-sm text-text-secondary">
                    Salin seluruh pemetaan mata pelajaran dan alokasi JP dari tahun ajaran lain ke tahun ajaran yang sedang aktif atau dipilih.
                </p>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1">Tahun Ajaran Sumber (Asal)</label>
                        <select wire:model.live="sourceTahunAjaranId" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                            <option value="">-- Pilih T.A. Sumber --</option>
                            @foreach($tahunAjarans as $ta)
                                @if($ta->id !== $targetTahunAjaranId)
                                    <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1">Jurusan yang Disalin</label>
                        <select wire:model.live="duplicateJurusanId" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                            <option value="semua">Semua Jurusan</option>
                            @foreach($jurusans as $j)
                                <option value="{{ $j->id }}">{{ $j->kode }} - {{ $j->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1">Tahun Ajaran Tujuan (Penerima)</label>
                        <select wire:model="targetTahunAjaranId" disabled class="w-full text-sm rounded-lg border-border bg-surface-hover/50 text-text-main cursor-not-allowed">
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="p-3 bg-primary-soft rounded-lg text-xs text-primary font-medium">
                    Terdeteksi <strong>{{ $sourceKurikulumCount }}</strong> data kurikulum pada sumber yang siap disalin.
                </div>

                <div class="pt-4 flex justify-end space-x-3 border-t border-border">
                    <button type="button" wire:click="closeDuplicateModal" class="px-4 py-2 text-sm text-text-secondary hover:bg-surface-hover rounded-lg">Batal</button>
                    <button type="button" wire:click="executeDuplicate" class="btn btn-primary" {{ $sourceKurikulumCount === 0 ? 'disabled' : '' }}>
                        Eksekusi Salin
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
