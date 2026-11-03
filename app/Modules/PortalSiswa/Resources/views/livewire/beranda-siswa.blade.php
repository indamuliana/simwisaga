<div class="space-y-6">
    <!-- Welcome Header / Profile Card -->
    <div class="relative overflow-hidden rounded-2xl border border-primary/20 bg-linear-to-br from-primary/10 via-surface to-surface p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-4 sm:gap-5">
                <div class="relative h-16 w-16 sm:h-20 sm:w-20 shrink-0 overflow-hidden rounded-2xl border-2 border-primary/30 bg-primary-soft shadow-inner flex items-center justify-center">
                    @if ($siswa && $siswa->foto)
                        <img src="{{ asset('storage/' . $siswa->foto) }}" alt="{{ $siswa->nama }}" class="h-full w-full object-cover">
                    @else
                        <span class="text-2xl sm:text-3xl font-black text-primary">{{ strtoupper(substr($siswa?->nama ?? auth()->user()->name, 0, 2)) }}</span>
                    @endif
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary text-white tracking-wide uppercase">
                            Siswa
                        </span>
                        @if ($siswa?->program)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                {{ $siswa->program->nama }}
                            </span>
                        @endif
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-text-main tracking-tight mt-1">
                        {{ $siswa?->nama ?? auth()->user()->name }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-y-1 gap-x-3 text-xs text-text-muted mt-1">
                        <span>NISN: <strong class="text-text-secondary">{{ $siswa?->nisn ?: '-' }}</strong></span>
                        <span>&bull;</span>
                        <span>NIPD: <strong class="text-text-secondary">{{ $siswa?->nipd ?: '-' }}</strong></span>
                        <span>&bull;</span>
                        <span>Rombel: <strong class="text-text-secondary">{{ $siswa?->rombel?->nama ?: 'Belum diplotting' }}</strong></span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:items-end gap-2 text-xs">
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

    <!-- Alert Status Pengajuan jika ada -->
    @if ($pendingPerbaikan > 0 || $pendingBeasiswa > 0)
        <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 p-4 text-xs font-medium text-amber-700 dark:text-amber-300 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <span class="text-base">⏳</span>
                <span>
                    Anda memiliki
                    @if ($pendingPerbaikan > 0)
                        <strong>{{ $pendingPerbaikan }} pengajuan perbaikan data</strong>
                    @endif
                    @if ($pendingPerbaikan > 0 && $pendingBeasiswa > 0) dan @endif
                    @if ($pendingBeasiswa > 0)
                        <strong>{{ $pendingBeasiswa }} pengajuan beasiswa</strong>
                    @endif
                    yang sedang menunggu verifikasi staf sekolah.
                </span>
            </div>
        </div>
    @endif

    <!-- Menu Akses Cepat (Quick Access Grid) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <a href="{{ route('portal-siswa.profil') }}" class="card p-4 hover:border-primary/40 hover:shadow-xs transition-all group flex flex-col items-center text-center">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform">
                👤
            </div>
            <span class="text-xs font-bold text-text-main group-hover:text-primary">Profil Saya</span>
            <span class="text-[10px] text-text-muted mt-0.5">Biodata & Ajukan Edit</span>
        </a>

        <a href="{{ route('portal-siswa.nilai-rapor') }}" class="card p-4 hover:border-primary/40 hover:shadow-xs transition-all group flex flex-col items-center text-center">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform">
                📊
            </div>
            <span class="text-xs font-bold text-text-main group-hover:text-primary">Nilai & Rapor</span>
            <span class="text-[10px] text-text-muted mt-0.5">Formatif, Sumatif, Rapor</span>
        </a>

        <a href="{{ route('portal-siswa.kehadiran') }}" class="card p-4 hover:border-primary/40 hover:shadow-xs transition-all group flex flex-col items-center text-center">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform">
                📅
            </div>
            <span class="text-xs font-bold text-text-main group-hover:text-primary">Kehadiran</span>
            <span class="text-[10px] font-bold text-emerald-600 mt-0.5">{{ $absensiStats['persen'] }}% Hadir</span>
        </a>

        <a href="{{ route('portal-siswa.upd-seni') }}" class="card p-4 hover:border-primary/40 hover:shadow-xs transition-all group flex flex-col items-center text-center">
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform">
                🎨
            </div>
            <span class="text-xs font-bold text-text-main group-hover:text-primary">UPD & Seni</span>
            <span class="text-[10px] text-text-muted mt-0.5">Ekskul & Seni Budaya</span>
        </a>

        <a href="{{ route('portal-siswa.prestasi') }}" class="card p-4 hover:border-primary/40 hover:shadow-xs transition-all group flex flex-col items-center text-center">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform">
                🏆
            </div>
            <span class="text-xs font-bold text-text-main group-hover:text-primary">Prestasi</span>
            <span class="text-[10px] text-text-muted mt-0.5">Piagam & Pengajuan</span>
        </a>

        <a href="{{ route('portal-siswa.beasiswa') }}" class="card p-4 hover:border-primary/40 hover:shadow-xs transition-all group flex flex-col items-center text-center">
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform">
                🎓
            </div>
            <span class="text-xs font-bold text-text-main group-hover:text-primary">Beasiswa</span>
            <span class="text-[10px] text-text-muted mt-0.5">OTA & Pengajuan Bantuan</span>
        </a>
    </div>

    <!-- Section Agenda Kegiatan Terdekat (Publik) -->
    <div class="card p-6">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2">
                <span class="text-xl">📢</span>
                <div>
                    <h2 class="text-base font-bold text-text-main">Agenda Kegiatan Terdekat</h2>
                    <p class="text-xs text-text-muted">Jadwal kegiatan akademik, ujian, libur, dan perayaan sekolah yang bersifat umum</p>
                </div>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary">
                Kategori Publik
            </span>
        </div>

        @if ($agendas->isEmpty())
            <div class="p-8 text-center rounded-xl border border-dashed border-border">
                <p class="text-xs text-text-muted">Belum ada agenda kegiatan publik terdekat yang dijadwalkan.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($agendas as $agenda)
                    <div class="rounded-xl border border-border bg-surface-raised p-4 hover:border-primary/40 transition-colors flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider text-white" style="background-color: {{ $agenda->display_warna }}">
                                    {{ $agenda->kategori?->nama ?? 'Agenda' }}
                                </span>
                                @if ($agenda->is_all_day)
                                    <span class="text-[10px] text-text-muted">Seharian</span>
                                @elseif ($agenda->jam_mulai)
                                    <span class="text-[10px] font-mono text-text-muted">
                                        {{ substr($agenda->jam_mulai, 0, 5) }} {{ $agenda->jam_selesai ? '- ' . substr($agenda->jam_selesai, 0, 5) : 'WIB' }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-sm font-bold text-text-main line-clamp-2">{{ $agenda->judul }}</h3>
                            @if ($agenda->deskripsi)
                                <p class="text-xs text-text-secondary mt-1 line-clamp-2">{{ $agenda->deskripsi }}</p>
                            @endif
                        </div>

                        <div class="mt-4 pt-3 border-t border-border flex items-center justify-between text-[11px] text-text-muted">
                            <span class="flex items-center gap-1 font-medium">
                                📅 {{ $agenda->tgl_mulai->format('d M Y') }}
                                @if ($agenda->tgl_selesai && $agenda->tgl_selesai != $agenda->tgl_mulai)
                                    - {{ $agenda->tgl_selesai->format('d M Y') }}
                                @endif
                            </span>
                            @if ($agenda->lokasi)
                                <span class="truncate max-w-[120px]" title="{{ $agenda->lokasi }}">📍 {{ $agenda->lokasi }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
