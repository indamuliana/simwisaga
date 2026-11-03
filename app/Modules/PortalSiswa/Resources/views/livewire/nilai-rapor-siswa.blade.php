<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface p-6 rounded-2xl border border-border shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                <span>📊 Portal Siswa</span>
                <span>&bull;</span>
                <span>Akademik</span>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">Nilai Akademik & Rapor</h1>
            <p class="text-xs text-text-muted mt-1">
                Pantau capaian nilai formatif, asesmen sumatif, catatan wali kelas, dan cetak lembar rapor resmi.
            </p>
        </div>

        @if ($siswa)
            <!-- Tombol Cetak Dokumen Rapor -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('rapor.cetak.wikrama', $siswa->id) }}" target="_blank" class="px-3 py-2 rounded-xl bg-primary-soft hover:bg-primary/20 text-primary border border-primary/30 text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs">
                    <span>🖨️</span>
                    <span>Rapor Wikrama</span>
                </a>
                <a href="{{ route('rapor.cetak.pemerintah', $siswa->id) }}" target="_blank" class="px-3 py-2 rounded-xl bg-surface-raised hover:bg-surface-hover text-text-main border border-border text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs">
                    <span>🏛️</span>
                    <span>Rapor Pemerintah</span>
                </a>
                <a href="{{ route('penilaian.transkrip.pdf', $siswa->id) }}" target="_blank" class="px-3 py-2 rounded-xl bg-surface-raised hover:bg-surface-hover text-text-main border border-border text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs">
                    <span>📄</span>
                    <span>Transkrip PDF</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Toggle Mode & Filter Semester -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface p-3 rounded-2xl border border-border shadow-xs">
        <div class="flex items-center gap-1.5">
            <button type="button" wire:click="$set('viewMode', 'semester')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $viewMode === 'semester' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                <span>📘 Nilai Per Semester</span>
            </button>
            <button type="button" wire:click="$set('viewMode', 'transkrip')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $viewMode === 'transkrip' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                <span>📜 Transkrip Nilai Kumulatif</span>
            </button>
        </div>

        @if ($viewMode === 'semester')
            <div class="flex items-center gap-2 text-xs">
                <span class="text-text-muted whitespace-nowrap">Pilih Semester:</span>
                <select wire:model.live="selectedSemesterId" class="rounded-xl border border-border bg-surface px-3 py-1.5 text-xs text-text-main font-semibold focus:border-primary focus:outline-hidden">
                    @foreach ($semesters as $s)
                        <option value="{{ $s->id }}">{{ $s->tahunAjaran?->nama }} - {{ $s->nama }} {{ $s->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    <!-- View 1: Nilai Per Semester -->
    @if ($viewMode === 'semester')
        <div class="space-y-6">
            <div class="card p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-sm font-bold text-text-main">Rincian Nilai Mata Pelajaran</h3>
                    <span class="text-xs text-text-muted">Total: {{ $nilaiSemesters->count() }} Mata Pelajaran</span>
                </div>

                @if ($nilaiSemesters->isEmpty())
                    <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                        Belum ada data nilai yang dipublikasikan untuk semester ini.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-surface-raised text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                                <tr>
                                    <th class="p-3">No</th>
                                    <th class="p-3">Mata Pelajaran</th>
                                    <th class="p-3">Guru Pengampu</th>
                                    <th class="p-3 text-center">Formatif</th>
                                    <th class="p-3 text-center">Sumatif</th>
                                    <th class="p-3 text-center font-bold">Nilai Akhir</th>
                                    <th class="p-3 text-center">Predikat</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach ($nilaiSemesters as $idx => $na)
                                    @php
                                        $mapel = $na->penugasanMengajar?->mapel;
                                        $guru = $na->penugasanMengajar?->guru;
                                        $score = (float) $na->nilai_akhir;
                                        $predikat = \App\Modules\Penilaian\Services\TranskripNilaiService::getPredikat($score);
                                    @endphp
                                    <tr class="hover:bg-surface-hover/50 transition-colors">
                                        <td class="p-3 text-text-muted w-10">{{ $idx + 1 }}</td>
                                        <td class="p-3">
                                            <div class="font-bold text-text-main">{{ $mapel?->nama }}</div>
                                            <div class="text-[10px] text-text-muted">{{ $mapel?->kelompokMapel?->nama ?: 'Umum' }}</div>
                                        </td>
                                        <td class="p-3 text-text-secondary">{{ $guru?->nama_lengkap ?: '-' }}</td>
                                        <td class="p-3 text-center font-mono font-medium text-text-secondary">{{ $na->nilai_formatif ?? '-' }}</td>
                                        <td class="p-3 text-center font-mono font-medium text-text-secondary">{{ $na->nilai_sumatif ?? '-' }}</td>
                                        <td class="p-3 text-center font-mono font-bold text-sm text-primary">{{ number_format($score, 1) }}</td>
                                        <td class="p-3 text-center">
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-black {{ $predikat === 'A' ? 'bg-emerald-500/10 text-emerald-600' : ($predikat === 'B' ? 'bg-blue-500/10 text-blue-600' : 'bg-amber-500/10 text-amber-600') }}">
                                                {{ $predikat }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Catatan Wali Kelas Card -->
            @if ($catatanWali)
                <div class="card p-6 bg-linear-to-r from-primary-soft/30 via-surface to-surface border border-primary/20 space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">✍️</span>
                        <div>
                            <h3 class="text-sm font-bold text-text-main">Catatan & Pesan Wali Kelas</h3>
                            <span class="text-[11px] text-text-muted">{{ $siswa?->rombel?->waliKelas?->nama_lengkap ?? 'Wali Kelas' }}</span>
                        </div>
                    </div>
                    <div class="p-4 rounded-xl bg-surface border border-border/80 text-xs text-text-secondary leading-relaxed">
                        {{ $catatanWali->catatan ?: 'Tetap semangat dalam belajar dan tingkatkan terus prestasimu!' }}
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- View 2: Transkrip Nilai Kumulatif -->
    @if ($viewMode === 'transkrip')
        <div class="card p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-border pb-4 gap-2">
                <div>
                    <h3 class="text-sm font-bold text-text-main">Rekapitulasi Transkrip Kumulatif (Semester 1 s.d. 6)</h3>
                    <p class="text-xs text-text-muted">Rata-rata seluruh capaian nilai mata pelajaran selama menempuh pendidikan.</p>
                </div>
                @if (!empty($transkripData['overallAverage']))
                    <div class="px-4 py-2 rounded-xl bg-primary/10 border border-primary/20 text-center">
                        <span class="text-[10px] font-bold text-primary uppercase tracking-wider block">IP Kumulatif / Rata-rata</span>
                        <span class="text-lg font-black text-primary font-mono">{{ number_format($transkripData['overallAverage'], 2) }}</span>
                    </div>
                @endif
            </div>

            @if (empty($transkripData['groups']))
                <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                    Data transkrip belum tersedia atau belum ada nilai yang tervalidasi.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-surface-raised text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                            <tr>
                                <th class="p-3">Mata Pelajaran</th>
                                <th class="p-2 text-center">S1</th>
                                <th class="p-2 text-center">S2</th>
                                <th class="p-2 text-center">S3</th>
                                <th class="p-2 text-center">S4</th>
                                <th class="p-2 text-center">S5</th>
                                <th class="p-2 text-center">S6</th>
                                <th class="p-2 text-center font-bold">Rata-rata</th>
                                <th class="p-2 text-center">Predikat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($transkripData['groups'] as $groupName => $subjects)
                                <tr class="bg-surface-raised/40 font-bold text-text-main text-[11px]">
                                    <td colspan="9" class="p-2.5 text-primary">{{ $groupName }}</td>
                                </tr>
                                @foreach ($subjects as $s)
                                    <tr class="hover:bg-surface-hover/50 transition-colors">
                                        <td class="p-2.5 pl-4 font-medium text-text-main">{{ $s['nama'] }}</td>
                                        @for ($i = 1; $i <= 6; $i++)
                                            <td class="p-2 text-center font-mono text-text-secondary">
                                                {{ isset($s['semesters'][$i]) ? number_format($s['semesters'][$i], 1) : '-' }}
                                            </td>
                                        @endfor
                                        <td class="p-2 text-center font-mono font-bold text-primary">
                                            {{ isset($s['average']) ? number_format($s['average'], 1) : '-' }}
                                        </td>
                                        <td class="p-2 text-center">
                                            @if (!empty($s['predikat']))
                                                <span class="inline-flex px-1.5 py-0.2 rounded text-[10px] font-bold bg-surface-raised text-text-secondary border border-border">
                                                    {{ $s['predikat'] }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>
