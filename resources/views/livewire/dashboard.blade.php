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
