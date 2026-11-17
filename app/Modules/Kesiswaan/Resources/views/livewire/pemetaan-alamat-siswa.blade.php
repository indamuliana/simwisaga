<div class="space-y-6">
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('kesiswaan.siswa') }}" class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Data Siswa</span>
                </a>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Pemetaan & Standarisasi Alamat Siswa</h1>
            <p class="text-sm text-text-secondary">Cocokkan dan perbaiki alamat domisili siswa dengan 91.599 Master Wilayah resmi Kemendagri.</p>
        </div>

        <div class="flex items-center gap-2">
            @if($stats['pasti'] > 0)
                <button type="button" wire:click="applyAllConfident" wire:confirm="Apakah Anda yakin ingin menerapkan seluruh saran perbaikan dengan status 'Pasti' ({{ $stats['pasti'] }} siswa) secara otomatis?" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>⚡ Terapkan Semua Saran Pasti ({{ $stats['pasti'] }})</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if(session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Kartu Statistik & Ringkasan Anomali -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="card p-3.5 space-y-1">
            <span class="text-[11px] font-bold text-text-secondary uppercase tracking-wider block">Total Siswa</span>
            <span class="text-xl font-mono font-bold text-text-main">{{ number_format($stats['total'], 0, ',', '.') }}</span>
        </div>

        <div class="card p-3.5 space-y-1 border-rose-200 dark:border-rose-900/50 bg-rose-50/30 dark:bg-rose-950/20">
            <span class="text-[11px] font-bold text-rose-700 dark:text-rose-400 uppercase tracking-wider block">Perlu Perbaikan</span>
            <span class="text-xl font-mono font-bold text-rose-600 dark:text-rose-400">{{ number_format($stats['anomali'], 0, ',', '.') }}</span>
        </div>

        <div class="card p-3.5 space-y-1 border-amber-200 dark:border-amber-900/50 bg-amber-50/30 dark:bg-amber-950/20">
            <span class="text-[11px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider block">Kab. Mismatch</span>
            <span class="text-xl font-mono font-bold text-amber-600 dark:text-amber-400">{{ number_format($stats['mismatch'], 0, ',', '.') }}</span>
        </div>

        <div class="card p-3.5 space-y-1">
            <span class="text-[11px] font-bold text-text-secondary uppercase tracking-wider block">Teks Kotor / Prefix</span>
            <span class="text-xl font-mono font-bold text-text-main">{{ number_format($stats['dirty'], 0, ',', '.') }}</span>
        </div>

        <div class="card p-3.5 space-y-1">
            <span class="text-[11px] font-bold text-text-secondary uppercase tracking-wider block">Luar Negeri</span>
            <span class="text-xl font-mono font-bold text-text-main">{{ number_format($stats['intl'], 0, ',', '.') }}</span>
        </div>

        <div class="card p-3.5 space-y-1 border-emerald-200 dark:border-emerald-900/50 bg-emerald-50/30 dark:bg-emerald-950/20">
            <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block">Sesuai Master</span>
            <span class="text-xl font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['valid'], 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Filter & Toolbar Aksi Masal -->
    <div class="card p-4 space-y-4">
        <!-- Tab Kategori Masalah -->
        <div class="flex flex-wrap items-center gap-1.5 border-b border-border pb-3">
            <button type="button" wire:click="$set('filterCategory', 'semua_anomali')" class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition {{ $filterCategory === 'semua_anomali' ? 'bg-rose-600 text-white shadow-2xs' : 'bg-surface hover:bg-surface-hover text-text-secondary' }}">
                Perlu Perbaikan ({{ $stats['anomali'] }})
            </button>
            <button type="button" wire:click="$set('filterCategory', 'kabupaten_tidak_sinkron')" class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition {{ $filterCategory === 'kabupaten_tidak_sinkron' ? 'bg-amber-600 text-white shadow-2xs' : 'bg-surface hover:bg-surface-hover text-text-secondary' }}">
                Kabupaten Mismatch ({{ $stats['mismatch'] }})
            </button>
            <button type="button" wire:click="$set('filterCategory', 'teks_kotor')" class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition {{ $filterCategory === 'teks_kotor' ? 'bg-primary text-white shadow-2xs' : 'bg-surface hover:bg-surface-hover text-text-secondary' }}">
                Teks Kotor ({{ $stats['dirty'] }})
            </button>
            <button type="button" wire:click="$set('filterCategory', 'data_kosong')" class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition {{ $filterCategory === 'data_kosong' ? 'bg-primary text-white shadow-2xs' : 'bg-surface hover:bg-surface-hover text-text-secondary' }}">
                Data Kosong ({{ $stats['empty'] }})
            </button>
            <button type="button" wire:click="$set('filterCategory', 'luar_negeri')" class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition {{ $filterCategory === 'luar_negeri' ? 'bg-primary text-white shadow-2xs' : 'bg-surface hover:bg-surface-hover text-text-secondary' }}">
                Luar Negeri ({{ $stats['intl'] }})
            </button>
            <button type="button" wire:click="$set('filterCategory', 'sudah_valid')" class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition {{ $filterCategory === 'sudah_valid' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-surface hover:bg-surface-hover text-text-secondary' }}">
                Sesuai Master ({{ $stats['valid'] }})
            </button>
            <button type="button" wire:click="$set('filterCategory', 'semua')" class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition {{ $filterCategory === 'semua' ? 'bg-slate-700 text-white shadow-2xs' : 'bg-surface hover:bg-surface-hover text-text-secondary' }}">
                Semua Siswa ({{ $stats['total'] }})
            </button>
        </div>

        <!-- Baris Pencarian & Tindakan Masal -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2 flex-1">
                <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari nama siswa, NIS, kelurahan, kecamatan, alamat..." class="text-xs rounded-lg border-border bg-surface text-text-main py-2 px-3 w-full sm:w-80">
                
                <select wire:model.live="filterRombel" class="text-xs rounded-lg border-border bg-surface text-text-main py-2 px-2.5">
                    <option value="">-- Semua Kelas / Rombel --</option>
                    @foreach($rombels as $rb)
                        <option value="{{ $rb->id }}">{{ $rb->nama }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Aksi Masal Berdasarkan Centang -->
            <div class="flex items-center gap-2">
                @if(count($selectedSiswaIds) > 0)
                    <button type="button" wire:click="applySelected" class="px-3.5 py-2 text-xs font-bold text-white bg-primary hover:bg-primary-hover rounded-xl shadow-xs transition cursor-pointer">
                        ✓ Terapkan Saran Terpilih ({{ count($selectedSiswaIds) }})
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabel Perbandingan Berdampingan (Side-by-Side) -->
    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border text-left text-xs">
                <thead class="bg-surface-hover/80 text-text-secondary font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-3 py-3 w-8 text-center">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-slate-300 text-primary focus:ring-primary cursor-pointer">
                        </th>
                        <th class="px-4 py-3 min-w-[200px]">Data Siswa</th>
                        <th class="px-4 py-3 min-w-[220px]">Data Saat Ini</th>
                        <th class="px-4 py-3 min-w-[200px]">Status & Catatan Anomali</th>
                        <th class="px-4 py-3 min-w-[240px]">Saran Standar Kemendagri</th>
                        <th class="px-4 py-3 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-surface">
                    @forelse($items as $row)
                        @php
                            $siswa = $row['siswa'];
                            $isMismatch = ($row['status'] === 'kabupaten_tidak_sinkron');
                            $isDirty = ($row['status'] === 'teks_kotor');
                            $isValid = ($row['status'] === 'sudah_valid');
                        @endphp
                        <tr wire:key="row-{{ $siswa->id }}" class="hover:bg-surface-hover/40 transition {{ in_array((string)$siswa->id, $selectedSiswaIds) ? 'bg-primary/5 dark:bg-primary/10' : '' }}">
                            <!-- Checkbox -->
                            <td class="px-3 py-3 text-center">
                                @if($row['is_anomali'])
                                    <input type="checkbox" wire:model.live="selectedSiswaIds" value="{{ $siswa->id }}" class="rounded border-slate-300 text-primary focus:ring-primary cursor-pointer">
                                @else
                                    <span class="text-emerald-500 font-bold">✓</span>
                                @endif
                            </td>

                            <!-- Siswa Info -->
                            <td class="px-4 py-3">
                                <div class="font-bold text-text-main text-sm">{{ $siswa->nama }}</div>
                                <div class="text-[11px] text-text-secondary font-mono mt-0.5">
                                    NIS: <b>{{ $siswa->nipd ?: $siswa->nisn ?: '-' }}</b>
                                    &bull; Kelas: <b>{{ $siswa->rombel?->nama ?? '-' }}</b>
                                </div>
                                @if($siswa->alamat)
                                    <div class="text-[11px] text-text-secondary italic mt-1 line-clamp-1" title="{{ $siswa->alamat }}">
                                        📍 {{ $siswa->alamat }}
                                    </div>
                                @endif
                            </td>

                            <!-- Data Saat Ini -->
                            <td class="px-4 py-3 font-mono text-[11px] space-y-0.5">
                                <div>Kel: <span class="{{ $isDirty ? 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 px-1 rounded' : 'text-text-main' }}">{{ $row['current']['kelurahan'] ?: '-' }}</span></div>
                                <div>Kec: <span class="text-text-main font-semibold">{{ $row['current']['kecamatan'] ?: '-' }}</span></div>
                                <div>Kab: <span class="{{ $isMismatch ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 font-bold px-1 rounded' : 'text-text-main' }}">{{ $row['current']['kabupaten_kota'] ?: '-' }}</span></div>
                                <div>Prov: <span class="text-text-secondary">{{ $row['current']['provinsi'] ?: '-' }}</span></div>
                            </td>

                            <!-- Catatan Anomali -->
                            <td class="px-4 py-3">
                                @if($isValid)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                        ✓ Sesuai Master
                                    </span>
                                @else
                                    <div class="space-y-1">
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $isMismatch ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300' }}">
                                            {{ $row['status_label'] }}
                                        </span>
                                        @foreach($row['issues'] as $iss)
                                            <p class="text-[11px] text-rose-600 dark:text-rose-400">&bull; {{ $iss }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <!-- Saran Standar Kemendagri -->
                            <td class="px-4 py-3">
                                @if($row['is_anomali'])
                                    <div class="p-2 rounded-lg bg-emerald-50/80 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 space-y-0.5 font-mono text-[11px]">
                                        <div class="flex items-center justify-between pb-1 mb-1 border-b border-emerald-200 dark:border-emerald-800/40">
                                            <span class="text-[10px] font-bold uppercase text-emerald-800 dark:text-emerald-300">Rekomendasi</span>
                                            <span class="text-[10px] px-1.5 py-0.2 rounded font-semibold {{ $row['confidence'] === 'pasti' ? 'bg-emerald-200 text-emerald-900 dark:bg-emerald-900 dark:text-emerald-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                                                Keyakinan: {{ ucfirst($row['confidence']) }}
                                            </span>
                                        </div>
                                        <div class="text-emerald-900 dark:text-emerald-200">Kel: <b>{{ $row['suggested']['kelurahan'] }}</b></div>
                                        <div class="text-emerald-900 dark:text-emerald-200">Kec: <b>{{ $row['suggested']['kecamatan'] }}</b></div>
                                        <div class="text-emerald-900 dark:text-emerald-200 font-bold">Kab: <b>{{ $row['suggested']['kabupaten_kota'] }}</b></div>
                                        <div class="text-emerald-800 dark:text-emerald-300">Prov: {{ $row['suggested']['provinsi'] }}</div>
                                    </div>
                                @else
                                    <span class="text-text-secondary text-[11px] italic">Tidak ada perubahan yang diperlukan.</span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="px-4 py-3 text-center space-y-1">
                                @if($row['is_anomali'])
                                    <button type="button" wire:click="applySingle({{ $siswa->id }})" class="w-full px-2.5 py-1 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-2xs transition cursor-pointer">
                                        ✓ Terapkan
                                    </button>
                                @endif

                                <button type="button" wire:click="openManualModal({{ $siswa->id }})" class="w-full px-2.5 py-1 text-xs font-semibold text-text-main bg-surface hover:bg-surface-hover border border-border rounded-lg shadow-2xs transition cursor-pointer">
                                    ✎ Edit Manual
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-text-secondary">
                                Tidak ada data siswa yang cocok dengan kriteria filter saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="p-4 border-t border-border">
                {{ $items->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Edit Manual (Cascading Master Wilayah) -->
    @if($showManualModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
            <div class="w-full max-w-xl bg-surface rounded-2xl shadow-2xl border border-border overflow-hidden my-8 animate-in fade-in zoom-in-95">
                <!-- Header Modal -->
                <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-surface-hover/50">
                    <div>
                        <h2 class="text-base font-bold text-text-main">Edit Alamat Siswa Secara Manual</h2>
                        <p class="text-xs text-text-secondary">Siswa: <b>{{ $manualNamaSiswa }}</b></p>
                    </div>
                    <button type="button" wire:click="closeManualModal" class="text-slate-400 hover:text-text-main text-2xl font-bold cursor-pointer">&times;</button>
                </div>

                <!-- Form Body -->
                <div class="p-6 space-y-4 text-xs">
                    <!-- Negara -->
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-text-secondary mb-1">Negara</label>
                        <select wire:model.live="manualNegara" class="w-full rounded-lg border-border bg-surface text-text-main py-1.5 px-2.5">
                            <option value="Indonesia">Indonesia</option>
                            <option value="Malaysia">Malaysia</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <!-- Cascading: Provinsi -> Kab -> Kec -> Kelurahan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-text-secondary mb-1">Provinsi</label>
                            @if($manualNegara === 'Indonesia')
                                <select wire:model.live="manualProvinsi" class="w-full rounded-lg border-border bg-surface text-text-main py-1.5 px-2.5">
                                    <option value="">-- Pilih Provinsi --</option>
                                    @foreach($provincesList as $prov)
                                        <option value="{{ $prov }}">{{ $prov }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" wire:model="manualProvinsi" placeholder="Provinsi/State" class="w-full rounded-lg border-border bg-surface text-text-main py-1.5 px-2.5">
                            @endif
                        </div>

                        <div>
                            <label class="block font-bold uppercase tracking-wider text-text-secondary mb-1">Kabupaten / Kota</label>
                            @if($manualNegara === 'Indonesia')
                                <select wire:model.live="manualKabupaten" class="w-full rounded-lg border-border bg-surface text-text-main py-1.5 px-2.5" {{ empty($manualProvinsi) ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Kab/Kota --</option>
                                    @foreach($kabupatenList as $kab)
                                        <option value="{{ $kab }}">{{ $kab }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" wire:model="manualKabupaten" placeholder="Kabupaten/Bandar" class="w-full rounded-lg border-border bg-surface text-text-main py-1.5 px-2.5">
                            @endif
                        </div>

                        <div>
                            <label class="block font-bold uppercase tracking-wider text-text-secondary mb-1">Kecamatan</label>
                            @if($manualNegara === 'Indonesia')
                                <select wire:model.live="manualKecamatan" class="w-full rounded-lg border-border bg-surface text-text-main py-1.5 px-2.5" {{ empty($manualKabupaten) ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Kecamatan --</option>
                                    @foreach($kecamatanList as $kec)
                                        <option value="{{ $kec }}">{{ $kec }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" wire:model="manualKecamatan" placeholder="Kecamatan/Mukim" class="w-full rounded-lg border-border bg-surface text-text-main py-1.5 px-2.5">
                            @endif
                        </div>

                        <div>
                            <label class="block font-bold uppercase tracking-wider text-text-secondary mb-1">Kelurahan / Desa</label>
                            @if($manualNegara === 'Indonesia')
                                <select wire:model="manualKelurahan" class="w-full rounded-lg border-border bg-surface text-text-main py-1.5 px-2.5" {{ empty($manualKecamatan) ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Desa/Kelurahan --</option>
                                    @foreach($desaList as $ds)
                                        <option value="{{ $ds }}">{{ $ds }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" wire:model="manualKelurahan" placeholder="Kampung/Kelurahan" class="w-full rounded-lg border-border bg-surface text-text-main py-1.5 px-2.5">
                            @endif
                        </div>
                    </div>

                    <!-- Alamat Jalan, RT, RW, Dusun, Kode Pos -->
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-text-secondary mb-1">Alamat Jalan / Tempat Tinggal</label>
                        <input type="text" wire:model="manualAlamat" placeholder="Jl. Pahlawan No. 12" class="w-full rounded-lg border-border bg-surface text-text-main py-1.5 px-2.5">
                    </div>

                    <div class="grid grid-cols-4 gap-2">
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-text-secondary mb-1">RT</label>
                            <input type="text" wire:model="manualRt" placeholder="01" class="w-full rounded-lg border-border bg-surface text-text-main py-1.5 px-2.5">
                        </div>
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-text-secondary mb-1">RW</label>
                            <input type="text" wire:model="manualRw" placeholder="05" class="w-full rounded-lg border-border bg-surface text-text-main py-1.5 px-2.5">
                        </div>
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-text-secondary mb-1">Dusun</label>
                            <input type="text" wire:model="manualDusun" placeholder="Dusun I" class="w-full rounded-lg border-border bg-surface text-text-main py-1.5 px-2.5">
                        </div>
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-text-secondary mb-1">Kode Pos</label>
                            <input type="text" wire:model="manualKodePos" placeholder="44151" class="w-full rounded-lg border-border bg-surface text-text-main py-1.5 px-2.5">
                        </div>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="px-6 py-3.5 bg-surface-hover/50 border-t border-border flex justify-end items-center gap-2">
                    <button type="button" wire:click="closeManualModal" class="px-4 py-2 text-xs font-semibold text-text-secondary hover:bg-slate-200 dark:hover:bg-slate-800 rounded-lg transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click="saveManual" class="px-4 py-2 text-xs font-bold text-white bg-primary hover:bg-primary-hover rounded-lg shadow-sm transition cursor-pointer">
                        Simpan Alamat
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
