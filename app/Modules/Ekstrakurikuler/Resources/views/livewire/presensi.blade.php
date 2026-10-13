<div class="space-y-5 pb-20">
    <!-- Breadcrumb & Navigasi -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div class="flex items-center gap-2 text-xs">
            <a href="{{ route('ekskul.index') }}" class="text-primary hover:underline flex items-center gap-1 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kegiatan UPD</span>
            </a>
            <span class="text-text-secondary">/</span>
            <span class="text-text-secondary font-medium">Presensi Mingguan: {{ $ekskul->nama }}</span>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('ekskul.penilaian', $ekskul->id) }}" class="btn btn-secondary text-xs flex items-center gap-1.5 w-full sm:w-auto justify-center">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Ke Input Penilaian</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session()->has('message'))
        <div class="p-3.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-200 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    <!-- Card Header Info Ekskul -->
    <div class="card p-4 border border-border bg-gradient-to-r from-blue-50/60 to-surface dark:from-blue-950/20 dark:to-surface">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-xl font-bold text-text-main">{{ $ekskul->nama }}</h2>
                    @if($ekskul->is_wajib)
                        <span class="text-[10px] px-2 py-0.5 rounded font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">Wajib (Tingkat X)</span>
                    @else
                        <span class="text-[10px] px-2 py-0.5 rounded font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">Pilihan</span>
                    @endif
                </div>
                <p class="text-xs text-text-secondary mt-1">
                    Instruktur: <strong class="text-text-main">{{ $ekskul->instruktur_display_name }}</strong> &bull; Jadwal Patokan: <strong>{{ $ekskul->hari_latihan ?? 'Jumat' }} ({{ $ekskul->jam_mulai ? substr($ekskul->jam_mulai,0,5) : '15:30' }})</strong>
                </p>
            </div>
            <div class="text-xs text-text-secondary bg-surface px-3 py-1.5 rounded-lg border border-border">
                Bobot Presensi: <strong class="text-emerald-600 font-bold">{{ $ekskul->bobot_presensi }}%</strong> dari Nilai Akhir Rapor
            </div>
        </div>
    </div>

    <!-- Navigasi Daftar Sesi Pertemuan (Horizontal Scroll Friendly) -->
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-text-secondary uppercase tracking-wider">Pilih Sesi Pertemuan:</span>
            <button wire:click="initNewSesi" class="btn btn-secondary text-xs py-1 px-2.5 flex items-center gap-1 font-semibold text-primary">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Buat Sesi Baru</span>
            </button>
        </div>

        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
            @forelse($sesiList as $s)
                <button wire:click="loadSesi({{ $s->id }})"
                        class="px-3.5 py-2 rounded-xl text-xs font-semibold shrink-0 transition flex items-center gap-2 border {{ $selectedSesiId == $s->id ? 'bg-primary text-white border-primary shadow-xs' : 'bg-surface text-text-main border-border hover:bg-surface-hover' }}">
                    <span>Pertemuan {{ $s->pertemuan_ke }}</span>
                    <span class="text-[11px] opacity-80">({{ $s->tanggal->format('d/m') }})</span>
                </button>
            @empty
                <span class="text-xs text-text-secondary italic">Belum ada sesi presensi tersimpan. Klik "+ Buat Sesi Baru".</span>
            @endforelse
        </div>
    </div>

    <!-- Form Detail Sesi Presensi -->
    <div class="card p-4 border border-border space-y-4">
        <div class="flex items-center justify-between border-b border-border pb-3">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    {{ $pertemuan_ke }}
                </span>
                <h3 class="text-sm font-bold text-text-main">
                    {{ $selectedSesiId ? 'Edit Presensi Pertemuan Ke-' . $pertemuan_ke : 'Input Presensi Sesi Baru (Pertemuan ' . $pertemuan_ke . ')' }}
                </h3>
            </div>
            @if($selectedSesiId)
                <button wire:click="deleteSesi({{ $selectedSesiId }})"
                        onclick="confirm('Hapus sesi pertemuan ini? Data presensi sesi ini akan terhapus.') || event.stopImmediatePropagation()"
                        class="text-xs text-rose-600 hover:underline">
                    Hapus Sesi
                </button>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold mb-1 text-text-main">Pertemuan Ke *</label>
                <input type="number" min="1" wire:model="pertemuan_ke" class="input w-full text-xs font-bold text-center">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-text-main">Tanggal Pelaksanaan *</label>
                <input type="date" wire:model="tanggal" class="input w-full text-xs">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-text-main">Jam Mulai</label>
                <input type="time" wire:model="jam_mulai" class="input w-full text-xs">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-text-main">Jam Selesai</label>
                <input type="time" wire:model="jam_selesai" class="input w-full text-xs">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-text-main">Materi / Catatan Latihan</label>
            <input type="text" wire:model="materi" placeholder="Misal: Teknik dasar, materi PBB, latihan fisik, persiapan lomba..." class="input w-full text-xs">
        </div>
    </div>

    <!-- Mobile-First Checklist Presensi -->
    <div class="space-y-3">
        <!-- Quick Action Bar & Ringkasan -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-surface p-3.5 rounded-xl border border-border">
            <div class="flex items-center gap-3 flex-wrap text-xs">
                <span class="font-bold text-text-main">Kehadiran ({{ $totalSiswa }} Siswa):</span>
                <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-bold">
                    Hadir: {{ $countHadir }}
                </span>
                <span class="px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 font-bold">
                    Tidak Hadir: {{ $countTidakHadir }}
                </span>
                @if($countIzin > 0)
                    <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 font-bold">
                        Izin: {{ $countIzin }}
                    </span>
                @endif
                @if($countSakit > 0)
                    <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 font-bold">
                        Sakit: {{ $countSakit }}
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
                <input type="text" wire:model.live.debounce.300ms="searchSiswa" placeholder="Cari nama siswa..." class="input text-xs w-44">
                <button wire:click="setSemuaHadir" class="btn btn-secondary text-xs flex items-center gap-1 font-semibold text-emerald-700 dark:text-emerald-400 border-emerald-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Set Semua Hadir</span>
                </button>
            </div>
        </div>

        <!-- Daftar Siswa Cards (Mobile-Optimized) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @forelse($filteredAttendance as $siswaId => $data)
                <div class="card p-3.5 border border-border flex items-center justify-between gap-3 hover:border-border-hover transition {{ $data['status'] === 'Hadir' ? 'border-l-4 border-l-emerald-500' : ($data['status'] === 'Tidak Hadir' ? 'border-l-4 border-l-rose-500 bg-rose-50/20 dark:bg-rose-950/10' : 'border-l-4 border-l-amber-500') }}">
                    <!-- Info Siswa -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-text-main truncate">{{ $data['nama'] }}</h4>
                            <span class="text-[11px] px-2 py-0.5 rounded bg-surface-hover border border-border font-medium text-text-secondary shrink-0">
                                {{ $data['rombel'] }}
                            </span>
                        </div>
                        <!-- Catatan kecil optional -->
                        <div class="mt-1.5">
                            <input type="text" wire:model.lazy="attendanceData.{{ $siswaId }}.catatan" placeholder="Catatan/alasan (opsional)..."
                                   class="input text-[11px] py-1 px-2 w-full border-border/80 bg-surface/50">
                        </div>
                    </div>

                    <!-- Tombol Cepat Presensi (Touch Target Besar Untuk HP) -->
                    <div class="flex items-center gap-1.5 shrink-0">
                        <!-- Tombol Hadir -->
                        <button type="button" wire:click="setStatus({{ $siswaId }}, 'Hadir')"
                                class="h-11 px-3.5 rounded-xl font-bold text-xs transition flex items-center justify-center gap-1 {{ $data['status'] === 'Hadir' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-surface border border-border text-text-secondary hover:bg-emerald-50 dark:hover:bg-emerald-950/40' }}"
                                title="Tandai Hadir">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span class="hidden sm:inline">Hadir</span>
                        </button>

                        <!-- Tombol Tidak Hadir -->
                        <button type="button" wire:click="setStatus({{ $siswaId }}, 'Tidak Hadir')"
                                class="h-11 px-3.5 rounded-xl font-bold text-xs transition flex items-center justify-center gap-1 {{ $data['status'] === 'Tidak Hadir' ? 'bg-rose-600 text-white shadow-xs' : 'bg-surface border border-border text-text-secondary hover:bg-rose-50 dark:hover:bg-rose-950/40' }}"
                                title="Tandai Tidak Hadir">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span class="hidden sm:inline">Absen</span>
                        </button>

                        <!-- Menu Opsi Tambahan (Izin / Sakit) -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" type="button" class="h-11 w-8 rounded-xl border border-border bg-surface text-text-secondary flex items-center justify-center hover:bg-surface-hover">
                                <span class="font-bold text-xs">&vellip;</span>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 top-full mt-1 w-28 bg-surface border border-border rounded-xl shadow-lg z-30 p-1 space-y-0.5 text-xs">
                                <button @click="open = false" wire:click="setStatus({{ $siswaId }}, 'Izin')" class="w-full text-left px-2.5 py-1.5 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-950 text-blue-600 font-medium">
                                    Izin
                                </button>
                                <button @click="open = false" wire:click="setStatus({{ $siswaId }}, 'Sakit')" class="w-full text-left px-2.5 py-1.5 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-950 text-amber-600 font-medium">
                                    Sakit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full card p-8 text-center text-text-secondary text-xs">
                    Tidak ada siswa anggota aktif pada ekskul ini. Silakan tambahkan anggota di menu Keanggotaan Siswa.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Floating / Sticky Save Bar di Bagian Bawah (Sangat Ramah HP) -->
    <div class="fixed bottom-0 inset-x-0 bg-surface/95 backdrop-blur-md border-t border-border p-3 z-40">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
            <div class="hidden sm:flex items-center gap-2 text-xs text-text-secondary">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span>Pertemuan Ke-{{ $pertemuan_ke }} &bull; Tanggal {{ $tanggal ? \Carbon\Carbon::parse($tanggal)->format('d F Y') : '-' }}</span>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                <button wire:click="setSemuaHadir" type="button" class="btn btn-secondary text-xs py-2.5 px-4 font-semibold">
                    Set Semua Hadir
                </button>
                <button wire:click="savePresensi" type="button" class="btn btn-primary text-xs py-2.5 px-6 font-bold flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    <span>Simpan Presensi Mingguan</span>
                </button>
            </div>
        </div>
    </div>
</div>
