<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface p-6 rounded-2xl border border-border shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                <span>📅 Portal Siswa</span>
                <span>&bull;</span>
                <span>Presensi</span>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">Rekap Kehadiran Siswa</h1>
            <p class="text-xs text-text-muted mt-1">
                Pantau kedisiplinan dan rekapitulasi kehadiran Anda secara kumulatif atau berdasarkan periode tingkat/semester.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="px-4 py-2.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-center">
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Tingkat Kehadiran</span>
                <span class="text-xl font-black text-emerald-600 font-mono">{{ $stats['persen'] }}%</span>
            </div>
        </div>
    </div>

    <!-- KPI Cards Statistik Kehadiran -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="card p-4 text-center">
            <span class="text-[10px] uppercase font-bold text-text-muted block">Hari Efektif</span>
            <span class="text-xl font-black text-text-main font-mono mt-1 block">{{ $stats['total'] }}</span>
            <span class="text-[10px] text-text-muted">Total Presensi</span>
        </div>
        <div class="card p-4 text-center border-l-4 border-l-emerald-500">
            <span class="text-[10px] uppercase font-bold text-emerald-600 block">Hadir (H)</span>
            <span class="text-xl font-black text-emerald-600 font-mono mt-1 block">{{ $stats['H'] }}</span>
            <span class="text-[10px] text-text-muted">Hari</span>
        </div>
        <div class="card p-4 text-center border-l-4 border-l-amber-500">
            <span class="text-[10px] uppercase font-bold text-amber-600 block">Sakit (S)</span>
            <span class="text-xl font-black text-amber-600 font-mono mt-1 block">{{ $stats['S'] }}</span>
            <span class="text-[10px] text-text-muted">Hari</span>
        </div>
        <div class="card p-4 text-center border-l-4 border-l-blue-500">
            <span class="text-[10px] uppercase font-bold text-blue-600 block">Izin (I)</span>
            <span class="text-xl font-black text-blue-600 font-mono mt-1 block">{{ $stats['I'] }}</span>
            <span class="text-[10px] text-text-muted">Hari</span>
        </div>
        <div class="card p-4 text-center border-l-4 border-l-rose-500 col-span-2 sm:col-span-1">
            <span class="text-[10px] uppercase font-bold text-rose-600 block">Alpa (A)</span>
            <span class="text-xl font-black text-rose-600 font-mono mt-1 block">{{ $stats['A'] }}</span>
            <span class="text-[10px] text-text-muted">Hari</span>
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
                    <option value="semua">Semua Periode</option>
                    @foreach ($semesters as $s)
                        <option value="{{ $s->id }}">{{ $s->tahunAjaran?->nama }} - {{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Table Log Riwayat Presensi -->
    <div class="card p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-border pb-3">
            <h3 class="text-sm font-bold text-text-main">Log Riwayat Presensi Harian</h3>
            <span class="text-xs text-text-muted">Menampilkan data presensi terbaru</span>
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
                            <th class="p-3">Tanggal</th>
                            <th class="p-3">Semester</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($riwayatPresensi as $item)
                            <tr class="hover:bg-surface-hover/50 transition-colors">
                                <td class="p-3 whitespace-nowrap">
                                    <div class="font-bold text-text-main">{{ $item->tanggal->translatedFormat('l, d F Y') }}</div>
                                    <div class="text-[10px] text-text-muted font-mono">{{ $item->tanggal->format('d/m/Y') }}</div>
                                </td>
                                <td class="p-3 whitespace-nowrap text-text-secondary">
                                    {{ $item->semester?->nama ?? '-' }}
                                </td>
                                <td class="p-3 text-center whitespace-nowrap">
                                    @if ($item->status === 'H')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                            Hadir
                                        </span>
                                    @elseif ($item->status === 'S')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20">
                                            Sakit
                                        </span>
                                    @elseif ($item->status === 'I')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-600 border border-blue-500/20">
                                            Izin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600 border border-rose-500/20">
                                            Alpa
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-text-secondary">
                                    {{ $item->keterangan ?: '-' }}
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
