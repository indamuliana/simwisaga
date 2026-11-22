<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface p-6 rounded-2xl border border-border shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                <span>⚡ Portal Siswa</span>
                <span>&bull;</span>
                <span>Unit Pengembangan Diri</span>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">Unit Pengembangan Diri (UPD)</h1>
            <p class="text-xs text-text-muted mt-1">
                Informasi keikutsertaan kegiatan ekstrakurikuler/UPD, jadwal latihan, rekap kehadiran, dan capaian nilai pembinaan karakter.
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

    <!-- Daftar Kegiatan UPD / Ekstrakurikuler -->
    <div class="card p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-border pb-3">
            <div class="flex items-center gap-2">
                <span class="text-lg">⚽</span>
                <div>
                    <h2 class="text-sm font-bold text-text-main">Kegiatan UPD yang Diikuti</h2>
                    <p class="text-xs text-text-muted">Kegiatan ekstrakurikuler wajib & pilihan untuk pengembangan minat dan bakat Anda</p>
                </div>
            </div>
            @if ($ekskulList->isNotEmpty())
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-primary-soft text-primary">
                    {{ $ekskulList->count() }} Kegiatan Terdaftar
                </span>
            @endif
        </div>

        @if ($ekskulList->isEmpty())
            <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted space-y-2">
                <span class="text-3xl block">🏸</span>
                <p class="font-medium text-text-main">Belum ada riwayat kegiatan UPD untuk periode yang dipilih.</p>
                <p class="text-[11px] text-text-muted">Hubungi koordinator kesiswaan atau pembina ekstrakurikuler untuk konfirmasi pendaftaran.</p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                @foreach ($ekskulList as $ea)
                    @php
                        $ekskul = $ea->ekstrakurikuler;
                        $sem = $ea->semester;
                        $nilai = $ea->nilai_angka;
                        $predikat = $ea->nilai_huruf ?: ($ea->predikat ?: ($nilai ? ($nilai >= 85 ? 'A (Sangat Baik)' : ($nilai >= 75 ? 'B (Baik)' : 'C (Cukup)')) : 'Aktif'));
                        $persenHadir = $ea->persentase_kehadiran !== null ? (float) $ea->persentase_kehadiran : ($ea->total_pertemuan > 0 ? round(($ea->total_hadir / $ea->total_pertemuan) * 100, 1) : null);
                    @endphp
                    <div class="rounded-2xl border border-border bg-surface-raised p-5 hover:border-primary/40 transition-all space-y-4 shadow-xs">
                        <!-- Header Kartu -->
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary-soft text-primary border border-primary/20">
                                        {{ $sem?->tahunAjaran?->nama }} &bull; {{ $sem?->nama ?? 'Semester ' . $ea->semester_id }}
                                    </span>
                                    @if ($ekskul?->is_wajib)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20">
                                            Wajib
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-base font-extrabold text-text-main mt-2">{{ $ekskul?->nama }}</h3>
                                <p class="text-xs text-text-secondary mt-0.5">
                                    Pembina / Pelatih: <strong class="text-text-main">{{ $ekskul?->pembina?->nama_lengkap ?? ($ekskul?->instruktur_nama ?: 'Tim Pembina UPD') }}</strong>
                                </p>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="text-[10px] uppercase font-bold text-text-muted block">Capaian Nilai</span>
                                <div class="flex items-center gap-1.5 justify-end mt-1">
                                    <span class="text-lg font-black text-primary font-mono">{{ $nilai !== null ? $nilai : '-' }}</span>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-black {{ str_contains($predikat, 'A') ? 'bg-emerald-500/10 text-emerald-600' : 'bg-blue-500/10 text-blue-600' }}">
                                        {{ $predikat }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Jadwal & Lokasi -->
                        <div class="grid grid-cols-2 gap-3 p-3 rounded-xl bg-surface border border-border text-xs">
                            <div>
                                <span class="text-[10px] text-text-muted font-bold uppercase block">Jadwal Latihan</span>
                                <span class="font-medium text-text-main mt-0.5 block">
                                    {{ $ekskul?->hari_latihan ?: 'Setiap Pekan' }}
                                    @if ($ekskul?->jam_mulai)
                                        ({{ substr($ekskul->jam_mulai, 0, 5) }} - {{ substr($ekskul->jam_selesai, 0, 5) }})
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] text-text-muted font-bold uppercase block">Lokasi / Tempat</span>
                                <span class="font-medium text-text-main mt-0.5 block truncate" title="{{ $ekskul?->tempat ?: 'Area Sekolah' }}">
                                    {{ $ekskul?->tempat ?: 'Area Kampus Sekolah' }}
                                </span>
                            </div>
                        </div>

                        <!-- Rekap Kehadiran Latihan -->
                        <div class="grid grid-cols-2 gap-3 pt-2 text-xs border-t border-border/80">
                            <div>
                                <span class="text-text-muted text-[10px] block">Tingkat Kehadiran Latihan</span>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="font-bold font-mono text-sm {{ $persenHadir >= 80 ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $persenHadir !== null ? $persenHadir . '%' : '100%' }}
                                    </span>
                                    @if ($ea->total_pertemuan)
                                        <span class="text-[10px] text-text-muted">({{ $ea->total_hadir }}/{{ $ea->total_pertemuan }} pertemuan)</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span class="text-text-muted text-[10px] block">Status Keanggotaan</span>
                                <span class="font-semibold text-text-main mt-0.5 block">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ ucfirst($ea->status_keanggotaan ?: 'Aktif') }}
                                    </span>
                                </span>
                            </div>
                        </div>

                        <!-- Catatan Pembina / Deskripsi -->
                        @if ($ea->deskripsi || $ea->catatan_pindah || $ekskul?->deskripsi)
                            <div class="p-3 rounded-xl bg-surface border border-border text-xs text-text-secondary leading-relaxed">
                                <span class="font-bold text-text-main text-[11px] block mb-0.5">Evaluasi & Catatan Pembina:</span>
                                {{ $ea->deskripsi ?: ($ekskul?->deskripsi ?: 'Berpartisipasi aktif dalam kegiatan rutin dan menunjukkan kedisiplinan yang baik.') }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
