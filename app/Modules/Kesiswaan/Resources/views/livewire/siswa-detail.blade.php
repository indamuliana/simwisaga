<div class="space-y-6">
    <!-- Breadcrumb & Top Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs text-text-secondary mb-1">
                <a href="{{ route('kesiswaan.siswa') }}" class="hover:text-primary transition">Kesiswaan</a>
                <span>&rsaquo;</span>
                <a href="{{ route('kesiswaan.siswa') }}" class="hover:text-primary transition">Data Induk Siswa</a>
                <span>&rsaquo;</span>
                <span class="text-text-main font-semibold">Detail Profil</span>
            </nav>
            <h1 class="text-xl sm:text-2xl font-bold text-text-main flex items-center gap-2.5">
                <span>{{ $siswa->nama }}</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider {{ $siswa->status === 'aktif' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : ($siswa->status === 'lulus' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300') }}">
                    {{ $siswa->status }}
                </span>
            </h1>
        </div>

        <!-- Back Button & Quick Actions -->
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('kesiswaan.siswa') }}" class="btn btn-secondary inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Data Siswa
            </a>

            @can('kesiswaan.siswa.edit')
            <a href="{{ route('kesiswaan.siswa.edit', $siswa->id) }}" class="btn btn-primary inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold shadow-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Data Siswa
            </a>
            @endcan

            <!-- Download Dropdown / Buttons -->
            <div class="inline-flex rounded-lg border border-border bg-surface p-0.5 shadow-xs" role="group">
                <button type="button" wire:click="downloadDokumen('pdf')" title="Unduh Buku Induk (PDF)" class="px-2.5 py-1.5 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-surface-hover rounded-md transition inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    PDF
                </button>
                <button type="button" wire:click="downloadDokumen('word')" title="Unduh Biodata (.doc)" class="px-2.5 py-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:bg-surface-hover rounded-md transition inline-flex items-center gap-1 border-l border-border">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Word
                </button>
                <button type="button" wire:click="downloadDokumen('excel')" title="Unduh Biodata (.xlsx)" class="px-2.5 py-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:bg-surface-hover rounded-md transition inline-flex items-center gap-1 border-l border-border">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Excel
                </button>
            </div>
        </div>
    </div>

    <!-- ── Hero Student Card ── -->
    <div class="card p-6 bg-surface border border-border shadow-xs rounded-2xl">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
            <!-- Student Avatar -->
            <div class="shrink-0 relative">
                @if($siswa->foto_path && file_exists(public_path('storage/' . $siswa->foto_path)))
                    <img src="{{ asset('storage/' . $siswa->foto_path) }}" 
                         alt="{{ $siswa->nama }}" 
                         class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover border-2 border-primary/20 shadow-md">
                @else
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-gradient-to-br from-primary/20 to-indigo-500/20 text-primary border-2 border-primary/20 flex flex-col items-center justify-center font-bold text-2xl shadow-inner">
                        <span>{{ strtoupper(substr($siswa->nama, 0, 2)) }}</span>
                        <span class="text-[10px] font-mono text-text-secondary mt-1 font-normal">{{ $siswa->jk === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                @endif
                <span class="absolute -bottom-1.5 -right-1.5 w-5 h-5 rounded-full border-2 border-surface {{ $siswa->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}" title="{{ $siswa->is_active ? 'Aktif' : 'Tidak Aktif' }}"></span>
            </div>

            <!-- Student Main Attributes -->
            <div class="flex-1 text-center md:text-left space-y-3">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-text-main tracking-tight">{{ $siswa->nama }}</h2>
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-x-4 gap-y-1 text-xs text-text-secondary font-mono mt-1">
                        <span>NIS/NIPD: <strong class="text-text-main">{{ $siswa->nipd ?: ($siswa->nisn ?: '-') }}</strong></span>
                        <span>&bull;</span>
                        <span>NISN: <strong class="text-text-main">{{ $canViewSensitive ? ($siswa->nisn ?: '-') : \App\Core\Support\DataMasker::maskNisn($siswa->nisn) }}</strong></span>
                        <span>&bull;</span>
                        <span>NIK: <strong class="text-text-main">{{ $canViewSensitive ? ($siswa->nik ?: '-') : \App\Core\Support\DataMasker::maskNik($siswa->nik) }}</strong></span>
                    </div>
                </div>

                <!-- Badges Matrix -->
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 pt-1">
                    <!-- Rombel Badge -->
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 text-xs font-bold border border-blue-200 dark:border-blue-900">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Kelas: {{ $siswa->rombel?->nama ?? 'Belum ditentukan' }}</span>
                        @if($siswa->rombel?->waliKelas)
                            <span class="font-normal text-[11px] opacity-75">({{ $siswa->rombel->waliKelas->name }})</span>
                        @endif
                    </div>

                    <!-- Rayon Badge -->
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 text-xs font-bold border border-emerald-200 dark:border-emerald-900">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Rayon: {{ $siswa->rayon?->nama ?? 'Belum di-plot' }}</span>
                        @if($siswa->rayon?->pembimbing)
                            <span class="font-normal text-[11px] opacity-75">({{ $siswa->rayon->pembimbing->name }})</span>
                        @endif
                    </div>

                    <!-- Program Badge -->
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 text-xs font-bold border border-amber-200 dark:border-amber-900">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>{{ $siswa->program?->nama ?? 'Reguler' }}</span>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-surface-hover text-text-secondary text-xs font-medium border border-border">
                        <span>JK: <strong>{{ $siswa->jk === 'L' ? 'Laki-laki' : 'Perempuan' }}</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Tab Navigation Bar ── -->
    <div class="border-b border-border flex items-center gap-2 overflow-x-auto no-scrollbar pt-2">
        <!-- Tab 1: Biodata -->
        <button type="button" 
                wire:click="setTab('biodata')"
                class="px-4 py-2.5 text-xs sm:text-sm font-bold border-b-2 transition whitespace-nowrap inline-flex items-center gap-2 {{ $activeTab === 'biodata' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            1. Biodata &amp; Orang Tua
        </button>

        <!-- Tab 2: Pilihan Kurikulum / Minat -->
        <button type="button" 
                wire:click="setTab('pilihan')"
                class="px-4 py-2.5 text-xs sm:text-sm font-bold border-b-2 transition whitespace-nowrap inline-flex items-center gap-2 {{ $activeTab === 'pilihan' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
            2. Akademik &amp; Pilihan Minat
            @php
                $totalPilihan = $siswa->seniBudayaAnggotas->count() + $siswa->ekstrakurikulerAnggotas->count() + $siswa->mapelPilihanAnggotas->count();
            @endphp
            @if($totalPilihan > 0)
                <span class="px-1.5 py-0.2 bg-primary/10 text-primary text-[10px] rounded-full font-extrabold">{{ $totalPilihan }}</span>
            @endif
        </button>

        <!-- Tab 3: Prestasi & Beasiswa -->
        <button type="button" 
                wire:click="setTab('prestasi')"
                class="px-4 py-2.5 text-xs sm:text-sm font-bold border-b-2 transition whitespace-nowrap inline-flex items-center gap-2 {{ $activeTab === 'prestasi' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
            </svg>
            3. Prestasi &amp; Beasiswa
            @if($siswa->prestasis->count() > 0 || $siswa->otaPenerimas->count() > 0 || $siswa->penerima_kip || $siswa->penerima_kps)
                <span class="px-1.5 py-0.2 bg-amber-500/20 text-amber-600 dark:text-amber-400 text-[10px] rounded-full font-extrabold">
                    {{ $siswa->prestasis->count() + ($siswa->otaPenerimas->count() > 0 ? 1 : 0) }}
                </span>
            @endif
        </button>

        <!-- Tab 4: Riwayat & Presensi -->
        <button type="button" 
                wire:click="setTab('riwayat')"
                class="px-4 py-2.5 text-xs sm:text-sm font-bold border-b-2 transition whitespace-nowrap inline-flex items-center gap-2 {{ $activeTab === 'riwayat' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            4. Riwayat &amp; Presensi
        </button>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 1: BIODATA & ORANG TUA                                          -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    @if($activeTab === 'biodata')
    <div class="space-y-6">
        <!-- 1.1 Data Pribadi & Kependudukan -->
        <div class="card p-6 bg-surface border border-border shadow-xs rounded-2xl space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-text-secondary flex items-center gap-2">
                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                </svg>
                Identitas Diri &amp; Kependudukan
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                <div class="p-3 rounded-xl bg-surface-hover/50 border border-border">
                    <span class="text-text-secondary block">Tempat, Tanggal Lahir</span>
                    <span class="font-bold text-text-main text-sm">
                        {{ $siswa->tempat_lahir ?? '-' }}, {{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->translatedFormat('d F Y') : '-' }}
                    </span>
                    @if($siswa->tanggal_lahir)
                        <span class="text-[11px] text-text-secondary block mt-0.5 font-normal">({{ $siswa->tanggal_lahir->age }} Tahun)</span>
                    @endif
                </div>

                <div class="p-3 rounded-xl bg-surface-hover/50 border border-border">
                    <span class="text-text-secondary block">Agama</span>
                    <span class="font-bold text-text-main text-sm">{{ $siswa->agama ?? '-' }}</span>
                </div>

                <div class="p-3 rounded-xl bg-surface-hover/50 border border-border">
                    <span class="text-text-secondary block">Kewarganegaraan</span>
                    <span class="font-bold text-text-main text-sm">{{ $siswa->negara ?? 'Indonesia' }}</span>
                </div>

                <div class="p-3 rounded-xl bg-surface-hover/50 border border-border">
                    <span class="text-text-secondary block">No. Kartu Keluarga (KK)</span>
                    <span class="font-mono font-bold text-text-main text-sm">{{ $canViewSensitive ? ($siswa->no_kk ?? '-') : \App\Core\Support\DataMasker::maskNik($siswa->no_kk) }}</span>
                </div>

                <div class="p-3 rounded-xl bg-surface-hover/50 border border-border">
                    <span class="text-text-secondary block">No. Akta Kelahiran</span>
                    <span class="font-mono font-bold text-text-main text-sm">{{ $siswa->no_akta_lahir ?? '-' }}</span>
                </div>

                <div class="p-3 rounded-xl bg-surface-hover/50 border border-border">
                    <span class="text-text-secondary block">Anak Ke- / Jml Saudara</span>
                    <span class="font-bold text-text-main text-sm">Anak ke-{{ $siswa->anak_ke_berapa ?? '-' }} dari {{ $siswa->jml_saudara_kandung ?? '-' }} bersaudara</span>
                </div>

                <div class="p-3 rounded-xl bg-surface-hover/50 border border-border">
                    <span class="text-text-secondary block">Sekolah Asal (SMP/MTs)</span>
                    <span class="font-bold text-text-main text-sm">{{ $siswa->sekolah_asal ?? '-' }}</span>
                </div>

                <div class="p-3 rounded-xl bg-surface-hover/50 border border-border">
                    <span class="text-text-secondary block">No. Peserta Ujian Nasional (UN)</span>
                    <span class="font-mono font-bold text-text-main text-sm">{{ $siswa->no_peserta_un ?? '-' }}</span>
                </div>

                <div class="p-3 rounded-xl bg-surface-hover/50 border border-border">
                    <span class="text-text-secondary block">No. Seri Ijazah &amp; SKHUN</span>
                    <span class="font-mono font-bold text-text-main text-sm">Ijazah: {{ $siswa->no_seri_ijazah ?? '-' }} | SKHUN: {{ $siswa->skhun ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- 1.2 Alamat & Domisili -->
        <div class="card p-6 bg-surface border border-border shadow-xs rounded-2xl space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-text-secondary flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Alamat &amp; Domisili Tempat Tinggal
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div class="p-4 rounded-xl bg-surface-hover/50 border border-border space-y-2">
                    <span class="text-text-secondary block font-semibold">Alamat Rumah Lengkap</span>
                    <p class="text-text-main text-sm leading-relaxed font-medium">
                        {{ $siswa->alamat ?? 'Belum ada data alamat' }}
                        @if($siswa->rt || $siswa->rw) (RT {{ $siswa->rt }}/RW {{ $siswa->rw }}) @endif
                    </p>
                    <div class="text-text-secondary text-xs pt-1 space-y-0.5">
                        <div>Dusun / Kampung: <strong>{{ $siswa->dusun ?? '-' }}</strong></div>
                        <div>Kelurahan / Desa: <strong>{{ $siswa->kelurahan ?? '-' }}</strong></div>
                        <div>Kecamatan: <strong>{{ $siswa->kecamatan ?? '-' }}</strong></div>
                        <div>Kabupaten / Kota: <strong>{{ $siswa->kabupaten_kota ?? '-' }}</strong></div>
                        <div>Provinsi: <strong>{{ $siswa->provinsi ?? '-' }}</strong> | Kode Pos: <strong>{{ $siswa->kode_pos ?? '-' }}</strong></div>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-surface-hover/50 border border-border space-y-3">
                    <span class="text-text-secondary block font-semibold">Aksesibilitas &amp; Transportasi</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <span class="text-text-secondary block">Jenis Tinggal</span>
                            <span class="font-bold text-text-main">{{ $siswa->jenis_tinggal ?? 'Bersama Orang Tua' }}</span>
                        </div>
                        <div>
                            <span class="text-text-secondary block">Alat Transportasi</span>
                            <span class="font-bold text-text-main">{{ $siswa->alat_transportasi ?? 'Sepeda Motor / Angkutan' }}</span>
                        </div>
                        <div>
                            <span class="text-text-secondary block">Jarak ke Sekolah</span>
                            <span class="font-bold text-text-main">{{ $siswa->jarak_ke_sekolah_km ? $siswa->jarak_ke_sekolah_km . ' KM' : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-text-secondary block">Koordinat Geografis</span>
                            <span class="font-mono text-text-main">{{ $siswa->lintang && $siswa->bujur ? $siswa->lintang . ', ' . $siswa->bujur : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.3 Data Orang Tua & Wali -->
        <div class="card p-6 bg-surface border border-border shadow-xs rounded-2xl space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-text-secondary flex items-center gap-2">
                <svg class="w-4 h-4 text-pink-600 dark:text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Data Orang Tua &amp; Wali Siswa
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <!-- Ayah Kandung -->
                @php $ayah = $siswa->ayah; @endphp
                <div class="p-4 rounded-xl bg-blue-50/50 dark:bg-blue-950/20 border border-blue-200/60 dark:border-blue-900/60 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-blue-700 dark:text-blue-300 text-sm">Ayah Kandung</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ ($ayah?->status_hidup ?? 'Hidup') === 'Hidup' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                            {{ $ayah?->status_hidup ?? 'Hidup' }}
                        </span>
                    </div>
                    <div class="text-base font-bold text-text-main">{{ $ayah?->nama ?? '-' }}</div>
                    <div class="space-y-1 text-text-secondary pt-1">
                        <div>NIK: <strong class="font-mono text-text-main">{{ $canViewSensitive ? ($ayah?->nik ?? '-') : \App\Core\Support\DataMasker::maskNik($ayah?->nik) }}</strong></div>
                        <div>Tahun Lahir: <strong class="text-text-main">{{ $ayah?->tahun_lahir ?? '-' }}</strong></div>
                        <div>Pendidikan: <strong class="text-text-main">{{ $ayah?->pendidikan ?? '-' }}</strong></div>
                        <div>Pekerjaan: <strong class="text-text-main">{{ $ayah?->pekerjaan ?? '-' }}</strong></div>
                        <div>Penghasilan: <strong class="text-text-main">{{ $ayah?->penghasilan ?? '-' }}</strong></div>
                        <div>No. Kontak/HP: <strong class="text-text-main">{{ $ayah?->no_hp ?? '-' }}</strong></div>
                    </div>
                </div>

                <!-- Ibu Kandung -->
                @php $ibu = $siswa->ibu; @endphp
                <div class="p-4 rounded-xl bg-pink-50/50 dark:bg-pink-950/20 border border-pink-200/60 dark:border-pink-900/60 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-pink-700 dark:text-pink-300 text-sm">Ibu Kandung</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ ($ibu?->status_hidup ?? 'Hidup') === 'Hidup' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                            {{ $ibu?->status_hidup ?? 'Hidup' }}
                        </span>
                    </div>
                    <div class="text-base font-bold text-text-main">{{ $ibu?->nama ?? '-' }}</div>
                    <div class="space-y-1 text-text-secondary pt-1">
                        <div>NIK: <strong class="font-mono text-text-main">{{ $canViewSensitive ? ($ibu?->nik ?? '-') : \App\Core\Support\DataMasker::maskNik($ibu?->nik) }}</strong></div>
                        <div>Tahun Lahir: <strong class="text-text-main">{{ $ibu?->tahun_lahir ?? '-' }}</strong></div>
                        <div>Pendidikan: <strong class="text-text-main">{{ $ibu?->pendidikan ?? '-' }}</strong></div>
                        <div>Pekerjaan: <strong class="text-text-main">{{ $ibu?->pekerjaan ?? '-' }}</strong></div>
                        <div>Penghasilan: <strong class="text-text-main">{{ $ibu?->penghasilan ?? '-' }}</strong></div>
                        <div>No. Kontak/HP: <strong class="text-text-main">{{ $ibu?->no_hp ?? '-' }}</strong></div>
                    </div>
                </div>

                <!-- Wali Siswa -->
                @php $wali = $siswa->wali; @endphp
                <div class="p-4 rounded-xl bg-surface-hover/50 border border-border space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-text-main text-sm">Wali Siswa</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            {{ $wali ? 'Terdaftar' : 'Tidak Ada' }}
                        </span>
                    </div>
                    <div class="text-base font-bold text-text-main">{{ $wali?->nama ?? '-' }}</div>
                    <div class="space-y-1 text-text-secondary pt-1">
                        <div>NIK: <strong class="font-mono text-text-main">{{ $canViewSensitive ? ($wali?->nik ?? '-') : \App\Core\Support\DataMasker::maskNik($wali?->nik) }}</strong></div>
                        <div>Tahun Lahir: <strong class="text-text-main">{{ $wali?->tahun_lahir ?? '-' }}</strong></div>
                        <div>Pendidikan: <strong class="text-text-main">{{ $wali?->pendidikan ?? '-' }}</strong></div>
                        <div>Pekerjaan: <strong class="text-text-main">{{ $wali?->pekerjaan ?? '-' }}</strong></div>
                        <div>Penghasilan: <strong class="text-text-main">{{ $wali?->penghasilan ?? '-' }}</strong></div>
                        <div>No. Kontak/HP: <strong class="text-text-main">{{ $wali?->no_hp ?? '-' }}</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.4 Fisik, Kontak & Akun Login -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Fisik & Kesehatan -->
            <div class="card p-6 bg-surface border border-border shadow-xs rounded-2xl space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-text-secondary">Fisik &amp; Kesehatan</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="p-3 rounded-lg bg-surface-hover border border-border">
                        <span class="text-text-secondary block">Tinggi Badan</span>
                        <span class="font-bold text-text-main text-base">{{ $siswa->tinggi_badan ? $siswa->tinggi_badan . ' cm' : '-' }}</span>
                    </div>
                    <div class="p-3 rounded-lg bg-surface-hover border border-border">
                        <span class="text-text-secondary block">Berat Badan</span>
                        <span class="font-bold text-text-main text-base">{{ $siswa->berat_badan ? $siswa->berat_badan . ' kg' : '-' }}</span>
                    </div>
                    <div class="p-3 rounded-lg bg-surface-hover border border-border">
                        <span class="text-text-secondary block">Lingkar Kepala</span>
                        <span class="font-bold text-text-main text-base">{{ $siswa->lingkar_kepala ? $siswa->lingkar_kepala . ' cm' : '-' }}</span>
                    </div>
                    <div class="p-3 rounded-lg bg-surface-hover border border-border">
                        <span class="text-text-secondary block">Kebutuhan Khusus</span>
                        <span class="font-bold text-text-main text-xs">{{ $siswa->kebutuhan_khusus ?? 'Tidak Ada' }}</span>
                    </div>
                </div>
            </div>

            <!-- Kontak & Akun Login Portal -->
            <div class="card p-6 bg-surface border border-border shadow-xs rounded-2xl space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-text-secondary">Kontak &amp; Kredensial Portal</h3>
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between p-2 rounded-lg bg-surface-hover border border-border">
                        <span class="text-text-secondary">No. Handphone / WA Siswa:</span>
                        <strong class="font-mono text-text-main">{{ $siswa->hp ?: ($siswa->telepon ?: '-') }}</strong>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-lg bg-surface-hover border border-border">
                        <span class="text-text-secondary">Email Siswa:</span>
                        <strong class="text-text-main">{{ $siswa->email ?? '-' }}</strong>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-lg bg-surface-hover border border-border">
                        <span class="text-text-secondary">Akun Login Siswa (Username):</span>
                        <span class="font-mono font-bold text-primary bg-primary/10 px-2 py-0.5 rounded">{{ $siswa->nipd ?: $siswa->nisn }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-lg bg-surface-hover border border-border">
                        <span class="text-text-secondary">Akun Login Orang Tua (Username):</span>
                        <span class="font-mono font-bold text-pink-600 bg-pink-500/10 px-2 py-0.5 rounded">ortu_{{ $siswa->nipd ?: $siswa->nisn }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 2: AKADEMIK & PILIHAN MINAT                                     -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    @if($activeTab === 'pilihan')
    <div class="space-y-6">
        <!-- 2.1 Seni Budaya yang Dipilih -->
        <div class="card p-6 bg-surface border border-border shadow-xs rounded-2xl space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                        <span class="p-1.5 rounded-lg bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                            </svg>
                        </span>
                        Seni Budaya yang Dipilih
                    </h3>
                    <p class="text-xs text-text-secondary">Cabang seni budaya pilihan siswa, pembimbing, dan capaian penilaian.</p>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-surface-hover text-text-secondary border border-border">
                    {{ $siswa->seniBudayaAnggotas->count() }} Terdaftar
                </span>
            </div>

            @if($siswa->seniBudayaAnggotas->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($siswa->seniBudayaAnggotas as $sbAnggota)
                    <div class="p-4 rounded-xl bg-surface-hover/40 border border-border space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h4 class="font-bold text-text-main text-base">{{ $sbAnggota->seniBudaya?->nama ?? 'Cabang Seni' }}</h4>
                                <div class="text-xs text-text-secondary">
                                    Pembimbing: <strong>{{ $sbAnggota->seniBudaya?->pembimbing?->name ?? 'Belum ditentukan' }}</strong>
                                </div>
                                <div class="text-[11px] text-text-secondary font-mono mt-0.5">
                                    Semester: {{ $sbAnggota->semester?->nama ?? '-' }} {{ $sbAnggota->semester?->tahunAjaran?->tahun ?? '' }}
                                </div>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase {{ ($sbAnggota->status_keanggotaan ?? 'aktif') === 'aktif' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                {{ $sbAnggota->status_keanggotaan ?? 'aktif' }}
                            </span>
                        </div>

                        <!-- Stats Grid Seni Budaya -->
                        <div class="grid grid-cols-3 gap-2 text-center text-xs pt-1 border-t border-border">
                            <div class="p-2 rounded-lg bg-surface border border-border">
                                <span class="text-[10px] text-text-secondary block">Nilai Akhir</span>
                                <span class="font-bold text-primary text-sm">{{ $sbAnggota->nilai_akhir !== null ? number_format($sbAnggota->nilai_akhir, 1) : '-' }}</span>
                            </div>
                            <div class="p-2 rounded-lg bg-surface border border-border">
                                <span class="text-[10px] text-text-secondary block">Predikat</span>
                                <span class="font-bold text-text-main text-sm">{{ $sbAnggota->nilai_huruf ?? '-' }}</span>
                            </div>
                            <div class="p-2 rounded-lg bg-surface border border-border">
                                <span class="text-[10px] text-text-secondary block">Kehadiran</span>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400 text-sm">
                                    {{ $sbAnggota->persentase_kehadiran !== null ? number_format($sbAnggota->persentase_kehadiran, 0) . '%' : ($sbAnggota->total_hadir ? $sbAnggota->total_hadir . 'x' : '-') }}
                                </span>
                            </div>
                        </div>

                        @if(!empty($sbAnggota->effective_deskripsi))
                        <div class="text-xs text-text-secondary p-2.5 rounded-lg bg-surface border border-border">
                            <span class="font-bold text-text-main block text-[11px] mb-0.5">Catatan Capaian:</span>
                            <p class="italic leading-relaxed">{{ $sbAnggota->effective_deskripsi }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 rounded-xl bg-surface-hover/30 border border-dashed border-border text-center text-xs text-text-secondary">
                    Siswa ini belum terdaftar di cabang Seni Budaya mana pun.
                </div>
            @endif
        </div>

        <!-- 2.2 Ekstrakurikuler / UPD -->
        <div class="card p-6 bg-surface border border-border shadow-xs rounded-2xl space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                        <span class="p-1.5 rounded-lg bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        Ekstrakurikuler &amp; Unit Pengembangan Diri (UPD)
                    </h3>
                    <p class="text-xs text-text-secondary">Keikutsertaan ekskul/UPD, pembina kegiatan, dan nilai partisipasi.</p>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-surface-hover text-text-secondary border border-border">
                    {{ $siswa->ekstrakurikulerAnggotas->count() }} Terdaftar
                </span>
            </div>

            @if($siswa->ekstrakurikulerAnggotas->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($siswa->ekstrakurikulerAnggotas as $eskulAnggota)
                    <div class="p-4 rounded-xl bg-surface-hover/40 border border-border space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h4 class="font-bold text-text-main text-base">{{ $eskulAnggota->ekstrakurikuler?->nama ?? 'Ekstrakurikuler' }}</h4>
                                <div class="text-xs text-text-secondary">
                                    Pembina/Instruktur: <strong>{{ $eskulAnggota->ekstrakurikuler?->pembina?->name ?? 'Belum ditentukan' }}</strong>
                                </div>
                                @if($eskulAnggota->ekstrakurikuler?->hari)
                                <div class="text-[11px] text-text-secondary mt-0.5">
                                    Jadwal: {{ $eskulAnggota->ekstrakurikuler->hari }} ({{ $eskulAnggota->ekstrakurikuler->jam_mulai }} - {{ $eskulAnggota->ekstrakurikuler->jam_selesai }}) di {{ $eskulAnggota->ekstrakurikuler->tempat ?? 'Sekolah' }}
                                </div>
                                @endif
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase {{ ($eskulAnggota->status_keanggotaan ?? 'aktif') === 'aktif' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                {{ $eskulAnggota->status_keanggotaan ?? 'aktif' }}
                            </span>
                        </div>

                        <!-- Stats Grid Ekskul -->
                        <div class="grid grid-cols-3 gap-2 text-center text-xs pt-1 border-t border-border">
                            <div class="p-2 rounded-lg bg-surface border border-border">
                                <span class="text-[10px] text-text-secondary block">Nilai Angka</span>
                                <span class="font-bold text-primary text-sm">{{ $eskulAnggota->nilai_angka !== null ? number_format($eskulAnggota->nilai_angka, 1) : '-' }}</span>
                            </div>
                            <div class="p-2 rounded-lg bg-surface border border-border">
                                <span class="text-[10px] text-text-secondary block">Predikat</span>
                                <span class="font-bold text-text-main text-sm">{{ $eskulAnggota->nilai_huruf ?? '-' }}</span>
                            </div>
                            <div class="p-2 rounded-lg bg-surface border border-border">
                                <span class="text-[10px] text-text-secondary block">Kehadiran</span>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400 text-sm">
                                    {{ $eskulAnggota->persentase_kehadiran !== null ? number_format($eskulAnggota->persentase_kehadiran, 0) . '%' : ($eskulAnggota->total_hadir ? $eskulAnggota->total_hadir . '/' . $eskulAnggota->total_pertemuan : '-') }}
                                </span>
                            </div>
                        </div>

                        @if(!empty($eskulAnggota->deskripsi))
                        <div class="text-xs text-text-secondary p-2.5 rounded-lg bg-surface border border-border">
                            <span class="font-bold text-text-main block text-[11px] mb-0.5">Catatan Perkembangan:</span>
                            <p class="italic leading-relaxed">{{ $eskulAnggota->deskripsi }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 rounded-xl bg-surface-hover/30 border border-dashed border-border text-center text-xs text-text-secondary">
                    Siswa ini belum terdaftar di Ekstrakurikuler / UPD mana pun.
                </div>
            @endif
        </div>

        <!-- 2.3 Mata Pelajaran Pilihan & Kelompok Khusus -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Mapel Pilihan -->
            <div class="card p-6 bg-surface border border-border shadow-xs rounded-2xl space-y-4">
                <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </span>
                    Mata Pelajaran Pilihan
                </h3>
                @if($siswa->mapelPilihanAnggotas->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($siswa->mapelPilihanAnggotas as $mapilAnggota)
                        <div class="p-3.5 rounded-xl bg-surface-hover border border-border space-y-1 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-text-main text-sm">{{ $mapilAnggota->mapelPilihan?->nama ?? 'Mapel Pilihan' }}</span>
                                <span class="px-2 py-0.5 rounded-full font-bold text-[10px] bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-900">
                                    {{ $mapilAnggota->status ?? 'Aktif' }}
                                </span>
                            </div>
                            <div class="text-text-secondary">Guru: <strong>{{ $mapilAnggota->mapelPilihan?->guru?->name ?? 'Belum ditentukan' }}</strong></div>
                            <div class="text-text-secondary font-mono text-[11px]">Tahun Ajaran: {{ $mapilAnggota->tahunAjaran?->tahun ?? '-' }}</div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 rounded-xl bg-surface-hover/30 border border-dashed border-border text-center text-xs text-text-secondary">
                        Belum ada Mata Pelajaran Pilihan yang dialokasikan.
                    </div>
                @endif
            </div>

            <!-- Custom Kelompok Lainnya -->
            <div class="card p-6 bg-surface border border-border shadow-xs rounded-2xl space-y-4">
                <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-teal-100 text-teal-700 dark:bg-teal-950 dark:text-teal-300">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </span>
                    Kelompok Minat / Khusus Lainnya
                </h3>
                @if($siswa->customKelompoks->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($siswa->customKelompoks as $cKelompok)
                        <div class="p-3.5 rounded-xl bg-surface-hover border border-border space-y-1 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-text-main text-sm flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $cKelompok->warna ?? '#3b82f6' }}"></span>
                                    {{ $cKelompok->nama }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full font-bold text-[10px] bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300 border border-teal-200 dark:border-teal-900">
                                    {{ $cKelompok->kategori ?: 'Khusus' }}
                                </span>
                            </div>
                            <div class="text-text-secondary">Pembimbing: <strong>{{ $cKelompok->pembimbing?->name ?? 'Belum ditentukan' }}</strong></div>
                            @if($cKelompok->keterangan)
                            <div class="text-text-secondary text-[11px] italic">{{ $cKelompok->keterangan }}</div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 rounded-xl bg-surface-hover/30 border border-dashed border-border text-center text-xs text-text-secondary">
                        Tidak ada kelompok minat / bimbingan khusus lainnya.
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 3: PRESTASI & BEASISWA                                          -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    @if($activeTab === 'prestasi')
    <div class="space-y-6">
        <!-- 3.1 Prestasi Siswa -->
        <div class="card p-6 bg-surface border border-border shadow-xs rounded-2xl space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                        <span class="p-1.5 rounded-lg bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </span>
                        Daftar Prestasi &amp; Penghargaan Siswa
                    </h3>
                    <p class="text-xs text-text-secondary">Riwayat capaian kejuaraan akademik, kejuruan, seni, dan olahraga.</p>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-surface-hover text-text-secondary border border-border">
                    {{ $siswa->prestasis->count() }} Prestasi
                </span>
            </div>

            @if($siswa->prestasis->isNotEmpty())
                <div class="space-y-3">
                    @foreach($siswa->prestasis as $prestasi)
                    <div class="p-4 rounded-xl bg-surface-hover/40 border border-border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="space-y-1.5 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-md text-xs font-black uppercase tracking-wide bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-300/40">
                                    {{ $prestasi->peringkat ?? 'Juara' }}
                                </span>
                                <h4 class="font-bold text-text-main text-sm sm:text-base">{{ $prestasi->nama_prestasi }}</h4>
                            </div>

                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-text-secondary">
                                <span>Tingkat: <strong class="text-text-main">{{ ucfirst($prestasi->tingkat ?? 'Sekolah') }}</strong></span>
                                <span>&bull;</span>
                                <span>Bidang: <strong class="text-text-main">{{ ucfirst($prestasi->bidang ?? $prestasi->kategori ?? 'Umum') }}</strong></span>
                                <span>&bull;</span>
                                <span>Penyelenggara: <strong class="text-text-main">{{ $prestasi->penyelenggara_nama ?? '-' }}</strong></span>
                                @if($prestasi->tanggal_perolehan)
                                <span>&bull;</span>
                                <span>Tanggal: <strong class="text-text-main">{{ \Carbon\Carbon::parse($prestasi->tanggal_perolehan)->translatedFormat('d M Y') }}</strong></span>
                                @endif
                            </div>

                            @if($prestasi->pivot && $prestasi->pivot->peran)
                            <div class="text-[11px] text-text-secondary">
                                Peran Siswa: <strong class="text-text-main">{{ ucfirst($prestasi->pivot->peran) }}</strong>
                                @if($prestasi->pivot->keterangan) - <span class="italic">{{ $prestasi->pivot->keterangan }}</span> @endif
                            </div>
                            @endif
                        </div>

                        <!-- Status Verifikasi & Berkas Sertifikat -->
                        <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-2 shrink-0 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-border">
                            <span class="text-[10px] px-2.5 py-1 rounded-full font-bold uppercase tracking-wider {{ $prestasi->status_verifikasi === 'approved' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300/40' : ($prestasi->status_verifikasi === 'rejected' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300') }}">
                                {{ $prestasi->status_verifikasi === 'approved' ? 'Terverifikasi' : ucfirst($prestasi->status_verifikasi ?? 'Pending') }}
                            </span>

                            @if($prestasi->sertifikat_file_path && file_exists(public_path('storage/' . $prestasi->sertifikat_file_path)))
                            <a href="{{ asset('storage/' . $prestasi->sertifikat_file_path) }}" target="_blank" class="text-xs font-semibold text-primary hover:underline inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Lihat Piagam
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 rounded-xl bg-surface-hover/30 border border-dashed border-border text-center text-xs text-text-secondary">
                    Belum ada riwayat prestasi atau kejuaraan yang tercatat untuk siswa ini.
                </div>
            @endif
        </div>

        <!-- 3.2 Penerima Beasiswa OTA (Orang Tua Asuh) -->
        <div class="card p-6 bg-surface border border-border shadow-xs rounded-2xl space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                        <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        Beasiswa Orang Tua Asuh (OTA)
                    </h3>
                    <p class="text-xs text-text-secondary">Informasi program beasiswa internal yayasan &amp; donatur orang tua asuh.</p>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $siswa->otaPenerimas->count() > 0 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-surface-hover text-text-secondary' }} border border-border">
                    {{ $siswa->otaPenerimas->count() > 0 ? 'Penerima Aktif' : 'Bukan Penerima' }}
                </span>
            </div>

            @if($siswa->otaPenerimas->isNotEmpty())
                <div class="space-y-4">
                    @foreach($siswa->otaPenerimas as $ota)
                    <div class="p-4 rounded-xl bg-surface-hover/40 border border-border space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h4 class="font-bold text-text-main text-base">Kelompok / Donatur: {{ $ota->kelompok?->nama ?? 'Donatur Umum' }}</h4>
                                <div class="text-xs text-text-secondary mt-0.5">
                                    Periode: <strong>{{ $ota->tanggal_mulai ? $ota->tanggal_mulai->format('d M Y') : '-' }}</strong> s.d. <strong>{{ $ota->tanggal_selesai ? $ota->tanggal_selesai->format('d M Y') : 'Seterusnya' }}</strong>
                                </div>
                                @if($ota->keterangan)
                                <div class="text-[11px] text-text-secondary italic mt-1">{{ $ota->keterangan }}</div>
                                @endif
                            </div>
                            <span class="text-[10px] px-2.5 py-0.5 rounded-full font-bold uppercase {{ $ota->status === 'aktif' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                {{ $ota->status }}
                            </span>
                        </div>

                        <!-- Komponen Bantuan yang Dibiayai -->
                        @if($ota->komponenBiayas->isNotEmpty())
                        <div class="pt-2 border-t border-border">
                            <span class="text-xs font-bold text-text-secondary block mb-1.5">Rincian Komponen Bantuan:</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 text-xs">
                                @foreach($ota->komponenBiayas as $biaya)
                                <div class="p-2 rounded-lg bg-surface border border-border flex items-center justify-between">
                                    <span class="text-text-secondary">{{ $biaya->nama_komponen }}</span>
                                    <strong class="text-text-main">Rp {{ number_format($biaya->nominal ?? 0, 0, ',', '.') }}</strong>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 rounded-xl bg-surface-hover/30 border border-dashed border-border text-center text-xs text-text-secondary">
                    Siswa saat ini tidak tercatat dalam program penerima beasiswa Orang Tua Asuh (OTA).
                </div>
            @endif
        </div>

        <!-- 3.3 Bantuan Sosial & Afirmasi Pemerintah -->
        <div class="card p-6 bg-surface border border-border shadow-xs rounded-2xl space-y-4">
            <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                <span class="p-1.5 rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </span>
                Bantuan Afirmasi Pemerintah (KIP, PIP, KPS/KKS)
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                <!-- KIP -->
                <div class="p-4 rounded-xl bg-surface-hover/50 border border-border space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-text-main">Kartu Indonesia Pintar (KIP)</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ $siswa->penerima_kip ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                            {{ $siswa->penerima_kip ? 'Penerima KIP' : 'Bukan Penerima' }}
                        </span>
                    </div>
                    <div class="text-text-secondary pt-1 space-y-0.5">
                        <div>Nomor KIP: <strong class="font-mono text-text-main">{{ $siswa->nomor_kip ?? '-' }}</strong></div>
                        <div>Nama di KIP: <strong class="text-text-main">{{ $siswa->nama_di_kip ?? '-' }}</strong></div>
                    </div>
                </div>

                <!-- KPS / KKS -->
                <div class="p-4 rounded-xl bg-surface-hover/50 border border-border space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-text-main">KPS / KKS</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ $siswa->penerima_kps ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                            {{ $siswa->penerima_kps ? 'Penerima KPS' : 'Bukan Penerima' }}
                        </span>
                    </div>
                    <div class="text-text-secondary pt-1 space-y-0.5">
                        <div>Nomor KPS: <strong class="font-mono text-text-main">{{ $siswa->no_kps ?? '-' }}</strong></div>
                        <div>Nomor KKS: <strong class="font-mono text-text-main">{{ $siswa->nomor_kks ?? '-' }}</strong></div>
                    </div>
                </div>

                <!-- Layak PIP -->
                <div class="p-4 rounded-xl bg-surface-hover/50 border border-border space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-text-main">Usulan PIP</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ $siswa->layak_pip ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                            {{ $siswa->layak_pip ? 'Layak PIP' : 'Tidak' }}
                        </span>
                    </div>
                    <div class="text-text-secondary pt-1 space-y-0.5">
                        <div>Alasan Kelayakan: <strong class="text-text-main">{{ $siswa->alasan_layak_pip ?? '-' }}</strong></div>
                    </div>
                </div>

                <!-- Rekening Bank Bantuan -->
                <div class="p-4 rounded-xl bg-surface-hover/50 border border-border space-y-2 sm:col-span-2 lg:col-span-3">
                    <span class="font-bold text-text-main block">Rekening Bank Penyaluran Bantuan</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        <div>Bank: <strong class="text-text-main">{{ $siswa->bank ?? '-' }}</strong></div>
                        <div>No. Rekening: <strong class="font-mono text-text-main">{{ $siswa->nomor_rekening_bank ?? '-' }}</strong></div>
                        <div>Atas Nama: <strong class="text-text-main">{{ $siswa->rekening_atas_nama ?? '-' }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 4: RIWAYAT & PRESENSI                                           -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    @if($activeTab === 'riwayat')
    <div class="space-y-6">
        <!-- 4.1 Statistik Presensi Harian -->
        <div class="card p-6 bg-surface border border-border shadow-xs rounded-2xl space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                        <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        Statistik Kehadiran Harian Siswa
                    </h3>
                    <p class="text-xs text-text-secondary">Rekapitulasi presensi harian di sekolah.</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-text-secondary block">Tingkat Kehadiran:</span>
                    <span class="text-xl font-black {{ $persenHadir >= 90 ? 'text-emerald-600 dark:text-emerald-400' : ($persenHadir >= 75 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400') }}">
                        {{ $persenHadir }}%
                    </span>
                </div>
            </div>

            <!-- Stats 4 Pillars -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900">
                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-300 block">Hadir (H)</span>
                    <span class="text-2xl font-black text-emerald-700 dark:text-emerald-300">{{ $absensiStats['H'] }}</span>
                    <span class="text-[10px] text-emerald-600/80 block mt-0.5">Hari</span>
                </div>
                <div class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900">
                    <span class="text-xs font-bold text-amber-700 dark:text-amber-300 block">Sakit (S)</span>
                    <span class="text-2xl font-black text-amber-700 dark:text-amber-300">{{ $absensiStats['S'] }}</span>
                    <span class="text-[10px] text-amber-600/80 block mt-0.5">Hari</span>
                </div>
                <div class="p-3.5 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900">
                    <span class="text-xs font-bold text-blue-700 dark:text-blue-300 block">Izin (I)</span>
                    <span class="text-2xl font-black text-blue-700 dark:text-blue-300">{{ $absensiStats['I'] }}</span>
                    <span class="text-[10px] text-blue-600/80 block mt-0.5">Hari</span>
                </div>
                <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900">
                    <span class="text-xs font-bold text-rose-700 dark:text-rose-300 block">Alpa (A)</span>
                    <span class="text-2xl font-black text-rose-700 dark:text-rose-300">{{ $absensiStats['A'] }}</span>
                    <span class="text-[10px] text-rose-600/80 block mt-0.5">Hari</span>
                </div>
            </div>

            <!-- 10 Riwayat Presensi Terakhir -->
            @if($riwayatAbsensi->isNotEmpty())
            <div class="pt-3 border-t border-border">
                <span class="text-xs font-bold text-text-secondary block mb-2">Riwayat Presensi Harian Terkini:</span>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="border-b border-border text-text-secondary uppercase text-[10px]">
                                <th class="py-2 px-3">Tanggal</th>
                                <th class="py-2 px-3">Status</th>
                                <th class="py-2 px-3">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($riwayatAbsensi as $abs)
                            <tr class="hover:bg-surface-hover/50">
                                <td class="py-2 px-3 font-mono">{{ \Carbon\Carbon::parse($abs->tanggal)->translatedFormat('d F Y') }}</td>
                                <td class="py-2 px-3">
                                    <span class="px-2 py-0.5 rounded font-bold uppercase text-[10px] {{ $abs->status === 'H' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : ($abs->status === 'S' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : ($abs->status === 'I' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300')) }}">
                                        {{ $abs->status === 'H' ? 'Hadir' : ($abs->status === 'S' ? 'Sakit' : ($abs->status === 'I' ? 'Izin' : 'Alpa')) }}
                                    </span>
                                </td>
                                <td class="py-2 px-3 text-text-secondary">{{ $abs->keterangan ?: '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- 4.2 Riwayat Rombel & Rayon -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Riwayat Rombel -->
            <div class="card p-6 bg-surface border border-border shadow-xs rounded-2xl space-y-4">
                <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </span>
                    Riwayat Perjalanan Kelas (Rombel)
                </h3>
                @if($siswa->riwayatRombel->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($siswa->riwayatRombel as $rRombel)
                        <div class="p-3 rounded-xl bg-surface-hover border border-border flex items-center justify-between text-xs">
                            <div>
                                <strong class="text-text-main text-sm block">{{ $rRombel->rombel?->nama ?? '-' }}</strong>
                                <span class="text-text-secondary text-[11px]">Tingkat {{ $rRombel->rombel?->tingkat ?? '-' }} &bull; {{ $rRombel->tahunAjaran?->tahun ?? 'Tahun Berjalan' }}</span>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300 border border-blue-200 dark:border-blue-900">
                                {{ $rRombel->rombel_id === $siswa->rombel_id ? 'Kelas Aktif' : 'Riwayat' }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-surface-hover/40 border border-border text-xs text-text-secondary">
                        Rombel saat ini: <strong class="text-text-main">{{ $siswa->rombel?->nama ?? 'Belum ditentukan' }}</strong>
                    </div>
                @endif
            </div>

            <!-- Riwayat Rayon -->
            <div class="card p-6 bg-surface border border-border shadow-xs rounded-2xl space-y-4">
                <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </span>
                    Riwayat Pembimbing Rayon
                </h3>
                @if($siswa->riwayatRayon->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($siswa->riwayatRayon as $rRayon)
                        <div class="p-3 rounded-xl bg-surface-hover border border-border flex items-center justify-between text-xs">
                            <div>
                                <strong class="text-text-main text-sm block">{{ $rRayon->rayon?->nama ?? '-' }}</strong>
                                <span class="text-text-secondary text-[11px]">Pembimbing: {{ $rRayon->rayon?->pembimbing?->name ?? '-' }}</span>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900">
                                {{ $rRayon->rayon_id === $siswa->rayon_id ? 'Rayon Aktif' : 'Riwayat' }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-surface-hover/40 border border-border text-xs text-text-secondary">
                        Rayon saat ini: <strong class="text-text-main">{{ $siswa->rayon?->nama ?? 'Belum ditentukan' }}</strong>
                        @if($siswa->rayon?->pembimbing)
                        <span class="block text-[11px] mt-0.5">Pembimbing: {{ $siswa->rayon->pembimbing->name }}</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- 4.3 Riwayat Keputusan Akademik -->
        <div class="card p-6 bg-surface border border-border shadow-xs rounded-2xl space-y-4">
            <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                <span class="p-1.5 rounded-lg bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </span>
                Riwayat Keputusan Akademik (Kenaikan / Kelulusan / Mutasi)
            </h3>
            @if($siswa->riwayatAkademiks->isNotEmpty())
                <div class="space-y-3">
                    @foreach($siswa->riwayatAkademiks as $rAkademik)
                    <div class="p-4 rounded-xl bg-surface-hover/40 border border-border space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm text-text-main uppercase tracking-wide">
                                {{ $rAkademik->label_keputusan }}
                            </span>
                            <span class="text-[11px] font-mono text-text-secondary">
                                {{ $rAkademik->tahunAjaran?->tahun ?? '-' }}
                            </span>
                        </div>
                        <div class="text-text-secondary">
                            Perubahan: <strong>{{ $rAkademik->rombelAsal?->nama ?? ('Tingkat ' . $rAkademik->tingkat_asal) }}</strong> 
                            &rarr; <strong>{{ $rAkademik->rombelTujuan?->nama ?? ('Tingkat ' . $rAkademik->tingkat_tujuan) }}</strong>
                        </div>
                        @if($rAkademik->catatan)
                        <div class="text-[11px] text-text-secondary italic">
                            Catatan: {{ $rAkademik->catatan }}
                        </div>
                        @endif
                        <div class="text-[10px] text-text-secondary/70 pt-1 border-t border-border flex items-center justify-between">
                            <span>Diproses oleh: {{ $rAkademik->diprosesOleh?->name ?? 'Sistem' }}</span>
                            <span>{{ $rAkademik->created_at ? $rAkademik->created_at->translatedFormat('d M Y H:i') : '' }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 rounded-xl bg-surface-hover/30 border border-dashed border-border text-center text-xs text-text-secondary">
                    Belum ada riwayat keputusan kenaikan kelas atau kelulusan resmi yang tercatat.
                </div>
            @endif
        </div>
    </div>
    @endif
</div>
