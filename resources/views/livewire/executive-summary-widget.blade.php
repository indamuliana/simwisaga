<div class="space-y-6">
    <!-- Baris 1: 3 Grafik Utama -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Demografi (Pie) -->
        <div class="card bg-surface p-5 rounded-xl border border-border shadow-sm">
            <h3 class="text-lg font-bold text-text-main mb-4">Demografi Sekolah</h3>
            <div id="chart-demografi" class="h-64" wire:ignore></div>
        </div>

        <!-- Chart Kehadiran (Line) -->
        <div class="card bg-surface p-5 rounded-xl border border-border shadow-sm">
            <h3 class="text-lg font-bold text-text-main mb-4">Tren Kehadiran (7 Hari)</h3>
            <div id="chart-kehadiran" class="h-64" wire:ignore></div>
        </div>

        <!-- Chart Akademik (Bar) -->
        <div class="card bg-surface p-5 rounded-xl border border-border shadow-sm">
            <h3 class="text-lg font-bold text-text-main mb-4">Rata-rata Nilai Tertinggi</h3>
            <div id="chart-akademik" class="h-64" wire:ignore></div>
        </div>
    </div>

    <!-- Baris 2: 2 Grafik Tambahan (Demo) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart Konseling (Bar) -->
        <div class="card bg-surface p-5 rounded-xl border border-border shadow-sm relative">
            <div class="absolute top-4 right-4 bg-primary-soft text-primary text-xs font-bold px-2 py-1 rounded">DATA DEMO</div>
            <h3 class="text-lg font-bold text-text-main mb-4">Bimbingan Konseling (Semester Ini)</h3>
            <div id="chart-konseling" class="h-64" wire:ignore></div>
        </div>

        <!-- Chart Pembayaran (Donut) -->
        <div class="card bg-surface p-5 rounded-xl border border-border shadow-sm relative">
            <div class="absolute top-4 right-4 bg-primary-soft text-primary text-xs font-bold px-2 py-1 rounded">DATA DEMO</div>
            <h3 class="text-lg font-bold text-text-main mb-4">Status Pembayaran SPP (Bulan Ini)</h3>
            <div id="chart-pembayaran" class="h-64" wire:ignore></div>
        </div>
    </div>

    @script
    <script>
        setTimeout(() => {
            if (typeof ApexCharts === 'undefined') {
                console.error('ApexCharts library is missing.');
                return;
            }

            // 1. Demografi Pie Chart
            new ApexCharts(document.getElementById('chart-demografi'), {
                chart: { type: 'pie', height: 280, fontFamily: 'inherit' },
                series: [{{ $demografi['siswaLaki'] }}, {{ $demografi['siswaPerempuan'] }}, {{ $demografi['guru'] }}, {{ $demografi['tendik'] }}],
                labels: ['Siswa (L)', 'Siswa (P)', 'Guru', 'Tendik'],
                colors: ['#3b82f6', '#ec4899', '#10b981', '#f59e0b'],
                legend: { position: 'bottom', labels: { colors: 'currentColor' } },
                stroke: { show: false }
            }).render();

            // 2. Kehadiran Line Chart
            new ApexCharts(document.getElementById('chart-kehadiran'), {
                chart: { type: 'line', height: 280, toolbar: { show: false }, fontFamily: 'inherit' },
                series: [
                    { name: 'Hadir', data: @json($kehadiran['series']['Hadir']) },
                    { name: 'Sakit', data: @json($kehadiran['series']['Sakit']) },
                    { name: 'Izin', data: @json($kehadiran['series']['Izin']) },
                    { name: 'Alpa', data: @json($kehadiran['series']['Alpa']) }
                ],
                xaxis: { categories: @json($kehadiran['categories']), labels: { style: { colors: '#9ca3af' } } },
                yaxis: { labels: { style: { colors: '#9ca3af' } } },
                colors: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444'],
                stroke: { curve: 'smooth', width: 2 },
                legend: { position: 'bottom', labels: { colors: 'currentColor' } }
            }).render();

            // 3. Akademik Bar Chart
            new ApexCharts(document.getElementById('chart-akademik'), {
                chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'inherit' },
                series: [{ name: 'Rata-rata Nilai', data: @json($akademik['series']) }],
                xaxis: { categories: @json($akademik['categories']), labels: { style: { colors: '#9ca3af' }, rotate: -45, trim: true, style: { fontSize: '10px' } } },
                yaxis: { labels: { style: { colors: '#9ca3af' } } },
                colors: ['#8b5cf6'],
                plotOptions: { bar: { borderRadius: 4, horizontal: false, columnWidth: '50%' } },
                dataLabels: { enabled: false },
                grid: { borderColor: '#374151', strokeDashArray: 4 }
            }).render();

            // 4. Konseling Bar Chart
            new ApexCharts(document.getElementById('chart-konseling'), {
                chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'inherit' },
                series: @json($konselingDemo['series']),
                xaxis: { categories: @json($konselingDemo['categories']), labels: { style: { colors: '#9ca3af' } } },
                yaxis: { labels: { style: { colors: '#9ca3af' } } },
                colors: ['#ef4444', '#10b981'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '50%', dataLabels: { position: 'top' } } },
                dataLabels: { enabled: false },
                grid: { borderColor: '#374151', strokeDashArray: 4 },
                legend: { position: 'bottom', labels: { colors: 'currentColor' } }
            }).render();

            // 5. Pembayaran Donut Chart
            new ApexCharts(document.getElementById('chart-pembayaran'), {
                chart: { type: 'donut', height: 280, fontFamily: 'inherit' },
                series: @json($pembayaranDemo['series']),
                labels: @json($pembayaranDemo['labels']),
                colors: ['#10b981', '#f59e0b', '#ef4444'],
                legend: { position: 'bottom', labels: { colors: 'currentColor' } },
                stroke: { show: false },
                plotOptions: { pie: { donut: { labels: { show: true, name: { color: '#9ca3af' }, value: { color: 'currentColor' } } } } }
            }).render();
        }, 100);
    </script>
    @endscript
</div>
