<div class="space-y-6">
    <!-- Header Navigasi -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-border pb-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('seni-budaya.index') }}" class="p-1 rounded-lg text-text-secondary hover:text-text-main hover:bg-surface-hover">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
                    Presensi: {{ $cabang->nama }}
                </h2>
            </div>
            <p class="text-sm text-text-secondary mt-1">
                Catat kehadiran pertemuan siswa cabang {{ $cabang->nama }}. Presensi berpengaruh sebesar {{ $cabang->bobot_presensi }}% terhadap nilai akhir.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('seni-budaya.penilaian', $cabang->id) }}" class="btn-secondary text-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Ke Lembar Penilaian
            </a>
            <button wire:click="openCreateSessionModal" class="btn-primary text-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Sesi Pertemuan
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    <!-- Selector Sesi Pertemuan -->
    @if($presensis->isNotEmpty())
        <div class="card p-4 border border-border space-y-3">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div class="flex items-center gap-2 overflow-x-auto max-w-full pb-1 sm:pb-0">
                    <span class="text-xs font-semibold text-text-secondary uppercase tracking-wider shrink-0 mr-1">Pilih Pertemuan:</span>
                    @foreach($presensis as $p)
                        <button type="button"
                                wire:click="$set('selectedPresensiId', {{ $p->id }})"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all shrink-0 {{ $selectedPresensiId === $p->id ? 'bg-primary text-white shadow-xs' : 'bg-surface-hover text-text-secondary hover:text-text-main hover:bg-surface' }}">
                            P-{{ $p->pertemuan_ke }} ({{ \Carbon\Carbon::parse($p->tanggal)->format('d/m') }})
                        </button>
                    @endforeach
                </div>
                @if($activeSession)
                    <div class="flex items-center gap-2 shrink-0">
                        <button wire:click="deleteSession({{ $activeSession->id }})" wire:confirm="Hapus sesi pertemuan ke-{{ $activeSession->pertemuan_ke }}?" class="p-1.5 text-text-muted hover:text-rose-600 rounded-md hover:bg-surface" title="Hapus Sesi">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                @endif
            </div>

            @if($activeSession)
                <div class="p-3 bg-surface-hover rounded-xl border border-border text-xs flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-4">
                        <span><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($activeSession->tanggal)->translatedFormat('l, d F Y') }}</span>
                        @if($activeSession->materi)
                            <span><strong>Materi/Agenda:</strong> {{ $activeSession->materi }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="markAllPresent" class="btn-secondary text-xs py-1 px-2.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Set Semua Hadir
                        </button>
                        <button type="button" wire:click="saveAttendance" class="btn-primary text-xs py-1 px-3 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            Simpan Presensi
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Tabel Matriks Kehadiran Siswa -->
    @if($activeSession && count($attendanceData) > 0)
        <div class="card overflow-hidden border border-border">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-hover text-text-secondary text-xs uppercase tracking-wider border-b border-border">
                        <tr>
                            <th class="px-4 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3">NISN / NIS</th>
                            <th class="px-4 py-3">Nama Siswa</th>
                            <th class="px-4 py-3">Kelas / Rombel</th>
                            <th class="px-4 py-3 text-center w-72">Status Kehadiran</th>
                            <th class="px-4 py-3">Catatan / Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border text-text-main">
                        @php $no = 1; @endphp
                        @foreach($attendanceData as $siswaId => $row)
                            <tr class="hover:bg-surface-hover transition-colors">
                                <td class="px-4 py-3 text-center text-text-muted">{{ $no++ }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-text-secondary">{{ $row['nisn'] }}</td>
                                <td class="px-4 py-3 font-medium">{{ $row['nama'] }}</td>
                                <td class="px-4 py-3 text-text-secondary">{{ $row['rombel'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="inline-flex rounded-lg p-0.5 bg-surface-hover border border-border">
                                        @foreach(['Hadir', 'Sakit', 'Izin', 'Tidak Hadir'] as $st)
                                            <button type="button"
                                                    wire:click="$set('attendanceData.{{ $siswaId }}.status', '{{ $st }}')"
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors {{ $row['status'] === $st ? ($st === 'Hadir' ? 'bg-emerald-600 text-white' : ($st === 'Tidak Hadir' ? 'bg-rose-600 text-white' : 'bg-amber-500 text-white')) : 'text-text-secondary hover:text-text-main' }}">
                                                {{ $st }}
                                            </button>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text"
                                           wire:model="attendanceData.{{ $siswaId }}.catatan"
                                           placeholder="Alasan izin / sakit..."
                                           class="input text-xs py-1 px-2.5 w-full">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-border flex justify-end">
                <button type="button" wire:click="saveAttendance" class="btn-primary text-sm flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Presensi Pertemuan Ini
                </button>
            </div>
        </div>
    @elseif($presensis->isEmpty())
        <div class="card p-8 text-center border border-dashed border-border">
            <svg class="w-12 h-12 text-text-muted mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h4 class="text-base font-semibold text-text-main">Belum Ada Sesi Pertemuan</h4>
            <p class="text-xs text-text-secondary mt-1 max-w-sm mx-auto">
                Silakan buat sesi pertemuan pertama untuk mulai mencatat kehadiran siswa cabang {{ $cabang->nama }}.
            </p>
            <div class="mt-4">
                <button wire:click="openCreateSessionModal" class="btn-primary text-sm inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Sesi Pertemuan Pertama
                </button>
            </div>
        </div>
    @endif

    <!-- Modal Buat Sesi Baru -->
    @if($showSessionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" x-cloak>
            <div class="card max-w-md w-full p-6 space-y-4 shadow-xl border border-border">
                <div class="flex justify-between items-center border-b border-border pb-3">
                    <h3 class="text-lg font-bold text-text-main">Tambah Sesi Pertemuan Presensi</h3>
                    <button type="button" wire:click="$set('showSessionModal', false)" class="text-text-muted hover:text-text-main text-xl leading-none">&times;</button>
                </div>

                <form wire:submit="createSession" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Pertemuan Ke- <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model="pertemuan_ke" min="1" class="input text-sm w-full">
                            @error('pertemuan_ke') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Tanggal <span class="text-rose-500">*</span></label>
                            <input type="date" wire:model="tanggal" class="input text-sm w-full">
                            @error('tanggal') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Jam Mulai</label>
                            <input type="time" wire:model="jam_mulai" class="input text-sm w-full">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Jam Selesai</label>
                            <input type="time" wire:model="jam_selesai" class="input text-sm w-full">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Materi / Agenda Pertemuan</label>
                        <input type="text" wire:model="materi" placeholder="misal: Latihan Gerak Dasar Tari Jaipong" class="input text-sm w-full">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Catatan Tambahan</label>
                        <textarea wire:model="catatan" rows="2" placeholder="Catatan jalannya latihan..." class="input text-sm w-full"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="$set('showSessionModal', false)" class="btn-secondary text-sm">Batal</button>
                        <button type="submit" class="btn-primary text-sm flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Buat Sesi & Buka Presensi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
