<div class="space-y-6">
    <!-- Header Navigasi Sub-Modul -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-border pb-4">
        <div>
            <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                </svg>
                Mata Pelajaran Seni Budaya
            </h2>
            <p class="text-sm text-text-secondary mt-1">
                Pengelolaan mata pelajaran Seni Budaya (Kelompok A - Muatan Umum) dengan pelaksanaan lintas rombel.
            </p>
        </div>
        <!-- Tab Sub-Modul -->
        <div class="flex flex-wrap items-center gap-1.5 bg-surface-hover p-1 rounded-xl border border-border">
            <a href="{{ route('seni-budaya.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold bg-white dark:bg-gray-800 text-primary shadow-xs">
                Dashboard
            </a>
            <a href="{{ route('seni-budaya.cabang') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Cabang Seni
            </a>
            <a href="{{ route('seni-budaya.anggota') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Keanggotaan Siswa
            </a>
            <a href="{{ route('seni-budaya.tp') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Tujuan Pembelajaran (TP)
            </a>
            <a href="{{ route('seni-budaya.rekap') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Rekapitulasi Nilai
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

    <!-- Kartu KPI -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-4 flex items-center gap-3.5 border border-border">
            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div>
                <p class="text-xs text-text-secondary font-medium uppercase tracking-wider">Cabang Seni Budaya</p>
                <p class="text-xl font-bold text-text-main">{{ $totalCabang }} <span class="text-xs font-normal text-text-secondary">cabang</span></p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3.5 border border-border">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-text-secondary font-medium uppercase tracking-wider">Siswa Terdaftar</p>
                <p class="text-xl font-bold text-text-main">{{ $totalSiswa }} <span class="text-xs font-normal text-text-secondary">peserta didik</span></p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3.5 border border-border">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-xs text-text-secondary font-medium uppercase tracking-wider">Sesi Presensi</p>
                <p class="text-xl font-bold text-text-main">{{ $totalPresensi }} <span class="text-xs font-normal text-text-secondary">pertemuan</span></p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3.5 border border-border">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-text-secondary font-medium uppercase tracking-wider">Semester Aktif</p>
                <p class="text-sm font-bold text-text-main truncate">{{ $semester ? ($semester->tahunAjaran?->nama . ' - ' . ucfirst($semester->semester)) : 'Belum Ditentukan' }}</p>
            </div>
        </div>
    </div>

    <!-- Toolbar Pencarian & Aksi Cepat -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="relative w-full sm:w-80">
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Cari cabang seni / guru pengampu..."
                   class="input pl-10 w-full text-sm">
            <svg class="w-4 h-4 text-text-muted absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <a href="{{ route('seni-budaya.anggota') }}" class="btn-secondary text-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Plotting & Import Siswa
            </a>
            <a href="{{ route('seni-budaya.cabang') }}" class="btn-primary text-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Cabang Seni
            </a>
        </div>
    </div>

    <!-- Grid Kartu Cabang Seni Budaya -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($cabangs as $cabang)
            @php
                $siswaCount = $anggotaCounts[$cabang->id] ?? 0;
                $pertemuanCount = $presensiCounts[$cabang->id] ?? 0;
                $gradedCount = $gradedCounts[$cabang->id] ?? 0;
                $persenNilai = $siswaCount > 0 ? round(($gradedCount / $siswaCount) * 100) : 0;
            @endphp
            <div class="card p-5 border border-border flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="space-y-3">
                    <div class="flex justify-between items-start gap-2">
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-md bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                            {{ $cabang->kode }}
                        </span>
                        <span class="text-xs text-text-muted">KKM: {{ $cabang->kkm }}</span>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-text-main line-clamp-1">
                            {{ $cabang->nama }}
                        </h3>
                        <p class="text-xs text-text-secondary mt-0.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Guru: <span class="font-medium text-text-main">{{ $cabang->guru_display_name }}</span>
                        </p>
                    </div>

                    @if($cabang->deskripsi)
                        <p class="text-xs text-text-muted line-clamp-2">{{ $cabang->deskripsi }}</p>
                    @endif

                    <!-- Statistik Siswa & Nilai -->
                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-border text-center">
                        <div class="p-2 bg-surface-hover rounded-lg">
                            <span class="text-xs text-text-muted block">Jumlah Siswa</span>
                            <span class="text-base font-bold text-text-main">{{ $siswaCount }}</span>
                        </div>
                        <div class="p-2 bg-surface-hover rounded-lg">
                            <span class="text-xs text-text-muted block">Total Pertemuan</span>
                            <span class="text-base font-bold text-text-main">{{ $pertemuanCount }}</span>
                        </div>
                    </div>

                    <!-- Progress Penilaian -->
                    <div class="space-y-1">
                        <div class="flex justify-between text-xs text-text-muted">
                            <span>Progres Penilaian</span>
                            <span class="font-medium text-text-main">{{ $gradedCount }} / {{ $siswaCount }} ({{ $persenNilai }}%)</span>
                        </div>
                        <div class="w-full bg-surface-hover rounded-full h-1.5 overflow-hidden">
                            <div class="bg-primary h-1.5 rounded-full transition-all duration-300" style="width: {{ $persenNilai }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="pt-4 mt-4 border-t border-border flex items-center gap-2">
                    <a href="{{ route('seni-budaya.presensi', $cabang->id) }}" class="btn-secondary text-xs flex-1 justify-center py-2 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Presensi
                    </a>
                    <a href="{{ route('seni-budaya.penilaian', $cabang->id) }}" class="btn-primary text-xs flex-1 justify-center py-2 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Penilaian
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full card p-8 text-center border border-dashed border-border">
                <svg class="w-12 h-12 text-text-muted mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                </svg>
                <h4 class="text-base font-semibold text-text-main">Belum Ada Cabang Seni Budaya</h4>
                <p class="text-xs text-text-secondary mt-1 max-w-sm mx-auto">
                    Mulai dengan menambahkan cabang Seni Budaya (misal Seni Tari, Seni Suara, Seni Musik, Seni Rupa) untuk mengelola siswa dan penilaian.
                </p>
                <div class="mt-4">
                    <a href="{{ route('seni-budaya.cabang') }}" class="btn-primary text-sm inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Buat Cabang Seni Pertama
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
