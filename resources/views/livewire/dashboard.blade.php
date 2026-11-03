<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-text-main">Selamat datang, {{ auth()->user()->name }}</h1>
        <p class="text-sm text-text-muted mt-1">
            Anda masuk sebagai: {{ $roles->join(', ') ?: 'belum memiliki role' }}
        </p>
    </div>

    @hasanyrole('Super Admin|Kepala Sekolah|Kesiswaan|Kurikulum|Operator')
        @livewire(\App\Core\Livewire\ExecutiveSummaryWidget::class)
    @endhasanyrole

    @hasrole('Siswa')
        <div class="card p-6 bg-linear-to-r from-primary-soft/40 via-surface to-surface border border-primary/20">
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

    <div class="card">
        <p class="text-sm text-text-secondary">
            Fondasi SIRAGA siap. Modul akan muncul di menu samping sesuai hak akses Anda.
        </p>
    </div>

    <!-- Widget Peringatan Alpa (Sprint S6) -->
    <livewire:app.modules.absensi.livewire.widget-peringatan-alpa />

    <!-- Widget Agenda Kalender Pendidikan -->
    @livewire(\App\Modules\KalenderPendidikan\Livewire\WidgetUpcomingEvents::class)
</div>
