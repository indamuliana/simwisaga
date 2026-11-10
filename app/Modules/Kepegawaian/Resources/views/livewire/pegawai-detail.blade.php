<div class="space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-border">
        <div class="flex items-center space-x-3">
            <a href="{{ route('kepegawaian.pegawai') }}" class="p-2 rounded-xl bg-surface border border-border text-text-secondary hover:text-text-main transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <nav class="flex text-xs text-text-secondary gap-1 mb-1">
                    <a href="{{ route('kepegawaian.pegawai') }}" class="hover:text-primary">Kepegawaian</a>
                    <span>/</span>
                    <span class="text-text-main font-medium">Detail Pegawai</span>
                </nav>
                <h1 class="text-2xl font-bold text-text-main tracking-tight">{{ $pegawai->nama_gelar }}</h1>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            @can('kepegawaian.pegawai.edit')
                <a href="{{ route('kepegawaian.pegawai.edit', $pegawai->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-primary rounded-xl hover:bg-primary-hover shadow-xs transition gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    Ubah Data Pegawai
                </a>
            @endcan
        </div>
    </div>

    <!-- Header Profil Card -->
    <div class="bg-surface rounded-2xl border border-border p-6 shadow-xs relative overflow-hidden">
        <div class="flex flex-col md:flex-row gap-6 items-start md:items-center">
            <div class="relative shrink-0">
                <img src="{{ $pegawai->foto_utama_url }}" alt="{{ $pegawai->nama_lengkap }}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover border-2 border-border shadow-md">
                @if($pegawai->is_active)
                    <span class="absolute -bottom-1.5 -right-1.5 px-2 py-0.5 text-[10px] font-bold tracking-wide uppercase bg-emerald-500 text-white rounded-md shadow-xs">Aktif</span>
                @else
                    <span class="absolute -bottom-1.5 -right-1.5 px-2 py-0.5 text-[10px] font-bold tracking-wide uppercase bg-slate-500 text-white rounded-md shadow-xs">Nonaktif</span>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-primary/10 text-primary uppercase">
                        {{ $pegawai->jenis_gtk }}
                    </span>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 uppercase">
                        Status: {{ $pegawai->status_kepegawaian }}
                    </span>
                    @if($pegawai->jabatan_struktural)
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300">
                            {{ $pegawai->jabatan_struktural }}
                        </span>
                    @endif
                </div>

                <h2 class="text-xl sm:text-2xl font-bold text-text-main truncate mb-1">
                    {{ $pegawai->nama_gelar }}
                </h2>

                <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-text-secondary">
                    <div><span class="font-medium text-text-main">NIP:</span> {{ $pegawai->nip ?: '-' }}</div>
                    <div>•</div>
                    <div><span class="font-medium text-text-main">NUPTK:</span> {{ $pegawai->nuptk ?: '-' }}</div>
                    <div>•</div>
                    <div><span class="font-medium text-text-main">NIK:</span> {{ $pegawai->nik ?: '-' }}</div>
                    <div>•</div>
                    <div><span class="font-medium text-text-main">Masa Kerja:</span> {{ $pegawai->masa_kerja_label }}</div>
                </div>
            </div>

            <div class="flex md:flex-col gap-2 shrink-0 w-full md:w-auto pt-4 md:pt-0 border-t md:border-t-0 border-border">
                <div class="p-3 bg-surface-ground rounded-xl text-center flex-1 md:min-w-[130px]">
                    <div class="text-[11px] font-medium text-text-secondary uppercase">Umur</div>
                    <div class="text-base font-bold text-text-main mt-0.5">{{ $pegawai->umur_label }}</div>
                </div>
                <div class="p-3 bg-surface-ground rounded-xl text-center flex-1 md:min-w-[130px]">
                    <div class="text-[11px] font-medium text-text-secondary uppercase">Lama Bekerja</div>
                    <div class="text-base font-bold text-text-main mt-0.5">{{ $pegawai->lama_bekerja_label }}</div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex overflow-x-auto gap-2 border-t border-border mt-6 pt-4 scrollbar-none">
            <button wire:click="setTab('biodata')" class="px-4 py-2 rounded-xl text-xs font-semibold transition whitespace-nowrap {{ $activeTab === 'biodata' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-ground' }}">
                Biodata & Identitas
            </button>
            <button wire:click="setTab('kepegawaian')" class="px-4 py-2 rounded-xl text-xs font-semibold transition whitespace-nowrap {{ $activeTab === 'kepegawaian' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-ground' }}">
                Kepegawaian & SK
            </button>
            <button wire:click="setTab('pajak')" class="px-4 py-2 rounded-xl text-xs font-semibold transition whitespace-nowrap {{ $activeTab === 'pajak' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-ground' }}">
                Pajak & Jaminan (BPJS)
            </button>
            <button wire:click="setTab('pendidikan')" class="px-4 py-2 rounded-xl text-xs font-semibold transition whitespace-nowrap {{ $activeTab === 'pendidikan' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-ground' }}">
                Pendidikan & Pengalaman
            </button>
            <button wire:click="setTab('keluarga')" class="px-4 py-2 rounded-xl text-xs font-semibold transition whitespace-nowrap {{ $activeTab === 'keluarga' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-ground' }}">
                Data Keluarga
            </button>
            <button wire:click="setTab('mapel')" class="px-4 py-2 rounded-xl text-xs font-semibold transition whitespace-nowrap {{ $activeTab === 'mapel' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-ground' }}">
                Mata Pelajaran Diampu
            </button>
            <button wire:click="setTab('foto')" class="px-4 py-2 rounded-xl text-xs font-semibold transition whitespace-nowrap {{ $activeTab === 'foto' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-ground' }}">
                Berkas Foto (5 Slot)
            </button>
        </div>
    </div>

    <!-- Tab 1: Biodata & Identitas -->
    @if($activeTab === 'biodata')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-surface rounded-2xl border border-border p-6 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-text-main uppercase tracking-wider pb-3 border-b border-border flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Identitas Pribadi
                </h3>
                <dl class="grid grid-cols-1 gap-y-3 text-xs">
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">Nama Lengkap</dt>
                        <dd class="font-semibold text-text-main text-right">{{ $pegawai->nama_lengkap }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">Gelar Depan / Belakang</dt>
                        <dd class="font-semibold text-text-main text-right">{{ ($pegawai->gelar_depan ?: '-') . ' / ' . ($pegawai->gelar_belakang ?: '-') }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">Jenis Kelamin</dt>
                        <dd class="font-semibold text-text-main text-right">{{ $pegawai->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">Tempat, Tanggal Lahir</dt>
                        <dd class="font-semibold text-text-main text-right">
                            {{ ($pegawai->tempat_lahir ?: '-') . ', ' . ($pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->isoFormat('D MMMM Y') : '-') }}
                        </dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">Umur</dt>
                        <dd class="font-semibold text-text-main text-right">{{ $pegawai->umur_label }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">Golongan Darah</dt>
                        <dd class="font-semibold text-text-main text-right">{{ $pegawai->golongan_darah ?: '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-1">
                        <dt class="text-text-secondary">NIK (KTP)</dt>
                        <dd class="font-mono font-semibold text-text-main text-right">{{ $pegawai->nik ?: '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-surface rounded-2xl border border-border p-6 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-text-main uppercase tracking-wider pb-3 border-b border-border flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Kontak & Alamat Domisili
                </h3>
                <dl class="grid grid-cols-1 gap-y-3 text-xs">
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">Nomor Telepon / WhatsApp</dt>
                        <dd class="font-semibold text-text-main text-right">{{ $pegawai->no_hp ?: '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">Email Resmi / Pribadi</dt>
                        <dd class="font-semibold text-text-main text-right">{{ $pegawai->email ?: '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">Cakupan Wilayah</dt>
                        <dd class="font-semibold text-text-main text-right uppercase">
                            {{ $pegawai->jenis_wilayah === 'luar_negeri' ? 'Luar Negeri (' . ($pegawai->negara ?: 'Asing') . ')' : 'Dalam Negeri (Indonesia)' }}
                        </dd>
                    </div>
                    @if($pegawai->jenis_wilayah !== 'luar_negeri')
                        <div class="flex justify-between py-1 border-b border-border/50">
                            <dt class="text-text-secondary">Provinsi</dt>
                            <dd class="font-semibold text-text-main text-right">{{ $pegawai->provinsi_nama ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-border/50">
                            <dt class="text-text-secondary">Kabupaten / Kota</dt>
                            <dd class="font-semibold text-text-main text-right">{{ $pegawai->kabupaten_nama ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-border/50">
                            <dt class="text-text-secondary">Kecamatan & Desa</dt>
                            <dd class="font-semibold text-text-main text-right">
                                {{ ($pegawai->kecamatan_nama ?: '-') . ', ' . ($pegawai->desa_nama ?: '-') }}
                            </dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-border/50">
                            <dt class="text-text-secondary">Kode Pos</dt>
                            <dd class="font-semibold text-text-main text-right">{{ $pegawai->kode_pos ?: '-' }}</dd>
                        </div>
                    @endif
                    <div class="py-1">
                        <dt class="text-text-secondary mb-1">Alamat Lengkap / Jalan</dt>
                        <dd class="font-medium text-text-main bg-surface-ground p-2.5 rounded-xl">{{ $pegawai->alamat_lengkap_formatted }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    @endif

    <!-- Tab 2: Kepegawaian & SK -->
    @if($activeTab === 'kepegawaian')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-surface rounded-2xl border border-border p-6 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-text-main uppercase tracking-wider pb-3 border-b border-border flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Status & Penugasan
                </h3>
                <dl class="grid grid-cols-1 gap-y-3 text-xs">
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">Jenis GTK (Jabatan)</dt>
                        <dd class="font-bold text-primary text-right uppercase">{{ $pegawai->jenis_gtk }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">Status Kepegawaian</dt>
                        <dd class="font-bold text-text-main text-right uppercase">{{ $pegawai->status_kepegawaian }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">Jabatan Struktural</dt>
                        <dd class="font-semibold text-text-main text-right">{{ $pegawai->jabatan_struktural ?: '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">NIP</dt>
                        <dd class="font-mono font-semibold text-text-main text-right">{{ $pegawai->nip ?: '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">NUPTK</dt>
                        <dd class="font-mono font-semibold text-text-main text-right">{{ $pegawai->nuptk ?: '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">Tanggal Awal Masuk Wikrama</dt>
                        <dd class="font-semibold text-text-main text-right">
                            {{ $pegawai->tgl_awal_masuk_wikrama ? $pegawai->tgl_awal_masuk_wikrama->isoFormat('D MMMM Y') : '-' }}
                        </dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">Lama Bekerja (Tahun)</dt>
                        <dd class="font-semibold text-text-main text-right">{{ $pegawai->lama_bekerja_label }}</dd>
                    </div>
                    <div class="flex justify-between py-1">
                        <dt class="text-text-secondary">Masa Kerja Lengkap</dt>
                        <dd class="font-bold text-emerald-600 dark:text-emerald-400 text-right">{{ $pegawai->masa_kerja_label }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-surface rounded-2xl border border-border p-6 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-text-main uppercase tracking-wider pb-3 border-b border-border flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Surat Keputusan (SK) & Sertifikasi Pendidik
                </h3>
                <dl class="grid grid-cols-1 gap-y-3 text-xs">
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">Nomor Peserta UKG (SIMPKB-ID)</dt>
                        <dd class="font-mono font-semibold text-text-main text-right">{{ $pegawai->nomor_peserta_ukg ?: '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">Nomor Registrasi Guru (NRG)</dt>
                        <dd class="font-mono font-semibold text-text-main text-right">{{ $pegawai->nomor_registrasi_guru ?: '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">No. Peserta Sertifikasi</dt>
                        <dd class="font-mono font-semibold text-text-main text-right">{{ $pegawai->nomor_peserta_sertifikasi ?: '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">Nomor Sertifikat Pendidik</dt>
                        <dd class="font-mono font-semibold text-text-main text-right">{{ $pegawai->nomor_sertifikat_pendidik ?: '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">TMT Awal Menjadi Guru</dt>
                        <dd class="font-semibold text-text-main text-right">
                            {{ $pegawai->tmt_awal_guru ? $pegawai->tmt_awal_guru->isoFormat('D MMMM Y') : '-' }}
                        </dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">No. SK Mulai Menjadi Guru</dt>
                        <dd class="font-mono font-semibold text-text-main text-right">{{ $pegawai->no_sk_mulai_guru ?: '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <dt class="text-text-secondary">Tgl. Diangkat Pegawai Tetap</dt>
                        <dd class="font-semibold text-text-main text-right">
                            {{ $pegawai->tgl_diangkat_pegawai_tetap ? $pegawai->tgl_diangkat_pegawai_tetap->isoFormat('D MMMM Y') : '-' }}
                        </dd>
                    </div>
                    <div class="flex justify-between py-1">
                        <dt class="text-text-secondary">No. SK Tetap Yayasan</dt>
                        <dd class="font-mono font-semibold text-text-main text-right">{{ $pegawai->no_sk_tetap_yayasan ?: '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    @endif

    <!-- Tab 3: Pajak & BPJS -->
    @if($activeTab === 'pajak')
        <div class="bg-surface rounded-2xl border border-border p-6 shadow-xs space-y-4 max-w-3xl">
            <h3 class="text-sm font-bold text-text-main uppercase tracking-wider pb-3 border-b border-border flex items-center gap-2">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>
                Perpajakan & Jaminan Sosial
            </h3>
            <dl class="grid grid-cols-1 gap-y-3 text-xs">
                <div class="flex justify-between py-2 border-b border-border/50">
                    <dt class="text-text-secondary">Nomor Pokok Wajib Pajak (NPWP)</dt>
                    <dd class="font-mono font-semibold text-text-main text-right">{{ $pegawai->npwp ?: '-' }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-border/50">
                    <dt class="text-text-secondary">Nama Wajib Pajak (sesuai NPWP)</dt>
                    <dd class="font-semibold text-text-main text-right">{{ $pegawai->nama_wajib_pajak ?: '-' }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-border/50">
                    <dt class="text-text-secondary">No. Kartu BPJS Kesehatan</dt>
                    <dd class="font-mono font-semibold text-text-main text-right">{{ $pegawai->no_kartu_bpjs_kesehatan ?: '-' }}</dd>
                </div>
                <div class="flex justify-between py-2">
                    <dt class="text-text-secondary">No. Kartu BPJS Ketenagakerjaan</dt>
                    <dd class="font-mono font-semibold text-text-main text-right">{{ $pegawai->no_kartu_bpjs_ketenagakerjaan ?: '-' }}</dd>
                </div>
            </dl>
        </div>
    @endif

    <!-- Tab 4: Pendidikan & Pengalaman -->
    @if($activeTab === 'pendidikan')
        <div class="space-y-6">
            <!-- Pendidikan Terakhir -->
            <div class="bg-surface rounded-2xl border border-border p-6 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-text-main uppercase tracking-wider pb-3 border-b border-border flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    Pendidikan Terakhir
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-3 bg-surface-ground rounded-xl">
                        <div class="text-[11px] font-medium text-text-secondary uppercase">Jenjang Pendidikan</div>
                        <div class="text-sm font-bold text-text-main mt-0.5">{{ $pegawai->pendidikan_terakhir ?: '-' }}</div>
                    </div>
                    <div class="p-3 bg-surface-ground rounded-xl col-span-1 sm:col-span-2">
                        <div class="text-[11px] font-medium text-text-secondary uppercase">Jurusan & Nama Instansi</div>
                        <div class="text-sm font-bold text-text-main mt-0.5">{{ $pegawai->jurusan_dan_instansi ?: '-' }}</div>
                    </div>
                    <div class="p-3 bg-surface-ground rounded-xl">
                        <div class="text-[11px] font-medium text-text-secondary uppercase">Tahun / Tgl Lulus</div>
                        <div class="text-sm font-bold text-text-main mt-0.5">
                            {{ ($pegawai->tahun_lulus_pendidikan_terakhir ?: '-') . ($pegawai->tgl_lulus_pendidikan_terakhir ? ' (' . $pegawai->tgl_lulus_pendidikan_terakhir->format('d/m/Y') . ')' : '') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengalaman Kerja 1 s/d 7 -->
            <div class="bg-surface rounded-2xl border border-border p-6 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-text-main uppercase tracking-wider pb-3 border-b border-border flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Riwayat Pengalaman Kerja (Maksimal 7 Riwayat)
                    </span>
                    <span class="text-xs text-text-secondary font-normal">
                        Total Tercatat: {{ is_array($pegawai->pengalaman_kerja) ? count(array_filter($pegawai->pengalaman_kerja, fn($i) => !empty($i['lembaga']))) : 0 }}
                    </span>
                </h3>

                <div class="space-y-3">
                    @php
                        $listPengalaman = is_array($pegawai->pengalaman_kerja) ? $pegawai->pengalaman_kerja : [];
                        $hasPengalaman = false;
                    @endphp

                    @for($i = 0; $i < 7; $i++)
                        @php
                            $exp = $listPengalaman[$i] ?? null;
                            $lembaga = $exp['lembaga'] ?? null;
                            $posisi = $exp['posisi'] ?? null;
                            $periode = $exp['periode'] ?? null;
                        @endphp
                        @if($lembaga || $posisi || $periode)
                            @php $hasPengalaman = true; @endphp
                            <div class="p-4 rounded-xl border border-border bg-surface-ground/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    <span class="w-7 h-7 rounded-lg bg-primary/10 text-primary font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ $i + 1 }}
                                    </span>
                                    <div>
                                        <div class="font-bold text-sm text-text-main">{{ $lembaga ?: 'Instansi / Perusahaan' }}</div>
                                        <div class="text-xs text-text-secondary mt-0.5">Posisi: <span class="font-medium text-text-main">{{ $posisi ?: '-' }}</span></div>
                                    </div>
                                </div>
                                <div class="sm:text-right shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-surface border border-border text-text-secondary">
                                        Periode: {{ $periode ?: '-' }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endfor

                    @if(!$hasPengalaman)
                        <div class="py-8 text-center text-slate-400 text-xs italic">
                            Belum ada data pengalaman kerja yang diinput.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Tab 5: Data Keluarga -->
    @if($activeTab === 'keluarga')
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Data Pasangan -->
                <div class="bg-surface rounded-2xl border border-border p-6 shadow-xs space-y-4">
                    <h3 class="text-sm font-bold text-text-main uppercase tracking-wider pb-3 border-b border-border flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        Status Pernikahan & Pasangan
                    </h3>
                    <dl class="grid grid-cols-1 gap-y-3 text-xs">
                        <div class="flex justify-between py-1 border-b border-border/50">
                            <dt class="text-text-secondary">Status Pernikahan</dt>
                            <dd class="font-bold text-text-main text-right uppercase">{{ $pegawai->status_pernikahan ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-border/50">
                            <dt class="text-text-secondary">Nama Istri / Suami</dt>
                            <dd class="font-semibold text-text-main text-right">{{ $pegawai->nama_pasangan ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-border/50">
                            <dt class="text-text-secondary">Tgl. Lahir Istri / Suami</dt>
                            <dd class="font-semibold text-text-main text-right">
                                {{ $pegawai->tgl_lahir_pasangan ? $pegawai->tgl_lahir_pasangan->isoFormat('D MMMM Y') : '-' }}
                            </dd>
                        </div>
                        <div class="flex justify-between py-1">
                            <dt class="text-text-secondary">Jumlah Anak</dt>
                            <dd class="font-bold text-text-main text-right">{{ $pegawai->jumlah_anak }} Anak</dd>
                        </div>
                    </dl>
                </div>

                <!-- Orang Tua -->
                <div class="bg-surface rounded-2xl border border-border p-6 shadow-xs space-y-4">
                    <h3 class="text-sm font-bold text-text-main uppercase tracking-wider pb-3 border-b border-border flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Orang Tua Kandung
                    </h3>
                    <dl class="grid grid-cols-1 gap-y-3 text-xs">
                        <div class="flex justify-between py-2 border-b border-border/50">
                            <dt class="text-text-secondary">Nama Ayah</dt>
                            <dd class="font-semibold text-text-main text-right">{{ $pegawai->nama_ayah ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-2">
                            <dt class="text-text-secondary">Nama Ibu</dt>
                            <dd class="font-semibold text-text-main text-right">{{ $pegawai->nama_ibu ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Rincian Data Anak (1 s/d 4) -->
            <div class="bg-surface rounded-2xl border border-border p-6 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-text-main uppercase tracking-wider pb-3 border-b border-border flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Daftar Anak (Slot 1 s/d 4)
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    @php
                        $listAnak = is_array($pegawai->data_anak) ? $pegawai->data_anak : [];
                    @endphp
                    @for($i = 0; $i < 4; $i++)
                        @php
                            $anak = $listAnak[$i] ?? null;
                            $namaAnak = $anak['nama'] ?? null;
                            $tglAnak = $anak['tgl_lahir'] ?? null;
                        @endphp
                        <div class="p-4 rounded-xl border border-border bg-surface-ground/50 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold uppercase text-primary">Anak {{ $i + 1 }}</span>
                                @if($namaAnak)
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                @endif
                            </div>
                            <div class="font-bold text-sm text-text-main">{{ $namaAnak ?: '-' }}</div>
                            <div class="text-xs text-text-secondary">
                                Tgl. Lahir: <span class="font-medium text-text-main">{{ $tglAnak ? \Carbon\Carbon::parse($tglAnak)->format('d/m/Y') : '-' }}</span>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    @endif

    <!-- Tab 6: Mata Pelajaran yang Diampu (Integrasi /kepegawaian/penugasan) -->
    @if($activeTab === 'mapel')
        <div class="bg-surface rounded-2xl border border-border p-6 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-border gap-2">
                <div>
                    <h3 class="text-sm font-bold text-text-main uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        Mata Pelajaran yang Diampu (Penugasan Mengajar)
                    </h3>
                    <p class="text-xs text-text-secondary mt-0.5">Data terhubung otomatis dari modul Penugasan Mengajar Guru.</p>
                </div>
                <div class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-primary/10 text-primary">
                    Total Beban Mengajar: {{ $pegawai->penugasanMengajars->sum('jp') }} Jam Pelajaran (JP)
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border border-border rounded-xl overflow-hidden">
                    <thead class="bg-surface-ground text-text-secondary border-b border-border uppercase font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3">Tahun Ajaran / Semester</th>
                            <th class="px-4 py-3">Mata Pelajaran</th>
                            <th class="px-4 py-3">Kelas / Rombel</th>
                            <th class="px-4 py-3 text-center">Beban JP</th>
                            <th class="px-4 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($pegawai->penugasanMengajars as $idx => $penugasan)
                            <tr class="hover:bg-surface-ground/40 transition">
                                <td class="px-4 py-3 text-center font-medium text-text-secondary">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-text-main">
                                    {{ $penugasan->semester?->tahunAjaran?->tahun ?? '-' }} ({{ ucfirst($penugasan->semester?->semester ?? '-') }})
                                </td>
                                <td class="px-4 py-3 font-bold text-text-main">{{ $penugasan->mapel?->nama ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300">
                                        {{ $penugasan->rombel?->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-primary">{{ $penugasan->jp }} JP</td>
                                <td class="px-4 py-3 text-text-secondary">{{ $penugasan->keterangan ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-400 italic">
                                    Pegawai ini belum memiliki penugasan mata pelajaran di kelas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Tab 7: Berkas 5 Foto (1 Utama + 4 Tambahan) -->
    @if($activeTab === 'foto')
        <div class="bg-surface rounded-2xl border border-border p-6 shadow-xs space-y-6">
            <div>
                <h3 class="text-sm font-bold text-text-main uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Berkas Foto Pegawai (1 Foto Utama & 4 Foto Tambahan)
                </h3>
                <p class="text-xs text-text-secondary mt-0.5">Slot 1 merupakan foto profil resmi utama, sedangkan slot 2 s/d 5 adalah foto pendukung beserta keterangannya.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @php
                    $fotosMap = $pegawai->fotos->keyBy('slot');
                @endphp
                @for($slot = 1; $slot <= 5; $slot++)
                    @php
                        $fotoObj = $fotosMap->get($slot);
                        $hasPhoto = false;
                        $fotoUrl = null;
                        $keterangan = $fotoObj?->keterangan;

                        if ($slot === 1) {
                            if ($fotoObj && file_exists(public_path('storage/' . $fotoObj->foto_path))) {
                                $fotoUrl = asset('storage/' . $fotoObj->foto_path);
                                $hasPhoto = true;
                            } elseif ($pegawai->foto_path && file_exists(public_path('storage/' . $pegawai->foto_path))) {
                                $fotoUrl = asset('storage/' . $pegawai->foto_path);
                                $hasPhoto = true;
                            }
                        } else {
                            if ($fotoObj && file_exists(public_path('storage/' . $fotoObj->foto_path))) {
                                $fotoUrl = asset('storage/' . $fotoObj->foto_path);
                                $hasPhoto = true;
                            }
                        }
                    @endphp

                    <div class="rounded-2xl border {{ $slot === 1 ? 'border-primary/40 ring-2 ring-primary/20 bg-primary/5' : 'border-border bg-surface-ground/30' }} p-4 flex flex-col justify-between space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase {{ $slot === 1 ? 'text-primary' : 'text-text-secondary' }}">
                                {{ $slot === 1 ? '★ Foto Utama' : 'Foto Tambahan ' . ($slot - 1) }}
                            </span>
                            @if($hasPhoto)
                                <span class="px-2 py-0.5 text-[10px] font-semibold bg-emerald-100 text-emerald-800 rounded">Tersedia</span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-semibold bg-slate-100 text-slate-500 rounded">Kosong</span>
                            @endif
                        </div>

                        <div class="aspect-square rounded-xl overflow-hidden border border-border bg-surface flex items-center justify-center relative group">
                            @if($hasPhoto)
                                <img src="{{ $fotoUrl }}" alt="Foto Slot {{ $slot }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                <a href="{{ $fotoUrl }}" target="_blank" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Buka Foto
                                </a>
                            @else
                                <div class="text-center p-4 text-slate-400">
                                    <svg class="w-8 h-8 mx-auto mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-[10px]">Belum diunggah</span>
                                </div>
                            @endif
                        </div>

                        <div class="text-xs">
                            <span class="text-text-secondary text-[11px] block">Keterangan:</span>
                            <span class="text-text-main font-medium text-[11px] line-clamp-2">{{ $keterangan ?: ($slot === 1 ? 'Foto Profil Utama' : '-') }}</span>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    @endif
</div>
