<div class="space-y-6">
    <!-- Header Navigasi Sub-Modul Kepesantrenan -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-border pb-4">
        <div>
            <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
                <svg class="w-7 h-7 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Halaqoh & Presensi Santri
            </h2>
            <p class="text-sm text-text-secondary mt-1">
                Pengelompokan santri unggulan (Halaqoh A, B, C, dst.), penunjukan pengajar halaqoh, dan presensi mandiri tanggal bebas.
            </p>
        </div>

        <!-- Tab Navigasi Submodul -->
        <div class="flex items-center gap-1.5 bg-surface-hover p-1.5 rounded-xl border border-border overflow-x-auto max-w-full">
            <a href="{{ route('pesantren.kamar') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface whitespace-nowrap">
                Kamar & Asrama
            </a>
            <a href="{{ route('pesantren.hafalan') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface whitespace-nowrap">
                Input Hafalan
            </a>
            <a href="{{ route('pesantren.rekap') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface whitespace-nowrap">
                Rekap & Rank MLBB
            </a>
            <a href="{{ route('pesantren.halaqoh') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold bg-white dark:bg-gray-800 text-teal-600 dark:text-teal-400 shadow-xs whitespace-nowrap">
                Halaqoh & Presensi
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    <!-- 3 Sub-Tab: Presensi Halaqoh vs Kelompok Halaqoh vs Rekap Presensi -->
    <div class="flex flex-wrap items-center gap-2 border-b border-border pb-1">
        <button type="button" wire:click="$set('activeTab', 'presensi')"
                class="px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2
                {{ $activeTab === 'presensi' ? 'bg-teal-600 text-white shadow-md' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
            <span>📋</span>
            <span>1. Presensi Harian Halaqoh</span>
        </button>

        <button type="button" wire:click="$set('activeTab', 'kelompok')"
                class="px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2
                {{ $activeTab === 'kelompok' ? 'bg-teal-600 text-white shadow-md' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
            <span>👥</span>
            <span>2. Kelompok & Plotting Halaqoh</span>
        </button>

        <button type="button" wire:click="$set('activeTab', 'rekap')"
                class="px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2
                {{ $activeTab === 'rekap' ? 'bg-teal-600 text-white shadow-md' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
            <span>📊</span>
            <span>3. Rekap Presensi Halaqoh</span>
        </button>
    </div>

    <!-- ==================== SUB-TAB 1: PRESENSI HALAQOH ==================== -->
    @if($activeTab === 'presensi')
        <div class="space-y-6">
            <!-- Toolbar Pengaturan Sesi Presensi -->
            <div class="bg-surface p-4 sm:p-5 rounded-2xl border border-border shadow-xs space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <div class="sm:col-span-4">
                        <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1">Pilih Kelompok Halaqoh</label>
                        <select wire:model.live="selectedHalaqohId" class="select w-full text-sm font-semibold">
                            <option value="">-- Pilih Halaqoh --</option>
                            @foreach($halaqohs as $h)
                                <option value="{{ $h->id }}">{{ $h->nama_halaqoh }} ({{ $h->pengajar?->nama_lengkap ?? 'Tanpa Pengajar' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1">Tanggal Presensi (Bebas)</label>
                        <input type="date" wire:model.live="presensiTanggal" class="input w-full text-sm">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1">Waktu / Sesi</label>
                        <select wire:model.live="presensiWaktuSesi" class="select w-full text-sm">
                            <option value="subuh">Ba'da Subuh</option>
                            <option value="ashar">Ba'da Ashar</option>
                            <option value="maghrib">Ba'da Maghrib</option>
                            <option value="isya">Ba'da Isya</option>
                            <option value="kegiatan">Kegiatan Tambahan</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <button type="button" wire:click="setAllStatus('H')" class="btn btn-secondary text-xs w-full py-2.5">
                            Set Semua Hadir
                        </button>
                    </div>
                </div>

                @if($selectedHalaqoh)
                    <div class="pt-3 border-t border-border flex flex-wrap items-center justify-between gap-3 text-xs">
                        <div class="text-text-secondary">
                            Pengajar: <strong class="text-text-main">{{ $selectedHalaqoh->pengajar?->nama_lengkap ?? '-' }}</strong> |
                            Total Santri: <strong class="text-text-main">{{ $selectedHalaqoh->siswas->count() }} Orang</strong>
                        </div>
                        <div>
                            <input type="text" wire:model="presensiCatatan" placeholder="Catatan kegiatan sesi ini (opsional)..." class="input text-xs w-full sm:w-72">
                        </div>
                    </div>
                @endif
            </div>

            <!-- Tabel Presensi Santri -->
            @if($selectedHalaqoh)
                <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-surface-hover/80 text-text-secondary border-b border-border font-bold">
                                <tr>
                                    <th class="p-3.5 w-12 text-center">No</th>
                                    <th class="p-3.5">Nama Santri</th>
                                    <th class="p-3.5">Rombel</th>
                                    <th class="p-3.5 text-center">Status Kehadiran</th>
                                    <th class="p-3.5">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @forelse($selectedHalaqoh->siswas as $idx => $siswa)
                                    @php
                                        $currentStatus = $attendanceMap[$siswa->id] ?? 'H';
                                    @endphp
                                    <tr class="hover:bg-surface-hover/50">
                                        <td class="p-3.5 text-center text-text-muted font-bold">{{ $idx + 1 }}</td>
                                        <td class="p-3.5 font-bold text-text-main">
                                            {{ $siswa->nama }}
                                            <span class="text-text-muted text-[11px] block">NISN: {{ $siswa->nisn ?? '-' }}</span>
                                        </td>
                                        <td class="p-3.5 text-text-secondary">{{ $siswa->rombel?->nama ?? '-' }}</td>

                                        <!-- Tombol Pilihan Status H / S / I / A -->
                                        <td class="p-3.5 text-center">
                                            <div class="inline-flex rounded-lg border border-border p-0.5 bg-surface-hover">
                                                <button type="button" wire:click="$set('attendanceMap.{{ $siswa->id }}', 'H')"
                                                        class="px-3 py-1 text-xs font-black rounded transition-all {{ $currentStatus === 'H' ? 'bg-emerald-600 text-white shadow-xs' : 'text-text-muted hover:text-text-main' }}">
                                                    Hadir (H)
                                                </button>
                                                <button type="button" wire:click="$set('attendanceMap.{{ $siswa->id }}', 'S')"
                                                        class="px-3 py-1 text-xs font-black rounded transition-all {{ $currentStatus === 'S' ? 'bg-amber-500 text-white shadow-xs' : 'text-text-muted hover:text-text-main' }}">
                                                    Sakit (S)
                                                </button>
                                                <button type="button" wire:click="$set('attendanceMap.{{ $siswa->id }}', 'I')"
                                                        class="px-3 py-1 text-xs font-black rounded transition-all {{ $currentStatus === 'I' ? 'bg-blue-600 text-white shadow-xs' : 'text-text-muted hover:text-text-main' }}">
                                                    Izin (I)
                                                </button>
                                                <button type="button" wire:click="$set('attendanceMap.{{ $siswa->id }}', 'A')"
                                                        class="px-3 py-1 text-xs font-black rounded transition-all {{ $currentStatus === 'A' ? 'bg-rose-600 text-white shadow-xs' : 'text-text-muted hover:text-text-main' }}">
                                                    Alfa (A)
                                                </button>
                                            </div>
                                        </td>

                                        <td class="p-3.5">
                                            <input type="text" wire:model="attendanceNotes.{{ $siswa->id }}" placeholder="Alasan jika S/I/A..." class="input text-xs w-full py-1">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-text-muted">
                                            Halaqoh ini belum memiliki santri. Buka tab "Kelompok & Plotting Halaqoh" untuk memasukkan santri.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($selectedHalaqoh->siswas->isNotEmpty())
                        <div class="p-4 bg-surface-hover/50 border-t border-border flex justify-end">
                            <button type="button" wire:click="savePresensi" class="btn btn-primary text-sm px-6 py-2.5 bg-teal-600 hover:bg-teal-700 flex items-center gap-2 shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Simpan Presensi Halaqoh</span>
                            </button>
                        </div>
                    @endif
                </div>
            @else
                <div class="p-12 text-center text-text-muted bg-surface rounded-2xl border border-dashed border-border">
                    Pilih salah satu kelompok halaqoh di atas untuk mengisi presensi.
                </div>
            @endif
        </div>
    @endif

    <!-- ==================== SUB-TAB 2: KELOMPOK & PLOTTING HALAQOH ==================== -->
    @if($activeTab === 'kelompok')
        <div class="space-y-6">
            <div class="flex justify-between items-center bg-surface p-4 rounded-xl border border-border">
                <div>
                    <h3 class="text-sm font-bold text-text-main">Daftar Kelompok Halaqoh</h3>
                    <p class="text-xs text-text-secondary">Kelompok pembinaan tahfiz Al-Qur'an santri unggulan.</p>
                </div>

                @can('pesantren.halaqoh.kelola')
                    <button wire:click="createHalaqoh" class="btn btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5 bg-teal-600 hover:bg-teal-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Halaqoh</span>
                    </button>
                @endcan
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($halaqohs as $h)
                    <div class="bg-surface rounded-2xl border border-border shadow-xs p-5 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase {{ $h->jenis_kelamin === 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                        {{ $h->jenis_kelamin === 'L' ? 'Ikhwan (Putra)' : 'Akhwat (Putri)' }}
                                    </span>
                                    <h4 class="text-lg font-bold text-text-main mt-1">{{ $h->nama_halaqoh }}</h4>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300">
                                    {{ $h->halaqoh_siswas_count }} Santri
                                </span>
                            </div>

                            <div class="text-xs space-y-1 pt-2 border-t border-border">
                                <div class="text-text-secondary flex justify-between">
                                    <span class="text-text-muted">Pengajar:</span>
                                    <span class="font-semibold text-text-main">{{ $h->pengajar?->nama_lengkap ?? 'Belum ada' }}</span>
                                </div>
                                @if($h->keterangan)
                                    <p class="text-text-muted italic text-[11px]">{{ $h->keterangan }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="pt-4 border-t border-border mt-4 flex items-center justify-between">
                            <button wire:click="openPlotting({{ $h->id }})" class="btn btn-secondary text-xs py-1.5 px-3">
                                Plotting Santri ({{ $h->halaqoh_siswas_count }})
                            </button>

                            @can('pesantren.halaqoh.kelola')
                                <div class="flex items-center gap-1">
                                    <button wire:click="editHalaqoh({{ $h->id }})" class="p-1 text-text-muted hover:text-primary rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="deleteHalaqoh({{ $h->id }})" wire:confirm="Hapus halaqoh ini?" class="p-1 text-text-muted hover:text-rose-600 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            @endcan
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-10 text-center text-text-muted bg-surface rounded-2xl border border-dashed border-border">
                        Belum ada kelompok halaqoh. Silakan klik "Tambah Halaqoh".
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- ==================== SUB-TAB 3: REKAP PRESENSI HALAQOH ==================== -->
    @if($activeTab === 'rekap')
        <div class="space-y-6">
            <!-- Filter Bar Rentang Tanggal, Halaqoh, Sesi & Preset Cepat -->
            <div class="bg-surface p-4 sm:p-5 rounded-2xl border border-border shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-border">
                    <div>
                        <h3 class="text-sm font-bold text-text-main flex items-center gap-2">
                            <span>📊</span>
                            <span>Filter Rekapitulasi Presensi Halaqoh</span>
                        </h3>
                        <p class="text-xs text-text-secondary">Pilih rentang tanggal mulai dan selesai untuk memfilter kehadiran santri.</p>
                    </div>

                    <!-- Quick Preset Tanggal -->
                    <div class="flex flex-wrap items-center gap-1.5 text-xs">
                        <span class="text-text-muted text-[11px] font-bold uppercase mr-1">Preset:</span>
                        <button type="button" wire:click="setDatePreset('today')" class="px-2.5 py-1 rounded-lg bg-surface-hover hover:bg-teal-50 dark:hover:bg-teal-950/40 hover:text-teal-600 font-semibold border border-border">
                            Hari Ini
                        </button>
                        <button type="button" wire:click="setDatePreset('week')" class="px-2.5 py-1 rounded-lg bg-surface-hover hover:bg-teal-50 dark:hover:bg-teal-950/40 hover:text-teal-600 font-semibold border border-border">
                            7 Hari
                        </button>
                        <button type="button" wire:click="setDatePreset('month')" class="px-2.5 py-1 rounded-lg bg-surface-hover hover:bg-teal-50 dark:hover:bg-teal-950/40 hover:text-teal-600 font-semibold border border-border">
                            Bulan Ini
                        </button>
                        <button type="button" wire:click="setDatePreset('last_month')" class="px-2.5 py-1 rounded-lg bg-surface-hover hover:bg-teal-50 dark:hover:bg-teal-950/40 hover:text-teal-600 font-semibold border border-border">
                            Bulan Lalu
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1">Tanggal Mulai</label>
                        <input type="date" wire:model.live="rekapTanggalMulai" class="input w-full text-xs font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1">Tanggal Selesai</label>
                        <input type="date" wire:model.live="rekapTanggalSelesai" class="input w-full text-xs font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1">Filter Kelompok Halaqoh</label>
                        <select wire:model.live="rekapHalaqohId" class="select w-full text-xs font-semibold">
                            <option value="">Semua Kelompok Halaqoh</option>
                            @foreach($halaqohs as $h)
                                <option value="{{ $h->id }}">{{ $h->nama_halaqoh }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-text-secondary mb-1">Filter Waktu Sesi</label>
                        <select wire:model.live="rekapWaktuSesi" class="select w-full text-xs font-semibold">
                            <option value="">Semua Waktu Sesi</option>
                            <option value="Subuh">Subuh</option>
                            <option value="Ashar">Ashar</option>
                            <option value="Maghrib">Maghrib</option>
                            <option value="Isya">Isya</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- KPI Ringkasan Global Kehadiran -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="bg-surface p-4 rounded-2xl border border-border shadow-xs">
                    <div class="text-[11px] font-bold uppercase text-text-secondary">Pertemuan Sesi</div>
                    <div class="text-2xl font-black text-text-main mt-1">{{ $globalStats->total_pertemuan ?? 0 }}</div>
                    <div class="text-[11px] text-text-muted mt-0.5">Sesi terlaksana</div>
                </div>
                <div class="bg-surface p-4 rounded-2xl border border-border shadow-xs">
                    <div class="text-[11px] font-bold uppercase text-text-secondary">Rata-rata Hadir</div>
                    <div class="text-2xl font-black text-teal-600 mt-1">{{ $globalPersen }}%</div>
                    <div class="text-[11px] text-text-muted mt-0.5">Persentase kehadiran</div>
                </div>
                <div class="bg-surface p-4 rounded-2xl border border-border shadow-xs">
                    <div class="text-[11px] font-bold uppercase text-emerald-600">Total Hadir (H)</div>
                    <div class="text-2xl font-black text-emerald-600 mt-1">{{ $globalStats->total_h ?? 0 }}</div>
                    <div class="text-[11px] text-text-muted mt-0.5">Santri hadir</div>
                </div>
                <div class="bg-surface p-4 rounded-2xl border border-border shadow-xs">
                    <div class="text-[11px] font-bold uppercase text-amber-600">Total Sakit (S)</div>
                    <div class="text-2xl font-black text-amber-600 mt-1">{{ $globalStats->total_s ?? 0 }}</div>
                    <div class="text-[11px] text-text-muted mt-0.5">Santri sakit</div>
                </div>
                <div class="bg-surface p-4 rounded-2xl border border-border shadow-xs">
                    <div class="text-[11px] font-bold uppercase text-sky-600">Total Izin (I)</div>
                    <div class="text-2xl font-black text-sky-600 mt-1">{{ $globalStats->total_i ?? 0 }}</div>
                    <div class="text-[11px] text-text-muted mt-0.5">Santri izin</div>
                </div>
                <div class="bg-surface p-4 rounded-2xl border border-border shadow-xs">
                    <div class="text-[11px] font-bold uppercase text-rose-600">Total Alfa (A)</div>
                    <div class="text-2xl font-black text-rose-600 mt-1">{{ $globalStats->total_a ?? 0 }}</div>
                    <div class="text-[11px] text-text-muted mt-0.5">Santri alfa / tanpa kabar</div>
                </div>
            </div>

            <!-- Sub-Tab Navigasi Rekap: Per Siswa / Per Kamar / Per Rayon -->
            <div class="flex items-center gap-2 border-b border-border pb-2">
                <button type="button" wire:click="$set('rekapSubTab', 'siswa')"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2
                        {{ $rekapSubTab === 'siswa' ? 'bg-teal-600 text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                    <span>👤</span>
                    <span>1. Rekap Per Santri</span>
                </button>
                <button type="button" wire:click="$set('rekapSubTab', 'kamar')"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2
                        {{ $rekapSubTab === 'kamar' ? 'bg-teal-600 text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                    <span>🛏️</span>
                    <span>2. Rekap Per Kamar</span>
                </button>
                <button type="button" wire:click="$set('rekapSubTab', 'rayon')"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2
                        {{ $rekapSubTab === 'rayon' ? 'bg-teal-600 text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                    <span>📍</span>
                    <span>3. Rekap Per Rayon</span>
                </button>
            </div>

            <!-- TABEL 1: REKAP PER SANTRI -->
            @if($rekapSubTab === 'siswa')
                <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                    <div class="p-4 border-b border-border flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <div>
                            <h4 class="text-sm font-bold text-text-main">Tabel Kehadiran Santri Halaqoh</h4>
                            <p class="text-xs text-text-secondary">Daftar santri program unggulan beserta statistik presensi pada periode terpilih.</p>
                        </div>
                        <div class="w-full sm:w-64">
                            <input type="text" wire:model.live.debounce.300ms="rekapSearchSiswa" placeholder="Cari santri..." class="input w-full text-xs">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-surface-hover/70 border-b border-border text-text-secondary uppercase text-[11px] tracking-wider">
                                    <th class="p-3 w-10 text-center font-bold">No</th>
                                    <th class="p-3 font-bold">Santri</th>
                                    <th class="p-3 font-bold">Halaqoh</th>
                                    <th class="p-3 font-bold">Kamar & Rayon</th>
                                    <th class="p-3 text-center font-bold text-emerald-600">H</th>
                                    <th class="p-3 text-center font-bold text-amber-600">S</th>
                                    <th class="p-3 text-center font-bold text-sky-600">I</th>
                                    <th class="p-3 text-center font-bold text-rose-600">A</th>
                                    <th class="p-3 text-center font-bold">Total Sesi</th>
                                    <th class="p-3 font-bold w-36">Kehadiran</th>
                                    <th class="p-3 text-center font-bold w-20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @forelse($rekapSiswas as $idx => $s)
                                    <tr class="hover:bg-surface-hover/40 transition-colors">
                                        <td class="p-3 text-center text-text-muted font-medium">{{ $idx + 1 }}</td>
                                        <td class="p-3">
                                            <div class="font-bold text-text-main">{{ $s->nama }}</div>
                                            <div class="text-[11px] text-text-muted">NISN: {{ $s->nisn ?? '-' }} &bull; {{ $s->rombel }}</div>
                                        </td>
                                        <td class="p-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-teal-50 dark:bg-teal-950/40 text-teal-600 border border-teal-200 dark:border-teal-800">
                                                {{ $s->halaqoh }}
                                            </span>
                                        </td>
                                        <td class="p-3">
                                            <div class="text-text-main font-medium">🛏️ {{ $s->kamar }}</div>
                                            <div class="text-[11px] text-text-muted">📍 {{ $s->rayon }}</div>
                                        </td>
                                        <td class="p-3 text-center font-bold text-emerald-600">{{ $s->h }}</td>
                                        <td class="p-3 text-center font-bold text-amber-600">{{ $s->s }}</td>
                                        <td class="p-3 text-center font-bold text-sky-600">{{ $s->i }}</td>
                                        <td class="p-3 text-center font-bold text-rose-600">{{ $s->a }}</td>
                                        <td class="p-3 text-center font-bold text-text-main">{{ $s->total_sesi }}</td>
                                        <td class="p-3">
                                            <div class="flex items-center justify-between text-[11px] font-bold mb-1">
                                                <span class="{{ $s->persen >= 80 ? 'text-emerald-600' : ($s->persen >= 60 ? 'text-amber-600' : 'text-rose-600') }}">
                                                    {{ $s->persen }}%
                                                </span>
                                            </div>
                                            <div class="w-full bg-surface-hover h-2 rounded-full overflow-hidden border border-border/50">
                                                <div class="h-full rounded-full {{ $s->persen >= 80 ? 'bg-emerald-500' : ($s->persen >= 60 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                     style="width: {{ $s->persen }}%"></div>
                                            </div>
                                        </td>
                                        <td class="p-3 text-center">
                                            <button type="button" wire:click="openDetailSiswa({{ $s->id }})" class="btn btn-secondary text-xs px-2.5 py-1">
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="py-8 text-center text-text-muted">
                                            Tidak ada data santri yang cocok dengan filter.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- TABEL 2: REKAP PER KAMAR -->
            @if($rekapSubTab === 'kamar')
                <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                    <div class="p-4 border-b border-border">
                        <h4 class="text-sm font-bold text-text-main">Tabel Rekapitulasi Presensi Halaqoh Per Kamar / Asrama</h4>
                        <p class="text-xs text-text-secondary">Akumulasi kehadiran santri unggulan di masing-masing kamar.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-surface-hover/70 border-b border-border text-text-secondary uppercase text-[11px] tracking-wider">
                                    <th class="p-3 w-10 text-center font-bold">No</th>
                                    <th class="p-3 font-bold">Kamar / Asrama</th>
                                    <th class="p-3 font-bold">Musrif / PJ</th>
                                    <th class="p-3 text-center font-bold">Santri Unggulan</th>
                                    <th class="p-3 text-center font-bold text-emerald-600">Total H</th>
                                    <th class="p-3 text-center font-bold text-amber-600">Total S</th>
                                    <th class="p-3 text-center font-bold text-sky-600">Total I</th>
                                    <th class="p-3 text-center font-bold text-rose-600">Total A</th>
                                    <th class="p-3 text-center font-bold">Total Sesi Rekap</th>
                                    <th class="p-3 font-bold w-40">Kehadiran Kamar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @forelse($rekapKamars as $idx => $k)
                                    <tr class="hover:bg-surface-hover/40 transition-colors">
                                        <td class="p-3 text-center text-text-muted font-medium">{{ $idx + 1 }}</td>
                                        <td class="p-3 font-bold text-text-main">
                                            🛏️ {{ $k->nama_kamar }}
                                            <div class="text-[11px] text-text-muted font-normal">Gedung: {{ $k->gedung }}</div>
                                        </td>
                                        <td class="p-3 text-text-secondary font-medium">{{ $k->musrif }}</td>
                                        <td class="p-3 text-center font-bold text-text-main">{{ $k->total_santri }} santri</td>
                                        <td class="p-3 text-center font-bold text-emerald-600">{{ $k->h }}</td>
                                        <td class="p-3 text-center font-bold text-amber-600">{{ $k->s }}</td>
                                        <td class="p-3 text-center font-bold text-sky-600">{{ $k->i }}</td>
                                        <td class="p-3 text-center font-bold text-rose-600">{{ $k->a }}</td>
                                        <td class="p-3 text-center font-bold text-text-main">{{ $k->total_sesi }}</td>
                                        <td class="p-3">
                                            <div class="flex items-center justify-between text-[11px] font-bold mb-1">
                                                <span class="{{ $k->persen >= 80 ? 'text-emerald-600' : ($k->persen >= 60 ? 'text-amber-600' : 'text-rose-600') }}">
                                                    {{ $k->persen }}%
                                                </span>
                                            </div>
                                            <div class="w-full bg-surface-hover h-2 rounded-full overflow-hidden border border-border/50">
                                                <div class="h-full rounded-full {{ $k->persen >= 80 ? 'bg-emerald-500' : ($k->persen >= 60 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                     style="width: {{ $k->persen }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="py-8 text-center text-text-muted">
                                            Belum ada data kamar asrama.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- TABEL 3: REKAP PER RAYON -->
            @if($rekapSubTab === 'rayon')
                <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                    <div class="p-4 border-b border-border">
                        <h4 class="text-sm font-bold text-text-main">Tabel Rekapitulasi Presensi Halaqoh Per Rayon</h4>
                        <p class="text-xs text-text-secondary">Akumulasi kehadiran santri unggulan di masing-masing rayon.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-surface-hover/70 border-b border-border text-text-secondary uppercase text-[11px] tracking-wider">
                                    <th class="p-3 w-10 text-center font-bold">No</th>
                                    <th class="p-3 font-bold">Rayon</th>
                                    <th class="p-3 font-bold">Pembimbing Rayon</th>
                                    <th class="p-3 text-center font-bold">Santri Unggulan</th>
                                    <th class="p-3 text-center font-bold text-emerald-600">Total H</th>
                                    <th class="p-3 text-center font-bold text-amber-600">Total S</th>
                                    <th class="p-3 text-center font-bold text-sky-600">Total I</th>
                                    <th class="p-3 text-center font-bold text-rose-600">Total A</th>
                                    <th class="p-3 text-center font-bold">Total Sesi Rekap</th>
                                    <th class="p-3 font-bold w-40">Kehadiran Rayon</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @forelse($rekapRayons as $idx => $r)
                                    <tr class="hover:bg-surface-hover/40 transition-colors">
                                        <td class="p-3 text-center text-text-muted font-medium">{{ $idx + 1 }}</td>
                                        <td class="p-3 font-bold text-text-main">
                                            📍 {{ $r->nama }}
                                        </td>
                                        <td class="p-3 text-text-secondary font-medium">{{ $r->pembimbing }}</td>
                                        <td class="p-3 text-center font-bold text-text-main">{{ $r->total_santri }} santri</td>
                                        <td class="p-3 text-center font-bold text-emerald-600">{{ $r->h }}</td>
                                        <td class="p-3 text-center font-bold text-amber-600">{{ $r->s }}</td>
                                        <td class="p-3 text-center font-bold text-sky-600">{{ $r->i }}</td>
                                        <td class="p-3 text-center font-bold text-rose-600">{{ $r->a }}</td>
                                        <td class="p-3 text-center font-bold text-text-main">{{ $r->total_sesi }}</td>
                                        <td class="p-3">
                                            <div class="flex items-center justify-between text-[11px] font-bold mb-1">
                                                <span class="{{ $r->persen >= 80 ? 'text-emerald-600' : ($r->persen >= 60 ? 'text-amber-600' : 'text-rose-600') }}">
                                                    {{ $r->persen }}%
                                                </span>
                                            </div>
                                            <div class="w-full bg-surface-hover h-2 rounded-full overflow-hidden border border-border/50">
                                                <div class="h-full rounded-full {{ $r->persen >= 80 ? 'bg-emerald-500' : ($r->persen >= 60 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                     style="width: {{ $r->persen }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="py-8 text-center text-text-muted">
                                            Belum ada data rayon.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Modal Form Halaqoh -->
    @if($showHalaqohModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface rounded-2xl border border-border shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="text-base font-bold text-text-main">
                        {{ $editingHalaqohId ? 'Edit Halaqoh' : 'Tambah Kelompok Halaqoh Baru' }}
                    </h3>
                    <button wire:click="$set('showHalaqohModal', false)" class="text-text-muted hover:text-text-main text-xl">&times;</button>
                </div>
                <form wire:submit="saveHalaqoh" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Nama Halaqoh *</label>
                        <input type="text" wire:model="nama_halaqoh" placeholder="Contoh: Halaqoh A" class="input w-full text-sm">
                        @error('nama_halaqoh') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Pengajar (Guru / Non-Guru)</label>
                        <select wire:model="pengajar_id" class="select w-full text-sm">
                            <option value="">-- Pilih Pengajar --</option>
                            @foreach($pengajars as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_lengkap }} ({{ ucfirst($p->jenis_gtk) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Kategori Gender</label>
                        <select wire:model="jenis_kelamin" class="select w-full text-sm">
                            <option value="L">Ikhwan (Putra)</option>
                            <option value="P">Akhwat (Putri)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Keterangan / Lokasi</label>
                        <textarea wire:model="keterangan" rows="2" placeholder="Tempat halaqoh / jadwal..." class="input w-full text-sm"></textarea>
                    </div>

                    <div class="pt-4 border-t border-border flex justify-end gap-2">
                        <button type="button" wire:click="$set('showHalaqohModal', false)" class="btn btn-secondary text-sm">Batal</button>
                        <button type="submit" class="btn btn-primary text-sm bg-teal-600 hover:bg-teal-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Plotting Santri Halaqoh -->
    @if($showPlottingModal && $activeHalaqoh)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface rounded-2xl border border-border shadow-2xl w-full max-w-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-surface-hover/50">
                    <div>
                        <h3 class="text-base font-bold text-text-main">Santri {{ $activeHalaqoh->nama_halaqoh }}</h3>
                        <p class="text-xs text-text-secondary">Pengajar: {{ $activeHalaqoh->pengajar?->nama_lengkap ?? '-' }} | {{ $activeHalaqoh->siswas->count() }} Santri</p>
                    </div>
                    <button wire:click="$set('showPlottingModal', false)" class="text-text-muted hover:text-text-main text-xl">&times;</button>
                </div>

                <div class="p-6 space-y-5">
                    @can('pesantren.halaqoh.kelola')
                        <div class="p-4 bg-surface-hover rounded-xl border border-border space-y-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-text-secondary">Masukkan Santri ke Halaqoh Ini</h4>
                            <div class="flex gap-2">
                                <select wire:model="selectedSiswaId" class="select text-xs flex-1">
                                    <option value="">-- Pilih Santri Unggulan Tersedia --</option>
                                    @foreach($availableSiswas as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->rombel?->nama ?? '-' }})</option>
                                    @endforeach
                                </select>
                                <button type="button" wire:click="addSiswaToHalaqoh" class="btn btn-primary text-xs py-2 bg-teal-600 hover:bg-teal-700 whitespace-nowrap">
                                    + Tambahkan
                                </button>
                            </div>
                        </div>
                    @endcan

                    <div class="border border-border rounded-xl overflow-hidden">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-surface-hover/80 text-text-secondary border-b border-border">
                                <tr>
                                    <th class="p-3">Santri</th>
                                    <th class="p-3">Rombel</th>
                                    <th class="p-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @forelse($activeHalaqoh->siswas as $siswa)
                                    <tr class="hover:bg-surface-hover/50">
                                        <td class="p-3 font-semibold text-text-main">{{ $siswa->nama }}</td>
                                        <td class="p-3 text-text-secondary">{{ $siswa->rombel?->nama ?? '-' }}</td>
                                        <td class="p-3 text-right">
                                            @can('pesantren.halaqoh.kelola')
                                                <button wire:click="removeSiswaFromHalaqoh({{ $siswa->id }})" wire:confirm="Keluarkan {{ $siswa->nama }} dari halaqoh ini?" class="text-rose-500 hover:text-rose-700 font-semibold p-1">
                                                    Keluarkan
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-6 text-center text-text-muted">
                                            Belum ada santri di halaqoh ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="px-6 py-3.5 bg-surface-hover/50 border-t border-border flex justify-end">
                    <button wire:click="$set('showPlottingModal', false)" class="btn btn-secondary text-sm">Tutup</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Detail Presensi Santri -->
    @if($showDetailModal && $detailSiswa)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface rounded-2xl border border-border shadow-2xl w-full max-w-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-surface-hover/40">
                    <div>
                        <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                            <span>👤</span>
                            <span>Riwayat Detail Presensi Santri</span>
                        </h3>
                        <p class="text-xs text-text-secondary mt-0.5">
                            {{ $detailSiswa->nama }} &bull; NISN: {{ $detailSiswa->nisn ?? '-' }} ({{ $detailSiswa->rombel?->nama ?? '-' }})
                        </p>
                    </div>
                    <button wire:click="closeDetailSiswa" class="text-text-muted hover:text-text-main text-2xl font-bold">&times;</button>
                </div>

                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <!-- Ringkasan Info Santri & Lokasi -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs bg-surface-hover/50 p-3 rounded-xl border border-border">
                        <div>
                            <span class="text-text-muted block">Halaqoh:</span>
                            <span class="font-bold text-teal-600">{{ $detailSiswa->pesantrenHalaqohs->first()?->nama_halaqoh ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-text-muted block">Kamar:</span>
                            <span class="font-bold text-text-main">{{ $detailSiswa->pesantrenKamars->first()?->nama_kamar ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-text-muted block">Rayon:</span>
                            <span class="font-bold text-text-main">{{ $detailSiswa->rayon?->nama ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-text-muted block">Periode Rekap:</span>
                            <span class="font-semibold text-text-secondary">{{ $rekapTanggalMulai }} s/d {{ $rekapTanggalSelesai }}</span>
                        </div>
                    </div>

                    <!-- Tabel Riwayat Kehadiran -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-text-secondary mb-2">Log Presensi Sesi</h4>
                        <div class="border border-border rounded-xl overflow-hidden">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-surface-hover text-text-secondary font-bold border-b border-border uppercase text-[11px]">
                                        <th class="p-2.5">Tanggal</th>
                                        <th class="p-2.5">Halaqoh</th>
                                        <th class="p-2.5">Sesi</th>
                                        <th class="p-2.5 text-center">Status</th>
                                        <th class="p-2.5">Catatan / Alasan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    @forelse($detailPresensis as $dp)
                                        <tr class="hover:bg-surface-hover/30">
                                            <td class="p-2.5 font-semibold text-text-main whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($dp->presensi?->tanggal)->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="p-2.5 text-text-secondary whitespace-nowrap">
                                                {{ $dp->presensi?->halaqoh?->nama_halaqoh ?? '-' }}
                                            </td>
                                            <td class="p-2.5 font-medium text-text-main whitespace-nowrap">
                                                {{ $dp->presensi?->waktu_sesi }}
                                            </td>
                                            <td class="p-2.5 text-center">
                                                @if($dp->status === 'H')
                                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500 text-white">Hadir</span>
                                                @elseif($dp->status === 'S')
                                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-500 text-white">Sakit</span>
                                                @elseif($dp->status === 'I')
                                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-sky-500 text-white">Izin</span>
                                                @elseif($dp->status === 'A')
                                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-500 text-white">Alfa</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-gray-200 text-gray-700">-</span>
                                                @endif
                                            </td>
                                            <td class="p-2.5 text-text-secondary">
                                                {{ $dp->catatan ?: '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-6 text-center text-text-muted">
                                                Belum ada catatan presensi pada rentang tanggal ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-3.5 bg-surface-hover/50 border-t border-border flex justify-end">
                    <button wire:click="closeDetailSiswa" class="btn btn-secondary text-sm">Tutup</button>
                </div>
            </div>
        </div>
    @endif
</div>
