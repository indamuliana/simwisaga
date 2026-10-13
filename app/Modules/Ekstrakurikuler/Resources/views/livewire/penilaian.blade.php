<div class="space-y-6 pb-20">
    <!-- Breadcrumb & Navigasi -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div class="flex items-center gap-2 text-xs">
            <a href="{{ route('ekskul.index') }}" class="text-primary hover:underline flex items-center gap-1 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kegiatan UPD</span>
            </a>
            <span class="text-text-secondary">/</span>
            <span class="text-text-secondary font-medium">Penilaian Rapor: {{ $ekskul->nama }}</span>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('ekskul.presensi', $ekskul->id) }}" class="btn btn-secondary text-xs flex items-center gap-1.5 w-full sm:w-auto justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span>Ke Presensi Mingguan</span>
            </a>
            <button wire:click="exportExcel" class="btn btn-secondary text-xs flex items-center gap-1.5 w-full sm:w-auto justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Export Excel</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session()->has('message'))
        <div class="p-3.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-200 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    <!-- Header Info Ekskul & Periode Rapor -->
    <div class="card p-5 border border-border bg-gradient-to-r from-blue-50/60 to-surface dark:from-blue-950/20 dark:to-surface space-y-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-xl font-bold text-text-main">Penilaian Ekstrakurikuler: {{ $ekskul->nama }}</h2>
                    @if($ekskul->is_wajib)
                        <span class="text-[10px] px-2 py-0.5 rounded font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">Wajib (Tingkat X)</span>
                    @else
                        <span class="text-[10px] px-2 py-0.5 rounded font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">Pilihan</span>
                    @endif
                </div>
                <p class="text-xs text-text-secondary mt-1">
                    Instruktur: <strong class="text-text-main">{{ $ekskul->instruktur_display_name }}</strong> &bull; Semester: <strong>{{ ucfirst($semester?->semester ?? '-') }} {{ $semester?->tahunAjaran->nama ?? '' }}</strong>
                </p>
            </div>

            <!-- Periode Switcher (STS 1, SAS, STS 2, SAT) -->
            <div class="flex items-center gap-1.5 bg-surface p-1 rounded-xl border border-border">
                @if($semester?->semester === 'genap')
                    <button wire:click="$set('activePeriod', 'STS 2')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $activePeriod === 'STS 2' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                        Periode STS 2
                    </button>
                    <button wire:click="$set('activePeriod', 'SAT')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $activePeriod === 'SAT' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                        Periode SAT (Rapor)
                    </button>
                @else
                    <button wire:click="$set('activePeriod', 'STS 1')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $activePeriod === 'STS 1' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                        Periode STS 1
                    </button>
                    <button wire:click="$set('activePeriod', 'SAS')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $activePeriod === 'SAS' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                        Periode SAS (Rapor)
                    </button>
                @endif
            </div>
        </div>

        <!-- Formula Bobot & Threshold Informasi (Read-Only dari Pengaturan Admin) -->
        <div class="border border-border/80 rounded-xl bg-surface p-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="w-6 h-6 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <span class="font-bold text-text-main">Formula Rapor:</span>
                <span class="px-2 py-0.5 rounded bg-surface-hover font-semibold text-primary">
                    Presensi Mingguan {{ $bobot_presensi }}% + Asesmen {{ $activePeriod }} {{ $bobot_penilaian }}%
                </span>
                <span class="text-text-secondary">| Rentang: <strong>A&ge;{{ $threshold_a }}, B&ge;{{ $threshold_b }}, C&ge;{{ $threshold_c }}</strong></span>
            </div>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole(['Kesiswaan', 'Kurikulum']))
                <a href="{{ route('ekskul.setting') }}" class="text-primary hover:underline text-[11px] font-medium shrink-0">
                    Ubah di Pengaturan Ekskul &rarr;
                </a>
            @endif
        </div>
    </div>

    <!-- Toolbar: Search Siswa & Tombol Massal -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 bg-surface p-3.5 rounded-xl border border-border">
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari siswa (Nama/NISN/Kelas)..." class="input text-xs w-full sm:w-64">
            <span class="text-xs text-text-secondary font-medium">
                Total: <strong>{{ $totalSiswa }}</strong> Siswa
            </span>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <button wire:click="generateDeskripsiSemua" type="button" class="btn btn-secondary text-xs flex items-center gap-1.5 font-medium">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>Generate Deskripsi Otomatis</span>
            </button>
            <button wire:click="saveNilai" type="button" class="btn btn-primary text-xs flex items-center gap-1.5 font-bold shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                <span>Simpan Penilaian Rapor</span>
            </button>
        </div>
    </div>

    <!-- Tabel Penilaian 4 Periode Rapor & Deskripsi -->
    <div class="card overflow-x-auto border border-border p-0">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface-hover text-text-secondary text-xs uppercase tracking-wider border-b border-border">
                <tr>
                    <th class="p-3 text-center w-12">No</th>
                    <th class="p-3 w-56">Nama Siswa</th>
                    <th class="p-3 w-28">Kelas</th>
                    <th class="p-3 text-center w-28">
                        Kehadiran
                        <span class="block text-[10px] font-normal lowercase text-text-secondary">bobot {{ $bobot_presensi }}%</span>
                    </th>
                    <th class="p-3 text-center w-32">
                        Nilai {{ $activePeriod }} *
                        <span class="block text-[10px] font-normal lowercase text-text-secondary">bobot {{ $bobot_penilaian }}%</span>
                    </th>
                    <th class="p-3 text-center w-24">
                        Nilai Akhir
                        <span class="block text-[10px] font-normal lowercase text-text-secondary">100%</span>
                    </th>
                    <th class="p-3 text-center w-28">Predikat</th>
                    <th class="p-3 min-w-[300px]">Deskripsi Capaian Kompetensi (Rapor)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($filteredList as $siswaId => $row)
                    <tr class="hover:bg-surface-hover/50 transition">
                        <td class="p-3 text-center text-xs text-text-secondary">
                            {{ $loop->iteration }}
                        </td>
                        <td class="p-3">
                            <div class="font-semibold text-text-main text-xs">{{ $row['nama'] }}</div>
                            <div class="text-[11px] text-text-secondary font-mono">NISN: {{ $row['nisn'] }}</div>
                        </td>
                        <td class="p-3 text-xs text-text-secondary">
                            <span class="px-2 py-0.5 rounded bg-surface border border-border font-medium">
                                {{ $row['rombel'] }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            <span class="font-bold text-xs {{ $row['nilai_presensi'] >= 80 ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $row['nilai_presensi'] }}%
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            <input type="number" min="0" max="100" step="any"
                                   wire:model.live.debounce.400ms="nilaiData.{{ $siswaId }}.nilai_asesmen"
                                   placeholder="0 - 100"
                                   class="input text-xs text-center font-bold w-24 py-1.5 border-border focus:border-primary">
                        </td>
                        <td class="p-3 text-center">
                            <span class="font-mono font-bold text-sm text-text-main">
                                {{ $row['nilai_akhir'] !== null ? $row['nilai_akhir'] : '-' }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            @if($row['nilai_huruf'] === 'Sangat Baik' || $row['nilai_huruf'] === 'A')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                    Sangat Baik
                                </span>
                            @elseif($row['nilai_huruf'] === 'Baik' || $row['nilai_huruf'] === 'B')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                                    Baik
                                </span>
                            @elseif($row['nilai_huruf'] === 'Cukup' || $row['nilai_huruf'] === 'C')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                    Cukup
                                </span>
                            @elseif($row['nilai_huruf'] === 'Kurang' || $row['nilai_huruf'] === 'D')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                    Kurang
                                </span>
                            @else
                                <span class="text-text-secondary text-xs">-</span>
                            @endif
                        </td>
                        <td class="p-3">
                            <textarea wire:model.lazy="nilaiData.{{ $siswaId }}.deskripsi" rows="2"
                                      placeholder="Deskripsi capaian rapor siswa..."
                                      class="input w-full text-xs py-1.5 px-2.5 leading-relaxed"></textarea>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-10 text-center text-text-secondary text-xs">
                            Belum ada siswa anggota terdaftar pada ekstrakurikuler ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Floating / Sticky Save Bar di Bagian Bawah -->
    <div class="fixed bottom-0 inset-x-0 bg-surface/95 backdrop-blur-md border-t border-border p-3 z-40">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
            <div class="text-xs text-text-secondary hidden sm:block">
                <span>Nilai tersimpan akan langsung ditampilkan pada <strong>Cetak Rapor Nilai Siswa (PDF)</strong>.</span>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                <button wire:click="generateDeskripsiSemua" type="button" class="btn btn-secondary text-xs py-2 px-3">
                    Generate Deskripsi
                </button>
                <button wire:click="saveNilai" type="button" class="btn btn-primary text-xs py-2 px-6 font-bold shadow-sm">
                    Simpan Nilai {{ $activePeriod }}
                </button>
            </div>
        </div>
    </div>
</div>
