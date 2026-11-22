<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface p-6 rounded-2xl border border-border shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                <span>🎨 Portal Siswa</span>
                <span>&bull;</span>
                <span>Kesenian & Kebudayaan</span>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">Seni Budaya</h1>
            <p class="text-xs text-text-muted mt-1">
                Informasi cabang seni budaya pilihan, jadwal bimbingan artistik, rekap presensi, dan penilaian capaian apresiasi seni.
            </p>
        </div>

        <!-- Filter Semester / Periode -->
        <div class="flex items-center gap-2 text-xs">
            <span class="text-text-muted whitespace-nowrap">Filter Periode:</span>
            <select wire:model.live="filterSemester" class="rounded-xl border border-border bg-surface px-3 py-1.5 text-xs text-text-main font-semibold focus:border-primary focus:outline-hidden">
                <option value="semua">Semua Semester</option>
                @foreach ($semesters as $s)
                    <option value="{{ $s->id }}">{{ $s->tahunAjaran?->nama }} - {{ $s->nama }} {{ $s->is_active ? '(Aktif)' : '' }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Daftar Cabang Seni Budaya yang Dipilih -->
    <div class="card p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-border pb-3">
            <div class="flex items-center gap-2">
                <span class="text-lg">🎭</span>
                <div>
                    <h2 class="text-sm font-bold text-text-main">Cabang Seni Budaya Terdaftar</h2>
                    <p class="text-xs text-text-muted">Cabang seni budaya yang Anda ambil sebagai bagian dari kurikulum dan apresiasi seni</p>
                </div>
            </div>
            @if ($seniList->isNotEmpty())
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-primary-soft text-primary">
                    {{ $seniList->count() }} Cabang Terdaftar
                </span>
            @endif
        </div>

        @if ($seniList->isEmpty())
            <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted space-y-2">
                <span class="text-3xl block">🎨</span>
                <p class="font-medium text-text-main">Belum ada data pendaftaran Seni Budaya untuk periode yang dipilih.</p>
                <p class="text-[11px] text-text-muted">Pastikan Anda telah terdaftar pada salah satu cabang seni budaya di semester aktif.</p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                @foreach ($seniList as $sa)
                    @php
                        $seni = $sa->seniBudaya;
                        $sem = $sa->semester;
                        $nilai = $sa->nilai_akhir;
                        $kkm = $seni?->kkm ?? 75;
                        $predikat = $sa->nilai_huruf ?: ($nilai !== null ? ($nilai >= 85 ? 'A (Sangat Baik)' : ($nilai >= 75 ? 'B (Baik)' : 'C (Cukup)')) : '-');
                        $persenHadir = $sa->persentase_kehadiran !== null ? (float) $sa->persentase_kehadiran : ($sa->total_pertemuan > 0 ? round(($sa->total_hadir / $sa->total_pertemuan) * 100, 1) : null);
                    @endphp
                    <div class="rounded-2xl border border-border bg-surface-raised p-5 hover:border-primary/40 transition-all space-y-4 shadow-xs">
                        <!-- Header Kartu -->
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/10 text-purple-600 border border-purple-500/20">
                                    {{ $sem?->tahunAjaran?->nama }} &bull; {{ $sem?->nama ?? 'Semester ' . $sa->semester_id }}
                                </span>
                                <h3 class="text-base font-extrabold text-text-main mt-2">{{ $seni?->nama }}</h3>
                                <p class="text-xs text-text-secondary mt-0.5">
                                    Guru Pembimbing: <strong class="text-text-main">{{ $seni?->guru?->nama_lengkap ?? ($seni?->instruktur_nama ?: 'Tim Guru Seni Budaya') }}</strong>
                                </p>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="text-[10px] uppercase font-bold text-text-muted block">Nilai Akhir (KKM: {{ $kkm }})</span>
                                <div class="flex items-center gap-1.5 justify-end mt-1">
                                    <span class="text-lg font-black {{ ($nilai !== null && $nilai < $kkm) ? 'text-rose-600' : 'text-primary' }} font-mono">
                                        {{ $nilai !== null ? number_format($nilai, 1) : '-' }}
                                    </span>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-black {{ str_contains($predikat, 'A') ? 'bg-emerald-500/10 text-emerald-600' : 'bg-blue-500/10 text-blue-600' }}">
                                        {{ $predikat }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Jadwal & Studio Latihan -->
                        <div class="grid grid-cols-2 gap-3 p-3 rounded-xl bg-surface border border-border text-xs">
                            <div>
                                <span class="text-[10px] text-text-muted font-bold uppercase block">Jadwal Kelas / Latihan</span>
                                <span class="font-medium text-text-main mt-0.5 block">
                                    {{ $seni?->hari_latihan ?: 'Sesuai Jadwal KBM' }}
                                    @if ($seni?->jam_mulai)
                                        ({{ substr($seni->jam_mulai, 0, 5) }} - {{ substr($seni->jam_selesai, 0, 5) }})
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] text-text-muted font-bold uppercase block">Studio / Ruang Praktik</span>
                                <span class="font-medium text-text-main mt-0.5 block truncate" title="{{ $seni?->tempat ?: 'Ruang Seni' }}">
                                    {{ $seni?->tempat ?: 'Studio Seni Budaya' }}
                                </span>
                            </div>
                        </div>

                        <!-- Rekap Kehadiran Kegiatan -->
                        <div class="grid grid-cols-2 gap-3 pt-2 text-xs border-t border-border/80">
                            <div>
                                <span class="text-text-muted text-[10px] block">Kehadiran Kegiatan</span>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="font-bold font-mono text-sm {{ $persenHadir >= 80 ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $persenHadir !== null ? $persenHadir . '%' : '100%' }}
                                    </span>
                                    @if ($sa->total_pertemuan)
                                        <span class="text-[10px] text-text-muted">({{ $sa->total_hadir }}/{{ $sa->total_pertemuan }} sesi)</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span class="text-text-muted text-[10px] block">Status Keanggotaan</span>
                                <span class="font-semibold text-text-main mt-0.5 block">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ ucfirst($sa->status_keanggotaan ?: 'Aktif') }}
                                    </span>
                                </span>
                            </div>
                        </div>

                        <!-- Evaluasi Deskripsi Capaian / Apresiasi Seni -->
                        @php
                            $deskripsi = $sa->deskripsi_manual ?: ($sa->deskripsi_tertinggi ?: ($seni?->deskripsi ?: ''));
                        @endphp
                        @if ($deskripsi)
                            <div class="p-3 rounded-xl bg-surface border border-border text-xs text-text-secondary leading-relaxed">
                                <span class="font-bold text-text-main text-[11px] block mb-0.5">Catatan Apresiasi Seni:</span>
                                {{ $deskripsi }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
