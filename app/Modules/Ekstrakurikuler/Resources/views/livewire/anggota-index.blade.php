<div class="space-y-6">
    <!-- Header Navigasi Sub-Modul -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-border pb-4">
        <div>
            <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Keanggotaan Siswa UPD (Unit Pengembangan Diri)
            </h2>
            <p class="text-sm text-text-secondary mt-1">
                Kelola daftar siswa per UPD, mutasi/pindah siswa antar kegiatan, auto-enroll Pramuka wajib kelas X, dan audit kepatuhan siswa.
            </p>
        </div>
        <!-- Tab Sub-Modul -->
        <div class="flex items-center gap-2 bg-surface-hover p-1 rounded-xl border border-border">
            <a href="{{ route('ekskul.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Kegiatan UPD
            </a>
            <a href="{{ route('ekskul.anggota') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold bg-white dark:bg-gray-800 text-primary shadow-xs">
                Keanggotaan Siswa
            </a>
            <a href="{{ route('ekskul.rekap') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Rekapitulasi UPD
            </a>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole(['Kesiswaan', 'Kurikulum', 'Operator']))
                <a href="{{ route('ekskul.setting') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                    Pengaturan UPD
                </a>
            @endif
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
    @if(session()->has('error'))
        <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl text-rose-800 dark:text-rose-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
        </div>
    @endif

    <!-- Aksi Utama & Navigasi Tab Internal Keanggotaan -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <!-- Internal Tab Selector -->
        <div class="flex items-center gap-2 border-b border-border w-full md:w-auto">
            <button wire:click="$set('activeTab', 'anggota')"
                    class="pb-3 px-3 text-sm font-semibold border-b-2 transition {{ $activeTab === 'anggota' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main' }}">
                Daftar Anggota Terdaftar (Per UPD)
            </button>
            <button wire:click="$set('activeTab', 'kepatuhan')"
                    class="pb-3 px-3 text-sm font-semibold border-b-2 transition flex items-center gap-1.5 {{ $activeTab === 'kepatuhan' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main' }}">
                <span>Audit Kepatuhan Min. 1 UPD</span>
            </button>
            <button wire:click="$set('activeTab', 'import')"
                    class="pb-3 px-3 text-sm font-semibold border-b-2 transition {{ $activeTab === 'import' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main' }}">
                Import Excel & Mutasi
            </button>
        </div>

        <!-- Tombol Aksi Cepat -->
        <div class="flex items-center gap-2 flex-wrap">
            <button wire:click="autoEnrollPramuka"
                    onclick="confirm('Sinkronisasi seluruh siswa aktif kelas X ke UPD Pramuka Wajib sekarang?') || event.stopImmediatePropagation()"
                    class="btn btn-secondary text-xs flex items-center gap-1.5 border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>Auto-Enroll Pramuka Kelas X</span>
            </button>
            <button wire:click="openAddModal()" class="btn btn-primary text-xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>Tambah Siswa Manual</span>
            </button>
        </div>
    </div>

    <!-- TAB 1: DAFTAR ANGGOTA TERDAFTAR (PER UPD) -->
    @if($activeTab === 'anggota')
        <!-- KONDISI A: TAMPILAN TABEL DAFTAR UPD (KETIKA BELUM ADA UPD DIPILIH) -->
        @if(!$selectedEkskulDetailId)
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-surface p-3.5 rounded-xl border border-border">
                    <div>
                        <h3 class="text-sm font-bold text-text-main">Pilih Unit Pengembangan Diri (UPD)</h3>
                        <p class="text-xs text-text-secondary">Klik pada salah satu UPD di bawah ini untuk melihat daftar siswa anggotanya serta memindahkan siswa.</p>
                    </div>
                    <div>
                        <select wire:model.live="semester_id" class="select text-xs">
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}">{{ ucfirst($sem->semester) }} {{ $sem->tahunAjaran->nama }} {{ $sem->is_active ? '(Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="card overflow-x-auto border border-border p-0">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-surface-hover/80 text-text-secondary text-xs uppercase tracking-wider border-b border-border">
                            <tr>
                                <th class="p-3.5 text-center w-12">No</th>
                                <th class="p-3.5">Nama UPD</th>
                                <th class="p-3.5">Kategori</th>
                                <th class="p-3.5">Instruktur / Pembina</th>
                                <th class="p-3.5">Jadwal Patokan</th>
                                <th class="p-3.5 text-center">Anggota Aktif</th>
                                <th class="p-3.5 text-center">Sesi Presensi</th>
                                <th class="p-3.5 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border text-xs">
                            @forelse($allEkskuls as $idx => $e)
                                <tr wire:click="selectEkskul({{ $e->id }})" class="hover:bg-primary/5 dark:hover:bg-primary/10 transition cursor-pointer">
                                    <td class="p-3.5 text-center text-text-secondary">{{ $idx + 1 }}</td>
                                    <td class="p-3.5">
                                        <div class="font-bold text-text-main text-sm hover:text-primary transition flex items-center gap-2">
                                            <span>{{ $e->nama }}</span>
                                            <svg class="w-4 h-4 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </div>
                                        @if($e->tempat)
                                            <div class="text-[11px] text-text-secondary">Lokasi: {{ $e->tempat }}</div>
                                        @endif
                                    </td>
                                    <td class="p-3.5">
                                        @if($e->is_wajib)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                                Wajib (Tingkat X)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                                                Pilihan (Bakat & Minat)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3.5 font-medium text-text-main">
                                        {{ $e->instruktur_display_name }}
                                    </td>
                                    <td class="p-3.5 text-text-secondary">
                                        {{ $e->hari_latihan ?? 'Jumat' }} ({{ $e->jam_mulai ? substr($e->jam_mulai,0,5) : '15:30' }})
                                    </td>
                                    <td class="p-3.5 text-center">
                                        <span class="font-bold text-sm text-primary">
                                            {{ $anggotaCounts[$e->id] ?? 0 }}
                                        </span>
                                        <span class="text-[11px] text-text-secondary">siswa</span>
                                    </td>
                                    <td class="p-3.5 text-center font-medium text-text-secondary">
                                        {{ $presensiCounts[$e->id] ?? 0 }} sesi
                                    </td>
                                    <td class="p-3.5 text-center" onclick="event.stopPropagation()">
                                        <button wire:click="selectEkskul({{ $e->id }})" class="btn btn-secondary text-xs py-1.5 px-3 flex items-center justify-center gap-1 font-semibold text-primary w-full">
                                            <span>Lihat Siswa</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-10 text-center text-text-secondary">
                                        Belum ada data UPD yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- KONDISI B: DETAIL ANGGOTA SISWA PADA UPD YANG DIPILIH -->
            <div class="space-y-4">
                <!-- Header Breadcrumb & Info UPD Terpilih -->
                <div class="card p-4 border border-border bg-gradient-to-r from-blue-50/60 to-surface dark:from-blue-950/20 dark:to-surface space-y-3">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <div class="flex items-center gap-3">
                            <button wire:click="clearSelectedEkskul" class="btn btn-secondary text-xs py-2 px-3 flex items-center gap-1 font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                <span>&larr; Kembali ke Daftar Seluruh UPD</span>
                            </button>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-lg font-bold text-text-main">{{ $selectedEkskulModel?->nama }}</h3>
                                    @if($selectedEkskulModel?->is_wajib)
                                        <span class="text-[10px] px-2 py-0.5 rounded font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">Wajib (Tingkat X)</span>
                                    @else
                                        <span class="text-[10px] px-2 py-0.5 rounded font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">Pilihan</span>
                                    @endif
                                </div>
                                <p class="text-xs text-text-secondary mt-0.5">
                                    Instruktur: <strong class="text-text-main">{{ $selectedEkskulModel?->instruktur_display_name }}</strong> &bull; Total Anggota: <strong class="text-primary">{{ $anggotas->total() }} Siswa</strong>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                            @if(count($selectedAnggotaIdsForTransfer) > 0)
                                <button wire:click="openMassTransferModal" class="btn btn-primary text-xs py-2 px-3 flex items-center gap-1.5 font-bold shadow-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    <span>Pindahkan {{ count($selectedAnggotaIdsForTransfer) }} Siswa Terpilih</span>
                                </button>
                            @endif
                            <button wire:click="openAddModal({{ $selectedEkskulDetailId }})" class="btn btn-secondary text-xs py-2 px-3 flex items-center gap-1.5 font-semibold text-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>+ Tambah Siswa ke UPD Ini</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Toolbar Filter Anggota Dalam UPD Ini -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-surface p-3.5 rounded-xl border border-border">
                    <div>
                        <label class="block text-[11px] font-semibold text-text-secondary mb-1">Filter Rombel / Kelas</label>
                        <select wire:model.live="filterRombel" class="select w-full text-xs">
                            <option value="">Semua Kelas</option>
                            @foreach($rombels as $r)
                                <option value="{{ $r->id }}">{{ $r->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-text-secondary mb-1">Status Keanggotaan</label>
                        <select wire:model.live="filterStatus" class="select w-full text-xs">
                            <option value="aktif">Aktif</option>
                            <option value="pindah">Pindah / Mutasi</option>
                            <option value="all">Semua Status</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-text-secondary mb-1">Cari Siswa</label>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama / NISN..." class="input w-full text-xs">
                    </div>
                </div>

                <!-- Tabel Siswa Anggota UPD -->
                <div class="card overflow-x-auto border border-border p-0">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-surface-hover/80 text-text-secondary text-xs uppercase tracking-wider border-b border-border">
                            <tr>
                                <th class="p-3.5 text-center w-10">
                                    <input type="checkbox"
                                           wire:click="$set('selectedAnggotaIdsForTransfer', {{ count($selectedAnggotaIdsForTransfer) === $anggotas->count() && $anggotas->count() > 0 ? '[]' : json_encode($anggotas->pluck('id')->all()) }})"
                                           {{ count($selectedAnggotaIdsForTransfer) === $anggotas->count() && $anggotas->count() > 0 ? 'checked' : '' }}
                                           class="rounded border-border text-primary focus:ring-primary">
                                </th>
                                <th class="p-3.5 text-center w-12">No</th>
                                <th class="p-3.5">Nama Siswa</th>
                                <th class="p-3.5">Kelas</th>
                                <th class="p-3.5 text-center">Kehadiran (%)</th>
                                <th class="p-3.5 text-center">Status</th>
                                <th class="p-3.5 text-center w-36">Aksi & Mutasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border text-xs">
                            @forelse($anggotas as $idx => $anggota)
                                <tr class="hover:bg-surface-hover/50 transition">
                                    <td class="p-3.5 text-center">
                                        <input type="checkbox" wire:model.live="selectedAnggotaIdsForTransfer" value="{{ $anggota->id }}" class="rounded border-border text-primary focus:ring-primary">
                                    </td>
                                    <td class="p-3.5 text-center text-text-secondary">
                                        {{ $anggotas->firstItem() + $idx }}
                                    </td>
                                    <td class="p-3.5">
                                        <div class="font-bold text-text-main">{{ $anggota->siswa?->nama ?? '-' }}</div>
                                        <div class="text-[11px] text-text-secondary font-mono">NISN: {{ $anggota->siswa?->nisn ?? '-' }}</div>
                                    </td>
                                    <td class="p-3.5 text-text-secondary">
                                        <span class="px-2 py-0.5 rounded bg-surface border border-border font-medium">
                                            {{ $anggota->siswa?->rombel?->nama ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 text-center">
                                        <span class="font-bold {{ $anggota->persentase_kehadiran >= 80 ? 'text-emerald-600' : ($anggota->persentase_kehadiran >= 60 ? 'text-amber-600' : 'text-rose-600') }}">
                                            {{ $anggota->persentase_kehadiran }}%
                                        </span>
                                        <div class="text-[10px] text-text-secondary">
                                            {{ $anggota->total_hadir }} / {{ $anggota->total_pertemuan }} sesi
                                        </div>
                                    </td>
                                    <td class="p-3.5 text-center">
                                        @if($anggota->status_keanggotaan === 'aktif')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300" title="{{ $anggota->catatan_pindah }}">
                                                Pindah
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3.5 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Tombol Edit Pindah ke UPD Lain -->
                                            <button wire:click="openTransferModal({{ $anggota->id }})" title="Pindahkan siswa ke UPD lain"
                                                    class="btn btn-secondary text-xs py-1 px-2.5 font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                                <span>Pindah UPD</span>
                                            </button>
                                            <button wire:click="removeAnggota({{ $anggota->id }})"
                                                    onclick="confirm('Keluarkan siswa ini dari keanggotaan UPD ini?') || event.stopImmediatePropagation()"
                                                    title="Keluarkan dari UPD"
                                                    class="p-1.5 rounded border border-border hover:bg-rose-50 text-rose-600 dark:hover:bg-rose-950/40">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-10 text-center text-text-secondary">
                                        Belum ada siswa anggota yang terdaftar pada UPD ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($anggotas->hasPages())
                        <div class="p-4 border-t border-border">
                            {{ $anggotas->links() }}
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @endif

    <!-- TAB 2: AUDIT KEPATUHAN MIN. 1 UPD PILIHAN -->
    @if($activeTab === 'kepatuhan')
        @if($complianceData)
            <!-- Ringkasan Kepatuhan -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="card p-4 border border-border bg-emerald-50/40 dark:bg-emerald-950/20">
                    <p class="text-xs text-emerald-800 dark:text-emerald-300 font-medium">Siswa Lengkap (Patuh Aturan UPD)</p>
                    <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">{{ $complianceData['compliant_count'] }} <span class="text-sm font-normal text-text-secondary">/ {{ $complianceData['total_students'] }} siswa</span></p>
                    <p class="text-xs text-text-secondary mt-1">Tingkat kepatuhan sekolah: <strong class="text-text-main">{{ $complianceData['compliance_rate'] }}%</strong></p>
                </div>
                <div class="card p-4 border border-border bg-rose-50/40 dark:bg-rose-950/20">
                    <p class="text-xs text-rose-800 dark:text-rose-300 font-medium">Siswa Belum Memenuhi Aturan</p>
                    <p class="text-2xl font-bold text-rose-700 dark:text-rose-400">{{ $complianceData['non_compliant_count'] }} <span class="text-sm font-normal text-text-secondary">siswa</span></p>
                    <p class="text-xs text-text-secondary mt-1">Siswa X belum lengkap Pramuka/Pilihan, atau siswa XI belum ada UPD (Kelas XII bebas)</p>
                </div>
                <div class="card p-4 border border-border flex flex-col justify-center gap-2">
                    <p class="text-xs text-text-secondary">Tindakan Cepat Pemenuhan:</p>
                    <div class="flex items-center gap-2">
                        <button wire:click="autoEnrollPramuka" class="btn btn-secondary text-xs flex-1 justify-center py-2">
                            Sinkron Pramuka X
                        </button>
                        <button wire:click="$set('activeTab', 'import')" class="btn btn-primary text-xs flex-1 justify-center py-2">
                            Import Massal
                        </button>
                    </div>
                </div>
            </div>

            <!-- Toolbar Filter Tab Kepatuhan -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-surface p-3.5 rounded-xl border border-border">
                <div class="flex items-center gap-3 flex-wrap w-full sm:w-auto">
                    <div>
                        <label class="block text-[11px] font-semibold text-text-secondary mb-1">Filter Tingkat</label>
                        <select wire:model.live="filterTingkat" class="select text-xs">
                            <option value="">Semua Tingkat</option>
                            <option value="X">Tingkat X (Wajib Pramuka + Min. 1 Pilihan)</option>
                            <option value="XI">Tingkat XI (Wajib Min. 1 UPD Bebas)</option>
                            <option value="XII">Tingkat XII (Bebas Kewajiban UPD)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-text-secondary mb-1">Filter Kelas / Rombel</label>
                        <select wire:model.live="filterRombel" class="select text-xs">
                            <option value="">Semua Kelas</option>
                            @foreach($rombels as $r)
                                <option value="{{ $r->id }}">{{ $r->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="text-xs text-text-secondary">
                    <span>Aturan: Siswa Kelas XII dibebaskan dari kewajiban UPD.</span>
                </div>
            </div>

            <!-- Tabel Daftar Siswa yang Belum Memenuhi -->
            <div class="card border border-border p-0 overflow-x-auto space-y-0">
                <div class="p-4 bg-surface-hover border-b border-border flex justify-between items-center">
                    <h3 class="text-sm font-bold text-text-main flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        Daftar Siswa Belum Memenuhi Ketentuan UPD ({{ count($complianceData['non_compliant_list']) }} Siswa)
                    </h3>
                </div>
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-hover/80 text-text-secondary text-xs uppercase tracking-wider border-b border-border">
                        <tr>
                            <th class="p-3.5 w-12 text-center">No</th>
                            <th class="p-3.5">Nama Siswa</th>
                            <th class="p-3.5">Kelas / Tingkat</th>
                            <th class="p-3.5">UPD Saat Ini</th>
                            <th class="p-3.5">Status Masalah</th>
                            <th class="p-3.5 text-center w-28">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border text-xs">
                        @forelse($complianceData['non_compliant_list'] as $idx => $row)
                            <tr class="hover:bg-surface-hover/50">
                                <td class="p-3.5 text-center text-text-secondary">{{ $idx + 1 }}</td>
                                <td class="p-3.5">
                                    <div class="font-bold text-text-main">{{ $row['siswa']->nama }}</div>
                                    <div class="text-[11px] text-text-secondary font-mono">NISN: {{ $row['siswa']->nisn }}</div>
                                </td>
                                <td class="p-3.5 text-text-secondary">
                                    {{ $row['siswa']->rombel?->nama ?? '-' }}
                                    @if($row['is_tingkat_x'])
                                        <span class="text-[10px] text-primary font-bold">(Tingkat X)</span>
                                    @elseif($row['is_tingkat_xi'])
                                        <span class="text-[10px] text-amber-600 font-bold">(Tingkat XI)</span>
                                    @endif
                                </td>
                                <td class="p-3.5">
                                    @if(count($row['ekskuls']) > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($row['ekskuls'] as $ek)
                                                <span class="px-2 py-0.5 rounded bg-surface border border-border font-medium">{{ $ek }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-rose-500 italic">Belum terdaftar di UPD manapun</span>
                                    @endif
                                </td>
                                <td class="p-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                        {{ $row['status_text'] }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <button wire:click="openEnrollSiswaModal({{ $row['siswa']->id }}, {{ $row['missing_pramuka'] ? 'true' : 'false' }})" class="btn btn-secondary text-xs py-1 px-2.5 font-bold text-primary hover:bg-primary/10 flex items-center justify-center gap-1 mx-auto">
                                        <span>+ Daftarkan</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-10 text-center text-emerald-600 font-medium">
                                    🎉 Luar biasa! Seluruh siswa pada kriteria filter ini sudah memenuhi kewajiban UPD.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    @endif

    <!-- TAB 3: IMPORT EXCEL & MUTASI ANGGOTA PER UPD PILIHAN -->
    @if($activeTab === 'import')
        @if(!$importPreviewData)
            <!-- TAMPILAN AWAL: LANGKAH 1 & LANGKAH 2 -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Box 1: Pilih UPD Pilihan & Unduh Template -->
                <div class="card border border-border p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-text-main">Langkah 1: Pilih UPD & Unduh Template</h3>
                            <p class="text-xs text-text-secondary">Pilih kegiatan UPD pilihan tujuan yang ingin diisi anggotanya.</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold mb-1 text-text-main">Pilih Unit Pengembangan Diri (UPD Pilihan) *</label>
                            <select wire:model.live="selectedImportEkskulId" class="select w-full text-xs">
                                <option value="">-- Pilih UPD Pilihan Tujuan --</option>
                                @foreach($choiceEkskuls as $ce)
                                    <option value="{{ $ce->id }}">{{ $ce->nama }} (Instruktur: {{ $ce->instruktur_display_name }})</option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-text-secondary mt-1">
                                Catatan: Import Excel ini hanya berlaku khusus untuk UPD Pilihan (Bakat & Minat).
                            </p>
                        </div>

                        <div class="p-3.5 bg-surface-hover rounded-xl text-xs space-y-1.5 border border-border">
                            <p class="font-bold text-text-main">Format Template Sederhana (2 Kolom):</p>
                            <ul class="list-disc list-inside text-text-secondary space-y-1">
                                <li><strong>Kolom A:</strong> NIS (NIPD siswa yang terdaftar di sekolah).</li>
                                <li><strong>Kolom B:</strong> Nama Siswa.</li>
                                <li>Isi data mulai dari baris ke-5 dan simpan file dalam format .xlsx atau .xls.</li>
                            </ul>
                        </div>

                        <button wire:click="downloadTemplatePerEkskul"
                                @if(!$selectedImportEkskulId) disabled @endif
                                class="btn btn-secondary text-xs flex items-center justify-center gap-2 w-full py-2.5 font-bold disabled:opacity-50">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Unduh Template Excel UPD Terpilih</span>
                        </button>
                    </div>
                </div>

                <!-- Box 2: Upload File Excel -->
                <div class="card border border-border p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-text-main">Langkah 2: Unggah File Excel</h3>
                            <p class="text-xs text-text-secondary">Pilih file Excel yang telah Anda isi data NIS siswa.</p>
                        </div>
                    </div>

                    @if(!$selectedImportEkskulId)
                        <div class="p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900 rounded-xl text-amber-800 dark:text-amber-300 text-xs">
                            Silakan pilih UPD Pilihan pada <strong>Langkah 1</strong> terlebih dahulu sebelum mengunggah file.
                        </div>
                    @else
                        <div class="space-y-4">
                            <div class="border-2 border-dashed border-border rounded-xl p-6 text-center hover:border-primary/50 transition">
                                <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv" class="file-input max-w-sm mx-auto">
                                <p class="text-[11px] text-text-secondary mt-2">Mendukung format .xlsx, .xls, atau .csv (Maksimal 5MB)</p>
                                @error('importFile') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div wire:loading wire:target="importFile" class="text-xs text-blue-600 flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Membaca dan memverifikasi isi file Excel... mohon tunggu sebentar.</span>
                            </div>

                            <p class="text-xs text-text-secondary">
                                Setelah file dipilih, sistem akan langsung menampilkan <strong>Preview & Validasi Cek Error</strong> sebelum data disimpan.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- TAMPILAN PREVIEW & VALIDASI ERROR SEBELUM COMMIT -->
            <div class="card border border-border p-6 space-y-5">
                <!-- Header Preview -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-border pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                            <h3 class="text-lg font-bold text-text-main">
                                Preview Import Anggota: {{ $importPreviewData['target_ekskul_nama'] }}
                            </h3>
                        </div>
                        <p class="text-xs text-text-secondary mt-1">
                            Periksa rincian data siswa di bawah ini. Siswa yang sebelumnya terdaftar di UPD pilihan lain akan otomatis dipindahkan ke UPD ini.
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="cancelImportPreview" class="btn btn-secondary text-xs">
                            Batal / Upload Ulang
                        </button>
                        <button type="button" wire:click="commitImport"
                                @if($importPreviewData['valid_count'] === 0) disabled @endif
                                class="btn btn-primary text-xs font-bold py-2 px-4 flex items-center gap-1.5 disabled:opacity-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>OK, Proses Import ({{ $importPreviewData['valid_count'] }} Siswa)</span>
                        </button>
                    </div>
                </div>

                <!-- KPI Badges Preview -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                    <div class="p-3 rounded-xl bg-surface border border-border text-center">
                        <p class="text-[11px] text-text-secondary font-medium">Total Baris File</p>
                        <p class="text-xl font-bold text-text-main">{{ $importPreviewData['total_rows'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900 text-center">
                        <p class="text-[11px] text-blue-800 dark:text-blue-300 font-medium">Siswa Baru</p>
                        <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $importPreviewData['new_count'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900 text-center">
                        <p class="text-[11px] text-amber-800 dark:text-amber-300 font-medium">Mutasi dari UPD Lain</p>
                        <p class="text-xl font-bold text-amber-600 dark:text-amber-400">{{ $importPreviewData['transfer_count'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900 text-center">
                        <p class="text-[11px] text-emerald-800 dark:text-emerald-300 font-medium">Sudah di UPD Ini</p>
                        <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ $importPreviewData['already_count'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900 text-center">
                        <p class="text-[11px] text-rose-800 dark:text-rose-300 font-medium">Error / Dilewati</p>
                        <p class="text-xl font-bold text-rose-600 dark:text-rose-400">{{ $importPreviewData['error_count'] }}</p>
                    </div>
                </div>

                @if($importPreviewData['error_count'] > 0)
                    <div class="p-3 bg-rose-50/80 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900 rounded-xl text-rose-800 dark:text-rose-300 text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Ditemukan {{ $importPreviewData['error_count'] }} baris bermasalah (NIS tidak ditemukan atau duplikat). Baris error akan otomatis dilewati saat menekan tombol OK.</span>
                    </div>
                @endif

                <!-- Tabel Baris Preview -->
                <div class="overflow-x-auto border border-border rounded-xl">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-surface-hover/80 text-text-secondary text-xs uppercase tracking-wider border-b border-border">
                            <tr>
                                <th class="p-3 text-center w-12">Baris</th>
                                <th class="p-3 w-32">NIS</th>
                                <th class="p-3">Nama Siswa (Database)</th>
                                <th class="p-3 w-36">Kelas / Rombel</th>
                                <th class="p-3 w-64">Status & Tindakan Mutasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border text-xs">
                            @forelse($importPreviewData['rows'] as $row)
                                <tr class="hover:bg-surface-hover/50 {{ !$row['is_valid'] ? 'bg-rose-50/20 dark:bg-rose-950/10' : '' }}">
                                    <td class="p-3 text-center text-text-secondary font-mono">{{ $row['row_num'] }}</td>
                                    <td class="p-3 font-mono font-bold {{ !$row['is_valid'] ? 'text-rose-600' : 'text-text-main' }}">
                                        {{ $row['nis'] ?: '-' }}
                                    </td>
                                    <td class="p-3">
                                        <div class="font-semibold text-text-main">{{ $row['nama_db'] !== '-' ? $row['nama_db'] : ($row['nama_excel'] ?: '-') }}</div>
                                        @if($row['nama_excel'] && $row['nama_db'] !== '-' && strtolower($row['nama_excel']) !== strtolower($row['nama_db']))
                                            <div class="text-[10px] text-text-secondary">Di file: {{ $row['nama_excel'] }}</div>
                                        @endif
                                    </td>
                                    <td class="p-3 text-text-secondary font-medium">
                                        {{ $row['rombel'] }}
                                    </td>
                                    <td class="p-3">
                                        @if($row['status'] === 'new')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                                                Siswa Baru
                                            </span>
                                        @elseif($row['status'] === 'transfer')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300" title="{{ $row['label'] }}">
                                                {{ $row['label'] }}
                                            </span>
                                        @elseif($row['status'] === 'already')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                                Sudah di UPD Ini
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                                {{ $row['label'] }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-text-secondary">
                                        Tidak ada baris data yang ditemukan dalam file.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif

    <!-- Modal Tambah Anggota Manual -->
    @if($showAddModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="card max-w-xl w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-lg font-bold text-text-main">Tambah Siswa Anggota UPD</h3>
                    <button wire:click="$set('showAddModal', false)" class="text-text-secondary hover:text-text-main text-lg">&times;</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-text-main">Pilih Unit Pengembangan Diri (UPD) *</label>
                        <select wire:model="selectedEkskulId" class="select w-full text-sm">
                            <option value="">-- Pilih UPD Tujuan --</option>
                            @foreach($allEkskuls as $e)
                                <option value="{{ $e->id }}">{{ $e->nama }} {{ $e->is_wajib ? '(Wajib Tingkat X)' : '(Pilihan)' }}</option>
                            @endforeach
                        </select>
                        @error('selectedEkskulId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1 text-text-main">Cari Siswa *</label>
                        <input type="text" wire:model.live.debounce.300ms="searchSiswaQuery" placeholder="Ketik nama / NISN / NIPD..." class="input w-full text-sm">
                    </div>

                    @if(count($siswaSearchResults) > 0)
                        <div class="border border-border rounded-xl divide-y divide-border max-h-48 overflow-y-auto bg-surface">
                            @foreach($siswaSearchResults as $res)
                                <label class="p-2.5 flex items-center gap-3 hover:bg-surface-hover cursor-pointer text-xs">
                                    <input type="checkbox" wire:model="selectedSiswaIds" value="{{ $res->id }}" class="rounded border-border text-primary focus:ring-primary">
                                    <div class="flex-1">
                                        <span class="font-semibold text-text-main">{{ $res->nama }}</span>
                                        <span class="text-text-secondary ml-2">({{ $res->rombel?->nama ?? '-' }})</span>
                                        <div class="text-[11px] text-text-secondary font-mono">NISN: {{ $res->nisn }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    @if(count($selectedSiswaIds) > 0)
                        <p class="text-xs text-primary font-medium">
                            {{ count($selectedSiswaIds) }} siswa dipilih untuk didaftarkan.
                        </p>
                    @endif
                    @error('selectedSiswaIds') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="$set('showAddModal', false)" class="btn btn-secondary text-xs">Batal</button>
                        <button type="button" wire:click="saveAddAnggota" class="btn btn-primary text-xs">Tambahkan Anggota</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Pindah / Mutasi UPD (Single atau Massal) -->
    @if($showTransferModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="card max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-lg font-bold text-text-main">
                        {{ $isMassTransfer ? 'Pindahkan ' . count($selectedAnggotaIdsForTransfer) . ' Siswa Terpilih ke UPD Lain' : 'Mutasi / Pindah Siswa ke UPD Lain' }}
                    </h3>
                    <button wire:click="$set('showTransferModal', false)" class="text-text-secondary hover:text-text-main text-lg">&times;</button>
                </div>

                @if(!$isMassTransfer && $transferAnggotaInfo)
                    <div class="p-3 bg-surface-hover rounded-xl text-xs space-y-1 border border-border">
                        <p class="font-bold text-text-main">{{ $transferAnggotaInfo->siswa?->nama }}</p>
                        <p class="text-text-secondary">Kelas: {{ $transferAnggotaInfo->siswa?->rombel?->nama }} | NISN: {{ $transferAnggotaInfo->siswa?->nisn }}</p>
                        <p class="text-rose-600 font-medium">UPD Asal: {{ $transferAnggotaInfo->ekstrakurikuler?->nama }}</p>
                    </div>
                @else
                    <div class="p-3 bg-blue-50 dark:bg-blue-950/40 rounded-xl text-xs border border-blue-200 dark:border-blue-900 text-blue-800 dark:text-blue-300">
                        Memindahkan <strong>{{ count($selectedAnggotaIdsForTransfer) }} siswa</strong> dari UPD saat ini ke UPD pilihan baru.
                    </div>
                @endif

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-text-main">Pilih UPD Tujuan *</label>
                        <select wire:model="targetEkskulId" class="select w-full text-xs">
                            <option value="">-- Pilih UPD Baru --</option>
                            @foreach($allEkskuls as $e)
                                @if(!$e->is_wajib && ($isMassTransfer || $e->id != ($transferAnggotaInfo?->ekstrakurikuler_id ?? 0)))
                                    <option value="{{ $e->id }}">{{ $e->nama }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('targetEkskulId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1 text-text-main">Alasan / Catatan Pindah</label>
                        <input type="text" wire:model="transferCatatan" class="input w-full text-xs" placeholder="Misal: Penyesuaian minat bakat siswa pada periode rapor ini">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="$set('showTransferModal', false)" class="btn btn-secondary text-xs">Batal</button>
                        <button type="button" wire:click="executeTransfer" class="btn btn-primary text-xs">Proses Pindah Siswa</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Ringkas Khusus Daftarkan Siswa (Dari Tab Audit Kepatuhan Tanpa Input Cari Siswa) -->
    @if($showEnrollModal && $enrollingSiswaInfo)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="card max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        <span>Daftarkan Siswa ke UPD</span>
                    </h3>
                    <button wire:click="$set('showEnrollModal', false)" class="text-text-secondary hover:text-text-main text-lg">&times;</button>
                </div>

                <!-- Info Siswa Terkunci (Readonly / Badge) -->
                <div class="p-3.5 bg-surface-hover rounded-xl border border-border space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-sm text-text-main">{{ $enrollingSiswaInfo->nama }}</span>
                        <span class="px-2 py-0.5 rounded font-bold bg-primary/10 text-primary border border-primary/20">
                            {{ $enrollingSiswaInfo->rombel?->nama ?? '-' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-text-secondary font-mono text-[11px]">
                        <span>NISN: {{ $enrollingSiswaInfo->nisn }}</span>
                        @if($enrollingSiswaInfo->nipd)
                            <span>&bull; NIPD: {{ $enrollingSiswaInfo->nipd }}</span>
                        @endif
                    </div>
                    @if($enrollingMissingPramuka)
                        <div class="pt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                Status: Belum Masuk Pramuka Wajib (Tingkat X)
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Opsi Cepat 1-Klik Jika Belum Pramuka (Tingkat X) -->
                @if($enrollingMissingPramuka && $pramukaEkskulId)
                    <div class="p-3 bg-rose-50/70 dark:bg-rose-950/30 rounded-xl border border-rose-200 dark:border-rose-900 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-rose-900 dark:text-rose-300">Pramuka Wajib Tingkat X</span>
                            <button type="button" wire:click="quickEnrollPramuka" class="btn btn-primary text-xs py-1 px-3 bg-rose-600 hover:bg-rose-700 border-none text-white font-bold">
                                Daftarkan ke Pramuka Sekarang
                            </button>
                        </div>
                        <p class="text-[11px] text-rose-700 dark:text-rose-400">
                            Siswa kelas X wajib mengikuti Pramuka Gugus Depan. Klik tombol di atas untuk langsung mendaftarkan 1-klik.
                        </p>
                    </div>

                    <div class="relative flex py-1 items-center">
                        <div class="flex-grow border-t border-border"></div>
                        <span class="flex-shrink mx-3 text-text-secondary text-[11px] uppercase font-semibold">atau pilih UPD lain</span>
                        <div class="flex-grow border-t border-border"></div>
                    </div>
                @endif

                <!-- Form Pemilihan UPD Tujuan (Tanpa Input Pencarian Siswa) -->
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-text-main">Pilih Unit Pengembangan Diri (UPD) *</label>
                        <select wire:model="enrollingTargetEkskulId" class="select w-full text-xs">
                            <option value="">-- Pilih UPD Tujuan --</option>
                            @foreach($allEkskuls as $e)
                                <option value="{{ $e->id }}">
                                    {{ $e->nama }} {{ $e->is_wajib ? '(Wajib Tingkat X)' : '(Pilihan)' }}
                                </option>
                            @endforeach
                        </select>
                        @error('enrollingTargetEkskulId') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="$set('showEnrollModal', false)" class="btn btn-secondary text-xs">Batal</button>
                        <button type="button" wire:click="saveEnrollSiswa" class="btn btn-primary text-xs font-bold">Daftarkan Siswa Ini</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
