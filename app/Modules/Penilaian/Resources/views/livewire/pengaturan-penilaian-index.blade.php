<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-text-main">Pengaturan Bobot &amp; Predikat Penilaian</h1>
            <p class="text-xs text-text-secondary">Konfigurasi bobot komponen nilai akhir (Rata TP, STS/SAS, Nilai Harian) dan skala interval predikat capaian.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button"
                    wire:click="resetToDefault"
                    wire:confirm="Yakin ingin mengembalikan seluruh bobot dan skala predikat ke pengaturan default?"
                    class="btn btn-outline text-xs flex items-center gap-1.5 py-1.5 px-3">
                <svg class="w-4 h-4 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Reset ke Standar</span>
            </button>
            <button type="button"
                    wire:click="save"
                    class="btn btn-primary text-xs flex items-center gap-1.5 py-1.5 px-4 font-semibold">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </div>

    @if (session('message'))
        <div class="p-3.5 text-xs text-emerald-800 bg-emerald-50 rounded-lg border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @error('bobot_total')
        <div class="p-3.5 text-xs text-rose-800 bg-rose-50 rounded-lg border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800 flex items-center gap-2">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ $message }}</span>
        </div>
    @enderror

    <!-- Card 1: Pengaturan Bobot Komponen Nilai Akhir -->
    <div class="bg-surface rounded-xl border border-border shadow-xs overflow-hidden">
        <div class="p-4 border-b border-border bg-surface-ground flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">
                    %
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-text-main">Bobot Komponen Nilai Akhir (9M)</h2>
                    <p class="text-xs text-text-secondary">Formula: ((Rata TP × Bobot TP) + (STS/SAS × Bobot STS) + (NH × Bobot NH)) ÷ 100</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs font-semibold {{ ($bobot_tp + $bobot_sts + $bobot_nh === 100) ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                    Total: {{ $bobot_tp + $bobot_sts + $bobot_nh }}%
                </span>
            </div>
        </div>

        <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Bobot Rata-rata TP -->
            <div class="space-y-1.5 p-3.5 rounded-lg border border-border bg-surface-ground/50">
                <label class="block text-xs font-semibold text-text-main">
                    Bobot Rata-rata TP (Tujuan Pembelajaran)
                </label>
                <div class="relative flex items-center">
                    <input type="number"
                           wire:model="bobot_tp"
                           min="0" max="100"
                           class="form-input w-full pr-8 text-sm font-semibold text-center"
                           placeholder="50">
                    <span class="absolute right-3 text-xs text-text-secondary font-bold">%</span>
                </div>
                <p class="text-[11px] text-text-secondary">Rata-rata formatif dari seluruh TP yang dinilai pada blok 9M terkait.</p>
                @error('bobot_tp') <span class="text-rose-600 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Bobot STS / SAS -->
            <div class="space-y-1.5 p-3.5 rounded-lg border border-border bg-surface-ground/50">
                <label class="block text-xs font-semibold text-text-main">
                    Bobot Asesmen Sumatif (STS 1 / SAS / STS 2 / SAT)
                </label>
                <div class="relative flex items-center">
                    <input type="number"
                           wire:model="bobot_sts"
                           min="0" max="100"
                           class="form-input w-full pr-8 text-sm font-semibold text-center"
                           placeholder="30">
                    <span class="absolute right-3 text-xs text-text-secondary font-bold">%</span>
                </div>
                <p class="text-[11px] text-text-secondary">Nilai tes sumatif tengah atau akhir semester pada periode berjalan.</p>
                @error('bobot_sts') <span class="text-rose-600 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Bobot Nilai Harian (NH) -->
            <div class="space-y-1.5 p-3.5 rounded-lg border border-border bg-surface-ground/50">
                <label class="block text-xs font-semibold text-text-main">
                    Bobot Nilai Harian (NH / Subjektif Guru)
                </label>
                <div class="relative flex items-center">
                    <input type="number"
                           wire:model="bobot_nh"
                           min="0" max="100"
                           class="form-input w-full pr-8 text-sm font-semibold text-center"
                           placeholder="20">
                    <span class="absolute right-3 text-xs text-text-secondary font-bold">%</span>
                </div>
                <p class="text-[11px] text-text-secondary">Nilai harian subjektif guru (secara otomatis terisi default 100).</p>
                @error('bobot_nh') <span class="text-rose-600 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <!-- Card 2: Pengaturan Interval Predikat Capaian -->
    <div class="bg-surface rounded-xl border border-border shadow-xs overflow-hidden">
        <div class="p-4 border-b border-border bg-surface-ground flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 flex items-center justify-center font-bold text-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-text-main">Skala Interval Predikat Capaian</h2>
                    <p class="text-xs text-text-secondary">Predikat dinamis berdasarkan Nilai Akhir (NA) siswa yang kompeten (≥ KKM). Jika nilai belum lengkap atau &lt; KKM, predikat otomatis "Belum Lengkap" atau "Belum Kompeten".</p>
                </div>
            </div>
            <button type="button"
                    wire:click="addInterval"
                    class="btn btn-outline text-xs flex items-center gap-1.5 py-1 px-3">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Tingkatan</span>
            </button>
        </div>

        <div class="p-5">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-border text-text-secondary bg-surface-ground/30">
                            <th class="py-2.5 px-3 font-semibold w-16 text-center">Urutan</th>
                            <th class="py-2.5 px-3 font-semibold w-28 text-center">Nilai Min</th>
                            <th class="py-2.5 px-3 font-semibold w-28 text-center">Nilai Max</th>
                            <th class="py-2.5 px-3 font-semibold w-48">Label Predikat</th>
                            <th class="py-2.5 px-3 font-semibold">Deskripsi Pedoman Ketercapaian</th>
                            <th class="py-2.5 px-3 font-semibold w-16 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($intervals as $idx => $item)
                            <tr class="hover:bg-surface-hover/30">
                                <td class="py-2.5 px-3 text-center text-text-secondary font-mono">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="py-2.5 px-3">
                                    <input type="number" step="0.01" min="0" max="100"
                                           wire:model="intervals.{{ $idx }}.min"
                                           class="form-input w-full text-center text-xs py-1">
                                </td>
                                <td class="py-2.5 px-3">
                                    <input type="number" step="0.01" min="0" max="100"
                                           wire:model="intervals.{{ $idx }}.max"
                                           class="form-input w-full text-center text-xs py-1">
                                </td>
                                <td class="py-2.5 px-3">
                                    <input type="text"
                                           wire:model="intervals.{{ $idx }}.label"
                                           class="form-input w-full font-semibold text-xs py-1"
                                           placeholder="Contoh: Mahir">
                                </td>
                                <td class="py-2.5 px-3">
                                    <input type="text"
                                           wire:model="intervals.{{ $idx }}.deskripsi"
                                           class="form-input w-full text-xs py-1"
                                           placeholder="Penjelasan deskriptif predikat">
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    <button type="button"
                                            wire:click="removeInterval({{ $idx }})"
                                            class="text-rose-500 hover:text-rose-700 p-1 rounded-md hover:bg-rose-50 dark:hover:bg-rose-950/30"
                                            title="Hapus baris">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800 rounded-lg text-xs text-amber-800 dark:text-amber-300">
                <strong>Catatan Aturan Sistem:</strong>
                <ul class="list-disc list-inside mt-1 space-y-0.5 text-[11px]">
                    <li>Jika ada nilai TP, NH, atau STS yang kosong/belum diisi, predikat otomatis <strong>Belum Lengkap</strong>.</li>
                    <li>Jika ada salah satu nilai di bawah KKM mata pelajaran, predikat otomatis <strong>Belum Kompeten</strong>.</li>
                    <li>Siswa dengan status <em>Belum Lengkap</em> atau <em>Belum Kompeten</em> mendapatkan narasi standar arahan perbaikan dan NA tidak dipublikasikan ke rapor hingga tuntas remedial.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
