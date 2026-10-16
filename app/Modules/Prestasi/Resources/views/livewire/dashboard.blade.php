<div class="space-y-6">
    @include('prestasi::partials.sub-nav', ['active' => 'index'])

    <!-- Filter Bar -->
    <div class="bg-surface rounded-2xl border border-border p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Filter Periode:</span>
            <select wire:model.live="selectedTahunAjaranId" class="text-xs rounded-xl border border-border bg-surface px-3 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary">
                <option value="">-- Semua Tahun Ajaran --</option>
                @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
                @endforeach
            </select>
            <select wire:model.live="selectedSemesterId" class="text-xs rounded-xl border border-border bg-surface px-3 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary">
                <option value="">-- Semua Semester --</option>
                @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}">{{ $sem->nama }} ({{ ucfirst($sem->semester) }})</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('prestasi.siswa') }}" class="btn btn-sm btn-primary flex items-center gap-1.5 text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                + Catat Prestasi Baru
            </a>
        </div>
    </div>

    <!-- 4 Metrik Kartu Utama -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Siswa -->
        <div class="bg-surface rounded-2xl border border-border p-5 relative overflow-hidden shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-text-secondary uppercase tracking-wider">Prestasi Siswa</span>
                    <h3 class="text-3xl font-black text-text-main mt-1">{{ number_format($stats['total_siswa']) }}</h3>
                    <p class="text-xs text-text-muted mt-1">Akademik, Lomba, Sertifikasi</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-border flex justify-between text-xs text-text-secondary">
                <span>Juara: <strong>{{ $stats['kategori_siswa']['non_akademik'] }}</strong></span>
                <span>Akademik: <strong>{{ $stats['kategori_siswa']['akademik'] }}</strong></span>
                <span>Sertifikasi: <strong>{{ $stats['kategori_siswa']['sertifikasi'] }}</strong></span>
            </div>
        </div>

        <!-- Guru & GTK -->
        <div class="bg-surface rounded-2xl border border-border p-5 relative overflow-hidden shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-text-secondary uppercase tracking-wider">Prestasi Guru & GTK</span>
                    <h3 class="text-3xl font-black text-text-main mt-1">{{ number_format($stats['total_guru']) }}</h3>
                    <p class="text-xs text-text-muted mt-1">Karya, Inovasi & Kejuaraan</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-border flex justify-between text-xs text-text-secondary">
                <a href="{{ route('prestasi.guru') }}" class="text-primary hover:underline font-medium">Kelola Prestasi GTK &rarr;</a>
            </div>
        </div>

        <!-- Sekolah -->
        <div class="bg-surface rounded-2xl border border-border p-5 relative overflow-hidden shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-text-secondary uppercase tracking-wider">Prestasi Sekolah</span>
                    <h3 class="text-3xl font-black text-text-main mt-1">{{ number_format($stats['total_sekolah']) }}</h3>
                    <p class="text-xs text-text-muted mt-1">Akreditasi & Kelembagaan</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-border flex justify-between text-xs text-text-secondary">
                <a href="{{ route('prestasi.sekolah') }}" class="text-purple-600 dark:text-purple-400 hover:underline font-medium">Portofolio Sekolah &rarr;</a>
            </div>
        </div>

        <!-- Menunggu Verifikasi -->
        <div class="bg-surface rounded-2xl border border-border p-5 relative overflow-hidden shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-text-secondary uppercase tracking-wider">Menunggu Verifikasi</span>
                    <h3 class="text-3xl font-black {{ $stats['total_pending'] > 0 ? 'text-amber-500' : 'text-text-main' }} mt-1">
                        {{ number_format($stats['total_pending']) }}
                    </h3>
                    <p class="text-xs text-text-muted mt-1">Pengajuan Mandiri Siswa</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-border flex justify-between text-xs text-text-secondary">
                <a href="{{ route('prestasi.verifikasi') }}" class="text-amber-600 hover:underline font-medium">Buka Antrean Verifikasi &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Leaderboard & Top Kontributor Prestasi -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top Rayon -->
        <div class="bg-surface rounded-2xl border border-border p-5 shadow-xs">
            <div class="flex items-center justify-between pb-3 border-b border-border">
                <h4 class="font-bold text-text-main flex items-center gap-2 text-sm">
                    <span class="text-base">🚩</span> Top Rayon Penyumbang Prestasi
                </h4>
            </div>
            <div class="mt-4 space-y-3">
                @forelse($stats['top_rayon'] as $idx => $rayon)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-surface-hover/60 border border-border/60">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center font-black text-xs {{ $idx === 0 ? 'bg-amber-500 text-white' : ($idx === 1 ? 'bg-slate-400 text-white' : ($idx === 2 ? 'bg-amber-700 text-white' : 'bg-surface text-text-muted border border-border')) }}">
                                {{ $idx + 1 }}
                            </span>
                            <div>
                                <h5 class="text-sm font-bold text-text-main">{{ $rayon['nama'] }}</h5>
                                <p class="text-xs text-text-secondary">Pembimbing: {{ $rayon['pembimbing'] }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-black text-teal-600 dark:text-teal-400 bg-teal-500/10 px-2.5 py-1 rounded-lg">
                            {{ $rayon['total'] }} Prestasi
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-text-muted text-center py-4">Belum ada data prestasi per rayon.</p>
                @endforelse
            </div>
        </div>

        <!-- Top Rombel -->
        <div class="bg-surface rounded-2xl border border-border p-5 shadow-xs">
            <div class="flex items-center justify-between pb-3 border-b border-border">
                <h4 class="font-bold text-text-main flex items-center gap-2 text-sm">
                    <span class="text-base">🎓</span> Top Rombel / Kelas Juara
                </h4>
            </div>
            <div class="mt-4 space-y-3">
                @forelse($stats['top_rombel'] as $idx => $rombel)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-surface-hover/60 border border-border/60">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center font-black text-xs {{ $idx === 0 ? 'bg-amber-500 text-white' : ($idx === 1 ? 'bg-slate-400 text-white' : ($idx === 2 ? 'bg-amber-700 text-white' : 'bg-surface text-text-muted border border-border')) }}">
                                {{ $idx + 1 }}
                            </span>
                            <div>
                                <h5 class="text-sm font-bold text-text-main">{{ $rombel['nama'] }}</h5>
                                <p class="text-xs text-text-secondary">Wali Kelas: {{ $rombel['wali_kelas'] }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-black text-blue-600 dark:text-blue-400 bg-blue-500/10 px-2.5 py-1 rounded-lg">
                            {{ $rombel['total'] }} Prestasi
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-text-muted text-center py-4">Belum ada data prestasi per rombel.</p>
                @endforelse
            </div>
        </div>

        <!-- Top Guru Pendamping -->
        <div class="bg-surface rounded-2xl border border-border p-5 shadow-xs">
            <div class="flex items-center justify-between pb-3 border-b border-border">
                <h4 class="font-bold text-text-main flex items-center gap-2 text-sm">
                    <span class="text-base">👨‍🏫</span> Top Guru Pembimbing Siswa
                </h4>
            </div>
            <div class="mt-4 space-y-3">
                @forelse($stats['top_guru'] as $idx => $guru)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-surface-hover/60 border border-border/60">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center font-black text-xs {{ $idx === 0 ? 'bg-amber-500 text-white' : ($idx === 1 ? 'bg-slate-400 text-white' : ($idx === 2 ? 'bg-amber-700 text-white' : 'bg-surface text-text-muted border border-border')) }}">
                                {{ $idx + 1 }}
                            </span>
                            <div>
                                <h5 class="text-sm font-bold text-text-main">{{ $guru['nama'] }}</h5>
                                <p class="text-xs text-text-secondary">NIP: {{ $guru['nip'] }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-black text-purple-600 dark:text-purple-400 bg-purple-500/10 px-2.5 py-1 rounded-lg">
                            {{ $guru['total'] }} Bimbingan
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-text-muted text-center py-4">Belum ada data pembimbing terdata.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Feed Prestasi Terbaru Siswa, Guru, & Sekolah -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 5 Prestasi Siswa Terbaru -->
        <div class="bg-surface rounded-2xl border border-border p-5 shadow-xs">
            <div class="flex items-center justify-between pb-3 border-b border-border">
                <h4 class="font-bold text-text-main flex items-center gap-2 text-sm">
                    <span>🏆</span> Prestasi Siswa Terbaru
                </h4>
                <a href="{{ route('prestasi.siswa') }}" class="text-xs text-primary hover:underline font-medium">Lihat Semua &rarr;</a>
            </div>
            <div class="divide-y divide-border mt-3">
                @forelse($recentSiswa as $p)
                    <div class="py-3 flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-600 flex items-center justify-center flex-shrink-0 font-black text-sm">
                            🏅
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300">
                                    {{ $p->peringkat }}
                                </span>
                                <span class="text-xs text-text-muted">&bull; {{ $p->tingkat_label }}</span>
                            </div>
                            <h5 class="font-bold text-sm text-text-main truncate mt-0.5">{{ $p->nama_prestasi }}</h5>
                            <p class="text-xs text-text-secondary">
                                Oleh: <strong class="text-text-main">{{ $p->tipe_peserta === 'kelompok' ? ($p->nama_tim ?: 'Tim Siswa') : $p->daftar_siswa_nama }}</strong>
                                @if($p->pembimbing_nama !== '-')
                                    &bull; Bimbingan: {{ $p->pembimbing_nama }}
                                @endif
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="text-[11px] text-text-muted block">{{ $p->tanggal_perolehan ? $p->tanggal_perolehan->format('d M Y') : '-' }}</span>
                            @if($p->sertifikat_file_path || $p->foto_file_path)
                                <span class="inline-flex items-center gap-1 text-[10px] text-teal-600 font-semibold mt-1">
                                    📎 Bukti Ada
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-text-muted text-center py-6">Belum ada data prestasi siswa terbaru.</p>
                @endforelse
            </div>
        </div>

        <!-- 5 Prestasi GTK & Sekolah Terbaru -->
        <div class="bg-surface rounded-2xl border border-border p-5 shadow-xs">
            <div class="flex items-center justify-between pb-3 border-b border-border">
                <h4 class="font-bold text-text-main flex items-center gap-2 text-sm">
                    <span>🌟</span> Prestasi Guru & Sekolah Terbaru
                </h4>
                <div class="flex items-center gap-2 text-xs">
                    <a href="{{ route('prestasi.guru') }}" class="text-primary hover:underline">Guru</a>
                    <span class="text-text-muted">&bull;</span>
                    <a href="{{ route('prestasi.sekolah') }}" class="text-purple-600 hover:underline">Sekolah</a>
                </div>
            </div>
            <div class="divide-y divide-border mt-3">
                @forelse($recentGuru->take(3) as $g)
                    <div class="py-3 flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center flex-shrink-0 font-black text-sm">
                            👨‍🏫
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                    GTK &bull; {{ $g->peringkat }}
                                </span>
                                <span class="text-xs text-text-muted">&bull; {{ $g->tingkat_label }}</span>
                            </div>
                            <h5 class="font-bold text-sm text-text-main truncate mt-0.5">{{ $g->nama_prestasi }}</h5>
                            <p class="text-xs text-text-secondary">Pendidik: <strong class="text-text-main">{{ $g->daftar_guru_nama }}</strong></p>
                        </div>
                        <span class="text-[11px] text-text-muted flex-shrink-0">{{ $g->tanggal_perolehan ? $g->tanggal_perolehan->format('d M Y') : '-' }}</span>
                    </div>
                @empty
                @endforelse

                @forelse($recentSekolah->take(2) as $s)
                    <div class="py-3 flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center flex-shrink-0 font-black text-sm">
                            🏫
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 dark:bg-purple-950 dark:text-purple-300">
                                    Sekolah &bull; {{ $s->capaian }}
                                </span>
                                <span class="text-xs text-text-muted">&bull; {{ $s->tingkat_label }}</span>
                            </div>
                            <h5 class="font-bold text-sm text-text-main truncate mt-0.5">{{ $s->nama_prestasi }}</h5>
                            <p class="text-xs text-text-secondary">Lembaga: {{ $s->penyelenggara_nama }}</p>
                        </div>
                        <span class="text-[11px] text-text-muted flex-shrink-0">{{ $s->tanggal_perolehan ? $s->tanggal_perolehan->format('d M Y') : '-' }}</span>
                    </div>
                @empty
                @endforelse

                @if($recentGuru->isEmpty() && $recentSekolah->isEmpty())
                    <p class="text-xs text-text-muted text-center py-6">Belum ada prestasi guru atau sekolah yang dicatat.</p>
                @endif
            </div>
        </div>
    </div>
</div>
