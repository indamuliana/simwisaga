<div class="space-y-6">
    <!-- Header Navigasi Sub-Modul -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-border pb-4">
        <div>
            <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                Portal Kegiatan Unit Pengembangan Diri (UPD)
            </h2>
            <p class="text-sm text-text-secondary mt-1">
                Pilih Unit Pengembangan Diri (UPD) binaan Anda untuk mencatat presensi mingguan dan menginput penilaian rapor.
            </p>
        </div>
        <!-- Tab Sub-Modul -->
        <div class="flex items-center gap-2 bg-surface-hover p-1 rounded-xl border border-border">
            <a href="{{ route('ekskul.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold bg-white dark:bg-gray-800 text-primary shadow-xs">
                Kegiatan UPD
            </a>
            <a href="{{ route('ekskul.anggota') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Keanggotaan Siswa
            </a>
            <a href="{{ route('ekskul.rekap') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Rekapitulasi UPD
            </a>
            @if($isManager)
                <a href="{{ route('ekskul.setting') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                    Pengaturan UPD
                </a>
            @endif
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

    <!-- Kartu KPI Minimalis -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-4 flex items-center gap-3.5 border border-border">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div>
                <p class="text-xs text-text-secondary font-medium uppercase tracking-wider">{{ $isManager ? 'Total UPD Aktif' : 'UPD Binaan Anda' }}</p>
                <p class="text-xl font-bold text-text-main">{{ $totalEkskul }} <span class="text-xs font-normal text-text-secondary">kegiatan</span></p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3.5 border border-border">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-text-secondary font-medium uppercase tracking-wider">Total Anggota Siswa</p>
                <p class="text-xl font-bold text-text-main">{{ $totalAnggota }} <span class="text-xs font-normal text-text-secondary">siswa</span></p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3.5 border border-border">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <div>
                <p class="text-xs text-text-secondary font-medium uppercase tracking-wider">Sesi Presensi Tercatat</p>
                <p class="text-xl font-bold text-text-main">{{ $totalSesi }} <span class="text-xs font-normal text-text-secondary">pertemuan</span></p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3.5 border border-border">
            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-xs text-text-secondary font-medium uppercase tracking-wider">Periode Aktif</p>
                <p class="text-sm font-bold text-text-main truncate">{{ $semester ? ucfirst($semester->semester) . ' ' . $semester->tahunAjaran->nama : 'Belum diatur' }}</p>
            </div>
        </div>
    </div>

    <!-- Banner Administrator Shortcut ke Pengaturan -->
    @if($isManager)
        <div class="p-3.5 bg-blue-50/80 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900 rounded-xl flex items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 text-blue-900 dark:text-blue-300">
                <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Halaman ini khusus untuk kegiatan presensi dan penilaian. Untuk menambah UPD baru, mengubah jadwal latihan, nama pelatih, atau bobot nilai, silakan buka <strong>Pengaturan UPD</strong>.</span>
            </div>
            <a href="{{ route('ekskul.setting') }}" class="btn btn-secondary text-xs shrink-0 font-semibold text-blue-700 dark:text-blue-300">
                Buka Pengaturan UPD &rarr;
            </a>
        </div>
    @endif

    <!-- Toolbar: Search & Filter -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 bg-surface p-3.5 rounded-xl border border-border">
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kegiatan UPD..."
                       class="input pl-9 w-full text-sm">
                <svg class="w-4 h-4 text-text-secondary absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <select wire:model.live="filterKategori" class="select text-sm w-40">
                <option value="all">Semua Kategori</option>
                <option value="wajib">Wajib Saja</option>
                <option value="pilihan">Pilihan Saja</option>
            </select>
        </div>
    </div>

    <!-- Grid Kartu Ekstrakurikuler Khusus Operasional Presensi & Penilaian -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($ekskuls as $ekskul)
            <div class="card border border-border hover:shadow-md transition flex flex-col justify-between overflow-hidden">
                <div class="p-5 space-y-4">
                    <!-- Title & Badges -->
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-base font-bold text-text-main">{{ $ekskul->nama }}</h3>
                            @if($ekskul->is_wajib)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                    Wajib (Tingkat X)
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                                    Pilihan
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-text-secondary mt-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Instruktur: <strong class="text-text-main">{{ $ekskul->instruktur_display_name }}</strong></span>
                        </p>
                    </div>

                    <!-- Informasi Jadwal & Lokasi Patokan -->
                    <div class="bg-surface-hover/70 p-3 rounded-lg text-xs space-y-1.5 border border-border/60">
                        <div class="flex items-center justify-between">
                            <span class="text-text-secondary flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Jadwal Patokan:
                            </span>
                            <span class="font-medium text-text-main">{{ $ekskul->hari_latihan ?? 'Jumat' }} ({{ $ekskul->jam_mulai ? substr($ekskul->jam_mulai,0,5) : '15:30' }} - {{ $ekskul->jam_selesai ? substr($ekskul->jam_selesai,0,5) : '17:00' }})</span>
                        </div>
                        @if($ekskul->tempat)
                            <div class="flex items-center justify-between">
                                <span class="text-text-secondary flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Lokasi:
                                </span>
                                <span class="font-medium text-text-main truncate max-w-[150px]">{{ $ekskul->tempat }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between pt-1 border-t border-border/40 text-[11px]">
                            <span class="text-text-secondary">Bobot Nilai:</span>
                            <span class="font-medium text-primary">Presensi {{ $ekskul->bobot_presensi }}% + Asesmen {{ $ekskul->bobot_penilaian }}%</span>
                        </div>
                    </div>

                    <!-- Statistik Cepat: Anggota & Sesi -->
                    <div class="flex items-center justify-between text-xs text-text-secondary px-1">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <strong>{{ $anggotaCounts[$ekskul->id] ?? 0 }}</strong> Siswa Terdaftar
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <strong>{{ $presensiCounts[$ekskul->id] ?? 0 }}</strong> Sesi Pertemuan
                        </span>
                    </div>
                </div>

                <!-- Footer Aksi Operasional -->
                <div class="p-3 bg-surface-hover/40 border-t border-border flex items-center gap-2">
                    <a href="{{ route('ekskul.presensi', $ekskul->id) }}" class="btn btn-secondary text-xs flex-1 justify-center py-2.5 flex items-center gap-1.5 font-bold">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span>Presensi Mingguan</span>
                    </a>
                    <a href="{{ route('ekskul.penilaian', $ekskul->id) }}" class="btn btn-primary text-xs flex-1 justify-center py-2.5 flex items-center gap-1.5 font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Penilaian Rapor</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full card p-12 text-center text-text-secondary space-y-3">
                <svg class="w-12 h-12 mx-auto text-text-secondary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <p class="font-medium text-base text-text-main">
                    {{ $isManager ? 'Tidak ada data ekstrakurikuler yang ditemukan.' : 'Anda belum ditugaskan sebagai instruktur / pembina pada ekstrakurikuler manapun.' }}
                </p>
                @if($isManager)
                    <p class="text-xs max-w-md mx-auto">Silakan atur kegiatan ekstrakurikuler pada sub-modul Pengaturan.</p>
                @endif
            </div>
        @endforelse
    </div>
</div>
