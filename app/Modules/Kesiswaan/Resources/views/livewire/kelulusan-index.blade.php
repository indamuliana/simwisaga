<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Kelulusan Siswa Tingkat Akhir (Kelas XII)</h1>
            <p class="text-sm text-text-secondary">Penetapan kelulusan peserta didik kelas XII dan pengalihan status otomatis ke entitas Alumni.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('alumni.index') }}" class="btn btn-secondary text-xs">
                🎓 Buka Modul Alumni
            </a>
        </div>
    </div>

    @if (session('status'))
    <div class="p-4 text-sm text-success bg-success-soft rounded-xl border border-success flex items-center gap-2">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span>{{ session('status') }}</span>
    </div>
    @endif

    @if (session('error'))
    <div class="p-4 text-sm text-rose-600 bg-rose-50 dark:bg-rose-950/40 rounded-xl border border-rose-200 dark:border-rose-900 flex items-center gap-2">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Filter Card -->
    <div class="card p-6 bg-surface border border-border shadow-sm rounded-2xl">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Tahun Ajaran Kelulusan</label>
                <select wire:model.live="tahunAjaranId" class="input w-full text-xs font-semibold">
                    @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Filter Rombel XII</label>
                <select wire:model.live="rombelId" class="input w-full text-xs font-semibold">
                    <option value="">-- Semua Kelas XII --</option>
                    @foreach($rombelsXII as $r)
                    <option value="{{ $r->id }}">{{ $r->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col justify-end">
                <span class="text-xs text-text-secondary">
                    Total Siswa Tingkat Akhir Terpilih: <strong class="text-text-main text-sm">{{ $totalSiswa }} siswa</strong>
                </span>
            </div>
        </div>
    </div>

    <!-- Statistik & Batch Prefill -->
    @if(!empty($kelulusanDecisions))
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card p-4 bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800 rounded-xl flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 uppercase">Dinyatakan Lulus</span>
                <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $totalLulus }} Siswa</div>
            </div>
            <button type="button" wire:click="setSemuaStatus('lulus')" class="text-xs font-semibold text-emerald-700 hover:underline">
                Setel Semua Lulus
            </button>
        </div>

        <div class="card p-4 bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800 rounded-xl flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase">Belum Lulus / Mengulang</span>
                <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $totalTidakLulus }} Siswa</div>
            </div>
            <span class="text-xs text-text-secondary">Ubah pada baris tabel</span>
        </div>

        <!-- Prefill Format Ijazah -->
        <div class="card p-4 bg-surface border border-border rounded-xl space-y-2">
            <span class="text-xs font-semibold text-text-secondary uppercase block">Format Prefix Nomor Ijazah (Opsional)</span>
            <div class="flex gap-2">
                <input type="text" wire:model="batchNoIjazahPrefix" placeholder="DN-02/M-SMK/K13/2026" class="input text-xs flex-1">
                <button type="button" wire:click="applyBatchIjazahPrefix" class="btn btn-secondary text-xs">
                    Terapkan
                </button>
            </div>
        </div>
    </div>

    <!-- Tabel Siswa Kelas XII -->
    <div class="card p-0 bg-surface border border-border shadow-sm rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border text-left text-xs">
                <thead class="bg-surface-hover/80 text-text-secondary font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">NISN / NIS</th>
                        <th class="px-4 py-3">Nama Siswa</th>
                        <th class="px-4 py-3">Kelas Asal</th>
                        <th class="px-4 py-3 text-center">JK</th>
                        <th class="px-4 py-3 w-44">Status Kelulusan</th>
                        <th class="px-4 py-3 w-56">Nomor Seri Ijazah</th>
                        <th class="px-4 py-3">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-text-main">
                    @foreach($kelulusanDecisions as $siswaId => $s)
                    <tr class="hover:bg-surface-hover/40 transition {{ $s['status'] === 'tidak_lulus' ? 'bg-amber-50/40 dark:bg-amber-950/20' : '' }}">
                        <td class="px-4 py-3 text-center text-text-secondary font-medium">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-mono font-bold">{{ $s['nis'] }}</td>
                        <td class="px-4 py-3 font-semibold">
                            {{ $s['nama'] }}
                            @if($s['status'] === 'tidak_lulus')
                            <span class="ml-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300">
                                Mengulang / Belum Lulus
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $s['rombel_nama'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $s['jk'] }}</td>
                        <td class="px-4 py-3">
                            <select wire:model.live="kelulusanDecisions.{{ $siswaId }}.status"
                                class="input w-full text-xs font-semibold {{ $s['status'] === 'lulus' ? 'text-emerald-700 dark:text-emerald-400' : 'text-amber-700 dark:text-amber-400' }}">
                                <option value="lulus">Lulus</option>
                                <option value="tidak_lulus">Tidak Lulus / Mengulang</option>
                                <option value="pindah">Pindah Sebelum Lulus</option>
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            @if($s['status'] === 'lulus')
                            <input type="text" wire:model="kelulusanDecisions.{{ $siswaId }}.nomor_ijazah"
                                placeholder="Contoh: M-SMK/01/2025" class="input w-full text-xs font-mono py-1">
                            @else
                            <span class="text-text-secondary text-[11px] italic">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <input type="text" wire:model="kelulusanDecisions.{{ $siswaId }}.catatan"
                                placeholder="Catatan kelulusan..." class="input w-full text-xs py-1">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tombol Aksi Eksekusi -->
        <div class="p-6 bg-surface border-t border-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="text-xs text-text-secondary max-w-lg">
                <strong>Perhatian:</strong> Siswa yang dinyatakan Lulus akan dinonaktifkan dari rombel belajar aktif dan otomatis dibuatkan kartu data di <strong>Modul Alumni</strong> untuk penelusuran tamatan (Tracer Study).
            </div>
            <button type="button"
                wire:click="prosesKelulusan"
                wire:confirm="Yakin ingin memproses kelulusan siswa kelas XII ini? Data siswa yang lulus akan langsung dialihkan ke database Alumni."
                class="btn btn-primary px-6 py-2.5 text-sm"
                wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="prosesKelulusan">🎓 Eksekusi Kelulusan ({{ $totalSiswa }} Siswa)</span>
                <span wire:loading wire:target="prosesKelulusan">Memproses Kelulusan...</span>
            </button>
        </div>
    </div>
    @else
    <div class="card p-12 text-center text-text-secondary bg-surface border border-border rounded-2xl">
        <p class="text-sm">Tidak ada siswa aktif kelas XII pada Tahun Ajaran / Rombel yang dipilih.</p>
    </div>
    @endif
</div>
