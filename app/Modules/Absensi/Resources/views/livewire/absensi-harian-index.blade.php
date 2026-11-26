<div class="space-y-6"
     x-data="{
         rowClass(status) {
             if (!status) {
                 return 'bg-orange-100 dark:bg-orange-950/60';
             }
             if (status === 'Hadir') {
                 return 'bg-surface hover:bg-surface-hover/50';
             }
             if (['Sakit', 'Izin', 'Alpa'].includes(status)) {
                 return 'bg-red-100 dark:bg-red-900/40';
             }
             return 'bg-yellow-100 dark:bg-amber-950/40';
         }
     }">
    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Absensi Harian Siswa</h1>
            <p class="text-sm text-text-secondary">Pencatatan presensi kehadiran siswa harian dengan filter rombel, rayon, gender, dan pencarian cepat.</p>
        </div>

        @if($canInput && count($siswas) > 0)
            <div class="flex flex-wrap items-center gap-2.5">
                @if($isPastDate && ! $isEditingPastDate)
                    <button type="button"
                            wire:click="mulaiEditAbsen"
                            class="btn btn-primary text-xs sm:text-sm inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Edit Absen</span>
                    </button>
                @elseif($isEditable)
                    @if($isPastDate)
                        <button type="button"
                                wire:click="batalEditAbsen"
                                class="btn btn-secondary text-xs sm:text-sm inline-flex items-center gap-1.5">
                            <span>Batal Edit</span>
                        </button>
                    @endif
                    <button type="button"
                            wire:click="tandaiSemuaHadir"
                            class="btn btn-secondary text-xs sm:text-sm inline-flex items-center gap-1.5"
                            title="Hanya menandai siswa yang belum terisi status absensinya">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Tandai Semua Hadir</span>
                    </button>
                    <button type="button"
                            wire:click="save"
                            class="btn btn-primary text-xs sm:text-sm inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        <span>Simpan Absensi</span>
                    </button>
                @endif
            </div>
        @endif
    </div>

    {{-- Banner Mode Tanggal Lampau --}}
    @if($isPastDate)
        @if(! $isEditingPastDate)
            <div class="p-3.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 rounded-xl text-xs sm:text-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>
                        <strong>Mode Baca Saja (Tanggal Lampau)</strong> &mdash; Data absensi tanggal {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }} terkunci agar tidak berubah tidak sengaja.
                        @if($canInput)
                            Klik <strong>Edit Absen</strong> jika ingin mengubah absensi di hari ini.
                        @endif
                    </span>
                </div>
            </div>
        @else
            <div class="p-3.5 bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 text-blue-900 dark:text-blue-200 rounded-xl text-xs sm:text-sm flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>
                    <strong>Mode Edit Tanggal Lampau Aktif</strong> &mdash; Anda sedang mengubah absensi tanggal {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}. Klik <strong>Simpan Absensi</strong> untuk menyimpan atau <strong>Batal Edit</strong> untuk mengunci kembali.
                </span>
            </div>
        @endif
    @endif

    {{-- Cards Statistik --}}
    <div class="space-y-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 text-xs text-text-secondary px-1">
            <span class="font-semibold uppercase tracking-wider">
                {{ $hasFullAccess ? 'Statistik Kehadiran Sekolah (Seluruh Siswa)' : 'Statistik Kehadiran Siswa Binaan' }}
            </span>
            <div class="flex items-center gap-2.5">
                <span class="text-text-secondary font-medium">
                    Tanggal: {{ $tanggal ? \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') : '-' }}
                </span>
                <span class="text-border">&bull;</span>
                @if($statistik['belum_absen'] > 0)
                    <span class="text-amber-600 dark:text-amber-400 font-medium">&#9888;&#65039; {{ $statistik['belum_absen'] }} belum diabsen</span>
                @else
                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">&check; Seluruh siswa ({{ $statistik['total'] }}) sudah tercatat</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
            <div class="card p-3.5 bg-surface border border-border rounded-xl">
                <span class="text-[11px] font-semibold text-text-secondary block">Total Siswa</span>
                <span class="text-xl font-bold text-text-main mt-1 block">{{ $statistik['total'] }}</span>
            </div>

            <div class="card p-3.5 bg-surface border border-emerald-300 dark:border-emerald-800/80 rounded-xl" title="Hadir">
                <span class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 block">H</span>
                <span class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1 block">{{ $statistik['hadir'] }}</span>
            </div>

            <div class="card p-3.5 bg-surface border border-amber-300 dark:border-amber-800/80 rounded-xl" title="Sakit">
                <span class="text-[11px] font-semibold text-amber-700 dark:text-amber-400 block">S</span>
                <span class="text-xl font-bold text-amber-600 dark:text-amber-400 mt-1 block">{{ $statistik['sakit'] }}</span>
            </div>

            <div class="card p-3.5 bg-surface border border-blue-300 dark:border-blue-800/80 rounded-xl" title="Izin">
                <span class="text-[11px] font-semibold text-blue-700 dark:text-blue-400 block">I</span>
                <span class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-1 block">{{ $statistik['izin'] }}</span>
            </div>

            <div class="card p-3.5 bg-surface border border-rose-300 dark:border-rose-800/80 rounded-xl" title="Alpa">
                <span class="text-[11px] font-semibold text-rose-700 dark:text-rose-400 block">A</span>
                <span class="text-xl font-bold text-rose-600 dark:text-rose-400 mt-1 block">{{ $statistik['alpa'] }}</span>
            </div>

            <div class="card p-3.5 bg-surface border border-purple-300 dark:border-purple-800/80 rounded-xl" title="Dispen CBT PLH">
                <span class="text-[11px] font-semibold text-purple-700 dark:text-purple-400 block truncate">CBT PLH</span>
                <span class="text-xl font-bold text-purple-600 dark:text-purple-400 mt-1 block">{{ $statistik['dispen_cbt_plh'] }}</span>
            </div>

            <div class="card p-3.5 bg-surface border border-fuchsia-300 dark:border-fuchsia-800/80 rounded-xl" title="Dispen CBT KWH">
                <span class="text-[11px] font-semibold text-fuchsia-700 dark:text-fuchsia-400 block truncate">CBT KWH</span>
                <span class="text-xl font-bold text-fuchsia-600 dark:text-fuchsia-400 mt-1 block">{{ $statistik['dispen_cbt_kwh'] }}</span>
            </div>

            <div class="card p-3.5 bg-surface border border-indigo-300 dark:border-indigo-800/80 rounded-xl" title="Dispen Lainnya (Lomba, dll)">
                <span class="text-[11px] font-semibold text-indigo-700 dark:text-indigo-400 block truncate">Dispen</span>
                <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mt-1 block">{{ $statistik['dispen_lainnya'] }}</span>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card p-5 bg-surface border border-border space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- Tanggal --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">Tanggal</label>
                <input type="date" wire:model.live="tanggal" class="input w-full">
            </div>

            {{-- Rombel --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">Rombel</label>
                <select wire:model.live="rombel_id" class="input w-full">
                    <option value="">{{ $hasFullAccess ? 'Semua Rombel (Seluruh Siswa)' : 'Semua Rombel Binaan' }}</option>
                    @foreach($rombels as $rombel)
                        <option value="{{ $rombel->id }}">{{ $rombel->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Rayon --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">Rayon</label>
                <select wire:model.live="rayon_id" class="input w-full">
                    <option value="">{{ $hasFullAccess ? 'Semua Rayon' : 'Semua Rayon Binaan' }}</option>
                    @foreach($rayons as $rayon)
                        <option value="{{ $rayon->id }}">{{ $rayon->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Gender --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">Gender</label>
                <select wire:model.live="gender" class="input w-full">
                    <option value="">Semua Gender (L &amp; P)</option>
                    <option value="L">Laki-laki (L)</option>
                    <option value="P">Perempuan (P)</option>
                </select>
            </div>

            {{-- Pencarian Cepat --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">Cari Siswa</label>
                <div class="relative">
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Nama, NIS, atau kelas..."
                           class="input w-full pl-8">
                    <svg class="w-4 h-4 text-text-secondary absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 rounded-xl text-sm space-y-1 shadow-xs">
            <div class="font-semibold">Gagal menyimpan absensi:</div>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Warning Message --}}
    @if(session()->has('warning'))
        <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 rounded-xl text-sm font-medium flex items-center gap-2 shadow-xs">
            <svg class="w-5 h-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    {{-- Flash Message --}}
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl text-sm font-medium flex items-center gap-2 shadow-xs">
            <svg class="w-5 h-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    {{-- Tabel Absensi --}}
    @if(count($siswas) > 0)
        <div class="space-y-3">
            {{-- Status Filter Info Bar & Legenda Warna --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2 px-1 text-xs text-text-secondary">
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <span>
                        Menampilkan <span class="font-bold text-text-main">{{ count($siswas) }} siswa</span>
                        <span class="text-text-secondary/70">(Urut NIS/NIPD)</span>
                    </span>
                    @if($rombel_id || $gender || $rayon_id || $search)
                        <span>&bull; Filter aktif:</span>
                        @if($rombel_id)
                            <span class="font-semibold text-text-main">Rombel: {{ $rombels->firstWhere('id', (int) $rombel_id)?->nama }}</span>
                        @endif
                        @if($rayon_id)
                            <span>&bull; Rayon: <span class="font-semibold text-text-main">{{ $rayons->firstWhere('id', (int) $rayon_id)?->nama ?? '-' }}</span></span>
                        @endif
                        @if($gender)
                            <span>&bull; Gender: <span class="font-semibold text-text-main">{{ $gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span></span>
                        @endif
                        @if($search)
                            <span>&bull; Kata kunci: <span class="font-semibold text-text-main">"{{ $search }}"</span></span>
                        @endif
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex flex-wrap items-center gap-2 text-[11px]">
                        <span class="inline-flex items-center gap-1">
                            <span class="w-3 h-3 rounded border border-orange-300 bg-orange-100 dark:bg-orange-950/60"></span>
                            Belum absen
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span class="w-3 h-3 rounded border border-border bg-surface"></span>
                            H
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span class="w-3 h-3 rounded border border-rose-300 bg-red-100 dark:bg-red-900/40"></span>
                            S/I/A
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span class="w-3 h-3 rounded border border-amber-300 bg-yellow-100 dark:bg-amber-950/40"></span>
                            Dispen
                        </span>
                    </div>

                    @if($isEditable)
                        <span class="text-border hidden sm:inline">|</span>
                        <button type="button" wire:click="tandaiSemuaHadir" class="text-primary hover:underline font-semibold cursor-pointer">
                            &check; Tandai Hadir yang Belum Diisi
                        </button>
                    @endif
                </div>
            </div>

            <div class="bg-surface border border-border shadow-xs rounded-xl overflow-x-auto">
                <table class="w-full text-xs sm:text-sm text-left border-collapse">
                    <thead class="text-[11px] uppercase tracking-wider text-text-secondary bg-surface-hover/80 border-b border-border">
                        <tr>
                            <th class="px-2.5 py-3 w-10 text-center">No</th>
                            <th class="px-2.5 py-3 whitespace-nowrap">NIS</th>
                            <th class="px-2.5 py-3 min-w-[180px]">Nama</th>
                            <th class="px-2.5 py-3 whitespace-nowrap">Rombel</th>
                            <th class="px-2.5 py-3 whitespace-nowrap">Rayon</th>
                            <th class="px-2 py-3 text-center w-10">JK</th>
                            @foreach($statuses as $status)
                                <th class="px-1.5 py-3 text-center whitespace-nowrap" title="{{ $status->fullLabel() }}">
                                    {{ $status->label() }}
                                </th>
                            @endforeach
                            <th class="px-2.5 py-3 min-w-[190px]">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($siswas as $siswa)
                            @php
                                $currentStatus = $absensiData[$siswa->id]['status'] ?? null;
                            @endphp
                            <tr wire:key="absensi-siswa-{{ $siswa->id }}"
                                data-status="{{ $currentStatus ?? '' }}"
                                class="transition-colors"
                                :class="rowClass($wire.absensiData[{{ $siswa->id }}]?.status)">
                                <td class="px-2.5 py-2.5 text-center text-xs text-text-secondary font-mono">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-2.5 py-2.5 text-xs font-mono text-text-secondary whitespace-nowrap">
                                    {{ $siswa->nipd ?: ($siswa->nisn ?: '-') }}
                                </td>
                                <td class="px-2.5 py-2.5 font-semibold text-text-main">
                                    {{ $siswa->nama }}
                                </td>
                                <td class="px-2.5 py-2.5 text-xs text-text-main whitespace-nowrap">
                                    {{ $siswa->rombel?->nama ?? '-' }}
                                </td>
                                <td class="px-2.5 py-2.5 whitespace-nowrap">
                                    @if($siswa->rayon)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] bg-slate-100/90 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300">
                                            {{ $siswa->rayon->nama }}
                                        </span>
                                    @else
                                        <span class="text-xs text-text-secondary">-</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2.5 text-center">
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded text-[10px] font-bold {{ strtoupper((string) $siswa->jk) === 'L' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-950/60 dark:text-pink-300' }}">
                                        {{ strtoupper((string) $siswa->jk) === 'L' ? 'L' : 'P' }}
                                    </span>
                                </td>
                                @foreach($statuses as $status)
                                    <td class="px-1.5 py-2.5 text-center">
                                        <label class="inline-flex items-center justify-center w-full h-full py-1 {{ $isEditable ? 'cursor-pointer' : 'cursor-not-allowed' }}"
                                               title="{{ $status->fullLabel() }}">
                                            <input type="radio"
                                                   name="status_{{ $siswa->id }}"
                                                   wire:model="absensiData.{{ $siswa->id }}.status"
                                                   value="{{ $status->value }}"
                                                   @disabled(! $isEditable)
                                                   class="text-primary focus:ring-primary h-4 w-4 disabled:opacity-60 disabled:cursor-not-allowed">
                                        </label>
                                    </td>
                                @endforeach
                                <td class="px-2.5 py-2">
                                    <div class="flex items-center gap-1.5">
                                        <input type="text"
                                               wire:model="absensiData.{{ $siswa->id }}.keterangan"
                                               @disabled(! $isEditable)
                                               class="input w-full text-xs py-1 bg-surface/90 disabled:opacity-65 disabled:cursor-not-allowed"
                                               placeholder="{{ $isEditable ? 'Alasan sakit, izin, lomba...' : '-' }}">
                                        @if($isEditable)
                                            <button type="button"
                                                    x-show="!!($wire.absensiData[{{ $siswa->id }}]?.status)"
                                                    x-cloak
                                                    wire:click="resetStatus({{ $siswa->id }})"
                                                    class="p-1 rounded text-text-secondary hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition shrink-0"
                                                    title="Reset status siswa">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Tombol Aksi di Bawah --}}
            @if($canInput)
                <div class="flex justify-end gap-2.5 pt-2">
                    @if($isPastDate && ! $isEditingPastDate)
                        <button type="button" wire:click="mulaiEditAbsen" class="btn btn-primary px-6 inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>Edit Absen</span>
                        </button>
                    @elseif($isEditable)
                        @if($isPastDate)
                            <button type="button" wire:click="batalEditAbsen" class="btn btn-secondary px-5">
                                Batal Edit
                            </button>
                        @endif
                        <button type="button" wire:click="save" class="btn btn-primary px-6 inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            <span>Simpan Absensi Harian</span>
                        </button>
                    @endif
                </div>
            @endif
        </div>
    @else
        <div class="card p-12 text-center border border-border">
            <div class="text-4xl mb-3">&#128269;</div>
            <h3 class="text-base font-bold text-text-main">Tidak Ada Siswa Ditemukan</h3>
            <p class="text-xs text-text-secondary mt-1">
                @if(! $hasFullAccess)
                    Tidak ada siswa binaan yang cocok dengan filter yang dipilih, atau akun Anda belum ditugaskan sebagai Wali Kelas / Pembimbing Rayon.
                @else
                    Tidak ada data siswa yang cocok dengan kombinasi filter Rombel, Rayon, Gender, atau kata kunci pencarian yang dipilih.
                @endif
            </p>
        </div>
    @endif
</div>
