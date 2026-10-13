<div class="space-y-6">
    <!-- Header Navigasi -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-border pb-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('seni-budaya.index') }}" class="p-1 rounded-lg text-text-secondary hover:text-text-main hover:bg-surface-hover">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
                    Lembar Penilaian: {{ $cabang->nama }}
                </h2>
            </div>
            <p class="text-sm text-text-secondary mt-1">
                Input asesmen sumatif (STS / SAS / SAT), pilih Capaian Tujuan Pembelajaran (TP) rapor, dan simpan nilai akhir siswa.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('seni-budaya.presensi', $cabang->id) }}" class="btn-secondary text-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Ke Presensi
            </a>
            <a href="{{ route('seni-budaya.export.rekap', $cabang->id) }}" class="btn-secondary text-sm flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Ekspor Excel
            </a>
            <button wire:click="saveAll" class="btn-primary text-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                Simpan Penilaian
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    <!-- Toolbar Periode & Konfigurasi Bobot -->
    <div class="card p-4 border border-border flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <!-- Pilihan Periode Penilaian -->
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Periode Asesmen:</span>
            <div class="inline-flex rounded-xl p-1 bg-surface-hover border border-border">
                @foreach(['STS 1', 'SAS', 'STS 2', 'SAT'] as $period)
                    <button type="button"
                            wire:click="$set('activePeriod', '{{ $period }}')"
                            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $activePeriod === $period ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                        {{ $period }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Info Formula & Tombol Massal -->
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-xs text-text-secondary bg-surface-hover px-2.5 py-1.5 rounded-lg border border-border">
                Formula: <strong>{{ $bobot_presensi }}%</strong> Presensi + <strong>{{ $bobot_penilaian }}%</strong> Asesmen (KKM: <strong>{{ $kkm }}</strong>)
            </span>
            <button wire:click="generateDeskripsiSemua" class="btn-secondary text-xs py-1.5 px-3 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Otomatiskan Deskripsi Rapor
            </button>
        </div>
    </div>

    <!-- Tabel Matriks Penilaian Siswa -->
    <div class="card overflow-hidden border border-border">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-hover text-text-secondary text-xs uppercase tracking-wider border-b border-border">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3 w-56">Identitas Siswa</th>
                        <th class="px-4 py-3 text-center w-24">Presensi (%)</th>
                        <th class="px-4 py-3 text-center w-32">Nilai {{ $activePeriod }} (0-100)</th>
                        <th class="px-4 py-3 text-center w-24">Nilai Akhir</th>
                        <th class="px-4 py-3 text-center w-16">Predikat</th>
                        <th class="px-4 py-3 text-center w-36">Capaian TP Rapor</th>
                        <th class="px-4 py-3">Deskripsi Capaian Kompetensi Rapor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-text-main">
                    @php $no = 1; @endphp
                    @forelse($nilaiData as $siswaId => $row)
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="px-4 py-3 text-center text-text-muted">{{ $no++ }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-text-main">{{ $row['nama'] }}</div>
                                <div class="text-xs text-text-muted">{{ $row['rombel'] }} &bull; NISN: {{ $row['nisn'] }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $row['nilai_presensi'] >= 80 ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' : 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300' }}">
                                    {{ number_format($row['nilai_presensi'], 0) }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="number"
                                       wire:model.live.debounce.400ms="nilaiData.{{ $siswaId }}.nilai_asesmen"
                                       min="0"
                                       max="100"
                                       placeholder="0-100"
                                       class="input text-center text-sm font-semibold w-24 py-1">
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-base {{ ($row['nilai_akhir'] ?? 0) >= $kkm ? 'text-primary' : 'text-rose-600' }}">
                                {{ $row['nilai_akhir'] !== null ? number_format($row['nilai_akhir'], 0) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded font-bold text-xs {{ in_array($row['nilai_huruf'], ['A', 'B']) ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $row['nilai_huruf'] ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button"
                                        wire:click="openTpModal({{ $siswaId }})"
                                        class="btn-secondary text-xs py-1 px-2.5 flex items-center justify-center gap-1 mx-auto">
                                    <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    <span>Pilih TP ({{ count($row['tp_tercapai_ids']) + count($row['tp_perlu_bimbingan_ids']) }})</span>
                                </button>
                            </td>
                            <td class="px-4 py-3 space-y-1">
                                @if($row['deskripsi_tertinggi'])
                                    <div class="text-xs text-emerald-700 dark:text-emerald-300">
                                        <span class="font-bold">&bull;</span> {{ $row['deskripsi_tertinggi'] }}
                                    </div>
                                @endif
                                @if($row['deskripsi_terendah'])
                                    <div class="text-xs text-rose-600 dark:text-rose-400">
                                        <span class="font-bold">&bull;</span> {{ $row['deskripsi_terendah'] }}
                                    </div>
                                @endif
                                <input type="text"
                                       wire:model="nilaiData.{{ $siswaId }}.deskripsi_manual"
                                       placeholder="Edit deskripsi manual bila diperlukan..."
                                       class="input text-xs py-1 px-2 w-full text-text-secondary">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-text-muted">
                                Belum ada siswa terdaftar di cabang {{ $cabang->nama }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(count($nilaiData) > 0)
            <div class="p-4 border-t border-border flex justify-end">
                <button type="button" wire:click="saveAll" class="btn-primary text-sm flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Semua Penilaian
                </button>
            </div>
        @endif
    </div>

    <!-- Modal Ceklis TP per Siswa -->
    @if($showTpModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" x-cloak>
            <div class="card max-w-xl w-full p-6 space-y-4 shadow-xl border border-border">
                <div class="flex justify-between items-center border-b border-border pb-3">
                    <div>
                        <h3 class="text-lg font-bold text-text-main">Capaian Tujuan Pembelajaran ({{ $activePeriod }})</h3>
                        <p class="text-xs text-text-secondary">Siswa: <strong>{{ $tpActiveSiswaNama }}</strong> &bull; Menampilkan TP teralokasi untuk periode <strong>{{ $activePeriod }}</strong></p>
                    </div>
                    <button type="button" wire:click="$set('showTpModal', false)" class="text-text-muted hover:text-text-main text-xl leading-none">&times;</button>
                </div>

                <div class="space-y-4 max-h-96 overflow-y-auto pr-1">
                    <!-- TP Tercapai Optimal -->
                    <div>
                        <label class="block text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Tujuan Pembelajaran Tercapai Optimal
                        </label>
                        <div class="space-y-1.5 bg-emerald-50/50 dark:bg-emerald-950/20 p-2.5 rounded-lg border border-emerald-200 dark:border-emerald-800">
                            @forelse($tps as $tp)
                                <label class="flex items-start gap-2.5 text-xs text-text-main p-1.5 rounded hover:bg-emerald-100/50 dark:hover:bg-emerald-900/30 cursor-pointer">
                                    <input type="checkbox"
                                           value="{{ $tp->id }}"
                                           wire:model="selectedTpTercapai"
                                           class="mt-0.5 rounded border-emerald-400 text-emerald-600 focus:ring-emerald-500">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-1.5">
                                            <strong class="text-emerald-800 dark:text-emerald-300">{{ $tp->kode }}</strong>
                                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-700 font-semibold">{{ $tp->target_asesmen_label }}</span>
                                        </div>
                                        <p class="mt-0.5 text-text-main">{{ $tp->deskripsi }}</p>
                                    </div>
                                </label>
                            @empty
                                <p class="text-xs text-text-muted italic">Belum ada TP terdaftar untuk periode {{ $activePeriod }} cabang ini.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- TP Perlu Peningkatan / Bimbingan -->
                    <div>
                        <label class="block text-xs font-bold text-rose-700 dark:text-rose-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Tujuan Pembelajaran Perlu Peningkatan / Bimbingan
                        </label>
                        <div class="space-y-1.5 bg-rose-50/50 dark:bg-rose-950/20 p-2.5 rounded-lg border border-rose-200 dark:border-rose-800">
                            @forelse($tps as $tp)
                                <label class="flex items-start gap-2.5 text-xs text-text-main p-1.5 rounded hover:bg-rose-100/50 dark:hover:bg-rose-900/30 cursor-pointer">
                                    <input type="checkbox"
                                           value="{{ $tp->id }}"
                                           wire:model="selectedTpPerluBimbingan"
                                           class="mt-0.5 rounded border-rose-400 text-rose-600 focus:ring-rose-500">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-1.5">
                                            <strong class="text-rose-800 dark:text-rose-300">{{ $tp->kode }}</strong>
                                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-rose-100 text-rose-700 font-semibold">{{ $tp->target_asesmen_label }}</span>
                                        </div>
                                        <p class="mt-0.5 text-text-main">{{ $tp->deskripsi }}</p>
                                    </div>
                                </label>
                            @empty
                                <p class="text-xs text-text-muted italic">Belum ada TP terdaftar untuk periode {{ $activePeriod }} cabang ini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-border">
                    <button type="button" wire:click="$set('showTpModal', false)" class="btn-secondary text-sm">Batal</button>
                    <button type="button" wire:click="saveTpSelection" class="btn-primary text-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Terapkan TP & Generate Deskripsi
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
