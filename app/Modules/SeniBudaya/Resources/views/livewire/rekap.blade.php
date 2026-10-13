<div class="space-y-6">
    <!-- Header Navigasi -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-border pb-4">
        <div>
            <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Rekapitulasi Nilai & Kehadiran Seni Budaya
            </h2>
            <p class="text-sm text-text-secondary mt-1">
                Rekapitulasi capaian nilai mata pelajaran Seni Budaya per cabang kegiatan atau per rombongan belajar (kelas).
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-1.5 bg-surface-hover p-1 rounded-xl border border-border">
            <a href="{{ route('seni-budaya.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Dashboard
            </a>
            <a href="{{ route('seni-budaya.cabang') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Cabang Seni
            </a>
            <a href="{{ route('seni-budaya.anggota') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Keanggotaan Siswa
            </a>
            <a href="{{ route('seni-budaya.tp') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Tujuan Pembelajaran (TP)
            </a>
            <a href="{{ route('seni-budaya.rekap') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold bg-white dark:bg-gray-800 text-primary shadow-xs">
                Rekapitulasi Nilai
            </a>
        </div>
    </div>

    <!-- Filter Mode & Opsi -->
    <div class="card p-4 border border-border flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex flex-wrap items-center gap-3">
            <div class="inline-flex rounded-xl p-1 bg-surface-hover border border-border">
                <button type="button"
                        wire:click="$set('viewMode', 'cabang')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $viewMode === 'cabang' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                    Berdasarkan Cabang Seni
                </button>
                <button type="button"
                        wire:click="$set('viewMode', 'rombel')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $viewMode === 'rombel' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                    Berdasarkan Rombel / Kelas
                </button>
            </div>

            @if($viewMode === 'cabang')
                <select wire:model.live="selectedCabangId" class="input text-sm">
                    @foreach($cabangs as $c)
                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                    @endforeach
                </select>
            @else
                <select wire:model.live="selectedRombelId" class="input text-sm">
                    @foreach($rombels as $r)
                        <option value="{{ $r->id }}">{{ $r->nama }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        @if($viewMode === 'cabang' && $selectedCabangId)
            <a href="{{ route('seni-budaya.export.rekap', $selectedCabangId) }}" class="btn-secondary text-sm flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Ekspor Excel Cabang Ini
            </a>
        @endif
    </div>

    <!-- Tabel Rekapitulasi -->
    <div class="card overflow-hidden border border-border">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-hover text-text-secondary text-xs uppercase tracking-wider border-b border-border">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">NISN / NIS</th>
                        <th class="px-4 py-3">Nama Siswa</th>
                        @if($viewMode === 'cabang')
                            <th class="px-4 py-3">Kelas / Rombel</th>
                        @else
                            <th class="px-4 py-3">Cabang Seni</th>
                        @endif
                        <th class="px-4 py-3 text-center">STS 1</th>
                        <th class="px-4 py-3 text-center">SAS</th>
                        <th class="px-4 py-3 text-center">STS 2</th>
                        <th class="px-4 py-3 text-center">SAT</th>
                        <th class="px-4 py-3 text-center">Presensi (%)</th>
                        <th class="px-4 py-3 text-center">Nilai Akhir</th>
                        <th class="px-4 py-3 text-center">Predikat</th>
                        <th class="px-4 py-3">Deskripsi Capaian Rapor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-text-main">
                    @php $no = 1; @endphp
                    @forelse($rows as $a)
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="px-4 py-3 text-center text-text-muted">{{ $no++ }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-text-secondary">
                                {{ $a->siswa?->nisn ?? '-' }} <span class="text-text-muted">/ {{ $a->siswa?->nipd ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 font-medium">{{ $a->siswa?->nama ?? '-' }}</td>
                            @if($viewMode === 'cabang')
                                <td class="px-4 py-3 text-text-secondary">{{ $a->siswa?->rombel?->nama ?? '-' }}</td>
                            @else
                                <td class="px-4 py-3 font-semibold text-primary">{{ $a->seniBudaya?->nama ?? '-' }}</td>
                            @endif
                            <td class="px-4 py-3 text-center text-xs">{{ $a->sts_1 !== null ? number_format($a->sts_1, 0) : '-' }}</td>
                            <td class="px-4 py-3 text-center text-xs">{{ $a->sas !== null ? number_format($a->sas, 0) : '-' }}</td>
                            <td class="px-4 py-3 text-center text-xs">{{ $a->sts_2 !== null ? number_format($a->sts_2, 0) : '-' }}</td>
                            <td class="px-4 py-3 text-center text-xs">{{ $a->sat !== null ? number_format($a->sat, 0) : '-' }}</td>
                            <td class="px-4 py-3 text-center font-medium">{{ number_format($a->persentase_kehadiran, 0) }}%</td>
                            <td class="px-4 py-3 text-center font-bold {{ ($a->nilai_akhir ?? 0) >= ($a->seniBudaya?->kkm ?? 75) ? 'text-primary' : 'text-rose-600' }}">
                                {{ $a->nilai_akhir !== null ? number_format($a->nilai_akhir, 0) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-xs">{{ $a->nilai_huruf ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs text-text-secondary max-w-xs truncate" title="{{ $a->effective_deskripsi }}">
                                {{ $a->effective_deskripsi ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-4 py-8 text-center text-text-muted">
                                Tidak ada data penilaian yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
