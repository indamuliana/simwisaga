<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface p-6 rounded-2xl border border-border shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                <span>🎨 Portal Siswa</span>
                <span>&bull;</span>
                <span>Minat & Bakat</span>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">UPD & Seni Budaya</h1>
            <p class="text-xs text-text-muted mt-1">
                Informasi keikutsertaan kegiatan Unit Pengembangan Diri (UPD/Ekstrakurikuler) dan cabang Seni Budaya pilihan Anda.
            </p>
        </div>
    </div>

    <!-- Section 1: Cabang Seni Budaya -->
    <div class="card p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-border pb-3">
            <div class="flex items-center gap-2">
                <span class="text-lg">🎭</span>
                <div>
                    <h2 class="text-sm font-bold text-text-main">Seni Budaya yang Dipilih</h2>
                    <p class="text-xs text-text-muted">Cabang seni budaya pilihan untuk pemenuhan kurikulum dan minat artistik</p>
                </div>
            </div>
            @if ($siswa?->seniBudayaAnggotas->isNotEmpty())
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary-soft text-primary">
                    {{ $siswa->seniBudayaAnggotas->count() }} Terdaftar
                </span>
            @endif
        </div>

        @if (!$siswa || $siswa->seniBudayaAnggotas->isEmpty())
            <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                Anda belum terdaftar dalam cabang Seni Budaya mana pun.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($siswa->seniBudayaAnggotas as $sb)
                    @php
                        $seni = $sb->seniBudaya;
                        $semester = $sb->semester;
                        $nilai = $sb->nilai_akhir;
                        $predikat = $sb->predikat ?: ($nilai >= 85 ? 'A' : ($nilai >= 75 ? 'B' : ($nilai >= 60 ? 'C' : 'D')));
                    @endphp
                    <div class="rounded-xl border border-border bg-surface-raised p-5 hover:border-primary/40 transition-colors space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/10 text-purple-600 border border-purple-500/20">
                                    {{ $semester?->tahunAjaran?->nama }} - {{ $semester?->nama ?? 'Semester Aktif' }}
                                </span>
                                <h3 class="text-base font-bold text-text-main mt-1.5">{{ $seni?->nama }}</h3>
                                <p class="text-xs text-text-secondary mt-0.5">
                                    Pembimbing: <strong class="text-text-main">{{ $seni?->pembimbing?->nama_lengkap ?: '-' }}</strong>
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] uppercase font-bold text-text-muted block">Nilai Capaian</span>
                                <div class="flex items-center gap-1.5 justify-end mt-0.5">
                                    <span class="text-lg font-black text-primary font-mono">{{ $nilai !== null ? number_format($nilai, 1) : '-' }}</span>
                                    @if ($predikat)
                                        <span class="inline-flex px-1.5 py-0.2 rounded text-[10px] font-black {{ $predikat === 'A' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-blue-500/10 text-blue-600' }}">
                                            {{ $predikat }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-border text-xs">
                            <div>
                                <span class="text-text-muted text-[10px] block">Kehadiran Kegiatan</span>
                                <span class="font-bold text-emerald-600 mt-0.5 block font-mono">
                                    {{ $sb->persentase_kehadiran !== null ? $sb->persentase_kehadiran . '%' : '-' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-text-muted text-[10px] block">Status Partisipasi</span>
                                <span class="font-semibold text-text-main mt-0.5 block">
                                    {{ ucfirst($sb->status ?? 'Aktif') }}
                                </span>
                            </div>
                        </div>

                        @if ($sb->catatan || $sb->deskripsi)
                            <div class="p-3 rounded-lg bg-surface border border-border text-xs text-text-secondary">
                                <span class="font-bold text-text-main text-[11px] block mb-0.5">Catatan Pembimbing:</span>
                                {{ $sb->catatan ?: $sb->deskripsi }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Section 2: Ekstrakurikuler / UPD -->
    <div class="card p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-border pb-3">
            <div class="flex items-center gap-2">
                <span class="text-lg">⚽</span>
                <div>
                    <h2 class="text-sm font-bold text-text-main">Ekstrakurikuler / UPD (Unit Pengembangan Diri)</h2>
                    <p class="text-xs text-text-muted">Kegiatan pengembangan potensi, minat, dan bakat di luar jam pelajaran formal</p>
                </div>
            </div>
            @if ($siswa?->ekstrakurikulerAnggotas->isNotEmpty())
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary-soft text-primary">
                    {{ $siswa->ekstrakurikulerAnggotas->count() }} Kegiatan
                </span>
            @endif
        </div>

        @if (!$siswa || $siswa->ekstrakurikulerAnggotas->isEmpty())
            <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                Anda belum terdaftar dalam kegiatan Ekstrakurikuler / UPD mana pun.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($siswa->ekstrakurikulerAnggotas as $ea)
                    @php
                        $ekskul = $ea->ekstrakurikuler;
                        $semester = $ea->semester;
                    @endphp
                    <div class="rounded-xl border border-border bg-surface-raised p-5 hover:border-primary/40 transition-colors space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-600 border border-blue-500/20">
                                    {{ $semester?->tahunAjaran?->nama }} - {{ $semester?->nama ?? 'Semester Aktif' }}
                                </span>
                                <h3 class="text-base font-bold text-text-main mt-1.5">{{ $ekskul?->nama }}</h3>
                                <p class="text-xs text-text-secondary mt-0.5">
                                    Pembina / Pelatih: <strong class="text-text-main">{{ $ekskul?->pembina?->nama_lengkap ?: '-' }}</strong>
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] uppercase font-bold text-text-muted block">Predikat</span>
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-black mt-0.5 bg-emerald-500/10 text-emerald-600">
                                    {{ $ea->predikat ?: ($ea->nilai_angka ? ($ea->nilai_angka >= 80 ? 'Sangat Baik (A)' : 'Baik (B)') : 'Aktif') }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-border text-xs">
                            <div>
                                <span class="text-text-muted text-[10px] block">Kehadiran Latihan</span>
                                <span class="font-bold text-text-main mt-0.5 block font-mono">
                                    {{ $ea->persentase_kehadiran !== null ? $ea->persentase_kehadiran . '%' : '-' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-text-muted text-[10px] block">Nilai Angka</span>
                                <span class="font-semibold text-primary mt-0.5 block font-mono">
                                    {{ $ea->nilai_angka ?: '-' }}
                                </span>
                            </div>
                        </div>

                        @if ($ea->keterangan || $ea->deskripsi)
                            <div class="p-3 rounded-lg bg-surface border border-border text-xs text-text-secondary">
                                <span class="font-bold text-text-main text-[11px] block mb-0.5">Keterangan:</span>
                                {{ $ea->keterangan ?: $ea->deskripsi }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Section 3: Mata Pelajaran Pilihan (Jika ada) -->
    @if ($siswa?->mapelPilihanAnggotas && $siswa->mapelPilihanAnggotas->isNotEmpty())
        <div class="card p-6 space-y-4">
            <div class="flex items-center gap-2 border-b border-border pb-3">
                <span class="text-lg">📚</span>
                <div>
                    <h2 class="text-sm font-bold text-text-main">Mata Pelajaran Pilihan</h2>
                    <p class="text-xs text-text-muted">Mata pelajaran pilihan peminatan khusus yang Anda ikuti</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($siswa->mapelPilihanAnggotas as $mp)
                    <div class="rounded-xl border border-border bg-surface-raised p-4 text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-text-main text-sm">{{ $mp->mapelPilihan?->nama }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary">
                                {{ $mp->tahunAjaran?->nama }}
                            </span>
                        </div>
                        <div class="text-text-muted">
                            Guru: <strong class="text-text-secondary">{{ $mp->mapelPilihan?->guru?->nama_lengkap ?: '-' }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
