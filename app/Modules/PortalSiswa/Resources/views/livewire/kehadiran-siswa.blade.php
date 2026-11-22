<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface p-6 rounded-2xl border border-border shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                <span>📅 Portal Siswa</span>
                <span>&bull;</span>
                <span>Disiplin & Kehadiran</span>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">Rekap Kehadiran Siswa</h1>
            <p class="text-xs text-text-muted mt-1">
                Pantau kedisiplinan dan rekapitulasi kehadiran Anda secara akumulatif per bulan, per semester, dan log harian.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="px-5 py-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-center">
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Tingkat Kehadiran</span>
                <span class="text-2xl font-black text-emerald-600 font-mono">{{ $stats['persen'] }}%</span>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface p-4 rounded-2xl border border-border shadow-xs">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-text-main">Filter Presensi:</span>
        </div>
        <div class="flex flex-wrap items-center gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="text-text-muted">Tingkat:</span>
                <select wire:model.live="filterTingkat" class="rounded-xl border border-border bg-surface px-3 py-1.5 text-xs text-text-main font-semibold focus:border-primary focus:outline-hidden">
                    <option value="semua">Semua Tingkat</option>
                    <option value="X">Kelas X</option>
                    <option value="XI">Kelas XI</option>
                    <option value="XII">Kelas XII</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-text-muted">Semester:</span>
                <select wire:model.live="filterSemester" class="rounded-xl border border-border bg-surface px-3 py-1.5 text-xs text-text-main font-semibold focus:border-primary focus:outline-hidden">
                    <option value="semua">Semua Semester</option>
                    @foreach ($semesters as $s)
                        <option value="{{ $s->id }}">{{ $s->tahunAjaran?->nama }} - {{ $s->nama }} {{ $s->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- KPI Cards Statistik Kehadiran -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="card p-4 text-center">
            <span class="text-[10px] uppercase font-bold text-text-muted block">Hari Terdata</span>
            <span class="text-2xl font-black text-text-main font-mono mt-1 block">{{ $stats['total'] }}</span>
            <span class="text-[10px] text-text-muted">Total Pertemuan</span>
        </div>
        <div class="card p-4 text-center border-l-4 border-l-emerald-500">
            <span class="text-[10px] uppercase font-bold text-emerald-600 block">Hadir (H)</span>
            <span class="text-2xl font-black text-emerald-600 font-mono mt-1 block">{{ $stats['H'] }}</span>
            <span class="text-[10px] text-text-muted">Hari Efektif</span>
        </div>
        <div class="card p-4 text-center border-l-4 border-l-amber-500">
            <span class="text-[10px] uppercase font-bold text-amber-600 block">Sakit (S)</span>
            <span class="text-2xl font-black text-amber-600 font-mono mt-1 block">{{ $stats['S'] }}</span>
            <span class="text-[10px] text-text-muted">Dengan Keterangan</span>
        </div>
        <div class="card p-4 text-center border-l-4 border-l-blue-500">
            <span class="text-[10px] uppercase font-bold text-blue-600 block">Izin (I)</span>
            <span class="text-2xl font-black text-blue-600 font-mono mt-1 block">{{ $stats['I'] }}</span>
            <span class="text-[10px] text-text-muted">Dengan Keterangan</span>
        </div>
        <div class="card p-4 text-center border-l-4 {{ $stats['A'] > 0 ? 'border-l-rose-500 bg-rose-500/5' : 'border-l-slate-400' }} col-span-2 sm:col-span-1">
            <span class="text-[10px] uppercase font-bold {{ $stats['A'] > 0 ? 'text-rose-600' : 'text-text-muted' }} block">Alpa (A)</span>
            <span class="text-2xl font-black {{ $stats['A'] > 0 ? 'text-rose-600' : 'text-text-muted' }} font-mono mt-1 block">{{ $stats['A'] }}</span>
            <span class="text-[10px] {{ $stats['A'] > 0 ? 'text-rose-600 font-bold' : 'text-text-muted' }}">Tanpa Keterangan</span>
        </div>
    </div>

    <!-- TABEL 1: MATRIKS REKAPITULASI PRESENSI BULANAN -->
    <div class="card p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-border pb-3">
            <div class="flex items-center gap-2">
                <span class="text-lg">📊</span>
                <div>
                    <h3 class="text-sm font-bold text-text-main">Matriks Rekapitulasi Presensi Bulanan</h3>
                    <p class="text-xs text-text-muted">Akumulasi presensi kehadiran siswa per bulan dalam tahun ajaran</p>
                </div>
            </div>
            <span class="text-xs text-text-muted">Total: {{ count($rekapBulanan) }} Bulan Terdata</span>
        </div>

        @if (empty($rekapBulanan))
            <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                Belum ada data akumulasi bulanan untuk filter yang dipilih.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-surface-raised text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                        <tr>
                            <th class="p-3 w-10 text-center">No</th>
                            <th class="p-3">Periode Bulan</th>
                            <th class="p-3 text-center">Hari Efektif</th>
                            <th class="p-3 text-center font-bold text-emerald-600">Hadir (H)</th>
                            <th class="p-3 text-center font-bold text-amber-600">Sakit (S)</th>
                            <th class="p-3 text-center font-bold text-blue-600">Izin (I)</th>
                            <th class="p-3 text-center font-bold text-rose-600">Alpa (A)</th>
                            <th class="p-3 text-center font-bold">Persentase</th>
                            <th class="p-3 text-center">Evaluasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($rekapBulanan as $idx => $rb)
                            <tr class="hover:bg-surface-hover/50 transition-colors">
                                <td class="p-3 text-center text-text-muted font-mono">{{ $idx + 1 }}</td>
                                <td class="p-3 font-bold text-text-main">{{ $rb['periode'] }}</td>
                                <td class="p-3 text-center font-mono font-medium text-text-secondary">{{ $rb['total'] }}</td>
                                <td class="p-3 text-center font-mono font-bold text-emerald-600 bg-emerald-500/5">{{ $rb['H'] }}</td>
                                <td class="p-3 text-center font-mono font-medium text-amber-600">{{ $rb['S'] }}</td>
                                <td class="p-3 text-center font-mono font-medium text-blue-600">{{ $rb['I'] }}</td>
                                <td class="p-3 text-center font-mono font-bold {{ $rb['A'] > 0 ? 'text-rose-600 bg-rose-500/10' : 'text-text-muted' }}">{{ $rb['A'] }}</td>
                                <td class="p-3 text-center font-mono font-black text-sm {{ $rb['persen'] >= 90 ? 'text-emerald-600' : ($rb['persen'] >= 80 ? 'text-amber-600' : 'text-rose-600') }}">
                                    {{ $rb['persen'] }}%
                                </td>
                                <td class="p-3 text-center">
                                    @if ($rb['persen'] >= 95)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600">
                                            Sangat Disiplin
                                        </span>
                                    @elseif ($rb['persen'] >= 85)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-600">
                                            Baik
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600">
                                            Perlu Perhatian
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- TABEL 2: LOG RIWAYAT PRESENSI HARIAN -->
    <div class="card p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-border pb-3">
            <div class="flex items-center gap-2">
                <span class="text-lg">🕒</span>
                <div>
                    <h3 class="text-sm font-bold text-text-main">Log Riwayat Presensi Harian</h3>
                    <p class="text-xs text-text-muted">Catatan presensi per tanggal kehadiran</p>
                </div>
            </div>
        </div>

        @if (!$riwayatPresensi || $riwayatPresensi->isEmpty())
            <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                Belum ada data riwayat presensi harian untuk filter yang dipilih.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-surface-raised text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                        <tr>
                            <th class="p-3 w-10 text-center">No</th>
                            <th class="p-3">Hari & Tanggal</th>
                            <th class="p-3">Semester</th>
                            <th class="p-3">Rombel</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($riwayatPresensi as $idx => $r)
                            @php
                                $val = $r->status instanceof \App\Enums\StatusAbsensi ? $r->status->value : $r->status;
                                $isH = in_array($val, ['Hadir', 'H']);
                                $isS = in_array($val, ['Sakit', 'S']);
                                $isI = in_array($val, ['Izin', 'I']);
                                $isA = in_array($val, ['Alpa', 'A']);
                            @endphp
                            <tr class="hover:bg-surface-hover/50 transition-colors {{ $isA ? 'bg-rose-500/5' : '' }}">
                                <td class="p-3 text-center text-text-muted font-mono">{{ $riwayatPresensi->firstItem() + $idx }}</td>
                                <td class="p-3 font-semibold text-text-main">
                                    {{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('l, d F Y') }}
                                </td>
                                <td class="p-3 text-text-secondary">
                                    {{ $r->semester?->tahunAjaran?->nama }} &bull; {{ $r->semester?->nama ?? 'Semester ' . $r->semester_id }}
                                </td>
                                <td class="p-3 text-text-secondary">
                                    {{ $r->rombel?->nama ?? '-' }}
                                </td>
                                <td class="p-3 text-center">
                                    @if ($isH)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                            Hadir
                                        </span>
                                    @elseif ($isS)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20">
                                            Sakit
                                        </span>
                                    @elseif ($isI)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-600 border border-blue-500/20">
                                            Izin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600 border border-rose-500/20">
                                            Alpa
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-text-muted">
                                    {{ $r->keterangan ?: '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $riwayatPresensi->links() }}
            </div>
        @endif
    </div>
</div>
