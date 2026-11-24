<div class="space-y-6">
    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Absensi Mata Pelajaran</h1>
            <p class="text-sm text-text-secondary">Pencatatan presensi siswa per mata pelajaran dengan filter bertingkat (Rombel &rarr; Mapel) dan pencarian cepat.</p>
        </div>
        @if($penugasan_id && count($siswas) > 0)
            <div class="flex items-center gap-2.5">
                <button type="button" wire:click="tandaiSemuaHadir" class="btn btn-secondary text-xs sm:text-sm inline-flex items-center gap-1.5" title="Hanya menandai siswa yang belum terisi">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Tandai Semua Hadir</span>
                </button>
                <button type="button" wire:click="save" class="btn btn-primary text-xs sm:text-sm inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    <span>Simpan Absensi Mapel</span>
                </button>
            </div>
        @endif
    </div>

    {{-- Cards Statistik (Selalu Aktif) --}}
    <div class="space-y-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 text-xs text-text-secondary px-1">
            <span class="font-semibold uppercase tracking-wider">
                @if($penugasan)
                    Statistik Mapel: <span class="text-text-main font-bold">{{ $penugasan->mapel->nama }} ({{ $penugasan->rombel->nama }})</span>
                @else
                    Statistik Kehadiran Mata Pelajaran
                @endif
            </span>
            <div class="flex items-center gap-2.5">
                <span class="text-text-secondary font-medium">Tanggal: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</span>
                <span class="text-border">&bull;</span>
                @if($penugasan_id)
                    @if($statistik['belum_absen'] > 0)
                        <span class="text-amber-600 dark:text-amber-400 font-medium">⚠️ {{ $statistik['belum_absen'] }} belum diabsen</span>
                    @else
                        <span class="text-emerald-600 dark:text-emerald-400 font-medium">✓ Seluruh siswa ({{ $statistik['total'] }}) sudah tercatat</span>
                    @endif
                @else
                    <span class="text-slate-400 italic">Pilih rombel & mapel untuk memuat data</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
            {{-- Total Siswa --}}
            <div class="card p-3.5 bg-surface border border-border rounded-xl">
                <span class="text-[11px] font-semibold text-text-secondary block">Total Siswa</span>
                <span class="text-xl font-bold text-text-main mt-1 block">{{ $statistik['total'] }}</span>
            </div>

            {{-- Hadir --}}
            <div class="card p-3.5 bg-surface border border-emerald-300 dark:border-emerald-800/80 rounded-xl">
                <span class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 block">Hadir</span>
                <span class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1 block">{{ $statistik['hadir'] }}</span>
            </div>

            {{-- Sakit --}}
            <div class="card p-3.5 bg-surface border border-amber-300 dark:border-amber-800/80 rounded-xl">
                <span class="text-[11px] font-semibold text-amber-700 dark:text-amber-400 block">Sakit</span>
                <span class="text-xl font-bold text-amber-600 dark:text-amber-400 mt-1 block">{{ $statistik['sakit'] }}</span>
            </div>

            {{-- Izin --}}
            <div class="card p-3.5 bg-surface border border-blue-300 dark:border-blue-800/80 rounded-xl">
                <span class="text-[11px] font-semibold text-blue-700 dark:text-blue-400 block">Izin</span>
                <span class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-1 block">{{ $statistik['izin'] }}</span>
            </div>

            {{-- Alpa (Bolos) --}}
            <div class="card p-3.5 bg-surface border border-rose-300 dark:border-rose-800/80 rounded-xl">
                <span class="text-[11px] font-semibold text-rose-700 dark:text-rose-400 block">Alpa (Bolos)</span>
                <span class="text-xl font-bold text-rose-600 dark:text-rose-400 mt-1 block">{{ $statistik['alpa'] }}</span>
            </div>

            {{-- Dispen CBT PLH --}}
            <div class="card p-3.5 bg-surface border border-purple-300 dark:border-purple-800/80 rounded-xl">
                <span class="text-[11px] font-semibold text-purple-700 dark:text-purple-400 block truncate" title="Dispen CBT PLH">CBT PLH</span>
                <span class="text-xl font-bold text-purple-600 dark:text-purple-400 mt-1 block">{{ $statistik['dispen_cbt_plh'] }}</span>
            </div>

            {{-- Dispen CBT KWH --}}
            <div class="card p-3.5 bg-surface border border-fuchsia-300 dark:border-fuchsia-800/80 rounded-xl">
                <span class="text-[11px] font-semibold text-fuchsia-700 dark:text-fuchsia-400 block truncate" title="Dispen CBT KWH">CBT KWH</span>
                <span class="text-xl font-bold text-fuchsia-600 dark:text-fuchsia-400 mt-1 block">{{ $statistik['dispen_cbt_kwh'] }}</span>
            </div>

            {{-- Dispen Lainnya --}}
            <div class="card p-3.5 bg-surface border border-indigo-300 dark:border-indigo-800/80 rounded-xl">
                <span class="text-[11px] font-semibold text-indigo-700 dark:text-indigo-400 block truncate" title="Dispen Lainnya (Lomba, dll)">Dispen Lain</span>
                <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mt-1 block">{{ $statistik['dispen_lainnya'] }}</span>
            </div>
        </div>
    </div>

    {{-- Filter Bar Bertingkat & Pencarian Cepat --}}
    <div class="card p-5 bg-surface border border-border space-y-4">
        {{-- Baris 1: Filter Utama (Tanggal -> Rombel -> Mapel) --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Tanggal --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">Tanggal</label>
                <input type="date" wire:model.live="tanggal" class="input w-full">
            </div>

            {{-- 1. Pilih Kelas / Rombel --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">
                    1. Kelas / Rombel <span class="text-rose-500">*</span>
                </label>
                <select wire:model.live="rombel_id" class="input w-full font-medium">
                    <option value="">-- Pilih Kelas / Rombel --</option>
                    @foreach($this->availableRombels as $rombel)
                        <option value="{{ $rombel->id }}">{{ $rombel->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 2. Pilih Mata Pelajaran --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">
                    2. Mata Pelajaran <span class="text-rose-500">*</span>
                </label>
                <select wire:model.live="penugasan_id" class="input w-full font-medium" @disabled(!$rombel_id)>
                    @if(!$rombel_id)
                        <option value="">-- Pilih Rombel Dahulu --</option>
                    @elseif($this->availablePenugasans->isEmpty())
                        <option value="">(Tidak ada penugasan di rombel ini)</option>
                    @else
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($this->availablePenugasans as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->mapel->nama }} @if($p->pegawai) ({{ $p->pegawai->nama_lengkap }}) @endif
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        {{-- Baris 2: Filter Tambahan & Pencarian Cepat --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-3 border-t border-border/60">
            {{-- Rayon --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">Rayon</label>
                <select wire:model.live="rayon_id" class="input w-full" @disabled(!$penugasan_id)>
                    <option value="">Semua Rayon</option>
                    @foreach($rayons as $rayon)
                        <option value="{{ $rayon->id }}">{{ $rayon->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Gender --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">Gender</label>
                <select wire:model.live="gender" class="input w-full" @disabled(!$penugasan_id)>
                    <option value="">Semua Gender (L & P)</option>
                    <option value="L">Laki-laki (L)</option>
                    <option value="P">Perempuan (P)</option>
                </select>
            </div>

            {{-- Pencarian Cepat Siswa --}}
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5 uppercase tracking-wider">Cari Siswa</label>
                <div class="relative">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Ketik nama atau NIS siswa..." 
                           class="input w-full pl-8"
                           @disabled(!$penugasan_id)>
                    <svg class="w-4 h-4 text-text-secondary absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash Message --}}
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl text-sm font-medium flex items-center gap-2 shadow-xs">
            <svg class="w-5 h-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    {{-- Tabel Absensi Mapel --}}
    @if(count($siswas) > 0)
        <div class="space-y-3">
            {{-- Status Filter Info Bar --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-1 text-xs text-text-secondary">
                <div>
                    Menampilkan <span class="font-bold text-text-main">{{ count($siswas) }} siswa</span>
                    <span class="text-text-secondary/70">({{ $penugasan?->rombel?->nama }} &bull; {{ $penugasan?->mapel?->nama }})</span>
                    @if($gender || $rayon_id || $search)
                        &bull; Filter aktif:
                        @if($rayon_id)
                            Rayon: <span class="font-semibold text-text-main">{{ $rayons->firstWhere('id', $rayon_id)?->nama ?? '-' }}</span>
                        @endif
                        @if($rayon_id && $gender) &bull; @endif
                        @if($gender)
                            Gender: <span class="font-semibold text-text-main">{{ $gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                        @endif
                        @if(($rayon_id || $gender) && $search) &bull; @endif
                        @if($search)
                            Kata kunci: <span class="font-semibold text-text-main">"{{ $search }}"</span>
                        @endif
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" wire:click="tandaiSemuaHadir" class="text-primary hover:underline font-semibold cursor-pointer">
                        &check; Tandai Hadir Siswa yang Belum Diisi
                    </button>
                </div>
            </div>

            <div class="bg-surface border border-border shadow-xs rounded-xl overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-[11px] uppercase tracking-wider text-text-secondary bg-surface-hover/80 border-b border-border">
                        <tr>
                            <th class="px-4 py-3 w-12 text-center">No</th>
                            <th class="px-4 py-3 min-w-[200px]">Identitas Siswa</th>
                            <th class="px-4 py-3 min-w-[150px]">Status Harian</th>
                            <th class="px-4 py-3 min-w-[500px]">Kehadiran Mapel</th>
                            <th class="px-4 py-3 min-w-[200px]">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($siswas as $siswa)
                            @php
                                $harianStatus = $absensiHarianStatus[$siswa->id] ?? null;
                                $enumHarian = $harianStatus ? \App\Enums\StatusAbsensi::tryFrom($harianStatus) : null;
                                $currentStatus = $absensiMapelData[$siswa->id]['status'] ?? null;
                            @endphp
                            <tr class="hover:bg-surface-hover/50 transition">
                                <td class="px-4 py-3 text-center text-xs text-text-secondary font-mono">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-text-main">{{ $siswa->nama }}</div>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span class="text-xs font-mono text-text-secondary font-medium">{{ $siswa->nipd ?: ($siswa->nisn ?: '-') }}</span>
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold {{ strtoupper($siswa->jk) === 'L' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-950/50 dark:text-pink-300' }}">
                                            {{ strtoupper($siswa->jk) === 'L' ? 'L' : 'P' }}
                                        </span>
                                        @if($siswa->rayon)
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                                {{ $siswa->rayon->nama }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($enumHarian)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $enumHarian->color() }}">
                                            {{ $enumHarian->label() }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Belum ada absen harian</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($statuses as $status)
                                            <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium cursor-pointer transition border {{ $currentStatus === $status->value ? 'border-primary bg-primary/10 text-primary font-bold ring-1 ring-primary' : 'border-border bg-surface text-text-secondary hover:bg-surface-hover' }}">
                                                <input type="radio" 
                                                       wire:model="absensiMapelData.{{ $siswa->id }}.status" 
                                                       value="{{ $status->value }}" 
                                                       class="text-primary focus:ring-primary h-3.5 w-3.5">
                                                <span>{{ $status->label() }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" 
                                           wire:model="absensiMapelData.{{ $siswa->id }}.keterangan" 
                                           class="input w-full text-xs py-1.5" 
                                           placeholder="Keterangan opsional...">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Tombol Simpan di Bawah --}}
            <div class="flex justify-end pt-2">
                <button type="button" wire:click="save" class="btn btn-primary px-6 inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    <span>Simpan Absensi Mapel</span>
                </button>
            </div>
        </div>
    @elseif(!$rombel_id)
        <div class="card p-12 text-center border border-border">
            <div class="text-4xl mb-3">&#127979;</div>
            <h3 class="text-base font-bold text-text-main">Pilih Kelas / Rombel Terlebih Dahulu</h3>
            <p class="text-xs text-text-secondary mt-1">
                Silakan pilih kelas / rombel pada langkah 1 di atas untuk melihat mata pelajaran yang diajarkan.
            </p>
        </div>
    @elseif($this->availablePenugasans->isEmpty())
        <div class="card p-12 text-center border border-amber-300 dark:border-amber-800/80 bg-amber-50/40 dark:bg-amber-950/20">
            <div class="text-4xl mb-3">&#9888;&#65039;</div>
            <h3 class="text-base font-bold text-amber-900 dark:text-amber-300">Belum Ada Penugasan Mengajar di Kelas Ini</h3>
            <p class="text-xs text-amber-700 dark:text-amber-400 mt-1 max-w-md mx-auto">
                Kelas ini belum memiliki data penugasan mata pelajaran & guru pada semester aktif. Anda dapat menambahkan penugasan mengajar melalui menu <strong>Akademik &rarr; Penugasan Mengajar</strong>.
            </p>
        </div>
    @elseif(!$penugasan_id)
        <div class="card p-12 text-center border border-border">
            <div class="text-4xl mb-3">&#128218;</div>
            <h3 class="text-base font-bold text-text-main">Pilih Mata Pelajaran</h3>
            <p class="text-xs text-text-secondary mt-1">
                Silakan pilih mata pelajaran pada langkah 2 untuk memuat daftar siswa kelas ini.
            </p>
        </div>
    @else
        <div class="card p-12 text-center border border-border">
            <div class="text-4xl mb-3">&#128269;</div>
            <h3 class="text-base font-bold text-text-main">Tidak Ada Siswa Ditemukan</h3>
            <p class="text-xs text-text-secondary mt-1">
                Tidak ada data siswa yang cocok dengan kombinasi filter atau kata kunci pencarian yang dipilih.
            </p>
        </div>
    @endif
</div>
