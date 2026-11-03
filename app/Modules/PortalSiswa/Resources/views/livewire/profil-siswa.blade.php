<div class="space-y-6">
    <!-- Header Profil Siswa -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-surface p-6 rounded-2xl border border-border shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                <span>👤 Portal Siswa</span>
                <span>&bull;</span>
                <span>Data Induk Lengkap</span>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">Profil &amp; Identitas Siswa</h1>
            <p class="text-xs text-text-muted mt-1">
                Seluruh data induk kependudukan, fisik, kontak, domisili, orang tua/wali, serta riwayat penempatan akademik Anda yang terdaftar di sistem.
            </p>
        </div>

        @if ($siswa)
            <div class="flex flex-wrap items-center gap-2">
                <!-- Dropdown Cetak Dokumen Buku Induk -->
                <div class="inline-flex rounded-xl shadow-2xs border border-border bg-surface-raised p-1" x-data="{ open: false }">
                    <button type="button" wire:click="downloadDokumen('pdf')" class="px-3 py-1.5 rounded-lg bg-surface hover:bg-surface-hover text-text-main text-xs font-bold transition-all flex items-center gap-1.5" wire:loading.attr="disabled">
                        <span>📥</span>
                        <span wire:loading.remove wire:target="downloadDokumen('pdf')">Buku Induk PDF</span>
                        <span wire:loading wire:target="downloadDokumen('pdf')">Mengunduh...</span>
                    </button>
                    <button type="button" wire:click="downloadDokumen('word')" class="px-2.5 py-1.5 rounded-lg hover:bg-surface-hover text-blue-600 text-xs font-bold transition-all" title="Unduh format Word (.docx)" wire:loading.attr="disabled">
                        <span>DOCX</span>
                    </button>
                    <button type="button" wire:click="downloadDokumen('excel')" class="px-2.5 py-1.5 rounded-lg hover:bg-surface-hover text-emerald-600 text-xs font-bold transition-all" title="Unduh format Excel (.xlsx)" wire:loading.attr="disabled">
                        <span>XLSX</span>
                    </button>
                </div>

                <button type="button" wire:click="openPengajuanModal" class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition-all flex items-center gap-2">
                    <span>✍️</span>
                    <span>Ajukan Perbaikan Data</span>
                </button>
            </div>
        @endif
    </div>

    @if (session('success_pengajuan'))
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-xs font-medium text-emerald-800 dark:text-emerald-300 flex items-center gap-2 shadow-2xs">
            <span>✅</span>
            <span>{{ session('success_pengajuan') }}</span>
        </div>
    @endif
    @if (session('success_password'))
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-xs font-medium text-emerald-800 dark:text-emerald-300 flex items-center gap-2 shadow-2xs">
            <span>🔐</span>
            <span>{{ session('success_password') }}</span>
        </div>
    @endif

    <!-- Hero Card Profil Ringkas -->
    <div class="relative overflow-hidden rounded-2xl border border-primary/20 bg-linear-to-br from-primary/10 via-surface to-surface p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-4 sm:gap-5">
                <div class="relative h-20 w-20 shrink-0 overflow-hidden rounded-2xl border-2 border-primary/30 bg-primary-soft shadow-inner flex items-center justify-center">
                    @if ($siswa && $siswa->foto)
                        <img src="{{ asset('storage/' . $siswa->foto) }}" alt="{{ $siswa->nama }}" class="h-full w-full object-cover">
                    @else
                        <span class="text-3xl font-black text-primary">{{ strtoupper(substr($siswa?->nama ?? auth()->user()->name, 0, 2)) }}</span>
                    @endif
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary text-white tracking-wide uppercase">
                            Siswa
                        </span>
                        @if ($siswa?->program)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                {{ $siswa->program->nama }}
                            </span>
                        @endif
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-surface-raised border border-border text-text-secondary">
                            {{ ucfirst($siswa?->status_siswa ?? 'Aktif') }}
                        </span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-text-main tracking-tight mt-1">
                        {{ $siswa?->nama ?? auth()->user()->name }}
                    </h2>
                    <div class="flex flex-wrap items-center gap-y-1 gap-x-3 text-xs text-text-muted mt-1">
                        <span>NISN: <strong class="text-text-secondary font-mono">{{ $siswa?->nisn ?: '-' }}</strong></span>
                        <span>&bull;</span>
                        <span>NIPD: <strong class="text-text-secondary font-mono">{{ $siswa?->nipd ?: '-' }}</strong></span>
                        <span>&bull;</span>
                        <span>NIK: <strong class="text-text-secondary font-mono">{{ $siswa?->nik ?: '-' }}</strong></span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:items-end gap-1.5 text-xs">
                <div class="flex items-center gap-1.5 text-text-secondary">
                    <span class="text-text-muted">Rombel Saat Ini:</span>
                    <strong class="text-text-main font-bold">{{ $siswa?->rombel?->nama ?: 'Belum diplotting' }}</strong>
                </div>
                @if ($siswa?->rombel?->waliKelas)
                    <div class="flex items-center gap-1.5 text-text-secondary">
                        <span class="text-text-muted">Wali Kelas:</span>
                        <span class="font-bold text-text-main">{{ $siswa->rombel->waliKelas->nama_lengkap }}</span>
                    </div>
                @endif
                @if ($siswa?->rayon?->pembimbing)
                    <div class="flex items-center gap-1.5 text-text-secondary">
                        <span class="text-text-muted">Rayon {{ $siswa->rayon->nama }}:</span>
                        <span class="font-bold text-text-main">{{ $siswa->rayon->pembimbing->nama_lengkap }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Section Navigation Tabs -->
    <div class="border-b border-border bg-surface p-2 rounded-2xl border shadow-xs">
        <nav class="flex flex-wrap items-center gap-1.5">
            <button type="button" wire:click="$set('activeSection', 'biodata')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $activeSection === 'biodata' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                <span>📋 Data Pribadi, Fisik &amp; Kontak</span>
            </button>
            <button type="button" wire:click="$set('activeSection', 'domisili')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $activeSection === 'domisili' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                <span>📍 Alamat &amp; Domisili Lengkap</span>
            </button>
            <button type="button" wire:click="$set('activeSection', 'orang_tua')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $activeSection === 'orang_tua' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                <span>👨‍👩‍👧 Orang Tua &amp; Wali</span>
            </button>
            <button type="button" wire:click="$set('activeSection', 'riwayat')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $activeSection === 'riwayat' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                <span>📜 Riwayat Rombel &amp; Rayon</span>
            </button>
            <button type="button" wire:click="$set('activeSection', 'pengajuan')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $activeSection === 'pengajuan' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                <span>📬 Riwayat Pengajuan Perbaikan</span>
                @if ($riwayatPengajuan->isNotEmpty())
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeSection === 'pengajuan' ? 'bg-white/20 text-white' : 'bg-surface-raised text-text-muted border border-border' }}">
                        {{ $riwayatPengajuan->count() }}
                    </span>
                @endif
            </button>
            <button type="button" wire:click="$set('activeSection', 'keamanan')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $activeSection === 'keamanan' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                <span>🔒 Ganti Kata Sandi</span>
            </button>
        </nav>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 1: DATA PRIBADI, FISIK, KONTAK & PENDIDIKAN ASAL                -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    @if ($activeSection === 'biodata')
        <div class="space-y-6">
            <!-- 1.1 Identitas Kependudukan Mendalam -->
            <div class="card p-6 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-text-secondary flex items-center gap-2 border-b border-border pb-3">
                    <span>🆔 Identitas Diri &amp; Kependudukan</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                    <div class="p-3.5 rounded-xl bg-surface-raised border border-border">
                        <span class="text-text-muted block text-[11px]">Nama Lengkap Siswa</span>
                        <span class="font-bold text-text-main text-sm mt-0.5 block">{{ $siswa?->nama }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-surface-raised border border-border">
                        <span class="text-text-muted block text-[11px]">Tempat, Tanggal Lahir</span>
                        <span class="font-bold text-text-main text-sm mt-0.5 block">
                            {{ $siswa?->tempat_lahir ?: '-' }}, {{ $siswa?->tanggal_lahir ? $siswa->tanggal_lahir->translatedFormat('d F Y') : '-' }}
                        </span>
                        @if ($siswa?->tanggal_lahir)
                            <span class="text-[10px] text-text-muted mt-0.5 block">({{ $siswa->tanggal_lahir->age }} Tahun)</span>
                        @endif
                    </div>
                    <div class="p-3.5 rounded-xl bg-surface-raised border border-border">
                        <span class="text-text-muted block text-[11px]">Jenis Kelamin</span>
                        <span class="font-bold text-text-main text-sm mt-0.5 block">
                            {{ ($siswa?->jk ?? $siswa?->jenis_kelamin) === 'L' ? 'Laki-laki' : ((($siswa?->jk ?? $siswa?->jenis_kelamin) === 'P') ? 'Perempuan' : '-') }}
                        </span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-surface-raised border border-border">
                        <span class="text-text-muted block text-[11px]">Nomor Induk Kependudukan (NIK)</span>
                        <span class="font-mono font-bold text-text-main text-sm mt-0.5 block">{{ $siswa?->nik ?: '-' }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-surface-raised border border-border">
                        <span class="text-text-muted block text-[11px]">No. Kartu Keluarga (KK)</span>
                        <span class="font-mono font-bold text-text-main text-sm mt-0.5 block">{{ $siswa?->no_kk ?: '-' }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-surface-raised border border-border">
                        <span class="text-text-muted block text-[11px]">No. Registrasi Akta Lahir</span>
                        <span class="font-mono font-bold text-text-main text-sm mt-0.5 block">{{ $siswa?->no_akta_lahir ?: '-' }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-surface-raised border border-border">
                        <span class="text-text-muted block text-[11px]">Agama</span>
                        <span class="font-bold text-text-main text-sm mt-0.5 block">{{ $siswa?->agama ?: '-' }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-surface-raised border border-border">
                        <span class="text-text-muted block text-[11px]">Kewarganegaraan</span>
                        <span class="font-bold text-text-main text-sm mt-0.5 block">{{ $siswa?->negara ?: 'Indonesia' }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-surface-raised border border-border">
                        <span class="text-text-muted block text-[11px]">Anak Ke- / Jumlah Saudara</span>
                        <span class="font-bold text-text-main text-sm mt-0.5 block">
                            Anak ke-{{ $siswa?->anak_ke_berapa ?: '-' }} dari {{ $siswa?->jml_saudara_kandung ?: '-' }} bersaudara
                        </span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-surface-raised border border-border sm:col-span-2 lg:col-span-3">
                        <span class="text-text-muted block text-[11px]">Kebutuhan Khusus</span>
                        <span class="font-bold text-text-main text-sm mt-0.5 block">{{ $siswa?->kebutuhan_khusus ?: 'Tidak Ada' }}</span>
                    </div>
                </div>
            </div>

            <!-- 1.2 Data Fisik & Kontak -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Data Fisik / Kesehatan -->
                <div class="card p-6 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-text-secondary flex items-center gap-2 border-b border-border pb-3">
                        <span>🩺 Kondisi Fisik &amp; Kesehatan</span>
                    </h3>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="p-3 rounded-xl bg-surface-raised border border-border">
                            <span class="text-text-muted text-[10px] uppercase font-bold block">Tinggi Badan</span>
                            <span class="text-lg font-black text-text-main font-mono mt-1 block">{{ $siswa?->tinggi_badan ? $siswa->tinggi_badan . ' cm' : '-' }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-surface-raised border border-border">
                            <span class="text-text-muted text-[10px] uppercase font-bold block">Berat Badan</span>
                            <span class="text-lg font-black text-text-main font-mono mt-1 block">{{ $siswa?->berat_badan ? $siswa->berat_badan . ' kg' : '-' }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-surface-raised border border-border">
                            <span class="text-text-muted text-[10px] uppercase font-bold block">Lingkar Kepala</span>
                            <span class="text-lg font-black text-text-main font-mono mt-1 block">{{ $siswa?->lingkar_kepala ? $siswa->lingkar_kepala . ' cm' : '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Kontak Siswa -->
                <div class="card p-6 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-text-secondary flex items-center gap-2 border-b border-border pb-3">
                        <span>📞 Kontak Siswa</span>
                    </h3>
                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-surface-raised border border-border">
                            <span class="text-text-muted">Nomor HP / WhatsApp</span>
                            <span class="font-mono font-bold text-text-main text-sm">{{ $siswa?->hp ?: ($siswa?->telepon ?: '-') }}</span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-surface-raised border border-border">
                            <span class="text-text-muted">Telepon Rumah</span>
                            <span class="font-mono font-bold text-text-main">{{ $siswa?->telepon ?: '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-surface-raised border border-border">
                            <span class="text-text-muted">Alamat Email</span>
                            <span class="font-medium text-text-main">{{ $siswa?->email ?: (auth()->user()->email ?: '-') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1.3 Asal Sekolah & Data Kelulusan SMP -->
            <div class="card p-6 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-text-secondary flex items-center gap-2 border-b border-border pb-3">
                    <span>🏫 Riwayat Pendidikan Sebelumnya (SMP/MTs)</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                    <div class="p-3.5 rounded-xl bg-surface-raised border border-border sm:col-span-2">
                        <span class="text-text-muted block text-[11px]">Nama Asal Sekolah</span>
                        <span class="font-bold text-text-main text-sm mt-0.5 block">{{ $siswa?->sekolah_asal ?: '-' }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-surface-raised border border-border">
                        <span class="text-text-muted block text-[11px]">No. Peserta UN SMP</span>
                        <span class="font-mono font-bold text-text-main mt-0.5 block">{{ $siswa?->no_peserta_un ?: '-' }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-surface-raised border border-border">
                        <span class="text-text-muted block text-[11px]">No. Seri Ijazah &amp; SKHUN</span>
                        <span class="font-mono font-bold text-text-main mt-0.5 block">
                            {{ $siswa?->no_seri_ijazah ?: '-' }} / {{ $siswa?->skhun ?: '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 2: ALAMAT & DOMISILI TEMPAT TINGGAL LENGKAP                     -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    @if ($activeSection === 'domisili')
        <div class="space-y-6">
            <div class="card p-6 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-text-secondary flex items-center gap-2 border-b border-border pb-3">
                    <span>📍 Domisili &amp; Wilayah Tempat Tinggal</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 rounded-xl bg-surface-raised border border-border space-y-3 text-xs">
                        <span class="font-bold text-text-main text-sm block">Alamat Rumah Lengkap</span>
                        <p class="text-text-secondary text-sm leading-relaxed">
                            {{ $siswa?->alamat ?: 'Belum ada data alamat' }}
                        </p>
                        <div class="pt-2 border-t border-border grid grid-cols-2 gap-2 text-xs">
                            <div>RT / RW: <strong class="text-text-main font-mono">{{ $siswa?->rt ?: '-' }} / {{ $siswa?->rw ?: '-' }}</strong></div>
                            <div>Dusun / Kampung: <strong class="text-text-main">{{ $siswa?->dusun ?: '-' }}</strong></div>
                            <div>Kelurahan / Desa: <strong class="text-text-main">{{ $siswa?->kelurahan ?: '-' }}</strong></div>
                            <div>Kecamatan: <strong class="text-text-main">{{ $siswa?->kecamatan ?: '-' }}</strong></div>
                            <div>Kabupaten / Kota: <strong class="text-text-main">{{ $siswa?->kabupaten_kota ?: '-' }}</strong></div>
                            <div>Provinsi: <strong class="text-text-main">{{ $siswa?->provinsi ?: '-' }}</strong></div>
                            <div>Kode Pos: <strong class="text-text-main font-mono">{{ $siswa?->kode_pos ?: '-' }}</strong></div>
                            <div>Negara: <strong class="text-text-main">{{ $siswa?->negara ?: 'Indonesia' }}</strong></div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-surface-raised border border-border space-y-3 text-xs">
                        <span class="font-bold text-text-main text-sm block">Aksesibilitas &amp; Lokasi Geografis</span>
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div class="p-3 rounded-lg bg-surface border border-border">
                                <span class="text-text-muted text-[11px] block">Jenis Tempat Tinggal</span>
                                <span class="font-bold text-text-main mt-0.5 block">{{ $siswa?->jenis_tinggal ?: 'Bersama Orang Tua' }}</span>
                            </div>
                            <div class="p-3 rounded-lg bg-surface border border-border">
                                <span class="text-text-muted text-[11px] block">Alat Transportasi</span>
                                <span class="font-bold text-text-main mt-0.5 block">{{ $siswa?->alat_transportasi ?: '-' }}</span>
                            </div>
                            <div class="p-3 rounded-lg bg-surface border border-border">
                                <span class="text-text-muted text-[11px] block">Jarak ke Sekolah</span>
                                <span class="font-bold text-text-main mt-0.5 block">{{ $siswa?->jarak_ke_sekolah_km ? $siswa->jarak_ke_sekolah_km . ' KM' : '-' }}</span>
                            </div>
                            <div class="p-3 rounded-lg bg-surface border border-border">
                                <span class="text-text-muted text-[11px] block">Koordinat Geografis</span>
                                <span class="font-mono text-text-main text-[11px] mt-0.5 block">
                                    {{ $siswa?->lintang && $siswa?->bujur ? $siswa->lintang . ', ' . $siswa->bujur : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 3: DATA ORANG TUA & WALI LENGKAP                                -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    @if ($activeSection === 'orang_tua')
        @php
            $ayah = $siswa?->orangTuas?->firstWhere('hubungan', 'ayah') ?? $siswa?->orangTuas?->firstWhere('hubungan', 'Ayah');
            $ibu = $siswa?->orangTuas?->firstWhere('hubungan', 'ibu') ?? $siswa?->orangTuas?->firstWhere('hubungan', 'Ibu');
            $wali = $siswa?->orangTuas?->firstWhere('hubungan', 'wali') ?? $siswa?->orangTuas?->firstWhere('hubungan', 'Wali');
        @endphp
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Ayah Kandung -->
                <div class="card p-6 bg-surface border border-blue-500/20 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-border pb-3">
                        <span class="font-bold text-blue-600 text-sm flex items-center gap-1.5">
                            <span>👨</span> Ayah Kandung
                        </span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-emerald-500/10 text-emerald-600">
                            Hidup
                        </span>
                    </div>
                    <div class="text-base font-bold text-text-main">{{ $ayah?->nama ?: '-' }}</div>
                    <div class="space-y-2 text-xs text-text-secondary pt-1">
                        <div>NIK: <strong class="font-mono text-text-main">{{ $ayah?->nik ?: '-' }}</strong></div>
                        <div>Tahun Lahir: <strong class="text-text-main">{{ $ayah?->tahun_lahir ?: '-' }}</strong></div>
                        <div>Pendidikan: <strong class="text-text-main">{{ $ayah?->jenjang_pendidikan ?: ($ayah?->pendidikan ?: '-') }}</strong></div>
                        <div>Pekerjaan: <strong class="text-text-main">{{ $ayah?->pekerjaan ?: '-' }}</strong></div>
                        <div>Penghasilan: <strong class="text-text-main">{{ $ayah?->penghasilan ?: '-' }}</strong></div>
                        <div>No. Kontak: <strong class="text-text-main font-mono">{{ $ayah?->no_hp ?: '-' }}</strong></div>
                    </div>
                </div>

                <!-- Ibu Kandung -->
                <div class="card p-6 bg-surface border border-pink-500/20 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-border pb-3">
                        <span class="font-bold text-pink-600 text-sm flex items-center gap-1.5">
                            <span>👩</span> Ibu Kandung
                        </span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-emerald-500/10 text-emerald-600">
                            Hidup
                        </span>
                    </div>
                    <div class="text-base font-bold text-text-main">{{ $ibu?->nama ?: '-' }}</div>
                    <div class="space-y-2 text-xs text-text-secondary pt-1">
                        <div>NIK: <strong class="font-mono text-text-main">{{ $ibu?->nik ?: '-' }}</strong></div>
                        <div>Tahun Lahir: <strong class="text-text-main">{{ $ibu?->tahun_lahir ?: '-' }}</strong></div>
                        <div>Pendidikan: <strong class="text-text-main">{{ $ibu?->jenjang_pendidikan ?: ($ibu?->pendidikan ?: '-') }}</strong></div>
                        <div>Pekerjaan: <strong class="text-text-main">{{ $ibu?->pekerjaan ?: '-' }}</strong></div>
                        <div>Penghasilan: <strong class="text-text-main">{{ $ibu?->penghasilan ?: '-' }}</strong></div>
                        <div>No. Kontak: <strong class="text-text-main font-mono">{{ $ibu?->no_hp ?: '-' }}</strong></div>
                    </div>
                </div>

                <!-- Wali Siswa -->
                <div class="card p-6 bg-surface border border-border shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-border pb-3">
                        <span class="font-bold text-text-main text-sm flex items-center gap-1.5">
                            <span>🤝</span> Wali Siswa (Jika Ada)
                        </span>
                    </div>
                    <div class="text-base font-bold text-text-main">{{ $wali?->nama ?: '-' }}</div>
                    <div class="space-y-2 text-xs text-text-secondary pt-1">
                        <div>NIK: <strong class="font-mono text-text-main">{{ $wali?->nik ?: '-' }}</strong></div>
                        <div>Tahun Lahir: <strong class="text-text-main">{{ $wali?->tahun_lahir ?: '-' }}</strong></div>
                        <div>Pendidikan: <strong class="text-text-main">{{ $wali?->jenjang_pendidikan ?: ($wali?->pendidikan ?: '-') }}</strong></div>
                        <div>Pekerjaan: <strong class="text-text-main">{{ $wali?->pekerjaan ?: '-' }}</strong></div>
                        <div>Penghasilan: <strong class="text-text-main">{{ $wali?->penghasilan ?: '-' }}</strong></div>
                        <div>No. Kontak: <strong class="text-text-main font-mono">{{ $wali?->no_hp ?: '-' }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 4: RIWAYAT ROMBEL, RAYON & KEPUTUSAN AKADEMIK                   -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    @if ($activeSection === 'riwayat')
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Riwayat Rombel -->
                <div class="card p-6 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-text-secondary flex items-center gap-2 border-b border-border pb-3">
                        <span>🏛️ Riwayat Rombel (Kelas)</span>
                    </h3>
                    @if (!$siswa?->riwayatRombel || $siswa->riwayatRombel->isEmpty())
                        <div class="p-6 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                            Belum ada riwayat perubahan rombel.
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($siswa->riwayatRombel as $rr)
                                <div class="p-3 rounded-xl bg-surface-raised border border-border flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-bold text-text-main">{{ $rr->rombel?->nama ?? '-' }}</span>
                                        <span class="text-[10px] text-text-muted block">Tingkat {{ $rr->rombel?->tingkat ?? '-' }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-primary-soft text-primary">
                                        {{ $rr->created_at ? $rr->created_at->format('d M Y') : '-' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Riwayat Rayon -->
                <div class="card p-6 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-text-secondary flex items-center gap-2 border-b border-border pb-3">
                        <span>🧭 Riwayat Rayon &amp; Pembimbing</span>
                    </h3>
                    @if (!$siswa?->riwayatRayon || $siswa->riwayatRayon->isEmpty())
                        <div class="p-6 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                            Belum ada riwayat rayon sebelumnya.
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($siswa->riwayatRayon as $ry)
                                <div class="p-3 rounded-xl bg-surface-raised border border-border flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-bold text-text-main">{{ $ry->rayon?->nama ?? '-' }}</span>
                                        <span class="text-[10px] text-text-muted block">Pembimbing: {{ $ry->rayon?->pembimbing?->nama_lengkap ?? '-' }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-surface border border-border text-text-secondary">
                                        {{ $ry->tgl_mulai ? $ry->tgl_mulai->format('d M Y') : '-' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Riwayat Keputusan Akademik -->
            @if ($siswa?->riwayatAkademiks && $siswa->riwayatAkademiks->isNotEmpty())
                <div class="card p-6 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-text-secondary flex items-center gap-2 border-b border-border pb-3">
                        <span>⚖️ Riwayat Keputusan Akademik (Kenaikan Kelas / Mutasi / Kelulusan)</span>
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-surface-raised text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                                <tr>
                                    <th class="p-3">Tanggal Keputusan</th>
                                    <th class="p-3">Jenis Keputusan</th>
                                    <th class="p-3">Tahun Ajaran</th>
                                    <th class="p-3">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach ($siswa->riwayatAkademiks as $ra)
                                    <tr class="hover:bg-surface-hover/50">
                                        <td class="p-3 whitespace-nowrap">{{ $ra->created_at->format('d/m/Y') }}</td>
                                        <td class="p-3">
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-primary-soft text-primary uppercase">
                                                {{ $ra->jenis_keputusan }}
                                            </span>
                                        </td>
                                        <td class="p-3">{{ $ra->tahunAjaran?->nama ?? '-' }}</td>
                                        <td class="p-3 text-text-secondary">{{ $ra->catatan ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 5: RIWAYAT PENGAJUAN PERBAIKAN DATA                             -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    @if ($activeSection === 'pengajuan')
        <div class="card p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-border pb-4">
                <div>
                    <h3 class="text-sm font-bold text-text-main">Riwayat Permohonan Koreksi Data</h3>
                    <p class="text-xs text-text-muted">Daftar pengajuan perbaikan identitas diri yang pernah Anda kirimkan.</p>
                </div>
                <button type="button" wire:click="openPengajuanModal" class="px-3 py-1.5 rounded-lg bg-primary hover:bg-primary-hover text-white text-xs font-bold transition-all flex items-center gap-1.5">
                    <span>➕ Ajukan Baru</span>
                </button>
            </div>

            @if ($riwayatPengajuan->isEmpty())
                <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                    Belum ada pengajuan perbaikan data yang dikirimkan.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-surface-raised text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                            <tr>
                                <th class="p-3">Tanggal</th>
                                <th class="p-3">Kategori &amp; Rincian</th>
                                <th class="p-3">Nilai Lama</th>
                                <th class="p-3">Usulan Baru</th>
                                <th class="p-3">Alasan</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Catatan Admin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($riwayatPengajuan as $p)
                                @php
                                    $firstChange = collect($p->rincian_perubahan)->first() ?? [];
                                    $fieldName = $firstChange['field'] ?? array_key_first($p->rincian_perubahan ?? []);
                                    $lama = $firstChange['lama'] ?? '-';
                                    $baru = $firstChange['baru'] ?? '-';
                                @endphp
                                <tr class="hover:bg-surface-hover/50 transition-colors">
                                    <td class="p-3 whitespace-nowrap text-text-secondary">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="p-3">
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-semibold bg-surface-raised text-text-secondary border border-border uppercase">
                                            {{ $p->kategori }}
                                        </span>
                                        <div class="font-bold text-text-main mt-0.5">{{ ucwords(str_replace('_', ' ', $fieldName)) }}</div>
                                    </td>
                                    <td class="p-3 text-rose-500 font-mono line-through">{{ $lama }}</td>
                                    <td class="p-3 text-emerald-600 font-mono font-bold">{{ $baru }}</td>
                                    <td class="p-3 max-w-[200px]">
                                        <div class="truncate text-text-secondary" title="{{ $p->alasan }}">{{ $p->alasan }}</div>
                                        @if ($p->lampiran)
                                            <a href="{{ asset('storage/' . $p->lampiran) }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] text-primary font-bold hover:underline mt-0.5">
                                                <span>📎 Lampiran</span>
                                            </a>
                                        @endif
                                    </td>
                                    <td class="p-3 whitespace-nowrap">
                                        @if ($p->status === 'menunggu')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20">
                                                ⏳ Menunggu
                                            </span>
                                        @elseif ($p->status === 'disetujui')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                                ✅ Disetujui
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600 border border-rose-500/20">
                                                ❌ Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-text-muted">
                                        {{ $p->catatan_admin ?: '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 6: GANTI KATA SANDI                                             -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    @if ($activeSection === 'keamanan')
        <div class="card p-6 max-w-xl mx-auto space-y-4">
            <h3 class="text-sm font-bold text-text-main border-b border-border pb-3 flex items-center gap-2">
                <span>🔐 Perbarui Kata Sandi Akun</span>
            </h3>
            <p class="text-xs text-text-muted">
                Pastikan menggunakan kata sandi yang aman dan tidak dibagikan kepada siapapun.
            </p>

            <form wire:submit.prevent="updatePassword" class="space-y-4 text-xs">
                <div>
                    <label class="block font-medium text-text-secondary mb-1">Kata Sandi Saat Ini</label>
                    <input type="password" wire:model="current_password" class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden">
                    @error('current_password') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium text-text-secondary mb-1">Kata Sandi Baru (Min. 8 karakter &amp; angka)</label>
                    <input type="password" wire:model="new_password" class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden">
                    @error('new_password') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium text-text-secondary mb-1">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" wire:model="new_password_confirmation" class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden">
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition-all flex items-center gap-2" wire:loading.attr="disabled">
                        <span wire:loading.remove>Simpan Password Baru</span>
                        <span wire:loading>Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Modal Form Ajukan Perbaikan Data Komprehensif -->
    @if ($showPengajuanModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="bg-surface rounded-2xl border border-border shadow-xl max-w-lg w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-sm font-bold text-text-main flex items-center gap-2">
                        <span>✍️ Formulir Pengajuan Perbaikan Data</span>
                    </h3>
                    <button type="button" wire:click="closePengajuanModal" class="text-text-muted hover:text-text-main text-lg font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="submitPengajuan" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-medium text-text-secondary mb-1">Kategori Perubahan</label>
                        <select wire:model.live="kategori" class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden">
                            <option value="biodata">Identitas Pribadi (Nama, NIK, No. KK, TTL, Akta, dll)</option>
                            <option value="domisili">Alamat &amp; Domisili (Jalan, RT/RW, Desa, Kecamatan, Kab/Kota)</option>
                            <option value="kontak">Kontak Siswa (No. HP, Telepon, Email)</option>
                            <option value="fisik">Kondisi Fisik (Tinggi, Berat Badan, Lingkar Kepala)</option>
                            <option value="pendidikan">Asal Sekolah &amp; Ijazah SMP</option>
                            <option value="orang_tua">Data Orang Tua / Wali (Nama, NIK, Pekerjaan, Gaji)</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-text-secondary mb-1">Bagian Data yang Diperbaiki</label>
                        <select wire:model="namaField" class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden">
                            @if ($kategori === 'biodata')
                                <option value="nama">Nama Lengkap Siswa</option>
                                <option value="nik">Nomor Induk Kependudukan (NIK)</option>
                                <option value="no_kk">Nomor Kartu Keluarga (KK)</option>
                                <option value="no_akta_lahir">Nomor Registrasi Akta Lahir</option>
                                <option value="tempat_lahir">Tempat Lahir</option>
                                <option value="tanggal_lahir">Tanggal Lahir (Format: YYYY-MM-DD)</option>
                                <option value="jk">Jenis Kelamin (L / P)</option>
                                <option value="agama">Agama</option>
                                <option value="anak_ke_berapa">Anak Ke-berapa</option>
                                <option value="jml_saudara_kandung">Jumlah Saudara Kandung</option>
                                <option value="kebutuhan_khusus">Kebutuhan Khusus</option>
                            @elseif ($kategori === 'domisili')
                                <option value="alamat">Alamat Jalan / Rumah</option>
                                <option value="rt">RT</option>
                                <option value="rw">RW</option>
                                <option value="dusun">Dusun / Kampung</option>
                                <option value="kelurahan">Kelurahan / Desa</option>
                                <option value="kecamatan">Kecamatan</option>
                                <option value="kabupaten_kota">Kabupaten / Kota</option>
                                <option value="provinsi">Provinsi</option>
                                <option value="kode_pos">Kode Pos</option>
                                <option value="jenis_tinggal">Jenis Tinggal</option>
                                <option value="alat_transportasi">Alat Transportasi</option>
                            @elseif ($kategori === 'kontak')
                                <option value="hp">Nomor HP / WhatsApp</option>
                                <option value="telepon">Telepon Rumah</option>
                                <option value="email">Email</option>
                            @elseif ($kategori === 'fisik')
                                <option value="tinggi_badan">Tinggi Badan (cm)</option>
                                <option value="berat_badan">Berat Badan (kg)</option>
                                <option value="lingkar_kepala">Lingkar Kepala (cm)</option>
                            @elseif ($kategori === 'pendidikan')
                                <option value="sekolah_asal">Nama Asal SMP/MTs</option>
                                <option value="no_peserta_un">No. Peserta UN SMP</option>
                                <option value="no_seri_ijazah">No. Seri Ijazah</option>
                                <option value="skhun">No. SKHUN</option>
                            @elseif ($kategori === 'orang_tua')
                                <option value="nama_ayah">Nama Ayah Kandung</option>
                                <option value="nik_ayah">NIK Ayah Kandung</option>
                                <option value="tahun_lahir_ayah">Tahun Lahir Ayah</option>
                                <option value="pekerjaan_ayah">Pekerjaan Ayah</option>
                                <option value="penghasilan_ayah">Penghasilan Ayah</option>
                                <option value="nama_ibu">Nama Ibu Kandung</option>
                                <option value="nik_ibu">NIK Ibu Kandung</option>
                                <option value="tahun_lahir_ibu">Tahun Lahir Ibu</option>
                                <option value="pekerjaan_ibu">Pekerjaan Ibu</option>
                                <option value="penghasilan_ibu">Penghasilan Ibu</option>
                                <option value="nama_wali">Nama Wali</option>
                                <option value="pekerjaan_wali">Pekerjaan Wali</option>
                            @else
                                <option value="catatan_lainnya">Lainnya</option>
                            @endif
                        </select>
                        @error('namaField') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-text-secondary mb-1">Nilai Usulan yang Benar (Nilai Baru)</label>
                        <input type="text" wire:model="nilaiBaru" placeholder="Masukkan perbaikan data yang benar..." class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden">
                        @error('nilaiBaru') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-text-secondary mb-1">Alasan Permohonan Koreksi</label>
                        <textarea wire:model="alasan" rows="3" placeholder="Contoh: Kesalahan penulisan NIK sesuai dokumen Kartu Keluarga terbaru..." class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden"></textarea>
                        @error('alasan') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-text-secondary mb-1">Upload Berkas Bukti Fisik (Foto KK / Akta / KTP / Ijazah, Maks: 3MB)</label>
                        <input type="file" wire:model="lampiranFile" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-xs text-text-secondary file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-soft file:text-primary hover:file:bg-primary/20">
                        @error('lampiranFile') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="closePengajuanModal" class="px-4 py-2 rounded-xl border border-border hover:bg-surface-hover text-text-secondary font-bold text-xs">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-hover text-white font-bold text-xs shadow-xs" wire:loading.attr="disabled">
                            <span wire:loading.remove>Kirim Permohonan</span>
                            <span wire:loading>Mengirim...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
