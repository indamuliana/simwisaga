<div class="space-y-6"
     x-data="{
         contactModal: null,
         copiedWaKey: null,
         chartInstance: null,
         openContacts(item) {
             this.contactModal = item;
         },
         closeContacts() {
             this.contactModal = null;
         },
         async copyWaSummary(key, text) {
             try {
                 await navigator.clipboard.writeText(text);
             } catch (e) {
                 const ta = document.createElement('textarea');
                 ta.value = text;
                 document.body.appendChild(ta);
                 ta.select();
                 document.execCommand('copy');
                 document.body.removeChild(ta);
             }
             this.copiedWaKey = key;
             setTimeout(() => {
                 if (this.copiedWaKey === key) this.copiedWaKey = null;
             }, 2500);
         },
         renderChart(chartData) {
             if (typeof ApexCharts === 'undefined' || !chartData) return;
             const el = document.getElementById('absensi-dashboard-chart');
             if (!el) return;
             if (this.chartInstance) {
                 this.chartInstance.destroy();
                 this.chartInstance = null;
             }
             const isDark = document.documentElement.classList.contains('dark');
             const textColor = '#9ca3af';
             const gridColor = isDark ? 'rgba(75, 85, 99, 0.25)' : 'rgba(229, 231, 235, 0.8)';

             const options = {
                 chart: {
                     type: 'area',
                     height: 280,
                     toolbar: { show: false },
                     fontFamily: 'inherit',
                     zoom: { enabled: false }
                 },
                 series: [
                     { name: 'H (Hadir)', data: chartData.series?.H || [] },
                     { name: 'Dispen', data: chartData.series?.Dispen || [] },
                     { name: 'S (Sakit)', data: chartData.series?.S || [] },
                     { name: 'I (Izin)', data: chartData.series?.I || [] },
                     { name: 'A (Alpa)', data: chartData.series?.A || [] }
                 ],
                 xaxis: {
                     categories: chartData.categories || [],
                     labels: { style: { colors: textColor, fontSize: '11px' } },
                     axisBorder: { show: false },
                     axisTicks: { show: false }
                 },
                 yaxis: {
                     min: 0,
                     labels: { style: { colors: textColor, fontSize: '11px' } }
                 },
                 colors: ['#10b981', '#8b5cf6', '#f59e0b', '#3b82f6', '#ef4444'],
                 stroke: { curve: 'smooth', width: 2 },
                 fill: {
                     type: 'gradient',
                     gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.04, stops: [0, 95, 100] }
                 },
                 grid: { borderColor: gridColor, strokeDashArray: 3 },
                 legend: {
                     position: 'bottom',
                     horizontalAlign: 'center',
                     labels: { colors: textColor },
                     itemMargin: { horizontal: 8, vertical: 2 }
                 },
                 tooltip: {
                     theme: isDark ? 'dark' : 'light',
                     y: { formatter: (val) => val + ' siswa' }
                 }
             };

             this.chartInstance = new ApexCharts(el, options);
             this.chartInstance.render();
         }
     }"
     x-init="$nextTick(() => renderChart(@js($data['chart'])))"
     @absensi-dashboard-chart-updated.window="renderChart($event.detail.chart)"
     @keydown.escape.window="closeContacts()">

    @php
        $kpi = $data['kpi'];
        $progressData = $progressTab === 'rayon' ? $data['progress_rayon'] : $data['progress_rombel'];
        $topAbsentItems = $topAbsentTab === 'rayon' ? $data['top_rayon_absent'] : $data['top_rombel_absent'];
        $tglFormatted = \Carbon\Carbon::parse($data['tanggal'])->translatedFormat('l, d F Y');
    @endphp

    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Dashboard Absensi</h1>
            <p class="text-sm text-text-secondary">
                {{ $hasFullAccess ? 'Pemantauan presensi siswa seluruh sekolah' : 'Pemantauan presensi siswa binaan Anda' }}
                &bull; <span class="font-medium text-text-main">{{ $tglFormatted }}</span>
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button"
                    @click="copyWaSummary('rekap', @js($data['wa_summary']))"
                    class="btn btn-secondary text-xs sm:text-sm inline-flex items-center gap-1.5"
                    title="Salin ringkasan kehadiran & rombel belum tuntas">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                </svg>
                <span x-show="copiedWaKey !== 'rekap'">Salin WA Rekap</span>
                <span x-show="copiedWaKey === 'rekap'" x-cloak class="text-emerald-600 dark:text-emerald-400 font-semibold">Tersalin!</span>
            </button>
            <button type="button"
                    @click="copyWaSummary('rayon', @js($data['wa_summary_rayon']))"
                    class="btn btn-secondary text-xs sm:text-sm inline-flex items-center gap-1.5"
                    title="Salin daftar ketidakhadiran dikelompokkan per Rayon">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                </svg>
                <span x-show="copiedWaKey !== 'rayon'">Salin WA Rayon</span>
                <span x-show="copiedWaKey === 'rayon'" x-cloak class="text-emerald-600 dark:text-emerald-400 font-semibold">Tersalin!</span>
            </button>
            <button type="button"
                    @click="copyWaSummary('rombel', @js($data['wa_summary_rombel']))"
                    class="btn btn-secondary text-xs sm:text-sm inline-flex items-center gap-1.5"
                    title="Salin daftar ketidakhadiran dikelompokkan per Rombel">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                </svg>
                <span x-show="copiedWaKey !== 'rombel'">Salin WA Rombel</span>
                <span x-show="copiedWaKey === 'rombel'" x-cloak class="text-emerald-600 dark:text-emerald-400 font-semibold">Tersalin!</span>
            </button>
            @if($canInput)
                <a href="{{ route('absensi.harian', ['tanggal' => $data['tanggal']]) }}"
                   class="btn btn-primary text-xs sm:text-sm inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span>Input Absensi Harian</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card p-4 sm:p-5 bg-surface border border-border rounded-xl space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-text-secondary mb-1 uppercase tracking-wider">Tanggal Fokus</label>
                <input type="date" wire:model.live="tanggal" class="input w-full text-sm">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-text-secondary mb-1 uppercase tracking-wider">Tingkat</label>
                <select wire:model.live="tingkat" class="input w-full text-sm">
                    <option value="">Semua Tingkat</option>
                    @foreach($tingkats as $t)
                        <option value="{{ $t }}">Kelas {{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-text-secondary mb-1 uppercase tracking-wider">Rombel</label>
                <select wire:model.live="rombel_id" class="input w-full text-sm">
                    <option value="">{{ $hasFullAccess ? 'Semua Rombel' : 'Semua Rombel Binaan' }}</option>
                    @foreach($rombels as $rombel)
                        <option value="{{ $rombel->id }}">{{ $rombel->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-text-secondary mb-1 uppercase tracking-wider">Rayon</label>
                <select wire:model.live="rayon_id" class="input w-full text-sm">
                    <option value="">{{ $hasFullAccess ? 'Semua Rayon' : 'Semua Rayon Binaan' }}</option>
                    @foreach($rayons as $rayon)
                        <option value="{{ $rayon->id }}">{{ $rayon->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-text-secondary mb-1 uppercase tracking-wider">Gender</label>
                <div class="flex items-center gap-2">
                    <select wire:model.live="gender" class="input w-full text-sm">
                        <option value="">Semua (L &amp; P)</option>
                        <option value="L">Laki-laki (L)</option>
                        <option value="P">Perempuan (P)</option>
                    </select>
                    @if($rombel_id || $rayon_id || $tingkat || $gender || $tanggal !== now()->toDateString())
                        <button type="button" wire:click="resetFilters" class="btn btn-secondary text-xs px-2.5 shrink-0" title="Reset filter">
                            Reset
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Statistik Kehadiran --}}
    <div class="space-y-3">
        {{-- Baris KPI Utama --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Persentase Kehadiran + Delta --}}
            <div class="card p-4 bg-surface border border-emerald-300 dark:border-emerald-800/80 rounded-xl">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Persentase Kehadiran</span>
                    @if($kpi['delta_persen_hadir'] !== null)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ $kpi['delta_persen_hadir'] >= 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300' }}">
                            {{ $kpi['delta_persen_hadir'] >= 0 ? '+' : '' }}{{ number_format($kpi['delta_persen_hadir'], 1) }}% vs {{ $kpi['prev_date_label'] }}
                        </span>
                    @endif
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($kpi['persen_hadir'], 1) }}%</span>
                    <span class="text-xs text-text-secondary">({{ $kpi['hadir_statistik'] }} / {{ $kpi['sudah_absen'] }} tercatat)</span>
                </div>
                <p class="text-[11px] text-text-secondary mt-1">Termasuk H ({{ $kpi['hadir'] }}) &amp; Dispen ({{ $kpi['dispen_total'] }})</p>
            </div>

            {{-- Progres Kepatuhan Input --}}
            <div class="card p-4 bg-surface border border-border rounded-xl">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Progres Input Absensi</span>
                    <span class="text-xs font-bold {{ $kpi['belum_absen'] === 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                        {{ number_format($kpi['persen_input'], 1) }}%
                    </span>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-text-main">{{ $kpi['sudah_absen'] }}</span>
                    <span class="text-xs text-text-secondary">dari {{ $kpi['total_siswa'] }} siswa aktif</span>
                </div>
                <div class="w-full bg-surface-hover rounded-full h-2 mt-2.5 overflow-hidden">
                    <div class="h-2 rounded-full transition-all {{ $kpi['belum_absen'] === 0 ? 'bg-emerald-500' : 'bg-amber-500' }}"
                         style="width: {{ min(100, $kpi['persen_input']) }}%"></div>
                </div>
            </div>

            {{-- Total Tidak Hadir (A + I + S) --}}
            <div class="card p-4 bg-surface border border-rose-300 dark:border-rose-800/80 rounded-xl">
                <span class="text-xs font-semibold text-text-secondary uppercase tracking-wider block">Total Tidak Hadir</span>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-rose-600 dark:text-rose-400">{{ $kpi['tidak_hadir'] }}</span>
                    <span class="text-xs text-text-secondary">siswa</span>
                </div>
                <div class="flex flex-wrap items-center gap-2 mt-2 text-[11px] font-semibold">
                    <span class="text-rose-600 dark:text-rose-400">A: {{ $kpi['alpa'] }}</span>
                    <span class="text-border">&bull;</span>
                    <span class="text-blue-600 dark:text-blue-400">I: {{ $kpi['izin'] }}</span>
                    <span class="text-border">&bull;</span>
                    <span class="text-amber-600 dark:text-amber-400">S: {{ $kpi['sakit'] }}</span>
                </div>
            </div>

            {{-- Belum Diabsen --}}
            <div class="card p-4 bg-surface border {{ $kpi['belum_absen'] > 0 ? 'border-orange-300 dark:border-orange-800' : 'border-emerald-300 dark:border-emerald-800' }} rounded-xl">
                <span class="text-xs font-semibold text-text-secondary uppercase tracking-wider block">Belum Diabsen</span>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold {{ $kpi['belum_absen'] > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                        {{ $kpi['belum_absen'] }}
                    </span>
                    <span class="text-xs text-text-secondary">siswa</span>
                </div>
                <p class="text-[11px] text-text-secondary mt-2">
                    {{ $data['progress_rombel']['summary']['lengkap'] }}/{{ $data['progress_rombel']['summary']['total'] }} rombel sudah tuntas 100%
                </p>
            </div>
        </div>

        {{-- Baris Rincian Status 8 Kartu --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-2.5">
            <div class="card p-3 bg-surface border border-border rounded-xl">
                <span class="text-[11px] font-semibold text-text-secondary block">Total</span>
                <span class="text-lg font-bold text-text-main">{{ $kpi['total_siswa'] }}</span>
            </div>
            <div class="card p-3 bg-surface border border-emerald-300 dark:border-emerald-800/80 rounded-xl" title="Hadir">
                <span class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 block">H</span>
                <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ $kpi['hadir'] }}</span>
            </div>
            <div class="card p-3 bg-surface border border-amber-300 dark:border-amber-800/80 rounded-xl" title="Sakit">
                <span class="text-[11px] font-semibold text-amber-700 dark:text-amber-400 block">S</span>
                <span class="text-lg font-bold text-amber-600 dark:text-amber-400">{{ $kpi['sakit'] }}</span>
            </div>
            <div class="card p-3 bg-surface border border-blue-300 dark:border-blue-800/80 rounded-xl" title="Izin">
                <span class="text-[11px] font-semibold text-blue-700 dark:text-blue-400 block">I</span>
                <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $kpi['izin'] }}</span>
            </div>
            <div class="card p-3 bg-surface border border-rose-300 dark:border-rose-800/80 rounded-xl" title="Alpa">
                <span class="text-[11px] font-semibold text-rose-700 dark:text-rose-400 block">A</span>
                <span class="text-lg font-bold text-rose-600 dark:text-rose-400">{{ $kpi['alpa'] }}</span>
            </div>
            <div class="card p-3 bg-surface border border-purple-300 dark:border-purple-800/80 rounded-xl" title="Dispen CBT PLH">
                <span class="text-[11px] font-semibold text-purple-700 dark:text-purple-400 block truncate">CBT PLH</span>
                <span class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ $kpi['dispen_cbt_plh'] }}</span>
            </div>
            <div class="card p-3 bg-surface border border-fuchsia-300 dark:border-fuchsia-800/80 rounded-xl" title="Dispen CBT KWH">
                <span class="text-[11px] font-semibold text-fuchsia-700 dark:text-fuchsia-400 block truncate">CBT KWH</span>
                <span class="text-lg font-bold text-fuchsia-600 dark:text-fuchsia-400">{{ $kpi['dispen_cbt_kwh'] }}</span>
            </div>
            <div class="card p-3 bg-surface border border-indigo-300 dark:border-indigo-800/80 rounded-xl" title="Dispen Lainnya">
                <span class="text-[11px] font-semibold text-indigo-700 dark:text-indigo-400 block truncate">Dispen</span>
                <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ $kpi['dispen_lainnya'] }}</span>
            </div>
        </div>
    </div>

    {{-- Grafik Tren Kehadiran + Top 5 Ketidakhadiran --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        {{-- Grafik Kehadiran (8 Kolom) --}}
        <div class="lg:col-span-8 card p-5 bg-surface border border-border rounded-xl space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-text-main">Grafik Tren Kehadiran</h2>
                    <p class="text-xs text-text-secondary">Periode: {{ $data['chart']['range_label'] ?: '-' }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-1.5">
                    @foreach([
                        '7d' => '7 Hari',
                        '14d' => '14 Hari',
                        '30d' => '30 Hari',
                        'bulan_ini' => 'Bulan Ini',
                        'semester' => 'Semester',
                    ] as $rKey => $rLabel)
                        <button type="button"
                                wire:click="setChartRange('{{ $rKey }}')"
                                class="px-2.5 py-1 rounded-lg text-xs font-semibold transition border {{ $chartRange === $rKey ? 'bg-primary text-white border-primary' : 'bg-surface text-text-secondary border-border hover:bg-surface-hover' }}">
                            {{ $rLabel }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div wire:ignore>
                <div id="absensi-dashboard-chart" class="w-full min-h-[280px]"></div>
            </div>
        </div>

        {{-- Top 5 Rombel / Rayon Ketidakhadiran Tertinggi (4 Kolom) --}}
        <div class="lg:col-span-4 card p-5 bg-surface border border-border rounded-xl flex flex-col justify-between space-y-4">
            <div class="space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <h2 class="text-base font-bold text-text-main">Top 5 Ketidakhadiran</h2>
                        <p class="text-[11px] text-text-secondary">Akumulasi S + I + A periode grafik</p>
                    </div>
                    <div class="inline-flex rounded-lg border border-border overflow-hidden text-[11px]">
                        <button type="button"
                                wire:click="setTopAbsentTab('rombel')"
                                class="px-2.5 py-1 font-semibold {{ $topAbsentTab === 'rombel' ? 'bg-primary text-white' : 'bg-surface text-text-secondary hover:bg-surface-hover' }}">
                            Rombel
                        </button>
                        <button type="button"
                                wire:click="setTopAbsentTab('rayon')"
                                class="px-2.5 py-1 font-semibold {{ $topAbsentTab === 'rayon' ? 'bg-primary text-white' : 'bg-surface text-text-secondary hover:bg-surface-hover' }}">
                            Rayon
                        </button>
                    </div>
                </div>

                @if(count($topAbsentItems) > 0)
                    @php $maxAbsent = max(1, $topAbsentItems[0]['total'] ?? 1); @endphp
                    <div class="space-y-3 pt-1">
                        @foreach($topAbsentItems as $idx => $item)
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-semibold text-text-main truncate pr-2">
                                        {{ $idx + 1 }}. {{ $item['nama'] }}
                                    </span>
                                    <span class="font-bold text-rose-600 dark:text-rose-400 whitespace-nowrap">
                                        {{ $item['total'] }} kasus
                                    </span>
                                </div>
                                <div class="w-full bg-surface-hover rounded-full h-1.5 overflow-hidden">
                                    <div class="h-1.5 rounded-full bg-rose-500"
                                         style="width: {{ round(($item['total'] / $maxAbsent) * 100) }}%"></div>
                                </div>
                                <div class="flex items-center gap-2 text-[10px] text-text-secondary">
                                    <span class="text-rose-600 dark:text-rose-400 font-semibold">A: {{ $item['a'] }}</span>
                                    <span>&bull;</span>
                                    <span class="text-blue-600 dark:text-blue-400 font-semibold">I: {{ $item['i'] }}</span>
                                    <span>&bull;</span>
                                    <span class="text-amber-600 dark:text-amber-400 font-semibold">S: {{ $item['s'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-10 text-center text-xs text-text-secondary">
                        Tidak ada catatan ketidakhadiran (S/I/A) pada periode ini.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Progres Kepatuhan Input Absensi (Per Rombel / Per Rayon) --}}
    <div class="card p-5 bg-surface border border-border rounded-xl space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-text-main">
                    Status Kepatuhan Absensi {{ $progressTab === 'rayon' ? 'Per Rayon' : 'Per Rombel' }}
                </h2>
                <p class="text-xs text-text-secondary">
                    Menampilkan {{ $progressTab === 'rayon' ? 'rayon' : 'rombel' }} yang belum tuntas di urutan teratas &bull;
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $progressData['summary']['lengkap'] }} Lengkap</span> &bull;
                    <span class="text-amber-600 dark:text-amber-400 font-semibold">{{ $progressData['summary']['parsial'] }} Sebagian</span> &bull;
                    <span class="text-rose-600 dark:text-rose-400 font-semibold">{{ $progressData['summary']['belum'] }} Belum Sama Sekali</span>
                </p>
            </div>
            <div class="inline-flex rounded-lg border border-border overflow-hidden text-xs shrink-0">
                <button type="button"
                        wire:click="setProgressTab('rombel')"
                        class="px-3 py-1.5 font-semibold {{ $progressTab === 'rombel' ? 'bg-primary text-white' : 'bg-surface text-text-secondary hover:bg-surface-hover' }}">
                    Per Rombel ({{ $data['progress_rombel']['summary']['total'] }})
                </button>
                <button type="button"
                        wire:click="setProgressTab('rayon')"
                        class="px-3 py-1.5 font-semibold {{ $progressTab === 'rayon' ? 'bg-primary text-white' : 'bg-surface text-text-secondary hover:bg-surface-hover' }}">
                    Per Rayon ({{ $data['progress_rayon']['summary']['total'] }})
                </button>
            </div>
        </div>

        @if(count($progressData['items']) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-96 overflow-y-auto pr-1">
                @foreach($progressData['items'] as $unit)
                    <div class="p-3.5 rounded-xl border transition {{ $unit['state'] === 'lengkap' ? 'border-emerald-200 dark:border-emerald-800/70 bg-emerald-50/40 dark:bg-emerald-950/20' : ($unit['state'] === 'belum' ? 'border-rose-200 dark:border-rose-800/70 bg-rose-50/50 dark:bg-rose-950/20' : 'border-amber-200 dark:border-amber-800/70 bg-amber-50/50 dark:bg-amber-950/20') }}">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <div class="font-bold text-sm text-text-main truncate">{{ $unit['nama'] }}</div>
                                <div class="text-[11px] text-text-secondary truncate">
                                    {{ $progressTab === 'rayon' ? 'Pembimbing:' : 'Wali Kelas:' }} {{ $unit['pj_nama'] }}
                                </div>
                            </div>
                            <span class="text-sm font-bold shrink-0 {{ $unit['state'] === 'lengkap' ? 'text-emerald-600 dark:text-emerald-400' : ($unit['state'] === 'belum' ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400') }}">
                                {{ number_format($unit['persen'], 0) }}%
                            </span>
                        </div>

                        <div class="w-full bg-surface rounded-full h-2 mt-2.5 overflow-hidden border border-border/50">
                            <div class="h-2 rounded-full {{ $unit['state'] === 'lengkap' ? 'bg-emerald-500' : ($unit['state'] === 'belum' ? 'bg-rose-500' : 'bg-amber-500') }}"
                                 style="width: {{ min(100, $unit['persen']) }}%"></div>
                        </div>

                        <div class="mt-2 flex items-center justify-between gap-2 text-[11px]">
                            <span class="font-semibold {{ $unit['state'] === 'lengkap' ? 'text-emerald-700 dark:text-emerald-300' : ($unit['state'] === 'belum' ? 'text-rose-700 dark:text-rose-300' : 'text-amber-700 dark:text-amber-300') }}">
                                {{ $unit['status_text'] }}
                            </span>
                            <span class="text-text-secondary font-mono">{{ $unit['sudah_absen'] }}/{{ $unit['total_siswa'] }}</span>
                        </div>

                        @if($unit['belum_absen'] > 0)
                            <div class="mt-2.5 pt-2 border-t border-border/50 flex flex-wrap items-center gap-2">
                                @if($canInput)
                                    <a href="{{ $unit['input_url'] }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-primary text-white hover:opacity-90 transition">
                                        Input Absen
                                    </a>
                                @endif
                                @if($unit['pj_wa_url'])
                                    <a href="{{ $unit['pj_wa_url'] }}"
                                       target="_blank"
                                       rel="noopener"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-emerald-600 text-white hover:opacity-90 transition">
                                        Ingatkan {{ $progressTab === 'rayon' ? 'Pembimbing' : 'Wali Kelas' }} (WA)
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-8 text-center text-xs text-text-secondary">
                Tidak ada data {{ $progressTab === 'rayon' ? 'rayon' : 'rombel' }} yang sesuai dengan filter.
            </div>
        @endif
    </div>

    {{-- Card Rekap Ketidakhadiran: 3 Tabel Terpisah Berdampingan (Alpa, Izin, Sakit) --}}
    <div class="space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 px-1">
            <div>
                <h2 class="text-base font-bold text-text-main">Rekap Ketidakhadiran Siswa</h2>
                <p class="text-xs text-text-secondary">Klik badge <strong>Chat</strong> pada siswa untuk melihat kontak Siswa, Ayah, Ibu, Wali, Pembimbing Rayon, dan Wali Kelas.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Tabel 1: Daftar Siswa Alpa --}}
            <div class="card bg-surface border border-rose-300 dark:border-rose-800/80 rounded-xl overflow-hidden flex flex-col">
                <div class="px-4 py-3 bg-rose-50 dark:bg-rose-950/40 border-b border-rose-200 dark:border-rose-800/80 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        <h3 class="text-sm font-bold text-rose-800 dark:text-rose-300">Daftar Siswa Alpa</h3>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-200">
                        {{ count($data['daftar_alpa']) }}
                    </span>
                </div>
                <div class="overflow-x-auto max-h-96 overflow-y-auto flex-1">
                    @if(count($data['daftar_alpa']) > 0)
                        <table class="w-full text-xs text-left">
                            <thead class="text-[10px] uppercase tracking-wider text-text-secondary bg-surface-hover/80 border-b border-border">
                                <tr>
                                    <th class="px-3 py-2">Siswa &amp; Rayon</th>
                                    <th class="px-3 py-2 text-right w-20">Hubungi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach($data['daftar_alpa'] as $item)
                                    <tr class="hover:bg-surface-hover/50">
                                        <td class="px-3 py-2.5">
                                            <div class="font-semibold text-text-main">{{ $item['nama'] }}</div>
                                            <div class="text-[11px] text-text-secondary mt-0.5">
                                                <span class="font-medium text-text-main">{{ $item['rombel_nama'] }}</span>
                                                &bull; {{ $item['rayon_nama'] }}
                                            </div>
                                            <div class="text-[11px] text-text-secondary">
                                                Pembimbing: <span class="text-text-main">{{ $item['pembimbing_rayon_nama'] }}</span>
                                            </div>
                                            @if($item['keterangan'])
                                                <div class="text-[11px] italic text-rose-700 dark:text-rose-300 mt-0.5">
                                                    "{{ $item['keterangan'] }}"
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-right align-top">
                                            <button type="button"
                                                    @click="openContacts(@js($item))"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300/80 dark:border-emerald-800 cursor-pointer transition"
                                                    title="Lihat 6 kontak (Siswa, Ayah, Ibu, Wali, Pembimbing Rayon, Wali Kelas)">
                                                <span>Chat</span>
                                                <span class="text-[10px] opacity-80">({{ $item['available_contacts_count'] }})</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-8 text-center text-xs text-text-secondary">
                            Tidak ada siswa Alpa pada tanggal ini.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tabel 2: Daftar Siswa Izin --}}
            <div class="card bg-surface border border-blue-300 dark:border-blue-800/80 rounded-xl overflow-hidden flex flex-col">
                <div class="px-4 py-3 bg-blue-50 dark:bg-blue-950/40 border-b border-blue-200 dark:border-blue-800/80 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                        <h3 class="text-sm font-bold text-blue-800 dark:text-blue-300">Daftar Siswa Izin</h3>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-200">
                        {{ count($data['daftar_izin']) }}
                    </span>
                </div>
                <div class="overflow-x-auto max-h-96 overflow-y-auto flex-1">
                    @if(count($data['daftar_izin']) > 0)
                        <table class="w-full text-xs text-left">
                            <thead class="text-[10px] uppercase tracking-wider text-text-secondary bg-surface-hover/80 border-b border-border">
                                <tr>
                                    <th class="px-3 py-2">Siswa &amp; Rayon</th>
                                    <th class="px-3 py-2 text-right w-20">Hubungi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach($data['daftar_izin'] as $item)
                                    <tr class="hover:bg-surface-hover/50">
                                        <td class="px-3 py-2.5">
                                            <div class="font-semibold text-text-main">{{ $item['nama'] }}</div>
                                            <div class="text-[11px] text-text-secondary mt-0.5">
                                                <span class="font-medium text-text-main">{{ $item['rombel_nama'] }}</span>
                                                &bull; {{ $item['rayon_nama'] }}
                                            </div>
                                            <div class="text-[11px] text-text-secondary">
                                                Pembimbing: <span class="text-text-main">{{ $item['pembimbing_rayon_nama'] }}</span>
                                            </div>
                                            @if($item['keterangan'])
                                                <div class="text-[11px] italic text-blue-700 dark:text-blue-300 mt-0.5">
                                                    "{{ $item['keterangan'] }}"
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-right align-top">
                                            <button type="button"
                                                    @click="openContacts(@js($item))"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300/80 dark:border-emerald-800 cursor-pointer transition"
                                                    title="Lihat 6 kontak (Siswa, Ayah, Ibu, Wali, Pembimbing Rayon, Wali Kelas)">
                                                <span>Chat</span>
                                                <span class="text-[10px] opacity-80">({{ $item['available_contacts_count'] }})</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-8 text-center text-xs text-text-secondary">
                            Tidak ada siswa Izin pada tanggal ini.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tabel 3: Daftar Siswa Sakit --}}
            <div class="card bg-surface border border-amber-300 dark:border-amber-800/80 rounded-xl overflow-hidden flex flex-col">
                <div class="px-4 py-3 bg-amber-50 dark:bg-amber-950/40 border-b border-amber-200 dark:border-amber-800/80 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <h3 class="text-sm font-bold text-amber-800 dark:text-amber-300">Daftar Siswa Sakit</h3>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-200">
                        {{ count($data['daftar_sakit']) }}
                    </span>
                </div>
                <div class="overflow-x-auto max-h-96 overflow-y-auto flex-1">
                    @if(count($data['daftar_sakit']) > 0)
                        <table class="w-full text-xs text-left">
                            <thead class="text-[10px] uppercase tracking-wider text-text-secondary bg-surface-hover/80 border-b border-border">
                                <tr>
                                    <th class="px-3 py-2">Siswa &amp; Rayon</th>
                                    <th class="px-3 py-2 text-right w-20">Hubungi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach($data['daftar_sakit'] as $item)
                                    <tr class="hover:bg-surface-hover/50">
                                        <td class="px-3 py-2.5">
                                            <div class="font-semibold text-text-main">{{ $item['nama'] }}</div>
                                            <div class="text-[11px] text-text-secondary mt-0.5">
                                                <span class="font-medium text-text-main">{{ $item['rombel_nama'] }}</span>
                                                &bull; {{ $item['rayon_nama'] }}
                                            </div>
                                            <div class="text-[11px] text-text-secondary">
                                                Pembimbing: <span class="text-text-main">{{ $item['pembimbing_rayon_nama'] }}</span>
                                            </div>
                                            @if($item['keterangan'])
                                                <div class="text-[11px] italic text-amber-700 dark:text-amber-300 mt-0.5">
                                                    "{{ $item['keterangan'] }}"
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-right align-top">
                                            <button type="button"
                                                    @click="openContacts(@js($item))"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300/80 dark:border-emerald-800 cursor-pointer transition"
                                                    title="Lihat 6 kontak (Siswa, Ayah, Ibu, Wali, Pembimbing Rayon, Wali Kelas)">
                                                <span>Chat</span>
                                                <span class="text-[10px] opacity-80">({{ $item['available_contacts_count'] }})</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-8 text-center text-xs text-text-secondary">
                            Tidak ada siswa Sakit pada tanggal ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Ringkas Siswa Dispensasi (CBT PLH / CBT KWH / Dispen) --}}
    <div class="card bg-surface border border-purple-300 dark:border-purple-800/80 rounded-xl overflow-hidden">
        <div class="px-4 py-3 bg-purple-50/70 dark:bg-purple-950/30 border-b border-purple-200 dark:border-purple-800/70 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-purple-900 dark:text-purple-200">Daftar Siswa Dispensasi (CBT PLH / CBT KWH / Dispen)</h2>
                <p class="text-[11px] text-text-secondary">Siswa tetap dihitung hadir secara statistik namun sedang menjalankan tugas/kegiatan di luar kelas.</p>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 dark:bg-purple-900/60 dark:text-purple-200">
                {{ count($data['daftar_dispen']) }} siswa
            </span>
        </div>

        @if(count($data['daftar_dispen']) > 0)
            <div class="overflow-x-auto max-h-80 overflow-y-auto">
                <table class="w-full text-xs text-left">
                    <thead class="text-[10px] uppercase tracking-wider text-text-secondary bg-surface-hover/80 border-b border-border">
                        <tr>
                            <th class="px-4 py-2.5">Siswa</th>
                            <th class="px-4 py-2.5">Rombel</th>
                            <th class="px-4 py-2.5">Rayon &amp; Pembimbing</th>
                            <th class="px-4 py-2.5">Jenis Dispen</th>
                            <th class="px-4 py-2.5">Keterangan</th>
                            <th class="px-4 py-2.5 text-right">Hubungi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($data['daftar_dispen'] as $item)
                            <tr class="hover:bg-surface-hover/50">
                                <td class="px-4 py-2.5 font-semibold text-text-main">{{ $item['nama'] }}</td>
                                <td class="px-4 py-2.5 text-text-main">{{ $item['rombel_nama'] }}</td>
                                <td class="px-4 py-2.5 text-text-secondary">
                                    <span class="text-text-main">{{ $item['rayon_nama'] }}</span>
                                    <span class="block text-[11px]">{{ $item['pembimbing_rayon_nama'] }}</span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-yellow-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                        {{ $item['status_label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-text-secondary">{{ $item['keterangan'] ?: '-' }}</td>
                                <td class="px-4 py-2.5 text-right">
                                    <button type="button"
                                            @click="openContacts(@js($item))"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300/80 dark:border-emerald-800 cursor-pointer transition">
                                        <span>Chat</span>
                                        <span class="text-[10px] opacity-80">({{ $item['available_contacts_count'] }})</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6 text-center text-xs text-text-secondary">
                Tidak ada siswa Dispensasi pada tanggal ini.
            </div>
        @endif
    </div>

    {{-- Early Warning: Alert Siswa Rawan (Beruntun & Akumulasi SP) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Alpa / Sakit Beruntun >= 2 Hari --}}
        <div class="card bg-surface border border-orange-300 dark:border-orange-800 rounded-xl overflow-hidden">
            <div class="px-4 py-3 bg-orange-50 dark:bg-orange-950/40 border-b border-orange-200 dark:border-orange-800 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-orange-900 dark:text-orange-200">Alert Ketidakhadiran Beruntun (&ge; 2 Hari)</h2>
                    <p class="text-[11px] text-text-secondary">Siswa Alpa atau Sakit berturut-turut hingga tanggal fokus — perlu tindak lanjut segera.</p>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800 dark:bg-orange-950/60 dark:text-orange-200">
                    {{ count($data['alert_beruntun']) }}
                </span>
            </div>
            <div class="overflow-x-auto max-h-80 overflow-y-auto">
                @if(count($data['alert_beruntun']) > 0)
                    <table class="w-full text-xs text-left">
                        <thead class="text-[10px] uppercase tracking-wider text-text-secondary bg-surface-hover/80 border-b border-border">
                            <tr>
                                <th class="px-4 py-2">Siswa &amp; Rayon</th>
                                <th class="px-3 py-2 text-center">Durasi</th>
                                <th class="px-4 py-2 text-right">Hubungi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($data['alert_beruntun'] as $item)
                                <tr class="hover:bg-surface-hover/50">
                                    <td class="px-4 py-2.5">
                                        <div class="font-semibold text-text-main">{{ $item['nama'] }}</div>
                                        <div class="text-[11px] text-text-secondary">
                                            {{ $item['rombel_nama'] }} &bull; {{ $item['rayon_nama'] }} ({{ $item['pembimbing_rayon_nama'] }})
                                        </div>
                                    </td>
                                    <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ $item['status_value'] === 'Alpa' ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' }}">
                                            {{ $item['status_label'] }} &bull; {{ $item['streak'] }} hari
                                        </span>
                                        <div class="text-[10px] text-text-secondary mt-0.5">Sejak {{ $item['since_label'] }}</div>
                                    </td>
                                    <td class="px-4 py-2.5 text-right">
                                        <button type="button"
                                                @click="openContacts(@js($item))"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300/80 dark:border-emerald-800 cursor-pointer transition">
                                            Chat ({{ $item['available_contacts_count'] }})
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-8 text-center text-xs text-text-secondary">
                        Tidak ada siswa yang Alpa atau Sakit berturut-turut &ge; 2 hari.
                    </div>
                @endif
            </div>
        </div>

        {{-- Akumulasi Alpa Tinggi Semester Aktif (SP1 / SP2 / SP3) --}}
        <div class="card bg-surface border border-rose-300 dark:border-rose-800 rounded-xl overflow-hidden">
            <div class="px-4 py-3 bg-rose-50 dark:bg-rose-950/40 border-b border-rose-200 dark:border-rose-800 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-rose-900 dark:text-rose-200">Alert Akumulasi Alpa Semester (SP 1 / 2 / 3)</h2>
                    <p class="text-[11px] text-text-secondary">Ambang batas: SP 1 (&ge;3x), SP 2 (&ge;6x), SP 3 (&ge;9x Alpa).</p>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-200">
                    {{ count($data['alert_sp']) }}
                </span>
            </div>
            <div class="overflow-x-auto max-h-80 overflow-y-auto">
                @if(count($data['alert_sp']) > 0)
                    <table class="w-full text-xs text-left">
                        <thead class="text-[10px] uppercase tracking-wider text-text-secondary bg-surface-hover/80 border-b border-border">
                            <tr>
                                <th class="px-4 py-2">Siswa &amp; Rayon</th>
                                <th class="px-3 py-2 text-center">Status SP</th>
                                <th class="px-4 py-2 text-right">Hubungi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($data['alert_sp'] as $item)
                                <tr class="hover:bg-surface-hover/50">
                                    <td class="px-4 py-2.5">
                                        <div class="font-semibold text-text-main">{{ $item['nama'] }}</div>
                                        <div class="text-[11px] text-text-secondary">
                                            {{ $item['rombel_nama'] }} &bull; {{ $item['rayon_nama'] }} ({{ $item['pembimbing_rayon_nama'] }})
                                        </div>
                                    </td>
                                    <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ $item['sp_level'] === 'SP 3' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300' : ($item['sp_level'] === 'SP 2' ? 'bg-orange-100 text-orange-800 dark:bg-orange-950/60 dark:text-orange-300' : 'bg-yellow-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300') }}">
                                            {{ $item['sp_level'] }} ({{ $item['total_alpa'] }}x A)
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-right">
                                        <button type="button"
                                                @click="openContacts(@js($item))"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300/80 dark:border-emerald-800 cursor-pointer transition">
                                            Chat ({{ $item['available_contacts_count'] }})
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-8 text-center text-xs text-text-secondary">
                        Belum ada siswa yang mencapai ambang batas SP (&ge; 3x Alpa) semester ini.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Kontak 6 Pihak (Siswa, Ayah, Ibu, Wali, Pembimbing Rayon, Wali Kelas) --}}
    <div x-show="contactModal !== null"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
         @click.self="closeContacts()">
        <div class="w-full max-w-lg bg-surface border border-border rounded-2xl shadow-xl overflow-hidden"
             x-show="contactModal !== null"
             x-transition>
            {{-- Header Modal --}}
            <div class="px-5 py-4 border-b border-border flex items-start justify-between gap-3 bg-surface-hover/50">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-bold text-text-main" x-text="contactModal?.nama"></h3>
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300"
                              x-text="contactModal?.status_full_label"></span>
                    </div>
                    <p class="text-xs text-text-secondary mt-0.5">
                        <span x-text="contactModal?.rombel_nama"></span> &bull;
                        <span x-text="contactModal?.rayon_nama"></span>
                    </p>
                    <p class="text-xs italic text-text-secondary mt-1"
                       x-show="contactModal?.keterangan"
                       x-text="'Keterangan: ' + (contactModal?.keterangan || '')"></p>
                </div>
                <button type="button"
                        @click="closeContacts()"
                        class="p-1.5 rounded-lg text-text-secondary hover:text-text-main hover:bg-surface-hover">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Daftar 6 Kontak --}}
            <div class="p-4 space-y-2.5 max-h-96 overflow-y-auto">
                <template x-for="contact in (contactModal?.contacts || [])" :key="contact.role">
                    <div class="p-3 rounded-xl border border-border bg-surface flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                                      x-text="contact.role"></span>
                                <span class="text-xs font-semibold text-text-main truncate" x-text="contact.nama"></span>
                            </div>
                            <div class="text-[11px] text-text-secondary font-mono mt-1"
                                 x-text="contact.no_hp || 'Nomor HP belum tersedia'"></div>
                        </div>

                        <div class="shrink-0">
                            <template x-if="contact.wa_url">
                                <a :href="contact.wa_url"
                                   target="_blank"
                                   rel="noopener"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 text-white hover:opacity-90 transition">
                                    <span>Chat WA</span>
                                </a>
                            </template>
                            <template x-if="!contact.wa_url">
                                <span class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-[11px] font-medium bg-surface-hover text-text-secondary cursor-not-allowed">
                                    Tidak tersedia
                                </span>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Footer Modal --}}
            <div class="px-5 py-3 border-t border-border bg-surface-hover/30 flex items-center justify-between text-[11px] text-text-secondary">
                <span>Pesan WhatsApp otomatis terisi sesuai status &amp; tanggal.</span>
                <button type="button" @click="closeContacts()" class="btn btn-secondary text-xs px-3 py-1">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
