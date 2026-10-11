<div class="space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold">Tahun Ajaran &amp; Semester</h1>
            <p class="text-sm text-text-secondary">Atur periode akademik aktif, tahun ajaran baru, dan periode kunci penginputan nilai/absensi.</p>
        </div>
        @can('akademik.tahunajaran.tambah')
        <button wire:click="create" class="btn btn-primary">
            + Tambah Tahun Ajaran
        </button>
        @endcan
    </div>

    @if (session('status'))
    <div class="rounded-lg border border-success bg-success-soft px-4 py-3 text-sm text-success">
        {{ session('status') }}
    </div>
    @endif

    <div class="space-y-4">
        @forelse ($tahunAjarans as $ta)
        <div class="card space-y-4 {{ $ta->is_active ? 'border-brand-500 ring-1 ring-brand-500/20' : '' }}">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b pb-3 border-border">
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold">T.A. {{ $ta->nama }}</h2>
                    @if ($ta->is_active)
                    <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-success dark:bg-green-900/40">
                        Tahun Ajaran Berjalan
                    </span>
                    @endif
                </div>
                <div class="text-xs text-text-secondary">
                    @if ($ta->tgl_mulai && $ta->tgl_selesai)
                    Periode: {{ $ta->tgl_mulai->format('d M Y') }} s/d {{ $ta->tgl_selesai->format('d M Y') }}
                    @endif
                </div>
            </div>

            {{-- Daftar Semester --}}
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach ($ta->semesters as $sem)
                <div class="rounded-lg border p-4 transition {{ $sem->is_active ? 'border-brand-300 bg-brand-50/40 dark:border-brand-800 dark:bg-brand-950/20' : 'border-border bg-surface-hover/50' }}">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-text-main">Semester {{ ucfirst($sem->semester) }}</span>
                        @if ($sem->is_active)
                        <span class="inline-flex items-center rounded-full bg-brand-600 px-2.5 py-0.5 text-xs font-bold text-white">
                            Aktif Sekarang
                        </span>
                        @elseif (auth()->user()->can('akademik.tahunajaran.edit'))
                        <button wire:click="openTransitionModal({{ $sem->id }})"
                            class="inline-flex items-center gap-1 rounded-md bg-brand-50 px-2 py-1 text-xs font-semibold text-brand-600 hover:bg-brand-100 dark:bg-brand-950/40 dark:text-brand-400 dark:hover:bg-brand-900/50">
                            ⚡ Aktifkan Semester
                        </button>
                        @endif
                    </div>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-2 border-t pt-3 text-xs border-border/60">
                        <div class="flex items-center gap-1.5">
                            <span class="text-text-secondary">Status Nilai:</span>
                            @if ($sem->isLockedForNilai())
                            <span class="font-semibold text-red-600 dark:text-red-400">&#128274; Terkunci</span>
                            @else
                            <span class="font-semibold text-green-600 dark:text-green-400">&#128275; Terbuka</span>
                            @endif
                            @can('akademik.tahunajaran.edit')
                            <button wire:click="toggleLockNilai({{ $sem->id }})" class="ml-1 text-[11px] text-gray-400 hover:text-text-main underline">
                                ({{ $sem->isLockedForNilai() ? 'Buka' : 'Kunci' }})
                            </button>
                            @endcan
                        </div>

                        <div class="flex items-center gap-1.5">
                            <span class="text-text-secondary">Status Absensi:</span>
                            @if ($sem->isLockedForAbsensi())
                            <span class="font-semibold text-red-600 dark:text-red-400">&#128274; Terkunci</span>
                            @else
                            <span class="font-semibold text-green-600 dark:text-green-400">&#128275; Terbuka</span>
                            @endif
                            @can('akademik.tahunajaran.edit')
                            <button wire:click="toggleLockAbsensi({{ $sem->id }})" class="ml-1 text-[11px] text-gray-400 hover:text-text-main underline">
                                ({{ $sem->isLockedForAbsensi() ? 'Buka' : 'Kunci' }})
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="card text-center text-sm text-text-secondary">
            Belum ada data tahun ajaran. Klik tombol Tambah Tahun Ajaran di atas.
        </div>
        @endforelse
    </div>

    {{-- Modal Tambah Tahun Ajaran --}}
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
        <div class="card w-full max-w-md space-y-4">
            <div class="flex items-center justify-between border-b border-border pb-3">
                <h2 class="text-lg font-semibold">Tambah Tahun Ajaran Baru</h2>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-text-secondary">&times;</button>
            </div>

            <form wire:submit="save" class="space-y-3">
                <div>
                    <label class="label">Nama Tahun Ajaran * (contoh: 2025/2026)</label>
                    <input type="text" wire:model="nama" placeholder="2025/2026" class="input">
                    @error('nama') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="label">Tanggal Mulai</label>
                        <input type="date" wire:model="tgl_mulai" class="input">
                        @error('tgl_mulai') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label">Tanggal Selesai</label>
                        <input type="date" wire:model="tgl_selesai" class="input">
                        @error('tgl_selesai') <p class="error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <label class="flex items-center gap-2 pt-1 text-sm">
                    <input type="checkbox" wire:model="is_active" class="rounded border-border text-brand-600">
                    <span>Jadikan sebagai Tahun Ajaran Aktif</span>
                </label>

                <div class="flex justify-end gap-2 border-t border-border pt-3">
                    <button type="button" wire:click="$set('showModal', false)" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">Simpan</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal Transisi Cerdas Pergantian Semester --}}
    @if ($showTransitionModal && $targetSem)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
        <div class="card w-full max-w-lg space-y-5 bg-surface border border-border shadow-2xl rounded-2xl p-6">
            <div class="flex items-center justify-between border-b border-border pb-3">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-brand-500/10 text-brand-500 text-lg">⚡</span>
                    <div>
                        <h2 class="text-lg font-bold text-text-main">Transisi Semester Baru</h2>
                        <p class="text-xs text-text-secondary">Konfirmasi &amp; penyesuaian otomatis siklus akademik</p>
                    </div>
                </div>
                <button wire:click="closeTransitionModal" class="text-text-secondary hover:text-text-main text-xl font-bold">&times;</button>
            </div>

            <div class="p-3.5 rounded-xl bg-surface-hover/70 border border-border/80 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-text-secondary">Semester Berjalan:</span>
                    <span class="font-semibold text-text-main">{{ $activeSem ? $activeSem->label : '-' }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-text-secondary">Akan Diaktifkan:</span>
                    <span class="font-bold text-brand-600 dark:text-brand-400">{{ $targetSem->label }}</span>
                </div>
            </div>

            <div class="space-y-3 text-xs">
                <h3 class="font-semibold text-text-main uppercase tracking-wider text-[11px]">Pilihan Transisi &amp; Proteksi Data:</h3>
                
                <label class="flex items-start gap-2.5 p-2.5 rounded-lg border border-border hover:bg-surface-hover/50 cursor-pointer transition">
                    <input type="checkbox" wire:model="lockNilaiSemesterLama" class="mt-0.5 rounded border-border text-brand-600 focus:ring-brand-500">
                    <div>
                        <span class="font-medium text-text-main block">Kunci Penginputan Nilai Semester Sebelumnya</span>
                        <span class="text-text-secondary text-[11px]">Mencegah perubahan nilai rapor pada semester lama setelah pembagian rapor.</span>
                    </div>
                </label>

                <label class="flex items-start gap-2.5 p-2.5 rounded-lg border border-border hover:bg-surface-hover/50 cursor-pointer transition">
                    <input type="checkbox" wire:model="lockAbsensiSemesterLama" class="mt-0.5 rounded border-border text-brand-600 focus:ring-brand-500">
                    <div>
                        <span class="font-medium text-text-main block">Kunci Absensi Semester Sebelumnya</span>
                        <span class="text-text-secondary text-[11px]">Mencegah perubahan catatan kehadiran siswa pada semester yang telah selesai.</span>
                    </div>
                </label>

                <label class="flex items-start gap-2.5 p-2.5 rounded-lg border border-brand-500/30 bg-brand-500/5 hover:bg-brand-500/10 cursor-pointer transition">
                    <input type="checkbox" wire:model="salinPenugasanMengajar" class="mt-0.5 rounded border-border text-brand-600 focus:ring-brand-500">
                    <div>
                        <span class="font-medium text-text-main block">Salin Penugasan Mengajar Guru (Rekomendasi)</span>
                        <span class="text-text-secondary text-[11px]">
                            @if($penugasanCountToCopy > 0)
                                Ditemukan <strong>{{ $penugasanCountToCopy }}</strong> penugasan guru mapel yang akan disalin otomatis ke semester baru.
                            @else
                                Menyalin jadwal guru mapel yang cocok dari semester sebelumnya agar guru langsung bisa menginput nilai.
                            @endif
                        </span>
                    </div>
                </label>
            </div>

            <div class="rounded-lg p-3 bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-400 text-xs">
                <strong>Catatan Dampak:</strong> Seluruh siswa dan rombel tetap berjalan seperti biasa. Periode aktif aplikasi akan dialihkan ke semester ini.
            </div>

            <div class="flex justify-end gap-2 border-t border-border pt-3">
                <button type="button" wire:click="closeTransitionModal" class="btn btn-secondary text-xs">Batal</button>
                <button type="button" wire:click="executeTransition" class="btn btn-primary text-xs" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="executeTransition">Aktifkan Semester Sekarang</span>
                    <span wire:loading wire:target="executeTransition">Memproses Transisi...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
