<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary text-white tracking-wide uppercase">
                    Portal Siswa
                </span>
                <span class="text-xs text-text-muted">&bull; Komunikasi Sekolah</span>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight mt-1 flex items-center gap-2.5">
                <span class="text-primary">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </span>
                <span>Kontak Guru &amp; Pegawai</span>
            </h1>
            <p class="text-xs text-text-secondary mt-1">
                Informasi kontak resmi dewan guru, pembimbing rayon, wali kelas, dan staf kependidikan SMK Wikrama 1 Garut.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('portal-siswa.beranda') }}" class="btn btn-secondary text-xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- BAGIAN 1: PENEMPATAN SPESIAL (PEMBIMBING RAYON & WALI KELAS)        -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    <div class="space-y-3">
        <div class="flex items-center justify-between border-b border-border/80 pb-2">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-text-main flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-primary animate-pulse"></span>
                    <span>Pembimbing Utama Saya (Prioritas)</span>
                </h2>
                <p class="text-[11px] text-text-muted">Kontak langsung guru/pegawai yang bertanggung jawab atas pendampingan Anda sehari-hari.</p>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-primary-soft text-primary border border-primary/20">
                Penting
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Pembimbing Rayon -->
            <div class="relative overflow-hidden rounded-2xl border-2 border-primary/30 bg-linear-to-br from-primary/10 via-surface to-surface p-5 shadow-xs">
                <div class="absolute top-0 right-0 transform translate-x-2 -translate-y-2 w-28 h-28 bg-primary/10 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex items-start justify-between gap-3 mb-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-primary text-white shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>Pembimbing Rayon {{ $siswa?->rayon?->nama ?? '-' }}</span>
                    </span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-text-muted bg-surface-raised px-2 py-0.5 rounded border border-border">
                        Rayon
                    </span>
                </div>

                @if ($pembimbingRayon)
                    <div class="flex items-start gap-4">
                        <div class="relative h-16 w-16 shrink-0 overflow-hidden rounded-2xl border-2 border-primary/40 bg-primary-soft shadow-inner flex items-center justify-center">
                            @if ($pembimbingRayon['foto_path'])
                                <img src="{{ asset('storage/' . $pembimbingRayon['foto_path']) }}" alt="{{ $pembimbingRayon['nama'] }}" class="h-full w-full object-cover">
                            @else
                                <span class="text-2xl font-black text-primary">{{ strtoupper(substr($pembimbingRayon['nama'], 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-black text-text-main truncate" title="{{ $pembimbingRayon['nama'] }}">
                                {{ $pembimbingRayon['nama'] }}
                            </h3>
                            <p class="text-xs text-primary font-semibold mt-0.5">{{ $pembimbingRayon['peran'] }}</p>
                            <p class="text-[11px] text-text-muted mt-1 leading-relaxed">
                                Konsultasi izin tidak masuk sekolah, kedisiplinan, tata tertib, dan bimbingan kepribadian.
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-border/80 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5 text-xs">
                        <div class="space-y-0.5 text-text-secondary">
                            <div class="flex items-center gap-1.5 font-mono">
                                <span class="text-text-muted">📱</span>
                                <span class="font-bold text-text-main">{{ $this->formatPhoneDisplay($pembimbingRayon['no_hp']) }}</span>
                            </div>
                            @if ($pembimbingRayon['email'])
                                <div class="flex items-center gap-1.5 text-[11px] text-text-muted truncate">
                                    <span>✉️</span>
                                    <span class="truncate">{{ $pembimbingRayon['email'] }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($pembimbingRayon['no_hp'])
                                <a href="{{ $this->formatWaUrl($pembimbingRayon['no_hp'], $pembimbingRayon['wa_pesan']) }}" target="_blank" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-2xs transition-all text-xs">
                                    <span>💬 Chat WA</span>
                                </a>
                                <a href="tel:{{ $pembimbingRayon['no_hp'] }}" class="inline-flex items-center justify-center p-2 rounded-xl bg-surface-raised hover:bg-surface border border-border text-text-main font-bold shadow-2xs transition-all" title="Panggil Nomor">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </a>
                            @endif
                            @if ($pembimbingRayon['email'])
                                <a href="mailto:{{ $pembimbingRayon['email'] }}" class="inline-flex items-center justify-center p-2 rounded-xl bg-surface-raised hover:bg-surface border border-border text-text-main font-bold shadow-2xs transition-all" title="Kirim Email">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="p-6 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                        Data Pembimbing Rayon belum terhubung dengan akun guru/pegawai.
                    </div>
                @endif
            </div>

            <!-- Wali Kelas -->
            <div class="relative overflow-hidden rounded-2xl border-2 border-indigo-500/30 bg-linear-to-br from-indigo-500/10 via-surface to-surface p-5 shadow-xs">
                <div class="absolute top-0 right-0 transform translate-x-2 -translate-y-2 w-28 h-28 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex items-start justify-between gap-3 mb-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-indigo-600 text-white shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span>Wali Kelas {{ $siswa?->rombel?->nama ?? '-' }}</span>
                    </span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-text-muted bg-surface-raised px-2 py-0.5 rounded border border-border">
                        Akademik
                    </span>
                </div>

                @if ($waliKelas)
                    <div class="flex items-start gap-4">
                        <div class="relative h-16 w-16 shrink-0 overflow-hidden rounded-2xl border-2 border-indigo-500/40 bg-indigo-500/10 shadow-inner flex items-center justify-center">
                            @if ($waliKelas['foto_path'])
                                <img src="{{ asset('storage/' . $waliKelas['foto_path']) }}" alt="{{ $waliKelas['nama'] }}" class="h-full w-full object-cover">
                            @else
                                <span class="text-2xl font-black text-indigo-600">{{ strtoupper(substr($waliKelas['nama'], 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-black text-text-main truncate" title="{{ $waliKelas['nama'] }}">
                                {{ $waliKelas['nama'] }}
                            </h3>
                            <p class="text-xs text-indigo-600 font-semibold mt-0.5">{{ $waliKelas['peran'] }}</p>
                            <p class="text-[11px] text-text-muted mt-1 leading-relaxed">
                                Konsultasi kemajuan belajar di kelas, kehadiran pembelajaran, buku rapor, dan koordinasi orang tua.
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-border/80 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5 text-xs">
                        <div class="space-y-0.5 text-text-secondary">
                            <div class="flex items-center gap-1.5 font-mono">
                                <span class="text-text-muted">📱</span>
                                <span class="font-bold text-text-main">{{ $this->formatPhoneDisplay($waliKelas['no_hp']) }}</span>
                            </div>
                            @if ($waliKelas['email'])
                                <div class="flex items-center gap-1.5 text-[11px] text-text-muted truncate">
                                    <span>✉️</span>
                                    <span class="truncate">{{ $waliKelas['email'] }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($waliKelas['no_hp'])
                                <a href="{{ $this->formatWaUrl($waliKelas['no_hp'], $waliKelas['wa_pesan']) }}" target="_blank" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-2xs transition-all text-xs">
                                    <span>💬 Chat WA</span>
                                </a>
                                <a href="tel:{{ $waliKelas['no_hp'] }}" class="inline-flex items-center justify-center p-2 rounded-xl bg-surface-raised hover:bg-surface border border-border text-text-main font-bold shadow-2xs transition-all" title="Panggil Nomor">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </a>
                            @endif
                            @if ($waliKelas['email'])
                                <a href="mailto:{{ $waliKelas['email'] }}" class="inline-flex items-center justify-center p-2 rounded-xl bg-surface-raised hover:bg-surface border border-border text-text-main font-bold shadow-2xs transition-all" title="Kirim Email">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="p-6 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                        Data Wali Kelas belum terhubung dengan akun guru/pegawai.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- BAGIAN 2: GURU PENGAMPU MATA PELAJARAN ROMBEL SAYA (TABEL)           -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    <div class="card p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-border pb-3">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-text-main flex items-center gap-2">
                    <span>📚 Guru Pengampu Rombel {{ $siswa?->rombel?->nama ?? '' }}</span>
                </h2>
                <p class="text-xs text-text-muted">Daftar dewan guru yang mengajar mata pelajaran di kelas Anda pada semester aktif ini.</p>
            </div>
            <span class="text-xs font-semibold text-text-secondary">
                Total: {{ $guruPengampus->count() }} Guru Mata Pelajaran
            </span>
        </div>

        @if ($guruPengampus->isEmpty())
            <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                Belum ada penugasan mengajar yang terdaftar untuk rombel Anda pada semester aktif ini.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-surface-raised text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                        <tr>
                            <th class="p-3 w-10 text-center">No</th>
                            <th class="p-3">Mata Pelajaran</th>
                            <th class="p-3">Guru Pengampu</th>
                            <th class="p-3">No. HP / WhatsApp</th>
                            <th class="p-3">Email</th>
                            <th class="p-3 text-center w-28">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($guruPengampus as $index => $gp)
                            <tr class="hover:bg-surface-hover/50 transition-colors">
                                <td class="p-3 text-center text-text-muted font-mono font-bold">{{ $index + 1 }}</td>
                                <td class="p-3">
                                    <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-bold bg-primary-soft text-primary">
                                        {{ $gp['mapel'] }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="h-8 w-8 shrink-0 overflow-hidden rounded-lg border border-border bg-surface flex items-center justify-center font-bold text-xs text-text-secondary">
                                            @if ($gp['foto_path'])
                                                <img src="{{ asset('storage/' . $gp['foto_path']) }}" alt="{{ $gp['nama'] }}" class="h-full w-full object-cover">
                                            @else
                                                {{ strtoupper(substr($gp['nama'], 0, 2)) }}
                                            @endif
                                        </div>
                                        <span class="font-bold text-text-main text-xs">{{ $gp['nama'] }}</span>
                                    </div>
                                </td>
                                <td class="p-3">
                                    @if ($gp['no_hp'])
                                        <div class="flex items-center gap-1.5 font-mono text-xs text-text-secondary">
                                            <span class="text-emerald-600">📱</span>
                                            <span class="font-semibold">{{ $this->formatPhoneDisplay($gp['no_hp']) }}</span>
                                        </div>
                                    @else
                                        <span class="text-text-muted italic">-</span>
                                    @endif
                                </td>
                                <td class="p-3 text-text-secondary">
                                    @if ($gp['email'])
                                        <a href="mailto:{{ $gp['email'] }}" class="hover:text-primary transition-colors hover:underline">
                                            {{ $gp['email'] }}
                                        </a>
                                    @else
                                        <span class="text-text-muted italic">-</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        @if ($gp['no_hp'])
                                            <a href="{{ $this->formatWaUrl($gp['no_hp'], $gp['wa_pesan']) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] inline-flex items-center gap-1 shadow-2xs transition-colors" title="Chat WhatsApp">
                                                <span>WA</span>
                                            </a>
                                            <a href="tel:{{ $gp['no_hp'] }}" class="p-1 rounded-lg bg-surface border border-border text-text-secondary hover:text-text-main transition-colors" title="Panggil Telepon">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                            </a>
                                        @endif
                                        @if ($gp['email'])
                                            <a href="mailto:{{ $gp['email'] }}" class="p-1 rounded-lg bg-surface border border-border text-text-secondary hover:text-text-main transition-colors" title="Kirim Email">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- BAGIAN 3: PEMBINA EKSKUL, SENI BUDAYA, & KELOMPOK CUSTOM (JIKA ADA)  -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    @if ($pembinaEkskul->isNotEmpty() || $guruSeni->isNotEmpty() || $pembimbingCustom->isNotEmpty())
        <div class="card p-6 space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-text-main flex items-center gap-2 border-b border-border pb-3">
                <span>🎯 Pembina Ekstrakurikuler, Seni Budaya &amp; Kelompok Khusus</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($pembinaEkskul as $pe)
                    <div class="rounded-xl border border-border bg-surface-raised/40 p-3.5 flex flex-col justify-between">
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20 mb-2">
                                Ekstrakurikuler: {{ $pe['ekskul'] }}
                            </span>
                            <h4 class="text-xs font-bold text-text-main">{{ $pe['nama'] }}</h4>
                            <p class="text-[11px] text-text-muted mt-0.5">{{ $pe['peran'] }}</p>
                        </div>
                        <div class="mt-3 pt-2 border-t border-border/50 flex items-center justify-between">
                            <span class="text-xs font-mono text-text-secondary">{{ $this->formatPhoneDisplay($pe['no_hp']) }}</span>
                            @if ($pe['no_hp'])
                                <a href="{{ $this->formatWaUrl($pe['no_hp'], $pe['wa_pesan']) }}" target="_blank" class="px-2 py-1 rounded-lg bg-emerald-600 text-white font-bold text-[10px]">
                                    WA
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach

                @foreach ($guruSeni as $gs)
                    <div class="rounded-xl border border-border bg-surface-raised/40 p-3.5 flex flex-col justify-between">
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-purple-500/10 text-purple-600 border border-purple-500/20 mb-2">
                                Seni Budaya: {{ $gs['seni'] }}
                            </span>
                            <h4 class="text-xs font-bold text-text-main">{{ $gs['nama'] }}</h4>
                            <p class="text-[11px] text-text-muted mt-0.5">{{ $gs['peran'] }}</p>
                        </div>
                        <div class="mt-3 pt-2 border-t border-border/50 flex items-center justify-between">
                            <span class="text-xs font-mono text-text-secondary">{{ $this->formatPhoneDisplay($gs['no_hp']) }}</span>
                            @if ($gs['no_hp'])
                                <a href="{{ $this->formatWaUrl($gs['no_hp'], $gs['wa_pesan']) }}" target="_blank" class="px-2 py-1 rounded-lg bg-emerald-600 text-white font-bold text-[10px]">
                                    WA
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach

                @foreach ($pembimbingCustom as $pc)
                    <div class="rounded-xl border border-border bg-surface-raised/40 p-3.5 flex flex-col justify-between">
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold text-white mb-2 shadow-2xs" style="background-color: {{ $pc['warna'] }}">
                                {{ $pc['kelompok'] }} ({{ $pc['kategori'] }})
                            </span>
                            <h4 class="text-xs font-bold text-text-main">{{ $pc['nama'] }}</h4>
                            <p class="text-[11px] text-text-muted mt-0.5">{{ $pc['peran'] }}</p>
                        </div>
                        <div class="mt-3 pt-2 border-t border-border/50 flex items-center justify-between">
                            <span class="text-xs font-mono text-text-secondary">{{ $this->formatPhoneDisplay($pc['no_hp']) }}</span>
                            @if ($pc['no_hp'])
                                <a href="{{ $this->formatWaUrl($pc['no_hp'], $pc['wa_pesan']) }}" target="_blank" class="px-2 py-1 rounded-lg bg-emerald-600 text-white font-bold text-[10px]">
                                    WA
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- BAGIAN 4: DIREKTORI SEMUA GURU & PEGAWAI SEKOLAH                     -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    <div class="card p-6 space-y-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-border pb-4">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-text-main flex items-center gap-2">
                    <span>🗂️ Direktori Kontak Semua Guru &amp; Pegawai</span>
                </h2>
                <p class="text-xs text-text-muted">Cari nomor telepon atau email seluruh staf pendidik dan tenaga kependidikan sekolah.</p>
            </div>

            <!-- Search input -->
            <div class="relative w-full md:w-72">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama, mapel, jabatan..."
                    class="input text-xs w-full pl-8 pr-8"
                />
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-text-muted">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                @if ($search)
                    <button type="button" wire:click="$set('search', '')" class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-text-muted hover:text-text-main">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>

        <!-- Filter Kategori Tabs -->
        <div class="flex flex-wrap items-center gap-1.5 text-xs">
            <button
                type="button"
                wire:click="$set('kategori', 'semua')"
                class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $kategori === 'semua' ? 'bg-primary text-white shadow-2xs' : 'bg-surface-raised hover:bg-surface text-text-secondary border border-border' }}"
            >
                Semua Pegawai
            </button>
            <button
                type="button"
                wire:click="$set('kategori', 'guru')"
                class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $kategori === 'guru' ? 'bg-primary text-white shadow-2xs' : 'bg-surface-raised hover:bg-surface text-text-secondary border border-border' }}"
            >
                Dewan Guru
            </button>
            <button
                type="button"
                wire:click="$set('kategori', 'tendik')"
                class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $kategori === 'tendik' ? 'bg-primary text-white shadow-2xs' : 'bg-surface-raised hover:bg-surface text-text-secondary border border-border' }}"
            >
                Tenaga Kependidikan (Tendik)
            </button>
            <button
                type="button"
                wire:click="$set('kategori', 'struktural')"
                class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $kategori === 'struktural' ? 'bg-primary text-white shadow-2xs' : 'bg-surface-raised hover:bg-surface text-text-secondary border border-border' }}"
            >
                Pejabat Struktural
            </button>
        </div>

        <!-- Direktori Tabel -->
        @if ($direktoriPegawai->isEmpty())
            <div class="p-12 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                Tidak ada guru atau pegawai yang cocok dengan kata kunci pencarian Anda.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-surface-raised text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                        <tr>
                            <th class="p-3 w-10 text-center">No</th>
                            <th class="p-3">Nama Guru / Pegawai</th>
                            <th class="p-3">Jenis GTK</th>
                            <th class="p-3">Jabatan Struktural / Mapel</th>
                            <th class="p-3">No. HP / WhatsApp</th>
                            <th class="p-3">Email</th>
                            <th class="p-3 text-center w-28">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($direktoriPegawai as $index => $peg)
                            <tr class="hover:bg-surface-hover/50 transition-colors">
                                <td class="p-3 text-center text-text-muted font-mono font-bold">{{ $index + 1 }}</td>
                                <td class="p-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="h-8 w-8 shrink-0 overflow-hidden rounded-lg border border-border bg-surface flex items-center justify-center font-bold text-xs text-text-secondary">
                                            @if ($peg->foto_path)
                                                <img src="{{ asset('storage/' . $peg->foto_path) }}" alt="{{ $peg->nama_lengkap }}" class="h-full w-full object-cover">
                                            @else
                                                {{ strtoupper(substr($peg->nama_lengkap, 0, 2)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <span class="font-bold text-text-main text-xs block">{{ $peg->nama_gelar }}</span>
                                            @if ($peg->nip || $peg->niy)
                                                <span class="text-[10px] text-text-muted font-mono">{{ $peg->nip ?: $peg->niy }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3 whitespace-nowrap">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ strtolower($peg->jenis_gtk) === 'guru' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-surface-raised text-text-secondary border border-border' }}">
                                        {{ $peg->jenis_gtk ?: 'Pegawai' }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    @if ($peg->jabatan_struktural)
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/20 mb-0.5">
                                            {{ $peg->jabatan_struktural }}
                                        </span>
                                    @endif
                                    @if ($peg->mapel_diampu)
                                        <span class="text-[11px] text-text-secondary block">
                                            Mapel: {{ $peg->mapel_diampu }}
                                        </span>
                                    @elseif (!$peg->jabatan_struktural)
                                        <span class="text-text-muted text-xs">-</span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    @if ($peg->no_hp)
                                        <div class="flex items-center gap-1.5 font-mono text-xs text-text-secondary">
                                            <span class="text-emerald-600">📱</span>
                                            <span class="font-semibold">{{ $this->formatPhoneDisplay($peg->no_hp) }}</span>
                                        </div>
                                    @else
                                        <span class="text-text-muted italic">-</span>
                                    @endif
                                </td>
                                <td class="p-3 text-text-secondary">
                                    @if ($peg->email)
                                        <a href="mailto:{{ $peg->email }}" class="hover:text-primary transition-colors hover:underline">
                                            {{ $peg->email }}
                                        </a>
                                    @else
                                        <span class="text-text-muted italic">-</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        @if ($peg->no_hp)
                                            <a
                                                href="{{ $this->formatWaUrl($peg->no_hp, "Assalamu'alaikum wr. wb. Halo Bapak/Ibu {$peg->nama_lengkap}, saya {$siswa?->nama} siswa SMK Wikrama 1 Garut...") }}"
                                                target="_blank"
                                                class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] inline-flex items-center gap-1 shadow-2xs transition-colors"
                                                title="Chat WhatsApp"
                                            >
                                                <span>WA</span>
                                            </a>
                                            <a
                                                href="tel:{{ $peg->no_hp }}"
                                                class="p-1 rounded-lg bg-surface border border-border text-text-secondary hover:text-text-main transition-colors"
                                                title="Panggil Telepon"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                            </a>
                                        @endif
                                        @if ($peg->email)
                                            <a
                                                href="mailto:{{ $peg->email }}"
                                                class="p-1 rounded-lg bg-surface border border-border text-text-secondary hover:text-text-main transition-colors"
                                                title="Kirim Email"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
