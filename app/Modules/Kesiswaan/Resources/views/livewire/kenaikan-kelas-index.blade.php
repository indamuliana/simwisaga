<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Wizard Kenaikan Kelas Siswa</h1>
            <p class="text-sm text-text-secondary">Proses mutasi kenaikan tingkat antar tahun ajaran secara massal dan penanganan siswa tinggal kelas.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('kesiswaan.plotting-rombel') }}" class="btn btn-secondary text-xs">
                Lihat Plotting Rombel
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

    <!-- Card Konfigurasi Asal & Tujuan -->
    <div class="card p-6 bg-surface border border-border shadow-sm rounded-2xl space-y-6">
        <div class="flex items-center gap-2 border-b border-border pb-3">
            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-500 text-white font-bold text-xs">1</span>
            <h2 class="text-base font-semibold text-text-main">Pilih Rombel Asal &amp; Rombel Tujuan</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Sisi Asal -->
            <div class="space-y-4 p-4 rounded-xl bg-surface-hover/60 border border-border/80">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-text-secondary">Kelas Asal (Tahun Ajaran Berjalan)</span>
                    <span class="text-xs px-2 py-0.5 rounded bg-brand-50 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300 font-semibold">Tingkat X &amp; XI</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">T.A. Asal</label>
                        <select wire:model.live="tahunAjaranAsalId" class="input w-full text-xs">
                            @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Rombel Asal *</label>
                        <select wire:model.live="rombelAsalId" class="input w-full text-xs font-semibold">
                            <option value="">-- Pilih Rombel Asal --</option>
                            @foreach($rombelsAsal as $r)
                            <option value="{{ $r->id }}">Kelas {{ $r->tingkat }} - {{ $r->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if($rombelAsal)
                <div class="text-xs text-text-secondary flex items-center justify-between pt-1">
                    <span>Jurusan: <strong>{{ $rombelAsal->jurusan?->nama }}</strong></span>
                    <span>Wali Kelas: <strong>{{ $rombelAsal->waliKelas?->name ?? '-' }}</strong></span>
                </div>
                @endif
            </div>

            <!-- Sisi Tujuan -->
            <div class="space-y-4 p-4 rounded-xl bg-surface-hover/60 border border-border/80">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-text-secondary">Kelas Tujuan (Tahun Ajaran Baru)</span>
                    <span class="text-xs px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-semibold">Target Naik</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">T.A. Tujuan *</label>
                        <select wire:model.live="tahunAjaranTujuanId" class="input w-full text-xs font-semibold">
                            <option value="">-- Pilih T.A. Tujuan --</option>
                            @foreach($tahunAjarans as $ta)
                            @if($ta->id != $tahunAjaranAsalId)
                            <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Rombel Tujuan Utama *</label>
                        <select wire:model.live="rombelTujuanUtamaId" class="input w-full text-xs font-semibold text-brand-600 dark:text-brand-400">
                            <option value="">-- Pilih Rombel Tujuan --</option>
                            @foreach($rombelsTujuan as $rt)
                            <option value="{{ $rt->id }}">Kelas {{ $rt->tingkat }} - {{ $rt->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if($rombelTujuanUtama)
                <div class="text-xs text-text-secondary flex items-center justify-between pt-1">
                    <span>Kapasitas: <strong>{{ $rombelTujuanUtama->kapasitas }} siswa</strong></span>
                    <span>Terisi: <strong>{{ $rombelTujuanUtama->siswas()->count() }} siswa</strong></span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Ringkasan Statistik Status Siswa -->
    @if(!empty($siswaDecisions))
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="card p-4 bg-surface border border-border rounded-xl">
            <span class="text-xs font-semibold text-text-secondary uppercase">Total Siswa</span>
            <div class="text-2xl font-bold text-text-main mt-1">{{ $totalSiswa }}</div>
        </div>
        <div class="card p-4 bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800 rounded-xl">
            <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 uppercase">Naik Kelas</span>
            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $totalNaik }}</div>
        </div>
        <div class="card p-4 bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800 rounded-xl">
            <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase">Tinggal Kelas</span>
            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $totalTinggal }}</div>
        </div>
        <div class="card p-4 bg-surface border border-border rounded-xl">
            <span class="text-xs font-semibold text-text-secondary uppercase">Mutasi Keluar / DO</span>
            <div class="text-2xl font-bold text-text-secondary mt-1">{{ $totalLainnya }}</div>
        </div>
    </div>

    <!-- Tabel Daftar Siswa & Keputusan -->
    <div class="card p-0 bg-surface border border-border shadow-sm rounded-2xl overflow-hidden">
        <div class="p-4 border-b border-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-surface-hover/30">
            <div class="flex items-center gap-2">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-500 text-white font-bold text-xs">2</span>
                <h2 class="text-base font-semibold text-text-main">Daftar Siswa &amp; Keputusan Kenaikan</h2>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" wire:click="setSemuaNaikKelas" class="btn btn-secondary text-xs">
                    ↺ Setel Semua: Naik Kelas
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border text-left text-xs">
                <thead class="bg-surface-hover/80 text-text-secondary font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">NISN / NIS</th>
                        <th class="px-4 py-3">Nama Siswa</th>
                        <th class="px-4 py-3 text-center">JK</th>
                        <th class="px-4 py-3 text-center">Kehadiran (S/I/A)</th>
                        <th class="px-4 py-3 w-48">Keputusan</th>
                        <th class="px-4 py-3 w-56">Rombel Tujuan</th>
                        <th class="px-4 py-3">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-text-main">
                    @foreach($siswaDecisions as $siswaId => $s)
                    <tr class="hover:bg-surface-hover/40 transition {{ $s['status'] === 'tinggal_kelas' ? 'bg-amber-50/40 dark:bg-amber-950/20' : '' }}">
                        <td class="px-4 py-3 text-center text-text-secondary font-medium">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-mono font-bold">{{ $s['nis'] }}</td>
                        <td class="px-4 py-3 font-semibold">
                            {{ $s['nama'] }}
                            @if(!empty($s['sudah_diproses']))
                            <span class="ml-1.5 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300" title="Keputusan untuk T.A. ini sudah tersimpan, Anda dapat mengedit dan menyimpan ulang kapan saja">
                                ✓ Tersimpan
                            </span>
                            @endif
                            @if($s['status'] === 'tinggal_kelas')
                            <span class="ml-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300">
                                Tinggal Kelas
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">{{ $s['jk'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-emerald-600 font-semibold" title="Sakit">{{ $s['rekap_absen']['sakit'] }}S</span> /
                            <span class="text-blue-600 font-semibold" title="Izin">{{ $s['rekap_absen']['izin'] }}I</span> /
                            <span class="{{ $s['rekap_absen']['alpa'] > 3 ? 'text-rose-600 font-bold' : 'text-gray-500' }}" title="Alpa">{{ $s['rekap_absen']['alpa'] }}A</span>
                        </td>
                        <td class="px-4 py-3">
                            <select wire:change="setStatusIndividual({{ $siswaId }}, $event.target.value)"
                                class="input w-full text-xs font-semibold {{ $s['status'] === 'tinggal_kelas' ? 'border-amber-400 text-amber-700 dark:text-amber-300' : ($s['status'] === 'naik_kelas' ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-600') }}">
                                <option value="naik_kelas" {{ $s['status'] === 'naik_kelas' ? 'selected' : '' }}>Naik Kelas</option>
                                <option value="tinggal_kelas" {{ $s['status'] === 'tinggal_kelas' ? 'selected' : '' }}>Tinggal Kelas</option>
                                <option value="pindah" {{ $s['status'] === 'pindah' ? 'selected' : '' }}>Pindah Sekolah</option>
                                <option value="dikeluarkan" {{ $s['status'] === 'dikeluarkan' ? 'selected' : '' }}>Dikeluarkan</option>
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            @if(in_array($s['status'], ['naik_kelas', 'tinggal_kelas']))
                            <select wire:model="siswaDecisions.{{ $siswaId }}.rombel_tujuan_id"
                                class="input w-full text-xs">
                                <option value="">-- Pilih Rombel --</option>
                                @foreach($rombelsTujuan as $rt)
                                <option value="{{ $rt->id }}">{{ $rt->nama }} (Kelas {{ $rt->tingkat }})</option>
                                @endforeach
                            </select>
                            @else
                            <span class="text-text-secondary text-[11px] italic">Nonaktif / Keluar</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <input type="text" wire:model="siswaDecisions.{{ $siswaId }}.catatan" placeholder="Catatan khusus..." class="input w-full text-xs py-1">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tombol Aksi Eksekusi -->
        <div class="p-6 bg-surface border-t border-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="text-xs text-text-secondary">
                Pastikan data keputusan kenaikan kelas di atas telah diverifikasi dengan seksama bersama Wali Kelas.
            </div>
            <button type="button"
                wire:click="prosesKenaikanKelas"
                wire:confirm="Yakin ingin memproses kenaikan kelas untuk rombel ini? Perubahan akan memperbarui data rombel siswa dan mencatat riwayat mutasi akademik."
                class="btn btn-primary px-6 py-2.5 text-sm"
                wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="prosesKenaikanKelas">✓ Eksekusi Kenaikan Kelas ({{ $totalSiswa }} Siswa)</span>
                <span wire:loading wire:target="prosesKenaikanKelas">Memproses Kenaikan...</span>
            </button>
        </div>
    </div>
    @elseif($rombelAsalId)
    <div class="card p-12 text-center text-text-secondary bg-surface border border-border rounded-2xl">
        <p class="text-sm">Tidak ada siswa aktif ditemukan di rombel ini.</p>
    </div>
    @else
    <div class="card p-12 text-center text-text-secondary bg-surface border border-border rounded-2xl">
        <p class="text-sm">Pilih Rombel Asal di atas untuk memuat daftar siswa.</p>
    </div>
    @endif
</div>
