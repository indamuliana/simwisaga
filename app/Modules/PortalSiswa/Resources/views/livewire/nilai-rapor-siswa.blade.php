<div class="space-y-6">
    <!-- Header Card -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-surface p-6 rounded-2xl border border-border shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                <span>📊 Portal Siswa</span>
                <span>&bull;</span>
                <span>Akademik & Hasil Belajar</span>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">Nilai Akademik, Ledger & Rapor</h1>
            <p class="text-xs text-text-muted mt-1">
                Pantau rincian seluruh nilai mapel (Formatif TP, Harian 9M, Asesmen Harian AH, Sumatif STS & SAS), status KKM, dan pratinjau dokumen rapor resmi.
            </p>
            @if ($historicalRombel)
                <div class="flex items-center gap-2.5 mt-2.5 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-primary-soft text-primary border border-primary/20">
                        <span>🏫 Rombel:</span>
                        <span>{{ $historicalRombel->nama }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-surface-raised text-text-secondary border border-border">
                        <span>👨‍🏫 Wali Kelas:</span>
                        <strong class="text-text-main">{{ $historicalWaliKelas?->nama_lengkap ?? 'Belum Ditugaskan' }}</strong>
                    </span>
                    @if ($siswa?->rayon)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-surface-raised text-text-secondary border border-border">
                            <span>📍 Rayon:</span>
                            <strong class="text-text-main">{{ $siswa->rayon->nama }}</strong>
                        </span>
                    @endif
                </div>
            @endif
        </div>

        @if ($siswa)
            <!-- Tombol Cetak Dokumen Rapor -->
            <div class="flex flex-wrap items-center gap-2 self-start lg:self-auto">
                <a href="{{ route('rapor.cetak.wikrama', ['siswa_id' => $siswa->id, 'semester_id' => $selectedSemesterId]) }}" target="_blank" class="px-3.5 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs">
                    <span>🖨️</span>
                    <span>Cetak Rapor Wikrama</span>
                </a>
                <a href="{{ route('rapor.cetak.pemerintah', ['siswa_id' => $siswa->id, 'semester_id' => $selectedSemesterId]) }}" target="_blank" class="px-3.5 py-2.5 rounded-xl bg-surface-raised hover:bg-surface-hover text-text-main border border-border text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs">
                    <span>🏛️</span>
                    <span>Cetak Rapor Pemerintah</span>
                </a>
                <a href="{{ route('penilaian.transkrip.pdf', $siswa->id) }}" target="_blank" class="px-3 py-2.5 rounded-xl bg-surface-raised hover:bg-surface-hover text-text-secondary hover:text-text-main border border-border text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs">
                    <span>📄</span>
                    <span>Transkrip PDF</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Filter Periode Semester & Tab Mode Navigasi -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface p-3 rounded-2xl border border-border shadow-xs">
        <div class="flex flex-wrap items-center gap-1.5">
            <button type="button" wire:click="$set('viewMode', 'ledger')" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $viewMode === 'ledger' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                <span>📋 Buku Ledger Nilai</span>
            </button>
            <button type="button" wire:click="$set('viewMode', 'rapor_wikrama')" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $viewMode === 'rapor_wikrama' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                <span>📜 Rapor Wikrama (9M)</span>
            </button>
            <button type="button" wire:click="$set('viewMode', 'rapor_pemerintah')" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $viewMode === 'rapor_pemerintah' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                <span>🏛️ Rapor Kurikulum Merdeka</span>
            </button>
            <button type="button" wire:click="$set('viewMode', 'transkrip')" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $viewMode === 'transkrip' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                <span>📈 Transkrip Kumulatif</span>
            </button>
        </div>

        <div class="flex items-center gap-2 text-xs">
            <span class="text-text-muted whitespace-nowrap">Pilih Semester:</span>
            <select wire:model.live="selectedSemesterId" class="rounded-xl border border-border bg-surface px-3 py-1.5 text-xs text-text-main font-semibold focus:border-primary focus:outline-hidden">
                @foreach ($semesters as $s)
                    <option value="{{ $s->id }}">{{ $s->tahunAjaran?->nama }} - {{ $s->nama }} {{ $s->is_active ? '(Aktif)' : '' }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 1: BUKU LEDGER NILAI PRIBADI                                    -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    @if ($viewMode === 'ledger')
        <div class="space-y-6">
            <!-- Statistik Ringkasan Semester -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="card p-4 text-center">
                    <span class="text-[10px] uppercase font-bold text-text-muted block">Rata-rata Semester</span>
                    <span class="text-2xl font-black text-primary font-mono mt-1 block">{{ number_format($summary['rata_rata'], 2) }}</span>
                    <span class="text-[10px] text-text-muted">Total {{ $summary['total_mapel'] }} Mapel</span>
                </div>
                <div class="card p-4 text-center border-l-4 border-l-emerald-500">
                    <span class="text-[10px] uppercase font-bold text-emerald-600 block">Mapel Tuntas</span>
                    <span class="text-2xl font-black text-emerald-600 font-mono mt-1 block">{{ $summary['tuntas_count'] }}</span>
                    <span class="text-[10px] text-text-muted">&ge; KKM Mata Pelajaran</span>
                </div>
                <div class="card p-4 text-center border-l-4 {{ $summary['remedial_count'] > 0 ? 'border-l-rose-500 bg-rose-500/5' : 'border-l-border' }}">
                    <span class="text-[10px] uppercase font-bold {{ $summary['remedial_count'] > 0 ? 'text-rose-600' : 'text-text-muted' }} block">Perlu Remedial</span>
                    <span class="text-2xl font-black {{ $summary['remedial_count'] > 0 ? 'text-rose-600' : 'text-text-muted' }} font-mono mt-1 block">{{ $summary['remedial_count'] }}</span>
                    <span class="text-[10px] {{ $summary['remedial_count'] > 0 ? 'text-rose-600 font-bold' : 'text-text-muted' }}">Di Bawah KKM</span>
                </div>
                <div class="card p-4 text-center border-l-4 border-l-blue-500">
                    <span class="text-[10px] uppercase font-bold text-blue-600 block">Rentang Nilai</span>
                    <span class="text-lg font-black text-blue-600 font-mono mt-1 block">{{ $summary['nilai_terendah'] }} - {{ $summary['nilai_tertinggi'] }}</span>
                    <span class="text-[10px] text-text-muted">Terendah s.d. Tertinggi</span>
                </div>
            </div>

            <!-- Alert Indikator Remedial jika ada -->
            @if ($summary['remedial_count'] > 0)
                <div class="rounded-2xl border border-rose-500/30 bg-rose-500/10 p-4 text-xs text-rose-800 dark:text-rose-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">⚠️</span>
                        <div>
                            <strong class="font-bold">Perhatian: Anda memiliki {{ $summary['remedial_count'] }} mata pelajaran yang belum memenuhi KKM!</strong>
                            <p class="text-[11px] text-rose-700 dark:text-rose-300 mt-0.5">Segera hubungi guru mata pelajaran terkait untuk mengikuti program remedial atau penugasan perbaikan materi.</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-3.5 text-xs text-emerald-800 dark:text-emerald-200 flex items-center gap-2.5">
                    <span>🎉</span>
                    <span><strong>Selamat!</strong> Seluruh capaian mata pelajaran pada semester ini telah memenuhi kriteria ketuntasan minimal (KKM).</span>
                </div>
            @endif

            <!-- Tabel Ledger Rincian Nilai -->
            <div class="card p-6 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-border pb-3 gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-text-main">Rincian Komponen Nilai & Evaluasi Ketuntasan</h3>
                        <p class="text-xs text-text-muted">Rincian nilai Formatif (TP), STS, SAS, Nilai Akhir (NA), dan tombol untuk melihat rincian asesmen harian lengkap.</p>
                    </div>
                    <span class="text-xs text-text-muted font-medium">Semester: {{ $selectedSemester?->nama }}</span>
                </div>

                @if (empty($ledgerRows))
                    <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                        Belum ada data nilai yang dipublikasikan untuk semester ini.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-surface-raised text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                                <tr>
                                    <th class="p-3 w-10 text-center">No</th>
                                    <th class="p-3">Mata Pelajaran & Guru</th>
                                    <th class="p-3 text-center">Formatif (TP)</th>
                                    <th class="p-3 text-center">STS</th>
                                    <th class="p-3 text-center">SAS</th>
                                    <th class="p-3 text-center">KKM</th>
                                    <th class="p-3 text-center font-bold">Nilai Akhir</th>
                                    <th class="p-3 text-center">Predikat</th>
                                    <th class="p-3 text-center">Status</th>
                                    <th class="p-3 text-center w-28">Rincian</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach ($ledgerRows as $idx => $row)
                                    <tr class="hover:bg-surface-hover/50 transition-colors {{ $row['is_remedial'] ? 'bg-rose-500/5' : '' }}">
                                        <td class="p-3 text-center text-text-muted font-mono font-bold">{{ $idx + 1 }}</td>
                                        <td class="p-3">
                                            <div class="font-bold text-text-main text-xs sm:text-sm">{{ $row['mapel_nama'] }}</div>
                                            <div class="text-[11px] text-text-muted mt-0.5 flex items-center gap-2">
                                                <span class="px-1.5 py-0.2 rounded bg-surface-raised border border-border text-[10px] font-semibold uppercase">{{ $row['kelompok'] }}</span>
                                                <span>&bull;</span>
                                                <span class="text-text-secondary">{{ $row['guru'] }}</span>
                                            </div>
                                            <!-- Jika ada TP yang perlu perbaikan -->
                                            @if (!empty($row['tp_remedial']))
                                                <div class="mt-2 p-2 rounded-lg bg-rose-500/10 border border-rose-500/20 text-[11px] text-rose-700 dark:text-rose-300">
                                                    <span class="font-bold block mb-0.5">⚠️ Perlu Remedial Materi / TP:</span>
                                                    <ul class="list-disc list-inside space-y-0.5">
                                                        @foreach ($row['tp_remedial'] as $tpr)
                                                            <li><strong class="font-mono">[{{ $tpr['kode'] }}]</strong> {{ $tpr['deskripsi'] }} (Nilai: <span class="font-mono font-bold">{{ $tpr['nilai'] }}</span>)</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="p-3 text-center font-mono font-medium text-text-secondary">
                                            {{ $row['avg_formatif'] ?: '-' }}
                                        </td>
                                        <td class="p-3 text-center font-mono font-medium text-text-secondary">
                                            {{ $row['sts'] !== null ? $row['sts'] : '-' }}
                                        </td>
                                        <td class="p-3 text-center font-mono font-medium text-text-secondary">
                                            {{ $row['sas'] !== null ? $row['sas'] : '-' }}
                                        </td>
                                        <td class="p-3 text-center font-mono font-bold text-text-muted">
                                            {{ $row['kkm'] }}
                                        </td>
                                        <td class="p-3 text-center font-mono font-black text-sm {{ $row['is_remedial'] ? 'text-rose-600 bg-rose-500/10 rounded-lg' : 'text-primary' }}">
                                            {{ number_format($row['nilai_akhir'], 1) }}
                                        </td>
                                        <td class="p-3 text-center">
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-black {{ $row['predikat'] === 'A' ? 'bg-emerald-500/10 text-emerald-600' : ($row['predikat'] === 'B' ? 'bg-blue-500/10 text-blue-600' : 'bg-amber-500/10 text-amber-600') }}">
                                                {{ $row['predikat'] }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-center whitespace-nowrap">
                                            @if ($row['is_remedial'])
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600 border border-rose-500/20">
                                                    <span>⚠️</span>
                                                    <span>Perlu Remedial</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                                    <span>✓</span>
                                                    <span>Tuntas</span>
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-3 text-center whitespace-nowrap">
                                            <button
                                                type="button"
                                                wire:click="openDetailModal('{{ $row['type'] }}', {{ $row['id'] }})"
                                                class="px-2.5 py-1.5 rounded-lg bg-primary hover:bg-primary-hover text-white text-[11px] font-bold transition-all inline-flex items-center gap-1 shadow-2xs cursor-pointer"
                                                title="Lihat Rincian Seluruh TP, Nilai Harian, dan Asesmen"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>Detail</span>
                                            </button>
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
                            <span class="text-[11px] text-text-muted">{{ $historicalWaliKelas?->nama_lengkap ?? 'Wali Kelas' }}</span>
                        </div>
                    </div>
                    <div class="p-4 rounded-xl bg-surface border border-border/80 text-xs text-text-secondary leading-relaxed">
                        {{ $catatanWali->catatan ?: 'Tetap semangat dalam belajar, pertahankan prestasi, dan perbaiki materi yang masih belum tuntas!' }}
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 2: PRATINJAU RAPOR WIKRAMA (9M)                                 -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    @if ($viewMode === 'rapor_wikrama')
        <div class="card p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-border pb-4 gap-3">
                <div>
                    <h3 class="text-sm font-bold text-text-main">Pratinjau Rapor Wikrama (9M)</h3>
                    <p class="text-xs text-text-muted">Capaian nilai kompetensi, rata-rata TP, nilai harian (NH 9M), dan narasi pembiasaan karakter khas SMK Wikrama.</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-primary-soft text-primary border border-primary/20 self-start sm:self-auto">
                    Periode Aktif: {{ $availablePeriodes9M[$selectedPeriode9M] ?? $selectedPeriode9M }}
                </span>
            </div>

            <!-- Sub-Filter Periode 9 Minggu -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-surface-raised p-3 rounded-xl border border-border">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-text-muted">Pilih Periode 9 Minggu:</span>
                    <div class="flex flex-wrap items-center gap-1.5">
                        @foreach ($availablePeriodes9M as $pKey => $pLabel)
                            <button
                                type="button"
                                wire:click="$set('selectedPeriode9M', '{{ $pKey }}')"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $selectedPeriode9M === $pKey ? 'bg-primary text-white shadow-2xs' : 'bg-surface hover:bg-surface-hover text-text-secondary border border-border' }}"
                            >
                                {{ $pLabel }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('rapor.cetak.wikrama', ['siswa_id' => $siswa->id, 'semester_id' => $selectedSemesterId, 'periode_id' => $matchingPeriodeId]) }}" target="_blank" class="px-3.5 py-1.5 rounded-lg bg-primary hover:bg-primary-hover text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs self-start sm:self-auto">
                    <span>🖨️</span>
                    <span>Cetak PDF {{ $selectedPeriode9M }}</span>
                </a>
            </div>

            @if (empty($raporWikramaRecords))
                <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                    Data Rapor Wikrama belum tersedia untuk periode {{ $selectedPeriode9M }} semester ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-surface-raised text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                            <tr>
                                <th class="p-3 w-10 text-center">No</th>
                                <th class="p-3">Mata Pelajaran</th>
                                <th class="p-3 text-center">KKM</th>
                                <th class="p-3 text-center">Rata TP</th>
                                <th class="p-3 text-center">Harian (9M)</th>
                                <th class="p-3 text-center">Sumatif</th>
                                <th class="p-3 text-center font-bold">Nilai Akhir</th>
                                <th class="p-3 text-center">Predikat</th>
                                <th class="p-3">Narasi Capaian Pembiasaan 9M</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($raporWikramaRecords as $idx => $rec)
                                <tr class="hover:bg-surface-hover/50 transition-colors">
                                    <td class="p-3 text-center text-text-muted font-mono font-bold">{{ $idx + 1 }}</td>
                                    <td class="p-3">
                                        <div class="font-bold text-text-main text-xs">{{ $rec['mapel_nama'] }}</div>
                                        <div class="text-[10px] text-text-muted">{{ $rec['kelompok'] }}</div>
                                    </td>
                                    <td class="p-3 text-center font-mono text-text-muted font-bold">{{ $rec['kkm'] }}</td>
                                    <td class="p-3 text-center font-mono text-text-secondary">{{ $rec['rata_tp'] !== null ? $rec['rata_tp'] : '-' }}</td>
                                    <td class="p-3 text-center font-mono font-medium text-emerald-600">{{ $rec['nh'] !== null ? $rec['nh'] : '-' }}</td>
                                    <td class="p-3 text-center font-mono text-text-secondary">{{ $rec['sumatif'] !== null ? $rec['sumatif'] : '-' }}</td>
                                    <td class="p-3 text-center font-mono font-black text-sm text-primary">{{ number_format($rec['nilai_akhir'], 1) }}</td>
                                    <td class="p-3 text-center">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-black {{ ($rec['predikat'] ?? '') === 'A' || ($rec['predikat'] ?? '') === 'Mahir' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-blue-500/10 text-blue-600' }}">
                                            {{ $rec['predikat'] }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-xs text-text-secondary leading-relaxed max-w-md">
                                        {{ $rec['narasi_capaian'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 3: PRATINJAU RAPOR PEMERINTAH (KURIKULUM MERDEKA)               -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    @if ($viewMode === 'rapor_pemerintah')
        <div class="card p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-border pb-4 gap-3">
                <div>
                    <h3 class="text-sm font-bold text-text-main">Pratinjau Rapor Pemerintah (Kurikulum Merdeka)</h3>
                    <p class="text-xs text-text-muted">Laporan Hasil Belajar resmi format standar Kementerian Pendidikan dengan rincian capaian kompetensi tertinggi dan yang perlu bimbingan.</p>
                </div>
                <a href="{{ route('rapor.cetak.pemerintah', ['siswa_id' => $siswa->id, 'semester_id' => $selectedSemesterId]) }}" target="_blank" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition-all flex items-center gap-2 self-start sm:self-auto shadow-2xs">
                    <span>🏛️</span>
                    <span>Unduh PDF Rapor Pemerintah</span>
                </a>
            </div>

            @if (empty($raporPemerintahRecords))
                <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                    Data Rapor Pemerintah belum tersedia untuk periode semester ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-surface-raised text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                            <tr>
                                <th class="p-3 w-10 text-center">No</th>
                                <th class="p-3">Mata Pelajaran</th>
                                <th class="p-3 text-center font-bold">Nilai Akhir</th>
                                <th class="p-3 text-center">Capaian Predikat</th>
                                <th class="p-3">Capaian Kompetensi Tertinggi &amp; Yang Perlu Pendampingan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($raporPemerintahRecords as $idx => $rec)
                                <tr class="hover:bg-surface-hover/50 transition-colors">
                                    <td class="p-3 text-center text-text-muted font-mono font-bold">{{ $idx + 1 }}</td>
                                    <td class="p-3">
                                        <div class="font-bold text-text-main text-xs">{{ $rec['mapel_nama'] }}</div>
                                        <div class="text-[10px] text-text-muted">{{ $rec['kelompok'] }}</div>
                                    </td>
                                    <td class="p-3 text-center font-mono font-black text-sm text-primary">{{ number_format($rec['nilai_akhir'], 1) }}</td>
                                    <td class="p-3 text-center">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-black {{ ($rec['predikat'] ?? '') === 'A' || ($rec['predikat'] ?? '') === 'Mahir' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-blue-500/10 text-blue-600' }}">
                                            {{ $rec['predikat'] }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-xs text-text-secondary leading-relaxed space-y-1.5 max-w-lg">
                                        @if (!empty($rec['deskripsi_tertinggi']))
                                            <div class="p-2 rounded-lg bg-emerald-500/5 border border-emerald-500/20">
                                                <strong class="text-emerald-700 dark:text-emerald-300 font-semibold block mb-0.5">🌟 Menunjukkan penguasaan sangat baik dalam:</strong>
                                                <span>{{ $rec['deskripsi_tertinggi'] }}</span>
                                            </div>
                                        @endif
                                        @if (!empty($rec['deskripsi_terendah']))
                                            <div class="p-2 rounded-lg bg-amber-500/5 border border-amber-500/20">
                                                <strong class="text-amber-700 dark:text-amber-300 font-semibold block mb-0.5">💡 Perlu pendampingan dan bimbingan dalam:</strong>
                                                <span>{{ $rec['deskripsi_terendah'] }}</span>
                                            </div>
                                        @endif
                                        @if (empty($rec['deskripsi_tertinggi']) && empty($rec['deskripsi_terendah']))
                                            <div class="text-text-muted">{{ $rec['capaian_kompetensi'] }}</div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 4: TRANSKRIP KUMULATIF (SEMESTER 1 S.D. 6)                      -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    @if ($viewMode === 'transkrip')
        <div class="card p-6 space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-border pb-4 gap-4">
                <div>
                    <h3 class="text-sm font-bold text-text-main">Rekapitulasi Transkrip Kumulatif (Semester 1 s.d. 6)</h3>
                    <p class="text-xs text-text-muted">Capaian nilai kumulatif seluruh mata pelajaran yang telah diselesaikan selama masa studi.</p>
                </div>
                @if (!empty($transkripData['summary']))
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="px-4 py-2 rounded-xl bg-primary/10 border border-primary/20 text-center">
                            <span class="text-[10px] font-bold text-primary uppercase tracking-wider block">Nilai Kumulatif</span>
                            <span class="text-xl font-black text-primary font-mono">{{ number_format($transkripData['summary']['nilai_kumulatif'] ?? 0, 2) }}</span>
                        </div>
                        <div class="px-4 py-2 rounded-xl bg-surface-raised border border-border text-center">
                            <span class="text-[10px] font-bold text-text-muted uppercase tracking-wider block">Predikat</span>
                            <span class="text-sm font-bold text-text-main">{{ $transkripData['summary']['predikat'] ?? '-' }}</span>
                        </div>
                        @if (!empty($transkripData['summary']['status_kelulusan']))
                            <div class="px-4 py-2 rounded-xl {{ ($transkripData['summary']['is_lulus'] ?? false) ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-600 border border-amber-500/20' }} text-center">
                                <span class="text-[10px] font-bold uppercase tracking-wider block">Status Kelulusan</span>
                                <span class="text-xs font-black">{{ $transkripData['summary']['status_kelulusan'] }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            @if (empty($transkripData['grouped_mapels']))
                <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                    Data transkrip kumulatif belum tersedia atau belum ada nilai yang tervalidasi.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-surface-raised text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                            <tr>
                                <th class="p-3 w-10 text-center">No</th>
                                <th class="p-3">Mata Pelajaran</th>
                                <th class="p-2 text-center w-14">KKM</th>
                                <th class="p-2 text-center w-12">S1</th>
                                <th class="p-2 text-center w-12">S2</th>
                                <th class="p-2 text-center w-12">S3</th>
                                <th class="p-2 text-center w-12">S4</th>
                                <th class="p-2 text-center w-12">S5</th>
                                <th class="p-2 text-center w-12">S6</th>
                                <th class="p-2 text-center font-bold w-16">Rata-rata</th>
                                <th class="p-2 text-center w-14">Predikat</th>
                                <th class="p-2 text-center w-20">Ketuntasan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @php $globalNo = 1; @endphp
                            @foreach ($transkripData['grouped_mapels'] as $group)
                                <tr class="bg-surface-raised/70 font-bold text-text-main text-[11px]">
                                    <td colspan="12" class="p-2.5 px-3 text-primary uppercase tracking-wide">
                                        Kelompok {{ $group['kelompok_kode'] }}: {{ $group['kelompok_nama'] }}
                                    </td>
                                </tr>
                                @foreach ($group['items'] as $s)
                                    <tr class="hover:bg-surface-hover/50 transition-colors">
                                        <td class="p-2 text-center text-text-muted font-mono">{{ $globalNo++ }}</td>
                                        <td class="p-2.5 font-medium text-text-main">
                                            <span>{{ $s['nama'] }}</span>
                                            <span class="text-[10px] text-text-muted font-mono block">Kode: {{ $s['kode'] }}</span>
                                        </td>
                                        <td class="p-2 text-center font-mono text-text-muted">{{ $s['kkm'] }}</td>
                                        @for ($i = 1; $i <= 6; $i++)
                                            <td class="p-2 text-center font-mono text-text-secondary">
                                                {{ isset($s['semesters'][$i]) && $s['semesters'][$i] !== null ? number_format($s['semesters'][$i], 1) : '-' }}
                                            </td>
                                        @endfor
                                        <td class="p-2 text-center font-mono font-black text-primary">
                                            {{ isset($s['rata_mapel']) && $s['rata_mapel'] !== null ? number_format($s['rata_mapel'], 1) : '-' }}
                                        </td>
                                        <td class="p-2 text-center">
                                            @if (!empty($s['predikat']) && $s['predikat'] !== '-')
                                                <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-surface-raised text-text-secondary border border-border">
                                                    {{ $s['predikat'] }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="p-2 text-center whitespace-nowrap">
                                            @if ($s['is_tuntas'])
                                                <span class="text-[10px] font-bold text-emerald-600">✓ Tuntas</span>
                                            @else
                                                <span class="text-[10px] font-bold text-rose-600">⚠️ Remedial</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            <!-- Baris Rata-rata Semester -->
                            @if (!empty($transkripData['semester_averages']))
                                <tr class="bg-primary/5 font-bold border-t-2 border-primary/20 text-xs text-text-main">
                                    <td colspan="3" class="p-3 text-right uppercase tracking-wider text-primary">
                                        Rata-rata Tiap Semester:
                                    </td>
                                    @for ($i = 1; $i <= 6; $i++)
                                        <td class="p-2 text-center font-mono font-black text-primary">
                                            {{ isset($transkripData['semester_averages'][$i]) && $transkripData['semester_averages'][$i] !== null ? number_format($transkripData['semester_averages'][$i], 1) : '-' }}
                                        </td>
                                    @endfor
                                    <td class="p-2 text-center font-mono font-black text-primary">
                                        {{ number_format($transkripData['summary']['nilai_kumulatif'] ?? 0, 1) }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- MODAL RINCIAN NILAI MATA PELAJARAN (TP, HARIAN, AH, SUMATIF)        -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    @if ($showDetailModal && $selectedDetail)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" wire:click="closeDetailModal"></div>

            <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
                <div class="relative w-full max-w-4xl transform overflow-hidden rounded-2xl bg-surface border border-border p-6 text-left shadow-2xl transition-all space-y-6">
                    <!-- Modal Header -->
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between border-b border-border pb-4 gap-3">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-primary text-white uppercase tracking-wider">
                                    {{ $selectedDetail['kelompok'] }}
                                </span>
                                @if ($selectedDetail['mapel_kode'])
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-surface-raised border border-border text-text-secondary">
                                        {{ $selectedDetail['mapel_kode'] }}
                                    </span>
                                @endif
                                @if ($selectedDetail['is_remedial'])
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-600 border border-rose-500/20">
                                        ⚠️ Perlu Remedial
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                        ✓ Tuntas KKM
                                    </span>
                                @endif
                            </div>
                            <h2 class="text-xl font-black text-text-main tracking-tight">
                                {{ $selectedDetail['mapel_nama'] }}
                            </h2>
                            <p class="text-xs text-text-muted">
                                Guru Pengampu: <strong class="text-text-main font-semibold">{{ $selectedDetail['guru'] }}</strong> &bull; KKM Standar: <strong class="text-text-main font-mono">{{ $selectedDetail['kkm'] }}</strong>
                            </p>
                        </div>

                        <!-- KPI Nilai Akhir -->
                        <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                            <div class="px-4 py-2 rounded-xl bg-primary-soft text-center border border-primary/20">
                                <span class="text-[10px] font-bold uppercase text-primary tracking-wider block">Nilai Akhir</span>
                                <span class="text-xl font-black text-primary font-mono">{{ number_format($selectedDetail['nilai_akhir'], 1) }}</span>
                            </div>
                            <div class="px-3 py-2 rounded-xl bg-surface-raised border border-border text-center">
                                <span class="text-[10px] font-bold uppercase text-text-muted tracking-wider block">Predikat</span>
                                <span class="text-base font-black text-text-main">{{ $selectedDetail['predikat'] }}</span>
                            </div>
                            <button type="button" wire:click="closeDetailModal" class="p-2 rounded-xl text-text-muted hover:text-text-main hover:bg-surface-raised transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body (4 Bagian Terstruktur) -->
                    <div class="space-y-6 max-h-[65vh] overflow-y-auto pr-1">
                        <!-- BAGIAN 1: FORMATIF PER TUJUAN PEMBELAJARAN (TP) -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-text-main flex items-center gap-1.5">
                                    <span>🎯</span>
                                    <span>1. Capaian Nilai Formatif per Tujuan Pembelajaran (TP)</span>
                                </h3>
                                <span class="text-[11px] text-text-muted">Total: {{ count($selectedDetail['formatif_tps']) }} TP</span>
                            </div>

                            @if (empty($selectedDetail['formatif_tps']))
                                <div class="p-4 rounded-xl border border-dashed border-border text-xs text-text-muted text-center">
                                    Belum ada data nilai formatif TP yang diinputkan untuk mata pelajaran ini.
                                </div>
                            @else
                                <div class="overflow-x-auto rounded-xl border border-border">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-surface-raised text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                                            <tr>
                                                <th class="p-2.5 w-16 text-center">Kode</th>
                                                <th class="p-2.5 w-16 text-center">Periode</th>
                                                <th class="p-2.5 w-16 text-center">AH</th>
                                                <th class="p-2.5">Deskripsi Materi / Kompetensi TP</th>
                                                <th class="p-2.5 text-center w-16 font-bold">Nilai</th>
                                                <th class="p-2.5 text-center w-24">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border">
                                            @foreach ($selectedDetail['formatif_tps'] as $tp)
                                                <tr class="hover:bg-surface-hover/50 transition-colors {{ $tp['is_remedial'] ? 'bg-rose-500/5' : '' }}">
                                                    <td class="p-2.5 text-center font-mono font-bold text-primary">{{ $tp['kode'] }}</td>
                                                    <td class="p-2.5 text-center font-mono text-text-muted">{{ $tp['target_periode'] }}</td>
                                                    <td class="p-2.5 text-center font-mono text-text-muted">{{ $tp['alokasi_ah'] }}</td>
                                                    <td class="p-2.5 text-text-main leading-relaxed">{{ $tp['deskripsi'] }}</td>
                                                    <td class="p-2.5 text-center font-mono font-black {{ $tp['is_remedial'] ? 'text-rose-600 bg-rose-500/10 rounded' : 'text-text-main' }}">
                                                        {{ $tp['nilai'] !== null ? $tp['nilai'] : '-' }}
                                                    </td>
                                                    <td class="p-2.5 text-center whitespace-nowrap">
                                                        @if ($tp['is_remedial'])
                                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600 border border-rose-500/20">
                                                                Remedial
                                                            </span>
                                                        @else
                                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                                                Tuntas
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

                        <!-- BAGIAN 2: NILAI HARIAN (TARGET PERIODE 9M) -->
                        <div class="space-y-3">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-text-main flex items-center gap-1.5">
                                <span>📅</span>
                                <span>2. Nilai Harian (Target Periode 9M)</span>
                            </h3>

                            @if (empty($selectedDetail['nilai_harians']))
                                <div class="p-3.5 rounded-xl border border-dashed border-border text-xs text-text-muted text-center">
                                    Belum ada catatan nilai harian untuk mata pelajaran ini.
                                </div>
                            @else
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                    @foreach ($selectedDetail['nilai_harians'] as $nh)
                                        <div class="p-3 rounded-xl border border-border bg-surface-raised text-center">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-text-muted block">
                                                Periode {{ $nh['target_periode'] }}
                                            </span>
                                            <span class="text-lg font-black text-emerald-600 font-mono mt-0.5 block">
                                                {{ number_format($nh['nilai'], 1) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- BAGIAN 3: ASESMEN HARIAN (AH) -->
                        @if (!empty($selectedDetail['asesmen_harians']))
                            <div class="space-y-3">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-text-main flex items-center gap-1.5">
                                    <span>📝</span>
                                    <span>3. Asesmen Harian (AH)</span>
                                </h3>
                                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-2">
                                    @foreach ($selectedDetail['asesmen_harians'] as $ah)
                                        <div class="p-2.5 rounded-xl border border-border bg-surface-raised text-center">
                                            <span class="text-[10px] font-mono font-bold text-text-muted block">{{ $ah['kode_ah'] }}</span>
                                            <span class="text-base font-black text-text-main font-mono mt-0.5 block">
                                                {{ number_format($ah['nilai'], 1) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- BAGIAN 4: SUMATIF & CAPAIAN KOMPETENSI -->
                        <div class="space-y-3">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-text-main flex items-center gap-1.5">
                                <span>🏆</span>
                                <span>4. Nilai Sumatif (STS &amp; SAS) dan Capaian Kompetensi</span>
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="p-3.5 rounded-xl border border-border bg-surface-raised flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] uppercase font-bold text-text-muted block">Sumatif Tengah Semester (STS)</span>
                                        <span class="text-xs text-text-secondary">Ujian Penilaian Tengah Semester</span>
                                    </div>
                                    <span class="text-xl font-black text-primary font-mono">
                                        {{ $selectedDetail['sts'] !== null ? number_format($selectedDetail['sts'], 1) : '-' }}
                                    </span>
                                </div>

                                <div class="p-3.5 rounded-xl border border-border bg-surface-raised flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] uppercase font-bold text-text-muted block">Sumatif Akhir Semester (SAS)</span>
                                        <span class="text-xs text-text-secondary">Ujian Penilaian Akhir Semester</span>
                                    </div>
                                    <span class="text-xl font-black text-primary font-mono">
                                        {{ $selectedDetail['sas'] !== null ? number_format($selectedDetail['sas'], 1) : '-' }}
                                    </span>
                                </div>
                            </div>

                            @if (!empty($selectedDetail['deskripsi_tertinggi']) || !empty($selectedDetail['deskripsi_terendah']))
                                <div class="p-4 rounded-xl border border-border bg-surface-raised/40 space-y-2 text-xs">
                                    <span class="font-bold text-text-main block">Capaian Kompetensi Siswa:</span>
                                    @if (!empty($selectedDetail['deskripsi_tertinggi']))
                                        <div class="text-emerald-700 dark:text-emerald-300">
                                            <strong>🌟 Sangat Baik:</strong> {{ $selectedDetail['deskripsi_tertinggi'] }}
                                        </div>
                                    @endif
                                    @if (!empty($selectedDetail['deskripsi_terendah']))
                                        <div class="text-amber-700 dark:text-amber-300">
                                            <strong>💡 Perlu Bimbingan:</strong> {{ $selectedDetail['deskripsi_terendah'] }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end pt-3 border-t border-border">
                        <button type="button" wire:click="closeDetailModal" class="px-5 py-2 rounded-xl bg-surface-raised hover:bg-surface-hover border border-border text-xs font-bold text-text-main transition-colors cursor-pointer">
                            Tutup Rincian
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
