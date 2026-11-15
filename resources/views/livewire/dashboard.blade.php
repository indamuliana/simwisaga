<div class="space-y-6">
    <!-- Header Dashboard -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-text-main">Selamat datang, {{ auth()->user()->name }}</h1>
            <p class="text-xs sm:text-sm text-text-muted mt-1 flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 font-medium text-text-secondary">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    T.A. {{ $tahunAjaranNama }} &bull; Semester {{ $semesterNama }}
                </span>
                <span>&bull;</span>
                <span>Masuk sebagai: <strong class="text-text-main font-semibold">{{ $roles->join(', ') ?: 'Pengguna' }}</strong></span>
            </p>
        </div>
    </div>

    @hasrole('Siswa')
        <div class="card p-5 bg-gradient-to-r from-primary-soft/40 via-surface to-surface border border-primary/20 rounded-xl shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-text-main">Akses Cepat Portal Siswa</h2>
                    <p class="text-xs text-text-muted mt-1">
                        Akses seluruh informasi data diri, nilai akademik, rapor, kehadiran, UPD/seni budaya, prestasi, dan beasiswa Anda.
                    </p>
                </div>
                <a href="{{ route('portal-siswa.beranda') }}" class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition-all flex items-center gap-2 whitespace-nowrap self-start sm:self-auto">
                    <span>🚀</span>
                    <span>Masuk ke Portal Siswa</span>
                </a>
            </div>
        </div>
    @endhasrole

    <!-- Split Grid: Kolom Utama (8 Kolom di LG) + Kolom Samping (4 Kolom di LG) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Kolom Utama (8 Kolom) -->
        <div class="lg:col-span-8 space-y-6">
            @hasanyrole('Super Admin|Kepala Sekolah|Kesiswaan|Kurikulum|Operator|Tata Usaha|Guru')
                @livewire(\App\Core\Livewire\ExecutiveSummaryWidget::class)
            @endhasanyrole
        </div>

        <!-- Kolom Samping (4 Kolom) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- 1. Pintasan Aksi Cepat -->
            <div class="card p-4 sm:p-5 bg-surface border border-border rounded-xl shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-border">
                    <h3 class="text-sm sm:text-base font-bold text-text-main flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Pintasan Cepat
                    </h3>
                    <span class="text-[11px] font-semibold text-text-muted">Aksi Mandiri</span>
                </div>
                <div class="mt-3.5 grid grid-cols-2 gap-2.5">
                    <!-- Input Nilai Mapel -->
                    <a href="{{ route('penilaian.index') }}" class="flex flex-col p-3 rounded-xl border border-border bg-surface-hover/30 hover:bg-surface-hover hover:border-primary/40 transition-all group">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2 group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </div>
                        <span class="text-xs font-bold text-text-main group-hover:text-primary transition-colors">Nilai Mapel</span>
                        <span class="text-[10px] text-text-muted mt-0.5">Formatif & STS</span>
                    </a>

                    <!-- Rapor Wikrama -->
                    <a href="{{ route('rapor.wikrama') }}" class="flex flex-col p-3 rounded-xl border border-border bg-surface-hover/30 hover:bg-surface-hover hover:border-primary/40 transition-all group">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2 group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        <span class="text-xs font-bold text-text-main group-hover:text-primary transition-colors">Rapor Wikrama</span>
                        <span class="text-[10px] text-text-muted mt-0.5">Cetak Rapor 9M</span>
                    </a>

                    <!-- Presensi Harian -->
                    <a href="{{ route('absensi.harian') }}" class="flex flex-col p-3 rounded-xl border border-border bg-surface-hover/30 hover:bg-surface-hover hover:border-primary/40 transition-all group">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-2 group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                        </div>
                        <span class="text-xs font-bold text-text-main group-hover:text-primary transition-colors">Presensi Harian</span>
                        <span class="text-[10px] text-text-muted mt-0.5">Absensi Rombel</span>
                    </a>

                    <!-- Panduan Rapor -->
                    <a href="{{ route('panduan.rapor-wikrama') }}" class="flex flex-col p-3 rounded-xl border border-border bg-surface-hover/30 hover:bg-surface-hover hover:border-primary/40 transition-all group">
                        <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-2 group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                        </div>
                        <span class="text-xs font-bold text-text-main group-hover:text-primary transition-colors">Buku Panduan</span>
                        <span class="text-[10px] text-text-muted mt-0.5">Panduan Rapor 9M</span>
                    </a>
                </div>
            </div>

            <!-- 2. Widget Peringatan Alpa (Hanya muncul jika ada siswa mencapai SP) -->
            <livewire:app.modules.absensi.livewire.widget-peringatan-alpa />

            <!-- 3. Widget Agenda Kegiatan Terdekat -->
            @livewire(\App\Modules\KalenderPendidikan\Livewire\WidgetUpcomingEvents::class)
        </div>
    </div>
</div>
