<div class="space-y-6">
    <!-- Header Navigasi Sub-Modul -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-border pb-4">
        <div>
            <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Rekapitulasi UPD (Unit Pengembangan Diri)
            </h2>
            <p class="text-sm text-text-secondary mt-1">
                Laporan komprehensif kehadiran mingguan dan nilai akhir 4 periode rapor seluruh Unit Pengembangan Diri (UPD).
            </p>
        </div>
        <!-- Tab Sub-Modul -->
        <div class="flex items-center gap-2 bg-surface-hover p-1 rounded-xl border border-border">
            <a href="{{ route('ekskul.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Kegiatan UPD
            </a>
            <a href="{{ route('ekskul.anggota') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Keanggotaan Siswa
            </a>
            <a href="{{ route('ekskul.rekap') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold bg-white dark:bg-gray-800 text-primary shadow-xs">
                Rekapitulasi UPD
            </a>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole(['Kesiswaan', 'Kurikulum', 'Operator']))
                <a href="{{ route('ekskul.setting') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                    Pengaturan UPD
                </a>
            @endif
        </div>
    </div>

    <!-- KPI Ringkasan Statistik Minimalis -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-4 border border-border flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-text-secondary font-medium uppercase tracking-wider">Total Anggota</p>
                <p class="text-xl font-bold text-text-main">{{ $totalMembers }} <span class="text-xs font-normal text-text-secondary">siswa</span></p>
            </div>
        </div>
        <div class="card p-4 border border-border flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-text-secondary font-medium uppercase tracking-wider">Rata-rata Kehadiran</p>
                <p class="text-xl font-bold text-emerald-600">{{ $avgAttendance }}%</p>
            </div>
        </div>
        <div class="card p-4 border border-border flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
            </div>
            <div>
                <p class="text-xs text-text-secondary font-medium uppercase tracking-wider">Rata-rata Nilai Akhir</p>
                <p class="text-xl font-bold text-text-main">{{ $avgScore }}</p>
            </div>
        </div>
        <div class="card p-4 border border-border flex items-center gap-2">
            <div class="space-y-1 w-full text-xs">
                <p class="text-text-secondary font-medium uppercase tracking-wider text-[11px]">Distribusi Predikat</p>
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-bold">A: {{ $countA }}</span>
                    <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 font-bold">B: {{ $countB }}</span>
                    <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 font-bold">C: {{ $countC }}</span>
                    <span class="px-1.5 py-0.5 rounded bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 font-bold">D: {{ $countD }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Toolbar Filter & Tombol Unduh -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 bg-surface p-3.5 rounded-xl border border-border">
        <div>
            <label class="block text-[11px] font-semibold text-text-secondary mb-1">Semester</label>
            <select wire:model.live="semester_id" class="select w-full text-xs">
                @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}">{{ ucfirst($sem->semester) }} {{ $sem->tahunAjaran->nama }} {{ $sem->is_active ? '(Aktif)' : '' }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-semibold text-text-secondary mb-1">Ekskul</label>
            <select wire:model.live="filterEkskul" class="select w-full text-xs">
                <option value="">Semua Ekskul</option>
                @foreach($ekskuls as $e)
                    <option value="{{ $e->id }}">{{ $e->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-semibold text-text-secondary mb-1">Rombel / Kelas</label>
            <select wire:model.live="filterRombel" class="select w-full text-xs">
                <option value="">Semua Kelas</option>
                @foreach($rombels as $r)
                    <option value="{{ $r->id }}">{{ $r->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-semibold text-text-secondary mb-1">Predikat</label>
            <select wire:model.live="filterPredikat" class="select w-full text-xs">
                <option value="all">Semua Predikat</option>
                <option value="A">Sangat Baik (A)</option>
                <option value="B">Baik (B)</option>
                <option value="C">Cukup (C)</option>
                <option value="D">Kurang (D)</option>
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-semibold text-text-secondary mb-1">Cari Siswa</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nama / NISN..." class="input w-full text-xs">
        </div>
        <div class="flex items-end">
            <button wire:click="exportExcel" class="btn btn-secondary text-xs w-full py-2 flex items-center justify-center gap-1.5 font-semibold text-emerald-700 dark:text-emerald-400 border-emerald-200">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Unduh Excel</span>
            </button>
        </div>
    </div>

    <!-- Tabel Rekapitulasi Lengkap -->
    <div class="card overflow-x-auto border border-border p-0">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface-hover text-text-secondary text-xs uppercase tracking-wider border-b border-border">
                <tr>
                    <th class="p-3 text-center w-12">No</th>
                    <th class="p-3">Nama Siswa</th>
                    <th class="p-3">Kelas</th>
                    <th class="p-3">Ekstrakurikuler</th>
                    <th class="p-3 text-center">Kehadiran</th>
                    <th class="p-3 text-center">STS 1</th>
                    <th class="p-3 text-center">SAS</th>
                    <th class="p-3 text-center">STS 2</th>
                    <th class="p-3 text-center">SAT</th>
                    <th class="p-3 text-center font-bold text-primary">Nilai Akhir</th>
                    <th class="p-3 text-center">Predikat</th>
                    <th class="p-3 min-w-[280px]">Deskripsi Rapor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border text-xs">
                @forelse($rekapList as $idx => $r)
                    <tr class="hover:bg-surface-hover/50 transition">
                        <td class="p-3 text-center text-text-secondary">
                            {{ $rekapList->firstItem() + $idx }}
                        </td>
                        <td class="p-3">
                            <div class="font-semibold text-text-main">{{ $r->siswa?->nama ?? '-' }}</div>
                            <div class="text-[11px] text-text-secondary font-mono">NISN: {{ $r->siswa?->nisn ?? '-' }}</div>
                        </td>
                        <td class="p-3 text-text-secondary">
                            <span class="px-2 py-0.5 rounded bg-surface border border-border font-medium">
                                {{ $r->siswa?->rombel?->nama ?? '-' }}
                            </span>
                        </td>
                        <td class="p-3 font-medium text-text-main">
                            {{ $r->ekstrakurikuler?->nama ?? '-' }}
                        </td>
                        <td class="p-3 text-center">
                            <span class="font-bold {{ $r->persentase_kehadiran >= 80 ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $r->persentase_kehadiran }}%
                            </span>
                        </td>
                        <td class="p-3 text-center font-mono">{{ $r->sts_1 ?? '-' }}</td>
                        <td class="p-3 text-center font-mono">{{ $r->sas ?? '-' }}</td>
                        <td class="p-3 text-center font-mono">{{ $r->sts_2 ?? '-' }}</td>
                        <td class="p-3 text-center font-mono">{{ $r->sat ?? '-' }}</td>
                        <td class="p-3 text-center font-mono font-bold text-sm text-text-main">
                            {{ $r->nilai_angka ?? '-' }}
                        </td>
                        <td class="p-3 text-center">
                            @if($r->nilai_huruf === 'Sangat Baik' || $r->nilai_huruf === 'A')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                    Sangat Baik
                                </span>
                            @elseif($r->nilai_huruf === 'Baik' || $r->nilai_huruf === 'B')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                                    Baik
                                </span>
                            @elseif($r->nilai_huruf === 'Cukup' || $r->nilai_huruf === 'C')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                    Cukup
                                </span>
                            @elseif($r->nilai_huruf === 'Kurang' || $r->nilai_huruf === 'D')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                    Kurang
                                </span>
                            @else
                                <span class="text-text-secondary">-</span>
                            @endif
                        </td>
                        <td class="p-3 text-text-secondary leading-relaxed">
                            {{ $r->deskripsi ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="p-10 text-center text-text-secondary">
                            Belum ada rekapitulasi penilaian pada filter yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($rekapList->hasPages())
            <div class="p-4 border-t border-border">
                {{ $rekapList->links() }}
            </div>
        @endif
    </div>
</div>
