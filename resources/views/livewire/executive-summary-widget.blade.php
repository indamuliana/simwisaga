<div class="space-y-6">
    <!-- 4 Kartu KPI Ringkas (Responsif: 2 kolom di mobile, 4 kolom di sm/lg) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- 1. Siswa Aktif -->
        <div class="card p-3.5 sm:p-4 bg-surface border border-border rounded-xl shadow-xs hover:border-border-hover transition-all">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-medium text-text-secondary truncate">Siswa Aktif</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-xl sm:text-2xl font-extrabold text-text-main tracking-tight">{{ number_format($demografi['totalSiswa']) }}</span>
                <span class="text-[10px] font-semibold text-text-muted">Orang</span>
            </div>
            <div class="mt-2 text-[11px] text-text-muted flex items-center justify-between gap-1 flex-wrap">
                <span>{{ $demografi['siswaLaki'] }} L</span>
                <span>•</span>
                <span>{{ $demografi['siswaPerempuan'] }} P</span>
            </div>
            <div class="mt-2 w-full bg-border rounded-full h-1.5 overflow-hidden flex">
                @php
                    $persenL = $demografi['totalSiswa'] > 0 ? round(($demografi['siswaLaki'] / $demografi['totalSiswa']) * 100) : 50;
                @endphp
                <div class="bg-blue-500 h-1.5" style="width: {{ $persenL }}%" title="Laki-laki: {{ $persenL }}%"></div>
                <div class="bg-pink-500 h-1.5" style="width: {{ 100 - $persenL }}%" title="Perempuan: {{ 100 - $persenL }}%"></div>
            </div>
        </div>

        <!-- 2. Kehadiran Siswa -->
        <div class="card p-3.5 sm:p-4 bg-surface border border-border rounded-xl shadow-xs hover:border-border-hover transition-all">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-medium text-text-secondary truncate">Kehadiran Siswa</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-xl sm:text-2xl font-extrabold text-text-main tracking-tight">{{ $kehadiranHariIni['persen'] }}%</span>
                <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400">
                    {{ $kehadiranHariIni['isToday'] ? 'Hari Ini' : 'Efektif' }}
                </span>
            </div>
            <div class="mt-2 text-[11px] text-text-muted truncate" title="{{ $kehadiranHariIni['tanggalLabel'] }}">
                <span>{{ $kehadiranHariIni['hadir'] }} Hadir</span>
                @if($kehadiranHariIni['sakit'] > 0) • <span class="text-amber-600">{{ $kehadiranHariIni['sakit'] }} S</span>@endif
                @if($kehadiranHariIni['izin'] > 0) • <span class="text-blue-600">{{ $kehadiranHariIni['izin'] }} I</span>@endif
                @if($kehadiranHariIni['alpa'] > 0) • <span class="text-red-600 font-bold">{{ $kehadiranHariIni['alpa'] }} A</span>@endif
            </div>
            <div class="mt-2 w-full bg-border rounded-full h-1.5 overflow-hidden">
                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $kehadiranHariIni['persen'] }}%"></div>
            </div>
        </div>

        <!-- 3. Kesiapan Rapor 9M -->
        <div class="card p-3.5 sm:p-4 bg-surface border border-border rounded-xl shadow-xs hover:border-border-hover transition-all">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-medium text-text-secondary truncate">Kesiapan Rapor 9M</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-xl sm:text-2xl font-extrabold text-text-main tracking-tight">{{ $rapor9m['persen'] }}%</span>
                <span class="text-[10px] font-semibold text-indigo-600 dark:text-indigo-400">Tuntas</span>
            </div>
            <div class="mt-2 text-[11px] text-text-muted truncate">
                <span>{{ $rapor9m['totalTuntas'] }} dari {{ $rapor9m['totalPenugasan'] }} Mapel</span>
            </div>
            <div class="mt-2 w-full bg-border rounded-full h-1.5 overflow-hidden">
                <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $rapor9m['persen'] }}%"></div>
            </div>
        </div>

        <!-- 4. GTK (Guru & Tendik) -->
        <div class="card p-3.5 sm:p-4 bg-surface border border-border rounded-xl shadow-xs hover:border-border-hover transition-all">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-medium text-text-secondary truncate">Guru & Tendik</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-xl sm:text-2xl font-extrabold text-text-main tracking-tight">{{ number_format($demografi['totalGTK']) }}</span>
                <span class="text-[10px] font-semibold text-text-muted">Personel</span>
            </div>
            <div class="mt-2 text-[11px] text-text-muted flex items-center justify-between gap-1 flex-wrap">
                <span>{{ $demografi['guru'] }} Guru</span>
                <span>•</span>
                <span>{{ $demografi['tendik'] }} Tendik</span>
            </div>
            <div class="mt-2 w-full bg-border rounded-full h-1.5 overflow-hidden flex">
                @php
                    $persenGuru = $demografi['totalGTK'] > 0 ? round(($demografi['guru'] / $demografi['totalGTK']) * 100) : 50;
                @endphp
                <div class="bg-amber-500 h-1.5" style="width: {{ $persenGuru }}%" title="Guru: {{ $persenGuru }}%"></div>
                <div class="bg-emerald-500 h-1.5" style="width: {{ 100 - $persenGuru }}%" title="Tendik: {{ 100 - $persenGuru }}%"></div>
            </div>
        </div>
    </div>

    <!-- Grafik 1: Tren Kehadiran Siswa (7 Hari Efektif) -->
    <div class="card p-4 sm:p-5 bg-surface border border-border rounded-xl shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-border">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-text-main flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                    Tren Kehadiran Siswa (7 Hari Efektif)
                </h3>
                <p class="text-xs text-text-muted mt-0.5">Grafik dinamika kehadiran, sakit, izin, dan alpa harian siswa</p>
            </div>
            <div class="text-[11px] font-semibold text-text-muted bg-surface-hover px-2.5 py-1 rounded-lg self-start sm:self-auto border border-border">
                T.A. {{ $tahunAjaranNama }} &bull; Sem. {{ $semesterNama }}
            </div>
        </div>
        <div class="mt-3">
            <div id="chart-kehadiran" class="w-full" wire:ignore></div>
        </div>
    </div>

    <!-- Grafik 2: Kesiapan Rapor 9 Minggu per Rombel -->
    <div class="card p-4 sm:p-5 bg-surface border border-border rounded-xl shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-border">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-text-main flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Kesiapan Rapor 9 Minggu per Rombel
                </h3>
                <p class="text-xs text-text-muted mt-0.5">Persentase penugasan mata pelajaran yang telah merampungkan rekap nilai 9M</p>
            </div>
            <div class="flex items-center gap-1.5 self-start sm:self-auto">
                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-2.5 py-1 rounded-lg border border-indigo-200 dark:border-indigo-900">
                    Rata-rata: {{ $rapor9m['persen'] }}%
                </span>
            </div>
        </div>
        <div class="mt-3">
            <div id="chart-rapor-9m" class="w-full" wire:ignore></div>
        </div>
    </div>

    @script
    <script>
        let chartKehadiran = null;
        let chartRapor = null;

        function initCharts() {
            if (typeof ApexCharts === 'undefined') {
                return;
            }

            const isDark = document.documentElement.classList.contains('dark');
            const textColor = '#9ca3af';
            const gridColor = isDark ? 'rgba(75, 85, 99, 0.25)' : 'rgba(229, 231, 235, 0.8)';
            const isMobile = window.innerWidth < 640;
            const chartHeight = isMobile ? 220 : 260;

            // 1. Chart Kehadiran (Line / Area)
            const elKehadiran = document.getElementById('chart-kehadiran');
            if (elKehadiran) {
                if (chartKehadiran) {
                    chartKehadiran.destroy();
                }

                const optionsKehadiran = {
                    chart: {
                        type: 'area',
                        height: chartHeight,
                        toolbar: { show: false },
                        fontFamily: 'inherit',
                        sparkline: { enabled: false },
                        zoom: { enabled: false }
                    },
                    series: [
                        { name: 'Hadir', data: @json($kehadiranTren['series']['Hadir']) },
                        { name: 'Sakit', data: @json($kehadiranTren['series']['Sakit']) },
                        { name: 'Izin', data: @json($kehadiranTren['series']['Izin']) },
                        { name: 'Alpa', data: @json($kehadiranTren['series']['Alpa']) }
                    ],
                    xaxis: {
                        categories: @json($kehadiranTren['categories']),
                        labels: {
                            style: { colors: textColor, fontSize: '11px' }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: {
                            style: { colors: textColor, fontSize: '11px' }
                        },
                        min: 0
                    },
                    colors: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444'],
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.35,
                            opacityTo: 0.05,
                            stops: [0, 95, 100]
                        }
                    },
                    grid: {
                        borderColor: gridColor,
                        strokeDashArray: 3
                    },
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center',
                        labels: { colors: textColor },
                        itemMargin: { horizontal: 8, vertical: 2 }
                    },
                    tooltip: {
                        theme: isDark ? 'dark' : 'light',
                        y: {
                            formatter: function (val) {
                                return val + " Siswa";
                            }
                        }
                    }
                };

                chartKehadiran = new ApexCharts(elKehadiran, optionsKehadiran);
                chartKehadiran.render();
            }

            // 2. Chart Kesiapan Rapor 9M (Horizontal Bar)
            const elRapor = document.getElementById('chart-rapor-9m');
            if (elRapor) {
                if (chartRapor) {
                    chartRapor.destroy();
                }

                const raporDetails = @json($rapor9m['details']);

                const optionsRapor = {
                    chart: {
                        type: 'bar',
                        height: chartHeight,
                        toolbar: { show: false },
                        fontFamily: 'inherit'
                    },
                    series: [{
                        name: 'Kesiapan Rapor 9M',
                        data: @json($rapor9m['series'])
                    }],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 6,
                            barHeight: '55%',
                            distributed: false,
                            dataLabels: {
                                position: 'right'
                            }
                        }
                    },
                    colors: ['#4f46e5'],
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return val + "%";
                        },
                        offsetX: 25,
                        style: {
                            fontSize: '11px',
                            fontWeight: 'bold',
                            colors: [textColor]
                        }
                    },
                    xaxis: {
                        categories: @json($rapor9m['categories']),
                        max: 100,
                        labels: {
                            formatter: function (val) {
                                return val + "%";
                            },
                            style: { colors: textColor, fontSize: '11px' }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: {
                            style: { colors: textColor, fontSize: '12px', fontWeight: 600 }
                        }
                    },
                    grid: {
                        borderColor: gridColor,
                        strokeDashArray: 3
                    },
                    tooltip: {
                        theme: isDark ? 'dark' : 'light',
                        custom: function({ series, seriesIndex, dataPointIndex, w }) {
                            const val = series[seriesIndex][dataPointIndex];
                            const detail = raporDetails[dataPointIndex] || '';
                            const rombelName = w.globals.labels[dataPointIndex];
                            return '<div class="p-2 text-xs font-sans ' + (isDark ? 'bg-gray-800 text-gray-100' : 'bg-white text-gray-800') + ' rounded shadow-md border border-gray-200 dark:border-gray-700">' +
                                '<div class="font-bold">' + rombelName + '</div>' +
                                '<div class="mt-1 text-indigo-500 font-semibold">' + val + '% Selesai</div>' +
                                '<div class="text-gray-400 text-[10px]">' + detail + ' selesai rekap</div>' +
                                '</div>';
                        }
                    }
                };

                chartRapor = new ApexCharts(elRapor, optionsRapor);
                chartRapor.render();
            }
        }

        setTimeout(initCharts, 80);

        // Re-render pada window resize untuk memastikan responsive
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(initCharts, 250);
        });
    </script>
    @endscript
</div>
